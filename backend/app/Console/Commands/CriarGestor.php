<?php

namespace App\Console\Commands;

use App\Models\Apoiador;
use App\Support\SenhaForte;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CriarGestor extends Command
{
    protected $signature = 'gestor:criar
                            {nome : Nome completo do gestor}
                            {email : E-mail de acesso do gestor}';

    protected $description = 'Cria uma conta de gestor da ONG (sem CPF) com senha gerada, para o primeiro acesso ao painel';

    public function handle(): int
    {
        $nome = trim((string) $this->argument('nome'));
        $email = mb_strtolower(trim((string) $this->argument('email')));

        if (Apoiador::whereRaw('LOWER(email) = ?', [$email])->exists()) {
            $this->error("Já existe uma conta com o e-mail {$email}.");
            $this->line('Para dar acesso de gestor a uma conta que já existe, use <info>php artisan gestor:senha</info>.');

            return self::FAILURE;
        }

        $senha = SenhaForte::gerar();

        Apoiador::create([
            'nome_completo' => $nome,
            'email' => $email,
            'senha' => Hash::make($senha),
            'senha_alterada_em' => now(),
            'trocar_senha_obrigatorio' => true,
            'tipo_usuario' => 'admin',
            'data_cadastro' => now()->toDateString(),
        ]);

        $this->info("Gestor {$nome} criado.");
        $this->newLine();
        $this->line("Senha gerada: <info>{$senha}</info>");
        $this->line('Anote agora: ela não será mostrada de novo. No primeiro acesso o sistema pede para trocá-la.');
        $this->newLine();
        $this->line('Entre em <info>/entrar</info> com este e-mail para acessar o painel.');

        return self::SUCCESS;
    }
}
