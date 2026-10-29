<?php

namespace App\Support;

use App\Models\Apoiador;
use Illuminate\Support\Facades\Hash;

class CriaGestor
{
    public static function emailJaExiste(string $email): bool
    {
        return Apoiador::whereRaw('LOWER(email) = ?', [mb_strtolower(trim($email))])->exists();
    }

    /**
     * Cria a conta de gestor (sem CPF) com senha gerada e troca obrigatória no 1º acesso.
     *
     * @return array{0: Apoiador, 1: string} a conta e a senha temporária, em texto
     */
    public static function criar(string $nome, string $email): array
    {
        $senha = SenhaForte::gerar();

        $gestor = Apoiador::create([
            'nome_completo' => trim($nome),
            'email' => mb_strtolower(trim($email)),
            'senha' => Hash::make($senha),
            'senha_alterada_em' => now(),
            'trocar_senha_obrigatorio' => true,
            'tipo_usuario' => 'admin',
            'data_cadastro' => now()->toDateString(),
        ]);

        return [$gestor, $senha];
    }
}
