<?php

namespace Tests\Feature;

use App\Models\Apoiador;
use App\Models\Crianca;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

// O painel Blade que disparava o seed saiu do MVP; o seed continua sendo o
// jeito de montar um banco de demonstração (php artisan db:seed).
class SeederDaOngTest extends TestCase
{
    use RefreshDatabase;

    public function test_rodar_o_seed_duas_vezes_nao_duplica_registros(): void
    {
        Artisan::call('db:seed', ['--force' => true]);
        $apoiadores = Apoiador::count();
        $criancas = Crianca::count();

        Artisan::call('db:seed', ['--force' => true]);

        $this->assertSame($apoiadores, Apoiador::count());
        $this->assertSame($criancas, Crianca::count());
    }

    public function test_seeder_com_dados_reais_da_ong_e_idempotente(): void
    {
        Artisan::call('db:seed', ['--class' => 'OngDadosSeeder', '--force' => true]);
        $primeira = [Apoiador::count(), Crianca::count()];

        Artisan::call('db:seed', ['--class' => 'OngDadosSeeder', '--force' => true]);

        $this->assertGreaterThan(0, $primeira[0]);
        $this->assertGreaterThan(0, $primeira[1]);
        $this->assertSame($primeira, [Apoiador::count(), Crianca::count()]);
    }

    public function test_seeder_com_dados_reais_cria_uma_administradora_na_tabela_de_apoiadores(): void
    {
        Artisan::call('db:seed', ['--class' => 'OngDadosSeeder', '--force' => true]);

        $this->assertSame(1, Apoiador::whereRaw('tipo_usuario = ?', ['admin'])->count());
    }

    public function test_seed_padrao_tambem_carrega_os_dados_reais_da_ong(): void
    {
        Artisan::call('db:seed', ['--force' => true]);

        $this->assertDatabaseHas('apoiadores', ['email' => 'gestor@exemplo.org', 'tipo_usuario' => 'admin']);
        $this->assertSame(1, Apoiador::whereRaw('tipo_usuario = ?', ['admin'])->count());

        $this->assertDatabaseHas('criancas', ['nome' => 'Lucas Gabriel Santos']);
    }
}
