<?php

defined('BASEPATH') or exit('No direct script access allowed');

class vatcase_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('vat_case');
        $this->primary_key = 'vat_id';
    }
    
    
    public function get_vat_case_by($product_type = APConstants::VAT_PRODUCT_LOCAL_SERVICE, $customer_type= APConstants::CUSTOMER_TYPE_PRIVATE){
        $this->db->select("vat_case.*, country.country_name");
        $this->db->join("country", "country.id = vat_case.baseon_country_id", 'left');
        $this->db->where("vat_case.product_type", $product_type);
        $this->db->where("vat_case.customer_type", $customer_type);

        $this->db->order_by("vat_case.vat_case_id");
        
        return parent::get_all();
    }
}