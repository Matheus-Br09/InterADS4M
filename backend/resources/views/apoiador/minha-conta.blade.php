@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto my-8 p-6 bg-white rounded shadow">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Olá, {{ $apoiador->nome_completo }}</h2>
            <p class="text-sm text-gray-500">
                CPF: {{ $apoiador->cpf }} | E-mail: {{ $apoiador->email }}
                @if($apoiador->celular)
                    | Celular: {{ $apoiador->celular }}
                @endif
            </p>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="text-red-600 hover:text-red-800 font-semibold text-sm transition-colors">
                Sair
            </button>
        </form>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Doações Únicas -->
        <div class="border p-4 rounded bg-gray-50">
            <div class="flex justify-between items-center mb-3">
                <h3 class="font-bold text-lg text-gray-700">Doações Únicas</h3>
                <a href="{{ route('apoio-unico.show') }}" class="text-sm text-blue-600 hover:underline font-semibold">+ Nova Doação</a>
            </div>
            @forelse($doacoesUnicas as $doacao)
                <div class="text-sm border-b py-2 flex justify-between">
                    <span>R$ {{ number_format($doacao->valor, 2, ',', '.') }} ({{ strtoupper($doacao->metodo_pagamento) }})</span>
                    <span class="text-green-600 font-semibold">{{ ucfirst($doacao->status) }}</span>
                </div>
            @empty
                <p class="text-gray-500 text-sm py-2">Nenhuma doação pontual registrada.</p>
            @endforelse
        </div>

        <!-- Apadrinhamentos -->
        <div class="border p-4 rounded bg-gray-50">
            <h3 class="font-bold text-lg text-gray-700 mb-3">Apadrinhamentos</h3>
            @forelse($apadrinhamentos as $apadrinhamento)
                <div class="text-sm border-b py-2">
                    <p class="font-medium">Plano / Criança ID: {{ $apadrinhamento->crianca_id ?? 'Geral' }}</p>
                    <p class="text-gray-500 text-xs">Status: {{ $apadrinhamento->status ?? 'Ativo' }}</p>
                </div>
            @empty
                <p class="text-gray-500 text-sm py-2">Nenhum apadrinhamento ativo no momento.</p>
            @endforelse
        </div>

        <!-- Doações Mensais -->
        <div class="border p-4 rounded bg-gray-50">
            <h3 class="font-bold text-lg text-gray-700 mb-3">Doações Mensais</h3>
            @forelse($doacoesMensais as $mensal)
                <div class="text-sm border-b py-2 flex justify-between">
                    <span>R$ {{ number_format($mensal->valor, 2, ',', '.') }}/mês</span>
                    <span class="text-blue-600 font-semibold">{{ ucfirst($mensal->status) }}</span>
                </div>
            @empty
                <p class="text-gray-500 text-sm py-2">Nenhuma assinatura de doação mensal.</p>
            @endforelse
        </div>

        <!-- Voluntariado -->
        <div class="border p-4 rounded bg-gray-50">
            <h3 class="font-bold text-lg text-gray-700 mb-3">Voluntariado</h3>
            @if($voluntario)
                <div class="text-sm py-2">
                    <p><span class="font-medium">Área de Atuação:</span> {{ $voluntario->area_interesse ?? 'Inscrito' }}</p>
                    <p><span class="font-medium">Status:</span> {{ $voluntario->status ?? 'Ativo' }}</p>
                </div>
            @else
                <p class="text-gray-500 text-sm py-2">Você ainda não está inscrito como voluntário.</p>
            @endif
        </div>
    </div>
</div>
@endsection