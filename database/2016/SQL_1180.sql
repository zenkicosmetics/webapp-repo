DROP TABLE IF EXISTS `postbox_history_activity`;
CREATE TABLE `postbox_history_activity` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) DEFAULT NULL,
  `postbox_id` bigint(20) DEFAULT NULL,
  `postbox_code` varchar(20) DEFAULT NULL,
  `postbox_name` varchar(255) DEFAULT NULL,
  `location_available_id` bigint(20) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `company` varchar(500) DEFAULT NULL,
  `action_type` varchar(50) DEFAULT NULL,
  `action_date` bigint(20) DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Convert data  to postbox_history_activity 
-- POSTBOX CREATE
INSERT INTO postbox_history_activity (customer_id,postbox_id,postbox_code, postbox_name, location_available_id,name,company,action_type,action_date,type)
                SELECT 
                customers.customer_id, 
                postbox.postbox_id, 
                postbox.postbox_code, 
                postbox.postbox_name, 
                postbox.location_available_id,
                postbox.name, 
                postbox.company, 
                if(postbox.created_date, '1', null)  as action_type, 
                postbox.created_date as action_date,
                postbox.type as after_type
                FROM postbox_history 
                INNER JOIN customers on customers.customer_id = postbox_history.customer_id
                LEFT JOIN postbox on postbox.postbox_id = postbox_history.postbox_id
                WHERE  postbox.postbox_code IS NOT NULL
                AND postbox.created_date IS NOT NULL
                GROUP BY
                postbox.postbox_code,
                postbox.postbox_name,
                postbox.name, 
                postbox.company

-- UNION ALL
-- POSTBOX DOWNGRADE ORDER
INSERT INTO postbox_history_activity (customer_id,postbox_id,postbox_code, postbox_name, location_available_id,name,company,action_type,action_date,type)

                SELECT 
                customers.customer_id, 
                postbox.postbox_id, 
                postbox.postbox_code, 
                postbox.postbox_name, 
                postbox.location_available_id,
                postbox.name, 
                postbox.company, 
                if((  ( postbox.type = 3  AND (postbox_history.new_postbox_type = 1 OR postbox_history.new_postbox_type = 2 OR postbox.new_postbox_type = 1 OR postbox.new_postbox_type = 2 ) ) 
                OR ( postbox.type = 2 AND  (postbox_history.new_postbox_type = 1 OR postbox.new_postbox_type = 1)) 
                OR (( (postbox.type = 2 OR postbox.type = 3) AND postbox_history.type = 1) OR ( postbox.type = 3 AND postbox_history.type = 2)) ), '2', null)  as action_type, 
                if(postbox_history.modified_date, UNIX_TIMESTAMP(postbox_history.modified_date), if(postbox.apply_date, UNIX_TIMESTAMP(postbox.apply_date), UNIX_TIMESTAMP(postbox_history.apply_date))),
                if( ( postbox.type = 3  AND (postbox_history.new_postbox_type = 1 OR postbox_history.new_postbox_type = 2 OR postbox.new_postbox_type = 1 OR postbox.new_postbox_type = 2 ) ) 
                OR ( postbox.type = 2 AND  (postbox_history.new_postbox_type = 1 OR postbox.new_postbox_type = 1)) , ( postbox_history.new_postbox_type OR postbox.new_postbox_type), 
                if( (( (postbox.type = 2 OR postbox.type = 3) AND postbox_history.type = 1) OR ( postbox.type = 3 AND postbox_history.type = 2)), postbox_history.type, null)) as after_type
                FROM postbox_history 
                INNER JOIN customers on customers.customer_id = postbox_history.customer_id
                LEFT JOIN postbox on postbox.postbox_id = postbox_history.postbox_id
                WHERE  postbox.postbox_code IS NOT NULL 
                AND (postbox_history.apply_date IS NOT NULL AND postbox_history.plan_date_change_postbox_type IS NULL)
                AND (  ( postbox.type = 3  AND (postbox_history.new_postbox_type = 1 OR postbox_history.new_postbox_type = 2 OR postbox.new_postbox_type = 1 OR postbox.new_postbox_type = 2 ) ) 
                OR ( postbox.type = 2 AND  (postbox_history.new_postbox_type = 1 OR postbox.new_postbox_type = 1)) 
                OR (( (postbox.type = 2 OR postbox.type = 3) AND postbox_history.type = 1) OR ( postbox.type = 3 AND postbox_history.type = 2)) )
                GROUP BY
                postbox.postbox_code,
                postbox.postbox_name,
                postbox.name, 
                postbox.company

-- UNION ALL
-- POSTBOX UPGRADE ORDER
INSERT INTO postbox_history_activity (customer_id,postbox_id,postbox_code, postbox_name, location_available_id,name,company,action_type,action_date,type)
                SELECT 
                customers.customer_id, 
                postbox.postbox_id, 
                postbox.postbox_code, 
                postbox.postbox_name, 
                postbox.location_available_id,
                postbox.name, 
                postbox.company, 
                if((  ( postbox.type = 1  AND (postbox_history.new_postbox_type = 3 OR postbox_history.new_postbox_type = 2 OR postbox.new_postbox_type = 3 OR postbox.new_postbox_type = 2) ) 
                 OR ( postbox.type = 2 AND ( postbox_history.new_postbox_type = 3 OR postbox.new_postbox_type = 3)) 
                OR ( ( (postbox.type = 2 OR postbox.type = 1) AND postbox_history.type = 3) OR ( postbox.type = 1 AND postbox_history.type = 2)) ), '3', null)  as action_type, 
                if(postbox_history.modified_date, UNIX_TIMESTAMP(postbox_history.modified_date), if(postbox.apply_date, UNIX_TIMESTAMP(postbox.apply_date), UNIX_TIMESTAMP(postbox_history.apply_date))),
                if( ( postbox.type = 1  AND (postbox_history.new_postbox_type = 3 OR postbox_history.new_postbox_type = 2 OR postbox.new_postbox_type = 3 OR postbox.new_postbox_type = 2) ) 
                 OR ( postbox.type = 2 AND ( postbox_history.new_postbox_type = 3 OR postbox.new_postbox_type = 3)) , postbox_history.new_postbox_type, 
                 if( ( ( (postbox.type = 2 OR postbox.type = 1) AND postbox_history.type = 3) OR ( postbox.type = 1 AND postbox_history.type = 2)), postbox_history.type, null)) as after_type
                FROM postbox_history 
                INNER JOIN customers on customers.customer_id = postbox_history.customer_id
                LEFT JOIN postbox on postbox.postbox_id = postbox_history.postbox_id
                WHERE    postbox.postbox_code IS NOT NULL 
                AND (postbox_history.apply_date IS NOT NULL AND postbox_history.plan_date_change_postbox_type IS NULL)
                AND (  ( postbox.type = 1  AND (postbox_history.new_postbox_type = 3 OR postbox_history.new_postbox_type = 2 OR postbox.new_postbox_type = 3 OR postbox.new_postbox_type = 2) ) 
                 OR ( postbox.type = 2 AND ( postbox_history.new_postbox_type = 3 OR postbox.new_postbox_type = 3)) 
                OR ( ( (postbox.type = 2 OR postbox.type = 1) AND postbox_history.type = 3) OR ( postbox.type = 1 AND postbox_history.type = 2)) )
                GROUP BY
                postbox.postbox_code,
                postbox.postbox_name,
                postbox.name, 
                postbox.company

-- UNION ALL
-- POSTBOX_DOWNGRADE
INSERT INTO postbox_history_activity (customer_id,postbox_id,postbox_code, postbox_name, location_available_id,name,company,action_type,action_date,type)
                SELECT 
                customers.customer_id, 
                postbox.postbox_id, 
                postbox.postbox_code, 
                postbox.postbox_name, 
                postbox.location_available_id,
                postbox.name, 
                postbox.company, 
                if((  ( postbox.type = 3  AND (postbox_history.new_postbox_type = 1 OR postbox_history.new_postbox_type = 2 OR postbox.new_postbox_type = 1 OR postbox.new_postbox_type = 2) ) 
                OR ( postbox.type = 2 AND  (postbox_history.new_postbox_type = 1 OR postbox.new_postbox_type = 1) )
                OR (( (postbox.type = 2 OR postbox.type = 3) AND postbox_history.type = 1) OR ( postbox.type = 3 AND postbox_history.type = 2)) ), '4', null)  as action_type, 
                if(postbox.plan_date_change_postbox_type, UNIX_TIMESTAMP(DATE_ADD(FROM_UNIXTIME(UNIX_TIMESTAMP(postbox.plan_date_change_postbox_type)),INTERVAL 1 DAY)),if(postbox_history.plan_date_change_postbox_type, UNIX_TIMESTAMP(DATE_ADD(FROM_UNIXTIME(UNIX_TIMESTAMP(postbox_history.plan_date_change_postbox_type)),INTERVAL 1 DAY)), UNIX_TIMESTAMP(postbox_history.modified_date))),
                if( ( postbox.type = 3  AND (postbox_history.new_postbox_type = 1 OR postbox_history.new_postbox_type = 2 OR postbox.new_postbox_type = 1 OR postbox.new_postbox_type = 2) ) 
                OR ( postbox.type = 2 AND  (postbox_history.new_postbox_type = 1 OR postbox.new_postbox_type = 1) ), postbox_history.new_postbox_type, 
                if((( (postbox.type = 2 OR postbox.type = 3) AND postbox_history.type = 1) OR ( postbox.type = 3 AND postbox_history.type = 2)), postbox_history.type, null)) as after_type
                FROM postbox_history 
                INNER JOIN customers on customers.customer_id = postbox_history.customer_id
                LEFT JOIN postbox on postbox.postbox_id = postbox_history.postbox_id
                WHERE   postbox.postbox_code IS NOT NULL 
                AND (postbox_history.apply_date IS NOT NULL AND postbox_history.plan_date_change_postbox_type IS NOT NULL)
                AND (  ( postbox.type = 3  AND (postbox_history.new_postbox_type = 1 OR postbox_history.new_postbox_type = 2 OR postbox.new_postbox_type = 1 OR postbox.new_postbox_type = 2) ) 
                OR ( postbox.type = 2 AND  (postbox_history.new_postbox_type = 1 OR postbox.new_postbox_type = 1) )
                OR (( (postbox.type = 2 OR postbox.type = 3) AND postbox_history.type = 1) OR ( postbox.type = 3 AND postbox_history.type = 2)) )
                GROUP BY
                postbox.postbox_code,
                postbox.postbox_name,
                postbox.name, 
                postbox.company

-- UNION ALL
-- POSTBOX_UPGRADE
INSERT INTO postbox_history_activity (customer_id,postbox_id,postbox_code, postbox_name, location_available_id,name,company,action_type,action_date,type)
                SELECT 
                customers.customer_id, 
                postbox.postbox_id, 
                postbox.postbox_code, 
                postbox.postbox_name, 
                postbox.location_available_id,
                postbox.name, 
                postbox.company, 
                if((  ( postbox.type = 1  AND (postbox_history.new_postbox_type = 3 OR postbox_history.new_postbox_type = 2 OR postbox.new_postbox_type = 3 OR postbox.new_postbox_type = 2) ) 
                 OR ( postbox.type = 2 AND (postbox_history.new_postbox_type = 3 OR postbox.new_postbox_type = 3) )
                OR ( ( (postbox.type = 2 OR postbox.type = 1) AND postbox_history.type = 3) OR ( postbox.type = 1 AND postbox_history.type = 2)) ), '5', null)  as action_type, 
                if(postbox.plan_date_change_postbox_type, UNIX_TIMESTAMP(DATE_ADD(FROM_UNIXTIME(UNIX_TIMESTAMP(postbox.plan_date_change_postbox_type)),INTERVAL 1 DAY)),if(postbox_history.plan_date_change_postbox_type, UNIX_TIMESTAMP(DATE_ADD(FROM_UNIXTIME(UNIX_TIMESTAMP(postbox_history.plan_date_change_postbox_type)),INTERVAL 1 DAY)), UNIX_TIMESTAMP(postbox_history.modified_date))),
                if( ( postbox.type = 1  AND (postbox_history.new_postbox_type = 3 OR postbox_history.new_postbox_type = 2 OR postbox.new_postbox_type = 3 OR postbox.new_postbox_type = 2) ) 
                 OR ( postbox.type = 2 AND (postbox_history.new_postbox_type = 3 OR postbox.new_postbox_type = 3) ) , postbox_history.new_postbox_type, 
                 if(( ( (postbox.type = 2 OR postbox.type = 1) AND postbox_history.type = 3) OR ( postbox.type = 1 AND postbox_history.type = 2)) , postbox_history.type, null)) as after_type
                FROM postbox_history 
                INNER JOIN customers on customers.customer_id = postbox_history.customer_id
                LEFT JOIN postbox on postbox.postbox_id = postbox_history.postbox_id
                WHERE  postbox.postbox_code IS NOT NULL
                AND (postbox_history.apply_date IS NOT NULL OR postbox_history.plan_date_change_postbox_type IS NOT NULL)
                 AND (  ( postbox.type = 1  AND (postbox_history.new_postbox_type = 3 OR postbox_history.new_postbox_type = 2 OR postbox.new_postbox_type = 3 OR postbox.new_postbox_type = 2) ) 
                 OR ( postbox.type = 2 AND (postbox_history.new_postbox_type = 3 OR postbox.new_postbox_type = 3) )
                OR ( ( (postbox.type = 2 OR postbox.type = 1) AND postbox_history.type = 3) OR ( postbox.type = 1 AND postbox_history.type = 2)))
                GROUP BY
                postbox.postbox_code,
                postbox.postbox_name,
                postbox.name, 
                postbox.company

-- UNION ALL
-- POSTBOX_DELETE_ORDER_BY_CUSTOMER
INSERT INTO postbox_history_activity (customer_id,postbox_id,postbox_code, postbox_name, location_available_id,name,company,action_type,action_date,type)
                SELECT 
                customers.customer_id, 
                postbox.postbox_id, 
                postbox.postbox_code, 
                postbox.postbox_name, 
                postbox.location_available_id,
                postbox.name, 
                postbox.company, 
                if((postbox_history.deleted = 1 OR postbox.deleted = 1), '6', null)  as action_type, 
                if(postbox_history.modified_date, UNIX_TIMESTAMP(postbox_history.modified_date), if(postbox.deleted_date, postbox.deleted_date,postbox_history.deleted_date)),
                if(postbox.new_postbox_type, postbox.new_postbox_type, if(postbox_history.new_postbox_type, postbox_history.new_postbox_type, postbox_history.type)) as after_type
                FROM postbox_history 
                INNER JOIN customers on customers.customer_id = postbox_history.customer_id
                LEFT JOIN postbox on postbox.postbox_id = postbox_history.postbox_id
                WHERE    postbox.postbox_code IS NOT NULL 
                AND (customers.`status` = 0 AND postbox_history.deleted = 1 AND postbox_history.completed_delete_flag = 0)
                GROUP BY
                postbox.postbox_code,
                postbox.postbox_name,
                postbox.name, 
                postbox.company

-- UNION ALL
-- POSTBOX_DELETE_ORDER_BY_SYSTEM
INSERT INTO postbox_history_activity (customer_id,postbox_id,postbox_code, postbox_name, location_available_id,name,company,action_type,action_date,type)
                SELECT 
                customers.customer_id, 
                postbox.postbox_id, 
                postbox.postbox_code, 
                postbox.postbox_name, 
                postbox.location_available_id,
                postbox.name, 
                postbox.company, 
                if((customers.`status` = 1 AND ( ( postbox_history.deleted = 1 AND postbox_history.completed_delete_flag = 1) OR ( postbox.deleted = 1 AND postbox.completed_delete_flag = 1) )), '7', null)  as action_type, 
                customers.deleted_date,
                if(postbox.new_postbox_type, postbox.new_postbox_type, if(postbox_history.new_postbox_type, postbox_history.new_postbox_type, postbox_history.type)) as after_type
                FROM postbox_history 
                INNER JOIN customers on customers.customer_id = postbox_history.customer_id
                LEFT JOIN postbox on postbox.postbox_id = postbox_history.postbox_id
                WHERE   postbox.postbox_code IS NOT NULL 
                AND (customers.`status` = 1 AND  postbox_history.deleted = 1 AND postbox_history.completed_delete_flag = 1)
                GROUP BY
                postbox.postbox_code,
                postbox.postbox_name,
                postbox.name, 
                postbox.company

-- UNION ALL
-- POSTBOX_DELETE
INSERT INTO postbox_history_activity (customer_id,postbox_id,postbox_code, postbox_name, location_available_id,name,company,action_type,action_date,type)
                SELECT 
                customers.customer_id, 
                postbox.postbox_id, 
                postbox.postbox_code, 
                postbox.postbox_name, 
                postbox.location_available_id,
                postbox.name, 
                postbox.company, 
                if(( ( postbox_history.deleted = 1 AND postbox_history.completed_delete_flag = 1) OR ( postbox.deleted = 1 AND postbox.completed_delete_flag = 1) ) , '8', null)  as action_type, 
                if(postbox.plan_deleted_date, UNIX_TIMESTAMP(DATE_ADD(FROM_UNIXTIME(UNIX_TIMESTAMP(postbox.plan_deleted_date)),INTERVAL 1 DAY)),if(postbox_history.plan_deleted_date, UNIX_TIMESTAMP(DATE_ADD(FROM_UNIXTIME(UNIX_TIMESTAMP(postbox_history.plan_deleted_date)),INTERVAL 1 DAY)), UNIX_TIMESTAMP(postbox_history.modified_date))),
                if(postbox.new_postbox_type, postbox.new_postbox_type, if(postbox_history.new_postbox_type, postbox_history.new_postbox_type, postbox_history.type)) as after_type
                FROM postbox_history 
                INNER JOIN customers on customers.customer_id = postbox_history.customer_id
                LEFT JOIN postbox on postbox.postbox_id = postbox_history.postbox_id
                WHERE    postbox.postbox_code IS NOT NULL 
                AND (customers.`status` = 0 AND postbox_history.deleted = 1 AND postbox_history.completed_delete_flag = 1)
                GROUP BY
                postbox.postbox_code,
                postbox.postbox_name,
                postbox.name, 
                postbox.company

