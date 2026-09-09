-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 07/09/2026 às 21:23
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `ong`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `apadrinhamentos`
--

CREATE TABLE `apadrinhamentos` (
  `id` int(11) NOT NULL,
  `apoiador_id` int(11) NOT NULL,
  `crianca_id` int(11) NOT NULL,
  `valor_mensal` decimal(10,2) NOT NULL,
  `data_inicio` datetime DEFAULT current_timestamp(),
  `status` enum('ativo','cancelado') DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `apoiadores`
--

CREATE TABLE `apoiadores` (
  `id` int(11) NOT NULL,
  `nome_completo` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `celular` varchar(20) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `sexo` enum('Masculino','Feminino') NOT NULL,
  `cep` varchar(10) NOT NULL,
  `logradouro` varchar(150) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `bairro` varchar(100) NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `estado` char(2) NOT NULL,
  `data_cadastro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `criancas`
--

CREATE TABLE `criancas` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `data_nascimento` date NOT NULL,
  `historico` text DEFAULT NULL,
  `imagem_perfil` varchar(255) DEFAULT NULL,
  `status` enum('disponivel','apadrinhada') DEFAULT 'disponivel',
  `data_cadastro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `doacoes_mensais`
--

CREATE TABLE `doacoes_mensais` (
  `id` int(11) NOT NULL,
  `apoiador_id` int(11) NOT NULL,
  `valor_mensal` decimal(10,2) NOT NULL,
  `dia_vencimento` int(11) NOT NULL,
  `status` enum('ativo','cancelado','inadimplente') DEFAULT 'ativo',
  `data_assinatura` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `doacoes_unicas`
--

CREATE TABLE `doacoes_unicas` (
  `id` int(11) NOT NULL,
  `apoiador_id` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `metodo_pagamento` enum('PIX','Cartão de Crédito','Boleto') NOT NULL,
  `data_doacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `galeria`
--

CREATE TABLE `galeria` (
  `id` int(11) NOT NULL,
  `legenda` varchar(150) DEFAULT NULL,
  `nome_imagem` varchar(255) NOT NULL,
  `data_upload` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `programas_acoes`
--

CREATE TABLE `programas_acoes` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `resumo` varchar(250) NOT NULL,
  `texto_completo` text NOT NULL,
  `categoria` enum('Neuropedagogia','Saúde e Bem-estar','Assistência Social','Educação','Outros') NOT NULL,
  `imagem_capa` varchar(255) NOT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `data_criacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `recompensas_apadrinhamento`
--

CREATE TABLE `recompensas_apadrinhamento` (
  `id` int(11) NOT NULL,
  `apadrinhamento_id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `mensagem` text DEFAULT NULL,
  `arquivo_midia` varchar(255) NOT NULL,
  `data_envio` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `voluntarios`
--

CREATE TABLE `voluntarios` (
  `id` int(11) NOT NULL,
  `apoiador_id` int(11) NOT NULL,
  `area_atuacao` enum('Neuropedagogia','Odontologia','Nutrição','Fisioterapia','Apoio Geral','Outros') NOT NULL,
  `disponibilidade` enum('Manhã','Tarde','Integral') NOT NULL,
  `arquivo_curriculo` varchar(255) NOT NULL,
  `status` enum('em_analise','entrevista_marcada','aprovado','recusado') DEFAULT 'em_analise',
  `data_entrevista` datetime DEFAULT NULL,
  `mensagem_entrevista` text DEFAULT NULL,
  `data_inscricao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `apadrinhamentos`
--
ALTER TABLE `apadrinhamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `apoiador_id` (`apoiador_id`),
  ADD KEY `crianca_id` (`crianca_id`);

--
-- Índices de tabela `apoiadores`
--
ALTER TABLE `apoiadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `cpf` (`cpf`);

--
-- Índices de tabela `criancas`
--
ALTER TABLE `criancas`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `doacoes_mensais`
--
ALTER TABLE `doacoes_mensais`
  ADD PRIMARY KEY (`id`),
  ADD KEY `apoiador_id` (`apoiador_id`);

--
-- Índices de tabela `doacoes_unicas`
--
ALTER TABLE `doacoes_unicas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `apoiador_id` (`apoiador_id`);

--
-- Índices de tabela `galeria`
--
ALTER TABLE `galeria`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `programas_acoes`
--
ALTER TABLE `programas_acoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `recompensas_apadrinhamento`
--
ALTER TABLE `recompensas_apadrinhamento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `apadrinhamento_id` (`apadrinhamento_id`);

--
-- Índices de tabela `voluntarios`
--
ALTER TABLE `voluntarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `apoiador_id` (`apoiador_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `apadrinhamentos`
--
ALTER TABLE `apadrinhamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `apoiadores`
--
ALTER TABLE `apoiadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `criancas`
--
ALTER TABLE `criancas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `doacoes_mensais`
--
ALTER TABLE `doacoes_mensais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `doacoes_unicas`
--
ALTER TABLE `doacoes_unicas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `galeria`
--
ALTER TABLE `galeria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `programas_acoes`
--
ALTER TABLE `programas_acoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `recompensas_apadrinhamento`
--
ALTER TABLE `recompensas_apadrinhamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `voluntarios`
--
ALTER TABLE `voluntarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `apadrinhamentos`
--
ALTER TABLE `apadrinhamentos`
  ADD CONSTRAINT `apadrinhamentos_ibfk_1` FOREIGN KEY (`apoiador_id`) REFERENCES `apoiadores` (`id`),
  ADD CONSTRAINT `apadrinhamentos_ibfk_2` FOREIGN KEY (`crianca_id`) REFERENCES `criancas` (`id`);

--
-- Restrições para tabelas `doacoes_mensais`
--
ALTER TABLE `doacoes_mensais`
  ADD CONSTRAINT `doacoes_mensais_ibfk_1` FOREIGN KEY (`apoiador_id`) REFERENCES `apoiadores` (`id`);

--
-- Restrições para tabelas `doacoes_unicas`
--
ALTER TABLE `doacoes_unicas`
  ADD CONSTRAINT `doacoes_unicas_ibfk_1` FOREIGN KEY (`apoiador_id`) REFERENCES `apoiadores` (`id`);

--
-- Restrições para tabelas `recompensas_apadrinhamento`
--
ALTER TABLE `recompensas_apadrinhamento`
  ADD CONSTRAINT `recompensas_apadrinhamento_ibfk_1` FOREIGN KEY (`apadrinhamento_id`) REFERENCES `apadrinhamentos` (`id`);

--
-- Restrições para tabelas `voluntarios`
--
ALTER TABLE `voluntarios`
  ADD CONSTRAINT `voluntarios_ibfk_1` FOREIGN KEY (`apoiador_id`) REFERENCES `apoiadores` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
