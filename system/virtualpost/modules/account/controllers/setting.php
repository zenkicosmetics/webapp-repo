<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Setting extends AccountSetting_Controller {
    private $_invoice_setup_validation = array(
        array(
            'field' => 'INSTANCE_OWNER_COMPANY_CODE',
            'label' => 'lang:INSTANCE_OWNER_COMPANY_CODE',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'INSTANCE_OWNER_STREET_CODE',
            'label' => 'lang:INSTANCE_OWNER_STREET_CODE',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'INSTANCE_OWNER_PLZ_CODE',
            'label' => 'lang:INSTANCE_OWNER_PLZ_CODE',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'INSTANCE_OWNER_CITY_CODE',
            'label' => 'lang:INSTANCE_OWNER_CITY_CODE',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'INSTANCE_OWNER_COUNTRY_CODE',
            'label' => 'lang:INSTANCE_OWNER_COUNTRY_CODE',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'INSTANCE_OWNER_TEL_INVOICE_CODE',
            'label' => 'lang:INSTANCE_OWNER_TEL_INVOICE_CODE',
            'rules' => 'trim'
        ),
        array(
            'field' => 'INSTANCE_OWNER_FAX_CODE',
            'label' => 'lang:INSTANCE_OWNER_FAX_CODE',
            'rules' => 'trim'
        ),
        array(
            'field' => 'INSTANCE_OWNER_MAIL_INVOICE_CODE',
            'label' => 'lang:INSTANCE_OWNER_MAIL_INVOICE_CODE',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'INSTANCE_OWNER_VAT_NUM_CODE',
            'label' => 'lang:INSTANCE_OWNER_VAT_NUM_CODE',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'INSTANCE_OWNER_REGISTERED_NUM_CODE',
            'label' => 'lang:INSTANCE_OWNER_REGISTERED_NUM_CODE',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'INSTANCE_OWNER_DIRECTOR_CODE',
            'label' => 'lang:INSTANCE_OWNER_DIRECTOR_CODE',
            'rules' => 'trim|required'
        )
    );
    
    private $_term_condition_validation = array(
        array(
            'field' => 'content',
            'label' => 'content',
            'rules' => 'trim|required'
        )
    );
    
    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     *
     * @todo Document properly please.
     */
    public function __construct() {
        parent::__construct();

        // check permission.
        if(!APContext::isEnterpriseCustomer() && !APContext::isAdminCustomerUser()){
            redirect('account');
        }
        
        $this->load->library('form_validation');
        $this->load->library('encrypt');
        

        // load the theme_example view
        $this->load->model(array(
            'customers/customer_m',
            'customers/customer_setting_m'
        ));
        
        // load library
        $this->load->library('files/files');
        $this->load->library('account/account_api');
        $this->load->library('settings/settings_api');
        
        // load lang
        $this->lang->load(array(
            'account_setting'
        ));
        
        
    }

    /**
     * Index Page for this controller.
     */
    public function index() {
        
    }
    
    /**
     * design setting.
     */
    public function design(){
        // Gets customer code.
        $customer_id = APContext::getParentCustomerCodeLoggedIn();
        
        if($_POST){
            $SITE_NAME_CODE = $this->input->post('SITE_NAME_CODE');
            if (!empty($SITE_NAME_CODE)) {
                AccountSetting::set($customer_id, APConstants::SITE_NAME_CODE, $SITE_NAME_CODE);
            }

            $SITE_LOGO_CODE = $this->input->post('SITE_LOGO_CODE');
            if (!empty($SITE_LOGO_CODE)) {
                AccountSetting::set($customer_id, APConstants::SITE_LOGO_CODE, $SITE_LOGO_CODE);
            }

            $SITE_LOGO_WHITE_CODE = $this->input->post('SITE_LOGO_WHITE_CODE');
            if (!empty($SITE_LOGO_WHITE_CODE)) {
                AccountSetting::set($customer_id, APConstants::SITE_LOGO_WHITE_CODE, $SITE_LOGO_WHITE_CODE);
            }

            $FIRST_LETTER_KEY = $this->input->post('FIRST_LETTER_KEY');
            if (!empty($FIRST_LETTER_KEY)) {
                AccountSetting::set($customer_id, APConstants::FIRST_LETTER_KEY, $FIRST_LETTER_KEY);
            }

            $FIRST_ENVELOPE_KEY = $this->input->post('FIRST_ENVELOPE_KEY');
            if (!empty($FIRST_ENVELOPE_KEY)) {
                AccountSetting::set($customer_id, APConstants::FIRST_ENVELOPE_KEY, $FIRST_ENVELOPE_KEY);
            }
            
            // Seting color list
            for($i=1; $i<= 100; $i++){
                $tmp = '0000'.$i;
                $key = 'COLOR_'.substr($tmp, -3);
                
                $color_param = $this->input->post($key);
                if(!empty($color_param)){
                    AccountSetting::set($customer_id, $key, $color_param);
                }
            }

            $this->session->set_flashdata('success', lang('account_setting.save_success'));
            redirect('account/setting/design');
        }
        
        $this->template->set('customer_id', $customer_id);
        $this->template->build('settings/design');
    }
    
    /**
     * invoice setup.
     */
    public function invoice_setup(){
        $customer_id = APContext::getParentCustomerCodeLoggedIn();

        if($_POST){
            $this->form_validation->set_rules($this->_invoice_setup_validation);
            
            if ($this->form_validation->run()) {
                $INSTANCE_OWNER_COMPANY_CODE = $this->input->post('INSTANCE_OWNER_COMPANY_CODE');
                if (!empty($INSTANCE_OWNER_COMPANY_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_COMPANY_CODE, $INSTANCE_OWNER_COMPANY_CODE);
                }

                $INSTANCE_OWNER_STREET_CODE = $this->input->post('INSTANCE_OWNER_STREET_CODE');
                if (!empty($INSTANCE_OWNER_STREET_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_STREET_CODE, $INSTANCE_OWNER_STREET_CODE);
                }

                $INSTANCE_OWNER_PLZ_CODE = $this->input->post('INSTANCE_OWNER_PLZ_CODE');
                if (!empty($INSTANCE_OWNER_PLZ_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_PLZ_CODE, $INSTANCE_OWNER_PLZ_CODE);
                }

                $INSTANCE_OWNER_CITY_CODE = $this->input->post('INSTANCE_OWNER_CITY_CODE');
                if (!empty($INSTANCE_OWNER_CITY_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_CITY_CODE, $INSTANCE_OWNER_CITY_CODE);
                }

                $INSTANCE_OWNER_REGION_CODE = $this->input->post('INSTANCE_OWNER_REGION_CODE');
                if (!empty($INSTANCE_OWNER_REGION_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_REGION_CODE, $INSTANCE_OWNER_REGION_CODE);
                }

                $INSTANCE_OWNER_COUNTRY_CODE = $this->input->post('INSTANCE_OWNER_COUNTRY_CODE');
                if (!empty($INSTANCE_OWNER_COUNTRY_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_COUNTRY_CODE, $INSTANCE_OWNER_COUNTRY_CODE);
                }

                $INSTANCE_OWNER_VAT_NUM_CODE = $this->input->post('INSTANCE_OWNER_VAT_NUM_CODE');
                if (!empty($INSTANCE_OWNER_VAT_NUM_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_VAT_NUM_CODE, $INSTANCE_OWNER_VAT_NUM_CODE);
                }

                $INSTANCE_OWNER_TAX_NUMBER_CODE = $this->input->post('INSTANCE_OWNER_TAX_NUMBER_CODE');
                if (!empty($INSTANCE_OWNER_TAX_NUMBER_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_TAX_NUMBER_CODE, $INSTANCE_OWNER_TAX_NUMBER_CODE);
                }

                $INSTANCE_OWNER_DIRECTOR_CODE = $this->input->post('INSTANCE_OWNER_DIRECTOR_CODE');
                if (!empty($INSTANCE_OWNER_DIRECTOR_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_DIRECTOR_CODE, $INSTANCE_OWNER_DIRECTOR_CODE);
                }

                $INSTANCE_OWNER_IBAN_CODE = $this->input->post('INSTANCE_OWNER_IBAN_CODE');
                if (!empty($INSTANCE_OWNER_IBAN_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_IBAN_CODE, $INSTANCE_OWNER_IBAN_CODE);
                }

                $INSTANCE_OWNER_SWIFT_CODE = $this->input->post('INSTANCE_OWNER_SWIFT_CODE');
                if (!empty($INSTANCE_OWNER_SWIFT_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_SWIFT_CODE, $INSTANCE_OWNER_SWIFT_CODE);
                }

                $INSTANCE_OWNER_BANK_NAME_CODE = $this->input->post('INSTANCE_OWNER_BANK_NAME_CODE');
                if (!empty($INSTANCE_OWNER_BANK_NAME_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_BANK_NAME_CODE, $INSTANCE_OWNER_BANK_NAME_CODE);
                }

                $INSTANCE_OWNER_TEL_INVOICE_CODE = $this->input->post('INSTANCE_OWNER_TEL_INVOICE_CODE');
                if (!empty($INSTANCE_OWNER_TEL_INVOICE_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_TEL_INVOICE_CODE, $INSTANCE_OWNER_TEL_INVOICE_CODE);
                }

                $INSTANCE_OWNER_FAX_CODE = $this->input->post('INSTANCE_OWNER_FAX_CODE');
                if (!empty($INSTANCE_OWNER_FAX_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_FAX_CODE, $INSTANCE_OWNER_FAX_CODE);
                }

                $INSTANCE_OWNER_TEL_SALES_CODE = $this->input->post('INSTANCE_OWNER_TEL_SALES_CODE');
                if (!empty($INSTANCE_OWNER_TEL_SALES_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_TEL_SALES_CODE, $INSTANCE_OWNER_TEL_SALES_CODE);
                }

                $INSTANCE_OWNER_TEL_SUPPORT_CODE = $this->input->post('INSTANCE_OWNER_TEL_SUPPORT_CODE');
                if (!empty($INSTANCE_OWNER_TEL_SUPPORT_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_TEL_SUPPORT_CODE, $INSTANCE_OWNER_TEL_SUPPORT_CODE);
                }

                $INSTANCE_OWNER_MAIL_INVOICE_CODE = $this->input->post('INSTANCE_OWNER_MAIL_INVOICE_CODE');
                if (!empty($INSTANCE_OWNER_MAIL_INVOICE_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_MAIL_INVOICE_CODE, $INSTANCE_OWNER_MAIL_INVOICE_CODE);
                }

                $INSTANCE_OWNER_MAIL_SALES_CODE = $this->input->post('INSTANCE_OWNER_MAIL_SALES_CODE');
                if (!empty($INSTANCE_OWNER_MAIL_SALES_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_MAIL_SALES_CODE, $INSTANCE_OWNER_MAIL_SALES_CODE);
                }

                $INSTANCE_OWNER_MAIL_SUPPORT_CODE = $this->input->post('INSTANCE_OWNER_MAIL_SUPPORT_CODE');
                if (!empty($INSTANCE_OWNER_MAIL_SUPPORT_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_MAIL_SUPPORT_CODE, $INSTANCE_OWNER_MAIL_SUPPORT_CODE);
                }

                $INSTANCE_OWNER_WEBSITE_CODE = $this->input->post('INSTANCE_OWNER_WEBSITE_CODE');
                if (!empty($INSTANCE_OWNER_WEBSITE_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_WEBSITE_CODE, $INSTANCE_OWNER_WEBSITE_CODE);
                }

                $INSTANCE_OWNER_REGISTERED_NUM_CODE = $this->input->post('INSTANCE_OWNER_REGISTERED_NUM_CODE');
                if (!empty($INSTANCE_OWNER_REGISTERED_NUM_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_REGISTERED_NUM_CODE, $INSTANCE_OWNER_REGISTERED_NUM_CODE);
                }

                $INSTANCE_OWNER_PLACE_REGISTRATION_CODE = $this->input->post('INSTANCE_OWNER_PLACE_REGISTRATION_CODE');
                if (!empty($INSTANCE_OWNER_PLACE_REGISTRATION_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_PLACE_REGISTRATION_CODE, $INSTANCE_OWNER_PLACE_REGISTRATION_CODE);
                }

                $INSTANCE_OWNER_ACCOUNTNUMBER_CODE = $this->input->post('INSTANCE_OWNER_ACCOUNTNUMBER_CODE');
                if (!empty($INSTANCE_OWNER_ACCOUNTNUMBER_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_ACCOUNTNUMBER_CODE, $INSTANCE_OWNER_ACCOUNTNUMBER_CODE);
                }

                $INSTANCE_OWNER_BANKCODE_CODE = $this->input->post('INSTANCE_OWNER_BANKCODE_CODE');
                if (!empty($INSTANCE_OWNER_BANKCODE_CODE)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_BANKCODE_CODE, $INSTANCE_OWNER_BANKCODE_CODE);
                }

                $INSTANCE_OWNER_CUSTOMS_NUMBER = $this->input->post('INSTANCE_OWNER_CUSTOMS_NUMBER');
                if (!empty($INSTANCE_OWNER_CUSTOMS_NUMBER)) {
                    AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_CUSTOMS_NUMBER, $INSTANCE_OWNER_CUSTOMS_NUMBER);
                }

                $message = lang('account_setting.save_success');
                $this->success_output($message);
                return;
            }else{
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }
        
        $invoice_company = AccountSetting::get($customer_id, APConstants::INSTANCE_OWNER_COMPANY_CODE);
        // Set default value if the invoice setting was not set
        if (empty($invoice_company)) {
            // Get invoicing address of enterprise account
            $invoice_address = CustomerUtils::getCustomerAddressByID($customer_id);
            $customer = CustomerUtils::getCustomerByID($customer_id);
            
            AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_COMPANY_CODE, $invoice_address->invoicing_company);
            AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_STREET_CODE, $invoice_address->invoicing_street);
            AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_PLZ_CODE, $invoice_address->invoicing_postcode);
            AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_CITY_CODE, $invoice_address->invoicing_city);
            AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_COUNTRY_CODE, $invoice_address->invoicing_country);
            AccountSetting::set($customer_id, APConstants::INSTANCE_OWNER_VAT_NUM_CODE, $customer->vat_number);
        }
        
        $countries = settings_api::getAllCountries();
        $this->template->set('countries', $countries);
        $this->template->set('customer_id', $customer_id);
        $this->template->build('settings/invoice_setup');
    }

    /**
     * show pricing of customers
     */
    public function price(){
        // check permission.
        if(!APContext::isAdminCustomerUser() && !APContext::isPrimaryCustomerUser()){
            redirect('account');
        }
        
        // load API
        $this->load->library("price/price_api");
        $this->load->library("customers/customers_api");
        $this->load->library("addresses/addresses_api");
        $this->load->library("settings/settings_api");
        $this->load->library("mailbox/mailbox_api");
        $this->load->library("account/AccountSetting");

        //$customer_id = APContext::getCustomerCodeLoggedIn();
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $customer_id = $parent_customer_id;
        $postbox = mailbox_api::getFirstLocationBy($parent_customer_id);
        $firstLocationID = is_object($postbox) ? $postbox->location_available_id : 0;
        $locationId = $this->input->get_post("location_id", 0);

        if (empty($locationId)) {
            $locationId = $firstLocationID;
        }

        $type = 0;
        if ($locationId == $firstLocationID) {
            $type = $postbox->type;
        }
        
        if($_POST){
            $this->template->set_layout(false);
            $locationId  = $this->input->get_post('h_location_id');

            $postbox_fee = $this->convert_number($this->input->post('postbox_fee'));
            if (!empty($postbox_fee)) {
                AccountSetting::set_alias01($customer_id, 'postbox_fee', $postbox_fee, $locationId);
            }

            $additional_incomming_items = $this->convert_number($this->input->post('additional_incomming_items'));
            if (!empty($additional_incomming_items)) {
                AccountSetting::set_alias01($customer_id, 'additional_incomming_items', $additional_incomming_items, $locationId);
            }

            $envelop_scanning = $this->convert_number($this->input->post('envelop_scanning'));
            if (!empty($envelop_scanning)) {
                AccountSetting::set_alias01($customer_id, 'envelop_scanning', $envelop_scanning, $locationId);
            }

            $opening_scanning = $this->convert_number($this->input->post('opening_scanning'));
            if (!empty($opening_scanning)) {
                AccountSetting::set_alias01($customer_id, 'opening_scanning', $opening_scanning, $locationId);
            }

            $send_out_directly = $this->convert_number($this->input->post('send_out_directly'));
            if (!empty($send_out_directly)) {
                AccountSetting::set_alias01($customer_id, 'send_out_directly', $send_out_directly, $locationId);
            }

            $shipping_plus = $this->convert_number($this->input->post('shipping_plus'));
            if (!empty($shipping_plus)) {
                AccountSetting::set_alias01($customer_id, 'shipping_plus', $shipping_plus, $locationId);
            }

            $send_out_collected = $this->convert_number($this->input->post('send_out_collected'));
            if (!empty($send_out_collected)) {
                AccountSetting::set_alias01($customer_id, 'send_out_collected', $send_out_collected, $locationId);
            }

            $collect_shipping_plus = $this->convert_number($this->input->post('collect_shipping_plus'));
            if (!empty($collect_shipping_plus)) {
                AccountSetting::set_alias01($customer_id, 'collect_shipping_plus', $collect_shipping_plus, $locationId);
            }

            $storing_items_over_free_letter = $this->convert_number($this->input->post('storing_items_over_free_letter'));
            if (!empty($storing_items_over_free_letter)) {
                AccountSetting::set_alias01($customer_id, 'storing_items_over_free_letter', $storing_items_over_free_letter, $locationId);
            }

            $storing_items_over_free_packages = $this->convert_number($this->input->post('storing_items_over_free_packages'));
            if (!empty($storing_items_over_free_packages)) {
                AccountSetting::set_alias01($customer_id, 'storing_items_over_free_packages', $storing_items_over_free_packages, $locationId);
            }

            $additional_included_page_opening_scanning = $this->convert_number($this->input->post('additional_included_page_opening_scanning'));
            if (!empty($additional_included_page_opening_scanning)) {
                AccountSetting::set_alias01($customer_id, 'additional_included_page_opening_scanning', $additional_included_page_opening_scanning, $locationId);
            }

            $custom_declaration_outgoing_01 = $this->convert_number($this->input->post('custom_declaration_outgoing_01'));
            if (!empty($custom_declaration_outgoing_01)) {
                AccountSetting::set_alias01($customer_id, 'custom_declaration_outgoing_01', $custom_declaration_outgoing_01, $locationId);
            }

            $custom_declaration_outgoing_02 = $this->convert_number($this->input->post('custom_declaration_outgoing_02'));
            if (!empty($custom_declaration_outgoing_02)) {
                AccountSetting::set_alias01($customer_id, 'custom_declaration_outgoing_02', $custom_declaration_outgoing_02, $locationId);
            }

            $custom_handling_import = $this->convert_number($this->input->post('custom_handling_import'));
            if (!empty($custom_handling_import)) {
                AccountSetting::set_alias01($customer_id, 'custom_handling_import', $custom_handling_import, $locationId);
            }
            
            $this->success_output(lang('account_setting.save_success'));
            return;
        }

        // Get don gia cua tat ca cac loai account type
        $pricing_map = price_api::getPricingMapByLocationId($locationId);

        // Gets list public location.
        $list_access_location = addresses_api::getLocationPublic();

        // Gets customer information
        $account = customers_api::getCustomerByID($customer_id);

        // Get currencies information
        $list_currencies = settings_api::getAllCurrencies();
        $selected_currency = customers_api::getStandardCurrency($customer_id);
        $decimal_separator = customers_api::getStandardDecimalSeparator($customer_id);

        $this->template->set('list_access_location', $list_access_location);
        $this->template->set('customer_id', $customer_id);
        $this->template->set('location_id', $locationId);
        $this->template->set('pricing_map', $pricing_map);
        $this->template->set('account', $account);
        $this->template->set('list_currencies', $list_currencies);
        $this->template->set('selected_currency', $selected_currency);
        $this->template->set('decimal_separator', $decimal_separator);
        $this->template->set('account_type', $type);
        $this->template->build('settings/price');
    }
    
    /**
     * save new vat for all user of enterprise customer in next month.
     */
    public function save_vat(){
        $this->template->set_layout(false);
        // check permission.
        if(!APContext::isPrimaryCustomerUser()){
            $this->error_output("You don't have permission.");
            return;
        }
        
        if($_POST){
            $vat_rate = $this->input->get_post('vat');
            $customer_id = APContext::getCustomerCodeLoggedIn();
            $list_customer_id = account_api::getListUserIdOfCustomer($customer_id);
            $list_id = implode(',', $list_customer_id);
            
            if(!empty($vat_rate)){
                $this->customer_m->update_by_many(array(
                    "customer_id IN (".$list_id.")" => null,
                    "status" => APConstants::OFF_FLAG,
                ), array(
                    "vat_rate" => $vat_rate
                ));
                
                //AccountSetting::set($customer_id, APConstants::CUSTOMER_NEW_VAT_KEY, $vat_rate);
            }
            $this->success_output(lang("account_setting.save_success"));
            return;
        }
    }
    
    /**
     * upload file.
     */
    public function upload(){
        $this->template->set_layout(false);
        $customer = APContext::getCustomerLoggedIn();
        $server_path = 'uploads/enterprise/' . $customer->customer_code . '/settings/';
        $imagepath = Files::upload_file($server_path, 'imagepath');
        
        $this->success_output($imagepath['local_file_path']);
        return;
    }
    
    /**
     * view file pdf/image.
     * @return type
     */
    public function view_file(){
        $local_file_path = $this->input->get_post('local_file_path');
        
        if (empty($local_file_path)) {
            return;
        }
        // Does not use layout
        $this->template->set_layout(FALSE);

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
            case 'jpge':
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

        // $seconds_to_cache = APConstants::CACHED_SECONDS;

        // $ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
        // header("Expires: $ts");
        // header("Pragma: cache");
        // header("Cache-Control: max-age=$seconds_to_cache");

        readfile($local_file_path);
    }
    
     /**
     * Save support setting for enterprise customer.
     */
    public function save_support_setting(){
        $this->template->set_layout(false);
        if(!APContext::isPrimaryCustomerUser()){
            $this->error_output("You don't have permission for this action!");
            return;
        }
        
        if($_POST){
            // Gets customer
            $customer_id = APContext::getCustomerCodeLoggedIn();
            $email_flag = $this->input->post('active_support_email_user_checkbox');
            $email = $this->input->post('active_support_email_user');
            $phone_flag = $this->input->post('active_support_phone_user_checkbox');
            $phone = $this->input->post('active_support_phone_user');
            
            // save email
            AccountSetting::set($customer_id, APConstants::CUSTOMER_SUPPORT_EMAIL_KEY, $email);
            AccountSetting::set_alias02($customer_id, APConstants::CUSTOMER_SUPPORT_EMAIL_KEY, $email_flag);
            
            // save phone 
            AccountSetting::set($customer_id, APConstants::CUSTOMER_SUPPORT_PHONE_KEY, $phone);
            AccountSetting::set_alias02($customer_id, APConstants::CUSTOMER_SUPPORT_PHONE_KEY, $phone_flag);
            
            $this->success_output(lang("account_setting.save_success"));
            return;
        }
    }
    
    /**
     * get support feedback setting.
     */
    public function get_support_feedback(){
        $this->template->set_layout(false);
        $this->template->build('settings/support_feedback');
    }
    
    /**
     * get automatic charge account setting page.
     */
    public function automatic_charge_account_setting(){
        ci()->load->library('payment/payment_api');
        $customer_id = APContext::getParentCustomerCodeLoggedIn();
        $is_valid_payment_method = payment_api::isSettingCreditCard($customer_id);
        $dialog_flag = $this->input->get_post('dialog');
        if($_POST){
            if($is_valid_payment_method){
                $CUSTOMER_AUTOMATIC_CHARGE_SETTING = $this->input->post('CUSTOMER_AUTOMATIC_CHARGE_SETTING');
                $CUSTOMER_AUTOMATIC_CHARGE_SETTING = $CUSTOMER_AUTOMATIC_CHARGE_SETTING ? 1 : 0;
                $CUSTOMER_AUTOMATIC_CHARGE_SETTING_01 = $this->input->post('CUSTOMER_AUTOMATIC_CHARGE_SETTING_01');
                $CUSTOMER_AUTOMATIC_CHARGE_SETTING_02 = $this->input->post('CUSTOMER_AUTOMATIC_CHARGE_SETTING_02');
                
                AccountSetting::set($customer_id, APConstants::CUSTOMER_AUTOMATIC_CHARGE_SETTING, $CUSTOMER_AUTOMATIC_CHARGE_SETTING);
                AccountSetting::set_alias02($customer_id, APConstants::CUSTOMER_AUTOMATIC_CHARGE_SETTING, $CUSTOMER_AUTOMATIC_CHARGE_SETTING_01);
                AccountSetting::set_alias03($customer_id, APConstants::CUSTOMER_AUTOMATIC_CHARGE_SETTING, $CUSTOMER_AUTOMATIC_CHARGE_SETTING_02);
                
                // check directly after, if my account is under the limit that I have set. If yes, then it should message “your credit card now will be charged with XXX EUR, please confirm”
                $limit_amount = $CUSTOMER_AUTOMATIC_CHARGE_SETTING_01;
                $charge_amount = $CUSTOMER_AUTOMATIC_CHARGE_SETTING_02;
                $auto_deposit_flag = CustomerUtils::checkAutomaticChargeCustomer($customer_id, $limit_amount);
                if ($auto_deposit_flag) {
                    // Return success
                    $this->success_output('', array(
                        'auto_deposit_flag' => '1',
                        'charge_amount' => APUtils::number_format($charge_amount, 2)
                    ));
                    return;
                }
               
                $this->success_output(lang("account_setting.save_success"));
            }else{
                $this->error_output(lang("account_setting.require_credit_card"));
            }
            return;
        }

        $this->template->set_layout(false);
        $this->template->set('is_valid_payment_method', $is_valid_payment_method);
        $this->template->set('customer_id', $customer_id);
        $this->template->set('dialog_flag', $dialog_flag);
        $this->template->build('automatic_charge');
    }
    
    /**
     * Save term_condition setting for enterprise customer.
     */
    public function save_term_condition_setting(){
        $this->template->set_layout(false);
        $this->lang->load('account_setting');
        if(!APContext::isPrimaryCustomerUser()){
            $this->error_output("You don't have permission for this action!");
            return;
        }
        
        if($_POST){
            // Gets customer
            $customer_id = APContext::getCustomerCodeLoggedIn();
            $checked = $this->input->post('is_checked');

            // save email
            AccountSetting::set($customer_id, APConstants::CUSTOMER_TERM_CONDITION_SETTING, $checked);
            
            $this->success_output(lang("account_setting.save_success"));
            return;
        }
    }
    
    /**
     * show history of term & condition of enteprise customer.
     * @return type
     */
    public function hitory_term_condition(){
        $this->template->set_layout(false);
        $this->load->model('settings/terms_service_m');

        // If current request is ajax
        if ($this->is_ajax_request() && $_POST) {
            // Get input condition
            $array_condition = array ();
            $type = $this->input->get_post('type');
            $array_condition ['type'] = $type;

            // update term and condition for enteprrise customer
            $array_condition ['customer_id'] = APContext::getParentCustomerCodeLoggedIn();
        
            // update limit into user_paging.
            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);
            
            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging ['limit'] = $limit;
            
            // Call search method
            $query_result = $this->terms_service_m->get_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);
            
            // Process output data
            $total = $query_result ['total'];
            $datas = $query_result ['data'];
            
            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
            
            $i = 0;
            foreach ( $datas as $row ) {
                $url = APContext::getFullBasePath() . 'customers/term_of_service';
                if ($type == '2') {
                    $url = APContext::getFullBasePath() . 'customers/privacy';
                }
                if ($row->use_flag != '1') {
                    $url = $row->file_name;
                }
                $response->rows [$i] ['id'] = $row->id;
                $response->rows [$i] ['cell'] = array (
                    $row->id,
                    $url,
                    APUtils::convert_timestamp_to_date($row->created_date),
                    $row->use_flag,
                    $row->id 
                );
                $i ++;
            }
            
            echo json_encode($response);
            return;
        }
        
        $this->template->build('settings/history_term_condition');
    }
    
    /**
     * add/edit term and condition of enterprise customer.
     */
    public function add_term_condition_enterprise(){
        $this->template->set_layout(false);
        $this->load->model('settings/terms_service_m');

        $term_id = $this->input->get_post('id');
        $customer_id = APContext::getParentCustomerCodeLoggedIn();
        $content = "";
        if(!empty($term_id)){
            $term = $this->terms_service_m->get_by_many(array(
                "id" => $term_id,
                "customer_id" => $customer_id
            ));
            if(!empty($term)){
                $content = $term->content;
            }
        }
        
        // If current request is ajax
        if ($this->is_ajax_request() && $_POST) {
            
            $this->form_validation->set_rules($this->_term_condition_validation);
            if ($this->form_validation->run()) {
                $new_content = $this->input->post('content');
                
                // Get current main object
                $main = $this->terms_service_m->get_system_term_service(array (
                    "type" => '1',
                    "use_flag" => '1',
                    "customer_id" => $customer_id
                ));

                if ($main) {
                    // Change file name
                    $this->terms_service_m->update_by_many(array (
                            "id" => $main->id 
                    ), array (
                            "file_name" => $main->file_name . '_Old_' . APUtils::convert_timestamp_to_date($main->created_date, 'dmYHi') 
                    ));
                }

                // Update use flag of other record to '0'
                // Insert new record
                $this->terms_service_m->update_by_many(array (
                    "type" => '1',
                    "customer_id" => $customer_id
                ), array (
                    "use_flag" => '0' 
                ));

                // Insert new record
                $this->terms_service_m->insert(array (
                    "type" => '1',
                    "file_name" => "Terms&Conditions",
                    "use_flag" => '1',
                    "customer_id" => $customer_id,
                    "created_date" => now(),
                    "content" => $new_content . '<br/><br/> as of ' . date('d.m.Y') ,
                    "effective_date" => now()
                ));
                
                // set customers need confirm term & condition.
                $this->customer_m->update_by_many(array(
                    "status" => APConstants::OFF_FLAG,
                    "parent_customer_id" => $customer_id
                ), array(
                    "accept_terms_condition_flag" => APConstants::OFF_FLAG
                ));
                
                $this->success_output(lang('account_setting.save_term_condition_success'));
            }else{
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
            return;
        }
        
        $this->template->set('content', $content);
        $this->template->build('settings/add_term_condition');
    }
    
    /**
     * Save term_condition setting for enterprise customer.
     */
    public function save_api_access_setting(){
        $this->load->library('customers/CustomerMessageSetting');
        $this->load->library('invoices/invoices_api');
        $this->load->library('price/price_api');
        
        $this->template->set_layout(false);
        $this->lang->load('account_setting');
        if(!APContext::isPrimaryCustomerUser()){
            $this->error_output("You don't have permission for this action!");
            return;
        }
        
        if($_POST){
            // Gets customer
            $customer_id = APContext::getCustomerCodeLoggedIn();
            $type= $this->input->get_post('type');
            $start_date = now();
            
            $pricing_map = price_api::getDefaultPricingModel();
            $api_access_contract_terms = $pricing_map[5]['api_access']->contract_terms;
            $api_access_cost = $pricing_map[5]['api_access']->item_value;
            if ($api_access_contract_terms == 'yearly') {
                $end_date = $start_date + 365 * 24 * 60 * 60;
            } else if ($api_access_contract_terms == 'quarterly') {
                $end_date = $start_date + 365 * 24 * 60 * 60 / 4;
            } else if ($api_access_contract_terms == 'monthly') {
                $end_date = $start_date + 365 * 24 * 60 * 60 / 12;
            } else {
                $end_date = $start_date + 365 * 24 * 60 * 60;
            }
            
            // Check and display message
            if ($type == 'enable') {
                // Add cost
                // $start_contract_date = APUtils::convert_timestamp_to_date($start_date, APConstants::DATE_TIME_YYYYMMDD);
                // $end_contract_date = APUtils::convert_timestamp_to_date($end_date, APConstants::DATE_TIME_YYYYMMDD);
                
                // save API Access contract
                AccountSetting::set($customer_id, APConstants::CUSTOMER_API_ACCESS_SETTING, APConstants::ON_FLAG);
                AccountSetting::set_alias02($customer_id, APConstants::CUSTOMER_API_ACCESS_SETTING, $end_date);
                AccountSetting::set_alias03($customer_id, APConstants::CUSTOMER_API_ACCESS_SETTING, APConstants::ON_FLAG);
                
                // Add cost to invoice detail
                invoices_api::createEnterpriseInvoiceDetail($customer_id, APConstants::INVOICE_ACTIVITY_TYPE_API_ACCESS);
                
            } else if ($type == 'disable_end_contract') {
                $end_contract_date = AccountSetting::get_alias02($customer_id, APConstants::CUSTOMER_API_ACCESS_SETTING);
                $end_contract_date_str = '';
                if (!empty($end_contract_date)) {
                    $end_contract_date_str = APUtils::convert_timestamp_to_date($end_contract_date);
                }
                
                // Show message
                $message = sprintf(lang("account_setting.api_access_disable_end_contract"), $end_contract_date_str);
                $message_type = 'api_access';
                CustomerMessageSetting::create($customer_id, $message, $message_type);
                
                AccountSetting::set_alias03($customer_id, APConstants::CUSTOMER_API_ACCESS_SETTING, APConstants::OFF_FLAG);
            } else if ($type == 'disable_end_immediately') {
                // Show message
                $message = lang("account_setting.api_access_disable_end_immediately");
                $message_type = 'api_access';
                CustomerMessageSetting::create($customer_id, $message, $message_type);
                
                // save API Access contract
                AccountSetting::set($customer_id, APConstants::CUSTOMER_API_ACCESS_SETTING, APConstants::OFF_FLAG);
                AccountSetting::set_alias02($customer_id, APConstants::CUSTOMER_API_ACCESS_SETTING, $end_date);
                AccountSetting::set_alias03($customer_id, APConstants::CUSTOMER_API_ACCESS_SETTING, APConstants::OFF_FLAG);
            }
            
            $this->success_output(lang("account_setting.save_success"));
            return;
        }
    }
    
    /**
     * Save own domain setting for enterprise customer.
     */
    public function save_own_domain_setting(){
        $this->load->library('invoices/invoices_api');
        $this->template->set_layout(false);
        if(!APContext::isPrimaryCustomerUser()){
            $this->error_output("You don't have permission for this action!");
            return;
        }
        
        if($_POST){
            $own_domain = $this->input->post('own_domain');
            if (empty($own_domain)) {
                $this->error_output('Your domain is required input.');
                return;
            }
            
            // Gets customer
            $customer_id = APContext::getCustomerCodeLoggedIn();
            $own_domain_flag = $this->input->post('own_domain_checkbox');
            
            // save email
            AccountSetting::set($customer_id, APConstants::CUSTOMER_OWN_DOMAIN_KEY, $own_domain);
            
            $enterprise_token = base64_encode(APUtils::generateRandom(32));
            AccountSetting::set($customer_id, APConstants::NEW_REGISTRATION_TOKEN_ENTERPRISE_ACCOUNT, $enterprise_token);
            AccountSetting::set_alias02($customer_id, APConstants::CUSTOMER_OWN_DOMAIN_KEY, $own_domain_flag);
            
            // Add invoices cost
            if ($own_domain_flag == APConstants::ON_FLAG) {
                if (APUtils::endsWith($own_domain, APConstants::DEFAULT_CLEVVERMAIL_DOMAIN)) {
                    invoices_api::createEnterpriseInvoiceDetail($customer_id, APConstants::INVOICE_ACTIVITY_TYPE_CLEVVER_SUBDOMAIN);
                } else {
                    invoices_api::createEnterpriseInvoiceDetail($customer_id, APConstants::INVOICE_ACTIVITY_TYPE_OWN_SUBDOMAIN);
                }
            }
            
            $this->success_output(lang("account_setting.save_success"));
            return;
        }
    }
    
    /**
     * Load own domain widget setting
     */
    public function generate_own_domain_widget() {
        $this->template->set_layout(false);
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $title_login = $this->input->post('title_login');
        $button_text = $this->input->post('button_text');
        
        if (!empty($title_login)) {
            AccountSetting::set($customer_id, APConstants::CUSTOMER_OWN_DOMAIN_LOGIN_KEY, $title_login);
        }
        if (!empty($button_text)) {
            AccountSetting::set_alias02($customer_id, APConstants::CUSTOMER_OWN_DOMAIN_LOGIN_KEY, $button_text);
        }
        
        $token = AccountSetting::get($customer_id, APConstants::NEW_REGISTRATION_TOKEN_ENTERPRISE_ACCOUNT);
        
        // Generate widget code
        $html_widget = $this->load->view('own_domain_widget_template', array(
            "title" => $title_login,
            "button_text" => $button_text,
            "token" => $token
        ), true);
        
        $this->success_output('', array(
            'html_widget' => $html_widget
        ));
    }
    
    /**
     * Load own domain widget setting
     */
    public function own_domain_widget_setting() {
        $this->template->set_layout(false);
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $title_login = AccountSetting::get($customer_id, APConstants::CUSTOMER_OWN_DOMAIN_LOGIN_KEY);
        $button_text = AccountSetting::get_alias02($customer_id, APConstants::CUSTOMER_OWN_DOMAIN_LOGIN_KEY);
        
        if (empty($title_login)) {
            $title_login = "Login to ClevverMail";
            AccountSetting::set($customer_id, APConstants::CUSTOMER_OWN_DOMAIN_LOGIN_KEY, $title_login);
        }
        if (empty($button_text)) {
            $button_text = "Login";
            AccountSetting::set_alias02($customer_id, APConstants::CUSTOMER_OWN_DOMAIN_LOGIN_KEY, $button_text);
        }
        
        $token = AccountSetting::get($customer_id, APConstants::NEW_REGISTRATION_TOKEN_ENTERPRISE_ACCOUNT);
        // Generate widget code
        $html_widget = $this->load->view('own_domain_widget_template', array(
            "title" => $title_login,
            "button_text" => $button_text,
            "token" => $token
        ), true);
        
        $this->template->set('html_widget', $html_widget);
        $this->template->set('title_login', $title_login);
        $this->template->set('button_text', $button_text);
        $this->template->build('own_domain_widget_setting');
    }
    
    /**
     * crop image.
     */
    public function crop(){
        $this->template->set_layout(false);
        $image_path = $this->input->post('image_path');
        $x1 = $this->input->post('left');
        $y1 = $this->input->post('top');
        $w = $this->input->post('right');
        $h = $this->input->post('bottom');
        
        if(!empty($image_path)){
            Files::crop_image($image_path, $x1, $y1, $w, $h);
            
//            list( $width,$height ) = getimagesize( $image_path );
//            $newwidth = 241;
//            $newheight = 50;
//
//            $thumb = imagecreatetruecolor( $newwidth, $newheight );
//            $source = imagecreatefromjpeg($image_path);
//
//            imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
//            imagejpeg($thumb,$image_path,100); 
//
//
//            $im = imagecreatefromjpeg($image_path);
//            $dest = imagecreatetruecolor($w,$h);
//
//            imagecopyresampled($dest,$im,0,0,$x1,$y1,$w,$h,$w,$h);
//            imagejpeg($dest,$image_path, 100);
        }
        $this->success_output($image_path);
        return;
    }
    
    /**
     * Reset design color.
     */
    public function reset_design_color(){
        $customer_id = APContext::getParentCustomerCodeLoggedIn();
        $listColors = Settings::get_list(APConstants::COLORS_LIST_KEY);
        foreach($listColors as $color){
            AccountSetting::set($customer_id, $color->LabelValue, $color->ActualValue);
        }
        
        $this->success_output("");
        return;
    }
    
    
    
    /**
     * ===================================================================================================
     *                                  PRIVATE METHOD
     * ===================================================================================================
     */
    /**
     * 
     * @param type $str
     * @return type
     */
    private function convert_number($str){
        return str_replace(",", ".", $str);
    }
}