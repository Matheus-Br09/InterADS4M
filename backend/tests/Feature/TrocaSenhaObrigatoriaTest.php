<?php

namespace Tests\Feature;

use App\Models\Apoiador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

/**
 * A senha que a ONG entrega na rotação dos hashes expostos no histórico do git
 * é a mesma que trafega por WhatsApp/e-mail. A troca obrigatória faz essa senha
 * morrer no primeiro acesso da pessoa.
 *
 * O estado é marcado por comando (`apoiadores:exigir-troca`) porque a tela de
 * troca só existe depois do login — não há caminho de interface para travar a
 * conta antes da entrega.
 */
class TrocaSenhaObrigatoriaTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    private const SENHA_ENTREGUE = 'Entregue-Temporaria-7';

    public function test_login_de_conta_marcada_cai_na_troca_de_senha(): void
    {
        $apoiador = $this->criarApoiador([
            'email' => 'ana@ong.org.br',
            'senha' => Hash::make(self::SENHA_ENTREGUE),
            'trocar_senha_obrigatorio' => true,
        ]);

        $response = $this->post('/entrar', $this->credenciaisDoApoiador($apoiador, self::SENHA_ENTREGUE));

        $response->assertRedirect(route('senha.edit'));
        $this->assertAuthenticated('apoiador');
    }

    public function test_conta_marcada_nao_acha_a_minha_conta(): void
    {
        $apoiador = $this->criarApoiador(['trocar_senha_obrigatorio' => true]);

        $response = $this->actingAs($apoiador, 'apoiador')->get('/minha-conta');

        $response->assertRedirect(route('senha.edit'));
        $response->assertSessionHas('aviso');
    }

    public function test_conta_marcada_nao_acha_a_doacao_unica(): void
    {
        $apoiador = $this->criarApoiador(['trocar_senha_obrigatorio' => true]);

        $this->actingAs($apoiador, 'apoiador')
            ->get('/apoio-unico')
            ->assertRedirect(route('senha.edit'));
    }

    public function test_gestor_marcado_tambem_fica_preso(): void
    {
        $gestor = $this->criarGestor(['trocar_senha_obrigatorio' => true]);

        $this->actingAs($gestor, 'apoiador')
            ->get('/')
            ->assertRedirect(route('senha.edit'));
    }

    /*
    * O ponto do recurso: a senha entregue deixa de valer depois da troca. Sem
    * esta checagem a "troca obrigatória" seria só um formulário a mais.
    */
    public function test_troca_valida_libera_o_acesso_e_derruba_a_senha_entregue(): void
    {
        $apoiador = $this->criarApoiador([
            'email' => 'ana@ong.org.br',
            'senha' => Hash::make(self::SENHA_ENTREGUE),
            'senha_alterada_em' => now(),
            'trocar_senha_obrigatorio' => true,
        ]);

        $response = $this->actingAs($apoiador, 'apoiador')->post('/minha-conta/senha', [
            'senha' => 'SenhaDela-9',
            'senha_confirmation' => 'SenhaDela-9',
        ]);

        $response->assertRedirect(route('minha-conta'));
        $response->assertSessionHas('success');

        $apoiador->refresh();

        $this->assertFalse($apoiador->trocar_senha_obrigatorio);
        $this->assertTrue($apoiador->senha_alterada_em->isToday());
        $this->assertTrue(Hash::check('SenhaDela-9', $apoiador->senha));
        $this->assertFalse(Hash::check(self::SENHA_ENTREGUE, $apoiador->senha));

        // Liberada mesmo: o painel responde e a senha antiga não entra mais.
        $this->actingAs($apoiador->fresh(), 'apoiador')->get('/minha-conta')->assertOk();

        $this->post('/entrar', $this->credenciaisDoApoiador($apoiador->fresh(), self::SENHA_ENTREGUE))
            ->assertSessionHasErrors('email');
    }

    /*
    * Sem esta trava a exigência é decorativa: a pessoa cola de volta a senha que
    * recebeu, o formulário aceita, e a senha que passou por WhatsApp continua
    * valendo — exatamente o que o recurso existe para impedir.
    */
    public function test_recusa_a_mesma_senha_que_a_conta_ja_usa(): void
    {
        $apoiador = $this->criarApoiador([
            'senha' => Hash::make(self::SENHA_ENTREGUE),
            'trocar_senha_obrigatorio' => true,
        ]);

        $response = $this->actingAs($apoiador, 'apoiador')->post('/minha-conta/senha', [
            'senha' => self::SENHA_ENTREGUE,
            'senha_confirmation' => self::SENHA_ENTREGUE,
        ]);

        $response->assertSessionHasErrors('senha');

        $apoiador->refresh();

        $this->assertTrue($apoiador->trocar_senha_obrigatorio, 'a conta continua presa na troca');
        $this->assertTrue(Hash::check(self::SENHA_ENTREGUE, $apoiador->senha));
    }

    public function test_troca_exige_senha_forte_e_confirmacao(): void
    {
        $apoiador = $this->criarApoiador(['trocar_senha_obrigatorio' => true]);

        $fraca = $this->actingAs($apoiador, 'apoiador')->post('/minha-conta/senha', [
            'senha' => 'abc123',
            'senha_confirmation' => 'abc123',
        ]);
        $fraca->assertSessionHasErrors('senha');

        $semConfirmacao = $this->actingAs($apoiador, 'apoiador')->post('/minha-conta/senha', [
            'senha' => 'SenhaBoa-9',
        ]);
        $semConfirmacao->assertSessionHasErrors('senha');

        $this->assertTrue($apoiador->fresh()->trocar_senha_obrigatorio);
    }

    public function test_troca_exige_login(): void
    {
        $this->get('/minha-conta/senha')->assertRedirect(route('login'));
    }

    public function test_conta_nao_marcada_entra_direto_no_painel(): void
    {
        $apoiador = $this->criarApoiador(['trocar_senha_obrigatorio' => false]);

        $this->actingAs($apoiador, 'apoiador')->get('/minha-conta')->assertOk();

        $this->post('/entrar', $this->credenciaisDoApoiador($apoiador))
            ->assertRedirect(route('minha-conta'));
    }

    public function test_troca_voluntaria_libera_o_painel_tambem(): void
    {
        $apoiador = $this->criarApoiador(['senha' => Hash::make(self::SENHA_ENTREGUE)]);

        $this->actingAs($apoiador, 'apoiador')->get(route('senha.edit'))->assertOk();

        $this->actingAs($apoiador, 'apoiador')->post('/minha-conta/senha', [
            'senha' => 'Outra-Senha-8',
            'senha_confirmation' => 'Outra-Senha-8',
        ])->assertRedirect(route('minha-conta'));

        $this->assertTrue(Hash::check('Outra-Senha-8', $apoiador->fresh()->senha));
    }

    public function test_comando_marca_uma_conta(): void
    {
        $this->criarApoiador(['email' => 'ana@ong.org.br']);

        $codigo = Artisan::call('apoiadores:exigir-troca', ['email' => 'ana@ong.org.br']);
        $saida = Artisan::output();

        $this->assertSame(0, $codigo);
        $this->assertTrue(Apoiador::where('email', 'ana@ong.org.br')->first()->trocar_senha_obrigatorio);
        $this->assertStringContainsString('criar uma senha nova', $saida);
    }

    /*
    * Travar a conta da gestão é a forma mais rápida de a ONG ficar sem acesso ao
    * próprio painel, então --todos ignora o admin — e avisa que ignorou.
    */
    public function test_comando_todos_nao_marca_a_gestao(): void
    {
        $this->criarApoiador(['email' => 'ana@ong.org.br']);
        $this->criarApoiador(['email' => 'bruno@ong.org.br']);
        $this->criarGestor(['email' => 'gestao@ong.org.br']);

        $codigo = Artisan::call('apoiadores:exigir-troca', ['--todos' => true]);
        $saida = Artisan::output();

        $this->assertSame(0, $codigo);
        $this->assertSame(2, Apoiador::where('trocar_senha_obrigatorio', true)->count());
        $this->assertFalse(Apoiador::where('email', 'gestao@ong.org.br')->first()->trocar_senha_obrigatorio);
        $this->assertStringNotContainsString('gestao@ong.org.br', $saida);
        $this->assertStringContainsString('A conta da gestao nao foi tocada', $saida);
    }

    public function test_comando_desfazer_limpa_a_exigencia(): void
    {
        $this->criarApoiador(['email' => 'ana@ong.org.br', 'trocar_senha_obrigatorio' => true]);
        $this->criarApoiador(['email' => 'bruno@ong.org.br', 'trocar_senha_obrigatorio' => true]);

        $codigo = Artisan::call('apoiadores:exigir-troca', ['--todos' => true, '--desfazer' => true]);

        $this->assertSame(0, $codigo);
        $this->assertSame(0, Apoiador::where('trocar_senha_obrigatorio', true)->count());
    }

    public function test_comando_email_inexistente_falha_sem_mexer_em_nada(): void
    {
        $this->criarApoiador(['email' => 'ana@ong.org.br']);

        $codigo = Artisan::call('apoiadores:exigir-troca', ['email' => 'ninguem@ong.org.br']);
        $saida = Artisan::output();

        $this->assertSame(1, $codigo);
        $this->assertStringContainsString('Nenhum apoiador encontrado', $saida);
        $this->assertFalse(Apoiador::where('email', 'ana@ong.org.br')->first()->trocar_senha_obrigatorio);
    }

    public function test_a_lista_mostra_a_exigencia_de_troca(): void
    {
        // Senha que não é a pública do seed: senão a conta entra na lista de
        // pendências de rotação por outro motivo e o teste não prova nada.
        $this->criarApoiador(['email' => 'ana@ong.org.br', 'senha' => Hash::make('SenhaQualquer-9'), 'senha_alterada_em' => now()]);
        $this->criarApoiador([
            'email' => 'bruno@ong.org.br',
            'senha' => Hash::make('SenhaQualquer-9'),
            'senha_alterada_em' => now(),
            'trocar_senha_obrigatorio' => true,
        ]);

        Artisan::call('apoiadores:listar');
        $saida = Artisan::output();

        $this->assertStringContainsString('troca', $saida);
        $this->assertStringContainsString('obrigatoria', $saida);

        // Conta que já rotacionou e não tem exigência não entra como pendência
        // de rotação: são coisas diferentes.
        Artisan::call('apoiadores:listar', ['--rotacionar' => true]);
        $filtrada = Artisan::output();
        $this->assertStringNotContainsString('bruno@ong.org.br', $filtrada);
    }

    /*
    * O cadastro público não pode se marcar sozinho: se conseguisse, qualquer
    * pessoa poderia travar a própria conta — e o estado viraria mais um campo
    * que o `create` do controller não deveria aceitar.
    */
    public function test_cadastro_publico_nao_marca_a_conta_sozinho(): void
    {
        $this->post('/cadastro', [
            'nome_completo' => 'Apoiante Demonstracao',
            'email' => 'apoiante@exemplo.com',
            'cpf' => '111.222.333-87',
            'senha' => 'Senha123',
            'senha_confirmation' => 'Senha123',
            'trocar_senha_obrigatorio' => 1,
        ]);

        $apoiador = Apoiador::where('email', 'apoiante@exemplo.com')->firstOrFail();

        $this->assertFalse($apoiador->trocar_senha_obrigatorio);
    }
}
