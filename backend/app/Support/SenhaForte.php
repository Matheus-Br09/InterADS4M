<?php

namespace App\Support;

final class SenhaForte
{
    /*
    | Fora do alfabeto: i, l, o, 0 e 1 (e o I e o L maiúsculos, que se confundem
    | com eles). É o conjunto que o usuário erra ao dictate a senha ou escreve
    | num papel. Sobram 61 caracteres, então 16 caracteres dão ~95 bits.
    */
    private const ALFABETO = 'ABCDEFGHJKMNPQRSTUVWXYZ'.'abcdefghjkmnpqrstuvwxyz'.'23456789'.'!@#$%';

    /*
    | Uma classe garantida por senha. Sem isso, uma em cada dez senhas geradas
    | saía sem nenhum dígito (só 8 dos 61 caracteres são número) e era
    | reprovada pela própria regra de força: o comando falhava sozinho, ao
    | acaso, e o gestor ficava sem senha nova.
    */
    private const CLASSES = [
        'ABCDEFGHJKMNPQRSTUVWXYZ',
        'abcdefghjkmnpqrstuvwxyz',
        '23456789',
        '!@#$%',
    ];

    public static function gerar(int $tamanho = 16): string
    {
        if ($tamanho < count(self::CLASSES)) {
            return self::sortearDoAlfabeto($tamanho);
        }

        $senha = '';

        foreach (self::CLASSES as $classe) {
            $senha .= $classe[random_int(0, mb_strlen($classe) - 1)];
        }

        return self::embaralhar($senha.self::sortearDoAlfabeto($tamanho - count(self::CLASSES)));
    }

    public static function temForcaSuficiente(string $senha, int $tamanhoMinimo = 8): bool
    {
        if (mb_strlen($senha) < $tamanhoMinimo) {
            return false;
        }

        // Uma senha só com letras minúsculas ("aaaaaaaa") passa no tamanho, mas
        // cai em segundos num ataque de dicionário; exigir variedade de classes
        // segura a barra sem obrigar nada do usuário.
        return preg_match('/[a-z]/', $senha) === 1
            && preg_match('/[A-Z]/', $senha) === 1
            && preg_match('/\d/', $senha) === 1;
    }

    private static function sortearDoAlfabeto(int $quantidade): string
    {
        $alfabeto = self::ALFABETO;
        $ultimo = mb_strlen($alfabeto) - 1;
        $senha = '';

        for ($posicao = 0; $posicao < $quantidade; $posicao++) {
            $senha .= $alfabeto[random_int(0, $ultimo)];
        }

        return $senha;
    }

    /*
    | Fisher-Yates: embaralhar a senha inteira, e não só a parte sorteada, para
    | que a classe garantida não fique sempre na mesma posição (senão todo mundo
    | receberia "!a2..." no começo e a posição viraria um padrão próprio).
    */
    private static function embaralhar(string $senha): string
    {
        $caracteres = mb_str_split($senha);

        for ($i = count($caracteres) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$caracteres[$i], $caracteres[$j]] = [$caracteres[$j], $caracteres[$i]];
        }

        return implode('', $caracteres);
    }
}
