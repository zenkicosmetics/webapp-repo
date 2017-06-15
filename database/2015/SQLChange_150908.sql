ALTER TABLE  `partner_digital_devices` ADD  `current_revision` INT NOT NULL AFTER  `ip` ,
ADD  `last_data_update` DATETIME NOT NULL AFTER  `current_revision`;