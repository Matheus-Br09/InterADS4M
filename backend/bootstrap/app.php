<?php

use App\Http\Middleware\CabecalhosDeSeguranca;
use App\Http\Middleware\EhGestor;
use App\Http\Middleware\TrocaSenhaObrigatoria;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Hospedagem compartilhada atrás de proxy: sem isso o Laravel não enxerga o HTTPS.
        $middleware->trustProxies(at: '*');
        $middleware->append(CabecalhosDeSeguranca::class);

        $middleware->alias([
            'gestor' => EhGestor::class,
            'troca-senha' => TrocaSenhaObrigatoria::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontFlash(['senha', 'senha_confirmation', 'senha_atual', 'token']);

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
