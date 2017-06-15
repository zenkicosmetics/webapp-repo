ALTER TABLE `users`
 ADD COLUMN `sent_notification_customer_flag` TINYINT(4) NULL DEFAULT '0' AFTER `location_available_id`,
 ADD COLUMN `info_email` VARCHAR(100) NULL DEFAULT NULL AFTER `sent_notification_customer_flag`;