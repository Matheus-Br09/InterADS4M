<?php

namespace App\Console\Commands;

use App\Models\Apoiador;
use Illuminate\Console\Command;

/**
 * Registra que a rotação de senha já foi feita, sem trocar senha nenhuma.
 *
 * Existe por causa da ordem das coisas: a rotação para fechar a exposição dos
 * hashes no histórico do git acontece de verdade no banco do cliente, e a
 * coluna `senha_alterada_em` só existe a partir de 26/09. Quem rotacionou as
 * contas antes disso tem a senha nova entregue às pessoas, mas a data não ficou
 * gravada. Rodar este comando carimba a data sem gerar senha nova — o que
 * obrigaria a reentregar senha a quem já recebeu a sua.
 */
class MarcaSenhaApoiadores extends Command
{
    protected $signature = 'apoiadores:marcar-senha
                            {email? : E-mail de uma conta}
                            {--todos : marca todas as contas que ainda nao tem data}
                            {--reforcar : sobrescreve a data de quem ja tem}';

    protected $description = 'Grava a data de rotacao sem trocar a senha (--todos, ou um e-mail)';

    public function handle(): int
    {
        $agora = now();

        if ($this->option('todos') || ! $this->argument('email')) {
            return $this->marcarTodas($agora);
        }

        $email = mb_strtolower(trim((string) $this->argument('email')));
        $apoiador = Apoiador::whereRaw('LOWER(email) = ?', [$email])->first();

        if (! $apoiador) {
            $this->error("Nenhum apoiador encontrado com o e-mail {$email}.");

            return self::FAILURE;
        }

        if ($apoiador->senha_alterada_em && ! $this->option('reforcar')) {
            $this->info("A conta {$apoiador->email} ja tem data de rotacao ({$apoiador->senha_alterada_em->format('d/m/Y H:i')}). Use --reforcar para sobrescrever.");

            return self::SUCCESS;
        }

        $apoiador->forceFill(['senha_alterada_em' => $agora])->save();

        $this->info("Rotacao registrada para {$apoiador->email} em ".$agora->format('d/m/Y H:i').'.');
        $this->line('A senha desta conta nao foi tocada.');

        return self::SUCCESS;
    }

    private function marcarTodas($agora): int
    {
        $query = Apoiador::query();

        // Sem --reforcar, só as contas sem data: sobrescrever a data de quem já
        // tem quebraria o histórico de quem rotacionou quando.
        if (! $this->option('reforcar')) {
            $query->whereNull('senha_alterada_em');
        }

        $marcadas = (clone $query)->get(['id', 'email', 'senha_alterada_em']);

        if ($marcadas->isEmpty()) {
            $this->info('Nenhuma conta sem data de rotacao. Use --reforcar se quiser sobrescrever as datas.');

            return self::SUCCESS;
        }

        $ids = $marcadas->pluck('id');
        Apoiador::whereIn('id', $ids)->update(['senha_alterada_em' => $agora]);

        $this->info(count($ids).' conta(s) marcada(s) em '.$agora->format('d/m/Y H:i').':');
        $this->newLine();

        foreach ($marcadas as $apoiador) {
            $this->line('  '.$apoiador->email);
        }

        $this->newLine();
        $this->line('Nenhuma senha foi trocada. Confira com: <info>php artisan apoiadores:listar</info>');

        return self::SUCCESS;
    }
}
