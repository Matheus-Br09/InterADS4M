<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use App\Support\BuscaLike;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

// Lista de pessoas inscritas em "Receba novidades da ONG": ver, baixar em planilha e remover quem pedir para sair.
class GestaoNewsletterController extends Controller
{
    public function index(Request $request): array
    {
        $filtros = $request->validate(['busca' => ['nullable', 'string', 'max:100']]);

        $consulta = Newsletter::query()->select(['id', 'email', 'nome', 'data_inscricao']);

        if ($padrao = BuscaLike::padrao($filtros['busca'] ?? null)) {
            $consulta->where(fn ($q) => $q
                ->whereRaw(BuscaLike::condicao('nome'), [$padrao])
                ->orWhereRaw(BuscaLike::condicao('email'), [$padrao]));
        }

        return ['dados' => $consulta->orderByDesc('data_inscricao')->orderByDesc('id')->paginate(20)];
    }

    // Planilha CSV: separador ";" e BOM no início, como o Excel em português espera.
    public function exportar(): StreamedResponse
    {
        $nome = 'inscritos-newsletter-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () {
            $saida = fopen('php://output', 'w');
            fwrite($saida, "\xEF\xBB\xBF");
            fputcsv($saida, ['Nome', 'E-mail', 'Data da inscrição'], ';', '"', '');

            Newsletter::query()->orderBy('data_inscricao')->orderBy('id')->chunk(500, function ($inscritos) use ($saida) {
                foreach ($inscritos as $inscrito) {
                    fputcsv($saida, [
                        self::celula($inscrito->nome),
                        self::celula($inscrito->email),
                        $inscrito->data_inscricao ? Carbon::parse($inscrito->data_inscricao)->format('d/m/Y H:i') : '',
                    ], ';', '"', '');
                }
            });

            fclose($saida);
        }, $nome, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function destroy(int $id): JsonResponse
    {
        Newsletter::findOrFail($id)->delete();

        return response()->json(status: 204);
    }

    // Quem se inscreve escolhe o próprio nome. Se ele começar com = + - @, o Excel pode tratá-lo como fórmula:
    // um apóstrofo na frente faz a planilha mostrar só o texto.
    private static function celula(?string $texto): string
    {
        $texto = (string) $texto;

        return $texto !== '' && str_contains("=+-@\t\r", $texto[0]) ? "'".$texto : $texto;
    }
}
