ALTER TABLE `postbox`
	ADD COLUMN `completed_delete_flag` TINYINT(4) NULL DEFAULT '0' AFTER `deleted`;