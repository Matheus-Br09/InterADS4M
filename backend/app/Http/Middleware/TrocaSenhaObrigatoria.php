<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Prende a conta em que a senha foi trocada por um comando até a pessoa criar
 * uma senha própria.
 *
 * A senha entregue pela ONG para a rotação dos hashes expostos no histórico do
 * git é a mesma que vai trafegar por WhatsApp/e-mail. Enquanto ela valer, quem
 * leu a conversa tem a conta. A troca obrigatória faz essa senha morrer no
 * primeiro acesso, que é a única janela em que dá para forçar isso sem ter que
 * resetar a conta de quem já entrou.
 */
class TrocaSenhaObrigatoria
{
    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user('apoiador');

        // Sem sessão ou sem exigência, o middleware não tem nada a dizer: quem
        // não está logado é assunto do middleware 'auth:apoiador'.
        if (! $usuario || ! $usuario->trocar_senha_obrigatorio) {
            return $next($request);
        }

        // As rotas de saída do estado (PUT auth/senha e POST auth/sair) ficam
        // fora do grupo 'troca-senha' em routes/integracao.php, então tudo o que
        // chega aqui com a conta presa é recusado.
        return response()->json(['message' => 'Troque sua senha para continuar.', 'codigo' => 'troca_senha_obrigatoria'], 403);
    }
}
