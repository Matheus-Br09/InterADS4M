<?php

namespace App\Support;

use Illuminate\Support\Str;
use InvalidArgumentException;

// Gera o "Pix copia e cola" (BR Code estático) conforme o Manual do BR Code do Banco Central.
class PixBrCode
{
    public static function gerar(string $chave, string $nome, string $cidade, ?string $valor, string $txid): string
    {
        if ($txid !== '***' && ! preg_match('/^[A-Za-z0-9]{1,25}$/', $txid)) {
            throw new InvalidArgumentException('txid deve ter de 1 a 25 caracteres alfanuméricos (ou ***).');
        }

        $nome = mb_substr(Str::ascii($nome), 0, 25);
        $cidade = mb_substr(Str::ascii($cidade), 0, 15);

        $contaPix = self::campo('00', 'br.gov.bcb.pix').self::campo('01', $chave);

        $payload = self::campo('00', '01')
            .self::campo('26', $contaPix)
            .self::campo('52', '0000')
            .self::campo('53', '986')
            .($valor !== null ? self::campo('54', $valor) : '')
            .self::campo('58', 'BR')
            .self::campo('59', $nome)
            .self::campo('60', $cidade)
            .self::campo('62', self::campo('05', $txid))
            .'6304';

        return $payload.self::crc16($payload);
    }

    // Campo EMV: id (2 dígitos) + tamanho (2 dígitos) + valor.
    private static function campo(string $id, string $valor): string
    {
        return $id.str_pad((string) strlen($valor), 2, '0', STR_PAD_LEFT).$valor;
    }

    // CRC16-CCITT-FALSE: polinômio 0x1021, início 0xFFFF, sem reflexão e sem XOR final.
    private static function crc16(string $texto): string
    {
        $crc = 0xFFFF;

        foreach (str_split($texto) as $caractere) {
            $crc ^= ord($caractere) << 8;

            for ($i = 0; $i < 8; $i++) {
                $crc = ($crc & 0x8000) ? (($crc << 1) ^ 0x1021) : ($crc << 1);
                $crc &= 0xFFFF;
            }
        }

        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }
}
