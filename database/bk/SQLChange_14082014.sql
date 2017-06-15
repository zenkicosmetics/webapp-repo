ALTER TABLE payment
ADD primary_card tinyint(4) DEFAULT NULL;

ALTER TABLE payment
DROP INDEX payment_uk;
