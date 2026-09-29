import assert from 'node:assert/strict'
import { createServer as createHttpServer } from 'node:http'
import { test } from 'node:test'
import { createServer } from 'vite'

test('proxy encaminha caminho, corpo, cookies e CSRF e devolve cookie de sessão', async (t) => {
  let recebida
  const api = createHttpServer(async (req, res) => {
    const chunks = []
    for await (const chunk of req) chunks.push(chunk)
    recebida = { url: req.url, method: req.method, headers: req.headers, body: Buffer.concat(chunks).toString() }
    res.writeHead(201, {
      'Content-Type': 'application/json',
      'Set-Cookie': 'sessao=teste; Domain=127.0.0.1; Path=/; HttpOnly; SameSite=Lax',
    })
    res.end(JSON.stringify({ dados: { nome_completo: 'Teste' } }))
  })
  await new Promise((resolve) => api.listen(0, '127.0.0.1', resolve))
  t.after(() => new Promise((resolve) => api.close(resolve)))
  const anterior = process.env.BACKEND_PROXY_URL
  process.env.BACKEND_PROXY_URL = `http://127.0.0.1:${api.address().port}`
  t.after(() => {
    if (anterior === undefined) delete process.env.BACKEND_PROXY_URL
    else process.env.BACKEND_PROXY_URL = anterior
  })
  const vite = await createServer({ server: { port: 0, host: '127.0.0.1' }, logLevel: 'silent' })
  t.after(() => vite.close())
  await vite.listen()
  const body = JSON.stringify({ nome_completo: 'Teste' })
  const response = await fetch(`http://127.0.0.1:${vite.httpServer.address().port}/api/v1/auth/cadastro`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', Cookie: 'sessao=anterior', 'X-CSRF-TOKEN': 'csrf-teste' },
    body,
  })
  assert.equal(response.status, 201)
  assert.equal((await response.json()).dados.nome_completo, 'Teste')
  assert.equal(recebida.url, '/api/v1/auth/cadastro')
  assert.equal(recebida.method, 'POST')
  assert.equal(recebida.body, body)
  assert.equal(recebida.headers.cookie, 'sessao=anterior')
  assert.equal(recebida.headers['x-csrf-token'], 'csrf-teste')
  assert.match(response.headers.get('set-cookie'), /sessao=teste/)
  assert.doesNotMatch(response.headers.get('set-cookie'), /Domain=/i)
})
