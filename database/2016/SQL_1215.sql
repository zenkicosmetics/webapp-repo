ALTER TABLE  `shipping_services` ADD  `tracking_information_flag` tinyint NULL default 1 ;

UPDATE shipping_services
SET tracking_information_flag = 0;