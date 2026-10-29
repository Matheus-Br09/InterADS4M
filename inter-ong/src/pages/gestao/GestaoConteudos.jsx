import { useState } from 'react'
import { backend } from '../../api/backend'
import AvisoApi from '../../components/AvisoApi'
import useLista from './useLista'
import Paginacao from './Paginacao'

const ABAS = [['noticias', 'Notícias'], ['materiais', 'Materiais educativos'], ['transparencia', 'Transparência']]
const TIPOS_NOTICIA = [['noticia', 'Notícia'], ['evento', 'Evento'], ['campanha', 'Campanha']]
const TIPOS_DOCUMENTO = ['Relatório Anual', 'Balancete', 'Estatuto', 'Certidão', 'Outros']
const AVISO_IMAGEM = 'Não publique fotos em que crianças possam ser reconhecidas sem autorização dos responsáveis.'

const campo = 'mt-1 block w-full rounded-lg border border-slate-300 bg-white p-2 font-normal'
const rotuloCampo = 'block text-sm font-semibold'

function Campos({ tipo }) {
  if (tipo === 'noticias') return <>
    <label className={rotuloCampo}>Título<input name="titulo" required maxLength={150} className={campo} /></label>
    <label className={rotuloCampo}>Tipo
      <select name="tipo" required className={campo}>{TIPOS_NOTICIA.map(([v, n]) => <option key={v} value={v}>{n}</option>)}</select>
    </label>
    <label className={rotuloCampo}>Resumo (até 255 letras)<input name="resumo" required maxLength={255} className={campo} /></label>
    <label className={rotuloCampo}>Texto completo<textarea name="texto_completo" required rows={6} className={campo} /></label>
    <label className={rotuloCampo}>Data do evento (se for um evento)<input type="date" name="data_evento" className={campo} /></label>
    <label className={rotuloCampo}>Imagem (opcional, JPG, PNG ou WEBP, até 5 MB)<input type="file" name="imagem" accept="image/jpeg,image/png,image/webp" className={campo} /></label>
    <p className="rounded-lg bg-yellow-50 p-3 text-sm text-yellow-900">{AVISO_IMAGEM}</p>
  </>
  if (tipo === 'materiais') return <>
    <label className={rotuloCampo}>Título<input name="titulo" required maxLength={150} className={campo} /></label>
    <label className={rotuloCampo}>Categoria<input name="categoria" required maxLength={100} className={campo} /></label>
    <label className={rotuloCampo}>Descrição (opcional)<textarea name="descricao" rows={3} className={campo} /></label>
    <label className={rotuloCampo}>Arquivo PDF (até 10 MB)<input type="file" name="arquivo_pdf" required accept="application/pdf" className={campo} /></label>
    <label className={rotuloCampo}>Imagem de capa (opcional, JPG, PNG ou WEBP, até 5 MB)<input type="file" name="imagem_capa" accept="image/jpeg,image/png,image/webp" className={campo} /></label>
    <p className="rounded-lg bg-yellow-50 p-3 text-sm text-yellow-900">{AVISO_IMAGEM}</p>
  </>
  return <>
    <label className={rotuloCampo}>Título<input name="titulo" required maxLength={150} className={campo} /></label>
    <label className={rotuloCampo}>Ano de referência<input type="number" name="ano_referencia" required min={1900} max={2200} className={campo} /></label>
    <label className={rotuloCampo}>Tipo de documento
      <select name="tipo_documento" required className={campo}>{TIPOS_DOCUMENTO.map((t) => <option key={t}>{t}</option>)}</select>
    </label>
    <label className={rotuloCampo}>Arquivo PDF (até 10 MB)<input type="file" name="arquivo_pdf" required accept="application/pdf" className={campo} /></label>
  </>
}

function Conteudos({ tipo }) {
  const [acao, setAcao] = useState({ erro: null, mensagem: '', ocupado: false })
  const [formKey, setFormKey] = useState(0)
  const lista = useLista((pagina, options) => backend.gestaoConteudos(tipo, pagina, options), tipo)

  async function publicar(evento) {
    evento.preventDefault()
    if (acao.ocupado) return
    const dados = new FormData(evento.currentTarget)
    // Campos opcionais vazios (arquivo sem escolha, texto em branco) não devem ser enviados.
    for (const [nome, valor] of [...dados.entries()]) {
      if ((valor instanceof File && valor.size === 0 && !valor.name) || valor === '') dados.delete(nome)
    }
    setAcao({ erro: null, mensagem: '', ocupado: true })
    try {
      await backend.publicarConteudo(tipo, dados)
      setAcao({ erro: null, mensagem: 'Conteúdo publicado.', ocupado: false })
      setFormKey((k) => k + 1)
      lista.recarregar()
    } catch (erro) {
      setAcao({ erro, mensagem: '', ocupado: false })
    }
  }

  async function excluir(item) {
    if (acao.ocupado || !window.confirm(`Excluir "${item.titulo}"? O conteúdo sai do site e não tem volta.`)) return
    setAcao({ erro: null, mensagem: '', ocupado: true })
    try {
      await backend.excluirConteudo(tipo, item.id)
      setAcao({ erro: null, mensagem: 'Conteúdo excluído.', ocupado: false })
      lista.recarregar()
    } catch (erro) {
      setAcao({ erro, mensagem: '', ocupado: false })
    }
  }

  return (
    <div className="mt-6 grid gap-8 lg:grid-cols-2">
      <form key={formKey} onSubmit={publicar} className="space-y-4 rounded-2xl bg-white p-5 shadow">
        <h3 className="text-lg font-bold">Publicar novo</h3>
        <Campos tipo={tipo} />
        <button disabled={acao.ocupado} className="rounded-lg bg-pink-700 px-5 py-2 font-semibold text-white disabled:opacity-50">{acao.ocupado ? 'Enviando…' : 'Publicar'}</button>
      </form>
      <div>
        <h3 className="text-lg font-bold">Já publicados</h3>
        <AvisoApi erro={acao.erro || lista.erro} mensagem={acao.mensagem} />
        {lista.carregando && <p role="status">Carregando…</p>}
        {!lista.carregando && !lista.erro && lista.itens.length === 0 && <p>Nada publicado ainda.</p>}
        <ul className="mt-2 space-y-3">
          {lista.itens.map((item) => (
            <li key={item.id} className="flex items-center justify-between gap-3 rounded-2xl bg-white p-4 shadow">
              <span className="min-w-0 break-words font-semibold">{item.titulo}</span>
              <button disabled={acao.ocupado} onClick={() => excluir(item)} className="shrink-0 rounded-lg border border-red-300 px-3 py-2 text-sm font-semibold text-red-800 disabled:opacity-50">Excluir</button>
            </li>
          ))}
        </ul>
        <Paginacao pagina={lista.pagina} ultima={lista.ultima} setPagina={lista.setPagina} />
      </div>
    </div>
  )
}

export default function GestaoConteudos() {
  const [tipo, setTipo] = useState('noticias')
  return (
    <section>
      <h2 className="text-2xl font-bold">Conteúdos</h2>
      <div className="mt-4 flex flex-wrap gap-2" role="group" aria-label="Tipo de conteúdo">
        {ABAS.map(([valor, nome]) => (
          <button key={valor} aria-pressed={tipo === valor} onClick={() => setTipo(valor)}
            className={`rounded-lg border px-3 py-2 text-sm font-semibold ${tipo === valor ? 'border-pink-700 bg-pink-700 text-white' : 'border-slate-300 bg-white'}`}>{nome}</button>
        ))}
      </div>
      <Conteudos key={tipo} tipo={tipo} />
    </section>
  )
}
