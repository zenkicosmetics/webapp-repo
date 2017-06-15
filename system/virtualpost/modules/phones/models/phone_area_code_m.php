<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author DungNT
 */
class phone_area_code_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('phone_area_code');
        $this->primary_key = 'id';
    }
    
    public function get_phone_area_id($country_code_3, $area_code) {
        $this->db->select('phone_area_code.id');
        $this->db->join('country', 'phone_area_code.country_id = country.id', "inner");
        
        $this->db->where('country.country_code_3', $country_code_3);
        $this->db->where('phone_area_code.area_code', $area_code);
        
        $obj = $this->db->get($this->_table)->row();
        return empty($obj) ? '' : $obj->id;
    }
}