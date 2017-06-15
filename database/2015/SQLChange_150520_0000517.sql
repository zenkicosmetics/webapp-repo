ALTER table customers
ADD auto_send_invoice_flag tinyint default 0

-- edit table.
ALTER TABLE `envelopes_history`
	ADD COLUMN `deleted_by` VARCHAR(50) NULL AFTER `modified_date`;