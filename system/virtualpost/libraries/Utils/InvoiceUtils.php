<?php

defined('BASEPATH') or exit('No direct script access allowed');

class InvoiceUtils extends Core_BaseClass {

    public function __construct() {
        parent::__construct();
        ci()->load->model(array(
            'customers/customer_m',
            'invoices/invoice_detail_m',
            'invoices/invoice_summary_m',
            'payment/payone_tran_hist_m',
            'payment/external_tran_hist_m',
            'phones/phone_invoice_by_location_m',
        ));
    }

    public static function getCurrentActivitiesInvoice($customer_id, $isPrimaryCustomer=false) {

        $target_month = APUtils::getTargetMonthInvoice();
        $target_year = APUtils::getTargetYearInvoice();
        $list_customer_id = array();
        if ($isPrimaryCustomer) {
            $list_customer_id = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($customer_id);
            $list_customer_id[] = $customer_id;
        } else {
            $list_customer_id[] = $customer_id;
        }

        $next_invoices = ci()->invoice_summary_m->get_many_by_many(array(
            'invoice_month' => $target_year . $target_month,
            'customer_id IN (' . implode(',', $list_customer_id) . ')' => null
        ));

        $next_invoices_display = new stdClass();
        $next_invoices_display->postboxes_amount = 0;
        $next_invoices_display->envelope_scanning_amount = 0;
        $next_invoices_display->scanning_amount = 0;
        $next_invoices_display->additional_items_amount = 0;
        $next_invoices_display->shipping_handing_amount = 0;
        $next_invoices_display->storing_amount = 0;
        $next_invoices_display->additional_pages_scanning_amount = 0;
        
        // Enterprise only
        $next_invoices_display->api_access_amount = 0;
        $next_invoices_display->own_location_amount = 0;
        $next_invoices_display->touch_panel_own_location_amount = 0;
        $next_invoices_display->own_mobile_app_amount = 0;
        $next_invoices_display->clevver_subdomain_amount = 0;
        $next_invoices_display->own_subdomain_amount = 0;

        if (!empty($next_invoices)) {
            foreach ($next_invoices as $next_invoice) {
                // Postbox amount
                $next_invoices_display->postboxes_amount += empty($next_invoice->free_postboxes_amount) ? 0 : $next_invoice->free_postboxes_amount;
                $next_invoices_display->postboxes_amount += empty($next_invoice->private_postboxes_amount) ? 0 : $next_invoice->private_postboxes_amount;
                $next_invoices_display->postboxes_amount += empty($next_invoice->business_postboxes_amount) ? 0 : $next_invoice->business_postboxes_amount;
                $next_invoices_display->postboxes_amount += empty($next_invoice->additional_private_postbox_amount) ? 0 : $next_invoice->additional_private_postbox_amount;
                $next_invoices_display->postboxes_amount += empty($next_invoice->additional_business_postbox_amount) ? 0 : $next_invoice->additional_business_postbox_amount;

                // Envelope scanning amount
                $next_invoices_display->envelope_scanning_amount += empty($next_invoice->envelope_scan_free_account) ? 0 : $next_invoice->envelope_scan_free_account;
                $next_invoices_display->envelope_scanning_amount += empty($next_invoice->envelope_scan_private_account) ? 0 : $next_invoice->envelope_scan_private_account;
                $next_invoices_display->envelope_scanning_amount += empty($next_invoice->envelope_scan_business_account) ? 0 : $next_invoice->envelope_scan_business_account;

                // Item scanning amount
                $next_invoices_display->scanning_amount += empty($next_invoice->item_scan_free_account) ? 0 : $next_invoice->item_scan_free_account;
                $next_invoices_display->scanning_amount += empty($next_invoice->item_scan_private_account) ? 0 : $next_invoice->item_scan_private_account;
                $next_invoices_display->scanning_amount += empty($next_invoice->item_scan_business_account) ? 0 : $next_invoice->item_scan_business_account;

                // Additional item amount
                $next_invoices_display->additional_items_amount += empty($next_invoice->additional_pages_scanning) ? 0 : $next_invoice->additional_pages_scanning;
                $next_invoices_display->additional_items_amount += empty($next_invoice->incomming_items_free_account) ? 0 : $next_invoice->incomming_items_free_account;
                $next_invoices_display->additional_items_amount += empty($next_invoice->incomming_items_private_account) ? 0 : $next_invoice->incomming_items_private_account;
                $next_invoices_display->additional_items_amount += empty($next_invoice->incomming_items_business_account) ? 0 : $next_invoice->incomming_items_business_account;

                // Additional scanning amount
                $next_invoices_display->additional_pages_scanning_amount += empty($next_invoice->additional_pages_scanning_free_amount) ? 0 : $next_invoice->additional_pages_scanning_free_amount;
                $next_invoices_display->additional_pages_scanning_amount += empty($next_invoice->additional_pages_scanning_private_amount) ? 0 : $next_invoice->additional_pages_scanning_private_amount;
                $next_invoices_display->additional_pages_scanning_amount += empty($next_invoice->additional_pages_scanning_business_amount) ? 0 : $next_invoice->additional_pages_scanning_business_amount;

                // Shipping handding amount
                $next_invoices_display->shipping_handing_amount += empty($next_invoice->direct_shipping_free_account) ? 0 : $next_invoice->direct_shipping_free_account;
                $next_invoices_display->shipping_handing_amount += empty($next_invoice->direct_shipping_private_account) ? 0 : $next_invoice->direct_shipping_private_account;
                $next_invoices_display->shipping_handing_amount += empty($next_invoice->direct_shipping_business_account) ? 0 : $next_invoice->direct_shipping_business_account;
                $next_invoices_display->shipping_handing_amount += empty($next_invoice->collect_shipping_free_account) ? 0 : $next_invoice->collect_shipping_free_account;
                $next_invoices_display->shipping_handing_amount += empty($next_invoice->collect_shipping_private_account) ? 0 : $next_invoice->collect_shipping_private_account;
                $next_invoices_display->shipping_handing_amount += empty($next_invoice->collect_shipping_business_account) ? 0 : $next_invoice->collect_shipping_business_account;

                // Shipping customs fee
                $next_invoices_display->shipping_handing_amount += $next_invoice->custom_declaration_outgoing_quantity_01 * $next_invoice->custom_declaration_outgoing_price_01;
                $next_invoices_display->shipping_handing_amount += $next_invoice->custom_declaration_outgoing_quantity_02 * $next_invoice->custom_declaration_outgoing_price_02;

                // Storing amount
                $next_invoices_display->storing_amount += empty($next_invoice->storing_letters_free_account) ? 0 : $next_invoice->storing_letters_free_account;
                $next_invoices_display->storing_amount += empty($next_invoice->storing_letters_private_account) ? 0 : $next_invoice->storing_letters_private_account;
                $next_invoices_display->storing_amount += empty($next_invoice->storing_letters_business_account) ? 0 : $next_invoice->storing_letters_business_account;
                $next_invoices_display->storing_amount += empty($next_invoice->storing_packages_free_account) ? 0 : $next_invoice->storing_packages_free_account;
                $next_invoices_display->storing_amount += empty($next_invoice->storing_packages_private_account) ? 0 : $next_invoice->storing_packages_private_account;
                $next_invoices_display->storing_amount += empty($next_invoice->storing_packages_business_account) ? 0 : $next_invoice->storing_packages_business_account;
                
                // Enterprise
                $next_invoices_display->api_access_amount += empty($next_invoice->api_access_amount) ? 0 : $next_invoice->api_access_amount;
                $next_invoices_display->own_location_amount += empty($next_invoice->own_location_amount) ? 0 : $next_invoice->own_location_amount;
                $next_invoices_display->touch_panel_own_location_amount += empty($next_invoice->touch_panel_own_location_amount) ? 0 : $next_invoice->touch_panel_own_location_amount;
                $next_invoices_display->own_mobile_app_amount += empty($next_invoice->own_mobile_app_amount) ? 0 : $next_invoice->own_mobile_app_amount;
                $next_invoices_display->clevver_subdomain_amount += empty($next_invoice->clevver_subdomain_amount) ? 0 : $next_invoice->clevver_subdomain_amount;
                $next_invoices_display->own_subdomain_amount += empty($next_invoice->own_subdomain_amount) ? 0 : $next_invoice->own_subdomain_amount;
            }
        }

        return $next_invoices_display;
    }

    public static function update_other_local_total_invoice($invoice_month, $total_invoice) {
        ci()->load->model('report/report_by_location_m');

        $total_other_local = ci()->report_by_location_m->sum_by_many(array(
            "invoice_month" => $invoice_month,
            "location_id <> 1" => null
                ), 'other_local_invoice');

        ci()->report_by_location_m->update_by_many(array(
            "invoice_month" => $invoice_month,
            "location_id" => "1"
                ), array(
            "other_local_invoice" => $total_invoice - $total_other_local
        ));
    }

    /**
     * Activities in Current Period
     */
    public static function load_current_activities($customer, $input_paging, $isPrimaryCustomer) {
        $target_month = APUtils::getTargetMonthInvoice();
        $target_year = APUtils::getTargetYearInvoice();
        $customer_id = $customer->customer_id;
        // Gets last day of month
        $target_first = APUtils::getFirstDayOfMonth($target_year . $target_month);
        $target_last = APUtils::getLastDayOfMonth($target_first);
        $list_customer_id = array();
        if ($isPrimaryCustomer) {
            $list_customer_id = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($customer_id);
            $list_customer_id[] = $customer_id;
        } else {
            $list_customer_id[] = $customer_id;
        }
        
        
        // Get input condition
        $array_condition = array(
            'invoice_detail.customer_id IN (' . implode(',', $list_customer_id) . ')' => null,
            'activity_date >=' => $target_first,
            'activity_date <=' => $target_last
        );

        $currency = ci()->customer_m->get_standard_setting_currency($customer_id);
        $decimal_separator = ci()->customer_m->get_standard_setting_decimal_separator($customer_id);
        $currency_sign = $currency->currency_sign;
        $currency_rate = $currency->currency_rate;

        // Call search method
        $query_result = ci()->invoice_detail_m->get_invoice_detail_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

        // Process output data
        $total = $query_result['total'];
        $datas = $query_result['data'];

        // Get output response
        $response = self::get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
        $datas_mobile = array();
        $i = 0;
        foreach ($datas as $row) {
            $vat = $row->vat;
            $response->rows[$i]['id'] = $row->id;
            if ($row->item_amount <= 0) {
                $net_price = "included";
            } else {
                $net_price = $currency_sign . ' ' . (APUtils::convert_currency($row->item_amount, $currency_rate, 2, $decimal_separator));
            }

            // #499 added: get vat + gross total
            $gross = (1 + $vat) * $row->item_amount;
            if ($customer->status == 1) {
                $net_price = 0;
                $gross = 0;
            }

            $activity = $row->item_number . '    ' . $row->activity;
            $activity_date = APUtils::displayDate($row->activity_date);
            $vat = ($vat * 100) . '%';

            $gross_total = $currency_sign . ' ' . APUtils::convert_currency($gross, $currency_rate, 2, $decimal_separator);

            $response->rows[$i]['cell'] = array(
                $row->id,
                $activity,
                $row->customer_code,
                $row->location_name,
                $activity_date,
                $net_price,
                $vat,
                $gross_total
            );

            $row->activity = $activity;
            $row->activity_date = $activity_date;
            $row->net_price = $net_price;
            $row->vat = $vat;
            $row->gross_total = $gross_total;

            $datas_mobile[$i] = $row;

            $i++;
        }

        return array(
            "mobile_current_invoices" => $datas_mobile,
            "web_current_invoices" => $response
        );
    }

    /**
     * Gets current activities of phone number
     * @param type $customer_id
     */
    public static function getCurrentActivityPhone($customer_id, $isPrimaryCustomer) {
        ci()->load->model('phones/phone_invoice_by_location_m');

        $target_month = APUtils::getTargetMonthInvoice();
        $target_year = APUtils::getTargetYearInvoice();
        $list_customer_id = array();
        if ($isPrimaryCustomer) {
            $list_customer_id = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($customer_id);
            $list_customer_id[] = $customer_id;
        } else {
            $list_customer_id[] = $customer_id;
        }

        $next_invoices = ci()->phone_invoice_by_location_m->get_many_by_many(array(
            'invoice_month' => $target_year . $target_month,
            'customer_id IN (' . implode(',', $list_customer_id) . ')' => null
        ));

        $next_invoices_display = new stdClass();
        $next_invoices_display->incomming_amount = 0;
        $next_invoices_display->outcomming_amount = 0;
        $next_invoices_display->phone_subscription_amount = 0;
        $next_invoices_display->phone_recurring_amount = 0;
        $next_invoices_display->setup_fee_amount = 0;
        foreach ($next_invoices as $invoice) {
            $next_invoices_display->incomming_amount += $invoice->incomming_amount;
            $next_invoices_display->outcomming_amount += $invoice->outcomming_amount;
            $next_invoices_display->phone_subscription_amount += $invoice->phone_subscription_amount;
            $next_invoices_display->phone_recurring_amount += $invoice->phone_recurring_amount;
            $next_invoices_display->setup_fee_amount += $invoice->setup_fee_amount;
        }

        return $next_invoices_display;
    }

    /**
     * Load old invoice
     */
    public static function load_old_invoice($customer, $input_paging) {
        ci()->load->model('invoices/invoice_summary_m');
        $target_month = APUtils::getTargetMonthInvoice();
        $target_year = APUtils::getTargetYearInvoice();

        $customer_id = $customer->customer_id;

        // #479: load all old invoice for deleted customer
        if ($customer->status == APConstants::ON_FLAG) {
            // Get input condition
            $array_condition = array(
                'customer_id' => $customer_id,
                "(((LEFT(invoice_month,6) <= '" . APUtils::getCurrentYear() . APUtils::getCurrentMonth() . "' AND (invoice_type = 0 OR invoice_type IS NULL OR invoice_type =1))) OR (invoice_type=2))" => null
            );
        } else {
            // Get input condition
            $array_condition = array(
                'customer_id' => $customer_id,
                "(((LEFT(invoice_month,6) < '" . $target_year . $target_month . "' AND (invoice_type = 0 OR invoice_type IS NULL OR invoice_type =1))) OR (invoice_type=2))" => null
            );
        }

        // Get standard settings of currency & decimal separator
        $currency = ci()->customer_m->get_standard_setting_currency($customer_id);
        $decimal_separator = ci()->customer_m->get_standard_setting_decimal_separator($customer_id);
        $currency_sign = $currency->currency_sign;
        $currency_rate = $currency->currency_rate;

        $query_result = ci()->invoice_summary_m->get_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

        // Process output data
        $total = $query_result['total'];
        $datas = $query_result['data'];

        // Get output response
        $response = self::get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);

        // Get all payone status
        $array_condition = array(
            'customer_id' => $customer_id,
            "(txaction = 'paid' OR txaction = 'refund' OR txaction = 'debit')" => null
        );
        $payone_tran_hist_data = ci()->payone_tran_hist_m->get_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

        $array_condition = array(
            'customer_id' => $customer_id
        );
        $external_tran_hist_data = ci()->external_tran_hist_m->get_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

        $payone_tran_hist_list = $payone_tran_hist_data ['data'];
        $external_tran_hist_list = $external_tran_hist_data ['data'];

        $i = 0;
        $total_gross_price = 0;
        $total_charge_price = 0;
        $invoice_charges = array();
        $net_sign = "";
        $gross_sign = "";

        $datas_mobile = array();

        // Delete all invoice where total invoice = 0;
        $invoices_data = array();
        foreach ($datas as $row) {
            if ($row->total_invoice != 0) {
                array_push($invoices_data, $row);
            }
        }

        // search last invoice.
        $targetYM = '';
        foreach ($invoices_data as $row) {
            if ($row->invoice_type != '2') {
                $targetYM = $row->invoice_month;
            }
        }
        
        $is_primary_customer = false;
        if($customer->account_type == APConstants::ENTERPRISE_TYPE && empty($customer->parent_customer_id)){
            $is_primary_customer = true;
            $list_users = ci()->customer_m->get_many_by_many(array(
                'parent_customer_id' => $customer_id
            ));

            $list_customer_id[] = $customer->customer_id;
            foreach($list_users as $user){
                $list_customer_id[] = $user->customer_id;
            }
        }

        // fill invoice to list.
        $invoice_code_list = array();
        foreach ($invoices_data as $row) {
            // if invoice is calculated, then continue to calculate.
            if (in_array($row->invoice_code, $invoice_code_list)) {
                continue;
            }

            if($is_primary_customer && $row->invoice_type != "2"){
                $gross_price = ci()->invoice_summary_m->sum_by_many(array(
                    "left(invoice_month,6) = ".$row->invoice_month => null,
                    "customer_id IN (".implode(",", $list_customer_id).")" => null,
                    "invoice_type" => $row->invoice_type
                ), 'total_invoice * (1 + vat)');
                $net_price = ci()->invoice_summary_m->sum_by_many(array(
                    "left(invoice_month,6) = ".$row->invoice_month => null,
                    "customer_id IN (".implode(",", $list_customer_id).")" => null,
                    "invoice_type" => $row->invoice_type
                ), 'total_invoice');
            }else{
                $gross_price = $row->total_invoice * (1 + $row->vat);
                $net_price = $row->total_invoice;
            }
            if (abs($net_price) < 0.01) {
                continue;
            }

            $invoice_date = ($row->invoice_type == '2') ? APUtils::displayDate($row->invoice_month) : APUtils::displayDate(APUtils::getLastDayOfMonth($row->invoice_month));
            if ($customer->status == 1 && $row->invoice_month == $targetYM) {
                $invoice_date = APUtils::convert_timestamp_to_date($customer->deleted_date, "d.m.Y H:s");
            }

            $transaction = ($gross_price >= 0) ? "Invoice" : "Credit Note";

            $net_total = $currency_sign . ' ' . $net_sign . APUtils::convert_currency($net_price, $currency_rate, 2, $decimal_separator);
            $brutto_total = $currency_sign . ' ' . $gross_sign . APUtils::convert_currency($gross_price, $currency_rate, 2, $decimal_separator);

            $row_mobile = new stdClass;
            $invoice_charges [] = array(
                $i,
                $transaction,
                $invoice_date,
                $row->invoice_code, // invoice_code.
                $net_total,
                $brutto_total,
                '',
                $row->invoice_code
            );

            $row_mobile->transaction = $transaction;
            $row_mobile->invoice_date = $invoice_date;
            $row_mobile->invoice_code = $row->invoice_code;
            $row_mobile->net_total = $net_total;
            $row_mobile->brutto_total = $brutto_total;
            $row_mobile->status = '';

            if ($transaction == 'Invoice') {

                $row_mobile->pdf_path = APContext::getFullBasePath() . 'invoices/export/?type=invoice';
            } else if ($transaction == 'Credit Note') {

                $row_mobile->pdf_path = APContext::getFullBasePath() . 'invoices/export/?type=credit';
            } else {
                $row_mobile->pdf_path = '';
            }

            $datas_mobile[$i] = $row_mobile;
            $i++;
        }

        // Gets old invoices of phone number
        if ($customer->status == APConstants::ON_FLAG) {
            // Get input condition
            $array_condition2 = array(
                'customer_id' => $customer_id,
                "LEFT(invoice_month,6) <= '" . APUtils::getCurrentYear() . APUtils::getCurrentMonth() . "'" => null
            );
        } else {
            // Get input condition
            $array_condition2 = array(
                'customer_id' => $customer_id,
                "LEFT(invoice_month,6) < '" . APUtils::getCurrentYear() . APUtils::getCurrentMonth() . "'" => null
            );
        }
        $invoices_phone = ci()->phone_invoice_by_location_m->get_invoice_paging($array_condition2, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

        foreach ($invoices_phone as $row) {
            if ($row->total_invoice == 0) {
                continue;
            }

            // if invoice is calculated, then continue to calculate.
            if (in_array($row->invoice_code, $invoice_code_list)) {
                continue;
            }

            $gross_price = $row->total_invoice * (1 + $row->vat);
            $net_price = $row->total_invoice;
            $invoice_date = APUtils::displayDate(APUtils::getLastDayOfMonth($row->invoice_month));
            if ($customer->status == 1 && $row->invoice_month == $targetYM) {
                $invoice_date = APUtils::convert_timestamp_to_date($customer->deleted_date);
            }
            // insert invoice list.
            $invoice_charges [] = array(
                $i,
                $gross_price >= 0 ? "Invoice" : "Credit Note",
                $invoice_date,
                $row->invoice_code, // invoice_code.
                $currency_sign . ' ' . $net_sign . APUtils::convert_currency($net_price, $currency_rate, 2, $decimal_separator),
                $currency_sign . ' ' . $gross_sign . APUtils::convert_currency($gross_price, $currency_rate, 2, $decimal_separator),
                '',
                $row->invoice_code
            );

            $i++;
        }

        // For each external payment transaction
        foreach ($external_tran_hist_list as $item) {
            if ($item->tran_amount <= 0) {
                $sign = "- ";
            } else {
                $sign = "+ ";
            }

            $payment_type = "Payment";
            $payment_status = "";
            if (!empty($item->status)) {
                $payment_status = $item->status;
            }

            $row_mobile = new stdClass;

            $invoice_date = APUtils::displayDate($item->tran_date)." 00:00";

            $invoice_code = $item->tran_id;
            $brutto_total = $currency_sign . ' ' . $sign . APUtils::convert_currency(abs($item->tran_amount), $currency_rate, 2, $decimal_separator);


            $invoice_charges[] = array(
                $i,
                $payment_type,
                $invoice_date,
                $invoice_code,
                '',
                // fixbug #446
                //APUtils::number_format($item->tran_amount, 2),
                $brutto_total,
                $payment_status,
                $i
            );

            $row_mobile->transaction = $payment_type;
            $row_mobile->invoice_date = $invoice_date;
            $row_mobile->invoice_code = $invoice_code;
            $row_mobile->net_total = '';
            $row_mobile->brutto_total = $brutto_total;
            $row_mobile->status = $payment_status;
            $row_mobile->pdf_path = '';
            $datas_mobile[$i] = $row_mobile;

            $total_charge_price += $item->tran_amount;
            $i++;
        }

        // For each payone payment transaction
        foreach ($payone_tran_hist_list as $item) {
            $status = self::getPayoneStatus($item->txaction);
            $tran_amount = APUtils::convert_currency($item->amount, $currency_rate, 2, $decimal_separator);
            if (strtolower($status) == 'refund') {
                // fix only for refund from payone.
                $sign = "+ ";
                $tran_amount = APUtils::convert_currency(abs($item->amount), $currency_rate, 2, $decimal_separator);
            } else if ($item->amount > 0) {
                $sign = "- ";
            } else {
                $sign = "+ ";
            }

            $row_mobile = new stdClass;
            $invoice_date = APUtils::convert_timestamp_to_date($item->create_time, 'd.m.Y H:i');

            $brutto_total = $currency_sign . ' ' . $sign . $tran_amount;
            $invoice_charges[] = array(
                $i,
                'Charge',
                $invoice_date,
                $item->txid,
                '',
                $brutto_total,
                $status,
                $i
            );

            $row_mobile->transaction = 'Charge';
            $row_mobile->invoice_date = $invoice_date;
            $row_mobile->invoice_code = $item->txid;
            $row_mobile->net_total = '';
            $row_mobile->brutto_total = $brutto_total;
            $row_mobile->status = $status;
            $row_mobile->pdf_path = '';
            $datas_mobile[$i] = $row_mobile;

            $total_charge_price += $item->amount;
            $i++;
        }



        //$open_balance = $total_gross_price - $total_charge_price;

        /**
         * Sort array by DESC Enter description here . ..
         *
         * @param unknown_type $cmp_score1
         * @param unknown_type $cmp_score2
         */
        function cmp_invoice_item($obj1, $obj2) {
            $a = APUtils::convertDateFormat02($obj1[2]);
            $b = APUtils::convertDateFormat02($obj2[2]);
            return strcmp(strtolower($b), strtolower($a));
        }

        usort($invoice_charges, "cmp_invoice_item");

        for ($i = 0; $i < count($invoice_charges); $i++) {
            $response->rows[$i]['cell'] = $invoice_charges[$i];
        }

        function cmp_invoice_item_mobile($obj1, $obj2) {
            $a = APUtils::convertDateFormat02($obj1->invoice_date);
            $b = APUtils::convertDateFormat02($obj2->invoice_date);
            return strcmp(strtolower($b), strtolower($a));
        }

        usort($datas_mobile, "cmp_invoice_item_mobile");

        $response->records = count($invoice_charges);

        return array(
            "mobile_old_invoices" => $datas_mobile,
            "web_old_invoices" => $response
        );
    }

    public static function getPayoneStatus($txaction) {
        if (strtoupper($txaction) == 'PAID') {
            return 'OK';
        } else if (strtoupper($txaction) == 'REFUND' || strtoupper($txaction) == 'DEBIT') {
            return 'Refund';
        } else if (strtoupper($txaction) == 'cancelation') {
            return 'canceled';
        } else if (!empty($txaction)) {
            return strtoupper($txaction);
        } else {
            return 'Pending';
        }
    }

    /**
     * Load current activities of phone number.
     * @param type $customer
     * @param type $input_paging
     */
    public static function load_current_phone_invoice($customer, $input_paging, $isPrimaryCustomer) {
        ci()->load->model('phones/phone_invoice_detail_m');
        $customer_id = $customer->customer_id;

        $currency = ci()->customer_m->get_standard_setting_currency($customer_id);
        $decimal_separator = ci()->customer_m->get_standard_setting_decimal_separator($customer_id);
        $currency_sign = $currency->currency_sign;
        $currency_rate = $currency->currency_rate;

        $target_month = APUtils::getTargetMonthInvoice();
        $target_year = APUtils::getTargetYearInvoice();
        $list_customer_id = array();
        if ($isPrimaryCustomer) {
            $list_customer_id = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($customer_id);
            $list_customer_id[] = $customer_id;
        } else {
            $list_customer_id[] = $customer_id;
        }

        // Gets last day of month
        $target_first = APUtils::getFirstDayOfMonth($target_year . $target_month);
        $target_last = APUtils::getLastDayOfMonth($target_first);

        // Get input condition
        $array_condition = array(
            'phone_invoice_detail.customer_id IN (' . implode(',', $list_customer_id) . ')' => null,
            'activity_date >=' => $target_first,
            'activity_date <=' => $target_last
        );

        // Call search method
        $query_result = ci()->phone_invoice_detail_m->get_invoice_detail_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

        // Process output data
        $total = $query_result['total'];
        $datas = $query_result['data'];

        // Get output response
        $response = self::get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
        $datas_mobile = array();
        $i = 0;
        foreach ($datas as $row) {
            $vat = $row->vat;
            $response->rows[$i]['id'] = $row->id;
            if ($row->item_amount <= 0) {
                $net_price = "included";
            } else {
                $net_price = $currency_sign . ' ' . (APUtils::convert_currency($row->item_amount, $currency_rate, 2, $decimal_separator));
            }

            // #499 added: get vat + gross total
            $gross = (1 + $vat) * $row->item_amount;
            if ($customer->status == 1) {
                $net_price = 0;
                $gross = 0;
            }

            $activity = $row->item_number . '    ' . $row->activity;
            $activity_date = APUtils::displayDate($row->activity_date);
            $vat = ($vat * 100) . '%';

            $gross_total = $currency_sign . ' ' . APUtils::convert_currency($gross, $currency_rate, 2, $decimal_separator);

            $response->rows[$i]['cell'] = array(
                $row->id,
                $activity,
                $row->customer_code,
                $row->location_name,
                $activity_date,
                $net_price,
                $vat,
                $gross_total
            );

            $row->activity = $activity;
            $row->activity_date = $activity_date;
            $row->net_price = $net_price;
            $row->vat = $vat;
            $row->gross_total = $gross_total;

            $datas_mobile[$i] = $row;

            $i++;
        }

        return array(
            "mobile_current_invoices" => $datas_mobile,
            "web_current_invoices" => $response
        );
    }

    /**
     * get open balance of phone number
     * @param type $customer_id
     */
    public static function getOpenBalanceOfPhone($customer_id) {
        ci()->load->model('phones/phone_invoice_by_location_m');

        $open_balance_due = ci()->phone_invoice_by_location_m->sum_by_many(array(
            "customer_id IN (" . $customer_id . ")" => null,
            "LEFT(invoice_month, 6) < " . APUtils::getCurrentYearMonth() => null
                ), 'total_invoice * (1 + vat)');


        return $open_balance_due;
    }

    /**
     * Gets open balance this month of phone number.
     * @param type $customer_id
     * @return type
     */
    public static function getOpenBalanceThisMonthOfPhone($customer_id) {
        ci()->load->model('phones/phone_invoice_by_location_m');

        $open_balance_this_month = ci()->phone_invoice_by_location_m->sum_by_many(array(
            "customer_id IN (" . $customer_id . ")" => null,
            "LEFT(invoice_month, 6) = " . APUtils::getCurrentYearMonth() => null
                ), 'total_invoice * (1 + vat)');

        return $open_balance_this_month;
    }

}
