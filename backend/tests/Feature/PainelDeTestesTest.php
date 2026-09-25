<?php

namespace Tests\Feature;

use App\Models\Apoiador;
use App\Models\Crianca;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

class PainelDeTestesTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_painel_de_testes_abre_com_o_banco_conectado(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Painel de Testes ONG SOS')
            ->assertSee('Conectado: MySQL (ong)');
    }

    public function test_painel_de_testes_mostra_os_indicadores_calculados(): void
    {
        $apoiador = $this->criarApoiador();
        $crianca = $this->criarCrianca();
        $this->criarApadrinhamento($apoiador, $crianca);
        $this->criarDoacaoUnica($apoiador);
        $this->criarPrograma();
        $this->criarVoluntario($apoiador);

        $this->assertSame(1, Apoiador::count());
        $this->assertSame(1, Crianca::count());

        $this->get('/')
            ->assertOk()
            ->assertSee($crianca->nome)
            ->assertSee($apoiador->nome_completo)
            ->assertSee('R$ 100,00');
    }

    public function test_painel_de_testes_avisa_quando_o_banco_esta_vazio(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Nenhuma criança cadastrada ainda.')
            ->assertSee('Nenhum apoiador cadastrado ainda.')
            ->assertSee('Nenhum programa cadastrado.')
            ->assertSee('Nenhum apadrinhamento ativo.');
    }

    public function test_gestor_cadastra_uma_crianca_atendida_pelo_painel(): void
    {
        $response = $this->post('/criancas/salvar', [
            'nome' => 'Criança Cadastrada',
            'data_nascimento' => '2016-03-05',
            'historico' => 'Acompanhamento neuropedagógico semanal.',
            'status' => 'disponivel',
        ]);

        $response->assertRedirect('/')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('criancas', [
            'nome' => 'Criança Cadastrada',
            'status' => 'disponivel',
            'imagem_perfil' => 'perfil_padrao.jpg',
        ]);
    }

    public function test_crianca_cadastrada_aparece_no_painel_e_na_api(): void
    {
        $this->post('/criancas/salvar', [
            'nome' => 'Criança Visível',
            'data_nascimento' => '2016-03-05',
            'status' => 'apadrinhada',
        ]);

        $this->get('/')->assertOk()->assertSee('Criança Visível');
        $this->getJson('/api/criancas')->assertJsonPath('total', 1);
    }

    public function test_cadastro_de_crianca_exige_nome_nascimento_e_status_valido(): void
    {
        $this->post('/criancas/salvar', [
            'nome' => '',
            'data_nascimento' => '',
            'status' => 'indisponivel',
        ])->assertSessionHasErrors(['nome', 'data_nascimento', 'status']);

        $this->assertDatabaseCount('criancas', 0);
    }

    public function test_cadastro_de_crianca_recusa_data_de_nascimento_invalida(): void
    {
        $this->post('/criancas/salvar', [
            'nome' => 'Data invalida',
            'data_nascimento' => '14/05/2016',
            'status' => 'disponivel',
        ])->assertSessionHasErrors('data_nascimento');

        $this->assertDatabaseCount('criancas', 0);
    }

    public function test_rota_de_seed_e_disparada_pelo_painel_e_popula_o_banco(): void
    {
        $this->post('/seed-dados')
            ->assertRedirect('/')
            ->assertSessionHas('success');

        $this->assertGreaterThan(0, Apoiador::count());
        $this->assertGreaterThan(0, Crianca::count());
    }

    public function test_rodar_o_seed_duas_vezes_nao_duplica_registros(): void
    {
        $this->post('/seed-dados');
        $apoiadores = Apoiador::count();
        $criancas = Crianca::count();

        $this->post('/seed-dados');

        $this->assertSame($apoiadores, Apoiador::count());
        $this->assertSame($criancas, Crianca::count());
    }

    public function test_seeder_com_dados_reais_da_ong_e_idempotente(): void
    {
        Artisan::call('db:seed', ['--class' => 'OngDadosSeeder', '--force' => true]);
        $primeira = [Apoiador::count(), Crianca::count()];

        Artisan::call('db:seed', ['--class' => 'OngDadosSeeder', '--force' => true]);

        $this->assertGreaterThan(0, $primeira[0]);
        $this->assertGreaterThan(0, $primeira[1]);
        $this->assertSame($primeira, [Apoiador::count(), Crianca::count()]);
    }

    public function test_seeder_com_dados_reais_cria_uma_administradora_na_tabela_de_apoiadores(): void
    {
        Artisan::call('db:seed', ['--class' => 'OngDadosSeeder', '--force' => true]);

        $this->assertSame(1, Apoiador::whereRaw('tipo_usuario = ?', ['admin'])->count());
    }

    public function test_painel_continua_funcional_apos_um_cadastro_invalido(): void
    {
        $this->post('/criancas/salvar', [])->assertSessionHasErrors();

        $this->get('/')->assertOk();
    }

    public function test_rotas_de_administracao_do_painel_hoje_respondem_sem_autenticacao(): void
    {
        $this->assertGuest('apoiador');

        $this->post('/criancas/salvar', [
            'nome' => 'Criança sem login',
            'data_nascimento' => '2016-03-05',
            'status' => 'disponivel',
        ])->assertRedirect('/');

        $this->post('/seed-dados')->assertRedirect('/');

        $this->assertSame(1, Crianca::where('nome', 'Criança sem login')->count());
    }
}
