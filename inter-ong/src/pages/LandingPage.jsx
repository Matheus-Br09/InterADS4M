import React from 'react'
import { Link } from 'react-router-dom'
import './css/LandingPage.css'


import heroChildImg from '../assets/hero-child.jpg'
import handsPuzzleImg from '../assets/hands-puzzle.jpg'
import projectEduImg from '../assets/project-edu.jpg'
import projectSupportImg from '../assets/project-support.jpg'
import projectCommunityImg from '../assets/project-community.jpg'
import projectCampaignImg from '../assets/project-campaign.jpg'
import helpHeartImg from '../assets/help-heart.jpg'
import avatarMaeImg from '../assets/avatar-mae.jpg'
import avatarVoluntarioImg from '../assets/avatar-voluntario.jpg'
import avatarParceiroImg from '../assets/avatar-parceiro.jpg'

export default function LandingPage() {
  return (
    <main className="landing-page-main" id="conteudo-principal">

      <a
        href="#conteudo-principal"
        className="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-[9999] focus:px-4 focus:py-2 focus:bg-white focus:text-pink-600 focus:rounded focus:shadow-lg focus:font-semibold"
      >
        Pular para o conteúdo principal
      </a>
      <section className="hero-section">
        
        <div className="hero-ambient-glow-sky" />
        <div className="hero-ambient-glow-pink" />

        <div className="landing-container">
          <div className="hero-grid">
            
          
            <div className="hero-content">
              
      
              <div className="badge-pill badge-pill-pink">
                <span className="badge-icon-heart">♥</span>
                <span>Juntos por um mundo mais inclusivo</span>
              </div>

        
              <h1 className="hero-headline">
                Acolher<br />
                Inclui<br />
                <span className="text-pink-highlight">Transforma</span>
              </h1>

     
              <p className="hero-subtitle">
                A SOS Tudo pelo Social desenvolve ações que acolhem, incluem e criam novas oportunidades para pessoas em situação de vulnerabilidade.
              </p>

    
              <div className="hero-actions-row">
                <a href="#projetos" className="btn-primary-pink">
                  <span>Conheça nossos projetos</span>
                  <span>→</span>
                </a>

                <Link to="/doar" className="btn-secondary-outline">
                  <span>Quero ajudar</span>
                  <span>♡</span>
                </Link>
              </div>

      
              <div className="hero-metrics-pills-row">

                <div className="hero-metric-pill-item">
                  <div className="hero-pill-icon-box">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
                      <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                    </svg>
                  </div>
                  <div>
                    <div className="hero-pill-number">+100</div>
                    <div className="hero-pill-label">pessoas atendidas</div>
                  </div>
                </div>

 
                <div className="hero-metric-pill-item">
                  <div className="hero-pill-icon-box">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
                      <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                    </svg>
                  </div>
                  <div>
                    <div className="hero-pill-number">+20</div>
                    <div className="hero-pill-label">projetos realizados</div>
                  </div>
                </div>


                <div className="hero-metric-pill-item">
                  <div className="hero-pill-icon-box">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">
                      <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                    </svg>
                  </div>
                  <div>
                    <div className="hero-pill-number">+50</div>
                    <div className="hero-pill-label">voluntários</div>
                  </div>
                </div>
              </div>

            </div>


            <div className="hero-visual-wrapper">
              <div className="hero-blob-shape" />

              <div className="hero-photo-card">
                <img
                  src={heroChildImg}
                  alt="Criança apoiada pela SOS Tudo pelo Social"
                />
                <div className="hero-shirt-tag">
                  <span className="hero-shirt-sos">SOS</span>
                  <span className="hero-shirt-sub">Tudo pelo social</span>
                </div>
              </div>


              <div className="hero-float-badge">
                <span className="hero-float-badge-icon">♥</span>
                <p className="hero-float-badge-text">
                  Pequenas ações,<br />
                  grandes mudanças.
                </p>
              </div>


              <div className="hero-floating-doodles doodle-font">
                <span className="hero-doodle-line hero-doodle-line-1">Mais inclusão</span>
                <span className="hero-doodle-line hero-doodle-line-2">Mais oportunidades</span>
                <span className="hero-doodle-line hero-doodle-line-3">Mais vidas</span>
                <span className="hero-doodle-heart">♡</span>
              </div>


              <div className="hero-left-doodle-heart" aria-hidden="true">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" aria-hidden="true" focusable="false">
                  <path strokeLinecap="round" strokeLinejoin="round" d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                </svg>
              </div>
            </div>

          </div>
        </div>
      </section>



      <section className="about-section" aria-label="Sobre nós">
        <div className="landing-container">
          <div className="about-grid">
            

            <div className="about-photo-wrapper">
              <div className="about-photo-box">
                <img
                  src={handsPuzzleImg}
                  alt="Mãos segurando coração de quebra-cabeça"
                />
              </div>
            </div>


            <div className="about-content">
              <div className="badge-pill badge-pill-pink">
                <span className="badge-icon-heart">♥</span>
                <span>Quem somos</span>
              </div>

              <h2 className="about-title">
                Mais que assistência,<br />
                <span className="text-pink-highlight">criamos oportunidades.</span>
              </h2>

              <p className="about-desc">
                A SOS Tudo pelo Social é uma organização sem fins lucrativos que atua no apoio, inclusão e desenvolvimento de pessoas em situação de vulnerabilidade, com foco em ações sociais, educacionais e de acolhimento.
              </p>

              <div>
                <Link to="/sobre" className="btn-outline-small">
                  <span>Conheça nossa história</span>
                  <span>→</span>
                </Link>
              </div>
            </div>


            <div className="about-pillars-grid">
              

              <div className="pillar-card">
                <div className="pillar-icon-box">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                  </svg>
                </div>
                <h3 className="pillar-card-title">Inclusão</h3>
                <p className="pillar-card-desc">
                  Acreditamos em um mundo mais justo para todos.
                </p>
              </div>


              <div className="pillar-card">
                <div className="pillar-icon-box">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                  </svg>
                </div>
                <h3 className="pillar-card-title">Acolhimento</h3>
                <p className="pillar-card-desc">
                  Valorizamos cada história e cada pessoa.
                </p>
              </div>


              <div className="pillar-card">
                <div className="pillar-icon-box">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM6 4h5v8l-2.5-1.5L6 12V4z" />
                  </svg>
                </div>
                <h3 className="pillar-card-title">Educação</h3>
                <p className="pillar-card-desc">
                  Promovemos conhecimento como ferramenta de mudança.
                </p>
              </div>

              <div className="pillar-card">
                <div className="pillar-icon-box">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2L4.5 20.29l.71.71L12 18l6.79 3 .71-.71z" />
                  </svg>
                </div>
                <h3 className="pillar-card-title">Oportunidades</h3>
                <p className="pillar-card-desc">
                  Trabalhamos para abrir novos caminhos.
                </p>
              </div>

            </div>

          </div>
        </div>
      </section>



      <section id="projetos" className="projects-section" aria-label="Nossos projetos">
        <div className="landing-container">
          

          <div className="projects-header-row">
            <div className="projects-header-left">
              <div className="badge-pill badge-pill-pink">
                <span className="badge-icon-heart">♥</span>
                <span>Nossas iniciativas</span>
              </div>
              <h2 className="section-headline">
                Conheça nossos <span className="text-pink-highlight">projetos</span>
              </h2>
              <p className="section-subtext">
                Ações que fazem a diferença na vida de muitas pessoas.
              </p>
            </div>

            <div>
              <Link to="/educacional" className="projects-link-all">
                <span>Ver todos os projetos</span>
                <span>→</span>
              </Link>
            </div>
          </div>


          <div className="projects-grid">
            

            <div className="project-card">
              <div className="project-image-box">
                <img src={projectEduImg} alt="Área Educacional" />
              </div>
              <div className="project-body">
                <div>
                  <span className="project-category-tag tag-sky">Educação Inclusiva</span>
                  <h3 className="project-card-title">Área Educacional</h3>
                  <p className="project-card-desc">
                    Atividades e recursos que estimulam o desenvolvimento e a aprendizagem de forma inclusiva.
                  </p>
                </div>
                <div className="project-card-bottom">
                  <Link to="/educacional" className="btn-circle-arrow" aria-label="Acessar projeto">
                    →
                  </Link>
                </div>
              </div>
            </div>

            <div className="project-card">
              <div className="project-image-box">
                <img src={projectSupportImg} alt="Acolhimento e Suporte" />
              </div>
              <div className="project-body">
                <div>
                  <span className="project-category-tag tag-pink">Apoio Social</span>
                  <h3 className="project-card-title">Acolhimento e Suporte</h3>
                  <p className="project-card-desc">
                    Ações de apoio a famílias em situação de vulnerabilidade, com foco no cuidado e na dignidade.
                  </p>
                </div>
                <div className="project-card-bottom">
                  <Link to="/sobre" className="btn-circle-arrow" aria-label="Acessar projeto">
                    →
                  </Link>
                </div>
              </div>
            </div>

            <div className="project-card">
              <div className="project-image-box">
                <img src={projectCommunityImg} alt="Cidadania em Ação" />
              </div>
              <div className="project-body">
                <div>
                  <span className="project-category-tag tag-sky">Comunidade</span>
                  <h3 className="project-card-title">Cidadania em Ação</h3>
                  <p className="project-card-desc">
                    Projetos que fortalecem os laços sociais e promovem a inclusão na comunidade.
                  </p>
                </div>
                <div className="project-card-bottom">
                  <Link to="/sobre" className="btn-circle-arrow" aria-label="Acessar projeto">
                    →
                  </Link>
                </div>
              </div>
            </div>


            <div className="project-card">
              <div className="project-image-box">
                <img src={projectCampaignImg} alt="Eventos e Campanhas" />
              </div>
              <div className="project-body">
                <div>
                  <span className="project-category-tag tag-pink">Conscientização</span>
                  <h3 className="project-card-title">Eventos e Campanhas</h3>
                  <p className="project-card-desc">
                    Iniciativas que informam, sensibilizam e mobilizam a sociedade para causas sociais importantes.
                  </p>
                </div>
                <div className="project-card-bottom">
                  <Link to="/contato" className="btn-circle-arrow" aria-label="Acessar projeto">
                    →
                  </Link>
                </div>
              </div>
            </div>

          </div>

        </div>
      </section>

      <section className="impact-section" aria-label="Nosso impacto">
        <div className="landing-container">
          
          <div className="impact-banner-wrapper">
            

            <div className="impact-doodle-sparkle">
              <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                <path strokeLinecap="round" d="M3 12h3m12 0h3M5.6 5.6l2.1 2.1m8.6 8.6l2.1 2.1M12 3v3m0 12v3M5.6 18.4l2.1-2.1m8.6-8.6l2.1-2.1" />
              </svg>
            </div>

            <div className="impact-grid">
              
   
              <div className="impact-left-content">
                <div className="badge-pill badge-pill-sky">
                  <span className="badge-icon-heart">♥</span>
                  <span>Nosso impacto</span>
                </div>
                <h2 className="impact-title">
                  Pequenas ações,<br />
                  <span className="text-pink-highlight">grandes transformações.</span>
                </h2>
                <p className="impact-desc">
                  Números que representam vidas, histórias e um futuro mais inclusivo.
                </p>
              </div>

         
              <div className="impact-metrics-row">
                

                <div className="impact-metric-card">
                  <div className="impact-metric-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                    </svg>
                  </div>
                  <div className="impact-metric-num">+100</div>
                  <div className="impact-metric-lbl">pessoas atendidas</div>
                </div>


                <div className="impact-metric-card">
                  <div className="impact-metric-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                    </svg>
                  </div>
                  <div className="impact-metric-num">+20</div>
                  <div className="impact-metric-lbl">projetos realizados</div>
                </div>


                <div className="impact-metric-card">
                  <div className="impact-metric-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                    </svg>
                  </div>
                  <div className="impact-metric-num">+50</div>
                  <div className="impact-metric-lbl">voluntários</div>
                </div>


                <div className="impact-metric-card">
                  <div className="impact-metric-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M5 9.2h3V19H5zM10.6 5h2.8v14h-2.8zm5.6 8H19v6h-2.8z" />
                    </svg>
                  </div>
                  <div className="impact-metric-num">+10</div>
                  <div className="impact-metric-lbl">comunidades alcançadas</div>
                </div>

              </div>

            </div>

          </div>

        </div>
      </section>



      <section className="help-section" aria-label="Como ajudar">
        <div className="landing-container">
          
 
          <div className="help-top-row">
            
            <div className="help-top-left">
              <div className="badge-pill badge-pill-pink">
                <span className="badge-icon-heart">♥</span>
                <span>Faça parte</span>
              </div>
              <h2 className="section-headline">
                Como você pode <span className="text-pink-highlight">ajudar?</span>
              </h2>
              <p className="section-subtext">
                Existem várias formas de contribuir com a nossa causa.
              </p>
            </div>

            <div className="help-top-right">
              <div className="help-doodle-juntos doodle-font">
                <span>♡ Juntos</span>
                <span>vamos mais longe</span>
              </div>

              <div className="help-photo-box">
                <img src={helpHeartImg} alt="Mãos segurando coração rosa" />
              </div>

              <div className="help-slogan-vertical doodle-font">
                Fazer<br />
                o bem<br />
                também<br />
                transforma<br />
                você ♡
              </div>
            </div>

          </div>


          <div className="help-cards-grid">
            

            <div className="help-action-card">
              <div className="help-card-left">
                <div className="help-card-icon-box icon-box-pink">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                  </svg>
                </div>
                <div>
                  <h3 className="help-card-title">Faça uma doação</h3>
                  <p className="help-card-desc">
                    Sua contribuição ajuda a manter nossos projetos e alcançar mais pessoas.
                  </p>
                </div>
              </div>
              <Link to="/doar" className="btn-circle-arrow" aria-label="Doar">
                →
              </Link>
            </div>


            <div className="help-action-card">
              <div className="help-card-left">
                <div className="help-card-icon-box icon-box-sky">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                  </svg>
                </div>
                <div>
                  <h3 className="help-card-title">Seja voluntário</h3>
                  <p className="help-card-desc">
                    Doe seu tempo e talento para transformar realidades.
                  </p>
                </div>
              </div>
              <Link to="/contato" className="btn-circle-arrow btn-circle-arrow-sky" aria-label="Ser voluntário">
                →
              </Link>
            </div>


            <div className="help-action-card">
              <div className="help-card-left">
                <div className="help-card-icon-box icon-box-rose">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-3H8v-3h3V7.5l4.5 4.5-4.5 4.5z" />
                  </svg>
                </div>
                <div>
                  <h3 className="help-card-title">Seja um parceiro</h3>
                  <p className="help-card-desc">
                    Empresas e instituições também podem fazer parte dessa causa.
                  </p>
                </div>
              </div>
              <Link to="/contato" className="btn-circle-arrow" aria-label="Ser parceiro">
                →
              </Link>
            </div>

          </div>

        </div>
      </section>



      <section className="testimonials-section" aria-label="Depoimentos">
        <div className="landing-container">
          

          <div className="projects-header-row">
            <div>
              <div className="badge-pill badge-pill-pink">
                <span className="badge-icon-heart">♥</span>
                <span>Histórias reais</span>
              </div>
              <h2 className="section-headline">
                Depoimentos que <span className="text-pink-highlight">inspiram</span>
              </h2>
            </div>

            <div>
              <Link to="/sobre" className="projects-link-all">
                <span>Ver mais depoimentos</span>
                <span>→</span>
              </Link>
            </div>
          </div>


          <div className="testimonials-grid">
            

            <div className="testimonial-card">
              <div>
                <span className="testimonial-quote-icon">“</span>
                <p className="testimonial-quote-text">
                  "A ONG fez toda a diferença na nossa família. Aqui encontramos acolhimento e apoio de verdade."
                </p>
              </div>
              <div className="testimonial-author-box">
                <img src={avatarMaeImg} alt="Mãe de atendido" className="testimonial-avatar" />
                <span className="testimonial-author-name">Mãe de atendido</span>
              </div>
            </div>


            <div className="testimonial-card">
              <div>
                <span className="testimonial-quote-icon">“</span>
                <p className="testimonial-quote-text">
                  "Ser voluntário é uma das melhores experiências da minha vida. A gente recebe muito mais do que dá."
                </p>
              </div>
              <div className="testimonial-author-box">
                <img src={avatarVoluntarioImg} alt="Voluntário" className="testimonial-avatar avatar-border-sky" />
                <span className="testimonial-author-name">Voluntário</span>
              </div>
            </div>

 
            <div className="testimonial-card">
              <div>
                <span className="testimonial-quote-icon">“</span>
                <p className="testimonial-quote-text">
                  "A inclusão muda vidas. E ver esse trabalho de perto me mostra que um mundo melhor é possível."
                </p>
              </div>
              <div className="testimonial-author-box">
                <img src={avatarParceiroImg} alt="Parceiro da ONG" className="testimonial-avatar" />
                <span className="testimonial-author-name">Parceiro da ONG</span>
              </div>
            </div>

          </div>

        </div>
      </section>

      <section className="cta-section" aria-label="Chamada para ação">
        <div className="landing-container">
          
          <div className="cta-banner-wrapper">
            
            <div className="cta-content-left">
              <h2 className="cta-title">
                Juntos podemos transformar mais histórias.
              </h2>
              <p className="cta-subtitle">
                Faça parte dessa rede de cuidado, inclusão e oportunidades.
              </p>
            </div>

            <div>
              <Link to="/doar" className="btn-primary-pink">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                </svg>
                <span>Quero ajudar agora</span>
                <span>→</span>
              </Link>
            </div>


            <div className="cta-right-group">
              <div className="cta-puzzle-icon">
                <svg width="100%" height="100%" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M20.5 11H19V7c0-1.1-.9-2-2-2h-4V3.5C13 2.12 11.88 1 10.5 1S8 2.12 8 3.5V5H4c-1.1 0-1.99.9-1.99 2v3.8H3.5c1.49 0 2.7 1.21 2.7 2.7s-1.21 2.7-2.7 2.7H2V20c0 1.1.9 2 2 2h3.8v-1.5c0-1.49 1.21-2.7 2.7-2.7s2.7 1.21 2.7 2.7V22H17c1.1 0 2-.9 2-2v-4h1.5c1.38 0 2.5-1.12 2.5-2.5s-1.12-2.5-2.5-2.5z" />
                </svg>
              </div>

              <div className="cta-slogan-text doodle-font">
                Inclusão hoje,<br />
                um amanhã<br />
                mais justo
              </div>
            </div>

          </div>

        </div>
      </section>

    </main>
  )
}



