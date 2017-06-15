<?php

defined('BASEPATH') or exit('No direct script access allowed');

class vatcase_standard_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('vat_case_standard');
        $this->primary_key = 'country_id';
    }
    
    public function get_eu_countries(){
        $this->db->select("vat_case_standard.*, country.country_code, country.country_name, country.eu_member_flag");
        $this->db->join("country", "country.id=vat_case_standard.country_id", "inner");
        $this->db->where("country.eu_member_flag", APConstants::ON_FLAG);
        
        return $this->db->get($this->_table)->result();
    }
}