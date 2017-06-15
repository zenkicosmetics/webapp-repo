ALTER TABLE customers
ADD deactivated_date BIGINT;

ALTER TABLE customers
ADD deleted_date BIGINT;

ALTER TABLE customers
ADD created_notify_date BIGINT;

UPDATE customers
SET deleted_date = last_updated_date
WHERE status = 1

UPDATE customers
SET deactivated_date = 1448158634
WHERE deactivated_type IN ('auto', 'manual');


UPDATE customers
SET created_notify_date = 1448158634 - 7 * 86400
WHERE
	activated_flag = '0'
and	deactivated_type IS NULL
and	(status IS NULL OR status = '0')
and	created_date < 1448158634 - 7 * 86400