/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : clevvermail_dev
Target Host     : localhost:3306
Target Database : clevvermail_dev
Date: 2016-04-07 23:37:12
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for push_message_notification
-- ----------------------------
DROP TABLE IF EXISTS `push_message_notification`;
CREATE TABLE `push_message_notification` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) NOT NULL,
  `postbox_id` bigint(20) DEFAULT NULL,
  `envelope_id` bigint(20) DEFAULT NULL,
  `notify_type` varchar(20) NOT NULL,
  `platform` varchar(20) DEFAULT NULL,
  `push_id` varchar(1000) NOT NULL,
  `message` varchar(1000) NOT NULL,
  `sent_flag` tinyint(4) NOT NULL COMMENT '0: Not send | 1: sent',
  `created_date` bigint(20) NOT NULL,
  `sent_date` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of push_message_notification
-- ----------------------------


/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : clevvermail_dev
Target Host     : localhost:3306
Target Database : clevvermail_dev
Date: 2016-04-07 23:37:29
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for customer_push_token
-- ----------------------------
DROP TABLE IF EXISTS `customer_push_token`;
CREATE TABLE `customer_push_token` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mobile_device_id` varchar(100) DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `platform` varchar(20) DEFAULT NULL,
  `push_id` varchar(1000) DEFAULT NULL,
  `active_flag` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of customer_push_token
-- ----------------------------
