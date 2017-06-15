ALTER TABLE `cases_verification_usps`
 ADD COLUMN `comment_for_registration_date` INT NULL DEFAULT NULL AFTER `comment_content`,
 ADD COLUMN `comment_for_registration_content` VARCHAR(1000) NULL DEFAULT NULL AFTER `comment_for_registration_date`;

ALTER TABLE `cases_verification_personal_identity`
 ADD COLUMN `comment_for_registration_date` INT NULL DEFAULT NULL AFTER `comment_content`,
 ADD COLUMN `comment_for_registration_content` VARCHAR(1000) NULL DEFAULT NULL AFTER `comment_for_registration_date`;

ALTER TABLE `cases_verification_company_hard`
 ADD COLUMN `comment_for_registration_date` INT NULL DEFAULT NULL AFTER `comment_content`,
 ADD COLUMN `comment_for_registration_content` VARCHAR(1000) NULL DEFAULT NULL AFTER `comment_for_registration_date`;