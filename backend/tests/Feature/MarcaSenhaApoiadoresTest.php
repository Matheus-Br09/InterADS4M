<?php

namespace Tests\Feature;

use App\Models\Apoiador;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

/**
 * A rotação de senha do histórico do git acontece no banco do cliente, e a
 * coluna `senha_alterada_em` só existe a partir de 26/09. Este comando carimba a
 * data sem trocar senha, para não obrigar quem já recebeu a senha nova a
 * receber outra.
 */
class MarcaSenhaApoiadoresTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_marca_uma_conta_sem_trocar_a_senha(): void
    {
        $apoiador = $this->criarApoiador(['email' => 'ana@ong.org.br']);
        $hashAntes = $apoiador->senha;

        Artisan::call('apoiadores:marcar-senha', ['email' => 'ana@ong.org.br']);
        $saida = Artisan::output();

        $apoiador->refresh();

        $this->assertNotNull($apoiador->senha_alterada_em);
        $this->assertSame($hashAntes, $apoiador->senha, 'a senha nao pode mudar ao carimbar a data');
        $this->assertStringContainsString('A senha desta conta nao foi tocada', $saida);
    }

    public function test_marca_todas_as_contas_sem_data(): void
    {
        $this->criarApoiador(['email' => 'ana@ong.org.br']);
        $this->criarApoiador(['email' => 'bruno@ong.org.br']);

        $codigo = Artisan::call('apoiadores:marcar-senha', ['--todos' => true]);

        $this->assertSame(0, $codigo);
        $this->assertSame(2, Apoiador::whereNotNull('senha_alterada_em')->count());
    }

    public function test_nao_sobrescreve_a_data_de_quem_ja_tem(): void
    {
        $antiga = now()->subMonth();
        $this->criarApoiador(['email' => 'ana@ong.org.br', 'senha_alterada_em' => $antiga]);
        $this->criarApoiador(['email' => 'bruno@ong.org.br']);

        Artisan::call('apoiadores:marcar-senha', ['--todos' => true]);

        // O banco guarda a data com precisão de segundo, então a comparação é
        // por minuto e segundo — comparar o objeto inteiro reprovaria por causa
        // dos microssegundos que o SQLite descarta.
        $this->assertSame(
            $antiga->format('d/m/Y H:i'),
            Apoiador::where('email', 'ana@ong.org.br')->first()->senha_alterada_em->format('d/m/Y H:i')
        );
        $this->assertNotNull(Apoiador::where('email', 'bruno@ong.org.br')->first()->senha_alterada_em);
    }

    public function test_reforcar_sobrescreve_a_data_de_todos(): void
    {
        $antiga = now()->subMonth();
        $this->criarApoiador(['email' => 'ana@ong.org.br', 'senha_alterada_em' => $antiga]);

        Artisan::call('apoiadores:marcar-senha', ['--todos' => true, '--reforcar' => true]);

        $this->assertTrue(
            Apoiador::where('email', 'ana@ong.org.br')->first()->senha_alterada_em->isToday()
        );
    }

    public function test_avisa_quando_tudo_ja_tem_data(): void
    {
        $this->criarApoiador(['email' => 'ana@ong.org.br', 'senha_alterada_em' => now()]);

        Artisan::call('apoiadores:marcar-senha', ['--todos' => true]);
        $saida = Artisan::output();

        $this->assertStringContainsString('Nenhuma conta sem data de rotacao', $saida);
    }

    public function test_email_inexistente_falha_sem_mexer_em_nada(): void
    {
        $this->criarApoiador(['email' => 'ana@ong.org.br', 'senha_alterada_em' => $antiga = now()]);

        $codigo = Artisan::call('apoiadores:marcar-senha', ['email' => 'ninguem@ong.org.br']);
        $saida = Artisan::output();

        $this->assertSame(1, $codigo);
        $this->assertStringContainsString('Nenhum apoiador encontrado', $saida);
        $this->assertSame(
            $antiga->format('d/m/Y H:i'),
            Apoiador::where('email', 'ana@ong.org.br')->first()->senha_alterada_em->format('d/m/Y H:i')
        );
    }

    public function test_senha_forte_gerada_pelo_comando_marca_a_data(): void
    {
        $apoiador = $this->criarApoiador(['email' => 'ana@ong.org.br', 'senha' => Hash::make('Antiga-Senha-9')]);
        $this->assertNull($apoiador->senha_alterada_em);

        Artisan::call('apoiador:senha', ['email' => 'ana@ong.org.br']);

        $this->assertNotNull($apoiador->fresh()->senha_alterada_em);
    }
}
