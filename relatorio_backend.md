# 📋 Relatório de Desenvolvimento — Backend ONG SOS

**Projeto:** InterADS4M — Site da ONG SOS  
**Responsável pelo Backend:** Carlos  
**Data:** 11/09/2026; última atualização em **15/09/2026**  
**Tecnologias:** Laravel (PHP 8.4), MySQL 8.0, Docker, Tailwind CSS, Blade  

---

## 1. Resumo do Progresso Nesta Sessão

### Sessão de 15/09/2026 — Importação do acervo da ONG (`ong.sql` + `ONG.zip`)
Foi realizada a **integração completa dos dados e arquivos da ONG** no backend do InterADS4M. O schema do banco foi **mesclado** (tabelas antigas do `init.sql` + tabelas/colunas do novo `ong.sql`) e **povoado com dados reais** através de migrations e seeder. As imagens do acervo foram extraídas para `public/img`, o `conexão.php` foi corrigido e o `init.sql` foi atualizado para que um container Docker novo suba com o banco já completo. Todos os endpoints REST existentes foram testados e aprovados.

### Sessões anteriores — Autenticação e Área Logada do Apoiador
Avançamos na estruturação e validação do **módulo de autenticação e área logada do Apoiador**. A infraestrutura de autenticação isolada (utilizando Custom Guards do Laravel) foi totalmente integrada e testada no banco de dados e na camada de visualização (views/controllers).

---

## 2. Infraestrutura e Banco de Dados

### 2.1 Container Docker (MySQL)
O banco de dados continua rodando em ambiente isolado via Docker:
- **Database:** `ong`
- **Porta:** `3306`
- **Ambiente:** Padronizado para toda a equipe.
- **Credenciais:** `meu_usuario` / `minha_senha` (container `meu_mysql`, MySQL 8.0).

### 2.2 Estrutura de Tabelas e Modificações
**Schema mesclado (15/09)** — o banco agora contém todas as tabelas, mesclando o schema antigo do `init.sql` com o novo `ong.sql`:
* **Tabela `apoiadores`:** Ajustada e sincronizada para suporte à autenticação própria; ganhou a coluna `tipo_usuario` (`apoiador` | `admin`) via migration `2026_09_15_000003`.
* **Tabelas de conteúdo da ONG (novas, migration `2026_09_15_000001`):** `criancas`, `apadrinhamentos`, `recompensas_apadrinhamento`, `doacoes_unicas`, `doacoes_mensais`, `galeria`, `programas_acoes`, `voluntarios`.
* **Tabelas administrativas (migration `2026_09_15_000002`):** `administradores`, `noticias`, `materiais_didaticos`, `documentos_transparencia`, `newsletter`.
* A tabela `apadrinhamentos` antiga (schema inicial, vazia e sem AUTO_INCREMENT) e a `criancas` parcialmente criada por uma migration antiga foram **dropadas** e recriadas pelas novas migrations.

### 2.3 Dados Importados (seeder `OngDadosSeeder`)
Dados reais extraídos do `ong.sql` e inseridos com sucesso (contagens verificadas no banco):

| Tabela | Total | Observações |
|---|---|---|
| `criancas` | 3 | Ben Tennyson, Bart Simpson, Chaves (com `imagem_perfil`) |
| `apoiadores` | 4 | Matheus Figueiredo, Danillo roger, João Silva, Carol (`admin`) |
| `apadrinhamentos` | 1 | Ben Tennyson → Matheus (R$100/mês, ativo) |
| `recompensas_apadrinhamento` | 1 | Vídeo/mensagem do Ben 10 (PNG) |
| `doacoes_unicas` | 3 | Valores em Pix, concluídas |
| `doacoes_mensais` | 3 | Pix Automático e Cartão de Crédito recorrente |
| `programas_acoes` | 1 | "Lute como uma Mãe Atípica" |
| `voluntarios` | 2 | 1 aprovado, 1 em análise |
| `galeria` | 1 | Logo da ONG |

### 2.4 `conexão.php`
Corrigido o nome do banco de `'meu_banco'` → `'ong'` em `backend\conexão.php`, alinhando com o `.env` e o container Docker.

---

## 3. Implementações de Código (Backend & Autenticação)

### 3.1 Model e Guard do Apoiador
* **Model `Apoiador.php`:** Atualizado para herdar de `Illuminate\Foundation\Auth\User`, permitindo que funcione como entidade autenticável do Laravel.
* **Configuração de Autenticação (`config/auth.php`):** Criado o guard personalizado `apoiador` e o provedor de usuários correspondente, separando o acesso dos doadores/voluntários dos usuários administrativos padrão.

### 3.2 Controllers e Fluxo de Sessão
* **`CadastroApoiadorController.php`:** Responsável por validar campos obrigatórios (CPF, e-mail, senha), criptografar a senha com `Hash::make()` e salvar o novo apoiador na tabela.
* **`LoginApoiadorController.php`:** Gerencia a autenticação com a chamada `Auth::guard('apoiador')->attempt(...)` e destruição de sessão no logout.
* **`MinhaContaController.php`:** Gerencia o painel interno do apoiador (`/minha-conta`). Recupera o usuário autenticado e prepara as relações de doações, apadrinhamentos e voluntariado.

### 3.3 Views e Rotas (Blade & Tailwind CSS)
* Criadas as views de cadastro, login e o painel (`resources/views/apoiador/minha-conta.blade.php`).
* Rotas protegidas e validadas:
  * `GET /cadastro` e `POST /cadastro`
  * `GET /entrar` e `POST /entrar`
  * `GET /minha-conta` (Área Restrita do Apoiador)
  * `POST /logout`

---

## 4. Testes e Validações Realizados

### 4.1 Sessão de 15/09 — Importação de Dados e Endpoints

| Teste | Status | Resultado |
|---|---|---|
| **Migrations (3 novas)** | 🟢 Aprovado | Todas as tabelas criadas sem erro. |
| **Seeder `OngDadosSeeder`** | 🟢 Aprovado | 3 crianças, 4 apoiadores, 1 apadrinhamento, recompensas, doações, programa, voluntários e galeria inseridos. |
| **`GET /api/criancas`** | 🟢 Aprovado | Total: 3, com relação de apadrinhamentos inline. |
| **`GET /api/apoiadores`** | 🟢 Aprovado | Total: 4, com voluntário, doações mensais e únicas inline. |
| **`GET /api/apadrinhamentos`** | 🟢 Aprovado | Total: 1, com apoiador, criança e recompensas. |
| **`GET /api/programas`** | 🟢 Aprovado | Total: 1 ("Lute como uma Mãe Atípica"). |
| **`GET /api/noticias`** | 🟢 Aprovado | Total: 0 (tabela pronta, sem registros). |
| **Imagens em `public/img`** | 🟢 Aprovado | `ben10.jpg`, `chaves.jpg`, `bart.jpg`, `logo.png`, `materia_1789498189.jpg`, `recompensa_1789499207.png` servidas via `/img/*`. |
| **`GET /api/voluntarios`** | 🟡 Não implementado | Rota inexistente (404). O frontend não consome API; rota fica para implementação. |

### 4.2 Sessões anteriores — Autenticação

| Teste | Status | Resultado |
|---|---|---|
| **Cadastro de Apoiador** | 🟢 Aprovado | Gravação correta no MySQL com hash da senha. |
| **Login com Guard Dedicado** | 🟢 Aprovado | Sessão iniciada e mantida com sucesso no guard `apoiador`. |
| **Acesso ao Painel (`/minha-conta`)** | 🟢 Aprovado | Exibição de saudações e dados pessoais cadastrados. |
| **Isolamento de Tabelas Inexistentes** | 🟢 Aprovado | O sistema não quebra com erro de SQL ao consultar doações pendentes. |
| **Logout de Usuário** | 🟢 Aprovado | Destruição da sessão e encerramento do acesso. |

---

## 5. O Que Foi Feito vs. O Que Falta Feazer

### 🟢 Concluído
1. Infraestrutura Docker com MySQL.
2. Estrutura de Models para todas as tabelas da ONG.
3. APIs REST de leitura para crianças, notícias, materiais e transparência.
4. Módulo de Autenticação Completo do Apoiador (Registro, Login, Guard e Painel).
5. **Mesclagem do schema do banco (init.sql + ong.sql) com todas as tabelas da ONG** (15/09).
6. **Importação dos dados reais da ONG via migrations + seeder** (crianças, apoiadores, apadrinhamentos, recompensas, doações mensais/únicas, programas, voluntários, galeria) (15/09).
7. **Extração e cópia do acervo de imagens para `public/img`** (15/09).
8. **Correção do `conexão.php`** (`meu_banco` → `ong`) (15/09).
9. **Atualização do `database/init.sql`** com o estado completo (schema + dados), garantindo ambiente limpo via Docker com tudo pronto (15/09).

---

### 🔴 Pendente — Alta Prioridade
1. **Configuração de CORS:** Liberar o backend para permitir requisições HTTP do frontend em React/Next.js.
2. **Autenticação Admin (JWT / Sanctum):** Painel administrativo restrito para a gestão de conteúdos por Carol e Flávio.
3. **Upload de Arquivos:**
   * Rota `POST /api/voluntarios` para envio de currículo (PDF) + validação de maioridade (+18 anos).
   * Rota de upload para `materiais_didaticos` e `documentos_transparencia`.
4. **Rota `GET /api/voluntarios`** (e demais endpoints REST de leitura que faltam) para expor as tabelas recém-importadas.

---

### 🟡 Pendente — Média Prioridade
1. **Refatoração de Controllers:** Dividir o `DashboardTesteController.php` em controllers específicos por domínio (`NoticiaController`, `CriancaController`, `ApadrinhamentoController`).
2. **Integração de Meios de Pagamento:** Preparar a estrutura/webhooks de PIX e Cartão para doações.

---

### 🟢 Pendente — Baixa Prioridade
1. **Paginação nas APIs REST:** Implementar paginação para listagens longas (ex.: notícias e galeria).

---

## 6. Histórico de Commits e Sincronização Git

* **Commits Anteriores:** Configurações iniciais, scripts SQL e modelos.
* **Commits Recentes:**
  * `feat(auth): configuracao do guard de apoiador e controllers de autenticacao`
  * `feat(views): ajuste na migration e telas de autenticação e painel do apoiador`
* **Alterações em andamento (15/09, ainda não commitadas — aguardando revisão):**
  * `conexão.php` (correção do nome do banco)
  * `database/init.sql` (schema + dados completos)
  * Migrations novas: `2026_09_15_000001_create_ong_content_tables`, `2026_09_15_000002_create_ong_admin_tables`, `2026_09_15_000003_add_tipo_usuario_to_apoiadores_table`
  * `database/seeders/OngDadosSeeder.php`
  * `public/img/` (acervo de imagens da ONG)