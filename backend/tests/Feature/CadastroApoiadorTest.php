<?php

namespace Tests\Feature;

use App\Models\Apoiador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

class CadastroApoiadorTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_novo_apoio_se_cadastra_e_entra_no_painel_automaticamente(): void
    {
        $response = $this->post('/cadastro', [
            'nome_completo' => 'Joao da Silva',
            'email' => 'joao@exemplo.com',
            'cpf' => '111.222.333-44',
            'celular' => '(81) 98888-7777',
            'senha' => 'senha123',
            'senha_confirmation' => 'senha123',
            'cidade' => 'Recife',
            'estado' => 'PE',
        ]);

        $response->assertRedirect(route('minha-conta'));
        $this->assertAuthenticated('apoiador');
        $this->assertDatabaseHas('apoiadores', [
            'nome_completo' => 'Joao da Silva',
            'email' => 'joao@exemplo.com',
            'cpf' => '111.222.333-44',
            'cidade' => 'Recife',
        ]);
    }

    public function test_senha_do_apoio_e_gravada_criptografada(): void
    {
        $this->post('/cadastro', [
            'nome_completo' => 'Joao da Silva',
            'email' => 'joao@exemplo.com',
            'cpf' => '111.222.333-44',
            'senha' => 'senha123',
            'senha_confirmation' => 'senha123',
        ]);

        $apoiador = Apoiador::where('email', 'joao@exemplo.com')->firstOrFail();

        $this->assertNotSame('senha123', $apoiador->senha);
        $this->assertTrue(Hash::check('senha123', $apoiador->senha));
    }

    public function test_cadastro_publico_nao_permite_se_registrar_como_administrador(): void
    {
        $this->post('/cadastro', [
            'nome_completo' => 'Falsa Administradora',
            'email' => 'falsa@exemplo.com',
            'cpf' => '555.666.777-88',
            'senha' => 'senha123',
            'senha_confirmation' => 'senha123',
            'tipo_usuario' => 'admin',
        ]);

        $apoiador = Apoiador::where('email', 'falsa@exemplo.com')->firstOrFail();

        $this->assertSame('apoiador', $apoiador->getRawOriginal('tipo_usuario'));
    }

    public function test_recusa_cadastro_sem_informacoes_obrigatorias(): void
    {
        $this->post('/cadastro', [])
            ->assertSessionHasErrors(['nome_completo', 'email', 'cpf', 'senha']);

        $this->assertGuest('apoiador');
        $this->assertDatabaseCount('apoiadores', 0);
    }

    public function test_recusa_cadastro_com_senha_menor_que_seis_caracteres(): void
    {
        $this->post('/cadastro', [
            'nome_completo' => 'Joao',
            'email' => 'joao@exemplo.com',
            'cpf' => '111.222.333-44',
            'senha' => '12345',
            'senha_confirmation' => '12345',
        ])->assertSessionHasErrors('senha');

        $this->assertDatabaseCount('apoiadores', 0);
    }

    public function test_recusa_cadastro_quando_a_confirmacao_de_senha_nao_confere(): void
    {
        $this->post('/cadastro', [
            'nome_completo' => 'Joao',
            'email' => 'joao@exemplo.com',
            'cpf' => '111.222.333-44',
            'senha' => 'senha123',
            'senha_confirmation' => 'outra999',
        ])->assertSessionHasErrors('senha');

        $this->assertDatabaseCount('apoiadores', 0);
    }

    public function test_recusa_cadastro_com_e_mail_ja_utilizado(): void
    {
        $this->criarApoiador(['email' => 'joao@exemplo.com']);

        $this->post('/cadastro', [
            'nome_completo' => 'Outro Joao',
            'email' => 'joao@exemplo.com',
            'cpf' => '999.888.777-66',
            'senha' => 'senha123',
            'senha_confirmation' => 'senha123',
        ])->assertSessionHasErrors('email');

        $this->assertSame(1, Apoiador::where('email', 'joao@exemplo.com')->count());
    }

    public function test_recusa_cadastro_com_cpf_ja_utilizado(): void
    {
        $this->criarApoiador(['cpf' => '111.222.333-44']);

        $this->post('/cadastro', [
            'nome_completo' => 'Joao',
            'email' => 'joao@exemplo.com',
            'cpf' => '111.222.333-44',
            'senha' => 'senha123',
            'senha_confirmation' => 'senha123',
        ])->assertSessionHasErrors('cpf');

        $this->assertDatabaseCount('apoiadores', 1);
    }

    public function test_telas_de_cadastro_e_login_ficam_acessiveis_para_visitantes(): void
    {
        $this->get('/cadastro')
            ->assertOk()
            ->assertSee('Cadastro de Apoiador');

        $this->get('/entrar')
            ->assertOk()
            ->assertSee('Entrar no Sistema');
    }
}
