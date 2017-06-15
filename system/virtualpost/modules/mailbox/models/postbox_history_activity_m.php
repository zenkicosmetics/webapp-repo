<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * #1180 create postbox history page like check item page 
 * @author Thanhth
 */
class postbox_history_activity_m extends MY_Model {

    function __construct() {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('postbox_history_activity');
        $this->primary_key = 'id';
    }

    /**
     * #1180 create postbox history page like check item page
     * Get all paging data
     *
     * @param unknown_type $array_where
     *            The array of condition (array ('name' => 'ThanhTH', 'age' => 30))
     * @param unknown_type $start
     *            The offset paging
     * @param unknown_type $limit
     *            The number of record per page
     * @param unknown_type $sort_column
     *            The sort column
     * @param unknown_type $sort_type
     *            The sort type
     * @return The array object array('total' => '9999', 'data' => '');
     */
    public function get_postbox_history_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC') {
        // Count all record with input condition
        $total_record = $this->count_by_postbox_history_paging($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('postbox_history_activity.*')->distinct();
        $this->db->select('customers.customer_id, customers.customer_code, customers.email');
        $this->db->join('customers', ' customers.customer_id = postbox_history_activity.customer_id', 'inner');
        $this->db->order_by('postbox_history_activity.action_date', $sort_type);

        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $this->db->limit($limit);
        if (!empty($sort_column)) {
            $this->db->order_by($sort_column, $sort_type);
        }

        $data = $this->db->get($this->_table, $limit, $start)->result();

        return array(
            "total" => $total_record,
            "data" => $data
        );
    }

    /**
     * #1180 create postbox history page like check item page
     * Count postbox
     *
     * @param unknown_type $array_where
     */
    public function count_by_postbox_history_paging($array_where) {
        $this->db->select('COUNT(DISTINCT(postbox_history_activity.id)) AS total_record');
        $this->db->from('postbox_history_activity');
        $this->db->join('customers', 'customers.customer_id = postbox_history_activity.customer_id', 'inner');


        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $result = $this->db->get()->row();
        return $result->total_record;
    }

    /**
     * #1180 create postbox history page like check item page
     * insert into select for data
     */
    public function insert_into($customer_id) {
        $sql = "INSERT INTO postbox_history_activity (customer_id,postbox_id,postbox_code, postbox_name, location_available_id,name,company,action_type,action_date,type)
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
                WHERE  
                customers.customer_id = '".$customer_id."' 
                AND postbox.postbox_code IS NOT NULL
                AND postbox.created_date IS NOT NULL
                GROUP BY
                postbox.postbox_code,
                postbox.postbox_name,
                postbox.name, 
                postbox.company

                UNION ALL

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
                WHERE  
               customers.customer_id = '".$customer_id."' 
                AND postbox.postbox_code IS NOT NULL 
                AND (postbox_history.apply_date IS NOT NULL AND postbox_history.plan_date_change_postbox_type IS NULL)
                AND (  ( postbox.type = 3  AND (postbox_history.new_postbox_type = 1 OR postbox_history.new_postbox_type = 2 OR postbox.new_postbox_type = 1 OR postbox.new_postbox_type = 2 ) ) 
                OR ( postbox.type = 2 AND  (postbox_history.new_postbox_type = 1 OR postbox.new_postbox_type = 1)) 
                OR (( (postbox.type = 2 OR postbox.type = 3) AND postbox_history.type = 1) OR ( postbox.type = 3 AND postbox_history.type = 2)) )
                GROUP BY
                postbox.postbox_code,
                postbox.postbox_name,
                postbox.name, 
                postbox.company

                UNION ALL

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
                WHERE  
                customers.customer_id = '".$customer_id."' 
                AND postbox.postbox_code IS NOT NULL 
                AND (postbox_history.apply_date IS NOT NULL AND postbox_history.plan_date_change_postbox_type IS NULL)
                AND (  ( postbox.type = 1  AND (postbox_history.new_postbox_type = 3 OR postbox_history.new_postbox_type = 2 OR postbox.new_postbox_type = 3 OR postbox.new_postbox_type = 2) ) 
                 OR ( postbox.type = 2 AND ( postbox_history.new_postbox_type = 3 OR postbox.new_postbox_type = 3)) 
                OR ( ( (postbox.type = 2 OR postbox.type = 1) AND postbox_history.type = 3) OR ( postbox.type = 1 AND postbox_history.type = 2)) )
                GROUP BY
                postbox.postbox_code,
                postbox.postbox_name,
                postbox.name, 
                postbox.company

                UNION ALL

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
                WHERE  
                customers.customer_id = '".$customer_id."' 
                AND postbox.postbox_code IS NOT NULL 
                AND (postbox_history.apply_date IS NOT NULL AND postbox_history.plan_date_change_postbox_type IS NOT NULL)
                AND (  ( postbox.type = 3  AND (postbox_history.new_postbox_type = 1 OR postbox_history.new_postbox_type = 2 OR postbox.new_postbox_type = 1 OR postbox.new_postbox_type = 2) ) 
                OR ( postbox.type = 2 AND  (postbox_history.new_postbox_type = 1 OR postbox.new_postbox_type = 1) )
                OR (( (postbox.type = 2 OR postbox.type = 3) AND postbox_history.type = 1) OR ( postbox.type = 3 AND postbox_history.type = 2)) )
                GROUP BY
                postbox.postbox_code,
                postbox.postbox_name,
                postbox.name, 
                postbox.company

                UNION ALL

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
                WHERE  
                customers.customer_id = '".$customer_id."' 
                AND postbox.postbox_code IS NOT NULL
                AND (postbox_history.apply_date IS NOT NULL OR postbox_history.plan_date_change_postbox_type IS NOT NULL)
                 AND (  ( postbox.type = 1  AND (postbox_history.new_postbox_type = 3 OR postbox_history.new_postbox_type = 2 OR postbox.new_postbox_type = 3 OR postbox.new_postbox_type = 2) ) 
                 OR ( postbox.type = 2 AND (postbox_history.new_postbox_type = 3 OR postbox.new_postbox_type = 3) )
                OR ( ( (postbox.type = 2 OR postbox.type = 1) AND postbox_history.type = 3) OR ( postbox.type = 1 AND postbox_history.type = 2)))
                GROUP BY
                postbox.postbox_code,
                postbox.postbox_name,
                postbox.name, 
                postbox.company

                UNION ALL

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
                WHERE  
                customers.customer_id = '".$customer_id."' 
                AND postbox.postbox_code IS NOT NULL 
                AND (customers.`status` = 0 AND postbox_history.deleted = 1 AND postbox_history.completed_delete_flag = 0)
                GROUP BY
                postbox.postbox_code,
                postbox.postbox_name,
                postbox.name, 
                postbox.company

                UNION ALL

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
                WHERE  
               customers.customer_id = '".$customer_id."' 
                AND postbox.postbox_code IS NOT NULL 
                AND (customers.`status` = 1 AND  postbox_history.deleted = 1 AND postbox_history.completed_delete_flag = 1)
                GROUP BY
                postbox.postbox_code,
                postbox.postbox_name,
                postbox.name, 
                postbox.company

                UNION ALL

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
                WHERE  
                customers.customer_id = '".$customer_id."' 
                AND postbox.postbox_code IS NOT NULL 
                AND (customers.`status` = 0 AND postbox_history.deleted = 1 AND postbox_history.completed_delete_flag = 1)
                GROUP BY
                postbox.postbox_code,
                postbox.postbox_name,
                postbox.name, 
                postbox.company";
        // Query sql 
        $query = $this->db->query($sql);

        if ($query) {
            return 1;
        }
    }

}
