<?php

namespace App\Http\Controllers;

use App\Models\Apoiador;
use App\Support\CriaGestor;
use App\Support\EncerraSessoesDoApoiador;
use App\Support\SenhaForte;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class GestaoGestoresController extends Controller
{
    public function index(): array
    {
        return ['dados' => Apoiador::where('tipo_usuario', 'admin')
            ->orderBy('nome_completo')
            ->get(['id', 'nome_completo', 'email'])
            ->map(fn (Apoiador $g) => $this->dados($g))
            ->all()];
    }

    public function store(Request $request): JsonResponse
    {
        $validado = $request->validate([
            'nome_completo' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
        ]);

        if (CriaGestor::emailJaExiste($validado['email'])) {
            throw ValidationException::withMessages(['email' => 'Já existe uma conta com este e-mail.']);
        }

        [$gestor, $senha] = CriaGestor::criar($validado['nome_completo'], $validado['email']);

        return response()->json(['dados' => $this->dados($gestor), 'senha_temporaria' => $senha], 201);
    }

    public function destroy(Request $request, int $id, EncerraSessoesDoApoiador $encerraSessoes): Response|JsonResponse
    {
        $alvo = Apoiador::where('tipo_usuario', 'admin')->findOrFail($id);

        if ($alvo->is($request->user('apoiador'))) {
            return response()->json([
                'status' => 'erro',
                'mensagem' => 'Você não pode desativar o seu próprio acesso.',
            ], 422);
        }

        DB::transaction(function () use ($alvo) {
            $alvo->forceFill([
                'tipo_usuario' => 'apoiador',
                'senha' => Hash::make(SenhaForte::gerar(32)),
                'senha_alterada_em' => now(),
            ])->save();
        });

        $encerraSessoes->encerrar($alvo);

        return response()->noContent();
    }

    private function dados(Apoiador $gestor): array
    {
        return ['id' => $gestor->id, 'nome_completo' => $gestor->nome_completo, 'email' => $gestor->email];
    }
}
