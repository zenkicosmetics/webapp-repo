<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @copyright Copyright (c) 2012-2013
 */
class email_customer_m extends MY_Model {
    protected $_table = 'email_customer';
    protected $primary_key = 'id';
    
    
    /**
     * Get all paging data
     *
     * @param unknown_type $array_where
     *            The array of condition (array ('name' => 'DungNT', 'age' =>
     *            30))
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
    public function get_email_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC', $group_by = '')
    {
        // Count all record with input condition
        $total_record = $this->count_email_paging($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('email_customer.*')->distinct();
        $this->db->join('emails', 'emails.code = email_customer.code', 'left');
        
        $this->db->where('emails.relevant_enterprise_account', APConstants::ON_FLAG);
        
        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }
        $this->db->limit($limit, $start);
        if (!empty($sort_column)) {
            $this->db->order_by($sort_column, $sort_type);
        }
        if (!empty($group_by)) {
            $this->db->group_by($group_by);
        }
        $data = $this->db->get($this->_table, $limit, $start)->result();

        return array(
            "total" => $total_record,
            "data" => $data
        );
    }
    
    /**
     * Count customer
     *
     * @param unknown_type $array_where
     */
    public function count_email_paging($array_where)
    {
        $this->db->select('COUNT(DISTINCT(email_customer.id)) AS total_record');
        $this->db->from('email_customer');
        $this->db->join('emails', 'emails.code = email_customer.code', 'left');
        
        $this->db->where('emails.relevant_enterprise_account', APConstants::ON_FLAG);
        

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
}

/* End of file email.php */
