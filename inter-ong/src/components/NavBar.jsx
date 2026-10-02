import { useState } from "react"
import { NavLink, Link } from "react-router-dom"
import logoImg from "../assets/logo.png"
import { useTheme } from "../context/ThemeContext.jsx"

export default function NavBar() {
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false)
  const { isDarkMode, toggleDarkMode } = useTheme()

  const toggleMobileMenu = () => {
    setIsMobileMenuOpen((prev) => !prev)
  }

  const closeMobileMenu = () => {
    setIsMobileMenuOpen(false)
  }


  const desktopNavLinkClass = ({ isActive }) =>
    `px-4 py-2 rounded-full text-sm font-semibold transition-all duration-200 border ${
      isActive
        ? "bg-white dark:bg-slate-800 text-[#fb2782] dark:text-pink-400 shadow-sm border-pink-200 dark:border-pink-900/50"
        : "text-gray-700 dark:text-slate-200 border-transparent hover:text-[#fb2782] dark:hover:text-pink-400 hover:bg-white/60 dark:hover:bg-slate-800/60"
    }`


  const mobileNavLinkClass = ({ isActive }) =>
    `flex items-center justify-between px-4 py-3 rounded-2xl text-base font-semibold transition-all duration-200 ${
      isActive
        ? "bg-white dark:bg-slate-800 text-[#fb2782] dark:text-pink-400 shadow-sm border border-pink-200 dark:border-pink-900/50"
        : "text-gray-700 dark:text-slate-200 hover:text-[#fb2782] dark:hover:text-pink-400 hover:bg-white/60 dark:hover:bg-slate-800/60"
    }`

  return (
    <header className="sticky top-0 z-50 w-full">

      <div
        className="navbar-bg-gradient w-full shadow-lg shadow-pink-500/10 rounded-b-[2rem] sm:rounded-b-[2.5rem] border-b border-pink-100/60 dark:border-slate-800/60 backdrop-blur-md"
      >
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between h-20 sm:h-24">
            
            <Link
              to="/"
              onClick={closeMobileMenu}
              className="flex items-center group transition-transform duration-200 hover:scale-105 active:scale-95"
              aria-label="SOS Tudo pelo Social - Página Inicial"
            >
              <div className="px-4 py-2 bg-white/60 dark:bg-slate-900/60 backdrop-blur border border-white/60 dark:border-slate-700/60 rounded-full">
              <img
                src={logoImg}
                alt="SOS Tudo pelo social"
                className="h-9 sm:h-11 md:h-12 w-auto object-contain drop-shadow-sm"
              />
           </div>
           </Link>


            <nav aria-label="Navegação principal" className="hidden md:flex items-center gap-2 lg:gap-3 bg-white/40 dark:bg-slate-900/40 backdrop-blur-sm p-1.5 rounded-full border border-white/60 dark:border-slate-800 shadow-inner">
              <NavLink to="/" className={desktopNavLinkClass} end>
                Início
              </NavLink>

              <NavLink to="/sobre" className={desktopNavLinkClass}>
                Sobre Nós
              </NavLink>

              <NavLink to="/contato" className={desktopNavLinkClass}>
                Contato
              </NavLink>

              <NavLink to="/educacional" className={desktopNavLinkClass}>
                Área Educacional
              </NavLink>

              <NavLink to="/login" className={desktopNavLinkClass}>
                Login / Cadastro
              </NavLink>

            </nav>

            <div className="hidden md:flex items-center gap-3">
              {/* Botão de Alternância de Tema (Claro / Escuro) */}
              <button
                type="button"
                onClick={toggleDarkMode}
                className="inline-flex items-center gap-2 px-3.5 py-2 rounded-full text-xs font-bold transition-all duration-200 border cursor-pointer shadow-xs bg-white/80 dark:bg-slate-800/90 text-slate-700 dark:text-slate-200 border-pink-200/70 dark:border-slate-700 hover:scale-105 active:scale-95"
                title={isDarkMode ? "Mudar para Tema Claro" : "Mudar para Tema Escuro"}
                aria-label={isDarkMode ? "Mudar para Tema Claro" : "Mudar para Tema Escuro"}
              >
                {isDarkMode ? (
                  <>
                    <svg className="w-4 h-4 fill-amber-400 text-amber-400" viewBox="0 0 24 24">
                      <path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1zM5.99 4.58c-.39-.39-1.03-.39-1.41 0s-.39 1.03 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41L5.99 4.58zm12.37 12.37c-.39-.39-1.03-.39-1.41 0s-.39 1.03 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41l-1.06-1.06zm1.06-10.96c.39-.39.39-1.03 0-1.41s-1.03-.39-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06zM7.05 18.36c.39-.39.39-1.03 0-1.41s-1.03-.39-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06z" />
                    </svg>
                    <span>Tema Claro</span>
                  </>
                ) : (
                  <>
                    <svg className="w-4 h-4 fill-slate-700 text-slate-700" viewBox="0 0 24 24">
                      <path d="M12.3 2a10 10 0 0 0 9.7 11.5 10 10 0 1 1-11.5-9.7c.6 0 1.2.1 1.8.2z" />
                    </svg>
                    <span>Tema Escuro</span>
                  </>
                )}
              </button>

              <NavLink
                to="/doar"
                className="group relative inline-flex items-center gap-2 px-6 py-2.5 rounded-full text-white font-bold text-sm tracking-wide bg-gradient-to-r from-[#fb2782] via-[#ff3b8d] to-[#ff6398] shadow-md shadow-pink-500/30 hover:shadow-lg hover:shadow-pink-500/45 hover:scale-105 active:scale-95 transition-all duration-200 overflow-hidden"
              >
                <span className="relative z-10 flex items-center gap-2">
                  <svg
                    className="w-4 h-4 fill-current transition-transform duration-300 group-hover:scale-125"
                    viewBox="0 0 24 24"
                  >
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                  </svg>
                  Doar Agora
                </span>
                <span className="absolute inset-0 bg-white/20 opacity-0 group-hover:opacity-100 transition-opacity duration-200" />
              </NavLink>
            </div>

            <div className="flex md:hidden items-center gap-2">
              {/* Botão de Tema Mobile */}
              <button
                type="button"
                onClick={toggleDarkMode}
                className="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold bg-white/80 dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-pink-200/50 dark:border-slate-700 shadow-xs"
                aria-label={isDarkMode ? "Mudar para Tema Claro" : "Mudar para Tema Escuro"}
              >
                {isDarkMode ? "☀️ Claro" : "🌙 Escuro"}
              </button>

              <NavLink
                to="/doar"
                onClick={closeMobileMenu}
                className="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-white font-bold text-xs bg-gradient-to-r from-[#fb2782] to-[#ff4785] shadow-sm shadow-pink-500/30 active:scale-95 transition-transform"
              >
                <svg className="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                  <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                </svg>
                Doar
              </NavLink>


              <button
                type="button"
                onClick={toggleMobileMenu}
                className="p-2.5 rounded-2xl bg-white/70 dark:bg-slate-800/80 text-gray-700 dark:text-slate-200 hover:text-[#fb2782] hover:bg-white border border-pink-200/50 dark:border-slate-700 shadow-sm transition-all duration-200 focus:outline-none"
                aria-label={isMobileMenuOpen ? "Fechar menu" : "Abrir menu de navegação"}
                aria-expanded={isMobileMenuOpen}
                aria-controls="mobile-nav-menu"
              >
                <div className="w-5 h-4 flex flex-col justify-between items-center relative">
                  <span
                    className={`block h-0.5 w-5 bg-current rounded-full transition-transform duration-300 ease-in-out ${
                      isMobileMenuOpen ? "rotate-45 translate-y-1.5" : ""
                    }`}
                  />
                  <span
                    className={`block h-0.5 w-5 bg-current rounded-full transition-opacity duration-200 ${
                      isMobileMenuOpen ? "opacity-0" : "opacity-100"
                    }`}
                  />
                  <span
                    className={`block h-0.5 w-5 bg-current rounded-full transition-transform duration-300 ease-in-out ${
                      isMobileMenuOpen ? "-rotate-45 -translate-y-2" : ""
                    }`}
                  />
                </div>
              </button>
            </div>

          </div>
        </div>


        <div
          id="mobile-nav-menu"
          role="navigation"
          aria-label="Menu de navegação mobile"
          className={`md:hidden overflow-hidden transition-all duration-300 ease-in-out ${
            isMobileMenuOpen ? "max-h-96 opacity-100 pb-5" : "max-h-0 opacity-0 pb-0"
          }`}
        >
          <div className="px-4 sm:px-6 pt-2 space-y-1.5">
            <div className="p-3 bg-white/90 dark:bg-slate-900/95 backdrop-blur-md rounded-2xl border border-white/80 dark:border-slate-800 shadow-lg space-y-1">
              <NavLink to="/" onClick={closeMobileMenu} className={mobileNavLinkClass} end>
                <span>Início</span>
                <span className="text-xs opacity-60">→</span>
              </NavLink>

              <NavLink to="/sobre" onClick={closeMobileMenu} className={mobileNavLinkClass}>
                <span>Sobre Nós</span>
                <span className="text-xs opacity-60">→</span>
              </NavLink>

              <NavLink to="/contato" onClick={closeMobileMenu} className={mobileNavLinkClass}>
                <span>Contato</span>
                <span className="text-xs opacity-60">→</span>
              </NavLink>

              <NavLink to="/educacional" onClick={closeMobileMenu} className={mobileNavLinkClass}>
                <span>Área Educacional</span>
                <span className="text-xs opacity-60">→</span>
              </NavLink>

              <div className="pt-2">
                <NavLink
                  to="/doar"
                  onClick={closeMobileMenu}
                  className="flex items-center justify-center gap-2 w-full py-3 rounded-xl text-white font-bold text-sm bg-gradient-to-r from-[#fb2782] to-[#ff4785] shadow-md shadow-pink-500/25 active:scale-95 transition-transform"
                >
                  <svg className="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                  </svg>
                  Faça sua Doação
                </NavLink>
              </div>
            </div>
          </div>
        </div>

      </div>


      {isMobileMenuOpen && (
        <div
          onClick={closeMobileMenu}
          className="fixed inset-0 top-20 sm:top-24 bg-black/20 backdrop-blur-[2px] z-[-1] md:hidden transition-opacity"
          aria-hidden="true"
        />
      )}
    </header>
  )
}