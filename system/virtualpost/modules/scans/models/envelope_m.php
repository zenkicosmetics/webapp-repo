<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Group model
 */
class Envelope_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->profile_table = $this->db->dbprefix('envelopes');
        $this->primary_key = 'id';
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
    public function insert($data)
    {
        $envelope_id = parent::insert($data);

        $to_customer_id = $data['to_customer_id'];
        $postbox_id = $data['postbox_id'];

        return $envelope_id;
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
    public function get_envelope_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_envelope($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('envelopes.*, customers.user_name as to_customer_name, users.username as admin_name, customers.activated_flag, customers.status, envelope_comment.text as comment');
        $this->db->join('customers', 'customers.customer_id = envelopes.to_customer_id');
        $this->db->select('customers.email_confirm_flag, customers.deactivated_type');
        $this->db->join('users', 'users.id = envelopes.completed_by', 'left');
        $this->db->join("envelope_comment", "envelope_comment.customer_id = customers.customer_id and envelope_comment.envelope_id = envelopes.id", "left");
        $this->db->join('postbox', 'postbox.postbox_id = envelopes.postbox_id', 'inner');

        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            if ($value != '') {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        // $this->db->where("postbox.completed_delete_flag <> 1", null);
        $this->db->limit($limit);

        if (!empty($sort_column)) {
            $arr_sort_column = explode(",", $sort_column);
            if (count($arr_sort_column) > 0) {
                foreach ($arr_sort_column as $sort) {
                    if ($sort == 'incomming_date') {
                        $this->db->order_by("DAY(FROM_UNIXTIME(envelopes.incomming_date))", "ASC");
                    } else {
                        $this->db->order_by($sort, $sort_type);
                    }
                }
            } else {
                if ($sort_column == 'incomming_date') {
                    $this->db->order_by("DAY(FROM_UNIXTIME(envelopes.incomming_date))", "ASC");
                } else {
                    $this->db->order_by($sort_column, $sort_type);
                }
            }
        }
        $data = $this->db->get($this->_table, $limit, $start)->result();

        return array(
            "total" => $total_record,
            "data" => $data
        );
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
    public function get_envelope_paging_mailbox($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_envelope($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('envelopes.*, customers.user_name as to_customer_name, users.username as admin_name, customers.activated_flag, customers.status, envelope_comment.text as comment');
        $this->db->select("envelope_shipping_tracking.tracking_number, shipping_services.name as shipping_service_name");
        $this->db->select("sc.tracking_number_url");
        $this->db->join('customers', 'customers.customer_id = envelopes.to_customer_id');
        $this->db->join('users', 'users.id = envelopes.completed_by', 'left');
        $this->db->join('postbox', 'postbox.postbox_id = envelopes.postbox_id', 'inner');
        $this->db->join("envelope_comment", "envelope_comment.customer_id=customers.customer_id and envelope_comment.envelope_id=envelopes.id", "left");
        $this->db->join("envelope_shipping_tracking", "envelope_shipping_tracking.envelope_id=envelopes.id", "left");
        $this->db->join('shipping_services', 'shipping_services.id = envelope_shipping_tracking.shipping_services_id', 'left');
        $this->db->join('shipping_carriers sc', 'shipping_services.carrier_id = sc.id', 'left');
        $this->db->where("postbox.completed_delete_flag <> 1", null);

        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            if ($value != '') {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }

        if (!empty($sort_column)) {
            $arr_sort_column = explode(",", $sort_column);
            if (count($arr_sort_column) > 0) {
                foreach ($arr_sort_column as $sort) {
                    $this->db->order_by($sort, $sort_type);
                }
            }
        }
        $data = $this->db->get($this->_table, $limit, $start)->result();

        return array(
            "total" => $total_record,
            "data" => $data
        );
    }

    public function count_envelope($array_where)
    {
        $this->db->select('COUNT(envelopes.id) AS TotalRecord');
        $this->db->join('customers', 'customers.customer_id = envelopes.to_customer_id');
        $this->db->join('users', 'users.id = envelopes.completed_by', 'left');
        $this->db->join('postbox', 'postbox.postbox_id = envelopes.postbox_id', 'inner');
        $this->db->join("envelope_comment", "envelope_comment.customer_id=customers.customer_id and envelope_comment.envelope_id=envelopes.id", "left");
        $this->db->join("envelope_shipping_tracking", "envelope_shipping_tracking.envelope_id=envelopes.id", "left");
        $this->db->join('shipping_services', 'shipping_services.id = envelope_shipping_tracking.shipping_services_id', 'left');

        // $this->db->where("postbox.completed_delete_flag <> 1", null);

        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            if ($value != '') {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $row = $this->db->get($this->_table)->row();
        return $row->TotalRecord;
    }
    
    public function count_by_parent_customer($parent_customer_id) {
        $this->db->select('COUNT(envelopes.id) AS TotalRecord');
        $this->db->join('customers', 'customers.customer_id = envelopes.to_customer_id');

        // Search all data with input condition
        $this->db->where('customers.parent_customer_id', $parent_customer_id);
         
        $row = $this->db->get($this->_table)->row();
        return $row->TotalRecord;
    }

    /**
     * Get danh sach customer can gui email notify khi co incomming request.
     */
    public function get_customer_notify()
    {
        $this->db->select('customers.customer_id, customers.email, customers.user_name')->distinct();
        $this->db->from('customers');
        $this->db->join('envelopes', 'envelopes.to_customer_id = customers.customer_id');
        $this->db->where('envelopes.email_notification_flag', '0');
        return $this->db->get()->result();
    }

    /**
     * Get danh sach cac items da het han (dua theo comming date).
     */
    public function update_storage_flag_envelope($customer_id, $postbox_id, $baseline_date)
    {
        $this->update_by_many(array(
            "to_customer_id" => $customer_id,
            "postbox_id" => $postbox_id,
            "storage_flag IS NULL" => null,
            "incomming_date < " => $baseline_date,
            "trash_flag IS NULL" => null
        ), array(
            "storage_flag" => APConstants::ON_FLAG,
            "storage_date" => now()
        ));
    }

    /**
     * Letter
     * Get danh sach cac items da het han (dua theo comming date).
     */
    public function get_store_expired_envelope($customer_id, $postbox_id, $baseline_date, $envelope_id)
    {
        $this->db->select('(SUM(' . $baseline_date . ' - envelopes.incomming_date)) / (60 * 60 * 24) as number_over_day ');
        $this->db->from('envelopes');
        $this->db->where('envelopes.to_customer_id', $customer_id);
        $this->db->where('envelopes.postbox_id', $postbox_id);
        $this->db->where('envelopes.id', $envelope_id);
        $this->db->where('envelopes.incomming_date < ', $baseline_date);
        $this->db->where("envelopes.envelope_type_id IN (SELECT LabelValue FROM Settings WHERE SettingCode='000025' AND Alias02='Letter')", NULL);

        return $this->db->get()->result();
    }

    /**
     * Get danh sach cac items da het han (dua theo comming date).
     */
    public function get_store_expired_envelope_bytype($customer_id, $postbox_id, $baseline_date)
    {
        $this->db->select('SUM(' . $baseline_date . ' - envelopes.incomming_date) / (60 * 60 * 24) as number_over_day ');
        $this->db->select('id');

        $this->db->from('envelopes');
        $this->db->where('envelopes.to_customer_id', $customer_id);
        $this->db->where('envelopes.postbox_id', $postbox_id);
        $this->db->where('envelopes.incomming_date < ', $baseline_date);
        $this->db->where("envelope_type_id IN (SELECT LabelValue FROM Settings WHERE SettingCode='000025' AND Alias02='Package')", NULL);

        return $this->db->get()->result();
    }

    /**
     * Get danh sach cac items da het han (dua theo comming date).
     */
    public function get_store_expired_envelope_package($customer_id, $postbox_id, $baseline_date, $envelope_id)
    {
        $this->db->select('(SUM(' . $baseline_date . ' - envelopes.incomming_date)) / (60 * 60 * 24) as number_over_day ');
        $this->db->from('envelopes');
        $this->db->where('envelopes.to_customer_id', $customer_id);
        $this->db->where('envelopes.postbox_id', $postbox_id);
        $this->db->where('envelopes.item_scan_date < ', $baseline_date);
        $this->db->where('envelopes.id', $envelope_id);
        $this->db->where("envelopes.envelope_type_id IN (SELECT LabelValue FROM Settings WHERE SettingCode='000025' AND Alias02='Package')", NULL);

        return $this->db->get()->result();
    }

    /**
     * Get all customer and postbox having request collect
     */
    public function get_postbox_collect()
    {
        $this->db->select('customers.customer_id, postbox.location_available_id')->distinct();
        $this->db->from('customers');
        $this->db->join('envelopes', 'envelopes.to_customer_id = customers.customer_id');
        $this->db->join('postbox', 'postbox.postbox_id = envelopes.postbox_id');
        $this->db->where('envelopes.collect_shipping_flag', '0');
        $this->db->where("envelopes.package_id is null OR envelopes.package_id = '0'");
        $this->db->where("(envelopes.item_scan_flag = '1' OR envelopes.item_scan_flag IS NULL)");

        return $this->db->get()->result();
    }

    /**
     * Get all customer and postbox having request collect
     */
    public function get_postbox_collect_bycustomer($customer_id)
    {
        $this->db->select('customers.customer_id, postbox.location_available_id, envelopes.*')->distinct();
        $this->db->from('customers');
        $this->db->join('envelopes', 'envelopes.to_customer_id = customers.customer_id');
        $this->db->join('postbox', 'postbox.postbox_id = envelopes.postbox_id');
        $this->db->where('envelopes.collect_shipping_flag', '0');
        $this->db->where('envelopes.package_id IS NULL OR envelopes.package_id = 0');
        $this->db->where("(envelopes.item_scan_flag = '1' OR envelopes.item_scan_flag IS NULL)");
        $this->db->where('customers.customer_id', $customer_id);

        return $this->db->get()->result();
    }

    /**
     * Get marked collect shipping item in this postbox 
     * @param type $customer_id
     * @param type $postbox_id
     * @return type
     */
    public function get_postbox_collect_by($customer_id, $postbox_id)
    {
        $this->db->select('customers.customer_id, postbox.location_available_id, envelopes.*')->distinct();
        $this->db->from('customers');
        $this->db->join('envelopes', 'envelopes.to_customer_id = customers.customer_id');
        $this->db->join('postbox', 'postbox.postbox_id = envelopes.postbox_id');
        //$this->db->where('( (envelopes.collect_shipping_flag = 0) OR (envelopes.storage_flag = 1 AND envelopes.current_storage_charge_fee_day > 0)) ', null);
        $this->db->where('(envelopes.collect_shipping_flag = 0) ', null);
        $this->db->where('(envelopes.package_id IS NULL OR envelopes.package_id = 0) ', null);
        $this->db->where('customers.customer_id', $customer_id);
        $this->db->where('postbox.postbox_id', $postbox_id);

        return $this->db->get()->result();
    }

    /**
     * Get all customer and postbox having request collect
     */
    public function get_customer_envelope_collect($postbox_id)
    {
        $this->db->select('customers.customer_id, envelopes.postbox_id, envelopes.id')->distinct();
        $this->db->from('customers');
        $this->db->join('envelopes', 'envelopes.to_customer_id = customers.customer_id');
        $this->db->where('envelopes.collect_shipping_flag', '0');
        $this->db->where('(envelopes.package_id is null OR envelopes.package_id = 0)');
        $this->db->where('envelopes.postbox_id', $postbox_id);

        return $this->db->get()->result();
    }

    /**
     * Get all customer and postbox having request collect
     */
    public function get_all_customer_has_fee_storage($customer_id = '')
    {
        $this->db->select('customers.customer_id,customers.account_type, postbox.type, envelopes.postbox_id, envelopes.id, envelopes.envelope_type_id')->distinct();
        $this->db->select('envelopes.incomming_date, envelopes.direct_shipping_date, envelopes.collect_shipping_date, envelopes.trash_date, postbox.location_available_id');
        $this->db->select('envelopes.current_storage_charge_fee_day, envelopes.previous_storage_charge_fee_day, envelopes.envelope_code');
        $this->db->select('envelopes.direct_shipping_flag, envelopes.collect_shipping_flag, envelopes.trash_flag');
        $this->db->select('envelopes.completed_date');

        $this->db->from('customers');
        $this->db->join('envelopes', 'envelopes.to_customer_id = customers.customer_id');
        $this->db->join('postbox', 'envelopes.postbox_id = postbox.postbox_id');

        if (!empty($customer_id)) {
            $this->db->where('customers.customer_id', $customer_id);
        }

        // $this->db->where('envelopes.storage_flag', APConstants::ON_FLAG);
        $this->db->where('postbox.deleted <> ', APConstants::ON_FLAG);
        $this->db->where('envelopes.completed_flag <> ', APConstants::ON_FLAG);
        $this->db->where("(customers.status is null OR customers.status <> '1')", null);

        return $this->db->get()->result();
    }


    /**
     * Tinh toan total invoice 10 phut 1 lan
     */
    public function update_all_customer_not_has_fee_storage()
    {
        $target_month = APUtils::getCurrentYear() . APUtils::getCurrentMonth();
        $sql = "
    	UPDATE invoice_summary
    	INNER JOIN customers on invoice_summary.customer_id=customers.customer_id
    	SET
        	storing_letters_free_account = 0,
            storing_packages_free_account= 0,
            storing_letters_free_quantity= 0,
            storing_letters_free_netprice= 0,
            storing_packages_free_quantity= 0,
            storing_packages_free_netprice= 0,
    
            storing_letters_private_account= 0,
            storing_packages_private_account= 0,
            storing_letters_private_quantity= 0,
            storing_letters_private_netprice= 0,
            storing_packages_private_quantity= 0,
            storing_packages_private_netprice= 0,
    
            storing_letters_business_account= 0,
            storing_packages_business_account= 0,
            storing_letters_business_quantity= 0,
            storing_letters_business_netprice= 0,
            storing_packages_business_quantity= 0,
            storing_packages_business_netprice= 0
    	WHERE 
    	   invoice_summary.customer_id IN (select distinct to_customer_id from envelopes WHERE current_storage_charge_fee_day = 0)
    	   AND (customers.`status` is null OR customers.`status` <> 1)
    	    AND substr( invoice_summary.invoice_month, 1, 6 ) = '{$target_month}'
    	";
        $this->customer_m->db->query($sql);
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
    public function get_envelope_paging_incomming($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_envelope_paging_incomming($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('envelopes.*, customers.email as to_customer_name, users.username as admin_name, customers.activated_flag, customers.status');
        $this->db->join('customers', 'customers.customer_id = envelopes.to_customer_id');
        $this->db->join("postbox as p", "envelopes.postbox_id = p.postbox_id", "inner");
        $this->db->join('users', 'users.id = envelopes.completed_by', 'left');

        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            if ($value != '') {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
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
    
    public function count_envelope_paging_incomming($array_where)
    {
        $this->db->select('COUNT(envelopes.id) AS TotalRecord');
        $this->db->join('customers', 'customers.customer_id = envelopes.to_customer_id');
        $this->db->join("postbox as p", "envelopes.postbox_id = p.postbox_id", "inner");
        $this->db->join('users', 'users.id = envelopes.completed_by', 'left');

        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            if ($value != '') {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $row = $this->db->get($this->_table)->row();

        return $row->TotalRecord;
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
    public function get_envelope_paging_storage_fee($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_envelope_storage_fee($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('envelopes.*, customers.customer_code, customers.customer_id, customers.email, p.type');
        $this->db->join('customers', 'customers.customer_id = envelopes.to_customer_id');
        $this->db->join("postbox as p", "p.postbox_id=envelopes.postbox_id");
        // #1298 replace search for customer id in storage report with full search 
         $this->db->join("customers_address", "customers_address.customer_id = envelopes.to_customer_id");

        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            if ($value != '') {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
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

    public function count_envelope_storage_fee($array_where)
    {
        $this->db->select('COUNT(envelopes.id) AS TotalRecord');
        $this->db->join('customers', 'customers.customer_id = envelopes.to_customer_id');
        $this->db->join("postbox as p", "p.postbox_id=envelopes.postbox_id");
        // #1298 replace search for customer id in storage report with full search 
         $this->db->join("customers_address", "customers_address.customer_id = envelopes.to_customer_id");

        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            if ($value != '') {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $row = $this->db->get($this->_table)->row();

        return $row->TotalRecord;
    }

    public function sum_envelope_storage_fee($array_where, $list_envelope_type)
    {
        $this->db->select('SUM(envelopes.current_storage_charge_fee_day) AS TotalRecord');

        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            if ($value != '') {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $this->db->where_in('envelope_type_id', $list_envelope_type);

        $row = $this->db->get($this->_table)->row();

        return $row->TotalRecord;
    }

    public function sum_previous_storage_charge_fee_day($array_where, $list_envelope_type)
    {
        $this->db->select('');
        $sql = " SELECT ";
        $sql = $sql . " SUM(LEAST(31, envelopes.previous_storage_charge_fee_day)) AS TotalRecord ";
        $sql = $sql . " FROM envelopes ";

        $sql = $sql . " WHERE '1'= '1' ";

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $sql = $sql . " AND " . $key . "='" . $value . "'";
            } else {
                $sql = $sql . " AND " . $key;
            }
        }

        $sql = $sql . " AND envelope_type_id IN ('" . implode("','", $list_envelope_type) . "')";

        $row = $this->db->query($sql)->row();
        return $row->TotalRecord;

    }

    public function get_all_customer_id_has_storage_cost_backdate()
    {
        $this->db->select('envelopes.to_customer_id as customer_id')->distinct();
        $this->db->join('customers', 'envelopes.to_customer_id = customers.customer_id');
        $this->db->where('previous_storage_charge_fee_day > ', '0');
        $this->db->where("(customers.status is null or customers.status <>'1')", null);

        return $this->db->get($this->_table)->result();
    }

    public function get_all_customer_id_has_storage_cost($input_customer_id='')
    {
        $this->db->select('envelopes.to_customer_id as customer_id')->distinct();
        $this->db->join("customers", "envelopes.to_customer_id = customers.customer_id", 'inner');
        $this->db->where("(customers.status <> 1 or customers.status is null)", null);
        
        if(empty($input_customer_id)){
            $this->db->where('current_storage_charge_fee_day > ', '0');
        }
        
        if(!empty($input_customer_id)){
            $this->db->where('envelopes.to_customer_id', $input_customer_id);
        }

        return $this->db->get($this->_table)->result();
    }

    public function sum_envelope_storage_fee_backdate($array_where, $list_envelope_type)
    {
        $sql = " SELECT ";
        $sql = $sql . "  SUM(CASE ";
        $sql = $sql . "  WHEN (previous_storage_charge_fee_day <= 30 AND current_storage_charge_fee_day > 0)  THEN previous_storage_charge_fee_day ";
        $sql = $sql . " WHEN (previous_storage_charge_fee_day <= 30 AND previous_storage_charge_fee_day > 0 AND (";
        $sql = $sql . "	(from_unixtime(collect_shipping_date, '%Y%m%d') >= '20160401'  AND from_unixtime(collect_shipping_date, '%Y%m%d') <= '20160430' ) ";
        $sql = $sql . "	OR (from_unixtime(direct_shipping_date, '%Y%m%d') >= '20160401'  AND from_unixtime(direct_shipping_date, '%Y%m%d') <= '20160430' ) ";
        $sql = $sql . "	OR  (from_unixtime(trash_date, '%Y%m%d') >= '20160401' AND from_unixtime(trash_date, '%Y%m%d') <= '20160430' )";
        $sql = $sql . " ))  THEN ";
        $sql = $sql . "	CASE";
        $sql = $sql . "		WHEN collect_shipping_date > 1459468800 THEN round((collect_shipping_date - 1459468800) / 86400)";
        $sql = $sql . "		WHEN direct_shipping_date > 1459468800 THEN round((direct_shipping_date - 1459468800) / 86400)";
        $sql = $sql . "		WHEN trash_date > 1459468800 THEN round((trash_date - 1459468800) / 86400)";
        $sql = $sql . "		ELSE previous_storage_charge_fee_day";
        $sql = $sql . "	END";
        $sql = $sql . " WHEN (previous_storage_charge_fee_day > 30 AND current_storage_charge_fee_day > 0) THEN 30 ";
        $sql = $sql . " ELSE 0 END";
        $sql = $sql . " ) TotalRecord";
        $sql = $sql . " FROM envelopes ";

        $sql = $sql . " WHERE '1'= '1' ";

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $sql = $sql . " AND " . $key . "='" . $value . "'";
            } else {
                $sql = $sql . " AND " . $key;
            }
        }

        $sql = $sql . " AND envelope_type_id IN ('" . implode("','", $list_envelope_type) . "')";

        $row = $this->db->query($sql)->row();
        return $row->TotalRecord;
    }

    public function get_max_envelope_code($array_where)
    {
        $this->db->select('envelopes.envelope_code AS max_envelope_code');

        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            if ($value != '') {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $this->db->order_by('envelope_code', 'DESC');

        $row = $this->db->get($this->_table)->row();
        if (empty($row) || empty($row->max_envelope_code)) {
            return 0;
        }
        $max_envelope_code = $row->max_envelope_code;

        return intval(substr($max_envelope_code, 23));
    }

    public function get_envelope_by_code($envelope_code)
    {
        $this->db->select('envelopes.*, customers.user_name as to_customer_name, customers.user_name,'
            . "ca.invoicing_address_name,"
            . ' ca.invoicing_company, ca.shipment_address_name, ca.shipment_company')->distinct();
        $this->db->join('customers', 'customers.customer_id = envelopes.to_customer_id');
        //$this->db->join('users', 'users.id = envelopes.completed_by', 'left');
        $this->db->join('postbox p', 'p.customer_id = customers.customer_id', 'left');
        $this->db->join('customers_address ca', 'ca.customer_id = customers.customer_id', 'left');
        $this->db->where("envelopes.envelope_code", $envelope_code);

        $data = parent::get_all();

        return $data ? $data[0] : "";
    }

    public function count_shipping_distinct_by_many($location_id, $report_month)
    {
        // Select statment
        $stmt = "SELECT ";
        $stmt = $stmt . " COUNT(id) as count_val";

        // FROM statement
        $stmt = $stmt . " FROM envelopes";

        // Where statement
        $stmt = $stmt . " WHERE location_id = {$location_id}";
        $stmt = $stmt . " AND (direct_shipping_flag = '1' OR collect_shipping_flag = '1')";
        if (!empty($report_month)) {
            $stmt = $stmt . " AND (from_unixtime(direct_shipping_date, '%Y%m')='" . $report_month . "' OR from_unixtime(collect_shipping_date, '%Y%m')='" . $report_month . "') ";
        }
        $row = $this->db->query($stmt)->row();

        return $row->count_val;
    }

    public function get_number_received_item($customer_id)
    {
        $this->db->join('postbox', 'postbox.postbox_id=envelopes.postbox_id', 'inner');
        $this->db->where("envelopes.to_customer_id", $customer_id);
        $this->db->where("postbox.deleted <> 1", null);
        $this->db->where("postbox.completed_delete_flag <> 1", null);
        $data = parent::get_all();

        return count($data);
    }

    /**
     * Gets enveloping on todo list.
     * @param unknown $array_where
     * @param number $start
     * @param number $limit
     * @param unknown $sort_column
     * @param string $sort_type
     * @return multitype:number multitype: |multitype:unknown
     */
    public function get_envelope_paging_todolist($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // where condition.
        $where_condition = "";
        foreach ($array_where as $key => $value) {
            if ($value == null) {
                $where_condition .= " AND ({$key})";
            } else {
                $where_condition .= " AND ({$key} = '{$value}')";
            }
        }

        // order by
        $order_by_condition = '';
        if (!empty($sort_column)) {
            $arr_sort_column = explode(",", $sort_column);
            if (count($arr_sort_column) > 0) {
                foreach ($arr_sort_column as $sort) {
                    if ($sort == 'incomming_date') {
                        if ($order_by_condition == '') {
                            $order_by_condition .= " DAY(FROM_UNIXTIME(incomming_date)) ASC ";
                        } else {
                            $order_by_condition .= ", DAY(FROM_UNIXTIME(incomming_date)) ASC ";
                        }
                    } else {
                        if ($order_by_condition == '') {
                            $order_by_condition .= " {$sort} {$sort_type} ";
                        } else {
                            $order_by_condition .= ", {$sort} {$sort_type} ";
                        }
                    }
                }
            } else {
                if ($sort_column == 'incomming_date') {
                    if ($order_by_condition == '') {
                        $order_by_condition .= " DAY(FROM_UNIXTIME(incomming_date)) ASC ";
                    } else {
                        $order_by_condition .= ", DAY(FROM_UNIXTIME(incomming_date)) ASC ";
                    }
                } else {
                    if ($order_by_condition == '') {
                        $order_by_condition .= " {$sort_column} {$sort_type}";
                    } else {
                        $order_by_condition .= ", {$sort_column} {$sort_type} ";
                    }
                }
            }
        }

        $sql = "(SELECT distinct
        envelopes.*
        , customers.email as to_customer_name
        , users.username as admin_name
        , customers.activated_flag
        , customers.status
        , customers.required_verification_flag
        , envelope_comment.text as comment
        , customers.email_confirm_flag
        , customers.deactivated_type
        , customers_address.invoice_address_verification_flag
        , postbox.name_verification_flag
        , postbox.company_verification_flag
        , 12 * ( YEAR(now()) - YEAR(FROM_UNIXTIME(customers.created_date) )) + (MONTH(now()) - MONTH (FROM_UNIXTIME(customers.created_date)) ) + 1 as registration_month
        FROM envelopes
        INNER JOIN customers ON customers.customer_id = envelopes.to_customer_id
        LEFT JOIN envelope_comment ON envelope_comment.customer_id=customers.customer_id and envelope_comment.envelope_id=envelopes.id
        INNER JOIN postbox ON postbox.postbox_id = envelopes.postbox_id
        LEFT JOIN customers_address ON customers_address.customer_id = envelopes.to_customer_id
        LEFT JOIN users ON users.id = envelopes.completed_by
        WHERE (
        (   ( (envelopes.direct_shipping_flag <> '1' OR envelopes.direct_shipping_flag IS NULL) AND  (envelopes.envelope_scan_flag = '0' OR envelopes.item_scan_flag = '0' ) )
           OR (envelopes.direct_shipping_flag =  '0'  AND (envelopes.item_scan_flag IS NULL OR envelopes.item_scan_flag = '1') )
		   OR (envelopes.direct_shipping_flag =  '1'  AND envelopes.tracking_number_flag = '0' AND envelopes.trash_flag IS NULL)
           OR (envelopes.trash_flag = '0') OR (envelopes.trash_flag = '".APConstants::ENVELOPE_TRASH_BY_CUSTOMER_IN_TRASH_FOLDER."')
        )
        AND ((postbox.company_verification_flag = 1 AND postbox.name_verification_flag = 1 AND customers_address.invoice_address_verification_flag = 1) OR (envelopes.trash_flag = '0') OR (envelopes.trash_flag = '".APConstants::ENVELOPE_TRASH_BY_CUSTOMER_IN_TRASH_FOLDER."') )
        AND (customers.activated_flag = '1' OR (customers.activated_flag='0' AND (customers.deactivated_type = '' OR customers.deactivated_type IS NULL)) OR (envelopes.trash_flag = '0') OR (envelopes.trash_flag = '".APConstants::ENVELOPE_TRASH_BY_CUSTOMER_IN_TRASH_FOLDER."') )
        )
        " . $where_condition . "
        )
       UNION ALL
       (SELECT distinct
        envelopes.* 
        , customers.user_name as to_customer_name
        , users.username as admin_name
        , customers.activated_flag
        , customers.status
        , customers.required_verification_flag      
        , envelope_comment.text as comment
        , customers.email_confirm_flag
        , customers.deactivated_type
        , customers_address.invoice_address_verification_flag
        , postbox.name_verification_flag
        , postbox.company_verification_flag
        , 12 * ( YEAR(now()) - YEAR(FROM_UNIXTIME(customers.created_date) )) + (MONTH(now()) - MONTH (FROM_UNIXTIME(customers.created_date)) ) + 1 as registration_month
        FROM envelopes
        INNER JOIN customers ON customers.customer_id = envelopes.to_customer_id
        LEFT JOIN envelope_comment ON envelope_comment.customer_id=customers.customer_id and envelope_comment.envelope_id=envelopes.id
        INNER JOIN postbox ON postbox.postbox_id = envelopes.postbox_id
        LEFT JOIN customers_address ON customers_address.customer_id = envelopes.to_customer_id
        LEFT JOIN users ON users.id = envelopes.completed_by
        WHERE(
		    (envelopes.collect_shipping_flag = '0' AND envelopes.package_id > 0 AND (envelopes.item_scan_flag IS NULL OR envelopes.item_scan_flag = '1'))
		 OR (envelopes.collect_shipping_flag = '1' AND envelopes.tracking_number_flag = '0' AND envelopes.trash_flag IS NULL)	
        )   
        AND ((postbox.company_verification_flag = 1) AND (postbox.name_verification_flag = 1) AND (customers_address.invoice_address_verification_flag = 1))
        AND (customers.activated_flag = '1' OR (customers.activated_flag='0' AND (customers.deactivated_type = '' OR customers.deactivated_type IS NULL)))
        " . $where_condition . " 
        GROUP BY envelopes.package_id)";       

        // Count all record with input condition
        $total_record = $this->db->query("SELECT COUNT(*) AS total FROM (" . $sql . ") AS TMP")->result();
        if ($total_record[0] && $total_record[0]->total == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }
     
        //echo $order_by_condition;exit;
        // get data statement.
        $sql_statement = "SELECT DISTINCT * FROM ({$sql}) AS TMP ORDER BY " . $order_by_condition . " LIMIT {$start},{$limit}";
        $data = $this->db->query($sql_statement)->result();

        return array(
            "total" => $total_record[0]->total,
            "data" => $data
        );
    }

    /**
     * Count envelopes by month.
     * @param unknown $yearMonth
     * @param unknown $locationId
     */
    public function countEnvelopesByMonth($yearMonth, $locationId, $charge_flag = true)
    {
        $location_condition = "";
        if ($locationId) {
            $location_condition .= " AND envelopes.location_id IN (" . $locationId . ") ";
        }
        
        if($charge_flag){
            $location_condition .= " AND customers.charge_fee_flag = 1 ";
        }
        $query = "
                (SELECT 
                    'received_number' as  kind
                    , count(*) as total
                FROM envelopes
                INNER JOIN customers ON customers.customer_id=envelopes.to_customer_id
                WHERE 
                    from_unixtime(envelopes.incomming_date, '%Y%m')='" . $yearMonth . "'
                    AND RIGHT(envelope_code, 4) <> '_000'
                    " . $location_condition . "
                )
                UNION
                (SELECT 
                    'envelope_scanned_number'
                    , count(*)
                FROM envelopes
                INNER JOIN customers ON customers.customer_id=envelopes.to_customer_id
                WHERE 
                    envelopes.envelope_scan_flag = 1
                    AND from_unixtime(envelopes.envelope_scan_date, '%Y%m')='" . $yearMonth . "'
                    AND RIGHT(envelope_code, 4) <> '_000'
                    " . $location_condition . "
                )
                UNION
                (SELECT 
                    'item_scanned_number'
                    , count(*) 
                FROM envelopes
                INNER JOIN customers ON customers.customer_id=envelopes.to_customer_id
                WHERE 
                    envelopes.item_scan_flag = 1
                    AND from_unixtime(envelopes.item_scan_date, '%Y%m')='" . $yearMonth . "'
                    AND RIGHT(envelope_code, 4) <> '_000'
                    " . $location_condition . "
                )
                UNION
                (SELECT 
                    'forwarded_number' 
                    , count(*)
                FROM envelopes
                INNER JOIN customers ON customers.customer_id=envelopes.to_customer_id
                WHERE 
                    (envelopes.direct_shipping_flag = '1' OR envelopes.collect_shipping_flag = '1')
                    AND (from_unixtime(envelopes.direct_shipping_date, '%Y%m')='" . $yearMonth . "' OR from_unixtime(envelopes.collect_shipping_date, '%Y%m')='" . $yearMonth . "') 
                    AND RIGHT(envelope_code, 4) <> '_000'
                    " . $location_condition . "
                )
        ";

        $result = $this->db->query($query)->result();

        return $result;
    }

    /**
     * Gets number item of customer list
     * @param unknown $listCustomerId
     */
    public function get_number_item_of_customer($listCustomerId)
    {
        $this->db->select("to_customer_id as customer_id, count(*) as number_item");
        $this->db->join('postbox', 'postbox.postbox_id=envelopes.postbox_id', 'inner');
        $this->db->where_in("envelopes.to_customer_id", $listCustomerId);
        $this->db->where("postbox.deleted <> 1", null);
        $this->db->where("postbox.completed_delete_flag <> 1", null);
        $this->db->where("RIGHT(envelopes.envelope_code, 4) <> '_000'", null);
        $this->db->group_by("to_customer_id");

        $data = $this->db->get($this->_table)->result();
        return $data;

        return count($data);
    }

    /**
     * Tinh toan total invoice 10 phut 1 lan
     */
    public function update_all_envelope_not_has_fee_storage()
    {
        $target_month = APUtils::getCurrentYear() . APUtils::getCurrentMonth();
        $sql = "
    	UPDATE envelopes
    	SET
        	storage_flag = 0,
            storage_date= NULL
    	WHERE 
    	    (direct_shipping_date > 0 and direct_shipping_flag = '1')
    	    OR (collect_shipping_date > 0 and collect_shipping_flag = '1')
    	    OR (trash_flag = '1' OR trash_flag = '".APConstants::ENVELOPE_COMPLETED_TRASH_BY_ADMIN."')
    	";
        $this->customer_m->db->query($sql);
        
        $first_day_month = APUtils::convert_date_to_timestamp(APUtils::getFirstDayOfCurrentMonth());
        $sql = "
    	UPDATE envelopes
    	SET
        	current_storage_charge_fee_day = 0
    	WHERE 
    	    ((direct_shipping_date > 0 and direct_shipping_date < ".$first_day_month.")
    	    OR (collect_shipping_date > 0 and collect_shipping_date < ".$first_day_month.")
    	    OR (trash_date > 0 and trash_date < ".$first_day_month."))
    	    AND completed_flag = '1' and completed_date < ".$first_day_month."
    	";
        $this->customer_m->db->query($sql);
    }

    public function countEnvelopesOfDeletedCustomer($yearMonth, $locationId, $charge_flag = true)
    {
        $location_condition = "";
        if ($locationId) {
            $location_condition .= " AND envelopes_completed.location_id IN (" . $locationId . ") ";
        }
        if($charge_flag){
            $location_condition .= " AND customers.charge_fee_flag = 1 ";
        }

        $query = "
            (SELECT 
                'received_number' as  kind
                , count(*) as total
            FROM envelopes_completed
            JOIN customers ON customers.customer_id=envelopes_completed.to_customer_id
            WHERE 
                from_unixtime(envelopes_completed.completed_date, '%Y%m')='" . $yearMonth . "'
                AND customers.status = 1
                AND activity_id=10
                " . $location_condition . "
            )
            UNION 
            (SELECT 
                'envelope_scanned_number'
                , count(*)
            FROM envelopes_completed
            JOIN customers ON customers.customer_id=envelopes_completed.to_customer_id
            WHERE 
                from_unixtime(envelopes_completed.completed_date, '%Y%m')='" . $yearMonth . "'
                AND customers.status = 1
                AND activity_id=1
                " . $location_condition . "
            )
            UNION
            (SELECT 
                'item_scanned_number'
                , count(*) 
            FROM envelopes_completed
            JOIN customers ON customers.customer_id=envelopes_completed.to_customer_id
            WHERE 
                from_unixtime(envelopes_completed.completed_date, '%Y%m')='" . $yearMonth . "'
                AND customers.status = 1
                AND activity_id=2
                " . $location_condition . "
            )
            UNION
            (SELECT 
                'forwarded_number' 
                , count(*)
            FROM envelopes_completed
            JOIN customers ON customers.customer_id=envelopes_completed.to_customer_id
            WHERE 
                from_unixtime(envelopes_completed.completed_date, '%Y%m')='" . $yearMonth . "'
                AND customers.status = 1
                AND (activity_id=3 OR activity_id = 4)
                " . $location_condition . "
            )
        ";

        $result = $this->db->query($query)->result();

        return $result;
    }
    // For updating shipping rate, shipping service of envelope
    public function add_shipping_rate($envelope_id,$shipping_rate,$shipping_rate_id){
        $list_envelope_id = explode(',', $envelope_id);
        $data = array(
               'shipping_rate' => $shipping_rate,
               'shipping_rate_id' => $shipping_rate_id
            );

        foreach ($list_envelope_id as $envelope){
            $this->db->where('id', $envelope);
            $this->db->update('envelopes', $data);
        }
        return 1;
    }
    // For getting all items which are ready for collect shipment
    public function getAllReadyCollectItems($cusotmer_id,$postbox_id){
        
        $this->db->select('envelope_customs.envelope_id');
        $this->db->from('envelope_customs');
        $this->db->where('envelope_customs.process_flag', APConstants::OFF_FLAG);
        $this->db->where('envelope_customs.customer_id', $cusotmer_id);

        $exclude_envelope_id = $this->db->get()->result();
        $ex_envelope_id = array();
        foreach($exclude_envelope_id as $ex_id){
            array_push($ex_envelope_id, $ex_id->envelope_id);
        }
        $exculde_str = '';
        if(!empty($ex_envelope_id)){
            $exculde_str = ' AND e.id NOT IN (';
            $exculde_str .= implode(",",$ex_envelope_id);
            $exculde_str .= ')';
        }
        
        //echo $exculde_str;exit;
        
        $query = 'select e.id from envelopes e where e.collect_shipping_flag = 0 AND (e.package_id IS NULL OR e.package_id=0) '
                . 'AND e.to_customer_id = '.$cusotmer_id.$exculde_str.' and e.postbox_id='.$postbox_id;

        return $this->db->query($query)->result();

    }
    
    public function getStorageNumberByLocation($customer_id, $location_id, $postbox_type, $envelope_types, $yearMonth){
        $this->db->select("COUNT(*) as total");
        $this->db->from("envelopes");
        $this->db->join("postbox", "envelopes.postbox_id = postbox.postbox_id");
        $this->db->where("envelopes.to_customer_id", $customer_id);
        $this->db->where("envelopes.location_id", $location_id);
        $this->db->where("postbox.type", $postbox_type);
        $this->db->where_in("envelopes.envelope_type_id", $envelope_types);
        $this->db->where("from_unixtime(envelopes.incomming_date, '%Y%m')='" . $yearMonth . "'", null);

        $row = $this->db->get()->row();

        return $row->total;
    }

    public function getInforItemTracking($envelope_id, $shipping_service_id){

        $this->db->select('
                et.tracking_number, ss.name as shipping_service_name, 
                c.email, e.from_customer_name, sc.tracking_number_url'
        )->distinct();
        
        $this->db->from("envelopes e");
        
        $this->db->join('envelope_shipping_tracking et', 'et.envelope_id = e.id', 'inner');
        $this->db->join('customers c', 'c.customer_id = e.to_customer_id', 'inner');

        $this->db->join('shipping_services ss', 'ss.id = et.shipping_services_id', 'inner');
        $this->db->join('shipping_carriers sc', 'ss.carrier_id = sc.id', 'left');
        
        $this->db->where('e.id', $envelope_id);

        $this->db->where('ss.id', $shipping_service_id);
        
        return  $this->db->get()->row();

    }

    /**
     * Get all request collect envelope of this postbox that need declare custom but does not declare yet
     */
    public function get_all_package_envelope($postbox_id)
    {
        $this->db->select('envelopes.id')->distinct();
        $this->db->from("envelopes");
        $this->db->join("settings", "envelopes.envelope_type_id = settings.ActualValue AND SettingCode = '".APConstants::ENVELOPE_TYPE_CODE."'");
        
        // can chekc nay de khai bao hai quan dung cho 2 packages request.
        $this->db->join("envelope_customs", "envelope_customs.envelope_id = envelopes.id", "left");
        $this->db->where("envelope_customs.package_id is null", null);
        
        $this->db->where('settings.Alias01', '1');
        $this->db->where('envelopes.postbox_id', $postbox_id);
        $this->db->where('envelopes.collect_shipping_flag', '0');
        $this->db->where('(envelopes.package_id is null or envelopes.package_id =0)', null);
        return $this->db->get()->result();
    }
    
    public function get_all_envelope_must_declare_customs($postbox_id) {
        $this->db->select('envelopes.id')->distinct();
        $this->db->from("envelopes");
        $this->db->join("envelope_customs", "envelope_customs.envelope_id = envelopes.id", "left");
        $this->db->where('envelopes.postbox_id', $postbox_id);
        $this->db->where('envelopes.collect_shipping_flag', '0');
        
        // chi lay ra cac item chua co dang ki declare custom.
        $this->db->where("envelope_customs.id is null", null);
        
        // chi get ra cac item chua co request collect shipping.
        $this->db->where('(envelopes.package_id is null or envelopes.package_id =0)', null);
        return $this->db->get()->result();
    }
    
    public function get_envelope_properties_by($customer_id, $package_id, $envelope_id=''){
        $this->db->select("e.*, ep.width, ep.height, ep.length");
        $this->db->from("envelopes e");
        $this->db->join("envelope_properties ep", "e.id=ep.envelope_id", "left");
        $this->db->where("e.to_customer_id", $customer_id);

        if($package_id){
            $this->db->where("e.package_id", $package_id);
        }

        if($envelope_id){
            $this->db->where("e.id", $envelope_id);
        }

        $result = $this->db->get()->result();
        return $result;
    }
    
    public function get_envelope_info($envelope_ids){
        $this->db->select("envelopes.*");
        $this->db->select("location.location_name");
        $this->db->join("location", "location.id=envelopes.location_id", "left");
        $this->db->where_in("envelopes.id", $envelope_ids);
        
        if(is_array($envelope_ids)){
            $result = $this->db->get($this->profile_table)->result();
        }else{
            $result = $this->db->get($this->profile_table)->row();
        }
        
        return $result;
    }
    
    /**
     * Count envelopes by month of customer of partner
     * @param unknown $yearMonth
     * @param unknown $locationId
     */
    public function countEnvelopesByMonthOfPartner($yearMonth, $partner_id)
    {
        $location_condition = "";
        if (!empty($partner_id)) {
            $location_condition .= " AND partner_customers.partner_id ='" . $partner_id . "' AND partner_customers.end_flag = 0 ";
        }
        
        $query = "
                (SELECT 
                    'received_number' as  kind
                    , count(*) as total
                FROM envelopes
                INNER JOIN customers ON customers.customer_id=envelopes.to_customer_id
                INNER JOIN partner_customers ON partner_customers.customer_id = envelopes.to_customer_id
                WHERE 
                    from_unixtime(envelopes.incomming_date, '%Y%m')='" . $yearMonth . "'
                    AND RIGHT(envelope_code, 4) <> '_000'
                    " . $location_condition . "
                )
                UNION
                (SELECT 
                    'envelope_scanned_number'
                    , count(*)
                FROM envelopes
                INNER JOIN customers ON customers.customer_id=envelopes.to_customer_id
                INNER JOIN partner_customers ON partner_customers.customer_id = envelopes.to_customer_id
                WHERE 
                    envelopes.envelope_scan_flag = 1
                    AND from_unixtime(envelopes.envelope_scan_date, '%Y%m')='" . $yearMonth . "'
                    AND RIGHT(envelope_code, 4) <> '_000'
                    " . $location_condition . "
                )
                UNION
                (SELECT 
                    'item_scanned_number'
                    , count(*) 
                FROM envelopes
                INNER JOIN customers ON customers.customer_id=envelopes.to_customer_id
                INNER JOIN partner_customers ON partner_customers.customer_id = envelopes.to_customer_id
                WHERE 
                    envelopes.item_scan_flag = 1
                    AND from_unixtime(envelopes.item_scan_date, '%Y%m')='" . $yearMonth . "'
                    AND RIGHT(envelope_code, 4) <> '_000'
                    " . $location_condition . "
                )
                UNION
                (SELECT 
                    'forwarded_number' 
                    , count(*)
                FROM envelopes
                INNER JOIN customers ON customers.customer_id=envelopes.to_customer_id
                INNER JOIN partner_customers ON partner_customers.customer_id = envelopes.to_customer_id
                WHERE 
                    (envelopes.direct_shipping_flag = '1' OR envelopes.collect_shipping_flag = '1')
                    AND (from_unixtime(envelopes.direct_shipping_date, '%Y%m')='" . $yearMonth . "' OR from_unixtime(envelopes.collect_shipping_date, '%Y%m')='" . $yearMonth . "') 
                    AND RIGHT(envelope_code, 4) <> '_000'
                    " . $location_condition . "
                )
        ";

        $result = $this->db->query($query)->result();

        return $result;
    }
    
    /**
     * Count envelopes by month of deleted customer of partner
     * @param unknown $yearMonth
     * @param unknown $locationId
     */
    public function countEnvelopesOfDeletedCustomerOfPartner($yearMonth, $partner_id)
    {
        $location_condition = "";
        if (!empty($partner_id)) {
            $location_condition .= " AND partner_customers.partner_id ='" . $partner_id . "' AND partner_customers.end_flag = 0 ";
        }

        $query = "
            (SELECT 
                'received_number' as  kind
                , count(*) as total
            FROM envelopes_completed
            JOIN customers ON customers.customer_id=envelopes_completed.to_customer_id
            JOIN partner_customers ON partner_customers.customer_id = envelopes_completed.to_customer_id
            WHERE 
                from_unixtime(envelopes_completed.completed_date, '%Y%m')='" . $yearMonth . "'
                -- AND customers.charge_fee_flag = 1
                AND customers.status = 1
                AND activity_id=10
                " . $location_condition . "
            )
            UNION 
            (SELECT 
                'envelope_scanned_number'
                , count(*)
            FROM envelopes_completed
            JOIN customers ON customers.customer_id=envelopes_completed.to_customer_id
            JOIN partner_customers ON partner_customers.customer_id = envelopes_completed.to_customer_id
            WHERE 
                from_unixtime(envelopes_completed.completed_date, '%Y%m')='" . $yearMonth . "'
                -- AND customers.charge_fee_flag = 1
                AND customers.status = 1
                AND activity_id=1
                " . $location_condition . "
            )
            UNION
            (SELECT 
                'item_scanned_number'
                , count(*) 
            FROM envelopes_completed
            JOIN customers ON customers.customer_id=envelopes_completed.to_customer_id
            JOIN partner_customers ON partner_customers.customer_id = envelopes_completed.to_customer_id
            WHERE 
                from_unixtime(envelopes_completed.completed_date, '%Y%m')='" . $yearMonth . "'
                -- AND customers.charge_fee_flag = 1
                AND customers.status = 1
                AND activity_id=2
                " . $location_condition . "
            )
            UNION
            (SELECT 
                'forwarded_number' 
                , count(*)
            FROM envelopes_completed
            JOIN customers ON customers.customer_id=envelopes_completed.to_customer_id
            JOIN partner_customers ON partner_customers.customer_id = envelopes_completed.to_customer_id
            WHERE 
                from_unixtime(envelopes_completed.completed_date, '%Y%m')='" . $yearMonth . "'
                -- AND customers.charge_fee_flag = 1
                AND customers.status = 1
                AND (activity_id=3 OR activity_id = 4)
                " . $location_condition . "
            )
        ";

        $result = $this->db->query($query)->result();

        return $result;
    }

}