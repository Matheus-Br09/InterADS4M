import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import './index.css'
import App from './App.jsx'
import { createBrowserRouter, RouterProvider } from 'react-router-dom'


// Importa as páginas

import LandingPage from './pages/LandingPage.jsx'
import Doar from './pages/Doar.jsx'
import Sobre from './pages/Sobre.jsx'
import ErrorPage from './pages/ErrorPage.jsx'
import Contato from './pages/Contato.jsx'

// const router = createBrowserRouter([
//   {
//     path: "/",
//     element: <App />,
//     errorElement: <ErrorPage />,
//     children: [
//       {
//         path: "landing",
//         element: <LandingPage />
//       },
//       {
//         path: "Sobre",
//         element: <Sobre />
//       },
//       {
//         path: "Doar",
//         element: <Doar />
//       }
//     ]
//   },
// ])


// Jás aqui a rota das páginas para acessá-las, caso queira adicionar uma página, coloque-a aqui 

const router = createBrowserRouter([
  {
    path: "/",
    errorElement: <ErrorPage />,
    element: <App />,
    children: [
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
        element: <Contato/>
      }
    ]
  }
])


createRoot(document.getElementById('root')).render(
  <StrictMode>
    <RouterProvider router={router}/>
  </StrictMode>,
)
