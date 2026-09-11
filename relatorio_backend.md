# 📋 Relatório de Desenvolvimento — Backend ONG SOS

**Projeto:** InterADS4M — Site da ONG SOS  
**Responsável pelo Backend:** Carlos  
**Data:** 11/09/2026  
**Tecnologias:** Laravel (PHP 8.4), MySQL 8.0, Docker, Tailwind CSS, Blade  

---

## 1. Resumo do Progresso Nesta Sessão

Nesta sessão, avançamos na estruturação e validação do **módulo de autenticação e área logada do Apoiador**. A infraestrutura de autenticação isolada (utilizando Custom Guards do Laravel) foi totalmente integrada e testada no banco de dados e na camada de visualização (views/controllers).

---

## 2. Infraestrutura e Banco de Dados

### 2.1 Container Docker (MySQL)
O banco de dados continua rodando em ambiente isolado via Docker:
- **Database:** `ong`
- **Porta:** `3306`
- **Ambiente:** Padronizado para toda a equipe.

### 2.2 Estrutura de Tabelas e Modificações
* **Tabela `apoiadores`:** Ajustada e sincronizada para suporte à autenticação própria.
* **Tabelas Adicionadas Anteriormente:** `administradores`, `noticias`, `materiais_didaticos`, `documentos_transparencia` e `newsletter`.
* **Tratamento de Dependências Relacionadas:** Implementado tratamento preventivo no controller (`Schema::hasTable`) para as tabelas `doacoes_unicas`, `doacoes_mensais`, `apadrinhamentos` e `voluntarios`, garantindo que o painel do apoiador carregue sem erros mesmo se algumas tabelas secundárias ainda não tiverem sido migradas no banco local.

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

---

### 🔴 Pendente — Alta Prioridade
1. **Configuração de CORS:** Liberar o backend para permitir requisições HTTP do frontend em React/Next.js.
2. **Migrations e Tabelas de Doações/Voluntariado:** Executar e relacionar as tabelas `doacoes_unicas`, `doacoes_mensais`, `apadrinhamentos` e `voluntarios` para que os dados apareçam no painel.
3. **Autenticação Admin (JWT / Sanctum):** Painel administrativo restrito para a gestão de conteúdos por Carol e Flávio.
4. **Upload de Arquivos:**
   * Rota `POST /api/voluntarios` para envio de currículo (PDF) + validação de maioridade (+18 anos).
   * Rota de upload para `materiais_didaticos` e `documentos_transparencia`.

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