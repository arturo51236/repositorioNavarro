-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: RubiksHub
-- ------------------------------------------------------
-- Server version	5.5.5-10.11.6-MariaDB-0+deb12u1

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
-- Table structure for table `acceso`
--

DROP TABLE IF EXISTS `acceso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `acceso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `uuid` varchar(180) NOT NULL,
  `fecha` datetime NOT NULL,
  `resultado` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1268771BDB38439E` (`usuario_id`),
  CONSTRAINT `FK_1268771BDB38439E` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acceso`
--

LOCK TABLES `acceso` WRITE;
/*!40000 ALTER TABLE `acceso` DISABLE KEYS */;
INSERT INTO `acceso` VALUES (1,1,'019011b5-0349-7980-8fa9-d563f7efc6f6','2024-06-13 13:06:33','Success'),(2,1,'01901de2-1c15-7bff-b24c-c273f415d8e7','2024-06-15 21:51:15','Success'),(3,NULL,'01901e61-4ae2-77e3-b37d-2bef4ab35341','2024-06-16 00:10:11','Failure'),(4,3,'01901e61-681d-7cd5-98a5-cbc06507ffbe','2024-06-16 00:10:18','Success');
/*!40000 ALTER TABLE `acceso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categoria`
--

DROP TABLE IF EXISTS `categoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categoria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(180) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `foto` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoria`
--

LOCK TABLES `categoria` WRITE;
/*!40000 ALTER TABLE `categoria` DISABLE KEYS */;
INSERT INTO `categoria` VALUES (4,'019011c1-5d07-70ee-bb88-b90c8e4e11a7','Octaedros','1666af20351ffd.png'),(5,'019011c2-b499-726f-ab36-abe1d848a9a6','Square-1','1666af25b46b50.png'),(6,'019011ca-e6e9-7869-82da-67083db37dd7','Prismas Pentagonales','1666af4746fb4f.png'),(9,'019011cb-b770-7160-998b-e286ecf5f63b','Otros','1666af4a9cd20a.png');
/*!40000 ALTER TABLE `categoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctrine_migration_versions`
--

LOCK TABLES `doctrine_migration_versions` WRITE;
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
INSERT INTO `doctrine_migration_versions` VALUES ('DoctrineMigrations\\Version20240613111924','2024-06-13 12:58:36',472);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estado`
--

DROP TABLE IF EXISTS `estado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estado` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(180) NOT NULL,
  `nombre` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estado`
--

LOCK TABLES `estado` WRITE;
/*!40000 ALTER TABLE `estado` DISABLE KEYS */;
INSERT INTO `estado` VALUES (1,'019017c3-efd4-70ac-ad91-368ec81abd9c','No confirmado'),(2,'019017c4-0a80-7757-80c7-3aae91dfe9fd','Confirmado'),(3,'019017c4-32ab-722a-b924-7399f5e6faba','Enviado'),(4,'019017c4-5136-70ae-9877-33506fb88331','Recibido');
/*!40000 ALTER TABLE `estado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fabricante`
--

DROP TABLE IF EXISTS `fabricante`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fabricante` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(180) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `foto` varchar(100) DEFAULT NULL,
  `descripcion` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fabricante`
--

LOCK TABLES `fabricante` WRITE;
/*!40000 ALTER TABLE `fabricante` DISABLE KEYS */;
INSERT INTO `fabricante` VALUES (1,'019011b7-00c4-7db7-a311-b5bec2a35d31','Rubik\'s Hub','1666aef5c57149.png','Nuestra compañía, fundada en el año XXXX, lleva detrás del diseño y la fabricación de cubos de Rubik artesanales desde hace 7 años.\r\nTratamos de ofrecer la mejor calidad en los productos y la felicidad en nuestros clientes cuándo los reciben.'),(2,'01901d2c-2bd1-7268-9d3a-701f4b0929e0','Moyu','1666dde4071a19.jpg','Compañía de cubos de Rubik, principalmente para speedcube aunque en los últimos años ha ido fabricando diversas modificaciones de una extrema calidad.'),(3,'01901d6f-8d58-76c3-9718-52782218c330','Qiyi','1666def8054396.jpg','Una reciente marca que está sorprendiendo con sus cubos, los speed cubers los comparan en calidad y funcionamiento con los Moyu.\r\nTienen un excelente Skewb y Pyraminx que han gustado mucho a los speed cubers.'),(4,'01901d70-8689-7ba4-aeb6-b62fc66a211a','Dayan','1666defc021ad1.png','Sin duda otra gran marca de Cubos de Rubik, tiene varias versiones del Cubo de Rubik 3×3, pero la más conocida es Dayan Zanchi V 3×3, Feliks Zemdegs consiguió un tiempo de 5.66 segundos con este cubo y fue WR durante años.');
/*!40000 ALTER TABLE `fabricante` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `linea_pedido`
--

DROP TABLE IF EXISTS `linea_pedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `linea_pedido` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `uuid` varchar(180) NOT NULL,
  `cantidad` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_183C31657645698E` (`producto_id`),
  KEY `IDX_183C31654854653A` (`pedido_id`),
  CONSTRAINT `FK_183C31654854653A` FOREIGN KEY (`pedido_id`) REFERENCES `pedido` (`id`),
  CONSTRAINT `FK_183C31657645698E` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `linea_pedido`
--

LOCK TABLES `linea_pedido` WRITE;
/*!40000 ALTER TABLE `linea_pedido` DISABLE KEYS */;
INSERT INTO `linea_pedido` VALUES (1,3,9,'019018ae-e9dd-7021-9556-ffe6ddd08b76',1),(2,1,10,'019018e2-a541-73b2-a4cd-342155092f56',3),(3,4,10,'019018e2-a587-7904-998a-e90abdd74e96',1),(4,1,11,'01901cda-46d0-7c99-ada3-a4a22bdf7bb3',2),(5,3,11,'01901cda-4709-7559-a805-b109ea899f84',1);
/*!40000 ALTER TABLE `linea_pedido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `memoria_cesta`
--

DROP TABLE IF EXISTS `memoria_cesta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `memoria_cesta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `uuid` varchar(180) NOT NULL,
  `cantidad` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7925FF51DB38439E` (`usuario_id`),
  KEY `IDX_7925FF517645698E` (`producto_id`),
  CONSTRAINT `FK_7925FF517645698E` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`id`),
  CONSTRAINT `FK_7925FF51DB38439E` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `memoria_cesta`
--

LOCK TABLES `memoria_cesta` WRITE;
/*!40000 ALTER TABLE `memoria_cesta` DISABLE KEYS */;
/*!40000 ALTER TABLE `memoria_cesta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedido`
--

DROP TABLE IF EXISTS `pedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedido` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estado_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `uuid` varchar(180) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C4EC16CE9F5A440B` (`estado_id`),
  KEY `IDX_C4EC16CEDB38439E` (`usuario_id`),
  CONSTRAINT `FK_C4EC16CE9F5A440B` FOREIGN KEY (`estado_id`) REFERENCES `estado` (`id`),
  CONSTRAINT `FK_C4EC16CEDB38439E` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido`
--

LOCK TABLES `pedido` WRITE;
/*!40000 ALTER TABLE `pedido` DISABLE KEYS */;
INSERT INTO `pedido` VALUES (9,2,1,'019018ae-e998-7f06-87b5-4276471acad6','2024-06-14 21:37:14'),(10,1,1,'019018e2-a4d4-7226-94b9-ae8e0a79d62e','2024-06-14 22:33:44'),(11,1,1,'01901cda-4559-7834-ae69-590156fe3d83','2024-06-15 17:03:05');
/*!40000 ALTER TABLE `pedido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `producto`
--

DROP TABLE IF EXISTS `producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `producto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria_id` int(11) NOT NULL,
  `fabricante_id` int(11) NOT NULL,
  `uuid` varchar(180) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `precio` int(11) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `disponibilidad` int(11) NOT NULL,
  `fotos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`fotos`)),
  `diseno_propio` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_A7BB06153397707A` (`categoria_id`),
  KEY `IDX_A7BB0615C0A0FDFA` (`fabricante_id`),
  CONSTRAINT `FK_A7BB06153397707A` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id`),
  CONSTRAINT `FK_A7BB0615C0A0FDFA` FOREIGN KEY (`fabricante_id`) REFERENCES `fabricante` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `producto`
--

LOCK TABLES `producto` WRITE;
/*!40000 ALTER TABLE `producto` DISABLE KEYS */;
INSERT INTO `producto` VALUES (1,9,1,'019011cc-d3fe-7b13-8886-0035a31dfc9a','Cubo X',20,'',15,'[\"1666af4f2a8d38.jpg\",\"1666af4f2aef0b.jpg\",\"1666af4f2b3cd0.jpg\",\"1666af4f2b8811.jpg\"]',1),(2,5,1,'019011cd-46fd-7a29-bce4-d6502910e814','Square-1 de 5 niveles',25,'',20,'[\"1666af5101faed.jpg\",\"1666af5102528a.jpg\",\"1666af51029fe1.jpg\",\"1666af5102ec0b.jpg\"]',1),(3,4,1,'019011cd-e1fc-7163-9ddb-14749f3f5de1','Square Gem',20,'',18,'[\"1666af537c5691.jpg\",\"1666af537caa15.jpg\",\"1666af537cf594.jpg\",\"1666af537d3ca0.jpg\"]',1),(4,6,1,'019011cf-123e-7148-a247-60a52eeb7489','Prisma Pentagonal',25,'',19,'[\"1666af585aa890.jpg\",\"1666af585b0038.jpg\",\"1666af585b530d.jpg\",\"1666af585c1a69.jpg\"]',1);
/*!40000 ALTER TABLE `producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `apellidos` varchar(100) DEFAULT NULL,
  `dni` varchar(9) NOT NULL,
  `email` varchar(255) NOT NULL,
  `foto` varchar(100) DEFAULT NULL,
  `pais` varchar(50) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `cp` int(11) DEFAULT NULL,
  `direccion` varchar(100) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_IDENTIFIER_UUID` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,'019011b4-d4b9-74ae-a212-655ca251776f','[\"ROLE_ADMIN\"]','$2y$13$5fdZCAWowYgRo6X6mC0Jc.fTFBDBAp.6m2sJbSRTF8BLX1Gm/LtWu','Arturo','Navarro Balbuena','06335550Q','arturonb51236@gmail.com','1666aeecebef52.jpg','ES','13',13700,'Calle Nueva 64 1A',0),(3,'01901e45-a93e-7453-94dd-380b3e67f66a','[]','$2y$13$rPWUTjaBqeRaJXaRA.D9Jeny2l9zXKVpnX2BS9xYn9yFmzLtq6amu','Arturo','Navarro Balbuena','06335550Q','rtsanonynousperson@gmail.com','1666e265083088.jpg','ES','13',13700,'Calle Nueva 64 1A',0);
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'RubiksHub'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-06-17 21:46:48
