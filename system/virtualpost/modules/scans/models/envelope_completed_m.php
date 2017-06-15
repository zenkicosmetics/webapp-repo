<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Group model
 */
class Envelope_completed_m extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->_table = $this->db->dbprefix('envelopes_completed');
        $this->primary_key = 'id';
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
    public function get_envelope_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC') {
        // Count all record with input condition
        $total_record = $this->count_by_envelope_paging($array_where);

        // Check total record
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        // select
        $this->db->select('envelopes_completed.*, customers.user_name as to_customer_name,'
                . ' users.username as admin_name, ca.invoicing_address_name, p.name, p.company as postbox_company_name,'
                . ' ca.invoicing_company, ca.shipment_address_name, ca.shipment_company')->distinct();

        // Join table
        $this->db->join('customers', 'envelopes_completed.to_customer_id = customers.customer_id', 'inner');
        $this->db->join('users', 'envelopes_completed.completed_by = users.id', 'left');
        $this->db->join('postbox p', 'envelopes_completed.postbox_id = p.postbox_id', 'inner');
        $this->db->join('customers_address ca', 'envelopes_completed.to_customer_id = ca.customer_id', 'left');

        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            if ($value != '') {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }

        // Limit
        if ($limit > 0) {
            $this->db->limit($limit);
        }

        // Order by column
        if (!empty($sort_column)) {
            $arr_sort_column = explode(",", $sort_column);
            if (count($arr_sort_column) > 0) {
                foreach ($arr_sort_column as $sort) {
                    if ($sort == 'incomming_date') {
                        $this->db->order_by("DAY(FROM_UNIXTIME(envelopes_completed.incomming_date))", "ASC");
                    } else {
                        $this->db->order_by($sort, $sort_type);
                    }
                }
            } else {
                if ($sort_column == 'incomming_date') {
                    $this->db->order_by("DAY(FROM_UNIXTIME(envelopes_completed.incomming_date))", "ASC");
                } else {
                    $this->db->order_by($sort_column, $sort_type);
                }
            }
        }

        // Get data
        if ($limit > 0) {
            $data = $this->db->get($this->_table, $limit, $start)->result();
        } else {
            $data = $this->db->get($this->_table)->result();
        }

        // return 
        return array(
            "total" => $total_record,
            "data" => $data
        );
    }

    public function update_envelope_completed($data = null) {

        if (!isset($data['id']))
            return 0;
        $this->db->where(array('envelope_id' => $data['id']));
        unset($data['id']);
        return ($this->db->update('envelopes_completed', $data));
        exit;
    }

    public function get_item($data) {
        $this->db->select("envelopes_completed.envelope_id,weight,from_customer_name,ep.width,ep.height,ep.length");
        $this->db->from("envelopes_completed");
        $this->db->join('envelope_properties ep', 'ep.envelope_id = envelopes_completed.envelope_id', 'left');
        $this->db->where(array('envelopes_completed.envelope_id' => $data['envelope_id'], 'completed_flag' => $data['completed_flag']));
        $rs = $this->db->get()->row();

        return $rs;
    }

    /**
     * Count customer
     * @param unknown_type $array_where
     */
    public function count_by_envelope_paging($array_where) {
        // select 
        $this->db->select('envelopes_completed.id')->distinct();

        // join table
        $this->db->join('customers', 'envelopes_completed.to_customer_id = customers.customer_id', 'inner');
        $this->db->join('users', 'envelopes_completed.completed_by = users.id', 'left');
        $this->db->join('postbox p', 'envelopes_completed.postbox_id = p.postbox_id', 'inner');
        $this->db->join('customers_address ca', 'envelopes_completed.to_customer_id = ca.customer_id', 'left');

        // Condition sql
        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }

        //get num row
        $result = $this->db->get($this->_table)->num_rows();

        //return num row
        return $result;
    }

    /**
     * Insert a new record into the database, calling the before and after create callbacks.
     *
     * @author Jamie Rumbelow
     * @author Dan Horrigan
     * @param array $data
     *            Information
     * @param boolean $skip_validation
     *            Whether we should skip the validation of the data.
     * @return integer true insert ID
     */
    public function insert($data) {
        // Get location
        $envelope_id = $data['envelope_id'];
        $envelope = $this->db->where('id', $data ['envelope_id'])->get('envelopes')->row();
        $activity_code = $envelope->envelope_code . '_' . sprintf('%1$02d', $data['activity_id']);
        $data['activity_code'] = $activity_code;
        return parent::insert($data);
    }

    public function get_envelope_complete_by_code($envelope_code) {

        $this->db->select('envelopes_completed.*, customers.user_name,'
                . ' users.username as admin_name, ca.invoicing_address_name,'
                . ' ca.invoicing_company, ca.shipment_address_name, ca.shipment_company')->distinct();
        $this->db->join('customers', 'customers.customer_id = envelopes_completed.to_customer_id');
        $this->db->join('users', 'users.id = envelopes_completed.completed_by', 'left');
        $this->db->join('postbox p', 'p.customer_id = customers.customer_id', 'left');
        $this->db->join('customers_address ca', 'ca.customer_id = customers.customer_id', 'left');
        //$this->db->like("LEFT('activity_code', 26) = '".$envelope_code."'", NULL);
        $this->db->like("activity_code", $envelope_code);
        $this->db->order_by('envelopes_completed.completed_date', 'DESC');

        $data = parent::get_all();
        return $data ? $data[0] : "";
    }

    public function get_info_item($envelope_id) {

        $this->db->select('envelope_shipping.shipping_date, users.username as admin_name, users.id, envelopes_completed.completed_by, ca.shipment_address_name,envelopes_completed.activity_id');

        $this->db->from("envelopes");

        $this->db->join('envelope_shipping', 'envelopes.id = envelope_shipping.envelope_id', 'inner');
        $this->db->join('envelopes_completed', 'envelopes_completed.envelope_id = envelopes.id', 'inner');
        $this->db->join('users', 'users.id = envelopes_completed.completed_by', 'inner');
        $this->db->join('customers_address ca', 'ca.customer_id = envelopes.to_customer_id', 'inner');
        $this->db->where('envelopes.id', $envelope_id);
        $this->db->where_in('envelopes_completed.activity_id', array(APConstants::COLLECT_FORWARDING_COMPLETED_ACTIVITY_TYPE, APConstants::DIRECT_FORWARDING_COMPLETED_ACTIVITY_TYPE));

        return $this->db->get()->row();
    }

    /*
     *  #1318 add a filter to the completed list (according to the ''completed dates'')
     *  Function get_activity_id
     * 
     *  return array 
     */

    public function get_activity_id() {
        $this->db->select('envelopes_completed.activity_id')->distinct();
        $this->db->order_by('activity_id');

        // get activity_id
        $rs = $this->db->get($this->_table)->result();

        // return array activity_id
        return $rs;
    }
 
    /*
     *  Insert activity to envelope_complete
     */
    public function insert_activity_history($ids, $activity_id, $completed_by_type, $completed_by) {
        
        $ids_string = APUtils::convertIdsInputToString($ids);
        
        $activity_code = '_' . sprintf('%1$02d', $activity_id);   
        
        //query string 
        $sql = "INSERT INTO envelopes_completed 
                (envelope_id, from_customer_name, to_customer_id, postbox_id, envelope_type_id, weight, weight_unit, last_updated_date, incomming_date,category_type, invoice_flag,
                shipping_type, include_estamp_flag, sync_cloud_flag, envelope_scan_flag, item_scan_flag, direct_shipping_flag, collect_shipping_flag, trash_flag, storage_flag,
                email_notification_flag, location_id, activity_code, completed_by, completed_date, activity_id, completed_flag, created_by_type, created_by_id)
                 
                (SELECT id, from_customer_name, to_customer_id, postbox_id, envelope_type_id, weight, weight_unit, last_updated_date, incomming_date, category_type, invoice_flag, 
                shipping_type, include_estamp_flag, sync_cloud_flag, envelope_scan_flag, item_scan_flag, direct_shipping_flag, collect_shipping_flag, trash_flag, storage_flag,
                email_notification_flag, location_id, CONCAT(envelope_code,'" . $activity_code ."'), " . $completed_by . ", " . now() . ", " . $activity_id . ", " . APConstants::ON_FLAG .
                "," . $completed_by_type . "," . $completed_by . " FROM envelopes WHERE id IN ('" . $ids_string . "'))";

        // Query sql 
        $query = $this->db->query($sql);
        
        // return 1: successfull | 0: fail
        if ($query) {
            return 1;
        }else{
            return 0;
        }
    }

}
