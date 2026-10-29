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

    public function test_proxy_com_https_conta_como_conexao_segura(): void
    {
        $this->get('/api/v1/config-publica', ['X-Forwarded-Proto' => 'https', 'X-Forwarded-For' => '203.0.113.9'])
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000');
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
