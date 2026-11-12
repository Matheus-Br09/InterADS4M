<?php

namespace App\Http\Controllers;

use App\Models\DocumentoTransparencia;
use App\Models\MaterialDidatico;
use App\Models\Noticia;
use App\Models\ProgramaAcao;
use App\Support\PixBrCode;

class ConfigPublicaController extends Controller
{
    // Só o que o site pode mostrar a qualquer visitante: a chave Pix e o e-mail da gestão ficam de fora.
    // "secoes" diz só se há algo publicado (sim/não), para o site esconder links de páginas vazias.
    public function __invoke(): array
    {
        return [
            'dados' => [
                'pix_disponivel' => PixBrCode::configurado(),
                'whatsapp' => config('ong.whatsapp') ?: null,
                'instagram' => config('ong.instagram') ?: null,
                'secoes' => [
                    'noticias' => Noticia::query()->exists(),
                    'programas' => ProgramaAcao::query()->where('status', 'ativo')->exists(),
                    'materiais' => MaterialDidatico::query()->exists(),
                    'transparencia' => DocumentoTransparencia::query()->exists(),
                ],
            ],
        ];
    }
}
