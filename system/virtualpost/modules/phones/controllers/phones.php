<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Phones extends Phone_Controller {

    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     */
    public function __construct() {
        parent::__construct();
        $this->load->library("pagination");
        $this->load->library('phones/phones_api');
    }

    public function index() {
        $customer_id = APContext::getCustomerCodeLoggedIn();
        
        // load call history
        $this->load_call_history($customer_id);
        $this->template->build('index');
    }

    /**
     * Load envelope information based on input parameters.
     */
    private function load_call_history($customer_id) {
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $base_url = base_url() . 'phones/index';
        // Get post box id
        $user_id = $this->input->get_post('u');
        if (empty($user_id)) {
            $user = $this->get_first_user();
            if ($user != null) {
                $user_id = $user->customer_id;
                $phone_number = $this->get_first_phonnumber_byuser($user);
                if (!empty($user)) {
                    redirect('phones/' . $this->method . '?u=' . $user_id.'&phone_number='.$phone_number);
                }
            }
        }
        $phone_number = $this->input->get_post('phone_number');
        if (empty($phone_number)) {
            $customer = CustomerUtils::getCustomerByID($user_id);
            if ($customer != null) {
                $user = $this->get_user_phonenumber($customer, $parent_customer_id);
                $phone_number = $this->get_first_phonnumber_byuser($user);
                if (!empty($phone_number)) {
                    redirect('phones/' . $this->method . '?u=' . $user_id.'&phone_number='.$phone_number);
                }
            }
        }
        // Gets call history from sontel.
        $this->get_call_history($parent_customer_id);
        
        // Load phone call history by user_id and phone_user_id
        $start = $this->input->get_post('start');
        $limit = $this->input->get_post('limit');
        if (empty($start)) {
            $start = 0;
        }
        if (empty($limit)) {
            $limit = APContext::getPagingSetting(); // APConstants::DEFAULT_PAGE_ROW;
        }
        
        $output = phones_api::loadPhoneCallHistory($customer_id, $user_id, $phone_number, $start, $limit);
        // Total record
        $total = $output['total'];
        $list_phone_call = $output['data'];
        
        // config panation.
        $config = array();
        $config["base_url"] = $base_url;
        $config["total_rows"] = $total;
        $config["per_page"] = $limit;
        $config["uri_segment"] = APConstants::PANATION_URI_SEGMET;

        $this->pagination->initialize($config);

        $this->template->set('page_link', $this->pagination->create_links());
        $this->template->set('start', $start);
        $this->template->set('limit', $limit);
        $this->template->set('total_phone_call', $total);
        $this->template->set('list_phone_call', $list_phone_call);
        $this->template->set('current_user_id', $user_id);
        $this->template->set('current_phone_number', $phone_number);
    }
    
    /**
     * Gets call history from sontel
     */
    private function get_call_history($parent_customer_id){
        $check_flag = '';//APContext::getSessionValue(APConstants::SESSION_UPDATE_CALL_HISTORY_SONTEL);
        if(empty($check_flag)){
            // gets account number id.
            $account_id = APContext::getSubAccountId($parent_customer_id);
        
            phones_api::getCallHistoryFromSontel($parent_customer_id, $account_id);
            APContext::setSessionValue(APConstants::SESSION_UPDATE_CALL_HISTORY_SONTEL, 1);
        }
    }

    /**
     * Get first postbox id.
     * 
     */
    protected function get_first_user() {
        $list_user = $this->get_all_phone_number();
        foreach($list_user as $user) {
            if (count($user->list_phonenumber) > 0) {
                return $user;
            }
        }
        return '';
    }
    
    /**
     * Get first postbox id.
     * 
     * @param unknown_type $user          
     */
    protected function get_first_phonnumber_byuser($user) {
        if (count($user->list_phonenumber) > 0) {
            return $user->list_phonenumber[0]->phone_number;
        }
        return '';
    }

}
