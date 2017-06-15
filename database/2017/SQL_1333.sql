ALTER TABLE `cases_company_ems`
	ADD COLUMN `comment_for_registration_date` INT(11) NULL DEFAULT NULL AFTER `comment_date`,
	ADD COLUMN `comment_for_registration_content` VARCHAR(1000) NULL DEFAULT NULL AFTER `comment_for_registration_date`;
	
ALTER TABLE `cases_contracts`
	ADD COLUMN `comment_for_registration_date` INT(11) NULL DEFAULT NULL AFTER `comment_date`,
	ADD COLUMN `comment_for_registration_content` VARCHAR(1000) NULL DEFAULT NULL AFTER `comment_for_registration_date`;

ALTER TABLE `cases_proof_business`
	ADD COLUMN `comment_for_registration_date` INT(11) NULL DEFAULT NULL AFTER `comment_date`,
	ADD COLUMN `comment_for_registration_content` VARCHAR(1000) NULL DEFAULT NULL AFTER `comment_for_registration_date`;