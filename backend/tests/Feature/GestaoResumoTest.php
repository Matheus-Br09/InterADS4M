<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

class GestaoResumoTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_resumo_conta_pendentes_e_em_analise(): void
    {
        $this->actingAsGestor();
        $this->criarDoacaoUnica(null, ['status' => 'pendente', 'protocolo' => 'DOA-R1']);
        $this->criarDoacaoUnica(null, ['status' => 'pendente', 'protocolo' => 'DOA-R2']);
        $this->criarDoacaoUnica(null, ['status' => 'concluido', 'protocolo' => 'DOA-R3']);
        foreach (range(1, 3) as $i) {
            $this->criarVoluntario($this->criarApoiador(), ['status' => 'em_analise']);
        }
        $this->criarVoluntario($this->criarApoiador(), ['status' => 'aprovado']);

        $this->getJson('/api/v1/gestao/resumo')
            ->assertOk()
            ->assertJsonPath('dados.doacoes_pendentes', 2)
            ->assertJsonPath('dados.voluntarios_em_analise', 3);
    }

    public function test_recebido_no_mes_soma_so_concluidas_do_mes_corrente(): void
    {
        $this->actingAsGestor();
        $this->criarDoacaoUnica(null, ['status' => 'concluido', 'valor' => 50.00, 'protocolo' => 'DOA-M1', 'data_doacao' => now()->startOfMonth()->addHour()]);
        $this->criarDoacaoUnica(null, ['status' => 'concluido', 'valor' => 30.00, 'protocolo' => 'DOA-M2', 'data_doacao' => now()->subMonthNoOverflow()->endOfMonth()]);
        $this->criarDoacaoUnica(null, ['status' => 'pendente', 'valor' => 99.00, 'protocolo' => 'DOA-M3', 'data_doacao' => now()]);

        $this->getJson('/api/v1/gestao/resumo')
            ->assertOk()
            ->assertJsonPath('dados.recebido_no_mes', 50);
    }

    public function test_resumo_sem_dados_devolve_zeros(): void
    {
        $this->actingAsGestor();

        $this->getJson('/api/v1/gestao/resumo')
            ->assertOk()
            ->assertJsonPath('dados.doacoes_pendentes', 0)
            ->assertJsonPath('dados.voluntarios_em_analise', 0)
            ->assertJsonPath('dados.recebido_no_mes', 0);
    }

    public function test_resumo_exige_gestor(): void
    {
        $this->getJson('/api/v1/gestao/resumo')->assertUnauthorized();

        $this->actingAs($this->criarApoiador(), 'apoiador');
        $this->getJson('/api/v1/gestao/resumo')->assertForbidden();
    }
}
