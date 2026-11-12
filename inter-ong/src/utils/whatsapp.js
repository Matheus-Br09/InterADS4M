// Monta o link wa.me da ONG. A mensagem pronta é opcional.
export function linkWhatsapp(whatsapp, mensagem) {
  const numero = String(whatsapp || '').replace(/\D/g, '')
  if (!numero) return null
  const texto = String(mensagem || '').trim()
  return texto ? `https://wa.me/${numero}?text=${encodeURIComponent(texto)}` : `https://wa.me/${numero}`
}
