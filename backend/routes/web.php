<?php

use App\Http\Controllers\DashboardTesteController;
use App\Http\Controllers\ApoiadorAuthController;
use App\Http\Controllers\DoacaoUnicaController;
use App\Http\Controllers\MinhaContaController;
use Illuminate\Support\Facades\Route;

// ==========================================
// PAINEL PROVISÓRIO DE TESTES
// ==========================================
Route::get('/', [DashboardTesteController::class, 'index'])->name('dashboard');
Route::post('/seed-dados', [DashboardTesteController::class, 'seedData'])->name('seed.data');
Route::post('/criancas/salvar', [DashboardTesteController::class, 'storeCrianca'])->name('criancas.store');

// Rotas de API (JSON)
Route::prefix('api')->group(function () {
    Route::get('/criancas', [DashboardTesteController::class, 'apiCriancas']);
    Route::get('/apoiadores', [DashboardTesteController::class, 'apiApoiadores']);
    Route::get('/programas', [DashboardTesteController::class, 'apiProgramas']);
    Route::get('/apadrinhamentos', [DashboardTesteController::class, 'apiApadrinhamentos']);
    Route::get('/noticias', [DashboardTesteController::class, 'apiNoticias']);
    Route::get('/materiais-didaticos', [DashboardTesteController::class, 'apiMateriaisDidaticos']);
    Route::get('/transparencia', [DashboardTesteController::class, 'apiTransparencia']);
    Route::post('/newsletter', [DashboardTesteController::class, 'storeNewsletter']);
});

// ==========================================
// AUTENTICAÇÃO E ÁREA DO APOIADOR
// ==========================================
Route::get('/cadastro', [ApoiadorAuthController::class, 'showRegister'])->name('register');
Route::post('/cadastro', [ApoiadorAuthController::class, 'register']);

Route::get('/entrar', [ApoiadorAuthController::class, 'showLogin'])->name('login');
Route::post('/entrar', [ApoiadorAuthController::class, 'login']);
Route::post('/sair', [ApoiadorAuthController::class, 'logout'])->name('logout');

// Área Restrita (Exige login do Apoiador)
Route::middleware('auth:apoiador')->group(function () {
    Route::get('/minha-conta', [MinhaContaController::class, 'index'])->name('minha-conta');
    Route::get('/apoio-unico', [DoacaoUnicaController::class, 'show'])->name('apoio-unico.show');
    Route::post('/apoio-unico', [DoacaoUnicaController::class, 'store'])->name('apoio-unico.store');
});