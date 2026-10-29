export function linkWhatsapp(whatsapp) {
  const numero = String(whatsapp || '').replace(/\D/g, '')
  return numero ? `https://wa.me/${numero}` : null
}
