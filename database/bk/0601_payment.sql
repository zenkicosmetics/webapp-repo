/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : virtualpost
Target Host     : localhost:3306
Target Database : virtualpost
Date: 2014-01-06 02:41:59
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for payment
-- ----------------------------
DROP TABLE IF EXISTS `payment`;
CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `account_type` varchar(30) DEFAULT NULL,
  `card_type` varchar(255) DEFAULT NULL,
  `card_number` varchar(255) DEFAULT NULL,
  `card_name` varchar(255) DEFAULT NULL,
  `cvc` varchar(3) DEFAULT NULL,
  `expired_year` varchar(2) DEFAULT NULL,
  `expired_month` varchar(2) DEFAULT NULL,
  `card_confirm_flag` tinyint(4) DEFAULT '0' COMMENT '0: Not confirm | 1: Confirm',
  `callback_tran_id` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`payment_id`),
  UNIQUE KEY `payment_id` (`payment_id`),
  UNIQUE KEY `payment_uk` (`customer_id`,`card_number`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of payment
-- ----------------------------
INSERT INTO `payment` VALUES ('6', '1', '30', 'M', '5500000000000004\r', 'Nguyen Trong Dung 02', '123', '14', '12', null, null);
INSERT INTO `payment` VALUES ('7', '3', '30', 'V', '4012001037141112', 'Nguyen', '123', '13', '12', null, null);
INSERT INTO `payment` VALUES ('8', '8', '30', 'V', '4111111111111111', 'Nguyen Trong Dung', '123', '13', '12', null, null);
