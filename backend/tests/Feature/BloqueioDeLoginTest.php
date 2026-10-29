<?php

namespace Tests\Feature;

use App\Models\Apoiador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

/*
| O limite de 10/min por IP não segura um ataque contra uma conta: a ONG
| inteira sai pelo mesmo IP e um robô distribuído nem passa por ele. Estes
| testes travam a conta, não o IP, e travam também a resposta que entrega
| informação sobre quais e-mails existem.
|
| O login é só pela API (POST /api/v1/auth/entrar), usada pelo painel da gestão.
*/
class BloqueioDeLoginTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    private const ENTRAR = '/api/v1/auth/entrar';

    public function test_cinco_erros_bloqueiam_a_conta_mesmo_com_a_senha_certa(): void
    {
        $gestor = $this->criarGestor();

        $this->errarLogin($gestor, 5);

        $this->postJson(self::ENTRAR, $this->credenciaisDoApoiador($gestor, 'senha123'))
            ->assertStatus(429);

        $this->assertGuest('apoiador');
    }

    public function test_a_resposta_diz_que_a_conta_esta_bloqueada_e_por_quanto_tempo(): void
    {
        $gestor = $this->criarGestor();

        $this->errarLogin($gestor, 5);

        $resposta = $this->postJson(self::ENTRAR, $this->credenciaisDoApoiador($gestor, 'senha123'))
            ->assertStatus(429)
            ->assertJsonPath('message', 'Muitas tentativas de acesso. Tente novamente mais tarde.')
            ->assertHeader('Retry-After');

        $this->assertIsInt($resposta->json('tentar_em'));
        $this->assertGreaterThan(0, $resposta->json('tentar_em'));
    }

    public function test_a_resposta_nao_diz_mais_que_a_senha_esta_errada_quando_o_problema_e_o_bloqueio(): void
    {
        $gestor = $this->criarGestor();

        $this->errarLogin($gestor, 5);

        $this->postJson(self::ENTRAR, $this->credenciaisDoApoiador($gestor, 'senha-errada'))
            ->assertStatus(429)
            ->assertJsonMissing(['message' => 'E-mail ou senha inválidos.']);
    }

    public function test_o_bloqueio_e_por_conta_e_nao_derruba_o_login_dos_outros(): void
    {
        $alvo = $this->criarGestor();
        $outro = $this->criarGestor(['email' => 'outra@exemplo.com']);

        $this->errarLogin($alvo, 5);

        $this->postJson(self::ENTRAR, $this->credenciaisDoApoiador($outro))->assertOk();

        $this->assertAuthenticatedAs($outro, 'apoiador');
    }

    /*
    | A chave do bloqueio usa o e-mail digitado, não o da conta encontrada. Se
    | usasse o da conta, um e-mail inexistente nunca bateria no limite e o
    | atacante distinguiria "conta existe" de "conta não existe" pela resposta.
    */
    public function test_e_mail_inexistente_e_bloqueado_do_mesmo_jeito_para_nao_entregar_a_lista_de_quem_existe(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $this->postJson(self::ENTRAR, ['email' => 'ninguem@exemplo.com', 'senha' => 'senha-errada'])
                ->assertUnauthorized();
        }

        $this->postJson(self::ENTRAR, ['email' => 'ninguem@exemplo.com', 'senha' => 'senha123'])
            ->assertStatus(429)
            ->assertJsonPath('message', 'Muitas tentativas de acesso. Tente novamente mais tarde.');
    }

    public function test_um_login_bem_sucedido_zera_o_contador_da_conta(): void
    {
        $gestor = $this->criarGestor();

        $this->errarLogin($gestor, 4);

        $this->postJson(self::ENTRAR, $this->credenciaisDoApoiador($gestor))->assertOk();
        $this->postJson('/api/v1/auth/sair')->assertNoContent();

        // Com o contador zerado, a conta aguenta mais 4 erros: se o login
        // correto não limpasse o histórico, a 5a tentativa errada já barraria.
        $this->errarLogin($gestor, 4);

        $this->postJson(self::ENTRAR, $this->credenciaisDoApoiador($gestor, 'senha-errada'))
            ->assertUnauthorized()
            ->assertJsonPath('message', 'E-mail ou senha inválidos.');
    }

    public function test_apoiador_comum_tambem_fica_bloqueado_depois_de_errar(): void
    {
        // O bloqueio vale antes de saber quem é a conta: senão o 403 de
        // "acesso restrito" viraria um jeito de testar senhas sem limite.
        $apoiador = $this->criarApoiador();

        $this->errarLogin($apoiador, 5);

        $this->postJson(self::ENTRAR, $this->credenciaisDoApoiador($apoiador))
            ->assertStatus(429);

        $this->assertGuest('apoiador');
    }

    private function errarLogin(Apoiador $conta, int $tentativas): void
    {
        for ($i = 1; $i <= $tentativas; $i++) {
            $this->postJson(self::ENTRAR, $this->credenciaisDoApoiador($conta, 'senha-errada'))
                ->assertUnauthorized();
        }
    }
}
