<?php

namespace Tests\Feature;

use App\Models\DoacaoUnica;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

class ApoioUnicoTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_apoio_logado_registra_uma_doacao_unica_em_seu_nome(): void
    {
        $apoiador = $this->criarApoiador();

        $response = $this->actingAs($apoiador, 'apoiador')
            ->post('/apoio-unico', [
                'valor' => '50.00',
                'metodo_pagamento' => 'pix',
            ]);

        $response->assertRedirect(route('minha-conta'))
            ->assertSessionHas('success', 'Doação realizada com sucesso!');

        $this->assertDatabaseHas('doacoes_unicas', [
            'apoiador_id' => $apoiador->id,
            'valor' => 50.00,
            'metodo_pagamento' => 'pix',
        ]);
    }

    public function test_doacao_unica_registrada_aparece_no_painel_do_apoio(): void
    {
        $apoiador = $this->criarApoiador();

        $this->actingAs($apoiador, 'apoiador')
            ->post('/apoio-unico', ['valor' => '75.50', 'metodo_pagamento' => 'cartao_credito'])
            ->assertRedirect(route('minha-conta'));

        $this->actingAs($apoiador, 'apoiador')
            ->get('/minha-conta')
            ->assertOk()
            ->assertSee('75,50');

        $this->assertSame(1, $apoiador->doacoesUnicas()->count());
    }

    public function test_doacao_unica_valida_o_valor_minimo_de_cinco_reais(): void
    {
        $apoiador = $this->criarApoiador();

        $this->actingAs($apoiador, 'apoiador')
            ->post('/apoio-unico', ['valor' => '4.99', 'metodo_pagamento' => 'pix'])
            ->assertSessionHasErrors('valor');

        $this->assertDatabaseCount('doacoes_unicas', 0);
    }

    public function test_doacao_unica_recusa_forma_de_pagamento_desconhecida(): void
    {
        $apoiador = $this->criarApoiador();

        $this->actingAs($apoiador, 'apoiador')
            ->post('/apoio-unico', ['valor' => '50.00', 'metodo_pagamento' => 'bitcoin'])
            ->assertSessionHasErrors('metodo_pagamento');

        $this->assertDatabaseCount('doacoes_unicas', 0);
    }

    public function test_doacao_unica_exige_valor_e_forma_de_pagamento(): void
    {
        $apoiador = $this->criarApoiador();

        $this->actingAs($apoiador, 'apoiador')
            ->post('/apoio-unico', [])
            ->assertSessionHasErrors(['valor', 'metodo_pagamento']);

        $this->assertDatabaseCount('doacoes_unicas', 0);
    }

    public function test_formulario_de_apoio_unico_exibe_as_formas_de_pagamento_aceitas(): void
    {
        $apoiador = $this->criarApoiador();

        $this->actingAs($apoiador, 'apoiador')
            ->get('/apoio-unico')
            ->assertOk()
            ->assertSee('name="valor"', false)
            ->assertSee('name="metodo_pagamento"', false)
            ->assertSee('cartao_credito', false);
    }

    public function test_valor_exatamente_no_minimo_e_aceito(): void
    {
        $apoiador = $this->criarApoiador();

        $this->actingAs($apoiador, 'apoiador')
            ->post('/apoio-unico', ['valor' => '5', 'metodo_pagamento' => 'boleto'])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('doacoes_unicas', ['valor' => 5.00, 'metodo_pagamento' => 'boleto']);
    }

    public function test_doacao_unica_gravada_pertence_ao_apoio_que_a_fez(): void
    {
        $apoiador = $this->criarApoiador();
        $intruso = $this->criarApoiador();

        $this->actingAs($apoiador, 'apoiador')
            ->post('/apoio-unico', ['valor' => '60.00', 'metodo_pagamento' => 'pix']);

        $this->actingAs($apoiador, 'apoiador')
            ->post('/apoio-unico', ['valor' => '60.00', 'metodo_pagamento' => 'pix'])
            ->assertRedirect(route('minha-conta'));

        $this->assertSame(0, $intruso->doacoesUnicas()->count());
        $this->assertSame(2, $apoiador->doacoesUnicas()->count());
        $this->assertSame(2, DoacaoUnica::count());
    }
}
