<?php defined('BASEPATH') or exit('No direct script access allowed');

class invoices extends AccountSetting_Controller
{
    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     *
     * Document properly please.
     */
    public function __construct()
    {
        parent::__construct();

        // load the theme_example view
        $this->load->model('invoices/invoice_detail_m');
        $this->load->model('settings/countries_m');
        $this->load->model('customers/customer_m');
        $this->load->model('invoices/invoice_summary_m');
        $this->load->model('invoices/invoice_summary_by_location_m');
        $this->load->model('payment/external_tran_hist_m');
        $this->load->model('payment/payone_tran_hist_m');
        $this->load->model('addresses/customers_address_m');
        $this->load->model('settings/currencies_m');
        $this->load->model('phones/phone_invoice_by_location_m');
        
        $this->load->library(array(
            'invoices/export',
            'customers/customers_api'
        ));

        // TODO: prevent user enteprise to access invoice page.
        $customer = APContext::getCustomerLoggedIn();
        if($customer->account_type == APConstants::ENTERPRISE_CUSTOMER && $customer->role_flag != 1 && !empty($customer->parent_customer_id)){
            redirect('/account');
            return false;
        }
        

        $this->lang->load('invoices');
    }

    /**
     * Index Page for this controller. Maps to the following URL http://example.com/index.php/welcome - or -
     * http://example.com/index.php/welcome/index - or - Since this controller is set as the default controller in config/routes.php, it's displayed
     * at http://example.com/ So any other public methods not prefixed with an underscore will map to /index.php/welcome/<method_name>
     *
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index()
    {
        $target_month = APUtils::getTargetMonthInvoice();
        $target_year = APUtils::getTargetYearInvoice();

        // load customer currency information
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $decimal_separator = $this->customer_m->get_standard_setting_decimal_separator($customer_id);
        $currency = customers_api::getStandardCurrency($customer_id);

        $this->template->set('currency', $currency);
        $this->template->set('decimal_separator', $decimal_separator);

        // load invoices
        //$this->load->library('invoices');
        //$this->invoices->cal_invoice_summary(APContext::getCustomerCodeLoggedIn(), $target_year, $target_month);
        $this->load_invoice($customer_id);

        //$customer = $this->customer_m->get_current_customer_info();
        //$this->template->set('customer', $customer);
        
        if(APContext::isPrimaryCustomerUser() || APContext::isAdminCustomerUser()){
            $list_customers = ci()->customer_m->get_many_by_many(array(
                "parent_customer_id" => $customer_id
            ));
            $list_customer_id = array($customer_id);
            foreach($list_customers as $row){
                $list_customer_id[] = $row->customer_id;
            }
            $list_id = implode(',', $list_customer_id);
        
            $adjust_open_balance = APUtils::getAdjustOpenBalanceDue($customer_id);
            $open_balance = $adjust_open_balance['OpenBalanceDue'] + InvoiceUtils::getOpenBalanceOfPhone($list_id);
            $open_balance_this_month = $adjust_open_balance['OpenBalanceThisMonth']  + InvoiceUtils::getOpenBalanceThisMonthOfPhone($list_id);
        }else{
            $adjust_open_balance = APUtils::getAdjustOpenBalanceDue($customer_id);
            $open_balance = $adjust_open_balance['OpenBalanceDue'] + InvoiceUtils::getOpenBalanceOfPhone($customer_id);
            $open_balance_this_month = $adjust_open_balance['OpenBalanceThisMonth']  + InvoiceUtils::getOpenBalanceThisMonthOfPhone($customer_id);
        }
        
        //Do not display negative open balance for customer
        $remain_charge_amount = $open_balance + $open_balance_this_month;
        
        //Customer need pay for system
        if ($remain_charge_amount >= 0) {
            //If open balance this month < 0 => open balance >0
            if ($open_balance_this_month < 0) {
                $open_balance = $remain_charge_amount;//Open balance
                $open_balance_this_month = 0;//Open balance this month
            } elseif ($open_balance < 0) {
                $open_balance = 0;//open balance
                $open_balance_this_month = $remain_charge_amount;//open balance this month
            }
        //Customer has deposit in system   
        } else {
            $open_balance = 0;//Open balance
            $open_balance_this_month = $remain_charge_amount;//Open balance this month
        }

        $this->template->set("open_balance", $open_balance);
        $this->template->set("open_balance_this_month", $open_balance_this_month);

        // Get and display message when return from paypal
        $paypal_status = $this->input->get_post('paypal_status');
        $this->template->set("paypal_status", $paypal_status);
        $paypal_message = '';
        if ($paypal_status == '1' || $paypal_status == '2') {
            $paypal_message = lang('paypal_status_' . $paypal_status);
        }
        $this->template->set("paypal_message", $paypal_message);

        $this->template->build('index');
    }

    /**
     * load invoices.
     */
    public function load_invoice($customer_id)
    {
        $isPrimaryCustomer = APContext::isPrimaryCustomerUser() || APContext::isAdminCustomerUser();
        if($isPrimaryCustomer){
            $customer_id = APContext::getParentCustomerCodeLoggedIn();
        }
        
        $next_invoices_display = InvoiceUtils::getCurrentActivitiesInvoice($customer_id, $isPrimaryCustomer);
        $this->template->set("next_invoices", $next_invoices_display);

        $next_invoice_phone = InvoiceUtils::getCurrentActivityPhone($customer_id, $isPrimaryCustomer);
        $this->template->set("next_invoice_phone", $next_invoice_phone);
    }
    
    /**
     * Activities in Current Period
     */
    public function load_current_activities()
    {
        $this->template->set_layout(false);
        $customer = APContext::getCustomerLoggedIn();
        $isPrimaryCustomer = APContext::isPrimaryCustomerUser() || APContext::isAdminCustomerUser();
        if($isPrimaryCustomer){
            $customer = APContext::getParentCustomerLoggedIn();
        }
        if ($this->is_ajax_request()) {
            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getPagingSetting();
            APContext::updatePagingSetting($limit);
            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = APContext::getPagingSetting();
            
            $list_current_invoice = InvoiceUtils::load_current_activities($customer, $input_paging, $isPrimaryCustomer);
            echo json_encode($list_current_invoice['web_current_invoices']);
            return;
        }
    }

    /**
     * Activities in Current Period
     */
    public function load_old_invoice()
    { 
        $customer = APContext::getCustomerLoggedIn();
        $customer_id = $customer->customer_id;
        $isPrimaryCustomer = APContext::isPrimaryCustomerUser() || APContext::isAdminCustomerUser();
        if($isPrimaryCustomer){
            $customer = APContext::getParentCustomerLoggedIn();
            $customer_id = $customer->customer_id;
        }
        
        // If current request is ajax
        if ($this->is_ajax_request()) {
            $limit = 100000;

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;

            // Get standard settings of currency & decimal separator
            $currency = $this->customer_m->get_standard_setting_currency($customer_id);
            $decimal_separator = $this->customer_m->get_standard_setting_decimal_separator($customer_id);
            $currency_sign = $currency->currency_sign;
            $currency_rate = $currency->currency_rate;
            
            $result = InvoiceUtils::load_old_invoice($customer, $input_paging);
        
            echo json_encode($result['web_old_invoices']);
        }
    }

    public function convert_currency()
    {
        $this->template->set_layout(FALSE);

        $converted_currency_id = $this->input->get_post('converted_currency_id');
        $base_amount = $this->input->get_post('base_amount');

        $currency = $this->currencies_m->get($converted_currency_id);
        $decimal_separator = $this->customer_m->get_standard_setting_decimal_separator();
        $currency_rate = $currency->currency_rate;
        $currency_short = $currency->currency_short;

        $convert_amount = APUtils::convert_currency($base_amount, $currency_rate, 2, $decimal_separator);
        $exchange_rate = APUtils::convert_currency($currency_rate, 1, 4, $decimal_separator) . ' ' . $currency_short . '/EUR';

        $this->success_output(lang('convert_currency_success'), array(
            'converted_amount' => $convert_amount,
            'exchange_rate' => $exchange_rate
        ));
        return;
    }

    function getPayoneStatus($txaction)
    {
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
     * Check payment exist before output pdf file
     */
    public function check_payment_exist()
    {
        // Does not use layout
        $this->template->set_layout(FALSE);

        $customer_id = APContext::getCustomerCodeLoggedIn();
        $this->load->model('payment/payment_m');
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
     * export pdf.
     *
     * @param unknown_type $target_date
     */
    public function export($invoice_code)
    {
        // Does not use layout
        $this->template->set_layout(FALSE);
        
        $type = $this->input->get_post("type");
        
        // get customer_id from request
        $customer_id = $this->input->get_post('customer_id');

        // export invoice or credit note
        $invoice_file_path = $this->export->export_invoice($invoice_code, $customer_id);

        // Get extends file
        header('Content-Disposition: inline');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($invoice_file_path));
        header('Accept-Ranges: bytes');
        header('Content-Type: application/pdf');

//         $seconds_to_cache = APConstants::CACHED_SECONDS;
//         $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
//         header("Expires: $ts");
//         header("Pragma: cache");
//         header("Cache-Control: max-age=$seconds_to_cache");

        readfile($invoice_file_path);
    }
    
    public function export_user_report($invoice_code)
    {
        // Does not use layout
        $this->template->set_layout(FALSE);

        // get customer_id from request
        $customer_id = $this->input->get_post('customer_id');
        
        if(empty($customer_id)){
            echo "not found";
            return;
        }

        // export invoice or credit note
        $invoice_file_path = $this->export->export_invoice_user($invoice_code, $customer_id);

        // Get extends file
        header('Content-Disposition: inline');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($invoice_file_path));
        header('Accept-Ranges: bytes');
        header('Content-Type: application/pdf');

//         $seconds_to_cache = APConstants::CACHED_SECONDS;
//         $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
//         header("Expires: $ts");
//         header("Pragma: cache");
//         header("Cache-Control: max-age=$seconds_to_cache");

        readfile($invoice_file_path);
    }

    /**
     * View pdf invoice file
     */
    public function view_pdf_invoice()
    {
        // Does not use layout
        $this->template->set_layout(FALSE);
        $url = $this->input->get_post('url');

        $this->template->set('pdf_file_url', $url);

        // load the theme_example view
        $this->template->build('view_pdf_invoice');
    }

    /**
     * Default page for 404 error.
     */
    public function page_construction()
    {
        // load the theme_example view
        $this->template->build('page_construction');
    }
    
    /**
     * load current activity of phone number.
     */
    public function load_current_phone_invoice(){
        $customer = APContext::getCustomerLoggedIn();
        $isPrimaryCustomer = APContext::isPrimaryCustomerUser();
        if ($this->is_ajax_request()) {
            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getPagingSetting();
            APContext::updatePagingSetting($limit);
            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = APContext::getPagingSetting();
            
            $list_current_invoice = InvoiceUtils::load_current_phone_invoice($customer,$input_paging, $isPrimaryCustomer);
            echo json_encode($list_current_invoice['web_current_invoices']);
            return;
        }
    }
    
    /**
     * Gets postbox activity.
     */
    public function get_postbox_activity(){
        $this->template->set_layout(FALSE);
        if($this->is_ajax_request()){
            
        }

        $this->template->build('postbox_activity');
    }
    
    /**
     * Gets postbox activity.
     */
    public function get_envelope_scan_activity(){
        $this->template->set_layout(FALSE);
        
        $this->template->set('type', 2);
        $this->template->build('show_envelope_activity');
    }
    
    /**
     * Gets postbox activity.
     */
    public function get_item_scan_activity(){
        $this->template->set_layout(FALSE);
        
        $this->template->set('type', 3);
        $this->template->build('show_envelope_activity');
    }
    
    /**
     * Gets postbox activity.
     */
    public function get_additional_item_activity(){
        $this->template->set_layout(FALSE);
        
        $this->template->set('type', 1);
        $this->template->build('show_envelope_activity');
    }
    
    /**
     * Gets postbox activity.
     */
    public function get_additional_pages_activity(){
        $this->template->set_layout(FALSE);
        
        $this->template->set('type', 6);
        $this->template->build('show_envelope_activity');
    }
    
    /**
     * Gets postbox activity.
     */
    public function get_shipping_activity(){
        $this->template->set_layout(FALSE);
        
        $this->template->set('type', 4);
        $this->template->build('show_envelope_activity');
    }
    
    /**
     * Gets postbox activity.
     */
    public function get_storing_activity(){
        $this->template->set_layout(FALSE);
        
        if($this->is_ajax_request() && $_POST){
            
            $this->load->model('scans/envelope_m');
            $this->load->library('price/price_api');
            
            $customer = APContext::getParentCustomerLoggedIn();
 
            $customer_list = array();
            if(APContext::isEnterpriseCustomer()){
                $customer_list = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($customer->customer_id);
            }
            $customer_list[] = $customer->customer_id;
            
            // update limit into user_paging.
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);
            
            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;
            
            // get list 
            $array_condition = array();
            $array_condition["envelopes.to_customer_id IN (" . implode(",", $customer_list) . ") "] = null;
            $array_condition['p.completed_delete_flag <> '] = APConstants::ON_FLAG;
            $array_condition['envelopes.current_storage_charge_fee_day > '] = '0';

            $query_result = $this->envelope_m->get_envelope_paging_storage_fee($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);
            
            // Get standard settings of currency & decimal separator
            $customer_id = APContext::getCustomerCodeLoggedIn();
            $currency = ci()->customer_m->get_standard_setting_currency($customer_id);
            $decimal_separator = ci()->customer_m->get_standard_setting_decimal_separator($customer_id);
            $currency_sign = $currency->currency_sign;
            $currency_rate = $currency->currency_rate;
            
            // Process output data
            $total = $query_result['total'];
            $datas = $query_result['data'];
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);
            $i = 0;
            foreach ($datas as $row) {
                $postbox_type = $row->type;
                if (empty($postbox_type)) {
                    $postbox_type = APConstants::FREE_TYPE;
                }
                $location_id = $row->location_id;
            
                // Get price model
                // $price_postbox = price_api::getPricingModelByLocationID($location_id, $postbox_type);
                $pricing_map = price_api::getPricingModelByCusotomerAndLocationID($customer_id, $location_id);
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
                if ($row->trash_date > 0 && 
                        ($row->trash_flag == APConstants::ENVELOPE_COMPLETED_TRASH_BY_ADMIN 
                        || $row->trash_flag == APConstants::OFF_FLAG 
                        || $row->trash_flag == APConstants::ENVELOPE_TRASH_BY_CUSTOMER_IN_TRASH_FOLDER 
                        || $row->trash_flag == APConstants::ON_FLAG )) {
                    $trashed_on = $row->trash_date;
                }

                $send_out_on = 0;
                if ($row->direct_shipping_date > 0 && $row->direct_shipping_flag == APConstants::ON_FLAG) {
                    $send_out_on = $row->direct_shipping_date;
                } else
                if ($row->collect_shipping_date > 0 && $row->collect_shipping_flag == APConstants::ON_FLAG) {
                    $send_out_on = $row->collect_shipping_date;
                }
            
                $response->rows[$i]['id'] = $row->id;
                $response->rows[$i]['cell'] = array(
                    $row->from_customer_name,
                    $row->envelope_code,
                    $row->customer_code,
                    $envelope_type,
                    APUtils::convert_timestamp_to_date($row->incomming_date),
                    $send_out_on > 0 ? APUtils::convert_timestamp_to_date($send_out_on) : '',
                    $trashed_on > 0 ? APUtils::convert_timestamp_to_date($trashed_on) : '',
                    $row->previous_storage_charge_fee_day,
                    $row->current_storage_charge_fee_day,
                    APUtils::convert_currency($price, $currency_rate, 2, $decimal_separator),
                    APUtils::convert_currency(($price * $row->current_storage_charge_fee_day), $currency_rate, 2, $decimal_separator),
                );
                $i ++;
            }
            echo json_encode($response);
            return;
        }
        
        $this->template->build('show_storing_activity');
    }
    
    /**
     * get detail activity.
     * @return type
     */
    public function get_detail_activity(){
        $type = $this->input->get_post('type');
        if($this->is_ajax_request() && $_POST){
            $customer = APContext::getParentCustomerLoggedIn();
 
            $customer_list = array();
            if(APContext::isEnterpriseCustomer()){
                $customer_list = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($customer->customer_id);
            }
            $customer_list[] = $customer->customer_id;

            // update limit into user_paging.
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);
            
            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;
            
            // get list 
            $array_condition = array(
                "LEFT(invoice_detail.activity_date, 6) = ". APUtils::getCurrentYearMonth() => null
            );
            //if($type == 1 || $type == 6){
            $array_condition['invoice_detail.item_amount > 0'] = null;
            //}
            
            if($type == 4){
                $array_condition['invoice_detail.activity_type IN (4,5,8)'] = null;
            }else{
                $array_condition['invoice_detail.activity_type'] = $type;
            }
            $array_condition['invoice_detail.customer_id IN ('.implode(",", $customer_list).')'] = null; 
            $list_details = $this->invoice_detail_m->get_invoice_detail_paging($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);
            
            // Get standard settings of currency & decimal separator
            $customer_id = APContext::getCustomerCodeLoggedIn();
            $currency = ci()->customer_m->get_standard_setting_currency($customer_id);
            $decimal_separator = ci()->customer_m->get_standard_setting_decimal_separator($customer_id);
            $currency_sign = $currency->currency_sign;
            $currency_rate = $currency->currency_rate;
            
            // Process output data
            $total = $list_details['total'];
            $datas = $list_details['data'];
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);
            $i = 0;
            foreach ($datas as $row) {
                $response->rows[$i]['id'] = $row->id;
                $response->rows[$i]['cell'] = array(
                    $row->from_customer_name,
                    $row->envelope_code,
                    $row->customer_code,
                    $row->location_name,
                    $row->activity,
                    APUtils::convert_currency($row->item_amount, $currency_rate, 2, $decimal_separator),
                );
                $i ++;
            }
            echo json_encode($response);
            return;
        }
    }


    
}