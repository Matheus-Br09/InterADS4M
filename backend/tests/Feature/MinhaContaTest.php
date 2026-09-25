<?php

namespace Tests\Feature;

use App\Models\Apadrinhamento;
use App\Models\Apoiador;
use App\Models\Crianca;
use App\Models\DoacaoMensal;
use App\Models\DoacaoUnica;
use App\Models\Voluntario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

class MinhaContaTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_painel_mostra_apoiador_suas_doacoes_apadrinhamentos_e_voluntariado(): void
    {
        $apoiador = $this->criarApoiador(['nome_completo' => 'Mariana Costa']);
        $crianca = $this->criarCrianca();
        $this->criarDoacaoUnica($apoiador, ['valor' => 120.00, 'metodo_pagamento' => 'Pix']);
        $this->criarDoacaoMensal($apoiador, ['valor_mensal' => 80.00]);
        $this->criarApadrinhamento($apoiador, $crianca);
        $this->criarVoluntario($apoiador, ['status' => 'aprovado']);

        $this->actingAs($apoiador, 'apoiador')
            ->get('/minha-conta')
            ->assertOk()
            ->assertSee('Mariana Costa')
            ->assertSee('120,00')
            ->assertSee('80,00')
            ->assertSee('aprovado');
    }

    public function test_painel_avisa_quando_o_apoio_ainda_nao_tem_nenhum_registro(): void
    {
        $apoiador = $this->criarApoiador();

        $this->actingAs($apoiador, 'apoiador')
            ->get('/minha-conta')
            ->assertOk()
            ->assertSee('Nenhuma doa')
            ->assertSee('Nenhum apadrinhamento ativo no momento.')
            ->assertSee('Nenhuma assinatura de doa')
            ->assertSee('como volunt');
    }

    public function test_painel_mostra_apenas_os_registros_do_apoio_logado(): void
    {
        $apoiador = $this->criarApoiador(['nome_completo' => 'Titular da conta']);
        $outro = $this->criarApoiador(['nome_completo' => 'Apoio de outra pessoa']);
        $this->criarDoacaoUnica($apoiador, ['valor' => 33.00, 'metodo_pagamento' => 'Pix']);
        $this->criarDoacaoUnica($outro, ['valor' => 999.00, 'metodo_pagamento' => 'Boleto']);

        $this->actingAs($apoiador, 'apoiador')
            ->get('/minha-conta')
            ->assertOk()
            ->assertSee('33,00')
            ->assertDontSee('999,00');
    }

    public function test_painel_nao_quebra_quando_o_apoio_tem_apadrinhamento_cancelado(): void
    {
        $apoiador = $this->criarApoiador();
        $crianca = $this->criarCrianca();
        $this->criarApadrinhamento($apoiador, $crianca, ['status' => 'cancelado']);

        $this->actingAs($apoiador, 'apoiador')
            ->get('/minha-conta')
            ->assertOk()
            ->assertSee('cancelado');
    }

    public function test_painel_sobrerede_doacao_recem_confirmada(): void
    {
        $apoiador = $this->criarApoiador();

        $this->actingAs($apoiador, 'apoiador')
            ->withSession(['success' => 'Doação realizada com sucesso!'])
            ->get('/minha-conta')
            ->assertOk()
            ->assertSee('Doação realizada com sucesso!');
    }

    public function test_painel_exibe_o_vinculo_de_cada_apadrinhamento_com_a_crianca(): void
    {
        $apoiador = $this->criarApoiador();
        $crianca = $this->criarCrianca();
        $apadrinhamento = $this->criarApadrinhamento($apoiador, $crianca);

        $this->assertSame($apadrinhamento->crianca_id, $crianca->id);
        $this->assertSame([$crianca->id], $crianca->apadrinhamentos->pluck('crianca_id')->all());
        $this->assertSame([$apadrinhamento->id], $apoiador->apadrinhamentos->pluck('id')->all());
    }

    public function test_relacionamentos_do_apoio_agrupam_doacoes_e_voluntariado(): void
    {
        $apoiador = $this->criarApoiador();
        $this->criarDoacaoUnica($apoiador);
        $this->criarDoacaoUnica($apoiador);
        $this->criarDoacaoMensal($apoiador);
        $voluntario = $this->criarVoluntario($apoiador);

        $this->assertCount(2, $apoiador->doacoesUnicas);
        $this->assertCount(1, $apoiador->doacoesMensais);
        $this->assertInstanceOf(Voluntario::class, $apoiador->voluntario);
        $this->assertSame($voluntario->id, $apoiador->voluntario->id);
        $this->assertNull($this->criarApoiador()->voluntario);
    }

    public function test_modelos_de_apadrinhamento_e_doacao_apontam_para_o_apoio_e_a_crianca(): void
    {
        $apoiador = $this->criarApoiador();
        $crianca = $this->criarCrianca();
        $apadrinhamento = $this->criarApadrinhamento($apoiador, $crianca);
        $doacao = $this->criarDoacaoUnica($apoiador);
        $this->criarDoacaoMensal($apoiador);

        $this->assertInstanceOf(Apoiador::class, $apadrinhamento->apoiador);
        $this->assertInstanceOf(Crianca::class, $apadrinhamento->crianca);
        $this->assertInstanceOf(Apadrinhamento::class, $crianca->apadrinhamentos->first());
        $this->assertInstanceOf(DoacaoUnica::class, $apoiador->doacoesUnicas->first());
        $this->assertInstanceOf(DoacaoMensal::class, $apoiador->doacoesMensais->first());
        $this->assertSame($doacao->id, $apoiador->doacoesUnicas->first()->id);
    }

    public function test_painel_mostra_o_valor_real_da_doacao_mensal(): void
    {
        $apoiador = $this->criarApoiador();
        $this->criarDoacaoMensal($apoiador, ['valor_mensal' => 39.90]);

        $this->actingAs($apoiador, 'apoiador')
            ->get('/minha-conta')
            ->assertOk()
            ->assertSee('39,90')
            ->assertDontSee('0,00/mês');
    }

    public function test_painel_mostra_a_area_de_atuacao_real_do_voluntario(): void
    {
        $apoiador = $this->criarApoiador();
        $this->criarVoluntario($apoiador, ['area_atuacao' => 'Fisioterapia']);

        $this->actingAs($apoiador, 'apoiador')
            ->get('/minha-conta')
            ->assertOk()
            ->assertSee('Fisioterapia')
            ->assertDontSee('>Inscrito<');
    }
}
