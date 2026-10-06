import React, { useState, useEffect } from 'react';

export default function App() {
  const [newsList, setNewsList] = useState([
    {
      id: '1',
      title: 'Projeto Acolher expande atendimento para mais de 500 famílias',
      category: 'Inclusão Social',
      date: '05 de Outubro, 2026',
      image: 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=800&auto=format&fit=crop',
      excerpt: 'Nova fase do projeto garante oficinas profissionalizantes e apoio psicológico para comunidades vulneráveis na região metropolitana.',
      featured: true
    },
    {
      id: '2',
      title: 'Mutirão de Cidadania realiza mais de 200 atendimentos em fim de semana',
      category: 'Comunidade',
      date: '02 de Outubro, 2026',
      image: 'https://images.unsplash.com/photo-1531206715517-5c0ba140b2b8?q=80&w=800&auto=format&fit=crop',
      excerpt: 'Emissão de documentos, consultas médicas básicas e recreação infantil marcaram a ação voluntária realizada no último sábado.',
      featured: false
    },
    {
      id: '3',
      title: 'Parceria estratégica garante novos recursos para oficinas de tecnologia',
      category: 'Educação',
      date: '28 de Setembro, 2026',
      image: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=800&auto=format&fit=crop',
      excerpt: 'Jovens atendidos pelas iniciativas da SOS Tudo pelo Social terão acesso a laboratórios de informática modernos e cursos de programação.',
      featured: false
    }
  ]);

  const [activeCategory, setActiveCategory] = useState('all');
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [toastMessage, setToastMessage] = useState(null);

  const [formData, setFormData] = useState({
    title: '',
    category: 'Inclusão Social',
    image: '',
    excerpt: '',
    featured: false
  });

  // =========================================================================
  // BASE PARA FUTURA INTEGRAÇÃO COM BANCO DE DADOS (Ex: Firebase, PostgreSQL)
  // =========================================================================
  useEffect(() => {
    // [TODO]: Substituir por chamada real assíncrona ao banco (ex: fetch('/api/news') ou onSnapshot do Firebase)
    console.log("--> [DB MOCK] Conectando ao banco de dados e buscando notícias...");
  }, []);

  const handleDatabaseSave = (newNewsItem) => {
    // [TODO]: Substituir por método de salvamento no banco (ex: await axios.post('/api/news', newNewsItem))
    console.log("--> [DB MOCK] Inserindo registro no banco de dados:", newNewsItem);
    setNewsList(prev => [newNewsItem, ...prev]);
    showToast("Notícia cadastrada com sucesso!");
  };

  const handleDatabaseDelete = (id) => {
    // [TODO]: Substituir por método de exclusão no banco (ex: await axios.delete(`/api/news/${id}`))
    console.log("--> [DB MOCK] Removendo registro do banco ID:", id);
    setNewsList(prev => prev.filter(item => item.id !== id));
    showToast("Notícia removida com sucesso!");
  };
  // =========================================================================

  const showToast = (msg) => {
    setToastMessage(msg);
    setTimeout(() => {
      setToastMessage(null);
    }, 3500);
  };

  const handleSubmitNews = (e) => {
    e.preventDefault();
    if (!formData.title || !formData.excerpt) return;

    const newItem = {
      id: 'news_' + Date.now(),
      title: formData.title,
      category: formData.category,
      image: formData.image || 'https://images.unsplash.com/photo-1509062522246-3755977927d7?q=80&w=800&auto=format&fit=crop',
      excerpt: formData.excerpt,
      date: new Date().toLocaleDateString('pt-BR', { day: '2-digit', month: 'long', year: 'numeric' }),
      featured: formData.featured
    };

    if (formData.featured) {
      setNewsList(prev => prev.map(item => ({ ...item, featured: false })));
    }

    handleDatabaseSave(newItem);
    setFormData({ title: '', category: 'Inclusão Social', image: '', excerpt: '', featured: false });
    setIsModalOpen(false);
  };

  const filteredNews = activeCategory === 'all' 
    ? newsList 
    : newsList.filter(item => item.category === activeCategory);

  const featuredItem = newsList.find(item => item.featured) || newsList[0];

  return (
    <div className="bg-slate-50 text-slate-900 min-h-screen font-sans selection:bg-[#ff2a75] selection:text-white flex flex-col justify-between">
      
      <main className="flex-grow">
        {/* HERO SECTION DE DESTAQUE */}
        <section className="relative overflow-hidden py-16 lg:py-24" style={{ background: 'radial-gradient(circle at 70% 30%, rgba(226,242,255,1) 0%, rgba(255,255,255,0) 70%)' }}>
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            <div className="text-center max-w-3xl mx-auto mb-12">
              <span className="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-[#ff2a75] bg-white px-4 py-1.5 rounded-full shadow-sm mb-4 border border-[#ff2a75]/20">
                📢 Mural de Informações & Atualizações
              </span>
              <h1 className="text-4xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight mb-6">
                Acolher, Incluir e <span className="text-[#ff2a75]">Transformar Vidas</span>
              </h1>
              <p className="text-base sm:text-lg text-slate-600 leading-relaxed">
                Acompanhe aqui todas as notícias, avanços dos projetos sociais, relatórios de impacto e histórias inspiradoras da nossa comunidade.
              </p>
              
              <div className="mt-8">
                <button 
                  onClick={() => setIsModalOpen(true)}
                  className="bg-[#ff2a75] hover:bg-[#e01c62] text-white text-sm font-semibold px-6 py-3 rounded-full shadow-lg shadow-[#ff2a75]/25 transition transform active:scale-95 inline-flex items-center gap-2"
                >
                  ➕ Cadastrar Nova Notícia (Simular Banco)
                </button>
              </div>
            </div>

            {/* BANNER DE DESTAQUE PRINCIPAL */}
            {featuredItem && (
              <div className="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-100 flex flex-col lg:flex-row gap-8 items-center transition duration-300 hover:shadow-2xl">
                <div className="w-full lg:w-1/2 h-72 sm:h-80 rounded-2xl overflow-hidden relative group">
                  <img 
                    src={featuredItem.image} 
                    alt={featuredItem.title} 
                    className="w-full h-full object-cover group-hover:scale-105 transition duration-500" 
                  />
                  <div className="absolute top-4 left-4 bg-[#ff2a75] text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-md flex items-center gap-1">
                    ✨ {featuredItem.category}
                  </div>
                </div>
                <div className="w-full lg:w-1/2 space-y-4">
                  <div className="flex items-center gap-3 text-xs text-slate-400 font-medium">
                    <span className="flex items-center gap-1">📅 {featuredItem.date}</span>
                    <span>&bull;</span>
                    <span className="text-[#ff2a75] font-semibold">Destaque Principal</span>
                  </div>
                  <h3 className="text-2xl sm:text-3xl font-extrabold text-slate-900 leading-tight">
                    {featuredItem.title}
                  </h3>
                  <p className="text-slate-600 text-sm sm:text-base leading-relaxed">
                    {featuredItem.excerpt}
                  </p>
                  <div className="pt-2 flex items-center gap-4">
                    <button 
                      onClick={() => showToast(`Abrindo matéria completa...`)}
                      className="bg-[#ff2a75] hover:bg-[#e01c62] text-white text-xs font-semibold px-6 py-3 rounded-full shadow-lg shadow-[#ff2a75]/25 transition flex items-center gap-2"
                    >
                      Ler Matéria Completa &rarr;
                    </button>
                    <button 
                      onClick={() => handleDatabaseDelete(featuredItem.id)}
                      className="text-slate-400 hover:text-red-500 p-2 rounded-full transition text-base" 
                      title="Excluir notícia"
                    >
                      🗑️
                    </button>
                  </div>
                </div>
              </div>
            )}

          </div>
        </section>

        {/* SEÇÃO DE LISTAGEM DE NOTÍCIAS */}
        <section className="py-16 bg-white border-t border-slate-100">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
              <div>
                <span className="text-xs font-bold uppercase tracking-wider text-[#ff2a75]">Últimas Atualizações</span>
                <h3 className="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Notícias Recentes</h3>
              </div>

              {/* Filtros de Categoria */}
              <div className="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-2 sm:pb-0">
                {['all', 'Inclusão Social', 'Comunidade', 'Educação'].map((cat) => (
                  <button
                    key={cat}
                    onClick={() => setActiveCategory(cat)}
                    className={`px-4 py-2 text-xs font-semibold rounded-full transition ${
                      activeCategory === cat 
                        ? 'bg-[#ff2a75] text-white shadow-sm' 
                        : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                    }`}
                  >
                    {cat === 'all' ? 'Todas' : cat}
                  </button>
                ))}
              </div>
            </div>

            {/* GRID DE CARTÕES */}
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              {filteredNews.length === 0 ? (
                <div className="col-span-3 text-center py-12 text-slate-400 text-sm">
                  Nenhuma notícia encontrada nesta categoria.
                </div>
              ) : (
                filteredNews.map(item => (
                  <div key={item.id} className="bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition duration-300 flex flex-col overflow-hidden group">
                    <div className="h-48 overflow-hidden relative">
                      <img 
                        src={item.image} 
                        alt={item.title} 
                        className="w-full h-full object-cover group-hover:scale-105 transition duration-500" 
                      />
                      <div className="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-[#ff2a75] text-[11px] font-bold px-3 py-1 rounded-full shadow-sm">
                        {item.category}
                      </div>
                    </div>
                    <div className="p-6 flex flex-col justify-between flex-grow">
                      <div>
                        <div className="text-xs text-slate-400 font-medium mb-2 flex items-center gap-1">
                          📅 {item.date}
                        </div>
                        <h4 className="text-lg font-bold text-slate-900 group-hover:text-[#ff2a75] transition leading-snug mb-2">
                          {item.title}
                        </h4>
                        <p className="text-slate-600 text-xs sm:text-sm line-clamp-3 leading-relaxed">
                          {item.excerpt}
                        </p>
                      </div>
                      <div className="pt-6 mt-6 border-t border-slate-100 flex items-center justify-between">
                        <button 
                          onClick={() => showToast(`Abrindo: ${item.title.substring(0, 25)}...`)}
                          className="text-xs font-bold text-[#ff2a75] hover:text-[#e01c62] transition flex items-center gap-1.5"
                        >
                          Ler mais &rarr;
                        </button>
                        <button 
                          onClick={() => handleDatabaseDelete(item.id)} 
                          className="text-slate-300 hover:text-red-500 text-sm p-1.5 transition" 
                          title="Excluir"
                        >
                          🗑️
                        </button>
                      </div>
                    </div>
                  </div>
                ))
              )}
            </div>

          </div>
        </section>
      </main>

      {/* RODAPÉ */}
      <footer className="bg-slate-900 text-white py-12 border-t border-slate-800">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-2xl bg-[#ff2a75] flex items-center justify-center text-white shadow-lg text-lg">
              ❤️
            </div>
            <div>
              <h4 className="font-extrabold text-white text-base">SOS Tudo pelo Social</h4>
              <p className="text-xs text-slate-400">Transformando realidades com amor e dedicação.</p>
            </div>
          </div>
          <p className="text-xs text-slate-400 text-center md:text-right">
            &copy; 2026 SOS Tudo pelo Social. Todos os direitos reservados.
          </p>
        </div>
      </footer>

      {/* MODAL DE CADASTRO (SIMULAÇÃO DE BANCO DE DADOS) */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
          <div className="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl relative animate-in fade-in zoom-in duration-200">
            <button 
              onClick={() => setIsModalOpen(false)} 
              className="absolute top-6 right-6 text-slate-400 hover:text-slate-600 w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center transition font-bold"
            >
              ✕
            </button>
            <div className="mb-6">
              <span className="text-xs font-bold uppercase tracking-wider text-[#ff2a75] bg-[#ff2a75]/10 px-3 py-1 rounded-full">
                Base para Banco de Dados
              </span>
              <h3 className="text-xl font-extrabold text-slate-900 mt-2">Cadastrar Nova Notícia</h3>
              <p className="text-xs text-slate-500 mt-1">
                Este formulário manipula o estado local. No futuro, os dados preenchidos aqui serão enviados via API para o Banco de Dados.
              </p>
            </div>

            <form onSubmit={handleSubmitNews} className="space-y-4">
              <div>
                <label className="block text-xs font-semibold text-slate-700 mb-1">Título da Notícia</label>
                <input 
                  type="text" 
                  required 
                  value={formData.title}
                  onChange={(e) => setFormData({...formData, title: e.target.value})}
                  placeholder="Ex: Projeto Social inaugura nova sede..." 
                  className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#ff2a75] transition" 
                />
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-semibold text-slate-700 mb-1">Categoria</label>
                  <select 
                    value={formData.category}
                    onChange={(e) => setFormData({...formData, category: e.target.value})}
                    className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#ff2a75] bg-white transition"
                  >
                    <option value="Inclusão Social">Inclusão Social</option>
                    <option value="Comunidade">Comunidade</option>
                    <option value="Educação">Educação</option>
                    <option value="Voluntariado">Voluntariado</option>
                  </select>
                </div>
                <div>
                  <label className="block text-xs font-semibold text-slate-700 mb-1">URL da Imagem</label>
                  <input 
                    type="url" 
                    value={formData.image}
                    onChange={(e) => setFormData({...formData, image: e.target.value})}
                    placeholder="https://exemplo.com/foto.jpg" 
                    className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#ff2a75] transition" 
                  />
                </div>
              </div>

              <div>
                <label className="block text-xs font-semibold text-slate-700 mb-1">Resumo / Conteúdo Curto</label>
                <textarea 
                  rows="3" 
                  required 
                  value={formData.excerpt}
                  onChange={(e) => setFormData({...formData, excerpt: e.target.value})}
                  placeholder="Breve descrição que aparecerá nos cartões de notícias..." 
                  className="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:border-[#ff2a75] transition"
                ></textarea>
              </div>

              <div className="flex items-center gap-2 pt-1">
                <input 
                  type="checkbox" 
                  id="newsFeatured" 
                  checked={formData.featured}
                  onChange={(e) => setFormData({...formData, featured: e.target.checked})}
                  className="w-4 h-4 text-[#ff2a75] rounded border-slate-300 focus:ring-[#ff2a75]" 
                />
                <label htmlFor="newsFeatured" className="text-xs font-medium text-slate-700">
                  Definir como Notícia em Destaque (Banner Principal)
                </label>
              </div>

              <div className="pt-4 flex items-center justify-end gap-3">
                <button 
                  type="button" 
                  onClick={() => setIsModalOpen(false)} 
                  className="px-5 py-2.5 rounded-full text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition"
                >
                  Cancelar
                </button>
                <button 
                  type="submit" 
                  className="px-6 py-2.5 rounded-full text-xs font-semibold text-white bg-[#ff2a75] hover:bg-[#e01c62] shadow-lg shadow-[#ff2a75]/25 transition"
                >
                  Salvar (Simular DB)
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* TOAST DE NOTIFICAÇÃO */}
      {toastMessage && (
        <div className="fixed bottom-5 right-5 z-50 bg-slate-900 text-white px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-3 transition-all duration-300">
          <span>✅</span>
          <span className="text-xs font-medium">{toastMessage}</span>
        </div>
      )}

    </div>
  );
}