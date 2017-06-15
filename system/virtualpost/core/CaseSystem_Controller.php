<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Code here is run before frontend controllers
 */
class CaseSystem_Controller extends MY_Controller {
    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('customers/customer_m');
        
        // Load admin themes helper
        $this->load->helper('admin_theme');
        
        // Show error and exit if the user does not have sufficient permissions
        if (! self::_check_access()) {
            redirect('catalogue');
        }
        
        $frontend_theme = "case_system";
        $web_path = APPPATH . 'themes/' . $frontend_theme . '/';
        
        // Set the location of assets
        Asset::add_path('theme', $web_path);
        Asset::set_path('theme');
        
        // Template configuration
        $this->template->enable_parser(FALSE)->set_theme($frontend_theme)->set_layout('default', '');
    }
    
    /**
     * Checks to see if a user object has access rights to the admin area.
     * 
     * @return boolean
     */
    private function _check_access() {
        // These pages get past permission checks
        $ignored_pages = array (
                'customers/login',
                'customers/logout',
                'customers/ajax_login',
                'customers/active',
                'customers/forgot_pass',
                'customers/term_of_service',
                'customers/register',
                'users/reset_pass',
                'users/reset_complete',
                'payment/success_callback',
                'payment/error_callback',
                'payment/authorize_success_callback',
                'payment/authorize_error_callback',
                'payment/payment_paypal_ipn',
	            'device/ping',
	            'device/setup',
	            'device/get_data',
	            'device/get_updates'
        );
        
        // Check if the current page is to be ignored
        $current_page = $this->uri->segment(1, '') . '/' . $this->uri->segment(2, 'index');
        
        // Dont need to log in, this is an open page
        if (in_array($current_page, $ignored_pages)) {
            return TRUE;
        }
        else if (! APContext::isCustomerLoggedIn()) {
            if ($this->is_ajax_request()) {
                // redirect('customers/logout_ajax');
                redirect('customers/ajax_login');
            }
            else {
                redirect('customers/login');
            }
        }
        
        // Check customer exist in database
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $customer = $this->customer_m->get_by_many(array (
                "customer_id" => $customer_id 
        ));
        if (empty($customer)) {
            if ($this->is_ajax_request()) {
                redirect('customers/ajax_login');
            }
            else {
                redirect('customers/login');
            }
        }
        
        // god knows what this is... erm...
        return TRUE;
    }
}
