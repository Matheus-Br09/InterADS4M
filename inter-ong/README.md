# React + Vite

This template provides a minimal setup to get React working in Vite with HMR and some ESLint rules.

Currently, two official plugins are available:

- [@vitejs/plugin-react](https://github.com/vitejs/vite-plugin-react/blob/main/packages/plugin-react) uses [Oxc](https://oxc.rs)
- [@vitejs/plugin-react-swc](https://github.com/vitejs/vite-plugin-react/blob/main/packages/plugin-react-swc) uses [SWC](https://swc.rs/)

## React Compiler

The React Compiler is not enabled on this template because of its impact on dev & build performances. To add it, see [this documentation](https://react.dev/learn/react-compiler/installation).

## Expanding the ESLint configuration

If you are developing a production application, we recommend using TypeScript with type-aware lint rules enabled. Check out the [TS template](https://github.com/vitejs/vite/tree/main/packages/create-vite/template-react-ts) for information on how to integrate TypeScript and [`typescript-eslint`](https://typescript-eslint.io) in your project.

## Integração React–Laravel (06/10/2026)

O front usa as rotas `/api/v1`, cookies de sessão e CSRF automático. O proxy do Vite
encaminha `/api` e `/img` para `BACKEND_PROXY_URL` (padrão `http://127.0.0.1:8000`).
Use `inter-ong/.env.example` como referência e deixe `VITE_BACKEND_URL` vazio com proxy.
O backend atual não permite credenciais em CORS: apontar diretamente para outra
origem não é suficiente para login. Em produção, encaminhe `/api` e `/img` pela
mesma origem da SPA e configure o fallback das rotas React para `index.html`.

Fluxos conectados: cadastro/login/logout, troca obrigatória e voluntária de senha,
edição de perfil, histórico e cancelamento de intenções pendentes, doações únicas e
mensais, apadrinhamento, inscrição/consulta de voluntariado com PDF, materiais
educativos e publicação por gestores, notícias e publicação/exclusão por gestores,
programas, transparência, estatísticas e newsletter. A gestão de materiais está
em `/gestao/materiais`; `/gestao` redireciona para ela.

No backend, aplique `php artisan migrate` para permitir `pendente` no status de
apadrinhamentos antes de usar esse fluxo. A migração preserva os registros existentes;
o rollback recusa reverter enquanto houver pendentes. Nenhum pagamento é realizado
pelo site. Foram removidos os códigos Pix fictícios e as confirmações simuladas.
As estatísticas ainda refletem o conteúdo do banco, incluindo demonstrações e
registros antigos que precisam de conciliação pela ONG.

Para desenvolvimento: execute `php artisan serve --host=127.0.0.1 --port=8000` em
`backend` e `npm run dev` em `inter-ong`. O banco e as demais migrations precisam
estar configurados conforme o README principal. Para verificar o front:
`npm test`, `npm run lint` e `npm run build` em `inter-ong`.
