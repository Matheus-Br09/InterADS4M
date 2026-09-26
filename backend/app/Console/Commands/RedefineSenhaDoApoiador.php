<?php

namespace App\Console\Commands;

use App\Models\Apoiador;
use App\Support\EncerraSessoesDoApoiador;
use App\Support\SenhaForte;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * Redefine a senha de um apoiador sem mexer no papel (tipo_usuario) dele.
 *
 * Existe porque o hash de senha de todo apoiador ficou exposto no histórico do
 * repositório (ver README, "Credenciais expostas no histórico do git"): com o
 * repositório aberto, o hash de 12 contas circulou e a rotação precisa de uma
 * ferramenta que não seja o gestor:senha, que promove a conta a admin.
 */
class RedefineSenhaDoApoiador extends Command
{
    protected $signature = 'apoiador:senha
                            {email : E-mail do apoiador}
                            {senha? : Nova senha. Se omitir, gera uma senha forte}';

    protected $description = 'Redefine a senha de um apoiador, mantendo o papel dele (não promove a gestor)';

    public function handle(EncerraSessoesDoApoiador $sessoes): int
    {
        $email = mb_strtolower(trim((string) $this->argument('email')));
        $gerada = ! $this->argument('senha');
        $senha = $gerada ? SenhaForte::gerar() : (string) $this->argument('senha');

        if (! $gerada && ! SenhaForte::temForcaSuficiente($senha)) {
            $this->error('A senha precisa ter pelo menos 8 caracteres, com maiúscula, minúscula e número.');
            $this->line('Para não escolher nada, rode o comando sem o segundo argumento: a senha é gerada e mostrada aqui.');

            return self::FAILURE;
        }

        $apoiador = Apoiador::whereRaw('LOWER(email) = ?', [$email])->first();

        if (! $apoiador) {
            $this->error("Nenhum apoiador encontrado com o e-mail {$email}.");

            return self::FAILURE;
        }

        $papel = $apoiador->tipo_usuario;

        $apoiador->forceFill([
            'senha' => Hash::make($senha),
            'senha_alterada_em' => now(),
        ])->save();

        $derrubadas = $sessoes->encerrar($apoiador);

        $this->info("Senha de {$apoiador->nome_completo} redefinida em ".now()->format('d/m/Y H:i')." (papel mantido: {$papel}).");
        $this->line($derrubadas > 0
            ? "{$derrubadas} sessão(ões) aberta(s) com a senha antiga foram encerradas."
            : 'Nenhuma sessão aberta com a senha antiga.');

        if ($gerada) {
            $this->newLine();
            $this->line("Senha gerada: <info>{$senha}</info>");
        }

        $this->newLine();
        $this->line('Entre em <info>/entrar</info> com este e-mail para acessar a sua conta.');

        return self::SUCCESS;
    }
}
