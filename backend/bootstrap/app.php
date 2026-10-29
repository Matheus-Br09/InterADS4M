<?php

use App\Http\Middleware\CabecalhosDeSeguranca;
use App\Http\Middleware\EhGestor;
use App\Http\Middleware\TrocaSenhaObrigatoria;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Proxies confiáveis vêm de TRUSTED_PROXIES (config/trustedproxy.php); '*' deixaria burlar os limites por IP.
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

        // Mensagens padrão do framework (em inglês) viram português nas respostas JSON.
        // Mensagens próprias (login bloqueado, 401/403 do app) são mantidas.
        $exceptions->render(function (Throwable $e, Request $request) {
            if (! ($request->is('api/*') || $request->expectsJson())) {
                return null;
            }

            $padrao = [
                404 => 'Não encontrado.',
                419 => 'Sua sessão expirou. Recarregue a página e tente de novo.',
                429 => 'Muitas tentativas. Aguarde um pouco e tente de novo.',
                500 => 'Algo deu errado. Tente de novo em instantes.',
            ];
            $cabecalhos = [];

            if ($e instanceof ModelNotFoundException) {
                $status = 404;
            } elseif ($e instanceof TokenMismatchException) {
                $status = 419;
            } elseif ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();
                $cabecalhos = $e->getHeaders();
                // Só troca a mensagem quando ela é a do framework, não uma escrita pelo app.
                $generica = in_array($e->getMessage(), ['', 'Too Many Attempts.', 'CSRF token mismatch.', 'Page Expired', 'Server Error', 'Not Found'], true)
                    || str_starts_with($e->getMessage(), 'The route ')
                    || str_starts_with($e->getMessage(), 'No query results');
                if (! $generica) {
                    return null;
                }
            } elseif (
                $e instanceof ValidationException || $e instanceof AuthenticationException
                || $e instanceof AuthorizationException || $e instanceof HttpResponseException
                || config('app.debug')
            ) {
                return null;
            } else {
                $status = 500;
            }

            if (! isset($padrao[$status])) {
                return null;
            }

            return response()->json(['message' => $padrao[$status]], $status, $cabecalhos);
        });
    })->create();
