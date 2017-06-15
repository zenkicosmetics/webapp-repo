<?php defined('BASEPATH') or exit('No direct script access allowed');

class invoices_api
{
    public function __construct()
    {
        ci()->load->model(array(
            'customers/customer_m',
            'settings/currencies_m'
        ));
    }

    public static function getVAT($invoicing_country, $customerType = APConstants::CUSTOMER_TYPE_ENTERPRISE)
    {
        ci()->load->model('invoices/vatcase_m');

        $retVAT = new stdClass();
        $retVAT->rate = 0;
        $retVAT->vat_case_id = 0;
        $retVAT->vat_case = 0;

        // Get vat from vat_table
        $vatRow = ci()->vatcase_m->get_by_many(
            array(
                "product_type" => APConstants::VAT_PRODUCT_LOCAL_SERVICE,
                "customer_type" => $customerType,
                "baseon_country_id" => $invoicing_country
            )
        );
        if (empty($vatRow)) {
            $vatRow = ci()->vatcase_m->get_by_many(
                array(
                    "product_type" => APConstants::VAT_PRODUCT_LOCAL_SERVICE,
                    "customer_type" => $customerType,
                    "baseon_country_id" => 0
                )
            );
        }
        if ($vatRow) {
            $retVAT->rate = $vatRow->rate;
            $retVAT->vat_case_id = $vatRow->vat_id;
            $retVAT->vat_case = $vatRow->vat_case_id;
        }

        return $retVAT;
    }

    public static function getInvoiceOfCurrentMonth($customerID, $VAT = '')
    {
        ci()->load->model('invoices/invoice_summary_m');

        $whereArray = array(
            "customer_id" => $customerID,
            "invoice_month" => date('Ym')
        );
        //if ($VAT) $whereArray['vat'] = $VAT;
        $invoice = ci()->invoice_summary_m->get_by_many($whereArray);

        return $invoice;
    }
    
    public static function getInvoiceSummaryIdOfCurrentMonth($customerID) {
        ci()->load->library('customers/customers_api');
        ci()->load->library('addresses/addresses_api');
        
        $customer = customers_api::getCustomerByID($customerID);
        $customerAddress = addresses_api::getCustomerAddress($customerID);
        $customerVAT = customers_api::getVATCustomer($customer, $customerAddress);
        $vat = $customerVAT->rate;
        $vatCaseID = $customerVAT->vat_case_id;
        $invoiceSummaryID = self::checkInvoiceOfCurrentMonth($customerID, $vat, $vatCaseID);
        return $invoiceSummaryID;
    }

    public static function getInvoiceSummary($customerID)
    {

        ci()->load->model('invoices/invoice_summary_m');

        $invoiceSummary = ci()->invoice_summary_m->get_credit_note_summary($customerID);

        return $invoiceSummary;
    }

    public static function createInvoiceOfCurrentMonth($customerID, $invoiceCode, $vat, $vatCaseID)
    {
        ci()->load->model('invoices/invoice_summary_m');

        $invoiceSummaryID = ci()->invoice_summary_m->insert(array(
                "invoice_month" => date('Ym'),
                "vat" => $vat,
                "vat_case" => $vatCaseID,
                "customer_id" => $customerID,
                'invoice_code' => $invoiceCode,
                "update_flag" => 0
            )
        );

        return $invoiceSummaryID;
    }

    public static function checkInvoiceOfCurrentMonth($customerID, $vat, $vatCaseID)
    {
        $invoice = self::getInvoiceOfCurrentMonth($customerID);
        if ($invoice) {
            $invoiceSummaryID = $invoice->id;
        } else {
            $invoiceCode = self::generateInvoiceNumber();
            $invoiceSummaryID = self::createInvoiceOfCurrentMonth($customerID, $invoiceCode, $vat, $vatCaseID);
        }

        return $invoiceSummaryID;
    }

    public static function getInvoiceCode($customerID)
    {
        ci()->load->model('invoices/invoice_summary_m');

        $currentInvoice = ci()->invoice_summary_m->get_by_many(array(
            "customer_id" => $customerID,
            'invoice_month' => date('Ym')
        ));
        if ($currentInvoice) {
            $invoiceCode = $currentInvoice->invoice_code;
        } else {
            $invoiceCode = self::generateInvoiceNumber();
        }

        return $invoiceCode;
    }

    public static function generateInvoiceNumber()
    {
        return APUtils::generateInvoiceCodeById('');
    }

    /**
     * Generate invoice code by invoice id.
     *
     * @param unknown $invoiceID
     */
    public static function generateInvoiceCodeById($invoiceID)
    {
        return APUtils::generateInvoiceCodeById($invoiceID);
    }

    public static function getTotalPagesChargedOfCurrentMonth($customerID, $activityType = 0)
    {
        ci()->load->model('invoices/invoice_detail_m');

        $totalPagesCharged = ci()->invoice_detail_m->getTotalPagesChargedOfCurrentMonth($customerID, $activityType);

        return ($totalPagesCharged) ? $totalPagesCharged : 0;
    }

    public static function createInvoiceDetailForEnvelopeScanned($customerID, $envelopeID, $postboxType, $invoiceSummaryID, $scanEnvelopePrice)
    {
        ci()->load->model('invoices/invoice_detail_m');

        $id = ci()->invoice_detail_m->insert(array(
            "customer_id" => $customerID,
            "activity_date" => date('Ymd'),
            "item_number" => 1,
            "unit_price" => $scanEnvelopePrice,
            "item_amount" => 1 * $scanEnvelopePrice,
            "unit" => Settings::get(APConstants::CURRENTCY_CODE),
            "envelope_id" => $envelopeID,
            "activity" => 'Envelope scanning',
            "activity_type" => APConstants::ENVELOPE_SCAN_ACTIVITY_TYPE,
            "postbox_type" => $postboxType,
            "invoice_summary_id" => $invoiceSummaryID
        ));

        return $id;
    }

    public static function createInvoiceDetailForDocumentScanned($customerID, $envelopeID, $postboxType, $invoiceSummaryID, $scanDocumentPrice)
    {
        ci()->load->model('invoices/invoice_detail_m');

        $id = ci()->invoice_detail_m->insert(array(
            "customer_id" => $customerID,
            "activity_date" => date('Ymd'),
            "item_number" => 1,
            "unit_price" => $scanDocumentPrice,
            "item_amount" => 1 * $scanDocumentPrice,
            "unit" => Settings::get(APConstants::CURRENTCY_CODE),
            "envelope_id" => $envelopeID,
            "activity" => 'scanning',
            "activity_type" => APConstants::ITEM_SCAN_ACTIVITY_TYPE,
            "postbox_type" => $postboxType,
            "invoice_summary_id" => $invoiceSummaryID
        ));

        return $id;
    }

    public static function createInvoiceDetailForAdditionalPagesScanned($customerID, $envelopeID, $postboxType, $invoiceSummaryID, $additionalPagesScanningNumber, $additionalPagesScanningPrice)
    {
        ci()->load->model('invoices/invoice_detail_m');

        $id = ci()->invoice_detail_m->insert(array(
            "customer_id" => $customerID,
            "activity" => 'Additional scanning',
            "activity_date" => date('Ymd'),
            "item_number" => $additionalPagesScanningNumber,
            "unit_price" => $additionalPagesScanningPrice,
            "item_amount" => intval($additionalPagesScanningNumber) * floatval($additionalPagesScanningPrice),
            "unit" => Settings::get(APConstants::CURRENTCY_CODE),
            "envelope_id" => $envelopeID,
            "activity_type" => APConstants::ADDITIONAL_SCAN_ACTIVITY_TYPE,
            "postbox_type" => $postboxType,
            "invoice_summary_id" => $invoiceSummaryID
        ));

        return $id;
    }
    
    /**
     * Create invoice detail for all enterprise cost
     * (Own Location, Touch Panel at own location, Own mobile app, API Access, Clevver Subdomain, Own Domain)
     * 
     * @param type $customer_id
     * @return type
     */
    public static function createEnterpriseInvoice($customer_id)
    {
        ci()->lang->load('addresses/location_customer_m');
        
        $current_date = now();
        // Check valid contract date
        $api_access_end_date = AccountSetting::get_alias02($customer_id, APConstants::CUSTOMER_API_ACCESS_SETTING);
        $api_access_flag = AccountSetting::get($customer_id, APConstants::CUSTOMER_API_ACCESS_SETTING);
        if ($api_access_flag == APConstants::ON_FLAG && $current_date <= $api_access_end_date) {
            self::createEnterpriseInvoiceDetail($customer_id, APConstants::INVOICE_ACTIVITY_TYPE_API_ACCESS);
        }
        
        // Own domain
        $own_domain_flag = AccountSetting::get_alias02($customer_id, APConstants::CUSTOMER_OWN_DOMAIN_KEY);
        $own_domain = AccountSetting::get($customer_id, APConstants::CUSTOMER_OWN_DOMAIN_KEY);
        if ($own_domain_flag == APConstants::ON_FLAG) {
            if (APUtils::endsWith($own_domain, APConstants::DEFAULT_CLEVVERMAIL_DOMAIN)) {
                self::createEnterpriseInvoiceDetail($customer_id, APConstants::INVOICE_ACTIVITY_TYPE_CLEVVER_SUBDOMAIN);
            } else {
                self::createEnterpriseInvoiceDetail($customer_id, APConstants::INVOICE_ACTIVITY_TYPE_OWN_SUBDOMAIN);
            }
        }
        
        // Own location
        $total_location = ci()->location_customer_m->count_by_many(array(
            'parent_customer_id' => $customer_id
        ));
        if ($total_location >= 1) {
            self::createEnterpriseInvoiceDetail($customer_id, APConstants::INVOICE_ACTIVITY_TYPE_OWN_LOCATION);
        }
    }
    
    /**
     * Create invoice detail for given activity
     * 
     * @param type $customerID
     * @param type $activity_type
     * @return type
     */
    public static function createEnterpriseInvoiceDetail($customerID, $activity_type)
    {
        ci()->load->model('invoices/invoice_detail_m');
        ci()->lang->load('invoices/invoices');
        ci()->load->library('price/price_api');
        
        $pricing_map = price_api::getDefaultPricingModel();
        $invoice_cost = 0;
        if ($activity_type == APConstants::INVOICE_ACTIVITY_TYPE_API_ACCESS) {
             $invoice_cost = $pricing_map[5]['api_access']->item_value;
        } else if ($activity_type == APConstants::INVOICE_ACTIVITY_TYPE_OWN_LOCATION) {
             $invoice_cost = $pricing_map[5]['own_location_monthly']->item_value;
        } else if ($activity_type == APConstants::INVOICE_ACTIVITY_TYPE_TOUCH_PANEL_OWN_LOCATION) {
             $invoice_cost = $pricing_map[5]['touch_panel_at_own_location_quarterly']->item_value;
        } else if ($activity_type == APConstants::INVOICE_ACTIVITY_TYPE_OWN_MOBILE_APP) {
             $invoice_cost = $pricing_map[5]['own_mobile_app_monthly']->item_value;
        } else if ($activity_type == APConstants::INVOICE_ACTIVITY_TYPE_CLEVVER_SUBDOMAIN) {
             $invoice_cost = $pricing_map[5]['clevver_subdomain']->item_value;
        } else if ($activity_type == APConstants::INVOICE_ACTIVITY_TYPE_OWN_SUBDOMAIN) {
             $invoice_cost = $pricing_map[5]['own_domain']->item_value;
        }
        
        $start_invoice_date = date('Ymd', strtotime(APUtils::getFirstDayOfCurrentMonth()));
        $end_invoice_date = date('Ymd', strtotime(APUtils::getLastDayOfCurrentMonth()));
        
        // Check existing the cost
        $invoice_detail_check = ci()->invoice_detail_m->get_by_many(array(
            "customer_id" => $customerID,
            "activity_type" => $activity_type,
            "start_invoice_date <= '".$start_invoice_date."'" => null,
            "end_invoice_date >= '".$start_invoice_date."'" => null
        ));
        
        // The cost of this item already charged before
        if (!empty($invoice_detail_check)) {
            return;
        }
        
        $primary_location_id = APUtils::getPrimaryLocationBy($customerID);
        
        $invoiceSummaryID = self::getInvoiceSummaryIdOfCurrentMonth($customerID);
        $id = ci()->invoice_detail_m->insert(array(
            "customer_id" => $customerID,
            "activity" => lang('invoice_activity_'.$activity_type),
            "activity_date" => date('Ymd'),
            "item_number" => 1,
            "unit_price" => $invoice_cost,
            "item_amount" => $invoice_cost,
            "unit" => Settings::get(APConstants::CURRENTCY_CODE),
            "envelope_id" => '',
            "activity_type" => $activity_type,
            "postbox_type" => '',
            "location_id" => $primary_location_id,
            "start_invoice_date" => $start_invoice_date,
            "end_invoice_date" => $end_invoice_date,
            "invoice_summary_id" => $invoiceSummaryID
        ));

        return $id;
    }

    public static function updateInvoiceDetailForAdditionalPagesScanned($customerID, $envelopeID, $postboxType, $invoiceSummaryID, $additionalPagesScanningNumber, $additionalPagesScanningPrice)
    {
        ci()->load->model('invoices/invoice_detail_m');

        ci()->invoice_detail_m->update_by_many(array(
            "customer_id" => $customerID,
            "envelope_id" => $envelopeID,
            "activity_type" => APConstants::ADDITIONAL_SCAN_ACTIVITY_TYPE
        ), array(
            "activity" => 'Additional scanning',
            "item_number" => $additionalPagesScanningNumber,
            "unit_price" => $additionalPagesScanningPrice,
            "item_amount" => intval($additionalPagesScanningNumber) * floatval($additionalPagesScanningPrice),
            "postbox_type" => $postboxType,
            "invoice_summary_id" => $invoiceSummaryID
        ));

        return true;
    }

    public static function checkExistInvoiceDetailForAdditionalDocumentScan($customerID, $envelopeID)
    {
        ci()->load->model('invoices/invoice_detail_m');

        $additionalPagesScanningInvoice = ci()->invoice_detail_m->get_by_many(array(
            "customer_id" => $customerID,
            "envelope_id" => $envelopeID,
            "activity_type" => APConstants::ADDITIONAL_SCAN_ACTIVITY_TYPE
        ));

        return $additionalPagesScanningInvoice;
    }

    public static function calculateCostForEnvelopeScan($customerID, $postboxID, $envelopeID)
    {
        ci()->load->library('invoices/EnvelopeItemScan');

        return EnvelopeItemScan::calculateCostForEnvelopeScan($customerID, $postboxID, $envelopeID);
    }

    public static function calculateCostForItemScan($customerID, $postboxID, $envelopeID)
    {
        ci()->load->library('invoices/EnvelopeItemScan');

        return EnvelopeItemScan::calculateCostForItemScan($customerID, $postboxID, $envelopeID);
    }

    /**
     * Summary data from tables [envelope_summary_month] to tables [invoice_summary]. Summary from invoice_detail to invoice_summary
     *
     * @param unknown_type $customerID
     * @param unknown_type $targetYear
     * @param unknown_type $targetMonth
     */
    public static function calculateInvoiceSummary($customerID, $targetYear, $targetMonth, $customerVat = null)
    {
        ci()->load->library('invoices/InvoiceSummary');

        ci()->invoicesummary->cal_invoice_summary($customerID, $targetYear, $targetMonth, $customerVat);
    }

    /**
     * calculate postbox invoice by customer.
     *
     * @param unknown $customer_id
     */
    public static function calculatePostboxInvoiceSummaryByLocation($customer_id, $pricings)
    {
        ci()->load->library('invoices/InvoiceSummaryByLocation');
        ci()->load->model("invoices/invoice_summary_by_location_m");
        ci()->load->model('mailbox/postbox_m');
        
        // reset invoice by location.
        ci()->invoice_summary_by_location_m->update_by_many(array(
            "customer_id" => $customer_id,
            "invoice_month" => APUtils::getCurrentYearMonth()
        ), array(
            "free_postboxes_amount" => 0,
            "free_postboxes_quantity" => 0,
            "free_postboxes_netprice" => 0,
            "private_postboxes_amount" => 0,
            "private_postboxes_quantity" => 0,
            "private_postboxes_netprice" => 0,
            "business_postboxes_amount" => 0,
            "business_postboxes_quantity" => 0,
            "business_postboxes_netprice" => 0
        ));

        // Gets all old postbox of customer 
        $postboxes = ci()->postbox_m->get_all_by_customer_not_include_new_with_pricing_template($customer_id);
        ci()->invoicesummarybylocation->calcPostboxInvoice($customer_id, $postboxes, false, $pricings);

        // Gets all old postbox of customer
        $new_postboxes = ci()->postbox_m->count_by_customer_include_new_with_pricing_template($customer_id);
        ci()->invoicesummarybylocation->calcPostboxInvoice($customer_id, $new_postboxes, true, $pricings);

        $free_postboxes = ci()->postbox_m->get_all_free_postboxes_by($customer_id);
        ci()->invoicesummarybylocation->calcFreePostboxInvoice($customer_id, $free_postboxes, $pricings);
    }

    public static function calculateTotalInvoice($yearMonth)
    {
        ci()->load->model('invoices/invoice_summary_m');

        $countInvoiceSummary = ci()->invoice_summary_m->updateTotalInvoice($yearMonth);

        return $countInvoiceSummary;
        
        
    }
    
    public static function calculateTotalInvoiceByLocation($yearMonth)
    {
        ci()->load->model('invoices/invoice_summary_by_location_m');

        $countInvoiceSummary = ci()->invoice_summary_by_location_m->updateTotalInvoice($yearMonth);

        return $countInvoiceSummary;
    }

    /**
     * Get total invoice by location.
     * This method will use for ticket #855
     * @param unknown_type $customer_id
     */
    public static function getTotalLocationInvoiceByCustomer($customer_id)
    {
        ci()->load->model('invoices/invoice_summary_by_location_m');
        $list_invoices = ci()->invoice_summary_by_location_m->summary_invoice_by_location($customer_id);
        $list_result = array();
        foreach ($list_invoices as $invoice) {
            $list_result[$invoice->location_id] = $invoice;
        }
        return $list_result;
    }

    /**
     * Get distribution invoice by location.
     *
     * @param unknown_type $customer_id
     * @param unknown_type $total_amount
     */
    public static function getDistributionInvoiceByLocation($customer_id, $total_amount)
    {
        $list_result = array();
        $list_distribution_invoice = invoices_api::getTotalLocationInvoiceByCustomer($customer_id);
        if (count($list_distribution_invoice) == 0) {
            return array();
        }

        // Get total
        $total = 0;
        foreach ($list_distribution_invoice as $invoice) {
            $total += $invoice->total_invoice;
        }


        if ($total == 0) {
            foreach ($list_distribution_invoice as $invoice) {
                $list_result[$invoice->location_id] = $total_amount / count($list_distribution_invoice);
            }
        } else {
            foreach ($list_distribution_invoice as $invoice) {
                $list_result[$invoice->location_id] = $total_amount * ($invoice->total_invoice / $total);
            }
        }
        return $list_result;
    }

    /**
     * count summary invoice detail by month.
     *
     * @param unknown $yearMonth
     * @param unknown $locationId
     */
    public static function countSummaryManualInvoice($yearMonth, $locationId, $share_rev_flag = false)
    {
        ci()->load->model("invoices/invoice_summary_by_location_m");
        $result = ci()->invoice_summary_by_location_m->summary_by_manual_invoice($yearMonth, $locationId, $share_rev_flag);

        $data = array();
        
        // return manual invoice
        // 1. custom_declaration_greater_1000
        // 2. custom_declaration_less_1000
        // 3. cash_payment_for_item_delivery
        // 4. cash_payment_free_for_item_delivery
        // 5. customs_cost_import
        // 6. customs_handling_fee_import
        // 7. address_verification
        // 8. special_service_fee_in_15min_intervalls
        // 9. personal_pickup_charge
        foreach ($result as $r) {
            $data[$r->kind] = new stdClass();
            $data[$r->kind]->quantity = (int)$r->quantity;
            $data[$r->kind]->total_amount = (float)$r->total_amount;
        }

        return $data;
    }
    
    /**
     * get invoice by location by month.
     * 
     * @param unknown $locationId
     * @param unknown $reportMonth
     * @return unknown
     */
    public static function getInvoiceSummaryByLocationByMonth($locationId, $reportMonth, $share_rev_flag = false)
    {
        ci()->load->model("invoices/invoice_summary_by_location_m");
        
        // Summary information
        $invoice_summary = ci()->invoice_summary_by_location_m->summary_by_location($locationId, $reportMonth, $share_rev_flag);
        
        return $invoice_summary;
    }
    
    public static function getTotalInvoiceSummaryByMonth($locations, $reportMonth){
        ci()->load->model("invoices/invoice_summary_by_location_m");
        
        $total_invoice = new stdClass();
        foreach($locations as $location_id ){
            // Gets rev share
            $rev_price_postboxes = price_api::getRevShareMapByLocationID($location_id);
            
            // get invoice summary.
            $invoice_summary = ci()->invoice_summary_by_location_m->summary_by_location($location_id, $reportMonth);
            
            
            
            $total_invoice->business_postboxes_quantity += $invoice_summary->business_postboxes_quantity;
        }
        
        return $total_invoice;
    }
    
    public static function getCreditNoteByLocationByMonth($locationId, $reportMonth, $share_rev_flag = false)
    {
        ci()->load->model("invoices/invoice_summary_by_location_m");
    
        // Summary information
        $invoice = ci()->invoice_summary_by_location_m->summary_by_credit_note($locationId, $reportMonth, $share_rev_flag);
    
        return $invoice;
    }
    
    /**
     * Gets all other invoice.
     * @param type $yearMonth
     * @param type $locationId
     * @return \stdClass
     */
    public static function sumOtherLocalInvoice($yearMonth, $locationId, $share_rev_flag = false)
    {
        ci()->load->model("invoices/invoice_summary_by_location_m");
        $data = ci()->invoice_summary_by_location_m->summary_by_other_local_invoice($yearMonth, $locationId, $share_rev_flag);
        
        $result = new stdClass();
        $result->quantity = 0;
        $result->total_amount = 0;
        if($data){
            $result->quantity = $data[0]->quantity ;
            $result->total_amount = $data[0]->total_amount;
        }
        return $result;
    }
    
    public static function getPostboxFeeByLocation($locationId, $yearMonth, $share_rev_flag = false)
    {
        ci()->load->model("invoices/invoice_summary_by_location_m");
        $data = ci()->invoice_summary_by_location_m->summary_postboxes_fee_by_location($yearMonth, $locationId, $share_rev_flag);

        $result = new stdClass();
        $result->free_postboxes_amount = 0;
        $result->private_postboxes_amount = 0;
        $result->business_postboxes_amount = 0;
        if($data){
            $result->free_postboxes_amount = $data[0]->free_postboxes_amount;
            $result->private_postboxes_amount = $data[0]->private_postboxes_amount;
            $result->business_postboxes_amount = $data[0]->business_postboxes_amount;
        }
        return $result;
    }
    
    
    public static function resetApplyDateOfPostboxAtEndOfMonth($customerId)
    {
        ci()->load->model("mailbox/postbox_m");
        // truong hop la cuoi thang thi xoa apply date di.
        if (APUtils::isLastDayOfMonth()) {
            // Update apply_date of postbox to remove calculate for next month
            ci()->postbox_m->update_by_many(array(
                "customer_id" => $customerId
            ), array(
                "apply_date" => null
            ));
        }
    }
    
    /**
     * update invoice summary total by location.
     * @param type $targetYM
     */
    public static function updateInvoiceSummaryTotalByLocation($targetYM, $target_location_id='', $update_invoice_total_flag=false){
        ci()->load->library('invoices/InvoiceSummaryByLocation');
        
        // updateInvoiceSummaryTotalByLocation 
        ci()->invoicesummarybylocation->updateInvoiceSummaryTotalByLocation($targetYM, $target_location_id, $update_invoice_total_flag);
    }
    
    /**
     * #914 NEW-FEATURE: Develop a function in Admin site to support deleting Invoice/Payment of Deleted customer
     * delete invoice function.
     * @param type $customerId
     * @param type $invoice_id
     * @return typed
     */
    public static function deleteInvoice($invoice_id, $customer_id) {
        // load models
        ci()->load->model('invoices/invoice_summary_m');
        ci()->load->model('invoices/invoice_summary_by_location_m');
        ci()->load->model('invoices/invoice_detail_m');
        
        //libs
         ci()->load->library(array(
            'users/ion_auth',
            'pyrocache',
            "Exceptions/BusinessException",
            "Exceptions/SystemException",
            "Exceptions/DAOException",
            "Exceptions/ThirdPartyException",
        ));
        
        log_audit_message(APConstants::LOG_INFOR, 'Delete invoice ID: ' . $invoice_id . '| Customer ID:' . $customer_id);

        try{
            // Get info invoice summary
            $invoice_summary = ci()->invoice_summary_m->get_by_many(array(
                "id" => $invoice_id,
                "customer_id" => $customer_id
            ));
            log_audit_message(APConstants::LOG_INFOR, 'Get info invoice summary: ' . json_encode($invoice_summary) . '<br/>');
            
            // Delete all invoice information
            if($invoice_summary){
                // delete invoice_summary_by_location
                if($invoice_summary->invoice_month && $invoice_summary->invoice_type ){
                    $invoice_summary_by_location = ci()->invoice_summary_by_location_m->get_many_by_many(array(
                        "invoice_month" => $invoice_summary->invoice_month ,
                        "invoice_type" => $invoice_summary->invoice_type,
                        "customer_id" => $customer_id
                    ));
                    log_audit_message(APConstants::LOG_ERROR, 'Delete invoice summary by location:' . json_encode($invoice_summary_by_location) . '<br/>', false, 'DELETE_INVOICE_SUMMARY_BY_LOCATION');
                    
                    ci()->invoice_summary_by_location_m->delete_by_many(array(
                        "invoice_month" => $invoice_summary->invoice_month ,
                        "invoice_type" => $invoice_summary->invoice_type,
                        "customer_id" => $customer_id
                    ));
                }

                // delete  invoice_detail
                if($invoice_summary->id){
                    $invoice_detail = ci()->invoice_detail_m->get_many_by_many(array(
                        "invoice_summary_id" => $invoice_summary->id,
                        "customer_id" => $customer_id
                    ));
                    log_audit_message(APConstants::LOG_ERROR, 'Delete invoice detail:' . json_encode($invoice_detail) . '<br/>', false, 'DELETE_INVOICE_DETAIL');
                    
                    ci()->invoice_detail_m->delete_by_many(array(
                        "invoice_summary_id" => $invoice_summary->id,
                        "customer_id" => $customer_id
                    ));  
                }

                // delete invoice_summary
                $invoice_summary = ci()->invoice_summary_m->get_many_by_many(array(
                    "id" => $invoice_id,
                    "customer_id" => $customer_id
                ));
                log_audit_message(APConstants::LOG_ERROR, 'Delete invoice summary:' . json_encode($invoice_summary) . '<br/>', false, 'DELETE_INVOICE_SUMMARY');
                
                ci()->invoice_summary_m->delete_by_many(array(
                    "id" => $invoice_id,
                    "customer_id" => $customer_id
                ));
                
                // return 
                 return true;
            }else{
                throw new BusinessException(lang('invoice.delete_error'));
            }
                  
        }catch (BusinessException $e) {
           throw new BusinessException($e->getMesage());
        }

    }
    
     /**
     * #914 NEW-FEATURE: Develop a function in Admin site to support deleting Invoice/Payment of Deleted customer
     * delete invoice function.
     * @param type $customerId
     * @param type $invoice_id
     * @return typed
     */
    public static function deletePayment($transaction_id, $customer_id, $tran_type) {
        // load models
        ci()->load->model('payment/external_tran_hist_m');
        ci()->load->model('payment/payone_tran_hist_m');
        
        //libs
         ci()->load->library(array(
            'users/ion_auth',
            'pyrocache',
            "Exceptions/BusinessException",
            "Exceptions/SystemException",
            "Exceptions/DAOException",
            "Exceptions/ThirdPartyException",
        ));
        
        log_audit_message(APConstants::LOG_INFOR, 'Delete invoice ID: ' . $transaction_id . '| Customer ID:' . $customer_id . '| Tran type:' . $tran_type);
        
        try{
            // Delete  bank tranfer ( manual payment)
            if($tran_type == '2'){
                $manual_payment = ci()->external_tran_hist_m->get_many_by_many(array(
                    "id" => $transaction_id,
                    "customer_id" => $customer_id
                ));
                log_audit_message(APConstants::LOG_ERROR, 'Delete bank tranfer(manual payment):' . json_encode($manual_payment) . '<br/>', false, 'DELETE_EXTERNAL_PAYMENT');
                
                ci()->external_tran_hist_m->delete_by_many(array(
                    "id" => $transaction_id,
                    "customer_id" => $customer_id
                ));
                
                // Return 
                return true;
            }else if ($tran_type == '1'){ // Delete Automatic payment 
                $automatic_payment = ci()->payone_tran_hist_m->get_many_by_many(array(
                    "id" => $transaction_id,
                    "customer_id" => $customer_id
                ));
                log_audit_message(APConstants::LOG_ERROR, 'Delete automatic payment):' . json_encode($automatic_payment) . '<br/>', false, 'DELETE_PAYONE_PAYMENT'); 
                
                 ci()->payone_tran_hist_m->delete_by_many(array(
                    "id" => $transaction_id,
                    "customer_id" => $customer_id
                ));
                 
                 // Return 
                return true;
            }else{
                throw new BusinessException(lang('payment.delete_error'));
            }        
        }catch (BusinessException $e) {
            throw new BusinessException($e->getMesage());
        }
    }
    
    /**
     * Get and update current invoice VAT if customer change the address at middle of month
     * @param type $customer_id
     * @param type $target_year
     * @param type $target_month
     */
    public static function update_invoice_vat($customer_id, $target_year, $target_month, $customerVat=null) {
        ci()->load->model('invoices/invoice_summary_m');
        ci()->load->model('invoices/invoice_summary_by_location_m');
        
        $current_invoice = ci()->invoice_summary_m->get_by_many(array(
            "customer_id" => $customer_id,
            "LEFT(invoice_month, 6) = " => $target_year.$target_month,
            // auto invoice
            "(invoice_type is null OR invoice_type <> 2)" => null
        ));
        
        if(empty($current_invoice)){
            return;
        }
        
        // Calculate new VAT
        if(empty($customerVat)){
            $customerVat = APUtils::getVatRateOfCustomer($customer_id);
        }
        $vat = $customerVat->rate;
        if ($current_invoice->vat != $vat || $current_invoice->vat_case != $customerVat->vat_case_id) {
            $message = 'Update VAT of customer.'.$customer_id.' From '.$current_invoice->vat.' to '.$vat;
            $message = $message. ', and VAT Case from '.$current_invoice->vat_case. ' to '.$customerVat->vat_case_id;
            log_audit_message(APConstants::LOG_INFOR, $message,FALSE, 'update_invoice_vat');
            
            ci()->invoice_summary_m->update_by_many(array(
                "customer_id" => $customer_id,
                "LEFT(invoice_month, 6) =" => $target_year.$target_month,
                // auto invoice
                "(invoice_type is null OR invoice_type <> 2)" => null
            ), array(
                'vat' => $vat,
                'vat_case' => $customerVat->vat_case_id
            ));
            
            ci()->invoice_summary_by_location_m->update_by_many(array(
                "customer_id" => $customer_id,
                "LEFT(invoice_month, 6)= " => $target_year.$target_month,
                // auto invoice
                "(invoice_type is null OR invoice_type <> 2)" => null
            ), array(
                'vat' => $vat,
                'vat_case' => $customerVat->vat_case_id
            ));
        }
    }

    /**
     * get number of customer.
     * @param type $target_month
     * @param type $location_id
     * @return type
     */
    public static function getNumberOfCustomerBy($target_month, $location_id){
        ci()->load->model(array(
            "report/report_by_location_m",
            'report/report_by_total_m'
        ));
        
        $result = 0;
        if(empty($location_id)){
            $report = ci()->report_by_total_m->get_by('invoice_month', $target_month);
            $result = !empty($report) ? $report->number_of_customer : 0;
        }else{
            $report = ci()->report_by_location_m->get_by_many(array(
                "invoice_month" => $target_month,
                "location_id" => $location_id
            ));
            $result = !empty($report) ?$report->number_of_customers_share : 0;
        }
        
        return $result;
    }
    
    /**
     * Recalculate all postbox fee of enterprise customer.
     * 
     * @param type $enterprise_customer_id
     * @param type $customer_id 
     */
    public static function calPostboxInvoicesOfEnterpriseCustomer($enterprise_customer_id, $pricings='') {
        ci()->load->model(array(
            "customers/customer_m",
            "mailbox/postbox_m"
        ));
        ci()->load->library(array(
            "invoices/invoices",
            'price/price_api'
        ));
        
        if(empty($pricings)){
            // Gets all pricings template
            $pricings = price_api::getAllPricingsGroupByTemplate();
        }
        
        // Get all customer of this enterprise customer
        $list_customers = ci()->customer_m->get_many_by_many(array(
            "( parent_customer_id = '".$enterprise_customer_id."' OR customer_id = '".$enterprise_customer_id."' )" => null
        ));

        foreach($list_customers as $customer) {
            ci()->invoices->calculate_invoice($customer->customer_id, $pricings);
        }
    }
        
    /**
     * Gest customer setting by key.
     * @param type $customer_setting
     * @param type $key
     * @param type $alias
     * @return type
     */
    public static function get_customer_setting_by_key($customer_setting, $key, $alias=''){
        $result = 0;
        foreach($customer_setting as $row){
            if(!empty($alias) &&( $row->alias01  == 'all' || $row->alias01 == $alias)){
                if($row->setting_key == $key){
                    $result = $row->setting_value;
                    break;
                }
            }else{
                if($row->setting_key == $key){
                    $result = $row->setting_value;
                    break;
                }
            }
        }
        
        return $result;
    }
}