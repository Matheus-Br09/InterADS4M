<?php

namespace Tests\Feature;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

class ExpirarDoacoesTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_expira_pendentes_com_mais_de_7_dias(): void
    {
        $velha = $this->criarDoacaoUnica(null, ['status' => 'pendente', 'protocolo' => 'EXP-1', 'data_doacao' => now()->subDays(8)]);
        $recente = $this->criarDoacaoUnica(null, ['status' => 'pendente', 'protocolo' => 'EXP-2', 'data_doacao' => now()->subDays(6)]);
        $concluida = $this->criarDoacaoUnica(null, ['status' => 'concluido', 'protocolo' => 'EXP-3', 'data_doacao' => now()->subDays(30)]);

        $this->artisan('doacoes:expirar')
            ->expectsOutput('1 doação(ões) expirada(s).')
            ->assertSuccessful();

        $this->assertSame('expirado', $velha->refresh()->status);
        $this->assertSame('pendente', $recente->refresh()->status);
        $this->assertSame('concluido', $concluida->refresh()->status);
    }

    public function test_comando_esta_agendado_diariamente(): void
    {
        $evento = collect(app(Schedule::class)->events())
            ->first(fn ($e) => str_contains($e->command, 'doacoes:expirar'));

        $this->assertNotNull($evento);
        $this->assertSame('0 0 * * *', $evento->expression);
    }
}
