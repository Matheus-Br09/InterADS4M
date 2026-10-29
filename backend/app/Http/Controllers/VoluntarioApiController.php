<?php

namespace App\Http\Controllers;

use App\Mail\NovoVoluntario;
use App\Models\Voluntario;
use App\Support\AvisaGestao;
use App\Support\BuscaLike;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class VoluntarioApiController extends Controller
{
    public function store(Request $request): array
    {
        $dados = $request->validate([
            'nome_completo' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'celular' => ['required', 'string', 'max:20'],
            'data_nascimento' => ['required', 'date', 'before_or_equal:'.now()->subYears(18)->toDateString()],
            'area_atuacao' => ['required', 'in:Neuropedagogia,Odontologia,Nutrição,Fisioterapia,Apoio Geral,Outros'],
            'disponibilidade' => ['required', 'in:Manhã,Tarde,Integral'],
            'arquivo_curriculo' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'aceite_privacidade' => ['accepted'],
        ], [
            'data_nascimento.before_or_equal' => 'É preciso ter 18 anos ou mais para ser voluntário.',
        ]);

        // O aceite só autoriza o envio: não é uma coluna da tabela.
        unset($dados['aceite_privacidade']);

        $dados['apoiador_id'] = null;

        if ($request->hasFile('arquivo_curriculo')) {
            $dados['arquivo_curriculo'] = $request->file('arquivo_curriculo')->store('curriculos', 'local');
        }

        $dados['status'] = 'em_analise';
        $dados['data_inscricao'] = now();

        $voluntario = Voluntario::create($dados);

        AvisaGestao::enviar(new NovoVoluntario($voluntario));

        // A rota é pública: a resposta não devolve o cadastro (data de
        // nascimento, caminho do currículo), só a confirmação.
        return ['message' => 'Inscrição recebida com sucesso! Entraremos em contato em breve.'];
    }

    public function index(Request $request): array
    {
        $filtros = $request->validate(['busca' => ['nullable', 'string', 'max:100']]);

        $consulta = Voluntario::with('apoiador:id,nome_completo,email')->latest('id');

        // Cadastros antigos guardam o nome no apoiador ligado. O e-mail do apoiador
        // fica de fora: o modelo o esconde da API, então achar por ele seria um resultado sem explicação.
        if ($padrao = BuscaLike::padrao($filtros['busca'] ?? null)) {
            $consulta->where(fn ($q) => $q
                ->whereRaw(BuscaLike::condicao('nome_completo'), [$padrao])
                ->orWhereRaw(BuscaLike::condicao('email'), [$padrao])
                ->orWhereHas('apoiador', fn ($a) => $a->whereRaw(BuscaLike::condicao('nome_completo'), [$padrao])));
        }

        return ['dados' => $consulta->paginate(20)];
    }

    public function update(Request $request, int $id): array
    {
        $voluntario = Voluntario::findOrFail($id);
        $voluntario->update($request->validate(['status' => ['required', 'in:em_analise,entrevista_marcada,aprovado,recusado'], 'data_entrevista' => ['nullable', 'date'], 'mensagem_entrevista' => ['nullable', 'string']]));

        return ['dados' => $voluntario->refresh()];
    }

    public function destroy(int $id): Response
    {
        $voluntario = Voluntario::findOrFail($id);

        if ($voluntario->arquivo_curriculo) {
            Storage::disk('local')->delete($voluntario->arquivo_curriculo);
        }

        $voluntario->delete();

        return response()->noContent();
    }

    public function curriculo(int $id)
    {
        $voluntario = Voluntario::findOrFail($id);
        abort_unless($voluntario->arquivo_curriculo && Storage::disk('local')->exists($voluntario->arquivo_curriculo), 404);

        return response()->download(Storage::disk('local')->path($voluntario->arquivo_curriculo));
    }
}
