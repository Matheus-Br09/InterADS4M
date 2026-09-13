<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Apoiador</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans text-gray-900 min-h-screen flex flex-col justify-between">

    <header class="bg-white shadow">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-blue-600"><a href="{{ url('/') }}">Portal da ONG</a></h1>
            <nav class="space-x-4">
                <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 font-medium">Entrar</a>
                <a href="{{ route('register') }}" class="bg-blue-600 text-white px-3 py-1.5 rounded hover:bg-blue-700">Cadastrar</a>
            </nav>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white p-6 rounded-lg shadow-md border">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Cadastro de Apoiador</h2>

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nome Completo *</label>
                    <input type="text" name="nome_completo" value="{{ old('nome_completo') }}" required class="mt-1 w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">E-mail *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">CPF *</label>
                    <input type="text" name="cpf" value="{{ old('cpf') }}" placeholder="000.000.000-00" required class="mt-1 w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Celular</label>
                    <input type="text" name="celular" value="{{ old('celular') }}" placeholder="(81) 99999-9999" class="mt-1 w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Senha *</label>
                    <input type="password" name="senha" required class="mt-1 w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Confirmar Senha *</label>
                    <input type="password" name="senha_confirmation" required class="mt-1 w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 font-semibold transition-colors">
                    Cadastrar
                </button>
            </form>

            <p class="mt-4 text-center text-sm text-gray-600">
                Já possui uma conta? <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Faça login</a>
            </p>
        </div>
    </main>

    <footer class="bg-white border-t py-4 text-center text-xs text-gray-500">
        © {{ date('Y') }} Sistema de Apoio e Doações. Todos os direitos reservados.
    </footer>

</body>
</html>