# 📋 Plano de Melhorias do Frontend (`inter-ong`)

## 1. Visão Geral
Auditoria arquitetural, técnica e visual do frontend da aplicação **inter-ong** (React 19 + Vite 8 + Tailwind CSS 4) integrado à API Laravel da ONG SOS Tudo pelo Social. O objetivo deste plano é estruturar as etapas para eliminar gargalos de performance, modernizar a arquitetura de componentes, padronizar o design system e elevar a acessibilidade (WCAG AA).

- **Tipo de Projeto:** WEB (React SPA)
- **Status Atual:** 20 testes passando (`node --test`), linter sem erros (`eslint`), build funcional, porém com débito técnico arquitetural e de performance CSS.

---

## 2. Critérios de Sucesso
- [ ] Eliminação de travamentos de renderização causados por transições CSS universais (`*`).
- [ ] Modularização do monólito `LandingPage.jsx` (700+ linhas) em seções isoladas e testáveis.
- [ ] Criação de biblioteca de componentes base (`Button`, `Input`, `Select`, `Modal`, `Alert`) eliminando duplicações em formulários (`Doar`, `LoginECadastro`, `GestaoMateriais`, `PainelConta`).
- [ ] Substituição de diálogos nativos do navegador (`window.confirm`) por Modal acessível com foco gerenciado.
- [ ] Padronização de hooks de dados (`useApiQuery` / `useApiMutation`) eliminando boilerplate de `AbortController`.
- [ ] Remoção da pasta residual vazia `Front-end/` na raiz do projeto.
- [ ] Validação de acessibilidade (WCAG AA) para contraste nos elementos de destaque e estados de foco.

---

## 3. Stack Tecnológica
- **Framework:** React 19 + React Router DOM 7
- **Bundler:** Vite 8
- **Estilização:** Tailwind CSS 4 + Variáveis CSS semânticas (Dark / Light Mode)
- **Animações:** Framer Motion 13
- **Testes:** Node Test Runner (`node --test`)

---

## 4. Estrutura de Arquivos Proposta

```
inter-ong/src/
├── api/
│   └── backend.js               # Cliente HTTP e contratos da API
├── components/
│   ├── ui/                      # [NOVO] Design System Primitivo
│   │   ├── Button.jsx
│   │   ├── Input.jsx
│   │   ├── Select.jsx
│   │   ├── Modal.jsx
│   │   └── Card.jsx
│   ├── AvisoApi.jsx
│   ├── NavBar.jsx
│   ├── Footer.jsx
│   └── PainelConta.jsx
├── hooks/                       # [NOVO] Hooks Customizados
│   └── useApi.js                # Encapsula AbortController, loading e tratamento de erro
├── pages/
│   ├── landing/                 # [NOVO] Modularização da LandingPage
│   │   ├── LandingPage.jsx
│   │   └── sections/
│   │       ├── HeroSection.jsx
│   │       ├── ProjectsSection.jsx
│   │       ├── MetricsSection.jsx
│   │       ├── TestimonialsSection.jsx
│   │       └── FaqSection.jsx
│   ├── Doar.jsx
│   ├── LoginECadastro.jsx
│   └── GestaoMateriais.jsx
└── index.css                    # CSS Global otimizado sem seletor universal de transição
```

---

## 5. Divisão de Tarefas (Task Breakdown)

### Fase 1: Limpeza & Performance CSS Crítica
- **TASK-01: Otimização de Transições Globais no CSS**
  - **Agente:** `frontend-specialist`
  - **Skills:** `clean-code`, `frontend-architecture`
  - **Prioridade:** P0
  - **Dependências:** Nenhuma
  - **INPUT:** `inter-ong/src/index.css` (linhas 83-91 com `* { transition: ... }`)
  - **OUTPUT:** Remoção do seletor universal em `*` e substituição por transição direcionada apenas aos contêineres e componentes que necessitam de transição suave de tema.
  - **VERIFY:** Ausência de regras `* { transition }` no CSS; transição de tema funcional sem lag de digitação ou rolagem.

- **TASK-02: Limpeza do Diretório Residual `Front-end/`**
  - **Agente:** `orchestrator`
  - **Skills:** `clean-code`
  - **Prioridade:** P1
  - **Dependências:** Nenhuma
  - **INPUT:** Pasta `c:\Users\eloah\Desktop\Inter4°\InterADS4M\Front-end` vazia
  - **OUTPUT:** Remoção da pasta vazia para evitar confusão no repositório.
  - **VERIFY:** Pasta removida; integridade do git preservada.

---

### Fase 2: Componentes Primitivos de UI (Design System)
- **TASK-03: Criação dos Componentes Primitivos Acessíveis**
  - **Agente:** `frontend-specialist`
  - **Skills:** `clean-code`, `design-spec`, `web-design-guidelines`
  - **Prioridade:** P1
  - **Dependências:** TASK-01
  - **INPUT:** Padrões repetidos de formulários em `Doar.jsx`, `LoginECadastro.jsx` e `GestaoMateriais.jsx`.
  - **OUTPUT:** Componentes `<Button />`, `<Input />`, `<Select />` e `<Modal />` em `src/components/ui/` com suporte nativo a dark mode, acessibilidade ARIA e estados de erro.
  - **VERIFY:** Componentes aceitam ref, id, labels, aria-describedby e passam no lint.

- **TASK-04: Refatoração do `PainelConta.jsx` (Substituição de `window.confirm`)**
  - **Agente:** `frontend-specialist`
  - **Skills:** `clean-code`, `web-design-guidelines`
  - **Prioridade:** P1
  - **Dependências:** TASK-03
  - **INPUT:** `src/components/PainelConta.jsx` com `window.confirm` e `<details>` não estilizados.
  - **OUTPUT:** Modal acessível para confirmação de cancelamento de apoio e `<details>` / cards visualmente polidos no tema.
  - **VERIFY:** Cancelamento testado sem alertas nativos do navegador.

---

### Fase 3: Modularização e Arquitetura de Páginas
- **TASK-05: Decomposição da `LandingPage.jsx` em Seções**
  - **Agente:** `frontend-specialist`
  - **Skills:** `frontend-architecture`, `clean-code`
  - **Prioridade:** P2
  - **Dependências:** TASK-01
  - **INPUT:** `src/pages/LandingPage.jsx` (700 linhas) e `src/pages/css/LandingPage.css` (21 KB).
  - **OUTPUT:** Separação em seções modulares (`HeroSection`, `ProjectsSection`, `MetricsSection`, `TestimonialsSection`, `FaqSection`) com props explícitas e reutilizáveis.
  - **VERIFY:** `LandingPage.jsx` com menos de 150 linhas orquestrando os componentes; renderização idêntica ou aprimorada.

- **TASK-06: Criação do Custom Hook de Dados (`useApi`)**
  - **Agente:** `frontend-specialist`
  - **Skills:** `frontend-architecture`, `nextjs-react-expert`
  - **Prioridade:** P2
  - **Dependências:** Nenhuma
  - **INPUT:** Lógica repetida de `AbortController`, `carregando`, `erro`, `tentativa` em `Educacional.jsx`, `Doar.jsx`, `GestaoMateriais.jsx`, `Noticias.jsx`.
  - **OUTPUT:** Hook `useApiQuery(apiFn, deps, options)` e `useApiMutation(apiFn)` em `src/hooks/useApi.js`.
  - **VERIFY:** Redução de código boilerplate em ao menos uma página piloto (`Educacional.jsx`).

---

### Fase 4: Validação & Testes
- **TASK-07: Execução dos Testes e Linter**
  - **Agente:** `test-engineer`
  - **Skills:** `lint-and-validate`, `testing-patterns`
  - **Prioridade:** P0
  - **Dependências:** TASK-01 a TASK-06
  - **INPUT:** Projeto com modificações.
  - **OUTPUT:** `npm test` e `npm run lint` executados com 100% de sucesso.
  - **VERIFY:** 20+ testes passando sem quebras nos mocks ou no cliente `backend.js`.

---

## 6. Phase X: Verificação Final
- [ ] Executar `npm run lint` no diretório `inter-ong` (0 erros).
- [ ] Executar `npm test` no diretório `inter-ong` (20 testes passando).
- [ ] Executar `npm run build` no diretório `inter-ong` (build de produção sem avisos críticos).
- [ ] Validar conformidade de acessibilidade e ausência de travamentos em tema claro/escuro.
