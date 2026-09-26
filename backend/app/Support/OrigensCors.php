<?php

namespace App\Support;

/**
 * Descobre quais origens o CORS aceita, a partir de uma variável de ambiente.
 *
 * Existe para o momento de publicar não exigir editar código versionado: o
 * backend vai para o servidor com o `config:cache` já gerado, e quem publica é
 * a pessoa que mexe no `.env`, não quem mexe em PHP. Escrever a origem no
 * `config/cors.php` faria o ajuste virar commit, com risco de o `*` de
 * desenvolvimento acabar em produção.
 */
class OrigensCors
{
    /**
     * @return array<int, string>
     */
    public static function aPartirDe(?string $valor): array
    {
        // Sem valor (ou vazio) vale a regra de desenvolvimento: qualquer origem.
        if ($valor === null || trim($valor) === '') {
            return ['*'];
        }

        $origens = array_filter(
            array_map('trim', explode(',', $valor)),
            fn (string $origem): bool => $origem !== ''
        );

        // " , , " só tem separador: vale a regra de desenvolvimento, e não uma
        // lista vazia — lista vazia quebraria o site inteiro sem mensagem clara.
        return $origens === [] ? ['*'] : array_values($origens);
    }
}
