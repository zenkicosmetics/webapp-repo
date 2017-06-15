ALTER TABLE cases
ADD COLUMN postbox_id INT;

ALTER TABLE cases_verification_settings
ADD COLUMN location_id INT;


ALTER TABLE cases_verification_settings
ADD COLUMN setting_type INT;


ALTER TABLE cases_product_base_taskname
ADD COLUMN activate_flag TINYINT;

-- ----------------------------
-- Table structure for cases_instance
-- ----------------------------
DROP TABLE IF EXISTS `cases_instance`;
CREATE TABLE `cases_instance` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `case_instance_name` varchar(250) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `list_milestone_id`  varchar(250) DEFAULT NULL,
  `updated_date` bigint(20) DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for cases_personal_identity
-- ----------------------------
ALTER TABLE `cases_personal_identity`
	ADD COLUMN `driver_license_file_path` VARCHAR(255) NULL DEFAULT NULL AFTER `birth_certificate_amazon_file_path`,
	ADD COLUMN `driver_license_amazon_file_path` VARCHAR(255) NULL DEFAULT NULL AFTER `driver_license_file_path`;
	
ALTER TABLE `cases_service_partner`
	ADD COLUMN `clevvermail_flag` TINYINT NULL DEFAULT '0' AFTER `main_contact_point`;




ALTER TABLE `cases`
	ADD COLUMN `deleted_flag` TINYINT NULL DEFAULT '0' AFTER `postbox_id`;


ALTER TABLE `cases_verification_company_hard`
	ADD COLUMN `deleted_flag` TINYINT NULL DEFAULT '0';
ALTER TABLE `cases_verification_personal_identity`
	ADD COLUMN `deleted_flag` TINYINT NULL DEFAULT '0' ;
ALTER TABLE `cases_verification_usps`
	ADD COLUMN `deleted_flag` TINYINT NULL DEFAULT '0';
	
	


ALTER TABLE `cases_verification_company_hard` ADD COLUMN `updated_by` BIGINT;

ALTER TABLE `cases_verification_personal_identity` ADD COLUMN `updated_by` BIGINT;

ALTER TABLE `cases_verification_usps` ADD COLUMN `updated_by` BIGINT;

ALTER TABLE `cases_personal_identity` ADD COLUMN `updated_by` BIGINT;

ALTER TABLE `cases_company_information` ADD COLUMN `updated_by` BIGINT;

ALTER TABLE `cases_registration_document` ADD COLUMN `updated_by` BIGINT;

ALTER TABLE `cases_milestone_instance` ADD COLUMN `updated_by` BIGINT;
	
ALTER TABLE `cases_verification_usps`
	ADD COLUMN `country_of_applicant` VARCHAR(12) NULL DEFAULT NULL AFTER `postcode_of_applicant`,
	ADD COLUMN `country_of_corporation` VARCHAR(255) NULL DEFAULT NULL AFTER `phone_of_corporation`;