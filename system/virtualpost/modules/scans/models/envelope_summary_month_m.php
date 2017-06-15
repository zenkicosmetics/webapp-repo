<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Group model
 */
class Envelope_summary_month_m extends MY_Model {
    function __construct() {
        parent::__construct();
        $this->_table = $this->db->dbprefix('envelope_summary_month');
        $this->primary_key = 'id';
    }
    
    /**
     * Summary envelope amount by month and customer
     * 
     * @param unknown_type $customer_id            
     * @param unknown_type $year            
     * @param unknown_type $month            
     */
    public function summary_envelope_bymonth($customer_id, $year, $month, $additional_incomming_flag = '') {
        $this->db->select('SUM(es.incomming_number * es.incomming_price) as incomming_amount');
        $this->db->select('SUM(es.envelope_scan_number * es.envelope_scan_price) as envelope_scan_amount');
        $this->db->select('SUM(es.document_scan_number * es.document_scan_price) as document_scan_amount');
        $this->db->select('SUM(es.direct_shipping_number * es.direct_shipping_price) as direct_shipping_amount');
        $this->db->select('SUM(es.collect_shipping_number * es.collect_shipping_price) as collect_shipping_amount');
        $this->db->select('SUM(es.additional_pages_scanning_number * es.additional_pages_scanning_price) as additional_pages_scanning');
        $this->db->select('es.customer_id');
        $this->db->select('p.type');
        $this->db->join('postbox p', 'p.postbox_id = es.postbox_id', 'inner');
        
        $this->db->from('envelope_summary_month es');
        
        $this->db->where('es.customer_id', $customer_id);
        $this->db->where('es.year', $year);
        $this->db->where('es.month', $month);
        if (! empty($additional_incomming_flag)) {
            $this->db->where('es.additional_incomming_flag', $additional_incomming_flag);
        }
        $this->db->group_by('es.customer_id');
        $this->db->group_by('p.type');
        
        $results = $this->db->get()->result();
        return $results;
    }
    
    /**
     * Summary envelope amount by month and customer
     *
     * @param unknown_type $customer_id
     * @param unknown_type $year
     * @param unknown_type $month
     */
    public function summary_additional_pages_scanning_bymonth($customer_id, $year, $month, $additional_incomming_flag = '') {
        $this->db->select('SUM(es.additional_pages_scanning_number) as additional_pages_scanning_number');
        $this->db->select('es.additional_pages_scanning_price as additional_pages_scanning_price');
        $this->db->select('es.customer_id');
        
        $this->db->from('envelope_summary_month es');
        
        $this->db->where('es.customer_id', $customer_id);
        $this->db->where('es.year', $year);
        $this->db->where('es.month', $month);
        if (! empty($additional_incomming_flag)) {
            $this->db->where('es.additional_incomming_flag', $additional_incomming_flag);
        }
        $this->db->group_by('es.customer_id');
        
        $results = $this->db->get()->row();
        return $results;
    }
    
    /**
     * Summary envelope amount by month and customer
     *
     * @param unknown_type $customer_id
     * @param unknown_type $year
     * @param unknown_type $month
     */
    public function summary_column_bymonth($customer_id, $year, $month, $column_number_name, $column_price_name, $type = '') {
        $this->db->select('SUM(es.'.$column_number_name.') as quantity');
        $this->db->select('es.'.$column_price_name.' as price');
        $this->db->join('postbox p', 'p.postbox_id = es.postbox_id', 'inner');
    
        $this->db->from('envelope_summary_month es');
    
        $this->db->where('es.customer_id', $customer_id);
        $this->db->where('es.year', $year);
        $this->db->where('es.month', $month);
        $this->db->where('es.'.$column_price_name.' > 0 ');
        
        if (!empty($type)) {
            $this->db->where('p.type', $type);
        }
    
        $results = $this->db->get()->row();
        if (empty($results)) {
            $results = new stdClass();
            $results->quantity = 0;
            $results->price = 0;
            $results->type = '';
        }
        return $results;
    }
    
    /**
     * Summary envelope amount by month and customer
     * 
     * @param unknown_type $customer_id            
     * @param unknown_type $year            
     * @param unknown_type $month            
     */
    public function summary_envelope_item_bymonth($customer_id, $year, $month) {
        $this->db->select('SUM(es.incomming_number) as incomming_number');
        $this->db->select('SUM(es.envelope_scan_number) as envelope_scan_number');
        $this->db->select('SUM(es.document_scan_number) as document_scan_number');
        $this->db->select('SUM(es.direct_shipping_number) as direct_shipping_number');
        $this->db->select('SUM(es.collect_shipping_number) as collect_shipping_number');
        $this->db->select('SUM(es.direct_shipping_number * es.direct_shipping_price) as direct_shipping_amount');
        $this->db->select('SUM(es.collect_shipping_number * es.collect_shipping_price) as collect_shipping_amount');
        
        $this->db->from('envelope_summary_month es');
        
        $this->db->where('es.customer_id', $customer_id);
        $this->db->where('es.year', $year);
        $this->db->where('es.month', $month);
        
        $results = $this->db->get()->result();
        return $results [0];
    }
    
    /**
     * Summary envelope amount by month and customer
     *
     * @param unknown_type $customer_id
     * @param unknown_type $year
     * @param unknown_type $month
     */
    public function summary_envelope($customer_id) {
        $this->db->select('SUM(es.incomming_number) as incomming_number');
        $this->db->select('SUM(es.envelope_scan_number) as envelope_scan_number');
        $this->db->select('SUM(es.document_scan_number) as document_scan_number');
        $this->db->select('SUM(es.direct_shipping_number) as direct_shipping_number');
        $this->db->select('SUM(es.collect_shipping_number) as collect_shipping_number');
        $this->db->select('SUM(es.direct_shipping_number * es.direct_shipping_price) as direct_shipping_amount');
        $this->db->select('SUM(es.collect_shipping_number * es.collect_shipping_price) as collect_shipping_amount');
    
        $this->db->from('envelope_summary_month es');
    
        $this->db->where('es.customer_id', $customer_id);
    
        $results = $this->db->get()->result();
        return $results [0];
    }
}