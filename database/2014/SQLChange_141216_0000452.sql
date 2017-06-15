-- Update naming for group.
UPDATE `groups` SET `description`='Super Admin' WHERE  `id`=1;
UPDATE `groups` SET `description`='Worker' WHERE  `id`=2;
UPDATE `groups` SET `description`='Instance Admin' WHERE  `id`=3;
UPDATE `groups` SET `description`='Location Admin' WHERE  `id`=4;


-- History log table.
CREATE TABLE `customer_cloud_history` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`customer_id` BIGINT(20) NULL DEFAULT NULL,
	`cloud_id` VARCHAR(3) NULL DEFAULT NULL,
	`auto_save_flag` TINYINT(4) NULL DEFAULT NULL COMMENT '0: not synchronized | 1:synchronized',
	`settings` VARCHAR(500) NULL DEFAULT NULL,
	`modified_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `id` (`id`)
)ENGINE=InnoDB COLLATE='utf8_general_ci'
;


-- Data exporting was unselected.


-- Dumping structure for table virtualpost.envelopes
CREATE TABLE IF NOT EXISTS `envelopes_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `envelope_id` bigint(20) unsigned NOT NULL,
  `envelope_code` varchar(40) DEFAULT NULL,
  `from_customer_name` varchar(255) DEFAULT NULL,
  `to_customer_id` bigint(255) DEFAULT NULL,
  `postbox_id` bigint(20) DEFAULT NULL,
  `envelope_type_id` varchar(11) DEFAULT NULL,
  `weight` decimal(15,3) DEFAULT NULL,
  `weight_unit` varchar(3) DEFAULT NULL,
  `completed_by` bigint(20) DEFAULT NULL,
  `completed_date` bigint(20) DEFAULT NULL,
  `incomming_date` bigint(20) DEFAULT NULL,
  `incomming_date_only` varchar(8) DEFAULT NULL,
  `last_updated_date` bigint(20) DEFAULT NULL,
  `category_type` varchar(10) DEFAULT NULL,
  `invoice_flag` tinyint(4) DEFAULT NULL,
  `shipping_type` tinyint(4) DEFAULT NULL,
  `include_estamp_flag` tinyint(4) DEFAULT NULL,
  `sync_cloud_flag` tinyint(4) DEFAULT '0' COMMENT '0: Not sync cloud | 1: Already cloud',
  `sync_cloud_date` bigint(20) DEFAULT NULL,
  `envelope_scan_flag` tinyint(4) DEFAULT NULL COMMENT '0: Request scan | 1: Scan completed',
  `item_scan_flag` tinyint(4) DEFAULT NULL COMMENT '0: Request item scan | 1: Item scan completed',
  `item_scan_date` bigint(20) DEFAULT NULL,
  `direct_shipping_flag` tinyint(4) DEFAULT NULL,
  `direct_shipping_date` bigint(20) DEFAULT NULL,
  `collect_shipping_flag` tinyint(4) DEFAULT NULL,
  `collect_shipping_date` bigint(20) DEFAULT NULL,
  `trash_flag` tinyint(4) DEFAULT NULL,
  `trash_date` bigint(8) DEFAULT NULL,
  `storage_flag` tinyint(4) DEFAULT NULL,
  `storage_date` bigint(20) DEFAULT NULL,
  `completed_flag` tinyint(4) DEFAULT NULL COMMENT '0: New | 1: Completed',
  `email_notification_flag` tinyint(4) DEFAULT '0' COMMENT '0: Not send email | 1: Already send email',
  `package_id` bigint(20) DEFAULT NULL,
  `shipping_id` bigint(20) DEFAULT NULL,
  `new_notification_flag` tinyint(4) DEFAULT '0',
  `incomming_letter_flag` tinyint(4) DEFAULT '0',
  `location_id` int(10) unsigned DEFAULT NULL,
  `modified_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table virtualpost.envelopes_completed
CREATE TABLE IF NOT EXISTS `envelopes_completed_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `envelopes_completed_id` bigint(20) unsigned NOT NULL,
  `envelope_id` bigint(20) DEFAULT NULL,
  `from_customer_name` varchar(255) DEFAULT NULL,
  `to_customer_id` bigint(255) DEFAULT NULL,
  `activity_id` tinyint(4) DEFAULT NULL,
  `activity_name` varchar(255) DEFAULT NULL,
  `postbox_id` bigint(20) DEFAULT NULL,
  `envelope_type_id` varchar(11) DEFAULT NULL,
  `weight` decimal(15,3) DEFAULT NULL,
  `weight_unit` varchar(3) DEFAULT NULL,
  `last_updated_date` int(11) DEFAULT NULL,
  `completed_by` bigint(20) DEFAULT NULL,
  `completed_date` bigint(20) DEFAULT NULL,
  `incomming_date` bigint(20) DEFAULT NULL,
  `category_type` varchar(10) DEFAULT NULL,
  `invoice_flag` tinyint(4) DEFAULT NULL,
  `shipping_type` tinyint(4) DEFAULT NULL,
  `include_estamp_flag` tinyint(4) DEFAULT NULL,
  `sync_cloud_flag` tinyint(4) DEFAULT '0' COMMENT '0: Not sync cloud | 1: Already cloud',
  `envelope_scan_flag` tinyint(4) DEFAULT NULL COMMENT '0: Request scan | 1: Scan completed',
  `item_scan_flag` tinyint(4) DEFAULT NULL COMMENT '0: Request item scan | 1: Item scan completed',
  `direct_shipping_flag` tinyint(4) DEFAULT NULL,
  `collect_shipping_flag` tinyint(4) DEFAULT NULL,
  `trash_flag` tinyint(4) DEFAULT NULL,
  `storage_flag` tinyint(4) DEFAULT NULL,
  `completed_flag` tinyint(4) DEFAULT NULL COMMENT '0: New | 1: Completed',
  `email_notification_flag` tinyint(4) DEFAULT '0' COMMENT '0: Not send email | 1: Already send email',
  `activity_code` varchar(250) DEFAULT NULL,
  `location_id` int(10) unsigned DEFAULT NULL,
  `modified_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table virtualpost.envelope_files

CREATE TABLE IF NOT EXISTS `envelope_files_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `envelope_file_id` bigint(20) NOT NULL,
  `envelope_id` bigint(20) DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `file_name` varchar(500) DEFAULT NULL,
  `public_file_name` varchar(500) DEFAULT NULL,
  `local_file_name` varchar(500) DEFAULT NULL,
  `amazon_path` varchar(500) DEFAULT NULL,
  `amazon_relate_path` varchar(500) DEFAULT NULL,
  `file_size` double DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL COMMENT '1: Envelope | 2: Document',
  `updated_date` bigint(20) DEFAULT NULL,
  `number_page` bigint(20) DEFAULT NULL,
  `modified_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table virtualpost.envelope_package
CREATE TABLE IF NOT EXISTS `envelope_package_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `package_id` bigint(20) NOT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `location_available_id` bigint(20) DEFAULT NULL,
  `package_date` varchar(8) CHARACTER SET utf8 DEFAULT NULL,
  `package_price` double(20,0) DEFAULT NULL,
  `modified_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.


-- Dumping structure for table virtualpost.envelope_shipping
CREATE TABLE IF NOT EXISTS `envelope_shipping_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `envelope_shipping_id` bigint(20) NOT NULL,
  `envelope_id` bigint(20) NOT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `postbox_id` bigint(20) DEFAULT NULL,
  `shipping_name` varchar(255) DEFAULT NULL,
  `shipping_company` varchar(255) DEFAULT NULL,
  `shipping_street` varchar(255) DEFAULT NULL,
  `shipping_postcode` varchar(255) DEFAULT NULL,
  `shipping_city` varchar(255) DEFAULT NULL,
  `shipping_region` varchar(255) DEFAULT NULL,
  `shipping_country` varchar(255) DEFAULT NULL,
  `estamp_url` varchar(255) DEFAULT NULL,
  `lable_size_id` int(11) DEFAULT NULL,
  `package_letter_size` varchar(255) DEFAULT NULL,
  `package_letter_size_id` int(11) DEFAULT NULL,
  `printer_id` int(11) DEFAULT NULL,
  `shipping_type_id` int(11) DEFAULT NULL,
  `shipping_date` bigint(20) DEFAULT NULL,
  `shipping_fee` decimal(10,2) DEFAULT NULL,
  `modified_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table virtualpost.postbox
drop table if exists postbox_history;
CREATE TABLE IF NOT EXISTS `postbox_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `postbox_id` bigint(20) NOT NULL,
  `postbox_code` varchar(20) DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `customer_code` varchar(20) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `invoice_name` varchar(120) DEFAULT NULL,
  `invoice_company` varchar(120) DEFAULT NULL,
  `postbox_name` varchar(255) DEFAULT NULL,
  `location_available_id` bigint(20) DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT '0',
  `is_main_postbox` tinyint(4) DEFAULT NULL,
  `plan_deleted_date` varchar(8) DEFAULT NULL,
  `updated_date` int(11) DEFAULT NULL,
  `apply_date` varchar(8) DEFAULT NULL,
  `new_postbox_type` smallint(6) DEFAULT NULL,
  `plan_date_change_postbox_type` varchar(8) DEFAULT NULL,
  `first_location_flag` tinyint(4) DEFAULT NULL,
  `modified_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

