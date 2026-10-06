import { useEffect, useRef, useState } from 'react'
import { backend } from '../api/backend'
import AvisoApi from '../components/AvisoApi'
import './css/Voluntariado.css'

export default function Voluntariado() {
  const [inscricao, setInscricao] = useState(null)
  const [erro, setErro] = useState(null)
  const [mensagem, setMensagem] = useState('')
  const [carregando, setCarregando] = useState(true)
  const [enviando, setEnviando] = useState(false)
  const [tentativa, setTentativa] = useState(0)
  const ocupado = useRef(false)
  useEffect(() => {
    const controller = new AbortController()
    backend.voluntariado({ signal: controller.signal }).then(({ dados }) => {
      if (!controller.signal.aborted) { setInscricao(dados); setErro(null) }
    }).catch(error => { if (!controller.signal.aborted) setErro(error) })
      .finally(() => { if (!controller.signal.aborted) setCarregando(false) })
    return () => controller.abort()
  }, [tentativa])
  async function enviar(event) {
    event.preventDefault()
    if (ocupado.current) return
    const form = event.currentTarget
    const dados = new FormData(form)
    if (dados.get('arquivo_curriculo').size > 10 * 1024 * 1024) {
      setErro(new Error('Escolha um currículo PDF de até 10 MB.')); return
    }
    ocupado.current = true; setEnviando(true); setErro(null); setMensagem('')
    try {
      const { dados: resultado } = await backend.inscreverVoluntario(dados)
      setInscricao(resultado); setMensagem('Inscrição recebida e enviada para análise.'); form.reset()
    } catch (error) { setErro(error) }
    finally { ocupado.current = false; setEnviando(false) }
  }
  return <main className="volunteer-page">
    <section className="volunteer-hero"><div className="hero-content">
      <span className="eyebrow">♥ Junte-se a nós</span><h1>Seja um<br /><span>voluntário</span></h1>
      <p>Sua atitude pode transformar realidades. Envie seu currículo e faça parte de um movimento que acredita em pessoas.</p>
    </div><div className="hero-visual"><div className="hero-circle" /><img src="https://images.unsplash.com/photo-1559027615-cd4628902d4a?auto=format&fit=crop&w=900&q=85" alt="Pessoas trabalhando juntas" /></div></section>
    <section className="form-section"><div className="form-card">
      <div className="form-heading"><h2>Vamos conversar?</h2><p>A inscrição usa os dados da sua conta. Enviar novamente substitui a inscrição anterior e retorna para análise.</p></div>
      <AvisoApi erro={erro} mensagem={mensagem} />
      {carregando ? <p role="status">Consultando sua inscrição…</p> : <>
        {inscricao && <div className="mb-6 rounded-xl bg-pink-50 p-4 text-slate-800"><h3>Sua inscrição</h3><p>Status: {inscricao.status.replaceAll('_', ' ')}</p><p>{inscricao.area_atuacao} · {inscricao.disponibilidade}</p>{inscricao.data_entrevista && <p>Entrevista: {new Date(inscricao.data_entrevista).toLocaleString('pt-BR')}</p>}{inscricao.mensagem_entrevista && <p>{inscricao.mensagem_entrevista}</p>}</div>}
        {erro && <button type="button" onClick={() => { setCarregando(true); setTentativa(t => t + 1) }} className="underline mb-4">Consultar inscrição novamente</button>}
        <form onSubmit={enviar}><fieldset disabled={enviando || erro?.status === 401 || erro?.codigo === 'troca_senha_obrigatoria'}>
          <div className="form-grid">
            <div className="field"><label htmlFor="area_atuacao">Área de atuação *</label><select id="area_atuacao" name="area_atuacao" required defaultValue=""><option value="">Selecione</option>{['Neuropedagogia', 'Odontologia', 'Nutrição', 'Fisioterapia', 'Apoio Geral', 'Outros'].map(v => <option key={v}>{v}</option>)}</select></div>
            <div className="field"><label htmlFor="disponibilidade">Disponibilidade *</label><select id="disponibilidade" name="disponibilidade" required defaultValue=""><option value="">Selecione</option>{['Manhã', 'Tarde', 'Integral'].map(v => <option key={v}>{v}</option>)}</select></div>
            <div className="field full-width"><label htmlFor="arquivo_curriculo">Currículo PDF (até 10 MB) *</label><input id="arquivo_curriculo" name="arquivo_curriculo" type="file" accept=".pdf,application/pdf" required /></div>
          </div><div className="form-footer"><button className="submit-button" type="submit">{enviando ? 'Enviando…' : 'Enviar inscrição →'}</button></div>
        </fieldset></form>
      </>}
    </div></section>
  </main>
}
