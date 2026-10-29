<?php

namespace App\Http\Controllers;

use App\Models\DoacaoUnica;
use App\Support\BuscaLike;
use Illuminate\Http\Request;

class GestaoDoacoesController extends Controller
{
    private const CAMPOS = ['id', 'protocolo', 'valor', 'status', 'nome_doador', 'email_doador', 'data_doacao'];

    public function index(Request $request): array
    {
        $filtros = $request->validate([
            'status' => ['nullable', 'in:pendente,concluido,cancelado,expirado'],
            'busca' => ['nullable', 'string', 'max:100'],
        ]);

        $consulta = DoacaoUnica::query()->select(self::CAMPOS);

        if (! empty($filtros['status'])) {
            $consulta->where('status', $filtros['status']);
        }

        if ($padrao = BuscaLike::padrao($filtros['busca'] ?? null)) {
            $consulta->where(fn ($q) => $q
                ->whereRaw(BuscaLike::condicao('nome_doador'), [$padrao])
                ->orWhereRaw(BuscaLike::condicao('protocolo'), [$padrao]));
        }

        return ['dados' => $consulta->orderByDesc('data_doacao')->orderByDesc('id')->paginate(20)];
    }

    public function update(Request $request, int $id): array
    {
        $doacao = DoacaoUnica::findOrFail($id);
        $dados = $request->validate(['status' => ['required', 'in:concluido,cancelado']]);

        if (! in_array($doacao->status, ['pendente', 'expirado'], true)) {
            abort(422, 'Só é possível alterar doações pendentes ou expiradas.');
        }

        $doacao->update($dados);

        return ['dados' => $doacao->refresh()];
    }

    public function anonimizar(int $id): array
    {
        $doacao = DoacaoUnica::findOrFail($id);
        $doacao->update(['nome_doador' => null, 'email_doador' => null]);

        return ['dados' => $doacao->refresh()];
    }
}
