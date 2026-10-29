import assert from 'node:assert/strict'
import { test } from 'node:test'
import { ehMaiorDeIdade } from '../src/utils/idade.js'

const hoje = new Date(2026, 9, 29)

test('ehMaiorDeIdade confere 18 anos completos', () => {
  assert.equal(ehMaiorDeIdade('2008-10-29', hoje), true)
  assert.equal(ehMaiorDeIdade('2008-10-30', hoje), false)
  assert.equal(ehMaiorDeIdade('2000-02-29', hoje), true)
  assert.equal(ehMaiorDeIdade('', hoje), false)
})
