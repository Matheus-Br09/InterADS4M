import "./css/Contato.css"

export default function Contato(){
    return (
        <div className="flex items-center justify-center">
            <main className="flex w-326 h-156 items-center justify-center m-6 bg-rose-400 rounded-2xl gap-45">
                <div className="bg-gray-100 rounded-2xl m-4 p-2.5">
                    <div className="info-contact">
                        <p className="info-name">imeiu da impresa</p>
                        <p>caceta@gmail.com</p>
                    </div>

                    <div className="info-contact">
                        <p className="info-name">Número de Telefone</p>
                        <p>9093012931239</p>
                    </div>
                    
                    <div className="info-contact">
                        <p className="info-name">Endereço</p>
                        <p>Rua Hosana Alves do Nascimento, n°493</p>
                        <p>Casa 01 - Janga</p>
                        <p>Paulista / PE</p>
                    </div>

                    <div className="info-contact">
                        <p className="info-name">Instagram</p>
                        <p>@SosTudopelosocial</p>
                    </div>
                    
                </div> 
                <div>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3951.5527711809514!2d-34.83390742411175!3d-7.941686879106385!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x7ab3d11fee5da57%3A0x7e7b6c1816d65ae6!2sSOS%20TUDO%20PELO%20SOCIAL!5e0!3m2!1spt-BR!2sbr!4v1789083824825!5m2!1spt-BR!2sbr" frameborder="0" className="rounded-2xl size-125 m-5"></iframe>
                </div>
                
            </main>  
        </div>
        
    )
}