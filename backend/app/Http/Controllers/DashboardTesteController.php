<?php

namespace App\Http\Controllers;

use App\Models\Apadrinhamento;
use App\Models\Apoiador;
use App\Models\Crianca;
use App\Models\DoacaoMensal;
use App\Models\DoacaoUnica;
use App\Models\DocumentoTransparencia;
use App\Models\MaterialDidatico;
use App\Models\Newsletter;
use App\Models\Noticia;
use App\Models\ProgramaAcao;
use App\Models\RecompensaApadrinhamento;
use App\Models\Voluntario;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DashboardTesteController extends Controller
{
    public function index()
    {
        $dbConnected = true;
        $dbError = null;

        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $dbConnected = false;
            $dbError = $e->getMessage();
        }

        $stats = [
            'apoiadores' => $dbConnected ? Apoiador::count() : 0,
            'criancas' => $dbConnected ? Crianca::count() : 0,
            'apadrinhamentos' => $dbConnected ? Apadrinhamento::where('status', 'ativo')->count() : 0,
            'programas' => $dbConnected ? ProgramaAcao::count() : 0,
            'voluntarios' => $dbConnected ? Voluntario::count() : 0,
            'doacoes_mensais' => $dbConnected ? DoacaoMensal::count() : 0,
            'doacoes_unicas' => $dbConnected ? DoacaoUnica::count() : 0,
        ];

        $criancas = $dbConnected ? Crianca::with('apadrinhamentos.apoiador')->latest('id')->take(10)->get() : collect();
        $apoiadores = $dbConnected ? Apoiador::with('voluntario', 'doacoesMensais')->latest('id')->take(10)->get() : collect();
        $programas = $dbConnected ? ProgramaAcao::latest('id')->take(10)->get() : collect();
        $apadrinhamentos = $dbConnected ? Apadrinhamento::with('apoiador', 'crianca', 'recompensas')->latest('id')->take(10)->get() : collect();

        return view('welcome', compact('dbConnected', 'dbError', 'stats', 'criancas', 'apoiadores', 'programas', 'apadrinhamentos'));
    }

    public function seedData()
    {
        Artisan::call('db:seed', ['--force' => true]);

        return redirect('/')->with('success', 'Dados de teste gerados com sucesso no MySQL do Docker!');
    }

    public function storeCrianca(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:150',
            'data_nascimento' => 'required|date',
            'historico' => 'nullable|string',
            'status' => 'required|in:disponivel,apadrinhada',
        ]);

        Crianca::create([
            'nome' => $validated['nome'],
            'data_nascimento' => $validated['data_nascimento'],
            'historico' => $validated['historico'] ?? 'Cadastro de teste via painel.',
            'imagem_perfil' => 'perfil_padrao.jpg',
            'status' => $validated['status'],
            'data_cadastro' => now(),
        ]);

        return redirect('/')->with('success', 'Criança cadastrada com sucesso no banco MySQL!');
    }

    // API JSON Endpoints
    //
    // Estas rotas são públicas (alimentam o site), então respondem com o que
    // o site precisa mostrar e nada mais: sem nome, contato, endereço, valor
    // de doação ou dado sensível de apoiador e de criança. As listas são
    // montadas campo a campo de propósito - devolver o model inteiro faria
    // qualquer coluna nova vazar sem querer.
    public function apiCriancas()
    {
        $dados = Crianca::with('apadrinhamentos')->get()->map(fn (Crianca $crianca) => [
            'nome' => $crianca->nome,
            // O status guardado no banco só muda quando alguém mexe no painel,
            // então o site recebe o estado real (apadrinhamento ativo) para não
            // mostrar "disponível" para uma criança que já tem padrinheiro.
            'status' => $crianca->apadrinhamentos->contains('status', 'ativo') ? 'apadrinhada' : $crianca->status,
            'imagem_perfil' => $crianca->imagem_perfil,
            // A idade serve para o site; a data de nascimento, não.
            'idade' => $crianca->data_nascimento ? Carbon::parse($crianca->data_nascimento)->age : null,
            'apadrinhada' => $crianca->apadrinhamentos->contains('status', 'ativo'),
        ]);

        return response()->json([
            'status' => 'success',
            'total' => Crianca::count(),
            'dados' => $dados,
        ]);
    }

    public function apiApoiadores()
    {
        // Apoiador é pessoa física: a API pública devolve só os números da
        // transparência, nunca a lista com nome, endereço ou valores pagos.
        return response()->json([
            'status' => 'success',
            'total' => Apoiador::count(),
            'doadores_mensais' => DoacaoMensal::where('status', 'ativo')->distinct('apoiador_id')->count('apoiador_id'),
            'doadores_unicos' => DoacaoUnica::distinct('apoiador_id')->count('apoiador_id'),
            'total_mensal' => (float) DoacaoMensal::where('status', 'ativo')->sum('valor_mensal'),
            'total_unico' => (float) DoacaoUnica::sum('valor'),
        ]);
    }

    public function apiProgramas()
    {
        return response()->json([
            'status' => 'success',
            'total' => ProgramaAcao::count(),
            'dados' => ProgramaAcao::all(),
        ]);
    }

    public function apiApadrinhamentos()
    {
        // Quem apadrinha e quanto paga não é público. Fica a criança
        // apadrinhada, o status do apadrinhamento e as recompensas enviadas.
        $dados = Apadrinhamento::with('crianca', 'recompensas')->get()->map(fn (Apadrinhamento $apadrinhamento) => [
            'status' => $apadrinhamento->status,
            'data_inicio' => $apadrinhamento->data_inicio,
            'crianca' => [
                'nome' => $apadrinhamento->crianca->nome,
                'status' => $apadrinhamento->crianca->status,
                'imagem_perfil' => $apadrinhamento->crianca->imagem_perfil,
            ],
            'recompensas' => $apadrinhamento->recompensas->map(fn (RecompensaApadrinhamento $recompensa) => [
                'titulo' => $recompensa->titulo,
                'arquivo_midia' => $recompensa->arquivo_midia,
            ]),
        ]);

        return response()->json([
            'status' => 'success',
            'total' => Apadrinhamento::count(),
            'dados' => $dados,
        ]);
    }

    public function apiNoticias()
    {
        return response()->json([
            'status' => 'success',
            'total' => Noticia::count(),
            'dados' => Noticia::latest('id')->get(),
        ]);
    }

    public function apiMateriaisDidaticos()
    {
        return response()->json([
            'status' => 'success',
            'total' => MaterialDidatico::count(),
            'dados' => MaterialDidatico::latest('id')->get(),
        ]);
    }

    public function apiTransparencia()
    {
        return response()->json([
            'status' => 'success',
            'total' => DocumentoTransparencia::count(),
            'dados' => DocumentoTransparencia::latest('ano_referencia')->get(),
        ]);
    }

    public function storeNewsletter(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:newsletter,email',
            'nome' => 'nullable|string|max:100',
        ]);

        $lead = Newsletter::create([
            'email' => $validated['email'],
            'nome' => $validated['nome'] ?? null,
            'data_inscricao' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'mensagem' => 'E-mail cadastrado com sucesso na newsletter!',
            'dados' => $lead,
        ], 201);
    }
}
