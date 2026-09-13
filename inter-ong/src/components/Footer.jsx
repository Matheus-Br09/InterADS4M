import logoImg from '../assets/logo.png'
import '../pages/css/Footer.css'
import { Link } from 'react-router-dom'

export default function Footer(){
    return(
        <div className="flex bg-blue-950 p-8 text-white justify-between ">
            <div className='w-98'>
                <img src={logoImg} alt="Logo S.O.S" className='w-50 mb-4'/>
                <p>Trabalhando incansavelmente por uma sociedade mais inclusiva, justa e acolheadora para todas as famílias.</p>

                <div className='flex mt-2 gap-3'>
                   <a href="https://www.instagram.com/SosTudopelosocial" target='_blank'>INSTAGRAM</a>

                   <a href="https://mail.google.com/mail/u/0/#inbox?compose=new/sostudopelosocial@gmail.com" target='_blank'>E-MAIL</a> 
                </div>

                
            </div>
            <div className='flex gap-26'>
                <div className='p-2'>
                    <p className='font-bold mb-3.5'>Links Rápidos</p>

                    <p className='link-rapido'><Link to={"/"}>Home</Link> </p>
                    <p className='link-rapido'><Link to={"sobre"}>Sobre</Link> </p>
                    <p className='link-rapido'><Link to={"educacional"}>Área Educacional</Link></p>
                    <p className='link-rapido'><Link to={"contato"}>Contato</Link> </p>

                </div>
                <div className='w-70 p-2'>
                    <p className='font-bold mb-3.5'>Contato</p>

                    <p className='contato-rapido'><a href="https://mail.google.com/mail/u/0/#inbox?compose=new/sostudopelosocial@gmail.com" target='_blank'>sostudopelosocial@gmail.com</a></p>

                    <p className='contato-rapido'><a href="https://www.instagram.com/SosTudopelosocial" target='_blank'>@Sostudopelosocial</a></p>

                    <p className='contato-rapido'>Rua Hosana Alves do Nascimento n°493, Casa 01 Janga, Paulista/PE</p>
                </div>
            </div>
            
        </div>
    )
}