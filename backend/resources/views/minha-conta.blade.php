<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Conta - Portal da ONG</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans text-gray-900 min-h-screen flex flex-col justify-between">

    <header class="bg-white shadow">
        <div class="max-w-6xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold text-blue-600"><a href="{{ url('/') }}">Portal da ONG</a></h1>
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-gray-700">Olá, {{ Auth::guard('apoiador')->user()->nome_completo }}</span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-sm bg-red-500 text-white px-3 py-1.5 rounded hover:bg-red-600 font-medium transition-colors">
                        Sair
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8 flex-grow w-full">
        <div class="bg-white p-6 rounded-lg shadow-md border mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Painel do Apoiador</h2>
            <p class="text-gray-600">Bem-vindo ao seu painel. Aqui você pode visualizar suas doações, apadrinhamentos e gerenciar seus dados cadastrais.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-md border">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Seus Dados</h3>
                <p class="text-sm text-gray-600"><strong>E-mail:</strong> {{ Auth::guard('apoiador')->user()->email }}</p>
                <p class="text-sm text-gray-600"><strong>CPF:</strong> {{ Auth::guard('apoiador')->user()->cpf }}</p>
                <p class="text-sm text-gray-600"><strong>Celular:</strong> {{ Auth::guard('apoiador')->user()->celular ?? 'Não informado' }}</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md border">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Ações Rápidas</h3>
                <div class="space-y-2 mt-4">
                    <a href="{{ route('apoio-unico.show') }}" class="block text-center bg-blue-600 text-white py-2 rounded hover:bg-blue-700 font-medium transition-colors">
                        Fazer Doação Única
                    </a>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t py-4 text-center text-xs text-gray-500">
        © {{ date('Y') }} Sistema de Apoio e Doações. Todos os direitos reservados.
    </footer>

</body>
</html>