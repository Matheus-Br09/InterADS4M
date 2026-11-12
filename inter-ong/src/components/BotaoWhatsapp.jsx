import { useEffect, useState } from 'react'
import { useConfig } from '../context/ConfigContext.jsx'
import { linkWhatsapp } from '../utils/whatsapp.js'

const MENSAGEM_PRONTA = 'Olá! Vim pelo site da ONG e gostaria de saber mais.'

// Botão fixo no canto da tela. Não aparece se a ONG não configurou o WhatsApp.
// O balão com a chamada aparece depois de 2 s, some depois de 8 s e volta com mouse ou teclado.
export default function BotaoWhatsapp() {
  const href = linkWhatsapp(useConfig()?.whatsapp, MENSAGEM_PRONTA)
  const [balao, setBalao] = useState(false)

  useEffect(() => {
    const mostrar = setTimeout(() => setBalao(true), 2000)
    const esconder = setTimeout(() => setBalao(false), 8000)
    return () => {
      clearTimeout(mostrar)
      clearTimeout(esconder)
    }
  }, [])

  if (!href) return null
  return (
    <a
      href={href}
      target="_blank"
      rel="noopener noreferrer"
      aria-label="Falar com a ONG pelo WhatsApp (abre em nova aba)"
      onMouseEnter={() => setBalao(true)}
      onMouseLeave={() => setBalao(false)}
      onFocus={() => setBalao(true)}
      onBlur={() => setBalao(false)}
      className="fixed bottom-4 right-4 z-40 flex items-center gap-3 focus:outline-none"
    >
      <span
        aria-hidden="true"
        className={`max-w-[11rem] rounded-2xl bg-white px-4 py-2 text-sm font-bold leading-tight text-slate-900 shadow-xl transition duration-300 sm:max-w-none sm:text-base ${
          balao ? 'translate-x-0 opacity-100' : 'pointer-events-none translate-x-3 opacity-0'
        }`}
      >
        Fale com a gente no WhatsApp
      </span>
      <span className="wa-pulso relative flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[#25D366] text-white shadow-xl transition hover:scale-110 hover:bg-[#1ebe5b] active:scale-95 sm:h-[60px] sm:w-[60px]">
        {/* Logo do WhatsApp: ícone "whatsapp-logo" (fill) do Phosphor Icons, licença MIT (veja src/assets/icons/LICENSE-phosphor.txt) */}
        <svg className="h-8 w-8 fill-current sm:h-9 sm:w-9" viewBox="0 0 256 256" aria-hidden="true" focusable="false">
          <path d="M152.58,145.23l23,11.48A24,24,0,0,1,152,176a72.08,72.08,0,0,1-72-72A24,24,0,0,1,99.29,80.46l11.48,23L101,118a8,8,0,0,0-.73,7.51,56.47,56.47,0,0,0,30.15,30.15A8,8,0,0,0,138,155ZM232,128A104,104,0,0,1,79.12,219.82L45.07,231.17a16,16,0,0,1-20.24-20.24l11.35-34.05A104,104,0,1,1,232,128Zm-40,24a8,8,0,0,0-4.42-7.16l-32-16a8,8,0,0,0-8,.5l-14.69,9.8a40.55,40.55,0,0,1-16-16l9.8-14.69a8,8,0,0,0,.5-8l-16-32A8,8,0,0,0,104,64a40,40,0,0,0-40,40,88.1,88.1,0,0,0,88,88A40,40,0,0,0,192,152Z" />
        </svg>
      </span>
    </a>
  )
}
