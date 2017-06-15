ALTER TABLE `location`
	ADD COLUMN `location_phone_number` VARCHAR(50) NULL DEFAULT NULL ;


INSERT INTO `emails` (`code`, `slug`, `subject`, `description`, `content`, `created_date`, `created_by_type`, `created_by_id`, `last_modified_date`, `last_modified_by_type`, `last_modified_by_id`, `deleted_flag`, `language`, `relevant_enterprise_account`, `template_type`) VALUES ('0058', 'email_notify_assign_digital_panel_location_enterprise', '[DEV] Clevvermail- notification email to assign panel device to location of enterprise customer', 'notification email to assign panel device to location of enterprise customer', '<p>\r\n Hi Clevver Team,</p>\r\n<p>\r\n &nbsp;</p>\r\n<p>\r\n The enteprise customer {{customer_code}} want to add panel code to location {{location_name}}</p>\r\n<p>\r\n &nbsp;</p>\r\n<p>\r\n Please check and support it.</p>', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'English', 0, '');


