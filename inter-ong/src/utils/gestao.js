export const STATUS_DOACAO = { pendente: 'Pendente', concluido: 'Confirmada', cancelado: 'Cancelada', expirado: 'Expirada' }
export const STATUS_VOLUNTARIO = { em_analise: 'Em análise', entrevista_marcada: 'Entrevista marcada', aprovado: 'Aprovado', recusado: 'Recusado' }

export const rotulo = (tabela, chave) => tabela[chave] || chave || ''

export function formatarReais(valor) {
  const numero = Number(valor)
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(Number.isFinite(numero) ? numero : 0)
}

export function formatarDataHora(texto) {
  const data = new Date(texto)
  return Number.isNaN(data.getTime()) ? '' : data.toLocaleString('pt-BR', { dateStyle: 'short', timeStyle: 'short' })
}

export function calcularIdade(dataNascimento, hoje = new Date()) {
  const partes = /^(\d{4})-(\d{2})-(\d{2})/.exec(dataNascimento || '')
  if (!partes) return null
  const [ano, mes, dia] = partes.slice(1).map(Number)
  let idade = hoje.getFullYear() - ano
  if (hoje.getMonth() + 1 < mes || (hoje.getMonth() + 1 === mes && hoje.getDate() < dia)) idade--
  return idade
}

// Normaliza uma lista do painel em { itens, pagina, ultima }.
// Aceita o paginador do Laravel, { dados, paginacao } e um array simples.
export function normalizarLista(dados) {
  if (Array.isArray(dados)) return { itens: dados, pagina: 1, ultima: 1 }
  if (Array.isArray(dados?.dados)) {
    const p = dados.paginacao || {}
    const porPagina = Number(p.por_pagina) || 0
    const total = Number(p.total) || 0
    return {
      itens: dados.dados,
      pagina: Number(p.pagina) || 1,
      ultima: porPagina > 0 ? Math.max(1, Math.ceil(total / porPagina)) : 1,
    }
  }
  return { itens: dados?.data || [], pagina: dados?.current_page || 1, ultima: dados?.last_page || 1 }
}
