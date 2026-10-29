<?php

namespace App\Http\Controllers;

use App\Support\PixBrCode;

class ConfigPublicaController extends Controller
{
    // Só o que o site pode mostrar a qualquer visitante: a chave Pix e o e-mail da gestão ficam de fora.
    public function __invoke(): array
    {
        return [
            'dados' => [
                'pix_disponivel' => PixBrCode::configurado(),
                'whatsapp' => config('ong.whatsapp') ?: null,
                'instagram' => config('ong.instagram') ?: null,
            ],
        ];
    }
}
