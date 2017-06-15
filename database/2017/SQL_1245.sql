ALTER TABLE `emails`
ADD COLUMN  `template_type` VARCHAR(20);

ALTER TABLE `email_customer`
ADD COLUMN  `template_type` VARCHAR(20);
