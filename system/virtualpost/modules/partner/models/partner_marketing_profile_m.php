<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Partner_marketing_profile_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('partner_marketing_profile');
        $this->primary_key = 'partner_id';
    }
    
    public function get_partner_marketing_by($customer_id){
        $this->db->select('partner_marketing_profile.*, partner_customers.customer_id, customers.bonus_flag');
        $this->db->join('partner_partner', 'partner_marketing_profile.partner_id = partner_partner.partner_id', "inner");
        $this->db->join('partner_customers', 'partner_customers.partner_id = partner_marketing_profile.partner_id', "inner");

        $this->db->where("partner_customers.customer_id", $customer_id);

        $result = $this->db->get($this->_table)->row();
        return $result;
    }
    
    public function get_list_bonus_customers(){
        $this->db->select('partner_marketing_profile.partner_id,partner_marketing_profile.bonus_location,partner_marketing_profile.bonus_month,partner_marketing_profile.bonus_flag');
        $this->db->select("partner_customers.customer_id, partner_customers.bonus_current_month, partner_customers.bonus_month_total, customers.status");
        $this->db->join('partner_partner', 'partner_marketing_profile.partner_id = partner_partner.partner_id', "inner");
        $this->db->join('partner_customers', 'partner_customers.partner_id = partner_marketing_profile.partner_id', "inner");
        $this->db->join('customers', 'customers.customer_id = partner_customers.customer_id', "inner");

        $this->db->where("partner_marketing_profile.bonus_flag", APConstants::ON_FLAG);
        $this->db->where("customers.account_type", APConstants::NORMAL_CUSTOMER);

        $result = $this->db->get($this->_table)->result();
        return $result;
    }
}