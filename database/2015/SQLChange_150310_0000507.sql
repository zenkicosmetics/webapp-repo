/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : clevvermail01
Target Host     : localhost:3306
Target Database : clevvermail01
Date: 2015-03-10 18:25:21
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for envelope_comment
-- ----------------------------
DROP TABLE IF EXISTS `envelope_comment`;
CREATE TABLE `envelope_comment` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) DEFAULT NULL,
  `envelope_id` bigint(20) DEFAULT NULL,
  `text` text,
  `created_date` bigint(20) DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `last_updated_date` bigint(20) DEFAULT NULL,
  `last_updated_by` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
