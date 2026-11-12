// Monta o link wa.me da ONG. A mensagem pronta é opcional.
export function linkWhatsapp(whatsapp, mensagem) {
  const numero = String(whatsapp || '').replace(/\D/g, '')
  if (!numero) return null
  const texto = String(mensagem || '').trim()
  return texto ? `https://wa.me/${numero}?text=${encodeURIComponent(texto)}` : `https://wa.me/${numero}`
}

// Mostra o número como (81) 98711-8820. Devolve null se não for um número brasileiro de 10 ou 11 dígitos.
export function formatarWhatsapp(whatsapp) {
  let numero = String(whatsapp || '').replace(/\D/g, '')
  if (numero.startsWith('55') && numero.length > 11) numero = numero.slice(2)
  const m = numero.match(/^(\d{2})(\d{4,5})(\d{4})$/)
  return m ? `(${m[1]}) ${m[2]}-${m[3]}` : null
}
