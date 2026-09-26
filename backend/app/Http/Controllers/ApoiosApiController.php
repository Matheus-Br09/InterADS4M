<?php

namespace App\Http\Controllers;

use App\Models\Apadrinhamento;
use App\Models\DoacaoMensal;
use App\Models\DoacaoUnica;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApoiosApiController extends Controller
{
    public function mensal(Request $request): array
    {
        $dados = $request->validate(['valor_mensal' => ['required', 'numeric', 'decimal:0,2', 'min:5', 'max:99999999.99'], 'dia_vencimento' => ['required', 'integer', 'between:1,31'], 'metodo_pagamento' => ['required', 'string', 'max:50']]);
        $dados['apoiador_id'] = $request->user('apoiador')->id;
        $dados['status'] = 'pendente';
        $dados['data_assinatura'] = now();

        return ['dados' => DoacaoMensal::create($dados), 'message' => 'Intenção de doação mensal registrada; nenhum pagamento foi realizado.'];
    }

    public function apadrinhar(Request $request): array
    {
        $dados = $request->validate(['crianca_id' => ['required', 'integer', 'exists:criancas,id'], 'valor_mensal' => ['required', 'numeric', 'decimal:0,2', 'min:5', 'max:99999999.99']]);
        $dados['apoiador_id'] = $request->user('apoiador')->id;
        $dados['status'] = 'pendente';
        $dados['data_inicio'] = now();

        return ['dados' => Apadrinhamento::create($dados), 'message' => 'Intenção de apadrinhamento registrada; nenhum pagamento foi realizado.'];
    }

    public function cancelar(Request $request, string $tipo, int $id): JsonResponse
    {
        $model = match ($tipo) {
            'doacoes' => DoacaoUnica::class, 'mensalidades' => DoacaoMensal::class, 'apadrinhamentos' => Apadrinhamento::class
        };
        $item = $model::where('apoiador_id', $request->user('apoiador')->id)->findOrFail($id);
        $item->update(['status' => 'cancelado']);

        return response()->json(status: 204);
    }
}
