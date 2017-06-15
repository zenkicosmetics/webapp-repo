/* Create table in target */
CREATE TABLE `shipping_credentials`(
	`id` bigint(20) NOT NULL  auto_increment , 
	`name` varchar(80) COLLATE utf8_general_ci NULL  , 
	`description` text COLLATE utf8_general_ci NULL  , 
	`account_no` varchar(80) COLLATE utf8_general_ci NULL  , 
	`meter_no` varchar(80) COLLATE utf8_general_ci NULL  , 
	`auth_key` varchar(80) COLLATE utf8_general_ci NULL  , 
	`username` varchar(80) COLLATE utf8_general_ci NULL  , 
	`password` varchar(80) COLLATE utf8_general_ci NULL  , 
	`estamp_partner_signature` varchar(255) COLLATE utf8_general_ci NULL  , 
	`estamp_namespace` varchar(255) COLLATE utf8_general_ci NULL  , 
	`partner_id` bigint(20) NULL  , 
	`percental_partner_upcharge` decimal(18,2) NULL  , 
	PRIMARY KEY (`id`) 
) ENGINE=InnoDB DEFAULT CHARSET='utf8' COLLATE='utf8_general_ci';


/* Alter table in target */
ALTER TABLE `shipping_services` 
	ADD COLUMN `shipping_api_code` varchar(512)  COLLATE utf8_general_ci NULL after `tracking_information_flag` , 
	ADD COLUMN `shipping_api_credential` varchar(512)  COLLATE utf8_general_ci NULL after `shipping_api_code` ;

