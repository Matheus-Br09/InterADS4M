import "./css/Contato.css"

export default function Contato(){
    return (
        <div className="flex items-center justify-center">
            <main className="flex w-full h-156 items-center justify-center m-6 bg-rose-400 rounded-2xl gap-45">
                
                <section aria-label="Informações de contato" className="bg-gray-100 rounded-2xl m-4 p-2.5 shadow-sm shadow-gray-500">
                    <h1 className="sr-only">Contato</h1>

                    <address style={{ fontStyle: "normal" }}>
                        <div className="info-contact" role="group" aria-label="E-mail">
                            <p className="info-name">E-mail da empresa</p>
                            <p>
                                <a href="mailto:amém@gmail.com" aria-label="Enviar e-mail para amém@gmail.com">
                                    amém@gmail.com
                                </a>
                            </p>
                        </div>

                        <div className="info-contact" role="group" aria-label="Telefone">
                            <p className="info-name">Número de Telefone</p>
                            <p>
                                <a href="tel:+559093012931239" aria-label="Ligar para 9093012931239">
                                    9093012931239
                                </a>
                            </p>
                        </div>
                        
                        <div className="info-contact" role="group" aria-label="Endereço">
                            <p className="info-name">Endereço</p>
                            <p>Rua Hosana Alves do Nascimento, n°493</p>
                            <p>Casa 01 - Janga</p>
                            <p>Paulista / PE</p>
                        </div>

                        <div className="info-contact" role="group" aria-label="Instagram">
                            <p className="info-name">Instagram</p>
                            <p>
                                <a
                                    href="https://www.instagram.com/SosTudopelosocial"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    aria-label="Visitar o Instagram @SosTudopelosocial (abre em nova aba)"
                                >
                                    @SosTudopelosocial
                                </a>
                            </p>
                        </div>
                    </address>
                </section>

                <div role="region" aria-label="Mapa da localização da ONG">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3951.5527711809514!2d-34.83390742411175!3d-7.941686879106385!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x7ab3d11fee5da57%3A0x7e7b6c1816d65ae6!2sSOS%20TUDO%20PELO%20SOCIAL!5e0!3m2!1spt-BR!2sbr!4v1789083824825!5m2!1spt-BR!2sbr"
                        title="Mapa com a localização da SOS Tudo pelo Social em Paulista, PE"
                        aria-label="Mapa do Google Maps mostrando a localização da ONG SOS Tudo pelo Social"
                        frameBorder="0"
                        className="rounded-2xl size-125 m-5 shadow-sm shadow-gray-500"
                        allowFullScreen
                        loading="lazy"
                        referrerPolicy="no-referrer-when-downgrade"
                    ></iframe>
                </div>
                
            </main>  
        </div>
        
    )
}