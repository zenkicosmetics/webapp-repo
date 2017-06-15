<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author TienNH
 */
class envelope_customs_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('envelope_customs');
        $this->primary_key = 'id';
    }
    
    public function get_id_by_many($list_envelope_id){
    	$this->db->select("id");
    	$this->db->where("envelope_id IN ('" . implode("','", $list_envelope_id). "')");
    	
    	$data = $this->db->get($this->_table)->result_array();
    	
    	return $data;
    }
    
    /**
     * Get all customer and postbox having request collect
     */
    public function get_envelope_customs_by_package_id($package_id, $customer_id, $postbox_id)
    {
        $this->db->select('envelope_customs.*')->distinct();
        $this->db->from("envelope_customs");
        $this->db->join("envelope_customs_detail", "envelope_customs_detail.customs_id = envelope_customs.id");
        $this->db->where('envelope_customs.package_id', $package_id);
        $this->db->where('envelope_customs.customer_id', $customer_id);
        $this->db->where('envelope_customs.postbox_id', $postbox_id);
        return $this->db->get()->row();
    }
    
    /**
     * Get all customer and postbox having request collect
     */
    public function get_envelope_customs_by_envelope_id($envelope_id)
    {
        $this->db->select('envelope_customs.*')->distinct();
        $this->db->from("envelope_customs");
        $this->db->join("envelope_customs_detail", "envelope_customs_detail.customs_id = envelope_customs.id");
        $this->db->where('envelope_customs.envelope_id', $envelope_id);
        return $this->db->get()->row();
    }
    
    /**
     * Get list envelopes need custom declaration of customers. 
     * @param type $customer_ids list customer (array or comma separated string)
     * @return type
     */
    public function get_list_pending_envelopes_custom_by_customer($customer_ids) {
        $this->db->select('envelope_customs.*')->distinct();
        $this->db->from("envelope_customs");
        $this->db->join("envelopes", "envelopes.id = envelope_customs.envelope_id");
        $this->db->where('envelope_customs.process_flag', APConstants::OFF_FLAG);
        $this->db->where('envelopes.trash_flag IS NULL', null);
        
        $list_customer_id = APUtils::convertIdsInputToString($customer_ids);
        if (empty($list_customer_id)) {
            $list_customer_id = '0';
        }
        $this->db->where('envelope_customs.customer_id IN (' . $list_customer_id .')', null);
        
        $data = $this->db->get()->result();
        
        return $data;
    }
}