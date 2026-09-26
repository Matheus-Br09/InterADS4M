<?php

namespace App\Console\Commands;

use App\Models\Apoiador;
use Illuminate\Console\Command;

/**
 * Prende a conta em que a senha foi trocada por um comando até a pessoa criar uma
 * senha própria.
 *
 * Existe por causa da ordem das coisas: a senha que a ONG entrega para a rotação
 * dos hashes expostos no histórico do git é a mesma que vai trafegar por
 * WhatsApp/e-mail. Enquanto essa senha valer, quem leu a conversa tem a conta. A
 * tela de troca só aparece DEPOIS do login, então não há caminho de interface
 * para marcar a conta antes da entrega — sem este comando, a única forma seria
 * mexer no banco na mão.
 */
class ExigirTrocaDeSenha extends Command
{
    protected $signature = 'apoiadores:exigir-troca
                            {email? : E-mail de uma conta}
                            {--todos : marca todas as contas de apoiador}
                            {--desfazer : limpa a exigencia}';

    protected $description = 'Faz o apoiador criar senha nova no primeiro acesso (--todos, ou um e-mail)';

    public function handle(): int
    {
        $exigir = ! $this->option('desfazer');

        if ($this->option('todos') || ! $this->argument('email')) {
            return $this->marcarTodas($exigir);
        }

        $email = mb_strtolower(trim((string) $this->argument('email')));
        $apoiador = Apoiador::whereRaw('LOWER(email) = ?', [$email])->first();

        if (! $apoiador) {
            $this->error("Nenhum apoiador encontrado com o e-mail {$email}.");

            return self::FAILURE;
        }

        $apoiador->forceFill(['trocar_senha_obrigatorio' => $exigir])->save();

        if ($exigir) {
            $this->info("A conta {$apoiador->email} vai ter que criar uma senha nova no proximo acesso.");
            $this->line('A senha atual dela continua valendo ate la.');

            return self::SUCCESS;
        }

        $this->info("Exigencia removida de {$apoiador->email}. Ela entra direto no painel.");

        return self::SUCCESS;
    }

    private function marcarTodas(bool $exigir): int
    {
        /*
        * A gestao fica de fora de propósito. Bloquear a conta do administrador é
        * a forma mais rápida de a ONG ficar sem acesso ao próprio painel, e o
        * comando que marca por e-mail continua disponível caso a conta da gestão
        * realmente precise.
        */
        $query = Apoiador::query()
            ->where(fn ($q) => $q->where('tipo_usuario', '!=', 'admin')->orWhereNull('tipo_usuario'));

        if (! $exigir) {
            $query->where('trocar_senha_obrigatorio', true);
        }

        $contas = (clone $query)->get(['id', 'email', 'trocar_senha_obrigatorio']);

        if ($contas->isEmpty()) {
            $this->info($exigir
                ? 'Nenhuma conta de apoiador para marcar.'
                : 'Nenhuma conta esta com exigencia de troca.');

            return self::SUCCESS;
        }

        Apoiador::whereIn('id', $contas->pluck('id'))
            ->update(['trocar_senha_obrigatorio' => $exigir]);

        $this->info(count($contas).($exigir ? ' conta(s) marcada(s):' : ' conta(s) liberada(s):'));
        $this->newLine();

        foreach ($contas as $conta) {
            $this->line('  '.$conta->email);
        }

        $this->newLine();

        if ($exigir) {
            $this->line('Entregue a senha atual delas por canal privado: ela morre no primeiro acesso.');
            $this->line('A conta da gestao nao foi tocada.');
        }

        $this->line('Confira com: <info>php artisan apoiadores:listar</info> (coluna troca)');

        return self::SUCCESS;
    }
}
