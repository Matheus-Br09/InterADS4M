<?php

namespace App\Console\Commands;

use App\Models\Apoiador;
use App\Support\SenhaForte;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class DefineSenhaDoGestor extends Command
{
    protected $signature = 'gestor:senha
                            {email : E-mail do apoiador que será gestor}
                            {senha? : Nova senha (mínimo 6 caracteres). Se omitir, gera uma senha forte}';

    protected $description = 'Define a senha de um apoiador e garante acesso de gestor da ONG';

    public function handle(): int
    {
        $email = mb_strtolower(trim((string) $this->argument('email')));
        $gerada = ! $this->argument('senha');
        $senha = $gerada ? SenhaForte::gerar() : (string) $this->argument('senha');

        if (mb_strlen($senha) < 6) {
            $this->error('A senha precisa ter pelo menos 6 caracteres.');

            return self::FAILURE;
        }

        $apoiador = Apoiador::whereRaw('LOWER(email) = ?', [$email])->first();

        if (! $apoiador) {
            $this->error("Nenhum apoiador encontrado com o e-mail {$email}.");
            $this->line('Rode <info>php artisan db:seed</info> para criar os dados de exemplo da ONG.');

            return self::FAILURE;
        }

        $jaEraGestor = $apoiador->tipo_usuario === 'admin';

        $apoiador->forceFill([
            'senha' => Hash::make($senha),
            'tipo_usuario' => 'admin',
        ])->save();

        $this->info("Senha de {$apoiador->nome_completo} atualizada.");
        $this->line($jaEraGestor
            ? 'A conta já era de gestor.'
            : 'A conta foi promovida a gestor da ONG.');

        if ($gerada) {
            $this->newLine();
            $this->line("Senha gerada: <info>{$senha}</info>");
        }

        $this->newLine();
        $this->line('Entre em <info>/entrar</info> com este e-mail para acessar o painel.');

        return self::SUCCESS;
    }
}
