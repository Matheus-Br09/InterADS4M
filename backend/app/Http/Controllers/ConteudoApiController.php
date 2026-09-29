<?php

namespace App\Http\Controllers;

use App\Models\DocumentoTransparencia;
use App\Models\MaterialDidatico;
use App\Models\Noticia;
use App\Models\ProgramaAcao;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ConteudoApiController extends Controller
{
    private const MODELOS = ['noticias' => Noticia::class, 'materiais' => MaterialDidatico::class, 'transparencia' => DocumentoTransparencia::class, 'programas' => ProgramaAcao::class];

    public function index(string $recurso): array
    {
        $consulta = $this->modelo($recurso)::query();
        if ($recurso === 'programas') {
            $consulta->where('status', 'ativo');
        }
        $itens = $consulta->latest('id')->paginate(20);

        return ['dados' => $itens->items(), 'paginacao' => ['pagina' => $itens->currentPage(), 'por_pagina' => $itens->perPage(), 'total' => $itens->total()]];
    }

    public function show(string $recurso, int $id): array
    {
        $item = $this->modelo($recurso)::query()->when($recurso === 'programas', fn ($query) => $query->where('status', 'ativo'))->findOrFail($id);

        return ['dados' => $item];
    }

    public function store(Request $request, string $recurso): array
    {
        if ($recurso === 'materiais' && $request->filled('arquivo_pdf_base64')) {
            return $this->storeMaterialJson($request);
        }

        $dados = $request->validate($this->regras($recurso, false));
        $item = $this->modelo($recurso)::create($this->arquivos($request, $dados, $recurso));

        return ['dados' => $item];
    }

    private function storeMaterialJson(Request $request): array
    {
        $dados = $request->validate([
            'titulo' => 'required|string|max:150',
            'descricao' => 'sometimes|nullable|string',
            'categoria' => 'required|string|max:100',
            'arquivo_pdf_base64' => 'required|string|max:9800000',
            'arquivo_pdf_nome' => 'required|string|max:255',
        ]);
        $prefixo = 'data:application/pdf;base64,';
        $conteudo = $dados['arquivo_pdf_base64'];

        if (! str_starts_with($conteudo, $prefixo)) {
            throw ValidationException::withMessages(['arquivo_pdf_base64' => 'O arquivo precisa ser um PDF.']);
        }

        $bytes = base64_decode(substr($conteudo, strlen($prefixo)), true);
        $mime = $bytes === false ? null : (new \finfo(FILEINFO_MIME_TYPE))->buffer($bytes);

        if ($bytes === false || strlen($bytes) > 7 * 1024 * 1024 || $mime !== 'application/pdf') {
            throw ValidationException::withMessages(['arquivo_pdf_base64' => 'O arquivo precisa ser um PDF válido de até 7 MB.']);
        }

        $caminho = 'materiais/'.Str::uuid().'.pdf';
        Storage::disk('public')->put($caminho, $bytes);
        $material = MaterialDidatico::create([
            'titulo' => $dados['titulo'],
            'descricao' => $dados['descricao'] ?? null,
            'categoria' => $dados['categoria'],
            'arquivo_pdf' => $caminho,
        ]);

        return ['dados' => $material];
    }

    public function update(Request $request, string $recurso, int $id): array
    {
        $item = $this->modelo($recurso)::findOrFail($id);
        $item->fill($this->arquivos($request, $request->validate($this->regras($recurso, true)), $recurso))->save();

        return ['dados' => $item->refresh()];
    }

    public function destroy(string $recurso, int $id): JsonResponse
    {
        $this->modelo($recurso)::findOrFail($id)->delete();

        return response()->json(status: 204);
    }

    public function arquivo(string $recurso, int $id, string $campo)
    {
        $item = $this->modelo($recurso)::findOrFail($id);
        abort_unless(in_array($campo, ['arquivo_pdf', 'imagem', 'imagem_capa', 'arquivo_midia'], true) && $item->{$campo}, 404);

        return Storage::disk('public')->download($item->{$campo});
    }

    private function modelo(string $recurso): string
    {
        abort_unless(isset(self::MODELOS[$recurso]), 404);

        return self::MODELOS[$recurso];
    }

    private function regras(string $recurso, bool $parcial): array
    {
        $prefixo = $parcial ? 'sometimes|' : 'required|';

        return match ($recurso) {
            'noticias' => ['titulo' => $prefixo.'string|max:150', 'resumo' => $prefixo.'string|max:255', 'texto_completo' => $prefixo.'string', 'imagem' => $prefixo.'nullable|file|mimes:jpg,jpeg,png,webp|max:5120', 'tipo' => $prefixo.'in:noticia,evento,campanha', 'data_evento' => $prefixo.'nullable|date'],
            'materiais' => ['titulo' => $prefixo.'string|max:150', 'descricao' => 'sometimes|nullable|string', 'arquivo_pdf' => $prefixo.'file|mimes:pdf|max:10240', 'imagem_capa' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:5120', 'categoria' => $prefixo.'string|max:100'],
            'transparencia' => ['titulo' => $prefixo.'string|max:150', 'ano_referencia' => $prefixo.'integer|min:1900|max:2200', 'tipo_documento' => $prefixo.'in:Relatório Anual,Balancete,Estatuto,Certidão,Outros', 'arquivo_pdf' => $prefixo.'file|mimes:pdf|max:10240'],
            'programas' => ['titulo' => $prefixo.'string|max:150', 'resumo' => $prefixo.'string|max:250', 'texto_completo' => $prefixo.'string', 'categoria' => $prefixo.'in:Neuropedagogia,Saúde e Bem-estar,Assistência Social,Educação,Outros', 'imagem_capa' => $prefixo.'nullable|image|mimes:jpg,jpeg,png,webp|max:5120', 'status' => $prefixo.'in:ativo,inativo'],
        };
    }

    private function arquivos(Request $request, array $dados, string $recurso): array
    {
        foreach (['imagem', 'imagem_capa', 'arquivo_pdf'] as $campo) {
            if ($request->hasFile($campo)) {
                $dados[$campo] = $request->file($campo)->store($recurso, 'public');
            }
        }

        return $dados;
    }
}
