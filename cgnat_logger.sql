-- MySQL dump 10.13  Distrib 8.0.36, for Linux (x86_64)
--
-- Host: localhost    Database: cgnat_logger
-- ------------------------------------------------------
-- Server version	8.0.36-0ubuntu0.20.04.1

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
-- Table structure for table `ADMIN`
--

DROP TABLE IF EXISTS `ADMIN`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ADMIN` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `reg_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ADMIN`
--

LOCK TABLES `ADMIN` WRITE;
/*!40000 ALTER TABLE `ADMIN` DISABLE KEYS */;
INSERT INTO `ADMIN` VALUES (1,'admin','$2y$10$eaucYcHqQQvEnStzsLR/l.im3MHR4S.19ax0JOtzEN4a95c2HuAi6','seuemail@email.com','2024-03-12 23:36:51');
/*!40000 ALTER TABLE `ADMIN` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `LOGS_CGNAT`
--

DROP TABLE IF EXISTS `LOGS_CGNAT`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `LOGS_CGNAT` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `data_hora` datetime NOT NULL,
  `ip_bras` varchar(15) DEFAULT NULL,
  `firewall_log` varchar(255) DEFAULT NULL,
  `dados_cgnat` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `LOGS_CGNAT`
--

LOCK TABLES `LOGS_CGNAT` WRITE;
/*!40000 ALTER TABLE `LOGS_CGNAT` DISABLE KEYS */;
/*!40000 ALTER TABLE `LOGS_CGNAT` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-03-12 20:37:07
