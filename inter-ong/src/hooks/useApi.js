import { useState, useEffect, useRef, useCallback } from 'react'

export function useApiQuery(apiFn, deps = [], options = {}) {
  const [dados, setDados] = useState(options.initialData ?? null)
  const [carregando, setCarregando] = useState(true)
  const [erro, setErro] = useState(null)
  const [tentativa, setTentativa] = useState(0)

  const recarregar = useCallback(() => {
    setTentativa((t) => t + 1)
  }, [])

  useEffect(() => {
    const controller = new AbortController()
    queueMicrotask(() => {
      if (!controller.signal.aborted) {
        setCarregando(true)
        setErro(null)
      }
    })

    apiFn({ signal: controller.signal })
      .then((resposta) => {
        if (!controller.signal.aborted) {
          setDados(resposta?.dados ?? resposta)
          setErro(null)
        }
      })
      .catch((err) => {
        if (!controller.signal.aborted) {
          setErro(err)
        }
      })
      .finally(() => {
        if (!controller.signal.aborted) {
          setCarregando(false)
        }
      })

    return () => controller.abort()
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [tentativa, ...deps])

  return { dados, carregando, erro, recarregar }
}

export function useApiMutation(mutationFn) {
  const [carregando, setCarregando] = useState(false)
  const [erro, setErro] = useState(null)
  const ocupado = useRef(false)

  const mutate = useCallback(
    async (...args) => {
      if (ocupado.current) return
      ocupado.current = true
      setCarregando(true)
      setErro(null)
      try {
        const resultado = await mutationFn(...args)
        return resultado
      } catch (err) {
        setErro(err)
        throw err
      } finally {
        ocupado.current = false
        setCarregando(false)
      }
    },
    [mutationFn]
  )

  return { mutate, carregando, erro }
}
