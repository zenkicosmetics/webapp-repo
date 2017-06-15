-- fixbug #371
ALTER TABLE `location`
	ADD COLUMN `language` VARCHAR(50) NULL DEFAULT NULL ;