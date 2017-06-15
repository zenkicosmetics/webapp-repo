<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Code here is run before frontend controllers
 */
class Public_Controller extends MY_Controller
{
    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     */
    public function __construct()
    {
        parent::__construct();

        // Load admin themes helper
        $this->load->helper('admin_theme');
        $this->load->model('mailbox/postbox_m');
        $this->load->model('scans/envelope_m');
        $this->load->model('customers/customer_m');
        $this->load->model('customers/postbox_customer_user_m');

        // Show error and exit if the user does not have sufficient permissions
        if (!self::_check_access()) {
            // redirect('mailbox');
        }

        $frontend_theme = Settings::get(APConstants::FRONTEND_THEMES_CODE);
        $web_path = APPPATH . 'themes/' . $frontend_theme . '/';

        // Set the location of assets
        Asset::add_path('theme', $web_path);
        Asset::set_path('theme');

        // Get all postbox of this customer
        $customer_users = $this->get_all_postbox();
        
        // gets customer setting
        $customer_id = 0;
        if (!empty($customer_users) && count($customer_users) > 0) {
            $customer_id = $customer_users[0]->customer_id;
        }
        if(APContext::isPrimaryCustomerUser()){
            $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
            $customer_product_setting = CustomerProductSetting::get_activate_flags($parent_customer_id);
        } else {
            $customer_product_setting = CustomerProductSetting::get_activate_flags($customer_id);
        }
        

        // Template configuration
        $this->template->set('customer_users', $customer_users);
        $this->template->set('customer_product_setting', $customer_product_setting);
        $this->template->enable_parser(FALSE)->set_theme($frontend_theme)->set_layout('default', '');
    }

    /**
     * Get all postbox of customer login.
     */
    protected function get_all_postbox()
    {
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        // $results = APContext::getSessionValue('get_all_postbox_list');
        // if ($results != null) {
        //    return $results;
        // }
        $results = array();
        if(APContext::isPrimaryCustomerUser()){
            // owner customer.
            // Get all postbox of this customer
            $customers = $this->customer_m->get_many_by_many(array(
                "parent_customer_id" => $parent_customer_id
            ));
            // For each customers
            foreach($customers as $customer) {
                $result = $this->get_user_postbox($customer, $parent_customer_id);
                if (!empty($result->list_postbox)) {
                    $results[] = $result;
                }
            }
            $customer = APContext::getCustomerLoggedIn();
            $result = $this->get_user_postbox($customer, $parent_customer_id);
            if (!empty($result->list_postbox)) {
                $results[] = $result;
            }
        } else {
            $customer = APContext::getCustomerLoggedIn();
            $result = $this->get_user_postbox($customer, $parent_customer_id);
            if (!empty($result->list_postbox)) {
                $results[] = $result;
            }
        }
        APContext::setSessionValue('get_all_postbox_list', $results);
        return $results;
    }
    
    /**
     * Get all postbox of given customer.
     * 
     * @param type $customer
     */
    private function get_user_postbox($customer, $parent_customer_id) {
        $list_postbox = array();
        if(empty($customer)){
            return array();
        }
        $customer_id = $customer->customer_id;
        // Get all postbox of this customer
        $postboxs = $this->postbox_m->get_postboxes_by($customer_id);

        if(empty($postboxs)){
            $customer->list_postbox = $list_postbox;
            return $customer;
        }

        // Foreach postbox count number of new items
        foreach ($postboxs as $postbox) {
            $postbox->number_new_item = $this->envelope_m->count_by_many(array(
                "to_customer_id" => $customer_id,
                "postbox_id" => $postbox->postbox_id,
                "completed_flag" => APConstants::OFF_FLAG,
                "(envelope_scan_flag IS NULL OR envelope_scan_flag = '0')" => null,
                "(item_scan_flag IS NULL OR item_scan_flag = '0')" => null,
                "(direct_shipping_flag IS NULL OR direct_shipping_flag = '0')" => null,
                "(collect_shipping_flag IS NULL OR collect_shipping_flag = '0')" => null,
                "trash_flag IS NULL" => null,
                "new_notification_flag" => APConstants::ON_FLAG
            ));
            $list_postbox [] = $postbox;
        }
        $customer->list_postbox = $list_postbox;
        return $customer;
    }

    /**
     * Get first postbox id.
     *
     * @param unknown_type $postboxs
     */
    protected function get_first_postbox()
    {
        $customer_users = $this->get_all_postbox();
        if (count($customer_users) > 0) {
            $postboxs = $customer_users[0]->list_postbox;
            if (count($postboxs) > 0) {
                return $postboxs [0]->postbox_id;
            }
        }
        return '';
    }

    /**
     * Checks to see if a user object has access rights to the admin area.
     *
     * @return boolean
     */
    private function _check_access()
    {
        // These pages get past permission checks
        $ignored_pages = array(
            'customers/login',
            'customers/view_external_login',
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
            'users/reset_pass_complete',
            'payment/success_callback',
            'payment/error_callback',
            'payment/payment_paypal_return',
            'payment/payment_paypal_calcel',
            'digitalpanel/generate',
            'digitalpanel/download',
            'customers/confirm_new_email',
            'customers/direct_access',
            'mailbox/dropbox_authorization_callback'
        );

        // Check if the current page is to be ignored
        $current_page = $this->uri->segment(1, '') . '/' . $this->uri->segment(2, 'index');

        // Dont need to log in, this is an open page
        if (in_array($current_page, $ignored_pages)) {
            return TRUE;
        } else if (!APContext::isCustomerLoggedIn()) {
            if ($this->is_ajax_request()) {
                redirect('customers/ajax_login');
            } else {
                redirect('customers/login');
            }
        }

        // Check customer exist in database
        if (!APContext::getAdminIdLoggedIn()) {
            $customer_id = APContext::getCustomerCodeLoggedIn();
            $customer = $this->customer_m->get_by_many(array(
                "customer_id" => $customer_id,
                "(status is NULL or status <> 1)" => null
            ));
            if (empty($customer)) {
                if ($this->is_ajax_request()) {
                    redirect('customers/ajax_login');
                } else {
                    redirect('customers/login');
                }
            }
        }
        // god knows what this is... erm...
        return TRUE;
    }
}
