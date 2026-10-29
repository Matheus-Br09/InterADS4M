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

// A área do apoiador saiu do MVP, mas os modelos (e o histórico no banco)
// continuam: estes testes travam as relações entre apoio, criança e doações.
class RelacionamentosDosModelosTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_apadrinhamento_liga_o_apoio_a_crianca(): void
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
}
