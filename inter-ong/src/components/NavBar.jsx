import { useState } from "react"
import { NavLink, Link } from "react-router-dom"
import logoImg from "../assets/logo.png"

export default function NavBar() {
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false)

  const toggleMobileMenu = () => {
    setIsMobileMenuOpen((prev) => !prev)
  }

  const closeMobileMenu = () => {
    setIsMobileMenuOpen(false)
  }


  const desktopNavLinkClass = ({ isActive }) =>
    `px-4 py-2 rounded-full text-sm font-semibold transition-all duration-200 border ${
      isActive
        ? "bg-white text-[#fb2782] shadow-sm border-pink-200"
        : "text-gray-700 border-transparent hover:text-[#fb2782] hover:bg-white/60"
    }`


  const mobileNavLinkClass = ({ isActive }) =>
    `flex items-center justify-between px-4 py-3 rounded-2xl text-base font-semibold transition-all duration-200 ${
      isActive
        ? "bg-white text-[#fb2782] shadow-sm border border-pink-200"
        : "text-gray-700 hover:text-[#fb2782] hover:bg-white/60"
    }`

  return (
    <header className="sticky top-0 z-50 w-full">

      <div
        className="w-full shadow-lg shadow-pink-500/10 rounded-b-[2rem] sm:rounded-b-[2.5rem] border-b border-pink-100/60 backdrop-blur-md"
        style={{
          background:
            "linear-gradient(180deg, #ff7096 0%, #ff98b7 32%, #ffd5e4 70%, #fff2f6 92%, #ffffff 100%)",
        }}
      >
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between h-20 sm:h-24">
            
            <Link
              to="/"
              onClick={closeMobileMenu}
              className="flex items-center group transition-transform duration-200 hover:scale-105 active:scale-95"
              aria-label="SOS Tudo pelo Social - Página Inicial"
            >
              <div className="px-4 py-2  bg-white/60 background-blur border-gradiente border-white/60 rounded-full ">
              <img
                src={logoImg}
                alt="SOS Tudo pelo social"
                className="h-9 sm:h-11 md:h-12 w-auto object-contain drop-shadow-sm "
              />
           </div>
           </Link>


            <nav className="hidden md:flex items-center gap-2 lg:gap-3 bg-white/40 backdrop-blur-sm p-1.5 rounded-full border border-white/60 shadow-inner">
              <NavLink to="/" className={desktopNavLinkClass} end>
                Início
              </NavLink>

              <NavLink to="/sobre" className={desktopNavLinkClass}>
                Sobre
              </NavLink>

              <NavLink to="/contato" className={desktopNavLinkClass}>
                Contato
              </NavLink>

              <NavLink to="/educacional" className={desktopNavLinkClass}>
                Área Educacional
              </NavLink>
            </nav>

            <div className="hidden md:flex items-center gap-3">
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
                className="p-2.5 rounded-2xl bg-white/70 text-gray-700 hover:text-[#fb2782] hover:bg-white border border-pink-200/50 shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[#fb2782]/40"
                aria-label={isMobileMenuOpen ? "Fechar menu" : "Abrir menu"}
                aria-expanded={isMobileMenuOpen}
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
          className={`md:hidden overflow-hidden transition-all duration-300 ease-in-out ${
            isMobileMenuOpen ? "max-h-96 opacity-100 pb-5" : "max-h-0 opacity-0 pb-0"
          }`}
        >
          <div className="px-4 sm:px-6 pt-2 space-y-1.5">
            <div className="p-3 bg-white/60 backdrop-blur-md rounded-2xl border border-white/80 shadow-sm space-y-1">
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