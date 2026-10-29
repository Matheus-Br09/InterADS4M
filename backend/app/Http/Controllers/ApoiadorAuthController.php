<?php

namespace App\Http\Controllers;

use App\Support\DadosDaConta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

/*
| No MVP só a gestão da ONG entra no sistema (pelo painel, via API). Apoiador
| comum não tem mais cadastro nem área própria: mesmo que uma conta antiga
| acerte a senha, o login é recusado sem abrir sessão.
*/
class ApoiadorAuthController extends Controller
{
    /*
    | O limite por IP (10/min, em AppServiceProvider) não segura um ataque
    | distribuído nem um robô que varre e-mails: o ManyToMany põe a ONG inteira
    | atrás do mesmo IP, então um IP só ganha o limite de todo mundo junto.
    | O bloqueio abaixo é por e-mail e IP; não é global por conta.
    */
    private const TENTATIVAS_POR_CONTA = 5;

    private const BLOQUEIO_EM_SEGUNDOS = 300;

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'senha' => 'required|string',
        ]);

        $chave = $this->chaveDeBloqueio($credentials['email'], $request);

        if (RateLimiter::tooManyAttempts($chave, self::TENTATIVAS_POR_CONTA)) {
            $segundos = RateLimiter::availableIn($chave);

            return response()->json(['message' => 'Muitas tentativas de acesso. Tente novamente mais tarde.', 'tentar_em' => $segundos], 429)->header('Retry-After', (string) $segundos);
        }

        $guard = Auth::guard('apoiador');

        // validate() confere a senha sem abrir sessão: a sessão só nasce depois
        // de saber que a conta é da gestão.
        if (! $guard->validate(['email' => $credentials['email'], 'password' => $credentials['senha']])) {
            RateLimiter::hit($chave, self::BLOQUEIO_EM_SEGUNDOS);

            return response()->json(['message' => 'E-mail ou senha inválidos.'], 401);
        }

        $usuario = $guard->getLastAttempted();

        if ($usuario->tipo_usuario !== 'admin') {
            return response()->json(['message' => 'Acesso restrito à gestão da ONG.'], 403);
        }

        // Zera o contador: quem acerta a senha não deve herdar o bloqueio
        // das tentativas anteriores, e uma conta real não fica presa por
        // causa de um robô que errou o e-mail algumas vezes.
        RateLimiter::clear($chave);

        // O attempt() faria isto sozinho; com o validate() é preciso pedir:
        // hash antigo (custo menor) é refeito no primeiro login bem-sucedido.
        if (config('hashing.rehash_on_login', true)) {
            $guard->getProvider()->rehashPasswordIfRequired($usuario, ['password' => $credentials['senha']]);
        }

        $guard->login($usuario);
        $request->session()->regenerate();

        return response()->json(['dados' => DadosDaConta::de($usuario)]);
    }

    /*
    | A chave usa o e-mail *digitado*, não o da conta encontrada: assim um
    | e-mail inexente trava igual a um existente e o ataque não consegue
    | distinguir os dois pela resposta. O IP entra na chave para o atacante não
    | conseguir trancar de propósito a conta de um gestor conhecido.
    */
    private function chaveDeBloqueio(string $email, Request $request): string
    {
        return 'login-conta:'.mb_strtolower(trim($email)).'|'.$request->ip();
    }

    public function logout(Request $request)
    {
        Auth::guard('apoiador')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
