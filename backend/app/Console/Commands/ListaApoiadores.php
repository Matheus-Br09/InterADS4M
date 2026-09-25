<?php

namespace App\Console\Commands;

use App\Models\Apoiador;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * Lista as contas do sistema com a situation de cada uma e o comando exato de
 * rotação pronto para copiar.
 *
 * Existe porque a rotação de senha do histórico do git (ver README, "Credenciais
 * expostas") é um trabalho manual, conta por conta, e cada erro de digitação no
 * e-mail é uma conta que continua com a senha antiga. Aqui a lista sai do banco
 * e o comando vem pronto.
 */
class ListaApoiadores extends Command
{
    protected $signature = 'apoiadores:listar {--rotacionar : mostra so as contas que ainda precisam de senha nova}';

    protected $description = 'Lista os apoiadores, a situacao da senha de cada um e o comando para rotacionar';

    /*
    | Senha que estava escrita no seeders e no dump removidos do repositório. O
    | hash dela é público (qualquer pessoa com o histórico antigo consegue
    | conferir), então uma conta que ainda responde a esta senha é uma conta
    | que precisa ser trocada agora. Não é senha de ninguém: é a string que o
    | código usava como exemplo.
    */
    private const SENHA_PUBLICA = 'senha123';

    public function handle(): int
    {
        $contas = Apoiador::orderBy('id')->get(['id', 'nome_completo', 'email', 'tipo_usuario', 'senha']);

        if ($contas->isEmpty()) {
            $this->warn('Nenhum apoiador no banco.');
            $this->line('Rodou <info>php artisan db:seed</info>?');

            return self::SUCCESS;
        }

        $linhas = $contas->map(fn (Apoiador $apoiador) => $this->linha($apoiador));

        if ($this->option('rotacionar')) {
            $linhas = $linhas->filter(fn (array $linha) => $linha['situacao'] !== 'ok');
        }

        if ($linhas->isEmpty()) {
            $this->info($this->option('rotacionar')
                ? 'Nenhuma conta com pendencia de senha.'
                : 'Nenhuma conta encontrada.');

            return self::SUCCESS;
        }

        $this->table(
            ['id', 'nome', 'papel', 'e-mail', 'situacao'],
            $linhas->map(fn (array $linha) => [
                $linha['id'],
                $linha['nome'],
                $linha['papel'],
                $linha['email'],
                $linha['situacao'],
            ])->all(),
        );

        $this->newLine();
        $this->line('Comandos de rotação (um por conta, a senha aparece uma vez só):');
        $this->newLine();

        foreach ($linhas as $linha) {
            $this->line('  <info>'.$linha['comando'].'</info>');
        }

        return self::SUCCESS;
    }

    private function linha(Apoiador $apoiador): array
    {
        // `apoiadores.email` é NOT NULL e unique no schema, então toda conta
        // tem e-mail para digitar: aqui não existe o caso "conta sem e-mail".
        $papel = $apoiador->tipo_usuario ?: 'apoiador';

        // Só procura o hash de quem tem senha; conta sem hash não cai em
        // password_verify e o erro de PDO não é o que o gestor precisa ler.
        $senhaPublica = $apoiador->senha && Hash::check(self::SENHA_PUBLICA, $apoiador->senha);

        return [
            'id' => $apoiador->id,
            'nome' => $apoiador->nome_completo,
            'papel' => $papel,
            'email' => $apoiador->email,
            'situacao' => $senhaPublica ? '<options=bold>senha publica do seed</>' : 'ok',
            'comando' => $papel === 'admin'
                ? "php artisan gestor:senha {$apoiador->email}"
                : "php artisan apoiador:senha {$apoiador->email}",
        ];
    }
}
