ALTER TABLE app_external
ADD COLUMN customer_id int;


SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for customer_message
-- ----------------------------
DROP TABLE IF EXISTS `customer_message`;
CREATE TABLE `customer_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) DEFAULT NULL,
  `message` varchar(1000) DEFAULT NULL,
  `message_type` varchar(100) DEFAULT NULL,
  `read_flag` tinyint(4) DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  `read_date` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


ALTER TABLE invoice_detail
ADD start_invoice_date varchar(8) after location_id;

ALTER TABLE invoice_detail
ADD end_invoice_date varchar(8) after location_id;


ALTER TABLE invoice_summary
ADD api_access_amount double DEFAULT 0 after payment_transaction_id;


ALTER TABLE invoice_summary_by_location
ADD api_access_amount double DEFAULT 0 after payment_transaction_id;

-- Add enterprise cost
ALTER TABLE invoice_summary
ADD own_location_amount double DEFAULT 0 after payment_transaction_id;

ALTER TABLE invoice_summary_by_location
ADD own_location_amount double DEFAULT 0 after payment_transaction_id;

ALTER TABLE invoice_summary
ADD touch_panel_own_location_amount double DEFAULT 0 after payment_transaction_id;

ALTER TABLE invoice_summary_by_location
ADD touch_panel_own_location_amount double DEFAULT 0 after payment_transaction_id;

ALTER TABLE invoice_summary
ADD own_mobile_app_amount double DEFAULT 0 after payment_transaction_id;

ALTER TABLE invoice_summary_by_location
ADD own_mobile_app_amount double DEFAULT 0 after payment_transaction_id;

ALTER TABLE invoice_summary
ADD clevver_subdomain_amount double DEFAULT 0 after payment_transaction_id;

ALTER TABLE invoice_summary_by_location
ADD clevver_subdomain_amount double DEFAULT 0 after payment_transaction_id;

ALTER TABLE invoice_summary
ADD own_subdomain_amount double DEFAULT 0 after payment_transaction_id;

ALTER TABLE invoice_summary_by_location
ADD own_subdomain_amount double DEFAULT 0 after payment_transaction_id;