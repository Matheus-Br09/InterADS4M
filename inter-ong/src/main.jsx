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
import Servicos from './pages/Servicos.jsx'
import Privacidade from './pages/Privacidade.jsx'
import GestaoLayout from './pages/gestao/GestaoLayout.jsx'
import GestaoEntrar from './pages/gestao/GestaoEntrar.jsx'
import GestaoDoacoes from "./pages/gestao/GestaoDoacoes.jsx"
import GestaoVoluntarios from "./pages/gestao/GestaoVoluntarios.jsx"
import GestaoConteudos from "./pages/gestao/GestaoConteudos.jsx"
import GestaoGestores from "./pages/gestao/GestaoGestores.jsx"
import GestaoTrocarSenha from './pages/gestao/GestaoTrocarSenha.jsx'
import Voluntariado from './pages/Voluntariado.jsx'
import Noticias from './pages/Noticias.jsx'



// Jás aqui a rota das páginas para acessá-las, caso queira adicionar uma página, coloque-a aqui

const router = createBrowserRouter([
  // Painel de gestão: fora do site público (sem menu, newsletter e rodapé)
  { path: "gestao/entrar", element: <GestaoEntrar />, errorElement: <ErrorPage /> },
  { path: "gestao/trocar-senha", element: <GestaoTrocarSenha />, errorElement: <ErrorPage /> },
  {
    path: "gestao",
    element: <GestaoLayout />,
    errorElement: <ErrorPage />,
    children: [
      { index: true, element: <Navigate to="doacoes" replace /> },
      // Telas das Tarefas 18 e 19 substituem estes avisos.
      { path: "doacoes", element: <GestaoDoacoes /> },
      { path: "voluntarios", element: <GestaoVoluntarios /> },
      { path: "conteudos", element: <GestaoConteudos /> },
      { path: "gestores", element: <GestaoGestores /> },
    ]
  },
  {
    path: "/",
    errorElement: <ErrorPage />,
    element: <App />,
    children: [
      { path: "programas", element: <Noticias key="programas" recurso="programas" titulo="Programas e ações" /> },
      { path: "transparencia", element: <Noticias key="transparencia" recurso="transparencia" titulo="Transparência" /> },
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
      { path: "servicos", element: <Servicos /> },
      { path: "privacidade", element: <Privacidade /> },

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
