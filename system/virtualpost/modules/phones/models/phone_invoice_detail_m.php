<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author DungNT
 */
class phone_invoice_detail_m extends MY_Model {

    function __construct() {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('phone_invoice_detail');
        $this->primary_key = 'id';
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
    public function get_invoice_detail_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC', $group_by = '') {
        // Count all record with input condition
        $total_record = $this->count_by_many($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('phone_invoice_detail.*, phone_invoice_by_location.vat, location.location_name');
        $this->db->join('phone_invoice_by_location', 'phone_invoice_by_location.id = phone_invoice_detail.invoice_summary_id', 'inner');
        $this->db->join('location', 'location.id = phone_invoice_detail.location_id', 'left');
        $this->db->join('cusotmers', 'customers.customer_id = phone_invoice_detail.customer_id', 'left');

        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }
        $this->db->limit($limit, $start);
        if (!empty($sort_column)) {
            $this->db->order_by($sort_column, $sort_type);
        }
        if (!empty($group_by)) {
            $this->db->group_by($group_by);
        }
        $data = $this->db->get($this->_table, $limit, $start)->result();

        return array(
            "total" => $total_record,
            "data" => $data
        );
    }

}
