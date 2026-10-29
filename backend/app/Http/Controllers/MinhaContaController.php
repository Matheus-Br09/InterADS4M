<?php

namespace App\Http\Controllers;

use App\Rules\SenhaForte;
use App\Support\EncerraSessoesDoApoiador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MinhaContaController extends Controller
{
    public function updateSenha(Request $request, EncerraSessoesDoApoiador $sessoes)
    {
        $apoiador = Auth::guard('apoiador')->user();

        $credentials = $request->validate([
            'senha' => ['required', 'string', 'confirmed', new SenhaForte],
            'senha_atual' => ! $apoiador->trocar_senha_obrigatorio ? ['required', 'string', 'current_password:apoiador'] : ['nullable', 'string'],
        ]);

        /*
        * Aceitar a mesma senha seria uma troca que não troca: a pessoa pode só
        * colar de volta a senha que a ONG entregou, que é justamente a senha
        * que chegou por WhatsApp. Sem esta trava o pedido de "senha nova" passa
        * no cadastro e a senha entregue continua valendo.
        */
        if (Hash::check($credentials['senha'], $apoiador->senha)) {
            throw ValidationException::withMessages(['senha' => 'A nova senha precisa ser diferente da senha atual.']);
        }

        $apoiador->forceFill([
            'senha' => Hash::make($credentials['senha']),
            'senha_alterada_em' => now(),
            'trocar_senha_obrigatorio' => false,
        ])->save();

        $sessoes->encerrar($apoiador);
        $request->session()->forget('_old_input');
        $request->session()->regenerate(true);

        return response()->json(['message' => 'Senha alterada com sucesso.']);
    }
}
