// Converte o texto digitado ("50", "50,5", "1.234,56") em "1234.56", ou null se inválido.
export function normalizarValor(texto) {
  const limpo = String(texto ?? '').trim().replace(/^R\$\s*/, '')
  if (!limpo) return null
  const padrao = limpo.includes(',') ? limpo.replace(/\./g, '').replace(',', '.') : limpo
  if (!/^\d+(\.\d{1,2})?$/.test(padrao)) return null
  const [inteiro, centavos = ''] = padrao.split('.')
  return `${inteiro.replace(/^0+(?=\d)/, '')}.${centavos.padEnd(2, '0')}`
}
