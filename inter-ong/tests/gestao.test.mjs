import assert from 'node:assert/strict'
import { test } from 'node:test'
import { rotulo, STATUS_DOACAO, STATUS_VOLUNTARIO, formatarReais, calcularIdade } from '../src/utils/gestao.js'

test('rotulo traduz status conhecidos e mantém desconhecidos', () => {
  assert.equal(rotulo(STATUS_DOACAO, 'concluido'), 'Confirmada')
  assert.equal(rotulo(STATUS_VOLUNTARIO, 'em_analise'), 'Em análise')
  assert.equal(rotulo(STATUS_DOACAO, 'novo'), 'novo')
})

test('formatarReais usa o padrão brasileiro', () => {
  assert.match(formatarReais('1234.5'), /^R\$\s1\.234,50$/)
  assert.match(formatarReais('abc'), /0,00/)
})

test('calcularIdade respeita o aniversário', () => {
  const hoje = new Date(2026, 9, 29)
  assert.equal(calcularIdade('2000-10-29', hoje), 26)
  assert.equal(calcularIdade('2000-10-30', hoje), 25)
  assert.equal(calcularIdade('', hoje), null)
})
