import { useState } from "react"
import './css/auth.css'

export default function LoginECadastro(){
    const [isLogin, setIsLogin] = useState('')

    const [email, setEmail] = useState('')
    const [password, setPassword] = useState('')
    const [confirmPassword, setConfirmPassword] = useState('')

    const [cpf, setCpf] = useState('');
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

    const handleSubmit = (e) => {
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
            fetch('NomeDoArquivoLogin.php')
            .then((resposta) => resposta.json())
            .then((dados) => {
                setMessage(dados)
            })
            .catch((error) => setError('O erro foi: ', error))
            console.log('funcionando o login')
        } else {
            fetch('NomeDoArquivoParaCadastro.php')
            .then((resposta) => resposta.json())
            .then((dados) => {
                setMessage(dados)
            })
            .catch((error) => setError('O erro foi: ', error))

            setIsLogin(true)
            setPassword('');
            setConfirmPassword('');
        }
    }

    

    return(
        <div className="auth-container">
            <div className="auth-card">
                <div className="auth-tabs">
                    <button
                    className="auth-button"
                    onClick={() => { setIsLogin(true); setError(''); setMessage('')}}>
                        Entrar
                    </button>

                    <button
                    className="auth-button"
                    onClick={() => {
                        setIsLogin(false); 
                        setError(''); 
                        setMessage('')}}>
                        Cadastrar
                    </button>
                </div>
                <form action="auth-form" onSubmit={handleSubmit}>

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
                                <label htmlFor="cpf">CPF:</label>
                                <input  
                                type="int"
                                maxLength={11}
                                placeholder=" 111.222.333-00 "
                                value={cpf}
                                onChange={(e)=> setCpf(e.target.value)}
                                />
                                <button type='button' onClick={validarCPF} className="auth-button">
                                    validar CPF
                                </button>
                                <p>{resultado}</p>

                            </div>
                        </div>
                        
                        
                        
 
                    )}

                    <button type="submit" className="auth-button">
                        {isLogin ? 'Entar' : 'Cadastrar'}
                    </button>

                </form>

                

            </div>

            
        </div>
    )
}