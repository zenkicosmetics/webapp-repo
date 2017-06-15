<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Group model
 */
class Envelope_storage_month_m extends MY_Model {
    function __construct() {
        parent::__construct();
        $this->_table = $this->db->dbprefix('envelope_storage_month');
        $this->primary_key = 'id';
    }
    
    /**
     * count the storage items by month and location.
     */
    public function count_storage_item_of_partner($yearMonth, $partner_id){
        $year = substr($yearMonth, 0, 4);
        $month = substr($yearMonth, 4, 2);
        $this->db->select("count(DISTINCT(envelope_storage_month.envelope_id)) as total");
        $this->db->join("customers", "customers.customer_id=envelope_storage_month.customer_id", "inner");
        $this->db->join('partner_customers', 'partner_customers.customer_id = customers.customer_id', 'inner');
        $this->db->where("envelope_storage_month.year", $year);
        $this->db->where("envelope_storage_month.month", $month);
        $this->db->where("partner_customers.partner_id", $partner_id);
        $this->db->where("partner_customers.end_flag", APConstants::OFF_FLAG);
        $this->db->where('envelope_storage_month.storage_flag', APConstants::ON_FLAG);
        
        $result = $this->db->get($this->_table)->row();
        
        return $result->total;
    }
    
    /**
     * count storage item by month.
     * @param type $year
     * @param type $month
     * @param type $location_id
     * @param type $charge_fee_flag
     * @return type
     */
    public function count_storage_items_by($year, $month, $location_id, $charge_fee_flag = true){
        $this->db->select("count(DISTINCT(envelope_storage_month.envelope_id)) as total");
        $this->db->join("customers", "customers.customer_id=envelope_storage_month.customer_id", "inner");
        $this->db->where("envelope_storage_month.year", $year);
        $this->db->where("envelope_storage_month.month", $month);
        $this->db->where('envelope_storage_month.storage_flag', APConstants::ON_FLAG);
        if(!empty($location_id)){
            $this->db->where("envelope_storage_month.location_id", $location_id);
        }
        if($charge_fee_flag){
            $this->db->where("customers.charge_fee_flag", APConstants::ON_FLAG);
        }
        
        $result = $this->db->get($this->_table)->row();
        
        return $result->total;
    }
}