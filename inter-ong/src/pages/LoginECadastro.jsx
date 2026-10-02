import { useEffect, useRef, useState } from 'react'
import { Link, NavLink } from 'react-router-dom'
import { backend } from '../api/backend'
import './css/auth.css'

const cadastroVazio = {
  nome_completo: '', cpf: '', celular: '', cep: '', logradouro: '',
  numero: '', complemento: '', bairro: '', cidade: '', estado: '',
}
const camposCadastro = [
  ['nome_completo', 'Nome completo', 'text', 'name', true],
  ['cpf', 'CPF', 'text', 'off', true],
  ['celular', 'Telefone', 'tel', 'tel'],
  ['cep', 'CEP', 'text', 'postal-code'],
  ['logradouro', 'Rua', 'text', 'address-line1'],
  ['numero', 'Número', 'text', 'off'],
  ['complemento', 'Complemento', 'text', 'address-line2'],
  ['bairro', 'Bairro', 'text', 'off'],
  ['cidade', 'Cidade', 'text', 'address-level2'],
  ['estado', 'Estado', 'text', 'address-level1'],
]

export default function LoginECadastro() {
  const [isLogin, setIsLogin] = useState(true)
  const [conta, setConta] = useState(null)
  const [trocaObrigatoria, setTrocaObrigatoria] = useState(false)
  const [carregando, setCarregando] = useState(true)
  const [enviando, setEnviando] = useState(false)
  const emAndamento = useRef(false)
  const [email, setEmail] = useState('')
  const [senha, setSenha] = useState('')
  const [confirmacao, setConfirmacao] = useState('')
  const [cadastro, setCadastro] = useState(cadastroVazio)
  const [erro, setErro] = useState('')
  const [errosCampos, setErrosCampos] = useState({})
  const [mensagem, setMensagem] = useState('')

  useEffect(() => {
    const controller = new AbortController()
    backend.conta({ signal: controller.signal }).then(({ dados }) => {
      setConta(dados)
      setTrocaObrigatoria(dados.trocar_senha_obrigatorio)
    }).catch((error) => {
      if (controller.signal.aborted) return
      if (error.codigo === 'troca_senha_obrigatoria') setTrocaObrigatoria(true)
      else if (error.status !== 401) setErro(error.message)
    }).finally(() => {
      if (!controller.signal.aborted) setCarregando(false)
    })
    return () => controller.abort()
  }, [])

  function limparAvisos() {
    setErro('')
    setErrosCampos({})
    setMensagem('')
  }

  function mostrarErro(error) {
    setErro(error.message)
    setErrosCampos(error.errors || {})
    if (error.status === 401) {
      setConta(null)
      setTrocaObrigatoria(false)
      setIsLogin(true)
    }
    if (error.codigo === 'troca_senha_obrigatoria') setTrocaObrigatoria(true)
  }

  async function enviar(event) {
    event.preventDefault()
    if (emAndamento.current) return
    limparAvisos()
    if ((!isLogin || trocaObrigatoria) && senha !== confirmacao) {
      setErro('As senhas não coincidem.')
      return
    }
    emAndamento.current = true
    setEnviando(true)
    try {
      if (trocaObrigatoria) {
        await backend.trocarSenha({ senha, senha_confirmation: confirmacao })
        const { dados } = await backend.conta()
        setConta(dados)
        setTrocaObrigatoria(false)
        setMensagem('Senha alterada com sucesso.')
      } else {
        const { dados } = isLogin
          ? await backend.entrar({ email, senha })
          : await backend.cadastrar({ ...cadastro, email, senha, senha_confirmation: confirmacao })
        setConta(dados)
        setTrocaObrigatoria(dados.trocar_senha_obrigatorio)
        setCadastro(cadastroVazio)
        setMensagem(isLogin ? 'Login realizado com sucesso.' : 'Conta criada com sucesso.')
      }
      setSenha('')
      setConfirmacao('')
    } catch (error) {
      mostrarErro(error)
    } finally {
      emAndamento.current = false
      setEnviando(false)
    }
  }

  async function sair() {
    if (emAndamento.current) return
    limparAvisos()
    emAndamento.current = true
    setEnviando(true)
    try {
      await backend.sair()
      setConta(null)
      setTrocaObrigatoria(false)
      setIsLogin(true)
      setEmail('')
      setSenha('')
      setConfirmacao('')
      setMensagem('Você saiu da sua conta.')
    } catch (error) {
      mostrarErro(error)
    } finally {
      emAndamento.current = false
      setEnviando(false)
    }
  }

  function alternar(login) {
    setIsLogin(login)
    setSenha('')
    setConfirmacao('')
    limparAvisos()
  }

  const erroCampo = (campo) => errosCampos[campo] && (
    <span id={`${campo}-erro`} className="error-msg">{errosCampos[campo].join(' ')}</span>
  )
  const acessibilidade = (campo) => ({
    'aria-invalid': Boolean(errosCampos[campo]),
    'aria-describedby': errosCampos[campo] ? `${campo}-erro` : undefined,
  })

  return (
    <div className="auth-container">
      <section className="auth-card" aria-label="Acesso à conta" aria-busy={carregando || enviando}>
        {erro && <p className="error-msg" role="alert">{erro}</p>}
        {/* {mensagem && <p className="success-msg" role="status">{mensagem}</p>} */}
        {carregando ? <p role="status">Verificando sua sessão…</p> : conta && !trocaObrigatoria ? (
          <div className='flex flex-col items-center'>
            <h2>Minha conta</h2>
            <p className='m-3'>Bem-vindo(a), {conta.nome_completo}!</p>
            <div className='flex gap-3 justify-center items-center m-2'>
              <p>Quer ser um voluntário?</p>
              <NavLink to="/voluntariado"><button className='auth-click rounded-2xl'>Clique Aqui</button></NavLink> 
            </div>
            <div className='flex gap-3 justify-center items-center m-2'>
              <p>Gostaria de doar?</p>
              <NavLink to="/doar"><button className='auth-click-2 rounded-2xl'>Clique Aqui</button></NavLink>
            </div>
            
            {conta.tipo_usuario === 'admin' && <p className="my-4"><Link to="/gestao/materiais" className="auth-button">Gerenciar materiais educativos</Link></p>}
            <button className="auth-button" disabled={enviando} onClick={sair}>Sair da conta</button>
          </div>
        ) : (
          <>
            {!trocaObrigatoria && (
              <div className="auth-tabs">
                <button className="auth-button" disabled={enviando} aria-pressed={isLogin} onClick={() => alternar(true)}>Fazer Login</button>
                <button className="auth-button" disabled={enviando} aria-pressed={!isLogin} onClick={() => alternar(false)}>Cadastrar Conta</button>
              </div>
            )}
            <h2>{trocaObrigatoria ? 'Crie uma nova senha' : isLogin ? 'Bem-vindo de volta!' : 'Crie sua conta'}</h2>
            {trocaObrigatoria && <p>Para continuar, substitua a senha temporária por uma senha sua.</p>}
            <form onSubmit={enviar}>
              <fieldset disabled={enviando} className="auth-fields">
                {!trocaObrigatoria && (
                  <div className="input-group">
                    <label htmlFor="email">E-mail</label>
                    <input id="email" type="email" autoComplete="username" required maxLength={255} value={email} onChange={(e) => setEmail(e.target.value)} {...acessibilidade('email')} />
                    {erroCampo('email')}
                  </div>
                )}
                {!isLogin && !trocaObrigatoria && (
                  <div className="auth-registration">
                    {camposCadastro.map(([campo, label, type, autoComplete, required]) => (
                      <div className="input-group" key={campo}>
                        <label htmlFor={campo}>{label}{required ? ' *' : ''}</label>
                        <input id={campo} type={type} autoComplete={autoComplete} required={required} maxLength={campo === 'cpf' ? 14 : 255} value={cadastro[campo]} onChange={(e) => setCadastro({ ...cadastro, [campo]: e.target.value })} {...acessibilidade(campo)} />
                        {erroCampo(campo)}
                      </div>
                    ))}
                  </div>
                )}
                <div className="input-group">
                  <label htmlFor="senha">{trocaObrigatoria ? 'Nova senha' : 'Senha'}</label>
                  <input id="senha" type="password" autoComplete={isLogin && !trocaObrigatoria ? 'current-password' : 'new-password'} required minLength={!isLogin || trocaObrigatoria ? 8 : undefined} value={senha} onChange={(e) => setSenha(e.target.value)} {...acessibilidade('senha')} />
                  {erroCampo('senha')}
                </div>
                {(!isLogin || trocaObrigatoria) && (
                  <>
                    <p>Use pelo menos 8 caracteres, com letra maiúscula, minúscula e número.</p>
                    <div className="input-group">
                      <label htmlFor="senha_confirmation">Confirmar senha</label>
                      <input id="senha_confirmation" type="password" autoComplete="new-password" required value={confirmacao} onChange={(e) => setConfirmacao(e.target.value)} {...acessibilidade('senha_confirmation')} />
                      {erroCampo('senha_confirmation')}
                    </div>
                  </>
                )}
                <button type="submit" className="auth-button">{enviando ? 'Aguarde…' : trocaObrigatoria ? 'Salvar nova senha' : isLogin ? 'Entrar' : 'Cadastrar'}</button>
              </fieldset>
            </form>
            {trocaObrigatoria && <button className="auth-button" disabled={enviando} onClick={sair}>Sair da conta</button>}
          </>
        )}
      </section>
    </div>
  )
}
