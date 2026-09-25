<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

// Trava de LGPD: as rotas em routes/api.php são públicas porque alimentam o
// site, então nenhuma delas pode devolver dado pessoal de apoiador (nome,
// contato, endereço, valores pagos, papel na gestão) nem dado sensível de
// criança (histórico, data de nascimento). Este teste varre a resposta
// inteira de cada endpoint, em qualquer nível, em vez de conferir caminho
// por caminho - assim uma coluna nova ou uma relação nova não escapa.
class ApiPublicaNaoExpoeDadoPessoalTest extends TestCase
{
    use CriaCenarioOng;
    use RefreshDatabase;

    private const CHAVES_PROIBIDAS = [
        'senha', 'password', 'cpf', 'celular', 'email',
        'cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado',
        'nome_completo', 'tipo_usuario', 'sexo',
        'historico', 'data_nascimento',
        'valor', 'valor_mensal', 'metodo_pagamento', 'dia_vencimento',
    ];

    private const ENDPOINTS = [
        '/api/criancas',
        '/api/apoiadores',
        '/api/apadrinhamentos',
        '/api/programas',
        '/api/noticias',
        '/api/materiais-didaticos',
        '/api/transparencia',
    ];

    public function test_nenhuma_api_publica_devolve_dado_pessoal_ou_financeiro(): void
    {
        $gestor = $this->criarGestor(['cidade' => 'Recife', 'estado' => 'PE']);
        $crianca = $this->criarCrianca([
            'nome' => 'Ben Tennyson',
            'historico' => 'Acompanhamento neuropedagogico semanal.',
        ]);
        $this->criarApadrinhamento($gestor, $crianca, ['valor_mensal' => 100.00]);
        $this->criarDoacaoUnica($gestor, ['valor' => 75.00]);
        $this->criarDoacaoMensal($gestor, ['valor_mensal' => 40.00]);
        $this->criarVoluntario($gestor, ['status' => 'aprovado']);
        $this->criarPrograma();
        $this->criarNoticia();

        foreach (self::ENDPOINTS as $endpoint) {
            $resposta = $this->getJson($endpoint)->assertOk();
            $proibidas = $this->chavesProibidas($resposta->json());

            $this->assertSame(
                [],
                $proibidas,
                "{$endpoint} está devolvendo dado que não pode ser público: ".implode(', ', $proibidas),
            );
        }
    }

    public function test_a_api_publica_nao_devolve_nome_de_apoiador_nem_valor_doado(): void
    {
        $apoiador = $this->criarApoiador();
        $this->criarApadrinhamento($apoiador, $this->criarCrianca());
        $this->criarDoacaoUnica($apoiador, ['valor' => 75.00]);

        foreach (self::ENDPOINTS as $endpoint) {
            $corpo = $this->getJson($endpoint)->assertOk()->getContent();

            $this->assertStringNotContainsString($apoiador->nome_completo, $corpo, "{$endpoint} devolveu o nome do apoiador.");
            $this->assertStringNotContainsString($apoiador->email, $corpo, "{$endpoint} devolveu o e-mail do apoiador.");
            $this->assertStringNotContainsString($apoiador->cpf, $corpo, "{$endpoint} devolveu o CPF do apoiador.");
            $this->assertStringNotContainsString($apoiador->senha, $corpo, "{$endpoint} devolveu a senha do apoiador.");
        }
    }

    public function test_o_painel_continua_mostrando_os_dados_para_a_gestao(): void
    {
        // O $hidden dos models só afeta JSON: a tela da gestão continua
        // precisando ver nome, e-mail e endereço para trabalhar.
        $this->actingAsGestor();

        $this->get('/')->assertOk();
    }

    // Varre a resposta inteira (qualquer profundidade) e devolve o caminho das
    // chaves proibidas encontradas.
    private function chavesProibidas(mixed $dados, string $caminho = ''): array
    {
        if (! is_array($dados)) {
            return [];
        }

        $encontradas = [];

        foreach ($dados as $chave => $valor) {
            if (is_string($chave) && in_array(strtolower($chave), self::CHAVES_PROIBIDAS, true)) {
                $encontradas[] = $caminho.$chave;
            }

            $encontradas = [...$encontradas, ...$this->chavesProibidas($valor, $caminho.$chave.'.')];
        }

        return $encontradas;
    }
}
