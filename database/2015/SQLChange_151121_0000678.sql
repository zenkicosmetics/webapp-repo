-- currencies
DROP TABLE IF EXISTS `currencies`;
CREATE TABLE `currencies` (
  `currency_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `currency_name` varchar(50) DEFAULT NULL,
  `currency_short` varchar(10) DEFAULT NULL,
  `currency_sign` varchar(20) DEFAULT NULL,
  `currency_rate` decimal(10,4) DEFAULT NULL COMMENT 'Exchange rate from this currency to EUR',
  `created_date` bigint(20) NOT NULL,
  `last_updated_date` bigint(20) DEFAULT NULL,
  `active_flag` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`currency_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `currencies` VALUES ('1', 'EURO', 'EUR', '&euro;', '1.0000', '1448342179', '1448342179', '1');
INSERT INTO `currencies` VALUES ('2', 'US-Dollar', 'USD', '$', '1.0651', '1448342179', '1448342179', '1');
INSERT INTO `currencies` VALUES ('3', 'Australian Dollar', 'AUD', 'A$', '1.4763', '1448342179', '1448342179', '1');

-- location
UPDATE `location` SET `country_id` = `country`;
ALTER TABLE `location` DROP COLUMN `country`;
ALTER TABLE `location` ADD COLUMN `currency_id` int DEFAULT 0 AFTER `region`;
UPDATE `location` SET `currency_id` = 1;

-- customers
ALTER TABLE `customers` ADD COLUMN `currency_id` int DEFAULT 0 AFTER `shipment`;
UPDATE `customers` SET `currency_id` = 0;

-- country
ALTER TABLE `country` ADD COLUMN `language` VARCHAR(50) AFTER `eu_member_flag`;
ALTER TABLE `country` ADD COLUMN `currency_id` int DEFAULT 1 AFTER `language`;
UPDATE `country` SET `language` = 'English';
UPDATE `country` SET `currency_id` = 1;