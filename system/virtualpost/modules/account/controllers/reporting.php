<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Reporting extends AccountSetting_Controller {
    
    
    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     *
     * @todo Document properly please.
     */
    public function __construct() {
        parent::__construct();

        $this->load->library('form_validation');
        $this->load->library('encrypt');

        // load the theme_example view
        $this->load->model(array(
            'customers/customer_m',
            'customers/customer_setting_m',
            'invoices/invoice_summary_m',
            'invoices/invoice_summary_by_user_m',
            'invoices/invoice_detail_m',
            'email/email_m'
        ));
        
        // load library
        $this->load->library(array(
            'account/account_api',
        ));
        
        // load lang
        $this->lang->load(array(
            'account_setting'
        ));
        
        if(!APContext::isPrimaryCustomerUser() && !APContext::isAdminCustomerUser()){
            redirect('account');
        }
    }

    /**
     * Index Page for this controller.
     */
    public function index() {
        // get customer login
        $customer_id = APContext::getParentCustomerCodeLoggedIn();
        
        $currency = $this->customer_m->get_standard_setting_currency($customer_id);
        $decimal_separator = $this->customer_m->get_standard_setting_decimal_separator($customer_id);
        if (empty($currency)) {
            $currency = $this->currencies_m->get_by(array('currency_short' => 'EUR'));
        }
        $this->template->set('currency', $currency);
        $this->template->set('decimal_separator', $decimal_separator);
        
        // load invoice.
        $this->load_invoice($customer_id);
        
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
        
        // get selected year, month
        $select_year = $this->input->get_post('year');
        $select_month = $this->input->get_post('month');
        if(empty($select_year)){
            $select_year = APUtils::getCurrentYear();
        }
        if(empty($select_month)){
            $select_month = APUtils::getCurrentMonth();
        }
        
        $this->template->set("customer_id", $customer_id);
        $this->template->set("select_year", $select_year);
        $this->template->set("select_month", $select_month);
        $this->template->set("list_month", $list_month);
        $this->template->set("list_year", $list_year);
        $this->template->build('reporting/index');
    }
    
    /**
     * load current invoice.
     */
    public function load_current_invoice(){
        $customer = APContext::getParentCustomerLoggedIn();
        
        // Get standard settings of currency & decimal separator
        $currency = ci()->customer_m->get_standard_setting_currency($customer->customer_id);
        $decimal_separator = ci()->customer_m->get_standard_setting_decimal_separator($customer->customer_id);
        $currency_rate = $currency->currency_rate;
        
        if ($this->is_ajax_request()) {
            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getPagingSetting();
            
            // get input
            $year = $this->input->get_post('year');
            $month = $this->input->get_post('month');
            $enquiry = APUtils::sanitizing($this->input->get_post('currentActivity'));
            if(empty($year)){
                $year = APUtils::getCurrentYear();
            }
            if(empty($month)){
                $month = APUtils::getCurrentMonth();
            }
            
            // Gest setting list
            $customer_id = $customer->customer_id;
            $customer_setting = AccountSetting::get_list_setting_by($customer->parent_customer_id);
            
            // gets list user of enterprise customer.
            $list_id = $this->get_list_customers_enterprise($customer->customer_id).",".$customer_id;

            // Get paging input
            $input_paging = $this->get_paging_input();
            
            // get result
            $yearmonth = $year.$month;
            $datas = $this->invoice_detail_m->get_account_paging($yearmonth, $list_id, $enquiry, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);
            $total = $datas['total'];
            $rows = $datas['data'];
            
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
            $i = 0;
            foreach ($rows as $row) {
                $response->rows[$i]['cell'] = array(
                    $i + 1,
                    $row->user_name,
                    $row->email,
                    $row->activity,
                    APUtils::displayDate($row->activity_date),
                    $row->item_amount > 0 ? APUtils::convert_currency($row->item_amount, $currency_rate, 2, $decimal_separator) : "Included",
                );
                $i++;
            }
            echo json_encode($response);
            return;
        }
    }
    
    /**
     * load list invoice.
     */
    public function load_list_customer_invoice(){
        $customer = APContext::getParentCustomerLoggedIn();
        // Get standard settings of currency & decimal separator
        $currency = ci()->customer_m->get_standard_setting_currency($customer->customer_id);
        $decimal_separator = ci()->customer_m->get_standard_setting_decimal_separator($customer->customer_id);
        $currency_rate = $currency->currency_rate;
        
        if ($this->is_ajax_request()) {
            // get input
            $year = $this->input->get_post('year');
            $month = $this->input->get_post('month');
            $enquiry = APUtils::sanitizing($this->input->get_post('currentActivity'));
            if(empty($year)){
                $year = APUtils::getCurrentYear();
            }
            if(empty($month)){
                $month = APUtils::getCurrentMonth();
            }
            
            // condition
            $array_condition = array();
            
            // gets list user of enterprise customer.
            $list_id = $this->get_list_customers_enterprise($customer->customer_id);
            $array_condition['invoice_summary_by_user.customer_id IN ('.$list_id.')'] = null;
            if(!empty($enquiry)){
                $array_condition["(c.user_name like '".$enquiry."' OR c.email like '%".$enquiry."%')"] = null;
            }
            $array_condition["LEFT(invoice_summary_by_user.invoice_month, 6) = '".$year.$month."'"] = null;
            
            // Get paging input
            $input_paging = $this->get_paging_input();

            // get result
            $datas = $this->invoice_summary_by_user_m->get_invoice_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);
            $total = $datas['total'];

            // don't show invoice of current month.
            if($year == APUtils::getCurrentYear() && $month == APUtils::getCurrentMonth()){
                $rows = array();
            }else{
                $rows = $datas['data'];
            }
            //$rows = $datas['data'];
            
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
            $i = 0;
            foreach ($rows as $row) {
                if(abs($row->total_invoice) < 0.005){
                    continue;
                }
                
                $invoice_date = ($row->invoice_type == '2') ? APUtils::displayDate($row->invoice_month) : APUtils::displayDate(APUtils::getLastDayOfMonth($row->invoice_month));
                $response->rows[$i]['cell'] = array(
                    $row->total_invoice > 0? "Invoice" : "Credit Note",
                    $row->customer_id,
                    $row->id,
                    $i + 1,
                    $row->user_name,
                    $row->email,
                    $invoice_date,
                    APUtils::convert_currency($row->total_invoice, $currency_rate, 2, $decimal_separator),
                    $row->invoice_code,
                    $row->send_invoice_flag
                );
                $i++;
            }
            echo json_encode($response);
            return;
        }
    }
    
    /**
     * send invoice report to customer.
     */
    public function send_invoice_report(){
        $this->load->library(array(
            'invoices/export'
        ));
        
        $this->template->set_layout(false);
        if($_POST){
            // Gets params
            $customer_id = $this->input->post('customer_id');
            $invoice_summary_id = $this->input->post('invoice_summary_id');
            
            if(empty($customer_id) || empty($invoice_summary_id)){
                $this->error_output("This invoice does not exist.");
                return;
            }
            
            // Gest customer
            $customer = APContext::getCustomerByID($customer_id);
            
            // get invoice summary
            $invoice_summary = $this->invoice_summary_m->get_by_many(array(
                "customer_id" => $customer_id,
                "id" => $invoice_summary_id
            ));
            if(empty($invoice_summary)){
                $this->error_output("This invoice does not exist.");
                return;
            }
            
            $invoice_code = $invoice_summary->invoice_code;
            
            // Check export file.
            $file_export  = $invoice_summary->invoice_file_path;
            if(!file_exists($file_export)){
                $file_export = $this->export->export_invoice_user($invoice_code, $customer_id);
            }
            $data = array(
                "slug" => APConstants::email_invoices_report_by_monthly,
                "to_email" => $customer->email,
                // Replace content
                "full_name" => $customer->user_name,
                'invoice_id' => $invoice_code,
                'attachments' => array(
                    'file' => $file_export
                )
            );

            // Send email
            MailUtils::sendEmailByTemplate($data);
            
            // Update 2 st payment flag
            $this->invoice_summary_m->update_by_many( array(
                'customer_id' => $customer_id,
                'id' => $invoice_summary_id
            ), array(
                'send_invoice_flag' => '1',
                'send_invoice_date' => now()
            ));
            
            $this->invoice_summary_by_user_m->update_by_many( array(
                'customer_id' => $customer_id,
                'id' => $invoice_summary_id
            ), array(
                'send_invoice_flag' => '1',
                'send_invoice_date' => now()
            ));
            //log_audit_message('error', 'send success manually by enterprise customer:' . $file_export . '<br/>', false, 'send-invoice');
            $this->success_output("");
        }
    }
    
    

    // ============================= PRIVATE FUNCTION =================================================
    /**
     * load invoices.
     */
    private function load_invoice($customer_id) {
        // todo: phone open balance.
        $phone_balance = 0;
        $this->template->set("phone_balance", $phone_balance);
        
        // get open balance.
        $open_balance = APUtils::getAdjustOpenBalanceDue($customer_id);
        $this->template->set("open_balance", $open_balance);
        
        // Gets list users of enterprise customer.
        $list_id = $this->get_list_customers_enterprise($customer_id);
        $list_id .= ",".$customer_id;
        
        $postbox_fee = $this->invoice_summary_m->get_postbox_fee_by($list_id, APUtils::getCurrentYearMonth());
        
        // Gets vat of enteprrise customer
        //$vat = APUtils::getVatRateOfCustomer($customer_id);
        //$rate = !empty($vat->rate)? $vat->rate : 0;
        
        $total_postbox_fee = ($postbox_fee->free_postboxes_amount + $postbox_fee->private_postboxes_amount + $postbox_fee->business_postboxes_amount);
        $total_invoice = $postbox_fee->total_invoice;
        $this->template->set("total_postbox_fee", $total_postbox_fee);
        $this->template->set("total_invoice", $total_invoice);
    }
    
    private function get_list_customers_enterprise($customer_id){
        $list_customers = $this->customer_m->get_many_by_many(array(
            "parent_customer_id" => $customer_id
        ));
        $list_customer_id = array();
        foreach($list_customers as $customer){
            $list_customer_id [] = $customer->customer_id;
        }
        //$list_customer_id = account_api::getListUserIdOfCustomer($customer_id);
        $list_id = implode(',', $list_customer_id);
        return $list_id;
    }
    
    private function get_setting_by_activity($customer_setting, $activity_type, $alias=''){
        $result = 0;
        $key = "";
        switch ($activity_type){
            case "1":
                $key = "additional_incomming_items";
                break;
            case "2":
                $key = "envelop_scanning";
                break;
            case "3":
                $key = "opening_scanning";
                break;
            case "4":
                // direct shipping
                $key = "";
                break;
            case "5":
                // collect shipping
                $key = "";
                break;
            case "6":
                $key = "additional_included_page_opening_scanning";
                break;
            case "7":
                $key = "custom_declaration_outgoing_01";
                break;
            case "8":
                $key = "custom_declaration_outgoing_02";
                break;
        }
        foreach($customer_setting as $row){
            if(!empty($alias) &&( $row->alias  == 'all' || $row->alias == $alias)){
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