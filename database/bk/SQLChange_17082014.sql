CREATE TABLE IF NOT EXISTS `envelope_properties` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT, 
`width` decimal(15,3) DEFAULT NULL,
`height` decimal(15,3) DEFAULT NULL,
`length` decimal(15,3) DEFAULT NULL,
`envelope_id` bigint(20) DEFAULT NULL,
PRIMARY KEY (`id`), 
UNIQUE KEY `id` (`id`) USING BTREE ) 
ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 