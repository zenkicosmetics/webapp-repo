ALTER TABLE invoice_summary
ADD additional_pages_scanning_free_quantity INT;

ALTER TABLE invoice_summary
ADD additional_pages_scanning_private_quantity INT;

ALTER TABLE invoice_summary
ADD additional_pages_scanning_business_quantity INT;

ALTER TABLE invoice_summary
ADD additional_pages_scanning_free_netprice double;

ALTER TABLE invoice_summary
ADD additional_pages_scanning_private_netprice double;

ALTER TABLE invoice_summary
ADD additional_pages_scanning_business_netprice double;

ALTER TABLE invoice_summary
ADD additional_pages_scanning_free_amount double;

ALTER TABLE invoice_summary
ADD additional_pages_scanning_private_amount double;

ALTER TABLE invoice_summary
ADD additional_pages_scanning_business_amount double;

-- delete old data
DELETE FROM `settings` WHERE SettingCode = '000099';

INSERT INTO `settings`(SettingCode, DefaultValue, ActualValue, LabelValue, ModuleName, SettingOrder, IsRequired, Alias01, Alias02, Alias03, Alias04, Alias05, description) 
VALUES ('000099', '', 'C:/wamp/www/virtualpost/system/virtualpost/tools/window/bin64/pdfinfo.exe', null, null, '1', '1', null, null, null, null, null, 'PDF Diectory information');

-- DEV Server
INSERT INTO `settings`(SettingCode, DefaultValue, ActualValue, LabelValue, ModuleName, SettingOrder, IsRequired, Alias01, Alias02, Alias03, Alias04, Alias05, description) 
VALUES ('000099', '', '/home/developer/www/app/system/virtualpost/tools/linux/bin64/pdfinfo', null, null, '1', '1', null, null, null, null, null, 'PDF Diectory information');