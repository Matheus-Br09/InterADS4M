<?php

namespace App\Support;

use App\Models\Apoiador;

class DadosDaConta
{
    /**
     * Create a new class instance.
     */
    public static function de(Apoiador $apoiador): array
    {
        return [
            'id' => $apoiador->id, 'nome_completo' => $apoiador->nome_completo, 'email' => $apoiador->email,
            'celular' => $apoiador->celular, 'cep' => $apoiador->cep, 'logradouro' => $apoiador->logradouro,
            'numero' => $apoiador->numero, 'complemento' => $apoiador->complemento, 'bairro' => $apoiador->bairro,
            'cidade' => $apoiador->cidade, 'estado' => $apoiador->estado, 'tipo_usuario' => $apoiador->tipo_usuario,
            'trocar_senha_obrigatorio' => (bool) $apoiador->trocar_senha_obrigatorio,
        ];
    }
}
