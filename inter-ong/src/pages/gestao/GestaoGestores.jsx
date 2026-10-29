import { useState } from 'react'
import { useOutletContext } from 'react-router-dom'
import { backend } from '../../api/backend'
import AvisoApi from '../../components/AvisoApi'
import useLista from './useLista'

const carregar = (_pagina, options) => backend.gestaoGestores(options)

export default function GestaoGestores() {
  const { gestor: eu } = useOutletContext()
  const [acao, setAcao] = useState({ erro: null, mensagem: '', ocupado: false })
  const [novo, setNovo] = useState(null) // { nome, email, senha } — mostrado uma única vez
  const [copiado, setCopiado] = useState('')
  const [formKey, setFormKey] = useState(0)
  const lista = useLista(carregar)

  async function adicionar(evento) {
    evento.preventDefault()
    if (acao.ocupado) return
    const dados = new FormData(evento.currentTarget)
    setAcao({ erro: null, mensagem: '', ocupado: true })
    setNovo(null)
    setCopiado('')
    try {
      const resposta = await backend.criarGestor({ nome_completo: dados.get('nome_completo').trim(), email: dados.get('email').trim() })
      setNovo({ nome: resposta.dados?.nome_completo, email: resposta.dados?.email, senha: resposta.senha_temporaria })
      setAcao({ erro: null, mensagem: '', ocupado: false })
      setFormKey((k) => k + 1)
      lista.recarregar()
    } catch (erro) {
      setAcao({ erro, mensagem: '', ocupado: false })
    }
  }

  async function copiar() {
    try {
      await navigator.clipboard.writeText(novo.senha)
      setCopiado('Senha copiada.')
    } catch {
      setCopiado('Não foi possível copiar. Selecione a senha e copie manualmente.')
    }
  }

  async function desativar(g) {
    if (acao.ocupado || !window.confirm(`Desativar o acesso de ${g.nome_completo}? A pessoa perde o acesso ao painel.`)) return
    setAcao({ erro: null, mensagem: '', ocupado: true })
    try {
      await backend.desativarGestor(g.id)
      setAcao({ erro: null, mensagem: 'Acesso desativado.', ocupado: false })
      lista.recarregar()
    } catch (erro) {
      setAcao({ erro, mensagem: '', ocupado: false })
    }
  }

  return (
    <section>
      <h2 className="text-2xl font-bold">Gestores</h2>
      <div className="mt-6 grid gap-8 lg:grid-cols-2">
        <div>
          <form key={formKey} onSubmit={adicionar} className="space-y-4 rounded-2xl bg-white p-5 shadow">
            <h3 className="text-lg font-bold">Adicionar gestor</h3>
            <label className="block text-sm font-semibold">Nome completo<input name="nome_completo" required maxLength={150} className="mt-1 block w-full rounded-lg border border-slate-300 p-2 font-normal" /></label>
            <label className="block text-sm font-semibold">E-mail<input type="email" name="email" required className="mt-1 block w-full rounded-lg border border-slate-300 p-2 font-normal" /></label>
            <button disabled={acao.ocupado} className="rounded-lg bg-pink-700 px-5 py-2 font-semibold text-white disabled:opacity-50">{acao.ocupado ? 'Criando…' : 'Adicionar gestor'}</button>
          </form>
          {novo && (
            <div role="status" className="mt-4 rounded-2xl border-2 border-green-700 bg-green-50 p-5">
              <p className="font-semibold">Gestor criado: {novo.nome}</p>
              <p className="mt-2 text-sm">Senha temporária (aparece só agora):</p>
              <p className="mt-1 flex flex-wrap items-center gap-3">
                <code className="select-all break-all rounded bg-white px-3 py-2 text-lg font-bold">{novo.senha}</code>
                <button onClick={copiar} className="rounded-lg border border-slate-400 bg-white px-3 py-2 text-sm font-semibold">Copiar senha</button>
              </p>
              {copiado && <p className="mt-2 text-sm">{copiado}</p>}
              <p className="mt-3 text-sm">Envie por um canal privado; no primeiro acesso a pessoa vai criar a própria senha.</p>
              <button onClick={() => { setNovo(null); setCopiado('') }} className="mt-3 text-sm font-semibold underline">Já anotei, fechar</button>
            </div>
          )}
        </div>
        <div>
          <h3 className="text-lg font-bold">Quem tem acesso</h3>
          <AvisoApi erro={acao.erro || lista.erro} mensagem={acao.mensagem} />
          {lista.carregando && <p role="status">Carregando…</p>}
          <ul className="mt-2 space-y-3">
            {lista.itens.map((g) => (
              <li key={g.id} className="flex items-center justify-between gap-3 rounded-2xl bg-white p-4 shadow">
                <span className="min-w-0 break-words">
                  <span className="block font-semibold">{g.nome_completo}{g.id === eu.id ? ' (você)' : ''}</span>
                  <span className="block text-sm break-all">{g.email}</span>
                </span>
                {g.id !== eu.id && <button disabled={acao.ocupado} onClick={() => desativar(g)} className="shrink-0 rounded-lg border border-red-300 px-3 py-2 text-sm font-semibold text-red-800 disabled:opacity-50">Desativar</button>}
              </li>
            ))}
          </ul>
        </div>
      </div>
    </section>
  )
}
