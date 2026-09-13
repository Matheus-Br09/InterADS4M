<?php

namespace App\Http\Controllers;

use App\Models\Apoiador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ApoiadorAuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.cadastro');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nome_completo' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:apoiadores',
            'cpf' => 'required|string|unique:apoiadores',
            'celular' => 'nullable|string',
            'senha' => 'required|string|min:6|confirmed',
        ]);

        $apoiador = Apoiador::create([
            'nome_completo' => $request->nome_completo,
            'email' => $request->email,
            'cpf' => $request->cpf,
            'celular' => $request->celular,
            'senha' => Hash::make($request->senha),
            'cep' => $request->cep ?? null,
            'logradouro' => $request->logradouro ?? null,
            'numero' => $request->numero ?? null,
            'complemento' => $request->complemento ?? null,
            'bairro' => $request->bairro ?? null,
            'cidade' => $request->cidade ?? null,
            'estado' => $request->estado ?? null,
            'data_cadastro' => now()->toDateString(),
        ]);

        Auth::guard('apoiador')->login($apoiador);

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
            'senha' => 'required',
        ]);

        if (Auth::guard('apoiador')->attempt(['email' => $credentials['email'], 'password' => $credentials['senha']])) {
            $request->session()->regenerate();
            return redirect()->intended('/minha-conta');
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('apoiador')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}