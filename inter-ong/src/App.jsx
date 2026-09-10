import { useState } from 'react'
import { BrowserRouter, Routes, Route, Link, Outlet  } from 'react-router-dom'

import './App.css'
import NavBar from './components/NavBar'


function App() {
    return(
      <div className='bg-amber-500'>
        <NavBar />

        <h1>Pagina inicial</h1>
        <Outlet />
        <p>Footer</p>
      </div>
    )
}

export default App
