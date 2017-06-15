<?php defined('BASEPATH') or exit('No direct script access allowed');

class SQLConfigs
{
    // List ALL customers' emails, that have one or more Berlin postboxes but are not on status deleted
    const SQL_SELECT_EMAIL = <<< SQL
SELECT DISTINCT
          c.email
FROM
          customers c2
INNER JOIN
          postbox p
ON
          p.customer_id = c.customer_id
INNER JOIN
          location l
ON
          l.id = p.location_available_id
          -- AND l.public_flag = 1
WHERE
          l.id = 1 AND -- l.location_name = 'Berlin'
          ( c.`status` <> 1 OR c.`status` IS NULL)
SQL;

    const SQL_SELECT_OPEN_BALANCE = <<< SQL
SELECT
          SUM(`i`.total_invoice) as open_balance,
          `c`.*
FROM
          `customers` as `c`
LEFT JOIN
          invoice_summary as `i`
ON
          `c`.customer_id = `i`.customer_id
WHERE
          (`c`.status = 1)
GROUP BY
          `i`.customer_id
HAVING
          open_balance > 0
SQL;

    // #786 To identify ALL items, that belong to deleted accounts and do not have a manually confirmed/completed trash activity
    const SQL_deleted_customers_with_trash_activities_ordered_but_not_mark_completed = <<< SQL
SELECT
		c.customer_id,
		c.customer_code,
		c.account_type,
        DATE_FORMAT(FROM_UNIXTIME(c.deactivated_date), '%Y.%m.%d') AS deactivated_date,
        DATE_FORMAT(FROM_UNIXTIME(c.plan_delete_date), '%Y.%m.%d') AS plan_delete_date,
        e.envelope_code,
        e.from_customer_name,
		e.weight,
		e.weight_unit,
        DATE_FORMAT(FROM_UNIXTIME(e.incomming_date), '%Y.%m.%d') AS incomming_date,
		DATE_FORMAT(FROM_UNIXTIME(e.direct_shipping_date), '%Y.%m.%d') AS direct_shipping_date,
		DATE_FORMAT(FROM_UNIXTIME(e.collect_shipping_date), '%Y.%m.%d') AS collect_shipping_date,
		DATE_FORMAT(FROM_UNIXTIME(e.envelope_scan_date), '%Y.%m.%d') AS envelope_scan_date,
		DATE_FORMAT(FROM_UNIXTIME(e.item_scan_date), '%Y.%m.%d') AS item_scan_date,
		e.trash_flag,
		e.completed_flag
FROM
		customers AS c
INNER JOIN
		envelopes AS e
ON
		c.customer_id = e.to_customer_id
WHERE
		c.status = 1 AND
        -- e.completed_flag <> 1 AND
		LENGTH(e.envelope_code) = 26 AND
		EXISTS (
		  SELECT * FROM envelopes_completed AS ec1 WHERE
		      ec1.envelope_id = e.id AND
		      (ec1.activity_id = 8 OR ec1.activity_id = 19) -- TRASH_ORDER_BY_CUSTOMER_ACTIVITY_TYPE = 8; TRASH_ORDER_BY_SYSTEM_ACTIVITY_TYPE = 19
		) AND
		NOT EXISTS (
          SELECT * FROM envelopes_completed AS ec2 WHERE
		      ec2.envelope_id = e.id AND
		      ec2.activity_id = 5 -- TRASH_COMPLETED_ACTIVITY_TYPE
		)
ORDER BY
		c.customer_id
SQL;

    const SQL_ENVELOPES_WITHOUT_NUMBER_PAGE = <<<SQL
SELECT * FROM envelope_files WHERE
  number_page = 0 AND
  FROM_UNIXTIME(created_date) >= '2016-01-01' AND
  local_file_name NOT REGEXP '^/uploads/images/envelope/[fF]irst'
SQL;

    const SQL_ENVELOPES_UPDATE_PATH_FILE = <<<SQL
SELECT * FROM envelope_files WHERE
  file_name NOT REGEXP '^https://node2.eu.clevvermail.com' OR
  public_file_name NOT REGEXP '^https://node2.eu.clevvermail.com'
SQL;

    const SQL_CUSTOMERS_WITHOUT_MAIN_POSTBOX = <<<SQL
SELECT DISTINCT
   c.customer_id
FROM
   customers AS c
INNER JOIN
   postbox AS p
ON
   p.customer_id = c.customer_id
WHERE
   p.customer_id NOT IN (
         SELECT DISTINCT
            customer_id
         FROM
            postbox
         WHERE
            is_main_postbox = 1
      ) AND
   c.status = 1
SQL;

    const SQL_rollback_envelope_status = <<<SQL
SELECT
        id,
        activity_id,
        completed_date,
        DATE_FORMAT(FROM_UNIXTIME(completed_date), '%d/%m/%Y') AS str_completed_date
FROM
        envelopes_completed
WHERE
        envelope_id = ?
ORDER BY
        completed_date ASC
SQL;
    
    const SQL_correct_data_cases_verification_usps = <<<SQL
select * from cases_verification_usps WHERE 
	verification_local_file_path LIKE '/mnt/nfs-share/webapp%' OR
	id_of_applicant_local_file_path LIKE '/mnt/nfs-share/webapp%' OR
	license_of_applicant_local_file_path LIKE '/mnt/nfs-share/webapp%' OR
	additional_local_file_path LIKE '/mnt/nfs-share/webapp%'
SQL;

}