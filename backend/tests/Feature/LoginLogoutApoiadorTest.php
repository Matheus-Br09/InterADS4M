<?php

namespace Tests\Feature;

use App\Models\Apoiador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

class LoginLogoutApoiadorTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_apoio_cadastrado_entra_no_painel_com_email_e_senha(): void
    {
        $apoiador = $this->criarApoiador();

        $response = $this->post('/entrar', $this->credenciaisDoApoiador($apoiador));

        $response->assertRedirect('/minha-conta');
        $this->assertAuthenticated('apoiador');
        $this->assertSame($apoiador->id, auth('apoiador')->id());
    }

    public function test_login_renova_o_identificador_de_sessao(): void
    {
        $apoiador = $this->criarApoiador();

        $this->get('/entrar');
        $antes = session()->getId();

        $this->post('/entrar', $this->credenciaisDoApoiador($apoiador));

        $this->assertNotSame($antes, session()->getId());
    }

    public function test_senha_errada_nao_autentica_o_apoio(): void
    {
        $apoiador = $this->criarApoiador();

        $this->post('/entrar', ['email' => $apoiador->email, 'senha' => 'senha-errada'])
            ->assertSessionHasErrors('email');

        $this->assertGuest('apoiador');
    }

    public function test_e_mail_desconhecido_nao_autentica_o_apoio(): void
    {
        $this->post('/entrar', ['email' => 'ninguem@exemplo.com', 'senha' => 'senha123'])
            ->assertSessionHasErrors('email');

        $this->assertGuest('apoiador');
    }

    public function test_recusa_login_sem_informacoes(): void
    {
        $this->post('/entrar', [])
            ->assertSessionHasErrors(['email', 'senha']);

        $this->assertGuest('apoiador');
    }

    public function test_apoio_logado_sai_da_conta_e_perde_o_acesso(): void
    {
        $apoiador = $this->criarApoiador();
        $this->actingAs($apoiador, 'apoiador');

        $this->post('/sair')->assertRedirect('/');

        $this->assertGuest('apoiador');
    }

    public function test_paginas_protegidas_redirecionam_para_o_login_quando_deslogado(): void
    {
        $this->get('/minha-conta')->assertRedirect('/entrar');
        $this->get('/apoio-unico')->assertRedirect('/entrar');
    }

    public function test_areas_protegidas_recusam_visitante_que_tenta_doar_sem_login(): void
    {
        $this->post('/apoio-unico', [
            'valor' => 50,
            'metodo_pagamento' => 'pix',
        ])->assertRedirect('/entrar');

        $this->assertDatabaseCount('doacoes_unicas', 0);
    }

    public function test_guard_de_apoiador_nao_confunde_com_o_guard_padrao_de_usuarios(): void
    {
        $apoiador = $this->criarApoiador();
        $this->actingAs($apoiador, 'apoiador');

        $this->assertTrue(auth('apoiador')->check());
        $this->assertFalse(auth('web')->check());
    }

    public function test_apoio_logado_acessa_o_painel_e_enxerga_o_proprio_nome(): void
    {
        $apoiador = $this->criarApoiador(['nome_completo' => 'Matheus Figueiredo']);
        $this->actingAs($apoiador, 'apoiador');

        $this->get('/minha-conta')
            ->assertOk()
            ->assertSee('Matheus Figueiredo')
            ->assertSee($apoiador->email);
    }

    public function test_guard_nao_encontra_apoio_inexistente_na_base(): void
    {
        $this->assertDatabaseMissing('apoiadores', ['id' => 999]);

        $this->post('/entrar', ['email' => 'fantasma@exemplo.com', 'senha' => 'senha123'])
            ->assertSessionHasErrors('email');

        $this->assertSame(0, Apoiador::count());
    }
}
