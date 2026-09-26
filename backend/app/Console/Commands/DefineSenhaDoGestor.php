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
                            {senha? : Nova senha (mínimo 8, com maiúscula, minúscula e número). Se omitir, gera uma senha forte}';

    protected $description = 'Define a senha de um apoiador e garante acesso de gestor da ONG';

    public function handle(): int
    {
        $email = mb_strtolower(trim((string) $this->argument('email')));
        $gerada = ! $this->argument('senha');
        $senha = $gerada ? SenhaForte::gerar() : (string) $this->argument('senha');

        // Rotação é a hora boa para exigir força: é o comando que o gestor roda
        // na mão, e aceitar "123456" aqui desmente a regra do cadastro público.
        // Só a senha digitada é conferida; a gerada já sai com uma de cada
        // classe (ver App\Support\SenhaForte).
        if (! $gerada && ! SenhaForte::temForcaSuficiente($senha)) {
            $this->error('A senha precisa ter pelo menos 8 caracteres, com maiúscula, minúscula e número.');
            $this->line('Para não escolher nada, rode o comando sem o segundo argumento: a senha é gerada e mostrada aqui.');

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
            'senha_alterada_em' => now(),
            'tipo_usuario' => 'admin',
        ])->save();

        $this->info("Senha de {$apoiador->nome_completo} atualizada em ".now()->format('d/m/Y H:i').'.');
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
