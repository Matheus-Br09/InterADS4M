<?php

namespace App\Support;

class BuscaLike
{
    /** Texto da busca pronto para LIKE: sem espaços nas pontas e com %, _ e ! tratados como letras comuns. Vazio vira null. */
    public static function padrao(?string $texto): ?string
    {
        $texto = trim((string) $texto);

        if ($texto === '') {
            return null;
        }

        return '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $texto).'%';
    }

    /** Condição SQL para usar com whereRaw/orWhereRaw junto de padrao(). O "!" é o escape (funciona igual em SQLite e MySQL). */
    public static function condicao(string $coluna): string
    {
        return "{$coluna} LIKE ? ESCAPE '!'";
    }
}
