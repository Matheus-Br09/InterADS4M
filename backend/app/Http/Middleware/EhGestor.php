<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EhGestor
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user('apoiador');

        if (! $usuario || $usuario->tipo_usuario !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'erro',
                    'mensagem' => 'Acesso restrito a gestores da ONG.',
                ], 403);
            }

            if (! $usuario) {
                return redirect()->guest(route('login'))
                    ->with('erro', 'Entre com o seu acesso para usar esta função.');
            }

            return redirect()->route('login')
                ->with('erro', 'Esta função é exclusiva da gestão da ONG.');
        }

        return $next($request);
    }
}
