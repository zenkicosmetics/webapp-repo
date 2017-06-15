ALTER TABLE `pricing`
ADD COLUMN  `item_value_owner` VARCHAR(100) AFTER item_value;

ALTER TABLE `pricing`
ADD COLUMN  `item_value_special` VARCHAR(100) AFTER item_value_owner;

ALTER TABLE `pricing`
ADD COLUMN  `item_value_owner_special` VARCHAR(100) AFTER item_value_special;


UPDATE pricing
SET item_value_owner = item_value, item_value_special = item_value, item_value_owner_special=item_value;

update pricing_template set pricing_type ='Clevver';
---------------
-- 24/05/2017
---------------

ALTER TABLE pricing_template
ADD COLUMN pricing_type VARCHAR(20);

ALTER TABLE location
ADD COLUMN enterprise_pricing_template_id int;

ALTER TABLE location
ADD COLUMN next_enterprise_pricing_template_id int;

ALTER TABLE invoice_detail
ADD COLUMN product_id int;

ALTER TABLE invoice_detail
ADD COLUMN product_type VARCHAR(20);

ALTER TABLE `invoice_detail`
	ADD COLUMN `show_flag` TINYINT NULL DEFAULT '1' AFTER `product_type`;
