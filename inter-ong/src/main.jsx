import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import './index.css'
import App from './App.jsx'
import { createBrowserRouter, RouterProvider, Navigate } from 'react-router-dom'


// Importa as páginas

import LandingPage from './pages/LandingPage.jsx'
import Doar from './pages/Doar.jsx'
import Sobre from './pages/Sobre.jsx'
import ErrorPage from './pages/ErrorPage.jsx'
import Contato from './pages/Contato.jsx'
import Educacional from './pages/Educacional.jsx'
import LoginECadastro from './pages/LoginECadastro.jsx'
import GestaoMateriais from './pages/GestaoMateriais.jsx'
import Voluntariado from './pages/Voluntariado.jsx'
import Noticias from './pages/Noticias.jsx'



// Jás aqui a rota das páginas para acessá-las, caso queira adicionar uma página, coloque-a aqui

const router = createBrowserRouter([
  {
    path: "/",
    errorElement: <ErrorPage />,
    element: <App />,
    children: [
      { path: "programas", element: <Noticias key="programas" recurso="programas" titulo="Programas e ações" /> },
      { path: "transparencia", element: <Noticias key="transparencia" recurso="transparencia" titulo="Transparência" /> },
      { path: 'gestao', element: <Navigate to='/gestao/materiais' replace /> },
      {
        index: true,
        element: <LandingPage />
      },
      {
        path: "doar",
        element: <Doar />
      },
      {
        path: "sobre",
        element: <Sobre />
      },
      {
        path: "contato",
        element: <Contato />
      },
      {
        path: "educacional",
        element: <Educacional />
      },
      {
        path: "login",
        element: <LoginECadastro />
      },

      {
        path: "gestao/materiais",
        element: <GestaoMateriais />
      },
      {
        path: "voluntariado",
        element: <Voluntariado />
      },
      {
        path: "noticias",
        element: <Noticias />
      }
    ]
  }
])


createRoot(document.getElementById('root')).render(
  <StrictMode>
    <RouterProvider router={router}/>
  </StrictMode>,
)
