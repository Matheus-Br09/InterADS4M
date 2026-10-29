<?php

namespace App\Console\Commands;

use App\Support\CriaGestor;
use Illuminate\Console\Command;

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

        if (CriaGestor::emailJaExiste($email)) {
            $this->error("Já existe uma conta com o e-mail {$email}.");
            $this->line('Para dar acesso de gestor a uma conta que já existe, use <info>php artisan gestor:senha</info>.');

            return self::FAILURE;
        }

        [, $senha] = CriaGestor::criar($nome, $email);

        $this->info("Gestor {$nome} criado.");
        $this->newLine();
        $this->line("Senha gerada: <info>{$senha}</info>");
        $this->line('Anote agora: ela não será mostrada de novo. No primeiro acesso o sistema pede para trocá-la.');
        $this->newLine();
        $this->line('Entre em <info>/gestao/entrar</info> com este e-mail para acessar o painel.');

        return self::SUCCESS;
    }
}
