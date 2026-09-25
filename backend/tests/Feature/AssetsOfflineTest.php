<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Vite;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

// O sistema roda offline, num pen drive. Nenhuma tela pode depender de CDN
// externo: o visual vem do build local (npm run build), nunca da internet.
class AssetsOfflineTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    private const CDNS_EXTERNOS = [
        'cdn.tailwindcss.com',
        'fonts.googleapis.com',
        'fonts.gstatic.com',
        'unpkg.com',
        'cdn.jsdelivr.net',
        'cdnjs.cloudflare.com',
    ];

    public static function telasPublicadas(): array
    {
        return [
            'entrar' => ['/entrar', 'visitante'],
            'cadastro' => ['/cadastro', 'visitante'],
            'apoio unico' => ['/apoio-unico', 'apoiador'],
            'minha conta' => ['/minha-conta', 'apoiador'],
            'painel do gestor' => ['/', 'gestor'],
        ];
    }

    #[DataProvider('telasPublicadas')]
    public function test_nenhuma_tela_carrega_arquivo_de_cdn_externo(string $rota, string $quem): void
    {
        $this->actingComo($quem);

        $html = (string) $this->get($rota)->assertOk()->getContent();

        foreach (self::CDNS_EXTERNOS as $cdn) {
            $this->assertStringNotContainsString($cdn, $html, "A tela {$rota} ainda carrega {$cdn}.");
        }
    }

    #[DataProvider('telasPublicadas')]
    public function test_telas_usam_o_build_local_quando_ele_existe(string $rota, string $quem): void
    {
        if (! is_file(public_path('build/manifest.json'))) {
            $this->markTestSkipped('Build de assets ausente: rode "npm run build" para conferir.');
        }

        $this->app->forgetInstance(Vite::class);
        $this->actingComo($quem);

        $html = (string) $this->get($rota)->assertOk()->getContent();

        $this->assertMatchesRegularExpression('#/build/assets/app-[^"\']+\.css#', $html, "A tela {$rota} nao carregou o CSS do build.");
    }

    private function actingComo(string $quem): void
    {
        match ($quem) {
            'gestor' => $this->actingAsGestor(),
            'apoiador' => $this->actingAs($this->criarApoiador(), 'apoiador'),
            default => $this->get('/sair'),
        };
    }
}
