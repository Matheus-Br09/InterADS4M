import { useRef, useState } from 'react'
import { useNavigate } from 'react-router-dom'
import { backend } from '../../api/backend'

const campo = 'mt-1 w-full rounded-xl border border-slate-300 bg-white p-3 text-slate-900 focus-visible:outline-2 focus-visible:outline-pink-600'

// Tela de entrada da gestão. Fica fora do site público (sem menu e rodapé).
export default function GestaoEntrar() {
  const navigate = useNavigate()
  const [erro, setErro] = useState('')
  const [enviando, setEnviando] = useState(false)
  const ocupado = useRef(false)

  async function entrar(event) {
    event.preventDefault()
    if (ocupado.current) return
    const dados = new FormData(event.currentTarget)
    ocupado.current = true; setEnviando(true); setErro('')
    try {
      await backend.entrar({ email: dados.get('email'), senha: dados.get('senha') })
      navigate('/gestao', { replace: true })
    } catch (error) {
      setErro(error.status === 429 && error.tentarEm
        ? `${error.message} Tente de novo em ${Math.ceil(error.tentarEm / 60)} minuto(s).`
        : error.message)
    } finally { ocupado.current = false; setEnviando(false) }
  }

  return (
    <main className="flex min-h-screen items-center justify-center bg-pink-50 px-4 py-12 text-slate-900">
      <form onSubmit={entrar} className="w-full max-w-sm rounded-2xl bg-white p-8 shadow-lg">
        <h1 className="text-2xl font-bold">Painel da ONG</h1>
        <p className="mt-2 text-sm text-slate-600">Área restrita à equipe de gestão.</p>
        <fieldset disabled={enviando} className="mt-6 space-y-4">
          <label className="block text-sm font-semibold">E-mail
            <input name="email" type="email" required autoComplete="username" className={campo} />
          </label>
          <label className="block text-sm font-semibold">Senha
            <input name="senha" type="password" required autoComplete="current-password" className={campo} />
          </label>
          {erro && <p role="alert" className="rounded-lg bg-red-50 p-3 text-sm text-red-800">{erro}</p>}
          <button type="submit" className="w-full rounded-xl bg-[#fb2782] px-6 py-3 font-bold text-white hover:bg-pink-700 disabled:opacity-60">
            {enviando ? 'Entrando…' : 'Entrar'}
          </button>
        </fieldset>
      </form>
    </main>
  )
}
