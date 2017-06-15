ALTER TABLE `invoice_detail_manual`
	ADD COLUMN `rev_share` VARCHAR(10) NULL DEFAULT NULL AFTER `vat_case`;