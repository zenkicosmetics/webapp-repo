-- Clevver SubDomain
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`, `type`, `rev_share_in_percent`) VALUES (1, 'clevver_subdomain', '29.95', 'Clevver Subdomain', 'EUR/month', 0, 1, 0);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`, `type`, `rev_share_in_percent`) VALUES (2, 'clevver_subdomain', '29.95', 'Clevver Subdomain', 'EUR/month', 0, 1, 0);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`, `type`, `rev_share_in_percent`) VALUES (3, 'clevver_subdomain', '29.95', 'Clevver Subdomain', 'EUR/month', 0, 1, 0);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`, `type`, `rev_share_in_percent`) VALUES (5, 'clevver_subdomain', '29.95', 'Clevver Subdomain', 'EUR/month', 0, 1, 0);

INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`, `type`, `rev_share_in_percent`) 
SELECT 1, 'clevver_subdomain', '29.95', 'Clevver Subdomain', 'EUR/month', id, 1, 0
FROM pricing_template where id > 0;

INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`, `type`, `rev_share_in_percent`) 
SELECT 2, 'clevver_subdomain', '29.95', 'Clevver Subdomain', 'EUR/month', id, 1, 0
FROM pricing_template where id > 0;

INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`, `type`, `rev_share_in_percent`) 
SELECT 3, 'clevver_subdomain', '29.95', 'Clevver Subdomain', 'EUR/month', id, 1, 0
FROM pricing_template where id > 0;

INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`, `type`, `rev_share_in_percent`) 
SELECT 5, 'clevver_subdomain', '29.95', 'Clevver Subdomain', 'EUR/month', id, 1, 0
FROM pricing_template where id > 0;


-- Own Domain
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`, `type`, `rev_share_in_percent`) VALUES (1, 'own_domain', '29.95', 'Own Domain', 'EUR/month', 0, 1, 0);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`, `type`, `rev_share_in_percent`) VALUES (2, 'own_domain', '29.95', 'Own Domain', 'EUR/month', 0, 1, 0);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`, `type`, `rev_share_in_percent`) VALUES (3, 'own_domain', '29.95', 'Own Domain', 'EUR/month', 0, 1, 0);
INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`, `type`, `rev_share_in_percent`) VALUES (5, 'own_domain', '29.95', 'Own Domain', 'EUR/month', 0, 1, 0);


INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`, `type`, `rev_share_in_percent`) 
SELECT 1, 'own_domain', '29.95', 'Own Domain', 'EUR/month', id, 1, 0
FROM pricing_template where id > 0;

INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`, `type`, `rev_share_in_percent`) 
SELECT 2, 'own_domain', '29.95', 'Own Domain', 'EUR/month', id, 1, 0
FROM pricing_template where id > 0;

INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`, `type`, `rev_share_in_percent`) 
SELECT 3, 'own_domain', '29.95', 'Own Domain', 'EUR/month', id, 1, 0
FROM pricing_template where id > 0;

INSERT INTO `pricing` (`account_type`, `item_name`, `item_value`, `item_description`, `item_unit`, `pricing_template_id`, `type`, `rev_share_in_percent`) 
SELECT 5, 'own_domain', '29.95', 'Own Domain', 'EUR/month', id, 1, 0
FROM pricing_template where id > 0;
