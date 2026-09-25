<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

/**
 * A rotação de senha é manual, conta por conta, e o e-mail digitado errado é uma
 * conta que continua com a senha antiga. Estes testes travam a lista que o
 * gestor usa para não errar. O e-mail é NOT NULL e unique no schema, então toda
 * conta listada tem e-mail e recebe um comando de rotação.
 */
class ListaDeApoiadoresTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_lista_as_contas_com_email_e_papel(): void
    {
        $this->criarApoiador(['email' => 'ana@ong.org.br', 'nome_completo' => 'Ana Souza']);
        $this->criarApoiador(['email' => 'bruno@ong.org.br', 'tipo_usuario' => 'admin']);

        $codigo = Artisan::call('apoiadores:listar');
        $saida = Artisan::output();

        $this->assertSame(0, $codigo);
        $this->assertStringContainsString('ana@ong.org.br', $saida);
        $this->assertStringContainsString('Ana Souza', $saida);
        $this->assertStringContainsString('admin', $saida);
    }

    public function test_mostra_o_comando_de_rotacao_de_cada_conta(): void
    {
        $this->criarApoiador(['email' => 'ana@ong.org.br']);
        $this->criarApoiador(['email' => 'gestao@ong.org.br', 'tipo_usuario' => 'admin']);

        Artisan::call('apoiadores:listar');
        $saida = Artisan::output();

        $this->assertStringContainsString('php artisan apoiador:senha ana@ong.org.br', $saida);
        // A conta de gestão é promovida pelo comando próprio; o outro não
        // promoveria um apoiador comum, o que mudaria o papel de quem se
        // cadastrou sozinho.
        $this->assertStringContainsString('php artisan gestor:senha gestao@ong.org.br', $saida);
    }

    public function test_aponta_a_conta_que_ainda_usa_a_senha_publica_do_seed(): void
    {
        $this->criarApoiador(['email' => 'ana@ong.org.br']);
        $this->criarApoiador([
            'email' => 'velha@ong.org.br',
            'senha' => Hash::make('senha123'),
        ]);

        Artisan::call('apoiadores:listar');
        $saida = Artisan::output();

        $this->assertStringContainsString('senha publica do seed', $saida);
        $this->assertStringContainsString('velha@ong.org.br', $saida);
    }

    public function test_conta_ja_rotacionada_fica_marcada_como_ok(): void
    {
        $this->criarApoiador([
            'email' => 'ana@ong.org.br',
            'senha' => Hash::make('Outra-Senha-9'),
        ]);

        Artisan::call('apoiadores:listar');
        $saida = Artisan::output();

        $this->assertStringNotContainsString('senha publica do seed', $saida);
    }

    public function test_o_filtro_rotacionar_esconde_as_contas_ja_resolvidas(): void
    {
        $this->criarApoiador(['email' => 'ana@ong.org.br', 'senha' => Hash::make('Outra-Senha-9')]);
        $this->criarApoiador(['email' => 'velha@ong.org.br', 'senha' => Hash::make('senha123')]);

        Artisan::call('apoiadores:listar', ['--rotacionar' => true]);
        $saida = Artisan::output();

        $this->assertStringContainsString('velha@ong.org.br', $saida);
        $this->assertStringNotContainsString('ana@ong.org.br', $saida);
    }

    public function test_avisa_quando_nao_ha_nenhuma_conta(): void
    {
        $codigo = Artisan::call('apoiadores:listar');
        $saida = Artisan::output();

        $this->assertSame(0, $codigo);
        $this->assertStringContainsString('Nenhum apoiador no banco', $saida);
    }
}
