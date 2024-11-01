
DROP TABLE IF EXISTS `permission_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permission_pages` (
  `permission_id` int NOT NULL,
  `page_name` varchar(100) NOT NULL,
  PRIMARY KEY (`permission_id`,`page_name`),
  CONSTRAINT `permission_pages_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission_pages`
--

LOCK TABLES `permission_pages` WRITE;
/*!40000 ALTER TABLE `permission_pages` DISABLE KEYS */;
INSERT INTO `permission_pages` VALUES (1,'/IGCSE Computer Science/CategorizedPastpapers/001 Data Representation Questions.md'),(1,'/IGCSE Computer Science/CategorizedPastpapers/002 Data Transmission Questions.md'),(1,'/IGCSE Computer Science/CategorizedPastpapers/003 Hardware Questions.md'),(1,'/IGCSE Computer Science/CategorizedPastpapers/004 Software Questions.md'),(1,'/IGCSE Computer Science/CategorizedPastpapers/005 The internet and its uses Questions.md'),(1,'/IGCSE Computer Science/CategorizedPastpapers/006 Automated and Emerging Technologies Questions.md'),(1,'/IGCSE Computer Science/CategorizedPastpapers/007 Algorithm Design And Problem Solving Questions.md'),(1,'/IGCSE Computer Science/CategorizedPastpapers/012 Flowcharts and TraceTable.md'),(1,'/IGCSE Computer Science/CategorizedPastpapers/013 Pseudocode.md'),(1,'/IGCSE Computer Science/CategorizedPastpapers/014 LogicCircuit.md'),(2,'/ASAL Computer Science/CategorizedPastpapers/001 DataRepresentation Questions.md'),(2,'/ASAL Computer Science/CategorizedPastpapers/002 Communication Questions.md'),(2,'/ASAL Computer Science/CategorizedPastpapers/003 Hardware Questions.md'),(2,'/ASAL Computer Science/CategorizedPastpapers/004 Processor Fundamentals Questions.md'),(2,'/ASAL Computer Science/CategorizedPastpapers/005 System Software Questions.md'),(2,'/ASAL Computer Science/CategorizedPastpapers/006 Security, Privacy And Data Integrity Questions.md'),(2,'/ASAL Computer Science/CategorizedPastpapers/007 Ethics and Ownership Questions.md'),(2,'/ASAL Computer Science/CategorizedPastpapers/008  Database Questions.md'),(2,'/ASAL Computer Science/CategorizedPastpapers/009 Algorithm Design and Problem-solving Questions.md'),(2,'/ASAL Computer Science/CategorizedPastpapers/010 Data Types and Structures.md'),(2,'/ASAL Computer Science/CategorizedPastpapers/011 Programming.md'),(2,'/ASAL Computer Science/CategorizedPastpapers/012 Software Development Problems.md'),(2,'/ASAL Computer Science/CategorizedPastpapers/015 Hardware and Virtual Machines Questions.md'),(2,'/ASAL Computer Science/CategorizedPastpapers/016 System Software Problems.md'),(2,'/ASAL Computer Science/CategorizedPastpapers/017 Security Problems.md'),(2,'/ASAL Computer Science/CategorizedPastpapers/018 Artificial Intelligence (AI) Questions.md'),(2,'/ASAL Computer Science/CategorizedPastpapers/Paper2 Questions.md'),(2,'/ASAL Computer Science/CategorizedPastpapers/Paper4 Questions.md');
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 ;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_permissions`
--

LOCK TABLES `user_permissions` WRITE;
/*!40000 ALTER TABLE `user_permissions` DISABLE KEYS */;
INSERT INTO `user_permissions` VALUES (6,1);
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 ;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (6,'baicao2010','baicao2010@qq.com','$2y$10$1fmfZ9TGytmcCX/z0apDnOsciqfxT5qvp7r9pUnbD/ovWF/X7nPAS','female','igcse_g1','1991-12-01','china','shenzhen','shenzhen_college','',1,NULL,NULL,'2024-10-08 13:13:46',NULL),(7,'baicao2010','baicao2012wxy@gmail.com','$2y$10$7lMMJ4xufdXL7ebbQrhul.aF0IW8KJMGyiFnnRo3Zh93W2uY7C3Jy','female','igcse_g1','1991-12-24','china','shenzhen','shenzhen_college','',1,NULL,NULL,'2024-10-10 14:27:08',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-10-23 20:39:23
