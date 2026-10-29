<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

class VoluntarioMvpTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    private function dados(array $extra = []): array
    {
        return array_merge([
            'nome_completo' => 'João Silva',
            'email' => 'joao@email.com',
            'celular' => '(11) 98765-4321',
            'area_atuacao' => 'Nutrição',
            'disponibilidade' => 'Manhã',
            'data_nascimento' => now()->subYears(30)->toDateString(),
            'aceite_privacidade' => true,
        ], $extra);
    }

    public function test_quem_faz_18_anos_hoje_pode_se_cadastrar(): void
    {
        $this->postJson('/api/v1/voluntariado', $this->dados([
            'data_nascimento' => now()->subYears(18)->toDateString(),
        ]))->assertOk();
    }

    public function test_menor_de_18_e_recusado(): void
    {
        $this->postJson('/api/v1/voluntariado', $this->dados([
            'data_nascimento' => now()->subYears(18)->addDay()->toDateString(),
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('data_nascimento')
            ->assertJsonPath('errors.data_nascimento.0', 'É preciso ter 18 anos ou mais para ser voluntário.');
    }

    public function test_aceite_de_privacidade_e_obrigatorio(): void
    {
        $dados = $this->dados();
        unset($dados['aceite_privacidade']);

        $this->postJson('/api/v1/voluntariado', $dados)
            ->assertStatus(422)
            ->assertJsonValidationErrors('aceite_privacidade');

        $this->assertDatabaseCount('voluntarios', 0);
    }

    public function test_data_de_nascimento_e_gravada(): void
    {
        $nascimento = now()->subYears(30)->toDateString();

        $this->postJson('/api/v1/voluntariado', $this->dados(['data_nascimento' => $nascimento]))->assertOk();

        $this->assertDatabaseHas('voluntarios', ['email' => 'joao@email.com', 'data_nascimento' => $nascimento]);
    }

    public function test_arquivo_que_nao_e_pdf_e_recusado(): void
    {
        Storage::fake('local');

        $this->postJson('/api/v1/voluntariado', $this->dados([
            'arquivo_curriculo' => UploadedFile::fake()->create('cv.pdf', 10, 'application/x-msdownload'),
        ]))->assertStatus(422);

        $this->assertDatabaseCount('voluntarios', 0);
    }

    public function test_gestor_exclui_voluntario_e_o_curriculo(): void
    {
        Storage::fake('local');
        $this->actingAsGestor();

        $caminho = UploadedFile::fake()->create('cv.pdf', 10, 'application/pdf')->store('curriculos', 'local');
        $voluntario = $this->criarVoluntario($this->criarApoiador(), ['arquivo_curriculo' => $caminho]);
        Storage::disk('local')->assertExists($caminho);

        $this->deleteJson('/api/v1/gestao/voluntarios/'.$voluntario->id)->assertNoContent();

        $this->assertDatabaseMissing('voluntarios', ['id' => $voluntario->id]);
        Storage::disk('local')->assertMissing($caminho);
    }

    public function test_apoiador_comum_nao_exclui_voluntario(): void
    {
        $apoiador = $this->criarApoiador();
        $voluntario = $this->criarVoluntario($apoiador);
        $this->actingAs($apoiador, 'apoiador');

        $this->deleteJson('/api/v1/gestao/voluntarios/'.$voluntario->id)->assertForbidden();

        $this->assertDatabaseHas('voluntarios', ['id' => $voluntario->id]);
    }

    public function test_consulta_da_propria_inscricao_nao_existe_mais(): void
    {
        $this->actingAs($this->criarApoiador(), 'apoiador');

        $this->getJson('/api/v1/voluntariado')->assertStatus(405);
    }
}
