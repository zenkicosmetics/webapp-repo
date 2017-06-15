<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class mailbox extends Public_Controller {

    private $shipping_validation_rules = array(
        array(
            'field' => 'shipment_address_name',
            'label' => 'lang:shipment_address_name',
            'rules' => 'validname'
        ),
        array(
            'field' => 'shipment_city',
            'label' => 'lang:shipment_city',
            'rules' => 'required|validname'
        ),
        array(
            'field' => 'shipment_company',
            'label' => 'lang:shipment_company',
            'rules' => 'valid_companyname|callback__check_shipment_company'
        ),
        array(
            'field' => 'shipment_country',
            'label' => 'lang:shipment_country',
            'rules' => 'required'
        ),
        array(
            'field' => 'shipment_postcode',
            'label' => 'lang:shipment_postcode',
            'rules' => 'required|postcode'
        ),
        array(
            'field' => 'shipment_region',
            'label' => 'lang:shipment_region',
            'rules' => 'validname'
        ),
        array(
            'field' => 'shipment_street',
            'label' => 'lang:shipment_street',
            'rules' => 'validname'
        )
    );

    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     *
     * @todo Document properly please.
     */
    public function __construct() {
        parent::__construct();

        // Payone
        $this->load->library('Payone_lib');
        $this->load->config('payone');

        // Model
        $this->load->model('scans/envelope_m');
        $this->load->model('customers/customer_m');
        $this->load->model('scans/envelope_pdf_content_m');
        $this->load->model('cloud/customer_cloud_m');
        $this->load->model('scans/envelope_file_m');
        $this->load->model('scans/envelope_completed_m');

        $this->load->model('mailbox/envelope_customs_m');
        $this->load->model('mailbox/envelope_customs_detail_m');
        $this->load->model('scans/envelope_package_m');
        $this->load->model('payment/payment_m');
        $this->load->model('mailbox/user_paging_m');
        $this->load->model('email/email_m');
        $this->load->model('mailbox/postbox_setting_m');
        $this->load->model('mailbox/postbox_m');
        $this->load->model('addresses/customers_address_m');
        $this->load->model('settings/countries_m');

        $this->load->model(array(
            'scans/envelope_shipping_request_m',
            'settings/terms_service_m'
        ));

        $this->load->library('S3');

        $this->lang->load(array(
            'delete_permission',
            'activity_permission'
        ));

        // load panation
        $this->load->library("pagination");
        $this->load->library("mailbox/mailbox_api");
        $this->load->library("scans/scans_api");
        $this->load->language("mailbox");
        $this->load->language("addresses/address");
    }

    /**
     * Default (For new items)
     */
    public function index() {
        $this->load->library('customers/customers_api');

        $base_url = base_url() . 'mailbox/index';
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();

        // Check open balance
        $total = APUtils::getAdjustOpenBalanceDue($parent_customer_id);
        $open_balance = $total["OpenBalanceDue"];
        $total_open_balance = $total["OpenBalanceDue"] + $total["OpenBalanceThisMonth"];

        if ($open_balance < 0.01) {
            // Activate this customer
            $customer = $this->customer_m->get_current_customer_info();
            // Only activated if this customer have deactivated type is not manual
            if ($customer->activated_flag != '1' && $customer->deactivated_type != 'manual') {
                //if ($customer->shipping_address_completed == '1' && $customer->invoicing_address_completed == '1' &&
                //    $customer->postbox_name_flag == '1' && $customer->name_comp_address_flag == '1' && $customer->city_address_flag == '1' &&
                //    $customer->payment_detail_flag == '1' && $customer->email_confirm_flag == '1'
                //) {
                //    customers_api::activateCustomerWhenUpdatePaymentMethod($customer_id);
                //}
                // update: convert registration process flag to customer_product_setting.
                $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;
                CustomerProductSetting::doActiveCustomer($customer_id, $created_by_id);
            }

            // #1012 Prepayment request
            $processPaymentFlag = APContext::getSessionValue('completeManualPrepaymentRequestFlag');
            if ($processPaymentFlag != APConstants::ON_FLAG) {
                mailbox_api::completeManualPrepaymentRequest($customer_id);
                APContext::setSessionValue('completeManualPrepaymentRequestFlag', APConstants::ON_FLAG);
            }
        }

        $currency = $this->customer_m->get_standard_setting_currency($customer_id);
        if (empty($currency)) {
            $currency = $this->currencies_m->get_by(array('currency_short' => 'EUR'));
        }

        $decimal_separator = $this->customer_m->get_standard_setting_decimal_separator($customer_id);
        $this->template->set('decimal_separator', $decimal_separator);

        $this->load_envelope($base_url, 7);
        $this->template->set('open_balance', $open_balance);
        $this->template->set('total_open_balance', 1 * $total_open_balance);
        $this->template->set('currency', $currency);
        $this->template->build('index');
    }

    /**
     * Default (For new items)
     */
    public function news() {
        $customer_id = APContext::getParentCustomerCodeLoggedIn();
        // Check open balance
        $total = APUtils::getAdjustOpenBalanceDue($customer_id);
        $total_open_balance = $total["OpenBalanceDue"] + $total["OpenBalanceThisMonth"];

        $base_url = base_url() . 'mailbox/news';
        $this->load_envelope($base_url, 1);
        $this->template->set('open_balance', $total["OpenBalanceDue"]);
        $this->template->set('total_open_balance', 1 * $total_open_balance);
        $this->template->build('index');
    }

    /**
     * Default (For envelope_scan)
     */
    public function envelope_scan() {
        $customer_id = APContext::getParentCustomerCodeLoggedIn();

        // Check open balance
        $total = APUtils::getAdjustOpenBalanceDue($customer_id);
        $total_open_balance = $total["OpenBalanceDue"] + $total["OpenBalanceThisMonth"];

        $base_url = base_url() . 'mailbox/envelope_scan';
        $this->load_envelope($base_url, 2);
        $this->template->set('open_balance', $total["OpenBalanceDue"]);
        $this->template->set('total_open_balance', 1 * $total_open_balance);
        $this->template->build('index');
    }

    /**
     * Default (For scans)
     */
    public function scans() {
        $customer_id = APContext::getParentCustomerCodeLoggedIn();

        // Check open balance
        $total = APUtils::getAdjustOpenBalanceDue($customer_id);
        $total_open_balance = $total["OpenBalanceDue"] + $total["OpenBalanceThisMonth"];

        $base_url = base_url() . 'mailbox/scans';
        $this->load_envelope($base_url, 2);
        $this->template->set('open_balance', $total["OpenBalanceDue"]);
        $this->template->set('total_open_balance', 1 * $total_open_balance);
        $this->template->build('index');
    }

    /**
     * Default (For new items)
     */
    public function send_out() {
        $customer_id = APContext::getParentCustomerCodeLoggedIn();

        // Check open balance
        $total = APUtils::getAdjustOpenBalanceDue($customer_id);
        $total_open_balance = $total["OpenBalanceDue"] + $total["OpenBalanceThisMonth"];

        $base_url = base_url() . 'mailbox/send_out';
        $this->load_envelope($base_url, 4);
        $this->template->set('open_balance', $total["OpenBalanceDue"]);
        $this->template->set('total_open_balance', 1 * $total_open_balance);
        $this->template->build('index');
    }

    /**
     * Default (For new items)
     */
    public function trash() {
        $customer_id = APContext::getParentCustomerCodeLoggedIn();

        // Check open balance
        $total = APUtils::getAdjustOpenBalanceDue($customer_id);
        $total_open_balance = $total["OpenBalanceDue"] + $total["OpenBalanceThisMonth"];

        $base_url = base_url() . 'mailbox/trash';
        $this->load_envelope($base_url, 5);
        $this->template->set('open_balance', $total["OpenBalanceDue"]);
        $this->template->set('total_open_balance', 1 * $total_open_balance);
        $this->template->build('index');
    }

    /**
     * Default (For new items)
     */
    public function instore() {
        $customer_id = APContext::getParentCustomerCodeLoggedIn();

        // Check open balance
        $total = APUtils::getAdjustOpenBalanceDue($customer_id);
        $total_open_balance = $total["OpenBalanceDue"] + $total["OpenBalanceThisMonth"];

        $base_url = base_url() . 'mailbox/instore';
        $this->load_envelope($base_url, 6);
        $this->template->set('open_balance', $total["OpenBalanceDue"]);
        $this->template->set('total_open_balance', 1 * $total_open_balance);
        $this->template->build('index');
    }

    /**
     * Get temp hash
     */
    private function getCreditCardCheckHash() {
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
     * Load envelope information based on input parameters.
     *
     * @param
     *            $envelope_status
     */
    private function load_envelope($base_url, $search_type) {
        $this->load->library('mailbox/mailbox_api');
        $this->load->library('customers/customers_api');
        $this->load->library('scans/scans_api');
        $this->load->library('payment/payment_api');
        $this->load->library('addresses/addresses_api');

        // Get post box id
        $postbox_id = $this->input->get_post('p');
        $skip = $this->input->get_post('skip');
        $first_regist = $this->input->get_post('first_regist');
        $declare_customs = $this->input->get_post('declare_customs');
        $this->template->set('hash', $this->getCreditCardCheckHash());

        // Store skip status to session
        if ($skip == '1') {
            APContext::setSessionValue(APConstants::SESSION_SKIP_CUS_KEY, $skip);
        }

        $fullTextSearchFlag = $this->input->get_post('fullTextSearchFlag');
        $fullTextSearchValue = $this->input->get_post('fullTextSearchValue');

        if (($postbox_id === '' || $postbox_id === null || $postbox_id === false)) {
            $all_postbox = $this->get_all_postbox();
            if ((!empty($all_postbox) && count($all_postbox[0]->list_postbox) > 1) || APContext::isPrimaryCustomerUser()) {
                redirect('mailbox/' . $this->method . '?p=0&skip=' . $skip . '&first_regist=' . $first_regist . '&declare_customs=' . $declare_customs);
            } else {
                $postbox_id = $this->get_first_postbox();

                if (!empty($postbox_id)) {
                    redirect('mailbox/' . $this->method . '?p=' . $postbox_id . '&skip=' . $skip . '&first_regist=' . $first_regist . '&declare_customs=' . $declare_customs);
                }
            }
        }

        $start = $this->input->get_post('start');
        $limit = $this->input->get_post('limit');

        if (empty($start)) {
            $start = 0;
        }
        if (empty($limit)) {
            $limit = APContext::getPagingSetting(); // APConstants::DEFAULT_PAGE_ROW;
            $limit = ($limit > 100) ? 100 : $limit;
        }

        // 20142021 Start added: #421
        $hide_panes = APContext::getHidePanesSetting();
        // 20142021 End added: #421
        // update limit into user_paging.
        APContext::updatePagingSetting($limit);
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $customer_id = 0;
        if ($postbox_id != 0) {
            $customer_id = APContext::getCustomerCodeLoggedInMailboxByPostbox($postbox_id);
            $customer = CustomerUtils::getCustomerByID($customer_id);
            $this->template->set('customer', $customer);
            $activated_flag = $customer->activated_flag;
        } else {
            $customer_id = 0;
            $customer = CustomerUtils::getCustomerByID($parent_customer_id);
            $this->template->set('customer', $customer);
            $activated_flag = $customer->activated_flag;
        }

        $direct_access_customer_view_flag = APContext::isAdminDirectAccessCustomerView();
        if (($activated_flag == '1' || $direct_access_customer_view_flag == '1')) {
            $output = mailbox_api::loadEnvelopes($parent_customer_id, $customer_id, $postbox_id, $search_type, $fullTextSearchFlag, $fullTextSearchValue, $start, $limit);

            // update notification.
            if ($fullTextSearchFlag != '1') {
                if ($search_type == 2 || $search_type == 4) {
                    $this->update_new_notification_flag($customer_id, $postbox_id);
                }
            }
            
            // Truong hop la khai bao customs
            $this->template->set('declare_customs_flag', $declare_customs);

            // Load all pending envelope need to declare customs
            //$pending_envelope_customs = mailbox_api::getEnvelopeCustoms($parent_customer_id);
            //$this->template->set('pending_envelope_customs', $pending_envelope_customs);

            // Load all pending envelope need to declare customs
            if ($postbox_id == 0 && (APContext::isPrimaryCustomerUser() || APContext::isStandardCustomer() )) {
                $list_user_id = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($parent_customer_id);
                $list_user_id[] = $parent_customer_id;
                $list_pending_envelope_customs = mailbox_api::getListPendingEnvelopeCustomsBy($list_user_id);
            } else {
                $list_pending_envelope_customs = mailbox_api::getListPendingEnvelopeCustoms($customer_id);
            }
            $this->template->set('list_pending_envelope_customs', $list_pending_envelope_customs);
        } else {
            $output = array(
                'total' => 0,
                'data' => array()
            );
        }

        // Total record
        $total = $output['total'];
        $envelopes = $output['data'];

        // config panation.
        $config = array();
        $config["base_url"] = $base_url;
        $config["total_rows"] = $total;
        $config["per_page"] = $limit;
        $config["uri_segment"] = APConstants::PANATION_URI_SEGMET;
        $choice = $config["total_rows"] / $config["per_page"];

        $this->pagination->initialize($config);
        $page = ($this->uri->segment(APConstants::PANATION_URI_SEGMET)) ? $this->uri->segment(APConstants::PANATION_URI_SEGMET) : 0;

        // Ouput data to view
        if (empty($skip)) {
            $skip = 0;
        }

        // Load payment information
        $payment_count = payment_api::countPayment($customer_id);
        $payment_exist = APConstants::OFF_FLAG;
        if ($payment_count > 0) {
            $payment_exist = APConstants::ON_FLAG;
        }
        $arr_package = array();
        if (count($envelopes)) {
            foreach ($envelopes as $envelope) {
                if (($envelope->package_id > 0) && (!in_array($envelope->package_id, $arr_package))) {
                    $shipping_address = EnvelopeUtils::getAddressOfEnvelope($customer->customer_id, $envelope->package_id);
                    $arr_package[$envelope->package_id] = $shipping_address->shipping_address_id;
                }
            }
        }

        // #589.
        //$list_collect_envelope = scans_api::getListCollectEnvelope($customer_id);
        // load term of service
        $terms_service = ci()->terms_service_m->get_system_term_service(array(
            "type" => '1',
            "use_flag" => '1'
        ));
        
        // Check duplicate transaction
        $trigger_charge_open_balance_due = APConstants::OFF_FLAG;
        if ($first_regist == APConstants::ON_FLAG) {
            $this->load->model('payment/payment_tran_hist_m');
            // Validate duplicate payment (will prevent it customer make same payment in 60 mins)
            $check_duplicate_payment = $this->payment_tran_hist_m->get_by_many(
                array(
                    "customer_id" => $customer_id,
                    "tran_date > " => now() - 60 * 60,
                    "tran_type" => "authorize"
                ));
            if (empty($check_duplicate_payment)) {
                $trigger_charge_open_balance_due = APConstants::ON_FLAG;
            }
        }
        $this->template->set('trigger_charge_open_balance_due', $trigger_charge_open_balance_due);
        
        $this->template->set('terms_service', $terms_service);
        $this->template->set('arr_package', $arr_package);
        //$this->template->set('list_collect_envelope', $list_collect_envelope);
        $this->template->set('hide_panes', $hide_panes);
        $this->template->set('skip', $skip);
        $this->template->set('payment_exist', $payment_exist);
        $this->template->set('first_regist', $first_regist);
        $this->template->set('fullTextSearchFlag', $fullTextSearchFlag);
        $this->template->set('fullTextSearchValue', $fullTextSearchValue);
        $this->template->set('p', $postbox_id);
        $this->template->set('page_link', $this->pagination->create_links());
        $this->template->set('current_postbox', $postbox_id);
        $this->template->set('start', $start);
        $this->template->set('limit', $limit);
        $this->template->set('total_envelope', $total);
        $this->template->set('envelopes', $envelopes);
        $this->template->set('search_type', $search_type);
    }

    /**
     * Click YES to request envelope scan in main table.
     */
    public function request_envelope_scan() {
        ci()->load->library('scans/scans_api');

        $this->template->set_layout(FALSE);

        $ids_input = $this->input->get_post('id');
        if (empty($ids_input) || count($ids_input) == 0) {
            $this->error_output('The list of envelope id is required.');
            return;
        }

        // #1012 - Prepayment method
        $ids = explode(",", $ids_input);

        // check verification postbox
        if ($this->check_postbox_verification($ids)) {
            $this->error_output('You must complete postbox verification of this item first.');
            return;
        }
        
        //get customer's ID 
        $customer_id = APContext::getCustomerCodeLoggedInMailbox($ids);

        // Add envelope scan request by customer
        scans_api::insertCompleteItem($ids_input, APConstants::SCAN_ENVELOPE_ORDER_BY_CUSTOMER_ACTIVITY_TYPE, APConstants::TRIGGER_BY_CUSTOMER, $customer_id);

        // Check prepayment with envelope's scan type
        $check_prepayment_data = CustomerUtils::checkApplyScanPrepayment(APConstants::TRIGGER_ACTION_TYPE_CUSTOMER, APConstants::ENVELOPE_SCAN_TYPE, $ids, $customer_id);

        // If prepayment is true 
        if ($check_prepayment_data['prepayment'] == true) {

            // Add envelope scan request to queue
            // Update envelope_scan_flag = 2 (orange)
            mailbox_api::requestEnvelopeScanToQueue($ids_input, $customer_id);

            //Add request prepayment by system
            scans_api::insertCompleteItem($ids_input, APConstants::REQUEST_PREPAYMENT_FOR_SCAN_ENVELOPE_BY_SYSTEM_ACTIVITY_TYPE);

            $check_prepayment_data['status'] = FALSE;
            echo json_encode($check_prepayment_data);
            return;
        }
        
        // Request envelope scan is successful
        // Update envelope_scan_flag = 0 (yellow) 
         mailbox_api::requestEnvelopeScan($ids_input, $customer_id);

        $this->success_output('');
    }

    /**
     * Open envelope scan.
     */
    public function open_envelope_scan() {
        $this->load->library('common/common_api');

        // Does not use layout
        $seconds_to_cache = APConstants::CACHED_SECONDS;

        $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
        header("Expires: $ts");
        header("Pragma: cache");
        header("Cache-Control: max-age=$seconds_to_cache");

        $this->template->set_layout(FALSE);
        $envelope_id = $this->input->get_post('id');
        $envelope = $this->envelope_m->get_by("id", $envelope_id);
        $customer_id = APContext::getCustomerCodeLoggedInMailbox($envelope_id);
        $preview_file = $this->envelope_file_m->get_by_many_order(
                array(
            "envelope_id" => $envelope_id,
            "customer_id" => $customer_id,
            "type" => '1'
                ), array(
            "updated_date" => "ASC",
            "created_date" => "ASC"
        ));

        $preview_file = common_api::setDynamicPathEnvelopeFile($preview_file);

        // Check if this is first letter
        if (APUtils::endsWith($envelope->envelope_code, '_000')) {
            $preview_file->public_file_name = APContext::getAssetPath() . Settings::get(APConstants::FIRST_ENVELOPE_KEY);
        } else if ($preview_file) {
            APUtils::download_amazon_file($preview_file);
        }

        $this->template->set('preview_file', $preview_file)->build('envelope_full');
    }

    /**
     * Open envelope scan.
     */
    public function open_item_scan() {
        $this->load->library('common/common_api');

        // Does not use layout
        $seconds_to_cache = APConstants::CACHED_SECONDS;

        $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
        header("Expires: $ts");
        header("Pragma: cache");
        header("Cache-Control: max-age=$seconds_to_cache");

        $this->template->set_layout(FALSE);
        $envelope_id = $this->input->get_post('id');
        $envelope = $this->envelope_m->get_by("id", $envelope_id);
        $customer_id = APContext::getCustomerCodeLoggedInMailbox($envelope_id);
        $preview_file = $this->envelope_file_m->get_by_many_order(
                array(
            "envelope_id" => $envelope_id,
            "customer_id" => $customer_id,
            "type" => '2'
                ), array(
            "updated_date" => "ASC",
            "created_date" => "ASC"
        ));

        $preview_file = common_api::setDynamicPathEnvelopeFile($preview_file);

        // Check if this is first letter
        if (APUtils::endsWith($envelope->envelope_code, '_000')) {
            $preview_file->public_file_name = APContext::getAssetPath() . Settings::get(APConstants::FIRST_LETTER_KEY);
        } else if ($preview_file) {
            APUtils::download_amazon_file($preview_file);
        }

        $this->template->set('preview_file', $preview_file)->build('document_full');
    }

    /**
     * Click YES to request scan item in main table.
     */
    public function request_scan() {
        ci()->load->library('scans/scans_api');

        $this->template->set_layout(FALSE);

        $ids_input = $this->input->get_post('id');
        if (empty($ids_input) || count($ids_input) == 0) {
            $this->error_output('The list of envelope id is required.');
            return;
        }

        // #1012 - Prepayment method
        $ids = explode(",", $ids_input);

        // check verification postbox
        if ($this->check_postbox_verification($ids)) {
            $this->error_output('You must complete postbox verification of this item first.');
            return;
        }
        
        //get customer's ID 
        $customer_id = APContext::getCustomerCodeLoggedInMailbox($ids);

        // Add request scan item by customer
        scans_api::insertCompleteItem($ids_input, APConstants::SCAN_ITEM_ORDER_BY_CUSTOMER_ACTIVITY_TYPE, APConstants::TRIGGER_BY_CUSTOMER, $customer_id);

        // Check prepayment with item's scan type
        $check_prepayment_data = CustomerUtils::checkApplyScanPrepayment(APConstants::TRIGGER_ACTION_TYPE_CUSTOMER, APConstants::ITEM_SCAN_TYPE, $ids, $customer_id);

        // If prepayment is true
        if ($check_prepayment_data['prepayment'] == true) {
            // Add item scan request to queue
            // Update item_scan_flag = 2 (orange)
            mailbox_api::requestItemScanToQueue($ids_input, $customer_id);

            //Insert activity request prepayment by system
            scans_api::insertCompleteItem($ids_input, APConstants::REQUEST_PREPAYMENT_FOR_SCAN_ITEM_BY_SYSTEM_ACTIVITY_TYPE);

            $check_prepayment_data['status'] = FALSE;
            echo json_encode($check_prepayment_data);
            return;
        }
        
        // Request item scan is successful
        // Update item_scan_flag = 0 (yellow) 
         mailbox_api::requestItemScan($ids_input, $customer_id);
         
        // Return
        $this->success_output('');
    }

    /**
     * Request delete envelope (logical delete).
     */
    public function request_delete_envelope() {
        ci()->load->library('scans/scans_api');

        $ids_input = $this->input->get_post('id');
        $customer = APContext::getCustomerLoggedIn();
        if (!empty($ids_input)) {
            $ids = explode(",", $ids_input);

            EnvelopeUtils::trashEnvelopes($ids, $customer);
        }
        $this->success_output('');
    }

    /**
     * Request delete envelope (physical delete).
     */
    public function delete_envelope($envelope) {
        $this->template->set_layout(FALSE);
        $id = $this->input->get_post('id');
        $customer_id = APContext::getCustomerCodeLoggedInMailbox($id);
        // 1 is out of trash folder | 2: trash folder
        $delete_type = $this->input->get_post('delete_type');
        $delete_key_sign = APUtils::build_delete_sign($envelope->envelope_scan_flag, $envelope->item_scan_flag, $envelope->direct_shipping_flag, $envelope->collect_shipping_flag);
        $delete_activity = lang("activity_" . $delete_key_sign);
        if (!empty($delete_activity)) {
            // Call method to delete
            $this->envelope_m->update_by_many(
                    array(
                'id' => $id,
                'to_customer_id' => $customer_id,
                'trash_flag IS NULL' => null
                    ), array(
                "trash_flag" => APConstants::OFF_FLAG,
                "trash_date" => now(),
                'last_updated_date' => now(),
                "new_notification_flag" => APConstants::OFF_FLAG
            ));
        }
    }

    /**
     * Click YES to request direct shipping in main table
     */
    public function send() {
        $this->load->model('scans/envelope_properties_m');
        $this->load->model('scans/envelope_shipping_m');
        ci()->load->library('scans/scans_api');
        $this->template->set_layout(FALSE);

        $ids_input = $this->input->get_post('envelope_id');
        if (empty($ids_input) || count($ids_input) == 0) {
            $this->error_output('The list of envelope id is required.');
            return;
        }
        $ids = explode(",", $ids_input);
        $customer_id = APContext::getCustomerCodeLoggedInMailbox($ids);
        
         // check verification postbox
        if ($this->check_postbox_verification($ids)) {
            $this->error_output('You must complete postbox verification of this item first.');
            return;
        }
        
        // Add direct shipping request
        scans_api::insertCompleteItem($ids_input, APConstants::DIRECT_FORWARDING_ORDER_BY_CUSTOMER_ACTIVITY_TYPE, APConstants::TRIGGER_BY_CUSTOMER, $customer_id);

        // Save shipping service
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

        // Save shipping service
        if (!empty($shipping_rate_id)) {
            foreach ($ids as $envelope_id) {
                $this->load->library('shipping/shipping_api');
                // Don't use this data model. Will remove later
                $this->envelope_m->add_shipping_rate($envelope_id, $shipping_rate, $shipping_rate_id);
                // Update shipping information
                shipping_api::saveShippingServiceFee($envelope_id, $shipping_fee, $raw_postal_charge, $raw_customs_handling, $raw_handling_charges, $number_parcel, $shipping_rate_id);
            }
        }

        // #1012 - Prepayment method
        $declare_customs_flag = false;
        foreach ($ids as $id) {
            $envelope = $this->envelope_m->get_by_many(
                    array(
                        'id' => $id,
                        'to_customer_id' => $customer_id
            ));
            if ($envelope && ($envelope->collect_shipping_flag === APConstants::ON_FLAG || $envelope->trash_flag != '' )) {
                continue;
            }
            $postbox_id = $envelope->postbox_id;
            // Check apply custom process
            $check_flag = EnvelopeUtils::check_customs_flag($customer_id, $postbox_id, $id);
            if ($check_flag === APConstants::ON_FLAG) {
                $this->envelope_m->update_by_many(array(
                    "id" => $id,
                    'to_customer_id' => $customer_id
                        ), array(
                    "collect_shipping_flag" => null,
                    "collect_shipping_date" => null
                ));

                mailbox_api::regist_envelope_customs($customer_id, $id, $postbox_id, APConstants::DIRECT_FORWARDING);
                $declare_customs_flag = true;
                //Add declare customer activity by system
                scans_api::insertCompleteItem($envelope_id, APConstants::WAITING_FOR_CUSTOMS_DECLARITON_ACTIVITY_TYPE);
            }
        }

        // Trigger before pre-payment
        if ($declare_customs_flag) {
            $declare_customs_obj = array(
                'declare_customs_flag' => APConstants::ON_FLAG
            );
            $this->success_output('', $declare_customs_obj);
            return;
        }
   
        // Check prepayment with direct shipping type
        $check_prepayment_data = CustomerUtils::checkApplyShippingPrepayment(APConstants::TRIGGER_ACTION_TYPE_CUSTOMER, 
                                                                            APConstants::SHIPPING_SERVICE_NORMAL, 
                                                                            APConstants::SHIPPING_TYPE_DIRECT, $ids, $customer_id);
        
        // If prepayment is true 
        if ($check_prepayment_data['prepayment'] == true) {
            
            // Add direct shipping request to queue
            // Update direct_shipping_flag = 2 (Organe) 
            mailbox_api::requestDirectShippingToQueue($ids_input, $customer_id);
            
            //Insert activity request prepayment by system
            scans_api::insertCompleteItem($ids_input, APConstants::REQUEST_PREPAYMENT_FOR_DIRECT_FORWARDING_BY_SYSTEM_ACTIVITY_TYPE);

            $check_prepayment_data['status'] = FALSE;
            echo json_encode($check_prepayment_data);
            return;
        }
        
        // Request direct shipping is successfull 
        // Update direct_shipping_flag = 0 (yellow)
        // And insert activity:REQUEST_TRACKING_NUMBER = '29'
        // Save address forwarding
        mailbox_api::requestDirectShipping($ids_input, $customer_id);
        scans_api::insertCompleteItem($ids_input, APConstants::REQUEST_TRACKING_NUMBER_ACTIVITY_TYPE);

        $this->success_output('');
    }

    /**
     * Request collect envelope (collect shipment)
     */
    public function collect() {
        ci()->load->library('scans/scans_api');

        $this->template->set_layout(FALSE);
        $ids_input = $this->input->get_post('envelope_id');
        if (empty($ids_input) || count($ids_input) == 0) {
            $this->error_output('The list of envelope id is required.');
            return;
        }

        // get array ids
        $ids = explode(",", $ids_input);
        
        // Get customer's ID
        $customer_id = APContext::getCustomerCodeLoggedInMailbox($ids);
        
        // Add collect shipping request
        scans_api::insertCompleteItem($ids_input, APConstants::MARK_COLLECT_FORWARDING_ORDER_BY_CUSTOMER_ACTIVITY_TYPE, APConstants::TRIGGER_BY_CUSTOMER, $customer_id);
        
        // Request collect shipping is successful
        // Update collect_shipping_flag = 0 (yellow)
        // save address shipping
        $result = mailbox_api::requestCollectShipping($ids_input, $customer_id);
        if ($result['status'] == false) {
            $this->error_output($result['message']);
        } else {
            $this->success_output('');
        }
    }

    /**
     * Un collect forwarding when this shipping still is not triggered
     */
    public function unmark_collect() {
        ci()->load->library('scans/completed_api');
        $this->template->set_layout(FALSE);
        
        $ids_input = json_decode($this->input->get_post('listId'), true);
        
        $ids = APUtils::convertIdsInputToArray($ids_input);
        
        if (empty($ids) || count($ids) == 0) {
            $this->error_output('The list of envelope id is required.');
            return;
        }
        
        foreach ($ids as $id) {
            //Cancel collect shipping request
            completed_api::cancel_collect_shipping_request($id);
        }
        // Get customer's ID
        $customer_id = APContext::getCustomerCodeLoggedInMailbox($ids);
        // Insert un mark activity
        scans_api::insertCompleteItem($ids, APConstants::UN_MARK_COLLECT_FORWARDING_BY_CUSTOMER_ACTIVITY_TYPE, APConstants::TRIGGER_BY_CUSTOMER, $customer_id);
        //Return ajax
        $this->success_output('');
    }

    /**
     * View envelope image.
     */
    public function view_envelope_image() {
        $this->load->library('common/common_api');

        // Does not use layout
        $this->template->set_layout(FALSE);
        $envelope_id = $this->input->get_post('envelope_id');
        $customer_id = APContext::getCustomerCodeLoggedInMailbox($envelope_id);
        $envelope = $this->envelope_m->get_by("id", $envelope_id);
        $preview_file = $this->envelope_file_m->get_by_many_order(
                array(
            "envelope_id" => $envelope_id,
            "customer_id" => $customer_id,
            "type" => '1'
                ), array(
            "updated_date" => "ASC",
            "created_date" => "ASC"
        ));

        if (!empty($preview_file)) {
            $preview_file = common_api::setDynamicPathEnvelopeFile($preview_file);

            // Check if this is first letter
            if (APUtils::endsWith($envelope->envelope_code, '_000')) {
                $preview_file->public_file_name = APContext::getAssetPath() . Settings::get(APConstants::FIRST_ENVELOPE_KEY);
            } else if ($preview_file) {
                APUtils::download_amazon_file($preview_file);
            }
        } else {
            $preview_file = new stdClass();
            $preview_file->public_file_name = "";
            $preview_file->file_name = "";
            $preview_file->local_file_name = "";
        }
        $this->template->set('preview_file', $preview_file)->build('envelope_preview');
    }

    /**
     * Display file scan with envelope id and file type (ENVELOPE or ITEM scan)
     */
    public function get_file_scan() {
        $this->load->library('common/common_api');

        // Does not use layout
        $this->template->set_layout(FALSE);
        $envelope_id = $this->input->get_post('envelope_id');
        $item_type = $this->input->get_post('type');

        // Get envelope information
        $envelope = $this->envelope_m->get_by("id", $envelope_id);
        if (empty($envelope)) {
            log_message(APConstants::LOG_ERROR, "Envelope id: " . $envelope_id . ' does not exist');
            return;
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
            $customer_id = APContext::getCustomerCodeLoggedInMailbox($envelope_id);
            $preview_file = $this->envelope_file_m->get_by_many_order(
                    array(
                "envelope_id" => $envelope_id,
                "customer_id" => $customer_id,
                "type" => $item_type
                    ), array(
                "updated_date" => "ASC",
                "created_date" => "ASC"
            ));
            $preview_file = common_api::setDynamicPathEnvelopeFile($preview_file);
            // Download data from amazon
            APUtils::download_amazon_file($preview_file);

            if (empty($preview_file)) {
                log_message(APConstants::LOG_ERROR, "Mailbox: preview file is fail: envelope_id: " . $envelope_id . ', customer_id:' . $customer_id . ', item type:' . $item_type);
                return;
            }

            // Get local file name
            $local_file_name = $preview_file->local_file_name;
        }
        if (!file_exists($local_file_name)) {
            log_message(APConstants::LOG_ERROR, "Local file name of envelope id: " . $envelope_id . ' does not exist. File name:' . $local_file_name . ' does not exist');
            return;
        }

        // Get extends file
        header('Content-Disposition: inline');
        $ext = substr($local_file_name, strrpos($local_file_name, '.') + 1);
        $ext = strtolower($ext);
        switch ($ext) {
            case 'jpg':
                header('Content-type: image/jpeg');
                break;
            case 'jpge':
                header('Content-type: image/jpeg');
                break;
            case 'png':
                header('Content-type: image/png');
                break;
            case 'tiff':
                header('Content-type: image/tiff');
                break;
            case 'pdf':
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
     * View envelope image.
     */
    public function view_document_image() {
        $this->load->library('common/common_api');

        // Does not use layout
        $this->template->set_layout(FALSE);
        $envelope_id = $this->input->get_post('envelope_id');
        $customer_id = APContext::getCustomerCodeLoggedInMailbox($envelope_id);
        $envelope = $this->envelope_m->get_by("id", $envelope_id);
        $preview_file = $this->envelope_file_m->get_by_many_order(
                array(
            "envelope_id" => $envelope_id,
            "customer_id" => $customer_id,
            "type" => '2'
                ), array(
            "updated_date" => "ASC",
            "created_date" => "ASC"
        ));

        if (!empty($preview_file)) {
            $preview_file = common_api::setDynamicPathEnvelopeFile($preview_file);

            // Check if this is first letter
            if (APUtils::endsWith($envelope->envelope_code, '_000')) {
                $preview_file->public_file_name = APContext::getAssetPath() . Settings::get(APConstants::FIRST_LETTER_KEY);
            } else if ($preview_file) {
                APUtils::download_amazon_file($preview_file);
            }
        } else {
            $preview_file = new stdClass();
            $preview_file->public_file_name = "";
            $preview_file->file_name = "";
            $preview_file->local_file_name = "";
        }
        $this->template->set('preview_file', $preview_file)->build('document_preview');
    }

    /**
     * Save As.
     */
    public function check_file_exist() {
        // Does not use layout
        $this->template->set_layout(FALSE);
        $envelope_id = $this->input->get_post('envelope_id');
        $customer_id = APContext::getCustomerCodeLoggedInMailbox($envelope_id);
        $envelope_file = $this->envelope_file_m->get_by_many_order(
                array(
            "envelope_id" => $envelope_id,
            "customer_id" => $customer_id,
            "type" => '2'
                ), array(
            "updated_date" => "ASC",
            "created_date" => "ASC"
        ));
        $document_file_name = '';
        if ($envelope_file && !empty($envelope_file->local_file_name)) {
            $document_file_name = $envelope_file->local_file_name;
            if (file_exists($document_file_name)) {
                return $this->success_output('1');
            }
        }
        return $this->success_output('0');
    }

    /**
     * Save As.
     */
    public function saveas() {
        // Does not use layout
        $this->template->set_layout(FALSE);
        // $this->load->library('zip');
        $this->load->helper('download');
        $envelope_id = $this->input->get_post('envelope_id');
        $customer_id = APContext::getCustomerCodeLoggedIn();

        /**
         * $envelope_file = $this->envelope_file_m->get_by_many_order(array ( "envelope_id" => $envelope_id, "customer_id" => $customer_id, "type" =>
         * '1' ), array ( "updated_date" => "ASC", "created_date" => "ASC" )); // $default_bucket_name = $this->config->item('default_bucket');
         * $envelope_file_name = ''; if ($envelope_file && ! empty($envelope_file->local_file_name)) { $envelope_file_name =
         * $envelope_file->local_file_name; // Download from S3 // S3::getObject($default_bucket_name, $envelope_file_name, APPPATH .
         * $envelope_file_name); // $this->zip->read_file($envelope_file_name); }
         */
        $envelope_file = $this->envelope_file_m->get_by_many_order(
                array(
            "envelope_id" => $envelope_id,
            "customer_id" => $customer_id,
            "type" => '2'
                ), array(
            "updated_date" => "ASC",
            "created_date" => "ASC"
        ));
        $document_file_name = '';
        if ($envelope_file && !empty($envelope_file->local_file_name)) {
            $document_file_name = $envelope_file->local_file_name;
            if (!file_exists($document_file_name)) {
                APUtils::download_amazon_file($envelope_file);
            }

            // Download from S3
            // S3::getObject($default_bucket_name, $document_file_name, APPPATH . $document_file_name);
            // $this->zip->read_file($document_file_name);
            // Read the file's contents
            $data = file_get_contents($document_file_name);
            $file_name = substr($document_file_name, strrpos($document_file_name, '/') + 1);
            force_download($file_name, $data);
        }

        // Write the zip file to a folder on your server. Name it
        // "my_backup.zip"
        // $this->zip->archive(APPPATH . 'temp/scans_' . $envelope_id . '.zip');
        // Download the file to your desktop. Name it "scans.zip"
        // $this->zip->download('scans_' . $envelope_id . '.zip');
    }

    // Call this method first by visiting
    // http://SITE_URL/example/request_dropbox
    public function request_dropbox() {
        $dropboxV2 = APContext::getDropbox();
        $dropboxV2->get_request_token();
    }

    // This method should not be called directly, it will be called after
    // the user approves your application and dropbox redirects to it
    public function access_dropbox() {
        $access_token = $this->session->userdata('access_token');

        // Save data to dropbox
        // Get dropbox setting of current customer
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $customer_setting = $this->customer_cloud_m->get_by_many(
            array(
                "cloud_id" => APConstants::CLOUD_DROPBOX_CODE,
                "customer_id" => $customer_id
        ));

        $setting = null;
        if (!empty($customer_setting) && !empty($customer_setting->settings)) {
            // Decode cloud setting
            $setting = json_decode($customer_setting->settings, true);
        }

        if (empty($access_token)) {
            redirect('cloud/index');
        }

        $setting['access_token'] = $access_token;
        $this->customer_cloud_m->update_by_many(
            array(
                "cloud_id" => APConstants::CLOUD_DROPBOX_CODE,
                "customer_id" => $customer_id
            ), array(
            "settings" => json_encode($setting)
        ));
        //Add cloud history
        $this->session->set_userdata(APConstants::SESSION_CLOUD_CUSTOMER_KEY, $setting);

        // If customer doest not setting this information, will store it
        if (empty($customer_setting)) {
            $this->customer_cloud_m->insert(
                array(
                    'cloud_id' => APConstants::CLOUD_DROPBOX_CODE,
                    'customer_id' => APContext::getCustomerCodeLoggedIn(),
                    "auto_save_flag" => '0',
                    "settings" => json_encode($setting)
            ));
            redirect('cloud/index');
        }

        redirect('mailbox/index');
    }

    // Once your application is approved you can proceed to load the library
    // with the access token data stored in the session. If you see your account
    // information printed out then you have successfully authenticated with
    // dropbox and can use the library to interact with your account.
    public function cloud_dropbox() {
        $dropboxV2 = APContext::getDropbox();
        if (empty($dropboxV2->getAccessToken())) {
            $this->success_output('login');
            return;
        }
        else {
            // $envelope_id = $this->input->get_post('id');
            $ids_input = $this->input->get_post('id');
            if (!empty($ids_input)) {
                $ids = explode(",", $ids_input);
                foreach ($ids as $envelope_id) {
                    $postbox_id = $this->input->get_post('postbox_id');
                    $customer_id = APContext::getCustomerCodeLoggedIn();

                    $envelope_file = $this->envelope_file_m->get_by_many_order(
                            array(
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

                    // Gets postbox and location name
                    $postbox_location = $this->postbox_m->get_postbox($postbox_id);
                    $subfolder_name = !empty($postbox_location) ? '/' . $postbox_location[0]->location_name : '/';
                    $parent_folder = $dropboxV2->getFolderName();
                    $dropboxV2->create_folder($parent_folder . $subfolder_name);

                    if (!empty($local_file_name)) {
                        // Copy file to physical path before add to dropbox
                        $amazon_relate_path = $envelope_file->amazon_relate_path;
                        $amazon_relate_path_arr = explode('/', $amazon_relate_path);

                        $local_file_name_temp = 'uploads/temp/' . $amazon_relate_path_arr[1];

                        copy($local_file_name, $local_file_name_temp);
                        chmod($local_file_name_temp, 0775);
                        $dropboxV2->add($parent_folder . $subfolder_name, $local_file_name_temp, array(
                            "mode" => "overwrite"
                        ));

                        // Delete temp file
                        unlink($local_file_name_temp);

                        // Update
                        $this->envelope_m->update_by_many(
                                array(
                            "id" => $envelope_id,
                            "to_customer_id" => $customer_id
                                ), array(
                            "sync_cloud_flag" => APConstants::ON_FLAG,
                            "sync_cloud_date" => now(),
                            'last_updated_date' => now()
                        ));
                    } else {
                        $this->success_output('Your request file does not exist or deleted.');
                        return;
                    }
                }
                $this->success_output('Your scan have been saved to in your cloud drive successfully.');
                return;
            }
            $this->success_output('Please request and wait document scan before saved to cloud driver.');
        }
    }

    /**
     * Update new notification flag.
     *
     * @param unknown_type $customer_id
     */
    private function update_new_notification_flag($envelope_id) {
        $customer_id = APContext::getCustomerCodeLoggedInMailbox($envelope_id);
        mailbox_api::update_new_notification_flag($envelope_id, $customer_id);

        // Get all postbox of this customer
        $postboxs = $this->get_all_postbox();

        // Template configuration
        $this->template->set('postboxs', $postboxs);
    }

    /**
     * View envelope image.
     */
    public function change_category_type() {
        // Does not use layout
        $this->template->set_layout(FALSE);
        $envelope_id = $this->input->get_post('envelope_id');
        $customer_id = APContext::getCustomerCodeLoggedInMailbox($envelope_id);
        // Get envelope information
        $envelope = $this->envelope_m->get_by("id", $envelope_id);
        $category_type = $this->input->get_post('category_type');
        $this->envelope_m->update_by_many(
                array(
            "to_customer_id" => $customer_id,
            "id" => $envelope_id
                ), array(
            "category_type" => $category_type
        ));

        // Update to database
        $pdf_content_file = $this->envelope_pdf_content_m->get_by_many(
                array(
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
            $this->envelope_pdf_content_m->update_by_many(
                    array(
                "envelope_id" => $envelope_id,
                "customer_id" => $customer_id
                    ), array(
                "envelope_content" => $envelope_content
            ));
        }

        $this->success_output('');
    }

    /**
     * Shipping function
     */
    public function regist_envelope_customs() {
        $this->template->set_layout(FALSE);
        $ids_input = $this->input->get_post('envelope_id');
        $postbox_id = $this->input->get_post('postbox_id');
        $shipping_type = $this->input->get_post('shipping_type');
        $customer_id = APContext::getCustomerCodeLoggedIn();


        if (empty($ids_input) || count($ids_input) == 0) {
            $this->error_output('The list of envelope id is required.');
            return;
        }

        $result = mailbox_api::regist_envelope_customs($customer_id, $ids_input, $postbox_id, $shipping_type);
        if ($result) {
            $this->success_output('');
        } else {
            $this->error_output('');
        }
    }

    /**
     * Shipping function
     */
    public function declare_customs() {
        $this->template->set_layout(FALSE);

        $this->load->library('price/price_api');
        $this->load->model('settings/countries_m');

        $envelope_id = $this->input->get_post('envelope_id');

        // Get envelope information
        $envelope = $this->envelope_m->get($envelope_id);
        $customer_id = APContext::getCustomerCodeLoggedInMailbox($envelope_id);
        $phone_number = EnvelopeUtils::get_phone_number($customer_id, $envelope);

        $shipping_type = 1;
        $package_id = $envelope->package_id;
        if ($envelope->collect_shipping_flag == "0") {
            $shipping_type = 2;

            // Gets all envelope on this package
            $envelope_custom = $this->envelope_customs_m->get_by_many(array("envelope_id" => $envelope_id));
            $envelope_customs = $this->envelope_customs_m->get_many_by_many(array(
                "package_id" => $envelope_custom->package_id
            ));

            $list_envelope_id = array();
            foreach ($envelope_customs as $ec) {
                $list_envelope_id[] = $ec->envelope_id;
            }
            $envelopes = $this->envelope_m->get_many_by_many(array(
                "id in (" . implode(',', $list_envelope_id) . ")" => null
            ));

            $weight = 0;
            foreach ($envelopes as $e) {
                $weight += $e->weight;
            }
            $this->template->set('weight', $weight);

            // display the number if there is one collect forwarding item.
            if (count($envelopes) == 1) {
                $shipping_type = 1;
            }
        }

        $postbox = $this->postbox_m->get($envelope->postbox_id);

        // gets pricing template
        $location_id = $postbox->location_available_id;
        // $pricing_map = price_api::getPricingModelByPostboxID($envelope->postbox_id, $postbox->type);
        $pricing_maps = price_api::getPricingModelByCusotomerAndLocationID($customer_id, $location_id);
        $pricing_map = $pricing_maps[$postbox->type];

        $this->template->set('phone_number', $phone_number);
        $this->template->set("pricing_map", $pricing_map);

        // get countries
        $countries = $this->countries_m->get_all();
        $this->template->set("countries", $countries);

        // Display the current page
        $this->template->set('envelope_id', $envelope_id);
        $this->template->set('package_id', $package_id);
        $this->template->set('envelope', $envelope);
        $this->template->set("shipping_type", $shipping_type);
        $this->template->build('mailbox/declare_customs');
    }

    /**
     * Shipping function
     */
    public function confirm_customs_declare() {
        $this->load->model('addresses/customers_address_m');
        $this->load->model('settings/countries_m');
        $this->template->set_layout(FALSE);
        $customer_id = APContext::getCustomerCodeLoggedIn();

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

    /**
     * Shipping function
     */
    public function save_declare_customs() {
        $this->template->set_layout(FALSE);
        $envelope_id = $this->input->get_post('envelope_id');
        $customs_data = $this->input->get_post('customs_data');
        $declare_customs = json_decode($customs_data);
        $customer_id = APContext::getCustomerCodeLoggedInMailbox($envelope_id);

        $phone_number = $this->input->get_post("phone_number");

        mailbox_api::save_declare_customs($customer_id, $envelope_id, $phone_number, $declare_customs);

        // Display the current page
        $this->template->set('envelope_id', $envelope_id);
        $this->success_output('');
    }

    /**
     * Load envelope customs
     */
    public function load_declare_customs() {
        $this->template->set_layout(FALSE);
        // update limit into user_paging.
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();

        // Get paging input
        $input_paging = $this->get_paging_input();
        $input_paging['limit'] = $limit;

        // Process output data
        $total = 100;

        // Get output response
        $response = $this->get_paging_output($total, $total, $input_paging['page']);
        for ($i = 0; $i < $total; $i++) {
            $response->rows[$i]['id'] = $i + 1;
            $response->rows[$i]['cell'] = array(
                $i + 1,
                '',
                '',
                ''
            );
        }
        echo json_encode($response);
    }

    /**
     * Request mark invoice envelop to send item scan to email interface in main item table
     */
    public function mark_invoice_envelope() {
        // Set template layout
        $this->template->set_layout(FALSE);

        // Get ids array 
        $ids = json_decode($this->input->get_post('listId'), true);
        
        // Get customer's ID
        $customer_id = APContext::getCustomerCodeLoggedInMailbox($ids);

        // And insert activity mark send scan item to email interface
        scans_api::insertCompleteItem($ids, APConstants::MARK_SEND_SCAN_ITEM_TO_EMAIL_INTERFACE_BY_CUSTOMER_ACTIVITY_TYPE, APConstants::TRIGGER_BY_CUSTOMER, $customer_id);
        
        //Get list envelopes does not request item scan yet
        $envelope_infos = $this->envelope_m->get_many_by_many(array(
            'id IN (' . implode(',', $ids) .')' => null,
            'item_scan_flag IS NULL' => null,
            'item_scan_date IS NULL' => null
        ));
        
        //Trigger item scan request for envelopes does not request item scan
        if ( !empty($envelope_infos) ) {
            $list_envelope_not_request_scan = array_column($envelope_infos, 'id');
            // Join array elements with a string
            $ids_input = APUtils::convertIdsInputToString($list_envelope_not_request_scan);
           
            //Add request scan item by system
            scans_api::insertCompleteItem($ids_input, APConstants::SCAN_ITEM_ORDER_BY_SYSTEM_ACTIVITY_TYPE);

            // Check prepayment data 
            $check_prepayment_data = CustomerUtils::checkApplyScanPrepayment(APConstants::TRIGGER_ACTION_TYPE_CUSTOMER, APConstants::ITEM_SCAN_TYPE, $list_envelope_not_request_scan, $customer_id);

            // If prepayment is true
            if ($check_prepayment_data['prepayment'] == true) {
                // Add item scan request to queue
                mailbox_api::requestItemScanToQueue($ids_input, $customer_id);

                //Insert activity request prepayment by system
                scans_api::insertCompleteItem($ids_input, APConstants::REQUEST_PREPAYMENT_FOR_SCAN_ITEM_BY_SYSTEM_ACTIVITY_TYPE);

            } else {
                // Item scan request is successful
                // Update item_scan_flag = 0 (yellow) 
                mailbox_api::requestItemScan($ids_input, $customer_id);

            }// end if-else
        }
        
        //Update invoice flag
        $this->envelope_m->update_many($ids, array('invoice_flag' => APConstants::ON_FLAG));

        //Return ajax
        $this->success_output('Update invoice flag successfully.');
        
        
    }

    
    /**
     * Trigger collect shipment by customer
     * @return boolean
     */
    public function collect_shipment() {
        ini_set('max_execution_time', 2400);
        $this->load->library('scans/scans_api');

        $this->template->set_layout(FALSE);

        $includeAllStoreFlag = $this->input->get_post('includeAllStoreFlag');

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

        $postbox_id = $this->input->get_post('p');

        // validate collect shippment on "all" folder
        if ($postbox_id == 0) {
            $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
            $list_user_id = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($parent_customer_id);
            $list_user_id[] = $parent_customer_id;
            $check_list_envelope_collective = $this->envelope_m->group_by('postbox_id')->get_many_by_many(array(
                "to_customer_id IN (" . implode(",", $list_user_id) . ")" => null,
                "collect_shipping_flag" => APConstants::OFF_FLAG,
                "(package_id IS NULL OR package_id = 0)" => null
            ));

            if (count($check_list_envelope_collective) == 0) {
                $this->success_output(lang('collect_shipment_warning'));
                return false;
            } else if (count($check_list_envelope_collective) > 1) {
                $this->error_output("There are many items on different postboxes. We only support to request collect forwarding items of one postbox.");
                return false;
            } else {
                $postbox_id = $check_list_envelope_collective[0]->postbox_id;
                $customer_id = $check_list_envelope_collective[0]->to_customer_id;
            }
        } else {
            $customer_id = APContext::getCustomerCodeLoggedInMailboxByPostbox($postbox_id);
        }

        // Make collect shipment request for all storage items
        if ($includeAllStoreFlag == '1') {
            scans_api::makeCollectShipment($postbox_id);
        }

        // Get all customer have envelope item marked request collect
        $listCollectiveItems = scans_api::getListCollectiveShippingItems($customer_id, $postbox_id);
        if (empty($listCollectiveItems)) {
            $this->success_output(lang('collect_shipment_warning'));
            return false;
        }

        $number_item = count($listCollectiveItems);
        if ($number_item == 0) {
            $this->success_output(lang('collect_shipment_warning'));
            return false;
        }
        
        $list_id = array();
        //Get list collective ids
        foreach ($listCollectiveItems as $item) {
            $list_id[] = $item->id;
        }
        
        // check verification postbox
        if ($this->check_postbox_verification($list_id)) {
            $this->error_output('You must complete postbox verification of this item first.');
            return;
        }
        
        //Log activity customer trigger collect shipment
        scans_api::insertCompleteItem($list_id, APConstants::TRIGGER_ITEM_COLLECT_FORWARDING_BY_CUSTOMER_ACTIVITY_TYPE, APConstants::TRIGGER_BY_CUSTOMER, $customer_id);

        // A collect shipment with only one package should turn into a direct shipment and should also be calculated as a direct shipment
        if ($number_item == 1) {
            $message = 'Number collect shipmment = 1. Change from collect to direct. {envelope_id:' . $listCollectiveItems[0]->id . '}';
            log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'collect_shipment');
            
            // Add direct shipping request by system
            scans_api::insertCompleteItem($listCollectiveItems[0]->id, APConstants::DIRECT_FORWARDING_ORDER_BY_SYSTEM_ACTIVITY_TYPE);

            // Trigger declare customs before prepayment
            $check_flag = EnvelopeUtils::check_customs_flag($customer_id, $postbox_id, $listCollectiveItems[0]->id);
            $declare_customs_obj = array(
                'declare_customs_flag' => $check_flag
            );

            // Reset collect flag
            $this->envelope_m->update_by_many(array(
                "id" => $listCollectiveItems[0]->id,
                "to_customer_id" => $customer_id
                    ), array(
                'collect_shipping_flag' => null,
                'direct_shipping_flag' => null,
                'last_updated_date' => now(),
                'collect_shipping_date' => null
            ));

            if ($check_flag === APConstants::ON_FLAG) {
                mailbox_api::regist_envelope_customs($customer_id, $listCollectiveItems[0]->id, $postbox_id, APConstants::DIRECT_FORWARDING);
                //Waiting declare custom activity
                scans_api::insertCompleteItem($listCollectiveItems[0]->id, APConstants::WAITING_FOR_CUSTOMS_DECLARITON_ACTIVITY_TYPE);
                $this->success_output(lang('collect_shipment_success'), $declare_customs_obj);
                return;
            }
            
            // Trigger pre-payment
            $check_prepayment_data = CustomerUtils::checkApplyShippingPrepayment(APConstants::TRIGGER_ACTION_TYPE_CUSTOMER, 
                                                                                APConstants::SHIPPING_SERVICE_NORMAL, 
                                                                                APConstants::SHIPPING_TYPE_DIRECT, 
                                                                                array($listCollectiveItems[0]->id), $customer_id);
            
            // If prepayment is true
            if ($check_prepayment_data['prepayment'] == true) {
                $message = 'Required prepayment for DIRECT SHIPPING. {envelope_id:' . $listCollectiveItems[0]->id . '}';
                log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'collect_shipment');

                // Add direct shipping request to queue
                // Update direct_shipping_flag = 2 (Organe) 
                mailbox_api::requestDirectShippingToQueue($listCollectiveItems[0]->id, $customer_id);
                
                //Insert activity need prepayment
                scans_api::insertCompleteItem($listCollectiveItems[0]->id, APConstants::REQUEST_PREPAYMENT_FOR_DIRECT_FORWARDING_BY_SYSTEM_ACTIVITY_TYPE);

                $check_prepayment_data['status'] = FALSE;
                echo json_encode($check_prepayment_data);
                return;
            }
            
            // Request direct shipping is successfull 
            // Update direct_shipping_flag = 0 (yellow)
            // And insert activity:REQUEST_TRACKING_NUMBER = '29'
            // Save address forwarding
            mailbox_api::requestDirectShipping($listCollectiveItems[0]->id, $customer_id);
            scans_api::insertCompleteItem($listCollectiveItems[0]->id, APConstants::REQUEST_TRACKING_NUMBER_ACTIVITY_TYPE);

            // Don't use this data model. Will remove later
            $this->envelope_m->add_shipping_rate($listCollectiveItems[0]->id, $shipping_rate, $shipping_rate_id);

            // Update shipping information
            $this->load->library('shipping/shipping_api');
            shipping_api::saveShippingServiceFee($listCollectiveItems[0]->id, $shipping_fee, $raw_postal_charge, $raw_customs_handling, $raw_handling_charges, $number_parcel, $shipping_rate_id);
            
            $this->success_output(lang('collect_shipment_success'), $declare_customs_obj);
            return;
        }
        
        

        //Validate weight for shipping and update shipping information
        $this->load->library('shipping/shipping_api');
        foreach ($listCollectiveItems as $item) {

            // Validate weight
            $validWeight = true;
            if (empty($shipping_rate_id)) {
                $validWeight = shipping_api::checkValidCollectItem($item->id);
            } else {
                $validWeight = shipping_api::checkValidCollectItemByShippingService($item->id, $shipping_rate_id);
            }
            if (!$validWeight) {
                $message = lang('collect_shipment_over68_warning');
                $this->error_output($message);
                return;
            }

            // Update shipping rate
            // Don't use this data model. Will remove later
            $this->envelope_m->add_shipping_rate($item->id, $shipping_rate, $shipping_rate_id);
            shipping_api::saveShippingServiceFee($item->id, $shipping_fee, $raw_postal_charge, $raw_customs_handling, $raw_handling_charges, $number_parcel, $shipping_rate_id);
            shipping_api::saveShippingAddress($item->id, $item->shipping_address_id);
        }

        $location_available_id = $listCollectiveItems[0]->location_available_id;
        $package_id = EnvelopeUtils::get_customs_package_id($listCollectiveItems[0]->id);
        if (empty($package_id)) {
            $package_id = scans_api::createCollectiveShippingPackage($customer_id, $location_available_id);
        }
        
        // Apply customs procedure
        $declare_customs_flag = EnvelopeUtils::apply_collect_customs_process($customer_id, $postbox_id, $package_id);
        if ($declare_customs_flag == APConstants::ON_FLAG) {
            scans_api::insertCompleteItem($list_id, APConstants::WAITING_FOR_CUSTOMS_DECLARITON_ACTIVITY_TYPE);
            $declare_customs_obj = array(
                'declare_customs_flag' => $declare_customs_flag
            );
            $this->success_output('', $declare_customs_obj);
            return;
        }

        // Check prepayment
        $check_prepayment_data = CustomerUtils::checkApplyShippingPrepayment(APConstants::TRIGGER_ACTION_TYPE_CUSTOMER, 
                                                                            APConstants::SHIPPING_SERVICE_NORMAL, 
                                                                            APConstants::SHIPPING_TYPE_COLLECT, $list_id, $customer_id);
        
        //if prepayment is true 
        if ($check_prepayment_data['prepayment'] == true) {
            
            // from array to string 
            $list_envelope_id_str = implode(',', $list_id);
            
            // Add collect shipping request to queue
            // collect_shipping_flag = 2(Organe)
            mailbox_api::requestCollectShippingToQueue($list_envelope_id_str, $customer_id);
            
            //Insert activity request prepayment
            scans_api::insertCompleteItem($list_envelope_id_str, APConstants::REQUEST_PREPAYMENT_FOR_COLLECT_FORWARDING_BY_SYSTEM_ACTIVITY_TYPE);
            
            $check_prepayment_data['status'] = FALSE;
            $check_prepayment_data['list_envelope_id'] = $list_envelope_id_str;
            echo json_encode($check_prepayment_data);
            return;
        }

        $declare_customs_obj = scans_api::updatePackageIDForAllCollectiveShippingItems($customer_id, $location_available_id, $package_id, $postbox_id);
        if ($declare_customs_obj['declare_customs_flag'] != APConstants::ON_FLAG) {
            //Request tracking number
            scans_api::insertCompleteItem($list_id, APConstants::REQUEST_TRACKING_NUMBER_ACTIVITY_TYPE);
        }

        $this->success_output(lang('collect_shipment_success'), $declare_customs_obj);
    }

    /**
     * Update hide panes setttings.
     */
    public function update_hide_panes() {
        // Gets input
        $hide_flag = $this->input->post("hide", "0");

        // update hide panes setting.
        if (APContext::getCustomerCodeLoggedIn()) {
            APContext::updateHidePanesSetting($hide_flag);
        }

        $this->success_output(" ");
    }

    public function save_shipping_address() {
        $this->template->set_layout(false);

        if ($this->is_ajax_request()) {
            $this->load->library('scans/scans_api');
            $this->load->library('shipping/shipping_api');
            $shipping_address_id = (int) $this->input->get_post('shipping_address_id');
            $envelope_id = (int) $this->input->get_post('envelope_id');

            $include_all_flag = $this->input->get_post('include_all_flag', '0');
            $green_flag = $this->input->get_post('green_flag', '0');
            $customer_id = APContext::getCustomerCodeLoggedInMailbox($envelope_id);

            scans_api::saveShippingAddress($shipping_address_id, $envelope_id, $customer_id, $include_all_flag, $green_flag);
            $this->success_output("");
        }
    }

    /*
     * For collective shipment whether location is same or not
     */

    function checkEnvelopLocationConstrain($envelope_id) {
        $locationArray = array();
        foreach ($envelope_id as $e_id) {
            $envelope = $this->envelope_m->get_by_many(array(
                'id' => $e_id
            ));
            $location = addresses_api::getLocationByID($envelope->location_id);
            if (!empty($location)) {
                if (!in_array($location->id, $locationArray, true)) {
                    array_push($locationArray, $location->id);
                }
            }
        }
        return $locationArray;
    }

    /**
     * Caculate shipping rate by envelope ID and shipping type.
     *
     * @param
     *            shipping_type (1: Direct forwarding | 2: Collect forwarding)
     */
    public function calculate_all_shipping() {
        $this->template->set_layout(FALSE);
        ci()->load->library('shipping/ShippingConfigs');
        $this->load->library('shipping/shipping_api');
        $this->load->library('addresses/addresses_api');
        $this->load->library('settings/settings_api');
        $this->load->helper('info/functions');
        $this->lang->load('shipping/shipping');

        $list_envelope_id = $this->input->get_post("envelope_id", "0");
        $shipping_type = $this->input->get_post("shipping_type", "0");
        $postbox_id = $this->input->get_post("postbox_id", "0");
        $envelope_ids = explode(',', $list_envelope_id);
        // Gets included all flag
        $included_all_flag = $this->input->get_post("included_all_flag", "0");
        $customer_id = APContext::getCustomerCodeLoggedInMailbox($envelope_ids[0]);
        $list_envelopes = array();
        if ($shipping_type == ShippingConfigs::DIRECT_SHIPPING) {
            $list_envelopes = $this->envelope_m->get_many_by_many(array(
                'id' => $envelope_ids[0],
                "to_customer_id" => $customer_id
            ));
        }
        // Collect
        else {
            $list_envelope_id = array();
            // if customer click included all item.
            if ($included_all_flag == APConstants::ON_FLAG) {
                $list_envelopes = $this->envelope_m->get_many_by_many(array(
                    "postbox_id" => $postbox_id,
                    "to_customer_id" => $customer_id,
                    "( (storage_flag =1 AND current_storage_charge_fee_day > 0  AND collect_shipping_flag <> '1'  
                            AND collect_shipping_flag <> '2' AND direct_shipping_flag <> '1' AND direct_shipping_flag <> '2') 
                        OR (direct_shipping_flag IS NULL AND collect_shipping_flag IS NULL))" => null,
                    "trash_flag IS NULL" => null
                ));
            } else {
                $list_envelopes = $this->envelope_m->getAllReadyCollectItems($customer_id, $postbox_id);
            }
        }

        if (empty($list_envelopes) || count($list_envelopes) == 0) {
            $this->error_output("", lang("could_not_calculate_shipping_fedex_message"));
            return;
        }

        // Get customer address
        if (count($envelope_ids) == 0) {
            $envelope = $list_envelopes[0];
            $envelope_id = $envelope->id;
        } else {
            $envelope_id = $envelope_ids[0];
        }
        $customers_address = shipping_api::getShippingAddressByEnvelope($customer_id, $envelope_id);

        // Call api to get rates
        try {
            $cost_object = shipping_api::calculateCostOfAllServices($customer_id, $list_envelopes, $envelope_id, $shipping_type);
            if ($cost_object == null) {
                $this->error_output("", lang('could_not_calculate_shipping_fedex_message'));
                return;
            }
            $data = $cost_object['data'];
            $listShippingServices = $cost_object['listShippingServices'];

            $html = $this->load->view('mailbox/calculate_all_shipping', array(
                'listShippingServices' => $listShippingServices,
                'shipping_type' => $shipping_type,
                'allServicesRates' => $data,
                'envelope_id' => $envelope_ids,
                'customers_address' => $customers_address,
                "list_envelope_id" => $this->input->get_post("envelope_id", "0"),
                "shipping_type" => $shipping_type,
                "postbox_id" => $postbox_id,
                'included_all_flag' => $included_all_flag,
                'target_envelope_id' => $envelope_id
                    ), true);
            $this->success_output("", $html);
            return;
        } catch (Exception $ex) {
            $this->error_output("", $ex->getMessage());
            return;
        }
    }

    /**
     * Callback From: check_company()
     *
     * @param string $email
     *            The Email address to validate
     * @return bool
     */
    public function _check_shipment_company($shipment_company_name) {
        $shipment_address_name = $this->input->get_post('shipment_address_name');
        if (empty($shipment_address_name) && empty($shipment_company_name)) {
            $this->form_validation->set_message('_check_shipment_company', lang('addresses.shipment_company_required'));
            return false;
        }
        return true;
    }

    public function update_session_mobile_adv_popup() {
        $flag = $this->input->get_post('flag', '0');
        $this->session->set_userdata(APConstants::SESSION_SHOW_MOBILE_ADV_FIRST_LOGIN, $flag);

        $this->success_output('');
        return;
    }

    /**
     * Display all item in collect shipment
     */
    public function display_all_collect_item() {
        $this->template->set_layout(FALSE);
        $package_id = $this->input->get_post('package_id', '0');
        $this->template->set('package_id', $package_id);
        $this->template->build('mailbox/display_all_collect_item');
    }

    /**
     * Display all item in collect shipment
     */
    public function search_all_collect_item() {
        $this->template->set_layout(FALSE);
        $package_id = $this->input->get_post('package_id', '0');
        $envelope_id = $this->input->get_post('envelope_id', '0');

        $customer_id = APContext::getCustomerCodeLoggedInMailbox($envelope_id);

        if (empty($package_id)) {
            // Gets all envelopes from envelope_custom
            $envelope = $this->envelope_customs_m->get_by('envelope_id', $envelope_id);
            $package_id = $envelope->package_id;

            $envelope_customs = $this->envelope_customs_m->get_many_by('package_id', $package_id);
            $list_envelope_id = array();
            foreach ($envelope_customs as $c) {
                $list_envelope_id[] = $c->envelope_id;
            }

            $array_condition = array(
                "to_customer_id" => $customer_id,
                "envelopes.id IN (" . implode(',', $list_envelope_id) . ")" => null
            );
        } else {
            $array_condition = array(
                "to_customer_id" => $customer_id,
                'package_id' => $package_id
            );
        }

        // update limit into user_paging.
        $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : '10';
        APContext::updateAdminPagingSetting($limit);

        // Get paging input
        $input_paging = $this->get_paging_input();
        $input_paging ['limit'] = $limit;

        // Call search method
        $query_result = scans_api::getEnvelopePagingInPrepareShippingPopup($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);

        // Process output data
        $total = $query_result['total'];
        $rows = $query_result['data'];

        // Get output response
        $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);

        $i = 0;
        foreach ($rows as $row) {
            $response->rows[$i]['id'] = $row->id;
            $response->rows[$i]['cell'] = array(
                $row->from_customer_name,
                Settings::get_label(APConstants::ENVELOPE_TYPE_CODE, $row->envelope_type_id),
                number_format($row->weight, 0) . $row->weight_unit,
                APUtils::viewDateFormat($row->incomming_date, 'd.m.Y')
            );
            $i++;
        }

        echo json_encode($response);
    }

    /**
     * Send email to accounting postbox email with item PDF scan
     */
    public function send_accounting_email() {

        //Array item will be sent scan item
        $envelope_ids = json_decode($this->input->get_post('listId'), true);

        //Get scan item PDF file of envelopes
        $attachments = EnvelopeUtils::get_item_scan_of_envelope($envelope_ids);

        //Get list envelope codes of postbox have PDF file
        $item_codes = array();
        $envelopes = $this->envelope_m->select('id, envelope_code')->where_in('id', array_keys($attachments))->get_all();
        if (!empty($envelopes)) {
            foreach ($envelopes as $envelope) {
                $item_codes[] = $envelope->envelope_code;
            }
        }

        $mail_data = array(
            'slug' => APConstants::accounting_invoice_email,
            'to_email' => $this->input->get_post('email'),
            'attachments' => $attachments,
            'full_name' => APContext::getCustomerLoggedIn()->user_name,
            'items' => implode(', ', $item_codes)
        );

        if (MailUtils::sendEmailByTemplate($mail_data)) {
            //Update sent item date
            $this->envelope_m->update_many(array_keys($attachments), array('invoice_date' => now(), 'invoice_flag' => APConstants::ON_FLAG));
            //Update status send email = 1 in mailqueues
            $this->load->model('email/email_queue_m');
            $where = array(
                'send_date >=' => strtotime('-24 hours'),
                'send_date <=' => now(),
                'status' => 0, //Email waiting to send,
                'to_email' => $this->input->get_post('email'),
                'slug' => APConstants::accounting_invoice_email
            );
            $email_queues = $this->email_queue_m->get_many_by_many($where);
            $email_queue_ids = array();
            if (!empty($email_queues)) {
                foreach ($email_queues as $email_queue) {
                    $item_id = APUtils::get_json_by_key($email_queue->data, 'item_id');
                    if (in_array($item_id, $envelope_ids)) {
                        $email_queue_ids[] = $email_queue->id;
                    }
                }
            }
            //Update send mail status = 1
            if (!empty($email_queue_ids)) {
                $this->email_queue_m->update_many($email_queue_ids, array('status' => 1));
            }
            $this->success_output('A email already has been sent to your accounting email. Please check your accounting email for detail.');
        } else {
            $this->error_output('Error while sending email to your accounting email. Please contact to admin.');
        };
    }

    /**
     * check verification postbox
     * @param type $envelope_ids
     */
    private function check_postbox_verification($envelope_ids) {
        if (empty($envelope_ids)) {
            return true;
        }

        $postboxes = $this->envelope_m->select("postbox_id, to_customer_id")->group_by("to_customer_id, postbox_id")->get_many_by_many(array(
            "id IN (" . implode(',', $envelope_ids) . ")" => null
        ));

        foreach ($postboxes as $p) {
            if (CaseUtils::isVerifiedPostboxAddress($p->postbox_id, $p->to_customer_id)) {
                // need verification case this postbox
                return false;

            }
        }

        // no need verification.
        return true;
    }

    /**
     * callback_url to verify access token
     */
    public function dropbox_authorization_callback(){
        $dropboxV2 = APContext::getDropbox();
        $accessToken = $dropboxV2->verify_callback();

        $this->session->set_userdata('access_token', $accessToken);
        redirect(site_url("mailbox/access_dropbox"));
    }

}
