<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author DuNT
 */
class customer_shipping_report_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('customer_shipping_report');
        $this->primary_key = 'id';
    }
    
    /**
     * get paging.
     * 
     * @param type $array_where
     * @param type $start
     * @param type $limit
     * @param type $sort_column
     * @param type $sort_type
     */
    public function get_shipping_report_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC'){
        // Count all record with input condition
        $total_record = $this->count_shipping_report_paging($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('customer_shipping_report.*')->distinct();
        $this->db->select('c.customer_code, c.email, ss.name as service_name, sc.name as carrier_name');
        $this->db->select('u.display_name as completed_by');
        $this->db->join('customers c', 'c.customer_id = customer_shipping_report.customer_id', 'left');
        $this->db->join('users u', 'u.id = customer_shipping_report.completed_by', 'left');
        $this->db->join('shipping_services ss', 'customer_shipping_report.shipping_service_id = ss.id', 'left');
        $this->db->join('shipping_carriers sc', 'customer_shipping_report.carrier_id = sc.id', 'left');

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }

        if (!empty($sort_column)) {
            $this->db->order_by($sort_column, $sort_type);
        }

        $data = $this->db->get($this->_table, $limit, $start)->result();

        return array(
            "total" => $total_record,
            "data" => $data
        );
    }
    
    /**
     * count shipping report paging.
     * 
     * @param type $array_where
     */
    public function count_shipping_report_paging($array_where){
        $this->db->select('COUNT(DISTINCT(customer_shipping_report.id)) AS total_record');
        $this->db->join('customers c', 'c.customer_id = customer_shipping_report.customer_id', 'left');
        $this->db->join('users u', 'u.id = customer_shipping_report.completed_by', 'left');
        $this->db->join('shipping_services ss', 'customer_shipping_report.shipping_service_id = ss.id', 'left');
        $this->db->join('shipping_carriers sc', 'customer_shipping_report.carrier_id = sc.id', 'left');

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $result = $this->db->get($this->_table)->row();
        return $result->total_record;
    }
    
    /**
     * gets total charge
     * 
     * @param type $array_where
     * @return type
     */
    public function get_total_charge($array_where){
        $this->db->select('SUM(postal_charge) as postal_charge');
        $this->db->select('SUM( case when upcharge is null then postal_charge else (1 + upcharge/100) * postal_charge end ) as upcharge');
        $this->db->join('customers c', 'c.customer_id = customer_shipping_report.customer_id', 'left');
        $this->db->join('users u', 'u.id = customer_shipping_report.completed_by', 'left');
        $this->db->join('shipping_services ss', 'customer_shipping_report.shipping_service_id = ss.id', 'left');
        $this->db->join('shipping_carriers sc', 'customer_shipping_report.carrier_id = sc.id', 'left');

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }

        $data = $this->db->get($this->_table)->row();
        return $data;
    }
}