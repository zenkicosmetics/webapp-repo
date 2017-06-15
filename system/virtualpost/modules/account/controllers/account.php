<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class account extends AccountSetting_Controller
{
    /**
     * Validation for basic profile data. The rest of the validation is built by streams.
     *
     * @var array
     */
    private $change_email_validation_rules = array(
        array(
            'field' => 'email',
            'label' => 'lang:email',
            'rules' => 'required|valid_email|max_length[255]|callback__check_email'
        ),
        array(
            'field' => 'current_password',
            'label' => 'lang:password',
            'rules' => 'required|trim|max_length[255]|min_length[6]'
        )
    );

    /**
     * Validation for basic profile data. The rest of the validation is built by streams.
     *
     * @var array
     */
    private $change_pass_validation_rules = array(
        array(
            'field' => 'current_password',
            'label' => 'lang:current_password',
            'rules' => 'required|trim|max_length[255]|min_length[6]'
        ),
        array(
            'field' => 'password',
            'label' => 'lang:password',
            'rules' => 'required|trim|matches[repeat_password]|max_length[255]|min_length[6]'
        ),
        array(
            'field' => 'repeat_password',
            'label' => 'lang:repeat_password',
            'rules' => 'required|trim|max_length[255]'
        )
    );

    /**
     * Validation for basic profile data. The rest of the validation is built by streams.
     *
     * @var array
     */
    private $change_account_type_validation_rules = array(
        array(
            'field' => 'account_type',
            'label' => 'lang:account_type',
            'rules' => 'required'
        )
    );

    /**
     * Validation for basic profile data. The rest of the validation is built by streams.
     *
     * @var array
     */
    private $add_postbox_validation_rules = array(
        array(
            'field' => 'postname',
            'label' => 'lang:postname',
            'rules' => 'trim|required|validname|max_length[255]'
        ),
        array(
            'field' => 'custname',
            'label' => 'lang:custname',
            'rules' => 'validname|max_length[255]'
        ),
        array(
            'field' => 'company',
            'label' => 'lang:company',
            'rules' => 'validname|max_length[255]|callback__check_company'
        )
    );

    /**
     * Validation for basic profile data. The rest of the validation is built by streams.
     *
     * @var array
     */
    private $postbox_validation_rules = array(
        array(
            'field' => 'postbox_name',
            'label' => 'lang:postname',
            'rules' => 'required'
        )
    );

    /**
     * Validation for basic profile data.
     * The rest of the validation is built by streams.
     *
     * @var array
     */
    private $invoice_address_rules = array(
        array(
            'field' => 'invoicing_address_name',
            'label' => 'lang:invoicing_address_name',
            'rules' => 'validname'
        ),
        array(
            'field' => 'invoicing_company',
            'label' => 'lang:invoicing_company',
            'rules' => 'validname|callback__check_invoicing_company'
        ),
        array(
            'field' => 'invoicing_street',
            'label' => 'lang:invoicing_street',
            'rules' => 'required'
        ),
        array(
            'field' => 'invoicing_postcode',
            'label' => 'lang:invoicing_postcode',
            'rules' => 'required|postcode'
        ),
        array(
            'field' => 'invoicing_city',
            'label' => 'lang:invoicing_city',
            'rules' => 'required|validname'
        ),
        array(
            'field' => 'invoicing_region',
            'label' => 'lang:invoicing_region',
            'rules' => 'required'
        ),
        array(
            'field' => 'invoicing_country',
            'label' => 'lang:invoicing_country',
            'rules' => 'required'
        ),
        array(
            'field' => 'invoicing_phone_number',
            'label' => 'lang:invoicing_phone_number',
            'rules' => 'phong_number'
        )
    );

    /**
     *forwariding address rule
     * @var type
     */
    private $fowarding_address_rules = array(
        array(
            'field' => 'shipment_address_name',
            'label' => 'lang:shipment_address_name',
            'rules' => 'validname'
        ),
        array(
            'field' => 'shipment_company',
            'label' => 'lang:shipment_company',
            'rules' => 'validname|callback__check_shipment_company'
        ),
        array(
            'field' => 'shipment_street',
            'label' => 'lang:shipment_street',
            'rules' => 'required'
        ),
        array(
            'field' => 'shipment_postcode',
            'label' => 'lang:shipment_postcode',
            'rules' => 'required|postcode'
        ),
        array(
            'field' => 'shipment_city',
            'label' => 'lang:shipment_city',
            'rules' => 'required|validname'
        ),
        array(
            'field' => 'shipment_region',
            'label' => 'lang:shipment_region',
            'rules' => ''
        ),
        array(
            'field' => 'shipment_country',
            'label' => 'lang:shipment_country',
            'rules' => 'required'
        ),
        array(
            'field' => 'shipment_phone_number',
            'label' => 'lang:shipment_phone_number',
            'rules' => 'phong_number'
        ),
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

        // load the theme_example view
        $this->load->model('customers/customer_m');
        $this->load->model('settings/countries_m');
        $this->load->model('price/pricing_m');
        $this->load->model('mailbox/postbox_m');
        $this->load->model('mailbox/postbox_setting_m');
        $this->load->model('mailbox/postbox_history_activity_m');
        $this->load->model('account/account_m');
        $this->load->model('addresses/location_m');
        $this->load->model('addresses/customer_location_m');
        $this->load->model('addresses/customers_address_m');
        $this->load->model('settings/currencies_m');

        ci()->load->library(array(
            'account/account_api',
            'invoices/Invoices',
            'price/price_api',
            'shipping/shipping_api'
        ));

        $this->lang->load('account');
        $this->lang->load('addresses/address');
        $this->lang->load('account/account_setting');
        $this->lang->load('customers/customer');
        $this->load->model('email/email_m');
    }

    /**
     * Index Page for this controller. Maps to the following URL http://example.com/index.php/welcome - or -
     * http://example.com/index.php/welcome/index - or - Since this controller is set as the default controller in config/routes.php, it's displayed
     * at http://example.com/ So any other public methods not prefixed with an underscore will map to /index.php/welcome/<method_name>
     *
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index()
    {
        // load customer information
        $this->load_customer_info();
        $this->load_postbox_info();
        $this->load_price_info();
        $this->load_address();

        $customer_id = APContext::getCustomerCodeLoggedIn();

        // load the theme_example view
        // 581: add postbox first location.
        $postbox = $this->postbox_m->getFirstLocationBy($customer_id);

        // #678 multi currency
        $currencies = $this->currencies_m->get_many_by_many(array(
            'active_flag' => APConstants::ON_FLAG
        ), 'currency_id, currency_short');
        $selected_currency = $this->customer_m->get_standard_setting_currency($customer_id);
        $decimal_separator = $this->customer_m->get_standard_setting_decimal_separator($customer_id);
        $selected_currency_id = $selected_currency->currency_id;
        $language = CustomerProductSetting::get($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'language');

        $customer = $this->customer_m->get_current_customer_info();
        $this->template->set('customer', $customer);

        // Get all countries
        $countries = $this->countries_m->get_many_by_many(array(), '', false, array(
            'country_name' => 'ASC'
        ));

        // Display the current page
        $this->template->set('countries', $countries);

        // init term & condition.
        $api_access = array(
            'app_code' => '',
            'app_key' => ''
        );
        if(APContext::isPrimaryCustomerUser()){
            account_api::initEnterpriseTermAndCondition($customer_id);
            $api_access = account_api::initAPIAccess($customer_id);
        }

        $this->load->model('settings/language_code_m');
        $languages = $this->language_code_m->get_many_by_many(array('status' => APConstants::ON_FLAG));

        $this->template->set('languages', $languages);
        $this->template->set('language', $language);
        $this->template->set('postbox', $postbox);
        $this->template->set('api_access', $api_access);
        $this->template->set('currencies', $currencies);
        $this->template->set('selected_currency_id', $selected_currency_id);
        $this->template->set('decimal_separator', $decimal_separator);
        $this->template->append_metadata($this->load->view('fragments/wysiwyg', array (), TRUE));
        $this->template->build('index');
    }

    /**
     * Load all address information.
     */
    private function load_address() {
        // Gets customerid logged in.
        $customer_id = APContext::getCustomerCodeLoggedIn();

        $address = new stdClass();
        $address = $this->customers_address_m->get_by('customer_id', $customer_id);
        $this->template->set("address", $address);
    }

    /**
     * Postbox settings
     */
    public function postbox_setting()
    {
        // load customer information
        $this->load_customer_info();
        $this->load_postbox_info();
        $this->load_price_info();
        $this->load_address();

        $customer_id = APContext::getCustomerCodeLoggedIn();

        // load the theme_example view
        // 581: add postbox first location.
        $postbox = $this->postbox_m->getFirstLocationBy($customer_id);

        // #678 multi currency
        $currencies = $this->currencies_m->get_many_by_many(array(
            'active_flag' => APConstants::ON_FLAG
        ), 'currency_id, currency_short');
        $selected_currency = $this->customer_m->get_standard_setting_currency($customer_id);
        $decimal_separator = $this->customer_m->get_standard_setting_decimal_separator($customer_id);
        $selected_currency_id = $selected_currency->currency_id;

        // Get all countries
        $countries = $this->countries_m->get_many_by_many(array(), '', false, array(
            'country_name' => 'ASC'
        ));

        // Display the current page
        $this->template->set('countries', $countries);

        // count business postbox
        $business_postbox_count = $this->postbox_m->count_by_many(array(
            "customer_id" => $customer_id,
            "deleted" => APConstants::OFF_FLAG,
            "type" => 3
        ));

        $this->template->set('business_postbox_count', $business_postbox_count);
        $this->template->set('postbox', $postbox);
        $this->template->set('currencies', $currencies);
        $this->template->set('selected_currency_id', $selected_currency_id);
        $this->template->set('decimal_separator', $decimal_separator);
        $this->template->build('postbox_setting');
    }


    /**
     * Get info of customer
     */
    private function load_customer_info()
    {
        ci()->load->library('payment/payment_api');
        ci()->load->library('customers/CustomerMessageSetting');

        // Gets customerid logged in.
        $customer_id = APContext::getCustomerCodeLoggedIn();

        $customer = $this->customer_m->get_current_customer_info();
        $this->template->set('customer', $customer);

        // Gets customer infor.
        $info = $this->customer_m->get_by('customer_id', $customer_id);
        if ($info->new_account_type != '' && $info->plan_date_change_account_type != '') {
            $message = lang('change_account_info_message');
            $new_account_name = lang('account_type_' . $info->new_account_type);
            $message = sprintf($message, $new_account_name, APUtils::displayDate($info->plan_date_change_account_type));
            $this->template->set("message", $message);
        }
        // Get Account Type
        $items = Settings::get_list(APConstants::ACCOUNT_TYPE);

        // $604: get all messages of account
        $messages = $this->postbox_m->get_messages_by($customer_id);

        // Get other message from customer_message_table
        $messages_records_obj = CustomerMessageSetting::get($customer_id, 0, 100);
        $messages_records = $messages_records_obj['data'];
        foreach ($messages_records as $item) {
            $new_message = new stdClass();
            $new_message->action_type = $item->message_type;
            $new_message->message_type = $item->message_type;
            $new_message->message = $item->message;
            $new_message->created_date = $item->created_date;
            $messages[] = $new_message;
        }

        // get customer product setting
        $customer_product_setting = CustomerProductSetting::get_activate_flags($customer_id);

        // get payment method check
        $is_valid_payment_method= payment_api::isSettingCreditCard($customer_id);

        $this->template->set("is_valid_payment_method", $is_valid_payment_method);
        $this->template->set("messages", $messages);
        $this->template->set("customer_product_setting", $customer_product_setting);
        $this->template->set("info", $info)->set("acct_type", $items);
    }

    /**
     * Load price information
     */
    private function load_price_info()
    {
        // Get don gia cua tat ca cac loai account type
        $pricings = $this->pricing_m->get_all();
        $pricing_map = array();
        foreach ($pricings as $price) {
            if (!array_key_exists($price->account_type, $pricing_map)) {
                $pricing_map [$price->account_type] = array();
            }
            $pricing_map [$price->account_type] [$price->item_name] = $price;
        }
        $this->template->set("pricing_map", $pricing_map);
    }

    /**
     * Get postbox info
     */
    private function load_postbox_info()
    {
        // Gets customerid logged in.
        $customer_id = APContext::getCustomerCodeLoggedIn();

        // Gets customer infor.
        if(APContext::isPrimaryCustomerUser()){
            $list_customer_id = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($customer_id);
            $postbox_count = $this->postbox_m->get_postbox_count_by_customer($customer_id, $list_customer_id);
        }else{
            $postbox_count = $this->postbox_m->get_postbox_count_by_customer($customer_id);
        }
        $data_main_postbox_setting = account_api::main_postbox_setting();
        $postboxes = $data_main_postbox_setting['postboxes'];
        $main_postbox_id = $data_main_postbox_setting['main_postbox_id'];
        // Load postbox setting
        $main_postbox_setting = null;
        if ($main_postbox_id > 0) {
            $main_postbox_setting = $this->postbox_setting_m->get_by_many(array(
                "postbox_id" => $main_postbox_id,
                "customer_id" => $customer_id
            ));
            if (!$main_postbox_setting) {
                $main_postbox_setting = new stdClass();
                $main_postbox_setting->always_scan_envelope = 0;
                $main_postbox_setting->always_scan_envelope_vol_avail = 0;
                $main_postbox_setting->always_scan_incomming = 0;
                $main_postbox_setting->always_scan_incomming_vol_avail = 0;
                $main_postbox_setting->email_notification = 0;
                $main_postbox_setting->invoicing_cycle = 0;
                $main_postbox_setting->collect_mail_cycle = 2;
                $main_postbox_setting->weekday_shipping = 0;
                $main_postbox_setting->email_scan_notification = 0;
                $main_postbox_setting->always_forward_directly = 0;
                $main_postbox_setting->always_forward_collect = 0;
                $main_postbox_setting->inform_email_when_item_trashed = 0;
                $main_postbox_setting->always_mark_invoice = 0;
                $main_postbox_setting->standard_service_national_letter = 0;
                $main_postbox_setting->standard_service_international_letter = 0;
                $main_postbox_setting->standard_service_national_package = 0;
                $main_postbox_setting->standard_service_international_package = 0;
            }

            $main_postbox_setting = $data_main_postbox_setting['main_postbox_setting'];

            //$weekday_shipping = $main_postbox_setting->weekday_shipping;
            //$collect_mail_cycle = $main_postbox_setting->collect_mail_cycle;

            $next_collect_date = account_api::get_next_collect_shipping($main_postbox_setting);
            $main_postbox_setting->next_collect_date = $next_collect_date;

            $standard_shipping_services = shipping_api::get_standard_shipping_services_by_postbox($main_postbox_id);
            $main_postbox_setting->standard_service_national_letter = empty($main_postbox_setting->standard_service_national_letter) ? $standard_shipping_services['standard_service_national_letter'] : $main_postbox_setting->standard_service_national_letter;
            $main_postbox_setting->standard_service_international_letter = empty($main_postbox_setting->standard_service_international_letter) ? $standard_shipping_services['standard_service_international_letter'] : $main_postbox_setting->standard_service_international_letter;
            $main_postbox_setting->standard_service_national_package = empty($main_postbox_setting->standard_service_national_package) ? $standard_shipping_services['standard_service_national_package'] : $main_postbox_setting->standard_service_national_package;
            $main_postbox_setting->standard_service_international_package = empty($main_postbox_setting->standard_service_international_package) ? $standard_shipping_services['standard_service_international_package'] : $main_postbox_setting->standard_service_international_package;

            //Get list available services by postbox
            $shipping_services = shipping_api::get_shipping_services_by_postbox($main_postbox_id);

            $main_postbox_setting->standard_service_national_letter_dropdownlist = shipping_api::filterListShippingServices($shipping_services, APConstants::ENVELOPE_TYPE_LETTER, array(0 , 1));
            $main_postbox_setting->standard_service_international_letter_dropdownlist = shipping_api::filterListShippingServices($shipping_services, APConstants::ENVELOPE_TYPE_LETTER, array(0 , 2));
            $main_postbox_setting->standard_service_national_package_dropdownlist = shipping_api::filterListShippingServices($shipping_services, APConstants::ENVELOPE_TYPE_PACKAGE, array(0 , 1));
            $main_postbox_setting->standard_service_international_package_dropdownlist = shipping_api::filterListShippingServices($shipping_services, APConstants::ENVELOPE_TYPE_LETTER, array(0 , 2));

            $main_postbox_setting->accounting_email = EnvelopeUtils::get_accounting_interface_by_postbox($main_postbox_id)['email'];
        }

        $data = array();
        foreach ($postbox_count as $item) {
            $data [$item->type] = $item->box_count;
        }

        $this->template->set("postbox_count", $data);
        $this->template->set("postboxs", $postboxes);
        $this->template->set("main_postbox_id", $main_postbox_id);
        $this->template->set("main_postbox_setting", $main_postbox_setting);
    }

    /**
     * Load postbox setting
     */
    public function load_postbox_setting()
    {
        $this->template->set_layout(FALSE);

        // Gets customerid logged in.
        //$customer_id = APContext::getCustomerCodeLoggedIn();
        $postbox_setting_id = $this->input->post('postbox_setting_id');
        $postbox_setting = $this->postbox_setting_m->get_by_many(array(
            "postbox_id" => $postbox_setting_id,
            //"customer_id" => $customer_id
        ));
        if (!$postbox_setting) {
            $postbox_setting = new stdClass();
            $postbox_setting->always_scan_envelope = 0;
            $postbox_setting->always_scan_envelope_vol_avail = 0;
            $postbox_setting->always_scan_incomming = 0;
            $postbox_setting->always_scan_incomming_vol_avail = 0;
            $postbox_setting->email_notification = 1;
            $postbox_setting->invoicing_cycle = 0;
            $postbox_setting->collect_mail_cycle = 2;
            $postbox_setting->weekday_shipping = 2;
            $postbox_setting->email_scan_notification = 0;
            $postbox_setting->always_forward_directly = 0;
            $postbox_setting->always_forward_collect = 0;
            $postbox_setting->auto_trash_flag = 0;
            $postbox_setting->trash_after_day = '';
            $postbox_setting->always_mark_invoice = 0;
            $postbox_setting->inform_email_when_item_trashed = 0;
            $postbox_setting->standard_service_natianal_letter = 0;
            $postbox_setting->standard_service_international_letter = 0;
            $postbox_setting->standard_service_national_package = 0;
            $postbox_setting->standard_service_international_package = 0;
        }

        //Get list available services by postbox
        $shipping_services = shipping_api::get_shipping_services_by_postbox($postbox_setting_id);
        $standard_shipping_services = shipping_api::get_standard_shipping_services_by_postbox($postbox_setting_id);

        $postbox_setting->standard_service_national_letter_dropdownlist = $this->build_standard_service_dropdownlist('standard_service_national_letter', shipping_api::filterListShippingServices($shipping_services, APConstants::ENVELOPE_TYPE_LETTER, array(0 , 1)),
                    empty($postbox_setting->standard_service_national_letter) ? $standard_shipping_services['standard_service_national_letter'] : $postbox_setting->standard_service_national_letter);

        $postbox_setting->standard_service_international_letter_dropdownlist = $this->build_standard_service_dropdownlist('standard_service_international_letter', shipping_api::filterListShippingServices($shipping_services, APConstants::ENVELOPE_TYPE_LETTER, array(0 , 2)),
                    empty($postbox_setting->standard_service_international_letter) ? $standard_shipping_services['standard_service_international_letter'] : $postbox_setting->standard_service_international_letter);

        $postbox_setting->standard_service_national_package_dropdownlist = $this->build_standard_service_dropdownlist('standard_service_national_package', shipping_api::filterListShippingServices($shipping_services, APConstants::ENVELOPE_TYPE_PACKAGE, array(0 , 1)),
                    empty($postbox_setting->standard_service_national_package) ? $standard_shipping_services['standard_service_national_package'] : $postbox_setting->standard_service_national_package);

        $postbox_setting->standard_service_international_package_dropdownlist = $this->build_standard_service_dropdownlist('standard_service_international_package', shipping_api::filterListShippingServices($shipping_services, APConstants::ENVELOPE_TYPE_LETTER, array(0 , 2)),
                    empty($postbox_setting->standard_service_international_package) ? $standard_shipping_services['standard_service_international_package'] : $postbox_setting->standard_service_international_package);

        $postbox_setting->accounting_email = EnvelopeUtils::get_accounting_interface_by_postbox($postbox_setting_id)['email'];

        // Next collect date
        $weekday_shipping = $postbox_setting->weekday_shipping;
        $collect_mail_cycle = $postbox_setting->collect_mail_cycle;
        $next_collect_date = account_api::get_next_collect_shipping($postbox_setting);

        $postbox_setting->next_collect_date = $next_collect_date;
        $this->success_output('', $postbox_setting);
        return;
    }


    public function build_standard_service_dropdownlist($name, $data, $default){
        return  my_form_dropdown(array(
                    "data" => $data,
                    "value_key" => 'id',
                    "label_key" => 'name',
                    "value" => $default,
                    "name" => $name,
                    "id"    => $name,
                    "clazz" => 'input-width',
                    "style" => 'width: 100%',
                    "has_empty" => true,
                    "option_default" => '---Select shipping service---',
                ));
    }

    /**
     * Forfot password
     */
    public function change_my_pass()
    {
        $this->load->library('customers/customers_api');

        $this->template->set_layout(FALSE);
        $customer = new stdClass();
        $customer->email = '';
        $customer->password = '';
        $customer->repeat_password = '';

        if ($_POST) {
            $this->form_validation->set_rules($this->change_pass_validation_rules);
            if ($this->form_validation->run()) {
                $passowrd = $this->input->post('password');
                $current_password = $this->input->post('current_password');
                $customer = APContext::getCustomerLoggedIn();
                $current_customer = $this->customer_m->get_by_many(array(
                    "email" => $customer->email,
                    "(status IS NULL OR status <> '1')" => null
                ));

                if (md5($current_password) != $current_customer->password) {
                    $message = lang('current_password_invalid');
                    $this->error_output($message);
                    return;
                }

                // Insert new customer
                $this->customer_m->update_by_many(array(
                    "email" => $customer->email
                ), array(
                    "password" => md5($passowrd)
                ));

                //#1309: Insert customer history
                $history = [
                    'customer_id' => $customer->customer_id,
                    'action_type' => APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_PASSWORD,
                    'created_by_id' => APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER,
                ];
                customers_api::insertCustomerHistory([$history]);

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

                $message = lang('change_my_pass_success');
                $this->success_output($message);
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Display the current page
        $this->template->set('customer', $customer)->build('change_my_pass');
    }

    /**
     * Forfot password
     */
    public function change_my_email()
    {
        ci()->load->library('payment/payone');
        ci()->load->library('customers/customers_api');

        $this->template->set_layout(FALSE);

        $customer_id = $this->input->get_post('customer_id');
        $customer_code_loggedin = APContext::getCustomerCodeLoggedIn();
        $is_edit_user = false;
        if(empty($customer_id)){
            $customer = APContext::getCustomerLoggedIn();
        }else{
            $customer = APContext::getCustomerByID($customer_id);
            $is_edit_user = true;
        }

        if ($_POST) {
            $this->form_validation->set_rules($this->change_email_validation_rules);
            if ($this->form_validation->run()) {
                $email = $this->input->post('email');
                $current_password = $this->input->post('current_password');

                if(empty($customer_id) || $customer_id == $customer_code_loggedin){
                    if( md5($current_password) != $customer->password ){
                        $message = lang('password_incorrect');
                        $this->error_output($message);
                        return;
                    }
                }

                //#1309: Insert customer history
                $history_list = [
                    'email' => [    // Log change email
                        'customer_id' => $customer_id,
                        'action_type' => APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_EMAIL,
                        'created_by_id' => APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER,
                        'current_data' => $email,
                        'old_data' => $customer->email
                    ],
                    'status' => [   // Log change status
                        'customer_id' => $customer_id,
                        'action_type' => APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_STATUS,
                        'created_by_id' => APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER,
                        'current_data' => APConstants::CUSTOMER_HISTORY_STATUS_NEVER_ACTIVATED,
                    ]
                ];

                // check edit user enterprise customer.
                if(!empty($customer_id) &&  $customer_id != $customer_code_loggedin && $customer->role_flag == APConstants::OFF_FLAG){
                    // only change email
                    $this->customer_m->update_by_many(array(
                        "customer_id" => $customer_id
                    ), array(
                        "email" => $email,
                        "email_confirm_flag" => APConstants::ON_FLAG,
                    ));
                    CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'email_confirm_flag', APConstants::ON_FLAG);
                    customers_api::insertCustomerHistory($history_list);
                    $message = "The email changed successfully.";
                } else{
                    // do change email
                    account_api::change_account_email($customer->customer_id, $email, $history_list);

                    //Send info to update customer in Payone system
                    $this->payone->update_user($customer_id);

                    // logout system
                    if(!APContext::isPrimaryCustomerUser()){
                        APContext::logout();
                    }
                    if (get_cookie('RememberCode')) {
                        delete_cookie('RememberCode');
                    }
                    $message = lang('change_my_email_success');
                }

                $this->success_output($message);
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Display the current page
        $this->template->set('is_edit_user', $is_edit_user);
        $this->template->set('customer_id', $customer_id);
        $this->template->set('customer_code_loggedin', $customer_code_loggedin);
        $this->template->set('customer', $customer)->build('change_my_email');
    }

    /**
     * Forfot password
     */
    public function change_auto_send_invoice_flag()
    {
        $this->template->set_layout(FALSE);
        $customer = APContext::getCustomerLoggedIn();
        $customer_id = $customer->customer_id;

        $auto_send_invoice_flag = $this->input->get_post('auto_send_invoice_flag');

        // Insert new customer
        $this->customer_m->update_by_many(array(
            "customer_id" => $customer_id
        ), array(
            "auto_send_invoice_flag" => $auto_send_invoice_flag
        ));

        $message = lang('change_auto_send_invoice_flag_success');
        $this->success_output($message);
        return;

    }

    /**
     * The customer changes currency
     */
    public function change_currency()
    {
        $this->template->set_layout(false);
        $customer = APContext::getCustomerLoggedIn();
        $customer_id = $customer->customer_id;

        $currency_id = $this->input->get_post('currency_id');

        // Update selected currency
        $this->customer_m->update_by_many(array('customer_id' => $customer_id), array('currency_id' => $currency_id));

        $message = lang('change_currency_success');
        $this->success_output($message);
        return;
    }

    /**
     * Customer changes language
     */
    public function change_language()
    {
        $this->template->set_layout(false);

        $customer = APContext::getCustomerLoggedIn();
        $customer_id = $customer->customer_id;

        $language = $this->input->get_post('language');

        CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'language', $language);

        $this->success_output('Change language successfully.');
        return;
    }

    /**
     * The customer changes decimal separator
     */
    public function change_decimal_separator()
    {
        $this->template->set_layout(false);
        $customer = APContext::getCustomerLoggedIn();
        $customer_id = $customer->customer_id;

        $decimal_separator = $this->input->get_post('decimal_separator');

        $this->customer_m->update_by_many(array('customer_id' => $customer_id), array('decimal_separator' => $decimal_separator));

        $message = lang('change_decimal_separator_success');
        $this->success_output($message);
        return;
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
     * Change settings
     */
    public function save_settings()
    {
        $this->template->set_layout(FALSE);

        // Gets customer address infor.
        $postbox_setting_id = $this->input->post('postbox_setting_id');
        $check = $this->postbox_m->get_by('postbox_id', $postbox_setting_id);

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
            $auto_trash_flag = $this->input->post('auto_trash_flag');
            $trash_after_day = $this->input->post('trash_after_day');
            $invoicing_cycle = $this->input->post('invoicing_cycle');
            $always_mark_invoice = $this->input->post('always_mark_invoice');
            $standard_service_national_letter = $this->input->post('standard_service_national_letter');
            $standard_service_international_letter = $this->input->post('standard_service_international_letter');
            $standard_service_national_package = $this->input->post('standard_service_national_package');
            $standard_service_international_package = $this->input->post('standard_service_international_package');
            $customer_id = $check->customer_id;

            // update settings information.
            $data = array(
                'always_scan_envelope_vol_avail' => isset($envelope_scan) ? $envelope_scan == 'on' ? 1 : 0 : 0,
                'always_scan_incomming_vol_avail' => isset($scans) ? $scans == 'on' ? 1 : 0 : 0,
                'always_scan_envelope' => isset($always_scan_envelope) ? $always_scan_envelope == 'on' ? 1 : 0 : 0,
                'always_scan_incomming' => isset($always_scan_incomming) ? $always_scan_incomming == 'on' ? 1 : 0 : 0,
                'email_scan_notification' => isset($email_scan_notification) ? $email_scan_notification == 'on' ? 1 : 0 : 0,
                'always_forward_directly' => isset($always_forward_directly) ? $always_forward_directly == 'on' ? 1 : 0 : 0,
                'always_forward_collect' => isset($always_forward_collect) ? $always_forward_collect == 'on' ? 1 : 0 : 0,
                'inform_email_when_item_trashed' => isset($inform_email_when_item_trashed) ? $inform_email_when_item_trashed == 'on' ? 1 : 0 : 0,
                'email_notification' => $email_notification,
                'collect_mail_cycle' => $collect_mail_cycle,
                'weekday_shipping' => $weekday_shipping,
                'postbox_id' => $postbox_setting_id,
                'customer_id' => $customer_id,
                'auto_trash_flag' => isset($auto_trash_flag) ? $auto_trash_flag == 'on' ? 1 : 0 : 0,
                'trash_after_day' => isset($trash_after_day) ? $trash_after_day : '',
                'always_mark_invoice' => isset($always_mark_invoice) ? $always_mark_invoice == 'on' ? 1 : 0 : 0,
                'standard_service_national_letter' => $standard_service_national_letter,
                'standard_service_international_letter' => $standard_service_international_letter,
                'standard_service_national_package' => $standard_service_national_package,
                'standard_service_international_package' => $standard_service_international_package
            );

            $postbox_setting_check = $this->postbox_setting_m->get_by_many(array(
                "postbox_id" => $postbox_setting_id,
                //"customer_id" => $customer_id
            ));

            if (empty($postbox_setting_check)) {
                $this->postbox_setting_m->insert($data);
            } else {
                $this->postbox_setting_m->update_by_many(array(
                    'postbox_id' => $postbox_setting_id,
                    //"customer_id" => $customer_id
                ), $data);
            }

            //Mark as invoice for all items of this postbox
            if ($data['always_mark_invoice'] == APConstants::ON_FLAG){
                $this->load->model('scans/envelope_m');
                //Mark invoice for all items are not mark invoice yet
                 $array_where = array(
                    "envelopes.postbox_id" => $postbox_setting_id,
                    "to_customer_id" => $customer_id,
                    "trash_flag IS NULL" => null
                );
                $this->envelope_m->update_by_many($array_where, array("invoice_flag" => APConstants::ON_FLAG));
            }

            $message = lang('change_account_setting_success');
            $this->success_output($message);
            return;
        }
        $this->template->set('customer', $check)->build('index');
    }

    /**
     * Change account type (Free, Private, Business)
     */
    public function change_my_account_type()
    {
    	// Load library
    	$this->load->library('addresses/addresses_api');
        $this->load->library('customers/customers_api');
        $this->template->set_layout(FALSE);

        // Gets customerid logged in.
        $customer_id = APContext::getCustomerCodeLoggedIn();

        // #472: get postboxes by customer_id.
        $postboxes = $this->postbox_m->get_postboxes_by($customer_id);
        $this->template->set("postboxes", $postboxes);

        // Gets customer infor.
        $customer = $this->customer_m->get_by('customer_id', $customer_id);


        if ($_POST) {
            $this->form_validation->set_rules($this->change_account_type_validation_rules);
            //$472: get current postbox.
            $postbox_id = $this->input->post("postbox_id");
            $curr_postbox = $this->postbox_m->get($postbox_id);

            // added #668: can not change bussiness postbox type if additional location.
            $primary_location = APUtils::getPrimaryLocationBy($customer_id);

            if ($primary_location != $curr_postbox->location_available_id) {
                $this->error_output(lang('can_not_change_if_is_additional_location'));
                return;
            }

            if ($customer->activated_flag != APConstants::ON_FLAG) {
                $this->error_output(lang('can_not_change_postbox_account_not_activated'));
                return;
            }

            if ($this->form_validation->run()) {

                // $current_account_type = $customer->account_type;
                $current_account_type = $curr_postbox->type;
                $new_account_type = $this->input->post('account_type');

                // Check if current account type and new account type is same
                if ($current_account_type == $new_account_type) {
                    $message = lang('change_my_account_type_success');
                    $this->success_output($message);
                    return;
                }

                // #1012 Add pre-payment process
                if ($new_account_type == APConstants::BUSINESS_TYPE) {
                    $location_id = $curr_postbox->location_available_id;
                    $check_prepayment_data = CustomerUtils::checkApplyAddPostboxPrepayment($new_account_type, $location_id, $customer_id);
                    if ($check_prepayment_data['prepayment'] == true) {
                        $check_prepayment_data['status'] = FALSE;
                        echo json_encode($check_prepayment_data);
                        return;
                    }
                }

                // If change from other to free
                if ($current_account_type === APConstants::FREE_TYPE) {
                    // Update account type of current customer
                    //$this->customer_m->update_by_many(array(
                    //    "customer_id" => $customer_id
                    //), array(
                    //    "account_type" => $new_account_type,
                    //    "new_account_type" => null,
                    //    "plan_date_change_account_type" => null
                    //));

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

                    /*
                     * #1180 create postbox history page like check item page
                     *  Upgrade order from AS YOU GO to private or bussiness
                     */
                    // CustomerUtils::actionPostboxHistoryActivity($postbox_id, APConstants::POSTBOX_UPGRADE, now(),$new_account_type, APConstants::INSERT_POSTBOX);
                    customers_api::addPostboxHistory($postbox_id, APConstants::POSTBOX_UPGRADE, $new_account_type, $current_account_type);
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
                    	// Account type
                    	$account_type = "Business";

                        $postbox_name = $curr_postbox->postbox_name;
                        $name = $curr_postbox->name;
                        $company = $curr_postbox->company;

                        // Get info location
                        $location = $this->addresses_api->getLocationByID ($curr_postbox->location_available_id);

                        // Get info country
                        $country = $this->countries_m->get_by_many(array(
                        		'id' => $location->country_id
                        ));

                        $to_email = Settings::get(APConstants::MAIL_CONTACT_CODE);
                        $data = array(
                            "slug" => APConstants::new_business_account_notification,
                            "to_email" => $to_email,
                            // Replace content
                            "user_name" => $customer->user_name,
                            "email" => $customer->email,
                            "account_type" => $account_type,
                            "postbox_name" => $postbox_name,
                            "name" => $name,
                            "company" => $company,
                            "street" => $location->street,
                            "postcode" => $location->postcode,
                            "city" => $location->city,
                            "region" => $location->region,
                            "country" => $country->country_name,
                            "location_email" => $location->email,
                            "location_phone" => $location->phone_number
                        );
                        // Send email
                        MailUtils::sendEmailByTemplate($data);

                        // Send email to customer
                        $data = array(
                            "slug" => APConstants::new_business_account_notification_for_customer,
                            "to_email" => $customer->email,
                            // Replace content
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

                    $message = lang('change_my_account_type_success');
                    $this->success_output($message);
                    return;
                } else {
                    $change_date = APUtils::getFirstDayOfNextMonth();
                    // Update account type of current customer
                    // DO NOT change account type when change postbox.
                    //$this->customer_m->update_by_many(array(
                    //    "customer_id" => $customer_id
                    //), array(
                    //    "new_account_type" => $new_account_type,
                    //    "plan_date_change_account_type" => $change_date
                    //));

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

                    /*
                     * #1180 create postbox history page like check item page
                     *  Activity: upgrade ordered, downgrade ordered, upgraded, downgraded
                     */
                    if ($current_account_type === APConstants::PRIVATE_TYPE && $new_account_type === APConstants::BUSINESS_TYPE) {
                        // Upgrade ordered from  private to bussiness
                        // CustomerUtils::actionPostboxHistoryActivity($postbox_id, APConstants::POSTBOX_UPGRADE_ORDER, now(), $new_account_type, APConstants::INSERT_POSTBOX);
                        customers_api::addPostboxHistory($postbox_id, APConstants::POSTBOX_UPGRADE_ORDER, $new_account_type, $current_account_type);
                    } else {
                        customers_api::addPostboxHistory($postbox_id, APConstants::POSTBOX_DOWNGRADE_ORDER, $new_account_type, $current_account_type);
                    }
                    // else if ($current_account_type === APConstants::BUSINESS_TYPE && $new_account_type != APConstants::BUSINESS_TYPE) {
                    //     //  Downgrade ordered from  AS YOU GO, private to bussiness
                    //     CustomerUtils::actionPostboxHistoryActivity($postbox_id, APConstants::POSTBOX_DOWNGRADE_ORDER, now(), $new_account_type, APConstants::INSERT_POSTBOX);
                    // } else if ($current_account_type === APConstants::PRIVATE_TYPE && $new_account_type === APConstants::FREE_TYPE) {
                    //     //  Downgrade ordered from  private to AS YOU GO
                    //     CustomerUtils::actionPostboxHistoryActivity($postbox_id, APConstants::POSTBOX_DOWNGRADE_ORDER, now(), $new_account_type, APConstants::INSERT_POSTBOX);
                    // }

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

                    $this->success_output($message);
                    return;
                }
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Display the current page
        $this->template->set('customer', $customer)->build('change_my_account_type');
    }

    /**
     * Add postbox
     */
    public function add_postbox($type = null, $location_id = null, $advanced = 0)
    {
        $this->template->set_layout(FALSE);

        ci()->load->library('payment/payone');
        ci()->load->library('addresses/addresses_api');
        ci()->load->library('price/price_api');
        ci()->load->library('account/account_api');

        $check = 0;

        // get this param for add postbox of user enterprise
        $customer_user_id = $this->input->get_post('customer_id');
        $product_type = $this->input->get_post('product_type');
        if(empty($customer_user_id)){
            $customer    = APContext::getCustomerLoggedIn();
        }else{
            $customer = APContext::getCustomerByID($customer_user_id);
        }

        $customer_id = $customer->customer_id;

        $locate = addresses_api::getLocationPublic();

        $account_type = $this->input->get_post('account_type');
        $location     = $this->input->get_post('location');

        $custname     = trim($this->input->get_post('custname'));
        $company      = trim($this->input->get_post('company'));
        $postname     = $this->input->get_post('postname');

        $primary_location = APUtils::getPrimaryLocationBy($customer_id);

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            // Check if this is enterprise customer, all postbox should be enterprise
            $isEnterpriseCustomer = APContext::isEnterpriseCustomer();
            if ($isEnterpriseCustomer) {
                $account_type = APConstants::ENTERPRISE_CUSTOMER;
            }

            // #1012 Add pre-payment process
            if ($account_type == APConstants::BUSINESS_TYPE) {
                $check_prepayment_data = CustomerUtils::checkApplyAddPostboxPrepayment($account_type, $location, $customer_id);
                if ($check_prepayment_data['prepayment'] == true) {
                    $check_prepayment_data['status'] = FALSE;
                    echo json_encode($check_prepayment_data);
                    return;
                }
            }

            try {
                $current_balance = APUtils::getActualOpenBalanceDue($customer_id);
                $open_balance    = $current_balance;

                // Check if current balance be greate than 0
                if ($current_balance > 0) {
                    $invoice_id = APUtils::genetateReferenceForOpenBalance($customer_id);
                    $result = $this->payone->authorize($customer_id, $invoice_id, $open_balance);

                    if (!$result) {
                        $message = sprintf(lang('customer.save_direct_charge_without_invoice_error'), $customer->email);
                        $this->error_output($message);
                        return;
                    }
                }
            } catch (Exception $e) {
                $message = sprintf(lang('customer.save_direct_charge_without_invoice_error'), $customer->email);
                $this->error_output($message);
                return;
            }

            // Check first location for free or private postbox.
            $main_location_id = $location;
            if (!$isEnterpriseCustomer && $account_type != APConstants::BUSINESS_TYPE) {
                $main_location_id = $primary_location;
            }
            if ($account_type == APConstants::FREE_TYPE && $main_location_id != $location) {
                $this->error_output(lang('add_postbox_error'));
                return;
                //}
            }
            if ($account_type == APConstants::PRIVATE_TYPE && $main_location_id != $location) {
                $this->error_output(lang('add_postbox_private_another_location_error'));
                return;
            }

            $this->form_validation->set_rules($this->add_postbox_validation_rules);

            if ($this->form_validation->run()) {
                // fix add postbox for user enterprise
                if(empty($customer_user_id) && $product_type == 'postbox'){
                    // add new postbox for new postbox user.
                    $insert_data = new stdClass();
                    $insert_data->account_type = $account_type;
                    $insert_data->location = $location;
                    $insert_data->custname = $custname;
                    $insert_data->company = $company;
                    $insert_data->postname = $postname;

                    $data = APContext::getSessionValue(APConstants::NEW_USER_POSTBOX_ENTERPRISE);
                    if(!empty($data)){
                        $insert_data->id = count($data) + 1;
                        $data[] = $insert_data;
                    }else{
                        $insert_data->id = 1;
                        $data = array($insert_data);
                    }

                    // save into session the new postbox information.
                    APContext::setSessionValue(APConstants::NEW_USER_POSTBOX_ENTERPRISE, $data);
                }else{
                    $new_postbox_id = account_api::addPostbox($customer, $account_type, $location, $custname, $company, $postname);

                    // assign postbox to user enterprise
                    if((!empty($customer_user_id) && $product_type == 'postbox') || APContext::isUserEnterprise($customer_user_id) ){
                        account_api::add_postbox_to_user(APContext::getParentCustomerCodeLoggedIn(), $customer_id, $new_postbox_id);
                    }
                }
                $message = lang('add_postbox_success');
                $this->success_output($message);

                return;

            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        $this->template->set('primary_location', $primary_location);

        if($location_id){
            $pricing_map = price_api::getPricingMapByLocationId($location_id);
            $this->template->set('pricing_map', $pricing_map);
        }

        // get list customers for enterprise customer case.
        if(APContext::isPrimaryCustomerUser()){
            $parent_customer_id = APContext::getCustomerCodeLoggedIn();
            $list_customer_id = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($parent_customer_id);
            $list_customer_id[] = $parent_customer_id;
            $list_users = $this->customer_m->get_many_by_many(array(
                "customer_id IN (".  implode(",", $list_customer_id).")" => null
            ));
            $this->template->set("list_users", $list_users);
        }
        
        // Display the current page
        $this->template->set('customer_id', $customer_user_id);
        $this->template->set('product_type', $product_type);
        $this->template->set('check', $check);
        $this->template->set('type', $type);
        $this->template->set('advanced', $advanced);
        $this->template->set('location_id', $location_id);
        $this->template->set('locate', $locate);
        $this->template->build('add_postbox');
    }

    /**
     * #1113 check for a unique name in postbox name field
     * check unique name in postbox
     */
   public function check_suggestion_name_of_postbox(){

        $location  = $this->input->get_post('location');

        $custname  = $this->input->get_post('custname');


      if(!empty($custname)){
          $name_replace = $this->get_suggestion_name_of_postbox($custname,$location);

        if ($name_replace !== "") {
            $data['message'] = sprintf(lang('customer.check_for_identical_name_in_postbox_name_field'), $custname, $name_replace);
            $data['value'] = $name_replace;
            $this->success_output('', $data);
            return ;
        } else {
            $data['value'] = '';
            $this->success_output('',$data);
            return;
        }
      }else{
          $this->success_output('');
          return;
      }

   }
    /**
     * #1113 get for a unique name in postbox name field
     * Get unique name in postbox
     */
    private function get_suggestion_name_of_postbox($postbox_name, $location_id){
        $customer_id = APContext::getCustomerCodeLoggedIn();

        $postboxes = $this->postbox_m->get_many_by_many(array(
            "customer_id" => $customer_id,
            "location_available_id" =>$location_id,
            "name like '".$postbox_name."%'" =>  null
        ));

        if($postboxes){
            $suggestion_name = trim($postbox_name);
            $index = 0;
            foreach($postboxes as $p){
                $tmp_name = explode(APConstants::SUGGESTION_SUITE_POSTBOX_NAME_PREFIX, $p->name);
                if(count($tmp_name) >1){
                    $index = intval(trim($tmp_name[count($tmp_name) - 1]));
                }
            }

            $index += 1;

            $position = strpos($suggestion_name, APConstants::SUGGESTION_SUITE_POSTBOX_NAME_PREFIX);
            if($position == false){
                $suggestion_name .= APConstants::SUGGESTION_SUITE_POSTBOX_NAME_PREFIX;
            }else{
                $tmp = explode(APConstants::SUGGESTION_SUITE_POSTBOX_NAME_PREFIX, $suggestion_name);
                $suggestion_name = $tmp[0].APConstants::SUGGESTION_SUITE_POSTBOX_NAME_PREFIX;
            }

            if($index < 10){
                $suggestion_name .= " 0".$index;
            }else{
                $suggestion_name .= " ".$index;
            }

            return $suggestion_name;
        }

        return  "";
    }

    /**
     * 1. When add new postbox if this customer has open balance due < 0 the system will trigger to required payment.
     * After customer make payment success to payone/paypal, payone/paypal will callback to clevvermail to update status.
     *
     * 2. At the client side, the system will make ajax call every 5s to check payment status and open add new postbox screen
     */
    public function check_prepayment_status() {
        $account_type = APConstants::BUSINESS_TYPE;
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $location_id = $this->input->get_post('location_id');
        // #1012 Add pre-payment process
        $check_prepayment_data = CustomerUtils::checkApplyAddPostboxPrepayment($account_type, $location_id, $customer_id);
        echo json_encode($check_prepayment_data);
    }

    /**
     * Delete postbox
     */
    public function delete_postbox()
    {
        $this->template->set_layout(FALSE);

        // Gets customerid logged in.
        $customer_id = APContext::getCustomerCodeLoggedIn();

        // Gets customer infor.
        $data = $this->postbox_m->get_list_postboxes($customer_id);

        // get list user of enterprise customer
        $list_user_id = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($customer_id);
        if(!empty($list_user_id)){
            foreach($list_user_id as $user_id){
                $tmp_list = $this->postbox_m->get_list_postboxes($user_id);
                foreach($tmp_list as $tmp){
                    $data[] = $tmp;
                }
            }
        }

        if ($_POST) {
            // #581: change requirement for
            $curr_balance = APUtils::getCurrentBalance($customer_id) + APUtils::getCurrentBalanceThisMonth($customer_id);
            if ($curr_balance > 0.1) {
                $message = lang('can_not_delete_postbox');
                $this->error_output($message, array("payment" => true, 'curr_balance' => $curr_balance));
                return;
            }

            // validate rule.
            $this->form_validation->set_rules($this->postbox_validation_rules);
            if ($this->form_validation->run()) {
                // Gets total postbox
                $total_postbox = $this->postbox_m->count_by_many(array(
                    "customer_id" => $customer_id,
                    "deleted <> 1" => null
                ));

                // case1: Check last postbox => delete account
                if ($total_postbox == 1) {
                    // delete account process
                    $message = lang('can_not_delete_main_postbox');
                    $this->error_output($message, array(
                        "last_postbox" => true
                    ));
                    return;
                }

                $postbox_id = $this->input->post('postbox_name');
                $postbox_check = $this->postbox_m->get_many_by_many(array(
                    "first_location_flag" => 1,
                    "deleted" => 0
                ));
                $is_first_location = false;
                if(count($postbox_check) == 1){
                    $is_first_location = true;
                }

                $this->error_output('', array("postbox_type" => APConstants::BUSINESS_TYPE, 'is_first_location' => $is_first_location, "last_postbox" => false));
                return;
            }
        }

        // Display the current page
        $this->template->set('data', $data)->build('delete_postbox');
    }

    /**
     * Delete postbox
     */
    public function reactivate_delete_postbox()
    {
        $this->template->set_layout(FALSE);
        $customer_id = APContext::getCustomerCodeLoggedIn();
        // Delete on the end of month
        $success = $this->customer_m->update_by_many(array(
            "customer_id" => $customer_id
        ), array(
            "plan_delete_date" => null
        ));
        if ($success) {
            $message = lang('reactivate_success');
            $this->success_output($message);
            return;
        } else {
            $message = lang('customer.reactivate_error');
            $this->error_output($message);
            return;
        }
    }

    /**
     * Delete postbox
     */
    public function confirm_delete_postbox()
    {
        $this->template->set_layout(FALSE);
        $customer_id = APContext::getCustomerCodeLoggedIn();

        if ($_POST) {
            $delete_type = $this->input->get_post('delete_type');
            $main_postbox = $this->postbox_m->get_by_many(array(
                "customer_id" => $customer_id,
                "is_main_postbox" => 1
            ));

            // Start fixbug #399
            $curr_balance = APUtils::getCurrentBalance($customer_id) + APUtils::getCurrentBalanceThisMonth($customer_id);
            if ($curr_balance > 0.1) {
                $message = lang('can_not_delete_postbox');
                $this->error_output($message, array("payment" => true));
                return;
            }
            // End fixbug #399

            // Gets customer infor.
            $customer = $this->customer_m->get_by('customer_id', $customer_id);
            $email_template_code = "";

            // Delete on the end of month
            if ($delete_type == '1') {
                $plan_delete_date = APUtils::getLastDayOfCurrentMonth();
                $success = $this->customer_m->update_by_many(array(
                    "customer_id" => $customer_id
                ), array(
                    "plan_delete_date" => $plan_delete_date
                ));
                if ($success) {
                    $message = lang('delete_success01');
                    $this->success_output($message);
                    return;
                } else {
                    $message = lang('customer.delete_error');
                    $this->error_output($message);
                    return;
                }

                // 20141005 Start fixbug:  #425
                // Gets email template for downgrade account.
                $email_template_code = APConstants::downgraded_business_account;
                // 20141005 End fixbug:  #425
            } // Delete now
            else if ($delete_type == '2') {
                // call delete customer infor
                CustomerUtils::deleteCustomer($customer_id, false, false, 1, $customer_id);

                // Clean current session
                APContext::logout();

                $message = lang('customer.delete_success');
                $this->success_output($message);
                return;
            }

            // Send email
            // Get main postbox
            $postbox_name = $main_postbox->postbox_name;
            $name = $main_postbox->name;
            $company = $main_postbox->company;

            // 20141005 Start fixbug:  #425
            //$email_template = $this->email_m->get_by('slug', APConstants::downgraded_business_account);
            // 20141005 End fixbug: #425

            if (!empty($email_template_code)) {
                $to_email = Settings::get(APConstants::MAIL_CONTACT_CODE);
                $data = array(
                    "slug" => $email_template_code,
                    "to_email" => $to_email,
                    // Replace content
                    "user_name" => $customer->user_name,
                    "email" => $customer->email,
                    "postbox_name" => $postbox_name,
                    "name" => $name,
                    "company" => $company
                );
                // Send email
                MailUtils::sendEmailByTemplate($data);
            }
        }

        // Display the current page
        $this->template->build('confirm_delete_postbox');
    }

    /**
     * View pricing information.
     */
    public function view_pricing_inline()
    {
        $this->load->model('price/pricing_m');

        // Get don gia cua tat ca cac loai account type
        $pricings = $this->pricing_m->get_all();
        $pricing_map = array();
        foreach ($pricings as $price) {
            if (!array_key_exists($price->account_type, $pricing_map)) {
                $pricing_map [$price->account_type] = array();
            }
            $pricing_map [$price->account_type] [$price->item_name] = $price;
        }
        $this->template->set('pricing_map', $pricing_map);
        $this->template->build('view_pricing');
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
        $address_name = $this->input->get_post('custname');
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
     * Check current balance
     */
    public function check_current_balance()
    {
        $this->template->set_layout(FALSE);
        $customer_id = APContext::getCustomerCodeLoggedIn();

        // get postbox type
        $postbox_id = $this->input->get_post("postbox_id", "");
        $type = "";
        if ($postbox_id) {
            $postbox = $this->postbox_m->get_by("postbox_id", $postbox_id);
            $type = $postbox->type;
        }

        // Gets total postbox
        $total_postbox = $this->postbox_m->count_by_many(array("customer_id" => $customer_id, "deleted <>1" => null));

        $add_method = $this->input->get_post('add', '');
        $balance = APUtils::getAdjustOpenBalanceDue($customer_id);
        if ($total_postbox > 1 || $add_method) {
            $curr_balance = $balance['OpenBalanceDue'];
        } else {
            $curr_balance = $balance['OpenBalanceDue'] + $balance['OpenBalanceThisMonth'];
        }

        if ($curr_balance > 0.1) {
            if ($total_postbox == 1) {
                $payment_method = APUtils::getPrimaryPaymentMethod($customer_id);
                if ($payment_method == "Credit Card" || $payment_method == "Paypal") {
                    $message = sprintf(lang('valid_payment_method'), APUtils::number_format($curr_balance));
                } else {
                    $message = sprintf(lang('invalid_payment_method'), APUtils::number_format($curr_balance));
                }

                if ($add_method) {
                    $message = sprintf(lang('invalid_current_balance_error'), APUtils::number_format($curr_balance));
                }

                $this->error_output($message, array("balance" => APUtils::number_format($curr_balance), "type" => $type, 'payment_method' => $payment_method, 'delete_flag' => 1));
            } else {
                $message = sprintf(lang('invalid_current_balance_error'), APUtils::number_format($curr_balance));
                $this->error_output($message, array("balance" => APUtils::number_format($curr_balance), "type" => $type, 'delete_flag' => 0));
            }
        } else {
            $message = lang('valid_current_balance_success');
            $this->success_output($message, array("balance" => APUtils::number_format($curr_balance), "type" => $type, 'delete_flag' => ($total_postbox == 1) ? 1 : 0));
        }
    }

    /**
     * direct payment when customer add/delete postbox.
     */
    public function direct_payment()
    {
        // load payone lib.
        $this->load->library('payment/payone');

        // Gets current balance.
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $customer = $this->customer_m->get($customer_id);
        $curr_balance = APUtils::getCurrentBalance($customer_id) + APUtils::getCurrentBalanceThisMonth($customer_id);

        $amount = $curr_balance;
        $invoice_id = APUtils::genetateReferenceForOpenBalance($customer_id);

        // Check invoice method
        if (!empty($customer->invoice_code)) {
            $message = sprintf(lang('customer.notsupport_direct_charge_without_invoice'), $customer->email);
            $this->error_output($message);
            return;
        }

        // If open balance greater 0
        $result = $this->payone->authorize($customer_id, $invoice_id, $amount);

        if ($result) {
            $message = sprintf(lang('customer.save_direct_charge_without_invoice_success'), $customer->email);
            $this->success_output($message);
            return;
        } else {
            $message = sprintf(lang('customer.save_direct_charge_without_invoice_error'), $customer->email);
            $this->error_output($message);
            return;
        }
    }

    public function delete_private_business_postbox()
    {
        $this->template->set_layout(false);

        // gets customer code.
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $postbox_id = $this->input->get_post('p', '0');

        // get another location.
        $location_list = $this->postbox_m->get_all_others_location($customer_id, $postbox_id);

        // check primary location
        $postbox_check = $this->postbox_m->get_many_by_many(array(
            "customer_id" => $customer_id,
            "first_location_flag" => APConstants::ON_FLAG,
            "deleted <> 1" => null
        ));
        $postbox = $this->postbox_m->get_many_by_many(array(
            "customer_id" => $customer_id,
            "postbox_id" => $postbox_id,
            "first_location_flag" => APConstants::ON_FLAG,
            "deleted <> 1" => null
        ));

        $primary_location = 0;
        if (count($postbox_check) == 1 && $postbox && $location_list) {
            $primary_location = 1;
        }

        $this->template->set("postbox_id", $postbox_id);
        $this->template->set('primary_location', $primary_location);
        $this->template->set("location_list", $location_list);
        $this->template->build('delete_private_business_postbox');
    }

    public function delete_free_postbox()
    {
        $this->template->set_layout(false);

        // gets customer code.
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $postbox_id = $this->input->get_post('p', '0');

        // get another location.
        $location_list = $this->postbox_m->get_all_others_location($customer_id, $postbox_id);

        // check primary location
        $postbox_check = $this->postbox_m->get_by_many(array(
            "customer_id" => $customer_id,
            "postbox_id" => $postbox_id,
            "first_location_flag" => APConstants::ON_FLAG,
            "deleted <> 1" => null
        ));

        $primary_location = 0;
        if ($postbox_check && $location_list) {
            $primary_location = 1;
        }

        $this->template->set("postbox_id", $postbox_id);
        $this->template->set('primary_location', $primary_location);
        $this->template->set("location_list", $location_list);
        $this->template->build('delete_free_postbox');
    }

    public function delete_last_postbox()
    {
        $customer_id = APContext::getCustomerCodeLoggedIn();

        $result = $this->generate_last_invoice_and_direct_payment($customer_id);

        if ($result) {
            // direct delete with free account.
            $account_type = APUtils::getAccountTypeBy($customer_id);
            if ($account_type == APConstants::FREE_TYPE) {
                CustomerUtils::deleteCustomer($customer_id, false, false, 1, $customer_id);

                APContext::logout();

                // #615 Calculate postbox fee after insert new postbox
                Events::trigger('cal_postbox_invoices_directly', array(
                    'customer_id' => $customer_id
                ), 'string');

                redirect("/customer");
            }

            $this->success_output('');
        } else {
            $curr_balance = APUtils::getCurrentBalance($customer_id);
            $message = sprintf(lang('account:save_direct_charge_without_invoice_errorr'), $curr_balance);
            $this->error_output($message);
        }

        return;
    }

    private function generate_last_invoice_and_direct_payment($customer_id)
    {
        // Gets target month to export invoice report.
        $curr_month = date("Ym");

        $this->load->library('invoices/export');
        $this->load->model('invoices/invoice_summary_m');
        $invoice_summary = $this->invoice_summary_m->get_by_many(array(
            'customer_id' => $customer_id,
            'invoice_file_path IS NOT NULL' => NULL,
            "(send_invoice_flag IS NULL OR send_invoice_flag <> '1')" => NULL,
            'total_invoice > 0' => NULL,
            'invoice_month' => $curr_month
        ));

        // Truong hop ko ton tai hoac da gui roi
        if (empty($invoice_summary) || $invoice_summary->send_invoice_flag == '1') {
            return false;
        }

        // export pdf file:
        $file_export = $this->export->export_invoice($invoice_summary->invoice_code, $customer_id);

        // send mail with invoice report attackment.
        if ($file_export) {
            // Update 2 st payment flag
            $this->invoice_summary_m->update_by_many(array(
                'customer_id' => $customer_id,
                'id' => $invoice_summary->id
            ), array(
                'send_invoice_flag' => '1',
                'send_invoice_date' => now(),
                "update_flag" => 0
            ));

            $customer = APContext::getCustomerLoggedIn();
            APUtils::send_email_invoices_monthly_report($customer, $file_export, '1', $invoice_summary->invoice_code);
        }

        return true;
    }

    public function payment_box()
    {
        $this->template->set_layout(FALSE);
        $method = $this->input->get_post('method');
        $customer_id = APContext::getCustomerCodeLoggedIn();

        $balance = APUtils::getAdjustOpenBalanceDue($customer_id);

        $total_postbox = $this->postbox_m->count_by_many(array("customer_id" => $customer_id, "deleted <>1" => null));

        if ($total_postbox > 1 || $method == 'add') {
            $curr_balance = $balance['OpenBalanceDue'];
        } else {
            $curr_balance = $balance['OpenBalanceDue'] + $balance['OpenBalanceThisMonth'];
        }

        // Display the current page
        $this->template->set('curr_balance', APUtils::number_format($curr_balance));
        $this->template->build('payment_box');
    }

    /**
     * common funciton for delete postbox.
     * @param unknown $postbox_id
     */
    public function set_delete_postbox()
    {
        $this->load->library('customers/customers_api');
        // Gets customer Id
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $postbox_id = $this->input->get_post('postbox_id', '');
        $new_primary_location = $this->input->get_post('new_primary_location', '');

        if ($postbox_id && $customer_id) {
            // Gets deleted postbox.
            $postbox_check = $this->postbox_m->get_by('postbox_id', $postbox_id);

            // Gets list user.
            $list_user_id = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($customer_id);

            // validate delete postbox of user enterprise.
            if(in_array($postbox_check->customer_id, $list_user_id)){
                // count all postbox of user
                $postbox_count = $this->postbox_m->count_by_many(array(
                    "customer_id" => $postbox_check->customer_id,
                    "deleted" => APConstants::OFF_FLAG
                ));
                if($postbox_count == 1 && count($list_user_id) <= 10){
                    // count number of users of enterprise customer.
                    // delete account process
                    $message = lang('delete_postbox_enterprise_fail');
                    $this->error_output($message, array());
                    return;
                }

                $customer_id = $postbox_check->customer_id;
            }

            // get plan delete date.
            $plan_delete_date = APUtils::getFirstDayOfNextMonth();

            // check as you go fee.
            $number_day_duration = APUtils::getDateDiff(date("Ymd",$postbox_check->created_date), APUtils::getLastDayOfCurrentMonth());
            $pricing_map = price_api::getPricingMapByLocationId($postbox_check->location_available_id);

            // If this is free account (deleted immediately)
            if ($number_day_duration < $pricing_map[APConstants::FREE_TYPE]['as_you_go']->item_value && $postbox_check->type === APConstants::FREE_TYPE) {
                // Start transaction
                $this->postbox_m->db->trans_begin();

                 /*
                * #1180 create postbox history page like check item page
                *   Activity: delete postbox ordered by customer
                */
                APUtils::deletePostbox($postbox_id, $customer_id, APConstants::POSTBOX_DELETE_ORDER_BY_CUSTOMER);

                // Update account type
                APUtils::updateAccountType($customer_id, $new_primary_location);

                // commit transaction
                $this->postbox_m->db->trans_commit();

                // commit transaction
                if($this->postbox_m->db->trans_status() == FALSE){
                    $this->postbox_m->db->trans_rollback();
                }else{
                    $this->postbox_m->db->trans_commit();
                }

                // #615 Calculate postbox fee after insert new postbox
                Events::trigger('cal_postbox_invoices_directly', array(
                    'customer_id' => $customer_id
                ), 'string');

                $message = lang('del_postbox_success');
                $this->success_output($message);
                return;
            } else {
                // Start transaction
                $this->postbox_m->db->trans_begin();

                $this->postbox_m->update_by_many(array(
                    'postbox_id' => $postbox_id,
                    "customer_id" => $customer_id
                ), array(
                    'deleted' => 1,
                    "plan_deleted_date" => $plan_delete_date,
                    "updated_date" => now()
                ));

                // cancel activity and trash item
                EnvelopeUtils::cancelActivityAndTrashItemBy($customer_id, $postbox_id);

                /*
                 * #1180 create postbox history page like check item page
                 *   Activity: delete ordered by customer
                 */

                customers_api::addPostboxHistory($postbox_id, APConstants::POSTBOX_DELETE_ORDER_BY_CUSTOMER,"");
                // CustomerUtils::actionPostboxHistoryActivity($postbox_id, APConstants::POSTBOX_DELETE_ORDER_BY_CUSTOMER, now(), "", APConstants::INSERT_POSTBOX);

                // Update account type
                APUtils::updateAccountType($customer_id, $new_primary_location);

                // commit transaction
                if($this->postbox_m->db->trans_status() == FALSE){
                    $this->postbox_m->db->trans_rollback();
                }else{
                    $this->postbox_m->db->trans_commit();
                }

                // #615 Calculate postbox fee after insert new postbox
                Events::trigger('cal_postbox_invoices_directly', array(
                    'customer_id' => $customer_id
                ), 'string');

                $message = sprintf(lang('del_postbox_success'), $postbox_check->postbox_name, APUtils::displayDate($plan_delete_date));
                $this->success_output($message);
                return;
            }
        }
    }

    public function get_max_postbox_code()
    {
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $location_id = $this->input->post('location_id');

        // Gets postbox code max.
        $postbox_code = $this->postbox_m->get_max_postbox_code_by($customer_id, $location_id);
        $max_code = $postbox_code ? $postbox_code[0]->code + 1 : 1;
        if ($max_code < 10) {
            $max_code = "0" . $max_code;
        }
        $this->success_output('', array('code' => $max_code));
        exit();
    }

    /**
     * Add multi postbox from prepayment screen
     */
    public function add_multi_postbox()
    {
        $this->template->set_layout(FALSE);
        $this->load->library('addresses/addresses_api');
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $primary_location_id = APUtils::getPrimaryLocationBy($customer_id);

        $list_location_id_input = $this->input->get_post('list_location_id', '');
        $list_location_id = explode(",", $list_location_id_input);

        // Get all locations
        $list_location = addresses_api::getLocationPublic();
        $locations = array();
        foreach ($list_location as $location) {
            // Get price template
            $location->selected = false;
            if (in_array($location->id, $list_location_id)) {
                $location->selected = true;
            }
            $location->price = CustomerUtils::estimateNewPostboxCost(APConstants::BUSINESS_TYPE, $location->id, $customer_id);
            $locations[] = $location;
        }

        $this->template->set('locations', $locations);
        $this->template->set('primary_location_id', $primary_location_id);
        $this->template->build('add_multi_postbox');
    }

    /**
     * Phone settings
     */
    public function phone_setting()
    {
        $this->load->model('phones/phone_setting_m');
        $this->lang->load('user');

        // gets current balance
        $customer_id = APContext::getCustomerCodeLoggedIn();

        // get phone setting
        $phone_setting = $this->phone_setting_m->get_by('parent_customer_id', $customer_id);

        // do post
        if($_POST){
            $notify_flag =$this->input->post('notify_flag');
            $max_usage =$this->input->post('max_usage');

            if(empty($phone_setting)){
                 $this->phone_setting_m->insert(array(
                     "parent_customer_id" => $customer_id,
                     "notify_flag" => $notify_flag,
                     "max_daily_usage" => $max_usage,
                     "created_date" => now()
                 ));
            }else{
                $this->phone_setting_m->update_by_many(array(
                    "parent_customer_id" => $customer_id,
                ),array(
                    "notify_flag" => $notify_flag,
                    "max_daily_usage" => $max_usage,
                    "created_date" => now()
                 ));
            }
            $this->success_output(lang('users.message.update_phone_setting_success'));
            return;
        }

        $current_balance  = APUtils::getCurrentBalance($customer_id);
        $this->template->set("current_balance", $current_balance);
        $this->load_postbox_info();

        // set decimal separator
        $currency = $this->customer_m->get_standard_setting_currency($customer_id);
        $decimal_separator = $this->customer_m->get_standard_setting_decimal_separator($customer_id);
        if (empty($currency)) {
            $currency = $this->currencies_m->get_by(array('currency_short' => 'EUR'));
        }

        $this->template->set('phone_setting', $phone_setting);
        $this->template->set('currency', $currency);
        $this->template->set('decimal_separator', $decimal_separator);

        // build template
        $this->template->build('phone_setting/index');
    }

    /**
     * save invoice address function.
     */
    public function save_invoice_address(){
        ci()->load->library('payment/payone');
        $this->template->set_layout(FALSE);

        // Gets customerid logged in.
        $customer_id = $this->input->post('customer_id');
        if(empty($customer_id)){
            $customer_id = APContext::getCustomerCodeLoggedIn();
        }

        if ($_POST) {
            $this->form_validation->set_rules($this->invoice_address_rules);
            if ($this->form_validation->run()) {
                // Gets params from request.
                $data_params = array(
                    'invoicing_address_name' => $this->input->post('invoicing_address_name'),
                    'invoicing_company' => $this->input->post('invoicing_company'),
                    'invoicing_street' => $this->input->post('invoicing_street'),
                    'invoicing_postcode' => $this->input->post('invoicing_postcode'),
                    'invoicing_city' => $this->input->post('invoicing_city'),
                    'invoicing_region' => $this->input->post('invoicing_region'),
                    'invoicing_country' => $this->input->post('invoicing_country'),
                    'invoicing_phone_number' => $this->input->post('invoicing_phone_number')
                );

                try{
                    //save invoice address
                    account_api::save_invoicing_address($customer_id, $data_params);
                    //Send info to update customer in Payone system
                    $this->payone->update_user($customer_id);
                    // reload customer login information.
                    APContext::reloadCustomerLoggedIn();
                } catch (BusinessException $e) {
                    $this->error_output($e->getMessage());
                    return;
                }

                $message = lang('change_invoice_address_setting_success');
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
     * save address forwarding.
     */
    public function save_address(){
        $this->template->set_layout(FALSE);

        // Gets customerid logged in.
        $customer_id = APContext::getCustomerCodeLoggedIn();

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->form_validation->set_rules($this->fowarding_address_rules);
            if ($this->form_validation->run()) {
                // Gets params from request.
                $data_params = array(
                    'shipment_address_name' => $this->input->post('shipment_address_name'),
                    'shipment_company' => $this->input->post('shipment_company'),
                    'shipment_street' => $this->input->post('shipment_street'),
                    'shipment_postcode' => $this->input->post('shipment_postcode'),
                    'shipment_city' => $this->input->post('shipment_city'),
                    'shipment_region' => $this->input->post('shipment_region'),
                    'shipment_country' => $this->input->post('shipment_country'),
                    'shipment_phone_number' => $this->input->post('shipment_phone_number'),
                );

                try{
                    //save invoice address
                    account_api::save_address($customer_id, $data_params);

                    // reload customer login information.
                    APContext::reloadCustomerLoggedIn();
                } catch (BusinessException $e) {
                    $this->error_output($e->getMessage());
                    return;
                }

                $message = lang('change_address_setting_success');
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
     * Callback From: check_company()
     *
     * @param string $email
     *            The Email address to validate
     * @return bool
     */
    public function _check_shipment_company($shipment_company_name)
    {
        $shipment_address_name = $this->input->get_post('shipment_address_name');
        if (empty($shipment_address_name) && empty($shipment_company_name)) {
            $this->form_validation->set_message('_check_shipment_company', lang('addresses.shipment_company_required'));
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
    public function _check_invoicing_company($invoicing_company_name)
    {
        $invoicing_address_name = $this->input->get_post('invoicing_address_name');
        if (empty($invoicing_address_name) && empty($invoicing_company_name)) {
            $this->form_validation->set_message('_check_invoicing_company', lang('addresses.invoicing_company_required'));
            return false;
        }
        return true;
    }

    /**
     * The customer changes currency
     */
    public function upgrade_customer_type()
    {
        $this->template->set_layout(false);

        $setup_flag = $this->input->get_post('setup_flag');
        // Submit data
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            // validate only standard account that can upgrade to enterprise account.
            if(APContext::isEnterpriseCustomer()){
                $this->error_output(lang('can_not_upgrade_enteprise'));
                return;
            }

            $customer_id = APContext::getCustomerCodeLoggedIn();
            $separatePostboxType = $this->input->get_post('separatePostboxType');
            if (empty($separatePostboxType)) {
                $separatePostboxType = '1';
            }

            account_api::upgradeCustomerType($customer_id, APConstants::ENTERPRISE_CUSTOMER, $separatePostboxType, $setup_flag);
            // get list user
            $list_user = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($customer_id);
            $postbox_count = $this->postbox_m->count_by_many(array(
                "customer_id IN (".  implode(",", $list_user).")" => null,
                "deleted"=> APConstants::OFF_FLAG
            ));

            $customer_postbox = $this->postbox_m->count_by_many(array(
                "customer_id" => $customer_id,
                "deleted"=> APConstants::OFF_FLAG
            ));
            $message = sprintf(lang('upgrade_customer_type_success'), $postbox_count + $customer_postbox);
            $this->success_output($message);
            return;
        } else {
            $this->template->set('setup_flag', $setup_flag);
            $this->template->build('confirm_upgrade_enterprise_customer');
        }
    }

    /**
     * update invoice address of user.
     * @return type
     */
    public function invoice_address(){
        $this->template->set_layout(false);
        $customer_id = $this->input->get_post('customer_id');
        
        if(empty($customer_id)){
            $customer_id = APContext::getCustomerCodeLoggedIn();
        }
        // address check
        $customer_address = $this->customers_address_m->get($customer_id);
 
        // Get all countries
        $countries = $this->countries_m->get_many_by_many(array(), '', false, array(
            'country_name' => 'ASC'
        ));
        
        $this->template->set("address", $customer_address);
        $this->template->set("customer_id", $customer_id);
        $this->template->set("countries", $countries);
        $this->template->build("invoice_address");
    }

}