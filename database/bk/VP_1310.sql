/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : virtualpost
Target Host     : localhost:3306
Target Database : virtualpost
Date: 2013-10-13 20:52:42
*/

SET FOREIGN_KEY_CHECKS=0;
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
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=latin1;

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
INSERT INTO `envelope_files` VALUES ('64', '29', '1', 'http://localhost/virtualpost/scans/todo/get_file_scan?envelope_id=29&type=2', 'http://localhost/virtualpost/mailbox/get_file_scan?envelope_id=29&type=2', 'system/virtualpost/uploads/filescan/1/DC29_C1_F.pdf', '57413', '1381670952', '2', '1381671681', '3');

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
  `additional_incomming_flag` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `envelope_summary_month_uk` (`customer_id`,`postbox_id`,`year`,`month`,`envelope_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of envelope_summary_month
-- ----------------------------
INSERT INTO `envelope_summary_month` VALUES ('42', '22', '1', '2', '2013', '09', '1', '0.00', '1', '0.00', '1', '0.50', '1', '0.50', null, null, '0');
INSERT INTO `envelope_summary_month` VALUES ('43', '23', '1', '2', '2013', '09', '1', '0.00', '0', '0.00', '0', '0.00', '0', '0.00', null, null, '0');
INSERT INTO `envelope_summary_month` VALUES ('44', '24', '1', '2', '2013', '10', '1', '0.00', '1', '0.00', '1', '0.50', '0', '0.00', null, null, '0');
INSERT INTO `envelope_summary_month` VALUES ('45', '25', '1', '2', '2013', '10', '1', '0.00', '1', '0.00', '1', '0.50', '0', '0.00', null, null, '0');
INSERT INTO `envelope_summary_month` VALUES ('46', '26', '1', '2', '2013', '10', '1', '0.00', '1', '0.00', '1', '0.50', '0', '0.00', null, null, '0');
INSERT INTO `envelope_summary_month` VALUES ('47', '23', '1', '2', '2013', '10', '0', '0.00', '1', '0.00', '1', '0.50', '0', '0.00', null, null, '0');
INSERT INTO `envelope_summary_month` VALUES ('48', '27', '1', '2', '2013', '10', '1', '0.00', '0', '0.00', '1', '0.50', '0', '0.00', null, null, '0');
INSERT INTO `envelope_summary_month` VALUES ('49', '28', '1', '2', '2013', '10', '1', '0.00', '0', '0.00', '1', '0.50', '0', '0.00', null, null, '0');
INSERT INTO `envelope_summary_month` VALUES ('50', '29', '1', '2', '2013', '10', '1', '0.00', '0', '0.00', '1', '0.50', '0', '0.00', null, null, '0');

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
  `incomming_date` bigint(20) DEFAULT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of envelopes
-- ----------------------------
INSERT INTO `envelopes` VALUES ('22', null, 'Nguyen Trong Dung', '1', '2', 'C5', '12.000', 'g', '1', '1380429703', '1380423697', '1380427025', '', '1', '0', '1', '0', null, '1', '1', '1380423870', '1', '1380429703', null, null, null, null, null, null, '1', '1', null, null, '0');
INSERT INTO `envelopes` VALUES ('23', null, 'Nguyen Trong Dung', '1', '2', 'C5', '31.000', 'g', null, null, '1380430559', '1381573719', '', '1', null, null, '0', null, '1', '1', '1381573766', null, null, null, null, null, null, null, null, '0', '1', null, null, '0');
INSERT INTO `envelopes` VALUES ('24', null, 'Nguyen Trong Dung', '1', '2', 'C4', '21.000', 'g', null, null, '1381562582', '1381576933', '', '1', null, null, '0', null, '1', '1', '1381577012', null, null, null, null, null, null, null, null, '0', '0', null, null, '0');
INSERT INTO `envelopes` VALUES ('25', null, 'Nguyen Trong Dung', '1', '2', 'C4', '21.000', 'g', null, null, '1381562833', '1381581388', '', '1', null, null, '0', null, '1', '1', '1381581332', null, null, null, null, null, null, null, null, '0', '0', null, null, '0');
INSERT INTO `envelopes` VALUES ('26', null, 'Nguyen Trong Dung', '1', '2', 'C4', '21.000', 'g', null, null, '1381562889', '1381584849', '', '1', null, null, '0', null, null, '1', '1381659677', null, null, null, null, null, null, null, null, '0', '0', null, null, '0');
INSERT INTO `envelopes` VALUES ('27', null, 'Nguyen Trong Dung', '1', '2', 'C5', '231.000', 'g', null, null, '1381670342', '1381670364', '', '1', null, null, '0', null, null, '1', '1381670403', null, null, null, null, null, null, null, null, '0', '0', null, null, '0');
INSERT INTO `envelopes` VALUES ('28', null, 'Nguyen Trong Dung', '1', '2', 'C5', '13.000', 'g', null, null, '1381670712', '1381670726', '', '1', null, null, '0', null, null, '1', '1381670767', null, null, null, null, null, null, null, null, '0', '0', null, null, '0');
INSERT INTO `envelopes` VALUES ('29', null, 'Nguyen Trong Dung', '1', '2', 'C4', '21.000', 'g', null, null, '1381670898', '1381670920', '', '1', null, null, '0', null, null, '1', '1381671723', null, null, null, null, null, null, null, null, '0', '0', null, null, '0');

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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=latin1;

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
  `additional_postbox_amount` double DEFAULT NULL,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of invoice_summary
-- ----------------------------
INSERT INTO `invoice_summary` VALUES ('1', '1', '201306', '9.95', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('2', '1', '201308', '9.95', null, null, null, null, null, null, null, null, null, null, '1', null, null, null, null, null, null, null, null, null, null, null, null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('3', '1', '201309', '24.8', null, '0', null, null, null, null, null, '0', null, null, '1', null, null, null, null, null, null, '0', null, null, null, null, null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('4', '3', '201309', '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', null, null, null, null, null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('5', '5', '201309', '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', null, null, null, null, null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('6', '6', '201309', '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', null, null, null, null, null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('7', '7', '201309', '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', null, null, null, null, null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('8', '8', '201309', '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', null, null, null, null, null, null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('9', '1', '201310', null, null, null, '0', null, null, '0', null, null, '3.5', null, null, null, '0', null, null, null, null, '0', '0', '0', '0', '0', '0', null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('10', '3', '201310', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', '0', '0', '0', '0', '0', null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('11', '5', '201310', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', '0', '0', '0', '0', '0', null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('12', '6', '201310', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', '0', '0', '0', '0', '0', null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('13', '7', '201310', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', '0', '0', '0', '0', '0', null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);
INSERT INTO `invoice_summary` VALUES ('14', '8', '201310', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0', '0', '0', '0', '0', '0', null, '0', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null);

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of postbox
-- ----------------------------
INSERT INTO `postbox` VALUES ('1', null, '1', 'DungNT', '1', '2', 'XXXX', 'VIB', '0', '1', null, '1376834021', null, '3', '20131001');
INSERT INTO `postbox` VALUES ('2', null, '1', 'Cust 1 - Post box Q', '1', '2', 'Cust 1 ', 'USOL-V', '0', null, null, '1378400690', null, null, null);
INSERT INTO `postbox` VALUES ('3', null, '3', 'Default postbox name 1 - customer03@localhost.com', '1', '1', 'Default name 1 - customer03@localhost.com', 'Default company 1 - customer03@localhost.com', '0', '1', null, null, null, null, null);
INSERT INTO `postbox` VALUES ('4', null, '5', 'Post Name 1', null, null, 'Name 1', 'Company 1', null, '1', null, null, null, null, null);
INSERT INTO `postbox` VALUES ('5', null, '6', 'Post Name 1', null, '1', 'Name 1', 'Company 1', null, '1', null, null, null, null, null);
INSERT INTO `postbox` VALUES ('6', null, '1', 'PrivatePostbox01', '1', '2', 'DungNT', 'VIB', '0', null, null, null, null, null, null);
INSERT INTO `postbox` VALUES ('7', null, '1', 'BusinessPostBox01', '1', '3', 'DungNT02', 'VIB', '0', null, null, null, null, null, null);
INSERT INTO `postbox` VALUES ('8', null, '7', '', null, '1', '', '', '0', '1', null, null, null, null, null);
INSERT INTO `postbox` VALUES ('9', null, '8', '', null, '1', '', '', '0', '1', null, null, null, null, null);

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of postbox_fee_month
-- ----------------------------
INSERT INTO `postbox_fee_month` VALUES ('1', '1', '201310', '1');

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
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=latin1;

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
INSERT INTO `pricing` VALUES ('12', '1', 'additional_incomming_items', '0.5', 'additional incomming items', '$');
INSERT INTO `pricing` VALUES ('13', '1', 'envelop_scanning', '0.2', 'envelop scanning', '$');
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
INSERT INTO `pricing` VALUES ('32', '2', 'additional_incomming_items', '0.3', 'additional incomming items', '$');
INSERT INTO `pricing` VALUES ('33', '2', 'envelop_scanning', '0.1', 'envelop scanning', '$');
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
INSERT INTO `pricing` VALUES ('52', '3', 'additional_incomming_items', '0.2', 'additional incomming items', '$');
INSERT INTO `pricing` VALUES ('53', '3', 'envelop_scanning', '0.05', 'envelop scanning', '$');
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
