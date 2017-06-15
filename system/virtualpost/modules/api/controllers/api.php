<?php defined('BASEPATH') or exit('No direct script access allowed');

class API extends API_Controller {
    // Rule for save external record payment
    private $validation_direct_charge_without_invoice = array(
        array(
            'field' => 'card_id',
            'label' => 'credit card',
            'rules' => 'required|trim'
        ),
        array(
            'field' => 'tran_amount',
            'label' => 'amount',
            'rules' => 'required|trim'
        )
    );

    public function __construct()
    {
        // set error repoting to false for proper formatting of json data
        error_reporting(E_ALL);
        ini_set('display_errors', '1');
        //error_reporting(0);

        parent::__construct();

        $this->load->library('Payone_lib');
        $this->load->config('payone');

        $this->load->model(array(
            'account/account_m',

            'customers/customer_m',
            'cloud/customer_cloud_m',

            'addresses/location_m',
            'addresses/location_envelope_types_m',
            'addresses/customers_address_hist_m',
            'addresses/customer_location_m',
            'addresses/customers_address_m',
            'addresses/customers_forward_address_m',

            'email/email_m',

            'payment/payment_job_hist_test_m',
            'payment/payment_job_hist_m',
            'payment/payone_transaction_hist_test_m',
            'payment/payone_tran_hist_m',
            'payment/external_tran_hist_m',
            'payment/payment_tran_hist_m',
            'payment/payment_m',

            'price/pricing_m',

            'mailbox/envelope_customs_detail_m',
            'mailbox/envelope_customs_m',
            'mailbox/postbox_setting_m',
            'mailbox/postbox_m',

            'invoices/invoice_detail_m',
            'invoices/invoice_summary_m',

            'report/payone_transaction_hist_m',

            'settings/currencies_m',
            'settings/terms_service_m',
            'settings/countries_m',

            'scans/envelope_package_m',
            'scans/envelope_completed_m',
            'scans/envelope_file_m',
            'scans/envelope_m',
            'scans/envelope_pdf_content_m',

            'users/mobile_session_m',
        ));

        ci()->load->library(array(
            'addresses/addresses_api',
            'api/mobile_api',
            'scans/scans_api',
            'mailbox/mailbox_api',
            'shipping/shipping_api',
            'customers/customers_api',
            "settings/settings_api",
            'shipping/ShippingConfigs',
            "api/mobile_cases_api",
            "S3",
            'cloud/cloud_api',
        ));

        $this->lang->load(array(
            'account/account',
            'mailbox/mailbox',
            'api',
            "payment/payment",
            'addresses/address',
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
     * Check login. If credentials are correct then return authenticated <br> Else If email exists then return email_exists <br> Else return
     * invalid_credentials. <br>
     *
     * @uses $_POST['email']
     * @uses $_POST['password']
     * @return array {'code' => 1001, 'message' => 'authenticated', 'result' => array(), 'session_key' => '123456'} or <br> {'code' => 1002, 'message'
     *         => 'email_exists', 'result' => array()} or <br> {'code' => 1003, 'message' => 'invalid_credentials', 'result' => array()}
     */
    public function login()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $partner_login_id = $this->input->post('login_id');
        $partner_login_type = $this->input->post('login_type');

        // check login by google or facebook.
        $customer = null;
        if(!empty($partner_login_id)){
            // get customer by login id.
            $account_setting = AccountSetting::getSettingByValue($partner_login_type, $partner_login_id);
            if(!empty($account_setting)){
                $customer = $this->customer_m->get_by_many(array(
                    "customer_id" => $account_setting->parent_customer_id,
                    'status' => APConstants::OFF_FLAG
                ));
            }
        }

        if(empty($customer)) {
            if (empty($email) || empty($password)) {
                $data = array(
                    'code' => 2000,
                    'message' => 'invalid or missing required parameters',
                    'result' => ''
                );
                $this->api_error_output($data);
                exit();
            }

            // Get user information by email and password
            $customer = $this->customer_m->get_active_customer_by_account($email, md5($password));
        }

        //if customer is exist and is not deleted yet
        if ($customer) {
            $new_session_key = APUtils::generateRandom(64);
            $new_session_key = md5($new_session_key);

            // Delete all old activity
            $this->mobile_session_m->delete_by_many(array(
                'last_activity <' => now() - API_Controller::EXPIRED_TIME
            ));
            // Build session key
            $this->mobile_session_m->insert(array(
                "session_key" => $new_session_key,
                "ip_address" => $this->getIPAddress(),
                "user_agent" => $this->getUserAgent(),
                "last_activity" => now(),
                "user_data" => json_encode($customer)
            ));

            $data = array(
                'code' => 1001,
                'message' => 'authenticated',
                'result' => $customer,
                'session_key' => $new_session_key
            );
            $this->api_success_output($data);
            exit();
        } else {
            // Get user information by email
            $customer = $this->customer_m->get_active_customer_by_email($email);

            // If customer email exists
            if ($customer) {
                $data = array(
                    'code' => 1002,
                    'message' => lang('wong_password_message'),
                    'result' => $customer
                );
                $this->api_error_output($data);
                exit();
            } else {
                $data = array(
                    'code' => 1003,
                    'message' => lang('account_is_not_existed_message'),
                    'result' => ''
                );
                $this->api_error_output($data);
                exit();
            }
        }
    }

    /**
     * Logout
     *
     * @return array {'code' => 1001, 'message' => 'logout successfully'}
     */
    public function logout()
    {
        // Get session key from header
        $session_key = $this->getSessionKey();

        // Delete all old activity
        $this->mobile_session_m->delete_by_many(array(
            'session_key' => $session_key
        ));

        $data = array(
            'code' => 1001,
            'message' => 'logout successfully'
        );
        $this->api_success_output($data);
    }

    /**
     * Register customer
     *
     * @uses $_POST['email']
     * @uses $_POST['password']
     * @return array {'code' => 1004, 'message' => 'user_created', 'result' => array()} or <br> {'code' => 1014, 'message' => 'user_already_exists',
     *         'result' => ''}
     */
    public function register()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        if (empty($email) || empty($password)) {
            $data = array(
                'code' => 2000,
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            exit();
        }

        $customer = $this->customer_m->get_active_customer_by_email($email);
        if ($customer) {
            $data = array(
                'code' => 1014,
                'message' => 'user_already_exists',
                'result' => ''
            );
            $this->api_error_output($data);
            exit();
        } else {
            $partner_login_id = $this->input->post('login_id');
            $partner_login_type = $this->input->post('login_type');

            // do register new user.
            $result = customers_api::registerNewCustomer($email, $password, '', $partner_login_id, $partner_login_type);
            $customer_id = $result['customer_id'];
            $postbox_id = $result['postbox_id'];

            // Generate new session key
            $new_session_key = APUtils::generateRandom(64);
            $new_session_key = md5($new_session_key);

            // Delete all old activity
            $this->mobile_session_m->delete_by_many(array(
                'last_activity <' => now() - API_Controller::EXPIRED_TIME
            ));
            // Build session key
            $customer = $this->customer_m->get_by_many(array(
                "customer_id" => $customer_id
            ));
            $this->mobile_session_m->insert(array(
                "session_key" => $new_session_key,
                "ip_address" => $this->getIPAddress(),
                "user_agent" => $this->getUserAgent(),
                "last_activity" => now(),
                "user_data" => json_encode($customer)
            ));

            $data = array(
                'code' => 1004,
                'message' => 'user_created',
                'result' => array(
                    'customer_id' => $customer_id,
                    'postbox_id' => $postbox_id
                ),
                'session_key' => $new_session_key
            );
            $this->api_success_output($data);
            exit();
        }
    }

    /**
     * Get all avaialable postbox locations void
     *
     * @return array {'code' => 1005, 'message' => 'list_available_locations', 'result' => array()}
     */
    public function all_postbox_locations()
    {
        //$all_locations = $this->location_m->get_all();
        $all_locations = $this->location_m->get_location_paging(array('location.public_flag' => 1), 0, 100000, '');
        $data = array(
            'code' => 1005,
            'message' => 'list_available_locations',
            'result' => $all_locations['data']
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Save postbox name Save postbox name
     *
     * @uses $_POST['email']
     * @uses $_POST['postbox_id']
     * @uses $_POST['postbox_name']
     * @uses $_POST['location_available_id']
     * @uses $_POST['address_name']
     * @uses $_POST['address_company_name']
     * @return array {'code' => 1006, 'message' => 'save_postboxname_success', 'result' => array()}
     */
    public function save_postboxname()
    {
        $customer = MobileContext::getCustomerLoggedIn();
        $email = $customer->email;
        $postbox_name = $this->input->post('postbox_name');
        $postbox_id = $this->input->post('postbox_id');
        $location_available_id = $this->input->post('location_available_id');
        $name = $this->input->post('address_name');
        $company = $this->input->post('address_company_name');

        if (empty($email) || empty($customer) || empty($postbox_name) || empty($location_available_id)) {
            $data = array(
                'code' => 2000,
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            exit();
        }
        if ($customer) {
            // New customer
            $customer_id = $customer->customer_id;

            if ($postbox_id) {
                $key = "postbox_id";
                $value = $postbox_id;
            } else {
                $key = "is_main_postbox";
                $value = 1;
            }

            // fix bug 1407
            $this->postbox_m->update_by_many(array(
                "customer_id" => $customer_id,
                "is_main_postbox" => 1
            ), array(
                "postbox_code" => '',
                "name" => '',
                "company" => ''
            ));

            // Update main postbox
            $this->postbox_m->update_by_many(array(
                "customer_id" => $customer_id,
                $key => $value
            ), array(
                "postbox_name" => $postbox_name,
                "location_available_id" => $location_available_id,
                "name" => $name,
                "company" => $company
            ));

            // Update data to customer
            $this->customer_m->update_by_many(array(
                "customer_id" => $customer_id
            ), array(
                "postbox_name_flag" => APConstants::ON_FLAG,
                "name_comp_address_flag" => APConstants::ON_FLAG,
                "city_address_flag" => APConstants::ON_FLAG
            ));
            // update: convert registration process flag to customer_product_setting.
            CustomerProductSetting::set_many($customer_id, APConstants::CLEVVERMAIL_PRODUCT, array(
                "postbox_name_flag",
                "name_comp_address_flag",
                "city_address_flag"
            ), array(
                APConstants::ON_FLAG,
                APConstants::ON_FLAG,
                APConstants::ON_FLAG
            ));

            // Update customer information
            $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
            $open_balance = $open_balance_data['OpenBalanceDue'];
            if ($open_balance <= 0.1) {
                // we dont have to tell the Customer that a valid credit Card with non working payment can reactivate the account.
                // in most cases the Customer will Chose a Card that can handle the payment if it is valid
                // Only reactivate if deactivated_type = auto
                $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;
                customers_api::reactivateCustomer($customer_id, $created_by_id);
            }

            $data = array(
                'code' => 1006,
                'message' => 'save_postboxname_success',
                'result' => ''
            );
            $this->api_success_output($data);
            exit();
        }
    }

    /**
     * Save postbox name Save postbox name
     *
     * @uses $_POST['postbox_id']
     * @uses $_POST['postbox_name']
     * @uses $_POST['postbox_type']
     * @uses $_POST['location_available_id']
     * @uses $_POST['address_name']
     * @uses $_POST['address_company_name']
     * @return array {'code' => 1006, 'message' => 'save_postboxname_success', 'result' => array()}
     */
    public function update_postbox()
    {
        $customer = MobileContext::getCustomerLoggedIn();
        $email = $customer->email;
        $postbox_name = $this->input->post('postbox_name');
        $postbox_id = $this->input->post('postbox_id');
        $postbox_type = $this->input->post('postbox_type');
        //$location_available_id = $this->input->post('location_available_id');
        $name = $this->input->post('address_name');
        $company = $this->input->post('address_company_name');
        $curr_postbox = $this->postbox_m->get($postbox_id);

        if (empty($email) || empty($customer) || empty($postbox_name) || empty($curr_postbox)) {
            $data = array(
                'code' => 2000,
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            exit();
        }
        $customer_id = $customer->customer_id;

        // added #668: can not change bussiness postbox type if additional location.
        $primary_location = APUtils::getPrimaryLocationBy($customer_id);



        // $current_account_type = $customer->account_type;
        $current_account_type = $curr_postbox->type;
        $new_account_type = $postbox_type;

        // Update main postbox
        $this->postbox_m->update_by_many(array(
            "customer_id" => $customer_id,
            "postbox_id" => $postbox_id
        ), array(
            "postbox_name" => $postbox_name,
            //"location_available_id" => $location_available_id,
            "name" => $name,
            "company" => $company
        ));

        // Update data to customer
        $this->customer_m->update_by_many(array(
            "customer_id" => $customer_id
        ), array(
            "postbox_name_flag" => APConstants::ON_FLAG,
            "name_comp_address_flag" => APConstants::ON_FLAG,
            "city_address_flag" => APConstants::ON_FLAG
        ));
        // update: convert registration process flag to customer_product_setting.
        CustomerProductSetting::set_many($customer_id, APConstants::CLEVVERMAIL_PRODUCT, array(
            "postbox_name_flag",
            "name_comp_address_flag",
            "city_address_flag"
        ), array(
            APConstants::ON_FLAG,
            APConstants::ON_FLAG,
            APConstants::ON_FLAG
        ));

        // Check if current account type and new account type is same
        if ($current_account_type == $new_account_type) {
            $data = array(
                'code' => 1006,
                'message' => 'save_postboxname_success',
                'result' => ''
            );
            $this->api_success_output($data);
            return;
        }

        if ($primary_location != $curr_postbox->location_available_id) {
            $data = array(
                'code' => 2000,
                'message' => lang('can_not_change_if_is_additional_location'),
                'result' => ''
            );
            $this->api_error_output($data);
            return;
        }

        if ($customer->activated_flag != APConstants::ON_FLAG) {
            $data = array(
                'code' => 2000,
                'message' => lang('can_not_change_postbox_account_not_activated'),
                'result' => ''
            );
            $this->api_error_output($data);
            return;
        }

        // #1012 Add pre-payment process
        if ($new_account_type == APConstants::BUSINESS_TYPE) {
            $location_id = $curr_postbox->location_available_id;
            $check_prepayment_data = CustomerUtils::checkApplyAddPostboxPrepayment($new_account_type, $location_id, $customer_id);
            if ($check_prepayment_data['prepayment'] == true) {
                $check_prepayment_data['status'] = FALSE;
                $data = array(
                    'code' => 1033,
                    'message' => 'The prepayment required to process',
                    'result' => $check_prepayment_data
                );
                $this->api_error_output($data);
                return;
            }
        }

        // If change from other to free
        if ($current_account_type === APConstants::FREE_TYPE) {

            // Update main postbox type of this account
            $this->postbox_m->update_by_many(array(
                "customer_id" => $customer_id,
                // #472
                //"postbox_id" => $main_postbox->postbox_id
                "postbox_id" => $curr_postbox->postbox_id
            ), array(
                "type" => $new_account_type,
                "plan_deleted_date" => null,
                "updated_date" => now(),
                "deleted" => 0,
                "apply_date" => APUtils::getCurrentYearMonthDate()
            ));

            // Delete postbox id fee and recalcalculate
            $this->load->model('mailbox/postbox_fee_month_m');
            $this->load->library('invoices/Invoices');
            $target_month = APUtils::getCurrentMonthInvoice();
            $target_year = APUtils::getCurrentYearInvoice();

            // Reset viec tinh phi cho account nay
            $this->postbox_fee_month_m->delete_by_many(array(
                "postbox_id" => $postbox_id,
                "year_month" => $target_year . $target_month,
                "postbox_fee_flag" => APConstants::ON_FLAG
            ));
            $this->invoices->calculate_invoice($customer_id);

            // Send email when new account type is business
            if ($new_account_type == APConstants::BUSINESS_TYPE) {
                // Get main postbox
                // $postbox_name = $main_postbox->postbox_name;
                // $name = $main_postbox->name;
                // $company = $main_postbox->company;
                $postbox_name = $curr_postbox->postbox_name;
                $name = $curr_postbox->name;
                $company = $curr_postbox->company;

                $to_email = Settings::get(APConstants::MAIL_CONTACT_CODE);
                $data = array(
                    "slug" => APConstants::new_business_account_notification,
                    "to_email" => $to_email,
                    // Replace content
                    "user_name" => $customer->user_name,
                    "email" => $customer->email,
                    "postbox_name" => $postbox_name,
                    "name" => $name,
                    "company" => $company,
                    "account_type" => "Business"
                );
                // Send email
                MailUtils::sendEmailByTemplate($data);

                // Send email to customer
                $data = array(
                    "slug" => APConstants::new_business_account_notification_for_customer,
                    "to_email" => $customer->email,
                );
                // Send email
                MailUtils::sendEmailByTemplate($data);
            }

            // Update account type
            APUtils::updateAccountType($customer_id);

            // revert envelopes of current month
            APUtils::revert_all_envelopes($customer_id, $curr_postbox->postbox_id, APUtils::getCurrentYear(), APUtils::getCurrentMonth());

            // #615 Calculate postbox fee after insert new postbox
            Events::trigger('cal_postbox_invoices_directly', array(
                'customer_id' => $customer_id
            ), 'string');
        } else {
            $change_date = APUtils::getFirstDayOfNextMonth();

            // Update main postbox type of this account
            $this->postbox_m->update_by_many(array(
                "customer_id" => $customer_id,
                "postbox_id" => $curr_postbox->postbox_id
            ), array(
                "new_postbox_type" => $new_account_type,
                "plan_date_change_postbox_type" => $change_date,
                "updated_date" => now(),
                "apply_date" => APUtils::getCurrentYearMonthDate()
            ));

            $message = lang('change_account_info_message');
            $new_account_name = lang('account_type_' . $new_account_type);
            $message = sprintf($message, $new_account_name, APUtils::displayDate($change_date));

            // #477: comment out.
            // Update account type
            // APUtils::updateAccountType($customer_id);
            // only update acocunt type:
            APUtils::updateOnlyAccountType($customer_id);

            // #615 Calculate postbox fee after insert new postbox
            Events::trigger('cal_postbox_invoices_directly', array(
                'customer_id' => $customer_id
            ), 'string');
        }



        $data = array(
            'code' => 1006,
            'message' => 'save_postboxname_success',
            'result' => ''
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Add postbox. adds a postbox to customer account.
     *
     * @uses $_POST['email']
     * @uses $_POST['location']
     * @uses $_POST['postboxname']
     * @uses $_POST['accounttype']
     * @uses $_POST['customername']
     * @uses $_POST['companyname']
     * @return array {'code' => 1007, 'message' => 'postbox_added', 'result' => array()}
     */
    public function add_postbox()
    {
        ci()->load->library('account/account_api');
        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);
        $customer_id = $customer->customer_id;

        $location = $this->input->post('location');
        $postname = $this->input->post('postboxname');
        $account_type = $this->input->post('accounttype');
        $custname = $this->input->post('customername');
        $company = $this->input->post('companyname');

        if (empty($email) || empty($customer) || empty($location) || empty($postname) || empty($account_type)) {
            $data = array(
                'code' => 2000,
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            exit();
        }

        $apply_date = null;
        if ($account_type === APConstants::PRIVATE_TYPE || $account_type === APConstants::BUSINESS_TYPE) {
            $apply_date = APUtils::getCurrentYearMonthDate();
        }

        // #1012 Add pre-payment process
        if ($account_type == APConstants::BUSINESS_TYPE) {
            $check_prepayment_data = CustomerUtils::checkApplyAddPostboxPrepayment($account_type, $location, $customer_id);
            if ($check_prepayment_data['prepayment'] == true) {
                $check_prepayment_data['status'] = FALSE;
                $data = array(
                    'code' => 1033,
                    'message' => 'The prepayment required to process',
                    'result' => $check_prepayment_data
                );

                $this->api_error_output($data);
                return;
            }
        }

        $postbox_id = account_api::addPostbox($customer, $account_type, $location, $custname, $company, $postname);

        $data = array(
            'code' => 1007,
            'message' => 'postbox_added',
            'result' => $postbox_id
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Save shipment and invoice address. Add / Update shipment and invoice address to customer account. <br /> can update address seperately by
     * setting parameter "update_address_for" (shipment , invoicing, both)
     *
     * @uses $_POST['email']
     * @uses $_POST['update_address_for']
     * @uses $_POST['shipment_address_name']
     * @uses $_POST['shipment_company']
     * @uses $_POST['shipment_street']
     * @uses $_POST['shipment_postcode']
     * @uses $_POST['shipment_city']
     * @uses $_POST['shipment_region']
     * @uses $_POST['shipment_country']
     * @uses $_POST['invoicing_address_name']
     * @uses $_POST['invoicing_company']
     * @uses $_POST['invoicing_street']
     * @uses $_POST['invoicing_postcode']
     * @uses $_POST['invoicing_city']
     * @uses $_POST['invoicing_region']
     * @uses $_POST['invoicing_country']
     * @return array {'code' => 1008, 'message' => 'shipping_invocing_address_added', 'result' => array()}
     */
    public function save_address()
    {
    	$this->load->library('invoices/invoices_api');

        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);
        $customer_id = $customer->customer_id;

        // get params
        $invoicing_address_name = $this->input->post('invoicing_address_name');
        $invoicing_company = $this->input->post('invoicing_company');
        $invoicing_street = $this->input->post('invoicing_street');
        $invoicing_postcode = $this->input->post('invoicing_postcode');
        $invoicing_city = $this->input->post('invoicing_city');
        $invoicing_region = $this->input->post('invoicing_region');
        $invoicing_country = $this->input->post('invoicing_country');
        $invoicing_phone_number = $this->input->post('invoicing_phone_number');

        if (empty($email) || empty($customer) || (empty($invoicing_address_name) && empty($invoicing_company))
                || empty($invoicing_street) || empty($invoicing_postcode) || empty($invoicing_city) || empty($invoicing_country)) {
            $data = array(
                'code' => 2000,
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            return;
        }

        // Gets customer address infor.
        $check = $this->customers_address_m->get_by('customer_id', $customer_id);

        // prepare address information.
        $data = array(
            'invoicing_address_name' => $invoicing_address_name,
            'invoicing_company' => $invoicing_company,
            'invoicing_street' => $invoicing_street,
            'invoicing_postcode' => $invoicing_postcode,
            'invoicing_city' => $invoicing_city,
            'invoicing_region' => $invoicing_region,
            'invoicing_country' => $invoicing_country,
            'invoicing_phone_number' => $invoicing_phone_number,
            "customer_id" => $customer_id
        );

        if ($check) {
            // Get country entity
            $invoicing_country_entity = $this->countries_m->get($invoicing_country);
            $data['eu_member_flag'] =  !empty($invoicing_country_entity) ? $invoicing_country_entity->eu_member_flag : 0;

            // the VAT check has to be redone, therefore the sign erased.
            if ($invoicing_company != $check->invoicing_company) {
                // Update VAT sign (reset vat number in customers)
                $data['vat_number'] = '';
            }

            // update invoice address
            $this->customers_address_m->update($customer_id, $data);

            // Update data to customer
            $this->customer_m->update_by_many(array(
                "customer_id" => $customer_id
            ), array(
                //"shipping_address_completed" => APConstants::ON_FLAG,
                "invoicing_address_completed" => APConstants::ON_FLAG
            ));

            $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
            $open_balance = $open_balance_data['OpenBalanceDue'];
            if ($open_balance <= 0.1) {
                // we dont have to tell the Customer that a valid credit Card with non working payment can reactivate the account.
                // in most cases the Customer will Chose a Card that can handle the payment if it is valid
                // Only reactivate if deactivated_type = auto
                $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;
                CustomerProductSetting::doActiveCustomer($customer_id, $created_by_id);
            }
        } else {
            $this->customers_address_m->insert($data);

            // Update data to customer
            $this->customer_m->update_by_many(array(
                "customer_id" => $customer_id
            ), array(
                //"shipping_address_completed" => APConstants::ON_FLAG,
                "invoicing_address_completed" => APConstants::ON_FLAG
            ));

            $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
            $open_balance = $open_balance_data['OpenBalanceDue'];
            if ($open_balance <= 0.1) {
                // we dont have to tell the Customer that a valid credit Card with non working payment can reactivate the account.
                // in most cases the Customer will Chose a Card that can handle the payment if it is valid
                // Only reactivate if deactivated_type = auto
                $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;
                CustomerProductSetting::doActiveCustomer($customer_id, $created_by_id);
            }
        }

        // update: convert registration process flag to customer_product_setting.
        CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, APConstants::activate_invoicing_address_completed, APConstants::ON_FLAG);

        // update invoice VAT of customer
        invoices_api::update_invoice_vat($customer_id, APUtils::getCurrentYear(), APUtils::getCurrentMonth(), null);

        // trigger start case.
        CaseUtils::start_verification_case($customer_id);

        $data = array(
            'code' => 1008,
            'message' => 'shipping_invocing_address_added',
            'result' => ''
        );
        $this->api_success_output($data);
        return;
    }

    /**
     * Forgot password Checks if customer email exists and is not deleted then generate random password and email to customer.
     *
     * @uses $_POST['email']
     * @return array {'code' => 1009, 'message' => 'customer_not_register', 'result' => array()} *or* <br> {'code' => 1010, 'message' =>
     *         'customer_deleted', 'result' => array()} or <br> {'code' => 1011, 'message' => 'Email containing temporary password sent', 'result' =>
     *         array()} *or* <br> {'code' => 1012, 'message' => 'Email not sent', 'result' => array()}
     */
    public function forgot_pass()
    {
        $customer = new stdClass();
        $customer->email = '';

        $email = $this->input->post('email');

        if (empty($email)) {
            $data = array(
                'code' => 2000,
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            exit();
        }

        // Get user information by email
        $customer = $this->customer_m->get_by_many(array(
            "email" => $email
        ));

        // Check if customer is not registered
        if (empty($customer)) {
            $data = array(
                'code' => 1009,
                'message' => 'customer_not_register',
                'result' => 'error'
            );
            $this->api_error_output($data);
            exit();
        }

        // Check if customer is delete
        if ($customer->status == '1') {
            $data = array(
                'code' => 1010,
                'message' => 'customer_deleted',
                'result' => 'error'
            );
            $this->api_error_output($data);
            exit();
        }

        // Reset password
        $new_pass = APUtils::generateRandom(8);
        $this->customer_m->update_by_many(array(
            "email" => $email
        ), array(
            "password" => md5($new_pass)
        ));

        // Build email content
        // Send email confirm for user
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
        $sent_status = MailUtils::sendEmailByTemplate($send_email_data);

        if ($sent_status) {
            $code = 1011;
            $message = 'Email containing temporary password sent';
        } else {
            $code = 1012;
            $message = 'Email not sent';
        }

        $data = array(
            'code' => $code,
            'message' => $message,
            'result' => $sent_status
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Check Registration Status Check if customer is active, filled shipping address, invoicing address, postbox name, company address, city, payment
     * details and verified email address.
     *
     * @uses $_POST['email']
     * @return array {'code' => 1013, 'message' => 'registration_status', 'result' => array()}
     */
    public function check_registration_status()
    {
        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);

        if (empty($email) || empty($customer)) {
            $data = array(
                'code' => 2000,
                'message' => 'Your account is not existed',
                'result' => ''
            );
            $this->api_error_output($data);
            return;
        }

        $status = APUtils::convertObjectToArray($customer);
        $status ['customer_status'] = customers_api::getCustomerStatus($customer);
        $customer_product_setting = CustomerProductSetting::get_activate_flags($customer->customer_id);
        foreach($customer_product_setting as $key=>$value){
            $status[$key] = $value;
        }

        // Check verify address status
        $customer_address = $this->customers_address_m->get_by('customer_id', $customer->customer_id);
        $invoice_verify_flag = 0;
        if(!empty($customer_address)){
            $invoice_verify_flag = $customer_address->invoice_address_verification_flag;
        }
        $postbox_verify_status = array();

        $all_postboxes = $this->postbox_m->get_many_by_many(array(
            "customer_id" => $customer->customer_id,
            "deleted <> " => '1',
            "(postbox_name IS NOT NULL AND postbox_name !='')" => null,
            "((name IS NOT NULL AND name != '') OR (company IS NOT NULL AND company != ''))" => null
        ));
        foreach ($all_postboxes as $postbox) {
            $postbox_id = $postbox->postbox_id;

            $postbox_verify_flag = 0;
            if($postbox->name_verification_flag == "1" && $postbox->company_verification_flag == "1"){
                $postbox_verify_flag = 1;
            }
            $postbox_verify_status[$postbox_id] = $postbox_verify_flag;
        }

        $status ['invoice_address_verification_flag'] = $invoice_verify_flag;
        $status ['verify_postbox_address_status'] = $postbox_verify_status;

        $data = array(
            'code' => 1013,
            'message' => 'registration_status',
            'result' => $status
        );
        $this->api_success_output($data);
        return;
    }

    /**
     * Get customer information
     */
    public function get_customer_info() {
        // Gets customerid logged in.
        $customer_id = MobileContext::getCustomerIDLoggedIn();
        $customer = $this->customer_m->get_by('customer_id', $customer_id);
        $currency = $this->customer_m->get_standard_setting_currency($customer_id);
        $decimal_separator = $this->customer_m->get_standard_setting_decimal_separator($customer_id);
        if (empty($currency)) {
            $currency = $this->currencies_m->get_by(array('currency_short' => 'EUR'));
        }

        $list_currency = $this->currencies_m->get_all();
        $data = array(
            'code' => 1015,
            'message' => 'get_customer_info',
            'result' => array(
                'customer' => $customer,
                'currency' => $currency,
                'list_currency' => $list_currency
            )
        );
        $this->api_success_output($data);
        exit();
    }

	/**
     * The customer changes currency
     */
    public function change_currency() {

        $customer_id = MobileContext::getCustomerIDLoggedIn();

        $currency_id = $this->input->get_post('currency_id');
        $decimal_separator = $this->input->get_post('decimal_separator');

        // Update selected currency
        $this->customer_m->update_by_many(array(
        	'customer_id' => $customer_id
        ), array(
        	'currency_id' => $currency_id,
            'decimal_separator' => $decimal_separator
        ));

        $data = array(
            'code' => 1015,
            'message' => lang('change_currency_success')
        );
        $this->api_success_output($data);
        return;
    }

    /**
     * Get all postbox by customer email
     *
     * @uses $_POST['email']
     * @return array {'code' => 1015, 'message' => 'list_all_postboxes_by_customer', 'result' => array()}
     */
    public function get_all_postboxes_by_customer()
    {
        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);

        if (empty($email) || empty($customer)) {
            $data = array(
                'code' => 2000,
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            exit();
        }

        $customer_id = $customer->customer_id;
        $all_postboxes = $this->postbox_m->get_many_by_many(array(
            "customer_id" => $customer_id,
            "deleted <> " => '1',
            "completed_delete_flag <> " => APConstants::ON_FLAG,
            "(postbox_name IS NOT NULL AND postbox_name !='')" => null,
            "((name IS NOT NULL AND name != '') OR (company IS NOT NULL AND company != ''))" => null
        ));
        foreach ($all_postboxes as $postbox) {
            $postbox->location_name = '';
            if (!empty($postbox->location_available_id)) {
                $postbox_location = $this->location_m->get($postbox->location_available_id);
                if (!empty($postbox_location)) {
                    $postbox->location_name = $postbox_location->location_name;
                }
            }
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

            // load standard shipping_service
            $standard_services = shipping_api::get_standard_shipping_services_by_postbox($postbox->postbox_id);
            $postbox->standard_service_national_letter = $standard_services['standard_service_national_letter'];
            $postbox->standard_service_international_letter = $standard_services['standard_service_international_letter'];
            $postbox->standard_service_national_package = $standard_services['standard_service_national_package'];
            $postbox->standard_service_international_package = $standard_services['standard_service_international_package'];
        }
        $data = array(
            'code' => 1015,
            'message' => 'list_all_postboxes_by_customer',
            'result' => $all_postboxes
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Get all addresses by customer email
     *
     * @uses $_POST['email']
     * @return array {'code' => 1016, 'message' => 'list_all_addresses_by_customer', 'result' => array()}
     */
    public function get_all_addresses_by_customer()
    {
        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);
        $customer_id = $customer->customer_id;

        if (empty($email) || empty($customer)) {
            $data = array(
                'code' => 2000,
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            exit();
        }

        //        $all_addresses = $this->customers_address_m->get_cust_postbox($customer_id);
        $all_addresses = new stdClass();
        $all_addresses = $this->customers_address_m->get_by('customer_id', $customer_id);
        if(!empty($all_addresses)){
            $all_addresses->shipment_country_name = "";
            $all_addresses->invoicing_country_name = "";
            $shippment_country = $this->countries_m->get($all_addresses->shipment_country);
            if(!empty($shippment_country)){
                $all_addresses->shipment_country_name = $shippment_country->country_name;
            }

            $invoicing_country = $this->countries_m->get($all_addresses->invoicing_country);
            if(!empty($invoicing_country)){
                $all_addresses->invoicing_country_name = $invoicing_country->country_name;
            }
        }

        $data = array(
            'code' => 1016,
            'message' => 'get_all_addresses_by_customer',
            'result' => $all_addresses
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Get list envelope type
     */
    public function get_envelope_type_list()
    {

        $location_id = $this->input->post('location_id');

        $list_type = addresses_api::get_envelope_type_list($location_id);

        $data = array(
            'code' => APConstants::API_RETURN_SUCCESS,
            'message' => 'get_envelope_type_list_success',
            'result' => $list_type
        );

        $this->api_success_output($data);
        exit();
    }

    /**
     * Get list envelope category
     */
    public function get_envelope_category_list()
    {
        $list_data = Settings::get_list(APConstants::CATEGORY_TYPE_CODE);
        $data = array(
            'code' => APConstants::API_RETURN_SUCCESS,
            'message' => 'get_envelope_category_list_success',
            'result' => $list_data
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Get all items in Postbox based on input parameters. For example to get New items set POST variable email, p and search_type=1 <br> Similarly
     * for storage item set POST variable email, p and search_type=6 <br> 7 - All, 1 - New, 2 - Envelope Scan, 3 - Scans, 4 - Send, 5 - Trash, 6 -
     * Store
     *
     * @uses $_POST['email']
     * @uses $_POST['p']
     * @uses $_POST['skip']
     * @uses $_POST['first_regist']
     * @uses $_POST['declare_customs']
     * @uses $_POST['search_type']
     * @uses $_POST['base_url']
     * @uses $_POST['fullTextSearchFlag']
     * @uses $_POST['fullTextSearchValue']
     * @uses $_POST['start']
     * @uses $_POST['limit']
     * @return array {'code' => 10.., 'message' => '', 'result' => array()}
     */
    public function load_envelope()
    {
        $this->load->library('mailbox/mailbox_api');

        // Get post box id
        $postbox_id = $this->input->post('p');
        $search_type = $this->input->post('search_type');

        $fullTextSearchFlag = $this->input->post('fullTextSearchFlag');
        $fullTextSearchValue = $this->input->post('fullTextSearchValue');

        $page = $this->input->post('start');
        $limit = $this->input->post('limit');

        if (empty($page)) {
            $page = 1;
        }

        if (empty($limit)) {
            $limit = 10;
        }
        $start = $limit * ($page - 1);

        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);

        if (empty($email) || $postbox_id === "" || $postbox_id === null || $postbox_id === false || empty($search_type)) {
            $data = array(
                'code' => 2000,
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            exit();
        }

        $customer_id = $customer->customer_id;

        $activated_flag = $customer->activated_flag;
        if ($activated_flag == '1') {
            $ouput = array();
            $parent_customer_id = empty($customer->parent_customer_id) ? $customer_id : $customer->parent_customer_id;
            $list_envelopes = mailbox_api::loadEnvelopes($parent_customer_id, $customer_id, $postbox_id, $search_type, $fullTextSearchFlag, $fullTextSearchValue, $start, $limit);
            $ouput['data'] = $list_envelopes['data'];
            $ouput['total'] = $list_envelopes['total'];

            // get envelope type label for envelop
            $envelope_list = $list_envelopes['data'];
            foreach($envelope_list as $row){
                $row->envelope_type_label = Settings::get_label(APConstants::ENVELOPE_TYPE_CODE, $row->envelope_type_id);
                $row->weight = floatval($row->weight);
            }
            $ouput['data'] = $envelope_list;

            // update notification.
            if ($fullTextSearchFlag != '1') {
                if($search_type == 2 || $search_type == 4){
                    $this->update_new_notification_flag($customer_id, $postbox_id);
                }
            }

            // Load all pending envelope need to declare customs
            $list_pending_envelope_customs = mailbox_api::getListPendingEnvelopeCustoms($customer_id);
            $ouput['evelope_custom_list'] = $list_pending_envelope_customs;
        } else {
            $ouput = array(
                'total' => 0,
                'data' => array()
            );
        }

        $data = array(
            'code' => 100,
            'message' => '',
            'result' => $ouput
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Get all available countries void
     *
     * @return array {'code' => 1017, 'message' => 'list_available_countries', 'result' => array()}
     */
    public function all_available_countries()
    {

        // Get all countries
        $all_countries = $this->countries_m->get_many_by_many(array(), '', false, array(
            'country_name' => 'ASC'
        ));
        $data = array(
            'code' => 1017,
            'message' => 'list_available_countries',
            'result' => $all_countries
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * View envelope image.
     *
     * @uses $_POST['email']
     * @uses $_POST['envelope_id']
     * @return array {'code' => 1018, 'message' => 'view_envelope_image', 'result' => array()}
     */
    public function view_envelope_image()
    {
        $envelope_id = $this->input->post('envelope_id');

        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);
        $customer_id = $customer->customer_id;

        if (empty($email) || empty($customer) || empty($envelope_id)) {
            $data = array(
                'code' => 2000,
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            exit();
        }

        //        $envelope = $this->envelope_m->get_by("id", $envelope_id);
        //        $preview_file = $this->envelope_file_m->get_by_many_order(array(
        //            "envelope_id" => $envelope_id,
        //            "customer_id" => $customer_id,
        //            "type" => '1'
        //                ), array(
        //            "updated_date" => "ASC",
        //            "created_date" => "ASC"
        //                ));
        //
        //        // Check if this is first letter
        //        if (APUtils::endsWith($envelope->envelope_code, '_000')) {
        //            $preview_file->public_file_name = APContext::getAssetPath() . Settings::get(APConstants::FIRST_ENVELOPE_KEY);
        //        } else if ($preview_file) {
        //            APUtils::download_amazon_file($preview_file);
        //        }
        //        print_r($preview_file);


        $preview_file = new stdClass();
        $preview_file->file_name = APContext::getAssetPath() . 'index.php/api/get_file_scan?envelope_id=' . $envelope_id . '&type=1&email=' . $email;
        $preview_file->public_file_name = APContext::getAssetPath() . 'index.php/api/get_file_scan?envelope_id=' . $envelope_id . '&type=1&email=' . $email;

        $data = array(
            'code' => 1018,
            'message' => 'view_envelope_image',
            'result' => $preview_file
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * View document image.
     *
     * @uses $_POST['email']
     * @uses $_POST['envelope_id']
     * @return array {'code' => 1019, 'message' => 'view_document_image', 'result' => array()}
     */
    public function view_document_image()
    {
        $envelope_id = $this->input->post('envelope_id');

        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);
        $customer_id = $customer->customer_id;

        if (empty($email) || empty($customer) || empty($envelope_id)) {
            $data = array(
                'code' => 2000,
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            exit();
        }

        //        $envelope = $this->envelope_m->get_by("id", $envelope_id);
        //        $preview_file = $this->envelope_file_m->get_by_many_order(array(
        //            "envelope_id" => $envelope_id,
        //            "customer_id" => $customer_id,
        //            "type" => '2'
        //                ), array(
        //            "updated_date" => "ASC",
        //            "created_date" => "ASC"
        //                ));


        // Check if this is first letter
        //        if (APUtils::endsWith($envelope->envelope_code, '_000')) {
        //            $preview_file->public_file_name = APContext::getAssetPath() . Settings::get(APConstants::FIRST_ENVELOPE_KEY);
        //        } else if ($preview_file) {
        //            APUtils::download_amazon_file($preview_file);
        //        }


        $preview_file = new stdClass();
        $preview_file->file_name = APContext::getAssetPath() . 'index.php/api/get_file_scan?envelope_id=' . $envelope_id . '&type=2&email=' . $email;
        $preview_file->public_file_name = APContext::getAssetPath() . 'index.php/api/get_file_scan?envelope_id=' . $envelope_id . '&type=2&email=' . $email;
        //        $preview_file->assests_path = APContext::getAssetPath();
        //        print_r($preview_file);exit;


        $data = array(
            'code' => 1019,
            'message' => 'view_document_image',
            'result' => $preview_file
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * View pricing information. Get pricing structure for each account type.
     *
     * @uses $_POST['location_id']
     * @return array {'code' => 1020, 'message' => 'view_pricing_info', 'result' => array()}
     */
    public function view_pricing_info()
    {
        $this->load->model('price/pricing_m');
        $this->load->model('customers/customer_m');
        $this->load->model('price/pricing_template_m');
        $this->load->model('addresses/location_pricing_m');

        // get location id
        $location_id = $this->input->get_post("location_id", 0);
        $location = $this->location_m->get_by_many(array(
            'id' => $location_id
        ));

        if (empty($location_id) || empty($location)) {
            $data = array(
                'code' => 2000,
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            exit();
        }

        $pricing_map = mobile_api::view_pricing_info($location);

        // update ticket #1142
        $result = array();
        foreach($pricing_map as $key=>$map){
            $tmp = $map;
            if($key == APConstants::FREE_TYPE){
                $tmp['as_you_go']->text = lang('as_you_go_duration_text');
                $tmp['postbox_fee']->item_value = $tmp['postbox_fee_as_you_go']->item_value;
                $tmp['as_you_go']->item_value = intval($tmp['as_you_go']->item_value / 30);
                $tmp['as_you_go']->item_unit = "Month(s)";

                unset($tmp['postbox_fee_as_you_go']);
            }
            $result[$key] = $tmp;
        }

        $data = array(
            'code' => 1020,
            'message' => 'view_pricing_info',
            'result' => $result
        );
        $this->api_success_output($data);
        exit();
    }


    /**
     * View term and conditions. Get term and conditions information. void
     *
     * @return array {'code' => 1021, 'message' => 'view_terms_info', 'result' => array()}
     */
    public function view_terms_info()
    {
         // Gets customerid logged in.
        $email = $this->input->post('email');
        $result = null;
        if(!empty($email)){
            $customer = $this->customer_m->get_active_customer_by_email($email);
            $result = settings_api::getTermAndConditionBy($customer->customer_id);
        }else{
            $result = settings_api::getTermAndCondition();
        }

        $data = array(
            'code' => 1021,
            'message' => 'view_terms_info',
            'result' => $result
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * View privacy information. Get privacy information. void
     *
     * @return array {'code' => 1022, 'message' => 'view_privacy_info', 'result' => array()}
     */
    public function view_privacy_info()
    {
        $content = settings_api::getPrivacyOfSystem();
        $data = array(
            'code' => 1022,
            'message' => 'view_privacy_info',
            'result' => $content
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Load postbox setting Get postbox setting by customer email and postbox id.
     *
     * @uses $_POST['email']
     * @uses $_POST['postbox_setting_id']
     * @return array {'code' => 1023, 'message' => 'load_postbox_setting', 'result' => array()}
     */
    public function load_postbox_setting()
    {
        ci()->load->library(array(
            'account/account_api',
            "shipping/shipping_api"
        ));

        // Gets customerid logged in.
        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);
        $customer_id = $customer->customer_id;

        $postbox_setting_id = $this->input->post('postbox_setting_id');

        if (empty($email) || empty($customer) || empty($postbox_setting_id)) {
            $data = array(
                'code' => 2000,
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            return;
        }

        $postbox_setting = $this->postbox_setting_m->get_by_many(array(
            "postbox_id" => $postbox_setting_id,
            "customer_id" => $customer_id
        ));

        if (empty($postbox_setting)) {
            $postbox_setting = new stdClass();
            $postbox_setting->always_scan_envelope = 0;
            $postbox_setting->always_scan_envelope_vol_avail = 0;
            $postbox_setting->always_scan_incomming = 0;
            $postbox_setting->always_scan_incomming_vol_avail = 0;
            $postbox_setting->email_notification = 0;
            $postbox_setting->invoicing_cycle = 0;
            $postbox_setting->collect_mail_cycle = 2;
            $postbox_setting->weekday_shipping = 0;
            $postbox_setting->email_scan_notification = 0;
            $postbox_setting->always_forward_directly = 0;
            $postbox_setting->always_forward_collect = 0;
            $postbox_setting->inform_email_when_item_trashed = 0;
            $postbox_setting->auto_trash_flag = 0;
            $postbox_setting->trash_after_day = 0;
            $postbox_setting->always_mark_invoice = 0;
            $postbox_setting->standard_service_national_letter = 0;
            $postbox_setting->standard_service_international_letter = 0;
            $postbox_setting->standard_service_national_package = 0;
            $postbox_setting->standard_service_international_package = 0;
            $postbox_setting->next_collect_date = "";
        }else{
            $next_collect_date = account_api::get_next_collect_shipping($postbox_setting);
            $postbox_setting->next_collect_date = $next_collect_date;
        }

        // init null data.
        $tmp = APUtils::convertObjectToArray($postbox_setting);
        foreach($tmp as $key=>$value){
            if($value == null && $key != 'next_collect_date'){
                $tmp[$key] = 0;
            }
        }
        $postbox_setting = APUtils::convertArrayToObject($tmp);

        //Get list available services by postbox
        $shipping_services = shipping_api::get_shipping_services_by_postbox($postbox_setting_id);

        $postbox_setting->standard_service_national_letter_dropdownlist = APUtils::removeKeyOfArray(shipping_api::filterListShippingServices($shipping_services, APConstants::ENVELOPE_TYPE_LETTER, array(0 , 1)));
        $postbox_setting->standard_service_international_letter_dropdownlist = APUtils::removeKeyOfArray(shipping_api::filterListShippingServices($shipping_services, APConstants::ENVELOPE_TYPE_LETTER, array(0 , 2)));
        $postbox_setting->standard_service_national_package_dropdownlist = APUtils::removeKeyOfArray(shipping_api::filterListShippingServices($shipping_services, APConstants::ENVELOPE_TYPE_PACKAGE, array(0 , 1)));
        $postbox_setting->standard_service_international_package_dropdownlist = APUtils::removeKeyOfArray(shipping_api::filterListShippingServices($shipping_services, APConstants::ENVELOPE_TYPE_LETTER, array(0 , 2)));

        $data = array(
            'code' => 1023,
            'message' => 'load_postbox_setting',
            'result' => $postbox_setting
        );
        $this->api_success_output($data);
        return;
    }

    /**
     * Change Email address Change Email address of loggedin customer.
     *
     * @uses $_POST['email']
     * @uses $_POST['new_email']
     * @return array {'code' => 1024, 'message' => 'change_my_email_success', 'result' => array()}
     */
    public function change_my_email()
    {
        ci()->load->library('account/account_api');

        // gets customer information
        $customer = MobileContext::getCustomerLoggedIn();
        $customer_id = $customer->customer_id;

        // Gets new email
        $new_email = $this->input->post('new_email');
        $current_password = $this->input->post('current_password');
        if (empty($customer) || empty($new_email)) {
            $data = array(
                'code' => 2000,
                'message' => 'Customer is not existed or email is empty.',
                'result' => ''
            );
            $this->api_error_output($data);
            return;
        }

        if(md5($current_password) != $customer->password){
            $this->api_error_output(array(
                'code' => 2000,
                'message' => 'Password is wrong.',
                'result' => ''
            ));
            return;
        }

        // Get user information by new_email
        $customer_exists = $this->customer_m->get_by_many(array(
            "email" => $new_email,
            'status' => APConstants::OFF_FLAG
        ));

        if ($customer_exists) {
            $message = 'change_my_email_error';
            $result = 'email_exists';
            $data = array(
                'code' => 1024,
                'message' => $message,
                'result' => $result
            );
            $this->api_error_output($data);
            return;
        }

        // change my email account
        account_api::change_account_email($customer_id, $new_email);

        $message = 'change_my_email_success';
        $result = $customer_id;
        $data = array(
            'code' => 1024,
            'message' => $message,
            'result' => $result
        );
        $this->api_success_output($data);
        return;
    }

    /**
     * Get auto postbox name by location return auto postbox name by location for loggedin customer.
     *
     * @uses $_POST['email']
     * @uses $_POST['location_available_id']
     * @return array {'code' => 1025, 'message' => 'auto_postbox_name_success', 'result' => array()}
     */
    public function get_auto_postbox_name()
    {
        $location_available_id = $this->input->post("location_available_id");

        // Gets customerid logged in.
        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);

        if (empty($email) || empty($customer) || empty($location_available_id)) {
            $data = array(
                'code' => 2000,
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            exit();
        }

        $customer_id = $customer->customer_id;
        $customer_code = sprintf('C%1$08d', $customer_id);
        $location_rec = $this->location_m->get($location_available_id);
        $short_location_name = strtoupper(substr($location_rec->location_name, 0, 3));
        $box_count = $this->postbox_m->count_by_customer_cityname($customer_code, $short_location_name) + 1;

        // Get customer code and update again
        $postbox_name = $short_location_name . sprintf('%1$02d', $box_count);
        //        print_r($postbox_name);
        //        print_r($location_rec);


        $data = array(
            'code' => 1025,
            'message' => 'auto_postbox_name_success',
            'result' => array(
                'postbox_name' => $postbox_name,
                'location' => $location_rec
            )
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Change Password Change Password for loggedin customer.
     *
     * @uses $_POST['email']
     * @uses $_POST['new_password']
     * @uses $_POST['current_password']
     * @return array {'code' => 1026, 'message' => 'change_my_pass_success', 'result' => array()}
     */
    public function change_my_pass()
    {
        $customer = new stdClass();
        $customer->email = '';
        $customer->password = '';
        $customer->repeat_password = '';

        //        if ($this->form_validation->run()) {
        $passowrd = $this->input->post('new_password');
        $current_password = $this->input->post('current_password');

        // Gets customerid logged in.
        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);

        if (empty($email) || empty($customer) || empty($passowrd) || empty($current_password)) {
            $data = array(
                'code' => 2000,
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            return;
        }

        //        $customer = APContext::getCustomerLoggedIn();
        //$current_customer = $this->customer_m->get_by_many(array(
        //    "email" => $customer->email
        //));

        if (md5($current_password) != $customer->password) {
            $message = 'change_my_pass_error';
            $result = 'current_password_invalid';
            $data = array(
                'code' => 1026,
                'message' => $message,
                'result' => $result
            );
            $this->api_error_output($data);
            return;
        } else {

            // Update customer password
            $customer_id = $this->customer_m->update_by_many(array(
                "email" => $customer->email
            ), array(
                "password" => md5($passowrd)
            ));

            // Build email content
            $to_email = $customer->email;
            $data = array(
                "slug" => APConstants::customer_change_password,
                "to_email" => $to_email,
                // Replace content
                "full_name" => $customer->email,
                "email" => $customer->email,
                "password" => $passowrd,
                "site_url" => APContext::getFullBalancerPath()
            );
            // Call API to send email
            MailUtils::sendEmailByTemplate($data);

            $message = 'change_my_pass_success';
            $result = $customer_id;
        }
        $data = array(
            'message' => $message,
            'result' => $result
        );
        $this->api_success_output($data);
        return;
    }

    /**
     * Resend email verification link Resend email verification link to active your account.
     *
     * @uses $_POST['email']
     * @return array {'code' => 1027, 'message' => 'resend_email_success', 'result' => array()}
     */
    public function resend_email_verification()
    {
        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);
        // $customer = $this->customer_m->get_by_many(array(
        //     "email" => $email
        // ));

        if (empty($email) || empty($customer)) {
            $data = array(
                'code' => 2000,
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            exit();
        }

        if ($customer) {
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
            $message = 'resend_email_success';
            $result = 'resend_email_success';
            //        print_r($result);
        } else {
            $message = 'resend_email_error';
            $result = 'resend_email_error';
        }
        $data = array(
            'code' => 1027,
            'message' => $message,
            'result' => $result
        );
        $this->api_success_output($data);
        exit();;
    }

    /**
     * Get open balance Gets open balance, current open balance in current month and next invoicing date.
     *
     * @uses $_POST['email']
     * @return array {'code' => 1028, 'message' => 'get_open_balance_success', 'result' => array()}
     */
    public function get_open_balance()
    {
        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);

        if (empty($email) || empty($customer)) {
            $data = array(
                'code' => 2000,
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            exit();
        }

        $customer_id = $customer->customer_id;

        $open_balance = APUtils::getCurrentBalance($customer_id);

        // gets current open balance in this month.
        $open_balance_this_month = APUtils::getCurrentBalanceThisMonth($customer_id);

        // Next Invoicing date
        $next_invoicing_date = APUtils::displayDate(APUtils::getLastDayOfCurrentMonth());

        $result = array(
            'open_balance' => APUtils::number_format($open_balance),
            'open_balance_this_month' => APUtils::number_format($open_balance_this_month),
            'next_invoicing_date' => $next_invoicing_date
        );

        $data = array(
            'code' => 1028,
            'message' => 'get_open_balance_success',
            'result' => $result
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Load current activities. Gets current activities data.
     *
     * @uses $_POST['email']
     * @return array {'code' => 1029, 'message' => 'get_current_activities_success', 'result' => array()}
     */
    public function get_current_activities()
    {
        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);

        if (empty($email) || empty($customer)) {
            $data = array(
                'code' => 2000,
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            exit();
        }

        $customer_id = $customer->customer_id;
        $next_invoices_display = InvoiceUtils::getCurrentActivitiesInvoice($customer_id);

        // Calculate total amount
        $total_amount = $next_invoices_display->postboxes_amount;
        $total_amount += $next_invoices_display->envelope_scanning_amount;
        $total_amount += $next_invoices_display->scanning_amount;
        $total_amount += $next_invoices_display->additional_items_amount;
        $total_amount += $next_invoices_display->additional_pages_scanning_amount;
        $total_amount += $next_invoices_display->shipping_handing_amount;
        $total_amount += $next_invoices_display->storing_amount;

        // Format data before send to client
        $next_invoices_display->total_amount = APUtils::number_format($total_amount);
        $next_invoices_display->postboxes_amount = APUtils::number_format($next_invoices_display->postboxes_amount);
        $next_invoices_display->envelope_scanning_amount = APUtils::number_format($next_invoices_display->envelope_scanning_amount);
        $next_invoices_display->scanning_amount = APUtils::number_format($next_invoices_display->scanning_amount);
        $next_invoices_display->additional_items_amount = APUtils::number_format($next_invoices_display->additional_items_amount);
        $next_invoices_display->shipping_handing_amount = APUtils::number_format($next_invoices_display->shipping_handing_amount);
        $next_invoices_display->storing_amount = APUtils::number_format($next_invoices_display->storing_amount);

        $data = array(
            'code' => 1029,
            'message' => 'get_current_activities_success',
            'result' => $next_invoices_display
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Load old Invoices Gets old invoices data.
     *
     * @uses $_POST['email']
     * @return array {'code' => 1030, 'message' => 'load_old_invoice_success', 'result' => array()}
     */
    public function load_old_invoice() {
        $customer = MobileContext::getCustomerLoggedIn();

        $input_paging = $this->get_paging_input();
        $result = InvoiceUtils::load_old_invoice($customer, $input_paging);

        $data = array(
            'code' => 1030,
            'message' => 'load_old_invoice_success',
            'result' => $result['mobile_old_invoices']
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Change settings Change or Update settings of postbox <br /> invoicing_cycle -> 1 <br /> email_notification -> 1-Immediately 2-Daily 3-Weekly
     * 4-Monthly 5-None <br /> collect_mail_cycle -> 1-Daily 2-Weekly 3-Monthly 4-Quarterly 5-Never <br /> weekday_shipping -> 1-Monday 2-Tuesday
     * 3-Wednesday 4-Thursday 5-Friday <br />
     *
     * @uses $_POST['email']
     * @uses $_POST['postbox_setting_id']
     * @uses $_POST['always_scan_incomming']
     * @uses $_POST['envelope_scan']
     * @uses $_POST['scans']
     * @uses $_POST['always_forward_directly']
     * @uses $_POST['always_forward_collect']
     * @uses $_POST['email_notification']
     * @uses $_POST['invoicing_cycle']
     * @uses $_POST['collect_mail_cycle']
     * @uses $_POST['weekday_shipping']
     * @return array {'code' => 1031, 'message' => 'save_postbox_settings_success', 'result' => array()}
     */
    public function save_postbox_settings()
    {
        // Gets customerid logged in.
        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);
        $customer_id = $customer->customer_id;

        // Gets customer address info.
        $check = $this->customer_m->get_by('customer_id', $customer_id);
        $postbox_setting_id = $this->input->post('postbox_setting_id');

        $postbox_setting_check = $this->postbox_setting_m->get_by_many(array(
            "postbox_id" => $postbox_setting_id,
            "customer_id" => $customer_id
        ));

        if ($check) {
            $envelope_scan = $this->input->post('envelope_scan');
            $scans = $this->input->post('scans');
            $always_scan_envelope = $this->input->post('always_scan_envelope');
            $always_scan_incomming = $this->input->post('always_scan_incomming');
            $email_scan_notification = $this->input->post('email_scan_notification');
            $always_forward_directly = $this->input->post('always_forward_directly');
            $always_forward_collect = $this->input->post('always_forward_collect');
            $inform_email_when_item_trashed = $this->input->post('inform_email_when_item_trashed');
            $email_notification = $this->input->post('email_notification');
            $collect_mail_cycle = $this->input->post('collect_mail_cycle');
            $weekday_shipping = $this->input->post('weekday_shipping');
            $standard_service_national_letter = $this->input->post('standard_service_national_letter');
            $standard_service_international_letter = $this->input->post('standard_service_international_letter');
            $standard_service_national_package = $this->input->post('standard_service_national_package');
            $standard_service_international_package = $this->input->post('standard_service_international_package');
            $always_mark_invoice = $this->input->post('always_mark_invoice');

            // update settings information.
            $data = array(
                'always_scan_envelope_vol_avail' => $envelope_scan,
                'always_scan_incomming_vol_avail' => $scans,
                'always_scan_envelope' => $always_scan_envelope,
                'always_scan_incomming' => $always_scan_incomming,
                'email_scan_notification' => $email_scan_notification,
                'always_forward_directly' => $always_forward_directly,
                'always_forward_collect' => $always_forward_collect,
                'inform_email_when_item_trashed' => $inform_email_when_item_trashed,
                'email_notification' => $email_notification,
                'collect_mail_cycle' => $collect_mail_cycle,
                'standard_service_national_letter' => $standard_service_national_letter,
                'standard_service_international_letter' => $standard_service_international_letter,
                'standard_service_national_package' => $standard_service_national_package,
                'standard_service_international_package' => $standard_service_international_package,
                'always_mark_invoice' => $always_mark_invoice,
                'weekday_shipping' => $weekday_shipping,
                'postbox_id' => $postbox_setting_id,
                'customer_id' => $customer_id
            );
            if (empty($postbox_setting_check)) {
                $this->postbox_setting_m->insert($data);
            } else {
                $this->postbox_setting_m->update_by_many(array(
                    'postbox_id' => $postbox_setting_id,
                    "customer_id" => $customer_id
                ), $data);
            }


            $message = 'save_postbox_settings_success';
            $result = '';
        }

        $data = array(
            'code' => 1031,
            'message' => $message,
            'result' => $result
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Request envelope scan. Request scanning of envelope from item detail page
     *
     * @uses $_POST['email']
     * @uses $_POST['envelope_id']
     * @return array {'code' => 1032, 'message' => 'request_envelope_scan_success', 'result' => array()}
     */
    public function request_envelope_scan()
    {
        $this->load->library('scans/scans_api');
        // Gets customerid logged in.
        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);
        $customer_id = $customer->customer_id;
        $ids_input = $this->input->post('envelope_id');

        if (empty($ids_input) || count($ids_input) == 0) {
            $data = array(
                'code' => 1033,
                'message' => 'The list of envelope id is required.',
                'result' => ''
            );
            $this->api_error_output($data);
            return;
        }

        // #1012 - Prepayment method
        $ids = explode(",", $ids_input);
        $check_prepayment_data = CustomerUtils::checkApplyScanPrepayment(APConstants::TRIGGER_ACTION_TYPE_CUSTOMER, 'envelope', $ids, $customer_id);
        if ($check_prepayment_data['prepayment'] == true) {
            $check_prepayment_data['status'] = FALSE;
            // Add item scan request to queue
            mailbox_api::requestEnvelopeScanToQueue($ids_input, $customer_id);

            // Recalculate after make request to queue
            $estimated_cost = CustomerUtils::estimateScanningCost($ids, 'envelope', $customer_id, true);
            $total_prepayment_amount = CustomerUtils::estimateTotalPrepaymentRequest($customer_id);
            $other_prepayment_cost = $total_prepayment_amount - $estimated_cost;
            $check_prepayment_data['estimated_cost'] = $estimated_cost;
            $check_prepayment_data['other_prepayment_cost'] = $other_prepayment_cost;

            $data = array(
                'code' => 1033,
                'message' => 'The prepayment required to process',
                'result' => $check_prepayment_data
            );
            $this->api_error_output($data);
            return;
        }

        if (!empty($ids_input)) {
            foreach ($ids as $id) {
                $this->envelope_m->update_by_many(array(
                    'id' => $id,
                    'to_customer_id' => $customer_id,
                    '(collect_shipping_flag is null and direct_shipping_flag is null)' => null,
                ), array(
                    'envelope_scan_flag' => APConstants::OFF_FLAG,
                    'last_updated_date' => now()
                ));
                $this->update_new_notification_flag($id, $customer_id);

                // Insert completed activity (Item scan ordered by customer)
                scans_api::insertCompleteItem($id, APConstants::SCAN_ENVELOPE_ORDER_BY_CUSTOMER_ACTIVITY_TYPE, APConstants::TRIGGER_BY_CUSTOMER, $customer_id);
            }
        }

        $data = array(
            'code' => 1032,
            'message' => 'request_envelope_scan_success',
            'result' => ''
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Request letter scan. Request scanning of letter from item detail page
     *
     * @uses $_POST['email']
     * @uses $_POST['envelope_id']
     * @return array {'code' => 1033, 'message' => 'request_letter_scan_success', 'result' => array()}
     */
    public function request_letter_scan()
    {
        $this->load->library('scans/scans_api');
        // Gets customerid logged in.
        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);
        $customer_id = $customer->customer_id;
        $ids_input = $this->input->post('envelope_id');

        if (empty($ids_input) || count($ids_input) == 0) {
            $data = array(
                'code' => 1033,
                'message' => 'The list of envelope id is required.',
                'result' => ''
            );
            $this->api_error_output($data);
            return;
        }

        // #1012 - Prepayment method
        $ids = explode(",", $ids_input);
        $check_prepayment_data = CustomerUtils::checkApplyScanPrepayment(APConstants::TRIGGER_ACTION_TYPE_CUSTOMER, 'item', $ids, $customer_id);
        if ($check_prepayment_data['prepayment'] == true) {
            $check_prepayment_data['status'] = FALSE;
            // Add item scan request to queue
            mailbox_api::requestItemScanToQueue($ids_input, $customer_id);

            // Recalculate after make request to queue
            $estimated_cost = CustomerUtils::estimateScanningCost($ids, 'item', $customer_id, true);
            $total_prepayment_amount = CustomerUtils::estimateTotalPrepaymentRequest($customer_id);
            $other_prepayment_cost = $total_prepayment_amount - $estimated_cost;
            $check_prepayment_data['estimated_cost'] = $estimated_cost;
            $check_prepayment_data['other_prepayment_cost'] = $other_prepayment_cost;

            $data = array(
                'code' => 1033,
                'message' => 'The prepayment required to process',
                'result' => $check_prepayment_data
            );
            $this->api_error_output($data);
            return;
        }

        if (!empty($ids_input)) {
            foreach ($ids as $id) {
                $this->envelope_m->update_by_many(array(
                    'id' => $id,
                    'to_customer_id' => $customer_id,
                    '(collect_shipping_flag is null and direct_shipping_flag is null)' => null,
                ), array(
                    'item_scan_flag' => APConstants::OFF_FLAG,
                    'last_updated_date' => now()
                ));
                $this->update_new_notification_flag($id, $customer_id);

                // Insert completed activity (Item scan ordered by customer)
                scans_api::insertCompleteItem($id, APConstants::SCAN_ITEM_ORDER_BY_CUSTOMER_ACTIVITY_TYPE, APConstants::TRIGGER_BY_CUSTOMER, $customer_id);
            }
        }

        $data = array(
            'code' => 1033,
            'message' => 'request_letter_scan_success',
            'result' => ''
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Request send envelope (direct shipment) Request sending envelope (direct shipment) to customer.
     * shipping_type = 1: Direct shipping | shipping_type = 2: Collect Shipping
     *
     * @uses $_POST['email']
     * @uses $_POST['envelope_id']
     * @return array {'code' => 1034, 'message' => 'request_send_envelope_success', 'result' => array()}
     */
    public function request_send_envelope()
    {
        $this->load->library('scans/scans_api');
        // Gets customerid logged in.
        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);
        $customer_id = $customer->customer_id;
        $ids_input = $this->input->post('envelope_id');
        $shipping_type = $this->input->post('shipping_type');

        if (empty($ids_input) || count($ids_input) == 0) {
            $data = array(
                'code' => 1033,
                'message' => 'The list of envelope id is required.',
                'result' => ''
            );
            $this->api_error_output($data);
            return;
        }

        // check direct shippment.
        if($shipping_type == APConstants::DIRECT_FORWARDING){
            // #1012 - Prepayment method
            $ids = explode(",", $ids_input);
            $check_prepayment_data = CustomerUtils::checkApplyShippingPrepayment(APConstants::TRIGGER_ACTION_TYPE_CUSTOMER, APConstants::SHIPPING_SERVICE_NORMAL,
                         APConstants::SHIPPING_TYPE_DIRECT, $ids, $customer_id);
            if ($check_prepayment_data['prepayment'] == true) {
                // Add request to queue
                mailbox_api::requestDirectShippingToQueue($ids_input, $customer_id);

                $check_prepayment_data['status'] = FALSE;
                $data = array(
                    'code' => 1033,
                    'message' => 'The prepayment required to process',
                    'result' => $check_prepayment_data
                );
                $this->api_error_output($data);
                return;
            }

            // Add request
            mailbox_api::requestDirectShipping($ids_input, $customer_id);
        }
        // mark collect shippment.
        else if($shipping_type == APConstants::COLLECT_FORWARDING){
            // #1012 - Prepayment method
            $ids = explode(",", $ids_input);
            $list_id = array();

            // check prepayment item
            foreach($ids as $id){
                $envelope = $this->envelope_m->get($id);
                if($envelope->collect_shipping_flag){
                    $list_id[] = $id;
                }
            }

            if(!empty($list_id)){
                $check_prepayment_data = CustomerUtils::checkApplyShippingPrepayment(APConstants::TRIGGER_ACTION_TYPE_CUSTOMER, APConstants::SHIPPING_SERVICE_NORMAL,
                             APConstants::SHIPPING_TYPE_DIRECT, $ids, $customer_id);
                if ($check_prepayment_data['prepayment'] == true) {
                    // Add request to queue
                    $list_envelope_id_str = implode(',', $list_id);
                    mailbox_api::requestCollectShippingToQueue($list_envelope_id_str, $customer_id);

                    $check_prepayment_data['status'] = FALSE;
                    $data = array(
                        'code' => 1033,
                        'message' => 'The prepayment required to process',
                        'result' => $check_prepayment_data
                    );
                    $this->api_error_output($data);
                    return;
                }
            }

            mailbox_api::requestCollectShipping($ids_input, $customer_id);
        }

        $data = array(
            'code' => 1034,
            'message' => 'request_send_envelope_success',
            'result' => ''
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Request save item to Dropbox Save the item to drop box. <br> Note: id -> envelope id
     *
     * @uses $_POST['email']
     * @uses $_POST['id']
     * @uses $_POST['postbox_id']
     * @return array {'code' => 1035, 'message' => 'save_item_cloud_dropbox_success', 'result' => array()}
     */
    public function save_item_cloud_dropbox()
    {
        // Gets customerid logged in.
        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);
        $customer_id = $customer->customer_id;
        $customer_setting = $this->customer_cloud_m->get_by_many(array(
            "cloud_id" => APConstants::CLOUD_DROPBOX_CODE,
            "customer_id" => $customer_id
        ));
        $setting = json_decode($customer_setting->settings, true);

        if (empty($customer_setting)) {
            $data = array(
                'code' => 2000,
                'message' => 'save_item_cloud_dropbox_failure',
                'result' => 'Please attach Dropbox to your account using web application.'
            );
            $this->api_error_output($data);
            exit();
        }

        if(empty($setting)) {
            $data = array(
                'code' => 1037,
                'message' => 'Your drop setting did not completed. Please access web version to add dropbox setting.',
                'result' => 'Your drop setting did not completed. Please access web version to add dropbox setting.'
            );
            $this->api_error_output($data);
            exit();
        } else {
            $dropboxV2 = APContext::getDropbox($setting);

            // $envelope_id = $this->input->post('id');
            $ids_input = $this->input->post('id');
            log_message(APConstants::LOG_DEBUG, ">>>>>>>>>>>>>> Envelope Input:" . $ids_input);
            if (!empty($ids_input)) {
                $ids = explode(",", $ids_input);
                foreach ($ids as $envelope_id) {
                    $postbox_id = $this->input->post('postbox_id');
                    //                    $customer_id = APContext::getCustomerCodeLoggedIn();


                    $envelope_file = $this->envelope_file_m->get_by_many_order(array(
                        "envelope_id" => $envelope_id,
                        "customer_id" => $customer_id,
                        "type" => '2'
                    ), array(
                        "updated_date" => "ASC",
                        "created_date" => "ASC"
                    ));

                    $document_file_name = '';
                    $local_file_name = '';
                    if ($envelope_file && !empty($envelope_file->local_file_name)) {
                        $document_file_name = $envelope_file->local_file_name;

                        // Download from S3
                        $default_bucket_name = $this->config->item('default_bucket');
                        APUtils::download_amazon_file($envelope_file);
                        $local_file_name = $document_file_name;
                    }
                    //print_r($local_file_name);exit;
                    $folder = $setting['folder_name'] . '/PO_' . $postbox_id;
                    $dropboxV2->create_folder($folder);
                    if (!empty($local_file_name)) {
                        $dropboxV2->add($folder, $local_file_name, array(
                            "overwrite" => false
                        ));

                        // Update
                        $this->envelope_m->update_by_many(array(
                            "id" => $envelope_id,
                            "to_customer_id" => $customer_id
                        ), array(
                            "sync_cloud_flag" => APConstants::ON_FLAG,
                            "sync_cloud_date" => now(),
                            'last_updated_date' => now()
                        ));
                    } else {
                        // Your request file does not exist or deleted.
                        $data = array(
                            'code' => 2000,
                            'message' => 'save_item_cloud_dropbox_failure',
                            'result' => 'Your request file does not exist or deleted.'
                        );
                        $this->api_error_output($data);
                        exit();
                    }
                }
                // Your scan have been saved to in your cloud drive successfully.
                $data = array(
                    'code' => 1035,
                    'message' => 'save_item_cloud_dropbox_success',
                    'result' => 'Your scan have been saved in your cloud drive successfully.'
                );
                $this->api_success_output($data);
                exit();
            }
            //            Please request and wait document scan before saved to cloud driver.
            $data = array(
                'code' => 2000,
                'message' => 'save_item_cloud_dropbox_failure',
                'result' => 'Please request and wait document scan before saved to cloud drive.'
            );
            $this->api_error_output($data);
            exit();
        }
    }

    /**
     * Activities in Current Period Get activities in current period.
     *
     * @uses $_POST['email']
     * @return array {'code' => 1036, 'message' => 'load_current_invoice_activies_success', 'result' => array()}
     */
    public function load_current_invoice_activies()
    {
        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);
        $customer_id = $customer->customer_id;

        $target_month = APUtils::getTargetMonthInvoice();
        $target_year = APUtils::getTargetYearInvoice();

        // Gets last day of month
        $target_first = APUtils::getFirstDayOfMonth($target_year . $target_month);
        $target_last = APUtils::getLastDayOfMonth($target_first);

        // Get input condition
        $array_condition = array(
            'invoice_detail.customer_id' => $customer_id,
            'activity_date >=' => $target_first,
            'activity_date <=' => $target_last
        );

        // If current request is ajax
        //        if ($this->is_ajax_request()) {
        //            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getPagingSetting();
        //
        //            // update limit into user_paging.
        //            APContext::updatePagingSetting($limit);
        //
        //            // Get paging input
        //            $input_paging = $this->get_paging_input();
        //            $input_paging ['limit'] = APContext::getPagingSetting();
        // Get paging input
        $page = isset($_REQUEST ['page']) ? $_REQUEST ['page'] : 1;
        $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : 100;
        $sidx = isset($_REQUEST ['sidx']) ? $_REQUEST ['sidx'] : '';
        $sord = isset($_REQUEST ['sord']) ? $_REQUEST ['sord'] : 'ASC';

        $start = $limit * $page - $limit;
        $start = ($start < 0) ? 0 : $start;

        $input_paging ['start'] = $start;
        $input_paging ['limit'] = $limit;
        $input_paging ['sort_column'] = $sidx;
        $input_paging ['sort_type'] = $sord;

        // Call search method
        $query_result = $this->invoice_detail_m->get_invoice_detail_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);
        // Process output data
        $total = $query_result ['total'];
        $datas = $query_result ['data'];

        // Get output response
        //            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);


        $i = 0;
        foreach ($datas as $row) {
            $vat = $row->vat;
            $response->rows [$i] ['id'] = $row->id;
            $net_price = '';
            if ($row->item_amount <= 0) {
                $net_price = "included";
            } else {
                $row->item_amount = $row->item_amount;
                $net_price = APConstants::MONEY_UNIT . ' ' . (APUtils::number_format($row->item_amount, 2));
            }

            // #499 added: get vat + gross total
            $gross = (1 + $vat) * $row->item_amount;
            $response->rows [$i] ['cell'] = array(
                $row->id,
                $row->item_number . '    ' . $row->activity,
                APUtils::displayDate($row->activity_date),
                $net_price,
                ($vat * 100) . '%',
                APUtils::number_format($gross, 2)
            );
            $i++;
        }
        //print_r($response);
        $data = array(
            'code' => 1036,
            'message' => 'load_current_invoice_activies_success',
            'result' => $response
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Change category type. Change category type of item. <br> 001 - Insurances <br> 002 - Invoices <br> 003 - Business <br> 004 - Memberships <br>
     * 005 - Private <br> 6 - Tax <br> 7 - Bank <br> 8 - Apartment <br> 9 - Other <br> 11 - my box <br>
     *
     * @uses $_POST['email']
     * @uses $_POST['envelope_id']
     * @uses $_POST['category_type']
     * @return array {'code' => 1037, 'message' => 'change_category_type_success', 'result' => array()}
     */
    public function change_category_type()
    {
        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);
        $customer_id = $customer->customer_id;

        $envelope_id = $this->input->post('envelope_id');
        // Get envelope information
        $envelope = $this->envelope_m->get_by("id", $envelope_id);
        $category_type = $this->input->post('category_type');

        $this->envelope_m->update_by_many(array(
            "to_customer_id" => $customer_id,
            "id" => $envelope_id
        ), array(
            "category_type" => $category_type
        ));

        // Update to database
        $pdf_content_file = $this->envelope_pdf_content_m->get_by_many(array(
            "envelope_id" => $envelope_id,
            "customer_id" => $customer_id
        ));
        if ($pdf_content_file) {
            $envelope_content = '';
            $envelope_content = $envelope_content . ' ' . $envelope->from_customer_name;
            $envelope_content = $envelope_content . ' ' . $envelope->weight;
            $envelope_content = $envelope_content . ' ' . APUtils::convert_timestamp_to_date($envelope->incomming_date, 'd.m.Y');
            $envelope_content = $envelope_content . ' ' . Settings::get_label(APConstants::ENVELOPE_TYPE_CODE, $envelope->envelope_type_id);
            $envelope_content = $envelope_content . ' ' . Settings::get_label(APConstants::CATEGORY_TYPE_CODE, $category_type);
            $this->envelope_pdf_content_m->update_by_many(array(
                "envelope_id" => $envelope_id,
                "customer_id" => $customer_id
            ), array(
                "envelope_content" => $envelope_content
            ));
        }

        $data = array(
            'code' => 1037,
            'message' => 'change_category_type_success',
            'result' => ''
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Get Invoice ID.
     *
     * @uses $_POST['email']
     * @return array {'code' => 1038, 'message' => 'get_invoice_id_success', 'result' => array()}
     */
    public function get_invoice_id()
    {
        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);
        $customer_id = $customer->customer_id;

        $invoice_id = APUtils::genetateReferenceForOpenBalance($customer_id);

        if (empty($email) || empty($customer) || empty($invoice_id)) {
            $data = array(
                'code' => 2000,
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            exit();
        }

        $result = new stdClass();
        $result->invoice_id = $invoice_id;

        $data = array(
            'code' => 1038,
            'message' => 'get_invoice_id_success',
            'result' => $result
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Add payment transaction history.
     *
     * @uses $_POST['email']
     * @uses $_POST['pseudocardpan']
     * @uses $_POST['amount']
     * @uses $_POST['invoice_id']
     * @return array {'code' => 1039, 'message' => 'add_payment_tran_hist_success', 'result' => array()}
     */
    public function add_payment_tran_hist()
    {
        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);
        $customer_id = $customer->customer_id;

        $pseudocardpan = $this->input->post('pseudocardpan');
        $amount = $this->input->post('amount');
        $invoice_id = $this->input->post('invoice_id');

        if (empty($email) || empty($customer) || empty($pseudocardpan) || empty($amount) || empty($invoice_id)) {
            $data = array(
                'code' => 2000,
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            exit();
        }

        // Insert data to payment_tran_hist
        $tran_id = $this->payment_tran_hist_m->insert(array(
            "customer_id" => $customer_id,
            "tran_date" => now(),
            "tran_type" => "authorize",
            "pseudocardpan" => $pseudocardpan,
            "amount" => $amount,
            "ccy" => 'EUR',
            "invoice_id" => $invoice_id
        ));

        $data = array(
            'code' => 1039,
            'message' => 'add_payment_tran_hist_success',
            'result' => array(
                'id' => $tran_id
            )
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Add payment transaction success.
     *
     * @uses $_POST['tran_id']
     * @uses $_POST['status']
     * @return array {'code' => 1040, 'message' => 'payment_tran_success', 'result' => array()}
     */
    public function payment_tran_success()
    {
        $tran_id = $this->input->post('tran_id');
        $status = $this->input->post('status');

        if (empty($tran_id) || empty($status)) {
            $data = array(
                'code' => 2000,
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            exit();
        }

        // Update status
        $this->payment_tran_hist_m->update_by_many(array(
            "id" => $tran_id
        ), array(
            "status" => $status
        ));

        $data = array(
            'code' => 1040,
            'message' => 'payment_tran_success',
            'result' => ''
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Add payment transaction Failiure.
     *
     * @uses $_POST['tran_id']
     * @uses $_POST['status']
     * @uses $_POST['errormessage']
     * @return array {'code' => 1041, 'message' => 'payment_tran_failiure', 'result' => array()}
     */
    public function payment_tran_failiure()
    {
        $tran_id = $this->input->post('tran_id');
        $status = $this->input->post('status');
        $errormessage = $this->input->post('errormessage');

        if (empty($tran_id) || empty($status) || empty($errormessage)) {
            $data = array(
                'code' => 2000,
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            exit();
        }

        // Update status
        $this->payment_tran_hist_m->update_by_many(array(
            "id" => $tran_id
        ), array(
            "status" => $status,
            "message" => $errormessage
        ));

        $data = array(
            'code' => 1041,
            'message' => 'payment_tran_failiure',
            'result' => ''
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Add new payment method. Add verified CC details into DB.
     *
     * @uses $_POST['email']
     * @uses $_POST['account_type']
     * @uses $_POST['card_type']
     * @uses $_POST['card_name']
     * @uses $_POST['cvc']
     * @uses $_POST['expired_year']
     * @uses $_POST['expired_month']
     * @uses $_POST['truncatedcardpan']
     * @uses $_POST['pseudocardpan']
     * @return array {'code' => 1042, 'message' => 'add_payment_success', 'result' => array()}
     */
    public function add_payment_method()
    {
        $this->load->library(array(
            'payment/payment_api'
        ));
        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);
        $customer_id = $customer->customer_id;

        $account_type = $this->input->post('account_type');
        $card_type = $this->input->post('card_type');
        $card_name = $this->input->post('card_name');
        $cvc = '';
        $expired_year = $this->input->post('expired_year');
        $expired_month = $this->input->post('expired_month');
        $truncatedcardpan = $this->input->post('truncatedcardpan');
        $pseudocardpan = $this->input->post('pseudocardpan');

        if (empty($email) || empty($customer_id) || empty($account_type) || empty($card_type) || empty($card_name)
            || empty($expired_year) || empty($expired_month) || empty($truncatedcardpan) || empty($pseudocardpan)
        ) {
            $data = array(
                'code' => 2000,
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            return;
        }

        // add payment method.
        $result = payment_api::addPaymentMethod($customer_id, $account_type, $card_type, $truncatedcardpan
                , $card_name, $cvc, $expired_year, $expired_month, $pseudocardpan);

        if(empty($result)){
            $message = 'add_payment_success';
            $data = array(
                'code' => 1042,
                'message' => $message,
                'result' => ''
            );
            $this->api_success_output($data);
        }else{
            $this->api_error_output($result);
        }
        return;
    }

    /**
     * Add new payment method.
     */
    public function add_deposit_invoice_method()
    {
        // Get current customer login
        $customer_id = MobileContext::getCustomerIDLoggedIn();
        $customer = $this->customer_m->get_by_many(array(
            "customer_id" => $customer_id
        ));

        // Check customer information
        if (empty($customer)) {
            $message = lang('add_payment_fail');
            $data = array(
                'code' => 1042,
                'message' => $message,
                'result' => ''
            );
            $this->api_error_output($data);
            return;
        }

        // Check invoice code
        $invoice_code = substr(md5($customer_id), 0, 6) . APUtils::generateRandom(4);

        // Update payment flag
        $this->customer_m->update_by_many(array(
            "customer_id" => $customer_id
        ),
            array(
                "invoice_type" => '2',
                "payment_detail_flag" => APConstants::ON_FLAG,
                "request_confirm_flag" => APConstants::ON_FLAG,
                "request_confirm_date" => now(),
                "last_updated_date" => now(),
                "invoice_code" => $invoice_code
            ));
        // update: convert registration process flag to customer_product_setting.
        CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'payment_detail_flag', APConstants::ON_FLAG);

        $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
        $open_balance = $open_balance_data['OpenBalanceDue'];
        if ($open_balance <= 0.1) {
            // we dont have to tell the Customer that a valid credit Card with non working payment can reactivate the account.
            // in most cases the Customer will Chose a Card that can handle the payment if it is valid
            // Only reactivate if deactivated_type = auto
            $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;
            customers_api::reactivateCustomer($customer_id, $created_by_id);
        }

        $message = lang('add_invoice_success');
        $data = array(
            'code' => 1042,
            'message' => $message,
            'result' => ''
        );
        $this->api_success_output($data);
        return;
    }

    /**
     * Make default payment request.
     *
     * @param unknown_type $card_number
     * @param unknown_type $card_type
     */
    public function make_default_payment($pseudocardpan, $callback_tran_id, $customer_id)
    {
        ci()->load->model("customers/customer_m");
        // Get config
        $merchant_id = $this->config->item('payone.merchant-id');
        $portal_id = $this->config->item('payone.portal-id');
        $portal_key = $this->config->item('payone.portal-key');
        $sub_account_id = $this->config->item('payone.sub-account-id');
        $mode = $this->config->item('payone.mode');
        $encoding = $this->config->item('payone.encoding');
        // Build service
        $bootstrap = new Payone_Bootstrap();
        $bootstrap->init();
        $builder = new Payone_Builder();
        $service = $builder->buildServiceVerification3dsCheck();
        // $request = new Payone_Api_Request_Preauthorization();
        $request = new Payone_Api_Request_Preauthorization();
        $request->setPortalid($portal_id);
        $request->setMid($merchant_id);
        $request->setKey($portal_key);
        $request->setAid($sub_account_id);
        $request->setMode($mode);
        $request->setEncoding($encoding);
        $request->setAmount('1');
        $request->setCurrency('EUR');
        $request->setClearingtype('cc');
        $request->setReference(now() . '_1ST');

        // Set person data
        $request_person_data = new Payone_Api_Request_Parameter_Authorization_PersonalData();
        $customer = ci()->customer_m->getCustomerInfoForPayOne($customer_id);
//        if (empty($customer_name)) {
//            $customer_name = 'Christian Hemmrich';
//        }
//        $request_person_data->setLastname($customer_name);
//        $request_person_data->setCountry('DE');
        $request_person_data->setCustomerid(!empty($customer->customer_code) ? $customer->customer_code : '');
        $request_person_data->setLastname(!empty($customer->invoicing_address_name) ? $customer->invoicing_address_name : 'Christian Hemmrich');
        $request_person_data->setCompany(!empty($customer->invoicing_company) ? $customer->invoicing_company : '');
        $request_person_data->setCountry(!empty($customer->country_code) ? $customer->country_code : 'DE');
        $request_person_data->setEmail(!empty($customer->email) ? $customer->email : '');

        $request->setPersonalData($request_person_data);
        $request_credit_date = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_CreditCard();
        $request_credit_date->setPseudocardpan($pseudocardpan);
        $request_credit_date->setSuccessurl(APContext::getFullBasePath() . 'payment/success_callback?callback_tran_id=' . $callback_tran_id);
        $request_credit_date->setErrorurl(APContext::getFullBasePath() . 'payment/error_callback?callback_tran_id=' . $callback_tran_id);
        $request->setPayment($request_credit_date);

        $service = $builder->buildServicePaymentPreauthorize();
        $response = $service->preauthorize($request);
        if ($response) {
            if ($response->getStatus() == 'REDIRECT') {
                // Update user id and transaction id
                $xid = $response->getTxid();
                $userid = $response->getUserid();

                // Store this information to database
                $this->payment_m->update_by_many(array(
                    "callback_tran_id" => $callback_tran_id
                ), array(
                    "xid" => $xid,
                    "userid" => $userid
                ));

                return $response->getRedirecturl();
            } else if ($response->getStatus() == 'APPROVED') {
                return '';
            }
        }
        return 'ERROR';
    }


    /**
     * Set this card as primary card payment_id of CC can be fetched from get_cards_list webservice.
     *
     * @uses $_POST['email']
     * @uses $_POST['payment_id']
     * @return array {'code' => 1043, 'message' => 'set_primary_card_success', 'result' => array()}
     */
    public function set_primary_card() {
         ci()->load->library(array(
            'payment/payment_api',
        ));
        $customer_id = MobileContext::getCustomerIDLoggedIn();
        $payment_id = $this->input->post('payment_id');

        payment_api::set_primary_card($customer_id, $payment_id);

        $data = array(
            'code' => 1043,
            'message' => 'set_primary_card_success',
            'result' => ''
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Get all cards by customer email
     *
     * @uses $_POST['email']
     * @return array {'code' => 1044, 'message' => 'get_cards_list_success', 'result' => array(), 'has_invoice_payment_method' => true/false, 'invoice_payment_method_name' => name}
     */
    public function get_cards_list()
    {
        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);
        $customer_id = $customer->customer_id;

        $address = $this->customers_address_m->get_by('customer_id', $customer_id);

        // Get all credit cards
        $cards_list = $this->payment_m->get_payment_account($customer_id, 0, 1000);

        $has_invoice_payment_method = false;
        if (!empty($customer->invoice_code)) {
            $has_invoice_payment_method = true;
        }
        $invoice_payment_method_name = '';
        if (!empty($address)) {
            $invoice_payment_method_name = $address->invoicing_address_name . ' - ' . $address->invoicing_company;
        }

        $data = array(
            'code' => 1044,
            'message' => 'get_cards_list_success',
            'result' => $cards_list,
            'has_invoice_payment_method' => $has_invoice_payment_method,
            'invoice_payment_method_name' => $invoice_payment_method_name
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Trash item by customer emailand envelope id
     *
     * @uses $_POST['email']
     * @uses $_POST['envelope_id']
     * @return array {'code' => 1046, 'message' => 'trash_item_success', 'result' => array()}
     */
    public function trash_item()
    {
        $this->load->library('scans/scans_api');
        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);
        $customer_id = $customer->customer_id;

        $ids_input = $this->input->post('envelope_id');

        if (!empty($ids_input)) {
            $ids = explode(",", $ids_input);

            EnvelopeUtils::trashEnvelopes($ids, $customer);
        }

        $data = array(
            'code' => 1046,
            'message' => 'trash_item_success',
            'result' => ''
        );
        $this->api_success_output($data);
        exit();
    }

    /**
     * Gets list pricing location. Gets list pricing location. void
     *
     * @return array {'code' => 1047, 'message' => 'list_pricing_location_success', 'result' => array()}
     */
    public function list_pricing_location()
    {

        // Gets list access location.
        $list_access_location = $this->location_m->get_all();

        $data = array(
            'code' => 1047,
            'message' => 'list_pricing_location_success',
            'result' => $list_access_location
        );
        $this->api_success_output($data);
        exit();
    }

    function get_paging_output($total, $limit, $page)
    {
        $response = new stdClass();
        $response->page = $page;
        if ($total > 0) {
            $total_pages = ceil($total / $limit);
        } else {
            $total_pages = 0;
        }
        $response->total = $total_pages;
        $response->records = $total;
        return $response;
    }

    /**
     * Update new notification flag.
     */
    private function update_new_notification_flag($envelope_id, $customer_id)
    {
        mailbox_api::update_new_notification_flag($envelope_id, $customer_id);
    }

    private function cal_old_invoice($invoice)
    {
        $row = $invoice;
        $row->pre_date = $row->invoice_month;

        return $row;
    }

    function getPayoneStatus($txaction)
    {
        if (strtoupper($txaction) == 'PAID') {
            return 'OK';
        } else if (strtoupper($txaction) == 'REFUND' || strtoupper($txaction) == 'DEBIT') {
            return 'Refund';
        } else {
            return 'Pending';
        }
    }

    /**
     * Display file scan with envelope id and file type (ENVELOPE or ITEM scan)
     */
    public function get_file_scan()
    {
        $email = $this->input->get_post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);
        $customer_id = $customer->customer_id;
        // Does not use layout
        //        $this->template->set_layout(FALSE);
        $envelope_id = $this->input->get_post('envelope_id');
        $item_type = $this->input->get_post('type');

        // Get envelope information
        $envelope = $this->envelope_m->get_by("id", $envelope_id);
        if (empty($envelope)) {
            echo "Envelope id: " . $envelope_id . ' does not exist';
            exit();
        }

        $local_file_name = '';
        // Check if this is first letter
        if (APUtils::endsWith($envelope->envelope_code, '_000')) {
            if ($item_type == '1') {
                $local_file_name = Settings::get(APConstants::FIRST_ENVELOPE_KEY);
            } else if ($item_type == '2') {
                $local_file_name = Settings::get(APConstants::FIRST_LETTER_KEY);
            }
        } else {

            // Get file information
            //            $customer_id = APContext::getCustomerCodeLoggedIn();
            $preview_file = $this->envelope_file_m->get_by_many_order(array(
                "envelope_id" => $envelope_id,
                "customer_id" => $customer_id,
                "type" => $item_type
            ), array(
                "updated_date" => "ASC",
                "created_date" => "ASC"
            ));
            if (empty($preview_file)) {
                return;
            }
            //    print_r($preview_file);exit;
            // Download data from amazon
            APUtils::download_amazon_file($preview_file);

            // Get local file name
            $local_file_name = $preview_file->local_file_name;
        }
        if (!file_exists($local_file_name)) {
            // log_message(APConstants::LOG_ERROR, "Local file name of envelope id: " . $envelope_id . ' does not exist. File name:' . $local_file_name . ' does not exist');
            exit();
        }

        // Get extends file
        header('Content-Disposition: inline');
        $ext = substr($local_file_name, strrpos($local_file_name, '.') + 1);
        $ext = strtolower($ext);
        switch ($ext) {
            case 'jpg' :
                header('Content-type: image/jpeg');
                break;
            case 'jpge' :
                header('Content-type: image/jpeg');
                break;
            case 'png' :
                header('Content-type: image/png');
                break;
            case 'tiff' :
                header('Content-type: image/tiff');
                break;
            case 'pdf' :
                header('Content-type: application/pdf');
                break;
        }

        $seconds_to_cache = APConstants::CACHED_SECONDS;

        $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
        header("Expires: $ts");
        header("Pragma: cache");
        header("Cache-Control: max-age=$seconds_to_cache");

        readfile($local_file_name);
    }

    /**
     * Create direct charge
     */
    function direct_charge_without_invoice()
    {
        $this->load->library('payment/payone');
        $this->lang->load('customers/customer');
        $customer_id = MobileContext::getCustomerIDLoggedIn();

        // Get envelope information
        $customer = $this->customer_m->get($customer_id);

        $this->form_validation->set_rules($this->validation_direct_charge_without_invoice);
        if ($this->form_validation->run()) {
            try {
                $amount = str_replace(',', '.', $this->input->get_post('tran_amount'));
                $invoice_id = APUtils::genetateReferenceForOpenBalance($customer_id);
                $credit_card_id = $this->input->get_post('card_id');

                // Check invoice method
                if ($customer->invoice_type == '2') {
                    $message = sprintf(lang('customer.notsupport_direct_charge_without_invoice'), $customer->email);
                    $data = array(
                        'code' => 1101,
                        'message' => $message,
                        'result' => ''
                    );
                    $this->api_error_output($data);
                    return;
                }

                // Validate amount
                if ($amount < 0) {
                    $message = lang('customer.save_direct_charge_without_invoice_validate');
                    $data = array(
                        'code' => 1102,
                        'message' => $message,
                        'result' => ''
                    );
                    $this->api_error_output($data);
                    return;
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
                    $data = array(
                        'code' => 1100,
                        'message' => $message,
                        'result' => ''
                    );
                    $this->api_success_output($data);
                    return;
                } // Redirect case
                else if ($result != APConstants::ON_FLAG) {
                    $data = array(
                        'code' => 1103,
                        'message' => '',
                        'result' => htmlentities($result)
                    );
                    $this->api_success_output($data);
                    return;
                } // Fail
                else {
                    $message = sprintf(lang('customer.save_direct_charge_without_invoice_error'), $customer->email);
                    $data = array(
                        'code' => 1104,
                        'message' => $message,
                        'result' => ''
                    );
                    $this->api_error_output($data);
                    return;
                }
            } catch (Exception $e) {
                $message = sprintf(lang('customer.save_direct_charge_without_invoice_error'), $customer->email);
                $data = array(
                    'code' => 1105,
                    'message' => $message,
                    'result' => ''
                );
                $this->api_error_output($data);
                return;
            }
        } else {
            $errors = $this->form_validation->error_json();
            $data = array(
                'code' => 1107,
                'message' => 'Input parameter is invalid',
                'result' => json_encode($errors)
            );
            $this->api_error_output($data);
            return;
        }
    }

    /**
     * Register new device
     */
    function register_device()
    {
        $this->load->model('api/customer_push_token_m');
        $customer_id = MobileContext::getCustomerIDLoggedIn();

        $push_id = $this->input->get_post('push_id');
        $mobile_device_id = $this->input->get_post('mobile_device_id');
        $platform = $this->input->get_post('platform');
        if (empty($push_id) || empty($mobile_device_id) || empty($platform)) {
        	$data = array(
                'message' => 'Input parameter is invalid'
            );
            $this->api_error_output($data);
            return;
        }
        if ($platform != APConstants::MOBILE_PLATFORM_ANDROID && $platform != APConstants::MOBILE_PLATFORM_IOS) {
        	$data = array(
                'message' => 'The platform value should be android or ios.'
            );
            $this->api_error_output($data);
            return;
        }

        // Check exist device id by customer
        $customer_push_token_check = $this->customer_push_token_m->get_by_many(array(
        	'mobile_device_id' => $mobile_device_id,
        	//'customer_id' => $customer_id,
        	'platform' => $platform
        ));

        if (empty($customer_push_token_check)) {
        	$this->customer_push_token_m->insert(array(
	        	'mobile_device_id' => $mobile_device_id,
	        	'customer_id' => $customer_id,
	        	'platform' => $platform,
        		'push_id' => $push_id,
        		'active_flag' => APConstants::ON_FLAG
	        ));
        } else {
        	$this->customer_push_token_m->update_by_many(array(
	        	'mobile_device_id' => $mobile_device_id,
	        	'platform' => $platform
	        ), array(
        		'push_id' => $push_id,
                'customer_id' => $customer_id,
        		'active_flag' => APConstants::ON_FLAG
	        ));
        }

        $data = array(
        	'message' => 'Register device success'
        );
        $this->api_success_output($data);
        return;
    }

    /**
     * Get app version
     */
    function get_app_version()
    {
        $app_code = parent::getAppCode();
        $app_key = parent::getAppKey();
        // Get session information
        $app_check = $this->app_external_m->get_by_many(array (
        	'app_code' => $app_code,
        	'app_key' => $app_key
        ));
        if (empty($app_check)) {
        	$data = array(
                'code' => 1107,
                'message' => 'The app code or app key is invalid.',
            );
            $this->api_error_output($data);
            return;
        }
        $data = array(
            'message' => 'get_app_version_success',
            'result' => json_encode($app_check)
        );
        $this->api_success_output($data);
    }

	/**
     * Invoice tick or untick
     */
    public function collect_shipment()
    {
        $this->load->library('scans/scans_api');
        $customer_id = MobileContext::getCustomerIDLoggedIn();
        $includeAllStoreFlag = $this->input->get_post('includeAllStoreFlag');
        $postbox_id = $this->input->get_post('postbox_id');

        // additional params
        $shipping_rate_id = $this->input->get_post('shipping_rate_id');
        $shipping_rate = $this->input->get_post("shipping_rate", "0");
        // Mapping with column envelope->shipping->forwarding_charges_postal
        $raw_postal_charge = $this->input->get_post("raw_postal_charge", "0");
        // Mapping with column envelope->shipping->customs_handling_fee
        $raw_customs_handling = $this->input->get_post("raw_customs_handling", "0");
        // Mapping with column envelope->shipping->forwarding_charges_fee
        $raw_handling_charges = $this->input->get_post("raw_handling_charges", "0");
        // Get number of parcel
        $number_parcel = $this->input->get_post("number_parcel", "0");
        // Replace "," by "."
        $shipping_fee = str_replace(",", ".", $shipping_rate);

        // Make collect shipment request for all storage items
        if ($includeAllStoreFlag == '1') {
            scans_api::makeCollectShipment($postbox_id, $customer_id);
        }

        // Get all customer have envelope item marked request collect
        $listCollectiveItems = scans_api::getListCollectiveShippingItems($customer_id, $postbox_id);
        if (empty($listCollectiveItems)) {
            $message = lang('collect_shipment_warning');
            $data = array(
                'code' => 1033,
                'message' => $message
            );
            $this->api_error_output($data);
        }

        $list_id = array();
        foreach($listCollectiveItems as $item){
            $list_id[] = $item->id;

            // Validate weight
            $validWeight = true;
            if (empty($shipping_rate_id)) {
                $validWeight = shipping_api::checkValidCollectItem($item->id);
            } else {
                $validWeight = shipping_api::checkValidCollectItemByShippingService($item->id, $shipping_rate_id);
            }
            if (!$validWeight) {
                $message = lang('collect_shipment_over68_warning');
                $data = array(
                    'code' => 1033,
                    'message' => $message,
                    'result' => ""
                );
                $this->api_error_output($data);
                return;
            }

            // Update shipping rate
            // Don't use this data model. Will remove later
            $this->envelope_m->add_shipping_rate($item->id, $shipping_rate, $shipping_rate_id);
            shipping_api::saveShippingServiceFee($item->id, $shipping_fee, $raw_postal_charge,
                        $raw_customs_handling, $raw_handling_charges,
                        $number_parcel, $shipping_rate_id);
            shipping_api::saveShippingAddress($item->id, $item->shipping_address_id);
        }

        $check_prepayment_data = CustomerUtils::checkApplyShippingPrepayment(APConstants::TRIGGER_ACTION_TYPE_CUSTOMER, APConstants::SHIPPING_SERVICE_NORMAL,
                     APConstants::SHIPPING_TYPE_COLLECT, $list_id, $customer_id);

        if ($check_prepayment_data['prepayment'] == true) {
            $list_envelope_id_str = implode(',', $list_id);
            mailbox_api::requestCollectShippingToQueue($list_envelope_id_str, $customer_id);
            $check_prepayment_data['status'] = FALSE;
            $check_prepayment_data['list_envelope_id'] = $list_envelope_id_str;
            $check_prepayment_data['status'] = FALSE;
            $data = array(
                'code' => 1033,
                'message' => 'The prepayment required to process',
                'result' => $check_prepayment_data
            );
            $this->api_error_output($data);
            return;
        }

        $location_available_id = $listCollectiveItems[0]->location_available_id;
        $package_id = scans_api::createCollectiveShippingPackage($customer_id, $location_available_id);
        $declare_customs_obj = scans_api::updatePackageIDForAllCollectiveShippingItems($customer_id, $location_available_id, $package_id, $postbox_id);
        if ($declare_customs_obj['declare_customs_flag'] != APConstants::ON_FLAG) {
            foreach ($listCollectiveItems as $collectiveItem) {
                scans_api::insertCompleteItem($collectiveItem->id, APConstants::REQUEST_TRACKING_NUMBER_ACTIVITY_TYPE, APConstants::TRIGGER_BY_CUSTOMER, $customer_id);
            }
        }
        $message = lang('collect_shipment_success');
        $data = array(
            'code' => 1033,
            'message' => $message
        );
        $this->api_success_output($data);
    }

    /**
     * Get paypal detail fee
     */
    public function get_paypal_fee()
    {
        $customer_id = MobileContext::getCustomerIDLoggedIn();
        $payment_amount = $this->input->get_post('payment_amount');
        $amount_obj = APUtils::includePaypalTransactionFee($payment_amount, $customer_id);

        $paypal_tran_fee = $amount_obj['paypal_transaction_fee'];
        $paypal_tran_vat = $amount_obj['paypal_transaction_vat'];
        $total_amount = $amount_obj['total_amount'];
        $total_amount = number_format($total_amount, 2);

        $payment_amount_data = array(
            "paypal_tran_fee" => $paypal_tran_fee,
            "paypal_tran_vat" => $paypal_tran_vat,
            "total_amount" => $total_amount
        );
        $data = array(
            'message' => 'get_paypal_fee sucessfully',
            'result' => $payment_amount_data
        );
        $this->api_success_output($data);
        return;
    }

    /**
     * Create paypal transaction
     */
    public function create_paypal_transaction()
    {
        $this->load->model('payment/paypal_tran_hist_m');
        $customer_id = MobileContext::getCustomerIDLoggedIn();
        $customer = APContext::getCustomerByID($customer_id);

        $payment_amount = $this->input->get_post('payment_amount');
        $payment_id = $this->input->get_post('txn_id');

        if (empty($payment_amount) || empty($payment_id)) {
            $data = array(
                'message' => 'The payment_amount and txn_id are required.',
            );
            $this->api_error_output($data);
            return;
        }
        $txn_id = '';
        try {
            $this->load->library('payment/paypal');
            $txn_id = $this->paypal->get_payment_info($payment_id);
        } catch (Exception $ex) {
            log_message(APConstants::LOG_ERROR, $ex);
            $data = array(
                'message' => 'The txn_id is invalid.',
            );
            $this->api_error_output($data);
            return;
        }

        if(empty($txn_id)) {
            $data = array(
                'message' => 'The txn_id is invalid.',
            );
            $this->api_error_output($data);
            return;
        }

        $amount_obj = APUtils::includePaypalTransactionFee($payment_amount, $customer_id);
        $paypal_tran_fee = $amount_obj['paypal_transaction_fee'];
        $paypal_tran_vat = $amount_obj['paypal_transaction_vat'];

        // Insert to paypal transaction hist
        $primary_location_id = APUtils::getPrimaryLocationBy($customer_id);
        $invoice_id = APUtils::genetateReferenceForOpenBalance($customer_id);

        // Check exist paypal tranaction hist
        $paypal_tran_hist_check = $this->paypal_tran_hist_m->get_by_many(array(
            'customer_id' => $customer_id,
            'txn_id' => $txn_id
        ));

        // Insert new paypal tranaction hist
        if (empty($paypal_tran_hist_check)) {
            $this->paypal_tran_hist_m->insert(
            array(
                'customer_id' => $customer_id,
                'invoice_id' => $invoice_id,
                'amount' => $amount_obj['total_amount'],
                'paypal_tran_fee' => $paypal_tran_fee,
                'paypal_tran_vat' => $paypal_tran_vat,
                'currency' => Settings::get(APConstants::PAYMENT_PAYPAL_CURRENCY_CODE),
                'description' => 'payment for clevvermail services for account ' . $customer->email,
                "location_id" => $primary_location_id,
                'created_date' => now(),
                'status' => APConstants::PAYPAl_STATUS_PENDING,
                'last_updated_date' => now(),
                'txn_id' => $txn_id
            ));
        }

        $data = array(
            'message' => 'create_paypal_transaction sucessfully',
        );
        $this->api_success_output($data);
        return;
    }

    /**
     * get envelope custom list.
     * @return type
     */
    public function get_evelope_custom_list(){

        $customer_id = MobileContext::getCustomerIDLoggedIn();

        // Load all pending envelope need to declare customs
        $envelope_has_customs = mailbox_api::getEnvelopeCustoms($customer_id);

        // Load all pending envelope need to declare customs
        $list_pending_envelope_customs = mailbox_api::getListPendingEnvelopeCustoms($customer_id);

        $payment_amount_data = array(
            "envelope_has_customs" => $envelope_has_customs,
            "list_pending_envelope_customs" => $list_pending_envelope_customs
        );
        $data = array(
            'message' => 'get_paypal_fee sucessfully',
            'result' => $payment_amount_data
        );
        $this->api_success_output($data);
        return;
    }

    public  function get_list_address_customer(){

        $customer = MobileContext::getCustomerLoggedIn();

        if($customer){

            $list_address = addresses_api::get_list_address_customer($customer->customer_id);
            $data = array(
                'message' => 'get list addresses successfully.',
                'result'  => $list_address
            );
            $this->api_success_output($data);
        }

    }

    public function delete_address_customer(){

        $customer = MobileContext::getCustomerLoggedIn();

        $shipping_address_id = (int) $this->input->post('shipping_address_id');

        if($customer && $shipping_address_id){

            $allowDeleteAddress = scans_api::checkShppingAdress($customer->customer_id, $shipping_address_id);

            if(!$allowDeleteAddress){

                $data = array(
                    'message' => lang('shpping_address_is_using'),
                );
                $this->api_error_output($data);
                return;
            }

            $resultDelete = addresses_api::deleteAlternativeAddress($shipping_address_id);
            if($resultDelete){

                $data = array(
                    'message' => lang('delete_alternative_address_success'),
                );
                $this->api_success_output($data);
                return;
            }
            else{
                $data = array(
                    'message' => lang('delete_alternative_address_error'),
                );
                $this->api_error_output($data);
                return;
            }

        }
        else{
            $data = array(
                'message' => 'The systems not found address',
            );
            $this->api_error_output($data);
            return;
        }
    }

    public function save_forward_address(){
        $customer_forward_id = (int) $this->input->post('customer_forward_id');

        $email = $this->input->post('email');
        $customer = $this->customer_m->get_active_customer_by_email($email);

        if(empty($customer)){

            $data = array(
                'message' => 'The systems not found customer',
                'result' => ''
            );
            $this->api_error_output($data);
            exit();
        }

        $customer_id = $customer->customer_id;

        $is_primary_address = $this->input->post('is_primary_address');
        $shipment_address_name = $this->input->post('shipment_address_name');
        $shipment_company = $this->input->post('shipment_company');
        $shipment_street = $this->input->post('shipment_street');
        $shipment_postcode = $this->input->post('shipment_postcode');
        $shipment_city = $this->input->post('shipment_city');
        $shipment_region = $this->input->post('shipment_region');
        $shipment_country = $this->input->post('shipment_country');
        $shipment_phone_number = $this->input->post('shipment_phone_number');
        $active_flag = 1;

        if (empty($email) || empty($customer) || (empty($shipment_address_name) && empty($shipment_company))
                || empty($shipment_street) || empty($shipment_postcode) || empty($shipment_city) || empty($shipment_country)) {
            $data = array(
                'message' => 'invalid or missing required parameters',
                'result' => ''
            );
            $this->api_error_output($data);
            exit();
        }

        $data = array(
                'customer_id'           => $customer_id,
                'shipment_address_name' => $shipment_address_name,
                'shipment_company'      => $shipment_company,
                'shipment_street'       => $shipment_street,
                'shipment_postcode'     => $shipment_postcode,
                'shipment_city'         => $shipment_city,
                'shipment_region'       => $shipment_region,
                'shipment_country'      => $shipment_country,
                'shipment_phone_number' => $shipment_phone_number,
                'active_flag'           => $active_flag,
                'update_date'           => date("Y-m-d H:i:s")
        );
        if($is_primary_address == 1){
            unset($data['active_flag']);
            unset($data['update_date']);
            // save primary address
            $customer_address_check = $this->customers_address_m->get_by('customer_id', $customer_id);
            if(!empty($customer_address_check)){
                $customer_forward_id = $customer_address_check->customer_id;
                $this->customers_address_m->update_by_many(array('customer_id' => $customer_id), $data);
            }else{
                $customer_forward_id = $this->customers_address_m->insert($data);
            }

            // Update data to customer
            $this->customer_m->update_by_many(array(
                "customer_id" => $customer_id
            ), array(
                "shipping_address_completed" => APConstants::ON_FLAG
            ));

            // update: convert registration process flag to customer_product_setting.
            CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, APConstants::activate_shipping_address_completed, APConstants::ON_FLAG);

            $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
            $open_balance = $open_balance_data['OpenBalanceDue'];
            if ($open_balance <= 0.1) {
                // we dont have to tell the Customer that a valid credit Card with non working payment can reactivate the account.
                // in most cases the Customer will Chose a Card that can handle the payment if it is valid
                // Only reactivate if deactivated_type = auto
                $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;
                CustomerProductSetting::doActiveCustomer($customer_id, $created_by_id);
            }
        }else{
            // save alternative forwarding address.
            $customer_forward_id = addresses_api::save_forward_address($customer_forward_id,  $data);
        }

        $response = array(
            'message' => 'Save forward address have been sucessfully',
            'result' => array("customer_forward_id" => $customer_forward_id)
        );
        $this->api_success_output($response);
        return;
    }

    public function save_shipping_address(){
        $customer_id = MobileContext::getCustomerIDLoggedIn();
        $shipping_address_id = (int) $this->input->post('shipping_address_id');
        $envelope_id = (int) $this->input->post('envelope_id');
        $include_all_flag = $this->input->get_post('include_all_flag', '0');
        $green_flag = $this->input->get_post('green_flag', '0');

        if(empty($customer_id) || empty($envelope_id))
        {
            $data = array(
                'message' => 'Customer not login or param envelope_id invalid',
            );
            $this->api_error_output($data);
            return;
        }

        scans_api::saveShippingAddress($shipping_address_id, $envelope_id, $customer_id, $include_all_flag, $green_flag);
        $data = array(
            'message' => 'Save shipping addresses have been successfully.',
        );

        $this->api_success_output($data);
        return;

    }

    public function check_customs_flag()
    {
        $this->load->library('price/price_api');
        $postbox_id = $this->input->get_post("postbox_id", "0");

        $envelope_id = $this->input->get_post("envelope_id", "0");

        $customer_id = MobileContext::getCustomerIDLoggedIn();
        //$customer_id = 32888;
        $list_envelope_apply_customs = array();

        $customs_process_flag = APConstants::OFF_FLAG;

        $response = array(
            'message' => '',
            'result' => ''
        );

        if(empty($postbox_id)){
            $response['message'] = lang('customs.postbox_id');
            $this->api_error_output($response);
            return;
        }

        if(empty($envelope_id)){
            $response['message'] = lang('customs.envelope_id');
            $this->api_error_output($response);
            return;
        }

        if(empty($customer_id)){
            $response['message'] = lang('customs.customer_id');
            $this->api_error_output($response);
            return;
        }

        $envelope = $this->envelope_m->get($envelope_id);
        $phone_number = EnvelopeUtils::get_phone_number($customer_id, $envelope);

        // gets pricing template
        $postbox = $this->postbox_m->get($envelope->postbox_id);
        // $pricing_map = price_api::getPricingModelByPostboxID($envelope->postbox_id, $postbox->type);
        $pricing_maps = price_api::getPricingModelByCusotomerAndLocationID($customer_id, $postbox->location_available_id);
        $pricing_map = $pricing_maps[$postbox->type];

        $check_flag = EnvelopeUtils::check_customs_flag($customer_id, $postbox_id, $envelope_id);
        $response['message'] = lang('customs.check_customs_flag_successful');
        $response['result'] = array(
            "customs_process_flag" => intval($check_flag),
            "phone_number"         => $phone_number,
            "custom_declaration_outgoing_01" => $pricing_map['custom_declaration_outgoing_01'],
            "custom_declaration_outgoing_02" => $pricing_map['custom_declaration_outgoing_02']
        );

        $this->api_success_output($response);
        return;

    }

    public function confirm_customs_declare()
    {
        $this->load->model('addresses/customers_address_m');
        $this->load->model('settings/countries_m');
        $this->template->set_layout(FALSE);
        $customer_id = MobileContext::getCustomerIDLoggedIn();
        //$customer_id = 32888;

        // Get envelope information
        $address = $this->customers_address_m->get_by('customer_id', $customer_id);
        // Get all countries
        if (is_numeric($address->invoicing_country)) {
            $invoice_country = $this->countries_m->get_by_many(array(
                "id" => $address->invoicing_country
            ));
        } else {
            $invoice_country = new stdClass();
            $invoice_country->country_name = $address->invoicing_country;
        }

        // Display the current page
        $this->template->set('address', $address);
        $this->template->set('invoice_country', $invoice_country);
        $this->template->build('mailbox/confirm_customs_declare');
    }

    /*
    *  var customs_data = [{"material_name":"Name","quantity":"5","cost":"120000"}]
       var envelope_id
       var phone_number
    */
    public function save_declare_customs()
    {
        $envelope_id  = $this->input->get_post('envelope_id');
        $phone_number = $this->input->get_post("phone_number");
        $customs_data = $this->input->get_post('customs_data');

        $declare_customs = json_decode($customs_data);

        $customer_id = MobileContext::getCustomerIDLoggedIn();
        //$customer_id = 32888;

        $result_save_customs = mailbox_api::save_declare_customs($customer_id, $envelope_id, $phone_number, $declare_customs);

        $response = array(
            'message' => '',
            'result' => ''
        );

        if($result_save_customs == true){

            $response['message'] = lang('api_save_declare_customs_successful');
            $this->api_success_output($response);
            return;
        }
        else {

            $response['message'] = lang('api_save_declare_customs_error');
            $this->api_error_output($response);
            return;
        }
    }

    public function get_detail_envelope()
    {
        $envelope_id  = $this->input->get_post('envelope_id');
        $response = array(
            'message' => '',
            'result' => ''
        );

        if(empty($envelope_id)){

            $response['message'] = lang('get_detail_envelope.empty_envelope_id');
            $this->api_error_output($response);
            return;
        }

        $envelope = $this->envelope_m->get($envelope_id);
        $phone_number = "";
        if(!empty($envelope)){

            $envelope->envelope_type_label = Settings::get_label(APConstants::ENVELOPE_TYPE_CODE, $envelope->envelope_type_id);

            $phone_number = EnvelopeUtils::get_phone_number($envelope->to_customer_id, $envelope);

            $envelope->phone_number = $phone_number;
        }

        if($envelope->collect_shipping_flag == "0"){
            $envelope->total_item = 0;
            $envelope_custom = $this->envelope_customs_m->get_by_many(array("envelope_id" => $envelope_id));
            if($envelope_custom){
                $envelope_customs = $this->envelope_customs_m->get_many_by_many(array(
                    "package_id" => $envelope_custom->package_id
                ));

                $list_envelope_id = array();
                foreach($envelope_customs as $ec){
                    $list_envelope_id[] = $ec->envelope_id;
                }
                $envelopes = $this->envelope_m->get_many_by_many(array(
                    "id in (".implode(',', $list_envelope_id).")" => null
                ));

                $weight = 0;
                foreach($envelopes as $e){
                    $weight += $e->weight;
                }
                $envelope->weight = floatval($weight);
                $envelope->total_item = count($envelope_customs);
            }
        }

        $response['result'] = $envelope;
        $this->api_success_output($response);
        return;

    }

    public function regist_envelope_customs() {
        $ids_input = $this->input->get_post('envelope_id');
        $postbox_id = $this->input->get_post('postbox_id');
        $shipping_type = $this->input->get_post('shipping_type');

        $customer_id = MobileContext::getCustomerIDLoggedIn();
        $response = array(
            'message' => '',
            'result' => ''
        );

        if (empty($ids_input) || count($ids_input) == 0) {
            $response['message'] = lang('api_regist_envelope_customs.empty_envelope_id');
            $this->api_error_output($response);
            return;
        }

        $ids = explode(",", $ids_input);
        $result = false;
        foreach($ids as $id){
            $check_flag = EnvelopeUtils::check_customs_flag($customer_id, $postbox_id, $id);
            if($check_flag == APConstants::ON_FLAG){
                $result = mailbox_api::regist_envelope_customs($customer_id, $id, $postbox_id, $shipping_type);
            }
        }

        if($result){
            $response['message'] = lang('api_regist_envelope_customs.successfully');
            $this->api_success_output($response);
        } else{
            $response['message'] = lang('api_regist_envelope_customs.error');
            $this->api_error_output($response);
        }
        return;
    }

    /**
     * Gets shipping calculator screen.
     * @return type
     */
    public function get_shipping_calculator(){
        $customerID = MobileContext::getCustomerIDLoggedIn();

        // get init data.
        $VAT = APUtils::getVatRateOfCustomer($customerID)->rate;
        $locations = addresses_api::getAllLocationsForDropDownList();
        $countries = settings_api::getAllCountriesForDropDownList();
        $currencies = settings_api::getAllCurrenciesForDropDownList();
        $customerAddress = addresses_api::getCustomerAddress($customerID);
        $standardCurrency = customers_api::getStandardCurrency($customerID);
        //$standardDecimalSeparator = customers_api::getStandardDecimalSeparator($customerID);

        $response = array();
        $response['result'] = array(
            "vat" => $VAT,
            "location" => $locations,
            "countries" =>$countries,
            "currencies" =>$currencies,
            "customerAddress" =>$customerAddress ? $customerAddress : null,
            "standardCurrency" =>$standardCurrency ? $standardCurrency : null,
            //"standardDecimalSeparator" =>$standardDecimalSeparator ? $standardDecimalSeparator : null,
        );
        $this->api_success_output($response);
        return;
    }

    /**
     * Get the list of available standard shipment services per location
     */
    public function get_shipping_services_by_location()
    {
        $locationID = $this->input->get_post("location_id");
        $location = addresses_api::getLocationByID($locationID);
        $serviceID = isset($location->available_shipping_services) ? $location->available_shipping_services : '';
        $shippingServiceIDs = explode(',', $serviceID);
        $shipping_service_type = '';
        $listShippingServices = shipping_api::getListShippingServicesByIDs($shippingServiceIDs, $shipping_service_type, true);

        $response = array();
        $response['result'] = $listShippingServices;
        $this->api_success_output($response);
        return true;
    }

    /**
     * Get the selected shipment service's description
     */
    public function get_shipping_service_description()
    {
        $shippingServiceID = $this->input->get_post("shipment_service_id", 0);
        $shippingService = shipping_api::getShippingServiceByID($shippingServiceID);

        $response = array();
        $response['result'] = $shippingService->long_desc;
        $this->api_success_output($response);
        return true;
    }

    /**
     * calculate shipping fee for checking.
     * @return type
     */
    public function shipping_calculator(){
        $customerID = MobileContext::getCustomerIDLoggedIn();
         $shippingInfo = array(
                ShippingConfigs::CUSTOMER_ID => $customerID,
                ShippingConfigs::LOCATION_ID => $this->input->get_post("location_id"),
                ShippingConfigs::SERVICE_ID => $this->input->get_post("shipment_service_id"),
                ShippingConfigs::SHIPPING_TYPE => $this->input->get_post("shipment_type_id"),
                ShippingConfigs::CUSTOMS_VALUE => $this->input->get_post("custom_insurance_value", 0),
                ShippingConfigs::STREET => $this->input->get_post("shipment_street"),
                ShippingConfigs::POSTAL_CODE => $this->input->get_post("shipment_postcode"),
                ShippingConfigs::CITY => $this->input->get_post("shipment_city"),
                ShippingConfigs::REGION => $this->input->get_post("shipment_region"),
                ShippingConfigs::COUNTRY_ID => $this->input->get_post("shipment_country_id")
            );

            $weight = $this->input->get_post('weight', 0);
            $separate_package_flag = true;
            $result = shipping_api::shipping_calculator(
                    $customerID,
                    $shippingInfo,
                    $this->input->get_post('number_of_parcels'),
                    $this->input->get_post('length', 0),
                    $this->input->get_post('width', 0),
                    $this->input->get_post('height', 0),
                    $weight, // Unit g in the screen
                    $this->input->get_post('multiple_quantity'),
                    $this->input->get_post('multiple_number_shipment', ''),
                    $this->input->get_post('multiple_length'),
                    $this->input->get_post('multiple_width'),
                    $this->input->get_post('multiple_height'),
                    $this->input->get_post('multiple_weight'), // Unit is g in the screen
                    $this->input->get_post('currency_id'),
                    $separate_package_flag);


            $response = array();

            if($result['status']){
                $response['message'] = "";
                $response['result'] = $result['data'];
                $this->api_success_output($response);
            }else{
                $response['message'] = $result['data']['errors'];
                $response['result'] = "";
                $this->api_error_output($response);
            }
            return ;
    }

    /**
     * calculate shipping item on mailbox.
     */
    public function calculate_shipping_all(){
        // load library
        $this->load->model('scans/envelope_properties_m');
        $this->load->library('common/common_api');
        $this->load->helper('info/functions');
        $this->lang->load('shipping/shipping');

        // gets params.
        $list_envelope_id = $this->input->get_post("envelope_id", "0");
        $shipping_type = $this->input->get_post("shipping_type", "0");
        $postbox_id = $this->input->get_post("postbox_id", "0");
        $envelope_ids = explode(',', $list_envelope_id);
        $included_all_flag = $this->input->get_post("included_all_flag", "0");

        $customer_id = MobileContext::getCustomerIDLoggedIn();

        // defualt fedex logo.
        $fedex_default_logo = base_url().APConstants::FEDEX_DEFAULT_LOGO_PATH;

        $list_envelopes = array();
        // direct shipping case.
        if ($shipping_type == ShippingConfigs::DIRECT_SHIPPING) {
            $list_envelopes = $this->envelope_m->get_many_by_many(array(
                'id' => $envelope_ids[0],
                "to_customer_id" => $customer_id
            ));

        } // collect shipping case.
        else{
            $list_envelope_id = array();
            // if customer click included all item.
            if($included_all_flag){
                $list_envelopes = $this->envelope_m->get_many_by_many(array(
                    "postbox_id" => $postbox_id,
                    "to_customer_id" => $customer_id,
                    "( (storage_flag =1 AND current_storage_charge_fee_day > 0  AND collect_shipping_flag <> '1'
                            AND collect_shipping_flag <> '2' AND direct_shipping_flag <> '1' AND direct_shipping_flag <> '2')
                        OR (direct_shipping_flag IS NULL AND collect_shipping_flag IS NULL))" => null,
                    "trash_flag IS NULL" => null
                ));
            }else{
                $list_envelopes = $this->envelope_m->getAllReadyCollectItems($customer_id,$postbox_id);
            }
        }

        if(!$list_envelopes){
            $response['message'] = strip_tags(lang("could_not_calculate_shipping_fedex_message"));
            $this->api_error_output($response);
            return;
        }

        // Get customer address
        $envelope = $list_envelopes[0];
        $envelope_id = $envelope->id;
        $customers_address = shipping_api::getShippingAddressByEnvelope($customer_id, $envelope_id);

        // Call api to get rates
        try {
            $cost_object = shipping_api::calculateCostOfAllServices($customer_id, $list_envelopes,$envelope_id, $shipping_type);
            if ($cost_object == null) {
                $response['message'] = strip_tags(lang('could_not_calculate_shipping_fedex_message'));
                $this->api_error_output($response);
                return;
            }
            $data = array();
            foreach($cost_object['data'] as $obj){
                $obj['logo_url'] = $obj['logo_url'] ? $obj['logo_url'] : $fedex_default_logo;
                $data[] = $obj;
            }

            $response['result'] = array(
                'shipping_type'=>$shipping_type,
                'allServicesRates'=> $data,
                'customers_address' => $customers_address
            );
            $response['message'] = "";
            $this->api_success_output($response);
        } catch (Exception $ex) {
            $response['message'] = strip_tags($ex->getMessage());
            $this->api_error_output($response);
        }
        return;
    }// end function calculate_shipping_all

    /**
     * confirm shipping service for direct shipment.
     * @return type
     */
    public function confirm_shipping_service(){

        $this->load->model('scans/envelope_properties_m');
        $this->load->model('scans/envelope_shipping_m');

        $envelope_id = $this->input->get_post("envelope_id", "0");
        $shipping_rate = $this->input->get_post("raw_total_charge", "0");
        $shipping_rate_id = $this->input->get_post("shipping_service_id", "0");

        // Mapping with column envelope->shipping->forwarding_charges_postal
        $raw_postal_charge = $this->input->get_post("raw_postal_charge", "0");

        // Mapping with column envelope->shipping->customs_handling_fee
        $raw_customs_handling = $this->input->get_post("raw_customs_handling", "0");

        // Mapping with column envelope->shipping->forwarding_charges_fee
        $raw_handling_charges = $this->input->get_post("raw_handling_charges", "0");

        // Get number of parcel
        $number_parcel = $this->input->get_post("number_parcel", "0");

        // Replace "," by "."
        $shipping_fee = str_replace(",", ".", $shipping_rate);

        // Don't use this data model. Will remove later
        $this->envelope_m->add_shipping_rate($envelope_id,$shipping_rate,$shipping_rate_id);

        // Update shipping information
        shipping_api::saveShippingServiceFee($envelope_id, $shipping_fee, $raw_postal_charge, $raw_customs_handling, $raw_handling_charges, $number_parcel, $shipping_rate_id);

        $response = array(
            'message' => '',
            'result' => ''
        );
        $this->api_success_output($response);
        return;
    }

    /**
     * Gets list of envelopes for collect shippment.
     */
    public function get_envelope_collect_list(){
        $postbox_id = $this->input->get_post('postbox_id', 0);
        $included_all_flag = $this->input->get_post('included_all_flag', 0);

        $customer_id = MobileContext::getCustomerIDLoggedIn();

        // Gets all items in customs table with process = 0
        $envelope_customs = ci()->envelope_customs_m->get_many_by_many(array(
            "customer_id" => $customer_id,
            "postbox_id" => $postbox_id,
            "process_flag" => APConstants::OFF_FLAG
        ));
        $list_envelope_id = array();
        $list_envelope_id[] = 0;
        foreach($envelope_customs as $e){
            $list_envelope_id[] = $e->envelope_id;
        }

        if($included_all_flag){
            $list_envelopes = $this->envelope_m->get_many_by_many( array(
                "postbox_id" => $postbox_id,
                "to_customer_id" => $customer_id,
                "( (storage_flag =1 AND current_storage_charge_fee_day > 0  AND collect_shipping_flag <> '1'
                        AND collect_shipping_flag <> '2' AND direct_shipping_flag <> '1' AND direct_shipping_flag <> '2')
                    OR (direct_shipping_flag IS NULL AND collect_shipping_flag IS NULL)
                    OR (collect_shipping_flag = 0 AND (package_id is null OR package_id = 0)))" => null,
                "trash_flag IS NULL" => null,
                "id NOT IN (".implode(',', $list_envelope_id).")" => null
            ));
        }else{
            $list_envelopes = $this->envelope_m->get_many_by_many(array(
                "postbox_id" => $postbox_id,
                "to_customer_id" => $customer_id,
                "(collect_shipping_flag = 0 AND package_id is null AND trash_flag is null)" => null,
                "id NOT IN (".implode(',', $list_envelope_id).")" => null
            ));
        }

        // get envelope type label for envelop
        $envelope_list = $list_envelopes;
        if(!empty($list_envelopes)){
            foreach($envelope_list as $row){
                $row->envelope_type_label = Settings::get_label(APConstants::ENVELOPE_TYPE_CODE, $row->envelope_type_id);
                $row->weight = floatval($row->weight);
            }
        }

        $response = array(
            'message' => '',
            'result' => $envelope_list
        );
        $this->api_success_output($response);
    }

    /**
     * accept term & condition.
     */
    public function accept_term_condition(){
        $customer_id = MobileContext::getCustomerIDLoggedIn();
        $this->customer_m->update_by("customer_id", $customer_id, array(
            "accept_terms_condition_flag" => APConstants::ON_FLAG
        ));

        // active customer.
        $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;
        CustomerProductSetting::doActiveCustomer($customer_id, $created_by_id);
        $response = array(
            'message' => '',
            'result' => ""
        );
        $this->api_success_output($response);
        return;
    }

    /**
     * view report invoice of customer
     */
    public function view_invoice_pdf(){
        $this->load->library(array(
            'invoices/export'
        ));
        $customer_id = MobileContext::getCustomerIDLoggedIn();
        $invoice_code = $this->input->get_post('tran_id');
        $invoice_file_path = $this->export->export_invoice($invoice_code, $customer_id);

        // Get file
        $this->view_file($invoice_file_path);
    }

    /**
     * add accounting email interface.
     * @return type
     */
    public function add_accounting_email(){
        // Gets customerid logged in.
        $customer_id = MobileContext::getCustomerIDLoggedIn();

        $accounting_email_validation_rules = array(
            array(
                'field' => 'postbox_id',
                'label' => 'Postbox',
                'rules' => 'required'
            ),
            array(
                'field' => 'interface_id',
                'label' => 'Interface',
                'rules' => 'required'
            ),
            array(
                'field' => 'accounting_email',
                'label' => 'Email',
                'rules' => 'required|valid_email|max_length[255]'
            )
        );

        $this->form_validation->set_rules($accounting_email_validation_rules);

        if ($this->form_validation->run()) {
            $accountingEmail   = $this->input->post('accounting_email');
            $postbox_id = $this->input->get_post('postbox_id', '');
            $interface_id   = $this->input->post('interface_id');
            $auto_send_pdf   = $this->input->post('auto_send_pdf');

            cloud_api::save_accounting_email($customer_id, $postbox_id, $accountingEmail, $interface_id, $auto_send_pdf);

            $response = array(
                'message' => '',
                'result' => ""
            );
            $this->api_success_output($response);
        } else {
            $errors = $this->form_validation->error_json();
            $data = array(
                'code' => 1107,
                'message' => 'Input parameter is invalid',
                'result' => json_encode($errors)
            );
            $this->api_error_output($data);
        }
        return;
    }

    /**
     * list interface of customer.
     */
    public function list_interface(){
        $list_customer_id = array();
        $customer_id = MobileContext::getCustomerIDLoggedIn();
        $isPrimaryCustomer = MobileContext::isPrimaryCustomerUser();
        if ($isPrimaryCustomer) {
            $list_customer_id = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($customer_id);
            $list_customer_id[] = $customer_id;
        } else {
            $list_customer_id[] = $customer_id;
        }
        $customer_cloud_service = $this->customer_cloud_m->get_all_cloud($customer_id);

        $response = array(
            'message' => '',
            'result' => array(
                "list_cloud_services" => $customer_cloud_service
        ) );
        $this->api_success_output($response);
        return;
    }

    /**
     * delete interface by id.
     * @return type
     */
    public function delete_interface(){
        $customer_id = MobileContext::getCustomerIDLoggedIn();
        $cloud_id = $this->input->post('cloud_id');
        $postbox_id = $this->input->post('postbox_id');

        // delete interface by id.
        cloud_api::delete_interface($customer_id, $cloud_id, $postbox_id);
        $response = array(
            'message' => 'You have delete this interface successfull',
            'result' => ""
        );
        $this->api_success_output($response);
        return;
    }

    /**
     * Gets list verification case.
     */
    public function get_list_verification_case(){
        $this->load->library(array(
            'cases/verification_api'
        ));

        // get list cases trigger
        $customer_id = MobileContext::getCustomerIDLoggedIn();
        $list_case_result = verification_api::get_list_case_result_verification($customer_id);

        $response = array(
            'message' => '',
            'result' => $list_case_result
        );
        $this->api_success_output($response);
    }

    /**
     * start case verification.
     */
    public function start_verification_personal_identification(){
        $case_id = $this->input->get_post("case_id", '');

        // Check exist this case
        $case_exist = $this->cases_m->get($case_id);

        $response = array(
            'message' => '',
            'result' => ''
        );

        if(empty($case_exist)){
            $response['message'] = 'This case does not exists';
            $this->api_error_output($response);
            return;
        }
        $response['result'] = mobile_cases_api::start_personal_verification($case_exist, 1);
        $this->api_success_output($response);
        return;
    }

    /**
     * save personal verfication
     */
    public function save_verification_personal_identification(){
        $case_id = $this->input->get_post("case_id", '');
        $base_taskname = $this->input->post('base_taskname');
        $comment_for_registration_content = $this->input->get_post('comment_for_registration_content');
        $result = mobile_cases_api::save_personal_identification_verification($case_id, $base_taskname, 1, $comment_for_registration_content);
        $response = array(
            'message' => $result['message'],
            'result' => $result
        );
        if($result['status']){
            $this->api_success_output($response);
        }else{
             $this->api_error_output($response);
        }
        return;
    }

    /**
     * start verificaton of company soft.
     */
    public function start_verification_company_identification_soft(){
        $case_id = $this->input->get_post("case_id", '');

        // Check exist this case
        $case_exist = $this->cases_m->get($case_id);

        $response = array(
            'message' => '',
            'result' => ''
        );

        if(empty($case_exist)){
            $response['message'] = 'This case does not exists';
            $this->api_error_output($response);
            return;
        }
        $response['result'] = mobile_cases_api::start_personal_verification($case_exist, 2);
        $this->api_success_output($response);
        return;
    }

    /**
     * save verification of company soft.
     */
    public function save_verification_company_identification_soft(){
        $case_id = $this->input->get_post("case_id", '');
        $base_taskname = $this->input->post('base_taskname');
        $comment_for_registration_content = $this->input->get_post('comment_for_registration_content');
        $result = mobile_cases_api::save_personal_identification_verification($case_id, $base_taskname, 2, $comment_for_registration_content);
        $response = array(
            'message' => $result['message'],
            'result' => $result
        );
        if($result['status']){
            $this->api_success_output($response);
        }else{
             $this->api_error_output($response);
        }
        return;
    }

    /**
     * upload verification file for personal identification and company soft verification.
     * @return type
     */
    public function upload_verification(){
        $case_id = $this->input->post('case_id');
        $input_file_client_name = $this->input->post('client_file_name');
        $base_taskname = $this->input->post('base_taskname');

        $result = mobile_cases_api::upload_verification($case_id, $base_taskname, $input_file_client_name);
        $response = array(
            'message' => $result['message'],
            'result' => $result
        );

        if($result["status"]) {
            $this->api_success_output($response);
        }else{
             $this->api_error_output($response);
        }
        return;
    }

    /**
     * view document verification file.
     */
    public function view_case_document(){
        $case_id = $this->input->post('case_id');
        $local_file_name = $this->input->post('client_file_name');
        $base_taskname = $this->input->post('base_taskname');
        $file_id = $this->input->post('file_id');

        // get local file path.
        $local_file_path = mobile_cases_api::get_case_document($case_id, $base_taskname, $local_file_name, $file_id);

        // Get file
        $this->view_file($local_file_path);
    }

    /**
     * contract term and condition.
     */
    public function start_TC_contract_MS(){
        $case_id = $this->input->get_post("case_id", '');
        $base_taskname = "TC_contract_MS";

        // Check exist this case
        $case_exist = $this->cases_m->get_many($case_id);
        $response = array(
            'message' => '',
            'result' => ''
        );

        if(empty($case_exist)){
            $response['message'] = 'This case does not exists';
            $this->api_error_output($response);
            return;
        }
        // get start case.
        $response['result'] = mobile_cases_api::start_term_and_condition_case($case_id, $base_taskname);

        $this->api_success_output($response);
        return;
    }

    /**
     * save term & condition case.
     */
    public function save_TC_contract_MS(){
        $case_id = $this->input->get_post('case_id');
        $base_taskname = "TC_contract_MS";
        $case_exist = $this->cases_m->get_many($case_id);
        if (empty($case_exist)) {
            $response['message'] = 'This case does not exists';
            $this->api_error_output($response);
            return;
        }
        $customer_id = APContext::getCustomerByCase($case_id);

        $case_resource = ci()->case_resource_m->get_by_many(array(
            "case_id" => $case_id,
            "base_taskname" => $base_taskname
        ));

        if(empty($case_resource)){
            $response['message'] = 'You must upload term & condition file.';
            $this->api_error_output($response);
            return;
        }

        // save case
        mobile_cases_api::save_term_and_condition_case($case_id, $base_taskname, $customer_id);

        $response = array(
            'message' => '',
            'result' => ''
        );
        $this->api_success_output($response);
        return;
    }

    /**
     * create term & condition pdf.
     */
    public function create_term_condition_pdf(){
        $case_id = $this->input->get_post('case_id');
        $base_taskname = $this->input->get_post('base_taskname');
        $customer_id = APContext::getCustomerByCase($case_id);
        $response = array(
            'message' => '',
            'result' => ''
        );
        if(empty($customer_id)){
            $response['message'] = 'This case does not exists';
            $this->api_error_output($response);
            return;
        }

        // get customer.
        $customer = APContext::getCustomerByID($customer_id);
        $filename = verification_api::create_term_condition_pdf($customer);

        // Get file
        $this->view_file($filename);
    }

    /**
     * view file.
     *
     * @param type $local_file_path
     */
    public function view_file($local_file_path){
        // Get extends file
        header('Content-Disposition: inline');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($local_file_path));
        header('Accept-Ranges: bytes');

        $ext = substr($local_file_path, strrpos($local_file_path, '.') + 1);
        $ext = strtolower($ext);
        switch ($ext) {
            case 'jpg':
                header('Content-Type: image/jpeg');
                break;
            case 'jpeg':
                header('Content-Type: image/jpeg');
                break;
            case 'png':
                header('Content-Type: image/png');
                break;
            case 'tiff':
                header('Content-Type: image/tiff');
                break;
            case 'pdf':
                header('Content-Type: application/pdf');
                break;
        }

        readfile($local_file_path);
    }

    /**
     * start proof_of_address_MS
     */
    public function start_proof_of_address_MS(){
        $case_id = $this->input->get_post("case_id", '');
        $base_taskname = "proof_of_address_MS";

        // Check exist this case
        $case_exist = $this->cases_m->get_many($case_id);
        $response = array(
            'message' => '',
            'result' => ''
        );

        if(empty($case_exist)){
            $response['message'] = 'This case does not exists';
            $this->api_error_output($response);
            return;
        }
        // get start case.
        $response['result'] = mobile_cases_api::start_proof_of_address_MS($case_id, $base_taskname);

        $this->api_success_output($response);
        return;
    }

    /**
     * save_proof_of_address_MS
     */
    public function save_proof_of_address_MS(){
        $case_id = $this->input->get_post('case_id');
        $base_taskname = "proof_of_address_MS";
        $comment_for_registration_content = $this->input->post("comment");

        $case_exist = $this->cases_m->get_many($case_id);
        if (empty($case_exist)) {
            $response['message'] = 'This case does not exists';
            $this->api_error_output($response);
            return;
        }
        $customer_id = APContext::getCustomerByCase($case_id);

        $case_resource = ci()->case_resource_m->get_by_many(array(
            "case_id" => $case_id,
            "base_taskname" => $base_taskname
        ));

        if(empty($case_resource)){
            $response['message'] = 'You must upload business file.';
            $this->api_error_output($response);
            return;
        }

        // save case
        mobile_cases_api::save_proof_of_address_MS($case_id, $base_taskname, $customer_id, $comment_for_registration_content);

        $response = array(
            'message' => '',
            'result' => ''
        );
        $this->api_success_output($response);
        return;
    }

    public function start_company_verification_E_MS(){
        $case_id = $this->input->get_post("case_id", '');
        $base_taskname = "company_verification_E_MS";

        $response = array(
            'message' => '',
            'result' => ''
        );

        // Check exist this case
        $case_exist = $this->cases_m->get($case_id);
        if (empty($case_exist)) {
            $response['message'] = 'This case does not exists';
            $this->api_error_output($response);
            return;
        }

        $response['result'] = mobile_cases_api::start_company_verification_E_MS($case_id, $base_taskname, $case_exist->postbox_id);
        $this->api_success_output($response);
        return;
    }

    public function save_company_verification_E_MS(){
        $case_id = $this->input->get_post('case_id');
        $base_taskname = "company_verification_E_MS";
        $case_exist = $this->cases_m->get_many($case_id);
        $response = array(
            'message' => '',
            'result' => ''
        );
        if (empty($case_exist)) {
            $response['message'] = 'This case does not exists';
            $this->api_error_output($response);
            return;
        }

        $comment_for_registration_content = $this->input->post("comment");
        $description = $this->input->post("description");
        $comment_for_registration_date = null;
        if(!empty($comment_for_registration_content)){
            $comment_for_registration_date = now();
        }
        // get mail receiver
        $mail_receiver_name = $this->input->get_post("mail_receiver_name");
        $mail_receiver_ids = $this->input->get_post("mail_receiver_id");

        // get business license, mail receiver, officer.
        $officer_file_ids = $this->input->get_post("officer_file_id");
        $officer_names = $this->input->get_post("officer_name");
        $officer_rates = $this->input->get_post("officer_rate");

        // get validate message.
        $error_message = mobile_cases_api::validateCompanyVerification_E_MS($case_id, $base_taskname, $description);
        if(!empty($error_message)){
            $response['message'] = $error_message;
            $this->api_error_output($response);
            return;
        }

        // get customer id
        $customer_id = APContext::getCustomerByCase($case_id);

        // do save function.
        mobile_cases_api::save_company_verification_E_MS($case_id, $base_taskname, $customer_id, $description, $comment_for_registration_content,
                $comment_for_registration_date, $mail_receiver_name, $mail_receiver_ids, $officer_file_ids, $officer_names, $officer_rates);

        $this->api_success_output($response);
        return;
    }
}

?>
