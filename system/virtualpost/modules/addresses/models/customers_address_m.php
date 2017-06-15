<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author TienNH
 */
class customers_address_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('customers_address');
        $this->primary_key = 'customer_id';
    }
    
    /*
     * @des: Get info customer address by id customer
     */
    public function get_customer_address($customer_id){
    	
    	$sql = "( SELECT 
    			 0 as shipping_address_id,
                 customer_id,
				`shipment_address_name` ,
				`shipment_company` ,
				`shipment_street` ,
				`shipment_postcode` ,
				`shipment_city`  ,
				`shipment_region` ,
				`shipment_country` ,
    			`shipment_phone_number` ,
				c.country_name,
				1 as is_primary_address
				FROM customers_address as ca
				LEFT JOIN country as c on ca.shipment_country = c.id
				WHERE customer_id = ".$customer_id."
				)
				UNION
				( SELECT 
                cf.`id` as shipping_address_id,
				`customer_id`
				,`shipment_address_name`
				,`shipment_company`
				,`shipment_street`
				,`shipment_postcode`
				,`shipment_city`
				,`shipment_region`
				,`shipment_country`
				,`shipment_phone_number`		
				, c.country_name
				, 0
				FROM customers_forward_address as cf 
				LEFT JOIN country as c on c.id=cf.shipment_country
				WHERE customer_id = ".$customer_id."
				) ORDER BY shipping_address_id DESC";
    	$query = $this->db->query($sql);
    	return $query->result();
    }
    
    /**
     * Gets invoicing address of customer.
     * @param type $customer_id
     * @return type
     */
    public function get_invoicing_address_by($customer_id){
        $this->db->select("customers_address.*");
        $this->db->select("country.country_name as invoicing_country_name");
        $this->db->join("country", "country.id=customers_address.invoicing_country", "left");
        $this->db->where("customers_address.customer_id", $customer_id);
        
        return $this->db->get($this->_table)->row();
    }
    
    /**
    * get all postbox of customer
    *     
    * @param mixed $custid
    */
    public function get_cust_postbox($custid=null){
        $query = $this->db->get_where('postbox',array("customer_id"=>$custid));        
        return $query->result();
    }
    
    /**
     * @Override
     */
    public function insert($data, $skip_validation = FALSE) {
    	
    	if($data && is_array($data))
    	{
    		$this->update_customer_address_hist($data,$actionType = "Insert");
    	}
    	parent::insert($data, $skip_validation = FALSE);
    	
    }
    
    /**
     * @Override
     */
    public function insert_many($data, $skip_validation = FALSE) {
    	 
    	if($data && is_array($data))
    	{
    		foreach($data as $row){
    			if(is_array($row))
    				$this->update_customer_address_hist($data,$actionType = "Insert");
    			else continue;
    		}
    		
    	}
    	parent::insert_many($data, $skip_validation = FALSE);
    	 
    }
    
    /**
     * Update a record, specified by an ID.
     *
     * @author Jamie Rumbelow
     * @param integer $primary_value
     *            The primary key basically the row's ID.
     * @param array $data
     *            The data to update.
     * @param boolean $skip_validation
     *            Whether we should skip the validation of the data.
     * @return boolean
     */
    public function update($primary_value, $data, $skip_validation = FALSE) {
        // Get current key
        $return = parent::update($primary_value, $data, $skip_validation);
    	$customer_address = $this->get($primary_value);
    	$data = APUtils::convertObjectToArray($customer_address);
    	
        $this->update_customer_address_hist($data,$actionType = "Update");
        
    	return $return;
    }
    
    /**
     * Update a record, specified by $key and $val.
     * The function accepts ghost parameters, fetched via func_get_args().
     * Those are:
     * 1. string `$key` The key to update with.
     * 2. string `$value` The value to match.
     * 3. array `$data` The data to update with.
     * The first two are used in the query in the where statement something
     * like:
     * <code>UPDATE {table} SET {$key}={$data} WHERE {$key}={$value}</code>
     *
     * @author Jamie Rumbelow
     * @return boolean
     */
    public function update_by_many($array_where, $data) {
    	$return = parent::update_by_many($array_where, $data);
   
        /* Record data change to customers address hist */
        $list_customer_address = $this->get_many_by_many($array_where);
        if(count($list_customer_address)){
        	foreach($list_customer_address as $obj_customer_address){
        		$customer_address = APUtils::convertObjectToArray($obj_customer_address);
        		$this->update_customer_address_hist($customer_address, $actionType = "Update");
        	}
        }
        
    	return $return;
    }
    
    /**
     * Update customer address history.
     * 
     * @param unknown_type $customer_address
     */
    public function update_customer_address_hist($customer_address, $actionType = null) {   	
        if($customer_address && is_array($customer_address) ){
        	
            ci()->load->model('addresses/customers_address_hist_m');
            $customer_address['updated_date'] = now();
            $customer_address['action_type'] = $actionType;
            //$data = APUtils::convertObjectToArray($customer_address);
            ci()->customers_address_hist_m->insert($customer_address);
        }
    }
}