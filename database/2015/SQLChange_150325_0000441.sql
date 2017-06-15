-- add location id to devices table
ALTER TABLE `partner_digital_devices` ADD `location_id` INT NOT NULL AFTER `id`, ADD INDEX (`location_id`) ;
ALTER TABLE `partner_digital_devices` ADD `secure_key` VARCHAR(32) NOT NULL AFTER `description`;
ALTER TABLE `partner_digital_devices` DROP `ip`;
ALTER TABLE `partner_digital_devices` ADD `last_ping_received` DATETIME NOT NULL ;