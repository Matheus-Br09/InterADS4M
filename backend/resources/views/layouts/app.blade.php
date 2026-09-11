<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Apoiador</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans text-gray-900 min-h-screen flex flex-col justify-between">

    <header class="bg-white shadow">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-blue-600"><a href="{{ url('/') }}">Portal da ONG</a></h1>
            <nav class="space-x-4">
                @if(Auth::guard('apoiador')->check())
                    <a href="{{ route('minha-conta') }}" class="text-gray-700 hover:text-blue-600 font-medium">Minha Conta</a>
                    <a href="{{ route('apoio-unico.show') }}" class="text-gray-700 hover:text-blue-600 font-medium">Fazer Doação</a>
                @else
                    <a href="{{ url('/entrar') }}" class="text-gray-700 hover:text-blue-600 font-medium">Entrar</a>
                    <a href="{{ url('/cadastro') }}" class="bg-blue-600 text-white px-3 py-1.5 rounded hover:bg-blue-700 text-sm font-medium">Cadastrar</a>
                @endif
            </nav>
        </div>
    </header>

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-white border-t mt-8 py-4 text-center text-sm text-gray-500">
        &copy; {{ date('Y') }} Sistema de Apoio e Doações. Todos os direitos reservados.
    </footer>

</body>
</html>