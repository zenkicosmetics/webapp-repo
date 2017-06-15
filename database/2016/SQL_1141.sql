ALTER TABLE `location`
	ADD COLUMN `phone_number` VARCHAR(30) NULL DEFAULT NULL AFTER `office_space_active_flag`;