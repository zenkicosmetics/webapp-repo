/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : clevvermail
Target Host     : localhost:3306
Target Database : clevvermail
Date: 2016-03-11 00:55:55
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for api_message_log
-- ----------------------------
DROP TABLE IF EXISTS `api_message_log`;
CREATE TABLE `api_message_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) DEFAULT NULL,
  `uri` varchar(255) DEFAULT NULL,
  `request_method` varchar(10) DEFAULT NULL,
  `request_header` varchar(500) DEFAULT NULL,
  `request_param` varchar(500) DEFAULT NULL,
  `request_date` bigint(20) DEFAULT NULL,
  `ip_address` varchar(30) DEFAULT NULL,
  `response` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of api_message_log
-- ----------------------------
