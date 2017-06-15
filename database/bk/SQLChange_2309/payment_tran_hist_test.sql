/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : virtualpost
Target Host     : localhost:3306
Target Database : virtualpost
Date: 2014-09-23 23:17:23
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for payment_tran_hist_test
-- ----------------------------
DROP TABLE IF EXISTS `payment_tran_hist_test`;
CREATE TABLE `payment_tran_hist_test` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) DEFAULT NULL,
  `tran_date` bigint(20) DEFAULT NULL,
  `tran_type` varchar(100) DEFAULT NULL,
  `pseudocardpan` varchar(30) DEFAULT NULL,
  `amount` decimal(10,0) DEFAULT NULL,
  `ccy` varchar(5) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `message` text,
  `invoice_id` bigint(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of payment_tran_hist_test
-- ----------------------------
INSERT INTO `payment_tran_hist_test` VALUES ('1', '8', '1405095181', 'authorize', null, '1', 'EUR', 'ERROR', 'Parameter {cardexpiredate} faulty or missing', null);
INSERT INTO `payment_tran_hist_test` VALUES ('2', '32025', '1407689371', 'authorize', '5500000049841368', '4', 'EUR', 'APPROVED', null, null);
