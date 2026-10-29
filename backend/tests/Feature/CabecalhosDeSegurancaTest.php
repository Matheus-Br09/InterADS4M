<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

class CabecalhosDeSegurancaTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_respostas_tem_cabecalhos_de_seguranca(): void
    {
        foreach (['/up', '/api/v1/config-publica', '/api/v1/rota-que-nao-existe'] as $uri) {
            $this->get($uri)
                ->assertHeader('X-Content-Type-Options', 'nosniff')
                ->assertHeader('X-Frame-Options', 'DENY')
                ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        }
    }

    public function test_hsts_so_em_https(): void
    {
        $this->get('http://localhost/api/v1/config-publica')->assertHeaderMissing('Strict-Transport-Security');

        $this->get('https://localhost/api/v1/config-publica')
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000');
    }

    public function test_proxy_confiavel_com_https_conta_como_conexao_segura(): void
    {
        config(['trustedproxy.proxies' => ['10.0.0.1']]);

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
            ->get('/api/v1/config-publica', ['X-Forwarded-Proto' => 'https'])
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000');
    }

    public function test_sem_proxy_confiavel_o_ip_vem_da_conexao_e_nao_do_cabecalho(): void
    {
        $this->assertSame([], config('trustedproxy.proxies'));

        Route::get('/_teste/ip', fn (\Illuminate\Http\Request $r) => $r->ip().'|'.($r->isSecure() ? 'https' : 'http'));

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
            ->get('/_teste/ip', ['X-Forwarded-For' => '1.2.3.4', 'X-Forwarded-Proto' => 'https'])
            ->assertSee('10.0.0.1|http', false);
    }

    public function test_com_proxy_confiavel_o_ip_encaminhado_e_usado(): void
    {
        config(['trustedproxy.proxies' => ['10.0.0.1']]);

        Route::get('/_teste/ip', fn (\Illuminate\Http\Request $r) => $r->ip());

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
            ->get('/_teste/ip', ['X-Forwarded-For' => '1.2.3.4'])
            ->assertSee('1.2.3.4', false);
    }

    public function test_falsos_x_forwarded_for_nao_driblam_o_limite_de_login(): void
    {
        $gestor = $this->criarGestor();

        for ($i = 1; $i <= 10; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
                ->postJson('/api/v1/auth/entrar', ['email' => $gestor->email, 'senha' => 'errada'], ['X-Forwarded-For' => "9.9.9.{$i}"]);
        }

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
            ->postJson('/api/v1/auth/entrar', ['email' => $gestor->email, 'senha' => 'errada'], ['X-Forwarded-For' => '9.9.9.200'])
            ->assertStatus(429);
    }

    public function test_lista_de_proxies_ignora_curingas_e_espacos(): void
    {
        $this->assertSame([], \App\Support\ProxiesConfiaveis::aPartirDe(null));
        $this->assertSame([], \App\Support\ProxiesConfiaveis::aPartirDe(' , '));
        $this->assertSame(['10.0.0.1', '172.16.0.0/12'], \App\Support\ProxiesConfiaveis::aPartirDe(' 10.0.0.1, *, 172.16.0.0/12 '));
    }

    public function test_disco_local_nao_serve_curriculos_por_url(): void
    {
        $this->assertFalse(config('filesystems.disks.local.serve'));

        $this->get('/storage/curriculos/x.pdf')->assertNotFound();

        foreach (Route::getRoutes() as $rota) {
            $this->assertStringStartsNotWith('storage/', $rota->uri());
        }
    }

    public function test_quem_nao_e_gestor_nao_ve_perfil_nem_troca_senha(): void
    {
        $this->actingAs($this->criarApoiador(), 'apoiador');

        $this->getJson('/api/v1/auth/eu')->assertForbidden();
        $this->putJson('/api/v1/auth/senha', [
            'senha_atual' => 'senha123',
            'senha' => 'NovaSenha123',
            'senha_confirmation' => 'NovaSenha123',
        ])->assertForbidden();
    }

    public function test_gestor_com_troca_obrigatoria_ainda_alcanca_a_troca_de_senha(): void
    {
        $gestor = $this->criarGestor(['trocar_senha_obrigatorio' => true]);
        $this->actingAs($gestor, 'apoiador');

        $this->getJson('/api/v1/gestao/doacoes')->assertStatus(403);
        $this->putJson('/api/v1/auth/senha', [
            'senha_atual' => 'senha123',
            'senha' => 'NovaSenha123',
            'senha_confirmation' => 'NovaSenha123',
        ])->assertOk();
    }

    public function test_fuso_horario_da_aplicacao_e_recife(): void
    {
        $this->assertSame('America/Recife', config('app.timezone'));
        $this->assertSame('America/Recife', now()->getTimezone()->getName());
    }
}
