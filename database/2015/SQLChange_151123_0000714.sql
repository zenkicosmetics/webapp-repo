ALTER TABLE `partner_digital_devices`
  ADD `type` VARCHAR(30) NOT NULL DEFAULT 0 AFTER `location_id`;

UPDATE `partner_digital_devices` SET `type` = 'clevverboard';

ALTER TABLE `partner_digital_devices`
  ADD `timezone` VARCHAR(50) NOT NULL AFTER `description`;

UPDATE `partner_digital_devices` SET `timezone` = 'Europe/Berlin';

ALTER TABLE `partner_digital_devices` MODIFY `created_date` BIGINT;
