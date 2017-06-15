<?php defined('BASEPATH') or exit('No direct script access allowed');

class InvoiceSummary
{
    /**
     * Summary data from tables [envelope_summary_month] to tables [invoice_summary]. Summary from invoice_detail to invoice_summary
     *
     * @param unknown_type $customer_id
     * @param unknown_type $target_year
     * @param unknown_type $target_month
     */
    public function cal_invoice_summary($customer_id, $target_year, $target_month, $customerVAT = null)
    {
        ci()->load->model('invoices/invoice_detail_m');
        ci()->load->model('invoices/invoice_summary_m');
        ci()->load->model('scans/envelope_shipping_m');

        if(empty($customerVAT)){
            $customer = APContext::getCustomerByID($customer_id);
            // Gets vat of customer.
            if(!empty($customer->parent_customer_id)){
                // Gets VAT of enteprrise customer.
                $customerVAT = APUtils::getVatRateOfCustomer($customer->parent_customer_id);
            }else{
                $customerVAT = APUtils::getVatRateOfCustomer($customer_id);
            }
        }
        
        // Amount
        $incomming_items_free_account = 0;
        $envelope_scan_free_account = 0;
        $item_scan_free_account = 0;
        $direct_shipping_free_account = 0;
        $collect_shipping_free_account = 0;
        $additional_pages_scanning_free_amount = 0;

        $incomming_items_private_account = 0;
        $envelope_scan_private_account = 0;
        $item_scan_private_account = 0;
        $direct_shipping_private_account = 0;
        $collect_shipping_private_account = 0;
        $additional_pages_scanning_private_amount = 0;

        $incomming_items_business_account = 0;
        $envelope_scan_business_account = 0;
        $item_scan_business_account = 0;
        $direct_shipping_business_account = 0;
        $collect_shipping_business_account = 0;
        $additional_pages_scanning_business_amount = 0;
        
        $custom_declaration_outgoing_price_01 = 0;
        $custom_declaration_outgoing_price_02 = 0;
        
        $api_access_amount = 0;
        $own_location_amount = 0;
        $touch_panel_own_location_amount = 0;
        $own_mobile_app_amount = 0;
        $clevver_subdomain_amount = 0;
        $own_subdomain_amount = 0;
        
        // Get all total amount by activity and type
        $invoices = ci()->invoice_detail_m->summary_envelope_bymonth($customer_id, $target_year, $target_month);

        $total_count = count($invoices);

        if(!$total_count){
            ci()->invoice_summary_m->update_by_many(array(
                'customer_id' => $customer_id,
                'invoice_month' => $target_year.$target_month
            ), array(
                // Envelope scanning amount
                "envelope_scan_free_account"          => 0,
                "envelope_scan_private_account"       => 0,
                "envelope_scan_business_account"      => 0,
                // Item scanning amount
                "item_scan_free_account"              => 0,
                "item_scan_private_account"           => 0,
                "item_scan_business_account"          => 0,
                // Additional item amount
                "additional_pages_scanning"           => 0,
                "incomming_items_free_account"        => 0,
                "incomming_items_private_account"     => 0,
                "incomming_items_business_account"    => 0,
                // Additional scanning amount
                "additional_pages_scanning_free_amount"       => 0,
                "additional_pages_scanning_private_amount"    => 0,
                "additional_pages_scanning_business_amount"   => 0,
                // Shipping handding amount
                "direct_shipping_free_account"      => 0,
                "direct_shipping_private_account"   => 0,
                "direct_shipping_business_account"  => 0,
                "collect_shipping_free_account"     => 0,
                "collect_shipping_private_account"  => 0,
                "collect_shipping_business_account" => 0,
                // Enterrpise
                "api_access_amount" => 0,
                "own_location_amount" => 0,
                "touch_panel_own_location_amount" => 0,
                "own_mobile_app_amount" => 0,
                "clevver_subdomain_amount" => 0,
                "own_subdomain_amount" => 0,
                
                "vat_case" => $customerVAT->vat_case_id,
                "vat" => $customerVAT->rate,
            ));
        }

        if($total_count){
            // Loop and calculate invoices amount
            $pre_invoice_id = '';
            $index = 0;
            foreach ($invoices as $invoice) {
                $index++;
                if ($pre_invoice_id == '') {
                    $pre_invoice_id = $invoice->invoice_summary_id;
                }

                if ($pre_invoice_id != $invoice->invoice_summary_id) {
                    // Update activity amount.
                    $this->updateActivityAmount(array(
                        'invoice_summary_id' => $pre_invoice_id,
                        // amount
                        "incomming_items_free_account" => $incomming_items_free_account,
                        "envelope_scan_free_account" => $envelope_scan_free_account,
                        "item_scan_free_account" => $item_scan_free_account,
                        "direct_shipping_free_account" => $direct_shipping_free_account,
                        "collect_shipping_free_account" => $collect_shipping_free_account,
                        "additional_pages_scanning_free_amount" => $additional_pages_scanning_free_amount,

                        "incomming_items_private_account" => $incomming_items_private_account,
                        "envelope_scan_private_account" => $envelope_scan_private_account,
                        "item_scan_private_account" => $item_scan_private_account,
                        "direct_shipping_private_account" => $direct_shipping_private_account,
                        "collect_shipping_private_account" => $collect_shipping_private_account,
                        "additional_pages_scanning_private_amount" => $additional_pages_scanning_private_amount,

                        "incomming_items_business_account" => $incomming_items_business_account,
                        "envelope_scan_business_account" => $envelope_scan_business_account,
                        "item_scan_business_account" => $item_scan_business_account,
                        "direct_shipping_business_account" => $direct_shipping_business_account,
                        "collect_shipping_business_account" => $collect_shipping_business_account,
                        "additional_pages_scanning_business_amount" => $additional_pages_scanning_business_amount,
                        
                        "custom_declaration_outgoing_quantity_01" => 1,
                        "custom_declaration_outgoing_quantity_02" => 1,
                        "custom_declaration_outgoing_price_01" => $custom_declaration_outgoing_price_01,
                        "custom_declaration_outgoing_price_02" => $custom_declaration_outgoing_price_02,
                        // Enterprise
                        "api_access_amount" => $api_access_amount,
                        "own_location_amount" => $own_location_amount,
                        "touch_panel_own_location_amount" => $touch_panel_own_location_amount,
                        "own_mobile_app_amount" => $own_mobile_app_amount,
                        "clevver_subdomain_amount" => $clevver_subdomain_amount,
                        "own_subdomain_amount" => $own_subdomain_amount,
                        
                        "update_flag" => 0,
                        "vat_case" => $customerVAT->vat_case_id,
                        "vat" => $customerVAT->rate,
                    ));

                    // Reset all amount activty
                    $incomming_items_free_account = 0;
                    $envelope_scan_free_account = 0;
                    $item_scan_free_account = 0;
                    $direct_shipping_free_account = 0;
                    $collect_shipping_free_account = 0;
                    $additional_pages_scanning_free_amount = 0;

                    $incomming_items_private_account = 0;
                    $envelope_scan_private_account = 0;
                    $item_scan_private_account = 0;
                    $direct_shipping_private_account = 0;
                    $collect_shipping_private_account = 0;
                    $additional_pages_scanning_private_amount = 0;

                    $incomming_items_business_account = 0;
                    $envelope_scan_business_account = 0;
                    $item_scan_business_account = 0;
                    $direct_shipping_business_account = 0;
                    $collect_shipping_business_account = 0;
                    $additional_pages_scanning_business_amount = 0;
                }

                // Gets data.
                $pre_invoice_id = $invoice->invoice_summary_id;
                $postbox_type = $invoice->type . '';
                $activity = $invoice->activity . '';
                $activity_type = $invoice->activity_type . '';
                $amount = $invoice->amount;
                if (empty($postbox_type)) {
                    $postbox_type = $invoice->account_type;
                }

                // If postbox type is FREE
                if ($postbox_type === APConstants::FREE_TYPE) {
                    if ($activity_type === APConstants::INCOMMING_ACTIVITY_TYPE) {
                        $incomming_items_free_account = $amount;
                    } else if ($activity_type === APConstants::ENVELOPE_SCAN_ACTIVITY_TYPE) {
                        $envelope_scan_free_account = $amount;
                    } else if ($activity_type === APConstants::ITEM_SCAN_ACTIVITY_TYPE) {
                        $item_scan_free_account = $amount;
                    } else if ($activity_type === APConstants::DIRECT_SHIPPING_ACTIVITY_TYPE) {
                        $direct_shipping_free_account = $amount;
                    } else if ($activity_type === APConstants::COLLECT_SHIPPING_ACTIVITY_TYPE) {
                        $collect_shipping_free_account = $amount;
                    } else if ($activity_type === APConstants::ADDITIONAL_SCAN_ACTIVITY_TYPE) {
                        $additional_pages_scanning_free_amount = $amount;
                    } else if ($activity_type === APConstants::CUSTOMS_DECLARATION_01_ACTIVITY_TYPE){
                        $custom_declaration_outgoing_price_01 = $amount;
                    }else if ($activity_type === APConstants::CUSTOMS_DECLARATION_02_ACTIVITY_TYPE){
                        $custom_declaration_outgoing_price_02 = $amount;
                    }
                } // Private
                else if ($postbox_type === APConstants::PRIVATE_TYPE) {
                    if ($activity_type === APConstants::INCOMMING_ACTIVITY_TYPE) {
                        $incomming_items_private_account = $amount;
                    } else if ($activity_type === APConstants::ENVELOPE_SCAN_ACTIVITY_TYPE) {
                        $envelope_scan_private_account = $amount;
                    } else if ($activity_type === APConstants::ITEM_SCAN_ACTIVITY_TYPE) {
                        $item_scan_private_account = $amount;
                    } else if ($activity_type === APConstants::DIRECT_SHIPPING_ACTIVITY_TYPE) {
                        $direct_shipping_private_account = $amount;
                    } else if ($activity_type === APConstants::COLLECT_SHIPPING_ACTIVITY_TYPE) {
                        $collect_shipping_private_account = $amount;
                    } else if ($activity_type === APConstants::ADDITIONAL_SCAN_ACTIVITY_TYPE) {
                        $additional_pages_scanning_private_amount = $amount;
                    }else if ($activity_type === APConstants::CUSTOMS_DECLARATION_01_ACTIVITY_TYPE){
                        $custom_declaration_outgoing_price_01 = $amount;
                    }else if ($activity_type === APConstants::CUSTOMS_DECLARATION_02_ACTIVITY_TYPE){
                        $custom_declaration_outgoing_price_02 = $amount;
                    }
                } // Business
                else if ($postbox_type === APConstants::BUSINESS_TYPE || $postbox_type == APConstants::ENTERPRISE_CUSTOMER) {
                    if ($activity_type === APConstants::INCOMMING_ACTIVITY_TYPE) {
                        $incomming_items_business_account = $amount;
                    } else if ($activity_type === APConstants::ENVELOPE_SCAN_ACTIVITY_TYPE) {
                        $envelope_scan_business_account = $amount;
                    } else if ($activity_type === APConstants::ITEM_SCAN_ACTIVITY_TYPE) {
                        $item_scan_business_account = $amount;
                    } else if ($activity_type === APConstants::DIRECT_SHIPPING_ACTIVITY_TYPE) {
                        $direct_shipping_business_account = $amount;
                    } else if ($activity_type === APConstants::COLLECT_SHIPPING_ACTIVITY_TYPE) {
                        $collect_shipping_business_account = $amount;
                    } else if ($activity_type === APConstants::ADDITIONAL_SCAN_ACTIVITY_TYPE) {
                        $additional_pages_scanning_business_amount = $amount;
                    }else if ($activity_type === APConstants::CUSTOMS_DECLARATION_01_ACTIVITY_TYPE){
                        $custom_declaration_outgoing_price_01 = $amount;
                    }else if ($activity_type === APConstants::CUSTOMS_DECLARATION_02_ACTIVITY_TYPE){
                        $custom_declaration_outgoing_price_02 = $amount;
                    }else if ($activity_type === APConstants::INVOICE_ACTIVITY_TYPE_API_ACCESS){
                        $api_access_amount = $amount;
                    }else if ($activity_type === APConstants::INVOICE_ACTIVITY_TYPE_OWN_LOCATION){
                        $own_location_amount = $amount;
                    }else if ($activity_type === APConstants::INVOICE_ACTIVITY_TYPE_TOUCH_PANEL_OWN_LOCATION){
                        $touch_panel_own_location_amount = $amount;
                    }else if ($activity_type === APConstants::INVOICE_ACTIVITY_TYPE_OWN_MOBILE_APP){
                        $own_mobile_app_amount = $amount;
                    }else if ($activity_type === APConstants::INVOICE_ACTIVITY_TYPE_CLEVVER_SUBDOMAIN){
                        $clevver_subdomain_amount = $amount;
                    }else if ($activity_type === APConstants::INVOICE_ACTIVITY_TYPE_OWN_SUBDOMAIN){
                        $own_subdomain_amount = $amount;
                    }
                }

                //  update if end of array
                if ($index == $total_count) {
                    // Update activity amount and break.
                    $this->updateActivityAmount(array(
                        'invoice_summary_id' => $invoice->invoice_summary_id,
                        // amount.
                        "incomming_items_free_account" => $incomming_items_free_account,
                        "envelope_scan_free_account" => $envelope_scan_free_account,
                        "item_scan_free_account" => $item_scan_free_account,
                        "direct_shipping_free_account" => $direct_shipping_free_account,
                        "collect_shipping_free_account" => $collect_shipping_free_account,
                        "additional_pages_scanning_free_amount" => $additional_pages_scanning_free_amount,

                        "incomming_items_private_account" => $incomming_items_private_account,
                        "envelope_scan_private_account" => $envelope_scan_private_account,
                        "item_scan_private_account" => $item_scan_private_account,
                        "direct_shipping_private_account" => $direct_shipping_private_account,
                        "collect_shipping_private_account" => $collect_shipping_private_account,
                        "additional_pages_scanning_private_amount" => $additional_pages_scanning_private_amount,

                        "incomming_items_business_account" => $incomming_items_business_account,
                        "envelope_scan_business_account" => $envelope_scan_business_account,
                        "item_scan_business_account" => $item_scan_business_account,
                        "direct_shipping_business_account" => $direct_shipping_business_account,
                        "collect_shipping_business_account" => $collect_shipping_business_account,
                        "additional_pages_scanning_business_amount" => $additional_pages_scanning_business_amount,
                        
                        "custom_declaration_outgoing_quantity_01" => 1,
                        "custom_declaration_outgoing_quantity_02" => 1,
                        "custom_declaration_outgoing_price_01" => $custom_declaration_outgoing_price_01,
                        "custom_declaration_outgoing_price_02" => $custom_declaration_outgoing_price_02,
                        
                        // Enterprise
                        "api_access_amount" => $api_access_amount,
                        "own_location_amount" => $own_location_amount,
                        "touch_panel_own_location_amount" => $touch_panel_own_location_amount,
                        "own_mobile_app_amount" => $own_mobile_app_amount,
                        "clevver_subdomain_amount" => $clevver_subdomain_amount,
                        "own_subdomain_amount" => $own_subdomain_amount,
                        
                        "vat_case" => $customerVAT->vat_case_id,
                        "vat" => $customerVAT->rate,
                    ));
                }
            } // end foreach
        }


        // Quantity & Price
        $incomming_items_free_quantity = 0;
        $incomming_items_free_netprice = 0;
        $incomming_items_private_quantity = 0;
        $incomming_items_private_netprice = 0;
        $incomming_items_business_quantity = 0;
        $incomming_items_business_netprice = 0;

        $envelope_scan_free_quantity = 0;
        $envelope_scan_free_netprice = 0;
        $envelope_scan_private_quantity = 0;
        $envelope_scan_private_netprice = 0;
        $envelope_scan_business_quantity = 0;
        $envelope_scan_business_netprice = 0;

        $item_scan_free_quantity = 0;
        $item_scan_free_netprice = 0;
        $item_scan_private_quantity = 0;
        $item_scan_private_netprice = 0;
        $item_scan_business_quantity = 0;
        $item_scan_business_netprice = 0;

        $direct_shipping_free_quantity = 0;
        $direct_shipping_free_netprice = 0;
        $direct_shipping_private_quantity = 0;
        $direct_shipping_private_netprice = 0;
        $direct_shipping_business_quantity = 0;
        $direct_shipping_business_netprice = 0;

        $collect_shipping_free_quantity = 0;
        $collect_shipping_free_netprice = 0;
        $private_collect_shipping_quantity = 0;
        $collect_shipping_private_quantity = 0;
        $collect_shipping_business_quantity = 0;
        $collect_shipping_business_netprice = 0;

        $additional_pages_scanning_free_quantity = 0;
        $additional_pages_scanning_free_netprice = 0;
        $additional_pages_scanning_private_quantity = 0;
        $additional_pages_scanning_private_netprice = 0;
        $additional_pages_scanning_business_quantity = 0;
        $additional_pages_scanning_business_netprice = 0;

        $custom_declaration_outgoing_quantity_01 = 0;
        $custom_declaration_outgoing_quantity_02 = 0;
        $custom_declaration_outgoing_price_01 = 0;
        $custom_declaration_outgoing_price_02 = 0;

        // Loop and calculate incoice quantity & price
        // Get all total amount by activity and type
        $count_invoices = ci()->invoice_detail_m->count_envelope_bymonth($customer_id, $target_year, $target_month);
        $pre_invoice_id = '';
        $index = 0;
        $total_count = count($count_invoices);

        // Loop and calculate invoices amount
        foreach ($count_invoices as $invoice) {
            $index++;
            if ($pre_invoice_id == '') {
                $pre_invoice_id = $invoice->invoice_summary_id;
            }

            if ($pre_invoice_id != $invoice->invoice_summary_id) {
                // update quantity and price.
                $this->updateActivityQuantityAndPrice(array(
                    'invoice_summary_id' => $invoice->invoice_summary_id,
                    // Quanity & Price
                    "incomming_items_free_quantity" => $incomming_items_free_quantity,
                    "incomming_items_free_netprice" => $incomming_items_free_netprice,
                    "incomming_items_private_quantity" => $incomming_items_private_quantity,
                    "incomming_items_private_netprice" => $incomming_items_private_netprice,
                    "incomming_items_business_quantity" => $incomming_items_business_quantity,
                    "incomming_items_business_netprice" => $incomming_items_business_netprice,

                    "envelope_scan_free_quantity" => $envelope_scan_free_quantity,
                    "envelope_scan_free_netprice" => $envelope_scan_free_netprice,
                    "envelope_scan_private_quantity" => $envelope_scan_private_quantity,
                    "envelope_scan_private_netprice" => $envelope_scan_private_netprice,
                    "envelope_scan_business_quantity" => $envelope_scan_business_quantity,
                    "envelope_scan_business_netprice" => $envelope_scan_business_netprice,

                    "item_scan_free_quantity" => $item_scan_free_quantity,
                    "item_scan_free_netprice" => $item_scan_free_netprice,
                    "item_scan_private_quantity" => $item_scan_private_quantity,
                    "item_scan_private_netprice" => $item_scan_private_netprice,
                    "item_scan_business_quantity" => $item_scan_business_quantity,
                    "item_scan_business_netprice" => $item_scan_business_netprice,

                    "direct_shipping_free_quantity" => $direct_shipping_free_quantity,
                    "direct_shipping_free_netprice" => $direct_shipping_free_netprice,
                    "direct_shipping_private_quantity" => $direct_shipping_private_quantity,
                    "direct_shipping_private_netprice" => $direct_shipping_private_netprice,
                    "direct_shipping_business_quantity" => $direct_shipping_business_quantity,
                    "direct_shipping_business_netprice" => $direct_shipping_business_netprice,

                    "collect_shipping_free_quantity" => $collect_shipping_free_quantity,
                    "collect_shipping_free_netprice" => $collect_shipping_free_netprice,
                    "collect_shipping_private_quantity" => $collect_shipping_private_quantity,
                    "collect_shipping_private_netprice" => $collect_shipping_private_quantity,
                    "collect_shipping_business_quantity" => $collect_shipping_business_quantity,
                    "collect_shipping_business_netprice" => $collect_shipping_business_netprice,

                    "additional_pages_scanning_free_quantity" => $additional_pages_scanning_free_quantity,
                    "additional_pages_scanning_free_netprice" => $additional_pages_scanning_free_netprice,
                    "additional_pages_scanning_private_quantity" => $additional_pages_scanning_private_quantity,
                    "additional_pages_scanning_private_netprice" => $additional_pages_scanning_private_netprice,
                    "additional_pages_scanning_business_quantity" => $additional_pages_scanning_business_quantity,
                    "additional_pages_scanning_business_netprice" => $additional_pages_scanning_business_netprice,

                    //"custom_declaration_outgoing_quantity_01" => $custom_declaration_outgoing_quantity_01,
                    //"custom_declaration_outgoing_quantity_02" => $custom_declaration_outgoing_quantity_02,
                    //"custom_declaration_outgoing_price_01" => $custom_declaration_outgoing_price_01,
                    //"custom_declaration_outgoing_price_02" => $custom_declaration_outgoing_price_02
                ));

                // Reset quantity and price.
                $incomming_items_free_quantity = 0;
                $incomming_items_free_netprice = 0;
                $incomming_items_private_quantity = 0;
                $incomming_items_private_netprice = 0;
                $incomming_items_business_quantity = 0;
                $incomming_items_business_netprice = 0;

                $envelope_scan_free_quantity = 0;
                $envelope_scan_free_netprice = 0;
                $envelope_scan_private_quantity = 0;
                $envelope_scan_private_netprice = 0;
                $envelope_scan_business_quantity = 0;
                $envelope_scan_business_netprice = 0;

                $item_scan_free_quantity = 0;
                $item_scan_free_netprice = 0;
                $item_scan_private_quantity = 0;
                $item_scan_private_netprice = 0;
                $item_scan_business_quantity = 0;
                $item_scan_business_netprice = 0;

                $direct_shipping_free_quantity = 0;
                $direct_shipping_free_netprice = 0;
                $direct_shipping_private_quantity = 0;
                $direct_shipping_private_netprice = 0;
                $direct_shipping_business_quantity = 0;
                $direct_shipping_business_netprice = 0;

                $collect_shipping_free_quantity = 0;
                $collect_shipping_free_netprice = 0;
                $private_collect_shipping_quantity = 0;
                $collect_shipping_private_quantity = 0;
                $collect_shipping_business_quantity = 0;
                $collect_shipping_business_netprice = 0;

                $additional_pages_scanning_free_quantity = 0;
                $additional_pages_scanning_free_netprice = 0;
                $additional_pages_scanning_private_quantity = 0;
                $additional_pages_scanning_private_netprice = 0;
                $additional_pages_scanning_business_quantity = 0;
                $additional_pages_scanning_business_netprice = 0;

                $custom_declaration_outgoing_quantity_01 = 0;
                $custom_declaration_outgoing_quantity_02 = 0;
                $custom_declaration_outgoing_price_01 = 0;
                $custom_declaration_outgoing_price_02 = 0;
            }

            // get values
            $pre_invoice_id = $invoice->invoice_summary_id;
            $postbox_type = $invoice->type . '';
            $activity = $invoice->activity . '';
            $activity_type = $invoice->activity_type . '';
            $quantity = $invoice->quantity;
            $price = $invoice->price;
            if (empty($postbox_type)) {
                $postbox_type = $invoice->account_type;
            }

            // If postbox type is FREE
            if ($postbox_type === APConstants::FREE_TYPE) {
                if ($activity_type === APConstants::INCOMMING_ACTIVITY_TYPE) {
                    $incomming_items_free_quantity = $quantity;
                    $incomming_items_free_netprice = $price;
                } else if ($activity_type === APConstants::ENVELOPE_SCAN_ACTIVITY_TYPE) {
                    $envelope_scan_free_quantity = $quantity;
                    $envelope_scan_free_netprice = $price;
                } else if ($activity_type === APConstants::ITEM_SCAN_ACTIVITY_TYPE) {
                    $item_scan_free_quantity = $quantity;
                    $item_scan_free_netprice = $price;
                } else if ($activity_type === APConstants::DIRECT_SHIPPING_ACTIVITY_TYPE) {
                    $direct_shipping_free_quantity = $quantity;
                    $direct_shipping_free_netprice = $price;
                } else if ($activity_type === APConstants::COLLECT_SHIPPING_ACTIVITY_TYPE) {
                    $collect_shipping_free_quantity = $quantity;
                    $collect_shipping_free_netprice = $price;
                } else if ($activity_type === APConstants::ADDITIONAL_SCAN_ACTIVITY_TYPE) {
                    $additional_pages_scanning_free_quantity = $quantity;
                    $additional_pages_scanning_free_netprice = $price;
                } else if ($activity_type === APConstants::CUSTOMS_DECLARATION_01_ACTIVITY_TYPE) {
                    $custom_declaration_outgoing_quantity_01 = $quantity;
                    $custom_declaration_outgoing_price_01 = $price;
                } else if ($activity_type === APConstants::CUSTOMS_DECLARATION_02_ACTIVITY_TYPE) {
                    $custom_declaration_outgoing_quantity_02 = $quantity;
                    $custom_declaration_outgoing_price_02 = $price;
                }
            } // Private
            else if ($postbox_type === APConstants::PRIVATE_TYPE) {
                if ($activity_type === APConstants::INCOMMING_ACTIVITY_TYPE) {
                    $incomming_items_private_quantity = $quantity;
                    $incomming_items_private_netprice = $price;
                } else if ($activity_type === APConstants::ENVELOPE_SCAN_ACTIVITY_TYPE) {
                    $envelope_scan_private_quantity = $quantity;
                    $envelope_scan_private_netprice = $price;
                } else if ($activity_type === APConstants::ITEM_SCAN_ACTIVITY_TYPE) {
                    $item_scan_private_quantity = $quantity;
                    $item_scan_private_netprice = $price;
                } else if ($activity_type === APConstants::DIRECT_SHIPPING_ACTIVITY_TYPE) {
                    $direct_shipping_private_quantity = $quantity;
                    $direct_shipping_private_netprice = $price;
                } else if ($activity_type === APConstants::COLLECT_SHIPPING_ACTIVITY_TYPE) {
                    $collect_shipping_private_quantity = $quantity;
                    $collect_shipping_private_netprice = $price;
                } else if ($activity_type === APConstants::ADDITIONAL_SCAN_ACTIVITY_TYPE) {
                    $additional_pages_scanning_private_quantity = $quantity;
                    $additional_pages_scanning_private_netprice = $price;
                } else if ($activity_type === APConstants::CUSTOMS_DECLARATION_01_ACTIVITY_TYPE) {
                    $custom_declaration_outgoing_quantity_01 = $quantity;
                    $custom_declaration_outgoing_price_01 = $price;
                } else if ($activity_type === APConstants::CUSTOMS_DECLARATION_02_ACTIVITY_TYPE) {
                    $custom_declaration_outgoing_quantity_02 = $quantity;
                    $custom_declaration_outgoing_price_02 = $price;
                }
            } // Business
            else if ($postbox_type === APConstants::BUSINESS_TYPE || $postbox_type == APConstants::ENTERPRISE_CUSTOMER) {
                if ($activity_type === APConstants::INCOMMING_ACTIVITY_TYPE) {
                    $incomming_items_business_quantity = $quantity;
                    $incomming_items_business_netprice = $price;
                } else if ($activity_type === APConstants::ENVELOPE_SCAN_ACTIVITY_TYPE) {
                    $envelope_scan_business_quantity = $quantity;
                    $envelope_scan_business_netprice = $price;
                } else if ($activity_type === APConstants::ITEM_SCAN_ACTIVITY_TYPE) {
                    $item_scan_business_quantity = $quantity;
                    $item_scan_business_netprice = $price;
                } else if ($activity_type === APConstants::DIRECT_SHIPPING_ACTIVITY_TYPE) {
                    $direct_shipping_business_quantity = $quantity;
                    $direct_shipping_business_netprice = $price;
                } else if ($activity_type === APConstants::COLLECT_SHIPPING_ACTIVITY_TYPE) {
                    $collect_shipping_business_quantity = $quantity;
                    $collect_shipping_business_netprice = $price;
                } else if ($activity_type === APConstants::ADDITIONAL_SCAN_ACTIVITY_TYPE) {
                    $additional_pages_scanning_business_quantity = $quantity;
                    $additional_pages_scanning_business_netprice = $price;
                } else if ($activity_type === APConstants::CUSTOMS_DECLARATION_01_ACTIVITY_TYPE) {
                    $custom_declaration_outgoing_quantity_01 = $quantity;
                    $custom_declaration_outgoing_price_01 = $price;
                } else if ($activity_type === APConstants::CUSTOMS_DECLARATION_02_ACTIVITY_TYPE) {
                    $custom_declaration_outgoing_quantity_02 = $quantity;
                    $custom_declaration_outgoing_price_02 = $price;
                }
            }

            //  update if end of array
            if ($index == $total_count) {
                // update quantity and price.
                $this->updateActivityQuantityAndPrice(array(
                    'invoice_summary_id' => $invoice->invoice_summary_id,
                    // Quanity & Price
                    "incomming_items_free_quantity" => $incomming_items_free_quantity,
                    "incomming_items_free_netprice" => $incomming_items_free_netprice,
                    "incomming_items_private_quantity" => $incomming_items_private_quantity,
                    "incomming_items_private_netprice" => $incomming_items_private_netprice,
                    "incomming_items_business_quantity" => $incomming_items_business_quantity,
                    "incomming_items_business_netprice" => $incomming_items_business_netprice,

                    "envelope_scan_free_quantity" => $envelope_scan_free_quantity,
                    "envelope_scan_free_netprice" => $envelope_scan_free_netprice,
                    "envelope_scan_private_quantity" => $envelope_scan_private_quantity,
                    "envelope_scan_private_netprice" => $envelope_scan_private_netprice,
                    "envelope_scan_business_quantity" => $envelope_scan_business_quantity,
                    "envelope_scan_business_netprice" => $envelope_scan_business_netprice,

                    "item_scan_free_quantity" => $item_scan_free_quantity,
                    "item_scan_free_netprice" => $item_scan_free_netprice,
                    "item_scan_private_quantity" => $item_scan_private_quantity,
                    "item_scan_private_netprice" => $item_scan_private_netprice,
                    "item_scan_business_quantity" => $item_scan_business_quantity,
                    "item_scan_business_netprice" => $item_scan_business_netprice,

                    "direct_shipping_free_quantity" => $direct_shipping_free_quantity,
                    "direct_shipping_free_netprice" => $direct_shipping_free_netprice,
                    "direct_shipping_private_quantity" => $direct_shipping_private_quantity,
                    "direct_shipping_private_netprice" => $direct_shipping_private_netprice,
                    "direct_shipping_business_quantity" => $direct_shipping_business_quantity,
                    "direct_shipping_business_netprice" => $direct_shipping_business_netprice,

                    "collect_shipping_free_quantity" => $collect_shipping_free_quantity,
                    "collect_shipping_free_netprice" => $collect_shipping_free_netprice,
                    "collect_shipping_private_quantity" => $collect_shipping_private_quantity,
                    "collect_shipping_private_netprice" => $collect_shipping_private_quantity,
                    "collect_shipping_business_quantity" => $collect_shipping_business_quantity,
                    "collect_shipping_business_netprice" => $collect_shipping_business_netprice,

                    "additional_pages_scanning_free_quantity" => $additional_pages_scanning_free_quantity,
                    "additional_pages_scanning_free_netprice" => $additional_pages_scanning_free_netprice,
                    "additional_pages_scanning_private_quantity" => $additional_pages_scanning_private_quantity,
                    "additional_pages_scanning_private_netprice" => $additional_pages_scanning_private_netprice,
                    "additional_pages_scanning_business_quantity" => $additional_pages_scanning_business_quantity,
                    "additional_pages_scanning_business_netprice" => $additional_pages_scanning_business_netprice,

                    //"custom_declaration_outgoing_quantity_01" => $custom_declaration_outgoing_quantity_01,
                    //"custom_declaration_outgoing_quantity_02" => $custom_declaration_outgoing_quantity_02,
                    //"custom_declaration_outgoing_price_01" => $custom_declaration_outgoing_price_01,
                    //"custom_declaration_outgoing_price_02" => $custom_declaration_outgoing_price_02
                ));

                // Reset quantity and price.
                $incomming_items_free_quantity = 0;
                $incomming_items_free_netprice = 0;
                $incomming_items_private_quantity = 0;
                $incomming_items_private_netprice = 0;
                $incomming_items_business_quantity = 0;
                $incomming_items_business_netprice = 0;

                $envelope_scan_free_quantity = 0;
                $envelope_scan_free_netprice = 0;
                $envelope_scan_private_quantity = 0;
                $envelope_scan_private_netprice = 0;
                $envelope_scan_business_quantity = 0;
                $envelope_scan_business_netprice = 0;

                $item_scan_free_quantity = 0;
                $item_scan_free_netprice = 0;
                $item_scan_private_quantity = 0;
                $item_scan_private_netprice = 0;
                $item_scan_business_quantity = 0;
                $item_scan_business_netprice = 0;

                $direct_shipping_free_quantity = 0;
                $direct_shipping_free_netprice = 0;
                $direct_shipping_private_quantity = 0;
                $direct_shipping_private_netprice = 0;
                $direct_shipping_business_quantity = 0;
                $direct_shipping_business_netprice = 0;

                $collect_shipping_free_quantity = 0;
                $collect_shipping_free_netprice = 0;
                $private_collect_shipping_quantity = 0;
                $collect_shipping_private_quantity = 0;
                $collect_shipping_business_quantity = 0;
                $collect_shipping_business_netprice = 0;

                $additional_pages_scanning_free_quantity = 0;
                $additional_pages_scanning_free_netprice = 0;
                $additional_pages_scanning_private_quantity = 0;
                $additional_pages_scanning_private_netprice = 0;
                $additional_pages_scanning_business_quantity = 0;
                $additional_pages_scanning_business_netprice = 0;

                $custom_declaration_outgoing_quantity_01 = 0;
                $custom_declaration_outgoing_quantity_02 = 0;
                $custom_declaration_outgoing_price_01 = 0;
                $custom_declaration_outgoing_price_02 = 0;
            }
        } // end foreach

        // Update charge fee and charge postal fee
        $envelopes_shipping = ci()->envelope_shipping_m->summary_shipping_fee_of_customer($customer_id, $target_year.$target_month );
        if($envelopes_shipping){
            ci()->invoice_summary_m->update_by_many(array(
                "customer_id" => $customer_id,
                "(invoice_type is null OR invoice_type <> 2)" => null,
                "LEFT(invoice_month,6)" => $target_year.$target_month
            ), array(
                "forwarding_charges_fee" => $envelopes_shipping[0]->forwarding_charges_fee,
                "forwarding_charges_postal" => $envelopes_shipping[0]->forwarding_charges_postal
            ));
        }

        // Calculate total invoice
        APUtils::updateTotalInvoiceOfInvoiceSummaryTargetMonth($customer_id, $target_year, $target_month);

        // #568
        $this->cal_invoice_summary_bylocation($customer_id, $target_year, $target_month, $customerVAT);
        // Comment this code because it run very slow now
        // APUtils::updateOpenBalanceToDB($customer_id);
    }

    /**
     * Summary data from tables [envelope_summary_month] to tables [invoice_summary]. Summary from invoice_detail to invoice_summary
     *
     * @param unknown_type $customer_id
     * @param unknown_type $target_year
     * @param unknown_type $target_month
     */
    public function cal_invoice_summary_bylocation($customer_id, $target_year, $target_month, $customerVAT)
    {
        ci()->load->model('invoices/invoice_detail_m');
        ci()->load->model('invoices/invoice_summary_by_location_m');
        ci()->load->model('mailbox/postbox_m');
        ci()->load->model('scans/envelope_shipping_m');

        // Get all location id
        $locations = ci()->postbox_m->get_all_location($customer_id);

        // For each location
        foreach ($locations as $location) {
            $location_id = $location->location_available_id;
            
            // init invoice record.
            $this->initInvoiceByLocation($location_id, $customer_id, $target_year.$target_month);

            // Amount
            $incomming_items_free_account = 0;
            $envelope_scan_free_account = 0;
            $item_scan_free_account = 0;
            $direct_shipping_free_account = 0;
            $collect_shipping_free_account = 0;
            $additional_pages_scanning_free_amount = 0;

            $incomming_items_private_account = 0;
            $envelope_scan_private_account = 0;
            $item_scan_private_account = 0;
            $direct_shipping_private_account = 0;
            $collect_shipping_private_account = 0;
            $additional_pages_scanning_private_amount = 0;

            $incomming_items_business_account = 0;
            $envelope_scan_business_account = 0;
            $item_scan_business_account = 0;
            $direct_shipping_business_account = 0;
            $collect_shipping_business_account = 0;
            $additional_pages_scanning_business_amount = 0;
            $custom_declaration_outgoing_price_01 = 0;
            $custom_declaration_outgoing_price_02 = 0;
            
            $api_access_amount = 0;
            $own_location_amount = 0;
            $touch_panel_own_location_amount = 0;
            $own_mobile_app_amount = 0;
            $clevver_subdomain_amount = 0;
            $own_subdomain_amount = 0;

            // Get all total amount by activity and type
            $invoices = ci()->invoice_detail_m->summary_envelope_bymonth_location($customer_id, $location_id, $target_year, $target_month);

            // Loop and calculate invoices amount
            $pre_invoice_id = '';
            $index = 0;
            //$total_count = count($invoices);
            foreach ($invoices as $invoice) {
                $index++;
                
                // Gets data.
                $pre_invoice_id = $invoice->invoice_summary_id;
                $postbox_type = $invoice->type . '';
                $activity = $invoice->activity . '';
                $activity_type = $invoice->activity_type . '';
                $amount = $invoice->amount;
                if (empty($postbox_type)) {
                    $postbox_type = $invoice->account_type;
                }
                
                // If postbox type is FREE
                if ($postbox_type === APConstants::FREE_TYPE) {
                    if ($activity_type === APConstants::INCOMMING_ACTIVITY_TYPE) {
                        $incomming_items_free_account = $amount;
                    } else if ($activity_type === APConstants::ENVELOPE_SCAN_ACTIVITY_TYPE) {
                        $envelope_scan_free_account = $amount;
                    } else if ($activity_type === APConstants::ITEM_SCAN_ACTIVITY_TYPE) {
                        $item_scan_free_account = $amount;
                    } else if ($activity_type === APConstants::DIRECT_SHIPPING_ACTIVITY_TYPE) {
                        $direct_shipping_free_account = $amount;
                    } else if ($activity_type === APConstants::COLLECT_SHIPPING_ACTIVITY_TYPE) {
                        $collect_shipping_free_account = $amount;
                    } else if ($activity_type === APConstants::ADDITIONAL_SCAN_ACTIVITY_TYPE) {
                        $additional_pages_scanning_free_amount = $amount;
                    }else if ($activity_type === APConstants::CUSTOMS_DECLARATION_01_ACTIVITY_TYPE){
                        $custom_declaration_outgoing_price_01 += $amount;
                    }else if ($activity_type === APConstants::CUSTOMS_DECLARATION_02_ACTIVITY_TYPE){
                        $custom_declaration_outgoing_price_02 += $amount;
                    }
                } // Private
                else if ($postbox_type === APConstants::PRIVATE_TYPE) {
                    if ($activity_type === APConstants::INCOMMING_ACTIVITY_TYPE) {
                        $incomming_items_private_account = $amount;
                    } else if ($activity_type === APConstants::ENVELOPE_SCAN_ACTIVITY_TYPE) {
                        $envelope_scan_private_account = $amount;
                    } else if ($activity_type === APConstants::ITEM_SCAN_ACTIVITY_TYPE) {
                        $item_scan_private_account = $amount;
                    } else if ($activity_type === APConstants::DIRECT_SHIPPING_ACTIVITY_TYPE) {
                        $direct_shipping_private_account = $amount;
                    } else if ($activity_type === APConstants::COLLECT_SHIPPING_ACTIVITY_TYPE) {
                        $collect_shipping_private_account = $amount;
                    } else if ($activity_type === APConstants::ADDITIONAL_SCAN_ACTIVITY_TYPE) {
                        $additional_pages_scanning_private_amount = $amount;
                    }else if ($activity_type === APConstants::CUSTOMS_DECLARATION_01_ACTIVITY_TYPE){
                        $custom_declaration_outgoing_price_01 += $amount;
                    }else if ($activity_type === APConstants::CUSTOMS_DECLARATION_02_ACTIVITY_TYPE){
                        $custom_declaration_outgoing_price_02 += $amount;
                    }
                } // Business
                else if ($postbox_type === APConstants::BUSINESS_TYPE || $postbox_type == APConstants::ENTERPRISE_CUSTOMER) {
                    if ($activity_type === APConstants::INCOMMING_ACTIVITY_TYPE) {
                        $incomming_items_business_account = $amount;
                    } else if ($activity_type === APConstants::ENVELOPE_SCAN_ACTIVITY_TYPE) {
                        $envelope_scan_business_account = $amount;
                    } else if ($activity_type === APConstants::ITEM_SCAN_ACTIVITY_TYPE) {
                        $item_scan_business_account = $amount;
                    } else if ($activity_type === APConstants::DIRECT_SHIPPING_ACTIVITY_TYPE) {
                        $direct_shipping_business_account = $amount;
                    } else if ($activity_type === APConstants::COLLECT_SHIPPING_ACTIVITY_TYPE) {
                        $collect_shipping_business_account = $amount;
                    } else if ($activity_type === APConstants::ADDITIONAL_SCAN_ACTIVITY_TYPE) {
                        $additional_pages_scanning_business_amount = $amount;
                    }else if ($activity_type === APConstants::CUSTOMS_DECLARATION_01_ACTIVITY_TYPE){
                        $custom_declaration_outgoing_price_01 += $amount;
                    }else if ($activity_type === APConstants::CUSTOMS_DECLARATION_02_ACTIVITY_TYPE){
                        $custom_declaration_outgoing_price_02 += $amount;
                    }else if ($activity_type === APConstants::INVOICE_ACTIVITY_TYPE_API_ACCESS){
                        $api_access_amount += $amount;
                    }else if ($activity_type === APConstants::INVOICE_ACTIVITY_TYPE_OWN_LOCATION){
                        $own_location_amount += $amount;
                    }else if ($activity_type === APConstants::INVOICE_ACTIVITY_TYPE_TOUCH_PANEL_OWN_LOCATION){
                        $touch_panel_own_location_amount += $amount;
                    }else if ($activity_type === APConstants::INVOICE_ACTIVITY_TYPE_OWN_MOBILE_APP){
                        $own_mobile_app_amount += $amount;
                    }else if ($activity_type === APConstants::INVOICE_ACTIVITY_TYPE_CLEVVER_SUBDOMAIN){
                        $clevver_subdomain_amount += $amount;
                    }else if ($activity_type === APConstants::INVOICE_ACTIVITY_TYPE_OWN_SUBDOMAIN){
                        $own_subdomain_amount += $amount;
                    }
                }
            } // end foreach
            
            // Update activity amount and break.
            $this->updateActivityAmountByLocation(array(
                "customer_id" => $customer_id,
                "invoice_month" => $target_year.$target_month,
                // amount.
                "incomming_items_free_account" => $incomming_items_free_account,
                "envelope_scan_free_account" => $envelope_scan_free_account,
                "item_scan_free_account" => $item_scan_free_account,
                "direct_shipping_free_account" => $direct_shipping_free_account,
                "collect_shipping_free_account" => $collect_shipping_free_account,
                "additional_pages_scanning_free_amount" => $additional_pages_scanning_free_amount,

                "incomming_items_private_account" => $incomming_items_private_account,
                "envelope_scan_private_account" => $envelope_scan_private_account,
                "item_scan_private_account" => $item_scan_private_account,
                "direct_shipping_private_account" => $direct_shipping_private_account,
                "collect_shipping_private_account" => $collect_shipping_private_account,
                "additional_pages_scanning_private_amount" => $additional_pages_scanning_private_amount,

                "incomming_items_business_account" => $incomming_items_business_account,
                "envelope_scan_business_account" => $envelope_scan_business_account,
                "item_scan_business_account" => $item_scan_business_account,
                "direct_shipping_business_account" => $direct_shipping_business_account,
                "collect_shipping_business_account" => $collect_shipping_business_account,
                "additional_pages_scanning_business_amount" => $additional_pages_scanning_business_amount,
                
                "custom_declaration_outgoing_quantity_01" => 1,
                "custom_declaration_outgoing_quantity_02" => 1,
                "custom_declaration_outgoing_price_01" => $custom_declaration_outgoing_price_01,
                "custom_declaration_outgoing_price_02" => $custom_declaration_outgoing_price_02,
                // Enterprise
                "api_access_amount" => $api_access_amount,
                "own_location_amount" => $own_location_amount,
                "touch_panel_own_location_amount" => $touch_panel_own_location_amount,
                "own_mobile_app_amount" => $own_mobile_app_amount,
                "clevver_subdomain_amount" => $clevver_subdomain_amount,
                "own_subdomain_amount" => $own_subdomain_amount,

                "vat_case" => $customerVAT->vat_case_id,
                "vat" => $customerVAT->rate,
                "location_id" => $location_id
            ));


            // Quantity & Price
            $incomming_items_free_quantity = 0;
            $incomming_items_free_netprice = 0;
            $incomming_items_private_quantity = 0;
            $incomming_items_private_netprice = 0;
            $incomming_items_business_quantity = 0;
            $incomming_items_business_netprice = 0;

            $envelope_scan_free_quantity = 0;
            $envelope_scan_free_netprice = 0;
            $envelope_scan_private_quantity = 0;
            $envelope_scan_private_netprice = 0;
            $envelope_scan_business_quantity = 0;
            $envelope_scan_business_netprice = 0;

            $item_scan_free_quantity = 0;
            $item_scan_free_netprice = 0;
            $item_scan_private_quantity = 0;
            $item_scan_private_netprice = 0;
            $item_scan_business_quantity = 0;
            $item_scan_business_netprice = 0;

            $direct_shipping_free_quantity = 0;
            $direct_shipping_free_netprice = 0;
            $direct_shipping_private_quantity = 0;
            $direct_shipping_private_netprice = 0;
            $direct_shipping_business_quantity = 0;
            $direct_shipping_business_netprice = 0;

            $collect_shipping_free_quantity = 0;
            $collect_shipping_free_netprice = 0;
            $private_collect_shipping_quantity = 0;
            $collect_shipping_private_quantity = 0;
            $collect_shipping_business_quantity = 0;
            $collect_shipping_business_netprice = 0;

            $additional_pages_scanning_free_quantity = 0;
            $additional_pages_scanning_free_netprice = 0;
            $additional_pages_scanning_private_quantity = 0;
            $additional_pages_scanning_private_netprice = 0;
            $additional_pages_scanning_business_quantity = 0;
            $additional_pages_scanning_business_netprice = 0;

            $custom_declaration_outgoing_quantity_01 = 0;
            $custom_declaration_outgoing_quantity_02 = 0;
            $custom_declaration_outgoing_price_01 = 0;
            $custom_declaration_outgoing_price_02 = 0;

            // Loop and calculate incoice quantity & price
            // Get all total amount by activity and type
            $count_invoices = ci()->invoice_detail_m->count_envelope_bymonth_location($customer_id, $location_id, $target_year, $target_month);
            $index = 0;
            //$total_count = count($count_invoices);

            // Loop and calculate invoices amount
            foreach ($count_invoices as $invoice) {
                $index++;

                // get values
                $postbox_type = $invoice->type . '';
                $activity = $invoice->activity . '';
                $activity_type = $invoice->activity_type . '';
                $quantity = $invoice->quantity;
                $price = $invoice->price;
                if (empty($postbox_type)) {
                    $postbox_type = $invoice->account_type;
                }

                // If postbox type is FREE
                if ($postbox_type === APConstants::FREE_TYPE) {
                    if ($activity_type === APConstants::INCOMMING_ACTIVITY_TYPE) {
                        $incomming_items_free_quantity = $quantity;
                        $incomming_items_free_netprice = $price;
                    } else if ($activity_type === APConstants::ENVELOPE_SCAN_ACTIVITY_TYPE) {
                        $envelope_scan_free_quantity = $quantity;
                        $envelope_scan_free_netprice = $price;
                    } else if ($activity_type === APConstants::ITEM_SCAN_ACTIVITY_TYPE) {
                        $item_scan_free_quantity = $quantity;
                        $item_scan_free_netprice = $price;
                    } else if ($activity_type === APConstants::DIRECT_SHIPPING_ACTIVITY_TYPE) {
                        $direct_shipping_free_quantity = $quantity;
                        $direct_shipping_free_netprice = $price;
                    } else if ($activity_type === APConstants::COLLECT_SHIPPING_ACTIVITY_TYPE) {
                        $collect_shipping_free_quantity = $quantity;
                        $collect_shipping_free_netprice = $price;
                    } else if ($activity_type === APConstants::ADDITIONAL_SCAN_ACTIVITY_TYPE) {
                        $additional_pages_scanning_free_quantity = $quantity;
                        $additional_pages_scanning_free_netprice = $price;
                    } else if ($activity_type === APConstants::CUSTOMS_DECLARATION_01_ACTIVITY_TYPE) {
                        $custom_declaration_outgoing_quantity_01 = $quantity;
                        $custom_declaration_outgoing_price_01 = $price;
                    } else if ($activity_type === APConstants::CUSTOMS_DECLARATION_02_ACTIVITY_TYPE) {
                        $custom_declaration_outgoing_quantity_02 = $quantity;
                        $custom_declaration_outgoing_price_02 = $price;
                    }
                } // Private
                else if ($postbox_type === APConstants::PRIVATE_TYPE) {
                    if ($activity_type === APConstants::INCOMMING_ACTIVITY_TYPE) {
                        $incomming_items_private_quantity = $quantity;
                        $incomming_items_private_netprice = $price;
                    } else if ($activity_type === APConstants::ENVELOPE_SCAN_ACTIVITY_TYPE) {
                        $envelope_scan_private_quantity = $quantity;
                        $envelope_scan_private_netprice = $price;
                    } else if ($activity_type === APConstants::ITEM_SCAN_ACTIVITY_TYPE) {
                        $item_scan_private_quantity = $quantity;
                        $item_scan_private_netprice = $price;
                    } else if ($activity_type === APConstants::DIRECT_SHIPPING_ACTIVITY_TYPE) {
                        $direct_shipping_private_quantity = $quantity;
                        $direct_shipping_private_netprice = $price;
                    } else if ($activity_type === APConstants::COLLECT_SHIPPING_ACTIVITY_TYPE) {
                        $collect_shipping_private_quantity = $quantity;
                        $collect_shipping_private_netprice = $price;
                    } else if ($activity_type === APConstants::ADDITIONAL_SCAN_ACTIVITY_TYPE) {
                        $additional_pages_scanning_private_quantity = $quantity;
                        $additional_pages_scanning_private_netprice = $price;
                    } else if ($activity_type === APConstants::CUSTOMS_DECLARATION_01_ACTIVITY_TYPE) {
                        $custom_declaration_outgoing_quantity_01 = $quantity;
                        $custom_declaration_outgoing_price_01 = $price;
                    } else if ($activity_type === APConstants::CUSTOMS_DECLARATION_02_ACTIVITY_TYPE) {
                        $custom_declaration_outgoing_quantity_02 = $quantity;
                        $custom_declaration_outgoing_price_02 = $price;
                    }
                } // Business
                else if ($postbox_type === APConstants::BUSINESS_TYPE || $postbox_type == APConstants::ENTERPRISE_CUSTOMER) {
                    if ($activity_type === APConstants::INCOMMING_ACTIVITY_TYPE) {
                        $incomming_items_business_quantity = $quantity;
                        $incomming_items_business_netprice = $price;
                    } else if ($activity_type === APConstants::ENVELOPE_SCAN_ACTIVITY_TYPE) {
                        $envelope_scan_business_quantity = $quantity;
                        $envelope_scan_business_netprice = $price;
                    } else if ($activity_type === APConstants::ITEM_SCAN_ACTIVITY_TYPE) {
                        $item_scan_business_quantity = $quantity;
                        $item_scan_business_netprice = $price;
                    } else if ($activity_type === APConstants::DIRECT_SHIPPING_ACTIVITY_TYPE) {
                        $direct_shipping_business_quantity = $quantity;
                        $direct_shipping_business_netprice = $price;
                    } else if ($activity_type === APConstants::COLLECT_SHIPPING_ACTIVITY_TYPE) {
                        $collect_shipping_business_quantity = $quantity;
                        $collect_shipping_business_netprice = $price;
                    } else if ($activity_type === APConstants::ADDITIONAL_SCAN_ACTIVITY_TYPE) {
                        $additional_pages_scanning_business_quantity = $quantity;
                        $additional_pages_scanning_business_netprice = $price;
                    } else if ($activity_type === APConstants::CUSTOMS_DECLARATION_01_ACTIVITY_TYPE) {
                        $custom_declaration_outgoing_quantity_01 = $quantity;
                        $custom_declaration_outgoing_price_01 = $price;
                    } else if ($activity_type === APConstants::CUSTOMS_DECLARATION_02_ACTIVITY_TYPE) {
                        $custom_declaration_outgoing_quantity_02 = $quantity;
                        $custom_declaration_outgoing_price_02 = $price;
                    }
                }
            } // end foreach
            
            // update quantity and price.
            $this->updateActivityQuantityAndPriceByLocation(array(
                "customer_id" => $customer_id,
                "invoice_month" => $target_year.$target_month,
                // Quanity & Price
                "incomming_items_free_quantity" => $incomming_items_free_quantity,
                "incomming_items_free_netprice" => $incomming_items_free_netprice,
                "incomming_items_private_quantity" => $incomming_items_private_quantity,
                "incomming_items_private_netprice" => $incomming_items_private_netprice,
                "incomming_items_business_quantity" => $incomming_items_business_quantity,
                "incomming_items_business_netprice" => $incomming_items_business_netprice,

                "envelope_scan_free_quantity" => $envelope_scan_free_quantity,
                "envelope_scan_free_netprice" => $envelope_scan_free_netprice,
                "envelope_scan_private_quantity" => $envelope_scan_private_quantity,
                "envelope_scan_private_netprice" => $envelope_scan_private_netprice,
                "envelope_scan_business_quantity" => $envelope_scan_business_quantity,
                "envelope_scan_business_netprice" => $envelope_scan_business_netprice,

                "item_scan_free_quantity" => $item_scan_free_quantity,
                "item_scan_free_netprice" => $item_scan_free_netprice,
                "item_scan_private_quantity" => $item_scan_private_quantity,
                "item_scan_private_netprice" => $item_scan_private_netprice,
                "item_scan_business_quantity" => $item_scan_business_quantity,
                "item_scan_business_netprice" => $item_scan_business_netprice,

                "direct_shipping_free_quantity" => $direct_shipping_free_quantity,
                "direct_shipping_free_netprice" => $direct_shipping_free_netprice,
                "direct_shipping_private_quantity" => $direct_shipping_private_quantity,
                "direct_shipping_private_netprice" => $direct_shipping_private_netprice,
                "direct_shipping_business_quantity" => $direct_shipping_business_quantity,
                "direct_shipping_business_netprice" => $direct_shipping_business_netprice,

                "collect_shipping_free_quantity" => $collect_shipping_free_quantity,
                "collect_shipping_free_netprice" => $collect_shipping_free_netprice,
                "collect_shipping_private_quantity" => $collect_shipping_private_quantity,
                "collect_shipping_private_netprice" => $collect_shipping_private_quantity,
                "collect_shipping_business_quantity" => $collect_shipping_business_quantity,
                "collect_shipping_business_netprice" => $collect_shipping_business_netprice,

                "additional_pages_scanning_free_quantity" => $additional_pages_scanning_free_quantity,
                "additional_pages_scanning_free_netprice" => $additional_pages_scanning_free_netprice,
                "additional_pages_scanning_private_quantity" => $additional_pages_scanning_private_quantity,
                "additional_pages_scanning_private_netprice" => $additional_pages_scanning_private_netprice,
                "additional_pages_scanning_business_quantity" => $additional_pages_scanning_business_quantity,
                "additional_pages_scanning_business_netprice" => $additional_pages_scanning_business_netprice,

                //"custom_declaration_outgoing_quantity_01" => $custom_declaration_outgoing_quantity_01,
                //"custom_declaration_outgoing_quantity_02" => $custom_declaration_outgoing_quantity_02,
                //"custom_declaration_outgoing_price_01" => $custom_declaration_outgoing_price_01,
                //"custom_declaration_outgoing_price_02" => $custom_declaration_outgoing_price_02,
                
                "vat_case" => $customerVAT->vat_case_id,
                "vat" => $customerVAT->rate,
                "location_id" => $location_id
            ));
        }
        
        
        // Update charge fee and charge postal fee
        $envelopes_shipping = ci()->envelope_shipping_m->summary_shipping_fee_of_customer($customer_id, $target_year.$target_month, true );
        if($envelopes_shipping){
            foreach($envelopes_shipping as $e){
                ci()->invoice_summary_by_location_m->update_by_many(array(
                    "customer_id" => $customer_id,
                    "(invoice_type is null OR invoice_type <> 2)" => null,
                    "LEFT(invoice_month,6)" => $target_year.$target_month,
                    "location_id" => $e->location_available_id
                ), array(
                    "forwarding_charges_fee" => $e->forwarding_charges_fee,
                    "forwarding_charges_postal" => $e->forwarding_charges_postal
                ));
            }
        }
        
        // calculate total invoice.
        ci()->invoice_summary_by_location_m->updateTotalInvoice($target_year.$target_month);
    }

    /**
     * Update activity invoice.
     *
     * @param unknown $param
     */
    private function updateActivityAmount($param)
    {
        ci()->invoice_summary_m->update_by_many(array(
            'id' => $param ['invoice_summary_id']
        ), array(
            // Amount
            "incomming_items_free_account" => $param['incomming_items_free_account'],
            "envelope_scan_free_account" => $param['envelope_scan_free_account'],
            "item_scan_free_account" => $param['item_scan_free_account'],
            "direct_shipping_free_account" => $param['direct_shipping_free_account'],
            "collect_shipping_free_account" => $param['collect_shipping_free_account'],

            "incomming_items_private_account" => $param['incomming_items_private_account'],
            "envelope_scan_private_account" => $param['envelope_scan_private_account'],
            "item_scan_private_account" => $param['item_scan_private_account'],
            "direct_shipping_private_account" => $param['direct_shipping_private_account'],
            "collect_shipping_private_account" => $param['collect_shipping_private_account'],

            "incomming_items_business_account" => $param['incomming_items_business_account'],
            "envelope_scan_business_account" => $param['envelope_scan_business_account'],
            "item_scan_business_account" => $param['item_scan_business_account'],
            "direct_shipping_business_account" => $param['direct_shipping_business_account'],
            "collect_shipping_business_account" => $param['collect_shipping_business_account'],

            "additional_pages_scanning_free_amount" => $param['additional_pages_scanning_free_amount'],
            "additional_pages_scanning_private_amount" => $param['additional_pages_scanning_private_amount'],
            "additional_pages_scanning_business_amount" => $param['additional_pages_scanning_business_amount'],
            
            "custom_declaration_outgoing_quantity_01" => 1,
            "custom_declaration_outgoing_quantity_02" => 1,
            "custom_declaration_outgoing_price_01" => $param['custom_declaration_outgoing_price_01'],
            "custom_declaration_outgoing_price_02" => $param['custom_declaration_outgoing_price_02'],
            
            "update_flag" => 0,
            "vat_case" => $param['vat_case'],
            "vat" => $param['vat'],
        ));
    }

    /**
     * Update quantity and price for activity.
     *
     * @param unknown $param
     */
    private function updateActivityQuantityAndPrice($param)
    {
        ci()->invoice_summary_m->update_by_many(array(
            'id' => $param ['invoice_summary_id']
        ), array(
            // Quanity & Price
            "incomming_items_free_quantity" => $param ['incomming_items_free_quantity'],
            "incomming_items_free_netprice" => $param ['incomming_items_free_netprice'],
            "incomming_items_private_quantity" => $param ['incomming_items_private_quantity'],
            "incomming_items_private_netprice" => $param ['incomming_items_private_netprice'],
            "incomming_items_business_quantity" => $param ['incomming_items_business_quantity'],
            "incomming_items_business_netprice" => $param ['incomming_items_business_netprice'],

            "envelope_scan_free_quantity" => $param ['envelope_scan_free_quantity'],
            "envelope_scan_free_netprice" => $param ['envelope_scan_free_netprice'],
            "envelope_scan_private_quantity" => $param ['envelope_scan_private_quantity'],
            "envelope_scan_private_netprice" => $param ['envelope_scan_private_netprice'],
            "envelope_scan_business_quantity" => $param ['envelope_scan_business_quantity'],
            "envelope_scan_business_netprice" => $param ['envelope_scan_business_netprice'],

            "item_scan_free_quantity" => $param ['item_scan_free_quantity'],
            "item_scan_free_netprice" => $param ['item_scan_free_netprice'],
            "item_scan_private_quantity" => $param ['item_scan_private_quantity'],
            "item_scan_private_netprice" => $param ['item_scan_private_netprice'],
            "item_scan_business_quantity" => $param ['item_scan_business_quantity'],
            "item_scan_business_netprice" => $param ['item_scan_business_netprice'],

            "direct_shipping_free_quantity" => $param ['direct_shipping_free_quantity'],
            "direct_shipping_free_netprice" => $param ['direct_shipping_free_netprice'],
            "direct_shipping_private_quantity" => $param ['direct_shipping_private_quantity'],
            "direct_shipping_private_netprice" => $param ['direct_shipping_private_netprice'],
            "direct_shipping_business_quantity" => $param ['direct_shipping_business_quantity'],
            "direct_shipping_business_netprice" => $param ['direct_shipping_business_netprice'],

            "collect_shipping_free_quantity" => $param ['collect_shipping_free_quantity'],
            "collect_shipping_free_netprice" => $param ['collect_shipping_free_netprice'],
            "collect_shipping_private_quantity" => $param ['collect_shipping_private_quantity'],
            "collect_shipping_private_netprice" => $param ['collect_shipping_private_quantity'],
            "collect_shipping_business_quantity" => $param ['collect_shipping_business_quantity'],
            "collect_shipping_business_netprice" => $param ['collect_shipping_business_netprice'],

            "additional_pages_scanning_free_quantity" => $param ['additional_pages_scanning_free_quantity'],
            "additional_pages_scanning_free_netprice" => $param ['additional_pages_scanning_free_netprice'],
            "additional_pages_scanning_private_quantity" => $param ['additional_pages_scanning_private_quantity'],
            "additional_pages_scanning_private_netprice" => $param ['additional_pages_scanning_private_netprice'],
            "additional_pages_scanning_business_quantity" => $param ['additional_pages_scanning_business_quantity'],
            "additional_pages_scanning_business_netprice" => $param ['additional_pages_scanning_business_netprice'],
            "update_flag" => 0,
        ));
    }

    /**
     * Update activity invoice.
     *
     * @param unknown $param
     */
    private function updateActivityAmountByLocation($param)
    {
        ci()->invoice_summary_by_location_m->update_by_many(array(
            'customer_id' => $param ['customer_id'],
            'location_id' => $param ['location_id'],
            'invoice_month' => $param ['invoice_month']
        ), array(
            // Amount
            "incomming_items_free_account" => $param ['incomming_items_free_account'],
            "envelope_scan_free_account" => $param ['envelope_scan_free_account'],
            "item_scan_free_account" => $param ['item_scan_free_account'],
            "direct_shipping_free_account" => $param ['direct_shipping_free_account'],
            "collect_shipping_free_account" => $param ['collect_shipping_free_account'],

            "incomming_items_private_account" => $param ['incomming_items_private_account'],
            "envelope_scan_private_account" => $param ['envelope_scan_private_account'],
            "item_scan_private_account" => $param ['item_scan_private_account'],
            "direct_shipping_private_account" => $param ['direct_shipping_private_account'],
            "collect_shipping_private_account" => $param ['collect_shipping_private_account'],

            "incomming_items_business_account" => $param ['incomming_items_business_account'],
            "envelope_scan_business_account" => $param ['envelope_scan_business_account'],
            "item_scan_business_account" => $param ['item_scan_business_account'],
            "direct_shipping_business_account" => $param ['direct_shipping_business_account'],
            "collect_shipping_business_account" => $param ['collect_shipping_business_account'],

            "additional_pages_scanning_free_amount" => $param ['additional_pages_scanning_free_amount'],
            "additional_pages_scanning_private_amount" => $param ['additional_pages_scanning_private_amount'],
            "additional_pages_scanning_business_amount" => $param ['additional_pages_scanning_business_amount'],
            
            "custom_declaration_outgoing_quantity_01" => 1,
            "custom_declaration_outgoing_quantity_02" => 1,
            "custom_declaration_outgoing_price_01" => $param['custom_declaration_outgoing_price_01'],
            "custom_declaration_outgoing_price_02" => $param['custom_declaration_outgoing_price_02'],
            
            "vat_case" => $param['vat_case'],
            "vat" => $param['vat'],
        ));
    }

    /**
     * Update quantity and price for activity.
     *
     * @param unknown $param
     */
    private function updateActivityQuantityAndPriceByLocation($param)
    {
        ci()->invoice_summary_by_location_m->update_by_many(array(
            'customer_id' => $param ['customer_id'],
            'location_id' => $param ['location_id'],
            'invoice_month' => $param ['invoice_month']
        ), array(
            // Quanity & Price
            "incomming_items_free_quantity" => $param ['incomming_items_free_quantity'],
            "incomming_items_free_netprice" => $param ['incomming_items_free_netprice'],
            "incomming_items_private_quantity" => $param ['incomming_items_private_quantity'],
            "incomming_items_private_netprice" => $param ['incomming_items_private_netprice'],
            "incomming_items_business_quantity" => $param ['incomming_items_business_quantity'],
            "incomming_items_business_netprice" => $param ['incomming_items_business_netprice'],

            "envelope_scan_free_quantity" => $param ['envelope_scan_free_quantity'],
            "envelope_scan_free_netprice" => $param ['envelope_scan_free_netprice'],
            "envelope_scan_private_quantity" => $param ['envelope_scan_private_quantity'],
            "envelope_scan_private_netprice" => $param ['envelope_scan_private_netprice'],
            "envelope_scan_business_quantity" => $param ['envelope_scan_business_quantity'],
            "envelope_scan_business_netprice" => $param ['envelope_scan_business_netprice'],

            "item_scan_free_quantity" => $param ['item_scan_free_quantity'],
            "item_scan_free_netprice" => $param ['item_scan_free_netprice'],
            "item_scan_private_quantity" => $param ['item_scan_private_quantity'],
            "item_scan_private_netprice" => $param ['item_scan_private_netprice'],
            "item_scan_business_quantity" => $param ['item_scan_business_quantity'],
            "item_scan_business_netprice" => $param ['item_scan_business_netprice'],

            "direct_shipping_free_quantity" => $param ['direct_shipping_free_quantity'],
            "direct_shipping_free_netprice" => $param ['direct_shipping_free_netprice'],
            "direct_shipping_private_quantity" => $param ['direct_shipping_private_quantity'],
            "direct_shipping_private_netprice" => $param ['direct_shipping_private_netprice'],
            "direct_shipping_business_quantity" => $param ['direct_shipping_business_quantity'],
            "direct_shipping_business_netprice" => $param ['direct_shipping_business_netprice'],

            "collect_shipping_free_quantity" => $param ['collect_shipping_free_quantity'],
            "collect_shipping_free_netprice" => $param ['collect_shipping_free_netprice'],
            "collect_shipping_private_quantity" => $param ['collect_shipping_private_quantity'],
            "collect_shipping_private_netprice" => $param ['collect_shipping_private_quantity'],
            "collect_shipping_business_quantity" => $param ['collect_shipping_business_quantity'],
            "collect_shipping_business_netprice" => $param ['collect_shipping_business_netprice'],

            "additional_pages_scanning_free_quantity" => $param ['additional_pages_scanning_free_quantity'],
            "additional_pages_scanning_free_netprice" => $param ['additional_pages_scanning_free_netprice'],
            "additional_pages_scanning_private_quantity" => $param ['additional_pages_scanning_private_quantity'],
            "additional_pages_scanning_private_netprice" => $param ['additional_pages_scanning_private_netprice'],
            "additional_pages_scanning_business_quantity" => $param ['additional_pages_scanning_business_quantity'],
            "additional_pages_scanning_business_netprice" => $param ['additional_pages_scanning_business_netprice']
        ));
    }
    
    private function initInvoiceByLocation($location_id, $customer_id, $invoice_month){
        $invoice_check = ci()->invoice_summary_by_location_m->get_by_many(array(
            'customer_id' => $customer_id,
            'location_id' => $location_id,
            'invoice_month' => $invoice_month
        ));
        
        if(!$invoice_check){
            ci()->invoice_summary_by_location_m->insert(array(
                'customer_id' => $customer_id,
                'location_id' => $location_id,
                'invoice_month' => $invoice_month,
                "invoice_type" => 1
            ));
        }
        
        return;
    }
}