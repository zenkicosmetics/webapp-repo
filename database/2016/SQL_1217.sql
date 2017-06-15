DROP TABLE IF EXISTS `cases_verification_history`;
CREATE TABLE `cases_verification_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `case_id` bigint(20) DEFAULT NULL,
  `base_task_name` varchar(255) DEFAULT NULL,
  `activity_type` tinyint(4) DEFAULT NULL,
  `activity_content` varchar(255) DEFAULT NULL,
  `activity_date` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

