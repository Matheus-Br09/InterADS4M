<?php

namespace Tests\Feature;

use App\Models\DoacaoUnica;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoacaoAnonimaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'ong.pix_chave' => '12345678000199',
            'ong.pix_nome' => 'SOS Tudo pelo Social',
            'ong.pix_cidade' => 'PAULISTA',
        ]);
    }

    public function test_visitante_registra_doacao_sem_login(): void
    {
        $resposta = $this->postJson('/api/v1/doacoes', ['valor' => '50.00'])
            ->assertCreated()
            ->assertJsonPath('dados.status', 'pendente')
            ->assertJsonPath('dados.valor', '50.00')
            ->assertJsonPath('pix.chave', '12345678000199');

        $protocolo = $resposta->json('dados.protocolo');

        $this->assertMatchesRegularExpression('/^SOS[A-Z0-9]{10}$/', $protocolo);
        $this->assertStringContainsString('0513'.$protocolo, $resposta->json('pix.copia_e_cola'));
        $this->assertDatabaseHas('doacoes_unicas', ['protocolo' => $protocolo, 'apoiador_id' => null, 'metodo_pagamento' => 'pix']);
    }

    public function test_nome_e_email_sao_opcionais_mas_validados(): void
    {
        $this->postJson('/api/v1/doacoes', ['valor' => '50.00', 'email_doador' => 'nao-e-email'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email_doador');

        $this->postJson('/api/v1/doacoes', ['valor' => '50.00', 'nome_doador' => 'Maria', 'email_doador' => 'maria@example.org'])
            ->assertCreated();

        $this->assertDatabaseHas('doacoes_unicas', ['nome_doador' => 'Maria', 'email_doador' => 'maria@example.org']);
    }

    public function test_valor_invalido_e_recusado(): void
    {
        foreach (['50,00', '4.99', 'abc'] as $valor) {
            $this->postJson('/api/v1/doacoes', ['valor' => $valor])
                ->assertUnprocessable()
                ->assertJsonValidationErrors('valor');
        }
    }

    public function test_sem_chave_pix_configurada_responde_503_e_nao_grava(): void
    {
        config(['ong.pix_chave' => '']);

        $this->postJson('/api/v1/doacoes', ['valor' => '50.00'])
            ->assertStatus(503)
            ->assertJsonPath('message', 'Doações por Pix estão temporariamente indisponíveis.');

        $this->assertSame(0, DoacaoUnica::count());
    }

    public function test_sem_nome_do_recebedor_responde_503_e_nao_grava(): void
    {
        config(['ong.pix_nome' => '']);

        $this->postJson('/api/v1/doacoes', ['valor' => '50.00'])
            ->assertStatus(503)
            ->assertJsonPath('message', 'Doações por Pix estão temporariamente indisponíveis.');

        $this->assertSame(0, DoacaoUnica::count());
    }

    public function test_resposta_nao_expoe_outras_doacoes(): void
    {
        DoacaoUnica::create([
            'valor' => 20, 'metodo_pagamento' => 'pix', 'nome_doador' => 'Fulano', 'data_doacao' => now(),
        ]);

        $corpo = $this->postJson('/api/v1/doacoes', ['valor' => '50.00'])->assertCreated()->getContent();

        $this->assertStringNotContainsString('Fulano', $corpo);
    }
}
