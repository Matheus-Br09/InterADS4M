<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

class GestaoBuscaTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_busca_encontra_doacao_por_nome_e_por_protocolo(): void
    {
        $this->actingAsGestor();
        $this->criarDoacaoUnica(null, ['protocolo' => 'DOA-100', 'nome_doador' => 'Maria Souza']);
        $this->criarDoacaoUnica(null, ['protocolo' => 'DOA-200', 'nome_doador' => 'Carlos Lima']);

        $this->getJson('/api/v1/gestao/doacoes?busca=maria')
            ->assertOk()
            ->assertJsonCount(1, 'dados.data')
            ->assertJsonPath('dados.data.0.protocolo', 'DOA-100');

        $this->getJson('/api/v1/gestao/doacoes?busca=DOA-200')
            ->assertOk()
            ->assertJsonCount(1, 'dados.data')
            ->assertJsonPath('dados.data.0.nome_doador', 'Carlos Lima');
    }

    public function test_busca_combina_com_filtro_de_status(): void
    {
        $this->actingAsGestor();
        $this->criarDoacaoUnica(null, ['status' => 'pendente', 'protocolo' => 'DOA-300', 'nome_doador' => 'Ana Paula']);
        $this->criarDoacaoUnica(null, ['status' => 'concluido', 'protocolo' => 'DOA-301', 'nome_doador' => 'Ana Clara']);
        $this->criarDoacaoUnica(null, ['status' => 'pendente', 'protocolo' => 'DOA-302', 'nome_doador' => 'Zeca Dias']);

        $this->getJson('/api/v1/gestao/doacoes?status=pendente&busca=ana')
            ->assertOk()
            ->assertJsonCount(1, 'dados.data')
            ->assertJsonPath('dados.data.0.protocolo', 'DOA-300');
    }

    public function test_busca_trata_percentual_e_sublinhado_como_texto(): void
    {
        $this->actingAsGestor();
        $this->criarDoacaoUnica(null, ['protocolo' => 'DOA-400', 'nome_doador' => 'Pedro Alves']);

        $this->getJson('/api/v1/gestao/doacoes?busca=%25')->assertOk()->assertJsonCount(0, 'dados.data');
        $this->getJson('/api/v1/gestao/doacoes?busca=_')->assertOk()->assertJsonCount(0, 'dados.data');
    }

    public function test_busca_so_com_espacos_equivale_a_sem_busca(): void
    {
        $this->actingAsGestor();
        $this->criarDoacaoUnica(null, ['protocolo' => 'DOA-500']);
        $this->criarDoacaoUnica(null, ['protocolo' => 'DOA-501']);

        $this->getJson('/api/v1/gestao/doacoes?busca=%20%20')
            ->assertOk()
            ->assertJsonCount(2, 'dados.data');
    }

    public function test_busca_de_voluntario_acha_nome_proprio_email_e_nome_guardado_no_apoiador(): void
    {
        $this->actingAsGestor();
        $this->criarVoluntario($this->criarApoiador(), ['nome_completo' => 'Beatriz Gomes', 'email' => 'bia@exemplo.com']);
        $antigo = $this->criarApoiador(['nome_completo' => 'Joana Antiga', 'email' => 'joana@exemplo.com']);
        $this->criarVoluntario($antigo, ['nome_completo' => null, 'email' => null]);

        $this->getJson('/api/v1/gestao/voluntarios?busca=beatriz')->assertOk()->assertJsonCount(1, 'dados.data');
        $this->getJson('/api/v1/gestao/voluntarios?busca=bia@exemplo')->assertOk()->assertJsonCount(1, 'dados.data');
        $this->getJson('/api/v1/gestao/voluntarios?busca=joana')->assertOk()->assertJsonCount(1, 'dados.data');
        $this->getJson('/api/v1/gestao/voluntarios?busca=%25')->assertOk()->assertJsonCount(0, 'dados.data');
    }
}
