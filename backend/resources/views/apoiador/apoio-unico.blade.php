@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-8 p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Fazer Doação Única</h2>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('apoio-unico.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block font-medium mb-1 text-gray-700">Valor da Doação (R$) *</label>
            <input type="number" step="0.01" name="valor" min="5" placeholder="50.00" value="{{ old('valor') }}" required class="w-full border rounded p-2 focus:outline-none focus:border-green-500">
        </div>

        <div class="mb-6">
            <label class="block font-medium mb-1 text-gray-700">Forma de Pagamento *</label>
            <select name="metodo_pagamento" required class="w-full border rounded p-2 focus:outline-none focus:border-green-500">
                <option value="">Selecione...</option>
                <option value="pix" {{ old('metodo_pagamento') == 'pix' ? 'selected' : '' }}>PIX</option>
                <option value="cartao_credito" {{ old('metodo_pagamento') == 'cartao_credito' ? 'selected' : '' }}>Cartão de Crédito</option>
                <option value="boleto" {{ old('metodo_pagamento') == 'boleto' ? 'selected' : '' }}>Boleto Bancário</option>
            </select>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('minha-conta') }}" class="w-1/2 text-center border border-gray-300 text-gray-700 py-2 rounded font-semibold hover:bg-gray-100 transition duration-200">
                Voltar
            </a>
            <button type="submit" class="w-1/2 bg-green-600 text-white py-2 rounded font-semibold hover:bg-green-700 transition duration-200">
                Confirmar
            </button>
        </div>
    </form>
</div>
@endsection