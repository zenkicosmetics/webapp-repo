ALTER TABLE `postbox_settings` ADD COLUMN `always_mark_invoice` TINYINT(4) NULL DEFAULT NULL AFTER `trash_after_day`;

ALTER TABLE `envelopes` ADD COLUMN `invoice_date` BIGINT(20) NULL DEFAULT NULL AFTER `invoice_flag`;

CREATE TABLE `email_queues` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`from_email` VARCHAR(255) NULL DEFAULT NULL,
	`to_email` VARCHAR(255) NULL DEFAULT NULL,
	`send_date` BIGINT(20) NULL DEFAULT NULL,
	`attachments` VARCHAR(1024) NULL DEFAULT NULL,
	`slug` VARCHAR(255) NULL DEFAULT NULL,
	`status` TINYINT(4) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=2
;

ALTER TABLE `email_queues`  ADD `data` VARCHAR(1024) NULL DEFAULT NULL AFTER `slug`;

