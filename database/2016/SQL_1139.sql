
-- Convert datetime to int 
ALTER TABLE customers_forward_address_hist
ADD created_date2 int

update customers_forward_address_hist SET created_date2 = UNIX_TIMESTAMP(created_date)

ALTER TABLE customers_forward_address_hist MODIFY created_date  int;

update customers_forward_address_hist SET created_date = created_date2

ALTER TABLE customers_forward_address_hist
DROP COLUMN created_date2


ALTER TABLE customers_forward_address
ADD created_date2 int

update customers_forward_address SET created_date2 = UNIX_TIMESTAMP(created_date)

ALTER TABLE customers_forward_address MODIFY created_date  int;

update customers_forward_address SET created_date = created_date2

ALTER TABLE customers_forward_address
DROP COLUMN created_date2


ALTER TABLE envelope_storage_month
ADD created_date2 int

update envelope_storage_month SET created_date2 = UNIX_TIMESTAMP(created_date)

ALTER TABLE envelope_storage_month MODIFY created_date  int;

update envelope_storage_month SET created_date = created_date2

ALTER TABLE envelope_storage_month
DROP COLUMN created_date2

