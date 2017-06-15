<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Group model
 */
class Envelope_shipping_m extends MY_Model {
    function __construct() {
        parent::__construct();
        $this->_table = $this->db->dbprefix('envelope_shipping');
        $this->primary_key = 'id';
    }
    
    /**
     * Summary by price report by location.
     *
     * @param unknown_type $location_id
     */
    public function summary_by_location($location_id, $month = '', $share_rev_flag = false) {
        $location_condition = '';
        if($location_id){
            $location_condition = " AND postbox.location_available_id = '" . $location_id . "' ";
        }
        
    	// Select statment
    	$stmt = "SELECT ";
    
    	$stmt = $stmt." SUM(forwarding_charges_postal) as forwarding_charges_postal, SUM(forwarding_charges_fee) as forwarding_charges_fee";

    	// FROM statement
    	$stmt = $stmt." FROM envelope_shipping INNER JOIN postbox ON envelope_shipping.postbox_id = postbox.postbox_id";
    	$stmt = $stmt." INNER JOIN customers ON envelope_shipping.customer_id = customers.customer_id";
    
    	// Where statement
        $stmt = $stmt." WHERE (1=1) " . $location_condition;
        if($share_rev_flag){
            $stmt = $stmt." AND customers.charge_fee_flag=1 ";
        }
    
    	if (!empty($month)) {
    		$stmt = $stmt." AND FROM_UNIXTIME(shipping_date, '%Y%m')  = '{$month}'";
    	}
    
    	$row = $this->db->query($stmt)->row();
    	return $row;
    }
    
    /**
     * summary by location and customer
     * @param type $location_id
     * @param type $month
     * @param type $share_rev_flag
     * @return type
     */
    public function summary_shipping_fee_of_customer($customer_id, $year_month, $by_location = false) {
    	// Select statment
    	$stmt = "SELECT ";
    
    	$stmt = $stmt." SUM(forwarding_charges_postal) as forwarding_charges_postal, SUM(forwarding_charges_fee) as forwarding_charges_fee, postbox.location_available_id";

    	// FROM statement
    	$stmt = $stmt." FROM envelope_shipping INNER JOIN postbox ON envelope_shipping.postbox_id = postbox.postbox_id";
    
    	// Where statement
        $stmt = $stmt." WHERE envelope_shipping.customer_id=" . $customer_id;

    	if (!empty($year_month)) {
    		$stmt = $stmt." AND FROM_UNIXTIME(shipping_date, '%Y%m')  = '{$year_month}'";
    	}
        
        if($by_location){
            $stmt = $stmt." GROUP BY postbox.location_available_id ";
        }
        
    	$row = $this->db->query($stmt)->result();
    	return $row;
    }
    
    /**
     * summary by location and customer
     * @param type $location_id
     * @param type $month
     * @param type $share_rev_flag
     * @return type
     */
    public function summary_all_shipping_fees($year_month, $by_location = false) {
    	// Select statment
    	$stmt = "SELECT ";
    
    	$stmt = $stmt." SUM(forwarding_charges_postal) as forwarding_charges_postal, SUM(forwarding_charges_fee) as forwarding_charges_fee, envelope_shipping.customer_id, postbox.location_available_id";

    	// FROM statement
    	$stmt = $stmt." FROM envelope_shipping INNER JOIN postbox ON envelope_shipping.postbox_id = postbox.postbox_id";
    
    	// Where statement
        $stmt = $stmt." WHERE 1 = 1 ";

    	if (!empty($year_month)) {
    		$stmt = $stmt." AND FROM_UNIXTIME(shipping_date, '%Y%m')  = '{$year_month}'";
    	}
        
        $stmt = $stmt." GROUP BY envelope_shipping.customer_id ";
        if($by_location){
            $stmt = $stmt." ,postbox.location_available_id ";
        }
        
        
    	$row = $this->db->query($stmt)->result();
    	return $row;
    }
    
    /**
     * Summary by price report by location.
     *
     * @param unknown_type $location_id
     */
    public function summary_by_partner($yearMonth, $partner_id) {
        // Select statment
        $stmt = "
                SELECT 
                    SUM(forwarding_charges_postal) as forwarding_charges_postal
                    , SUM(forwarding_charges_fee) as forwarding_charges_fee
                    , SUM( forwarding_charges_fee * partner_customers.customer_discount/100 ) as  forwarding_charges_fee_discount_total
                    , SUM( forwarding_charges_postal * partner_customers.customer_discount/100 ) as  forwarding_charges_postal_rev_discount_total
                    , SUM( forwarding_charges_fee * partner_customers.rev_share_ad/100 ) as  forwarding_charges_fee_share_total
                    , SUM( forwarding_charges_postal * partner_customers.rev_share_ad/100 ) as  forwarding_charges_postal_rev_share_total
                FROM envelope_shipping
                INNER JOIN customers ON customers.customer_id = envelope_shipping.customer_id
                INNER JOIN partner_customers ON partner_customers.customer_id = customers.customer_id
                WHERE partner_customers.partner_id = '".$partner_id."'
                    AND partner_customers.end_flag = 0
                    AND FROM_UNIXTIME(shipping_date, '%Y%m') = '".$yearMonth."'";

        $row = $this->db->query($stmt)->row();
        return $row;
    }
}