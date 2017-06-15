/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : clevvermail01
Target Host     : localhost:3306
Target Database : clevvermail01
Date: 2015-09-25 00:41:15
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for cases
-- ----------------------------
DROP TABLE IF EXISTS `cases`;
CREATE TABLE `cases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `case_identifier` varchar(50) NOT NULL,
  `opening_date` int(11) DEFAULT NULL,
  `product_id` bigint(50) DEFAULT NULL,
  `country` int(11) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  `modified_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cases
-- ----------------------------
INSERT INTO `cases` VALUES ('1', '32026', '111222333', '1440547200', '111222', '208', 'This is test data', '0', '1440458584', '2015-08-25 06:23:04');
INSERT INTO `cases` VALUES ('2', '20', 'BA1111111', '1442880000', '0', '435', 'This is test data', '0', '1442914138', '2015-09-22 16:28:58');
INSERT INTO `cases` VALUES ('4', '20', 'BA1111112', '1443052800', '1', '435', 'This is test data', '0', '1443109899', '2015-09-24 22:51:39');

-- ----------------------------
-- Table structure for cases_additional_information
-- ----------------------------
DROP TABLE IF EXISTS `cases_additional_information`;
CREATE TABLE `cases_additional_information` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `case_id` bigint(20) DEFAULT NULL,
  `declaration_of_director` varchar(2000) DEFAULT NULL,
  `confirm_flag` varchar(1) DEFAULT NULL,
  `transaction_limit` int(10) DEFAULT NULL,
  `transaction_peryear` int(11) DEFAULT NULL,
  `transaction_value_peryear` double DEFAULT NULL,
  `transaction_value_limit` double DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  `updated_date` bigint(20) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cases_additional_information
-- ----------------------------

-- ----------------------------
-- Table structure for cases_checklist
-- ----------------------------
DROP TABLE IF EXISTS `cases_checklist`;
CREATE TABLE `cases_checklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `case_id` int(11) NOT NULL DEFAULT '0',
  `payment_status` tinyint(4) DEFAULT '0',
  `personal_identify` tinyint(4) DEFAULT '0',
  `company_information` tinyint(4) DEFAULT '0',
  `registration` tinyint(4) DEFAULT '0',
  `power_of_attorney` tinyint(4) DEFAULT '0',
  `modify_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cases_checklist
-- ----------------------------
INSERT INTO `cases_checklist` VALUES ('1', '2', '0', '0', '0', '0', '0', '2015-09-22 16:28:58');
INSERT INTO `cases_checklist` VALUES ('2', '3', '0', '0', '0', '0', '0', '2015-09-24 22:50:55');
INSERT INTO `cases_checklist` VALUES ('3', '4', '0', '0', '0', '0', '0', '2015-09-24 22:51:39');

-- ----------------------------
-- Table structure for cases_company_information
-- ----------------------------
DROP TABLE IF EXISTS `cases_company_information`;
CREATE TABLE `cases_company_information` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `case_id` bigint(20) DEFAULT NULL,
  `company_legal` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `post_code` varchar(10) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `country` int(11) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `purpose_of_company` varchar(4000) DEFAULT NULL,
  `registered_capital` varchar(255) DEFAULT NULL,
  `capital_paid` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `email_address` varchar(100) DEFAULT NULL,
  `registration_number` varchar(30) DEFAULT NULL,
  `created_date` int(11) DEFAULT NULL,
  `updated_date` int(11) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cases_company_information
-- ----------------------------

-- ----------------------------
-- Table structure for cases_milestone
-- ----------------------------
DROP TABLE IF EXISTS `cases_milestone`;
CREATE TABLE `cases_milestone` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `milestone_name` varchar(255) DEFAULT NULL,
  `partner_id` bigint(20) DEFAULT NULL,
  `updated_date` bigint(20) DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cases_milestone
-- ----------------------------
INSERT INTO `cases_milestone` VALUES ('2', '1', 'Payment', '2', null, '1443091431');
INSERT INTO `cases_milestone` VALUES ('3', '1', 'Personal Identity', '2', null, '1443091446');
INSERT INTO `cases_milestone` VALUES ('4', '1', 'Company Information', '2', null, '1443109587');
INSERT INTO `cases_milestone` VALUES ('5', '1', 'Document of company registration', '2', null, '1443109599');
INSERT INTO `cases_milestone` VALUES ('6', '1', 'Power of Attorney ', '2', null, '1443109625');

-- ----------------------------
-- Table structure for cases_milestone_instance
-- ----------------------------
DROP TABLE IF EXISTS `cases_milestone_instance`;
CREATE TABLE `cases_milestone_instance` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `case_id` bigint(20) DEFAULT NULL,
  `milestone_id` varchar(255) DEFAULT NULL,
  `partner_name` varchar(255) DEFAULT NULL,
  `contact_name` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  `updated_date` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cases_milestone_instance
-- ----------------------------
INSERT INTO `cases_milestone_instance` VALUES ('1', '3', '2', 'VNB', 'Nguyen Nam Phong', '0', '1443109855', null);
INSERT INTO `cases_milestone_instance` VALUES ('2', '4', '2', 'VNB', 'Nguyen Nam Phong', '0', '1443109900', null);
INSERT INTO `cases_milestone_instance` VALUES ('3', '4', '3', 'VNB', 'Nguyen Nam Phong', '0', '1443109900', null);
INSERT INTO `cases_milestone_instance` VALUES ('4', '4', '4', 'VNB', 'Nguyen Nam Phong', '0', '1443109900', null);
INSERT INTO `cases_milestone_instance` VALUES ('5', '4', '5', 'VNB', 'Nguyen Nam Phong', '0', '1443109900', null);
INSERT INTO `cases_milestone_instance` VALUES ('6', '4', '6', 'VNB', 'Nguyen Nam Phong', '0', '1443109900', null);

-- ----------------------------
-- Table structure for cases_personal_identity
-- ----------------------------
DROP TABLE IF EXISTS `cases_personal_identity`;
CREATE TABLE `cases_personal_identity` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `case_id` bigint(20) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `street_address` varchar(500) DEFAULT NULL,
  `post_code` varchar(10) DEFAULT NULL,
  `city` varchar(250) DEFAULT NULL,
  `region` varchar(250) DEFAULT NULL,
  `country` int(11) DEFAULT NULL,
  `date_of_birth` varchar(10) DEFAULT NULL,
  `place_of_birth` varchar(250) DEFAULT NULL,
  `country_of_birth` int(11) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `mobile_number` varchar(20) DEFAULT NULL,
  `email_address` varchar(100) DEFAULT NULL,
  `passport_number` varchar(20) DEFAULT NULL,
  `director_number` tinyint(4) DEFAULT '1',
  `created_date` int(11) DEFAULT NULL,
  `updated_date` int(11) DEFAULT NULL,
  `passport_local_file_path` varchar(255) DEFAULT NULL,
  `birth_certificate_local_file_path` varchar(255) DEFAULT NULL,
  `passport_amazon_file_path` varchar(255) DEFAULT NULL,
  `birth_certificate_amazon_file_path` varchar(255) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cases_personal_identity
-- ----------------------------

-- ----------------------------
-- Table structure for cases_product
-- ----------------------------
DROP TABLE IF EXISTS `cases_product`;
CREATE TABLE `cases_product` (
  `id` int(11) NOT NULL DEFAULT '0',
  `product_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cases_product
-- ----------------------------
INSERT INTO `cases_product` VALUES ('1', 'Bank Account');
INSERT INTO `cases_product` VALUES ('2', 'Company Registration');
INSERT INTO `cases_product` VALUES ('3', 'Accounting / Book');
INSERT INTO `cases_product` VALUES ('4', 'Translate Service');

-- ----------------------------
-- Table structure for cases_registration_document
-- ----------------------------
DROP TABLE IF EXISTS `cases_registration_document`;
CREATE TABLE `cases_registration_document` (
  `id` bigint(20) NOT NULL DEFAULT '0',
  `case_id` bigint(20) DEFAULT NULL,
  `registraton_document_local_file_path` varchar(255) DEFAULT NULL,
  `registraton_document_amazon_file_path` varchar(255) DEFAULT NULL,
  `translate_registraton_document_local_file_path` varchar(255) DEFAULT NULL,
  `translate_registraton_document_amazon_file_path` varchar(255) DEFAULT NULL,
  `created_date` int(11) DEFAULT NULL,
  `updated_date` int(11) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cases_registration_document
-- ----------------------------

-- ----------------------------
-- Table structure for cases_service_partner
-- ----------------------------
DROP TABLE IF EXISTS `cases_service_partner`;
CREATE TABLE `cases_service_partner` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `partner_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `main_contact_point` varchar(255) DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  `updated_date` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cases_service_partner
-- ----------------------------
INSERT INTO `cases_service_partner` VALUES ('2', 'VNB', 'namphone@gmail.com', '123456799', 'Nguyen Nam Phong', '1443087359', null);
INSERT INTO `cases_service_partner` VALUES ('3', 'ClevverMail', 'clevvermail@mail.com', '111222333444', 'ClevverMail LTD', '1443109549', null);

-- ----------------------------
-- Table structure for cases_taskname
-- ----------------------------
DROP TABLE IF EXISTS `cases_taskname`;
CREATE TABLE `cases_taskname` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `milestone_id` bigint(20) DEFAULT NULL,
  `task_name` varchar(255) DEFAULT NULL,
  `base_task_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cases_taskname
-- ----------------------------
INSERT INTO `cases_taskname` VALUES ('5', '2', 'Payment', 'payment');
INSERT INTO `cases_taskname` VALUES ('6', '3', 'Personal Identity', 'personal_identify');
INSERT INTO `cases_taskname` VALUES ('7', '4', 'Company Information', 'company_information');
INSERT INTO `cases_taskname` VALUES ('8', '5', 'Document of company registration', 'document_of_company_registration');
INSERT INTO `cases_taskname` VALUES ('9', '6', 'Power of Attorney ', 'power_of_attorney');

-- ----------------------------
-- Table structure for cases_taskname_instance
-- ----------------------------
DROP TABLE IF EXISTS `cases_taskname_instance`;
CREATE TABLE `cases_taskname_instance` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `milestone_instance_id` bigint(20) DEFAULT NULL,
  `base_task_name` varchar(255) DEFAULT NULL,
  `case_id` bigint(20) DEFAULT NULL,
  `task_name` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  `updated_date` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cases_taskname_instance
-- ----------------------------
INSERT INTO `cases_taskname_instance` VALUES ('1', '2', 'payment', '4', 'Payment', '0', '1443109900', null);
INSERT INTO `cases_taskname_instance` VALUES ('2', '3', 'personal_identify', '4', 'Personal Identity', '0', '1443109900', null);
INSERT INTO `cases_taskname_instance` VALUES ('3', '4', 'company_information', '4', 'Company Information', '0', '1443109900', null);
INSERT INTO `cases_taskname_instance` VALUES ('4', '5', 'document_of_company_registration', '4', 'Document of company registration', '0', '1443109900', null);
INSERT INTO `cases_taskname_instance` VALUES ('5', '6', 'power_of_attorney', '4', 'Power of Attorney ', '0', '1443109900', null);

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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of groups
-- ----------------------------
INSERT INTO `groups` VALUES ('0', 'supper admin', 'Supper Admin');
INSERT INTO `groups` VALUES ('1', 'admin', 'Instance Owner');
INSERT INTO `groups` VALUES ('2', 'worker', 'worker user');
INSERT INTO `groups` VALUES ('4', 'location', 'location admin');
INSERT INTO `groups` VALUES ('5', 'partner', 'Service Partner');
