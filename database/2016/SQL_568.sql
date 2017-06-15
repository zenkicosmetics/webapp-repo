ALTER TABLE `paypal_transaction_hist`
 ADD COLUMN `location_id` INT NULL DEFAULT NULL AFTER `txn_id`;
 
 
ALTER TABLE `postbox`
	ADD COLUMN `deleted_date` INT NULL AFTER `deleted_flag`;

ALTER TABLE `postbox_history`
	ADD COLUMN `deleted_date` INT NULL ;



-- added 201604
DROP TABLE IF EXISTS envelope_storage_month;
CREATE TABLE `envelope_storage_month` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`envelope_id` BIGINT(20) NULL DEFAULT NULL,
	`customer_id` BIGINT(20) NULL DEFAULT NULL,
	`postbox_id` BIGINT(20) NULL DEFAULT NULL,
	`year` VARCHAR(4) NULL DEFAULT NULL,
	`month` VARCHAR(2) NULL DEFAULT NULL,
	`storage_flag` TINYINT(4) NULL DEFAULT '1' COMMENT '0:not instorage| 1: instorage',
	`location_id` INT(11) NULL DEFAULT '1',
	`created_date` DATETIME NULL DEFAULT NULL,
	`modified_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;

CREATE TABLE `invoice_summary_total_by_location` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`location_id` BIGINT(20) NULL DEFAULT NULL,
	`invoice_month` VARCHAR(8) NULL DEFAULT NULL,
	`free_postboxes_amount` DOUBLE NULL DEFAULT NULL,
	`private_postboxes_amount` DOUBLE NULL DEFAULT NULL,
	`business_postboxes_amount` DOUBLE NULL DEFAULT NULL,
	`incomming_items_free_account` DOUBLE NULL DEFAULT NULL,
	`incomming_items_private_account` DOUBLE NULL DEFAULT NULL,
	`incomming_items_business_account` DOUBLE NULL DEFAULT NULL,
	`envelope_scan_free_account` DOUBLE NULL DEFAULT NULL,
	`envelope_scan_private_account` DOUBLE NULL DEFAULT NULL,
	`envelope_scan_business_account` DOUBLE NULL DEFAULT NULL,
	`item_scan_free_account` DOUBLE NULL DEFAULT NULL,
	`item_scan_private_account` DOUBLE NULL DEFAULT NULL,
	`item_scan_business_account` DOUBLE NULL DEFAULT NULL,
	`direct_shipping_free_account` DOUBLE NULL DEFAULT NULL,
	`direct_shipping_private_account` DOUBLE NULL DEFAULT NULL,
	`direct_shipping_business_account` DOUBLE NULL DEFAULT NULL,
	`collect_shipping_free_account` DOUBLE NULL DEFAULT NULL,
	`collect_shipping_private_account` DOUBLE NULL DEFAULT NULL,
	`collect_shipping_business_account` DOUBLE NULL DEFAULT NULL,
	`storing_letters_free_account` DOUBLE NULL DEFAULT NULL,
	`storing_letters_private_account` DOUBLE NULL DEFAULT NULL,
	`storing_letters_business_account` DOUBLE NULL DEFAULT NULL,
	`storing_packages_free_account` DOUBLE NULL DEFAULT NULL,
	`storing_packages_private_account` DOUBLE NULL DEFAULT NULL,
	`storing_packages_business_account` DOUBLE NULL DEFAULT NULL,
	`forwarding_charges_postal` DOUBLE NULL DEFAULT NULL,
	`forwarding_charges_fee` DOUBLE NULL DEFAULT NULL,
	`cash_payment_for_item_delivery_amount` DOUBLE NULL DEFAULT NULL,
	`cash_payment_free_for_item_delivery_amount` DOUBLE NULL DEFAULT NULL,
	`customs_cost_import_amount` DOUBLE NULL DEFAULT NULL,
	`customs_handling_fee_import_amount` DOUBLE NULL DEFAULT NULL,
	`address_verification_amount` DOUBLE NULL DEFAULT NULL,
	`special_service_fee_in_15min_intervalls_amount` DOUBLE NULL DEFAULT NULL,
	`personal_pickup_charge_amount` DOUBLE NULL DEFAULT NULL,
	`paypal_fee` DOUBLE NULL DEFAULT NULL,
	`other_local_invoice` DOUBLE NULL DEFAULT NULL,
	`credit_note_given` DOUBLE NULL DEFAULT NULL,
	`net_total_invoice` DOUBLE NULL DEFAULT NULL,
	`gross_total_invoice` DOUBLE NULL DEFAULT NULL,
	`share_total_invoice` DOUBLE NULL DEFAULT NULL,
	`invoice_type` VARCHAR(10) NULL DEFAULT NULL,
	`additional_pages_scanning_free_amount` DOUBLE NULL DEFAULT NULL,
	`additional_pages_scanning_private_amount` DOUBLE NULL DEFAULT NULL,
	`additional_pages_scanning_business_amount` DOUBLE NULL DEFAULT NULL,
	`custom_declaration_outgoing_price_01` DECIMAL(18,10) NULL DEFAULT NULL,
	`custom_declaration_outgoing_price_02` DECIMAL(18,10) NULL DEFAULT NULL,
	`free_postboxes_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`private_postboxes_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`business_postboxes_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`incomming_items_free_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`incomming_items_private_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`incomming_items_business_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`envelope_scan_free_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`envelope_scan_private_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`envelope_scan_business_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`item_scan_free_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`item_scan_private_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`item_scan_business_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`direct_shipping_free_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`direct_shipping_private_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`direct_shipping_business_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`collect_shipping_free_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`collect_shipping_private_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`collect_shipping_business_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`storing_letters_free_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`storing_letters_private_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`storing_letters_business_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`storing_packages_free_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`storing_packages_private_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`storing_packages_business_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`forwarding_charges_postal_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`forwarding_charges_fee_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`cash_payment_for_item_delivery_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`cash_payment_free_for_item_delivery_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`customs_cost_import_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`customs_handling_fee_import_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`address_verification_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`special_service_fee_in_15min_intervalls_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`personal_pickup_charge_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`paypal_fee_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`other_local_invoice_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`credit_note_given_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`additional_pages_scanning_free_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`additional_pages_scanning_private_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`additional_pages_scanning_business_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`custom_declaration_outgoing_price_01_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`custom_declaration_outgoing_price_02_share_rev` VARCHAR(10) NULL DEFAULT NULL,
	`created_date` DATETIME NULL DEFAULT NULL,
	`created_by_type` TINYINT(4) NULL DEFAULT NULL,
	`created_by_id` BIGINT(20) NULL DEFAULT NULL,
	`last_modified_date` DATETIME NULL DEFAULT NULL,
	`last_modified_by_type` TINYINT(4) NULL DEFAULT NULL,
	`last_modified_by_id` BIGINT(20) NULL DEFAULT NULL,
	`deleted_flag` TINYINT(4) NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;

ALTER TABLE `invoice_detail_manual`
	ADD COLUMN `rev_share` VARCHAR(10) NULL DEFAULT NULL AFTER `vat_case`;

ALTER TABLE `invoice_summary_by_location`
	ADD COLUMN `rev_share` VARCHAR(10) NULL DEFAULT NULL AFTER `invoice_type`;

ALTER TABLE `location`
	ADD COLUMN `rev_share` VARCHAR(10) NULL DEFAULT NULL AFTER `country_id`;

=========================== 10/08/2016================

ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `free_postboxes_amount_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `private_postboxes_amount_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `business_postboxes_amount_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `incomming_items_free_account_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `incomming_items_private_account_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `incomming_items_business_account_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `envelope_scan_free_account_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `envelope_scan_private_account_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `envelope_scan_business_account_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `item_scan_free_account_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `item_scan_private_account_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `item_scan_business_account_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `direct_shipping_free_account_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `direct_shipping_private_account_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `direct_shipping_business_account_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `collect_shipping_free_account_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `collect_shipping_private_account_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `collect_shipping_business_account_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `storing_letters_free_account_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `storing_letters_private_account_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `storing_letters_business_account_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `storing_packages_free_account_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `storing_packages_private_account_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `storing_packages_business_account_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `forwarding_charges_postal_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `forwarding_charges_fee_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `cash_payment_for_item_delivery_amount_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `cash_payment_free_for_item_delivery_amount_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `customs_cost_import_amount_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `customs_handling_fee_import_amount_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `address_verification_amount_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `special_service_fee_in_15min_intervalls_amount_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `personal_pickup_charge_amount_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `paypal_fee_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `other_local_invoice_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `credit_note_given_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `additional_pages_scanning_free_amount_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `additional_pages_scanning_private_amount_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `additional_pages_scanning_business_amount_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `custom_declaration_outgoing_price_01_amount_share` DOUBLE NULL DEFAULT NULL;
ALTER TABLE `invoice_summary_total_by_location` ADD COLUMN `custom_declaration_outgoing_price_02_amount_share` DOUBLE NULL DEFAULT NULL;
