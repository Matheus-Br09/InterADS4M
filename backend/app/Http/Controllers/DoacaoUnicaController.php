<?php

namespace App\Http\Controllers;

use App\Mail\NovaDoacao;
use App\Models\DoacaoUnica;
use App\Support\AvisaGestao;
use App\Support\PixBrCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DoacaoUnicaController extends Controller
{
    // Doação por Pix aberta a qualquer visitante: registra a intenção e devolve o "copia e cola".
    public function store(Request $request): JsonResponse
    {
        if (! PixBrCode::configurado()) {
            return response()->json(['message' => 'Doações por Pix estão temporariamente indisponíveis.'], 503);
        }

        $validated = $request->validate([
            'valor' => 'required|numeric|decimal:0,2|min:5|max:99999.99',
            'nome_doador' => 'nullable|string|max:255',
            'email_doador' => 'nullable|email|max:255',
        ]);

        $doacao = DoacaoUnica::create([
            'valor' => $validated['valor'],
            'nome_doador' => $validated['nome_doador'] ?? null,
            'email_doador' => $validated['email_doador'] ?? null,
            'protocolo' => $this->novoProtocolo(),
            'metodo_pagamento' => 'pix',
            'status' => 'pendente',
            'data_doacao' => now(),
        ]);

        AvisaGestao::enviar(new NovaDoacao($doacao));

        $copiaECola = PixBrCode::gerar(
            config('ong.pix_chave'),
            config('ong.pix_nome'),
            config('ong.pix_cidade'),
            number_format((float) $validated['valor'], 2, '.', ''),
            $doacao->protocolo,
        );

        return response()->json([
            'dados' => [
                'protocolo' => $doacao->protocolo,
                'valor' => number_format((float) $validated['valor'], 2, '.', ''),
                'status' => 'pendente',
            ],
            'pix' => [
                'chave' => config('ong.pix_chave'),
                'copia_e_cola' => $copiaECola,
            ],
            'message' => 'Doação registrada. Pague o Pix para concluí-la.',
        ], 201);
    }

    private function novoProtocolo(): string
    {
        do {
            $protocolo = 'SOS'.strtoupper(Str::random(10));
        } while (DoacaoUnica::where('protocolo', $protocolo)->exists());

        return $protocolo;
    }
}
