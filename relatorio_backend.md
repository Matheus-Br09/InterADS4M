# 📋 Relatório de Desenvolvimento — Backend ONG SOS

**Projeto:** InterADS4M — Site da ONG SOS  
**Responsável pelo Backend:** Carlos  
**Data:** 11/09/2026; última atualização em **25/09/2026**  
**Tecnologias:** Laravel 13 (PHP 8.4), MySQL 8.0, Docker, Vite, Tailwind CSS 4, Blade  

---

## 1. Resumo do Progresso Nesta Sessão

### Sessão de 25/09/2026 — Suíte de testes, segurança do painel, Vite local e CPF
Fechamos o ciclo de qualidade e segurança do backend. Foi criada uma **suíte automatizada de 165 testes (651 assertions)** que roda em SQLite, cobrindo autenticação, painel, APIs, seeders, CPF, inventário de rotas e assets offline. Em paralelo, o painel em `/` deixou de ser público: ganhou o middleware `EhGestor`, que exige `tipo_usuario = admin`, e o comando `gestor:senha` resolve o acesso da gestão. As APIs públicas passaram a esconder `senha`, `cpf`, `celular` e `email` de qualquer apoiador. O cadastro público agora **valida o CPF no servidor** (dígitos verificadores, sem dígitos repetidos) e grava o campo só com números. Por fim, as telas Blade trocaram o `cdn.tailwindcss.com` por **build local do Vite**, para o sistema funcionar sem internet no pen drive. A cobertura extra revelou e resolveu dois bugs de integração com o site: **`POST /api/newsletter` exigia token de CSRF (419 em produção)** e não havia configuração de CORS (ver 4.2).

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

### 4.8 Sessão de 26/09 — Senha forte, bloqueio de conta e limite na doação

**Comando:** `cd backend && php artisan test` → **165 testes, 165 aprovados, 2.957 assertions**.

| Arquivo de teste | Testes | O que trava |
|---|---|---|
| `tests/Feature/BloqueioDeLoginTest.php` | 7 | 5 erros travam a conta por 5 minutos (mesmo quando a senha está certa), login certo zera o contador, o bloqueio é por conta e não derruba o login dos outros, e-mail inexistente trava igual a existente (a resposta não vaza quem tem conta), a tela diz quando volta e o e-mail digitado volta no formulário. |
| `tests/Feature/RotacaoDeSenhaTest.php` | 9 | As duas ferramentas de rotação, a senha gerada entra no painel, a antiga morre, sessão antiga é derrubada, seed sem hash previsível — e a regressão do gerador: 2.200 senhas seguidas sempre passam na regra de força, e o tamanho é 16 sem prefixo fixo. |
| `tests/Feature/CadastroApoiadorTest.php` | 16 | Cadastro, senha criptografada, CPF de ponta a ponta e agora a senha forte: recusa `12345678` (tamanho ok, sem variedade) e diz o que falta. |
| `tests/Feature/GestorDeAcessoTest.php` | 8 | `gestor:senha` promove e libera o painel, recusa senha curta e senha de 8 caracteres sem variedade, aceita e-mail com caixa alta e avisa quando a conta não existe. |
| `tests/Feature/LimiteDeRequisicoesTest.php` | 4 | Os cinco limites nomeados. O teste do limite de IP do login passou a usar e-mail malformado: com senha errada, a partir da 5ª vez quem responde é o bloqueio por conta e o teste mediria duas coisas ao mesmo tempo. |

**Achado de passagem (26/09):** `LoginECadastro.jsx` (SPA) ainda não chama o backend — posta para `NomeDoArquivoLogin.php` / `NomeDoArquivoParaCadastro.php`, que não existem, e valida CPF em `api.invertexto.com`. As proteções novas valem para o caminho Blade (`/cadastro`, `/entrar`), não para o formulário do site, e o CPF digitado ali está indo para um serviço de terceiro. Ver pendência 10 da seção 5.

### 4.1 Sessão de 25/09 — Suíte Automatizada

**Comando:** `cd backend && php artisan test` → **152 testes, 152 aprovados, 651 assertions** (número da sessão de 25/09; a suite está em 165 desde 26/09, ver 4.8) (banco SQLite em memória, sem depender do MySQL do Docker). Nenhum teste fica marcado como *incompleto*: o contrato de CORS passou a existir e é verificado de verdade.

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
| `tests/Feature/ApiParaOSiteTest.php` | 5 | Leitura das APIs por outra origem, envio da newsletter sem token de sessão, permissão de CORS e CSRF preservado nos formulários do backend. |
| `tests/Feature/LimiteDeRequisicoesTest.php` | 4 | Limite por IP na newsletter, no cadastro e no login, e limite geral das APIs. |
| `tests/Feature/ApiPublicaNaoExpoeDadoPessoalTest.php` | 3 | Nenhuma API pública devolve dado pessoal ou financeiro de apoiador/criança, em qualquer nível da resposta. |
| `tests/Feature/ExampleTest.php` + `tests/Unit/ExampleTest.php` | 2 | Testes de exemplo do Laravel. |
| **Total** | **145** | |

| Validação manual | Status | Resultado |
|---|---|---|
| `npm run build` | 🟢 Aprovado | `public/build` gerado sem erro (aviso do pacote opcional `fontaine`, não bloqueia). |
| Renderização das telas com build | 🟢 Aprovado | `/entrar`, `/cadastro` e demais telas emitem `build/assets/app-*.css` e nenhum `cdn.tailwindcss`. |
| `vendor/bin/pint` nos arquivos alterados | 🟢 Aprovado | Estilo corrigido apenas nos arquivos tocados (sem reescrever o projeto inteiro). |

### 4.2 CSRF e CORS: problema encontrado pelos testes e já corrigido

Ao escrever os testes de integração com o site, apareceram três coisas concretas:

1. **Não existe rota duplicada sem `/api`.** As linhas `/criancas`, `/apoiadores`… de `routes/web.php` estavam dentro de `Route::prefix('api')`, ou seja, o caminho real já era `/api/criancas`. Nada estava duplicado.
2. **`POST /api/newsletter` exigia token de sessão (CSRF).** As APIs estavam declaradas em `routes/web.php`, então recebiam o grupo `web`, que inclui `PreventRequestForgery`. Os testes não percebiam isso porque o Laravel pula a checagem de CSRF quando está rodando em modo de teste — **em produção, o envio da newsletter pelo site levaria 419 (Token Mismatch)**.
3. **CORS não existia** (não havia `config/cors.php`). Um site em outra origem não conseguia ler a resposta do backend.

Como o projeto ainda está em desenvolvimento, as duas pendências foram resolvidas agora:

| Correção | O que foi feito |
|---|---|
| CSRF | As 8 rotas de API saíram do grupo `web` e foram para `backend/routes/api.php` (grupo `api`: sem cookie de sessão, sem token de CSRF). Os URLs continuam iguais (`/api/...`), o painel e os formulários Blade seguem com CSRF. |
| CORS | Criado `backend/config/cors.php` com `paths: ['api/*']` liberando qualquer origem (`allowed_origins: ['*']`), próprio do desenvolvimento. |

Os testes agora travam esse acordo nos dois sentidos, com o middleware efetivo da rota (grupos `web`/`api` abertos):

* `test_o_site_nao_precisa_de_token_de_sessao_para_postar` — `POST /api/newsletter` **não** pode voltar a exigir CSRF;
* `test_os_formularios_do_backend_continuam_com_protecao_de_csrf` — `POST /cadastro`, `/entrar`, `/apoio-unico` e `/criancas/salvar` **precisam** continuar protegidos;
* `test_leitura_da_api_vem_com_permissao_para_o_site_leer` e `test_o_site_pode_enviar_a_newsletter_de_outra_origem` — resposta com `Access-Control-Allow-Origin`.

> ⚠️ **Antes de publicar:** trocar `allowed_origins: ['*']` em `config/cors.php` pela origem real do site (ex.: `['https://ongsos.org.br']`). Sem isso, qualquer página da internet poderia ler as respostas da API.

**Ainda pendente:** antes de publicar, trocar `allowed_origins: ['*']` pela origem real do site (ver 5).

### 4.3 Rate limiting — nenhuma rota escrevia sem limite

A instalação não tinha limite de requisições em lugar nenhum: a `POST /api/newsletter` (pública, grava no banco) aceitava chamadas infinitas e o `POST /entrar` permitia testar senha em massa. Agora os limites ficam nomeados em `backend/app/Providers/AppServiceProvider.php`:

| Limite | Onde | Valor | Motivo |
|---|---|---|---|
| `api` | grupo `api` inteiro (`$middleware->throttleApi('api')`) | 120/min por IP | rede de segurança para todas as leituras públicas |
| `newsletter` | `POST /api/newsletter` | 5/min por IP | é pública e grava no banco |
| `cadastro` | `POST /cadastro` | 5/min por IP | evita cadastro em massa |
| `login` | `POST /entrar` | 10/min por IP | evita tentativa de senha em massa |

Os limites são **nomeados** de propósito: dois `throttle:5,1` inline na mesma rota usam a mesma chave de cache, o contador é somado duas vezes por requisição e o limite efetivo cai pela metade. Isso aconteceu na primeira versão desta implementação (o 3o envio da newsletter já levava 429 em vez do 6o) e o teste da newsletter pega a regressão.

### 4.4 Auditoria de segurança (25/09) — APIs públicas expunham dado pessoal

A auditoria achou um problema crítico: as rotas de `routes/api.php` são públicas, e três delas devolviam o model inteiro. `Apoiador::$hidden` cobria só `senha`, `cpf`, `celular` e `email` — **ficavam expostos** nome completo, sexo, endereço residencial completo, `tipo_usuario` (revelava quem é a gestão) e os valores doados. `Crianca` não tinha `$hidden` nenhum: nome, data de nascimento e o campo `historico` (texto às vezes clínico) saíam em público. `Apadrinhamento` ligava **quem apadrinha, qual criança e quanto paga**.

Como o `config/cors.php` está liberado para qualquer origem em desenvolvimento, qualquer página da internet podia enumerar tudo isso.

O que mudou:

| Endpoint | Antes | Agora |
|---|---|---|
| `GET /api/apoiadores` | lista com nome, endereço, voluntariado e valores pagos | só os números da transparência: `total`, `doadores_mensais`, `doadores_unicos`, `total_mensal`, `total_unico` |
| `GET /api/criancas` | nome, data de nascimento, histórico e a relação de apadrinhamento com o apoiador | nome, status real (apadrinhamento ativo), imagem, **idade** e `apadrinhada` |
| `GET /api/apadrinhamentos` | apoiador (com nome), `valor_mensal`, criança | criança, status, recompensas enviadas (sem autor e sem valor) |

As listas passam a ser montadas **campo a campo** no controller, e não com `Model::get()`: devolver o model inteiro faria qualquer coluna nova vazar sem ninguém perceber. Como segunda rede, `historico`/`data_nascimento` entraram no `$hidden` da `Crianca` e endereço + `tipo_usuario` no `$hidden` do `Apoiador` (isso não afeta o painel: as views Blade leem os atributos direto, e há teste guaranteeing).

`tests/Feature/ApiPublicaNaoExpoeDadoPessoalTest.php` (3 testes) varre a resposta inteira dos 7 endpoints públicos, em qualquer nível de aninhamento, procurando 22 chaves proibidas (`senha`, `cpf`, `email`, `logradouro`, `historico`, `data_nascimento`, `valor`, `tipo_usuario`…): uma coluna ou relação nova não passa sem o teste reclamar.

> **Decisão de produto pendente:** o nome das crianças continua público (o site precisa para apresentar o apadrinhamento). Publicar nome de menor exige autorização dos responsáveis — a ONG confirmou que tem a autorização, mas ela precisa estar **registrada** (ficha de consentimento assinada, com data e prazo de uso), e não só no entendimento da equipe. Também não há "mural de apoiadores" público; se a ONG quiser, isso deve ser uma lista curada, não a tabela inteira.

### 4.5 Credenciais de pessoas reais no repositório (25/09) — rotação de senha

Pior que a API: o `backend/database/init.sql` e o `OngDadosSeeder` estavam versionados num repositório **público** com dados de pessoas reais — nome, CPF, celular, endereço e, o pior, **hash bcrypt da senha** (12 contas, incluindo a conta de gestão). Com o repositório aberto, esses hashes são hashes de senha como qualquer outro: dá para testar senha contra eles indefinidamente, e é assim que senhas reaproveitadas caem.

Além disso, os dois seeders escreviam senha **conhecida** (`Hash::make('senha123')` no `DatabaseSeeder`), o que transformava qualquer instalação que rodasse o seed numa conta com senha pública.

O que mudou:

| Antes | Agora |
|---|---|
| E-mails reais (a conta da gestão e dois e-mails pessoais) e hash de senha fixo no seeder | e-mails de exemplo (`@exemplo.org`), nomes e CPFs sintéticos, celular/endereço fictício |
| `Hash::make('senha123')` escrito no `DatabaseSeeder` | senha nasce **inutilizável** (`Hash::make(SenhaForte::gerar())`) e o seed imprime os comandos para definir a senha de cada conta |
| `gestor:senha` gerava `Gestor` + 4 dígitos (9.000 combinações) | `SenhaForte::gerar()`: 16 caracteres de um alfabeto sem caractere ambíguo (`il1oO0`), ~96 bits |
| Só existia `gestor:senha` (que **promove** a conta a admin) | novo `apoiador:senha <email> [senha]`, que redefine a senha **sem** mudar o papel e encerra as sessões que ainda valem com a senha antiga |
| `backend/database/init.sql` versionado com PII, e `docker-compose.yml` montando esse dump no MySQL | dump removido do repositório (e do histórico) e `.gitignore` impedindo novo dump versionado; o schema passa a vir só de `php artisan migrate` |

O dump também era uma **fonte de bug**: ele dizia `cep` NOT NULL sem default, enquanto a migration diz `cep` nullable. Quem instalou pelo Docker e rodou o seed em 11/09 tomou erro 1364 no cadastro público (`/cadastro`), e o Laravel logou o SQL com os valores — CPF e hash bcrypt de uma pessoa real foram parar no `storage/logs/laravel.log`. Ter duas definições de schema foi o que causou o erro; agora há uma só.

A queda da sessão é feita decodificando o payload em base64 de `sessions` e procurando a chave `login_apoiador_<id>` — o `LIKE` no payload não funciona porque o `DatabaseSessionHandler` do Laravel grava base64 (achado no teste, que usa o mesmo formato do framework).

`tests/Feature/RotacaoDeSenhaTest.php` (7 testes) cobre a senha gerada (16 caracteres, sem caractere ambíguo, e que entra no painel), a troca da senha antiga, o `apoiador:senha` sem promoção de papel, o encerramento da sessão alheia que **não** pode ser derrubada, a recusa de senha fraca, a garantia de que **nenhuma** conta criada pelo seed aceita senha previsível, e uma guarda que reprova qualquer `$2y$` ou e-mail de domínio real escrito em um seeder.

> **O que o rework não desfaz:** quem já clonou o repositório antes mantém os arquivos, e o GitHub segura commits órfãos por um tempo. A rotação de senha é a parte que protege de fato — por isso ela é o passo obrigatório depois do push reescrito.


### 4.6 Sessão de 15/09 — Importação de Dados e Endpoints

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

### 4.7 Sessões anteriores — Autenticação

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
9. **Geração do dump `database/init.sql`** com schema + dados para subir o MySQL pelo Docker. Em 25/09 esse arquivo saiu do repositório: carregava dado de pessoa real e divergia das migrations (ver 4.5) (15/09).
10. **Suíte automatizada de 165 testes** cobrindo autenticação, painel, APIs, seeders, domínio, CPF, inventário de rotas e assets (25/09).
11. **Painel de gestão protegido** por `EhGestor` (`tipo_usuario = admin`) + comando `gestor:senha` (25/09).
12. **APIs públicas sem dados pessoais** de apoiador (`senha`, `cpf`, `celular`, `email`, endereço e `tipo_usuario`) e sem dado sensível de criança (`historico`, `data_nascimento`), com lista montada campo a campo e teste que varre a resposta inteira (25/09).
13. **Validação de CPF no cadastro público**, com gravação só em dígitos e resposta 422 (25/09).
14. **Telas Blade sem CDN**, com Tailwind 4 compilado pelo Vite e fontes do sistema (25/09).
15. **`$fillable` completos**, seed padrão ligado ao `OngDadosSeeder` e views mortas removidas (25/09).
16. **APIs movidas para `routes/api.php`** (sem CSRF) e **`config/cors.php` criado** liberando origem em desenvolvimento (25/09).
17. **Rate limiting por IP** em todas as rotas: 120/min na API, 5/min na newsletter, 5/min no cadastro, 10/min no login e 10/min na doa\u00e7\u00e3o \u00fanica (25/09, doacao-unica em 26/09).
18. **Auditoria de segurança das 19 frentes** (SQLi, IDOR, XSS, SSRF, upload, cookies, CSRF, CORS, LGPD, força bruta, rate limit, arquivos expostos): sem SQL injection, IDOR, XSS, SSRF nem upload; 3 problemas críticos corrigidos (itens 12, 19 e 20) (25/09).
19. **Credenciais de pessoas reais fora do repositório:** `database/init.sql` removido (do repositório e do histórico), seeds com e-mail de exemplo e senha inutilizável, `SenhaForte` (16 caracteres, sem caractere ambíguo) e novo comando `apoiador:senha` para redefinir senha sem promover a gestor e derrubar a sessão antiga (25/09).
20. **Senha forte e bloqueio de conta (26/09):** `min:6` no cadastro p\u00fablico aceitava `123456`; agora cadastro e os dois comandos de rota\u00e7\u00e3o exigem 8 caracteres com mai\u00fascula, min\u00fascula e n\u00famero (`app/Rules/SenhaForte.php`). O login trava a conta por 5 minutos depois de 5 erros, com mensagem dizendo quando volta, e o login certo zera o contador (`tests/Feature/BloqueioDeLoginTest.php`, 7 testes). Um e-mail inexistente trava igual a um existente, para a resposta n\u00e3o revelar quais contas existem. `POST /apoio-unico` ganhou limite de 10/min; `POST /sair` ficou sem limite de prop\u00f3sito (n\u00e3o consome recurso e um 429 ali deixaria o apoiador preso logado, com tela de erro no lugar do logout). Corrigido de passagem um **bug do gerador**: uma em cada dez senhas geradas sa\u00eda sem nenhum d\u00edgito (s\u00f3 8 dos 61 caracteres do alfabeto s\u00e3o n\u00famero) e era reprovada pela pr\u00f3pria regra de for\u00e7a, ent\u00e3o `gestor:senha` sem argumento falhava ao acaso; o gerador agora garante um caractere de cada classe e embaralha, com teste de 2.200 amostras.

---

### 🔴 Pendente — Alta Prioridade
1. **Rotacionar as senhas de verdade:** o histórico do git foi reescrito e os seeds foram limpos, mas quem clonou o repositório antes ainda tem os hashes de 12 contas. Rodar `php artisan gestor:senha gestor@exemplo.org` e `php artisan apoiador:senha <email>` em cada conta real é o passo que fecha o problema — e vale avisar os apoiadores para trocarem a senha em outros serviços onde usaram a mesma.
2. **Registrar a autorização dos responsáveis** das crianças, por escrito (nome da criança, finalidade, data e prazo), já que o nome completo segue público na API (25/09).
3. **Fechar o CORS antes de publicar:** em desenvolvimento o `config/cors.php` libera qualquer origem (`allowed_origins: ['*']`). Trocar pela origem real do site antes de ir ao ar.
4. **`APP_DEBUG=false`:** hoje está `true` no `.env` **e no `.env.example`**. O `LOG_LEVEL=debug` não é a causa do CPF no log: quem gravou foi o Laravel, ao logar o SQL com os valores quando um insert falhou (11/09). O `storage/logs/laravel.log` tem 1,7 MB com CPF e hash de senha de uma pessoa real, sem rotação — apagar e passar a limpar antes de compartilhar o pen drive.
5. **Cookies e HTTPS:** `SESSION_SECURE_COOKIE` não existe no `.env` nem no `.env.example` (cookie sem flag `Secure`), sem HSTS, sem cabeçalhos de segurança e sem `trustProxies`.
6. **Força bruta e senha fraca (parcialmente resolvido em 26/09):** ver item 21 do que já foi feito - a senha fraca e o bloqueio por conta entraram. Continua de pé: o bloqueio é por IP **e** por conta, mas um atacante distribuído ainda tem 5 tentativas por IP.
7. **Conferir duas fotos do acervo antes de publicar.** Nenhuma das 13 imagens do repositório tem EXIF (sem câmera, GPS ou data), o que é compatível com banco de imagens — mas não prova nada, porque exportar pelo WhatsApp/Instagram também remove metadado. As 7 da landing (`inter-ong/src/assets/`) entraram no `b2ea7d3 landing page 16/09`, antes de qualquer importação: origem duvidosa. As 6 do acervo (`backend/public/img/`) entraram no `ee1dfcc` e são da ONG — as duas que precisam de olhar humano são `materia_1789498189.jpg` (capa do programa, 1200x1600, formato retrato) e `recompensa_1789499207.png` (mídia de recompensa, 738x414). Se tiverem pessoa identificável, trocar por imagem genérica antes de ir ao ar.
8. **Upload de Arquivos:**
   * Rota `POST /api/voluntarios` para envio de currículo (PDF) + validação de maioridade (+18 anos).
   * Rota de upload para `materiais_didaticos` e `documentos_transparencia`. Nome de arquivo gerado pelo servidor (nunca do usuário), `mimes` + `max` e fora do `public/` quando não for para ser servido.
9. **Rota `GET /api/voluntarios`** (e demais endpoints REST de leitura que faltam) para expor as tabelas recém-importadas.
10. **Adaptar a tela de cadastro do frontend** ao novo CPF: o backend responde 422 e a SPA deve mostrar a mensagem do campo `cpf` (hoje a validação acontece no frontend, via API externa do inverterto). **Pior do que aparente (achado em 26/09):** essa tela ainda não chama o backend - o `handleSubmit` faz `fetch('NomeDoArquivoLogin.php')` e `fetch('NomeDoArquivoParaCadastro.php')`, dois nomes de arquivo que não existem, e a validação de CPF vai para `https://api.invertexto.com/api-validador-cpf-cnpj/`. Ou seja: (a) o cadastro público com senha forte, CPF e bloqueio de conta **não existe para quem usa o site** - ele existe no Blade `/cadastro`; (b) o CPF digitado no formulário é enviado a uma empresa de terceiro sem qualquer aviso ao usuário - diferente dos links de Instagram, do mapa do Google e do PDF do gov.br, que só levam o visitante embora, e também sem necessidade, porque o backend já valida CPF por dígito verificador (`App\Rules\Cpf`); (c) o `setIsLogin(true)` após o cadastro é estado que não existe no componente. Enquanto isso não for ligado, as proteções do item 20 protegem o caminho Blade, não o caminho do site.

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

* **Estado:** repositório com 78 commits, `main` e `origin/main` no mesmo commit.
* **Atenção (25/09):** o histórico foi **reescrito** para apagar o dump com PII e as credenciais de seed, o que trocou o SHA de todos os commits. Os SHAs abaixo são os **novos**; qualquer referência a SHA antigo (em issue, PR ou anotação) não vale mais. Quem já tinha clonado precisa atualizar com `git fetch && git reset --hard origin/main`.
* **Sessão de 26/09/2026 (backend), do mais recente para o mais antigo:**
  * `a36b44d` docs: detalha a checagem das fotos do acervo
  * `39f2804` docs: registro da auditoria, pendências e rotação de senha
  * *(este commit)* feat(backend): senha forte, bloqueio de conta e limite na doação
* **Sessão de 25/09/2026 (backend), do mais recente para o mais antigo:**
  * `46724b5` fix(backend): tira credencial de pessoa real do repositorio
  * `9c125af` fix(backend): tira dado pessoal das APIs publicas
  * `78097d8` feat(backend): limita requisicoes por IP nas rotas de escrita
  * `88796c9` fix(backend): tira APIs do CSRF e configura CORS para desenvolvimento
  * `1ac32bd` test(backend): amplia cobertura de CPF, rotas, integracao com o site e CSS compilado
  * `5c7f974` docs: atualiza relatorio do backend com testes, seguranca, Vite e CPF
  * `b07c378` refactor(backend): troca CDN do Tailwind por build local do Vite
  * `444f38d` feat(backend): valida CPF no cadastro e guarda so digitos
  * `b77d984` fix(backend): tira dados pessoais das APIs e cria comando de acesso do gestor
  * `a0ddf54` fix(backend): restringe leitura do painel a gestao e remove views mortas
  * `806e8be` fix(backend): protege escrita do painel, liga seed da ONG e completa fillable
  * `bb8fa53` test(backend): cria suite de testes do backend e corrige painel do apoiador
* **Sessão de 15/09/2026 (backend):**
  * `conexão.php` (correção do nome do banco)
  * ~~`database/init.sql`~~ (removido do repositório em 25/09 — ver 4.5; o schema ficou só nas migrations)
  * Migrations novas: `2026_09_15_000001_create_ong_content_tables`, `2026_09_15_000002_create_ong_admin_tables`, `2026_09_15_000003_add_tipo_usuario_to_apoiadores_table`
  * `database/seeders/OngDadosSeeder.php`
  * `public/img/` (acervo de imagens da ONG)
* **Commits do frontend (`inter-ong/`, mantidos intactos nesta sessão):** `fefe70b` (centraliza itens do login), `5eff3c1` (endereço no login/Contato/Sobre), `0a08226` (useState por componente de endereço), `d665b30` (molde do `fetch` para o backend).
* **Observação:** a suíte roda em SQLite e o build de assets é local; nenhum commit do backend depende de banco ou internet para ser validado.
