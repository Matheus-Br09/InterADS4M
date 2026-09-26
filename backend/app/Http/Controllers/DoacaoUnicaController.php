<?php

namespace App\Http\Controllers;

use App\Models\DoacaoUnica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoacaoUnicaController extends Controller
{
    public function show()
    {
        return view('apoiador.apoio-unico');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'valor' => 'required|numeric|decimal:0,2|min:5|max:99999999.99',
            'metodo_pagamento' => 'required|in:pix,cartao_credito,boleto',
        ]);

        $apoiador = Auth::guard('apoiador')->user();

        $doacao = DoacaoUnica::create([
            'apoiador_id' => $apoiador->id,
            'valor' => $validated['valor'],
            'metodo_pagamento' => $validated['metodo_pagamento'],
            'status' => 'pendente',
            'data_doacao' => now(),
        ]);

        if ($request->is('api/v1/*')) {
            return response()->json(['dados' => $doacao, 'message' => 'Intenção registrada; nenhum pagamento foi realizado.'], 201);
        }

        return redirect()->route('minha-conta')->with('success', 'Intenção de doação registrada. Nenhum pagamento foi realizado.');
    }
}
