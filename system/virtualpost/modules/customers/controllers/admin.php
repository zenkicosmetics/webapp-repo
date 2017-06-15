<?php defined('BASEPATH') or exit ('No direct script access allowed');

/**
 * Admin controller for the customer module
 */
class Admin extends Admin_Controller
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
            'rules' => 'required|trim|matches[repeat_password]|min_length[6]|max_length[255]'
        ),
        array(
            'field' => 'repeat_password',
            'label' => 'lang:repeat_password',
            'rules' => 'required|trim|min_length[6]|max_length[255]'
        ),
        array(
            'field' => 'charge_fee_flag',
            'label' => 'lang:charge_fee_flag',
            'rules' => ''
        ),
        array(
            'field' => 'required_verification_flag',
            'label' => 'Verification Address',
            'rules' => ''
        ),
        array(
            'field' => 'shipping_factor_fc',
            'label' => 'Customer based shipping factor FC',
            'rules' => ''
        ),
        array(
            'field' => 'required_prepayment_flag',
            'label' => 'Pre-Payment',
            'rules' => ''
        )
    );
    private $validation_rules02 = array(
        array(
            'field' => 'email',
            'label' => 'lang:email',
            'rules' => 'required|valid_email|max_length[255]|callback__check_email'
        ),
        array(
            'field' => 'charge_fee_flag',
            'label' => 'lang:charge_fee_flag',
            'rules' => ''
        ),
        array(
            'field' => 'required_verification_flag',
            'label' => 'Verification Address',
            'rules' => ''
        ),
        array(
            'field' => 'shipping_factor_fc',
            'label' => 'Customer based shipping factor FC',
            'rules' => ''
        ),
        array(
            'field' => 'required_prepayment_flag',
            'label' => 'Pre-Payment',
            'rules' => ''
        )
    );
    private $validation_rules03 = array(
        array(
            'field' => 'password',
            'label' => 'lang:password',
            'rules' => 'required|trim|matches[repeat_password]|min_length[6]|max_length[255]'
        ),
        array(
            'field' => 'repeat_password',
            'label' => 'lang:repeat_password',
            'rules' => 'required|trim|min_length[6]|max_length[255]'
        )
    );

    // Rule for save external record payment
    private $validation_rules04 = array(
        array(
            'field' => 'tranDate',
            'label' => 'Date',
            'rules' => 'required|trim'
        ),
        array(
            'field' => 'tranId',
            'label' => 'Transaction ID',
            'rules' => 'required|trim'
        ),
        array(
            'field' => 'tranAmount',
            'label' => 'Amount',
            'rules' => 'required|trim'
        )
    );

    // Rule for save external record payment
    private $validation_rules05 = array(
        array(
            'field' => 'tranAmount',
            'label' => 'Amount',
            'rules' => 'required|trim'
        )
    );
    private $add_blacklist_validation_rules = array(
        array(
            'field' => 'email',
            'label' => 'email',
            'rules' => 'required|trim'
        )
    );

    private $history_postbox_validation_rules = array(
        array(
            'field' => 'customer',
            'label' => 'Search text',
            'rules' => 'required|trim'
        )
    );

    private $customer_history_validation_rules = array(
        array(
            'field' => 'customer',
            'label' => 'Search text',
            'rules' => 'required|trim'
        )
    );
    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();

        // Load the required classes
        $this->load->model('customer_m');
        $this->load->model('customers/customer_blacklist_m');
        $this->load->model('customers/customer_blacklist_hist_m');
        $this->load->model('settings/countries_m');
        $this->load->model('cloud/customer_cloud_m');
        $this->load->model('addresses/customers_address_m');
        $this->load->model('scans/envelope_summary_month_m');

        // $this->load->helper('customer');
        $this->load->library('form_validation');
        $this->lang->load('customer');
        $this->lang->load('account/account');

        // load the theme_example view
        $this->load->model('invoices/invoice_detail_m');
        $this->load->model('email/email_m');
        $this->load->model('invoices/invoice_summary_m');
        $this->load->model('invoices/invoice_summary_by_location_m');
        $this->load->model('mailbox/postbox_m');
        $this->load->model('scans/envelope_m');
        $this->load->model('payment/payment_m');
        $this->load->model('invoices/vatcase_m');

        $this->load->model('addresses/location_m');
        $this->load->model('partner/partner_m');

        $this->load->model('scans/envelope_shipping_m');

        // load external api
        $this->load->library(array(
            "customers/customers_api",
            "mailbox/mailbox_api",
            "invoices/invoices_api",
            "scans/scans_api"
        ));
    }

    /**
     * List all customer
     */
    public function index()
    {
        $this->load->library('customers/customers_api');
        // Get input condition
        $enquiry = APUtils::sanitizing($this->input->get_post("enquiry"));
        $hideDeletedCustomer = $this->input->get_post("hideDeletedCustomer");

        $list_access_location = APUtils::loadListAccessLocation();

        // location filter
        $this->template->set('list_access_location', $list_access_location);

        // If current request is ajax
        if ($this->is_ajax_request()) {

            $input_paging = $this->get_paging_input();
            $location_id = $this->input->get_post("location_id");
            $account_type = $this->input->get_post("account_type");
            $response = customers_api::get_list_customer($enquiry, $hideDeletedCustomer, $input_paging, $location_id, 0, $account_type);

            echo json_encode($response['web_customer_list']);
        } else {
            // Display the current page
            $this->template->set('header_title', lang('header:list_customer_title'))->build('admin/index');
        }
    }

    /**
     * Generate invoice code
     */
    public function generate_invoice_code()
    {
        $id = $this->input->get_post("id");

        $response = customers_api::generate_invoice_code($id);
        if($response['status']){
            $this->success_output($response['message'], array(
                'invoice_code' => $response['invoice_code']
            ));
        }
        else {
            $this->success_output($response['message']);
        }

    }

    /**
     * Get main post box address
     *
     * @param unknown_type $customer_id
     */
    private function get_main_postbox_address($customer_id)
    {
        return $this->postbox_m->get_main_postbox_address($customer_id);
    }

    /**
     * Get activated status.
     *
     * @param unknown_type $activated_flag
     */
    private function get_activated_status($activated_flag)
    {
        if ($activated_flag === '1') {
            return lang('customer.activated');
        } else {
            return lang('customer.not_activated');
        }
    }

    /**
     * Get deleted status.
     *
     * @param unknown_type $delete_detail_flag
     */
    private function get_status($activated_flag, $status)
    {
        if ($status === '1') {
            return lang('customer.status.deleted');
        } else if ($activated_flag === '1') {
            return lang('customer.activated');
        } else {
            return lang('customer.not_activated');
        }
    }

    /**
     * Get activated status.
     *
     * @param unknown_type $activated_flag
      this function have been move to customers_api
     */
    /*
    private function get_dropbox_status($cloud_id)
    {
        if (!empty ($cloud_id)) {
            return lang('customer.yes');
        } else {
            return lang('customer.no');
        }
    }
    */
    /**
     * Tinh toan so luong item (envelope scan, document scan, shipping).
     *
     * @param unknown_type $customer_id
     this function have been move to customers_api
     */
    /*
    private function cal_scan_items($customer)
    {
        $customer_id = $customer->customer_id;
        $startDate = $customer->created_date;
        $endDate = now();
        $diff_month = APUtils::getMongthDiff($startDate, $endDate);
        if ($diff_month == 0) {
            $diff_month = 1;
        }

        $scan_summary = ci()->envelope_summary_month_m->summary_envelope($customer_id);
        $envelope_scan_number = $scan_summary->envelope_scan_number;
        $document_scan_number = $scan_summary->document_scan_number;
        $shipping_number = $scan_summary->direct_shipping_number;
        $shipping_number += $scan_summary->collect_shipping_number;

        return array(
            "envelope_scan_number" => empty ($envelope_scan_number) ? 0 : APUtils::number_format($envelope_scan_number / $diff_month, 0),
            "document_scan_number" => empty ($document_scan_number) ? 0 : APUtils::number_format($document_scan_number / $diff_month, 0),
            "shipping_number" => empty ($shipping_number) ? 0 : APUtils::number_format($shipping_number / $diff_month, 0)
        );
    }
    */
    /**
     * Method for handling different form actions
     */
    public function add()
    {
        $this->template->set_layout(FALSE);

        $customer = new stdClass();
        $customer->customer_id = '';

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->form_validation->set_rules($this->validation_rules);

            if ($this->form_validation->run()) {

                // Check black list customer
                if (APUtils::existBlackListEmail($this->input->get_post('email'))) {
                    $message = lang('customer.exist_in_black_list');
                    $this->error_output($message);
                    return;
                }

                try {
                    $insert_data = $this->input->post();
                    $insert_data['account_type'] = APConstants::NORMAL_CUSTOMER;
                    $insert_data['password'] = md5($insert_data ['password']);
                    $activated_key = APUtils::generateRandom(30);
                    $insert_data['activated_key'] = $activated_key;
                    $insert_data['created_date'] = now();
                    $insert_data['status'] = "0";
                    $insert_data['accept_terms_condition_flag'] = APConstants::ON_FLAG;
                    $created_by_id = APContext::getAdminIdLoggedIn();
                    $customer_id = $this->customer_m->insert($insert_data, null, $created_by_id);
                    $activated_url = APContext::getFullBalancerPath() . "customers/active?key=" . $activated_key;

                    // added: case verifycation.
                    if ($this->input->post('required_verification_flag') == APConstants::ON_FLAG) {
                        CaseUtils::start_verification_case($customer_id, true);
                    } else {
                        CaseUtils::cancel_verification_case($customer_id);
                    }

                    // Dang ky thong tin vao postbox
                    $postbox_id = $this->postbox_m->insert(array(
                        "customer_id" => $customer_id,
                        "postbox_name" => "",
                        "name" => "",
                        "company" => "",
                        "type" => APConstants::FREE_TYPE,
                        "is_main_postbox" => 1,
                        "location_available_id" => 1,// default berlin location
                        "first_location_flag" => 1,
                        "name_verification_flag" => APConstants::OFF_FLAG,
                        "company_verification_flag" => APConstants::OFF_FLAG,
                        "created_date" => now()
                    ));

                    // Create default postbox
                    customers_api::addPostboxHistory($postbox_id, APConstants::POSTBOX_CREATE, APConstants::FREE_TYPE);
                    // Send email confirm for user
                    $email_template = $this->email_m->get_by('slug', APConstants::new_customer_register);
                    $data = array(
                        "full_name" => $this->input->get_post('email'),
                        "email" => $this->input->get_post('email'),
                        "password" => $this->input->get_post('password'),
                        "account_type" => APConstants::FREE_TYPE,
                        "active_url" => $activated_url,
                        "site_url" => APContext::getFullBalancerPath()
                    );
                    $content = APUtils::parserString($email_template->content, $data);
                    MailUtils::sendEmail('', $this->input->get_post('email'), $email_template->subject, $content);

                    $message = sprintf(lang('customer.add_success'), $this->input->post('email'));
                    $this->success_output($message);
                } catch (Exception $e) {
                    $message = sprintf(lang('customer.add_error'), $this->input->post('email'));
                    $this->error_output($message);
                }
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        } else {
            // Loop through each validation rule
            foreach ($this->validation_rules as $rule) {
                $customer->{$rule['field']} = set_value($rule['field']);
            }
            $customer->shipping_factor_fc = '1.0';

            // Display the current page
            $this->template->set('customer', $customer)->set('action_type', 'add')->build('admin/form');
        }
    }

    /**
     * Method for handling different form actions
     */
    public function edit()
    {
        ci()->load->library('customers/customers_api');
        ci()->load->library('mailbox/mailbox_api');
        ci()->load->library('email/email_api');
        ci()->load->library('payment/payment_api');
        ci()->load->library('partner/partner_api');
        ci()->load->library('payment/payone');

        $this->template->set_layout(FALSE);

        $customerID = $this->input->get_post("id");
        $customer = new stdClass ();
        $isEnterpriseCustomer = APContext::isEnterpriseCustomerByID($customerID);
        if ($customerID) {
            $conditionNamesCustomer1 = array("customer_id", "invoice_type IS NULL");
            $conditionValuesCustomer1 = array($customerID, $invoice_type = null);
            $dataNamesCustomer1 = array("invoice_type", "invoice_code");
            $dataValuesCustomer1 = array(1, '');
            customers_api::updateCustomer($conditionNamesCustomer1, $conditionValuesCustomer1, $dataNamesCustomer1, $dataValuesCustomer1);

            $customer = customers_api::getCustomerByID($customerID);

            if (!empty ($customer)) {
                $customer->repeat_password = '';
                $customer->password = '';
            }
        }

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->form_validation->set_rules($this->validation_rules02);
            if ($this->form_validation->run()) {

                $updateData  = $this->input->post();
                $created_by_id = APContext::getAdminIdLoggedIn();
                $save_result = customers_api::save_customer($customer, $updateData, $created_by_id);

                //Send info to update customer in Payone system
                $this->payone->update_user($customerID);

                // Update pricing type
                if ($isEnterpriseCustomer) {
                    $pricing_type = $this->input->get_post("pricing_type");
                    if (empty($pricing_type)) {
                        $pricing_type = APConstants::CUSTOMER_PRICING_TYPE_NORMAL;
                    }
                    AccountSetting::set($customerID, APConstants::CUSTOMER_PRICING_TYPE, $pricing_type);
                }


                if($save_result['status']){
                    $this->success_output($save_result['message']);
                    return true;
                }
                else{

                    $this->error_output($save_result['message']);
                    return false;
                }

            }  else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return false;
            }
        }
        if ($isEnterpriseCustomer) {
            $pricing_type = AccountSetting::get($customerID, APConstants::CUSTOMER_PRICING_TYPE);
            if (empty($pricing_type)) {
                $pricing_type = APConstants::CUSTOMER_PRICING_TYPE_NORMAL;
                AccountSetting::set($customerID, APConstants::CUSTOMER_PRICING_TYPE, $pricing_type);
            }
            $this->template->set('pricing_type', $pricing_type);
        }
        $this->template->set('isEnterpriseCustomer', $isEnterpriseCustomer);
        // #581: change first location flag.
        $postbox_first = mailbox_api::getFirstLocationBy($customerID);
        $location_list = mailbox_api::getLocationListBy($customerID);

        $this->template->set('first_location', $postbox_first);
        $this->template->set('location_list', $location_list);

        // Gets partner code if have.
        $partner = partner_api::getPartnerCodeByCustomer($customerID);

        $partner_code = $partner ? $partner->partner_code : "";
        $this->template->set('partner_code', $partner_code);

        // Display the current page
        $this->template->set('customer', $customer);
        $this->template->set('action_type', 'edit');
        $this->template->build('admin/form');
    }

    /**
     * Method for handling different form actions
     */
    public function change_pass()
    {
        $customer_id = $this->input->get_post("id");
        $customer = new stdClass ();
        if (!empty ($customer_id)) {
            $customer = $this->customer_m->get($customer_id);
            if (!empty ($customer)) {
                $customer->repeat_password = '';
                $customer->password = '';
            }
        }

        $this->template->set_layout(FALSE);

        if ($_POST) {

            $this->form_validation->set_rules($this->validation_rules03);

            if ($this->form_validation->run()) {

                $insert_data = $this->input->post();
                $created_by_id = APContext::getAdminIdLoggedIn();
                $result  =  customers_api::change_pass_customer($customer, $insert_data, $created_by_id);
                if($result['status']){
                    $this->success_output($result['message']);
                    return;
                }
                else{

                    $this->error_output($result['message']);
                    return;
                }

            } else {

                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        $this->template->set('customer', $customer)->set('action_type', 'edit')->build('admin/change_pass');
    }

    /**
     * Delete customer
     */
    public function delete()
    {
        $customer_id = $this->input->get_post("id");
        $add_blacklist_flag = $this->input->get_post("add_blacklist_flag");
        $charge_flag = $this->input->get_post('charge');
        $created_by_id = APContext::getAdminIdLoggedIn();
       /*
        * #1180 create postbox history page like check item page
        *  Activity: APConstants::POSTBOX_DELETE_ORDER_BY_SYSTEM
        */
        $message = CustomerUtils::deleteCustomer($customer_id, true, $add_blacklist_flag, $charge_flag, APContext::getAdminIdLoggedIn(), $created_by_id);

        if ($message['message'] == null) {
            $message = sprintf(lang('customer.delete_success'));
            $this->success_output($message);
            return;
        } else {
            // $message = sprintf(lang('customer.delete_error'));
            $this->error_output($message);
            return;
        }
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
        $id = $this->input->get_post("id");
        $email = $this->input->get_post("email");
        // Get user information by email
        $customer = $this->customer_m->get_by_many(array(
            "email" => $email,

            // fixbug #492
            "(status is null OR status = 0)" => null
        ));

        if ($customer && $customer->customer_id != $id) {
            $this->form_validation->set_message('_check_email', lang('email_exist'));
            return false;
        }
        if ($customer && $customer->email != $email) {
            $this->form_validation->set_message('_check_email', lang('email_exist'));
            return false;
        }
        return true;
    }

    /**
     * Buil array to insert customer to db
     *
     * @param unknown_type $arr
     * @param unknown_type $key_arr
     * @return multitype:multitype:
     */
    private function _build_array_insert($arr)
    {
        $arr_insert = array();
        $len = count($arr);
        for ($i = 1; $i < $len; $i++) {
            if ($len > 1) {
                $arr_insert [] = array_combine($arr [0], $arr [$i]);
            }
        }
        return $arr_insert;
    }

    /**
     * View detail information for admin.
     */
    public function view_detail_customer()
    {
        $this->template->set_layout(FALSE);

        #1058 add multi dimension capability for admin
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $currency_sign = APUtils::get_currency_sign_in_user_profiles();
        $currency_rate = APUtils::get_currency_rate_in_user_profiles();
        $date_format = APUtils::get_date_format_in_user_profiles();

        $id = $this->input->get_post("id");
        $customer_id = $id;
        $envelope_id = $this->input->get_post("envelope_id");
        if (empty ($id)) {
            return;
        }
        $customer = new stdClass ();
        $customer = $this->customer_m->get($id);
        $customer_shipping_address = $this->customers_address_m->get($id);
        $main_postbox = $this->postbox_m->get_by_many(array(
            'customer_id' => $id,
            'is_main_postbox' => '1'
        ));
        $total_vat = 1.19;
        // #472
        // $vat = APUtils::getVatFeeByCustomer($id);


        // 20140515 DuNT Start fixbug #203
        if ($customer_shipping_address) {
            if (is_numeric($customer_shipping_address->shipment_country)) {
                $ship_country = $this->countries_m->get($customer_shipping_address->shipment_country);
                if ($ship_country) {
                    $customer_shipping_address->shipment_country = $ship_country->country_name;
                }
            }
            if (is_numeric($customer_shipping_address->invoicing_country)) {
                $invoice_country = $this->countries_m->get($customer_shipping_address->invoicing_country);
                if ($invoice_country) {
                    $customer_shipping_address->invoicing_country = $invoice_country->country_name;
                }
            }
        }

        // Gets customer information.


        // 20140515 DuNT End fixbug #203
        $customer_complete_ship_address = $this->envelope_shipping_m->get_by_many(array(
            "envelope_id" => $id,
            "envelope_id" => $envelope_id
        ));
        $customer_cloud = $this->customer_cloud_m->get_by_many(array(
            "customer_id" => $id
        ));
        $scan_item = customers_api::cal_scan_items($customer);
        if ($customer_cloud) {
            $dropbox_status = customers_api::get_dropbox_status($customer_cloud->cloud_id);
        } else {
            $dropbox_status = customers_api::get_dropbox_status(null);
        }

        // Gets customer infor.
        $postbox_counts = $this->postbox_m->get_postbox_count_by_customer($id);
        $free_postbox_count = 0;
        $private_postbox_count = 0;
        $business_postbox_count = 0;
        foreach ($postbox_counts as $postbox_count) {
            if ($postbox_count->type == '1') {
                $free_postbox_count = $postbox_count->box_count;
            } else if ($postbox_count->type == '2') {
                $private_postbox_count = $postbox_count->box_count;
            } else if ($postbox_count->type == '3') {
                $business_postbox_count = $postbox_count->box_count;
            }
        }

        $target_month = APUtils::getTargetMonthInvoice();
        $target_year = APUtils::getTargetYearInvoice();
        $next_invoices = $this->invoice_summary_m->get_by_many(array(
            'invoice_month' => $target_year . $target_month,
            'customer_id' => $id
        ));

        $next_invoices_display = new stdClass ();
        $next_invoices_display->postboxes_amount = 0;
        $next_invoices_display->envelope_scanning_amount = 0;
        $next_invoices_display->scanning_amount = 0;
        $next_invoices_display->additional_items_amount = 0;
        $next_invoices_display->shipping_handing_amount = 0;
        $next_invoices_display->storing_amount = 0;
        $next_invoices_display->additional_pages_scanning_amount = 0;
        if ($next_invoices) {
            // Postbox amount
            $next_invoices_display->postboxes_amount += empty ($next_invoices->free_postboxes_amount) ? 0 : $next_invoices->free_postboxes_amount;
            $next_invoices_display->postboxes_amount += empty ($next_invoices->private_postboxes_amount) ? 0 : $next_invoices->private_postboxes_amount;
            $next_invoices_display->postboxes_amount += empty ($next_invoices->business_postboxes_amount) ? 0 : $next_invoices->business_postboxes_amount;
            // #472
            // $next_invoices_display->postboxes_amount =
            // $next_invoices_display->postboxes_amount / ($total_vat -
            // $vat);
            $next_invoices_display->postboxes_amount = $next_invoices_display->postboxes_amount ;

            // Envelope scanning amount
            $next_invoices_display->envelope_scanning_amount += empty ($next_invoices->envelope_scan_free_account) ? 0 : $next_invoices->envelope_scan_free_account;
            $next_invoices_display->envelope_scanning_amount += empty ($next_invoices->envelope_scan_private_account) ? 0 : $next_invoices->envelope_scan_private_account;
            $next_invoices_display->envelope_scanning_amount += empty ($next_invoices->envelope_scan_business_account) ? 0 : $next_invoices->envelope_scan_business_account;
            // #472
            // $next_invoices_display->envelope_scanning_amount =
            // $next_invoices_display->envelope_scanning_amount /
            // ($total_vat - $vat);
            $next_invoices_display->envelope_scanning_amount = $next_invoices_display->envelope_scanning_amount;

            // Item scanning amount
            $next_invoices_display->scanning_amount += empty ($next_invoices->item_scan_free_account) ? 0 : $next_invoices->item_scan_free_account;
            $next_invoices_display->scanning_amount += empty ($next_invoices->item_scan_private_account) ? 0 : $next_invoices->item_scan_private_account;
            $next_invoices_display->scanning_amount += empty ($next_invoices->item_scan_business_account) ? 0 : $next_invoices->item_scan_business_account;
            // #472
            // $next_invoices_display->scanning_amount =
            // $next_invoices_display->scanning_amount / ($total_vat - $vat);
            #1058 add multi dimension capability for admin
            $next_invoices_display->scanning_amount = $next_invoices_display->scanning_amount ;

            // Additional item amount
            $next_invoices_display->additional_items_amount += empty ($next_invoices->incomming_items_free_account) ? 0 : ($next_invoices->incomming_items_free_account);
            $next_invoices_display->additional_items_amount += empty ($next_invoices->incomming_items_private_account) ? 0 : ($next_invoices->incomming_items_private_account );
            $next_invoices_display->additional_items_amount += empty ($next_invoices->incomming_items_business_account) ? 0 : ($next_invoices->incomming_items_business_account );
            // $next_invoices_display->additional_items_amount =
            // $next_invoices_display->additional_items_amount /
            // ($total_vat - $vat);
            #1058 add multi dimension capability for admin
            $next_invoices_display->additional_items_amount = $next_invoices_display->additional_items_amount;

            // Additional item scanning amount
            $next_invoices_display->additional_pages_scanning_amount += empty ($next_invoices->additional_pages_scanning_free_amount) ? 0 : ($next_invoices->additional_pages_scanning_free_amount);
            $next_invoices_display->additional_pages_scanning_amount += empty ($next_invoices->additional_pages_scanning_private_amount) ? 0 : ($next_invoices->additional_pages_scanning_private_amount);
            $next_invoices_display->additional_pages_scanning_amount += empty ($next_invoices->additional_pages_scanning_business_amount) ? 0 : ($next_invoices->additional_pages_scanning_business_amount);
            #1058 add multi dimension capability for admin
            $next_invoices_display->additional_pages_scanning_amount = $next_invoices_display->additional_pages_scanning_amount;

            // Shipping handding amount
            $next_invoices_display->shipping_handing_amount += empty ($next_invoices->direct_shipping_free_account) ? 0 : $next_invoices->direct_shipping_free_account;
            $next_invoices_display->shipping_handing_amount += empty ($next_invoices->direct_shipping_private_account) ? 0 : $next_invoices->direct_shipping_private_account;
            $next_invoices_display->shipping_handing_amount += empty ($next_invoices->direct_shipping_business_account) ? 0 : $next_invoices->direct_shipping_business_account;
            $next_invoices_display->shipping_handing_amount += empty ($next_invoices->collect_shipping_free_account) ? 0 : $next_invoices->collect_shipping_free_account;
            $next_invoices_display->shipping_handing_amount += empty ($next_invoices->collect_shipping_private_account) ? 0 : $next_invoices->collect_shipping_private_account;
            $next_invoices_display->shipping_handing_amount += empty ($next_invoices->collect_shipping_business_account) ? 0 : $next_invoices->collect_shipping_business_account;
            // #472
            // $next_invoices_display->shipping_handing_amount =
            // $next_invoices_display->shipping_handing_amount * (1 +
            // $vat);
            #1058 add multi dimension capability for admin
            $next_invoices_display->shipping_handing_amount = $next_invoices_display->shipping_handing_amount ;

            // Storing amount
            $next_invoices_display->storing_amount += empty ($next_invoices->storing_letters_free_account) ? 0 : $next_invoices->storing_letters_free_account;
            $next_invoices_display->storing_amount += empty ($next_invoices->storing_letters_private_account) ? 0 : $next_invoices->storing_letters_private_account;
            $next_invoices_display->storing_amount += empty ($next_invoices->storing_letters_business_account) ? 0 : $next_invoices->storing_letters_business_account;
            $next_invoices_display->storing_amount += empty ($next_invoices->storing_packages_free_account) ? 0 : $next_invoices->storing_packages_free_account;
            $next_invoices_display->storing_amount += empty ($next_invoices->storing_packages_private_account) ? 0 : $next_invoices->storing_packages_private_account;
            $next_invoices_display->storing_amount += empty ($next_invoices->storing_packages_business_account) ? 0 : $next_invoices->storing_packages_business_account;
            // #472
            // $next_invoices_display->storing_amount =
            // $next_invoices_display->storing_amount / ($total_vat - $vat);
            #1058 add multi dimension capability for admin
            $next_invoices_display->storing_amount = $next_invoices_display->storing_amount;
        }

        $next_invoices_date = APUtils::viewDateFormat(strtotime(APUtils::getLastDayOfCurrentMonth()),$date_format);
        $this->template->set("next_invoices_date", $next_invoices_date);

        $account_type = lang('account_type_'.$customer->account_type);
        $this->template->set("account_type", $account_type);

        if ($customer->status === '1') {
            $customer_status = lang('customer.status.deleted');
        } else if ($customer->activated_flag === '1') {
            $customer_status =  lang('customer.activated');
        }
        else {
            $customer_status =  lang('customer.not_activated');
        }
        $this->template->set("customer_status", $customer_status);


        if ($customer->charge_fee_flag === '1') {
            $customer_charge_fee =  lang('customer.charge');
        }
        else {
            $customer_charge_fee = lang('customer.no_charge');
        }
        $this->template->set("customer_charge_fee", $customer_charge_fee);

        $next_invoices = $next_invoices_display;
        if($next_invoices){
        	#1058 add multi dimension capability for admin
            $postboxes = APUtils::view_currency_with_currency_sign_unit($next_invoices->postboxes_amount, $currency_short, $currency_sign, $currency_rate, $decimal_separator);
            $envelope_scanning = APUtils::view_currency_with_currency_sign_unit($next_invoices->envelope_scanning_amount, $currency_short, $currency_sign, $currency_rate, $decimal_separator);
            $scanning = APUtils::view_currency_with_currency_sign_unit($next_invoices->scanning_amount, $currency_short, $currency_sign, $currency_rate, $decimal_separator);
            $additional_items =  APUtils::view_currency_with_currency_sign_unit($next_invoices->additional_items_amount, $currency_short, $currency_sign, $currency_rate, $decimal_separator);
            $additional_scanning_items =  APUtils::view_currency_with_currency_sign_unit($next_invoices->additional_pages_scanning_amount, $currency_short, $currency_sign, $currency_rate, $decimal_separator);
            $shipping_handling = APUtils::view_currency_with_currency_sign_unit($next_invoices->shipping_handing_amount, $currency_short, $currency_sign, $currency_rate, $decimal_separator);
            $storing_items =  APUtils::view_currency_with_currency_sign_unit($next_invoices->storing_amount, $currency_short, $currency_sign, $currency_rate, $decimal_separator);

            $total = $next_invoices->postboxes_amount;
            $total += $next_invoices->envelope_scanning_amount;
            $total += $next_invoices->scanning_amount;
            $total += $next_invoices->additional_items_amount;
            $total += $next_invoices->shipping_handing_amount;
            $total += $next_invoices->storing_amount;
            $total += $next_invoices->additional_pages_scanning_amount;
            #1058 add multi dimension capability for admin
            $current_total = APUtils::view_currency_with_currency_sign_unit($total, $currency_short, $currency_sign, $currency_rate, $decimal_separator);

            $this->template->set("postboxes", $postboxes);
            $this->template->set("envelope_scanning", $envelope_scanning);
            $this->template->set("scanning", $scanning);
            $this->template->set("additional_items", $additional_items);
            $this->template->set("additional_scanning_items", $additional_scanning_items);
            $this->template->set("shipping_handling", $shipping_handling);
            $this->template->set("storing_items", $storing_items);

            $this->template->set("current_total", $current_total);

        }

        if ($customer->invoice_type == '1') {
           $standard_payment_method = "Credit Card";
        } else {
            $standard_payment_method =  "Invoice";
        }
        $this->template->set("standard_payment_method", $standard_payment_method);

        $vat  = APUtils::getVatRateOfCustomer($customer->customer_id);
        $vat_rate = APUtils::number_format(($vat->rate)*100, 2,$decimal_separator).'%';

        $this->template->set("vat", $vat);
        $this->template->set("vat_rate", $vat_rate);

        if ($customer->activated_flag === '1') {
            $customer_activated =  lang('customer.activated');
        }
        else {
            $customer_activated = lang('customer.not_activated');

        }
        $this->template->set("customer_activated", $customer_activated);

        // Gets customer product setting.
        $active_flag = CustomerProductSetting::get_activate_flags($customer_id);

        $this->template->set("next_invoices", $next_invoices_display);
        $this->template->set("customer", $customer);
        $this->template->set("main_postbox", $main_postbox);
        $this->template->set("scan_item", $scan_item);
        $this->template->set("dropbox_status", $dropbox_status);
        $this->template->set("customer_cloud", $customer_cloud);
        $this->template->set("customer_shipping_address", $customer_shipping_address);
        $this->template->set("free_postbox_count", $free_postbox_count);
        $this->template->set("private_postbox_count", $private_postbox_count);
        $this->template->set("business_postbox_count", $business_postbox_count);
        $this->template->set("customer_complete_ship_address", $customer_complete_ship_address);
        $this->template->set("decimal_separator", $decimal_separator);
        $this->template->set("currency_sign", $currency_sign);
        $this->template->set("active_flag", $active_flag);
        $this->template->set("date_format", $date_format);

        // Display the current page
        $this->template->build('admin/view_form');
    }

    /**
     * Internal check login.
     *
     * @param unknown_type $user_name
     * @param unknown_type $password
     */
    public function view_site()
    {
        $customer_id = $this->input->get_post('customer_id');
        // Get user information by email
        $customer = $this->customer_m->get_by_many(array(
            "customer_id" => $customer_id
        ));


        if ($customer) {
            // reset session
            APContext::setSessionValue('get_all_postbox_list', null);
            APContext::setSessionValue(APConstants::SESSION_CUSTOMER_KEY, null);
            APContext::setSessionValue(APConstants::SESSION_PARENT_CUSTOMER_KEY, null);
            APContext::setSessionValue(APConstants::SESSION_CUSTOMER_ADDRESS_KEY, null);
            APContext::setSessionValue(APConstants::GROUP_CUSTOMER_ROLE_KEY, null);
            APContext::setSessionValue(APConstants::SESSION_UPDATE_CALL_HISTORY_SONTEL, null);
            APContext::setSessionValue(APConstants::SESSION_PARENT_CUSTOMER_KEY, null);
            APContext::setSessionValue(APConstants::SESSION_SKIP_CUS_KEY, 0);

            // Store customer information to session
            $this->session->set_userdata(APConstants::SESSION_CUSTOMER_KEY, $customer);
            $this->session->unset_userdata(APConstants::SESSION_SHOW_MOBILE_ADV_FIRST_LOGIN);
            $this->session->set_userdata(APConstants::GROUP_CUSTOMER_ROLE_KEY, APConstants::GROUP_CUSTOMER_PRIMARY);

            // Get current customer dropbox
            $customer_id = $customer->customer_id;
            $customer_setting = $this->customer_cloud_m->get_by_many(array(
                "cloud_id" => APConstants::CLOUD_DROPBOX_CODE,
                "customer_id" => $customer_id
            ));

            $parent_customer_id = $customer->parent_customer_id;
            if(empty($parent_customer_id)){
                $parent_customer_id = $customer_id;
                $this->session->set_userdata(APConstants::SESSION_PARENT_CUSTOMER_KEY, $customer);
            }else{
                $parent_customer = APContext::getCustomerByID($parent_customer_id);
                $this->session->set_userdata(APConstants::SESSION_PARENT_CUSTOMER_KEY, $parent_customer);
            }
            APContext::reloadListUser($parent_customer_id, $customer->customer_id);

            // Get customer address
            $address = $this->customers_address_m->get_by('customer_id', $customer_id);
            // Store customer information to session
            $this->session->set_userdata(APConstants::SESSION_CUSTOMER_ADDRESS_KEY, $address);

            // reset first login
            APContext::setSessionValue(APConstants::SESSION_UPDATE_CALL_HISTORY_SONTEL, 0);

            if (!empty ($customer_setting)) {
                if (!empty ($customer_setting->settings)) {
                    // Decode cloud setting
                    $setting = json_decode($customer_setting->settings, true);
                    $this->session->set_userdata(APConstants::SESSION_CLOUD_CUSTOMER_KEY, $setting);
                }
            }

            CaseUtils::start_verification_case($customer_id);

            $this->session->set_userdata(APConstants::DIRECT_ACCESS_CUSTOMER_KEY, APConstants::ON_FLAG);

            // Redirect to mailbox site
            redirect('mailbox/index');
        }
    }

    /**
     * Create direct charge
     */
    function create_direct_charge()
    {
        ci()->load->library('invoices/invoices_api');

        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('id');

        // Get envelope information
        $customer = $this->customer_m->get($customer_id);

        // fixbug #446
        $invoice_number = invoices_api::generateInvoiceNumber();

        // #472: change vat case.
        $vat_cases = APUtils::getVATListByCustomer($customer_id);
        $this->template->set("vat_cases", $vat_cases);

        $list_locations = $this->location_m->get_all_location($customer_id);

        if ($customer->status == '1') {
            $list_locations[] = ci()->postbox_m->getFirstLocationBy($customer_id);
        }
        #1058 add multi dimension capability for admin
        $date_format = APUtils::get_date_format_in_user_profiles();

        $this->template->set('list_locations', $list_locations);
        $this->template->set('date_format', $date_format);

        // Display the current page
        $this->template->set('customer_id', $customer_id);
        $this->template->set('customer', $customer);
        $this->template->set("invoice_number", $invoice_number);
        $this->template->build('customers/admin/create_direct_charge');
    }

    /**
     * Create direct charge
     */
    function record_external_payment()
    {
        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('id');

        // Get envelope information
        $customer = $this->customer_m->get($customer_id);
        #1058 add multi dimension capability for admin
        $date_format = APUtils::get_date_format_in_user_profiles();

        // Display the current page
        $this->template->set('customer_id', $customer_id);
        $this->template->set('customer', $customer);
        $this->template->set('date_format', $date_format);
        $this->template->build('customers/admin/record_external_payment');
    }

    /**
     * Open this pending activity of pre-payment process
     */
    function list_prepayment_activity() {
        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('id');
        $customer = APContext::getCustomerByID($customer_id);

        // Manual de-activated customer OR  Auto de-activated customer
        $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
        $open_balance_due = $open_balance_data['OpenBalanceDue'];
        $open_balance_this_month = $open_balance_data['OpenBalanceThisMonth'];
        $total_avail_cost = (-1) * ($open_balance_due + $open_balance_this_month);

        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        $currency_rate = APUtils::get_currency_rate_in_user_profiles();

        $this->template->set('currency_short', $currency_short);
        $this->template->set('decimal_separator', $decimal_separator);
        $this->template->set('currency_rate', $currency_rate);

        // Display the current page
        $this->template->set('customer_id', $customer_id);
        $this->template->set('customer', $customer);
        $this->template->set('total_avail_cost', $total_avail_cost);
        $this->template->set('open_balance_due', $open_balance_due);
        $this->template->set('open_balance_this_month', $open_balance_this_month);
        $this->template->build('customers/admin/list_prepayment_activity');
    }

    /**
     * Open this pending activity of pre-payment process
     */
    function get_list_prepayment_activity() {
        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('id');
        $currency = $this->customer_m->get_standard_setting_currency($customer_id);
        $decimal_separator = $this->customer_m->get_standard_setting_decimal_separator($customer_id);
        if (empty($currency)) {
            $currency = $this->currencies_m->get_by(array('currency_short' => 'EUR'));
        }

        // update limit into user_paging.
        $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();

        // Get paging input
        $input_paging = $this->get_paging_input();
        $input_paging ['limit'] = $limit;

        $list_pending_envelops = mailbox_api::getAllPendingPrepaymentItem($customer_id);
        $list_pending_activity = array();
        foreach ($list_pending_envelops as $item) {
            if ($item->envelope_scan_flag === '2') {
                $item->activity_id = 'envelope_scan_activity_'.$item->id;
                $item->activity_name = 'envelope_scan_activity';
                $item->acitivity_cost = CustomerUtils::estimateScanningCost(array($item->id), 'envelope', $customer_id, true);
                $list_pending_activity[] = $item;
            }
            if ($item->item_scan_flag === '2') {
                $item->activity_id = 'item_scan_activity_'.$item->id;
                $item->activity_name = 'item_scan_activity';
                $item->acitivity_cost = CustomerUtils::estimateScanningCost(array($item->id), 'item', $customer_id, true);
                $list_pending_activity[] = $item;
            }
            if ($item->direct_shipping_flag === '2') {
                $item->activity_id = 'direct_shipping_activity_'.$item->id;
                $item->activity_name = 'direct_shipping_activity';
                $acitivity_cost_obj = CustomerUtils::estimateShippingCost(APConstants::SHIPPING_SERVICE_NORMAL, APConstants::SHIPPING_TYPE_DIRECT,
                            array($item->id), $customer_id, true);

                $item->acitivity_cost = $acitivity_cost_obj['cost'];
                $list_pending_activity[] = $item;
            }
            if ($item->collect_shipping_flag === '2') {
                $item->activity_id = 'collect_shipping_activity_'.$item->id;
                $item->activity_name = 'collect_shipping_activity';
                $acitivity_cost_obj = CustomerUtils::estimateShippingCost(APConstants::SHIPPING_SERVICE_NORMAL, APConstants::SHIPPING_TYPE_COLLECT,
                            array($item->id), $customer_id, true);
                 $item->acitivity_cost = $acitivity_cost_obj['cost'];
                $list_pending_activity[] = $item;
            }
        }

        // Process output data
        $total = count($list_pending_activity);

        // Get output response
        $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
        $i = 0;
        foreach ( $list_pending_activity as $row ) {
            $activity_name = lang('pending_'.$item->activity_name);
            $activity_cost = $currency->currency_sign.' '.APUtils::convert_currency($row->acitivity_cost, $currency->currency_rate, 2, $decimal_separator);
            $response->rows[$i]['id'] = $row->activity_id;
            $response->rows[$i]['cell'] = array (
                $row->activity_id,
                $row->from_customer_name,
                $item->activity_name,
                $activity_name,
                $row->acitivity_cost,
                $activity_cost,
                $row->id
            );
            $i ++;
        }
        echo json_encode($response);
        return;
    }

    /**
     * Submit list prepayment activity to complete
     * The list have json format
     * [
     *  {envelope_id: 11, activity_id: 'envelope_scan_activity'},
     *  {envelope_id: 11, activity_id: 'item_scan_activity'}
     * ]
     */
    function submit_list_prepayment_activity() {
        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('id');
        $pending_activity_data = $this->input->get_post('list_pending_activity');
        if (empty($pending_activity_data)) {
            $this->error_output('Please select pending activity.');
            return;
        }
        $list_pending_activity = json_decode($pending_activity_data);
        foreach ($list_pending_activity as $item) {
            $envelope_id = $item->envelope_id;
            $activity_id = $item->activity_id;

            $envelope = $this->envelope_m->get($item->envelope_id);

            // Check envelope activity
            if ($activity_id === APConstants::ENVELOPE_SCAN_ACTIVITY) {
                // Request envelope scan is successful
                // Update envelope_scan_flag = 0 (yellow)
                mailbox_api::requestEnvelopeScan($envelope_id, $customer_id);

            } elseif ($activity_id === APConstants::ITEM_SCAN_ACTIVITY) {
                // Request item scan is successful
                // Update item_scan_flag = 0 (yellow)
                mailbox_api::requestItemScan($envelope_id, $customer_id);

            } elseif ($activity_id === APConstants::DIRECT_SHIPPING_ACTIVITY) {

                $check_flag = EnvelopeUtils::check_customs_flag($customer_id, $envelope->postbox_id, $envelope->id);

                // Check flag
                if ($check_flag) {
                    mailbox_api::regist_envelope_customs($customer_id, $envelope->id, $envelope->postbox_id, APConstants::DIRECT_FORWARDING);
                } else {
                    // Request direct shipping is successfull
                    // Update direct_shipping_flag = 0 (yellow)
                    // And insert activity:REQUEST_TRACKING_NUMBER = '29'
                    // Save address forwarding
                    mailbox_api::requestDirectShipping($envelope_id, $customer_id);
                }
            } elseif ($activity_id === APConstants::COLLECT_SHIPPING_ACTIVITY) {
                $check_flag = EnvelopeUtils::check_customs_flag($customer_id, $envelope->postbox_id, $envelope->id);
                if ($check_flag) {
                    mailbox_api::regist_envelope_customs($customer_id, $envelope->id, $envelope->postbox_id, APConstants::DIRECT_FORWARDING);
                } else {
                    mailbox_api::requestCollectShippingAfterPrepayment($envelope_id, $customer_id);
                }
            }
        }
        $this->success_output('You have been completed pending acitivty.');
        return;
    }

    /**
     * Create direct charge
     */
    function save_external_payment()
    {
        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('id');

        $this->load->library('customers/customers_api');
        $this->load->library('payment/payment_api');
        // Get envelope information
        //$customer = $this->customer_m->get($customer_id);
        $customer = customers_api::getCustomerByID($customer_id);

        if($customer->account_type == APConstants::ENTERPRISE_CUSTOMER && !empty($customer->parent_customer_id)){
            $this->error_output("You can not create payment for user enterprise.");
            return;
        }

        $this->form_validation->set_rules($this->validation_rules04);
        // $this->form_validation->set_message('matches',
        // lang('customer.confirm_password_error'));

        #1058 add multi dimension capability for admin
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $currency_rate = APUtils::get_currency_rate_in_user_profiles();

        if ($this->form_validation->run()) {
            try {
                $insert_data = $this->input->post();
                $insert_data ['tranAmount'] = str_replace(',', '.', APUtils::convert_number_in_currency($this->input->get_post('tranAmount'), $currency_short, $currency_rate));
                $insert_data ['tranDate'] = APUtils::convertDateFormatFrom02($this->input->get_post('tranDate'));

                // Insert data to external_tran_hist
                payment_api::insertExternalTranHist($customer_id, $insert_data ['tranId'], $insert_data ['tranDate'], $insert_data ['tranAmount']);

                // Manual de-activated customer OR  Auto de-activated customer
                $total = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
                $open_balance_due = $total['OpenBalanceDue'];
                $open_balance_this_month = $total['OpenBalanceThisMonth'];
                if ($total['OpenBalanceDue'] < 0.01 ) {
                    if (($customer->activated_flag == 0) && ($customer->status == 0)
                            && (($customer->deactivated_type == APConstants::MANUAL_INACTIVE_TYPE)
                                    || ($customer->deactivated_type == APConstants::AUTO_INACTIVE_TYPE))) {
                        customers_api::updateActiveCustomer($customer_id);
                    }
                }

                // If available cost < 0
                if ($open_balance_due + $open_balance_this_month <= 0.01) {
                    $total_pending = mailbox_api::countAllPendingPrepaymentItem($customer_id);
                    if ($total_pending > 0) {
                        $other_prepayment_cost = CustomerUtils::estimateTotalPrepaymentRequest($customer_id);
                        if ($open_balance_due + $open_balance_this_month + $other_prepayment_cost <= 0.01) {
                            // #1012 - Should do it manual now
                            mailbox_api::completeManualPrepaymentRequest($customer_id);
                        } else {
                            $return_data = array(
                                'pending_activity_flag' => true
                            );
                            $this->error_output('Please select activity to complete.', $return_data);
                            return;
                        }
                    }
                }

                $message = sprintf(lang('customer.record_external_payment_success'), $customer->email);
                $this->success_output($message);
                return;
            } catch (Exception $e) {
                $message = sprintf(lang('customer.record_external_payment_error'), $customer->email);
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
     * Create direct charge
     */
    function record_refund_payment()
    {
        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('id');

        // Get envelope information
        $customer = $this->customer_m->get($customer_id);

        // Display the current page
        $this->template->set('customer_id', $customer_id);
        $this->template->set('customer', $customer);

        // #472: change vat case.
        $vat_cases = APUtils::getVATListByCustomer($customer_id);

        $this->template->set("vat_cases", $vat_cases);

        $list_locations = $this->location_m->get_all_location($customer_id);

        if ($customer->status == '1') {
            $list_locations[] = ci()->postbox_m->getFirstLocationBy($customer_id);
        }

        #1058 add multi dimension capability for admin
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        $date_format = APUtils::get_date_format_in_user_profiles();

        $this->template->set('list_locations', $list_locations);
        $this->template->set('decimal_separator', $decimal_separator);
        $this->template->set('date_format', $date_format);

        $this->template->build('customers/admin/record_refund_payment');
    }

    /**
     * Create direct charge (does not use from item #42 of ticket #446) Will use new method is save_credit_note
     */
    function save_refund_payment()
    {
        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('id');

        // Get envelope information
        $customer = $this->customer_m->get($customer_id);

        $this->form_validation->set_rules($this->validation_rules04);
        // $this->form_validation->set_message('matches',
        // lang('customer.confirm_password_error'));

        #1058 add multi dimension capability for admin
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $currency_rate = APUtils::get_currency_rate_in_user_profiles();

        if ($this->form_validation->run()) {
            try {
                $insert_data = $this->input->post();
                $insert_data ['tranAmount'] = str_replace(',', '.', APUtils::convert_number_in_currency($this->input->get_post('tranAmount'), $currency_short, $currency_rate));
                $insert_data ['tranDate'] = APUtils::convertDateFormatFrom02($this->input->get_post('tranDate'));

                // Insert data to external_tran_hist
                $this->load->model('payment/external_tran_hist_m');
                $external_id = $this->external_tran_hist_m->insert(array(
                    "customer_id" => $customer_id,
                    "tran_id" => $insert_data ['tranId'],
                    "tran_date" => $insert_data ['tranDate'],
                    "tran_amount" => $insert_data ['tranAmount'],

                    // fixbug #446
                    "payment_type" => "1", // credit
                    "created_date" => now(),
                    "status" => "OK"
                ));

                // ffixbug 446
                $invoice_id = 'CN_' . APUtils::getCurrentYearMonthDate() . "_" . $external_id;
                $this->external_tran_hist_m->update_by_many(array(
                    "id" => $external_id
                ), array(
                    "tran_id" => $invoice_id
                ));

                $message = sprintf(lang('customer.record_refund_payment_success'), $customer->email);
                $this->success_output($message);
                return;
            } catch (Exception $e) {
                $message = sprintf(lang('customer.record_refund_payment_error'), $customer->email);
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
     * Check if the customer has no credit card
     */
    function check_customer_has_no_credit_card()
    {
        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('id');
        #1058 add multi dimension capability for admin
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $currency_sign = APUtils::get_currency_sign_in_user_profiles();
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();

        // Check if the customer has no credit card (any valid payment)
        $valid_payment_count = $this->payment_m->count_by_many(array(
            'customer_id' => $customer_id,
            'card_confirm_flag' => APConstants::ON_FLAG
        ));
        if ($valid_payment_count < 1) {
            // Open balance due
            $open_balance = APUtils::getCurrentBalance($customer_id);
            $open_balance_text = APUtils::number_format($open_balance, 2,$decimal_separator);

            // Open current month
            $open_balance_this_month = APUtils::getCurrentBalanceThisMonth($customer_id);
            $open_balance_this_month_text = APUtils::number_format($open_balance_this_month,2,$decimal_separator);

            $errorMsg = lang('customer.has_no_credit_card');

            $this->template->set('open_balance', $open_balance_text);
            $this->template->set('open_balance_this_month', $open_balance_this_month_text);
            $this->template->set('errorMsg', $errorMsg);
            $this->template->set("currency_short", $currency_short);
            $this->template->set("currency_sign", $currency_sign);

            $message = $this->template->build('customers/admin/check_customer_has_no_credit_card', array(), true);
            $this->error_output($message);
            return;
        } else {
            // The customer actually has a credit_card
            $this->success_output('');
            return;
        }
    }

    /**
     * Create direct charge
     */
    function create_direct_charge_without_invoice()
    {
        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('id');

        // Get envelope information
        $customer = $this->customer_m->get($customer_id);

       // #1058 add multi dimension capability for admin
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $currency_sign = APUtils::get_currency_sign_in_user_profiles();
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();

        // Open balance due
        $open_balance = APUtils::getCurrentBalance($customer_id);
        $open_balance_text = APUtils::number_format($open_balance,2,$decimal_separator);

        // Open current month
        $open_balance_this_month = APUtils::getCurrentBalanceThisMonth($customer_id);
        $open_balance_this_month_text = APUtils::number_format($open_balance_this_month, 2, $decimal_separator);

        // Display the current page
        $this->template->set('customer_id', $customer_id);
        $this->template->set('customer', $customer);
        $this->template->set('open_balance', $open_balance_text);
        $this->template->set('open_balance_this_month', $open_balance_this_month_text);
        $this->template->set("currency_short", $currency_short);
        $this->template->set("currency_sign", $currency_sign);

        $this->template->build('customers/admin/create_direct_charge_without_invoice');
    }

    /**
     * Create direct charge
     */
    function save_direct_charge_without_invoice()
    {
        $this->load->library('payment/payone');
        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('id');

        // Get envelope information
        $customer = $this->customer_m->get($customer_id);

        if($customer->account_type == APConstants::ENTERPRISE_CUSTOMER && !empty($customer->parent_customer_id)){
            $this->error_output("You can not create invoice for user enterprise.");
            return;
        }

        $this->form_validation->set_rules($this->validation_rules05);
        if ($this->form_validation->run()) {
            try {
                $amount = str_replace(',', '.', $this->input->get_post('tranAmount'));
                $invoice_id = APUtils::genetateReferenceForOpenBalance($customer_id);

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

                // If open balance greater 0
                $result = $this->payone->authorize($customer_id, $invoice_id, $amount);

                // Move sent email function when receive message OK from payone
                // Reference method payment > authorize_success_callback


                if ($result) {
                    $message = sprintf(lang('customer.save_direct_charge_without_invoice_success'), $customer->email);
                    $this->success_output($message);
                    return;
                } else {
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
     * Save direct charge
     */
    function save_direct_charge()
    {
        $this->load->library('invoices/export');
        $this->load->model('invoices/invoice_detail_manual_m');
        $this->load->library('payment/payone');

        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('customer_id');
        $location_id = $this->input->get_post('location_id');
        $customs_data = $this->input->get_post('customs_data');
        $invoice_date = APUtils::convertDateFormatFrom04($this->input->get_post('invoice_date'));

        $customer = $this->customer_m->get($customer_id);
        if($customer->account_type == APConstants::ENTERPRISE_CUSTOMER && !empty($customer->parent_customer_id)){
            $this->error_output("You can not create invoice for user enterprise.");
            return;
        }

        $invoices_data = json_decode($customs_data);

        if(empty($location_id)){
            // set default location is berlin.
            $location_id = 1;
        }

        // #472: change vatcase
        // $vat = APUtils::getVatFeeByCustomer($customer_id);
        // $vat_case = APUtils::getVatFeeByCustomerType($customer_id);
        $array_check_temp = array();

        #1058 add multi dimension capability for admin
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $currency_rate = APUtils::get_currency_rate_in_user_profiles();

        if ($invoices_data) {
            // Make payment with payone
            $total_amount = 0;
            $total_payment = 0;
            $invoice_code = '';
            $has_item_fail = false;
            foreach ($invoices_data as $invoice) {
                // #472: change vat case.
                $customerVat = APUtils::calcVatFromParamSubmit($customer_id, $invoice->vat_case);
                $vat = $customerVat->rate;
                $vat_case = $customerVat->vat_case_id;

                // insert net total.
                // $total_amount += $invoice->quantity * $invoice->net_price *
                // (1 + $vat);
                $total_amount = $invoice->quantity * APUtils::convert_number_in_currency($invoice->net_price, $currency_short, $currency_rate);
                $total_payment += $invoice->quantity * APUtils::convert_number_in_currency($invoice->net_price, $currency_short, $currency_rate) * (1 + $vat);
                $current_total_amount = 0;

                $key_check_temp = $customer_id . '###' . $invoice_date . '###' . $vat;
                if (!array_key_exists($key_check_temp, $array_check_temp)) {
                    // Insert data to invoice_summary table
                    $invoice_summary_id = $this->invoice_summary_m->insert(array(
                        "customer_id" => $customer_id,
                        "invoice_month" => $invoice_date,
                        "vat" => $vat,
                        "vat_case" => $vat_case,
                        "payment_1st_flag" => APConstants::OFF_FLAG,
                        "payment_1st_amount" => 0,
                        "payment_2st_flag" => APConstants::OFF_FLAG,
                        "payment_2st_amount" => 0,
                        "total_invoice" => 0,
                        "invoice_type" => '2',
                        "update_flag" => 0
                    ));
                    $array_check_temp [$key_check_temp] = $invoice_summary_id;
                    if (empty ($invoice_code)) {
                        $invoice_code = APUtils::generateInvoiceCodeById($invoice_summary_id);
                    }
                } else {
                    $invoice_summary_id = $array_check_temp [$key_check_temp];
                    // Check exist invoices
                    $invoice_summary_check = $this->invoice_summary_m->get_by_many(array(
                        "customer_id" => $customer_id,
                        "id" => $invoice_summary_id
                    ));
                    if (empty ($invoice_summary_check)) {
                        // Insert data to invoice_summary table
                        $invoice_summary_id = $this->invoice_summary_m->insert(array(
                            "customer_id" => $customer_id,
                            "invoice_month" => $invoice_date,
                            "payment_1st_flag" => APConstants::OFF_FLAG,
                            "payment_1st_amount" => 0,
                            "payment_2st_flag" => APConstants::OFF_FLAG,
                            "payment_2st_amount" => 0,
                            "total_invoice" => 0,
                            "invoice_type" => '2',
                            "update_flag" => 0
                        ));
                    } else {
                        $current_total_amount = $invoice_summary_check->total_invoice;
                        $invoice_summary_id = $invoice_summary_check->id;
                    }
                    $array_check_temp [$key_check_temp] = $invoice_summary_id;
                }

                log_message(APConstants::LOG_ERROR, "Customer > save_direct_charge > authorize: customer_id:" . $customer_id . ",GROSS total_amount: " . $total_amount * (1 + $vat));
                $result = $this->payone->authorize($customer_id, $invoice_code, $total_amount * (1 + $vat));
                log_message(APConstants::LOG_ERROR, "Customer > save_direct_charge > authorize: result:" . $result);
                // $result = true;
                if ($result) {
                    // Insert data to invoice_summary table
                    $this->invoice_summary_m->update_by_many(array(
                        "id" => $invoice_summary_id
                    ), array(
                        "customer_id" => $customer_id,
                        "invoice_month" => $invoice_date,
                        "vat" => $vat,
                        "vat_case" => $vat_case,
                        "payment_1st_flag" => APConstants::ON_FLAG,
                        "payment_1st_amount" => $total_amount,
                        "payment_2st_flag" => APConstants::ON_FLAG,
                        "payment_2st_amount" => 0,
                        "total_invoice" => $current_total_amount + $total_amount,
                        "invoice_type" => '2',
                        "invoice_code" => $invoice_code
                    ));

                    // Insert invoice detail
                    // Insert data to invoice_summary_manual
                    $this->invoice_detail_manual_m->insert(array(
                        "customer_id" => $customer_id,
                        "created_date" => now(),
                        "description" => $invoice->description,
                        "quantity" => $invoice->quantity,
                        "net_price" => APUtils::convert_number_in_currency($invoice->net_price,$currency_short, $currency_rate),
                        "vat" => $vat,
                        "vat_case" => $vat_case,
                        "gross_price" => APUtils::convert_number_in_currency($invoice->net_price, $currency_short, $currency_rate) * (1 + $vat),
                        "payment_flag" => APConstants::ON_FLAG,
                        "payment_date" => strtotime($invoice_date),
                        "invoice_date" =>  $invoice_date,
                        "invoice_summary_id" => $invoice_summary_id,
                        "location_id" => $location_id
                    ));
                } else {
                    $this->invoice_summary_m->delete_by_many(array(
                        "id" => $invoice_summary_id
                    ));
                    $has_item_fail = true;
                    $this->error_output(lang('customer.make_manual_payment_fail'));
                    return;
                }
            }

            // update manual total report.
            invoices_api::updateInvoiceSummaryTotalByLocation(substr($invoice_date, 0, 6), $location_id);

            // insert summary by location.
            $this->insertInvoiceSummaryByLocation(array(
                "customer_id" => $customer_id,
                "invoice_month" => $invoice_date,
                "vat" => $vat,
                "vat_case" => $vat_case,
                "total_invoice" => $current_total_amount + $total_amount,
                "invoice_type" => '2',
                "location_id" => $location_id,
                "invoice_code" => $invoice_code
            ));

            // Check exist invoices
            $invoice_summary_check = $this->invoice_summary_m->get_by_many(array(
                "customer_id" => $customer_id,
                "invoice_code" => $invoice_code
            ));

            // Only send email if have at least one item process successfully
            if (!empty ($invoice_summary_check)) {
                // Send email confirm for user
                // Move sent email function when receive message OK from payone
                // Reference method payment > authorize_success_callback


                $customer = $this->customer_m->get($customer_id);

                // Export to PDF and send email
                $invoice_file_path = $this->export->export_invoice($invoice_code, $customer_id);

                // Send email
                $this->send_email_invoices_monthly_report($customer, $invoice_file_path, '1', $invoice_code);

                // Insert data to invoice_summary table
                $this->invoice_summary_m->update_by_many(array(
                    "id" => $invoice_summary_id
                ), array(
                    "send_invoice_flag" => APConstants::ON_FLAG,
                    "send_invoice_date" => now()
                ));
            }
        }
        $this->success_output(lang('customer.make_manual_payment_success'));
    }

    /**
     * List all customer
     */
    public function postboxlist()
    {
        $this->load->library('customers/customers_api');

        // Get input condition
        $enquiry = $this->input->get_post("enquiry");

        $hideDeletedPostbox = $this->input->get_post("hideDeletedPostbox");
        $list_access_location = APUtils::loadListAccessLocation();
        $this->template->set('list_access_location', $list_access_location);
        // If current request is ajax
        if ($this->is_ajax_request()) {

            $input_paging  = $this->get_paging_input();
            //#1250 HOTFIX: the Hamburg panel shows this customer but this cusotmer has already been deleted
            $limit = isset ($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);
            $input_paging ['limit'] = $limit;

            $location_id   = $this->input->get_post("location_id");
            $customers_api = new customers_api;
            $list_postbox  = $customers_api->postboxlist($enquiry, $hideDeletedPostbox, $input_paging, $location_id, 0);

            echo json_encode($list_postbox['web_postbox_list']);

        } else {

            $this->template->set('header_title', lang('header:list_customer_title'))->build('admin/postboxlist');
        }
    }

    /**
     * export postbox list to csv.
     */
    public function export_postbox_csv()
    {
        ini_set('memory_limit', '-1');
	ini_set('max_execution_time', 1200);
        $this->load->library('customers/customers_api');

        // Get input condition
        $enquiry = $this->input->get_post("enquiry");

        $array_condition = array();
        if (!empty ($enquiry)) {
            $array_condition ["(customers.email LIKE '%" . $enquiry . "%'" . " OR (p.name LIKE '%" . $enquiry . "%'" . " OR p.company LIKE '%" . $enquiry . "%'))"] = null;
        }

        $list_access_location = APUtils::loadListAccessLocation();
        $this->template->set('list_access_location', $list_access_location);

        $list_access_location_id = array();
        $location_id = $this->input->get_post("location_id");
        foreach ($list_access_location as $location) {
            $list_access_location_id [] = $location->id;
        }
        if (!empty ($location_id) && array_key_exists($location_id, $list_access_location_id)) {
            $list_access_location_id = array(
                $location_id
            );
        } else {
            if (APContext::isAdminUser()) {
                $list_access_location_id = array();
            } else {
                $list_access_location_id [] = 0;
            }
        }

        // export declaration.
        $export_date = date('dMy');
        $filename = 'postbox_list_' . $export_date . '.csv';
        $headers = array(
            "Customer Code",
            "Postbox ID",
            "Postbox Name",
            "Postbox Company",
            "Postbox Type",
            "Created Date",
            "Customer Status",
            "Email",
            "Received Items",
            "Invoice Name",
            "Invoice Company"
        );

        $export_rows [] = $headers;

        // Call search method
        $query_result = $this->customer_m->get_postbox_paging($array_condition, 0, 0, null, null, $list_access_location_id);

        // Process output data
        $total = $query_result ['total'];
        $datas = $query_result ['data'];

        // Open the output stream
        $fh = fopen('php://output', 'w');
        ob_start();
        fputcsv($fh, $headers);

        foreach ($datas as $row) {
            //$scan_item = $this->cal_scan_items($row);
            $scan_item = customers_api::cal_scan_items($row);
            // $main_postbox_address =
            // $this->get_main_postbox_address($row->customer_id);
            $customer_address = $this->customers_address_m->get_by_many(array(
                'customer_id' => $row->customer_id
            ));
            $number_received_items = $this->envelope_m->count_by_many(array(
                'to_customer_id' => $row->customer_id
            ));
            $city = empty ($customer_address) ? '' : $customer_address->shipment_city;
            $country = empty ($customer_address) ? '' : $customer_address->shipment_country;
            if (is_numeric($country)) {
                $country_entity = $this->countries_m->get($country);
                if ($country_entity) {
                    $country = $country_entity->country_name;
                }
            }
            $user_name = '';
            if (!empty ($row->name)) {
                $user_name = $row->name;
            }
            $company = $row->company;

            $account_type = '';
            if (!empty ($row->account_type)) {
                $account_type = lang('account_type_' . $row->account_type);
            }

            $invoice_code = $row->invoice_code;
            $export_row = array(
                $row->customer_code,
                $row->postbox_code,
                $row->name,
                $row->postbox_company,
                $account_type,
                $row->postbox_created_date,
                customers_api::getCustomerStatus($row),
                $row->email,
                $number_received_items,
                $row->invoicing_address_name,
                $row->invoicing_company
            );

            $export_rows [] = $export_row;
            fputcsv($fh, $export_row);
        }

        // $output = APUtils::arrayToCsv($export_rows);
        // header('Content-Description: File Transfer');
        // header('Content-type: text/csv');
        // header('Cache-Control: must-revalidate');
        // header('Content-Disposition: attachment; filename=' . $filename);
        // print chr(255) . chr(254) . mb_convert_encoding($output, 'UTF-16LE',
        // 'UTF-8');
        // exit();


        $string = ob_get_clean();

        // Output CSV-specific headers
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename);
        header('Content-Transfer-Encoding: binary');

        // Stream the CSV data
        exit ($string);
    }

    /**
     * Create direct invoice
     */
    function create_direct_invoice()
    {
        $this->load->library('invoices/invoices_api');

        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('id');

        // Get envelope information
        $customer = $this->customer_m->get($customer_id);
        $invoice_number = invoices_api::generateInvoiceNumber();

        // #472: change vat case.
        $vat_cases = APUtils::getVATListByCustomer($customer_id);
        $this->template->set("vat_cases", $vat_cases);

        $list_locations = $this->location_m->get_all_location($customer_id);

        if ($customer->status == '1') {
            $list_locations[] = ci()->postbox_m->getFirstLocationBy($customer_id);
        }
        #1058 add multi dimension capability for admin
        $date_format = APUtils::get_date_format_in_user_profiles();
        $currency_short = APUtils::get_currency_short_in_user_profiles();

        $this->template->set('list_locations', $list_locations);
        $this->template->set('date_format', $date_format);
        $this->template->set('currency_short', $currency_short);

        // Display the current page
        $this->template->set('customer_id', $customer_id);
        $this->template->set('customer', $customer);
        $this->template->set("invoice_number", $invoice_number);
        $this->template->build('customers/admin/create_direct_invoice');
    }

    /**
     * Save direct charge
     */
    function save_direct_invoice()
    {
        $this->load->library('invoices/export');
        $this->load->model('invoices/invoice_detail_manual_m');
        $this->load->library('payment/payone');

        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('customer_id');
        $customer = $this->customer_m->get_by_many(array(
            'customer_id' => $customer_id
        ));

        if($customer->account_type == APConstants::ENTERPRISE_CUSTOMER && !empty($customer->parent_customer_id)){
            $this->error_output("You can not create invoice for user enterprise.");
            return;
        }

        $customs_data = $this->input->get_post('customs_data');
        $invoice_date = APUtils::convertDateFormatFrom04($this->input->get_post('invoice_date'));

        $invoices_data = json_decode($customs_data);

        // #472: change vatcase
        // $vat = APUtils::getVatFeeByCustomer($customer_id);
        // $vat_case = APUtils::getVatFeeByCustomerType($customer_id);
        $array_check_temp = array();

        // #568
        $location_id = $this->input->get_post('location_id');
        if(empty($location_id)){
            // set default location is berlin.
            $location_id = 1;
        }

        #1058 add multi dimension capability for admin
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $currency_rate = APUtils::get_currency_rate_in_user_profiles();

        if ($invoices_data) {
            // Make payment with payone
            $total_amount = 0;
            $invoice_code = '';
            foreach ($invoices_data as $invoice) {
                if (empty ($invoice->vat_case)) {
                    $this->error_output('Please press enter to select VAT case.');
                    return;
                }
                // #472: change vat case.
                $customerVat = APUtils::calcVatFromParamSubmit($customer_id, $invoice->vat_case);
                $vat = $customerVat->rate;
                $vat_case = $customerVat->vat_case_id;
                $total_amount = $invoice->quantity * APUtils::convert_number_in_currency($invoice->net_price, $currency_short, $currency_rate);
                $current_total_amount = 0;

                $key_check_temp = $customer_id . '###' . $invoice_date . '###' . $vat;
                if (!array_key_exists($key_check_temp, $array_check_temp)) {

                    // Insert data to invoice_summary table
                    $invoice_summary_id = $this->invoice_summary_m->insert(array(
                        "customer_id" => $customer_id,
                        "invoice_month" => $invoice_date,
                        "payment_1st_flag" => APConstants::OFF_FLAG,
                        "payment_1st_amount" => 0,
                        "payment_2st_flag" => APConstants::OFF_FLAG,
                        "payment_2st_amount" => 0,
                        "total_invoice" => 0,
                        "invoice_type" => '2',
                        "update_flag" => 0
                    ));
                    $array_check_temp [$key_check_temp] = $invoice_summary_id;
                    if (empty ($invoice_code)) {
                        $invoice_code = APUtils::generateInvoiceCodeById($invoice_summary_id);
                    }
                } else {
                    $invoice_summary_id = $array_check_temp [$key_check_temp];
                    // Check exist invoices
                    $invoice_summary_check = $this->invoice_summary_m->get_by_many(array(
                        "customer_id" => $customer_id,
                        "id" => $invoice_summary_id
                    ));
                    if (empty ($invoice_summary_check)) {
                        // Insert data to invoice_summary table
                        $invoice_summary_id = $this->invoice_summary_m->insert(array(
                            "customer_id" => $customer_id,
                            "invoice_month" => $invoice_date,
                            "payment_1st_flag" => APConstants::OFF_FLAG,
                            "payment_1st_amount" => 0,
                            "payment_2st_flag" => APConstants::OFF_FLAG,
                            "payment_2st_amount" => 0,
                            "total_invoice" => 0,
                            "invoice_type" => '2',
                            "update_flag" => 0
                        ));
                    } else {
                        $current_total_amount = $invoice_summary_check->total_invoice;
                        $invoice_summary_id = $invoice_summary_check->id;
                    }
                    $array_check_temp [$key_check_temp] = $invoice_summary_id;
                }

                // Insert data to invoice_summary table
                $this->invoice_summary_m->update_by_many(array(
                    "id" => $invoice_summary_id
                ), array(
                    "customer_id" => $customer_id,
                    "invoice_month" => $invoice_date,
                    "vat" => $vat,
                    "vat_case" => $vat_case,
                    "payment_1st_flag" => APConstants::ON_FLAG,
                    "payment_1st_amount" => $current_total_amount + $total_amount,
                    "payment_2st_flag" => APConstants::ON_FLAG,
                    "payment_2st_amount" => 0,
                    "total_invoice" => $current_total_amount + $total_amount,
                    "invoice_type" => '2',
                    "invoice_code" => $invoice_code
                ));

                // Insert data to invoice_summary_manual
                $this->invoice_detail_manual_m->insert(array(
                    "customer_id" => $customer_id,
                    "created_date" => now(),
                    "description" => $invoice->description,
                    "quantity" => $invoice->quantity,
                    "net_price" => APUtils::convert_number_in_currency($invoice->net_price, $currency_short, $currency_rate),
                    "vat" => $vat,
                    "vat_case" => $vat_case,
                    "gross_price" => APUtils::convert_number_in_currency($invoice->net_price, $currency_short, $currency_rate) * (1 + $vat),
                    "payment_flag" => APConstants::ON_FLAG,
                    "rev_share" => $this->input->post('rev_share', 0),
                    "payment_date" => strtotime($invoice_date),
                    "invoice_date" =>  $invoice_date,
                    "location_id" => $location_id,
                    "invoice_summary_id" => $invoice_summary_id
                ));
            }

            // update manual total report.
            invoices_api::updateInvoiceSummaryTotalByLocation(substr($invoice_date, 0, 6), $location_id);

            // insert summary by location.
            $this->insertInvoiceSummaryByLocation(array(
                "customer_id" => $customer_id,
                "invoice_month" => $invoice_date,
                "vat" => $vat,
                "vat_case" => $vat_case,
                "total_invoice" => $current_total_amount + $total_amount,
                "invoice_type" => '2',
                "rev_share" => $this->input->post('rev_share', 0),
                "invoice_code" => $invoice_code,
                "location_id" => $location_id
            ));

            // Export to PDF and send email
            $invoice_file_path = $this->export->export_invoice($invoice_code, $customer_id);

            // Send email
            $this->send_email_invoices_monthly_report($customer, $invoice_file_path, '1', $invoice_code);

            // Insert data to invoice_summary table
            $this->invoice_summary_m->update_by_many(array(
                "id" => $invoice_summary_id
            ), array(
                "send_invoice_flag" => APConstants::ON_FLAG,
                "send_invoice_date" => now(),
                "update_flag" => 0
            ));
        }
        $this->success_output(lang('customer.make_manual_invoice_success'));
    }

    /**
     * Save credit note
     */
    function save_credit_note()
    {
        $this->load->library('invoices/export');
        $this->load->model('invoices/invoice_detail_manual_m');
        $this->load->library('payment/payone');

        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('customer_id');
        $customer = $this->customer_m->get($customer_id);

        if($customer->account_type == APConstants::ENTERPRISE_CUSTOMER && !empty($customer->parent_customer_id)){
            $this->error_output("You can not create credit note for user enterprise.");
            return;
        }

        $customs_data = $this->input->get_post('customs_data');
        #1058 add multi dimension capability for admin
        $invoice_date = APUtils::convertDateFormatFrom04($this->input->get_post('invoice_date'));

        $invoices_data = json_decode($customs_data);
        $array_check_temp = array();

        // #568
        $location_id = $this->input->get_post('location_id');
        if(empty($location_id)){
            // set default location is berlin.
            $location_id = 1;
        }

        #1058 add multi dimension capability for admin
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $currency_rate = APUtils::get_currency_rate_in_user_profiles();

        if ($invoices_data) {
            // Make payment with payone
            $total_amount = 0;
            $invoice_code = '';
            foreach ($invoices_data as $invoice) {
                // #472: change vat case.
                $vat_case = 0;
                $customerVat = APUtils::calcVatFromParamSubmit($customer_id, $invoice->vat_case);
                $vat = $customerVat->rate;
                $vat_case_id = $customerVat->vat_case_id;

                // insert NET total.
                $total_amount = $invoice->quantity * APUtils::convert_number_in_currency($invoice->net_price, $currency_short, $currency_rate); #1058 add multi dimension capability for admin
                $current_total_amount = 0;

                $key_check_temp = $customer_id . '###' . $invoice_date . '###' . $vat;
                if (!array_key_exists($key_check_temp, $array_check_temp)) {
                    // Insert data to invoice_summary table
                    $invoice_summary_id = $this->invoice_summary_m->insert(array(
                        "customer_id" => $customer_id,
                        "invoice_month" => $invoice_date,
                        "payment_1st_flag" => APConstants::OFF_FLAG,
                        "payment_1st_amount" => 0,
                        "payment_2st_flag" => APConstants::OFF_FLAG,
                        "payment_2st_amount" => 0,
                        "total_invoice" => 0,
                        "invoice_type" => '2',
                        "update_flag" => 0
                    ));

                    $array_check_temp [$key_check_temp] = $invoice_summary_id;

                    if (empty ($invoice_code)) {
                        $invoice_code = APUtils::generateInvoiceCodeById($invoice_summary_id, true);
                    }
                } else {
                    $invoice_summary_id = $array_check_temp [$key_check_temp];
                    // Check exist invoices
                    $invoice_summary_check = $this->invoice_summary_m->get_by_many(array(
                        "customer_id" => $customer_id,
                        "id" => $invoice_summary_id
                    ));
                    if (empty ($invoice_summary_check)) {
                        // Insert data to invoice_summary table
                        $invoice_summary_id = $this->invoice_summary_m->insert(array(
                            "customer_id" => $customer_id,
                            "invoice_month" => $invoice_date,
                            "payment_1st_flag" => APConstants::OFF_FLAG,
                            "payment_1st_amount" => 0,
                            "payment_2st_flag" => APConstants::OFF_FLAG,
                            "payment_2st_amount" => 0,
                            "total_invoice" => 0,
                            "invoice_type" => '2',
                            "update_flag" => 0
                        ));
                    } else {
                        $current_total_amount = $invoice_summary_check->total_invoice;
                        $invoice_summary_id = $invoice_summary_check->id;
                    }
                    $array_check_temp [$key_check_temp] = $invoice_summary_id;
                }

                // Insert data to invoice_summary table
                $this->invoice_summary_m->update_by_many(array(
                    "id" => $invoice_summary_id
                ), array(
                    "customer_id" => $customer_id,
                    "invoice_month" => $invoice_date,
                    "vat" => $vat,
                    "vat_case" => $vat_case,
                    "payment_1st_flag" => APConstants::ON_FLAG,
                    "payment_1st_amount" => $current_total_amount + $total_amount * (-1),
                    "payment_2st_flag" => APConstants::ON_FLAG,
                    "payment_2st_amount" => 0,
                    "total_invoice" => $current_total_amount + $total_amount * (-1),
                    "invoice_type" => '2',
                    "invoice_code" => $invoice_code
                ));

                // Insert data to invoice_summary_manual
                $this->invoice_detail_manual_m->insert(array(
                    "customer_id" => $customer_id,
                    "created_date" => now(),
                    "description" => $invoice->description,
                    "quantity" => $invoice->quantity,
                    "net_price" => (-1) * APUtils::convert_number_in_currency($invoice->net_price, $currency_short, $currency_rate), #1058 add multi dimension capability for admin
                    "vat" => $vat,
                    "rev_share" => $this->input->post('rev_share', 0),
                    "vat_case" => $vat_case,
                    "gross_price" => (-1) * (APUtils::convert_number_in_currency($invoice->net_price, $currency_short, $currency_rate) * (1 + $vat)), #1058 add multi dimension capability for admin
                    "payment_flag" => APConstants::ON_FLAG,
                    "payment_date" => strtotime($invoice_date),
                    "invoice_date" =>  $invoice_date,
                    "invoice_summary_id" => $invoice_summary_id,
                    "location_id" => $location_id
                ));
            }

            // update manual total report.
            invoices_api::updateInvoiceSummaryTotalByLocation(substr($invoice_date, 0, 6), $location_id);

            // insert summary by location.
            $this->insertInvoiceSummaryByLocation(array(
                "customer_id" => $customer_id,
                "invoice_month" => $invoice_date,
                "vat" => $vat,
                "vat_case" => $vat_case_id,
                "rev_share" => $this->input->post('rev_share', 0),
                "total_invoice" => $current_total_amount + $total_amount * (-1),
                "invoice_type" => '2',
                "invoice_code" => $invoice_code,
                "location_id" => $location_id
            ));

            // Export to PDF and send email
            $invoice_file_path = $this->export->export_invoice($invoice_code, $customer_id);

            // Send email
            $this->send_email_invoices_monthly_report($customer, $invoice_file_path, '2', $invoice_code);

            // Insert data to invoice_summary table
            $this->invoice_summary_m->update_by_many(array(
                "id" => $invoice_summary_id
            ), array(
                "send_invoice_flag" => APConstants::ON_FLAG,
                "send_invoice_date" => now(),
                "update_flag" => 0
            ));
        }
        $this->success_output(lang('customer.make_manual_credit_note_success'));
    }

    /**
     * send email invoices monthly report of customer.
     *
     * @param unknown $customer
     * @param unknown $file_export
     * @param $invoice_type (1:
     *            Invoice | 2: Credit Note)
     */
    private function send_email_invoices_monthly_report($customer, $file_export, $invoice_type, $invoice_code)
    {
        APUtils::send_email_invoices_monthly_report($customer, $file_export, $invoice_type, $invoice_code);
    }

    /**
     * List all customer
     */
    public function blacklist()
    {
        // Get input condition
        $enquiry = $this->input->get_post("enquiry");

        $array_condition = array();
        if (!empty ($enquiry)) {
            $array_condition ["(c.customer_code LIKE '%{$enquiry}%' OR c.email LIKE '%" . $enquiry . "%')"] = null;
        }

        $list_access_location = APUtils::loadListAccessLocation();
        $this->template->set('list_access_location', $list_access_location);

        #1058 add multi dimension capability for admin
        $date_format = APUtils::get_date_format_in_user_profiles();

        // If current request is ajax
        if ($this->is_ajax_request()) {

            $this->load->library('invoices/invoices_api');
            // update limit into user_paging.
            $limit = isset ($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();

            // Get paging input
            $input_paging = $this->get_paging_input();
            //echo "<pre>";print_r($input_paging);exit;
            $input_paging ['limit'] = $limit;

            // Call search method
            $query_result = $this->customer_m->get_customer_blacklist_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

            // Process output data
            $total = $query_result ['total'];
            $datas = $query_result ['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
            // #1058 add multi dimension capability for admin
            $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
            $currency_short = APUtils::get_currency_short_in_user_profiles();
            $currency_rate = APUtils::get_currency_rate_in_user_profiles();

            //echo "<pre>";print_r($datas);exit;
            $i = 0;
            $deleted_by = "";
            foreach ($datas as $row) {

                if($row->status == "1" && $row->customer_id == $row->deleted_by){
                    $deleted_by = "Customer";
                }else if($row->status == "1" &&  !empty($row->display_name)){
                    $deleted_by = $row->display_name;
                }
                else if($row->status == "1" && $row->deleted_by == '0'){
                    $deleted_by = "Systems";
                }else {
                    $deleted_by = "";
                }


                $invoiceSummary = invoices_api::getInvoiceSummary($row->customer_id);
                $response->rows [$i] ['id'] = $row->id;
                $response->rows [$i] ['cell'] = array(
                    $row->id,
                    $row->email,
                    $row->customer_id,
                    $row->customer_code,
                    APUtils::viewDateFormat($row->created_date, $date_format),
                    APUtils::viewDateFormat($row->register_date,$date_format),
                    (APUtils::viewDateFormat(($row->created_date - $row->register_date), 'm')),
                    APUtils::view_convert_number_in_currency($invoiceSummary, $currency_short, $currency_rate, $decimal_separator), #1058 add multi dimension capability for admin
                    $deleted_by
                );
                $i++;
            }
            echo json_encode($response);
        } else {
            // Display the current page
            $this->template->set('header_title', lang('header:list_customer_title'))->build('admin/blacklist');
        }
    }

    /**
     * Method for handling different form actions
     */
    public function add_customer_blacklist()
    {
        $this->template->set_layout(FALSE);
        $customer_blacklist = new stdClass ();
        $customer_blacklist->id = '';
        if ($_POST) {
            $this->form_validation->set_rules($this->add_blacklist_validation_rules);

            if ($this->form_validation->run()) {
                $email = $this->input->get_post("email");
                try {
                    // Validate customer information
                    $customer_check = $this->customer_m->get_by_many(array(
                        "email" => $email
                    ));

                    // Check exist
                    $customer_id = 0;
                    if (!empty ($customer_check)) {
                        $customer_id = $customer_check->customer_id;
                    }

                    // Check exist black list
                    $customer_blacklist_check = $this->customer_blacklist_m->get_by_many(array(
                        "email" => $email
                    ));
                    if (!empty ($customer_blacklist_check)) {
                        $message = sprintf(lang('customer_blacklist.exist_blacklist'), $this->input->post('email'));
                        $this->error_output($message);
                        return;
                    }

                    // Dang ky thong tin vao postbox
                    $this->customer_blacklist_m->insert(array(
                        "customer_id" => $customer_id,
                        "email" => $email,
                        "created_date" => now()
                    ));

                    $message = sprintf(lang('customer_blacklist.add_success'), $this->input->post('email'));
                    $this->success_output($message);
                    return;
                } catch (Exception $e) {
                    $message = sprintf(lang('customer_blacklist.add_error'), $this->input->post('email'));
                    $this->error_output($message);
                    return;
                }
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Loop through each validation rule
        foreach ($this->add_blacklist_validation_rules as $rule) {
            $customer_blacklist->{$rule ['field']} = set_value($rule ['field']);
        }

        // Display the current page
        $this->template->set('customer_blacklist', $customer_blacklist)->set('action_type', 'add')->build('admin/add_customer_blacklist');
    }

    /**
     * Delete customer
     */
    public function delete_customer_blacklist()
    {
        $email_blacklist_id = $this->input->get_post("email");

        // Get customer backlist information
        $customer_black_list = $this->customer_blacklist_m->get_by_many(array(
            "email" => $email_blacklist_id
        ));
        // Delete customer black list
        $this->customer_blacklist_m->delete_by_many(array(
            "email" => $email_blacklist_id
        ));
		// Insert table customer_blacklist_hist
        $this->customer_blacklist_hist_m->insert(array(
            "customer_id" => $customer_black_list->customer_id,
            "email" => $customer_black_list->email,
            "created_date" => now()
        ));

        $message = sprintf(lang('customer_blacklist.delete_success'));
        $this->success_output($message);
        return;
    }

    /**
     * change postbox and re-calculate invoice.
     */
    public function change_postbox_private()
    {
        $message = '';
        if ($_POST) {
            $customer_id = $this->input->post('customer_id');
            $postbox_id = $this->input->post('postbox_id');
            $curr_type = $this->input->post('current_type');
            $new_type = $this->input->post('new_type');

            $this->load->library('invoices/Invoices');
            $this->load->model('mailbox/postbox_fee_month_m');

            $postbox = $this->postbox_m->get_by_many(array(
                'postbox_id' => $postbox_id
            ));
            $customer = $this->customer_m->get_by_many(array(
                'customer_id' => $customer_id
            ));

            if ($customer && $postbox && $curr_type != $new_type && $postbox->type == $curr_type) {
                $target_month = APUtils::getCurrentMonthInvoice();
                $target_year = APUtils::getCurrentYearInvoice();

                if ($new_type == APConstants::FREE_TYPE) {
                    // Update account type
                    $this->postbox_m->update_by_many(array(
                        "customer_id" => $customer_id,
                        "postbox_id" => $postbox_id
                    ), array(
                        "type" => $new_type
                    ));
                    APUtils::updateAccountType($customer_id);

                    // Reset viec tinh phi cho account nay
                    $this->postbox_fee_month_m->delete_by_many(array(
                        "postbox_id" => $postbox_id,
                        "year_month" => $target_year . $target_month,
                        "postbox_fee_flag" => APConstants::ON_FLAG
                    ));
                }

                // re-calculate invoice.
                $this->invoices->cal_postbox_invoices($customer);
                $this->invoices->cal_invoice_summary($customer_id, $target_year, $target_month);
                $message = "Change success!";
            }
        }

        $this->template->set('message', $message)->build('admin/change_postbox_private');
    }

    /**
     * Gets list customer deleted with open balance > 0: function nay dung de report cho christian.
     */
    public function get_list_customer_deleted()
    {
        $p = $this->input->get_post('p');
        $customer_list = $this->customer_m->get_many_by_many(array(
            "status" => '1'
        ));

        $result = array();
        if ($p == 1) {
            // hien thi cac customer da deleted ma co envelope hoac postbox chua
            // bi delete.
            foreach ($customer_list as $customer) {
                $postbox_check = $this->postbox_m->get_by_many(array(
                    "deleted <> 1" => null,
                    "completed_delete_flag <> 1" => null,
                    "customer_id" => $customer->customer_id
                ));

                $envelope_check = $this->envelope_m->get_by_many(array(
                    "trash_flag <> 1" => null,
                    "to_customer_id" => $customer->customer_id
                ));

                if ($postbox_check || $envelope_check) {
                    $customer->open_balance = 0;
                    array_push($result, $customer);
                }
            }
        } else {
            // hien thi cac customer bi deleted ma open balance <> 0
            foreach ($customer_list as $customer) {
                $open_balance = APUtils::getActualOpenBalanceDue($customer->customer_id);
                if ($open_balance != 0) {
                    $customer->open_balance = $open_balance;
                    array_push($result, $customer);
                }
            }
        }

        $this->template->set('result', $result);
        $this->template->build('admin/list_customer_deleted');
    }

    public function get_rev_location($location_id = ''){
        $this->template->set_layout(false);

        if($location_id){
            $location = $this->location_m->get_by('id', $location_id);

            if($location){
                $this->success_output("", array("rev_share" => $location->rev_share ? $location->rev_share : 0));
                return;
            }
        }

        $this->success_output("", array("rev_share" => 0));
        return;
    }

    /**
     * save invoice summary by location.
     * @param unknown $data
     */
    private function insertInvoiceSummaryByLocation($data)
    {
        $summary_id = $this->invoice_summary_by_location_m->insert($data);

        return $summary_id;
    }

    public function create_direct_credit_note(){
        if($_POST){
            // load external api
            $this->load->library(array(
                "invoices/export"
            ));

            $customer_id = $this->input->post('customer_id');
            $balance = $this->input->post('open_balance');

            if($customer_id && $balance){
                $customerVat = CustomerUtils::getVatRateOfCustomer($customer_id);
                $vat = $customerVat->rate;
                $vatCase = $customerVat->vat_case_id;

                // create credit note.
                $invoiceSummaryId = $this->invoice_summary_m->insert(array(
                    "vat" => $vat,
                    "vat_case" => $vatCase,
                    "customer_id" => $customer_id,
                    "invoice_month" => APUtils::getCurrentYearMonthDate(),
                    "payment_1st_flag" => APConstants::OFF_FLAG,
                    "payment_1st_amount" => $balance / (1 + $vat),
                    "payment_2st_flag" => APConstants::OFF_FLAG,
                    "payment_2st_amount" => 0,
                    "total_invoice" => (-1) * ($balance) / (1 + $vat),
                    "invoice_type" => '2'
                ));
                $invoiceCode = APUtils::generateInvoiceCodeById($invoiceSummaryId, true);
                $this->invoice_summary_m->update_by_many(array(
                    "id" => $invoiceSummaryId
                        ), array(
                    "invoice_code" => $invoiceCode
                ));

                // Insert credit detail manual and credit note by location
                CustomerUtils::createCreditNoteByLocation($customer_id, $balance,$invoiceSummaryId, $invoiceCode, $customerVat);

                // export credit note.
                ci()->export->export_invoice($invoiceCode, $customer_id);
            }
        }

        $this->template->build('admin/create_direct_credit_note');
    }

    /**
     * #1180 create postbox history page like check item page
     * Check search text in  postbox history (postbox_history)
     */
     public function postboxhistorylist()
    {

        if ($_POST) {
            $this->form_validation->set_rules($this->history_postbox_validation_rules);

            if ($this->form_validation->run()) {
                // Get input condition
                $customer=  $this->input->get_post("customer");

                $message = '';
                if(empty($customer) ){
                    $message .= 'Search text field is required .<br/>';
                    $this->error_output($message);
                    return;
                }else{
                    // If current request is ajax
                    $input_paging = $this->get_paging_input();

                    $response = customers_api::get_list_postbox_history($customer, $input_paging, 0);
                    echo json_encode($response['web_postbox_history_list']);
                    return;
                }
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        $this->template->set('header_title', lang('header:list_postbox_history_title'))->build('admin/list_postbox_history');

    }

    /*
     * Get info detail of customer for screen prepare shipping
     * @param: customer_id
     */
    public function getCustomerInfo(){

        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post("customer_id");
        $customer = $this->customer_m->get_by('customer_id', $customer_id);
        $customer_shipping_address = $this->customers_address_m->get($customer_id);
        if ($customer_shipping_address) {
            if (is_numeric($customer_shipping_address->shipment_country)) {
                $ship_country = $this->countries_m->get($customer_shipping_address->shipment_country);
                if ($ship_country) {
                    $customer_shipping_address->shipment_country = $ship_country->country_name;
                }
            }
            if (is_numeric($customer_shipping_address->invoicing_country)) {
                $invoice_country = $this->countries_m->get($customer_shipping_address->invoicing_country);
                if ($invoice_country) {
                    $customer_shipping_address->invoicing_country = $invoice_country->country_name;
                }
            }
        }
        $open_balance_this_month = APUtils::getCurrentBalanceThisMonth($customer_id);
        $customer_status = customers_api::getCustomerStatus($customer);
        $decimal_separator = $this->customer_m->get_standard_setting_decimal_separator($customer_id);
        $currency = customers_api::getStandardCurrency($customer_id);
        $open_balance = APUtils::getCurrentBalance($customer_id);

        $this->template->set("open_balance", $open_balance);
        $this->template->set('currency', $currency);
        $this->template->set('customer', $customer);
        $this->template->set('decimal_separator', $decimal_separator);
        $this->template->set("open_balance_this_month", $open_balance_this_month);
        $this->template->set('customer_status', $customer_status);
        $this->template->set('customer_shipping_address', $customer_shipping_address);
        $this->template->build('admin/customer_info');
    }

    /*
     * Set Prepayment for shipment
     * @param: customer_id
     */
    public function setPrePaymmentForShipment(){

        if ($this->is_ajax_request()) {
            $this->template->set_layout(FALSE);
            $envelope_id = $this->input->get_post("envelope_id");

            $this->success_output("Envelope ID: " + $envelope_id);
            return;
        }
    }

    /**
     * #1309 create account history page to manage account activities
     */
    public function customerhistorylist()
    {
        if ($_POST) {
            $this->form_validation->set_rules($this->customer_history_validation_rules);

            if ($this->form_validation->run()) {
                // Get input condition
                $customer = $this->input->get_post("customer");
                $message = '';
                if(empty($customer) ){
                    $message .= 'Search text field is required .<br/>';
                    $this->error_output($message);
                    return;
                }else{
                    // If current request is ajax
                    $input_paging = $this->get_paging_input();
                    $response = customers_api::get_list_customer_history($customer, $input_paging, 0);
                    echo json_encode($response);
                    return;
                }
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        $this->template->set('header_title', lang('header:list_customer_history_title'))->build('admin/list_customer_history');
    }
}