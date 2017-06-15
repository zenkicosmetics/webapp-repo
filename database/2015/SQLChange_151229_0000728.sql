ALTER TABLE country
ADD COLUMN decimal_separator CHAR(1) DEFAULT ',' COMMENT 'Like currency, decimal separator is specific to each country and can be changed by the customer' AFTER currency_id;

ALTER TABLE customers
ADD COLUMN decimal_separator CHAR(1) DEFAULT ',' COMMENT 'Like currency, it is used to show all price values' AFTER currency_id;