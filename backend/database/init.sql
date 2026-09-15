-- MySQL dump 10.13  Distrib 8.0.46, for Linux (x86_64)
--
-- Host: localhost    Database: ong
-- ------------------------------------------------------
-- Server version	8.0.46

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `ong`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `ong` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `ong`;

--
-- Table structure for table `administradores`
--

DROP TABLE IF EXISTS `administradores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `administradores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `administradores_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `administradores`
--

LOCK TABLES `administradores` WRITE;
/*!40000 ALTER TABLE `administradores` DISABLE KEYS */;
/*!40000 ALTER TABLE `administradores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `apadrinhamentos`
--

DROP TABLE IF EXISTS `apadrinhamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `apadrinhamentos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `apoiador_id` bigint unsigned NOT NULL,
  `crianca_id` bigint unsigned NOT NULL,
  `valor_mensal` decimal(10,2) NOT NULL,
  `data_inicio` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('ativo','cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativo',
  PRIMARY KEY (`id`),
  KEY `apadrinhamentos_apoiador_id_foreign` (`apoiador_id`),
  KEY `apadrinhamentos_crianca_id_foreign` (`crianca_id`),
  CONSTRAINT `apadrinhamentos_apoiador_id_foreign` FOREIGN KEY (`apoiador_id`) REFERENCES `apoiadores` (`id`),
  CONSTRAINT `apadrinhamentos_crianca_id_foreign` FOREIGN KEY (`crianca_id`) REFERENCES `criancas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `apadrinhamentos`
--

LOCK TABLES `apadrinhamentos` WRITE;
/*!40000 ALTER TABLE `apadrinhamentos` DISABLE KEYS */;
INSERT INTO `apadrinhamentos` VALUES (1,2,1,100.00,'2026-09-14 18:53:06','ativo');
/*!40000 ALTER TABLE `apadrinhamentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `apoiadores`
--

DROP TABLE IF EXISTS `apoiadores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `apoiadores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome_completo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpf` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `celular` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sexo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logradouro` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complemento` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_usuario` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'apoiador',
  `data_cadastro` date NOT NULL DEFAULT (curdate()),
  PRIMARY KEY (`id`),
  UNIQUE KEY `apoiadores_email_unique` (`email`),
  UNIQUE KEY `apoiadores_cpf_unique` (`cpf`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `apoiadores`
--

LOCK TABLES `apoiadores` WRITE;
/*!40000 ALTER TABLE `apoiadores` DISABLE KEYS */;
INSERT INTO `apoiadores` VALUES (1,'Jo├úo Silva','joao@email.com','$2y$12$1qTeCK6Up1Nc3MxAFQVgWeMGIlLyKEHDYlyFBT.d41wj.kaNS9t6K','12345678900',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'apoiador','2026-09-12'),(2,'Matheus Figueiredo','matheusfigueiredo949@gmail.com','$2y$10$TNIipK2olHsBFO5Saqu2zuw.0Fe73ABrh2VNHZ59MqLaEGWm0rsC.','12378945610','81985746105','Masculino','54220140','avenida Dolores dura','108','','curado','Jaboat├úo dos Guararapes','PE','apoiador','2026-09-11'),(3,'Danillo roger','nilloroger@gmail.com','$2y$10$cqv4dY15DauS1lpg09VhaO2eWHUM0ONzSnRvS2kO6U9t6jT30OZGy','12378945611','81900112233','Feminino','54220140','avenida Dolores dura','108','','curado','Jaboat├úo dos Guararapes','PE','apoiador','2026-09-11'),(4,'Carol','carol@sos.org.br','$2y$10$LBs3d/jDszUXH5EAIfw5messh59k4N4GOSvmRlCY87LHM.cz7cTEC','','','Masculino','','','',NULL,'','','','admin','2026-09-15');
/*!40000 ALTER TABLE `apoiadores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `criancas`
--

DROP TABLE IF EXISTS `criancas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `criancas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_nascimento` date NOT NULL,
  `historico` text COLLATE utf8mb4_unicode_ci,
  `imagem_perfil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('disponivel','apadrinhada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disponivel',
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `criancas`
--

LOCK TABLES `criancas` WRITE;
/*!40000 ALTER TABLE `criancas` DISABLE KEYS */;
INSERT INTO `criancas` VALUES (1,'Ben Tennyson (Ben 10)','2013-12-27','Menino muito en├®rgico que adora descobertas e aventuras c├│smicas. Precisa de apoio para seus projetos de ci├¬ncias e materiais pedag├│gicos criativos.','ben10.jpg','disponivel','2026-09-14 18:35:58'),(2,'Bart Simpson','2015-04-01','Conhecido por suas travessuras na escola, mas tem um talento art├¡stico incr├¡vel para o skate e grafite. Busca apoio para canalizar toda sua energia em oficinas de arte e refor├ºo escolar.','bart.jpg','disponivel','2026-09-14 18:35:58'),(3,'Chaves','2014-08-15','Morador da vila mais famosa da TV, muito carism├ítico e sonhador. Participa das atividades recreativas e busca apoio alimentar e educacional para o seu desenvolvimento.','chaves.jpg','disponivel','2026-09-14 18:35:58');
/*!40000 ALTER TABLE `criancas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doacoes_mensais`
--

DROP TABLE IF EXISTS `doacoes_mensais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doacoes_mensais` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `apoiador_id` bigint unsigned NOT NULL,
  `valor_mensal` decimal(10,2) NOT NULL,
  `dia_vencimento` int NOT NULL,
  `metodo_pagamento` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pix Autom├ítico',
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativo',
  `data_assinatura` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `doacoes_mensais_apoiador_id_foreign` (`apoiador_id`),
  CONSTRAINT `doacoes_mensais_apoiador_id_foreign` FOREIGN KEY (`apoiador_id`) REFERENCES `apoiadores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doacoes_mensais`
--

LOCK TABLES `doacoes_mensais` WRITE;
/*!40000 ALTER TABLE `doacoes_mensais` DISABLE KEYS */;
INSERT INTO `doacoes_mensais` VALUES (1,3,20.00,20,'Pix Autom├ítico','ativo','2026-09-12 21:07:10'),(2,3,80.00,20,'Pix Autom├ítico','ativo','2026-09-12 21:18:15'),(3,2,40.00,20,'Cart├úo de Cr├®dito (Recorrente)','ativo','2026-09-14 18:47:58');
/*!40000 ALTER TABLE `doacoes_mensais` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doacoes_unicas`
--

DROP TABLE IF EXISTS `doacoes_unicas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doacoes_unicas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `apoiador_id` bigint unsigned NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `metodo_pagamento` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'concluido',
  `data_doacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `doacoes_unicas_apoiador_id_foreign` (`apoiador_id`),
  CONSTRAINT `doacoes_unicas_apoiador_id_foreign` FOREIGN KEY (`apoiador_id`) REFERENCES `apoiadores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doacoes_unicas`
--

LOCK TABLES `doacoes_unicas` WRITE;
/*!40000 ALTER TABLE `doacoes_unicas` DISABLE KEYS */;
INSERT INTO `doacoes_unicas` VALUES (1,3,75.00,'Pix','concluido','2026-09-11 11:25:35'),(2,3,10.00,'Pix','concluido','2026-09-11 11:26:30'),(3,2,10.00,'Pix','concluido','2026-09-14 18:47:46');
/*!40000 ALTER TABLE `doacoes_unicas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documentos_transparencia`
--

DROP TABLE IF EXISTS `documentos_transparencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documentos_transparencia` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ano_referencia` int NOT NULL,
  `tipo_documento` enum('Relat├│rio Anual','Balancete','Estatuto','Certid├úo','Outros') COLLATE utf8mb4_unicode_ci NOT NULL,
  `arquivo_pdf` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_upload` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documentos_transparencia`
--

LOCK TABLES `documentos_transparencia` WRITE;
/*!40000 ALTER TABLE `documentos_transparencia` DISABLE KEYS */;
/*!40000 ALTER TABLE `documentos_transparencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `galeria`
--

DROP TABLE IF EXISTS `galeria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `galeria` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `legenda` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nome_imagem` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_upload` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `galeria`
--

LOCK TABLES `galeria` WRITE;
/*!40000 ALTER TABLE `galeria` DISABLE KEYS */;
INSERT INTO `galeria` VALUES (1,'Logo da ONG','logo.png','2026-09-15 16:00:00');
/*!40000 ALTER TABLE `galeria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `materiais_didaticos`
--

DROP TABLE IF EXISTS `materiais_didaticos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `materiais_didaticos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `arquivo_pdf` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `imagem_capa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categoria` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Neuropedagogia',
  `data_upload` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `materiais_didaticos`
--

LOCK TABLES `materiais_didaticos` WRITE;
/*!40000 ALTER TABLE `materiais_didaticos` DISABLE KEYS */;
/*!40000 ALTER TABLE `materiais_didaticos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_09_11_195652_create_apoiadores_table',1),(5,'2026_09_15_000001_create_ong_content_tables',2),(6,'2026_09_15_000002_create_ong_admin_tables',2),(7,'2026_09_15_000003_add_tipo_usuario_to_apoiadores_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `newsletter`
--

DROP TABLE IF EXISTS `newsletter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `newsletter` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_inscricao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `newsletter_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `newsletter`
--

LOCK TABLES `newsletter` WRITE;
/*!40000 ALTER TABLE `newsletter` DISABLE KEYS */;
/*!40000 ALTER TABLE `newsletter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `noticias`
--

DROP TABLE IF EXISTS `noticias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `noticias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `resumo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `texto_completo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `imagem` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` enum('noticia','evento','campanha') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'noticia',
  `data_evento` timestamp NULL DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `noticias`
--

LOCK TABLES `noticias` WRITE;
/*!40000 ALTER TABLE `noticias` DISABLE KEYS */;
/*!40000 ALTER TABLE `noticias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `programas_acoes`
--

DROP TABLE IF EXISTS `programas_acoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `programas_acoes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `resumo` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `texto_completo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria` enum('Neuropedagogia','Sa├║de e Bem-estar','Assist├¬ncia Social','Educa├º├úo','Outros') COLLATE utf8mb4_unicode_ci NOT NULL,
  `imagem_capa` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('ativo','inativo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativo',
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `programas_acoes`
--

LOCK TABLES `programas_acoes` WRITE;
/*!40000 ALTER TABLE `programas_acoes` DISABLE KEYS */;
INSERT INTO `programas_acoes` VALUES (1,'Lute como uma M├úe At├¡pica','Projeto oferece rede de apoio e empreendedorismo para m├úes de crian├ºas neurodivergentes','Com foco na sa├║de mental e na independ├¬ncia financeira, iniciativa da ONG SOS Tudo pelo Social transforma a realidade de mulheres que dedicam suas vidas ao cuidado de filhos com necessidades espec├¡ficas, promovendo acolhimento e capacita├º├úo. A iniciativa oferece apoio psicol├│gico, rodas de di├ílogo e oficinas de empreendedorismo para as m├úes at├¡picas.','Assist├¬ncia Social','materia_1789498189.jpg','ativo','2026-09-15 18:00:00');
/*!40000 ALTER TABLE `programas_acoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recompensas_apadrinhamento`
--

DROP TABLE IF EXISTS `recompensas_apadrinhamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recompensas_apadrinhamento` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `apadrinhamento_id` bigint unsigned NOT NULL,
  `titulo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensagem` text COLLATE utf8mb4_unicode_ci,
  `arquivo_midia` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_envio` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `recompensas_apadrinhamento_apadrinhamento_id_foreign` (`apadrinhamento_id`),
  CONSTRAINT `recompensas_apadrinhamento_apadrinhamento_id_foreign` FOREIGN KEY (`apadrinhamento_id`) REFERENCES `apadrinhamentos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recompensas_apadrinhamento`
--

LOCK TABLES `recompensas_apadrinhamento` WRITE;
/*!40000 ALTER TABLE `recompensas_apadrinhamento` DISABLE KEYS */;
INSERT INTO `recompensas_apadrinhamento` VALUES (1,1,'t├í na hora de virar her├│i','E a├¡, beleza? Aqui ├® o Ben Tennyson. Fiquei sabendo que voc├¬ ├® um grande f├ú das minhas aventuras... Continue sendo esse f├ú incr├¡vel, respeitando seus pais, estudando bastante e ajudando quem precisa. Um grande abra├ºo do seu amigo, Ben 10','recompensa_1789499207.png','2026-09-15 16:06:47');
/*!40000 ALTER TABLE `recompensas_apadrinhamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('aRgiPFrijWA3v3TckQhxXZOv1hrWNItm2pU6Sxia',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiI0Y05LZzlObEpLZ0V0NHNXNnlrQlEzQ2xKb2NUTFVJTkVYcTVEaXBrIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hcGlcL3Byb2dyYW1hcyIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1789504356),('d6ZuFaANHIQ1fXNNkhZcCsp5YcMd4jfrUCpAOTAH',NULL,'127.0.0.1','curl/8.21.0','eyJfdG9rZW4iOiJGaVpDOUJITFphVjRBbDhESmxFYVA4UTBlNUF4d1owV05ncG9GZGFMIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hcGlcL2Fwb2lhZG9yZXMiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1789243267),('FvTSfwkokGb4i6qi9s8uo3buNYiw9uCKhcIzleMS',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJRN2NGNzdZMXNVOHRGdU42VkkzSkZPa2FzeGdweWs2QmduRUplSFBSIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hcGlcL2FwYWRyaW5oYW1lbnRvcyIsInJvdXRlIjpudWxsfSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1789504356),('gJyexoAkiP2jTM5n6uNZtz4PyJqYZdcSqNFDH5dt',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJhalJJSXdJTWVZdXZSR25mcWJTVllQYURjNXJ5dEI5SHd2TmNPcWN4IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hcGlcL2NyaWFuY2FzIiwicm91dGUiOm51bGx9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19',1789504353),('HTzm7cjQVHFgPVh86HwE2EcFn847RmPqqcj1nZhZ',NULL,'127.0.0.1','curl/8.21.0','eyJfdG9rZW4iOiJHV3hhZjQzWm1wQXFFaWo4cTlXY2Z6SlE0bDduRjgxTVNvWWhOZTJ6IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hcGlcL2Fwb2lhZG9yZXMiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1789242866),('j6OMhxaSsX9Qx9IowoXdzG3NZYvSlhsdC4SqlZH3',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; pt-BR) WindowsPowerShell/5.1.26100.9444','eyJfdG9rZW4iOiJNeE1hVG1PTFk2QUpsNnJhcWxzZXBuMDlqTE11T1AzdmlwQ2dGWU5HIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hcGlcL2Fwb2lhZG9yZXMiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1789504353),('o52xg5CSxhMzSsLd2J9NjvaCmX0MR7Ui9yScig4w',NULL,'127.0.0.1','curl/8.21.0','eyJfdG9rZW4iOiJxb0Z1dU55MnJydFN0aEhDUFJ5MlFzUUFFN2pVNlJWc2luTDVDZjlUIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hcGlcL2Fwb2lhZG9yZXMiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1789243219),('y4wmEvFi3j4l91CaugavhzPl01LvJem55ruMl5wN',NULL,'127.0.0.1','curl/8.21.0','eyJfdG9rZW4iOiJGTkdhanlFTjJNcVZSa0tvQlpiRHBRdHpXT3o5VkhuVmVFSHQzcWsyIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hcGlcL2Fwb2lhZG9yZXMiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1789242775),('yd9tJ6sjQC6EqdsCJlGsCKtGozue3FHis27LTRw3',NULL,'127.0.0.1','curl/8.21.0','eyJfdG9rZW4iOiJmWkVJVWc1Y0dBRWJ5Yko1dzI3UWFKUGo4akNBRUxQS2lwZkNZeEpCIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hcGlcL2Fwb2lhZG9yZXMiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1789243657),('YIWiWRI9Iqkp3ZcVDyV9nVYEHS3g8k2fOSCK2WhR',NULL,'127.0.0.1','curl/8.21.0','eyJfdG9rZW4iOiJOc1hxcFVLMzdUQUpLdWMxWUFZdzFKdmp0Z3NtZzR3Qk5qNDJ4T1VnIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9hcGlcL2Fwb2lhZG9yZXMiLCJyb3V0ZSI6bnVsbH0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1789243633);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `voluntarios`
--

DROP TABLE IF EXISTS `voluntarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `voluntarios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `apoiador_id` bigint unsigned NOT NULL,
  `area_atuacao` enum('Neuropedagogia','Odontologia','Nutri├º├úo','Fisioterapia','Apoio Geral','Outros') COLLATE utf8mb4_unicode_ci NOT NULL,
  `disponibilidade` enum('Manh├ú','Tarde','Integral') COLLATE utf8mb4_unicode_ci NOT NULL,
  `arquivo_curriculo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `status` enum('em_analise','entrevista_marcada','aprovado','recusado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'em_analise',
  `data_entrevista` timestamp NULL DEFAULT NULL,
  `mensagem_entrevista` text COLLATE utf8mb4_unicode_ci,
  `data_inscricao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `voluntarios_apoiador_id_foreign` (`apoiador_id`),
  CONSTRAINT `voluntarios_apoiador_id_foreign` FOREIGN KEY (`apoiador_id`) REFERENCES `apoiadores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voluntarios`
--

LOCK TABLES `voluntarios` WRITE;
/*!40000 ALTER TABLE `voluntarios` DISABLE KEYS */;
INSERT INTO `voluntarios` VALUES (1,3,'Outros','Manh├ú','','em_analise',NULL,'por favor, tente de novo e envie seu curr├¡culo','2026-09-14 18:14:11'),(2,2,'Outros','Manh├ú','','aprovado','2026-10-27 14:00:00','esperando voc├¬','2026-09-14 18:48:24');
/*!40000 ALTER TABLE `voluntarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-15 20:32:59
