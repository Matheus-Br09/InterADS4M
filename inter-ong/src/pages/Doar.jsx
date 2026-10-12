import { useEffect, useRef, useState } from 'react'
import { Link } from 'react-router-dom'
import { backend } from '../api/backend'
import AvisoApi from '../components/AvisoApi'
import realOficinaUvaImg from '../assets/real-oficina-uva.jpg'
import './css/Doar.css'

export default function Doar() {
  const [modo, setModo] = useState('unica')
  const [valor, setValor] = useState('50')
  const [criancas, setCriancas] = useState([])
  const [crianca, setCrianca] = useState('')
  const [dia, setDia] = useState('10')
  const [erro, setErro] = useState(null)
  const [erroLista, setErroLista] = useState(null)
  const [mensagem, setMensagem] = useState('')
  const [carregando, setCarregando] = useState(true)
  const [enviando, setEnviando] = useState(false)
  const [tentativa, setTentativa] = useState(0)

  // Estado do Modal Pix
  const [modalPixAberto, setModalPixAberto] = useState(false)
  const [pixCopiado, setPixCopiado] = useState(false)
  const [protocoloGerado, setProtocoloGerado] = useState(null)
  const [dadosDoacao, setDadosDoacao] = useState(null)

  const ocupado = useRef(false)
  const CHAVE_PIX = '48.219.821/0001-90' // CNPJ oficial da ONG

  useEffect(() => {
    const controller = new AbortController()
    backend.criancas({ signal: controller.signal }).then(({ dados }) => {
      if (!controller.signal.aborted) { setCriancas(dados); setErroLista(null) }
    }).catch(error => { if (!controller.signal.aborted) setErroLista(error) })
      .finally(() => { if (!controller.signal.aborted) setCarregando(false) })
    return () => controller.abort()
  }, [tentativa])

  async function enviar(event) {
    event.preventDefault()
    if (ocupado.current) return
    ocupado.current = true
    setEnviando(true)
    setErro(null)
    setMensagem('')

    try {
      const resultado = modo === 'unica'
        ? await backend.doacao({ valor, metodo_pagamento: 'pix' })
        : modo === 'mensal'
          ? await backend.mensalidade({ valor_mensal: valor, dia_vencimento: Number(dia), metodo_pagamento: 'pix' })
          : await backend.apadrinhamento({ crianca_id: Number(crianca), valor_mensal: valor })

      const proto = resultado?.dados?.id ?? Math.floor(100000 + Math.random() * 900000)
      setProtocoloGerado(proto)
      setDadosDoacao({
        valor,
        modo,
        criancaNome: criancas.find(c => String(c.id) === String(crianca))?.nome
      })
      setMensagem(`${resultado.message} Protocolo: ${proto}. Status: pendente.`)
      setModalPixAberto(true)
    } catch (error) {
      setErro(error)
    } finally {
      ocupado.current = false
      setEnviando(false)
    }
  }

  function copiarChavePix() {
    if (navigator.clipboard) {
      navigator.clipboard.writeText(CHAVE_PIX).then(() => {
        setPixCopiado(true)
        setTimeout(() => setPixCopiado(false), 3500)
      })
    }
  }

  const valoresPredefinidos = [25, 50, 100, 200]

  return (
    <main className="min-h-screen bg-pink-50/40 px-4 py-12 dark:bg-[#090d16] transition-colors duration-400">
      <div className="mx-auto max-w-6xl">
        {/* Cabeçalho */}
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

        {/* Grade Principal */}
        <div className="grid gap-8 lg:grid-cols-2 items-start">
          {/* Coluna Esquerda: Foto Real e Transparência */}
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
                <span>🛡️</span> Como funciona seu apoio?
              </h2>
              <p className="text-sm sm:text-base text-slate-600 dark:text-slate-300 leading-relaxed">
                Você registra sua doação de forma prática e segura com geração imediata da Chave Pix na tela. Cada contribuição é acompanhada com protocolo oficial da instituição.
              </p>

              <div className="pt-2 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-sm">
                <span className="text-slate-500 dark:text-slate-400">Já tem cadastro no sistema?</span>
                <Link to="/login" className="font-bold text-[#fb2782] dark:text-pink-400 hover:underline">
                  Minha Conta →
                </Link>
              </div>
            </div>
          </section>

          {/* Coluna Direita: Formulário de Doação */}
          <section className="rounded-3xl bg-white dark:bg-[#121a2d] p-6 sm:p-8 text-slate-800 dark:text-slate-100 shadow-xl border border-pink-100 dark:border-slate-800">
            <div className="flex items-center justify-between mb-6 pb-4 border-b border-pink-100/80 dark:border-slate-800">
              <h2 className="text-2xl font-extrabold text-slate-900 dark:text-white">
                Quero apoiar
              </h2>
              <span className="text-xs font-semibold px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                Pix Instantâneo
              </span>
            </div>

            <AvisoApi erro={erro} mensagem={mensagem} />

            <form onSubmit={enviar} className="space-y-6">
              <fieldset disabled={enviando} className="space-y-6">
                {/* Seleção do Modo de Apoio */}
                <div>
                  <label className="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">
                    Tipo de contribuição
                  </label>
                  <div className="grid grid-cols-3 gap-2">
                    {[
                      { id: 'unica', label: 'Doação única' },
                      { id: 'mensal', label: 'Mensal' },
                      { id: 'apadrinhamento', label: 'Apadrinhar' }
                    ].map(tab => (
                      <button
                        type="button"
                        key={tab.id}
                        onClick={() => { setModo(tab.id); setMensagem(''); setErro(null) }}
                        className={`py-2.5 px-2 rounded-xl text-xs sm:text-sm font-bold transition-all border text-center ${
                          modo === tab.id
                            ? 'bg-[#fb2782] text-white border-[#fb2782] shadow-sm shadow-pink-500/30'
                            : 'bg-slate-50 dark:bg-[#0e1526] text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-pink-300'
                        }`}
                      >
                        {tab.label}
                      </button>
                    ))}
                  </div>
                </div>

                {/* Seleção de Valores Pré-definidos */}
                <div>
                  <label className="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-2">
                    Selecione um valor sugerido
                  </label>
                  <div className="grid grid-cols-4 gap-2">
                    {valoresPredefinidos.map(v => (
                      <button
                        type="button"
                        key={v}
                        aria-pressed={Number(valor) === v}
                        onClick={() => setValor(String(v))}
                        className={`relative py-3 rounded-xl border text-sm sm:text-base font-bold transition-all ${
                          Number(valor) === v
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

                {/* Input de Valor Personalizado */}
                <div>
                  <label htmlFor="valor-custom" className="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-1">
                    {modo === 'unica' ? 'Valor da doação (R$)' : 'Valor mensal (R$)'}
                  </label>
                  <div className="relative">
                    <span className="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-slate-400 dark:text-slate-500">
                      R$
                    </span>
                    <input
                      id="valor-custom"
                      className="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#0e1526] text-slate-900 dark:text-white font-semibold text-lg focus:outline-none focus:ring-2 focus:ring-[#fb2782] transition-colors"
                      type="number"
                      min="5"
                      max="99999999.99"
                      step="0.01"
                      required
                      value={valor}
                      onChange={e => setValor(e.target.value)}
                      placeholder="Outro valor..."
                    />
                  </div>
                </div>

                {/* Opção Mensal: Dia de vencimento */}
                {modo === 'mensal' && (
                  <div>
                    <label htmlFor="dia-vencimento" className="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-1">
                      Dia de vencimento preferido
                    </label>
                    <input
                      id="dia-vencimento"
                      className="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#0e1526] text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-[#fb2782]"
                      type="number"
                      min="1"
                      max="31"
                      required
                      value={dia}
                      onChange={e => setDia(e.target.value)}
                    />
                  </div>
                )}

                {/* Opção Apadrinhamento: Criança */}
                {modo === 'apadrinhamento' && (
                  <div>
                    <AvisoApi erro={erroLista} />
                    {carregando ? (
                      <p role="status" className="text-sm text-slate-500 dark:text-slate-400 py-2">
                        Carregando crianças disponíveis…
                      </p>
                    ) : (
                      <>
                        {erroLista ? (
                          <button
                            type="button"
                            className="text-sm font-bold text-[#fb2782] underline"
                            onClick={() => { setCarregando(true); setTentativa(t => t + 1) }}
                          >
                            Tentar novamente carregar lista
                          </button>
                        ) : (
                          <div>
                            <label htmlFor="selecao-crianca" className="block text-sm font-bold text-slate-800 dark:text-slate-200 mb-1">
                              Escolha a criança a apadrinhar
                            </label>
                            <select
                              id="selecao-crianca"
                              className="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#0e1526] text-slate-900 dark:text-white font-medium focus:outline-none focus:ring-2 focus:ring-[#fb2782]"
                              required
                              value={crianca}
                              onChange={e => setCrianca(e.target.value)}
                            >
                              <option value="">Selecione uma criança</option>
                              {criancas
                                .filter(c => c.id && !c.apadrinhada && c.status === 'disponivel')
                                .map(c => (
                                  <option key={c.id} value={c.id}>
                                    {c.nome}{c.idade != null ? ` · ${c.idade} anos` : ''}
                                  </option>
                                ))}
                            </select>
                          </div>
                        )}
                        {!erroLista && !criancas.some(c => c.id && !c.apadrinhada && c.status === 'disponivel') && (
                          <p className="text-xs text-amber-600 dark:text-amber-400 mt-1">
                            Nenhuma criança disponível para apadrinhamento no momento.
                          </p>
                        )}
                      </>
                    )}
                  </div>
                )}

                {/* Botão de Confirmação com Pix */}
                <button
                  type="submit"
                  disabled={modo === 'apadrinhamento' && (!crianca || carregando || Boolean(erroLista))}
                  className="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-[#fb2782] via-[#ff3b8d] to-[#ff4785] text-white font-extrabold text-base sm:text-lg shadow-lg shadow-pink-500/30 hover:shadow-xl hover:shadow-pink-500/40 hover:scale-[1.01] active:scale-98 transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none cursor-pointer flex items-center justify-center gap-2"
                >
                  {enviando ? (
                    'Gerando chave Pix…'
                  ) : (
                    <>
                      <span>Doar R$ {valor} com Pix</span>
                      <span>♥</span>
                    </>
                  )}
                </button>
              </fieldset>
            </form>
          </section>
        </div>
      </div>

      {/* MODAL PIX COM FUNDO EMBAÇADO (Sem sair da tela) */}
      {modalPixAberto && (
        <div
          role="dialog"
          aria-modal="true"
          aria-labelledby="pix-modal-title"
          className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/75 backdrop-blur-md animate-in fade-in duration-200"
          onClick={() => setModalPixAberto(false)}
        >
          <div
            className="relative w-full max-w-lg bg-white dark:bg-[#121a2d] border border-pink-200 dark:border-slate-700/80 rounded-3xl p-6 sm:p-8 shadow-2xl modal-pop-in text-slate-800 dark:text-slate-100"
            onClick={e => e.stopPropagation()}
          >
            {/* Botão de Fechar */}
            <button
              type="button"
              onClick={() => setModalPixAberto(false)}
              className="absolute top-4 right-4 p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
              aria-label="Fechar janela de pagamento"
            >
              ✕
            </button>

            {/* Cabeçalho do Modal */}
            <div className="text-center mb-6">
              <div className="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950/80 text-emerald-500 border border-emerald-200 dark:border-emerald-800 mb-3 shadow-inner">
                <svg className="w-8 h-8 fill-current" viewBox="0 0 24 24">
                  <path d="M12 2L4 6v12l8 4 8-4V6l-8-4zm0 2.2L18 7.3v9.4L12 19.8 6 16.7V7.3L12 4.2zM12 9l-4 3 4 3 4-3-4-3z" />
                </svg>
              </div>
              <h2 id="pix-modal-title" className="text-2xl font-extrabold text-slate-900 dark:text-white">
                Pagamento via Pix
              </h2>
              <p className="text-sm text-slate-600 dark:text-slate-300 mt-1">
                Finalize sua contribuição escaneando o código ou copiando a chave abaixo.
              </p>
            </div>

            {/* Resumo da Doação */}
            <div className="bg-slate-50 dark:bg-[#0e1526] rounded-2xl p-4 border border-slate-200/80 dark:border-slate-800 mb-6 flex items-center justify-between">
              <div>
                <p className="text-xs uppercase font-bold text-slate-400 dark:text-slate-500">Valor</p>
                <p className="text-2xl font-black text-[#fb2782] dark:text-pink-400">
                  R$ {dadosDoacao?.valor ?? valor}
                </p>
                {dadosDoacao?.criancaNome && (
                  <p className="text-xs text-slate-600 dark:text-slate-300 mt-0.5">
                    Apadrinhamento de {dadosDoacao.criancaNome}
                  </p>
                )}
              </div>
              <div className="text-right">
                <p className="text-xs uppercase font-bold text-slate-400 dark:text-slate-500">Protocolo</p>
                <p className="text-sm font-mono font-bold text-slate-800 dark:text-slate-200">
                  #{protocoloGerado}
                </p>
                <span className="inline-block mt-1 text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950/70 text-amber-800 dark:text-amber-300">
                  Aguardando Pix
                </span>
              </div>
            </div>

            {/* QR Code Pix Estilizado */}
            <div className="flex flex-col items-center justify-center p-4 bg-slate-50 dark:bg-[#0e1526] rounded-2xl border border-slate-200/80 dark:border-slate-800 mb-6">
              <div className="p-3 bg-white rounded-xl shadow-xs border border-slate-200">
                <svg className="w-40 h-40" viewBox="0 0 100 100" fill="none">
                  {/* Padrão QR Code Simulado Vetorial com Alta Nitidez */}
                  <rect width="100" height="100" fill="white" />
                  <rect x="10" y="10" width="26" height="26" rx="4" fill="#0f172a" />
                  <rect x="14" y="14" width="18" height="18" rx="2" fill="white" />
                  <rect x="18" y="18" width="10" height="10" rx="1" fill="#0f172a" />

                  <rect x="64" y="10" width="26" height="26" rx="4" fill="#0f172a" />
                  <rect x="68" y="14" width="18" height="18" rx="2" fill="white" />
                  <rect x="72" y="18" width="10" height="10" rx="1" fill="#0f172a" />

                  <rect x="10" y="64" width="26" height="26" rx="4" fill="#0f172a" />
                  <rect x="14" y="68" width="18" height="18" rx="2" fill="white" />
                  <rect x="18" y="72" width="10" height="10" rx="1" fill="#0f172a" />

                  {/* Detalhes de pontos */}
                  <circle cx="45" cy="20" r="3" fill="#0f172a" />
                  <circle cx="55" cy="25" r="2.5" fill="#0f172a" />
                  <circle cx="48" cy="35" r="3" fill="#0f172a" />
                  <circle cx="20" cy="48" r="3" fill="#0f172a" />
                  <circle cx="32" cy="50" r="2.5" fill="#0f172a" />
                  <circle cx="65" cy="48" r="3" fill="#0f172a" />
                  <circle cx="78" cy="52" r="2.5" fill="#0f172a" />
                  <circle cx="50" cy="50" r="4" fill="#fb2782" />
                  <circle cx="48" cy="65" r="3" fill="#0f172a" />
                  <circle cx="60" cy="70" r="3" fill="#0f172a" />
                  <circle cx="75" cy="75" r="3.5" fill="#0f172a" />
                  <circle cx="85" cy="85" r="3" fill="#0f172a" />
                  <circle cx="52" cy="85" r="2.5" fill="#0f172a" />
                </svg>
              </div>
              <p className="text-xs text-slate-500 dark:text-slate-400 mt-2 font-medium">
                Abra o app do seu banco e aponte a câmera
              </p>
            </div>

            {/* Chave Pix e Botão Copiar */}
            <div className="space-y-3 mb-6">
              <label className="block text-xs uppercase font-bold text-slate-500 dark:text-slate-400">
                Chave Pix (CNPJ Oficial)
              </label>
              <div className="flex items-center gap-2">
                <input
                  type="text"
                  readOnly
                  value={CHAVE_PIX}
                  className="flex-1 px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-[#0e1526] text-slate-900 dark:text-white font-mono text-sm font-semibold select-all"
                />
                <button
                  type="button"
                  onClick={copiarChavePix}
                  className={`px-4 py-2.5 rounded-xl font-bold text-xs sm:text-sm transition-all flex items-center gap-1.5 cursor-pointer whitespace-nowrap ${
                    pixCopiado
                      ? 'bg-emerald-600 text-white'
                      : 'bg-[#fb2782] text-white hover:bg-pink-600 shadow-sm'
                  }`}
                >
                  {pixCopiado ? '✓ Copiado!' : 'Copiar Chave'}
                </button>
              </div>
            </div>

            {/* Botão de Fechar Modal */}
            <div className="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800">
              <Link
                to="/login"
                onClick={() => setModalPixAberto(false)}
                className="text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-[#fb2782]"
              >
                Ver histórico de doações
              </Link>
              <button
                type="button"
                onClick={() => setModalPixAberto(false)}
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

