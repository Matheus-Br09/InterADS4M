# 📋 Relatório de Desenvolvimento — Backend ONG SOS
**Projeto:** InterADS4M — Site da ONG SOS  
**Responsável pelo Backend:** Carlos  
**Data:** 10/09/2026  
**Tecnologias:** Laravel (PHP), MySQL 8.0, Docker

---

## 1. Infraestrutura do Banco de Dados

### 1.1 Container Docker (MySQL)
**Arquivo:** [`docker-compose.yml`](file:///c:/Users/Carlos/OneDrive/Documentos/Antigravity_Projetos/InterADS4M/backend/docker-compose.yml)

O banco de dados **não roda instalado diretamente na máquina**. Ele roda dentro de um **container Docker**, o que garante:
- Qualquer pessoa que clonar o projeto sobe o banco com um único comando (`docker compose up -d`).
- O ambiente de desenvolvimento é idêntico para toda a equipe.
- Nenhuma instalação manual do MySQL é necessária.

**Configurações do banco:**
| Parâmetro | Valor |
|---|---|
| Imagem | `mysql:8.0` |
| Banco | `ong` |
| Porta | `3306` |
| Root password | `minha_senha_root` |
| Usuário | `meu_usuario` |

---

## 2. Banco de Dados — Estrutura de Tabelas

### 2.1 Tabelas originais do projeto

| Tabela | Finalidade |
|---|---|
| `apoiadores` | Cadastro completo de doadores e voluntários (nome, CPF, endereço, login) |
| `criancas` | Crianças atendidas pela ONG disponíveis para apadrinhamento |
| `apadrinhamentos` | Liga um apoiador a uma criança com valor mensal definido |
| `recompensas_apadrinhamento` | Arquivos de mídia enviados para os padrinhos como agradecimento |
| `doacoes_mensais` | Controle de assinaturas e doações recorrentes |
| `doacoes_unicas` | Registro de doações avulsas (PIX, Cartão, Boleto) |
| `galeria` | Fotos da ONG com legenda para exibição no site |
| `programas_acoes` | Programas e projetos da ONG (Neuropedagogia, Saúde, etc.) |
| `voluntarios` | Ficha do voluntário: área de atuação, disponibilidade, status e currículo |

### 2.2 Tabelas adicionadas nesta sessão

As tabelas abaixo foram identificadas como **ausentes no banco original**, mas necessárias para atender 100% dos requisitos levantados pelo documento da ONG:

| Tabela | Requisito Atendido | Motivo da Criação |
|---|---|---|
| `administradores` | Req. 7 — Painel Admin (Carol e Flávio) | Sem essa tabela, não é possível criar o login restrito do painel administrativo |
| `noticias` | Req. 4 — Portal de Notícias e Eventos | O Model `Noticia.php` já existia no Laravel, mas a tabela no banco não havia sido criada |
| `materiais_didaticos` | Req. 4 — Área Educacional para Mães e Crianças | Mesma situação: Model existia, tabela não existia no banco |
| `documentos_transparencia` | Req. 4 — Prestação de Contas e Transparência | Sem essa tabela, não há como armazenar relatórios, balancetes e certidões da ONG |
| `newsletter` | Req. 5 — E-mail Marketing | Para capturar e-mails de visitantes interessados e montar base de contatos para campanhas futuras |

### 2.3 Coluna adicionada em tabela existente

| Tabela | Coluna adicionada | Motivo |
|---|---|---|
| `apoiadores` | `data_nascimento DATE` | Requisito 5: voluntários precisam ter +18 anos. Sem a data de nascimento, essa validação é impossível |

> **Regra do banco:** Datas são sempre armazenadas no formato internacional `AAAA-MM-DD` (ex: `2007-08-25`). A conversão para o formato brasileiro `DD/MM/AAAA` é feita no momento da exibição, seja no Laravel (PHP) ou no React (JavaScript).

---

## 3. Models do Laravel

Os **Models** são classes PHP que representam cada tabela do banco de dados no Laravel. Eles permitem fazer consultas como `Crianca::all()` sem escrever SQL manual.

### 3.1 Models que já existiam

| Model | Tabela correspondente |
|---|---|
| [`Apoiador.php`](file:///c:/Users/Carlos/OneDrive/Documentos/Antigravity_Projetos/InterADS4M/backend/app/Models/Apoiador.php) | `apoiadores` |
| [`Crianca.php`](file:///c:/Users/Carlos/OneDrive/Documentos/Antigravity_Projetos/InterADS4M/backend/app/Models/Crianca.php) | `criancas` |
| [`Apadrinhamento.php`](file:///c:/Users/Carlos/OneDrive/Documentos/Antigravity_Projetos/InterADS4M/backend/app/Models/Apadrinhamento.php) | `apadrinhamentos` |
| [`DoacaoMensal.php`](file:///c:/Users/Carlos/OneDrive/Documentos/Antigravity_Projetos/InterADS4M/backend/app/Models/DoacaoMensal.php) | `doacoes_mensais` |
| [`DoacaoUnica.php`](file:///c:/Users/Carlos/OneDrive/Documentos/Antigravity_Projetos/InterADS4M/backend/app/Models/DoacaoUnica.php) | `doacoes_unicas` |
| [`Galeria.php`](file:///c:/Users/Carlos/OneDrive/Documentos/Antigravity_Projetos/InterADS4M/backend/app/Models/Galeria.php) | `galeria` |
| [`ProgramaAcao.php`](file:///c:/Users/Carlos/OneDrive/Documentos/Antigravity_Projetos/InterADS4M/backend/app/Models/ProgramaAcao.php) | `programas_acoes` |
| [`Voluntario.php`](file:///c:/Users/Carlos/OneDrive/Documentos/Antigravity_Projetos/InterADS4M/backend/app/Models/Voluntario.php) | `voluntarios` |
| [`RecompensaApadrinhamento.php`](file:///c:/Users/Carlos/OneDrive/Documentos/Antigravity_Projetos/InterADS4M/backend/app/Models/RecompensaApadrinhamento.php) | `recompensas_apadrinhamento` |
| [`Administrador.php`](file:///c:/Users/Carlos/OneDrive/Documentos/Antigravity_Projetos/InterADS4M/backend/app/Models/Administrador.php) | `administradores` |
| [`Noticia.php`](file:///c:/Users/Carlos/OneDrive/Documentos/Antigravity_Projetos/InterADS4M/backend/app/Models/Noticia.php) | `noticias` |
| [`MaterialDidatico.php`](file:///c:/Users/Carlos/OneDrive/Documentos/Antigravity_Projetos/InterADS4M/backend/app/Models/MaterialDidatico.php) | `materiais_didaticos` |

### 3.2 Models criados nesta sessão

| Model | Tabela | Motivo |
|---|---|---|
| [`DocumentoTransparencia.php`](file:///c:/Users/Carlos/OneDrive/Documentos/Antigravity_Projetos/InterADS4M/backend/app/Models/DocumentoTransparencia.php) | `documentos_transparencia` | Criado para completar o par Model↔Tabela que estava faltando |
| [`Newsletter.php`](file:///c:/Users/Carlos/OneDrive/Documentos/Antigravity_Projetos/InterADS4M/backend/app/Models/Newsletter.php) | `newsletter` | Criado para completar o par Model↔Tabela que estava faltando |

---

## 4. Rotas e APIs

**Arquivo:** [`routes/web.php`](file:///c:/Users/Carlos/OneDrive/Documentos/Antigravity_Projetos/InterADS4M/backend/routes/web.php)

Todas as APIs seguem o padrão REST e retornam dados em formato **JSON**. O Frontend em React (ou qualquer cliente HTTP) pode chamar essas rotas.

### 4.1 Rotas GET (Leitura de dados — públicas)

| Rota | Method | Descrição |
|---|---|---|
| `/api/criancas` | GET | Lista todas as crianças disponíveis para apadrinhamento |
| `/api/apoiadores` | GET | Lista todos os apoiadores/doadores cadastrados |
| `/api/programas` | GET | Lista os programas e projetos da ONG |
| `/api/apadrinhamentos` | GET | Lista os apadrinhamentos ativos com dados do padrinho e da criança |
| `/api/noticias` | GET | Lista notícias, eventos e campanhas (adicionado nesta sessão) |
| `/api/materiais-didaticos` | GET | Lista os PDFs e materiais para download das mães (adicionado nesta sessão) |
| `/api/transparencia` | GET | Lista documentos de prestação de contas (adicionado nesta sessão) |

### 4.2 Rotas POST (Envio de dados — formulários)

| Rota | Method | Descrição |
|---|---|---|
| `/api/newsletter` | POST | Recebe e salva o e-mail de um visitante interessado |
| `/criancas/salvar` | POST | Cadastra uma nova criança no sistema |

### 4.3 Controller responsável

**Arquivo:** [`DashboardTesteController.php`](file:///c:/Users/Carlos/OneDrive/Documentos/Antigravity_Projetos/InterADS4M/backend/app/Http/Controllers/DashboardTesteController.php)

Atualmente, **todas as rotas são controladas por esse único controller**. Conforme o projeto crescer, o ideal é separar em controllers específicos (ex: `NoticiaController`, `VoluntarioController`, `NewsletterController`).

---

## 5. Dados de Teste Inseridos no Banco

Foram inseridos via DBeaver (script SQL manual) os seguintes registros iniciais para permitir testes das APIs:

| Tabela | Registros inseridos |
|---|---|
| `administradores` | Carol e Flávio (com senha `admin123` hasheada em bcrypt) |
| `criancas` | Lucas Gabriel e Sofia Martins |
| `noticias` | 1 notícia sobre oficinas + 1 evento de mutirão de saúde |
| `materiais_didaticos` | 1 guia de atividades lúdicas neuropedagógicas |
| `programas_acoes` | 2 programas: Neuropedagogia e Saúde e Bem-estar |

---

## 6. O Que Ainda Precisa Ser Feito no Backend

| Prioridade | Tarefa | Descrição |
|---|---|---|
| 🔴 Alta | **CORS** | Liberar o backend para receber chamadas do Frontend React (bloqueado por padrão) |
| 🔴 Alta | **Upload de Currículo** | Rota `POST /api/voluntarios` que aceita arquivo PDF + valida idade (+18 anos) |
| 🔴 Alta | **Autenticação Admin** | Login com JWT/Sanctum para Carol e Flávio acessarem o painel |
| 🟡 Média | **Upload de Materiais/Docs** | Rota de upload de PDFs para `materiais_didaticos` e `documentos_transparencia` |
| 🟡 Média | **Organizar Controllers** | Separar as rotas em controllers dedicados por entidade |
| 🟢 Baixa | **Paginação nas APIs** | Retornar os dados paginados (ex: 10 por página) em vez de tudo de uma vez |

---

## 7. Git — Commits Realizados

| Commit | Hash | Descrição |
|---|---|---|
| Inicial | (antes da sessão) | Estrutura inicial do projeto |
| Sessão atual | `4ceda50` | `feat(database): atualiza init.sql com novas tabelas de requisitos e cria models do Laravel` |

