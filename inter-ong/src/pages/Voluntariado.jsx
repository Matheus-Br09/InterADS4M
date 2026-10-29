import { useRef, useState } from 'react'
import { backend } from '../api/backend'
import { Link } from 'react-router-dom'
import AvisoApi from '../components/AvisoApi'
import { ehMaiorDeIdade } from '../utils/idade'
import realRodaConversaImg from '../assets/real-roda-conversa.jpg'
import './css/Voluntariado.css'

export default function Voluntariado() {
  const [erro, setErro] = useState(null)
  const [mensagem, setMensagem] = useState('')
  const [enviando, setEnviando] = useState(false)
  const [sucesso, setSucesso] = useState(false)
  const ocupado = useRef(false)

  async function enviar(event) {
    event.preventDefault()
    if (ocupado.current) return

    const form = event.currentTarget
    const dados = new FormData(form)

    if (!ehMaiorDeIdade(dados.get('data_nascimento'))) {
      setMensagem('')
      setSucesso(false)
      setErro(new Error('É preciso ter 18 anos ou mais para ser voluntário.'))
      return
    }

    // Validação e remoção de arquivo vazio
    const arquivo = dados.get('arquivo_curriculo')
    if (arquivo && arquivo.size > 0) {
      // Arquivo foi enviado, validar tamanho
      if (arquivo.size > 10 * 1024 * 1024) {
        setErro(new Error('Escolha um currículo PDF de até 10 MB.'))
        return
      }
    } else {
      // Nenhum arquivo selecionado, remover do FormData
      dados.delete('arquivo_curriculo')
    }

    ocupado.current = true
    setEnviando(true)
    setErro(null)
    setMensagem('')
    setSucesso(false)

    try {
      const resposta = await backend.inscreverVoluntario(dados)
      setMensagem(resposta.message || 'Inscrição recebida com sucesso! Entraremos em contato em breve.')
      setSucesso(true)
      form.reset()
    } catch (error) {
      setErro(error)
      setSucesso(false)
    } finally {
      ocupado.current = false
      setEnviando(false)
    }
  }

  return (
    <main className="volunteer-page">
      <section className="volunteer-hero">
        <div className="hero-content">
          <span className="eyebrow">♥ Junte-se a nós</span>
          <h1>
            Seja um
            <br />
            <span>voluntário</span>
          </h1>
          <p>
            Sua atitude pode transformar realidades. Envie seu currículo e faça parte de um movimento que acredita em pessoas.
          </p>
        </div>
        <div className="hero-visual">
          <div className="hero-circle" />
          <img src={realRodaConversaImg} alt="Equipe e voluntários reunidos em roda de conversa no instituto" />
        </div>
      </section>

      <section className="form-section">
        <div className="form-card">
          <div className="form-heading">
            <h2>Vamos conversar?</h2>
            <p>Preencha o formulário abaixo e entraremos em contato para conhecer você melhor.</p>
          </div>

          <AvisoApi erro={erro} mensagem={mensagem} />

          {sucesso && (
            <div className="mb-6 rounded-xl bg-green-50 border border-green-200 p-4 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-300">
              <h3 className="font-bold mb-2">✅ Inscrição enviada!</h3>
              <p>Recebemos sua inscrição e vamos analisá-la com carinho. Em breve entraremos em contato por email ou telefone.</p>
            </div>
          )}

          <form onSubmit={enviar}>
            <fieldset disabled={enviando}>
              <div className="form-grid">
                {/* Nome Completo */}
                <div className="field full-width">
                  <label htmlFor="nome_completo">Nome completo *</label>
                  <input
                    id="nome_completo"
                    name="nome_completo"
                    type="text"
                    required
                    maxLength={255}
                    placeholder="Maria Silva"
                    autoComplete="name"
                  />
                </div>

                {/* Email */}
                <div className="field">
                  <label htmlFor="email">Email *</label>
                  <input
                    id="email"
                    name="email"
                    type="email"
                    required
                    maxLength={255}
                    placeholder="maria@email.com"
                    autoComplete="email"
                  />
                </div>

                {/* Celular */}
                <div className="field">
                  <label htmlFor="celular">Celular/WhatsApp *</label>
                  <input
                    id="celular"
                    name="celular"
                    type="tel"
                    required
                    maxLength={20}
                    placeholder="(11) 98765-4321"
                    autoComplete="tel"
                  />
                </div>

                {/* Data de nascimento */}
                <div className="field">
                  <label htmlFor="data_nascimento">Data de nascimento *</label>
                  <input
                    id="data_nascimento"
                    name="data_nascimento"
                    type="date"
                    required
                    autoComplete="bday"
                  />
                </div>

                {/* Área de Atuação */}
                <div className="field">
                  <label htmlFor="area_atuacao">Área de atuação *</label>
                  <select id="area_atuacao" name="area_atuacao" required defaultValue="">
                    <option value="">Selecione</option>
                    {['Neuropedagogia', 'Odontologia', 'Nutrição', 'Fisioterapia', 'Apoio Geral', 'Outros'].map((v) => (
                      <option key={v} value={v}>
                        {v}
                      </option>
                    ))}
                  </select>
                </div>

                {/* Disponibilidade */}
                <div className="field">
                  <label htmlFor="disponibilidade">Disponibilidade *</label>
                  <select id="disponibilidade" name="disponibilidade" required defaultValue="">
                    <option value="">Selecione</option>
                    {['Manhã', 'Tarde', 'Integral'].map((v) => (
                      <option key={v} value={v}>
                        {v}
                      </option>
                    ))}
                  </select>
                </div>

                {/* Currículo (opcional agora) */}
                <div className="field full-width">
                  <label htmlFor="arquivo_curriculo">Currículo PDF (opcional, até 10 MB)</label>
                  <input
                    id="arquivo_curriculo"
                    name="arquivo_curriculo"
                    type="file"
                    accept=".pdf,application/pdf"
                  />
                  <small className="text-slate-500 dark:text-slate-400 text-sm mt-1 block">
                    Se preferir, pode enviar seu currículo depois por email
                  </small>
                </div>
              </div>

              <label className="mt-4 flex items-start gap-2 text-sm">
                <input type="checkbox" name="aceite_privacidade" value="1" required className="mt-1" />
                <span>
                  Li e aceito a <Link to="/privacidade" className="underline">política de privacidade</Link>
                </span>
              </label>

              <div className="form-footer">
                <button className="submit-button" type="submit" disabled={enviando}>
                  {enviando ? 'Enviando…' : 'Enviar inscrição →'}
                </button>
              </div>
            </fieldset>
          </form>
        </div>
      </section>
    </main>
  )
}
