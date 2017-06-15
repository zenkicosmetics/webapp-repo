/*
* Check free account have than one postbox free
*/

SELECT c.user_name,c.email,c.status, t.*  
FROM (
	SELECT customer_id, type, new_postbox_type, FROM_UNIXTIME(created_date) as postbox_created_date, completed_delete_flag, first_location_flag, count(*) as number_postbox_free 
	FROM `postbox` 
	WHERE type = 1 and completed_delete_flag <> 1
	GROUP by customer_id ,type 
	HAVING number_postbox_free > 1 
	ORDER BY `postbox_id`  DESC
) as t 
INNER JOIN customers as c ON t.customer_id = c.customer_id
WHERE c.status <> 1