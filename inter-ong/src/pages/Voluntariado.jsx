
import { useState } from "react";
import "./css/Voluntariado.css";

export default function Voluntariado() {
  const [form, setForm] = useState({
    nome: "",
    email: "",
    telefone: "",
    area: "",
    disponibilidade: "",
    motivacao: "",
  });

  const [enviado, setEnviado] = useState(false);

  function handleChange(e) {
    setForm({ ...form, [e.target.name]: e.target.value });
  }

  function handleSubmit(e) {
    e.preventDefault();
    setEnviado(true);
    // Aqui você pode integrar o envio com sua API ou backend.
  }

  return (
    <main className="volunteer-page">
      <section className="volunteer-hero">
        <div className="hero-content">
          <span className="eyebrow">
            <span>♥</span> Junte-se a nós
          </span>

          <h1>
            Seja um
            <br />
            <span>voluntário</span>
          </h1>

          <p>
            Sua atitude pode transformar realidades.
            Preencha o formulário e faça parte de um
            movimento que acredita em pessoas,
            oportunidades e em um futuro melhor.
          </p>

          <div className="hero-note">
            <span>♥</span>
            <div>
              <strong>Juntos fazemos a diferença!</strong>
              <small>Cada gesto de solidariedade importa.</small>
            </div>
          </div>
        </div>

        <div className="hero-visual">
          <div className="hero-circle" />
          <img
            src="https://images.unsplash.com/photo-1559027615-cd4628902d4a?auto=format&fit=crop&w=900&q=85"
            alt="Pessoas trabalhando juntas em uma ação voluntária"
          />
          <span className="floating-heart">♡</span>
        </div>
      </section>

      <section className="form-section">
        <div className="form-card">
          <div className="form-heading">
            <span className="eyebrow">
              <span>♥</span> Formulário de voluntariado
            </span>

            <h2>Vamos conversar?</h2>
            <p>
              Preencha seus dados e conte um pouco sobre você.
              Nossa equipe poderá entrar em contato.
            </p>
          </div>

          {enviado ? (
            <div className="success-message">
              <span className="success-heart">♥</span>
              <h3>Obrigado pelo seu interesse!</h3>
              <p>
                Seus dados foram preenchidos. Para concluir a
                inscrição, conecte este formulário ao sistema
                de envio da organização.
              </p>
              <button
                type="button"
                className="submit-button"
                onClick={() => setEnviado(false)}
              >
                Voltar ao formulário
              </button>
            </div>
          ) : (
            <form onSubmit={handleSubmit}>
              <div className="form-grid">
                <div className="field">
                  <label htmlFor="nome">Nome completo <b>*</b></label>
                  <input
                    id="nome"
                    name="nome"
                    value={form.nome}
                    onChange={handleChange}
                    placeholder="Digite seu nome completo"
                    autoComplete="name"
                    required
                  />
                </div>

                <div className="field">
                  <label htmlFor="email">E-mail <b>*</b></label>
                  <input
                    id="email"
                    name="email"
                    type="email"
                    value={form.email}
                    onChange={handleChange}
                    placeholder="seu@email.com"
                    autoComplete="email"
                    required
                  />
                </div>

                <div className="field">
                  <label htmlFor="telefone">Telefone <b>*</b></label>
                  <input
                    id="telefone"
                    name="telefone"
                    type="tel"
                    value={form.telefone}
                    onChange={handleChange}
                    placeholder="(00) 00000-0000"
                    autoComplete="tel"
                    required
                  />
                </div>

                <div className="field">
                  <label htmlFor="area">Área de interesse <b>*</b></label>
                  <select
                    id="area"
                    name="area"
                    value={form.area}
                    onChange={handleChange}
                    required
                  >
                    <option value="">Selecione uma área</option>
                    <option value="Educacao">Educação</option>
                    <option value="Acoes sociais">Ações sociais</option>
                    <option value="Eventos">Eventos</option>
                    <option value="Comunicacao">Comunicação</option>
                    <option value="Apoio administrativo">Apoio administrativo</option>
                    <option value="Outra">Outra</option>
                  </select>
                </div>

                <div className="field full-width">
                  <label htmlFor="disponibilidade">
                    Disponibilidade <b>*</b>
                  </label>
                  <select
                    id="disponibilidade"
                    name="disponibilidade"
                    value={form.disponibilidade}
                    onChange={handleChange}
                    required
                  >
                    <option value="">Selecione sua disponibilidade</option>
                    <option value="Durante a semana">Durante a semana</option>
                    <option value="Finais de semana">Finais de semana</option>
                    <option value="Algumas horas por mês">Algumas horas por mês</option>
                    <option value="Disponibilidade flexível">Disponibilidade flexível</option>
                  </select>
                </div>

                <div className="field full-width">
                  <label htmlFor="motivacao">
                    Por que deseja ser voluntário? <b>*</b>
                  </label>
                  <textarea
                    id="motivacao"
                    name="motivacao"
                    value={form.motivacao}
                    onChange={handleChange}
                    placeholder="Conte um pouco sobre sua motivação..."
                    rows="5"
                    required
                  />
                </div>
              </div>

              <div className="form-footer">
                <small><b>*</b> Campos obrigatórios</small>
                <button className="submit-button" type="submit">
                  Enviar formulário <span>→</span>
                </button>
              </div>
            </form>
          )}
        </div>
      </section>

      <section className="volunteer-cta">
        <span className="cta-heart">♡</span>
        <h2>
          Juntos podemos construir
          <br />
          <span>um mundo mais inclusivo.</span>
        </h2>
        <div className="cta-line" />
      </section>
    </main>
  );
}