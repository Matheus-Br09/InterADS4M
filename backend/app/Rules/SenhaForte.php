<?php

namespace App\Rules;

use App\Support\SenhaForte as Senha;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/*
| A regra vive aqui (e não em uma validação de array) para poder ser aplicada
| no cadastro e reaproveitada no comando de rotação, sempre com a mesma
| mensagem. O critério está em App\Support\SenhaForte.
|
| "min:6" acceptava "123456": um ataque de dicionário contra o bcrypt abre
| espaço sem esforço. Oito caracteres com variedade de classes segura a barra
| sem obrigar o usuário a nada.
*/
class SenhaForte implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! Senha::temForcaSuficiente($value)) {
            $fail('A senha precisa ter pelo menos 8 caracteres, com maiúscula, minúscula e número.');
        }
    }
}
