<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar senha - Portal da ONG</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans text-gray-900 min-h-screen flex flex-col justify-between">

    <header class="bg-white shadow">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-blue-600"><a href="{{ url('/') }}">Portal da ONG</a></h1>
            <nav class="space-x-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-red-600 hover:text-red-800 font-semibold text-sm transition-colors">
                        Sair
                    </button>
                </form>
            </nav>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white p-6 rounded-lg shadow-md border">

            @if ($apoiador->trocar_senha_obrigatorio)
                <h2 class="text-2xl font-bold mb-2 text-gray-800 text-center">Crie a sua senha</h2>

                <div class="mb-4 p-3 bg-blue-50 border border-blue-300 text-blue-900 rounded text-sm">
                    Por segurança, a senha que a ONG entregou vale só para este
                    primeiro acesso. Crie uma senha sua para continuar: a outra
                    deixa de funcionar assim que você trocar.
                </div>
            @else
                <h2 class="text-2xl font-bold mb-6 text-gray-800 text-center">Alterar minha senha</h2>
            @endif

            @if (session('aviso'))
                <div class="mb-4 p-3 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded text-sm">
                    {{ session('aviso') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('senha.update') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700">Nova senha</label>
                    <input type="password" name="senha" required minlength="8" autocomplete="new-password"
                           class="mt-1 w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Repita a nova senha</label>
                    <input type="password" name="senha_confirmation" required minlength="8" autocomplete="new-password"
                           class="mt-1 w-full p-2 border rounded-md focus:ring-blue-500 focus:border-blue-500">
                </div>

                <p class="text-xs text-gray-600">
                    A senha precisa ter no mínimo 8 caracteres, com letra maiúscula,
                    letra minúscula e número.
                </p>

                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 font-semibold transition-colors">
                    Gravar nova senha
                </button>
            </form>

            @unless ($apoiador->trocar_senha_obrigatorio)
                <p class="mt-4 text-center text-sm text-gray-600">
                    <a href="{{ route('minha-conta') }}" class="text-blue-600 hover:underline">Voltar para minha conta</a>
                </p>
            @endunless
        </div>
    </main>

    <footer class="bg-white border-t py-4 text-center text-xs text-gray-500">
        © {{ date('Y') }} Sistema de Apoio e Doações. Todos os direitos reservados.
    </footer>

</body>
</html>
