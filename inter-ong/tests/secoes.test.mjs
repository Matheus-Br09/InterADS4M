import assert from 'node:assert/strict'
import { test } from 'node:test'
import { secaoDisponivel } from '../src/utils/secoes.js'

test('secaoDisponivel só libera a seção quando o servidor diz que há conteúdo', () => {
  const config = { secoes: { noticias: true, programas: false } }
  assert.equal(secaoDisponivel(config, 'noticias'), true)
  assert.equal(secaoDisponivel(config, 'programas'), false)
  assert.equal(secaoDisponivel(config, 'materiais'), false)
})

test('secaoDisponivel esconde tudo enquanto a configuração não chegou', () => {
  assert.equal(secaoDisponivel(null, 'noticias'), false)
  assert.equal(secaoDisponivel(undefined, 'noticias'), false)
  assert.equal(secaoDisponivel({}, 'noticias'), false)
})
