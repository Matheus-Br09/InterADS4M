<?php

namespace Database\Seeders;

use App\Models\Apoiador;
use App\Models\Crianca;
use App\Models\Apadrinhamento;
use App\Models\RecompensaApadrinhamento;
use App\Models\DoacaoMensal;
use App\Models\DoacaoUnica;
use App\Models\Galeria;
use App\Models\ProgramaAcao;
use App\Models\Voluntario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with demo ONG data.
     */
    public function run(): void
    {
        // 1. Criancas
        $crianca1 = Crianca::firstOrCreate(
            ['nome' => 'Lucas Gabriel Santos'],
            [
                'data_nascimento' => '2017-06-14',
                'historico' => 'Adora desenhar e praticar esportes. Participa das oficinas de artes e reforço escolar.',
                'imagem_perfil' => 'crianca1.jpg',
                'status' => 'disponivel',
                'data_cadastro' => now(),
            ]
        );

        $crianca2 = Crianca::firstOrCreate(
            ['nome' => 'Sophia Beatriz Oliveira'],
            [
                'data_nascimento' => '2018-09-22',
                'historico' => 'Muito comunicativa, gosta de música e teatro. Recebe acompanhamento neuropedagógico.',
                'imagem_perfil' => 'crianca2.jpg',
                'status' => 'apadrinhada',
                'data_cadastro' => now(),
            ]
        );

        $crianca3 = Crianca::firstOrCreate(
            ['nome' => 'Mateus Henrique Silva'],
            [
                'data_nascimento' => '2016-03-05',
                'historico' => 'Apaixonado por robótica e jogos educativos. Muito dedicado nas atividades da ONG.',
                'imagem_perfil' => 'crianca3.jpg',
                'status' => 'disponivel',
                'data_cadastro' => now(),
            ]
        );

        // 2. Apoiadores
        $apoiador1 = Apoiador::firstOrCreate(
            ['email' => 'mariana.costa@email.com'],
            [
                'nome_completo' => 'Mariana Costa Ramos',
                'senha' => Hash::make('senha123'),
                'celular' => '(11) 98765-4321',
                'cpf' => '123.456.789-00',
                'sexo' => 'Feminino',
                'cep' => '01310-100',
                'logradouro' => 'Avenida Paulista',
                'numero' => '1000',
                'complemento' => 'Apto 42',
                'bairro' => 'Bela Vista',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'data_cadastro' => now(),
            ]
        );

        $apoiador2 = Apoiador::firstOrCreate(
            ['email' => 'roberto.almeida@email.com'],
            [
                'nome_completo' => 'Roberto Carlos Almeida',
                'senha' => Hash::make('senha123'),
                'celular' => '(11) 97654-3210',
                'cpf' => '987.654.321-99',
                'sexo' => 'Masculino',
                'cep' => '04578-000',
                'logradouro' => 'Rua Funchal',
                'numero' => '250',
                'complemento' => 'Sala 12',
                'bairro' => 'Vila Olímpia',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'data_cadastro' => now(),
            ]
        );

        // 3. Programas e Acoes
        ProgramaAcao::firstOrCreate(
            ['titulo' => 'Oficina de Neuropedagogia e Desenvolvimento'],
            [
                'resumo' => 'Atendimento especializado para crianças neurodivergentes e estímulo à aprendizagem.',
                'texto_completo' => 'O programa oferece sessões semanais com especialistas em neuropedagogia para desenvolvimento cognitivo, social e motor.',
                'categoria' => 'Neuropedagogia',
                'imagem_capa' => 'programa_neuro.jpg',
                'status' => 'ativo',
                'data_criacao' => now(),
            ]
        );

        ProgramaAcao::firstOrCreate(
            ['titulo' => 'Sorriso Saudável: Odontologia Preventiva'],
            [
                'resumo' => 'Mutirão de saúde bucal e prevenção para todas as famílias atendidas pela ONG.',
                'texto_completo' => 'Consultas odontológicas preventivas, aplicação de flúor e distribuição de kits de higiene para as crianças da comunidade.',
                'categoria' => 'Saúde e Bem-estar',
                'imagem_capa' => 'programa_odonto.jpg',
                'status' => 'ativo',
                'data_criacao' => now(),
            ]
        );

        ProgramaAcao::firstOrCreate(
            ['titulo' => 'Futuro Brilhante: Reforço Escolar e Tecnologia'],
            [
                'resumo' => 'Aulas de apoio escolar, alfabetização digital e introdução à robótica.',
                'texto_completo' => 'Laboratório de informática aberto para aprendizado e apoio nas disciplinas escolares do ensino fundamental.',
                'categoria' => 'Educação',
                'imagem_capa' => 'programa_educacao.jpg',
                'status' => 'ativo',
                'data_criacao' => now(),
            ]
        );

        // 4. Apadrinhamento
        $apadrinhamento = Apadrinhamento::firstOrCreate(
            ['apoiador_id' => $apoiador1->id, 'crianca_id' => $crianca2->id],
            [
                'valor_mensal' => 120.00,
                'data_inicio' => now()->subMonths(2),
                'status' => 'ativo',
            ]
        );

        // 5. Recompensa do Apadrinhamento
        RecompensaApadrinhamento::firstOrCreate(
            ['apadrinhamento_id' => $apadrinhamento->id, 'titulo' => 'Carta de Agradecimento e Desenho'],
            [
                'mensagem' => 'A Sophia preparou este lindo desenho para agradecer o apoio nas suas aulas de música!',
                'arquivo_midia' => 'desenho_sophia.jpg',
                'data_envio' => now()->subDays(15),
            ]
        );

        // 6. Doacoes Mensais e Unicas
        DoacaoMensal::firstOrCreate(
            ['apoiador_id' => $apoiador1->id],
            [
                'valor_mensal' => 80.00,
                'dia_vencimento' => 10,
                'status' => 'ativo',
                'data_assinatura' => now()->subMonths(3),
            ]
        );

        DoacaoUnica::firstOrCreate(
            ['apoiador_id' => $apoiador2->id, 'valor' => 250.00],
            [
                'metodo_pagamento' => 'PIX',
                'data_doacao' => now()->subDays(5),
            ]
        );

        // 7. Voluntario
        Voluntario::firstOrCreate(
            ['apoiador_id' => $apoiador2->id],
            [
                'area_atuacao' => 'Odontologia',
                'disponibilidade' => 'Tarde',
                'arquivo_curriculo' => 'curriculo_roberto.pdf',
                'status' => 'aprovado',
                'data_entrevista' => now()->subMonth(),
                'mensagem_entrevista' => 'Profissional com grande experiência e disponibilidade aos sábados.',
                'data_inscricao' => now()->subMonths(2),
            ]
        );

        // 8. Galeria
        Galeria::firstOrCreate(
            ['nome_imagem' => 'evento_arraia_2026.jpg'],
            [
                'legenda' => 'Festa Junina da ONG com as crianças e voluntários',
                'data_upload' => now()->subDays(20),
            ]
        );
    }
}
