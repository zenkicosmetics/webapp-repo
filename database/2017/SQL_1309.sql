/* Create table in target */
DROP TABLE IF EXISTS `customer_history`;
CREATE TABLE `customer_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) NOT NULL,
  `action_type` varchar(80) NOT NULL,
  `old_data` text,
  `current_data` text,
  `created_by_id` bigint(20) NOT NULL COMMENT '-1: cronjob; 0: user change themselves; > 0 : admin id',
  `created_date` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=157 DEFAULT CHARSET=utf8;
