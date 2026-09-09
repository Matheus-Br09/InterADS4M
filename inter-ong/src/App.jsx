import { Link, Outlet  } from 'react-router-dom'

import './App.css'

import NavBar from './components/NavBar'


function App() {
    return(
      <div className='bg-amber-500'>

        <NavBar />

        <main>
          <h1 className='text-center text-3xl'>Pagina inicial</h1>
          <Outlet />
        </main>
        
        <div className='text-center bottom-0'>
          <p>Footer</p>
        </div>
      </div>
    )
}

export default App
