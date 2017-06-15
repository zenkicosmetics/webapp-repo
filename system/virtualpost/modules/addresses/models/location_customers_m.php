<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author 
 */
class location_customers_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('location_customers');
        $this->primary_key = 'id';
    }
    
    public function get_all_enterprise_location() {
        $this->db->select('location_customers.*, customers.customer_code');
        $this->db->join('customers', 'location_customers.parent_customer_id = customers.customer_id', "inner");
        
        return $this->db->get($this->_table)->result();
    }
    
    public function get_location_by($location_id){
        $this->db->select("location_customers.*, customers.customer_code, location.location_name");
        $this->db->join('customers', 'location_customers.parent_customer_id=customers.customer_id', 'inner');
        $this->db->join('location', 'location_customers.location_id=location.id', 'inner');
        $this->db->where("location_customers.location_id", $location_id);
        return $this->db->get($this->_table)->row();
    }
    
}