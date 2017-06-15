/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : virtualpost
Target Host     : localhost:3306
Target Database : virtualpost
Date: 2013-06-16 19:08:57
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for ci_sessions
-- ----------------------------
DROP TABLE IF EXISTS `ci_sessions`;
CREATE TABLE `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` bigint(20) NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of ci_sessions
-- ----------------------------
INSERT INTO `ci_sessions` VALUES ('19b3bb3e13dd4bbc461c7ed8352719ad', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:21.0) Gecko/20100101 Firefox/21.0', '1371384436', 'a:9:{s:9:\"user_data\";s:0:\"\";s:8:\"username\";s:5:\"admin\";s:5:\"email\";s:15:\"admin@admin.com\";s:2:\"id\";s:1:\"1\";s:7:\"user_id\";s:1:\"1\";s:8:\"group_id\";s:1:\"1\";s:5:\"group\";s:5:\"admin\";s:21:\"SESSION_USERADMIN_KEY\";O:8:\"stdClass\":19:{s:2:\"id\";s:1:\"1\";s:10:\"ip_address\";s:9:\"127.0.0.1\";s:8:\"username\";s:5:\"admin\";s:12:\"display_name\";s:17:\"Nguyen Trong Dung\";s:8:\"password\";s:40:\"e7e793ccb91033a84efea89476e8475cee644fb8\";s:4:\"salt\";s:10:\"9462e8eee0\";s:5:\"email\";s:15:\"admin@admin.com\";s:15:\"activation_code\";s:4:\"NULL\";s:23:\"forgotten_password_code\";s:10:\"1268889823\";s:23:\"forgotten_password_time\";N;s:13:\"remember_code\";s:10:\"1268889823\";s:10:\"created_on\";s:10:\"1268889823\";s:10:\"last_login\";s:10:\"1371379302\";s:6:\"active\";s:1:\"1\";s:10:\"first_name\";s:6:\"Nguyen\";s:9:\"last_name\";s:4:\"Dung\";s:7:\"company\";s:2:\"FF\";s:5:\"phone\";s:10:\"1112223333\";s:8:\"group_id\";s:1:\"1\";}s:20:\"SESSION_CUSTOMER_KEY\";O:8:\"stdClass\":16:{s:11:\"customer_id\";s:1:\"1\";s:9:\"user_name\";s:24:\"customer01@localhost.com\";s:5:\"email\";s:24:\"customer01@localhost.com\";s:8:\"password\";s:6:\"123456\";s:13:\"envelope_scan\";s:1:\"1\";s:5:\"scans\";s:1:\"1\";s:8:\"shipment\";s:1:\"1\";s:19:\"number_of_postboxes\";N;s:20:\"always_scan_envelope\";N;s:21:\"always_scan_incomming\";N;s:18:\"email_notification\";N;s:15:\"invoicing_cycle\";N;s:18:\"collect_mail_cycle\";N;s:16:\"weekday_shipping\";N;s:16:\"last_access_date\";s:10:\"1371345757\";s:9:\"token_key\";s:32:\"5ad07c37b4676576fedb738fddb2d48d\";}}');

-- ----------------------------
-- Table structure for city
-- ----------------------------
DROP TABLE IF EXISTS `city`;
CREATE TABLE `city` (
  `city_id` int(11) NOT NULL AUTO_INCREMENT,
  `city` varchar(30) DEFAULT NULL,
  `state_code` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`city_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of city
-- ----------------------------

-- ----------------------------
-- Table structure for cloud
-- ----------------------------
DROP TABLE IF EXISTS `cloud`;
CREATE TABLE `cloud` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `cloud_id` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL COMMENT '0: not synchronized | 1:synchronized',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cloud
-- ----------------------------

-- ----------------------------
-- Table structure for country
-- ----------------------------
DROP TABLE IF EXISTS `country`;
CREATE TABLE `country` (
  `country_code` int(11) NOT NULL,
  `country_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`country_code`),
  UNIQUE KEY `country_code` (`country_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of country
-- ----------------------------

-- ----------------------------
-- Table structure for customers
-- ----------------------------
DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `customer_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `envelope_scan` smallint(255) DEFAULT NULL,
  `scans` smallint(255) DEFAULT NULL,
  `shipment` smallint(255) DEFAULT NULL,
  `number_of_postboxes` int(11) DEFAULT NULL,
  `always_scan_envelope` tinyint(4) DEFAULT NULL COMMENT '0: not always| 1:alway scan',
  `always_scan_incomming` tinyint(4) DEFAULT NULL COMMENT '0: not always| 1:always scan',
  `email_notification` smallint(6) DEFAULT NULL,
  `invoicing_cycle` smallint(6) DEFAULT NULL,
  `collect_mail_cycle` smallint(6) DEFAULT NULL,
  `weekday_shipping` smallint(6) DEFAULT NULL,
  `last_access_date` int(11) DEFAULT NULL,
  `token_key` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `user_id` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of customers
-- ----------------------------
INSERT INTO `customers` VALUES ('1', 'customer01@localhost.com', 'customer01@localhost.com', '123456', '1', '1', '1', null, null, null, null, null, null, null, '1371384148', '5ad07c37b4676576fedb738fddb2d48d');
INSERT INTO `customers` VALUES ('3', 'customer03@localhost.com', 'customer03@localhost.com', '123456', null, null, null, null, null, null, null, null, null, null, '1371293625', null);

-- ----------------------------
-- Table structure for customers_address
-- ----------------------------
DROP TABLE IF EXISTS `customers_address`;
CREATE TABLE `customers_address` (
  `customer_id` bigint(20) NOT NULL,
  `shipment_address_name` varchar(255) NOT NULL,
  `shipment_company` varchar(120) DEFAULT NULL,
  `shipment_street` varchar(255) DEFAULT NULL,
  `shipment_postcode` varchar(20) DEFAULT NULL,
  `shipment_city` varchar(60) DEFAULT NULL,
  `shipment_region` varchar(255) DEFAULT NULL,
  `shipment_country` varchar(120) DEFAULT NULL,
  `invoicing_address_name` varchar(255) DEFAULT NULL,
  `invoicing_company` varchar(120) DEFAULT NULL,
  `invoicing_street` varchar(255) DEFAULT NULL,
  `invoicing_postcode` varchar(20) DEFAULT NULL,
  `invoicing_city` varchar(60) DEFAULT NULL,
  `invoicing_region` varchar(255) DEFAULT NULL,
  `invoicing_country` varchar(120) DEFAULT NULL,
  `is_bussiness` tinyint(4) DEFAULT NULL COMMENT '0:is not bussiness| 1:is bussiness',
  `vat_number` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `user_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of customers_address
-- ----------------------------
INSERT INTO `customers_address` VALUES ('1', '111111', '1111111111111111', '11111111111111111', '111111111', '11111111111', '111111111111', '1111111111111111', '1111111111', '11111111111', '111111111', '111111111111', '111111111', '11111111111111111', '1111111111111', null, null);

-- ----------------------------
-- Table structure for customers_invoices
-- ----------------------------
DROP TABLE IF EXISTS `customers_invoices`;
CREATE TABLE `customers_invoices` (
  `customer_id` bigint(20) NOT NULL,
  `envelope_scan` varchar(255) DEFAULT NULL,
  `scans` varchar(255) DEFAULT NULL,
  `shipment` varchar(255) DEFAULT NULL,
  `number_of_postboxes` int(11) DEFAULT NULL,
  `always_scan_envelope` tinyint(4) DEFAULT NULL COMMENT '0: not always| 1:alway scan',
  `always_scan_incomming` tinyint(4) DEFAULT NULL COMMENT '0: not always| 1:always scan',
  `email_notification` smallint(6) DEFAULT NULL,
  `invoicing_cycle` smallint(6) DEFAULT NULL,
  `collect_mail_cycle` smallint(6) DEFAULT NULL,
  `weekday_shipping` smallint(6) DEFAULT NULL,
  `shipment_address_name` varchar(255) NOT NULL,
  `shipment_company` varchar(120) DEFAULT NULL,
  `shipment_street` varchar(255) DEFAULT NULL,
  `shipment_postcode` varchar(20) DEFAULT NULL,
  `shipment_city` varchar(60) DEFAULT NULL,
  `shipment_region` varchar(255) DEFAULT NULL,
  `shipment_country` varchar(120) DEFAULT NULL,
  `invoicing_address_name` varchar(255) DEFAULT NULL,
  `invoicing_company` varchar(120) DEFAULT NULL,
  `invoicing_street` varchar(255) DEFAULT NULL,
  `invoicing_postcode` varchar(20) DEFAULT NULL,
  `invoicing_city` varchar(60) DEFAULT NULL,
  `invoicing_region` varchar(255) DEFAULT NULL,
  `invoicing_country` varchar(120) DEFAULT NULL,
  `is_bussiness` tinyint(4) DEFAULT NULL COMMENT '0:is not bussiness| 1:is bussiness',
  `vat_number` varchar(30) DEFAULT NULL,
  `payment_method` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `user_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of customers_invoices
-- ----------------------------

-- ----------------------------
-- Table structure for customers_location_available
-- ----------------------------
DROP TABLE IF EXISTS `customers_location_available`;
CREATE TABLE `customers_location_available` (
  `location_available_id` bigint(20) NOT NULL DEFAULT '0',
  `customer_id` bigint(20) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `postcode` varchar(9) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`location_available_id`),
  UNIQUE KEY `user_id` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of customers_location_available
-- ----------------------------

-- ----------------------------
-- Table structure for envelope_files
-- ----------------------------
DROP TABLE IF EXISTS `envelope_files`;
CREATE TABLE `envelope_files` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `envelope_id` bigint(20) DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `file_name` varchar(500) DEFAULT NULL,
  `file_size` double DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  `updated_date` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of envelope_files
-- ----------------------------
INSERT INTO `envelope_files` VALUES ('1', '1', '1', 'uploads/E1_C1_F_WebTWAINImage.png', '50630', null, null);
INSERT INTO `envelope_files` VALUES ('2', '20', '1', 'uploads/E20_C1_F_FileScan_20', '50630', null, '1371351034');
INSERT INTO `envelope_files` VALUES ('3', '20', '1', 'uploads/E20_C1_F_FileScan_20', '50630', null, '1371351034');
INSERT INTO `envelope_files` VALUES ('4', '20', '1', 'uploads/E20_C1_F_FileScan_20', '50630', null, '1371351034');
INSERT INTO `envelope_files` VALUES ('5', '10', '1', 'uploads/E10_C1_F_FileScan_10', '50630', null, '1371352645');
INSERT INTO `envelope_files` VALUES ('6', '10', '1', 'uploads/E10_C1_F_FileScan_10', '50630', null, '1371352645');
INSERT INTO `envelope_files` VALUES ('7', '20', '1', 'uploads/E20_C1_F_FileScan_20', '50630', null, '1371351034');
INSERT INTO `envelope_files` VALUES ('8', '10', '1', 'uploads/E10_C1_F_FileScan_10', '50630', null, '1371352645');
INSERT INTO `envelope_files` VALUES ('9', '20', '1', 'uploads/E20_C1_F_FileScan_20', '50630', null, '1371351034');
INSERT INTO `envelope_files` VALUES ('10', '10', '1', 'uploads/E10_C1_F_FileScan_10', '50630', null, '1371352645');
INSERT INTO `envelope_files` VALUES ('11', '20', '1', 'uploads/E20_C1_F_FileScan_20', '50630', null, '1371351034');
INSERT INTO `envelope_files` VALUES ('12', '20', '1', 'uploads/E20_C1_F_FileScan_20', '50630', null, '1371351034');

-- ----------------------------
-- Table structure for envelopes
-- ----------------------------
DROP TABLE IF EXISTS `envelopes`;
CREATE TABLE `envelopes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `from_customer_name` varchar(255) DEFAULT NULL,
  `to_customer_id` bigint(255) DEFAULT NULL,
  `postbox_id` bigint(20) DEFAULT NULL,
  `envelope_type_id` varchar(11) DEFAULT NULL,
  `weight` decimal(15,3) DEFAULT NULL,
  `weight_unit` varchar(3) DEFAULT NULL,
  `sent_date` int(11) DEFAULT NULL,
  `activity` varchar(255) DEFAULT NULL,
  `completed_by` bigint(20) DEFAULT NULL,
  `completed_date` bigint(20) DEFAULT NULL,
  `incomming_date` bigint(20) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL COMMENT '0: Incomming | 1: todo',
  `category_type` varchar(10) DEFAULT NULL,
  `invoice_flag` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of envelopes
-- ----------------------------
INSERT INTO `envelopes` VALUES ('9', 'Nguyen Trong Dung', '1', '1', 'C5', '11.000', 'g', '1370440816', 'Incomming', null, null, null, '1', '003', '1');
INSERT INTO `envelopes` VALUES ('10', 'Nguyen Trong Dung', '1', '1', 'C5', '11.000', 'g', '1370440819', 'Collect Shipping', null, null, null, '2', '003', '1');
INSERT INTO `envelopes` VALUES ('11', 'Nguyen Trong Dung', '1', '2', 'C5', '11.000', 'g', '1370440820', 'Incomming', null, null, null, '3', '003', '1');
INSERT INTO `envelopes` VALUES ('12', 'Nguyen Trong Dung', '1', '2', 'C5', '11.000', 'g', '1370440821', 'Incomming', null, null, null, '4', '003', '1');
INSERT INTO `envelopes` VALUES ('14', 'Nguyen Trong Dung', '1', '1', 'C5', '11.000', 'g', '1370742288', 'Incomming', null, null, null, '1', '003', '0');
INSERT INTO `envelopes` VALUES ('15', 'Nguyen Trong Dung', '1', '1', 'C5', '11.000', 'g', '1370742299', 'Incomming', null, null, null, '1', '005', '0');
INSERT INTO `envelopes` VALUES ('17', 'Nguyen Trong Dung', '1', '2', 'C5', '11.000', 'g', '1370742316', 'Incomming', null, null, null, '1', '005', '1');
INSERT INTO `envelopes` VALUES ('18', 'Nguyen Trong Dung', '1', '2', 'C5', '12.000', 'g', '1370742322', 'Incomming', null, null, null, '1', '005', '1');
INSERT INTO `envelopes` VALUES ('19', 'Nguyen Trong Dung', '1', '2', 'C5', '33.000', 'g', '1370742324', 'Incomming', null, null, null, '1', '005', '1');
INSERT INTO `envelopes` VALUES ('20', 'Nguyen Trong Dung', '1', '2', 'C3', '33.000', 'g', '1370742327', 'Incomming', null, null, null, '2', '005', '1');
INSERT INTO `envelopes` VALUES ('21', 'Nguyen Trong Dung', '1', '2', 'C3', '44.000', 'g', '1370742334', 'Incomming', null, null, null, '3', '005', '1');
INSERT INTO `envelopes` VALUES ('22', 'Nguyen Trong Dung', '1', '2', 'C3', '44.000', 'g', '1370742337', 'Incomming', null, null, null, '3', '002', '1');
INSERT INTO `envelopes` VALUES ('23', 'Nguyen Trong Dung', '1', '2', 'C3', '44.000', 'g', '1370742344', 'Incomming', null, null, null, '2', '001', '1');
INSERT INTO `envelopes` VALUES ('24', 'Nguyen Trong Dung', '1', '2', 'C3', '44.000', 'g', '1370742347', 'Incomming', null, null, null, '3', '003', '1');
INSERT INTO `envelopes` VALUES ('25', 'Nguyen Trong Dung', '1', '2', 'C3', '44.000', 'g', '1370742348', 'Incomming', null, null, null, '3', '003', '1');
INSERT INTO `envelopes` VALUES ('26', 'Nguyen Trong Dung', '1', '2', 'C3', '44.000', 'g', '1370742350', 'Incomming', null, null, null, '1', '003', '1');
INSERT INTO `envelopes` VALUES ('27', 'Nguyen Trong Dung', '1', '2', 'C3', '44.000', 'g', '1370742354', 'Incomming', null, null, null, '2', '003', '1');
INSERT INTO `envelopes` VALUES ('28', 'Nguyen Trong Dung', '1', '2', 'C3', '44.000', 'g', '1370742355', 'Incomming', null, null, null, '1', '003', '1');
INSERT INTO `envelopes` VALUES ('29', 'Nguyen Trong Dung', '1', '2', 'C3', '44.000', 'g', '1370742356', 'Incomming', null, null, null, '2', '003', '1');
INSERT INTO `envelopes` VALUES ('30', 'Nguyen Trong Dung', '1', '2', 'C3', '44.000', 'g', '1370742357', 'Incomming', null, null, null, '1', '003', '1');
INSERT INTO `envelopes` VALUES ('31', 'Nguyen Trong Dung', '1', '2', 'C3', '44.000', 'g', '1370742358', 'Incomming', null, null, null, '2', '003', '1');
INSERT INTO `envelopes` VALUES ('32', 'Nguyen Trong Dung', '1', '2', 'C3', '44.000', 'g', '1370742359', 'Incomming', null, null, null, '1', '003', '1');
INSERT INTO `envelopes` VALUES ('35', 'Nguyen Trong Dung 3', '1', '2', 'C3', '44.000', 'g', '1370742369', 'Incomming', null, null, null, '1', '001', '1');
INSERT INTO `envelopes` VALUES ('36', 'Nguyen Trong Dung 4', '1', '2', 'C3', '44.000', 'g', '1370742372', 'Incomming', null, null, null, '1', '001', '1');
INSERT INTO `envelopes` VALUES ('37', 'Nguyen Trong Dung 5', '1', '2', 'C3', '44.000', 'g', '1370742378', 'Incomming', null, null, null, '1', '001', '1');
INSERT INTO `envelopes` VALUES ('39', 'Nguyen Trong Dung 1', '1', '2', 'C3', '22.000', 'g', '1370742384', 'Incomming', null, null, null, '7', '001', '1');
INSERT INTO `envelopes` VALUES ('40', 'Nguyen Trong Dung 1', '1', '2', 'C4', '3333.000', 'g', '1370742388', 'Incomming', null, null, null, '7', '001', '1');
INSERT INTO `envelopes` VALUES ('41', 'Nguyen Trong Dung', '1', '1', 'C5', '22.000', 'g', '1371296075', 'Incomming', null, null, null, '1', '003', '0');
INSERT INTO `envelopes` VALUES ('42', 'Nguyen Trong Dung', '1', '1', 'C5', '22.000', 'g', '1371296084', 'Incomming', null, null, null, '1', '003', '0');
INSERT INTO `envelopes` VALUES ('43', 'Nguyen Trong Dung', '1', '2', 'C5', '22.000', 'g', '1371296337', 'Incomming', null, null, null, '1', '003', '0');
INSERT INTO `envelopes` VALUES ('44', 'Nguyen Trong Dung', '1', '2', 'C5', '22.000', 'g', '1371296338', 'Incomming', null, null, null, '1', '003', '0');
INSERT INTO `envelopes` VALUES ('45', 'Nguyen Trong Dung', '1', '2', 'C5', '22.000', 'g', '1371296340', 'Incomming', null, null, null, '1', '003', '0');
INSERT INTO `envelopes` VALUES ('46', 'Nguyen Trong Dung', '1', '2', 'C5', '22.000', 'g', '1371296341', 'Incomming', null, null, null, '1', '003', '0');

-- ----------------------------
-- Table structure for files_scan
-- ----------------------------
DROP TABLE IF EXISTS `files_scan`;
CREATE TABLE `files_scan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_id` bigint(255) DEFAULT NULL,
  `scan_type_id` int(11) DEFAULT NULL,
  `weight` decimal(15,3) DEFAULT NULL,
  `weight_unit` varchar(3) DEFAULT NULL,
  `scanned_date` int(11) DEFAULT NULL,
  `activity` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of files_scan
-- ----------------------------

-- ----------------------------
-- Table structure for groups
-- ----------------------------
DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of groups
-- ----------------------------
INSERT INTO `groups` VALUES ('1', 'admin', 'administrator');
INSERT INTO `groups` VALUES ('2', 'worker', 'worker user');

-- ----------------------------
-- Table structure for location
-- ----------------------------
DROP TABLE IF EXISTS `location`;
CREATE TABLE `location` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `location_name` varchar(60) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `postcode` varchar(10) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of location
-- ----------------------------
INSERT INTO `location` VALUES ('1', 'Berlin 1', 'Musterstraße 13', '10025', 'Berlin', 'Berlin', 'Germany');

-- ----------------------------
-- Table structure for modules
-- ----------------------------
DROP TABLE IF EXISTS `modules`;
CREATE TABLE `modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `version` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `skip_xss` tinyint(1) NOT NULL,
  `is_frontend` tinyint(1) NOT NULL,
  `is_backend` tinyint(1) NOT NULL,
  `menu` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `installed` tinyint(1) NOT NULL,
  `is_core` tinyint(1) NOT NULL,
  `updated_on` int(11) NOT NULL DEFAULT '0',
  `modulename` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PACK_KEYS=0 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of modules
-- ----------------------------

-- ----------------------------
-- Table structure for payment
-- ----------------------------
DROP TABLE IF EXISTS `payment`;
CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(30) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `account` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`payment_id`),
  UNIQUE KEY `payment_id` (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of payment
-- ----------------------------

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` bigint(20) DEFAULT NULL,
  `module_name` varchar(100) DEFAULT NULL,
  `role` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of permissions
-- ----------------------------

-- ----------------------------
-- Table structure for postbox
-- ----------------------------
DROP TABLE IF EXISTS `postbox`;
CREATE TABLE `postbox` (
  `postbox_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) DEFAULT NULL,
  `location_available_id` bigint(20) DEFAULT NULL,
  `postbox_name` varchar(255) DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`postbox_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of postbox
-- ----------------------------
INSERT INTO `postbox` VALUES ('1', '1', '1', 'Cust 1 - Post box 1 ', null, 'Cust 1 ', 'USOL-V');
INSERT INTO `postbox` VALUES ('2', '1', '1', 'Cust 1 - Post box Q', null, 'Cust 1 ', 'USOL-V');
INSERT INTO `postbox` VALUES ('3', '3', '1', 'Default postbox name 1 - customer03@localhost.com', null, 'Default name 1 - customer03@localhost.com', 'Default company 1 - customer03@localhost.com');

-- ----------------------------
-- Table structure for profiles
-- ----------------------------
DROP TABLE IF EXISTS `profiles`;
CREATE TABLE `profiles` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `display_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `company` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lang` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en',
  `bio` text COLLATE utf8_unicode_ci,
  `dob` int(11) DEFAULT NULL,
  `gender` set('m','f','') COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_line1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_line2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address_line3` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postcode` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `updated_on` int(11) unsigned DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `ordering_count` int(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of profiles
-- ----------------------------
INSERT INTO `profiles` VALUES ('1', '1', 'Nguyen Dung', 'Nguyen', 'Dung', '', 'en', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);

-- ----------------------------
-- Table structure for settings
-- ----------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `SettingKey` bigint(20) NOT NULL AUTO_INCREMENT,
  `SettingCode` varchar(10) DEFAULT NULL,
  `DefaultValue` varchar(1000) DEFAULT NULL,
  `ActualValue` varchar(1000) DEFAULT NULL,
  `LabelValue` varchar(100) DEFAULT NULL,
  `ModuleName` varchar(50) DEFAULT NULL,
  `SettingOrder` int(11) DEFAULT NULL,
  `IsRequired` tinyint(4) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`SettingKey`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of settings
-- ----------------------------
INSERT INTO `settings` VALUES ('1', '000001', '10', '10', null, null, '1', '1', 'Record Per Page');
INSERT INTO `settings` VALUES ('2', '000002', '10,20,50', '10,20,50', null, null, '1', '1', 'List items per page');
INSERT INTO `settings` VALUES ('3', '000003', '1', '1', 'Active', null, '1', null, 'Status');
INSERT INTO `settings` VALUES ('4', '000003', '0', '0', 'UnActive', null, '2', null, 'Status');
INSERT INTO `settings` VALUES ('5', '000004', 'new_admin', 'new_admin', 'Administrator themes', null, '1', null, 'Administrator themes');
INSERT INTO `settings` VALUES ('6', '000005', 'new_user', 'new_user', 'Fronend themes', null, '1', null, 'Frontend themes');
INSERT INTO `settings` VALUES ('8', '000006', 'SMTP', 'SMTP', 'SMTP', null, '1', null, 'MAIL_PROTOCOL');
INSERT INTO `settings` VALUES ('10', '000007', '/usr/sbin/sendmail', 'ssl://smtp.googlemail.com', '/usr/sbin/sendmail', null, '1', null, 'MAIL_SENDMAIL_PATH');
INSERT INTO `settings` VALUES ('11', '000008', 'MAIL_SMTP_HOST', 'ssl://smtp.googlemail.com', 'MAIL_SMTP_HOST', null, '1', null, 'MAIL_SMTP_HOST');
INSERT INTO `settings` VALUES ('12', '000009', 'relation.test.02@gmail.com', 'relation.test.02@gmail.com', 'relation.test.02@gmail.com', null, '1', null, 'MAIL_SMTP_USER');
INSERT INTO `settings` VALUES ('14', '000010', 'relation@123', 'relation@123', 'relation@123', null, '1', null, 'MAIL_SMTP_PASS');
INSERT INTO `settings` VALUES ('15', '000011', '465', '465', '465', null, '1', null, 'MAIL_SMTP_PORT');
INSERT INTO `settings` VALUES ('16', '000012', 'partb2.com.au', 'partb2.com.au', 'partb2.com.au', null, '1', null, 'MAIL_ALIAS_NAME');
INSERT INTO `settings` VALUES ('17', '000013', 'relation02@gmail.com', 'relation02@gmail.com', 'relation02@gmail.com', null, '1', null, 'CONTACT_EMAIL');
INSERT INTO `settings` VALUES ('18', '000014', 'admin@localhost', 'admin@localhost12', 'admin@localhost', null, '1', null, 'MAIL_SERVER');
INSERT INTO `settings` VALUES ('19', '000015', 'Un-named Website', 'Virtual Post', 'Un-named Website', null, '1', null, 'Site Name ');
INSERT INTO `settings` VALUES ('20', '000016', 'Add your slogan here', 'Add your slogan here', 'Add your slogan here', null, '1', null, 'Site Slogan ');
INSERT INTO `settings` VALUES ('21', '000017', 'Y-m-d', 'Y-m-d', 'Y-m-d', null, '1', null, 'Date Format');
INSERT INTO `settings` VALUES ('22', '000018', '$', '$', '$', null, '1', null, 'Currency');
INSERT INTO `settings` VALUES ('23', '000019', '1', '1', '1', null, '1', null, 'Site Status');
INSERT INTO `settings` VALUES ('25', '000020', 'Sorry, this website is currently unavailable.', 'Sorry, this website is currently unavailable.', 'Sorry, this website is currently unavailable.', null, '1', null, 'Unavailable Message');
INSERT INTO `settings` VALUES ('26', '000021', 'PAYMENT_PAYPAL_USERNAME_CODE', 'PAYMENT_PAYPAL_USERNAME_CODE', 'PAYMENT_PAYPAL_USERNAME_CODE', null, '1', null, 'PAYMENT_PAYPAL_USERNAME_CODE');
INSERT INTO `settings` VALUES ('27', '000022', 'PAYMENT_PAYPAL_PASSWORD', 'PAYMENT_PAYPAL_PASSWORD', 'PAYMENT_PAYPAL_PASSWORD', null, '1', null, 'PAYMENT_PAYPAL_PASSWORD');
INSERT INTO `settings` VALUES ('28', '000023', 'PAYMENT_PAYPAL_SIGNATURE', 'PAYMENT_PAYPAL_SIGNATURE', 'PAYMENT_PAYPAL_SIGNATURE', null, '1', null, 'PAYMENT_PAYPAL_SIGNATURE');
INSERT INTO `settings` VALUES ('29', '000024', 'PAYMENT_EWAY_CUSTOMERID_CODE', 'PAYMENT_EWAY_CUSTOMERID_CODE', null, null, null, null, 'PAYMENT_EWAY_CUSTOMERID_CODE');
INSERT INTO `settings` VALUES ('30', '000025', 'C5', 'C5', 'C5', null, '3', null, 'Envelop Type');
INSERT INTO `settings` VALUES ('31', '000025', 'C4', 'C4', 'C4', null, '2', null, 'Envelop Type');
INSERT INTO `settings` VALUES ('32', '000026', null, '001', 'Category Type 1', null, '1', null, 'Category Type');
INSERT INTO `settings` VALUES ('33', '000026', null, '002', 'Category Type 2', null, '2', null, 'Category Type');
INSERT INTO `settings` VALUES ('34', '000026', null, '003', 'Category Type 3', null, '3', null, 'Category Type');
INSERT INTO `settings` VALUES ('35', '000026', null, '004', 'Category Type 4', null, '4', null, 'Category Type');
INSERT INTO `settings` VALUES ('36', '000026', null, '005', 'Category Type 5', null, '5', null, 'Category Type');
INSERT INTO `settings` VALUES ('37', '000025', 'C3', 'C3', 'C3', null, '1', null, 'Envelop Type');
INSERT INTO `settings` VALUES ('38', '000027', 'INV', 'INV', 'INV', null, null, null, 'Transaction Type');
INSERT INTO `settings` VALUES ('39', '000027', 'CRD', 'CRD', 'CRD', null, null, null, 'Transaction Type');
INSERT INTO `settings` VALUES ('40', '000028', '1', '1', 'Option 1', null, '1', null, 'Discount Type');
INSERT INTO `settings` VALUES ('41', '000028', '2', '2', 'Option 2', null, '2', null, 'Discount Type');
INSERT INTO `settings` VALUES ('42', '000028', '3', '3', 'Option 3', null, '3', null, 'Discount Type');
INSERT INTO `settings` VALUES ('43', '000029', '1', '3', '1', null, null, null, 'Default Discount Type');
INSERT INTO `settings` VALUES ('44', '000030', '1', '1', '1', null, null, null, 'GST Type (1 or 0)');
INSERT INTO `settings` VALUES ('46', '000031', '/uploads/images/logo/default_logo.png', '/uploads/images/logo/default_logo1.png', '/uploads/images/logo/default_logo.png', null, null, null, 'Site logo url');
INSERT INTO `settings` VALUES ('47', '000032', '0', '0', 'Available', null, null, null, 'Catalogue type (0: Available | 1: Catalogued)');
INSERT INTO `settings` VALUES ('48', '000032', '1', '1', 'Catalogued', null, null, null, 'Catalogue type (0: Available | 1: Catalogued)');

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varbinary(16) NOT NULL,
  `username` varchar(100) NOT NULL,
  `display_name` varchar(50) DEFAULT NULL,
  `password` varchar(80) NOT NULL,
  `salt` varchar(40) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `activation_code` varchar(40) DEFAULT NULL,
  `forgotten_password_code` varchar(40) DEFAULT NULL,
  `forgotten_password_time` int(11) unsigned DEFAULT NULL,
  `remember_code` varchar(40) DEFAULT NULL,
  `created_on` int(11) unsigned NOT NULL,
  `last_login` int(11) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', '127.0.0.1', 'admin', 'Nguyen Trong Dung', 'e7e793ccb91033a84efea89476e8475cee644fb8', '9462e8eee0', 'admin@admin.com', 'NULL', '1268889823', null, '1268889823', '1268889823', '1371379302', '1', 'Nguyen', 'Dung', 'FF', '1112223333', '1');
INSERT INTO `users` VALUES ('2', '', 'worker', 'Worker', 'e7e793ccb91033a84efea89476e8475cee644fb8', '9462e8eee0', 'worker@admin.com', null, '1268889823', null, '1268889823', '0', '1371379246', '1', 'Nguyen', 'Ha', 'FF', null, '2');

-- ----------------------------
-- Table structure for zipcodes
-- ----------------------------
DROP TABLE IF EXISTS `zipcodes`;
CREATE TABLE `zipcodes` (
  `zipcode` varchar(5) NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `city` varchar(32) NOT NULL,
  `state` varchar(2) NOT NULL,
  `state_name` varchar(15) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `zip_class` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`zipcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of zipcodes
-- ----------------------------
