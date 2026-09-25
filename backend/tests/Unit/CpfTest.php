<?php

namespace Tests\Unit;

use App\Rules\Cpf;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CpfTest extends TestCase
{
    public static function cpfsValidos(): array
    {
        return [
            'sem mascara' => ['11122233387'],
            'com mascara' => ['111.222.333-87'],
            'com espaco' => ['111.222.333 87'],
            'outro valido' => ['12345678908'],
            'com zeros no fim' => ['98765432100'],
        ];
    }

    public static function cpfsInvalidos(): array
    {
        return [
            'digito verificador errado' => ['111.222.333-44'],
            'digitos repetidos' => ['111.111.111-11'],
            'zeros' => ['000.000.000-00'],
            'falta um digito' => ['1234567890'],
            'digito a mais' => ['123456789012'],
            'letras' => ['abcdefghijk'],
            'mascara pela metade' => ['111.222.333'],
            'vazio' => [''],
        ];
    }

    #[DataProvider('cpfsValidos')]
    public function test_aceita_cpf_valido(string $cpf): void
    {
        $this->assertTrue(Cpf::valido($cpf));
        $this->assertEmpty($this->erros($cpf));
    }

    #[DataProvider('cpfsInvalidos')]
    public function test_recusa_cpf_invalido(string $cpf): void
    {
        $this->assertFalse(Cpf::valido($cpf));
    }

    public function test_recusa_cpf_invalido_pela_regra(): void
    {
        // Campo vazio fica por conta do 'required': o Laravel não roda regra
        // comum em valor em branco, então a lista abaixo é só dos preenchidos.
        foreach (['111.222.333-44', '111.111.111-11', '000.000.000-00', '1234567890', 'abcdefghijk'] as $cpf) {
            $this->assertNotEmpty($this->erros($cpf), "O CPF {$cpf} deveria ser recusado.");
        }
    }

    public function test_campo_em_branco_e_barrado_pelo_required(): void
    {
        $this->assertNotEmpty(
            Validator::make(['cpf' => ''], ['cpf' => ['required', new Cpf]])->errors()->get('cpf'),
        );
    }

    public function test_mensagem_de_erro_explica_que_o_cpf_e_invalido(): void
    {
        $this->assertSame(
            ['O CPF informado é inválido. Confira os números e tente de novo.'],
            $this->erros('111.222.333-44'),
        );
    }

    public function test_apenas_digitos_tira_mascara_e_espacos(): void
    {
        $this->assertSame('11122233387', Cpf::apenasDigitos('111.222.333-87'));
        $this->assertSame('11122233387', Cpf::apenasDigitos(' 111 222 333 87 '));
        $this->assertSame('123', Cpf::apenasDigitos('abc123'));
        $this->assertSame('', Cpf::apenasDigitos(null));
    }

    public function test_formatar_coloca_mascara_de_tres_dois(): void
    {
        $this->assertSame('111.222.333-87', Cpf::formatar('11122233387'));
        $this->assertSame('111.222.333-87', Cpf::formatar('111.222.333-87'));
        $this->assertSame('123', Cpf::formatar('123'));
    }

    private function erros(mixed $cpf): array
    {
        return Validator::make(['cpf' => $cpf], ['cpf' => [new Cpf]])->errors()->get('cpf');
    }
}
