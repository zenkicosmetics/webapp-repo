INSERT INTO `settings`(`SettingCode`, `DefaultValue`, `ActualValue`, `LabelValue`, `ModuleName`, `SettingOrder`, `IsRequired`, `description`) VALUES ('000045', '0.2', '0.2', '0.2', null, null, null, 'Shipping&Handding plus');
ALTER TABLE `envelope_shipping` ADD `shipping_fee` DECIMAL( 10, 2 ) NULL ;
ALTER TABLE `customers` ADD `email_confirm_flag` tinyint(4) default 0 NULL ;
