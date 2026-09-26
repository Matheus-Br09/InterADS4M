<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

class IntegracaoFrontendTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_api_entrega_csrf_e_cadastro_json(): void
    {
        $this->getJson('/api/v1/csrf')->assertOk()->assertJsonStructure(['token']);

        $response = $this->postJson('/api/v1/auth/cadastro', [
            'nome_completo' => 'Pessoa Frontend', 'email' => 'frontend@example.org', 'cpf' => '11122233387',
            'senha' => 'SenhaFrontend123', 'senha_confirmation' => 'SenhaFrontend123',
        ]);

        $response->assertCreated()->assertJsonPath('dados.email', 'frontend@example.org');
        $this->assertAuthenticated('apoiador');
    }

    public function test_api_de_login_retorna_conta_e_logout(): void
    {
        $apoiador = $this->criarApoiador(['email' => 'frontend@example.org', 'senha' => Hash::make('SenhaFrontend123')]);

        $this->postJson('/api/v1/auth/entrar', ['email' => $apoiador->email, 'senha' => 'SenhaFrontend123'])
            ->assertOk()->assertJsonPath('dados.id', $apoiador->id);
        $this->getJson('/api/v1/auth/eu')->assertOk()->assertJsonPath('dados.email', $apoiador->email);
        $this->postJson('/api/v1/auth/sair')->assertNoContent();
        $this->getJson('/api/v1/auth/eu')->assertUnauthorized();
    }

    public function test_api_de_conteudo_publica_programas_e_pagina(): void
    {
        $this->criarPrograma(['titulo' => 'Programa frontend']);
        $this->getJson('/api/v1/conteudos/programas')->assertOk()
            ->assertJsonPath('dados.0.titulo', 'Programa frontend')
            ->assertJsonStructure(['dados', 'paginacao']);
    }

    public function test_api_de_doacao_registra_intencao_pendente(): void
    {
        $apoiador = $this->criarApoiador();
        $this->actingAs($apoiador, 'apoiador')->postJson('/api/v1/doacoes', ['valor' => '25.00', 'metodo_pagamento' => 'pix'])
            ->assertCreated()->assertJsonPath('dados.status', 'pendente');
        $this->assertDatabaseHas('doacoes_unicas', ['apoiador_id' => $apoiador->id, 'status' => 'pendente']);
    }
}
