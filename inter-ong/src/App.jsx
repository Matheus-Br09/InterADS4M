import { Outlet } from 'react-router-dom'
import NavBar from './components/NavBar.jsx'
import './App.css'

function App() {
  return (
    <div>
      <NavBar />

      <Outlet />
      <p>Footer</p>
    </div>
  )
}

export default App
