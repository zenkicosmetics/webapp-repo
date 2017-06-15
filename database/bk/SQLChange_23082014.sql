ALTER TABLE payment_tran_hist
ADD invoice_id bigint DEFAULT NULL;

DROP TABLE IF EXISTS `payone_transaction_hist`;
CREATE TABLE `payone_transaction_hist` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `aid` varchar(20) DEFAULT NULL,
  `txid` varchar(30) DEFAULT NULL,
  `reference` varchar(30) DEFAULT NULL,
  `userid` varchar(30) DEFAULT NULL,
  `customerid` varchar(30) DEFAULT NULL,
  `create_time` bigint(30) DEFAULT NULL,
  `booking_date` bigint(30) DEFAULT NULL,
  `document_date` bigint(30) DEFAULT NULL,
  `document_reference` varchar(30) DEFAULT NULL,
  `param` varchar(30) DEFAULT NULL,
  `event` varchar(30) DEFAULT NULL,
  `clearingtype` varchar(10) DEFAULT NULL,
  `amount` decimal(19,2) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
  `invoice_id` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `external_tran_hist`;
CREATE TABLE `external_tran_hist` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tran_id` bigint(20) DEFAULT NULL,
  `tran_date` varchar(10) DEFAULT NULL,
  `tran_amount` decimal(10,2) DEFAULT NULL,
  `customer_id` bigint(20) DEFAULT NULL,
   `created_date` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;