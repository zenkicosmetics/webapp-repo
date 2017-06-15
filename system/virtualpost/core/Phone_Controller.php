<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Code here is run before frontend controllers
 */
class Phone_Controller extends MY_Controller {
    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     */
    public function __construct() {
        parent::__construct();
        
        // Load admin themes helper
        $this->load->helper('admin_theme');
        $this->load->model('customers/phone_customer_user_m');
        $this->load->model('scans/envelope_m');
        $this->load->model('customers/customer_m');
        
        // Show error and exit if the user does not have sufficient permissions
        if (! self::_check_access()) {
            //redirect('mailbox');
        }
        
        $frontend_theme = Settings::get(APConstants::PHONE_THEMES_CODE);
        $web_path = APPPATH . 'themes/' . $frontend_theme . '/';
        // Set the location of assets
        Asset::add_path('theme', $web_path);
        Asset::set_path('theme');
        
        // Get all postbox of this customer
        // If empty user phone list will redirect to my account > user to create new user phone
        $customer_users = $this->get_all_phone_number();
        
        //if (empty($customer_users) || count($customer_users) == 0) {
            //redirect('account/users/phone_users');
        //}
        
        // Template configuration
        $this->template->set('phone_users', $customer_users);
        $this->template->enable_parser(FALSE)->set_theme($frontend_theme)->set_layout('default', '');
    }
    
    /**
     * Get all postbox of customer login.
     */
    protected function get_all_phone_number() {
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $results = array();
        if(APContext::isPrimaryCustomerUser()){
            // owner customer.
            // Get all postbox of this customer
            $customers = $this->customer_m->get_many_by_many(array(
                "parent_customer_id" => $parent_customer_id
            ));
            // For each customers
            foreach($customers as $customer) {
                $result = $this->get_user_phonenumber($customer, $parent_customer_id);
                if (!empty($result->list_phonenumber)) {
                    $results[] = $result;
                }
            }
            $customer = APContext::getCustomerLoggedIn();
            $result = $this->get_user_phonenumber($customer, $parent_customer_id);
            if (!empty($result->list_phonenumber)) {
                $results[] = $result;
            }
        } else {
            $customer = APContext::getCustomerLoggedIn();
            $result = $this->get_user_phonenumber($customer, $parent_customer_id);
            if (!empty($result->list_phonenumber)) {
                $results[] = $result;
            }
        }
        APContext::setSessionValue('get_all_phonenumber_list', $results);
        return $results;
    }
    
    /**
     * Get all postbox of given customer.
     * 
     * @param type $customer
     */
    protected function get_user_phonenumber($customer, $parent_customer_id) {
        if(empty($customer)){
            return array();
        }
        $customer_id = $customer->customer_id;
        // Get all postbox of this customer
        $list_phonenumber = $this->phone_number_m->get_list_phonenumber_by_user($parent_customer_id, $customer_id);
        $customer->list_phonenumber = $list_phonenumber;
        return $customer;
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
            	'customers/welcome',
                'customers/forgot_pass',
                'customers/term_of_service',
                'customers/term_of_service_external',
                'customers/privacy',
                'customers/privacy_external',
                'customers/view_terms',
                'customers/view_privacy',
                'customers/register',
                'customers/register_external',
                'customers/login_external',
                'customers/forgot_pass_external',
                'users/reset_pass',
                'users/reset_complete',
                'payment/success_callback',
                'payment/error_callback',
                'payment/payment_paypal_return',
                'payment/payment_paypal_calcel',
                'digitalpanel/generate',
                'digitalpanel/download'
        );
        
        // Check if the current page is to be ignored
        $current_page = $this->uri->segment(1, '') . '/' . $this->uri->segment(2, 'index');
        
        // Dont need to log in, this is an open page
        if (in_array($current_page, $ignored_pages)) {
            return TRUE;
        }
        else if (! APContext::isCustomerLoggedIn()) {
            if ($this->is_ajax_request()) {
                redirect('customers/ajax_login');
            }
            else {
                redirect('customers/login');
            }
        }
        
        // Check customer exist in database
        if(!APContext::getAdminIdLoggedIn()){
            $customer_id = APContext::getCustomerCodeLoggedIn();
            $customer = $this->customer_m->get_by_many(array (
                    "customer_id" => $customer_id,
                    "(status is NULL or status <> 1)" => null
            ));
            if (empty($customer)) {
                if ($this->is_ajax_request()) {
                    redirect('customers/ajax_login');
                }
                else {
                    redirect('customers/login');
                }
            }
        }
        // god knows what this is... erm...
        return TRUE;
    }
}
