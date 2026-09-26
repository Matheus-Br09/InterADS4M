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
            'nome_completo' => 'Apoiante Demonstracao',
            'email' => 'apoiante@exemplo.com',
            'cpf' => '111.222.333-87',
            'celular' => '(81) 98888-7777',
            'senha' => 'Senha123',
            'senha_confirmation' => 'Senha123',
            'cidade' => 'Recife',
            'estado' => 'PE',
        ]);

        $response->assertRedirect(route('minha-conta'));
        $this->assertAuthenticated('apoiador');
        $this->assertDatabaseHas('apoiadores', [
            'nome_completo' => 'Apoiante Demonstracao',
            'email' => 'apoiante@exemplo.com',
            'cpf' => '11122233387',
            'cidade' => 'Recife',
        ]);
    }

    public function test_senha_do_apoio_e_gravada_criptografada(): void
    {
        $this->post('/cadastro', [
            'nome_completo' => 'Apoiante Demonstracao',
            'email' => 'apoiante@exemplo.com',
            'cpf' => '111.222.333-87',
            'senha' => 'Senha123',
            'senha_confirmation' => 'Senha123',
        ]);

        $apoiador = Apoiador::where('email', 'apoiante@exemplo.com')->firstOrFail();

        $this->assertNotSame('Senha123', $apoiador->senha);
        $this->assertTrue(Hash::check('Senha123', $apoiador->senha));
    }

    /*
     * A data da última rotação precisa nascer junto com a conta: se o cadastro
     * deixasse a coluna vazia, toda conta nova nasceria na lista de pendências
     * de `apoiadores:listar --rotacionar` sem ter senha nenhuma para trocar.
     */
    public function test_o_cadastro_registra_a_data_da_senha(): void
    {
        $this->post('/cadastro', [
            'nome_completo' => 'Apoiante Demonstracao',
            'email' => 'apoiante@exemplo.com',
            'cpf' => '555.666.777-10',
            'senha' => 'Senha123',
            'senha_confirmation' => 'Senha123',
        ]);

        $apoiador = Apoiador::where('email', 'apoiante@exemplo.com')->firstOrFail();

        $this->assertNotNull($apoiador->senha_alterada_em);
        $this->assertTrue($apoiador->senha_alterada_em->isToday());
    }

    public function test_cadastro_publico_nao_permite_se_registrar_como_administrador(): void
    {
        $this->post('/cadastro', [
            'nome_completo' => 'Falsa Administradora',
            'email' => 'falsa@exemplo.com',
            'cpf' => '555.666.777-10',
            'senha' => 'Senha123',
            'senha_confirmation' => 'Senha123',
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

    public function test_recusa_cadastro_com_senha_curta(): void
    {
        $this->post('/cadastro', $this->dadosDeCadastro(['senha' => 'Ab1', 'senha_confirmation' => 'Ab1']))
            ->assertSessionHasErrors('senha');

        $this->assertDatabaseCount('apoiadores', 0);
    }

    /*
    | O tamanho sozinho não segura: "12345678" tem 8 caracteres e cai em
    | segundos num ataque de dicionário. A regra exige variedade de classes.
    */
    public function test_recusa_cadastro_com_senha_fraca_mesmo_atingindo_o_tamanho_minimo(): void
    {
        $this->post('/cadastro', $this->dadosDeCadastro(['senha' => '12345678', 'senha_confirmation' => '12345678']))
            ->assertSessionHasErrors('senha');

        $this->assertDatabaseCount('apoiadores', 0);
    }

    public function test_a_senha_fraca_devolve_mensagem_que_explica_o_que_falta(): void
    {
        $resposta = $this->from('/cadastro')->post('/cadastro', $this->dadosDeCadastro([
            'senha' => 'senhasenha',
            'senha_confirmation' => 'senhasenha',
        ]));

        $resposta->assertSessionHasErrors('senha');
        $resposta->assertSessionHasErrors([
            'senha' => 'A senha precisa ter pelo menos 8 caracteres, com maiúscula, minúscula e número.',
        ]);
    }

    public function test_aceita_senha_forte_no_cadastro(): void
    {
        $this->post('/cadastro', $this->dadosDeCadastro([
            'senha' => 'Apadrinha1',
            'senha_confirmation' => 'Apadrinha1',
        ]))->assertRedirect('/minha-conta');

        $this->assertTrue(Hash::check('Apadrinha1', Apoiador::first()->senha));
    }

    public function test_recusa_cadastro_quando_a_confirmacao_de_senha_nao_confere(): void
    {
        $this->post('/cadastro', [
            'nome_completo' => 'Apoiante',
            'email' => 'apoiante@exemplo.com',
            'cpf' => '111.222.333-87',
            'senha' => 'Senha123',
            'senha_confirmation' => 'Outra123',
        ])->assertSessionHasErrors('senha');

        $this->assertDatabaseCount('apoiadores', 0);
    }

    public function test_recusa_cadastro_com_e_mail_ja_utilizado(): void
    {
        $this->criarApoiador(['email' => 'apoiante@exemplo.com']);

        $this->post('/cadastro', [
            'nome_completo' => 'Outro Apoiante',
            'email' => 'apoiante@exemplo.com',
            'cpf' => '999.888.777-05',
            'senha' => 'Senha123',
            'senha_confirmation' => 'Senha123',
        ])->assertSessionHasErrors('email');

        $this->assertSame(1, Apoiador::where('email', 'apoiante@exemplo.com')->count());
    }

    public function test_recusa_cadastro_com_cpf_ja_utilizado(): void
    {
        $this->criarApoiador(['cpf' => '111.222.333-87']);

        $this->post('/cadastro', [
            'nome_completo' => 'Apoiante',
            'email' => 'apoiante@exemplo.com',
            'cpf' => '111.222.333-87',
            'senha' => 'Senha123',
            'senha_confirmation' => 'Senha123',
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

    public function test_cadastro_recusa_cpf_invalido_com_mensagem_clara(): void
    {
        $this->post('/cadastro', [
            'nome_completo' => 'Apoiante',
            'email' => 'apoiante@exemplo.com',
            'cpf' => '111.222.333-44',
            'senha' => 'Senha123',
            'senha_confirmation' => 'Senha123',
        ])->assertSessionHasErrors('cpf');

        $this->assertDatabaseCount('apoiadores', 0);
    }

    public function test_cadastro_recusa_cpf_com_digitos_repetidos(): void
    {
        $this->post('/cadastro', [
            'nome_completo' => 'Apoiante',
            'email' => 'apoiante@exemplo.com',
            'cpf' => '111.111.111-11',
            'senha' => 'Senha123',
            'senha_confirmation' => 'Senha123',
        ])->assertSessionHasErrors('cpf');

        $this->assertDatabaseCount('apoiadores', 0);
    }

    public function test_cadastro_aceita_cpf_sem_mascara_e_guarda_so_digitos(): void
    {
        $this->post('/cadastro', [
            'nome_completo' => 'Apoiante',
            'email' => 'apoiante@exemplo.com',
            'cpf' => '12345678908',
            'senha' => 'Senha123',
            'senha_confirmation' => 'Senha123',
        ])->assertRedirect(route('minha-conta'));

        $this->assertDatabaseHas('apoiadores', [
            'email' => 'apoiante@exemplo.com',
            'cpf' => '12345678908',
        ]);
    }

    public function test_cpf_ja_cadastrado_e_recusado_mesmo_com_mascara_diferente(): void
    {
        $this->criarApoiador(['email' => 'primeiro@exemplo.com', 'cpf' => '12345678908']);

        $this->post('/cadastro', [
            'nome_completo' => 'Segundo Apoiante',
            'email' => 'segundo@exemplo.com',
            'cpf' => '123.456.789-08',
            'senha' => 'Senha123',
            'senha_confirmation' => 'Senha123',
        ])->assertSessionHasErrors('cpf');

        $this->assertDatabaseCount('apoiadores', 1);
    }

    /*
    | Cada teste roda em banco novo (RefreshDatabase), então o e-mail pode
    | repetir entre testes; dentro de um mesmo teste não, porque aí o erro seria
    | de e-mail em vez do campo que está sob exame. O CPF precisa ser válido de
    | verdade, senão a validação de dígito verificador reclama antes.
    */
    private function dadosDeCadastro(array $extra = []): array
    {
        static::$sequencia++;

        return $extra + [
            'nome_completo' => 'Apoiante Demonstracao',
            'email' => 'apoio'.static::$sequencia.'@exemplo.com',
            'cpf' => '111.222.333-87',
            'senha' => 'Senha123',
            'senha_confirmation' => 'Senha123',
        ];
    }
}
