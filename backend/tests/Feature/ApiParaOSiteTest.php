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

// O site em inter-ong/ chama o backend por fetch em /api/v1 (o routes/api.php
// antigo, sem sessão e sem CSRF, saiu do MVP). A config/cors.php libera a
// origem para leitura, e toda rota que grava continua exigindo o token de CSRF
// que o site busca em GET /api/v1/csrf. Estes testes travam esse acordo.
class ApiParaOSiteTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    private const ORIGEM_LOCAL = 'http://localhost:5173';

    public function test_o_site_le_as_apis_publicas_sem_login(): void
    {
        $this->criarApoiador();
        $this->criarNoticia(['titulo' => 'Mutirão de saúde']);
        $this->assertGuest('apoiador');

        // Público para o site ler, mas só com o que ele mostra: os números da
        // transparência, nunca a lista de apoiadores
        // (ver ApiPublicaNaoExpoeDadoPessoalTest).
        $this->getJson('/api/v1/estatisticas')
            ->assertOk()
            ->assertJsonPath('total', 1)
            ->assertJsonMissingPath('dados');

        $this->getJson('/api/v1/conteudos/noticias')
            ->assertOk()
            ->assertJsonPath('dados.0.titulo', 'Mutirão de saúde');
    }

    public function test_leitura_da_api_vem_com_permissao_para_o_site_leer(): void
    {
        $this->getJson('/api/v1/estatisticas', ['Origin' => self::ORIGEM_LOCAL])
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

        $this->getJson('/api/v1/estatisticas', ['Origin' => 'https://ongsos.org.br'])
            ->assertOk()
            ->assertHeader('Access-Control-Allow-Origin', 'https://ongsos.org.br');

        $this->getJson('/api/v1/estatisticas', ['Origin' => 'https://www.ongsos.org.br'])
            ->assertOk()
            ->assertHeader('Access-Control-Allow-Origin', 'https://www.ongsos.org.br');
    }

    public function test_origem_que_nao_esta_na_lista_nao_recebe_permissao(): void
    {
        config(['cors.allowed_origins' => OrigensCors::aPartirDe('https://ongsos.org.br,https://www.ongsos.org.br')]);

        $this->getJson('/api/v1/estatisticas', ['Origin' => 'https://site-que-nao-e-a-ong.example'])
            ->assertOk()
            ->assertHeaderMissing('Access-Control-Allow-Origin');
    }

    public function test_origem_que_esta_na_lista_com_espaco_ainda_passa(): void
    {
        // Regressão do erro clássico de .env: "https://a.com, https://b.com".
        // Sem o trim, a segunda origem não bate e o navegador acusa CORS.
        config(['cors.allowed_origins' => OrigensCors::aPartirDe(' https://ongsos.org.br , https://www.ongsos.org.br ')]);

        $this->getJson('/api/v1/estatisticas', ['Origin' => 'https://www.ongsos.org.br'])
            ->assertOk()
            ->assertHeader('Access-Control-Allow-Origin', 'https://www.ongsos.org.br');
    }

    public function test_o_site_pode_enviar_a_newsletter_de_outra_origem(): void
    {
        $this->postJson('/api/v1/newsletter', ['email' => 'interessada@exemplo.com', 'aceite_privacidade' => true], [
            'Origin' => self::ORIGEM_LOCAL,
        ])
            ->assertCreated()
            ->assertHeader('Access-Control-Allow-Origin', '*');

        $this->assertDatabaseHas('newsletter', ['email' => 'interessada@exemplo.com']);
    }

    public function test_toda_rota_que_grava_exige_token_de_csrf(): void
    {
        $alvos = [
            'POST /api/v1/auth/entrar',
            'POST /api/v1/auth/sair',
            'PUT /api/v1/auth/senha',
            'POST /api/v1/newsletter',
            'POST /api/v1/doacoes',
            'POST /api/v1/voluntariado',
            'POST /api/v1/gestao/conteudos/noticias',
            'PATCH /api/v1/gestao/voluntarios/1',
            'DELETE /api/v1/gestao/doacoes/1',
        ];

        foreach ($alvos as $alvo) {
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
