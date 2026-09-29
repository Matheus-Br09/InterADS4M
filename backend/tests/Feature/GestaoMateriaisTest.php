<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

class GestaoMateriaisTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_gestor_publica_pdf_sem_capa_ou_descricao_e_categoria_aparece_no_site(): void
    {
        Storage::fake('public');
        $gestor = $this->criarApoiador(['tipo_usuario' => 'admin']);
        $pdf = UploadedFile::fake()->create('guia.pdf', 50, 'application/pdf');

        $resposta = $this->actingAs($gestor, 'apoiador')->postJson('/api/v1/gestao/conteudos/materiais', [
            'titulo' => 'Guia de atividades', 'categoria' => 'Atividades', 'arquivo_pdf' => $pdf,
        ]);

        $resposta->assertOk()->assertJsonPath('dados.categoria', 'Atividades');
        $this->assertDatabaseHas('materiais_didaticos', ['titulo' => 'Guia de atividades', 'categoria' => 'Atividades', 'imagem_capa' => null, 'descricao' => null]);
        Storage::disk('public')->assertExists($resposta->json('dados.arquivo_pdf'));
        $this->app['auth']->guard('apoiador')->logout();
        $this->getJson('/api/v1/conteudos/materiais')->assertOk()->assertJsonPath('dados.0.titulo', 'Guia de atividades')->assertJsonPath('dados.0.categoria', 'Atividades');
        $this->get('/api/v1/conteudos/materiais/'.$resposta->json('dados.id').'/arquivos/arquivo_pdf')->assertOk()->assertDownload();
    }

    public function test_gestor_publica_pdf_json_sem_arquivo_temporario(): void
    {
        Storage::fake('public');
        $gestor = $this->criarApoiador(['tipo_usuario' => 'admin']);
        $conteudo = base64_encode("%PDF-1.4\n1 0 obj\n<<>>\nendobj\n");

        $resposta = $this->actingAs($gestor, 'apoiador')->postJson('/api/v1/gestao/conteudos/materiais', [
            'titulo' => 'Guia JSON', 'categoria' => 'Educação', 'arquivo_pdf_base64' => "data:application/pdf;base64,{$conteudo}", 'arquivo_pdf_nome' => 'guia.pdf',
        ]);

        $resposta->assertOk()->assertJsonPath('dados.titulo', 'Guia JSON');
        Storage::disk('public')->assertExists($resposta->json('dados.arquivo_pdf'));
    }

    public function test_visitante_nao_publica_e_recebe_401(): void
    {
        Storage::fake('public');

        $this->postJson('/api/v1/gestao/conteudos/materiais', [])->assertUnauthorized();

        $this->assertDatabaseCount('materiais_didaticos', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_apoiador_nao_publica_e_recebe_403(): void
    {
        Storage::fake('public');
        $apoiador = $this->criarApoiador(['tipo_usuario' => 'apoiador']);

        $this->actingAs($apoiador, 'apoiador')->postJson('/api/v1/gestao/conteudos/materiais', [])->assertForbidden();

        $this->assertDatabaseCount('materiais_didaticos', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_gestor_com_troca_obrigatoria_recebe_403(): void
    {
        Storage::fake('public');
        $gestor = $this->criarApoiador(['tipo_usuario' => 'admin', 'trocar_senha_obrigatorio' => true]);

        $this->actingAs($gestor, 'apoiador')->postJson('/api/v1/gestao/conteudos/materiais', [])->assertForbidden()->assertJsonPath('codigo', 'troca_senha_obrigatoria');

        $this->assertDatabaseCount('materiais_didaticos', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_campos_obrigatorios_ausentes_recebem_422(): void
    {
        Storage::fake('public');
        $gestor = $this->criarApoiador(['tipo_usuario' => 'admin']);

        $this->actingAs($gestor, 'apoiador')->postJson('/api/v1/gestao/conteudos/materiais', [])->assertUnprocessable()->assertJsonValidationErrors(['titulo', 'categoria', 'arquivo_pdf']);

        $this->assertDatabaseCount('materiais_didaticos', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_arquivo_que_nao_e_pdf_recebe_422(): void
    {
        Storage::fake('public');
        $gestor = $this->criarApoiador(['tipo_usuario' => 'admin']);

        $this->actingAs($gestor, 'apoiador')->postJson('/api/v1/gestao/conteudos/materiais', [
            'titulo' => 'Guia', 'categoria' => 'Educação', 'arquivo_pdf' => UploadedFile::fake()->create('guia.pdf', 10, 'text/plain'),
        ])->assertUnprocessable()->assertJsonValidationErrors(['arquivo_pdf']);

        $this->assertDatabaseCount('materiais_didaticos', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_pdf_maior_que_10_mb_recebe_422(): void
    {
        Storage::fake('public');
        $gestor = $this->criarApoiador(['tipo_usuario' => 'admin']);

        $this->actingAs($gestor, 'apoiador')->postJson('/api/v1/gestao/conteudos/materiais', [
            'titulo' => 'Guia', 'categoria' => 'Educação', 'arquivo_pdf' => UploadedFile::fake()->create('guia.pdf', 10241, 'application/pdf'),
        ])->assertUnprocessable()->assertJsonValidationErrors(['arquivo_pdf']);

        $this->assertDatabaseCount('materiais_didaticos', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }
}
