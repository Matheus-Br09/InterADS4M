import { use, useState } from "react"
import './css/auth.css'
import { backend, prepararCsrf } from '../api/backend'

export default function LoginECadastro(){
    const [isLogin, setIsLogin] = useState('')

    const [email, setEmail] = useState('')
    const [password, setPassword] = useState('')
    const [confirmPassword, setConfirmPassword] = useState('')

    const [nome_completo, setNomeCompleto] = useState('')
    const [cpf, setCpf] = useState('');

    const [telefone, setTelefone] = useState('')
    const [cep, setCep] = useState('')
    const [rua, setRua] = useState('')
    const [numero, setNumero] = useState('')
    const [complemento, setComplemento] = useState('')
    const [bairro, setBairro] = useState('')
    const [cidade, setCidade] = useState('')
    const [estado, setEstado] = useState('')

    const [resultado, setResultado] = useState('');

    const [message, setMessage] = useState('')
    const [error, setError] = useState('')

    const validarCPF = async () => {
        try {
            const resposta = await fetch(
                `https://api.invertexto.com/api-validador-cpf-cnpj/${cpf}`
            );

            const dados = await resposta.json();

            if (dados.valido) {
                setResultado("CPF válido!");
            } else {
                setResultado("CPF inválido!");
            }

        } catch (erro) {
            console.log("Erro:", erro);
            setResultado("Erro ao validar CPF.");
        }
    }

    const handleSubmit = async (e) => {
        e.preventDefault()
        setError('')
        setMessage('')

        // Para você ai plebeu do back-end esses dois if's são tratamento de erro

        if (!email || !password){
            setError('Por favor, preencha os campos corretamente.')
            return;
        }

        if (!isLogin && password !== confirmPassword){
            setError('As senhas não coincidem')
            return;
        }

        // aqui que vai acontecer a parada toda pro back pegar os dados

        if (isLogin){
            try{
                await prepararCsrf();
                const resposta = await backend.entrar({email: email, 
                    senha: password,
                })
                console.log('Login realizado com sucesso: ', resposta)
            } catch (err) {
                console.log('Erro ao fazer login: ', err)
                setError('Credenciais inválidas')
            }         

        } else {
            try{
                await prepararCsrf()
                const resposta = await backend.cadastrar({
                    nome_completo: nome_completo,
                    email: email,
                    cpf: cpf,
                    celular: telefone,
                    cep: cep,
                    logradouro: rua,
                    numero: numero,
                    complemento: complemento,
                    bairro: bairro,
                    cidade: cidade,
                    estado: estado,
                    senha: password,
                    senha_confirmation: confirmPassword
                })
            } catch (err) {
                console.log('Erro: ', err)
                return;
            }

            setIsLogin(true)

            
            
        }
    }

    

    return(
        <div className="auth-container">
            <div className="auth-card">
                <div className="auth-tabs">
                    <button
                    className="auth-button"
                    onClick={() => { setIsLogin(true); setError(''); setMessage('')}}>
                        Fazer Login
                    </button>

                    <button
                    className="auth-button"
                    onClick={() => {
                        setIsLogin(false); 
                        setError(''); 
                        setMessage('')}}>
                        Cadastrar Conta
                    </button>
                </div>
                <form action="auth-form" onSubmit={handleSubmit} className="flex flex-col justify-center items-center mb-38.5">

                    <h2>
                        {isLogin ? 'Bem-vindo de volta!' 
                        : 'Crie sua conta'}
                    </h2>

                    {error && <p className="error-msg">{error}</p>}
                    {message && <p className="succes-msg">{message}</p>}

                    <div className="input-group">
                        <label htmlFor="email">E-mail</label>
                        <input 
                        type="email" 
                        id="email"
                        placeholder="seu@email.com"
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}/>
                    </div>

                    <div className="input-group">
                        <label htmlFor="password">Senha</label>
                        <input 
                        type="password" 
                        id="password"
                        placeholder="Sua Senha"
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}/>
                    </div>

                    
                    {!isLogin && (
                        <div>
                           <div className="input-group">
                                <label htmlFor="confirmPassword">Confirmar Senha</label>
                                <input 
                                type="password"
                                id="confirmPassword" 
                                placeholder="Repita Sua Senha"
                                value={confirmPassword}
                                onChange={(e) => setConfirmPassword(e.target.value)}
                                />
                            </div> 
                            <div className="input-group">
                                <label htmlFor="nome_completo">Nome Completo </label>
                                <input type="text" 
                                id="nome_completo"
                                placeholder="Seu nome aqui"
                                value={nome_completo}
                                onChange={(e) => setNomeCompleto(e.target.value)}/>
                            </div>
                            <div className="grid grid-cols-3 gap-4">

                                <div className="input-group items-center">
                                    <label htmlFor="cpf">CPF:</label>
                                    <input  
                                    type="text"
                                    maxLength={14}
                                    placeholder=" 111.222.333-00 "
                                    value={cpf}
                                    onChange={(e)=> setCpf(e.target.value)}
                                    />
                                    <button type='button' onClick={validarCPF} className="auth-button">
                                        validar CPF
                                    </button>
                                    <p>{resultado}</p>
                                </div>

                                <div className="input-group">
                                    <label htmlFor="telefone">Telefone: </label>
                                    <input type="tel" 
                                    name="telefone" id="tel" 
                                    placeholder="(81) 99999-9999"  
                                    value={telefone}
                                    onChange={(e) => {setTelefone(e.target.value)}}/>
                                </div>
                                
                                <div className="input-group">
                                    <label htmlFor="CEP: ">
                                        CEP: 
                                    </label>
                                    <input type="text" 
                                        name="cep" id="cep" 
                                        placeholder="55555-000"
                                        value={cep}
                                        onChange={(e) => {setCep(e.target.value)}}/>
                                </div>

                                <div className="input-group">
                                    <label htmlFor="rua">Rua: </label>
                                    <input type="text" 
                                    name="rua" id="rua"
                                    placeholder="Avenida Paulista"
                                    value={rua}
                                    onChange={(e) => setRua(e.target.value)} />
                                </div>

                                <div className="input-group">
                                    <label htmlFor="numero">N°: </label>
                                    <input type="number" 
                                    placeholder="56" 
                                    value={numero}
                                    onChange={(e) => setNumero(e.target.value)}/>
                                </div>

                                <div className="input-group">
                                    <label htmlFor="Complemento">Complemento: </label>
                                    <input type="text" name="complement" 
                                    id="complement" 
                                    placeholder="Bloco A"
                                    value={complemento}
                                    onChange={(e) => {setComplemento(e.target.value)}}/>
                                </div>

                                <div className="input-group col-span-3 justify-self-center w-full max-w-xs">
                                    <label htmlFor="bairro">Bairro: </label>
                                    <input type="text" name="bairro" 
                                    id="bairro" 
                                    placeholder="Paulista"
                                    value={bairro}
                                    onChange={(e) => {setBairro(e.target.value)}}/>
                                </div>
                             

                            </div>
                            
                        </div>
 
                    )}

                    <button type="submit" className="auth-button">
                        {isLogin ? 'Entrar' : 'Cadastrar'}
                    </button>

                </form>

                

            </div>

            
        </div>
    )
}