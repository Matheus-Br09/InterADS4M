import React from 'react'
import { Link } from 'react-router-dom'
import "./css/ErrorPage.css"

const ErrorPage = () => {
    return (
        <div className="error-page">
            <h1>Erro 404</h1>
            <p>Oops! A página que você está procurando não foi encontrada.</p>
            <Link to="/">Voltar para o início</Link>
        </div>
    )
}

export default ErrorPage;