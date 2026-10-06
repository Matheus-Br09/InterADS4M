import { useRef, useState } from 'react'
import { backend } from '../api/backend'
import AvisoApi from './AvisoApi'

export default function Newsletter() {
  const [erro, setErro] = useState(null)
  const [mensagem, setMensagem] = useState('')
  const [enviando, setEnviando] = useState(false)
  const ocupado = useRef(false)
  async function enviar(event) {
    event.preventDefault()
    if (ocupado.current) return
    const form = event.currentTarget
    const dados = Object.fromEntries(new FormData(form))
    ocupado.current = true; setEnviando(true); setErro(null); setMensagem('')
    try { const resposta = await backend.newsletter(dados); setMensagem(resposta.mensagem); form.reset() }
    catch (error) { setErro(error) }
    finally { ocupado.current = false; setEnviando(false) }
  }
  return <section className="bg-pink-50 p-8 text-slate-800" aria-label="Newsletter"><div className="mx-auto max-w-3xl"><h2 className="text-xl font-bold">Receba novidades da ONG</h2><AvisoApi erro={erro} mensagem={mensagem} /><form onSubmit={enviar} className="mt-4"><fieldset disabled={enviando} className="flex flex-wrap gap-3"><label>Nome (opcional)<input name="nome" maxLength={100} autoComplete="name" className="block rounded-lg border bg-white p-2" /></label><label>E-mail<input name="email" type="email" required autoComplete="email" className="block rounded-lg border bg-white p-2" /></label><button className="self-end rounded-lg bg-pink-600 px-4 py-2 text-white" type="submit">{enviando ? 'Enviando…' : 'Inscrever-se'}</button></fieldset></form></div></section>
}
