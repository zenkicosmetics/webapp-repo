ALTER TABLE `partner_marketing_profile`
	ADD COLUMN `bonus_flag` BIT NULL DEFAULT b'0' AFTER `deleted_flag`;
ALTER TABLE `partner_marketing_profile`
	ADD COLUMN `bonus_month` INT NULL AFTER `bonus_flag`,
	ADD COLUMN `bonus_location` INT NULL AFTER `bonus_month`;

ALTER TABLE `partner_customers`
	ADD COLUMN `bonus_current_month` VARCHAR(6) NULL  AFTER `deleted_flag`,
	ADD COLUMN `bonus_month_total` INT NULL DEFAULT '0' AFTER `bonus_current_month`;


