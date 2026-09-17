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
npm install && npm run build  # assets (Vite/Tailwind)
php artisan dev
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
- ✅ Frontend: estrutura inicial com páginas placeholder
- ⏳ Pendente: CORS, auth admin, upload de arquivos, endpoints REST restantes

---

## 👥 Colaboradores

- Carlos
- Matheus

---

## 📝 Licença

A combinar.