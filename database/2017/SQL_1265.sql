ALTER TABLE `cases_verification_history`
	ADD COLUMN `activity_by` BIGINT(20) NULL DEFAULT NULL AFTER `activity_date`;
