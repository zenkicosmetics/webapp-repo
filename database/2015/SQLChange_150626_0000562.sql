ALTER TABLE `pricing` ADD COLUMN `rev_share_in_percent` FLOAT NULL DEFAULT NULL AFTER `type`;



update `pricing`  set `rev_share_in_percent` =50