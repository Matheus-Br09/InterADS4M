import "./css/Sobre.css";

import { motion } from "framer-motion";

export default function Sobre() {

  return (

    <div className="min-h-screen bg-gradient-to-b from-pink-100 via-white to-pink-200 py-16 px-4">

      <div className="max-w-4xl mx-auto">

        {/* Título */}

        <div className="text-center mb-10">

          <h1 className="text-4xl font-bold bg-gradient-to-r from-pink-500 to-purple-500 bg-clip-text text-transparent drop-shadow-md">

            Quem Somos

          </h1>

        </div>

        {/* Card principal */}

        <motion.div

          initial={{ opacity: 0, y: 30 }}

          animate={{ opacity: 1, y: 0 }}

          transition={{ duration: 0.6 }}

          className="bg-white/70 backdrop-blur-md p-8 rounded-3xl shadow-xl border border-white/40 hover:scale-[1.01] transition-all duration-300"

        >

          <p className="text-gray-700 leading-relaxed mb-4">

            Uma liderança comprometida com a transformação social em Paulista-PE, cuja jornada política nasceu do desejo genuíno de fazer a diferença na vida das famílias que mais precisam de apoio, cuidado e oportunidades.

          </p>

          <p className="text-gray-700 leading-relaxed mb-4">

            Com uma atuação próxima da comunidade, busca entender de forma real as necessidades da população, valorizando cada história e cada desafio enfrentado no dia a dia.

          </p>

          <p className="text-gray-700 leading-relaxed mb-4">

            Com coragem, empatia e sensibilidade, transformou a convivência ao lado das famílias em uma verdadeira missão de vida: garantir dignidade, respeito e acesso a direitos básicos, especialmente para pessoas com deficiência e em situação de vulnerabilidade social.

          </p>

          <p className="text-gray-700 leading-relaxed mb-4">

            Mais do que ouvir, essa liderança atua de forma prática, promovendo ações que impactam diretamente a qualidade de vida da população.

          </p>

          <p className="text-gray-700 leading-relaxed mb-4">

            Acredita que a transformação social acontece através da união, do trabalho coletivo e do fortalecimento de políticas públicas inclusivas.

          </p>

          <p className="text-gray-700 leading-relaxed mb-4">

            Por isso, atua incentivando projetos sociais, promovendo iniciativas educativas e criando oportunidades para o desenvolvimento pessoal e profissional de jovens, adultos e idosos.

          </p>

          <p className="text-gray-700 leading-relaxed mb-4">

            Através do projeto "SOS Tudo pelo Social", trabalhamos para construir uma rede de apoio sólida e abrangente, que atende desde a primeira infância até a terceira idade.

          </p>

          <p className="text-gray-700 leading-relaxed mb-4">

            Nosso compromisso é promover inclusão, dignidade e qualidade de vida para todos, oferecendo suporte, orientação e acolhimento para famílias que enfrentam desafios diários.

          </p>

          <p className="text-gray-700 leading-relaxed">

            Seguimos firmes no propósito de construir uma sociedade mais justa, humana e acessível, onde cada pessoa tenha voz, vez e oportunidade de viver com respeito e dignidade.

          </p>

        </motion.div>

        {/* Divider */}

        <div className="w-24 h-1 bg-gradient-to-r from-pink-400 to-purple-400 mx-auto my-10 rounded-full"></div>

        {/* Missão */}

        <motion.div

          initial={{ opacity: 0, y: 30 }}

          animate={{ opacity: 1, y: 0 }}

          transition={{ duration: 0.6, delay: 0.2 }}

          className="mt-12 bg-gradient-to-r from-pink-300 to-purple-300 p-8 rounded-3xl shadow-lg text-center"

        >

          <h2 className="text-2xl font-semibold mb-4 text-white drop-shadow">

            Missão

          </h2>

          <p className="text-white leading-relaxed">

            "Construir uma sociedade mais justa e inclusiva, onde cada pessoa

            tenha acesso aos cuidados e oportunidades que merece,

            independentemente de suas limitações."

          </p>

        </motion.div>

      </div>

    </div>

  );

}