-- MySQL dump 10.13  Distrib 5.1.66, for debian-linux-gnu (i486)
--
-- Host: nelaonline.org    Database: nelaonli_FFFGen
-- ------------------------------------------------------
-- Server version	5.0.96-community-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Not dumping tablespaces as no INFORMATION_SCHEMA.FILES table on this server
--

--
-- Table structure for table `UserHasPermissionRole`
--

DROP TABLE IF EXISTS `UserHasOrganizationalRole`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UserHasOrganizationRole` (
  `badgeid` varchar(15) NOT NULL default '',
  `orgroleid` int(11) NOT NULL default '0',
  `reporttoid` varchar(15) default '',
  `conid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`badgeid`,`orgroleid`,`conid`),
  KEY `permroleid` (`orgroleid`),
  KEY `badgeid` (`badgeid`),
  KEY `conid` (`conid`),
  CONSTRAINT `UserHasOrganizationRole_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
  CONSTRAINT `UserHasOrganizationRole_ibfk_2` FOREIGN KEY (`orgroleid`) REFERENCES `OrgnaizationRoles` (`orgroleid`),
  CONSTRAINT `UserHasOrganizationRole_ibfk_3` FOREIGN KEY (`conid`) REFERENCES `ConInfo` (`conid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `UserHasPermissionRole`
--

LOCK TABLES `UserHasOrganizationRole` WRITE;
/*!40000 ALTER TABLE `UserHasOrganizationRole` DISABLE KEYS */;
INSERT INTO `UserHasOrganizationRole` VALUES 
(120,2,1,40);
/*!40000 ALTER TABLE `UserHasOrganizationRole` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-01-18 10:25:38
-- MySQL dump 10.13  Distrib 5.1.66, for debian-linux-gnu (i486)
--
-- Host: nelaonline.org    Database: nelaonli_FFFGen
-- ------------------------------------------------------
-- Server version	5.0.96-community-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Not dumping tablespaces as no INFORMATION_SCHEMA.FILES table on this server
--

--
-- Table structure for table `PermissionRoles`
--

DROP TABLE IF EXISTS `OrganizationRoles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OrganizationRoles` (
  `orgroleid` int(11) NOT NULL auto_increment,
  `orgrolename` varchar(100) default NULL,
  `orgrolelayer` int(11) NOT NULL default '2',
  `orgrolenotes` text,
  PRIMARY KEY  (`permroleid`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `PermissionRoles`
--

LOCK TABLES `PermissionRoles` WRITE;
/*!40000 ALTER TABLE `PermissionRoles` DISABLE KEYS */;
INSERT INTO `PermissionRoles` VALUES 
(1,'Con Chair',1,'Runs the con'),
(2,'Deputy Con Chair',2,'Assists the Con Chair'),
(3,'Volunteer Department Head',2,'All the general volunteers, load in, load out'),
(4,'Programming Department Head',2,'Programming'),
(5,'Programming','Programming Volunteer Staff'),
(6,'General','General Volunteers'),
(7,'SuperProgramming','Super Programming Volunteers'),
(8,'SuperGeneral','Super General Volunteers'),
(9,'Liaison','Programming Liaisons'),
(10,'SuperLiaison','Super Programming Liaisons'),
(11,'Watch','The Watch Volunteers'),
(12,'SuperWatch','Super Watch Volunteers'),
(13,'Registration','Registration Volunteers'),
(14,'SuperRegistration','Super Registration Volunteers'),
(15,'Vendor','Vendor'),
(16,'SuperVendor','Super Vendor Volunteers'),
(17,'Events','Events Volunteers'),
(18,'SuperEvents','Super Events Volunteers'),
(19,'Logistics','Logistics Volunteers'),
(20,'SuperLogistics','Super Logistics Volunteers'),
(21,'Sales','Sales Volunteers'),
(22,'SuperSales','Super Sales Volunteers'),
(23,'Fasttrack','Fasttrack Volunteers'),
(24,'SuperFasttrack','Super Fasttrack Volunteers');
/*!40000 ALTER TABLE `PermissionRoles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-01-18 10:29:00
