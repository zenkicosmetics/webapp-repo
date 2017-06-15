ALTER TABLE `partner_receipt`
ADD COLUMN  `local_file_path` VARCHAR(250) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci' AFTER `location_id`;
--
ALTER TABLE `partner_receipt`
ADD COLUMN  `amazon_file_path` VARCHAR(250) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci' AFTER `location_id`;
