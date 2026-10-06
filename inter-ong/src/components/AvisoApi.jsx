import { Link } from 'react-router-dom'

export default function AvisoApi({ erro, mensagem }) {
  return <>
    {erro && <div role="alert" className="my-4 rounded-xl bg-red-50 p-4 text-red-800">
      <p>{erro.message}</p>
      {Object.entries(erro.errors || {}).map(([campo, mensagens]) => <p key={campo}>{mensagens.join(' ')}</p>)}
      {(erro.status === 401 || erro.codigo === 'troca_senha_obrigatoria') && <Link to="/login" className="underline">{erro.codigo === 'troca_senha_obrigatoria' ? 'Trocar senha em Minha conta' : 'Entrar ou criar conta'}</Link>}
    </div>}
    {mensagem && <p role="status" className="my-4 rounded-xl bg-green-50 p-4 text-green-900">{mensagem}</p>}
  </>
}
