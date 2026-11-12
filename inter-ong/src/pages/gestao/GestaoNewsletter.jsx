import { useState } from 'react'
import { backend } from '../../api/backend'
import AvisoApi from '../../components/AvisoApi'
import { formatarDataHora } from '../../utils/gestao'
import useLista from './useLista'
import useAtraso from './useAtraso'
import CampoBusca from './CampoBusca'
import Paginacao from './Paginacao'

// Pessoas que se inscreveram em "Receba novidades da ONG": ver, baixar a planilha e remover quem pedir para sair.
export default function GestaoNewsletter() {
  const [busca, setBusca] = useState('')
  const [acao, setAcao] = useState({ erro: null, mensagem: '', ocupado: false })
  const buscaAtrasada = useAtraso(busca)
  const lista = useLista((pagina, options) => backend.gestaoNewsletter(pagina, options, buscaAtrasada), buscaAtrasada)

  async function remover(inscrito) {
    if (acao.ocupado) return
    if (!window.confirm(`Remover ${inscrito.email} da lista? Isso apaga o e-mail e o nome desta pessoa e não pode ser desfeito.`)) return
    setAcao({ erro: null, mensagem: '', ocupado: true })
    try {
      await backend.removerInscrito(inscrito.id)
      setAcao({ erro: null, mensagem: 'Inscrito removido da lista.', ocupado: false })
      lista.recarregar()
    } catch (erro) {
      setAcao({ erro, mensagem: '', ocupado: false })
    }
  }

  return (
    <section>
      <h2 className="text-2xl font-bold">Inscritos nas novidades</h2>
      <p className="mt-2 rounded-xl bg-yellow-50 p-4 text-yellow-900">
        Estas pessoas se inscreveram em “Receba novidades da ONG” e aceitaram a política de privacidade. O site <strong>não envia</strong> as novidades:
        baixe a planilha e use a lista no programa de e-mail da ONG. Quem pedir para sair da lista deve ser removido aqui.
      </p>
      <a href={backend.newsletterExportarUrl()} download
        className="mt-4 inline-block rounded-lg bg-pink-700 px-4 py-2 text-sm font-semibold text-white hover:bg-pink-800">Baixar planilha (CSV)</a>
      <CampoBusca valor={busca} onMudar={setBusca} rotulo="Buscar por nome ou e-mail" />
      <AvisoApi erro={acao.erro || lista.erro} mensagem={acao.mensagem} />
      {lista.carregando && <p role="status" className="mt-4">Carregando…</p>}
      {!lista.carregando && !lista.erro && lista.itens.length === 0 && (
        <p className="mt-4">{buscaAtrasada.trim() ? 'Nenhum inscrito encontrado para essa busca.' : 'Ninguém se inscreveu ainda.'}</p>
      )}
      <ul className="mt-4 grid gap-4 md:grid-cols-2">
        {lista.itens.map((i) => (
          <li key={i.id} className="rounded-2xl border border-[var(--border-soft)] bg-white p-5 shadow-sm">
            <p className="break-all text-lg font-bold text-slate-900">{i.nome || 'Sem nome informado'}</p>
            <dl className="mt-2 space-y-1 text-sm">
              <div><dt className="inline font-semibold">E-mail: </dt><dd className="inline break-all">{i.email}</dd></div>
              <div><dt className="inline font-semibold">Inscrição: </dt><dd className="inline">{formatarDataHora(i.data_inscricao)}</dd></div>
            </dl>
            <div className="mt-4 flex">
              <button disabled={acao.ocupado} onClick={() => remover(i)}
                className="ml-auto px-1 py-2 text-xs text-slate-600 underline hover:text-red-800 disabled:opacity-50">Remover da lista</button>
            </div>
          </li>
        ))}
      </ul>
      <Paginacao pagina={lista.pagina} ultima={lista.ultima} setPagina={lista.setPagina} />
    </section>
  )
}
