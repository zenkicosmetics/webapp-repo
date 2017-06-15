ALTER TABLE `envelope_customs`
	ADD COLUMN `phone_number` VARCHAR(20) NULL DEFAULT NULL AFTER `process_flag`;