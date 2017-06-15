<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author TienNH
 */
class payment_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('payment');
        $this->primary_key = 'payment_id';
    }
    
    /**
     * Gets all payment methods of customer.
     * 
     * @param unknown_type $customer_id
     * @return object
     */
    function get_payment_account($customer_id ,$start, $limit){
        $this->db->where("customer_id", $customer_id);
        $this->db->where("card_confirm_flag", APConstants::ON_FLAG);
        if ($limit > 0) {
            $this->db->limit($limit, $start);
        }
        return parent::get_all();
    }
    
    function count_payment_account($customer_id){
        $this->db->where("customer_id", $customer_id);
    
        return parent::count_all();
    }
    
    function get_all_cards_of_active_user(){
        $this->db->join("customers", "payment.customer_id=customers.customer_id", "inner");
        $this->db->where("customers.activated_flag", APConstants::ON_FLAG);
        $this->db->where("payment.primary_card", "1");
        $this->db->where("customers.status <> 1", null);
        
        return parent::get_all();
    }
}