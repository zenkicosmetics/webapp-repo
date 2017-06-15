ALTER TABLE envelopes
ADD current_storage_charge_fee_day decimal(10,0) default 0;

ALTER TABLE envelopes
ADD previous_storage_charge_fee_day decimal(10,0) default 0;

INSERT INTO `settings`(SettingCode, DefaultValue, ActualValue, LabelValue, ModuleName, SettingOrder, IsRequired, Alias01, Alias02, Alias03, Alias04, Alias05, description) 
VALUES ('000090', '', '2015-05-05', null, null, '1', '1', null, null, null, null, null, 'Baseline date to calculate storage fee');

-- added flag upload s3
INSERT INTO `settings` ( `SettingCode`, `DefaultValue`, `ActualValue`, `LabelValue`, `ModuleName`, `SettingOrder`, `IsRequired`, `Alias01`, `Alias02`, `Alias03`, `Alias04`, `Alias05`, `description`) VALUES ( '000095', '1', '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'flag upload on S3');
