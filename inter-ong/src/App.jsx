import { Outlet } from 'react-router-dom'
import NavBar from './components/NavBar.jsx'
import './App.css'
import Footer from './components/Footer.jsx'
<<<<<<< HEAD
import ScrollToTop from './components/ScrollToTop.jsx'

function App() {
  return (
    <div>
      
      <NavBar />
      <ScrollToTop />

      <Outlet />

      <Footer />
    </div>
=======
import { ThemeProvider } from './context/ThemeContext.jsx'

function App() {
  return (
    <ThemeProvider>
      <div className="min-h-screen bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-100 transition-colors duration-300">
        <NavBar />
        <Outlet />
        <Footer />
      </div>
    </ThemeProvider>
>>>>>>> 6d49191745cf0103f78cca80919b0befce4b66a3
  )
}

export default App
