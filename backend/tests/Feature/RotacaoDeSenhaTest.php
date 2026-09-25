<?php

namespace Tests\Feature;

use App\Models\Apoiador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

/**
 * Rotação de credenciais: os hashes de senha de 12 apoiadores ficaram
 * expostos no histórico do git (ver README, "Credenciais expostas"). Estes
 * testes travam as duas ferramentas da rotação - gestor:senha e
 * apoiador:senha - e a regra de que nenhum seed carrega hash escrito à mão.
 */
class RotacaoDeSenhaTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_gestor_senha_agera_uma_senha_forte_e_ela_entra_no_painel(): void
    {
        $apoiador = $this->criarApoiador();
        $codigo = Artisan::call('gestor:senha', ['email' => $apoiador->email]);
        $saida = Artisan::output();

        $this->assertSame(0, $codigo);
        $this->assertMatchesRegularExpression('/Senha gerada: (\S+)/', $saida);

        preg_match('/Senha gerada: (\S+)/', $saida, $achado);
        $senha = $achado[1];

        // 16 caracteres de um alfabeto sem caractere ambíguo (i/I/l/L/o/O/0/1).
        $this->assertSame(16, mb_strlen($senha));
        $this->assertDoesNotMatchRegularExpression('/[iIlL1oO0]/', $senha);
        $this->assertTrue(Hash::check($senha, $apoiador->refresh()->senha));

        $this->post('/entrar', ['email' => $apoiador->email, 'senha' => $senha])
            ->assertRedirect('/minha-conta');
    }

    public function test_a_senha_gerada_troca_a_senha_antiga(): void
    {
        $apoiador = $this->criarApoiador();
        Artisan::call('gestor:senha', ['email' => $apoiador->email]);
        preg_match('/Senha gerada: (\S+)/', Artisan::output(), $achado);

        $this->post('/entrar', ['email' => $apoiador->email, 'senha' => 'senha123'])
            ->assertSessionHasErrors('email');
        $this->assertGuest('apoiador');

        $this->post('/entrar', ['email' => $apoiador->email, 'senha' => $achado[1]])
            ->assertRedirect('/minha-conta');
    }

    public function test_apoiador_senha_redefine_sem_promover_a_gestor(): void
    {
        $apoiador = $this->criarApoiador(['tipo_usuario' => 'apoiador']);
        $codigo = Artisan::call('apoiador:senha', ['email' => $apoiador->email]);
        $saida = Artisan::output();

        $this->assertSame(0, $codigo);
        $this->assertStringContainsString('papel mantido: apoiador', $saida);
        $this->assertSame('apoiador', $apoiador->refresh()->tipo_usuario);

        preg_match('/Senha gerada: (\S+)/', $saida, $achado);
        $this->post('/entrar', ['email' => $apoiador->email, 'senha' => $achado[1]])
            ->assertRedirect('/minha-conta');
    }

    public function test_apoiador_senha_derruba_a_sessao_que_ainda_valia(): void
    {
        $apoiador = $this->criarApoiador();

        // SESSION_DRIVER=array no phpunit.xml não grava linha em `sessions`, então
        // a sessão é criada aqui no mesmo formato do DatabaseSessionHandler do
        // Laravel (payload em base64) - em produção o SESSION_DRIVER é database.
        $chave = 'login_apoiador_'.$apoiador->id;
        $payload = base64_encode((string) json_encode([
            '_token' => 'token-de-teste',
            $chave => $apoiador->id,
        ]));
        $outro = base64_encode((string) json_encode([
            '_token' => 'token-de-teste',
            'login_apoiador_999' => 999,
        ]));

        DB::table('sessions')->insert([
            ['id' => 'sessao-do-apoiador', 'user_id' => null, 'ip_address' => '127.0.0.1', 'user_agent' => 'teste', 'payload' => $payload, 'last_activity' => time()],
            ['id' => 'sessao-de-outro', 'user_id' => null, 'ip_address' => '127.0.0.1', 'user_agent' => 'teste', 'payload' => $outro, 'last_activity' => time()],
        ]);

        $codigo = Artisan::call('apoiador:senha', ['email' => $apoiador->email]);

        $this->assertSame(0, $codigo);
        $this->assertStringContainsString('sessão(ões) aberta(s) com a senha antiga foram encerradas', Artisan::output());
        $this->assertDatabaseMissing('sessions', ['id' => 'sessao-do-apoiador']);
        $this->assertDatabaseHas('sessions', ['id' => 'sessao-de-outro']);
    }

    public function test_apoiador_senha_recusa_senha_fraca_e_email_desconhecido(): void
    {
        $apoiador = $this->criarApoiador();
        $antes = $apoiador->senha;

        $this->assertSame(1, Artisan::call('apoiador:senha', [
            'email' => $apoiador->email,
            'senha' => 'abc123',
        ]));
        $this->assertStringContainsString('8 caracteres', Artisan::output());
        $this->assertSame(1, Artisan::call('apoiador:senha', [
            'email' => 'ninguem@teste.com',
            'senha' => 'SenhaBoa123',
        ]));
        $this->assertSame($antes, $apoiador->refresh()->senha);
    }

    public function test_o_seed_nao_deixa_nenhuma_senha_utilizavel(): void
    {
        Artisan::call('db:seed', ['--force' => true]);

        foreach (Apoiador::all() as $apoiador) {
            foreach (['senha123', '123456', 'admin', 'Gestor1234!', 'password', $apoiador->email] as $chute) {
                $this->assertFalse(
                    Hash::check($chute, $apoiador->senha),
                    "A conta {$apoiador->email} aceita a senha \"{$chute}\"."
                );
            }
        }
    }

    public function test_nenhum_seed_escreve_hash_ou_email_de_pessoa_real(): void
    {
        // Guarda de regressão: hash de senha escrito à mão no repositório
        // publica a credencial de quem for usar aquele banco.
        $arquivos = [
            database_path('seeders/OngDadosSeeder.php'),
            database_path('seeders/DatabaseSeeder.php'),
        ];

        foreach ($arquivos as $arquivo) {
            $conteudo = File::get($arquivo);

            $this->assertDoesNotMatchRegularExpression(
                '/\$2y\$/',
                $conteudo,
                basename($arquivo).' tem hash de senha escrito à mão.'
            );
            $this->assertDoesNotMatchRegularExpression(
                '/[\'"][^\'"]*@(gmail|hotmail|outlook|yahoo|sos\.org\.br)[\'"]/',
                $conteudo,
                basename($arquivo).' tem e-mail de domínio pessoal/real.'
            );
        }
    }
}
