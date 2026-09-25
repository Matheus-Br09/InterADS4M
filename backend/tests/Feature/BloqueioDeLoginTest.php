<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

/*
| O limite de 10/min por IP não segura um ataque contra uma conta: a ONG
| inteira sai pelo mesmo IP e um robô distribuído nem passa por ele. Estes
| testes travam a conta, não o IP, e travam também a resposta que entrega
| informação sobre quais e-mails existem.
|
| As mensagens são conferidas na tela depois do redirect (followingRedirects)
| em vez de dentro do array da sessão: o que interessa é o que o apoiador lê.
*/
class BloqueioDeLoginTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_cinco_erros_bloqueiam_a_conta_mesmo_com_a_senha_certa(): void
    {
        $apoiador = $this->criarApoiador();

        for ($i = 1; $i <= 5; $i++) {
            $this->from('/entrar')->post('/entrar', $this->credenciaisDoApoiador($apoiador, 'senha-errada'))
                ->assertRedirect('/entrar')
                ->assertSessionHasErrors('email');
        }

        $this->from('/entrar')->post('/entrar', $this->credenciaisDoApoiador($apoiador, 'senha123'))
            ->assertRedirect('/entrar')
            ->assertSessionHasErrors('email');

        $this->assertGuest('apoiador');
    }

    public function test_a_tela_diz_que_a_conta_esta_bloqueada_e_por_quanto_tempo(): void
    {
        $apoiador = $this->criarApoiador();

        $this->errarLogin($apoiador, 5);

        $resposta = $this->followingRedirects()
            ->from('/entrar')
            ->post('/entrar', $this->credenciaisDoApoiador($apoiador, 'senha123'));

        $resposta->assertOk();
        $resposta->assertSee('Muitas tentativas de acesso');
        $resposta->assertSee('Tente novamente em');
        $this->assertMatchesRegularExpression(
            '/Tente novamente em \d+ segundos/',
            strip_tags($resposta->getContent()),
            'A tela precisa dizer em quantos segundos a conta volta a funcionar.',
        );
    }

    public function test_a_tela_nao_diz_mais_que_a_senha_esta_errada_quando_o_problema_e_o_bloqueio(): void
    {
        $apoiador = $this->criarApoiador();

        $this->errarLogin($apoiador, 5);

        $resposta = $this->followingRedirects()
            ->from('/entrar')
            ->post('/entrar', $this->credenciaisDoApoiador($apoiador, 'senha-errada'));

        $resposta->assertSee('Muitas tentativas de acesso');
        $resposta->assertDontSee('não correspondem aos nossos registros');
    }

    public function test_o_bloqueio_e_por_conta_e_nao_derruba_o_login_dos_outros(): void
    {
        $alvo = $this->criarApoiador();
        $outro = $this->criarApoiador(['email' => 'outra@exemplo.com']);

        $this->errarLogin($alvo, 5);

        $this->post('/entrar', $this->credenciaisDoApoiador($outro))
            ->assertRedirect('/minha-conta');

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
            $this->from('/entrar')->post('/entrar', ['email' => 'ninguem@exemplo.com', 'senha' => 'senha-errada']);
        }

        $resposta = $this->followingRedirects()
            ->from('/entrar')
            ->post('/entrar', ['email' => 'ninguem@exemplo.com', 'senha' => 'senha123']);

        $resposta->assertSee('Muitas tentativas de acesso');
        $resposta->assertDontSee('não correspondem aos nossos registros');
    }

    public function test_um_login_bem_sucedido_zeta_o_contador_da_conta(): void
    {
        $apoiador = $this->criarApoiador();

        $this->errarLogin($apoiador, 4);

        $this->post('/entrar', $this->credenciaisDoApoiador($apoiador))
            ->assertRedirect('/minha-conta');

        // Com o contador zerado, a conta aguenta mais 4 erros: se o login
        // correto não limpasse o histórico, a 5a tentativa errada já barraria.
        $this->errarLogin($apoiador, 4);

        $resposta = $this->followingRedirects()
            ->from('/entrar')
            ->post('/entrar', $this->credenciaisDoApoiador($apoiador, 'senha-errada'));

        $resposta->assertSee('não correspondem aos nossos registros');
    }

    public function test_o_e_mail_digitado_volta_no_formulario_para_nao_ter_de_digitar_de_novo(): void
    {
        $apoiador = $this->criarApoiador();

        $this->from('/entrar')->post('/entrar', $this->credenciaisDoApoiador($apoiador, 'senha-errada'))
            ->assertRedirect('/entrar')
            ->assertSessionHasInput('email', $apoiador->email);
    }

    private function errarLogin($apoiador, int $tentativas): void
    {
        for ($i = 1; $i <= $tentativas; $i++) {
            $this->from('/entrar')->post('/entrar', $this->credenciaisDoApoiador($apoiador, 'senha-errada'))
                ->assertSessionHasErrors('email');
        }
    }
}
