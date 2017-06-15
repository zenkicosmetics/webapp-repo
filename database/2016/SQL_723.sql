/* add field action_type to table customers_address_hist */
ALTER TABLE `customers_address_hist` ADD `action_type` VARCHAR( 50 ) NULL AFTER `invoice_address_verification_flag` ;


/* add field action_type to table postbox_history */
ALTER TABLE `postbox_history` ADD `action_type` VARCHAR( 50 ) NULL AFTER `company_verification_flag` ;

/* add field completed_delete_flag to table postbox_history */
ALTER TABLE `postbox_history` ADD `completed_delete_flag` tinyint(4) DEFAULT '0' AFTER `deleted` ;

/* add field first_location_flag to table postbox_history */
ALTER TABLE `postbox_history` ADD `created_date` BIGINT(20) NULL DEFAULT NULL COMMENT 'created date of postbox' AFTER `first_location_flag`;