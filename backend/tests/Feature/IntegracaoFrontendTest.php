<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

class IntegracaoFrontendTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    public function test_gestor_publica_pdf_sem_opcionais_e_site_baixa_o_arquivo(): void
    {
        Storage::fake('public');
        $this->actingAsGestor();

        $resposta = $this->postJson('/api/v1/gestao/conteudos/materiais', [
            'titulo' => 'Guia de inclusão', 'categoria' => 'Educação',
            'arquivo_pdf' => UploadedFile::fake()->create('guia.pdf', 10, 'application/pdf'),
        ])->assertOk()->assertJsonPath('dados.categoria', 'Educação');

        $this->assertDatabaseHas('materiais_didaticos', ['id' => $resposta->json('dados.id'), 'categoria' => 'Educação']);
        Storage::disk('public')->assertExists($resposta->json('dados.arquivo_pdf'));
        $this->postJson('/api/v1/auth/sair')->assertNoContent();
        $this->getJson('/api/v1/conteudos/materiais')->assertOk()->assertJsonPath('dados.0.titulo', 'Guia de inclusão');
        $this->get('/api/v1/conteudos/materiais/'.$resposta->json('dados.id').'/arquivos/arquivo_pdf')->assertDownload();
    }

    public function test_gestor_publica_noticia_sem_imagem_ou_data_e_exclui(): void
    {
        $this->actingAsGestor();
        $resposta = $this->postJson('/api/v1/gestao/conteudos/noticias', [
            'titulo' => 'Nova ação', 'resumo' => 'Resumo', 'texto_completo' => 'Conteúdo completo',
            'tipo' => 'noticia', 'imagem' => null, 'data_evento' => null,
        ])->assertOk()->assertJsonPath('dados.titulo', 'Nova ação');

        $id = $resposta->json('dados.id');
        $this->assertDatabaseHas('noticias', ['id' => $id, 'imagem' => null, 'data_evento' => null]);
        $this->deleteJson('/api/v1/gestao/conteudos/noticias/'.$id)->assertNoContent();
        $this->assertDatabaseMissing('noticias', ['id' => $id]);
    }

    public function test_apoiador_nao_pode_publicar_conteudo_da_ong(): void
    {
        $this->actingAs($this->criarApoiador(), 'apoiador');
        $this->postJson('/api/v1/gestao/conteudos/materiais', ['titulo' => 'Não publicar'])->assertForbidden();
        $this->postJson('/api/v1/gestao/conteudos/noticias', ['titulo' => 'Não publicar'])->assertForbidden();
        $this->assertDatabaseCount('materiais_didaticos', 0);
        $this->assertDatabaseCount('noticias', 0);
    }

    public function test_api_entrega_csrf_e_cadastro_json(): void
    {
        $this->getJson('/api/v1/csrf')->assertOk()->assertJsonStructure(['token']);

        $response = $this->postJson('/api/v1/auth/cadastro', [
            'nome_completo' => 'Pessoa Frontend', 'email' => 'frontend@example.org', 'cpf' => '11122233387',
            'senha' => 'SenhaFrontend123', 'senha_confirmation' => 'SenhaFrontend123',
        ]);

        $response->assertCreated()->assertJsonPath('dados.email', 'frontend@example.org');
        $this->assertAuthenticated('apoiador');
    }

    public function test_api_de_login_retorna_conta_e_logout(): void
    {
        $apoiador = $this->criarApoiador(['email' => 'frontend@example.org', 'senha' => Hash::make('SenhaFrontend123')]);

        $this->postJson('/api/v1/auth/entrar', ['email' => $apoiador->email, 'senha' => 'SenhaFrontend123'])
            ->assertOk()->assertJsonPath('dados.id', $apoiador->id);
        $this->getJson('/api/v1/auth/eu')->assertOk()->assertJsonPath('dados.email', $apoiador->email);
        $this->postJson('/api/v1/auth/sair')->assertNoContent();
        $this->getJson('/api/v1/auth/eu')->assertUnauthorized();
    }

    public function test_api_de_conteudo_publica_programas_e_pagina(): void
    {
        $this->criarPrograma(['titulo' => 'Programa frontend']);
        $this->getJson('/api/v1/conteudos/programas')->assertOk()
            ->assertJsonPath('dados.0.titulo', 'Programa frontend')
            ->assertJsonStructure(['dados', 'paginacao']);
    }

    public function test_api_de_doacao_registra_intencao_pendente(): void
    {
        $apoiador = $this->criarApoiador();
        $this->actingAs($apoiador, 'apoiador')->postJson('/api/v1/doacoes', ['valor' => '25.00', 'metodo_pagamento' => 'pix'])
            ->assertCreated()->assertJsonPath('dados.status', 'pendente');
        $this->assertDatabaseHas('doacoes_unicas', ['apoiador_id' => $apoiador->id, 'status' => 'pendente']);
    }

    public function test_lista_publica_permite_registrar_apadrinhamento_pendente_sem_ativar_crianca(): void
    {
        $apoiador = $this->criarApoiador();
        $crianca = $this->criarCrianca(['status' => 'disponivel']);

        $lista = $this->getJson('/api/criancas')->assertOk()
            ->assertJsonPath('dados.0.id', $crianca->id);

        $this->actingAs($apoiador, 'apoiador')->postJson('/api/v1/apadrinhamentos', [
            'crianca_id' => $lista->json('dados.0.id'), 'valor_mensal' => '50.00',
        ])->assertOk()->assertJsonPath('dados.status', 'pendente');

        $this->assertDatabaseHas('apadrinhamentos', [
            'apoiador_id' => $apoiador->id, 'crianca_id' => $crianca->id,
            'valor_mensal' => 50, 'status' => 'pendente',
        ]);
        $this->getJson('/api/criancas')->assertOk()
            ->assertJsonPath('dados.0.apadrinhada', false)
            ->assertJsonPath('dados.0.status', 'disponivel');
    }
}
