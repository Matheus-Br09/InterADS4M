# 🌱 InterADS4M

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat-square)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat-square)
![React](https://img.shields.io/badge/React-19-61DAFB?style=flat-square)
![Vite](https://img.shields.io/badge/Vite-646CFF?style=flat-square)
![Tailwind](https://img.shields.io/badge/Tailwind_CSS-4-06B6D4?style=flat-square)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=flat-square)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=flat-square)

Plataforma web para a **ONG SOS** — divulgação de ações, angariação de doações e gestão de apoiadores, voluntários e crianças atendidas. Sistema em dois repositórios: API REST e views em **Laravel** + SPA em **React**.

> Projeto colaborativo — Carlos & Matheus. Status: **em desenvolvimento**.

---

## ✨ Funcionalidades

### Área pública (SPA React)

- 🏠 Landing page institucional
- ❤️ **Doações** — página de doações e apoio único
- 🧭 Sobre a ONG, contato e conteúdo educacional

### Backend / Área do apoiador (Laravel)

- 📋 **Cadastro e login de apoiadores** (guard custom `apoiador`, senha forte e bloqueio por tentativas)
- 🏠 **Painel `minha-conta`** — área protegida do apoiador
- 💰 **Apoio único** — doação avulsa com autenticação
- 📚 **Conteúdo da ONG** — crianças, programas, notícias, materiais didáticos e documentos de transparência
- 📧 Cadastro na newsletter

---

## 🛠️ Stack

| Camada | Tecnologia |
| --- | --- |
| **Backend** | Laravel 13 (PHP 8.3) |
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
- **Bloqueio por conta, não só por IP**: 5 erros travam aquele e-mail por 5
  minutos, com mensagem dizendo quando volta. O limite por IP (10/min)
  continua valendo: ele protege o servidor, o bloqueio protege a conta. Login
  certo zera o contador, e um e-mail inexistente trava igual a um existente
  (senão a resposta revelaria quais e-mails têm conta). Chave em
  `ApoiadorAuthController`, testes em `tests/Feature/BloqueioDeLoginTest.php`.

Limites de requisição: `api` 120/min, `newsletter` 5/min, `cadastro` 5/min,
`login` 10/min, `doacao-unica` 10/min (todos por IP, em `AppServiceProvider`).

As APIs JSON (`/api/criancas`, `/api/apoiadores`, `/api/programas`,
`/api/apadrinhamentos`, `/api/noticias`, `/api/materiais-didaticos`,
`/api/transparencia` e `POST /api/newsletter`) continuam **públicas** porque
alimentam o site, mas devolvem só o que o site precisa mostrar:

- `/api/apoiadores`: apenas números — total de apoiadores, doadores e valores agregados;
- `/api/criancas`: nome, idade, imagem e se já tem padrinheiro (sem data de nascimento nem histórico);
- `/api/apadrinhamentos`: a criança e as recompensas enviadas (sem dizer quem apadrinha nem quanto paga).

Nada de `senha`, `cpf`, `email`, `celular`, endereço, valor pago ou papel na gestão sai dessas rotas. As listas são montadas campo a campo e `tests/Feature/ApiPublicaNaoExpoeDadoPessoalTest.php` varre a resposta inteira de todas elas para travar isso.

### Publicando (quando sair de desenvolvimento)

Hoje o projeto **não está em produção**, e o `.env.example` está com os valores
de desenvolvimento. Nada aqui trava o trabalho local; esta é a lista do que
precisa estar diferente no dia da publicação, e é toda em `.env` — sem editar
código:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com.br
CORS_ALLOWED_ORIGINS=https://seudominio.com.br
SESSION_SECURE_COOKIE=true      # só se o acesso for por HTTPS
```

E os comandos, na ordem:

```bash
cd backend
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm ci && npm run build
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan storage:link
```

Duas coisas que só valem a pena conferir porque não aparecem em erro nenhum:

- **`SESSION_SECURE_COOKIE` só com HTTPS.** Ligado em `http://localhost` ou em
  IP de rede local, o navegador simplesmente não devolve o cookie e **todo
  mundo fica deslogado**, sem mensagem.
- **`CORS_ALLOWED_ORIGINS` com a origem do site, não do backend.** O valor é a
  origem de onde o `fetch` sai, ou seja, o endereço do site público. Asterisco
  em produção deixa qualquer página da internet ler as respostas da API. Várias
  origens separe por vírgula, sem espaços sobrando.

Depois de publicar, `php artisan apoiadores:listar` continua sendo o jeito de
conferir se nenhuma conta ficou com a senha antiga.

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
php artisan test    # 191 testes: APIs, autenticação, painel, seeders, CPF, rotas, CORS/CSRF, limite de requisições, dados pessoais, rotação de senhas e assets offline
```

### Frontend SPA

```bash
cd inter-ong
npm install
npm run dev
```

---

## 🔄 Status atual

- ✅ Backend: autenticação do apoiador, dados da ONG importados
- ✅ Painel de gestão protegido por `tipo_usuario = admin` + comando `gestor:senha`
- ✅ Cadastro público com validação de CPF no servidor
- ✅ Telas Blade com Vite/Tailwind locais (funciona sem internet)
- ✅ 191 testes automatizados no backend
- ✅ Frontend: estrutura inicial com páginas placeholder
- ⏳ Pendente: ligar a tela de cadastro/login da SPA ao backend (hoje ela posta para `NomeDoArquivoLogin.php`, que nao existe, e valida CPF num servico de terceiro), rodar a rotação de senha nas contas reais, restringir CORS antes de publicar, registrar a autorização dos responsáveis das crianças, revisar as fotos do acervo, upload de arquivos, endpoints REST restantes

---

## 👥 Colaboradores

- Carlos
- Matheus

---

## 📝 Licença

A combinar.