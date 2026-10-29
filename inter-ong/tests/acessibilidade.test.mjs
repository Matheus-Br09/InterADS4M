import assert from 'node:assert/strict'
import { test } from 'node:test'
import { ESCALAS, proximaEscala, lerPreferencias, salvarPreferencias } from '../src/utils/acessibilidade.js'

const padrao = { escala: 100, contraste: false }

test('proximaEscala sobe e desce um degrau e respeita os limites', () => {
  assert.deepEqual(ESCALAS, [100, 112, 125, 150])
  assert.equal(proximaEscala(100, 1), 112)
  assert.equal(proximaEscala(150, 1), 150)
  assert.equal(proximaEscala(100, -1), 100)
  assert.equal(proximaEscala(150, -1), 125)
})

test('lerPreferencias devolve o padrão sem storage, com exceção ou JSON inválido', () => {
  assert.deepEqual(lerPreferencias(undefined), padrao)
  assert.deepEqual(lerPreferencias({ getItem() { throw new Error('bloqueado') } }), padrao)
  assert.deepEqual(lerPreferencias({ getItem: () => '{quebrado' }), padrao)
  assert.deepEqual(lerPreferencias({ getItem: () => JSON.stringify({ escala: 999, contraste: 'sim' }) }), padrao)
})

test('salvar e ler preserva os valores; salvar engole exceção', () => {
  const dados = {}
  const storage = { getItem: (k) => dados[k] ?? null, setItem: (k, v) => { dados[k] = v } }
  salvarPreferencias(storage, { escala: 125, contraste: true })
  assert.deepEqual(lerPreferencias(storage), { escala: 125, contraste: true })
  assert.doesNotThrow(() => salvarPreferencias({ setItem() { throw new Error('cheio') } }, padrao))
  assert.doesNotThrow(() => salvarPreferencias(undefined, padrao))
})
