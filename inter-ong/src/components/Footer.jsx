import logoImg from '../assets/logo.png'
import '../pages/css/Footer.css'
import { Link } from 'react-router-dom'
import { useConfig } from '../context/ConfigContext.jsx'
import { linkInstagram } from '../utils/instagram.js'

const LINKS = [
    ['/', 'Home'],
    ['/contato', 'Contato'],
    ['/sobre', 'Sobre'],
    ['/programas', 'Programas'],
    ['/educacional', 'Área Educacional'],
    ['/transparencia', 'Transparência'],
]

const pilula = 'inline-flex items-center rounded-full border border-slate-700 bg-slate-900/60 px-4 py-1.5 text-sm font-semibold transition hover:border-[#fb2782] hover:text-[#ff66a3]'
const titulo = 'mb-3.5 font-bold text-[#ff66a3]'

export default function Footer(){
    const instagram = linkInstagram(useConfig()?.instagram)
    return(
        <footer className="text-white" aria-label="Rodapé do site">
            <div className="h-1 bg-gradient-to-r from-[#fb2782] via-[#ff98b7] to-[#3bb4ee]" aria-hidden="true" />

            <div className="mx-auto max-w-6xl px-6 py-10">
                <div className="grid gap-10 md:grid-cols-[1.3fr_1fr_1.2fr]">
                    <div>
                        <img src={logoImg} alt="Logo SOS Tudo pelo Social" className='mb-4 w-44'/>
                        <p className="max-w-sm text-slate-300">Trabalhando incansavelmente por uma sociedade mais inclusiva, justa e acolhedora para todas as famílias.</p>

                        <nav aria-label="Redes sociais" className='mt-4 flex flex-wrap gap-3'>
                            <a
                                href={instagram}
                                target='_blank'
                                rel="noopener noreferrer"
                                aria-label="Visitar nosso Instagram (abre em nova aba)"
                                className={pilula}
                            >
                                Instagram
                            </a>

                            <a
                                href="mailto:sostudopelosocial@gmail.com"
                                aria-label="Enviar e-mail para sostudopelosocial@gmail.com"
                                className={pilula}
                            >
                                E-mail
                            </a>
                        </nav>
                    </div>

                    <nav aria-label="Links rápidos de navegação">
                        <p className={titulo} id="quick-links-heading">Links Rápidos</p>

                        <ul role="list" aria-labelledby="quick-links-heading" className="grid grid-cols-2 gap-x-6" style={{ listStyle: "none", padding: 0, margin: 0 }}>
                            {LINKS.map(([caminho, nome]) => (
                                <li key={caminho} className='link-rapido'><Link to={caminho} className="text-slate-300 hover:text-white">{nome}</Link></li>
                            ))}
                        </ul>
                    </nav>

                    <address style={{ fontStyle: "normal" }}>
                        <p className={titulo} id="contact-footer-heading">Contato</p>

                        <p className='contato-rapido'>
                            <a
                                href="mailto:sostudopelosocial@gmail.com"
                                aria-label="Enviar e-mail para sostudopelosocial@gmail.com"
                                className="break-all text-slate-300 hover:text-white"
                            >
                                sostudopelosocial@gmail.com
                            </a>
                        </p>

                        <p className='contato-rapido'>
                            <a
                                href={instagram}
                                target='_blank'
                                rel="noopener noreferrer"
                                aria-label="Visitar @Sostudopelosocial no Instagram (abre em nova aba)"
                                className="text-slate-300 hover:text-white"
                            >
                                @Sostudopelosocial
                            </a>
                        </p>

                        <p className='contato-rapido text-slate-300'>Rua Hosana Alves do Nascimento n°493, Casa 01 Janga, Paulista/PE</p>
                    </address>
                </div>

                <div className="mt-10 flex flex-wrap items-center justify-between gap-2 border-t border-slate-800 pt-5 text-sm text-slate-400">
                    <span>© {new Date().getFullYear()} SOS Tudo pelo Social</span>
                    <Link to="/privacidade" className="hover:text-white">Política de privacidade</Link>
                </div>
            </div>
        </footer>
    )
}
