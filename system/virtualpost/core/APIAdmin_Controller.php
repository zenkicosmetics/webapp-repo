<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Code here is run before frontend controllers
 */
class APIAdmin_Controller extends MY_Controller {
    
    // Set expired time (60 minutes = 60 * 60 * 60)
    const EXPIRED_TIME = 1209600; // 2 weeks
    
    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     */
    public function __construct() {
        
        parent::__construct();
        $this->load->model('customers/customer_m');
        $this->load->model('users/mobile_session_m');
        $this->load->model('api/api_message_log_m');
        $this->load->model('api/app_external_m');
        
        $this->load->library(array(
            'users/users_api'
        ));
        
        // Get session key from header
        $session_key = $this->getSessionKey();
        $app_code = $this->getAppCode();
        $app_key = $this->getAppKey();
        
    	// Show error and exit if the user does not have sufficient permissions
        if (! self::_check_valid_request($app_code, $app_key)) {
            $data = array (
            		'code' => 8888,
            		'status' => 9999,
            		'message' => 'request is invalid',
            		'result' => ''
            );
            echo json_encode($data);
            exit();
        }
        
        // Show error and exit if the user does not have sufficient permissions
        if (! self::_check_access($session_key)) {
            $data = array (
            		'code' => 9999,
            		'status' => 9999,
            		'message' => 'session is invalid',
            		'result' => ''
            );
            echo json_encode($data);
            exit();
        }
    }
    
    /**
     * Get session key from header
     */
    public function getSessionKey() {
        $key = $this->input->get_request_header('Session-key', TRUE);
        if (empty($key)) {
            $key = $this->input->get_request_header('Session-Key', TRUE);
        }
        if (empty($key)) {
        	$key = $this->input->get_request_header('session-key', TRUE);
        }
        if (empty($key)) {
            return '';
        }
        return $key;
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
        return ci()->input->ip_address();
    }
    
    /**
     * Get IP Address
     */
    public function getUserAgent() {
    	return ci()->input->user_agent();
    }
    
    /**
     * Build success response message
     */
    protected function api_success_output($data) {
        $data['status'] = APConstants::API_RETURN_SUCCESS;
        echo json_encode($data);
        $this->_log_request($data);
        exit(); // Stop execution script
    }
    
	/**
     * Build error response message
     */
    protected function api_error_output($data) {
        $data['status'] = APConstants::API_RETURN_ERROR;
        echo json_encode($data);
        $this->_log_request($data);
        exit(); // Stop execution script
    }
    
    /**
     * Checks to see if a user object has access rights to the admin area.
     * 
     * @return boolean
     */
    private function _check_valid_request($app_code, $app_key) {
    	// Get session information
        $app_check = $this->app_external_m->get_by_many(array (
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
     * Checks to see if a user object has access rights to the admin area.
     * 
     * @return boolean
     */
    private function _check_access($session_key) {
        
        // These pages get past permission checks
        $ignored_pages = array (
            'api/admin/login',
            'api/admin/reset_password',
            'api/admin/get_app_version',
            'api/admin/view_file',
        );
        
        // Check if the current page is to be ignored
        $current_page = $this->uri->segment(1, '') . '/' . $this->uri->segment(2, 'index'). '/' . $this->uri->segment(3, '');
    
        // Dont need to log in, this is an open page
        if (in_array($current_page, $ignored_pages)) {
            return TRUE;
        }
        
        // Get session information
        $session_check = $this->mobile_session_m->get_by_many(array (
                'session_key' => $session_key 
        ));
        
        $message = "\n Session Key: ".$session_key." \n ";

        // Check exist session information
        if (empty($session_check)) {

            $message .= ' \n Session key: '.$session_key.' is invalid \n ';
            log_audit_message(APConstants::LOG_ERROR, $message, FALSE, 'apiadmin_controller_check_access_');
            log_message(APConstants::LOG_ERROR, '>>>>>> Session key: '.$session_key.' is invalid');
            return FALSE;
        }

        // Check expired time
        if ($session_check->last_activity < now() - APIAdmin_Controller::EXPIRED_TIME) {

            $message .= ' \n Session key: '.$session_key.' is expired \n';
            log_audit_message(APConstants::LOG_ERROR, $message,FALSE,'apiadmin_controller_check_access_');
            log_message(APConstants::LOG_ERROR, '>>>>>> Session key: '.$session_key.' is expired');
            return FALSE;
        }
        
        // Check customer information
        $user_data = $session_check->user_data;
        


        // Decode user_data from JSON to object
        if (empty($user_data)) {
            
            $message .= '\n User data of session key: '.$session_key.' is empty \n';
            log_audit_message(APConstants::LOG_ERROR, $message, FALSE, 'apiadmin_controller_check_access_');

            log_message(APConstants::LOG_ERROR, '>>>>>> User data of session key: '.$session_key.' is empty');
            return FALSE;
        }
        
        $user_data_object = json_decode($user_data);
        if (empty($user_data_object) || empty($user_data_object->id)) {

            $message .= '\n User data of session key: '.$session_key.' is invalid \n';
            log_audit_message(APConstants::LOG_ERROR, $message, FALSE, 'apiadmin_controller_check_access_');

            log_message(APConstants::LOG_ERROR, '>>>>>> User data of session key: '.$session_key.' is invalid');
            return FALSE;
        }

        $message .= '\n User data: '.$user_data." \n ";
        log_audit_message(APConstants::LOG_ERROR, $message, FALSE, 'apiadmin_controller_check_access_');
        
        // Store customer information to session and update last_activity date
        users_api::set_group_user($user_data_object);
        $this->session->set_userdata(APConstants::SESSION_MOBILE_ADMIN_KEY, $user_data_object);

        $this->mobile_session_m->update_by_many(array (
                'session_key' => $session_key 
        ), array (
                'last_activity' => now() 
        ));
        
        return TRUE;
    }
    
    // Request Parameter Log function
    protected function _log_request($data) {
        $request_method = $this->input->server('REQUEST_METHOD');
        return $this->api_message_log_m->insert(array(
        			'app_code' =>  $this->getAppCode(),
                    'customer_id' => 0,
                    'uri' => $this->uri->uri_string(),
                    'request_method' => $request_method,
                    'request_header' => json_encode($this->input->request_headers()),
                    'request_param' => $request_method == 'GET' ? json_encode($this->input->get()):json_encode($this->input->post()),
        			'request_date' => function_exists('now') ? now() : time(),
                    'ip_address' => $this->input->ip_address(),
                    'response' => json_encode($data)
                ));
    }

    public function get_paging_mobile_input($column = '', $sort_type = 'DESC', $page=0, $rows=100)
    {
        $page  = isset($_REQUEST['page']) ? $_REQUEST['page'] : $page;
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : $rows;
        $sidx  = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : $column;
        $sord  = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : $sort_type;

        $start = $limit * $page - $limit;
        $start = ($start < 0) ? 0 : $start;

        return array(
            "page" => $page,
            "start" => $start,
            "limit" => $limit,
            "sort_type" => $sord,
            "sort_column" => $sidx
        );
    }

}
