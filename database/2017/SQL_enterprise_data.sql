-- App Type
INSERT INTO `settings` VALUES (0, '000243', 'ivr', 'ivr', 'IVR', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0');
INSERT INTO `settings` VALUES (0, '000243', 'mailbox', 'mailbox', 'mailbox', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0');
INSERT INTO `settings` VALUES (0, '000243', 'sysprompt', 'sysprompt', 'sysprompt', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0');

-- Princing contract terms
INSERT INTO `settings` VALUES (0, '000248', 'monthly', 'monthly', 'monthly', null, 1, null, null, null, null, null, null, null, null, null, null, null, null, null, '0');
INSERT INTO `settings` VALUES (0, '000248', 'quarterly', 'quarterly', 'quarterly', null, 2, null, null, null, null, null, null, null, null, null, null, null, null, null, '0');
INSERT INTO `settings` VALUES (0, '000248', 'yearly', 'yearly', 'yearly', null, 3, null, null, null, null, null, null, null, null, null, null, null, null, null, '0');

-- Princing billing period
INSERT INTO `settings` VALUES (0, '000249', 'monthly', 'monthly', 'monthly', null, 1, null, null, null, null, null, null, null, null, null, null, null, null, null, '0');
INSERT INTO `settings` VALUES (0, '000249', 'quarterly', 'quarterly', 'quarterly', null, 2, null, null, null, null, null, null, null, null, null, null, null, null, null, '0');
INSERT INTO `settings` VALUES (0, '000249', 'yearly', 'yearly', 'yearly', null, 3, null, null, null, null, null, null, null, null, null, null, null, null, null, '0');

INSERT INTO `settings` VALUES (0, '000247', 'Employee', 'Employee', 'Employee', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0');
INSERT INTO `settings` VALUES (0, '000247', 'Individual', 'Individual', 'Individual', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0');
INSERT INTO `settings` VALUES (0, '000247', 'Company', 'Company', 'Company', null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, '0');





-- SCRIPT CONVERT SETTING DATA OF REGISTRATION PROCESS
insert into customer_product_settings (customer_id,product_id, setting_key, setting_value)
select customer_id, 1, 'invoicing_address_completed', invoicing_address_completed from customers;

insert into customer_product_settings (customer_id,product_id, setting_key, setting_value)
select customer_id, 1, 'postbox_name_flag', invoicing_address_completed from customers;

insert into customer_product_settings (customer_id,product_id, setting_key, setting_value)
select customer_id, 1, 'name_comp_address_flag', invoicing_address_completed from customers;

insert into customer_product_settings (customer_id,product_id, setting_key, setting_value)
select customer_id, 1, 'city_address_flag', invoicing_address_completed from customers;

insert into customer_product_settings (customer_id,product_id, setting_key, setting_value)
select customer_id, 1, 'payment_detail_flag', invoicing_address_completed from customers;

insert into customer_product_settings (customer_id,product_id, setting_key, setting_value)
select customer_id, 1, 'email_confirm_flag', invoicing_address_completed from customers;

insert into customer_product_settings (customer_id,product_id, setting_key, setting_value)
select customer_id, 1, 'shipping_address_completed', invoicing_address_completed from customers;

-- UPDATE ACCOUNT TYPES OT NORMAL USER -------------------
UPDATE customers SET account_type = 4;




































