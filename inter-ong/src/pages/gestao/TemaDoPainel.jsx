import { Outlet } from 'react-router-dom'
import { ThemeProvider } from '../../context/ThemeContext.jsx'

// Dá ao painel o mesmo tema claro/escuro do site (usa a escolha já salva no navegador).
export default function TemaDoPainel() {
  return <ThemeProvider><Outlet /></ThemeProvider>
}
