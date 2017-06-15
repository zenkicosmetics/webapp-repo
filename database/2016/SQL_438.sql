
/*`no_tracking_number` tinyint(2) NOT NULL DEFAULT '0',*/

DROP TABLE IF EXISTS `envelope_shipping_tracking`;
CREATE TABLE `envelope_shipping_tracking` (
  `id` int(11) NOT NULL,
  `envelope_id` int(11) NOT NULL,
  `tracking_number` varchar(300) NOT NULL,
  `shipping_services_id` int(11) NOT NULL,
  `created_date` bigint(20) NOT NULL,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `envelope_shipping_tracking`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `envelope_shipping_tracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `envelopes` ADD `tracking_number_flag` TINYINT(2) NOT NULL DEFAULT '0' AFTER `deleted_flag`;

ALTER TABLE `envelope_shipping_tracking` ADD `package_id` INT(12) NULL DEFAULT NULL AFTER `shipping_services_id`;