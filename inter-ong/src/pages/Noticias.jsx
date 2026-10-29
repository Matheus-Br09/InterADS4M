import { useEffect, useState } from 'react'
import { backend } from '../api/backend'
import AvisoApi from '../components/AvisoApi'

export default function Noticias({ recurso = 'noticias', titulo = 'Notícias e eventos' }) {
  const [pagina, setPagina] = useState(1)
  const [tentativa, setTentativa] = useState(0)
  const [resultado, setResultado] = useState(null)
  useEffect(() => {
    const controller = new AbortController()
    backend.conteudos(recurso, pagina, { signal: controller.signal })
      .then(resposta => { if (!controller.signal.aborted) setResultado({ recurso, pagina, tentativa, resposta }) })
      .catch(error => { if (!controller.signal.aborted) setResultado({ recurso, pagina, tentativa, erro: error }) })
    return () => controller.abort()
  }, [recurso, pagina, tentativa])
  const carregando = resultado?.recurso !== recurso || resultado?.pagina !== pagina || resultado?.tentativa !== tentativa
  const dados = !carregando ? resultado.resposta?.dados || [] : []
  const paginacao = resultado?.resposta?.paginacao
  const paginas = paginacao ? Math.max(1, Math.ceil(paginacao.total / paginacao.por_pagina)) : 1
  return <main className="min-h-screen bg-pink-50/40 px-4 py-12 dark:bg-slate-950"><div className="mx-auto max-w-6xl">
    <header className="mb-10 text-center"><span className="text-pink-600 font-semibold">SOS Tudo pelo Social</span><h1 className="mt-3 text-4xl font-bold">{titulo}</h1><p className="mt-4">Acompanhe as informações publicadas pela ONG.</p></header>
    {carregando ? <p role="status">Carregando…</p> : resultado.erro ? <><AvisoApi erro={resultado.erro} /><button onClick={() => setTentativa(t => t + 1)} className="underline">Tentar novamente</button></> : <>
      {dados.length === 0 && <p role="status">Nenhum conteúdo publicado nesta página.</p>}
      <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">{dados.map(item => <article key={item.id} className="rounded-2xl border border-pink-100 bg-white p-6 text-slate-800 shadow-sm">
        <span className="text-sm text-pink-700">{item.tipo || item.categoria || item.tipo_documento}{item.ano_referencia && ` · ${item.ano_referencia}`}</span>
        <h2 className="mt-3 text-xl font-bold">{item.titulo}</h2><p className="my-4 whitespace-pre-line">{item.resumo}</p>
        {item.data_evento && <p>Data do evento: {new Date(item.data_evento).toLocaleString('pt-BR')}</p>}
        {item.texto_completo && <details className="my-4"><summary className="cursor-pointer text-pink-700">Ler conteúdo completo</summary><p className="mt-3 whitespace-pre-line">{item.texto_completo}</p></details>}
        {item.arquivo_pdf && <a href={backend.arquivoConteudo(recurso, item.id)} target="_blank" rel="noreferrer" className="underline text-pink-700">Baixar documento PDF</a>}
        {(item.imagem || item.imagem_capa) && <a className="block my-3 underline text-pink-700" href={backend.arquivoConteudo(recurso, item.id, item.imagem ? 'imagem' : 'imagem_capa')} target="_blank" rel="noreferrer">Abrir imagem</a>}
      </article>)}</div>
    </>}
    <nav aria-label="Paginação" className="my-8 flex justify-center gap-4"><button disabled={carregando || pagina <= 1} onClick={() => setPagina(p => p - 1)} className="disabled:opacity-40">Anterior</button><span>Página {pagina}</span><button disabled={carregando || Boolean(resultado?.erro) || pagina >= paginas} onClick={() => setPagina(p => p + 1)} className="disabled:opacity-40">Próxima</button></nav>
  </div></main>
}
