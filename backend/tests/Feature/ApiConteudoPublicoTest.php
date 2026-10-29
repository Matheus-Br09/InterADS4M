<?php

namespace Tests\Feature;

use App\Models\Apoiador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

// O que o site público lê do backend: os números da transparência
// (GET /api/v1/estatisticas) e os conteúdos publicados (GET /api/v1/conteudos/*).
class ApiConteudoPublicoTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_site_precisa_expor_apenas_os_numeros_dos_apoiadores(): void
    {
        $apoiador = $this->criarApoiador();
        $this->criarDoacaoUnica($apoiador, ['valor' => 75.00]);
        $this->criarDoacaoMensal($apoiador, ['valor_mensal' => 40.00]);
        $this->criarVoluntario($apoiador, ['status' => 'aprovado']);

        // A lista com nome/voluntariado/doações não é pública: a API devolve só
        // os números que o site usa na transparência.
        $this->getJson('/api/v1/estatisticas')
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('total', 1)
            ->assertJsonPath('doadores_mensais', 1)
            ->assertJsonPath('doadores_unicos', 1)
            ->assertJsonPath('total_mensal', 40)
            ->assertJsonPath('total_unico', 75)
            ->assertJsonMissingPath('dados');
    }

    public function test_site_precisa_expor_os_programas_sociais_da_ong(): void
    {
        $this->criarPrograma(['titulo' => 'Lute como uma Mãe Atípica', 'categoria' => 'Assistência Social']);

        $this->getJson('/api/v1/conteudos/programas')
            ->assertOk()
            ->assertJsonPath('paginacao.total', 1)
            ->assertJsonPath('dados.0.titulo', 'Lute como uma Mãe Atípica')
            ->assertJsonPath('dados.0.categoria', 'Assistência Social');
    }

    public function test_site_precisa_expor_noticias_materiais_didaticos_e_transparencia(): void
    {
        $this->criarNoticia(['titulo' => 'Mutirao de saude']);

        $this->getJson('/api/v1/conteudos/noticias')
            ->assertOk()
            ->assertJsonPath('paginacao.total', 1)
            ->assertJsonPath('dados.0.titulo', 'Mutirao de saude');

        $this->getJson('/api/v1/conteudos/materiais')
            ->assertOk()
            ->assertJsonStructure(['dados', 'paginacao']);

        $this->getJson('/api/v1/conteudos/transparencia')
            ->assertOk()
            ->assertJsonStructure(['dados', 'paginacao']);
    }

    public function test_apis_publicas_respondem_com_lista_vazia_quando_nao_ha_registros(): void
    {
        $this->getJson('/api/v1/estatisticas')
            ->assertOk()
            ->assertJsonPath('total', 0);

        foreach (['programas', 'noticias', 'materiais', 'transparencia'] as $recurso) {
            $this->getJson("/api/v1/conteudos/{$recurso}")
                ->assertOk()
                ->assertJsonPath('paginacao.total', 0)
                ->assertJsonPath('dados', []);
        }
    }

    public function test_apis_publicas_de_conteudo_nao_exigem_autenticacao(): void
    {
        $this->assertGuest('apoiador');

        $this->getJson('/api/v1/estatisticas')->assertOk();
        $this->getJson('/api/v1/conteudos/noticias')->assertOk();
    }

    public function test_lista_de_voluntarios_nao_e_publica(): void
    {
        $this->criarVoluntario($this->criarApoiador());

        $this->getJson('/api/voluntarios')->assertNotFound();
        $this->getJson('/api/v1/voluntarios')->assertNotFound();
        $this->getJson('/api/v1/gestao/voluntarios')->assertUnauthorized();
    }

    public function test_total_da_api_bate_com_a_quantidade_real_de_registros(): void
    {
        $this->criarApoiador();
        $this->criarPrograma();
        $this->criarNoticia();

        $this->getJson('/api/v1/conteudos/programas')->assertJsonPath('paginacao.total', 1);
        $this->getJson('/api/v1/conteudos/noticias')->assertJsonPath('paginacao.total', 1);
        $this->getJson('/api/v1/estatisticas')->assertJsonPath('total', Apoiador::count());
    }
}
