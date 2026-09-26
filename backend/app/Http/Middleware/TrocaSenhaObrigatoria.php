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
    /*
    * Rotas liberadas mesmo com a conta presa. A de troca é a saída do estado
    * (sem ela a conta ficaria sem caminho nenhum) e a de sair evita que a
    * pessoa fiqueobligada a continuar.
    */
    private const ROTAS_LIBERADAS = [
        'senha.edit',
        'senha.update',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $usuario = $request->user('apoiador');

        // Sem sessão ou sem exigência, o middleware não tem nada a dizer: quem
        // não está logado é assunto do middleware 'auth:apoiador'.
        if (! $usuario || ! $usuario->trocar_senha_obrigatorio) {
            return $next($request);
        }

        if (in_array($request->route()?->getName(), self::ROTAS_LIBERADAS, true)) {
            return $next($request);
        }

        return redirect()
            ->route('senha.edit')
            ->with('aviso', 'Crie uma senha nova para continuar usando o sistema.');
    }
}
