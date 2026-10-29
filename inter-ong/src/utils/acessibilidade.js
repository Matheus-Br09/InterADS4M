export const ESCALAS = [100, 112, 125, 150]
export const CHAVE = 'sos-acessibilidade'
const PADRAO = { escala: 100, contraste: false }

// Sobe (+1) ou desce (-1) um degrau de tamanho de letra, sem passar dos limites.
export function proximaEscala(atual, direcao) {
  const indice = Math.max(0, ESCALAS.indexOf(atual))
  return ESCALAS[Math.min(ESCALAS.length - 1, Math.max(0, indice + direcao))]
}

export function lerPreferencias(storage) {
  try {
    const salvo = JSON.parse(storage.getItem(CHAVE))
    return {
      escala: ESCALAS.includes(salvo?.escala) ? salvo.escala : PADRAO.escala,
      contraste: salvo?.contraste === true,
    }
  } catch {
    return { ...PADRAO }
  }
}

export function salvarPreferencias(storage, prefs) {
  try {
    storage.setItem(CHAVE, JSON.stringify({ escala: prefs.escala, contraste: prefs.contraste }))
  } catch { /* navegador sem armazenamento: a escolha vale só nesta visita */ }
}
