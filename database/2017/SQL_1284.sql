

ALTER TABLE `report_by_location`
	ADD COLUMN `free_postboxes_netprice` DOUBLE NULL DEFAULT NULL AFTER `creditnote_quantity_share`,
	ADD COLUMN `private_postboxes_netprice` DOUBLE NULL DEFAULT NULL AFTER `free_postboxes_netprice`,
	ADD COLUMN `business_postboxes_netprice` DOUBLE NULL DEFAULT NULL AFTER `private_postboxes_netprice`,
	ADD COLUMN `incomming_items_free_netprice` DOUBLE NULL DEFAULT NULL AFTER `business_postboxes_netprice`,
	ADD COLUMN `incomming_items_private_netprice` DOUBLE NULL DEFAULT NULL AFTER `incomming_items_free_netprice`,
	ADD COLUMN `incomming_items_business_netprice` DOUBLE NULL DEFAULT NULL AFTER `incomming_items_private_netprice`,
	ADD COLUMN `envelope_scan_free_netprice` DOUBLE NULL DEFAULT NULL AFTER `incomming_items_business_netprice`,
	ADD COLUMN `envelope_scan_private_netprice` DOUBLE NULL DEFAULT NULL AFTER `envelope_scan_free_netprice`,
	ADD COLUMN `envelope_scan_business_netprice` DOUBLE NULL DEFAULT NULL AFTER `envelope_scan_private_netprice`,
	ADD COLUMN `item_scan_free_netprice` DOUBLE NULL DEFAULT NULL AFTER `envelope_scan_business_netprice`,
	ADD COLUMN `item_scan_private_netprice` DOUBLE NULL DEFAULT NULL AFTER `item_scan_free_netprice`,
	ADD COLUMN `item_scan_business_netprice` DOUBLE NULL DEFAULT NULL AFTER `item_scan_private_netprice`,
	ADD COLUMN `additional_pages_scanning_free_netprice` DOUBLE NULL DEFAULT NULL AFTER `item_scan_business_netprice`,
	ADD COLUMN `additional_pages_scanning_private_netprice` DOUBLE NULL DEFAULT NULL AFTER `additional_pages_scanning_free_netprice`,
	ADD COLUMN `additional_pages_scanning_business_netprice` DOUBLE NULL DEFAULT NULL AFTER `additional_pages_scanning_private_netprice`,
	ADD COLUMN `storing_letters_free_netprice` DOUBLE NULL DEFAULT NULL AFTER `additional_pages_scanning_business_netprice`,
	ADD COLUMN `storing_letters_private_netprice` DOUBLE NULL DEFAULT NULL AFTER `storing_letters_free_netprice`,
	ADD COLUMN `storing_letters_business_netprice` DOUBLE NULL DEFAULT NULL AFTER `storing_letters_private_netprice`,
	ADD COLUMN `storing_packages_free_netprice` DOUBLE NULL DEFAULT NULL AFTER `storing_letters_business_netprice`,
	ADD COLUMN `storing_packages_private_netprice` DOUBLE NULL DEFAULT NULL AFTER `storing_packages_free_netprice`,
	ADD COLUMN `storing_packages_business_netprice` DOUBLE NULL DEFAULT NULL AFTER `storing_packages_private_netprice`;
