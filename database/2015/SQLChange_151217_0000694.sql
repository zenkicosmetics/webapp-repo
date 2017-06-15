
INSERT INTO `emails` (`slug`, `subject`, `description`, `content`) VALUES
	('new_incomming_notification_monthly', 'ClevverMail - You have received a new item', 'Email template for incoming notification ( sent by monthly)', '<br />\r\n<br />\r\n<p>\r\n	Dear, {{full_name}}</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	We have received {{total}} item(s) for you from last month.</p>\r\n<p>\r\n	Please log into your account to activate scanning or forwarding.</p>\r\n<p>\r\n	{{site_url}}</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	Best regards,</p>\r\n<p>\r\n	Your ClevverMail Team</p>\r\n<br />'),
	('new_incomming_notification_weekly', 'ClevverMail - You have received a new item', 'Email template for incoming notification ( sent by weekly)', '<br />\r\n<br />\r\n<p>\r\n	Dear, {{full_name}}</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	We have received {{total}} item(s) for you&nbsp; from last week.</p>\r\n<p>\r\n	Please log into your account to activate scanning or forwarding.</p>\r\n<p>\r\n	{{site_url}}</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	Best regards,</p>\r\n<p>\r\n	Your ClevverMail Team</p>\r\n<br />'),
	('new_incomming_notification_daily', '[DEV] ClevverMail - You have received a new item', 'Email template for incoming notification ( sent by daily)', '<br />\r\n<p>\r\n	Dear, {{full_name}}</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	We have received {{total}} item(s) for you&nbsp; from yesterday.</p>\r\n<p>\r\n	Please log into your account to activate scanning or forwarding.</p>\r\n<p>\r\n	{{site_url}}</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	Best regards,</p>\r\n<p>\r\n	Your ClevverMail Team</p>');
