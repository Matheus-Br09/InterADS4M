import { useConfig } from '../context/ConfigContext.jsx'
import { linkWhatsapp } from '../utils/whatsapp.js'

// Botão fixo no canto da tela. Não aparece se a ONG não configurou o WhatsApp.
export default function BotaoWhatsapp() {
  const href = linkWhatsapp(useConfig()?.whatsapp)
  if (!href) return null
  return (
    <a
      href={href}
      target="_blank"
      rel="noopener noreferrer"
      aria-label="Falar com a ONG pelo WhatsApp (abre em nova aba)"
      className="fixed bottom-4 right-4 z-40 inline-flex items-center gap-2 rounded-full bg-green-600 px-4 py-3 text-sm font-bold text-white shadow-lg transition hover:scale-105 hover:bg-green-700 active:scale-95"
    >
      <svg className="h-5 w-5 fill-current" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
        <path d="M20 2H4a2 2 0 0 0-2 2v18l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z" />
      </svg>
      WhatsApp
    </a>
  )
}
