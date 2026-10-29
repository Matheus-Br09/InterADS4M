<?php

namespace Tests\Feature;

use Tests\TestCase;

class ConfigPublicaTest extends TestCase
{
    public function test_config_publica_informa_whatsapp_instagram_e_disponibilidade_do_pix(): void
    {
        config([
            'ong.pix_chave' => '12345678000199',
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

    public function test_sem_chave_pix_o_pix_fica_indisponivel(): void
    {
        config(['ong.pix_chave' => '', 'ong.whatsapp' => '']);

        $this->getJson('/api/v1/config-publica')
            ->assertOk()
            ->assertJsonPath('dados.pix_disponivel', false)
            ->assertJsonPath('dados.whatsapp', null);
    }
}
