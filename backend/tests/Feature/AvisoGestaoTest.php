<?php

namespace Tests\Feature;

use App\Mail\NovaDoacao;
use App\Mail\NovoVoluntario;
use App\Models\DoacaoUnica;
use App\Models\Voluntario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AvisoGestaoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'ong.pix_chave' => '12345678000199',
            'ong.pix_nome' => 'SOS Tudo pelo Social',
            'ong.pix_cidade' => 'PAULISTA',
            'ong.email_gestao' => 'gestao@example.org',
        ]);
    }

    private function dadosVoluntario(): array
    {
        return [
            'nome_completo' => 'João Silva',
            'email' => 'joao@email.com',
            'celular' => '(11) 98765-4321',
            'area_atuacao' => 'Nutrição',
            'disponibilidade' => 'Manhã',
            'data_nascimento' => '1990-01-01',
            'aceite_privacidade' => true,
        ];
    }

    public function test_doacao_envia_aviso_para_a_gestao(): void
    {
        Mail::fake();

        $this->postJson('/api/v1/doacoes', ['valor' => '50.00', 'nome_doador' => 'Maria'])->assertCreated();

        Mail::assertSent(NovaDoacao::class, function ($m) {
            return $m->hasTo('gestao@example.org')
                && str_starts_with($m->envelope()->subject, 'Nova doação registrada — protocolo SOS');
        });
    }

    public function test_voluntario_envia_aviso_para_a_gestao(): void
    {
        Mail::fake();

        $this->postJson('/api/v1/voluntariado', $this->dadosVoluntario())->assertOk();

        Mail::assertSent(NovoVoluntario::class, function ($m) {
            return $m->hasTo('gestao@example.org')
                && $m->envelope()->subject === 'Nova inscrição de voluntário — João Silva';
        });
    }

    public function test_sem_email_configurado_nada_e_enviado(): void
    {
        Mail::fake();
        config(['ong.email_gestao' => '']);

        $this->postJson('/api/v1/doacoes', ['valor' => '50.00'])->assertCreated();
        $this->postJson('/api/v1/voluntariado', $this->dadosVoluntario())->assertOk();

        Mail::assertNothingSent();
        Mail::assertNothingQueued();
    }

    public function test_falha_no_smtp_nao_impede_o_registro(): void
    {
        Exceptions::fake();
        Mail::shouldReceive('to')->twice()->andThrow(new \RuntimeException('smtp'));

        $this->postJson('/api/v1/doacoes', ['valor' => '50.00'])->assertCreated();
        $this->assertSame(1, DoacaoUnica::count());

        $this->postJson('/api/v1/voluntariado', $this->dadosVoluntario())->assertOk();
        $this->assertSame(1, Voluntario::count());

        Exceptions::assertReported(fn (\RuntimeException $e) => $e->getMessage() === 'smtp');
    }
}
