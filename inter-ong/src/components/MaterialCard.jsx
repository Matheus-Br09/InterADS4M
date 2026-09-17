export default function MaterialCard({ material, onSelect }) {
  const getBadgeColor = (cat) => {
    switch (cat) {
      case "neuropedagogia":
        return "bg-purple-100 text-purple-700 border-purple-200";
      case "familia":
        return "bg-pink-100 text-pink-700 border-pink-200";
      case "atividades":
        return "bg-emerald-100 text-emerald-700 border-emerald-200";
      case "psicomotricidade":
        return "bg-amber-100 text-amber-800 border-amber-200";
      case "portais":
        return "bg-blue-100 text-blue-700 border-blue-200";
      default:
        return "bg-gray-100 text-gray-700 border-gray-200";
    }
  };

  const isPdf = material.tipo === "pdf";

  return (
    <div className="group relative flex flex-col justify-between bg-white rounded-3xl p-6 sm:p-7 border border-pink-100/80 shadow-sm hover:shadow-xl hover:shadow-pink-500/10 hover:border-pink-300/80 transition-all duration-300 transform hover:-translate-y-1">
      {/* Top badges */}
      <div>
        <div className="flex items-center justify-between gap-2 mb-4">
          <span
            className={`px-3 py-1 rounded-full text-xs font-semibold border ${getBadgeColor(
              material.categoria
            )}`}
          >
            {material.categoriaLabel || material.categoria}
          </span>

          <div className="flex items-center gap-1.5">
            {material.destaque && (
              <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                ★ Destaque
              </span>
            )}
            <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-600">
              {isPdf ? "📄 PDF" : "🔗 LINK"}
            </span>
          </div>
        </div>

        {/* Title */}
        <h3
          onClick={() => onSelect(material)}
          className="text-lg font-bold text-slate-800 group-hover:text-[#fb2782] cursor-pointer transition-colors line-clamp-2 leading-snug mb-2.5"
        >
          {material.titulo}
        </h3>

        {/* Description */}
        <p className="text-slate-600 text-sm line-clamp-3 leading-relaxed mb-4">
          {material.descricao}
        </p>
      </div>

      {/* Footer Info & Actions */}
      <div className="mt-4 pt-4 border-t border-slate-100">
        {/* Metadados rápidos */}
        <div className="flex items-center justify-between text-xs text-slate-500 mb-4">
          <span className="truncate max-w-[170px]" title={material.publico}>
            👥 {material.publico || "Geral"}
          </span>
          <span className="shrink-0 font-medium text-slate-600">
            {material.paginas || material.tempoLeitura || "Acesso Livre"}
          </span>
        </div>

        {/* Botões */}
        <div className="flex items-center gap-2">
          <button
            type="button"
            onClick={() => onSelect(material)}
            className="flex-1 py-2 px-3 rounded-xl border border-slate-200 hover:border-pink-200 text-xs font-semibold text-slate-700 hover:text-[#fb2782] hover:bg-pink-50/50 transition-colors"
          >
            Ver Detalhes
          </button>

          <a
            href={material.url || material.arquivo_pdf || "#"}
            target="_blank"
            rel="noopener noreferrer"
            className="flex-1 inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-[#fb2782] to-[#ff4785] hover:opacity-95 shadow-sm shadow-pink-500/20 active:scale-95 transition-all"
          >
            {isPdf ? (
              <>
                <svg
                  className="w-3.5 h-3.5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={2}
                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                  />
                </svg>
                <span>Baixar</span>
              </>
            ) : (
              <>
                <svg
                  className="w-3.5 h-3.5"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={2}
                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                  />
                </svg>
                <span>Acessar</span>
              </>
            )}
          </a>
        </div>
      </div>
    </div>
  );
}
