<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

class GestaoGestoresTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_lista_so_os_gestores(): void
    {
        $gestor = $this->actingAsGestor();
        $this->criarApoiador();

        $this->getJson('/api/v1/gestao/gestores')
            ->assertOk()
            ->assertJsonCount(1, 'dados')
            ->assertJsonPath('dados.0.id', $gestor->id)
            ->assertJsonPath('dados.0.email', $gestor->email)
            ->assertJsonMissingPath('dados.0.senha');
    }

    public function test_gestor_cria_outro_gestor_com_senha_temporaria(): void
    {
        $this->actingAsGestor();

        $resposta = $this->postJson('/api/v1/gestao/gestores', [
            'nome_completo' => 'Maria Gestora',
            'email' => 'Maria@ONG.org.br',
        ])->assertCreated()
            ->assertJsonPath('dados.nome_completo', 'Maria Gestora')
            ->assertJsonPath('dados.email', 'maria@ong.org.br');

        $senha = $resposta->json('senha_temporaria');
        $this->assertNotEmpty($senha);

        $this->assertDatabaseHas('apoiadores', [
            'email' => 'maria@ong.org.br',
            'tipo_usuario' => 'admin',
            'trocar_senha_obrigatorio' => true,
            'cpf' => null,
        ]);
        $criado = \App\Models\Apoiador::where('email', 'maria@ong.org.br')->first();
        $this->assertTrue(Hash::check($senha, $criado->senha));
    }

    public function test_email_repetido_e_recusado(): void
    {
        $this->actingAsGestor();
        $existente = $this->criarApoiador(['email' => 'jaexiste@ong.org.br']);

        $this->postJson('/api/v1/gestao/gestores', [
            'nome_completo' => 'Outra Pessoa',
            'email' => 'JaExiste@ong.org.br',
        ])->assertUnprocessable()->assertJsonValidationErrors('email');

        $this->assertSame(1, \App\Models\Apoiador::where('id', $existente->id)->count());
    }

    public function test_gestor_desativa_outro_gestor_e_ele_perde_acesso(): void
    {
        $alvo = $this->criarGestor();
        $senhaAntiga = $alvo->senha;
        $this->actingAsGestor();

        $this->deleteJson('/api/v1/gestao/gestores/'.$alvo->id)->assertNoContent();

        $alvo->refresh();
        $this->assertSame('apoiador', $alvo->tipo_usuario);
        $this->assertNotSame($senhaAntiga, $alvo->senha);
        $this->assertFalse(Hash::check('senha123', $alvo->senha));

        Auth::guard('apoiador')->logout();
        $this->actingAs($alvo->fresh(), 'apoiador');
        $this->getJson('/api/v1/gestao/doacoes')->assertForbidden();
    }

    public function test_nao_pode_desativar_a_si_mesmo(): void
    {
        $gestor = $this->actingAsGestor();

        $this->deleteJson('/api/v1/gestao/gestores/'.$gestor->id)
            ->assertUnprocessable()
            ->assertJsonPath('mensagem', 'Você não pode desativar o seu próprio acesso.');

        $this->assertSame('admin', $gestor->fresh()->tipo_usuario);
    }

    public function test_nao_gestor_nao_acessa(): void
    {
        $this->actingAs($this->criarApoiador(), 'apoiador');

        $this->getJson('/api/v1/gestao/gestores')->assertForbidden();
        $this->postJson('/api/v1/gestao/gestores', ['nome_completo' => 'X Y', 'email' => 'x@y.com'])->assertForbidden();
    }
}
