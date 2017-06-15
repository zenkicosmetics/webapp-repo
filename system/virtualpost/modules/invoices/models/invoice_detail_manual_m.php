<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author TienNH
 */
class invoice_detail_manual_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('invoice_detail_manual');
        $this->primary_key = 'id';
    }
    
    public function get_manual_credit_note_by($invoice_summary_id, $location_id=''){
        $this->db->select("invoice_detail_manual.*, location.location_name");
        $this->db->join("location", "location.id=invoice_detail_manual.location_id", "left");
        $this->db->where('invoice_summary_id', $invoice_summary_id);
        if(!empty($location_id)){
            $this->db->where('location_id', $location_id);
        }
        
        return  $this->db->get($this->_table)->result();
    }
}