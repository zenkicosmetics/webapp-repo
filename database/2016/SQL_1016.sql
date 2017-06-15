DROP TABLE IF EXISTS `location_envelope_types`;
CREATE TABLE `location_envelope_types` (
  `id` int(12) NOT NULL,
  `location_id` int(12) DEFAULT NULL COMMENT 'Primary of location',
  `type_id` varchar(10) DEFAULT NULL COMMENT 'Envelope category type of settings'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `location_envelope_types`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `location_envelope_types`
  MODIFY `id` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;