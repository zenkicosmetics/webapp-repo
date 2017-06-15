ALTER TABLE country
ADD COLUMN risk_class tinyint;

ALTER TABLE country
ADD COLUMN invoice_address_verification tinyint;

ALTER TABLE country
ADD COLUMN private_postbox_verification tinyint;

ALTER TABLE country
ADD COLUMN business_postbox_verification tinyint;

UPDATE country SET risk_class = 3, invoice_address_verification = 0, private_postbox_verification =0, business_postbox_verification=0 ;

ALTER TABLE customers_address
ADD COLUMN invoice_address_verification_flag tinyint NOT NULL DEFAULT 1;

ALTER TABLE customers_address_hist
ADD COLUMN invoice_address_verification_flag tinyint NOT NULL DEFAULT 1;

UPDATE customers_address SET invoice_address_verification_flag = 1;

UPDATE customers_address_hist SET invoice_address_verification_flag = 1;

ALTER TABLE postbox
ADD COLUMN name_verification_flag tinyint NOT NULL DEFAULT 1;

ALTER TABLE postbox
ADD COLUMN company_verification_flag tinyint NOT NULL DEFAULT 1;

ALTER TABLE postbox_history
ADD COLUMN name_verification_flag tinyint NOT NULL DEFAULT 1;

ALTER TABLE postbox_history
ADD COLUMN company_verification_flag tinyint NOT NULL DEFAULT 1;

UPDATE postbox SET company_verification_flag = 1, name_verification_flag = 1;

UPDATE postbox_history SET company_verification_flag = 1, name_verification_flag = 1;


/*
Navicat MySQL Data Transfer
Source Host     : localhost:3306
Source Database : clevvermail01
Target Host     : localhost:3306
Target Database : clevvermail01
Date: 2015-11-14 09:40:53
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cases
-- ----------------------------


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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cases_checklist
-- ----------------------------

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
  `comment_date` bigint(20) DEFAULT NULL,
  `comment_content` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cases_company_information
-- ----------------------------

-- ----------------------------
-- Table structure for cases_company_shareholder_verification
-- ----------------------------
DROP TABLE IF EXISTS `cases_company_shareholder_verification`;
CREATE TABLE `cases_company_shareholder_verification` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `case_id` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cases_company_shareholder_verification
-- ----------------------------

-- ----------------------------
-- Table structure for cases_milestone
-- ----------------------------
DROP TABLE IF EXISTS `cases_milestone`;
CREATE TABLE `cases_milestone` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `milestone_name` varchar(255) DEFAULT NULL,
  `depend_milestone_id` int(11) DEFAULT NULL,
  `partner_id` bigint(20) DEFAULT NULL,
  `updated_date` bigint(20) DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cases_milestone
-- ----------------------------
INSERT INTO `cases_milestone` VALUES ('1', '1', 'Payment', null, '2', null, '1443091431');
INSERT INTO `cases_milestone` VALUES ('2', '1', 'Personal Identity', '2', '5', '1444059939', '1443091446');
INSERT INTO `cases_milestone` VALUES ('3', '1', 'Company Information', '2', '5', '1444038703', '1443109587');
INSERT INTO `cases_milestone` VALUES ('4', '1', 'Document of company registration', '0', '5', '1444227647', '1443109599');
INSERT INTO `cases_milestone` VALUES ('5', '1', 'Power of Attorney ', '0', '5', '1444227659', '1443109625');
INSERT INTO `cases_milestone` VALUES ('6', '5', 'Personal identification', '0', '5', null, '1445613561');
INSERT INTO `cases_milestone` VALUES ('7', '5', 'Company identification soft', '0', '5', null, '1445613893');
INSERT INTO `cases_milestone` VALUES ('8', '5', 'Company identification hard', '0', '5', null, '1445613996');
INSERT INTO `cases_milestone` VALUES ('9', '5', 'Special Form PS1583', '0', '5', null, '1445614367');

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
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cases_milestone_instance
-- ----------------------------

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
  `comment_date` bigint(20) DEFAULT NULL,
  `comment_content` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

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
INSERT INTO `cases_product` VALUES ('5', 'Verification');

-- ----------------------------
-- Table structure for cases_product_base_taskname
-- ----------------------------
DROP TABLE IF EXISTS `cases_product_base_taskname`;
CREATE TABLE `cases_product_base_taskname` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `base_taskname` varchar(255) DEFAULT NULL,
  `taskname` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cases_product_base_taskname
-- ----------------------------
INSERT INTO `cases_product_base_taskname` VALUES ('1', '1', 'payment', 'Payment');
INSERT INTO `cases_product_base_taskname` VALUES ('2', '1', 'personal_identify', 'Personal Identity');
INSERT INTO `cases_product_base_taskname` VALUES ('3', '1', 'company_information', 'Company Information');
INSERT INTO `cases_product_base_taskname` VALUES ('4', '1', 'document_of_company_registration', 'Document of company registration');
INSERT INTO `cases_product_base_taskname` VALUES ('5', '1', 'power_of_attorney', 'Power of Attorney ');
INSERT INTO `cases_product_base_taskname` VALUES ('6', '5', 'verification_personal_identification', 'Personal identification');
INSERT INTO `cases_product_base_taskname` VALUES ('7', '5', 'verification_company_identification_soft', 'Company identification soft');
INSERT INTO `cases_product_base_taskname` VALUES ('8', '5', 'verification_company_identification_hard', 'Company identification hard');
INSERT INTO `cases_product_base_taskname` VALUES ('9', '5', 'verification_special_form_PS1583', 'Special Form PS1583');

-- ----------------------------
-- Table structure for cases_registration_document
-- ----------------------------
DROP TABLE IF EXISTS `cases_registration_document`;
CREATE TABLE `cases_registration_document` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `case_id` bigint(20) DEFAULT NULL,
  `registraton_document_local_file_path` varchar(255) DEFAULT NULL,
  `registraton_document_amazon_file_path` varchar(255) DEFAULT NULL,
  `translate_registraton_document_local_file_path` varchar(255) DEFAULT NULL,
  `translate_registraton_document_amazon_file_path` varchar(255) DEFAULT NULL,
  `created_date` int(11) DEFAULT NULL,
  `updated_date` int(11) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `comment_date` bigint(20) DEFAULT NULL,
  `comment_content` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cases_registration_document
-- ----------------------------

-- ----------------------------
-- Table structure for cases_service_partner
-- ----------------------------
DROP TABLE IF EXISTS `cases_service_partner`;
CREATE TABLE `cases_service_partner` (
  `id` bigint(20) NOT NULL,
  `partner_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `main_contact_point` varchar(255) DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  `updated_date` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cases_service_partner
-- ----------------------------
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
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cases_taskname
-- ----------------------------
INSERT INTO `cases_taskname` VALUES ('5', '1', 'Payment', 'payment');
INSERT INTO `cases_taskname` VALUES ('6', '2', 'Personal Identity', 'personal_identify');
INSERT INTO `cases_taskname` VALUES ('7', '3', 'Company Information', 'company_information');
INSERT INTO `cases_taskname` VALUES ('8', '4', 'Document of company registration', 'document_of_company_registration');
INSERT INTO `cases_taskname` VALUES ('9', '5', 'Power of Attorney ', 'power_of_attorney');
INSERT INTO `cases_taskname` VALUES ('10', '6', 'Personal identification', 'verification_personal_identification');
INSERT INTO `cases_taskname` VALUES ('11', '7', 'Company identification soft', 'verification_company_identification_soft');
INSERT INTO `cases_taskname` VALUES ('12', '8', 'Company identification hard', 'verification_company_identification_hard');
INSERT INTO `cases_taskname` VALUES ('13', '9', 'Special Form PS1583', 'verification_special_form_PS1583');

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
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cases_taskname_instance
-- ----------------------------
-- ----------------------------
-- Table structure for cases_verification_company_hard
-- ----------------------------
DROP TABLE IF EXISTS `cases_verification_company_hard`;
CREATE TABLE `cases_verification_company_hard` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `case_id` bigint(20) DEFAULT NULL,
  `verification_local_file_path` varchar(255) DEFAULT NULL,
  `verification_amazon_file_path` varchar(255) DEFAULT NULL,
  `shareholders_name_01` varchar(100) DEFAULT NULL,
  `shareholders_rate_01` decimal(10,2) DEFAULT NULL,
  `shareholders_local_file_path_01` varchar(255) DEFAULT NULL,
  `shareholders_amazon_file_path_01` varchar(255) DEFAULT NULL,
  `shareholders_name_02` varchar(100) DEFAULT NULL,
  `shareholders_rate_02` decimal(10,2) DEFAULT NULL,
  `shareholders_local_file_path_02` varchar(255) DEFAULT NULL,
  `shareholders_amazon_file_path_02` varchar(255) DEFAULT NULL,
  `shareholders_name_03` varchar(100) DEFAULT NULL,
  `shareholders_rate_03` decimal(10,2) DEFAULT NULL,
  `shareholders_local_file_path_03` varchar(255) DEFAULT NULL,
  `shareholders_amazon_file_path_03` varchar(255) DEFAULT NULL,
  `shareholders_name_04` varchar(100) DEFAULT NULL,
  `shareholders_rate_04` decimal(10,2) DEFAULT NULL,
  `shareholders_local_file_path_04` varchar(255) DEFAULT NULL,
  `shareholders_amazon_file_path_04` varchar(255) DEFAULT NULL,
  `created_date` int(11) DEFAULT NULL,
  `updated_date` int(11) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `comment_date` int(11) DEFAULT NULL,
  `comment_content` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cases_verification_company_hard
-- ----------------------------

-- ----------------------------
-- Table structure for cases_verification_personal_identity
-- ----------------------------
DROP TABLE IF EXISTS `cases_verification_personal_identity`;
CREATE TABLE `cases_verification_personal_identity` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) DEFAULT NULL,
  `case_id` bigint(20) DEFAULT NULL,
  `verification_local_file_path` varchar(255) DEFAULT NULL,
  `verification_amazon_file_path` varchar(255) DEFAULT NULL,
  `type` tinyint(4) DEFAULT NULL COMMENT '1: personal_identification | 2: company soft',
  `created_date` int(11) DEFAULT NULL,
  `updated_date` int(11) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `comment_date` int(11) DEFAULT NULL,
  `comment_content` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cases_verification_personal_identity
-- ----------------------------

-- ----------------------------
-- Table structure for cases_verification_usps
-- ----------------------------
DROP TABLE IF EXISTS `cases_verification_usps`;
CREATE TABLE `cases_verification_usps` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `case_id` bigint(20) DEFAULT NULL,
  `name_to_delivery` varchar(255) DEFAULT NULL,
  `name_of_applicant` varchar(255) DEFAULT NULL,
  `street_of_applicant` varchar(255) DEFAULT NULL,
  `city_of_applicant` varchar(255) DEFAULT NULL,
  `region_of_applicant` varchar(255) DEFAULT NULL,
  `postcode_of_applicant` varchar(12) DEFAULT NULL,
  `phone_of_applicant` varchar(30) DEFAULT NULL,
  `id_of_applicant` varchar(50) DEFAULT NULL,
  `license_of_applicant` varchar(50) DEFAULT NULL,
  `name_of_corporation` varchar(255) DEFAULT NULL,
  `street_of_corporation` varchar(255) DEFAULT NULL,
  `city_of_corporation` varchar(255) DEFAULT NULL,
  `region_of_corporation` varchar(255) DEFAULT NULL,
  `postcode_of_corporation` varchar(255) DEFAULT NULL,
  `phone_of_corporation` varchar(255) DEFAULT NULL,
  `business_type_of_corporation` varchar(255) DEFAULT NULL,
  `note1` varchar(1000) DEFAULT NULL,
  `note2` varchar(1000) DEFAULT NULL,
  `note3` varchar(1000) DEFAULT NULL,
  `created_date` int(11) DEFAULT NULL,
  `updated_date` int(11) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `comment_date` int(11) DEFAULT NULL,
  `comment_content` varchar(1000) DEFAULT NULL,
  `verification_local_file_path` varchar(255) DEFAULT NULL,
  `verification_amazon_file_path` varchar(255) DEFAULT NULL,
  `id_of_applicant_local_file_path` varchar(255) DEFAULT NULL,
  `id_of_applicant_amazon_file_path` varchar(255) DEFAULT NULL,
  `license_of_applicant_local_file_path` varchar(255) DEFAULT NULL,
  `license_of_applicant_amazon_file_path` varchar(255) DEFAULT NULL,
  `additional_local_file_path` varchar(255) DEFAULT NULL,
  `additional_amazon_file_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of cases_verification_usps
-- ----------------------------

ALTER TABLE `cases_milestone` ADD `cmra` BIGINT(20) DEFAULT NULL ;