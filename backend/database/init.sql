-- Script de inicializacao do banco ong_sos
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Estrutura para tabela `administradores`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `administradores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `administradores` (`id`, `nome`, `email`, `senha`, `data_criacao`) VALUES
(1, 'Carol Administradora', 'carol@sos.org.br', '$2y$10$fOZdzsvn1fhHof2RDBklWOQCwh5f5x9FyvWfRjzyDWtI6yosPi6se', '2026-09-07 10:38:40')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- --------------------------------------------------------
-- Estrutura para tabela `materiais_didaticos`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `materiais_didaticos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) NOT NULL,
  `descricao` text DEFAULT NULL,
  `arquivo_pdf` varchar(255) NOT NULL,
  `imagem_capa` varchar(255) DEFAULT NULL,
  `data_upload` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Estrutura para tabela `noticias`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `noticias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `resumo` text NOT NULL,
  `texto_completo` longtext NOT NULL,
  `imagem` varchar(255) NOT NULL,
  `data_criacao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `noticias` (`id`, `titulo`, `resumo`, `texto_completo`, `imagem`, `data_criacao`) VALUES
(1, 'Capacitação que salva vidas: ONG realiza palestra de Primeiros Socorros para a comunidade', 'A ONG realizou uma palestra gratuita de primeiros socorros, reunindo moradores e voluntários com o objetivo de preparar a comunidade para agir rapidamente em situações de emergência.', 'O evento reuniu moradores, líderes locais e voluntários, destacando a importância de saber como prestar os primeiros cuidados antes mesmo da chegada das equipes médicas de emergência, como o SAMU (192) ou o Corpo de Bombeiros (193).O treinamento uniu teoria e prática de forma simples, mostrando que qualquer pessoa, mesmo sem formação na área da saúde, pode fazer a diferença e salvar uma vida se tiver a instrução correta.Aprendizado prático e simulaçõesSob a orientação de profissionais capacitados, os participantes aprenderam o passo a passo de como lidar com os acidentes mais comuns do dia a dia. A programação abordou temas cruciais:Avaliação e segurança da cena: A importância de analisar o local do acidente primeiro, garantindo que quem vai ajudar não se torne mais uma vítima.Manobra de desengasgo (Heimlich): Demonstração prática de como desobstruir as vias aéreas de adultos, crianças e bebês em casos de sufocamento por comida ou objetos.Reanimação Cardiopulmonar (RCP): Treinamento em manequins para aprender o ritmo e a força corretos da massagem cardíaca em casos de parada cardiorrespiratória.Manejo de ferimentos e mitos: Instruções sobre como estancar sangramentos, imobilizar fraturas leves e o que nunca fazer em casos de queimaduras (como aplicar pasta de dente ou borra de café, práticas populares que pioram a lesão).Empoderamento comunitárioPara a coordenação da [Nome da ONG], levar esse tipo de conhecimento para a comunidade é uma forma de exercer a cidadania e proteger as famílias da região, reduzindo o pânico e o medo que costumam travar as pessoas em momentos críticos.\"Em uma emergência, cada segundo conta. O intervalo entre o acidente e a chegada da ambulância é o momento mais crítico. Quando a comunidade sabe o que fazer, as chances de sobrevivência e de recuperação da vítima aumentam drasticamente\", explicou a equipe organizadora.Parcerias e próximos passosO evento contou com o apoio de parceiros locais que viabilizaram os materiais de simulação e o espaço para o treinamento. Diante do grande interesse do público e das vagas esgotadas, a ONG já planeja abrir novas turmas para os próximos meses, incluindo módulos específicos para o cuidado com idosos e acidentes domésticos na infância.A [Nome da ONG] agradece a presença de todos os participantes e o apoio dos profissionais voluntários que compartilharam seus conhecimentos de forma tão generosa.Quer levar esse conhecimento para sua família?Se você perdeu essa oportunidade ou quer apoiar os nossos próximos projetos sociais e educativos, veja como participar:Inscreva-se na lista de espera: Garanta sua vaga para a próxima oficina de primeiros socorros enviando uma mensagem para o nosso WhatsApp [Inserir Telefone].Apoie a ONG: Ajude-nos a manter esses cursos gratuitos fazendo uma doação de qualquer valor ou sendo um parceiro institucional.Siga nossas redes: Fique por dentro de todas as nossas ações pelo Instagram @[Inserir Redes Sociais].', 'banner1.jpg', '2026-09-07 10:00:36'),
(2, 'Eita, trem bão! ONG realiza Festa Junina recheada de sorrisos e tradição para as crianças', 'O nosso arraiá foi bom demais da conta! A ONG realizou uma confraternização especial de Festa Junina para as crianças atendidas pelo projeto.', 'O que rolou no nosso ArraiáBrincadeiras clássicas: Teve pescaria, corrida do saco, jogo das argolas e, claro, muitos prêmios para a criançada.Comilança da boa: Uma mesa farta com pipoca, milho cozido, bolo de fubá, canjica e doces típicos juninos.Quadrilha animada: Os pequenos capricharam nos trajes caipiras, nos vestidos rodados, nos chapéus de palha e deram um show na nossa tradicional dança junina.Mais que uma festa, uma grande famíliaEssa confra junina foi pensada com muito carinho para proporcionar memórias inesquecíveis e fortalecer os laços de amizade entre as crianças e a equipe da ONG. Ver o brilho no olhar e o sorriso de cada um deles reforça o nosso propósito de que ser criança é, acima de tudo, ser feliz.Agradecemos de coração a todos os voluntários, doadores e parceiros que ajudaram a preparar os doces, a decoração e tornaram esse dia tão especial!', 'banner2.jpg', '2026-09-07 10:00:36'),
(3, 'Dia do Orgulho Autista ', 'Em celebração ao Dia do Orgulho Autista, a ONG SOS Tudo Pelo Social lança campanha de conscientização focada na neurodiversidade, respeito e inclusão social. ', 'Orgulho de ser, orgulho de incluir: ONG SOS Tudo Pelo Social celebra o Dia do Orgulho AutistaNo dia 18 de junho, celebra-se mundialmente o Dia do Orgulho Autista, uma data criada para mudar a visão da sociedade em relação ao Transtorno do Espectro Autista (TEA). Longe de ser encarado apenas como uma condição médica, o dia busca celebrar a neurodiversidade, a identidade e as potencialidades únicas de cada indivíduo.Alinhada com esse propósito, a ONG SOS Tudo Pelo Social lançou uma campanha institucional emocionante focada no afeto, no respeito e na verdadeira inclusão. Com o lema \"Celebrar a neurodiversidade é construir um mundo com mais inclusão, respeito e amor\", a organização reforça seu papel de agente transformador na sociedade, lutando para que direitos básicos sejam plenamente assegurados.Respeito não é favor. É direito!A peça central da campanha estampa uma mensagem direta e necessária: o respeito às diferenças não deve ser visto como uma concessão ou ato de caridade, mas como um direito fundamental garantido por lei. A infância e a juventude atípicas precisam encontrar espaços acolhedores em escolas, parques, mercados e em toda a comunidade.A campanha abraça a premissa de que existe \"cada jeito de ser, um jeito único de entender o mundo\". Valorizar o olhar e a percepção de uma pessoa autista enriquece o convívio social e ensina toda a comunidade sobre empatia e paciência.O papel das ferramentas visuais e símbolosA identidade visual adotada pela SOS Tudo Pelo Social utiliza cores vibrantes e o icônico quebra-cabeça colorido — que simboliza a complexidade e a diversidade do espectro autista. O uso desses elementos em corações e camisas ajuda a desmistificar o diagnóstico e traz leveza e afeto para um debate que, muitas vezes, é cercado de preconceitos ou barreiras institucionais.O objetivo principal da divulgação da imagem é ecoar uma voz uníssona: Juntos por um mundo mais acessível para todos!Como fazer parte dessa rede de apoio?A inclusão não acontece apenas em datas comemorativas, ela se constrói no cotidiano. A ONG convida toda a sociedade civil, empresas e voluntários a somarem forças na construção de projetos contínuos de acolhimento e suporte para famílias e pessoas neurodivergentes.Quer apoiar as ações da ONG?Você pode fazer a diferença hoje mesmo ajudando a manter os projetos sociais e as campanhas educativas ativos.Compartilhe esta mensagem: Baixe a imagem oficial e divulgue em suas redes sociais com a hashtag oficial da campanha.Seja um voluntário: Ajude nas ações diárias e eventos promovidos pela nossa equipe.Faça uma doação: Contribua diretamente com a nossa instituição para ampliarmos o número de famílias atendidas.', '6a9ec1b3c8749.jpg', '2026-09-07 10:52:51')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- --------------------------------------------------------
-- Estrutura para tabela `voluntarios`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `voluntarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome_completo` varchar(150) NOT NULL,
  `idade` int(11) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `area_atuacao` varchar(100) NOT NULL,
  `arquivo_curriculo` varchar(255) DEFAULT NULL,
  `data_cadastro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
