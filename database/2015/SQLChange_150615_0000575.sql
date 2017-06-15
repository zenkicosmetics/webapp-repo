ALTER TABLE `partner_digital_devices` ADD `timezone` VARCHAR(50) NOT NULL AFTER `description`;

ALTER TABLE `partner_digital_devices` ADD `type` VARCHAR(30) NOT NULL AFTER `location_id`;

UPDATE `partner_digital_devices` SET `type` = 'clevverboard';

ALTER TABLE `partner_digital_devices` ADD `ip` VARCHAR(15) NOT NULL AFTER `secure_key`;

CREATE TABLE IF NOT EXISTS `cases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `case_type` varchar(50) NOT NULL,
  `data` blob,
  `created_date` bigint(20) DEFAULT NULL,
  `last_updated_date` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

ALTER TABLE `cases` ADD `case_identifier` VARCHAR(50) NOT NULL AFTER `user_id`;

ALTER TABLE `cases` ADD `status` VARCHAR(20) NOT NULL AFTER `data`;
