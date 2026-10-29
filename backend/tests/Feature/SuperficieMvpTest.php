<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

// O MVP só tem duas portas: o site público (que não pede login) e o painel da
// gestão. Cadastro e login de apoiador, apadrinhamento e o painel Blade antigo
// saíram; este teste garante que eles não voltam sem querer e que só a gestão
// consegue abrir sessão.
class SuperficieMvpTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public static function rotasRemovidas(): array
    {
        return [
            'lista de criancas' => ['GET', '/api/criancas'],
            'lista de apoiadores' => ['GET', '/api/apoiadores'],
            'lista de apadrinhamentos' => ['GET', '/api/apadrinhamentos'],
            'lista de programas antiga' => ['GET', '/api/programas'],
            'lista de noticias antiga' => ['GET', '/api/noticias'],
            'lista de materiais antiga' => ['GET', '/api/materiais-didaticos'],
            'lista de transparencia antiga' => ['GET', '/api/transparencia'],
            'newsletter antiga' => ['POST', '/api/newsletter'],
            'cadastro pela api' => ['POST', '/api/v1/auth/cadastro'],
            'editar conta' => ['PATCH', '/api/v1/conta'],
            'historico de doacoes' => ['GET', '/api/v1/conta/doacoes'],
            'historico de mensalidades' => ['GET', '/api/v1/conta/mensalidades'],
            'historico de apadrinhamentos' => ['GET', '/api/v1/conta/apadrinhamentos'],
            'doacao mensal' => ['POST', '/api/v1/mensalidades'],
            'apadrinhar' => ['POST', '/api/v1/apadrinhamentos'],
            'cancelar apoio' => ['DELETE', '/api/v1/conta/doacoes/1'],
            'painel blade' => ['GET', '/'],
            'gerar dados de teste' => ['POST', '/seed-dados'],
            'cadastrar crianca no painel' => ['POST', '/criancas/salvar'],
            'tela de cadastro' => ['GET', '/cadastro'],
            'envio do cadastro' => ['POST', '/cadastro'],
            'minha conta' => ['GET', '/minha-conta'],
            'apoio unico' => ['GET', '/apoio-unico'],
            'tela de login blade' => ['GET', '/entrar'],
            'login blade' => ['POST', '/entrar'],
            'tela de troca de senha blade' => ['GET', '/minha-conta/senha'],
        ];
    }

    #[DataProvider('rotasRemovidas')]
    public function test_rotas_removidas_respondem_404(string $metodo, string $uri): void
    {
        // Mesmo com a gestão logada: a rota não pode existir, não basta negar.
        $this->actingAsGestor();

        $this->json($metodo, $uri)->assertNotFound();
    }

    public function test_apoiador_comum_nao_consegue_entrar(): void
    {
        $apoiador = $this->criarApoiador();

        $this->postJson('/api/v1/auth/entrar', $this->credenciaisDoApoiador($apoiador))
            ->assertForbidden()
            ->assertExactJson(['message' => 'Acesso restrito à gestão da ONG.']);

        $this->assertGuest('apoiador');
    }

    public function test_gestor_entra(): void
    {
        $gestor = $this->criarGestor();

        $this->postJson('/api/v1/auth/entrar', $this->credenciaisDoApoiador($gestor))
            ->assertOk()
            ->assertJsonPath('dados.email', $gestor->email);

        $this->assertAuthenticatedAs($gestor, 'apoiador');
    }

    public function test_nenhuma_rota_publica_devolve_dado_pessoal(): void
    {
        $voluntario = $this->criarApoiador(['nome_completo' => 'Valentina Voluntaria', 'email' => 'valentina@exemplo.org']);
        $this->criarVoluntario($voluntario, ['status' => 'aprovado']);
        $this->criarDoacaoUnica(null, ['nome_doador' => 'Dora Doadora', 'email_doador' => 'dora@exemplo.org']);
        $this->criarCrianca(['nome' => 'Cecilia Crianca']);
        $this->criarNoticia();
        $this->criarPrograma();

        $rotasPublicas = [
            '/api/v1/csrf',
            '/api/v1/config-publica',
            '/api/v1/estatisticas',
            '/api/v1/conteudos/noticias',
            '/api/v1/conteudos/programas',
            '/api/v1/conteudos/materiais',
            '/api/v1/conteudos/transparencia',
        ];

        foreach ($rotasPublicas as $rota) {
            $corpo = $this->getJson($rota)->assertOk()->getContent();

            foreach (['Valentina Voluntaria', 'valentina@exemplo.org', 'Dora Doadora', 'dora@exemplo.org', 'Cecilia Crianca'] as $dadoPessoal) {
                $this->assertStringNotContainsString($dadoPessoal, $corpo, "{$rota} devolveu um dado pessoal: {$dadoPessoal}.");
            }
        }
    }
}
