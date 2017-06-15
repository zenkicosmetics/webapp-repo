
CREATE TABLE IF NOT EXISTS `partner_digital_devices_setting` (
  `panel_code` varchar(50) NOT NULL,
  `message_title` varchar(100) DEFAULT '',
  `message_summary` varchar(500) DEFAULT '',
  `message_fulltext` text DEFAULT '',
  `wifi_ssid` varchar(100) DEFAULT '',
  `wifi_password` varchar(100) DEFAULT '',
  PRIMARY KEY (`panel_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=68 ;
