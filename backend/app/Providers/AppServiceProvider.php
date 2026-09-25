<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Limites por IP. São nomeados de propósito: dois `throttle:` inline na
        // mesma rotaariam a mesma chave no cache e o menor limite valeria duas
        // vezes (o contador é compartilhado), ver testes/Feature/LimiteDeRequisicoesTest.php.
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(120)->by((string) $request->ip()));

        RateLimiter::for('newsletter', fn (Request $request) => Limit::perMinute(5)->by((string) $request->ip()));

        RateLimiter::for('cadastro', fn (Request $request) => Limit::perMinute(5)->by((string) $request->ip()));

        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(10)->by((string) $request->ip()));
    }
}
