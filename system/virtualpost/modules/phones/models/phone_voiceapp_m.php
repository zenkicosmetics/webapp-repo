<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author DungNT
 */
class phone_voiceapp_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('phone_voiceapp');
        $this->primary_key = 'id';
    }
    
    /**
     * Get all phone number
     * @param unknown $array_where
     * @param number $start
     * @param number $limit
     * @param unknown $sort_column
     * @param string $sort_type
     * @return multitype:number multitype: |multitype:unknown multitype:
     */
    public function get_voiceapp_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_by_many($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('phone_user_voiceapp.*');

        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }

        $this->db->limit($limit, $start);
        if (!empty($sort_column)) {
            $this->db->order_by($sort_column, $sort_type);
        }
        $data = $this->db->get($this->_table, $limit, $start)->result();

        return array(
            "total" => $total_record,
            "data" => $data
        );
    }
}