ALTER TABLE `envelope_shipping` ADD COLUMN `shipment_phone_number` VARCHAR( 30 ) NULL DEFAULT NULL AFTER `shipping_country` 

ALTER TABLE `envelope_shipping_request` ADD COLUMN `shipment_phone_number` VARCHAR( 30 ) NULL DEFAULT NULL AFTER `shipping_country` 