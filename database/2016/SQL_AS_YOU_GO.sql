ALTER TABLE `invoice_summary`
	ADD COLUMN `free_postboxes_amount` DOUBLE NULL DEFAULT NULL AFTER `invoice_month`,
	ADD COLUMN `free_postboxes_quantity` INT NULL DEFAULT NULL AFTER `payment_2st_flag`,
	ADD COLUMN `free_postboxes_netprice` DOUBLE NULL DEFAULT NULL AFTER `free_postboxes_quantity`,
	ADD COLUMN `additional_free_postbox_quantity` INT NULL DEFAULT NULL AFTER `storing_packages_business_netprice`,
	ADD COLUMN `additional_free_postbox_netprice` DOUBLE NULL DEFAULT NULL AFTER `additional_free_postbox_quantity`;
ALTER TABLE `invoice_summary`
	ADD COLUMN `additional_free_postbox_amount` DOUBLE NULL DEFAULT NULL AFTER `storing_packages_business_account`;

ALTER TABLE `invoice_summary_by_location`
	ADD COLUMN `free_postboxes_amount` DOUBLE NULL DEFAULT NULL AFTER `invoice_month`,
	ADD COLUMN `free_postboxes_quantity` INT NULL DEFAULT NULL AFTER `additional_business_postbox_amount`,
	ADD COLUMN `free_postboxes_netprice` DOUBLE NULL DEFAULT NULL AFTER `free_postboxes_quantity`,
	ADD COLUMN `additional_free_postbox_quantity` INT NULL DEFAULT NULL AFTER `storing_packages_business_netprice`,
	ADD COLUMN `additional_free_postbox_netprice` DOUBLE NULL DEFAULT NULL AFTER `additional_free_postbox_quantity`;

ALTER TABLE `invoice_summary_by_location`
	ADD COLUMN `additional_free_postbox_amount` DOUBLE NULL DEFAULT NULL AFTER `storing_packages_business_account`;


