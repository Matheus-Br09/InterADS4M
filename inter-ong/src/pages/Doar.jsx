import { useEffect, useRef, useState } from 'react'
import { Link } from 'react-router-dom'
import { backend } from '../api/backend'
import AvisoApi from '../components/AvisoApi'
import heroChildImg from '../assets/hero-child.jpg'
import './css/Doar.css'

export default function Doar() {
  const [modo, setModo] = useState('unica')
  const [valor, setValor] = useState('50')
  const [criancas, setCriancas] = useState([])
  const [crianca, setCrianca] = useState('')
  const [dia, setDia] = useState('10')
  const [erro, setErro] = useState(null)
  const [erroLista, setErroLista] = useState(null)
  const [mensagem, setMensagem] = useState('')
  const [carregando, setCarregando] = useState(true)
  const [enviando, setEnviando] = useState(false)
  const [tentativa, setTentativa] = useState(0)
  const ocupado = useRef(false)
  useEffect(() => {
    const controller = new AbortController()
    backend.criancas({ signal: controller.signal }).then(({ dados }) => {
      if (!controller.signal.aborted) { setCriancas(dados); setErroLista(null) }
    }).catch(error => { if (!controller.signal.aborted) setErroLista(error) })
      .finally(() => { if (!controller.signal.aborted) setCarregando(false) })
    return () => controller.abort()
  }, [tentativa])
  async function enviar(event) {
    event.preventDefault()
    if (ocupado.current) return
    ocupado.current = true; setEnviando(true); setErro(null); setMensagem('')
    try {
      const resultado = modo === 'unica'
        ? await backend.doacao({ valor, metodo_pagamento: 'pix' })
        : modo === 'mensal'
          ? await backend.mensalidade({ valor_mensal: valor, dia_vencimento: Number(dia), metodo_pagamento: 'pix' })
          : await backend.apadrinhamento({ crianca_id: Number(crianca), valor_mensal: valor })
      setMensagem(`${resultado.message} Protocolo: ${resultado.dados.id}. Status: pendente.`)
    } catch (error) { setErro(error) }
    finally { ocupado.current = false; setEnviando(false) }
  }
  const campo = 'mt-2 w-full rounded-xl border border-slate-300 bg-white p-3 text-slate-800'
  return <main className="min-h-screen bg-pink-50/40 px-4 py-12 dark:bg-slate-950">
    <div className="mx-auto max-w-6xl">
      <header className="mb-10 text-center"><span className="font-bold text-pink-600">♥ Juntos transformamos vidas</span><h1 className="my-4 text-4xl font-bold">Faça parte dessa história</h1><p>Sua contribuição ajuda a manter as ações da SOS Tudo pelo Social.</p></header>
      <div className="grid gap-8 lg:grid-cols-2">
        <section><img src={heroChildImg} alt="Ações de apoio da ONG" className="w-full rounded-3xl" /><div className="mt-6 rounded-2xl bg-white p-6 text-slate-800"><h2 className="text-xl font-bold">Como funciona?</h2><p className="mt-3">Você registra uma intenção de apoio vinculada à sua conta. Nenhum pagamento é realizado nesta página. Cobrança automática, Pix e confirmação de pagamento ainda não estão disponíveis.</p><Link to="/login" className="mt-4 inline-block text-pink-700 underline">Entrar / Minha conta</Link></div></section>
        <section className="rounded-3xl bg-white p-6 text-slate-800 shadow-sm sm:p-8">
          <h2 className="mb-6 text-2xl font-bold">Quero apoiar</h2>
          <AvisoApi erro={erro} mensagem={mensagem} />
          <form onSubmit={enviar}><fieldset disabled={enviando} className="space-y-5">
            <label className="block">Tipo de apoio<select className={campo} value={modo} onChange={e => { setModo(e.target.value); setMensagem(''); setErro(null) }}><option value="unica">Doação única</option><option value="mensal">Doação mensal</option><option value="apadrinhamento">Apadrinhamento</option></select></label>
            <div className="flex flex-wrap gap-2">{[25, 50, 100, 200].map(v => <button type="button" key={v} aria-pressed={Number(valor) === v} onClick={() => setValor(String(v))} className={`rounded-xl border px-4 py-3 ${Number(valor) === v ? 'bg-pink-600 text-white' : 'bg-pink-50'}`}>R$ {v}</button>)}</div>
            <label className="block">{modo === 'unica' ? 'Valor (R$)' : 'Valor mensal (R$)'}<input className={campo} type="number" min="5" max="99999999.99" step="0.01" required value={valor} onChange={e => setValor(e.target.value)} /></label>
            {modo === 'mensal' && <label className="block">Dia de vencimento desejado<input className={campo} type="number" min="1" max="31" required value={dia} onChange={e => setDia(e.target.value)} /></label>}
            {modo === 'apadrinhamento' && <div><AvisoApi erro={erroLista} />{carregando ? <p role="status">Carregando crianças…</p> : <>
              {erroLista ? <button type="button" className="underline" onClick={() => { setCarregando(true); setTentativa(t => t + 1) }}>Tentar novamente</button> : <label className="block">Criança<select className={campo} required value={crianca} onChange={e => setCrianca(e.target.value)}><option value="">Selecione</option>{criancas.filter(c => c.id && !c.apadrinhada && c.status === 'disponivel').map(c => <option key={c.id} value={c.id}>{c.nome}{c.idade != null ? ` · ${c.idade} anos` : ''}</option>)}</select></label>}
              {!erroLista && !criancas.some(c => c.id && !c.apadrinhada && c.status === 'disponivel') && <p>Nenhuma criança disponível para apadrinhamento no momento.</p>}
            </>}</div>}
            <p className="text-sm">A intenção fica pendente de acompanhamento pela ONG. O registro não confirma doação nem apadrinhamento ativo.</p>
            <button type="submit" disabled={modo === 'apadrinhamento' && (!crianca || carregando || Boolean(erroLista))} className="w-full rounded-xl bg-pink-600 p-4 font-bold text-white disabled:opacity-50">{enviando ? 'Registrando…' : 'Registrar intenção de apoio ♥'}</button>
          </fieldset></form>
        </section>
      </div>
    </div>
  </main>
}
