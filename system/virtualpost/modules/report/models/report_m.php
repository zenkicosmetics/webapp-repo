<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author TienNH
 */
class report_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * ---------------------------------------------------------------------------------------------- Accounting report
     * -----------------------------------------------------------------------------------------------
     */

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
    public function get_account_report_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_account_report_paging($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('invoice_summary.*');
        $this->db->select('p.name, p.company');
        $this->db->select('customers.customer_id');
        $this->db->select('customers.email');
        $this->db->select('vat.reverse_charge');
        $this->db->from('invoice_summary');
        $this->db->join('customers', 'customers.customer_id = invoice_summary.customer_id', 'inner');
        $this->db->join('postbox p', 'p.customer_id = customers.customer_id', 'inner');
        $this->db->join('vat_case vat', 'vat.vat_id = invoice_summary.vat_case', 'left');
        // Search all data with input condition
        $this->db->where('p.is_main_postbox', '1');
        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        if ($limit > 0) {
            $this->db->limit($limit);
        }
        if (!empty($sort_column)) {
            if ($sort_column == 'name') {
                $this->db->order_by('p.name', $sort_type);
            } else if ($sort_column == 'company') {
                $this->db->order_by('p.company', $sort_type);
            } else {
                $this->db->order_by($sort_column, $sort_type);
            }
        }
        if ($limit > 0) {
            $data = $this->db->get(null, $limit, $start)->result();
        } else {
            $data = $this->db->get(null)->result();
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
    public function count_account_report_paging($array_where)
    {
        $this->db->select('COUNT(invoice_summary.id) AS total_record');
        $this->db->from('invoice_summary');
        $this->db->join('customers', 'customers.customer_id = invoice_summary.customer_id', 'inner');
        $this->db->join('postbox p', 'p.customer_id = customers.customer_id', 'inner');
        $this->db->where('p.is_main_postbox', '1');
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
     * ---------------------------------------------------------------------------------------------- Invoice report
     * -----------------------------------------------------------------------------------------------
     */

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
    public function get_invoices_report_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_invoices_report_paging($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }


        $this->db->select('invoice_summary.*');
        $this->db->select('p.name, p.company');
        $this->db->select('customers.customer_code');
        $this->db->select('customers.email');
        $this->db->select('country.country_name');
        $this->db->select('customers_address.eu_member_flag');
        $this->db->select('vat.reverse_charge');
        $this->db->select('customers.vat_number');
        $this->db->select('country.country_code');
        $this->db->select('country.id as country_id');

        $this->db->select('(SELECT count(postbox_id) FROM postbox WHERE postbox.customer_id = invoice_summary.customer_id) as total_postbox');
        $this->db->from('invoice_summary');
        $this->db->join('customers', 'customers.customer_id = invoice_summary.customer_id', 'left');
        $this->db->join('customers_address', 'customers.customer_id = customers_address.customer_id', 'left');
        $this->db->join('country', 'customers_address.invoicing_country = country.id', 'left');
        $this->db->join('postbox p', 'p.customer_id = customers.customer_id AND p.is_main_postbox = 1', 'left'); // turning seach in sql 
        $this->db->join('vat_case vat', 'vat.vat_id = invoice_summary.vat_case', 'left');

        // Search all data with input condition
        $this->db->where('invoice_summary.total_invoice <> 0');
        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        
        $this->db->group_by('invoice_summary.id');
        
        if (!empty($sort_column)) {
                $this->db->order_by($sort_column, $sort_type);

        }

        if ($limit > 0) {
            $this->db->limit($limit);
            $data = $this->db->get(null, $limit, $start)->result();
        } else {
            $data = $this->db->get(null)->result();
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
    public function count_invoices_report_paging($array_where)
    {
        // turning seach in sql 
        $sql = 'SELECT COUNT(invoice_sum.id) AS total_record 
                FROM  (SELECT (invoice_summary.id) 
                        FROM invoice_summary
                        LEFT JOIN customers ON customers.customer_id = invoice_summary.customer_id
                        LEFT JOIN customers_address ON customers.customer_id = customers_address.customer_id
                        LEFT JOIN country ON customers_address.invoicing_country = country.id
                        LEFT JOIN postbox p ON p.customer_id = customers.customer_id AND p.is_main_postbox = 1
                        LEFT JOIN vat_case vat ON vat.vat_id = invoice_summary.vat_case WHERE ';
        
                        foreach ($array_where as $key => $value) {
                            if ($value != null) {
                                $sql .=  $key . '=' .$value.  ' AND ';
                            } else {
                                 $sql .=  $key . ' AND ';
                            }
                         }
                     $sql .= ' invoice_summary.total_invoice <> 0 ' ;
                     $sql .= ' GROUP BY  invoice_summary.id) invoice_sum ';
                     
        // Query sql with pramameter "Update"
        $query = $this->db->query($sql);
        $row = $query->row();
        
//        $result = $this->db->get()->row();
        return $row->total_record;
    }

    /**
     * Count customer
     *
     * @param unknown_type $array_where
     */
    public function count_invoices_report_multivat($customer_id)
    {
        $this->db->select('COUNT(Distinct invoice_summary.vat) AS total_record');
        $this->db->from('invoice_summary');
        $this->db->join('customers', 'customers.customer_id = invoice_summary.customer_id', 'left');
        $this->db->where('customers.customer_id', $customer_id);
        $result = $this->db->get()->row();
        return $result->total_record;
    }

    /**
     * Monthy report by location
     */
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
    public function get_monthy_report_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Build query get all customer by location
        $sub_query = $this->get_all_customer_bylocation();

        // Count all record with input condition
        $total_record = $this->count_monthy_report_paging($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('SUM(invoice_summary.total_invoice) as total_invoice, invoice_summary.invoice_month');
        $this->db->select('TEMP.location_available_id, TEMP.location_name');
        $this->db->from('invoice_summary');
        $this->db->join('(' . $sub_query . ') TEMP', 'TEMP.customer_id = invoice_summary.customer_id', 'INNER');

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }

        // Group by
        $this->db->group_by(array(
            'TEMP.location_available_id',
            'TEMP.location_name',
            'invoice_summary.invoice_month'
        ));
        if ($limit != -1) {
            $this->db->limit($limit, $start);
        }
        if (!empty($sort_column)) {
            $this->db->order_by($sort_column, $sort_type);
        }
        $data = $this->db->get()->result();

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
    public function count_monthy_report_paging($array_where)
    {
        $sub_query = $this->get_all_customer_bylocation();
        $query = ' SELECT COUNT(distinct invoice_summary.invoice_month, TEMP.location_available_id) as total_record FROM invoice_summary INNER JOIN';
        $query .= ' (' . $sub_query . ') TEMP ON TEMP.customer_id = invoice_summary.customer_id';
        $result = $this->db->query($query)->row();
        return $result->total_record;
    }

    /**
     * Build sub query get all customer by location
     */
    private function get_all_customer_bylocation()
    {
        $sub_query = ' SELECT distinct C.customer_id, P.location_available_id, L.location_name';
        $sub_query .= ' FROM postbox P INNER JOIN location L ON P.location_available_id = L.id';
        $sub_query .= ' INNER JOIN customers C ON C.customer_id = P.customer_id';
        return $sub_query;
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
    public function get_open_balance_report_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC') {
        // Count all record with input condition
        $total_record = $this->count_open_balance_report_paging($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }
        #1323 BUG: multiple entries in open balance list 
        $this->db->select('customers.customer_id')->distinct();
        $this->db->select('p.name, p.company');
        $this->db->select('customers.*');
        $this->db->select('country.country_name');
        $this->db->select('co.open_balance_due, co.open_balance_month');
        $this->db->select('payment.card_charge_flag');

        $this->db->from('customers');
        $this->db->join('customers_address', 'customers.customer_id = customers_address.customer_id', 'inner');
        $this->db->join('country', 'customers_address.shipment_country = country.id', 'inner');
        $this->db->join('postbox p', 'p.customer_id = customers.customer_id', 'inner');
        $this->db->join('customer_openbalance co', 'co.customer_id = customers.customer_id', 'left');
        $this->db->join('payment', 'payment.customer_id = customers.customer_id and payment.primary_card=1', 'left');
		// #1295 improve filter in open balance report 
		//$this->db->join('invoice_summary', 'customers.customer_id = invoice_summary.customer_id', 'left');

        $this->db->join('customer_product_settings cs1', "cs1.customer_id = customers.customer_id AND cs1.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs1.setting_key='postbox_name_flag'", 'left');
        $this->db->join('customer_product_settings cs2', "cs2.customer_id = customers.customer_id AND cs2.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs2.setting_key='name_comp_address_flag'", 'left');
        $this->db->join('customer_product_settings cs3', "cs3.customer_id = customers.customer_id AND cs3.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs3.setting_key='invoicing_address_completed'", 'left');
        $this->db->join('customer_product_settings cs4', "cs4.customer_id = customers.customer_id AND cs4.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs4.setting_key='city_address_flag'", 'left');
        $this->db->join('customer_product_settings cs5', "cs5.customer_id = customers.customer_id AND cs5.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs5.setting_key='email_confirm_flag'", 'left');
        $this->db->join('customer_product_settings cs6', "cs6.customer_id = customers.customer_id AND cs6.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs6.setting_key='shipping_address_completed'", 'left');

        // Search all data with input condition
        $this->db->where('p.is_main_postbox', '1');
		//#1323 BUG: multiple entries in open balance list 
        $this->db->where('p.first_location_flag', '1'); 
        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        if (!empty($sort_column) && $sort_column != 'open_balance') {
            if ($sort_column == 'name') {
                $this->db->order_by('p.name', $sort_type);
            } else if ($sort_column == 'company') {
                $this->db->order_by('p.company', $sort_type);
            } else {
                $this->db->order_by($sort_column, $sort_type);
            }
        }
        if ($limit > 0) {
            $data = $this->db->get(null, $limit, $start)->result();
        } else {
            $data = $this->db->get(null)->result();
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
    public function count_open_balance_report_paging($array_where) {
        // #1323 BUG: multiple entries in open balance list 
        $this->db->select('customers.customer_id')->distinct();
        $this->db->select('p.name, p.company');
        $this->db->select('customers.*');
        $this->db->select('country.country_name');
        $this->db->select('co.open_balance_due, co.open_balance_month');
        $this->db->select('payment.card_charge_flag');

        $this->db->from('customers');
        $this->db->join('customers_address', 'customers.customer_id = customers_address.customer_id', 'inner');
        $this->db->join('country', 'customers_address.shipment_country = country.id', 'inner');
        $this->db->join('postbox p', 'p.customer_id = customers.customer_id', 'inner');
        $this->db->join('customer_openbalance co', 'co.customer_id = customers.customer_id', 'left');
        $this->db->join('payment', 'payment.customer_id = customers.customer_id and payment.primary_card=1', 'left');
        // #1295 improve filter in open balance report 
        $this->db->join('invoice_summary', 'customers.customer_id = invoice_summary.customer_id', 'left');
        
        $this->db->join('customer_product_settings cs1', "cs1.customer_id = customers.customer_id AND cs1.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs1.setting_key='postbox_name_flag'", 'left');
        $this->db->join('customer_product_settings cs2', "cs2.customer_id = customers.customer_id AND cs2.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs2.setting_key='name_comp_address_flag'", 'left');
        $this->db->join('customer_product_settings cs3', "cs3.customer_id = customers.customer_id AND cs3.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs3.setting_key='invoicing_address_completed'", 'left');
        $this->db->join('customer_product_settings cs4', "cs4.customer_id = customers.customer_id AND cs4.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs4.setting_key='city_address_flag'", 'left');
        $this->db->join('customer_product_settings cs5', "cs5.customer_id = customers.customer_id AND cs5.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs5.setting_key='email_confirm_flag'", 'left');
        $this->db->join('customer_product_settings cs6', "cs6.customer_id = customers.customer_id AND cs6.product_id=".APConstants::CLEVVERMAIL_PRODUCT." AND cs6.setting_key='shipping_address_completed'", 'left');

        // Search all data with input condition
        $this->db->where('p.is_main_postbox', '1');
        //#1323 BUG: multiple entries in open balance list 
        $this->db->where('p.first_location_flag', '1'); 
        
        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        $result = $this->db->get()->num_rows();
        return $result;
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
    public function get_open_balance_for_payment($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_open_balance_for_payment($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $this->db->select('p.name, p.company');
        $this->db->select('customers.*');
        $this->db->select('country.country_name');
        // $this->db->select('co.open_balance_due, co.open_balance_month');

        $this->db->from('customers');
        $this->db->join('customers_address', 'customers.customer_id = customers_address.customer_id', 'inner');
        $this->db->join('country', 'customers_address.shipment_country = country.id', 'inner');
        $this->db->join('postbox p', 'p.customer_id = customers.customer_id', 'inner');

        // Search all data with input condition
        $this->db->where('p.is_main_postbox', '1');
        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }
        if (!empty($sort_column) && $sort_column != 'open_balance') {
            if ($sort_column == 'name') {
                $this->db->order_by('p.name', $sort_type);
            } else if ($sort_column == 'company') {
                $this->db->order_by('p.company', $sort_type);
            } else {
                $this->db->order_by($sort_column, $sort_type);
            }
        }
        if ($limit > 0) {
            $data = $this->db->get(null, $limit, $start)->result();
        } else {
            $data = $this->db->get(null)->result();
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
    public function count_open_balance_for_payment($array_where)
    {
        $this->db->select('COUNT(customers.customer_id) AS total_record');

        $this->db->from('customers');
        $this->db->join('customers_address', 'customers.customer_id = customers_address.customer_id', 'inner');
        $this->db->join('country', 'customers_address.shipment_country = country.id', 'inner');
        $this->db->join('postbox p', 'p.customer_id = customers.customer_id', 'inner');

        $this->db->where('p.is_main_postbox', '1');
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
    public function get_transaction_report_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC')
    {
        // Count all record with input condition
        $total_record = $this->count_transaction_report_paging($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }

        $query = ' SELECT tran_hist.*, p.name, p.company, customers.customer_id, customers.email';
        $query = $query . " FROM (SELECT id, txid, FROM_UNIXTIME(booking_date,'%Y%m%d') AS tran_date, customer_id, amount, txaction, '1' tran_type FROM payone_transaction_hist UNION SELECT id, tran_id, tran_date, customer_id, tran_amount, status, '2' tran_type FROM external_tran_hist) tran_hist";
        $query = $query . ' INNER JOIN customers ON customers.customer_id = tran_hist.customer_id';
        $query = $query . ' INNER JOIN postbox p ON p.customer_id = customers.customer_id';
        $query = $query . " WHERE p.is_main_postbox = '1' ";

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $query = $query . ' AND ' . $key . "='" . $value . "'";
            } else {
                $query = $query . ' AND ' . $key;
            }
        }

        if (!empty($sort_column)) {
            if ($sort_column == 'name') {
                $query = $query . ' ORDER BY p.name ' . $sort_type;
            } else if ($sort_column == 'company') {
                $query = $query . ' ORDER BY p.company ' . $sort_type;
            } else {
                $query = $query . ' ORDER BY tran_hist.tran_date ' . $sort_type;
            }
        }
        if ($limit > 0) {
            $query = $query . ' LIMIT ' . $start . ', ' . $limit;
        }

        $data = $this->db->query($query)->result();

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
    public function count_transaction_report_paging($array_where)
    {
        $query = ' SELECT COUNT(tran_hist.id) AS total_record';
        $query = $query . " FROM (SELECT id, txid, FROM_UNIXTIME(booking_date,'%Y%m%d') AS tran_date, customer_id, amount, txaction FROM payone_transaction_hist UNION SELECT id, tran_id, tran_date, customer_id, tran_amount, status FROM external_tran_hist) tran_hist";
        $query = $query . ' INNER JOIN customers ON customers.customer_id = tran_hist.customer_id';
        $query = $query . ' INNER JOIN postbox p ON p.customer_id = customers.customer_id';
        $query = $query . " WHERE p.is_main_postbox = '1' ";

        foreach ($array_where as $key => $value) {
            if ($value != null) {
                $query = $query . ' AND ' . $key . "='" . $value . "'";
            } else {
                $query = $query . ' AND ' . $key;
            }
        }

        $result = $this->db->query($query)->row();
        return $result->total_record;
    }
}