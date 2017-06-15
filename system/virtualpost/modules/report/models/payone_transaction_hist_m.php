<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author TienNH
 */
class payone_transaction_hist_m extends MY_Model {
    function __construct() {
        parent::__construct();
        $this->_table = $this->profile_table = $this->db->dbprefix('payone_transaction_hist');
        $this->primary_key = 'id';
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
    public function get_transaction_report_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC') {
        // Count all record with input condition
        $total_record = $this->count_transaction_report_paging($array_where);
        if ($total_record == 0) {
            return array (
                    "total" => 0,
                    "data" => array () 
            );
        }
        
        $this->db->select('payone_transaction_hist.*');
        $this->db->select('p.name, p.company');
        $this->db->select('customers.customer_id');
        $this->db->select('customers.email');
        $this->db->from('payone_transaction_hist');
        $this->db->join('customers', 'customers.customer_id = payone_transaction_hist.customer_id', 'inner');
        $this->db->join('postbox p', 'p.customer_id = customers.customer_id', 'inner');
        // Search all data with input condition
        $this->db->where('p.is_main_postbox', '1');
        foreach ( $array_where as $key => $value ) {
            if ($value != null) {
                $this->db->where($key, $value);
            }
            else {
                $this->db->where($key);
            }
        }
        if ($limit > 0) {
            $this->db->limit($limit);
        }
        if (! empty($sort_column)) {
            if ($sort_column == 'name') {
                $this->db->order_by('p.name', $sort_type);
            }
            else if ($sort_column == 'company') {
                $this->db->order_by('p.company', $sort_type);
            }
            else {
                $this->db->order_by($sort_column, $sort_type);
            }
        }
        if ($limit > 0) {
            $data = $this->db->get(null, $limit, $start)->result();
        }
        else {
            $data = $this->db->get(null)->result();
        }
        
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
    public function count_transaction_report_paging($array_where) {
        $this->db->select('COUNT(payone_transaction_hist.id) AS total_record');
        $this->db->from('payone_transaction_hist');
        $this->db->join('customers', 'customers.customer_id = payone_transaction_hist.customer_id', 'inner');
        $this->db->join('postbox p', 'p.customer_id = customers.customer_id', 'inner');
        $this->db->where('p.is_main_postbox', '1');
        foreach ( $array_where as $key => $value ) {
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