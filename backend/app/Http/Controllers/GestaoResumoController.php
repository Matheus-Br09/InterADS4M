<?php

namespace App\Http\Controllers;

use App\Models\DoacaoUnica;
use App\Models\Voluntario;

class GestaoResumoController extends Controller
{
    public function show(): array
    {
        $recebido = DoacaoUnica::where('status', 'concluido')
            ->whereBetween('data_doacao', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('valor');

        return ['dados' => [
            'doacoes_pendentes' => DoacaoUnica::where('status', 'pendente')->count(),
            'voluntarios_em_analise' => Voluntario::where('status', 'em_analise')->count(),
            'recebido_no_mes' => (float) $recebido,
        ]];
    }
}
