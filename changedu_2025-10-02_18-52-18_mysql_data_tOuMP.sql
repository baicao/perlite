-- MySQL dump 10.13  Distrib 8.0.36, for Linux (x86_64)
--
-- Host: localhost    Database: changedu
-- ------------------------------------------------------
-- Server version	8.0.36

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
-- Table structure for table `permission_pages`
--

DROP TABLE IF EXISTS `permission_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permission_pages` (
  `permission_id` int NOT NULL,
  `page_name` varchar(100) NOT NULL,
  PRIMARY KEY (`permission_id`,`page_name`),
  CONSTRAINT `permission_pages_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission_pages`
--

LOCK TABLES `permission_pages` WRITE;
/*!40000 ALTER TABLE `permission_pages` DISABLE KEYS */;
INSERT INTO `permission_pages` VALUES (1,'/IGCSE/Computer Science/CategorizedPastpapers/001 Data Representation Questions.md'),(1,'/IGCSE/Computer Science/CategorizedPastpapers/002 Data Transmission Questions.md'),(1,'/IGCSE/Computer Science/CategorizedPastpapers/003 Hardware Questions.md'),(1,'/IGCSE/Computer Science/CategorizedPastpapers/004 Software Questions.md'),(1,'/IGCSE/Computer Science/CategorizedPastpapers/005 The internet and its uses Questions.md'),(1,'/IGCSE/Computer Science/CategorizedPastpapers/006 Automated and Emerging Technologies Questions.md'),(1,'/IGCSE/Computer Science/CategorizedPastpapers/007 Algorithm Design And Problem Solving Questions.md'),(1,'/IGCSE/Computer Science/CategorizedPastpapers/012 Flowcharts and TraceTable.md'),(1,'/IGCSE/Computer Science/CategorizedPastpapers/013 Pseudocode.md'),(1,'/IGCSE/Computer Science/CategorizedPastpapers/014 LogicCircuit.md'),(1,'/IGCSE/Computer Science/CategorizedPastpapers/015 PastPapers 2024.md'),(2,'/ASAL/Computer Science/CategorizedPastpapers/001 DataRepresentation Questions.md'),(2,'/ASAL/Computer Science/CategorizedPastpapers/002 Communication Questions.md'),(2,'/ASAL/Computer Science/CategorizedPastpapers/003 Hardware Questions.md'),(2,'/ASAL/Computer Science/CategorizedPastpapers/004 Processor Fundamentals Questions.md'),(2,'/ASAL/Computer Science/CategorizedPastpapers/005 System Software Questions.md'),(2,'/ASAL/Computer Science/CategorizedPastpapers/006 Security, Privacy And Data Integrity Questions.md'),(2,'/ASAL/Computer Science/CategorizedPastpapers/007 Ethics and Ownership Questions.md'),(2,'/ASAL/Computer Science/CategorizedPastpapers/008  Database Questions.md'),(2,'/ASAL/Computer Science/CategorizedPastpapers/009 Algorithm Design and Problem-solving Questions.md'),(2,'/ASAL/Computer Science/CategorizedPastpapers/010 Data Types and Structures.md'),(2,'/ASAL/Computer Science/CategorizedPastpapers/011 Programming.md'),(2,'/ASAL/Computer Science/CategorizedPastpapers/012 Software Development Problems.md'),(2,'/ASAL/Computer Science/CategorizedPastpapers/015 Hardware and Virtual Machines Questions.md'),(2,'/ASAL/Computer Science/CategorizedPastpapers/016 System Software Problems.md'),(2,'/ASAL/Computer Science/CategorizedPastpapers/017 Security Problems.md'),(2,'/ASAL/Computer Science/CategorizedPastpapers/018 Artificial Intelligence (AI) Questions.md'),(2,'/ASAL/Computer Science/CategorizedPastpapers/Paper2 Questions.md'),(2,'/ASAL/Computer Science/CategorizedPastpapers/Paper4 Questions.md');
/*!40000 ALTER TABLE `permission_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'IGCSE CategorizedPastpapers','Access to IGCSE CategorizedPastpapers'),(2,'ASAL CategorizedPastpapers','Access to ASAL CategorizedPastpapers');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_permissions`
--

DROP TABLE IF EXISTS `user_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_permissions` (
  `user_id` int NOT NULL,
  `permission_id` int NOT NULL,
  PRIMARY KEY (`user_id`,`permission_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `user_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `user_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_permissions`
--

LOCK TABLES `user_permissions` WRITE;
/*!40000 ALTER TABLE `user_permissions` DISABLE KEYS */;
INSERT INTO `user_permissions` VALUES (6,1),(33,1),(34,1),(45,1),(57,1),(6,2),(45,2);
/*!40000 ALTER TABLE `user_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `grade` varchar(20) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `school` varchar(100) DEFAULT NULL,
  `verification_code` varchar(32) NOT NULL,
  `is_verified` tinyint(1) DEFAULT '0',
  `subscription_type` enum('monthly','yearly') DEFAULT NULL,
  `subscription_expiry` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `reset_token` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `country_code` varchar(20) DEFAULT NULL,
  `is_phone_verified` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (6,'baicao2010','baicao2010@qq.com','$2y$10$1fmfZ9TGytmcCX/z0apDnOsciqfxT5qvp7r9pUnbD/ovWF/X7nPAS','male','','1991-12-01','china','shenzhen','待定','',1,NULL,NULL,'2024-10-08 05:13:46',NULL,'17722690277','+86',1),(7,'baicao2010','baicao2012wxy@gmail.com','$2y$10$7lMMJ4xufdXL7ebbQrhul.aF0IW8KJMGyiFnnRo3Zh93W2uY7C3Jy','female','igcse_g1','1991-12-24','china','shenzhen','shenzhen_college','',1,NULL,NULL,'2024-10-10 06:27:08',NULL,NULL,NULL,0),(8,'Grrk','grrk@qq.com','$2y$10$2nGP.p0BdkRqku7NpdnqGuYEVPu6LSck/2KxCczc7h5UHQJf6X1y.','male','3','1990-03-02','china','shenzhen','','',1,NULL,NULL,'2024-11-02 09:41:10',NULL,'13691863291','+86',1),(9,'elenana','3443218813@qq.com','$2y$10$//H9yR2uSflztDye/Obd..tL0eyzsQZPg0rmDrvDfg.0D7k81CSBm','female','igcse_g2','2024-11-06','china','shanghai','','',1,NULL,NULL,'2024-11-06 13:53:57',NULL,NULL,NULL,0),(10,'112','2643060176@qq.com','$2y$10$1t59w5VUcTQboaa0Hrkh7OAkEv0RRPPwwThN9TziOIf5LicF.w9hi','female','igcse_g1','2009-11-07','china','shanghai','harrow_shanghai','',1,NULL,NULL,'2024-11-07 14:47:34',NULL,NULL,NULL,0),(11,'刘笑彤','3398272121@QQ.COM','$2y$10$tpaHYLK2DJQYAdZduPLc9eLdosvJxdsQfwgY7kGqRp4fTz0IG4kVC','female','国内高二','2007-12-14','china','天津','天津市武清区杨村三中','',1,NULL,NULL,'2024-11-08 00:55:26',NULL,NULL,NULL,0),(12,'Amand NI','18482556@qq.com','$2y$10$zPyWiUEwDQTswNcASHmgyu7tUNLPmtIXHyz9QnKNYoSH1Zd4aILii','female','7年级','2012-02-11','china','香港','','',1,NULL,NULL,'2024-11-08 01:04:25',NULL,NULL,NULL,0),(13,'xcw1898','xcw1898@163.com','$2y$10$AL/M8EWiq.UFnwUZNY2V9umuIB1uEuPvlROcsOMEBHMR4QIOBoALy','female','g11','2008-07-20','china','shenzhen','harrow_shenzhen','',1,NULL,NULL,'2024-11-08 01:06:38',NULL,NULL,NULL,0),(14,'Lavender','276292035@qq.com','$2y$10$U86D4rcADZTf1TRGnbfTauM6XoQ19xHv0tOvOZ7Rf5/qLd60kqjmW','female','ap_grade_9','2011-11-08','china','','','',1,NULL,NULL,'2024-11-08 01:07:28',NULL,NULL,NULL,0),(15,'Tinatiger','36167101@qq.com','$2y$10$ssCIZN25886JAVohWEMaVO5XIliPZGAhuKGumeJosM3Q7RkfeeVz.','male','DSE中四','2000-04-13','china','珠海','珠海','',1,NULL,NULL,'2024-11-08 01:29:45',NULL,NULL,NULL,0),(16,'wan','50078814@qq.com','$2y$10$Jkk0r.px5Nf95wZItlB.9u7RyS1LD0uXTlCk.YLkPwTGKHE/gGlqe','male','','2012-06-08','china','','','',1,NULL,NULL,'2024-11-08 01:36:23',NULL,NULL,NULL,0),(17,'夜燭','435977631@qq.com','$2y$10$pK9AebTTaVaK4vj0BwXlmerDSfJLjjnYfsdThLlzWVV24UF7tK5fO','female','中三','2010-05-11','china','香港','','',1,NULL,NULL,'2024-11-08 01:58:49',NULL,NULL,NULL,0),(18,'Wendyli','cathleeny@163.com','$2y$10$6HHABJiNsyUiM1096aQ6q.jEgI0CF0KEU93S5TfPgarPove.olIc6','female','','2024-11-08','china','shenzhen','','',1,NULL,NULL,'2024-11-08 02:22:11',NULL,NULL,NULL,0),(19,'Ellen','ellen.wang@miraeasset.com','$2y$10$BhDOvL1E409J1C9PezxXHesDmk0ccPzs5uk4qaA09ngVEw42X9eQi','male','igcse_g1','2011-07-08','china','shanghai','','',1,NULL,NULL,'2024-11-08 02:46:13',NULL,NULL,NULL,0),(20,'Jenny','1163324006@qq.com','$2y$10$bfvUFopwy9v6Pa7vLVUCV.uRi7SAySDwLQL/YsHN8FVbICuBVSWDC','female','','2112-04-24','china','shanghai','','',1,NULL,NULL,'2024-11-08 02:54:25',NULL,NULL,NULL,0),(21,'孙哥','sundan.1223@foxmail.com','$2y$10$2DvgrNXtsHUdjYUL/d9nJ.zZ6HQu6KDo43P5ItACXuUxgpvJXBNnm','male','igcse_g1','2008-12-08','china','shenzhen','concordia','',1,NULL,NULL,'2024-11-08 03:21:04',NULL,NULL,NULL,0),(22,'WINNA','winnashan@winwin-metal.com','$2y$10$xwYeHxRv4LZcbUkPb4QqI.28HaDIKMDQ2.h/LJ7RcoZJrL3FMUki.','female','中三','2009-10-15','china','香港','金文泰','',1,NULL,NULL,'2024-11-08 03:40:12',NULL,NULL,NULL,0),(23,'gaoyan','star_gaoyq8@126.com','$2y$10$UuT02vBbM9QD8v84wBTTwOSaXGnC5yA0tfNwa9xdM6Dluj/CDkmam','female','ap_grade_10','1984-11-08','china','东莞','东莞','',1,NULL,NULL,'2024-11-08 08:56:08',NULL,NULL,NULL,0),(25,'447340184','447340184@qq.com','$2y$10$jPC8.5iYiywO1BZSJTM4wOhJ7/NtcMZ6SbB.E40LfFj91eAYvH63a','female','igcse_g1','2010-07-12','china','beijing','shenzhen_college','',1,NULL,NULL,'2024-11-08 09:33:07',NULL,NULL,NULL,0),(27,'Sophia','wenwen090122@qq.com','$2y$10$baGg8GaiKATLIQ4rNTcGIen74Xu.CiYY08X0hYQwBWo7W8G9VWzXi','female','','2009-01-22','china','shenzhen','harrow_shenzhen','',1,NULL,NULL,'2024-11-08 09:37:16',NULL,NULL,NULL,0),(28,'菩提树','66676492@qq.com','$2y$10$JzJppBj0sEBH4X5rkEjTR.PzxEAb1kBB3glcvRj434OtM4tD7fP8y','female','ap_grade_9','2010-11-08','china','香港','巴蜀','',1,NULL,NULL,'2024-11-08 09:54:36',NULL,NULL,NULL,0),(29,'Rachel ','vip_wenhua666@163.com','$2y$10$OqPRpl9hkLuGjjqRzhgDCeIq/yyK3cgEQ9q3tVqkkVX29csEMP8Xa','female','中3','2009-11-08','china','HK','Fanling Lutheran Secondary School','',1,NULL,NULL,'2024-11-08 10:50:56',NULL,NULL,NULL,0),(30,'reboor','64459255@qq.com','$2y$10$4dUAvVAFMTTFx/kVT9KXUO6IpojO.DMN8XHis7TlWmTh2pxHX9aFi','female','ap_grade_9','2010-02-18','china','shenzhen','','934560',0,NULL,NULL,'2024-11-08 13:56:00',NULL,NULL,NULL,0),(31,'Chanjia999','chanjia999@gmail.com','$2y$10$4SBWLwXwbiM4jQZh2dOGguP6OaiUdKlKTjz/eBLl8q5iw7B1CxW6y','male','中四','2008-12-09','china','香港','','',1,NULL,NULL,'2024-11-08 16:31:48',NULL,NULL,NULL,0),(32,'994055233','994055233@qq.com','$2y$10$9MdIQNoOnb8GsNVGitesMeG8zHe8T.bOYZg4MX5FEDkJSYbGqTkwa','male','ap_grade_9','2010-09-04','china','','','',1,NULL,NULL,'2024-11-08 22:39:26',NULL,NULL,NULL,0),(33,'Amy','2514629872@qq.com','$2y$10$7x70WxOkPckPtn6l.uq2KOb2cB3FT0l/7M5lK6pazBGhPLr.K1jXW','female','igcse_g1','2009-05-06','china','杭州','杭州国际','',1,NULL,NULL,'2024-11-09 00:50:15',NULL,NULL,NULL,0),(34,'Zhou ZiXi','s23232.zhou@stu.scie.com.cn','$2y$10$QBakKJ3P1bh9OachewaAT.5jvT60QCX7JXlwiWLJZFwQiPwWdqYnG','male','as','2007-11-16','china','shenzhen','shenzhen_college','',1,NULL,NULL,'2024-11-09 02:54:55',NULL,NULL,NULL,0),(35,'花花','summermama@126.com','$2y$10$H48nMP7U3YiK16MXxzUige3vw6Gbw0LN0ISr68nZGzGWDPd75Ycf2','female','Dse','2012-05-12','china','香港中学','圣罗撒','',1,NULL,NULL,'2024-11-12 02:01:06',NULL,NULL,NULL,0),(36,'szmickhuang','szmickhuang@gmail.com','$2y$10$1/A8mTXobw07iAovWyNiMOlNZrvwsIpyvaf3lrlMy9eQB/zhRn0w.','female','Grade 8','2011-06-12','china','shenzhen','','',1,NULL,NULL,'2024-11-12 13:42:57',NULL,NULL,NULL,0),(37,'Cindy','caijie_nj@126.com','$2y$10$mjIaUQVWZlAXNQ3/L8TBOeS0UA7OxyNjQkfsLOYxTsXrVvID6mwSu','female','国内初一','2011-09-21','china','南京','南京梅园中学','',1,NULL,NULL,'2024-11-14 16:48:15',NULL,NULL,NULL,0),(38,'vivi开到荼蘼','loveldtm@126.com','$2y$10$.owg/fJcWjyTB8I59wB0HeD15iLctd32G8j9vX7asphjqfrbNsk46','female','中三','2009-10-27','china','shenzhen','南山中英文','493704',0,NULL,NULL,'2024-11-15 04:51:54',NULL,NULL,NULL,0),(39,'vivi开到荼蘼','lovekdtm@126.com','$2y$10$LtLLQn./u/Fw0CYc4oXStOs9K3Uuzs2Szx5eoCOi0jLuyJMfsLsz2','female','中三','2009-10-27','china','shenzhen','南山中英文','',1,NULL,NULL,'2024-11-15 04:54:38',NULL,NULL,NULL,0),(40,'Kelly801802','272527036@qq.com','$2y$10$48lpM3dnzgp3lxDHQZKCYuzexAsJaf/OSHnaUH1umZ7NUFKFyKcRm','female','ap_grade_9','2010-01-27','china','广州市','爱莎荔湾外籍子女学校','',1,NULL,NULL,'2024-11-15 07:44:05',NULL,NULL,NULL,0),(41,'刘润嘉','YD25LRJ@163.COM','$2y$10$AB68J7olyiU.gQU415TJTOmlfGe6XPuu3FC9gA.xt65IbvxrNHqF6','female','ap_grade_12','2006-09-21','china','shenzhen','深圳市云顶学校','',1,NULL,NULL,'2024-11-16 08:08:42',NULL,NULL,NULL,0),(42,'JUSTIN','472604231@QQ.com','$2y$10$JTu8nSCVOid5UESHzPF9POxf5sgkD3zypfn8JYKhghZtKSTp7kfnu','male','ap_grade_12','2006-06-16','china','suzhou','dulwich_suzhou','',1,NULL,NULL,'2024-11-20 09:12:34',NULL,NULL,NULL,0),(43,'李佳仪','1876051500@qq.com','$2y$10$zNl5p0J8gdVTszZDOqC.Pe17VpAxWpQEYK753rP5QIPNPA3d3fwE.','female','dse_s3','2009-11-23','china','HK','香港信义会心诚中学','decc123c6144629c4f2142cd45a57429',0,NULL,NULL,'2024-11-23 07:50:15',NULL,'13501595689','+86',1),(44,'墨缘','352574332@qq.com','$2y$10$PFzVBl/7uocjpF1KgJmD/.FB4BzlqiWMU9Hwo.rf/W.KUZvAJG3ry','female','dse_s4','2009-03-11','china','成都','师大一中','d7d4ebf2b50bd8e72ca3ed71843b858a',0,NULL,NULL,'2024-11-24 08:11:14',NULL,'15928916230','+86',0),(45,'世界需要七休日','s22428.Wang@stu.scie.com.cn','$2y$10$mL4IwDTl1GtnqONrSR/dPuVdkn3R6cRAYDT7fHKIEg1XdKZ7gPnfu','female','as','2008-07-31','china','shenzhen','shenzhen_college_of_international_education','37f8a97da11b99d5e2ff73554a409012',0,NULL,NULL,'2024-11-26 00:10:30',NULL,'13418852318','+86',1),(46,'文雨','3042898@qq.com','$2y$10$jacM7WPjHaIMRSEtU.K/k.cJujv4erwCY5n3eRt.ZnSzYdj6NB9ei','female','dse_s4','2009-07-07','china','','shenzhen_college_of_international_education','7adb879f9e8c6ec7e1d4a88bb9a9a3f6',0,NULL,NULL,'2024-11-26 04:18:13',NULL,'15305757192','+86',1),(47,'15313880811','185767741@qq.com','$2y$10$.B262mk9mE0uFhy9Ti72ZOl51JbzUiCPs4YpY0YNQr13fAI/XwuWa','female','dse_s4','2010-07-12','china','shenzhen','shenzhen_college_of_international_education','914c835cde71e0d87a04b6523d4fde05',0,NULL,NULL,'2024-12-02 13:31:16',NULL,'15313880811','+86',1),(48,'Cassie','hkjyfzxh@gmail.com','$2y$10$EPFLUY4KfNEj/5PKxBWJKe.oTtiegMcItAExEDUr5DhU7ayTf9Ctu','female','dse_s3','2010-11-26','china','hk','','053d9131f961a747fb705b871d295484',0,NULL,NULL,'2024-12-03 07:59:27',NULL,'65510828','+852',0),(49,'Damon','878600023@qq.com','$2y$10$KsB2qHPGXAEBJF3yTWoEi.HWboV0qePqWonkbXQyRR.411TqAmpVO','male','dse_s4','2009-03-16','china','shenzhen','shenzhen_college_of_international_education','a67501ac5d7e22df042057e87d956571',0,NULL,NULL,'2024-12-06 11:17:23',NULL,'18126200533','+86',0),(50,'XP','2149856541@qq.com','$2y$10$tfRz48lPWlO5yUwCemz6n.MuX7TB3dRiKWu1sAp08K1N/iyR7310i','female','dse_s2','2024-12-06','china','shanghai','shanghai_campus','0f0f1acdc0132480a9c63968401eef2f',0,NULL,NULL,'2024-12-06 12:55:52',NULL,'18721585609','+86',0),(51,'小胡','14751125868@163.com','$2y$10$ULgYwAW8QUCU68d/PUTm/Oo02hH5IYVG6OMEaee3ewjsWb4FvHTuu','male','as','2025-01-02','uk','江苏','shenzhen_college_of_international_education','37b014fc3095c9126c142ceabecc6ffa',0,NULL,NULL,'2025-01-01 20:50:25',NULL,'14751125868','+86',1),(52,'bbsttdown','jiangchungen@gmail.com','$2y$10$3LVpIr0fgaDK.8rxO5Pr7.2.WST4bfcGdapXxxewAObqaYivE9dFC','male','as','2006-01-01','china','beijing','beijing_campus','c1c08a823ff220fc0fa39f4918741eb8',0,NULL,NULL,'2025-01-05 22:21:39',NULL,'17554258652','+86',1),(53,'guoruowei','2891305508@qq.com','$2y$10$R7IiQPInOqbiiLd0Z/fLVebGf.Wx5K5apMwxLGFnBLVsTCQAImkqG','male','dse_s6','2004-02-14','china','shenzhen','shenzhen_college_of_international_education','ae6b61b20809346c8ef5436cca771f00',0,NULL,NULL,'2025-01-25 08:12:42',NULL,'13133972997','+86',1),(54,'Emma','1158577358@qq.com','$2y$10$qOy5bPNDNOiOoCW3EkDLh.aosOf1fUjXk.jH8cvwIvB.MdzrLlB3q','female','igcse_g1','2008-10-27','china','江苏','suzhou_campus','f93d0b53dfb01d0e76a98d5e1e3d83bc',0,NULL,NULL,'2025-02-15 03:01:38',NULL,'15062642607','+86',0),(55,'zuxin','zuxin.guo@outlook.com','$2y$10$pUqo6T.fv/mo0Hf/nZ6ULuhtco9Vz8yKmkGxLtQBzBDDvb0eqKQcW','female','igcse_g2','2000-12-13','。','。','。','44f5d95ba4e891bcaea4dda47eee5df6',0,NULL,NULL,'2025-02-20 19:35:05',NULL,'18561823621','+86',1),(56,'jshi','1715247794@qq.com','$2y$10$8UwGGPV/IZI3e6JK4VcF6.3Gs2PP2PvFEg//DVBUNNVkc.WuBAdAu','male','','2004-09-25','uk','london','','5e6dd47e9ae738919e24764968497698',0,NULL,NULL,'2025-02-28 16:17:02',NULL,'15258344345','+86',1),(57,'471718280','471718280@qq.com','$2y$10$1fmfZ9TGytmcCX/z0apDnOsciqfxT5qvp7r9pUnbD/ovWF/X7nPAS','male','ap_grade_12','2007-05-08','china','beijing','shenzhen_college_of_international_education','190532de41e3eb16db3bbc931dc4ffbf',1,NULL,NULL,'2025-04-03 04:12:22',NULL,'15821020418','+86',0),(58,'1;','abc123@mycom.com','$2y$10$1rNjAkrE9JdsfGLFKYyHJOPkOMZUogJ8jYTXOgA9P1iKHkgQWm.wC','male','',NULL,'','','shenzhen_college_of_international_education','d095e517256dd0f08f01e7f9f3bdd6e6',0,NULL,NULL,'2025-04-14 09:49:34',NULL,'54321678','+86',0),(59,'ubBXeXAp','testing@example.com','$2y$10$o0LO2HBqCe7gUhESYVbZVe4XCETPszO3LnL1CuKZePypZbH6kXlj6','male','dse_s1','1967-01-01','china','beijing','shenzhen_college_of_international_education','ce94aea205e2c34e8962d7819081138b',0,NULL,NULL,'2025-04-14 22:27:17','090c8ed7ca7d85e63864f423953d88221da4a8a1ef5b5ec03ece85d02e0cfd95','555-666-0606','+852',0),(60,'ubBXeXAp','testing@example.comesiincludesrc=httpbxss.merpb.png','$2y$10$8m80pun/p43bMElsGla4b.qMwKx.vVIfxP8XKShxwDwGUTfBvL09a','male','dse_s1','1967-01-01','china','beijing','shenzhen_college_of_international_education','d9ae94c6d464ad38088d10732fba3f0f',0,NULL,NULL,'2025-04-14 22:27:38','31a81e34907f2420c8a39d3f8cd426fe856370e2d2fc3f301b8af784f9adc279','555-666-0606','+86',0),(61,'papaya','2230812599@qq.com','$2y$10$86msiqBNWR2p/Bw4hyMDJeprHs913q51624leN8nxxTdOFvkBCqQu','female','al','2006-11-11','uk','shenzhen','shenzhen_college_of_international_education','2edfbd91f42c8c756f1358a69c587088',0,NULL,NULL,'2025-04-18 04:14:13',NULL,'15994740245','+86',1),(62,'Vivianzhang','839753130@qq.com','$2y$10$6A9EL98uyiw/wlic86ibF.5UzoFWJPHBMeGU1dh3PpPcsWoo2xIlS','male','dse_s3','2008-11-22','china','香港','','8d6a73917acf82aaa038b10a985fe18a',0,NULL,NULL,'2025-04-24 08:55:31',NULL,'13809213013','+86',1),(63,'eatfishfish','425912414@qq.com','$2y$10$cWnLway2h9TRIAbhbSmK9ulQWi/2utwa/KGgGffzVUePPz7tFDlGC','female','dse_s1','2007-10-27','china','shanghai','shenzhen_college_of_international_education','1c2ce317ead22408c3ee777e069edd89',0,NULL,NULL,'2025-05-21 11:02:55',NULL,'15190877165','+86',1),(64,'mumu','26755040@qq.com','$2y$10$Wjh8ipInaFGagxtZ0tUvheg32oQ9S8rVFLbh6rOkClZU0EGFogCL2','male','dse_s5','2009-02-26','china','shenzhen','shenzhen_college_of_international_education','5a949ba08908870b02463eaa2f4acc18',0,NULL,NULL,'2025-06-06 03:32:07',NULL,'18924653502','+86',0),(65,'QI','478456676@qq.com','$2y$10$PlmF.chQKoC0iSnsZtXA1On9b03htbSMAu7Z2BJENtPcPrMZidZ9i','male','ap_grade_12','2015-03-31','china','shanghai','shenzhen_college_of_international_education','06fbb7d6bf8b516a43a63d35e74d4457',0,NULL,NULL,'2025-06-26 14:16:54',NULL,'18816512891','+86',1),(66,'a','caiqinyu1120@163.com','$2y$10$iHtylxSKXqwLCvWeN71xdeOliYV/3UrWxPvCLxcmEaAnMFYuVwebu','male','igcse_g2','2025-07-26','china','','','9fb9593c8c8dd5f249bfa5d2f446ffcd',0,NULL,NULL,'2025-07-26 10:46:47',NULL,'18601661699','+86',0),(67,'KWONG ','wkwong@uisgz.org','$2y$10$ZjkL7B4bXT039wxRljuNJuE8XboOtWb0EKcR8K4uOfts6xKXqe6IO','male','dse_s6','1989-12-29','china','shenzhen','shenzhen_college_of_international_education','e47946753a528e0a30ae0655b5af50cc',0,NULL,NULL,'2025-08-05 15:01:48',NULL,'13143801542','+86',0),(68,'Linda','237730538@qq.com','$2y$10$fu729DKEI2o9DpFUUOI61.D1MpCM2vCNp9RuP6DxZ9M.EQIQ4c5.O','male','igcse_g1','2011-08-23','uk','shanghai','','3b5d933597b87182bda78d39053e1ca1',0,NULL,NULL,'2025-08-19 02:03:16',NULL,'15102708116','+86',1),(69,'ZHANGXY','2042939048@qq.com','$2y$10$5u29kC2Yh5S58ChtW1MIAucbyU4oyMdfbydQkghLiZtaJ4IwJqsAi','male','dse_s6','2008-03-26','china','shenzhen','','3c5a2e7a01d1ac63539156ae6cddfe04',0,NULL,NULL,'2025-09-05 14:56:44',NULL,'189022870966','+86',0),(70,'张鑫元','zhangxy9856@163.com','$2y$10$PKKPNPwDIeU.e/X0WHhd4eSqjZGgT9gODFGbUB1MX0vlsqYg61yAS','male','dse_s6','2008-03-26','china','shenzhen','','3b59ec72c4292ecce051d2160be2ccb8',0,NULL,NULL,'2025-09-05 15:15:29',NULL,'18922870966','+86',0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `verification_codes`
--

DROP TABLE IF EXISTS `verification_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `verification_codes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `phone_number` varchar(20) NOT NULL,
  `code` varchar(6) NOT NULL,
  `create_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `expiry` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `verification_codes`
--

LOCK TABLES `verification_codes` WRITE;
/*!40000 ALTER TABLE `verification_codes` DISABLE KEYS */;
INSERT INTO `verification_codes` VALUES (1,'+8617722690277','736204','2024-11-21 17:46:23','2024-11-21 17:49:23'),(2,'+8617722690277','978625','2024-11-21 17:56:25','2024-11-21 17:59:25'),(3,'+8613691863291','821183','2024-11-21 18:06:45','2024-11-21 18:09:45'),(4,'+8617722690277','781163','2024-11-21 22:22:04','2024-11-21 22:25:04'),(5,'+8613501595689','468643','2024-11-23 15:52:15','2024-11-23 15:55:15'),(6,'+8613418852318','688769','2024-11-26 08:10:49','2024-11-26 08:13:49'),(7,'+8615313880811','608416','2024-12-02 21:32:20','2024-12-02 21:35:20'),(8,'+8615313880811','595001','2024-12-03 07:02:11','2024-12-03 07:05:11'),(9,'+8617722690277','121073','2024-12-03 18:24:22','2024-12-03 18:27:22'),(10,'+8613418852318','120200','2024-12-05 08:34:07','2024-12-05 08:37:07'),(11,'+8613418852318','701602','2024-12-09 22:32:32','2024-12-09 22:35:32'),(12,'+8617722690277','311643','2024-12-13 12:32:40','2024-12-13 12:35:40'),(13,'+8617722690277','804064','2024-12-21 16:28:39','2024-12-21 16:31:39'),(14,'+8617722690277','745380','2024-12-21 16:29:32','2024-12-21 16:32:32'),(15,'+8617722690277','647695','2024-12-28 16:34:17','2024-12-28 16:37:17'),(16,'+8614751125868','439591','2025-01-02 04:50:53','2025-01-02 04:53:53'),(17,'+8617554258652','983939','2025-01-06 06:22:04','2025-01-06 06:25:04'),(18,'+8617554258652','376410','2025-01-06 10:54:02','2025-01-06 10:57:02'),(19,'+8617554258652','709029','2025-01-06 13:23:56','2025-01-06 13:26:56'),(20,'+8617554258652','924306','2025-01-06 13:46:21','2025-01-06 13:49:21'),(21,'+8617554258652','259055','2025-01-06 20:09:42','2025-01-06 20:12:42'),(22,'+8617722690277','186361','2025-01-11 11:25:27','2025-01-11 11:28:27'),(23,'+8615305757192','675202','2025-01-22 16:23:43','2025-01-22 16:26:43'),(24,'+8613133972997','268718','2025-01-25 16:13:03','2025-01-25 16:16:03'),(25,'+8617722690277','814713','2025-02-11 20:18:36','2025-02-11 20:21:36'),(26,'+8615258344345','217197','2025-03-01 00:18:31','2025-03-01 00:21:31'),(27,'+8617722690277','992921','2025-03-02 15:34:46','2025-03-02 15:37:46'),(28,'+8613418852318','823639','2025-03-04 09:39:12','2025-03-04 09:42:12'),(29,'+8613418852318','383766','2025-03-10 09:33:49','2025-03-10 09:36:49'),(30,'+8613418852318','488917','2025-03-17 19:05:50','2025-03-17 19:08:50'),(31,'+8613418852318','497282','2025-03-24 22:12:47','2025-03-24 22:15:47'),(32,'+8613418852318','263338','2025-03-26 22:17:13','2025-03-26 22:20:13'),(33,'+8613418852318','453641','2025-03-26 22:17:42','2025-03-26 22:20:42'),(34,'+8613418852318','169719','2025-03-27 21:08:35','2025-03-27 21:11:35'),(35,'+8613418852318','130616','2025-03-27 21:11:18','2025-03-27 21:14:18'),(36,'+8618561823621','670900','2025-04-01 19:44:59','2025-04-01 19:47:59'),(37,'+8617722690277','473397','2025-04-03 11:32:24','2025-04-03 11:35:24'),(38,'+8617722690277','590472','2025-04-03 19:49:29','2025-04-03 19:52:29'),(39,'+8617722690277','641815','2025-04-06 14:48:23','2025-04-06 14:51:23'),(40,'+8615994740245','328847','2025-04-18 12:15:31','2025-04-18 12:18:31'),(41,'+8615994740245','479978','2025-04-22 10:17:29','2025-04-22 10:20:29'),(42,'+8615994740245','838680','2025-04-22 10:19:54','2025-04-22 10:22:54'),(43,'+8613809213013','971159','2025-05-03 13:50:07','2025-05-03 13:53:07'),(44,'+8613418852318','776056','2025-05-07 16:33:44','2025-05-07 16:36:44'),(45,'+8613418852318','248854','2025-05-08 21:13:54','2025-05-08 21:16:54'),(46,'+8617722690277','388116','2025-05-11 10:47:56','2025-05-11 10:50:56'),(47,'+8617722690277','666348','2025-05-11 13:02:55','2025-05-11 13:05:55'),(48,'+8617722690277','559408','2025-05-17 14:14:22','2025-05-17 14:17:22'),(49,'+8615190877165','320384','2025-05-21 19:03:19','2025-05-21 19:06:19'),(50,'+8615190877165','380312','2025-05-21 19:06:34','2025-05-21 19:09:34'),(51,'+8615190877165','209174','2025-05-23 14:37:10','2025-05-23 14:40:10'),(52,'+8615190877165','311216','2025-05-24 23:36:18','2025-05-24 23:39:18'),(53,'+8613809213013','193453','2025-06-25 22:05:51','2025-06-25 22:08:51'),(54,'+8618816512891','723047','2025-06-26 22:17:07','2025-06-26 22:20:07'),(55,'+8618816512891','275801','2025-06-27 19:20:34','2025-06-27 19:23:34'),(56,'+8618601661699','878448','2025-07-26 18:47:13','2025-07-26 18:50:13'),(57,'+8613143801542','571959','2025-08-05 23:02:11','2025-08-05 23:05:11'),(58,'+8617722690277','224836','2025-08-18 18:17:35','2025-08-18 18:20:35'),(59,'+8617722690277','692982','2025-08-18 18:19:34','2025-08-18 18:22:34'),(60,'+8615102708116','362579','2025-08-19 10:03:30','2025-08-19 10:06:30'),(61,'+8617722690277','232029','2025-08-22 11:59:32','2025-08-22 12:02:32'),(62,'+8617722690277','502443','2025-08-26 17:56:21','2025-08-26 17:59:21'),(63,'+8618922870966','144675','2025-09-05 23:16:04','2025-09-05 23:19:04'),(64,'+8617722690277','267222','2025-09-23 14:27:00','2025-09-23 14:30:00'),(65,'+8617722690277','695658','2025-10-02 18:25:41','2025-10-02 18:28:41'),(66,'+8617722690277','345327','2025-10-02 18:35:41','2025-10-02 18:38:41');
/*!40000 ALTER TABLE `verification_codes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'changedu'
--

--
-- Dumping routines for database 'changedu'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-02 18:52:20
