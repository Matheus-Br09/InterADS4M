<?php

namespace Tests\Feature;

use App\Models\Apoiador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

// No MVP só a gestão da ONG entra no sistema, pela API usada pelo painel.
class LoginLogoutGestorTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_gestor_entra_com_email_e_senha(): void
    {
        $gestor = $this->criarGestor();

        $this->postJson('/api/v1/auth/entrar', $this->credenciaisDoApoiador($gestor))
            ->assertOk()
            ->assertJsonPath('dados.id', $gestor->id);

        $this->assertAuthenticated('apoiador');
        $this->assertSame($gestor->id, auth('apoiador')->id());
    }

    public function test_login_renova_o_identificador_de_sessao(): void
    {
        $gestor = $this->criarGestor();

        $this->getJson('/api/v1/csrf');
        $antes = session()->getId();

        $this->postJson('/api/v1/auth/entrar', $this->credenciaisDoApoiador($gestor))->assertOk();

        $this->assertNotSame($antes, session()->getId());
    }

    public function test_senha_errada_nao_autentica(): void
    {
        $gestor = $this->criarGestor();

        $this->postJson('/api/v1/auth/entrar', ['email' => $gestor->email, 'senha' => 'senha-errada'])
            ->assertUnauthorized()
            ->assertJsonPath('message', 'E-mail ou senha inválidos.');

        $this->assertGuest('apoiador');
    }

    public function test_e_mail_desconhecido_responde_igual_a_senha_errada(): void
    {
        $this->assertDatabaseMissing('apoiadores', ['email' => 'ninguem@exemplo.com']);

        $this->postJson('/api/v1/auth/entrar', ['email' => 'ninguem@exemplo.com', 'senha' => 'senha123'])
            ->assertUnauthorized()
            ->assertJsonPath('message', 'E-mail ou senha inválidos.');

        $this->assertGuest('apoiador');
        $this->assertSame(0, Apoiador::count());
    }

    public function test_recusa_login_sem_informacoes(): void
    {
        $this->postJson('/api/v1/auth/entrar', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'senha']);

        $this->assertGuest('apoiador');
    }

    public function test_senha_errada_de_apoiador_comum_tambem_responde_401(): void
    {
        // Senha errada não pode revelar se a conta é ou não da gestão.
        $apoiador = $this->criarApoiador();

        $this->postJson('/api/v1/auth/entrar', ['email' => $apoiador->email, 'senha' => 'senha-errada'])
            ->assertUnauthorized();

        $this->assertGuest('apoiador');
    }

    public function test_gestor_logado_sai_da_conta_e_perde_o_acesso(): void
    {
        $this->actingAsGestor();

        $this->postJson('/api/v1/auth/sair')->assertNoContent();

        $this->assertGuest('apoiador');
        $this->getJson('/api/v1/auth/eu')->assertUnauthorized();
    }

    public function test_rotas_da_gestao_recusam_visitante(): void
    {
        $this->getJson('/api/v1/auth/eu')->assertUnauthorized();
        $this->getJson('/api/v1/gestao/doacoes')->assertUnauthorized();
        $this->getJson('/api/v1/gestao/voluntarios')->assertUnauthorized();
    }

    public function test_guard_de_apoiador_nao_confunde_com_o_guard_padrao_de_usuarios(): void
    {
        $this->actingAsGestor();

        $this->assertTrue(auth('apoiador')->check());
        $this->assertFalse(auth('web')->check());
    }
}
