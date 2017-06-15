

CREATE TABLE `tolist_clicklog` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`envelope_id` INT NULL,
	`envelope_code` VARCHAR(50) NULL,
	`user_id` INT NULL,
	`status` TINYINT NULL,
	`modified_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
;
