# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.5.5-10.1.23-MariaDB)
# Database: xxx
# Generation Time: 2020-04-14 16:03:49 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table radacct
# ------------------------------------------------------------

DROP TABLE IF EXISTS `radacct`;

CREATE TABLE `radacct` (
  `radacctid` bigint(21) NOT NULL AUTO_INCREMENT,
  `acctsessionid` varchar(64) NOT NULL DEFAULT '',
  `acctuniqueid` varchar(32) NOT NULL DEFAULT '',
  `username` varchar(64) NOT NULL DEFAULT '',
  `groupname` varchar(64) NOT NULL DEFAULT '',
  `realm` varchar(64) DEFAULT '',
  `nasipaddress` varchar(15) NOT NULL DEFAULT '',
  `nasportid` varchar(15) DEFAULT NULL,
  `nasporttype` varchar(32) DEFAULT NULL,
  `acctstarttime` datetime DEFAULT NULL,
  `acctstoptime` datetime DEFAULT NULL,
  `acctsessiontime` int(12) DEFAULT NULL,
  `acctauthentic` varchar(32) DEFAULT NULL,
  `connectinfo_start` varchar(50) DEFAULT NULL,
  `connectinfo_stop` varchar(50) DEFAULT NULL,
  `acctinputoctets` bigint(20) DEFAULT NULL,
  `acctoutputoctets` bigint(20) DEFAULT NULL,
  `calledstationid` varchar(50) NOT NULL DEFAULT '',
  `callingstationid` varchar(50) NOT NULL DEFAULT '',
  `acctterminatecause` varchar(32) NOT NULL DEFAULT '',
  `servicetype` varchar(32) DEFAULT NULL,
  `framedprotocol` varchar(32) DEFAULT NULL,
  `framedipaddress` varchar(15) NOT NULL DEFAULT '',
  `acctstartdelay` int(12) DEFAULT NULL,
  `acctstopdelay` int(12) DEFAULT NULL,
  `xascendsessionsvrkey` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`radacctid`),
  KEY `username` (`username`),
  KEY `framedipaddress` (`framedipaddress`),
  KEY `acctsessionid` (`acctsessionid`),
  KEY `acctsessiontime` (`acctsessiontime`),
  KEY `acctuniqueid` (`acctuniqueid`),
  KEY `acctstarttime` (`acctstarttime`),
  KEY `acctstoptime` (`acctstoptime`),
  KEY `nasipaddress` (`nasipaddress`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table radadmin
# ------------------------------------------------------------

DROP TABLE IF EXISTS `radadmin`;

CREATE TABLE `radadmin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `radadmin` WRITE;
/*!40000 ALTER TABLE `radadmin` DISABLE KEYS */;

INSERT INTO `radadmin` (`id`, `username`, `password`, `salt`)
VALUES
	(8,'Sutton2k9','e2cc6e73f09decbc13d19d946665985b0dc90e6901a6393a8a8b74771732f93c','7e8'),
	(12,'Cryptic','0bfcbd79855122daaa05421f4a784827869b2af02f710a8d5381bcf6842204af','6e8');

/*!40000 ALTER TABLE `radadmin` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table radcheck
# ------------------------------------------------------------

DROP TABLE IF EXISTS `radcheck`;

CREATE TABLE `radcheck` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '',
  `attribute` varchar(64) NOT NULL DEFAULT '',
  `op` char(2) NOT NULL DEFAULT '==',
  `value` varchar(253) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `username` (`username`(32))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `radcheck` WRITE;
/*!40000 ALTER TABLE `radcheck` DISABLE KEYS */;

INSERT INTO `radcheck` (`id`, `username`, `attribute`, `op`, `value`)
VALUES
	(8134,'1','User-Password',':=','214nlk124n12kl4');

/*!40000 ALTER TABLE `radcheck` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table radgroupcheck
# ------------------------------------------------------------

DROP TABLE IF EXISTS `radgroupcheck`;

CREATE TABLE `radgroupcheck` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `groupname` varchar(64) NOT NULL DEFAULT '',
  `attribute` varchar(64) NOT NULL DEFAULT '',
  `op` char(2) NOT NULL DEFAULT '==',
  `value` varchar(253) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `groupname` (`groupname`(32))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table radgroupreply
# ------------------------------------------------------------

DROP TABLE IF EXISTS `radgroupreply`;

CREATE TABLE `radgroupreply` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `groupname` varchar(64) NOT NULL DEFAULT '',
  `attribute` varchar(64) NOT NULL DEFAULT '',
  `op` char(2) NOT NULL DEFAULT '=',
  `value` varchar(253) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `groupname` (`groupname`(32))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table radmsg
# ------------------------------------------------------------

DROP TABLE IF EXISTS `radmsg`;

CREATE TABLE `radmsg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msg` varchar(10000) DEFAULT NULL,
  `user` varchar(5000) DEFAULT NULL,
  `time` varchar(10000) DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table radonline
# ------------------------------------------------------------

DROP TABLE IF EXISTS `radonline`;

CREATE TABLE `radonline` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Username` text,
  `TimeStamp` text,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table radport
# ------------------------------------------------------------

DROP TABLE IF EXISTS `radport`;

CREATE TABLE `radport` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Server` varchar(255) DEFAULT NULL,
  `Username` varchar(5000) DEFAULT NULL,
  `Port` int(255) DEFAULT NULL,
  `Date` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table radpostauth
# ------------------------------------------------------------

DROP TABLE IF EXISTS `radpostauth`;

CREATE TABLE `radpostauth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '',
  `pass` varchar(64) NOT NULL DEFAULT '',
  `reply` varchar(32) NOT NULL DEFAULT '',
  `authdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table radreply
# ------------------------------------------------------------

DROP TABLE IF EXISTS `radreply`;

CREATE TABLE `radreply` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(64) NOT NULL DEFAULT '',
  `attribute` varchar(64) NOT NULL DEFAULT '',
  `op` char(2) NOT NULL DEFAULT '=',
  `value` varchar(253) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `username` (`username`(32))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table radservers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `radservers`;

CREATE TABLE `radservers` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `IP` varchar(255) DEFAULT NULL,
  `AUTH` text,
  `NAME` varchar(255) DEFAULT NULL,
  `FILE` varchar(255) DEFAULT NULL,
  `ENABLE` int(11) DEFAULT NULL,
  `PORT` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `radservers` WRITE;
/*!40000 ALTER TABLE `radservers` DISABLE KEYS */;

INSERT INTO `radservers` (`ID`, `IP`, `AUTH`, `NAME`, `FILE`, `ENABLE`, `PORT`)
VALUES
	(2864,'ca-montreal02.crypticvpn.com','-----BEGIN CERTIFICATE-----\nMIIDKzCCAhOgAwIBAgIJAKsuNfHFmkujMA0GCSqGSIb3DQEBCwUAMBMxETAPBgNV\nBAMTCENoYW5nZU1lMB4XDTE3MDgyMzIwMzMzN1oXDTI3MDgyMTIwMzMzN1owEzER\nMA8GA1UEAxMIQ2hhbmdlTWUwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIB\nAQDZx2IdgI6vNz95G48EFeCciU7kYpfxEOC42rwPkpQi8VGzibmxyqgFtnBLwe2q\ncJY8CBUy5nMf3q7ac8XLstdsqoUlSq+gcDCp3F0NfIozVTSgnhbYLAMwCD7MS2SS\n0YBZzaE+DjNK0ETKWda5iMgT9cAv2h4ejgczV4o8/Zz/R5KcxmKI+aptX6crabY1\n9G4n92Ux/Hadw5sf/uFzmpfxh6FkOoHqgEeslJGYw7Lxg3HSD6yWiUAm0M/1tQb8\nFyFbXWpvL5Rp9BM5P7EAb0M5YRGwi8237/kfMrK+r4O2XdvShDLB7V8ASKDHQMlg\nubEI5CJUrcMGisopVwacJl/rAgMBAAGjgYEwfzAdBgNVHQ4EFgQU8DCF4qeT8MA1\nUjvh9hPY88UQA8MwQwYDVR0jBDwwOoAU8DCF4qeT8MA1Ujvh9hPY88UQA8OhF6QV\nMBMxETAPBgNVBAMTCENoYW5nZU1lggkAqy418cWaS6MwDAYDVR0TBAUwAwEB/zAL\nBgNVHQ8EBAMCAQYwDQYJKoZIhvcNAQELBQADggEBAHIH156+07l0jBrxQ6DuKCc3\n5D+hBlb6pXmKos8IsVkWQfDq7pE5UOp6AtKOkdu+AqXJSFEGz5/szHeE6Jg+iwFJ\nidql63Y5UeL6ouwJujXkkBnB3GAOrdWQ5t55YnV/PeCgO5lHG7LGkAUjKGnam7wo\neDtdVovYLZqgb08z24Bqi7u4Z8I3EMVvfei02ooKnMazCMyPCwyUkvbpAT8LwwAY\n7cGNjFmquiwERVuGuMLSFJspC8KB1gNgWQ7Qj2+1bo0jSHF9y0xqhncyeuHovGxt\nceUXfKbZaKckTjOqeGPp7Oc2IKWq2EtZmjblcneSQYlQKvUub15WqzQnbTPP3F8=\n-----END CERTIFICATE-----\n','Canada, Montreal','ca-montreal02.crt',1,0),
	(2865,'pt-lisbon.crypticvpn.com','-----BEGIN CERTIFICATE-----\nMIIDKzCCAhOgAwIBAgIJAO4GZumEm29SMA0GCSqGSIb3DQEBCwUAMBMxETAPBgNV\nBAMTCENoYW5nZU1lMB4XDTE2MDUyNjIwMDQyOVoXDTI2MDUyNDIwMDQyOVowEzER\nMA8GA1UEAxMIQ2hhbmdlTWUwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIB\nAQDS9h3JON/rICS3pC2zP4gLARB/ejuaVCicKCm25shpbc3IXDqWVU/94Rb522oR\n2qjy+ECW5XJtwh+e2cH41TSqrfs4IBKWdZ3EDtV2kGZ56vuk5s4WQYnVbHXuxaML\ntHjLZ3BRsg68BJSNJTyEeVU7aKmey1Z1CGOCF7oMsIvhYvlghTTGq5jhBPrnuiDO\ngN73yC73Jm92mwLrIMrvbUX/OVh7S02vl+ciD1BpFytaoyJQw2ohiCyynoju8/3w\nvnDie1sNvAIxvXM57bXvFxFKUnoeVIVYp4B8obos6LcV0QD7aXbLTV965q9EiGtn\nNdiV9mEV/Mt4lBBDBiFSv3+JAgMBAAGjgYEwfzAdBgNVHQ4EFgQUWUuwllGsWamb\nogoGWxRIp1C/Q9MwQwYDVR0jBDwwOoAUWUuwllGsWambogoGWxRIp1C/Q9OhF6QV\nMBMxETAPBgNVBAMTCENoYW5nZU1lggkA7gZm6YSbb1IwDAYDVR0TBAUwAwEB/zAL\nBgNVHQ8EBAMCAQYwDQYJKoZIhvcNAQELBQADggEBAAKvEEfkbWzL8M4DRrD6Z5Bj\no7bQbosQ7/mXm4aNsK/GWgSVCi7Sk35hW4xZBUe9ikz77ZCHJ4/YPKLUm9QHl7QR\nURyIpu2CYrjBV0zu3vprTsI7XiI684mlYR1c9immlrTtm3Q/vbcq7i0XFJ0+R3aN\nrDhZNPlKDynfvw2U4f/TFtKLOBAJ8v5Mn9B+tcib7cExhhTbEVY7j0pF6euv7xoo\nJNhH+ZdlhS5JGAboR3BtN9n0VI/K/M0AYxbCMnnSLdwPLmoaR+9jk3t4r3HHnyEp\nfSq1MUuyMTqE48gWKH0uJKtfhSpyx2MWK4Mu0+MRbXcn7ND32mHSaofXT0gK4Xw=\n-----END CERTIFICATE-----\n','Portugal, Lisbon','pt-lisbon.crt',1,0),
	(2866,'hu-budapest01.crypticvpn.com','-----BEGIN CERTIFICATE-----\nMIIDKzCCAhOgAwIBAgIJAKCDr9dTSe3EMA0GCSqGSIb3DQEBCwUAMBMxETAPBgNV\nBAMTCENoYW5nZU1lMB4XDTE4MDMwNjAzNDM1NFoXDTI4MDMwMzAzNDM1NFowEzER\nMA8GA1UEAxMIQ2hhbmdlTWUwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIB\nAQDE1iaibXvTq8qlUDZUAN/u/3YPoLLFqqWjJJrWFM8k/+MA6tGhsuNBy5xtJfPP\n6I/ryQZi0AZRwpOXga535AYFKE+WdyNBAtbLQCieyE//2m+9UjppmXQ+KEi4xs73\nqrRErCx2mLxJRPMw9hBiQeFvLWdKONFJ7u3TKiSbkvWv+zOdKrq38QWBu8kU3hZo\n3ARlhnKse1QdoVj+pgnpvX+jhpQFfa/1hmIpHjKlbk5I+QqSQmzcUbaxBA7PX5sI\nd2lthzQ7ydlUwtcxNHtmU/W+fXW7qAN1QbvYUuTnngi/SKa/81LhbNe8Y1QacK3k\nJq9fr+iEKChajuHVvVkCBpinAgMBAAGjgYEwfzAdBgNVHQ4EFgQUKhPx2M16xGlu\nVVPEKChIJ0GuZ3gwQwYDVR0jBDwwOoAUKhPx2M16xGluVVPEKChIJ0GuZ3ihF6QV\nMBMxETAPBgNVBAMTCENoYW5nZU1lggkAoIOv11NJ7cQwDAYDVR0TBAUwAwEB/zAL\nBgNVHQ8EBAMCAQYwDQYJKoZIhvcNAQELBQADggEBAGaAWuH+Vd29AvHXq4n2pSWv\n1+M7Q+wnc4X4dGDa3lui/Splzj/kUwnAvcZ7SIniLQ9+hZax1G0bNVEf5nMFWJXy\nTRykhULK8ziDuElTwcW1HhDDlXj2Tcagsm8tCta1LtWd/b93L1Jaq9VcsfQnOLLn\nCYJWKVzkivsLNLGl5QJN4lB89+tY8Z83Nl16X548PNWPOgUX1xMqbE7uIx+Ogtfv\nSnqEf6VXEKBAtGB65/oo32I4yuXqaY5YWRXtaTLzpHgpIlmX/MgmrI5nJNH4dkyf\nazrWZJY3Kv1VLxpPoIkJnFvGjPVJANM1VIBdytFsxVjwkC2aokGkLcdZlP1swBs=\n-----END CERTIFICATE-----\n','Hungary, Budapest','hu-budapest01.crt',1,0),
	(2867,'es-madrid01.crypticvpn.com','-----BEGIN CERTIFICATE-----\nMIIDKzCCAhOgAwIBAgIJAOwejzkGlhK9MA0GCSqGSIb3DQEBCwUAMBMxETAPBgNV\nBAMTCENoYW5nZU1lMB4XDTE2MDUyNjE5NTEyOVoXDTI2MDUyNDE5NTEyOVowEzER\nMA8GA1UEAxMIQ2hhbmdlTWUwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIB\nAQCZlHk8FIukjXB0NYh1Pc07sF5qibpGxG78sqgXqJEyfqtx0mR0vt+1M+Y5YOBp\nQcv3dZSNurMdPDMJ3wlopXLCtTROVkBBYdetL6LpvtcJG87T40yoMMjCt9hqynGU\nEFG5TAABeWsNWB9WfJG7WSbCZZUZn/Hm9t19NYMg/Lwv8Rm7XCWh70z2yzvND/+E\nXQY90egQH44m9JPMqpaPjs+sWsMwo14onvIgynM37/hkVtOPjiYQ/mvQCzcFFBa5\nncX0+R8hhz5e/5N4N6V6RXj5e39xrEn3hENAsjDgONMz5auclvwhWdq2/zLZlZNV\n9dFarpbxTiPhRsSUtpFIS0jzAgMBAAGjgYEwfzAdBgNVHQ4EFgQUCg0uvAPfgdvd\nyypPNF/FIYlzoW0wQwYDVR0jBDwwOoAUCg0uvAPfgdvdyypPNF/FIYlzoW2hF6QV\nMBMxETAPBgNVBAMTCENoYW5nZU1lggkA7B6POQaWEr0wDAYDVR0TBAUwAwEB/zAL\nBgNVHQ8EBAMCAQYwDQYJKoZIhvcNAQELBQADggEBAGFDEM1b+pEnVYYMEMu3u12W\nIbb79QexDc1NClMtq/XEiB0EUPYI2/+kcXEUc9F3J9/mM9zQguVCaZ/bdcvlmNjb\ndNpRcE0YXRNJ0KEb0EnLLIg9N0CbBGR+dEJOnSI6ABSuSr4AnTxQ/Mr5t0+JTJht\nFb6ZRjPwN7pL+U10FdNGWetntqGf2QSKdiMk3Agy/8fH1k3KusSar1UKzqxOPpaT\ngfib848BPwQYJI4ApRZvKBB9/4ITOrmfObjtDa44+7CNSdBgp1bHHFXcvtqCAyMO\nbbXys+5O0jv812Zir2lh0MiZGuQiu+0WQ3aXGDSVLwsLHpsOqXErRtLt1DtTbd4=\n-----END CERTIFICATE-----\n','Spain, Madrid','es-madrid01.crt',1,0),
	(2868,'br-sanpaolo03.crypticvpn.com','-----BEGIN CERTIFICATE-----\nMIIDKzCCAhOgAwIBAgIJAL7/L4xiaohUMA0GCSqGSIb3DQEBCwUAMBMxETAPBgNV\nBAMTCENoYW5nZU1lMB4XDTE3MDcxNDA4NDg0MVoXDTI3MDcxMjA4NDg0MVowEzER\nMA8GA1UEAxMIQ2hhbmdlTWUwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIB\nAQDJyd6sEQejja1RTwjdj8eOsHhnj9wrU7mAndvlvbDWCQy6mRmpKeTgOgectCuk\nYcyB0atinixLGf1BpXkmaS1WnkyU19e+1GD5W3eEwdABGy04ENde+Z3P5yGi+EML\nSfhc0WD401ALPx0bKy60diCKcXXXBa0jDGXFSHIrsgcZHk+14FtnnHDM1DDMxka8\nRvfIDxELbnXksBfurjI/k4TITQBwdKEbIQlhrIMMqeDhPps07lW31p7+mV8tljC2\nqE8dBfKj9bQPtex4MHPTrc/n2zZsy6x7xSHGpI8OA6dXXMjdb7I6/AmcAvNhuG5H\nEWUNPQsNFF1NIA4mn8SEbQlLAgMBAAGjgYEwfzAdBgNVHQ4EFgQUmXkwNrr0J2qD\nlgsq6pHoPIr942YwQwYDVR0jBDwwOoAUmXkwNrr0J2qDlgsq6pHoPIr942ahF6QV\nMBMxETAPBgNVBAMTCENoYW5nZU1lggkAvv8vjGJqiFQwDAYDVR0TBAUwAwEB/zAL\nBgNVHQ8EBAMCAQYwDQYJKoZIhvcNAQELBQADggEBALcptE4m9n403dP8JktF9pod\npWGAvz5AQMeE7WAMUAx7zpQVYJj6hkgxmDtRm1Lrdw8Hynhi/5yvGvPzLav8Rups\npO+ucSDbkp6tCAMKiIPTDNB3xEXiM5vPMAhK3X/dvH5D+wN1rGoBA9KLkZz6XCLe\nq/gRZtqdHcmOO/Uveq4on+JZzYal+ky49T2saqLV5ChnqNbfA58IEoTr8xLJkQHk\nvU1JgAq9bwg4yhQ4YxvwTFf8kQI/vA3sbXCXffMd/0QdbyBDsSlI+7/8OvxRGq7x\nVyNSJl/Hh73K6r0yjypbcihppbsdgtWe6C41DyAjC9QP3CmaDK7Ft/aFhpzNVx4=\n-----END CERTIFICATE-----\n','Brazil, Sanpaolo','br-sanpaolo03.crt',1,0),
	(2869,'us-chicago02.crypticvpn.com','-----BEGIN CERTIFICATE-----\nMIIDKzCCAhOgAwIBAgIJAOQabkf57S73MA0GCSqGSIb3DQEBCwUAMBMxETAPBgNV\nBAMTCENoYW5nZU1lMB4XDTE3MDgyMzIwMzY1NloXDTI3MDgyMTIwMzY1NlowEzER\nMA8GA1UEAxMIQ2hhbmdlTWUwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIB\nAQCfotONtlFuzkmvtHiCTPxYHy7cqHf9iFEJmr06+FMr3Rd1B720Ql0jUc7oDTwh\nI4hfX0zGOw7mAZeZZXYcxASSsa6YLt89I6H2LT2lhS3GA97Za75iNUAYWHJme1i1\nQPsUDkkB31gB1mASsBJZN3fAB9Gmt526tYRW3X4Shqdrr757BbHz1Fo8S+h33UZw\nsTEvjnZlR+sAa7IT0XZAqDxTvI8IvPhm8aNwbFCXpCjz6nKvH/iWiSssosdJLpb3\nH53vG+vnmb0YLqhX8zLwePjeiXsFBS9c+nsUzdrSs+l/disw4E6/pHmPEbvMXnl1\nEy12+DkGehC52WZqa5wUyQ/5AgMBAAGjgYEwfzAdBgNVHQ4EFgQUajY4krjetmYo\nRpCc7HIYGZyoUEMwQwYDVR0jBDwwOoAUajY4krjetmYoRpCc7HIYGZyoUEOhF6QV\nMBMxETAPBgNVBAMTCENoYW5nZU1lggkA5BpuR/ntLvcwDAYDVR0TBAUwAwEB/zAL\nBgNVHQ8EBAMCAQYwDQYJKoZIhvcNAQELBQADggEBAAy8dUrChWSVBsR226RdQ+Ko\nK2VxfZflCtTZ7FnuKaWhViNSlEFcA4rIZRdbJ1lphowegwV1KwKV2Vt2vz2rmdMa\n9PX4Cos7VmmDjWljjCxfc8u4Scx7JDPIM3ntHNhJOl4H5Z38NI0KRdF5HoMoEbdK\nfVStXO7zah1fMbJxuR0ZF7bi2rgWhu/m3C9ggVe6yLIlJBCO8Ud7Mx4Bf8sBp2yE\nMj9f70b3TZn46Ax818xLw7YzATWk0/5tMoXAOXOKTkxxdSsw7tLVpeputL1CgtAB\nekfS9tIF7kEpAz42Fukblk8uVbAft4tjiujOUWVWCqYzSjjRrOg4IQlmYk/2S3U=\n-----END CERTIFICATE-----\n','United States, Chicago','us-chicago02.crt',1,0),
	(2870,'us-losangeles01.crypticvpn.com','-----BEGIN CERTIFICATE-----\nMIIDKzCCAhOgAwIBAgIJAIRxgeFLqCUmMA0GCSqGSIb3DQEBCwUAMBMxETAPBgNV\nBAMTCENoYW5nZU1lMB4XDTE3MDcxNDA4NDg0OVoXDTI3MDcxMjA4NDg0OVowEzER\nMA8GA1UEAxMIQ2hhbmdlTWUwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIB\nAQDTi0mF2NR5Y1rdKCGeICSnG6I8O2iKAYdAZUPVsatv+Npsp7Af2fRLIs17uB6l\nSVwZ8FvUyXj0iCDmvnB/edUixFSnVrHJskpnbOwtaYaQMzwgIfA75wRW4n/v3jV5\nfQ6yddtD3/w/BTG2rQSmlLuwPoHUJK/zdkO8FTGZZcwBG62qM0gc8QIL8BmcZtHo\nD/GCe/5lhdf8eDJO8SIVYQ2dwPnY/Dk+XbUZTTEUVv11hI4WFHARSgdL2ONeVH45\n9n7/AClQ30qPSTB2B328ke2wEFb9GiEgOwfOijQBw17i/zrDUpE4+f7uW7sK5g2t\nWuGqyTZBajHESuB0dBYWHBPTAgMBAAGjgYEwfzAdBgNVHQ4EFgQUOpZUWFH6muVw\nVFY6N6Uw+N3XWxQwQwYDVR0jBDwwOoAUOpZUWFH6muVwVFY6N6Uw+N3XWxShF6QV\nMBMxETAPBgNVBAMTCENoYW5nZU1lggkAhHGB4UuoJSYwDAYDVR0TBAUwAwEB/zAL\nBgNVHQ8EBAMCAQYwDQYJKoZIhvcNAQELBQADggEBAFHw41BJT8CgMLRODg8E1VE5\ndXmTa9aAsUk2cWUAUwRor4TqNeyii93wKaOhgiYNlbhlN9XIlaS5KYbzi56RF1r4\n4NPjeCxjxlSW6rmWCjOC5eWfXhhqcSUBKC1gFgcpu4SK3W4KxaYTQEB9IlweFo+H\nlX/dze9r3xQYDDc1975ND1z/yyyhd4/IKb9FjpKa5+vX/GahPGpo6yVng2FnwHSx\n9iCli09jjOYZJPtJsq5Q/mgvX1EtZWoQk4V3cwd+fwnqgKmSWw1oCNA+HkG6Roai\nj0lqm66jzYHh0LgXBgIMgKseDDlc+auDeZdrzaVw+FV2CN3re9+LTXM60T2PbuA=\n-----END CERTIFICATE-----\n','United States, Losangeles','us-losangeles01.crt',1,0),
	(2871,'cn-hongkong04.crypticvpn.com','-----BEGIN CERTIFICATE-----\nMIIDKzCCAhOgAwIBAgIJALMaXXJOSdouMA0GCSqGSIb3DQEBCwUAMBMxETAPBgNV\nBAMTCENoYW5nZU1lMB4XDTE3MDYyMzA2MzcwNVoXDTI3MDYyMTA2MzcwNVowEzER\nMA8GA1UEAxMIQ2hhbmdlTWUwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIB\nAQDWv0e1V2GPShgolP/n0JPML0MFbeauoB5WoUqRvUJdBE5dg4dEhou8JeaxauxH\nQj9iwMK8JHy4eXFbGgj2m03TvjGuiRXopgR0sTfmRfB6cuciAgCPRlDqq5MAinkU\nckte7t5bV7D9ZbHymXALE36Jbf5fjEug2eUoPNq0i+f3dri5+qZA/tpKkeaPbCIe\nRc6MSGrZCMp3LAZS7KJYeDeEGFN0N1gG7J7Ze6nIKo1JnNSs6z0mAtCD8fi3l7lO\nUMItGY+/IeVoY2fR1db5mwedH//p203p0vj3ONuKY3LdyNkRWM/MRILnWFoEMcM1\nH2K6jg7w/fbq6lrWHA13yExPAgMBAAGjgYEwfzAdBgNVHQ4EFgQUnXfPTdi2Z1sy\nIPTKsNRu2i4jztIwQwYDVR0jBDwwOoAUnXfPTdi2Z1syIPTKsNRu2i4jztKhF6QV\nMBMxETAPBgNVBAMTCENoYW5nZU1lggkAsxpdck5J2i4wDAYDVR0TBAUwAwEB/zAL\nBgNVHQ8EBAMCAQYwDQYJKoZIhvcNAQELBQADggEBAAfJixDlq+SHScWcFzM1MbcG\njucWWqs2y0wuDUwb8ijs2/DQ0QBwVTgZSrhLCeOrjNXOb3x9/pKdv1LLSDlKqlUp\n0EsJGWsXPeAiKjOs0T8K7oZ8Hsh1yDUIL2c7jD8tIsTdPqUwKkj3gyBav0LCWVbb\n2yoRl33szmU0qW9gyz+VHuMqlLg8poL5Rd1xRrHjceJh2uQSVyGmOjxkFjgMF31b\n4ApIq1H+6o7i8ngS+4eq+5h8zcwUcmrolDUfpUix8BJP5iGl4S5hVoSJMx8im/N0\nsbNa2nv9WqcxjZeoap68KE8Qiap/ZxuG5kKo+n3dn1kp4QSIxUcZqvjD4OFERK4=\n-----END CERTIFICATE-----\n','China, Hongkong','cn-hongkong04.crt',1,0),
	(2872,'za-johannesburg04.crypticvpn.com','-----BEGIN CERTIFICATE-----\nMIIDKzCCAhOgAwIBAgIJAMc+ypj8uzNxMA0GCSqGSIb3DQEBCwUAMBMxETAPBgNV\nBAMTCENoYW5nZU1lMB4XDTE3MDcxNDA4NDM0NVoXDTI3MDcxMjA4NDM0NVowEzER\nMA8GA1UEAxMIQ2hhbmdlTWUwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIB\nAQCwZtjO5qd18e+5B7vABuVz+W9TC65+4/QX6lYgnon1DqfbKVlz30ypjoXAz+pc\nS3I3Tj0OEeCjTe9R83Kfdzo8RLrIlfQxEcfQvSSACunyqYKqfJ5x52OdXVHHQfOI\nC63wHMJyE9YDhZFb+L4p/f32l4IGiAgOqipDOtClSGVKeoqtWGWttdSn+ocf5Rtr\nUXRY5GoNEIhOV5mGTFCDdcLG6ErGP0anCWZ3UJq6yUz8jb+7qqwu0SZEqxXyht0A\nCLZHPHlNbCad0YpnH2hHXEAFmGT/KKSxiZRvosMX7RU7UfKidSjSfhcPSOAWJj3H\nWUT4D1rrbLx+PKD5+BN/ISxXAgMBAAGjgYEwfzAdBgNVHQ4EFgQUf9au0SL4LvHK\nR/6hFR314UDU/dYwQwYDVR0jBDwwOoAUf9au0SL4LvHKR/6hFR314UDU/dahF6QV\nMBMxETAPBgNVBAMTCENoYW5nZU1lggkAxz7KmPy7M3EwDAYDVR0TBAUwAwEB/zAL\nBgNVHQ8EBAMCAQYwDQYJKoZIhvcNAQELBQADggEBAGl6G00EUJn1cSFeyXbDdlGU\nuVgcHIdU7yPg3MQNjzN8YY510cCilESvtgklQfudObDqyTm/8o1p9uBGoXwDUL9I\nyzT5c242Li0rTG1OfT3Cf3kbwZvPEyDcy/ufI9txvvoPrTFYTYpaLXndUuWxohpe\nOPT26Zgd+FMvCHZGzsq8I+N0QtswXay/lEffM1s6Psu/6Lo1JOekLv0aHzjYK2mP\nhc9LmgMXsKQxpIfyS1iEkR7bkwzYC03ynGV1ePUhpYMRpo513j23VL+NDzWSi+NB\nKvDLUii+tei7W21iXCzPy42iEP24CvzJC1tZ0QeV1QCtQSfrt72O9JvZllMh1A4=\n-----END CERTIFICATE-----\n','South Africa, Johannesburg','za-johannesburg04.crt',1,0);

/*!40000 ALTER TABLE `radservers` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table radserverversion
# ------------------------------------------------------------

DROP TABLE IF EXISTS `radserverversion`;

CREATE TABLE `radserverversion` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Username` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `radserverversion` WRITE;
/*!40000 ALTER TABLE `radserverversion` DISABLE KEYS */;

INSERT INTO `radserverversion` (`ID`, `Username`)
VALUES
	(1,'Sutton2k9'),
	(2,'Sutton2k9'),
	(3,'Sutton2k9'),
	(4,'Cryptic'),
	(5,'Cryptic'),
	(6,'Cryptic'),
	(7,'Cryptic'),
	(8,'Cryptic'),
	(9,'cryptic'),
	(10,'Cryptic\n'),
	(11,'Cryptic'),
	(12,'Auto'),
	(13,'Auto'),
	(14,'Auto'),
	(15,'Auto'),
	(16,'Auto'),
	(17,'Auto'),
	(18,'Auto'),
	(19,'Auto'),
	(20,'Cryptic'),
	(21,'Auto'),
	(22,'Auto'),
	(23,'Auto'),
	(24,'Auto'),
	(25,'Auto'),
	(26,'Auto'),
	(27,'Auto'),
	(28,'Auto'),
	(29,'Auto'),
	(30,'Auto'),
	(31,'Auto'),
	(32,'Auto'),
	(33,'Cryptic'),
	(34,'Auto'),
	(35,'Auto'),
	(36,'Auto'),
	(37,'Auto'),
	(38,'Auto'),
	(39,'Auto'),
	(40,'Auto'),
	(41,'Auto'),
	(42,'Auto'),
	(43,'Auto'),
	(44,'Auto'),
	(45,'Auto'),
	(46,'Auto'),
	(47,'Auto'),
	(48,'Auto'),
	(49,'Auto'),
	(50,'Auto'),
	(51,'Auto'),
	(52,'Auto'),
	(53,'Auto'),
	(54,'Auto'),
	(55,'Auto'),
	(56,'Auto'),
	(57,'Auto'),
	(58,'Auto'),
	(59,'Auto'),
	(60,'Auto'),
	(61,'Auto'),
	(62,'Auto'),
	(63,'Auto'),
	(64,'Auto'),
	(65,'Cryptic'),
	(66,'Cryptic'),
	(67,'Auto'),
	(68,'Auto'),
	(69,'Auto'),
	(70,'Cryptic'),
	(71,'Auto'),
	(72,'Auto'),
	(73,'Auto'),
	(74,'Auto'),
	(75,'Auto'),
	(76,'Auto'),
	(77,'Auto'),
	(78,'Auto'),
	(79,'Auto'),
	(80,'Auto'),
	(81,'Auto'),
	(82,'Auto'),
	(83,'Auto'),
	(84,'Auto'),
	(85,'Auto'),
	(86,'Auto'),
	(87,'Auto'),
	(88,'Auto'),
	(89,'Auto'),
	(90,'Auto'),
	(91,'Auto'),
	(92,'Auto'),
	(93,'Auto'),
	(94,'Auto'),
	(95,'Auto'),
	(96,'Auto'),
	(97,'Auto'),
	(98,'Auto'),
	(99,'Auto'),
	(100,'Auto'),
	(101,'Auto'),
	(102,'Auto'),
	(103,'Auto'),
	(104,'Auto'),
	(105,'Auto'),
	(106,'Auto'),
	(107,'Auto'),
	(108,'Auto'),
	(109,'Auto'),
	(110,'Auto'),
	(111,'Auto'),
	(112,'Auto'),
	(113,'Auto'),
	(114,'Auto'),
	(115,'Auto'),
	(116,'Auto'),
	(117,'Auto'),
	(118,'Auto'),
	(119,'Auto'),
	(120,'Auto'),
	(121,'Auto'),
	(122,'Auto'),
	(123,'Auto'),
	(124,'Auto'),
	(125,'Auto'),
	(126,'Auto'),
	(127,'Auto'),
	(128,'Auto'),
	(129,'Auto'),
	(130,'Auto'),
	(131,'Auto'),
	(132,'Auto'),
	(133,'Auto'),
	(134,'Auto'),
	(135,'Auto'),
	(136,'Auto'),
	(137,'Auto'),
	(138,'Auto'),
	(139,'Auto'),
	(140,'Cryptic'),
	(141,'Auto'),
	(142,'Auto'),
	(143,'Auto'),
	(144,'Auto'),
	(145,'Auto'),
	(146,'Auto'),
	(147,'Auto'),
	(148,'Auto'),
	(149,'Auto'),
	(150,'Auto'),
	(151,'Auto'),
	(152,'Auto'),
	(153,'Auto'),
	(154,'Auto'),
	(155,'Auto'),
	(156,'Auto'),
	(157,'Auto'),
	(158,'Auto'),
	(159,'Auto'),
	(160,'Auto'),
	(161,'Auto'),
	(162,'Auto'),
	(163,'Auto'),
	(164,'Auto'),
	(165,'Auto'),
	(166,'Auto'),
	(167,'Auto'),
	(168,'Auto'),
	(169,'Auto'),
	(170,'Auto'),
	(171,'Auto');

/*!40000 ALTER TABLE `radserverversion` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table radstatus
# ------------------------------------------------------------

DROP TABLE IF EXISTS `radstatus`;

CREATE TABLE `radstatus` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` tinytext NOT NULL,
  `SName` tinytext NOT NULL,
  `IP` tinytext NOT NULL,
  `OpenVPN` tinytext NOT NULL,
  `PPTP` tinytext NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `radstatus` WRITE;
/*!40000 ALTER TABLE `radstatus` DISABLE KEYS */;

INSERT INTO `radstatus` (`ID`, `Name`, `SName`, `IP`, `OpenVPN`, `PPTP`)
VALUES
	(2,'VPN Login Server','Vpn Server','127.0.0.2','True','True');

/*!40000 ALTER TABLE `radstatus` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table radusergroup
# ------------------------------------------------------------

DROP TABLE IF EXISTS `radusergroup`;

CREATE TABLE `radusergroup` (
  `username` varchar(64) NOT NULL DEFAULT '',
  `groupname` varchar(64) NOT NULL DEFAULT '',
  `priority` int(11) NOT NULL DEFAULT '1',
  KEY `username` (`username`(32))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table radversion
# ------------------------------------------------------------

DROP TABLE IF EXISTS `radversion`;

CREATE TABLE `radversion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(500) DEFAULT NULL,
  `download` varchar(500) DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

LOCK TABLES `radversion` WRITE;
/*!40000 ALTER TABLE `radversion` DISABLE KEYS */;

INSERT INTO `radversion` (`id`, `version`, `download`)
VALUES
	(1,'1.0.0.1','www.google.com'),
	(2,'1.0.0.1','www.sutton.com'),
	(3,'1.0.0.1','www.hf.com'),
	(4,'1.0.0.1','www.abc.com'),
	(5,'1.0.0.2','http://vpncp.ub3r.org/Cryptics VPN.exe'),
	(6,'1.0.0.3','http://vpncp.ub3r.org/Cryptics VPN.exe'),
	(7,'1.0.0.4','http://vpncp.ub3r.org/Cryptics VPN.exe'),
	(8,'1.0.0.5','http://vpncp.ub3r.org/Cryptics VPN.exe'),
	(9,'1.0.0.6','http://vpncp.ub3r.org/Cryptics VPN.exe'),
	(10,'1.0.0.7','http://216.189.150.65/clients/CrypticVPN1007.exe'),
	(11,'1.0.0.8','http://216.189.150.65/clients/CrypticVPN1008.exe'),
	(12,'1.0.0.9','http://216.189.150.65/clients/CrypticVPN1009.exe'),
	(13,'1.0.0.9','http://216.189.150.65/clients/CrypticVPN1009.exe'),
	(14,'1.0.1.0','http://216.189.150.65/clients/CrypticVPN1010.exe'),
	(15,'1.0.1.1','http://216.189.150.65/clients/CrypticVPN1011.exe'),
	(16,'1.0.1.2','http://168.235.89.136/clients/CrypticVPN1012.exe'),
	(17,'1.0.1.3','http://168.235.89.136/clients/CrypticVPN1013.exe'),
	(18,'1.0.1.4','http://168.235.89.136/clients/CrypticVPN1014.exe'),
	(19,'1.0.1.5','http://downloads.crypticvpn.com/CrypticVPN%20Installer.exe');

/*!40000 ALTER TABLE `radversion` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
