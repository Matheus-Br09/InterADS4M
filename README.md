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
| **Backend** | Laravel 13 (PHP 8.4.1+) |
| Banco | MySQL 8.0 (via Docker) + Eloquent |
| **Frontend SPA** | React 19 + Vite + React Router |
| Estilo | Tailwind CSS |
| Assets backend | Vite + Blade templates |
| Testes | PHPUnit 12 |

---

## 💡 Núcleo de dados da ONG

15 modelos Eloquent, incluindo: `Apadrinhamento`, `Apoiador`, `Crianca`, `DoacaoMensal`, `DoacaoUnica`, `DocumentoTransparencia`, `MaterialDidatico`, `Newsletter`, `Noticia`, `ProgramaAcao`, `RecompensaApadrinhamento`, `Voluntario`.

---

## 📁 Estrutura

```
InterADS4M/
├── backend/             # API Laravel + garantias (Blade/Vite)
│   ├── app/             # Controllers e Models
│   ├── config/          # Guard custom "apoiador"
│   ├── database/        # Migrations e seeders (dump do banco fica fora do git)
│   ├── docker-compose.yml  # MySQL 8.0
│   └── routes/web.php   # Todas as rotas
├── inter-ong/           # SPA React (Vite)
│   └── src/
│       ├── components/  # NavBar, Footer
│       └── pages/       # LandingPage, Doar, Sobre, Contato, Educacional
└── relatorio_backend.md # Documentação do progresso
```

---

## 🚀 Como rodar

### Backend

```bash
cd backend
docker compose up -d          # sobe o MySQL 8.0
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed           # dados de demonstração da ONG
npm install && npm run build  # assets (Vite/Tailwind)
php artisan dev
```

### Acesso ao painel de gestão

O painel em `/` (indicadores, crianças, apoiadores, seed e cadastro de crianças) é
**exclusivo da gestão da ONG** e exige um apoiador com `tipo_usuario = admin`.

O seed cria a conta `gestor@exemplo.org` **sem senha utilizável** — de propósito:
escrever um hash de senha no repositório publica a credencial de quem for usar
aquele banco (já aconteceu, ver "Credenciais expostas no histórico do git" mais
abaixo). A senha é definida por comando:

```bash
php artisan gestor:senha gestor@exemplo.org "minha-senha-forte"   # senha definida
php artisan gestor:senha gestor@exemplo.org                        # gera uma senha forte
```

Para **redefinir a senha de um apoiador sem promover a gestor** (é o que a
rotação de credenciais usa):

```bash
php artisan apoiador:senha apoiador1@exemplo.org     # gera e mostra a senha nova
php artisan apoiador:senha marina.costa@email.com "outra-senha-forte"
```

O comando também encerra as sessões que ainda valem com a senha antiga. Depois é
só entrar em `/entrar` com o e-mail e a senha.

### Credenciais expostas no histórico do git

O `backend/database/init.sql` (dump com CPF, e-mail, endereço, 12 hashes de senha
e uma linha da tabela `sessions` de pessoas reais) e o `OngDadosSeeder` com
credenciais de verdade ficaram versionados num repositório público. Isso foi
corrigido: o dump saiu do repositório, os seeds passaram a usar e-mails de
exemplo e senha inutilizável, e o **histórico do git foi reescrito** — por isso
todos os commits têm SHA novo. Quem já clonou precisa atualizar:

```bash
git fetch origin && git reset --hard origin/main
```

**O que a reescrita não desfaz:** quem já clonou antes mantém os arquivos, e o
GitHub guarda commits órfãos por um tempo. Por isso a rotação de senhas é a
parte que realmente protege as contas:

```bash
php artisan apoiadores:listar                        # lista as contas e o comando de cada uma
php artisan apoiadores:listar --rotacionar           # só as contas com pendência de senha
```

A lista sai do próprio banco, marca quem ainda responde `senha123` (a senha
pública que estava no seed e no dump removidos), imprime o comando exato de
cada conta (para não sobrar dígito de e-mail digitado errado) e mostra em
`senha em` a data da última rotação — a tabela `apoiadores` nunca teve
`created_at`/`updated_at`, então essa data é gravada em `senha_alterada_em` por
cada rotação. `nunca` significa "ninguém rotacionou esta conta por aqui" e
conta como pendência no `--rotacionar`.

```bash
php artisan gestor:senha gestor@exemplo.org        # conta da gestão (promove a gestor)
php artisan apoiador:senha <email>                  # cada apoiador, mantendo o papel dele
```

**Rotacionou as contas antes de 26/09?** A coluna `senha_alterada_em` só
existe a partir dessa migration, então quem já entregou senha nova às pessoas
não tem a data gravada. Este comando carimba a data **sem trocar senha
nenhuma** — o contrário seria obrigar a reentregar senha a quem já recebeu a
sua:

```bash
php artisan apoiadores:marcar-senha carol@sos.org.br    # uma conta
php artisan apoiadores:marcar-senha --todos             # todas que ainda estão sem data
php artisan apoiadores:marcar-senha --todos --reforcar  # sobrescreve as datas (use com cuidado)
```

Sem `--reforcar`, nenhum comando sobrescreve a data de quem já tem — é a
proteção contra um `--todos` acidental apagar o histórico de quem rotacionou
quando.

Um teste (`tests/Feature/RotacaoDeSenhaTest.php`) impede que um hash de senha ou
um e-mail de domínio real volte para qualquer seeder.

**A senha que você entrega é a senha que a pessoa vai ter até ela trocar.** Se
ela vai por WhatsApp ou e-mail, quem leu a conversa tem a conta — e o código não
tem como encerrar isso sozinho. Este comando trava a conta na tela de troca até
a pessoa criar uma senha própria, que é a única janela em que dá para forçar
isso: depois que ela já entrou com a senha temporária, não dá mais para obrigar
sem resetar a conta.

```bash
php artisan apoiadores:exigir-troca carol@sos.org.br   # uma conta
php artisan apoiadores:exigir-troca --todos            # todas as contas de apoiador
php artisan apoiadores:exigir-troca --todos --desfazer # cancela (ninguém entrou ainda)
```

`--todos` **ignora a conta da gestão** e avisa no terminal: travar o admin é a
forma mais rápida de a ONG ficar sem acesso ao próprio painel. Para a conta da
gestão, use o e-mail. A coluna `troca` de `php artisan apoiadores:listar` mostra
quem está presa na troca (`obrigatoria`), separada da coluna `senha em` — conta
rotacionada e conta que já trocou a senha são coisas diferentes.

Um detalhe que só o teste pega: a troca **recusa** a senha que a conta já está
usando. Sem isso a pessoa cola de volta a senha temporária, o formulário aceita
e a senha entregue continua valendo — o pedido de "senha nova" vira enfeite.
### Senha e tentativas de login

- **Senha forte em todo lugar**: o cadastro público (`/cadastro`) e os dois
  comandos de rotação exigem no mínimo 8 caracteres com maiúscula, minúscula e
  número. `min:6` aceitava `123456`, que cai em segundos num ataque de
  dicionário. A regra está em `app/Rules/SenhaForte.php` e o critério em
  `app/Support/SenhaForte.php` - os três lugares falam a mesma língua.
- **Senha gerada nunca sai fraca**: o gerador garante uma maiúscula, uma
  minúscula, um número e um símbolo por construção, e embaralha. Antes, uma em
  cada dez senhas geradas não tinha nenhum dígito e era reprovada pela própria
  regra - o comando falhava ao acaso.
- **Bloqueio por e-mail e IP**: 5 erros bloqueiam aquele par por 5
  minutos, com mensagem dizendo quando volta. O limite por IP (10/min)
  continua valendo; o bloqueio por e-mail/IP não impede tentativas distribuídas entre IPs. Login
  certo zera o contador, e um e-mail inexistente trava igual a um existente
  (senão a resposta revelaria quais e-mails têm conta). Chave em
  `ApoiadorAuthController`, testes em `tests/Feature/BloqueioDeLoginTest.php`.

Limites de requisição: `api` 120/min, `newsletter` 5/min, `cadastro` 5/min,
`login` 10/min, `doacao-unica` 10/min (por IP) e `troca-senha-post` 10/min por e-mail/IP, em `AppServiceProvider`.

As APIs JSON (`/api/criancas`, `/api/apoiadores`, `/api/programas`,
`/api/apadrinhamentos`, `/api/noticias`, `/api/materiais-didaticos`,
`/api/transparencia` e `POST /api/newsletter`) continuam **públicas** porque
alimentam o site, mas devolvem só o que o site precisa mostrar:

- `/api/apoiadores`: apenas números — total de apoiadores, doadores e valores agregados;
- `/api/criancas`: nome, idade, imagem e se já tem padrinheiro (sem data de nascimento nem histórico);
- `/api/apadrinhamentos`: a criança e as recompensas enviadas (sem dizer quem apadrinha nem quanto paga).

Nada de `senha`, `cpf`, `email`, `celular`, endereço, valor pago ou papel na gestão sai dessas rotas. As listas são montadas campo a campo e `tests/Feature/ApiPublicaNaoExpoeDadoPessoalTest.php` varre a resposta inteira de todas elas para travar isso.

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

1. Copie `backend/public/index.php` para a pasta pública com o nome `laravel.php` e ajuste os caminhos de `vendor/autoload.php` e `bootstrap/app.php` para onde está o backend (ex.: `__DIR__.'/../ong/backend/vendor/autoload.php'`).
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
php artisan key:generate
php artisan migrate --force        # cria as tabelas; NÃO rode seeders
php artisan storage:link           # imagens/PDFs públicos
php artisan config:cache
php artisan route:cache
```

- Currículos de voluntários ficam em disco **privado** (`storage/app/private/curriculos`) e só saem pelo painel. Imagens e PDFs públicos ficam no disco `public` (`storage/app/public`); o `storage:link` cria o atalho `public/storage` dentro do backend. Como aqui a pasta pública é outra, se alguma imagem não abrir crie também um atalho de `<pasta pública>/storage` para `~/ong/backend/storage/app/public` **(confirmar no painel da Hostinger** se o plano permite atalhos).
- Se o `composer` não existir no servidor, rode `composer install --no-dev` no seu computador (mesma versão de PHP, 8.3) e envie a pasta `vendor/`.
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
- Toda semana, faça um `mysqldump` do banco e uma cópia de `storage/app` (currículos e arquivos enviados) para um lugar seguro fora do servidor:

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

### Assets do backend (Vite + Tailwind, sem internet)

As telas Blade são compiladas por `npm run build` e servidas de `public/build`.
Nenhuma tela usa CDN ou fonte externa, porque o sistema roda offline no pen drive.

```bash
cd backend
npm install
npm run build      # rode de novo sempre que criar/renomear classes nas telas
npm run dev        # opcional: recompila sozinho enquanto vc programa
```

Os testes não dependem do build. Para conferir o build de verdade, rode
`php artisan test --filter=AssetsOfflineTest` depois de `npm run build`.

### Cadastro de apoiador

`POST /cadastro` valida o CPF no servidor (dígitos verificadores, sem dígitos
repetidos) e guarda o valor só com números, mesmo que a pessoa digite com
máscara. CPF inválido volta `422` com o erro no campo `cpf`, então a tela de
cadastro da SPA deve mostrar a mensagem que o backend devolver.

### Testes

```bash
cd backend
php artisan test    # 239 testes: APIs, autenticação, painel, seeders, CPF, rotas, CORS/CSRF, limite de requisições, dados pessoais, rotação de senhas e assets offline
```

### Frontend SPA

```bash
cd inter-ong
npm install
npm run dev
```

### Integração do frontend com a API

O cliente React pode importar `inter-ong/src/api/backend.js`. A API usa a sessão do Laravel e fica em `/api/v1`; no desenvolvimento, o frontend deve usar a mesma origem por proxy ou definir `VITE_BACKEND_URL`. Antes do primeiro POST, chame `prepararCsrf()` e envie o token retornado no header `X-CSRF-TOKEN`. As requisições usam `credentials: include`.

Os recursos públicos ficam em `GET /api/v1/conteudos/noticias`, `materiais`, `transparencia` e `programas`, além de `GET /api/v1/estatisticas`. Cadastro e login usam `POST /api/v1/auth/cadastro` e `POST /api/v1/auth/entrar`. A resposta 403 com `codigo=troca_senha_obrigatoria` indica que o usuário precisa chamar `PUT /api/v1/auth/senha`; 401 indica sessão ausente ou credencial inválida; 422 traz erros por campo.

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

### Correções da auditoria de 26/09/2026

A suíte atual passou com **239 testes e 3.211 assertions**. Foram corrigidos o encerramento de sessões na rotação/troca de senha, a persistência indevida de senhas em dados de formulário na sessão, o rehash no login e a associação de recompensas no seed. Os testes de sessão usam o armazenamento real do Laravel, inclusive criptografia e isolamento entre usuários/guards.

A revogação implementada pressupõe `SESSION_DRIVER=database`. Não trocar o driver sem adaptar e validar essa operação. As correções não executam rotação nem encerram retroativamente sessões do banco real.

O formulário de doação registra **intenção pendente**, sem cobrança. As estatísticas de doação única incluem apenas registros `concluido`; valores antigos e dados de demonstração ainda precisam de conciliação antes de divulgar arrecadação real. O provedor e os webhooks de pagamento continuam pendentes.

O `relatorio_backend.md` contém o estado atual, o contrato de integração com a SPA e os limites da validação em SQLite. Laravel Boost foi instalado somente para desenvolvimento, conforme as instruções locais do projeto.
