ALTER TABLE country
ADD phone_country_code varchar(30);


ALTER TABLE country
ADD country_code_3 varchar(3);


-- -----------------------------------------------
-- customers_setting table 
-- -----------------------------------------------
DROP TABLE IF EXISTS `customers_setting`;
CREATE TABLE `customers_setting` (
	`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`parent_customer_id` BIGINT(20) NOT NULL,
	`setting_key` VARCHAR(50) NULL DEFAULT NULL,
	`setting_value` VARCHAR(250) NULL DEFAULT NULL,
	`alias01` VARCHAR(20) NULL DEFAULT NULL,
	`created_date` INT(11) NULL DEFAULT NULL,
	`modified_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`alias02` VARCHAR(100) NULL DEFAULT NULL,
	`alias03` VARCHAR(100) NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	INDEX `customer_id_setting_key` (`parent_customer_id`, `setting_key`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;


-- CUSTOMER TABLE
ALTER TABLE customers
ADD COLUMN parent_customer_id bigint NULL after customer_id;

ALTER TABLE customers
ADD COLUMN role_flag tinyint NULL after parent_customer_id;

ALTER TABLE `customers`
	ADD COLUMN `language`  varchar(30) NULL DEFAULT NULL;

ALTER TABLE `customers`
	ADD COLUMN `date_format`  varchar(10) NULL DEFAULT NULL;

alter table customers
ADD customer_type tinyint NULL DEFAULT NULL;
ALTER TABLE `customers`
	ADD COLUMN `vat_rate` DOUBLE NULL DEFAULT NULL;

-- --------------------- location table -----------------------------------------------
ALTER TABLE `location`
	ADD COLUMN `share_external_flag`  tinyint NULL DEFAULT 1;
	
DROP TABLE IF EXISTS `location_customers`;
CREATE TABLE `location_customers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `location_id` bigint(20) NOT NULL,
  `parent_customer_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


-- --------------------- invoice table -----------------------------------------------
DROP TABLE IF EXISTS  invoice_summary_by_user;
CREATE TABLE `invoice_summary_by_user` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`invoice_code` VARCHAR(50) NULL DEFAULT NULL,
	`location_id` BIGINT(20) NULL DEFAULT '1' COMMENT 'Default is berlin location',
	`customer_id` BIGINT(20) NULL DEFAULT NULL,
	`invoice_month` VARCHAR(8) NULL DEFAULT NULL,
	`free_postboxes_amount` DOUBLE NULL DEFAULT '0',
	`private_postboxes_amount` DOUBLE NULL DEFAULT '0',
	`business_postboxes_amount` DOUBLE NULL DEFAULT '0',
	`incomming_items_free_account` DOUBLE NULL DEFAULT '0',
	`incomming_items_private_account` DOUBLE NULL DEFAULT '0',
	`incomming_items_business_account` DOUBLE NULL DEFAULT '0',
	`envelope_scan_free_account` DOUBLE NULL DEFAULT '0',
	`envelope_scan_private_account` DOUBLE NULL DEFAULT '0',
	`envelope_scan_business_account` DOUBLE NULL DEFAULT '0',
	`item_scan_free_account` DOUBLE NULL DEFAULT '0',
	`item_scan_private_account` DOUBLE NULL DEFAULT '0',
	`item_scan_business_account` DOUBLE NULL DEFAULT '0',
	`additional_pages_scanning` DOUBLE NULL DEFAULT '0',
	`direct_shipping_free_account` DOUBLE NULL DEFAULT '0',
	`direct_shipping_private_account` DOUBLE NULL DEFAULT '0',
	`direct_shipping_business_account` DOUBLE NULL DEFAULT '0',
	`collect_shipping_free_account` DOUBLE NULL DEFAULT '0',
	`collect_shipping_private_account` DOUBLE NULL DEFAULT '0',
	`collect_shipping_business_account` DOUBLE NULL DEFAULT '0',
	`storing_letters_free_account` DOUBLE NULL DEFAULT '0',
	`storing_letters_private_account` DOUBLE NULL DEFAULT '0',
	`storing_letters_business_account` DOUBLE NULL DEFAULT '0',
	`storing_packages_free_account` DOUBLE NULL DEFAULT '0',
	`storing_packages_private_account` DOUBLE NULL DEFAULT '0',
	`storing_packages_business_account` DOUBLE NULL DEFAULT '0',
	`invoice_flag` TINYINT(4) NULL DEFAULT '0' COMMENT '1: La da thanh toan (se khong thong ke de thanh toan lai) |  0: La doi tuong thanh toan',
	`payment_1st_flag` TINYINT(4) NULL DEFAULT NULL,
	`payment_2st_flag` TINYINT(4) NULL DEFAULT NULL,
	`free_postboxes_quantity` INT(11) NULL DEFAULT '0',
	`free_postboxes_netprice` DOUBLE NULL DEFAULT '0',
	`private_postboxes_quantity` INT(11) NULL DEFAULT '0',
	`private_postboxes_netprice` DOUBLE NULL DEFAULT '0',
	`business_postboxes_quantity` INT(11) NULL DEFAULT '0',
	`business_postboxes_netprice` DOUBLE NULL DEFAULT '0',
	`incomming_items_free_quantity` INT(11) NULL DEFAULT '0',
	`incomming_items_free_netprice` DOUBLE NULL DEFAULT '0',
	`incomming_items_private_quantity` INT(11) NULL DEFAULT '0',
	`incomming_items_private_netprice` DOUBLE NULL DEFAULT '0',
	`incomming_items_business_quantity` INT(11) NULL DEFAULT '0',
	`incomming_items_business_netprice` DOUBLE NULL DEFAULT '0',
	`envelope_scan_free_quantity` INT(11) NULL DEFAULT '0',
	`envelope_scan_free_netprice` DOUBLE NULL DEFAULT '0',
	`envelope_scan_private_quantity` INT(11) NULL DEFAULT '0',
	`envelope_scan_private_netprice` DOUBLE NULL DEFAULT '0',
	`envelope_scan_business_quantity` INT(11) NULL DEFAULT '0',
	`envelope_scan_business_netprice` DOUBLE NULL DEFAULT '0',
	`item_scan_free_quantity` INT(11) NULL DEFAULT '0',
	`item_scan_free_netprice` DOUBLE NULL DEFAULT '0',
	`item_scan_private_quantity` INT(11) NULL DEFAULT '0',
	`item_scan_private_netprice` DOUBLE NULL DEFAULT '0',
	`item_scan_business_quantity` INT(11) NULL DEFAULT '0',
	`item_scan_business_netprice` DOUBLE NULL DEFAULT '0',
	`additional_pages_scanning_quantity` INT(11) NULL DEFAULT '0',
	`additional_pages_scanning_netprice` DOUBLE NULL DEFAULT '0',
	`direct_shipping_free_quantity` INT(11) NULL DEFAULT '0',
	`direct_shipping_free_netprice` DOUBLE NULL DEFAULT '0',
	`direct_shipping_private_quantity` INT(11) NULL DEFAULT '0',
	`direct_shipping_private_netprice` DOUBLE NULL DEFAULT '0',
	`direct_shipping_business_quantity` INT(11) NULL DEFAULT '0',
	`direct_shipping_business_netprice` DOUBLE NULL DEFAULT '0',
	`collect_shipping_free_quantity` INT(11) NULL DEFAULT '0',
	`collect_shipping_free_netprice` DOUBLE NULL DEFAULT '0',
	`collect_shipping_private_quantity` INT(11) NULL DEFAULT '0',
	`collect_shipping_private_netprice` DOUBLE NULL DEFAULT '0',
	`collect_shipping_business_quantity` INT(11) NULL DEFAULT '0',
	`collect_shipping_business_netprice` DOUBLE NULL DEFAULT '0',
	`storing_letters_free_quantity` INT(11) NULL DEFAULT '0',
	`storing_letters_free_netprice` DOUBLE NULL DEFAULT '0',
	`storing_letters_private_quantity` INT(11) NULL DEFAULT '0',
	`storing_letters_private_netprice` DOUBLE NULL DEFAULT '0',
	`storing_letters_business_quantity` INT(11) NULL DEFAULT '0',
	`storing_letters_business_netprice` DOUBLE NULL DEFAULT '0',
	`storing_packages_free_quantity` INT(11) NULL DEFAULT '0',
	`storing_packages_free_netprice` DOUBLE NULL DEFAULT '0',
	`storing_packages_private_quantity` INT(11) NULL DEFAULT '0',
	`storing_packages_private_netprice` DOUBLE NULL DEFAULT '0',
	`storing_packages_business_quantity` INT(11) NULL DEFAULT '0',
	`storing_packages_business_netprice` DOUBLE NULL DEFAULT '0',
	`invoice_file_path` VARCHAR(250) NULL DEFAULT NULL,
	`total_invoice` DOUBLE NULL DEFAULT NULL,
	`vat` DOUBLE NULL DEFAULT NULL,
	`vat_case` INT(4) NULL DEFAULT NULL,
	`invoice_type` VARCHAR(10) NULL DEFAULT '1',
	`payment_1st_amount` DECIMAL(10,2) NULL DEFAULT '0.00',
	`payment_2st_amount` DECIMAL(10,2) NULL DEFAULT '0.00',
	`send_invoice_flag` TINYINT(4) NULL DEFAULT '0',
	`send_invoice_date` BIGINT(20) NULL DEFAULT NULL,
	`additional_pages_scanning_free_quantity` INT(11) NULL DEFAULT '0',
	`additional_pages_scanning_private_quantity` INT(11) NULL DEFAULT '0',
	`additional_pages_scanning_business_quantity` INT(11) NULL DEFAULT '0',
	`additional_pages_scanning_free_netprice` DOUBLE NULL DEFAULT '0',
	`additional_pages_scanning_private_netprice` DOUBLE NULL DEFAULT '0',
	`additional_pages_scanning_business_netprice` DOUBLE NULL DEFAULT '0',
	`additional_pages_scanning_free_amount` DOUBLE NULL DEFAULT '0',
	`additional_pages_scanning_private_amount` DOUBLE NULL DEFAULT '0',
	`additional_pages_scanning_business_amount` DOUBLE NULL DEFAULT '0',
	`custom_declaration_outgoing_quantity_01` INT(11) NULL DEFAULT '0',
	`custom_declaration_outgoing_quantity_02` INT(11) NULL DEFAULT '0',
	`custom_declaration_outgoing_price_01` DECIMAL(18,10) NULL DEFAULT '0.0000000000',
	`custom_declaration_outgoing_price_02` DECIMAL(18,10) NULL DEFAULT '0.0000000000',
	`payment_transaction_id` VARCHAR(300) NULL DEFAULT NULL,
	`created_date` DATETIME NULL DEFAULT NULL,
	`created_by_type` TINYINT(4) NULL DEFAULT NULL,
	`created_by_id` BIGINT(20) NULL DEFAULT NULL,
	`last_modified_date` DATETIME NULL DEFAULT NULL,
	`last_modified_by_type` TINYINT(4) NULL DEFAULT NULL,
	`last_modified_by_id` BIGINT(20) NULL DEFAULT NULL,
	`deleted_flag` TINYINT(4) NULL DEFAULT '0',
	`postbox_fee_upcharge` DOUBLE NULL DEFAULT '0',
	`additional_incomming_item_upcharge` DOUBLE NULL DEFAULT '0',
	`envelope_scan_upcharge` DOUBLE NULL DEFAULT '0',
	`item_scan_upcharge` DOUBLE NULL DEFAULT '0',
	`direct_shipping_upcharge` DOUBLE NULL DEFAULT '0',
	`direct_shipping_postal_upcharge` DOUBLE NULL DEFAULT '0',
	`collect_shipping_upcharge` DOUBLE NULL DEFAULT '0',
	`collect_shipping_postal_upcharge` DOUBLE NULL DEFAULT '0',
	`storing_letter_upcharge` DOUBLE NULL DEFAULT '0',
	`storing_package_upcharge` DOUBLE NULL DEFAULT '0',
	`included_page_scan_upcharge` DOUBLE NULL DEFAULT '0',
	`custom_declaration_outgoing_01_upcharge` DOUBLE NULL DEFAULT '0',
	`custom_declaration_outgoing_02_upcharge` DOUBLE NULL DEFAULT '0',
	`custom_handling_import_upcharge` DOUBLE NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;

-- ------------------------ EMAIL TABLE=-------------------------------
alter table emails
ADD language varchar(30);

alter table emails
ADD relevant_enterprise_account tinyint default 0;
ALTER TABLE `emails`
	ADD COLUMN `code` VARCHAR(4) NOT NULL DEFAULT '0' AFTER `id`;

UPDATE emails SET language = 'English';


update emails
set code = right(concat('0000',id), 4);

-- -------------------------------- email customer table =---------------------

CREATE TABLE `email_customer` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`code` VARCHAR(4) NOT NULL DEFAULT '0',
	`customer_id` BIGINT(20) NOT NULL,
	`slug` VARCHAR(255) NULL DEFAULT NULL,
	`subject` VARCHAR(255) NULL DEFAULT NULL,
	`description` VARCHAR(500) NULL DEFAULT NULL,
	`content` MEDIUMTEXT NULL,
	`language` VARCHAR(30) NULL DEFAULT NULL,
	`relevant_enterprise_account` TINYINT(4) NULL DEFAULT NULL,
	`created_date` DATETIME NULL DEFAULT NULL,
	`created_by_type` TINYINT(4) NULL DEFAULT NULL,
	`created_by_id` BIGINT(20) NULL DEFAULT NULL,
	`last_modified_date` DATETIME NULL DEFAULT NULL,
	`last_modified_by_type` TINYINT(4) NULL DEFAULT NULL,
	`last_modified_by_id` BIGINT(20) NULL DEFAULT NULL,
	`deleted_flag` TINYINT(4) NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `product_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` varchar(100) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `created_date` int(11) DEFAULT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `products` (`product_id`, `product_name`, `description`, `created_date`) VALUES
	(1, 'ClevverMail', 'Clevvermail product', NULL),
	(2, 'ClevverPhone', 'ClevverPhone product', NULL);



CREATE TABLE `customer_product_settings` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`customer_id` BIGINT(20) NOT NULL,
	`product_id` INT(11) NOT NULL,
	`setting_key` VARCHAR(50) NOT NULL,
	`setting_value` VARCHAR(10) NULL DEFAULT NULL,
	`created_date` INT(11) NULL DEFAULT NULL,
	`modified_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	INDEX `customer_id_product_id_setting_key` (`customer_id`, `product_id`, `setting_key`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;


ALTER TABLE `terms_services`
	ADD COLUMN `customer_id` BIGINT(20) NOT NULL DEFAULT '0' AFTER `id`;




ALTER TABLE `cases_verification_settings`
	ADD COLUMN `is_user_company` TINYINT(4) NULL DEFAULT NULL COMMENT '0: not user company| 1: user company' AFTER `deleted_flag`;



drop table if exists cases_phone_number;
CREATE TABLE `cases_phone_number` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`case_id` INT(11) NULL DEFAULT NULL,
	`description` VARCHAR(500) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`created_date` INT(11) NULL DEFAULT NULL,
	`updated_date` INT(11) NULL DEFAULT NULL,
	`status` TINYINT(4) NULL DEFAULT NULL,
	`type` TINYINT(4) NULL DEFAULT '1' COMMENT '1: company|2:not company',
	`update_by` INT(11) NULL DEFAULT NULL,
	`comment_content` TEXT NULL  COLLATE 'utf8_unicode_ci',
	`comment_date` INT(11) NULL DEFAULT NULL,
	`deleted_flag` TINYINT(4) NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
ALTER TABLE `cases`
	ADD COLUMN `target_id` VARCHAR(50) NULL DEFAULT NULL AFTER `last_modified_by_id`,
	ADD COLUMN `target_type` VARCHAR(50) NULL DEFAULT 'postbox' AFTER `target_id`;








