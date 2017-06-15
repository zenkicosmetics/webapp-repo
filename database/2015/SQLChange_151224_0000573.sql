DROP TABLE IF EXISTS `customers_forward_address`;
CREATE TABLE `customers_forward_address` (
  `id` int(12) NOT NULL,
  `customer_id` bigint(20) NOT NULL,
  `shipment_address_name` varchar(255) NOT NULL,
  `shipment_company` varchar(120) DEFAULT NULL,
  `shipment_street` varchar(255) DEFAULT NULL,
  `shipment_postcode` varchar(20) DEFAULT NULL,
  `shipment_city` varchar(60) DEFAULT NULL,
  `shipment_region` varchar(255) DEFAULT NULL,
  `shipment_country` varchar(120) DEFAULT NULL,
  `shipment_phone_number` varchar(30) DEFAULT NULL,
  `active_flag` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1: show in address book; 0: address temporary',
  `created_date` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `customers_forward_address`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `customers_forward_address`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT;

DROP TABLE IF EXISTS `customers_forward_address_hist`;
CREATE TABLE `customers_forward_address_hist` (
  `id` int(12) NOT NULL,
  `customers_forward_address_id` int(12) NOT NULL,	
  `customer_id` bigint(20) NOT NULL,
  `shipment_address_name` varchar(255) NOT NULL,
  `shipment_company` varchar(120) DEFAULT NULL,
  `shipment_street` varchar(255) DEFAULT NULL,
  `shipment_postcode` varchar(20) DEFAULT NULL,
  `shipment_city` varchar(60) DEFAULT NULL,
  `shipment_region` varchar(255) DEFAULT NULL,
  `shipment_country` varchar(120) DEFAULT NULL,
  `shipment_phone_number` varchar(30) DEFAULT NULL,
  `active_flag` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1: show in address book; 0: address temporary',
  `action_type` varchar(50) DEFAULT NULL,	
  `created_date` datetime DEFAULT NULL,
  `update_date` datetime DEFAULT NULL	
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `customers_forward_address_hist`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `customers_forward_address_hist`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT;

ALTER TABLE `envelopes` ADD `shipping_address_date` BIGINT(20) NULL AFTER `shipping_address_id`;

