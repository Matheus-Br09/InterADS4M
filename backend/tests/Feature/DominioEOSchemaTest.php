<?php

namespace Tests\Feature;

use App\Models\Administrador;
use App\Models\Apoiador;
use App\Models\Crianca;
use App\Models\DoacaoMensal;
use App\Models\DoacaoUnica;
use App\Models\DocumentoTransparencia;
use App\Models\MaterialDidatico;
use App\Models\Noticia;
use App\Models\ProgramaAcao;
use App\Models\Voluntario;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DominioEOSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_todas_as_tabelas_da_ong_existem_no_banco(): void
    {
        $tabelas = [
            'apoiadores', 'criancas', 'apadrinhamentos', 'recompensas_apadrinhamento',
            'doacoes_unicas', 'doacoes_mensais', 'galeria', 'programas_acoes',
            'voluntarios', 'administradores', 'noticias', 'materiais_didaticos',
            'documentos_transparencia', 'newsletter',
        ];

        foreach ($tabelas as $tabela) {
            $this->assertTrue(Schema::hasTable($tabela), "Tabela ausente: {$tabela}");
        }
    }

    public function test_apoio_criado_pelo_cadastro_publico_nunca_vira_administrador(): void
    {
        $this->post('/cadastro', [
            'nome_completo' => 'Tentativa de(admin)',
            'email' => 'admin-falso@exemplo.com',
            'cpf' => '12312312312',
            'senha' => 'senha123',
            'senha_confirmation' => 'senha123',
            'tipo_usuario' => 'admin',
        ]);

        $apoiador = Apoiador::where('email', 'admin-falso@exemplo.com')->firstOrFail();

        $this->assertSame('apoiador', $apoiador->getRawOriginal('tipo_usuario'));
    }

    public function test_modelo_de_administrador_tem_a_sua_propria_tabela_e_oculta_a_senha(): void
    {
        $this->assertSame('administradores', (new Administrador())->getTable());

        $administrador = new Administrador(['nome' => 'Carol', 'email' => 'gestor@exemplo.org', 'senha' => 'x']);
        $administrador->senha = 'segredo';

        $this->assertArrayNotHasKey('senha', $administrador->toArray());
    }

    public function test_apoiador_oculta_a_senha_quando_e_serializado(): void
    {
        $apoiador = new Apoiador(['nome_completo' => 'Teste', 'email' => 't@t.com', 'cpf' => '1']);
        $apoiador->senha = Hash::make('segredo');

        $this->assertArrayNotHasKey('senha', $apoiador->toArray());
        $this->assertArrayNotHasKey('cpf', $apoiador->toArray());
        $this->assertArrayNotHasKey('email', $apoiador->toArray());
        $this->assertArrayNotHasKey('celular', $apoiador->toArray());
    }

    public function test_apoiador_usa_o_campo_de_senha_em_portugues(): void
    {
        $senha = Hash::make('senha123');
        $apoiador = new Apoiador();
        $apoiador->senha = $senha;

        $this->assertSame($senha, $apoiador->getAuthPassword());
    }

    public function test_todos_os_valores_de_categoria_de_programa_sao_aceitos(): void
    {
        $categorias = ['Neuropedagogia', 'Saúde e Bem-estar', 'Assistência Social', 'Educação', 'Outros'];

        foreach ($categorias as $indice => $categoria) {
            $this->criarProgramaComCategoria($categoria);
            $this->assertDatabaseHas('programas_acoes', ['categoria' => $categoria]);
        }
    }

    public function test_programa_recusa_categoria_fora_do_dominio(): void
    {
        $this->expectException(QueryException::class);

        ProgramaAcao::create([
            'titulo' => 'Categoria inválida',
            'resumo' => 'Resumo',
            'texto_completo' => 'Texto',
            'categoria' => 'Categoria inexistente',
            'imagem_capa' => 'capa.jpg',
            'status' => 'ativo',
            'data_criacao' => now(),
        ]);
    }

    public function test_area_de_atuacao_e_disponibilidade_do_voluntario_sao_validadas(): void
    {
        foreach (['Neuropedagogia', 'Odontologia', 'Nutrição', 'Fisioterapia', 'Apoio Geral', 'Outros'] as $area) {
            foreach (['Manhã', 'Tarde', 'Integral'] as $disponibilidade) {
                Voluntario::create([
                    'apoiador_id' => $this->criarApoiadorUnico()->id,
                    'area_atuacao' => $area,
                    'disponibilidade' => $disponibilidade,
                    'arquivo_curriculo' => '',
                    'status' => 'em_analise',
                    'data_inscricao' => now(),
                ]);
            }
        }

        $this->assertSame(18, Voluntario::count());
    }

    public function test_status_da_crianca_sempre_comeca_disponivel(): void
    {
        $crianca = Crianca::create([
            'nome' => 'Sem status informado',
            'data_nascimento' => '2016-01-01',
            'data_cadastro' => now(),
        ]);

        $this->assertSame('disponivel', $crianca->fresh()->status);
    }

    public function test_doacao_unica_comeca_com_status_concluido(): void
    {
        $doacao = DoacaoUnica::create([
            'apoiador_id' => $this->criarApoiadorUnico()->id,
            'valor' => 25.00,
            'metodo_pagamento' => 'Pix',
            'data_doacao' => now(),
        ]);

        $this->assertSame('concluido', $doacao->fresh()->status);
    }

    public function test_doacao_mensal_comeca_com_status_ativo(): void
    {
        $doacao = DoacaoMensal::create([
            'apoiador_id' => $this->criarApoiadorUnico()->id,
            'valor_mensal' => 30.00,
            'dia_vencimento' => 5,
            'data_assinatura' => now(),
        ]);

        $this->assertSame('ativo', $doacao->fresh()->status);
    }

    public function test_modelos_de_conteudo_aceitam_registros_com_campos_opcionais_vazios(): void
    {
        $this->assertSame('noticia', Noticia::create([
            'titulo' => 'Notícia sem imagem',
            'resumo' => 'Resumo',
            'texto_completo' => 'Texto',
            'data_criacao' => now(),
        ])->fresh()->tipo);

        $this->assertNull(MaterialDidatico::create([
            'titulo' => 'Material sem capa',
            'arquivo_pdf' => 'material.pdf',
            'data_upload' => now(),
        ])->fresh()->imagem_capa);

        $this->assertSame('Relatório Anual', DocumentoTransparencia::create([
            'titulo' => 'Relatório 2025',
            'ano_referencia' => 2025,
            'tipo_documento' => 'Relatório Anual',
            'arquivo_pdf' => 'relatorio.pdf',
            'data_upload' => now(),
        ])->fresh()->tipo_documento);
    }

    public function test_valores_nulos_sao_aceitos_nos_campos_opcionais_da_crianca(): void
    {
        $crianca = Crianca::create([
            'nome' => 'Crianca sem ficha',
            'data_nascimento' => '2014-02-02',
            'historico' => null,
            'imagem_perfil' => null,
            'data_cadastro' => now(),
        ]);

        $this->assertNull($crianca->fresh()->historico);
        $this->assertNull($crianca->fresh()->imagem_perfil);
    }

    private function criarProgramaComCategoria(string $categoria): void
    {
        ProgramaAcao::create([
            'titulo' => 'Programa ' . $categoria,
            'resumo' => 'Resumo',
            'texto_completo' => 'Texto',
            'categoria' => $categoria,
            'imagem_capa' => 'capa.jpg',
            'status' => 'ativo',
            'data_criacao' => now(),
        ]);
    }

    private function criarApoiadorUnico(): Apoiador
    {
        static $sequencia = 0;
        $sequencia++;

        return Apoiador::create([
            'nome_completo' => 'Apoiador ' . $sequencia,
            'email' => 'apoio' . $sequencia . '@exemplo.com',
            'senha' => Hash::make('senha123'),
            'cpf' => (string) $sequencia,
            'data_cadastro' => now()->toDateString(),
        ]);
    }

    public function test_campos_de_massa_assignment_nao_sao_descartados_em_silencio(): void
    {
        $apoiador = $this->criarApoiadorUnico();
        $apoiador->forceFill(['tipo_usuario' => 'admin'])->save();
        $this->assertSame('admin', $apoiador->fresh()->tipo_usuario);

        $doacao = DoacaoUnica::create([
            'apoiador_id' => $apoiador->id,
            'valor' => 50.00,
            'metodo_pagamento' => 'Pix',
            'status' => 'pendente',
            'data_doacao' => now(),
        ]);
        $this->assertSame('pendente', $doacao->fresh()->status);

        $mensal = DoacaoMensal::create([
            'apoiador_id' => $apoiador->id,
            'valor_mensal' => 40.00,
            'dia_vencimento' => 5,
            'metodo_pagamento' => 'Cartao de Credito',
            'status' => 'ativo',
            'data_assinatura' => now(),
        ]);
        $this->assertSame('Cartao de Credito', $mensal->fresh()->metodo_pagamento);

        $noticia = Noticia::create([
            'titulo' => 'Campanha de inverno',
            'resumo' => 'Resumo da campanha.',
            'texto_completo' => 'Texto da campanha.',
            'tipo' => 'campanha',
            'data_criacao' => now(),
        ]);
        $this->assertSame('campanha', $noticia->fresh()->tipo);
    }
}
