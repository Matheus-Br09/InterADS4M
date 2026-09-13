import { Outlet } from 'react-router-dom'
import NavBar from './components/NavBar.jsx'
import './App.css'
import Footer from './components/Footer.jsx'

function App() {
  return (
    <div>
      
      <NavBar />

      <Outlet />

      <Footer />
    </div>
  )
}

export default App
