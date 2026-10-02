import assert from 'node:assert/strict'
import { afterEach, mock, test } from 'node:test'
import { backend } from '../src/api/backend.js'

afterEach(() => mock.restoreAll())
const json = (data, status = 200) => Response.json(data, { status })
function responder(respostas) {
  return mock.method(globalThis, 'fetch', async () => {
    assert.ok(respostas.length, 'Requisição inesperada')
    return respostas.shift()
  })
}

test('login usa cookie e CSRF; logout usa o token renovado depois do login', async () => {
  const fetch = responder([
    json({ token: 'antes' }), json({ dados: { id: 1 } }),
    json({ token: 'depois' }), new Response(null, { status: 204 }),
  ])
  await backend.entrar({ email: 'teste@example.org', senha: 'Teste123' })
  assert.equal(await backend.sair(), null)
  const calls = fetch.mock.calls.map(({ arguments: args }) => args)
  assert.equal(calls[1][0], '/api/v1/auth/entrar')
  assert.equal(calls[1][1].credentials, 'include')
  assert.equal(calls[1][1].headers['X-CSRF-TOKEN'], 'antes')
  assert.deepEqual(JSON.parse(calls[1][1].body), { email: 'teste@example.org', senha: 'Teste123' })
  assert.equal(calls[3][1].headers['X-CSRF-TOKEN'], 'depois')
})

test('cadastro envia os dados pelo POST correto', async () => {
  const fetch = responder([json({ token: 'csrf' }), json({ dados: { id: 1 } }, 201)])
  const dados = { nome_completo: 'Teste', email: 'teste@example.org', cpf: '11122233387', senha: 'Teste123', senha_confirmation: 'Teste123', celular: '81999999999', logradouro: 'Rua Teste' }
  await backend.cadastrar(dados)
  const [url, options] = fetch.mock.calls[1].arguments
  assert.equal(url, '/api/v1/auth/cadastro')
  assert.equal(options.method, 'POST')
  assert.deepEqual(JSON.parse(options.body), dados)
})

test('erros de validação preservam campos e não repetem o cadastro', async () => {
  const fetch = responder([json({ token: 'csrf' }), json({ message: 'CPF inválido.', errors: { cpf: ['CPF inválido.'] } }, 422)])
  await assert.rejects(backend.cadastrar({}), (error) => {
    assert.equal(error.status, 422)
    assert.deepEqual(error.errors, { cpf: ['CPF inválido.'] })
    return true
  })
  assert.equal(fetch.mock.callCount(), 2)
})

test('recupera CSRF expirado e repete uma única vez', async () => {
  const fetch = responder([json({ token: 'antigo' }), json({}, 419), json({ token: 'novo' }), json({ dados: {} })])
  await backend.trocarSenha({ senha: 'Nova1234', senha_confirmation: 'Nova1234' })
  assert.equal(fetch.mock.calls[3].arguments[1].headers['X-CSRF-TOKEN'], 'novo')
  assert.equal(fetch.mock.calls[3].arguments[1].method, 'PUT')
})

test('419 persistente termina sem loop', async () => {
  const fetch = responder([json({ token: 'a' }), json({}, 419), json({ token: 'b' }), json({}, 419)])
  await assert.rejects(backend.sair(), { status: 419 })
  assert.equal(fetch.mock.callCount(), 4)
})

test('restauração da conta preserva o código de troca obrigatória', async () => {
  const fetch = responder([json({ codigo: 'troca_senha_obrigatoria', message: 'Troque sua senha.' }, 403)])
  await assert.rejects(backend.conta(), { status: 403, codigo: 'troca_senha_obrigatoria' })
  assert.equal(fetch.mock.callCount(), 1)
})

test('401 e 429 preservam status e não são repetidos', async () => {
  responder([json({}, 401), json({ token: 'a' }), json({ message: 'Aguarde.', tentar_em: 60 }, 429)])
  await assert.rejects(backend.conta(), { status: 401 })
  await assert.rejects(backend.entrar({}), { status: 429, tentarEm: 60 })
})

test('HTML no lugar de API gera diagnóstico legível', async () => {
  responder([new Response('<html>Vite</html>', { headers: { 'Content-Type': 'text/html' } })])
  await assert.rejects(backend.conta(), /configuração da API/)
})

test('falha de rede tem mensagem legível e não repete gravação', async () => {
  const fetch = mock.method(globalThis, 'fetch', async () => { throw new TypeError('Failed to fetch') })
  await assert.rejects(backend.cadastrar({}), /conectar ao servidor/)
  assert.equal(fetch.mock.callCount(), 1)
})

test('upload preserva FormData para o navegador definir boundary', async () => {
  const fetch = responder([json({ token: 'a' }), json({ dados: {} }, 201)])
  const formulario = new FormData()
  formulario.append('nome', 'Teste')
  await backend.inscreverVoluntario(formulario)
  const options = fetch.mock.calls[1].arguments[1]
  assert.equal(options.body, formulario)
  assert.equal(options.headers['Content-Type'], undefined)
})

test('cancelamento da leitura não vira erro de conexão', async () => {
  mock.method(globalThis, 'fetch', async () => { throw new DOMException('Cancelado', 'AbortError') })
  await assert.rejects(backend.conta(), { name: 'AbortError' })
})

test('materiais respeitam a página e permitem cancelar uma consulta antiga', async () => {
  const resposta = { dados: [{ id: 21, titulo: 'Guia', arquivo_pdf: 'materiais/guia.pdf' }], paginacao: { pagina: 2, por_pagina: 20, total: 21 } }
  const fetch = responder([json(resposta)])
  const controller = new AbortController()
  assert.deepEqual(await backend.conteudos('materiais', 2, { signal: controller.signal }), resposta)
  const [url, options] = fetch.mock.calls[0].arguments
  assert.equal(url, '/api/v1/conteudos/materiais?page=2')
  assert.equal(options.signal, controller.signal)
  assert.equal(fetch.mock.callCount(), 1)
})

test('download de material usa a rota pública, sem expor caminho de armazenamento', () => {
  assert.equal(backend.arquivoConteudo('materiais', 21), '/api/v1/conteudos/materiais/21/arquivos/arquivo_pdf')
})

test('gestão publica PDF como JSON na rota protegida com CSRF', async () => {
  const fetch = responder([json({ token: 'seguro' }), json({ dados: { id: 30, titulo: 'Guia' } })])
  const dados = { titulo: 'Guia', categoria: 'Educação', descricao: null, arquivo_pdf_base64: 'data:application/pdf;base64,JVBERi0xLjQ=', arquivo_pdf_nome: 'guia.pdf' }
  assert.equal((await backend.publicarMaterial(dados)).dados.id, 30)
  const [url, options] = fetch.mock.calls[1].arguments
  assert.equal(url, '/api/v1/gestao/conteudos/materiais')
  assert.equal(options.method, 'POST')
  assert.equal(options.headers['X-CSRF-TOKEN'], 'seguro')
  assert.equal(options.headers['Content-Type'], 'application/json')
  assert.deepEqual(JSON.parse(options.body), dados)
})

test('gestão preserva a mensagem de acesso negado e trata limite de upload', async () => {
  responder([json({ token: 'a' }), json({ mensagem: 'Acesso restrito a gestores da ONG.' }, 403), json({ token: 'b' }), new Response('Too large', { status: 413 })])
  await assert.rejects(backend.publicarMaterial(new FormData()), { status: 403, message: 'Acesso restrito a gestores da ONG.' })
  await assert.rejects(backend.publicarMaterial(new FormData()), { status: 413, message: 'O arquivo excede o limite de envio do servidor. Escolha um PDF menor.' })
})

test('upload informa quando o PHP não consegue criar arquivo temporário', async () => {
  const aviso = '<b>Warning</b>: PHP Request Startup: File upload error - unable to create a temporary file on line 0 {"message":"Unauthenticated."}'
  responder([json({ token: 'a' }), new Response(aviso, { status: 401 })])
  await assert.rejects(backend.publicarMaterial(new FormData()), { status: 401, message: /arquivo temporário/ })
})
