-- 0: Don't show if calculation fails | 1: Show if calculation fails
ALTER TABLE `shipping_services` ADD COLUMN `show_calculation_fails`  tinyint DEFAULT 1;


ALTER TABLE `shipping_apis` ADD COLUMN `partner_id`  int DEFAULT 0;

ALTER TABLE `shipping_apis` ADD COLUMN `percental_partner_upcharge`  decimal(18,2) DEFAULT 0;



SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for customer_shipping_report
-- ----------------------------
DROP TABLE IF EXISTS `customer_shipping_report`;
CREATE TABLE `customer_shipping_report` (
  `id` bigint(20) NOT NULL DEFAULT '0',
  `customer_id` bigint(20) DEFAULT NULL,
  `location_id` bigint(20) DEFAULT NULL,
  `carrier_id` int(11) DEFAULT NULL,
  `shipping_service_id` int(11) DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL,
  `weight` decimal(18,2) DEFAULT NULL,
  `customs_id` bigint(20) DEFAULT NULL,
  `api_account_id` bigint(20) DEFAULT NULL,
  `api_account_no` varchar(100) DEFAULT NULL,
  `postal_charge` decimal(18,2) DEFAULT NULL,
  `upcharge` decimal(18,2) DEFAULT NULL,
  `shipping_date` bigint(20) DEFAULT NULL,
  `completed_by` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of customer_shipping_report
-- ----------------------------
ALTER TABLE `customer_shipping_report`
	CHANGE COLUMN `id` `id` BIGINT(20) NOT NULL AUTO_INCREMENT FIRST;
	

ALTER TABLE `customer_shipping_report`
	ADD COLUMN `source_package_id` BIGINT(20) NULL DEFAULT NULL COMMENT 'if direct, that is envelope_id . else that is package_id' AFTER `location_id`;