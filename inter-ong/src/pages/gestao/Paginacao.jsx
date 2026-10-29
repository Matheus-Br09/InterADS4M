export default function Paginacao({ pagina, ultima, setPagina }) {
  if (ultima <= 1) return null
  const botao = 'rounded-lg border border-slate-300 bg-white px-4 py-2 font-semibold disabled:opacity-40'
  return (
    <nav aria-label="Páginas" className="mt-6 flex items-center justify-center gap-4">
      <button className={botao} disabled={pagina <= 1} onClick={() => setPagina(pagina - 1)}>Anterior</button>
      <span>Página {pagina} de {ultima}</span>
      <button className={botao} disabled={pagina >= ultima} onClick={() => setPagina(pagina + 1)}>Próxima</button>
    </nav>
  )
}
