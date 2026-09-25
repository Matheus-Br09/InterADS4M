<?php

namespace Tests\Feature;

use App\Models\Apoiador;
use App\Models\RecompensaApadrinhamento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

class ApiConteudoPublicoTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_site_precisa_expor_as_criancas_atendidas_com_total_e_idade(): void
    {
        $crianca = $this->criarCrianca([
            'nome' => 'Ben Tennyson',
            'data_nascimento' => now()->subYears(9)->toDateString(),
        ]);
        $this->criarApadrinhamento($this->criarApoiador(), $crianca);

        $response = $this->getJson('/api/criancas');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('total', 1)
            ->assertJsonPath('dados.0.nome', 'Ben Tennyson')
            ->assertJsonPath('dados.0.status', 'apadrinhada')
            ->assertJsonPath('dados.0.apadrinhada', true)
            ->assertJsonPath('dados.0.idade', 9);
    }

    public function test_site_precisa_expor_apenas_os_numeros_dos_apoiadores(): void
    {
        $apoiador = $this->criarApoiador();
        $this->criarDoacaoUnica($apoiador, ['valor' => 75.00]);
        $this->criarDoacaoMensal($apoiador, ['valor_mensal' => 40.00]);
        $this->criarVoluntario($apoiador, ['status' => 'aprovado']);

        // A lista com nome/voluntariado/doações não é pública: a API devolve só
        // os números que o site usa na transparência.
        $response = $this->getJson('/api/apoiadores');

        $response->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('total', 1)
            ->assertJsonPath('doadores_mensais', 1)
            ->assertJsonPath('doadores_unicos', 1)
            ->assertJsonPath('total_mensal', 40)
            ->assertJsonPath('total_unico', 75)
            ->assertJsonMissingPath('dados');
    }

    public function test_api_de_apoiadores_nunca_expoe_a_senha_criptografada(): void
    {
        $this->criarApoiador();

        $this->getJson('/api/apoiadores')
            ->assertOk()
            ->assertJsonMissingPath('dados.0.senha');
    }

    public function test_apis_publicas_nao_expoem_dados_pessoais_do_apoiador(): void
    {
        $apoiador = $this->criarApoiador([
            'cpf' => '12345678901',
            'celular' => '(81) 91234-5678',
            'logradouro' => 'Rua secretive 123',
            'cidade' => 'Recife',
        ]);
        $crianca = $this->criarCrianca();
        $this->criarApadrinhamento($apoiador, $crianca);

        $this->getJson('/api/apadrinhamentos')
            ->assertOk()
            ->assertJsonMissingPath('dados.0.apoiador')
            ->assertJsonMissingPath('dados.0.valor_mensal')
            ->assertJsonMissingPath('dados.0.crianca.historico')
            ->assertJsonMissingPath('dados.0.crianca.data_nascimento');

        $this->getJson('/api/criancas')
            ->assertOk()
            ->assertJsonMissingPath('dados.0.apadrinhamentos')
            ->assertJsonMissingPath('dados.0.historico')
            ->assertJsonMissingPath('dados.0.data_nascimento');
    }

    public function test_site_precisa_expor_os_programas_sociais_da_ong(): void
    {
        $this->criarPrograma(['titulo' => 'Lute como uma Mãe Atípica', 'categoria' => 'Assistência Social']);

        $this->getJson('/api/programas')
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('total', 1)
            ->assertJsonPath('dados.0.titulo', 'Lute como uma Mãe Atípica')
            ->assertJsonPath('dados.0.categoria', 'Assistência Social');
    }

    public function test_site_precisa_expor_apadrinhamentos_com_a_crianca_e_as_recompensas(): void
    {
        $apoiador = $this->criarApoiador();
        $crianca = $this->criarCrianca(['nome' => 'Bart Simpson']);
        $apadrinhamento = $this->criarApadrinhamento($apoiador, $crianca, ['valor_mensal' => 100.00]);
        RecompensaApadrinhamento::create([
            'apadrinhamento_id' => $apadrinhamento->id,
            'titulo' => 'Carta de agradecimento',
            'mensagem' => 'Obrigado pelo apoio!',
            'arquivo_midia' => 'recompensa.png',
            'data_envio' => now(),
        ]);

        $this->getJson('/api/apadrinhamentos')
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('total', 1)
            ->assertJsonPath('dados.0.status', 'ativo')
            ->assertJsonPath('dados.0.crianca.nome', 'Bart Simpson')
            ->assertJsonCount(1, 'dados.0.recompensas')
            ->assertJsonPath('dados.0.recompensas.0.arquivo_midia', 'recompensa.png');
    }

    public function test_site_precisa_expor_noticias_materiais_didaticos_e_transparencia(): void
    {
        $this->criarNoticia(['titulo' => 'Mutirao de saude']);

        $this->getJson('/api/noticias')
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('total', 1)
            ->assertJsonPath('dados.0.titulo', 'Mutirao de saude');

        $this->getJson('/api/materiais-didaticos')
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['status', 'total', 'dados']);

        $this->getJson('/api/transparencia')
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['status', 'total', 'dados']);
    }

    public function test_apis_publicas_respondem_com_lista_vazia_quando_nao_ha_registros(): void
    {
        $this->getJson('/api/criancas')
            ->assertOk()
            ->assertJsonPath('total', 0)
            ->assertJsonPath('dados', []);

        $this->getJson('/api/apoiadores')
            ->assertOk()
            ->assertJsonPath('total', 0);

        $this->getJson('/api/programas')
            ->assertOk()
            ->assertJsonPath('total', 0);

        $this->getJson('/api/apadrinhamentos')
            ->assertOk()
            ->assertJsonPath('total', 0);

        $this->getJson('/api/noticias')
            ->assertOk()
            ->assertJsonPath('total', 0);

        $this->getJson('/api/materiais-didaticos')
            ->assertOk()
            ->assertJsonPath('total', 0);

        $this->getJson('/api/transparencia')
            ->assertOk()
            ->assertJsonPath('total', 0);
    }

    public function test_apis_publicas_de_conteudo_nao_exigem_autenticacao(): void
    {
        $this->assertGuest('apoiador');

        $this->getJson('/api/criancas')->assertOk();
        $this->getJson('/api/apoiadores')->assertOk();
    }

    public function test_rota_de_leitura_de_voluntarios_ainda_nao_existe(): void
    {
        $apoiador = $this->criarApoiador();
        $this->criarVoluntario($apoiador);

        $this->getJson('/api/voluntarios')->assertNotFound();
    }

    public function test_total_da_api_bate_com_a_quantidade_real_de_registros(): void
    {
        $apoiadores = Apoiador::count();
        $this->criarCrianca();
        $this->criarCrianca();
        $this->criarPrograma();
        $this->criarNoticia();

        $this->getJson('/api/criancas')->assertJsonPath('total', 2);
        $this->getJson('/api/programas')->assertJsonPath('total', 1);
        $this->getJson('/api/noticias')->assertJsonPath('total', 1);
        $this->getJson('/api/apoiadores')->assertJsonPath('total', $apoiadores);
    }
}
