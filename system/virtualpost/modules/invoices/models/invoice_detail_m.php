<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @author TienNH
 */
class invoice_detail_m extends MY_Model {
    function __construct() {
        parent::__construct();
        
        $this->_table = $this->profile_table = $this->db->dbprefix('invoice_detail');
        $this->primary_key = 'id';
    }
    
    /**
     * Summary envelope amount by month and customer
     *
     * @param unknown_type $customer_id
     * @param unknown_type $year
     * @param unknown_type $month
     */
    public function summary_envelope_bymonth($customer_id, $year, $month) {
        $this->db->select('activity_type, activity, SUM(item_amount) as amount, postbox_type as type, customers.account_type, invoice_detail.invoice_summary_id');
        $this->db->join('envelopes', 'invoice_detail.envelope_id = envelopes.id', 'left');
        $this->db->join('postbox', 'envelopes.postbox_id = postbox.postbox_id', 'left');
        $this->db->join('customers', 'customers.customer_id =  invoice_detail.customer_id', 'left');
    
        $this->db->from('invoice_detail');
    
        $this->db->where('invoice_detail.customer_id', $customer_id);
        $this->db->where('substring(activity_date,1,6)', $year.$month);

        $this->db->group_by('invoice_detail.postbox_type');
        $this->db->group_by('invoice_detail.activity_type');
        $this->db->group_by('invoice_detail.activity');
        $this->db->group_by('invoice_detail.invoice_summary_id');
        $this->db->group_by('customers.account_type');
        $this->db->order_by("invoice_detail.invoice_summary_id");
    
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
    public function summary_envelope_bymonth_location($customer_id, $location_id, $year, $month) {
    	$this->db->select('activity_type, invoice_detail.location_id, activity, SUM(item_amount) as amount, postbox_type as type, customers.account_type, invoice_detail.invoice_summary_id');
    	$this->db->join('envelopes', 'invoice_detail.envelope_id = envelopes.id', 'left');
    	$this->db->join('postbox', 'envelopes.postbox_id = postbox.postbox_id', 'left');
    	$this->db->join('customers', 'customers.customer_id =  invoice_detail.customer_id', 'left');
    
    	$this->db->from('invoice_detail');
    
    	$this->db->where('invoice_detail.customer_id', $customer_id);
    	$this->db->where('substring(activity_date,1,6)', $year.$month);
    	$this->db->where('invoice_detail.location_id', $location_id);
    
    	$this->db->group_by('invoice_detail.postbox_type');
    	$this->db->group_by('invoice_detail.activity_type');
    	$this->db->group_by('invoice_detail.activity');
    	$this->db->group_by('invoice_detail.invoice_summary_id');
    	$this->db->group_by('invoice_detail.location_id');
    	$this->db->group_by('customers.account_type');
    	$this->db->order_by("invoice_detail.invoice_summary_id");
    
    	$results = $this->db->get()->result();
    	return $results;
    }
    
    /**
     * Get all paging data
     *
     * @param unknown_type $array_where
     *            The array of condition (array ('name' => 'DungNT', 'age' =>
     *            30))
     * @param unknown_type $start
     *            The offset paging
     * @param unknown_type $limit
     *            The number of record per page
     * @param unknown_type $sort_column
     *            The sort column
     * @param unknown_type $sort_type
     *            The sort type
     * @return The array object array('total' => '9999', 'data' => '');
     */
    public function get_invoice_detail_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC', $group_by='') {
    	// Count all record with input condition
    	$total_record = $this->count_by_many($array_where);
    	if ($total_record == 0) {
    		return array (
    				"total" => 0,
    				"data" => array ()
    		);
    	}
    
    	$this->db->select('invoice_detail.*, invoice_summary.vat, location.location_name, customers.customer_code, envelopes.envelope_code');
    	$this->db->join('invoice_summary', 'invoice_summary.id = invoice_detail.invoice_summary_id', 'inner');
    	$this->db->join('location', 'location.id = invoice_detail.location_id', 'left');
        $this->db->join('customers', 'customers.customer_id = invoice_detail.customer_id', 'left');
        $this->db->join('envelopes', 'envelopes.id = invoice_detail.envelope_id', 'left');
    	
    	// Search all data with input condition
    	foreach ( $array_where as $key => $value ) {
    		$this->db->where($key, $value);
    	}
    	$this->db->limit($limit, $start);
    	if (! empty($sort_column)) {
    		$this->db->order_by($sort_column, $sort_type);
    	}
    	if(!empty($group_by)){
    		$this->db->group_by($group_by);
    	}
    	$data = $this->db->get($this->_table, $limit, $start)->result();
    
    	return array (
    			"total" => $total_record,
    			"data" => $data
    	);
    }
    
    /**
     * Summary envelope amount by month and customer
     *
     * @param unknown_type $customer_id
     * @param unknown_type $year
     * @param unknown_type $month
     */
    public function count_envelope_bymonth($customer_id, $year, $month) {
        $this->db->select('activity_type,activity, SUM(item_number) as quantity, avg(unit_price) as price, max(unit_price) as max_price, min(unit_price) as min_price, postbox_type as type, customers.account_type, invoice_detail.invoice_summary_id');
        $this->db->join('envelopes', 'invoice_detail.envelope_id = envelopes.id', 'left');
        $this->db->join('postbox', 'envelopes.postbox_id = postbox.postbox_id', 'left');
        $this->db->join('customers', 'customers.customer_id =  invoice_detail.customer_id', 'left');
    
        $this->db->from('invoice_detail');
    
        $this->db->where('invoice_detail.customer_id', $customer_id);
        $this->db->where('substring(activity_date,1,6)', $year.$month);
        $this->db->where('invoice_detail.unit_price > 0', null);
    
        $this->db->group_by('invoice_detail.postbox_type');
        $this->db->group_by('invoice_detail.activity_type');
        $this->db->group_by('invoice_detail.activity');
        $this->db->group_by('invoice_detail.invoice_summary_id');
        $this->db->group_by('customers.account_type');
        
        $this->db->order_by("invoice_detail.invoice_summary_id");
    
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
    public function count_envelope_bymonth_location($customer_id, $location_id, $year, $month) {
    	$this->db->select('activity_type,activity, invoice_detail.location_id, SUM(item_number) as quantity, avg(unit_price) as price, max(unit_price) as max_price, min(unit_price) as min_price, postbox_type as type, customers.account_type, invoice_detail.invoice_summary_id');
    	$this->db->join('envelopes', 'invoice_detail.envelope_id = envelopes.id', 'left');
    	$this->db->join('postbox', 'envelopes.postbox_id = postbox.postbox_id', 'left');
    	$this->db->join('customers', 'customers.customer_id =  invoice_detail.customer_id', 'left');
    
    	$this->db->from('invoice_detail');
    
    	$this->db->where('invoice_detail.customer_id', $customer_id);
    	$this->db->where('substring(activity_date,1,6)', $year.$month);
    	$this->db->where('invoice_detail.location_id', $location_id);
    	$this->db->where('invoice_detail.unit_price > 0', null);
    
    	$this->db->group_by('invoice_detail.postbox_type');
    	$this->db->group_by('invoice_detail.activity_type');
    	$this->db->group_by('invoice_detail.activity');
    	$this->db->group_by('invoice_detail.location_id');
    	$this->db->group_by('invoice_detail.invoice_summary_id');
    	$this->db->group_by('customers.account_type');
    	
    	$this->db->order_by("invoice_detail.invoice_summary_id");
    
    	$results = $this->db->get()->result();
    	return $results;
    }
    
    
    /**
     * Insert a new record into the database, calling the before and after
     * create callbacks.
     *
     * @author Jamie Rumbelow
     * @author Dan Horrigan
     * @param array $data
     *            Information
     * @param boolean $skip_validation
     *            Whether we should skip the validation of the data.
     * @return integer true insert ID
     */
    public function insert($data, $skip_validation = FALSE) {
        // Insert location id, using for partner reporting
    	if ($data && empty($data['location_id']) && !empty($data['envelope_id'])) {
    	    $data['location_id'] = APUtils::getLocationIdByEnvelope($data['envelope_id']);
    	}
        
        return parent::insert($data, $skip_validation);
    }
    
    /**
     * Get total page number already charged
     * @param unknown_type $customer_id
     * @param unknown_type $year_month
     * @param unknown_type $type
     * @return multitype:number multitype: |multitype:unknown
     */
    public function get_total_page_number_charged($customer_id, $year_month, $activity_type = '') {
    	$query = ' SELECT SUM(invoice_detail.item_number) AS total_page_number';
    	$query = $query." FROM invoice_detail ";
    	$query = $query." WHERE substr(activity_date, 1, 6) = '".$year_month."' ";
    	$query = $query." AND customer_id = '".$customer_id."' ";
    	 
    	if (!empty($activity_type)) {
    		$query = $query." AND activity_type = '".$activity_type."' ";
    	}
    
    	$result = $this->db->query($query)->row();
    	return $result->total_page_number;
    }

	public function getTotalPagesChargedOfCurrentMonth($customerID, $activityType = 0)
	{
		$yearMonth = date('Ym');

		$sql = <<<SQL
SELECT
		SUM(item_number) AS total_pages
FROM
		invoice_detail
WHERE
		SUBSTR(activity_date, 1, 6) = '{$yearMonth}'
	AND customer_id = {$customerID}
SQL;
		if ($activityType) {
			$sql .= PHP_EOL . "	AND activity_type = {$activityType}";
		}
		$row = $this->db->query($sql)->row();

		return $row->total_pages;
	}
    
    /**
     * get paging of users of customer.
     * @param type $array_where
     * @param type $start
     * @param type $limit
     * @param type $sort_column
     * @param type $sort_type
     * @param type $group_by
     */
    public function get_account_paging($yearmonth, $list_id, $enquiry, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC', $group_by=''){
        $enquiry_condition = "";
        if(!empty($enquiry)){
            $enquiry_condition = " AND (c.user_name like '%".$enquiry."%' OR c.email like '%".$enquiry."%' ) ";
        }
        // build SQL statement.
        $stmt = "(
                    SELECT 
                        c.customer_id,
                        c.parent_customer_id,
                        c.user_name,
                        c.email,
                        i.activity,
                        i.activity_date,
                        i.item_amount,
                        i.location_id
                    FROM invoice_detail i
                    LEFT JOIN customers c ON i.customer_id = c.customer_id
                    WHERE LEFT(i.activity_date, 6) = '".$yearmonth."'
                    AND c.customer_id IN (".$list_id." )
                    ".$enquiry_condition." 
                ) UNION ALL
                (
                    SELECT 
                        c.customer_id,
                        c.parent_customer_id,
                        c.user_name,
                        c.email,
                        i.activity,
                        i.activity_date,
                        i.item_amount,
                        i.location_id
                    FROM phone_invoice_detail i
                    LEFT JOIN customers c ON i.customer_id = c.customer_id
                    WHERE FROM_UNIXTIME(i.activity_date, '%Y%m') = '".$yearmonth."'
                    AND c.customer_id IN (".$list_id.")
                    ".$enquiry_condition." 
                ) ";
        
        // count all result
        $total = $this->db->query("SELECT COUNT(*) as total FROM (".$stmt.") TMP")->row();
        if($total->total == 0){
            return array (
                    "total" => 0,
                    "data" => array ()
            );
        }
        
        $limit_condition = " LIMIT ".$start.", ".$limit;
        $result = $this->db->query($stmt. $limit_condition)->result();

        return array (
                "total" => $total->total,
                "data" => $result
        );
    }
}