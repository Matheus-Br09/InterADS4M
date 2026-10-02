import { MATERIAIS_EDUCATIVOS } from "../data/materiaisEducativos.js";

export default function Educacional() {
  return (
    <div className="min-h-screen bg-pink-50/40 py-12 px-4 sm:px-6 lg:px-8">
      <div className="max-w-6xl mx-auto">
       
        <div className="text-center mb-10">
          <h1 className="text-3xl sm:text-4xl font-bold text-slate-800 mb-3">
            Área Educacional
          </h1>
          <p className="text-slate-600 max-w-xl mx-auto text-sm sm:text-base">
            Materiais educativos, guias e links de apoio disponibilizados pela ONG.
          </p>
        </div>

        
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {MATERIAIS_EDUCATIVOS.map((material) => (
            <div
              key={material.id}
              className="bg-white rounded-2xl p-6 border border-pink-100 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between"
            >
              <div>
                
                <span className="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-pink-100 text-[#fb2782] mb-3">
                  {material.categoria}
                </span>

              
                <h2 className="text-lg font-bold text-slate-800 mb-2">
                  {material.titulo}
                </h2>

               
                <p className="text-sm text-slate-600 leading-relaxed mb-4">
                  {material.descricao}
                </p>
              </div>

        
              <a
                href={material.url}
                target="_blank"
                rel="noopener noreferrer"
                className="mt-4 inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl text-white font-semibold text-sm bg-gradient-to-r from-[#fb2782] to-[#ff4785] hover:opacity-95 transition-opacity"
              >
                Acessar Material ({material.tipo})
              </a>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}