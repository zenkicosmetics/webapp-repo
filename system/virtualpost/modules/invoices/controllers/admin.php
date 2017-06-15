<?php defined('BASEPATH') or exit('No direct script access allowed');

class admin extends Admin_Controller {
    
    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     *
     * @todo Document properly please.
     */
    public function __construct() {
        parent::__construct();
        // Load the required classes
        $this->load->model(array(
            'customers/customer_m',
            'invoices/invoice_summary_m',
            'invoices/invoice_summary_by_location_m'
        ));

        $this->load->library(array(
            "invoices/invoices_api",
        ));

        $this->load->library('form_validation');
        
        // Load file lang
        $this->lang->load('invoices');
    }

    /**
     * #914 NEW-FEATURE: Develop a function in Admin site to support deleting Invoice/Payment of Deleted customer 
     */
    public function index() {
        
    }
    
    /**
     * #914 NEW-FEATURE: Develop a function in Admin site to support deleting Invoice/Payment of Deleted customer
     * Delete invoices
     */
    public function delete_invoice()
    {
        $invoice_id = $this->input->get_post("invoice_id");
        $customer_id= $this->input->get_post("customer_id");
        
        // check user is super admin
        if(!APContext::isSupperAdminUser()){
             $this->error_output('does not delete to invoice');
        }
        
        // try-catch for delete payment 
        try{
            invoices_api::deleteInvoice($invoice_id, $customer_id);
            $message = sprintf(lang('invoice.delete_success'));
            $this->success_output($message);
        }catch (BusinessException $e){
            $this->error_output($e->getmessage());
        }
        
        return;
    }
    
    /**
     * #914 NEW-FEATURE: Develop a function in Admin site to support deleting Invoice/Payment of Deleted customer
     * Delete invoices
     */
    public function delete_payment()
    {
        $transaction_id = $this->input->get_post("transaction_id");
        $customer_id= $this->input->get_post("customer_id");
        $tran_type= $this->input->get_post("tran_type");
        
        // check user is super admin
        if(!APContext::isSupperAdminUser()){
             $this->error_output('does not delete to payment');
        }
        
        // try-catch for delete payment 
        try{
            invoices_api::deletePayment($transaction_id, $customer_id, $tran_type);
            $message = sprintf(lang('payment.delete_success'));
            $this->success_output($message);
        }catch (BusinessException $e){
            $this->error_output($e->getmessage());
        }
        
        return;
    }
    
    /**
     * list credit note by location and month.
     */
    public function list_creditnote_by_location(){
        $this->template->set_layout(false);
        
        $location_id = $this->input->get_post('location_id');
        $yearmonth = $this->input->get_post('ym');
        
        if(empty($yearmonth)){
            $yearmonth = APUtils::getCurrentYearMonth();
        }
        
        if($_POST){
            $currency_short = APUtils::get_currency_short_in_user_profiles();
            $currency_rate = APUtils::get_currency_rate_in_user_profiles();
            $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
            $date_format = APUtils::get_date_format_in_user_profiles();
        
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);
            
            // Gets input paging 
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;

            // do get list credit note.
            if (empty($location_id)) {
                $array_condition = array(
                    "LEFT(invoice_summary.invoice_month,6)='".$yearmonth."'" => null,
                    "invoice_summary.total_invoice < 0" => null,
                    "invoice_summary.invoice_type" => "2"
                );
                // Gets credit note for total line.
                $query_result = $this->invoice_summary_m->get_creditnote_paging($array_condition, $input_paging['start'], $input_paging['limit']
                        , $input_paging['sort_column'], $input_paging['sort_type']);
            } else {
                $array_condition = array(
                    "LEFT(invoice_summary_by_location.invoice_month,6)='".$yearmonth."'" => null,
                    "invoice_summary_by_location.location_id" => $location_id,
                    "invoice_summary_by_location.total_invoice < 0" => null,
                    "invoice_summary_by_location.invoice_type" => "2"
                );
                
                // Gets credit note for location line.
                $query_result = $this->invoice_summary_by_location_m->get_creditnote_paging($array_condition, $input_paging['start']
                        , $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);
            }

            // Process output data
            $total = $query_result['total'];
            $datas = $query_result['data'];

            $i = 0;
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);
            foreach ($datas as $row) {
                $status = '';
                if($row->status == APConstants::ON_FLAG){
                    $status = "Deleted";
                }else{
                    if($row->activated_flag == APConstants::ON_FLAG){
                        $status = "Activated";
                    }else if ($row->deactivated_type == 'auto'){
                        $status = "Auto-deactivated";
                    }else if ($row->deactivated_type == 'manual'){
                        $status = "Manual-deactivated";
                    }else{
                        $status = "Not activated";
                    }
                }
                
                // get invoice code from invoice_sumary
                if(empty($row->invoice_code)){
                    $invoice_summary = $this->invoice_summary_m->get_by_many(array(
                        "customer_id" => $row->customer_id,
                        "invoice_month" => $row->invoice_month,
                        "invoice_type" => $row->invoice_type
                    ));
                    if($invoice_summary){
                        $row->invoice_code = $invoice_summary->invoice_code;
                    }
                }
                
                $response->rows[$i]['id'] = $row->id;
                $response->rows[$i]['cell'] = array(
                    $row->customer_id,
                    $row->customer_code,
                    $row->user_name,
                    $row->email,
                    $status,
                    $row->invoice_code,
                    APUtils::convertDateFormatFrom($row->invoice_month),
                    APUtils::view_convert_number_in_currency($row->total_invoice, $currency_short, $currency_rate, $decimal_separator),
                    $row->invoice_code
                );
                $i++;
            }
            echo json_encode($response);

            return;
        }
        
        $this->template->set("location_id", $location_id);
        $this->template->set("yearmonth", $yearmonth);
        $this->template->build('admin/list_creditnote');
        return;
    }
}