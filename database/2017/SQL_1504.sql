ALTER TABLE customer_shipping_report
    ADD COLUMN `shipping_api_id` BIGINT(20) NULL DEFAULT NULL,
    ADD COLUMN `shipping_credential_id` BIGINT(20) NULL DEFAULT NULL;

