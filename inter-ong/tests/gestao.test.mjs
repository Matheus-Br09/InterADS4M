import assert from 'node:assert/strict'
import { test } from 'node:test'
import { rotulo, STATUS_DOACAO, STATUS_VOLUNTARIO, formatarReais, calcularIdade, normalizarLista, textoOuReserva, mensagemBuscaVazia } from '../src/utils/gestao.js'

test('textoOuReserva devolve o texto, ou a reserva quando vazio', () => {
  assert.equal(textoOuReserva('Ana', '(sem nome)'), 'Ana')
  assert.equal(textoOuReserva('  ', '(sem nome)'), '(sem nome)')
  assert.equal(textoOuReserva(null, '—'), '—')
  assert.equal(textoOuReserva(undefined, '—'), '—')
})

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

test('normalizarLista entende {dados, paginacao}', () => {
  const r = normalizarLista({ dados: [1, 2], paginacao: { pagina: 2, por_pagina: 20, total: 45 } })
  assert.deepEqual(r, { itens: [1, 2], pagina: 2, ultima: 3 })
  assert.equal(normalizarLista({ dados: [], paginacao: { pagina: 1, por_pagina: 20, total: 0 } }).ultima, 1)
})

test('normalizarLista entende o paginador do Laravel', () => {
  assert.deepEqual(normalizarLista({ data: [1], current_page: 3, last_page: 5 }), { itens: [1], pagina: 3, ultima: 5 })
})

test('normalizarLista entende array simples', () => {
  assert.deepEqual(normalizarLista([1, 2]), { itens: [1, 2], pagina: 1, ultima: 1 })
})

test('mensagemBuscaVazia avisa que a busca vale só dentro do filtro escolhido', () => {
  assert.match(mensagemBuscaVazia('Pendentes'), /Pendentes/)
  assert.match(mensagemBuscaVazia('Pendentes'), /Todas/)
  assert.doesNotMatch(mensagemBuscaVazia('Todas'), /Tente o botão/)
})
