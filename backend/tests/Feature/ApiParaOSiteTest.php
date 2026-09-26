<?php

namespace Tests\Feature;

use App\Support\OrigensCors;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

// O site em inter-ong/ roda em outra origem e chama o backend por fetch.
// Por isso as rotas de API ficam em routes/api.php (sem cookie de sessão e
// sem token de CSRF) e a config/cors.php libera a origem. Estes testes
// travam esse acordo; os formulários do próprio backend continuam
// protegidos por CSRF porque são telas do Laravel.
class ApiParaOSiteTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    private const ORIGEM_LOCAL = 'http://localhost:5173';

    public function test_o_site_le_as_apis_publicas_sem_login(): void
    {
        $apoiador = $this->criarApoiador();
        $crianca = $this->criarCrianca(['nome' => 'Ben Tennyson']);
        $this->criarApadrinhamento($apoiador, $crianca);
        $this->assertGuest('apoiador');

        // Público para o site ler, mas só com o que ele mostra: a lista de
        // apoiadores com nome e contato não é pública
        // (ver ApiPublicaNaoExpoeDadoPessoalTest).
        $this->getJson('/api/apoiadores')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonMissingPath('dados');

        $this->getJson('/api/criancas')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonPath('dados.0.nome', 'Ben Tennyson')
            ->assertJsonPath('dados.0.apadrinhada', true);
    }

    public function test_leitura_da_api_vem_com_permissao_para_o_site_leer(): void
    {
        $this->getJson('/api/criancas', ['Origin' => self::ORIGEM_LOCAL])
            ->assertOk()
            ->assertHeader('Access-Control-Allow-Origin', '*');
    }

    /*
     * O 'allowed_origins' virou configuração de .env (CORS_ALLOWED_ORIGINS) para
     * o ajuste de produção não ser commit de código. Aqui vai o outro lado
     * disso, que é o comportamento do php-cors e vale registrar:
     *
     * - Com '*', a resposta volta com '*' (desenvolvimento).
     * - Com uma origem só, a resposta volta com essa origem, venha o pedido de
     *   onde vier. Quem barra é o navegador, ao comparar com a própria origem.
     * - Com duas ou mais, a resposta volta com a origem do pedido **só se** ela
     *   estiver na lista; origem que não está sai sem o header nenhum.
     *
     * Ou seja: o header 'sumir' é o sinal de bloqueio, e nunca pode ser '*'
     * depois de publicada a lista de verdade.
     */
    public function test_com_a_lista_de_origens_preenchida_o_header_para_de_ser_coringa(): void
    {
        config(['cors.allowed_origins' => OrigensCors::aPartirDe('https://ongsos.org.br,https://www.ongsos.org.br')]);

        $this->getJson('/api/criancas', ['Origin' => 'https://ongsos.org.br'])
            ->assertOk()
            ->assertHeader('Access-Control-Allow-Origin', 'https://ongsos.org.br');

        $this->getJson('/api/criancas', ['Origin' => 'https://www.ongsos.org.br'])
            ->assertOk()
            ->assertHeader('Access-Control-Allow-Origin', 'https://www.ongsos.org.br');
    }

    public function test_origem_que_nao_esta_na_lista_nao_recebe_permissao(): void
    {
        config(['cors.allowed_origins' => OrigensCors::aPartirDe('https://ongsos.org.br,https://www.ongsos.org.br')]);

        $this->getJson('/api/criancas', ['Origin' => 'https://site-que-nao-e-a-ong.example'])
            ->assertOk()
            ->assertHeaderMissing('Access-Control-Allow-Origin');
    }

    public function test_origem_que_esta_na_lista_com_espaco_ainda_passa(): void
    {
        // Regressão do erro clássico de .env: "https://a.com, https://b.com".
        // Sem o trim, a segunda origem não bate e o navegador acusa CORS.
        config(['cors.allowed_origins' => OrigensCors::aPartirDe(' https://ongsos.org.br , https://www.ongsos.org.br ')]);

        $this->getJson('/api/criancas', ['Origin' => 'https://www.ongsos.org.br'])
            ->assertOk()
            ->assertHeader('Access-Control-Allow-Origin', 'https://www.ongsos.org.br');
    }

    public function test_o_site_pode_enviar_a_newsletter_de_outra_origem(): void
    {
        $this->postJson('/api/newsletter', ['email' => 'interessada@exemplo.com'], [
            'Origin' => self::ORIGEM_LOCAL,
            'Accept' => 'application/json',
        ])
            ->assertCreated()
            ->assertHeader('Access-Control-Allow-Origin', '*');

        $this->assertDatabaseHas('newsletter', ['email' => 'interessada@exemplo.com']);
    }

    public function test_o_site_nao_precisa_de_token_de_sessao_para_postar(): void
    {
        $this->assertNotContains(
            PreventRequestForgery::class,
            $this->middlewareEfetivo('POST', '/api/newsletter'),
            'POST /api/newsletter nao pode exigir token de sessao: o site nao tem como enviar.',
        );
    }

    public function test_os_formularios_do_backend_continuam_com_protecao_de_csrf(): void
    {
        foreach (['POST /cadastro', 'POST /entrar', 'POST /apoio-unico', 'POST /criancas/salvar'] as $alvo) {
            [$metodo, $uri] = explode(' ', $alvo);

            $this->assertContains(
                PreventRequestForgery::class,
                $this->middlewareEfetivo($metodo, $uri),
                "{$alvo} precisa continuar protegido por CSRF.",
            );
        }
    }

    // Middleware efetivo da rota, com os grupos ('web', 'api') abertos.
    private function middlewareEfetivo(string $metodo, string $uri): array
    {
        $grupos = app(HttpKernel::class)->getMiddlewareGroups();
        $rota = Route::getRoutes()->match(Request::create($uri, $metodo));

        return $this->abrirGrupos(app(Router::class)->gatherRouteMiddleware($rota), $grupos);
    }

    private function abrirGrupos(array $middleware, array $grupos, int $nivel = 0): array
    {
        $resolvido = [];

        foreach ($middleware as $item) {
            if ($nivel < 5 && isset($grupos[$item])) {
                $resolvido = [...$resolvido, ...$this->abrirGrupos($grupos[$item], $grupos, $nivel + 1)];

                continue;
            }

            $resolvido[] = $item;
        }

        return $resolvido;
    }
}
