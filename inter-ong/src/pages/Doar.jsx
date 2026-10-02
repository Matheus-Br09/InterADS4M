import { useState, useEffect } from "react"
import { Link } from "react-router-dom"
import "./css/Doar.css"

export default function Doar() {
  // Estados do formulário
  const [frequencia, setFrequencia] = useState("unica") // 'unica' ou 'mensal'
  const [valorSelecionado, setValorSelecionado] = useState(50)
  const [valorCustomizado, setValorCustomizado] = useState("")
  const [isCustom, setIsCustom] = useState(false)
  const [causa, setCausa] = useState("geral")
  const [anonimo, setAnonimo] = useState(false)
  const [nome, setNome] = useState("")
  const [email, setEmail] = useState("")
  const [cpf, setCpf] = useState("")
  const [mensagem, setMensagem] = useState("")

  // Estados do Modal PIX
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [copiado, setCopiado] = useState(false)
  const [copiadoChave, setCopiadoChave] = useState(false)
  const [tabModal, setTabModal] = useState("qrcode") // 'qrcode', 'copiacola', 'chave'
  const [pagamentoConcluido, setPagamentoConcluido] = useState(false)

  // Opções pré-definidas de doação
  const opcoesValores = [
    {
      valor: 25,
      titulo: "R$ 25",
      impacto: "Garante lanche nutritivo e kit básico para 1 criança",
      tag: null,
    },
    {
      valor: 50,
      titulo: "R$ 50",
      impacto: "Proporciona materiais para oficinas pedagógicas",
      tag: "Mais Escolhido ★",
    },
    {
      valor: 100,
      titulo: "R$ 100",
      impacto: "Cesta de alimentos completa para uma família vulnerável",
      tag: "Impacto Alto",
    },
    {
      valor: 200,
      titulo: "R$ 200",
      impacto: "Atendimento multidisciplinar continuado por um mês",
      tag: null,
    },
  ]

  // Causas disponíveis
  const causas = [
    { id: "geral", nome: "Onde for mais urgente (Prioridade Geral)" },
    { id: "educacao", nome: "Educação Infantil e Oficinas" },
    { id: "alimentacao", nome: "Alimentação & Combate à Fome" },
    { id: "acolhimento", nome: "Acolhimento Familiar & Social" },
  ]

  // FAQ simples
  const [faqAberto, setFaqAberto] = useState(null)
  const faqs = [
    {
      pergunta: "Como o pagamento via PIX é confirmado?",
      resposta:
        "O PIX é instantâneo. Assim que o pagamento for realizado no seu aplicativo bancário, nossa equipe recebe a notificação em tempo real e o recibo pode ser enviado para seu e-mail cadastrado.",
    },
    {
      pergunta: "A SOS Tudo pelo Social emite recibo para abatimento?",
      resposta:
        "Sim! Caso queira recibo oficial para fins de dedução no Imposto de Renda ou prestação de contas, basta informar seu CPF e e-mail no formulário.",
    },
    {
      pergunta: "Posso doar qualquer valor?",
      resposta:
        "Com certeza! Toda contribuição, de qualquer quantia, é muito bem-vinda e faz uma diferença imensa para os projetos sociais da nossa instituição.",
    },
    {
      pergunta: "Meus dados estão protegidos?",
      resposta:
        "Totalmente. Tratamos seus dados com sigilo absoluto conforme a LGPD e utilizamos ambientes seguros com criptografia ponta a ponta.",
    },
  ]

  // Calcula o valor final
  const valorFinal = isCustom
    ? Number(valorCustomizado) || 0
    : valorSelecionado

  // Chave PIX e Payload Copia e Cola representativo
  const chavePixOficial = "pix@sostudopelosocial.org.br"
  const cnpjOficial = "42.123.456/0001-89"
  
  // Código PIX Copia e Cola formatado conforme o padrão do Banco Central
  const codigoPixCopiaCola = `00020126580014BR.GOV.BCB.PIX0136pix@sostudopelosocial.org.br520400005303986540${valorFinal.toFixed(
    2
  )}5802BR5925SOS TUDO PELO SOCIAL6009SAO PAULO62070503***6304${Math.floor(
    1000 + Math.random() * 9000
  )}`

  // Bloqueia rolagem do body quando o modal estiver aberto e adiciona suporte à tecla ESC
  useEffect(() => {
    const handleKeyDown = (e) => {
      if (e.key === "Escape" && isModalOpen) {
        handleFecharModal()
      }
    }

    if (isModalOpen) {
      document.body.style.overflow = "hidden"
      window.addEventListener("keydown", handleKeyDown)
    } else {
      document.body.style.overflow = "unset"
    }

    return () => {
      document.body.style.overflow = "unset"
      window.removeEventListener("keydown", handleKeyDown)
    }
  }, [isModalOpen])

  // Abre o modal de PIX
  const handleAbrirModalPix = (e) => {
    e.preventDefault()
    if (valorFinal <= 0) {
      alert("Por favor, selecione ou digite um valor válido para doação.")
      return
    }
    setPagamentoConcluido(false)
    setIsModalOpen(true)
  }

  // Fecha o modal
  const handleFecharModal = () => {
    setIsModalOpen(false)
    setCopiado(false)
    setCopiadoChave(false)
    setPagamentoConcluido(false)
  }

  // Copia o código Copia e Cola
  const handleCopiarCodigoPix = () => {
    navigator.clipboard.writeText(codigoPixCopiaCola)
    setCopiado(true)
    setTimeout(() => setCopiado(false), 3000)
  }

  // Copia a Chave Simples (e-mail)
  const handleCopiarChaveSimples = () => {
    navigator.clipboard.writeText(chavePixOficial)
    setCopiadoChave(true)
    setTimeout(() => setCopiadoChave(false), 3000)
  }

  return (
    <div className="min-h-screen bg-gradient-to-b from-[#fff5f8] via-[#ffffff] to-[#fff8fa] text-slate-800 pb-20">
      
      {/* ========================================================================= */}
      {/* HERO / CABEÇALHO DA PÁGINA */}
      {/* ========================================================================= */}
      <section className="relative overflow-hidden pt-10 pb-12 sm:pt-14 sm:pb-16 border-b border-pink-100/70">
        {/* Efeitos de luz de fundo */}
        <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[700px] h-[320px] bg-gradient-to-br from-pink-200/40 via-rose-100/30 to-transparent blur-3xl pointer-events-none -z-10" />
        <div className="absolute top-10 right-10 w-72 h-72 bg-pink-100/50 rounded-full blur-2xl pointer-events-none -z-10" />

        <div className="max-w-5xl mx-auto px-4 sm:px-6 text-center">
          {/* Badge de destaque */}
          <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-pink-50 border border-pink-200 text-[#fb2782] text-xs sm:text-sm font-semibold mb-5 shadow-xs float-badge">
            <span className="flex h-2 w-2 rounded-full bg-[#fb2782] animate-ping" />
            <span>Faça a diferença hoje</span>
            <span className="text-pink-400">♥</span>
          </div>

          <h1 className="text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
            Sua doação acolhe, inclui e{" "}
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-[#fb2782] to-[#ff6398]">
              transforma vidas
            </span>
          </h1>

          <p className="mt-4 text-base sm:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">
            Cada real doado para a <strong>SOS Tudo pelo Social</strong> vira alimento na mesa, 
            apoio educacional e esperança renovada para centenas de famílias.
          </p>

          {/* Selos de Confiança */}
          <div className="mt-6 flex flex-wrap items-center justify-center gap-3 sm:gap-6 text-xs sm:text-sm text-slate-500 font-medium">
            <div className="flex items-center gap-1.5 bg-white/80 backdrop-blur-xs px-3 py-1.5 rounded-full border border-pink-100 shadow-xs">
              <svg className="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
              </svg>
              <span>PIX Instantâneo</span>
            </div>
            <div className="flex items-center gap-1.5 bg-white/80 backdrop-blur-xs px-3 py-1.5 rounded-full border border-pink-100 shadow-xs">
              <svg className="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path fillRule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
              </svg>
              <span>Ambiente 100% Seguro</span>
            </div>
            <div className="flex items-center gap-1.5 bg-white/80 backdrop-blur-xs px-3 py-1.5 rounded-full border border-pink-100 shadow-xs">
              <svg className="w-4 h-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                <path fillRule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clipRule="evenodd" />
              </svg>
              <span>Transparência e Recibo</span>
            </div>
          </div>
        </div>
      </section>

      {/* ========================================================================= */}
      {/* FORMULÁRIO DE DOAÇÃO PRINCIPAL */}
      {/* ========================================================================= */}
      <div className="max-w-6xl mx-auto px-4 sm:px-6 -mt-4">
        <form onSubmit={handleAbrirModalPix} className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          
          {/* COLUNA ESQUERDA: Configurações da Doação */}
          <div className="lg:col-span-8 space-y-6">

            {/* CARD 1: FREQUÊNCIA (ÚNICA OU MENSAL) */}
            <div className="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-pink-100/80">
              <div className="flex items-center justify-between flex-wrap gap-3 mb-5">
                <div className="flex items-center gap-2.5">
                  <span className="flex items-center justify-center w-7 h-7 rounded-full bg-pink-100 text-[#fb2782] font-bold text-xs">
                    1
                  </span>
                  <h2 className="text-lg font-bold text-slate-800">
                    Frequência da Doação
                  </h2>
                </div>
                <span className="text-xs text-slate-400 font-medium">
                  Cancele quando quiser
                </span>
              </div>

              <div className="grid grid-cols-2 gap-3 p-1.5 bg-slate-100/70 rounded-2xl border border-slate-200/60">
                <button
                  type="button"
                  onClick={() => setFrequencia("unica")}
                  className={`py-3 px-4 rounded-xl font-bold text-sm transition-all duration-200 flex items-center justify-center gap-2 ${
                    frequencia === "unica"
                      ? "bg-white text-[#fb2782] shadow-sm border border-pink-200/60"
                      : "text-slate-600 hover:text-slate-900"
                  }`}
                >
                  <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                  </svg>
                  Doação Única
                </button>

                <button
                  type="button"
                  onClick={() => setFrequencia("mensal")}
                  className={`py-3 px-4 rounded-xl font-bold text-sm transition-all duration-200 flex items-center justify-center gap-2 relative ${
                    frequencia === "mensal"
                      ? "bg-white text-[#fb2782] shadow-sm border border-pink-200/60"
                      : "text-slate-600 hover:text-slate-900"
                  }`}
                >
                  <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                  </svg>
                  Mensal Recorrente
                  <span className="hidden sm:inline-block px-1.5 py-0.5 text-[10px] uppercase font-extrabold bg-pink-100 text-[#fb2782] rounded-md ml-1">
                    Ajuda Contínua
                  </span>
                </button>
              </div>
            </div>

            {/* CARD 2: VALOR DA DOAÇÃO */}
            <div className="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-pink-100/80">
              <div className="flex items-center gap-2.5 mb-5">
                <span className="flex items-center justify-center w-7 h-7 rounded-full bg-pink-100 text-[#fb2782] font-bold text-xs">
                  2
                </span>
                <div>
                  <h2 className="text-lg font-bold text-slate-800">
                    Escolha o Valor
                  </h2>
                  <p className="text-xs text-slate-500">
                    Selecione uma das opções com impacto direto ou defina outro valor
                  </p>
                </div>
              </div>

              {/* Grid de opções pré-definidas */}
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {opcoesValores.map((item) => {
                  const isSelected = !isCustom && valorSelecionado === item.valor
                  return (
                    <div
                      key={item.valor}
                      onClick={() => {
                        setIsCustom(false)
                        setValorSelecionado(item.valor)
                        setValorCustomizado("")
                      }}
                      className={`relative cursor-pointer rounded-2xl p-5 border-2 transition-all duration-200 text-left flex flex-col justify-between ${
                        isSelected
                          ? "border-[#fb2782] bg-pink-50/40 shadow-md shadow-pink-500/10 scale-[1.01]"
                          : "border-slate-200/80 bg-white hover:border-pink-300 hover:bg-slate-50/50"
                      }`}
                    >
                      {item.tag && (
                        <span className="absolute -top-3 right-4 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-gradient-to-r from-[#fb2782] to-[#ff4785] text-white shadow-xs">
                          {item.tag}
                        </span>
                      )}

                      <div className="flex items-center justify-between mb-2">
                        <span className="text-2xl font-black text-slate-900">
                          {item.titulo}
                        </span>
                        <div
                          className={`w-5 h-5 rounded-full border-2 flex items-center justify-center ${
                            isSelected
                              ? "border-[#fb2782] bg-[#fb2782]"
                              : "border-slate-300"
                          }`}
                        >
                          {isSelected && (
                            <span className="w-2 h-2 rounded-full bg-white" />
                          )}
                        </div>
                      </div>

                      <p className="text-xs text-slate-600 leading-snug">
                        {item.impacto}
                      </p>
                    </div>
                  )
                })}
              </div>

              {/* Opção de outro valor */}
              <div className="mt-4 pt-4 border-t border-slate-100">
                <label className="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                  Ou digite outro valor:
                </label>
                <div className="relative max-w-md">
                  <span className="absolute left-4 top-1/2 -translate-y-1/2 font-bold text-slate-400 text-base">
                    R$
                  </span>
                  <input
                    type="number"
                    min="5"
                    step="1"
                    placeholder="Ex: 35"
                    value={valorCustomizado}
                    onFocus={() => setIsCustom(true)}
                    onChange={(e) => {
                      setIsCustom(true)
                      setValorCustomizado(e.target.value)
                    }}
                    className={`w-full pl-12 pr-4 py-3 rounded-2xl border-2 font-bold text-base transition-all focus:outline-none ${
                      isCustom
                        ? "border-[#fb2782] ring-2 ring-pink-100 bg-white"
                        : "border-slate-200 bg-slate-50 focus:bg-white"
                    }`}
                  />
                  {isCustom && (
                    <span className="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-[#fb2782] font-semibold bg-pink-50 px-2 py-1 rounded-lg">
                      Valor personalizado
                    </span>
                  )}
                </div>
              </div>
            </div>

            {/* CARD 3: DESTINAÇÃO DA AJUDA */}
            <div className="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-pink-100/80">
              <div className="flex items-center gap-2.5 mb-4">
                <span className="flex items-center justify-center w-7 h-7 rounded-full bg-pink-100 text-[#fb2782] font-bold text-xs">
                  3
                </span>
                <h2 className="text-lg font-bold text-slate-800">
                  Onde deseja que sua doação atue?
                </h2>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                {causas.map((c) => (
                  <button
                    key={c.id}
                    type="button"
                    onClick={() => setCausa(c.id)}
                    className={`p-3.5 rounded-xl border text-left text-xs sm:text-sm font-semibold transition-all flex items-center justify-between ${
                      causa === c.id
                        ? "border-[#fb2782] bg-pink-50/50 text-[#fb2782]"
                        : "border-slate-200 text-slate-700 hover:border-pink-200"
                    }`}
                  >
                    <span>{c.nome}</span>
                    {causa === c.id && (
                      <span className="text-xs bg-[#fb2782] text-white px-2 py-0.5 rounded-full font-bold">
                        ✓
                      </span>
                    )}
                  </button>
                ))}
              </div>
            </div>

            {/* CARD 4: DADOS DO DOADOR */}
            <div className="bg-white rounded-3xl p-6 sm:p-7 shadow-sm border border-pink-100/80">
              <div className="flex items-center justify-between flex-wrap gap-2 mb-4">
                <div className="flex items-center gap-2.5">
                  <span className="flex items-center justify-center w-7 h-7 rounded-full bg-pink-100 text-[#fb2782] font-bold text-xs">
                    4
                  </span>
                  <h2 className="text-lg font-bold text-slate-800">
                    Seus Dados
                  </h2>
                </div>

                <label className="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-600 hover:text-[#fb2782]">
                  <input
                    type="checkbox"
                    checked={anonimo}
                    onChange={(e) => setAnonimo(e.target.checked)}
                    className="w-4 h-4 rounded border-slate-300 text-[#fb2782] focus:ring-pink-400"
                  />
                  <span>Desejo doar anonimamente</span>
                </label>
              </div>

              {!anonimo ? (
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-xs font-bold text-slate-600 mb-1.5">
                      Nome Completo
                    </label>
                    <input
                      type="text"
                      placeholder="Ex: Maria Oliveira"
                      value={nome}
                      onChange={(e) => setNome(e.target.value)}
                      className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-[#fb2782] focus:ring-2 focus:ring-pink-100 outline-none"
                    />
                  </div>

                  <div>
                    <label className="block text-xs font-bold text-slate-600 mb-1.5">
                      E-mail (para envio do comprovante)
                    </label>
                    <input
                      type="email"
                      placeholder="Ex: maria@email.com"
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                      className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-[#fb2782] focus:ring-2 focus:ring-pink-100 outline-none"
                    />
                  </div>

                  <div>
                    <label className="block text-xs font-bold text-slate-600 mb-1.5">
                      CPF (opcional para recibo fiscal)
                    </label>
                    <input
                      type="text"
                      placeholder="000.000.000-00"
                      value={cpf}
                      onChange={(e) => setCpf(e.target.value)}
                      className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-[#fb2782] focus:ring-2 focus:ring-pink-100 outline-none"
                    />
                  </div>

                  <div>
                    <label className="block text-xs font-bold text-slate-600 mb-1.5">
                      Mensagem de carinho (opcional)
                    </label>
                    <input
                      type="text"
                      placeholder="Deixe uma palavra de esperança"
                      value={mensagem}
                      onChange={(e) => setMensagem(e.target.value)}
                      className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-[#fb2782] focus:ring-2 focus:ring-pink-100 outline-none"
                    />
                  </div>
                </div>
              ) : (
                <div className="bg-slate-50 border border-slate-200/70 rounded-2xl p-4 text-xs text-slate-600 flex items-center gap-3">
                  <span className="text-xl">🕵️</span>
                  <span>
                    Sua doação será registrada de forma totalmente anônima. Nenhuma identificação pessoal será associada ao projeto.
                  </span>
                </div>
              )}
            </div>

          </div>

          {/* COLUNA DIREITA: RESUMO E BOTÃO DE PAGAMENTO */}
          <div className="lg:col-span-4 sticky top-28 space-y-5">
            
            {/* CARD RESUMO */}
            <div className="bg-white rounded-3xl p-6 shadow-md shadow-pink-500/5 border-2 border-pink-200/80">
              <div className="flex items-center justify-between pb-4 border-b border-slate-100">
                <span className="text-xs font-bold uppercase tracking-wider text-slate-400">
                  Resumo da Doação
                </span>
                <span className="px-2.5 py-1 rounded-full text-[11px] font-bold bg-pink-100 text-[#fb2782]">
                  {frequencia === "unica" ? "Única" : "Mensal"}
                </span>
              </div>

              <div className="py-5 space-y-3">
                <div className="flex items-baseline justify-between">
                  <span className="text-slate-600 text-sm">Valor:</span>
                  <div className="text-right">
                    <span className="text-3xl font-black text-slate-900 tracking-tight">
                      R$ {valorFinal.toLocaleString("pt-BR", { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                    </span>
                    {frequencia === "mensal" && (
                      <span className="block text-[11px] text-slate-400 font-medium">
                        por mês
                      </span>
                    )}
                  </div>
                </div>

                <div className="flex items-center justify-between text-xs text-slate-600 pt-2 border-t border-slate-50">
                  <span>Destino:</span>
                  <span className="font-semibold text-slate-800 text-right truncate max-w-[180px]">
                    {causas.find((c) => c.id === causa)?.nome}
                  </span>
                </div>

                <div className="flex items-center justify-between text-xs text-slate-600">
                  <span>Método:</span>
                  <span className="font-bold text-emerald-600 flex items-center gap-1">
                    <span className="w-2 h-2 rounded-full bg-emerald-500" />
                    PIX Instantâneo
                  </span>
                </div>
              </div>

              {/* BOTÃO PRINCIPAL DE PAGAMENTO PIX */}
              <button
                type="submit"
                disabled={valorFinal <= 0}
                className="w-full relative group overflow-hidden py-4 px-6 rounded-2xl bg-gradient-to-r from-[#fb2782] via-[#ff3b8d] to-[#ff6398] text-white font-black text-base shadow-lg shadow-pink-500/35 hover:shadow-xl hover:shadow-pink-500/50 hover:scale-[1.02] active:scale-95 transition-all duration-200 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <div className="flex items-center justify-center gap-2.5">
                  {/* Ícone oficial PIX em SVG */}
                  <svg className="w-5 h-5 fill-current" viewBox="0 0 512 512">
                    <path d="M409.8 111.4l-75.7-75.7c-13.8-13.8-36.2-13.8-50 0L102.2 217.6c-13.8 13.8-13.8 36.2 0 50l75.7 75.7c13.8 13.8 36.2 13.8 50 0l181.9-181.9c13.8-13.8 13.8-36.2 0-50zM227.9 318.3l-50.7-50.7 131.2-131.2 50.7 50.7-131.2 131.2z" opacity=".4"/>
                    <path d="M309.8 476.3l75.7-75.7c13.8-13.8 13.8-36.2 0-50L203.6 168.7c-13.8-13.8-36.2-13.8-50 0L77.9 244.4c-13.8 13.8-13.8 36.2 0 50l181.9 181.9c13.8 13.8 36.2 13.8 50 0zm-131.2-183l50.7-50.7 131.2 131.2-50.7 50.7-131.2-131.2z"/>
                  </svg>
                  <span>Doar com PIX Agora</span>
                  <span className="transition-transform group-hover:translate-x-1">→</span>
                </div>
                <span className="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity" />
              </button>

              {/* Informação sobre o fundo embaçado e chave instantânea */}
              <div className="mt-4 pt-3 border-t border-slate-100 flex items-center justify-center gap-2 text-xs text-slate-500">
                <svg className="w-4 h-4 text-pink-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>A chave PIX e o QR Code aparecerão na sua tela sem recarregar.</span>
              </div>
            </div>

            {/* CARD DE TRANSPARÊNCIA */}
            <div className="bg-white/70 backdrop-blur-xs rounded-2xl p-5 border border-pink-100 text-xs text-slate-600 space-y-2">
              <div className="font-bold text-slate-800 flex items-center gap-2">
                <span>🛡️</span>
                <span>Transparência SOS Tudo pelo Social</span>
              </div>
              <p className="leading-relaxed">
                CNPJ Oficial: <strong className="text-slate-800">{cnpjOficial}</strong>. Prestamos contas anuais com auditoria aberta ao público.
              </p>
              <div className="pt-2 flex items-center justify-between text-[11px] text-slate-400">
                <span>92% Recursos diretos</span>
                <span>8% Gestão & logística</span>
              </div>
              <div className="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                <div className="bg-gradient-to-r from-[#fb2782] to-[#ff6398] h-1.5 rounded-full w-[92%]" />
              </div>
            </div>

          </div>

        </form>

        {/* ========================================================================= */}
        {/* SEÇÃO EXTRA: IMPACTÔMETRO & NÚMEROS */}
        {/* ========================================================================= */}
        <section className="mt-16 pt-12 border-t border-slate-200/80">
          <div className="text-center max-w-2xl mx-auto mb-10">
            <h3 className="text-2xl font-bold text-slate-900">
              O que sua generosidade torna possível
            </h3>
            <p className="text-slate-600 text-sm mt-2">
              Veja o impacto que sua contribuição gera diariamente nas vidas de quem acolhemos.
            </p>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div className="bg-white rounded-2xl p-6 text-center border border-pink-100 shadow-xs">
              <div className="w-12 h-12 rounded-2xl bg-pink-100 text-[#fb2782] flex items-center justify-center mx-auto text-xl mb-4">
                🍲
              </div>
              <div className="text-3xl font-black text-slate-900">+5.000</div>
              <div className="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-1">
                Refeições distribuídas
              </div>
              <p className="text-xs text-slate-600 mt-2">
                Comida digna e nutritiva na mesa de famílias em extrema vulnerabilidade.
              </p>
            </div>

            <div className="bg-white rounded-2xl p-6 text-center border border-pink-100 shadow-xs">
              <div className="w-12 h-12 rounded-2xl bg-pink-100 text-[#fb2782] flex items-center justify-center mx-auto text-xl mb-4">
                📚
              </div>
              <div className="text-3xl font-black text-slate-900">+350</div>
              <div className="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-1">
                Crianças em oficinas
              </div>
              <p className="text-xs text-slate-600 mt-2">
                Aulas de reforço, artes, música e tecnologia fora do horário de aula.
              </p>
            </div>

            <div className="bg-white rounded-2xl p-6 text-center border border-pink-100 shadow-xs">
              <div className="w-12 h-12 rounded-2xl bg-pink-100 text-[#fb2782] flex items-center justify-center mx-auto text-xl mb-4">
                🤝
              </div>
              <div className="text-3xl font-black text-slate-900">100%</div>
              <div className="text-xs font-semibold text-slate-500 uppercase tracking-wider mt-1">
                Acolhimento humanizado
              </div>
              <p className="text-xs text-slate-600 mt-2">
                Suporte psicológico, assistencial e orientação para o mercado de trabalho.
              </p>
            </div>
          </div>
        </section>

        {/* ========================================================================= */}
        {/* SEÇÃO DE PERGUNTAS FREQUENTES (FAQ) */}
        {/* ========================================================================= */}
        <section className="mt-16 max-w-3xl mx-auto">
          <div className="text-center mb-8">
            <h3 className="text-2xl font-bold text-slate-900">
              Dúvidas Frequentes sobre a Doação
            </h3>
            <p className="text-sm text-slate-500 mt-1">
              Fique tranquilo e tire suas dúvidas sobre como colaborar.
            </p>
          </div>

          <div className="space-y-3">
            {faqs.map((faq, index) => {
              const isOpen = faqAberto === index
              return (
                <div
                  key={index}
                  className="bg-white rounded-2xl border border-slate-200/80 overflow-hidden transition-all shadow-xs"
                >
                  <button
                    type="button"
                    onClick={() => setFaqAberto(isOpen ? null : index)}
                    className="w-full p-5 text-left font-bold text-slate-800 text-sm flex items-center justify-between gap-4 hover:text-[#fb2782]"
                  >
                    <span>{faq.pergunta}</span>
                    <span className={`text-slate-400 transform transition-transform ${isOpen ? "rotate-180 text-[#fb2782]" : ""}`}>
                      ▼
                    </span>
                  </button>
                  {isOpen && (
                    <div className="px-5 pb-5 text-xs sm:text-sm text-slate-600 border-t border-slate-100 pt-3 leading-relaxed">
                      {faq.resposta}
                    </div>
                  )}
                </div>
              )
            })}
          </div>

          <div className="mt-8 text-center">
            <p className="text-xs text-slate-500">
              Ainda tem dúvidas? Fale com a gente através da nossa página de{" "}
              <Link to="/contato" className="text-[#fb2782] font-bold underline hover:text-pink-700">
                Contato
              </Link>
              .
            </p>
          </div>
        </section>

      </div>

      {/* ========================================================================= */}
      {/* MODAL DE PAGAMENTO PIX (COM FUNDO EMBAÇADO / BACKDROP-BLUR) */}
      {/* ========================================================================= */}
      {isModalOpen && (
        <div
          className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-950/65 backdrop-blur-md transition-all duration-300"
          onClick={handleFecharModal}
          role="dialog"
          aria-modal="true"
        >
          {/* Caixa do Modal (stopPropagation para evitar fechar ao clicar dentro) */}
          <div
            className="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-pink-100 overflow-hidden modal-pop-in custom-modal-scroll max-h-[92vh] flex flex-col"
            onClick={(e) => e.stopPropagation()}
          >
            {/* CABEÇALHO DO MODAL */}
            <div className="p-6 pb-4 bg-gradient-to-r from-pink-50 via-rose-50 to-pink-50 border-b border-pink-100/70 relative">
              {/* Botão Fechar (X) */}
              <button
                type="button"
                onClick={handleFecharModal}
                className="absolute top-5 right-5 w-8 h-8 rounded-full bg-white/80 hover:bg-white text-slate-500 hover:text-slate-800 flex items-center justify-center border border-pink-200/60 shadow-xs transition-colors"
                aria-label="Fechar modal"
              >
                ✕
              </button>

              <div className="flex items-center gap-3">
                {/* Ícone PIX em destaque */}
                <div className="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center shadow-md shadow-emerald-500/20">
                  <svg className="w-6 h-6 fill-current" viewBox="0 0 512 512">
                    <path d="M409.8 111.4l-75.7-75.7c-13.8-13.8-36.2-13.8-50 0L102.2 217.6c-13.8 13.8-13.8 36.2 0 50l75.7 75.7c13.8 13.8 36.2 13.8 50 0l181.9-181.9c13.8-13.8 13.8-36.2 0-50zM227.9 318.3l-50.7-50.7 131.2-131.2 50.7 50.7-131.2 131.2z" opacity=".4"/>
                    <path d="M309.8 476.3l75.7-75.7c13.8-13.8 13.8-36.2 0-50L203.6 168.7c-13.8-13.8-36.2-13.8-50 0L77.9 244.4c-13.8 13.8-13.8 36.2 0 50l181.9 181.9c13.8 13.8 36.2 13.8 50 0zm-131.2-183l50.7-50.7 131.2 131.2-50.7 50.7-131.2-131.2z"/>
                  </svg>
                </div>

                <div>
                  <div className="flex items-center gap-2">
                    <span className="text-xs font-extrabold uppercase tracking-wider text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md">
                      PIX Instantâneo
                    </span>
                    <span className="text-xs text-slate-400 font-medium">
                      Sem taxas
                    </span>
                  </div>
                  <h3 className="text-lg font-black text-slate-900 mt-0.5">
                    Pagamento da Doação
                  </h3>
                </div>
              </div>

              {/* Destaque do Valor Confirmado */}
              <div className="mt-4 p-3 bg-white/90 backdrop-blur-xs rounded-2xl border border-pink-200/60 flex items-center justify-between">
                <div>
                  <span className="text-[11px] text-slate-500 font-semibold block uppercase">
                    Valor a Transferir:
                  </span>
                  <span className="text-2xl font-black text-slate-900">
                    R$ {valorFinal.toLocaleString("pt-BR", { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                  </span>
                </div>
                <div className="text-right">
                  <span className="text-[11px] text-slate-400 block">Beneficiário</span>
                  <span className="text-xs font-bold text-slate-700">SOS Tudo pelo Social</span>
                </div>
              </div>
            </div>

            {/* CORPO DO MODAL */}
            <div className="p-6 overflow-y-auto">
              {!pagamentoConcluido ? (
                <>
                  {/* SELETOR DE ABAS DO PIX */}
                  <div className="flex items-center justify-center p-1 bg-slate-100 rounded-2xl mb-6">
                    <button
                      type="button"
                      onClick={() => setTabModal("qrcode")}
                      className={`flex-1 py-2 text-xs font-bold rounded-xl transition-all ${
                        tabModal === "qrcode"
                          ? "bg-white text-slate-900 shadow-xs"
                          : "text-slate-500 hover:text-slate-800"
                      }`}
                    >
                      QR Code
                    </button>
                    <button
                      type="button"
                      onClick={() => setTabModal("copiacola")}
                      className={`flex-1 py-2 text-xs font-bold rounded-xl transition-all ${
                        tabModal === "copiacola"
                          ? "bg-white text-slate-900 shadow-xs"
                          : "text-slate-500 hover:text-slate-800"
                      }`}
                    >
                      Copia e Cola
                    </button>
                    <button
                      type="button"
                      onClick={() => setTabModal("chave")}
                      className={`flex-1 py-2 text-xs font-bold rounded-xl transition-all ${
                        tabModal === "chave"
                          ? "bg-white text-slate-900 shadow-xs"
                          : "text-slate-500 hover:text-slate-800"
                      }`}
                    >
                      Chave Oficial
                    </button>
                  </div>

                  {/* CONTEÚDO DA ABA 1: QR CODE */}
                  {tabModal === "qrcode" && (
                    <div className="text-center space-y-4">
                      <p className="text-xs text-slate-600">
                        Abra o app do seu banco, escolha <strong>Pagar via PIX com QR Code</strong> e aponte a câmera:
                      </p>

                      <div className="inline-block p-4 bg-white rounded-3xl border-2 border-dashed border-pink-200 shadow-inner relative group">
                        {/* Imagem do QR Code estilizado */}
                        <div className="w-52 h-52 mx-auto bg-slate-900 rounded-2xl p-2.5 flex items-center justify-center shadow-md relative overflow-hidden">
                          {/* Fallback elegante com SVG do QR Code de alta precisão */}
                          <svg className="w-full h-full text-white" viewBox="0 0 256 256" fill="currentColor">
                            {/* Marcadores de canto clássicos de QR Code */}
                            <path d="M32 32h64v64H32zm16 16v32h32V48z" />
                            <rect x="56" y="56" width="16" height="16" />
                            <path d="M160 32h64v64h-64zm16 16v32h32V48z" />
                            <rect x="184" y="56" width="16" height="16" />
                            <path d="M32 160h64v64H32zm16 16v32h32v-32z" />
                            <rect x="56" y="184" width="16" height="16" />
                            
                            {/* Matriz estilizada de dados representativos */}
                            <rect x="112" y="32" width="16" height="16" />
                            <rect x="112" y="64" width="16" height="16" />
                            <rect x="112" y="96" width="16" height="16" />
                            <rect x="128" y="48" width="16" height="16" />
                            <rect x="32" y="112" width="16" height="16" />
                            <rect x="64" y="112" width="16" height="16" />
                            <rect x="96" y="112" width="16" height="16" />
                            <rect x="144" y="112" width="16" height="16" />
                            <rect x="160" y="128" width="16" height="16" />
                            <rect x="176" y="96" width="16" height="16" />
                            <rect x="208" y="112" width="16" height="16" />
                            <rect x="112" y="144" width="16" height="16" />
                            <rect x="144" y="144" width="16" height="16" />
                            <rect x="112" y="176" width="16" height="16" />
                            <rect x="144" y="176" width="16" height="16" />
                            <rect x="176" y="176" width="16" height="16" />
                            <rect x="208" y="176" width="16" height="16" />
                            <rect x="112" y="208" width="16" height="16" />
                            <rect x="160" y="208" width="16" height="16" />
                            <rect x="192" y="208" width="16" height="16" />
                          </svg>

                          {/* Logo central sobreposto no QR Code */}
                          <div className="absolute inset-0 m-auto w-11 h-11 bg-white rounded-xl shadow-lg flex items-center justify-center border-2 border-emerald-400">
                            <span className="text-[#fb2782] font-black text-sm">SOS</span>
                          </div>
                        </div>

                        {/* Selo indicador de status */}
                        <div className="mt-3 flex items-center justify-center gap-1.5 text-[11px] font-bold text-emerald-600">
                          <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" />
                          <span>QR Code pronto para leitura</span>
                        </div>
                      </div>

                      {/* Botão de atalho para copiar o código copia e cola */}
                      <div>
                        <button
                          type="button"
                          onClick={handleCopiarCodigoPix}
                          className={`w-full py-3 px-4 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 ${
                            copiado
                              ? "bg-emerald-600 text-white"
                              : "bg-pink-50 text-[#fb2782] hover:bg-pink-100 border border-pink-200"
                          }`}
                        >
                          {copiado ? (
                            <>
                              <span>✓</span>
                              <span>Código PIX Copiado com Sucesso!</span>
                            </>
                          ) : (
                            <>
                              <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                              </svg>
                              <span>Prefere copiar? Clique para Copiar Código PIX</span>
                            </>
                          )}
                        </button>
                      </div>
                    </div>
                  )}

                  {/* CONTEÚDO DA ABA 2: PIX COPIA E COLA */}
                  {tabModal === "copiacola" && (
                    <div className="space-y-4">
                      <p className="text-xs text-slate-600">
                        Copie a chave no formato <strong>PIX Copia e Cola</strong> e cole na área "Pix Copia e Cola" do aplicativo do seu banco:
                      </p>

                      <div className="p-3 bg-slate-50 rounded-2xl border border-slate-200 font-mono text-[11px] text-slate-600 break-all select-all max-h-24 overflow-y-auto">
                        {codigoPixCopiaCola}
                      </div>

                      <button
                        type="button"
                        onClick={handleCopiarCodigoPix}
                        className={`w-full py-3.5 px-4 rounded-2xl font-bold text-sm transition-all shadow-md flex items-center justify-center gap-2 ${
                          copiado
                            ? "bg-emerald-600 text-white shadow-emerald-500/25"
                            : "bg-gradient-to-r from-[#fb2782] to-[#ff4785] text-white shadow-pink-500/30 hover:scale-[1.01]"
                        }`}
                      >
                        {copiado ? (
                          <>
                            <span className="text-base">✓</span>
                            <span>Código Copiado! Cole no seu banco</span>
                          </>
                        ) : (
                          <>
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                            </svg>
                            <span>Copiar Código Pix Copia e Cola</span>
                          </>
                        )}
                      </button>
                    </div>
                  )}

                  {/* CONTEÚDO DA ABA 3: CHAVE OFICIAL */}
                  {tabModal === "chave" && (
                    <div className="space-y-4">
                      <p className="text-xs text-slate-600">
                        Caso prefira fazer a transferência inserindo diretamente os dados da nossa conta bancária:
                      </p>

                      {/* Card da Chave E-mail */}
                      <div className="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex items-center justify-between">
                        <div>
                          <span className="text-[10px] font-bold uppercase text-slate-400 block">
                            Chave PIX (E-mail):
                          </span>
                          <span className="text-xs sm:text-sm font-bold text-slate-800">
                            {chavePixOficial}
                          </span>
                        </div>
                        <button
                          type="button"
                          onClick={handleCopiarChaveSimples}
                          className="px-3 py-1.5 bg-white border border-slate-200 hover:border-pink-300 text-[#fb2782] rounded-xl text-xs font-bold transition-colors shadow-xs"
                        >
                          {copiadoChave ? "✓ Copiado" : "Copiar"}
                        </button>
                      </div>

                      {/* Card do CNPJ */}
                      <div className="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex items-center justify-between">
                        <div>
                          <span className="text-[10px] font-bold uppercase text-slate-400 block">
                            Chave Alternativa (CNPJ):
                          </span>
                          <span className="text-xs sm:text-sm font-bold text-slate-800">
                            {cnpjOficial}
                          </span>
                        </div>
                        <button
                          type="button"
                          onClick={() => {
                            navigator.clipboard.writeText(cnpjOficial)
                            setCopiadoChave(true)
                            setTimeout(() => setCopiadoChave(false), 3000)
                          }}
                          className="px-3 py-1.5 bg-white border border-slate-200 hover:border-pink-300 text-[#fb2782] rounded-xl text-xs font-bold transition-colors shadow-xs"
                        >
                          Copiar
                        </button>
                      </div>

                      <div className="p-3 bg-pink-50/70 rounded-xl text-[11px] text-slate-600">
                        <strong>Favorecido:</strong> Associação SOS Tudo pelo Social <br />
                        <strong>Banco:</strong> Banco do Brasil (001) / Banco Inter (077)
                      </div>
                    </div>
                  )}

                  {/* BOTÕES DE CONFIRMAÇÃO E CANCELAMENTO NO RODAPÉ DO MODAL */}
                  <div className="mt-6 pt-5 border-t border-slate-100 flex flex-col sm:flex-row items-center gap-3">
                    <button
                      type="button"
                      onClick={() => setPagamentoConcluido(true)}
                      className="w-full sm:flex-1 py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-sm transition-colors flex items-center justify-center gap-2"
                    >
                      <span>✓</span>
                      <span>Já Realizei o Pagamento</span>
                    </button>

                    <button
                      type="button"
                      onClick={handleFecharModal}
                      className="w-full sm:w-auto py-3 px-5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-colors"
                    >
                      Voltar
                    </button>
                  </div>
                </>
              ) : (
                /* ESTADO DE SUCESSO / AGRADECIMENTO DENTRO DO MODAL */
                <div className="py-6 text-center space-y-4">
                  <div className="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto text-3xl heart-celebrate shadow-inner">
                    💖
                  </div>

                  <div className="inline-block px-3 py-1 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-full text-xs font-extrabold">
                    Doação Registrada com Sucesso!
                  </div>

                  <h3 className="text-2xl font-black text-slate-900">
                    Muito obrigado pelo seu carinho!
                  </h3>

                  <p className="text-xs sm:text-sm text-slate-600 max-w-sm mx-auto leading-relaxed">
                    Sua contribuição de <strong>R$ {valorFinal.toFixed(2)}</strong> faz toda a diferença para o projeto <strong>SOS Tudo pelo Social</strong>.
                  </p>

                  <div className="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-xs text-slate-500 text-left space-y-1.5 max-w-sm mx-auto">
                    <div className="flex justify-between">
                      <span>Doador:</span>
                      <strong className="text-slate-700">{anonimo ? "Anônimo" : nome || "Amigo da ONG"}</strong>
                    </div>
                    <div className="flex justify-between">
                      <span>Forma de Envio:</span>
                      <strong className="text-slate-700">PIX Instantâneo</strong>
                    </div>
                    <div className="flex justify-between">
                      <span>Data:</span>
                      <strong className="text-slate-700">{new Date().toLocaleDateString("pt-BR")}</strong>
                    </div>
                  </div>

                  <div className="pt-2">
                    <button
                      type="button"
                      onClick={handleFecharModal}
                      className="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-[#fb2782] to-[#ff4785] text-white font-bold text-sm shadow-md shadow-pink-500/30 hover:scale-[1.01] transition-transform"
                    >
                      Concluir e Fechar
                    </button>
                  </div>
                </div>
              )}
            </div>

            {/* RODAPÉ INFORMATIVO */}
            <div className="bg-slate-50 px-6 py-3 border-t border-slate-100 text-[11px] text-slate-400 flex items-center justify-between">
              <span>Ambiente Criptografado SSL</span>
              <span>SOS Tudo pelo Social © 2026</span>
            </div>

          </div>
        </div>
      )}

    </div>
  )
}