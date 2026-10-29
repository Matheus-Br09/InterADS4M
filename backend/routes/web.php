<?php

use App\Http\Middleware\RespostaJson;
use Illuminate\Support\Facades\Route;

/*
| O backend do MVP é só a API em /api/v1: o site público (inter-ong/) e o painel
| da gestão conversam com ela. Não há mais telas Blade (painel provisório,
| cadastro, login e área do apoiador saíram).
*/
Route::prefix('api/v1')->middleware([RespostaJson::class, 'throttle:api'])->group(__DIR__.'/integracao.php');
