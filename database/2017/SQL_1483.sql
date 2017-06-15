-- Replace old data language in country table by language_code id
Update `country` JOIN `language_codes` ON `country`.`language` = `language_codes`.`code` SET `country`.`language` = `language_codes`.`id`;

-- Change data type of language column in country table
ALTER TABLE `country` MODIFY COLUMN `language`  tinyint NULL DEFAULT NULL AFTER `eu_member_flag`;