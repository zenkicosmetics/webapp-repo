<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Code here is run before frontend controllers
 */
class APIPartner_Controller extends MY_Controller {

    // Set expired time (60 minutes = 60 * 60 * 60)
    const EXPIRED_TIME = 216000000000;

    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('customers/customer_m');
        $this->load->model('api/api_message_log_m');
        $this->load->model('api/app_external_m');

        // Get session key from header
        $app_code = $this->getAppCode();
        $app_key = $this->getAppKey();

        // Show error and exit if the user does not have sufficient permissions
        if (!self::_check_valid_request($app_code, $app_key)) {
            $data = array(
                'status' => 9999,
                'message' => "Your key is invalid",
                'result' => ''
            );
            echo json_encode($data);
            exit();
        }
    }

    /**
     * Get app code from header
     */
    public function getAppCode() {
        $app_code = $this->input->get_request_header('App-code', TRUE);
        if (empty($app_code)) {
            $app_code = $this->input->get_request_header('App-Code', TRUE);
        }
        if (empty($app_code)) {
            $app_code = $this->input->get_request_header('app-code', TRUE);
        }
        if (empty($app_code)) {
            return '';
        }
        return $app_code;
    }

    /**
     * Get app key from header
     */
    public function getAppKey() {
        $app_key = $this->input->get_request_header('App-key', TRUE);
        if (empty($app_key)) {
            $app_key = $this->input->get_request_header('App-Key', TRUE);
        }
        if (empty($app_key)) {
            $app_key = $this->input->get_request_header('app-key', TRUE);
        }
        if (empty($app_key)) {
            return '';
        }
        return $app_key;
    }

    /**
     * Get IP Address
     */
    public function getIPAddress() {
        return $this->input->ip_address();
    }

    /**
     * Get IP Address
     */
    public function getUserAgent() {
        return $this->input->user_agent();
    }

    /**
     * Build success response message
     */
    protected function api_success_output($message, $data= array()) {
        $status = $data['status'];
        unset($data['status']);
        $response = array(
            "status" => $status,
            "result" => $data,
            "message" => $message
        );
        echo json_encode($response);
        $this->_log_request($response);
        exit(); // Stop execution script
    }

    /**
     * Build error response message
     */
    protected function api_error_output($message, $data= array()) {
        $status = $data['status'];
        unset($data['status']);
        $response = array(
            "status" => $status,
            "result" => $data,
            "message" => $message
        );
        echo json_encode($response);
        $this->_log_request($response);
        
        exit(); // Stop execution script
    }

    /**
     * Checks to see if a user object has access rights to the admin area.
     * 
     * @return boolean
     */
    private function _check_valid_request($app_code, $app_key) {
        // Get session information
        $app_check = $this->app_external_m->get_by_many(array(
            'app_code' => $app_code,
            'app_key' => $app_key
        ));
        if (empty($app_check)) {
            return FALSE;
        }
        if ($app_check->disable_flag == APConstants::ON_FLAG) {
            return FALSE;
        }
        return true;
    }
    
    /**
     * Gets enteprise customer.
     */
    protected function getEnterpriseCustomerId(){
        $app_code = $this->getAppCode();
        $app_key = $this->getAppKey();
        
        $app_check = $this->app_external_m->get_by_many(array(
            'app_code' => $app_code,
            'app_key' => $app_key,
            "disable_flag" => 0
        ));
        
        if(empty($app_check)){
            return 0;
        }else{
            return  $app_check->customer_id;
        }
    }

    /**
     * Checks to see if a user object has access rights to the admin area.
     * 
     * @return boolean
     */
    private function _check_access($session_key) {
        // These pages get past permission checks
        $ignored_pages = array(
            'api/phone/get_phone_pricing',
            'api/phone/get_phone_country',
            'api/phone/get_phone_area',
            'api/phone/get_list_phone_number',
        );

        // Check if the current page is to be ignored
        $current_page = $this->uri->segment(1, '') . '/' . $this->uri->segment(2, 'index').'/'. $this->uri->segment(3, 'index');

        // Dont need to log in, this is an open page
        if (in_array($current_page, $ignored_pages)) {
            return TRUE;
        }
        
        return TRUE;
    }

    // Request Parameter Log function
    protected function _log_request($data) {
        $request_method = $this->input->server('REQUEST_METHOD');
        return $this->api_message_log_m->insert(array(
                    'app_code' => $this->getAppCode(),
                    //'customer_id' => MobileContext::getCustomerIDLoggedIn(),
                    'uri' => $this->uri->uri_string(),
                    'request_method' => $request_method,
                    'request_header' => json_encode($this->input->request_headers()),
                    'request_param' => $request_method == 'GET' ? json_encode($this->input->get()) : json_encode($this->input->post()),
                    'request_date' => function_exists('now') ? now() : time(),
                    'ip_address' => $this->input->ip_address(),
                    'response' => json_encode($data)
        ));
    }

}
