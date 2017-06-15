<?php

defined('BASEPATH') or exit('No direct script access allowed');

class cases_verification_settings_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('cases_verification_settings');
        $this->primary_key = 'id';
    }
    
    /**
     * Gets devices template paging.
     *
     * @param unknown $array_where
     * @param number $start
     * @param number $limit
     * @param unknown $sort_column
     * @param string $sort_type
     * @return multitype:number multitype: |multitype:unknown multitype:
     */
    public function get_cases_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC') {
        // Count all record with input condition
        $total_record = $this->count_cases_paging($array_where);
        if ($total_record == 0) {
            return array (
                    "total" => 0,
                    "data" => array ()
            );
        }
    
        $this->db->select('cases_verification_settings.*, country.country_name')->distinct();
        
        $this->db->join("country", "cases_verification_settings.country_code = country.country_code", "left");

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
    
    /**
     * Count customer
     *
     * @param unknown_type $array_where
     */
    public function count_cases_paging ($array_where)
    {
    	$this->db->select('COUNT(DISTINCT(cases_verification_settings.id)) AS total_record');
    	
    	$this->db->from('cases_verification_settings');
    	 $this->db->join("country", "cases_verification_settings.country_code = country.country_code", "left");
    
    	foreach ($array_where as $key => $value) {
    		if ($value != null) {
    			$this->db->where($key, $value);
    		}
    		else {
    			$this->db->where($key);
    		}
    	}
    	$result = $this->db->get()->row();
    	return $result->total_record;
    }
}