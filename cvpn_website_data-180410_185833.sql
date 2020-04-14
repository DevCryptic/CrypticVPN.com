-- MySQL dump 10.15  Distrib 10.0.34-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: cvpn_data
-- ------------------------------------------------------
-- Server version	10.0.34-MariaDB

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
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(32) NOT NULL,
  `rVPN` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `activation` varchar(20) NOT NULL,
  `isactive` varchar(1) NOT NULL,
  `isbanned` varchar(1) NOT NULL,
  `isstaff` varchar(1) NOT NULL,
  `receive_dtinfo` int(1) NOT NULL,
  `special_offers` int(1) NOT NULL,
  `pfoption` varchar(1) NOT NULL,
  `dSID` varchar(1) NOT NULL,
  `expire` int(11) NOT NULL,
  `acdate` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9495 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--


--
-- Table structure for table `actions`
--

DROP TABLE IF EXISTS `actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serverid` int(11) NOT NULL,
  `action` varchar(20) NOT NULL,
  `userid` int(11) NOT NULL,
  `newpassword` varchar(50) NOT NULL,
  `port` int(11) NOT NULL,
  `internalip` varchar(15) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55223 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actions`
--

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'cryptic','4b064ec9594e28e185a6721b5aa2f8ee','admin@crypticvpn.com'),(2,'blackphoex','d03b6a03a083cacf08b73e27e3a760e5','crypticvpnstaff@gmail.com');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `blacklist`
--

DROP TABLE IF EXISTS `blacklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blacklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `port` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `blacklist`
--

LOCK TABLES `blacklist` WRITE;
/*!40000 ALTER TABLE `blacklist` DISABLE KEYS */;
INSERT INTO `blacklist` VALUES (1,80),(2,443),(3,25),(5,110),(6,53),(7,465),(8,587),(9,26),(10,995),(11,143),(13,50081),(14,49017),(15,1194),(16,3389),(17,993),(18,8080),(19,8000),(20,21),(21,22);
/*!40000 ALTER TABLE `blacklist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dedicateds`
--

DROP TABLE IF EXISTS `dedicateds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dedicateds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `ipaddress` varchar(15) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(128) NOT NULL,
  `purchasedate` int(11) NOT NULL,
  `nextpayment` int(11) NOT NULL,
  `vpnspots` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `config_dl` varchar(255) NOT NULL,
  `profile_dl` varchar(255) NOT NULL,
  `dt_notes` varchar(255) NOT NULL,
  `tid` varchar(120) NOT NULL,
  `ping` varchar(10) NOT NULL,
  `package` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dedicateds`
--

LOCK TABLES `dedicateds` WRITE;
/*!40000 ALTER TABLE `dedicateds` DISABLE KEYS */;
/*!40000 ALTER TABLE `dedicateds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dediusers`
--

DROP TABLE IF EXISTS `dediusers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dediusers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sid` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dediusers`
--

LOCK TABLES `dediusers` WRITE;
/*!40000 ALTER TABLE `dediusers` DISABLE KEYS */;
/*!40000 ALTER TABLE `dediusers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dpackages`
--

DROP TABLE IF EXISTS `dpackages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dpackages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `details` text NOT NULL,
  `length` int(11) NOT NULL,
  `unit` varchar(10) NOT NULL,
  `spots` int(11) NOT NULL,
  `price` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dpackages`
--

LOCK TABLES `dpackages` WRITE;
/*!40000 ALTER TABLE `dpackages` DISABLE KEYS */;
/*!40000 ALTER TABLE `dpackages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dpayment_logs`
--

DROP TABLE IF EXISTS `dpayment_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dpayment_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` varchar(120) NOT NULL,
  `payer` int(11) NOT NULL,
  `processor` varchar(15) NOT NULL,
  `amount` float NOT NULL,
  `packageid` varchar(25) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dpayment_logs`
--

LOCK TABLES `dpayment_logs` WRITE;
/*!40000 ALTER TABLE `dpayment_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `dpayment_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `giftcodes`
--

DROP TABLE IF EXISTS `giftcodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `giftcodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `length` int(11) NOT NULL,
  `unit` varchar(10) NOT NULL,
  `usedby` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=560 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `giftcodes`
--



--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender` varchar(30) NOT NULL,
  `title` varchar(50) NOT NULL,
  `details` text NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
INSERT INTO `news` VALUES (1,'1','Testing','this is a test',1460159483);
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `online_users`
--

DROP TABLE IF EXISTS `online_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `online_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session` char(100) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_users`
--

LOCK TABLES `online_users` WRITE;
/*!40000 ALTER TABLE `online_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `online_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `packages`
--

DROP TABLE IF EXISTS `packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `details` text NOT NULL,
  `length` int(11) NOT NULL,
  `unit` varchar(10) NOT NULL,
  `pfenabled` int(1) NOT NULL,
  `price` float NOT NULL,
  `sellNowProductID` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `packages`
--

LOCK TABLES `packages` WRITE;
/*!40000 ALTER TABLE `packages` DISABLE KEYS */;
INSERT INTO `packages` VALUES (2,'1 Day Trial','',1,'Days',1,1.05,'283f74c92121'),(3,'Monthly','',1,'Months',1,2.95,'471429984f6d'),(4,'Quarterly','.',3,'Months',1,7.95,'068d7a0eeb8b'),(5,'Semi-Annual','.',6,'Months',1,15.5,'1d60c33799d3'),(6,'Lifetime','.',5000,'Months',1,25,'c65efa9c9bbb');
/*!40000 ALTER TABLE `packages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_logs`
--

DROP TABLE IF EXISTS `payment_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` varchar(120) NOT NULL,
  `payer` varchar(30) NOT NULL,
  `processor` varchar(50) NOT NULL,
  `amount` float NOT NULL,
  `packageid` varchar(25) NOT NULL,
  `date` int(11) NOT NULL,
  `valid` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7414 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_logs`
--


--
-- Table structure for table `ports`
--

DROP TABLE IF EXISTS `ports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner` int(11) NOT NULL,
  `port` int(7) NOT NULL,
  `internal_ip` varchar(15) NOT NULL,
  `server_id` int(11) NOT NULL,
  `dateOpened` int(11) NOT NULL,
  `dateClosed` int(11) NOT NULL,
  `status` varchar(1) NOT NULL,
  `portType` varchar(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33973 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ports`
--

--
-- Table structure for table `pwresets`
--

DROP TABLE IF EXISTS `pwresets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pwresets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `token` varchar(50) NOT NULL,
  `dateRequested` int(11) NOT NULL,
  `isvalid` varchar(1) NOT NULL,
  `ipaddress` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2167 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pwresets`
--


--
-- Table structure for table `servers`
--

DROP TABLE IF EXISTS `servers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `servers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ipaddress` varchar(50) DEFAULT NULL,
  `country` varchar(30) NOT NULL,
  `city` varchar(30) NOT NULL,
  `name` varchar(50) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(128) NOT NULL,
  `status` varchar(10) NOT NULL,
  `down_reason` varchar(100) NOT NULL,
  `pfEnabled` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servers`
--

LOCK TABLES `servers` WRITE;
/*!40000 ALTER TABLE `servers` DISABLE KEYS */;
INSERT INTO `servers` VALUES (18,'es-madrid01.crypticvpn.com','Spain','Madrid','Madrid','root','SfhK7Iq7DwvYcAV5MIQWNUk18JQlvM/VwTBzfMq8t4Q=','offline','-',1),(19,'pt-lisbon.crypticvpn.com','Portugal','Lisbon','Lisbon','root','D3JNyrwbMK7z7SscXlCtbDc8blRQGv3J/UDxVKviPV8=','117','-',1),(22,'ca-montreal02.crypticvpn.com','Canada','Montreal','Montreal (DDOS Protected)','root','rpWH+rpUNDCg9K9CPUnUKM4kbn5YBKwNzUCYh7FHbtA=','offline','-',0),(26,'hu-budapest01.crypticvpn.com','Hungary','Budapest','Budapest','root','KlC3NZH0iNNaYmD2RxaTA1qjdydjhvqSnBeDC5O6mpA=','136','-',1),(41,'cn-hongkong04.crypticvpn.com','China','Hong-Kong','HongKong','root','pwtWTvQRYVC89FDFvGRbfd75YvROq1NzUMzEdf11/Lo=','246','-',1),(59,'za-johannesburg04.crypticvpn.com','South-Africa','Johannesburg','Johannesburg','root','miFixfBjCOwOaYp/XNDbiEWZ1Huo+JQwdIcjhr6eJVg=','256','-',1),(60,'br-sanpaolo03.crypticvpn.com','Brazil','SanPaolo','Brazil','root','Io2fwwZpFLaf008JigeaucOoxYL9CBJh51GescpJR90=','154','-',1),(69,'us-losangeles01.crypticvpn.com','United-States','Los Angeles','Los Angeles','root','I3gEGmV1jPM3vZc9y4HUqBrOE9x3P4fQ1aOrOA3Chp8=','106','-',1),(70,'us-chicago02.crypticvpn.com','United-States','Chicago','Chicago','root','sySjMysSwZILmEhZTG62fNo12xv2reMtZQ9LqvODRW8=','40','-',1);
/*!40000 ALTER TABLE `servers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `sitetitle` varchar(50) NOT NULL,
  `siteurl` varchar(100) NOT NULL,
  `sitemail` varchar(120) NOT NULL,
  `cpmerchant` varchar(100) NOT NULL,
  `cpsecret` varchar(100) NOT NULL,
  `paypal` varchar(100) NOT NULL,
  `vpndownload` varchar(120) NOT NULL,
  `tapdownload` varchar(150) NOT NULL,
  `configdownload` varchar(120) NOT NULL,
  `vpnnotes` varchar(255) NOT NULL,
  `require_confirmation` int(1) NOT NULL,
  `mailingtype` varchar(10) NOT NULL,
  `smtpport` int(11) NOT NULL,
  `smtpuser` varchar(100) NOT NULL,
  `smtppass` varchar(150) NOT NULL,
  `smtphost` varchar(120) NOT NULL,
  `localkey` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

--
-- Table structure for table `ticketreplies`
--

DROP TABLE IF EXISTS `ticketreplies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticketreplies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `author` int(1) NOT NULL,
  `reply` text NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7231 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticketreplies`
--

LOCK TABLES `ticketreplies` WRITE;
/*!40000 ALTER TABLE `ticketreplies` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticketreplies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department` varchar(20) NOT NULL,
  `senderid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `details` text NOT NULL,
  `date` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2822 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vpnresets`
--

DROP TABLE IF EXISTS `vpnresets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vpnresets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=773 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vpnresets`
--


/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-04-10 18:58:33
