<?php

defined('BASEPATH') or exit('No direct script access allowed');

class admin extends Admin_Controller {

    /**
     * Validation for basic profile data.
     * The rest of the validation is built by streams.
     *
     * @var array
     */
    private $validation_add_partner_receipt_rules = array(
        array(
            'field' => 'partner_id',
            'label' => 'Partner',
            'rules' => 'callback_is_required'
        ),
        array(
            'field' => 'location_id',
            'label' => 'Location',
            'rules' => 'callback_is_required'
        ),
        array(
            'field' => 'date_of_receipt',
            'label' => 'Date Of Receipt',
            'rules' => 'callback_is_required'
        ),
        array(
            'field' => 'net_amount',
            'label' => 'Net Amount', 
            'rules' => 'callback_is_required|callback_is_number' //#1296 add receipt scan/upload to receipts 
        ),
        array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => 'callback_is_required'
        ),
        array(
            'field' => 'partner_file_id',
            'label' => 'Upload Receipt',
            'rules' => 'callback_is_required'
        )
    );
    private $location_report_rules = array(
        array(
            'field' => 'costOfLocationAdvertising',
            'label' => 'Cost of location advertising',
            'rules' => 'required|callback_is_number|callback_greater_than_equal_to[0]'
        ),
        array(
            'field' => 'hardwareAmortization',
            'label' => 'Hardware amortization',
            'rules' => 'required|callback_is_number|callback_greater_than_equal_to[0]'
        ),
        array(
            'field' => 'locationExternalReceipts',
            'label' => 'Location external receipts',
            'rules' => 'required|callback_is_number|callback_greater_than_equal_to[0]'
        ),
        array(
            'field' => 'currentOpenBalance',
            'label' => 'Current Open balance',
            'rules' => 'required|callback_is_number'
        ),
        array(
            'field' => 'totalInvoiceableSoFar',
            'label' => 'Total invoiceable so far',
            'rules' => 'callback_is_number'
        ),
        array(
            'field' => 'totalInvoicedSoFar',
            'label' => 'Total invoiced so far',
            'rules' => 'callback_is_number'
        ),
        array(
            'field' => 'invoicesWrittenThisMonth',
            'label' => 'Invoices written this month',
            'rules' => 'callback_is_number'
        ),
        array(
            'field' => 'totalPaymentsMadeTillEndOfThisMonth',
            'label' => 'Total payments made till end of this month',
            'rules' => 'callback_is_number'
        )
    );

    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     *
     * @todo Document properly please.
     */
    public function __construct() {
        parent::__construct();
        // Load the required classes
        $this->load->model(array(
            "report/report_by_location_m",
            'report/report_m',
            "report_by_total_m",
            'customers/customer_m',
            'addresses/location_m',
            'report/payone_transaction_hist_m',
            "invoices/invoice_summary_by_location_m",
            "invoices/invoice_summary_m",
            'report/location_report_m'
        ));

        $this->load->library(array(
            'price/price_api',
            'mailbox/mailbox_api',
            "customers/customers_api",
            "scans/scans_api",
            "invoices/invoices_api",
            "report/report_api",
            "payment/payment_api",
            "invoices/export",
        ));

        $this->load->library('form_validation');

        ci()->load->library('S3');

        // Load lang 
        $this->lang->load('message');
    }

    /**
     * Default (For new items)
     */
    public function index() {
        
    }

    // Call back greater than equal to 
    public function greater_than_equal_to($str, $min) {
        if ($str < $min) {
            $this->form_validation->set_message('greater_than_equal_to', 'The %s field must contain a number greater than or equal to %s.');
            return FALSE;
        }
        return TRUE;
    }

    // Call back check number 
    public function is_number($str) {
        if ($str == '0') {
            return TRUE;
        } else if (!floatval($str)) {
            $message = admin_language('report_views_admin_managereceipt_NumberValidatedMessage');
            $this->form_validation->set_message('is_number', $message);
            return FALSE;
        }
        return TRUE;
    }
    
     // Call back require 
    public function is_required($str) {
        if (!isset($str)) {
            $message = admin_language('report_views_admin_managereceipt_RequiredValidatedMessage');
            $this->form_validation->set_message('is_required', $message);
            return FALSE;
        } else{
            return TRUE;
        }
        return TRUE;
    }

    /**
     * report overview
     */
    public function overview() {
        $this->lang->load('invoices/invoices');

        // Gets locations
        $locations = APUtils::loadListAccessLocation();
        $location_id = $this->input->get_post("location_id", "");

        // #1058 add multi dimension capability for admin
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();

        // Get overview information
        $overview = new stdClass();
        $overview->total_account = 0;
        $overview->total_inactive_account = 0;
        $overview->total_postboxes = 0;
        $overview->percent_business_postboxes = 0;
        $overview->avg_activity_open_time = 0;
        $overview->avg_revenue_account = 0;
        $overview->avg_location_account = 0;
        $overview->tickets_created = 0;
        $overview->avg_revenue_postbox_free = 0;
        $overview->avg_revenue_postbox_private = 0;
        $overview->avg_revenue_postbox_business = 0;
        $overview->avg_revenue_postbox = 0;

        // Get all total account
        $base_condition[0] = $location_id;
        $total_activated_account = customers_api::countAllCustomersActivatedWithChargeFee($base_condition);
        $total_account = customers_api::countAllCustomersWithChargeFee($base_condition);
        $overview->total_account = $total_account;
        $overview->total_inactive_account = $total_account - $total_activated_account;

        // Get postbox of  customer
        $base_condition_postbox["location_available_id"] = $location_id;
        $base_condition_postbox["customers.charge_fee_flag"] = APConstants::ON_FLAG;
        $overview->total_postboxes = mailbox_api::countAllPostboxesOfCustomerBy($base_condition_postbox);
        $base_condition_postbox["type"] = "3";
        $base_condition_postbox["deleted <>"] = "1";
        $total_business_postbox = mailbox_api::countAllPostboxesOfCustomerBy($base_condition_postbox);
        if ($overview->total_postboxes > 0) {
            // #1058 add multi dimension capability for admin
            $overview->percent_business_postboxes = APUtils::number_format(($total_business_postbox / $overview->total_postboxes) * 100, 0, $decimal_separator) . '%';
        }
        $this->template->set("overview", $overview);


        // 2. Calculate month fee
        $current_month = APUtils::getCurrentMonthInvoice();
        $intCurMonth = intval($current_month);
        $list_monthly_report = array();
        for ($i = $intCurMonth; $i >= $intCurMonth - 2; $i--) {
            $monthly_report = new stdClass();
            $monthly_report->month = lang('month_' . $i);
            $monthly_report->total_invoices = 0;
            $monthly_report->total_credit_notes = 0;
            $monthly_report->total_revenue = 0;
            $monthly_report->total_new_accounts = 0;
            $monthly_report->total_deleted_accounts = 0;
            $monthly_report->percent_account_churn = 0;
            $monthly_report->new_account_net_adds = 0;
            $monthly_report->new_postboxed = 0;
            $monthly_report->deleted_postboxes = 0;
            $monthly_report->percent_postbox_churn = 0;
            $monthly_report->new_postbox_net_adds = 0;

            $monthly_report->num_of_items_received = 0;
            $monthly_report->num_of_envelope_scans = 0;
            $monthly_report->num_of_item_scans = 0;
            $monthly_report->num_of_items_shippments = 0;

            $list_monthly_report[] = $monthly_report;
        }
        $this->template->set("list_monthly_report", $list_monthly_report);

        $this->template->set("locations", $locations);
        $this->template->set("location_id", $location_id);
        $this->template->build('admin/overview');
    }

    public function invoices() {
        // Gets locations
        $locations = APUtils::loadListAccessLocation();

        $year = $this->input->get_post("year", "");
        $month = $this->input->get_post("month", "");

        if (empty($year)) {
            $year = date("Y", now());
        }
        if (empty($month)) {
            $month = date("m", now());
        }

        $list_month = array();
        $list_year = array();
        for ($i = 1; $i <= 12; $i++) {
            $new_item = new stdClass();
            $new_item->id = $i < 10 ? '0' . $i : $i;
            $new_item->label = $i < 10 ? '0' . $i : $i;
            $list_month[] = $new_item;
        }
        for ($i = 0; $i < 10; $i++) {
            $new_item = new stdClass();
            $new_item->id = date('Y') - $i;
            $new_item->label = date('Y') - $i;
            $list_year[] = $new_item;
        }
        // Check user is super admin 
        $check_super_admin = APContext::isSupperAdminUser();

        $this->template->set("check_super_admin", $check_super_admin);
        $this->template->set("select_year", $year);
        $this->template->set("select_month", $month);
        $this->template->set("list_month", $list_month);
        $this->template->set("list_year", $list_year);
        $this->template->build('admin/invoices');
    }

    public function monthly_report() {
        // Gets locations
        $locations = APUtils::loadListAccessLocation();

        $this->template->set("locations", $locations);
        $this->template->build('admin/monthly_report');
    }

    public function accounting_report() {
        // Gets locations
        $locations = APUtils::loadListAccessLocation();

        $this->template->set("locations", $locations);
        $this->template->build('admin/accounting_report');
    }

    public function transaction() {
        // Gets locations
        $locations = APUtils::loadListAccessLocation();

        // #1293 use same seach filtering for transactions as in invoices
        $year = $this->input->get_post("year", "");
        $month = $this->input->get_post("month", "");

        if (empty($year)) {
            $year = date("Y", now());
        }
        if (empty($month)) {
            $month = date("m", now());
        }

        $list_month = array();
        $list_year = array();
        for ($i = 1; $i <= 12; $i++) {
            $new_item = new stdClass();
            $new_item->id = $i < 10 ? '0' . $i : $i;
            $new_item->label = $i < 10 ? '0' . $i : $i;
            $list_month[] = $new_item;
        }
        for ($i = 0; $i < 10; $i++) {
            $new_item = new stdClass();
            $new_item->id = date('Y') - $i;
            $new_item->label = date('Y') - $i;
            $list_year[] = $new_item;
        }

        // Check user is super admin 
        $check_super_admin = APContext::isSupperAdminUser();

//        $this->template->set("locations", $locations);
        $this->template->set("select_year", $year);
        $this->template->set("select_month", $month);
        $this->template->set("list_month", $list_month);
        $this->template->set("list_year", $list_year);
        $this->template->set("check_super_admin", $check_super_admin);
        $this->template->build('admin/transaction_report');
    }

    /**
     * Show location report
     */
    public function location_report() {
        $locations = array();
        // Gets locations
        if (APContext::isAdminUser()) {
            $firstItem = new stdClass();
            $firstItem->id = "";
            $firstItem->location_name = "Total";
            $locations[] = $firstItem;
        }
        $locations_access = APUtils::loadListAccessLocation();
        foreach ($locations_access as $la) {
            $locations[] = $la;
        }
        $this->template->set("locations", $locations);

        // Gets locations id
        $location_id = empty($this->input->get_post("location_available_id")) ? $locations[0]->id : $this->input->get_post("location_available_id");
        $year = $this->input->get_post("year", "");
        $month = $this->input->get_post("month", "");

        if (empty($year)) {
            $year = date("Y", now());
        }
        if (empty($month)) {
            $month = date("m", now());
        }

        $this->template->set("select_year", $year);
        $this->template->set("select_month", $month);

        $report_month = $year . $month;
        if (empty($report_month)) {
            $report_month = date("Ym", now());
        }

        if (!empty($location_id)) {
            // #481 location selection.
            APContext::updateLocationUserSetting($location_id);
        }

        $selected_location = null;
        foreach ($locations as $item_location) {
            if ($item_location->id == $location_id) {
                $selected_location = $item_location;
            }
        }

        // get receipt month.
        $receiptMonth = $month . '.' . $year;
        if (empty($month) && empty($year)) {
            $receiptMonth = date("m.Y", now());
        }
        $partner_id = "";
        if (!empty($selected_location) && !empty($selected_location->partner_id)) {
            $partner_id = $selected_location->partner_id;
        }

        // Gets rev share with location for other local invoice and credit note given
        $rev_share = price_api::getRevShareOfLocation($location_id);

        $list_month = array();
        $list_year = array();
        for ($i = 1; $i <= 12; $i++) {
            $new_item = new stdClass();
            $new_item->id = $i < 10 ? '0' . $i : $i;
            $new_item->label = $i < 10 ? '0' . $i : $i;
            $list_month[] = $new_item;
        }
        for ($i = 0; $i < 10; $i++) {
            $new_item = new stdClass();
            $new_item->id = date('Y') - $i;
            $new_item->label = date('Y') - $i;
            $list_year[] = $new_item;
        }

        // Get currency rate and currency short
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $currency_rate = APUtils::get_currency_rate_in_user_profiles();
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();

        $this->template->set("location_rev_share", $rev_share);
        $this->template->set("location_id", empty($this->input->get_post("location_available_id")) ? $locations[0]->id : $this->input->get_post("location_available_id"));
        $this->template->set("list_month", $list_month);
        $this->template->set("list_year", $list_year);
        $this->template->set("check_detail_template", false);
        $this->template->set("currency_short", $currency_short);
        $this->template->set("currency_rate", $currency_rate);
        $this->template->set("decimal_separator", $decimal_separator);

        if (empty($location_id)) {
            $invoice_by_location = $this->report_by_location_m->get_invoice_by_month($report_month);
            $this->template->set("invoice_by_location", $invoice_by_location);
            if (empty($invoice_by_location)) {
                invoices_api::updateInvoiceSummaryTotalByLocation($report_month, '', true);
                $invoice_by_location = $this->report_by_location_m->get_invoice_by_month($report_month);
            }

            $invoice_by_total = $this->report_by_total_m->get_by('invoice_month', $report_month);
            $this->template->set("invoice_by_total", $invoice_by_total);

            // get summary total
            $total_invoice = $this->invoice_summary_m->sum_by_many(array(
                "left(invoice_month,6)" => $report_month
                    ), 'total_invoice');
            $this->template->set("total_invoice", $total_invoice);

            // count all manual invoice 
            $total_manual_invoice = $this->invoice_summary_m->count_by_many(array(
                "left(invoice_month,6)" => $report_month,
                "invoice_type" => 2,
                "total_invoice <>" => 0
            ));
            $this->template->set("total_manual_invoice", $total_manual_invoice);

            $this->template->build('admin/location_report_total');
        } else {
            $invoice_by_location = $this->report_by_location_m->get_by_many(array(
                "left(invoice_month, 6) =" => $report_month,
                "location_id" => $location_id
            ));
            if (empty($invoice_by_location)) {
                invoices_api::updateInvoiceSummaryTotalByLocation($report_month, $location_id, false);

                $invoice_by_location = $this->report_by_location_m->get_by_many(array(
                    "left(invoice_month, 6) =" => $report_month,
                    "location_id" => $location_id
                ));
            }
            $pricing = price_api::getPricingMapByLocationId($location_id);
            $this->template->set("pricing", $pricing);
            $this->template->set("invoice_by_location", $invoice_by_location);

            $invoice = $this->invoice_summary_by_location_m->summary_by_location($location_id, $report_month, true);
            $this->template->set("invoice", $invoice);
            // count all manual invoice 
            $total_manual_invoice = $this->invoice_summary_by_location_m->count_manual_invoices_by($report_month, $location_id, true);
            $this->template->set("total_manual_invoice", $total_manual_invoice);

            $this->template->build('admin/location_report');
        }
    }

    /**
     * #1072 add location report generation 
     * Method for create report form action
     */
    public function create_location_report() {
        $this->template->set_layout(FALSE);

        // get params
        $location_id = $this->input->get_post("location_id", 0);
        $year = $this->input->get_post('year', '');
        $month = $this->input->get_post('month', '');

        // Codition for get location report
        $conditions = array(
            'location_id' => $location_id,
            'year' => $year,
            'month' => $month
        );

        $location_reports = $this->location_report_m->get_by_many($conditions);

        // if $_POST then create pdf and save data
        if ($_POST) {
            // load libary from_validation
            $this->load->library('form_validation');

            // set rule
            $this->form_validation->set_rules($this->location_report_rules);

            if ($this->form_validation->run()) {
                try {
                    // Gets params str_replace(',', '.', str_replace('.', '', $string_number))
                    $cost_of_location_advertising = floatval(str_replace(',', '.', $this->input->get_post("costOfLocationAdvertising", 0)));
                    $hardware_amortization = floatval(str_replace(',', '.', $this->input->get_post('hardwareAmortization', 0)));
                    $location_external_receipts = floatval(str_replace(',', '.', $this->input->get_post('locationExternalReceipts', 0)));
                    $current_open_balance = floatval(str_replace(',', '.', $this->input->get_post('currentOpenBalance', 0)));
                    //Total invoiceable so far, Total invoiced so far, Invoices written this month, Total payments made till end of this month
                    $total_invoiceable_so_far = floatval(str_replace(',', '.', $this->input->get_post('totalInvoiceableSoFar', 0)));
                    $total_invoiced_so_far = floatval(str_replace(',', '.', $this->input->get_post('totalInvoicedSoFar', 0)));
                    $invoices_written_this_month = floatval(str_replace(',', '.', $this->input->get_post('invoicesWrittenThisMonth', 0)));
                    $total_payments_made_till_end_of_this_month = floatval(str_replace(',', '.', $this->input->get_post('totalPaymentsMadeTillEndOfThisMonth', 0)));

                    // Check location report total or location report
                    if (empty($location_id)) {
                        // export location report total
                        $report_file_path = $this->export->export_location_report($location_id, $year, $month, $cost_of_location_advertising, $hardware_amortization, $location_external_receipts, $current_open_balance, $total_invoiceable_so_far, $total_invoiced_so_far, $invoices_written_this_month, $total_payments_made_till_end_of_this_month);
                    } else {
                        // export location report 
                        $report_file_path = $this->export->export_location_report($location_id, $year, $month, $cost_of_location_advertising, $hardware_amortization, $location_external_receipts, $current_open_balance, $total_invoiceable_so_far, $total_invoiced_so_far, $invoices_written_this_month, $total_payments_made_till_end_of_this_month);
                    }

                    // Send message 
                    if ($report_file_path) {
                        $data = array($location_id, $year, $month, $cost_of_location_advertising, $hardware_amortization, $location_external_receipts, $current_open_balance);

                        $this->success_output(admin_language('report_controller_admin_CreateLocalReportOutput'), $data);
                        return;
                    } else {
                        $this->error_output(admin_language('report_controller_admin_CreateLocalReportError'));
                        return;
                    }
                } catch (Exception $e) {
                    $this->error_output(admin_language('report_controller_admin_CreateLocalReportError'));
                    return;
                }
            } else {
                $error_messages = $this->form_validation->error_array();
                if (!empty($error_messages)) {
                    $response['message'] = $error_messages;
                    $this->error_output(validation_errors(), $response);
                    return;
                }
            }
        }

        // if GET show form 
        if ($location_reports) {
            $cost_of_location_advertising = $location_reports->advertising_cost;
            $hardware_amortization = $location_reports->hardware_cost;
            $location_external_receipts = $location_reports->location_external_cost;
            $current_open_balance = $location_reports->current_open_balance;
            //Total invoiceable so far, Total invoiced so far, Invoices written this month, Total payments made till end of this month
            $total_invoiceable_so_far = $location_reports->total_invoiceable_so_far;
            $total_invoiced_so_far = $location_reports->total_invoiced_so_far;
            $invoices_written_this_month = $location_reports->invoices_written_this_month;
            $total_payments_made_till_end_of_this_month = $location_reports->total_payments_made_till_end_of_this_month;
        } else {
            $cost_of_location_advertising = '0';
            $hardware_amortization = '0';
            $location_external_receipts = '0';
            $current_open_balance = '0';
            //Total invoiceable so far, Total invoiced so far, Invoices written this month, Total payments made till end of this month
            $total_invoiceable_so_far = '0';
            $total_invoiced_so_far = '0';
            $invoices_written_this_month = '0';
            $total_payments_made_till_end_of_this_month = '0';
        }
        $this->template->set("location_id", $location_id);
        $this->template->set("year", $year);
        $this->template->set("month", $month);
        $this->template->set("cost_of_location_advertising", $cost_of_location_advertising);
        $this->template->set("hardware_amortization", $hardware_amortization);
        $this->template->set("location_external_receipts", $location_external_receipts);
        $this->template->set("current_open_balance", $current_open_balance);
        //Total invoiceable so far, Total invoiced so far, Invoices written this month, Total payments made till end of this month
        $this->template->set("total_invoiceable_so_far", $total_invoiceable_so_far);
        $this->template->set("total_invoiced_so_far", $total_invoiced_so_far);
        $this->template->set("invoices_written_this_month", $invoices_written_this_month);
        $this->template->set("total_payments_made_till_end_of_this_month", $total_payments_made_till_end_of_this_month);

        $this->template->build('admin/form');
    }

    /**
     * #1072 add location report generation 
     * Method for view report location and report location total by pdf
     */
    public function view_pdf_report() {

        $location_id = $this->input->get_post("location_id", 0);

        $year = $this->input->get_post('year', '');

        $month = $this->input->get_post('month', '');

        $cost_of_location_advertising = $this->input->get_post("costOfLocationAdvertising", 0);
        $hardware_amortization = $this->input->get_post('hardwareAmortization', 0);
        $location_external_receipts = $this->input->get_post('locationExternalReceipts', 0);
        $current_open_balance = $this->input->get_post('currentOpenBalance', 0);

        //Total invoiceable so far, Total invoiced so far, Invoices written this month, Total payments made till end of this month
        $total_invoiceable_so_far = $this->input->get_post('totalInvoiceableSoFar', 0);
        $total_invoiced_so_far = $this->input->get_post('totalInvoicedSoFar', 0);
        $invoices_written_this_month = $this->input->get_post('invoicesWrittenThisMonth', 0);
        $total_payments_made_till_end_of_this_month = $this->input->get_post('totalPaymentsMadeTillEndOfThisMonth', 0);

        $array_condition = array(
            'location_id' => $location_id,
            'year' => $year,
            'month' => $month
        );

        $location_reports = ci()->location_report_m->get_by_many($array_condition);

        $file_path = $location_reports->file_path;
        // Check file exists then return file  
        if (!file_exists($file_path)) {
            // Check location report total or location report
            if (empty($location_id)) {
                // export location report total
                $file_path = $this->export->export_location_report($location_id, $year, $month, $cost_of_location_advertising, $hardware_amortization, $location_external_receipts, $current_open_balance, $total_invoiceable_so_far, $total_invoiced_so_far, $invoices_written_this_month, $total_payments_made_till_end_of_this_month);
            } else {
                // export location report 
                $file_path = $this->export->export_location_report($location_id, $year, $month, $cost_of_location_advertising, $hardware_amortization, $location_external_receipts, $current_open_balance, $total_invoiceable_so_far, $total_invoiced_so_far, $invoices_written_this_month, $total_payments_made_till_end_of_this_month);
            }
        }

        header('Content-Description: inline');
        header('Content-type: application/pdf');
        readfile($file_path);
    }

    public function update_location_report() {
        $targetYM = $this->input->get_post('ym');

        if (!empty($targetYM)) {
            invoices_api::updateInvoiceSummaryTotalByLocation($targetYM, '', true);
            //echo "done.";
            redirect('admin/report/location_report');
        }
    }

    public function open_balance() {
        // Gets locations
        $locations = APUtils::loadListAccessLocation();

        $this->template->set("locations", $locations);
        $this->template->build('admin/open_balance_report');
    }

    public function storage_fee() {
        // Gets locations
        $locations = APUtils::loadListAccessLocation();

        $this->template->set("locations", $locations);
        $this->template->build('admin/storage_fee_report');
    }

    public function email_send_hist() {
        $this->template->build('admin/email_send_hist');
    }

    /**
     * Get account reporting
     */
    public function account_report_search() {
        //#1058 add multi dimension capability for admin
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $currency_rate = APUtils::get_currency_rate_in_user_profiles();
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        $date_format = APUtils::get_date_format_in_user_profiles();

        // Get input condition
        $fromDate = $this->input->get_post("fromDate");
        $toDate = $this->input->get_post("toDate");
        $withVAT = $this->input->get_post("withVAT");
        $reverse_charge = $this->input->get_post("reverse_charge");
        $target_month = APUtils::getTargetMonthInvoice();
        $target_year = APUtils::getTargetYearInvoice();

        // Gets locations id
        $location_id = $this->input->get_post("location_available_id", "");
        if (!$location_id) {
            $locations = APUtils::loadListAccessLocation();
            $location_id = APUtils::getListIdsOfObjectArray($locations, "id");
            $location_id = implode(",", $location_id);
        } else {
            // #481 location selection.
            APContext::updateLocationUserSetting($location_id);
        }

        $array_condition = array();
        if (!empty($fromDate)) {
            $fromDate = APUtils::convertDateFormat02($fromDate);
            $array_condition["invoice_summary.invoice_month >="] = $fromDate;
        }
        if (!empty($toDate)) {
            $toDate = APUtils::convertDateFormat02($toDate);
            $array_condition["invoice_summary.invoice_month <="] = $toDate;
        }
        if (!empty($withVAT)) {
            $array_condition['invoice_summary.vat > 0'] = null;
        }
        if (!empty($reverse_charge)) {
            $array_condition["invoice_summary.vat_case IN (0,1,5)"] = null;
        }
        $array_condition['invoice_summary.total_invoice <> 0 '] = null;
        $array_condition["invoice_summary.invoice_month < " . $target_year . $target_month] = null;
        $array_condition["p.location_available_id IN (" . $location_id . ") "] = null;

        // update limit into user_paging.
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
        APContext::updateAdminPagingSetting($limit);

        // Get paging input
        $input_paging = $this->get_paging_input();
        $input_paging['limit'] = $limit;

        // Call search method
        $query_result = $this->report_m->get_account_report_paging($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);

        // Process output data
        $total = $query_result['total'];
        $datas = $query_result['data'];

        // Get output response
        $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);

        $i = 0;
        foreach ($datas as $row) {
            $vat = $row->vat;
            $reverse_charge = $row->reverse_charge;

            if ($reverse_charge == '1') {
                $reverse_charge_out = 'Yes';
            } else {
                $reverse_charge_out = 'No';
            }

            $multiple_vat_count = $this->report_m->count_invoices_report_multivat($row->customer_id);
            $multiple_vat = 'No';
            if ($multiple_vat_count > 1) {
                $multiple_vat = 'Yes';
            }

            $gross_price = $row->total_invoice;
            $net_price = $gross_price / (1 + $vat);
            $invoice_month = $row->invoice_month;
            if (strlen($invoice_month) == 6) {
                $invoice_month = APUtils::getLastDayOfMonth($invoice_month . '01');
            }
            $invoice_code = $row->invoice_code;
            $response->rows[$i]['id'] = $row->id;
            $response->rows[$i]['cell'] = array(
                $row->id,
                $row->customer_id,
                $row->name,
                $row->company,
                $row->email,
                APUtils::convertDateFormatFrom($invoice_month),
                $row->invoice_code,
                APUtils::view_convert_number_in_currency($net_price, $currency_short, $currency_rate, $decimal_separator),
                $vat,
                $multiple_vat,
                APUtils::view_convert_number_in_currency($gross_price, $currency_short, $currency_rate, $decimal_separator),
                $reverse_charge_out
            );
            $i++;
        }
        echo json_encode($response);
    }

    /**
     * Export accounting list to CSV
     */
    public function export_accounting_csv() {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 1200);
        // Get input condition
        $fromDate = $this->input->get_post("fromDate");
        $toDate = $this->input->get_post("toDate");
        $withVAT = $this->input->get_post("withVAT");
        $reverse_charge = $this->input->get_post("reverse_charge");

        $array_condition = array();
        if (!empty($fromDate)) {
            $fromDate = APUtils::convertDateFormat02($fromDate);
            $array_condition['invoice_summary.invoice_month >='] = $fromDate;
        }
        if (!empty($toDate)) {
            $toDate = APUtils::convertDateFormat02($toDate);
            $array_condition['invoice_summary.invoice_month <='] = $toDate;
        }
        if (!empty($withVAT)) {
            $array_condition['invoice_summary.vat > 0'] = null;
        }
        if (!empty($reverse_charge)) {
            $array_condition["invoice_summary.vat_case IN (0,1,5)"] = null;
        }

        $export_date = date('dMy');
        $filename = 'Accounting_' . $export_date . '.csv';

        $export_rows[] = array(
            admin_language("report_controller_admin_export_accounting_HeaderName"),
            admin_language("report_controller_admin_export_accounting_HeaderCompany"),
            admin_language("report_controller_admin_export_accounting_HeaderDate"),
            admin_language("report_controller_admin_export_accounting_HeaderNetTotal"),
            admin_language("report_controller_admin_export_accounting_HeaderVat"),
            admin_language("report_controller_admin_export_accounting_HeaderMulVat"),
            admin_language("report_controller_admin_export_accounting_HeaderGrossVat"),
            admin_language("report_controller_admin_export_accounting_HeaderRev"),
        );

        // Call search method
        $query_result = $this->report_m->get_account_report_paging($array_condition, 0, 0, null, null);

        // Process output data
        $total = $query_result['total'];
        $datas = $query_result['data'];

        $i = 0;
        foreach ($datas as $row) {
            $vat = $row->vat;
            $reverse_charge = $row->reverse_charge;

            if ($reverse_charge == '1') {
                $reverse_charge_out = 'Yes';
            } else {
                $reverse_charge_out = 'No';
            }

            $multiple_vat_count = $this->report_m->count_invoices_report_multivat($row->customer_id);
            $multiple_vat = 'No';
            if ($multiple_vat_count > 1) {
                $multiple_vat = 'Yes';
            }

            $gross_price = $row->total_invoice;
            $net_price = $gross_price / (1 + $vat);
            $invoice_month = $row->invoice_month;
            if (strlen($invoice_month) == 6) {
                $invoice_month = APUtils::getLastDayOfMonth($invoice_month . '01');
            }
            $invoice_code = $row->invoice_code;
            $export_row = array(
                $row->name,
                $row->company,
                APUtils::convertDateFormat01($invoice_month),
                APUtils::number_format($net_price, 2),
                $vat,
                $multiple_vat,
                APUtils::number_format($gross_price, 2),
                $reverse_charge_out
            );
            $export_rows[] = $export_row;
        }

        $output = APUtils::arrayToCsv($export_rows);
        header('Content-Description: File Transfer');
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        // print chr(255) . chr(254) . mb_convert_encoding($output, 'UTF-16LE', 'UTF-8');
        echo $output;
        exit();
    }

    /**
     * Get account reporting
     */
    public function invoice_report_search() {
        //#1058 add multi dimension capability for admin
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $currency_rate = APUtils::get_currency_rate_in_user_profiles();
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();

        // Get input condition
        $enquiry = $this->input->get_post("enquiry");

        $from_year = $this->input->get_post('year', 0);
        $from_month = $this->input->get_post('month', 0);
        $to_year = $this->input->get_post('to_year');
        $to_month = $this->input->get_post('to_month');

        if (!$to_year) {
            $to_year = APUtils::getTargetYearInvoice();
        }

        $array_condition = array();
        if (!empty($enquiry)) {
            $enquiry = APUtils::sanitizing($enquiry);
            $array_condition["(customers.customer_code = '" . $enquiry . "' OR customers.customer_id = '" . $enquiry . "'"
                    . " OR customers.email = '" . $enquiry . "' OR invoice_summary.invoice_code = '" . $enquiry . "'"
                    . " OR p.name LIKE '%" . $enquiry . "%'" . " OR p.company LIKE '%" . $enquiry . "%')"] = null;
        }
//        $array_condition['invoice_summary.total_invoice <> 0 '] = null; // turning seach in sql 

        if ($to_year && $to_month) {
            $array_condition["LEFT(invoice_summary.invoice_month, 6) <= '" . $to_year . $to_month . "'"] = null;
        }

        if ($from_year && $from_month) {
            $array_condition["LEFT(invoice_summary.invoice_month, 6) >= '" . $from_year . $from_month . "'"] = null;
        }

//        if($to_month > APUtils::getPreviousMonth()){
//            $array_condition["invoice_summary.invoice_type = 2"] = null;
//        }

        if ($to_month > APUtils::getPreviousMonth()) {
            $array_condition["(((invoice_summary.invoice_type = 0 OR invoice_summary.invoice_type IS NULL OR invoice_summary.invoice_type =1) AND LEFT(invoice_summary.invoice_month, 6) < '"
                    . $to_year . $to_month . "') OR invoice_summary.invoice_type = 2)"] = null;
        }

        // update limit into user_paging.
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
        APContext::updateAdminPagingSetting($limit);

        // Get paging input
        $input_paging = $this->get_paging_input();
        $input_paging['limit'] = $limit;

        // Call search method
        $query_result = $this->report_m->get_invoices_report_paging($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);

        // Process output data
        $total = $query_result['total'];
        $datas = $query_result['data'];

        // Get output response
        $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);

        // Gets G-Konto and Konto
        // Settings::get(APConstants::THIRST_COUNTRY_TAXABLE)
        $gkonto = Settings::get(APConstants::GEGENKONTO_NUMBER);
        $thirst_country_taxable = Settings::get(APConstants::THIRST_COUNTRY_TAXABLE);
        $eu_country_taxable = Settings::get(APConstants::EU_COUNTRY_TAXABLE);
        $inland_taxable = Settings::get(APConstants::INLAND_TAXABLE_REVENUE);

        $i = 0;
        foreach ($datas as $row) {
            $vat = $row->vat;
            $reverse_charge = $row->reverse_charge;

            if ($reverse_charge == '1') {
                $reverse_charge_out = 'Yes';
            } else {
                $reverse_charge_out = 'No';
            }

            // update gross total.
            $gross_price = $row->total_invoice * (1 + $vat);
            $invoice_month = $row->invoice_month;
            if (strlen($invoice_month) == 6) {
                $invoice_month = APUtils::getLastDayOfMonth($invoice_month . '01');
            }

            // konto number
            $konto = $inland_taxable;

            if ($row->country_id == APConstants::GERMANY_COUNTRY_ID) {
                $konto = $inland_taxable;
            } else if ($row->eu_member_flag == APConstants::ON_FLAG) {
                if ($vat == 0) {
                    $konto = $eu_country_taxable;
                } else {
                    $konto = $inland_taxable;
                }
            } else if ($row->eu_member_flag != APConstants::ON_FLAG) {
                if ($vat == 0) {
                    $konto = $thirst_country_taxable;
                } else {
                    $konto = $inland_taxable;
                }
            }

            $payment_status = '';
            if ($row->invoice_flag == '1' && $row->payment_1st_flag == APConstants::ON_FLAG && $row->payment_2st_flag == APConstants::ON_FLAG) {
                $payment_status = 'OK';
            }

            $eu_member_flag = 'No';
            if ($row->eu_member_flag == APConstants::ON_FLAG) {
                $eu_member_flag = 'Yes';
            }

            $response->rows[$i]['id'] = $row->id;
            $response->rows[$i]['cell'] = array(
                $row->id,
                $row->customer_id,
                $row->customer_code,
                $row->invoice_code,
                $row->name, //#914 NEW-FEATURE: Develop a function in Admin site to support deleting Invoice/Payment of Deleted customer 
                $row->company, //#914 NEW-FEATURE: Develop a function in Admin site to support deleting Invoice/Payment of Deleted customer 
                $row->country_code,
                $row->vat_number ? substr($row->vat_number, 2, strlen($row->vat_number)) : "",
                $row->country_name,
                $eu_member_flag,
                $row->email,
                $row->total_postbox,
                APUtils::view_convert_number_in_currency($gross_price, $currency_short, $currency_rate, $decimal_separator),
                APUtils::view_convert_number_in_currency(abs($gross_price), $currency_short, $currency_rate, $decimal_separator),
                APUtils::view_convert_number_in_currency($row->total_invoice, $currency_short, $currency_rate, $decimal_separator),
                ($vat * 100) . '%',
                APUtils::displayDate($invoice_month),
                date("dm", strtotime($invoice_month)),
                $reverse_charge_out,
                // S: credit note, H: invoice type.
                $gross_price > 0 ? "H" : "S",
                $gkonto,
                $konto,
                $row->invoice_code // #914 NEW-FEATURE: Develop a function in Admin site to support deleting Invoice/Payment of Deleted customer 
            );
            $i++;
        }
        echo json_encode($response);
    }

    /**
     * Export invoices list
     */
    public function invoice_report_export() {
        // Get input condition
        $enquiry = $this->input->get_post("enquiry");

        $from_year = $this->input->get_post('year', 0);
        $from_month = $this->input->get_post('month', 0);
        $to_year = $this->input->get_post('to_year');
        $to_month = $this->input->get_post('to_month');

        if (!$to_year) {
            $to_year = APUtils::getTargetYearInvoice();
        }

        $array_condition = array();
        if (!empty($enquiry)) {
            $array_condition["(customers.customer_code = '" . $enquiry . "' OR customers.customer_id = '" . $enquiry . "'"
                    . " OR customers.email = '" . $enquiry . "' OR invoice_summary.invoice_code = '" . $enquiry . "'"
                    . " OR p.name LIKE '%" . $enquiry . "%'" . " OR p.company LIKE '%" . $enquiry . "%')"] = null;
        }
//        $array_condition['invoice_summary.total_invoice <> 0 '] = null; // turning seach in sql 

        if ($to_year && $to_month) {
            $array_condition["LEFT(invoice_summary.invoice_month, 6) <= '" . $to_year . $to_month . "'"] = null;
        }

        if ($from_year && $from_month) {
            $array_condition["LEFT(invoice_summary.invoice_month, 6) >= '" . $from_year . $from_month . "'"] = null;
        }

//        if($to_month > APUtils::getPreviousMonth()){
//            $array_condition["invoice_summary.invoice_type = 2"] = null;
//        }

        if ($to_month > APUtils::getPreviousMonth()) {
            $array_condition["(((invoice_summary.invoice_type = 0 OR invoice_summary.invoice_type IS NULL OR invoice_summary.invoice_type =1) AND LEFT(invoice_summary.invoice_month, 6) < '"
                    . $to_year . $to_month . "') OR invoice_summary.invoice_type = 2)"] = null;
        }

        $start = 0;
        // Call search method
        $query_result = $this->report_m->get_invoices_report_paging($array_condition, $start, 1, null, null);
        // Process output data
        $total = $query_result['total'];
        $datas = $query_result['data'];
        $export_date = date('dMy');
        $filename = 'Invoices_' . $export_date . '.csv';

        $export_rows[] = array(
            admin_language("report_controller_admin_InvReportExpHeaderCusId"),
            admin_language("report_controller_admin_InvReportExpHeaderInvCode"),
            admin_language("report_controller_admin_InvReportExpHeaderName"),
            admin_language("report_controller_admin_InvReportExpHeaderCompany"),
            admin_language("report_controller_admin_InvReportExpHeaderCountryCode"),
            admin_language("report_controller_admin_InvReportExpHeaderVatId"),
            admin_language("report_controller_admin_InvReportExpHeaderCountry"),
            admin_language("report_controller_admin_InvReportExpHeaderEu"),
            admin_language("report_controller_admin_InvReportExpHeaderEmail"),
            admin_language("report_controller_admin_InvReportExpHeaderPb"),
            admin_language("report_controller_admin_InvReportExpHeaderCharge"),
            admin_language("report_controller_admin_InvReportExpHeaderValue"),
            admin_language("report_controller_admin_InvReportExpHeaderNetTotal"),
            admin_language("report_controller_admin_InvReportExpHeaderVatId"),
            admin_language("report_controller_admin_InvReportExpHeaderDate"),
            admin_language("report_controller_admin_InvReportExpHeaderDdmm"),
            admin_language("report_controller_admin_InvReportExpHeaderRev"),
            admin_language("report_controller_admin_InvReportExpHeaderSh"),
            admin_language("report_controller_admin_InvReportExpHeaderGkonto"),
            admin_language("report_controller_admin_InvReportExpHeaderKonto")
        );

        $gkonto = Settings::get(APConstants::GEGENKONTO_NUMBER);
        $thirst_country_taxable = Settings::get(APConstants::THIRST_COUNTRY_TAXABLE);
        $eu_country_taxable = Settings::get(APConstants::EU_COUNTRY_TAXABLE);
        $inland_taxable = Settings::get(APConstants::INLAND_TAXABLE_REVENUE);

        while ($start < $total) {
            // Call search method
            $query_result = $this->report_m->get_invoices_report_paging($array_condition, $start, 1000, null, null);
            $start += 1000;

            // Process output data
            $datas = $query_result['data'];

            $i = 0;
            foreach ($datas as $row) {
                $vat = $row->vat;
                $reverse_charge = $row->reverse_charge;

                if ($reverse_charge == '1') {
                    $reverse_charge_out = 'Yes';
                } else {
                    $reverse_charge_out = 'No';
                }

                // update gross total.
                $gross_price = $row->total_invoice * (1 + $vat);
                $invoice_month = $row->invoice_month;
                if (strlen($invoice_month) == 6) {
                    $invoice_month = APUtils::getLastDayOfMonth($invoice_month . '01');
                }

                // konto number
                $konto = $inland_taxable;

                if ($row->country_id == APConstants::GERMANY_COUNTRY_ID) {
                    $konto = $inland_taxable;
                } else if ($row->eu_member_flag == APConstants::ON_FLAG) {
                    if ($vat == 0) {
                        $konto = $eu_country_taxable;
                    } else {
                        $konto = $inland_taxable;
                    }
                } else if ($row->eu_member_flag != APConstants::ON_FLAG) {
                    if ($vat == 0) {
                        $konto = $thirst_country_taxable;
                    } else {
                        $konto = $inland_taxable;
                    }
                }

                $payment_status = '';
                if ($row->invoice_flag == '1' && $row->payment_1st_flag == APConstants::ON_FLAG && $row->payment_2st_flag == APConstants::ON_FLAG) {
                    $payment_status = 'OK';
                }

                $eu_member_flag = 'No';
                if ($row->eu_member_flag == APConstants::ON_FLAG) {
                    $eu_member_flag = 'Yes';
                }

                $export_rows[] = array(
                    $row->customer_code,
                    $row->invoice_code,
                    $row->name, //#914 NEW-FEATURE: Develop a function in Admin site to support deleting Invoice/Payment of Deleted customer 
                    $row->company, //#914 NEW-FEATURE: Develop a function in Admin site to support deleting Invoice/Payment of Deleted customer 
                    $row->country_code,
                    $row->vat_number ? substr($row->vat_number, 2, strlen($row->vat_number)) : "",
                    $row->country_name,
                    $eu_member_flag,
                    $row->email,
                    $row->total_postbox,
                    APUtils::number_format($gross_price, 2),
                    APUtils::number_format(abs($gross_price), 2),
                    APUtils::number_format($row->total_invoice, 2),
                    ($vat * 100) . '%',
                    APUtils::displayDate($invoice_month),
                    date("dm", strtotime($invoice_month)),
                    $reverse_charge_out,
                    // S: credit note, H: invoice type.
                    $gross_price > 0 ? "H" : "S",
                    $gkonto,
                    $konto
                );
            }
        }

        $output = APUtils::arrayToCsv($export_rows);
        header('Content-Description: File Transfer');
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        echo $output;
        // print chr(255) . chr(254) . mb_convert_encoding($output, 'UTF-16LE', 'UTF-8');
        exit();
    }

    /**
     * View pdf invoice file
     */
    public function view_pdf_invoice() {
        // Does not use layout
        $this->template->set_layout(FALSE);
        $url = $this->input->get_post('url');

        $this->template->set('pdf_file_url', $url);
        // load the theme_example view
        $this->template->build('invoices/view_pdf_invoice');
    }

    /**
     * export pdf.
     *
     * @param unknown_type $target_date
     */
    public function export_invoice($invoice_code) {
        // $invoice_id = $this->input->get_post('invoice_id');
        $customer_id = $this->input->get_post('customer_id');
        $this->load->library('invoices/export');

        $type = $this->input->get_post("type");

        // export credit note, export invoice.
        $invoice_file_path = $this->export->export_invoice($invoice_code, $customer_id);

        // Load invoices library
        // $invoice_file_path = $this->export->export_invoice($invoice_code, $customer_id);
        $action = $this->input->get_post('action');

        if (empty($invoice_file_path)) {
            echo "not file";
            return;
        }

        // file transfer if the action is download
        if ($action === 'download') {
            header('Content-Description: File Transfer');
            header('Content-type: application/pdf');
            header('Content-Disposition: attachment; filename=' . basename($invoice_file_path));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($invoice_file_path));
        }
        readfile($invoice_file_path);
    }

    /**
     * Check payment exist before output pdf file
     */
    public function check_payment_exist() {
        // Does not use layout
        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('customer_id');
        $this->load->model('payment/payment_m');
        $this->load->model('customers/customer_m');
        $customer_payments = $this->payment_m->get_many_by('customer_id', $customer_id);

        // Check payment exist
        if ($customer_payments && count($customer_payments) > 0) {
            return $this->success_output('1');
        }

        // Check invoice_code exist
        $customer = $this->customer_m->get_by_many(array(
            "customer_id" => $customer_id
        ));
        if (!empty($customer->invoice_code)) {
            return $this->success_output('1');
        }

        // If does not exist then return error
        return $this->success_output('0');
    }

    /**
     * Get account reporting
     */
    public function monthy_report_search() {
        $array_condition = array();

        //#1058 add multi dimension capability for admin
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $currency_rate = APUtils::get_currency_rate_in_user_profiles();
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        $date_format = APUtils::get_date_format_in_user_profiles();

        // Gets locations id
        $location_id = $this->input->get_post("location_available_id", "");
        if (!$location_id) {
            $locations = APUtils::loadListAccessLocation();
            $location_id = APUtils::getListIdsOfObjectArray($locations, "id");
            $location_id = implode(",", $location_id);
        } else {
            // #481 location selection.
            APContext::updateLocationUserSetting($location_id);
        }
        $array_condition["TEMP.location_available_id IN (" . $location_id . ") "] = null;

        // update limit into user_paging.
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
        APContext::updateAdminPagingSetting($limit);

        // Get paging input
        $input_paging = $this->get_paging_input();
        $input_paging['limit'] = $limit;

        // Call search method
        $query_result = $this->report_m->get_monthy_report_paging($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);

        // Process output data
        $total = $query_result['total'];
        $datas = $query_result['data'];

        // Get output response
        $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);

        $i = 0;
        foreach ($datas as $row) {
            $gross_price = $row->total_invoice;
            $invoice_month = $row->invoice_month;
            if (strlen($invoice_month) == 6) {
                $invoice_month = APUtils::getLastDayOfMonth($invoice_month . '01');
            }
            $invoice_month = APUtils::viewDateFormat(strtotime($invoice_month), $date_format);
            $response->rows[$i]['id'] = $i + 1;
            $response->rows[$i]['cell'] = array(
                $i + 1,
                $invoice_month,
                $row->location_name,
                APUtils::view_convert_number_in_currency($gross_price, $currency_short, $currency_rate, $decimal_separator),
                $row->invoice_month,
                $row->invoice_month
            );
            $i++;
        }
        echo json_encode($response);
    }

    /**
     * Get account reporting
     */
    public function transaction_report_search() {
        //#1058 add multi dimension capability for admin
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $currency_rate = APUtils::get_currency_rate_in_user_profiles();
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        $date_format = APUtils::get_date_format_in_user_profiles();

        /*
         * #1293 use same seach filtering for transactions as in invoices
         */
        $array_condition = array();

        // Get input condition
        $enquiry = $this->input->get_post("enquiry");
        $from_year = $this->input->get_post('year', 0);
        $from_month = $this->input->get_post('month', 0);
        $to_year = $this->input->get_post('to_year');
        $to_month = $this->input->get_post('to_month');

        // Check $to_year is false
        if (!$to_year) {
            $to_year = APUtils::getTargetYearInvoice();
        }

        // Check condition search text ( Transaction ID, customer email, name, company ) 
        if (!empty($enquiry)) {
            $enquiry = APUtils::sanitizing($enquiry);
            $array_condition["(tran_hist.txid = '" . $enquiry . "'"
                    . " OR customers.email = '" . $enquiry . "'"
                    . " OR p.name LIKE '%" . $enquiry . "%'" . " OR p.company LIKE '%" . $enquiry . "%')"] = null;
        }

        // Check transaction date 
        if ($to_year && $to_month) {
            $array_condition["tran_hist.tran_date <= '" . $to_year . $to_month . "31'"] = null;
        }

        if ($from_year && $from_month) {
            $array_condition["tran_hist.tran_date >= '" . $from_year . $from_month . "01'"] = null;
        }

        // update limit into user_paging.
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
        APContext::updateAdminPagingSetting($limit);

        // Get paging input
        $input_paging = $this->get_paging_input();
        $input_paging['limit'] = $limit;

        // Call search method
        $query_result = $this->report_m->get_transaction_report_paging($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);

        // Process output data
        $total = $query_result['total'];
        $datas = $query_result['data'];

        // Get output response
        $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);

        $i = 0;
        foreach ($datas as $row) {
            $tran_status = '';
            $tran_type = $row->tran_type;
            if ($tran_type == '1') {
                if (!empty($row->txaction) && strtolower($row->txaction == 'paid')) {
                    $tran_status = 'OK';
                } else {
                    $tran_status = strtoupper($row->txaction);
                }
            } else {
                $tran_status = 'OK';
            }

            $response->rows[$i]['id'] = $row->id;
            $response->rows[$i]['cell'] = array(
                $row->id,
                $row->customer_id,
                $row->name,
                $row->company,
                $row->email,
                $row->txid,
                APUtils::viewDateFormat(strtotime($row->tran_date), $date_format),
                APUtils::view_convert_number_in_currency($row->amount, $currency_short, $currency_rate, $decimal_separator) . '  ' . $currency_short,
                $tran_status,
                $row->id, //#914 NEW-FEATURE: Develop a function in Admin site to support deleting Invoice/Payment of Deleted customer
                $tran_type //#914 NEW-FEATURE: Develop a function in Admin site to support deleting Invoice/Payment of Deleted customer
            );
            $i++;
        }
        echo json_encode($response);
    }

    /**
     * Export transaction
     */
    public function transaction_report_export() {
        /*
         * #1293 use same seach filtering for transactions as in invoices
         */
        $array_condition = array();

        // Get input condition
        $enquiry = $this->input->get_post("enquiry");
        $from_year = $this->input->get_post('year', 0);
        $from_month = $this->input->get_post('month', 0);
        $to_year = $this->input->get_post('to_year');
        $to_month = $this->input->get_post('to_month');

        if (!$to_year) {
            $to_year = APUtils::getTargetYearInvoice();
        }

        // Check condition search text ( Transaction ID, customer email, name, company ) 
        if (!empty($enquiry)) {
            $enquiry = APUtils::sanitizing($enquiry);
            $array_condition["(tran_hist.txid = '" . $enquiry . "'"
                    . " OR customers.email = '" . $enquiry . "'"
                    . " OR p.name LIKE '%" . $enquiry . "%'" . " OR p.company LIKE '%" . $enquiry . "%')"] = null;
        }

        // Check transaction date 
        if ($to_year && $to_month) {
            $array_condition["tran_hist.tran_date <= '" . $to_year . $to_month . "31'"] = null;
        }

        if ($from_year && $from_month) {
            $array_condition["tran_hist.tran_date >= '" . $from_year . $from_month . "01'"] = null;
        }

        // export date and filename export 
        $export_date = date('dMy');
        $filename = 'Transactions_' . $export_date . '.csv';

        // export column's header 
        $export_rows[] = array(
            admin_language("report_controller_admin_TranReportExpHeaderCustId"),
            admin_language("report_controller_admin_TranReportExpHeaderName"),
            admin_language("report_controller_admin_TranReportExpHeaderCompany"),
            admin_language("report_controller_admin_TranReportExpHeaderEmail"),
            admin_language("report_controller_admin_TranReportExpHeaderTranId"),
            admin_language("report_controller_admin_TranReportExpHeaderTranDate"),
            admin_language("report_controller_admin_TranReportExpHeaderAmount"),
            admin_language("report_controller_admin_TranReportExpHeaderStatus"),
        );

        // update limit into user_paging.
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
        APContext::updateAdminPagingSetting($limit);

        //get total record 
        $query = $this->report_m->get_transaction_report_paging($array_condition, 0, 0, 'tran_date', 'desc');
        $total = $query['total'];
        if ($total > 0) {
            $total_pages = ceil($total / $limit);
        } else {
            $total_pages = 0;
        }

        // Export data 
        if ($total_pages > 0) {
            for ($page = 1; $page <= $total_pages; $page++) {
                // get start 
                $start = $limit * $page - $limit;
                $start = ($start < 0) ? 0 : $start;

                // Call search method
                $query_result = $this->report_m->get_transaction_report_paging($array_condition, $start, $limit, 'tran_date', 'desc');
                $datas = $query_result['data'];

                $tran_status = '';
                foreach ($datas as $row) {
                    $tran_type = $row->tran_type;
                    if ($tran_type == '1') {
                        if (!empty($row->txaction) && strtolower($row->txaction == 'paid')) {
                            $tran_status = 'OK';
                        } else {
                            $tran_status = strtoupper($row->txaction);
                        }
                    } else {
                        $tran_status = 'OK';
                    }

                    $export_row = array(
                        $row->customer_id,
                        $row->name,
                        $row->company,
                        $row->email,
                        $row->txid,
                        APUtils::displayDate($row->tran_date),
                        APUtils::number_format($row->amount) . ' EUR',
                        $tran_status
                    );
                    $export_rows[] = $export_row;
                }
            }
        }

        $output = APUtils::arrayToCsv($export_rows);
        header('Content-Description: File Transfer');
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        echo $output;
        // print chr(255) . chr(254) . mb_convert_encoding($output, 'UTF-16LE', 'UTF-8');
        exit();
    }

    /**
     * Import transaction.
     */
    public function import_transaction() {
        $config = array();
        $config['upload_path'] = 'uploads/temp/';
        $config['allowed_types'] = '*';
        $config['overwrite'] = TRUE;
        $this->load->library('upload', $config);
        $this->load->model('invoices/invoice_summary_m');
        if (!$this->upload->do_upload('imagepath')) {
            $this->error_output(admin_language('report_controller_admin_ImportTranError'));
            return;
        } else {
            $upload_data = $this->upload->data();
            $fileTempName = $upload_data['full_path'];

            // $data = $this->load->library('excelreader', $fileTempName);
            $data = CSVReader::parse_file($fileTempName);

            $row_arr = array();
            $i = 0;
            foreach ($data as $row) {
                $i++;
                $row_arr[] = $row;
                $txid = $row['txid'];
                $event = $row['event'];

                // Validate
                $check_tran = $this->payone_transaction_hist_m->get_by_many(
                        array(
                            "txid" => $txid,
                            "event" => $event
                ));
                // Can not insert dupplicate data
                if ($check_tran) {
                    continue;
                }

                // Reference
                $reference = $row['reference'];
                $arr_reference = explode("_", $reference);
                $invoice_id = '';
                $customer_id = null;
                if (count($arr_reference) >= 3) {
                    $invoice_id = $arr_reference[1];
                }
                if (count($arr_reference) == 4) {
                    if (is_numeric($arr_reference[2])) {
                        $customer_id = $arr_reference[2];
                    }
                }
                if (empty($customer_id)) {
                    // Get customer id from invoice_id
                    $invoice_summary = $this->invoice_summary_m->get_by_many(
                            array(
                                "id" => $invoice_id
                    ));
                    if ($invoice_summary) {
                        $customer_id = $invoice_summary->customer_id;
                    }
                }

                $create_time = strtotime($row['create_time']);
                $booking_date = strtotime($row['booking_date']);
                $document_date = strtotime($row['document_date']);

                // Insert data to database
                $this->payone_transaction_hist_m->insert(
                        array(
                            "aid" => $row['aid'],
                            "txid" => $row['txid'],
                            "reference" => $row['reference'],
                            "userid" => $row['userid'],
                            "customerid" => $row['customerid'],
                            "create_time" => $create_time,
                            "booking_date" => $booking_date,
                            "document_date" => $document_date,
                            "document_reference" => $row['document_reference'],
                            "event" => $row['event'],
                            "param" => $row['param'],
                            "clearingtype" => $row['clearingtype'],
                            "amount" => str_replace(',', '.', $row['amount']),
                            "currency" => $row['currency'],
                            "customer_id" => $customer_id,
                            "invoice_id" => $invoice_id,
                            "last_update_date" => now(),
                            "txaction" => $row['event']
                ));
            }

            // Delete file
            @unlink($this->upload_dir . $fileTempName);

            $this->success_output(admin_language('report_controller_admin_ImportTranOutput'));
            return;
        }
    }

    /**
     * Get list open balance report
     */
    public function open_balance_report_search() {
        $this->load->library('customers/customers_api');
        $this->load->model('payment/payment_job_hist_m');
        $this->load->model('payment/payment_job_hist_test_m');

        //#1058 add multi dimension capability for admin
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $currency_rate = APUtils::get_currency_rate_in_user_profiles();
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();

        $array_condition = array();

        // Baseline open balance report
        $open_balance_baseline = date('dmY', strtotime(APUtils::getLastDayOfPreviousMonth()));

        // Get input condition
        $filter_status = $this->input->get_post("filter_status");
        $filter_balance = $this->input->get_post("filter_balance");
        $filter_payment = $this->input->get_post("filter_payment");

        //#1295 improve filter in open balance report 
        $enquiry = $this->input->get_post("enquiry");

        /*
         * #1295 improve filter in open balance report 
         */
        // search text
        if (!empty($enquiry)) {
            $new_enquiry = APUtils::sanitizing($enquiry);
            $array_condition ["(customers.email = '" . $new_enquiry . "'" .
                    " OR customers.customer_code = '" . $new_enquiry . "'" .
                    " OR customers.customer_id = '" . $new_enquiry . "'" .
                    " OR customers_address.invoicing_address_name LIKE '%" . $new_enquiry . "%'" .
                    " OR customers_address.invoicing_company LIKE '%" . $new_enquiry . "%'" .
                    " OR p.name LIKE '%" . $new_enquiry . "%'" .
                    " OR p.company LIKE '%" . $new_enquiry . "%')"] = null;
        }

        // Filter by open balance
        $array_condition["(((co.open_balance_due IS NULL OR co.open_balance_due = '0') AND (co.open_balance_month > 0.005 OR co.open_balance_month < -0.005))  OR co.open_balance_due > 0.005)"] = null;

        //Filter By Status
        if ($filter_status == '1') { //Active 
            $array_condition["(customers.activated_flag = 1 AND (customers.status = 0 OR customers.status = -1 OR customers.status IS NULL ))"] = null;
        } else if ($filter_status == '2') { //Auto-deactivated
            $array_condition["( customers.status = 0 AND customers.activated_flag = 0 AND customers.deactivated_type ='auto' 
                                AND cs1.setting_value = 1 AND cs2.setting_value = 1 
                                AND cs4.setting_value = 1 AND cs3.setting_value = 1 
                                AND cs6.setting_value = 1 AND cs5.setting_value = 1 )"] = null;
        } else if ($filter_status == '3') { //Manu-deactivated
            $array_condition["( customers.status = 0 AND customers.activated_flag = 0 AND customers.deactivated_type ='manual'  
                                AND cs1.setting_value = 1 AND cs2.setting_value = 1 
                                AND cs4.setting_value = 1 AND cs3.setting_value = 1 
                                AND cs6.setting_value = 1 AND cs5.setting_value = 1 )"] = null;
        } else if ($filter_status == '4') { //Never-activated
            $array_condition["((customers.status = 0 AND customers.activated_flag = 0 AND (customers.deactivated_type IS NULL OR customers.deactivated_type ='' )) 
                            OR (customers.status <> 1 AND customers.activated_flag <> 1 AND ( cs5.setting_value IS NULL OR cs5.setting_value = '0' ))
                            OR (customers.status = 0 AND customers.activated_flag = 0 
                            AND (customers.deactivated_type = 'manual' OR customers.deactivated_type = 'auto' OR customers.deactivated_type = '')
                            AND ( cs5.setting_value IS NULL OR cs5.setting_value ='0' )))"] = null;
        } else if ($filter_status == '5') { //Delete
            $array_condition["(customers.status = 1)"] = null;
        }

        // Filter by open balance due
        if ($filter_balance == '1') { //positive 
            $array_condition["( co.open_balance_due > 0.005)"] = null;
        } else if ($filter_balance == '2') { //Zero
            $array_condition["((co.open_balance_due IS NULL OR co.open_balance_due = '0') AND (co.open_balance_month > 0.005 OR co.open_balance_month < -0.005))"] = null;
        }

        // Filter by paument
        if ($filter_payment == '1') { //Credit card
            $array_condition["(customers.invoice_type = 1)"] = null;
        } else if ($filter_payment == '2') { //Invoice
            $array_condition["(customers.invoice_type = 2 OR customers.invoice_type IS NULL )"] = null;
        }

        // Will display "No charge" customer, but don't allow make payment job
        // $array_condition ['customers.charge_fee_flag'] = '1';
        // update limit into user_paging.
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
        APContext::updateAdminPagingSetting($limit);

        // Get paging input
        $input_paging = $this->get_paging_input();
        $input_paging['limit'] = $limit;

        $input_paging['sort_column'] = 'co.open_balance_due';
        $input_paging['sort_type'] = 'desc';

        // Call search method
        $query_result = $this->report_m->get_open_balance_report_paging($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);

        // Process output data
        $total = $query_result['total'];
        $rows = $query_result['data'];

        // Get output response
        $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);

        $list_open_balance = array();
        $i = 0;
        foreach ($rows as $row) {
            $open_balance = $row->open_balance_due;

            // Gets current open balance in this month
            $curr_open_balance_this_month = $row->open_balance_month;

            $payment_method = 'N.A';
            if ($row->invoice_type == '1') {
                $payment_method = "Credit Card";
            } else {
                $payment_method = "Invoice";
            }

            // Check if customer already run payment job for this month
            $payment_job_check = $this->payment_job_hist_m->get_by_many(
                    array(
                        "customer_id" => $row->customer_id,
                        "open_balance_baseline" => $open_balance_baseline
            ));
            $paymnet_status = '';
            $last_payment_attempt = '';
            if ($payment_job_check) {
                $paymnet_status = 'Processing';
                if (!empty($payment_job_check->payment_status) && strtolower($payment_job_check->payment_status) == 'paid') {
                    $paymnet_status = 'OK';
                }
                $payment_job_check->created_date += 2 * 60 * 60;
                $last_payment_attempt = APUtils::convert_timestamp_to_date($payment_job_check->created_date, 'd.m.Y - H:i');
            }

            // Check charging credit card is FAIL/OK
            if ($row->card_charge_flag == APConstants::CARD_CHARGE_OK) {
                $credit_card_charge = 'OK';
            } elseif ($row->card_charge_flag == APConstants::CARD_CHARGE_FAIL) {
                $credit_card_charge = 'FAIL';
            } else {
                $credit_card_charge = 'N.A.';
            }

            // All inactive with open balance
            $status = customers_api::getCustomerStatus($row);

            // Calculate number deactivated days
            $row_activated_flag = $row->activated_flag;

            $number_deactivated_days = '';
            if ($row_activated_flag == '1') {
                $number_deactivated_days = '';
            } else {
                $number_deactivated_days = CustomerUtils::getInactiveDayOfCustomerBy($row);
            }
            if ($number_deactivated_days == 0) {
                $number_deactivated_days = '';
            }

            $response->rows[$i]['id'] = $row->customer_id;
            $response->rows[$i]['cell'] = array(
                $row->customer_id,
                $row->customer_id,
                $row->charge_fee_flag,
                $row->customer_code,
                $row->name,
                $row->company,
                $row->email,
                APUtils::view_convert_number_in_currency($open_balance, $currency_short, $currency_rate, $decimal_separator) . '  ' . $currency_short,
                APUtils::view_convert_number_in_currency($curr_open_balance_this_month, $currency_short, $currency_rate, $decimal_separator) . '  ' . $currency_short,
                $payment_method,
                $credit_card_charge,
                $status,
                $last_payment_attempt,
                $number_deactivated_days,
                $row->customer_id
            );
            $i++;
        }

        echo json_encode($response);
    }

    /**
     * Export accounting list to CSV
     */
    public function export_open_balance_csv() {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 1200);
        $this->load->library('customers/customers_api');
        $this->load->model('payment/payment_job_hist_m');
        $this->load->model('payment/payment_job_hist_test_m');

        $array_condition = array();

        // Baseline open balance report
        $open_balance_baseline = date('dmY', strtotime(APUtils::getLastDayOfPreviousMonth()));

        // Get input condition
        $filter_status = $this->input->get_post("filter_status");
        $filter_balance = $this->input->get_post("filter_balance");
        $filter_payment = $this->input->get_post("filter_payment");

        //#1295 improve filter in open balance report 
        $enquiry = $this->input->get_post("enquiry");

        /*
         * #1295 improve filter in open balance report 
         */
        // search text
        if (!empty($enquiry)) {
            $new_enquiry = APUtils::sanitizing($enquiry);
            $array_condition ["(customers.email = '" . $new_enquiry . "'" .
                    " OR customers.customer_code = '" . $new_enquiry . "'" .
                    " OR customers.customer_id = '" . $new_enquiry . "'" .
                    " OR customers_address.invoicing_address_name LIKE '%" . $new_enquiry . "%'" .
                    " OR customers_address.invoicing_company LIKE '%" . $new_enquiry . "%'" .
                    " OR p.name LIKE '%" . $new_enquiry . "%'" .
                    " OR p.company LIKE '%" . $new_enquiry . "%')"] = null;
        }

        // Filter by open balance
        $array_condition["(((co.open_balance_due IS NULL OR co.open_balance_due = '0') AND (co.open_balance_month > 0.005 OR co.open_balance_month < -0.005))  OR co.open_balance_due > 0.005)"] = null;

        //Filter By Status
        if ($filter_status == '1') { //Active 
            $array_condition["(customers.activated_flag = 1 AND (customers.status = 0 OR customers.status = -1 OR customers.status IS NULL ))"] = null;
        } else if ($filter_status == '2') { //Auto-deactivated
            $array_condition["( customers.status = 0 AND customers.activated_flag = 0 AND customers.deactivated_type ='auto' 
                                AND cs1.setting_value = 1 AND cs2.setting_value = 1 
                                AND cs4.setting_value = 1 AND cs3.setting_value = 1 
                                AND cs6.setting_value = 1 AND cs5.setting_value = 1 )"] = null;
        } else if ($filter_status == '3') { //Manu-deactivated
            $array_condition["( customers.status = 0 AND customers.activated_flag = 0 AND customers.deactivated_type ='manual'  
                                AND cs1.setting_value = 1 AND cs2.setting_value = 1 
                                AND cs4.setting_value = 1 AND cs3.setting_value = 1 
                                AND cs6.setting_value = 1 AND cs5.setting_value = 1 )"] = null;
        } else if ($filter_status == '4') { //Never-activated
            $array_condition["((customers.status = 0 AND customers.activated_flag = 0 AND (customers.deactivated_type IS NULL OR customers.deactivated_type ='' )) 
                            OR (customers.status <> 1 AND customers.activated_flag <> 1 AND ( cs5.setting_value IS NULL OR cs5.setting_value = '0' ))
                            OR (customers.status = 0 AND customers.activated_flag = 0 
                            AND (customers.deactivated_type = 'manual' OR customers.deactivated_type = 'auto' OR customers.deactivated_type = '')
                            AND ( cs5.setting_value IS NULL OR cs5.setting_value ='0' )))"] = null;
        } else if ($filter_status == '5') { //Delete
            $array_condition["(customers.status = 1)"] = null;
        }

        // Filter by open balance due
        if ($filter_balance == '1') { //positive 
            $array_condition["( co.open_balance_due > 0.005)"] = null;
        } else if ($filter_balance == '2') { //Zero
            $array_condition["((co.open_balance_due IS NULL OR co.open_balance_due = '0') AND (co.open_balance_month > 0.005 OR co.open_balance_month < -0.005))"] = null;
        }

        // Filter by paument
        if ($filter_payment == '1') { //Credit card
            $array_condition["(customers.invoice_type = 1)"] = null;
        } else if ($filter_payment == '2') { //Invoice
            $array_condition["(customers.invoice_type = 2 OR customers.invoice_type IS NULL )"] = null;
        }
        // Will display "No charge" customer, but don't allow make payment job
        // $array_condition ['customers.charge_fee_flag'] = '1';
        // export date and filename export 
        $export_date = date('dMy');
        $filename = 'OpenBalance_' . $export_date . '.csv';

        // export column's header 
        $export_rows[] = array(
            admin_language("report_controller_admin_ExpOpenBalanceHeaderCusId"),
            admin_language("report_controller_admin_ExpOpenBalanceHeaderName"),
            admin_language("report_controller_admin_ExpOpenBalanceHeaderCompany"),
            admin_language("report_controller_admin_ExpOpenBalanceHeaderEmail"),
            admin_language("report_controller_admin_ExpOpenBalanceHeaderOpeBal"),
            admin_language("report_controller_admin_ExpOpenBalanceHeaderOpenBalMonth"),
            admin_language("report_controller_admin_ExpOpenBalanceHeaderPayMed"),
            admin_language("report_controller_admin_ExpOpenBalanceHeaderStatus"),
            admin_language("report_controller_admin_ExpOpenBalanceHeaderLasPayAttempt"),
            admin_language("report_controller_admin_ExpOpenBalanceHeaderTestStatus"),
        );

        // update limit into user_paging.
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
        APContext::updateAdminPagingSetting($limit);

        //get total record 
        $query = $this->report_m->get_open_balance_report_paging($array_condition, 0, 0, 'co.open_balance_due', 'desc');
        $total = $query['total'];
        if ($total > 0) {
            $total_pages = ceil($total / $limit);
        } else {
            $total_pages = 0;
        }

        // Export data 
        if ($total_pages > 0) {
            for ($page = 1; $page <= $total_pages; $page++) {
                // get start 
                $start = $limit * $page - $limit;
                $start = ($start < 0) ? 0 : $start;

                // Call search method
                $query_result = $this->report_m->get_open_balance_report_paging($array_condition, $start, $limit, 'co.open_balance_due', 'desc');
                $datas = $query_result['data'];

                foreach ($datas as $row) {
                    $open_balance = $row->open_balance_due;

                    // Gets current open balance in this month
                    $curr_open_balance_this_month = $row->open_balance_month;

                    $payment_method = '';
                    if ($row->invoice_type == '1') {
                        $payment_method = "Credit Card";
                    } else {
                        $payment_method = "Invoice";
                    }

                    // Check if customer already run payment job for this month
                    $payment_job_check = $this->payment_job_hist_m->get_by_many(
                            array(
                                "customer_id" => $row->customer_id,
                                "open_balance_baseline" => $open_balance_baseline
                    ));
                    $paymnet_status = '';
                    $last_payment_attempt = '';
                    if ($payment_job_check) {
                        $paymnet_status = 'Processing';
                        if (!empty($payment_job_check->payment_status) && strtolower($payment_job_check->payment_status) == 'paid') {
                            $paymnet_status = 'OK';
                        }
                        $last_payment_attempt = APUtils::convert_timestamp_to_date($payment_job_check->created_date, 'd.m.Y - H:i');
                    }

                    // Get test status
                    $payment_job_test_check = $this->payment_job_hist_test_m->get_by_many(
                            array(
                                "customer_id" => $row->customer_id,
                                "open_balance_baseline" => $open_balance_baseline
                    ));
                    $test_status = '';
                    $last_test = '';
                    if ($payment_job_test_check) {
                        $payment_test_status = '';
                        if (!empty($payment_job_test_check->payment_status) && strtolower($payment_job_test_check->payment_status) == 'paid') {
                            $payment_test_status = 'OK';
                        } else
                        if ($payment_job_test_check->payment_status == 'ERROR') {
                            $payment_test_status = 'ERROR';
                        } else
                        if (!empty($payment_job_test_check)) {
                            $payment_test_status = 'Processing';
                        }
                        $last_test = APUtils::convert_timestamp_to_date($payment_job_test_check->created_date, 'd.m.Y - H:i');
                        $test_status = $last_test . '(' . $payment_test_status . ')';
                    }

                    // All inactive with open balance
                    $status = customers_api::getCustomerStatus($row);

                    $export_row = array(
                        $row->customer_code,
                        $row->name,
                        $row->company,
                        $row->email,
                        APUtils::number_format($open_balance, 2) . ' EUR',
                        APUtils::number_format($curr_open_balance_this_month, 2) . ' EUR',
                        $payment_method,
                        $status,
                        $last_payment_attempt,
                        $test_status
                    );
                    $export_rows[] = $export_row;
                }
            }
        }

        $output = APUtils::arrayToCsv($export_rows);
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        // print chr(255) . chr(254) . mb_convert_encoding($output, 'UTF-16LE', 'UTF-8');
        echo $output;
        exit();
    }

    /**
     * Make all open balance payment
     */
    public function payment_open_balance() {
        $this->load->model('payment/payment_job_hist_m');
        $this->load->model('email/email_m');
        $this->load->model('customers/customer_m');

        $this->load->library('invoices/Invoices');
        $this->load->library('payment/payone');

        // Get list customer id
        $input_list_customer_id = $this->input->get_post("list_customer_id");
        $list_customer_id = explode(",", $input_list_customer_id);
        // 20141202 Start fix: limit auto payment.
        // $limit_payment = APConstants::PAYMENT_CUSTOMER_LIMIT;
        // if(count($list_customer_id) > $limit_payment){
        // for($i=$limit_payment; $i< count($list_customer_id); $i++){
        // unset($list_customer_id[$i]);
        // }
        // }
        // 20141202 End fix: limit auto payment.

        $start = $this->input->get_post("start", 0);
        $limit = 10;

        // Baseline open balance report
        // $open_balance_baseline = date('dmY', strtotime(APUtils::getLastDayOfPreviousMonth()));
        $open_balance_baseline = date('dmY', now());

        $array_condition = array();
        $array_condition['customers.charge_fee_flag'] = '1';

        // Call search method
        $query_result = $this->report_m->get_open_balance_for_payment($array_condition, $start, $limit, null, null);

        // Process output data
        $datas = $query_result['data'];
        $i = 0;
        $total = $query_result['total'];
        $success = 0;
        foreach ($datas as $row) {
            try {
                $customer_id = $row->customer_id;

                // Only process for selected customers
                if (count($list_customer_id) > 0 && !empty($list_customer_id[0])) {
                    if (!in_array($customer_id, $list_customer_id)) {
                        log_audit_message(APConstants::LOG_DEBUG, '(Return) This customer does not belong selected list:' . $customer_id);
                        continue;
                    }
                }

                $customer = $this->customer_m->get($customer_id);
                // $open_balance = APUtils::getActualOpenBalanceDue($customer_id);
                $open_balance = APUtils::getCurrentBalance($customer_id);

                $invoice_id = APUtils::genetateReferenceForOpenBalance($customer_id);
                if ($open_balance < 0.01) {
                    log_audit_message(APConstants::LOG_ERROR, '(Return) Open balance is less than 0.01:' . $customer_id);
                    continue;
                }

                // Check if customer already run payment job for this month
                $payment_job_check = $this->payment_job_hist_m->get_by_many(
                        array(
                            "customer_id" => $customer_id,
                            "open_balance_baseline" => $open_balance_baseline
                ));

                // This customer already run payment job for this open balance
                if (!empty($payment_job_check) && $payment_job_check->job_status == APConstants::ON_FLAG) {
                    log_audit_message(APConstants::LOG_ERROR, '(Return) Payment job already exist:' . $customer_id);
                    continue;
                }

                $payment_method = '';
                if ($row->invoice_type == '1') {
                    $payment_method = "Credit Card";
                } else {
                    $payment_method = "Invoice";
                }

                // By pass Invoice method
                if ($payment_method == "Invoice") {
                    log_audit_message(APConstants::LOG_DEBUG, '(Return) Payment method is invoice:' . $customer_id);
                    continue;
                }

                if (empty($payment_job_check)) {
                    log_audit_message(APConstants::LOG_DEBUG, 'Insert Payment Job Hist exists of customer ID:' . $customer_id);
                    // Insert data to payment job
                    $this->payment_job_hist_m->insert(
                            array(
                                "customer_id" => $customer_id,
                                "open_balance_baseline" => $open_balance_baseline,
                                "reference" => $invoice_id,
                                "created_date" => now(),
                                "created_by" => APContext::getAdminIdLoggedIn(),
                                "open_balance" => $open_balance,
                                "job_status" => APConstants::OFF_FLAG
                    ));
                } else {
                    log_audit_message(APConstants::LOG_DEBUG, 'Update Payment Job Hist exists of customer ID:' . $customer_id);
                    // Update payment job hist
                    $this->payment_job_hist_m->update_by_many(
                            array(
                        "customer_id" => $customer_id,
                        "open_balance_baseline" => $open_balance_baseline
                            ), array(
                        "created_date" => now(),
                        "created_by" => APContext::getAdminIdLoggedIn(),
                        "open_balance" => $open_balance,
                        "job_status" => APConstants::OFF_FLAG
                    ));
                }

                if ($payment_method == "Credit Card") {
                    // If open balance greater 0
                    $result = $this->payone->authorize($customer_id, $invoice_id, $open_balance);

                    // Check result
                    if ($result) {
                        // Update payment job hist
                        $this->payment_job_hist_m->update_by_many(
                                array(
                            "customer_id" => $customer_id,
                            "open_balance_baseline" => $open_balance_baseline,
                            "job_status" => APConstants::OFF_FLAG
                                ), array(
                            "job_status" => APConstants::ON_FLAG
                        ));

                        $success++;
                    }                     // Deactive this account and send email to customer
                    else {
                        log_audit_message(APConstants::LOG_ERROR, 'Deactivated customer (Payment 1):' . $customer_id);

                        if (empty($customer->deactivated_type) && $customer->activated_flag == APConstants::ON_FLAG) {
                            $this->customer_m->update_by_many(array(
                                "customer_id" => $customer_id
                                    ), array(
                                "activated_flag" => APConstants::OFF_FLAG,
                                "deactivated_type" => APConstants::AUTO_INACTIVE_TYPE,
                                "payment_detail_flag" => APConstants::OFF_FLAG,
                                'deactivated_date' => now(),
                                "last_updated_date" => now()
                            ));
                            // update: convert registration process flag to customer_product_setting.
                            CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'payment_detail_flag', APConstants::OFF_FLAG);
                        }

                        // Send email confirm for user
                        // $email_template = $this->email_m->get_by('slug', APConstants::deactived_customer_notification);
                        // $data = array ( "full_name" => $customer->email, "email" => $customer->email, "site_url" => base_url());
                        // $content = APUtils::parserString($email_template->content, $data);
                        // MailUtils::sendEmail('', $this->input->get_post('email'), $email_template->subject, $content);
                    }
                }
            } catch (Exception $e) {
                log_audit_message(APConstants::LOG_ERROR, $e);
                log_audit_message(APConstants::LOG_ERROR, 'Error when make payment for customer:' . $customer_id);
            }
        }

        $data = array(
            "start" => $start + $limit,
            'limit' => $limit,
            "total" => $total
        );
        $this->success_output(admin_language('report_controller_admin_PayOneBalanceOutput'), $data);
    }

    /**
     * Make all open balance test payment
     */
    public function test_payment_open_balance() {
        $this->load->model('payment/payment_job_hist_test_m');
        $this->load->model('email/email_m');
        $this->load->model('customers/customer_m');

        $this->load->library('invoices/Invoices');
        $this->load->library('payment/payone');

        // Get list customer id
        $input_list_customer_id = $this->input->get_post("list_customer_id");
        $list_customer_id = explode(",", $input_list_customer_id);

        // Baseline open balance report
        // $open_balance_baseline = date('dmY', strtotime(APUtils::getLastDayOfPreviousMonth()));
        $open_balance_baseline = date('dmY', now());

        $array_condition = array();
        $array_condition['customers.charge_fee_flag'] = '1';
        // Call search method
        $limit = 10;
        $start = $this->input->get_post('start', 0);
        $query_result = $this->report_m->get_open_balance_for_payment($array_condition, $start, $limit, null, null);

        // Process output data
        $datas = $query_result['data'];
        $total = $query_result['total'];

        $i = 0;
        log_message(APConstants::LOG_DEBUG, '(Return) List selected customer:' . implode(',', $list_customer_id) . 'Total selected: ' . count($list_customer_id));
        foreach ($datas as $row) {
            try {
                $customer_id = $row->customer_id;

                // Only process for selected customers
                if (count($list_customer_id) > 0 && !empty($list_customer_id[0])) {
                    if (!in_array($customer_id, $list_customer_id)) {
                        log_message(APConstants::LOG_DEBUG, '(TEST Return 1) This customer does not belong selected list:' . $customer_id);
                        continue;
                    }
                }

                $customer = $this->customer_m->get($customer_id);
                $open_balance = APUtils::getCurrentBalance($customer_id);
                $invoice_id = APUtils::genetateReferenceForOpenBalance($customer_id);
                if (APUtils::number_format($open_balance, 2) == '0,00') {
                    continue;
                }

                // Check if customer already run payment job for this month
                $payment_job_check = $this->payment_job_hist_test_m->get_by_many(
                        array(
                            "customer_id" => $customer_id,
                            "open_balance_baseline" => $open_balance_baseline
                ));

                // This customer already run payment job for this open balance
                if (!empty($payment_job_check) && $payment_job_check->job_status == APConstants::ON_FLAG) {
                    continue;
                }

                $payment_method = '';
                if ($row->invoice_type == '1') {
                    $payment_method = "Credit Card";
                } else {
                    $payment_method = "Invoice";
                }

                // By pass Invoice method
                if ($payment_method == "Invoice") {
                    continue;
                }

                if (empty($payment_job_check)) {
                    // Insert data to payment job
                    $this->payment_job_hist_test_m->insert(
                            array(
                                "customer_id" => $customer_id,
                                "open_balance_baseline" => $open_balance_baseline,
                                "reference" => $invoice_id,
                                "created_date" => now(),
                                "created_by" => APContext::getAdminIdLoggedIn(),
                                "open_balance" => $open_balance,
                                "job_status" => APConstants::OFF_FLAG
                    ));
                } else {
                    // Update payment job hist
                    $this->payment_job_hist_test_m->update_by_many(
                            array(
                        "customer_id" => $customer_id,
                        "open_balance_baseline" => $open_balance_baseline
                            ), array(
                        "created_date" => now(),
                        "created_by" => APContext::getAdminIdLoggedIn(),
                        "open_balance" => $open_balance,
                        "job_status" => APConstants::OFF_FLAG
                    ));
                }

                if ($payment_method == "Credit Card") {
                    // If open balance greater 0
                    $result = $this->payone->test_authorize($customer_id, $invoice_id, $open_balance);

                    // Check result
                    if ($result) {
                        // Update payment job hist
                        $this->payment_job_hist_test_m->update_by_many(
                                array(
                            "customer_id" => $customer_id,
                            "open_balance_baseline" => $open_balance_baseline,
                            "job_status" => APConstants::OFF_FLAG
                                ), array(
                            "job_status" => APConstants::ON_FLAG
                        ));
                    }                     // Deactive this account and send email to customer
                    else {
                        // Update payment job hist
                        $this->payment_job_hist_test_m->update_by_many(
                                array(
                            "customer_id" => $customer_id,
                            "open_balance_baseline" => $open_balance_baseline
                                ), array(
                            "payment_status" => "ERROR"
                        ));
                    }
                }
            } catch (Exception $e) {
                log_message(APConstants::LOG_ERROR, $e);
                log_message(APConstants::LOG_ERROR, 'Error when make test payment for customer:' . $customer_id);
            }
        }
        $data = array(
            "start" => $start + $limit,
            'limit' => $limit,
            "total" => $total
        );
        $this->success_output(admin_language('report_controller_admin_TestPayOneBalanceOutput'), $data);
    }

    /**
     * Storage Fee Search
     */
    public function storage_fee_search() {
        $this->load->library('price/price_api');
        $this->load->model('scans/envelope_m');
        $array_condition = array();

        //#1058 add multi dimension capability for admin
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $currency_rate = APUtils::get_currency_rate_in_user_profiles();
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        $date_format = APUtils::get_date_format_in_user_profiles();

        // Gets locations id
        $location_id = $this->input->get_post("location_available_id", "");
        #1298 replace search for customer id in storage report with full search 
        $enquiry = $this->input->get_post("enquiry", "");

        if (!$location_id) {
            $locations = APUtils::loadListAccessLocation();
            $location_id = APUtils::getListIdsOfObjectArray($locations, "id");
            $location_id = implode(",", $location_id);
        } else {
            // #481 location selection.
            APContext::updateLocationUserSetting($location_id);
        }
        $array_condition["envelopes.location_id IN (" . $location_id . ") "] = null;
        $array_condition['p.completed_delete_flag <> '] = APConstants::ON_FLAG;
        $array_condition['envelopes.current_storage_charge_fee_day > '] = '0';

        // #1298 replace search for customer id in storage report with full search 
        if (!empty($enquiry)) {
            $new_enquiry = APUtils::sanitizing($enquiry);
            $array_condition ["(customers.email = '" . $new_enquiry . "'" .
                    " OR customers.customer_code = '" . $new_enquiry . "'" .
                    " OR customers.customer_id = '" . $new_enquiry . "'" .
                    " OR customers_address.invoicing_address_name LIKE '%" . $new_enquiry . "%'" .
                    " OR customers_address.invoicing_company LIKE '%" . $new_enquiry . "%'" .
                    " OR p.postbox_id = '" . $new_enquiry . "'" .
                    " OR p.postbox_code = '" . $new_enquiry . "'" .
                    " OR p.name LIKE '%" . $new_enquiry . "%'" .
                    " OR p.company LIKE '%" . $new_enquiry . "%')"] = null;
        }

        // Get paging input
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
        APContext::updateAdminPagingSetting($limit);

        $input_paging = $this->get_paging_input();
        $input_paging['limit'] = $limit;
        // Call search method
        $query_result = $this->envelope_m->get_envelope_paging_storage_fee($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);

        // Process output data
        $total = $query_result['total'];
        $datas = $query_result['data'];

        // Get output response
        $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);

        $i = 0;
        foreach ($datas as $row) {
            $response->rows[$i]['id'] = $row->id;
            $postbox_type = $row->type;
            if (empty($postbox_type)) {
                $postbox_type = APConstants::FREE_TYPE;
            }
            $location_id = $row->location_id;

            // Get price model
            // $price_postbox = price_api::getPricingModelByLocationID($location_id, $postbox_type);
            $pricing_map = price_api::getPricingModelByCusotomerAndLocationID($row->customer_id, $location_id);
            $price_postbox = $pricing_map[$postbox_type];

            // $/day
            $price_per_day_per_letter = $price_postbox['storing_items_over_free_letter'];
            $price_per_day_per_package = $price_postbox['storing_items_over_free_packages'];

            $envelope_type = Settings::get_alias02(APConstants::ENVELOPE_TYPE_CODE, $row->envelope_type_id);
            $price = 0;
            if ($envelope_type == 'Letter') {
                $price = $price_per_day_per_letter;
            } else
            if ($envelope_type == 'Package') {
                $price = $price_per_day_per_package;
            }

            $trashed_on = 0;
            if ($row->trash_date > 0 && ($row->trash_flag == APConstants::ENVELOPE_COMPLETED_TRASH_BY_ADMIN || $row->trash_flag == APConstants::OFF_FLAG || $row->trash_flag == APConstants::ENVELOPE_TRASH_BY_CUSTOMER_IN_TRASH_FOLDER || $row->trash_flag == APConstants::ON_FLAG )) {
                $trashed_on = $row->trash_date;
            }

            $send_out_on = 0;
            if ($row->direct_shipping_date > 0 && $row->direct_shipping_flag == APConstants::ON_FLAG) {
                $send_out_on = $row->direct_shipping_date;
            } else
            if ($row->collect_shipping_date > 0 && $row->collect_shipping_flag == APConstants::ON_FLAG) {
                $send_out_on = $row->collect_shipping_date;
            }
            $response->rows[$i]['cell'] = array(
                $row->id,
                $row->customer_id,
                $row->customer_code,
                $row->email,
                $row->envelope_code,
                $envelope_type,
                APUtils::viewDateFormat($row->incomming_date, $date_format),
                $send_out_on > 0 ? APUtils::viewDateFormat($send_out_on, $date_format) : '',
                $trashed_on > 0 ? APUtils::viewDateFormat($trashed_on, $date_format) : '',
                $row->previous_storage_charge_fee_day,
                $row->current_storage_charge_fee_day,
                APUtils::view_convert_number_in_currency($price, $currency_short, $currency_rate, $decimal_separator),
                APUtils::view_convert_number_in_currency(($price * $row->current_storage_charge_fee_day), $currency_short, $currency_rate, $decimal_separator)
            );
            $i++;
        }

        echo json_encode($response);
    }

    /**
     * Show all emails
     *
     * @access public
     * @return void
     */
    public function email_send_hist_search() {
        $this->load->model('email/email_sent_hist_m');
        // Get input condition
        $keyword = $this->input->get_post("enquiry");

        //#1058 add multi dimension capability for admin
        $date_format = APUtils::get_date_format_in_user_profiles();

        $array_condition = array();
        if (!empty($keyword)) {
            $array_condition["( email_sent_hist.to_email LIKE '%" . $keyword . "%'  OR email_sent_hist.subject LIKE '%" . $keyword . "%' )"] = null;
        }

        // If current request is ajax
        if ($this->is_ajax_request()) {
            // Get paging input
            $input_paging = $this->post_paging_input();
            // Call search method
            $query_result = $this->email_sent_hist_m->get_paging($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);

            // Process output data
            $total = $query_result['total'];
            $datas = $query_result['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);

            $i = 0;
            foreach ($datas as $row) {
                $response->rows[$i]['id'] = $row->id;
                $response->rows[$i]['cell'] = array(
                    $row->id,
                    $row->to_email,
                    APUtils::viewDateFormat($row->sent_date, $date_format),
                    $row->subject,
                    strip_tags($row->content)
                );
                $i++;
            }

            echo json_encode($response);
        }
    }

    /**
     * Display manage receipt screen
     */
    public function manage_receipts() {
        // Gets locations
        $locations = APUtils::loadListAccessLocation();

        $this->template->set("locations", $locations);
        $this->template->build('admin/manage_receipt');
    }

    /**
     * //#1296 add receipt scan/upload to receipts 
     * 
     * function manage_receipts_search
     * 
     * Get list manage receipts
     * 
     * return json_encode($response)
     */
    public function manage_receipts_search() {
        // load model 
        $this->load->model('report/partner_receipt_m');

        // Defined $array_condition
        $array_condition = array();

        // Baseline open balance report
        $open_balance_baseline = date('dmY', strtotime(APUtils::getLastDayOfPreviousMonth()));

        // Get input condition
        $enquiry = $this->input->get_post("enquiry");

        if (!empty($enquiry)) {
            // the search field, search for: partner name, description, location
            $new_enquiry = APUtils::sanitizing($enquiry);
            $array_condition["(partner_partner.partner_name LIKE '%" . $new_enquiry . "%'" .
                    " OR location.location_name LIKE '%" . $new_enquiry . "%'" .
                    " OR partner_receipt.description LIKE '%" . $new_enquiry . "%')"] = null;
        }

        // update limit into user_paging.
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
        APContext::updateAdminPagingSetting($limit);

        // Get paging input
        $input_paging = $this->get_paging_input();
        $input_paging['limit'] = $limit;

        // Call search method
        $query_result = $this->partner_receipt_m->get_receipt_paging($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);

        // Process output data
        $total = $query_result['total'];
        $datas = $query_result['data'];

        /*
         * Get output response  
         */
        // Get response: page, total, records
        $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);
        
        // Format date
        $format_date = APUtils::get_date_format_in_user_profiles();

        // Get response: id of rows and cell of rows
        $i = 0;
        foreach ($datas as $row) {
            $response->rows[$i]['id'] = $row->id;
            $response->rows[$i]['cell'] = array(
                $row->id,
                $row->partner_name,
                APUtils::displayDateFormat($row->date_of_receipt, $format_date),
                APUtils::number_format($row->net_amount),
                $row->description,
                $row->id, //#1296 add receipt scan/upload to receipts 
                $row->id
            );
            $i++;
        }

        // retrun json response 
        echo json_encode($response);
    }

    /**
     * Method for handling different form actions
     */
    public function add_partner_receipt() {
        $this->load->model('report/partner_receipt_m');
        $this->load->model('partner/partner_m');

        $this->template->set_layout(FALSE);
        $receipt = new stdClass();
        $receipt->id = '';
        $receipt->local_file_path = '';

        if ($_POST) {
            // Set validation rules
            $this->form_validation->set_rules($this->validation_add_partner_receipt_rules);

            // Check validation 
            if ($this->form_validation->run()) {
                //#1296 add receipt scan/upload to receipt
                // Try-catch 
                try {
                    // receipt's data
                    $data = array(
                        "partner_id" => $this->input->post('partner_id'),
                        "location_id" => $this->input->post('location_id'),
                        "date_of_receipt" => $this->input->post('date_of_receipt'),
                        "description" => $this->input->post('description'),
                        "net_amount" => $this->input->post('net_amount'),
                        "local_file_path" => trim($this->input->post('local_file_path')),
                        "created_date" => now(),
                        "created_by" => APContext::getAdminLoggedIn()->username
                    );

                    /*
                     * Upload file on amazone. 
                     */
                    $default_bucket_name = ci()->config->item('default_bucket');

                    // Defined server file name 
                    $server_file_name = $data['local_file_path'];
                    $upload_file_name_tmp = explode('/', $server_file_name);
                    $upload_file_name = $upload_file_name_tmp[count($upload_file_name_tmp) - 1];
                    $ext_file = explode('.', $upload_file_name);

                    // Defined  path relate amazon
                    $amazon_relate_path = 'partner_receipts/' . $data['partner_id'] . '/';
                    $file_name = $data['partner_id'] . '_' . date('YmdHis') . '.' . $ext_file[1];

                    // Check exists of path 
                    if (!is_dir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . $amazon_relate_path)) {
                        mkdir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . $amazon_relate_path, 0777, TRUE);
                        chmod(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . $amazon_relate_path, 0777);
                    }

                    // move temp file into local server file name
                    copy($data["local_file_path"], Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . $amazon_relate_path . $file_name);

                    // Log message file upload to S3
                    log_message(APConstants::LOG_DEBUG, "Upload file to Amazon: " . $server_file_name);

                    // Upload file to S3
                    $upload_result = S3::putObjectFile($server_file_name, $default_bucket_name, $amazon_relate_path . $file_name, S3::ACL_PRIVATE);

                    // Check status upload file to S3
                    if (!$upload_result) {
                        // Message error
                        $upload_result['message'] = admin_language('report_views_admin_managereceipt_PartnerReceiptUploadS3ErrorMessage'); // lang('report.S3.error');

                        // Log audit message 
                        log_audit_message(APConstants::LOG_INFOR, "ERROR S3 upload file: server_filename: " . $server_file_name . ", amazon_relate_path: " . $amazon_relate_path, FALSE, 'upload_file_partner_receipt');

                        // Output error
                        $this->error_output($upload_result['message']);
                        return;
                    }

                    // update file path.
                    $data['local_file_path'] = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . $amazon_relate_path . $file_name;
                    $data['amazon_file_path'] = $amazon_relate_path . $file_name;

                    // Add a receipt: Insert receipt's record into database
                    $this->partner_receipt_m->insert($data);

                    // Add success 
                    $message = admin_language('report_views_admin_managereceipt_PartnerReceiptAddSuccessMessage'); //lang('report.partner_receipt.success');
                    $this->success_output($message);
                    return;
                } catch (Exception $e) {
                    // Add error
                    $message = admin_language('report_views_admin_managereceipt_PartnerReceiptAddErrorMessage'); //lang('report.partner_receipt.error');
                    $this->error_output($message);
                    return;
                }
            } else { // Notification error for validation rules
                // json error 
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Loop through each validation rule
        foreach ($this->validation_add_partner_receipt_rules as $rule) {
            $receipt->{$rule['field']} = set_value($rule['field']);
        }
        
        //#1296 add receipt scan/upload to receipts 
        $list_partners = $this->partner_m->get_all();
        // $list_locations = $this->location_m->get_all();
        $list_locations = APUtils::loadListAccessLocation();
        $this->template->set('list_partners', $list_partners);
        $this->template->set('list_locations', $list_locations);

        // Display the current page
        $this->template->set('receipt', $receipt)
                ->set('action_type', 'add')
                ->build('admin/add_partner_receipt');
    }

    /**
     * Method for handling different form actions
     */
    public function edit_partner_receipt() {
        // Load model
        $this->load->model('report/partner_receipt_m');
        $this->load->model('partner/partner_m');

        // Get receipt's ID
        $id = $this->input->get_post("id", 0);

        //Defined receipt
        $receipt = new stdClass();
        if (!empty($id)) {
            $receipt = $this->partner_receipt_m->get($id);
        }
        // Set template layout
        $this->template->set_layout(FALSE);

        if ($_POST) {
            // Set validation rules
            $this->form_validation->set_rules($this->validation_add_partner_receipt_rules);

            // Check validation 
            if ($this->form_validation->run()) {
                try {
                    //#1296 add receipt scan/upload to receipt
                    // receipt's data
                    $data = array(
                        "partner_id" => $this->input->post('partner_id'),
                        "location_id" => $this->input->post('location_id'),
                        "date_of_receipt" => $this->input->post('date_of_receipt'),
                        "description" => $this->input->post('description'),
                        "net_amount" => $this->input->post('net_amount'),
                        "local_file_path" => trim($this->input->post('local_file_path')),
                        "created_date" => now(),
                        "created_by" => APContext::getAdminLoggedIn()->username
                    );

                    // Check receipt 
                    if( (!empty($receipt) && (!empty($data['local_file_path']) && $receipt->local_file_path != $data['local_file_path']) 
                            || (!empty($data['local_file_path']) && empty($receipt) )) ) {
                        $old_file = $receipt->local_file_path;

                        /*
                         * Upload file on amazone. 
                         */
                        $default_bucket_name = ci()->config->item('default_bucket');

                        // Defined server file name 
                        $server_file_name = $data['local_file_path'];
                        $upload_file_name_tmp = explode('/', $server_file_name);
                        $upload_file_name = $upload_file_name_tmp[count($upload_file_name_tmp) - 1];
                        $ext_file = explode('.', $upload_file_name);

                        // Defined  path relate amazon
                        $amazon_relate_path = 'partner_receipts/' . $data['partner_id'] . '/';
                        $file_name = $data['partner_id'] . '_' . date('YmdHis') . '.' . $ext_file[1];

                        // Check exists of path 
                        if (!is_dir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . $amazon_relate_path)) {
                            mkdir(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . $amazon_relate_path, 0777, TRUE);
                            chmod(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . $amazon_relate_path, 0777);
                        }

                        // move temp file into local server file name
                        copy($data['local_file_path'], Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . $amazon_relate_path . $file_name);

                        // Log message file upload to S3
                        log_message(APConstants::LOG_DEBUG, "Upload file to Amazon: " . $server_file_name);

                        // Upload file to S3
                        $upload_result = S3::putObjectFile($server_file_name, $default_bucket_name, $amazon_relate_path . $file_name, S3::ACL_PRIVATE);

                        // Check status upload file to S3
                        if (!$upload_result) {
                            // Message error
                            $upload_result['message'] = admin_language('report_views_admin_managereceipt_PartnerReceiptUploadS3ErrorMessage');//lang('report.S3.error');

                            // Log audit message 
                            log_audit_message(APConstants::LOG_INFOR, "ERROR S3 upload file: server_filename: " . $server_file_name . ", amazon_relate_path: " . $amazon_relate_path . $file_name, FALSE, 'upload_file_partner_receipt');

                            // Output error
                            $this->error_output($upload_result['message']);
                            return;
                        }

                        // update file path.
                        $data['local_file_path'] = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE) . $amazon_relate_path . $file_name;
                        $data['amazon_file_path'] = $amazon_relate_path . $file_name;

                        // delete older file
                        if (!empty($old_file)) {
                            unlink($old_file);
                            unlink($server_file_name);
                            S3::deleteObject($default_bucket_name, $old_file);
                        }
                    }

                    // Edit receipt: update record receipt to database
                    $this->partner_receipt_m->update_by_many(array("id" => $id), $data);

                    // Message susscess
                    $message = admin_language('report_views_admin_managereceipt_PartnerReceiptEditSuccessMessage'); //lang('report.partner_receipt.success');
                    $this->success_output($message);
                    return;
                } catch (Exception $e) {
                    // Message error
                    $message =  admin_language('report_views_admin_managereceipt_PartnerReceiptEditErrorMessage'); //lang('report.partner_receipt.error');
                    $this->error_output($message);
                    return;
                }
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        $list_partners = $this->partner_m->get_all();
        $list_locations = $this->location_m->get_many_by_many(array(
            'partner_id' => $receipt->partner_id
        ));

        $this->template->set('list_partners', $list_partners);
        $this->template->set('list_locations', $list_locations);

        // Display the current page
        $this->template->set('receipt', $receipt)
                ->set('action_type', 'edit')
                ->build('admin/add_partner_receipt');
    }

    /**
     * Delete partner receipt
     */
    public function delete_partner_receipt() {
        $this->load->model('report/partner_receipt_m');
        $id = $this->input->get_post("id");
        try {
            // Dang ky thong tin vao postbox
            $this->partner_receipt_m->delete_by_many(array(
                "id" => $id
            ));

            $message = admin_language('report_controller_admin_DelPartnerRecpErr');
            $this->success_output($message);
            return;
        } catch (Exception $e) {
            $message = admin_language('report_controller_admin_DelPartnerRecpError');
            $this->error_output($message);
            return;
        }
    }

    public function import_csv() {
        $this->template->set_layout(false);
        $this->load->library('files/files');
        $this->load->model("payment/payone_transaction_hist_temp_m");
        $this->load->model('payment/payment_tran_hist_m');
        $csv_path_file = Files::upload('tmp');

        if ($csv_path_file) {
            $file = fopen($csv_path_file, "r");
            $header_row_flag = 0;
            $rows = array();
            while (!feof($file)) {
                $data = (fgetcsv($file, 1000, ";"));
                if ($data) {
                    $rows[] = $data;
                }
            }
            fclose($file);

            // import into database.
            $first_header_flag = true;
            foreach ($rows as $r) {
                if ($first_header_flag) {
                    $first_header_flag = false;
                    continue;
                }

                $reference = $r[2];
                $payment_tran_hist = $this->payment_tran_hist_m->get_by_many(array(
                    "reference" => $reference
                ));
                $format_invoice_id = $reference;
                if (!empty($payment_tran_hist)) {
                    $format_invoice_id = $payment_tran_hist->invoice_id;
                }
                $tmp = explode("_", $format_invoice_id);
                if (count($tmp) < 3) {
                    continue;
                }

                $customer_id = $tmp[2];
                $invoice_id = $tmp[1];
                $txaction = $r[10];
                $amount = str_replace(',', '.', $r[12]);
                $tmp_data = array(
                    'aid' => $r[0],
                    'txid' => $r[1],
                    'reference' => $r[2],
                    'userid' => $r[3],
                    'customerid' => $r[4],
                    'create_time' => strtotime($r[5]),
                    'booking_date' => strtotime($r[6]),
                    'document_date' => strtotime($r[7]),
                    'document_reference' => $r[8],
                    'param' => $r[9],
                    'event' => $r[10],
                    'clearingtype' => $r[11],
                    'amount' => $amount,
                    'currency' => $r[13],
                    "customer_id" => $customer_id,
                    "invoice_id" => $invoice_id,
                    "txaction" => $txaction,
                    "last_update_date" => strtotime($r[5])
                );

                $existed = $this->payone_transaction_hist_temp_m->get_by_many(array(
                    "txid" => $r[1],
                    "txaction" => $txaction
                ));

                if ($existed) {
                    $this->payone_transaction_hist_temp_m->update_by_many(array('txid' => $r[1], "txaction" => $txaction), $tmp_data);
                } else {
                    $this->payone_transaction_hist_temp_m->insert($tmp_data);
                }
            }
        }

        $this->success_output(admin_language('report_controller_admin_ImportCsvOutput'));
        return;
    }

    /**
     * convert function for location report
     */
    public function convertPaypalInvoice() {
        $stmt = "SELECT s1.*
                FROM invoice_summary s1
                left join invoice_summary_by_location s2 
                on s1.customer_id=s2.customer_id and s1.invoice_code = s2.invoice_code
                and s1.invoice_month = s2.invoice_month
                WHERE s1.payment_transaction_id IS NOT NULL AND s1.invoice_type = 2
                and ( s2.id is null or s2.total_invoice is null or s2.total_invoice = 0)";

        $result = $this->customer_m->db->query($stmt)->result();
        echo "total record: " . count($result) . '<br/>';
        foreach ($result as $row) {
            $check = $this->invoice_summary_by_location_m->get_by_many(array(
                "customer_id" => $row->customer_id,
                "invoice_month" => $row->invoice_month,
                "invoice_code" => $row->invoice_code,
                "invoice_type" => 2
            ));

            if ($check) {
                $this->invoice_summary_by_location_m->update_by_many(array(
                    "customer_id" => $row->customer_id,
                    "invoice_month" => $row->invoice_month,
                    "invoice_code" => $row->invoice_code,
                    "invoice_type" => 2
                        ), array(
                    "total_invoice" => $row->total_invoice,
                    "payment_transaction_id" => $row->payment_transaction_id,
                    "vat" => $row->vat,
                    "vat_case" => $row->vat_case
                ));
            } else {
                $this->invoice_summary_by_location_m->insert(array(
                    "customer_id" => $row->customer_id,
                    "invoice_month" => $row->invoice_month,
                    "invoice_code" => $row->invoice_code,
                    "invoice_type" => 2,
                    "total_invoice" => $row->total_invoice,
                    "payment_transaction_id" => $row->payment_transaction_id,
                    "vat" => $row->vat,
                    "vat_case" => $row->vat_case
                ));
            }

            echo admin_language('report_controller_admin_ConvPayoneInvInvCode') . $row->invoice_code . ", "
                . admin_language('report_controller_admin_ConvPayoneInvCusId') . $row->customer_id . ", "
                . admin_language('report_controller_admin_ConvPayoneInvPaypalId') . $row->payment_transaction_id . "<br/>";
        }
    }

    /**
     * Martketing partner report
     */
    public function marketing_partner() {
        $this->load->model(array(
            "partner/partner_m",
            "report/report_by_partner_m"
        ));
        $this->load->library(array(
            'partner/partner_api',
        ));

        // Gets marketing partner
        $marketing_partners = $this->partner_m->get_many_by_many(array(
            "partner_type" => APConstants::PARTNER_MARKETING_TYPE,
            "deleted_flag" => APConstants::OFF_FLAG
        ));

        $selected_partner = $this->input->get_post('partner');
        $year = $this->input->get_post("year", "");
        $month = $this->input->get_post("month", "");
        if (empty($year)) {
            $year = date("Y", now());
        }
        if (empty($month)) {
            $month = date("m", now());
        }
        $report_month = $year . $month;
        if (empty($selected_partner)) {
            $selected_partner = $marketing_partners[0]->partner_id;
        }

        // update report month if current month selected.
        if ($report_month == date("Ym", now()) || $this->input->get_post('update_flag') == "1") {
            partner_api::updatePartnerMarketingReport($selected_partner, $report_month);
        }

        $list_month = array();
        $list_year = array();
        for ($i = 1; $i <= 12; $i++) {
            $new_item = new stdClass();
            $new_item->id = $i < 10 ? '0' . $i : $i;
            $new_item->label = $i < 10 ? '0' . $i : $i;
            $list_month[] = $new_item;
        }
        for ($i = 0; $i < 10; $i++) {
            $new_item = new stdClass();
            $new_item->id = date('Y') - $i;
            $new_item->label = date('Y') - $i;
            $list_year[] = $new_item;
        }

        // gets data of report.
        $invoice = $this->report_by_partner_m->get_by_many(array(
            "partner_id" => $selected_partner,
            "invoice_month" => $year . $month
        ));

        // Get currency rate and currency short
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $currency_rate = APUtils::get_currency_rate_in_user_profiles();
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();

        $this->template->set("currency_short", $currency_short)->set("currency_rate", $currency_rate)->set("decimal_separator", $decimal_separator);
        $this->template->set("select_year", $year)->set("select_month", $month)->set("list_month", $list_month)->set("list_year", $list_year);
        $this->template->set("selected_partner", $selected_partner)->set("marketing_partners", $marketing_partners);
        $this->template->set("invoice", $invoice);
        $this->template->build("admin/marketing_partner_report");
    }

    /**
     * #1296 add receipt scan/upload to receipts 
     *  upload resource file
     * 
     * function upload_file
     *
     * @return type
     */
    public function upload_file() {
        // Set template layout is false 
        $this->template->set_layout(false);

        // Get input 
        $type = $this->input->get_post("type");
        $client_file_name = $this->input->get_post("input_file_client_name");

        // Get resource file of receipt partner
        $result = partner_api::uploadResourceReceipt($type, $client_file_name);

        // Output 
        if ($result['status']) {
            // Response success 
            $this->success_output($result['message'], array("local_file_path" => $result['local_file_path']));
            return;
        } else {
            // Response error 
            $this->error_output($result['message']);
            return;
        }
    }

    /**
     * #1296 add receipt scan/upload to receipts 
     * view receipt's resourse file
     * @return type
     */
    public function view_resource() {
        // Load model
        $this->load->model("report/partner_receipt_m");

        // Set template layout
        $this->template->set_layout(false);

        // Get id
        $id = $this->input->get_post("id");

        // Get partner's resource
        $partner_resource = $this->partner_receipt_m->get($id);

        // Check partner's resource
        if ($partner_resource) {
            // Call view receipt's file from local file path 
            $this->view_receipt_file($partner_resource->local_file_path);
        } else {
            // Output if file is not exist
            echo "not file";
        }

        // Return 
        return;
    }

    /**
     * #1296 add receipt scan/upload to receipts 
     * Function view_receipt_file 
     * Output's file
     * 
     * @param type string  $local_file_path
     * 
     * @return type int 
     */
    private function view_receipt_file($local_file_path) {
        if (empty($local_file_path)) {
            echo "Not file";
            return;
        }
        // Does not use layout
        $this->template->set_layout(FALSE);

        // Get extends file
        header('Content-Disposition: inline');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($local_file_path));
        header('Accept-Ranges: bytes');

        $ext = substr($local_file_path, strrpos($local_file_path, '.') + 1);
        $ext = strtolower($ext);
        switch ($ext) {
            case 'jpg':
                header('Content-Type: image/jpeg');
                break;
            case 'bmp':
                header('Content-Type: image/bmp');
                break;
            case 'png':
                header('Content-Type: image/png');
                break;
            case 'tif':
                header('Content-Type: image/tiff');
                break;
            case 'pdf':
                header('Content-Type: application/pdf');
                break;
        }

        // Outputs file
        readfile($local_file_path);
    }

    /**
     * export credit note.
     * @param type $invoice_code
     * @return type
     */
    public function export_credit_by_location($invoice_code) {
        // $invoice_id = $this->input->get_post('invoice_id');
        $customer_id = $this->input->get_post('customer_id');
        $this->load->library('invoices/export');

        $tmp = $this->input->get_post('tmp');
        $type = $this->input->get_post("type");
        $location_id = $this->input->get_post("location_id");

        // export credit note.
        if (empty($location_id)) {
            $invoice_file_path = $this->export->export_invoice($invoice_code, $customer_id);
        } else {
            $invoice_file_path = $this->export->export_credit_by_location($invoice_code, $customer_id, $location_id);
        }

        // Load invoices library
        // $invoice_file_path = $this->export->export_invoice($invoice_code, $customer_id);
        $action = $this->input->get_post('action');

        if (empty($invoice_file_path)) {
            echo "not file";
            return;
        }

        // file transfer if the action is download
        if ($action === 'download') {
            header('Content-Description: File Transfer');
            header('Content-type: application/pdf');
            header('Content-Disposition: attachment; filename=' . basename($invoice_file_path));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($invoice_file_path));
        }
        readfile($invoice_file_path);
    }

}
