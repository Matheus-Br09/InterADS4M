import { Link } from 'react-router-dom'

const SERVICOS = [
  {
    titulo: 'Neuropedagogia',
    texto: 'Acompanhamento voltado ao aprendizado de pessoas neurodivergentes, respeitando o ritmo e as necessidades de cada uma.',
  },
  {
    titulo: 'Nutrição',
    texto: 'Orientação alimentar para ajudar no bem-estar e na saúde das pessoas atendidas e de suas famílias.',
  },
  {
    titulo: 'Odontologia',
    texto: 'Cuidado com a saúde bucal, com atenção e paciência para quem precisa de um atendimento mais acolhedor.',
  },
  {
    titulo: 'Fisioterapia',
    texto: 'Apoio ao movimento e ao desenvolvimento motor, com cuidado e atenção a cada pessoa.',
  },
]

export default function Servicos() {
  return (
    <main className="mx-auto max-w-5xl px-4 py-12 sm:px-6">
      <h1 className="text-4xl font-extrabold text-slate-900 dark:text-white sm:text-5xl">Nossos serviços</h1>
      <div className="mt-4 h-1.5 w-20 rounded-full bg-[#fb2782]" aria-hidden="true" />
      <p className="mt-5 max-w-2xl text-lg text-slate-600 dark:text-slate-300">
        A SOS Tudo pelo Social atende pessoas neurodivergentes e suas famílias em Paulista, Pernambuco.
      </p>

      <div className="mt-10 grid gap-6 sm:grid-cols-2">
        {SERVICOS.map((servico) => (
          <section key={servico.titulo} className="rounded-3xl border border-pink-100 bg-white p-7 shadow-md transition hover:-translate-y-1 hover:shadow-lg dark:border-slate-800 dark:bg-slate-900">
            <h2 className="text-2xl font-bold text-pink-700 dark:text-pink-400">{servico.titulo}</h2>
            {/* REVISAR COM A ONG */}
            <p className="mt-2 text-slate-700 dark:text-slate-300">{servico.texto}</p>
          </section>
        ))}
      </div>

      <div className="mt-10 flex flex-wrap gap-3">
        <Link to="/voluntariado" className="rounded-full bg-[#fb2782] px-6 py-3 text-sm font-bold text-white shadow-md transition hover:scale-105">
          Quero ser voluntário
        </Link>
        <Link to="/doar" className="rounded-full border border-[#fb2782] px-6 py-3 text-sm font-bold text-[#fb2782] transition hover:bg-pink-50 dark:text-pink-400 dark:hover:bg-slate-800">
          Doar
        </Link>
      </div>
    </main>
  )
}
