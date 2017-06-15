<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author Hung
 */
class customers_forward_address_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('customers_forward_address');
        $this->primary_key = 'id';

        $this->load->model('addresses/customers_forward_address_hist_m');
    }
    
    /**
     * @Override
     */
    public function insert($data, $skip_validation = FALSE) {
    	 
    	if($data && is_array($data))
    	{
    		$this->update_customer_forward_address_hist($data,"Insert");
    	}
    	return parent::insert($data, $skip_validation = FALSE);
    	 
    }
    
    /**
     * @Override
     */
    public function insert_many($data, $skip_validation = FALSE) {
    
    	if($data && is_array($data))
    	{
    		foreach($data as $row){
    			if(is_array($row))
    			{
    				$this->update_customer_forward_address_hist($data,"Insert");
    			}
    			else {
    				continue;
    			}
    			
    		}
    
    	}
    	return parent::insert_many($data, $skip_validation = FALSE);
    
    }
    
    /**
     * @Override
     */
    public function update($primary_value, $data, $skip_validation = FALSE) {
    	// Get current key
    	$return = parent::update($primary_value, $data, $skip_validation);
    	$customer_address = $this->get($primary_value);
    	$customer_address->customers_forward_address_id = $customer_address->id;
        unset($customer_address->id);
    	$data = APUtils::convertObjectToArray($customer_address);
    	$this->update_customer_forward_address_hist($data,"Update");
    
    	return $return;
    }

    public function delete($id){
        // Get current key
        $customer_address = $this->get($id);
        if($customer_address && is_object($customer_address)){
            
            $customer_address->customers_forward_address_id = $customer_address->id;
            unset($customer_address->id);
            $data = APUtils::convertObjectToArray($customer_address);
            $this->update_customer_forward_address_hist($data,"Delete");
        }
        
        return parent::delete($id);
    }
    
    /**
     * @Override
     */
    public function update_by_many($array_where, $data) {
    	
    	$return = parent::update_by_many($array_where, $data);
    	$customer_address_list = $this->get_many_by_many($array_where);
    	if(count($customer_address_list)){
	    	foreach($customer_address_list as $customer_address){
                $customer_address->customers_forward_address_id = $customer_address->id;
	    		unset($customer_address->id);
	    		$data = APUtils::convertObjectToArray($customer_address);
	    		$this->update_customer_forward_address_hist($data,"Update");
	    	}
    	}
    	return $return;
    }

    /**
     * Update customer address history.
     * 
     * @param unknown_type $customer_address
     */
    public function update_customer_forward_address_hist($customer_forward_address, $actionType = null) {       
        if($customer_forward_address && is_array($customer_forward_address) ){
            
            ci()->load->model('addresses/customers_forward_address_hist_m');
            $customer_forward_address['hist_created_date'] = date("Y-m-d H:i:s");
            $customer_forward_address['hist_update_date']  = date("Y-m-d H:i:s");
            $customer_forward_address['action_type'] = $actionType;
            //$data = APUtils::convertObjectToArray($customer_address);
            $this->customers_forward_address_hist_m->insert($customer_forward_address);
        }
    }
    
    
    
 
}