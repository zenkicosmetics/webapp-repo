
-- Dumping structure for table clevvermail_webapp_phonenumber.phone_area_code
DROP TABLE IF EXISTS `phone_area_code`;
CREATE TABLE IF NOT EXISTS `phone_area_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) DEFAULT NULL,
  `area_code` varchar(20) DEFAULT NULL,
  `area_name` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=363 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table clevvermail_webapp_phonenumber.phone_call_history
DROP TABLE IF EXISTS `phone_call_history`;
CREATE TABLE IF NOT EXISTS `phone_call_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `record_id` bigint(20) NOT NULL DEFAULT '0',
  `customer_id` bigint(20) DEFAULT NULL,
  `parent_customer_id` bigint(20) DEFAULT NULL,
  `phone_user_id` int(11) DEFAULT NULL,
  `phone_number` varchar(30) DEFAULT NULL,
  `activity` varchar(250) DEFAULT NULL,
  `target_phone_number` varchar(30) DEFAULT NULL,
  `system_response` varchar(250) DEFAULT NULL,
  `call_start_time` int(11) DEFAULT NULL,
  `call_status` int(11) DEFAULT NULL COMMENT '1: received| 0: no anwser.',
  `duration` varchar(17) DEFAULT '0',
  `cost` decimal(18,2) DEFAULT NULL,
  `file_url` varchar(250) DEFAULT NULL,
  `created_date` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `record_id` (`record_id`,`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table clevvermail_webapp_phonenumber.phone_customer_subaccount
DROP TABLE IF EXISTS `phone_customer_subaccount`;
CREATE TABLE IF NOT EXISTS `phone_customer_subaccount` (
  `customer_id` bigint(20) NOT NULL DEFAULT '0',
  `account_id` bigint(20) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `user_fname` varchar(30) NOT NULL,
  `password` varchar(250) NOT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  `updated_date` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table clevvermail_webapp_phonenumber.phone_customer_users
DROP TABLE IF EXISTS `phone_customer_users`;
CREATE TABLE IF NOT EXISTS `phone_customer_users` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parent_customer_id` bigint(20) NOT NULL,
  `customer_id` bigint(20) NOT NULL,
  `phone_user_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `phone_customer_users_id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table clevvermail_webapp_phonenumber.phone_invoice_by_location
DROP TABLE IF EXISTS `phone_invoice_by_location`;
CREATE TABLE IF NOT EXISTS `phone_invoice_by_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_code` varchar(50) DEFAULT NULL,
  `invoice_month` varchar(8) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `parent_customer_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `incomming_quantity` int(11) DEFAULT NULL,
  `incomming_amount` double DEFAULT NULL,
  `outcomming_quantity` int(11) DEFAULT NULL,
  `outcomming_amount` double DEFAULT NULL,
  `phone_subscription_quantity` int(11) DEFAULT NULL,
  `phone_subscription_amount` double DEFAULT NULL,
  `vat` double DEFAULT NULL,
  `vat_case` int(11) DEFAULT NULL,
  `total_invoice` double DEFAULT NULL,
  `rev_share` double DEFAULT NULL,
  `created_date` int(11) DEFAULT NULL,
  `modified_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `phone_recurring_quantity` int(11) DEFAULT NULL,
  `phone_recurring_amount` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table clevvermail_webapp_phonenumber.phone_invoice_detail
DROP TABLE IF EXISTS `phone_invoice_detail`;
CREATE TABLE IF NOT EXISTS `phone_invoice_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `invoice_summary_id` int(11) DEFAULT NULL,
  `parent_customer_id` int(11) DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `activity` varchar(255) DEFAULT NULL,
  `activity_date` varchar(8) DEFAULT NULL,
  `item_number` int(11) DEFAULT NULL,
  `item_amount` double DEFAULT NULL,
  `create_date` int(11) DEFAULT NULL,
  `modified_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `activity_type` tinyint(4) DEFAULT NULL,
  `phone_number` varchar(30) DEFAULT NULL,
  `reference` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=952 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table clevvermail_webapp_phonenumber.phone_number
DROP TABLE IF EXISTS `phone_number`;
CREATE TABLE IF NOT EXISTS `phone_number` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `phone_code` varchar(15) DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `parent_customer_id` bigint(20) DEFAULT NULL,
  `phone_number` varchar(40) DEFAULT NULL,
  `target_id` varchar(20) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '0: disabled| 1: enabled',
  `location_id` tinyint(4) DEFAULT '1',
  `country_code` varchar(3) DEFAULT NULL,
  `area_code` varchar(10) DEFAULT NULL,
  `decsription` varchar(250) DEFAULT '1',
  `created_date` int(11) DEFAULT '1',
  `modified_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `end_contract_date` int(11) DEFAULT NULL,
  `plan_delete_date` int(11) DEFAULT NULL,
  `auto_renewal` tinyint(4) DEFAULT '1',
  `is_verification_flag` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table clevvermail_webapp_phonenumber.phone_phones
DROP TABLE IF EXISTS `phone_phones`;
CREATE TABLE IF NOT EXISTS `phone_phones` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `phone_id` bigint(20) DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `parent_customer_id` bigint(20) DEFAULT NULL,
  `phone_name` varchar(250) DEFAULT NULL,
  `phone_type` varchar(10) DEFAULT NULL COMMENT 'regular|IP',
  `phone_number` varchar(40) DEFAULT NULL COMMENT '(phone_number) if phone type is regular',
  `created_date` bigint(20) DEFAULT NULL,
  `modified_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `target_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table clevvermail_webapp_phonenumber.phone_settings
DROP TABLE IF EXISTS `phone_settings`;
CREATE TABLE IF NOT EXISTS `phone_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_customer_id` int(11) DEFAULT NULL,
  `notify_flag` tinyint(4) DEFAULT NULL,
  `max_daily_usage` decimal(10,2) DEFAULT NULL,
  `created_date` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table clevvermail_webapp_phonenumber.phone_target
DROP TABLE IF EXISTS `phone_target`;
CREATE TABLE IF NOT EXISTS `phone_target` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parent_customer_id` bigint(20) NOT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `target_name` varchar(250) NOT NULL,
  `target_id` varchar(100) NOT NULL,
  `target_type` varchar(250) NOT NULL,
  `use_flag` tinyint(20) NOT NULL DEFAULT '1',
  `created_date` int(11) DEFAULT '1',
  `updated_date` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table clevvermail_webapp_phonenumber.phone_user_number
DROP TABLE IF EXISTS `phone_user_number`;
CREATE TABLE IF NOT EXISTS `phone_user_number` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `phone_code` varchar(15) DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `parent_customer_id` bigint(20) DEFAULT NULL,
  `phone_user_id` int(11) DEFAULT NULL,
  `phone_number` varchar(40) DEFAULT NULL,
  `endpoint` varchar(100) DEFAULT 'postbox',
  `endpoint_type` varchar(20) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '1' COMMENT '0: disabled| 1: enabled',
  `location_id` tinyint(4) DEFAULT '1',
  `country_code` varchar(3) DEFAULT NULL,
  `area_code` varchar(10) DEFAULT NULL,
  `decsription` varchar(250) DEFAULT '1',
  `created_date` int(11) DEFAULT '1',
  `modified_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_verification_flag` tinyint(4) DEFAULT '0' COMMENT '0: need verification|1:completed verfication',
  `end_contract_date` int(11) DEFAULT NULL,
  `plan_delete_date` int(11) DEFAULT NULL,
  `auto_renewal` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table clevvermail_webapp_phonenumber.phone_voiceapp
DROP TABLE IF EXISTS `phone_voiceapp`;
CREATE TABLE IF NOT EXISTS `phone_voiceapp` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `parent_customer_id` bigint(20) DEFAULT NULL,
  `sub_account_id` bigint(11) DEFAULT NULL,
  `app_id` varchar(20) DEFAULT NULL,
  `app_type` varchar(10) DEFAULT NULL,
  `name` varchar(250) DEFAULT '1' COMMENT '0: disabled| 1: enabled',
  `use_flag` tinyint(20) DEFAULT '1',
  `data_setting` text,
  `created_date` int(11) DEFAULT '1',
  `updated_date` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table clevvermail_webapp_phonenumber.pricing_phones_number
DROP TABLE IF EXISTS `pricing_phones_number`;
CREATE TABLE IF NOT EXISTS `pricing_phones_number` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_code_3` varchar(3) NOT NULL,
  `currency` varchar(3) NOT NULL,
  `range` int(11) NOT NULL DEFAULT '1',
  `type` varchar(20) NOT NULL,
  `price_category` varchar(20) NOT NULL,
  `one_time_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `one_time_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `recurring_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `recurring_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_call_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_call_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_min_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_min_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_sms_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_sms_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `charge_interval` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_fax_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_fax_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `recurrence_interval` varchar(10) DEFAULT NULL,
  `remarks` varchar(4000) DEFAULT NULL,
  `created_date` bigint(20) NOT NULL,
  `last_modified_date` bigint(20) DEFAULT NULL,
  `last_sync_date` bigint(20) DEFAULT NULL,
  `is_latest_fee` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table clevvermail_webapp_phonenumber.pricing_phones_number_customer
DROP TABLE IF EXISTS `pricing_phones_number_customer`;
CREATE TABLE IF NOT EXISTS `pricing_phones_number_customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) NOT NULL,
  `country_code_3` varchar(3) NOT NULL,
  `range` int(11) NOT NULL DEFAULT '1',
  `currency` varchar(3) NOT NULL,
  `type` varchar(20) NOT NULL,
  `price_category` varchar(20) NOT NULL,
  `one_time_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `one_time_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `recurring_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `recurring_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_call_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_call_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_min_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_min_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_sms_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_sms_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `charge_interval` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_fax_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_fax_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `recurrence_interval` varchar(10) DEFAULT NULL,
  `remarks` varchar(4000) DEFAULT NULL,
  `created_date` bigint(20) NOT NULL,
  `last_modified_date` bigint(20) DEFAULT NULL,
  `last_sync_date` bigint(20) DEFAULT NULL,
  `is_latest_fee` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=233 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table clevvermail_webapp_phonenumber.pricing_phones_number_latest
DROP TABLE IF EXISTS `pricing_phones_number_latest`;
CREATE TABLE IF NOT EXISTS `pricing_phones_number_latest` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_code_3` varchar(3) NOT NULL,
  `range` int(11) NOT NULL DEFAULT '1',
  `currency` varchar(3) NOT NULL,
  `type` varchar(20) NOT NULL,
  `price_category` varchar(20) NOT NULL,
  `one_time_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `one_time_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `recurring_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `recurring_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_call_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_call_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_min_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_min_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_sms_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_sms_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `charge_interval` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_fax_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_fax_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `recurrence_interval` varchar(10) DEFAULT NULL,
  `remarks` varchar(4000) DEFAULT NULL,
  `created_date` bigint(20) NOT NULL,
  `last_modified_date` bigint(20) DEFAULT NULL,
  `last_sync_date` bigint(20) DEFAULT NULL,
  `is_latest_fee` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=409 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table clevvermail_webapp_phonenumber.pricing_phones_outboundcalls
DROP TABLE IF EXISTS `pricing_phones_outboundcalls`;
CREATE TABLE IF NOT EXISTS `pricing_phones_outboundcalls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_code_3` varchar(3) DEFAULT NULL,
  `pricing_name` varchar(255) DEFAULT NULL,
  `currency` varchar(3) NOT NULL,
  `per_call_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_call_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `usage_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `usage_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `charge_interval` decimal(18,3) NOT NULL,
  `price_plan` varchar(20) NOT NULL,
  `dialcode_list` varchar(4000) DEFAULT NULL,
  `remarks` varchar(4000) DEFAULT NULL,
  `created_date` bigint(20) NOT NULL,
  `last_modified_date` bigint(20) DEFAULT NULL,
  `last_sync_date` bigint(20) DEFAULT NULL,
  `is_latest_fee` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=472 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table clevvermail_webapp_phonenumber.pricing_phones_outboundcalls_customer
DROP TABLE IF EXISTS `pricing_phones_outboundcalls_customer`;
CREATE TABLE IF NOT EXISTS `pricing_phones_outboundcalls_customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) NOT NULL,
  `country_code_3` varchar(3) DEFAULT NULL,
  `pricing_name` varchar(255) DEFAULT NULL,
  `currency` varchar(3) NOT NULL,
  `per_call_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_call_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `usage_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `usage_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `charge_interval` decimal(18,3) NOT NULL,
  `price_plan` varchar(20) NOT NULL,
  `dialcode_list` varchar(4000) DEFAULT NULL,
  `remarks` varchar(4000) DEFAULT NULL,
  `created_date` bigint(20) NOT NULL,
  `last_modified_date` bigint(20) DEFAULT NULL,
  `last_sync_date` bigint(20) DEFAULT NULL,
  `is_latest_fee` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1885 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table clevvermail_webapp_phonenumber.pricing_phones_outboundcalls_latest
DROP TABLE IF EXISTS `pricing_phones_outboundcalls_latest`;
CREATE TABLE IF NOT EXISTS `pricing_phones_outboundcalls_latest` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_code_3` varchar(3) DEFAULT NULL,
  `pricing_name` varchar(255) DEFAULT NULL,
  `currency` varchar(3) NOT NULL,
  `per_call_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `per_call_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `usage_fee` decimal(18,3) NOT NULL DEFAULT '0.000',
  `usage_fee_upcharge` decimal(18,3) NOT NULL DEFAULT '0.000',
  `charge_interval` decimal(18,3) NOT NULL,
  `price_plan` varchar(20) NOT NULL,
  `dialcode_list` varchar(4000) DEFAULT NULL,
  `remarks` varchar(4000) DEFAULT NULL,
  `created_date` bigint(20) NOT NULL,
  `last_modified_date` bigint(20) DEFAULT NULL,
  `last_sync_date` bigint(20) DEFAULT NULL,
  `is_latest_fee` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=943 DEFAULT CHARSET=utf8;



















