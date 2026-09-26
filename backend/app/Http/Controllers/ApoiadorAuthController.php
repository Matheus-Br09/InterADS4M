<?php

namespace App\Http\Controllers;

use App\Models\Apoiador;
use App\Rules\Cpf;
use App\Rules\SenhaForte;
use App\Support\DadosDaConta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

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

    public function showRegister()
    {
        return view('auth.cadastro');
    }

    public function register(Request $request)
    {
        $request->merge(['cpf' => Cpf::apenasDigitos($request->cpf)]);

        $validated = $request->validate([
            'nome_completo' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:apoiadores',
            'cpf' => ['required', 'string', new Cpf, 'unique:apoiadores'],
            'celular' => 'nullable|string|max:255',
            'cep' => 'nullable|string|max:255',
            'logradouro' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:255',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:255',
            'senha' => ['required', 'string', 'confirmed', new SenhaForte],
        ]);

        $apoiador = Apoiador::create([
            'nome_completo' => $validated['nome_completo'],
            'email' => $validated['email'],
            'cpf' => $validated['cpf'],
            'celular' => $validated['celular'] ?? null,
            'senha' => Hash::make($validated['senha']),
            'senha_alterada_em' => now(),
            'cep' => $validated['cep'] ?? null,
            'logradouro' => $validated['logradouro'] ?? null,
            'numero' => $validated['numero'] ?? null,
            'complemento' => $validated['complemento'] ?? null,
            'bairro' => $validated['bairro'] ?? null,
            'cidade' => $validated['cidade'] ?? null,
            'estado' => $validated['estado'] ?? null,
            'tipo_usuario' => 'apoiador',
            'data_cadastro' => now()->toDateString(),
        ]);

        Auth::guard('apoiador')->login($apoiador);

        if ($request->is('api/v1/*')) {
            return response()->json(['dados' => DadosDaConta::de($apoiador)], 201);
        }

        return redirect()->route('minha-conta');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'senha' => 'required|string',
        ]);

        $chave = $this->chaveDeBloqueio($credentials['email'], $request);

        if (RateLimiter::tooManyAttempts($chave, self::TENTATIVAS_POR_CONTA)) {
            $segundos = RateLimiter::availableIn($chave);

            if ($request->is('api/v1/*')) {
                return response()->json(['message' => 'Muitas tentativas de acesso. Tente novamente mais tarde.', 'tentar_em' => $segundos], 429)->header('Retry-After', (string) $segundos);
            }

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => "Muitas tentativas de acesso. Tente novamente em {$segundos} segundos."]);
        }

        if (Auth::guard('apoiador')->attempt(['email' => $credentials['email'], 'password' => $credentials['senha']])) {
            // Zera o contador: quem acerta a senha não deve herdar o bloqueio
            // das tentativas anteriores, e uma conta real não fica presa por
            // causa de um robô que errou o e-mail algumas vezes.
            RateLimiter::clear($chave);
            $request->session()->regenerate();

            if ($request->is('api/v1/*')) {
                return response()->json(['dados' => DadosDaConta::de(Auth::guard('apoiador')->user())]);
            }

            // Conta com troca de senha pendente vai direto para ela: cair no
            // painel e ser jogada de volta pela metade só confunde. Quem
            // cadastrou agora nunca entra neste caminho — conta nova nasce com
            // a senha que a pessoa escolheu.
            if (Auth::guard('apoiador')->user()->trocar_senha_obrigatorio) {
                return redirect()->route('senha.edit');
            }

            return redirect()->intended('/minha-conta');
        }

        RateLimiter::hit($chave, self::BLOQUEIO_EM_SEGUNDOS);

        if ($request->is('api/v1/*')) {
            return response()->json(['message' => 'E-mail ou senha inválidos.'], 401);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
            ]);
    }

    /*
    | A chave usa o e-mail *digitado*, não o da conta encontrada: assim um
    | e-mail inexente trava igual a um existente e o ataque não consegue
    | distinguir os dois pela resposta. O IP entra na chave para o atacante não
    | conseguir trancar de propósito a conta de um apoiador conhecido.
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

        if ($request->is('api/v1/*')) {
            return response()->noContent();
        }

        return redirect('/');
    }
}
