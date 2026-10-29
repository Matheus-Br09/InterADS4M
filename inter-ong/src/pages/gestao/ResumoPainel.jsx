import { useEffect, useState } from 'react'
import { backend } from '../../api/backend'
import { formatarReais } from '../../utils/gestao'

// Três números no topo do painel. `versao` muda quando uma ação do painel
// (confirmar doação, mudar situação de voluntário) pede para recontar.
export default function ResumoPainel({ versao }) {
  const [resumo, setResumo] = useState(null)
  const [falhou, setFalhou] = useState(false)

  useEffect(() => {
    const controller = new AbortController()
    backend.gestaoResumo({ signal: controller.signal })
      .then(({ dados }) => { if (!controller.signal.aborted) { setFalhou(false); setResumo(dados) } })
      .catch(() => { if (!controller.signal.aborted) setFalhou(true) })
    return () => controller.abort()
  }, [versao])

  if (falhou) return <p className="mx-auto max-w-6xl px-4 pt-4 text-sm text-slate-600">Não foi possível carregar o resumo agora. As listas abaixo continuam funcionando.</p>

  // Cores dos números: amarelo, azul e rosa do cartaz da ONG (versões escuras no tema claro, para ler bem)
  const itens = [
    ['Doações para confirmar', resumo?.doacoes_pendentes, 'text-amber-700 dark:text-[#ffc928]'],
    ['Voluntários em análise', resumo?.voluntarios_em_analise, 'text-sky-700 dark:text-[#3bb4ee]'],
    ['Confirmadas, feitas neste mês', resumo ? formatarReais(resumo.recebido_no_mes) : undefined, 'text-pink-700 dark:text-[#ff66a3]'],
  ]

  return (
    <section aria-label="Resumo" className="mx-auto grid max-w-6xl grid-cols-3 gap-2 px-4 pt-4 sm:gap-3">
      {itens.map(([nome, valor, cor]) => (
        <div key={nome} className="min-w-0 rounded-2xl border border-[var(--border-soft)] bg-white p-3 shadow-sm sm:p-4">
          <p className={`font-extrabold whitespace-nowrap sm:text-3xl ${String(valor ?? '').length > 9 ? 'text-sm' : 'text-base'} ${cor}`}>{valor ?? '…'}</p>
          <p className="text-xs text-slate-600 sm:text-sm">{nome}</p>
        </div>
      ))}
    </section>
  )
}
