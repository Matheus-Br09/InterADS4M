<?php

namespace Tests\Feature;

use App\Models\Apadrinhamento;
use App\Models\Apoiador;
use App\Models\RecompensaApadrinhamento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Session\DatabaseSessionHandler;
use Illuminate\Session\EncryptedStore;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

class CorrecaoAuditoriaBackendTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public static function comandosDeRotacao(): array
    {
        return [
            'apoiador json' => ['apoiador:senha', false, 'json'],
            'gestor json' => ['gestor:senha', false, 'json'],
            'apoiador criptografado' => ['apoiador:senha', true, 'json'],
            'gestor criptografado' => ['gestor:senha', true, 'json'],
            'apoiador php' => ['apoiador:senha', false, 'php'],
        ];
    }

    #[DataProvider('comandosDeRotacao')]
    public function test_rotacao_revoga_sessoes_reais_e_preserva_outros_usuarios(string $comando, bool $criptografada, string $serializacao): void
    {
        config(['session.encrypt' => $criptografada, 'session.serialization' => $serializacao]);
        $apoiador = $this->criarApoiador();
        $outro = $this->criarApoiador();
        $primeira = $this->gravarSessao($apoiador);
        $segunda = $this->gravarSessao($apoiador);
        $alheia = $this->gravarSessao($outro);
        $outroGuard = $this->gravarSessao($apoiador, 'web');

        $this->artisan($comando, ['email' => $apoiador->email, 'senha' => 'NovaSenha123'])->assertSuccessful();

        $this->assertDatabaseMissing('sessions', ['id' => $primeira]);
        $this->assertDatabaseMissing('sessions', ['id' => $segunda]);
        $this->assertDatabaseHas('sessions', ['id' => $alheia]);
        $this->assertDatabaseHas('sessions', ['id' => $outroGuard]);
        $this->assertTrue(Hash::check('NovaSenha123', $apoiador->fresh()->senha));
    }

    public function test_troca_de_senha_revoga_sessao_anterior_e_outros_dispositivos(): void
    {
        $apoiador = $this->criarGestor(['senha' => Hash::make('Anterior123')]);
        $dispositivo = $this->gravarSessao($apoiador);
        $alheia = $this->gravarSessao($this->criarApoiador());
        config(['session.driver' => 'database']);
        app('session')->forgetDrivers();
        app()->forgetInstance('session.store');
        Auth::forgetGuards();
        $this->postJson('/api/v1/auth/entrar', ['email' => $apoiador->email, 'senha' => 'Anterior123'])->assertOk();
        $anterior = session()->getId();
        $this->assertDatabaseHas('sessions', ['id' => $anterior]);

        $this->putJson('/api/v1/auth/senha', ['senha_atual' => 'Anterior123', 'senha' => 'NovaSenha123', 'senha_confirmation' => 'NovaSenha123'])
            ->assertOk();

        $this->assertDatabaseMissing('sessions', ['id' => $anterior]);
        $this->assertDatabaseMissing('sessions', ['id' => $dispositivo]);
        $this->assertDatabaseHas('sessions', ['id' => $alheia]);
        $this->assertDatabaseHas('sessions', ['id' => session()->getId()]);
        $this->assertAuthenticatedAs($apoiador, 'apoiador');
        $this->assertTrue(Hash::check('NovaSenha123', $apoiador->fresh()->senha));
    }

    public function test_senha_repetida_nao_fica_no_old_input(): void
    {
        $apoiador = $this->criarApoiador(['senha' => Hash::make('Auditoria123')]);

        $this->actingAs($apoiador, 'apoiador')->putJson('/api/v1/auth/senha', [
            'senha_atual' => 'Auditoria123', 'senha' => 'Auditoria123', 'senha_confirmation' => 'Auditoria123',
        ])->assertJsonValidationErrors('senha')->assertSessionMissing('_old_input.senha')->assertSessionMissing('_old_input.senha_confirmation');

        $this->assertTrue(Hash::check('Auditoria123', $apoiador->fresh()->senha));
    }

    public static function formulariosComSenha(): array
    {
        return ['login' => ['POST', '/api/v1/auth/entrar'], 'troca' => ['PUT', '/api/v1/auth/senha']];
    }

    #[DataProvider('formulariosComSenha')]
    public function test_erros_de_validacao_nao_guardam_senha_na_sessao(string $metodo, string $rota): void
    {
        $apoiador = $this->criarGestor();

        $this->actingAs($apoiador, 'apoiador')->json($metodo, $rota, [
            'senha' => 'x', 'senha_confirmation' => 'y',
        ])->assertUnprocessable()->assertSessionMissing('_old_input.senha')->assertSessionMissing('_old_input.senha_confirmation');
    }

    public function test_login_atualiza_hash_na_coluna_senha(): void
    {
        $apoiador = $this->criarGestor(['senha' => Hash::make('Auditoria123', ['rounds' => 4])]);
        $anterior = $apoiador->senha;
        Hash::driver()->setRounds(5);

        $this->postJson('/api/v1/auth/entrar', ['email' => $apoiador->email, 'senha' => 'Auditoria123'])->assertOk();

        $this->assertAuthenticatedAs($apoiador, 'apoiador');
        $this->assertNotSame($anterior, $apoiador->fresh()->senha);
        $this->assertTrue(Hash::check('Auditoria123', $apoiador->fresh()->senha));
        $this->assertFalse(Hash::needsRehash($apoiador->fresh()->senha));
    }

    public function test_seed_associa_recompensa_ao_apadrinhamento_correto_em_base_preenchida(): void
    {
        $anterior = $this->criarApadrinhamento($this->criarApoiador(), $this->criarCrianca());

        Artisan::call('db:seed', ['--class' => 'OngDadosSeeder', '--force' => true]);
        Artisan::call('db:seed', ['--class' => 'OngDadosSeeder', '--force' => true]);

        $apoiador = Apoiador::where('email', 'apoiador1@exemplo.org')->firstOrFail();
        $novo = Apadrinhamento::where('apoiador_id', $apoiador->id)->firstOrFail();
        $recompensa = RecompensaApadrinhamento::where('titulo', 'tá na hora de virar herói')->sole();
        $this->assertNotSame($anterior->id, $novo->id);
        $this->assertSame($novo->id, $recompensa->apadrinhamento_id);
    }

    public function test_totais_publicos_ignoram_doacoes_pendentes_e_canceladas(): void
    {
        $confirmado = $this->criarApoiador();
        $pendente = $this->criarApoiador();
        $this->criarDoacaoUnica($confirmado, ['valor' => 25, 'status' => 'concluido']);
        $this->criarDoacaoUnica($confirmado, ['valor' => 30, 'status' => 'concluido']);
        $this->criarDoacaoUnica($pendente, ['valor' => 100, 'status' => 'pendente']);
        $this->criarDoacaoUnica($pendente, ['valor' => 200, 'status' => 'cancelado']);

        $this->getJson('/api/v1/estatisticas')->assertOk()->assertJsonPath('doadores_unicos', 1)->assertJsonPath('total_unico', 55);
    }

    public static function valoresInvalidos(): array
    {
        return ['acima da coluna' => ['100000000.00'], 'fracao de centavo' => ['5.001']];
    }

    #[DataProvider('valoresInvalidos')]
    public function test_doacao_recusa_valor_incompativel_com_o_banco(string $valor): void
    {
        config(['ong.pix_chave' => '12345678000199', 'ong.pix_nome' => 'SOS Tudo pelo Social', 'ong.pix_cidade' => 'PAULISTA']);

        $this->postJson('/api/v1/doacoes', ['valor' => $valor])->assertJsonValidationErrors('valor');

        $this->assertDatabaseCount('doacoes_unicas', 0);
    }

    private function gravarSessao(Apoiador $apoiador, string $guard = 'apoiador'): string
    {
        $handler = new DatabaseSessionHandler(DB::connection(), 'sessions', 120);
        $sessao = config('session.encrypt')
            ? new EncryptedStore('auditoria', $handler, Crypt::getFacadeRoot(), null, config('session.serialization'))
            : new Store('auditoria', $handler, null, config('session.serialization'));
        $sessao->start();
        $sessao->put(Auth::guard($guard)->getName(), $apoiador->id);
        $sessao->save();

        return $sessao->getId();
    }
}
