-- edit table.
ALTER TABLE `invoice_summary`
	ADD COLUMN `custom_declaration_outgoing_quantity_01` int;
	
ALTER TABLE `invoice_summary`
	ADD COLUMN `custom_declaration_outgoing_quantity_02` int;
	
ALTER TABLE `invoice_summary`
	ADD COLUMN `custom_declaration_outgoing_price_01` decimal(18,10);
	
ALTER TABLE `invoice_summary`
	ADD COLUMN `custom_declaration_outgoing_price_02` decimal(18,10);
	
ALTER TABLE `envelope_files`
	ADD COLUMN `sync_amazon_flag` int;
	
		
ALTER TABLE `envelope_files_history`
	ADD COLUMN `sync_amazon_flag` int;
	
-- delete old data
DELETE FROM `settings` WHERE SettingCode = '000098';

INSERT INTO `settings`(SettingCode, DefaultValue, ActualValue, LabelValue, ModuleName, SettingOrder, IsRequired, Alias01, Alias02, Alias03, Alias04, Alias05, description) 
VALUES ('000098', '', 'cash payment fee', null, null, '1', '1', null, null, null, null, null, 'Dropdown list of description to create manual invoice');

INSERT INTO `settings`(SettingCode, DefaultValue, ActualValue, LabelValue, ModuleName, SettingOrder, IsRequired, Alias01, Alias02, Alias03, Alias04, Alias05, description) 
VALUES ('000098', '', 'import customs fee', null, null, '1', '1', null, null, null, null, null, 'Dropdown list of description to create manual invoice');

INSERT INTO `settings`(SettingCode, DefaultValue, ActualValue, LabelValue, ModuleName, SettingOrder, IsRequired, Alias01, Alias02, Alias03, Alias04, Alias05, description) 
VALUES ('000098', '', 'address verification', null, null, '1', '1', null, null, null, null, null, 'Dropdown list of description to create manual invoice');

INSERT INTO `settings`(SettingCode, DefaultValue, ActualValue, LabelValue, ModuleName, SettingOrder, IsRequired, Alias01, Alias02, Alias03, Alias04, Alias05, description) 
VALUES ('000098', '', 'customs declaration (>1000 EUR)', null, null, '1', '1', null, null, null, null, null, 'Dropdown list of description to create manual invoice');

INSERT INTO `settings`(SettingCode, DefaultValue, ActualValue, LabelValue, ModuleName, SettingOrder, IsRequired, Alias01, Alias02, Alias03, Alias04, Alias05, description) 
VALUES ('000098', '', 'special service fee (in 15min intervals)', null, null, '1', '1', null, null, null, null, null, 'Dropdown list of description to create manual invoice');

INSERT INTO `settings`(SettingCode, DefaultValue, ActualValue, LabelValue, ModuleName, SettingOrder, IsRequired, Alias01, Alias02, Alias03, Alias04, Alias05, description) 
VALUES ('000098', '', 'personal pickup charge', null, null, '1', '1', null, null, null, null, null, 'Dropdown list of description to create manual invoice');


DROP TABLE IF EXISTS `partner_receipt`;
CREATE TABLE `partner_receipt` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `partner_id` bigint(20) DEFAULT NULL,
  `date_of_receipt` varchar(10) DEFAULT NULL,
  `description` varchar(4000) DEFAULT NULL,
  `net_amount` decimal(18,10) DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

ALTER TABLE invoice_detail
ADD COLUMN location_id bigint(20);

ALTER TABLE invoice_detail_manual
ADD COLUMN location_id bigint(20);

ALTER TABLE partner_receipt
ADD COLUMN location_id bigint(20);

DROP TABLE IF EXISTS `invoice_summary_by_location`;
CREATE TABLE `invoice_summary_by_location` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `invoice_code` varchar(50) DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `location_id` bigint(20) DEFAULT NULL,
  `invoice_month` varchar(8) DEFAULT NULL,
  `private_postboxes_amount` double DEFAULT NULL,
  `business_postboxes_amount` double DEFAULT NULL,
  `incomming_items_free_account` double DEFAULT NULL,
  `incomming_items_private_account` double DEFAULT NULL,
  `incomming_items_business_account` double DEFAULT NULL,
  `envelope_scan_free_account` double DEFAULT NULL,
  `envelope_scan_private_account` double DEFAULT NULL,
  `envelope_scan_business_account` double DEFAULT NULL,
  `item_scan_free_account` double DEFAULT NULL,
  `item_scan_private_account` double DEFAULT NULL,
  `item_scan_business_account` double DEFAULT NULL,
  `additional_pages_scanning` double DEFAULT NULL,
  `direct_shipping_free_account` double DEFAULT NULL,
  `direct_shipping_private_account` double DEFAULT NULL,
  `direct_shipping_business_account` double DEFAULT NULL,
  `collect_shipping_free_account` double DEFAULT NULL,
  `collect_shipping_private_account` double DEFAULT NULL,
  `collect_shipping_business_account` double DEFAULT NULL,
  `storing_letters_free_account` double DEFAULT NULL,
  `storing_letters_private_account` double DEFAULT NULL,
  `storing_letters_business_account` double DEFAULT NULL,
  `storing_packages_free_account` double DEFAULT NULL,
  `storing_packages_private_account` double DEFAULT NULL,
  `storing_packages_business_account` double DEFAULT NULL,
  `additional_private_postbox_amount` double DEFAULT NULL,
  `additional_business_postbox_amount` double DEFAULT NULL,
  `private_postboxes_quantity` int(11) DEFAULT NULL,
  `private_postboxes_netprice` double DEFAULT NULL,
  `business_postboxes_quantity` int(11) DEFAULT NULL,
  `business_postboxes_netprice` double DEFAULT NULL,
  `incomming_items_free_quantity` int(11) DEFAULT NULL,
  `incomming_items_free_netprice` double DEFAULT NULL,
  `incomming_items_private_quantity` int(11) DEFAULT NULL,
  `incomming_items_private_netprice` double DEFAULT NULL,
  `incomming_items_business_quantity` int(11) DEFAULT NULL,
  `incomming_items_business_netprice` double DEFAULT NULL,
  `envelope_scan_free_quantity` int(11) DEFAULT NULL,
  `envelope_scan_free_netprice` double DEFAULT NULL,
  `envelope_scan_private_quantity` int(11) DEFAULT NULL,
  `envelope_scan_private_netprice` double DEFAULT NULL,
  `envelope_scan_business_quantity` int(11) DEFAULT NULL,
  `envelope_scan_business_netprice` double DEFAULT NULL,
  `item_scan_free_quantity` int(11) DEFAULT NULL,
  `item_scan_free_netprice` double DEFAULT NULL,
  `item_scan_private_quantity` int(11) DEFAULT NULL,
  `item_scan_private_netprice` double DEFAULT NULL,
  `item_scan_business_quantity` int(11) DEFAULT NULL,
  `item_scan_business_netprice` double DEFAULT NULL,
  `additional_pages_scanning_quantity` int(11) DEFAULT NULL,
  `additional_pages_scanning_netprice` double DEFAULT NULL,
  `direct_shipping_free_quantity` int(11) DEFAULT NULL,
  `direct_shipping_free_netprice` double DEFAULT NULL,
  `direct_shipping_private_quantity` int(11) DEFAULT NULL,
  `direct_shipping_private_netprice` double DEFAULT NULL,
  `direct_shipping_business_quantity` int(11) DEFAULT NULL,
  `direct_shipping_business_netprice` double DEFAULT NULL,
  `collect_shipping_free_quantity` int(11) DEFAULT NULL,
  `collect_shipping_free_netprice` double DEFAULT NULL,
  `collect_shipping_private_quantity` int(11) DEFAULT NULL,
  `collect_shipping_private_netprice` double DEFAULT NULL,
  `collect_shipping_business_quantity` int(11) DEFAULT NULL,
  `collect_shipping_business_netprice` double DEFAULT NULL,
  `storing_letters_free_quantity` int(11) DEFAULT NULL,
  `storing_letters_free_netprice` double DEFAULT NULL,
  `storing_letters_private_quantity` int(11) DEFAULT NULL,
  `storing_letters_private_netprice` double DEFAULT NULL,
  `storing_letters_business_quantity` int(11) DEFAULT NULL,
  `storing_letters_business_netprice` double DEFAULT NULL,
  `storing_packages_free_quantity` int(11) DEFAULT NULL,
  `storing_packages_free_netprice` double DEFAULT NULL,
  `storing_packages_private_quantity` int(11) DEFAULT NULL,
  `storing_packages_private_netprice` double DEFAULT NULL,
  `storing_packages_business_quantity` int(11) DEFAULT NULL,
  `storing_packages_business_netprice` double DEFAULT NULL,
  `additional_private_postbox_quantity` int(11) DEFAULT NULL,
  `additional_private_postbox_netprice` double DEFAULT NULL,
  `additional_business_postbox_quantity` int(11) DEFAULT NULL,
  `additional_business_postbox_netprice` double DEFAULT NULL,
  `vat` double DEFAULT NULL,
  `vat_case` tinyint(4) DEFAULT NULL,
  `total_invoice` double DEFAULT NULL,
  `invoice_type` varchar(10) DEFAULT NULL,
  `additional_pages_scanning_free_quantity` int(11) DEFAULT NULL,
  `additional_pages_scanning_private_quantity` int(11) DEFAULT NULL,
  `additional_pages_scanning_business_quantity` int(11) DEFAULT NULL,
  `additional_pages_scanning_free_netprice` double DEFAULT NULL,
  `additional_pages_scanning_private_netprice` double DEFAULT NULL,
  `additional_pages_scanning_business_netprice` double DEFAULT NULL,
  `additional_pages_scanning_free_amount` double DEFAULT NULL,
  `additional_pages_scanning_private_amount` double DEFAULT NULL,
  `additional_pages_scanning_business_amount` double DEFAULT NULL,
  `custom_declaration_outgoing_quantity_01` int(11) DEFAULT NULL,
  `custom_declaration_outgoing_quantity_02` int(11) DEFAULT NULL,
  `custom_declaration_outgoing_price_01` decimal(18,10) DEFAULT NULL,
  `custom_declaration_outgoing_price_02` decimal(18,10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12670 DEFAULT CHARSET=latin1;


ALTER TABLE envelope_shipping
ADD COLUMN forwarding_charges_postal  decimal(18,10) DEFAULT NULL;

ALTER TABLE envelope_shipping
ADD COLUMN forwarding_charges_fee  decimal(18,10) DEFAULT NULL;

ALTER TABLE `invoice_summary_by_location`
	ADD COLUMN `invoice_summary_id` BIGINT(20) NULL DEFAULT '0' AFTER `id`;
	
ALTER TABLE postbox
ADD COLUMN created_date BIGINT NULL;

UPDATE postbox
SET created_date = apply_date;

ALTER TABLE envelopes
ADD COLUMN envelope_scan_date BIGINT NULL;

UPDATE envelopes
SET envelope_scan_date = item_scan_date
WHERE envelope_scan_flag = '1';
