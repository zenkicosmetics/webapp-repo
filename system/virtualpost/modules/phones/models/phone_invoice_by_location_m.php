<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author DungNT
 */
class phone_invoice_by_location_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('phone_invoice_by_location');
        $this->primary_key = 'id';
    }
    
    /**
     * get invoice paging.
     * @param type $array_condition2
     * @param type $start
     * @param type $limit
     * @param type $sort_column
     * @param type $sort_type
     * @param type $group_by
     */
    public function get_invoice_paging($array_where, $start, $limit, $sort_column='', $sort_type='asc', $group_by=''){
        $this->db->select('SUM(incomming_quantity) as incomming_quantity, SUM(incomming_amount) as incomming_amount');
        $this->db->select('SUM(outcomming_quantity) as outcomming_quantity, SUM(outcomming_amount) as outcomming_amount');
        $this->db->select('SUM(phone_subscription_quantity) as phone_subscription_quantity, SUM(phone_subscription_amount) as phone_subscription_amount');
        $this->db->select('SUM(total_invoice) as total_invoice');
        $this->db->select('vat, vat_case, invoice_code, invoice_month, customer_id, location.location_name');
        $this->join('location', 'location.id=phone_invoice_by_location.location_id', 'left');

        $this->db->group_by('customer_id, invoice_month, invoice_code');
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

        return $data;
    }
}