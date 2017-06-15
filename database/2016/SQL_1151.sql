ALTER TABLE `customers` ADD `auto_trash_flag` TINYINT(2) NOT NULL DEFAULT '0' COMMENT '0: hide | 1: show' AFTER `required_verification_flag`;

ALTER TABLE `postbox_settings`
	ADD COLUMN `auto_trash_flag` TINYINT(4) NULL DEFAULT NULL AFTER `inform_email_when_item_trashed`,
	ADD COLUMN `trash_after_day` INT NULL DEFAULT NULL AFTER `auto_trash_flag`;
