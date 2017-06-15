ALTER TABLE `envelopes` ADD `shipping_rate`  VARCHAR( 10 ) NULL AFTER `shipping_id` ;
ALTER TABLE `envelopes` ADD `shipping_rate_id` INT( 5 ) NULL AFTER `shipping_rate` ;