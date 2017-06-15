<?php defined('BASEPATH') or exit('No direct script access allowed');

class enterprise extends APIPartner_Controller {
    
    private  $_customer_id = 0;
    
    private $_successStatus = "000000";
    private $_noDataStatus = "000001";
    private $_pageSizeInvalidStatus = "000101";
    private $_PageNumberInvalidStatus = "000102";
    private $_invalidYMStatus = "000002";
    private $_invalidYMFutureStatus = "000003";
    private $_systemErrorStatus = "999999";
    
    private $_successMessage = "Your request processed successfully";
    private $_systemErrorMessage = "System error occurred please contact your system administrator";
    private $_nodataMessage = "No record found";
    private $_pageSizeInvalidMessage = "The page_size is invalid. The page_size should be integer and greater or equal 0";
    private $_PageNumberInvalidMessage = "The page_num is invalid. The page_num should be integer and greater or equal 1";
    
    public function __construct()
    {
        // set error repoting to false for proper formatting of json data
        parent::__construct();

        $this->load->library('Payone_lib');
        $this->load->config('payone');
        
        $this->load->model(array(
            'account/account_m',
            'phones/pricing_phones_number_m',
            'phones/pricing_phones_outboundcalls_m',
            'invoices/invoice_summary_by_user_m',
        ));

        ci()->load->library(array(
            'api/mobile_api',
            "settings/settings_api",
            "phones/phones_api"
        ));
       
        $this->lang->load(array(
            'account/account',
            'api',
        ));
    }

    /**
     * Verify Web Services are working. void
     *
     * @return array {'code' => 1000, 'message' => 'Working'}
     */
    public function index()
    {
        $data = array(
            'code' => 1000,
            'message' => 'Working',
            'result' => 'index'
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Gets phone country
     */
    public function invoices()  {
        $targetYM = $this->input->get('ym');
        $customerCode = $this->input->get('customer_code');
        
        
        if(!empty($customerCode)){
            $customerCode = intval(str_replace("C0","",$customerCode));
        }
        
        $result = array("status" => $this->_successStatus);
        try{
            // validate target month.
            $tmp_ym = (int) $targetYM;
            if(empty($targetYM) || $tmp_ym != $targetYM){
                $this->api_error_output("The month is invalid format. The correct format should be YYYYMM", array("status"=> $this->_invalidYMStatus));
            }
            $currentYM = APUtils::getCurrentYearMonth();
            if($tmp_ym > $currentYM){
                $this->api_error_output("The month is invalid. The month should be less than or equal the last month", array("status"=> $this->_invalidYMFutureStatus));
            }

            // get enterprise customer id
            $customer_id = $this->getEnterpriseCustomerId();

            // gets list customer id
            $list_customer_ids = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($customer_id);
            $list_customer_ids[] = $customer_id;

            $startDayOfMonth = APUtils::getFirstDayOfMonth($targetYM);
            $endDayOfMonth = APUtils::getLastDayOfMonth($startDayOfMonth);

            // nodata case.
            if(count($list_customer_ids) == 0){
                $result["status"] = $this->_noDataStatus;
                $this->api_success_output($this->_nodataMessage, $result);
            }
            
            
            foreach($list_customer_ids as $id){
                if(!empty($customerCode)){
                    if($customerCode != $id){
                        continue;
                    }
                }

                $user = APContext::getCustomerByID($id);

                $tmp = new stdClass();
                $tmp->customer_code = $user->customer_code;
                $tmp->customer_email = $user->email;
                $tmp->start_date = APUtils::convert_timestamp_to_date($user->created_date);
                $tmp->currency = "EUR";
                $tmp->start_invoice_date = APUtils::displayDate($startDayOfMonth);
                $tmp->end_invoice_date = APUtils::displayDate($endDayOfMonth);

                // Gets invoice summary by user
                $invoices = $this->invoice_summary_by_user_m->get_many_by_many(array(
                    "LEFT(invoice_month, 6) = ".$targetYM => null,
                    "customer_id" => $id
                ));

                $total_fee_net = 0;
                $total_upcharge_net = 0;
                $total_net = 0;

                $total_fee_gross = 0;
                $total_upcharge_gross = 0;
                $total_gross = 0;

                $list_items = array();            
                foreach($invoices as $invoice){
                    $item = new stdClass();
                    $item->vat_rate = $invoice->vat;
                    $item->currency = "EUR";

                    // incomming item
                    $item->item_id = APConstants::INCOMMING_ACTIVITY_TYPE;
                    $item->item_name = "Incomming";
                    $item->fee_net = $invoice->incomming_items_business_account - ($invoice->incomming_items_business_quantity * $invoice->additional_incomming_item_upcharge);
                    $item->upcharge_net = $invoice->incomming_items_business_quantity * $invoice->additional_incomming_item_upcharge;
                    $item->net_total = $invoice->incomming_items_business_account;
                    $item->fee_gross = $item->fee_net* (1 + $invoice->vat);
                    $item->upcharge_gross = $item->upcharge_net* (1 + $invoice->vat);
                    $item->gross_total = $item->net_total* (1 + $invoice->vat);
                    $list_items[] = $item;

                    // envelope scan 
                    $item->item_id = APConstants::ENVELOPE_SCAN_ACTIVITY_TYPE;
                    $item->item_name = "Envelope scan";
                    $item->fee_net = $invoice->envelope_scan_business_account - ($invoice->envelope_scan_business_quantity * $invoice->envelope_scan_upcharge);
                    $item->upcharge_net = $invoice->envelope_scan_business_quantity * $invoice->envelope_scan_upcharge;
                    $item->net_total = $invoice->envelope_scan_business_account;
                    $item->fee_gross = $item->fee_net* (1 + $invoice->vat);
                    $item->upcharge_gross = $item->upcharge_net* (1 + $invoice->vat);
                    $item->gross_total = $item->net_total* (1 + $invoice->vat);
                    $list_items[] = $item;

                    // document/item scan 
                    $item->item_id = APConstants::ENVELOPE_SCAN_ACTIVITY_TYPE;
                    $item->item_name = "Envelope scan";
                    $item->fee_net = $invoice->item_scan_business_account - ($invoice->item_scan_business_quantity * $invoice->item_scan_upcharge);
                    $item->upcharge_net = $invoice->item_scan_business_quantity * $invoice->item_scan_upcharge;
                    $item->net_total = $invoice->item_scan_business_account;
                    $item->fee_gross = $item->fee_net* (1 + $invoice->vat);
                    $item->upcharge_gross = $item->upcharge_net* (1 + $invoice->vat);
                    $item->gross_total = $item->net_total* (1 + $invoice->vat);
                    $list_items[] = $item;

                    // forwarding activity
                    $item->item_id = APConstants::DIRECT_SHIPPING_ACTIVITY_TYPE;
                    $item->item_name = "Shipping & Handling";
                    $item->upcharge_net = 0;//$invoice->direct_shipping_business_quantity * $invoice->item_scan_upcharge;
                    $item->fee_net = ($invoice->direct_shipping_business_account + $invoice->collect_shipping_business_account);
                    $item->net_total = ($invoice->direct_shipping_business_account + $invoice->collect_shipping_business_account);
                    $item->fee_gross = $item->fee_net* (1 + $invoice->vat);
                    $item->upcharge_gross = 0;//$item->upcharge_net* (1 + $invoice->vat);
                    $item->gross_total = $item->net_total* (1 + $invoice->vat);
                    $list_items[] = $item;

                    // additional pages scanning
                    $item->item_id = APConstants::ADDITIONAL_SCAN_ACTIVITY_TYPE;
                    $item->item_name = "Additional scanning";
                    $item->upcharge_net = $invoice->additional_pages_scanning_business_quantity * $invoice->included_page_scan_upcharge;
                    $item->fee_net = $invoice->additional_pages_scanning_business_amount - $item->upcharge_net;
                    $item->net_total = $invoice->additional_pages_scanning_business_amount;
                    $item->fee_gross = $item->fee_net* (1 + $invoice->vat);
                    $item->upcharge_gross = $item->upcharge_net* (1 + $invoice->vat);
                    $item->gross_total = $item->net_total* (1 + $invoice->vat);
                    $list_items[] = $item;

                    // storing letter fee
                    $item->item_id = APConstants::INVOICE_ACTIVITY_TYPE_STORAGE_FEE_LETTER;
                    $item->item_name = "Storing letter";
                    $item->upcharge_net = $invoice->storing_letters_business_quantity * $invoice->storing_letter_upcharge;
                    $item->fee_net = $invoice->storing_letters_business_account - $item->upcharge_net;
                    $item->net_total = $invoice->storing_letters_business_account;
                    $item->fee_gross = $item->fee_net* (1 + $invoice->vat);
                    $item->upcharge_gross = $item->upcharge_net* (1 + $invoice->vat);
                    $item->gross_total = $item->net_total* (1 + $invoice->vat);
                    $list_items[] = $item;

                    // storing package fee
                    $item->item_id = APConstants::INVOICE_ACTIVITY_TYPE_STORAGE_FEE_PACKAGE;
                    $item->item_name = "Storing package";
                    $item->upcharge_net = $invoice->storing_packages_business_quantity * $invoice->storing_package_upcharge;
                    $item->fee_net = $invoice->storing_packages_business_account - $item->upcharge_net;
                    $item->net_total = $invoice->storing_packages_business_account;
                    $item->fee_gross = $item->fee_net* (1 + $invoice->vat);
                    $item->upcharge_gross = $item->upcharge_net* (1 + $invoice->vat);
                    $item->gross_total = $item->net_total* (1 + $invoice->vat);
                    $list_items[] = $item;

                    // postbox fee
                    $item->item_id = APConstants::INVOICE_ACTIVITY_TYPE_POSTBOX_FEE;
                    $item->item_name = "Storing package";
                    $item->upcharge_net = $invoice->business_postboxes_quantity * $invoice->postbox_fee_upcharge;
                    $item->fee_net = $invoice->business_postboxes_amount - $item->upcharge_net;
                    $item->net_total = $invoice->business_postboxes_amount;
                    $item->fee_gross = $item->fee_net* (1 + $invoice->vat);
                    $item->upcharge_gross = $item->upcharge_net* (1 + $invoice->vat);
                    $item->gross_total = $item->net_total* (1 + $invoice->vat);
                    $list_items[] = $item;

                    $total_fee_net += $invoice->total_invoice;
                    $total_net += $invoice->total_invoice;
                    $total_fee_gross += $invoice->total_invoice * (1 + $invoice->vat);
                    $total_gross += $invoice->total_invoice * (1 + $invoice->vat);
                }

                $tmp->list_items = $list_items;
                $tmp->total_fee_net = $total_fee_net;
                $tmp->total_upcharge_net = $total_upcharge_net;
                $tmp->total_net = $total_net;

                $tmp->total_fee_gross = $total_fee_gross;
                $tmp->total_upcharge_gross = $total_upcharge_gross;
                $tmp->total_gross = $total_gross;
                $result[] = $tmp;
            }

            $this->api_success_output('Your request processed successfully', $result);
        }catch(Exception $ex){
            $this->api_error_output($this->_systemErrorMessage, array("status" => $this->_systemErrorStatus ));
        }
    }
    
    /**
     * get all users of enteprise account.
     */
    public function customers(){
        $customer_id = $this->getEnterpriseCustomerId();
        $pageSize = $this->input->get("page_size");
        $pageNum = $this->input->get("page_num");
        
        try{
            $result = array(
                "status" => $this->_successStatus
            );
            
            // validate page size and page numb
            $tmp_pagesize = (int) $pageSize;
            if(!empty($pageSize) && ($tmp_pagesize != $pageSize || $pageSize < 0)){
                $result["status"] = $this->_pageSizeInvalidStatus;
                $this->api_error_output($this->_pageSizeInvalidMessage, $result);
            }
            
            // validate page num
            $tmp_pagenum = (int) $pageNum;
            if(!empty($pageNum) && ($tmp_pagenum != $pageNum || $pageNum < 0)){
                $result["status"] = $this->_PageNumberInvalidStatus;
                $this->api_error_output($this->_PageNumberInvalidMessage, $result);
            }
            
            // Init paging 
            if(!$pageNum){
                $pageNum = 1;
            }
            if(!$pageSize){
                $pageSize = 10;
            }
            if($pageSize == 0){
                $pageSize = 1000;
            }
            
            // gets list customers id.
            $list_customer_ids = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($customer_id, $pageNum, $pageSize);
            $list_customer_ids[] = $customer_id;
            
            // nodata case.
            if(count($list_customer_ids) == 0){
                $result["status"] = $this->_noDataStatus;
                $this->api_success_output($this->_nodataMessage, $result);
            }
            
            foreach($list_customer_ids as $id){
                $user = APContext::getCustomerByID($id);
                $tmp = new stdClass();
                $tmp->customer_code = $user->customer_code;
                $tmp->customer_email = $user->email;
                $tmp->start_date = APUtils::convert_timestamp_to_date($user->created_date);
                $result[] = $tmp;
            }
            $this->api_success_output($this->_successMessage, $result);
        }catch(Exception $ex){
            $this->api_error_output($this->_systemErrorMessage, array("status" => $this->_systemErrorStatus ));
        }
    }

}

?>
