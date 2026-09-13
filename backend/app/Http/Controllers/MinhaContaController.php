<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class MinhaContaController extends Controller
{
    public function index()
    {
        $apoiador = Auth::guard('apoiador')->user();

        // Carrega as relações apenas se as tabelas existirem no banco
        $doacoesUnicas   = Schema::hasTable('doacoes_unicas') ? $apoiador->doacoesUnicas : collect();
        $doacoesMensais  = Schema::hasTable('doacoes_mensais') ? $apoiador->doacoesMensais : collect();
        $apadrinhamentos = Schema::hasTable('apadrinhamentos') ? $apoiador->apadrinhamentos : collect();
        $voluntario      = Schema::hasTable('voluntarios') ? $apoiador->voluntario : null;

        return view('apoiador.minha-conta', compact(
            'apoiador',
            'doacoesUnicas',
            'doacoesMensais',
            'apadrinhamentos',
            'voluntario'
        ));
    }
}