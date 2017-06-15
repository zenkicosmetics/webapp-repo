<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author TienNH
 */
class invoice_summary_by_location_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('invoice_summary_by_location');
        $this->primary_key = 'id';
    }

    /**
     * Summary by price report by location.
     *
     * @param unknown_type $location_id
     */
    public function summary_by_location($location_id, $month = '', $share_rev_flag =false)
    {
        // Select statment
        $stmt = "SELECT ";

        $stmt = $stmt . " SUM(free_postboxes_amount) as free_postboxes_amount, SUM(free_postboxes_quantity) as free_postboxes_quantity, ";
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
        $stmt = $stmt . " SUM(forwarding_charges_postal) as forwarding_charges_postal";
        
        // FROM statement
        $stmt = $stmt . " FROM invoice_summary_by_location";
        $stmt = $stmt . " INNER JOIN customers ON customers.customer_id = invoice_summary_by_location.customer_id";

        // Where statement
        $stmt = $stmt." WHERE customers.account_type = 4 ";
        
        if($location_id){
            $stmt = $stmt . " AND location_id = {$location_id} ";
        }
        if($share_rev_flag){
            $stmt = $stmt . " AND  customers.charge_fee_flag = 1 ";
        }
        if (!empty($month)) {
            $stmt = $stmt . " AND substr(invoice_month, 1,6)  = '{$month}'";
        }

        $row = $this->db->query($stmt)->row();
        return $row;
    }

    /**
     * Summary by price report by location and invoice description
     *
     * @param unknown_type $location_id
     */
    public function summary_by_manual_invoice($yearMonth, $locationId, $share_rev_flag=false)
    {
        $condition = "";
        if ($yearMonth) {
            $condition = $condition. " AND LEFT(invoice_date, 6)  = '{$yearMonth}'";
        }
        if($locationId){
            $condition = $condition. " AND location_id = '" . $locationId . "' ";
        }
        
        if($share_rev_flag){
            $condition .= " AND customers.charge_fee_flag = 1 ";
        }

        // Select statment
        $stmt = "
                (
                    SELECT
                        'custom_declaration_greater_1000' as kind
                        , count(DISTINCT invoice_summary_id) as quantity
                        , SUM(quantity * net_price) as total_amount
                    FROM invoice_detail_manual
                    INNER JOIN customers on customers.customer_id = invoice_detail_manual.customer_id
                    WHERE invoice_detail_manual.description = 'customs declaration (>1000 EUR)'
                        " . $condition . "
                )
                UNION
                (
                    SELECT
                        'custom_declaration_less_1000' as kind
                        , count(DISTINCT invoice_summary_id) as quantity
                        , SUM(quantity * net_price) as total_amount
                    FROM invoice_detail_manual
                    INNER JOIN customers on customers.customer_id = invoice_detail_manual.customer_id
                    WHERE invoice_detail_manual.description = 'customs declaration (<1000 EUR)'
                        " . $condition . "
                )
                UNION
                (
                    SELECT
                        'cash_payment_for_item_delivery' as kind
                        , count(DISTINCT invoice_summary_id) as quantity
                        , SUM(quantity * net_price) as total_amount
                    FROM invoice_detail_manual
                    INNER JOIN customers on customers.customer_id = invoice_detail_manual.customer_id
                    WHERE invoice_detail_manual.description = 'cash payment for item on delivery'
                        " . $condition . "
                )
                UNION
                (
                    SELECT
                        'cash_payment_free_for_item_delivery' as kind
                        , count(DISTINCT invoice_summary_id) as quantity
                        , SUM(quantity * net_price) as total_amount
                    FROM invoice_detail_manual
                    INNER JOIN customers on customers.customer_id = invoice_detail_manual.customer_id
                    WHERE invoice_detail_manual.description = 'cash payment fee'
                        " . $condition . "
                )
                UNION
                (
                    SELECT
                        'customs_cost_import' as kind
                        , count(DISTINCT invoice_summary_id) as quantity
                        , SUM(quantity * net_price) as total_amount
                    FROM invoice_detail_manual
                    INNER JOIN customers on customers.customer_id = invoice_detail_manual.customer_id
                    WHERE invoice_detail_manual.description = 'customs cost import'
                        " . $condition . "
                )
                UNION
                (
                    SELECT
                        'customs_handling_fee_import' as kind
                        , count(DISTINCT invoice_summary_id) as quantity
                        , SUM(quantity * net_price) as total_amount
                    FROM invoice_detail_manual
                    INNER JOIN customers on customers.customer_id = invoice_detail_manual.customer_id
                    WHERE invoice_detail_manual.description = 'customs handling fee import'
                        " . $condition . "
                )
                UNION
                (
                    SELECT
                        'address_verification' as kind
                        , count(DISTINCT invoice_summary_id) as quantity
                        , SUM(quantity * net_price) as total_amount
                    FROM invoice_detail_manual
                    INNER JOIN customers on customers.customer_id = invoice_detail_manual.customer_id
                    WHERE invoice_detail_manual.description = 'address verification'
                        " . $condition . "
                )
                UNION
                (
                    SELECT
                        'special_service_fee_in_15min_intervalls' as kind
                        , count(DISTINCT invoice_summary_id) as quantity
                        , SUM(quantity * net_price) as total_amount
                    FROM invoice_detail_manual
                    INNER JOIN customers on customers.customer_id = invoice_detail_manual.customer_id
                    WHERE invoice_detail_manual.description = 'special service fee (in 15min intervalls)'
                        " . $condition . "
                )
                UNION
                (
                    SELECT
                        'personal_pickup_charge' as kind
                        , count(DISTINCT invoice_summary_id) as quantity
                        , SUM(quantity * net_price) as total_amount
                    FROM invoice_detail_manual
                    INNER JOIN customers on customers.customer_id = invoice_detail_manual.customer_id
                    WHERE invoice_detail_manual.description = 'personal pickup charge'
                        " . $condition . "
                )
        ";

        $result = $this->db->query($stmt)->result();
        return $result;
    }

    /**
     * Summary by price report by location and invoice description
     *
     * @param unknown_type $location_id
     */
    public function summary_by_credit_note($location_id, $month = '', $share_rev_flag = false)
    {
        $location_condition = '';
        if($location_id){
            $location_condition = " AND location_id = '" . $location_id . "' ";
        }

        // Select statment
        $stmt = "SELECT ";
        $stmt = $stmt . " count(DISTINCT invoice_summary_id) as quantity, SUM(quantity * net_price) as total_amount ";

        // FROM statement
        $stmt = $stmt . " FROM invoice_detail_manual";
        $stmt = $stmt . " INNER JOIN customers ON customers.customer_id = invoice_detail_manual.customer_id";

        // Where statement
        $stmt = $stmt . " WHERE invoice_detail_manual.net_price < 0 ".$location_condition;

        if (!empty($month)) {
            $stmt = $stmt . " AND LEFT(invoice_date, 6)  = '{$month}'";
        }
        
        if($share_rev_flag){
            $stmt = $stmt . " AND customers.charge_fee_flag = 1 ";
        }

        $row = $this->db->query($stmt)->row();
        return $row;
    }

    /**
     * Calculate summary total invoice by location
     *
     * @param unknown_type $location_id
     */
    public function summary_invoice_by_location($customer_id)
    {
        // Select statment
        $stmt = "SELECT ";
        $stmt = $stmt . " SUM(total_invoice) as total_invoice, location_id ";

        // FROM statement
        $stmt = $stmt . " FROM invoice_summary_by_location";

        // Where statement
        $stmt = $stmt . " WHERE  customer_id = {$customer_id}";
        $stmt = $stmt . " GROUP BY location_id ";

        $rows = $this->db->query($stmt)->result();
        return $rows;
    }

    public function summary_by_other_local_invoice($yearMonth, $locationId, $share_rev_flag = false)
    {
        $condition = "";
        if ($yearMonth) {
            $condition = " AND LEFT(invoice_date, 6)  = '{$yearMonth}'";
        }
        if($locationId){
            $condition = $condition. " AND location_id = '" . $locationId . "' ";
        }
        
        if($share_rev_flag){
            $condition .= "  AND customers.charge_fee_flag = 1 ";
        }
        
        $stmt = "
                SELECT
                        count(DISTINCT invoice_summary_id) as quantity
                        , SUM(quantity * net_price) as total_amount
                    FROM invoice_detail_manual
                    INNER JOIN customers on customers.customer_id = invoice_detail_manual.customer_id
                    WHERE invoice_detail_manual.net_price > 0
                        AND invoice_detail_manual.description <> 'customs declaration (>1000 EUR)'
                        AND invoice_detail_manual.description <> 'customs declaration (<1000 EUR)'
                        AND invoice_detail_manual.description <> 'cash payment for item on delivery'
                        AND invoice_detail_manual.description <> 'cash payment fee'
                        AND invoice_detail_manual.description <> 'customs cost import'
                        AND invoice_detail_manual.description <> 'customs handling fee import'
                        AND invoice_detail_manual.description <> 'address verification'
                        AND invoice_detail_manual.description <> 'special service fee (in 15min intervalls)'
                        AND invoice_detail_manual.description <> 'personal pickup charge'
                        AND invoice_detail_manual.description <> 'Paypal Transaction Fee'
                        AND invoice_detail_manual.description <> 'auto-credit note'
                        " . $condition . "
              ";

        $result = $this->db->query($stmt)->result();
        return $result;
    }

    public function summary_postboxes_fee_by_location($yearMonth, $locationId, $share_rev_flag =false)
    {
        $location_condition = '';
        if($locationId){
            $location_condition = " AND location_id = '" . $locationId . "' ";
        }
        $stmt = " SELECT
                    SUM(free_postboxes_amount) as free_postboxes_amount
                  , SUM(private_postboxes_amount) as private_postboxes_amount
                  , SUM(business_postboxes_amount) as business_postboxes_amount
                  FROM invoice_summary_by_location
                  INNER JOIN customers on customers.customer_id = invoice_summary_by_location.customer_id
                  WHERE customers.account_type= 4 AND invoice_month = '" . $yearMonth . "'
                      ".$location_condition."
              ";
        
        if($share_rev_flag){
            $stmt .= " AND customers.charge_fee_flag = 1";
        }

        $result = $this->db->query($stmt)->result();
        return $result;
    }
    
    public function get_storage_day_by_location($location_id, $target_month){
        $location_condition = '';
        if($location_id){
            $location_condition = " AND location_id = '" . $location_id . "' ";
        }
        $stmt = " 
            SELECT SUM(storing_letters_free_quantity + storing_letters_private_quantity + storing_letters_business_quantity
                    + storing_packages_free_quantity + storing_packages_private_quantity + storing_packages_business_quantity) as total
            FROM invoice_summary_by_location
            WHERE 
                LEFT(invoice_month, 6) = '".$target_month."'
                ".$location_condition;
        $result = $this->db->query($stmt)->row();
        return $result;
    }
    
    public function get_list_postbox_fee_by($customer_id, $invoice_month, $location_id = ''){
        $this->db->select("invoice_summary_by_location.*, location.location_name");
        $this->db->join('location','location.id=invoice_summary_by_location.location_id','left');
        $this->db->where('invoice_summary_by_location.customer_id', $customer_id);
        $this->db->where('invoice_summary_by_location.invoice_month', $invoice_month);
        if(!empty($location_id)){
            $this->db->where('invoice_summary_by_location.location_id', $location_id);
        }
        
        return $this->db->get($this->_table)->result();
    }
    
    public function updateTotalInvoice($yearMonth) {
        $sql = "
            UPDATE invoice_summary_by_location
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
                    + (IFNULL(custom_declaration_outgoing_quantity_01, 0) * IFNULL(custom_declaration_outgoing_price_01, 0) )
                    + (IFNULL(custom_declaration_outgoing_quantity_02,0) * IFNULL(custom_declaration_outgoing_price_02, 0) )
                    )
                WHERE (
                      invoice_type = 0 OR
                      invoice_type IS NULL OR
                      invoice_type = 1
                    ) 
                    AND SUBSTR( invoice_month, 1, 6 ) = '{$yearMonth}'";

        $this->db->query($sql);

        return $this->db->affected_rows();
    }
    
    public function reset_storage_cost($yearMonth){
        $sql = "
            UPDATE invoice_summary_by_location
            INNER JOIN customers on invoice_summary_by_location.customer_id=customers.customer_id
                SET 
                    storing_letters_free_account = 0,
                    storing_letters_private_account = 0,
                    storing_letters_business_account = 0,
                    storing_packages_free_account = 0,
                    storing_packages_private_account= 0,
                    storing_packages_business_account = 0,
                    storing_letters_private_netprice = 0,
                    storing_letters_business_quantity = 0,
                    storing_letters_business_netprice = 0,
                    storing_packages_free_quantity = 0,
                    storing_packages_free_netprice =0,
                    storing_packages_private_quantity =0,
                    storing_packages_private_netprice =0,
                    storing_packages_business_quantity =0,
                    storing_packages_business_netprice =0
                WHERE 
                    (customers.`status` is null OR customers.`status` <> 1)
                    AND (
                      invoice_summary_by_location.invoice_type = 0 OR
                      invoice_summary_by_location.invoice_type IS NULL OR
                      invoice_summary_by_location.invoice_type = 1
                    ) 
                    AND invoice_summary_by_location.customer_id IN (select distinct to_customer_id from envelopes WHERE current_storage_charge_fee_day = 0)
                    AND invoice_summary_by_location.invoice_month = '{$yearMonth}'";

        $this->db->query($sql);

        return $this->db->affected_rows();
    }
    
    /**
     * count all manual invoice of location.
     * @param type $targetYM
     * @param type $location_id
     * @param type $share_rev_flag
     * @return type
     */
    public function count_manual_invoices_by($targetYM, $location_id = '', $share_rev_flag = false){
        $this->db->select("count(*) as cnt");
        $this->db->join("customers", "customers.customer_id=invoice_summary_by_location.customer_id", "left");
        
        if($share_rev_flag){
            $this->db->where("customers.charge_fee_flag", APConstants::ON_FLAG);
        }
        
        if(!empty($location_id)){
            $this->db->where("invoice_summary_by_location.location_id", $location_id);
        }
        
        $this->db->where("LEFT(invoice_summary_by_location.invoice_month,6)", $targetYM);
        $this->db->where("invoice_summary_by_location.invoice_type", "2");
        $this->db->where("invoice_summary_by_location.total_invoice <>", 0);
        
        $result = $this->db->get($this->_table)->row();
        
        return $result->cnt;
    }
    
    /**
     * Summary by price report by location and invoice description
     *
     * @param unknown_type $location_id
     */
    public function summary_by_manual_invoice_of_partner($yearMonth, $partner_id)
    {
        $condition = "";
        if (!empty($yearMonth)) {
            $condition .= " AND LEFT(invoice_date, 6)  = '{$yearMonth}'";
        }
        if(!empty($partner_id)){
            $condition .= " AND partner_customers.partner_id = '" . $partner_id . "' AND partner_customers.end_flag = 0 ";
        }

        // Select statment
        $stmt = "
                (
                    SELECT
                        'custom_declaration_greater_1000' as kind
                        , count(DISTINCT invoice_summary_id) as quantity
                        , SUM(quantity * net_price) as total_amount
                        , SUM(quantity * net_price * partner_customers.customer_discount/100) as discount_total
                        , SUM(((quantity * net_price) - (quantity * net_price * partner_customers.customer_discount/100))* partner_customers.rev_share_ad/100) as rev_share_total
                    FROM invoice_detail_manual
                    INNER JOIN customers on customers.customer_id = invoice_detail_manual.customer_id
                    INNER JOIN partner_customers ON partner_customers.customer_id = customers.customer_id
                    WHERE invoice_detail_manual.description = 'customs declaration (>1000 EUR)'
                        " . $condition . "
                )
                UNION
                (
                    SELECT
                        'custom_declaration_less_1000' as kind
                        , count(DISTINCT invoice_summary_id) as quantity
                        , SUM(quantity * net_price) as total_amount
                        , SUM(quantity * net_price * partner_customers.customer_discount/100) as discount_total
                        , SUM(((quantity * net_price)- (quantity * net_price * partner_customers.customer_discount/100)) * partner_customers.rev_share_ad/100) as rev_share_total
                    FROM invoice_detail_manual
                    INNER JOIN customers on customers.customer_id = invoice_detail_manual.customer_id
                    INNER JOIN partner_customers ON partner_customers.customer_id = customers.customer_id
                    WHERE invoice_detail_manual.description = 'customs declaration (<1000 EUR)'
                        " . $condition . "
                )
                UNION
                (
                    SELECT
                        'cash_payment_for_item_delivery' as kind
                        , count(DISTINCT invoice_summary_id) as quantity
                        , SUM(quantity * net_price) as total_amount
                        , SUM(quantity * net_price * partner_customers.customer_discount/100) as discount_total
                        , SUM(((quantity * net_price)- (quantity * net_price * partner_customers.customer_discount/100))* partner_customers.rev_share_ad/100) as rev_share_total
                    FROM invoice_detail_manual
                    INNER JOIN customers on customers.customer_id = invoice_detail_manual.customer_id
                    INNER JOIN partner_customers ON partner_customers.customer_id = customers.customer_id
                    WHERE invoice_detail_manual.description = 'cash payment for item on delivery'
                        " . $condition . "
                )
                UNION
                (
                    SELECT
                        'cash_payment_free_for_item_delivery' as kind
                        , count(DISTINCT invoice_summary_id) as quantity
                        , SUM(quantity * net_price) as total_amount
                        , SUM(quantity * net_price * partner_customers.customer_discount/100) as discount_total
                        , SUM(((quantity * net_price)- (quantity * net_price * partner_customers.customer_discount/100))* partner_customers.rev_share_ad/100) as rev_share_total
                    FROM invoice_detail_manual
                    INNER JOIN customers on customers.customer_id = invoice_detail_manual.customer_id
                    INNER JOIN partner_customers ON partner_customers.customer_id = customers.customer_id
                    WHERE invoice_detail_manual.description = 'cash payment fee'
                        " . $condition . "
                )
                UNION
                (
                    SELECT
                        'customs_cost_import' as kind
                        , count(DISTINCT invoice_summary_id) as quantity
                        , SUM(quantity * net_price) as total_amount
                        , SUM(quantity * net_price * partner_customers.customer_discount/100) as discount_total
                        , SUM(((quantity * net_price)- (quantity * net_price * partner_customers.customer_discount/100))* partner_customers.rev_share_ad/100) as rev_share_total
                    FROM invoice_detail_manual
                    INNER JOIN customers on customers.customer_id = invoice_detail_manual.customer_id
                    INNER JOIN partner_customers ON partner_customers.customer_id = customers.customer_id
                    WHERE invoice_detail_manual.description = 'customs cost import'
                        " . $condition . "
                )
                UNION
                (
                    SELECT
                        'customs_handling_fee_import' as kind
                        , count(DISTINCT invoice_summary_id) as quantity
                        , SUM(quantity * net_price) as total_amount
                        , SUM(quantity * net_price * partner_customers.customer_discount/100) as discount_total
                        , SUM(((quantity * net_price)- (quantity * net_price * partner_customers.customer_discount/100))* partner_customers.rev_share_ad/100) as rev_share_total
                    FROM invoice_detail_manual
                    INNER JOIN customers on customers.customer_id = invoice_detail_manual.customer_id
                    INNER JOIN partner_customers ON partner_customers.customer_id = customers.customer_id
                    WHERE invoice_detail_manual.description = 'customs handling fee import'
                        " . $condition . "
                )
                UNION
                (
                    SELECT
                        'address_verification' as kind
                        , count(DISTINCT invoice_summary_id) as quantity
                        , SUM(quantity * net_price) as total_amount
                        , SUM(quantity * net_price * partner_customers.customer_discount/100) as discount_total
                        , SUM(((quantity * net_price)- (quantity * net_price * partner_customers.customer_discount/100))* partner_customers.rev_share_ad/100) as rev_share_total
                    FROM invoice_detail_manual
                    INNER JOIN customers on customers.customer_id = invoice_detail_manual.customer_id
                    INNER JOIN partner_customers ON partner_customers.customer_id = customers.customer_id
                    WHERE invoice_detail_manual.description = 'address verification'
                        " . $condition . "
                )
                UNION
                (
                    SELECT
                        'special_service_fee_in_15min_intervalls' as kind
                        , count(DISTINCT invoice_summary_id) as quantity
                        , SUM(quantity * net_price) as total_amount
                        , SUM(quantity * net_price * partner_customers.customer_discount/100) as discount_total
                        , SUM(((quantity * net_price)- (quantity * net_price * partner_customers.customer_discount/100))* partner_customers.rev_share_ad/100) as rev_share_total
                    FROM invoice_detail_manual
                    INNER JOIN customers on customers.customer_id = invoice_detail_manual.customer_id
                    INNER JOIN partner_customers ON partner_customers.customer_id = customers.customer_id
                    WHERE invoice_detail_manual.description = 'special service fee (in 15min intervalls)'
                        " . $condition . "
                )
                UNION
                (
                    SELECT
                        'personal_pickup_charge' as kind
                        , count(DISTINCT invoice_summary_id) as quantity
                        , SUM(quantity * net_price) as total_amount
                        , SUM(quantity * net_price * partner_customers.customer_discount/100) as discount_total
                        , SUM(((quantity * net_price )- (quantity * net_price * partner_customers.customer_discount/100))* partner_customers.rev_share_ad/100) as rev_share_total
                    FROM invoice_detail_manual
                    INNER JOIN customers on customers.customer_id = invoice_detail_manual.customer_id
                    INNER JOIN partner_customers ON partner_customers.customer_id = customers.customer_id
                    WHERE invoice_detail_manual.description = 'personal pickup charge'
                        " . $condition . "
                )
        ";

        $result = $this->db->query($stmt)->result();
        return $result;
    }
    
    /**
     * Summary by price report by location and invoice description
     *
     * @param unknown_type $location_id
     */
    public function summary_by_credit_note_of_partner($yearMonth, $patner_id)
    {
        // Select statment
        $stmt = " SELECT 
                    count(DISTINCT invoice_summary_id) as quantity
                    , SUM(quantity * net_price) as total_amount
                    , SUM(quantity * net_price * partner_customers.customer_discount/100) as discount_total
                    , SUM(((quantity * net_price)- (quantity * net_price * partner_customers.customer_discount/100))* partner_customers.rev_share_ad/100) as rev_share_total
                FROM invoice_detail_manual
                INNER JOIN customers ON customers.customer_id = invoice_detail_manual.customer_id
                INNER JOIN partner_customers ON partner_customers.customer_id = customers.customer_id
                WHERE invoice_detail_manual.net_price < 0
                    AND partner_customers.partner_id = '".$patner_id."'
                    AND partner_customers.end_flag = 0
                    AND LEFT(invoice_date, 6)  = '".$yearMonth."'";

        $row = $this->db->query($stmt)->row();
        return $row;
    }
    
    public function summary_by_paypal_total_of_partner($yearMonth, $patner_id)
    {
        // Select statment
        $stmt = " SELECT 
                    SUM(total_invoice) as total_invoice,
                    SUM(total_invoice * partner_customers.customer_discount/100) as discount_total,
                    SUM(((total_invoice)- (total_invoice * partner_customers.customer_discount/100))* partner_customers.rev_share_ad/100) as rev_share_total
                FROM invoice_summary_by_location
                INNER JOIN customers ON customers.customer_id = invoice_summary_by_location.customer_id
                INNER JOIN partner_customers ON partner_customers.customer_id = customers.customer_id
                WHERE partner_customers.partner_id = '".$patner_id."'
                    AND partner_customers.end_flag = 0
                    AND (invoice_summary_by_location.payment_transaction_id IS NOT NULL AND invoice_summary_by_location.payment_transaction_id <> '')
                    AND LEFT(invoice_month, 6)  = '".$yearMonth."'";

        return $this->db->query($stmt)->row();
    }
    
    public function summary_by_paypal_quantity_of_partner($yearMonth, $patner_id)
    {
        // Select statment
        $stmt = " SELECT 
                    count(DISTINCT invoice_summary_id) as quantity
                FROM invoice_summary_by_location
                INNER JOIN customers ON customers.customer_id = invoice_summary_by_location.customer_id
                INNER JOIN partner_customers ON partner_customers.customer_id = customers.customer_id
                WHERE partner_customers.partner_id = '".$patner_id."'
                    AND partner_customers.end_flag = 0
                    AND (invoice_summary_by_location.payment_transaction_id IS NOT NULL AND invoice_summary_by_location.payment_transaction_id <> '')
                    AND LEFT(invoice_month, 6)  = '".$yearMonth."'";

        $row = $this->db->query($stmt)->row();
        return $row->quantity;
    }
    
    /**
     * sum other invoice of partner.
     * 
     * @param type $yearMonth
     * @param type $patner_id
     * @return type
     */
    public function summary_all_manual_invoice_of_partner($yearMonth, $patner_id)
    {
        $condition = "";
        if (!empty($yearMonth)) {
            $condition .= " AND LEFT(invoice_date, 6)  = '{$yearMonth}'";
        }
        if(!empty($partner_id)){
            $condition .= " AND partner_customers.partner_id = '" . $partner_id . "' AND partner_customers.end_flag = 0 ";
        }
        
        $stmt = "
                SELECT
                        count(DISTINCT invoice_summary_id) as quantity
                        , SUM(quantity * net_price) as total_amount
                        , SUM(quantity * net_price * partner_customers.customer_discount/100) as discount_total
                        , SUM(((quantity * net_price ) - (quantity * net_price * partner_customers.customer_discount/100))* partner_customers.rev_share_ad/100 ) as rev_share_total
                    FROM invoice_detail_manual
                    INNER JOIN customers on customers.customer_id = invoice_detail_manual.customer_id
                    INNER JOIN partner_customers ON partner_customers.customer_id = customers.customer_id
                    WHERE invoice_detail_manual.net_price > 0
                        AND invoice_detail_manual.description <> 'customs declaration (>1000 EUR)'
                        AND invoice_detail_manual.description <> 'customs declaration (<1000 EUR)'
                        AND invoice_detail_manual.description <> 'cash payment for item on delivery'
                        AND invoice_detail_manual.description <> 'cash payment fee'
                        AND invoice_detail_manual.description <> 'customs cost import'
                        AND invoice_detail_manual.description <> 'customs handling fee import'
                        AND invoice_detail_manual.description <> 'address verification'
                        AND invoice_detail_manual.description <> 'special service fee (in 15min intervalls)'
                        AND invoice_detail_manual.description <> 'personal pickup charge'
                        AND invoice_detail_manual.description <> 'Paypal Transaction Fee'
                        AND invoice_detail_manual.description <> 'auto-credit note'
                        " . $condition . "
              ";

        return $this->db->query($stmt)->row();
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
        
        $this->db->select('invoice_summary_by_location.*, customers.customer_code, customers.email, customers.user_name, customers.status');
        $this->db->select('customers.activated_flag, customers.deactivated_type');
        $this->db->join('customers', 'customers.customer_id = invoice_summary_by_location.customer_id', 'inner');
        
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