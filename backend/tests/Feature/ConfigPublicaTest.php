<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CriaCenarioOng;
use Tests\TestCase;

class ConfigPublicaTest extends TestCase
{
    use CriaCenarioOng, RefreshDatabase;

    public function test_config_publica_informa_whatsapp_instagram_e_disponibilidade_do_pix(): void
    {
        config([
            'ong.pix_chave' => '12345678000199',
            'ong.pix_nome' => 'SOS Tudo pelo Social',
            'ong.pix_cidade' => 'PAULISTA',
            'ong.whatsapp' => '5581999990000',
            'ong.instagram' => 'https://www.instagram.com/sostudopelosocial/',
        ]);

        $this->getJson('/api/v1/config-publica')
            ->assertOk()
            ->assertJsonPath('dados.pix_disponivel', true)
            ->assertJsonPath('dados.whatsapp', '5581999990000')
            ->assertJsonPath('dados.instagram', 'https://www.instagram.com/sostudopelosocial/');
    }

    public function test_config_publica_nao_expoe_chave_pix_nem_email_da_gestao(): void
    {
        config([
            'ong.pix_chave' => '12345678000199',
            'ong.email_gestao' => 'gestao@ong.example',
        ]);

        $corpo = $this->getJson('/api/v1/config-publica')->assertOk()->getContent();

        $this->assertStringNotContainsString('12345678000199', $corpo);
        $this->assertStringNotContainsString('gestao@ong.example', $corpo);
    }

    public function test_sem_nome_do_recebedor_o_pix_fica_indisponivel(): void
    {
        config(['ong.pix_chave' => '12345678000199', 'ong.pix_nome' => '', 'ong.pix_cidade' => 'PAULISTA']);

        $this->getJson('/api/v1/config-publica')
            ->assertOk()
            ->assertJsonPath('dados.pix_disponivel', false);
    }

    public function test_sem_chave_pix_o_pix_fica_indisponivel(): void
    {
        config(['ong.pix_chave' => '', 'ong.whatsapp' => '']);

        $this->getJson('/api/v1/config-publica')
            ->assertOk()
            ->assertJsonPath('dados.pix_disponivel', false)
            ->assertJsonPath('dados.whatsapp', null);
    }

    public function test_sem_conteudo_publicado_nenhuma_secao_aparece_como_disponivel(): void
    {
        $this->getJson('/api/v1/config-publica')
            ->assertOk()
            ->assertJsonPath('dados.secoes.noticias', false)
            ->assertJsonPath('dados.secoes.programas', false)
            ->assertJsonPath('dados.secoes.materiais', false)
            ->assertJsonPath('dados.secoes.transparencia', false);
    }

    public function test_secao_aparece_quando_tem_conteudo_e_programa_inativo_nao_conta(): void
    {
        $this->criarNoticia();
        $this->criarPrograma(['status' => 'inativo']);

        $this->getJson('/api/v1/config-publica')
            ->assertOk()
            ->assertJsonPath('dados.secoes.noticias', true)
            ->assertJsonPath('dados.secoes.programas', false);

        $this->criarPrograma(['status' => 'ativo']);

        $this->getJson('/api/v1/config-publica')->assertJsonPath('dados.secoes.programas', true);
    }
}
