import { useEffect, useRef, useState } from 'react'
import { NavLink, Outlet, useNavigate } from 'react-router-dom'
import { backend } from '../../api/backend'

const ABAS = [
  ['doacoes', 'Doações'],
  ['voluntarios', 'Voluntários'],
  ['conteudos', 'Conteúdos'],
  ['gestores', 'Gestores'],
]

// Moldura do painel: confere quem está logado e mostra as abas.
export default function GestaoLayout() {
  const navigate = useNavigate()
  const [gestor, setGestor] = useState(null)
  const [erro, setErro] = useState('')
  const [tentativa, setTentativa] = useState(0)
  const saindo = useRef(false)

  useEffect(() => {
    const controller = new AbortController()
    backend.conta({ signal: controller.signal })
      .then(({ dados }) => { if (!controller.signal.aborted) { setErro(''); setGestor(dados) } })
      .catch((error) => {
        if (controller.signal.aborted) return
        if (error.status === 401) navigate('/gestao/entrar', { replace: true })
        else if (error.codigo === 'troca_senha_obrigatoria') navigate('/gestao/trocar-senha', { replace: true })
        else setErro(error.message)
      })
    return () => controller.abort()
  }, [tentativa, navigate])

  async function sair() {
    if (saindo.current) return
    saindo.current = true
    try { await backend.sair() } catch { /* sessão já expirada: segue para a entrada */ }
    saindo.current = false
    navigate('/gestao/entrar', { replace: true })
  }

  if (erro) {
    return (
      <main className="min-h-screen bg-pink-50 px-4 py-12 text-slate-900">
        <div className="mx-auto max-w-md rounded-2xl bg-white p-8 shadow-lg">
          <p role="alert" className="text-red-800">{erro}</p>
          <div className="mt-4 flex gap-4">
            <button onClick={() => setTentativa((t) => t + 1)} className="font-semibold underline">Tentar novamente</button>
            <button onClick={sair} className="font-semibold underline">Sair</button>
          </div>
        </div>
      </main>
    )
  }
  if (!gestor) return <main className="min-h-screen bg-pink-50 p-8 text-slate-900"><p role="status">Carregando…</p></main>

  return (
    <div className="min-h-screen bg-pink-50/50 text-slate-900">
      <header className="bg-white shadow-sm">
        <div className="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-4">
          <h1 className="text-lg font-bold">Painel da ONG</h1>
          <p className="flex items-center gap-4 text-sm">
            <span>{gestor.nome_completo}</span>
            <button onClick={sair} className="rounded-lg border border-slate-300 px-3 py-1 font-semibold hover:bg-slate-100">Sair</button>
          </p>
        </div>
        <nav aria-label="Seções do painel" className="mx-auto flex max-w-6xl gap-2 overflow-x-auto px-4">
          {ABAS.map(([caminho, rotulo]) => (
            <NavLink key={caminho} to={caminho} className={({ isActive }) => `border-b-4 px-4 py-2 font-semibold whitespace-nowrap ${isActive ? 'border-[#fb2782] text-pink-700' : 'border-transparent hover:text-pink-700'}`}>{rotulo}</NavLink>
          ))}
        </nav>
      </header>
      <main className="mx-auto max-w-6xl px-4 py-8"><Outlet context={{ gestor }} /></main>
    </div>
  )
}
