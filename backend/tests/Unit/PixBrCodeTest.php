<?php

namespace Tests\Unit;

use App\Support\PixBrCode;
use PHPUnit\Framework\TestCase;

class PixBrCodeTest extends TestCase
{
    public function test_gera_o_exemplo_oficial_do_banco_central(): void
    {
        $this->assertSame(
            '00020126580014br.gov.bcb.pix0136123e4567-e12b-12d1-a456-4266554400005204000053039865802BR5913Fulano de Tal6008BRASILIA62070503***63041D3D',
            PixBrCode::gerar('123e4567-e12b-12d1-a456-426655440000', 'Fulano de Tal', 'BRASILIA', null, '***')
        );
    }

    public function test_inclui_valor_e_txid(): void
    {
        $codigo = PixBrCode::gerar('12345678000199', 'SOS Tudo', 'Paulista', '50.00', 'SOSABC123');

        $this->assertStringContainsString('540550.00', $codigo);
        $this->assertStringContainsString('0509SOSABC123', $codigo);
        $this->assertMatchesRegularExpression('/6304[0-9A-F]{4}$/', $codigo);
    }

    public function test_remove_acentos_e_corta_nome_e_cidade(): void
    {
        $codigo = PixBrCode::gerar('12345678000199', 'Associação SOS Tudo pelo Social Paulista', 'Paulista-Pernambuco', null, '***');

        $this->assertStringContainsString('5925Associacao SOS Tudo pelo', $codigo);
        $this->assertStringContainsString('6015Paulista-Pernam', $codigo);
    }
}
