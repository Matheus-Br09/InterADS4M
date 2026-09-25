<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Cpf implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! static::valido($value)) {
            $fail('O CPF informado é inválido. Confira os números e tente de novo.');
        }
    }

    public static function apenasDigitos(mixed $value): string
    {
        return preg_replace('/\D/', '', (string) $value) ?? '';
    }

    public static function valido(mixed $cpf): bool
    {
        $cpf = static::apenasDigitos($cpf);

        if (strlen($cpf) !== 11) {
            return false;
        }

        // 000.000.000-00, 111.111.111-11 e afins passam no cálculo, mas não são CPFs
        if (preg_match('/^(\d)\1{10}$/', $cpf) === 1) {
            return false;
        }

        for ($tamanho = 9; $tamanho < 11; $tamanho++) {
            $soma = 0;

            for ($digito = 0; $digito < $tamanho; $digito++) {
                $soma += ((int) $cpf[$digito]) * (($tamanho + 1) - $digito);
            }

            $resto = $soma % 11;
            $verificador = (int) $cpf[$tamanho];

            if ($verificador !== ($resto < 2 ? 0 : 10 - $resto)) {
                return false;
            }
        }

        return true;
    }

    public static function formatar(mixed $cpf): string
    {
        $cpf = static::apenasDigitos($cpf);

        if (strlen($cpf) !== 11) {
            return $cpf;
        }

        return substr($cpf, 0, 3).'.'.substr($cpf, 3, 3).'.'.substr($cpf, 6, 3).'-'.substr($cpf, 9, 2);
    }
}
