ALTER TABLE invoice_summary
ADD invoice_type VARCHAR(10);

ALTER TABLE invoice_summary
MODIFY invoice_month VARCHAR(8);

CREATE TABLE `invoice_detail_manual` (
  `id` bigint(20) NOT NULL DEFAULT '0',
  `invoice_summary_id` bigint(20) DEFAULT NULL,
  `invoice_id` bigint(20) DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `created_date` bigint(20) DEFAULT NULL,
  `description` text,
  `quantity` int(11) DEFAULT NULL,
  `net_price` double DEFAULT NULL,
  `vat` double DEFAULT NULL,
  `vat_case` int(11) DEFAULT NULL,
  `gross_price` double DEFAULT NULL,
  `payment_flag` tinyint(4) DEFAULT NULL,
  `payment_date` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;