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
            'valor'            => 'required|numeric|min:5',
            'metodo_pagamento' => 'required|in:pix,cartao_credito,boleto',
        ]);

        $apoiador = Auth::guard('apoiador')->user();

        DoacaoUnica::create([
            'apoiador_id'      => $apoiador->id,
            'valor'            => $validated['valor'],
            'metodo_pagamento' => $validated['metodo_pagamento'],
            'status'           => 'concluido',
            'data_doacao'      => now(),
        ]);

        return redirect()->route('minha-conta')->with('success', 'Doação realizada com sucesso!');
    }
}