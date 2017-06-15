<?php defined('BASEPATH') or exit('No direct script access allowed');

class Customs_matrix_m extends MY_Model {
    
    function __construct() {
        parent::__construct();
        $this->_table = $this->db->dbprefix('customs_matrix');
        $this->primary_key = 'id';
    }
    
    /**
     * Get all countries by group by from_country
     */
    public function get_all_country()
    {
    	$this->db->select('from_country');
    	$this->db->group_by('from_country');
    	
    	$data = $this->db->get($this->_table)->result();
    
    	return $data;
    }
    
    /**
     * Get limit countries by group by from_country
     */
    public function get_country_limit($array_where, $selected_column, $start, $limit)
    {
    
    	$this->db->select($selected_column);
    	$this->db->group_by($selected_column);
        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }
    	$this->db->limit($limit, $start);
    	 
    	$data = $this->db->get($this->_table)->result();
    
    	return $data;
    }
    
    /**
     * Get limit countries by group by from_country
     */
    public function get_custom($from_list_country, $to_list_country)
    {
    
    	$this->db->select('from_country, to_country, custom_flag');
        if (count($from_list_country) > 0) {
            $this->db->where_in('from_country', $from_list_country);
        }
    	if (count($to_list_country) > 0) {
            $this->db->where_in('to_country', $to_list_country);
        }
    
    	$rows = $this->db->get($this->_table)->result();
    
    	return $rows;
    }
    
    /**
     * Update customs by from and to country
     */
    public function update_custom($custom_flag, $from_country, $to_country)
    {
    	$data = array('custom_flag'=>$custom_flag);
    	
    	$this->db->where('from_country', $from_country);
    	$this->db->where('to_country', $to_country);
    	$this->db->update($this->_table, $data);
    	
    }

}

/* End of file customs_matrix_m.php */