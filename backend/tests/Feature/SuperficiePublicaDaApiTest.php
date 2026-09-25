<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route as Rota;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

// A lista de rotas é a fronteira de segurança do backend: o que é público
// alimenta o site, o que é do painel mostra dado pessoal. Este teste trava
// essa lista, para nenhuma rota nova entrar no grupo errado sem querer.
class SuperficiePublicaDaApiTest extends TestCase
{
    use RefreshDatabase;

    private const APIS_PUBLICAS = [
        'GET api/apadrinhamentos',
        'GET api/apoiadores',
        'GET api/criancas',
        'GET api/materiais-didaticos',
        'GET api/noticias',
        'GET api/programas',
        'GET api/transparencia',
        'POST api/newsletter',
    ];

    public function test_a_lista_de_apis_publicas_nao_mudou_sem_querer(): void
    {
        $this->assertSame(self::APIS_PUBLICAS, $this->rotasDeApi());
    }

    public function test_nenhuma_api_publica_exige_login_ou_gestao(): void
    {
        foreach ($this->rotasDeApi() as $metodoEUri) {
            $rota = $this->rota($metodoEUri);

            $this->assertNotContains('auth:apoiador', $rota->gatherMiddleware(), "{$metodoEUri} exige login.");
            $this->assertNotContains('gestor', $rota->gatherMiddleware(), "{$metodoEUri} exige gestao.");
        }
    }

    public function test_rotas_do_painel_que_mostram_dado_pessoal_exigem_gestao(): void
    {
        foreach (['GET /', 'POST /seed-dados', 'POST /criancas/salvar'] as $metodoEUri) {
            $this->assertContains('gestor', $this->rota($metodoEUri)->gatherMiddleware(), "{$metodoEUri} nao exige gestao.");
        }
    }

    public function test_as_telas_do_apoiador_exigem_o_guard_deles(): void
    {
        foreach (['GET /minha-conta', 'GET /apoio-unico', 'POST /apoio-unico'] as $metodoEUri) {
            $this->assertContains('auth:apoiador', $this->rota($metodoEUri)->gatherMiddleware(), "{$metodoEUri} nao exige login.");
        }
    }

    public function test_as_telas_publicas_nao_exigem_nenhum_login(): void
    {
        foreach (['GET /entrar', 'POST /entrar', 'GET /cadastro', 'POST /cadastro'] as $metodoEUri) {
            $middleware = $this->rota($metodoEUri)->gatherMiddleware();

            $this->assertNotContains('auth:apoiador', $middleware, "{$metodoEUri} exige login.");
            $this->assertNotContains('gestor', $middleware, "{$metodoEUri} exige gestao.");
        }
    }

    private function rotasDeApi(): array
    {
        $achadas = [];

        foreach (Route::getRoutes() as $rota) {
            foreach ($rota->methods() as $metodo) {
                if ($metodo === 'HEAD') {
                    continue;
                }

                $uri = $rota->uri();

                if (str_starts_with($uri, 'api/')) {
                    $achadas[] = $metodo.' '.$uri;
                }
            }
        }

        sort($achadas);

        return $achadas;
    }

    private function rota(string $metodoEUri): Rota
    {
        [$metodo, $uri] = explode(' ', $metodoEUri, 2);

        return Route::getRoutes()->match(Request::create($uri, $metodo));
    }
}
