# Host: localhost  (Version: 5.5.53)
# Date: 2019-04-18 23:14:24
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "demo"
#

DROP TABLE IF EXISTS `demo`;
CREATE TABLE `demo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `created_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

#
# Data for table "demo"
#

INSERT INTO `demo` VALUES (1,'张三','2019-04-15 20:48:45'),(2,'李四','2019-04-15 20:49:28'),(3,'王五','2019-04-15 20:49:36'),(4,'赵六','2019-04-15 20:49:50');
