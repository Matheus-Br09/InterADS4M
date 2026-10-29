import { createContext, useContext } from 'react'
import { backend } from '../api/backend'
import { useApiQuery } from '../hooks/useApi'

const ConfigContext = createContext(null)

export function ConfigProvider({ children }) {
  // Se a API falhar, a configuração fica null e o site usa os valores padrão.
  const { dados } = useApiQuery((options) => backend.configPublica(options), [])
  return <ConfigContext.Provider value={dados ?? null}>{children}</ConfigContext.Provider>
}

// eslint-disable-next-line react-refresh/only-export-components
export function useConfig() {
  return useContext(ConfigContext)
}
