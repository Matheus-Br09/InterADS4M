<?php

namespace App\Console\Commands;

use App\Models\Apoiador;
use App\Support\SenhaForte;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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

    public function handle(): int
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

        $apoiador->forceFill(['senha' => Hash::make($senha)])->save();

        // A sessão guardada em `sessions` continua valendo com a senha antiga;
        // derrubar as sessões do próprio apoiador fecha a janela de quem já
        // estiver autenticado. O guard 'apoiador' grava a chave
        // `login_apoiador_<id>` no payload, que o driver de banco grava em
        // base64 - por isso é preciso decodificar para procurar a chave.
        $chave = 'login_apoiador_'.$apoiador->id;
        $derrubadas = 0;

        foreach (DB::table('sessions')->get(['id', 'payload']) as $sessao) {
            $dados = json_decode((string) base64_decode($sessao->payload, true), true);

            if (is_array($dados) && array_key_exists($chave, $dados)) {
                DB::table('sessions')->where('id', $sessao->id)->delete();
                $derrubadas++;
            }
        }

        $this->info("Senha de {$apoiador->nome_completo} redefinida (papel mantido: {$papel}).");
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
