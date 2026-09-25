<?php

namespace Tests\Feature;

use App\Models\Newsletter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitante_se_inscreve_na_newsletter_pelo_formulario_publico(): void
    {
        $response = $this->postJson('/api/newsletter', [
            'email' => 'interessada@exemplo.com',
            'nome' => 'Maria Interessada',
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('dados.email', 'interessada@exemplo.com')
            ->assertJsonPath('dados.nome', 'Maria Interessada');

        $this->assertDatabaseHas('newsletter', [
            'email' => 'interessada@exemplo.com',
            'nome' => 'Maria Interessada',
        ]);
    }

    public function test_inscricao_na_newsletter_aceita_apenas_o_endereco_de_email(): void
    {
        $this->postJson('/api/newsletter', ['email' => 'sem-nome@exemplo.com'])
            ->assertCreated()
            ->assertJsonPath('dados.nome', null);

        $this->assertDatabaseHas('newsletter', ['email' => 'sem-nome@exemplo.com']);
    }

    public function test_nao_permite_inscricao_duplicada_na_newsletter(): void
    {
        Newsletter::create([
            'email' => 'repetida@exemplo.com',
            'nome' => 'Já inscrita',
            'data_inscricao' => now(),
        ]);

        $this->postJson('/api/newsletter', ['email' => 'repetida@exemplo.com'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');

        $this->assertSame(1, Newsletter::where('email', 'repetida@exemplo.com')->count());
    }

    public function test_recusa_e_mail_invalido_na_newsletter(): void
    {
        $this->postJson('/api/newsletter', ['email' => 'nao-e-email'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');

        $this->assertDatabaseCount('newsletter', 0);
    }

    public function test_recusa_newsletter_sem_e_mail(): void
    {
        $this->postJson('/api/newsletter', ['nome' => 'Sem e-mail'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }
}
