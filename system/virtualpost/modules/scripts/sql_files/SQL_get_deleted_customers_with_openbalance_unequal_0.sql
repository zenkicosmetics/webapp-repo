CREATE TABLE `temp_deleted_customers_with_open_balance_unequal_zero` (
  `id` int(12) NOT NULL,
  `open_blance_due` varchar(20) DEFAULT NULL,
  `open_blance_this_month` varchar(20) DEFAULT NULL,
  `gross_open_balance` varchar(20) DEFAULT NULL,
  `customer_id` bigint(20) NOT NULL,
  `customer_code` varchar(20) DEFAULT NULL,
  `user_name` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `currence` varchar(20) DEFAULT NULL
  
) ENGINE=InnoDB DEFAULT CHARSET= utf8;

ALTER TABLE `temp_deleted_customers_with_open_balance_unequal_zero`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `temp_deleted_customers_with_open_balance_unequal_zero`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
