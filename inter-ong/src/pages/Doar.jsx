import { useEffect, useRef, useState } from 'react'
import QRCode from 'qrcode'
import { backend } from '../api/backend'
import AvisoApi from '../components/AvisoApi'
import { normalizarValor } from '../utils/valor'
import realOficinaUvaImg from '../assets/real-oficina-uva.jpg'
import './css/Doar.css'

const CAMPO = 'w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#0e1526] text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-[#fb2782]'
const ROTULO = 'block text-sm font-bold text-slate-800 dark:text-slate-200 mb-1'
const VALORES = [25, 50, 100, 200]

export default function Doar() {
  const [valor, setValor] = useState('50')
  const [nome, setNome] = useState('')
  const [email, setEmail] = useState('')
  const [erro, setErro] = useState(null)
  const [enviando, setEnviando] = useState(false)

  const [modalAberto, setModalAberto] = useState(false)
  const [copiado, setCopiado] = useState(false)
  const [doacao, setDoacao] = useState(null)
  const [qrCode, setQrCode] = useState(null)

  const ocupado = useRef(false)
  const botaoFechar = useRef(null)
  const origemFoco = useRef(null)

  useEffect(() => {
    if (!modalAberto) return undefined
    botaoFechar.current?.focus()
    const aoTeclar = (e) => { if (e.key === 'Escape') setModalAberto(false) }
    document.addEventListener('keydown', aoTeclar)
    return () => {
      document.removeEventListener('keydown', aoTeclar)
      origemFoco.current?.focus?.()
    }
  }, [modalAberto])

  async function enviar(event) {
    event.preventDefault()
    if (ocupado.current) return
    const valorNormalizado = normalizarValor(valor)
    if (!valorNormalizado) {
      setErro(new Error('Informe um valor válido, por exemplo 50 ou 50,00.'))
      return
    }
    ocupado.current = true
    origemFoco.current = document.activeElement
    setEnviando(true)
    setErro(null)

    try {
      const corpo = { valor: valorNormalizado }
      if (nome.trim()) corpo.nome_doador = nome.trim()
      if (email.trim()) corpo.email_doador = email.trim()
      const resultado = await backend.doacao(corpo)
      let imagem = null
      try {
        imagem = await QRCode.toDataURL(resultado.pix.copia_e_cola, { margin: 1, width: 320 })
      } catch {
        // Sem QR Code o doador ainda pode usar o copia e cola.
      }
      setQrCode(imagem)
      setDoacao({ ...resultado.dados, pix: resultado.pix })
      setCopiado(false)
      setModalAberto(true)
    } catch (error) {
      setErro(error)
    } finally {
      ocupado.current = false
      setEnviando(false)
    }
  }

  function copiar(texto) {
    if (!navigator.clipboard) return
    navigator.clipboard.writeText(texto).then(() => {
      setCopiado(true)
      setTimeout(() => setCopiado(false), 3500)
    }).catch(() => {})
  }

  const valorAtual = normalizarValor(valor)

  return (
    <main className="min-h-screen bg-pink-50/40 px-4 py-12 dark:bg-[#090d16] transition-colors duration-400">
      <div className="mx-auto max-w-6xl">
        <header className="mb-10 text-center">
          <span className="badge-pill-pink inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs sm:text-sm font-bold border border-pink-200 text-[#fb2782] bg-pink-100/70 shadow-xs">
            ♥ Juntos transformamos vidas
          </span>
          <h1 className="my-4 text-3xl sm:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight">
            Faça parte dessa história
          </h1>
          <p className="max-w-2xl mx-auto text-base sm:text-lg text-slate-600 dark:text-slate-300">
            Sua contribuição apoia diariamente oficinas terapêuticas, alimentação e suporte contínuo para crianças do Instituto SOS Tudo pelo Social.
          </p>
        </header>

        <div className="grid gap-8 lg:grid-cols-2 items-start">
          <section className="space-y-6">
            <div className="relative overflow-hidden rounded-3xl shadow-lg border border-pink-100 dark:border-slate-800 bg-white dark:bg-[#121a2d]">
              <img
                src={realOficinaUvaImg}
                alt="Crianças atendidas pelo instituto SOS Tudo pelo Social com desenhos artísticos"
                className="w-full h-80 sm:h-96 object-cover"
              />
              <div className="absolute bottom-4 left-4 right-4 bg-slate-950/75 backdrop-blur-md rounded-2xl p-4 text-white border border-white/20">
                <p className="text-xs font-semibold text-pink-300 uppercase tracking-wider">Ação Real em Paulista-PE</p>
                <p className="text-sm font-medium mt-0.5">Oficinas lúdicas de artes e desenvolvimento psicomotor</p>
              </div>
            </div>

            <div className="rounded-3xl bg-white dark:bg-[#121a2d] p-6 sm:p-8 text-slate-800 dark:text-slate-100 shadow-md border border-pink-100/80 dark:border-slate-800 space-y-4">
              <h2 className="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span aria-hidden="true">🛡️</span> Como funciona seu apoio?
              </h2>
              <p className="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed">
                Você doa por Pix, sem precisar de cadastro. Cada contribuição recebe um protocolo para você acompanhar com a ONG.
              </p>
            </div>
          </section>

          <section className="rounded-3xl bg-white dark:bg-[#121a2d] p-6 sm:p-8 text-slate-800 dark:text-slate-100 shadow-xl border border-pink-100 dark:border-slate-800">
            <div className="flex items-center justify-between mb-6 pb-4 border-b border-pink-100/80 dark:border-slate-800">
              <h2 className="text-2xl font-extrabold text-slate-900 dark:text-white">Doação única</h2>
              <span className="text-xs font-semibold px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                Pix Instantâneo
              </span>
            </div>

            <AvisoApi erro={erro} />

            <form onSubmit={enviar} className="space-y-6">
              <fieldset disabled={enviando} className="space-y-6">
                <div>
                  <p id="rotulo-sugeridos" className="text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">
                    Selecione um valor sugerido
                  </p>
                  <div role="group" aria-labelledby="rotulo-sugeridos" className="grid grid-cols-4 gap-2">
                    {VALORES.map(v => (
                      <button
                        type="button"
                        key={v}
                        aria-pressed={valorAtual === `${v}.00`}
                        onClick={() => setValor(String(v))}
                        className={`relative py-3 rounded-xl border text-sm sm:text-base font-bold transition-all ${
                          valorAtual === `${v}.00`
                            ? 'bg-[#fb2782] text-white border-[#fb2782] shadow-md shadow-pink-500/25 scale-[1.02]'
                            : 'bg-pink-50/60 dark:bg-[#0e1526] text-slate-800 dark:text-slate-200 border-pink-100 dark:border-slate-700 hover:border-pink-300'
                        }`}
                      >
                        R$ {v}
                        {v === 50 && (
                          <span className="absolute -top-2.5 left-1/2 -translate-x-1/2 text-[9px] font-extrabold uppercase bg-amber-400 text-slate-900 px-1.5 py-0.5 rounded-full shadow-xs whitespace-nowrap">
                            Favorito
                          </span>
                        )}
                      </button>
                    ))}
                  </div>
                </div>

                <div>
                  <label htmlFor="valor-custom" className={ROTULO}>Valor da doação (R$)</label>
                  <div className="relative">
                    <span className="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-slate-400 dark:text-slate-500" aria-hidden="true">R$</span>
                    <input
                      id="valor-custom"
                      className="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#0e1526] text-slate-900 dark:text-white font-semibold text-lg focus:outline-none focus:ring-2 focus:ring-[#fb2782] transition-colors"
                      type="text"
                      inputMode="decimal"
                      required
                      value={valor}
                      onChange={e => setValor(e.target.value)}
                      placeholder="Outro valor..."
                    />
                  </div>
                </div>

                <div>
                  <label htmlFor="nome-doador" className={ROTULO}>
                    Seu nome <span className="font-normal text-slate-500">(opcional)</span>
                  </label>
                  <input id="nome-doador" className={CAMPO} type="text" autoComplete="name" maxLength={120} value={nome} onChange={e => setNome(e.target.value)} />
                </div>

                <div>
                  <label htmlFor="email-doador" className={ROTULO}>
                    Seu e-mail <span className="font-normal text-slate-500">(opcional)</span>
                  </label>
                  <input id="email-doador" className={CAMPO} type="email" autoComplete="email" maxLength={160} value={email} onChange={e => setEmail(e.target.value)} />
                </div>

                <button
                  type="submit"
                  className="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-[#fb2782] via-[#ff3b8d] to-[#ff4785] text-white font-extrabold text-base sm:text-lg shadow-lg shadow-pink-500/30 hover:shadow-xl hover:shadow-pink-500/40 hover:scale-[1.01] active:scale-98 transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none cursor-pointer flex items-center justify-center gap-2"
                >
                  {enviando ? 'Gerando Pix…' : (
                    <>
                      <span>Doar {valorAtual ? `R$ ${valorAtual.replace('.', ',')}` : ''} com Pix</span>
                      <span aria-hidden="true">♥</span>
                    </>
                  )}
                </button>
              </fieldset>
            </form>
          </section>
        </div>
      </div>

      {modalAberto && doacao && (
        <div
          role="dialog"
          aria-modal="true"
          aria-labelledby="pix-modal-title"
          className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-md animate-in fade-in duration-200"
          onClick={() => setModalAberto(false)}
        >
          <div
            className="relative w-full max-w-lg max-h-[95vh] overflow-y-auto bg-white dark:bg-[#121a2d] border border-pink-200 dark:border-slate-700/80 rounded-3xl p-6 sm:p-8 shadow-2xl modal-pop-in text-slate-800 dark:text-slate-100"
            onClick={e => e.stopPropagation()}
          >
            <button
              ref={botaoFechar}
              type="button"
              onClick={() => setModalAberto(false)}
              className="absolute top-4 right-4 p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
              aria-label="Fechar janela de pagamento"
            >
              ✕
            </button>

            <div className="text-center mb-6">
              <h2 id="pix-modal-title" className="text-2xl font-extrabold text-slate-900 dark:text-white">
                Pagamento via Pix
              </h2>
              <p className="text-sm text-slate-600 dark:text-slate-300 mt-1">
                Pague escaneando o QR Code ou copiando o código abaixo.
              </p>
            </div>

            <div className="bg-slate-50 dark:bg-[#0e1526] rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 mb-6 flex items-center justify-between">
              <div>
                <p className="text-xs uppercase font-bold text-slate-500 dark:text-slate-400">Valor</p>
                <p className="text-2xl font-black text-[#fb2782] dark:text-pink-400">
                  R$ {String(doacao.valor).replace('.', ',')}
                </p>
              </div>
              <div className="text-right">
                <p className="text-xs uppercase font-bold text-slate-500 dark:text-slate-400">Protocolo</p>
                <p className="text-sm font-mono font-bold text-slate-800 dark:text-slate-200 select-all">{doacao.protocolo}</p>
                <span className="inline-block mt-1 text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950/70 text-amber-800 dark:text-amber-300">
                  Aguardando Pix
                </span>
              </div>
            </div>

            {qrCode && (
              <div className="flex flex-col items-center justify-center p-4 bg-slate-50 dark:bg-[#0e1526] rounded-2xl border border-slate-200/80 dark:border-slate-800 mb-6">
                <div className="p-3 bg-white rounded-xl shadow-xs border border-slate-200">
                  <img src={qrCode} alt="QR Code do Pix para pagar a doação" className="w-48 h-48" />
                </div>
                <p className="text-xs text-slate-500 dark:text-slate-400 mt-2 font-medium">
                  Abra o app do seu banco e aponte a câmera
                </p>
              </div>
            )}

            <div className="space-y-3 mb-6">
              <button
                type="button"
                onClick={() => copiar(doacao.pix.copia_e_cola)}
                className={`w-full px-4 py-3 rounded-xl font-bold text-sm transition-all cursor-pointer ${
                  copiado ? 'bg-emerald-600 text-white' : 'bg-[#fb2782] text-white hover:bg-pink-600 shadow-sm'
                }`}
              >
                {copiado ? '✓ Copiado!' : 'Copiar Pix copia e cola'}
              </button>
              <p role="status" className="sr-only">{copiado ? 'Código Pix copiado.' : ''}</p>

              <div>
                <label htmlFor="chave-pix" className="block text-xs uppercase font-bold text-slate-500 dark:text-slate-400 mb-1">
                  Ou use a chave Pix
                </label>
                <input
                  id="chave-pix"
                  type="text"
                  readOnly
                  value={doacao.pix.chave}
                  className="w-full px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-[#0e1526] text-slate-900 dark:text-white font-mono text-sm font-semibold select-all"
                />
              </div>
            </div>

            <p className="text-sm text-slate-600 dark:text-slate-300 mb-4">
              Depois de pagar, a ONG confirma sua doação pelo extrato. Guarde seu protocolo.
            </p>

            <div className="flex justify-end pt-4 border-t border-slate-100 dark:border-slate-800">
              <button
                type="button"
                onClick={() => setModalAberto(false)}
                className="px-5 py-2 rounded-xl bg-slate-900 dark:bg-slate-800 text-white font-bold text-xs hover:bg-slate-800 transition-colors"
              >
                Concluir
              </button>
            </div>
          </div>
        </div>
      )}
    </main>
  )
}
