<?php

namespace App\Http\Controllers;

class ConfigPublicaController extends Controller
{
    // Só o que o site pode mostrar a qualquer visitante: a chave Pix e o e-mail da gestão ficam de fora.
    public function __invoke(): array
    {
        return [
            'dados' => [
                'pix_disponivel' => filled(config('ong.pix_chave')),
                'whatsapp' => config('ong.whatsapp') ?: null,
                'instagram' => config('ong.instagram') ?: null,
            ],
        ];
    }
}
