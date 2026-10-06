import { useEffect, useRef, useState } from 'react'
import { backend } from '../api/backend'
import AvisoApi from '../components/AvisoApi'

export default function Noticias({ recurso = 'noticias', titulo = 'Notícias e eventos' }) {
  const [pagina, setPagina] = useState(1)
  const [tentativa, setTentativa] = useState(0)
  const [resultado, setResultado] = useState(null)
  const [admin, setAdmin] = useState(false)
  const [erro, setErro] = useState(null)
  const [mensagem, setMensagem] = useState('')
  const [enviando, setEnviando] = useState(false)
  const ocupado = useRef(false)
  useEffect(() => {
    const controller = new AbortController()
    backend.conteudos(recurso, pagina, { signal: controller.signal })
      .then(resposta => { if (!controller.signal.aborted) setResultado({ recurso, pagina, tentativa, resposta }) })
      .catch(error => { if (!controller.signal.aborted) setResultado({ recurso, pagina, tentativa, erro: error }) })
    return () => controller.abort()
  }, [recurso, pagina, tentativa])
  useEffect(() => {
    const controller = new AbortController()
    backend.conta({ signal: controller.signal }).then(({ dados }) => {
      if (!controller.signal.aborted) setAdmin(dados.tipo_usuario === 'admin')
    }).catch(() => { if (!controller.signal.aborted) setAdmin(false) })
    return () => controller.abort()
  }, [])
  async function publicar(event) {
    event.preventDefault()
    if (ocupado.current) return
    const form = event.currentTarget
    const dados = new FormData(form)
    if (!dados.get('imagem')?.size) dados.set('imagem', '')
    ocupado.current = true; setEnviando(true); setErro(null); setMensagem('')
    try {
      await backend.publicarNoticia(dados)
      form.reset(); setMensagem('Notícia publicada.'); setPagina(1); setTentativa(t => t + 1)
    } catch (error) { setErro(error); if (error.status === 401 || error.status === 403) setAdmin(false) }
    finally { ocupado.current = false; setEnviando(false) }
  }
  async function excluir(id) {
    if (ocupado.current || !window.confirm('Excluir esta notícia publicada?')) return
    ocupado.current = true; setEnviando(true); setErro(null); setMensagem('')
    try { await backend.excluirNoticia(id); setTentativa(t => t + 1); setMensagem('Notícia excluída.') }
    catch (error) { setErro(error); if (error.status === 401 || error.status === 403) setAdmin(false) }
    finally { ocupado.current = false; setEnviando(false) }
  }
  const carregando = resultado?.recurso !== recurso || resultado?.pagina !== pagina || resultado?.tentativa !== tentativa
  const dados = !carregando ? resultado.resposta?.dados || [] : []
  const paginacao = resultado?.resposta?.paginacao
  const paginas = paginacao ? Math.max(1, Math.ceil(paginacao.total / paginacao.por_pagina)) : 1
  const campo = 'mt-2 w-full rounded-xl border border-slate-300 bg-white p-3 text-slate-800'
  return <main className="min-h-screen bg-pink-50/40 px-4 py-12 dark:bg-slate-950"><div className="mx-auto max-w-6xl">
    <header className="mb-10 text-center"><span className="text-pink-600 font-semibold">SOS Tudo pelo Social</span><h1 className="mt-3 text-4xl font-bold">{titulo}</h1><p className="mt-4">Acompanhe as informações publicadas pela ONG.</p></header>
    <AvisoApi erro={erro} mensagem={mensagem} />
    {carregando ? <p role="status">Carregando…</p> : resultado.erro ? <><AvisoApi erro={resultado.erro} /><button onClick={() => setTentativa(t => t + 1)} className="underline">Tentar novamente</button></> : <>
      {dados.length === 0 && <p role="status">Nenhum conteúdo publicado nesta página.</p>}
      <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">{dados.map(item => <article key={item.id} className="rounded-2xl border border-pink-100 bg-white p-6 text-slate-800 shadow-sm">
        <span className="text-sm text-pink-700">{item.tipo || item.categoria || item.tipo_documento}{item.ano_referencia && ` · ${item.ano_referencia}`}</span>
        <h2 className="mt-3 text-xl font-bold">{item.titulo}</h2><p className="my-4 whitespace-pre-line">{item.resumo}</p>
        {item.data_evento && <p>Data do evento: {new Date(item.data_evento).toLocaleString('pt-BR')}</p>}
        {item.texto_completo && <details className="my-4"><summary className="cursor-pointer text-pink-700">Ler conteúdo completo</summary><p className="mt-3 whitespace-pre-line">{item.texto_completo}</p></details>}
        {item.arquivo_pdf && <a href={backend.arquivoConteudo(recurso, item.id)} target="_blank" rel="noreferrer" className="underline text-pink-700">Baixar documento PDF</a>}
        {(item.imagem || item.imagem_capa) && <a className="block my-3 underline text-pink-700" href={backend.arquivoConteudo(recurso, item.id, item.imagem ? 'imagem' : 'imagem_capa')} target="_blank" rel="noreferrer">Abrir imagem</a>}
        {admin && recurso === 'noticias' && <button disabled={enviando} onClick={() => excluir(item.id)} className="mt-4 block text-red-700 underline">Excluir notícia</button>}
      </article>)}</div>
    </>}
    <nav aria-label="Paginação" className="my-8 flex justify-center gap-4"><button disabled={carregando || pagina <= 1} onClick={() => setPagina(p => p - 1)} className="disabled:opacity-40">Anterior</button><span>Página {pagina}</span><button disabled={carregando || Boolean(resultado?.erro) || pagina >= paginas} onClick={() => setPagina(p => p + 1)} className="disabled:opacity-40">Próxima</button></nav>
    {admin && recurso === 'noticias' && <section className="mt-12 rounded-2xl bg-white p-6 text-slate-800"><h2 className="text-2xl font-bold">Publicar notícia</h2><form onSubmit={publicar}><fieldset disabled={enviando} className="mt-5 space-y-4">
      <label className="block">Título<input name="titulo" required maxLength={150} className={campo} /></label>
      <label className="block">Tipo<select name="tipo" className={campo}><option value="noticia">Notícia</option><option value="evento">Evento</option><option value="campanha">Campanha</option></select></label>
      <label className="block">Resumo<textarea name="resumo" required maxLength={255} className={campo} /></label>
      <label className="block">Conteúdo completo<textarea name="texto_completo" required rows={6} className={campo} /></label>
      <label className="block">Data do evento (opcional)<input name="data_evento" type="datetime-local" className={campo} /></label>
      <label className="block">Imagem (opcional, até 5 MB)<input name="imagem" type="file" accept=".jpg,.jpeg,.png,.webp" className={campo} /></label>
      <button type="submit" className="rounded-xl bg-pink-600 px-6 py-3 text-white">{enviando ? 'Publicando…' : 'Publicar'}</button>
    </fieldset></form></section>}
  </div></main>
}
