CREATE TABLE `cases_resources` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`case_id` INT(11) NULL DEFAULT NULL,
	`base_taskname` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`local_file_path` VARCHAR(250) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`amazon_file_path` VARCHAR(250) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`seq_number` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`deleted_flag` TINYINT(4) NULL DEFAULT '0' COMMENT '1:deleted',
	`created_date` INT(11) NULL DEFAULT NULL,
	`updated_date` INT(11) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
;
CREATE TABLE `case_usps_officer` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`case_id` INT(11) NULL DEFAULT NULL,
	`name` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`rate` DOUBLE NULL DEFAULT NULL,
	`type` TINYINT(4) NULL DEFAULT NULL COMMENT '0:officer|1:owner',
	`officer_local_path` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`officer_amazon_path` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`base_taskname` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`created_date` INT(11) NULL DEFAULT NULL,
	`updated_date` INT(11) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
;
CREATE TABLE `case_usps_mail_receiver` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`case_id` INT(11) NULL DEFAULT NULL,
	`name` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`receiver_local_path` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`receiver_amazon_path` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`base_taskname` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`created_date` INT(11) NULL DEFAULT NULL,
	`updated_date` INT(11) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
;
CREATE TABLE `case_usps_business_license` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`case_id` INT(11) NULL DEFAULT NULL,
	`business_license_local_file_path` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`business_license_amazon_file_path` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`base_taskname` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`created_date` INT(11) NULL DEFAULT NULL,
	`updated_date` INT(11) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
;
CREATE TABLE `cases_proof_business` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`case_id` INT(11) NULL DEFAULT NULL,
	`description` VARCHAR(500) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`created_date` INT(11) NULL DEFAULT NULL,
	`updated_date` INT(11) NULL DEFAULT NULL,
	`status` TINYINT(4) NULL DEFAULT NULL,
	`update_by` INT(11) NULL DEFAULT NULL,
	`comment_content` VARCHAR(200) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`comment_date` INT(11) NULL DEFAULT NULL,
	`deleted_flag` TINYINT(4) NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
;
CREATE TABLE `cases_contracts` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`case_id` INT(11) NULL DEFAULT NULL,
	`status` TINYINT(4) NULL DEFAULT NULL COMMENT '0:open|1:progress|2:completed|3:reject',
	`comment_content` VARCHAR(200) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`comment_date` INT(11) NULL DEFAULT NULL,
	`created_date` INT(11) NULL DEFAULT NULL,
	`update_date` INT(11) NULL DEFAULT NULL,
	`deleted_flag` TINYINT(4) NULL DEFAULT '0' COMMENT '1:deleted',
	`update_by` INT(11) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
;
CREATE TABLE `cases_company_ems` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`case_id` INT(11) NULL DEFAULT NULL,
	`description` VARCHAR(500) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`created_date` INT(11) NULL DEFAULT NULL,
	`updated_date` INT(11) NULL DEFAULT NULL,
	`status` TINYINT(4) NULL DEFAULT NULL,
	`update_by` INT(11) NULL DEFAULT NULL,
	`comment_content` VARCHAR(200) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`comment_date` INT(11) NULL DEFAULT NULL,
	`deleted_flag` TINYINT(4) NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
;







---------------------------------- Master data------------------

INSERT INTO `cases_product_base_taskname` (`id`, `product_id`, `base_taskname`, `taskname`, `activate_flag`, `created_date`, `created_by_type`, `created_by_id`, `last_modified_date`, `last_modified_by_type`, `last_modified_by_id`, `deleted_flag`) VALUES
	(16, 5, 'proof_of_address_MS', 'proof of address MS', 1, NULL, NULL, NULL, NULL, NULL, NULL, 0),
	(17, 5, 'company_verification_E_MS', 'company verification E MS', 1, NULL, NULL, NULL, NULL, NULL, NULL, 0),
	(18, 5, 'TC_contract_MS', 'T&C contract MS', 1, NULL, NULL, NULL, NULL, NULL, NULL, 0);
	
INSERT INTO `cases_milestone` (`id`, `product_id`, `milestone_name`, `depend_milestone_id`, `partner_id`, `updated_date`, `created_date`, `cmra`, `created_by_type`, `created_by_id`, `last_modified_date`, `last_modified_by_type`, `last_modified_by_id`, `deleted_flag`) VALUES
	
	(40, 5, 'proof of address MS', 0, 7, NULL, 1475484713, 0, NULL, NULL, NULL, NULL, NULL, 0),
	(41, 5, 'company verification E MS', 0, 7, NULL, 1475484731, 0, NULL, NULL, NULL, NULL, NULL, 0),
	(42, 5, 'T&C contract MS', 0, 7, NULL, 1475484753, 0, NULL, NULL, NULL, NULL, NULL, 0);

INSERT INTO `cases_instance` (`id`, `case_instance_name`, `product_id`, `list_milestone_id`, `updated_date`, `created_date`, `created_by_type`, `created_by_id`, `last_modified_date`, `last_modified_by_type`, `last_modified_by_id`, `deleted_flag`) VALUES
	
	(39, 'proof of address MS', 5, '40', 1475484864, 1475484814, NULL, NULL, NULL, NULL, NULL, 0),
	(40, 'company verification E MS', 5, '41', 1475484855, 1475484825, NULL, NULL, NULL, NULL, NULL, 0),
	(41, 'T&C contract MS', 5, '42', 1475484848, 1475484835, NULL, NULL, NULL, NULL, NULL, 0);

INSERT INTO `cases_taskname` (`id`, `milestone_id`, `task_name`, `base_task_name`, `created_date`, `created_by_type`, `created_by_id`, `last_modified_date`, `last_modified_by_type`, `last_modified_by_id`, `deleted_flag`) VALUES
	(42, 40, 'proof of address MS', 'proof_of_address_MS', NULL, NULL, NULL, NULL, NULL, NULL, 0),
	(43, 41, 'company verification E MS', 'company_verification_E_MS', NULL, NULL, NULL, NULL, NULL, NULL, 0),
	(44, 42, 'T&C contract MS', 'TC_contract_MS', NULL, NULL, NULL, NULL, NULL, NULL, 0);

