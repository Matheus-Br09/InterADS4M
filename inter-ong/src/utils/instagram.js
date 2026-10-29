export const INSTAGRAM_PADRAO = 'https://www.instagram.com/SosTudopelosocial'

// Aceita o endereço completo ou só o nome do perfil (com ou sem @).
export function linkInstagram(valor) {
  const texto = String(valor || '').trim()
  if (!texto) return INSTAGRAM_PADRAO
  if (/^https:\/\//i.test(texto)) return texto
  const perfil = texto.replace(/^@/, '').replace(/[^\w.]/g, '')
  return perfil ? `https://www.instagram.com/${perfil}` : INSTAGRAM_PADRAO
}
