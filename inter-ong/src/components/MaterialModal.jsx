import { useState, useEffect } from "react";

export default function MaterialModal({ material, onClose }) {
  const [copied, setCopied] = useState(false);

  useEffect(() => {
    const handleKeyDown = (e) => {
      if (e.key === "Escape") onClose();
    };
    window.addEventListener("keydown", handleKeyDown);
    document.body.style.overflow = "hidden";

    return () => {
      window.removeEventListener("keydown", handleKeyDown);
      document.body.style.overflow = "auto";
    };
  }, [onClose]);

  if (!material) return null;

  const handleCopyLink = () => {
    if (navigator.clipboard) {
      navigator.clipboard.writeText(material.url);
      setCopied(true);
      setTimeout(() => setCopied(false), 2500);
    }
  };

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

  return (
    <div
      className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-900/60 backdrop-blur-sm animate-fade-in"
      onClick={onClose}
    >
      <div
        className="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto bg-white rounded-3xl shadow-2xl border border-pink-100 p-6 sm:p-8 transition-all transform scale-100"
        onClick={(e) => e.stopPropagation()}
      >
        {/* Header do Modal */}
        <div className="flex items-start justify-between gap-4 pb-4 border-b border-gray-100">
          <div className="flex flex-wrap items-center gap-2">
            <span
              className={`px-3 py-1 rounded-full text-xs font-semibold border ${getBadgeColor(
                material.categoria
              )}`}
            >
              {material.categoriaLabel || material.categoria}
            </span>
            <span className="px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
              {material.tipoLabel || material.tipo?.toUpperCase()}
            </span>
            {material.destaque && (
              <span className="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-400/20 text-amber-800 border border-amber-300">
                ★ Destaque SOS
              </span>
            )}
          </div>

          <button
            type="button"
            onClick={onClose}
            className="p-2 rounded-full text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors focus:outline-none"
            aria-label="Fechar janela"
          >
            <svg
              className="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M6 18L18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>

        {/* Conteúdo Principal */}
        <div className="mt-5 space-y-5">
          <h2 className="text-xl sm:text-2xl font-bold text-slate-800 leading-snug">
            {material.titulo}
          </h2>

          <p className="text-slate-600 leading-relaxed text-sm sm:text-base">
            {material.descricao}
          </p>

          {/* Ficha técnica rápida */}
          <div className="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 bg-pink-50/50 rounded-2xl border border-pink-100 text-xs sm:text-sm">
            <div>
              <span className="block text-slate-400 font-medium">Público-alvo</span>
              <span className="font-semibold text-slate-700">
                {material.publico || "Geral"}
              </span>
            </div>
            <div>
              <span className="block text-slate-400 font-medium">Faixa Etária</span>
              <span className="font-semibold text-slate-700">
                {material.faixaEtaria || "Livre"}
              </span>
            </div>
            <div className="col-span-2 sm:col-span-1">
              <span className="block text-slate-400 font-medium">Formato / Duração</span>
              <span className="font-semibold text-slate-700">
                {material.paginas || material.tempoLeitura || "Digital"}
              </span>
            </div>
          </div>

          {/* O que você vai encontrar */}
          {material.objetivos && material.objetivos.length > 0 && (
            <div className="space-y-2">
              <h3 className="text-sm font-bold uppercase tracking-wider text-slate-500">
                O que este material inclui:
              </h3>
              <ul className="space-y-2">
                {material.objetivos.map((obj, index) => (
                  <li
                    key={index}
                    className="flex items-start gap-2.5 text-sm text-slate-700"
                  >
                    <span className="w-5 h-5 rounded-full bg-pink-100 text-[#fb2782] flex items-center justify-center text-xs shrink-0 font-bold mt-0.5">
                      ✓
                    </span>
                    <span>{obj}</span>
                  </li>
                ))}
              </ul>
            </div>
          )}

          {/* Tags */}
          {material.tags && material.tags.length > 0 && (
            <div className="flex flex-wrap gap-1.5 pt-2">
              {material.tags.map((tag, idx) => (
                <span
                  key={idx}
                  className="px-2.5 py-1 rounded-lg bg-gray-100 text-gray-600 text-xs font-medium"
                >
                  #{tag}
                </span>
              ))}
            </div>
          )}
        </div>

        {/* Rodapé de Ações */}
        <div className="mt-8 pt-5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
          <button
            type="button"
            onClick={handleCopyLink}
            className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 text-slate-700 text-sm font-medium hover:bg-gray-50 transition-colors"
          >
            <svg
              className="w-4 h-4 text-slate-500"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
              />
            </svg>
            {copied ? "✓ Link Copiado!" : "Copiar Link"}
          </button>

          <div className="flex items-center gap-2 w-full sm:w-auto">
            <button
              type="button"
              onClick={onClose}
              className="w-full sm:w-auto px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors"
            >
              Voltar
            </button>

            <a
              href={material.url || material.arquivo_pdf || "#"}
              target="_blank"
              rel="noopener noreferrer"
              className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-white font-semibold text-sm bg-gradient-to-r from-[#fb2782] to-[#ff4785] hover:opacity-95 shadow-md shadow-pink-500/20 active:scale-95 transition-all"
            >
              {material.tipo === "pdf" ? (
                <>
                  <svg
                    className="w-4 h-4"
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
                  <span>Acessar / Baixar Material</span>
                </>
              ) : (
                <>
                  <svg
                    className="w-4 h-4"
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
                  <span>Abrir Portal / Link</span>
                </>
              )}
            </a>
          </div>
        </div>
      </div>
    </div>
  );
}
