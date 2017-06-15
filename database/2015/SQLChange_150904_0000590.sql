ALTER TABLE `envelopes`
	ADD COLUMN `remarked_flag` TINYINT NULL DEFAULT '0' COMMENT '0: nothing| 1: yellow|2: red|3: green' AFTER `envelope_scan_date`;
	
ALTER TABLE `envelopes_completed`
	ADD COLUMN `remarked_flag` TINYINT NULL DEFAULT '0' COMMENT '0: nothing| 1: yellow|2: red|3: green' AFTER `location_id`;

ALTER TABLE `envelopes_completed_history`
	ADD COLUMN `remarked_flag` TINYINT NULL DEFAULT '0' COMMENT '0: nothing| 1: yellow|2: red|3: green' AFTER `location_id`;

ALTER TABLE `envelopes_history`
	ADD COLUMN `remarked_flag` TINYINT NULL DEFAULT '0' COMMENT '0: nothing| 1: yellow|2: red|3: green' AFTER `location_id`;
