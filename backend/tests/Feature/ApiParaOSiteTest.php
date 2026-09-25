<?php

namespace Tests\Feature;

use App\Models\Apoiador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Middleware\PreventRequestForgery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

// O site em inter-ong/ roda em outra origem e chama o backend por fetch.
// Hoje a leitura funciona, mas o navegador não consegue ler a resposta de
// outra origem e o POST da newsletter exige token de sessão. Os dois testes
// marcados como "incompleto" são o contrato que falta: quando o CORS for
// configurado, eles viram as asserções que travam o comportamento.
class ApiParaOSiteTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    private const ORIGEM_DO_SITE = 'https://ongsos.org.br';

    public function test_o_site_le_as_apis_publicas_sem_login(): void
    {
        $this->criarApoiador();
        $this->assertGuest('apoiador');

        $this->getJson('/api/apoiadores')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('dados.0.nome_completo', Apoiador::first()->nome_completo);
    }

    public function test_leitura_da_api_e_respeitada_pelo_navegador(): void
    {
        $this->getJson('/api/criancas', ['Origin' => self::ORIGEM_DO_SITE])->assertOk();
    }

    public function test_origem_externa_recebe_permissao_cors(): void
    {
        $this->markTestIncomplete(
            'CORS ainda nao configurado: falta config/cors.php (o site em outra origem '
            .'nao consegue ler a resposta). Defina as origens permitidas e troque este teste por assertHeader().'
        );
    }

    public function test_post_da_newsletter_da_para_o_site_sem_token_csrf(): void
    {
        $rota = Route::getRoutes()->match(Request::create('/api/newsletter', 'POST'));

        $this->assertNotContains(
            PreventRequestForgery::class,
            $rota->gatherMiddleware(),
            'POST /api/newsletter nao pode exigir token de sessao: o site nao tem como enviar. '
            .'Tire a rota do grupo web ou use $middleware->validateCsrfTokens(except: ["api/*"]).',
        );
    }
}
