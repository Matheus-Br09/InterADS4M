import { useCallback, useEffect, useRef, useState } from 'react'
import { NavLink, Outlet, useNavigate } from 'react-router-dom'
import { backend } from '../../api/backend'
import logoImg from '../../assets/logo.png'
import BotaoTema from './BotaoTema'
import ResumoPainel from './ResumoPainel'

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
  const [versaoResumo, setVersaoResumo] = useState(0)
  const atualizarResumo = useCallback(() => setVersaoResumo((v) => v + 1), [])

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
      <main className="min-h-screen bg-[var(--bg-page)] px-4 py-12 text-slate-900">
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
  if (!gestor) return <main className="min-h-screen bg-[var(--bg-page)] p-8 text-slate-900"><p role="status">Carregando…</p></main>

  return (
    <div className="min-h-screen bg-[var(--bg-page)] text-slate-900">
      <header className="border-t-4 border-t-[#fb2782] border-b border-b-[var(--border-soft)] bg-white">
        <div className="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 py-3">
          <div className="flex items-center gap-3">
            <img src={logoImg} alt="SOS Tudo pelo Social" className="h-10 w-auto" />
            <h1 className="text-lg font-bold">Painel da ONG</h1>
          </div>
          <div className="flex flex-wrap items-center gap-3 text-sm">
            <span>{gestor.nome_completo}</span>
            <BotaoTema />
            <button onClick={sair} className="rounded-full border border-slate-300 px-3 py-1 font-semibold hover:bg-slate-100">Sair</button>
          </div>
        </div>
      </header>
      <ResumoPainel versao={versaoResumo} />
      <div className="mt-4 border-y border-[var(--border-soft)] bg-white">
        <nav aria-label="Seções do painel" className="mx-auto flex max-w-6xl gap-2 overflow-x-auto px-4">
          {ABAS.map(([caminho, rotulo]) => (
            <NavLink key={caminho} to={caminho} className={({ isActive }) => `border-b-4 px-4 py-3 font-semibold whitespace-nowrap ${isActive ? 'border-[#fb2782] text-pink-700' : 'border-transparent hover:text-pink-700'}`}>{rotulo}</NavLink>
          ))}
        </nav>
      </div>
      <main className="mx-auto max-w-6xl px-4 py-8"><Outlet context={{ gestor, atualizarResumo }} /></main>
    </div>
  )
}
