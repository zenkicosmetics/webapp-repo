<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author TienNH
 */
class invoice_summary_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('invoice_summary');
        $this->primary_key = 'id';
    }

    /**
     * Get tat ca cac customer dang bi pending fee den thoi diem hien tai.
     */
    public function get_all_customer_pending_fee()
    {
        $current_invoice_month = APUtils::getCurrentYear() . APUtils::getCurrentMonth();
        $this->db->select('customers.customer_id')->distinct();
        $this->db->from('customers');
        $this->db->join('invoice_summary', 'customers.customer_id = invoice_summary.customer_id');
        $this->db->where('customers.charge_fee_flag', APConstants::ON_FLAG);
        $this->db->where('invoice_summary.invoice_month <> ', $current_invoice_month);
        $this->db->where('customers.charge_fee_flag', '1');
        $this->db->where("((invoice_summary.payment_1st_flag IS NULL OR invoice_summary.payment_1st_flag = '0' OR invoice_summary.payment_2st_flag IS NULL OR invoice_summary.payment_2st_flag = '0'))", null);
        
        return $this->db->get()->result();
    }

    /**
     * Get next record id.
     * Don't use this method now
     */
    public function get_next_id()
    {
        $maxid = 0;
        $row = $this->db->query('SELECT MAX(id) AS `maxid` FROM `invoice_summary`')->row();
        if ($row) {
            $maxid = $row->maxid + 1;
        }
        return $maxid;
    }

    /**
     * Gets credit note of customer.
     *
     * @param unknown $customer_id
     */
    public function get_credit_note_summary($customer_id)
    {
        $stmt = "SELECT SUM( ROUND(total_invoice *(1 + vat), 2)) as total FROM invoice_summary where customer_id IN ({$customer_id}) AND total_invoice< 0";
        $row = $this->db->query($stmt)->row();

        if ($row) {
            return abs($row->total);
        }

        return 0;
    }

    /**
     * Gets credit note of customer.
     *
     * @param unknown $customer_id
     */
    public function get_credit_note_summary_openbalance_due($customer_id)
    {
        $target_month = APUtils::getCurrentMonthInvoice();
        $target_year = APUtils::getCurrentYearInvoice();
        $current_invoice_month = $target_year . $target_month;

        // $stmt = "SELECT SUM( ROUND(total_invoice *(1 + vat), 2)) as total FROM invoice_summary where customer_id IN ({$customer_id}) AND total_invoice < 0 and LEFT(invoice_month,6) < {$current_invoice_month}";
        $stmt = "SELECT SUM( ROUND(total_invoice *(1 + vat), 2)) as total FROM invoice_summary where customer_id IN ({$customer_id}) AND total_invoice < 0";
        $row = $this->db->query($stmt)->row();

        if ($row) {
            return abs($row->total);
        }

        return 0;
    }

    /**
     * Gets gross price of customer for current invoice month.
     *
     * @param unknown $customer_id
     * @param unknown $current_invoice_month
     */
    public function get_gross_price_of_customer($customer_id, $current_invoice_month)
    {
        $stmt = "SELECT SUM( ROUND(total_invoice *(1 + vat),2) ) as total FROM invoice_summary where customer_id IN ({$customer_id}) AND total_invoice > 0 AND LEFT(invoice_month,6) < '{$current_invoice_month}'";
        $row = $this->db->query($stmt)->row();

        if ($row) {
            return abs($row->total);
        }

        return 0;
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
    public function insert($data)
    {
        $invoice_month = $data ['invoice_month'];
        $customer_id = $data ['customer_id'];
        $vat = 0;
        if (!empty($data ['vat'])) {
            $vat = $data ['vat'];
        }

        // Check customer status
        $customer = APContext::getCustomerByID($customer_id);
        if (empty($customer) || $customer->status == APConstants::ON_FLAG) {
            // If this customer is deleted, only allow to add manual invoices
            if (isset($data['invoice_type']) && $data['invoice_type'] != '2') {
                return;
            }
        }

        if (isset($data['update_flag'])) {
            unset($data['update_flag']);
        }

        unset($data['location_id']);

        // Generate invoice code again
        // With each month and invoice_type = 1 (auto) we will have only one invoice_code
        if (!empty($data ['invoice_type']) && $data ['invoice_type'] == '1') {
            $check_invoice = parent::get_by_many(array(
                'customer_id' => $customer_id,
                'invoice_month' => $invoice_month,
                'invoice_type' => '1'
            ));
            if (!empty($check_invoice)) {
                $data ['invoice_code'] = $check_invoice->invoice_code;

                return parent::update_by_many(array(
                    'customer_id' => $customer_id,
                    'invoice_month' => $invoice_month,
                    'invoice_type' => '1'
                ), $data);
            }
        }

        return parent::insert($data);
    }

    /**
     * Update a record, specified by $key and $val.
     * The function accepts ghost parameters, fetched via func_get_args().
     * Those are:
     * 1. string `$key` The key to update with.
     * 2. string `$value` The value to match.
     * 3. array `$data` The data to update with.
     * The first two are used in the query in the where statement something
     * like:
     * <code>UPDATE {table} SET {$key}={$data} WHERE {$key}={$value}</code>
     *
     * @author Jamie Rumbelow
     * @return boolean
     */
    public function update_by_many($array_where, $data)
    {
        if (isset($array_where['update_flag']) || isset($data['update_flag'])) {
            unset($array_where['update_flag']);
            unset($data['update_flag']);
        }

        unset($data['location_id']);

        parent::update_by_many($array_where, $data);
    }

    public function sum_previous_storage_charge_fee_day($array_where)
    {
        $this->db->select('SUM(storing_letters_free_account) AS storing_letters_free_account');
        $this->db->select('SUM(storing_packages_free_account) AS storing_packages_free_account');
        $this->db->select('SUM(storing_letters_free_quantity) AS storing_letters_free_quantity');
        $this->db->select('SUM(storing_letters_free_netprice) AS storing_letters_free_netprice');
        $this->db->select('SUM(storing_packages_free_quantity) AS storing_packages_free_quantity');
        $this->db->select('SUM(storing_packages_free_netprice) AS storing_packages_free_netprice');

        $this->db->select('SUM(storing_letters_private_account) AS storing_letters_private_account');
        $this->db->select('SUM(storing_packages_private_account) AS storing_packages_private_account');
        $this->db->select('SUM(storing_letters_private_quantity) AS storing_letters_private_quantity');
        $this->db->select('SUM(storing_letters_private_netprice) AS storing_letters_private_netprice');
        $this->db->select('SUM(storing_packages_private_quantity) AS storing_packages_private_quantity');
        $this->db->select('SUM(storing_packages_private_netprice) AS storing_packages_private_netprice');

        $this->db->select('SUM(storing_letters_business_account) AS storing_letters_business_account');
        $this->db->select('SUM(storing_packages_business_account) AS storing_packages_business_account');
        $this->db->select('SUM(storing_letters_business_quantity) AS storing_letters_business_quantity');
        $this->db->select('SUM(storing_letters_business_netprice) AS storing_letters_business_netprice');
        $this->db->select('SUM(storing_packages_business_quantity) AS storing_packages_business_quantity');
        $this->db->select('SUM(storing_packages_business_netprice) AS storing_packages_business_netprice');

        // Search all data with input condition
        foreach ($array_where as $key => $value) {
            if ($value != '') {
                $this->db->where($key, $value);
            } else {
                $this->db->where($key);
            }
        }

        $row = $this->db->get($this->_table)->row();
        return $row;
    }

    public function updateTotalInvoice($yearMonth)
    {
        $sql = <<<SQL
        UPDATE invoice_summary
        inner join customers on invoice_summary.customer_id=customers.customer_id 
        SET total_invoice = (
                      IFNULL(free_postboxes_amount , 0)
                    + IFNULL(private_postboxes_amount , 0)
                    + IFNULL(business_postboxes_amount, 0)
                    + IFNULL(additional_private_postbox_amount, 0)
                    + IFNULL(additional_business_postbox_amount, 0)
                    + IFNULL(incomming_items_free_account , 0)
                    + IFNULL(incomming_items_private_account , 0)
                    + IFNULL(incomming_items_business_account, 0)
                    + IFNULL(envelope_scan_free_account, 0)
                    + IFNULL(envelope_scan_private_account, 0)
                    + IFNULL(envelope_scan_business_account, 0)
                    + IFNULL(item_scan_free_account, 0)
                    + IFNULL(item_scan_private_account, 0)
                    + IFNULL(item_scan_business_account, 0)
                    + IFNULL(storing_letters_free_account, 0)
                    + IFNULL(storing_letters_private_account, 0)
                    + IFNULL(storing_letters_business_account, 0)
                    + IFNULL(storing_packages_free_account, 0)
                    + IFNULL(storing_packages_private_account, 0)
                    + IFNULL(storing_packages_business_account, 0)
                    + IFNULL(additional_pages_scanning_free_amount, 0)
                    + IFNULL(additional_pages_scanning_private_amount, 0)
                    + IFNULL(additional_pages_scanning_business_amount, 0)
                    + IFNULL(direct_shipping_free_account, 0)
                    + IFNULL(direct_shipping_private_account, 0)
                    + IFNULL(direct_shipping_business_account, 0)
                    + IFNULL(collect_shipping_free_account, 0)
                    + IFNULL(collect_shipping_private_account, 0)
                    + IFNULL(collect_shipping_business_account, 0)
                    + IFNULL(api_access_amount, 0)
                    + IFNULL(own_location_amount, 0)
                    + IFNULL(touch_panel_own_location_amount, 0)
                    + IFNULL(own_mobile_app_amount, 0)
                    + IFNULL(clevver_subdomain_amount, 0)
                    + IFNULL(own_subdomain_amount, 0)
                    + (IFNULL(custom_declaration_outgoing_quantity_01, 0) * IFNULL(custom_declaration_outgoing_price_01, 0) )
                    + (IFNULL(custom_declaration_outgoing_quantity_02,0) * IFNULL(custom_declaration_outgoing_price_02, 0) )
                    )
                WHERE (customers.`status` is null OR customers.`status` <> 1) 
                    AND (
                      invoice_summary.invoice_type = 0 OR
                      invoice_summary.invoice_type IS NULL OR
                      invoice_summary.invoice_type = 1
                    ) 
                    AND SUBSTR( invoice_summary.invoice_month, 1, 6 ) = '{$yearMonth}'
SQL;
        $this->db->query($sql);

        return $this->db->affected_rows();
    }
    
    /**
     * Sum by partner
     * @param type $location_id
     * @param type $month
     * @param type $share_rev_flag
     * @return type
     */
    public function summary_by_partner($yearMonth, $partner_id)
    {
        // Select statment
        $stmt = "SELECT ";

        $stmt = $stmt . " SUM(free_postboxes_amount ) as free_postboxes_amount, SUM(free_postboxes_quantity) as free_postboxes_quantity, ";
        $stmt = $stmt . " SUM(private_postboxes_amount) as private_postboxes_amount, SUM(private_postboxes_quantity) as private_postboxes_quantity, ";
        $stmt = $stmt . " SUM(business_postboxes_amount) as business_postboxes_amount, SUM(business_postboxes_quantity) as business_postboxes_quantity, ";
        
        $stmt = $stmt . " SUM(incomming_items_free_quantity) as incomming_items_free_quantity, SUM(incomming_items_private_quantity) as incomming_items_private_quantity, SUM(incomming_items_business_quantity) as incomming_items_business_quantity, ";
        $stmt = $stmt . " SUM(envelope_scan_free_quantity) as envelope_scan_free_quantity, SUM(envelope_scan_private_quantity) as envelope_scan_private_quantity, SUM(envelope_scan_business_quantity) as envelope_scan_business_quantity, ";
        $stmt = $stmt . " SUM(item_scan_free_quantity) as item_scan_free_quantity, SUM(item_scan_private_quantity) as item_scan_private_quantity, SUM(item_scan_business_quantity) as item_scan_business_quantity, ";
        $stmt = $stmt . " SUM(additional_pages_scanning_free_quantity) as additional_pages_scanning_free_quantity, SUM(additional_pages_scanning_private_quantity) as additional_pages_scanning_private_quantity, SUM(additional_pages_scanning_business_quantity) as additional_pages_scanning_business_quantity, ";

        $stmt = $stmt . " SUM(storing_letters_free_quantity) as storing_letters_free_quantity, SUM(storing_letters_private_quantity) as storing_letters_private_quantity, SUM(storing_letters_business_quantity) as storing_letters_business_quantity, ";
        $stmt = $stmt . " SUM(storing_packages_free_quantity) as storing_packages_free_quantity, SUM(storing_packages_private_quantity) as storing_packages_private_quantity, SUM(storing_packages_business_quantity) as storing_packages_business_quantity, ";

        $stmt = $stmt . " SUM(custom_declaration_outgoing_quantity_01) as custom_declaration_outgoing_quantity_01, SUM(custom_declaration_outgoing_quantity_02) as custom_declaration_outgoing_quantity_02, ";
        $stmt = $stmt . " SUM(custom_declaration_outgoing_quantity_01 * custom_declaration_outgoing_price_01) as custom_declaration_outgoing_price_01, SUM(custom_declaration_outgoing_quantity_02 * custom_declaration_outgoing_price_02) as custom_declaration_outgoing_price_02, ";

        $stmt = $stmt . " SUM(direct_shipping_free_quantity + direct_shipping_private_quantity + direct_shipping_business_quantity) as direct_shipping_quantity, SUM(direct_shipping_free_account + direct_shipping_private_account + direct_shipping_business_account) as direct_shipping_account, ";
        $stmt = $stmt . " SUM(collect_shipping_free_quantity + collect_shipping_private_quantity + collect_shipping_business_quantity) as collect_shipping_quantity, SUM(collect_shipping_free_account + collect_shipping_private_account + collect_shipping_business_account) as collect_shipping_account, ";
        
        $stmt = $stmt . " SUM(incomming_items_free_account) as incomming_items_free_account, SUM(incomming_items_private_account) as incomming_items_private_account, SUM(incomming_items_business_account) as incomming_items_business_account, ";
        
        $stmt = $stmt . " SUM(envelope_scan_free_account) as envelope_scan_free_account, SUM(envelope_scan_private_account) as envelope_scan_private_account, SUM(envelope_scan_business_account) as envelope_scan_business_account, ";
        $stmt = $stmt . " SUM(item_scan_free_account) as item_scan_free_account, SUM(item_scan_private_account) as item_scan_private_account, SUM(item_scan_business_account) as item_scan_business_account, ";
        
        $stmt = $stmt . " SUM(additional_pages_scanning_free_amount) as additional_pages_scanning_free_amount, SUM(additional_pages_scanning_private_amount) as additional_pages_scanning_private_amount, SUM(additional_pages_scanning_business_amount) as additional_pages_scanning_business_amount, ";
        
        $stmt = $stmt . " SUM(storing_letters_free_account) as storing_letters_free_account, SUM(storing_letters_private_account) as storing_letters_private_account, SUM(storing_letters_business_account) as storing_letters_business_account, ";
        $stmt = $stmt . " SUM(storing_packages_free_account) as storing_packages_free_account, SUM(storing_packages_private_account) as storing_packages_private_account, SUM(storing_packages_business_account) as storing_packages_business_account, ";
        
        $stmt = $stmt . " MAX(free_postboxes_netprice) as free_postboxes_netprice, MAX(private_postboxes_netprice) as private_postboxes_netprice, MAX(business_postboxes_netprice) as business_postboxes_netprice,";
        
        $stmt = $stmt . " MAX(incomming_items_free_netprice) as incomming_items_free_netprice, MAX(incomming_items_private_netprice) as incomming_items_private_netprice, MAX(incomming_items_business_netprice) as incomming_items_business_netprice,";
        
        $stmt = $stmt . " MAX(envelope_scan_free_netprice) as envelope_scan_free_netprice, MAX(envelope_scan_private_netprice) as envelope_scan_private_netprice, MAX(envelope_scan_business_netprice) as envelope_scan_business_netprice,";
        $stmt = $stmt . " MAX(item_scan_free_netprice) as item_scan_free_netprice, MAX(item_scan_private_netprice) as item_scan_private_netprice, MAX(item_scan_business_netprice) as item_scan_business_netprice,";
        
        $stmt = $stmt . " MAX(direct_shipping_free_netprice) as direct_shipping_free_netprice, MAX(collect_shipping_private_netprice) as collect_shipping_private_netprice, MAX(direct_shipping_business_netprice) as direct_shipping_business_netprice,";
        $stmt = $stmt . " MAX(collect_shipping_free_netprice) as collect_shipping_free_netprice, MAX(collect_shipping_private_netprice) as collect_shipping_private_netprice, MAX(collect_shipping_business_netprice) as collect_shipping_business_netprice,";
        
        $stmt = $stmt . " MAX(storing_letters_free_netprice) as storing_letters_free_netprice, MAX(storing_letters_private_netprice) as storing_letters_private_netprice, MAX(storing_letters_business_netprice) as storing_letters_business_netprice,";
        $stmt = $stmt . " MAX(storing_packages_free_netprice) as storing_packages_free_netprice, MAX(storing_packages_private_netprice) as storing_packages_private_netprice, MAX(storing_packages_business_netprice) as storing_packages_business_netprice,";
        
        $stmt = $stmt . " MAX(additional_pages_scanning_free_netprice) as additional_pages_scanning_free_netprice, MAX(additional_pages_scanning_private_netprice) as additional_pages_scanning_private_netprice, MAX(additional_pages_scanning_business_netprice) as additional_pages_scanning_business_netprice,";
        
        $stmt = $stmt . " SUM(total_invoice) as total_invoice,";
        
        $stmt = $stmt . " SUM(forwarding_charges_fee) as forwarding_charges_fee,";
        $stmt = $stmt . " SUM(forwarding_charges_postal) as forwarding_charges_postal,";
        
        // share rev amount
        $stmt = $stmt . " SUM(((free_postboxes_amount) - (free_postboxes_amount * partner_customers.customer_discount/100))* partner_customers.rev_share_ad/100 ) as free_postboxes_amount_share,";
        $stmt = $stmt . " SUM(((private_postboxes_amount) - (private_postboxes_amount * partner_customers.customer_discount/100))* partner_customers.rev_share_ad/100 ) as private_postboxes_amount_share, ";
        $stmt = $stmt . " SUM(((business_postboxes_amount) - (business_postboxes_amount * partner_customers.customer_discount/100))* partner_customers.rev_share_ad/100 ) as business_postboxes_amount_share, ";
        
        $stmt = $stmt . " SUM(((incomming_items_free_account) - (incomming_items_free_account * partner_customers.customer_discount/100))* partner_customers.rev_share_ad/100 ) as incomming_items_free_account_share,";
        $stmt = $stmt . " SUM(((incomming_items_private_account) - (incomming_items_private_account * partner_customers.customer_discount/100))* partner_customers.rev_share_ad/100 ) as incomming_items_private_account_share, ";
        $stmt = $stmt . " SUM(((incomming_items_business_account) - (incomming_items_business_account * partner_customers.customer_discount/100))* partner_customers.rev_share_ad/100 ) as incomming_items_business_account_share, ";
        
        $stmt = $stmt . " SUM(((envelope_scan_free_account) - (envelope_scan_free_account * partner_customers.customer_discount/100))* partner_customers.rev_share_ad/100 ) as envelope_scan_free_account_share, ";
        $stmt = $stmt . " SUM(((envelope_scan_private_account) - (envelope_scan_private_account * partner_customers.customer_discount/100))* partner_customers.rev_share_ad/100 ) as envelope_scan_private_account_share, ";
        $stmt = $stmt . " SUM(((envelope_scan_business_account) - (envelope_scan_business_account * partner_customers.customer_discount/100))* partner_customers.rev_share_ad/100 ) as envelope_scan_business_account_share, ";
        
        $stmt = $stmt . " SUM(((item_scan_free_account) - (item_scan_free_account * partner_customers.customer_discount/100))* partner_customers.rev_share_ad/100 ) as item_scan_free_account_share, ";
        $stmt = $stmt . " SUM(((item_scan_private_account) - (item_scan_private_account * partner_customers.customer_discount/100))* partner_customers.rev_share_ad/100 ) as item_scan_private_account_share, ";
        $stmt = $stmt . " SUM(((item_scan_business_account) - (item_scan_business_account * partner_customers.customer_discount/100))* partner_customers.rev_share_ad/100 ) as item_scan_business_account_share, ";
        
        $stmt = $stmt . " SUM(((additional_pages_scanning_free_amount) - (additional_pages_scanning_free_amount * partner_customers.customer_discount/100)) * partner_customers.rev_share_ad/100 ) as additional_pages_scanning_free_amount_share,";
        $stmt = $stmt . " SUM(((additional_pages_scanning_private_amount) - (additional_pages_scanning_private_amount * partner_customers.customer_discount/100)) * partner_customers.rev_share_ad/100 ) as additional_pages_scanning_private_amount_share,";
        $stmt = $stmt . " SUM(((additional_pages_scanning_business_amount) - (additional_pages_scanning_business_amount * partner_customers.customer_discount/100)) * partner_customers.rev_share_ad/100 ) as additional_pages_scanning_business_amount_share, ";
        
        $stmt = $stmt . " SUM(((storing_letters_free_account) - (storing_letters_free_account * partner_customers.customer_discount/100)) * partner_customers.rev_share_ad/100 ) as storing_letters_free_account_share,";
        $stmt = $stmt . " SUM(((storing_letters_private_account) - (storing_letters_private_account * partner_customers.customer_discount/100)) * partner_customers.rev_share_ad/100 ) as storing_letters_private_account_share,";
        $stmt = $stmt . " SUM(((storing_letters_business_account) - (storing_letters_business_account * partner_customers.customer_discount/100)) * partner_customers.rev_share_ad/100 ) as storing_letters_business_account_share, ";
        
        $stmt = $stmt . " SUM(((storing_packages_free_account) - (storing_packages_free_account * partner_customers.customer_discount/100)) * partner_customers.rev_share_ad/100 ) as storing_packages_free_account_share,";
        $stmt = $stmt . " SUM(((storing_packages_private_account) - (storing_packages_private_account * partner_customers.customer_discount/100)) * partner_customers.rev_share_ad/100 ) as storing_packages_private_account_share, ";
        $stmt = $stmt . " SUM(((storing_packages_business_account) - (storing_packages_business_account * partner_customers.customer_discount/100)) * partner_customers.rev_share_ad/100 ) as storing_packages_business_account_share, ";
        
        $stmt = $stmt . " SUM(((forwarding_charges_fee) - (forwarding_charges_fee * partner_customers.customer_discount/100)) * partner_customers.rev_share_ad/100 ) as forwarding_charges_fee_share,";
        $stmt = $stmt . " SUM(((forwarding_charges_postal) - (forwarding_charges_postal * partner_customers.customer_discount/100)) * partner_customers.rev_share_ad/100 ) as forwarding_charges_postal_share,";
        
        $stmt = $stmt . " SUM(custom_declaration_outgoing_quantity_01 * custom_declaration_outgoing_price_01 * partner_customers.rev_share_ad/100) as custom_declaration_outgoing_price_01_share, ";
        $stmt = $stmt . " SUM(custom_declaration_outgoing_quantity_02 * custom_declaration_outgoing_price_02 * partner_customers.rev_share_ad/100) as custom_declaration_outgoing_price_02_share, ";
        
        // discount amount.
        $stmt = $stmt . " SUM(free_postboxes_amount * partner_customers.customer_discount/100) as free_postboxes_amount_discount,";
        $stmt = $stmt . " SUM(private_postboxes_amount* partner_customers.customer_discount/100 ) as private_postboxes_amount_discount, ";
        $stmt = $stmt . " SUM(business_postboxes_amount* partner_customers.customer_discount/100 ) as business_postboxes_amount_discount, ";
        
        $stmt = $stmt . " SUM(incomming_items_free_account* partner_customers.customer_discount/100 ) as incomming_items_free_account_discount,";
        $stmt = $stmt . " SUM(incomming_items_private_account* partner_customers.customer_discount/100 ) as incomming_items_private_account_discount, ";
        $stmt = $stmt . " SUM(incomming_items_business_account* partner_customers.customer_discount/100 ) as incomming_items_business_account_discount, ";
        
        $stmt = $stmt . " SUM(envelope_scan_free_account* partner_customers.customer_discount/100) as envelope_scan_free_account_discount, ";
        $stmt = $stmt . " SUM(envelope_scan_private_account* partner_customers.customer_discount/100 ) as envelope_scan_private_account_discount, ";
        $stmt = $stmt . " SUM(envelope_scan_business_account* partner_customers.customer_discount/100 ) as envelope_scan_business_account_discount, ";
        
        $stmt = $stmt . " SUM(item_scan_free_account * partner_customers.customer_discount/100) as item_scan_free_account_discount, ";
        $stmt = $stmt . " SUM(item_scan_private_account * partner_customers.customer_discount/100) as item_scan_private_account_discount, ";
        $stmt = $stmt . " SUM(item_scan_business_account * partner_customers.customer_discount/100) as item_scan_business_account_discount, ";
        
        $stmt = $stmt . " SUM(additional_pages_scanning_free_amount * partner_customers.customer_discount/100) as additional_pages_scanning_free_amount_discount,";
        $stmt = $stmt . " SUM(additional_pages_scanning_private_amount * partner_customers.customer_discount/100) as additional_pages_scanning_private_amount_discount,";
        $stmt = $stmt . " SUM(additional_pages_scanning_business_amount * partner_customers.customer_discount/100) as additional_pages_scanning_business_amount_discount, ";
        
        $stmt = $stmt . " SUM(storing_letters_free_account * partner_customers.customer_discount/100) as storing_letters_free_account_discount,";
        $stmt = $stmt . " SUM(storing_letters_private_account * partner_customers.customer_discount/100) as storing_letters_private_account_discount,";
        $stmt = $stmt . " SUM(storing_letters_business_account * partner_customers.customer_discount/100) as storing_letters_business_account_discount, ";
        
        $stmt = $stmt . " SUM(storing_packages_free_account * partner_customers.customer_discount/100) as storing_packages_free_account_discount,";
        $stmt = $stmt . " SUM(storing_packages_private_account * partner_customers.customer_discount/100) as storing_packages_private_account_discount, ";
        $stmt = $stmt . " SUM(storing_packages_business_account * partner_customers.customer_discount/100) as storing_packages_business_account_discount, ";
        
        $stmt = $stmt . " SUM(forwarding_charges_fee * partner_customers.customer_discount/100) as forwarding_charges_fee_discount,";
        $stmt = $stmt . " SUM(forwarding_charges_postal * partner_customers.customer_discount/100) as forwarding_charges_postal_discount,";
        
        $stmt = $stmt . " SUM(custom_declaration_outgoing_quantity_01 * custom_declaration_outgoing_price_01  * partner_customers.rev_share_ad/100) as custom_declaration_outgoing_price_01_discount, ";
        $stmt = $stmt . " SUM(custom_declaration_outgoing_quantity_02 * custom_declaration_outgoing_price_02  * partner_customers.rev_share_ad/100) as custom_declaration_outgoing_price_02_discount ";

        
        // FROM statement
        $stmt = $stmt . " FROM invoice_summary";
        $stmt = $stmt . " INNER JOIN customers ON customers.customer_id = invoice_summary.customer_id";
        $stmt = $stmt . " INNER JOIN partner_customers ON customers.customer_id = partner_customers.customer_id";

        // Where statement
        $stmt = $stmt." WHERE 1= 1 ";
        $stmt = $stmt . " AND partner_customers.partner_id = '{$partner_id}' ";
        $stmt = $stmt . " AND partner_customers.end_flag = 0 ";
        $stmt = $stmt . " AND substr(invoice_month, 1,6)  = '{$yearMonth}'";

        $row = $this->db->query($stmt)->row();
        return $row;
    }

	/**
     * Gets total postbox fee of customer.
     * @param type $customer_id
     */
    public function get_postbox_fee_by($customer_id, $yearMonth){
        $this->db->select("SUM(free_postboxes_amount) as free_postboxes_amount");
        $this->db->select("SUM(private_postboxes_amount) as private_postboxes_amount");
        $this->db->select("SUM(business_postboxes_amount) as business_postboxes_amount");
        $this->db->select("SUM(total_invoice) as total_invoice");
        $this->db->where('customer_id IN ('.$customer_id.')', null);
        $this->db->where('invoice_type <> 2', null);
        $this->db->where("LEFT(invoice_month, 6)='".$yearMonth."'", null);
        
        $result = $this->db->get($this->_table)->row();
        return  $result;
    }
    
    /**
     * get account paging.
     * 
     * @param type $yearmonth
     * @param type $list_id
     * @param type $enquiry
     * @param type $start
     * @param type $limit
     * @param type $sort_column
     * @param type $sort_type
     * @param type $group_by
     */
    public function get_account_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC', $group_by=''){
        // count all record.
        $total_record = $this->count_account_paging($array_where);
        if($total_record == 0){
            return array (
                    "total" => 0,
                    "data" => array ()
            );
        }
        
        $this->db->select("invoice_summary.*, c.user_name, c.email");
        $this->db->join('customers c', 'c.customer_id=invoice_summary.customer_id', 'left');
        
        // Search all data with input condition
        foreach ( $array_where as $key => $value ) {
            $this->db->where($key, $value);
        }

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
    
    public function count_account_paging($array_where){
        $this->db->select("count(*) as total");
        $this->db->join('customers c', 'c.customer_id=invoice_summary.customer_id', 'left');
        
        // Search all data with input condition
        foreach ( $array_where as $key => $value ) {
            $this->db->where($key, $value);
        }
        $data = $this->db->get($this->_table)->row();
        return $data->total;
    }
    
    /**
     * Gets list paging of credit note.
     */
    public function get_creditnote_paging($array_where, $start = 0, $limit = 10, $sort_column, $sort_type = 'ASC', $group_by = '') {
        // Count all record with input condition
        $total_record = $this->count_by_many($array_where);
        if ($total_record == 0) {
            return array(
                "total" => 0,
                "data" => array()
            );
        }
        
        $this->db->select('invoice_summary.*, customers.customer_code, customers.email, customers.user_name, customers.status');
        $this->db->select('customers.activated_flag, customers.deactivated_type');
        $this->db->join('customers', 'customers.customer_id = invoice_summary.customer_id', 'inner');
        
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

}