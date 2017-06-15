<?php defined('BASEPATH') or exit('No direct script access allowed');

class InvoiceSummaryByLocation
{
    /**
     * default constructor.
     */
    public function __construct()
    {
        ci()->load->model('mailbox/postbox_fee_month_m');
        ci()->load->model('price/pricing_m');
        ci()->load->model('customers/customer_m');
        ci()->load->model('addresses/location_m');
        ci()->load->model("invoices/invoice_summary_by_location_m");
    }
    /**
     * Calculate psotbox invoice.
     * @param unknown $postboxes
     * @param string $new_flag
     */
    public function calcPostboxInvoice($invoice_code, $customer_id, $postboxes, $new_flag=false, $pricings, $customerVat)
    {
        // Gets number day of month.
        $start_day_of_month = APUtils::getFirstDayOfCurrentMonth();
        $end_day_of_month = APUtils::getLastDayOfCurrentMonth();
    
        // tinh so ngay can tinh toan invoice. set la 1 neu chua tinh invoice tung ngay.
        $number_of_day_month = APUtils::getDateDiff($start_day_of_month, $end_day_of_month);

        foreach($postboxes as $postbox) {
            $postbox_account_type = $postbox->type;
            $location_id = $postbox->location_available_id;
    
            // Get pricings by template 
            $pricing_map = $pricings[$postbox->pricing_template_id];

            // Get so ngay can tinh phi postbox.
            $number_day_must_be_calculated_fee = $number_of_day_month;
            
            if($new_flag){
                // truong hop tao moi private hoac business. hoac upgrade len business postbox.
                if(!empty($postbox->apply_date)){
                    $number_day_must_be_calculated_fee = APUtils::getDateDiff($postbox->apply_date, $end_day_of_month);
                }
                
                $number_day_must_be_calculated_fee = ($number_day_must_be_calculated_fee <= $number_of_day_month) ? $number_day_must_be_calculated_fee : $number_of_day_month;

                // update postbox invoice
                $this->updatePostboxInvoice($invoice_code, $customer_id, $postbox, $postbox_account_type, $location_id, $number_day_must_be_calculated_fee, $number_of_day_month, $pricing_map, $new_flag, $customerVat);
            
                // tinh toan cho as you go fee, neu upgrade tu free len private/business.
                if(!empty($postbox->apply_date)){
                    $postbox_created_date = date("Ymd",$postbox->created_date);
                    $number_days_duration = APUtils::getDateDiff($postbox_created_date, $postbox->apply_date) -1;
                    $actual_days_calculation_as_you_go_fee = APUtils::getDateDiff(APUtils::getFirstDayOfCurrentMonth(), $postbox->apply_date)-1;
                    $free_postbox_netprice = $pricing_map [APConstants::FREE_TYPE] ['postbox_fee_as_you_go'];
                    $number_day_must_be_calculated_as_you_go_fee = 0;
                    if($number_days_duration >= $pricing_map [APConstants::FREE_TYPE] ['as_you_go']){
                        $free_postbox_quantity = 1;
                        $number_day_must_be_calculated_as_you_go_fee = $number_days_duration - $pricing_map [APConstants::FREE_TYPE] ['as_you_go'];

                        if($number_day_must_be_calculated_as_you_go_fee >= $actual_days_calculation_as_you_go_fee){
                            $number_day_must_be_calculated_as_you_go_fee = $actual_days_calculation_as_you_go_fee;
                        }
                        // donot calculate free postbox 
                        if($number_day_must_be_calculated_as_you_go_fee <=0){
                            $number_day_must_be_calculated_as_you_go_fee = 0;
                        }
                    }

                    // dont calculate the free postbox in 6 months
                    if($number_day_must_be_calculated_as_you_go_fee > 0){
                        // update postbox invoice
                        $postbox_type = 1; // as you go
                        $this->updatePostboxInvoice($invoice_code, $customer_id, $postbox, $postbox_type, $location_id, $number_day_must_be_calculated_as_you_go_fee, $number_of_day_month, $pricing_map, false, $customerVat);
                    }
                }
            }else{
                $postbox_created_date = APUtils::convert_timestamp_to_date($postbox->created_date, 'Ymd');
                if($postbox_created_date > $start_day_of_month){
                    $number_day_must_be_calculated_fee = APUtils::getDateDiff($postbox_created_date, $end_day_of_month);
                }
                
                $number_day_must_be_calculated_fee = ($number_day_must_be_calculated_fee <= $number_of_day_month) ? $number_day_must_be_calculated_fee : $number_of_day_month;
    
                // update postbox invoice
                $this->updatePostboxInvoice($invoice_code, $customer_id, $postbox, $postbox_account_type, $location_id, $number_day_must_be_calculated_fee, $number_of_day_month, $pricing_map, $new_flag, $customerVat);
            }
            
        }
    }
    
    /**
     * calculate free postbox 
     * @param type $customer_id
     */
    public function calcFreePostboxInvoice($invoice_code, $customer_id, $postboxes, $pricings, $customerVAT){
        // Gets number day of month.
        $start_day_of_month = APUtils::getFirstDayOfCurrentMonth();
        $end_day_of_month = APUtils::getLastDayOfCurrentMonth();
    
        // tinh so ngay can tinh toan invoice. set la 1 neu chua tinh invoice tung ngay.
        $number_of_day_month = APUtils::getDateDiff($start_day_of_month, $end_day_of_month);

        foreach($postboxes as $postbox) {
            $postbox_account_type = $postbox->type;
            $location_id = $postbox->location_available_id;
    
            // Get pricings by template 
            $pricing_map = $pricings[$postbox->pricing_template_id];

            if(empty($postbox->created_date)){
                continue;
            }
            
            // created date.
            $postbox_created_date = date("Ymd",$postbox->created_date);

            // Get so ngay can tinh phi postbox.
            $number_day_must_be_calculated_fee = 0;
            
            // get days from start day of month.
            $number_day_end_of_month = APUtils::getDateDiff($end_day_of_month, $postbox_created_date)-1;

            if($number_day_end_of_month >= $pricing_map [$postbox_account_type] ['as_you_go'] + $number_of_day_month){
                $number_day_must_be_calculated_fee = $number_of_day_month;
            }else{
                $number_day_must_be_calculated_fee = $number_day_end_of_month - $pricing_map [$postbox_account_type] ['as_you_go'];
            }
            
            // dont calculate the free postbox in 6 months
            if($number_day_must_be_calculated_fee <=0){
                continue;
            }
    
            // update postbox invoice
            $this->updatePostboxInvoice($invoice_code, $customer_id, $postbox, $postbox_account_type, $location_id, $number_day_must_be_calculated_fee, $number_of_day_month, $pricing_map, false, $customerVAT);
        }
    }
    
    /**
     * update invoice summary total by lcoation
     * @param type $targetYM
     */
    public function updateInvoiceSummaryTotalByLocation($targetYM, $target_location_id='', $update_invoice_total_flag = false){
        ci()->load->library(array(
            'price/price_api',
            'payment/payment_api',
            "report/report_api"
        ));
        ci()->load->model(array(
            'addresses/location_m',
            'scans/envelope_shipping_m',
            "report/report_by_location_m",
            "report/report_by_total_m",
            "invoices/invoice_summary_by_location_m",
            "invoices/invoice_summary_m",
        ));
        
        ini_set('memory_limit', '-1');
        
        // update invoices
        $list_invoices = ci()->report_by_location_m->get_invoice_total_by_location($targetYM);
        $list_share_invoices = ci()->report_by_location_m->get_invoice_total_by_location($targetYM, true);

        foreach($list_invoices as $invoice_by_location){
            $location_id = $invoice_by_location->location_id;
            
            if(empty($location_id) || (!empty($target_location_id) && $location_id != $target_location_id) ){
                continue;
            }

            // gets rev share value.
            $rev_price = price_api::getRevShareMapByLocationID($location_id);
            $pricing = price_api::getPricingMapByLocationId($location_id);
            $invoice = $this->calTotalInvoiceSummaryByLocation($invoice_by_location, $rev_price, $targetYM, $location_id, $list_share_invoices);

            $data = array(
                "free_postboxes_amount" => $invoice->free_postboxes_amount,
                "private_postboxes_amount" => $invoice->private_postboxes_amount,
                "business_postboxes_amount" => $invoice->business_postboxes_amount,
                
                "incomming_items_free_account" => $invoice->incomming_items_free_account,
                "incomming_items_private_account" => $invoice->incomming_items_private_account,
                "incomming_items_business_account" => $invoice->incomming_items_business_account,
                
                "envelope_scan_free_account" => $invoice->envelope_scan_free_account,
                "envelope_scan_private_account" => $invoice->envelope_scan_private_account,
                "envelope_scan_business_account" => $invoice->envelope_scan_business_account,
                
                "item_scan_free_account" => $invoice->item_scan_free_account,
                "item_scan_private_account" => $invoice->item_scan_private_account,
                "item_scan_business_account" => $invoice->item_scan_business_account,
                
                "direct_shipping_free_account" => $invoice->direct_shipping_free_account,
                "direct_shipping_private_account" => $invoice->direct_shipping_private_account,
                "direct_shipping_business_account" => $invoice->direct_shipping_business_account,
                
                "collect_shipping_free_account" => $invoice->collect_shipping_free_account,
                "collect_shipping_private_account" => $invoice->collect_shipping_private_account,
                "collect_shipping_business_account" => $invoice->collect_shipping_business_account,
                
                "storing_letters_free_account" => $invoice->storing_letters_free_account,
                "storing_letters_private_account" => $invoice->storing_letters_private_account,
                "storing_letters_business_account" => $invoice->storing_letters_business_account,
                "storing_packages_free_account" => $invoice->storing_packages_free_account,
                "storing_packages_private_account" => $invoice->storing_packages_private_account,
                "storing_packages_business_account" => $invoice->storing_packages_business_account,
                
                "forwarding_charges_postal" => $invoice->forwarding_charges_postal,
                "forwarding_charges_fee" => $invoice->forwarding_charges_fee,
                
                //"cash_payment_for_item_delivery_amount" => $invoice->cash_payment_for_item_delivery_amount,
                "cash_payment_free_for_item_delivery_amount" => $invoice->cash_payment_free_for_item_delivery_amount,
                
                "customs_cost_import_amount" => $invoice->customs_cost_import_amount,
                "customs_handling_fee_import_amount" => $invoice->customs_handling_fee_import_amount,
                "address_verification_amount" => $invoice->address_verification_amount,
                "special_service_fee_in_15min_intervalls_amount" => $invoice->special_service_fee_in_15min_intervalls_amount,
                "personal_pickup_charge_amount" => $invoice->personal_pickup_charge_amount,

                "additional_pages_scanning_free_amount" => $invoice->additional_pages_scanning_free_amount,
                "additional_pages_scanning_private_amount" => $invoice->additional_pages_scanning_private_amount,
                "additional_pages_scanning_business_amount" => $invoice->additional_pages_scanning_business_amount,
                
                "custom_declaration_outgoing_price_01" => $invoice->custom_declaration_outgoing_price_01,
                "custom_declaration_outgoing_price_02" => $invoice->custom_declaration_outgoing_price_02,
                
                // share rev
                "free_postboxes_share_rev" => $invoice->free_postboxes_share_rev,
                "private_postboxes_share_rev" => $invoice->private_postboxes_share_rev,
                "business_postboxes_share_rev" => $invoice->business_postboxes_share_rev,
                
                "incomming_items_free_share_rev" => $invoice->incomming_items_free_share_rev,
                "incomming_items_private_share_rev" => $invoice->incomming_items_private_share_rev,
                "incomming_items_business_share_rev" => $invoice->incomming_items_business_share_rev,
                
                "envelope_scan_free_share_rev" => $invoice->envelope_scan_free_share_rev,
                "envelope_scan_private_share_rev" => $invoice->envelope_scan_private_share_rev,
                "envelope_scan_business_share_rev" => $invoice->envelope_scan_business_share_rev,
                
                "item_scan_free_share_rev" => $invoice->item_scan_free_share_rev,
                "item_scan_private_share_rev" => $invoice->item_scan_private_share_rev,
                "item_scan_business_share_rev" => $invoice->item_scan_business_share_rev,
                
                "direct_shipping_free_share_rev" => $invoice->direct_shipping_free_share_rev,
                "direct_shipping_private_share_rev" => $invoice->direct_shipping_private_share_rev,
                "direct_shipping_business_share_rev" => $invoice->direct_shipping_business_share_rev,
                
                "collect_shipping_free_share_rev" => $invoice->collect_shipping_free_share_rev,
                "collect_shipping_private_share_rev" => $invoice->collect_shipping_private_share_rev,
                "collect_shipping_business_share_rev" => $invoice->collect_shipping_business_share_rev,
                
                "storing_letters_free_share_rev" => $invoice->storing_letters_free_share_rev,
                "storing_letters_private_share_rev" => $invoice->storing_letters_private_share_rev,
                "storing_letters_business_share_rev" => $invoice->storing_letters_business_share_rev,
                
                "storing_packages_free_share_rev" => $invoice->storing_packages_free_share_rev,
                "storing_packages_private_share_rev" => $invoice->storing_packages_private_share_rev,
                "storing_packages_business_share_rev" => $invoice->storing_packages_business_share_rev,
                
                "forwarding_charges_postal_share_rev" => $invoice->forwarding_charges_postal_share_rev,
                "forwarding_charges_fee_share_rev" => $invoice->forwarding_charges_fee_share_rev,
                
                //"cash_payment_for_item_delivery_share_rev" => $invoice->cash_payment_for_item_delivery_share_rev,
                "cash_payment_free_for_item_delivery_share_rev" => $invoice->cash_payment_free_for_item_delivery_share_rev,
                
                "customs_cost_import_share_rev" => $invoice->customs_cost_import_share_rev,
                "customs_handling_fee_import_share_rev" => $invoice->customs_handling_fee_import_share_rev,
                
                "address_verification_share_rev" => $invoice->address_verification_share_rev,
                "special_service_fee_in_15min_intervalls_share_rev" => $invoice->special_service_fee_in_15min_intervalls_share_rev,
                "personal_pickup_charge_share_rev" => $invoice->personal_pickup_charge_share_rev,
                "paypal_fee_share_rev" => $invoice->paypal_fee_share_rev,
                "other_local_invoice_share_rev" => $invoice->other_local_invoice_share_rev,
                "credit_note_given_share_rev" => $invoice->credit_note_given_share_rev,
                
                "additional_pages_scanning_free_share_rev" => $invoice->additional_pages_scanning_free_share_rev,
                "additional_pages_scanning_private_share_rev" => $invoice->additional_pages_scanning_private_share_rev,
                "additional_pages_scanning_business_share_rev" => $invoice->additional_pages_scanning_business_share_rev,
                
                "custom_declaration_outgoing_price_01_share_rev" => $invoice->custom_declaration_outgoing_price_01_share_rev,
                "custom_declaration_outgoing_price_02_share_rev" => $invoice->custom_declaration_outgoing_price_02_share_rev,
                
                // Share value
                "free_postboxes_amount_share" => $invoice->free_postboxes_share,
                "private_postboxes_amount_share" => $invoice->private_postboxes_share,
                "business_postboxes_amount_share" => $invoice->business_postboxes_share,
                
                "incomming_items_free_account_share" => $invoice->incomming_items_free_share,
                "incomming_items_private_account_share" => $invoice->incomming_items_private_share,
                "incomming_items_business_account_share" => $invoice->incomming_items_business_share,
                
                "envelope_scan_free_account_share" => $invoice->envelope_scan_free_share,
                "envelope_scan_private_account_share" => $invoice->envelope_scan_private_share,
                "envelope_scan_business_account_share" => $invoice->envelope_scan_business_share,
                
                "item_scan_free_account_share" => $invoice->item_scan_free_share,
                "item_scan_private_account_share" => $invoice->item_scan_private_share,
                "item_scan_business_account_share" => $invoice->item_scan_business_share,
                
                "direct_shipping_free_account_share" => $invoice->direct_shipping_free_share,
                "direct_shipping_private_account_share" => $invoice->direct_shipping_private_share,
                "direct_shipping_business_account_share" => $invoice->direct_shipping_business_share,
                
                "collect_shipping_free_account_share" => $invoice->collect_shipping_free_share,
                "collect_shipping_private_account_share" => $invoice->collect_shipping_private_share,
                "collect_shipping_business_account_share" => $invoice->collect_shipping_business_share,
                
                "storing_letters_free_account_share" => $invoice->storing_letters_free_share,
                "storing_letters_private_account_share" => $invoice->storing_letters_private_share,
                "storing_letters_business_account_share" => $invoice->storing_letters_business_share,
                
                "storing_packages_free_account_share" => $invoice->storing_packages_free_share,
                "storing_packages_private_account_share" => $invoice->storing_packages_private_share,
                "storing_packages_business_account_share" => $invoice->storing_packages_business_share,
                
                "forwarding_charges_postal_share" => $invoice->forwarding_charges_postal_share,
                "forwarding_charges_fee_share" => $invoice->forwarding_charges_fee_share,
                
                //"cash_payment_for_item_delivery_amount_share" => $invoice->cash_payment_for_item_delivery_amount_share,
                "cash_payment_free_for_item_delivery_amount_share" => $invoice->cash_payment_free_for_item_delivery_amount_share,
                
                "customs_cost_import_amount_share" => $invoice->customs_cost_import_share,
                "customs_handling_fee_import_amount_share" => $invoice->customs_handling_fee_import_share,
                
                "address_verification_amount_share" => $invoice->address_verification_share,
                "special_service_fee_in_15min_intervalls_amount_share" => $invoice->special_service_fee_in_15min_intervalls_share,
                "personal_pickup_charge_amount_share" => $invoice->personal_pickup_charge_share,
                "paypal_fee_share" => $invoice->paypal_fee_share,
                "other_local_invoice_share" => $invoice->other_local_invoice_share,
                "credit_note_given_share" => $invoice->credit_note_given_share,
                
                "additional_pages_scanning_free_amount_share" => $invoice->additional_pages_scanning_free_share,
                "additional_pages_scanning_private_amount_share" => $invoice->additional_pages_scanning_private_share,
                "additional_pages_scanning_business_amount_share" => $invoice->additional_pages_scanning_business_share,
                
                "custom_declaration_outgoing_price_01_amount_share" => $invoice->custom_declaration_outgoing_price_01_share,
                "custom_declaration_outgoing_price_02_amount_share" => $invoice->custom_declaration_outgoing_price_02_share,
                
                // other fee
                "paypal_fee" => $invoice->paypal_fee,
                "other_local_invoice" => $invoice->other_local_invoice,
                "credit_note_given" => $invoice->credit_note_given,
                
                "net_total_invoice" =>$invoice->net_total_invoice,
                "gross_total_invoice" => $invoice->gross_total_invoice,
                "share_total_invoice" => $invoice->share_total_invoice,
                
                //"number_of_account" => $invoice->number_of_account,
                "number_of_account_share" => (int)$invoice->number_of_account_share,
                "number_of_postbox" => (int)$invoice->number_of_postbox,
                "number_of_postbox_share" => (int)$invoice->number_of_postbox_share,
                "number_of_item_received" => (int)$invoice->number_of_item_received,
                "number_of_item_received_share" => (int)$invoice->number_of_item_received_share,
                "number_of_envelope_scan" => (int)$invoice->number_of_envelope_scan,
                "number_of_envelope_scan_share" => (int)$invoice->number_of_envelope_scan_share,
                "number_of_item_scan" => (int)$invoice->number_of_item_scan,
                "number_of_item_scan_share" => (int)$invoice->number_of_item_scan_share,
                "number_of_item_forwarded" => (int)$invoice->number_of_item_forwarded,
                "number_of_item_forwarded_share" => (int)$invoice->number_of_item_forwarded_share,
                "number_of_storage_item" => (int)$invoice->number_of_storage_item,
                "number_of_storage_item_share" => (int)$invoice->number_of_storage_item_share,
                //"number_of_new_registration" => $invoice->number_of_new_registration,
                "number_of_new_registration_share" => (int)$invoice->number_of_new_registration_share,
                //"number_of_never_activated_deleted" => $invoice->number_of_never_activated_deleted,
                "number_of_never_activated_deleted_share" => (int)$invoice->number_of_never_activated_deleted_share,
                //"number_of_manual_deleted" => $invoice->number_of_manual_deleted,
                "number_of_manual_deleted_share" => (int)$invoice->number_of_manual_deleted_share,
                //"number_of_automatic_deleted" => $invoice->number_of_automatic_deleted,
                "number_of_automatic_deleted_share" =>(int) $invoice->number_of_automatic_deleted_share,
                //"number_of_customers" => $invoice->number_of_customers,
                "number_of_customers_share" => (int)$invoice->number_of_customers_share,
                "free_postbox_quantity" => $invoice->free_postbox_quantity,
                "free_postbox_quantity_share" => $invoice->free_postbox_quantity_share,
                "private_postbox_quantity" => $invoice->private_postbox_quantity,
                "private_postbox_quantity_share" => $invoice->private_postbox_quantity_share,
                "business_postbox_quantity" => $invoice->business_postbox_quantity,
                "business_postbox_quantity_share" => $invoice->business_postbox_quantity_share,
                "free_incoming_quantity" => $invoice->free_incoming_quantity,
                "free_incoming_quantity_share" => $invoice->free_incoming_quantity_share,
                "private_incoming_quantity" => $invoice->private_incoming_quantity,
                "private_incoming_quantity_share" => $invoice->private_incoming_quantity_share,
                "business_incoming_item_quantity" => $invoice->business_incoming_item_quantity,
                "business_incoming_item_quantity_share" => $invoice->business_incoming_item_quantity_share,
                "free_envelope_scan_quantity" => $invoice->free_envelope_scan_quantity,
                "free_envelope_scan_quantity_share" => $invoice->free_envelope_scan_quantity_share,
                "private_envelope_scan_quantity" => $invoice->private_envelope_scan_quantity,
                "private_envelope_scan_quantity_share" => $invoice->private_envelope_scan_quantity_share,
                "business_envelope_scan_quantity" => $invoice->business_envelope_scan_quantity,
                "business_envelope_scan_quantity_share" => $invoice->business_envelope_scan_quantity_share,
                "free_item_scan_quantity" => $invoice->free_item_scan_quantity,
                "free_item_scan_quantity_share" => $invoice->free_item_scan_quantity_share,
                "private_item_scan_quantity" => $invoice->private_item_scan_quantity,
                "private_item_scan_quantity_share" => $invoice->private_item_scan_quantity_share,
                "business_item_scan_quantity" => $invoice->business_item_scan_quantity,
                "business_item_scan_quantity_share" => $invoice->business_item_scan_quantity_share,
                "free_additional_page_quantity" => $invoice->free_additional_page_quantity,
                "free_additional_page_quantity_share" => $invoice->free_additional_page_quantity_share,
                "private_additional_page_quantity" => $invoice->private_additional_page_quantity,
                "private_additional_page_quantity_share" => $invoice->private_additional_page_quantity_share,
                "business_additional_page_quantity" => $invoice->business_additional_page_quantity,
                "business_additional_page_quantity_share" => $invoice->business_additional_page_quantity_share,
                "fowarding_charge_postal_quantity" => $invoice->fowarding_charge_postal_quantity,
                "fowarding_charge_postal_quantity_share" => $invoice->fowarding_charge_postal_quantity_share,
                "fowarding_charge_fee_quantity" => $invoice->fowarding_charge_fee_quantity,
                "fowarding_charge_fee_quantity_share" => $invoice->fowarding_charge_fee_quantity_share,
                "free_storage_letter_quanity" => $invoice->free_storage_letter_quanity,
                "free_storage_letter_quanity_share" => $invoice->free_storage_letter_quanity_share,
                "private_storage_letter_quanity" => $invoice->private_storage_letter_quanity,
                "private_storage_letter_quanity_share" => $invoice->private_storage_letter_quanity_share,
                "business_storage_letter_quanity" => $invoice->business_storage_letter_quanity,
                "business_storage_letter_quanity_share" => $invoice->business_storage_letter_quanity_share,
                "free_storage_package_quanity" => $invoice->free_storage_package_quanity,
                "free_storage_package_quanity_share" => $invoice->free_storage_package_quanity_share,
                "private_storage_package_quanity" => $invoice->private_storage_package_quanity,
                "private_storage_package_quanity_share" => $invoice->private_storage_package_quanity_share,
                "business_storage_package_quanity" => $invoice->business_storage_package_quanity,
                "business_storage_package_quanity_share" => $invoice->business_storage_package_quanity_share,
                "custom_declaration_quantity" => $invoice->custom_declaration_quantity,
                "custom_declaration_quantity_share" => $invoice->custom_declaration_quantity_share,
                "cash_payment_fee_quantity" => $invoice->cash_payment_fee_quantity,
                "cash_payment_fee_quantity_share" => $invoice->cash_payment_fee_quantity_share,
                "custom_cost_import_quantity" => $invoice->custom_cost_import_quantity,
                "custom_cost_import_quantity_share" => $invoice->custom_cost_import_quantity_share,
                "import_custom_fee_quantity" => $invoice->import_custom_fee_quantity,
                "import_custom_fee_quantity_share" => $invoice->import_custom_fee_quantity_share,
                "address_verification_quantity" => $invoice->address_verification_quantity,
                "address_verification_quantity_share" => $invoice->address_verification_quantity_share,
                "special_service_fee_quantity" => $invoice->special_service_fee_quantity,
                "special_service_fee_quantity_share" => $invoice->special_service_fee_quantity_share,
                "peronsal_pickup_charge_quantity" => $invoice->peronsal_pickup_charge_quantity,
                "peronsal_pickup_charge_quantity_share" => $invoice->peronsal_pickup_charge_quantity_share,
                "paypal_transaction_fee_quantity" => $invoice->paypal_transaction_fee_quantity,
                "paypal_transaction_fee_quantity_share" => $invoice->paypal_transaction_fee_quantity_share,
                "other_local_invoice_quantity" => $invoice->other_local_invoice_quantity,
                "other_local_invoice_quantity_share" => $invoice->other_local_invoice_quantity_share,
                "creditnote_quantity" => $invoice->creditnote_quantity,
                "creditnote_quantity_share" => $invoice->creditnote_quantity_share,
                
                // net price
                "free_postboxes_netprice" => $pricing[APConstants::FREE_TYPE]['postbox_fee_as_you_go']->item_value,
                "private_postboxes_netprice" => $pricing[APConstants::PRIVATE_TYPE]['postbox_fee']->item_value,
                "business_postboxes_netprice" => $pricing[APConstants::BUSINESS_TYPE]['postbox_fee']->item_value,
                
                "incomming_items_free_netprice" => $pricing[APConstants::FREE_TYPE]['additional_incomming_items']->item_value,
                "incomming_items_private_netprice" => $pricing[APConstants::PRIVATE_TYPE]['additional_incomming_items']->item_value,
                "incomming_items_business_netprice" => $pricing[APConstants::BUSINESS_TYPE]['additional_incomming_items']->item_value,
                
                "envelope_scan_free_netprice" => $pricing[APConstants::FREE_TYPE]['envelop_scanning']->item_value,
                "envelope_scan_private_netprice" => $pricing[APConstants::PRIVATE_TYPE]['envelop_scanning']->item_value,
                "envelope_scan_business_netprice" => $pricing[APConstants::BUSINESS_TYPE]['envelop_scanning']->item_value,
                
                "item_scan_free_netprice" => $pricing[APConstants::FREE_TYPE]['opening_scanning']->item_value,
                "item_scan_private_netprice" => $pricing[APConstants::PRIVATE_TYPE]['opening_scanning']->item_value,
                "item_scan_business_netprice" => $pricing[APConstants::BUSINESS_TYPE]['opening_scanning']->item_value,
                
                "additional_pages_scanning_free_netprice" => $pricing[APConstants::FREE_TYPE]['additional_pages_scanning_price']->item_value,
                "additional_pages_scanning_private_netprice" => $pricing[APConstants::PRIVATE_TYPE]['additional_pages_scanning_price']->item_value,
                "additional_pages_scanning_business_netprice" => $pricing[APConstants::BUSINESS_TYPE]['additional_pages_scanning_price']->item_value,
                
                "storing_letters_free_netprice" => $pricing[APConstants::FREE_TYPE]['storing_items_over_free_letter']->item_value,
                "storing_letters_private_netprice" => $pricing[APConstants::PRIVATE_TYPE]['storing_items_over_free_letter']->item_value,
                "storing_letters_business_netprice" => $pricing[APConstants::BUSINESS_TYPE]['storing_items_over_free_letter']->item_value,
                
                "storing_packages_free_netprice" => $pricing[APConstants::FREE_TYPE]['storing_items_over_free_packages']->item_value,
                "storing_packages_private_netprice" => $pricing[APConstants::PRIVATE_TYPE]['storing_items_over_free_packages']->item_value,
                "storing_packages_business_netprice" => $pricing[APConstants::BUSINESS_TYPE]['storing_items_over_free_packages']->item_value,
            );
            $invoice_check = ci()->report_by_location_m->get_by_many(array(
                "location_id" => $location_id,
                "invoice_month" => $targetYM
            ));
            
            if($invoice_check){
                ci()->report_by_location_m->update_by_many(array(
                    "invoice_month" => $targetYM,
                    "location_id" => $location_id
                ), $data);
            }else{
                $data ['invoice_month'] = $targetYM;
                $data ['location_id'] = $location_id;
                //$data ['invoice_type'] = $invoice->invoice_type;
                ci()->report_by_location_m->insert($data);
            }
        }//end foeach.
        
        // check update total invoice.
        if(!$update_invoice_total_flag){
            return;
        }
        // update total line.
        $receipt_month = substr($targetYM, 4, 2).'.'.substr($targetYM, 0, 4);
        
        // Gets location report datas
        $data_total = report_api::getLocationReportDatas($targetYM, null, null, $receipt_month);
        $invoice_total_check = ci()->report_by_total_m->get_by("invoice_month", $targetYM);

        if(empty($invoice_total_check)){
            ci()->report_by_total_m->insert(array(
                'invoice_month' => $targetYM,
                'number_of_account' => (int)$data_total['number_customers']['number_of_account'],
                'number_of_postbox' => (int)$data_total['numberPostboxs']['total'],
                'item_received' => (int)$data_total['numberEnvelopes']['received_num'],
                'envelope_scanned' => (int)$data_total['numberEnvelopes']['envelope_scanned_num'],
                'item_scanned' => (int)$data_total['numberEnvelopes']['item_scanned_num'],
                'item_forwarded' => (int)$data_total['numberEnvelopes']['forwarded_num'],
                'item_on_storage' => (int)$data_total['numberEnvelopes']['storage_num'],
                'new_registration' => (int)$data_total['number_customers']['new_registration'],
                'never_activated_deleted' => (int)$data_total['number_customers']['number_never_activated_deleted'],
                'manually_deleted' => (int)$data_total['number_customers']['number_manually_deleted'],
                'automatically_deleted' => (int)$data_total['number_customers']['number_automatic_deleted'],
                'number_of_customer' => (int)$data_total['number_customers']['number_of_customer'],
                'cash_expenditure_of_partner' => (double)$data_total['otherCashExpenditure'],
                'created_date' => now()
            ));
        }else{
            ci()->report_by_total_m->update_by_many(array(
                "invoice_month" => $targetYM
            ),array(
                'number_of_account' => (int)$data_total['number_customers']['number_of_account'],
                'number_of_postbox' => (int)$data_total['numberPostboxs']['total'],
                'item_received' => (int)$data_total['numberEnvelopes']['received_num'],
                'envelope_scanned' => (int)$data_total['numberEnvelopes']['envelope_scanned_num'],
                'item_scanned' => (int)$data_total['numberEnvelopes']['item_scanned_num'],
                'item_forwarded' => (int)$data_total['numberEnvelopes']['forwarded_num'],
                'item_on_storage' => (int)$data_total['numberEnvelopes']['storage_num'],
                'new_registration' => (int)$data_total['number_customers']['new_registration'],
                'never_activated_deleted' => (int)$data_total['number_customers']['number_never_activated_deleted'],
                'manually_deleted' => (int)$data_total['number_customers']['number_manually_deleted'],
                'automatically_deleted' => (int)$data_total['number_customers']['number_automatic_deleted'],
                'number_of_customer' => (int)$data_total['number_customers']['number_of_customer'],
                'cash_expenditure_of_partner' => (double)$data_total['otherCashExpenditure'],
            ));
        }
    }
    
    /**
     * Gets pricings by template id
     * 
     * @param unknown $pricing_template_id
     * @return multitype:multitype:
     */
    private function getPricingByTemplateId($pricing_template_id)
    {
        // Get pricing template of postbox.
        $pricings = ci()->pricing_m->get_many_by_many(array(
                "pricing_template_id" => $pricing_template_id
        ));
        $pricing_map = array();
        foreach ($pricings as $price)
        {
            if (!array_key_exists($price->account_type, $pricing_map))
            {
                $pricing_map [$price->account_type] = array();
            }
            $pricing_map [$price->account_type] [$price->item_name] = $price->item_value;
        }
        
        return $pricing_map;
    }
    
    /**
     * update postbox invoice by location.
     * 
     * @param unknown $customer_id
     * @param unknown $postbox_account_type
     * @param unknown $location_id
     * @param unknown $number_day_must_be_calculated_fee
     * @param unknown $number_of_day_month
     * @param unknown $pricing_map
     * @param unknown $new_flag
     */
    private function updatePostboxInvoice($invoice_code, $customer_id, $postbox, $postbox_account_type, $location_id, $number_day_must_be_calculated_fee, $number_of_day_month, $pricing_map, $new_flag, $customerVat)
    {
        $target_month = APUtils::getCurrentMonthInvoice();
        $target_year = APUtils::getCurrentYearInvoice();
        
        $vat = $customerVat->rate;
        $vat_case_id = $customerVat->vat_case_id;
        
        $free_postbox_quantity = 0;
        $private_postbox_quantity = 0;
        $business_postbox_quantity = 0;
        
        $free_postbox_amount = 0;
        $private_postbox_amount = 0;
        $business_postbox_amount= 0;
        
        $free_postbox_netprice = $pricing_map [APConstants::FREE_TYPE] ['postbox_fee_as_you_go'];
        $private_postbox_netprice = $pricing_map [$postbox_account_type] ['postbox_fee'] ;
        $business_postbox_netprice = $pricing_map [$postbox_account_type] ['postbox_fee'];
        
        $number_day_must_be_calculated_fee = $number_day_must_be_calculated_fee >= $number_of_day_month ? $number_of_day_month: $number_day_must_be_calculated_fee;
        if ($postbox_account_type === APConstants::PRIVATE_TYPE) {
            $private_postbox_quantity = 1;
            $private_postbox_amount = $private_postbox_quantity * $private_postbox_netprice * $number_day_must_be_calculated_fee / $number_of_day_month;
        }else if ($postbox_account_type === APConstants::BUSINESS_TYPE) {
            $business_postbox_quantity = 1;
            $business_postbox_amount = $business_postbox_quantity * $business_postbox_netprice * $number_day_must_be_calculated_fee / $number_of_day_month;
        }else{
            if($number_day_must_be_calculated_fee > 0){
                $free_postbox_quantity = 1;
                $free_postbox_amount = $free_postbox_quantity * $pricing_map [APConstants::FREE_TYPE] ['postbox_fee_as_you_go'] * $number_day_must_be_calculated_fee / $number_of_day_month;
            }
        }
        
        // calculate as you go fee
        if($new_flag){
            // created date.
            $postbox_created_date = date("Ymd",$postbox->created_date);
            $postbox_apply_date = $postbox->apply_date;

            $number_days_duration = APUtils::getDateDiff($postbox_created_date, $postbox_apply_date) -1;
            $actual_days_calculation_as_you_go_fee = APUtils::getDateDiff(APUtils::getFirstDayOfCurrentMonth(), $postbox_apply_date)-1;
            $number_day_must_be_calculated_as_you_go_fee = 0;
            if($number_days_duration > $pricing_map [APConstants::FREE_TYPE] ['as_you_go']){
                $free_postbox_quantity = 1;
                $number_day_must_be_calculated_as_you_go_fee = $number_days_duration - $pricing_map [APConstants::FREE_TYPE] ['as_you_go'];
                $number_day_must_be_calculated_as_you_go_fee = $number_day_must_be_calculated_as_you_go_fee >= $actual_days_calculation_as_you_go_fee ? $actual_days_calculation_as_you_go_fee : $number_day_must_be_calculated_as_you_go_fee;
                $free_postbox_amount = $pricing_map [APConstants::FREE_TYPE] ['postbox_fee_as_you_go'] * $number_day_must_be_calculated_as_you_go_fee / $number_of_day_month;
            }
        }
        
        if($postbox_account_type == APConstants::ENTERPRISE_CUSTOMER){
            $business_postbox_netprice = $pricing_map [$postbox_account_type] ['postbox_fee'];
            $business_postbox_quantity = 1;
            $business_postbox_amount = $business_postbox_quantity * $business_postbox_netprice;
        }
        
        $invoice_check = ci()->invoice_summary_by_location_m->get_by_many(array(
            'invoice_month' => $target_year . $target_month,
            'customer_id' => $customer_id,
            "location_id" => $postbox->location_available_id,
            "invoice_type" => '1'
        ));
        
        if($invoice_check){
            $free_postbox_amount += $invoice_check->free_postboxes_amount;
            $free_postbox_quantity += $invoice_check->free_postboxes_quantity;
            
            $private_postbox_amount += $invoice_check->private_postboxes_amount;
            $private_postbox_quantity += $invoice_check->private_postboxes_quantity ;
            
            $business_postbox_amount += $invoice_check->business_postboxes_amount;
            $business_postbox_quantity += $invoice_check->business_postboxes_quantity;

            // case: upgrade postbox type.
            if($new_flag){
                // update data
                if ($postbox_account_type === APConstants::PRIVATE_TYPE) {
                    ci()->invoice_summary_by_location_m->update_by_many(array(
                        'invoice_month' => $target_year . $target_month,
                        'customer_id' => $customer_id,
                        "location_id" => $location_id
                    ), array(
                        "private_postboxes_amount" => $private_postbox_amount,
                        "private_postboxes_quantity" => $private_postbox_quantity,
                        "private_postboxes_netprice" => $private_postbox_netprice,
                        
                        'invoice_code' => $invoice_code,
                        "vat" => $vat,
                        "vat_case" => $vat_case_id
                    ));
                } else if ($postbox_account_type === APConstants::BUSINESS_TYPE || $postbox_account_type == APConstants::ENTERPRISE_CUSTOMER) {
                    ci()->invoice_summary_by_location_m->update_by_many(array(
                        'invoice_month' => $target_year . $target_month,
                        'customer_id' => $customer_id,
                        "location_id" => $location_id
                    ), array(
                        "business_postboxes_amount" => $business_postbox_amount,
                        "business_postboxes_quantity" => $business_postbox_quantity,
                        "business_postboxes_netprice" => $business_postbox_netprice,
                        
                        'invoice_code' => $invoice_code,
                        "vat" => $vat,
                        "vat_case" => $vat_case_id
                    ));
                }else{
                    ci()->invoice_summary_by_location_m->update_by_many(array(
                        'invoice_month' => $target_year . $target_month,
                        'customer_id' => $customer_id,
                        "location_id" => $location_id
                    ), array(
                        "free_postboxes_amount" => $free_postbox_amount,
                        "free_postboxes_quantity" => $free_postbox_quantity,
                        "free_postboxes_netprice" => $free_postbox_netprice,
                        
                        'invoice_code' => $invoice_code,
                        "vat" => $vat,
                        "vat_case" => $vat_case_id
                    ));
                }
            }else{
                // update data
                if ($postbox_account_type === APConstants::PRIVATE_TYPE) {
                    ci()->invoice_summary_by_location_m->update_by_many(array(
                        'invoice_month' => $target_year . $target_month,
                        'customer_id' => $customer_id,
                        "location_id" => $location_id
                    ), array(
                        "private_postboxes_amount" => $private_postbox_amount,
                        "private_postboxes_quantity" => $private_postbox_quantity,
                        "private_postboxes_netprice" => $private_postbox_netprice,
                        
                        'invoice_code' => $invoice_code,
                        "vat" => $vat,
                        "vat_case" => $vat_case_id
                    ));
                } else if ($postbox_account_type === APConstants::BUSINESS_TYPE || $postbox_account_type == APConstants::ENTERPRISE_CUSTOMER) {
                    ci()->invoice_summary_by_location_m->update_by_many(array(
                        'invoice_month' => $target_year . $target_month,
                        'customer_id' => $customer_id,
                        "location_id" => $location_id
                    ), array(
                        "business_postboxes_amount" => $business_postbox_amount,
                        "business_postboxes_quantity" => $business_postbox_quantity,
                        "business_postboxes_netprice" => $business_postbox_netprice,
                        
                        'invoice_code' => $invoice_code,
                        "vat" => $vat,
                        "vat_case" => $vat_case_id
                    ));
                }else{
                    ci()->invoice_summary_by_location_m->update_by_many(array(
                        'invoice_month' => $target_year . $target_month,
                        'customer_id' => $customer_id,
                        "location_id" => $location_id
                    ), array(
                        "free_postboxes_amount" => $free_postbox_amount,
                        "free_postboxes_quantity" => $free_postbox_quantity,
                        "free_postboxes_netprice" => $free_postbox_netprice,
                        
                        'invoice_code' => $invoice_code,
                        "vat" => $vat,
                        "vat_case" => $vat_case_id
                    ));
                }
            }
        }else{
            // insert new invoice.
            ci()->invoice_summary_by_location_m->insert(array(
                'invoice_month' => $target_year . $target_month,
                'customer_id' => $customer_id,
                "location_id" => $location_id,
                "invoice_type" => '1',
                
                "free_postboxes_amount" => $free_postbox_amount,
                "free_postboxes_quantity" => $free_postbox_quantity,
                "free_postboxes_netprice" => $free_postbox_netprice,

                "private_postboxes_amount" => $private_postbox_amount,
                "private_postboxes_quantity" => $private_postbox_quantity,
                "private_postboxes_netprice" => $private_postbox_netprice,

                "business_postboxes_amount" => $business_postbox_amount,
                "business_postboxes_quantity" => $business_postbox_quantity,
                "business_postboxes_netprice" => $business_postbox_netprice,
                
                'invoice_code' => $invoice_code,
                "vat" => $vat,
                "vat_case" => $vat_case_id
            ));
        }
    }
    
    private function calTotalInvoiceSummaryByLocation($invoice_by_location, $rev_price_postboxs, $targetYM, $location_id, $list_share_invoices){
        $rate = 1;
        $share_total_invoice = 0;
        $invoice = & $invoice_by_location;
        
        $location_rev_share = price_api::getRevShareOfLocation($location_id);
        
        // init share invoice.
        $list_share_invoice = & $invoice_by_location;
        foreach($list_share_invoice as $key=>$value){
            $list_share_invoice->{$key} = 0;
        }
        
        // set share invoice.
        foreach($list_share_invoices as $share_location){
            if($location_id == $share_location->location_id){
                $list_share_invoice = $share_location;
                break;
            }
        }
        
        // get location
        $location = ci()->location_m->get($location_id);
        $partner_id = "";
        if(!empty($location)){
            $partner_id = $location->partner_id;
        }
        $receipt_month = substr($targetYM, 4, 2).'.'.substr($targetYM, 0, 4);
        
        // Gets location report datas
        $data = report_api::getLocationReportDatas($targetYM, $location_id, $partner_id, $receipt_month);
        
        // Gets manual invoice
        $manual_data = $data['manualInvoices'];
        $manual_data_share = $data['manualInvoicesShare'];

        // Gets forwarding charge total, other invoice, credit note by location.
        //$otherLocalInvoice = $data['otherLocalInvoice'];
        $credit_note = $data['credit_note'];
        $forwardingCharges = $data['forwardingCharges'];
        
        // Gets share invoice of forwarding charge total, other invoice, credit note by location.
        //$otherLocalInvoiceShare = $data['otherLocalInvoiceShare'];
        $credit_note_share = $data['credit_note_share'];
        $forwardingChargesShare = $data['forwardingChargesShare'];
        
        // Gets paypal fee
        $paypalFee = $data['paypalFee'];
        
        // Gets invoice
        $invoice_summary = $data['invoice_summary'];
        $invoice_summary_share = $data['invoice_summary_share'];
        
        // number postbox.
        $number_of_postbox = $data['numberPostboxs'];
        $number_of_postbox_share = $data['numberPostboxsShare'];
        $invoice->number_of_postbox_share = $number_of_postbox_share['total'];
        $invoice->number_of_postbox = $number_of_postbox['total'];
        
        // number of item received.
        $numberEnvelopes = $data['numberEnvelopes'];
        $numberEnvelopesShare = $data['numberEnvelopesShare'];
        $invoice->number_of_item_received_share = $numberEnvelopesShare['received_num'];
        $invoice->number_of_envelope_scan_share = $numberEnvelopesShare['envelope_scanned_num'];
        $invoice->number_of_item_scan_share = $numberEnvelopesShare['item_scanned_num'];
        $invoice->number_of_item_forwarded_share = $numberEnvelopesShare['forwarded_num'];
        $invoice->number_of_storage_item_share = $numberEnvelopesShare['storage_num'];
        $invoice->number_of_item_received = $numberEnvelopes['received_num'];
        $invoice->number_of_envelope_scan = $numberEnvelopes['envelope_scanned_num'];
        $invoice->number_of_item_scan = $numberEnvelopes['item_scanned_num'];
        $invoice->number_of_item_forwarded = $numberEnvelopes['forwarded_num'];
        $invoice->number_of_storage_item = $numberEnvelopes['storage_num'];
        
        // number of customer and number of registration.
        $number_of_customer = $data['number_customers'];
        $invoice->number_of_account_share = 0;
        $invoice->number_of_new_registration_share = $number_of_customer['new_registration'];
        $invoice->number_of_never_activated_deleted_share = $number_of_customer['number_of_customer'];
        $invoice->number_of_manual_deleted_share = $number_of_customer['number_manually_deleted'];
        $invoice->number_of_automatic_deleted_share = $number_of_customer['number_automatic_deleted'];
        $invoice->number_of_customers_share = $number_of_customer['number_of_customer'];
        
        // delcare variable.
        $invoice->forwarding_charges_postal = 0;
        $invoice->forwarding_charges_fee = 0;
        $invoice->other_local_invoice = 0;
        $invoice->credit_note_given = 0;
        $invoice->other_local_invoice = 0;
        $invoice->credit_note_given = 0;

        // TODO: convert to invoice summary by  location
        if(!empty($forwardingCharges)){
            $invoice->forwarding_charges_postal = $forwardingCharges->forwarding_charges_postal;
            $invoice->forwarding_charges_fee = $forwardingCharges->forwarding_charges_fee;
        }
        
        $invoice->paypal_fee = $paypalFee['total'];
        $invoice->paypal_fee_share = $paypalFee['total'];
        
        $invoice->cash_payment_for_item_delivery_amount = $manual_data['cash_payment_for_item_delivery']->total_amount;
        $invoice->cash_payment_free_for_item_delivery_amount = $manual_data['cash_payment_free_for_item_delivery']->total_amount;
        $invoice->customs_cost_import_amount = $manual_data['customs_cost_import']->total_amount;
        $invoice->customs_handling_fee_import_amount = $manual_data['customs_handling_fee_import']->total_amount;
        $invoice->address_verification_amount = $manual_data['address_verification']->total_amount;
        $invoice->special_service_fee_in_15min_intervalls_amount = $manual_data['special_service_fee_in_15min_intervalls']->total_amount;
        $invoice->personal_pickup_charge_amount = $manual_data['personal_pickup_charge']->total_amount;
        
        // added custom declaration fee + manual custom declaration fee
        $invoice->custom_declaration_outgoing_price_01 = $manual_data['custom_declaration_greater_1000']->total_amount;
        $invoice->custom_declaration_outgoing_price_02 = $manual_data['custom_declaration_less_1000']->total_amount;

        // manual invoice.
        //$invoice->other_local_invoice = $otherLocalInvoice->total_amount;
        
        //credit note.
        if(!empty($credit_note)){
            $invoice->credit_note_given = $credit_note->total_amount;
            $invoice->creditnote_quantity = $credit_note->quantity;
        }
        
        // sharing unit
        $invoice->free_postboxes_share_rev = $rev_price_postboxs[APConstants::FREE_TYPE]['postbox_fee'];
        $invoice->private_postboxes_share_rev = $rev_price_postboxs[APConstants::PRIVATE_TYPE]['postbox_fee'];
        $invoice->business_postboxes_share_rev = $rev_price_postboxs[APConstants::BUSINESS_TYPE]['postbox_fee'];
        
        $invoice->incomming_items_free_share_rev = $rev_price_postboxs[APConstants::FREE_TYPE]['additional_incomming_items'];
        $invoice->incomming_items_private_share_rev = $rev_price_postboxs[APConstants::PRIVATE_TYPE]['additional_incomming_items'];
        $invoice->incomming_items_business_share_rev = $rev_price_postboxs[APConstants::BUSINESS_TYPE]['additional_incomming_items'];
        
        $invoice->envelope_scan_free_share_rev = $rev_price_postboxs[APConstants::FREE_TYPE]['envelop_scanning'];
        $invoice->envelope_scan_private_share_rev = $rev_price_postboxs[APConstants::PRIVATE_TYPE]['envelop_scanning'];
        $invoice->envelope_scan_business_share_rev = $rev_price_postboxs[APConstants::BUSINESS_TYPE]['envelop_scanning'];
        $invoice->item_scan_free_share_rev = $rev_price_postboxs[APConstants::FREE_TYPE]['opening_scanning'];
        $invoice->item_scan_private_share_rev = $rev_price_postboxs[APConstants::PRIVATE_TYPE]['opening_scanning'];
        $invoice->item_scan_business_share_rev = $rev_price_postboxs[APConstants::BUSINESS_TYPE]['opening_scanning'];
        
        $invoice->direct_shipping_free_share_rev = $rev_price_postboxs[APConstants::FREE_TYPE]['shipping_plus'];
        $invoice->direct_shipping_private_share_rev = $rev_price_postboxs[APConstants::PRIVATE_TYPE]['shipping_plus'];
        $invoice->direct_shipping_business_share_rev = $rev_price_postboxs[APConstants::BUSINESS_TYPE]['shipping_plus'];
        $invoice->collect_shipping_free_share_rev = $rev_price_postboxs[APConstants::FREE_TYPE]['shipping_plus'];
        $invoice->collect_shipping_private_share_rev = $rev_price_postboxs[APConstants::PRIVATE_TYPE]['shipping_plus'];
        $invoice->collect_shipping_business_share_rev = $rev_price_postboxs[APConstants::BUSINESS_TYPE]['shipping_plus'];
        
        $invoice->storing_letters_free_share_rev = $rev_price_postboxs[APConstants::FREE_TYPE]['storing_items_over_free_letter'];
        $invoice->storing_letters_private_share_rev = $rev_price_postboxs[APConstants::PRIVATE_TYPE]['storing_items_over_free_letter'];
        $invoice->storing_letters_business_share_rev = $rev_price_postboxs[APConstants::BUSINESS_TYPE]['storing_items_over_free_letter'];
        $invoice->storing_packages_free_share_rev = $rev_price_postboxs[APConstants::FREE_TYPE]['storing_items_over_free_packages'];
        $invoice->storing_packages_private_share_rev = $rev_price_postboxs[APConstants::PRIVATE_TYPE]['storing_items_over_free_packages'];
        $invoice->storing_packages_business_share_rev = $rev_price_postboxs[APConstants::BUSINESS_TYPE]['storing_items_over_free_packages'];
        
        $invoice->forwarding_charges_postal_share_rev = $rev_price_postboxs[APConstants::FREE_TYPE]['shipping_plus'];
        $invoice->forwarding_charges_fee_share_rev = $rev_price_postboxs[APConstants::FREE_TYPE]['shipping_plus'];
        
        $invoice->cash_payment_for_item_delivery_share_rev = 100;
        $invoice->cash_payment_free_for_item_delivery_share_rev = $rev_price_postboxs[APConstants::FREE_TYPE]['cash_payment_on_delivery_percentage'];
        $invoice->customs_cost_import_share_rev = 0;
        $invoice->customs_handling_fee_import_share_rev = $rev_price_postboxs[APConstants::FREE_TYPE]['custom_handling_import'];
        $invoice->address_verification_share_rev = $rev_price_postboxs[APConstants::FREE_TYPE]['official_address_verification'];
        $invoice->special_service_fee_in_15min_intervalls_share_rev = $rev_price_postboxs[APConstants::FREE_TYPE]['special_requests_charge_by_time'];
        $invoice->personal_pickup_charge_share_rev = $rev_price_postboxs[APConstants::FREE_TYPE]['pickup_charge'];
        $invoice->paypal_fee_share_rev = $rev_price_postboxs[APConstants::FREE_TYPE]['paypal_transaction_fee'];
        $invoice->other_local_invoice_share_rev = $location_rev_share;
        $invoice->credit_note_given_share_rev = $location_rev_share;
        
        $invoice->additional_pages_scanning_free_share_rev = $rev_price_postboxs[APConstants::FREE_TYPE]['additional_pages_scanning_price'];
        $invoice->additional_pages_scanning_private_share_rev = $rev_price_postboxs[APConstants::PRIVATE_TYPE]['additional_pages_scanning_price'];
        $invoice->additional_pages_scanning_business_share_rev = $rev_price_postboxs[APConstants::BUSINESS_TYPE]['additional_pages_scanning_price'];
        
        $invoice->custom_declaration_outgoing_price_01_share_rev = $rev_price_postboxs[APConstants::FREE_TYPE]['custom_declaration_outgoing_01'];
        $invoice->custom_declaration_outgoing_price_02_share_rev = $rev_price_postboxs[APConstants::FREE_TYPE]['custom_declaration_outgoing_02'];
        
        // sharing value
        $invoice->free_postboxes_share = $list_share_invoice->free_postboxes_amount;
        $invoice->private_postboxes_share = $list_share_invoice->private_postboxes_amount;
        $invoice->business_postboxes_share = $list_share_invoice->business_postboxes_amount;
        
        $invoice->incomming_items_free_share = $list_share_invoice->incomming_items_free_account;
        $invoice->incomming_items_private_share = $list_share_invoice->incomming_items_private_account; 
        $invoice->incomming_items_business_share = $list_share_invoice->incomming_items_business_account; 
        
        $invoice->envelope_scan_free_share = $list_share_invoice->envelope_scan_free_account; 
        $invoice->envelope_scan_private_share = $list_share_invoice->envelope_scan_private_account;
        $invoice->envelope_scan_business_share = $list_share_invoice->envelope_scan_business_account; 
        $invoice->item_scan_free_share = $list_share_invoice->item_scan_free_account; 
        $invoice->item_scan_private_share = $list_share_invoice->item_scan_private_account; 
        $invoice->item_scan_business_share = $list_share_invoice->item_scan_business_account; 
        
        $invoice->direct_shipping_free_share = $list_share_invoice->direct_shipping_free_account; 
        $invoice->direct_shipping_private_share = $list_share_invoice->direct_shipping_private_account; 
        $invoice->direct_shipping_business_share = $list_share_invoice->direct_shipping_business_account; 
        $invoice->collect_shipping_free_share = $list_share_invoice->collect_shipping_free_account;
        $invoice->collect_shipping_private_share = $list_share_invoice->collect_shipping_private_account; 
        $invoice->collect_shipping_business_share = $list_share_invoice->collect_shipping_business_account; 
        
        $invoice->storing_letters_free_share = $list_share_invoice->storing_letters_free_account; 
        $invoice->storing_letters_private_share = $list_share_invoice->storing_letters_private_account; 
        $invoice->storing_letters_business_share = $list_share_invoice->storing_letters_business_account; 
        $invoice->storing_packages_free_share = $list_share_invoice->storing_packages_free_account; 
        $invoice->storing_packages_private_share = $list_share_invoice->storing_packages_private_account; 
        $invoice->storing_packages_business_share = $list_share_invoice->storing_packages_business_account; 
        
        $invoice->additional_pages_scanning_free_share = $list_share_invoice->additional_pages_scanning_free_amount;
        $invoice->additional_pages_scanning_private_share = $list_share_invoice->additional_pages_scanning_private_amount; 
        $invoice->additional_pages_scanning_business_share = $list_share_invoice->additional_pages_scanning_business_amount; 
        
        $invoice->custom_declaration_outgoing_price_01_share = ($manual_data_share['custom_declaration_greater_1000']->total_amount); 
        $invoice->custom_declaration_outgoing_price_02_share = ($manual_data_share['custom_declaration_less_1000']->total_amount); 
        
        $invoice->forwarding_charges_postal_share = 0;
        $invoice->forwarding_charges_fee_share = 0;
        $invoice->other_local_invoice_share = 0;
        $invoice->credit_note_given_share = 0;
        $invoice->other_local_invoice_share = 0;
        $invoice->credit_note_given_share = 0;
        //$invoice->paypal_fee_share = 0;
        
        //credit note.
        if($credit_note_share){
            $invoice->credit_note_given_share =$credit_note_share->total_amount;
            $invoice->creditnote_quantity_share = $credit_note_share->quantity;
        }
        
        // TODO: convert to invoice summary by  location
        // fowarding charge
        if($forwardingChargesShare){
            $invoice->forwarding_charges_postal_share = $forwardingChargesShare->forwarding_charges_postal;
            $invoice->forwarding_charges_fee_share = $forwardingChargesShare->forwarding_charges_fee;
        }
        
        // Cash expenditure of partner
        $invoice->cash_expenditure_of_partner = $data['otherCashExpenditure'];

        $invoice->cash_payment_free_for_item_delivery_amount_share = $manual_data_share['cash_payment_free_for_item_delivery']->total_amount;
        
        // added custom declaration fee + manual custom declaration fee
        $invoice->custom_declaration_outgoing_price_01_share = $manual_data_share['custom_declaration_greater_1000']->total_amount;
        $invoice->custom_declaration_outgoing_price_02_share = $manual_data_share['custom_declaration_less_1000']->total_amount;
        
        $invoice->customs_cost_import_share = $manual_data_share['customs_cost_import']->total_amount * $location_rev_share;
        
        $invoice->customs_handling_fee_import_share = $manual_data_share['customs_handling_fee_import']->total_amount;
        $invoice->address_verification_share = $manual_data_share['address_verification']->total_amount;
        $invoice->special_service_fee_in_15min_intervalls_share = $manual_data_share['special_service_fee_in_15min_intervalls']->total_amount;
        $invoice->personal_pickup_charge_share = $manual_data_share['personal_pickup_charge']->total_amount;
        
        //$invoice->paypal_fee_share = $invoice->paypal_fee;

        // update quantity of location.
        $invoice->free_postbox_quantity                 = $number_of_postbox['number_free'];
        $invoice->free_postbox_quantity_share           = $number_of_postbox_share['number_free'];
        $invoice->private_postbox_quantity              = $number_of_postbox['number_private'];
        $invoice->private_postbox_quantity_share        = $number_of_postbox_share['number_private'];
        $invoice->business_postbox_quantity             = $number_of_postbox['number_business'];
        $invoice->business_postbox_quantity_share       = $number_of_postbox_share['number_business'];
        
        $invoice->free_incoming_quantity                = $invoice_summary->incomming_items_free_quantity;
        $invoice->free_incoming_quantity_share          = $invoice_summary_share->incomming_items_free_quantity;
        $invoice->private_incoming_quantity             = $invoice_summary->incomming_items_private_quantity;
        $invoice->private_incoming_quantity_share       = $invoice_summary_share->incomming_items_private_quantity;
        $invoice->business_incoming_item_quantity       = $invoice_summary->incomming_items_business_quantity;
        $invoice->business_incoming_item_quantity_share = $invoice_summary_share->incomming_items_business_quantity;
        
        $invoice->free_envelope_scan_quantity           = $invoice_summary->envelope_scan_free_quantity;
        $invoice->free_envelope_scan_quantity_share     = $invoice_summary_share->envelope_scan_free_quantity;
        $invoice->private_envelope_scan_quantity        = $invoice_summary->envelope_scan_private_quantity;
        $invoice->private_envelope_scan_quantity_share  = $invoice_summary_share->envelope_scan_private_quantity;
        $invoice->business_envelope_scan_quantity       = $invoice_summary->envelope_scan_business_quantity;
        $invoice->business_envelope_scan_quantity_share = $invoice_summary_share->envelope_scan_business_quantity;
        
        $invoice->free_item_scan_quantity               = $invoice_summary->item_scan_free_quantity;
        $invoice->free_item_scan_quantity_share         = $invoice_summary_share->item_scan_free_quantity;
        $invoice->private_item_scan_quantity            = $invoice_summary->item_scan_private_quantity;
        $invoice->private_item_scan_quantity_share      = $invoice_summary_share->item_scan_private_quantity;
        $invoice->business_item_scan_quantity           = $invoice_summary->item_scan_business_quantity;
        $invoice->business_item_scan_quantity_share     = $invoice_summary_share->item_scan_business_quantity;
        
        $invoice->free_additional_page_quantity         = $invoice_summary->additional_pages_scanning_free_quantity;
        $invoice->free_additional_page_quantity_share   = $invoice_summary_share->additional_pages_scanning_free_quantity;
        $invoice->private_additional_page_quantity      = $invoice_summary->additional_pages_scanning_private_quantity;
        $invoice->private_additional_page_quantity_share = $invoice_summary_share->additional_pages_scanning_private_quantity;
        $invoice->business_additional_page_quantity     = $invoice_summary->additional_pages_scanning_business_quantity;
        $invoice->business_additional_page_quantity_share = $invoice_summary_share->additional_pages_scanning_business_quantity;
        
        $invoice->fowarding_charge_postal_quantity      = $invoice_summary->direct_shipping_quantity;
        $invoice->fowarding_charge_postal_quantity_share= $invoice_summary_share->direct_shipping_quantity;
        $invoice->fowarding_charge_fee_quantity         = $invoice_summary->collect_shipping_quantity;
        $invoice->fowarding_charge_fee_quantity_share   = $invoice_summary_share->collect_shipping_quantity;
        
        $invoice->free_storage_letter_quanity           = $invoice_summary->storing_letters_free_quantity;
        $invoice->free_storage_letter_quanity_share     = $invoice_summary_share->storing_letters_free_quantity;
        $invoice->private_storage_letter_quanity        = $invoice_summary->storing_letters_private_quantity;
        $invoice->private_storage_letter_quanity_share  = $invoice_summary_share->storing_letters_private_quantity;
        $invoice->business_storage_letter_quanity       = $invoice_summary->storing_letters_business_quantity;
        $invoice->business_storage_letter_quanity_share = $invoice_summary_share->storing_letters_business_quantity;
        
        $invoice->free_storage_package_quanity          = $invoice_summary->storing_packages_free_quantity;
        $invoice->free_storage_package_quanity_share    = $invoice_summary_share->storing_packages_free_quantity;
        $invoice->private_storage_package_quanity       = $invoice_summary->storing_packages_private_quantity;
        $invoice->private_storage_package_quanity_share = $invoice_summary_share->storing_packages_private_quantity;
        $invoice->business_storage_package_quanity      = $invoice_summary->storing_packages_business_quantity;
        $invoice->business_storage_package_quanity_share  = $invoice_summary_share->storing_packages_business_quantity;
        
        $invoice->custom_declaration_quantity           = $manual_data['custom_declaration_greater_1000']->quantity + $manual_data['custom_declaration_less_1000']->quantity;
        $invoice->custom_declaration_quantity_share     = $manual_data_share['custom_declaration_greater_1000']->quantity + $manual_data_share['custom_declaration_less_1000']->quantity;
        
        $invoice->cash_payment_fee_quantity             = $manual_data['cash_payment_free_for_item_delivery']->quantity;
        $invoice->cash_payment_fee_quantity_share       = $manual_data_share['cash_payment_free_for_item_delivery']->quantity;
        
        $invoice->custom_cost_import_quantity           = $manual_data['customs_cost_import']->quantity;
        $invoice->custom_cost_import_quantity_share     = $manual_data_share['customs_cost_import']->quantity;
        
        $invoice->import_custom_fee_quantity            = $manual_data['customs_handling_fee_import']->quantity;
        $invoice->import_custom_fee_quantity_share      = $manual_data_share['customs_handling_fee_import']->quantity;

        $invoice->address_verification_quantity         = $manual_data['address_verification']->quantity;
        $invoice->address_verification_quantity_share   = $manual_data_share['address_verification']->quantity;
        
        $invoice->special_service_fee_quantity          = $manual_data['special_service_fee_in_15min_intervalls']->quantity;
        $invoice->special_service_fee_quantity_share    = $manual_data_share['special_service_fee_in_15min_intervalls']->quantity;
        
        $invoice->peronsal_pickup_charge_quantity       = $manual_data['personal_pickup_charge']->quantity;
        $invoice->peronsal_pickup_charge_quantity_share = $manual_data_share['personal_pickup_charge']->quantity;

        $invoice->paypal_transaction_fee_quantity       = $paypalFee['quantity'];
        $invoice->paypal_transaction_fee_quantity_share = $paypalFee['quantity'];
        
        // other local quantity and amount
        $count_manual_invoice = ci()->invoice_summary_by_location_m->count_manual_invoices_by($targetYM, $location_id, false);
        $count_manual_invoice_share = ci()->invoice_summary_by_location_m->count_manual_invoices_by($targetYM, $location_id, true);
        $invoice->other_local_invoice_quantity          = $count_manual_invoice - (
                $invoice->creditnote_quantity
                + $invoice->paypal_transaction_fee_quantity
                + $invoice->peronsal_pickup_charge_quantity
                + $invoice->special_service_fee_quantity
                + $invoice->address_verification_quantity
                + $invoice->import_custom_fee_quantity
                + $invoice->custom_cost_import_quantity
                + $invoice->cash_payment_fee_quantity
                + $invoice->custom_declaration_quantity
                );
        $invoice->other_local_invoice_quantity_share    = $count_manual_invoice_share - (
                $invoice->creditnote_quantity_share
                + $invoice->paypal_transaction_fee_quantity_share
                + $invoice->peronsal_pickup_charge_quantity_share
                + $invoice->special_service_fee_quantity_share
                + $invoice->address_verification_quantity_share
                + $invoice->import_custom_fee_quantity_share
                + $invoice->custom_cost_import_quantity_share
                + $invoice->cash_payment_fee_quantity_share
                + $invoice->custom_declaration_quantity_share
                );

        // other amount.
        $total_manual_invoice = ci()->invoice_summary_by_location_m->summary_by_location($location_id, $targetYM, false);
        $total_manual_invoice_share = ci()->invoice_summary_by_location_m->summary_by_location($location_id, $targetYM, true);
        $total_number = $this->calcTotalNumber($invoice);
        $invoice->other_local_invoice = $total_manual_invoice->total_invoice - $total_number['total'];
        $invoice->other_local_invoice_share = $total_manual_invoice_share->total_invoice - $total_number['total_share'];
        $invoice->share_total_invoice = 0;
        if($invoice->other_local_invoice_share > 0 && $invoice->other_local_invoice_quantity_share <= 0){
            $invoice->other_local_invoice_quantity_share = 1;
        }
        if($invoice->other_local_invoice > 0 && $invoice->other_local_invoice_quantity <= 0){
            $invoice->other_local_invoice_quantity = 1;
        }

        // return result
        return $invoice;
    }
    
    /**
     * calculate the total number.
     * @param type $invoice
     * @return type
     */
    private function calcTotalNumber($invoice_by_location){
        // total
        $total = 0;
        $total += round($invoice_by_location->free_postboxes_amount,2 );
        $total += round($invoice_by_location->private_postboxes_amount,2 );
        $total += round($invoice_by_location->business_postboxes_amount,2 );

        $total += round($invoice_by_location->incomming_items_free_account,2 );
        $total += round($invoice_by_location->incomming_items_private_account,2 );
        $total += round($invoice_by_location->incomming_items_business_account,2 );

        $total += round($invoice_by_location->envelope_scan_free_account,2 );
        $total += round($invoice_by_location->envelope_scan_private_account,2 );
        $total += round($invoice_by_location->envelope_scan_business_account,2 );

        $total += round($invoice_by_location->item_scan_free_account,2 );
        $total += round($invoice_by_location->item_scan_private_account,2 );
        $total += round($invoice_by_location->item_scan_business_account,2 );

        $total += round($invoice_by_location->additional_pages_scanning_free_amount,2 );
        $total += round($invoice_by_location->additional_pages_scanning_private_amount,2 );
        $total += round($invoice_by_location->additional_pages_scanning_business_amount,2 );

        $total += round($invoice_by_location->forwarding_charges_postal + $invoice_by_location->forwarding_charges_fee,2 );

        $total += round($invoice_by_location->storing_letters_free_account,2 );
        $total += round($invoice_by_location->storing_letters_private_account,2 );
        $total += round($invoice_by_location->storing_letters_business_account,2 );

        $total += round($invoice_by_location->storing_packages_free_account,2 );
        $total += round($invoice_by_location->storing_packages_private_account,2 );
        $total += round($invoice_by_location->storing_packages_business_account,2 );

        $total += round($invoice_by_location->custom_declaration_outgoing_price_01, 2);
        $total += round($invoice_by_location->custom_declaration_outgoing_price_02, 2);

        $total += round($invoice_by_location->cash_payment_free_for_item_delivery_amount, 2);
        $total += round($invoice_by_location->customs_cost_import_amount, 2);
        $total += round($invoice_by_location->customs_handling_fee_import_amount, 2);
        $total += round($invoice_by_location->address_verification_amount, 2);
        $total += round($invoice_by_location->special_service_fee_in_15min_intervalls_amount, 2);
        $total += round($invoice_by_location->personal_pickup_charge_amount, 2);

        $total += round($invoice_by_location->paypal_fee, 2);
        $total += round($invoice_by_location->credit_note_given, 2);

        // total share
        $total_share = 0;
        $total_share += round($invoice_by_location->free_postboxes_share,2 );
        $total_share += round($invoice_by_location->private_postboxes_share,2 );
        $total_share += round($invoice_by_location->business_postboxes_share,2 );

        $total_share += round($invoice_by_location->incomming_items_free_share,2 );
        $total_share += round($invoice_by_location->incomming_items_private_share,2 );
        $total_share += round($invoice_by_location->incomming_items_business_share,2 );

        $total_share += round($invoice_by_location->envelope_scan_free_share,2 );
        $total_share += round($invoice_by_location->envelope_scan_private_share,2 );
        $total_share += round($invoice_by_location->envelope_scan_business_share,2 );

        $total_share += round($invoice_by_location->item_scan_free_share,2 );
        $total_share += round($invoice_by_location->item_scan_private_share,2 );
        $total_share += round($invoice_by_location->item_scan_business_share,2 );

        $total_share += round($invoice_by_location->additional_pages_scanning_free_share,2 );
        $total_share += round($invoice_by_location->additional_pages_scanning_private_share,2 );
        $total_share += round($invoice_by_location->additional_pages_scanning_business_share,2 );

        $total_share += round($invoice_by_location->forwarding_charges_postal_share + $invoice_by_location->forwarding_charges_fee_share,2 );

        $total_share += round($invoice_by_location->storing_letters_free_share,2 );
        $total_share += round($invoice_by_location->storing_letters_private_share,2 );
        $total_share += round($invoice_by_location->storing_letters_business_share,2 );

        $total_share += round($invoice_by_location->storing_packages_free_share,2 );
        $total_share += round($invoice_by_location->storing_packages_private_share,2 );
        $total_share += round($invoice_by_location->storing_packages_business_share,2 );

        $total_share += round($invoice_by_location->custom_declaration_outgoing_price_01_share, 2);
        $total_share += round($invoice_by_location->custom_declaration_outgoing_price_02_share, 2);

        $total_share += round($invoice_by_location->cash_payment_free_for_item_delivery_amount_share, 2);
        $total_share += round($invoice_by_location->customs_cost_import_share, 2);
        $total_share += round($invoice_by_location->customs_handling_fee_import_share, 2);
        $total_share += round($invoice_by_location->address_verification_share, 2);
        $total_share += round($invoice_by_location->special_service_fee_in_15min_intervalls_share, 2);
        $total_share += round($invoice_by_location->personal_pickup_charge_share, 2);

        $total_share += round($invoice_by_location->paypal_fee_share, 2);
        $total_share += round($invoice_by_location->credit_note_given_share, 2);
        
        return array(
            "total" => $total,
            "total_share" => $total_share
        );
    }
}