<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author TienNH
 */
class instance_m extends MY_SupperAdminModel {
    // Declare supprt admin database instance
    private $supperadmin_db;
    
    function __construct() {
        parent::__construct();
        $this->supperadmin_db = $this->load->database('supper_admin', TRUE);
        $this->_table = $this->profile_table = $this->supperadmin_db->dbprefix('instances');
        $this->primary_key = 'instance_id';
    }
    
    /**
     * Get all paging data
     * 
     * @param unknown_type $array_where
     *            The array of condition (array ('name' => 'DungNT', 'age' => 30))
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
    public function get_customer_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC') {
        // Count all record with input condition
        $total_record = $this->count_by_instance_paging($array_where);
        if ($total_record == 0) {
            return array (
                    "total" => 0,
                    "data" => array () 
            );
        }
        
        $this->supperadmin_db->select('instances.instance_id,instances.instance_code,instances.name');
        $this->supperadmin_db->select('ID.domain_name, ID.full_url, ID.domain_type');
        $this->supperadmin_db->select('IA.s3_name, IA.s3_type');
        $this->supperadmin_db->select('IDB.*');
        
        $this->supperadmin_db->join('instance_domain ID', 'ID.instance_id = instances.instance_id', 'inner');
        $this->supperadmin_db->join('instance_amazon IA', 'IA.instance_id = instances.instance_id', 'inner');
        $this->supperadmin_db->join('instance_database IDB', 'IDB.instance_id = instances.instance_id', 'inner');
        
        // Search all data with input condition
        foreach ( $array_where as $key => $value ) {
            if ($value != null) {
                $this->supperadmin_db->where($key, $value);
            }
            else {
                $this->supperadmin_db->where($key);
            }
        }
        $this->supperadmin_db->limit($limit);
        if (! empty($sort_column)) {
            $this->supperadmin_db->order_by($sort_column, $sort_type);
        }
        $data = $this->supperadmin_db->get($this->_table, $limit, $start)->result();
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
    public function count_by_instance_paging($array_where) {
        $this->supperadmin_db->select('COUNT(instances.instance_id) AS total_record');
        $this->supperadmin_db->join('instance_domain ID', 'ID.instance_id = instances.instance_id', 'inner');
        foreach ( $array_where as $key => $value ) {
            if ($value != null) {
                $this->supperadmin_db->where($key, $value);
            }
            else {
                $this->supperadmin_db->where($key);
            }
        }
        $result = $this->supperadmin_db->get($this->_table)->row();
        return $result->total_record;
    }
}