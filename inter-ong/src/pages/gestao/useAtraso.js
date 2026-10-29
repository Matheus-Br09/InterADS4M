import { useEffect, useState } from 'react'

// Devolve o valor só depois de ele parar de mudar por `ms` milissegundos
// (evita pedir ao servidor a cada letra digitada na busca).
export default function useAtraso(valor, ms = 300) {
  const [atrasado, setAtrasado] = useState(valor)
  useEffect(() => {
    const espera = setTimeout(() => setAtrasado(valor), ms)
    return () => clearTimeout(espera)
  }, [valor, ms])
  return atrasado
}
