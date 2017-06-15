-- ======================================================================
-- INSERT  postbox table
-- ======================================================================
ALTER TABLE `postbox`
	ADD COLUMN `first_location_flag` TINYINT NULL DEFAULT NULL AFTER `plan_date_change_postbox_type`;

