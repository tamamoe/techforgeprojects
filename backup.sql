-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: localhost    Database: cs2team61_db
-- ------------------------------------------------------
-- Server version	8.0.45-0ubuntu0.22.04.1

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
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart` (
  `cartid` int NOT NULL AUTO_INCREMENT,
  `userid` int NOT NULL,
  `productid` int NOT NULL,
  `quantity` int DEFAULT '1',
  `addedat` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cartid`),
  KEY `userid` (`userid`),
  KEY `productid` (`productid`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`productid`) REFERENCES `products` (`productid`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
INSERT INTO `cart` VALUES (38,11,17,1,'2026-03-22 17:19:00'),(39,21,28,5,'2026-03-22 19:30:05');
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `categoryid` int NOT NULL AUTO_INCREMENT,
  `categoryname` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_520_ci,
  `createdat` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`categoryid`),
  UNIQUE KEY `categoryname` (`categoryname`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Motherboard','Computer motherboards and mainboards','2025-12-05 00:38:14'),(2,'CPU','Central Processing Units and processors','2025-12-05 00:38:14'),(3,'GPU','Graphics Processing Units and video cards','2025-12-05 00:38:14'),(4,'RAM','Computer memory and RAM modules','2025-12-05 00:38:14'),(5,'Storage','SSDs, hard drives, and storage devices','2025-12-05 00:38:14');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contacts` (
  `contactid` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `subject` varchar(200) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT 'unread',
  `submittedat` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`contactid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacts`
--

LOCK TABLES `contacts` WRITE;
/*!40000 ALTER TABLE `contacts` DISABLE KEYS */;
INSERT INTO `contacts` VALUES (1,'mo','12345678@gmail.com',NULL,'hello i need a new gpu type','unread','2025-12-05 13:17:12'),(2,'Dhillon Flora','230252217@aston.ac.uk',NULL,'test','unread','2025-12-09 12:26:30'),(3,'Dhillon Flora','dhillonflora@gmail.com',NULL,'h','unread','2025-12-09 12:55:01'),(4,'Dhillon Flora','dhillon@gmail.com',NULL,'test','unread','2025-12-12 11:08:03'),(5,'Dhillon Flora','230252217@aston.ac.uk',NULL,'test','unread','2026-02-04 10:13:14'),(6,'Dhillon Flora','test@gmail.ccom',NULL,'hh','unread','2026-02-12 11:21:51');
/*!40000 ALTER TABLE `contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `orderid` int NOT NULL AUTO_INCREMENT,
  `userid` int NOT NULL,
  `ordernumber` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `totalamount` decimal(10,2) NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT 'pending',
  `shippingaddress` varchar(500) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `orderdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`orderid`),
  UNIQUE KEY `ordernumber` (`ordernumber`),
  KEY `userid` (`userid`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,21,'TF-AFD8484C',274.99,'delivered',NULL,'2026-03-20 22:44:39'),(2,22,'TF-CF2D69E8',146.99,'delivered',NULL,'2026-03-21 13:21:51'),(3,23,'TF-D62B1328',301.23,'returned',NULL,'2026-03-21 13:26:42'),(4,23,'TF-DE8BA0B6',146.99,'delivered',NULL,'2026-03-21 14:59:46'),(5,23,'TF-91996B55',146.99,'delivered',NULL,'2026-03-21 15:45:12'),(6,11,'TF-D5471F5E',448.22,'returned',NULL,'2026-03-22 17:13:20');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `paymentid` int NOT NULL AUTO_INCREMENT,
  `orderid` int NOT NULL,
  `paymentmethod` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `paymentstatus` varchar(20) COLLATE utf8mb4_unicode_520_ci DEFAULT 'pending',
  `amount` decimal(10,2) NOT NULL,
  `paymentdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`paymentid`),
  KEY `orderid` (`orderid`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`orderid`) REFERENCES `orders` (`orderid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `productid` int NOT NULL AUTO_INCREMENT,
  `productname` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `categoryid` int NOT NULL,
  `stock` int DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_520_ci,
  `price` decimal(10,2) NOT NULL,
  `rating` decimal(2,1) DEFAULT '0.0',
  `imageurl` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `createdat` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`productid`),
  KEY `categoryid` (`categoryid`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`categoryid`) REFERENCES `categories` (`categoryid`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'MSI B650 GAMING PLUS WIFI',1,15,'ATX motherboard, Socket AM5, DDR5, WiFi, Bluetooth',134.49,4.5,'products/msi_b650.jpg','2025-12-05 00:42:04',0),(2,'Gigabyte B850 A ELITE WF7',1,12,'Socket AM5, DDR5, 256GB max memory, HDMI',219.99,4.6,'products/gigabyte_b850.jpg','2025-12-05 00:42:04',0),(3,'ASUS B650E MAX GAMING WIFI W',1,18,'White motherboard, Socket AM5, DDR5, 256GB max',173.99,4.4,'products/asus_b650e.jpg','2025-12-05 00:42:04',0),(4,'MSI Z790 GAMING PLUS WIFI',1,10,'ATX, Intel LGA 1700, DDR5, WiFi',189.99,4.5,'products/msi_z790.jpg','2025-12-05 00:42:04',0),(5,'Gigabyte H610M H V3 DDR4',1,25,'Supports Intel Core 14th Gen, LGA 1700, DDR4',52.97,4.2,'products/gigabyte_h610m.jpg','2025-12-05 00:42:04',0),(6,'Gigabyte B760M GAMING X AX',1,14,'Micro ATX, LGA 1700, DDR5, WiFi',143.84,4.3,'products/gigabyte_b760m.jpg','2025-12-05 00:42:04',0),(7,'AMD Ryzen 7 7800X3D',2,8,'5 GHz, Socket AM5, 120W, Ryzen 7',325.97,4.8,'products/ryzen_7800x3d.jpg','2025-12-05 00:42:04',0),(8,'AMD Ryzen 5 7600X',2,12,'4.7 GHz, Socket AM5, 105W, Ryzen 5',150.80,4.6,'products/ryzen_7600x.jpg','2025-12-05 00:42:04',0),(9,'Intel Core i9-14900K',2,3000,'3.2 GHz base, LGA 1700, 125W, Intel Core i9',404.00,4.7,'products/intel_i9_14900k.jpg','2025-12-05 00:42:04',0),(10,'Intel Core i5-14600K',2,10,'2.8 GHz, 14 cores, LGA 1700, 125W',217.62,4.5,'products/intel_i5_14600k.jpg','2025-12-05 00:42:04',0),(11,'Intel Core i7-14700KF',2,1,'5.6 GHz boost, 20 cores, LGA 1200, 125W',282.62,4.6,'products/intel_i7_14700kf.jpg','2025-12-05 00:42:04',0),(12,'XFX Radeon RX 9060XT',3,10,'16GB GDDR6, AMD RDNA 4, 150W',361.92,4.5,'products/xfx_rx9060xt.jpg','2025-12-05 00:42:04',0),(13,'PowerColor RX 7900 XTX 24GB',3,999,'24GB GDDR6, 7680x4320 resolution',719.99,4.7,'products/powercolor_rx7900xtx.jpg','2025-12-05 00:42:04',0),(14,'PowerColor RX 6800 XT',3,8,'16GB GDDR6, 16 GHz memory clock',940.99,4.6,'products/powercolor_rx6800xt.jpg','2025-12-05 00:42:04',0),(15,'ASUS Dual GeForce RTX 5060',3,10,'8GB GDDR7, DLSS 4, HDMI 2.1b',274.99,4.4,'products/asus_rtx5060.jpg','2025-12-05 00:42:04',0),(16,'Gigabyte GeForce RTX 5070',3,0,'12GB GDDR7, 28000 MHz memory',509.99,4.8,'products/gigabyte_rtx5070.jpg','2025-12-05 00:42:04',0),(17,'Gigabyte GeForce RTX 3050',3,15,'6GB GDDR6, Ray Tracing, DLSS',176.77,4.2,'products/gigabyte_rtx3050.jpg','2025-12-05 00:42:04',0),(18,'Crucial DDR5 16GB 4800MHz',4,20,'Laptop memory, SODIMM, CL40, Black',97.99,4.5,'products/crucial_ddr5_16gb.jpg','2025-12-05 00:42:04',0),(19,'Crucial DDR5 8GB 4800MHz',4,25,'Laptop memory, SODIMM, CL40, Black',34.50,4.4,'products/crucial_ddr5_8gb.jpg','2025-12-05 00:42:04',0),(20,'Acer Predator Hera 32GB',4,1,'DDR5 RGB, 6800MHz CL32, AMD EXPO compatible',256.99,4.7,'products/acer_predator_32gb.jpg','2025-12-05 00:42:04',0),(21,'TEAMGROUP T-Force Delta RGB 32GB',4,12,'DDR5 6000MHz CL40, Desktop, Black',311.99,4.6,'products/teamgroup_32gb.jpg','2025-12-05 00:42:04',0),(22,'Kingston FURY Renegade 64GB',4,8,'DDR5 RGB, 6400MT/s CL32, Silver/Black',316.99,4.8,'products/kingston_fury_64gb.jpg','2025-12-05 00:42:04',0),(23,'Corsair VENGEANCE RGB 64GB',4,1,'DDR5 7000MHz CL40, Black, XMP 3.0',349.99,4.7,'products/corsair_vengeance_64gb.jpg','2025-12-05 00:42:04',0),(24,'fanxiang M.2 SSD 256GB',5,30,'NVMe PCIe Gen3x4, Up to 3200MB/s',30.55,4.3,'products/fanxiang_256gb.jpg','2025-12-05 00:42:04',0),(25,'BIWIN Black Opal NV3500 512GB',5,25,'PCIe Gen 3x4 NVMe, 3500 MB/s read',42.99,4.4,'products/biwin_512gb.jpg','2025-12-05 00:42:04',0),(26,'Acer Predator GM9 1TB',5,15,'Gen5 SSD, up to 14000 MB/s, NVMe 2.0',119.99,4.8,'products/acer_gm9_1tb.jpg','2025-12-05 00:42:04',0),(27,'Crucial P310 1TB',5,20,'M.2 NVMe PCIe Gen4, 7100MB/s',74.99,4.6,'products/crucial_p310_1tb.jpg','2025-12-05 00:42:04',0),(28,'Lexar EQ790 2TB',5,8,'M.2 PCIe Gen4x4, Up to 7000MB/s',146.99,4.5,'products/lexar_2tb.jpg','2025-12-05 00:42:04',0),(29,'WD Black SN850X 4TB',5,6,'M.2 PCIe Gen4 x4 NVMe',301.23,4.9,'products/wd_black_4tb.jpg','2025-12-05 00:42:04',0);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `reviewid` int NOT NULL AUTO_INCREMENT,
  `orderid` int NOT NULL,
  `userid` int NOT NULL,
  `rating` int NOT NULL,
  `review_text` text COLLATE utf8mb4_unicode_520_ci,
  `review_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`reviewid`),
  CONSTRAINT `reviews_chk_1` CHECK (((`rating` >= 1) and (`rating` <= 5)))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (1,4,23,5,'amazing thankjs','2026-03-21 15:00:25'),(2,6,11,5,'yippie','2026-03-22 17:14:26');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `userid` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `firstname` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT '',
  `lastname` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT '',
  `isadmin` tinyint(1) DEFAULT '0',
  `communicationpreference` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT 'email',
  `darkmode` tinyint(1) DEFAULT '1',
  `createdat` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userid`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'rehan1@gmail.com','$2y$10$E0YEj8Iv2jOJoVwzSeRQF.hIunyuC5Gg/.hkH6kU1k6uGRONhs.Iu','','',0,'email',0,'2025-12-04 14:51:37'),(2,'rehan112@gmail.com','$2y$10$7/P8XyXXm.uZ5UYfbrgs8OIKrjyrJj/CnXOqY8t.3B8ICXZQvWjtS','','',0,'email',0,'2025-12-04 15:05:02'),(3,'rehan@gmail.com','$2y$10$Kbbka0OzazqzXLrKzS9..eSErWB9uV55nP/SSXdY53KC9Q6y1SjrC','','',0,'email',0,'2025-12-04 17:27:33'),(4,'testing1@gmail.com','$2y$10$uwZwC1Woa9gQYYZCe4wa/.OyUfbNSyUb5p5AW9/AzpVtWr.AdmYyC','','',0,'email',0,'2025-12-04 17:35:17'),(5,'testing11@gmail.com','$2y$10$aXNxqlNEF5SkqexhwsX0buhH/kpXICeVm1oyqTLbLa9dgOcPE6roC','','',0,'email',0,'2025-12-04 17:38:33'),(6,'test33@gmail.com','$2y$10$8BQ3O6iH3vKpW4aRCXvcwe8YgdDjq7jsJs2pj7oiK8Osi4VKSWuG2','','',0,'email',0,'2025-12-05 13:44:13'),(7,'230090284@aston.ac.uk','$2y$10$pMk3S/eiymO51BVcx0CVeO2fwth9SqrY0/h96dy3VaIcLB.509UXa','','',0,'email',0,'2025-12-09 12:28:43'),(8,'rhr84irhrzzjfrf@gmail.com','$2y$10$7WXIz3Vpzvmdl92CT3ChouFrOVwzA6cNQy8TL9HzzpPyXPlNBljyG','','',0,'email',0,'2025-12-09 13:00:50'),(9,'rehan999@gmail.com','$2y$10$g4.yhtL1okUqqKE0ohFye.7A2pH7VBt4lIkkvw1QuHO3qH6Z8/4Fu','','',0,'email',0,'2026-02-04 10:11:38'),(10,'rehan222@gmail.com','$2y$10$QPWSWKaKIqVE2zJTGBraF.DB.tZUZM4jTsJhin1x4gcNCYBaR8wz.','','',0,'email',0,'2026-02-04 10:12:04'),(11,'testacc@test.com','$2y$10$yxhbwEYW8fhbYWpJUGhgKOgCKO2/uhg/EAE1YS1qhRvXqMt4v3y/C','Test','Account',0,'1',0,'2026-02-04 10:12:33'),(12,'rehan12222@gmail.com','$2y$10$ca/3JuZrJ1F7Akg/FlZ0Peks1o1PuclmU92I7a/fWAo/6fIVNsb7q','','',0,'email',0,'2026-02-04 10:36:26'),(13,'rehan199@gmail.com','$2y$10$oCmlzmWWeVsYylnt/5dgBeYoRglZuPjON4BXkkqGmjqU3njowLrXu','','',0,'email',0,'2026-02-04 17:57:19'),(14,'r12@gmail.com','$2y$10$03tr2Qv0CkWzSVzlCigv5eXT1ezieuB/0cFpkVwTVf9u57.bg3eBK','','',0,'email',0,'2026-02-04 18:00:29'),(15,'reh10@gmail.com','$2y$10$Zxl/BEkZ0SH2AyenRRN5TOP1v0pDxQ/Gw0/MLqIhZ9eBahyRNH116','','',0,'email',0,'2026-02-04 18:04:50'),(16,'rehan11111111@gmail.com','$2y$10$Xg1G2LB/.dWJNxuVyQ9IzOTqcoZwBhq6tPzieOkLjnnBsB5dEdVhu','','',0,'email',0,'2026-02-28 15:02:04'),(17,'rehan233@gmail.com','$2y$10$LHbhQbJ6t.C0EkbHtQfT5eyQc4vY449daR3a5h9VdScUNyrrZ4thK','','',0,'email',0,'2026-02-28 16:08:14'),(18,'test12@gmail.com','$2y$10$nx.jO/qwWcJu6YhrhfDnUesH16AbORkq6yfjP5hePcBUbsM9Wq2Qa','','',0,'email',0,'2026-03-01 00:37:32'),(19,'abcdefg@gmail.com','$2y$10$vxFNk6kcifqU8byUdin.IOvSOKCnwCasQp8GoYY.B0RmBmHJNbfza','','',0,'email',0,'2026-03-03 21:29:49'),(20,'login1@gmail.com','$2y$10$fQ9MtnVt7JAr58S4Bh0the0xhLBiyDOj.IkN56embWDG2.ZRdT.7K','','',0,'email',0,'2026-03-03 21:33:12'),(21,'admin@techforge.com','$2y$10$sfDLP7w1z6w80KPfN1YL1.96BKVET1t9O1BCuo6v87iNiKEdevf4a','','',1,'email',0,'2026-03-12 13:56:03'),(22,'h@gmail.com','$2y$10$3HyQ32OsXrKZiyWvqQUFqOuWOxBh7RzRCheDNLjGxMB/bcVL/ZbAC','','',0,'email',0,'2026-03-21 13:21:36'),(23,'a@a.com','$2y$10$jTbM6G3an.q2YbozZ6qL4u6S8syxGmKlHaMhQvyODMtdyYKsK9DJ.','','',0,'email',0,'2026-03-21 13:26:17');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'cs2team61_db'
--

--
-- Dumping routines for database 'cs2team61_db'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-22 23:28:54
