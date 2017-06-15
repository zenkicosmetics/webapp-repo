-- Create partner table.
CREATE TABLE `partner_partner` (
	`partner_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`partner_code` VARCHAR(20) NULL DEFAULT '0',
	`partner_name` VARCHAR(50) NULL DEFAULT '0',
	`company_name` VARCHAR(50) NULL DEFAULT '0',
	`location_street` VARCHAR(255) NULL DEFAULT '0',
	`location_zipcode` VARCHAR(20) NULL DEFAULT '0',
	`location_city` VARCHAR(60) NULL DEFAULT '0',
	`location_region` VARCHAR(255) NULL DEFAULT '0',
	`location_country` VARCHAR(30) NULL DEFAULT '0',
	`invoicing_street` VARCHAR(255) NULL DEFAULT '0',
	`invoicing_zipcode` VARCHAR(20) NULL DEFAULT '0',
	`invoicing_city` VARCHAR(60) NULL DEFAULT '0',
	`invoicing_region` VARCHAR(255) NULL DEFAULT '0',
	`invoicing_country` VARCHAR(30) NULL DEFAULT '0',
	`price_model` VARCHAR(30) NULL DEFAULT '0',
	`threhold_for_direct_prepay_charge` DOUBLE NULL DEFAULT '0',
	`rev_share_in_percent` DOUBLE NULL DEFAULT '0',
	`bank_name` VARCHAR(255) NULL DEFAULT '0',
	`bank_account_holder` VARCHAR(255) NULL DEFAULT '0',
	`iban` VARCHAR(255) NULL DEFAULT '0',
	`swift_bic` VARCHAR(255) NULL DEFAULT '0',
	`vat_number` DOUBLE NULL DEFAULT '0',
	`company_telephone` VARCHAR(20) NULL DEFAULT '0',
	`support_telephone` VARCHAR(20) NULL DEFAULT '0',
	`fax` VARCHAR(30) NULL DEFAULT '0',
	PRIMARY KEY (`partner_id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB;

-- Add new column for location
ALTER TABLE location add column `partner_id` INT UNSIGNED NULL;

-- Add new column for location
ALTER TABLE envelopes add column `location_id` INT UNSIGNED NULL;

-- Add new column for location
ALTER TABLE envelopes_completed add column `location_id` INT UNSIGNED NULL;

-- Add new column partner_id for user.
ALTER TABLE `users`
	ADD COLUMN `partner_id` INT NULL AFTER `location_available_id`;

-- 2 roles for partner + location.
INSERT INTO `groups` (`name`, `description`) VALUES ('partner', 'partner admin');
INSERT INTO `groups` (`name`, `description`) VALUES ('location', 'location admin');


-- Add new column for pricing
ALTER TABLE `pricing`
	ADD COLUMN `pricing_template_id` INT NOT NULL DEFAULT '0' AFTER `item_unit`;

ALTER TABLE `location`
	ADD COLUMN `pricing_template_id` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `partner_id`;

-- create pricing template table.
CREATE TABLE `pricing_template` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(100) NOT NULL,
	`description` VARCHAR(1000) NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

-- create digital devices table.
CREATE TABLE `partner_digital_devices` (
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`panel_code` VARCHAR(50) NOT NULL,
/*	`status` TINYINT NULL ,*/
	`description` VARCHAR(500) NULL DEFAULT '',
/*	`ip` VARCHAR(128) NULL DEFAULT '',*/
	`created_date` INT NULL DEFAULT '0',
	`modified_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `panel_id` (`panel_code`)
)COLLATE='utf8_general_ci' ENGINE=InnoDB;

-- add panel id into locaiton table
/*ALTER TABLE `location`
	ADD COLUMN `device_id` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `pricing_template_id`;*/



-- default pricing template
INSERT INTO `pricing_template` (`id`, `name`, `description`) VALUES (1, 'Default Template', 'This is default template pricing');

-- default data pricing. PRICING TEMPLTE ID = 1
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'address_number', '1', 'address', 'numbers', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'included_incomming_items', '0', 'included incomming items', NULL, 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'storage', '1', 'storage', 'GB', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'hand_sorting_of_advertising', 'No', 'hand sorting of advertising', NULL, 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'envelope_scanning_front', '5', 'envelope scanning (front)', NULL, 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'included_opening_scanning', '0', 'included opening and scanning', NULL, 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'storing_items_letters', '4', 'storing items (letters)', 'weeks', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'storing_items_packages', '1', 'storing items (packages)', 'week', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'storing_items_digitally', '1', 'storing items digitally', 'year', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'trashing_items', '-1', 'trashing items', 'unlimited', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'cloud_service_connection', 'Yes', 'cloud service connection', 'included', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'additional_incomming_items', '0.5', 'additional incomming items', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'envelop_scanning', '0.2', 'envelop scanning', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'opening_scanning', '1', 'opening and scanning', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'send_out_directly', '1', 'send out to original address directly', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'send_out_collected', '2', 'send out to original address collected', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'storing_items_over_free_letter', '0.05', 'storing items over free period (letters)', 'EUR/day', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'storing_items_over_free_packages', '0.20', 'storing items over free period (packages)', 'EUR/day', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'additional_private_mailbox', '4.95', 'additional private mailbox', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'additional_business_mailbox', '9.95', 'additional business mailbox ', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'address_number', '1', 'address', 'numbers', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'included_incomming_items', '10', 'included incomming items', NULL, 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'storage', '0', 'storage', 'GB', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'hand_sorting_of_advertising', 'Yes', 'hand sorting of advertising', NULL, 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'envelope_scanning_front', '10', 'envelope scanning (front)', NULL, 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'included_opening_scanning', '5', 'included opening and scanning', NULL, 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'storing_items_letters', '4', 'storing items (letters)', 'weeks', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'storing_items_packages', '1', 'storing items (packages)', 'week', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'storing_items_digitally', '1', 'storing items digitally', 'year', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'trashing_items', '-1', 'trashing items', 'unlimited', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'cloud_service_connection', 'Yes', 'cloud service connection', 'included', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'additional_incomming_items', '0.3', 'additional incomming items', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'envelop_scanning', '0.1', 'envelop scanning', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'opening_scanning', '0.5', 'opening and scanning', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'send_out_directly', '0.5', 'send out to original address directly', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'send_out_collected', '1', 'send out to original address collected', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'storing_items_over_free_letter', '0.04', 'storing items over free period (letters)', 'EUR/day', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'storing_items_over_free_packages', '0.15', 'storing items over free period (packages)', 'EUR/day', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'additional_private_mailbox', '4.95', 'additional private mailbox', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'additional_business_mailbox', '9.95', 'additional business mailbox ', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'address_number', '1', 'address', 'numbers', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'included_incomming_items', '50', 'included incomming items', NULL, 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'storage', '0', 'storage', 'GB', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'hand_sorting_of_advertising', 'Yes', 'hand sorting of advertising', NULL, 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'envelope_scanning_front', '50', 'envelope scanning (front)', NULL, 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'included_opening_scanning', '10', 'included opening and scanning', NULL, 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'storing_items_letters', '4', 'storing items (letters)', 'weeks', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'storing_items_packages', '1', 'storing items (packages)', 'week', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'storing_items_digitally', '1', 'storing items digitally', 'year', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'trashing_items', '-1', 'trashing items', 'unlimited', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'cloud_service_connection', 'Yes', 'cloud service connection', 'included', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'additional_incomming_items', '0.2', 'additional incomming items', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'envelop_scanning', '0.05', 'envelop scanning', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'opening_scanning', '0.4', 'opening and scanning', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'send_out_directly', '0.4', 'send out to original address directly', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'send_out_collected', '0.8', 'send out to original address collected', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'storing_items_over_free_letter', '0.03', 'storing items over free period (letters)', 'EUR/day', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'storing_items_over_free_packages', '0.10', 'storing items over free period (packages)', 'EUR/day', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'additional_private_mailbox', '4.95', 'additional private mailbox', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'additional_business_mailbox', '9.95', 'additional business mailbox ', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'postbox_fee', '0', 'fee for first postbox', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'postbox_fee', '4.95', 'postbox fee for first', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'postbox_fee', '9.95', 'Postbox fee for first', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'additional_pages_scanning_price', '0.084', 'Additional pages scanning / 1 page', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'additional_pages_scanning_price', '0.084', 'Additional pages scanning / 1 page', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'additional_pages_scanning_price', '0.084', 'Additional pages scanning / 1 page', 'EUR', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'include_pages_scanning_number', '10', 'Include pages scanning number', 'numbers', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'include_pages_scanning_number', '10', 'Include pages scanning number', 'numbers', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'include_pages_scanning_number', '10', 'Include pages scanning number', 'numbers', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 1, 'shipping_plus', '20', 'shipping plus', '%', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 2, 'shipping_plus', '20', 'shipping plus', '%', 1);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`) VALUES ( 3, 'shipping_plus', '20', 'shipping plus', '%', 1);









/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : clevvermail_supper
Target Host     : localhost:3306
Target Database : clevvermail_supper
Date: 2014-11-15 05:47:28
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for instance_amazon
-- ----------------------------
DROP TABLE IF EXISTS `instance_amazon`;
CREATE TABLE `instance_amazon` (
  `instance_id` bigint(20) NOT NULL DEFAULT '0',
  `s3_name` varchar(255) DEFAULT NULL,
  `s3_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`instance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of instance_amazon
-- ----------------------------
INSERT INTO `instance_amazon` VALUES ('4', 'DEV01', '0');
INSERT INTO `instance_amazon` VALUES ('5', 'DEV02', '0');

-- ----------------------------
-- Table structure for instance_database
-- ----------------------------
DROP TABLE IF EXISTS `instance_database`;
CREATE TABLE `instance_database` (
  `instance_id` bigint(20) NOT NULL,
  `database_name` varchar(255) DEFAULT NULL,
  `database_type` varchar(255) DEFAULT NULL,
  `host_address` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `last_updated_date` bigint(20) DEFAULT NULL,
  `last_updated_by` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`instance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of instance_database
-- ----------------------------
INSERT INTO `instance_database` VALUES ('4', 'clevvermail01', '0', 'localhost', 'root', '', '1415642876', '1', '1415654874', '1');
INSERT INTO `instance_database` VALUES ('5', 'clevvermail02', '0', 'localhost', 'root', '', '1415642913', '1', null, null);

-- ----------------------------
-- Table structure for instance_domain
-- ----------------------------
DROP TABLE IF EXISTS `instance_domain`;
CREATE TABLE `instance_domain` (
  `instance_id` bigint(20) NOT NULL DEFAULT '0',
  `domain_name` varchar(255) DEFAULT NULL,
  `full_url` varchar(255) DEFAULT NULL,
  `domain_type` tinyint(4) DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `last_updated_date` bigint(20) DEFAULT NULL,
  `last_updated_by` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`instance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of instance_domain
-- ----------------------------
INSERT INTO `instance_domain` VALUES ('1', null, null, null, null, null, null, null);
INSERT INTO `instance_domain` VALUES ('4', 'devlocal01.clevvermail.com', 'http://devlocal01.clevvermail.com', '0', '1415642876', '1', '1415654874', '1');
INSERT INTO `instance_domain` VALUES ('5', 'devlocal02.clevvermail.com', 'http://devlocal02.clevvermail.com', '0', '1415642913', '1', null, null);

-- ----------------------------
-- Table structure for instances
-- ----------------------------
DROP TABLE IF EXISTS `instances`;
CREATE TABLE `instances` (
  `instance_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `instance_code` varchar(20) DEFAULT NULL,
  `name` varchar(250) DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  `updated_date` bigint(20) DEFAULT NULL,
  `activated_flag` tinyint(4) DEFAULT NULL,
  `activated_date` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`instance_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of instances
-- ----------------------------
INSERT INTO `instances` VALUES ('4', '04', 'Instance DEV01', '1415642876', '1415654873', '1', '1415642876');
INSERT INTO `instances` VALUES ('5', '05', 'Instance DEV02', '1415642913', '1415642913', '1', '1415642913');

-- ----------------------------
-- Table structure for supper_admin
-- ----------------------------
DROP TABLE IF EXISTS `supper_admin`;
CREATE TABLE `supper_admin` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  `updated_date` bigint(20) DEFAULT NULL,
  `last_login_date` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of supper_admin
-- ----------------------------

