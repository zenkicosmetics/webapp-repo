-- #511 **************************************************************************************************************
ALTER TABLE `pricing`
	ADD COLUMN `type` INT NULL COMMENT '0:local service| 1:digital good | 2: shipping.' AFTER `pricing_template_id`;