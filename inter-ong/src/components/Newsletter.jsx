import { useRef, useState } from 'react'
import { backend } from '../api/backend'
import { Link } from 'react-router-dom'
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
    try { const resposta = await backend.newsletter(dados); setMensagem(resposta.message || resposta.mensagem || 'Inscrição feita!'); form.reset() }
    catch (error) { setErro(error) }
    finally { ocupado.current = false; setEnviando(false) }
  }
  const campo = 'mt-1 block w-full rounded-xl border border-[var(--border)] bg-[var(--bg-input)] p-2.5 font-normal'
  return <section className="border-t border-[var(--border-soft)] bg-[var(--bg-subtle)] px-4 py-10 sm:px-8" aria-label="Newsletter"><div className="mx-auto max-w-3xl"><h2 className="text-2xl font-bold">Receba novidades da ONG</h2><AvisoApi erro={erro} mensagem={mensagem} /><form onSubmit={enviar} className="mt-4"><fieldset disabled={enviando} className="flex flex-wrap gap-4"><label className="min-w-0 flex-1 basis-48 text-sm font-semibold">Nome (opcional)<input name="nome" maxLength={100} autoComplete="name" className={campo} /></label><label className="min-w-0 flex-1 basis-48 text-sm font-semibold">E-mail<input name="email" type="email" required autoComplete="email" className={campo} /></label><label className="flex basis-full items-start gap-2 text-sm"><input type="checkbox" name="aceite_privacidade" value="1" required className="mt-1" /><span>Li e aceito a <Link to="/privacidade" className="underline">política de privacidade</Link></span></label><button className="self-end rounded-full bg-[#fb2782] px-6 py-2.5 font-bold text-white shadow-md transition hover:scale-105 disabled:opacity-60" type="submit">{enviando ? 'Enviando…' : 'Inscrever-se'}</button></fieldset></form></div></section>
}
