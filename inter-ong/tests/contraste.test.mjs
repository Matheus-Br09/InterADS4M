import assert from 'node:assert/strict'
import fs from 'node:fs'
import { test } from 'node:test'

const raiz = new URL('../src/', import.meta.url)
const indexCss = fs.readFileSync(new URL('index.css', raiz), 'utf8')

// Lê as variáveis (--nome: valor) de um bloco do index.css, ex.: ':root' ou 'html.dark'.
function variaveis(seletor) {
  const inicio = indexCss.indexOf(`${seletor} {`)
  assert.notEqual(inicio, -1, `bloco ${seletor} não encontrado`)
  const bloco = indexCss.slice(inicio, indexCss.indexOf('}', inicio))
  return Object.fromEntries([...bloco.matchAll(/(--[\w-]+):\s*([^;]+);/g)].map(([, nome, valor]) => [nome, valor.trim()]))
}

function luminancia(hex) {
  const [r, g, b] = [1, 3, 5].map((i) => parseInt(hex.slice(i, i + 2), 16) / 255)
    .map((c) => (c <= 0.03928 ? c / 12.92 : ((c + 0.055) / 1.055) ** 2.4))
  return 0.2126 * r + 0.7152 * g + 0.0722 * b
}
const contraste = (a, b) => {
  const [claro, escuro] = [luminancia(a), luminancia(b)].sort((x, y) => y - x)
  return (claro + 0.05) / (escuro + 0.05)
}

const PARES = [
  ['--text-primary', '--bg-page'], ['--text-primary', '--bg-card'],
  ['--text-secondary', '--bg-page'], ['--text-secondary', '--bg-card'],
  ['--text-muted', '--bg-page'], ['--text-muted', '--bg-card'],
]

for (const [tema, seletor] of [['claro', ':root'], ['escuro', 'html.dark']]) {
  test(`tema ${tema}: textos têm contraste mínimo de 4.5 sobre os fundos`, () => {
    const v = { ...variaveis(':root'), ...variaveis(seletor) }
    for (const [texto, fundo] of PARES) {
      const razao = contraste(v[texto], v[fundo])
      assert.ok(razao >= 4.5, `${texto} (${v[texto]}) sobre ${fundo} (${v[fundo]}) = ${razao.toFixed(2)}`)
    }
  })
}

test('a variável de fundo das faixas existe nos dois temas e no escuro não é degradê claro', () => {
  assert.ok(variaveis(':root')['--fundo-secao'], '--fundo-secao falta no tema claro')
  assert.equal(variaveis('html.dark')['--fundo-secao'], 'var(--bg-page)')
})

test('CSS das páginas não escreve degradê branco direto (some no tema escuro); usa --fundo-secao', () => {
  const pasta = new URL('pages/css/', raiz)
  const achados = []
  for (const arquivo of fs.readdirSync(pasta).filter((f) => f.endsWith('.css'))) {
    fs.readFileSync(new URL(arquivo, pasta), 'utf8').split('\n').forEach((linha, i) => {
      if (/background(-image)?:\s*linear-gradient\([^;]*(#fff\b|#ffffff\b|white)/i.test(linha)) achados.push(`${arquivo}:${i + 1} ${linha.trim()}`)
    })
  }
  assert.deepEqual(achados, [])
})
