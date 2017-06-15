CREATE TABLE `location_report` (
 `id` INT NOT NULL,
 `location_id` INT NULL,
 `year` VARCHAR(4) NULL,
 `month` VARCHAR(2) NULL,
 `advertising_cost` DOUBLE NULL,
 `hardware_cost` DOUBLE NULL,
 `location_external_cost` DOUBLE NULL,
 `current_open_balance` DOUBLE NULL,
 `file_path` VARCHAR(250) NULL,
 `created_date` VARCHAR(250) NULL,
 `modified_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
;

ALTER TABLE `location_report` CHANGE COLUMN `id` `id` INT(11) NOT NULL AUTO_INCREMENT FIRST;
ALTER TABLE `location_report` ADD COLUMN `amazon_filepath`  VARCHAR(250) NULL AFTER `file_path` ;

-- add 
-- Total invoiceable so far
ALTER TABLE `location_report`
	ADD COLUMN `total_invoiceable_so_far` DOUBLE NULL DEFAULT NULL AFTER `current_open_balance`;
-- Total invoiced so far
ALTER TABLE `location_report`
	ADD COLUMN `total_invoiced_so_far` DOUBLE NULL DEFAULT NULL AFTER `total_invoiceable_so_far`;
-- Invoices written this month
ALTER TABLE `location_report`
	ADD COLUMN `invoices_written_this_month` DOUBLE NULL DEFAULT NULL AFTER `total_invoiced_so_far`;
-- Total payments made till end of this month
ALTER TABLE `location_report`
	ADD COLUMN `total_payments_made_till_end_of_this_month` DOUBLE NULL DEFAULT NULL AFTER `invoices_written_this_month`;


