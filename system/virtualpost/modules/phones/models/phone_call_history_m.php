<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author DungNT
 */
class phone_call_history_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('phone_call_history');
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
    public function get_phone_call_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_phone_call($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('phone_call_history.*');
        $this->db->select('phone_number.phone_number');

        $this->db->join('phone_number', 'phone_call_history.phone_number = phone_number.phone_number', "inner");

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
    
    public function count_phone_call($array_where)
    {
        $this->db->select('COUNT(phone_call_history.id) AS TotalRecord');
        $this->db->join('phone_number', 'phone_call_history.phone_number = phone_number.phone_number', "inner");

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