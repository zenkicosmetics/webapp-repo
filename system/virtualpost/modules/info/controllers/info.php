<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class info extends AccountSetting_Controller
{
    /**
     * Validation rules
     *
     * @var array
     */
    private $validation_rules = array(
        array(
            'field' => 'location_id',
            'label' => 'Location',
            'rules' => 'required'
        ),
        array(
            'field' => 'shipment_street',
            'label' => 'Street',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'shipment_postcode',
            'label' => 'Post Code',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'shipment_city',
            'label' => 'City',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'shipment_region',
            'label' => 'Region',
            'rules' => ''
        ),
        array(
            'field' => 'shipment_country_id',
            'label' => 'Country',
            'rules' => 'required'
        ),
        array(
            'field' => 'shipment_service_id',
            'label' => 'Shipping Service',
            'rules' => 'required'
        ),
        array(
            'field' => 'shipment_service_description',
            'label' => 'Shipping Service Description',
            'rules' => ''
        ),
        array(
            'field' => 'number_of_parcels',
            'label' => 'Number of parcels',
            'rules' => 'required|numeric'
        ),
        array(
            'field' => 'length',
            'label' => 'Length',
            'rules' => 'required|numeric'
        ),
        array(
            'field' => 'width',
            'label' => 'Width',
            'rules' => 'required|numeric'
        ),
        array(
            'field' => 'height',
            'label' => 'Height',
            'rules' => 'required|numeric'
        ),
        array(
            'field' => 'weight',
            'label' => 'Weight',
            'rules' => 'required'
        ),
        array(
            'field' => 'currency_id',
            'label' => 'Currency',
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

        // load the theme_example view
        $this->load->model('price/pricing_m');
        $this->load->model('settings/terms_service_m');
        $this->load->model('customers/customer_m');
        $this->load->model('addresses/location_m');
        $this->load->model('addresses/customers_address_m');
        
        $this->load->model(array(
            'phones/phone_area_code_m',
            'settings/countries_m',
            'phones/pricing_phones_number_m',
            'phones/pricing_phones_outboundcalls_m',
            'phones/pricing_phones_outboundcalls_customer_m',
            "settings/currencies_m"
        ));
        
        ci()->load->library(array(
            'account/account_api',
            'customers/customers_api',
            'phones/sonetel',
            'phones/phones_api'
        ));
        
        $this->lang->load('info');
    }

    /**
     * View pricing information.
     */
    public function view_pricing_inline()
    {
        // load API
        $this->load->library("price/price_api");
        $this->load->library("customers/customers_api");
        $this->load->library("addresses/addresses_api");
        $this->load->library("settings/settings_api");
        $this->load->library("mailbox/mailbox_api");

        $customer_id = APContext::getCustomerCodeLoggedIn();
        $postbox = mailbox_api::getFirstLocationBy($customer_id);
        $firstLocationID = is_object($postbox) ? $postbox->location_available_id : 0;
        $locationId = $this->input->get_post("location_id", 0);

        if (!$locationId) {
            $locationId = $firstLocationID;
        }

        $type = 0;
        if ($locationId == $firstLocationID) {
            $type = $postbox->type;
        }

        $currencyId = $this->input->get_post("currency_id", 0);
        // Get don gia cua tat ca cac loai account type
        $pricing_map = price_api::getPricingMapByLocationId($locationId);

        // Gets list public location.
        $list_access_location = addresses_api::getLocationPublic();

        // Gets customer information
        $account = customers_api::getCustomerByID($customer_id);

        // Get currencies information
        $list_currencies = settings_api::getAllCurrencies();
        if ($currencyId) {
            $selected_currency = settings_api::getCurrencyByID($currencyId);
        } else {
            $selected_currency = customers_api::getStandardCurrency($customer_id);
        }
        $decimal_separator = customers_api::getStandardDecimalSeparator($customer_id);

        $this->template->set('list_access_location', $list_access_location);
        $this->template->set('location_id', $locationId);
        $this->template->set('pricing_map', $pricing_map);
        $this->template->set('account', $account);
        $this->template->set('list_currencies', $list_currencies);
        $this->template->set('selected_currency', $selected_currency);
        $this->template->set('decimal_separator', $decimal_separator);
        $this->template->set('account_type', $type);
        $this->template->build('view_pricing');
    }

    /**
     * View term information.
     */
    public function view_term_inline()
    {
        $popup_flag = $this->input->get_post("popup_flag");
        if($popup_flag == 1){
            $this->template->set_layout(FALSE);
        }
        
        if(APContext::isUserEnterprise(APContext::getCustomerCodeLoggedIn())){
            $content = settings_api::getTermAndConditionEnterprise(APContext::getParentCustomerCodeLoggedIn());
        }else{
            $content = settings_api::getTermAndCondition();
        }
        
        $this->template->set('content', $content);
        
        $this->template->set('popup_flag', $popup_flag);
        
        $this->template->build('view_content');
    }

    /**
     * How it works.
     */
    public function how_it_works()
    {
        $popup_flag = $this->input->get_post("popup_flag");
        if($popup_flag == 1){
            $this->template->set_layout(FALSE);
        }
        
        $customer_id = APContext::getCustomerCodeLoggedIn();
        // Get all postbox of this customer
        $list_postbox = $this->postbox_m->get_many_by_many(array(
            "customer_id" => $customer_id,
            "deleted <> " => '1',
            "(postbox_name IS NOT NULL AND postbox_name !='')" => null
        ));

        $locate = $this->location_m->get_location_paging(array('location.public_flag' => 1), 0, 100000, '');
        $customer = $this->customer_m->get_current_customer_info();
        $postbox_count = $this->postbox_m->get_postbox_count_by_customer($customer_id);
        $data_main_postbox_setting = account_api::main_postbox_setting();

        $decimal_separator = $this->customer_m->get_standard_setting_decimal_separator($customer_id);
        $currency = customers_api::getStandardCurrency($customer_id);

        $open_balance = APUtils::getCurrentBalance($customer_id);
        $open_balance_this_month = APUtils::getCurrentBalanceThisMonth($customer_id);
        $postbox = $this->postbox_m->getFirstLocationBy($customer_id);
        $next_invoices_display = InvoiceUtils::getCurrentActivitiesInvoice($customer_id);


        $info = $this->customer_m->get_by('customer_id', $customer_id);
        if ($info->new_account_type != '' && $info->plan_date_change_account_type != '') {
            $message = lang('change_account_info_message');
            $new_account_name = lang('account_type_' . $info->new_account_type);
            $message = sprintf($message, $new_account_name, APUtils::displayDate($info->plan_date_change_account_type));
            $this->template->set("message", $message);
        }
        $items = Settings::get_list(APConstants::ACCOUNT_TYPE);

        $data = array();
        foreach ($postbox_count as $item) {
            $data [$item->type] = $item->box_count;
        }
        
        $currencies = $this->currencies_m->get_many_by_many(array(
            'active_flag' => APConstants::ON_FLAG
        ), 'currency_id, currency_short');
        $selected_currency = $this->customer_m->get_standard_setting_currency($customer_id);
        $selected_currency_id = $selected_currency->currency_id;
        
        // get customer product setting
        $customer_product_setting = CustomerProductSetting::get_activate_flags($customer_id);
        $this->template->set("customer_product_setting", $customer_product_setting);
        
        $this->template->set('currencies', $currencies);
        $this->template->set('selected_currency_id', $selected_currency_id);

        $this->template->set("postbox_count", $data);
        $this->template->set("info", $info)->set("acct_type", $items);
        $this->template->set("open_balance_this_month", $open_balance_this_month);
        $this->template->set("open_balance", $open_balance);
        $this->template->set("postboxs", $data_main_postbox_setting['postboxes']);
        $this->template->set("main_postbox_id", $data_main_postbox_setting['main_postbox_id']);
        $this->template->set("main_postbox_setting",$data_main_postbox_setting['main_postbox_setting']);
        $this->template->set("next_invoices", $next_invoices_display);
        $this->template->set('currency', $currency);
        $this->template->set('decimal_separator', $decimal_separator);
        $this->template->set('locate', $locate['data']);
        $this->template->set('customer', $customer);
        $this->template->set("list_postbox", $list_postbox);
        $this->template->set("postbox", $postbox);
        $this->template->build('how_it_works');
    }

    /**
     * View privacy information.
     */
    public function view_privacy_inline()
    {
        $content = settings_api::getPrivacyOfSystem();
        $this->template->set('content', $content);
        $this->template->build('view_content');
    }
   
    public function shipping_calculator()
    {
        $this->load->library(array(
            'shipping/shipping_api',
            'shipping/ShippingConfigs',
            'common/common_api',
            'settings/settings_api',
            'customers/customers_api',
            'addresses/addresses_api'
        ));
        $this->lang->load('shipping/shipping');

        $customerID = APContext::getCustomerCodeLoggedIn();
        $customer = $this->customer_m->get($customerID);
        log_message('error', $this->input->get_post('multiple_number_shipment'));
        if ($this->is_ajax_request()) {
            $this->template->set_layout(FALSE);
            $customerAddress = addresses_api::getCustomerAddress($customerID);
            $shippingInfo = array(
                ShippingConfigs::CUSTOMER_ID => $customerID,
                ShippingConfigs::LOCATION_ID => $this->input->get_post("location_id"),
                ShippingConfigs::SERVICE_ID => $this->input->get_post("shipment_service_id"),
                ShippingConfigs::SHIPPING_TYPE => $this->input->get_post("shipment_type_id"),
                ShippingConfigs::CUSTOMS_VALUE => $this->input->get_post("total_insured_value"),
                ShippingConfigs::STREET => $this->input->get_post("shipment_street"),
                ShippingConfigs::POSTAL_CODE => $this->input->get_post("shipment_postcode"),
                ShippingConfigs::CITY => $this->input->get_post("shipment_city"),
                ShippingConfigs::REGION => $this->input->get_post("shipment_region"),
                ShippingConfigs::COUNTRY_ID => $this->input->get_post("shipment_country_id"),
                ShippingConfigs::NAME => !empty($customerAddress) ? $customerAddress->shipment_address_name: "",
                ShippingConfigs::PHONE_NUMBER => !empty($customerAddress) ? $customerAddress->shipment_phone_number: "",
                ShippingConfigs::EMAIL => $customer->email,
                ShippingConfigs::COMPANY_NAME => !empty($customerAddress) ? $customerAddress->shipment_company: ""
            );
            $separate_package_flag = true;
           
            $result = shipping_api::shipping_calculator(
                    $customerID,
                    $shippingInfo,
                    $this->input->get_post('number_of_parcels'),
                    $this->input->get_post('length'),
                    $this->input->get_post('width'),
                    $this->input->get_post('height'),
                    $this->input->get_post('weight') * 1000, // Unit kg in the screen
                    $this->input->get_post('multiple_quantity'),
                    $this->input->get_post('multiple_number_shipment'),
                    $this->input->get_post('multiple_length'),
                    $this->input->get_post('multiple_width'),
                    $this->input->get_post('multiple_height'),
                    $this->input->get_post('multiple_weight'), // Unit is g in the screen
                    $this->input->get_post('currency_id'),
                    $separate_package_flag);
            
            if($result['status']){
                $this->success_output('', $result['data']);
            }else{
                //if(trim(strip_tags($result['data']['errors'])) == 'Service is not allowed.'){
                $result['data']['errors'] = lang('shipping_service.cannot_calculate');
                //}
                $this->error_output('', $result['data']);
            }
            return ;
        } else {
            $VAT = APUtils::getVatRateOfCustomer($customerID)->rate;
            $this->template->set('VAT', $VAT * 100);

            // Show all locations
            $locations = addresses_api::getLocationPublic();
            $this->template->set('locations', $locations);

            // Show all countries
            $countries = settings_api::getAllCountriesForDropDownList();
            $this->template->set('countries', $countries);

            // Show all currencies
            $currencies = settings_api::getAllCurrenciesForDropDownList();
            $this->template->set('currencies', $currencies);

            // Get standard settings (currency, decimal separator)
            $standardCurrency = customers_api::getStandardCurrency($customerID);
            $standardDecimalSeparator = customers_api::getStandardDecimalSeparator($customerID);
            $this->template->set('standard_currency', $standardCurrency);
            $this->template->set('decimal_separator', $standardDecimalSeparator);

            // Show customer address by default
            $customerAddress = addresses_api::getCustomerAddress($customerID);
            $this->template->set('shipment_street', !empty($customerAddress) ? $customerAddress->shipment_street: "");
            $this->template->set('shipment_postcode', !empty($customerAddress) ? $customerAddress->shipment_postcode:"");
            $this->template->set('shipment_city', !empty($customerAddress) ? $customerAddress->shipment_city:"");
            $this->template->set('shipment_region', !empty($customerAddress) ? $customerAddress->shipment_region:"");
            $this->template->set('shipment_country_id', !empty($customerAddress) ? $customerAddress->shipment_country:"");

            // Display the page finally
            $this->template->build('shipping_calculator');
        }
        
    }

    /**
     * Get the list of available standard shipment services per location
     */
    public function get_shipping_services_by_location()
    {
        $this->load->library('addresses/addresses_api');
        $this->load->library('shipping/shipping_api');

        $this->template->set_layout(FALSE);

        $locationID = $this->input->get_post("location_id");
        $location = addresses_api::getLocationByID($locationID);
        if(empty($location)){
            $this->success_output('success', array());
            return;
        }
        $shippingServiceIDs = isset($location->available_shipping_services) ? $location->available_shipping_services : '';
        if(empty($shippingServiceIDs)){
            $this->success_output('success', array());
            return;
        }
        
        $shippingServiceIDs = explode(',', $shippingServiceIDs);
        $shipping_service_type = '';
        $listShippingServices = shipping_api::getListShippingServicesByIDs($shippingServiceIDs, $shipping_service_type, true);

        $this->success_output('success', $listShippingServices);
        return true;
    }

    /**
     * Get the selected shipment service's description
     */
    public function get_shipping_service_description()
    {
        $this->load->library('shipping/shipping_api');

        $this->template->set_layout(FALSE);

        $shippingServiceID = $this->input->get_post("shipment_service_id", 0);
        $shippingService = shipping_api::getShippingServiceByID($shippingServiceID);

        $this->success_output('success', array(
            'shipment_service_description' => $shippingService->long_desc
        ));
        return true;
    }

    /**
     * Show the form view of input parcels information
     */
    public function input_parcels_info()
    {
        $this->template->set_layout(FALSE);

        $mode = $this->input->get_post('mode');
        $parcelsData = json_decode($this->input->get_post('parcelsData'));
        $lines = $this->input->get_post('lines', 1);

        // Display the current page
        $this->template->set('mode', $mode);
        $this->template->set('parcels', $parcelsData);
        $this->template->set('lines', $lines);

        $this->template->build('input_parcels_info');
    }

    /**
     * Recalculate shipping values in other currency
     */
    public function convert_currency()
    {
        $this->load->model('settings/currencies_m');
        $this->load->model('customers/customer_m');
        $this->template->set_layout(FALSE);

        $converted_currency_id = $this->input->get_post('converted_currency_id');
        $base_postal_charge = $this->input->get_post('base_postal_charge', 0);
        $base_customs_handling = $this->input->get_post('base_customs_handling', 0);
        $base_handling_charges = $this->input->get_post('base_handling_charges', 0);
        $base_VAT = $this->input->get_post('base_VAT', 0);
        $base_total_charge = $this->input->get_post('base_total_charge', 0);

        $currency = $this->currencies_m->get($converted_currency_id);
        $currency_rate = $currency->currency_rate;
        $decimal_separator = $this->customer_m->get_standard_setting_decimal_separator();

        $converted_postal_charge = APUtils::convert_currency($base_postal_charge, $currency_rate, 2, $decimal_separator);
        $converted_customs_handling = APUtils::convert_currency($base_customs_handling, $currency_rate, 2, $decimal_separator);
        $converted_handling_charges = APUtils::convert_currency($base_handling_charges, $currency_rate, 2, $decimal_separator);
        $converted_VAT = APUtils::convert_currency($base_VAT, $currency_rate, 2, $decimal_separator);
        $converted_total_charge = APUtils::convert_currency($base_total_charge, $currency_rate, 2, $decimal_separator);

        $this->success_output(lang('convert_currency_success'), array(
            'postal_charge' => $converted_postal_charge,
            'customs_handling' => $converted_customs_handling,
            'handling_charges' => $converted_handling_charges,
            'VAT' => $converted_VAT,
            'total_charge' => $converted_total_charge
        ));
        return true;
    }
    
    /**
     * phone pricing page.
     */
    public function phone_pricing(){
        $is_dialog = $this->input->get_post("show_dialog");
        
        if($is_dialog == '1'){
            $this->template->set_layout(false);
        }
        
        // Get list country
        $list_country = phones_api::get_all_countries();
        $this->template->set("list_country", $list_country);
        
        // Default get area of USA (country_id = 430)
        $list_area = $this->phone_area_code_m->get_many_by_many(array(
            'country_id' => 430
        ));
        $currencies = $this->currencies_m->get_many_by_many(array(
            'active_flag' => APConstants::ON_FLAG
        ));
        
        if(APContext::isEnterpriseCustomer()){
            $list_forwarding = $this->pricing_phones_outboundcalls_customer_m->get_many_by_many(array(
                "customer_id" => APContext::getParentCustomerCodeLoggedIn()
            ));
        }else{
            $list_forwarding = $this->pricing_phones_outboundcalls_m->get_all();
        }
        
        // get all pricing plan
        $price_plan = $this->pricing_phones_number_m->get_all();
        
        $this->template->set("list_forwarding", $list_forwarding);
        $this->template->set("price_plan", $price_plan);
        $this->template->set("list_area", $list_area);
        $this->template->set("currencies", $currencies);
        $this->template->set("is_dialog", $is_dialog);
        if($is_dialog == '1'){
            $this->template->build('partial/phone_pricing_partial');
        }else{
            $this->template->build('phone_pricing');
        }
    }
    
    /**
     * calculate phone number rate.
     */
    public function cal_phone_number_pricing_plan(){
        $this->template->set_layout(false);
        $customer_id = APContext::getCustomerCodeLoggedIn();
        if($_POST){
            $country_code = $this->input->post('country_code');
            $country_code_forwarding = $this->input->post('country_code_forwarding');
            $area_code = $this->input->post('area_code');
            $currency_short = $this->input->post('currency');
            $minutes = $this->input->post('minutes');
            
            $currency = $this->currencies_m->get_by('currency_short', $currency_short);
            $decimal_separator = $this->customer_m->get_standard_setting_decimal_separator($customer_id);
            
            $data = array(
                "setup_fee" => 0,
                "monthly_fee" => 0,
                "call_forwarding_fee" => 0,
                "estimated_cost" => 0,
            );
            // TODO: AREA CODE
            $price_plan = $this->pricing_phones_number_m->get_by_many(array(
                "country_code_3" => $country_code
            ));
            
            if(!empty($price_plan)){
                $data['setup_fee'] = $price_plan->one_time_fee;
                $data['monthly_fee'] = $price_plan->recurring_fee;
            }
            
            if(!empty($country_code_forwarding)){
                if(APContext::isEnterpriseCustomer()){
                    $forwarding_fee = $this->pricing_phones_outboundcalls_customer_m->get_by_many(array(
                        "country_code_3" => $country_code_forwarding,
                        "customer_id" => APContext::getParentCustomerCodeLoggedIn()
                    ));
                }else{
                    $forwarding_fee = $this->pricing_phones_outboundcalls_m->get_by_many(array(
                        "country_code_3" => $country_code_forwarding
                    ));
                }
                if(!empty($forwarding_fee)){
                    $data['call_forwarding_fee'] = $minutes * $forwarding_fee->usage_fee * (1 + ($forwarding_fee->usage_fee_upcharge/100));
                }
            }
            $data['estimated_cost'] = $data['setup_fee'] + $data['monthly_fee'] + $data['call_forwarding_fee'];
            
            // format number
            $data = array(
                "setup_fee" => APUtils::convert_currency($data['setup_fee'], $currency->currency_rate, 2, $decimal_separator).' '.$currency->currency_short,
                "monthly_fee" => APUtils::convert_currency($data['monthly_fee'], $currency->currency_rate, 2, $decimal_separator).' '.$currency->currency_short,
                "call_forwarding_fee" => APUtils::convert_currency($data['call_forwarding_fee'], $currency->currency_rate, 2, $decimal_separator).' '.$currency->currency_short,
                "estimated_cost" => $currency->currency_sign. ' ' .APUtils::convert_currency($data['estimated_cost'], $currency->currency_rate, 2, $decimal_separator),
            );
            
            $this->success_output('', $data);
            return;
        }
    }
    
    /**
     * load phone number list by country and area code.
     */
    public function load_phone_number_list(){
        $this->template->set_layout(false);
        if($_POST){
            $list_data = array();
            $country_code = $this->input->post('country_code');
            $area_code = $this->input->post('area_code');
            
            // gets list phone number.
            $list_data = phones_api::get_list_phone_number_by($country_code, $area_code);
            
            echo json_encode($list_data);
            return;
        }
    }
    
    /**
     * load outbound call list
     */
    public function load_outboundcall_list(){
        $this->template->set_layout(false);
        $enquiry = $this->input->get_post('enquiry');
        
        $array_condition = array();
        if(!empty($enquiry)){
            $array_condition ["pricing_phones_outboundcalls.pricing_name like '%".APUtils::sanitizing($enquiry)."%'"] = null;
        }
        
        // Gets limit request.
        $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APConstants::NUMBER_RECORD_PER_PAGE_CODE;
        
        // Get paging input
        $input_paging = $this->get_paging_input();
        $input_paging ['limit'] = $limit;

        // Call search method
        $query_result = $this->pricing_phones_outboundcalls_m->get_price_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

        // Process output data
        $total = $query_result['total'];
        $rows = $query_result['data'];

        // Get output response
        $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);

        $i = 0;
        foreach ($rows as $row) {
            $response->rows [$i] ['id'] = $row->id;
            $response->rows [$i] ['cell'] = array(
                $row->id,
                $row->pricing_name,
                APUtils::number_format($row->usage_fee * (1 + ($row->usage_fee_upcharge/100))),
                $row->id,
            );
            $i++;
        }
        echo json_encode($response);
        
        return;
    }
    
    /**
     * get phone description limitation
     */
    public function get_phone_limitation(){
        $this->template->set_layout(false);
        
        $country_code = $this->input->get_post('country_code');
        $phone_number = $this->input->get_post('phone_number');
        
        $result = $this->sonetel->get_phonenumber_description($country_code, $phone_number);

        $message = "";
        if(!empty($result->response[0])){
            $message = $this->buildPhoneDescriptionMessage($result->response[0]);
        }
        $this->success_output('', $message);
        return;
    }
    
    /**
     * View pricing information.
     */
    public function api_info()
    {
        // load API
        $this->load->library("price/price_api");
        $this->load->library("customers/customers_api");

        $customer_id = APContext::getCustomerCodeLoggedIn();
        // Gets customer information
        $customer = customers_api::getCustomerByID($customer_id);
        $this->template->set('customer', $customer);
        $this->template->build('api_info');
    }
    
    /**
     * build message from 
     * @param type $phonenumber
     */
    private function buildPhoneDescriptionMessage($phonenumber){
        $message = "<ol>";
        
        if($phonenumber->sms_support == 'yes'){
            $message .= "<li>This number can receive SMS.</li>";
        }
        
        if($phonenumber->fax_support == 'yes'){
            $message .= "<li>Inbound fax can be activated on this number.</li>";
        }
        
        if($phonenumber->addr_req != 'none'){
            $message .= "<li>This number must be verification address.</li>";
        }
        
        if($phonenumber->freetest != 'no'){
            $message .= "<li>Free Trials for this number.</li>";
        }
        
        if($phonenumber->freetest == 'no' && ($phonenumber->price_category == 'gold++' || $phonenumber->price_category == 'gold+')){
            $message .= "<li>Free Trials for Gold Numbers.</li>";
        }
        
        $message .= "</ol>";
        return $message;
    }
}