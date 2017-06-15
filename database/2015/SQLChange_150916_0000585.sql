DROP table if exists partner_marketing_profile;
CREATE TABLE `partner_marketing_profile` (
	`partner_id` INT(11) NOT NULL,
	`duration_rev_share` DOUBLE NULL DEFAULT '0',
	`token` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`customer_discount` DOUBLE NULL DEFAULT '0',
	`partner_domain` VARCHAR(200) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`rev_share_ad` DOUBLE NULL DEFAULT '0',
	`registration` DOUBLE NULL DEFAULT '0',
	`width` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`height` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`title` VARCHAR(250) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	`activation` DOUBLE NULL DEFAULT '0',
	`script_widget` TEXT NULL COLLATE 'utf8_unicode_ci',
	`session_catch` TEXT NULL COLLATE 'utf8_unicode_ci',
	`script_landing_page` TEXT NULL COLLATE 'utf8_unicode_ci',
	`created_date` INT(11) NULL DEFAULT '0',
	`modified_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
;




INSERT INTO `settings`(SettingCode, DefaultValue, ActualValue, LabelValue, ModuleName, SettingOrder, IsRequired, Alias01, Alias02, Alias03, Alias04, Alias05, description) 
VALUES ('000103', 'widget', 'widget', 'widget', null, '1', '1', null, null, null, null, null, 'widget theme');