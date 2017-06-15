-- ----------------------------
-- Table structure for customers
-- ----------------------------
DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `customer_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `account_type` smallint(6) DEFAULT NULL,
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
INSERT INTO `customers` VALUES ('1', 'customer01@localhost.com', 'customer01@localhost.com', '123456','1', '1', '1', '1', null, null, null, null, null, null, null, '1371384148', '5ad07c37b4676576fedb738fddb2d48d');
INSERT INTO `customers` VALUES ('3', 'customer03@localhost.com', 'customer03@localhost.com', '123456','2',null, null, null, null, null, null, null, null, null, null, '1371293625', null);

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
  `deleted` tinyint(4) DEFAULT '0' COMMENT '0: actived| 1:deleted',
  PRIMARY KEY (`postbox_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of postbox
-- ----------------------------
INSERT INTO `postbox` VALUES ('1', '1', '1', 'Cust 1 - Post box 1 ', '1', 'Cust 1 ', 'USOL-V','0');
INSERT INTO `postbox` VALUES ('2', '1', '1', 'Cust 1 - Post box Q', '2', 'Cust 1 ', 'USOL-V','0');
INSERT INTO `postbox` VALUES ('3', '3', '1', 'Default postbox name 1 - customer03@localhost.com', '2', 'Default name 1 - customer03@localhost.com', 'Default company 1 - customer03@localhost.com','0');

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
INSERT INTO `settings` VALUES ('38', '000027', '1', '1', 'Immediately', null, null, null, 'Email notification Type');
INSERT INTO `settings` VALUES ('39', '000027', '2', '2', 'Daily', null, null, null, 'Email notification Type');
INSERT INTO `settings` VALUES ('40', '000027', '3', '3', 'Weekly', null, null, null, 'Email notification Type');
INSERT INTO `settings` VALUES ('41', '000027', '4', '4', 'Monthly', null, null, null, 'Email notification Type');
INSERT INTO `settings` VALUES ('42', '000027', '5', '5', 'None', null, null, null, 'Email notification Type');
INSERT INTO `settings` VALUES ('43', '000028', '1', '1', 'Monthly', null, null, null, 'Invoicing cycle Type');
INSERT INTO `settings` VALUES ('44', '000028', '2', '2', 'Quarterly', null, null, null, 'Invoicing cycle Type');
INSERT INTO `settings` VALUES ('45', '000029', '1', '1', 'Daily', null, null, null, 'Collection items Type');
INSERT INTO `settings` VALUES ('46', '000029', '2', '2', 'Weekly', null, null, null, 'Collection items Type');
INSERT INTO `settings` VALUES ('47', '000029', '3', '3', 'Monthly', null, null, null, 'Collection items Type');
INSERT INTO `settings` VALUES ('48', '000029', '4', '4', 'Quarterly', null, null, null, 'Collection items Type');
INSERT INTO `settings` VALUES ('49', '000030', '1', '1', 'Monday', null, null, null, 'Weekday for shipping');
INSERT INTO `settings` VALUES ('50', '000030', '2', '2', 'Tuesday', null, null, null, 'Weekday for shipping');
INSERT INTO `settings` VALUES ('51', '000030', '3', '3', 'Wednesday', null, null, null, 'Weekday for shipping');
INSERT INTO `settings` VALUES ('52', '000030', '4', '4', 'Thursday', null, null, null, 'Weekday for shipping');
INSERT INTO `settings` VALUES ('53', '000030', '5', '5', 'Friday', null, null, null, 'Weekday for shipping');
INSERT INTO `settings` VALUES ('54', '000031', '/uploads/images/logo/default_logo.png', '/uploads/images/logo/default_logo1.png', '/uploads/images/logo/default_logo.png', null, null, null, 'Site logo url');
INSERT INTO `settings` VALUES ('55', '000032', '0', '0', 'Available', null, null, null, 'Catalogue type (0: Available | 1: Catalogued)');
INSERT INTO `settings` VALUES ('56', '000032', '1', '1', 'Catalogued', null, null, null, 'Catalogue type (0: Available | 1: Catalogued)');
INSERT INTO `settings` VALUES ('57', '000033', '1', '1', 'Free', null, null, null, 'Account type (1: Free | 2: Private | 3: Business)');
INSERT INTO `settings` VALUES ('58', '000033', '2', '2', 'Private', null, null, null, 'Account type (1: Free | 2: Private | 3: Business)');
INSERT INTO `settings` VALUES ('59', '000033', '3', '3', 'Business', null, null, null, 'Account type (1: Free | 2: Private | 3: Business)');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of customers_location_available
-- ----------------------------
INSERT INTO `customers_location_available` VALUES 
         ('1', '1', 'Berlin', 'Musterstraße 12', '10025', 'Berlin Berlin', 'Germany'),
         ('2', '1', 'New York', 'Wall 2', '80237', 'New York', 'USA');