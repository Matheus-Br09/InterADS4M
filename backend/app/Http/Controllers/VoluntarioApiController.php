<?php

namespace App\Http\Controllers;

use App\Models\Voluntario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VoluntarioApiController extends Controller
{
    public function minhaInscricao(Request $request): array
    {
        return ['dados' => $request->user('apoiador')->voluntario];
    }

    public function store(Request $request): array
    {
        $dados = $request->validate([
            'area_atuacao' => ['required', 'in:Neuropedagogia,Odontologia,Nutrição,Fisioterapia,Apoio Geral,Outros'],
            'disponibilidade' => ['required', 'in:Manhã,Tarde,Integral'],
            'arquivo_curriculo' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);
        $dados['apoiador_id'] = $request->user('apoiador')->id;
        $dados['arquivo_curriculo'] = $request->file('arquivo_curriculo')->store('curriculos', 'local');
        $dados['status'] = 'em_analise';
        $dados['data_inscricao'] = now();
        $voluntario = Voluntario::updateOrCreate(['apoiador_id' => $dados['apoiador_id']], $dados);
        return ['dados' => $voluntario];
    }

    public function index(): array
    {
        return ['dados' => Voluntario::with('apoiador:id,nome_completo,email')->latest('id')->paginate(20)];
    }

    public function update(Request $request, int $id): array
    {
        $voluntario = Voluntario::findOrFail($id);
        $voluntario->update($request->validate(['status' => ['required', 'in:em_analise,entrevista_marcada,aprovado,recusado'], 'data_entrevista' => ['nullable', 'date'], 'mensagem_entrevista' => ['nullable', 'string']]));
        return ['dados' => $voluntario->refresh()];
    }

    public function curriculo(int $id)
    {
        $voluntario = Voluntario::findOrFail($id);
        abort_unless($voluntario->arquivo_curriculo && Storage::disk('local')->exists($voluntario->arquivo_curriculo), 404);
        return response()->download(Storage::disk('local')->path($voluntario->arquivo_curriculo));
    }
}
