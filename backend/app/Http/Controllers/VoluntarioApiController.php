<?php

namespace App\Http\Controllers;

use App\Mail\NovoVoluntario;
use App\Models\Voluntario;
use App\Support\AvisaGestao;
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
        // Debug: ver o que está vindo
        \Log::info('Voluntario request', [
            'has_file' => $request->hasFile('arquivo_curriculo'),
            'all_keys' => array_keys($request->all()),
            'file_info' => $request->file('arquivo_curriculo') ? 'File exists' : 'No file',
        ]);

        // Verifica se é cadastro anônimo (sem login) ou autenticado
        $isAuthenticated = $request->user('apoiador') !== null;

        if ($isAuthenticated) {
            // Usuário logado: usa dados da conta
            $dados = $request->validate([
                'area_atuacao' => ['required', 'in:Neuropedagogia,Odontologia,Nutrição,Fisioterapia,Apoio Geral,Outros'],
                'disponibilidade' => ['required', 'in:Manhã,Tarde,Integral'],
                'arquivo_curriculo' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            ]);
            $dados['apoiador_id'] = $request->user('apoiador')->id;
        } else {
            // Cadastro anônimo: requer nome, email, celular
            $dados = $request->validate([
                'nome_completo' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'celular' => ['required', 'string', 'max:20'],
                'area_atuacao' => ['required', 'in:Neuropedagogia,Odontologia,Nutrição,Fisioterapia,Apoio Geral,Outros'],
                'disponibilidade' => ['required', 'in:Manhã,Tarde,Integral'],
                'arquivo_curriculo' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            ]);
            $dados['apoiador_id'] = null;
        }

        // Upload do currículo se enviado
        if ($request->hasFile('arquivo_curriculo')) {
            $dados['arquivo_curriculo'] = $request->file('arquivo_curriculo')->store('curriculos', 'local');
        }

        $dados['status'] = 'em_analise';
        $dados['data_inscricao'] = now();

        // Se autenticado, atualiza ou cria baseado no apoiador_id
        // Se anônimo, sempre cria novo registro
        if ($isAuthenticated) {
            $voluntario = Voluntario::updateOrCreate(['apoiador_id' => $dados['apoiador_id']], $dados);
        } else {
            $voluntario = Voluntario::create($dados);
        }

        AvisaGestao::enviar(new NovoVoluntario($voluntario));

        return ['dados' => $voluntario, 'message' => 'Inscrição recebida com sucesso! Entraremos em contato em breve.'];
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
