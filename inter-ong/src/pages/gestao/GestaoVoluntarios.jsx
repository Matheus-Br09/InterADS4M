import { useState } from 'react'
import { useOutletContext } from 'react-router-dom'
import { backend } from '../../api/backend'
import AvisoApi from '../../components/AvisoApi'
import { STATUS_VOLUNTARIO, calcularIdade, textoOuReserva } from '../../utils/gestao'
import useLista from './useLista'
import useAtraso from './useAtraso'
import CampoBusca from './CampoBusca'
import Paginacao from './Paginacao'

export default function GestaoVoluntarios() {
  const [acao, setAcao] = useState({ erro: null, mensagem: '', ocupado: false })
  const [busca, setBusca] = useState('')
  const buscaAtrasada = useAtraso(busca)
  const { atualizarResumo } = useOutletContext()
  const lista = useLista((pagina, options) => backend.gestaoVoluntarios(pagina, options, buscaAtrasada), buscaAtrasada)

  async function executar(chamada, texto, confirmacao) {
    if (acao.ocupado || (confirmacao && !window.confirm(confirmacao))) return
    setAcao({ erro: null, mensagem: '', ocupado: true })
    try {
      await chamada()
      setAcao({ erro: null, mensagem: texto, ocupado: false })
      lista.recarregar()
      atualizarResumo()
    } catch (erro) {
      setAcao({ erro, mensagem: '', ocupado: false })
    }
  }

  return (
    <section>
      <h2 className="text-2xl font-bold">Voluntários</h2>
      <CampoBusca valor={busca} onMudar={setBusca} rotulo="Buscar por nome ou e-mail" />
      <AvisoApi erro={acao.erro || lista.erro} mensagem={acao.mensagem} />
      {lista.carregando && <p role="status" className="mt-4">Carregando…</p>}
      {!lista.carregando && !lista.erro && lista.itens.length === 0 && <p className="mt-4">{buscaAtrasada.trim() ? 'Nenhum voluntário encontrado para essa busca.' : 'Nenhum voluntário cadastrado.'}</p>}
      <ul className="mt-4 grid gap-4 md:grid-cols-2">
        {lista.itens.map((v) => {
          // Cadastros antigos guardam o nome no apoiador ligado.
          const nome = textoOuReserva(v.nome_completo || v.apoiador?.nome_completo, '(sem nome)')
          const idade = calcularIdade(v.data_nascimento)
          return (
          <li key={v.id} className="rounded-2xl border border-[var(--border-soft)] bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <p className="text-xl font-bold break-words text-slate-900">{nome}</p>
            <dl className="mt-2 space-y-1 text-sm">
              <div><dt className="inline font-semibold">E-mail: </dt><dd className="inline break-all">{textoOuReserva(v.email, '—')}</dd></div>
              <div><dt className="inline font-semibold">Celular: </dt><dd className="inline">{textoOuReserva(v.celular, '—')}</dd></div>
              {idade !== null && <div><dt className="inline font-semibold">Idade: </dt><dd className="inline">{idade} anos</dd></div>}
              <div><dt className="inline font-semibold">Área: </dt><dd className="inline">{textoOuReserva(v.area_atuacao, '—')}</dd></div>
              <div><dt className="inline font-semibold">Disponibilidade: </dt><dd className="inline">{textoOuReserva(v.disponibilidade, '—')}</dd></div>
            </dl>
            <label className="mt-3 block text-sm font-semibold">Situação
              <select value={v.status} disabled={acao.ocupado} className="mt-1 block w-full rounded-lg border border-slate-300 bg-white p-2"
                onChange={(e) => executar(() => backend.atualizarVoluntario(v.id, { status: e.target.value }), 'Situação atualizada.')}>
                {Object.entries(STATUS_VOLUNTARIO).map(([valor, nome]) => <option key={valor} value={valor}>{nome}</option>)}
              </select>
            </label>
            <div className="mt-4 flex flex-wrap gap-2">
              {v.arquivo_curriculo
                ? <a href={backend.curriculoUrl(v.id)} className="rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold">Baixar currículo</a>
                : <span className="px-1 py-2 text-sm text-slate-600">Sem currículo</span>}
              <button disabled={acao.ocupado} className="ml-auto px-1 py-2 text-xs text-slate-600 underline hover:text-red-800 disabled:opacity-50"
                onClick={() => executar(() => backend.excluirVoluntario(v.id), 'Voluntário excluído.', `Excluir ${nome}? Isso apaga os dados e o currículo, sem volta.`)}>Excluir</button>
            </div>
          </li>
          )
        })}
      </ul>
      <Paginacao pagina={lista.pagina} ultima={lista.ultima} setPagina={lista.setPagina} />
    </section>
  )
}
