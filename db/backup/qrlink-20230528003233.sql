-- MariaDB dump 10.19  Distrib 10.6.12-MariaDB, for debian-linux-gnu (aarch64)
--
-- Host: localhost    Database: qrlink
-- ------------------------------------------------------
-- Server version	10.6.12-MariaDB-0ubuntu0.22.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `url_shorten`
--

DROP TABLE IF EXISTS `url_shorten`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `url_shorten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` text DEFAULT NULL,
  `short_code` varchar(50) NOT NULL,
  `hits` int(11) NOT NULL,
  `added_date` timestamp NULL DEFAULT current_timestamp(),
  `last_acess` timestamp NULL DEFAULT NULL,
  `short_code_password` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=305 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `url_shorten`
--

LOCK TABLES `url_shorten` WRITE;
/*!40000 ALTER TABLE `url_shorten` DISABLE KEYS */;
INSERT INTO `url_shorten` VALUES (6,'https://insidethediv.com/javascript-filter-table-row-single-and-multiple-columns','78ad6c',32,'2023-03-10 12:54:51','2023-04-21 18:58:25',NULL),(7,'https://www.codehim.com/text-input/javascript-table-search-all-columns/','7d6f53',3,'2023-03-10 12:55:01','2023-04-21 21:19:03',NULL),(62,'https://bityli.com/3pOod','c549e7',1,'2023-04-03 19:00:57','2023-04-16 21:04:38',NULL),(67,'https://stackoverflow.com/questions/7400325/how-to-add-the-values-from-checkboxes-to-an-array','07162a',1,'2023-04-04 19:22:22','2023-04-26 23:47:21',NULL),(100,'https://lempire.notion.site/600-AI-tools-f4f3250a2a8a476580754e6bd1a89a50','996386',0,'2023-04-10 17:02:52',NULL,NULL),(148,'https://stackoverflow.com/questions/44222466/js-how-to-handle-a-400-bad-request-error-returned-from-api-url','c140d6',0,'2023-04-17 17:55:33',NULL,'d41d8cd98f00b204e9800998ecf8427e'),(153,'https://codepen.io/foolishdevweb/pen/rNpYPeo','689a10',0,'2023-04-18 12:11:10',NULL,'d41d8cd98f00b204e9800998ecf8427e'),(173,'https://github.com/mauriciotoledo10/short-urls','8b5345',1,'2023-04-20 14:10:54','2023-04-20 19:41:52','$2y$08$Qh81.bIbRRiDWILF91PGzOHpUcfbuyNsID.eNbch6Trf9eTrezWg6'),(205,'https://qrlink.net.br/','92fc3a',0,'2023-04-23 03:30:33',NULL,NULL),(212,'https://qrlink.com.br/info.php','84cf53',0,'2023-04-24 15:29:32',NULL,NULL),(225,'https://github.com/audreyfeldroy/favicon-cheat-sheet','cb2e8e',1,'2023-04-27 12:51:59','2023-05-27 12:07:55',NULL),(226,'https://simpleicons.org/','7de890',0,'2023-04-27 12:53:09',NULL,NULL),(227,'https://github.com/joshbuchea/HEAD','0c2e18',0,'2023-04-27 12:55:53',NULL,NULL),(228,'https://gist.github.com/micc83/fe6b5609b3a280e5516e2a3e9f633675','412c34',0,'2023-04-27 14:59:10',NULL,NULL),(238,'https://www.youtube.com/watch?v=wfca7hpLAgo&list=PLidFt61KMVZZSrBV0QcDOyHkNF0o1PblQ&index=3&pp=iAQB','2887fa',2,'2023-04-29 14:52:12','2023-05-27 12:53:17',NULL),(239,'https://web.dio.me/track/formacao-blockchain','2d0dc1',0,'2023-04-30 03:14:59',NULL,NULL),(240,'https://youtu.be/tn3rv6Vjz4o','fa7e37',0,'2023-04-30 12:04:25',NULL,NULL),(242,'https://bitsofco.de/making-abbr-work-for-touchscreen-keyboard-mouse/','595eaa',1,'2023-04-30 20:28:18','2023-05-08 22:19:33',NULL),(248,'https://plog.com.br/','0d5ad5',0,'2023-05-03 15:20:27',NULL,NULL),(259,'https://htmldom.dev/detect-the-dark-mode/','92115e',3,'2023-05-07 22:08:58','2023-05-23 00:46:58',NULL),(260,'https://stackoverflow.com/questions/8496452/sql-select-where-with-date-and-time#8496485','68827b',0,'2023-05-08 01:43:08',NULL,NULL),(270,'https://youtu.be/rSyXzwQ9i1M','693cf5',0,'2023-05-14 23:41:12',NULL,NULL),(273,'http://xh6liiypqffzwnu5734ucwps37tn2g6npthvugz3gdoqpikujju525yd.onion/','75daa4',1,'2023-05-15 16:20:02','2023-05-19 22:45:45',NULL),(276,'https://www.youtube.com/@TVImperial/streams','91dd30',1,'2023-05-18 01:16:39','2023-05-20 14:18:23',NULL),(277,'https://preply.com/pt/teste-de-lingua/ingles/perguntas','cd580a',0,'2023-05-18 14:38:30',NULL,NULL),(278,'https://preply.com/pt/blog/aprenda-ingles-como-as-estrelas-de-hollywood/','39c521',0,'2023-05-18 14:42:14',NULL,NULL),(280,'https://blog.openreplay.com/implementing-dark-mode-with-bulma/','65113e',2,'2023-05-18 20:08:12','2023-05-23 17:25:26',NULL),(282,'https://www.animestotais.xyz/2021/08/arquivo-x-1-2-3-temporada-dual-audio.html','cf899b',3,'2023-05-19 01:56:49','2023-05-27 12:45:41',NULL),(283,'https://codepen.io/ohlaph/pen/mvPPeg','48ff51',0,'2023-05-19 15:11:42',NULL,NULL),(284,'https://gist.github.com/RakibSiddiquee/9ea3578412d0ebf2cd9b2544d989fb91','64a525',0,'2023-05-19 15:11:46',NULL,NULL),(286,'https://stackoverflow.com/questions/299628/is-an-entity-body-allowed-for-an-http-delete-request','f09b60',0,'2023-05-19 16:40:54',NULL,NULL),(287,'https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html','b20f94',0,'2023-05-19 17:47:09',NULL,NULL),(288,'https://youtu.be/hTCHn-YGObI?list=TLPQMTkwNTIwMjMh8chJp92SPg&t=1076','704151',0,'2023-05-19 23:23:10',NULL,NULL),(290,'https://curtlink.com/3pOod','7af5d2',1,'2023-05-20 19:40:46','2023-05-20 20:25:19',NULL),(291,'https://plog.com.br/egpLc','c144d2',1,'2023-05-20 21:47:11','2023-05-27 18:10:54',NULL),(292,'https://htmldom.dev/sort-a-table-by-clicking-its-headers/','33705d',0,'2023-05-21 04:01:01',NULL,NULL),(294,'https://www.horadecodar.com.br/2021/10/30/como-detectar-se-a-aba-do-navegador-nao-esta-ativa-com-javascript/','36a172',0,'2023-05-21 22:14:39',NULL,NULL),(296,'https://hacker-ai.ai/#hacker-ai','035368',0,'2023-05-22 16:25:55',NULL,NULL),(297,'https://stefangabos.github.io/world_countries/','9e8f83',1,'2023-05-22 16:53:48','2023-05-27 18:10:33',NULL),(298,'https://www.bbc.com/portuguese/articles/cg35pylglzro?at_medium=RSS&at_campaign=KARANGA','4b0071',0,'2023-05-23 16:35:15',NULL,NULL),(302,'https://stannis-pub-jaragua.goomer.app/?cupomSETI15','40dfaf',0,'2023-05-26 14:33:32',NULL,NULL),(303,'https://datum.gupy.io/job/eyJqb2JJZCI6NDgyMDM4Niwic291cmNlIjoibGlua2VkaW4ifQ==?jobBoardSource=linkedin','4a520e',0,'2023-05-27 01:48:49',NULL,NULL),(304,'https://www.youtube.com/playlist?list=PLIivdWyY5sqJrKl7D2u-gmis8h9K66qoj','3cf490',0,'2023-05-27 16:10:34',NULL,NULL);
/*!40000 ALTER TABLE `url_shorten` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-05-28  0:32:33
