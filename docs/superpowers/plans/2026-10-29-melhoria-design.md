# Melhoria de design do site e do painel — Plano de execução

> **Para quem executa:** REQUIRED SUB-SKILL: use superpowers:subagent-driven-development (recomendado) ou superpowers:executing-plans para executar tarefa por tarefa. Os passos usam caixas `- [ ]`.

**Objetivo:** site sem cortes no celular, sem texto invisível, com páginas no mesmo visual, e painel com resumo, busca e cartões mais claros.

**Arquitetura:** o painel é React (`inter-ong/src/pages/gestao/`) falando com Laravel (`backend/`). Resumo e busca ficam no servidor, porque as listas são paginadas (20 por página). Cores passam a vir só das variáveis de `inter-ong/src/index.css`.

**Stack:** React 19 + Tailwind 4 + Vite; Laravel + PHPUnit; testes do front com `node --test` (`inter-ong/tests/*.test.mjs`).

**Spec:** `docs/superpowers/specs/2026-10-29-melhoria-design-design.md` (ler junto, principalmente "Decidido", "Fatos do projeto" e "Decisões de quem escreveu").

## Restrições globais

- Branch novo `melhoria-design`, criado a partir de `mvp-lancamento`. Nunca direto no `main`.
- **Passos de commit só rodam com o dono autorizando.** Sem push. Mensagens curtas, uma por mudança lógica.
- Não mudar a paleta: tema escuro padrão `#090d16`, rosa `#fb2782` (`inter-ong/src/index.css`). Não reescrever textos; **única exceção**: selo "Pix Instantâneo" → "Doação por Pix" (`inter-ong/src/pages/Doar.jsx:135`).
- Sem bibliotecas novas sem explicar o motivo ao dono. Não ler nem mostrar `.env`. A pasta `.superpowers/` nunca entra em commit.
- PHP roda só pelo PowerShell (Herd); ver memória `ambiente-local-windows`. Depoimentos e fotos ficam como estão.
- Contraste mínimo 4.5:1 em texto comum, nos dois temas (escuro é o padrão). Zero rolagem horizontal em 390px.
- Mensagens e rótulos em português do Brasil, linguagem simples.

## Foco de revisão (casos que o spec implica e nenhum teste óbvio cobre)

1. Busca com `%`, `_` ou só espaços: deve tratar como texto comum, não como curinga nem listar tudo por engano.
2. Resumo sem nenhuma doação/voluntário: devolve zeros, nunca erro nem `null`.
3. Doação concluída no último dia do mês ou do mês anterior: só entra em "recebido no mês" a do mês corrente.
4. Voluntário/doador sem nome (cadastros antigos): cartão mostra "(sem nome)"; sem protocolo mostra "—".
5. Nome ou e-mail muito longo no cartão ou tela de 320px: nada vaza para fora.

---

### Tarefa 1: Resumo do painel no servidor

**Arquivos:**
- Criar: `backend/app/Http/Controllers/GestaoResumoController.php`
- Modificar: `backend/routes/integracao.php` (dentro do grupo `gestao`, perto da linha 49)
- Teste: `backend/tests/Feature/GestaoResumoTest.php` (usar `CriaCenarioOng`, como `GestaoDoacoesTest`)

**Interfaces:**
- Produz: `GET /api/v1/gestao/resumo` → `{"dados": {"doacoes_pendentes": int, "voluntarios_em_analise": int, "recebido_no_mes": float}}`. Só gestor autenticado (mesmo grupo das outras rotas `gestao`).

- [ ] **Passo 1:** criar o branch `melhoria-design` e rodar a suíte do backend atual para saber a linha de base. Esperado: tudo verde antes de mexer.
- [ ] **Passo 2:** escrever os testes que falham, com estes nomes e conferências:
  - `test_resumo_conta_pendentes_e_em_analise`: 2 doações `pendente`, 1 `concluido`, 3 voluntários `em_analise`, 1 `aprovado` → `doacoes_pendentes = 2`, `voluntarios_em_analise = 3`.
  - `test_recebido_no_mes_soma_so_concluidas_do_mes_corrente`: concluída de R$ 50 neste mês, concluída de R$ 30 no mês passado, pendente de R$ 99 neste mês → `recebido_no_mes = 50.0`.
  - `test_resumo_sem_dados_devolve_zeros`: banco vazio → `0`, `0`, `0.0`.
  - `test_resumo_exige_gestor`: sem login → 401/403 igual às outras rotas `gestao`.
- [ ] **Passo 3:** rodar e ver falhar (rota não existe).
- [ ] **Passo 4:** implementar `GestaoResumoController::show(): array` retornando `['dados' => [...]]`. "Mês corrente" = de `now()->startOfMonth()` até `now()->endOfMonth()` sobre `data_doacao`, status `concluido`, soma de `valor` (convertida para `float`). Sem migração.
- [ ] **Passo 5:** rodar os testes novos e a suíte inteira. Esperado: tudo verde.
- [ ] **Passo 6:** commit (só com autorização do dono): `feat: resumo do painel`.

### Tarefa 2: Busca nas listas do painel

**Arquivos:**
- Modificar: `backend/app/Http/Controllers/GestaoDoacoesController.php:12-23`, `backend/app/Http/Controllers/VoluntarioApiController.php` (método `index`, ~linha 52)
- Teste: acrescentar em `backend/tests/Feature/GestaoDoacoesTest.php` e `backend/tests/Feature/VoluntarioMvpTest.php`

**Interfaces:**
- Consumes: nada das tarefas anteriores.
- Produz: parâmetro opcional `busca` (texto, máx. 100 caracteres) em `GET /api/v1/gestao/doacoes` e `GET /api/v1/gestao/voluntarios`. Doações: procura em `nome_doador` e `protocolo`. Voluntários: procura no nome (coluna do voluntário e nome do apoiador ligado, porque cadastros antigos guardam o nome lá) e no e-mail. Combina com o filtro `status` e com a paginação.

- [ ] **Passo 1:** escrever os testes que falham:
  - `test_busca_encontra_doacao_por_nome_e_por_protocolo`
  - `test_busca_combina_com_filtro_de_status`
  - `test_busca_trata_percentual_e_sublinhado_como_texto`: buscar `%` não devolve tudo.
  - `test_busca_so_com_espacos_equivale_a_sem_busca`
  - `test_busca_de_voluntario_acha_nome_guardado_no_apoiador`
- [ ] **Passo 2:** rodar e ver falhar.
- [ ] **Passo 3:** implementar: validar `busca` como `nullable|string|max:100`, aplicar `trim`, escapar `%` e `_` antes do `LIKE`, ignorar se ficar vazio.
- [ ] **Passo 4:** rodar a suíte inteira. Esperado: tudo verde.
- [ ] **Passo 5:** commit (só com autorização): `feat: busca nas listas do painel`.

### Tarefa 3: Ajudantes do front (cliente da API e textos reserva)

**Arquivos:**
- Modificar: `inter-ong/src/api/backend.js:60-63`, `inter-ong/src/utils/gestao.js`
- Teste: `inter-ong/tests/gestao.test.mjs` (ver como os testes existentes importam `utils/`)

**Interfaces:**
- Consumes: rotas das Tarefas 1 e 2.
- Produz: `backend.gestaoResumo(options)`; `backend.gestaoDoacoes(status, pagina, options, busca = '')` e `backend.gestaoVoluntarios(pagina, options, busca = '')` (busca vai em `busca=` com `encodeURIComponent`, omitida se vazia); `textoOuReserva(valor: string | null | undefined, reserva: string): string` em `utils/gestao.js` (devolve `reserva` se vazio ou só espaços).

- [ ] **Passo 1:** escrever os testes que falham para `textoOuReserva`: `textoOuReserva('Ana', '(sem nome)') === 'Ana'`, `textoOuReserva('  ', '(sem nome)') === '(sem nome)'`, `textoOuReserva(null, '—') === '—'`.
- [ ] **Passo 2:** `cd inter-ong; npm test`. Esperado: falha (função não existe).
- [ ] **Passo 3:** implementar `textoOuReserva` e os métodos do `backend.js`.
- [ ] **Passo 4:** `npm test` e `npm run lint`. Esperado: verde.
- [ ] **Passo 5:** commit (só com autorização): `feat: ajudantes do painel no front`.

### Tarefa 4: Painel — resumo, busca e cartões

**Arquivos:**
- Modificar: `inter-ong/src/pages/gestao/GestaoLayout.jsx`, `GestaoDoacoes.jsx`, `GestaoVoluntarios.jsx`, `useLista.js` (aceitar a busca como parte da `chave`, com espera de ~300 ms antes de pedir ao servidor)
- Criar: `inter-ong/src/pages/gestao/ResumoPainel.jsx` (três números), `CampoBusca.jsx`
- Modificar: `docs/guia-painel-gestao.md`

**Interfaces:**
- Consumes: `backend.gestaoResumo`, parâmetro `busca`, `textoOuReserva`.
- Produz: `<ResumoPainel />` (sem props; busca e mostra os 3 números, com erro e carregando); `<CampoBusca valor onMudar rotulo />`.

- [ ] **Passo 1:** `ResumoPainel` no topo do `GestaoLayout` (acima das abas), três cartões: "Doações para confirmar", "Voluntários em análise", "Recebido neste mês" (formatado com `formatarReais`). Se o resumo falhar, mostrar aviso discreto sem derrubar o painel.
- [ ] **Passo 2:** `CampoBusca` nas abas Doações (rótulo "Buscar por nome ou protocolo") e Voluntários ("Buscar por nome ou e-mail"). Mudar a busca volta para a página 1.
- [ ] **Passo 3:** cartão de voluntário: nome em destaque no topo com `textoOuReserva(v.nome_completo, '(sem nome)')`; e-mail/celular com `'—'`; "Idade" só aparece com data de nascimento. Cartão de doação: protocolo com `'—'`.
- [ ] **Passo 4:** "Excluir" e "Apagar dados do doador" viram link discreto (texto pequeno, sem borda, cor neutra com hover vermelho), separado do botão principal; **manter** o `window.confirm` já existente.
- [ ] **Passo 5:** verificar de verdade: subir backend e front, entrar no painel, conferir (a) 3 números batem com o banco, (b) busca por nome, protocolo e `%`, (c) cartão de voluntário antigo sem nome, (d) 390px sem rolagem lateral, (e) tema escuro e claro. Tirar fotos com o Edge sem interface, como no levantamento. Dizer ao dono o que não foi possível testar.
- [ ] **Passo 6:** atualizar `docs/guia-painel-gestao.md` (resumo e busca). `npm run lint` e `npm run build` verdes.
- [ ] **Passo 7:** commit (só com autorização): `feat: painel com resumo, busca e cartões mais claros`.

### Tarefa 5: Cores centralizadas e texto visível nos dois temas

**Arquivos:**
- Modificar: `inter-ong/src/index.css`, `inter-ong/src/pages/css/Voluntariado.css`, `LandingPage.css`, `Doar.css`, `Contato.css`, `Sobre.css`, `Footer.css`, `auth.css`; `Servicos.jsx`, `Voluntariado.jsx`, `NavBar.jsx` onde houver cor escrita direto
- Teste: `inter-ong/tests/contraste.test.mjs`

**Interfaces:**
- Produz: no `index.css`, as variáveis existentes (`--bg-*`, `--text-*`, `--border*`, `--pink*`) como fonte única; nenhuma tela define cor própria para texto/fundo.

- [ ] **Passo 1:** escrever `contraste.test.mjs`: lê `index.css`, extrai os valores de `:root` e de `html.dark` e confere razão ≥ 4.5 para os pares `--text-primary`/`--bg-page`, `--text-primary`/`--bg-card`, `--text-secondary`/`--bg-page`, `--text-secondary`/`--bg-card`, `--text-muted`/`--bg-page` (cada um nos dois temas). Rodar; corrigir variável que falhar sem mudar `--bg-page` escuro nem `--pink`.
- [ ] **Passo 2:** corrigir os defeitos do levantamento, um a um: título "Mais que assistência…" e "Histórias que inspiram" (página inicial), "Vamos conversar?" e texto "Li e aceito a política de privacidade" (Voluntariado), botões "Quero ser voluntário" (Serviços) e "Doar Agora" (menu). Causa provável: faixas claras com texto que herda branco no tema escuro, e `Voluntariado.css` com paleta própria (`--pink`, `--dark`, `--text`). Trocar por variáveis do `index.css`; reduzir o uso de `!important` onde a correção não depender dele.
- [ ] **Passo 3:** conferir com o Edge sem interface, tema escuro e claro, 1440px e 390px, cada página citada. Nenhum texto sem leitura.
- [ ] **Passo 4:** `npm test`, `npm run lint`, `npm run build` verdes.
- [ ] **Passo 5:** commit (só com autorização): `fix: textos visíveis nos dois temas`.

### Tarefa 6: Celular sem rolagem lateral

**Arquivos:**
- Modificar: o que a investigação apontar (suspeitos do levantamento: textos decorativos "Mais inclusão / Mais oportunidades / Mais vidas" em `LandingPage.jsx`/`LandingPage.css`, botão "Doar" em `NavBar.jsx`, formulário de `Voluntariado.jsx`)

- [ ] **Passo 1:** escrever uma verificação descartável (fora do projeto, na pasta temporária) que abre cada rota do site e do painel no Edge sem interface a 390px e a 320px e informa as que têm `document.documentElement.scrollWidth > window.innerWidth`, com o elemento mais largo. Rodar e registrar a lista inicial.
- [ ] **Passo 2:** achar a causa real em cada página (elemento posicionado fora da tela, largura fixa, imagem sem limite) e corrigir na raiz; só usar `overflow-x: hidden` no `body` como último recurso e dizendo ao dono.
- [ ] **Passo 3:** repetir a verificação. Esperado: lista vazia a 390px e 320px, nos dois temas. Conferir que botão "Doar" e formulário de voluntário aparecem inteiros.
- [ ] **Passo 4:** commit (só com autorização): `fix: site cabe na tela do celular`.

### Tarefa 7: Páginas simples no visual da página inicial

**Arquivos:**
- Modificar: `Servicos.jsx`, `Privacidade.jsx`, `components/Newsletter.jsx`, `components/Footer.jsx` e os CSS correspondentes; depois as demais páginas de `inter-ong/src/pages/` que destoarem (Noticias, Educacional, Contato, Sobre) — quem executa faz o inventário e anota no commit quais mudou.

- [ ] **Passo 1:** listar as páginas e comparar com a página inicial (títulos, faixas, cartões, botões, espaçamentos). Reaproveitar `components/ui/Card.jsx` e `Button.jsx` em vez de criar estilos novos.
- [ ] **Passo 2:** aplicar o visual, sem mudar textos. Páginas curtas não deixam grande espaço vazio no fim (rodapé encosta no conteúdo).
- [ ] **Passo 3:** conferir as páginas com o Edge sem interface, dois temas, 1440px e 390px, e rodar a verificação da Tarefa 6 de novo.
- [ ] **Passo 4:** commit (só com autorização): `style: páginas simples no visual da página inicial`.

### Tarefa 8: Selo do Pix e menu do computador

**Arquivos:**
- Modificar: `inter-ong/src/pages/Doar.jsx:135`, `inter-ong/src/components/NavBar.jsx`

- [ ] **Passo 1:** trocar o texto do selo para "Doação por Pix". Nada mais de texto na página de doação.
- [ ] **Passo 2:** reduzir o menu do computador (hoje 9 itens + tema + Doar) agrupando os menos usados num item "Mais" (quem executa escolhe quais; registrar a escolha para o dono conferir). O menu do celular continua com todos os itens.
- [ ] **Passo 3:** conferir menu a 1440px e a 390px, nos dois temas; `npm run lint` e `npm run build`.
- [ ] **Passo 4:** commit (só com autorização): `style: selo do Pix e menu enxuto`.

### Fechamento

- [ ] Rodar de uma vez: suíte do backend, `npm test`, `npm run lint`, `npm run build`; refazer a verificação de celular em todas as rotas.
- [ ] Mostrar ao dono, em linguagem simples, o que mudou e como conferir; listar o que não deu para testar. Lembrar das pendências: depoimentos reais e confirmação das fotos com a ONG.
