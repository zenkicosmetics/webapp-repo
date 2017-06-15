<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author DuNT
 */
class pricing_phones_outboundcalls_customer_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('pricing_phones_outboundcalls_customer');
        $this->primary_key = 'id';
    }
    
    /**
     * Get all phone call history.
     * 
     * @param unknown $array_where
     * @param number $start
     * @param number $limit
     * @param unknown $sort_column
     * @param string $sort_type
     * @return multitype:number multitype: |multitype:unknown multitype:
     */
    public function get_price_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_price_paging($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('pricing_phones_outboundcalls_customer.*');
        $this->db->select('country.country_name');

        $this->db->join('country', 'pricing_phones_outboundcalls_customer.country_code_3 = country.country_code_3', "left");

        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
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
    
    public function count_price_paging($array_where)
    {
        $this->db->select('COUNT(pricing_phones_outboundcalls_customer.id) AS TotalRecord');

        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            if ($value != '') {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $row = $this->db->get($this->_table)->row();
        return $row->TotalRecord;
    }
}