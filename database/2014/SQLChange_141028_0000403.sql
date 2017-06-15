-- ======================================================================
-- INSERT trigger email for chec expired date of credit card
-- ======================================================================
DELETE FROM `emails` WHERE `slug` IN ('email_is_confirmed_card_expired_date_remain_thirty_days', 'email_is_confirmed_card_expired_date_remain_sixty_days', 'email_change_new_payment_method_standard');
INSERT INTO `emails` (`slug`, `subject`, `description`, `content`) VALUES
	('email_is_confirmed_card_expired_date_remain_thirty_days', '[ClevverMail]  expired date of your credit card', 'e-mail is comfirmed expired date of credit card.', '<br />\r\n<p>\r\n	Dear {{full_name}},</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	After 30 days, your credit card has expired date.</p>\r\n<p>\r\n	Please update your credit card.</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	Best regards,</p>\r\n<p>\r\n	ClevverMail</p>'),
	('email_is_confirmed_card_expired_date_remain_sixty_days', '[ClevverMail]  expired date of your credit card', 'e-mail is comfirmed expired date of credit card.', '<br />\r\n<br />\r\n<p>\r\n	Dear {{full_name}},</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	After 60 days, your credit card has expired date.</p>\r\n<p>\r\n	Please update your credit card.</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	Best regards,</p>\r\n<p>\r\n	ClevverMail</p>'),
	('email_change_new_payment_method_standard', '[Clevermail] new payment method selected as standard', 'e-mail (new payment method selected as standard)', '<p>\r\n	Dear ,</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	Your credit card has expired date.</p>\r\n<p>\r\n	We will change new payment method to another credit card.</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	Please access to this site {{site_url}} to verify.</p>\r\n<p>\r\n	&nbsp;</p>\r\n<p>\r\n	Beat Regards,</p>\r\n<p>\r\n	ClevverMail Team</p>\r\n<p>\r\n	&nbsp;</p>');


