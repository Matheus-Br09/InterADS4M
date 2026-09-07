<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Testes ONG - Laravel + Docker MySQL</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --bg-main: #0f172a;
            --bg-card: #1e293b;
            --bg-card-hover: #334155;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: #334155;
            --radius: 12px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            min-height: 100vh;
            padding: 24px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--border);
        }

        .title-group h1 {
            font-size: 24px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: -0.5px;
        }

        .title-group p {
            color: var(--text-muted);
            font-size: 14px;
            margin-top: 4px;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-status.error {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: currentColor;
            box-shadow: 0 0 10px currentColor;
        }

        /* Alerts */
        .alert {
            padding: 14px 18px;
            border-radius: var(--radius);
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        /* Metric Grid */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }

        .metric-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            transition: all 0.2s ease;
        }

        .metric-card:hover {
            transform: translateY(-2px);
            border-color: #475569;
        }

        .metric-card .label {
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .metric-card .value {
            font-size: 32px;
            font-weight: 800;
            margin-top: 8px;
            color: #fff;
        }

        /* Action Buttons */
        .actions-bar {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 32px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
        }

        .btn-secondary {
            background: var(--bg-card);
            color: var(--text-main);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: var(--bg-card-hover);
        }

        /* Layout Grid */
        .main-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        @media (max-width: 900px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
            margin-bottom: 24px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-header h2 {
            font-size: 18px;
            font-weight: 700;
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 8px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 12px;
            margin-bottom: 20px;
            overflow-x: auto;
        }

        .tab-btn {
            background: none;
            border: none;
            color: var(--text-muted);
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .tab-btn.active {
            background: rgba(79, 70, 229, 0.2);
            color: #818cf8;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th {
            text-align: left;
            color: var(--text-muted);
            font-weight: 600;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            color: #cbd5e1;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .tag {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
        }

        .tag-success {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
        }

        .tag-warning {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
        }

        .tag-primary {
            background: rgba(79, 70, 229, 0.2);
            color: #818cf8;
        }

        /* Forms */
        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
            color: var(--text-muted);
        }

        input, select, textarea {
            width: 100%;
            padding: 10px 14px;
            background: #0f172a;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: #fff;
            font-size: 14px;
            outline: none;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--primary);
        }

        /* API Endpoint Box */
        .api-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            background: #0f172a;
            border: 1px solid var(--border);
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .api-url {
            font-family: monospace;
            font-size: 13px;
            color: #38bdf8;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Header -->
    <header>
        <div class="title-group">
            <h1>🤝 Painel de Testes ONG SOS</h1>
            <p>Integração Laravel 11 + Docker MySQL 8.0</p>
        </div>
        <div>
            @if($dbConnected)
                <div class="badge-status">
                    <span class="dot"></span> Conectado: MySQL (ong)
                </div>
            @else
                <div class="badge-status error">
                    <span class="dot"></span> Erro de Conexão com o Banco
                </div>
            @endif
        </div>
    </header>

    @if(session('success'))
        <div class="alert alert-success">
            <span>✅ {{ session('success') }}</span>
        </div>
    @endif

    <!-- Cards de Métricas -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="label">Crianças Cadastradas</div>
            <div class="value">{{ $stats['criancas'] }}</div>
        </div>
        <div class="metric-card">
            <div class="label">Apoiadores</div>
            <div class="value">{{ $stats['apoiadores'] }}</div>
        </div>
        <div class="metric-card">
            <div class="label">Apadrinhamentos Ativos</div>
            <div class="value">{{ $stats['apadrinhamentos'] }}</div>
        </div>
        <div class="metric-card">
            <div class="label">Programas / Ações</div>
            <div class="value">{{ $stats['programas'] }}</div>
        </div>
        <div class="metric-card">
            <div class="label">Voluntários</div>
            <div class="value">{{ $stats['voluntarios'] }}</div>
        </div>
    </div>

    <!-- Barra de Ações Rápidas -->
    <div class="actions-bar">
        <form action="{{ route('seed.data') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-primary">
                ⚡ Gerar / Recarregar Dados de Teste
            </button>
        </form>
        <a href="/api/criancas" target="_blank" class="btn btn-secondary">
            🌐 Testar API JSON (Crianças)
        </a>
        <a href="/api/apoiadores" target="_blank" class="btn btn-secondary">
            🌐 Testar API JSON (Apoiadores)
        </a>
    </div>

    <!-- Grade Principal -->
    <div class="main-grid">
        <!-- Visualizador de Tabelas -->
        <div class="card">
            <div class="card-header">
                <h2>📊 Dados no Banco de Dados</h2>
            </div>

            <!-- Abas -->
            <div class="tabs">
                <button class="tab-btn active" onclick="switchTab('tab-criancas', this)">Crianças</button>
                <button class="tab-btn" onclick="switchTab('tab-apoiadores', this)">Apoiadores</button>
                <button class="tab-btn" onclick="switchTab('tab-programas', this)">Programas & Ações</button>
                <button class="tab-btn" onclick="switchTab('tab-apadrinhamentos', this)">Apadrinhamentos</button>
            </div>

            <!-- Tab Crianças -->
            <div id="tab-criancas" class="tab-content active">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Nascimento</th>
                            <th>Status</th>
                            <th>Padrinho / Madrinha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($criancas as $crianca)
                            <tr>
                                <td>#{{ $crianca->id }}</td>
                                <td><strong>{{ $crianca->nome }}</strong></td>
                                <td>{{ \Carbon\Carbon::parse($crianca->data_nascimento)->format('d/m/Y') }}</td>
                                <td>
                                    @if($crianca->status == 'apadrinhada')
                                        <span class="tag tag-success">Apadrinhada</span>
                                    @else
                                        <span class="tag tag-warning">Disponível</span>
                                    @endif
                                </td>
                                <td>
                                    @if($crianca->apadrinhamentos->isNotEmpty())
                                        {{ $crianca->apadrinhamentos->first()->apoiador->nome_completo ?? 'N/A' }}
                                    @else
                                        <span style="color: var(--text-muted);">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 24px;">
                                    Nenhuma criança cadastrada ainda. Clique no botão "Gerar Dados de Teste"!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Tab Apoiadores -->
            <div id="tab-apoiadores" class="tab-content">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome Completo</th>
                            <th>E-mail</th>
                            <th>Cidade / UF</th>
                            <th>É Voluntário?</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($apoiadores as $apoiador)
                            <tr>
                                <td>#{{ $apoiador->id }}</td>
                                <td><strong>{{ $apoiador->nome_completo }}</strong></td>
                                <td>{{ $apoiador->email }}</td>
                                <td>{{ $apoiador->cidade }}/{{ $apoiador->estado }}</td>
                                <td>
                                    @if($apoiador->voluntario)
                                        <span class="tag tag-primary">Sim ({{ $apoiador->voluntario->area_atuacao }})</span>
                                    @else
                                        <span style="color: var(--text-muted);">Não</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 24px;">
                                    Nenhum apoiador cadastrado ainda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Tab Programas -->
            <div id="tab-programas" class="tab-content">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Categoria</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($programas as $programa)
                            <tr>
                                <td>#{{ $programa->id }}</td>
                                <td><strong>{{ $programa->titulo }}</strong></td>
                                <td><span class="tag tag-primary">{{ $programa->categoria }}</span></td>
                                <td><span class="tag tag-success">{{ ucfirst($programa->status) }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 24px;">
                                    Nenhum programa cadastrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Tab Apadrinhamentos -->
            <div id="tab-apadrinhamentos" class="tab-content">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Apoiador</th>
                            <th>Criança</th>
                            <th>Valor Mensal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($apadrinhamentos as $apadrinhamento)
                            <tr>
                                <td>#{{ $apadrinhamento->id }}</td>
                                <td><strong>{{ $apadrinhamento->apoiador->nome_completo ?? 'N/A' }}</strong></td>
                                <td>{{ $apadrinhamento->crianca->nome ?? 'N/A' }}</td>
                                <td>R$ {{ number_format($apadrinhamento->valor_mensal, 2, ',', '.') }}</td>
                                <td><span class="tag tag-success">{{ ucfirst($apadrinhamento->status) }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 24px;">
                                    Nenhum apadrinhamento ativo.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Formulário de Teste de Cadastro & Endpoints -->
        <div>
            <!-- Form Novo Cadastro -->
            <div class="card">
                <div class="card-header">
                    <h2>➕ Testar Inserção de Criança</h2>
                </div>
                <form action="{{ route('criancas.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="nome">Nome Completo</label>
                        <input type="text" id="nome" name="nome" placeholder="Ex: Gabriel Moreira" required>
                    </div>
                    <div class="form-group">
                        <label for="data_nascimento">Data de Nascimento</label>
                        <input type="date" id="data_nascimento" name="data_nascimento" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="disponivel">Disponível para Apadrinhamento</option>
                            <option value="apadrinhada">Já Apadrinhada</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="historico">Histórico / Observação</label>
                        <textarea id="historico" name="historico" rows="2" placeholder="Gosta de desenhar, etc."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        Salvar no MySQL (Docker)
                    </button>
                </form>
            </div>

            <!-- Endpoints de API -->
            <div class="card">
                <div class="card-header">
                    <h2>🔗 Rotas de API (JSON)</h2>
                </div>
                <p style="font-size: 13px; color: var(--text-muted); margin-bottom: 16px;">
                    Clique para testar o retorno JSON das APIs que o frontend consumirá:
                </p>

                <div class="api-item">
                    <span class="api-url">GET /api/criancas</span>
                    <a href="/api/criancas" target="_blank" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">Abrir</a>
                </div>
                <div class="api-item">
                    <span class="api-url">GET /api/apoiadores</span>
                    <a href="/api/apoiadores" target="_blank" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">Abrir</a>
                </div>
                <div class="api-item">
                    <span class="api-url">GET /api/programas</span>
                    <a href="/api/programas" target="_blank" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">Abrir</a>
                </div>
                <div class="api-item">
                    <span class="api-url">GET /api/apadrinhamentos</span>
                    <a href="/api/apadrinhamentos" target="_blank" class="btn btn-secondary" style="padding: 4px 10px; font-size: 12px;">Abrir</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(tabId, btn) {
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));

        document.getElementById(tabId).classList.add('active');
        btn.classList.add('active');
    }
</script>

</body>
</html>
