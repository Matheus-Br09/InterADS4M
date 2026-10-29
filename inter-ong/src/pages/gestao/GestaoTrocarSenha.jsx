import { useRef, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { backend } from '../../api/backend'

const campo = 'mt-1 w-full rounded-xl border border-slate-300 bg-white p-3 text-slate-900 focus-visible:outline-2 focus-visible:outline-pink-600'

// Obrigatória no primeiro acesso: troca a senha entregue pela ONG por uma só sua.
export default function GestaoTrocarSenha() {
  const navigate = useNavigate()
  const [erro, setErro] = useState('')
  const [campos, setCampos] = useState({})
  const [enviando, setEnviando] = useState(false)
  const ocupado = useRef(false)

  async function trocar(event) {
    event.preventDefault()
    if (ocupado.current) return
    const dados = new FormData(event.currentTarget)
    const senha = dados.get('senha')
    if (senha !== dados.get('senha_confirmation')) {
      setCampos({}); setErro('As duas senhas novas não são iguais.'); return
    }
    ocupado.current = true; setEnviando(true); setErro(''); setCampos({})
    try {
      await backend.trocarSenha({ senha_atual: dados.get('senha_atual'), senha, senha_confirmation: dados.get('senha_confirmation') })
      navigate('/gestao', { replace: true })
    } catch (error) {
      if (error.status === 401) { navigate('/gestao/entrar', { replace: true }); return }
      setErro(error.message); setCampos(error.errors || {})
    } finally { ocupado.current = false; setEnviando(false) }
  }

  return (
    <main className="flex min-h-screen items-center justify-center bg-[var(--bg-page)] px-4 py-12 text-slate-900">
      <form onSubmit={trocar} className="w-full max-w-sm rounded-2xl bg-white p-8 shadow-lg">
        <h1 className="text-2xl font-bold">Crie sua senha</h1>
        <p className="mt-2 text-sm text-slate-600">Por segurança, troque a senha que você recebeu por uma só sua. Use pelo menos 8 caracteres, com letra maiúscula, minúscula e número.</p>
        <fieldset disabled={enviando} className="mt-6 space-y-4">
          <label className="block text-sm font-semibold">Senha atual (a que você recebeu)
            <input name="senha_atual" type="password" required autoComplete="current-password" className={campo} />
          </label>
          <label className="block text-sm font-semibold">Nova senha
            <input name="senha" type="password" required minLength={8} autoComplete="new-password" className={campo} />
            {campos.senha && <span className="mt-1 block text-sm font-normal text-red-800">{campos.senha[0]}</span>}
          </label>
          <label className="block text-sm font-semibold">Repita a nova senha
            <input name="senha_confirmation" type="password" required minLength={8} autoComplete="new-password" className={campo} />
          </label>
          {erro && <p role="alert" className="rounded-lg bg-red-50 p-3 text-sm text-red-800">{erro}</p>}
          <button type="submit" className="w-full rounded-xl bg-[#fb2782] px-6 py-3 font-bold text-white hover:bg-pink-700 disabled:opacity-60">
            {enviando ? 'Salvando…' : 'Salvar nova senha'}
          </button>
        </fieldset>
      </form>
    </main>
  )
}
