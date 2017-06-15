ALTER TABLE `location`
 ADD COLUMN `sent_daily_reminder_flag` TINYINT NULL DEFAULT '0' AFTER `phone_number`;