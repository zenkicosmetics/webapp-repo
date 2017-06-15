/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : virtualpost
Target Host     : localhost:3306
Target Database : virtualpost
Date: 2013-11-17 09:27:39
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
INSERT INTO `ci_sessions` VALUES ('50d7c69e6859f5ed797cc334001b9753', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:25.0) Gecko/20100101 Firefox/25.0', '1384572139', '');
INSERT INTO `ci_sessions` VALUES ('c01d261155c60c19614d70080b2a4e03', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:25.0) Gecko/20100101 Firefox/25.0', '1384570122', 'a:8:{s:9:\"user_data\";s:0:\"\";s:8:\"username\";s:5:\"admin\";s:5:\"email\";s:15:\"admin@admin.com\";s:2:\"id\";s:1:\"1\";s:7:\"user_id\";s:1:\"1\";s:8:\"group_id\";s:1:\"1\";s:5:\"group\";s:5:\"admin\";s:21:\"SESSION_USERADMIN_KEY\";O:8:\"stdClass\":20:{s:2:\"id\";s:1:\"1\";s:10:\"ip_address\";s:9:\"127.0.0.1\";s:8:\"username\";s:5:\"admin\";s:12:\"display_name\";s:17:\"Nguyen Trong Dung\";s:8:\"password\";s:40:\"e7e793ccb91033a84efea89476e8475cee644fb8\";s:4:\"salt\";s:10:\"9462e8eee0\";s:5:\"email\";s:15:\"admin@admin.com\";s:15:\"activation_code\";s:4:\"NULL\";s:23:\"forgotten_password_code\";s:10:\"1268889823\";s:23:\"forgotten_password_time\";N;s:13:\"remember_code\";s:10:\"1268889823\";s:10:\"created_on\";s:10:\"1268889823\";s:10:\"last_login\";s:10:\"1384570323\";s:6:\"active\";s:1:\"1\";s:10:\"first_name\";s:6:\"Nguyen\";s:9:\"last_name\";s:4:\"Dung\";s:7:\"company\";s:2:\"FF\";s:5:\"phone\";s:10:\"1112223333\";s:8:\"group_id\";s:1:\"1\";s:21:\"location_available_id\";s:1:\"1\";}}');

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
-- Table structure for clouds
-- ----------------------------
DROP TABLE IF EXISTS `clouds`;
CREATE TABLE `clouds` (
  `cloud_id` varchar(3) NOT NULL DEFAULT '',
  `cloud_name` varchar(250) DEFAULT NULL,
  `active_flag` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`cloud_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of clouds
-- ----------------------------
INSERT INTO `clouds` VALUES ('001', 'Dropbox', '1');
INSERT INTO `clouds` VALUES ('002', 'iCloud', '0');
INSERT INTO `clouds` VALUES ('003', 'Skydrive', '0');
INSERT INTO `clouds` VALUES ('004', 'Google Drive', '0');
INSERT INTO `clouds` VALUES ('005', 'Amazon Drive', '0');
INSERT INTO `clouds` VALUES ('006', 'Evernote', '0');
INSERT INTO `clouds` VALUES ('007', 'Box.net', '0');

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
-- Table structure for customer_cloud
-- ----------------------------
DROP TABLE IF EXISTS `customer_cloud`;
CREATE TABLE `customer_cloud` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) DEFAULT NULL,
  `cloud_id` varchar(3) DEFAULT NULL,
  `auto_save_flag` tinyint(4) DEFAULT NULL COMMENT '0: not synchronized | 1:synchronized',
  `settings` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of customer_cloud
-- ----------------------------

-- ----------------------------
-- Table structure for customer_payment
-- ----------------------------
DROP TABLE IF EXISTS `customer_payment`;
CREATE TABLE `customer_payment` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) DEFAULT NULL,
  `invoice_id` bigint(11) DEFAULT NULL,
  `payment_date` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of customer_payment
-- ----------------------------

-- ----------------------------
-- Table structure for customers
-- ----------------------------
DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
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
  `invoicing_address_completed` tinyint(4) DEFAULT '0',
  `postbox_name_flag` tinyint(4) DEFAULT '0',
  `name_comp_address_flag` tinyint(4) DEFAULT NULL,
  `city_address_flag` tinyint(4) DEFAULT NULL,
  `payment_detail_flag` tinyint(4) DEFAULT '0' COMMENT '0',
  `activated_flag` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `user_id` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of customers
-- ----------------------------
INSERT INTO `customers` VALUES ('1', 'C00000001', 'customer01@localhost.com', 'customer01@localhost.com', '2', 'e10adc3949ba59abbe56e057f20f883e', '1', '1', '1', null, '1384449022', 'e131382ab9b91843abc715f0a4fbe32d', null, '3', '20131001', '1', '1', '1', '1', '1', '1', '1');
INSERT INTO `customers` VALUES ('3', 'C00000003', 'customer03@localhost.com', 'customer03@localhost.com', '1', 'e10adc3949ba59abbe56e057f20f883e', '1', '1', null, null, '1380639324', null, null, null, null, '1', '1', '1', '1', '1', '1', '1');
INSERT INTO `customers` VALUES ('5', 'C00000005', 'nguyen.trong.dung830323@gmail.com', 'nguyen.trong.dung830323@gmail.com', null, 'c4ca4238a0b923820dcc509a6f75849b', null, null, null, null, null, null, '0', null, null, '0', '0', '0', null, null, '0', null);
INSERT INTO `customers` VALUES ('6', 'C00000006', 'relation.test.05@gmail.com', 'relation.test.05@gmail.com', null, 'e10adc3949ba59abbe56e057f20f883e', null, null, null, null, '1377190885', null, '0', null, null, '0', '0', '0', null, null, '0', null);
INSERT INTO `customers` VALUES ('7', 'C00000007', 'dungnt001@gmail.com', 'dungnt001@gmail.com', null, 'e10adc3949ba59abbe56e057f20f883e', null, null, null, null, '1378744289', null, '0', null, null, '0', '0', '0', null, null, '0', '0');
INSERT INTO `customers` VALUES ('8', 'C00000008', 'customer02@localhost.com', 'customer02@localhost.com', null, 'e10adc3949ba59abbe56e057f20f883e', null, null, null, null, '1379346141', null, '0', null, null, '0', '0', '0', null, null, '0', '0');
INSERT INTO `customers` VALUES ('10', 'C00000010', 'dungnt08@localhost.com', 'dungnt08@localhost.com', null, 'e10adc3949ba59abbe56e057f20f883e', null, null, null, null, '1383452690', null, '0', null, null, '0', '0', '0', null, null, '0', '0');

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
INSERT INTO `customers_address` VALUES ('1', 'Ship Nam 1', 'Ship Company Name 1', 'Ship Steer 1', 'Ship Post Code 1', 'Ship City 1', 'Ship Region 1', 'Ship Country 1', 'Invoice Name 1', 'Invoice Company 1', 'Invoice Street 1', 'Invoice Post Code 1', 'Invoice City 1', 'Invoice Region 1', 'Invoice Country 1', null, null);
INSERT INTO `customers_address` VALUES ('3', 'Nguyen Trong Dung', 'VIB', 'Xom 2 Cam Giang', '084', 'Bac Ninh', 'Ha Noi', 'Viet Name', 'Nguyen Trong Dung', 'VIB', 'Xom 2 Cam Giang', '084', 'Bac Ninh', 'Ha Noi', 'Viet Name', null, null);

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
  `location_available_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `postcode` varchar(9) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`location_available_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of customers_location_available
-- ----------------------------
INSERT INTO `customers_location_available` VALUES ('1', '1', 'Berlin', 'Musterstraße 12', '10025', 'Berlin Berlin', 'Germany');
INSERT INTO `customers_location_available` VALUES ('2', '1', 'New York', 'Wall 2', '80237', 'New York', 'USA');

-- ----------------------------
-- Table structure for emails
-- ----------------------------
DROP TABLE IF EXISTS `emails`;
CREATE TABLE `emails` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of emails
-- ----------------------------
INSERT INTO `emails` VALUES ('1', 'new_customer_register', '[Virtual Post] - Customer registration nofitication', 'Email template for register new customer', '<p>Dear, {{full_name}} </p>\r\n<p>Your account in {{site_url}} has been created.</p>\r\n<p>Your login: {{email}}</p>\r\n<p>Your password: {{password}}</p>\r\n<p>\r\nPlease click to this link <a href=\"{{site_url}}\">website</a> to login.\r\n</p>\r\n<p>Best regards,</p>\r\n<p>Virtual Post</p>');
INSERT INTO `emails` VALUES ('2', 'customer_reset_password', '[Virtual Post] - New password nofitication', 'Email template for customer reset password', '<p>Dear, {{full_name}} </p>\r\n<p>Your password of account in {{site_url}} has been reseted.</p>\r\n<p>Your login: {{email}}</p>\r\n<p>Your password: {{password}}</p>\r\n\r\n<p>Best regards,</p>\r\n<p>Virtual Post</p>');
INSERT INTO `emails` VALUES ('3', 'new_incomming_notification', 'New incomming notification', 'Email template for incomming notification 1', '<p>Dear, {{full_name}} </p>\r\n<p>New incomming envelope has been created.</p>\r\n<p>Please click to this link <a href=\"{{site_url}}\">website</a> to check it.</p>\r\n\r\n<p>Best regards,</p>\r\n<p>Virtual Post</p>');
INSERT INTO `emails` VALUES ('5', 'XXXXX', 'XXXXXXXXXXXXX', 'XXXXXXXXXXXXXXXXXXX', 'XXXXXXXXXXXXXXXXXXXXXXXXX');

-- ----------------------------
-- Table structure for envelope_files
-- ----------------------------
DROP TABLE IF EXISTS `envelope_files`;
CREATE TABLE `envelope_files` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `envelope_id` bigint(20) DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `file_name` varchar(500) DEFAULT NULL,
  `public_file_name` varchar(500) DEFAULT NULL,
  `local_file_name` varchar(500) DEFAULT NULL,
  `file_size` double DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL COMMENT '1: Envelope | 2: Document',
  `updated_date` bigint(20) DEFAULT NULL,
  `number_page` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `envelope_files_uk` (`envelope_id`,`customer_id`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of envelope_files
-- ----------------------------
INSERT INTO `envelope_files` VALUES ('53', '22', '1', 'http://localhost/virtualpost/scans/todo/get_file_scan?envelope_id=22&type=1', 'http://localhost/virtualpost/mailbox/get_file_scan?envelope_id=22&type=1', 'system/virtualpost/uploads/filescan/1/EC22_C1_F.png', '50630', '1380423757', '1', null, null);
INSERT INTO `envelope_files` VALUES ('54', '22', '1', 'http://localhost/virtualpost/scans/todo/get_file_scan?envelope_id=22&type=2', 'http://localhost/virtualpost/mailbox/get_file_scan?envelope_id=22&type=2', 'system/virtualpost/uploads/filescan/1/DC22_C1_F.pdf', '72742', '1380423845', '2', null, null);
INSERT INTO `envelope_files` VALUES ('55', '23', '1', 'http://localhost/virtualpost/scans/todo/get_file_scan?envelope_id=23&type=1', 'http://localhost/virtualpost/mailbox/get_file_scan?envelope_id=23&type=1', 'EC23_C1_F.jpg', '29255', '1381564657', '1', '1381573597', null);
INSERT INTO `envelope_files` VALUES ('56', '23', '1', 'http://localhost/virtualpost/scans/todo/get_file_scan?envelope_id=23&type=2', 'http://localhost/virtualpost/mailbox/get_file_scan?envelope_id=23&type=2', 'system/virtualpost/uploads/filescan/1/DC23_C1_F.jpg', '29255', '1381573759', '2', null, null);
INSERT INTO `envelope_files` VALUES ('57', '24', '1', 'http://localhost/virtualpost/scans/todo/get_file_scan?envelope_id=24&type=1', 'http://localhost/virtualpost/mailbox/get_file_scan?envelope_id=24&type=1', 'system/virtualpost/uploads/filescan/1/EC24_C1_F.jpg', '29255', '1381576962', '1', null, null);
INSERT INTO `envelope_files` VALUES ('58', '24', '1', 'http://localhost/virtualpost/scans/todo/get_file_scan?envelope_id=24&type=2', 'http://localhost/virtualpost/mailbox/get_file_scan?envelope_id=24&type=2', 'system/virtualpost/uploads/filescan/1/DC24_C1_F.jpg', '29255', '1381577009', '2', null, null);
INSERT INTO `envelope_files` VALUES ('59', '25', '1', 'http://localhost/virtualpost/scans/todo/get_file_scan?envelope_id=25&type=2', 'http://localhost/virtualpost/mailbox/get_file_scan?envelope_id=25&type=2', 'system/virtualpost/uploads/filescan/1/DC25_C1_F.pdf', '60180', '1381581290', '2', null, null);
INSERT INTO `envelope_files` VALUES ('60', '25', '1', 'http://localhost/virtualpost/scans/todo/get_file_scan?envelope_id=25&type=1', 'http://localhost/virtualpost/mailbox/get_file_scan?envelope_id=25&type=1', 'system/virtualpost/uploads/filescan/1/EC25_C1_F.png', '29255', '1381581418', '1', null, null);
INSERT INTO `envelope_files` VALUES ('61', '26', '1', 'http://localhost/virtualpost/scans/todo/get_file_scan?envelope_id=26&type=2', 'http://localhost/virtualpost/mailbox/get_file_scan?envelope_id=26&type=2', 'DC26_C1_F.pdf', '57413', '1381584909', '2', '1381659649', null);
INSERT INTO `envelope_files` VALUES ('62', '27', '1', 'http://localhost/virtualpost/scans/todo/get_file_scan?envelope_id=27&type=2', 'http://localhost/virtualpost/mailbox/get_file_scan?envelope_id=27&type=2', 'system/virtualpost/uploads/filescan/1/DC27_C1_F.pdf', '85890', '1381670399', '2', null, '0');
INSERT INTO `envelope_files` VALUES ('63', '28', '1', 'http://localhost/virtualpost/scans/todo/get_file_scan?envelope_id=28&type=2', 'http://localhost/virtualpost/mailbox/get_file_scan?envelope_id=28&type=2', 'system/virtualpost/uploads/filescan/1/DC28_C1_F.pdf', '57413', '1381670764', '2', null, '0');
INSERT INTO `envelope_files` VALUES ('65', '26', '1', 'http://localhost/virtualpost/scans/todo/get_file_scan?envelope_id=26&type=1', 'http://localhost/virtualpost/mailbox/get_file_scan?envelope_id=26&type=1', 'system/virtualpost/uploads/filescan/1/EC26_C1_F.png', '29255', '1382888161', '1', null, '1');
INSERT INTO `envelope_files` VALUES ('66', '28', '1', 'http://localhost/virtualpost/scans/todo/get_file_scan?envelope_id=28&type=1', 'http://localhost/virtualpost/mailbox/get_file_scan?envelope_id=28&type=1', 'system/virtualpost/uploads/filescan/1/EC28_C1_F.png', '29255', '1383576166', '1', null, '1');
INSERT INTO `envelope_files` VALUES ('67', '27', '1', 'http://localhost/virtualpost/scans/todo/get_file_scan?envelope_id=27&type=1', 'http://localhost/virtualpost/mailbox/get_file_scan?envelope_id=27&type=1', 'system/virtualpost/uploads/filescan/1/EC27_C1_F.png', '50630', '1383846487', '1', null, '1');
INSERT INTO `envelope_files` VALUES ('68', '29', '1', 'http://localhost/virtualpost/scans/todo/get_file_scan?envelope_id=29&type=2', 'http://localhost/virtualpost/mailbox/get_file_scan?envelope_id=29&type=2', 'system/virtualpost/uploads/filescan/1/DC29_C1_F.pdf', '131512', '1383846813', '2', null, '3');
INSERT INTO `envelope_files` VALUES ('69', '30', '1', 'http://localhost/virtualpost/scans/todo/get_file_scan?envelope_id=30&type=2', 'http://localhost/virtualpost/mailbox/get_file_scan?envelope_id=30&type=2', 'system/virtualpost/uploads/filescan/1/DC30_C1_F.pdf', '165954', '1383847282', '2', '1383847831', '4');
INSERT INTO `envelope_files` VALUES ('70', '31', '1', 'http://localhost/virtualpost/scans/todo/get_file_scan?envelope_id=31&type=2', 'http://localhost/virtualpost/mailbox/get_file_scan?envelope_id=31&type=2', 'system/virtualpost/uploads/filescan/1/DC31_C1_F.pdf', '230897', '1384003985', '2', '1384007578', '2');

-- ----------------------------
-- Table structure for envelope_package
-- ----------------------------
DROP TABLE IF EXISTS `envelope_package`;
CREATE TABLE `envelope_package` (
  `package_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) DEFAULT NULL,
  `postbox_id` bigint(20) DEFAULT NULL,
  `package_date` varchar(8) CHARACTER SET utf8 DEFAULT NULL,
  `package_price` double(20,0) DEFAULT NULL,
  PRIMARY KEY (`package_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of envelope_package
-- ----------------------------

-- ----------------------------
-- Table structure for envelope_shipping
-- ----------------------------
DROP TABLE IF EXISTS `envelope_shipping`;
CREATE TABLE `envelope_shipping` (
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of envelope_shipping
-- ----------------------------
INSERT INTO `envelope_shipping` VALUES ('1', '22', '1', '2', 'Ship Nam 1', 'Ship Company Name 1', 'Ship Steer 1', 'Ship Post Code 1', 'Ship City 1', 'Ship Region 1', 'Ship Country 1', 'https://internetmarke.deutschepost.de/PcfExtensionWeb/preview?keyphase=0&data=4hIwToET8IA8a7MX%2FxFhftST26epqIwUCJp%2FLpKgoRJyKf3ul4IR4WVqlbBctC0saNTPkj49%2FDyVR8oy4FKqBw%3D%3D', '1', 'Letter size A, 20g, C5, 0.58EUR', '1', '1', '3', '1380429703');

-- ----------------------------
-- Table structure for envelope_summary_month
-- ----------------------------
DROP TABLE IF EXISTS `envelope_summary_month`;
CREATE TABLE `envelope_summary_month` (
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
  `collect_shipping_price` decimal(10,0) DEFAULT NULL,
  `additional_pages_scanning_number` bigint(20) DEFAULT NULL,
  `additional_pages_scanning_price` decimal(10,0) DEFAULT NULL,
  `total_pages_scanning_number` bigint(20) DEFAULT NULL,
  `additional_incomming_flag` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `envelope_summary_month_uk` (`customer_id`,`postbox_id`,`year`,`month`,`envelope_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of envelope_summary_month
-- ----------------------------
INSERT INTO `envelope_summary_month` VALUES ('42', '22', '1', '2', '2013', '09', '1', '0.00', '1', '0.00', '1', '0.50', '1', '0.50', null, null, null, null, null, '0');
INSERT INTO `envelope_summary_month` VALUES ('43', '23', '1', '2', '2013', '09', '1', '0.00', '0', '0.00', '0', '0.00', '0', '0.00', null, null, null, null, null, '0');
INSERT INTO `envelope_summary_month` VALUES ('44', '24', '1', '2', '2013', '10', '1', '0.00', '1', '0.00', '1', '0.50', '0', '0.00', null, null, null, null, null, '0');
INSERT INTO `envelope_summary_month` VALUES ('45', '25', '1', '2', '2013', '10', '1', '0.00', '1', '0.00', '1', '0.50', '0', '0.00', null, null, null, null, null, '0');
INSERT INTO `envelope_summary_month` VALUES ('46', '26', '1', '2', '2013', '10', '1', '0.00', '1', '0.00', '1', '0.50', '0', '0.00', null, null, null, null, null, '0');
INSERT INTO `envelope_summary_month` VALUES ('47', '23', '1', '2', '2013', '10', '0', '0.00', '1', '0.00', '1', '0.50', '0', '0.00', null, null, null, null, null, '0');
INSERT INTO `envelope_summary_month` VALUES ('48', '27', '1', '2', '2013', '10', '1', '0.00', '1', '0.00', '1', '0.50', '0', '0.00', null, null, null, null, null, '0');
INSERT INTO `envelope_summary_month` VALUES ('49', '28', '1', '2', '2013', '10', '1', '0.00', '1', '0.00', '1', '0.50', '0', '0.00', null, null, null, null, null, '0');
INSERT INTO `envelope_summary_month` VALUES ('50', '29', '1', '2', '2013', '10', '1', '0.00', '1', '0.00', '1', '0.50', '0', '0.00', null, null, null, null, null, '0');
INSERT INTO `envelope_summary_month` VALUES ('51', '28', '1', '2', '2013', '11', '0', '0.00', '1', '0.00', '0', '0.00', '0', '0.00', null, null, null, null, null, '0');
INSERT INTO `envelope_summary_month` VALUES ('52', '27', '1', '2', '2013', '11', '0', '0.00', '1', '0.00', '0', '0.00', '0', '0.00', null, null, null, null, null, '0');
INSERT INTO `envelope_summary_month` VALUES ('53', '29', '1', '2', '2013', '11', '1', '0.00', '0', '0.00', '1', '0.50', '0', '0.00', null, null, null, null, null, '0');
INSERT INTO `envelope_summary_month` VALUES ('54', '30', '1', '2', '2013', '11', '1', '0.00', '0', '0.00', '1', '0.50', '0', '0.00', null, null, null, null, null, '0');
INSERT INTO `envelope_summary_month` VALUES ('55', '31', '1', '2', '2013', '11', '1', '0.00', '0', '0.00', '1', '0.50', '0', '0.00', null, null, null, null, null, '0');

-- ----------------------------
-- Table structure for envelopes
-- ----------------------------
DROP TABLE IF EXISTS `envelopes`;
CREATE TABLE `envelopes` (
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
  `incomming_date_ony` varchar(6) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of envelopes
-- ----------------------------
INSERT INTO `envelopes` VALUES ('22', null, 'Nguyen Trong Dung', '1', '2', 'C5', '12.000', 'g', '1', '1380429703', null, '1380423697', null, '1380427025', '', '1', '0', '1', '0', null, '1', '1', '1380423870', '1', '1380429703', null, null, null, null, '1', '1382888180', '1', '1', null, null, '0');
INSERT INTO `envelopes` VALUES ('23', null, 'Nguyen Trong Dung', '1', '2', 'C5', '31.000', 'g', null, null, null, '1380430559', null, '1382888836', '', '1', null, null, '0', null, '1', '1', '1381573766', '0', null, null, null, null, null, '1', '1382888180', '0', '1', null, null, '0');
INSERT INTO `envelopes` VALUES ('24', null, 'Nguyen Trong Dung', '1', '2', 'C4', '21.000', 'g', null, null, null, '1381562582', null, '1382888820', '', '1', null, null, '0', null, '1', '1', '1381577012', '0', null, null, null, null, null, '1', '1384007585', '0', '1', null, null, '0');
INSERT INTO `envelopes` VALUES ('25', null, 'Nguyen Trong Dung', '1', '2', 'C4', '21.000', 'g', null, null, null, '1381562833', null, '1382888819', '', '1', null, null, '0', null, '1', '1', '1381581332', '0', null, null, null, null, null, '1', '1384007585', '0', '1', null, null, '0');
INSERT INTO `envelopes` VALUES ('26', null, 'Nguyen Trong Dung', '1', '2', 'C4', '21.000', 'g', null, null, null, '1381562889', null, '1382888819', '', '1', null, null, '0', null, '1', '1', '1381659677', '0', null, null, null, null, null, '1', '1384007585', '0', '1', null, null, '0');
INSERT INTO `envelopes` VALUES ('27', null, 'Nguyen Trong Dung', '1', '2', 'C5', '231.000', 'g', null, null, null, '1381670342', null, '1382888094', '', '1', null, null, '0', null, '1', '1', '1381670403', '0', null, null, null, null, null, '1', '1384293900', '0', '1', null, null, '0');
INSERT INTO `envelopes` VALUES ('28', null, 'Nguyen Trong Dung', '1', '2', 'C5', '13.000', 'g', null, null, null, '1381670712', null, '1382888094', '', '1', null, null, '0', null, '1', '1', '1381670767', '0', null, null, null, null, null, '1', '1384293900', '0', '1', null, null, '0');
INSERT INTO `envelopes` VALUES ('29', 'C00000001_2_071113_000', 'Nguyen Trong Dung', '1', '2', 'C4', '12.000', 'g', null, null, null, '1383846767', '071113', '1383846783', '', '1', null, null, '0', null, null, '1', '1383846815', null, null, null, null, null, null, null, null, '0', '1', null, null, '0');
INSERT INTO `envelopes` VALUES ('30', 'C00000001_2_071113_001', 'Nguyen Trong Dung', '1', '2', 'C4', '33.000', 'g', null, null, null, '1383846997', '071113', '1383847025', '', '1', null, null, '0', null, null, '1', '1383847843', null, null, null, null, null, null, null, null, '0', '1', null, null, '0');
INSERT INTO `envelopes` VALUES ('31', 'C00000001_2_071113_002', 'Nguyen Trong Dung', '1', '2', 'C4', '2.000', 'g', null, null, null, '1383849731', '071113', '1383849822', '', '1', null, null, '0', null, null, '1', '1384007583', null, null, null, null, null, null, null, null, '0', '1', null, null, '0');

-- ----------------------------
-- Table structure for envelopes_completed
-- ----------------------------
DROP TABLE IF EXISTS `envelopes_completed`;
CREATE TABLE `envelopes_completed` (
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of envelopes_completed
-- ----------------------------
INSERT INTO `envelopes_completed` VALUES ('8', '22', 'Nguyen Trong Dung', '1', '1', null, '2', 'C5', '12.000', 'g', '1380423727', '1', '1380423767', '1380423697', null, '0', null, null, '0', '1', null, null, null, null, null, '1', '0');
INSERT INTO `envelopes_completed` VALUES ('9', '22', 'Nguyen Trong Dung', '1', '2', null, '2', 'C5', '12.000', 'g', '1380423819', '1', '1380423870', '1380423697', '', '1', null, null, '0', '1', '1', null, null, null, null, '1', '0');
INSERT INTO `envelopes_completed` VALUES ('10', '22', 'Nguyen Trong Dung', '1', '3', null, '2', 'C5', '12.000', 'g', '1380427025', '1', '1380429703', '1380423697', '', '1', '0', '0', '0', '1', '1', '1', null, null, null, '1', '0');
INSERT INTO `envelopes_completed` VALUES ('11', '23', 'Nguyen Trong Dung', '1', '1', null, '2', 'C5', '31.000', 'g', '1380430733', '1', '1381573605', '1380430559', null, '0', null, null, '0', '1', null, null, null, null, null, '1', '1');
INSERT INTO `envelopes_completed` VALUES ('12', '23', 'Nguyen Trong Dung', '1', '2', null, '2', 'C5', '31.000', 'g', '1381573719', '1', '1381573766', '1380430559', '', '1', null, null, '0', '1', '1', null, null, null, null, '1', '1');
INSERT INTO `envelopes_completed` VALUES ('13', '24', 'Nguyen Trong Dung', '1', '1', null, '2', 'C4', '21.000', 'g', '1381576933', '1', '1381576979', '1381562582', null, '0', null, null, '0', '1', '0', null, null, null, null, '1', '0');
INSERT INTO `envelopes_completed` VALUES ('14', '24', 'Nguyen Trong Dung', '1', '2', null, '2', 'C4', '21.000', 'g', '1381576933', '1', '1381577013', '1381562582', '', '1', null, null, '0', '1', '1', null, null, null, null, '1', '0');
INSERT INTO `envelopes_completed` VALUES ('15', '25', 'Nguyen Trong Dung', '1', '2', null, '2', 'C4', '21.000', 'g', '1381577039', '1', '1381581333', '1381562833', '', '1', null, null, '0', null, '1', null, null, null, null, '1', '0');
INSERT INTO `envelopes_completed` VALUES ('16', '25', 'Nguyen Trong Dung', '1', '1', null, '2', 'C4', '21.000', 'g', '1381581388', '1', '1381581423', '1381562833', '', '1', null, null, '0', '1', '1', null, null, null, null, '1', '0');
INSERT INTO `envelopes_completed` VALUES ('17', '26', 'Nguyen Trong Dung', '1', '2', null, '2', 'C4', '21.000', 'g', '1381584849', '1', '1381659679', '1381562889', '', '1', null, null, '0', null, '1', null, null, null, null, '1', '0');
INSERT INTO `envelopes_completed` VALUES ('18', '27', 'Nguyen Trong Dung', '1', '2', null, '2', 'C5', '231.000', 'g', '1381670364', '1', '1381670404', '1381670342', '', '1', null, null, '0', null, '1', null, null, null, null, '1', '0');
INSERT INTO `envelopes_completed` VALUES ('19', '28', 'Nguyen Trong Dung', '1', '2', null, '2', 'C5', '13.000', 'g', '1381670726', '1', '1381670768', '1381670712', '', '1', null, null, '0', null, '1', null, null, null, null, '1', '0');
INSERT INTO `envelopes_completed` VALUES ('20', '29', 'Nguyen Trong Dung', '1', '2', null, '2', 'C4', '21.000', 'g', '1381670920', '1', '1381671724', '1381670898', '', '1', null, null, '0', null, '1', null, null, null, null, '1', '0');
INSERT INTO `envelopes_completed` VALUES ('21', '26', 'Nguyen Trong Dung', '1', '1', null, '2', 'C4', '21.000', 'g', '1382850502', '1', '1382888181', '1381562889', '', '1', null, null, '0', '1', '1', null, null, null, null, '1', '0');
INSERT INTO `envelopes_completed` VALUES ('22', '29', 'Nguyen Trong Dung', '1', '1', null, '2', 'C4', '21.000', 'g', '1382888093', '1', '1382888321', '1381670898', '', '1', null, null, '0', '1', '1', '0', null, null, null, '1', '0');
INSERT INTO `envelopes_completed` VALUES ('23', '29', 'Nguyen Trong Dung', '1', '5', null, '2', 'C4', '21.000', 'g', '1382891402', '1', '1382891593', '1381670898', '', '1', null, null, '0', '1', '1', null, null, '0', null, '1', '0');
INSERT INTO `envelopes_completed` VALUES ('24', '28', 'Nguyen Trong Dung', '1', '1', null, '2', 'C5', '13.000', 'g', '1382888094', '1', '1383576378', '1381670712', '', '1', null, null, '0', '1', '1', '0', null, null, null, '1', '1');
INSERT INTO `envelopes_completed` VALUES ('25', '27', 'Nguyen Trong Dung', '1', '1', null, '2', 'C5', '231.000', 'g', '1382888094', '1', '1383846495', '1381670342', '', '1', null, null, '0', '1', '1', '0', null, null, null, '1', '1');
INSERT INTO `envelopes_completed` VALUES ('26', '29', 'Nguyen Trong Dung', '1', '2', null, '2', 'C4', '12.000', 'g', '1383846783', '1', '1383846816', '1383846767', '', '1', null, null, '0', null, '1', null, null, null, null, '1', '0');
INSERT INTO `envelopes_completed` VALUES ('27', '30', 'Nguyen Trong Dung', '1', '2', null, '2', 'C4', '33.000', 'g', '1383847025', '1', '1383847844', '1383846997', '', '1', null, null, '0', null, '1', null, null, null, null, '1', '0');
INSERT INTO `envelopes_completed` VALUES ('28', '31', 'Nguyen Trong Dung', '1', '2', null, '2', 'C4', '2.000', 'g', '1383849822', '1', '1384007585', '1383849731', '', '1', null, null, '0', null, '1', null, null, null, null, '1', '0');

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
-- Table structure for invoice_detail
-- ----------------------------
DROP TABLE IF EXISTS `invoice_detail`;
CREATE TABLE `invoice_detail` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) DEFAULT NULL,
  `activity` varchar(255) DEFAULT NULL,
  `activity_date` varchar(8) DEFAULT NULL,
  `item_number` int(11) DEFAULT NULL,
  `unit_price` double DEFAULT NULL,
  `item_amount` double DEFAULT NULL,
  `unit` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of invoice_detail
-- ----------------------------
INSERT INTO `invoice_detail` VALUES ('1', '1', 'Incomming', '20130804', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('2', '1', 'Incomming', '20130804', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('3', '1', 'Incomming', '20130804', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('4', '1', 'Incomming', '20130804', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('5', '1', 'Incomming', '20130804', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('6', '1', 'Incomming', '20130804', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('7', '1', 'Incomming', '20130804', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('8', '1', 'Incomming', '20130804', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('9', '1', 'Incomming', '20130804', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('10', '1', 'Incomming', '20130804', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('11', '1', 'Incomming', '20130804', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('12', '1', 'Incomming', '20130804', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('13', '1', 'Incomming', '20130804', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('14', '1', 'Incomming', '20130804', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('15', '1', 'Envelope scan', '20130804', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('16', '1', 'Incomming', '20130805', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('17', '1', 'Incomming', '20130805', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('18', '1', 'Envelope scanning', '20130807', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('19', '1', 'Envelope scanning', '20130807', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('20', '1', 'Envelope scanning', '20130807', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('21', '1', 'Envelope scanning', '20130807', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('22', '1', 'Envelope scanning', '20130807', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('23', '1', 'Envelope scanning', '20130807', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('24', '1', 'Incomming', '20130807', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('25', '1', 'Incomming', '20130807', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('26', '1', 'Incomming', '20130807', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('27', '1', 'Envelope scanning', '20130807', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('28', '1', 'Envelope scanning', '20130807', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('29', '1', 'Envelope scanning', '20130807', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('30', '1', 'Envelope scanning', '20130808', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('31', '1', 'Envelope scanning', '20130808', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('32', '1', 'Envelope scanning', '20130808', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('33', '1', 'Envelope scanning', '20130808', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('34', '1', 'Envelope scanning', '20130808', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('35', '1', 'Envelope scanning', '20130808', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('36', '1', 'Incomming', '20130808', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('37', '1', 'Envelope scanning', '20130808', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('38', '1', 'Envelope scanning', '20130808', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('39', '1', 'Incomming', '20130808', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('40', '1', 'Envelope scanning', '20130808', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('41', '1', 'Incomming', '20130808', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('42', '1', 'Envelope scanning', '20130808', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('43', '1', 'Scanning', '20130808', '1', '1', '1', '$');
INSERT INTO `invoice_detail` VALUES ('44', '1', 'Scanning', '20130808', '1', '1', '1', '$');
INSERT INTO `invoice_detail` VALUES ('45', '1', 'Incomming', '20130810', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('46', '1', 'Incomming', '20130810', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('47', '1', 'Scanning', '20130820', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('48', '1', 'Incomming', '20130821', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('49', '1', 'Incomming', '20130821', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('50', '1', 'Incomming', '20130824', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('51', '1', 'Incomming', '20130824', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('52', '1', 'Incomming', '20130824', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('53', '1', 'Incomming', '20130824', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('54', '1', 'Incomming', '20130824', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('55', '1', 'Envelope scanning', '20130824', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('56', '1', 'Scanning', '20130824', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('57', '1', 'Incomming', '20130824', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('58', '1', 'Envelope scanning', '20130824', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('59', '1', 'Scanning', '20130824', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('60', '1', 'Envelope scanning', '20130824', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('61', '1', 'Scanning', '20130824', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('62', '1', 'Scanning', '20130826', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('63', '1', 'Incomming', '20130902', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('64', '1', 'Incomming', '20130902', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('65', '1', 'Envelope scanning', '20130902', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('66', '1', 'Scanning', '20130911', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('67', '1', 'Scanning', '20130918', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('68', '1', 'Envelope scanning', '20130918', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('69', null, 'Shipping&Handling', '20130918', '1', null, '0', '$');
INSERT INTO `invoice_detail` VALUES ('70', '1', 'Incomming', '20130924', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('71', null, 'Shipping&Handling', '20130929', '1', null, '0', '$');
INSERT INTO `invoice_detail` VALUES ('72', null, 'Shipping&Handling', '20130929', '1', null, '0', '$');
INSERT INTO `invoice_detail` VALUES ('73', '1', 'Shipping&Handling', '20130929', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('74', '1', 'Shipping&Handling', '20130929', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('75', '1', 'Envelope scanning', '20130929', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('76', '1', 'Shipping&Handling', '20130929', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('77', '1', 'Incomming', '20130929', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('78', '1', 'Envelope scanning', '20130929', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('79', '1', 'Scanning', '20130929', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('80', '1', 'Shipping&Handling', '20130929', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('81', '1', 'Shipping&Handling', '20130929', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('82', '1', 'Shipping&Handling', '20130929', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('83', '1', 'Shipping&Handling', '20130929', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('84', '1', 'Incomming', '20130929', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('85', '1', 'Incomming', '20131012', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('86', '1', 'Incomming', '20131012', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('87', '1', 'Incomming', '20131012', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('88', '1', 'Envelope scanning', '20131012', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('89', '1', 'Scanning', '20131012', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('90', '1', 'Envelope scanning', '20131012', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('91', '1', 'Scanning', '20131012', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('92', '1', 'Scanning', '20131012', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('93', '1', 'Envelope scanning', '20131012', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('94', '1', 'Scanning', '20131013', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('95', '1', 'Incomming', '20131013', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('96', '1', 'Scanning', '20131013', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('97', '1', 'Incomming', '20131013', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('98', '1', 'Scanning', '20131013', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('99', '1', 'Incomming', '20131013', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('100', '1', 'Scanning', '20131013', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('101', '1', 'Envelope scanning', '20131027', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('102', '1', 'Envelope scanning', '20131027', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('103', '1', 'Envelope scanning', '20131104', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('104', '1', 'Envelope scanning', '20131104', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('105', '1', 'Envelope scanning', '20131107', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('106', '1', 'Incomming', '20131107', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('107', '1', 'Scanning', '20131107', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('108', '1', 'Incomming', '20131107', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('109', '1', 'Scanning', '20131107', '1', '0.5', '0.5', '$');
INSERT INTO `invoice_detail` VALUES ('110', '1', 'Incomming', '20131107', '1', '0', '0', '$');
INSERT INTO `invoice_detail` VALUES ('111', '1', 'Scanning', '20131109', '1', '0.5', '0.5', '$');

-- ----------------------------
-- Table structure for invoice_summary
-- ----------------------------
DROP TABLE IF EXISTS `invoice_summary`;
CREATE TABLE `invoice_summary` (
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
  `additional_private_postbox_quantity` int(11) DEFAULT NULL,
  `additional_private_postbox_netprice` double DEFAULT NULL,
  `additional_business_postbox_quantity` int(11) DEFAULT NULL,
  `additional_business_postbox_netprice` double DEFAULT NULL,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of invoice_summary
-- ----------------------------
INSERT INTO `invoice_summary` VALUES ('1', '1', '201306', '9.95', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('2', '1', '201308', '9.95', null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('3', '1', '201309', '24.8', null, '0', null, null, null, null, null, '0', null, null, '1', null, null, null, null, null, null, '0', null, null, null, null, null, null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('4', '3', '201309', '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', null, null, null, null, null, null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('5', '5', '201309', '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', null, null, null, null, null, null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('6', '6', '201309', '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', null, null, null, null, null, null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('7', '7', '201309', '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', null, null, null, null, null, null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('8', '8', '201309', '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', null, null, null, null, null, null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('9', '1', '201310', null, null, null, '0', null, null, '0', null, null, '3.5', null, null, null, '0', null, null, null, null, '0', '0', '0', '0', '0', '0', null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('10', '3', '201310', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', '0', '0', '0', '0', '0', null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('11', '5', '201310', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', '0', '0', '0', '0', '0', null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('12', '6', '201310', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', '0', '0', '0', '0', '0', null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('13', '7', '201310', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', '0', '0', '0', '0', '0', null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('14', '8', '201310', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', '0', '0', '0', '0', '0', null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('15', '1', '201311', '4.95', null, null, '0', null, null, '0', null, null, '1.5', null, null, null, '0', null, null, null, null, '0', '0', '0', '0', '0', '0', null, null, '0', null, null, '1', '4.95', null, null, null, null, '0', '9.95', '0', '0', '3', '0', '0', '0', '0', '0', '2', '0', '0', '0', '0', '0', '3', '0', '0', '0', null, null, '0', '0', '0', '0', '0', '0', '0', '0', null, null, '0', '0', null, '0.05', null, '0.04', null, '0.03', null, '0.2', null, '0.15', null, '0.1');
INSERT INTO `invoice_summary` VALUES ('16', '3', '201311', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', '0', '0', '0', '0', '0', null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0.05', null, '0.04', null, '0.03', null, '0.2', null, '0.15', null, '0.1');
INSERT INTO `invoice_summary` VALUES ('17', '5', '201311', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', '0', '0', '0', '0', '0', null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0.05', null, '0.04', null, '0.03', null, '0.2', null, '0.15', null, '0.1');
INSERT INTO `invoice_summary` VALUES ('18', '6', '201311', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', '0', '0', '0', '0', '0', null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0.05', null, '0.04', null, '0.03', null, '0.2', null, '0.15', null, '0.1');
INSERT INTO `invoice_summary` VALUES ('19', '7', '201311', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', '0', '0', '0', '0', '0', null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0.05', null, '0.04', null, '0.03', null, '0.2', null, '0.15', null, '0.1');
INSERT INTO `invoice_summary` VALUES ('20', '8', '201311', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', '0', '0', '0', '0', '0', null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0.05', null, '0.04', null, '0.03', null, '0.2', null, '0.15', null, '0.1');
INSERT INTO `invoice_summary` VALUES ('21', '10', '201311', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', '0', '0', '0', '0', '0', null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0.05', null, '0.04', null, '0.03', null, '0.2', null, '0.15', null, '0.1');

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
  `image_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of location
-- ----------------------------
INSERT INTO `location` VALUES ('1', 'Berlin 1', 'Musterstraße 13', '10025', 'Berlin', 'Berlin', 'Germany', '');
INSERT INTO `location` VALUES ('2', 'Viet Name', 'Ha noi', '093', 'Ha Noi', 'Ha Noi', 'Viet name', '/uploads/images/location/default_location.png');

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
-- Table structure for package_prices
-- ----------------------------
DROP TABLE IF EXISTS `package_prices`;
CREATE TABLE `package_prices` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `weight` int(11) DEFAULT NULL,
  `weight_unit` varchar(3) DEFAULT NULL,
  `size` varchar(255) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `price_unit` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of package_prices
-- ----------------------------
INSERT INTO `package_prices` VALUES ('1', 'Letter size A', '20', 'g', 'C5', '0.58', 'EUR');
INSERT INTO `package_prices` VALUES ('2', 'Letter size B', '40', 'g', 'C4', '0.95', 'EUR');
INSERT INTO `package_prices` VALUES ('3', 'Letter size C', '100', 'g', 'C3', '1.45', 'EUR');
INSERT INTO `package_prices` VALUES ('4', 'Letter size D', '500', 'g', 'C2', '2.45', 'EUR');
INSERT INTO `package_prices` VALUES ('5', 'Large letter size A', '500', 'g', '20cmx20cmx4cmx', '4.9', 'EUR');
INSERT INTO `package_prices` VALUES ('6', 'Large letter size B', '1000', 'g', '30cmx20cmx10cmx', '6.9', 'EUR');
INSERT INTO `package_prices` VALUES ('7', 'Large letter size C', '5000', 'g', '30cmx40cmx40cmx', '9.9', 'EUR');

-- ----------------------------
-- Table structure for payment
-- ----------------------------
DROP TABLE IF EXISTS `payment`;
CREATE TABLE `payment` (
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of payment
-- ----------------------------
INSERT INTO `payment` VALUES ('5', '1', '30', 'V', '4111111111111111', 'Nguyen Trong Dung', '123', '13', '12');
INSERT INTO `payment` VALUES ('6', '1', '30', 'M', '5500000000000004\r', 'Nguyen Trong Dung 02', '123', '14', '12');
INSERT INTO `payment` VALUES ('7', '3', '30', 'V', '4012001037141112', 'Nguyen', '123', '13', '12');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of permissions
-- ----------------------------

-- ----------------------------
-- Table structure for postbox
-- ----------------------------
DROP TABLE IF EXISTS `postbox`;
CREATE TABLE `postbox` (
  `postbox_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `postbox_code` varchar(30) DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `postbox_name` varchar(255) DEFAULT NULL,
  `location_available_id` bigint(20) DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `deleted` tinyint(4) DEFAULT '0',
  `is_main_postbox` tinyint(4) DEFAULT NULL,
  `plan_deleted_date` varchar(8) DEFAULT NULL,
  `updated_date` int(8) DEFAULT NULL,
  `apply_date` varchar(8) DEFAULT NULL COMMENT 'Only for private or business (when add on middle of month)',
  `new_postbox_type` smallint(6) DEFAULT NULL,
  `plan_date_change_postbox_type` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`postbox_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of postbox
-- ----------------------------
INSERT INTO `postbox` VALUES ('1', 'C00000001_P0000001', '1', 'DungNT', '1', '2', 'XXXX', 'VIB', '0', '1', null, '1376834021', null, '3', '20131001');
INSERT INTO `postbox` VALUES ('2', 'C00000001_P00000002', '1', 'Cust 1 - Post box Q', '1', '2', 'Cust 1 ', 'USOL-V', '0', null, null, '1378400690', null, null, null);
INSERT INTO `postbox` VALUES ('3', 'C00000003_P00000003', '3', 'Default postbox name 1 - customer03@localhost.com', '1', '1', 'Default name 1 - customer03@localhost.com', 'Default company 1 - customer03@localhost.com', '0', '1', null, null, null, null, null);
INSERT INTO `postbox` VALUES ('4', 'C00000005_P00000004', '5', 'Post Name 1', null, null, 'Name 1', 'Company 1', null, '1', null, null, null, null, null);
INSERT INTO `postbox` VALUES ('5', 'C00000006_P00000005', '6', 'Post Name 1', null, '1', 'Name 1', 'Company 1', null, '1', null, null, null, null, null);
INSERT INTO `postbox` VALUES ('6', 'C00000001_P00000006', '1', 'PrivatePostbox01', '1', '2', 'DungNT', 'VIB', '0', null, null, null, null, null, null);
INSERT INTO `postbox` VALUES ('7', 'C00000001_P00000007', '1', 'BusinessPostBox01', '1', '3', 'DungNT02', 'VIB', '0', null, null, null, null, null, null);
INSERT INTO `postbox` VALUES ('8', 'C00000007_P00000008', '7', '', null, '1', '', '', '0', '1', null, null, null, null, null);
INSERT INTO `postbox` VALUES ('9', 'C00000008_P00000009', '8', '', null, '1', '', '', '0', '1', null, null, null, null, null);
INSERT INTO `postbox` VALUES ('11', 'C00000010_P00000011', '10', '', null, '1', '', '', '0', '1', null, null, null, null, null);

-- ----------------------------
-- Table structure for postbox_fee_month
-- ----------------------------
DROP TABLE IF EXISTS `postbox_fee_month`;
CREATE TABLE `postbox_fee_month` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `postbox_id` bigint(20) DEFAULT '0',
  `year_month` varchar(6) DEFAULT NULL,
  `postbox_fee_flag` varchar(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `postbox_fee_month_uk` (`postbox_id`,`year_month`,`postbox_fee_flag`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of postbox_fee_month
-- ----------------------------
INSERT INTO `postbox_fee_month` VALUES ('1', '1', '201310', '1');
INSERT INTO `postbox_fee_month` VALUES ('2', '1', '201311', '1');

-- ----------------------------
-- Table structure for postbox_settings
-- ----------------------------
DROP TABLE IF EXISTS `postbox_settings`;
CREATE TABLE `postbox_settings` (
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

-- ----------------------------
-- Records of postbox_settings
-- ----------------------------
INSERT INTO `postbox_settings` VALUES ('1', '1', '1', '0', '1', '0', '1', '1', '1', '1');
INSERT INTO `postbox_settings` VALUES ('2', '1', '0', '1', '0', '1', '2', '1', '1', '1');

-- ----------------------------
-- Table structure for pricing
-- ----------------------------
DROP TABLE IF EXISTS `pricing`;
CREATE TABLE `pricing` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `account_type` tinyint(4) DEFAULT NULL COMMENT 'Reference: 000033',
  `item_name` varchar(250) DEFAULT NULL,
  `item_value` varchar(250) DEFAULT NULL,
  `item_description` varchar(255) DEFAULT NULL,
  `item_unit` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of pricing
-- ----------------------------
INSERT INTO `pricing` VALUES ('1', '1', 'address_number', '1', 'address', 'numbers');
INSERT INTO `pricing` VALUES ('2', '1', 'included_incomming_items', '0', 'included incomming items', null);
INSERT INTO `pricing` VALUES ('3', '1', 'storage', '1', 'storage', 'GB');
INSERT INTO `pricing` VALUES ('4', '1', 'hand_sorting_of_advertising', '0', 'hand sorting of advertising', null);
INSERT INTO `pricing` VALUES ('5', '1', 'envelope_scanning_front', '5', 'envelope scanning (front)', null);
INSERT INTO `pricing` VALUES ('6', '1', 'included_opening_scanning', '0', 'included opening and scanning', null);
INSERT INTO `pricing` VALUES ('7', '1', 'storing_items_letters', '4', 'storing items (letters)', 'weeks');
INSERT INTO `pricing` VALUES ('8', '1', 'storing_items_packages', '1', 'storing items (packages)', 'week');
INSERT INTO `pricing` VALUES ('9', '1', 'storing_items_digitally', '1', 'storing items digitally', 'year');
INSERT INTO `pricing` VALUES ('10', '1', 'trashing_items', '-1', 'trashing items', 'unlimited');
INSERT INTO `pricing` VALUES ('11', '1', 'cloud_service_connection', 'included', 'cloud service connection', 'included');
INSERT INTO `pricing` VALUES ('12', '1', 'additional_incomming_items', '0.5', 'additional incomming items', 'EUR');
INSERT INTO `pricing` VALUES ('13', '1', 'envelop_scanning', '0.2', 'envelop scanning', 'EUR');
INSERT INTO `pricing` VALUES ('14', '1', 'opening_scanning', '1', 'opening and scanning', null);
INSERT INTO `pricing` VALUES ('15', '1', 'send_out_directly', '1', 'send out to original address directly', null);
INSERT INTO `pricing` VALUES ('16', '1', 'send_out_collected', '2', 'send out to original address collected', null);
INSERT INTO `pricing` VALUES ('17', '1', 'storing_items_over_free_letter', '0.05', 'storing items over free period (letters)', 'day');
INSERT INTO `pricing` VALUES ('18', '1', 'storing_items_over_free_packages', '0.2', 'storing items over free period (packages)', null);
INSERT INTO `pricing` VALUES ('19', '1', 'additional_private_mailbox', '0', 'additional private mailbox', null);
INSERT INTO `pricing` VALUES ('20', '1', 'additional_business_mailbox', '0', 'additional business mailbox ', null);
INSERT INTO `pricing` VALUES ('21', '2', 'address_number', '1', 'address', 'numbers');
INSERT INTO `pricing` VALUES ('22', '2', 'included_incomming_items', '10', 'included incomming items', null);
INSERT INTO `pricing` VALUES ('23', '2', 'storage', '0', 'storage', 'GB');
INSERT INTO `pricing` VALUES ('24', '2', 'hand_sorting_of_advertising', '-1', 'hand sorting of advertising', null);
INSERT INTO `pricing` VALUES ('25', '2', 'envelope_scanning_front', '10', 'envelope scanning (front)', null);
INSERT INTO `pricing` VALUES ('26', '2', 'included_opening_scanning', '5', 'included opening and scanning', null);
INSERT INTO `pricing` VALUES ('27', '2', 'storing_items_letters', '4', 'storing items (letters)', 'weeks');
INSERT INTO `pricing` VALUES ('28', '2', 'storing_items_packages', '1', 'storing items (packages)', 'week');
INSERT INTO `pricing` VALUES ('29', '2', 'storing_items_digitally', '1', 'storing items digitally', 'year');
INSERT INTO `pricing` VALUES ('30', '2', 'trashing_items', '-1', 'trashing items', 'unlimited');
INSERT INTO `pricing` VALUES ('31', '2', 'cloud_service_connection', 'included', 'cloud service connection', 'included');
INSERT INTO `pricing` VALUES ('32', '2', 'additional_incomming_items', '0.3', 'additional incomming items', 'EUR');
INSERT INTO `pricing` VALUES ('33', '2', 'envelop_scanning', '0.1', 'envelop scanning', 'EUR');
INSERT INTO `pricing` VALUES ('34', '2', 'opening_scanning', '0.5', 'opening and scanning', null);
INSERT INTO `pricing` VALUES ('35', '2', 'send_out_directly', '0.5', 'send out to original address directly', null);
INSERT INTO `pricing` VALUES ('36', '2', 'send_out_collected', '1', 'send out to original address collected', null);
INSERT INTO `pricing` VALUES ('37', '2', 'storing_items_over_free_letter', '0.04', 'storing items over free period (letters)', 'day');
INSERT INTO `pricing` VALUES ('38', '2', 'storing_items_over_free_packages', '0.15', 'storing items over free period (packages)', null);
INSERT INTO `pricing` VALUES ('39', '2', 'additional_private_mailbox', '4.95', 'additional private mailbox', null);
INSERT INTO `pricing` VALUES ('40', '2', 'additional_business_mailbox', '9.95', 'additional business mailbox ', null);
INSERT INTO `pricing` VALUES ('41', '3', 'address_number', '1', 'address', 'numbers');
INSERT INTO `pricing` VALUES ('42', '3', 'included_incomming_items', '50', 'included incomming items', null);
INSERT INTO `pricing` VALUES ('43', '3', 'storage', '0', 'storage', 'GB');
INSERT INTO `pricing` VALUES ('44', '3', 'hand_sorting_of_advertising', '-1', 'hand sorting of advertising', null);
INSERT INTO `pricing` VALUES ('45', '3', 'envelope_scanning_front', '50', 'envelope scanning (front)', null);
INSERT INTO `pricing` VALUES ('46', '3', 'included_opening_scanning', '10', 'included opening and scanning', null);
INSERT INTO `pricing` VALUES ('47', '3', 'storing_items_letters', '4', 'storing items (letters)', 'weeks');
INSERT INTO `pricing` VALUES ('48', '3', 'storing_items_packages', '1', 'storing items (packages)', 'week');
INSERT INTO `pricing` VALUES ('49', '3', 'storing_items_digitally', '1', 'storing items digitally', 'year');
INSERT INTO `pricing` VALUES ('50', '3', 'trashing_items', '-1', 'trashing items', 'unlimited');
INSERT INTO `pricing` VALUES ('51', '3', 'cloud_service_connection', 'included', 'cloud service connection', 'included');
INSERT INTO `pricing` VALUES ('52', '3', 'additional_incomming_items', '0.2', 'additional incomming items', 'EUR');
INSERT INTO `pricing` VALUES ('53', '3', 'envelop_scanning', '0.05', 'envelop scanning', 'EUR');
INSERT INTO `pricing` VALUES ('54', '3', 'opening_scanning', '0.4', 'opening and scanning', null);
INSERT INTO `pricing` VALUES ('55', '3', 'send_out_directly', '0.4', 'send out to original address directly', null);
INSERT INTO `pricing` VALUES ('56', '3', 'send_out_collected', '0.8', 'send out to original address collected', null);
INSERT INTO `pricing` VALUES ('57', '3', 'storing_items_over_free_letter', '0.03', 'storing items over free period (letters)', 'EUR/day');
INSERT INTO `pricing` VALUES ('58', '3', 'storing_items_over_free_packages', '0.1', 'storing items over free period (packages)', 'EUR/day');
INSERT INTO `pricing` VALUES ('59', '3', 'additional_private_mailbox', '2.95', 'additional private mailbox', null);
INSERT INTO `pricing` VALUES ('60', '3', 'additional_business_mailbox', '4.95', 'additional business mailbox ', null);
INSERT INTO `pricing` VALUES ('61', '1', 'postbox_fee', '0', 'fee for first postbox', 'EUR');
INSERT INTO `pricing` VALUES ('62', '2', 'postbox_fee', '4.95', 'postbox fee for first', 'EUR');
INSERT INTO `pricing` VALUES ('63', '3', 'postbox_fee', '9.95', 'Postbox fee for first', 'EUR');
INSERT INTO `pricing` VALUES ('64', '1', 'additional_pages_scanning_price', '0.084', 'Additional pages scanning / 1 page', 'EUR');
INSERT INTO `pricing` VALUES ('65', '2', 'additional_pages_scanning_price', '0.084', 'Additional pages scanning / 1 page', 'EUR');
INSERT INTO `pricing` VALUES ('66', '3', 'additional_pages_scanning_price', '0.084', 'Additional pages scanning / 1 page', 'EUR');
INSERT INTO `pricing` VALUES ('67', '1', 'include_pages_scanning_number', '10', 'Include pages scanning number', 'numbers');
INSERT INTO `pricing` VALUES ('68', '2', 'include_pages_scanning_number', '10', 'Include pages scanning number', 'numbers');
INSERT INTO `pricing` VALUES ('69', '3', 'include_pages_scanning_number', '10', 'Include pages scanning number', 'numbers');

INSERT INTO `pricing` VALUES ('70', '1', 'shipping_plus', '0.2','shipping plus', 'EUR');
INSERT INTO `pricing` VALUES ('71', '2', 'shipping_plus', '0.2','shipping plus', 'EUR');
INSERT INTO `pricing` VALUES ('72', '3', 'shipping_plus', '0.2','shipping plus', 'EUR');

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
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of settings
-- ----------------------------
INSERT INTO `settings` VALUES ('1', '000001', '10', '10', null, null, '1', '1', 'Record Per Page');
INSERT INTO `settings` VALUES ('2', '000002', '10,20,50,100', '10,20,50,100', null, null, '1', '1', 'List items per page');
INSERT INTO `settings` VALUES ('3', '000003', '1', '1', 'Active', null, '1', null, 'Status');
INSERT INTO `settings` VALUES ('4', '000003', '0', '0', 'UnActive', null, '2', null, 'Status');
INSERT INTO `settings` VALUES ('5', '000004', 'new_admin', 'new_admin2', 'Administrator themes', null, '1', null, 'Administrator themes');
INSERT INTO `settings` VALUES ('6', '000005', 'new_user2', 'new_user2', 'Fronend themes', null, '1', null, 'Frontend themes');
INSERT INTO `settings` VALUES ('8', '000006', 'SMTP', 'SMTP', 'SMTP', null, '1', null, 'MAIL_PROTOCOL');
INSERT INTO `settings` VALUES ('10', '000007', '/usr/sbin/sendmail', 'ssl://smtp.googlemail.com', '/usr/sbin/sendmail', null, '1', null, 'MAIL_SENDMAIL_PATH');
INSERT INTO `settings` VALUES ('11', '000008', 'MAIL_SMTP_HOST', 'ssl://smtp.googlemail.com', 'MAIL_SMTP_HOST', null, '1', null, 'MAIL_SMTP_HOST');
INSERT INTO `settings` VALUES ('12', '000009', 'relation.test.02@gmail.com', 'relation.test.02@gmail.com', 'relation.test.02@gmail.com', null, '1', null, 'MAIL_SMTP_USER');
INSERT INTO `settings` VALUES ('14', '000010', 'relation@123', 'relation@123', 'relation@123', null, '1', null, 'MAIL_SMTP_PASS');
INSERT INTO `settings` VALUES ('15', '000011', '465', '465', '465', null, '1', null, 'MAIL_SMTP_PORT');
INSERT INTO `settings` VALUES ('16', '000012', 'virtualpost.com.de', 'virtualpost.com.de', 'virtualpost.com.de', null, '1', null, 'MAIL_ALIAS_NAME');
INSERT INTO `settings` VALUES ('17', '000013', 'relation02@gmail.com', 'relation02@gmail.com', 'relation02@gmail.com', null, '1', null, 'CONTACT_EMAIL');
INSERT INTO `settings` VALUES ('18', '000014', 'admin@localhost', 'admin@localhost12', 'admin@localhost', null, '1', null, 'MAIL_SERVER');
INSERT INTO `settings` VALUES ('19', '000015', 'Un-named Website', 'CleverMail', 'Un-named Website', null, '1', null, 'Site Name ');
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
INSERT INTO `settings` VALUES ('38', '000027', 'INV', '1', 'Immediately', null, '1', null, 'E-Mail notiﬁcation for incomming');
INSERT INTO `settings` VALUES ('39', '000027', 'CRD', '2', 'Daily', null, '2', null, 'E-Mail notiﬁcation for incomming');
INSERT INTO `settings` VALUES ('40', '000027', '1', '3', 'Weekly', null, '3', null, 'E-Mail notiﬁcation for incomming');
INSERT INTO `settings` VALUES ('41', '000027', '2', '4', 'Monthly', null, '4', null, 'E-Mail notiﬁcation for incomming');
INSERT INTO `settings` VALUES ('42', '000027', '3', '5', 'None', null, '5', null, 'E-Mail notiﬁcation for incomming');
INSERT INTO `settings` VALUES ('43', '000028', '1', '1', 'Monthly', null, '1', null, 'Invoicing cycle ');
INSERT INTO `settings` VALUES ('44', '000028', '2', '2', 'Quarterly ', null, '2', null, 'Invoicing cycle ');
INSERT INTO `settings` VALUES ('46', '000029', '1', '1', 'Daily', null, '1', null, 'Collect items for shipping\r\nCollect items for shipping\r\nCollect items for shipping');
INSERT INTO `settings` VALUES ('47', '000029', '2', '2', 'Weekly', null, '2', null, 'Collect items for shipping');
INSERT INTO `settings` VALUES ('48', '000029', '3', '3', 'Monthly', null, '3', null, 'Collect items for shipping');
INSERT INTO `settings` VALUES ('49', '000029', '4', '4', 'Quarterly ', null, '4', null, 'Collect items for shipping');
INSERT INTO `settings` VALUES ('50', '000030', '1', '1', 'Monthly', null, '1', null, 'Weekday for shipping ');
INSERT INTO `settings` VALUES ('51', '000030', '2', '2', 'Quarterly', null, '2', null, 'Weekday for shipping ');
INSERT INTO `settings` VALUES ('57', '000033', '1', '1', 'Free', null, null, null, 'Account type (1: Free | 2: Private | 3: Business)');
INSERT INTO `settings` VALUES ('58', '000033', '2', '2', 'Private', null, null, null, 'Account type (1: Free | 2: Private | 3: Business)');
INSERT INTO `settings` VALUES ('59', '000033', '3', '3', 'Business', null, null, null, 'Account type (1: Free | 2: Private | 3: Business)');
INSERT INTO `settings` VALUES ('60', '000034', '1', '1', 'Shipping Type 1', null, '1', null, 'Shipping Type');
INSERT INTO `settings` VALUES ('61', '000034', '2', '2', 'Shipping Type 2', null, '2', null, 'Shipping Type');
INSERT INTO `settings` VALUES ('62', '000035', 'http://isvat.appspot.com', 'http://isvat.appspot.com', 'VAT Number', null, '1', null, 'VAT Number Link');
INSERT INTO `settings` VALUES ('63', '000036', 'https://internetmarke.deutschepost.de/OneClickForApp?wsdl', 'https://internetmarke.deutschepost.de/OneClickForAppV2?wsdl', 'E-Stamp', null, '2', null, 'E-Stamp Link');
INSERT INTO `settings` VALUES ('64', '000037', 'pcf_07@zq4nnzgbnbvt3.webpage.t-com.de', 'pcf_07@zq4nnzgbnbvt3.webpage.t-com.de', 'E-Stamp User', null, '3', null, 'E-Stamp username');
INSERT INTO `settings` VALUES ('65', '000038', 'kQHOpCuo', 'kQHOpCuo', 'E-Stamp Password', null, '4', null, 'E-Stamp Password');
INSERT INTO `settings` VALUES ('66', '000039', 'ADHCL', 'ADHCL', 'E-Stamp PARTNER ID', null, '5', null, 'E-Stamp PARTNER ID');
INSERT INTO `settings` VALUES ('67', '000040', '1', '1', 'E-Stamp KEY PHASE', null, '6', null, 'E-Stamp KEY PHASE');
INSERT INTO `settings` VALUES ('68', '000041', 'hFIHYEnHlAnjvFsmK6OQJhHef7M4rOlo', 'hFIHYEnHlAnjvFsmK6OQJhHef7M4rOlo', 'E-Stamp PARTNER SIGNATURE', null, '7', null, 'E-Stamp PARTNER SIGNATURE');
INSERT INTO `settings` VALUES ('69', '000042', 'http://oneclickforapp.dpag.de/V2', 'http://oneclickforapp.dpag.de/V2', 'E-Stamp Namespace', null, '7', null, 'E-Stamp Namespace');
INSERT INTO `settings` VALUES ('70', '000026', null, '6', 'Category Type 6', null, null, null, null);
INSERT INTO `settings` VALUES ('71', '000043', null, 'o1bol1e5xevvpnc', 'o1bol1e5xevvpnc', null, null, null, 'Dropbox app key');
INSERT INTO `settings` VALUES ('72', '000044', null, 'xu53120l2w1zujx', 'xu53120l2w1zujx', null, null, null, 'Dropbox app secret');

INSERT INTO `settings`(SettingCode, DefaultValue, ActualValue, LabelValue, description) VALUES ('000113', '1', '1', '1', 'Enable Fedex shipping');

-- ----------------------------
-- Table structure for user_paging
-- ----------------------------
DROP TABLE IF EXISTS `user_paging`;
CREATE TABLE `user_paging` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) DEFAULT NULL,
  `setting_key` varchar(20) DEFAULT NULL,
  `setting_value` varchar(20) DEFAULT NULL,
  `user_type` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 PACK_KEYS=0 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of user_paging
-- ----------------------------
INSERT INTO `user_paging` VALUES ('1', '1', 'paging_setting', '50', '1');
INSERT INTO `user_paging` VALUES ('2', '2', 'paging_setting', '10', '1');
INSERT INTO `user_paging` VALUES ('3', '6', 'paging_setting', '10', '0');
INSERT INTO `user_paging` VALUES ('4', '7', 'paging_setting', '10', '0');
INSERT INTO `user_paging` VALUES ('5', '3', 'paging_setting', '10', '0');

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
  `location_available_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', '127.0.0.1', 'admin', 'Nguyen Trong Dung', 'e7e793ccb91033a84efea89476e8475cee644fb8', '9462e8eee0', 'admin@admin.com', 'NULL', '1268889823', null, '1268889823', '1268889823', '1384570323', '1', 'Nguyen', 'Dung', 'FF', '1112223333', '1', '1');
INSERT INTO `users` VALUES ('2', '', 'worker', 'Worker', 'e7e793ccb91033a84efea89476e8475cee644fb8', '9462e8eee0', 'worker@admin.com', null, '1268889823', null, '1268889823', '0', '1376845171', '1', 'Nguyen', 'Hai', 'FF', null, '2', '1');
INSERT INTO `users` VALUES ('7', '127.0.0.1', 'worker02', 'Nguyen', '9f4357a2faeb0623ee9b48cfea63faed17084fd1', '64f043', 'worker02@localhost.com', null, null, null, null, '1372004627', '1372004627', '1', 'Trong', 'Dung', null, null, '2', '1');

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
