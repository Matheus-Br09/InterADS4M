<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use RuntimeException;
use Tests\TestCase;

class MensagensEmPortuguesTest extends TestCase
{
    use RefreshDatabase;

    public function test_validacao_de_valor_vem_em_portugues(): void
    {
        config(['ong.pix_chave' => '12345678000199', 'ong.pix_nome' => 'SOS Tudo pelo Social', 'ong.pix_cidade' => 'PAULISTA']);
        $r = $this->postJson('/api/v1/doacoes', ['valor' => '4.99'])->assertStatus(422);

        $msg = $r->json('errors.valor.0');
        $this->assertStringContainsStringIgnoringCase('valor', $msg);
        $this->assertStringContainsString('pelo menos 5', $msg);
        $this->assertStringNotContainsString('must be', (string) $r->json('message'));
    }

    public function test_campo_obrigatorio_usa_nome_traduzido(): void
    {
        $this->postJson('/api/v1/voluntariado', [])
            ->assertStatus(422)
            ->assertJsonPath('errors.nome_completo.0', 'O campo nome completo é obrigatório.');
    }

    public function test_404_json_em_portugues(): void
    {
        $this->getJson('/api/v1/rota-que-nao-existe')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Não encontrado.']);
    }

    public function test_500_em_portugues_sem_vazar_detalhes(): void
    {
        config(['app.debug' => false]);
        Route::get('/api/v1/_explode', fn () => throw new RuntimeException('segredo'));

        $this->getJson('/api/v1/_explode')
            ->assertStatus(500)
            ->assertExactJson(['message' => 'Algo deu errado. Tente de novo em instantes.']);
    }

    public function test_429_em_portugues(): void
    {
        Route::get('/api/v1/_limite', fn () => 'ok')->middleware('throttle:1,1');

        $this->getJson('/api/v1/_limite')->assertOk();
        $this->getJson('/api/v1/_limite')
            ->assertStatus(429)
            ->assertJsonPath('message', 'Muitas tentativas. Aguarde um pouco e tente de novo.');
    }
}
