<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Customers extends Public_Controller
{
    /**
     * Validation for basic profile data.
     * The rest of the validation is built by streams.
     *
     * @var array
     */
    private $validation_rules = array(
        array(
            'field' => 'email',
            'label' => 'lang:email',
            'rules' => 'required|valid_email|max_length[255]|callback__check_email'
        ),
        array(
            'field' => 'password',
            'label' => 'lang:password',
            'rules' => 'required|trim|max_length[255]|min_length[6]'
        ),
        array(
            'field' => 'agree_flag',
            'label' => 'lang:agree_flag',
            'rules' => 'required'
        )
    );

    /**
     * Validation for basic profile data.
     * The rest of the validation is built by streams.
     *
     * @var array
     */
    private $validation_rules_02 = array(
        array(
            'field' => 'postbox_name',
            'label' => 'lang:postbox_name',
            'rules' => 'required'
        ),
        array(
            'field' => 'address_name',
            'label' => 'lang:address_name',
            'rules' => ''
        ),
        array(
            'field' => 'address_company_name',
            'label' => 'lang:address_company_name',
            'rules' => 'callback__check_company'
        ),
        array(
            'field' => 'location_available_id',
            'label' => 'lang:location_available_id',
            'rules' => 'required'
        )
    );

    // Rule for save external record payment
    private $validation_rules05 = array(
        array(
            'field' => 'cardID',
            'label' => 'credit card',
            'rules' => 'required|trim'
        ),
        array(
            'field' => 'tranAmount',
            'label' => 'amount',
            'rules' => 'required|trim'
        )
    );

    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     *
     * @todo Document properly please.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->library('form_validation');
        $this->load->library('encrypt');
        $this->lang->load('customer');

        // load the theme_example view
        $this->load->model('customers/customer_m');
        $this->load->model('addresses/customers_address_m');
        $this->load->model('addresses/customers_forward_address_m');
        $this->load->model('addresses/location_m');
        $this->load->model('cloud/customer_cloud_m');
        $this->load->model('mailbox/postbox_m');

        $this->load->model('email/email_m');
        $this->load->model('settings/countries_m');
        $this->load->model('settings/currencies_m');
        $this->load->model('mailbox/user_paging_m');
        $this->load->model('mailbox/postbox_setting_m');

        $this->load->model('payment/payment_m');
        $this->load->model('partner/partner_customer_m');
        $this->load->model('partner/partner_m');
        $this->load->model('partner/partner_marketing_profile_m');

        $this->load->model('customers/customer_blacklist_m');
        $this->load->model('customers/customer_blacklist_hist_m');

        $this->load->library('customers_api');
    }

    /**
     * Index Page for this controller.
     */
    public function index()
    {
    }

    /**
     * Login with email address and password
     */
    public function login()
    {
        if ($this->is_ajax_request()) {
            $this->template->set_layout(false);
        }

        // Set the validation rules
        $this->validation_rules = array(
            array(
                'field' => 'user_name',
                'label' => lang('email'),
                'rules' => 'required|callback__check_login'
            ),
            array(
                'field' => 'password',
                'label' => lang('password'),
                'rules' => 'required'
            )
        );

        // Redirect to mailbox if customer already login
        if (APContext::isCustomerLoggedIn()) {
            // Check customer exist in database
            $customer_id = APContext::getCustomerCodeLoggedIn();
            $customer = $this->customer_m->get_by_many(array(
                "customer_id" => $customer_id,
                "(status is NULL or status <> 1)" => null
            ));
            if (!empty($customer) && !$this->is_ajax_request()) {
                // #563: trigger to start case verification for
                CaseUtils::start_verification_case(APContext::getCustomerCodeLoggedIn());

                // redirect.
                redirect('mailbox');
            }
        }

        if ($_POST) {
            // Call validation and set rules
            $this->form_validation->set_rules($this->validation_rules);

            // If the validation worked, or the user is already logged in
            if ($this->form_validation->run() || $this->logged_in()) {
                // 20151114 DuNT Start added:  fixbug #673
                // reset hide panel 
                $this->user_paging_m->update_by_many(array(
                    "user_id" => APContext::getCustomerCodeLoggedIn(),
                    "setting_key" => APConstants::USER_HIDE_PANES_LAYOUT
                ), array(
                    "setting_value" => ''
                ));
                // 20151114 DuNT End added:  fixbug #673

                // #563: trigger to start case verification for
                CaseUtils::start_verification_case(APContext::getCustomerCodeLoggedIn());

                if ($this->is_ajax_request()) {
                    $this->success_output('');
                    return;
                } else {
                    // check enterprise code
                    $enterprise_token = $this->input->get_post('token');
                    if(!empty($enterprise_token)){
                        $account_setting = AccountSetting::getSettingByValue(APConstants::NEW_REGISTRATION_TOKEN_ENTERPRISE_ACCOUNT, $enterprise_token);
                        if(!empty($account_setting)){
                            $parent_customer_id = $account_setting->parent_customer_id;
                            $own_domain = AccountSetting::get($parent_customer_id, APConstants::CUSTOMER_OWN_DOMAIN_KEY);
                            if (!empty($own_domain)) {
                                redirect($own_domain, 'refresh');
                            }
                        }
                    }
                    $key = $this->input->post('key');
                    if ($key == '20140123') {
                        redirect('customers/welcome');
                    } else {
                        redirect('mailbox');
                    }
                }
            } else {
                if ($this->is_ajax_request()) {
                    $this->error_output('Your username or password are incorrect.');
                    return;
                }
            }
        }
        $login_type = $this->input->post('login_type');
        if ($login_type == 'widget') {
            $this->template->set_layout(FALSE)->build('view_external_login');
        } else {
            $this->template->set_layout(FALSE)->build('login');
        }
        
    }
    
    /**
     * Login with email address and password
     */
    public function view_external_login()
    {
        $title = $this->input->get_post('title');
        $button_text = $this->input->get_post('button_text');
        $token = $this->input->get_post('token');
        
        $this->template->set('title', $title);
        $this->template->set('button_text', $button_text);
        $this->template->set('token', $token);
        $this->template->set_layout(FALSE)->build('view_external_login');
    }

    /**
     * Login with email address and password
     */
    public function login_external()
    {
        header('content-type: application/json; charset=utf-8');
        $this->template->set_layout(FALSE);

        $message = "";
        $user_name = $this->input->get_post('username', '');
        $password = $this->input->get_post('password', '');
        if ($user_name == '' || $password == '') {
            $message = 'Username and password must be required.';
        }

        if (!$this->_check_login($user_name)) {
            $message = 'Username is incorrect.';
        } else {

            // If the validation worked, or the user is already logged in
            if ($message == '' && $this->logged_in()) {
                echo $_REQUEST['callback'] . "(" . json_encode(array("status" => true, 'message' => 'success login')) . ")";
                exit();
            }
        }

        echo $_REQUEST['callback'] . "(" . json_encode(array("status" => false, 'message' => 'Your username or password are incorrect.')) . ")";
        exit();
    }

    /**
     * Welcome page
     */
    public function welcome()
    {
        $this->template->set_layout(FALSE)->build('welcome');
    }

    /**
     * Login with email address and password
     */
    public function logout()
    {
        APContext::logout();

        if (get_cookie('RememberCode')) {
            delete_cookie('RememberCode');
        }

        // $this->session->sess_destroy();

        redirect('customers/login');
        // $this->template->set_layout(FALSE)->build('logout');
    }

    /**
     * Forfot password
     */
    public function forgot_pass()
    {
        $this->template->set_layout(FALSE);
        $customer = new stdClass();
        $customer->email = '';

        if ($_POST) {
            // Set the validation rules
            $this->validation_rules = array(
                array(
                    'field' => 'email',
                    'label' => lang('email_label'),
                    'rules' => 'required'
                )
            );

            $this->form_validation->set_rules($this->validation_rules);
            if ($this->form_validation->run()) {
                $email = $this->input->post('email');

                // Get user information by email
                $customer = $this->customer_m->get_by_many(array(
                    "email" => $email
                ));

                // Check if customer is delete
                if (empty($customer)) {
                    $this->error_output(lang('customer.customer_not_register'));
                    return;
                }

                // Check if customer is delete
                if ($customer->status == '1') {
                    $this->error_output(lang('customer.customer_deleted'));
                    return;
                }

                // Reset password
                $new_pass = APUtils::generateRandom(8);
                $this->customer_m->update_by_many(array(
                    "email" => $email
                ), array(
                    "password" => md5($new_pass)
                ));

                // Send email
                $send_email_data = array(
                    "slug" => APConstants::customer_reset_password,
                    "to_email" => $email,
                    // Replace content
                    "full_name" => $email,
                    "email" => $email,
                    "password" => $new_pass,
                    "site_url" => APContext::getFullBalancerPath()
                );
                // Call API to send email
                MailUtils::sendEmailByTemplate($send_email_data);
                
                
                $this->success_output(lang('forgot_pass_successful'));
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Display the current page
        $this->template->set('customer', $customer)->build('forgot_pass');
    }

    public function forgot_pass_external()
    {
        header('content-type: application/json; charset=utf-8');
        $this->template->set_layout(FALSE);

        $email = $this->input->get_post('email');

        $callback = isset($_REQUEST['callback']) ? $_REQUEST['callback'] : "";
        if ($email) {
            // Get user information by email
            $customer = $this->customer_m->get_by_many(array(
                "email" => $email
            ));

            // Check if customer is delete
            if (!($customer)) {
                echo $callback . "(" . json_encode(array("status" => false, 'message' => "Customer is not registered.")) . ")";
                exit();
            }

            // Check if customer is delete
            if ($customer->status == '1') {
                echo $callback . "(" . json_encode(array("status" => false, 'message' => lang('customer.customer_deleted'))) . ")";
                exit();
            }

            // Reset password
            $new_pass = APUtils::generateRandom(8);
            $this->customer_m->update_by_many(array(
                "email" => $email
            ), array(
                "password" => md5($new_pass)
            ));
            
            // Send email
            $send_email_data = array(
                "slug" => APConstants::customer_reset_password,
                "to_email" => $email,
                // Replace content
                "full_name" => $email,
                "email" => $email,
                "password" => $new_pass,
                "site_url" => APContext::getFullBalancerPath()
            );
            // Call API to send email
            MailUtils::sendEmailByTemplate($send_email_data);

            // ouput success
            echo $callback . "(" . json_encode(array("status" => true, 'message' => lang('forgot_pass_successful'))) . ")";
            exit();
        } else {
            echo $callback . "(" . json_encode(array("status" => false, 'message' => 'Email is required.')) . ")";
            exit();
        }
    }

    /**
     * Ajax login
     */
    public function logout_ajax()
    {
        $this->template->set_layout(FALSE);
        $this->session->unset_userdata(APConstants::SESSION_CUSTOMER_KEY);
        $this->session->unset_userdata(APConstants::SESSION_GROUP_SEARCH_KEY);
        $this->session->unset_userdata(APConstants::SESSION_MAP_PRODUCT_KEY);
        $this->session->unset_userdata(APConstants::SESSION_MEMBERS_KEY);

        if (get_cookie('RememberCode')) {
            delete_cookie('RememberCode');
        }

        $this->session->sess_destroy();
        $this->template->set_layout(FALSE);
        $this->error_output('session time out', array(
            'code' => '999'
        ));
    }

    /**
     * Forfot password
     */
    public function register()
    {
        $this->template->set_layout(FALSE);
        $customer = new stdClass();
        $customer->email = '';
        $customer->password = '';
        $customer->repeat_password = '';

        if ($_POST) {
            $this->form_validation->set_rules($this->validation_rules);
            if ($this->form_validation->run()) {
                $email = $this->input->post('email');
                $password = $this->input->post('password');
                $google_click_id = $this->input->get_post('google_click_id');

                // Check black list customer
                if (APUtils::existBlackListEmail($this->input->get_post('email'))) {
                    $message = lang('customer.exist_in_black_list');
                    $this->error_output($message);
                    return;
                }

                // do register new user.
                $result = customers_api::registerNewCustomer($email, $password, $google_click_id);
                $customer_id = $result['customer_id'];
                
                // DuNT Added: ##585 : customer registration of partner
                $p_code = $this->input->get_post('p', '');
                $p_website = $this->getBaseURLBy($this->input->get_post('p_website', ''));
                if(!empty($customer_id)){
                    customers_api::registerPartnerCustomer($customer_id, $p_code, $p_website);
                }
                // DuNT end added: #585  customer registration of partner

                // auto login this account into our system.
                $this->internal_login($email, $password, false);

                $message = lang('customer.add_success');
                $message = sprintf($message, $email);
                $this->success_output($message);
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Display the current page
        $this->template->set('customer', $customer)->build('register');
    }

    /**
     * register for another domain.
     */
    public function register_external()
    {
        header('content-type: application/json; charset=utf-8');

        $this->template->set_layout(FALSE);
        //$this->form_validation->set_rules($this->validation_rules);
        $email = $this->input->get_post('email');
        $password = $this->input->get_post('password');

        $message = "";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Email is not valid";
        }
        if (strlen($password) < 6) {
            $message = "Password must be more than 6 character.";
        }
        
        if(empty($email)){
            $message = "Email field is required.";
        }

        // Check black list customer
        if (APUtils::existBlackListEmail($this->input->get_post('email'))) {
            $message = lang('customer.exist_in_black_list');
        }

        if (!$this->_check_email($email)) {
            $message = lang('email_exist');
        }

        if ($message == '') {
            // Check black list customer
            /*if (APUtils::existBlackListEmail($this->input->get_post('email'))) {
                $message = lang('customer.exist_in_black_list');
                //$this->error_output($message);
                echo $_REQUEST['callback'] . "(" . json_encode(array("status" => false, 'message' => $message)) . ")";
                return;
            }*/

            // get google click id.
            $google_click_id = $this->input->get_post('google_click_id', '');

            // do register new user.
            $result = customers_api::registerNewCustomer($email, $password, $google_click_id);
            $customer_id = $result['customer_id'];
            
            // check enterprise code
            $enterprise_token = $this->input->get_post('enterprise_token');
            if(!empty($enterprise_token)){
                $account_setting = AccountSetting::getSettingByValue(APConstants::NEW_REGISTRATION_TOKEN_ENTERPRISE_ACCOUNT, $enterprise_token);
                if(!empty($account_setting)){
                    $parent_customer_id = $account_setting->parent_customer_id;
                    
                    // update customer to user enterprise
                    $this->customer_m->update_by_many(array(
                        "customer_id" => $customer_id
                    ), array(
                        "parent_customer_id" => $parent_customer_id,
                        "account_type" => APConstants::ENTERPRISE_TYPE,
                        "role_flag" => APConstants::OFF_FLAG
                    ));
                    
                    $this->postbox_m->update_by_many(array(
                        "customer_id" => $customer_id
                    ), array(
                        "type" => APConstants::ENTERPRISE_TYPE
                    ));
                }
            }

            // DuNT Added: #585 + #1510 : customer registration of partner
            $p_bonus_code = $this->input->get_post('bonus_code', '');
            $p_code = $this->input->get_post('p', '');
            $p_website = $this->getBaseURLBy($this->input->get_post('p_website', ''));
            customers_api::registerPartnerCustomer($customer_id, $p_code, $p_website, $p_bonus_code);
            // DuNT end added: #585  customer registration of partner

            // do auto login into our system.
            //$this->internal_login($email, $password, false);

            $message = lang('customer.add_success');
            $message = sprintf($message, $email);
            //$this->success_output($message);
            echo $_REQUEST['callback'] . "(" . json_encode(array("status" => true, 'message' => $message)) . ")";
            return;
        } else {
            echo $_REQUEST['callback'] . "(" . json_encode(array("status" => false, 'message' => $message)) . ")";
            return;
        }
    }

    /**
     * Login with email address and password
     */
    public function ajax_login()
    {
        $this->template->set_layout(FALSE);
        $this->error_output('session time out', array(
            'code' => '999'
        ));
    }

    /**
     * Active your account.
     */
    public function active()
    {
        $this->template->set_layout(FALSE);
        $key = $this->input->get_post('key');
        if (empty($key)) {
            // Redirect to mailbox
            redirect('mailbox');
        }
        
        $customer = $this->customer_m->get_by_many(array(
            "activated_key" => $key
        ));
        if (empty($customer)) {
            // Redirect to mailbox
            $this->logout();
        }
        $customer_id = $customer->customer_id;
        $this->customer_m->update_by_many(array(
            "activated_key" => $key
        ), array(
            "email_confirm_flag" => APConstants::ON_FLAG
        ));

        // update: convert registration process flag to customer_product_setting.
        CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'email_confirm_flag', APConstants::ON_FLAG);
        
        $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
        $open_balance = $open_balance_data['OpenBalanceDue'];
        if ($open_balance < 0.1) {
            // we dont have to tell the Customer that a valid credit Card with non working payment can reactivate the account.
            // in most cases the Customer will Chose a Card that can handle the payment if it is valid
            // Only reactivate if deactivated_type = auto
            $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;
            customers_api::reactivateCustomer($customer_id, $created_by_id);

            if ($this->is_ajax_request()) {
                $this->success_output('Your account have been active successfully.');
                return;
            } else {
                // Redirect to mailbox
                redirect('mailbox');
            }
        } else {
            if ($this->is_ajax_request()) {
                $this->success_output('Can not activate your account because the open balance due grater than 0.');
                return;
            } else {
                // Redirect to mailbox
                redirect('mailbox');
            }
        }
    }

    /**
     * Active your account.
     */
    public function resend_email_confirm()
    {
        $this->template->set_layout(FALSE);
        $customer = $this->customer_m->get_by_many(array(
            "customer_id" => APContext::getCustomerCodeLoggedIn()
        ));
        $isPrimaryCustomer = APContext::isPrimaryCustomerUser();
        $customer_id = $this->input->get_post('customer_id');
        if (!empty($customer_id) && $isPrimaryCustomer) {
            $customer = CustomerUtils::getCustomerByID($customer_id);
        }

        $activated_key = $customer->activated_key;
        // Send email confirm for user
        $email_template = $this->email_m->get_by('slug', APConstants::new_customer_register);
        $activated_url = APContext::getFullBalancerPath() . "customers/active?key=" . $activated_key;
        $data = array(
            "full_name" => $customer->email,
            "email" => $customer->email,
            "active_url" => $activated_url,
            "site_url" => APContext::getFullBalancerPath()
        );
        $content = APUtils::parserString($email_template->content, $data);
        MailUtils::sendEmail('', $customer->email, $email_template->subject, $content);
        $message = sprintf(lang('resend_email_success'), $customer->email);
        $this->success_output($message);
    }

    /**
     * Callback From: login()
     *
     * @param string $email
     *            The Email address to validate
     * @return bool
     */
    public function _check_login($user_name)
    {
        $this->session->unset_userdata(APConstants::SESSION_SHOW_MOBILE_ADV_FIRST_LOGIN);
        $remember = false;
        if ($this->input->post('remember_me') == 'on') {
            $remember = true;
        }

        // Get password information screen
        $password = $this->input->get_post('password');

        // Internal login
        $result = $this->internal_login($user_name, $password, $remember);

        $user = APContext::getCustomerLoggedIn();
        if ($user && $user->deactivated_type == 'manual') {
            $message = sprintf(lang('customer.account_manual_activated_message'), $user->customer_code);
            $this->form_validation->set_message('_check_login', $message);
            $this->session->set_userdata(APConstants::SESSION_CUSTOMER_KEY, '');
        } else {
            // if user enterprise is not activated
            $user_name = $this->input->post('user_name');
            $password = $this->input->get_post('password');
            $customer = $this->customer_m->get_active_customer_by_account($user_name, md5($password));
            if(!empty($customer) && $customer->account_type == APConstants::ENTERPRISE_CUSTOMER 
                && !empty($customer->parent_customer_id) 
                && $customer->activated_flag == '0'){
                $this->form_validation->set_message('_check_login', "Your account is not activated. please contact with your admin.");
            }else{
                $this->form_validation->set_message('_check_login', lang('login_unsuccessful'));
            }
        }
        return $result;
    }

    /**
     * Internal check login.
     *
     * @param unknown_type $user_name
     * @param unknown_type $password
     */
    private function internal_login($user_name, $password, $remember)
    {
        // Get user information by email and password
        $customer = $this->customer_m->get_active_customer_by_account($user_name, md5($password));

        // if customer is exist and is not deleted yet
        if ($customer) {
            if($customer->account_type == APConstants::ENTERPRISE_CUSTOMER 
                    && !empty($customer->parent_customer_id) 
                    && $customer->activated_flag == '0'){
                return false;
            }
            
            // Store customer information to session
            $this->session->set_userdata(APConstants::SESSION_CUSTOMER_KEY, $customer);
            $this->session->unset_userdata(APConstants::DIRECT_ACCESS_CUSTOMER_KEY);
            $this->session->unset_userdata(APConstants::SESSION_SKIP_CUS_KEY);

            if($customer->role_flag == APConstants::ON_FLAG){
                $this->session->set_userdata(APConstants::GROUP_CUSTOMER_ROLE_KEY, APConstants::GROUP_CUSTOMER_ADMIN);
            }else{
                $this->session->set_userdata(APConstants::GROUP_CUSTOMER_ROLE_KEY, APConstants::GROUP_CUSTOMER_USER);
            }
            $parent_customer_id = $customer->parent_customer_id;
            if (!empty($parent_customer_id)) {
                $parent_customer = APContext::getCustomerByID($parent_customer_id);
                $this->session->set_userdata(APConstants::SESSION_PARENT_CUSTOMER_KEY, $parent_customer);
            }else{
                $this->session->set_userdata(APConstants::SESSION_PARENT_CUSTOMER_KEY, $customer);
                $parent_customer_id = $customer->customer_id;
            }
            APContext::reloadListUser($parent_customer_id, $customer->customer_id);

            // Get current customer dropbox
            $customer_id = $customer->customer_id;
            $customer_setting = $this->customer_cloud_m->get_by_many(
                array(
                    "cloud_id" => APConstants::CLOUD_DROPBOX_CODE,
                    "customer_id" => $customer_id
                ));

            // Get customer address
            $address = $this->customers_address_m->get_by('customer_id', $customer_id);
            // Store customer information to session
            $this->session->set_userdata(APConstants::SESSION_CUSTOMER_ADDRESS_KEY, $address);

            if (!empty($customer_setting)) {
                if (!empty($customer_setting->settings)) {
                    // Decode cloud setting
                    $setting = json_decode($customer_setting->settings, true);
                    $this->session->set_userdata(APConstants::SESSION_CLOUD_CUSTOMER_KEY, $setting);
                }
            }

            if ($remember) {
                $remember_code = $this->encrypt->encode(serialize($customer), Settings::get(APConstants::MAIL_SMTP_PASS_CODE));
                set_cookie('RememberCode', $remember_code, 86500 + time());
            }

            // Update last access date
            $this->customer_m->update_by_many(array(
                "customer_id" => $customer->customer_id
            ), array(
                "last_access_date" => now()
            ));

            return true;
        }
        return false;
    }

    /**
     * Callback From: forgot_pass()
     *
     * @param string $email
     *            The Email address to validate
     * @return bool
     */
    public function _check_forgot_pass($email)
    {
        // Get user information by email
        $customer = $this->customer_m->get_by_many(array(
            "email" => $email
        ));

        if (!$customer) {
            $this->form_validation->set_message('_check_forgot_pass', lang('forgot_pass_unsuccessful'));
            return false;
        }
        return true;
    }

    /**
     * Callback From: check_email()
     *
     * @param string $email
     *            The Email address to validate
     * @return bool
     */
    public function _check_email($email)
    {
        // Get user information by email
        $customer = $this->customer_m->get_active_customer_by_email($email);

        if ($customer) {
            $this->form_validation->set_message('_check_email', lang('email_exist'));
            return false;
        }
        return true;
    }

    /**
     * Callback From: check_company()
     *
     * @param string $email
     *            The Email address to validate
     * @return bool
     */
    public function _check_company($address_company_name)
    {
        $address_name = $this->input->get_post('address_name');
        if (empty(trim($address_name)) && empty(trim($address_company_name))) {
            $this->form_validation->set_message('_check_company', lang('customer.address_required'));
            return false;
        }
        ci()->lang->load('addresses/address');
        if (strtolower(trim($address_name)) == strtolower(trim($address_company_name))) {
            $this->form_validation->set_message('_check_company', lang('error_company_same_name'));
            return false;
        }
        return true;
    }

    /**
     * logged_in
     *
     * @return bool
     * @author Mathew
     */
    public function logged_in()
    {
        $remember_code = get_cookie('RememberCode');
        if ($remember_code) {
            $customer = unserialize($this->encrypt->decode($remember_code, Settings::get(APConstants::MAIL_SMTP_PASS_CODE)));
            if ($customer) {
                $this->session->set_userdata(APConstants::SESSION_CUSTOMER_KEY, $customer);
            }
        }

        return (bool)$this->session->userdata(APConstants::SESSION_CUSTOMER_KEY);
    }

    /**
     * Display term of service
     */
    public function term_of_service()
    {
        $this->load->model('settings/terms_service_m');
        $this->template->set_layout(FALSE);
        $customer_id = APContext::getCustomerCodeLoggedIn();
        
        $content = settings_api::getTermAndCondition();
        if(!empty($customer_id)){
            if(APContext::isUserEnterprise($customer_id)){
                $content = settings_api::getTermAndConditionEnterprise(APContext::getParentCustomerCodeLoggedIn());
            }
        }
        $this->template->set('content', $content);
        $this->template->build('info/view_content_inline');
    }

    /**
     * Display term of service
     */
    public function term_of_service_external()
    {
        header('content-type: application/json; charset=utf-8');
        $this->template->set_layout(FALSE);

        $content = settings_api::getTermAndCondition();
        $html = $this->load->view("info/view_content_inline", array("content" => $content), true);
        echo $_REQUEST['callback'] . "(" . json_encode(array("status" => true, 'html' => $html)) . ")";
        exit();
    }

    /**
     * Display term of service
     */
    public function privacy_external()
    {
        header('content-type: application/json; charset=utf-8');
        $this->template->set_layout(FALSE);

        $content = settings_api::getPrivacyOfSystem();
        $html = $this->load->view("info/view_content_inline", array("content" => $content), true);
        $callback = isset($_REQUEST['callback']) && !empty($_REQUEST['callback']) ? $_REQUEST['callback'] : "abc";
        echo $callback . "(" . json_encode(array("status" => true, 'html' => $html)) . ")";
        exit();
    }

    /**
     * Display term of service
     */
    public function privacy()
    {
        $this->template->set_layout(FALSE);

        $content = settings_api::getPrivacyOfSystem();
        $this->template->set('content', $content);
        $this->template->build('info/view_content_inline');
    }

    /**
     * Display term of service
     */
    public function view_terms()
    {
        $id = $this->input->get_post('id');
        $this->load->model('settings/terms_service_m');
        $this->template->set_layout(FALSE);

        $query_result = $this->terms_service_m->get_system_term_service(array(
            "type" => '1',
            "id" => $id
        ));

        $content = '';
        if ($query_result) {
            $content = $query_result->content;
        }
        $this->template->set('content', $content);
        $this->template->build('info/view_content_inline');
    }

    /**
     * Display term of service
     */
    public function view_privacy()
    {
        $id = $this->input->get_post('id');
        $this->template->set_layout(FALSE);

        $content = settings_api::getPrivacyOfSystem();
        $this->template->set('content', $content);
        $this->template->build('info/view_content_inline');
    }

    /**
     * Forfot password
     */
    public function register_address()
    {
        $this->template->set_layout(FALSE);
        $customer_id = APContext::getCustomerCodeLoggedIn();

        $customer_address = $this->customers_address_m->get_by('customer_id', $customer_id);
        // Get all countries
        $countries = $this->countries_m->get_many_by_many(array(), '', false, array(
            'country_name' => 'ASC'
        ));

        // Display the current page
        $this->template->set('customer_address', $customer_address);
        $this->template->set('countries', $countries);
        $this->template->build('register_address');
    }

    /*
     * Des: manage forward address of customer.
    */
    public function forwardAddress()
    {
        $this->template->set_layout(FALSE);
        
        $envelope_id = $this->input->get_post('envelope_id');
        $customer_id = $this->input->get_post('customer_id');
        if(empty($customer_id)){
            if(empty($envelope_id)){
                $customer_id = APContext::getCustomerCodeLoggedIn();
            }else{
                $customer_id = APContext::getCustomerCodeLoggedInMailbox($envelope_id);
            }
        }
        $customer_address = $this->customers_address_m->get_by('customer_id', $customer_id);
        $address_alt = $this->customers_forward_address_m->get_many_by_many(
            array('customer_id' => $customer_id,'active_flag'=>1)
        );

        // Get all countries
        $countries = $this->countries_m->get_many_by_many(array(), '', false, array(
            'country_name' => 'ASC'
        ));

        // Display the current page
        $this->load->helper('url');
        $this->template->set('customer_address', $customer_address);
        $this->template->set('address_alt', $address_alt);
        $this->template->set('countries', $countries);
        $this->template->set('envelope_id', $envelope_id);
        $this->template->set('customer_id', $customer_id);
        $this->template->build('manage_forward_address');
    }

    public function direct_change_forward_address()
    {

        $this->template->set_layout(FALSE);
        $this->load->model('scans/envelope_m');
        
        ci()->load->library('addresses/addresses_api');
        $envelope_id = $this->input->get_post('envelope_id');
        if(empty($envelope_id)) return;

        $envelope = $this->envelope_m->get($envelope_id);

        $customer_id = APContext::getCustomerCodeLoggedInMailbox($envelope_id);

        $customer_address = addresses_api::getExtraCustomerAddress($customer_id);
        
        if(count($customer_address)){
            $this->standard_forward_address($customer_address);
        }

        $this->template->set('envelope', $envelope);
        $this->template->set('customer_id', $customer_id);
        $this->template->set('customer_address', $customer_address);
        $this->template->build('direct_change_forward_address');
    }

    public function collect_change_forward_address()
    {

        $this->template->set_layout(FALSE);
        $this->load->model('scans/envelope_m');
        
        ci()->load->library('addresses/addresses_api');

        $envelope_id = $this->input->get_post('envelope_id');
        $arr_package = $this->input->get_post('arr_package');
        $hide_flag = $this->input->get_post('hide_flag', '');
        $reload_rate_flag = $this->input->get_post('reload_rate_flag', '');
        $green_flag = $this->input->get_post('green_flag', '');
        $from_flag = $this->input->get_post('from_flag');
        
        $arr_package = json_decode($arr_package);

        if(empty($envelope_id)) return;

        $envelope = $this->envelope_m->get($envelope_id);

        
        $customer_id = APContext::getCustomerCodeLoggedInMailbox($envelope_id);

        $customer_address = addresses_api::getExtraCustomerAddress($customer_id);
       
        if(count($customer_address)){
            $this->standard_forward_address($customer_address);
        }
  
        $this->template->set('customer_id', $customer_id);
        $this->template->set('arr_package', $arr_package);
        $this->template->set('envelope', $envelope);
        $this->template->set("hide_flag", $hide_flag);
        $this->template->set('customer_address', $customer_address);
        $this->template->set('green_flag', $green_flag);
        $this->template->set('reload_rate_flag', $reload_rate_flag);
        $this->template->set('from_flag', $from_flag);
        $this->template->build('collect_change_forward_address');
    }

    public function standard_forward_address($customer_address){
        $primaryAddress = "";
        if(count($customer_address)):
            foreach($customer_address as $row){
                if($row->is_primary_address == 1){
                    $primaryAddress  = $row;

                    $standardFWAddress = "";
                    if(!empty($primaryAddress->shipment_address_name)){
                        $standardFWAddress .= ucwords(strtolower($primaryAddress->shipment_address_name)).", ";
                    }
                    if(!empty($primaryAddress->shipment_company)){
                        $standardFWAddress .= ucwords(strtolower($primaryAddress->shipment_company)).", ";
                    }
                    if(!empty($primaryAddress->shipment_street)){
                        $standardFWAddress .= ucwords(strtolower($primaryAddress->shipment_street)).", ";
                    }
                    if(!empty($primaryAddress->shipment_city)){
                        $standardFWAddress .= ucwords(strtolower($primaryAddress->shipment_city)).", ";
                    }
                    if(!empty($primaryAddress->country_name)){
                        $standardFWAddress .= ucwords(strtolower($primaryAddress->country_name)).", ";
                    }
                    if(!empty($primaryAddress->shipment_phone_number)){
                        $standardFWAddress .= ucwords(strtolower($primaryAddress->shipment_phone_number)).", ";
                    }

                    $standardFWAddress = APUtils::autoHidenTextUTF8($standardFWAddress, $startPosition = 0, $encoding = 'UTF-8', $numberLastCharacter = 2, $strCompare=", ");


                }
            }

            $this->template->set('standardFWAddress', $standardFWAddress);
        endif;
    }

    /*
     * Des: New forward address of customer.
     */
    public function newForwardAddress()
    {

        $this->template->set_layout(FALSE);

        // Get all countries
        $countries = $this->countries_m->get_many_by_many(array(), '', false, array(
            'country_name' => 'ASC'
        ));
        $envelope_id = $this->input->get_post('envelope_id');
        if(empty($envelope_id)){
            $customer_id = APContext::getCustomerCodeLoggedIn();
        }else{
            $customer_id = APContext::getCustomerCodeLoggedInMailbox($envelope_id);
        }
        
        // Display the current page
        $this->load->helper('url');
        $this->template->set('customer_id', $customer_id);
        $this->template->set('countries', $countries);
        $this->template->set('envelope_id', $envelope_id);
        $this->template->build('new_forward_address');
    }

    /**
     * Forfot password
     */
    public function register_postboxname()
    {
        $this->template->set_layout(FALSE);
        $customer_id = APContext::getCustomerCodeLoggedIn();

        $postbox = $this->postbox_m->get_by_many(
            array(
                'customer_id' => $customer_id,
                'first_location_flag' => APConstants::ON_FLAG
            ));
        $customer_address = $this->customers_address_m->get_by('customer_id', $customer_id);

        $locate = $this->location_m->get_many_by_many(array(
            'public_flag' => 1
        ));

        $this->template->set('locate', $locate);
        $this->template->set('address', $customer_address);
        $this->template->set('postbox', $postbox);

        // Display the current page
        $this->template->build('register_postboxname');
    }

    /**
     * Forfot password
     */
    public function register_payment()
    {
        $this->template->set_layout(FALSE);
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $this->load->model('payment/payment_m');
        $this->load->model('addresses/customers_address_m');

        $payment = $this->payment_m->get_by('customer_id', $customer_id);
        
        // Gets invoice address.
        $invoice_address = $this->customers_address_m->get_by('customer_id', $customer_id);
        $this->template->set('invoice_address', $invoice_address);
        
        $this->template->set('payment', $payment);
        // Display the current page
        $this->template->build('register_payment');
    }

    /**
     * View pricing information.
     */
    public function view_pricing()
    { 
        $this->template->set_layout(FALSE);
        $this->load->library("price/price_api");
        $this->load->library("customers/customers_api");
        $this->lang->load('addresses/address');
        
        // Gets customerid logged in.
        $customerId = APContext::getCustomerCodeLoggedIn();

        $postbox = $this->postbox_m->get_by(array(
            'customer_id' => $customerId,
            'is_main_postbox' => 1,
            'first_location_flag' => 1
        ));

        $locationId = ($postbox) ? $postbox->location_available_id : 0;
        $highline = 0;
        if($this->input->get_post('location_id')){
            $locationId = $this->input->get_post('location_id');
            $highline = 1;
        }
        // Gets customer infor.
        $account = customers_api::getCustomerByID($customerId);

        // Get don gia cua tat ca cac loai account type
        $pricing_map = price_api::getPricingMapByLocationId($locationId);
        $this->template->set('highline', $highline);
        $this->template->set('pricing_map', $pricing_map);
        $this->template->set('account', $account);
        $this->template->build('view_pricing');
    }

    /**
     * Save postbox name
     */
    public function save_postboxname()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->form_validation->set_rules($this->validation_rules_02);
            if ($this->form_validation->run()) {

                ci()->load->library('mailbox/mailbox_api');
                ci()->load->library('customers/customers_api');

                $onFlag = APConstants::ON_FLAG;

                $first_location_flag = $this->input->get_post('first_location_flag');
                if (empty($first_location_flag)) {
                    $first_location_flag = APConstants::OFF_FLAG;
                }
                // Insert new customer
                $customer_id = APContext::getCustomerCodeLoggedIn();
                // Update main postbox

                $postbox_name = $this->input->get_post('postbox_name');
                $location_available_id = $this->input->get_post('location_available_id');
                $name = $this->input->get_post('address_name');
                $company = $this->input->get_post('address_company_name');
                $account_type = $this->input->get_post("account_type");
                
                // fix bug 1407
                $this->postbox_m->update_by_many(array(
                    "customer_id" => $customer_id,
                    "is_main_postbox" => $onFlag
                ), array(
                    "postbox_code" => '',
                    "name" => '',
                    "company" => ''
                ));
                
                $this->postbox_m->update_by_many(array(
                    "customer_id" => $customer_id,
                    "is_main_postbox" => $onFlag
                ), array(
                    "postbox_name" => $postbox_name,
                    "location_available_id" => $location_available_id,
                    "name" => trim($name),
                    "company" => trim($company),
                    "type" => $account_type,
                    "first_location_flag" => APConstants::ON_FLAG,
                    'created_date' => now()
                ));
                
                $postbox = $this->postbox_m->get_by_many(array(
                		"customer_id" => $customer_id,
                		"is_main_postbox" => $onFlag
                ));
                
                /* 
                 * #1180 create postbox history page like check item page
                 * Update default postbox into postbox_history_activity table
                 */
                $array_where = array(
                    "customer_id" => $customer_id,
                    "postbox_id" => $postbox->postbox_id
                );
                CustomerUtils::actionPostboxHistoryActivity($array_where,'', '', '', APConstants::UPDATE_POSTBOX);
                // #1044: send email when add new postbox.
                MailUtils::sendEmailWhenAddPostBox($postbox->postbox_id, $postbox->type, $customer_id, $location_available_id);

                // Update data to customer
                $conditionCustomerKey1  = array("customer_id");
                $conditionCustomerVal1   = array($customer_id);
                $dataCustomerKey1       = array("postbox_name_flag", "name_comp_address_flag", "city_address_flag");
                $dataCustomerVal1       = array($onFlag, $onFlag, $onFlag);
                //customers_api::updateCustomer($conditionCustomerKey1, $conditionCustomerVal1, $dataCustomerKey1, $dataCustomerVal1);
                // update: convert registration process flag to customer_product_setting.
                CustomerProductSetting::set_many($customer_id, APConstants::CLEVVERMAIL_PRODUCT, $dataCustomerKey1, $dataCustomerVal1);

                // Update customer information
                $last_updated_date = now();

                //$conditionCustomerKey2  = array("customer_id","shipping_address_completed","invoicing_address_completed","postbox_name_flag","name_comp_address_flag","city_address_flag","payment_detail_flag","email_confirm_flag");
                //$conditionCustomerVal2  = array($customer_id, $onFlag, $onFlag, $onFlag, $onFlag, $onFlag, $onFlag, $onFlag);
                //$dataCustomerKey2       = array("activated_flag", "last_updated_date");
                //$dataCustomerVal2       = array($onFlag, $last_updated_date);
                //customers_api::updateCustomer($conditionCustomerKey2, $conditionCustomerVal2, $dataCustomerKey2, $dataCustomerVal2);
                // update: convert registration process flag to customer_product_setting.
                $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;
                CustomerProductSetting::doActiveCustomer($customer_id, $created_by_id);

                APContext::reloadCustomerLoggedIn();
                $message = lang('customer.save_postboxname_success');
                $this->success_output($message);
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }
    }

    /**
     * Get postbox name by location
     */
    public function get_auto_postbox_name()
    {
        $this->template->set_layout(FALSE);
        $location_available_id = $this->input->get_post("location_available_id");
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $customer_code = sprintf('C%1$08d', $customer_id);
        $location_rec = $this->location_m->get($location_available_id);
        $short_location_name = strtoupper(substr($location_rec->location_name, 0, 3));
        $box_count = $this->postbox_m->count_by_customer_cityname($customer_code, $short_location_name) + 1;
        
        // Get countr name
        $location_rec->country = '';
        if (!empty($location_rec)) {
            $country = $this->countries_m->get_by('id', $location_rec->country_id);
            if (!empty($country)) {
                $location_rec->country = $country->country_name;
            }
        }
        
        // Get customer code and update again
        $postbox_name = $short_location_name . sprintf('%1$02d', $box_count);
        $this->success_output('', array(
            'postbox_name' => $postbox_name,
            'location' => $location_rec
        ));
    }

    /**
     * Callback From: login()
     *
     * @param string $email
     *            The Email address to validate
     * @return bool
     */
    public function check_session()
    {
        $this->template->set_layout(FALSE);
        $customer = APContext::getCustomerLoggedIn();
        $res = array(
            "session_exist" => empty($customer) ? false : true
        );
        echo json_encode($res);
    }

    /**
     * ****************************** PAYMENT *****************************************************
     */
    /**
     * Open invoice payment window screen
     */
    public function invoice_payment()
    {
        $this->template->set_layout(FALSE);
        $this->template->build('invoice_payment');
    }

    /**
     * Open paypal payment window screen
     */
    public function paypal_payment()
    {
        $this->template->set_layout(FALSE);
        $this->template->build('paypal_payment');
    }

    /**
     * Open paypal payment window screen
     */
    public function paypal_payment_invoice()
    {
        $this->load->model('settings/currencies_m');
        $this->load->model('customers/customer_m');
        $this->template->set_layout(FALSE);
        $customer_id = APContext::getCustomerCodeLoggedIn();
        
        $prepayment = $this->input->get_post("prepayment");
        $type = $this->input->get_post("type");
        $str_list_envelope_id = $this->input->get_post("list_envelope_id");
        $action_type = $this->input->get_post("action_type");
        $location_id = $this->input->get_post('location_id');
        $thank_page = $this->input->get_post("thank_page", "");

        $pre_total_amount = 0;
        $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
        $open_balance_due = $open_balance_data['OpenBalanceDue'];
        $open_balance_this_month = $open_balance_data['OpenBalanceThisMonth'];
        if ($prepayment == APConstants::ON_FLAG) {
            if ($type == 'shipping') {
                $pre_total_amount = CustomerUtils::estimateTotalPrepaymentRequest($customer_id) + $open_balance_due + $open_balance_this_month;
            } else if ($type == 'scanning') {
                $pre_total_amount = CustomerUtils::estimateTotalPrepaymentRequest($customer_id) + $open_balance_due + $open_balance_this_month;
            } else if ($type == 'add_more_postbox'){
                
                $amount = floatval($this->input->get_post("amount", 0));
                if ($amount > 0) {
                    $pre_total_amount = $amount;
                } else {
                    $estimate_cost = CustomerUtils::estimateNewPostboxCost(APConstants::BUSINESS_TYPE, $location_id, $customer_id);
                    $pre_total_amount = $estimate_cost + $open_balance_due + $open_balance_this_month;
                }
                
            }
        }
        $this->template->set('list_envelope_id', $str_list_envelope_id);
        $this->template->set('prepayment', $prepayment);
        $this->template->set('action_type', $action_type);
        $this->template->set('type', $type);
        $this->template->set('location_id', $location_id);
        $this->template->set("thank_page", $thank_page);

        // Delete postbox
        if ($action_type == 'delete_postbox') {
            // Count number of postbox
            $number_postbox = CustomerUtils::countNumberPostbox($customer_id);
            if ($number_postbox > 1) {
                $pre_total_amount = $open_balance_due;
            } else if ($number_postbox == 1) {
                $pre_total_amount = $open_balance_due + $open_balance_this_month;
            }
        }
        $this->template->set('pre_total_amount', $pre_total_amount);
        
        $currencies = $this->currencies_m->get_all_currencies_except_euro();
        $selected_currency = $this->customer_m->get_standard_setting_currency($customer_id);
        $decimal_separator = $this->customer_m->get_standard_setting_decimal_separator($customer_id);
        if ($selected_currency->currency_short == 'EUR') {
            $selected_currency = $this->currencies_m->get_by(array('currency_short' => 'USD'));
        }

        $total_postbox = $this->postbox_m->count_by_many(array("customer_id" => $customer_id, "deleted <>1" => null));
        $this->template->set('total_postbox', $total_postbox);
        if ($total_postbox > 1) {
            $open_balance = $open_balance_data['OpenBalanceDue'];
        } else {
            $open_balance = $open_balance_data['OpenBalanceDue'] + $open_balance_data['OpenBalanceThisMonth'];
        }
        $this->template->set('open_balance', $open_balance); 

        $this->template->set('currencies', $currencies);
        $this->template->set('selected_currency', $selected_currency);
        $this->template->set('decimal_separator', $decimal_separator);
        if ($prepayment == APConstants::ON_FLAG) {
            $this->template->build('paypal_pre_payment');
        } else {
            $this->template->build('paypal_payment_invoice');
        }
        
    }

    /**
     * Open paypal payment window screen
     */
    public function save_paypal_payment()
    {
        $this->template->set_layout(FALSE);            

        $this->load->library('paypallib');
        $this->load->model('payment/paypal_tran_hist_m');

        $customer_id = APContext::getCustomerCodeLoggedIn();
        $paypal_amount = str_replace(',', '.', $this->input->get_post("paypal_amount"));
        $prepayment = $this->input->get_post("prepayment");
        $type = $this->input->get_post("type");
        $location_id = $this->input->get_post("location_id");

        $customer = $this->customer_m->get($customer_id);
        if (empty($paypal_amount) || $paypal_amount == '0') {
            // Validate pyapal amount
            $open_balance = APUtils::getCurrentBalance($customer_id);
        } else {
            $open_balance = $paypal_amount;
        }
        $invoice_id = APUtils::genetateReferenceForOpenBalance($customer_id);
        if ($open_balance < 0.01) {
            $this->error_output('Can not get open balance due value less than 0.01.');
            return;
        }

        // Get paypal transaction fee
        $amount_obj = APUtils::includePaypalTransactionFee($open_balance, $customer_id);
        $paypal_tran_fee = $amount_obj['paypal_transaction_fee'];
        $paypal_tran_vat = $amount_obj['paypal_transaction_vat'];
        $open_balance_amount = round($amount_obj['total_amount'], 2);

        $payment_account = APUtils::getPaypalAccount($customer_id);
        $email = '';
        if (!empty($payment_account)) {
            $email = $payment_account->card_number;
        }

        $params = array(
            'amount' => $open_balance_amount,
            'currency' => Settings::get(APConstants::PAYMENT_PAYPAL_CURRENCY_CODE),
            'description' => 'payment for clevvermail services for account ' . $customer->email,
            'return_url' => base_url() . 'payment/payment_paypal_return?invoice_id=' . $invoice_id.'&prepayment='.$prepayment.'&type='.$type.'&location_id='.$location_id,
            'cancel_url' => base_url() . 'payment/payment_paypal_cancel?invoice_id=' . $invoice_id.'&prepayment='.$prepayment.'&type='.$type.'&location_id='.$location_id
        );
        if (!empty($email)) {
            $params['email'] = $email;
        }

        $requestParams = array(
            'RETURNURL' => base_url() . 'payment/payment_paypal_return?invoice_id=' . $invoice_id.'&prepayment='.$prepayment.'&type='.$type.'&location_id='.$location_id,
            'CANCELURL' => base_url() . 'payment/payment_paypal_cancel?invoice_id=' . $invoice_id.'&prepayment='.$prepayment.'&type='.$type.'&location_id='.$location_id
        );
        $orderParams = array(
            'PAYMENTREQUEST_0_AMT' => $open_balance_amount,
            'PAYMENTREQUEST_0_SHIPPINGAMT' => '0',
            'PAYMENTREQUEST_0_CURRENCYCODE' => Settings::get(APConstants::PAYMENT_PAYPAL_CURRENCY_CODE),
            'PAYMENTREQUEST_0_ITEMAMT' => $open_balance_amount
        );

        $item = array(
            'L_PAYMENTREQUEST_0_NAME0' => 'ClevverMail',
            'L_PAYMENTREQUEST_0_DESC0' => $invoice_id,
            'L_PAYMENTREQUEST_0_NUMBER0' => $invoice_id,
            'L_PAYMENTREQUEST_0_AMT0' => $open_balance_amount,
            'L_PAYMENTREQUEST_0_QTY0' => '1',
            'CUSTOM' => $invoice_id
        );

        // Insert to paypal transaction hist
        $primary_location_id = APUtils::getPrimaryLocationBy($customer_id);
        $paypal_tran_hist_id = $this->paypal_tran_hist_m->insert(
            array(
                'customer_id' => $customer_id,
                'invoice_id' => $invoice_id,
                'amount' => $params['amount'],
                'paypal_tran_fee' => $paypal_tran_fee,
                'paypal_tran_vat' => $paypal_tran_vat,
                'currency' => $params['currency'],
                'description' => $params['description'],
                "location_id" => $primary_location_id,
                'created_date' => now(),
                'status' => APConstants::PAYPAL_STATUS_NEW
            ));

        $paypal = $this->paypallib;
        $response = $paypal->request('SetExpressCheckout', $requestParams + $orderParams + $item);
        
        $message = "\n SetExpressCheckout Response: \n";
        $message .= "RequestParams: ".json_encode($requestParams)."\n";
        $message .= "OrderParams: ".json_encode($orderParams)."\n";
        $message .= "Item: ".json_encode($item)."\n";
        $message .= "Response: ".json_encode($response);
        $message .= "\n -------------------------------------------------------------------------------------- \n";

        log_audit_message(APConstants::LOG_ERROR, $message,false,'paypal_tracking_');

        if (is_array($response) && isset($response['ACK']) &&  $response['ACK'] == 'Success') { // Request successful
            $token = $response['TOKEN'];
            $result = array(
                'token' => urlencode($token)
            );
            $txn_id = $response['CORRELATIONID'];
            // Update staus of paypal transaction hist table
            $this->paypal_tran_hist_m->update_by_many(array(
                'id' => $paypal_tran_hist_id
            ), array(
                'last_updated_date' => now(),
                'txn_id' => $txn_id
            ));
            echo $token;
        } else {
            // Update staus of paypal transaction hist table
            $this->paypal_tran_hist_m->update_by_many(array(
                'id' => $paypal_tran_hist_id
            ),
                array(
                    'last_updated_date' => now(),
                    'status' => APConstants::PAYPAl_STATUS_ERROR,
                    'message' => json_encode($response)
                ));
            $result = array(
                'token' => ''
            );
            echo '';
        }
    }

    /**
     * Open credit card payment window screen
     */
    public function creditcard_payment()
    {
        $this->template->set_layout(FALSE);
        if ($_POST) {
        }
        $this->template->build('creditcard_payment');
    }

    /**
     * Make directly charge by credit
     */
    public function direct_charge()
    {
        $this->load->library('invoices/Invoices');
        $this->load->library('payment/payone');

        $customer_id = APContext::getCustomerCodeLoggedIn();
        // Baseline open balance report
        $open_balance_baseline = date('dmY', strtotime(APUtils::getLastDayOfPreviousMonth()));

        $customer = $this->customer_m->get($customer_id);
        $open_balance = APUtils::getCurrentBalance($customer_id);
        $invoice_id = APUtils::genetateReferenceForOpenBalance($customer_id);
        if ($open_balance < 0.01) {
            $this->success_output('');
        }

        $payment_method = '';
        if ($customer->invoice_type == '1') {
            $payment_method = "Credit Card";
        } else {
            $payment_method = "";
        }

        // By pass Invoice method
        if ($payment_method == "") {
            $this->error_output('The standard payment method is not Credit Card. The system can not process this request.');
            return;
        }

        if ($payment_method == "Credit Card") {
            // If open balance greater 0
            $result = $this->payone->authorize($customer_id, $invoice_id, $open_balance);

            // Check result
            if (!$result) {
                log_message(APConstants::LOG_ERROR, 'Deactivated customer (Payment 1):' . $customer_id);
                $this->customer_m->update_by_many(array(
                    "customer_id" => $customer_id
                ),
                    array(
                        "activated_flag" => APConstants::OFF_FLAG,
                        "deactivated_type" => APConstants::AUTO_INACTIVE_TYPE,
                        "payment_detail_flag" => APConstants::OFF_FLAG,
                        "last_updated_date" => now()
                    ));
                
                // update: convert registration process flag to customer_product_setting.
                CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'payment_detail_flag', APConstants::OFF_FLAG);

                // Send email confirm for user
                $data = array(
                    "slug" => APConstants::deactived_customer_notification,
                    "to_email" => $customer->email,
                    // Replace content
                    "full_name" => $customer->email,
                    "email" => $customer->email,
                    "site_url" => APContext::getFullBalancerPath()
                );
                // Send email
                MailUtils::sendEmailByTemplate($data);
            }
        }
        $this->success_output('');
    }

    /**
     * Create direct charge
     */
    function create_direct_charge_without_invoice()
    {
        $this->template->set_layout(FALSE);
        $customer_id = APContext::getCustomerCodeLoggedIn();

        $prepayment = $this->input->get_post("prepayment");
        $type = $this->input->get_post("type");
        $str_list_envelope_id = $this->input->get_post("list_envelope_id");
        $action_type = $this->input->get_post("action_type");
        $pre_total_amount = 0;
        $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
        $open_balance_due = $open_balance_data['OpenBalanceDue'];
        $open_balance_this_month = $open_balance_data['OpenBalanceThisMonth'];
        if ($prepayment == APConstants::ON_FLAG) {
            if ($type == 'shipping') {
                $pre_total_amount = CustomerUtils::estimateTotalPrepaymentRequest($customer_id) + $open_balance_due + $open_balance_this_month;
            } else if ($type == 'scanning') {
                $pre_total_amount = CustomerUtils::estimateTotalPrepaymentRequest($customer_id) + $open_balance_due + $open_balance_this_month;
            } else if ($type == 'add_more_postbox'){
                $location_id = $this->input->get_post('location_id');
                $amount = floatval($this->input->get_post("amount", 0));
                if ($amount > 0) {
                    $pre_total_amount = $amount;
                } else {
                    $estimate_cost = CustomerUtils::estimateNewPostboxCost(APConstants::BUSINESS_TYPE, $location_id, $customer_id);
                    $pre_total_amount = $estimate_cost + $open_balance_due + $open_balance_this_month;
                }
            } else {
                $pre_total_amount = $this->input->get_post("charge_amount");
            }
        } else {
            $pre_total_amount = $this->input->get_post("charge_amount");
        }
        $this->template->set('list_envelope_id', $str_list_envelope_id);
        $this->template->set('prepayment', $prepayment);
        $this->template->set('action_type', $action_type);
        $this->template->set('type', $type);
        $this->template->set('pre_total_amount', APUtils::number_format($pre_total_amount));
        
        // Delete postbox
        $total_postbox = 1;
        if ($action_type == 'delete_postbox') {
            // Count number of postbox
            $total_postbox = CustomerUtils::countNumberPostbox($customer_id);
            if ($total_postbox > 1) {
                $this->template->set('pre_total_amount', APUtils::number_format($open_balance_due));
            } else if ($total_postbox == 1) {
                $this->template->set('pre_total_amount', APUtils::number_format($open_balance_due + $open_balance_this_month));
            }
        }
        $this->template->set('total_postbox', $total_postbox);
        
        // Get envelope information
        $customer = $this->customer_m->get($customer_id);

        // Get customer currency information
        $currencies = $this->currencies_m->get_all_currencies_except_euro();
        $selected_currency = $this->customer_m->get_standard_setting_currency($customer_id);
        if ($selected_currency->currency_short == 'EUR') {
            $selected_currency = $this->currencies_m->get_by(array('currency_short' => 'USD'));
        }
        $decimal_separator = $this->customer_m->get_standard_setting_decimal_separator($customer_id);

        // $list_payments = $this->payment_m->get_payment_account($customer_id, 0, 10000);
        $list_payments = $this->payment_m->get_many_by_many(array(
            "customer_id" => $customer_id,
            "card_confirm_flag" => APConstants::ON_FLAG,
            "secure_3d_flag" => APConstants::ON_FLAG,
            "card_type <> 'A'" => NULL
        ));

        // Display the current page
        $this->template->set('customer_id', $customer_id);
        $this->template->set('customer', $customer);
        $this->template->set('list_payments', $list_payments);

        $this->template->set('currencies', $currencies);
        $this->template->set('selected_currency', $selected_currency);
        $this->template->set('decimal_separator', $decimal_separator);

        $this->template->build('customers/create_direct_charge_without_invoice');
    }

    /**
     * Create direct charge
     */
    function save_direct_charge_without_invoice()
    {
        $this->load->library('payment/payone');
        $this->load->model('payment/payment_tran_hist_m');
        $this->load->model('report/payone_transaction_hist_m');
        $this->template->set_layout(FALSE);
        $customer_id = APContext::getCustomerCodeLoggedIn();
        
        $prepayment = $this->input->get_post("prepayment");
        $type = $this->input->get_post("type");
        $str_list_envelope_id = $this->input->get_post("list_envelope_id");
        $action_type = $this->input->get_post("action_type");

        // Get envelope information
        $customer = $this->customer_m->get($customer_id);

        $this->form_validation->set_rules($this->validation_rules05);
        if ($this->form_validation->run()) {
            try {
                $amount = str_replace(',', '.', $this->input->get_post('tranAmount'));
                $invoice_id = APUtils::genetateReferenceForOpenBalance($customer_id);
                $credit_card_id = $this->input->get_post('cardID');

                // Check invoice method
                if ($customer->invoice_type == '2') {
                    $message = sprintf(lang('customer.notsupport_direct_charge_without_invoice'), $customer->email);
                    $this->error_output($message);
                    return;
                }

                // Validate amount
                if ($amount < 0) {
                    $message = lang('customer.save_direct_charge_without_invoice_validate');
                    $this->error_output($message);
                    return;
                }
                
                // Validate duplicate payment (will prevent it customer make same payment in 5 mins)
                $check_duplicate_payment = $this->payment_tran_hist_m->get_by_many(
                    array(
                        "customer_id" => $customer_id,
                        "tran_date > " => now() - 5 * 60,
                        "tran_type" => "authorize"
                    ));
                if (!empty($check_duplicate_payment)) {
                    // Check in payone_transaction_hist table
                    $reference = $check_duplicate_payment->reference;
                    $txaction = "appointed";
                    $check_payone_transaction_hist = $this->payone_transaction_hist_m->get_by_many(
                        array(
                            "customer_id" => $customer_id,
                            "reference" => $reference,
                            "txaction" => $txaction
                    ));
                    
                    if (!empty($check_payone_transaction_hist)) {
                        $message = lang('customer.duplicate_payone_payment_warning');
                        $this->error_output($message);
                        return;
                    }
                }

                // If open balance greater 0
                $result = $this->payone->authorize_bycreditcard($customer_id, $invoice_id, $amount, '', $credit_card_id);
                log_message(APConstants::LOG_ERROR, 'authorize_success_callback (Redirect URL DEBUG):' . $result);
                // Move sent email function when receive message OK from payone
                // Reference method payment > authorize_success_callback

                // Success case
                if ($result == APConstants::OFF_FLAG) {
                    $message = sprintf(lang('customer.save_direct_charge_without_invoice_success2'), APUtils::number_format($amount),
                        $customer->email);
                    $this->success_output($message);
                    return;
                } // Redirect case
                else if ($result != APConstants::ON_FLAG) {
                    echo json_encode(
                        array(
                            'status' => true,
                            'redirect' => true,
                            'message' => $result,
                            'result_chekc' => htmlentities($result)
                        ));
                    return;
                } // Fail
                else {
                    $message = sprintf(lang('customer.save_direct_charge_without_invoice_error'), $customer->email);
                    $this->error_output($message);
                    return;
                }
            } catch (Exception $e) {
                $message = sprintf(lang('customer.save_direct_charge_without_invoice_error'), $customer->email);
                $this->error_output($message);
                return;
            }
        } else {
            $errors = $this->form_validation->error_json();
            echo json_encode($errors);
            return;
        }
    }

    /**
     * Gets base url
     * @param unknown $referer
     */
    private function getBaseURLBy($referer)
    {
        $result = $referer;

        // replace prefix.
        $result = str_replace(array('https://', 'http://', '?'), array('', '', '/'), $result);

        $arrResult = explode('/', $result);
        return $arrResult[0];
    }

    public function get_result_content_check($redirect_url)
    {

        $html = file_get_html($redirect_url);
        $action_link = '';
        foreach ($html->find('form') as $element) {
            if ($element->name == 'acsredirect') {
                $action_link = $element->action;
                break;
            }
        }
        $post_data = array();
        foreach ($html->find('input') as $element) {
            if ($element->name == 'PaReq') {
                $post_data[] = "PaReq=" . ($element->value);
                break;
            }
        }

        foreach ($html->find('form') as $element) {
            if ($element->name == 'TermUrl') {
                $post_data[] = "TermUrl=" . ($element->value);
                break;
            }
        }

        echo $action_link, "<br>";
        echo implode('&', $post_data);

        $result = $this->submitFormWithCURL($action_link, 'POST', implode('&', $post_data));
        return ($result);
    }

    private function trace($var)
    {
        echo "<pre>";
        print_r(htmlentities($var));
        echo "</pre>";
    }

    private function submitFormWithCURL($action_link, $method, $post_data)
    {

        //create cURL connection
        $curl_connection = curl_init($action_link);

        //set options
        curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl_connection, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
        curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);

        //set data to be posted
        curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post_data);

        //perform our request
        $result = curl_exec($curl_connection);


        //close the connection
        curl_close($curl_connection);

        return $result;
    }
    
    /**
     * Display form for pre-payment
     */
    public function estimate_fee_pre_payment(){
        $this->template->set_layout(FALSE);
        
        $ids_input = $this->input->get_post('list_envelope_id');
        $list_envelope_id = explode(",", $ids_input);
        $type = $this->input->get_post('type');
        $action_type = $this->input->get_post('action_type');
        $customer_id = APContext::getCustomerCodeLoggedIn();
        
        $estimate_cost = 0;
        $location_id = '';
        $estimated_type = '';
        if($type == 'shipping'){
            $estimate_cost_obj = CustomerUtils::estimateShippingCost(APConstants::SHIPPING_SERVICE_NORMAL, 
                            $action_type, $list_envelope_id, $customer_id, true);
            $estimate_cost = $estimate_cost_obj['cost'];
            $estimated_type = $estimate_cost_obj['estimated_type'];
        } else if ($type == 'scanning'){
            $estimate_cost = CustomerUtils::estimateScanningCost($list_envelope_id, $action_type, $customer_id, true);
        } else if ($type == 'add_more_postbox'){
            $location_id = $this->input->get_post('location_id');
            $postbox_type = $this->input->get_post('postbox_type');
            $estimate_cost = CustomerUtils::estimateNewPostboxCost($postbox_type, $location_id, $customer_id);
        } else if ($type == 'change_postbox_type'){
            $postbox_id = $this->input->get_post('postbox_id');
            $postbox = $this->postbox_m->get_by('postbox_id', $postbox_id);
            $location_id = $postbox->location_available_id;
            $postbox_type = $this->input->get_post('postbox_type');
            $estimate_cost = CustomerUtils::estimateNewPostboxCost($postbox_type, $location_id, $customer_id);
        }
        $total_prepayment_amount = CustomerUtils::estimateTotalPrepaymentRequest($customer_id);
        $other_prepayment_amount = $total_prepayment_amount;
        if($type == 'shipping' || $type == 'scanning'){
            $other_prepayment_amount = $total_prepayment_amount - $estimate_cost;
        }
        
        $open_balance = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
        $currency = $this->customer_m->get_standard_setting_currency($customer_id);
        $decimal_separator = $this->customer_m->get_standard_setting_decimal_separator($customer_id);
        if (empty($currency)) {
            $currency = $this->currencies_m->get_by(array('currency_short' => 'EUR'));
        }
        
        $this->template->set('currency', $currency);
        $this->template->set('decimal_separator', $decimal_separator);
        $this->template->set('open_balance_due', $open_balance['OpenBalanceDue']);
        $this->template->set('open_balance_current_month', $open_balance['OpenBalanceThisMonth']);
        $this->template->set('estimate_cost', $estimate_cost);
        $this->template->set('other_prepayment_amount', $other_prepayment_amount);
        
        $this->template->set('action_type', $action_type);
        $this->template->set('estimated_type', $estimated_type);
        $this->template->set('list_envelope_id', $ids_input);
        $this->template->set('type', $type);
        $this->template->set('location_id', $location_id);
        
        $this->template->build('estimate_cost');
    }
    
    /**
     * confirm new email
     */
    public function confirm_new_email(){
        $activated_key = $this->input->get_post('key');
        
        if(!empty($activated_key)){
            $customer = $this->customer_m->get_by_many(array(
                "activated_key" => $activated_key,
            ));
            
            log_audit_message('error', 'Customer:' . count($customer) . '<br/>', false, 'confirm_new_email');
            
            if(!empty($customer)){
                $customer_id = $customer->customer_id;
                $this->customer_m->update_by_many(array(
                    "customer_id" => $customer_id
                ), array(
                    "deactivated_type" => '',
                    "email_confirm_flag" => APConstants::ON_FLAG
                ));

                //#1309: Insert customer history
                $history = [
                    'customer_id' => $customer_id,
                    'action_type' => APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_STATUS,
                    'created_by_id' => APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER,
                    'current_data' => APConstants::CUSTOMER_HISTORY_STATUS_ACTIVATED
                ];

                // update: convert registration process flag to customer_product_setting.
                CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'email_confirm_flag', APConstants::ON_FLAG);
                customers_api::insertCustomerHistory([$history]);
                // Sync email address to payone
                CustomerUtils::syncEmailToPayone($customer_id);
                
                $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
                $open_balance = $open_balance_data['OpenBalanceDue'];
                if ($open_balance < 0.1) {
                    // we dont have to tell the Customer that a valid credit Card with non working payment can reactivate the account.
                    // in most cases the Customer will Chose a Card that can handle the payment if it is valid
                    // Only reactivate if deactivated_type = auto
                    $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;
                    customers_api::reactivateCustomer($customer_id, $created_by_id);
                }
            }else{
                $this->logout();
                return;
            }
        }
        redirect('customers/login');
    }
    
    /**
     * confirm new email
     */
    public function mobile_adv_popup(){
        $this->template->set_layout(FALSE);
        $this->template->build('customers/mobile_adv_popup');
    }
    
     /**
     * confirm new email
     */
    public function update_mobile_adv_popup(){
        $this->template->set_layout(FALSE);
        $flag = $this->input->get_post('flag');
        if (empty($flag)) {
            $flag = 0;
        }
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $this->customer_m->update_by_many(array(
            "customer_id" => $customer_id
        ), array(
            "show_mobile_adv_flag" => $flag
        ));
        $this->success_output('');
        return;
    }
    
    public function direct_access(){
        // Check access key
        $access_token = $this->input->get_post('access_token', '');
        if($access_token){
            $customer = $this->customer_m->get_by_many(array(
                "direct_access_key" => $access_token,
                "direct_access_expired >=" => now() 
            ));
            
            $this->session->set_userdata(APConstants::SESSION_CUSTOMER_KEY, $customer);
            $this->session->unset_userdata(APConstants::DIRECT_ACCESS_CUSTOMER_KEY);
            $this->session->unset_userdata(APConstants::SESSION_SKIP_CUS_KEY);
            
            redirect('mailbox');
        }
        
        redirect('customers/login');
    }
    
    public function accept_terms_condition(){
        $customer = APContext::getCustomerLoggedIn();
        $this->customer_m->update_by("customer_id", $customer->customer_id,
        array(
            "accept_terms_condition_flag" => APConstants::ON_FLAG
        ));
        
        // active customer.
        $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;
        CustomerProductSetting::doActiveCustomer($customer->customer_id, $created_by_id);
        $this->success_output('');
        return;
    }
    
    /**
     * Register postbox address of enterprise user
     */
    public function register_address_postbox_enterprise_user(){
        $this->load->library('addresses/addresses_api');
        $this->load->library('account/account_api');
        
        $this->template->set_layout(false);
        $customer = APContext::getCustomerLoggedIn();
        
        if(!APContext::isPrimaryCustomerUser()){
            $this->error_output("You don't have permission.");
            return;
        }
        
        // Gets users of customer
        $list_all_user_not_activated = $this->customer_m->get_many_by_many(array(
            "parent_customer_id" => $customer->customer_id,
            "activated_flag" => APConstants::OFF_FLAG
        ));
        $list_user_not_activated = array();
        $postbox_list = $this->postbox_m->get_many_by_many(array(
            "customer_id" => $customer->customer_id,
            //"is_main_postbox" => 1,
            "deleted" => APConstants::OFF_FLAG
        ));
        
        foreach($postbox_list as $p){
            $tmp_customer = clone $customer;
            $tmp_customer->postbox = $p;
            $list_user_not_activated[] = $tmp_customer;
        }
        
        foreach($list_all_user_not_activated as $user){
            $user->postbox = $this->postbox_m->get_by('customer_id', $user->customer_id);
            
            // If can not get postbox will init new object
            if ($user->postbox != null) {
               $list_user_not_activated[] = $user;
            }
        }
        
        // gets location list.
        $locations = addresses_api::getLocationPublic();
        
        $this->template->set("customer", $customer);
        $this->template->set("locations", $locations);
        $this->template->set("list_user_not_activated", $list_user_not_activated);
        $this->template->build("register_address_postbox_enterprise");
    }
    
    /**
     * Save address postbox for enterprise user.
     */
    public function save_register_address_postbox_enterprise_user(){
        $this->template->set_layout(false);
        $this->load->library('account/account_api');
        
        if(!APContext::isPrimaryCustomerUser()){
            $this->error_output("You don't have permission.");
            return;
        }
        
        $customer_id = APContext::getCustomerCodeLoggedIn();
        if($_POST){
            $user_names = $this->input->post('user_name');
            $emails = $this->input->post('email');
            $customer_ids = $this->input->post('customer_id');
            $postbox_names = $this->input->post('postbox_name');
            $postbox_companies = $this->input->post('postbox_company');
            $locations = $this->input->post('location');
            $status = $this->input->post('status');
            $postbox_ids = $this->input->post('postbox_id');
            
            // validate email.
            $check_customer = $this->customer_m->get_many_by_many(array(
                "email in ('".implode("','", $emails)."')" => null,
                "status" => APConstants::OFF_FLAG,
                "parent_customer_id <>".$customer_id => null
            ));
            if(!empty($check_customer)){
                $list_error = array();
                $message = "The emails is existed as below.<br/>";
                foreach($check_customer as $c){
                    $message .= $c->email."<br/>";
                }
                $this->error_output($message);
                return;
            }
            
            // save information
            $index = 0;
            $warning_message = '';
            foreach($customer_ids as $user_id){
                if ($customer_id != $user_id) {
                    $user = $this->customer_m->get_by_many(array(
                        "customer_id" => $user_id,
                        "status" => APConstants::OFF_FLAG,
                        "parent_customer_id" => $customer_id
                    ));
                } else {
                    $user = $this->customer_m->get_by_many(array(
                        "customer_id" => $customer_id
                    ));
                }
                if($user){
                    $activeFlag = $status[$index];
                    // Update data to customer
                    
                    if(!empty($postbox_names[$index]) || !empty($postbox_companies[$index])){
                        $dataCustomerKey1       = array("postbox_name_flag", "name_comp_address_flag", "city_address_flag");
                        $dataCustomerVal1       = array($activeFlag, $activeFlag, $activeFlag);
                        // customers_api::updateCustomer($conditionCustomerKey1, $conditionCustomerVal1, $dataCustomerKey1, $dataCustomerVal1);
                        // update: convert registration process flag to customer_product_setting.
                        CustomerProductSetting::set_many($user->customer_id, APConstants::CLEVVERMAIL_PRODUCT, $dataCustomerKey1, $dataCustomerVal1);

                        $this->postbox_m->update_by_many(array(
                            "customer_id" => $user->customer_id,
                            "postbox_id" => $postbox_ids[$index]
                        ), array(
                            "postbox_name" => $postbox_names[$index],
                            "name" => $postbox_names[$index],
                            "company" => $postbox_companies[$index],
                            "location_available_id" => $locations[$index]
                        ));
                    } else{
                        $warning_message .=  'Can not activated this account: '.$emails[$index].". Please input name or company of postbox.<br/>";
                    }
                    
                    // Validate default email address
                    if ($activeFlag == APConstants::ON_FLAG 
                            && $emails[$index] == $user->customer_code.'@clevvermail.com') {
                        // do not completed email confirm in this case.
                        CustomerProductSetting::set($user->customer_id, APConstants::CLEVVERMAIL_PRODUCT, APConstants::activate_email_confirm_flag, 0);
                        $warning_message .=  'Can not activated this account: '.$emails[$index]." with default email address. Please use the user email address.<br/>";
                    } else {
                        $this->customer_m->update_by_many(array(
                            "customer_id" => $user->customer_id,
                            "parent_customer_id" => $customer_id
                        ), array(
                            "email" => $emails[$index],
                            "user_name" => $user_names[$index],
                            "activated_flag" => $status[$index],
                        ));
                        
                        // activate customer
                        $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;
                        CustomerProductSetting::doActiveCustomer($customer_id, $created_by_id);
                        
                        // do active customer
                        // CustomerProductSetting::doActiveCustomerWithPhoneProduct($customer_id);
                        
                        // start case for user enterprise.
                        CaseUtils::start_verification_case($user->customer_id, true);
                    }
                    
                    // If this is main user account, set product default for it
                    if ($customer_id == $user_id) { 
                        account_api::add_postbox_to_user($customer_id, $customer_id, $postbox_ids[$index]);
                    }
                }
                
                $index ++;
            }
            if (empty($warning_message)) {
                // remark completed register 10 postboxes window.
                CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, APConstants::activate_10_postbox_enterprise_customer, APConstants::ON_FLAG);
            }

            if (!empty($warning_message)) {
                $this->success_output($warning_message);
            } else {
                $this->success_output("You have updated information sucessfully.");
            }
            return;
        }
    }
    
    /**
     * load price list for registration postbox name
     */
    public function load_price_list(){
        $this->template->set_layout(false);
        
        $this->template->build('load_price_list');
    }
    
    /**
     * load pricing list detail
     */
    public function load_price_list_detail()
    { 
        $this->template->set_layout(FALSE);
        $this->load->library("price/price_api");
        $this->load->library("customers/customers_api");
        $this->lang->load('addresses/address');
        
        // Gets customerid logged in.
        $customerId = APContext::getCustomerCodeLoggedIn();

        $postbox = $this->postbox_m->get_by(array(
            'customer_id' => $customerId,
            'is_main_postbox' => 1,
            'first_location_flag' => 1
        ));
        
        $account_type = $this->input->get_post('type');

        $locationId = ($postbox) ? $postbox->location_available_id : 0;
        $highline = 0;
        if($this->input->get_post('location_id')){
            $locationId = $this->input->get_post('location_id');
            $highline = 1;
        }
        // Gets customer infor.
        $account = customers_api::getCustomerByID($customerId);

        $locate = $this->location_m->get_many_by_many(array(
            'public_flag' => 1
        ));

        $this->template->set('locate', $locate);
        
        // Get don gia cua tat ca cac loai account type
        $pricing_map = price_api::getPricingMapByLocationId($locationId);

        $this->template->set('account_type', $account_type);
        $this->template->set('locationId', $locationId);
        $this->template->set('highline', $highline);
        $this->template->set('is_price_list_detail', '1');
        $this->template->set('pricing_map', $pricing_map);
        $this->template->set('account', $account);
        
        if($this->input->get_post('hide_flag') == 1){
            $this->template->build('load_price_list_detail');
        }else{
            $this->template->build('price_list_detail');
        }
    }
    
    /**
     * load select product page for registration
     */
    public function load_select_product_register(){
        $this->template->set_layout(false);
        
        $this->template->build('load_select_product_register');
    }
    
    /**
     * load email confirmation page.
     */
    public function load_email_confirmation(){
        $this->template->set_layout(false);
        
        $this->template->build('load_email_confirmation');
    }
    
    
    /**
     * load another clevver product page.
     */
    public function add_another_clevver_product(){
        $this->load->library('addresses/addresses_api');
        $this->load->library('price/price_api');
        
        $this->template->set_layout(false);
        $this->load->model('phones/phone_number_m');
        
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $customer_id = APContext::getCustomerCodeLoggedIn();
            
        // Gets postbox data
        $postbox_list = $this->postbox_m->get_many_by_many(array(
            "(name is not null or company is not null)" => null,
            "customer_id" => $customer_id
        ));
        $total_postbox = count($postbox_list);
        if($_POST && $this->is_ajax_request()){
            $input_paging = $this->get_paging_input();
            $input_paging ['limit'] = 100;
            
            // Gets phone data.
            $array_condition = array();
            $array_condition ["phone_number.parent_customer_id"] = $parent_customer_id;
            $query_result = $this->phone_number_m->get_number_paging($array_condition, 0, 100, '', '');
            
            // Get output response
            $response = $this->get_paging_output($total_postbox + $query_result['total'], $input_paging ['limit'], $input_paging ['page']);

            $i = 0;
            // calculate phone data
            foreach ($query_result['data'] as $row) {
                $response->rows [$i] ['id'] = $row->id;
                $response->rows [$i] ['cell'] = array(
                    $i,
                    "Phone",
                    "Location: ".$row->country_name,
                    "Monthly",
                    $row->customer_id,
                    $row->id,
                );
                $i++;
            }
            
            // calculate postbox data.
            foreach($postbox_list as $p){
                $postbox_name = $p->postbox_name;
                $location = addresses_api::getLocationByID($p->location_available_id);
                // $price = price_api::getPricingModelByLocationID($p->location_available_id);
                $price = price_api::getPricingModelByCusotomerAndLocationID($customer_id, $p->location_available_id);
                $postbox_fee = $price[$p->type]['postbox_fee'];
                if($p->type == APConstants::FREE_TYPE){
                    $postbox_fee = "6 months free - ". $price[$p->type]['postbox_fee_as_you_go']. ' EUR after*';
                }
                
                $response->rows [$i] ['id'] = $p->postbox_id;
                $response->rows [$i] ['cell'] = array(
                    $i,
                    "Postbox, location: ". $location->location_name,
                    "postbox: ".$postbox_name,
                    "Monthly",
                    $postbox_fee,
                    $p->postbox_id,
                );
                $i++;
            }
            
            echo json_encode($response);
            return;
        }
        
        // save add another product window flag
        CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'add_another_product_window', APConstants::ON_FLAG);
        
        $this->template->set('postbox_count', $total_postbox);
        $this->template->build('add_another_clevver_product');
    }
    
    /**
     * save_selection_product_register
     */
    public function save_selection_product_register(){
        $product_type = $this->input->get_post('product_type');
        $customer_id = APContext::getCustomerCodeLoggedIn();
        customers_api::save_selection_product_register($customer_id, $product_type);
        $this->success_output("");
        return;
    }
}

