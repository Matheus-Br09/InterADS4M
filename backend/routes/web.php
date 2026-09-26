<?php

use App\Http\Controllers\ApoiadorAuthController;
use App\Http\Controllers\DashboardTesteController;
use App\Http\Controllers\DoacaoUnicaController;
use App\Http\Controllers\MinhaContaController;
use Illuminate\Support\Facades\Route;

// ==========================================
// PAINEL PROVISÓRIO DE TESTES
// ==========================================
// O painel mostra dados pessoais (crianças, apoiadores, valores),
// então o acesso é exclusivo da gestão da ONG. As APIs JSON abaixo
// continuam públicas porque alimentam o site em inter-ong/.
Route::middleware(['gestor', 'troca-senha'])->group(function () {
    Route::get('/', [DashboardTesteController::class, 'index'])->name('dashboard');
    Route::post('/seed-dados', [DashboardTesteController::class, 'seedData'])->name('seed.data');
    Route::post('/criancas/salvar', [DashboardTesteController::class, 'storeCrianca'])->name('criancas.store');
});

// ==========================================
// AUTENTICAÇÃO E ÁREA DO APOIADOR
// ==========================================
// Limites por IP: impedem alguém de ficar tentando senha ou criando
// cadastros em massa (10 tentativas de login e 5 cadastros por minuto).
// Os limites são nomeados no AppServiceProvider.
Route::get('/cadastro', [ApoiadorAuthController::class, 'showRegister'])->name('register');
Route::post('/cadastro', [ApoiadorAuthController::class, 'register'])->middleware('throttle:cadastro');

Route::get('/entrar', [ApoiadorAuthController::class, 'showLogin'])->name('login');
Route::post('/entrar', [ApoiadorAuthController::class, 'login'])->middleware('throttle:login');
Route::post('/sair', [ApoiadorAuthController::class, 'logout'])->name('logout');

// Área Restrita (Exige login do Apoiador)
Route::middleware(['auth:apoiador', 'troca-senha'])->group(function () {
    Route::get('/minha-conta', [MinhaContaController::class, 'index'])->name('minha-conta');
    Route::get('/apoio-unico', [DoacaoUnicaController::class, 'show'])->name('apoio-unico.show');
    Route::post('/apoio-unico', [DoacaoUnicaController::class, 'store'])
        ->middleware('throttle:doacao-unica')
        ->name('apoio-unico.store');
});

// Troca da própria senha. Fica de fora do grupo acima de propósito: enquanto a
// conta está presa na troca obrigatória, estas são as únicas rotas que podem
// responder — se entrassem no 'troca-senha', a conta ficaria sem saída.
Route::middleware('auth:apoiador')->group(function () {
    Route::get('/minha-conta/senha', [MinhaContaController::class, 'editSenha'])->name('senha.edit');
    Route::post('/minha-conta/senha', [MinhaContaController::class, 'updateSenha'])
        ->middleware('throttle:troca-senha-post')
        ->name('senha.update');
});
