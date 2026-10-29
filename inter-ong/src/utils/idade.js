export function ehMaiorDeIdade(dataNascimento, hoje = new Date()) {
  const partes = /^(\d{4})-(\d{2})-(\d{2})$/.exec(dataNascimento || '')
  if (!partes) return false
  const [ano, mes, dia] = partes.slice(1).map(Number)
  const nascimento = new Date(ano, mes - 1, dia)
  if (nascimento.getMonth() !== mes - 1 || nascimento.getDate() !== dia) return false
  // Quem nasceu em 29/02 completa 18 anos em 01/03 nos anos não bissextos.
  const aniversario18 = new Date(ano + 18, mes - 1, dia)
  return hoje >= aniversario18
}
