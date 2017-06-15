INSERT INTO `emails` ( `slug`, `subject`, `description`, `content`) VALUES
	('email_is_notified_envelope_is_direct_deleted', '[ClevverMail] - Delete envelope nofitication', 'notify to customer when customer delete envelope directly', '<br />\r\nDear {{full_name}},<br />\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	You have been deleted an envelope from Clevvermail System.</p>\r\n<p>\r\n	Please verify this on this link {{site_url}}.</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	Best regards,</p>\r\n<p>\r\n	ClevverMail</p>'),
	('email_is_notified_envelope_is_trashed', '[ClevverMail] - Trash envelope nofitication', 'trash envelope', '<br />\r\nDear {{full_name}},<br />\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	An envelope has been trashed.</p>\r\n<p>\r\n	Please verify this on this link {{site_url}}.</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	Best regards,</p>\r\n<p>\r\n	ClevverMail</p>');


ALTER TABLE `postbox_settings`
	ADD COLUMN `inform_email_when_item_trashed` TINYINT(4) NULL DEFAULT NULL AFTER `always_forward_collect`;
