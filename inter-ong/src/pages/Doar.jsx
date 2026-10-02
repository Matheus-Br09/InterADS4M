import { useState, useEffect } from "react"
import { Link } from "react-router-dom"
import "./css/Doar.css"
import heroChildImg from "../assets/hero-child.jpg"
import projectEduImg from "../assets/project-edu.jpg"
import projectSupportImg from "../assets/project-support.jpg"
import projectCommunityImg from "../assets/project-community.jpg"

export default function Doar() {
  // Modo de Navegação Principal: 'doacao' (Doação Direta) ou 'apadrinhamento' (Apadrinhar Criança)
  const [modoPagina, setModoPagina] = useState("doacao")

  // Estados da Doação Geral
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

  // Estados do Modal PIX (Doação Geral)
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [copiado, setCopiado] = useState(false)
  const [copiadoChave, setCopiadoChave] = useState(false)
  const [tabModal, setTabModal] = useState("qrcode") // 'qrcode', 'copiacola', 'chave'
  const [pagamentoConcluido, setPagamentoConcluido] = useState(false)

  // Estados do Apadrinhamento
  const [criancaSelecionada, setCriancaSelecionada] = useState(null)
  const [isModalApadrinharOpen, setIsModalApadrinharOpen] = useState(false)
  const [nomePadrinho, setNomePadrinho] = useState("")
  const [emailPadrinho, setEmailPadrinho] = useState("")
  const [whatsPadrinho, setWhatsPadrinho] = useState("")
  const [cartaPadrinho, setCartaPadrinho] = useState("")
  const [apadrinhamentoConcluido, setApadrinhamentoConcluido] = useState(false)
  const [copiadoApadrinhar, setCopiadoApadrinhar] = useState(false)

  // Lista de crianças assistidas pelo instituto para apadrinhamento
  const criancasInstituto = [
    {
      id: "lucas-8",
      nome: "Lucas",
      idade: "8 anos",
      foto: heroChildImg,
      sonho: "Sonha em ser engenheiro e ama desenhar robôs no papel.",
      interesse: "Robótica e Matemática",
      valorMensal: 90,
      corBadge: "bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300",
      cobertura: "Aulas de reforço + Kit escolar completo + Alimentação diária",
    },
    {
      id: "sophia-6",
      nome: "Sophia",
      idade: "6 anos",
      foto: projectEduImg,
      sonho: "Quer ser veterinária e ama cuidar dos animais e ler histórinhas.",
      interesse: "Leitura e Animais",
      valorMensal: 80,
      corBadge: "bg-[#fff0f5] text-[#fb2782] dark:bg-pink-950/70 dark:text-pink-300",
      cobertura: "Livros didáticos + Uniforme de oficinas + Suporte nutricional",
    },
    {
      id: "mateus-10",
      nome: "Mateus",
      idade: "10 anos",
      foto: projectSupportImg,
      sonho: "Apaixonado por futebol, sonha em ser atleta e aprender violão.",
      interesse: "Esportes e Música",
      valorMensal: 100,
      corBadge: "bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300",
      cobertura: "Escolinha de esportes + Aulas de música + Cesta nutricional",
    },
    {
      id: "beatriz-7",
      nome: "Beatriz",
      idade: "7 anos",
      foto: projectCommunityImg,
      sonho: "Pinta quadros com tinta guache e sonha em conhecer museus.",
      interesse: "Artes e Pintura",
      valorMensal: 85,
      corBadge: "bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300",
      cobertura: "Material de artes + Passeios culturais + Suporte psicológico",
    },
  ]

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
      pergunta: "Como funciona o Apadrinhamento de uma criança?",
      resposta:
        "Ao apadrinhar, você assume uma contribuição mensal (ex: R$ 80 a R$ 100) direcionada ao desenvolvimento integral daquela criança. Você recebe relatórios periódicos com o progresso escolar, fotos e pode inclusive enviar cartas e mensagens de incentivo para seu afilhado!",
    },
    {
      pergunta: "Como o pagamento via PIX é confirmado?",
      resposta:
        "O PIX é instantâneo. Assim que o pagamento for realizado no seu aplicativo bancário, nossa equipe recebe a notificação em tempo real e o recibo/certificado é gerado imediatamente.",
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
  ]

  // Calcula o valor final da doação direta
  const valorFinal = isCustom ? Number(valorCustomizado) || 0 : valorSelecionado

  // Chave PIX e Payload Copia e Cola
  const chavePixOficial = "pix@sostudopelosocial.org.br"
  const cnpjOficial = "42.123.456/0001-89"
  
  const codigoPixCopiaCola = `00020126580014BR.GOV.BCB.PIX0136pix@sostudopelosocial.org.br520400005303986540${valorFinal.toFixed(
    2
  )}5802BR5925SOS TUDO PELO SOCIAL6009SAO PAULO62070503***6304${Math.floor(
    1000 + Math.random() * 9000
  )}`

  // Bloqueia rolagem do body quando qualquer modal estiver aberto
  useEffect(() => {
    const handleKeyDown = (e) => {
      if (e.key === "Escape") {
        if (isModalOpen) handleFecharModal()
        if (isModalApadrinharOpen) handleFecharModalApadrinhar()
      }
    }

    if (isModalOpen || isModalApadrinharOpen) {
      document.body.style.overflow = "hidden"
      window.addEventListener("keydown", handleKeyDown)
    } else {
      document.body.style.overflow = "unset"
    }

    return () => {
      document.body.style.overflow = "unset"
      window.removeEventListener("keydown", handleKeyDown)
    }
  }, [isModalOpen, isModalApadrinharOpen])

  // Abre o modal de PIX para Doação Geral
  const handleAbrirModalPix = (e) => {
    e.preventDefault()
    if (valorFinal <= 0) {
      alert("Por favor, selecione ou digite um valor válido para doação.")
      return
    }
    setPagamentoConcluido(false)
    setIsModalOpen(true)
  }

  // Fecha o modal de Doação Geral
  const handleFecharModal = () => {
    setIsModalOpen(false)
    setCopiado(false)
    setCopiadoChave(false)
    setPagamentoConcluido(false)
  }

  // Abre o modal de Apadrinhamento
  const handleAbrirApadrinhar = (crianca) => {
    setCriancaSelecionada(crianca)
    setApadrinhamentoConcluido(false)
    setIsModalApadrinharOpen(true)
  }

  // Fecha o modal de Apadrinhamento
  const handleFecharModalApadrinhar = () => {
    setIsModalApadrinharOpen(false)
    setCopiadoApadrinhar(false)
    setApadrinhamentoConcluido(false)
  }

  // Copia código da Doação Geral
  const handleCopiarCodigoPix = () => {
    navigator.clipboard.writeText(codigoPixCopiaCola)
    setCopiado(true)
    setTimeout(() => setCopiado(false), 3000)
  }

  // Copia código do Apadrinhamento
  const handleCopiarCodigoApadrinhar = () => {
    const codigoApadrinhar = `00020126580014BR.GOV.BCB.PIX0136pix@sostudopelosocial.org.br520400005303986540${(criancaSelecionada?.valorMensal || 90).toFixed(2)}5802BR5925APADRINHAR ${criancaSelecionada?.nome.toUpperCase()}6009SAO PAULO6304${Math.floor(1000 + Math.random() * 9000)}`
    navigator.clipboard.writeText(codigoApadrinhar)
    setCopiadoApadrinhar(true)
    setTimeout(() => setCopiadoApadrinhar(false), 3000)
  }

  return (
    <div className="min-h-screen bg-gradient-to-b from-[#fff5f8] via-white to-[#fff8fa] dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 text-slate-800 dark:text-slate-100 transition-colors duration-300 pb-20">
      
      {/* HERO / CABEÇALHO */}
      <section className="relative overflow-hidden pt-10 pb-12 sm:pt-14 sm:pb-16 border-b border-pink-100/70 dark:border-slate-800">
        <div className="absolute top-0 left-1/2 -translate-x-1/2 w-[700px] h-[320px] bg-gradient-to-br from-pink-200/40 via-rose-100/30 to-transparent dark:from-pink-950/20 dark:via-purple-950/10 blur-3xl pointer-events-none -z-10" />

        <div className="max-w-5xl mx-auto px-4 sm:px-6 text-center">
          <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-pink-50 dark:bg-slate-800 border border-pink-200 dark:border-pink-900/50 text-[#fb2782] dark:text-pink-400 text-xs sm:text-sm font-semibold mb-5 shadow-xs float-badge">
            <span className="flex h-2 w-2 rounded-full bg-[#fb2782] animate-ping" />
            <span>Transforme Vidas & Apadrinhe Crianças</span>
            <span className="text-pink-400">♥</span>
          </div>

          <h1 className="text-3xl sm:text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
            Sua doação acolhe, inclui e{" "}
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-[#fb2782] to-[#ff6398]">
              transforma futuras gerações
            </span>
          </h1>

          <p className="mt-4 text-base sm:text-lg text-slate-600 dark:text-slate-300 max-w-2xl mx-auto leading-relaxed">
            Escolha entre fazer uma <strong>doação financeira direta</strong> ou <strong>apadrinhar uma criança</strong> do instituto com acompanhamento e relatórios mensais.
          </p>

          {/* SELETOR DE MODO DA PÁGINA (DOAÇÃO vs APADRINHAMENTO) */}
          <div className="mt-8 flex justify-center">
            <div className="inline-flex p-1.5 bg-slate-200/80 dark:bg-slate-800 rounded-2xl border border-slate-300/70 dark:border-slate-700 shadow-inner max-w-md w-full">
              <button
                type="button"
                onClick={() => setModoPagina("doacao")}
                className={`flex-1 py-3 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center justify-center gap-2 ${
                  modoPagina === "doacao"
                    ? "bg-white dark:bg-slate-900 text-[#fb2782] dark:text-pink-400 shadow-md border border-pink-200/60 dark:border-pink-900/50"
                    : "text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white"
                }`}
              >
                <span>💳</span>
                <span>Doação Direta PIX</span>
              </button>

              <button
                type="button"
                onClick={() => setModoPagina("apadrinhamento")}
                className={`flex-1 py-3 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center justify-center gap-2 relative ${
                  modoPagina === "apadrinhamento"
                    ? "bg-white dark:bg-slate-900 text-[#fb2782] dark:text-pink-400 shadow-md border border-pink-200/60 dark:border-pink-900/50"
                    : "text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white"
                }`}
              >
                <span>👶</span>
                <span>Apadrinhar Criança</span>
                <span className="hidden sm:inline-block text-[10px] uppercase font-black px-1.5 py-0.5 rounded bg-pink-100 dark:bg-pink-950 text-[#fb2782] dark:text-pink-300">
                  Novo
                </span>
              </button>
            </div>
          </div>
        </div>
      </section>

      {/* CONTEÚDO PRINCIPAL (DOAÇÃO DIRETA OU APADRINHAMENTO) */}
      <div className="max-w-6xl mx-auto px-4 sm:px-6 pt-8">
        
        {/* ========================================================================= */}
        {/* MODO 1: DOAÇÃO DIRETA PIX */}
        {/* ========================================================================= */}
        {modoPagina === "doacao" && (
          <form onSubmit={handleAbrirModalPix} className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {/* COLUNA ESQUERDA */}
            <div className="lg:col-span-8 space-y-6">

              {/* CARD 1: FREQUÊNCIA */}
              <div className="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 shadow-sm border border-pink-100/80 dark:border-slate-800">
                <div className="flex items-center justify-between flex-wrap gap-3 mb-5">
                  <div className="flex items-center gap-2.5">
                    <span className="flex items-center justify-center w-7 h-7 rounded-full bg-pink-100 dark:bg-pink-950 text-[#fb2782] dark:text-pink-400 font-bold text-xs">
                      1
                    </span>
                    <h2 className="text-lg font-bold text-slate-800 dark:text-white">
                      Frequência da Doação
                    </h2>
                  </div>
                  <span className="text-xs text-slate-400 font-medium">
                    Sem fidelidade
                  </span>
                </div>

                <div className="grid grid-cols-2 gap-3 p-1.5 bg-slate-100/70 dark:bg-slate-950 rounded-2xl border border-slate-200/60 dark:border-slate-800">
                  <button
                    type="button"
                    onClick={() => setFrequencia("unica")}
                    className={`py-3 px-4 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2 ${
                      frequencia === "unica"
                        ? "bg-white dark:bg-slate-900 text-[#fb2782] dark:text-pink-400 shadow-sm border border-pink-200/60 dark:border-pink-900/50"
                        : "text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white"
                    }`}
                  >
                    <span>⚡</span> Doação Única
                  </button>

                  <button
                    type="button"
                    onClick={() => setFrequencia("mensal")}
                    className={`py-3 px-4 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-2 ${
                      frequencia === "mensal"
                        ? "bg-white dark:bg-slate-900 text-[#fb2782] dark:text-pink-400 shadow-sm border border-pink-200/60 dark:border-pink-900/50"
                        : "text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white"
                    }`}
                  >
                    <span>🔄</span> Mensal Recorrente
                  </button>
                </div>
              </div>

              {/* CARD 2: ESCOLHA O VALOR */}
              <div className="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 shadow-sm border border-pink-100/80 dark:border-slate-800">
                <div className="flex items-center gap-2.5 mb-5">
                  <span className="flex items-center justify-center w-7 h-7 rounded-full bg-pink-100 dark:bg-pink-950 text-[#fb2782] dark:text-pink-400 font-bold text-xs">
                    2
                  </span>
                  <div>
                    <h2 className="text-lg font-bold text-slate-800 dark:text-white">
                      Escolha o Valor
                    </h2>
                    <p className="text-xs text-slate-500 dark:text-slate-400">
                      Selecione um valor pré-definido ou digite o valor que desejar
                    </p>
                  </div>
                </div>

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
                        className={`relative cursor-pointer rounded-2xl p-5 border-2 transition-all text-left flex flex-col justify-between ${
                          isSelected
                            ? "border-[#fb2782] bg-pink-50/40 dark:bg-pink-950/30 shadow-md shadow-pink-500/10 scale-[1.01]"
                            : "border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900/60 hover:border-pink-300"
                        }`}
                      >
                        {item.tag && (
                          <span className="absolute -top-3 right-4 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-gradient-to-r from-[#fb2782] to-[#ff4785] text-white shadow-xs">
                            {item.tag}
                          </span>
                        )}

                        <div className="flex items-center justify-between mb-2">
                          <span className="text-2xl font-black text-slate-900 dark:text-white">
                            {item.titulo}
                          </span>
                          <div
                            className={`w-5 h-5 rounded-full border-2 flex items-center justify-center ${
                              isSelected
                                ? "border-[#fb2782] bg-[#fb2782]"
                                : "border-slate-300 dark:border-slate-700"
                            }`}
                          >
                            {isSelected && (
                              <span className="w-2 h-2 rounded-full bg-white" />
                            )}
                          </div>
                        </div>

                        <p className="text-xs text-slate-600 dark:text-slate-300 leading-snug">
                          {item.impacto}
                        </p>
                      </div>
                    )
                  })}
                </div>

                {/* Valor customizado */}
                <div className="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                  <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
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
                      className={`w-full pl-12 pr-4 py-3 rounded-2xl border-2 font-bold text-base transition-all focus:outline-none dark:text-white ${
                        isCustom
                          ? "border-[#fb2782] bg-white dark:bg-slate-900"
                          : "border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950"
                      }`}
                    />
                  </div>
                </div>
              </div>

              {/* CARD 3: CAUSA */}
              <div className="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 shadow-sm border border-pink-100/80 dark:border-slate-800">
                <div className="flex items-center gap-2.5 mb-4">
                  <span className="flex items-center justify-center w-7 h-7 rounded-full bg-pink-100 dark:bg-pink-950 text-[#fb2782] dark:text-pink-400 font-bold text-xs">
                    3
                  </span>
                  <h2 className="text-lg font-bold text-slate-800 dark:text-white">
                    Destino da sua doação
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
                          ? "border-[#fb2782] bg-pink-50/50 dark:bg-pink-950/40 text-[#fb2782] dark:text-pink-300"
                          : "border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:border-pink-200"
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

              {/* CARD 4: SEUS DADOS */}
              <div className="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 shadow-sm border border-pink-100/80 dark:border-slate-800">
                <div className="flex items-center justify-between flex-wrap gap-2 mb-4">
                  <div className="flex items-center gap-2.5">
                    <span className="flex items-center justify-center w-7 h-7 rounded-full bg-pink-100 dark:bg-pink-950 text-[#fb2782] dark:text-pink-400 font-bold text-xs">
                      4
                    </span>
                    <h2 className="text-lg font-bold text-slate-800 dark:text-white">
                      Seus Dados (Opcional)
                    </h2>
                  </div>

                  <label className="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-[#fb2782]">
                    <input
                      type="checkbox"
                      checked={anonimo}
                      onChange={(e) => setAnonimo(e.target.checked)}
                      className="w-4 h-4 rounded border-slate-300 text-[#fb2782] focus:ring-pink-400"
                    />
                    <span>Doar anonimamente</span>
                  </label>
                </div>

                {!anonimo ? (
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                      <label className="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5">
                        Nome Completo
                      </label>
                      <input
                        type="text"
                        placeholder="Ex: Maria Oliveira"
                        value={nome}
                        onChange={(e) => setNome(e.target.value)}
                        className="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-white text-sm outline-none focus:border-[#fb2782]"
                      />
                    </div>

                    <div>
                      <label className="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1.5">
                        E-mail
                      </label>
                      <input
                        type="email"
                        placeholder="Ex: maria@email.com"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                        className="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-white text-sm outline-none focus:border-[#fb2782]"
                      />
                    </div>
                  </div>
                ) : (
                  <div className="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 text-xs text-slate-600 dark:text-slate-400 flex items-center gap-3">
                    <span>🕵️</span>
                    <span>Sua doação será registrada anonimamente.</span>
                  </div>
                )}
              </div>

            </div>

            {/* COLUNA DIREITA (RESUMO) */}
            <div className="lg:col-span-4 sticky top-28 space-y-5">
              <div className="bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-md shadow-pink-500/5 border-2 border-pink-200/80 dark:border-slate-800">
                <div className="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                  <span className="text-xs font-bold uppercase tracking-wider text-slate-400">
                    Resumo da Doação
                  </span>
                  <span className="px-2.5 py-1 rounded-full text-[11px] font-bold bg-pink-100 dark:bg-pink-950 text-[#fb2782] dark:text-pink-400">
                    {frequencia === "unica" ? "Única" : "Mensal"}
                  </span>
                </div>

                <div className="py-5 space-y-3">
                  <div className="flex items-baseline justify-between">
                    <span className="text-slate-600 dark:text-slate-400 text-sm">Valor:</span>
                    <span className="text-3xl font-black text-slate-900 dark:text-white">
                      R$ {valorFinal.toLocaleString("pt-BR", { minimumFractionDigits: 2 })}
                    </span>
                  </div>
                  <div className="flex items-center justify-between text-xs text-slate-600 dark:text-slate-400 pt-2 border-t border-slate-100 dark:border-slate-800">
                    <span>Método:</span>
                    <span className="font-bold text-emerald-600 dark:text-emerald-400">
                      PIX Instantâneo
                    </span>
                  </div>
                </div>

                <button
                  type="submit"
                  disabled={valorFinal <= 0}
                  className="w-full relative group overflow-hidden py-4 px-6 rounded-2xl bg-gradient-to-r from-[#fb2782] via-[#ff3b8d] to-[#ff6398] text-white font-black text-base shadow-lg shadow-pink-500/35 hover:scale-[1.02] active:scale-95 transition-all cursor-pointer"
                >
                  <div className="flex items-center justify-center gap-2.5">
                    <span>Doar com PIX Agora</span>
                    <span>→</span>
                  </div>
                </button>
              </div>
            </div>

          </form>
        )}

        {/* ========================================================================= */}
        {/* MODO 2: APADRINHAMENTO DE CRIANÇAS DO INSTITUTO */}
        {/* ========================================================================= */}
        {modoPagina === "apadrinhamento" && (
          <div className="space-y-8">
            <div className="bg-gradient-to-r from-pink-500 via-rose-500 to-pink-600 text-white rounded-3xl p-6 sm:p-8 shadow-lg shadow-pink-500/20 relative overflow-hidden">
              <div className="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none" />
              <div className="max-w-2xl">
                <span className="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold tracking-wider uppercase mb-3 inline-block">
                  Vínculo de Amor & Futuro
                </span>
                <h2 className="text-2xl sm:text-3xl font-black leading-tight">
                  Apadrinhar uma Criança do Instituto SOS
                </h2>
                <p className="mt-2 text-sm sm:text-base text-pink-100 leading-relaxed">
                  Ao se tornar padrinho ou madrinha, você ajuda diretamente nas despesas de educação, saúde, alimentação e desenvolvimento cultural do seu afilhado(a).
                </p>
              </div>
            </div>

            {/* GRID DAS CRIANÇAS */}
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              {criancasInstituto.map((c) => (
                <div
                  key={c.id}
                  className="bg-white dark:bg-slate-900 rounded-3xl border border-pink-100 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between"
                >
                  <div className="p-6">
                    <div className="flex items-start gap-4">
                      {/* Foto da criança */}
                      <img
                        src={c.foto}
                        alt={c.nome}
                        className="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover border-2 border-pink-200 dark:border-pink-900/60 shadow-md shrink-0"
                      />
                      <div>
                        <div className="flex items-center gap-2">
                          <h3 className="text-xl font-black text-slate-900 dark:text-white">
                            {c.nome}
                          </h3>
                          <span className="text-xs text-slate-500 font-semibold">
                            • {c.idade}
                          </span>
                        </div>
                        <span className={`inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold mt-1 ${c.corBadge}`}>
                          {c.interesse}
                        </span>
                        <p className="text-xs text-slate-600 dark:text-slate-300 mt-2.5 italic leading-relaxed">
                          "{c.sonho}"
                        </p>
                      </div>
                    </div>

                    <div className="mt-5 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-400 space-y-1.5">
                      <div className="font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                        <span>🌱 O que a bolsa mensal cobre:</span>
                      </div>
                      <p className="pl-4 text-slate-500 dark:text-slate-400 leading-snug">
                        {c.cobertura}
                      </p>
                    </div>
                  </div>

                  <div className="p-4 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <div>
                      <span className="text-[10px] text-slate-400 font-bold uppercase block">
                        Mensalidade do Afilhado
                      </span>
                      <span className="text-xl font-black text-slate-900 dark:text-white">
                        R$ {c.valorMensal},00
                        <span className="text-xs text-slate-400 font-normal">/mês</span>
                      </span>
                    </div>

                    <button
                      type="button"
                      onClick={() => handleAbrirApadrinhar(c)}
                      className="py-2.5 px-5 rounded-2xl bg-gradient-to-r from-[#fb2782] to-[#ff4785] text-white font-bold text-xs sm:text-sm shadow-md shadow-pink-500/25 hover:scale-105 active:scale-95 transition-all flex items-center gap-2 cursor-pointer"
                    >
                      <span>♥</span>
                      <span>Apadrinhar {c.nome}</span>
                    </button>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* ========================================================================= */}
        {/* SEÇÃO DE PERGUNTAS FREQUENTES (FAQ) */}
        {/* ========================================================================= */}
        <section className="mt-16 max-w-3xl mx-auto">
          <div className="text-center mb-8">
            <h3 className="text-2xl font-bold text-slate-900 dark:text-white">
              Perguntas Frequentes
            </h3>
            <p className="text-sm text-slate-500 dark:text-slate-400 mt-1">
              Tire suas dúvidas sobre Doação e Apadrinhamento
            </p>
          </div>

          <div className="space-y-3">
            {faqs.map((faq, index) => {
              const isOpen = faqAberto === index
              return (
                <div
                  key={index}
                  className="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 overflow-hidden transition-all shadow-xs"
                >
                  <button
                    type="button"
                    onClick={() => setFaqAberto(isOpen ? null : index)}
                    className="w-full p-5 text-left font-bold text-slate-800 dark:text-slate-100 text-sm flex items-center justify-between gap-4 hover:text-[#fb2782]"
                  >
                    <span>{faq.pergunta}</span>
                    <span className={`text-slate-400 transform transition-transform ${isOpen ? "rotate-180 text-[#fb2782]" : ""}`}>
                      ▼
                    </span>
                  </button>
                  {isOpen && (
                    <div className="px-5 pb-5 text-xs sm:text-sm text-slate-600 dark:text-slate-300 border-t border-slate-100 dark:border-slate-800 pt-3 leading-relaxed">
                      {faq.resposta}
                    </div>
                  )}
                </div>
              )
            })}
          </div>
        </section>

      </div>

      {/* ========================================================================= */}
      {/* MODAL 1: DOAÇÃO DIRETA PIX (COM FUNDO EMBAÇADO / BACKDROP-BLUR) */}
      {/* ========================================================================= */}
      {isModalOpen && (
        <div
          className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-950/70 backdrop-blur-md transition-all duration-300"
          onClick={handleFecharModal}
          role="dialog"
          aria-modal="true"
        >
          <div
            className="relative w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-pink-100 dark:border-slate-800 overflow-hidden modal-pop-in custom-modal-scroll max-h-[92vh] flex flex-col text-slate-800 dark:text-slate-100"
            onClick={(e) => e.stopPropagation()}
          >
            {/* CABEÇALHO */}
            <div className="p-6 pb-4 bg-gradient-to-r from-pink-50 via-rose-50 to-pink-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 border-b border-pink-100/70 dark:border-slate-800 relative">
              <button
                type="button"
                onClick={handleFecharModal}
                className="absolute top-5 right-5 w-8 h-8 rounded-full bg-white/80 dark:bg-slate-800 text-slate-500 dark:text-slate-300 flex items-center justify-center border border-pink-200/60 dark:border-slate-700 shadow-xs"
              >
                ✕
              </button>

              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center shadow-md">
                  <svg className="w-6 h-6 fill-current" viewBox="0 0 512 512">
                    <path d="M409.8 111.4l-75.7-75.7c-13.8-13.8-36.2-13.8-50 0L102.2 217.6c-13.8 13.8-13.8 36.2 0 50l75.7 75.7c13.8 13.8 36.2 13.8 50 0l181.9-181.9c13.8-13.8 13.8-36.2 0-50zM227.9 318.3l-50.7-50.7 131.2-131.2 50.7 50.7-131.2 131.2z" opacity=".4"/>
                    <path d="M309.8 476.3l75.7-75.7c13.8-13.8 13.8-36.2 0-50L203.6 168.7c-13.8-13.8-36.2-13.8-50 0L77.9 244.4c-13.8 13.8-13.8 36.2 0 50l181.9 181.9c13.8 13.8 36.2 13.8 50 0zm-131.2-183l50.7-50.7 131.2 131.2-50.7 50.7-131.2-131.2z"/>
                  </svg>
                </div>
                <div>
                  <span className="text-xs font-extrabold uppercase text-emerald-700 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-950 px-2 py-0.5 rounded-md">
                    PIX Instantâneo
                  </span>
                  <h3 className="text-lg font-black text-slate-900 dark:text-white mt-0.5">
                    Pagamento da Doação
                  </h3>
                </div>
              </div>

              <div className="mt-4 p-3 bg-white/90 dark:bg-slate-950/80 rounded-2xl border border-pink-200/60 dark:border-slate-800 flex items-center justify-between">
                <div>
                  <span className="text-[11px] text-slate-500 dark:text-slate-400 font-semibold block uppercase">
                    Valor a Transferir:
                  </span>
                  <span className="text-2xl font-black text-slate-900 dark:text-white">
                    R$ {valorFinal.toLocaleString("pt-BR", { minimumFractionDigits: 2 })}
                  </span>
                </div>
                <div className="text-right">
                  <span className="text-[11px] text-slate-400 block">Beneficiário</span>
                  <span className="text-xs font-bold text-slate-700 dark:text-slate-200">SOS Tudo pelo Social</span>
                </div>
              </div>
            </div>

            {/* CORPO DO MODAL */}
            <div className="p-6 overflow-y-auto">
              {!pagamentoConcluido ? (
                <>
                  <div className="flex items-center justify-center p-1 bg-slate-100 dark:bg-slate-950 rounded-2xl mb-6">
                    <button
                      type="button"
                      onClick={() => setTabModal("qrcode")}
                      className={`flex-1 py-2 text-xs font-bold rounded-xl transition-all ${
                        tabModal === "qrcode"
                          ? "bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs"
                          : "text-slate-500 hover:text-slate-800 dark:hover:text-white"
                      }`}
                    >
                      QR Code
                    </button>
                    <button
                      type="button"
                      onClick={() => setTabModal("copiacola")}
                      className={`flex-1 py-2 text-xs font-bold rounded-xl transition-all ${
                        tabModal === "copiacola"
                          ? "bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs"
                          : "text-slate-500 hover:text-slate-800 dark:hover:text-white"
                      }`}
                    >
                      Copia e Cola
                    </button>
                    <button
                      type="button"
                      onClick={() => setTabModal("chave")}
                      className={`flex-1 py-2 text-xs font-bold rounded-xl transition-all ${
                        tabModal === "chave"
                          ? "bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs"
                          : "text-slate-500 hover:text-slate-800 dark:hover:text-white"
                      }`}
                    >
                      Chave Oficial
                    </button>
                  </div>

                  {tabModal === "qrcode" && (
                    <div className="text-center space-y-4">
                      <p className="text-xs text-slate-600 dark:text-slate-300">
                        Aponte a câmera do aplicativo do seu banco para ler o QR Code:
                      </p>

                      <div className="inline-block p-4 bg-white dark:bg-slate-950 rounded-3xl border-2 border-dashed border-pink-200 dark:border-slate-700 shadow-inner">
                        <div className="w-52 h-52 mx-auto bg-slate-900 rounded-2xl p-2.5 flex items-center justify-center relative shadow-md">
                          <svg className="w-full h-full text-white" viewBox="0 0 256 256" fill="currentColor">
                            <path d="M32 32h64v64H32zm16 16v32h32V48z" />
                            <rect x="56" y="56" width="16" height="16" />
                            <path d="M160 32h64v64h-64zm16 16v32h32V48z" />
                            <rect x="184" y="56" width="16" height="16" />
                            <path d="M32 160h64v64H32zm16 16v32h32v-32z" />
                            <rect x="56" y="184" width="16" height="16" />
                            <rect x="112" y="32" width="16" height="16" />
                            <rect x="112" y="64" width="16" height="16" />
                            <rect x="112" y="96" width="16" height="16" />
                            <rect x="144" y="112" width="16" height="16" />
                            <rect x="176" y="176" width="16" height="16" />
                          </svg>
                          <div className="absolute inset-0 m-auto w-11 h-11 bg-white rounded-xl shadow-lg flex items-center justify-center border-2 border-emerald-400">
                            <span className="text-[#fb2782] font-black text-sm">SOS</span>
                          </div>
                        </div>
                      </div>

                      <button
                        type="button"
                        onClick={handleCopiarCodigoPix}
                        className={`w-full py-3 px-4 rounded-xl text-xs font-bold transition-all ${
                          copiado
                            ? "bg-emerald-600 text-white"
                            : "bg-pink-50 dark:bg-slate-800 text-[#fb2782] dark:text-pink-400 border border-pink-200 dark:border-slate-700"
                        }`}
                      >
                        {copiado ? "✓ Código Copiado!" : "Copiar Código PIX Copia e Cola"}
                      </button>
                    </div>
                  )}

                  {tabModal === "copiacola" && (
                    <div className="space-y-4">
                      <div className="p-3 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-200 dark:border-slate-800 font-mono text-[11px] text-slate-600 dark:text-slate-300 break-all select-all">
                        {codigoPixCopiaCola}
                      </div>

                      <button
                        type="button"
                        onClick={handleCopiarCodigoPix}
                        className="w-full py-3.5 px-4 rounded-2xl font-bold text-sm bg-gradient-to-r from-[#fb2782] to-[#ff4785] text-white shadow-md cursor-pointer"
                      >
                        {copiado ? "✓ Copiado com sucesso!" : "Copiar Código Pix Copia e Cola"}
                      </button>
                    </div>
                  )}

                  {tabModal === "chave" && (
                    <div className="space-y-3">
                      <div className="p-4 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <div>
                          <span className="text-[10px] font-bold text-slate-400 uppercase">Chave PIX E-mail</span>
                          <span className="text-xs font-bold block text-slate-800 dark:text-slate-200">{chavePixOficial}</span>
                        </div>
                        <button
                          type="button"
                          onClick={() => {
                            navigator.clipboard.writeText(chavePixOficial)
                            setCopiadoChave(true)
                            setTimeout(() => setCopiadoChave(false), 3000)
                          }}
                          className="px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-[#fb2782] dark:text-pink-400 rounded-xl text-xs font-bold"
                        >
                          {copiadoChave ? "✓ Copiado" : "Copiar"}
                        </button>
                      </div>
                    </div>
                  )}

                  <div className="mt-6 pt-5 border-t border-slate-100 dark:border-slate-800 flex items-center gap-3">
                    <button
                      type="button"
                      onClick={() => setPagamentoConcluido(true)}
                      className="flex-1 py-3 px-4 rounded-xl bg-emerald-600 text-white font-bold text-xs sm:text-sm cursor-pointer"
                    >
                      ✓ Já Realizei o Pagamento
                    </button>
                    <button
                      type="button"
                      onClick={handleFecharModal}
                      className="py-3 px-5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs font-bold"
                    >
                      Voltar
                    </button>
                  </div>
                </>
              ) : (
                <div className="py-6 text-center space-y-4">
                  <div className="w-16 h-16 bg-pink-100 dark:bg-pink-950 rounded-full flex items-center justify-center mx-auto text-3xl heart-celebrate">
                    💖
                  </div>
                  <h3 className="text-2xl font-black text-slate-900 dark:text-white">
                    Muito obrigado pelo seu carinho!
                  </h3>
                  <p className="text-xs sm:text-sm text-slate-600 dark:text-slate-300 max-w-sm mx-auto">
                    Sua doação faz toda a diferença para os atendidos da <strong>SOS Tudo pelo Social</strong>.
                  </p>
                  <button
                    type="button"
                    onClick={handleFecharModal}
                    className="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-[#fb2782] to-[#ff4785] text-white font-bold text-sm shadow-md"
                  >
                    Concluir
                  </button>
                </div>
              )}
            </div>
          </div>
        </div>
      )}

      {/* ========================================================================= */}
      {/* MODAL 2: APADRINHAMENTO DE CRIANÇA (COM FUNDO EMBAÇADO / BACKDROP-BLUR) */}
      {/* ========================================================================= */}
      {isModalApadrinharOpen && criancaSelecionada && (
        <div
          className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-950/70 backdrop-blur-md transition-all duration-300"
          onClick={handleFecharModalApadrinhar}
          role="dialog"
          aria-modal="true"
        >
          <div
            className="relative w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-pink-100 dark:border-slate-800 overflow-hidden modal-pop-in custom-modal-scroll max-h-[92vh] flex flex-col text-slate-800 dark:text-slate-100"
            onClick={(e) => e.stopPropagation()}
          >
            {/* CABEÇALHO DO APADRINHAMENTO */}
            <div className="p-6 pb-4 bg-gradient-to-r from-pink-500 via-rose-500 to-pink-600 text-white relative">
              <button
                type="button"
                onClick={handleFecharModalApadrinhar}
                className="absolute top-5 right-5 w-8 h-8 rounded-full bg-white/20 hover:bg-white/30 text-white flex items-center justify-center border border-white/30 shadow-xs"
              >
                ✕
              </button>

              <div className="flex items-center gap-4">
                <img
                  src={criancaSelecionada.foto}
                  alt={criancaSelecionada.nome}
                  className="w-16 h-16 rounded-2xl object-cover border-2 border-white shadow-md shrink-0"
                />
                <div>
                  <span className="text-[11px] font-bold uppercase tracking-wider bg-white/20 px-2 py-0.5 rounded-md">
                    Apadrinhamento de Criança
                  </span>
                  <h3 className="text-xl font-black mt-1">
                    Apadrinhar {criancaSelecionada.nome} ({criancaSelecionada.idade})
                  </h3>
                </div>
              </div>

              <div className="mt-4 p-3 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 flex items-center justify-between">
                <div>
                  <span className="text-[10px] text-pink-100 uppercase font-semibold block">
                    Mensalidade de Apadrinhamento:
                  </span>
                  <span className="text-2xl font-black text-white">
                    R$ {criancaSelecionada.valorMensal},00 /mês
                  </span>
                </div>
              </div>
            </div>

            {/* CORPO DO MODAL DE APADRINHAMENTO */}
            <div className="p-6 overflow-y-auto">
              {!apadrinhamentoConcluido ? (
                <form
                  onSubmit={(e) => {
                    e.preventDefault()
                    setApadrinhamentoConcluido(true)
                  }}
                  className="space-y-4"
                >
                  <p className="text-xs text-slate-600 dark:text-slate-300">
                    Preencha seus dados para receber o <strong>Certificado de Padrinho/Madrinha</strong> e enviar uma mensagem para seu afilhado(a):
                  </p>

                  <div>
                    <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                      Seu Nome Completo *
                    </label>
                    <input
                      type="text"
                      required
                      placeholder="Ex: Carlos Eduardo"
                      value={nomePadrinho}
                      onChange={(e) => setNomePadrinho(e.target.value)}
                      className="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-white text-sm outline-none focus:border-[#fb2782]"
                    />
                  </div>

                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                      <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        Seu E-mail *
                      </label>
                      <input
                        type="email"
                        required
                        placeholder="carlos@email.com"
                        value={emailPadrinho}
                        onChange={(e) => setEmailPadrinho(e.target.value)}
                        className="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-white text-sm outline-none focus:border-[#fb2782]"
                      />
                    </div>
                    <div>
                      <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        WhatsApp (para relatórios)
                      </label>
                      <input
                        type="tel"
                        placeholder="(00) 90000-0000"
                        value={whatsPadrinho}
                        onChange={(e) => setWhatsPadrinho(e.target.value)}
                        className="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-white text-sm outline-none focus:border-[#fb2782]"
                      />
                    </div>
                  </div>

                  <div>
                    <label className="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                      Carta ou Mensagem para {criancaSelecionada.nome}
                    </label>
                    <textarea
                      rows="3"
                      placeholder={`Escreva uma palavra de incentivo para ${criancaSelecionada.nome}...`}
                      value={cartaPadrinho}
                      onChange={(e) => setCartaPadrinho(e.target.value)}
                      className="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-800 dark:text-white text-sm outline-none focus:border-[#fb2782]"
                    />
                  </div>

                  {/* PIX DO APADRINHAMENTO */}
                  <div className="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-3">
                    <span className="text-xs font-bold text-slate-800 dark:text-slate-200 block">
                      Pagamento da Primeira Mensalidade via PIX:
                    </span>
                    <button
                      type="button"
                      onClick={handleCopiarCodigoApadrinhar}
                      className="w-full py-3 px-4 rounded-xl text-xs font-bold bg-pink-50 dark:bg-slate-800 text-[#fb2782] dark:text-pink-400 border border-pink-200 dark:border-slate-700 flex items-center justify-center gap-2 cursor-pointer"
                    >
                      {copiadoApadrinhar ? "✓ Código Copiado!" : `Copiar PIX Apadrinhamento (R$ ${criancaSelecionada.valorMensal},00)`}
                    </button>
                  </div>

                  <div className="pt-4 flex items-center gap-3">
                    <button
                      type="submit"
                      className="flex-1 py-3.5 px-4 rounded-2xl bg-gradient-to-r from-[#fb2782] to-[#ff4785] text-white font-bold text-sm shadow-md shadow-pink-500/30 hover:scale-[1.01] transition-transform cursor-pointer"
                    >
                      Confirmar Apadrinhamento ♥
                    </button>
                    <button
                      type="button"
                      onClick={handleFecharModalApadrinhar}
                      className="py-3.5 px-4 rounded-2xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 font-bold text-xs"
                    >
                      Cancelar
                    </button>
                  </div>
                </form>
              ) : (
                /* TELA DE SUCESSO DE APADRINHAMENTO */
                <div className="py-6 text-center space-y-4">
                  <div className="w-20 h-20 bg-pink-100 dark:bg-pink-950 rounded-full flex items-center justify-center mx-auto text-4xl heart-celebrate">
                    🌟
                  </div>
                  <span className="inline-block px-3 py-1 bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 font-extrabold rounded-full text-xs">
                    Certificado de Padrinho Gerado!
                  </span>
                  <h3 className="text-2xl font-black text-slate-900 dark:text-white">
                    Parabéns, {nomePadrinho || "Padrinho/Madrinha"}!
                  </h3>
                  <p className="text-xs sm:text-sm text-slate-600 dark:text-slate-300 max-w-sm mx-auto leading-relaxed">
                    Você agora é o Padrinho oficial de <strong>{criancaSelecionada.nome}</strong>. Sua mensagem foi registrada e será entregue com todo carinho!
                  </p>
                  <button
                    type="button"
                    onClick={handleFecharModalApadrinhar}
                    className="w-full py-3.5 px-6 rounded-2xl bg-gradient-to-r from-[#fb2782] to-[#ff4785] text-white font-bold text-sm shadow-md"
                  >
                    Concluir e Voltar
                  </button>
                </div>
              )}
            </div>
          </div>
        </div>
      )}

    </div>
  )
}