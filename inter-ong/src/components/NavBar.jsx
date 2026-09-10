import { Link } from "react-router-dom"

export default function NavBar(){
    return(
        <div>
            <nav className='flex justify-between items-center'>
                <div className='border rounded-2xl p-3'>
                    <Link to={'/'}>A imagem fica aqui</Link>
                </div>

                <div className='flex gap-3'>
                    <Link to={"/doar"}>
                        <p className='border p-3 rounded-2xl'>Doar</p>
                    </Link>

                    <Link to={"/sobre"}>
                        <p className='border p-3 rounded-2xl'>Sobre</p>
                    </Link>

                    <Link to={"/contato"}>
                        <p className='border p-3 rounded-2xl'>Contato</p>
                    </Link>

                    <Link to={""}>
                        <p className='border p-3 rounded-2xl'>Área Educacional</p>
                    </Link>

                </div>
            </nav>
        </div>
        
    )
}