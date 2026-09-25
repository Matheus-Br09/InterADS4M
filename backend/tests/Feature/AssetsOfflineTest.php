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

    public function test_css_compilado_tem_as_classes_usadas_nas_telas(): void
    {
        $css = $this->cssCompilado();

        // Estas classes só existem dentro dos .blade.php, então só aparecem no
        // CSS se o Tailwind varreu as telas.
        foreach (['max-w-6xl', 'bg-blue-600', 'grid-cols-1', 'md:grid-cols-2'] as $classe) {
            $this->assertStringContainsString(
                $this->seletorDaClasse($classe),
                $css,
                "A classe {$classe} das telas nao entrou no CSS compilado. Rode npm run build.",
            );
        }

        // E classe que ninguem usa nao deve entrar: o build e sob medida.
        $this->assertStringNotContainsString($this->seletorDaClasse('animate-bounce'), $css);
    }

    private function cssCompilado(): string
    {
        $manifest = public_path('build/manifest.json');

        if (! is_file($manifest)) {
            $this->markTestSkipped('Build de assets ausente: rode "npm run build" para conferir.');
        }

        // No manifesto o caminho é relativo a public/build
        $arquivo = 'build/'.ltrim(json_decode((string) file_get_contents($manifest), true)['resources/css/app.css']['file'] ?? '', '/');

        if ($arquivo === 'build/' || ! is_file(public_path($arquivo))) {
            $this->markTestSkipped('Manifesto sem o CSS do build: rode "npm run build".');
        }

        return (string) file_get_contents(public_path($arquivo));
    }

    private function seletorDaClasse(string $classe): string
    {
        // No CSS o dois-pontos do modificador vira \:
        return '.'.str_replace(':', '\\:', $classe);
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
