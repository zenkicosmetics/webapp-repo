/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : clevvermail_new
Target Host     : localhost:3306
Target Database : clevvermail_new
Date: 2016-07-18 21:07:01
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for auto_sequence
-- ----------------------------
DROP TABLE IF EXISTS `auto_sequence`;
CREATE TABLE `auto_sequence` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of auto_sequence
-- ----------------------------
