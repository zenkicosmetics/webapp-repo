<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Group model
 */
class Countries_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->set_table_name('country');
        $this->profile_table = $this->db->dbprefix('country');
        $this->primary_key = 'id';
    }

    /**
     * Get all paging data
     *
     * @param unknown_type $array_where
     *            The array of condition (array ('name' => 'KhoiLV', 'age' => 30))
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
    public function get_list_countries_paging(array $array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_by_many($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('*');

        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }
        $this->db->limit($limit);
        if (!empty($sort_column)) {
            $this->db->order_by($sort_column, $sort_type);
        }
        $rows = $this->db->get($this->_table, $limit, $start)->result();

        return array(
            "total" => $total_record,
            "data" => $rows
        );
    }

    public function getAllCountriesForDropDownList()
    {
        $this->db->select('id, country_name');
        $this->db->from('country');
        $this->db->order_by('country_name', 'ASC');

        $query = $this->db->get();
        $rows = $query->result();

        return $rows;
    }
    
    public function getAllLanguagesForDropDownList()
    {
    	$this->db->select('language')->distinct();
    	$this->db->from('country');
    
    	$query = $this->db->get();
    	$rows = $query->result();
    
    	return $rows;
    }
    
    public function get_country_by($array_where)
    {
    	$this->db->select('country_name');
    	$this->db->from('country');
    	
    	// Search all data with input condition
    	foreach ($array_where as $key => $value) {
    		$this->db->where($key, $value);
    	}
    
    	$query = $this->db->get();
    	$ret  = $query->row();
    
    	return $ret->country_name;
    }
}