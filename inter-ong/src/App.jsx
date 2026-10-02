import { Outlet } from 'react-router-dom'
import NavBar from './components/NavBar.jsx'
import './App.css'
import Footer from './components/Footer.jsx'
import ScrollToTop from './components/ScrollToTop.jsx'

function App() {
  return (
    <div>
      
      <NavBar />
      <ScrollToTop />

      <Outlet />

      <Footer />
    </div>
  )
}

export default App
