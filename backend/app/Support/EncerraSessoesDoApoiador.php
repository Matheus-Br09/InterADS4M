<?php

namespace App\Support;

use App\Models\Apoiador;
use Illuminate\Session\DatabaseSessionHandler;
use Illuminate\Session\EncryptedStore;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class EncerraSessoesDoApoiador
{
    /**
     * Revoga as sessões do guard apoiador no armazenamento em banco do projeto.
     * O ID do apoiador fica no valor; a chave pertence ao guard, não à pessoa.
     */
    public function encerrar(Apoiador $apoiador): int
    {
        $conexao = DB::connection(config('session.connection'));
        $tabela = config('session.table', 'sessions');
        $handler = new DatabaseSessionHandler($conexao, $tabela, config('session.lifetime', 120));
        $chave = Auth::guard('apoiador')->getName();
        $encerradas = 0;

        foreach ($conexao->table($tabela)->select('id')->lazyById(100) as $registro) {
            $sessao = config('session.encrypt')
                ? new EncryptedStore('revogacao', $handler, Crypt::getFacadeRoot(), $registro->id, config('session.serialization', 'json'))
                : new Store('revogacao', $handler, $registro->id, config('session.serialization', 'json'));
            $sessao->start();

            if ((string) $sessao->get($chave) === (string) $apoiador->getAuthIdentifier()) {
                $encerradas += $conexao->table($tabela)->where('id', $registro->id)->delete();
            }
        }

        return $encerradas;
    }
}
