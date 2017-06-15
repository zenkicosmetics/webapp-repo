ALTER TABLE `terms_services` ADD `need_customer_approval_flag` TINYINT(2) NOT NULL DEFAULT '0' AFTER `content`;
ALTER TABLE `terms_services` ADD `message_to_customer_flag` TINYINT(2) NOT NULL DEFAULT '0' AFTER `need_customer_approval_flag`;
ALTER TABLE `terms_services` ADD `message_to_customer` TEXT NULL AFTER `content`;
ALTER TABLE `terms_services` ADD `effective_date` BIGINT(20) NULL AFTER `message_to_customer_flag`;
ALTER TABLE `customers` ADD `accept_terms_condition_flag` TINYINT(2) NOT NULL DEFAULT '0' AFTER `auto_trash_flag`;
ALTER TABLE `terms_services` ADD `notify_flag` TINYINT(2) NOT NULL DEFAULT '0' AFTER `effective_date`;