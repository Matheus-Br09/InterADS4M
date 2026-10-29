import assert from 'node:assert/strict'
import { test } from 'node:test'
import { normalizarValor } from '../src/utils/valor.js'

test('normalizarValor converte texto digitado em valor com duas casas', () => {
  assert.equal(normalizarValor('50'), '50.00')
  assert.equal(normalizarValor('50,5'), '50.50')
  assert.equal(normalizarValor('1.234,56'), '1234.56')
  assert.equal(normalizarValor('abc'), null)
  assert.equal(normalizarValor(''), null)
})
