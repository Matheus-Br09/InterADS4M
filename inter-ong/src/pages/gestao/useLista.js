import { useCallback, useEffect, useState } from 'react'
import { normalizarLista } from '../../utils/gestao.js'

// Carrega uma página de uma lista do painel e permite recarregar.
// `chave` identifica o filtro: ao mudar, volta para a página 1.
export default function useLista(carregar, chave = '') {
  const [estado, setEstado] = useState({ itens: [], pagina: 1, ultima: 1, carregando: true, erro: null })
  const [versao, setVersao] = useState(0)
  const [pedido, setPedido] = useState({ chave, pagina: 1 })

  // Trocar o filtro reinicia na página 1 (ajuste de estado durante a renderização).
  if (pedido.chave !== chave) setPedido({ chave, pagina: 1 })
  const pagina = pedido.chave === chave ? pedido.pagina : 1

  useEffect(() => {
    const controller = new AbortController()
    carregar(pagina, { signal: controller.signal })
      .then(({ dados }) => {
        if (controller.signal.aborted) return
        setEstado({ ...normalizarLista(dados), carregando: false, erro: null })
      })
      .catch((erro) => { if (!controller.signal.aborted) setEstado((e) => ({ ...e, carregando: false, erro })) })
    return () => controller.abort()
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [pagina, versao, chave])

  const setPagina = useCallback((p) => setPedido({ chave, pagina: p }), [chave])
  const recarregar = useCallback(() => setVersao((v) => v + 1), [])
  return { ...estado, setPagina, recarregar }
}
