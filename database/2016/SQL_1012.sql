INSERT INTO `settings` VALUES (0, '000220', '5', '5', 'ESTIMATE_SHIPPING_COST_LETTER_NATIONAL', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0');
INSERT INTO `settings` VALUES (0, '000221', '10', '10', 'ESTIMATE_SHIPPING_COST_LETTER_INNATIONAL', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0');
INSERT INTO `settings` VALUES (0, '000222', '20', '20', 'ESTIMATE_SHIPPING_COST_PACKAGE_NATIONAL', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0');
INSERT INTO `settings` VALUES (0, '000223', '50', '50', 'ESTIMATE_SHIPPING_COST_PACKAGE_INNATIONAL', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0');




SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for envelope_prepayment_request
-- ----------------------------
DROP TABLE IF EXISTS `envelope_prepayment_request`;

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for envelope_prepayment_cost
-- ----------------------------
DROP TABLE IF EXISTS `envelope_prepayment_cost`;
CREATE TABLE `envelope_prepayment_cost` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `envelope_id` bigint(20) DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `postbox_id` bigint(20) DEFAULT NULL,
  `envelope_scan_cost` decimal(20,0) DEFAULT NULL,
  `item_scan_cost` decimal(10,0) DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  `updated_date` bigint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=628 DEFAULT CHARSET=utf8;