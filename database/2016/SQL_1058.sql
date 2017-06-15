-- Create table user_profiles
DROP TABLE IF EXISTS `user_profiles`;
CREATE TABLE `user_profiles` (
 `user_id` BIGINT(20) NOT NULL,
 `language` VARCHAR(50) NULL DEFAULT NULL,
 `currency_id` INT(11) NULL DEFAULT NULL,
 `length_unit` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
 `weight_unit` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
 `decimal_separator` CHAR(1) NULL DEFAULT ',' COMMENT 'Like currency, decimal separator is specific to each country and can be changed by the customer' COLLATE 'utf8_unicode_ci',
 `date_format` VARCHAR(10) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
 `created_date` BIGINT(20) NULL DEFAULT NULL,
 `modified_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`user_id`)
) COLLATE='utf8_unicode_ci' ENGINE=InnoDB;

-- Insert date format setting
INSERT INTO `settings` (`SettingCode`, `DefaultValue`, `ActualValue`, `LabelValue`, `ModuleName`, `SettingOrder`, `IsRequired`, `Alias01`, `Alias02`, `Alias03`, `Alias04`, `Alias05`, `description`) VALUES
('000230', 'd/m/Y', 'd/m/Y', 'DD/MM/YY', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'date format DD/MM/YY');
INSERT INTO `settings` (`SettingCode`, `DefaultValue`, `ActualValue`, `LabelValue`, `ModuleName`, `SettingOrder`, `IsRequired`, `Alias01`, `Alias02`, `Alias03`, `Alias04`, `Alias05`, `description`) VALUES
('000230', 'm/d/Y', 'm/d/Y', 'MM/DD/YY', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'date format MM/DD/YY');

-- Insert length unit setting
INSERT INTO `settings` (`SettingCode`, `DefaultValue`, `ActualValue`, `LabelValue`, `ModuleName`, `SettingOrder`, `IsRequired`, `Alias01`, `Alias02`, `Alias03`, `Alias04`, `Alias05`, `description`) VALUES
('000231', 'cm', 'cm', 'cm', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'length unit format cm');
INSERT INTO `settings` (`SettingCode`, `DefaultValue`, `ActualValue`, `LabelValue`, `ModuleName`, `SettingOrder`, `IsRequired`, `Alias01`, `Alias02`, `Alias03`, `Alias04`, `Alias05`, `description`) VALUES
('000231', 'inch', 'inch', 'inch', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'length unit format inch');

-- Insert weight unit setting
INSERT INTO `settings` (`SettingCode`, `DefaultValue`, `ActualValue`, `LabelValue`, `ModuleName`, `SettingOrder`, `IsRequired`, `Alias01`, `Alias02`, `Alias03`, `Alias04`, `Alias05`, `description`) VALUES
('000232', 'g', 'g', 'g', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'weight unit format g');
INSERT INTO `settings` (`SettingCode`, `DefaultValue`, `ActualValue`, `LabelValue`, `ModuleName`, `SettingOrder`, `IsRequired`, `Alias01`, `Alias02`, `Alias03`, `Alias04`, `Alias05`, `description`) VALUES
('000232', 'oz', 'oz', 'oz', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'weight unit format oz');

-- Insert decimal separator unit setting
INSERT INTO `settings` (`SettingCode`, `DefaultValue`, `ActualValue`, `LabelValue`, `ModuleName`, `SettingOrder`, `IsRequired`, `Alias01`, `Alias02`, `Alias03`, `Alias04`, `Alias05`, `description`) VALUES
('000233', ',', ',', 'Comma (,)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'decimal separator format');
INSERT INTO `settings` (`SettingCode`, `DefaultValue`, `ActualValue`, `LabelValue`, `ModuleName`, `SettingOrder`, `IsRequired`, `Alias01`, `Alias02`, `Alias03`, `Alias04`, `Alias05`, `description`) VALUES
('000233', '.', '.', 'Dot (.)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'decimal separator format ');

-- Insert pound number weight, inch number length  unit setting
INSERT INTO `settings` (`SettingCode`, `DefaultValue`, `ActualValue`, `LabelValue`, `ModuleName`, `SettingOrder`, `IsRequired`, `Alias01`, `Alias02`, `Alias03`, `Alias04`, `Alias05`, `description`) VALUES
('000234', '0.035', '0.035', '0.035', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ounce number weight unit');
INSERT INTO `settings` (`SettingCode`, `DefaultValue`, `ActualValue`, `LabelValue`, `ModuleName`, `SettingOrder`, `IsRequired`, `Alias01`, `Alias02`, `Alias03`, `Alias04`, `Alias05`, `description`) VALUES
('000235', '0.3937', '0.3937', '0.3937', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'inch number length unit ');