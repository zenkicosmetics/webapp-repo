--
-- Table structure for table `shipping_carriers`
--
DROP TABLE IF EXISTS `shipping_carriers`;

CREATE TABLE IF NOT EXISTS `shipping_carriers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(5) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `carriers`
--

INSERT INTO `shipping_carriers` (`id`, `code`, `name`, `description`) VALUES
(1, 'FDX', 'FedEx', 'courier delivery services'),
(2, 'DHL', 'DHL', 'express logistics'),
(3, 'DEP', 'Deutsche Post', 'postal services, courier');

-- --------------------------------------------------------

--
-- Table structure for table `shipping_apis`
--

DROP TABLE IF EXISTS `shipping_apis`;

CREATE TABLE IF NOT EXISTS `shipping_apis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) DEFAULT '0',
  `name` varchar(80) DEFAULT NULL,
  `description` text,
  `account_no` varchar(80) DEFAULT NULL,
  `meter_no` varchar(80) DEFAULT NULL,
  `auth_key` varchar(80) DEFAULT NULL,
  `password` varchar(80) DEFAULT NULL,
  `site_id` varchar(80) DEFAULT NULL,
  `username` varchar(80) DEFAULT NULL,
  `estamp_partner_signature` varchar(255) DEFAULT NULL,
  `estamp_namespace` varchar(255) DEFAULT NULL,
  `price_includes_vat` float(3,2) DEFAULT '0',
  `created_date` datetime DEFAULT NULL,
  `created_by_type` tinyint(4) DEFAULT NULL,
  `created_by_id` bigint(20) DEFAULT NULL,
  `last_modified_date` datetime DEFAULT NULL,
  `last_modified_by_type` tinyint(4) DEFAULT NULL,
  `last_modified_by_id` bigint(20) DEFAULT NULL,
  `deleted_flag` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `carrier_id` (`carrier_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `shipping_apis`
--

INSERT INTO `shipping_apis` (`id`, `carrier_id`, `name`, `description`, `account_no`, `meter_no`, `auth_key`, `password`, `site_id`, `username`, `estamp_partner_signature`, `estamp_namespace`, `price_includes_vat`, `created_date`, `created_by_type`, `created_by_id`, `last_modified_date`, `last_modified_by_type`, `last_modified_by_id`, `deleted_flag`) VALUES
(1, 1, 'FedEx Germany', 'FedEx Germany account', '601619601', '100259254', '8bM2KLjWexxIji2A', 'I4aXn9YHm1TNaVg1UIwQx3o9k', '', '', NULL, NULL, 0.19, NULL, 0, 0, NULL, 0, 0, 0),
(2, 1, 'FedEx USA', 'FedEx USA account', 'xxxxxxxx', 'xxxxxxxx', 'xxxxxxxxxxxxxxxxxx', 'xxxxxxxxxxxxxxxxxxxxxxx', '', '', NULL, NULL, 0.00, NULL, 0, 0, NULL, 0, 0, 0),
(3, 3, 'Deutsche Post', 'Deutsche Post main account', '12345', '', '', 'xxxxxxxxx', '', 'xxxxxxxxx', NULL, NULL, 0.00, NULL, 0, 0, NULL, 0, 0, 0),
(4, 2, 'DHL Germany', 'DHL Express Germany main account', '5555555', '', '', 'xxxxxxxxx', 'xxxxxxxxx', 'xxxxxxxxx', NULL, NULL, 0.00, NULL, 0, 0, NULL, 0, 0, 0),
(5, 0, 'DHL UK', 'DHL UK account', 'xxxxxxxx', '', '', 'xxxxxxxxx', 'xxxxxxxxx', '', NULL, NULL, 0.00, NULL, 0, 0, NULL, 0, 0, 0),
(6, 0, 'E-Filiale', 'E-Filiale is an interface to send information to and get back a postal label to print out. This postal label is in the form of a QR code and is pre-paid', 'ADHCL', NULL, '1', 'Fehumi12', 'https://internetmarke.deutschepost.de/OneClickForAppV2?wsdl', 'pcf_09@zq4nnzgbnbvt3.webpage.t-com.de', 'hFIHYEnHlAnjvFsmK6OQJhHef7M4rOlo', 'http://oneclickforapp.dpag.de/V2', 0.00, NULL, 0, 0, NULL, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `shipping_services`
--

DROP TABLE IF EXISTS `shipping_services`;

CREATE TABLE IF NOT EXISTS `shipping_services` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `short_desc` varchar(100) DEFAULT NULL,
  `long_desc` text NOT NULL,
  `api_svc_code1` varchar(30) NOT NULL COMMENT 'service code used in api',
  `api_svc_code2` varchar(30) NOT NULL COMMENT 'service code used in api',
  `carrier_id` int(11) DEFAULT NULL,
  `api_acc_id` int(11) NOT NULL COMMENT 'api account id',
  `logo` varchar(80) NOT NULL,
  `factor_a` decimal(10,2) NOT NULL,
  `factor_b` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `carrier_id` (`carrier_id`),
  KEY `api_code1` (`api_svc_code1`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `shipping_services`
--

INSERT INTO `shipping_services` (`id`, `name`, `short_desc`, `long_desc`, `api_svc_code1`, `api_svc_code2`, `carrier_id`, `api_acc_id`, `logo`, `factor_a`, `factor_b`) VALUES
(1, 'FedEx International Economy', 'Express service', '', 'INTERNATIONAL_ECONOMY', '', 1, 1, 'assets/img/fedex_express_logo.gif', 0.70, 1.00),
(2, 'FedEx International Priority', 'Express service', '', 'INTERNATIONAL_PRIORITY', '', 1, 1, 'assets/img/fedex_express_logo.gif', 0.70, 1.00),
(3, 'FedEx International First', 'Express service', '', 'INTERNATIONAL_FIRST', '', 1, 1, 'assets/img/fedex_express_logo.gif', 0.80, 1.00),
(4, 'FedEx Priority Overnight', 'Express service', 'Domestic USA & Canada', 'PRIORITY_OVERNIGHT', '', 1, 2, 'assets/img/fedex_express_logo.gif', 0.60, 1.00),
(5, 'FedEx Ground', 'Ground Service', 'Domestic USA & Canada', 'FEDEX_GROUND', '', 1, 2, 'assets/img/fedex_ground_logo.gif', 0.40, 1.00),
(6, 'Deutsche Post Brief', 'Standard postal letter shipment', 'Letters will be sent as standard registered mail...', '', '', 3, 3, 'assets/img/deutsche_post_logo.png', 0.50, 1.00),
(7, 'DHL Domestic Express 9:00', 'Express service', 'Express shipment within Germany with delivery before 9 a.m. on the next working day', '', '', 2, 4, 'assets/img/dhl_express_logo.gif', 0.30, 1.00),
(8, 'DHL Domestic Express 10:00', 'Express service', 'Express shipment within Germany with delivery before 10 a.m. on the next working day', '', '', 2, 4, 'assets/img/dhl_express_logo.gif', 0.30, 1.00),
(9, 'DHL Domestic Express 12:00', 'Express service', 'Express shipment within Germany with delivery before midday on the next working day', '', '', 2, 4, 'assets/img/dhl_express_logo.gif', 0.40, 1.00),
(10, 'DHL Domestic Express', 'Express service', 'Express shipment within Germany with delivery during the course of the day.', '', '', 2, 4, 'assets/img/dhl_express_logo.gif', 0.50, 1.00);


--
-- Alter table `location`
--

ALTER TABLE `location` ADD `shipping_factor_fl` FLOAT DEFAULT 1.0 AFTER `device_id`;
ALTER TABLE `location` ADD `available_shipping_services` VARCHAR(255) DEFAULT '1,2,3' AFTER `shipping_factor_fl`;
ALTER TABLE `location` ADD `primary_letter_shipping` int(10) AFTER `available_shipping_services`;
ALTER TABLE `location` ADD `standard_national_parcel_service` int(10) AFTER `primary_letter_shipping`;
ALTER TABLE `location` ADD `standard_international_parcel_service` int(10) AFTER `standard_national_parcel_service`;

--
-- Alter table `customers`
--

ALTER TABLE `customers` ADD `shipping_factor_fc` FLOAT DEFAULT 1.0 AFTER `plan_date_change_account_type`;


ALTER TABLE `envelope_shipping` ADD `customs_handling_fee` FLOAT DEFAULT 0 AFTER `forwarding_charges_fee`;

ALTER TABLE `envelope_shipping` ADD `shipping_service_id` int DEFAULT 0 AFTER `shipping_date`;

ALTER TABLE envelope_shipping_request ADD shipping_service_id int;

ALTER TABLE envelope_customs ADD package_id int AFTER envelope_id;

ALTER TABLE `envelope_shipping` ADD `insurance_customs_cost` DECIMAL( 18, 10 ) NOT NULL AFTER `customs_handling_fee` ;

ALTER TABLE `envelope_shipping` ADD `special_service_fee` DECIMAL( 18, 10 ) NOT NULL AFTER `customs_handling_fee` ;
ALTER TABLE `envelope_shipping` CHANGE `special_service_fee` `special_service_fee` DECIMAL( 18, 10 ) NOT NULL DEFAULT '0';

-- =================================== update label size===================================
UPDATE `settings` SET `ActualValue`='1' WHERE  `SettingCode`='000034' AND DefaultValue=1;
UPDATE `settings` SET `ActualValue`='2' WHERE  `SettingCode`='000034' AND DefaultValue=2;
UPDATE `settings` SET `ActualValue`='3' WHERE  `SettingCode`='000034' AND DefaultValue=3;


INSERT INTO `settings` (`SettingCode`, `DefaultValue`, `ActualValue`, `LabelValue`, `SettingOrder`, `description`, `created_by_type`, `created_by_id`, `last_modified_by_type`, `last_modified_by_id`) VALUES ('000236', '1', '1', 'PAPER_4X6', 2, 'Shipping Type', 0, 0, 0, 0);
INSERT INTO `settings` (`SettingCode`, `DefaultValue`, `ActualValue`, `LabelValue`, `SettingOrder`, `description`, `created_by_type`, `created_by_id`, `last_modified_by_type`, `last_modified_by_id`) VALUES ('000236', '2', '2', 'PAPER_4X8', 2, 'Shipping Type', 0, 0, 0, 0);
INSERT INTO `settings` (`SettingCode`, `DefaultValue`, `ActualValue`, `LabelValue`, `SettingOrder`, `description`, `created_by_type`, `created_by_id`, `last_modified_by_type`, `last_modified_by_id`) VALUES ('000236', '3', '3', 'PAPER_4X9', 2, 'Shipping Type', 0, 0, 0, 0);
INSERT INTO `settings` (`SettingCode`, `DefaultValue`, `ActualValue`, `LabelValue`, `SettingOrder`, `description`, `created_by_type`, `created_by_id`, `last_modified_by_type`, `last_modified_by_id`) VALUES ('000236', '4', '4', 'PAPER_7X4.75', 2, 'Shipping Type', 0, 0, 0, 0);
INSERT INTO `settings` (`SettingCode`, `DefaultValue`, `ActualValue`, `LabelValue`, `SettingOrder`, `description`, `created_by_type`, `created_by_id`, `last_modified_by_type`, `last_modified_by_id`) VALUES ('000236', '5', '5', 'PAPER_8.5X11_BOTTOM_HALF_LABEL', 2, 'Shipping Type', 0, 0, 0, 0);
INSERT INTO `settings` (`SettingCode`, `DefaultValue`, `ActualValue`, `LabelValue`, `SettingOrder`, `description`, `created_by_type`, `created_by_id`, `last_modified_by_type`, `last_modified_by_id`) VALUES ('000236', '6', '6', 'PAPER_8.5X11_TOP_HALF_LABEL', 2, 'Shipping Type', 0, 0, 0, 0);
INSERT INTO `settings` (`SettingCode`, `DefaultValue`, `ActualValue`, `LabelValue`, `SettingOrder`, `description`, `created_by_type`, `created_by_id`, `last_modified_by_type`, `last_modified_by_id`) VALUES ('000236', '7', '7', 'PAPER_LETTER', 2, 'Shipping Type', 0, 0, 0, 0);


ALTER TABLE `shipping_services` ADD `weight_limit` FLOAT DEFAULT 68 AFTER `factor_b`;
ALTER TABLE `shipping_services` ADD `dimension_limit` FLOAT DEFAULT 330 AFTER `weight_limit`;

ALTER TABLE `shipping_services` ADD `shipping_service_template` varchar(3) DEFAULT '1' AFTER `dimension_limit`;


/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : clevvermail_new
Target Host     : localhost:3306
Target Database : clevvermail_new
Date: 2016-09-30 10:48:30
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for envelope_pickup_schedule
-- ----------------------------
DROP TABLE IF EXISTS `envelope_pickup_schedule`;
CREATE TABLE `envelope_pickup_schedule` (
  `id` bigint(20) NOT NULL DEFAULT '0',
  `envelope_id` bigint(20) DEFAULT NULL,
  `package_id` bigint(20) DEFAULT NULL,
  `postbox_id` bigint(20) DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `pickup_date` bigint(20) DEFAULT NULL,
  `pickup_transaction_id` varchar(250) DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of envelope_pickup_schedule
-- ----------------------------


ALTER TABLE envelope_prepayment_cost
ADD direct_shipping_cost decimal(20,2) after `item_scan_cost`;

ALTER TABLE envelope_prepayment_cost
ADD collect_shipping_cost decimal(20,2) after direct_shipping_cost;

ALTER TABLE envelope_prepayment_cost
ADD shipping_info_decode_key varchar(4000) after collect_shipping_cost;