# React + Vite

This template provides a minimal setup to get React working in Vite with HMR and some ESLint rules.

Currently, two official plugins are available:

- [@vitejs/plugin-react](https://github.com/vitejs/vite-plugin-react/blob/main/packages/plugin-react) uses [Oxc](https://oxc.rs)
- [@vitejs/plugin-react-swc](https://github.com/vitejs/vite-plugin-react/blob/main/packages/plugin-react-swc) uses [SWC](https://swc.rs/)

## React Compiler

The React Compiler is not enabled on this template because of its impact on dev & build performances. To add it, see [this documentation](https://react.dev/learn/react-compiler/installation).

## Expanding the ESLint configuration

If you are developing a production application, we recommend using TypeScript with type-aware lint rules enabled. Check out the [TS template](https://github.com/vitejs/vite/tree/main/packages/create-vite/template-react-ts) for information on how to integrate TypeScript and [`typescript-eslint`](https://typescript-eslint.io) in your project.

## Integração local com o Laravel

O frontend encaminha `/api` para `http://127.0.0.1:8000`. Mantenha o Laravel e o frontend abertos em terminais separados:

```powershell
# Na pasta backend, com PHP e banco já configurados:
php artisan serve --host=127.0.0.1 --port=8000

# Na pasta inter-ong:
npm run dev
```

Acesse `/login` no endereço exibido pelo Vite. Cadastro, login, recuperação da sessão, saída e troca obrigatória de senha usam a API Laravel. O cadastro valida CPF no backend e já inicia a sessão. Os erros de validação aparecem junto aos campos.

Se o backend usar outra porta, copie `.env.example` para `.env.local`, ajuste `BACKEND_PROXY_URL` e reinicie o Vite. Deixe `VITE_BACKEND_URL` vazio para usar o proxy e os cookies na mesma origem. Não coloque credenciais de banco no frontend.

O proxy também funciona em `npm run preview`. Para publicar o build, configure o servidor da hospedagem para encaminhar `/api` ao Laravel: o proxy do Vite não faz parte dos arquivos compilados. Acesso direto a outra origem com `VITE_BACKEND_URL` exige configuração de CORS com credenciais e cookies compatíveis no backend; não é a configuração local padrão.

```powershell
npm test
npm run lint
npm run build
```

Os testes do cliente usam respostas simuladas e o teste de proxy usa um servidor HTTP temporário. Eles não substituem a validação com Laravel e MySQL em execução.
