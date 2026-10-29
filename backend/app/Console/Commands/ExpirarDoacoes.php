<?php

namespace App\Console\Commands;

use App\Models\DoacaoUnica;
use Illuminate\Console\Command;

class ExpirarDoacoes extends Command
{
    protected $signature = 'doacoes:expirar';

    protected $description = 'Marca como expiradas as doações pendentes antigas';

    public function handle(): int
    {
        $limite = now()->subDays((int) config('ong.doacao_expira_dias'));

        $total = DoacaoUnica::where('status', 'pendente')
            ->where('data_doacao', '<', $limite)
            ->update(['status' => 'expirado']);

        $this->info("{$total} doação(ões) expirada(s).");

        return self::SUCCESS;
    }
}
