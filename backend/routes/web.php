<?php

use App\Http\Controllers\DashboardTesteController;
use Illuminate\Support\Facades\Route;

// Painel Visual Provisorio de Testes
Route::get('/', [DashboardTesteController::class, 'index'])->name('dashboard');
Route::post('/seed-dados', [DashboardTesteController::class, 'seedData'])->name('seed.data');
Route::post('/criancas/salvar', [DashboardTesteController::class, 'storeCrianca'])->name('criancas.store');

// Rotas de API (JSON) para testar no navegador ou Postman
Route::prefix('api')->group(function () {
    Route::get('/criancas', [DashboardTesteController::class, 'apiCriancas']);
    Route::get('/apoiadores', [DashboardTesteController::class, 'apiApoiadores']);
    Route::get('/programas', [DashboardTesteController::class, 'apiProgramas']);
    Route::get('/apadrinhamentos', [DashboardTesteController::class, 'apiApadrinhamentos']);
});
