<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Group model
 */
class Setting_m extends MY_Model {
    
    function __construct() {
        parent::__construct();
        $this->profile_table = $this->db->dbprefix('settings');
        $this->primary_key = 'SettingKey';
    }
    
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
    public function get_category_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC') {
        // Count all record with input condition
        $total_record = $this->count_by_many($array_where);
        if ($total_record == 0) {
            return array (
                    "total" => 0,
                    "data" => array ()
            );
        }
    
        $this->db->select('*');
        
        // Search all data with input condition
        foreach ( $array_where as $key => $value ) {
            $this->db->where($key, $value);
        }
        $this->db->limit($limit);
        if (! empty($sort_column)) {
            $this->db->order_by($sort_column, $sort_type);
        }
        $data = $this->db->get($this->_table, $limit, $start)->result();
    
        return array (
                "total" => $total_record,
                "data" => $data
        );
    }
}