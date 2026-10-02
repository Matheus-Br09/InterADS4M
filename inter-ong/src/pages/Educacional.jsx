import { useEffect, useState } from 'react'
import { backend } from '../api/backend'

export default function Educacional() {
  const [pagina, setPagina] = useState(1)
  const [tentativa, setTentativa] = useState(0)
  const [resultado, setResultado] = useState(null)

  useEffect(() => {
    const controller = new AbortController()
    backend.conteudos('materiais', pagina, { signal: controller.signal })
      .then((resposta) => {
        if (!controller.signal.aborted) {
          setResultado({ pagina, tentativa, resposta })
        }
      })
      .catch((error) => {
        if (!controller.signal.aborted) {
          setResultado({ pagina, tentativa, erro: error.message })
        }
      })
    return () => controller.abort()
  }, [pagina, tentativa])

  const carregando = resultado?.pagina !== pagina || resultado?.tentativa !== tentativa
  const erro = !carregando && resultado?.erro
  const materiais = !carregando && !erro ? resultado.resposta.dados : []
  const paginacao = !carregando && !erro ? resultado.resposta.paginacao : null
  const totalPaginas = paginacao ? Math.max(1, Math.ceil(paginacao.total / paginacao.por_pagina)) : 1

  return (
    <main className="min-h-screen bg-pink-50/40 py-12 px-4 sm:px-6 lg:px-8">
      <div className="max-w-6xl mx-auto">
        <div className="text-center mb-10">
          <h1 className="text-3xl sm:text-4xl font-bold text-slate-800 mb-3">Área Educacional</h1>
          <p className="text-slate-600 max-w-xl mx-auto text-sm sm:text-base">
            Materiais educativos disponibilizados pela ONG.
          </p>
        </div>

        <section aria-label="Materiais educativos" aria-busy={carregando}>
          {carregando && <p role="status" className="text-center text-slate-600">Carregando materiais…</p>}
          {erro && (
            <div className="text-center rounded-2xl bg-white border border-pink-100 p-6">
              <p role="alert" className="text-red-700 mb-4">{erro}</p>
              <button type="button" onClick={() => setTentativa((valor) => valor + 1)} className="px-5 py-2 rounded-xl bg-pink-100 text-slate-800 font-semibold">
                Tentar novamente
              </button>
            </div>
          )}
          {!carregando && !erro && materiais.length === 0 && (
            <div className="text-center text-slate-600" role="status">
              <p>Nenhum material disponível nesta página.</p>
              {pagina > 1 && <button type="button" className="mt-4 underline" onClick={() => setPagina(1)}>Voltar à primeira página</button>}
            </div>
          )}
          {!carregando && !erro && (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {materiais.map((material) => (
                <article key={material.id} className="bg-white rounded-2xl p-6 border border-pink-100 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                  <div>
                    <span className="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-pink-100 text-[#fb2782] mb-3">
                      {material.categoria || 'Material educativo'}
                    </span>
                    <h2 className="text-lg font-bold text-slate-800 mb-2">{material.titulo}</h2>
                    {material.descricao && <p className="text-sm text-slate-600 leading-relaxed mb-4 whitespace-pre-line">{material.descricao}</p>}
                  </div>
                  {material.arquivo_pdf ? (
                    <a href={backend.arquivoConteudo('materiais', material.id)} target="_blank" rel="noopener noreferrer" aria-label={`Baixar PDF: ${material.titulo} (abre em nova aba)`} className="mt-4 inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl text-white font-semibold text-sm bg-gradient-to-r from-[#fb2782] to-[#ff4785] hover:opacity-95 transition-opacity">
                      Baixar material (PDF)
                    </a>
                  ) : <p className="mt-4 text-sm text-slate-500">Arquivo ainda não disponível.</p>}
                </article>
              ))}
            </div>
          )}
        </section>

        {!carregando && !erro && materiais.length > 0 && totalPaginas > 1 && (
          <nav aria-label="Paginação dos materiais" className="mt-8 flex items-center justify-center gap-4">
            <button type="button" disabled={pagina <= 1} onClick={() => setPagina((valor) => valor - 1)} className="px-4 py-2 rounded-xl bg-white border border-pink-100 disabled:opacity-40">Anterior</button>
            <span role="status">Página {pagina} de {totalPaginas}</span>
            <button type="button" disabled={pagina >= totalPaginas} onClick={() => setPagina((valor) => valor + 1)} className="px-4 py-2 rounded-xl bg-white border border-pink-100 disabled:opacity-40">Próxima</button>
          </nav>
        )}
      </div>
    </main>
  )
}
