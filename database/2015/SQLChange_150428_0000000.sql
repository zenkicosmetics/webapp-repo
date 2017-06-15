DELETE FROM pricing WHERE
item_name IN ('custom_declaration_outgoing', 'custom_declaration_outgoing_01', 'custom_declaration_outgoing_02', 'collect_shipping_plus', 'pickup_charge', 'special_requests_charge_by_time');

-- Insert data for Customs declaration outgoing (value >1000 EUR)
INSERT INTO pricing (account_type, item_name, item_description, item_value, item_unit, pricing_template_id, type)
VALUES (1, 'custom_declaration_outgoing_01', 'Customs declaration outgoing (value >1000 EUR)', '59', 'EUR', 1, 1);

INSERT INTO pricing (account_type, item_name, item_description, item_value, item_unit, pricing_template_id, type)
VALUES (2, 'custom_declaration_outgoing_01', 'Customs declaration outgoing (value >1000 EUR)', '59', 'EUR', 1, 1);

INSERT INTO pricing (account_type, item_name, item_description, item_value, item_unit, pricing_template_id, type)
VALUES (3, 'custom_declaration_outgoing_01', 'Customs declaration outgoing (value >1000 EUR)', '59', 'EUR', 1, 1);

-- Insert data for Customs declaration outgoing (value <1000 EUR)
INSERT INTO pricing (account_type, item_name, item_description, item_value, item_unit, pricing_template_id, type)
VALUES (1, 'custom_declaration_outgoing_02', 'Customs declaration outgoing (value <1000 EUR)', '10', 'EUR', 1, 1);

INSERT INTO pricing (account_type, item_name, item_description, item_value, item_unit, pricing_template_id, type)
VALUES (2, 'custom_declaration_outgoing_02', 'Customs declaration outgoing (value <1000 EUR)', '10', 'EUR', 1, 1);

INSERT INTO pricing (account_type, item_name, item_description, item_value, item_unit, pricing_template_id, type)
VALUES (3, 'custom_declaration_outgoing_02', 'Customs declaration outgoing (value <1000 EUR)', '10', 'EUR', 1, 1);

-- Shipping collected to forwarding address (percentage on top of postal charge)
INSERT INTO pricing (account_type, item_name, item_description, item_value, item_unit, pricing_template_id, type)
VALUES (1, 'collect_shipping_plus', 'Shipping collected to forwarding address (percentage on top of postal charge)', '20', '%', 1, 1);

INSERT INTO pricing (account_type, item_name, item_description, item_value, item_unit, pricing_template_id, type)
VALUES (2, 'collect_shipping_plus', 'Shipping collected to forwarding address (percentage on top of postal charge)', '20', '%', 1, 1);

INSERT INTO pricing (account_type, item_name, item_description, item_value, item_unit, pricing_template_id, type)
VALUES (3, 'collect_shipping_plus', 'Shipping collected to forwarding address (percentage on top of postal charge)', '20', '%', 1, 1);

-- Pickup charge (only with confirmed appointment)
INSERT INTO pricing (account_type, item_name, item_description, item_value, item_unit, pricing_template_id, type)
VALUES (1, 'pickup_charge', 'Pickup charge (only with confirmed appointment)', '50', 'EUR', 1, 1);

INSERT INTO pricing (account_type, item_name, item_description, item_value, item_unit, pricing_template_id, type)
VALUES (2, 'pickup_charge', 'Pickup charge (only with confirmed appointment)', '50', 'EUR', 1, 1);

INSERT INTO pricing (account_type, item_name, item_description, item_value, item_unit, pricing_template_id, type)
VALUES (3, 'pickup_charge', 'Pickup charge (only with confirmed appointment)', '50', 'EUR', 1, 1);

-- Pickup charge (only with confirmed appointment)
INSERT INTO pricing (account_type, item_name, item_description, item_value, item_unit, pricing_template_id, type)
VALUES (1, 'special_requests_charge_by_time', 'Pickup charge (only with confirmed appointment)', '60', 'EUR', 1, 1);

INSERT INTO pricing (account_type, item_name, item_description, item_value, item_unit, pricing_template_id, type)
VALUES (2, 'special_requests_charge_by_time', 'Pickup charge (only with confirmed appointment)', '60', 'EUR', 1, 1);

INSERT INTO pricing (account_type, item_name, item_description, item_value, item_unit, pricing_template_id, type)
VALUES (3, 'special_requests_charge_by_time', 'Pickup charge (only with confirmed appointment)', '60', 'EUR', 1, 1);


-- paypal_transaction_fee
INSERT INTO pricing (account_type, item_name, item_description, item_value, item_unit, pricing_template_id, type)
VALUES (1, 'paypal_transaction_fee', 'Pickup charge (only with confirmed appointment)', '3', '%', 1, 1);
INSERT INTO pricing (account_type, item_name, item_description, item_value, item_unit, pricing_template_id, type)
VALUES (2, 'paypal_transaction_fee', 'Pickup charge (only with confirmed appointment)', '3', '%', 1, 1);
INSERT INTO pricing (account_type, item_name, item_description, item_value, item_unit, pricing_template_id, type)
VALUES (3, 'paypal_transaction_fee', 'Pickup charge (only with confirmed appointment)', '3', '%', 1, 1);

DELETE FROM pricing
where pricing_template_id = 0;


INSERT INTO pricing (account_type, item_name, item_description, item_value, item_unit, pricing_template_id, type)
SELECT account_type, item_name, item_description, item_value, item_unit, 0, type
FROM pricing
WHERE pricing_template_id = 1;


ALTER TABLE `vat_case`
	ADD COLUMN `reverse_charge` TINYINT NULL DEFAULT '0' AFTER `notes`;
