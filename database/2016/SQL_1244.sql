ALTER TABLE `postbox_settings` 
    ADD COLUMN `standard_service_national_letter` BIGINT(20) NULL DEFAULT NULL AFTER `always_mark_invoice`,
    ADD COLUMN `standard_service_international_letter` BIGINT(20) NULL DEFAULT NULL AFTER `always_mark_invoice`,
    ADD COLUMN `standard_service_national_package` BIGINT(20) NULL DEFAULT NULL AFTER `always_mark_invoice` ,
    ADD COLUMN `standard_service_international_package` BIGINT(20) NULL DEFAULT NULL AFTER `always_mark_invoice`;



