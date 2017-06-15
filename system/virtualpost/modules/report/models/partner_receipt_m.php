<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author Nguyen Dung
 */
class partner_receipt_m extends MY_Model {

    function __construct() {
        parent::__construct();
        $this->_table = $this->profile_table = $this->db->dbprefix('partner_receipt');
        $this->primary_key = 'id';
    }

    /**
     * #1296 add receipt scan/upload to receipts 
     * 
     * Get all receipt paging data 
     * 
     * function get_receipt_paging
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
    public function get_receipt_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC') {
        // Count all record with input condition
        $total_record = $this->count_receipt_paging($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        // select fields
        $this->db->select('partner_receipt.*, partner_partner.partner_name, location.location_name')->distinct();

        // Join tables
        $this->db->join('partner_partner', 'partner_receipt.partner_id = partner_partner.partner_id', 'inner');
        $this->db->join('location', 'partner_receipt.location_id = location.id', 'inner');

        // Condition array 
        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }

        //Limit 
        if ($limit > 0) {
            $this->db->limit($limit);
        }

        // Order by column
        if (!empty($sort_column)) {
            $this->db->order_by($sort_column, $sort_type);
        }

        // Get data 
        if ($limit > 0) {
            $data = $this->db->get($this->_table, $limit, $start)->result();
        } else {
            $data = $this->db->get($this->_table)->result();
        }

        // return total and data 
        return array(
            "total" => $total_record,
            "data" => $data
        );
    }

    /**
     * #1296 add receipt scan/upload to receipts 
     * 
     * Count number row receipt paging
     * 
     * function count_receipt_paging
     * 
     * @param unknown_type $array_where    
     * 
     * return number row         
     */
    public function count_receipt_paging($array_where) {
        // Select 
        $this->db->select('partner_receipt.id')->distinct();

        // Join tables
        $this->db->join('partner_partner', 'partner_receipt.partner_id = partner_partner.partner_id', 'inner');
        $this->db->join('location', 'partner_receipt.location_id = location.id', 'inner');

        // Condition array
        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }

        //get num row
        $result = $this->db->get($this->_table)->num_rows();

        //return num row
        return $result;
    }

}
