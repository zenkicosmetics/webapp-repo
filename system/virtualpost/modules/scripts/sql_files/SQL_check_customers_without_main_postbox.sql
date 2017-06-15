/*
* Check customers don't have main_postbox
*/

SELECT
      customers . * ,
      COUNT( postbox.postbox_id ) AS cnt
FROM
      customers
INNER JOIN
      postbox
ON
      customers.customer_id = postbox.customer_id
WHERE
      postbox.is_main_postbox = 1
GROUP BY
      customers.customer_id
HAVING cnt =0