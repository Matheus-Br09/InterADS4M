# 📋 Relatório de Desenvolvimento — Backend ONG SOS

**Projeto:** InterADS4M — Site da ONG SOS  
**Responsável pelo Backend:** Carlos  
**Data:** 11/09/2026; última atualização em **25/09/2026**  
**Tecnologias:** Laravel 13 (PHP 8.4), MySQL 8.0, Docker, Vite, Tailwind CSS 4, Blade  

---

## 1. Resumo do Progresso Nesta Sessão

### Sessão de 25/09/2026 — Suíte de testes, segurança do painel, Vite local e CPF
Fechamos o ciclo de qualidade e segurança do backend. Foi criada uma **suíte automatizada de 137 testes (499 assertions)** que roda em SQLite, cobrindo autenticação, painel, APIs, seeders, CPF, inventário de rotas e assets offline. Em paralelo, o painel em `/` deixou de ser público: ganhou o middleware `EhGestor`, que exige `tipo_usuario = admin`, e o comando `gestor:senha` resolve o acesso da gestão. As APIs públicas passaram a esconder `senha`, `cpf`, `celular` e `email` de qualquer apoiador. O cadastro público agora **valida o CPF no servidor** (dígitos verificadores, sem dígitos repetidos) e grava o campo só com números. Por fim, as telas Blade trocaram o `cdn.tailwindcss.com` por **build local do Vite**, para o sistema funcionar sem internet no pen drive. A cobertura extra revelou um bug de integração: **`POST /api/newsletter` exige token de CSRF e hoje leva 419 em produção** (ver 4.2).

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

### 3.4 Segurança do Painel de Gestão (25/09)
* **Middleware `EhGestor`:** registrado como `gestor` em `bootstrap/app.php`; aplicado em `GET /`, `POST /seed-dados` e `POST /criancas/salvar`. Qualquer apoiador sem `tipo_usuario = admin` é redirecionado para `/entrar`.
* **Views mortas removidas:** `minha-conta.blade.php` (raiz) e `auth/entrar.blade.php` ficavam duplicadas e sem rota; foram apagadas.
* **APIs públicas sem dados pessoais:** `Apoiador::$hidden` passou a esconder `senha`, `cpf`, `celular` e `email` — inclusive em relações aninhadas (`/api/apoiadores`, `/api/criancas`, `/api/apadrinhamentos`).
* **Comando `gestor:senha`:** `php artisan gestor:senha <email> [senha]` promove um apoiador existente a `admin`, aceita o e-mail com qualquer caixa e pode gerar uma senha forte. É a forma documentada de recuperar o acesso da gestão (conta do seed: `gestor@exemplo.org`).
* **`$fillable` completados** em `Apoiador`, `DoacaoUnica`, `DoacaoMensal` e `Noticia`; o cadastro público passa a gravar `tipo_usuario = apoiador` de forma explícita, ignorando qualquer valor enviado no formulário.
* **`DatabaseSeeder` chama `OngDadosSeeder`:** o seed padrão já entrega o banco com os dados reais da ONG.

### 3.5 Validação de CPF no Cadastro (25/09)
* **Regra `App\Rules\Cpf`:** confere comprimento (11 dígitos), dígitos verificadores e rejeita CPFs de dígitos repetidos (`111.111.111-11`). Traz `apenasDigitos()` e `formatar()` para reuso.
* **`ApoiadorAuthController::register()`:** normaliza o CPF para apenas números antes de validar, aplica a regra e mantém `unique:apoiadores`. CPF inválido volta **422** com o erro no campo `cpf`.
* **Model `Apoiador`:** mutator `cpf()` grava só dígitos, para a unicidade não depender de como a pessoa digitou (com ou sem máscara).

### 3.6 Assets Locais com Vite (25/09)
* As quatro telas Blade (`layouts/app`, `auth/login`, `auth/cadastro` e `welcome`) trocaram `<script src="https://cdn.tailwindcss.com">` por `@vite(['resources/css/app.css', 'resources/js/app.js'])`.
* `resources/css/app.css` ganhou `@source '../../resources/views/**/*.blade.php'`, para o Tailwind 4 varrer as classes das telas.
* O painel de testes perdeu o `<link>` do Google Fonts e passou a usar fontes do sistema.
* `npm run build` gera `public/build` (CSS ~40 kB + JS). O diretório é ignorado pelo Git: **rodar `npm install && npm run build` é obrigatório** depois de criar ou renomear classes nas telas.
* Os testes rodam com `withoutVite()` no `tests/TestCase.php`, então a suíte não depende do build.
* *Observação:* o plugin gera um CSS de fontes separado (`_fonts-*.css`) que não entra automaticamente no HTML; por isso as telas ficam com a pilha de fontes do sistema.

---

## 4. Testes e Validações Realizados

### 4.1 Sessão de 25/09 — Suíte Automatizada

**Comando:** `cd backend && php artisan test` → **137 testes, 137 aprovados, 499 assertions** (banco SQLite em memória, sem depender do MySQL do Docker). Um teste fica marcado como *incompleto* de propósito: é o contrato de CORS que ainda não existe (ver 4.4).

| Arquivo de teste | Testes | O que trava |
|---|---|---|
| `tests/Feature/PainelDeTestesTest.php` | 18 | Painel exige gestor, seed por rota, cadastro de criança, APIs continuam abertas a visitantes. |
| `tests/Unit/CpfTest.php` | 18 | Dígitos verificadores, máscara, dígitos repetidos, `apenasDigitos()` e `formatar()`. |
| `tests/Feature/DominioEOSchemaTest.php` | 14 | Tabelas do banco, tipos de doação, seeders idempotentes, cadastro público nunca cria admin. |
| `tests/Feature/CadastroApoiadorTest.php` | 13 | Cadastro, senha criptografada e CPF de ponta a ponta (válido, inválido, repetido, sem máscara, duplicado). |
| `tests/Feature/ApiConteudoPublicoTest.php` | 11 | Endpoints REST e ausência de `senha`, `cpf`, `celular` e `email` nas respostas. |
| `tests/Feature/AssetsOfflineTest.php` | 11 | Nenhuma tela carrega CDN/fonte externa; com build presente, o CSS local é obrigatório e precisa conter as classes usadas nas telas (5 telas × 2 verificações + 1 do CSS compilado). |
| `tests/Feature/LoginLogoutApoiadorTest.php` | 11 | Guard `apoiador`, credenciais, sessão e logout. |
| `tests/Feature/MinhaContaTest.php` | 10 | Tela logada e correções de `$mensal->valor` / `$voluntario->area_interesse`. |
| `tests/Feature/ApoioUnicoTest.php` | 8 | Fluxo de doação única. |
| `tests/Feature/GestorDeAcessoTest.php` | 7 | Comando `gestor:senha` (promoção, e-mail case-insensitive, senha gerada, e-mail inexistente). |
| `tests/Feature/SuperficiePublicaDaApiTest.php` | 5 | Inventário das rotas: a lista de APIs públicas não muda por accidento e cada grupo de rota exige o middleware certo. |
| `tests/Feature/NewsletterTest.php` | 5 | `POST /api/newsletter` e validações. |
| `tests/Feature/ApiParaOSiteTest.php` | 4 | Leitura das APIs por outra origem e a pendência de CSRF/CORS. |
| `tests/Feature/ExampleTest.php` + `tests/Unit/ExampleTest.php` | 2 | Testes de exemplo do Laravel. |
| **Total** | **137** | |

| Validação manual | Status | Resultado |
|---|---|---|
| `npm run build` | 🟢 Aprovado | `public/build` gerado sem erro (aviso do pacote opcional `fontaine`, não bloqueia). |
| Renderização das telas com build | 🟢 Aprovado | `/entrar`, `/cadastro` e demais telas emitem `build/assets/app-*.css` e nenhum `cdn.tailwindcss`. |
| `vendor/bin/pint` nos arquivos alterados | 🟢 Aprovado | Estilo corrigido apenas nos arquivos tocados (sem reescrever o projeto inteiro). |

### 4.2 Pendência encontrada pelos testes: CSRF e CORS nas APIs

Ao escrever os testes de integração com o site, apareceram três coisas concretas:

1. **Não existe rota duplicada sem `/api`.** As linhas `/criancas`, `/apoiadores`… de `routes/web.php` estão dentro de `Route::prefix('api')`, ou seja, o caminho real já é `/api/criancas`. Nada está duplicado.
2. **`POST /api/newsletter` exige token de sessão (CSRF).** As APIs foram declaradas em `routes/web.php`, então recebem o grupo `web`, que inclui `PreventRequestForgery`. Os testes não perceivebem isso porque o Laravel pula a checagem de CSRF quando está rodando em modo de teste — **em produção, o envio da newsletter pelo site leva 419 (Token Mismatch)**. O teste `test_post_da_newsletter_da_para_o_site_sem_token_csrf` já falha sozinho assim que a rota for corrigida.
3. **CORS não está configurado** (não existe `config/cors.php`). Um site em outra origem não consegue ler a resposta do backend. O teste `test_origem_externa_recebe_permissao_cors` está marcado como *incompleto* até as origens permitidas serem definidas.

Correção sugerida (depende de decisão de produto: quais origens liberar):
* `config/cors.php` com `paths: ['api/*']` e as origens do site (dev + produção);
* tirar as rotas de API do grupo `web` (passá-las para `routes/api.php`) ou usar `$middleware->validateCsrfTokens(except: ['api/*'])`.

### 4.3 Sessão de 15/09 — Importação de Dados e Endpoints

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

### 4.4 Sessões anteriores — Autenticação

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
10. **Suíte automatizada de 137 testes** cobrindo autenticação, painel, APIs, seeders, domínio, CPF, inventário de rotas e assets (25/09).
11. **Painel de gestão protegido** por `EhGestor` (`tipo_usuario = admin`) + comando `gestor:senha` (25/09).
12. **APIs públicas sem dados pessoais** de apoiador (`senha`, `cpf`, `celular`, `email`) (25/09).
13. **Validação de CPF no cadastro público**, com gravação só em dígitos e resposta 422 (25/09).
14. **Telas Blade sem CDN**, com Tailwind 4 compilado pelo Vite e fontes do sistema (25/09).
15. **`$fillable` completos**, seed padrão ligado ao `OngDadosSeeder` e views mortas removidas (25/09).

---

### 🔴 Pendente — Alta Prioridade
1. **Configuração de CORS:** não existe `config/cors.php`, então o site em outra origem não consegue ler as respostas do backend. Definir as origens permitidas (dev e produção) e criar o arquivo.
2. **`POST /api/newsletter` retorna 419 em produção:** a rota está no grupo `web` e exige token de CSRF, que a SPA não tem como enviar. Mover as APIs para `routes/api.php` ou usar `$middleware->validateCsrfTokens(except: ['api/*'])`.
3. **Upload de Arquivos:**
   * Rota `POST /api/voluntarios` para envio de currículo (PDF) + validação de maioridade (+18 anos).
   * Rota de upload para `materiais_didaticos` e `documentos_transparencia`.
4. **Rota `GET /api/voluntarios`** (e demais endpoints REST de leitura que faltam) para expor as tabelas recém-importadas.
5. **Adaptar a tela de cadastro do frontend** ao novo CPF: o backend responde 422 e a SPA deve mostrar a mensagem do campo `cpf` (hoje a validação acontece no frontend, via API externa do inverterto).

---

### 🟡 Pendente — Média Prioridade
1. **Refatoração de Controllers:** Dividir o `DashboardTesteController.php` em controllers específicos por domínio (`NoticiaController`, `CriancaController`, `ApadrinhamentoController`).
2. **Integração de Meios de Pagamento:** Preparar a estrutura/webhooks de PIX e Cartão para doações.
3. **Autenticação Admin para a SPA (JWT / Sanctum):** o painel Blade já é protegido por sessão + `tipo_usuario = admin`; falta o equivalente para o frontend React (hoje não há endpoints administrativos de escrita expostos).

---

### 🟡 Pendente — Baixa Prioridade
1. **Paginação nas APIs REST:** Implementar paginação para listagens longas (ex.: notícias e galeria).
2. **Publicar a fonte Instrument Sans:** o build já gera o CSS da fonte, mas ele não é ligado no HTML; é preciso incluí-lo no `@vite` para as telas usarem a tipografia pretendida.

---

## 6. Histórico de Commits e Sincronização Git

* **Estado:** repositório com 70+ commits. O `origin/main` está em `73a6f2c`; o commit de ampliação de cobertura (CPF, inventário de rotas, integração com o site e CSS compilado) está apenas na máquina local.
* **Sessão de 25/09/2026 (backend):**
  * `8d53dfa test(backend): cria suite de testes do backend e corrige painel do apoiador`
  * `e299f36 fix(backend): protege escrita do painel, liga seed da ONG e completa fillable`
  * `01634ac fix(backend): restringe leitura do painel a gestao e remove views mortas`
  * `deb6526 fix(backend): tira dados pessoais das APIs e cria comando de acesso do gestor`
  * `b05c2b3 feat(backend): valida CPF no cadastro e guarda so digitos`
  * `264ebbb refactor(backend): troca CDN do Tailwind por build local do Vite`
  * `73a6f2c docs: atualiza relatorio do backend com testes, seguranca, Vite e CPF`
  * *(pendente de push)* `test(backend): amplia cobertura de CPF, rotas, integracao com o site e CSS compilado`
* **Sessão de 15/09/2026 (backend):**
  * `conexão.php` (correção do nome do banco)
  * `database/init.sql` (schema + dados completos)
  * Migrations novas: `2026_09_15_000001_create_ong_content_tables`, `2026_09_15_000002_create_ong_admin_tables`, `2026_09_15_000003_add_tipo_usuario_to_apoiadores_table`
  * `database/seeders/OngDadosSeeder.php`
  * `public/img/` (acervo de imagens da ONG)
* **Commits do frontend (`inter-ong/`, mantidos intactos nesta sessão):** `c6b813b`, `ed228c2`, `be114ee`, `10e5890`, `09326a1`.
* **Observação:** a suíte roda em SQLite e o build de assets é local; nenhum commit do backend depende de banco ou internet para ser validado.