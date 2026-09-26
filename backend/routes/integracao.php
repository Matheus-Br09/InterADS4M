<?php

use App\Http\Controllers\ApoiadorAuthController;
use App\Http\Controllers\ApoiosApiController;
use App\Http\Controllers\ContaApiController;
use App\Http\Controllers\ConteudoApiController;
use App\Http\Controllers\DashboardTesteController;
use App\Http\Controllers\DoacaoUnicaController;
use App\Http\Controllers\MinhaContaController;
use App\Http\Controllers\SessaoApiController;
use App\Http\Controllers\VoluntarioApiController;
use Illuminate\Support\Facades\Route;

Route::get('csrf', [SessaoApiController::class, 'csrf']);
Route::post('auth/cadastro', [ApoiadorAuthController::class, 'register'])->middleware('throttle:cadastro');
Route::post('auth/entrar', [ApoiadorAuthController::class, 'login'])->middleware('throttle:login');
Route::get('conteudos/{recurso}', [ConteudoApiController::class, 'index'])->whereIn('recurso', ['noticias', 'materiais', 'transparencia', 'programas']);
Route::get('conteudos/{recurso}/{id}', [ConteudoApiController::class, 'show'])->whereNumber('id');
Route::get('conteudos/{recurso}/{id}/arquivos/{campo}', [ConteudoApiController::class, 'arquivo'])->whereNumber('id')->name('api.conteudo.arquivo');
Route::post('newsletter', [DashboardTesteController::class, 'storeNewsletter'])->middleware('throttle:newsletter');
Route::get('estatisticas', [DashboardTesteController::class, 'apiApoiadores']);

Route::middleware('auth:apoiador')->group(function () {
    Route::post('auth/sair', [ApoiadorAuthController::class, 'logout']);
    Route::put('auth/senha', [MinhaContaController::class, 'updateSenha'])->middleware('throttle:troca-senha-post');

    Route::middleware('troca-senha')->group(function () {
        Route::get('auth/eu', [ContaApiController::class, 'perfil']);
        Route::patch('conta', [ContaApiController::class, 'update']);
        Route::get('conta/{tipo}', [ContaApiController::class, 'historico'])->whereIn('tipo', ['doacoes', 'mensalidades', 'apadrinhamentos']);
        Route::post('doacoes', [DoacaoUnicaController::class, 'store'])->middleware('throttle:doacao-unica');
        Route::post('mensalidades', [ApoiosApiController::class, 'mensal'])->middleware('throttle:doacao-unica');
        Route::post('apadrinhamentos', [ApoiosApiController::class, 'apadrinhar'])->middleware('throttle:doacao-unica');
        Route::delete('conta/{tipo}/{id}', [ApoiosApiController::class, 'cancelar'])->whereIn('tipo', ['doacoes', 'mensalidades', 'apadrinhamentos'])->whereNumber('id');
        Route::get('voluntariado', [VoluntarioApiController::class, 'minhaInscricao']);
        Route::post('voluntariado', [VoluntarioApiController::class, 'store'])->middleware('throttle:uploads');

        Route::prefix('gestao')->middleware(['gestor', 'throttle:gestao'])->group(function () {
            Route::get('conteudos/{recurso}', [ConteudoApiController::class, 'index'])->whereIn('recurso', ['noticias', 'materiais', 'transparencia', 'programas']);
            Route::get('conteudos/{recurso}/{id}', [ConteudoApiController::class, 'show'])->whereNumber('id');
            Route::post('conteudos/{recurso}', [ConteudoApiController::class, 'store'])->middleware('throttle:uploads');
            Route::patch('conteudos/{recurso}/{id}', [ConteudoApiController::class, 'update'])->whereNumber('id')->middleware('throttle:uploads');
            Route::delete('conteudos/{recurso}/{id}', [ConteudoApiController::class, 'destroy'])->whereNumber('id');
            Route::get('voluntarios', [VoluntarioApiController::class, 'index']);
            Route::patch('voluntarios/{id}', [VoluntarioApiController::class, 'update'])->whereNumber('id');
            Route::get('voluntarios/{id}/curriculo', [VoluntarioApiController::class, 'curriculo'])->whereNumber('id');
        });
    });
});
