-- ----------------------------
-- Insert data to pricing table
-- ----------------------------
INSERT INTO `pricing`(account_type, item_name, item_value, item_description, item_unit, pricing_template_id) VALUES ( '1', 'paypal_transaction_fee', '3', 'paypal transaction fee', '%', '1');
INSERT INTO `pricing`(account_type, item_name, item_value, item_description, item_unit, pricing_template_id)  VALUES ('2', 'paypal_transaction_fee', '3', 'paypal_transaction_fee', '%', '1');
INSERT INTO `pricing`(account_type, item_name, item_value, item_description, item_unit, pricing_template_id)  VALUES ('3', 'paypal_transaction_fee', '3', 'paypal_transaction_fee', '%', '1');
INSERT INTO `pricing`(account_type, item_name, item_value, item_description, item_unit, pricing_template_id)  VALUES ('1', 'paypal_transaction_vat', '0.57', 'paypal_transaction_vat', 'EUR', '1');
INSERT INTO `pricing`(account_type, item_name, item_value, item_description, item_unit, pricing_template_id)  VALUES ('2', 'paypal_transaction_vat', '0.57', 'paypal_transaction_vat', 'EUR', '1');
INSERT INTO `pricing`(account_type, item_name, item_value, item_description, item_unit, pricing_template_id)  VALUES ('3', 'paypal_transaction_vat', '0.57', 'paypal_transaction_vat', 'EUR', '1');


/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : clevvermail01
Target Host     : localhost:3306
Target Database : clevvermail01
Date: 2015-03-17 04:45:00
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for paypal_transaction_hist
-- ----------------------------
DROP TABLE IF EXISTS `paypal_transaction_hist`;
CREATE TABLE `paypal_transaction_hist` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) DEFAULT NULL,
  `invoice_id` varchar(250) DEFAULT NULL,
  `amount` decimal(18,10) DEFAULT NULL COMMENT 'Total amount (include vat and transaction fee)',
  `paypal_tran_fee` decimal(18,10) DEFAULT NULL,
  `paypal_tran_vat` decimal(18,10) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `last_updated_date` bigint(20) DEFAULT NULL,
  `message` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of paypal_transaction_hist
-- ----------------------------
