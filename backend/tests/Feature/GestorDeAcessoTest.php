<?php

namespace Tests\Feature;

use App\Models\Apoiador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

class GestorDeAcessoTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_comando_define_a_senha_e_promove_o_apoiador_a_gestor(): void
    {
        $apoiador = $this->criarApoiador(['email' => 'gestora@ong.org.br']);

        $this->assertSame('apoiador', $apoiador->fresh()->tipo_usuario);

        $codigo = Artisan::call('gestor:senha', ['email' => 'gestora@ong.org.br', 'senha' => 'Nova-senha-123']);
        $saida = Artisan::output();

        $this->assertSame(0, $codigo);
        $this->assertStringContainsString('Senha de '.$apoiador->nome_completo.' atualizada.', $saida);
        $this->assertStringContainsString('promovida a gestor', $saida);

        $atualizado = $apoiador->fresh();
        $this->assertSame('admin', $atualizado->tipo_usuario);
        $this->assertTrue(Hash::check('Nova-senha-123', $atualizado->senha));
    }

    public function test_gestor_definido_pelo_comando_entra_no_painel(): void
    {
        $apoiador = $this->criarApoiador(['email' => 'gestora@ong.org.br']);

        Artisan::call('gestor:senha', ['email' => 'gestora@ong.org.br', 'senha' => 'Nova-senha-123']);

        $this->post('/entrar', $this->credenciaisDoApoiador($apoiador, 'Nova-senha-123'))
            ->assertRedirect('/minha-conta');

        $this->assertAuthenticatedAs($apoiador->fresh(), 'apoiador');

        $this->get('/')->assertOk()->assertSee('Painel de Testes ONG SOS');
    }

    public function test_comando_promove_um_apoiador_comum_e_libera_o_painel(): void
    {
        $apoiador = $this->criarApoiador(['email' => 'apoiador@ong.org.br']);

        $this->actingAs($apoiador, 'apoiador');
        $this->get('/')->assertRedirect('/entrar');

        Artisan::call('gestor:senha', ['email' => 'apoiador@ong.org.br', 'senha' => 'Outra-senha-123']);

        $this->actingAs($apoiador->fresh(), 'apoiador');
        $this->get('/')->assertOk();
    }

    public function test_comando_aceita_o_e_maiusculo(): void
    {
        $apoiador = $this->criarApoiador(['email' => 'Gestora@ONG.org.br']);

        Artisan::call('gestor:senha', ['email' => '  gestora@ong.org.br  ', 'senha' => 'Nova-senha-123']);

        $this->assertSame('admin', $apoiador->fresh()->tipo_usuario);
    }

    public function test_comando_avisa_quando_o_apoiador_nao_existe(): void
    {
        $codigo = Artisan::call('gestor:senha', ['email' => 'ninguem@ong.org.br', 'senha' => 'Nova-senha-123']);
        $saida = Artisan::output();

        $this->assertSame(1, $codigo);
        $this->assertStringContainsString('Nenhum apoiador encontrado', $saida);
        $this->assertSame(0, Apoiador::count());
    }

    public function test_comando_recusa_senha_fraca(): void
    {
        $apoiador = $this->criarApoiador(['email' => 'gestora@ong.org.br']);

        // Comprimento sozinho não segura: 8 caracteres sem variedade de classe
        // cai em segundos num ataque de dicionário.
        $codigo = Artisan::call('gestor:senha', ['email' => 'gestora@ong.org.br', 'senha' => '12345678']);
        $saida = Artisan::output();

        $this->assertSame(1, $codigo);
        $this->assertStringContainsString('pelo menos 8 caracteres', $saida);
        $this->assertStringContainsString('maiúscula', $saida);
        $this->assertSame('apoiador', $apoiador->fresh()->tipo_usuario);
        $this->assertTrue(Hash::check('senha123', $apoiador->fresh()->senha));
    }

    public function test_comando_recusa_senha_curta(): void
    {
        $apoiador = $this->criarApoiador(['email' => 'gestora@ong.org.br']);

        $codigo = Artisan::call('gestor:senha', ['email' => 'gestora@ong.org.br', 'senha' => '123']);
        $saida = Artisan::output();

        $this->assertSame(1, $codigo);
        $this->assertStringContainsString('pelo menos 8 caracteres', $saida);
        $this->assertTrue(Hash::check('senha123', $apoiador->fresh()->senha));
    }

    public function test_comando_gera_senha_quando_ela_e_omitida(): void
    {
        $apoiador = $this->criarApoiador(['email' => 'gestora@ong.org.br']);

        $codigo = Artisan::call('gestor:senha', ['email' => 'gestora@ong.org.br']);
        $saida = Artisan::output();

        $this->assertSame(0, $codigo);
        $this->assertSame('admin', $apoiador->fresh()->tipo_usuario);

        preg_match('/Senha gerada: (\S+)/', $saida, $achado);

        $this->assertNotEmpty($achado, 'A senha gerada deve aparecer na saída do comando.');
        $this->assertTrue(Hash::check($achado[1], $apoiador->fresh()->senha));
    }
}
