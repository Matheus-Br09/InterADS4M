import { useEffect, useRef, useState } from 'react'
import { Link } from 'react-router-dom'
import { backend } from '../api/backend'

export default function GestaoMateriais() {
  const [acesso, setAcesso] = useState(null)
  const [tentativa, setTentativa] = useState(0)
  const [enviando, setEnviando] = useState(false)
  const [erro, setErro] = useState('')
  const [errosCampos, setErrosCampos] = useState({})
  const [publicado, setPublicado] = useState(null)
  const emAndamento = useRef(false)

  useEffect(() => {
    const controller = new AbortController()
    backend.conta({ signal: controller.signal }).then(({ dados }) => {
      if (!controller.signal.aborted) setAcesso({ tentativa, permitido: dados.tipo_usuario === 'admin' })
    }).catch((error) => {
      if (!controller.signal.aborted) setAcesso({ tentativa, permitido: false, status: error.status, codigo: error.codigo, erro: error.message })
    })
    return () => controller.abort()
  }, [tentativa])

  async function publicar(event) {
    event.preventDefault()
    if (emAndamento.current || !acesso?.permitido) return
    const form = event.currentTarget
    const dados = new FormData(form)
    const pdf = dados.get('arquivo_pdf')
    setErro('')
    setErrosCampos({})
    setPublicado(null)
    if (!pdf?.size || pdf.size > 10 * 1024 * 1024 || !pdf.name.toLowerCase().endsWith('.pdf')) {
      setErrosCampos({ arquivo_pdf: ['Selecione um PDF de até 10 MB.'] })
      return
    }
    emAndamento.current = true
    setEnviando(true)
    try {
      const arquivoBase64 = await new Promise((resolve, reject) => {
        const leitor = new FileReader()
        leitor.onload = () => resolve(leitor.result)
        leitor.onerror = () => reject(new Error('Não foi possível ler o PDF selecionado.'))
        leitor.readAsDataURL(pdf)
      })
      const { dados: material } = await backend.publicarMaterial({
        titulo: dados.get('titulo'),
        categoria: dados.get('categoria'),
        descricao: dados.get('descricao') || null,
        arquivo_pdf_base64: arquivoBase64,
        arquivo_pdf_nome: pdf.name,
      })
      setPublicado(material)
      form.reset()
    } catch (error) {
      setErro(error.message)
      setErrosCampos(error.errors || {})
      if (error.status === 401 || error.status === 403) {
        setAcesso({ tentativa, permitido: false, status: error.status, codigo: error.codigo, erro: error.message })
      }
    } finally {
      emAndamento.current = false
      setEnviando(false)
    }
  }

  const campoErro = (nome) => errosCampos[nome] && <p id={`${nome}-erro`} className="text-sm text-red-700" role="alert">{errosCampos[nome].join(' ')}</p>
  const atributos = (nome) => ({ 'aria-invalid': Boolean(errosCampos[nome]), 'aria-describedby': errosCampos[nome] ? `${nome}-erro` : undefined })
  const campoClasse = 'w-full rounded-xl border border-slate-300 bg-white p-3 text-slate-800 focus:outline-pink-500'
  const verificando = acesso?.tentativa !== tentativa

  return (
    <main className="min-h-screen bg-pink-50/40 px-4 py-12 sm:px-6">
      <div className="mx-auto max-w-2xl">
        <Link to="/login" className="text-sm text-pink-700 underline">Voltar à minha conta</Link>
        <h1 className="mt-4 text-3xl font-bold text-slate-800">Materiais educativos</h1>
        <p className="mt-3 mb-8 text-slate-600">Publique um PDF para disponibilizá-lo na Área Educacional.</p>
        {verificando ? <p role="status">Verificando seu acesso…</p> : !acesso.permitido ? (
          <section className="rounded-2xl border border-pink-100 bg-white p-6">
            <p role="alert" className="mb-4 text-slate-700">
              {acesso.status === 401 ? 'Entre com uma conta de administrador para publicar materiais.'
                : acesso.codigo === 'troca_senha_obrigatoria' ? 'Troque sua senha em Minha conta antes de continuar.'
                  : acesso.erro || 'Esta área é exclusiva dos administradores da ONG.'}
            </p>
            <Link to="/login" className="font-semibold text-pink-700 underline">Ir para Minha conta / Entrar</Link>
            <button type="button" onClick={() => setTentativa((valor) => valor + 1)} className="ml-4 underline">Verificar acesso novamente</button>
          </section>
        ) : (
          <section className="rounded-2xl border border-pink-100 bg-white p-6 sm:p-8" aria-busy={enviando}>
            {publicado && (
              <div className="mb-6 rounded-xl bg-green-50 p-4 text-green-900" role="status">
                <p><strong>{publicado.titulo}</strong> foi publicado com sucesso.</p>
                <Link to="/educacional" className="mt-2 inline-block underline">Ver na Área Educacional</Link>
                <a href={backend.arquivoConteudo('materiais', publicado.id)} target="_blank" rel="noopener noreferrer" className="ml-4 underline">Baixar PDF publicado</a>
              </div>
            )}
            {erro && <p role="alert" className="mb-4 text-red-700">{erro}</p>}
            <form onSubmit={publicar}>
              <fieldset disabled={enviando} className="space-y-5">
                <div>
                  <label htmlFor="titulo" className="mb-2 block font-semibold">Título *</label>
                  <input id="titulo" name="titulo" required maxLength={150} className={campoClasse} {...atributos('titulo')} />
                  {campoErro('titulo')}
                </div>
                <div>
                  <label htmlFor="categoria" className="mb-2 block font-semibold">Categoria *</label>
                  <input id="categoria" name="categoria" required maxLength={100} placeholder="Ex.: Educação, Inclusão, Atividades" className={campoClasse} {...atributos('categoria')} />
                  {campoErro('categoria')}
                </div>
                <div>
                  <label htmlFor="descricao" className="mb-2 block font-semibold">Descrição (opcional)</label>
                  <textarea id="descricao" name="descricao" rows={4} className={campoClasse} {...atributos('descricao')} />
                  {campoErro('descricao')}
                </div>
                <div>
                  <label htmlFor="arquivo_pdf" className="mb-2 block font-semibold">Arquivo PDF *</label>
                  <input id="arquivo_pdf" name="arquivo_pdf" type="file" accept=".pdf,application/pdf" required className={campoClasse} {...atributos('arquivo_pdf')} />
                  <p className="mt-2 text-sm text-slate-500">PDF de até 10 MB. O arquivo ficará disponível ao público após a publicação.</p>
                  {campoErro('arquivo_pdf')}
                </div>
                <button type="submit" className="w-full rounded-xl bg-pink-600 px-6 py-3 font-semibold text-white hover:bg-pink-700 disabled:opacity-60">
                  {enviando ? 'Publicando…' : 'Publicar material'}
                </button>
              </fieldset>
            </form>
          </section>
        )}
      </div>
    </main>
  )
}
