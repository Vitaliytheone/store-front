# ************************************************************
# Sequel Pro SQL dump
# Версия 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Адрес: localhost (MySQL 5.6.35)
# Схема: panels
# Время создания: 2018-07-23 07:45:35 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Дамп таблицы notification_email
# ------------------------------------------------------------

CREATE TABLE `notification_email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` text NOT NULL,
  `message` longtext NOT NULL,
  `code` varchar(300) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `notification_email` WRITE;
/*!40000 ALTER TABLE `notification_email` DISABLE KEYS */;

INSERT INTO `notification_email` (`id`, `subject`, `message`, `code`, `enabled`)
VALUES
	(4,'Panel created','Panel created','panel_created',1),
	(5,'Restore password','Restore password \r\n{{restore_url}}','restore_password',1),
	(6,'Email changed','Email changed','email_changed',1),
	(7,'Password changed','Password changed','password_changed',1),
	(8,'Panel has beed frozen','Panel has beed frozen','panel_frozen',1),
	(9,'Panel will be expired','Panel will be expired','panel_expired',1),
	(10,'2Checkout payment under review','2Checkout payment under review','2checkout_review',1),
	(11,'2Checkout fraud review passed','2Checkout fraud review passed','2checkout_pass',1),
	(12,'2Checkout fraud review failed','2Checkout fraud review failed','2checkout_failed',1),
	(13,'Domain created','Domain created','domain_issued',1),
	(14,'SSL certificate issued','SSL certificate issued','ssl_issued',1),
	(15,'Invoice created','Invoice created','invoice_created',1),
	(16,'New message','New message','new_message',1),
	(17,'New ticket','New ticket','new_ticket',1),
	(18,'Paypal payment under review','Paypal payment under review','paypal_review',1),
	(19,'Paypal fraud review passed','Paypal fraud review passed','paypal_pass',1),
	(20,'Paypal fraud review failed edited 1','Paypal fraud review failed edited 1','paypal_failed',1),
	(21,'Paypal payment verification required','To confirm the authenticity of your billing account, please click on the link below. \r\n{{verify_link}}','paypal_verify',1),
	(22,'PayPal verification for perfectpanel.com','Please follow below link to verify your PayPal email\r\n{{verify_link}}','paypal_verify',1),
	(23,'SSL certificate renewed','SSL certificate renewed','ssl_renewed',0),
	(24,'Domain renewed','Domain renewed','domain_renewed',0);

/*!40000 ALTER TABLE `notification_email` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
