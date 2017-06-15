/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : clevvermail_new
Target Host     : localhost:3306
Target Database : clevvermail_new
Date: 2016-09-06 23:47:41
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for web_message_log
-- ----------------------------
DROP TABLE IF EXISTS `web_message_log`;
CREATE TABLE `web_message_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `request_from` varchar(20) NOT NULL,
  `request_by` bigint(20) DEFAULT NULL,
  `uri` varchar(255) DEFAULT NULL,
  `uri_param` varchar(4000) DEFAULT NULL,
  `request_method` varchar(10) DEFAULT NULL,
  `request_header` varchar(500) DEFAULT NULL,
  `request_param` longtext,
  `request_date` bigint(20) DEFAULT NULL,
  `ip_address` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of web_message_log
-- ----------------------------

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for log_audit_message
-- ----------------------------
DROP TABLE IF EXISTS `log_audit_message`;
CREATE TABLE `log_audit_message` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `level` varchar(10) DEFAULT NULL,
  `type` varchar(250) DEFAULT NULL,
  `message` text,
  `created_date` bigint(20) DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `created_by_type` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;