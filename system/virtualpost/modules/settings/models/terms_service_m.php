<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Terms and service model
 * 
 * @author DungNT
 */
class Terms_service_m extends MY_Model {
    function __construct() {
        parent::__construct();
        $this->profile_table = $this->db->dbprefix('terms_services');
        $this->_table = $this->db->dbprefix('terms_services');
    }
    
    /**
     * Gets term & service of system
     * @param type $array_where
     */
    public function get_system_term_service($array_where){
        if(!empty($array_where)){
            foreach($array_where as $key=>$value){
                if($value == null){
                    $this->db->where($key);
                }else{
                    $this->db->where($key, $value);
                }
            }
        }
        
        $this->db->order_by('created_date', 'desc');
        
        // customer_id=0: system clevvermail term & service
        $this->db->where('customer_id', 0);
        
        return $this->db->get($this->_table)->row();
    }
    
    /**
     * Gets term & service of enterprise customer
     * @param type $array_where
     */
    public function get_enterprise_term_service($customer_id, $array_where){
        if(!empty($array_where)){
            foreach($array_where as $key=>$value){
                if($value == null){
                    $this->db->where($key);
                }else{
                    $this->db->where($key, $value);
                }
            }
            
        }
        
        // customer_id=0: system clevvermail term & service, customer_id > 0 => term & service of enteprise customer.
        $this->db->where('customer_id', $customer_id);
        
        return $this->db->get($this->_table)->result();
    }
}

/* End of file settings_m.php */