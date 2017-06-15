<?php defined('BASEPATH') or exit('No direct script access allowed');

class partner_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('partner_partner');
        $this->primary_key = 'partner_id';
    }

    /**
     *
     * @param unknown $array_where
     * @param number $start
     * @param number $limit
     * @param unknown $sort_column
     * @param string $sort_type
     * @return multitype:number multitype: |multitype:unknown multitype:
     */
    public function get_partner_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_by_many($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('partner_partner.*, partner_marketing_profile.duration_rev_share, partner_marketing_profile.customer_discount
                , partner_marketing_profile.registration, partner_marketing_profile.activation, partner_marketing_profile.duration_rev_share
                , partner_marketing_profile.partner_domain, partner_marketing_profile.rev_share_ad
                , partner_marketing_profile.bonus_flag, partner_marketing_profile.bonus_month, partner_marketing_profile.bonus_location
                , partner_marketing_profile.script_widget, partner_marketing_profile.script_landing_page');
        $this->db->select('country.country_name');

        $this->db->join('partner_marketing_profile', 'partner_marketing_profile.partner_id = partner_partner.partner_id', "left");
        $this->db->join('country', 'country.id = partner_partner.invoicing_country', "left");

        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            $this->db->where($key, $value);
        }

        $this->db->limit($limit);
        if (!empty($sort_column)) {
            $this->db->order_by($sort_column, $sort_type);
        }
        $data = $this->db->get($this->_table, $limit, $start)->result();

        return array(
            "total" => $total_record,
            "data" => $data
        );
    }

    /**
     *
     * @param unknown $id
     * @return unknown
     */
    public function get_marketing_profile($partner_id)
    {
        $this->db->select('partner_partner.*, pmp.duration_rev_share, pmp.customer_discount
                , pmp.registration, pmp.activation, pmp.duration_rev_share
                , pmp.partner_domain, pmp.rev_share_ad, pmp.session_catch
                , pmp.script_widget, pmp.script_landing_page, pmp.duration_customer_discount
                , pmp.width, pmp.height, pmp.title
                , pmp.bonus_flag, pmp.bonus_month, pmp.bonus_location
                , cases_service_partner.email, cases_service_partner.main_contact_point, cases_service_partner.phone');
        $this->db->join('partner_marketing_profile as pmp', 'pmp.partner_id = partner_partner.partner_id', "left");
        $this->db->join('cases_service_partner', 'cases_service_partner.partner_id = partner_partner.partner_id', "left");

        $this->db->where("partner_partner.partner_id", $partner_id);

        $data = $this->db->get($this->_table)->result();

        return $data ? $data[0] : '';
    }

    public function get_partner_code_by_customer($customer_id)
    {
        $this->db->select('partner_partner.*');
        $this->db->join('partner_customers', 'partner_customers.partner_id = partner_partner.partner_id', "inner");

        $this->db->where("partner_customers.customer_id", $customer_id);

        $data = $this->db->get($this->_table)->result();

        return $data ? $data[0] : '';
    }

    /**
     *  Get partner name and address
     */
    public function getPartnerNameAndAddress($caseID, $baseTaskname)
    {
        $this->db->select("partner_partner.*,cases_service_partner.clevvermail_flag, cases_milestone.cmra, cases_milestone.partner_id");
        $this->db->from('cases_milestone_instance');
        $this->db->join("cases_milestone", "cases_milestone.id = cases_milestone_instance.milestone_id", 'inner');
        $this->db->join("cases_service_partner", "cases_milestone.cmra = cases_service_partner.partner_id", 'left');
        $this->db->join("partner_partner", "cases_milestone.cmra = partner_partner.partner_id", 'left');
        $this->db->join("cases_taskname_instance", "cases_milestone_instance.id = cases_taskname_instance.milestone_instance_id");
        $this->db->where("cases_taskname_instance.case_id", $caseID);
        $this->db->where("cases_taskname_instance.base_task_name", $baseTaskname);

        $row = $this->db->get()->row();

        return $row;
    }
    
     /**
     *  Get partner by location id
     * 
     * function getPartnerByLocationID
     *  
     * @param type $locationID
     * 
     * Return object's data, if faliure is empty
     */
    public function getPartnerByLocationID($locationID)
    {
        // Select field 
        $this->db->select("location.id, partner_partner.*");
        
        // left join table 
        $this->db->join("location", "partner_partner.partner_id = location.partner_id", "left");
        
        // Condition 
        $this->db->where("location.id", $locationID);

        // return the query result as an array of objects, or an empty array on failure
        $data = $this->db->get($this->_table)->result();
     
        // return object's data
        return $data ? $data : '';
    }
}