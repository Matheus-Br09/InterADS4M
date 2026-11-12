import { useTheme } from '../../context/theme.js'
import IconeTema from '../../components/IconeTema.jsx'

export default function BotaoTema() {
  const { isDarkMode, toggleDarkMode } = useTheme()
  const nome = isDarkMode ? 'Mudar para tema claro' : 'Mudar para tema escuro'
  return (
    <button type="button" onClick={toggleDarkMode} aria-label={nome} title={nome}
      className="inline-flex items-center gap-1.5 rounded-full border border-slate-300 bg-white px-3 py-1 text-sm font-semibold hover:bg-slate-100">
      <IconeTema escuro={isDarkMode} />
      {isDarkMode ? 'Claro' : 'Escuro'}
    </button>
  )
}
