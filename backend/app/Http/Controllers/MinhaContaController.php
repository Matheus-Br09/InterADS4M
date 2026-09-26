<?php

namespace App\Http\Controllers;

use App\Rules\SenhaForte;
use App\Support\EncerraSessoesDoApoiador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class MinhaContaController extends Controller
{
    public function index()
    {
        $apoiador = Auth::guard('apoiador')->user();

        // Carrega as relações apenas se as tabelas existirem no banco
        $doacoesUnicas = Schema::hasTable('doacoes_unicas') ? $apoiador->doacoesUnicas : collect();
        $doacoesMensais = Schema::hasTable('doacoes_mensais') ? $apoiador->doacoesMensais : collect();
        $apadrinhamentos = Schema::hasTable('apadrinhamentos') ? $apoiador->apadrinhamentos : collect();
        $voluntario = Schema::hasTable('voluntarios') ? $apoiador->voluntario : null;

        return view('apoiador.minha-conta', compact(
            'apoiador',
            'doacoesUnicas',
            'doacoesMensais',
            'apadrinhamentos',
            'voluntario'
        ));
    }

    public function editSenha()
    {
        return view('apoiador.senha', ['apoiador' => Auth::guard('apoiador')->user()]);
    }

    public function updateSenha(Request $request, EncerraSessoesDoApoiador $sessoes)
    {
        $apoiador = Auth::guard('apoiador')->user();

        $credentials = $request->validate([
            'senha' => ['required', 'string', 'confirmed', new SenhaForte],
            'senha_atual' => $request->is('api/v1/*') && ! $apoiador->trocar_senha_obrigatorio ? ['required', 'string', 'current_password:apoiador'] : ['nullable', 'string'],
        ]);

        /*
        * Aceitar a mesma senha seria uma troca que não troca: a pessoa pode só
        * colar de volta a senha que a ONG entregou, que é justamente a senha
        * que chegou por WhatsApp. Sem esta trava o pedido de "senha nova" passa
        * no cadastro e a senha entregue continua valendo.
        */
        if (Hash::check($credentials['senha'], $apoiador->senha)) {
            if ($request->is('api/v1/*')) {
                throw \Illuminate\Validation\ValidationException::withMessages(['senha' => 'A nova senha precisa ser diferente da senha atual.']);
            }
            return back()
                ->withErrors(['senha' => 'A nova senha precisa ser diferente da senha que você está usando.']);
        }

        $apoiador->forceFill([
            'senha' => Hash::make($credentials['senha']),
            'senha_alterada_em' => now(),
            'trocar_senha_obrigatorio' => false,
        ])->save();

        $sessoes->encerrar($apoiador);
        $request->session()->forget('_old_input');
        $request->session()->regenerate(true);

        if ($request->is('api/v1/*')) {
            return response()->json(['message' => 'Senha alterada com sucesso.']);
        }

        return redirect()
            ->route('minha-conta')
            ->with('success', 'Senha alterada com sucesso.');
    }
}
