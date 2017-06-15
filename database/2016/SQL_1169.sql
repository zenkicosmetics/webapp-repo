ALTER TABLE `shipping_services`
	ADD COLUMN `service_type` INT(11) NULL DEFAULT 0 COMMENT '0:Both| 1:national | 2:International' AFTER `shipping_service_template`;
	
	
INSERT INTO `settings` (`SettingCode`, `DefaultValue`, `ActualValue`, `LabelValue`, `ModuleName`, `SettingOrder`, `IsRequired`, `Alias01`, `Alias02`, `Alias03`, `Alias04`, `Alias05`, `description`, `created_date`, `created_by_type`, `created_by_id`, `last_modified_date`, `last_modified_by_type`, `last_modified_by_id`, `deleted_flag`) VALUES
('000238', '0', '0', 'Both', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 'dropdown for shipping services settings', NULL, 0, 0, NULL, 0, 0, 0),
('000238', '1', '1', 'National', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'dropdown for shipping services settings', NULL, 0, 0, NULL, 0, 0, 0),
('000238', '2', '2', 'International', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'dropdown for shipping services settings', NULL, 0, 0, NULL, 0, 0, 0);



ALTER TABLE `shipping_services`
	ADD COLUMN `packaging_type` INT(11) NULL DEFAULT 1 COMMENT '1:normal letters and packages| 2:only parcels and express envelopes' AFTER `service_type`;
	
INSERT INTO `settings` (`SettingCode`, `DefaultValue`, `ActualValue`, `LabelValue`, `ModuleName`, `SettingOrder`, `IsRequired`, `Alias01`, `Alias02`, `Alias03`, `Alias04`, `Alias05`, `description`, `created_date`, `created_by_type`, `created_by_id`, `last_modified_date`, `last_modified_by_type`, `last_modified_by_id`, `deleted_flag`) VALUES
('000239', '1', '1', 'Normal letters and packages', NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, 'dropdown for shipping services settings', NULL, 0, 0, NULL, 0, 0, 0),
('000239', '2', '2', 'Only parcels and express envelopes', NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL, 'dropdown for shipping services settings', NULL, 0, 0, NULL, 0, 0, 0);
