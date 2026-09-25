<?php

namespace Tests\Concerns;

use App\Models\Apadrinhamento;
use App\Models\Apoiador;
use App\Models\Crianca;
use App\Models\DoacaoMensal;
use App\Models\DoacaoUnica;
use App\Models\Noticia;
use App\Models\ProgramaAcao;
use App\Models\Voluntario;
use Illuminate\Support\Facades\Hash;

trait CriaCenarioOng
{
    protected static int $sequencia = 0;

    protected function criarApoiador(array $atributos = []): Apoiador
    {
        static::$sequencia++;

        return Apoiador::create(array_merge([
            'nome_completo' => 'Apoiador Teste ' . static::$sequencia,
            'email' => 'apoiador' . static::$sequencia . '@teste.com',
            'senha' => Hash::make('senha123'),
            'cpf' => sprintf('%011d', static::$sequencia),
            'celular' => '(81) 90000-0000',
            'data_cadastro' => now()->toDateString(),
        ], $atributos));
    }

    protected function criarCrianca(array $atributos = []): Crianca
    {
        static::$sequencia++;

        return Crianca::create(array_merge([
            'nome' => 'Crianca Teste ' . static::$sequencia,
            'data_nascimento' => '2015-05-05',
            'historico' => 'Historico de teste.',
            'imagem_perfil' => 'crianca.jpg',
            'status' => 'disponivel',
            'data_cadastro' => now(),
        ], $atributos));
    }

    protected function criarPrograma(array $atributos = []): ProgramaAcao
    {
        static::$sequencia++;

        return ProgramaAcao::create(array_merge([
            'titulo' => 'Programa Teste ' . static::$sequencia,
            'resumo' => 'Resumo do programa de teste.',
            'texto_completo' => 'Texto completo do programa de teste.',
            'categoria' => 'Neuropedagogia',
            'imagem_capa' => 'capa.jpg',
            'status' => 'ativo',
            'data_criacao' => now(),
        ], $atributos));
    }

    protected function criarApadrinhamento(Apoiador $apoiador, Crianca $crianca, array $atributos = []): Apadrinhamento
    {
        return Apadrinhamento::create(array_merge([
            'apoiador_id' => $apoiador->id,
            'crianca_id' => $crianca->id,
            'valor_mensal' => 100.00,
            'data_inicio' => now(),
            'status' => 'ativo',
        ], $atributos));
    }

    protected function criarDoacaoUnica(Apoiador $apoiador, array $atributos = []): DoacaoUnica
    {
        return DoacaoUnica::create(array_merge([
            'apoiador_id' => $apoiador->id,
            'valor' => 50.00,
            'metodo_pagamento' => 'Pix',
            'data_doacao' => now(),
        ], $atributos));
    }

    protected function criarDoacaoMensal(Apoiador $apoiador, array $atributos = []): DoacaoMensal
    {
        return DoacaoMensal::create(array_merge([
            'apoiador_id' => $apoiador->id,
            'valor_mensal' => 40.00,
            'dia_vencimento' => 10,
            'status' => 'ativo',
            'data_assinatura' => now(),
        ], $atributos));
    }

    protected function criarVoluntario(Apoiador $apoiador, array $atributos = []): Voluntario
    {
        return Voluntario::create(array_merge([
            'apoiador_id' => $apoiador->id,
            'area_atuacao' => 'Odontologia',
            'disponibilidade' => 'Tarde',
            'arquivo_curriculo' => 'curriculo.pdf',
            'status' => 'em_analise',
            'data_inscricao' => now(),
        ], $atributos));
    }

    protected function criarNoticia(array $atributos = []): Noticia
    {
        static::$sequencia++;

        return Noticia::create(array_merge([
            'titulo' => 'Noticia Teste ' . static::$sequencia,
            'resumo' => 'Resumo da noticia de teste.',
            'texto_completo' => 'Texto completo da noticia de teste.',
            'imagem' => 'noticia.jpg',
            'data_criacao' => now(),
        ], $atributos));
    }

    protected function credenciaisDoApoiador(Apoiador $apoiador, string $senha = 'senha123'): array
    {
        return ['email' => $apoiador->email, 'senha' => $senha];
    }
}
