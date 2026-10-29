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
 * O estado é marcado por comando (`apoiadores:exigir-troca`) porque a troca
 * (PUT /api/v1/auth/senha) só existe depois do login — não há caminho de
 * interface para travar a conta antes da entrega.
 */
class TrocaSenhaObrigatoriaTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    private const SENHA_ENTREGUE = 'Entregue-Temporaria-7';

    public function test_login_de_conta_marcada_avisa_que_a_troca_e_obrigatoria(): void
    {
        $gestor = $this->criarGestor([
            'email' => 'ana@ong.org.br',
            'senha' => Hash::make(self::SENHA_ENTREGUE),
            'trocar_senha_obrigatorio' => true,
        ]);

        $this->postJson('/api/v1/auth/entrar', $this->credenciaisDoApoiador($gestor, self::SENHA_ENTREGUE))
            ->assertOk()
            ->assertJsonPath('dados.trocar_senha_obrigatorio', true);

        $this->assertAuthenticated('apoiador');
    }

    public function test_conta_marcada_nao_acha_o_proprio_perfil(): void
    {
        $gestor = $this->criarGestor(['trocar_senha_obrigatorio' => true]);

        $this->actingAs($gestor, 'apoiador')
            ->getJson('/api/v1/auth/eu')
            ->assertForbidden()
            ->assertJsonPath('codigo', 'troca_senha_obrigatoria');
    }

    public function test_gestor_marcado_tambem_fica_preso(): void
    {
        $gestor = $this->criarGestor(['trocar_senha_obrigatorio' => true]);

        $this->actingAs($gestor, 'apoiador')
            ->getJson('/api/v1/gestao/doacoes')
            ->assertForbidden()
            ->assertJsonPath('codigo', 'troca_senha_obrigatoria');
    }

    public function test_conta_marcada_ainda_consegue_sair(): void
    {
        $gestor = $this->criarGestor(['trocar_senha_obrigatorio' => true]);

        $this->actingAs($gestor, 'apoiador')->postJson('/api/v1/auth/sair')->assertNoContent();

        $this->assertGuest('apoiador');
    }

    /*
    * O ponto do recurso: a senha entregue deixa de valer depois da troca. Sem
    * esta checagem a "troca obrigatória" seria só um formulário a mais.
    */
    public function test_troca_valida_libera_o_acesso_e_derruba_a_senha_entregue(): void
    {
        $gestor = $this->criarGestor([
            'email' => 'ana@ong.org.br',
            'senha' => Hash::make(self::SENHA_ENTREGUE),
            'senha_alterada_em' => now(),
            'trocar_senha_obrigatorio' => true,
        ]);

        // Com a troca obrigatória, a senha atual não é pedida: a pessoa acabou
        // de entrar com a senha entregue.
        $this->actingAs($gestor, 'apoiador')->putJson('/api/v1/auth/senha', [
            'senha' => 'SenhaDela-9',
            'senha_confirmation' => 'SenhaDela-9',
        ])->assertOk()->assertJsonPath('message', 'Senha alterada com sucesso.');

        $gestor->refresh();

        $this->assertFalse($gestor->trocar_senha_obrigatorio);
        $this->assertTrue($gestor->senha_alterada_em->isToday());
        $this->assertTrue(Hash::check('SenhaDela-9', $gestor->senha));
        $this->assertFalse(Hash::check(self::SENHA_ENTREGUE, $gestor->senha));

        // Liberada mesmo: o painel responde e a senha antiga não entra mais.
        $this->actingAs($gestor->fresh(), 'apoiador')->getJson('/api/v1/gestao/doacoes')->assertOk();

        $this->postJson('/api/v1/auth/sair');
        $this->postJson('/api/v1/auth/entrar', $this->credenciaisDoApoiador($gestor->fresh(), self::SENHA_ENTREGUE))
            ->assertUnauthorized();
    }

    /*
    * Sem esta trava a exigência é decorativa: a pessoa cola de volta a senha que
    * recebeu, o formulário aceita, e a senha que passou por WhatsApp continua
    * valendo — exatamente o que o recurso existe para impedir.
    */
    public function test_recusa_a_mesma_senha_que_a_conta_ja_usa(): void
    {
        $gestor = $this->criarGestor([
            'senha' => Hash::make(self::SENHA_ENTREGUE),
            'trocar_senha_obrigatorio' => true,
        ]);

        $this->actingAs($gestor, 'apoiador')->putJson('/api/v1/auth/senha', [
            'senha' => self::SENHA_ENTREGUE,
            'senha_confirmation' => self::SENHA_ENTREGUE,
        ])->assertJsonValidationErrors('senha');

        $gestor->refresh();

        $this->assertTrue($gestor->trocar_senha_obrigatorio, 'a conta continua presa na troca');
        $this->assertTrue(Hash::check(self::SENHA_ENTREGUE, $gestor->senha));
    }

    public function test_troca_exige_senha_forte_e_confirmacao(): void
    {
        $gestor = $this->criarGestor(['trocar_senha_obrigatorio' => true]);

        $this->actingAs($gestor, 'apoiador')->putJson('/api/v1/auth/senha', [
            'senha' => 'abc123',
            'senha_confirmation' => 'abc123',
        ])->assertJsonValidationErrors('senha');

        $this->actingAs($gestor, 'apoiador')->putJson('/api/v1/auth/senha', [
            'senha' => 'SenhaBoa-9',
        ])->assertJsonValidationErrors('senha');

        $this->assertTrue($gestor->fresh()->trocar_senha_obrigatorio);
    }

    public function test_troca_exige_login(): void
    {
        $this->putJson('/api/v1/auth/senha', ['senha' => 'SenhaBoa-9', 'senha_confirmation' => 'SenhaBoa-9'])
            ->assertUnauthorized();
    }

    public function test_conta_nao_marcada_entra_direto_no_painel(): void
    {
        $gestor = $this->criarGestor(['trocar_senha_obrigatorio' => false]);

        $this->postJson('/api/v1/auth/entrar', $this->credenciaisDoApoiador($gestor))
            ->assertOk()
            ->assertJsonPath('dados.trocar_senha_obrigatorio', false);

        $this->getJson('/api/v1/gestao/doacoes')->assertOk();
    }

    public function test_troca_voluntaria_exige_a_senha_atual(): void
    {
        $gestor = $this->criarGestor(['senha' => Hash::make(self::SENHA_ENTREGUE)]);

        $this->actingAs($gestor, 'apoiador')->putJson('/api/v1/auth/senha', [
            'senha' => 'Outra-Senha-8',
            'senha_confirmation' => 'Outra-Senha-8',
        ])->assertJsonValidationErrors('senha_atual');

        $this->actingAs($gestor, 'apoiador')->putJson('/api/v1/auth/senha', [
            'senha_atual' => self::SENHA_ENTREGUE,
            'senha' => 'Outra-Senha-8',
            'senha_confirmation' => 'Outra-Senha-8',
        ])->assertOk();

        $this->assertTrue(Hash::check('Outra-Senha-8', $gestor->fresh()->senha));
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
}
