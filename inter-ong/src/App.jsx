import Newsletter from './components/Newsletter.jsx'
import { Outlet } from 'react-router-dom'
import NavBar from './components/NavBar.jsx'
import './App.css'
import Footer from './components/Footer.jsx'
import ScrollToTop from './components/ScrollToTop.jsx'
import { ThemeProvider } from './context/ThemeContext.jsx'
import { ConfigProvider } from './context/ConfigContext.jsx'
import BotaoWhatsapp from './components/BotaoWhatsapp.jsx'


function App() {
  return (
    <ThemeProvider>
      <ConfigProvider>
      <div className="min-h-screen bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 transition-colors duration-300">
        <NavBar />
        <ScrollToTop />
        <Outlet />
        <Newsletter />
        <Footer />
        <BotaoWhatsapp />
      </div>
      </ConfigProvider>
    </ThemeProvider>

  )
}

export default App
