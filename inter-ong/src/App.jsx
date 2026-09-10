import { useState } from 'react'
import { BrowserRouter, Routes, Route, Link, Outlet  } from 'react-router-dom'

import './App.css'


function App() {
    return(
      <div className='bg-amber-500'>
        <h1>Pagina inicial</h1>
        <Outlet />
        <p>Footer</p>
      </div>
    )
}

export default App
