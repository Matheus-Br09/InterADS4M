import './css/Sobre.css'

export default function Sobre(){
    return(
        <div className="justify-center items-center ">
            <div>
                <div className='title-div'>
                    <h1>Quem Somos</h1>
                </div>
                <section aria-label="Sobre a organização" className='w-4xl p-5 m-auto mb-5 break-normal rounded-2xl shadow-sm shadow-gray'>
                    <p className='text-sobre'>Uma liderança comprometida com a transformação social em Paulista-PE, cuja jornada política nasceu do desejo genuíno de fazer a diferença na vida das famílias que mais precisam de apoio e cuidado.</p> <br/>

                    <p className='text-sobre'>Com coragem e sensibilidade, transformou a vivência ao lado das famílias em uma missão de vida: garantir dignidade, respeito e oportunidades para pessoas com deficiência. Uma voz firme que representa milhares de pessoas que enfrentam diariamente os mesmos desafios.</p> <br/>

                    <p className='text-sobre'>Através do projeto "SOS Tudo pelo Social", trabalhamos para criar uma rede de apoio abrangente que atende desde a primeira infância até a terceira idade, promovendo inclusão, dignidade e qualidade de vida para todos.</p>
                </section>


                <section aria-label="Missão da organização" className='missao-div'>
                    <h2>Missão</h2>
                    <p>"Construir uma sociedade mais justa e inclusiva, onde cada pessoa tenha acesso aos cuidados e oportunidades que merece, independentemente de suas limitações ou condições sociais. Trabalhar incansavelmente para que nenhuma família se sinta desamparada em sua jornada."</p>
                </section>
            </div>
            
            

        </div>
    )
}