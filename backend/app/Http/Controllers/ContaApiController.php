<?php

namespace App\Http\Controllers;

use App\Support\DadosDaConta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContaApiController extends Controller
{
    public function perfil(Request $request): array
    {
        return ['dados' => DadosDaConta::de($request->user('apoiador'))];
    }

    public function update(Request $request): array
    {
        $validated = $request->validate([
            'nome_completo' => ['sometimes', 'string', 'max:255'], 'celular' => ['sometimes', 'nullable', 'string', 'max:255'],
            'cep' => ['sometimes', 'nullable', 'string', 'max:255'], 'logradouro' => ['sometimes', 'nullable', 'string', 'max:255'],
            'numero' => ['sometimes', 'nullable', 'string', 'max:255'], 'complemento' => ['sometimes', 'nullable', 'string', 'max:255'],
            'bairro' => ['sometimes', 'nullable', 'string', 'max:255'], 'cidade' => ['sometimes', 'nullable', 'string', 'max:255'],
            'estado' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);
        $apoiador = $request->user('apoiador');
        $apoiador->forceFill($validated)->save();

        return ['dados' => DadosDaConta::de($apoiador->refresh())];
    }

    public function historico(Request $request, string $tipo): array|JsonResponse
    {
        $apoiador = $request->user('apoiador');
        $dados = match ($tipo) {
            'doacoes' => $apoiador->doacoesUnicas()->latest('id')->get(),
            'mensalidades' => $apoiador->doacoesMensais()->latest('id')->get(),
            'apadrinhamentos' => $apoiador->apadrinhamentos()->with('crianca', 'recompensas')->latest('id')->get()->map(fn ($item) => [
                'id' => $item->id, 'crianca' => $item->crianca?->only(['id', 'nome', 'imagem_perfil']), 'status' => $item->status,
                'data_inicio' => $item->data_inicio, 'recompensas' => $item->recompensas->map->only(['id', 'titulo', 'arquivo_midia']),
            ]),
        };

        return ['dados' => $dados];
    }
}
