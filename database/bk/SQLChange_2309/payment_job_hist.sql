/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : virtualpost
Target Host     : localhost:3306
Target Database : virtualpost
Date: 2014-09-23 23:17:02
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for payment_job_hist
-- ----------------------------
DROP TABLE IF EXISTS `payment_job_hist`;
CREATE TABLE `payment_job_hist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) DEFAULT NULL,
  `open_balance_baseline` varchar(8) DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `open_balance` decimal(11,2) DEFAULT NULL,
  `job_status` tinyint(30) DEFAULT NULL,
  `payment_status` varchar(30) DEFAULT NULL,
  `reference` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `PaymentJob_Hist` (`customer_id`,`open_balance_baseline`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of payment_job_hist
-- ----------------------------
INSERT INTO `payment_job_hist` VALUES ('3', '1', '31082014', '1410963400', '1', '592.46', '0', null, null);
