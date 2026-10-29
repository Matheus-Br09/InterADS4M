import { useEffect, useRef, useState } from 'react'
import { ESCALAS, lerPreferencias, proximaEscala, salvarPreferencias } from '../utils/acessibilidade.js'

function guardar() {
  try { return window.localStorage } catch { return undefined }
}

const botao = 'rounded-lg border-2 border-slate-300 bg-white px-3 py-2 text-sm font-bold text-slate-800 hover:border-pink-600 focus-visible:outline-3 focus-visible:outline-offset-2 focus-visible:outline-pink-600 disabled:cursor-not-allowed disabled:opacity-40 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100'

// Botão fixo no canto esquerdo (o do WhatsApp fica no direito).
export default function AcessibilidadeWidget() {
  const [aberto, setAberto] = useState(false)
  const [prefs, setPrefs] = useState(() => lerPreferencias(guardar()))
  const gatilho = useRef(null)
  const raiz = useRef(null)

  useEffect(() => {
    const html = document.documentElement
    html.style.fontSize = `${prefs.escala}%`
    html.classList.toggle('alto-contraste', prefs.contraste)
    salvarPreferencias(guardar(), prefs)
  }, [prefs])

  useEffect(() => {
    if (!aberto) return
    const aoTeclar = (e) => { if (e.key === 'Escape') { setAberto(false); gatilho.current?.focus() } }
    const aoClicarFora = (e) => { if (!raiz.current?.contains(e.target)) setAberto(false) }
    document.addEventListener('keydown', aoTeclar)
    document.addEventListener('mousedown', aoClicarFora)
    return () => { document.removeEventListener('keydown', aoTeclar); document.removeEventListener('mousedown', aoClicarFora) }
  }, [aberto])

  const mudarEscala = (direcao) => setPrefs((p) => ({ ...p, escala: proximaEscala(p.escala, direcao) }))

  return (
    <div ref={raiz} className="fixed bottom-4 left-4 z-40">
      {aberto && (
        <div id="painel-acessibilidade" role="group" aria-label="Opções de acessibilidade" className="mb-3 w-64 rounded-2xl border border-slate-200 bg-white p-4 text-slate-800 shadow-xl dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
          <p className="text-sm font-bold">Tamanho da letra: {prefs.escala}%</p>
          <div className="mt-2 flex gap-2">
            <button type="button" className={botao} aria-label="Diminuir o tamanho da letra" disabled={prefs.escala === ESCALAS[0]} onClick={() => mudarEscala(-1)}>A−</button>
            <button type="button" className={botao} aria-label="Aumentar o tamanho da letra" disabled={prefs.escala === ESCALAS[ESCALAS.length - 1]} onClick={() => mudarEscala(1)}>A+</button>
          </div>
          <button type="button" className={`${botao} mt-3 w-full`} aria-pressed={prefs.contraste} onClick={() => setPrefs((p) => ({ ...p, contraste: !p.contraste }))}>
            Alto contraste: {prefs.contraste ? 'ligado' : 'desligado'}
          </button>
          <button type="button" className={`${botao} mt-3 w-full`} onClick={() => setPrefs({ escala: 100, contraste: false })}>Restaurar</button>
        </div>
      )}
      <button
        ref={gatilho}
        type="button"
        aria-label="Acessibilidade"
        aria-expanded={aberto}
        aria-controls="painel-acessibilidade"
        onClick={() => setAberto((a) => !a)}
        className="flex h-12 w-12 items-center justify-center rounded-full bg-[#fb2782] text-white shadow-lg transition hover:scale-105 focus-visible:outline-3 focus-visible:outline-offset-2 focus-visible:outline-slate-900 dark:focus-visible:outline-white"
      >
        <svg className="h-6 w-6 fill-current" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
          <circle cx="12" cy="4.5" r="2.2" />
          <path d="M5 8.5h14v2.2h-5.2v3.1l2.2 6.2-2.1.8-2.2-5.6-2.2 5.6-2.1-.8 2.2-6.2v-3.1H5z" />
        </svg>
      </button>
    </div>
  )
}
