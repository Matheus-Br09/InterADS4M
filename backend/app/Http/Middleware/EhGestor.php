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

        // Só existe API: quem não é gestor recebe sempre a resposta em JSON.
        if (! $usuario || $usuario->tipo_usuario !== 'admin') {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Acesso restrito a gestores da ONG.',
            ], 403);
        }

        return $next($request);
    }
}
