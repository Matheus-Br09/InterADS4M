import "./css/Sobre.css";
import realRodaConversaImg from '../assets/real-roda-conversa.jpg';
import realOficinaArteImg from '../assets/real-oficina-arte.jpg';
import realRecreacaoPraiaImg from '../assets/real-recreacao-praia.jpg';
import realOficinaUvaImg from '../assets/real-oficina-uva.jpg';

export default function Sobre() {

  return (

    <div className="min-h-screen bg-gradient-to-b from-pink-100 via-white to-pink-200 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950 py-16 px-4">

      <div className="max-w-4xl mx-auto">

        {/* Título */}

        <div className="text-center mb-10">

          <h1 className="text-4xl font-bold bg-gradient-to-r from-pink-500 to-purple-500 bg-clip-text text-transparent drop-shadow-md">

            Quem Somos

          </h1>

        </div>

        {/* Card principal */}

        <div className="sobre-card rounded-3xl bg-white dark:bg-[#121a2d] p-8 shadow-sm border border-pink-100 dark:border-slate-800 space-y-4">

          <p className="text-gray-700 dark:text-slate-200 leading-relaxed">
            Uma liderança comprometida com a transformação social em Paulista-PE, cuja jornada política nasceu do desejo genuíno de fazer a diferença na vida das famílias que mais precisam de apoio, cuidado e oportunidades.
          </p>

          <p className="text-gray-700 dark:text-slate-200 leading-relaxed">
            Com uma atuação próxima da comunidade, busca entender de forma real as necessidades da população, valorizando cada história e cada desafio enfrentado no dia a dia.
          </p>

          <p className="text-gray-700 dark:text-slate-200 leading-relaxed">
            Com coragem, empatia e sensibilidade, transformou a convivência ao lado das famílias em uma verdadeira missão de vida: garantir dignidade, respeito e acesso a direitos básicos, especialmente para pessoas com deficiência e em situação de vulnerabilidade social.
          </p>

          <p className="text-gray-700 dark:text-slate-200 leading-relaxed">
            Mais do que ouvir, essa liderança atua de forma prática, promovendo ações que impactam diretamente a qualidade de vida da população.
          </p>

          <p className="text-gray-700 dark:text-slate-200 leading-relaxed">
            Acredita que a transformação social acontece através da união, do trabalho coletivo e do fortalecimento de políticas públicas inclusivas.
          </p>

          <p className="text-gray-700 dark:text-slate-200 leading-relaxed">
            Por isso, atua incentivando projetos sociais, promovendo iniciativas educativas e criando oportunidades para o desenvolvimento pessoal e profissional de jovens, adultos e idosos.
          </p>

          <p className="text-gray-700 dark:text-slate-200 leading-relaxed">
            Através do projeto "SOS Tudo pelo Social", trabalhamos para construir uma rede de apoio sólida e abrangente, que atende desde a primeira infância até a terceira idade.
          </p>

          <p className="text-gray-700 dark:text-slate-200 leading-relaxed">
            Nosso compromisso é promover inclusão, dignidade e qualidade de vida para todos, oferecendo suporte, orientação e acolhimento para famílias que enfrentam desafios diários.
          </p>

          <p className="text-gray-700 dark:text-slate-200 leading-relaxed">
            Seguimos firmes no propósito de construir uma sociedade mais justa, humana e acessível, onde cada pessoa tenha voz, vez e oportunidade de viver com respeito e dignidade.
          </p>

        </div>

        {/* Galeria de Fotos Reais das Ações */}
        <div className="my-12">
          <div className="text-center mb-6">
            <span className="text-xs font-bold uppercase tracking-wider text-pink-600 dark:text-pink-400">Nossa Realidade em Ação</span>
            <h2 className="text-2xl font-bold text-slate-800 dark:text-slate-100 mt-1">Momentos Reais do Instituto</h2>
          </div>

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div className="overflow-hidden rounded-2xl border border-pink-100 dark:border-slate-800 shadow-sm bg-white dark:bg-slate-900 group">
              <img src={realRodaConversaImg} alt="Roda de acolhimento e suporte comunitário no instituto" className="h-56 w-full object-cover transition-transform duration-300 group-hover:scale-105" />
              <div className="p-3 text-center text-xs font-semibold text-slate-700 dark:text-slate-300">
                Acolhimento Familiar & Rodas de Diálogo
              </div>
            </div>

            <div className="overflow-hidden rounded-2xl border border-pink-100 dark:border-slate-800 shadow-sm bg-white dark:bg-slate-900 group">
              <img src={realOficinaArteImg} alt="Oficina de artes e desenvolvimento pedagógico com as crianças" className="h-56 w-full object-cover transition-transform duration-300 group-hover:scale-105" />
              <div className="p-3 text-center text-xs font-semibold text-slate-700 dark:text-slate-300">
                Oficinas Educativas & Aprendizado Prático
              </div>
            </div>

            <div className="overflow-hidden rounded-2xl border border-pink-100 dark:border-slate-800 shadow-sm bg-white dark:bg-slate-900 group">
              <img src={realOficinaUvaImg} alt="Crianças apresentando suas atividades artísticas no instituto" className="h-56 w-full object-cover transition-transform duration-300 group-hover:scale-105" />
              <div className="p-3 text-center text-xs font-semibold text-slate-700 dark:text-slate-300">
                Estimulação Cognitiva & Criatividade
              </div>
            </div>

            <div className="overflow-hidden rounded-2xl border border-pink-100 dark:border-slate-800 shadow-sm bg-white dark:bg-slate-900 group">
              <img src={realRecreacaoPraiaImg} alt="Atividade ao ar livre de lazer e inclusão na praia" className="h-56 w-full object-cover transition-transform duration-300 group-hover:scale-105" />
              <div className="p-3 text-center text-xs font-semibold text-slate-700 dark:text-slate-300">
                Integração Comunitária, Lazer & Praia
              </div>
            </div>
          </div>
        </div>

        {/* Divider */}

        <div className="w-24 h-1 bg-gradient-to-r from-pink-400 to-purple-400 mx-auto my-10 rounded-full"></div>

        {/* Missão */}

        <div className="bg-pink-400 dark:bg-pink-900/60 p-6 rounded-2xl shadow-xl shadow-fuchsia-900/20 text-center border border-pink-300 dark:border-pink-800">
          <h2 className="text-2xl font-semibold mb-3 text-white drop-shadow">
            Missão
          </h2>

          <p className="text-white leading-relaxed max-w-2xl mx-auto">
            "Construir uma sociedade mais justa e inclusiva, onde cada pessoa
            tenha acesso aos cuidados e oportunidades que merece,
            independentemente de suas limitações."
          </p>
        </div>

      </div>

    </div>

  );

}