# 🌱 InterADS4M

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square)
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square)
![React](https://img.shields.io/badge/React-19-61DAFB?style=flat-square)
![Vite](https://img.shields.io/badge/Vite-646CFF?style=flat-square)
![Tailwind](https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?style=flat-square)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=flat-square)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=flat-square)

Plataforma web para a **ONG SOS** — divulgação de ações, angariação de doações e gestão de apoiadores, voluntários e crianças atendidas. Sistema em dois repositórios: API REST e views em **Laravel** + SPA em **React**.

> Projeto colaborativo — Carlos & Matheus. Status: **MVP pronto, em preparação para publicação**.

---

## ✨ Funcionalidades

### Área pública (SPA React)

- 🏠 Landing page institucional, páginas de serviços e política de privacidade
- ❤️ **Doações via Pix** — sem login; o visitante recebe um protocolo e a gestão confirma o pagamento conferindo o extrato
- 🙋 **Cadastro de voluntários** (maiores de 18 anos, currículo opcional)
- 📚 Notícias, materiais educativos e documentos de transparência
- 💬 Link para o WhatsApp da ONG e widget de acessibilidade (fonte e contraste)

### Painel de gestão (`/gestao/entrar`)

- 💰 Doações: confirmar, cancelar e apagar dados do doador (pedido de exclusão)
- 🙋 Voluntários: mudar a situação, baixar currículo e excluir
- 📰 Conteúdos: publicar e excluir notícias, materiais (PDF) e transparência (PDF)
- 👥 Gestores: adicionar e desativar acessos
- Guia para a equipe da ONG: [`docs/guia-painel-gestao.md`](docs/guia-painel-gestao.md)

### Backend (Laravel, API em `/api/v1`)

- Nenhum endpoint público devolve dado pessoal; as rotas de gestão exigem login (401/403 sem ele)
- E-mail para a gestão a cada nova doação ou novo voluntário
- Rotina diária `doacoes:expirar` marca como expiradas as doações pendentes antigas

---

## 🛠️ Stack

| Camada | Tecnologia |
| --- | --- |
| **Backend** | Laravel 13 (PHP 8.3 ou mais novo), só API em `/api/v1` |
| Banco | MySQL em produção; SQLite no desenvolvimento local (padrão do `.env.example`) |
| **Frontend SPA** | React 19 + Vite + React Router |
| Estilo | Tailwind CSS |
| Testes | PHPUnit 12 (backend); `node --test` e ESLint (frontend) |

---

## 📁 Estrutura

```
InterADS4M/
├── backend/             # API Laravel (sem telas Blade; só e-mails em resources/views/mail)
│   ├── app/             # Controllers, Models, comandos artisan
│   ├── database/        # Migrations e seeders de demonstração
│   └── routes/          # web.php (prefixo /api/v1), integracao.php (rotas da API), console.php (agenda)
├── inter-ong/           # SPA React (Vite): site público e painel em /gestao
├── docs/                # Guia do painel e specs/planos
└── relatorio_backend.md # Documentação do progresso (histórico)
```

---

## 🚀 Como rodar (desenvolvimento)

### Backend

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve             # API em http://localhost:8000/api/v1
```

O `.env.example` já usa SQLite. Para criar o primeiro acesso ao painel:

```bash
php artisan gestor:criar "Seu Nome" seu@email.org
```

O comando mostra uma **senha temporária uma única vez**; no primeiro login em `/gestao/entrar` a pessoa é obrigada a criar uma senha própria.

### Frontend SPA

```bash
cd inter-ong
npm install
npm run dev
```

Em desenvolvimento, use a mesma origem por proxy ou defina `VITE_BACKEND_URL` (ex.: `http://localhost:8000`).

### Testes

```bash
cd backend && php artisan test
cd inter-ong && npm test && npm run lint
```

### Comandos artisan que existem

| Comando | Para que serve |
| --- | --- |
| `gestor:criar "Nome" email` | Cria um gestor com senha temporária (troca obrigatória no primeiro login) |
| `doacoes:expirar` | Marca como expiradas as doações pendentes antigas (roda todo dia pelo agendador) |
| `gestor:senha`, `apoiador:senha`, `apoiadores:listar`, `apoiadores:marcar-senha`, `apoiadores:exigir-troca` | Comandos legados de rotação de senha da época do cadastro de apoiadores; o MVP não tem mais cadastro público de apoiador (só gestores têm conta). Veja `php artisan list` |

### Histórico: credenciais expostas no passado

Um dump antigo (`init.sql`) e um seed com dados e senhas reais ficaram versionados num repositório público. Foi corrigido: o dump saiu, os seeds usam e-mails de exemplo e senha inutilizável, e o **histórico do git foi reescrito** (todos os commits têm SHA novo). Quem clonou antes precisa atualizar com `git fetch origin && git reset --hard origin/main`. As contas reais daquela época devem ter a senha trocada. Um teste (`tests/Feature/RotacaoDeSenhaTest.php`) impede que hash de senha ou e-mail de domínio real volte para um seeder.

### API: o que é público e o que exige login

Rotas reais em `backend/routes/integracao.php`, todas sob `/api/v1`:

- **Públicas:** `GET csrf`, `GET conteudos/{noticias|materiais|transparencia|programas}` (e `/{id}`, e os arquivos), `GET estatisticas` (só números agregados), `GET config-publica` (Pix e contatos da ONG), `POST newsletter`, `POST doacoes` (doação via Pix, sem login) e `POST voluntariado` (cadastro de voluntário).
- **Login do gestor:** `POST auth/entrar`, `POST auth/sair`, `GET auth/eu`, `PUT auth/senha`.
- **Só gestor logado** (prefixo `gestao/`): conteúdos (criar, editar, excluir), voluntários (listar, mudar situação, baixar currículo, excluir), doações (listar, confirmar/cancelar, anonimizar) e gestores (listar, criar, desativar). Sem login a resposta é 401/403.

Nenhuma rota pública devolve dado pessoal (voluntário, doador, criança); `tests/Feature/ApiPublicaNaoExpoeDadoPessoalTest.php` varre as respostas para travar isso.

**Limites de requisição** (em `AppServiceProvider`): `api` 120/min, `login` 10/min, `doacao-unica` 10/min, `uploads` 10/min, `gestao` 60/min, `newsletter` 5/min e `troca-senha-post` 10/min. No login, 5 erros para o mesmo e-mail e IP bloqueiam por 5 minutos. Senhas exigem 8+ caracteres com maiúscula, minúscula e número.

---

### Publicando na Hostinger (Fase 2)

Passo a passo para colocar o site no ar. Tudo aqui usa `.env` e comandos; não
precisa mexer em código. Pontos marcados com **(confirmar no painel da Hostinger)**
dependem do plano contratado e só se confirmam na conta real.

#### 1. Antes de começar

- Plano da Hostinger com **PHP 8.3 ou mais novo** (o `backend/composer.json` exige `^8.3`), **MySQL**, **acesso SSH** e **cron**. **(confirmar no painel da Hostinger** se o plano tem SSH e cron.)
- Domínio `.org.br` registrado no CNPJ da ONG, apontado para a hospedagem.
- Chave Pix **confirmada diretamente com a ONG**, antes de colocar o site no ar.
- Um e-mail da ONG criado na Hostinger, para o site enviar os avisos (`MAIL_*`).

#### 2. Como as peças se encaixam (mesmo domínio, recomendado)

- O site (React) fica na raiz do domínio: `/`.
- A API (Laravel) responde em `/api/v1` **no mesmo domínio**. Assim não há problema de CORS e o cookie de login funciona direto.
- O Laravel fica **fora** da pasta pública do site (por exemplo `~/ong/backend`), para ninguém acessar `.env` ou `storage/` pela internet.
- O site não precisa saber o endereço da API: com `VITE_BACKEND_URL` **vazio**, ele chama o mesmo domínio.

#### 3. Construir o site (no seu computador)

```bash
cd inter-ong
npm ci
npm run build        # gera a pasta inter-ong/dist
```

Deixe `VITE_BACKEND_URL` vazio (mesmo domínio). Envie **o conteúdo** de `inter-ong/dist` para a pasta pública do domínio (em geral `public_html`) **(confirmar no painel da Hostinger)**.

#### 4. Enviar o backend

Envie a pasta `backend/` para fora da pasta pública (ex.: `~/ong/backend`), **sem** `.env`, `vendor/`, `node_modules/` nem `storage/` do seu computador. Pode ser por Git no servidor (via SSH) ou pelo gerenciador de arquivos.

> **Nunca copie o banco de dados local nem a pasta `storage/` do computador para o servidor.** O banco local tem dados de pessoas reais de testes antigos. Em produção o banco começa vazio.

#### 5. Ligar o domínio à API (`.htaccess`)

O site é uma SPA: endereços como `/doar` ou `/gestao/entrar` não são arquivos de verdade e precisam cair no `index.html`. E tudo que começa com `/api` precisa ir para o Laravel. A Hostinger costuma usar servidor LiteSpeed, que lê o `.htaccess` do mesmo jeito que o Apache **(confirmar no painel da Hostinger)**.

O Laravel já traz `backend/public/.htaccess` (feito para ele ser a pasta pública inteira). Como aqui a pasta pública é a do site, os dois **não** podem ser o mesmo arquivo. Uma forma que funciona:

1. Copie `backend/public/index.php` para a pasta pública com o nome `laravel.php` e ajuste os **três** caminhos que o arquivo usa: `storage/framework/maintenance.php`, `vendor/autoload.php` e `bootstrap/app.php`, para onde está o backend (ex.: `__DIR__.'/../ong/backend/vendor/autoload.php'`). O número de `../` depende de onde a pasta pública fica em relação ao backend; na Hostinger ela costuma ser `~/domains/<domínio>/public_html` **(confirmar no painel da Hostinger)**, então ajuste os `../` de acordo.
2. Crie um `.htaccess` na pasta pública (junto ao `index.html` do site):

```apache
RewriteEngine On

# Cabeçalhos que o Laravel precisa receber
RewriteCond %{HTTP:Authorization} .
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteCond %{HTTP:x-xsrf-token} .
RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

# Tudo que começa com /api vai para o Laravel
RewriteRule ^api(/.*)?$ laravel.php [L]

# Arquivos reais do site (JS, CSS, imagens) são servidos como são
RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]

# Qualquer outra rota do site cai no index.html (a SPA decide a tela)
RewriteRule ^ index.html [L]
```

Este trecho **não foi testado em uma conta real da Hostinger**: depois de enviar, teste `https://<domínio>/api/v1/csrf` (deve responder JSON) e `https://<domínio>/gestao/entrar` (deve abrir a tela de login). Se algo falhar, ajuste com o suporte da Hostinger.

Alternativa: um subdomínio (`api.<domínio>`) com `backend/public` como pasta raiz dele. Nesse caso `VITE_BACKEND_URL` e `CORS_ALLOWED_ORIGINS` precisam ser preenchidos e o cookie de login exige conferência extra; por isso o mesmo domínio é o recomendado.

#### 6. Banco de dados e `.env` de produção

1. Crie o banco MySQL no painel da Hostinger e anote nome, usuário e senha.
2. No servidor, copie `backend/.env.example` para `backend/.env` e preencha. O bloco **"Produção (Hostinger)"** do próprio `.env.example` lista o que trocar; resumo:

| Chave | O que colocar |
|---|---|
| `APP_ENV`, `APP_DEBUG` | `production` e `false` |
| `APP_URL` | `https://<domínio>` |
| `APP_TIMEZONE` | `America/Recife` |
| `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | `mysql` e os dados do banco criado |
| `LOG_LEVEL` | `warning` |
| `SESSION_SECURE_COOKIE` | `true` (o site usa HTTPS) |
| `CORS_ALLOWED_ORIGINS` | `https://<domínio>` (nunca `*`) |
| `TRUSTED_PROXIES` | deixe **vazio** (veja a nota abaixo) |
| `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_SCHEME`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS` | dados do e-mail da ONG na Hostinger |
| `ONG_PIX_CHAVE`, `ONG_PIX_NOME`, `ONG_PIX_CIDADE` | dados Pix **confirmados com a ONG** |
| `ONG_WHATSAPP`, `ONG_INSTAGRAM`, `ONG_EMAIL_GESTAO` | contatos da ONG (`ONG_EMAIL_GESTAO` recebe os avisos de doação e voluntário) |
| `ONG_DOACAO_EXPIRA_DIAS` | opcional (padrão 7) |

A senha do banco e a do e-mail ficam **só** no `.env` do servidor. Nunca no Git, nunca no chat.

> **`TRUSTED_PROXIES`:** deixe vazio. Só preencha com o IP/faixa do proxy da Hostinger se o site não reconhecer que está em HTTPS ou mostrar o IP errado do visitante. Sintomas: cabeçalho HSTS ausente, cookie seguro não funcionando (todo mundo deslogado), redirecionamentos estranhos. O IP real do proxy: **confirmar no painel da Hostinger** ou com o suporte.

#### 7. Comandos no servidor (via SSH, dentro da pasta do backend)

```bash
cd ~/ong/backend
composer install --no-dev --optimize-autoloader
php artisan key:generate --force   # SÓ na primeira instalação
php artisan migrate --force        # cria as tabelas; NÃO rode seeders
php artisan storage:link           # imagens/PDFs públicos
php artisan config:cache
php artisan route:cache
```

- Currículos de voluntários ficam em disco **privado** (`storage/app/private/curriculos`) e só saem pelo painel. Imagens e PDFs públicos ficam no disco `public` (`storage/app/public`); o `storage:link` cria o atalho `public/storage` dentro do backend. Como aqui a pasta pública é outra, se alguma imagem não abrir crie também um atalho de `<pasta pública>/storage` para `~/ong/backend/storage/app/public` **(confirmar no painel da Hostinger** se o plano permite atalhos).
- Se o `composer` não existir no servidor, rode `composer install --no-dev` no seu computador (mesma versão de PHP, 8.3) e envie a pasta `vendor/`.
- **`key:generate` só uma vez, na primeira instalação.** Em produção ele pede confirmação, por isso o `--force`. Rodar de novo troca a chave da aplicação e invalida sessões e dados criptografados.
- Depois de qualquer mudança no `.env`, rode `php artisan config:cache` de novo.

#### 8. Primeiro gestor

```bash
php artisan gestor:criar "Nome da Pessoa" email@da-ong.org.br
```

O comando mostra uma **senha temporária uma única vez**; anote e entregue por canal privado. Em `https://<domínio>/gestao/entrar` a pessoa entra e é obrigada a criar a própria senha. Os outros gestores são criados pelo próprio painel (aba Gestores). Guia para a equipe: [`docs/guia-painel-gestao.md`](docs/guia-painel-gestao.md).

#### 9. Rotina diária (cron)

No painel da Hostinger, crie uma tarefa cron para rodar **a cada minuto** **(confirmar no painel da Hostinger** o menu e o caminho do PHP):

```
* * * * * cd ~/ong/backend && php artisan schedule:run >> /dev/null 2>&1
```

É ela que dispara `doacoes:expirar` uma vez por dia (marca como expiradas as doações pendentes antigas). Sem o cron, doações velhas ficam "Pendentes" para sempre.

#### 10. SSL

Ative o certificado SSL gratuito do domínio no painel e force HTTPS **(confirmar no painel da Hostinger)**. Só depois confira `SESSION_SECURE_COOKIE=true`; sem HTTPS o login do painel não funciona.

#### 11. Backup

- Ligue o backup automático da Hostinger **(confirmar no painel da Hostinger** a frequência do plano).
- Toda semana, faça um `mysqldump` do banco e uma cópia de `storage/app` (currículos e arquivos enviados) para um lugar seguro fora do servidor. Rode os comandos **de dentro da pasta do backend** (`cd ~/ong/backend`):

```bash
mysqldump -u <usuario> -p <banco> > backup-$(date +%F).sql
tar czf storage-$(date +%F).tar.gz storage/app
```

Esses arquivos têm dados pessoais: guarde com cuidado e não envie ao Git.

#### 12. Checklist antes de anunciar (critérios de pronto, spec §9)

Spec: `docs/superpowers/specs/2026-10-29-mvp-lancamento-design.md`.

- [ ] 1. Visitante doa via Pix sem login e recebe protocolo; a gestão recebe e-mail, vê a doação pendente e marca como confirmada.
- [ ] 2. Maior de 18 se cadastra como voluntário (com e sem currículo); menor de 18 é recusado no site e no servidor; a gestão recebe e-mail e vê o cadastro.
- [ ] 3. Nenhum endereço público devolve dado pessoal; rotas de gestão sem login dão 401/403.
- [ ] 4. Um gestor publica uma notícia e um PDF de transparência sozinho, seguindo o guia.
- [ ] 5. O site funciona no celular; o widget de acessibilidade muda fonte e contraste.
- [ ] 6. Testes passando: `php artisan test` (backend), `npm test` e `npm run lint` (frontend).
- [ ] 7. Chave Pix exibida confirmada pela ONG.
- [ ] 8. Teste com a ONG (critério de aceitação do documento acadêmico) antes do anúncio público.

#### Lembretes

- **`SESSION_SECURE_COOKIE` só com HTTPS.** Ligado em `http://localhost`, o navegador não devolve o cookie e todo mundo fica deslogado, sem mensagem.
- **`CORS_ALLOWED_ORIGINS` é a origem do site, nunca `*` em produção.** Várias origens, separe por vírgula, sem espaços.
- **Nunca copie o banco ou o `storage/` locais para o servidor.**

### Integração do frontend com a API

O cliente React fica em `inter-ong/src/api/backend.js`. A API usa a sessão do Laravel e fica em `/api/v1`; as requisições usam `credentials: include`. Antes de um POST/PUT/PATCH/DELETE, o cliente pede o token em `GET /api/v1/csrf` e o envia no header `X-CSRF-TOKEN` (já feito pelo `backend.js`).

O login do gestor é `POST /api/v1/auth/entrar`. A resposta 403 com `codigo=troca_senha_obrigatoria` indica que a pessoa precisa chamar `PUT /api/v1/auth/senha`; 401 indica sessão ausente ou credencial inválida; 422 traz erros por campo.

---

## 🔄 Status atual

- ✅ MVP de lançamento (Fase 1) pronto: doação via Pix com protocolo, cadastro de voluntários, painel de gestão, WhatsApp, acessibilidade, páginas de serviços e privacidade
- ✅ API pública sem dados pessoais; rotas de gestão protegidas por login
- ✅ Testes automatizados no backend e no frontend
- ⏳ Fase 2: publicar na Hostinger (seção acima), confirmar a chave Pix com a ONG, teste de aceitação com a ONG, incluir prints no guia do painel, revisar textos de serviços e privacidade com a ONG
- 🗑️ Removidos do MVP: login/"minha conta" do apoiador e apadrinhamento

---

## 👥 Colaboradores

- Carlos
- Matheus

---

## 📝 Licença

A combinar.

### Histórico (auditoria de 26/09/2026, antes do MVP)

Texto mantido só como registro; o que vale hoje está nas seções acima. A revogação de sessões pressupõe `SESSION_DRIVER=database`: não troque o driver sem adaptar e validar essa operação. O `relatorio_backend.md` guarda o histórico do progresso do backend.
