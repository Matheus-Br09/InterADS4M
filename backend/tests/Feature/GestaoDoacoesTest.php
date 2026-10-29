<?php

namespace Tests\Feature;

use App\Models\DoacaoUnica;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

class GestaoDoacoesTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_gestor_lista_doacoes_filtrando_por_status(): void
    {
        $this->actingAsGestor();
        $this->criarDoacaoUnica(null, ['status' => 'pendente', 'protocolo' => 'DOA-1']);
        $this->criarDoacaoUnica(null, ['status' => 'concluido', 'protocolo' => 'DOA-2']);

        $this->getJson('/api/v1/gestao/doacoes?status=pendente')
            ->assertOk()
            ->assertJsonCount(1, 'dados.data')
            ->assertJsonPath('dados.data.0.protocolo', 'DOA-1');

        $this->getJson('/api/v1/gestao/doacoes')
            ->assertOk()
            ->assertJsonCount(2, 'dados.data');
    }

    public function test_gestor_confirma_doacao_pendente(): void
    {
        $this->actingAsGestor();
        $doacao = $this->criarDoacaoUnica(null, ['status' => 'pendente', 'protocolo' => 'DOA-3']);

        $this->patchJson("/api/v1/gestao/doacoes/{$doacao->id}", ['status' => 'concluido'])
            ->assertOk()
            ->assertJsonPath('dados.status', 'concluido');

        $this->assertSame('concluido', $doacao->refresh()->status);
    }

    public function test_gestor_confirma_doacao_expirada(): void
    {
        $this->actingAsGestor();
        $doacao = $this->criarDoacaoUnica(null, ['status' => 'expirado', 'protocolo' => 'DOA-4']);

        $this->patchJson("/api/v1/gestao/doacoes/{$doacao->id}", ['status' => 'concluido'])
            ->assertOk()
            ->assertJsonPath('dados.status', 'concluido');
    }

    public function test_status_invalido_e_recusado(): void
    {
        $this->actingAsGestor();
        $doacao = $this->criarDoacaoUnica(null, ['status' => 'pendente', 'protocolo' => 'DOA-5']);

        $this->patchJson("/api/v1/gestao/doacoes/{$doacao->id}", ['status' => 'pago'])
            ->assertUnprocessable();
    }

    public function test_anonimizar_remove_nome_e_email_mas_mantem_valor(): void
    {
        $this->actingAsGestor();
        $doacao = $this->criarDoacaoUnica(null, [
            'protocolo' => 'DOA-6',
            'nome_doador' => 'Maria',
            'email_doador' => 'maria@teste.com',
            'valor' => 75.00,
        ]);

        $this->deleteJson("/api/v1/gestao/doacoes/{$doacao->id}")
            ->assertOk()
            ->assertJsonPath('dados.nome_doador', null)
            ->assertJsonPath('dados.email_doador', null);

        $doacao = DoacaoUnica::findOrFail($doacao->id);
        $this->assertNull($doacao->nome_doador);
        $this->assertNull($doacao->email_doador);
        $this->assertEquals(75.00, $doacao->valor);
    }

    public function test_visitante_e_apoiador_comum_nao_acessam(): void
    {
        $this->getJson('/api/v1/gestao/doacoes')->assertUnauthorized();

        $this->actingAs($this->criarApoiador(), 'apoiador');
        $this->getJson('/api/v1/gestao/doacoes')->assertForbidden();
    }
}
