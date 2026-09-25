<?php

namespace App\Support;

final class SenhaForte
{
    /*
    | Fora do alfabeto: i, l, o, 0 e 1 (e o I e o L maiúsculos, que se confundem
    | com eles). É o conjunto que o usuário erra ao dictate a senha ou escreve
    | num papel. Sobram 59 caracteres, então 16 caracteres dão ~94 bits.
    */
    private const ALFABETO = 'ABCDEFGHJKMNPQRSTUVWXYZ'.'abcdefghjkmnpqrstuvwxyz'.'23456789'.'!@#$%';

    public static function gerar(int $tamanho = 16): string
    {
        $alfabeto = self::ALFABETO;
        $ultimo = mb_strlen($alfabeto) - 1;
        $senha = '';

        for ($posicao = 0; $posicao < $tamanho; $posicao++) {
            $senha .= $alfabeto[random_int(0, $ultimo)];
        }

        return $senha;
    }

    public static function temForcaSuficiente(string $senha, int $tamanhoMinimo = 8): bool
    {
        if (mb_strlen($senha) < $tamanhoMinimo) {
            return false;
        }

        // Uma senha só com letras minúsculas ("aaaaaaaa") passa no tamanho, mas
        // cai em segundos num ataque de dicionário; exigir variety de classes
        // segura a barra sem Obligar nada do usuário.
        return preg_match('/[a-z]/', $senha) === 1
            && preg_match('/[A-Z]/', $senha) === 1
            && preg_match('/\d/', $senha) === 1;
    }
}
