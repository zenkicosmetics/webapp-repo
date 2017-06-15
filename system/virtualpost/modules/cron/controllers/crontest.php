<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class crontest extends MY_Controller
{
    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     */
    public function __construct()
    {
        parent::__construct();

        date_default_timezone_set('CET');

        // load the theme_example view
        $this->load->library(array(
            'cron_api',
            'CronUtils',
            'CronConfigs'
        ));
    }
    
    /**
     * Using to debug individual method in LIVE
     */
    public function test_includePaypalTransactionFee() {
        $this->checkCronjobKey();
        $customer_id = $this->input->get_post('customer_id');
        
        $paypal_transaction_vat = APUtils::getVatRateOfCustomer($customer_id);
        var_dump($paypal_transaction_vat);
        
        $amount_obj = APUtils::includePaypalTransactionFee(25, $customer_id);
        var_dump($amount_obj);
    }
    
    private function checkCronjobKey()
    {
        $key = $this->input->get_post('key');
        $validKey = CronConfigs::CRON_KEY_default;

        if ($key != $validKey) {
            echo CronConfigs::EXEC_CRONJOB_INVALID_REQUEST;
            exit();
        }
    }
    
    public function generate_invoice_pdf(){
        $this->load->model('invoices/invoice_summary_m');
        $this->load->library('invoices/export');
        
        $this->checkCronjobKey();
        
        // gen lai cac invoice bi loi pdf layout: do co delcare custom fee.
        $invoices = $this->invoice_summary_m->get_many_by_many(array(
            "(custom_declaration_outgoing_quantity_01 > 0 OR custom_declaration_outgoing_quantity_02 > 0)",
            "invoice_month" => '201610',
        ));
        echo "Total count: ".count($invoices);
        echo "<br/>";
        foreach ($invoices as $invoice){
            echo "customer_id: {$invoice->customer_id}, invoice code: {$invoice->invoice_code}<br/>";
            $this->export->export_invoice($invoice->invoice_code, $invoice->customer_id);
        }
        
        echo "done";
    }
    
    public function update_wrong_code(){
        $this->load->model('mailbox/postbox_m');
        $this->load->model('scans/envelope_m');
        $this->load->model('scans/envelope_completed_m');
        
        $postboxs = $this->postbox_m->get_many_by_many(array(
            "customer_id in (32950, 32960, 32962, 33033, 33037)" => null
        ));
        
        // update postbox.
        foreach($postboxs as $p){
            echo "POSTBOX ID: ".$p->postbox_id.", postbox code: ".$p->postbox_code.", customer_id:".$p->customer_id."<br/>";
            $tmp_postbox_code = $p->postbox_code;
            $tmp = explode("_", $tmp_postbox_code);
            $new_code = 'C000'.$p->customer_id;
            $this->postbox_m->update_by_many(array(
                "postbox_id" => $p->postbox_id
            ), array(
                "postbox_code" => $new_code.'_'.$tmp[1]
            ));
            
            $envelopes = $this->envelope_m->get_many_by_many(array(
                "to_customer_id" => $p->customer_id,
                "postbox_id" => $p->postbox_id
            ));
            
            foreach($envelopes as $e){
                $tmp_code = explode('_', $e->envelope_code);
                echo "old code: ".$e->envelope_code.", ";
                $envelope_code = $new_code;
                for($i=1; $i< count($tmp_code); $i++){
                    $envelope_code .= "_".$tmp_code[$i];
                }
                echo "new code: ".$envelope_code."<br/>";
                $this->envelope_m->update_by_many(array(
                    "id" => $e->id
                ), array(
                    "envelope_code" => $envelope_code
                ));
            }
            
            $envelopes = $this->envelope_completed_m->get_many_by_many(array(
                "to_customer_id" => $p->customer_id,
                "postbox_id" => $p->postbox_id
            ));
            
            foreach($envelopes as $e){
                $tmp_code = explode('_', $e->activity_code);
                echo "old activity code: ".$e->activity_code.", ";
                $envelope_code = $new_code;
                for($i=1; $i< count($tmp_code); $i++){
                    $envelope_code .= "_".$tmp_code[$i];
                }
                echo "new activity code: ".$envelope_code."<br/>";
                $this->envelope_completed_m->update_by_many(array(
                    "id" => $e->id
                ), array(
                    "activity_code" => $envelope_code
                ));
            }
        }
        
        echo  "done";
    }
    
    /**
     * Using to debug individual method in LIVE
     */
    public function test_updatePayoneUser() {
    	$this->checkCronjobKey();
        $customer_id = $this->input->get_post('customer_id');
        if (empty($customer_id)){
        	$customer_id = 37059;
        }
    	CustomerUtils::syncEmailToPayone($customer_id);
        echo 'Update email of customers:'.$customer_id.' successfully.';
    }
    
    public function test_calculatePhoneInvoiceSummary() {
        $this->load->library('phones/phones_api');
        $customer_id = $this->input->get_post('customer_id');
        if (empty($customer_id)){
            echo "Customer ID is required";
        }
        phones_api::calculatePhoneInvoiceSummary($customer_id);
        echo "Finish";
    }

     public function test_internal_assign_phonenumber_byuser() {
        $this->load->library('account/account_api');
        $parent_customer_id = '32930';
        $customer_id = '32989';
        $phone_number = '12015006346';
        
        account_api::internal_assign_phonenumber_byuser($parent_customer_id, $customer_id, $phone_number);
        
        echo "Finish";
    }
    
    public function test_calculateInvoiceSummarybyUser(){
        $this->load->library('invoices/invoices');
        $customer_id = $this->input->get_post('customer_id');
        if (empty($customer_id)){
            echo "Customer ID is required";
            return;
        }
        // Recalculate postbox fee
        $this->invoices->cal_invoice_summary($customer_id, APUtils::getCurrentYear(), APUtils::getCurrentMonth());
        echo "Finish";
    }
    
    public function clone_pricing_from_default_template(){
        $this->load->model('price/pricing_m');
        $this->load->model('price/pricing_template_m');
        $item_name = $this->input->get_post('item_name');
        
        if (empty($item_name)) {
            echo 'Item name is required';
            return;
        }
        
        // Get all price data from default pricing
        $default_all_prices = $this->pricing_m->get_many_by_many(array(
            'pricing_template_id' => 0,
            'item_name' => $item_name
        ));
        if (count($default_all_prices) == 0) {
            echo 'Item name did not exist.';
            return;
        }
        
        $all_templates = $this->pricing_template_m->get_many_by_many(array(
            'id > 0' => null,
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        
        // For each data of default prices
        foreach ($default_all_prices as $price) {
            // For each template
            $array_price = APUtils::convertObjectToArray($price);
            unset($array_price['id']);
            unset($array_price['pricing_template_id']);
            
            // For each template
            foreach ($all_templates as $template) {
                $price_template_id = $template->id;
                
                $array_price['pricing_template_id'] = $price_template_id;
                $check_duplicate = $this->pricing_m->get_by_many(array(
                   'item_name' => $array_price['item_name'],
                   'account_type' => $array_price['account_type'],
                   'pricing_template_id' => $array_price['pricing_template_id'],
                ));
                
                if (empty($check_duplicate)) {
                    $this->pricing_m->insert($array_price);
                }
            }
        }
        
        echo "Finish";
        
    }
    
    public function test_auto_calculate_recurring_fee_phone_number(){
        $this->load->library("phones/phones_api");
        
        // Extend expired contract
        phones_api::autoExtendPhoneNumberContract();
        
        // Calculate recurring fee
        phones_api::autoCalculateRecurringFeePhoneNumber();
    }
    
    /**
     * sync call history from sontel of all account.
     * @return boolean
     */
    public function test_sync_callhistory_sontel(){
        // Load model
        $this->load->model('phones/phone_customer_subaccount_m');
        $this->load->library('phones/phones_api');

        // Only process if this is first day of month
        $customers = $this->phone_customer_subaccount_m->get_all();
        $message = "<h3>List data synchronized:</h3><br/>";
        foreach($customers as $customer){
            $account_id = $customer->account_id;
            $customer_id= $customer->customer_id;
            try{
                $total_record = phones_api::getCallHistoryFromSontel($customer_id, $account_id);
                $message .= "<div>Customer_id: ".$customer_id.", total record sync: ".$total_record."</div>";
            }catch(ThirdPartyException $e){
                $message .= "<div> can not sync Customer_id: ".$customer_id."</div>";
                log_audit_message('error', 'can not sync phone call history of Customer_id:' . $customer_id.", message: ".$e->getMessage() , false, 'sync_callhistory_sontel');
            } catch (DAOException $e1){
                $message .= "<div> can not sync Customer_id: ".$customer_id."</div>";
                log_audit_message('error', 'can not sync phone call history of Customer_id:' . $customer_id.", message: ".$e1->getMessage() , false, 'sync_callhistory_sontel');
            }
        }
        
        echo $message;
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }
    
    public function test_google_ocr() {
        $this->load->library('GoogleOCR');
        $content = GoogleOCR::getTextContent('C:\clevvermail\ImageData0.png');
        echo $content;
        
    }
    public function test_server_ocr() {
        $this->load->library('OCRUtils');
        $result = OCRUtils::convertImageToSearchablePDF('/var/www/clevvermail_webapp/shared/data/filescan/32885/ImageData0.png', '/var/www/clevvermail_webapp/shared/data/filescan/32885/ImageData0PDF');
        var_dump($result);
        OCRUtils::convertPDFToSearchable('/var/www/clevvermail_webapp/shared/data/filescan/32885/ImageData0.pdf', '/var/www/clevvermail_webapp/shared/data/filescan/32885/ImageData1PDF.pdf');
        echo 'Finish';
    }
    
    public function test1(){
        $this->load->model('scans/envelope_m');
        
        $el = $this->envelope_m->get("25");
        
        if($el->completed_by === 0){
            echo "haha: ". $el->completed_by;
        }else{
            echo "right";
        }
    }
    
    /**
     * update deleted number on location report.
     */
    public function updateDeletedNumberLocationReport(){
        $this->load->model(array(
            "addresses/location_m",
            "invoices/invoice_summary_total_by_location_m",
            "report/report_by_location_m",
            "report/report_by_total_m",
        ));
        
        $this->load->library('mailbox/mailbox_api');
        
        $target_month = array("201701", "201702", "201703", "201704");
        
        foreach($target_month as $ym){
            echo $ym."<br/>";
            $customer_count = mailbox_api::countCustomersRegistrationByMonth($ym, '');
            $this->report_by_total_m->update_by_many(array(
                "invoice_month" => $ym
            ), array(
                "never_activated_deleted" => $customer_count["number_never_activated_deleted"],
                "manually_deleted" => $customer_count["number_manually_deleted"],
                "automatically_deleted" => $customer_count["number_automatic_deleted"]
            ));
            
            $invoices = $this->report_by_location_m->get_many_by_many(array(
                "invoice_month" => $ym
            ));
            foreach($invoices as $invoice){
                $location_id = $invoice->location_id;
                if(empty($location_id)){
                    continue;
                }
                $customerNumbers = mailbox_api::countCustomersRegistrationByMonth($ym, $location_id);
                $this->report_by_location_m->update_by_many(array(
                    "location_id" => $location_id,
                    "invoice_month" => $ym
                ), array(
                    "number_of_never_activated_deleted_share" => $customerNumbers["number_never_activated_deleted"],
                    "number_of_manual_deleted_share" => $customerNumbers["number_manually_deleted"],
                    "number_of_automatic_deleted_share" => $customerNumbers["number_automatic_deleted"]
                ));
            }
                    
            echo "<br/>";
        }
        
        echo "done";
    }
    
}