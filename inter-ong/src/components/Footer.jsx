import logoImg from '../assets/logo.png'
import '../pages/css/Footer.css'
import { Link } from 'react-router-dom'

export default function Footer(){
    return(
        <footer className="flex bg-blue-950 p-8 text-white justify-between" aria-label="Rodapé do site">
            <div className='w-98'>
                <img src={logoImg} alt="Logo SOS Tudo pelo Social" className='w-50 mb-4'/>
                <p>Trabalhando incansavelmente por uma sociedade mais inclusiva, justa e acolhedora para todas as famílias.</p>

                <nav aria-label="Redes sociais" className='flex mt-2 gap-3'>
                   <a
                     href="https://www.instagram.com/SosTudopelosocial"
                     target='_blank'
                     rel="noopener noreferrer"
                     aria-label="Visitar nosso Instagram (abre em nova aba)"
                   >
                     INSTAGRAM
                   </a>

                   <a
                     href="mailto:sostudopelosocial@gmail.com"
                     aria-label="Enviar e-mail para sostudopelosocial@gmail.com"
                   >
                     E-MAIL
                   </a>
                </nav>

                
            </div>
            <div className='flex gap-26'>
                <nav aria-label="Links rápidos de navegação" className='p-2'>
                    <p className='font-bold mb-3.5' id="quick-links-heading">Links Rápidos</p>

                    <ul role="list" aria-labelledby="quick-links-heading" style={{ listStyle: "none", padding: 0, margin: 0 }}>
                        <li className='link-rapido'><Link to={"/"}>Home</Link></li>
                        <li className='link-rapido'><Link to={"sobre"}>Sobre</Link></li>
                        <li className='link-rapido'><Link to={"educacional"}>Área Educacional</Link></li>
                        <li className='link-rapido'><Link to={"contato"}>Contato</Link></li>
                    </ul>
                </nav>

                <address style={{ fontStyle: "normal" }} className='w-70 p-2'>
                    <p className='font-bold mb-3.5' id="contact-footer-heading">Contato</p>

                    <p className='contato-rapido'>
                        <a
                            href="mailto:sostudopelosocial@gmail.com"
                            aria-label="Enviar e-mail para sostudopelosocial@gmail.com"
                        >
                            sostudopelosocial@gmail.com
                        </a>
                    </p>

                    <p className='contato-rapido'>
                        <a
                            href="https://www.instagram.com/SosTudopelosocial"
                            target='_blank'
                            rel="noopener noreferrer"
                            aria-label="Visitar @Sostudopelosocial no Instagram (abre em nova aba)"
                        >
                            @Sostudopelosocial
                        </a>
                    </p>

                    <p className='contato-rapido'>Rua Hosana Alves do Nascimento n°493, Casa 01 Janga, Paulista/PE</p>
                </address>
            </div>
            
        </footer>
    )
}
