/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : clevvermail_new
Target Host     : localhost:3306
Target Database : clevvermail_new
Date: 2016-07-09 00:15:54
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for cron_job_data
-- ----------------------------
DROP TABLE IF EXISTS `cron_job_data`;
CREATE TABLE `cron_job_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_name` varchar(100) DEFAULT NULL,
  `param_name` varchar(100) DEFAULT NULL,
  `param_value` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cron_job_data
-- ----------------------------
