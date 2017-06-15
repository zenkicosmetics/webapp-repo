<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author TienNH
 */
class customer_location_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('customers_location_available');
        $this->primary_key = 'location_available_id';
    }
    
    /**
    * get all location of customer
    *     
    * @param mixed $custid
    */
    public function get_customer_location($custid=null){
        $query = $this->db->get_where('customers_location_available',array("customer_id"=>$custid));        
        return $query->result();
    }
}