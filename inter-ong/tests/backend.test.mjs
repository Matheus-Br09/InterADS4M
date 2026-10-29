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

test('erros de validação preservam campos e não repetem a gravação', async () => {
  const fetch = responder([json({ token: 'csrf' }), json({ message: 'CPF inválido.', errors: { cpf: ['CPF inválido.'] } }, 422)])
  await assert.rejects(backend.entrar({}), (error) => {
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
  await assert.rejects(backend.entrar({}), /conectar ao servidor/)
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

test('newsletter usa a rota real', async () => {
  const fetch = responder([json({ token: 'b' }), json({ mensagem: 'Inscrito' }, 201)])
  await backend.newsletter({ email: 'teste@example.org' })
  assert.equal(fetch.mock.calls[1].arguments[0], '/api/v1/newsletter')
})

test('doação anônima envia valor e dados opcionais por POST /api/v1/doacoes com CSRF', async () => {
  const fetch = responder([json({ token: 'csrf' }), json({ dados: { protocolo: 'D-1' }, pix: { copia_e_cola: '000201' } }, 201)])
  const dados = { valor: '50.00', nome_doador: 'Maria', email_doador: 'maria@example.org' }
  await backend.doacao(dados)
  const [url, options] = fetch.mock.calls[1].arguments
  assert.equal(url, '/api/v1/doacoes')
  assert.equal(options.method, 'POST')
  assert.equal(options.headers['X-CSRF-TOKEN'], 'csrf')
  assert.deepEqual(JSON.parse(options.body), dados)
})

test('gestão confirma doação com PATCH /api/v1/gestao/doacoes/7 e corpo {status:"concluido"}', async () => {
  const fetch = responder([json({ token: 'csrf' }), json({ dados: { id: 7 } })])
  await backend.atualizarDoacao(7, 'concluido')
  const [url, options] = fetch.mock.calls[1].arguments
  assert.equal(url, '/api/v1/gestao/doacoes/7')
  assert.equal(options.method, 'PATCH')
  assert.deepEqual(JSON.parse(options.body), { status: 'concluido' })
})

test('excluir voluntário usa DELETE e aceita 204', async () => {
  const fetch = responder([json({ token: 'csrf' }), new Response(null, { status: 204 })])
  assert.equal(await backend.excluirVoluntario(3), null)
  const [url, options] = fetch.mock.calls[1].arguments
  assert.equal(url, '/api/v1/gestao/voluntarios/3')
  assert.equal(options.method, 'DELETE')
})

test('configPublica faz GET /api/v1/config-publica sem CSRF', async () => {
  const fetch = responder([json({ dados: { pix_disponivel: true, whatsapp: null, instagram: null } })])
  const resposta = await backend.configPublica()
  assert.equal(resposta.dados.pix_disponivel, true)
  assert.equal(fetch.mock.callCount(), 1)
  const [url, options] = fetch.mock.calls[0].arguments
  assert.equal(url, '/api/v1/config-publica')
  assert.equal(options.method, undefined)
  assert.equal(options.headers['X-CSRF-TOKEN'], undefined)
})

test('métodos da gestão montam método, URL e corpo corretos', async () => {
  const fetch = responder([
    json({ dados: [] }), json({ dados: [] }), json({ dados: [] }), json({ dados: [] }), json({ dados: [] }),
    json({ token: 'c' }), json({ dados: {} }),
    json({ token: 'c' }), json({ dados: {} }),
    json({ token: 'c' }), json({ dados: {} }),
    json({ token: 'c' }), json({ dados: {} }),
    json({ token: 'c' }), new Response(null, { status: 204 }),
    json({ token: 'c' }), json({ dados: {}, senha_temporaria: 'x' }, 201),
    json({ token: 'c' }), new Response(null, { status: 204 }),
  ])
  await backend.gestaoDoacoes('pendente', 2)
  await backend.gestaoDoacoes()
  await backend.gestaoVoluntarios(3)
  await backend.gestaoConteudos('noticias', 4)
  await backend.gestaoGestores()
  await backend.anonimizarDoacao(5)
  await backend.atualizarVoluntario(6, { status: 'entrevista', data_entrevista: '2026-11-01' })
  const form = new FormData()
  await backend.publicarConteudo('materiais', form)
  await backend.excluirConteudo('noticias', 8)
  await backend.criarGestor({ nome_completo: 'Ana', email: 'ana@example.org' })
  await backend.desativarGestor(9)
  const chamadas = fetch.mock.calls.map(({ arguments: args }) => [args[0], args[1].method ?? 'GET'])
  assert.deepEqual(chamadas.filter((_, i) => i < 5 || i % 2 === 0), [
    ['/api/v1/gestao/doacoes?status=pendente&page=2', 'GET'],
    ['/api/v1/gestao/doacoes?page=1', 'GET'],
    ['/api/v1/gestao/voluntarios?page=3', 'GET'],
    ['/api/v1/gestao/conteudos/noticias?page=4', 'GET'],
    ['/api/v1/gestao/gestores', 'GET'],
    ['/api/v1/gestao/doacoes/5', 'DELETE'],
    ['/api/v1/gestao/voluntarios/6', 'PATCH'],
    ['/api/v1/gestao/conteudos/materiais', 'POST'],
    ['/api/v1/gestao/conteudos/noticias/8', 'DELETE'],
    ['/api/v1/gestao/gestores', 'POST'],
    ['/api/v1/gestao/gestores/9', 'DELETE'],
  ])
  assert.equal(fetch.mock.calls[10].arguments[1].body, form)
  assert.equal(backend.curriculoUrl(6), '/api/v1/gestao/voluntarios/6/curriculo')
})
