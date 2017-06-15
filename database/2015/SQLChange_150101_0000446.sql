-- fixbug #446
ALTER TABLE `external_tran_hist`
	ADD COLUMN `payment_type` TINYINT NULL DEFAULT NULL COMMENT '0: payment|1:credit' AFTER `customer_id`;
	
ALTER TABLE `invoice_summary`
	ADD COLUMN `payment_type` TINYINT NULL DEFAULT NULL COMMENT '0: invoice|1:charge' AFTER `invoice_type`;