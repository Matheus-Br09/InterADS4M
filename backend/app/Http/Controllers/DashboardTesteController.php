<?php

namespace App\Http\Controllers;

use App\Models\Apoiador;
use App\Models\DoacaoMensal;
use App\Models\DoacaoUnica;
use App\Models\Newsletter;
use Illuminate\Http\Request;

class DashboardTesteController extends Controller
{
    // Estas rotas são públicas (alimentam o site), então respondem com o que
    // o site precisa mostrar e nada mais: sem nome, contato, endereço, valor
    // de doação individual ou dado sensível.
    public function apiApoiadores()
    {
        // Apoiador é pessoa física: a API pública devolve só os números da
        // transparência, nunca a lista com nome, endereço ou valores pagos.
        return response()->json([
            'status' => 'success',
            'total' => Apoiador::count(),
            'doadores_mensais' => DoacaoMensal::where('status', 'ativo')->distinct('apoiador_id')->count('apoiador_id'),
            'doadores_unicos' => DoacaoUnica::where('status', 'concluido')->distinct('apoiador_id')->count('apoiador_id'),
            'total_mensal' => (float) DoacaoMensal::where('status', 'ativo')->sum('valor_mensal'),
            'total_unico' => (float) DoacaoUnica::where('status', 'concluido')->sum('valor'),
        ]);
    }

    public function storeNewsletter(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:newsletter,email',
            'nome' => 'nullable|string|max:100',
            'aceite_privacidade' => 'accepted',
        ]);

        $lead = Newsletter::create([
            'email' => $validated['email'],
            'nome' => $validated['nome'] ?? null,
            'data_inscricao' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'mensagem' => 'E-mail cadastrado com sucesso na newsletter!',
            'dados' => $lead,
        ], 201);
    }
}
