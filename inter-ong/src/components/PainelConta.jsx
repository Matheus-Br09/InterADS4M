import { useEffect, useState } from 'react'
import { backend } from '../api/backend'
import AvisoApi from './AvisoApi'
import Modal from './ui/Modal'
import Button from './ui/Button'

const tipos = { doacoes: 'Doações únicas', mensalidades: 'Doações mensais', apadrinhamentos: 'Apadrinhamentos' }
const campos = [
  ['nome_completo', 'Nome completo'],
  ['celular', 'Telefone'],
  ['cep', 'CEP'],
  ['logradouro', 'Rua'],
  ['numero', 'Número'],
  ['complemento', 'Complemento'],
  ['bairro', 'Bairro'],
  ['cidade', 'Cidade'],
  ['estado', 'Estado'],
]

export default function PainelConta({ conta, onAtualizar, onErroSessao }) {
  const [resultado, setResultado] = useState(null)
  const [tentativa, setTentativa] = useState(0)
  const [erro, setErro] = useState(null)
  const [mensagem, setMensagem] = useState('')
  const [enviando, setEnviando] = useState(false)
  const [itemParaCancelar, setItemParaCancelar] = useState(null)

  useEffect(() => {
    const controller = new AbortController()
    Promise.all(
      Object.keys(tipos).map(async (tipo) => [
        tipo,
        (await backend.historico(tipo, { signal: controller.signal })).dados,
      ])
    )
      .then((itens) => {
        if (!controller.signal.aborted) setResultado({ tentativa, dados: Object.fromEntries(itens) })
      })
      .catch((error) => {
        if (!controller.signal.aborted) setResultado({ tentativa, erro: error })
      })
    return () => controller.abort()
  }, [tentativa])

  async function executar(acao) {
    if (enviando) return
    setEnviando(true)
    setErro(null)
    setMensagem('')
    try {
      await acao()
    } catch (error) {
      setErro(error)
      if (error.status === 401 || error.codigo === 'troca_senha_obrigatoria') onErroSessao(error)
    } finally {
      setEnviando(false)
    }
  }

  function confirmarCancelamento() {
    if (!itemParaCancelar) return
    const { tipo, id } = itemParaCancelar
    setItemParaCancelar(null)
    executar(async () => {
      await backend.cancelarApoio(tipo, id)
      setTentativa((t) => t + 1)
      setMensagem('Intenção cancelada com sucesso.')
    })
  }

  function salvar(event) {
    event.preventDefault()
    const dados = Object.fromEntries(new FormData(event.currentTarget))
    executar(async () => {
      const resposta = await backend.atualizarConta(dados)
      onAtualizar(resposta.dados)
      setMensagem('Dados atualizados com sucesso.')
    })
  }

  function senha(event) {
    event.preventDefault()
    const form = event.currentTarget
    const dados = Object.fromEntries(new FormData(form))
    executar(async () => {
      await backend.trocarSenha(dados)
      form.reset()
      setMensagem('Senha alterada com sucesso.')
    })
  }

  return (
    <div className="w-full my-6 space-y-6 text-left">
      <AvisoApi erro={erro} mensagem={mensagem} />

      {/* Accordion Meus dados */}
      <details className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm transition-all group">
        <summary className="cursor-pointer font-bold text-slate-800 dark:text-slate-100 flex items-center justify-between list-none">
          <span>👤 Meus dados cadastrais</span>
          <span className="text-pink-600 transition-transform group-open:rotate-180">▼</span>
        </summary>
        <form onSubmit={salvar} className="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
          <fieldset disabled={enviando} className="grid gap-3 sm:grid-cols-2">
            {campos.map(([nome, label]) => (
              <label key={nome} className="block text-xs font-bold text-slate-700 dark:text-slate-300">
                {label}
                <input
                  name={nome}
                  required={nome === 'nome_completo'}
                  defaultValue={conta[nome] || ''}
                  maxLength={255}
                  className="mt-1 w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-sm text-slate-800 dark:text-slate-100 outline-none focus:border-[#fb2782]"
                />
              </label>
            ))}
            <div className="sm:col-span-2 pt-2">
              <Button type="submit" disabled={enviando}>
                {enviando ? 'Salvando…' : 'Salvar dados'}
              </Button>
            </div>
          </fieldset>
        </form>
      </details>

      {/* Accordion Alterar senha */}
      <details className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm transition-all group">
        <summary className="cursor-pointer font-bold text-slate-800 dark:text-slate-100 flex items-center justify-between list-none">
          <span>🔒 Alterar minha senha</span>
          <span className="text-pink-600 transition-transform group-open:rotate-180">▼</span>
        </summary>
        <form onSubmit={senha} className="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
          <fieldset disabled={enviando} className="space-y-3 max-w-md">
            {[
              ['senha_atual', 'Senha atual'],
              ['senha', 'Nova senha'],
              ['senha_confirmation', 'Confirme a nova senha'],
            ].map(([nome, label]) => (
              <label key={nome} className="block text-xs font-bold text-slate-700 dark:text-slate-300">
                {label}
                <input
                  name={nome}
                  type="password"
                  required
                  minLength={nome === 'senha_atual' ? undefined : 8}
                  autoComplete={nome === 'senha_atual' ? 'current-password' : 'new-password'}
                  className="mt-1 w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-2.5 text-sm text-slate-800 dark:text-slate-100 outline-none focus:border-[#fb2782]"
                />
              </label>
            ))}
            <p className="text-xs text-slate-500 dark:text-slate-400">
              Use 8 ou mais caracteres, com maiúscula, minúscula e número.
            </p>
            <Button type="submit" disabled={enviando}>
              {enviando ? 'Alterando…' : 'Alterar senha'}
            </Button>
          </fieldset>
        </form>
      </details>

      {/* Seção Meus apoios */}
      <section className="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-sm">
        <h3 className="text-xl font-bold text-slate-800 dark:text-slate-100 mb-4">
          ♥ Meus apoios e doações
        </h3>
        {resultado?.tentativa !== tentativa ? (
          <p role="status" className="text-sm text-slate-500">
            Carregando histórico…
          </p>
        ) : resultado.erro ? (
          <div className="space-y-3">
            <AvisoApi erro={resultado.erro} />
            <button
              type="button"
              className="text-sm font-bold text-pink-600 underline"
              onClick={() => setTentativa((t) => t + 1)}
            >
              Tentar novamente
            </button>
          </div>
        ) : (
          Object.entries(tipos).map(([tipo, titulo]) => (
            <div key={tipo} className="my-5">
              <h4 className="font-bold text-sm text-slate-700 dark:text-slate-300 border-b border-slate-100 dark:border-slate-800 pb-1">
                {titulo}
              </h4>
              {!resultado.dados[tipo].length && (
                <p className="text-xs text-slate-400 my-2 italic">Nenhum registro encontrado.</p>
              )}
              {resultado.dados[tipo].map((item) => (
                <div
                  key={item.id}
                  className="my-3 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
                >
                  <div>
                    <p className="font-bold text-sm text-slate-800 dark:text-slate-200">
                      Registro #{item.id}
                      {item.crianca && ` · ${item.crianca.nome}`}
                    </p>
                    <p className="text-xs text-slate-500 dark:text-slate-400">
                      Status:{' '}
                      <span className="font-semibold text-slate-700 dark:text-slate-300">
                        {item.status}
                      </span>
                    </p>
                    {(item.valor || item.valor_mensal) && (
                      <p className="text-sm font-extrabold text-[#fb2782]">
                        {Number(item.valor || item.valor_mensal).toLocaleString('pt-BR', {
                          style: 'currency',
                          currency: 'BRL',
                        })}
                      </p>
                    )}
                  </div>
                  {item.status === 'pendente' && (
                    <button
                      type="button"
                      disabled={enviando}
                      className="text-xs font-bold text-red-600 hover:text-red-700 dark:text-red-400 underline cursor-pointer"
                      onClick={() => setItemParaCancelar({ tipo, id: item.id })}
                    >
                      Cancelar intenção
                    </button>
                  )}
                </div>
              ))}
            </div>
          ))
        )}
      </section>

      {/* Modal Acessível de Confirmação de Cancelamento (Substitui window.confirm) */}
      <Modal
        isOpen={Boolean(itemParaCancelar)}
        onClose={() => setItemParaCancelar(null)}
        title="Confirmar cancelamento"
      >
        <div className="space-y-4">
          <p className="text-sm text-slate-600 dark:text-slate-300">
            Tem certeza de que deseja cancelar esta intenção de apoio? Esta ação registrará o
            cancelamento no histórico da sua conta.
          </p>
          <div className="flex items-center justify-end gap-3 pt-2">
            <Button
              variant="outline"
              size="sm"
              onClick={() => setItemParaCancelar(null)}
              disabled={enviando}
            >
              Voltar
            </Button>
            <Button
              variant="danger"
              size="sm"
              onClick={confirmarCancelamento}
              disabled={enviando}
            >
              {enviando ? 'Cancelando…' : 'Sim, cancelar'}
            </Button>
          </div>
        </div>
      </Modal>
    </div>
  )
}
