<?php

namespace App\Http\Controllers;

use App\Models\Apoiador;
use App\Models\Crianca;
use App\Models\Apadrinhamento;
use App\Models\DoacaoMensal;
use App\Models\DoacaoUnica;
use App\Models\ProgramaAcao;
use App\Models\Voluntario;
use App\Models\Galeria;
use App\Models\Noticia;
use App\Models\MaterialDidatico;
use App\Models\DocumentoTransparencia;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

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
    public function apiCriancas()
    {
        return response()->json([
            'status' => 'success',
            'total' => Crianca::count(),
            'dados' => Crianca::with('apadrinhamentos.apoiador')->get()
        ]);
    }

    public function apiApoiadores()
    {
        return response()->json([
            'status' => 'success',
            'total' => Apoiador::count(),
            'dados' => Apoiador::with('voluntario', 'doacoesMensais', 'doacoesUnicas')->get()
        ]);
    }

    public function apiProgramas()
    {
        return response()->json([
            'status' => 'success',
            'total' => ProgramaAcao::count(),
            'dados' => ProgramaAcao::all()
        ]);
    }

    public function apiApadrinhamentos()
    {
        return response()->json([
            'status' => 'success',
            'total' => Apadrinhamento::count(),
            'dados' => Apadrinhamento::with('apoiador', 'crianca', 'recompensas')->get()
        ]);
    }

        public function apiNoticias()
    {
        return response()->json([
            'status' => 'success',
            'total' => Noticia::count(),
            'dados' => Noticia::latest('id')->get()
        ]);
    }

    public function apiMateriaisDidaticos()
    {
        return response()->json([
            'status' => 'success',
            'total' => MaterialDidatico::count(),
            'dados' => MaterialDidatico::latest('id')->get()
        ]);
    }

    public function apiTransparencia()
    {
        return response()->json([
            'status' => 'success',
            'total' => DocumentoTransparencia::count(),
            'dados' => DocumentoTransparencia::latest('ano_referencia')->get()
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
            'dados' => $lead
        ], 201);
    }
}
