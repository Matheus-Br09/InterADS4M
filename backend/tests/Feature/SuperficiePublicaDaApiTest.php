<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route as Rota;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

// A lista de rotas é a fronteira de segurança do backend: o que é público
// alimenta o site, o que é da gestão mostra dado pessoal. Este teste trava
// essa lista, para nenhuma rota nova entrar no grupo errado sem querer.
class SuperficiePublicaDaApiTest extends TestCase
{
    use RefreshDatabase;

    // Rotas que respondem sem login. Qualquer rota nova aqui precisa ser
    // pensada como pública (e entrar em ApiPublicaNaoExpoeDadoPessoalTest se
    // for leitura).
    private const ROTAS_PUBLICAS = [
        'GET api/v1/config-publica',
        'GET api/v1/conteudos/{recurso}',
        'GET api/v1/conteudos/{recurso}/{id}',
        'GET api/v1/conteudos/{recurso}/{id}/arquivos/{campo}',
        'GET api/v1/csrf',
        'GET api/v1/estatisticas',
        'GET up',
        'POST api/v1/auth/entrar',
        'POST api/v1/doacoes',
        'POST api/v1/newsletter',
        'POST api/v1/voluntariado',
    ];

    // Rotas da conta logada que ficam fora do grupo da gestão: sair, trocar a
    // própria senha e ver o próprio perfil.
    private const ROTAS_DA_CONTA = [
        'GET api/v1/auth/eu',
        'POST api/v1/auth/sair',
        'PUT api/v1/auth/senha',
    ];

    public function test_a_lista_de_rotas_publicas_nao_mudou_sem_querer(): void
    {
        $publicas = array_values(array_filter(
            $this->todasAsRotas(),
            fn (string $metodoEUri) => ! in_array('auth:apoiador', $this->rota($metodoEUri)->gatherMiddleware(), true),
        ));

        $this->assertSame(self::ROTAS_PUBLICAS, $publicas);
    }

    public function test_nenhuma_rota_publica_exige_login_ou_gestao(): void
    {
        foreach (self::ROTAS_PUBLICAS as $metodoEUri) {
            $middleware = $this->rota($metodoEUri)->gatherMiddleware();

            $this->assertNotContains('auth:apoiador', $middleware, "{$metodoEUri} exige login.");
            $this->assertNotContains('gestor', $middleware, "{$metodoEUri} exige gestao.");
        }
    }

    public function test_toda_rota_logada_fora_da_conta_exige_gestao(): void
    {
        $logadas = array_diff($this->todasAsRotas(), self::ROTAS_PUBLICAS, self::ROTAS_DA_CONTA);

        $this->assertNotEmpty($logadas);

        foreach ($logadas as $metodoEUri) {
            $middleware = $this->rota($metodoEUri)->gatherMiddleware();

            $this->assertStringStartsWith('api/v1/gestao/', explode(' ', $metodoEUri, 2)[1], "{$metodoEUri} esta fora do grupo da gestao.");
            $this->assertContains('auth:apoiador', $middleware, "{$metodoEUri} nao exige login.");
            $this->assertContains('gestor', $middleware, "{$metodoEUri} nao exige gestao.");
            $this->assertContains('troca-senha', $middleware, "{$metodoEUri} nao respeita a troca obrigatoria.");
        }
    }

    public function test_rotas_da_conta_exigem_login(): void
    {
        foreach (self::ROTAS_DA_CONTA as $metodoEUri) {
            $this->assertContains('auth:apoiador', $this->rota($metodoEUri)->gatherMiddleware(), "{$metodoEUri} nao exige login.");
        }
    }

    public function test_nao_sobrou_tela_blade_nem_api_antiga(): void
    {
        foreach ($this->todasAsRotas() as $metodoEUri) {
            $uri = explode(' ', $metodoEUri, 2)[1];

            $this->assertTrue(
                $uri === 'up' || str_starts_with($uri, 'api/v1/'),
                "{$metodoEUri} esta fora de /api/v1: o MVP nao tem mais telas Blade nem o routes/api.php antigo.",
            );
        }
    }

    private function todasAsRotas(): array
    {
        $achadas = [];

        foreach (Route::getRoutes() as $rota) {
            foreach ($rota->methods() as $metodo) {
                if ($metodo === 'HEAD') {
                    continue;
                }

                $achadas[] = $metodo.' '.$rota->uri();
            }
        }

        sort($achadas);

        return $achadas;
    }

    private function rota(string $metodoEUri): Rota
    {
        [$metodo, $uri] = explode(' ', $metodoEUri, 2);

        foreach (Route::getRoutes() as $rota) {
            if ($rota->uri() === $uri && in_array($metodo, $rota->methods(), true)) {
                return $rota;
            }
        }

        $this->fail("Rota {$metodoEUri} nao encontrada.");
    }
}
