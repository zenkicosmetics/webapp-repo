<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author TienNH
 */
class envelope_customs_detail_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('envelope_customs_detail');
        $this->primary_key = 'id';
    }
    
    public function get_custom_detail_by($custom_id){
        $this->db->select("envelope_customs_detail.*, country.country_name");
        $this->db->join("country", "envelope_customs_detail.country_origin=country.id", "left");
        $this->db->where("envelope_customs_detail.customs_id", $custom_id);
        return $this->db->get($this->_table)->result();
    }
}