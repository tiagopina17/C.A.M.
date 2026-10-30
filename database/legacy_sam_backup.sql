-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: sam
-- ------------------------------------------------------
-- Server version	8.4.3

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
-- Table structure for table `avaliação`
--

DROP TABLE IF EXISTS `avaliação`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `avaliação` (
  `ref_Utilizadores` int NOT NULL,
  `classificacao` int NOT NULL,
  `Comentario` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  KEY `fk_Avaliação_Utilizadores1_idx` (`ref_Utilizadores`),
  CONSTRAINT `fk_Avaliação_Utilizadores1` FOREIGN KEY (`ref_Utilizadores`) REFERENCES `utilizadores` (`id_Utilizadores`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `avaliação`
--

LOCK TABLES `avaliação` WRITE;
/*!40000 ALTER TABLE `avaliação` DISABLE KEYS */;
/*!40000 ALTER TABLE `avaliação` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id_Categorias` int NOT NULL AUTO_INCREMENT,
  `Nome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_Categorias`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Higiene'),(2,'Alimentos');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contactos`
--

DROP TABLE IF EXISTS `contactos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contactos` (
  `id_contacto` int NOT NULL AUTO_INCREMENT,
  `tema` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `detalhes` varchar(1000) COLLATE utf8mb4_general_ci NOT NULL,
  `ficheiro` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_contacto`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contactos`
--

LOCK TABLES `contactos` WRITE;
/*!40000 ALTER TABLE `contactos` DISABLE KEYS */;
INSERT INTO `contactos` VALUES (1,'duvida','aaaaaaaaa',NULL,'aaaaaaaaaaaaaaaa@gmo.com'),(2,'duvida','aaaaaaaaa',NULL,'aaaaaaaaaaaaaaaa@gmo.com');
/*!40000 ALTER TABLE `contactos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funcionarios`
--

DROP TABLE IF EXISTS `funcionarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `funcionarios` (
  `id_Funcionarios` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `ref_id_Funcionarios_Tipos` int NOT NULL,
  `imgperfil` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `inicio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_Funcionarios`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_Funcionarios_Tipos_idx` (`ref_id_Funcionarios_Tipos`),
  CONSTRAINT `fk_Funcionarios_Tipos` FOREIGN KEY (`ref_id_Funcionarios_Tipos`) REFERENCES `funcionarios_tipos` (`id_Funcionarios_Tipos`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funcionarios`
--

LOCK TABLES `funcionarios` WRITE;
/*!40000 ALTER TABLE `funcionarios` DISABLE KEYS */;
INSERT INTO `funcionarios` VALUES (9,'Tiago Pina','tiagopina2004@gmail.comsaa','$2y$10$c4jh8CjfxxrMpbInfMfRNejQZQ7WOrmox9NMQdvlaoOAwK17qdVXG',1,NULL,'2025-06-04 03:57:39'),(11,'cara','tiagopina2004@gmail.ccara','$2y$10$GUFKjeKvcDGkMzRrdhiFTuOVnqezEvqmmPqc4zCjcpUOU9gutlxqa',2,NULL,'2025-07-04 03:03:01'),(12,'Tiago Pina','tiagopina2004@gmail.cona','$2y$10$tSqGorZHiQ09GXnu5kBe/unPe7bx8uQ42zAgaD5S/2b2UHygjObBG',1,NULL,'2025-07-23 02:27:08'),(15,'A','tiagopina2004@gmail.comesme','$2y$10$6TE6.yhsN7GxFEBfnLB2ru.4edjQEya/qLlplpReNIdAywwdIFnwO',2,NULL,'2025-07-24 19:43:49'),(16,'alcv3344','ana@gmail.com','$2y$10$n/Ebj1.pydOQZqT6Yw8wVOrArvyRc/blqiRPjEjX.IhLpdcSfjn62',2,NULL,'2025-07-24 20:06:00'),(17,'Tiago Pina','tiagopina2004@gmail.conaafvwfw','$2y$10$i6sn0XPMcPXugR.OybPzUOhL2wOLlVxy5FbqhHkz5rntAUaPGkyk6',1,NULL,'2025-07-24 20:10:05'),(20,'aaaaaa','tiagopina2004@gmail.comii','$2y$10$fcyKaBSo64dTtMwfRsVga.tQqiPMlnyY0Fm2cgKxQp5rMQOv7XPhm',1,NULL,'2025-10-02 16:00:22'),(21,'Tiago Pina','tiagopina2004@gmail.comiii','$2y$10$LJszkb7.nzscPO1fEHsc7Oxn2rboxvZjI74Ow5dg7/RZc7FQu2Zl6',1,NULL,'2025-10-02 16:02:15'),(22,'Tiago Pina','tiagopina2004@gmail.comi','$argon2id$v=19$m=65536,t=4,p=3$VlVWLnRwR2FTLmhDYWIzYQ$5ZaFtm+WE3lU6hhjSxBuRznWXT83qXPmCtYD8jmud3M',2,NULL,'2025-10-03 17:54:44'),(23,'Tiago Pina','tiagopina2004@gmail.com','$argon2id$v=19$m=65536,t=4,p=3$R1ByRkIzWDRzclhaZU1QWg$QRvGwDXv+0VQ15bALRu9X6YB/qD2kVOFkpmM7cU5fCI',1,NULL,'2026-08-25 01:58:28');
/*!40000 ALTER TABLE `funcionarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funcionarios_codigo`
--

DROP TABLE IF EXISTS `funcionarios_codigo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `funcionarios_codigo` (
  `id_funcionarios_codigo` int NOT NULL AUTO_INCREMENT,
  `ref_id_Loja` smallint NOT NULL,
  `codigo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_funcionarios_codigo`),
  KEY `fk_funcionarios_codigo_lojas_idx` (`ref_id_Loja`),
  CONSTRAINT `fk_funcionarios_codigo_lojas` FOREIGN KEY (`ref_id_Loja`) REFERENCES `lojas` (`id_Loja`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funcionarios_codigo`
--

LOCK TABLES `funcionarios_codigo` WRITE;
/*!40000 ALTER TABLE `funcionarios_codigo` DISABLE KEYS */;
/*!40000 ALTER TABLE `funcionarios_codigo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funcionarios_lojas`
--

DROP TABLE IF EXISTS `funcionarios_lojas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `funcionarios_lojas` (
  `ref_id_Funcionario` int NOT NULL,
  `ref_id_Loja` smallint NOT NULL,
  `inicio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ref_id_Funcionario`,`ref_id_Loja`),
  KEY `fk_Funcionarios_has_Lojas_Lojas_idx` (`ref_id_Loja`),
  KEY `fk_Funcionarios_has_Lojas_Funcionarios_idx` (`ref_id_Funcionario`),
  CONSTRAINT `fk_Funcionarios_has_Lojas_Funcionarios` FOREIGN KEY (`ref_id_Funcionario`) REFERENCES `funcionarios` (`id_Funcionarios`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Funcionarios_has_Lojas_Lojas` FOREIGN KEY (`ref_id_Loja`) REFERENCES `lojas` (`id_Loja`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funcionarios_lojas`
--

LOCK TABLES `funcionarios_lojas` WRITE;
/*!40000 ALTER TABLE `funcionarios_lojas` DISABLE KEYS */;
INSERT INTO `funcionarios_lojas` VALUES (9,13,'2025-06-04 03:57:39'),(11,13,'2025-07-04 03:03:01'),(12,15,'2025-07-23 02:27:08'),(15,16,'2025-07-24 19:43:49'),(16,16,'2025-07-24 20:06:00'),(17,17,'2025-07-24 20:10:05'),(20,20,'2025-10-02 16:00:22'),(21,21,'2025-10-02 16:02:15'),(22,20,'2025-10-03 17:54:45'),(23,22,'2026-08-25 01:58:28');
/*!40000 ALTER TABLE `funcionarios_lojas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `funcionarios_tipos`
--

DROP TABLE IF EXISTS `funcionarios_tipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `funcionarios_tipos` (
  `id_Funcionarios_Tipos` int NOT NULL,
  `nome` text COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_Funcionarios_Tipos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `funcionarios_tipos`
--

LOCK TABLES `funcionarios_tipos` WRITE;
/*!40000 ALTER TABLE `funcionarios_tipos` DISABLE KEYS */;
INSERT INTO `funcionarios_tipos` VALUES (1,'Proprietário'),(2,'Funcionário');
/*!40000 ALTER TABLE `funcionarios_tipos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lojas`
--

DROP TABLE IF EXISTS `lojas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lojas` (
  `id_Loja` smallint NOT NULL AUTO_INCREMENT,
  `nome_loja` text COLLATE utf8mb4_general_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_general_ci,
  `place_id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `lat` double NOT NULL,
  `lon` double NOT NULL,
  `imgperfil` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `inicio` datetime NOT NULL,
  PRIMARY KEY (`id_Loja`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lojas`
--

LOCK TABLES `lojas` WRITE;
/*!40000 ALTER TABLE `lojas` DISABLE KEYS */;
INSERT INTO `lojas` VALUES (13,'ah vcrjra','yups','ChIJH5BcfsLvJA0Ryeum7J8zKK8',41.4413192,-8.2950212,NULL,'2025-06-04 03:57:39'),(15,'v3vaev','ciuack','ChIJ33DE9llHJA0R9QbTSQ_wAMc',41.3792572,-8.7623844,NULL,'2025-07-23 02:27:08'),(16,'limasa','onete','ChIJY2l6vk6tJA0RxuK7JBAzDdo',41.1642102,-7.7928465,NULL,'2025-07-23 19:21:23'),(17,'coisas','ec ans','ChIJf4QFSSTNGg0R9GcqWiduEtQ',37.0832195,-8.2768171,NULL,'2025-07-24 20:10:05'),(18,'a','a','ChIJLfpHKDEvGw0RZTNAmn3ZLS4',37.1299506,-8.5802307,NULL,'2025-10-02 15:53:31'),(19,'123','456','ChIJAR0w3x4zGQ0RJokduDahSp8',38.748831,-9.16493,NULL,'2025-10-02 15:56:17'),(20,'arco','e','ChIJFQdeEXSGIw0RQmqlTzaDQns',40.8814991,-8.6331148,NULL,'2025-10-02 16:00:22'),(21,'uni','ar','ChIJR2YzxXczGQ0RS9MqmH5xG0A',38.7250968,-9.1462745,NULL,'2025-10-02 16:02:15'),(22,'123','123','ChIJWWDWD4IzGQ0R4FDvd_kI3mw',38.719172,-9.1438643,NULL,'2026-08-25 01:58:28');
/*!40000 ALTER TABLE `lojas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produtos`
--

DROP TABLE IF EXISTS `produtos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produtos` (
  `id_Produtos` int NOT NULL AUTO_INCREMENT,
  `ref_id_Categorias` int NOT NULL,
  `nome` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `capa` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ref_id_subcategorias` int DEFAULT NULL,
  PRIMARY KEY (`id_Produtos`) USING BTREE,
  KEY `fk_Serviços_Categorias_idx` (`ref_id_Categorias`),
  KEY `fk_servicos_subcategorias` (`ref_id_subcategorias`) USING BTREE,
  CONSTRAINT `fk_servicos_subcategorias` FOREIGN KEY (`ref_id_subcategorias`) REFERENCES `subcategorias` (`id_subcategoria`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Serviços_Categorias` FOREIGN KEY (`ref_id_Categorias`) REFERENCES `categorias` (`id_Categorias`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produtos`
--

LOCK TABLES `produtos` WRITE;
/*!40000 ALTER TABLE `produtos` DISABLE KEYS */;
/*!40000 ALTER TABLE `produtos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subcategorias`
--

DROP TABLE IF EXISTS `subcategorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subcategorias` (
  `id_subcategoria` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `id_Categorias` int NOT NULL,
  PRIMARY KEY (`id_subcategoria`),
  KEY `id_Categorias` (`id_Categorias`),
  CONSTRAINT `fk_subcategorias_categorias` FOREIGN KEY (`id_Categorias`) REFERENCES `categorias` (`id_Categorias`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subcategorias`
--

LOCK TABLES `subcategorias` WRITE;
/*!40000 ALTER TABLE `subcategorias` DISABLE KEYS */;
INSERT INTO `subcategorias` VALUES (1,'cena1',1);
/*!40000 ALTER TABLE `subcategorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos`
--

DROP TABLE IF EXISTS `tipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos` (
  `id_Tipos` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_Tipos`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos`
--

LOCK TABLES `tipos` WRITE;
/*!40000 ALTER TABLE `tipos` DISABLE KEYS */;
INSERT INTO `tipos` VALUES (1,'utilizador'),(3,'moderador'),(4,'administrador');
/*!40000 ALTER TABLE `tipos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilizadores`
--

DROP TABLE IF EXISTS `utilizadores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `utilizadores` (
  `id_Utilizadores` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `imgperfil` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ref_id_Tipos` int NOT NULL,
  `inicio` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_Utilizadores`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_Utilizadores_Tipos1_idx` (`ref_id_Tipos`),
  CONSTRAINT `fk_Utilizadores_Tipos1` FOREIGN KEY (`ref_id_Tipos`) REFERENCES `tipos` (`id_Tipos`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilizadores`
--

LOCK TABLES `utilizadores` WRITE;
/*!40000 ALTER TABLE `utilizadores` DISABLE KEYS */;
INSERT INTO `utilizadores` VALUES (2,'Tiagouser','tiagouser@soullindo.comm','$2y$10$dOOx0SPaVlk83ygV/7k54Oisnhf83ISm2OFeiJ3zDuJxa5S.rt.Ki',NULL,1,'2024-07-27 01:21:36'),(10,'Tiago Pina','tiagopina2004@gmail.coms','$2y$10$DgzpFHLBCN3omLtywoSlCuBeWYeXuoDYqGcj5Ch85pWgroW7kVL9q',NULL,4,'2025-04-26 15:32:44'),(21,'aaaa','tiagopina2004@gmail.comsaaaa','$2y$10$8Csqs3gOPO45V6fY.pBGwOBUmEBxRtmyDr8dS7pFmuxZY0UW9IkdO',NULL,1,'2025-07-04 02:56:36'),(40,'1wqedwr','tiagopina2004@gmail.com','$argon2id$v=19$m=65536,t=4,p=3$V3RVS1QyclFEdU5DL1NjYw$xpDyaAXJTncuf3fHg0NMwiNvW+NBKKSmt4h82gnVMqs',NULL,1,'2026-08-25 02:00:03');
/*!40000 ALTER TABLE `utilizadores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilizadores_lojas`
--

DROP TABLE IF EXISTS `utilizadores_lojas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `utilizadores_lojas` (
  `ref_id_Utilizador` int NOT NULL,
  `ref_id_Loja` smallint NOT NULL,
  PRIMARY KEY (`ref_id_Utilizador`,`ref_id_Loja`),
  KEY `fk_Utilizador_has_Lojas_Lojas_idx` (`ref_id_Loja`),
  KEY `fk_Utilizador_has_Lojas_Utilizador_idx` (`ref_id_Utilizador`),
  CONSTRAINT `fk_Utilizador_has_Lojas_Lojas` FOREIGN KEY (`ref_id_Loja`) REFERENCES `lojas` (`id_Loja`),
  CONSTRAINT `fk_Utilizador_has_Lojas_Utilizador` FOREIGN KEY (`ref_id_Utilizador`) REFERENCES `utilizadores` (`id_Utilizadores`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilizadores_lojas`
--

LOCK TABLES `utilizadores_lojas` WRITE;
/*!40000 ALTER TABLE `utilizadores_lojas` DISABLE KEYS */;
/*!40000 ALTER TABLE `utilizadores_lojas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-23 23:36:26
