<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Roles controller for the groups module
 */
class Completed extends Admin_Controller {

    /**
     * Constructor method
     */
    public function __construct() {
        parent::__construct();

        ci()->load->library(array(
            'invoices/invoices',
            'invoices/export',
            'settings/settings_api',
            'scans/scans_api',
            'scans/completed_api',
            'account/account_api',
            'mailbox/mailbox_api',
            'form_validation'
        ));

        // Load model
        $this->load->model('envelope_m');
        $this->load->model('envelope_completed_m');
        $this->load->model('envelope_properties_m');
        $this->load->model('envelope_file_m');
        $this->load->model('customers/customer_m');
        $this->load->model('scans/envelope_summary_month_m');
        $this->load->model('invoices/invoice_detail_m');
        $this->load->model('invoices/invoice_summary_m');
        $this->load->model('addresses/location_m');
        $this->load->model('addresses/customers_address_m');
        $this->load->model('scans/envelope_shipping_tracking_m');
        $this->load->model('customers/customer_shipping_report_m');
        $this->load->model('shipping/shipping_apis_m');
        $this->load->model('shipping/shipping_services_m');

        // Load language
        $this->lang->load('scans');
        $this->lang->load(array(
            'mailbox/delete_permission',
            'mailbox/activity_permission'
        ));
    }

    /**
     * Display all incomming envelope.
     */
    public function index() {
        /*
         * #1318 add a filter to the completed list 
         */
        //Get list activity
        $activity_id_list = $this->envelope_completed_m->get_activity_id();
        if ($activity_id_list) {
            foreach ($activity_id_list as $row) {
                $activity_arr[] = (object) array('id' => $row->activity_id, 'activity_name' => lang('envelope.completed_activity_' . $row->activity_id));
            }

            $this->template->set('activity_list', $activity_arr);
        }

        // Get date filter
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

        $this->template->set("select_year", $year);
        $this->template->set("select_month", $month);
        $this->template->set("list_month", $list_month);
        $this->template->set("list_year", $list_year);

        // List location access
        $list_access_location = APUtils::loadListAccessLocation();

        $this->template->set('list_access_location', $list_access_location);

        // Gets location
        $location = $this->input->get_post("location_id", "");

        // #481: location selection.
        if ($location) {
            APContext::updateLocationUserSetting($location);
        } else {
            $location = APContext::getLocationUserSetting();
        }

        $this->template->set("location_id", $location);

        // Display the current page
        $this->template->build('completed/index');
    }

    /**
     * Create a new group role
     */
    public function search() {
        $this->load->model('mailbox/envelope_customs_m');

        #1058 add multi dimension capability for admin
        $date_format = APUtils::get_date_format_in_user_profiles();
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $currency_rate = APUtils::get_currency_rate_in_user_profiles();
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        $weight_unit = APUtils::get_weight_unit_in_user_profiles();

        // If current request is ajax
        if ($this->is_ajax_request()) {
            // Get input condition
            $enquiry = $this->input->get_post("enquiry");
            $input_location_id = $this->input->get_post('location_available_id');

            //#1318 add a filter to the completed list
            $from_year = $this->input->get_post('year', 0);
            $from_month = $this->input->get_post('month', 0);
            $to_year = $this->input->get_post('to_year');
            $to_month = $this->input->get_post('to_month');
            $activity_id = $this->input->get_post('activity_id');

            // Check $to_year is false (#1318 add a filter to the completed list)
            if (!$to_year) {
                $to_year = APUtils::getTargetYearInvoice();
            }

            // #481 location selection.
            APContext::updateLocationUserSetting($input_location_id);
            $list_access_location = APUtils::loadListAccessLocation();

            $list_access_location_id = array();
            if ($list_access_location && count($list_access_location) > 0) {
                foreach ($list_access_location as $location) {
                    $list_access_location_id [] = $location->id;
                }
            }
            $list_filter_location_id = array(
                0
            );
            if (empty($input_location_id)) {
                $list_filter_location_id = $list_access_location_id;
            } else {
                if (in_array($input_location_id, $list_access_location_id)) {
                    $list_filter_location_id [] = $input_location_id;
                }
            }
            $array_condition = array(
                "completed_flag" => APConstants::ON_FLAG
            );

            // location
            $array_condition ['location_id IN ' . "(" . implode(",", $list_filter_location_id) . ")"] = null;

            if (!empty($enquiry)) {
                //#1318 add a filter to the completed list
                $enquiry = APUtils::sanitizing($enquiry);

                $array_condition ["(envelopes_completed.from_customer_name LIKE '%" . $enquiry . "%'" .
                        " OR customers.email LIKE '%" . $enquiry . "%'" .
                        " OR p.name LIKE '%" . $enquiry . "%'" .
                        " OR p.company LIKE '%" . $enquiry . "%'" .
                        " OR ca.invoicing_address_name LIKE '%" . $enquiry . "%'" .
                        " OR ca.invoicing_company LIKE '%" . $enquiry . "%'" .
                        " OR ca.shipment_address_name LIKE '%" . $enquiry . "%'" .
                        " OR ca.shipment_company LIKE '%" . $enquiry . "%'" .
                        " OR envelopes_completed.activity_code LIKE '%" . $enquiry . "%')"] = null;
            }

            // #1318 add a filter to the completed list
            if ($to_year && $to_month) {
                $array_condition["(( FROM_UNIXTIME(envelopes_completed.completed_date, '%Y%m') <= '" . $to_year . $to_month . "' )"] = null;
            }

            if ($from_year && $from_month) {
                $array_condition["( FROM_UNIXTIME(envelopes_completed.completed_date, '%Y%m') >= '" . $from_year . $from_month . "' ))"] = null;
            }

            // activity 
            if (!empty($activity_id)) {
                $array_condition ["activity_id = '" . $activity_id . "'"] = null;
            }

            // update limit into user_paging.
            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging ['limit'] = $limit;

            // Call search method
            $query_result = $this->envelope_completed_m->get_envelope_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

            // Process output data
            $total = $query_result ['total'];
            $datas = $query_result ['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);

            /*
             * #1335 BUG: completed list show incorrect when show all 
             */

            $customs = array();
            $customs_id_list = array();

            //get custom envelope
            $customs = $this->envelope_customs_m->get_all();

            // Get customs's envelope ID list 
            foreach ($customs as $row) {
                $customs_id_list[] = $row->envelope_id;
            }

            $i = 0;
            foreach ($datas as $row) {
                //#1335 BUG: completed list show incorrect when show all 
                if (in_array($row->envelope_id, $customs_id_list)) {
                    $customs_flag = '1';
                } else {
                    $customs_flag = '';
                }

                $response->rows [$i] ['id'] = $row->id;

                $cost = 0;
                $vat = 0;

                $cost_obj = completed_api::getEnvelopeCostById($row->envelope_id, $row);

                $shipping_tracking = $this->envelope_shipping_tracking_m->get_by('envelope_id', $row->envelope_id);
                if (!empty($shipping_tracking)) {
                    $activity = "<u>" . lang('envelope.completed_activity_' . $row->activity_id) . "</u>";
                } else {
                    $activity = lang('envelope.completed_activity_' . $row->activity_id);
                }
                #1058 add multi dimension capability for admin
                $weight = APUtils::view_convert_number_in_weight($row->weight, $weight_unit, $decimal_separator);

                $postal_charge = null;
                if (in_array($row->activity_id, array(APConstants::DIRECT_FORWARDING_COMPLETED_ACTIVITY_TYPE, APConstants::COLLECT_FORWARDING_COMPLETED_ACTIVITY_TYPE))) {
                    $postal_charge = $this->envelope_shipping_m->get_by('envelope_id', $row->envelope_id);
                }

                $response->rows [$i] ['cell'] = array(
                    $row->id,
                    $row->activity_code,
                    $row->from_customer_name,
                    $row->to_customer_id,
                    $row->to_customer_name,
                    $row->invoicing_address_name,
                    $row->invoicing_company,
                    $row->shipment_address_name,
                    $row->shipment_company,
                    $row->envelope_type_id,
                    Settings::get_label(APConstants::ENVELOPE_TYPE_CODE, $row->envelope_type_id),
                    $weight,
                    APUtils::viewDateFormat($row->last_updated_date, $date_format . APConstants::TIMEFORMAT_OUTPUT01), #1058 add multi dimension capability for admin
                    $row->id,
                    $activity,
                    $row->completed_by,
                    empty($row->admin_name) ? $row->shipment_address_name : $row->admin_name,
                    APUtils::viewDateFormat($row->completed_date, $date_format . APConstants::TIMEFORMAT_OUTPUT01), #1058 add multi dimension capability for admin
                    APUtils::view_convert_number_in_currency($cost_obj ['cost'], $currency_short, $currency_rate, $decimal_separator), #1058 add multi dimension capability for admin
                    empty($postal_charge) ? "" : APUtils::view_convert_number_in_currency($postal_charge->forwarding_charges_postal, $currency_short, $currency_rate, $decimal_separator),
                    $cost_obj ['vat'] . '%',
                    $customs_flag,
                    $row->id,
                    $row->envelope_id,
                    $row->incomming_date
                );
                $i++;
            }

            echo json_encode($response);
        } else {
            // Display the current page
            $this->template->set('header_title', lang('header:list_group_title'))->build('admin/index');
        }
    }

    /**
     * #1318 add a filter to the completed list
     * Export completed list
     */
    public function completed_list_export() {
        $this->load->model('mailbox/envelope_customs_m');

        // Get input condition
        $enquiry = $this->input->get_post("enquiry");
        $from_year = $this->input->get_post('year', 0);
        $from_month = $this->input->get_post('month', 0);
        $to_year = $this->input->get_post('to_year');
        $to_month = $this->input->get_post('to_month');
        $input_location_id = $this->input->get_post('location_available_id');
        $activity_id = $this->input->get_post('activity_id');

        #1058 add multi dimension capability for admin
        $date_format = APUtils::get_date_format_in_user_profiles();
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $currency_rate = APUtils::get_currency_rate_in_user_profiles();
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        $weight_unit = APUtils::get_weight_unit_in_user_profiles();

        $array_condition = array(
            "completed_flag" => APConstants::ON_FLAG
        );

        APContext::updateLocationUserSetting($input_location_id);
        $list_access_location = APUtils::loadListAccessLocation();

        $list_access_location_id = array();
        if ($list_access_location && count($list_access_location) > 0) {
            foreach ($list_access_location as $location) {
                $list_access_location_id [] = $location->id;
            }
        }
        $list_filter_location_id = array(
            0
        );
        if (empty($input_location_id)) {
            $list_filter_location_id = $list_access_location_id;
        } else {
            if (in_array($input_location_id, $list_access_location_id)) {
                $list_filter_location_id [] = $input_location_id;
            }
        }

        // location
        $array_condition ['location_id IN ' . "(" . implode(",", $list_filter_location_id) . ")"] = null;

        if (!empty($enquiry)) {
            //#1318 add a filter to the completed list
            $enquiry = APUtils::sanitizing($enquiry);

            $array_condition ["(envelopes_completed.from_customer_name LIKE '%" . $enquiry . "%'" .
                    " OR customers.email LIKE '%" . $enquiry . "%'" .
                    " OR p.name LIKE '%" . $enquiry . "%'" .
                    " OR p.company LIKE '%" . $enquiry . "%'" .
                    " OR ca.invoicing_address_name LIKE '%" . $enquiry . "%'" .
                    " OR ca.invoicing_company LIKE '%" . $enquiry . "%'" .
                    " OR ca.shipment_address_name LIKE '%" . $enquiry . "%'" .
                    " OR ca.shipment_company LIKE '%" . $enquiry . "%'" .
                    " OR envelopes_completed.activity_code LIKE '%" . $enquiry . "%')"] = null;
        }

        if (!$to_year) {
            $to_year = APUtils::getTargetYearInvoice();
        }

        // Check completed date 
        if ($to_year && $to_month) {
            $array_condition["(( FROM_UNIXTIME(envelopes_completed.completed_date, '%Y%m') <= '" . $to_year . $to_month . "' )"] = null;
        }

        if ($from_year && $from_month) {
            $array_condition["( FROM_UNIXTIME(envelopes_completed.completed_date, '%Y%m') >= '" . $from_year . $from_month . "' ))"] = null;
        }

        // activity 
        if (!empty($activity_id)) {
            $array_condition ["activity_id = '" . $activity_id . "'"] = null;
        }

        // export date and filename export 
        $export_date = date('dMy');
        $filename = 'Completed_list_' . $export_date . '.csv';

        // export column's header 
        $export_rows[] = array(
            'Activity ID',
            'From',
            'To',
            'Invoicing',
            'Invoicing Company',
            'Shipment',
            'Shipment Company',
            'Type',
            'Weight',
            'Date and Time',
            'Activity',
            'Completed By',
            'Completed Date',
            'Cost',
            'Postal Charge',
            'VAT',
            'Customs'
        );

        // update limit into user_paging.
        $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
        APContext::updateAdminPagingSetting($limit);

        //get total record 
        $query = $this->envelope_completed_m->get_envelope_paging($array_condition, 0, 0, 'completed_date', 'desc');
        $total = $query['total'];
        if ($total > 0) {
            $total_pages = ceil($total / $limit);
        } else {
            $total_pages = 0;
        }

        /*
         * #1335 BUG: completed list show incorrect when show all 
         */
        $customs = array();
        $customs_id_list = array();

        //get custom envelope
        $customs = $this->envelope_customs_m->get_all();

        // Get customs's envelope ID list 
        foreach ($customs as $row) {
            $customs_id_list[] = $row->envelope_id;
        }

        // Export data 
        if ($total_pages > 0) {
            for ($page = 1; $page <= $total_pages; $page++) {
                // get start 
                $start = $limit * $page - $limit;
                $start = ($start < 0) ? 0 : $start;

                // Call search method
                $query_result = $this->envelope_completed_m->get_envelope_paging($array_condition, $start, $limit, 'completed_date', 'desc');
                $datas = $query_result['data'];

                $i = 0;
                $cost = 0;
                $vat = 0;
                foreach ($datas as $row) {
                    //#1335 BUG: completed list show incorrect when show all 
                    if (in_array($row->envelope_id, $customs_id_list)) {
                        $customs_flag = 'Yes';
                    } else {
                        $customs_flag = 'No';
                    }

                    $cost_obj = completed_api::getEnvelopeCostById($row->envelope_id, $row);

                    $shipping_tracking = $this->envelope_shipping_tracking_m->get_by('envelope_id', $row->envelope_id);
                    if (!empty($shipping_tracking)) {
                        $activity = "<u>" . lang('envelope.completed_activity_' . $row->activity_id) . "</u>";
                    } else {
                        $activity = lang('envelope.completed_activity_' . $row->activity_id);
                    }

                    #1058 add multi dimension capability for admin
                    $weight = APUtils::view_convert_number_in_weight($row->weight, $weight_unit, $decimal_separator);

                    $postal_charge = null;
                    if (in_array($row->activity_id, array(APConstants::DIRECT_FORWARDING_COMPLETED_ACTIVITY_TYPE, APConstants::COLLECT_FORWARDING_COMPLETED_ACTIVITY_TYPE))) {
                        $postal_charge = $this->envelope_shipping_m->get_by('envelope_id', $row->envelope_id);
                    }

                    $export_row = array(
                        $row->activity_code,
                        $row->from_customer_name,
                        $row->to_customer_name,
                        $row->invoicing_address_name,
                        $row->invoicing_company,
                        $row->shipment_address_name,
                        $row->shipment_company,
                        Settings::get_label(APConstants::ENVELOPE_TYPE_CODE, $row->envelope_type_id),
                        $weight,
                        APUtils::viewDateFormat($row->last_updated_date, $date_format . APConstants::TIMEFORMAT_OUTPUT01), #1058 add multi dimension capability for admin
                        $activity,
                        $row->completed_by,
                        APUtils::viewDateFormat($row->completed_date, $date_format . APConstants::TIMEFORMAT_OUTPUT01), #1058 add multi dimension capability for admin
                        APUtils::view_convert_number_in_currency($cost_obj ['cost'], $currency_short, $currency_rate, $decimal_separator), #1058 add multi dimension capability for admin
                        empty($postal_charge) ? "" : APUtils::view_convert_number_in_currency($postal_charge->forwarding_charges_postal, $currency_short, $currency_rate, $decimal_separator),
                        $cost_obj ['vat'] . '%',
                        $customs_flag
                    );
                    $i++;
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
     * Delete customer
     */
    public function delete() {

        $envelope_completed_id = $this->input->get_post("id");
        $result = completed_api::delete($envelope_completed_id);

        if ($result['status']) {

            $this->success_output($result['message']);
            return;
        } else {

            $this->error_output($result['message']);
            return;
        }
    }

    public function view_detail_items() {

        $this->template->set_layout(FALSE);
        $this->load->model('addresses/customers_forward_address_m');

        $customer_id = $this->input->get_post('customer_id');
        $envelope_id = $this->input->get_post('envelope_id');

        $shipping_tracking = $this->envelope_shipping_tracking_m->get_by('envelope_id', $envelope_id);

        $shipping_services = null;
        if (!empty($shipping_tracking)) {
            $shipping_services = $this->shipping_services_m->get_by("id", $shipping_tracking->shipping_services_id);
        }

        $envelope = $this->envelope_m->get($envelope_id);
        //Some item have not in table envelopes, get latest status of items from history(envelope_completed)
        if (empty($envelope)) {
            $envelope = $this->envelope_completed_m->get_by_many_order(array("envelope_id" => $envelope_id), array("id" => "DESC"));
        }

        $shipping_services_available = todo_api::get_shipping_services_by_envelope($envelope);
        $customer_address = $shipping_services_available['customer_address'];

        $fullAddress = "";
        if (is_object($customer_address)) {

            if (!empty($customer_address->shipment_address_name)) {
                $fullAddress .= $customer_address->shipment_address_name . "\n ";
            }
            if (!empty($customer_address->shipment_company)) {
                $fullAddress .= $customer_address->shipment_company . "\n ";
            }
            if (!empty($customer_address->shipment_street)) {
                $fullAddress .= $customer_address->shipment_street . "\n ";
            }

            $fullAddress .= $customer_address->shipment_postcode . " ";
            $fullAddress .= $customer_address->shipment_city . "\n ";

            if (!empty($customer_address->shipment_region)) {
                $fullAddress .= $customer_address->shipment_region . "\n ";
            }
            if (!empty($customer_address->shipment_country)) {
                $country = settings_api::getCountryByID($customer_address->shipment_country);
                $fullAddress .= $country->country_name;
            }
        }
        $this->template->set('shipping_services', $shipping_services);
        $this->template->set('shipping_tracking', $shipping_tracking);
        $this->template->set('fullAddress', $fullAddress);
        $this->template->build('completed/view_detail_items');
    }

    /*
     *  Des: Save info Item on check Item page
     */

    public function save_item_info() {
        #1058 add multi dimension capability for admin
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        $length_unit = APUtils::get_length_unit_in_user_profiles();
        $weight_unit = APUtils::get_weight_unit_in_user_profiles();

        if ($this->is_ajax_request()) {

            $data['weight'] = $this->input->post('item_weight');
            $data['height'] = $this->input->post('item_height');
            $data['width'] = $this->input->post('item_width');

            $data['length'] = $this->input->post('item_length');
            $data['envelope_id'] = (int) $this->input->post('item_update_id');
            $data['from_customer_name'] = $this->input->post('item_from');

            $data['tracking_number'] = $this->input->post('tracking_number');
            $data['shipping_services'] = $this->input->post('shipping_services');

            $result_update = completed_api::save_item_info($data);

            if ($result_update) {

                $this->success_output("");
                return;
            } else {
                $this->error_output('');
                return;
            }
        }
    }

    /**
     * Get item information in check item page.
     */
    public function check_item() {

        $envelope_code = $this->input->get_post("item_id", '');

        #1058 add multi dimension capability for admin
        $date_format = APUtils::get_date_format_in_user_profiles();
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        $length_unit = APUtils::get_length_unit_in_user_profiles(FALSE);
        $weight_unit = APUtils::get_weight_unit_in_user_profiles(FALSE);

        if ($this->is_ajax_request()) {
            //Get item info
            $result = completed_api::check_item($envelope_code);

            if ($result['status']) {

                $this->success_output("", $result['message']);
            } else {
                $this->error_output($result['message']);
            }
            $envelope = $result['envelope'];
            $envelope_completed = $result['envelope_completed'];
            $this->template->set("envelope", $envelope);
            $this->template->set("envelope_completed", $envelope_completed);
            exit();
        }

        $this->template->set("envelope_code", $envelope_code);
        $this->template->set("date_format", $date_format);
        $this->template->set("decimal_separator", $decimal_separator);
        $this->template->set("length_unit", $length_unit);
        $this->template->set("weight_unit", $weight_unit);

        // Display the current page
        $this->template->build('completed/check_item');
    }

    /**
     * Get item activity in check item page.
     */
    public function search_complated_activities_check_item() {
        $envelope_code = $this->input->get_post("item_id", '');

        if ($this->is_ajax_request()) {
            // update limit into user_paging.
            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging ['limit'] = $limit;
            //Get item activity
            $response = completed_api::search_complated_activities_check_item($envelope_code, $input_paging);
            echo json_encode($response['web_activities_check_item']);
        } else {

            $this->template->set('header_title', lang('header:list_group_title'))->build('admin/index');
        }
    }

    /**
     * export pdf.
     *
     * @param unknown_type $target_date
     */
    public function export_customs_pdf_invoice() {
        $this->load->library('invoices/export');
        $envelope_id = $this->input->get_post("envelope_id");
        $invoice_file_path = $this->export->export_custom_invoice($envelope_id);
        readfile($invoice_file_path);
    }

    /**
     * Request to export customs pdf file
     */
    public function request_export_customs_pdf_invoice() {
        $this->load->library('invoices/export');
        $envelope_id = $this->input->get_post("envelope_id");
        $view_flag = $this->input->get_post('view');
        $custom_file_path = $this->export->export_custom_invoice($envelope_id, array(), false);
        if ($view_flag == 1) {
            header('Content-Disposition: inline');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($custom_file_path));
            header('Accept-Ranges: bytes');
            header('Content-Type: application/pdf');

            readfile($custom_file_path);
        } else {
            $this->success_output('The pdf file has been created successfully.');
        }
    }

    /**
     * View pdf invoice file
     */
    public function view_customs_pdf_invoice() {
        $envelope_id = $this->input->get_post("envelope_id");
        $custom_file_path = $this->export->export_custom_invoice($envelope_id);

        // Get extends file
        header('Content-Disposition: inline');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($custom_file_path));
        header('Accept-Ranges: bytes');
        header('Content-Type: application/pdf');

        readfile($custom_file_path);
    }

    public function disable_prepayment() {

        $envelop_id = $this->input->get_post('id');

        $customer_id = APContext::getCustomerCodeLoggedIn();

        completed_api::disable_prepayment($envelop_id, $customer_id);

        $this->success_output("");
    }

    /**
     * cancel request.
     */
    public function cancel_request() {

        $envelope_id = $this->input->get_post('id', '');
        $type = $this->input->get_post('type', '');

        $response = completed_api::cancel_request($envelope_id, $type);

        if ($response['status']) {

            $this->success_output("success", '');
        } else {

            $this->error_output($response['message']);
        }
    }

    /**
     * Display shipment list
     */
    public function shipment_list() {
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
        $location_id = $this->input->get_post("location_available_id", "");
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

        $selected_location = null;
        foreach ($locations as $item_location) {
            if ($item_location->id == $location_id) {
                $selected_location = $item_location;
            }
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

        // get all shipping apis
        $shipping_api = $this->shipping_apis_m->get_all();

        $account_no = $this->input->get_post('account_no');

        $this->template->set("shipping_api", $shipping_api);
        $this->template->set("account_no", $account_no);
        $this->template->set("location_id", $location_id);
        $this->template->set("list_month", $list_month);
        $this->template->set("list_year", $list_year);
        // Display the current page
        $this->template->build('completed/shipment_list');
    }

    /**
     * search shipping list.
     */
    public function search_shipping_list() {
        if ($this->is_ajax_request() && $_POST) {

            $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
            $currency_short = APUtils::get_currency_short_in_user_profiles();
            //$currency_sign = APUtils::get_currency_sign_in_user_profiles();
            $currency_rate = APUtils::get_currency_rate_in_user_profiles();
            $date_format = APUtils::get_date_format_in_user_profiles();
            $weight_unit = APUtils::get_weight_unit_in_user_profiles();


            // update limit into user_paging.
            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging ['limit'] = $limit;

            // load list access location.
            $locations_access = APUtils::loadListAccessLocation();

            // check array condition.
            $enquiry = APUtils::sanitizing($this->input->get_post('enquiry'));
            $location_id = $this->input->get_post('location_available_id');
            $account_no = $this->input->get_post('account_no');
            $year = $this->input->get_post('year');
            $month = $this->input->get_post('month');
            if (empty($year) || empty($month)) {
                $report_month = APUtils::getCurrentYearMonth();
            } else {
                $report_month = $year . $month;
            }

            if (empty($location_id)) {
                $list_filter_location_id = array();
                foreach ($locations_access as $la) {
                    $list_filter_location_id[] = $la->id;
                }
            } else {
                $list_filter_location_id = array($location_id);
            }

            $array_condition = array(
                "from_unixtime(customer_shipping_report.shipping_date, '%Y%m') = '" . $report_month . "'" => null,
            );

            $array_condition ['customer_shipping_report.location_id IN ' . "(" . implode(",", $list_filter_location_id) . ")"] = null;
            if (!empty($enquiry)) {
                $array_condition ["(c.customer_code like '%" . $enquiry . "%' OR c.email like '%" . $enquiry . "%' OR c.customer_id like '%" . $enquiry . "%' OR customer_shipping_report.tracking_number like '%" . $enquiry . "%')"] = null;
            }
            if (!empty($account_no)) {
                $array_condition ['customer_shipping_report.api_account_no'] = $account_no;
            }

            // Call search method
            $query_result = $this->customer_shipping_report_m->get_shipping_report_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

            $total = $query_result['total'];
            $datas = $query_result['data'];
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
            $i = 0;
            foreach ($datas as $row) {
                // get evelope code.
                if ($row->type == '2') {
                    // collect shipment type
                    $envelope = $this->envelope_m->get_by('package_id', $row->source_package_id);
                } else {
                    // direct shipping type.
                    $envelope = $this->envelope_m->get($row->source_package_id);
                }
                $envelope_code = $envelope->envelope_code;
                $envelope_id = $envelope->id;

                $rate = 1 + ($row->upcharge / 100);
                $weight = APUtils::view_convert_number_in_weight($row->weight, $weight_unit, $decimal_separator);
                $response->rows [$i] ['id'] = $row->id;
                $response->rows [$i] ['cell'] = array(
                    $row->id,
                    $envelope_id,
                    APUtils::viewDateFormat($row->shipping_date, $date_format),
                    $row->customer_code,
                    $envelope_code,
                    $row->email,
                    $row->carrier_name,
                    $row->service_name,
                    $row->tracking_number,
                    $row->type == 1 ? "Direct" : 'Collect',
                    $weight,
                    $row->customs_id,
                    $row->api_account_no,
                    APUtils::view_convert_number_in_currency($row->postal_charge, $currency_short, $currency_rate, $decimal_separator),
                    APUtils::view_convert_number_in_currency($row->postal_charge * $rate, $currency_short, $currency_rate, $decimal_separator),
                    $row->completed_by
                );
                $i++;
            }

            echo json_encode($response);
            return;
        }
    }

    /**
     * gets total postal charge and upcharge
     */
    public function get_total_charge() {
        $this->template->set_layout(false);
        // load list access location.
        $locations_access = APUtils::loadListAccessLocation();

        // check array condition.
        $enquiry = APUtils::sanitizing($this->input->get_post('enquiry'));
        $location_id = $this->input->get_post('location_available_id');
        $account_no = $this->input->get_post('account_no');
        $year = $this->input->get_post('year');
        $month = $this->input->get_post('month');
        if (empty($year) || empty($month)) {
            $report_month = APUtils::getCurrentYearMonth();
        } else {
            $report_month = $year . $month;
        }

        if (empty($location_id)) {
            $list_filter_location_id = array();
            foreach ($locations_access as $la) {
                $list_filter_location_id[] = $la->id;
            }
        } else {
            $list_filter_location_id = array($location_id);
        }

        $array_condition = array(
            "from_unixtime(customer_shipping_report.shipping_date, '%Y%m') = '" . $report_month . "'" => null,
        );

        $array_condition ['customer_shipping_report.location_id IN ' . "(" . implode(",", $list_filter_location_id) . ")"] = null;
        if (!empty($enquiry)) {
            $array_condition ["(c.customer_code like '%" . $enquiry . "%' OR c.email like '%" . $enquiry . "%' OR c.customer_id like '%" . $enquiry . "%' OR customer_shipping_report.tracking_number like '%" . $enquiry . "%')"] = null;
        }
        if (!empty($account_no)) {
            $array_condition ['customer_shipping_report.api_account_no'] = $account_no;
        }
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $currency_rate = APUtils::get_currency_rate_in_user_profiles();

        $row = $this->customer_shipping_report_m->get_total_charge($array_condition);
        $postal_charge = APUtils::view_convert_number_in_currency($row->postal_charge, $currency_short, $currency_rate, $decimal_separator);
        $upcharge = APUtils::view_convert_number_in_currency($row->upcharge, $currency_short, $currency_rate, $decimal_separator);
        $this->success_output('', array(
            "postal_charge" => $postal_charge,
            "upcharge" => $upcharge
        ));
        return;
    }

    //----------------------- Function private -------------------------------------------//

    private function getAccountStatus($customer) {
        $accountDuration = account_api::getAccountDuration($customer->created_date);
        $accountStatus = ($accountDuration > 1) ? "{$accountDuration} (months)" : "{$accountDuration} (month)";
        if (account_api::isActiveAccount($customer)) {
            $accountStatus .= ' - Active';
        } elseif (account_api::isInactiveAccount($customer)) {
            $accountStatus .= ' - Inactive';
            $inactiveDays = account_api::getAccountInactiveDays($customer->deactivated_date);
            if ($inactiveDays > 0) {
                if ($inactiveDays > 1) {
                    $accountStatus .= " ({$inactiveDays} days)";
                } else {
                    $accountStatus .= " ({$inactiveDays} day)";
                }
            }
        } else {
            $accountStatus .= ' - Deleted';
        }

        return $accountStatus;
    }

    private function getPostboxVerifiedStatus($envelope) {
        $verifiedStatus = mailbox_api::getPostboxVerifiedStatus($envelope->postbox_id);
        if ($verifiedStatus == APConstants::VERIFIED_STATUS_INCOMPLETE) {
            $verifiedStatus = "<span style='color: red; font-weight: bold;'>{$verifiedStatus}</span>";
        }

        return $verifiedStatus;
    }

}
