ALTER TABLE `envelope_customs_detail`
	ADD COLUMN `hs_code` VARCHAR(50) NULL DEFAULT NULL AFTER `cost`,
	ADD COLUMN `country_origin` INT NULL DEFAULT NULL AFTER `hs_code`;
