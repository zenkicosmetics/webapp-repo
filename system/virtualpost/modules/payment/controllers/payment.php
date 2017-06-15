<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class payment extends AccountSetting_Controller
{
    /**
     * Validation for basic profile data.
     * The rest of the validation is built by streams.
     *
     * @var array
     */
    private $validation_rules = array(
        array(
            'field' => 'account_type',
            'label' => 'lang:account_type',
            'rules' => 'required'
        ),
        array(
            'field' => 'card_type',
            'label' => 'lang:card_type',
            'rules' => 'required|max_length[255]'
        ),
        array(
            'field' => 'card_number',
            'label' => 'lang:card_number',
            'rules' => 'required|max_length[255]'
        ),
        array(
            'field' => 'card_name',
            'label' => 'lang:card_name',
            'rules' => 'required|max_length[255]'
        ),
        array(
            'field' => 'expired_year',
            'label' => 'lang:expired_year',
            'rules' => 'required|max_length[2]'
        ),
        array(
            'field' => 'expired_month',
            'label' => 'lang:expired_month',
            'rules' => 'required|max_length[2]'
        ),
        array(
            'field' => 'cvc',
            'label' => 'lang:cvc',
            'rules' => 'required|max_length[4]'
        )
    );

    /**
     * Validation for basic profile data.
     * The rest of the validation is built by streams.
     *
     * @var array
     */
    private $validation_rules02 = array(
        array(
            'field' => 'pseudocardpan',
            'label' => 'lang:card_number',
            'rules' => 'required'
        ),
        array(
            'field' => 'truncatedcardpan',
            'label' => 'lang:card_number',
            'rules' => 'required'
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

        $this->load->library('Payone_lib');
        $this->load->config('payone');

        // load the theme_example view
        $this->load->model('addresses/customers_address_m');
        $this->load->model('payment/payment_m');
        $this->load->model('customers/customer_m');
        $this->load->model('invoices/invoice_summary_m');
        $this->load->model("invoices/invoice_summary_by_location_m");
        $this->load->model("mailbox/postbox_m");

        $this->load->library('form_validation');
        $this->lang->load('payment');
        $this->lang->load('mailbox/mailbox');

        // load external api
        $this->load->library(array(
            "customers/customers_api",
            "mailbox/mailbox_api",
            'price/price_api',
            'payment/payment_api'
        ));

        // load panation
        $this->load->library("pagination");
    }

    /**
     * Index Page for this controller.
     * Maps to the following URL http://example.com/index.php/welcome - or -
     * http://example.com/index.php/welcome/index - or - Since this controller is set as the default controller in config/routes.php, it's displayed
     * at http://example.com/ So any other public methods not prefixed with an underscore will map to /index.php/welcome/<method_name>
     *
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index()
    {
        $now_time = now();
        $add_payment = $this->input->get_post('add_payment');
        // load payment account
        $this->load_payment_account();

        // get customer.
        $customer_id = APContext::getCustomerCodeLoggedIn();

        // get payment method check
        $is_valid_payment_method= payment_api::isSettingCreditCard($customer_id);
        $this->template->set("is_valid_payment_method", $is_valid_payment_method);
        $this->template->set("customer_id", $customer_id);

        $address = $this->customers_address_m->get_by('customer_id', $customer_id);
        $this->template->set("address", $address);

        $customer = $this->customer_m->get_current_customer_info();
        $this->template->set('customer', $customer);
        $this->template->set('add_payment', $add_payment);
        // load the theme_example view
        $this->template->set('hash', $this->getCreditCardCheckHash());
        $this->template->build('index');
    }

    /**
     * load payment account.
     */
    private function load_payment_account()
    {
        $base_url = base_url() . 'payment';

        $start = $this->input->get_post('start');
        $limit = $this->input->get_post('limit');

        if (empty($start)) {
            $start = 0;
        }
        if (empty($limit)) {
            // $limit = APContext::getPagingSetting(); // $limit = APConstants::DEFAULT_PAGE_ROW;
            $limit = 10000;
        }

        // update limit into user_paging.
        APContext::updatePagingSetting($limit);

        // config panation.
        $config = array();
        $config["base_url"] = $base_url;
        $config["total_rows"] = $this->payment_m->count_payment_account(APContext::getCustomerCodeLoggedIn());
        $config["per_page"] = $limit;
        $config["uri_segment"] = APConstants::PANATION_URI_SEGMET;
        $choice = $config["total_rows"] / $config["per_page"];

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(APConstants::PANATION_URI_SEGMET)) ? $this->uri->segment(APConstants::PANATION_URI_SEGMET) : 0;

        // gets customer login id
        $customer_id = APContext::getCustomerCodeLoggedIn();

        $all_accounts = $this->payment_m->get_payment_account($customer_id, $start, $limit);

        // fixbug #440; Gets latest invoice menual
        $invoice = $this->invoice_summary_m->get_by_many(
            array(
                "invoice_type" => "2", // manual
                "customer_id" => $customer_id
            ), '', false, array(
            "id" => "DESC"
        ));

        $this->template->set("all_accounts", $all_accounts);
        $this->template->set('page_link', $this->pagination->create_links());
        $this->template->set('start', $start);
        $this->template->set('limit', $limit);
        $this->template->set('invoice', $invoice);
    }

    /**
     * Add new payment method.
     */
    public function add_invoice_method()
    {
        $payment_method = new stdClass();
        $this->template->set_layout(FALSE);

        // Get current customer login
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $customer = $this->customer_m->get_by_many(array(
            "customer_id" => APContext::getCustomerCodeLoggedIn()
        ));

        // Check customer information
        if (empty($customer)) {
            $message = lang('add_payment_fail');
            echo json_encode(array(
                'status' => false,
                'message' => $message
            ));
            return;
        }

        // Check invoice code
        $invoice_code = $this->input->post('invoice_code');
        // Check invoice code
        if (empty($invoice_code)) {
            $message = lang('invoice_code_required');
            echo json_encode(array(
                'status' => false,
                'message' => $message
            ));
            return;
        }

        // Check valid invoice code
        if (empty($customer->invoice_code)) {
            $message = lang('invoice_code_notexist');
            echo json_encode(array(
                'status' => false,
                'message' => $message
            ));
            return;
        }

        // Check valid invoice code
        if ($customer->invoice_code != $invoice_code) {
            $message = lang('invoice_code_invalid');
            echo json_encode(array(
                'status' => false,
                'message' => $message
            ));
            return;
        }

        // Update payment flag
        $this->customer_m->update_by_many(
            array(
                "customer_id" => $customer_id,
                "invoice_code" => $invoice_code
            ),
            array(
                "invoice_type" => '2',
                "payment_detail_flag" => APConstants::ON_FLAG,
                "request_confirm_flag" => APConstants::ON_FLAG,
                "request_confirm_date" => now(),
                "last_updated_date" => now()
            ));

        // update: convert registration process flag to customer_product_setting.
        CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'payment_detail_flag', APConstants::ON_FLAG);

        // Active this customer if open balance due <= 0
        $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
        $open_balance_due = $open_balance_data['OpenBalanceDue'];

        // Update customer information
        if ($open_balance_due <= 0) {
            customers_api::activateCustomerWhenUpdatePaymentMethod($customer_id);
        }

        $message = lang('add_invoice_success');
        $this->success_output($message);
        return;
    }

    /**
     * Add new payment method.
     */
    public function add_deposit_invoice_method()
    {
        $payment_method = new stdClass();
        $this->template->set_layout(FALSE);

        // Get current customer login
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $customer = $this->customer_m->get_by_many(array(
            "customer_id" => APContext::getCustomerCodeLoggedIn()
        ));

        // Check customer information
        if (empty($customer)) {
            $message = lang('add_payment_fail');
            echo json_encode(array(
                'status' => false,
                'message' => $message
            ));
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

        //#1309: Insert customer history
        $history = [
            'customer_id' => $customer_id,
            'action_type' => APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_PAYMENT_METHOD,
            'current_data' => APConstants::CUSTOMER_HISTORY_PAYMENT_METHOD_INVOICE,
            'created_by_id' => APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER,
        ];

        // update: convert registration process flag to customer_product_setting.
        CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'payment_detail_flag', APConstants::ON_FLAG);
        customers_api::insertCustomerHistory([$history]);
        $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
        $open_balance = $open_balance_data['OpenBalanceDue'];
        if ($open_balance <= 0.1) {
            // Update customer information
            customers_api::activateCustomerWhenUpdatePaymentMethod($customer_id);
        }

        $message = lang('add_invoice_success');
        $this->success_output($message);
        return;
    }

    /**
     * Add new payment method.
     * (Paypal)
     */
    public function add_paypal_method()
    {
        $payment_method = new stdClass();
        $this->template->set_layout(FALSE);

        // Get current customer login
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $customer = $this->customer_m->get_by_many(array(
            "customer_id" => APContext::getCustomerCodeLoggedIn()
        ));

        // Check customer information
        if (empty($customer)) {
            $message = lang('add_payment_fail');
            echo json_encode(array(
                'status' => false,
                'message' => $message
            ));
            return;
        }

        // Check invoice code
        $paypal_account = $this->input->post('paypal_account');
        // Check invoice code
        if (empty($paypal_account)) {
            $message = lang('paypal_account_required');
            echo json_encode(array(
                'status' => false,
                'message' => $message
            ));
            return;
        }

        $this->payment_m->update_by_many(array(
            "customer_id" => APContext::getCustomerCodeLoggedIn()
        ), array(
            "primary_card" => APConstants::OFF_FLAG
        ));

        // Insert payment information
        $this->payment_m->insert(
            array(
                "account_type" => APConstants::PAYMENT_PAYPAL_ACCOUNT,
                "card_type" => '',
                "card_number" => $paypal_account,
                "card_name" => '',
                "cvc" => '',
                "expired_year" => '',
                "expired_month" => '',
                "customer_id" => APContext::getCustomerCodeLoggedIn(),
                "card_confirm_flag" => APConstants::ON_FLAG,
                "pseudocardpan" => '',
                "callback_tran_id" => '',
                "primary_card" => TRUE
            ));

        // Update data to customer
        $this->customer_m->update_by_many(array(
            "customer_id" => $customer_id
        ), array(
            "payment_detail_flag" => APConstants::ON_FLAG
        ));

        // update: convert registration process flag to customer_product_setting.
        CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'payment_detail_flag', APConstants::ON_FLAG);

        // Update customer information
        $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
        $open_balance = $open_balance_data['OpenBalanceDue'];
        if ($open_balance <= 0.1) {
            // Update customer information
            customers_api::activateCustomerWhenUpdatePaymentMethod($customer_id);
        }

        $message = lang('add_paypal_success');
        $this->success_output($message);
        return;
    }

    /**
     * Add new payment method.
     */
    public function add()
    {
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $payment_method = new stdClass();
        $this->template->set_layout(FALSE);

        if ($_POST) {
            $this->form_validation->set_rules($this->validation_rules02);

            $account_type = $this->input->post('account_type');
            $card_type = $this->input->post('card_type');
            $card_number = $this->input->post('truncatedcardpan');
            $card_name = $this->input->post('card_name');
            //$cvc = $this->input->post('cvc');
            $expired_year = $this->input->post('expired_year');
            $expired_month = $this->input->post('expired_month');

            if ($this->form_validation->run()) {
                $cvc = '';
                $pseudocardpan = $this->input->post('pseudocardpan');

                $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;
                // add payment method.
                $result = payment_api::addPaymentMethod($customer_id, $account_type, $card_type, $card_number, $card_name, $cvc, $expired_year, $expired_month, $pseudocardpan, $created_by_id);

                if(empty($result)){
                    $message = lang('add_payment_success');
                    $this->success_output($message);
                    return;
                }else{
                    echo json_encode($result);
                    return;
                }
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Loop through each validation rule
        foreach ($this->validation_rules as $rule) {
            $payment_method->{$rule['field']} = set_value($rule['field']);
        }

        // Display the current page
        $this->template->set('payment_method', $payment_method)
            ->set('action_type', 'add')
            ->build('form');
    }

    /**
     * Get temp hash
     */
    private function getCreditCardCheckHash()
    {
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $hash = '';
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

        $service = $builder->buildServiceClientApiGenerateHash();
        $request = new Payone_ClientApi_Request_CreditCardCheck();
        $request->setPortalid($portal_id);
        $request->setMid($merchant_id);
        $request->setAid($sub_account_id);
        $request->setMode($mode);
        $request->setStorecarddata("yes");
        $request->setResponsetype("JSON");
        $request->setRequest("creditcardcheck");
        $request->setEncoding($encoding);
        $hash = $service->generate($request, $portal_key);

        return $hash;
    }

    /**
     * Request delete payment method (physical delete).
     */
    public function delete()
    {
        $this->template->set_layout(FALSE);
        $id = $this->input->get_post('id');

        // Get detail payment information
        $payments = $this->payment_m->get_many_by_many(array(
            'customer_id' => APContext::getCustomerCodeLoggedIn()
        ));

        $customer_id = APContext::getCustomerCodeLoggedIn();

        //if (count($payments) == 1 && $customer->activated_flag == '1' && $customer->invoice_type != 2 && !empty($customer->invoice_code)) {
        //    $message = lang('delete_payment_fail02');
        //    $this->error_output($message);
        //    return;
        //}

        // Get detail payment information
        $payment = $this->payment_m->get_by_many( array(
            'payment_id' => $id,
            'customer_id' => $customer_id
        ));

        // Delete payment and set new primary active credit card
        if (!empty($payment)) {
            // Call method to delete
            $this->payment_m->delete_by_many( array(
                'payment_id' => $id,
                'customer_id' => $customer_id
            ));

            //#1309: Insert customer history
            $history_list['remove_card'] = [
                'customer_id' => $customer_id,
                'action_type' => APConstants::CUSTOMER_HISTORY_ACTIVITY_REMOVE_CREDITCARD,
                'current_data' => json_encode($payment),
                'created_by_id' => APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER
            ];

            // Gets valid payment card
            $valid_cards = $this->payment_m->get_payment_account($customer_id, 0, 10);
            if(!$valid_cards){
                $invoice_code = substr(md5($customer_id), 0, 6) . APUtils::generateRandom(4);

                // Update new invoice_code to database
                $this->customer_m->update_by_many(array(
                    "customer_id" => $customer_id
                ), array(
                    "invoice_code" => $invoice_code,
                    "invoice_type" => "2",
                ));

                //#1309: Insert customer history
                $change_paymentmethod_history = $history_list['remove_card'];
                $change_paymentmethod_history['action_type'] = APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_PAYMENT_METHOD;
                $change_paymentmethod_history['current_data'] = APConstants::CUSTOMER_HISTORY_PAYMENT_METHOD_INVOICE;
                $history_list['change_paymentmethod'] = $change_paymentmethod_history;

                $openbalance = APUtils::getAdjustOpenBalanceDue($customer_id);
                if($openbalance['ActualOpenBalanceDue'] + $openbalance['OpenBalanceThisMonth'] > 0 ){
                    $this->customer_m->update_by_many(array(
                        "customer_id" => $customer_id
                    ), array(
                            "activated_flag" => APConstants::OFF_FLAG,
                            "deactivated_type" => APConstants::AUTO_INACTIVE_TYPE,
                            "deactivated_date" => now(),
                            "payment_detail_flag" => APConstants::OFF_FLAG,
                            "last_updated_date" => now()
                    ) );
                    // update: convert registration process flag to customer_product_setting.
                    CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'payment_detail_flag', APConstants::OFF_FLAG);

                    $this->success_output(lang('delete_payment_method_success_notification'));
                    return;
                }
            }else{
                // Set new primary credit card
                if ($payment->primary_card == '1') {
                    $payments = $this->payment_m->get_many_by_many( array(
                        'customer_id' => $customer_id,
                        "card_confirm_flag" => APConstants::ON_FLAG
                    ));
                    if (count($payments) > 0) {
                        $this->payment_m->update_by_many(array(
                            "customer_id" => $customer_id,
                            'payment_id' => $payments[0]->payment_id
                        ), array(
                            "primary_card" => APConstants::ON_FLAG
                        ));

                        //#1309: Insert customer history
                        $change_primarycard_history = $history_list['remove_card'];
                        $change_primarycard_history['action_type'] = APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_PRIMARY_CREDITCARD;
                        $change_primarycard_history['current_data'] = json_encode($payments[0]);
                        $history_list['change_primarycard'] = $change_primarycard_history;
                    }
                }
            }
        }
        customers_api::insertCustomerHistory($history_list);
        $this->success_output('');
    }

    public function check_valid_card(){
        $this->template->set_layout(FALSE);
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $valid_cards = $this->payment_m->get_payment_account($customer_id, 0, 10);

        if(empty($valid_cards) || ($valid_cards && count($valid_cards) == 1)){
            $this->error_output(lang('confirmation_delete_payment_method'));
        }else{
            $this->success_output('Are you sure you want to delete?');
        }
        return;
    }

    /**
     * Set this card as primary card
     */
    public function set_primarycard()
    {
        $this->template->set_layout(FALSE);
        $payment_id = $this->input->get_post('id');
        $customer_id = APContext::getCustomerCodeLoggedIn();

        payment_api::set_primary_card($customer_id, $payment_id);

        $this->success_output('');
    }

    /**
     * Make default payment request.
     *
     * @param unknown_type $card_number
     * @param unknown_type $card_type
     */
    public function make_default_payment($pseudocardpan, $callback_tran_id, $customer_name = '')
    {
        return payment_api::make_default_payment($pseudocardpan, $callback_tran_id, $customer_name);
    }

    /**
     * Success callback
     */
    public function authorize_success_callback()
    {
        $this->load->model('report/payone_transaction_hist_m');
        $this->load->model('payment/payment_job_hist_m');
        $this->load->model('payment/payment_job_hist_test_m');
        $this->load->model('payment/payone_transaction_hist_test_m');
        $this->load->model('invoices/invoice_summary_m');
        $this->load->model('customers/customer_m');
        $this->load->model('payment/payment_m');
        $this->load->model('payment/payment_tran_hist_m');

        $this->load->library('customers/customers_api');
        $this->load->library('mailbox/mailbox_api');

        $this->template->set_layout(FALSE);

        // Insert data to payone_transaction_hist
        $message = json_encode($this->input->post());
        log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'authorize_success_callback');

        $reference = $this->input->get_post('reference');
        $portal_key = md5($this->config->item('payone.portal-key'));

        $bootstrap = new Payone_Bootstrap();
        $bootstrap->init();
        $builder = new Payone_Builder();
        $validIps = array(
            "213.178.72.196",
            "213.178.72.197",
            "217.70.200.0",
            "217.70.200.1",
            "217.70.200.2",
            "217.70.200.3",
            "217.70.200.4",
            "217.70.200.5",
            "217.70.200.6",
            "217.70.200.7",
            "217.70.200.8",
            "217.70.200.9",
            "217.70.200.10",
            "217.70.200.11",
            "217.70.200.12",
            "217.70.200.13",
            "217.70.200.14",
            "217.70.200.15",
            "217.70.200.16",
            "217.70.200.17",
            "217.70.200.18",
            "217.70.200.19",
            "217.70.200.20",
            "217.70.200.21",
            "217.70.200.22",
            "217.70.200.23",
            "217.70.200.24",

            "185.60.20.0",
            "185.60.20.1",
            "185.60.20.2",
            "185.60.20.3",
            "185.60.20.4",
            "185.60.20.5",
            "185.60.20.6",
            "185.60.20.7",
            "185.60.20.8",
            "185.60.20.9",
            "185.60.20.10",
            "185.60.20.11",
            "185.60.20.12",
            "185.60.20.13",
            "185.60.20.14",
            "185.60.20.15",
            "185.60.20.16",
            "185.60.20.17",
            "185.60.20.18",
            "185.60.20.19",
            "185.60.20.20",
            "185.60.20.21",
            "185.60.20.22",
            "185.60.20.23",
            "185.60.20.24"
        );
        try {
            $service = $builder->buildServiceTransactionStatusHandleRequest($portal_key, $validIps);
            // Easy Way, Service Checks $_POST for data
            // $response = $service->handleByPost();

            $txaction = $this->input->get_post('txaction');
            $mode = $this->input->get_post('mode');
            if (!empty($txaction)) {
                $payment_tran_hist = $this->payment_tran_hist_m->get_by_many(array(
                    "reference" => $reference
                ));
                $format_invoice_id = '';
                if (!empty($payment_tran_hist)) {
                    $format_invoice_id = $payment_tran_hist->invoice_id;
                }

                $customer_id = '';
                $invoice_id = '';
                $arr_reference = explode("_", $format_invoice_id);
                if (count($arr_reference) <= 3) {
                    log_message(APConstants::LOG_ERROR, '>>>>>>>>>>>>>>>>authorize_success_callback:>>>Reference format is not correct. Format Invoice ID:' . $format_invoice_id . '.Reference: ' . $reference);
                    $invoice_check = $this->invoice_summary_m->get_by_many(array(
                        "invoice_code" => $format_invoice_id
                    ));
                    if (!empty($invoice_check)) {
                        $invoice_id = $invoice_check->id;
                        $customer_id = $invoice_check->customer_id;
                    } else {
                        echo "Tsok";
                        return;
                    }
                } else {
                    $invoice_id = $arr_reference[1];
                    $customer_id = $arr_reference[2];
                }

                // Check exist customer
                $customer = $this->customer_m->get_by_many(array(
                    "customer_id" => $customer_id
                ));
                if (empty($customer)) {
                    log_message(APConstants::LOG_ERROR,
                        '>>>>>>>>>>>>>>>>authorize_success_callback:>>>Customer ID:' . $customer_id . ' does not exist');
                    return;
                }

                // Check exist invoice summary
                if ($invoice_id != 'SUM') {
                    $invoice = $this->invoice_summary_m->get_by_many(array(
                        "id" => $invoice_id
                    ));
                    if (empty($invoice)) {
                        log_message(APConstants::LOG_ERROR,
                            '>>>>>>>>>>>>>>>>authorize_success_callback:>>>Invoice ID:' . $invoice_id . ' does not exist');
                        return;
                    }
                } // Truong hop $invoice_id = 'SUM' nghia la charge tu open balance
                else {
                    $invoice_id = '';
                }

                // Using for LIVE system (for actual charge)
                if (!empty($mode) && strtolower($mode) == 'live') {

                    // Get payone transaction hisotry
                    $payone_transaction_hist = $this->payone_transaction_hist_m->get_by_many(
                        array(
                            "reference" => $reference,
                            "txaction" => strtolower($txaction)
                        ));

                    $price =  floatval($this->input->get_post('price', 0));
                    $amount = $price;
                    if (!empty($txaction) &&
                        (strtoupper($txaction) == 'REFUND' || strtoupper($txaction) == 'DEBIT')) {
                        $receivable = floatval($this->input->get_post('receivable', 0));
                        $amount = $price - $receivable;
                    }

                    // If not exist the insert new
                    if (!$payone_transaction_hist) {
                        // Insert data to table
                        $this->payone_transaction_hist_m->insert(
                            array(
                                "aid" => $this->input->get_post('aid'),
                                "txid" => $this->input->get_post('txid'),
                                "reference" => $this->input->get_post('reference'),
                                "userid" => $this->input->get_post('userid'),
                                "customerid" => $this->input->get_post('userid'),
                                "create_time" => $this->input->get_post('txtime'),
                                "booking_date" => $this->input->get_post('txtime'),
                                "document_date" => $this->input->get_post('txtime'),
                                "document_reference" => $this->input->get_post('document_reference'),
                                "event" => $this->input->get_post('event'),
                                "param" => $this->input->get_post('param'),
                                "clearingtype" => $this->input->get_post('clearingtype'),
                                "amount" => $amount,
                                "currency" => $this->input->get_post('currency'),
                                "customer_id" => $customer_id,
                                "invoice_id" => $invoice_id,
                                'txaction' => $txaction,
                                "last_update_date" => now()
                            ));
                    } else {

                        // Update status
                        $this->payone_transaction_hist_m->update_by_many(
                            array(
                                "reference" => $reference,
                                "txaction" => strtolower($txaction)
                            ),
                            array(
                                'txaction' => $txaction,
                                "amount" => $amount,
                                "create_time" => $this->input->get_post('txtime'),
                                "booking_date" => $this->input->get_post('txtime'),
                                "document_date" => $this->input->get_post('txtime'),
                                "last_update_date" => now()
                            ));
                    }

                    // Check if $txaction = paid and open balance is zero
                    // Update all invoice summary data
                    if (!empty($txaction) && strtoupper($txaction) == 'PAID') {
                        $open_balance = APUtils::getCurrentBalance($customer_id);
                        $target_month = APUtils::getCurrentMonthInvoice();
                        $target_year = APUtils::getCurrentYearInvoice();
                        $current_invoice_month = $target_year . $target_month;

                        // Update data
                        log_audit_message(APConstants::LOG_INFOR, '>>>>>>>>>>>>>>>>authorize_success_callback:>>>Customer ID:' . $customer_id . ' has open balance due:' . $open_balance, FALSE, 'authorize_success_callback');
                        if ($open_balance <= 0.1) {
                            // Update 2 st payment flag
                            $this->invoice_summary_m->update_by_many(
                                array(
                                    'customer_id' => $customer_id,
                                    "(invoice_type = '1' OR invoice_type IS NULL)" => null,
                                    "invoice_month < " => $current_invoice_month
                                ),
                                array(
                                    'invoice_flag' => '1',
                                    'payment_2st_flag' => APConstants::ON_FLAG,
                                    'payment_1st_flag' => APConstants::ON_FLAG,
                                    "update_flag" => false
                                ));

                            // Only reactivate if deactivated_type = auto
                            customers_api::reactivateCustomerWhenPaymentSuccess($customer_id);
                            log_audit_message(APConstants::LOG_INFOR, '>>>>>>>>>>>>>>>>authorize_success_callback:>>>Customer ID:' . $customer_id . ' has been reactivated', FALSE, 'authorize_success_callback');

                            // #1012 Prepayment request
                            mailbox_api::completeManualPrepaymentRequest($customer_id);
                        }

                        // Update card_charge_flag to PAID
                        if (!empty($payment_tran_hist)) {
                            $this->payment_m->update_by_many(array(
                                "payment_id" => $payment_tran_hist->payment_id,
                                "customer_id" => $customer_id
                            ),
                                array(
                                    "card_charge_flag" => APConstants::CARD_CHARGE_OK,
                                )
                            );

                        }

                        // Send email confirm for user
                        $customer_address = $this->customers_address_m->get_by_many(
                            array(
                                "customer_id" => $customer_id
                            ));

                        $invoice_name = "";
                        if (!empty($customer_address)) {
                            $invoice_name = $customer_address->invoicing_address_name;
                            if (empty($invoice_name)) {
                                $invoice_name = $customer_address->invoicing_company;
                            }
                        }

                        // Only send email for first time
                        if (empty($payone_transaction_hist)) {
                            $customer = $this->customer_m->get($customer_id);
                            $data = array(
                                "slug" => APConstants::admin_make_payment_invoices_success,
                                "to_email" => $customer->email,
                                // Replace content
                                "full_name" => $customer->email,
                                "invoice_name" => $invoice_name,
                                "total_amount" => APUtils::number_format($this->input->get_post('price'))
                            );
                            // Send email
                            MailUtils::sendEmailByTemplate($data);
                        }
                    }

                    // Update payment_status of payment job hist
                    if (!empty($txaction)) {
                        // Update payment job hist
                        $this->payment_job_hist_m->update_by_many(
                            array(
                                "customer_id" => $customer_id,
                                "reference" => $this->input->get_post('reference')
                            ), array(
                            "payment_status" => $txaction
                        ));
                    }

                    // Check if customer was deleted and open balance > 0
                    // Make credit note to adjust open balance
                    if (!empty($txaction) && strtoupper($txaction) == 'REFUND'
                        && $customer->status == APConstants::ON_FLAG && $open_balance > 0.01) {
                        CustomerUtils::createCreditNoteByCustomer($customer_id, $open_balance);
                    }

                    // #543: BUG: last payment method is not always set as standard
                    // Update invoice type (make CC is standard payment method)
                    $this->customer_m->update_by_many(array(
                        'customer_id' => $customer_id
                    ), array(
                        'invoice_type' => '1',
                        'invoice_code' => ''
                    ));
                } // Using for TEST/DEV system (for actual charge)
                else if (!empty($mode) && strtolower($mode) == 'test') {
                    // Get payone transaction hisotry
                    $payone_transaction_hist = $this->payone_transaction_hist_test_m->get_by_many(
                        array(
                            "reference" => $reference
                        ));

                    // If not exist the insert new
                    if (!$payone_transaction_hist) {
                        // Insert data to table
                        $this->payone_transaction_hist_test_m->insert(
                            array(
                                "aid" => $this->input->get_post('aid'),
                                "txid" => $this->input->get_post('txid'),
                                "reference" => $this->input->get_post('reference'),
                                "userid" => $this->input->get_post('userid'),
                                "customerid" => $this->input->get_post('userid'),
                                "create_time" => $this->input->get_post('txtime'),
                                "booking_date" => $this->input->get_post('txtime'),
                                "document_date" => $this->input->get_post('txtime'),
                                "document_reference" => $this->input->get_post('document_reference'),
                                "event" => $this->input->get_post('event'),
                                "param" => $this->input->get_post('param'),
                                "clearingtype" => $this->input->get_post('clearingtype'),
                                "amount" => $this->input->get_post('price'),
                                "currency" => $this->input->get_post('currency'),
                                "customer_id" => $customer_id,
                                "invoice_id" => $invoice_id,
                                'txaction' => $txaction,
                                "last_update_date" => now()
                            ));
                    } else {
                        // Update status
                        $this->payone_transaction_hist_test_m->update_by_many(
                            array(
                                "reference" => $reference
                            ),
                            array(
                                'txaction' => $txaction,
                                "create_time" => $this->input->get_post('txtime'),
                                "booking_date" => $this->input->get_post('txtime'),
                                "document_date" => $this->input->get_post('txtime'),
                                "last_update_date" => now()
                            ));
                    }

                    // Update payment_status of payment job hist
                    if (!empty($txaction)) {
                        // Update payment job hist
                        $this->payment_job_hist_test_m->update_by_many(
                            array(
                                "customer_id" => $customer_id,
                                "reference" => $this->input->get_post('reference')
                            ), array(
                            "payment_status" => $txaction
                        ));
                    }
                }
            }
            echo "Tsok";
            return;
        } catch (Exception $e) {
            log_message(APConstants::LOG_ERROR, $e);
        }
    }

    /**
     * Success callback
     */
    public function payone_authorize_error_callback()
    {
        $this->template->set_layout(FALSE);
        // Insert data to payone_transaction_hist
        $message = json_encode($this->input->post());
        log_audit_message(APConstants::LOG_ERROR, '>>>>>>>>>>>>>>>>payone_authorize_error_callback:>>>' . $message);
        $this->template->build('authorize_error_callback');
    }

    /**
     * Success callback
     */
    public function payone_authorize_success_callback()
    {
        $this->template->set_layout(FALSE);
        // Insert data to payone_transaction_hist
        $message = json_encode($this->input->post());
        log_audit_message(APConstants::LOG_INFOR, '>>>>>>>>>>>>>>>>payone_authorize_success_callback:>>>' . $message);
        $this->template->build('authorize_success_callback');
    }

    /**
     * This method will call when you make preauthorized with 1EUR for all default payment request to payone.
     */
    public function success_callback()
    {
        $mode = APContext::getFullBasePath();
        $this->template->set_layout(FALSE);
        $callback_tran_id = $this->input->get_post('callback_tran_id');

        // Get customer information
        $payment = $this->payment_m->get_by_many(array(
            "callback_tran_id" => $callback_tran_id
        ));
        if (empty($payment)) {
            return;
        }
        $customer_id = $payment->customer_id;
        // Update credit card status
        $this->payment_m->update_by_many(array(
            "callback_tran_id" => $callback_tran_id
        ), array(
            "card_confirm_flag" => APConstants::ON_FLAG,
            "secure_3d_flag" => APConstants::ON_FLAG
        ));

        // Set primary card
        $payments = $this->payment_m->get_many_by_many(
            array(
                'customer_id' => $customer_id,
                "primary_card" => APConstants::ON_FLAG,
                "card_confirm_flag" => APConstants::ON_FLAG
            ));

        // Always to set last CC is primary card
        $this->payment_m->update_by_many(array(
            "customer_id" => $customer_id
        ), array(
            "primary_card" => APConstants::OFF_FLAG
        ));
        $this->payment_m->update_by_many(
            array(
                "customer_id" => $customer_id,
                "callback_tran_id" => $callback_tran_id
            ), array(
            "primary_card" => APConstants::ON_FLAG
        ));

        // #543: BUG: last payment method is not always set as standard
        // Update invoice type (make CC is standard payment method)
        $this->customer_m->update_by_many(array(
            'customer_id' => $customer_id
        ), array(
            'invoice_type' => '1'
        ));

        // Add payment library and make pending payment history (the system will call authorize_success_callback when success charge)
        $this->load->library('payone');
        $payment_result = $this->payone->make_pending_payment($customer_id);

        // If make payment successfully
        if ($payment_result) {
            // Update data to customer
            $this->customer_m->update_by_many(array(
                "customer_id" => $customer_id
            ),
                array(
                    "payment_detail_flag" => APConstants::ON_FLAG,
                    "last_updated_date" => now()
                ));

            // update: convert registration process flag to customer_product_setting.
            CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'payment_detail_flag', APConstants::ON_FLAG);

            // If open balance less than 0.1 will activated customer now
            $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
            $open_balance = $open_balance_data['OpenBalanceDue'];
            if ($open_balance <= 0.1) {
                // we dont have to tell the Customer that a valid credit Card with non working payment can reactivate the account.
                // in most cases the Customer will Chose a Card that can handle the payment if it is valid
                // Only reactivate if deactivated_type = auto
                customers_api::reactivateCustomerWhenPaymentSuccess($customer_id);
            }

            MailUtils::sendEmail('', "nguyen.trong.dung830323@gmail.com", 'ClevverMail Success Payment - ' . $mode,
                'Transaction Success ID:' . $callback_tran_id);
            log_message(APConstants::LOG_INFOR, '>>>>>>>>>>>>>> Transaction Success:' . $callback_tran_id);
            $this->template->build('success_callback');
        } else {
            MailUtils::sendEmail('', "nguyen.trong.dung830323@gmail.com", 'ClevverMail Success Fail - ' . $mode,
                'Transaction Success ID:' . $callback_tran_id);
            log_message(APConstants::LOG_INFOR, '>>>>>>>>>>>>>> Transaction Fail:' . $callback_tran_id);
            $this->template->build('error_callback');
        }
    }

    /**
     * Error callback
     */
    public function error_callback()
    {
        $mode = APContext::getFullBasePath();
        $this->template->set_layout(FALSE);
        $callback_tran_id = $this->input->get_post('callback_tran_id');
        MailUtils::sendEmail('', "nguyen.trong.dung830323@gmail.com", 'ClevverMail Fail Payment - ' . $mode, 'Transaction Fail ID:' . $callback_tran_id);
        log_message(APConstants::LOG_ERROR, '>>>>>>>>>>>>>> Transaction Error:' . $callback_tran_id);
        $this->template->build('error_callback');
    }

    /**
     * Paypal Success callback
     */
    public function payment_paypal_return()
    {
        $this->template->set_layout(FALSE);
        $this->load->model('invoices/invoice_detail_manual_m');
        $this->load->model('paypal_tran_hist_m');

        log_audit_message(APConstants::LOG_ERROR, 'Start call payment_paypal_return.');
        $params = json_encode($_POST);
        $reveice_message = json_encode($params);
        log_audit_message(APConstants::LOG_ERROR, 'payment_paypal_return Message:' . $reveice_message);

        $reference = $this->input->get_post("invoice_id");
        log_message(APConstants::LOG_DEBUG, '>>>>>>>>>>>>>>>>payment_paypal_return:>>>' . $reference);
        $paypal_transaction_hist = $this->paypal_tran_hist_m->get_by_many(array(
            'invoice_id' => $reference
        ));
        if (empty($paypal_transaction_hist)) {
            log_message(APConstants::LOG_DEBUG,
                '>>>>>>>>>>>>>>>>payment_paypal_return:>>>' . $reference . ' Can not find the paypal transaction hist');
            return;
        }

        $this->load->library('merchant');
        $this->merchant->load('paypal_express');
        $settings = array(
            'username' => Settings::get(APConstants::PAYMENT_PAYPAL_USERNAME_CODE),
            'password' => Settings::get(APConstants::PAYMENT_PAYPAL_PASSWORD_CODE),
            'signature' => Settings::get(APConstants::PAYMENT_PAYPAL_SIGNATURE_CODE),
            'test_mode' => Settings::get(APConstants::PAYMENT_PAYPAL_TEST_MODE) == 'true'
        );
        $this->merchant->initialize($settings);

        $this->load->model('payment/external_tran_hist_m');
        $this->load->model('invoices/invoice_summary_m');
        $this->load->model('customers/customer_m');

        $customer_id = '';
        $invoice_id = '';
        $arr_reference = explode("_", $reference);
        if (count($arr_reference) <= 3) {
            log_message(APConstants::LOG_ERROR, '>>>>>>>>>>>>>>>>payment_paypal_return:>>>Reference format is not correct.');
            return;
        }
        $invoice_id = $arr_reference[1];
        $customer_id = $arr_reference[2];

        $customer = $this->customer_m->get($customer_id);
        $open_balance = $paypal_transaction_hist->amount;
        $params = array(
            'amount' => $open_balance,
            'currency' => $paypal_transaction_hist->currency,
            'description' => $paypal_transaction_hist->description
            //'return_url' => base_url() . 'payment/payment_paypal_return?invoice_id=' . $reference,
            //'cancel_url' => base_url() . 'payment/payment_paypal_cancel?invoice_id=' . $reference
        );
        log_message(APConstants::LOG_DEBUG,
            '>>>>>>>>>>>>>>>>payment_paypal_return:>>> REQUEST' . json_encode($params));
        $response = $this->merchant->purchase_return($params);
        log_message(APConstants::LOG_DEBUG,
            '>>>>>>>>>>>>>>>>payment_paypal_return:>>> RESPONSE' . json_encode($response));

        log_message(APConstants::LOG_DEBUG, '>>>>>>>>>>>>>>>>payment_paypal_return:>>>transction_id:' . $response->reference());
        if ($response->success()) {
            $txn_id = $response->reference();
            // Update staus of paypal transaction hist table
            $this->paypal_tran_hist_m->update_by_many(
                array(
                    'customer_id' => $customer_id,
                    'invoice_id' => $reference
                ),
                array(
                    'txn_id' => $txn_id,
                    'last_updated_date' => now(),
                    'status' => APConstants::PAYPAl_STATUS_PENDING
                ));
        }
        redirect('invoices?paypal_status=1');
        // $this->template->build('payment_paypal_return');
    }

    /**
     * Received payment paypal notify
     */
    public function payment_paypal_ipn()
    {
        log_audit_message(APConstants::LOG_ERROR, 'Start call payment_paypal_ipn.');
        $params = json_encode($_POST);
        $reveice_message = json_encode($params);
        log_audit_message(APConstants::LOG_ERROR, 'Paypal IPN Message:' . $reveice_message);

        $this->template->set_layout(FALSE);
        $this->load->model('invoices/invoice_detail_manual_m');
        $this->load->model('paypal_tran_hist_m');
        $this->load->model('payment/external_tran_hist_m');
        $this->load->model('invoices/invoice_summary_m');
        $this->load->model('customers/customer_m');

        $txn_id = $this->input->get_post("txn_id");
        if (empty($txn_id) || $txn_id == null) {
            log_message(APConstants::LOG_DEBUG, '>>>>>>>>>>>>>>>>payment_paypal_ipn:>>> The paypal transaction ID is empty');
            return;
        }
        $paypal_transaction_hist = $this->paypal_tran_hist_m->get_by_many(array(
            'txn_id' => $txn_id
        ));
        if (empty($paypal_transaction_hist) || $paypal_transaction_hist == null) {
            log_message(APConstants::LOG_DEBUG, '>>>>>>>>>>>>>>>>payment_paypal_ipn:>>>' . $txn_id . ' Can not find the paypal transaction hist');
            return;
        }

        $reference = $paypal_transaction_hist->invoice_id;
        $customer_id = '';
        $invoice_id = '';
        $arr_reference = explode("_", $reference);
        if (count($arr_reference) <= 3) {
            log_message(APConstants::LOG_ERROR, '>>>>>>>>>>>>>>>>payment_paypal_ipn:>>>Reference format is not correct.');
            return;
        }
        $invoice_id = $arr_reference[1];
        $customer_id = $arr_reference[2];

        $customer = $this->customer_m->get($customer_id);
        $open_balance = $paypal_transaction_hist->amount;

        $payment_status = $this->input->get_post("payment_status");
        if (empty($payment_status) || $payment_status == null) {
            log_message(APConstants::LOG_DEBUG, '>>>>>>>>>>>>>>>>payment_paypal_ipn:>>> The paypal transaction status is empty');
            return;
        }
        $status = $payment_status;
        if ($payment_status == 'Completed') {
            $status = "OK";
        }

        // Check exist paypal transaction
        $external_tran_hist_exist = $this->external_tran_hist_m->get_by_many(
            array(
                "customer_id" => $customer_id,
                "tran_id" => 'paypal transaction:' . $txn_id
            ));

        $vat_obj = APUtils::getVatRateOfCustomer($customer_id);
        if ($external_tran_hist_exist != null) {
            // Update external tran hist status
            $this->external_tran_hist_m->update_by_many(
                array(
                    "customer_id" => $customer_id,
                    "tran_id" => 'paypal transaction:' . $txn_id
                ),
                array(
                    "tran_date" => date('Ymd'),
                    // fixbug #461
                    // "tran_amount" => $insert_data ['tranAmount'],
                    "tran_amount" => (-1) * abs($open_balance),
                    // fixbug #446
                    "payment_type" => "0", // payment
                    "created_date" => now(),
                    "status" => $status
                ));
        } else {
            $this->external_tran_hist_m->insert(
                array(
                    "customer_id" => $customer_id,
                    "tran_id" => 'paypal transaction:' . $txn_id,
                    "tran_date" => date('Ymd'),
                    // fixbug #461
                    // "tran_amount" => $insert_data ['tranAmount'],
                    "tran_amount" => (-1) * abs($open_balance),
                    // fixbug #446
                    "payment_type" => "0", // payment
                    "created_date" => now(),
                    "status" => $status
                ));
        }

        if ($payment_status == 'Completed') {
            // Update payment detail flag if this customer payment successfully
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

            $invoice_summary_check = $this->invoice_summary_m->get_by_many(
                array(
                    "customer_id" => $customer_id,
                    "payment_transaction_id" => $txn_id
                ));

            // Gets primary location.
            $location_id = APUtils::getPrimaryLocationBy($customer_id);
            if(empty($location_id)){
                // set default location is berlin.
                $location_id = 1;
            }

            $invoice_summary_id = 0;
            if ($invoice_summary_check == null) {
                log_message(APConstants::LOG_DEBUG, 'Insert new invoice summary.');
                // Insert paypal transaction fee to invoice summary table
                $invoice_summary_id = $this->invoice_summary_m->insert(
                    array(
                        "customer_id" => $customer_id,
                        "payment_transaction_id" => $txn_id,
                        "invoice_month" => APUtils::getCurrentYearMonthDate(),
                        "vat" => $vat_obj->rate,
                        "vat_case" => $vat_obj->vat_case_id,
                        "payment_1st_flag" => APConstants::OFF_FLAG,
                        "payment_1st_amount" => 0,
                        "payment_2st_flag" => APConstants::OFF_FLAG,
                        "payment_2st_amount" => 0,
                        "total_invoice" => $paypal_transaction_hist->paypal_tran_fee,
                        "invoice_type" => '2',
                        "location_id" => $location_id
                    ));

                $invoice_id = 'INV_' . APUtils::getCurrentYearMonthDate() . '_' . $customer_id . "_" . $invoice_summary_id;
                $new_invoice_code = APUtils::generateInvoiceCodeById($invoice_summary_id);
                // Insert data to invoice_summary table
                $this->invoice_summary_m->update_by_many(array(
                    "id" => $invoice_summary_id
                ), array(
                    "invoice_code" => $new_invoice_code,
                    "update_flag" => false
                ));

                $this->invoice_summary_by_location_m->insert(array(
                    "customer_id" => $customer_id,
                    "invoice_summary_id" => $invoice_summary_id,
                    "invoice_code" => $new_invoice_code,
                    "invoice_month" => APUtils::getCurrentYearMonthDate(),
                    "vat" => $vat_obj->rate,
                    "vat_case" => $vat_obj->vat_case_id,
                    "rev_share" => price_api::getRevShareOfLocation($location_id),
                    "total_invoice" =>  $paypal_transaction_hist->paypal_tran_fee,
                    "invoice_type" => '2',
                    "payment_transaction_id" => $txn_id,
                    "location_id" => $location_id
                ));
            }

            $invoice_summary_detail_check = $this->invoice_detail_manual_m->get_by_many( array(
                "customer_id" => $customer_id,
                "invoice_summary_id" => $invoice_summary_id
            ));

            // Insert to invoice summary detail
            if ($invoice_summary_detail_check == null) {
                log_message(APConstants::LOG_DEBUG, 'Insert new $invoice_summary_detail_check.');
                // Insert data to invoice_summary_manual
                $this->invoice_detail_manual_m->insert(
                    array(
                        "customer_id" => $customer_id,
                        "created_date" => now(),
                        "description" => 'Paypal Transaction Fee',
                        "quantity" => 1,
                        "net_price" => $paypal_transaction_hist->paypal_tran_fee,
                        "vat" => $vat_obj->rate,
                        "vat_case" => $vat_obj->vat_case_id,
                        "gross_price" => $paypal_transaction_hist->paypal_tran_fee + $paypal_transaction_hist->paypal_tran_vat,
                        "payment_flag" => APConstants::ON_FLAG,
                        "payment_date" => now(),
                        "invoice_date" =>  APUtils::getCurrentYearMonthDate(),
                        "invoice_summary_id" => $invoice_summary_id,
                        "location_id" => $location_id
                    ));
            }

            // Update staus of paypal transaction hist table
            $this->paypal_tran_hist_m->update_by_many(
                array(
                    'customer_id' => $customer_id,
                    'invoice_id' => $reference
                ),
                array(
                    'last_updated_date' => now(),
                    'status' => APConstants::PAYPAL_STATUS_APPROVAL
                ));

            $after_open_balance = APUtils::getCurrentBalance($customer_id);
            $target_month = APUtils::getCurrentMonthInvoice();
            $target_year = APUtils::getCurrentYearInvoice();
            $current_invoice_month = $target_year . $target_month;

            // Update data
            if ($after_open_balance <= 0.1) {
                // Update 2 st payment flag
                $this->invoice_summary_m->update_by_many(
                    array(
                        'customer_id' => $customer_id,
                        "(invoice_type = 'auto' OR invoice_type IS NULL)" => null,
                        "invoice_month < " => $current_invoice_month
                    ),
                    array(
                        'invoice_flag' => '1',
                        'payment_2st_flag' => APConstants::ON_FLAG,
                        'payment_1st_flag' => APConstants::ON_FLAG,
                        "update_flag" => false
                    ));

                // Only reactivate if deactivated_type = auto
                customers_api::reactivateCustomerWhenPaymentSuccess($customer_id);

                // #1012 Prepayment request
                mailbox_api::completeManualPrepaymentRequest($customer_id);
            }
        }

        // Get payment manual flag
        $payment_manual = $this->input->get_post("payment_manual");
        if ($payment_manual == '1') {
            log_message(APConstants::LOG_DEBUG, 'Make Payment Paypal Manual');
            echo "OK";
        }
    }

    /**
     * Paypal Success callback
     */
    public function payment_paypal_cancel()
    {
        $this->load->model('paypal_tran_hist_m');

        $reference = $this->input->get_post("invoice_id");
        log_message(APConstants::LOG_DEBUG, '>>>>>>>>>>>>>>>>payment_paypal_calcel:>>>' . $reference);
        $paypal_transaction_hist = $this->paypal_tran_hist_m->get_by_many(array(
            'invoice_id' => $reference
        ));
        if (empty($paypal_transaction_hist)) {
            log_message(APConstants::LOG_DEBUG,
                '>>>>>>>>>>>>>>>>payment_paypal_calcel:>>>' . $reference . ' Can not find the paypal transaction hist');
            redirect('invoices?paypal_status=2');
            return;
        }

        $message = json_encode($this->input->get_post());
        $paypal_tran_hist = $this->paypal_tran_hist_m->get_by_many(array(
            'invoice_id' => $reference
        ));
        // Update staus of paypal transaction hist table
        $this->paypal_tran_hist_m->update_by_many(array(
            'invoice_id' => $reference
        ),
            array(
                'last_updated_date' => now(),
                'status' => APConstants::PAYPAl_STATUS_ERROR,
                'message' => $message
            ));

        redirect('invoices?paypal_status=2');
        // $this->template->build('payment_paypal_calcel');
    }

    /**
     * Default page for 404 error.
     */
    public function check_credit_card()
    {
        // Get config
        $merchant_id = $this->config->item('payone.merchant-id');
        $portal_id = $this->config->item('payone.portal-id');
        $portal_key = $this->config->item('payone.portal-key');
        $sub_account_id = $this->config->item('payone.sub-account-id');
        $mode = $this->config->item('payone.mode');
        $encoding = $this->config->item('payone.encoding');

        // Get input parameter
        $card_number = $this->input->get_post('card_number');
        $card_type = $this->input->get_post('card_type');
        $cvc = $this->input->get_post('cvc');
        $expired_year = $this->input->get_post('expired_year');
        $expired_month = $this->input->get_post('expired_month');
        $expired = $expired_year . $expired_month;

        // Build service
        $bootstrap = new Payone_Bootstrap();
        $bootstrap->init();
        $builder = new Payone_Builder();
        $service = $builder->buildServiceVerificationCreditCardCheck();

        $request = new Payone_Api_Request_CreditCardCheck();
        $request->setPortalid($portal_id);
        $request->setMid($merchant_id);
        $request->setKey($portal_key);
        $request->setAid($sub_account_id);
        $request->setMode($mode);
        $request->setEncoding($encoding);
        $request->setStorecarddata("yes");
        $request->setLanguage("de");

        $request->setCardpan($card_number);
        $request->setCardcvc2($cvc);
        $request->setCardtype($card_type);
        $request->setCardexpiredate($expired);

        // Set Parameters here ...
        $response = $service->check($request);
        if (!empty($response) && $response->getStatus() === 'VALID') {
            return array(
                'status' => true,
                "message" => '',
                "pseudocardpan" => $response->getPseudocardpan(),
                "truncatedcardpan" => $response->getTruncatedcardpan()
            );
        }
        return array(
            'status' => false,
            "message" => $response->getCustomermessage()
        );
    }

    /**
     * Make default payment 3d credit card.
     */
    public function check_3dcredit_card()
    {
        $account_type = $this->input->post('account_type');
        $card_type = $this->input->post('card_type');
        $card_number = $this->input->post('card_number');
        $card_name = $this->input->post('card_name');
        $cvc = $this->input->post('cvc');
        $expired_year = $this->input->post('expired_year');
        $expired_month = $this->input->post('expired_month');
        $customer_id = APContext::getCustomerCodeLoggedIn();

        // Make request to create payment
        $expired = $expired_year . $expired_month;
        $callback_tran_id = APContext::getCustomerCodeLoggedIn() . '_' . APUtils::generateRandom(32);

        $this->success_output('1');
        return;
    }

    /**
     * Update 3D secure flag (Using to update secure_3d_flag)
     */
    private function update_3d_secure_flag()
    {
        $bootstrap = new Payone_Bootstrap();
        $bootstrap->init();
        $builder = new Payone_Builder();
        $service = $builder->buildServiceVerification3dsCheck();

        // Get config
        $merchant_id = $this->config->item('payone.merchant-id');
        $portal_id = $this->config->item('payone.portal-id');
        $portal_key = $this->config->item('payone.portal-key');
        $sub_account_id = $this->config->item('payone.sub-account-id');
        $mode = $this->config->item('payone.mode');
        $encoding = $this->config->item('payone.encoding');

        $request_3d_check = new Payone_Api_Request_3dsCheck();
        $request_3d_check->setPortalid($portal_id);
        $request_3d_check->setMid($merchant_id);
        $request_3d_check->setKey($portal_key);
        $request_3d_check->setAid($sub_account_id);
        $request_3d_check->setMode($mode);

        // Get all payment information
        $list_payments = $this->payment_m->get_all();
        foreach ($list_payments as $payment) {
            if (!empty($payment->pseudocardpan)) {
                $pseudocardpan = $payment->pseudocardpan;
            }
        }
    }

    /**
     * Default page for 404 error.
     */
    private function check_3dcredit_card_temp()
    {
        $bootstrap = new Payone_Bootstrap();
        $bootstrap->init();
        $builder = new Payone_Builder();
        $service = $builder->buildServiceVerification3dsCheck();

        // Get config
        $merchant_id = $this->config->item('payone.merchant-id');
        $portal_id = $this->config->item('payone.portal-id');
        $portal_key = $this->config->item('payone.portal-key');
        $sub_account_id = $this->config->item('payone.sub-account-id');
        $mode = $this->config->item('payone.mode');
        $encoding = $this->config->item('payone.encoding');

        $card_number = $this->input->get_post('card_number');
        $card_type = $this->input->get_post('card_type');
        $cvc = $this->input->get_post('cvc');
        $expired_year = $this->input->get_post('expired_year');
        $expired_month = $this->input->get_post('expired_month');
        $expired = $expired_year . $expired_month;

        // Init 3d check
        $request_3d_check = new Payone_Api_Request_3dsCheck();
        $request_3d_check->setPortalid($portal_id);
        $request_3d_check->setMid($merchant_id);
        $request_3d_check->setKey($portal_key);
        $request_3d_check->setAid($sub_account_id);
        $request_3d_check->setMode($mode);
        $request_3d_check->setEncoding($encoding);
        $request_3d_check->setAmount('1');
        $request_3d_check->setCurrency('USD');
        $request_3d_check->setClearingtype('cc');
        $request_3d_check->setEncoding('UTF-8');
        $request_3d_check->setCardtype($card_type);
        $request_3d_check->setCardcvc2($cvc);
        $request_3d_check->setCardexpiredate($expired);
        $request_3d_check->setCardpan($card_number);
        $request_3d_check->setExiturl(APContext::getFullBasePath() . '/cron/payone_fallback?key=5eb96ffccee348403fbf2cd4a0addca0');

        // Build service
        $response = $service->check($request_3d_check);
        // Check response
        if (!empty($response) && $response->getStatus() != 'ERROR') {
            $this->success_output('1');
        } else {
            $this->success_output('0');
        }
    }

    private function test_payone3()
    {
        // Get config
        $merchant_id = $this->config->item('payone.merchant-id');
        $portal_id = $this->config->item('payone.portal-id');
        $portal_key = $this->config->item('payone.portal-key');

        $sub_account_id = $this->config->item('payone.sub-account-id');
        $mode = $this->config->item('payone.mode');
        $encoding = $this->config->item('payone.encoding');

        // Request
        $request = "authorization";

        $id[1] = "123-345"; // Your item no.
        $pr[1] = 5900; // Price in Cent
        $no[1] = 1; // Quantity
        $de[1] = "Puma Outdoor"; // Item description
        $va[1] = 19; // VAT (optional)
        $amount = round($pr[1] * $no[1]); // Total amount
        $currency = "EUR"; // Currency
        $reference = "73464354"; // Merchant reference no.
        $customerid = "123456"; // Merchant customer ID (option)
        $productid = "123456";

        $hash = md5(
            $sub_account_id . $amount . $currency . $customerid . $de[1] . $id[1] . $no[1] . $portal_id . $pr[1] . $reference . $request . $va[1] .
            $portal_key); // Parameters
        // in
        // sorted
        // order
        // +
        // key

        // Display the current page
        $this->template->set('productid', $productid);
        $this->template->set('mode', $mode);
        $this->template->set('request', $request);
        $this->template->set('currency', $currency);
        $this->template->set('portal_id', $portal_id);
        $this->template->set('sub_account_id', $sub_account_id);
        $this->template->set('customerid', $customerid);
        $this->template->set('portal_key', $portal_key);
        $this->template->set('reference', $reference);
        $this->template->set('hash', $hash)->build('buy_now');
    }

    private function test_payone2()
    {
        // load the theme_example view
        $this->load->library('payment/payone');
        $this->payone->preauthorize(1, now(), 1000);
    }

    private function test_payone()
    {
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
        // $service = $builder->buildServicePaymentPreauthorize();
        $service = $builder->buildServiceVerification3dsCheck();

        // $request = new Payone_Api_Request_Preauthorization();
        $request = new Payone_Api_Request_Authorization();
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
        $request_person_data->setLastname("Nguyen Test 01");
        $request_person_data->setCountry('DE');
        $request->setPersonalData($request_person_data);

        // Set credit card
        // Get input parameter
        $card_number = '5486225329055904';
        // $card_number = '5453010000080200';
        $card_type = 'M';
        $cvc = '738';
        $expired = '1701';

        $request_credit_card = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_CreditCard();
        $request_credit_card->setCardpan($card_number);
        $request_credit_card->setCardtype($card_type);
        $request_credit_card->setCardcvc2($cvc);
        $request_credit_card->setCardexpiredate($expired);
        $request_credit_card->setEcommercemode('internet');
        $request->setPayment($request_credit_card);

        // Init request 3D check
        $request_3d_check = new Payone_Api_Request_3dsCheck();
        $request_3d_check->setPortalid($portal_id);
        $request_3d_check->setMid($merchant_id);
        $request_3d_check->setKey($portal_key);
        $request_3d_check->setAid($sub_account_id);
        $request_3d_check->setMode($mode);
        $request_3d_check->setEncoding($encoding);
        $request_3d_check->setAmount('1');
        $request_3d_check->setCurrency('USD');
        $request_3d_check->setClearingtype('cc');
        $request_3d_check->setEncoding('UTF-8');
        $request_3d_check->setCardtype($card_type);
        $request_3d_check->setCardcvc2($cvc);
        $request_3d_check->setCardexpiredate($expired);
        $request_3d_check->setCardpan($card_number);
        // $request->setPayment($request_credit_date);
        $request_3d_check->setExiturl('http://54.214.27.234/dev/index.php/cron/payone_fallback?key=5eb96ffccee348403fbf2cd4a0addca0');

        // $response = $service->check($request_3d_check);
        // var_dump($response);

        /**
         * $xid = ''; if (!empty($response) && $response->status == 'VALID') { $xid = $response->xid; }
         */

        $xid = '126504147';
        $service = $builder->buildServicePaymentPreauthorize();
        $secure = new Payone_Api_Request_Parameter_Authorization_3dsecure();
        $secure->setXid($xid);
        // $request->set3dsecure($secure);
        // $response = $service->preauthorize($request);
        // var_dump($response);

        $service = $builder->buildServicePaymentAuthorize();
        $response = $service->authorize($request);
        var_dump($response);

        // Capture
    }

}

