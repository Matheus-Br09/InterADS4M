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
        $this->assertStringContainsString('Senha de '.$apoiador->nome_completo.' atualizada em ', $saida);
        $this->assertStringContainsString('promovida a gestor', $saida);

        $atualizado = $apoiador->fresh();
        $this->assertSame('admin', $atualizado->tipo_usuario);
        $this->assertTrue(Hash::check('Nova-senha-123', $atualizado->senha));
        $this->assertNotNull($atualizado->senha_alterada_em, 'definir a senha do gestor tambem e uma rotacao');
    }

    public function test_gestor_definido_pelo_comando_entra_no_painel(): void
    {
        $apoiador = $this->criarApoiador(['email' => 'gestora@ong.org.br']);

        Artisan::call('gestor:senha', ['email' => 'gestora@ong.org.br', 'senha' => 'Nova-senha-123']);

        $this->postJson('/api/v1/auth/entrar', $this->credenciaisDoApoiador($apoiador, 'Nova-senha-123'))
            ->assertOk();

        $this->assertAuthenticatedAs($apoiador->fresh(), 'apoiador');

        $this->getJson('/api/v1/gestao/doacoes')->assertOk();
    }

    public function test_comando_promove_um_apoiador_comum_e_libera_o_painel(): void
    {
        $apoiador = $this->criarApoiador(['email' => 'apoiador@ong.org.br']);

        $this->actingAs($apoiador, 'apoiador');
        $this->getJson('/api/v1/gestao/doacoes')->assertForbidden();

        Artisan::call('gestor:senha', ['email' => 'apoiador@ong.org.br', 'senha' => 'Outra-senha-123']);

        $this->actingAs($apoiador->fresh(), 'apoiador');
        $this->getJson('/api/v1/gestao/doacoes')->assertOk();
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

    public function test_gestor_criar_cria_admin_sem_cpf_e_exige_troca_de_senha(): void
    {
        $codigo = Artisan::call('gestor:criar', ['nome' => 'Gestora Nova', 'email' => 'Nova@Ong.org.br']);
        $saida = Artisan::output();

        $this->assertSame(0, $codigo);

        $gestor = Apoiador::where('email', 'nova@ong.org.br')->firstOrFail();
        $this->assertSame('Gestora Nova', $gestor->nome_completo);
        $this->assertSame('admin', $gestor->tipo_usuario);
        $this->assertNull($gestor->cpf);
        $this->assertTrue($gestor->trocar_senha_obrigatorio);

        // A senha aparece uma única vez na saída e confere com o hash gravado.
        $this->assertSame(1, preg_match('/Senha gerada: (\S+)/', $saida, $m));
        $this->assertTrue(Hash::check($m[1], $gestor->senha));
    }

    public function test_gestor_criar_recusa_email_repetido(): void
    {
        $this->criarApoiador(['email' => 'repetido@ong.org.br']);

        $codigo = Artisan::call('gestor:criar', ['nome' => 'Outra Pessoa', 'email' => 'REPETIDO@ong.org.br']);

        $this->assertSame(1, $codigo);
        $this->assertSame(1, Apoiador::where('email', 'repetido@ong.org.br')->count());
        $this->assertStringContainsString('Já existe uma conta', Artisan::output());
    }
}
