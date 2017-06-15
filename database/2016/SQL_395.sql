/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : clevvermail_new
Target Host     : localhost:3306
Target Database : clevvermail_new
Date: 2016-11-18 14:37:47
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for customer_payment_user
-- ----------------------------
DROP TABLE IF EXISTS `customer_payment_user`;
CREATE TABLE `customer_payment_user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) DEFAULT NULL,
  `userid` varchar(50) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL COMMENT 'payone|paypal',
  `created_date` bigint(20) DEFAULT NULL,
  `updated_date` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_payment_user_unique` (`customer_id`,`type`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of customer_payment_user
-- ----------------------------
