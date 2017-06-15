-- phpMyAdmin SQL Dump
-- version 3.5.5
-- http://www.phpmyadmin.net
--
-- Host: dbvjrtual.cdb8ugp8zpgb.us-west-2.rds.amazonaws.com:3306
-- Generation Time: Dec 29, 2013 at 03:16 PM
-- Server version: 5.5.31-log
-- PHP Version: 5.3.23

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `dbvjrtual`
--

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

DROP TABLE IF EXISTS `city`;
CREATE TABLE IF NOT EXISTS `city` (
  `city_id` int(11) NOT NULL AUTO_INCREMENT,
  `city` varchar(30) DEFAULT NULL,
  `state_code` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`city_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

DROP TABLE IF EXISTS `ci_sessions`;
CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` bigint(20) NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ci_sessions`
--

INSERT INTO `ci_sessions` (`session_id`, `ip_address`, `user_agent`, `last_activity`, `user_data`) VALUES
('2373f4a2ca513191e0e5d2f38bac9f17', '54.214.27.234', 'Wget/1.14 (linux-gnu)', 1388329621, ''),
('4e5be35572e4ef8a41845292a4015462', '54.214.27.234', 'Wget/1.14 (linux-gnu)', 1388326022, '');

-- --------------------------------------------------------

--
-- Table structure for table `clouds`
--

DROP TABLE IF EXISTS `clouds`;
CREATE TABLE IF NOT EXISTS `clouds` (
  `cloud_id` varchar(3) NOT NULL DEFAULT '',
  `cloud_name` varchar(250) DEFAULT NULL,
  `active_flag` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`cloud_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `clouds`
--

INSERT INTO `clouds` (`cloud_id`, `cloud_name`, `active_flag`) VALUES
('001', 'Dropbox', 1),
('002', 'iCloud', 0),
('003', 'Skydrive', 0),
('004', 'Google Drive', 0),
('005', 'Amazon Drive', 0),
('006', 'Evernote', 0),
('007', 'Box.net', 0);

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
CREATE TABLE IF NOT EXISTS `country` (
  `country_code` int(11) NOT NULL,
  `country_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`country_code`),
  UNIQUE KEY `country_code` (`country_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `customer_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_code` varchar(20) DEFAULT NULL,
  `user_name` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `account_type` smallint(6) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `envelope_scan` smallint(255) DEFAULT NULL,
  `scans` smallint(255) DEFAULT NULL,
  `shipment` smallint(255) DEFAULT NULL,
  `number_of_postboxes` int(11) DEFAULT NULL,
  `last_access_date` int(11) DEFAULT NULL,
  `token_key` varchar(255) DEFAULT NULL,
  `auto_save_cloud` tinyint(4) DEFAULT '0',
  `new_account_type` tinyint(4) DEFAULT NULL,
  `plan_date_change_account_type` varchar(8) DEFAULT NULL,
  `shipping_address_completed` tinyint(4) DEFAULT '0',
  `activated_flag` tinyint(4) DEFAULT '0',
  `invoicing_address_completed` tinyint(4) DEFAULT '0',
  `postbox_name_flag` tinyint(4) DEFAULT '0',
  `name_comp_address_flag` tinyint(4) DEFAULT '0',
  `city_address_flag` tinyint(4) DEFAULT '0',
  `payment_detail_flag` tinyint(4) DEFAULT '0',
  `charge_fee_flag` tinyint(4) DEFAULT '0',
  `email_confirm_flag` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `user_id` (`customer_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=93 ;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `customer_code`, `user_name`, `email`, `account_type`, `password`, `envelope_scan`, `scans`, `shipment`, `number_of_postboxes`, `last_access_date`, `token_key`, `auto_save_cloud`, `new_account_type`, `plan_date_change_account_type`, `shipping_address_completed`, `activated_flag`, `invoicing_address_completed`, `postbox_name_flag`, `name_comp_address_flag`, `city_address_flag`, `payment_detail_flag`, `charge_fee_flag`, `email_confirm_flag`) VALUES
(1, 'C00000001', 'customer01@localhost.com', 'customer01@localhost.com', 1, 'e10adc3949ba59abbe56e057f20f883e', 1, 1, 1, NULL, 1388281926, '5f2ced2e093c0fef1b0168beb7ecd001', 0, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 0, 1),
(54, 'C00000054', 'hemmrich1@dieholding.de', 'hemmrich1@dieholding.de', 1, 'e10adc3949ba59abbe56e057f20f883e', NULL, NULL, NULL, NULL, 1380642678, NULL, 0, NULL, NULL, 1, 1, 1, 1, 1, 1, 1, 0, 1),
(81, 'C00000081', 'nguyen.trong.dung830323@gmail.com', 'nguyen.trong.dung830323@gmail.com', 1, 'e10adc3949ba59abbe56e057f20f883e', NULL, NULL, NULL, NULL, 1387894317, 'VRE9xNNPWPy4oV5nvy9ku5AgbdiFCn', 0, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 1),
(82, 'C00000082', 'dykatana@freenet.de', 'dykatana@freenet.de', 1, 'e10adc3949ba59abbe56e057f20f883e', NULL, NULL, NULL, NULL, 1387884787, 'vY7wM16W1Ir8KqY8aZaNNy2BCclt0O', 0, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 1),
(83, 'C00000083', 'partner@dertaler.de', 'partner@dertaler.de', 1, 'e10adc3949ba59abbe56e057f20f883e', NULL, NULL, NULL, NULL, 1387884952, 'yLvMhiDOTwrg0DBiEyaK8r9UUk07FL', 0, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 1),
(84, 'C00000084', 'testershubdemo95@gmail.com', 'testershubdemo95@gmail.com', 1, 'af8c273f5835e9be4899240b99a1f94e', NULL, NULL, NULL, NULL, 1387909301, '7DUdYPZvY2QyHB8QZ0XqsnOsjaxSSW', 0, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 1),
(85, 'C00000085', 'testershubdemo7@gmail.com', 'testershubdemo7@gmail.com', 1, '8e607a4752fa2e59413e5790536f2b42', NULL, NULL, NULL, NULL, 1388236203, '8PR8zkUmJHI8Zeub3GilqkZj9n19zB', 0, NULL, NULL, 1, 0, 1, 1, 1, 1, 0, 1, 1),
(86, 'C00000086', 'jayantd.testershub@gmail.com', 'jayantd.testershub@gmail.com', 1, 'e10adc3949ba59abbe56e057f20f883e', NULL, NULL, NULL, NULL, 1388223001, '7ZJadcjjw84Y4UxZX7arKIRO7qceku', 0, NULL, NULL, 1, 0, 1, 1, 1, 1, 0, 1, 1),
(87, 'C00000087', 'testerHUB@mail.com', 'testerHUB@mail.com', 1, 'e10adc3949ba59abbe56e057f20f883e', NULL, NULL, NULL, NULL, NULL, 'ow5DaRpZLCjeoDSyxBS5FjKFPHlijc', 0, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 1),
(88, 'C00000088', 'advaitk.testershub@gmail.com', 'advaitk.testershub@gmail.com', 1, 'e10adc3949ba59abbe56e057f20f883e', NULL, NULL, NULL, NULL, 1388229083, 'WInCUh9hHDezYchUcg0ZQChJymqgCV', 0, NULL, NULL, 1, 0, 1, 1, 1, 1, 0, 0, 1),
(89, 'C00000089', 'testershubdemo7', 'testershubdemo7', 1, '8e607a4752fa2e59413e5790536f2b42', NULL, NULL, NULL, NULL, 1388057021, 'bfCxM6PhPbzzh9uCYZhHIabqcG72JS', 0, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 1, 1),
(90, 'C00000090', 'testershubdemo7@gmail.', 'testershubdemo7@gmail.', 1, '8e607a4752fa2e59413e5790536f2b42', NULL, NULL, NULL, NULL, 1388057053, 'UVRqR9M4xZLprnIrWbt1tbzM3Vp1NC', 0, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 1, 1),
(91, 'C00000091', 'testershubdemo2@gmail.com', 'testershubdemo2@gmail.com', 1, 'e10adc3949ba59abbe56e057f20f883e', NULL, NULL, NULL, NULL, 1388057341, 'nAyqs7fyVdoZlt5K3QARC6XrCQgcb5', 0, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 1),
(92, 'C00000092', 'jayantdangare@rediffmail.com', 'jayantdangare@rediffmail.com', 1, 'e10adc3949ba59abbe56e057f20f883e', NULL, NULL, NULL, NULL, 1388170157, 'joFrfn8sRJuRe9ulCTf3PForrPitsQ', 0, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `customers_address`
--

DROP TABLE IF EXISTS `customers_address`;
CREATE TABLE IF NOT EXISTS `customers_address` (
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

--
-- Dumping data for table `customers_address`
--

INSERT INTO `customers_address` (`customer_id`, `shipment_address_name`, `shipment_company`, `shipment_street`, `shipment_postcode`, `shipment_city`, `shipment_region`, `shipment_country`, `invoicing_address_name`, `invoicing_company`, `invoicing_street`, `invoicing_postcode`, `invoicing_city`, `invoicing_region`, `invoicing_country`, `is_bussiness`, `vat_number`) VALUES
(1, 'Nguyen Trong Dung', 'VIB - Funnyfox', 'Ha Noi', '084', 'Ha Noi', 'Ha Noi', 'Viet Nam', 'Christian Hemmrich', 'dieHolding H&H GmbH', 'Aldegreverweg 29', '59227', 'Ahlen', 'Northrine Westfalia', 'Germany', NULL, NULL),
(3, 'Nguyen Trong Dung', 'VIB', 'HN', '084', 'HN', 'HN', 'Viet Nam', 'Nguyen Trong Dung', 'VIB', 'HN', '084', 'HN', 'HN', 'Viet Nam', NULL, NULL),
(41, '1111', '1111111111', '1111111111111', '111111111', '11111111', '1111111111', '11111111111', '1111', '1111111111', '1111111111111', '111111111', '11111111', '1111111111', '11111111111', NULL, NULL),
(42, 'Christian Hemmrich', 'dieHolding H&H GmbH', 'Alfred-Delp-Str. 1', '49080', 'Osnabrück', 'sdfgas', 'Germany', 'Christian Hemmrich', 'dieHolding H&H GmbH', 'Alfred-Delp-Str. 1', '49080', 'Osnabrück', 'asfdg', 'Germany', NULL, NULL),
(44, 'Christian Hemmrich', 'dieHolding H&H GmbH', 'Aldegreverweg 29', '59227', 'Ahlen', 'NRW', 'Germany', 'Christian Hemmrich', 'dieholding H&H GmbH', 'Aldegreverwg 29', '549227', 'Ahlen', 'nRW', 'Germany', NULL, NULL),
(45, 'Christian Hemmrich', 'asdfgasdgf', 'asdgasdg', 'asdasdg', 'asdgasdg', 'asdgasdg', 'asdgasdg', 'asdgsadg', 'asdgasdg', 'asdgasdg', 'asdgasdg', 'asdgasdg', 'asdgasdg', 'asdgasdg', NULL, NULL),
(50, 'öasldkfjölakas', 'sadf', 'ölkhölkj', 'asdf', 'ölkhj', 'ölkj', 'ölkj', 'öasldkfjölakas', 'sadf', 'ölkhölkj', 'asdf', 'ölkhj', 'ölkj', 'ölkj', NULL, NULL),
(51, 'Nguyen Trong Dung', 'VIB', 'HN', '084', 'Ha Noi', 'Ha Noi', 'Viet Nam', 'Nguyen Trong Dung', 'VIB', 'HN', '084', 'Ha Noi', 'Ha Noi', 'Viet Nam', NULL, NULL),
(52, 'sdfgsdfgsdfg', 'sdfgsdfg', 'sdsdfg', 'sdfg', 'sdfgsdf', 'gsdfg', 'sdfgsd', 'sdfgsdfgsdfg', 'sdfgsdfg', 'sdsdfg', 'sdfg', 'sdfgsdf', 'gsdfg', 'sdfgsd', NULL, NULL),
(53, 'asdfa', 'sdfasdfasdf', 'asdfasd', 'fasdf', 'asdf', 'asdfasd', 'fasd', 'asdfa', 'sdfasdfasdf', 'asdfasd', 'fasdf', 'asdf', 'asdfasd', 'fasd', NULL, NULL),
(54, 'werqwes', 'gasfdg', 'asfhg', 'adfga', 'asfg', 'afsdga', 'fgasfg', 'werqwes', 'gasfdg', 'asfhg', 'adfga', 'asfg', 'afsdga', 'fgasfg', NULL, NULL),
(55, 'Tim Breite', 'home', 'Friedrichstraße 18', '49078', 'Osnabrück', 'Niedersachsen', 'Deuschland', 'Tim Breite', 'home', 'Friedrichstraße 18', '49078', 'Osnabrück', 'Niedersachsen', 'Deuschland', NULL, NULL),
(56, 'dfjashdf', 'lkjhölkjsdf', 'ölkhsdfölkj', '87634', 'jhdf', 'asödlfkjhj', 'aösldjhfa', 'dfjashdf', 'lkjhölkjsdf', 'ölkhsdfölkj', '87634', 'jhdf', 'asödlfkjhj', 'aösldjhfa', NULL, NULL),
(62, 'Shipment address Name', 'Shipment address Company', 'Shipment address Street', '084', 'Ha Noi', 'Viet Name', 'Viet Nam', 'Shipment address Name', 'Shipment address Company', 'Shipment address Street', '084', 'Ha Noi', 'Viet Name', 'Viet Nam', NULL, NULL),
(63, 'sdfghhsdfhgsd', 'fhsdfgh', 'sdfgh', 'sdgfhs', 'dfghhsdf', 'hsdfh', 'sdfh', 'sdfghhsdfhgsd', 'fhsdfgh', 'sdfgh', 'sdgfhs', 'dfghhsdf', 'hsdfh', 'sdfh', NULL, NULL),
(64, 'Christian Hemmrich', 'dieHolding H&H GmbH', 'Aldegreverweg 29', '59227', 'Ahlen', 'NRW', 'Germany', 'Christian Hemmrich', 'dieHolding H&H GmbH', 'Aldegreverweg 29', '59227', 'Ahlen', 'NRW', 'Germany', NULL, NULL),
(75, 'Test', 'Test', 'Test', '123456', 'Test', 'Test', 'Test', 'Test', 'Test', 'Test', '123456', 'Test', 'Test', 'Test', NULL, NULL),
(80, 'sdfasdf', 'asdfasd', 'fasdfasd', 'fasdfas', 'dfasdf', 'asdfasdf', 'asdfasdf', 'sdfasdf', 'asdfasd', 'fasdfasd', 'fasdfas', 'dfasdf', 'asdfasdf', 'asdfasdf', NULL, NULL),
(85, 'test', 'test', 'test', '411015', 'test', 'test', 'test', 'test', 'test', 'test', '411015', 'test', 'test', 'test', NULL, NULL),
(86, 'test', 'test', 'test', 'test', 'test', 'teste', 'test', 'test', 'test', 'test', 'test', 'test', 'teste', 'test', NULL, NULL),
(88, '@#$232314234234@@@@@@@@@@@@@@@@#', '@#$%^', '@#$@', '@#@5@#@@############', '@#$@$@$3532525', '@$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$4', '!!1111', '@#$232314234234@@@@@@@@@@@@@@@@#', '@#$%^', '@#$@', '@#@5@#@@############', '@#$@$@$3532525', '@$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$4', '111111', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customers_invoices`
--

DROP TABLE IF EXISTS `customers_invoices`;
CREATE TABLE IF NOT EXISTS `customers_invoices` (
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

-- --------------------------------------------------------

--
-- Table structure for table `customers_location_available`
--

DROP TABLE IF EXISTS `customers_location_available`;
CREATE TABLE IF NOT EXISTS `customers_location_available` (
  `location_available_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `postcode` varchar(9) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`location_available_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `customers_location_available`
--

INSERT INTO `customers_location_available` (`location_available_id`, `customer_id`, `name`, `street`, `postcode`, `region`, `country`) VALUES
(1, 1, 'Berlin', 'Musterstraße 12', '10025', 'Berlin Berlin', 'Germany'),
(2, 1, 'New York', 'Wall 2', '80237', 'New York', 'USA');

-- --------------------------------------------------------

--
-- Table structure for table `customer_cloud`
--

DROP TABLE IF EXISTS `customer_cloud`;
CREATE TABLE IF NOT EXISTS `customer_cloud` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) DEFAULT NULL,
  `cloud_id` varchar(3) DEFAULT NULL,
  `auto_save_flag` tinyint(4) DEFAULT NULL COMMENT '0: not synchronized | 1:synchronized',
  `settings` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `customer_cloud`
--

INSERT INTO `customer_cloud` (`id`, `customer_id`, `cloud_id`, `auto_save_flag`, `settings`) VALUES
(3, 40, '001', 1, NULL),
(5, 43, '001', 1, NULL),
(7, 45, '001', 1, NULL),
(8, 1, '001', 1, '{"folder_name":"ClevverMail2609","oauth_token":"zaffxx1phuf1nlr4","oauth_token_secret":"evwdggo1gqsjbot"}'),
(11, 3, '001', 0, '{"oauth_token":"vcqispdwaf5oix62","oauth_token_secret":"cdtl45nhhw8ay1n","folder_name":"\\/Test2609\\/PO_1"}'),
(16, 42, '001', 1, '{"oauth_token":"axvfy8szl1iva17j","oauth_token_secret":"439ad5ofsyygsfp","folder_name":"\\/_Hemmi\\/clevver"}'),
(17, 64, '001', 1, '{"oauth_token":"7ulitawrv9qx1ewb","oauth_token_secret":"ae8ei52h55pwz6o","folder_name":"\\/_Hemmi\\/clevver"}');

-- --------------------------------------------------------

--
-- Table structure for table `customer_payment`
--

DROP TABLE IF EXISTS `customer_payment`;
CREATE TABLE IF NOT EXISTS `customer_payment` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) DEFAULT NULL,
  `payment_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

DROP TABLE IF EXISTS `emails`;
CREATE TABLE IF NOT EXISTS `emails` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `emails`
--

INSERT INTO `emails` (`id`, `slug`, `subject`, `description`, `content`) VALUES
(1, 'new_customer_register', '[ClevverMail] - Customer registration nofitication', 'Email template for register new customer', '<p>\n	Dear, {{full_name}}</p>\n<p>\n	&nbsp;</p>\n<p>\n	Thank you for registering with ClevverMail.</p>\n<p>\n	Your account has been created.</p>\n<p>\n	&nbsp;</p>\n<p>\n	Your login: {{email}}</p>\n<p>\n	Please click to this link {{active_url}} to activate your account.</p>\n<p>\n	&nbsp;</p>\n<p>\n	sincerely, your ClevverMail Team</p>'),
(2, 'customer_reset_password', '[ClevverMail] - New password nofitication', 'Email template for customer reset password', '<p>\n	Dear, {{full_name}}</p>\n<p>\n	Your password of account in {{site_url}} has been reseted.</p>\n<p>\n	Your login: {{email}} Your password: {{password}}</p>\n<p>\n	Best regards,</p>\n<p>\n	Clevver Mail</p>'),
(3, 'new_incomming_notification', '[ClevverMail] - New incomming nofitication', 'Email template for incomming notification', '<p>\n	Dear, {{full_name}}</p>\n<p>\n	New incomming envelope has been created.</p>\n<p>\n	Please click to this link {{site_url}} to check it.</p>\n<p>\n	Best regards,</p>\n<p>\n	ClevverMail</p>');

-- --------------------------------------------------------

--
-- Table structure for table `envelopes`
--

DROP TABLE IF EXISTS `envelopes`;
CREATE TABLE IF NOT EXISTS `envelopes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=108 ;

--
-- Dumping data for table `envelopes`
--

INSERT INTO `envelopes` (`id`, `envelope_code`, `from_customer_name`, `to_customer_id`, `postbox_id`, `envelope_type_id`, `weight`, `weight_unit`, `completed_by`, `completed_date`, `incomming_date`, `incomming_date_only`, `last_updated_date`, `category_type`, `invoice_flag`, `shipping_type`, `include_estamp_flag`, `sync_cloud_flag`, `sync_cloud_date`, `envelope_scan_flag`, `item_scan_flag`, `item_scan_date`, `direct_shipping_flag`, `direct_shipping_date`, `collect_shipping_flag`, `collect_shipping_date`, `trash_flag`, `trash_date`, `storage_flag`, `storage_date`, `completed_flag`, `email_notification_flag`, `package_id`, `shipping_id`, `new_notification_flag`) VALUES
(92, 'C00000001_BER02_231213_001', 'DungNT2312-002', 1, 2, 'C4', 21.000, 'g', 1, 1387883863, 1387812104, '231213', 1387812181, '001', 1, 0, 0, 0, NULL, 1, 1, 1387814202, 1, 1387883536, 127, NULL, NULL, NULL, NULL, NULL, 1, 1, 3, NULL, 0),
(93, 'C00000001_BER02_231213_002', 'DungNT2312-003', 1, 2, 'C4', 21.000, 'g', 1, 1387883536, 1387812120, '231213', 1387812181, '', 1, 0, 0, 0, NULL, 0, 1, 1387883487, 1, 1387883536, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 3, NULL, 0),
(94, 'C00000001_BER02_231213_003', 'DungNT2312-004', 1, 2, 'C5', 21.000, 'g', 1, 1387883558, 1387812135, '231213', 1387812181, '', 1, 0, 0, 0, NULL, 1, 1, 1387817027, 1, 1387883536, 127, NULL, NULL, NULL, NULL, NULL, 1, 1, 3, NULL, 0),
(97, 'C00000001_BER01_251213_001', 'christain', 1, 1, 'C5', 23.000, 'g', NULL, NULL, 1387967805, '251213', 1388007379, '', 1, NULL, NULL, 0, NULL, NULL, 1, 1387967862, NULL, NULL, NULL, NULL, 1, 1388007379, NULL, NULL, 0, 1, NULL, NULL, 0),
(98, 'C00000001_BER01_251213_002', 'christian', 1, 1, 'C4', 32.000, 'g', NULL, NULL, 1387968603, '251213', 1387968618, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, 0),
(99, 'C00000001_BER01_251213_003', 'christian', 1, 1, 'C4', 23.000, 'g', 1, 1388129449, 1387969557, '251213', 1387969932, '', 1, 0, 0, 0, NULL, NULL, 1, 1388129326, 1, 1388129449, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 4, NULL, 0),
(100, 'C00000001_BER01_251213_004', 'Christain1', 1, 1, 'C4', 23.000, 'g', 1, 1388129449, 1387969742, '251213', 1387969925, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, 1, 1388129449, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 4, NULL, 0),
(101, 'C00000001_BER01_251213_005', 'Christain2', 1, 1, 'C5', 23.000, 'g', 1, 1388129449, 1387969752, '251213', 1387969942, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, 1, 1388129449, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 4, NULL, 0),
(102, 'C00000001_BER01_251213_006', 'Christain3', 1, 1, 'C4', 23.000, 'g', 1, 1388129449, 1387969764, '251213', 1387969932, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, 1, 1388129449, 0, NULL, NULL, NULL, NULL, NULL, 1, 1, 4, NULL, 0),
(103, 'C00000001_BER01_251213_007', 'Christian4', 1, 1, 'C3', 0.000, 'g', NULL, NULL, 1387969774, '251213', 1388129588, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 1, 6, NULL, 0),
(104, 'C00000001_BER02_251213_001', 'christian', 1, 2, 'C5', 23.000, 'g', NULL, NULL, 1387969953, '251213', 1387969968, NULL, 0, NULL, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 0, 1, 5, NULL, 0),
(105, 'C00000001_BER02_251213_002', 'DungNT1226-001', 1, 2, 'C4', 11.000, 'g', NULL, NULL, 1388005360, '251213', 1388007045, NULL, 0, NULL, NULL, 0, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1388007045, NULL, NULL, 0, 1, NULL, NULL, 0),
(106, 'C00000001_BER01_251213_008', 'DungNT1226-002', 1, 1, 'C4', 11.000, 'g', NULL, NULL, 1388007942, '251213', 1388128050, '', 1, NULL, NULL, 0, NULL, NULL, 1, 1388008277, NULL, NULL, NULL, NULL, 1, 1388128050, NULL, NULL, 0, 1, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `envelopes_completed`
--

DROP TABLE IF EXISTS `envelopes_completed`;
CREATE TABLE IF NOT EXISTS `envelopes_completed` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=155 ;

--
-- Dumping data for table `envelopes_completed`
--

INSERT INTO `envelopes_completed` (`id`, `envelope_id`, `from_customer_name`, `to_customer_id`, `activity_id`, `activity_name`, `postbox_id`, `envelope_type_id`, `weight`, `weight_unit`, `last_updated_date`, `completed_by`, `completed_date`, `incomming_date`, `category_type`, `invoice_flag`, `shipping_type`, `include_estamp_flag`, `sync_cloud_flag`, `envelope_scan_flag`, `item_scan_flag`, `direct_shipping_flag`, `collect_shipping_flag`, `trash_flag`, `storage_flag`, `completed_flag`, `email_notification_flag`, `activity_code`) VALUES
(125, 92, 'DungNT2312-002', 1, 2, NULL, 2, 'C4', 21.000, 'g', 1387812181, 1, 1387814202, 1387812104, '001', 1, NULL, NULL, 0, 0, 1, NULL, 0, NULL, NULL, 1, 1, 'C00000001_BER02_231213_001_02'),
(126, 94, 'DungNT2312-004', 1, 2, NULL, 2, 'C5', 21.000, 'g', 1387812181, 1, 1387817027, 1387812135, '', 1, NULL, NULL, 0, 0, 1, NULL, 0, NULL, NULL, 1, 1, 'C00000001_BER02_231213_003_02'),
(127, 94, 'DungNT2312-004', 1, 1, NULL, 2, 'C5', 21.000, 'g', 1387812181, 1, 1387883447, 1387812135, '', 1, NULL, NULL, 0, 1, 1, NULL, 0, NULL, NULL, 1, 1, 'C00000001_BER02_231213_003_01'),
(128, 93, 'DungNT2312-003', 1, 2, NULL, 2, 'C4', 21.000, 'g', 1387812181, 1, 1387883487, 1387812120, '', 1, NULL, NULL, 0, 0, 1, NULL, 0, NULL, NULL, 1, 1, 'C00000001_BER02_231213_002_02'),
(129, 94, 'DungNT2312-004', 1, 3, NULL, 2, 'C5', 21.000, 'g', 1387812181, 1, 1387883536, 1387812135, '', 1, NULL, NULL, 0, 1, 1, NULL, 0, NULL, NULL, 1, 1, 'C00000001_BER02_231213_003_03'),
(130, 94, 'DungNT2312-004', 1, 3, NULL, 2, 'C5', 21.000, 'g', 1387812181, 1, 1387883536, 1387812135, '', 1, NULL, NULL, 0, 1, 1, NULL, 0, NULL, NULL, 1, 1, 'C00000001_BER02_231213_003_03'),
(131, 94, 'DungNT2312-004', 1, 3, NULL, 2, 'C5', 21.000, 'g', 1387812181, 1, 1387883536, 1387812135, '', 1, NULL, NULL, 0, 1, 1, 1, 0, NULL, NULL, 1, 1, 'C00000001_BER02_231213_003_03'),
(132, 94, 'DungNT2312-004', 1, 4, NULL, 2, 'C5', 21.000, 'g', 1387812181, 1, 1387883558, 1387812135, '', 1, 0, 1, 0, 1, 1, 1, 127, NULL, NULL, 1, 1, 'C00000001_BER02_231213_003_04'),
(133, 94, 'DungNT2312-004', 1, 4, NULL, 2, 'C5', 21.000, 'g', 1387812181, 1, 1387883558, 1387812135, '', 1, 0, 1, 0, 1, 1, 1, 127, NULL, NULL, 1, 1, 'C00000001_BER02_231213_003_04'),
(134, 94, 'DungNT2312-004', 1, 4, NULL, 2, 'C5', 21.000, 'g', 1387812181, 1, 1387883558, 1387812135, '', 1, 0, 1, 0, 1, 1, 1, 127, NULL, NULL, 1, 1, 'C00000001_BER02_231213_003_04'),
(135, 95, 'DungNT2312-005', 1, 5, NULL, 1, 'C5', 21.000, 'g', 1387883756, 1, 1387883786, 1387812146, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0, NULL, 1, 1, 'C00000001_BER01_231213_002_05'),
(136, 96, 'DungNT2312-006', 1, 5, NULL, 1, 'C5', 21.000, 'g', 1387883769, 1, 1387883791, 1387812200, NULL, 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, NULL, 1, 1, 'C00000001_BER01_231213_003_05'),
(137, 92, 'DungNT2312-002', 1, 1, NULL, 2, 'C4', 21.000, 'g', 1387812181, 1, 1387883816, 1387812104, '001', 1, 0, 0, 0, 1, 1, 1, 0, NULL, NULL, 1, 1, 'C00000001_BER02_231213_001_01'),
(138, 92, 'DungNT2312-002', 1, 4, NULL, 2, 'C4', 21.000, 'g', 1387812181, 1, 1387883863, 1387812104, '001', 1, 0, 0, 0, 1, 1, 1, 127, NULL, NULL, 1, 1, 'C00000001_BER02_231213_001_04'),
(139, 92, 'DungNT2312-002', 1, 4, NULL, 2, 'C4', 21.000, 'g', 1387812181, 1, 1387883863, 1387812104, '001', 1, 0, 0, 0, 1, 1, 1, 127, NULL, NULL, 1, 1, 'C00000001_BER02_231213_001_04'),
(140, 92, 'DungNT2312-002', 1, 4, NULL, 2, 'C4', 21.000, 'g', 1387812181, 1, 1387883863, 1387812104, '001', 1, 0, 0, 0, 1, 1, 1, 127, NULL, NULL, 1, 1, 'C00000001_BER02_231213_001_04'),
(141, 91, 'DungNT2312-001', 1, 5, NULL, 1, 'C4', 11.000, 'g', 1387900989, 1, 1387901099, 1387812089, NULL, 0, NULL, NULL, 0, NULL, NULL, 0, NULL, 0, NULL, 1, 1, 'C00000001_BER01_231213_001_05'),
(142, 97, 'christain', 1, 2, NULL, 1, 'C5', 23.000, 'g', 1387967830, 1, 1387967862, 1387967805, '', 1, NULL, NULL, 0, NULL, 1, NULL, NULL, NULL, NULL, 1, 1, 'C00000001_BER01_251213_001_02'),
(143, 105, 'DungNT1226-001', 1, 1, NULL, 2, 'C4', 11.000, 'g', 1388005360, 1, 1388005412, 1388005360, NULL, 0, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, NULL, 1, 1, 'C00000001_BER02_251213_002_01'),
(144, 105, 'DungNT1226-001', 1, 6, NULL, 2, 'C4', 11.000, 'g', 1388005431, 1, 1388007045, 1388005360, NULL, 0, NULL, NULL, 0, 1, 0, NULL, NULL, 0, NULL, 1, 1, 'C00000001_BER02_251213_002_06'),
(145, 97, 'christain', 1, 5, NULL, 1, 'C5', 23.000, 'g', 1387967880, 1, 1388007379, 1387967805, '', 1, NULL, NULL, 0, NULL, 1, NULL, NULL, 0, NULL, 1, 1, 'C00000001_BER01_251213_001_05'),
(146, 106, 'DungNT1226-002', 1, 2, NULL, 1, 'C4', 11.000, 'g', 1388008215, 1, 1388008277, 1388007942, '', 1, NULL, NULL, 0, NULL, 1, NULL, NULL, NULL, NULL, 1, 1, 'C00000001_BER01_251213_008_02'),
(147, 107, 'Micro', 1, 2, NULL, 1, 'C4', 32.000, 'g', 1388096353, 1, 1388096416, 1388096331, '', 1, NULL, NULL, 0, NULL, 1, NULL, NULL, NULL, NULL, 1, 1, 'C00000001_BER01_261213_001_02'),
(148, 106, 'DungNT1226-002', 1, 5, NULL, 1, 'C4', 11.000, 'g', 1388008310, 1, 1388128050, 1388007942, '', 1, NULL, NULL, 0, NULL, 1, NULL, NULL, 0, NULL, 1, 1, 'C00000001_BER01_251213_008_05'),
(149, 107, 'Micro', 1, 5, NULL, 1, 'C4', 32.000, 'g', 1388128059, 1, 1388128073, 1388096331, '', 1, NULL, NULL, 0, NULL, 1, NULL, NULL, 0, NULL, 1, 1, 'C00000001_BER01_261213_001_05'),
(150, 99, 'christian', 1, 2, NULL, 1, 'C4', 23.000, 'g', 1387969932, 1, 1388129327, 1387969557, '', 1, NULL, NULL, 0, NULL, 1, NULL, 0, NULL, NULL, 1, 1, 'C00000001_BER01_251213_003_02'),
(151, 100, 'Christain1', 1, 3, NULL, 1, 'C4', 23.000, 'g', 1387969925, 1, 1388129449, 1387969742, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, 1, 1, 'C00000001_BER01_251213_004_03'),
(152, 100, 'Christain1', 1, 3, NULL, 1, 'C4', 23.000, 'g', 1387969925, 1, 1388129449, 1387969742, NULL, 0, NULL, NULL, 0, NULL, NULL, 1, 0, NULL, NULL, 1, 1, 'C00000001_BER01_251213_004_03'),
(153, 100, 'Christain1', 1, 3, NULL, 1, 'C4', 23.000, 'g', 1387969925, 1, 1388129449, 1387969742, NULL, 0, 0, 0, 0, NULL, NULL, 1, 0, NULL, NULL, 1, 1, 'C00000001_BER01_251213_004_03'),
(154, 100, 'Christain1', 1, 3, NULL, 1, 'C4', 23.000, 'g', 1387969925, 1, 1388129450, 1387969742, NULL, 0, 0, 0, 0, NULL, NULL, 1, 0, NULL, NULL, 1, 1, 'C00000001_BER01_251213_004_03');

-- --------------------------------------------------------

--
-- Table structure for table `envelope_files`
--

DROP TABLE IF EXISTS `envelope_files`;
CREATE TABLE IF NOT EXISTS `envelope_files` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `envelope_files_uk` (`envelope_id`,`customer_id`,`type`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=109 ;

--
-- Dumping data for table `envelope_files`
--

INSERT INTO `envelope_files` (`id`, `envelope_id`, `customer_id`, `file_name`, `public_file_name`, `local_file_name`, `amazon_path`, `amazon_relate_path`, `file_size`, `created_date`, `type`, `updated_date`, `number_page`) VALUES
(100, 92, 1, 'http://dev.clevvermail.com/app/index.php/scans/todo/get_file_scan?envelope_id=92&type=2', 'http://dev.clevvermail.com/app/index.php/mailbox/get_file_scan?envelope_id=92&type=2', 'system/virtualpost/uploads/filescan/1/DC92_C1_F.pdf', 'http://clevvermail-dev.s3.amazonaws.com/1/DC92_C1_F.pdf', '1/DC92_C1_F.pdf', 165954, 1387814194, 2, NULL, 4),
(101, 94, 1, 'http://dev.clevvermail.com/app/index.php/scans/todo/get_file_scan?envelope_id=94&type=2', 'http://dev.clevvermail.com/app/index.php/mailbox/get_file_scan?envelope_id=94&type=2', 'system/virtualpost/uploads/filescan/1/DC94_C1_F.pdf', 'http://clevvermail-dev.s3.amazonaws.com/1/DC94_C1_F.pdf', '1/DC94_C1_F.pdf', 165954, 1387814270, 2, NULL, 4),
(102, 94, 1, 'http://dev.clevvermail.com/app/index.php/scans/todo/get_file_scan?envelope_id=94&type=1', 'http://dev.clevvermail.com/app/index.php/mailbox/get_file_scan?envelope_id=94&type=1', 'system/virtualpost/uploads/filescan/1/EC94_C1_F.png', 'http://clevvermail-dev.s3.amazonaws.com/1/EC94_C1_F.png', '1/EC94_C1_F.png', 779941, 1387883444, 1, NULL, 1),
(103, 92, 1, 'http://dev.clevvermail.com/app/index.php/scans/todo/get_file_scan?envelope_id=92&type=1', 'http://dev.clevvermail.com/app/index.php/mailbox/get_file_scan?envelope_id=92&type=1', 'system/virtualpost/uploads/filescan/1/EC92_C1_F.png', 'http://clevvermail-dev.s3.amazonaws.com/1/EC92_C1_F.png', '1/EC92_C1_F.png', 779941, 1387883812, 1, NULL, 1),
(104, 93, 1, 'http://dev.clevvermail.com/app/index.php/scans/todo/get_file_scan?envelope_id=93&type=1', 'http://dev.clevvermail.com/app/index.php/mailbox/get_file_scan?envelope_id=93&type=1', 'system/virtualpost/uploads/filescan/1/EC93_C1_F.png', 'http://clevvermail-dev.s3.amazonaws.com/1/EC93_C1_F.png', '1/EC93_C1_F.png', 779941, 1387883883, 1, NULL, 1),
(105, 105, 1, 'http://dev.clevvermail.com/app/index.php/scans/todo/get_file_scan?envelope_id=105&type=1', 'http://dev.clevvermail.com/app/index.php/mailbox/get_file_scan?envelope_id=105&type=1', 'system/virtualpost/uploads/filescan/1/EC105_C1_F.png', 'http://clevvermail-dev.s3.amazonaws.com/1/EC105_C1_F.png', '1/EC105_C1_F.png', 50630, 1388005406, 1, NULL, 1),
(106, 106, 1, 'http://dev.clevvermail.com/app/index.php/scans/todo/get_file_scan?envelope_id=106&type=2', 'http://dev.clevvermail.com/app/index.php/mailbox/get_file_scan?envelope_id=106&type=2', 'system/virtualpost/uploads/filescan/1/DC106_C1_F.pdf', 'http://clevvermail-dev.s3.amazonaws.com/1/DC106_C1_F.pdf', '1/DC106_C1_F.pdf', 131512, 1388008267, 2, NULL, 3),
(108, 99, 1, 'http://dev.clevvermail.com/app/index.php/scans/todo/get_file_scan?envelope_id=99&type=2', 'http://dev.clevvermail.com/app/index.php/mailbox/get_file_scan?envelope_id=99&type=2', 'system/virtualpost/uploads/filescan/1/DC99_C1_F.pdf', 'http://clevvermail-dev.s3.amazonaws.com/1/DC99_C1_F.pdf', '1/DC99_C1_F.pdf', 254351, 1388129323, 2, NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `envelope_package`
--

DROP TABLE IF EXISTS `envelope_package`;
CREATE TABLE IF NOT EXISTS `envelope_package` (
  `package_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) DEFAULT NULL,
  `postbox_id` bigint(20) DEFAULT NULL,
  `package_date` varchar(8) CHARACTER SET utf8 DEFAULT NULL,
  `package_price` double(20,0) DEFAULT NULL,
  PRIMARY KEY (`package_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `envelope_package`
--

INSERT INTO `envelope_package` (`package_id`, `customer_id`, `postbox_id`, `package_date`, `package_price`) VALUES
(3, 1, 2, '20131223', NULL),
(4, 1, 1, '20131225', NULL),
(5, 1, 2, '20131225', NULL),
(6, 1, 1, '20131227', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `envelope_shipping`
--

DROP TABLE IF EXISTS `envelope_shipping`;
CREATE TABLE IF NOT EXISTS `envelope_shipping` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

--
-- Dumping data for table `envelope_shipping`
--

INSERT INTO `envelope_shipping` (`id`, `envelope_id`, `customer_id`, `postbox_id`, `shipping_name`, `shipping_company`, `shipping_street`, `shipping_postcode`, `shipping_city`, `shipping_region`, `shipping_country`, `estamp_url`, `lable_size_id`, `package_letter_size`, `package_letter_size_id`, `printer_id`, `shipping_type_id`, `shipping_date`, `shipping_fee`) VALUES
(14, 92, 1, 2, 'Nguyen Trong Dung', 'VIB - Funnyfox', 'Ha Noi', '084', 'Ha Noi', 'Ha Noi', 'Viet Nam', 'http://dev.clevvermail.com/app/downloads/internetmarken/1387883512_fUNzWAKe99/0.png', 1, 'Letter size A, 20g, C5, 0.58 EUR', 1, 1, 3, 1387883536, 0.58),
(15, 93, 1, 2, 'Nguyen Trong Dung', 'VIB - Funnyfox', 'Ha Noi', '084', 'Ha Noi', 'Ha Noi', 'Viet Nam', 'http://dev.clevvermail.com/app/downloads/internetmarken/1387883512_fUNzWAKe99/0.png', 1, 'Letter size A, 20g, C5, 0.58 EUR', 1, 1, 3, 1387883536, 0.58),
(16, 94, 1, 2, 'Nguyen Trong Dung', 'VIB - Funnyfox', 'Ha Noi', '084', 'Ha Noi', 'Ha Noi', 'Viet Nam', 'http://dev.clevvermail.com/app/downloads/internetmarken/1387883512_fUNzWAKe99/0.png', 1, 'Letter size A, 20g, C5, 0.58 EUR', 1, 1, 3, 1387883536, 0.58),
(17, 92, 1, 2, 'Nguyen Trong Dung', 'VIB - Funnyfox', 'Ha Noi', '084', 'Ha Noi', 'Ha Noi', 'Viet Nam', '', 1, 'Letter size C, 100g, C3, 1.45EUR', 3, 1, 4, 1387883558, 1.45),
(18, 93, 1, 2, 'Nguyen Trong Dung', 'VIB - Funnyfox', 'Ha Noi', '084', 'Ha Noi', 'Ha Noi', 'Viet Nam', '', 1, 'Letter size C, 100g, C3, 1.45EUR', 3, 1, 4, 1387883558, 1.45),
(19, 94, 1, 2, 'Nguyen Trong Dung', 'VIB - Funnyfox', 'Ha Noi', '084', 'Ha Noi', 'Ha Noi', 'Viet Nam', '', 1, 'Letter size C, 100g, C3, 1.45EUR', 3, 1, 4, 1387883558, 1.45),
(20, 92, 1, 2, 'Nguyen Trong Dung', 'VIB - Funnyfox', 'Ha Noi', '084', 'Ha Noi', 'Ha Noi', 'Viet Nam', '', 1, '', 0, 1, 4, 1387883863, 1.95),
(21, 93, 1, 2, 'Nguyen Trong Dung', 'VIB - Funnyfox', 'Ha Noi', '084', 'Ha Noi', 'Ha Noi', 'Viet Nam', '', 1, '', 0, 1, 4, 1387883863, 1.95),
(22, 94, 1, 2, 'Nguyen Trong Dung', 'VIB - Funnyfox', 'Ha Noi', '084', 'Ha Noi', 'Ha Noi', 'Viet Nam', '', 1, '', 0, 1, 4, 1387883863, 1.95),
(23, 99, 1, 1, 'Nguyen Trong Dung', 'VIB - Funnyfox', 'Ha Noi', '084', 'Ha Noi', 'Ha Noi', 'Viet Nam', '', 1, 'Letter size D, 500g, C2, 2.45 EUR', 4, 1, 3, 1388129449, 2.45),
(24, 100, 1, 1, 'Nguyen Trong Dung', 'VIB - Funnyfox', 'Ha Noi', '084', 'Ha Noi', 'Ha Noi', 'Viet Nam', '', 1, 'Letter size D, 500g, C2, 2.45 EUR', 4, 1, 3, 1388129449, 2.45),
(25, 101, 1, 1, 'Nguyen Trong Dung', 'VIB - Funnyfox', 'Ha Noi', '084', 'Ha Noi', 'Ha Noi', 'Viet Nam', '', 1, 'Letter size D, 500g, C2, 2.45 EUR', 4, 1, 3, 1388129449, 2.45),
(26, 102, 1, 1, 'Nguyen Trong Dung', 'VIB - Funnyfox', 'Ha Noi', '084', 'Ha Noi', 'Ha Noi', 'Viet Nam', '', 1, 'Letter size D, 500g, C2, 2.45 EUR', 4, 1, 3, 1388129449, 2.45);

-- --------------------------------------------------------

--
-- Table structure for table `envelope_summary_month`
--

DROP TABLE IF EXISTS `envelope_summary_month`;
CREATE TABLE IF NOT EXISTS `envelope_summary_month` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `envelope_id` bigint(20) DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `postbox_id` bigint(20) DEFAULT NULL,
  `year` varchar(4) DEFAULT NULL,
  `month` varchar(4) DEFAULT NULL,
  `incomming_number` bigint(20) DEFAULT '0',
  `incomming_price` decimal(10,2) DEFAULT '0.00',
  `envelope_scan_number` bigint(20) DEFAULT '0',
  `envelope_scan_price` decimal(10,2) DEFAULT '0.00',
  `document_scan_number` bigint(20) DEFAULT '0',
  `document_scan_price` decimal(10,2) DEFAULT '0.00',
  `direct_shipping_number` bigint(20) DEFAULT '0',
  `direct_shipping_price` decimal(10,2) DEFAULT '0.00',
  `collect_shipping_number` bigint(20) DEFAULT NULL,
  `collect_shipping_price` decimal(10,2) DEFAULT NULL,
  `additional_incomming_flag` tinyint(4) DEFAULT '0',
  `additional_pages_scanning_number` bigint(20) DEFAULT NULL,
  `additional_pages_scanning_price` decimal(10,2) DEFAULT NULL,
  `total_pages_scanning_number` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `envelope_summary_month_uk` (`customer_id`,`postbox_id`,`year`,`month`,`envelope_id`) USING BTREE
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=137 ;

--
-- Dumping data for table `envelope_summary_month`
--

INSERT INTO `envelope_summary_month` (`id`, `envelope_id`, `customer_id`, `postbox_id`, `year`, `month`, `incomming_number`, `incomming_price`, `envelope_scan_number`, `envelope_scan_price`, `document_scan_number`, `document_scan_price`, `direct_shipping_number`, `direct_shipping_price`, `collect_shipping_number`, `collect_shipping_price`, `additional_incomming_flag`, `additional_pages_scanning_number`, `additional_pages_scanning_price`, `total_pages_scanning_number`) VALUES
(120, 91, 1, 1, '2013', '12', 1, 0.50, 0, 0.00, 0, 0.00, 0, 0.00, NULL, NULL, 1, NULL, NULL, NULL),
(121, 92, 1, 2, '2013', '12', 1, 0.50, 1, 0.00, 1, 1.00, 1, 1.70, NULL, NULL, 1, 0, 0.00, 4),
(122, 93, 1, 2, '2013', '12', 1, 0.50, 1, 0.00, 1, 1.00, 1, 1.70, NULL, NULL, 1, 0, 0.00, 0),
(123, 94, 1, 2, '2013', '12', 1, 0.50, 1, 0.00, 1, 1.00, 1, 1.70, NULL, NULL, 1, 0, 0.00, 4),
(124, 95, 1, 1, '2013', '12', 1, 0.50, 0, 0.00, 0, 0.00, 0, 0.00, NULL, NULL, 1, NULL, NULL, NULL),
(125, 96, 1, 1, '2013', '12', 1, 0.50, 0, 0.00, 0, 0.00, 0, 0.00, NULL, NULL, 1, NULL, NULL, NULL),
(126, 97, 1, 1, '2013', '12', 1, 0.50, 0, 0.00, 1, 1.00, 0, 0.00, NULL, NULL, 1, 0, 0.00, 0),
(127, 98, 1, 1, '2013', '12', 1, 0.50, 0, 0.00, 0, 0.00, 0, 0.00, NULL, NULL, 1, NULL, NULL, NULL),
(128, 99, 1, 1, '2013', '12', 1, 0.50, 0, 0.00, 1, 1.00, 1, 52.45, NULL, NULL, 1, 0, 0.00, 2),
(129, 100, 1, 1, '2013', '12', 1, 0.50, 0, 0.00, 0, 0.00, 1, 52.45, NULL, NULL, 1, NULL, NULL, NULL),
(130, 101, 1, 1, '2013', '12', 1, 0.50, 0, 0.00, 0, 0.00, 1, 52.45, NULL, NULL, 1, NULL, NULL, NULL),
(131, 102, 1, 1, '2013', '12', 1, 0.50, 0, 0.00, 0, 0.00, 1, 52.45, NULL, NULL, 1, NULL, NULL, NULL),
(132, 103, 1, 1, '2013', '12', 1, 0.50, 0, 0.00, 0, 0.00, 0, 0.00, NULL, NULL, 1, NULL, NULL, NULL),
(133, 104, 1, 2, '2013', '12', 1, 0.50, 1, 0.00, 0, 0.00, 0, 0.00, NULL, NULL, 1, NULL, NULL, NULL),
(134, 105, 1, 2, '2013', '12', 1, 0.50, 1, 0.00, 0, 0.00, 0, 0.00, NULL, NULL, 1, NULL, NULL, NULL),
(135, 106, 1, 1, '2013', '12', 1, 0.50, 0, 0.00, 1, 1.00, 0, 0.00, NULL, NULL, 1, 0, 0.00, 3),
(136, 107, 1, 1, '2013', '12', 1, 0.50, 0, 0.00, 1, 1.00, 0, 0.00, NULL, NULL, 1, 0, 0.00, 2);

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`) VALUES
(1, 'admin', 'administrator'),
(2, 'worker', 'worker user');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_detail`
--

DROP TABLE IF EXISTS `invoice_detail`;
CREATE TABLE IF NOT EXISTS `invoice_detail` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) DEFAULT NULL,
  `activity` varchar(255) DEFAULT NULL,
  `activity_date` varchar(8) DEFAULT NULL,
  `item_number` int(11) DEFAULT NULL,
  `unit_price` double DEFAULT NULL,
  `item_amount` double DEFAULT NULL,
  `unit` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=277 ;

--
-- Dumping data for table `invoice_detail`
--

INSERT INTO `invoice_detail` (`id`, `customer_id`, `activity`, `activity_date`, `item_number`, `unit_price`, `item_amount`, `unit`) VALUES
(237, 1, 'Incomming', '20131223', 1, 0.5, 0.5, '$'),
(238, 1, 'Incomming', '20131223', 1, 0.5, 0.5, '$'),
(239, 1, 'Incomming', '20131223', 1, 0.5, 0.5, '$'),
(240, 1, 'Incomming', '20131223', 1, 0.5, 0.5, '$'),
(241, 1, 'Incomming', '20131223', 1, 0.5, 0.5, '$'),
(242, 1, 'Incomming', '20131223', 1, 0.5, 0.5, '$'),
(243, 1, 'Scanning', '20131223', 1, 1, 1, '$'),
(244, 1, 'Scanning', '20131223', 1, 1, 1, '$'),
(245, 1, 'Envelope scanning', '20131224', 1, 0, 0, '$'),
(246, 1, 'Scanning', '20131224', 1, 1, 1, '$'),
(247, 1, 'Shipping&Handling', '20131224', 1, 1.696, 1.696, '$'),
(248, 1, 'Shipping&Handling', '20131224', 1, 1.696, 1.696, '$'),
(249, 1, 'Shipping&Handling', '20131224', 1, 1.696, 1.696, '$'),
(250, 1, 'Shipping&Handling', '20131224', 1, 1.696, 1.696, '$'),
(251, 1, 'Shipping&Handling', '20131224', 1, 1.696, 1.696, '$'),
(252, 1, 'Shipping&Handling', '20131224', 1, 1.696, 1.696, '$'),
(253, 1, 'Envelope scanning', '20131224', 1, 0, 0, '$'),
(254, 1, 'Shipping&Handling', '20131224', 1, 1.696, 1.696, '$'),
(255, 1, 'Shipping&Handling', '20131224', 1, 1.696, 1.696, '$'),
(256, 1, 'Shipping&Handling', '20131224', 1, 1.696, 1.696, '$'),
(257, 1, 'Incomming', '20131225', 1, 0.5, 0.5, '$'),
(258, 1, 'Scanning', '20131225', 1, 1, 1, '$'),
(259, 1, 'Incomming', '20131225', 1, 0.5, 0.5, '$'),
(260, 1, 'Incomming', '20131225', 1, 0.5, 0.5, '$'),
(261, 1, 'Incomming', '20131225', 1, 0.5, 0.5, '$'),
(262, 1, 'Incomming', '20131225', 1, 0.5, 0.5, '$'),
(263, 1, 'Incomming', '20131225', 1, 0.5, 0.5, '$'),
(264, 1, 'Incomming', '20131225', 1, 0.5, 0.5, '$'),
(265, 1, 'Incomming', '20131225', 1, 0.5, 0.5, '$'),
(266, 1, 'Incomming', '20131225', 1, 0.5, 0.5, '$'),
(267, 1, 'Envelope scanning', '20131225', 1, 0, 0, '$'),
(268, 1, 'Incomming', '20131225', 1, 0.5, 0.5, '$'),
(269, 1, 'Scanning', '20131225', 1, 1, 1, '$'),
(270, 1, 'Incomming', '20131226', 1, 0.5, 0.5, '$'),
(271, 1, 'Scanning', '20131226', 1, 1, 1, '$'),
(272, 1, 'Scanning', '20131227', 1, 1, 1, '$'),
(273, 1, 'Shipping&Handling', '20131227', 1, 52.45, 52.45, '$'),
(274, 1, 'Shipping&Handling', '20131227', 1, 52.45, 52.45, '$'),
(275, 1, 'Shipping&Handling', '20131227', 1, 52.45, 52.45, '$'),
(276, 1, 'Shipping&Handling', '20131227', 1, 52.45, 52.45, '$');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_summary`
--

DROP TABLE IF EXISTS `invoice_summary`;
CREATE TABLE IF NOT EXISTS `invoice_summary` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) DEFAULT NULL,
  `invoice_month` varchar(6) DEFAULT NULL,
  `private_postboxes_amount` double DEFAULT NULL,
  `business_postboxes_amount` double DEFAULT NULL,
  `incomming_items_free_account` double DEFAULT NULL,
  `incomming_items_private_account` double DEFAULT NULL,
  `incomming_items_business_account` double DEFAULT NULL,
  `envelope_scan_free_account` double DEFAULT NULL,
  `envelope_scan_private_account` double DEFAULT NULL,
  `envelope_scan_business_account` double DEFAULT NULL,
  `item_scan_free_account` double DEFAULT NULL,
  `item_scan_private_account` double DEFAULT NULL,
  `item_scan_business_account` double DEFAULT NULL,
  `additional_pages_scanning` double DEFAULT NULL,
  `direct_shipping_free_account` double DEFAULT NULL,
  `direct_shipping_private_account` double DEFAULT NULL,
  `direct_shipping_business_account` double DEFAULT NULL,
  `collect_shipping_free_account` double DEFAULT NULL,
  `collect_shipping_private_account` double DEFAULT NULL,
  `collect_shipping_business_account` double DEFAULT NULL,
  `storing_letters_free_account` double DEFAULT NULL,
  `storing_letters_private_account` double DEFAULT NULL,
  `storing_letters_business_account` double DEFAULT NULL,
  `storing_packages_free_account` double DEFAULT NULL,
  `storing_packages_private_account` double DEFAULT NULL,
  `storing_packages_business_account` double DEFAULT NULL,
  `additional_private_postbox_amount` double DEFAULT NULL,
  `additional_business_postbox_amount` double DEFAULT NULL,
  `invoice_flag` tinyint(4) DEFAULT '0' COMMENT '1: La da thanh toan (se khong thong ke de thanh toan lai) |  0: La doi tuong thanh toan',
  `payment_1st_flag` tinyint(4) DEFAULT NULL,
  `payment_2st_flag` tinyint(4) DEFAULT NULL,
  `private_postboxes_quantity` int(11) DEFAULT NULL,
  `private_postboxes_netprice` double DEFAULT NULL,
  `business_postboxes_quantity` int(11) DEFAULT NULL,
  `business_postboxes_netprice` double DEFAULT NULL,
  `incomming_items_free_quantity` int(11) DEFAULT NULL,
  `incomming_items_free_netprice` double DEFAULT NULL,
  `incomming_items_private_quantity` int(11) DEFAULT NULL,
  `incomming_items_private_netprice` double DEFAULT NULL,
  `incomming_items_business_quantity` int(11) DEFAULT NULL,
  `incomming_items_business_netprice` double DEFAULT NULL,
  `envelope_scan_free_quantity` int(11) DEFAULT NULL,
  `envelope_scan_free_netprice` double DEFAULT NULL,
  `envelope_scan_private_quantity` int(11) DEFAULT NULL,
  `envelope_scan_private_netprice` double DEFAULT NULL,
  `envelope_scan_business_quantity` int(11) DEFAULT NULL,
  `envelope_scan_business_netprice` double DEFAULT NULL,
  `item_scan_free_quantity` int(11) DEFAULT NULL,
  `item_scan_free_netprice` double DEFAULT NULL,
  `item_scan_private_quantity` int(11) DEFAULT NULL,
  `item_scan_private_netprice` double DEFAULT NULL,
  `item_scan_business_quantity` int(11) DEFAULT NULL,
  `item_scan_business_netprice` double DEFAULT NULL,
  `additional_pages_scanning_quantity` int(11) DEFAULT NULL,
  `additional_pages_scanning_netprice` double DEFAULT NULL,
  `direct_shipping_free_quantity` int(11) DEFAULT NULL,
  `direct_shipping_free_netprice` double DEFAULT NULL,
  `direct_shipping_private_quantity` int(11) DEFAULT NULL,
  `direct_shipping_private_netprice` double DEFAULT NULL,
  `direct_shipping_business_quantity` int(11) DEFAULT NULL,
  `direct_shipping_business_netprice` double DEFAULT NULL,
  `collect_shipping_free_quantity` int(11) DEFAULT NULL,
  `collect_shipping_free_netprice` double DEFAULT NULL,
  `collect_shipping_private_quantity` int(11) DEFAULT NULL,
  `collect_shipping_private_netprice` double DEFAULT NULL,
  `collect_shipping_business_quantity` int(11) DEFAULT NULL,
  `collect_shipping_business_netprice` double DEFAULT NULL,
  `storing_letters_free_quantity` int(11) DEFAULT NULL,
  `storing_letters_free_netprice` double DEFAULT NULL,
  `storing_letters_private_quantity` int(11) DEFAULT NULL,
  `storing_letters_private_netprice` double DEFAULT NULL,
  `storing_letters_business_quantity` int(11) DEFAULT NULL,
  `storing_letters_business_netprice` double DEFAULT NULL,
  `storing_packages_free_quantity` int(11) DEFAULT NULL,
  `storing_packages_free_netprice` double DEFAULT NULL,
  `storing_packages_private_quantity` int(11) DEFAULT NULL,
  `storing_packages_private_netprice` double DEFAULT NULL,
  `storing_packages_business_quantity` int(11) DEFAULT NULL,
  `storing_packages_business_netprice` double DEFAULT NULL,
  `additional_private_postbox_quantity` int(11) DEFAULT NULL,
  `additional_private_postbox_netprice` double DEFAULT NULL,
  `additional_business_postbox_quantity` int(11) DEFAULT NULL,
  `additional_business_postbox_netprice` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=107 ;

--
-- Dumping data for table `invoice_summary`
--

INSERT INTO `invoice_summary` (`id`, `customer_id`, `invoice_month`, `private_postboxes_amount`, `business_postboxes_amount`, `incomming_items_free_account`, `incomming_items_private_account`, `incomming_items_business_account`, `envelope_scan_free_account`, `envelope_scan_private_account`, `envelope_scan_business_account`, `item_scan_free_account`, `item_scan_private_account`, `item_scan_business_account`, `additional_pages_scanning`, `direct_shipping_free_account`, `direct_shipping_private_account`, `direct_shipping_business_account`, `collect_shipping_free_account`, `collect_shipping_private_account`, `collect_shipping_business_account`, `storing_letters_free_account`, `storing_letters_private_account`, `storing_letters_business_account`, `storing_packages_free_account`, `storing_packages_private_account`, `storing_packages_business_account`, `additional_private_postbox_amount`, `additional_business_postbox_amount`, `invoice_flag`, `payment_1st_flag`, `payment_2st_flag`, `private_postboxes_quantity`, `private_postboxes_netprice`, `business_postboxes_quantity`, `business_postboxes_netprice`, `incomming_items_free_quantity`, `incomming_items_free_netprice`, `incomming_items_private_quantity`, `incomming_items_private_netprice`, `incomming_items_business_quantity`, `incomming_items_business_netprice`, `envelope_scan_free_quantity`, `envelope_scan_free_netprice`, `envelope_scan_private_quantity`, `envelope_scan_private_netprice`, `envelope_scan_business_quantity`, `envelope_scan_business_netprice`, `item_scan_free_quantity`, `item_scan_free_netprice`, `item_scan_private_quantity`, `item_scan_private_netprice`, `item_scan_business_quantity`, `item_scan_business_netprice`, `additional_pages_scanning_quantity`, `additional_pages_scanning_netprice`, `direct_shipping_free_quantity`, `direct_shipping_free_netprice`, `direct_shipping_private_quantity`, `direct_shipping_private_netprice`, `direct_shipping_business_quantity`, `direct_shipping_business_netprice`, `collect_shipping_free_quantity`, `collect_shipping_free_netprice`, `collect_shipping_private_quantity`, `collect_shipping_private_netprice`, `collect_shipping_business_quantity`, `collect_shipping_business_netprice`, `storing_letters_free_quantity`, `storing_letters_free_netprice`, `storing_letters_private_quantity`, `storing_letters_private_netprice`, `storing_letters_business_quantity`, `storing_letters_business_netprice`, `storing_packages_free_quantity`, `storing_packages_free_netprice`, `storing_packages_private_quantity`, `storing_packages_private_netprice`, `storing_packages_business_quantity`, `storing_packages_business_netprice`, `additional_private_postbox_quantity`, `additional_private_postbox_netprice`, `additional_business_postbox_quantity`, `additional_business_postbox_netprice`) VALUES
(93, 1, '201312', NULL, NULL, 8.5, NULL, NULL, 0, NULL, NULL, 7, NULL, NULL, 0, 214.9, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 17, 0.5, 0, 0, 0, 0, 5, 0, 0, 0, 0, 0, 7, 0, 0, 0, 0, 0, 0, NULL, 7, 0, 0, 0, 0, 0, NULL, NULL, 0, 0, 0, 0, NULL, 0.05, NULL, NULL, NULL, NULL, NULL, 0.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(94, 54, '201312', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.05, NULL, NULL, NULL, NULL, NULL, 0.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(95, 81, '201312', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.05, NULL, NULL, NULL, NULL, NULL, 0.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(96, 82, '201312', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.05, NULL, NULL, NULL, NULL, NULL, 0.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(97, 83, '201312', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.05, NULL, NULL, NULL, NULL, NULL, 0.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(98, 84, '201312', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.05, NULL, NULL, NULL, NULL, NULL, 0.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(99, 85, '201312', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, 0, 0, 0, NULL, NULL, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.05, NULL, NULL, NULL, NULL, NULL, 0.2, NULL, NULL, NULL, NULL, 1, 0, 1, 0),
(100, 86, '201312', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.05, NULL, NULL, NULL, NULL, NULL, 0.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(101, 87, '201312', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.05, NULL, NULL, NULL, NULL, NULL, 0.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(102, 88, '201312', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.05, NULL, NULL, NULL, NULL, NULL, 0.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(103, 89, '201312', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.05, NULL, NULL, NULL, NULL, NULL, 0.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(104, 90, '201312', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.05, NULL, NULL, NULL, NULL, NULL, 0.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(105, 91, '201312', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.05, NULL, NULL, NULL, NULL, NULL, 0.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(106, 92, '201312', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.05, NULL, NULL, NULL, NULL, NULL, 0.2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

DROP TABLE IF EXISTS `location`;
CREATE TABLE IF NOT EXISTS `location` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `location_name` varchar(60) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `postcode` varchar(10) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`id`, `location_name`, `street`, `postcode`, `city`, `region`, `country`, `image_path`) VALUES
(1, 'Berlin', 'Musterstraße 13', '10025', 'Berlin', 'Berlin', 'Germany', NULL),
(2, 'New York', 'Broadway', '10555', 'New York', 'NY', 'USA', NULL),
(3, 'Barcelona', 'Rue 2', '64545', 'Barcelona', 'Bask', 'Spain', NULL),
(4, 'London', 'Buckingham Road', '3KH 43K', 'London', 'London', 'United Kingdom', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;
CREATE TABLE IF NOT EXISTS `modules` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PACK_KEYS=0 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `package_prices`
--

DROP TABLE IF EXISTS `package_prices`;
CREATE TABLE IF NOT EXISTS `package_prices` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `weight` int(11) DEFAULT NULL,
  `weight_unit` varchar(3) DEFAULT NULL,
  `size` varchar(255) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `price_unit` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `package_prices`
--

INSERT INTO `package_prices` (`id`, `name`, `weight`, `weight_unit`, `size`, `price`, `price_unit`) VALUES
(1, 'Letter size A', 20, 'g', 'C5', 0.58, 'EUR'),
(2, 'Letter size B', 40, 'g', 'C4', 0.95, 'EUR'),
(3, 'Letter size C', 100, 'g', 'C3', 1.45, 'EUR'),
(4, 'Letter size D', 500, 'g', 'C2', 2.45, 'EUR'),
(5, 'Large letter size A', 500, 'g', '20cmx20cmx4cmx', 4.9, 'EUR'),
(6, 'Large letter size B', 1000, 'g', '30cmx20cmx10cmx', 6.9, 'EUR'),
(7, 'Large letter size C', 5000, 'g', '30cmx40cmx40cmx', 9.9, 'EUR');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
CREATE TABLE IF NOT EXISTS `payment` (
  `payment_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `account_type` varchar(30) DEFAULT NULL,
  `card_type` varchar(255) DEFAULT NULL,
  `card_number` varchar(255) DEFAULT NULL,
  `card_name` varchar(255) DEFAULT NULL,
  `cvc` varchar(3) DEFAULT NULL,
  `expired_year` varchar(2) DEFAULT NULL,
  `expired_month` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`payment_id`),
  UNIQUE KEY `payment_id` (`payment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `customer_id`, `account_type`, `card_type`, `card_number`, `card_name`, `cvc`, `expired_year`, `expired_month`) VALUES
(5, 1, '30', 'V', '4111111111111111', 'Nguyen Trong Dung', '123', '13', '12'),
(6, 1, '30', 'M', '5500000000000004\r', 'Nguyen Trong Dung 02', '123', '14', '12'),
(7, 3, '30', 'V', '4012001037141112', 'Nguyen', '123', '13', '12'),
(8, 64, '30', 'M', '5486225329055904', 'Christian Hemmrich', '738', '17', '01'),
(9, 62, '30', 'M', '5500000000000004', 'DungNT', '123', '14', '12'),
(10, 41, '30', 'M', '5500000000000004', 'XXX', '123', '14', '12');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` bigint(20) DEFAULT NULL,
  `module_name` varchar(100) DEFAULT NULL,
  `role` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `postbox`
--

DROP TABLE IF EXISTS `postbox`;
CREATE TABLE IF NOT EXISTS `postbox` (
  `postbox_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `postbox_code` varchar(20) DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
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
  PRIMARY KEY (`postbox_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=79 ;

--
-- Dumping data for table `postbox`
--

INSERT INTO `postbox` (`postbox_id`, `postbox_code`, `customer_id`, `postbox_name`, `location_available_id`, `type`, `name`, `company`, `deleted`, `is_main_postbox`, `plan_deleted_date`, `updated_date`, `apply_date`, `new_postbox_type`, `plan_date_change_postbox_type`) VALUES
(1, 'C00000001_BER01', 1, 'Cust 1 - Post box 1 ', 1, 1, 'Cust 1 ', 'USOL-V', 0, 1, NULL, 1380668821, NULL, 1, '20131001'),
(2, 'C00000001_BER02', 1, 'Cust 1 - Post box Q', 1, 1, 'Cust 1 ', 'USOL-V', 0, 0, NULL, 1380668821, NULL, 1, '20131001'),
(39, 'C00000054_BER01', 54, 'asdfgasdfg', 1, 1, 'afsgasfg', 'asfgafsg', 0, 1, NULL, NULL, NULL, NULL, NULL),
(66, NULL, 81, '', NULL, 1, '', '', 0, 1, NULL, NULL, NULL, NULL, NULL),
(67, NULL, 82, '', NULL, 1, '', '', 0, 1, NULL, NULL, NULL, NULL, NULL),
(68, NULL, 83, '', NULL, 1, '', '', 0, 1, NULL, NULL, NULL, NULL, NULL),
(69, NULL, 84, '', NULL, 1, '', '', 0, 1, NULL, NULL, NULL, NULL, NULL),
(70, NULL, 85, 'test', 4, 1, 'test1', 'test2', 0, 1, NULL, NULL, NULL, NULL, NULL),
(71, NULL, 86, 'test', 4, 1, 'test', 'test', 0, 1, NULL, NULL, NULL, NULL, NULL),
(72, NULL, 87, '', NULL, 1, '', '', 0, 1, NULL, NULL, NULL, NULL, NULL),
(73, NULL, 88, 'test', 1, 1, 'tesdst', 'test', 0, 1, NULL, NULL, NULL, NULL, NULL),
(74, NULL, 89, '', NULL, 1, '', '', 0, 1, NULL, NULL, NULL, NULL, NULL),
(75, NULL, 90, '', NULL, 1, '', '', 0, 1, NULL, NULL, NULL, NULL, NULL),
(76, NULL, 91, '', NULL, 1, '', '', 0, 1, NULL, NULL, NULL, NULL, NULL),
(77, 'C00000085_NEW01', 85, 'demo7', 2, 1, 'tester', 'testhub', 0, NULL, NULL, NULL, NULL, NULL, NULL),
(78, NULL, 92, '', NULL, 1, '', '', 0, 1, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `postbox_fee_month`
--

DROP TABLE IF EXISTS `postbox_fee_month`;
CREATE TABLE IF NOT EXISTS `postbox_fee_month` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `postbox_id` bigint(20) DEFAULT '0',
  `year_month` varchar(6) DEFAULT NULL,
  `postbox_fee_flag` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `postbox_fee_month_uk` (`postbox_id`,`year_month`,`postbox_fee_flag`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `postbox_fee_month`
--

INSERT INTO `postbox_fee_month` (`id`, `postbox_id`, `year_month`, `postbox_fee_flag`) VALUES
(8, 1, '201312', '1'),
(9, 70, '201312', '1');

-- --------------------------------------------------------

--
-- Table structure for table `postbox_settings`
--

DROP TABLE IF EXISTS `postbox_settings`;
CREATE TABLE IF NOT EXISTS `postbox_settings` (
  `postbox_id` bigint(20) NOT NULL DEFAULT '0',
  `customer_id` bigint(20) DEFAULT NULL,
  `always_scan_envelope` tinyint(4) DEFAULT NULL,
  `always_scan_envelope_vol_avail` tinyint(4) DEFAULT NULL,
  `always_scan_incomming` tinyint(4) DEFAULT NULL,
  `always_scan_incomming_vol_avail` tinyint(4) DEFAULT NULL,
  `email_notification` tinyint(4) DEFAULT NULL,
  `invoicing_cycle` tinyint(4) DEFAULT NULL,
  `collect_mail_cycle` tinyint(4) DEFAULT NULL,
  `weekday_shipping` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`postbox_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `postbox_settings`
--

INSERT INTO `postbox_settings` (`postbox_id`, `customer_id`, `always_scan_envelope`, `always_scan_envelope_vol_avail`, `always_scan_incomming`, `always_scan_incomming_vol_avail`, `email_notification`, `invoicing_cycle`, `collect_mail_cycle`, `weekday_shipping`) VALUES
(1, 1, 0, 0, 0, 0, 1, 1, 1, 4),
(2, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(40, 55, 0, 0, 0, 0, 1, 1, 2, 1),
(70, 85, 0, 0, 0, 0, 1, 1, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `pricing`
--

DROP TABLE IF EXISTS `pricing`;
CREATE TABLE IF NOT EXISTS `pricing` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account_type` tinyint(4) DEFAULT NULL COMMENT 'Reference: 000033',
  `item_name` varchar(250) DEFAULT NULL,
  `item_value` varchar(250) DEFAULT NULL,
  `item_description` varchar(255) DEFAULT NULL,
  `item_unit` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=73 ;

--
-- Dumping data for table `pricing`
--

INSERT INTO `pricing` (`id`, `account_type`, `item_name`, `item_value`, `item_description`, `item_unit`) VALUES
(1, 1, 'address_number', '1', 'address', 'numbers'),
(2, 1, 'included_incomming_items', '0', 'included incomming items', NULL),
(3, 1, 'storage', '1', 'storage', 'GB'),
(4, 1, 'hand_sorting_of_advertising', '0', 'hand sorting of advertising', NULL),
(5, 1, 'envelope_scanning_front', '5', 'envelope scanning (front)', NULL),
(6, 1, 'included_opening_scanning', '0', 'included opening and scanning', NULL),
(7, 1, 'storing_items_letters', '4', 'storing items (letters)', 'weeks'),
(8, 1, 'storing_items_packages', '1', 'storing items (packages)', 'week'),
(9, 1, 'storing_items_digitally', '1', 'storing items digitally', 'year'),
(10, 1, 'trashing_items', '-1', 'trashing items', 'unlimited'),
(11, 1, 'cloud_service_connection', 'included', 'cloud service connection', 'included'),
(12, 1, 'additional_incomming_items', '0.5', 'additional incomming items', 'EUR'),
(13, 1, 'envelop_scanning', '0.2', 'envelop scanning', 'EUR'),
(14, 1, 'opening_scanning', '1', 'opening and scanning', NULL),
(15, 1, 'send_out_directly', '1', 'send out to original address directly', NULL),
(16, 1, 'send_out_collected', '2', 'send out to original address collected', NULL),
(17, 1, 'storing_items_over_free_letter', '0.05', 'storing items over free period (letters)', 'day'),
(18, 1, 'storing_items_over_free_packages', '0.2', 'storing items over free period (packages)', NULL),
(19, 1, 'additional_private_mailbox', '0', 'additional private mailbox', NULL),
(20, 1, 'additional_business_mailbox', '0', 'additional business mailbox ', NULL),
(21, 2, 'address_number', '1', 'address', 'numbers'),
(22, 2, 'included_incomming_items', '10', 'included incomming items', NULL),
(23, 2, 'storage', '0', 'storage', 'GB'),
(24, 2, 'hand_sorting_of_advertising', '-1', 'hand sorting of advertising', NULL),
(25, 2, 'envelope_scanning_front', '10', 'envelope scanning (front)', NULL),
(26, 2, 'included_opening_scanning', '5', 'included opening and scanning', NULL),
(27, 2, 'storing_items_letters', '4', 'storing items (letters)', 'weeks'),
(28, 2, 'storing_items_packages', '1', 'storing items (packages)', 'week'),
(29, 2, 'storing_items_digitally', '1', 'storing items digitally', 'year'),
(30, 2, 'trashing_items', '-1', 'trashing items', 'unlimited'),
(31, 2, 'cloud_service_connection', 'included', 'cloud service connection', 'included'),
(32, 2, 'additional_incomming_items', '0.3', 'additional incomming items', 'EUR'),
(33, 2, 'envelop_scanning', '0.1', 'envelop scanning', 'EUR'),
(34, 2, 'opening_scanning', '0.5', 'opening and scanning', NULL),
(35, 2, 'send_out_directly', '0.5', 'send out to original address directly', NULL),
(36, 2, 'send_out_collected', '1', 'send out to original address collected', NULL),
(37, 2, 'storing_items_over_free_letter', '0.04', 'storing items over free period (letters)', 'day'),
(38, 2, 'storing_items_over_free_packages', '0.15', 'storing items over free period (packages)', NULL),
(39, 2, 'additional_private_mailbox', '4.95', 'additional private mailbox', NULL),
(40, 2, 'additional_business_mailbox', '9.95', 'additional business mailbox ', NULL),
(41, 3, 'address_number', '1', 'address', 'numbers'),
(42, 3, 'included_incomming_items', '50', 'included incomming items', NULL),
(43, 3, 'storage', '0', 'storage', 'GB'),
(44, 3, 'hand_sorting_of_advertising', '-1', 'hand sorting of advertising', NULL),
(45, 3, 'envelope_scanning_front', '50', 'envelope scanning (front)', NULL),
(46, 3, 'included_opening_scanning', '10', 'included opening and scanning', NULL),
(47, 3, 'storing_items_letters', '4', 'storing items (letters)', 'weeks'),
(48, 3, 'storing_items_packages', '1', 'storing items (packages)', 'week'),
(49, 3, 'storing_items_digitally', '1', 'storing items digitally', 'year'),
(50, 3, 'trashing_items', '-1', 'trashing items', 'unlimited'),
(51, 3, 'cloud_service_connection', 'included', 'cloud service connection', 'included'),
(52, 3, 'additional_incomming_items', '0.2', 'additional incomming items', 'EUR'),
(53, 3, 'envelop_scanning', '0.05', 'envelop scanning', 'EUR'),
(54, 3, 'opening_scanning', '0.4', 'opening and scanning', NULL),
(55, 3, 'send_out_directly', '0.4', 'send out to original address directly', NULL),
(56, 3, 'send_out_collected', '0.8', 'send out to original address collected', NULL),
(57, 3, 'storing_items_over_free_letter', '0.03', 'storing items over free period (letters)', 'EUR/day'),
(58, 3, 'storing_items_over_free_packages', '0.1', 'storing items over free period (packages)', 'EUR/day'),
(59, 3, 'additional_private_mailbox', '4,95', 'additional private mailbox', NULL),
(60, 3, 'additional_business_mailbox', '9,95', 'additional business mailbox ', NULL),
(61, 1, 'postbox_fee', '0', 'fee for first postbox', 'EUR'),
(62, 2, 'postbox_fee', '4.95', 'postbox fee for first', 'EUR'),
(63, 3, 'postbox_fee', '9.95', 'Postbox fee for first', 'EUR'),
(64, 1, 'additional_pages_scanning_price', '0.084', 'Additional pages scanning / 1 page', 'EUR'),
(65, 2, 'additional_pages_scanning_price', '0.084', 'Additional pages scanning / 1 page', 'EUR'),
(66, 3, 'additional_pages_scanning_price', '0.084', 'Additional pages scanning / 1 page', 'EUR'),
(67, 1, 'include_pages_scanning_number', '10', 'Include pages scanning number', 'numbers'),
(68, 2, 'include_pages_scanning_number', '10', 'Include pages scanning number', 'numbers'),
(69, 3, 'include_pages_scanning_number', '10', 'Include pages scanning number', 'numbers'),
(70, 1, 'shipping_plus', '20', 'shipping plus', '%'),
(71, 2, 'shipping_plus', '20', 'shipping plus', '%'),
(72, 3, 'shipping_plus', '20', 'shipping plus', '%');

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

DROP TABLE IF EXISTS `profiles`;
CREATE TABLE IF NOT EXISTS `profiles` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`id`, `user_id`, `display_name`, `first_name`, `last_name`, `company`, `lang`, `bio`, `dob`, `gender`, `phone`, `mobile`, `address_line1`, `address_line2`, `address_line3`, `postcode`, `website`, `updated_on`, `updated`, `created`, `created_by`, `ordering_count`) VALUES
(1, 1, 'Nguyen Dung', 'Nguyen', 'Dung', '', 'en', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=91 ;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`SettingKey`, `SettingCode`, `DefaultValue`, `ActualValue`, `LabelValue`, `ModuleName`, `SettingOrder`, `IsRequired`, `description`) VALUES
(1, '000001', '10', '10', NULL, NULL, 1, 1, 'Record Per Page'),
(2, '000002', '10,20,50,100', '10,20,50,100', NULL, NULL, 1, 1, 'List items per page'),
(3, '000003', '1', '1', 'Active', NULL, 1, NULL, 'Status'),
(4, '000003', '0', '0', 'UnActive', NULL, 2, NULL, 'Status'),
(5, '000004', 'new_admin2', 'new_admin2', 'Administrator themes', NULL, 1, NULL, 'Administrator themes'),
(6, '000005', 'new_user2', 'new_user2', 'Fronend themes', NULL, 1, NULL, 'Frontend themes'),
(8, '000006', 'SMTP', 'SMTP', 'SMTP', NULL, 1, NULL, 'MAIL_PROTOCOL'),
(10, '000007', '/usr/sbin/sendmail', 'ssl://smtp.googlemail.com', '/usr/sbin/sendmail', NULL, 1, NULL, 'MAIL_SENDMAIL_PATH'),
(11, '000008', 'MAIL_SMTP_HOST', 'ssl://smtp.googlemail.com', 'MAIL_SMTP_HOST', NULL, 1, NULL, 'MAIL_SMTP_HOST'),
(12, '000009', 'relation.test.02@gmail.com', 'relation.test.02@gmail.com', 'relation.test.02@gmail.com', NULL, 1, NULL, 'MAIL_SMTP_USER'),
(14, '000010', 'relation@123', 'relation@123', 'relation@123', NULL, 1, NULL, 'MAIL_SMTP_PASS'),
(15, '000011', '465', '465', '465', NULL, 1, NULL, 'MAIL_SMTP_PORT'),
(16, '000012', 'ClevverMail', 'ClevverMail', 'ClevverMail', NULL, 1, NULL, 'MAIL_ALIAS_NAME'),
(17, '000013', 'register@clevvermail.com', 'register@clevvermail.com', 'register@clevvermail.com', NULL, 1, NULL, 'CONTACT_EMAIL'),
(18, '000014', 'admin@localhost', 'admin@localhost12', 'admin@localhost', NULL, 1, NULL, 'MAIL_SERVER'),
(19, '000015', 'Un-named Website', 'ClevverMail', 'Un-named Website', NULL, 1, NULL, 'Site Name '),
(20, '000016', 'Add your slogan here', 'Add your slogan here', 'Add your slogan here', NULL, 1, NULL, 'Site Slogan '),
(21, '000017', 'Y-m-d', 'Y-m-d', 'Y-m-d', NULL, 1, NULL, 'Date Format'),
(22, '000018', '$', '$', '$', NULL, 1, NULL, 'Currency'),
(23, '000019', '1', '1', '1', NULL, 1, NULL, 'Site Status'),
(25, '000020', 'Sorry, this website is currently unavailable.', 'Sorry, this website is currently unavailable.', 'Sorry, this website is currently unavailable.', NULL, 1, NULL, 'Unavailable Message'),
(26, '000021', 'PAYMENT_PAYPAL_USERNAME_CODE', 'PAYMENT_PAYPAL_USERNAME_CODE', 'PAYMENT_PAYPAL_USERNAME_CODE', NULL, 1, NULL, 'PAYMENT_PAYPAL_USERNAME_CODE'),
(27, '000022', 'PAYMENT_PAYPAL_PASSWORD', 'PAYMENT_PAYPAL_PASSWORD', 'PAYMENT_PAYPAL_PASSWORD', NULL, 1, NULL, 'PAYMENT_PAYPAL_PASSWORD'),
(28, '000023', 'PAYMENT_PAYPAL_SIGNATURE', 'PAYMENT_PAYPAL_SIGNATURE', 'PAYMENT_PAYPAL_SIGNATURE', NULL, 1, NULL, 'PAYMENT_PAYPAL_SIGNATURE'),
(29, '000024', 'PAYMENT_EWAY_CUSTOMERID_CODE', 'PAYMENT_EWAY_CUSTOMERID_CODE', NULL, NULL, NULL, NULL, 'PAYMENT_EWAY_CUSTOMERID_CODE'),
(30, '000025', 'C5', 'C5', 'C5', NULL, 3, NULL, 'Envelop Type'),
(31, '000025', 'C4', 'C4', 'C4', NULL, 2, NULL, 'Envelop Type'),
(32, '000026', NULL, '001', 'Insurances', NULL, 1, NULL, 'Category Type'),
(33, '000026', NULL, '002', 'Invoices', NULL, 2, NULL, 'Category Type'),
(34, '000026', NULL, '003', 'Business', NULL, 3, NULL, 'Category Type'),
(35, '000026', NULL, '004', 'Memberships', NULL, 4, NULL, 'Category Type'),
(36, '000026', NULL, '005', 'Private', NULL, 5, NULL, 'Category Type'),
(37, '000025', 'C3', 'C3', 'C3', NULL, 1, NULL, 'Envelop Type'),
(38, '000027', 'INV', '1', 'Immediately', NULL, 1, NULL, 'E-Mail notiﬁcation for incomming'),
(39, '000027', 'CRD', '2', 'Daily', NULL, 2, NULL, 'E-Mail notiﬁcation for incomming'),
(40, '000027', '1', '3', 'Weekly', NULL, 3, NULL, 'E-Mail notiﬁcation for incomming'),
(41, '000027', '2', '4', 'Monthly', NULL, 4, NULL, 'E-Mail notiﬁcation for incomming'),
(42, '000027', '3', '5', 'None', NULL, 5, NULL, 'E-Mail notiﬁcation for incomming'),
(43, '000028', '1', '1', 'Monthly', NULL, 1, NULL, 'Invoicing cycle '),
(44, '000028', '2', '2', 'Quarterly ', NULL, 2, NULL, 'Invoicing cycle '),
(46, '000029', '1', '1', 'Daily', NULL, 1, NULL, 'Collect items for shipping\r\nCollect items for shipping\r\nCollect items for shipping'),
(47, '000029', '2', '2', 'Weekly', NULL, 2, NULL, 'Collect items for shipping'),
(48, '000029', '3', '3', 'Monthly', NULL, 3, NULL, 'Collect items for shipping'),
(49, '000029', '4', '4', 'Quarterly ', NULL, 4, NULL, 'Collect items for shipping'),
(57, '000033', '1', '1', 'Free', NULL, NULL, NULL, 'Account type (1: Free | 2: Private | 3: Business)'),
(58, '000033', '2', '2', 'Private', NULL, NULL, NULL, 'Account type (1: Free | 2: Private | 3: Business)'),
(59, '000033', '3', '3', 'Business', NULL, NULL, NULL, 'Account type (1: Free | 2: Private | 3: Business)'),
(60, '000034', '1', '1', 'Shipping Type 1', NULL, 1, NULL, 'Shipping Type'),
(61, '000034', '2', '2', 'Shipping Type 2', NULL, 2, NULL, 'Shipping Type'),
(62, '000035', 'http://isvat.appspot.com', 'http://isvat.appspot.com', 'VAT Number', NULL, 1, NULL, 'VAT Number Link'),
(63, '000036', 'https://internetmarke.deutschepost.de/OneClickForAppV2/OneClickForAppServiceV2', 'https://internetmarke.deutschepost.de/OneClickForAppV2?wsdl', 'E-Stamp', NULL, 2, NULL, 'E-Stamp Link'),
(64, '000037', 'pcf_07@zq4nnzgbnbvt3.webpage.t-com.de', 'pcf_07@zq4nnzgbnbvt3.webpage.t-com.de', 'E-Stamp User', NULL, 3, NULL, 'E-Stamp username'),
(65, '000038', 'MailService1', 'kQHOpCuo', 'E-Stamp Password', NULL, 4, NULL, 'E-Stamp Password'),
(66, '000039', 'IMPAR', 'ADHCL', 'E-Stamp PARTNER ID', NULL, 5, NULL, 'E-Stamp PARTNER ID'),
(67, '000040', '1', '1', 'E-Stamp KEY PHASE', NULL, 6, NULL, 'E-Stamp KEY PHASE'),
(68, '000041', 'hFIHYEnHlAnjvFsmK6OQJhHef7M4rOlo', 'hFIHYEnHlAnjvFsmK6OQJhHef7M4rOlo', 'E-Stamp PARTNER SIGNATURE', NULL, 7, NULL, 'E-Stamp PARTNER SIGNATURE'),
(69, '000042', 'http://oneclickforapp.dpag.de/V2', 'http://oneclickforapp.dpag.de/V2', 'E-Stamp Namespace', NULL, 7, NULL, 'E-Stamp Namespace'),
(70, '000026', NULL, '6', 'Tax', NULL, NULL, NULL, NULL),
(71, '000043', NULL, 'o1bol1e5xevvpnc', 'o1bol1e5xevvpnc', NULL, NULL, NULL, 'Dropbox app key'),
(72, '000044', NULL, 'xu53120l2w1zujx', 'xu53120l2w1zujx', NULL, NULL, NULL, 'Dropbox app secret'),
(73, '000026', NULL, '7', 'Bank', NULL, NULL, NULL, NULL),
(74, '000026', NULL, '8', 'Apartment', NULL, NULL, NULL, NULL),
(75, '000026', NULL, '9', 'other', NULL, NULL, NULL, NULL),
(76, '000029', '5', '5', 'Never', NULL, 5, NULL, 'Collect items for shipping'),
(82, '000030', '2', '2', 'Monday', NULL, 1, NULL, 'Weekday for shipping '),
(83, '000030', '3', '3', 'Tuesday', NULL, 2, NULL, 'Weekday for shipping '),
(84, '000030', '4', '4', 'Wednesday', NULL, 3, NULL, 'Weekday for shipping '),
(85, '000030', '5', '5', 'Thursday', NULL, 4, NULL, 'Weekday for shipping '),
(86, '000030', '6', '6', 'Friday', NULL, 5, NULL, 'Weekday for shipping '),
(87, '000045', '0.2', '0.2', '0.2', NULL, NULL, NULL, 'Shipping&Handding plus'),
(89, '000025', NULL, '4', 'C6', NULL, NULL, NULL, NULL),
(90, '000025', NULL, '5', 'C1', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
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
  `location_available_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `ip_address`, `username`, `display_name`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `forgotten_password_time`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`, `group_id`, `location_available_id`) VALUES
(1, '127.0.0.1', 'admin', 'Nguyen Trong Dung', 'e7e793ccb91033a84efea89476e8475cee644fb8', '9462e8eee0', 'admin@admin.com', 'NULL', '1268889823', NULL, '32f167c3d7bcd7b50f9781fd5af78a6e9fcdb53f', 1268889823, 1388199589, 1, 'Nguyen', 'Dung', 'FF', '1112223333', 1, 2),
(2, '', 'worker', 'Worker', '1b1526db9737ac9624dee50ff0f0911d1bc7dd22', '9462e8eee0', 'worker@admin.com', NULL, '1268889823', NULL, '1268889823', 0, 1371379246, 1, 'Nguyen', 'Hai', 'FF', NULL, 2, 0),
(7, '127.0.0.1', 'worker02', 'Nguyen', '639d7757f989d32996aeee6920fd7a6bb9ad300c', '64f043', 'worker02@localhost.com', NULL, NULL, NULL, NULL, 1372004627, 1372004627, 1, 'Trong', 'Dung', NULL, NULL, 2, 0),
(9, '146.52.208.31', 'Hemmrich', 'Hemmi', 'fbce2188e4f276ebf256714ad60402e27972c94c', '8aa17e', 'mail@dieholding.de', NULL, NULL, NULL, 'd06539c55bef94aa6f8ed37b4ab592285971a557', 1374059172, 1374059449, 1, 'Christian', 'Hemmrich', NULL, NULL, 1, 0),
(10, '91.5.207.71', 'testerHUB', 'testerHUB', 'c7644ded982d1e532b5ed3aa3a62a4019585959e', 'd9a61b', 'testerHUB@mail.com', NULL, NULL, NULL, NULL, 1387886016, 1388232363, 1, 'John', 'Doe', NULL, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_paging`
--

DROP TABLE IF EXISTS `user_paging`;
CREATE TABLE IF NOT EXISTS `user_paging` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `setting_key` varchar(20) DEFAULT NULL,
  `setting_value` varchar(20) DEFAULT NULL,
  `user_type` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 PACK_KEYS=0 ROW_FORMAT=COMPACT AUTO_INCREMENT=45 ;

--
-- Dumping data for table `user_paging`
--

INSERT INTO `user_paging` (`id`, `user_id`, `setting_key`, `setting_value`, `user_type`) VALUES
(1, 1, 'paging_setting', '50', 0),
(2, 1, 'paging_setting', '100', 1),
(3, 40, 'paging_setting', '50', 0),
(4, 41, 'paging_setting', '10', 0),
(5, 42, 'paging_setting', '10', 0),
(6, 43, 'paging_setting', '10', 0),
(7, 44, 'paging_setting', '10', 0),
(8, 45, 'paging_setting', '10', 0),
(9, 46, 'paging_setting', '10', 0),
(10, 48, 'paging_setting', '10', 0),
(11, 49, 'paging_setting', '10', 0),
(12, 50, 'paging_setting', '10', 0),
(13, 51, 'paging_setting', '10', 0),
(14, 52, 'paging_setting', '10', 0),
(15, 53, 'paging_setting', '10', 0),
(16, 3, 'paging_setting', '10', 0),
(17, 54, 'paging_setting', '10', 0),
(18, 55, 'paging_setting', '10', 0),
(19, 56, 'paging_setting', '10', 0),
(20, 57, 'paging_setting', '10', 0),
(21, 58, 'paging_setting', '10', 0),
(22, 59, 'paging_setting', '10', 0),
(23, 62, 'paging_setting', '10', 0),
(24, 63, 'paging_setting', '10', 0),
(25, 64, 'paging_setting', '10', 0),
(26, 65, 'paging_setting', '10', 0),
(27, 66, 'paging_setting', '10', 0),
(28, 67, 'paging_setting', '10', 0),
(29, 75, 'paging_setting', '10', 0),
(30, 76, 'paging_setting', '10', 0),
(31, 77, 'paging_setting', '10', 0),
(32, 78, 'paging_setting', '10', 0),
(33, 79, 'paging_setting', '10', 0),
(34, 80, 'paging_setting', '10', 0),
(35, 81, 'paging_setting', '10', 0),
(36, 83, 'paging_setting', '10', 0),
(37, 10, 'paging_setting', '100', 1),
(38, 86, 'paging_setting', '10', 0),
(39, 84, 'paging_setting', '10', 0),
(40, 85, 'paging_setting', '10', 0),
(41, 88, 'paging_setting', '10', 0),
(42, 90, 'paging_setting', '10', 0),
(43, 91, 'paging_setting', '10', 0),
(44, 92, 'paging_setting', '10', 0);

-- --------------------------------------------------------

--
-- Table structure for table `zipcodes`
--

DROP TABLE IF EXISTS `zipcodes`;
CREATE TABLE IF NOT EXISTS `zipcodes` (
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

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
