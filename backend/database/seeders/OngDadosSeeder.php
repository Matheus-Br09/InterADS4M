<?php

namespace Database\Seeders;

use App\Models\Apadrinhamento;
use App\Models\Apoiador;
use App\Models\Crianca;
use App\Models\DoacaoMensal;
use App\Models\DoacaoUnica;
use App\Models\Galeria;
use App\Models\ProgramaAcao;
use App\Models\RecompensaApadrinhamento;
use App\Models\Voluntario;
use App\Support\SenhaForte;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OngDadosSeeder extends Seeder
{
    public function run(): void
    {
        /*
        | Este seeder só cria dados de DEMONSTRAÇÃO: e-mails de exemplo (@exemplo.org,
        | domínio reservado pela RFC 2606), telefones e CPFs sintéticos, e as
        | crianças são personagens de desenho. Nenhum dado de pessoa real e,
        | principalmente, nenhum hash de senha fica no repositório - a senha de
        | cada conta nasce inutilizável e quem for usar o painel define a sua
        | com o comando gestor:senha (gestão) ou apoiador:senha (apoiador).
        |
        | A senha é seedada como hash de um valor aleatório descartado: escrever
        | aqui um hash de senha conhecida (mesmo que de um usuário de teste)
        | publica a credencial de quem usa o banco, e foi assim que os hashes
        | reais chegaram ao histórico do git.
        */
        $semSenhaUsavel = fn (): string => Hash::make(SenhaForte::gerar());

        $apoiador1 = Apoiador::firstOrCreate(
            ['email' => 'apoiador1@exemplo.org'],
            [
                'nome_completo' => 'AnaDemonstração',
                'senha' => $semSenhaUsavel(),
                'celular' => '81900000001',
                'cpf' => '11144477735',
                'sexo' => 'Feminino',
                'cep' => '53000000',
                'logradouro' => 'Rua de Exemplo',
                'numero' => '100',
                'complemento' => '',
                'bairro' => 'Exemplo',
                'cidade' => 'Recife',
                'estado' => 'PE',
                'tipo_usuario' => 'apoiador',
                'data_cadastro' => '2026-09-11 10:44:24',
            ]
        );

        $apoiador2 = Apoiador::firstOrCreate(
            ['email' => 'apoiador2@exemplo.org'],
            [
                'nome_completo' => 'Bruno Demonstração',
                'senha' => $semSenhaUsavel(),
                'celular' => '81900000002',
                'cpf' => '52998224725',
                'sexo' => 'Masculino',
                'cep' => '53000000',
                'logradouro' => 'Rua de Exemplo',
                'numero' => '200',
                'complemento' => '',
                'bairro' => 'Exemplo',
                'cidade' => 'Recife',
                'estado' => 'PE',
                'tipo_usuario' => 'apoiador',
                'data_cadastro' => '2026-09-11 10:58:27',
            ]
        );

        $admin = Apoiador::firstOrCreate(
            ['email' => 'gestor@exemplo.org'],
            [
                'nome_completo' => 'Gestão da ONG',
                'senha' => $semSenhaUsavel(),
                'celular' => '',
                'cpf' => '',
                'sexo' => '',
                'cep' => '',
                'logradouro' => '',
                'numero' => '',
                'complemento' => null,
                'bairro' => '',
                'cidade' => '',
                'estado' => '',
                'tipo_usuario' => 'admin',
                'data_cadastro' => '2026-09-15 16:42:47',
            ]
        );

        $ben10 = Crianca::firstOrCreate(
            ['nome' => 'Ben Tennyson (Ben 10)'],
            [
                'data_nascimento' => '2013-12-27',
                'historico' => 'Menino muito enérgico que adora descobertas e aventuras cósmicas. Precisa de apoio para seus projetos de ciências e materiais pedagógicos criativos.',
                'imagem_perfil' => 'ben10.jpg',
                'status' => 'disponivel',
                'data_cadastro' => '2026-09-14 18:35:58',
            ]
        );

        $bart = Crianca::firstOrCreate(
            ['nome' => 'Bart Simpson'],
            [
                'data_nascimento' => '2015-04-01',
                'historico' => 'Conhecido por suas travessuras na escola, mas tem um talento artístico incrível para o skate e grafite. Busca apoio para canalizar toda sua energia em oficinas de arte e reforço escolar.',
                'imagem_perfil' => 'bart.jpg',
                'status' => 'disponivel',
                'data_cadastro' => '2026-09-14 18:35:58',
            ]
        );

        $chaves = Crianca::firstOrCreate(
            ['nome' => 'Chaves'],
            [
                'data_nascimento' => '2014-08-15',
                'historico' => 'Morador da vila mais famosa da TV, muito carismático e sonhador. Participa das atividades recreativas e busca apoio alimentar e educacional para o seu desenvolvimento.',
                'imagem_perfil' => 'chaves.jpg',
                'status' => 'disponivel',
                'data_cadastro' => '2026-09-14 18:35:58',
            ]
        );

        Apadrinhamento::firstOrCreate(
            ['apoiador_id' => $apoiador1->id, 'crianca_id' => $ben10->id],
            [
                'valor_mensal' => 100.00,
                'data_inicio' => '2026-09-14 18:53:06',
                'status' => 'ativo',
            ]
        );

        RecompensaApadrinhamento::firstOrCreate(
            ['apadrinhamento_id' => 1, 'titulo' => 'tá na hora de virar herói'],
            [
                'mensagem' => 'E aí, beleza? Aqui é o Ben Tennyson. Fiquei sabendo que você é um grande fã das minhas aventuras... Continue sendo esse fã incrível, respeitando seus pais, estudando bastante e ajudando quem precisa. Um grande abraço do seu amigo, Ben 10',
                'arquivo_midia' => 'recompensa_1789499207.png',
                'data_envio' => '2026-09-15 16:06:47',
            ]
        );

        DoacaoMensal::firstOrCreate(
            ['apoiador_id' => $apoiador2->id, 'valor_mensal' => 20.00],
            [
                'dia_vencimento' => 20,
                'metodo_pagamento' => 'Pix Automático',
                'status' => 'ativo',
                'data_assinatura' => '2026-09-12 21:07:10',
            ]
        );

        DoacaoMensal::firstOrCreate(
            ['apoiador_id' => $apoiador2->id, 'valor_mensal' => 80.00],
            [
                'dia_vencimento' => 20,
                'metodo_pagamento' => 'Pix Automático',
                'status' => 'ativo',
                'data_assinatura' => '2026-09-12 21:18:15',
            ]
        );

        DoacaoMensal::firstOrCreate(
            ['apoiador_id' => $apoiador1->id, 'valor_mensal' => 40.00],
            [
                'dia_vencimento' => 20,
                'metodo_pagamento' => 'Cartão de Crédito (Recorrente)',
                'status' => 'ativo',
                'data_assinatura' => '2026-09-14 18:47:58',
            ]
        );

        DoacaoUnica::firstOrCreate(
            ['apoiador_id' => $apoiador2->id, 'valor' => 75.00, 'data_doacao' => '2026-09-11 11:25:35'],
            ['metodo_pagamento' => 'Pix', 'status' => 'concluido']
        );

        DoacaoUnica::firstOrCreate(
            ['apoiador_id' => $apoiador2->id, 'valor' => 10.00, 'data_doacao' => '2026-09-11 11:26:30'],
            ['metodo_pagamento' => 'Pix', 'status' => 'concluido']
        );

        DoacaoUnica::firstOrCreate(
            ['apoiador_id' => $apoiador1->id, 'valor' => 10.00, 'data_doacao' => '2026-09-14 18:47:46'],
            ['metodo_pagamento' => 'Pix', 'status' => 'concluido']
        );

        ProgramaAcao::firstOrCreate(
            ['titulo' => 'Lute como uma Mãe Atípica'],
            [
                'resumo' => 'Projeto oferece rede de apoio e empreendedorismo para mães de crianças neurodivergentes',
                'texto_completo' => 'Com foco na saúde mental e na independência financeira, iniciativa da ONG SOS Tudo pelo Social transforma a realidade de mulheres que dedicam suas vidas ao cuidado de filhos com necessidades específicas, promovendo acolhimento e capacitação. A iniciativa oferece apoio psicológico, rodas de diálogo e oficinas de empreendedorismo para as mães atípicas.',
                'categoria' => 'Assistência Social',
                'imagem_capa' => 'materia_1789498189.jpg',
                'status' => 'ativo',
                'data_criacao' => '2026-09-15 18:00:00',
            ]
        );

        Voluntario::firstOrCreate(
            ['apoiador_id' => $apoiador2->id],
            [
                'area_atuacao' => 'Outros',
                'disponibilidade' => 'Manhã',
                'arquivo_curriculo' => '',
                'status' => 'em_analise',
                'data_entrevista' => null,
                'mensagem_entrevista' => 'por favor, tente de novo e envie seu currículo',
                'data_inscricao' => '2026-09-14 18:14:11',
            ]
        );

        Voluntario::firstOrCreate(
            ['apoiador_id' => $apoiador1->id],
            [
                'area_atuacao' => 'Outros',
                'disponibilidade' => 'Manhã',
                'arquivo_curriculo' => '',
                'status' => 'aprovado',
                'data_entrevista' => '2026-10-27 14:00:00',
                'mensagem_entrevista' => 'esperando você',
                'data_inscricao' => '2026-09-14 18:48:24',
            ]
        );

        Galeria::firstOrCreate(
            ['nome_imagem' => 'logo.png'],
            ['legenda' => 'Logo da ONG', 'data_upload' => '2026-09-15 16:00:00']
        );

        $this->command?->info('Contas de demonstração criadas (senha inutilizável por segurança):');
        $this->command?->line('  php artisan gestor:senha '.$admin->email.'   # acesso da gestão');
        $this->command?->line('  php artisan apoiador:senha '.$apoiador1->email);
        $this->command?->line('  php artisan apoiador:senha '.$apoiador2->email);
    }
}
