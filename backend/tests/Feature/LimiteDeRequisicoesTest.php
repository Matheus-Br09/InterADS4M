<?php

namespace Tests\Feature;

use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

// Nenhuma rota que grava no banco ou testa senha pode ficar sem limite:
// sem isso, um robô spamma a newsletter ou fica tentando senha em massa.
// Estes testes travam os limites nomeados do AppServiceProvider.
//
// O teste da newsletter também pega um erro de configuração comum: dois
// `throttle:` inline na mesma rota usam a mesma chave de cache, então o
// contador é somado duas vezes e o menor limite vale pela metade. Por isso
// os limites são nomeados (a chave inclui o nome).
class LimiteDeRequisicoesTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_a_newsletter_para_de_aceitar_depois_de_cinco_envios_no_minuto(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->postJson('/api/newsletter', ['email' => "interesse{$i}@exemplo.com"])
                ->assertCreated();
        }

        $this->postJson('/api/newsletter', ['email' => 'interesse6@exemplo.com'])
            ->assertStatus(429)
            ->assertHeader('Retry-After');

        // O 6o envio não pode ter gravado nada.
        $this->assertDatabaseCount('newsletter', 5);
    }

    /*
    | E-mails malformados: a validação barra antes do bloqueio por conta, então
    | estes 10 pedidos são medidos só pelo limite de IP. Se usasse senha errada,
    | a partir do 5o o bloqueio por conta assumiria a resposta e o teste estaria
    | medindo duas coisas ao mesmo tempo.
    */
    public function test_o_login_para_de_aceitar_tentativa_depois_de_dez_por_minuto(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $this->from('/entrar')->post('/entrar', [
                'email' => 'nao-e-email',
                'senha' => 'senha-errada',
            ])->assertRedirect();
        }

        $this->from('/entrar')->post('/entrar', [
            'email' => 'nao-e-email',
            'senha' => 'senha-errada',
        ])->assertStatus(429);
    }

    public function test_o_login_ainda_deixa_entrar_dentro_do_limite(): void
    {
        $apoiador = $this->criarApoiador();

        $this->post('/entrar', ['email' => $apoiador->email, 'senha' => 'senha123'])
            ->assertRedirect('/minha-conta');

        $this->assertAuthenticatedAs($apoiador, 'apoiador');
    }

    public function test_as_apis_publicas_tem_limite_geral_e_as_de_escrita_tem_o_proprio(): void
    {
        $this->assertContem('api', $this->middlewareEfetivo('GET', '/api/criancas'));
        $this->assertContem('api', $this->middlewareEfetivo('GET', '/api/apoiadores'));
        $this->assertContem('api', $this->middlewareEfetivo('GET', '/api/noticias'));

        $this->assertContem('newsletter', $this->middlewareEfetivo('POST', '/api/newsletter'));
        $this->assertContem('cadastro', $this->middlewareEfetivo('POST', '/cadastro'));
        $this->assertContem('login', $this->middlewareEfetivo('POST', '/entrar'));
        $this->assertContem('doacao-unica', $this->middlewareEfetivo('POST', '/apoio-unico'));
    }

    private function assertContem(string $limite, array $middleware): void
    {
        $this->assertContains(
            ThrottleRequests::class.':'.$limite,
            $middleware,
            "Esperava o limite '{$limite}' e veio: ".implode(' | ', $middleware),
        );
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
