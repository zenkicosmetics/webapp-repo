/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : virtualpost
Target Host     : localhost:3306
Target Database : virtualpost
Date: 2014-09-23 23:17:44
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for external_tran_hist
-- ----------------------------
DROP TABLE IF EXISTS `external_tran_hist`;
CREATE TABLE `external_tran_hist` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tran_id` bigint(20) DEFAULT NULL,
  `tran_date` varchar(10) DEFAULT NULL,
  `tran_amount` decimal(10,2) DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of external_tran_hist
-- ----------------------------
INSERT INTO `external_tran_hist` VALUES ('2', '1111122222', '20140822', '123.00', '1', '1408785551');
INSERT INTO `external_tran_hist` VALUES ('3', '1111111122222', '20140822', '-124.21', '1', '1408785565');
