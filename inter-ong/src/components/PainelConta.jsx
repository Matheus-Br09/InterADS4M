import { useEffect, useState } from 'react'
import { backend } from '../api/backend'
import AvisoApi from './AvisoApi'

const tipos = { doacoes: 'Doações únicas', mensalidades: 'Doações mensais', apadrinhamentos: 'Apadrinhamentos' }
const campos = [['nome_completo', 'Nome completo'], ['celular', 'Telefone'], ['cep', 'CEP'], ['logradouro', 'Rua'], ['numero', 'Número'], ['complemento', 'Complemento'], ['bairro', 'Bairro'], ['cidade', 'Cidade'], ['estado', 'Estado']]

export default function PainelConta({ conta, onAtualizar, onErroSessao }) {
  const [resultado, setResultado] = useState(null)
  const [tentativa, setTentativa] = useState(0)
  const [erro, setErro] = useState(null)
  const [mensagem, setMensagem] = useState('')
  const [enviando, setEnviando] = useState(false)

  useEffect(() => {
    const controller = new AbortController()
    Promise.all(Object.keys(tipos).map(async tipo => [tipo, (await backend.historico(tipo, { signal: controller.signal })).dados]))
      .then(itens => { if (!controller.signal.aborted) setResultado({ tentativa, dados: Object.fromEntries(itens) }) })
      .catch(error => { if (!controller.signal.aborted) setResultado({ tentativa, erro: error }) })
    return () => controller.abort()
  }, [tentativa])
  async function executar(acao) {
    if (enviando) return
    setEnviando(true); setErro(null); setMensagem('')
    try { await acao() }
    catch (error) {
      setErro(error)
      if (error.status === 401 || error.codigo === 'troca_senha_obrigatoria') onErroSessao(error)
    } finally { setEnviando(false) }
  }
  function cancelar(tipo, id) {
    if (!window.confirm('Cancelar esta intenção de apoio?')) return
    executar(async () => {
      await backend.cancelarApoio(tipo, id)
      setTentativa(t => t + 1)
      setMensagem('Intenção cancelada.')
    })
  }
  function salvar(event) {
    event.preventDefault()
    const dados = Object.fromEntries(new FormData(event.currentTarget))
    executar(async () => { const resposta = await backend.atualizarConta(dados); onAtualizar(resposta.dados); setMensagem('Dados atualizados.') })
  }
  function senha(event) {
    event.preventDefault()
    const form = event.currentTarget
    const dados = Object.fromEntries(new FormData(form))
    executar(async () => { await backend.trocarSenha(dados); form.reset(); setMensagem('Senha alterada com sucesso.') })
  }
  return <div className="w-full my-6 space-y-6 text-left">
    <AvisoApi erro={erro} mensagem={mensagem} />
    <details><summary className="cursor-pointer font-bold">Meus dados</summary><form onSubmit={salvar}><fieldset disabled={enviando} className="my-4 grid gap-3 sm:grid-cols-2">{campos.map(([nome, label]) => <label key={nome} className="block">{label}<input name={nome} required={nome === 'nome_completo'} defaultValue={conta[nome] || ''} maxLength={255} className="w-full rounded-lg border p-2" /></label>)}<button className="auth-button" type="submit">Salvar dados</button></fieldset></form></details>
    <details><summary className="cursor-pointer font-bold">Alterar minha senha</summary><form onSubmit={senha}><fieldset disabled={enviando} className="my-4 space-y-3">{[['senha_atual', 'Senha atual'], ['senha', 'Nova senha'], ['senha_confirmation', 'Confirme a nova senha']].map(([nome, label]) => <label key={nome} className="block">{label}<input name={nome} type="password" required minLength={nome === 'senha_atual' ? undefined : 8} autoComplete={nome === 'senha_atual' ? 'current-password' : 'new-password'} className="w-full rounded-lg border p-2" /></label>)}<p>Use 8 ou mais caracteres, com maiúscula, minúscula e número.</p><button className="auth-button" type="submit">Alterar senha</button></fieldset></form></details>
    <section><h3 className="text-xl font-bold">Meus apoios</h3>
      {resultado?.tentativa !== tentativa ? <p role="status">Carregando histórico…</p> : resultado.erro ? <><AvisoApi erro={resultado.erro} /><button className="underline" onClick={() => setTentativa(t => t + 1)}>Tentar novamente</button></> : Object.entries(tipos).map(([tipo, titulo]) => <div key={tipo} className="my-4"><h4 className="font-bold">{titulo}</h4>{!resultado.dados[tipo].length && <p>Nenhum registro.</p>}{resultado.dados[tipo].map(item => <div key={item.id} className="my-2 rounded-lg border p-3"><p>Registro #{item.id}{item.crianca && ` · ${item.crianca.nome}`}</p><p>Status: {item.status}</p>{(item.valor || item.valor_mensal) && <p>{Number(item.valor || item.valor_mensal).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' })}</p>}{item.status === 'pendente' && <button disabled={enviando} className="text-red-700 underline" onClick={() => cancelar(tipo, item.id)}>Cancelar intenção</button>}</div>)}</div>)}
    </section>
  </div>
}
