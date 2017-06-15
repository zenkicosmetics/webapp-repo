ALTER TABLE `vat_case`
	ADD COLUMN `type` INT NULL COMMENT '0: no vat | 1: EU vat| 2: germany vat' AFTER `baseon_country_id`;
ALTER TABLE `vat_case`
	ADD COLUMN `notes` VARCHAR(1000) NULL AFTER `type`;

CREATE TABLE `location_pricing` (
	`location_id` INT NOT NULL,
	`pricing_template_id` INT NOT NULL,
	PRIMARY KEY (`location_id`, `pricing_template_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;

CREATE TABLE `vat_case_standard` (
	`country_id` INT(11) NOT NULL,
	`rate` FLOAT NULL DEFAULT NULL,
	PRIMARY KEY (`country_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;

ALTER TABLE `pricing_template`
	CHANGE COLUMN `id` `id` INT(10) NOT NULL AUTO_INCREMENT FIRST;

INSERT INTO `pricing_template` (`id`, `name`, `description`) VALUES (0, 'Default template', 'default template');


insert into vat_case_standard (country_id) select id from country where eu_member_flag=1;
update vat_case_standard set rate = (select rate from vat_case where vat_case.baseon_country_id=vat_case_standard.country_id and vat_case.product_type='local service' limit 1);

insert into vat_case_standard (country_id) select baseon_country_id from vat_case where product_type='shipping' and customer_type='private';
update vat_case_standard set rate = (select rate from vat_case where vat_case.baseon_country_id=vat_case_standard.country_id and customer_type='private' limit 1);



-- ! #IMPORTANT: MAINTAIN DATA INTO location_pricing