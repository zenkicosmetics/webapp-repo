<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author
 */
class customer_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('customers');
        $this->primary_key = 'customer_id';
    }

    /**
     * Get all paging data
     *
     * @param unknown_type $array_where
     *            The array of condition (array ('name' => 'DungNT', 'age' => 30))
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
    public function get_customer_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC', $list_access_location_id = array())
    {
        // Count all record with input condition
        $total_record = $this->count_by_customer_paging($array_where, $list_access_location_id);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('customers.*')->distinct();
        $this->db->select('p.name, p.company, p.name_verification_flag, p.company_verification_flag');
        $this->db->select('
                ca.invoicing_address_name,
                ca.invoicing_company,
                ca.shipment_address_name,
                ca.shipment_city,
                ca.shipment_country,
                ca.shipment_company,
                ca.invoicing_country,
                ca.eu_member_flag,
                ca.invoice_address_verification_flag,
                ct.country_name,
                u.display_name,
                cs1.setting_value as active_postbox_name_flag
       	');
        $this->db->join('postbox p', 'p.customer_id = customers.customer_id', 'inner');
        $this->db->join('customers_address ca', 'ca.customer_id = customers.customer_id', 'left');
        $this->db->join('country ct', 'ct.id = ca.shipment_country', 'left');
        $this->db->join('users u', 'u.id = customers.deleted_by', 'left');
        $this->db->join('customer_product_settings cs1', "cs1.customer_id = customers.customer_id AND cs1.product_id=1 AND cs1.setting_key='postbox_name_flag'", 'left');


        // Search all data with input condition
        //$this->db->where('p.is_main_postbox', '1');
        if (count($list_access_location_id) > 0) {
            $this->db->where('p.location_available_id', $list_access_location_id[0]);
        }
        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $this->db->limit($limit);
        if (!empty($sort_column)) {
            if ($sort_column == 'user_name') {
                $this->db->order_by('p.name', $sort_type);
                $this->db->order_by('p.company', $sort_type);
            } else {
                $this->db->order_by($sort_column, $sort_type);
            }
        }
        $this->db->order_by('customers.customer_id', 'desc');
        $this->db->group_by('customers.customer_id');
        $data = $this->db->get($this->_table, $limit, $start)->result();

        return array(
            "total" => $total_record,
            "data" => $data
        );
    }

    /**
     * Count customer
     *
     * @param unknown_type $array_where
     */
    public function count_by_customer_paging($array_where, $list_access_location_id = array())
    {
        $this->db->select('COUNT(DISTINCT(customers.customer_id)) AS total_record');
        $this->db->from('customers');
        $this->db->join('postbox p', 'p.customer_id = customers.customer_id', 'inner');
        $this->db->join('customers_address ca', 'ca.customer_id = customers.customer_id', 'left');
        $this->db->join('country ct', 'ct.id = ca.shipment_country', 'left');
        $this->db->join('users u', 'u.id = customers.deleted_by', 'left');
        // Search all data with input condition
        //$this->db->where('p.is_main_postbox', '1');

        if (count($list_access_location_id) > 0) {
            $this->db->where('p.location_available_id', $list_access_location_id[0]);
        }

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $result = $this->db->get()->row();
        return $result->total_record;
    }

    // --------------------------------------------------------------------------
    /**
     * Get all paging data
     *
     * @param unknown_type $array_where
     *            The array of condition (array ('name' => 'DungNT', 'age' => 30))
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
    public function get_customer_blacklist_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_by_customer_blacklist_paging($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('c.customer_id,c.deleted_by, c.customer_code,c.status,
            c.created_date as register_date, customer_blacklist.*,u.display_name'
        )->distinct();
        $this->db->join('customers c', 'customer_blacklist.customer_id = c.customer_id', 'left');
        $this->db->join('users u', 'u.id = c.deleted_by', 'left');
        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $this->db->limit($limit);
        if (!empty($sort_column)) {
            if ($sort_column == 'customer_code' || $sort_column == 'customer_code' || $sort_column == 'register_date' || $sort_column == 'deleted_by') {
                if( $sort_column == 'register_date'){
                    $sort_column = 'created_date';
                }
                $this->db->order_by('c.'.$sort_column, $sort_type);
            } else {
                $this->db->order_by('customer_blacklist.'.$sort_column, $sort_type);
            }
        }
        else {
             $this->db->order_by('customer_blacklist.id', 'DESC');
        }

        $data = $this->db->get('customer_blacklist', $limit, $start)->result();

        return array(
            "total" => $total_record,
            "data" => $data
        );
    }

    /**
     * Count customer
     *
     * @param unknown_type $array_where
     */
    public function count_by_customer_blacklist_paging($array_where)
    {
        $this->db->select('COUNT(DISTINCT(cb.customer_id)) AS total_record');

        $this->db->from('customer_blacklist cb');
        $this->db->join('customers c', 'cb.customer_id = c.customer_id', 'left');
        $this->db->join('users u', 'u.id = c.deleted_by', 'left');
        // Search all data with input condition

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $result = $this->db->get()->row();
        return $result->total_record;
    }

    // --------------------------------------------------------------------------


    /**
     * Create a new customer
     */
    public function insert($input = array(), $not_is_import = TRUE, $created_by_id = null)
    {
        $raw_customer_id = parent::insert(array(
            'user_name' => $input ['email'],
            'password' => $input ['password'],
            'email' => $input ['email'],
            'account_type' => $input ['account_type'],
            'charge_fee_flag' => $input ['charge_fee_flag'],
            'activated_key' => $input ['activated_key'],
            'accept_terms_condition_flag' => isset($input ['accept_terms_condition_flag']) ? $input ['accept_terms_condition_flag']: 0,
            'created_date' => empty($input ['created_date']) ? now() : $input ['created_date'],
            'created_notify_date' => now(),
            'required_verification_flag' => (isset($input ['required_verification_flag']) ? $input ['required_verification_flag'] : 0)
        ));

        //#1309: Insert customer history_list
        $history_list = array();
        // Log create action
        $history_list['created'] = [
            'customer_id' => $raw_customer_id,
            'action_type' => APConstants::CUSTOMER_HISTORY_ACTIVITY_CREATE,
            'old_data' => null,
            'current_data' => $input ['email'],
            'created_by_id' => $created_by_id
        ];
        // Log change status to never-activated
        $history_list['status'] = [
            'customer_id' => $raw_customer_id,
            'action_type' => APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_STATUS,
            'old_data' => null,
            'current_data' => APConstants::CUSTOMER_HISTORY_STATUS_NEVER_ACTIVATED,
            'created_by_id' => $created_by_id
        ];
        customers_api::insertCustomerHistory($history_list);

        // Get customer code and update again
        $customer_id = $raw_customer_id;
        $customer_code = sprintf('C%1$08d', $customer_id);
        parent::update($raw_customer_id, array(
            'customer_code' => $customer_code
        ));
        parent::update_by_many(array(
            'customer_code' => $customer_code
        ), array(
            'customer_id' => $customer_id
        ));
        return $customer_id;
    }

    /**
     * Get all customers have been registered within 24 hour
     */
    public function get_new_customer_in24($pre_created_date)
    {
        $this->db->select('customers.customer_id, postbox.postbox_id')->distinct();
        $this->db->from('customers');
        $this->db->join('postbox', 'customers.customer_id = postbox.customer_id');
        $this->db->where('postbox.is_main_postbox', '1');
        $this->db->where('customers.created_date <= ', $pre_created_date);
        $this->db->where('customers.created_date > ', $pre_created_date -  24 * 60 * 60);

        return $this->db->get()->result();
    }

    /**
     * Get all customer have not make payment 1
     */
    public function get_all_customer_for_payment1($invoice_month)
    {
        $this->db->select('customers.*')->distinct();
        $this->db->from('customers');
        $this->db->join('invoice_summary', 'customers.customer_id = invoice_summary.customer_id');
        $this->db->where('customers.charge_fee_flag', APConstants::ON_FLAG);
        $this->db->where('invoice_summary.invoice_month', $invoice_month);
        $this->db->where('customers.charge_fee_flag', '1');
        // $this->db->where('customers.activated_flag', '1');
        $this->db->where("(invoice_summary.payment_1st_flag IS NULL OR invoice_summary.payment_1st_flag = '0')", null);
        return $this->db->get()->result();
    }

    /**
     * Get all customer have not make payment 2
     */
    public function get_all_customer_for_payment2($invoice_month)
    {
        $this->db->select('customers.*')->distinct();
        $this->db->from('customers');
        $this->db->join('invoice_summary', 'customers.customer_id = invoice_summary.customer_id');
        $this->db->where('customers.charge_fee_flag', APConstants::ON_FLAG);
        $this->db->where('invoice_summary.invoice_month', $invoice_month);
        $this->db->where('customers.charge_fee_flag', '1');
        // $this->db->where('customers.activated_flag', '1');
        $this->db->where("(invoice_summary.payment_2st_flag IS NULL OR invoice_summary.payment_2st_flag = '0')", null);
        return $this->db->get()->result();
    }

    /**
     * Get all customer have not make payment 2
     */
    public function get_all_customer_for_payment($invoice_month)
    {
        $this->db->select('customers.*')->distinct();
        $this->db->from('customers');
        $this->db->join('invoice_summary', 'customers.customer_id = invoice_summary.customer_id');
        $this->db->where('customers.charge_fee_flag', APConstants::ON_FLAG);
        $this->db->where('invoice_summary.invoice_month', $invoice_month);
        $this->db->where('customers.charge_fee_flag', '1');
        return $this->db->get()->result();
    }

    /**
     * Get all customer for charge
     */
    public function get_all_customer_for_charge()
    {
        $this->db->select('customers.*')->distinct();
        $this->db->from('customers');

        $this->db->where('customers.charge_fee_flag', APConstants::ON_FLAG);

        // Not get deleted account
        $this->db->where("(status IS NULL OR status <> '1')", NULL);

        return $this->db->get()->result();
    }

    /**
     * Get all customer for notify open balance.
     * Will only be send to customers that have invoice or paypal payment as standard and truely have an open balance due after the month end invoice is created.
     */
    public function get_all_customer_for_notify_open_balance()
    {
        $this->db->select('customers.*')->distinct();
        $this->db->from('customers');

        $this->db->where('customers.charge_fee_flag', APConstants::ON_FLAG);

        // Invoice (Not support paypal)
        $this->db->where('customers.invoice_type', '2');

        // Not get deleted account
        $this->db->where("(status IS NULL OR status <> '1')", NULL);

        return $this->db->get()->result();
    }

    /**
     * Get all customer for notify open balance.
     * Will only be send to customers that have invoice or paypal payment as standard and truely have an open balance due after the month end invoice is created.
     */
    public function get_all_customer_for_calculate_storage_fee()
    {
        $this->db->select('customers.*')->distinct();
        $this->db->from('customers');

        $this->db->join('envelopes', 'customers.customer_id = envelopes.to_customer_id');
        $this->db->where('customers.charge_fee_flag', APConstants::ON_FLAG);

        // Not get deleted account
        $this->db->where("(status IS NULL OR status <> '1')", NULL);
        // $this->db->where("envelopes.current_storage_charge_fee_day > 0", NULL);

        return $this->db->get()->result();
    }

    /**
     *
     * @param type $id
     * @return type
     */
    public function set_deleted($id = 0)
    {
        $update_arr = array(
            'status' => 1,
            'plan_delete_date' => null,
            'deleted_date' => now()
        );
        return parent::update($id, $update_arr);
    }

    /**
     * Update customer information.
     *
     * @see MY_Model::update()
     */
    public function update_password($id = 0, $input = array())
    {
        $update_arr ['password'] = $input ['password'];
        return parent::update($id, $update_arr);
    }

    /**
     * Check customer with id already exist.
     *
     * @param unknown_type $id_arr
     * @return boolean
     */
    public function is_existed($id_arr)
    {
        $total_row = parent::count_by_many($id_arr);
        if ($total_row) {
            return TRUE;
        }
        return FALSE;
    }

    private function _get_last_id()
    {
        $last_id = now();
        $last_id_object = $this->db->select('customer_id')->order_by('customer_id desc')->get('customers')->row();

        if (empty($last_id_object)) {
            return $last_id;
        } else {
            $last_id = $last_id_object->customer_id;
        }

        return ++$last_id;
    }

    public function get_current_customer_info()
    {
        $customer_id = APContext::getCustomerCodeLoggedIn();
        if (!$customer_id)
            return false;

        $this->db->select('t1.*, t2.invoice_address_verification_flag, t3.currency_sign, t3.currency_rate');
        $this->db->from('customers t1');
        $this->db->join('customers_address t2', 't2.customer_id = t1.customer_id', 'left');
        $this->db->join('currencies t3', 't3.currency_id = t1.currency_id', 'left');
        $this->db->where('t1.customer_id', $customer_id);

        $query = $this->db->get();
        if ($query->num_rows() != 0) {
            return $query->row();
        } else {
            return false;
        }

    }

    public function get_standard_setting_currency($customer_id = 0)
    {
        $currency = null;
        if (empty($customer_id)) $customer_id = APContext::getCustomerCodeLoggedIn();

        // Firstly, get currency by customer's selection/setting
        $this->db->select('t2.currency_id, t2.currency_short, t2.currency_sign, t2.currency_rate');
        $this->db->from('customers t1');
        $this->db->join('currencies t2', 't2.currency_id = t1.currency_id', 'inner');
        $this->db->where('t1.customer_id', $customer_id);
        $query = $this->db->get();
        $currency = ($query->num_rows() != 0) ? $query->row() : null;

        // Secondly, get currency basing on customer's country
        if (empty($currency)) {
            $this->db->select('cu.currency_id, cu.currency_short, cu.currency_sign, cu.currency_rate');
            $this->db->from('country co');
            $this->db->join('customers_address ca', 'co.id = ca.invoicing_country', 'inner');
            $this->db->join('currencies cu', 'co.currency_id = cu.currency_id', 'inner');
            $this->db->where('ca.customer_id', $customer_id);
            $query = $this->db->get();
            $currency = ($query->num_rows() != 0) ? $query->row() : null;

            // Third, get currency by default
            if (empty($currency)) {
                $this->db->select('currency_id, currency_short, currency_sign, currency_rate');
                $this->db->from('currencies');
                $this->db->where('currency_short', 'EUR');
                $query = $this->db->get();
                $currency = $query->row();
            }
        }

        return $currency;
    }

    public function get_standard_setting_decimal_separator($customer_id = 0)
    {
        if (empty($customer_id)) $customer_id = APContext::getCustomerCodeLoggedIn();

        // Firstly, get decimal separator setting by the customer
        $this->db->select('decimal_separator');
        $this->db->from('customers');
        $this->db->where('customer_id', $customer_id);
        $row = $this->db->get()->row();
        $decimal_separator = isset($row) ? $row->decimal_separator : '';

        // Secondly, get decimal separator specific to each country
        if (empty($decimal_separator)) {
            $this->db->select('co.decimal_separator');
            $this->db->from('country co');
            $this->db->join('customers_address ad', 'co.id = ad.invoicing_country', 'inner');
            $this->db->where('ad.customer_id', $customer_id);
            $row = $this->db->get()->row();
            $decimal_separator = isset($row) ? $row->decimal_separator : '';

            // Third, set decimal separator by default value
            if (empty($decimal_separator)) $decimal_separator = APConstants::DECIMAL_SEPARATOR_COMMA;
        }

        return $decimal_separator;
    }

    public function get_active_customer_by_email($email)
    {
        $this->db->where('email', $email);
        $this->db->where('(status is NULL or status <> 1)', null);
        return $this->db->get($this->_table)->row();
    }

    public function get_active_customer_by_account($email, $password)
    {
        $this->db->where('email', $email);
        $this->db->where('password', $password);
        $this->db->where('(status is NULL or status <> 1)', null);
        return $this->db->get($this->_table)->row();
    }

    public function update_by_many($array_where, $data)
    {
        $data ['last_updated_date'] = now();
        return parent::update_by_many($array_where, $data);
    }

    /**
     * Get all paging data by postbox
     *
     * @param unknown_type $array_where
     *            The array of condition (array ('name' => 'DungNT', 'age' => 30))
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
    public function get_postbox_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC', $list_access_location_id = array())
    {
        // Count all record with input condition
        $total_record = $this->count_by_postbox_paging($array_where, $list_access_location_id);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }
        //customers.customer_id,customers.email,customers.customer_code,customers.status,customers.deactivated_type,customers.email_confirm_flag
        $this->db->select('customers.*, p.name_verification_flag, p.company_verification_flag');
        $this->db->select('p.postbox_code, p.name,p.deleted,p.completed_delete_flag, p.company, p.type, p.company as postbox_company, p.created_date as postbox_created_date, p.name as postbox_name');
        $this->db->select('ca.invoicing_address_name, ca.invoicing_company');
        $this->db->select(' (SELECT COUNT(*) FROM envelopes WHERE to_customer_id = customers.customer_id AND envelopes.postbox_id=p.postbox_id) as number_received_items');
        $this->db->join('postbox p', 'p.customer_id = customers.customer_id', 'inner');
        $this->db->join('customers_address ca', 'ca.customer_id = customers.customer_id', 'left');

        if (count($list_access_location_id) > 0) {
            $this->db->where('p.location_available_id', $list_access_location_id[0]);
        }
        //$this->db->where("(name <> '' or company <> '')", null);
        //$this->db->where("(p.deleted = '0' OR p.deleted IS NULL)", null);

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }

        if ($limit) {
            $this->db->limit($limit);
        }
        // sort the list as follows:
        // 1. priority: postbox type (business, private, free)
        // 2. priority: customer status (active, auto-deactivated, manu-deactivated)
        // 3. priority: number of received items (highes on top)
        if ($sort_column == 'postbox_name') {
            $this->db->order_by('p.name', $sort_type);
        }
        if ($sort_column == 'postbox_company') {
            $this->db->order_by('p.company', $sort_type);
        }
        $this->db->order_by('p.type', "DESC");
        $this->db->order_by('customers.deactivated_type', "ASC");
        $this->db->order_by('number_received_items', "DESC");
        if ($limit) {
            $data = $this->db->get($this->_table, $limit, $start)->result();
        } else {
            $data = $this->db->get($this->_table)->result();
        }
        return array(
            "total" => $total_record,
            "data" => $data
        );
    }

    /**
     * Count customer
     *
     * @param unknown_type $array_where
     */
    public function count_by_postbox_paging($array_where, $list_access_location_id = array())
    {
        $this->db->select('COUNT(p.postbox_id) AS total_record');
        $this->db->from('customers');
        //$this->db->join('customer_cloud', 'customer_cloud.customer_id = customers.customer_id', 'left');
        $this->db->join('postbox p', 'p.customer_id = customers.customer_id', 'inner');
        $this->db->join('customers_address ca', 'ca.customer_id = customers.customer_id', 'left');
        // Search all data with input condition

        if (count($list_access_location_id) > 0) {
            $this->db->where('p.location_available_id', $list_access_location_id[0]);
        }

        //$this->db->where("(name <> '' or company <> '')", null);
        //$this->db->where("(p.deleted = '0' OR p.deleted IS NULL)", null);

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $result = $this->db->get()->row();
        return $result->total_record;
    }

    /**
     * Gets all customers that must verify cases
     */
    public function get_list_customers_must_verify_case()
    {
        $this->db->select('c.customer_id, c.email');
        $this->db->from('customers as c');
        $this->db->join('customers_address as ad', 'c.customer_id=ad.customer_id', 'inner');
        $this->db->join('postbox as p', 'c.customer_id=p.customer_id', 'inner');
        $this->db->join('customer_product_settings cs1', "cs1.customer_id = c.customer_id AND cs1.product_id=1 AND cs1.setting_key='invoicing_address_completed'", 'left');
        $this->db->where('(cs1.setting_value is null OR cs1.setting_value = 0)', null);
        $this->db->or_where('p.name_verification_flag <> 1', null);
        $this->db->or_where('p.company_verification_flag <> 1', null);

        $this->db->group_by('c.customer_id');

        $data = $this->db->get()->result();
        return $data;
    }

    /**
     * #694: Gets all customers that has notification incomming item daily
     */
    public function get_customer_has_daily_incomming_notification()
    {
        $this->db->select('c.customer_id, c.email, a.invoicing_address_name, a.invoicing_company,p.postbox_name');
        $this->db->from('customers as c');
        $this->db->join('postbox as p', 'c.customer_id=p.customer_id', 'inner');
        $this->db->join('envelopes as e', 'c.customer_id=e.to_customer_id', 'inner');
        $this->db->join('postbox_settings as ps', 'p.customer_id=ps.customer_id AND p.postbox_id = ps.postbox_id', 'inner');
        $this->db->join('customers_address as a', 'c.customer_id=a.customer_id', 'left');
        $this->db->where('e.email_notification_flag', APConstants::OFF_FLAG);
        $this->db->where("ps.email_notification", 2);

        $this->db->group_by('c.customer_id');

        $data = $this->db->get()->result();
        return $data;
    }

    /**
     * #694: Gets all customers that has notification incomming item weekly
     * @return unknown
     */
    public function get_customer_has_weekly_incomming_notification()
    {
        $this->db->select('c.customer_id, c.email, a.invoicing_address_name, a.invoicing_company,p.postbox_name');
        $this->db->from('customers as c');
        $this->db->join('postbox as p', 'c.customer_id=p.customer_id', 'inner');
        $this->db->join('envelopes as e', 'c.customer_id=e.to_customer_id', 'inner');
        $this->db->join('postbox_settings as ps', 'p.customer_id=ps.customer_id AND p.postbox_id = ps.postbox_id', 'inner');
        $this->db->join('customers_address as a', 'c.customer_id=a.customer_id', 'left');
        $this->db->where('e.email_notification_flag', APConstants::OFF_FLAG);
        $this->db->where("ps.email_notification", 3);

        $this->db->group_by('c.customer_id');

        $data = $this->db->get()->result();

        return $data;
    }

    /**
     * #694: Gets all customers that has notification incomming item monthly
     * @return unknown
     */
    public function get_customer_has_monthly_incomming_notification()
    {
        $this->db->select('c.customer_id, c.email, a.invoicing_address_name, a.invoicing_company,p.postbox_name');
        $this->db->from('customers as c');
        $this->db->join('postbox as p', 'c.customer_id=p.customer_id', 'inner');
        $this->db->join('envelopes as e', 'c.customer_id=e.to_customer_id', 'inner');
        $this->db->join('postbox_settings as ps', 'p.customer_id=ps.customer_id AND p.postbox_id = ps.postbox_id', 'inner');
        $this->db->join('customers_address as a', 'c.customer_id=a.customer_id', 'left');
        $this->db->where('e.email_notification_flag', APConstants::OFF_FLAG);
        $this->db->where("ps.email_notification", 4);

        $this->db->group_by('c.customer_id');

        $data = $this->db->get()->result();

        return $data;
    }

    /**
     * Gets all customers must require to verify cases.
     */
    public function get_all_customer_must_verify_case_address()
    {
        $this->db->select('customers.*')->distinct();
        $this->db->from('customers');
        $this->db->join('customers_address', 'customers_address.customer_id=customers.customer_id', 'inner');
        $this->db->join('postbox', 'customers.customer_id=postbox.customer_id', 'inner');
        $this->db->where('( customers_address.invoice_address_verification_flag = 0 OR postbox.company_verification_flag = 0 OR postbox.name_verification_flag = 0) ', null);
        $this->db->where('customers.activated_flag', APConstants::ON_FLAG);
        $this->db->where('customers.status <> 1', null);
        $this->db->where('postbox.completed_delete_flag <> 1', null);

        $this->db->limit(100);

        $data = $this->db->get()->result();

        return $data;
    }

	/**
     * Gets all customers must require to verify cases.
     */
    public function get_all_customer_has_vat_number()
    {
        $this->db->select('customers.customer_id, customers.vat_number as v_vat_number, customers_address.*')->distinct();
        $this->db->from('customers');
        $this->db->join('customers_address', 'customers_address.customer_id=customers.customer_id', 'inner');
        $this->db->where("(customers.vat_number IS NOT NULL and customers.vat_number <> '')", null);
        $data = $this->db->get()->result();

        return $data;
    }

    /**
     * Gets all customers.
     */
    public function get_all_customer()
    {
    	$this->db->select('customers.*, customers_address.invoicing_address_name')->distinct();
    	$this->db->from('customers');
    	$this->db->join('customers_address', 'customers_address.customer_id=customers.customer_id', 'left');
    	$data = $this->db->get()->result();

    	return $data;
    }

    /**
     * Gets all customers great than baseline customer_id
     */
    public function get_all_customer_great_than_baseline($array_where)
    {
    	$this->db->select('customers.*, customers_address.invoicing_address_name')->distinct();
    	$this->db->from('customers');
    	$this->db->join('customers_address', 'customers_address.customer_id=customers.customer_id', 'left');
    	//     	$this->db->where('customer_id >', $customer_id);
    	foreach ($array_where as $key => $value) {
    		if ($value != null) {
    			$this->db->where($key, $value);
    		} else {
    			$this->db->where($key);
    		}
    	}
    	$data = $this->db->get()->result();

    	return $data;
    }

    /**
     * Gets all customers great than customer_id .
     */
    public function get_all_customer_delete_by($array_where)
    {
    	$this->db->select('customers.*')->distinct();
    	$this->db->from('customers');
//     	$this->db->where('customer_id >', $customer_id);
    	foreach ($array_where as $key => $value) {
    		if ($value != null) {
    			$this->db->where($key, $value);
    		} else {
    			$this->db->where($key);
    		}
    	}
    	$this->db->where('status', APConstants::ON_FLAG);
    	$data = $this->db->get()->result();

    	return $data;
    }

    /**
     * Gets all customers add must require to verify cases.
     */
    public function get_customer_by($array_where)
    {
    	$this->db->select('*')->distinct();
    	$this->db->from('customers_address');

    	foreach ($array_where as $key => $value) {
    		if ($value != null) {
    			$this->db->where($key, $value);
    		} else {
    			$this->db->where($key);
    		}
    	}

    	$data = $this->db->get()->result();

    	return $data;
    }

    /**
     * Get list of customer by location
     */
    public function get_new_and_delete_customers_by_location($location_id,$status = null)
    {
    	$this->db->select('c.customer_id,c.email,c.status,c.created_date, c.deleted_date,
            p.postbox_id,postbox_code, p.location_available_id,p.postbox_code, p.name,p.company')->distinct();
    	$this->db->from('customers c');
        $this->db->join('postbox p', 'c.customer_id = p.customer_id', 'inner');
        $this->db->where('p.location_available_id', $location_id);

        $current_time = time();
        if($status == 1){
            //$this->db->where("from_unixtime(c.deleted_date, '%Y%m%d')= '".date("Ymd")."'", null);
            $this->db->where("c.deleted_date >'".($current_time - 86400)."'", null);
            $this->db->where("c.deleted_date <='".$current_time."'", null);
            $this->db->where('c.status', APConstants::ON_FLAG);
        }
        else{
            //$this->db->where("from_unixtime(c.created_date, '%Y%m%d')= '".date("Ymd")."'", null);
            $this->db->where("c.created_date >'".($current_time - 86400)."'", null);
            $this->db->where("c.created_date <='".$current_time."'", null);
            $this->db->where('c.status <> ', APConstants::ON_FLAG);
        }
        $this->db->group_by('c.customer_id');
        $data = $this->db->get()->result();
    	return $data;

    }

    /**
     * Get list customers have setting auto-trash
     */
    public function get_customer_setting_auto_trash()
    {
    	$this->db->select('c.customer_id,c.email,c.user_name, ps.postbox_id,ps.auto_trash_flag,ps.trash_after_day')->distinct();
    	$this->db->from('customers c');
        $this->db->join('postbox_settings ps', 'c.customer_id = ps.customer_id', 'inner');

        $this->db->where('ps.auto_trash_flag', APConstants::ON_FLAG);
        $this->db->where('ps.trash_after_day > 0', null);
        $this->db->where('c.auto_trash_flag',  APConstants::ON_FLAG);
        $this->db->where("(c.status <> '1' OR c.status IS NULL)",  null);

        $data = $this->db->get()->result();
    	return $data;

    }

     /**
     * #1161 create report cron job
     * 1. Count accounts deactivated because of failed setup process ( not activated account)
     *
     * @param unknown_type $array_where
     */
    public function count_accounts_deactivated_by_failed_setup_process($array_where)
    {
        $this->db->select('COUNT(DISTINCT(customers.customer_id)) AS total_record');
        $this->db->from('customers');
        $this->db->join('postbox p', 'p.customer_id = customers.customer_id', 'inner');
        $this->db->join('customers_address ca', 'ca.customer_id = customers.customer_id', 'left');
        $this->db->join('country ct', 'ct.id = ca.shipment_country', 'left');

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $result = $this->db->get()->row();
        return $result->total_record;
    }

     /**
     * #1161 create report cron job
     * 2.accounts deactivated due to failed payment
     *
     * @param unknown_type $array_where
     */
    public function count_accounts_deactivated_by_failed_payment($array_where)
    {
        $this->db->select('COUNT(DISTINCT(customers.customer_id)) AS total_record');
        $this->db->from('customers');
        $this->db->join('postbox p', 'p.customer_id = customers.customer_id', 'inner');
        $this->db->join('customers_address ca', 'ca.customer_id = customers.customer_id', 'left');
        $this->db->join('country ct', 'ct.id = ca.shipment_country', 'left');

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $result = $this->db->get()->row();
        return $result->total_record;
    }

     /**
     * #1161 create report cron job
     * 3. Accounts manually deactivated
     *
     * @param unknown_type $array_where
     */
    public function count_accounts_manually_deactivated($array_where)
    {
        $this->db->select('COUNT(DISTINCT(customers.customer_id)) AS total_record');
        $this->db->from('customers');
        $this->db->join('postbox p', 'p.customer_id = customers.customer_id', 'inner');
        $this->db->join('customers_address ca', 'ca.customer_id = customers.customer_id', 'left');
        $this->db->join('country ct', 'ct.id = ca.shipment_country', 'left');

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $result = $this->db->get()->row();
        return $result->total_record;
    }

     /**
     * #1161 create report cron job
     * 4. accounts (older 3 month) deleted automatically
     *
     * @param unknown_type $array_where
     */
    public function count_accounts_deleted_automatically_older_three_month($array_where)
    {
        $this->db->select('COUNT(DISTINCT(customers.customer_id)) AS total_record');
        $this->db->from('customers');
        $this->db->join('postbox p', 'p.customer_id = customers.customer_id', 'inner');
        $this->db->join('customers_address ca', 'ca.customer_id = customers.customer_id', 'left');
        $this->db->join('country ct', 'ct.id = ca.shipment_country', 'left');

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $result = $this->db->get()->row();
        return $result->total_record;
    }

    /**
     * #1161 create report cron job
     * 5.accounts (younger 3 month) deleted automatically
     *
     * @param unknown_type $array_where
     */
    public function count_accounts_deleted_automatically_younger_three_month($array_where)
    {
        $this->db->select('COUNT(DISTINCT(customers.customer_id)) AS total_record');
        $this->db->from('customers');
        $this->db->join('postbox p', 'p.customer_id = customers.customer_id', 'inner');
        $this->db->join('customers_address ca', 'ca.customer_id = customers.customer_id', 'left');
        $this->db->join('country ct', 'ct.id = ca.shipment_country', 'left');

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $result = $this->db->get()->row();
        return $result->total_record;
    }

    /**
     * #1161 create report cron job
     * 6.accounts deleted manually
     *
     * @param unknown_type $array_where
     */
    public function count_accounts_deleted_manually($array_where)
    {
        $this->db->select('COUNT(DISTINCT(customers.customer_id)) AS total_record');
        $this->db->from('customers');
        $this->db->join('postbox p', 'p.customer_id = customers.customer_id', 'inner');
        $this->db->join('customers_address ca', 'ca.customer_id = customers.customer_id', 'left');
        $this->db->join('country ct', 'ct.id = ca.shipment_country', 'left');

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $result = $this->db->get()->row();
        return $result->total_record;
    }

    /**
     * count customer number of partner
     */
    public function count_customer_partner_by($array_where){
        $this->db->select('COUNT(DISTINCT(customers.customer_id)) AS total_record');
        $this->db->from('customers');
        $this->db->join('postbox', 'postbox.customer_id = customers.customer_id', 'left');
        $this->db->join('partner_customers', 'partner_customers.customer_id = customers.customer_id', 'inner');
        $this->db->join('customer_product_settings cs1', "cs1.customer_id = customers.customer_id AND cs1.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs1.setting_key='postbox_name_flag'", 'left');
        $this->db->join('customer_product_settings cs2', "cs2.customer_id = customers.customer_id AND cs2.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs2.setting_key='name_comp_address_flag'", 'left');
        $this->db->join('customer_product_settings cs3', "cs3.customer_id = customers.customer_id AND cs3.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs3.setting_key='invoicing_address_completed'", 'left');
        $this->db->join('customer_product_settings cs4', "cs4.customer_id = customers.customer_id AND cs4.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs4.setting_key='city_address_flag'", 'left');
        $this->db->join('customer_product_settings cs5', "cs5.customer_id = customers.customer_id AND cs5.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs5.setting_key='email_confirm_flag'", 'left');

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $result = $this->db->get()->row();
        return $result->total_record;
    }

    /**
     * Gets list customers that sets automatic charge
     * @param type $array_where
     * @return type
     */
    public function getListAutomaticChargeCustomer(){
        $this->db->select('customers.customer_id, cs1.setting_value, cs1.alias02, cs1.alias03');
        $this->db->from('customers');
        $this->db->join('customer_settings cs1', "cs1.parent_customer_id = customers.customer_id AND cs1.setting_key='".APConstants::CUSTOMER_AUTOMATIC_CHARGE_SETTING."'", 'inner');
        $this->db->where('cs1.setting_value', APConstants::ON_FLAG);
        $this->db->where('customers.account_type', APConstants::ENTERPRISE_CUSTOMER);

        $this->db->where('cs1.alias02 > 0', null);
        $this->db->where('cs1.alias03 > 0', null);

        $result = $this->db->get()->result();
        return $result;
    }

    public function getCustomerInfoForPayOne($customer_id) {

        ci()->db->select('cs.customer_code, cs.email, csa.invoicing_address_name, csa.invoicing_company, ct.country_code' );
        ci()->db->from('customers cs');
        ci()->db->join('customers_address csa', 'csa.customer_id = cs.customer_id');
        ci()->db->join('country ct', 'ct.id = csa.invoicing_country');
        ci()->db->where('cs.customer_id', $customer_id);

        $result = ci()->db->get()->row();
        return $result;
    }

    /**
     * Gets customers and setup status.
     * @param type $array_where
     */
    public function get_customers_by($array_where){
        $this->db->select('customers.*, cs1.setting_value as postbox_name_flag, cs2.setting_value as name_comp_address_flag, cs3.setting_value as invoicing_address_completed, cs4.setting_value as city_address_flag, cs5.setting_value as email_confirm_flag ');
        $this->db->from('customers');
        $this->db->join('customer_product_settings cs1', "cs1.customer_id = customers.customer_id AND cs1.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs1.setting_key='postbox_name_flag'", 'left');
        $this->db->join('customer_product_settings cs2', "cs2.customer_id = customers.customer_id AND cs2.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs2.setting_key='name_comp_address_flag'", 'left');
        $this->db->join('customer_product_settings cs3', "cs3.customer_id = customers.customer_id AND cs3.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs3.setting_key='invoicing_address_completed'", 'left');
        $this->db->join('customer_product_settings cs4', "cs4.customer_id = customers.customer_id AND cs4.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs4.setting_key='city_address_flag'", 'left');
        $this->db->join('customer_product_settings cs5', "cs5.customer_id = customers.customer_id AND cs5.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs5.setting_key='email_confirm_flag'", 'left');

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }

        $result = $this->db->get()->result();
        return $result;
    }

    /**
     * count customer number of partner
     */
    public function count_customer_by($array_where){
        $this->db->select('COUNT(DISTINCT(customers.customer_id)) AS total_record');
        $this->db->from('customers');
        $this->db->join('postbox', 'postbox.customer_id = customers.customer_id', 'left');
        $this->db->join('customer_product_settings cs1', "cs1.customer_id = customers.customer_id AND cs1.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs1.setting_key='postbox_name_flag'", 'left');
        $this->db->join('customer_product_settings cs2', "cs2.customer_id = customers.customer_id AND cs2.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs2.setting_key='name_comp_address_flag'", 'left');
        $this->db->join('customer_product_settings cs3', "cs3.customer_id = customers.customer_id AND cs3.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs3.setting_key='invoicing_address_completed'", 'left');
        $this->db->join('customer_product_settings cs4', "cs4.customer_id = customers.customer_id AND cs4.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs4.setting_key='city_address_flag'", 'left');
        $this->db->join('customer_product_settings cs5', "cs5.customer_id = customers.customer_id AND cs5.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs5.setting_key='email_confirm_flag'", 'left');

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $result = $this->db->get()->row();
        return $result->total_record;
    }

    public function count_customers_with_or_statements($array_where, $or_where = [])
    {
        $this->db->select('COUNT(DISTINCT(customers.customer_id)) AS total_record');
        $this->db->from('customers');
        $this->db->join('postbox', 'postbox.customer_id = customers.customer_id', 'left');
        $this->db->join('customer_product_settings cs1', "cs1.customer_id = customers.customer_id AND cs1.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs1.setting_key='postbox_name_flag'", 'left');
        $this->db->join('customer_product_settings cs2', "cs2.customer_id = customers.customer_id AND cs2.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs2.setting_key='name_comp_address_flag'", 'left');
        $this->db->join('customer_product_settings cs3', "cs3.customer_id = customers.customer_id AND cs3.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs3.setting_key='invoicing_address_completed'", 'left');
        $this->db->join('customer_product_settings cs4', "cs4.customer_id = customers.customer_id AND cs4.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs4.setting_key='city_address_flag'", 'left');
        $this->db->join('customer_product_settings cs5', "cs5.customer_id = customers.customer_id AND cs5.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs5.setting_key='email_confirm_flag'", 'left');

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        foreach ($or_where as $key => $value) {
            if ($value != null) {
                $this->db->or_where($key, $value);
            } else {
                $this->db->or_where($key);
            }
        }
        $result = $this->db->get()->row();
        return $result->total_record;
    }

    public function get_customer_info($customer_id = NULL)
    {
        $customer_id = $customer_id ? $customer_id : APContext::getCustomerCodeLoggedIn();
        if (!$customer_id)
            return false;

        $this->db->select('t1.*, t2.invoicing_country,t2.invoice_address_verification_flag, t3.currency_sign, t3.currency_rate');
        $this->db->from('customers t1');
        $this->db->join('customers_address t2', 't2.customer_id = t1.customer_id', 'left');
        $this->db->join('currencies t3', 't3.currency_id = t1.currency_id', 'left');
        $this->db->where('t1.customer_id', $customer_id);

        $query = $this->db->get();
        if ($query->num_rows() != 0) {
            return $query->row();
        } else {
            return false;
        }
    }
}
