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

- 📋 **Cadastro e login de apoiadores** (guard custom `apoiador`)
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
│   ├── database/        # Migrations, seeders, init.sql
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
php artisan db:seed           # dados reais da ONG + dados de demonstração
npm install && npm run build  # assets (Vite/Tailwind)
php artisan dev
```

### Acesso ao painel de gestão

O painel em `/` (indicadores, crianças, apoiadores, seed e cadastro de crianças) é
**exclusivo da gestão da ONG** e exige um apoiador com `tipo_usuario = admin`. O seed
cria a conta `gestor@exemplo.org`. Para definir a senha dela (ou de qualquer outro
apoiador, promovendo a gestor):

```bash
php artisan gestor:senha gestor@exemplo.org "minha-senha-forte"   # senha definida
php artisan gestor:senha gestor@exemplo.org                        # gera uma senha
```

Depois é só entrar em `/entrar` com o e-mail e a senha.

As APIs JSON (`/api/criancas`, `/api/apoiadores`, `/api/programas`,
`/api/apadrinhamentos`, `/api/noticias`, `/api/materiais-didaticos`,
`/api/transparencia` e `POST /api/newsletter`) continuam **públicas** porque
alimentam o site, mas devolvem só o que o site precisa mostrar:

- `/api/apoiadores`: apenas números — total de apoiadores, doadores e valores agregados;
- `/api/criancas`: nome, idade, imagem e se já tem padrinheiro (sem data de nascimento nem histórico);
- `/api/apadrinhamentos`: a criança e as recompensas enviadas (sem dizer quem apadrinha nem quanto paga).

Nada de `senha`, `cpf`, `email`, `celular`, endereço, valor pago ou papel na gestão sai dessas rotas. As listas são montadas campo a campo e `tests/Feature/ApiPublicaNaoExpoeDadoPessoalTest.php` varre a resposta inteira de todas elas para travar isso.

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
php artisan test    # 145 testes: APIs, autenticação, painel, seeders, CPF, rotas, CORS/CSRF, limite de requisições e assets offline
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
- ✅ 145 testes automatizados no backend
- ✅ Frontend: estrutura inicial com páginas placeholder
- ⏳ Pendente: rotacionar dados expostos no histórico do git, restringir CORS antes de publicar, upload de arquivos, endpoints REST restantes

---

## 👥 Colaboradores

- Carlos
- Matheus

---

## 📝 Licença

A combinar.