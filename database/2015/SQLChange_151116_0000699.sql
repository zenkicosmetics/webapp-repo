ALTER TABLE `payment`
ADD `card_charge_flag` tinyint DEFAULT 0;

UPDATE `payment` SET `card_charge_flag` = 0;


ALTER TABLE payment_tran_hist
ADD `payment_id` bigint;