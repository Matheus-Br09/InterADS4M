<?php

namespace Tests\Feature;

use App\Models\Newsletter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

class GestaoNewsletterTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    private function inscrito(string $email, ?string $nome = null, string $data = '2026-11-10 09:00:00'): Newsletter
    {
        return Newsletter::create(['email' => $email, 'nome' => $nome, 'data_inscricao' => $data]);
    }

    public function test_gestor_lista_inscritos_do_mais_novo_para_o_mais_antigo(): void
    {
        $this->actingAsGestor();
        $this->inscrito('antigo@teste.com', 'Antiga', '2026-11-01 08:00:00');
        $this->inscrito('novo@teste.com', 'Nova', '2026-11-11 08:00:00');

        $this->getJson('/api/v1/gestao/newsletter')
            ->assertOk()
            ->assertJsonCount(2, 'dados.data')
            ->assertJsonPath('dados.data.0.email', 'novo@teste.com')
            ->assertJsonPath('dados.data.1.email', 'antigo@teste.com');
    }

    public function test_busca_acha_por_nome_ou_email(): void
    {
        $this->actingAsGestor();
        $this->inscrito('ana@teste.com', 'Ana Paula');
        $this->inscrito('bia@teste.com', 'Beatriz');

        $this->getJson('/api/v1/gestao/newsletter?busca=paula')->assertOk()->assertJsonCount(1, 'dados.data');
        $this->getJson('/api/v1/gestao/newsletter?busca=bia@')->assertOk()->assertJsonCount(1, 'dados.data');
    }

    public function test_sem_login_nada_da_newsletter_e_acessivel(): void
    {
        $inscrito = $this->inscrito('x@teste.com');

        $this->getJson('/api/v1/gestao/newsletter')->assertUnauthorized();
        $this->getJson('/api/v1/gestao/newsletter/exportar')->assertUnauthorized();
        $this->deleteJson("/api/v1/gestao/newsletter/{$inscrito->id}")->assertUnauthorized();
    }

    public function test_exportar_baixa_planilha_csv_com_cabecalho_e_acentos(): void
    {
        $this->actingAsGestor();
        $this->inscrito('maria@teste.com', 'Maria José', '2026-11-10 09:30:00');

        $resposta = $this->get('/api/v1/gestao/newsletter/exportar')->assertOk();

        $resposta->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('attachment', $resposta->headers->get('content-disposition'));

        $corpo = $resposta->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $corpo, 'o BOM faz o Excel abrir os acentos certos');
        $linhas = $this->linhasDoCsv($corpo);
        $this->assertSame(['Nome', 'E-mail', 'Data da inscrição'], $linhas[0]);
        $this->assertSame(['Maria José', 'maria@teste.com', '10/11/2026 09:30'], $linhas[1]);
    }

    private function linhasDoCsv(string $corpo): array
    {
        $corpo = substr($corpo, 3); // tira o BOM (3 bytes) do começo

        return array_map(fn ($linha) => str_getcsv($linha, ';', '"', ''), array_values(array_filter(preg_split('/\R/', $corpo))));
    }

    public function test_exportar_neutraliza_formulas_para_nao_rodarem_na_planilha(): void
    {
        $this->actingAsGestor();
        $this->inscrito('ataque@teste.com', '=HYPERLINK("http://mau.example","clique")');

        $corpo = $this->get('/api/v1/gestao/newsletter/exportar')->streamedContent();

        $linhas = $this->linhasDoCsv($corpo);
        $this->assertSame("'=HYPERLINK(\"http://mau.example\",\"clique\")", $linhas[1][0]);
    }

    public function test_gestor_remove_inscrito_que_pediu_para_sair(): void
    {
        $this->actingAsGestor();
        $inscrito = $this->inscrito('sair@teste.com');

        $this->deleteJson("/api/v1/gestao/newsletter/{$inscrito->id}")->assertNoContent();

        $this->assertDatabaseMissing('newsletter', ['email' => 'sair@teste.com']);
    }

    public function test_remover_inscrito_que_nao_existe_da_404(): void
    {
        $this->actingAsGestor();

        $this->deleteJson('/api/v1/gestao/newsletter/999')->assertNotFound();
    }
}
