<?php

namespace Tests\Unit;

use App\Support\OrigensCors;
use PHPUnit\Framework\TestCase;

/**
 * A lista de origens do CORS é a configuração que separa "site de
 * desenvolvimento" de "site publicado". Um erro de digitação aqui derruba o
 * site inteiro (nenhuma origem permitida) ou abre a API para qualquer um
 * ('*' em produção), e quem escreve é a pessoa na hora de publicar, em arquivo
 * de ambiente, sem nenhum teste rodando por perto. Por isso a regra fica
 * testada aqui.
 */
class OrigensCorsTest extends TestCase
{
    public function test_sem_valor_aceita_qualquer_origem_para_desenvolvimento(): void
    {
        $this->assertSame(['*'], OrigensCors::aPartirDe(null));
        $this->assertSame(['*'], OrigensCors::aPartirDe(''));
        $this->assertSame(['*'], OrigensCors::aPartirDe('   '));
        $this->assertSame(['*'], OrigensCors::aPartirDe('*'));
    }

    public function test_le_a_origem_unica_publicada(): void
    {
        $this->assertSame(['https://ongsos.org.br'], OrigensCors::aPartirDe('https://ongsos.org.br'));
    }

    public function test_aceita_varias_origens_separadas_por_virgula(): void
    {
        $this->assertSame(
            ['https://ongsos.org.br', 'https://www.ongsos.org.br'],
            OrigensCors::aPartirDe('https://ongsos.org.br,https://www.ongsos.org.br')
        );
    }

    public function test_tira_o_espaco_que_vem_depois_da_virgula(): void
    {
        // O erro clássico de .env: "https://a.com, https://b.com" com um espaço
        // antes da segunda origem. Sem o trim, a origem com espaço não bate com
        // o que o navegador manda e o erro aparece como "CORS" do nada.
        $this->assertSame(
            ['https://a.com', 'https://b.com'],
            OrigensCors::aPartirDe(' https://a.com , https://b.com ')
        );
    }

    public function test_ignora_virgulas_vazias(): void
    {
        $this->assertSame(['https://a.com'], OrigensCors::aPartirDe('https://a.com,,'));
    }

    public function test_lista_so_com_virgulas_nao_derruba_o_site(): void
    {
        // Lista vazia em 'allowed_origins' bloqueia toda origem: o site inteiro
        // deixaria de funcionar sem nenhuma pista do motivo. Melhor cair em '*',
        // que é o comportamento de desenvolvimento, e alguém vê na hora.
        $this->assertSame(['*'], OrigensCors::aPartirDe(' , , '));
    }
}
