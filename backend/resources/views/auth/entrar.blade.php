@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-8 p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Entrar na Minha Conta</h2>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ url('/entrar') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block font-medium mb-1 text-gray-700">E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="w-full border rounded p-2 focus:outline-none focus:border-blue-500">
        </div>

        <div class="mb-4">
            <label class="block font-medium mb-1 text-gray-700">Senha</label>
            <input type="password" name="senha" required class="w-full border rounded p-2 focus:outline-none focus:border-blue-500">
        </div>

        <div class="mb-6 flex items-center">
            <input type="checkbox" name="remember" id="remember" class="mr-2">
            <label for="remember" class="text-sm text-gray-600">Lembrar-me</label>
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded font-semibold hover:bg-blue-700 transition duration-200">
            Entrar
        </button>
    </form>

    <p class="mt-4 text-center text-sm text-gray-600">
        Ainda não tem conta? <a href="{{ url('/cadastro') }}" class="text-blue-600 hover:underline">Cadastre-se</a>
    </p>
</div>
@endsection