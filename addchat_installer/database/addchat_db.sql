
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table ac_messages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ac_messages`;

CREATE TABLE `ac_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `m_from` int(11) NOT NULL DEFAULT '0',
  `m_to` int(11) NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `m_from_delete` tinyint(1) NOT NULL DEFAULT '0',
  `m_to_delete` tinyint(1) NOT NULL DEFAULT '0',
  `dt_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ac_profiles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ac_profiles`;

CREATE TABLE `ac_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `fullname` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:offline;1:online;2:away;3:busy',
  `dt_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table ac_settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ac_settings`;

CREATE TABLE `ac_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `s_name` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `s_value` text COLLATE utf8mb4_unicode_ci,
  `dt_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `ac_settings` WRITE;
/*!40000 ALTER TABLE `ac_settings` DISABLE KEYS */;

INSERT INTO `ac_settings` (`id`, `s_name`, `s_value`, `dt_updated`)
VALUES
	(1,'admin_user_id','1','2019-10-18 12:53:26'),
	(2,'pagination_limit','5','2019-10-18 12:53:26'),
	(3,'img_upload_path','upload','2019-03-06 00:00:00'),
	(4,'assets_path','assets','2019-10-18 12:53:26'),
	(5,'users_table','users','2019-10-18 12:53:26'),
	(6,'users_col_id','id','2019-10-18 12:53:26'),
	(7,'users_col_email','email','2019-10-18 12:53:26'),
	(8,'site_name','AddChat','2019-10-18 12:53:26'),
	(9,'site_logo',NULL,'2019-09-06 08:25:52'),
	(10,'chat_icon',NULL,'2019-09-06 08:24:20'),
	(11,'notification_type','0','2019-10-18 12:53:26'),
	(12,'footer_text','AddChat | by Classiebit','2019-10-18 12:53:26'),
	(13,'footer_url','https://classiebit.com/addchat-codeigniter-pro','2019-10-18 12:53:26');

/*!40000 ALTER TABLE `ac_settings` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table ac_users_messages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ac_users_messages`;

CREATE TABLE `ac_users_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `buddy_id` int(11) NOT NULL,
  `messages_count` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_id` (`users_id`,`buddy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
