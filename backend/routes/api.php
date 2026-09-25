<?php

use App\Http\Controllers\DashboardTesteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas de API
|--------------------------------------------------------------------------
|
| Estas rotas alimentam o site em inter-ong/ e ficam em routes/api.php de
| propósito: o grupo 'api' não usa cookie de sessão nem exige token de CSRF,
| então o site consegue ler as respostas e enviar a newsletter de outra
| origem. A leitura é pública; o que exige gestão fica em routes/web.php.
|
*/

Route::get('/criancas', [DashboardTesteController::class, 'apiCriancas']);
Route::get('/apoiadores', [DashboardTesteController::class, 'apiApoiadores']);
Route::get('/programas', [DashboardTesteController::class, 'apiProgramas']);
Route::get('/apadrinhamentos', [DashboardTesteController::class, 'apiApadrinhamentos']);
Route::get('/noticias', [DashboardTesteController::class, 'apiNoticias']);
Route::get('/materiais-didaticos', [DashboardTesteController::class, 'apiMateriaisDidaticos']);
Route::get('/transparencia', [DashboardTesteController::class, 'apiTransparencia']);
Route::post('/newsletter', [DashboardTesteController::class, 'storeNewsletter']);
