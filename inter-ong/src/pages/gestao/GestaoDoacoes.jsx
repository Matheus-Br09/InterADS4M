import { useState } from 'react'
import { backend } from '../../api/backend'
import AvisoApi from '../../components/AvisoApi'
import { STATUS_DOACAO, rotulo, formatarReais, formatarDataHora } from '../../utils/gestao'
import useLista from './useLista'
import Paginacao from './Paginacao'

const FILTROS = [['pendente', 'Pendentes'], ['concluido', 'Confirmadas'], ['cancelado', 'Canceladas'], ['expirado', 'Expiradas'], ['', 'Todas']]
const btn = 'rounded-lg px-3 py-2 text-sm font-semibold'

export default function GestaoDoacoes() {
  const [status, setStatus] = useState('pendente')
  const [acao, setAcao] = useState({ erro: null, mensagem: '', ocupado: false })
  const lista = useLista((pagina, options) => backend.gestaoDoacoes(status, pagina, options), status)

  async function executar(chamada, texto, confirmacao) {
    if (acao.ocupado || (confirmacao && !window.confirm(confirmacao))) return
    setAcao({ erro: null, mensagem: '', ocupado: true })
    try {
      await chamada()
      setAcao({ erro: null, mensagem: texto, ocupado: false })
      lista.recarregar()
    } catch (erro) {
      setAcao({ erro, mensagem: '', ocupado: false })
    }
  }

  return (
    <section>
      <h2 className="text-2xl font-bold">Doações</h2>
      <p className="mt-2 rounded-xl bg-yellow-50 p-4 text-yellow-900">Confira no extrato do banco o valor e o protocolo antes de confirmar.</p>
      <div className="mt-4 flex flex-wrap gap-2" role="group" aria-label="Filtrar por situação">
        {FILTROS.map(([valor, nome]) => (
          <button key={nome} aria-pressed={status === valor} onClick={() => { setStatus(valor); setAcao({ erro: null, mensagem: '', ocupado: false }) }}
            className={`${btn} border ${status === valor ? 'border-pink-700 bg-pink-700 text-white' : 'border-slate-300 bg-white'}`}>{nome}</button>
        ))}
      </div>
      <AvisoApi erro={acao.erro || lista.erro} mensagem={acao.mensagem} />
      {lista.carregando && <p role="status" className="mt-4">Carregando…</p>}
      {!lista.carregando && !lista.erro && lista.itens.length === 0 && <p className="mt-4">Nenhuma doação nesta situação.</p>}
      <ul className="mt-4 grid gap-4 md:grid-cols-2">
        {lista.itens.map((d) => {
          const podeAlterar = d.status === 'pendente' || d.status === 'expirado'
          return (
            <li key={d.id} className="rounded-2xl bg-white p-5 shadow">
              <div className="flex items-start justify-between gap-2">
                <p className="text-xl font-bold">{formatarReais(d.valor)}</p>
                <span className="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold">{rotulo(STATUS_DOACAO, d.status)}</span>
              </div>
              <dl className="mt-2 space-y-1 text-sm">
                <div><dt className="inline font-semibold">Protocolo: </dt><dd className="inline break-all">{d.protocolo}</dd></div>
                <div><dt className="inline font-semibold">Data: </dt><dd className="inline">{formatarDataHora(d.data_doacao)}</dd></div>
                <div><dt className="inline font-semibold">Doador: </dt><dd className="inline break-all">{d.nome_doador || 'Dados apagados'}{d.email_doador ? ` (${d.email_doador})` : ''}</dd></div>
              </dl>
              <div className="mt-4 flex flex-wrap gap-2">
                {podeAlterar && <>
                  <button disabled={acao.ocupado} className={`${btn} bg-green-700 text-white disabled:opacity-50`}
                    onClick={() => executar(() => backend.atualizarDoacao(d.id, 'concluido'), 'Pagamento confirmado.', `Confirmar o pagamento de ${formatarReais(d.valor)} (protocolo ${d.protocolo})?`)}>Confirmar pagamento</button>
                  <button disabled={acao.ocupado} className={`${btn} border border-slate-300 disabled:opacity-50`}
                    onClick={() => executar(() => backend.atualizarDoacao(d.id, 'cancelado'), 'Doação cancelada.', `Cancelar a doação de protocolo ${d.protocolo}?`)}>Cancelar</button>
                </>}
                {d.nome_doador && <button disabled={acao.ocupado} className={`${btn} border border-red-300 text-red-800 disabled:opacity-50`}
                  onClick={() => executar(() => backend.anonimizarDoacao(d.id), 'Dados do doador apagados.', 'Apagar o nome e o e-mail deste doador? O valor da doação continua registrado, mas os dados pessoais são apagados e não voltam.')}>Apagar dados do doador</button>}
              </div>
            </li>
          )
        })}
      </ul>
      <Paginacao pagina={lista.pagina} ultima={lista.ultima} setPagina={lista.setPagina} />
    </section>
  )
}
