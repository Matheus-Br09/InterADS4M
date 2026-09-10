import { useState } from 'react'
import { BrowserRouter, Routes, Route, Link, Outlet  } from 'react-router-dom'
import NavBar from './components/NavBar.jsx'

import './App.css'


function App() {
    return(
      <div>
        <NavBar />
        <h1>Pagina inicial</h1>
        <Outlet />
        <p>Footer</p>
      </div>
    )
}

export default App
