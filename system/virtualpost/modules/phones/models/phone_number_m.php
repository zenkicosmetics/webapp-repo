<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author DungNT
 */
class phone_number_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('phone_number');
        $this->primary_key = 'id';
    }
    
    /**
     * Get all phone number
     * @param unknown $array_where
     * @param number $start
     * @param number $limit
     * @param unknown $sort_column
     * @param string $sort_type
     * @return multitype:number multitype: |multitype:unknown multitype:
     */
    public function get_number_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_by_many($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('phone_number.*');
        $this->db->select('country.country_name');
        $this->db->select('phone_area_code.area_name');
        $this->db->select('phone_target.target_id, phone_target.target_type');

        $this->db->join('phone_area_code', 'phone_area_code.area_code = phone_number.area_code', "inner");
        $this->db->join('country', 'country.country_code_3 = phone_number.country_code', "inner");
        $this->db->join('phone_target', 'phone_target.id = phone_number.target_id', "left");

        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }

        $this->db->limit($limit);
        if (!empty($sort_column)) {
            $this->db->order_by($sort_column, $sort_type);
        }
        if ($limit > 0) {
            $data = $this->db->get($this->_table, $limit, $start)->result();
        } else {
            $data = $this->db->get($this->_table)->result();
        }

        return array(
            "total" => $total_record,
            "data" => $data
        );
    }
    
    /**
     * Get list of phone number by user id.
     * 
     * @param type $parent_customer_id
     * @param type $customer_id
     */
    public function get_list_phonenumber_by_user($parent_customer_id, $customer_id)
    {
        $this->db->select('phone_number.*');
        $this->db->select('phone_area_code.area_name as location_name');

        $this->db->join('phone_customer_users', 'phone_customer_users.customer_id = phone_number.customer_id', "inner");
        $this->db->join('phone_area_code', 'phone_area_code.area_code = phone_number.area_code', "inner");
        $this->db->join('customers', 'customers.customer_id = phone_number.customer_id', "inner");
        
        $this->db->where('phone_number.customer_id', $customer_id);
        $this->db->where('phone_number.parent_customer_id', $parent_customer_id);
        // $this->db->where('customers.activated_flag', '1');
        
        $this->db->order_by('phone_area_code.area_name', 'ASC');
        
        return $this->db->get($this->_table)->result();
    }
    
    /**
     * Get list of phone number by user id.
     * 
     * @param type $parent_customer_id
     * @param type $customer_id
     */
    public function get_list_avail_phonenumber_by_customer($parent_customer_id)
    {
        $this->db->select('phone_number.*');
        $this->db->select('phone_area_code.area_name');
        $this->db->select('country.country_name');

        $this->db->join('phone_area_code', 'phone_area_code.area_code = phone_number.area_code', "inner");
        $this->db->join('country', 'country.country_code_3 = phone_number.country_code', "inner");
        $this->db->where('phone_number.parent_customer_id', $parent_customer_id);
        $this->db->where('phone_number.customer_id IS NULL', null);
        // $this->db->where('phone_number.phone_user_id', null);
        
        $this->db->order_by('phone_area_code.area_name', 'ASC');
        
        return $this->db->get($this->_table)->result();
    }
    
    public function get_location($parent_customer_id, $customer_id, $phone_number) {
        $this->db->select('country.country_name');
        $this->db->select('phone_area_code.area_name as location_name');
        
        $this->db->join('phone_area_code', 'phone_area_code.area_code = phone_number.area_code', "inner");
        $this->db->join('country', 'phone_area_code.country_id = country.id', "inner");
        
        $this->db->where('phone_number.parent_customer_id', $parent_customer_id);
        // $this->db->where('phone_number.customer_id', $customer_id);
        $this->db->where('phone_number.phone_number', $phone_number);
        
        return $this->db->get($this->_table)->row();
    }
    
    /**
     * Count all postbox start with $customer_code_$city_code
     *
     * @param unknown_type $customer_code
     * @param unknown_type $city_code
     */
    public function get_max_phone_code($parent_customer_id)
    {
        $sql = "SELECT MAX(phone_code) as phone_code FROM phone_number WHERE parent_customer_id = '" . $parent_customer_id . "'";
        $query = $this->db->query($sql, array());
        $result = $query->result();
        $max_phone_code = '';
        if (!empty($result) && count($result) > 0) {
            $max_phone_code = $result [0]->phone_code;
        }
        if (empty($max_phone_code)) {
            return 0;
        } else {
            return intval(substr($max_phone_code, 13));
        }
    }
}