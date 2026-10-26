<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VoluntarioAnonimoTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitante_pode_se_cadastrar_como_voluntario_sem_login(): void
    {
        Storage::fake('local');

        $response = $this->postJson('/api/v1/voluntariado', [
            'nome_completo' => 'João Silva',
            'email' => 'joao@email.com',
            'celular' => '(11) 98765-4321',
            'area_atuacao' => 'Nutrição',
            'disponibilidade' => 'Manhã',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['dados' => ['id', 'nome_completo', 'email', 'celular', 'area_atuacao', 'disponibilidade', 'status']]);

        $this->assertDatabaseHas('voluntarios', [
            'nome_completo' => 'João Silva',
            'email' => 'joao@email.com',
            'celular' => '(11) 98765-4321',
            'area_atuacao' => 'Nutrição',
            'disponibilidade' => 'Manhã',
            'status' => 'em_analise',
            'apoiador_id' => null,
        ]);
    }

    public function test_cadastro_anonimo_requer_nome_email_celular(): void
    {
        $response = $this->postJson('/api/v1/voluntariado', [
            'area_atuacao' => 'Nutrição',
            'disponibilidade' => 'Manhã',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['nome_completo', 'email', 'celular']);
    }

    public function test_cadastro_anonimo_valida_email(): void
    {
        $response = $this->postJson('/api/v1/voluntariado', [
            'nome_completo' => 'João Silva',
            'email' => 'email-invalido',
            'celular' => '(11) 98765-4321',
            'area_atuacao' => 'Nutrição',
            'disponibilidade' => 'Manhã',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_cadastro_anonimo_com_curriculo(): void
    {
        Storage::fake('local');

        $curriculo = UploadedFile::fake()->create('curriculo.pdf', 100, 'application/pdf');

        $response = $this->postJson('/api/v1/voluntariado', [
            'nome_completo' => 'Maria Santos',
            'email' => 'maria@email.com',
            'celular' => '(21) 99999-8888',
            'area_atuacao' => 'Fisioterapia',
            'disponibilidade' => 'Tarde',
            'arquivo_curriculo' => $curriculo,
        ]);

        $response->assertStatus(200);

        $voluntario = $response->json('dados');
        $this->assertNotNull($voluntario['arquivo_curriculo']);

        Storage::disk('local')->assertExists($voluntario['arquivo_curriculo']);
    }

    public function test_cadastro_anonimo_curriculo_opcional(): void
    {
        Storage::fake('local');

        $response = $this->postJson('/api/v1/voluntariado', [
            'nome_completo' => 'Pedro Costa',
            'email' => 'pedro@email.com',
            'celular' => '(31) 97777-6666',
            'area_atuacao' => 'Odontologia',
            'disponibilidade' => 'Integral',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('voluntarios', [
            'nome_completo' => 'Pedro Costa',
            'email' => 'pedro@email.com',
        ]);
    }

    public function test_cadastro_anonimo_valida_area_atuacao(): void
    {
        $response = $this->postJson('/api/v1/voluntariado', [
            'nome_completo' => 'Ana Lima',
            'email' => 'ana@email.com',
            'celular' => '(41) 96666-5555',
            'area_atuacao' => 'Área Inválida',
            'disponibilidade' => 'Manhã',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['area_atuacao']);
    }

    public function test_cadastro_anonimo_valida_disponibilidade(): void
    {
        $response = $this->postJson('/api/v1/voluntariado', [
            'nome_completo' => 'Carlos Souza',
            'email' => 'carlos@email.com',
            'celular' => '(51) 95555-4444',
            'area_atuacao' => 'Apoio Geral',
            'disponibilidade' => 'Disponibilidade Inválida',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['disponibilidade']);
    }

    public function test_multiplos_voluntarios_anonimos_podem_se_cadastrar(): void
    {
        Storage::fake('local');

        // Primeiro cadastro
        $this->postJson('/api/v1/voluntariado', [
            'nome_completo' => 'Voluntário 1',
            'email' => 'vol1@email.com',
            'celular' => '(11) 91111-1111',
            'area_atuacao' => 'Nutrição',
            'disponibilidade' => 'Manhã',
        ])->assertStatus(200);

        // Segundo cadastro (email diferente)
        $this->postJson('/api/v1/voluntariado', [
            'nome_completo' => 'Voluntário 2',
            'email' => 'vol2@email.com',
            'celular' => '(11) 92222-2222',
            'area_atuacao' => 'Fisioterapia',
            'disponibilidade' => 'Tarde',
        ])->assertStatus(200);

        $this->assertDatabaseCount('voluntarios', 2);
    }

    public function test_retorna_mensagem_de_sucesso(): void
    {
        Storage::fake('local');

        $response = $this->postJson('/api/v1/voluntariado', [
            'nome_completo' => 'Teste Mensagem',
            'email' => 'teste@email.com',
            'celular' => '(11) 98888-7777',
            'area_atuacao' => 'Outros',
            'disponibilidade' => 'Integral',
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['message' => 'Inscrição recebida com sucesso! Entraremos em contato em breve.']);
    }
}
