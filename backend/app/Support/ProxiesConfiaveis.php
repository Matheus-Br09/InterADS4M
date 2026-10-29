<?php

namespace App\Support;

/**
 * Lê TRUSTED_PROXIES (IPs ou faixas CIDR separados por vírgula).
 *
 * Quem não está na lista não pode dizer qual é o IP do visitante: se qualquer
 * um fosse confiável, bastaria mandar um X-Forwarded-For diferente a cada
 * requisição para driblar os limites de tentativas de login. Vazio significa
 * "não confiar em proxy nenhum". Os curingas '*' e '**' são ignorados de
 * propósito, pelo mesmo motivo.
 */
class ProxiesConfiaveis
{
    /**
     * @return array<int, string>
     */
    public static function aPartirDe(?string $valor): array
    {
        if ($valor === null) {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', explode(',', $valor)),
            fn (string $proxy): bool => $proxy !== '' && ! in_array($proxy, ['*', '**'], true)
        ));
    }
}
