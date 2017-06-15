<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Location extends AccountSetting_Controller {
    
    /**
     * Validation for basic profile data. The rest of the validation is built by streams.
     *
     * @var array
     */
    private $validation_rules = array(
        array(
            'field' => 'location_name',
            'label' => 'lang:location_name',
            'rules' => 'required|validname|max_length[60]'
        ),
        array(
            'field' => 'partner_id',
            'label' => 'lang:partner_id',
            'rules' => ''
        ),
        array(
            'field' => 'country_id',
            'label' => 'lang:country',
            'rules' => ''
        ),
        array(
            'field' => 'city',
            'label' => 'lang:city',
            'rules' => 'required|validname|max_length[255]'
        ),
        array(
            'field' => 'region',
            'label' => 'lang:region',
            'rules' => 'required|validname|max_length[255]'
        ),
        array(
            'field' => 'street',
            'label' => 'lang:street',
            'rules' => 'required|validname|max_length[255]'
        ),
        array(
            'field' => 'phone_number',
            'label' => 'lang:phone_number',
            'rules' => 'required|max_length[30]'
        ),
        array(
            'field' => 'postcode',
            'label' => 'lang:postcode',
            'rules' => 'required|postcode|max_length[10]'
        ),
        array(
            'field' => 'image_path',
            'label' => 'lang:image_path',
            'rules' => ''
        ),
        array(
            'field' => 'device_id',
            'label' => 'lang:device_id',
            'rules' => ''
        ),
        array(
            'field' => 'public_flag',
            'label' => 'lang:public_flag',
            'rules' => ''
        ),
        array(
            'field' => 'share_external_flag',
            'label' => 'lang:share_external_flag',
            'rules' => ''
        ),
        array(
            'field' => 'primary_letter_shipping',
            'label' => 'Standard national letter shipping',
            'rules' => ''
        ),
        array(
            'field' => 'primary_international_letter_shipping',
            'label' => 'Standard international letter shipping',
            'rules' => ''
        ),
        array(
            'field' => 'standard_national_parcel_service',
            'label' => 'Standard national parcel service',
            'rules' => ''
        ),
        array(
            'field' => 'standard_international_parcel_service',
            'label' => 'Standard international parcel service',
            'rules' => ''
        ),
        array(
            'field' => 'sent_daily_reminder_flag',
            'label' => 'sent_daily_reminder_flag',
            'rules' => ''
        ),
        array(
            'field' => 'only_express_shipping_flag',
            'label' => 'only_express_shipping_flag',
            'rules' => ''
        ),
        array(
            'field' => 'shared_office_space_flag',
            'label' => 'shared_office_space_flag',
            'rules' => ''
        ),
        array(
            'field' => 'shared_office_image_path',
            'label' => 'shared_office_image_path',
            'rules' => ''
        ),
        array(
            "field" => 'location_phone_number',
            'label' => 'lang:phone_number',
            'rules' => 'required|max_length[30]'
        )
        
    );
    
    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     *
     * @todo Document properly please.
     */
    public function __construct() {
        parent::__construct();
        
        // Load the required classes
        $this->load->model('addresses/location_m');
        $this->load->model('office/location_office_feature_m');
        $this->load->model('office/location_office_m');

        $this->load->model('settings/countries_m');
        $this->load->model('addresses/location_pricing_m');
        $this->load->model('mailbox/postbox_m');
        $this->load->model('price/pricing_template_m');
        $this->load->model('partner/partner_m');
        $this->load->model('device/digital_devices_m');
        $this->load->model('settings/currencies_m');
        $this->load->library('form_validation');
        $this->load->library('common/common_api');
        $this->load->library('customers/customers_api');
        $this->lang->load('addresses/address');
        $this->load->model("report/report_by_location_m");
        $this->load->library('addresses/addresses_api');

        $this->load->library('files/files');
        
    }

    /**
     * List all users
     */
    public function index()
    {
        // Get input condition
        $array_condition = array();

        // If current request is ajax
        if ($this->is_ajax_request()) {
            $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
            // update limit into user_paging.
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10;
            $array_condition['location_customers.parent_customer_id'] = $parent_customer_id;

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;

            // Call search method
            $query_result = $this->location_m->get_location_customer_paging($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);

            // Process output data
            $total = $query_result ['total'];
            $rows = $query_result ['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);

            $i = 0;
            $date_format = 'd.m.Y';
            $report_month = date('Ym');
            foreach ($rows as $row) {
                $invoice_by_location = $this->report_by_location_m->get_by_many(array(
                    "left(invoice_month, 6) =" => $report_month,
                    "location_id" => $row->id
                ));
                $number_user = !empty($invoice_by_location) ? $invoice_by_location->number_of_customers_share : 0;
                $number_postbox = customers_api::countPostboxByLocation($row->id);
                $status = 'N/A';
                if (!empty($row->last_ping_received)) {
                    $deviceLastPing = strtotime($row->last_ping_received);
                    $status = 'online';
                    if ($deviceLastPing < (time() - (10 * 60))) {
                        if ($deviceLastPing < $row->panel_created_date) {
                            $deviceLastPing = $row->panel_created_date;
                        }
                        $status = 'offline since ' . APUtils::viewDateFormat($deviceLastPing,$date_format.APConstants::TIMEFORMAT_OUTPUT02);
                    }
                }
                $response->rows[$i]['id'] = $row->id;
                $response->rows[$i]['cell'] = array(
                    $row->id,
                    $row->location_name,
                    $number_user,
                    $number_postbox,
                    $row->share_external_flag,
                    $status,
                    $row->public_flag,
                    $row->id
                );
                $i++;
            }

            echo json_encode($response);
        } else {
            // Display the current page
            $this->template->build('location/index');
        }
    }

    /**
     * Method for handling different form actions
     */
    public function add()
    {
        ci()->load->library('addresses/addresses_api');
        ci()->load->library('price/price_api');
        ci()->load->library('partner/partner_api');
        ci()->load->library('settings/settings_api');
        ci()->load->library('device/device_api');
        ci()->load->model('addresses/location_envelope_types_m');
        ci()->load->model('addresses/location_customers_m');
        ci()->load->model('partner/partner_m');
        ci()->load->model('cases/cases_service_partner_m');
        $this->load->model('addresses/location_users_m');
        $this->load->library('invoices/invoices_api');
        $this->load->model('device/digital_devices_m');
        
        $this->template->set_layout(FALSE);

        $location = new stdClass();
        $location->id = '';

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->load->library('files/files');
            $this->form_validation->set_rules($this->validation_rules);

            $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
            $location_name               = $this->input->post('location_name');
            $street                      = $this->input->post('street');
            $postcode                    = $this->input->post('postcode');
            $city                        = $this->input->post('city');
            $region                      = $this->input->post('region');
            $country_id                  = $this->input->post('country_id');
            $phone_number                = $this->input->post('phone_number');
            $location_phone_number       = $this->input->post('location_phone_number');
            $public_flag                 = $this->input->post('public_flag');
            $location_id                 = $this->input->post('id');
            $only_express_shipping_flag  = (int)$this->input->post('only_express_shipping_flag');
            $email                       = $this->input->post('email');
            $phone_number_flag           = $this->input->post('phone_number_flag');
            $partner_id                  = '';
            $business_postbox_text       = $this->input->post('business_postbox_text');
            $office_space_active_flag    = $this->input->post('office_space_active_flag');
            $shared_office_space_flag    = $this->input->post('shared_office_space_flag');
            $booking_email_address       = $this->input->post('booking_email_address');
            $sent_daily_reminder_flag    = $this->input->post('sent_daily_reminder_flag');
            $available_shipping_services = $this->input->post('available_shipping_services', '');
            if ($available_shipping_services && is_array($available_shipping_services)) {
                $available_shipping_services = implode(',', $available_shipping_services);
            }
            $device_id = $this->input->post('device_id');
            $shipping_factor_fl = '';
            if (empty($public_flag)) {
                $public_flag = 0;
            }
            $rev_share = '';
            
            // $share_external_flag = $this->input->post('share_external_flag');
            $share_external_flag = 0;
            $auto_add_location_partner = '1';
            $parent_customer = APContext::getCustomerByID($parent_customer_id);
            $customer_address = CustomerUtils::getCustomerAddressByID($parent_customer_id);
            if ($auto_add_location_partner == '1') {
                $rev_share = 75;
                // Check if customer already is parner
                $partner_check = ci()->cases_service_partner_m->get_by_many(array('email' => $parent_customer->email));
                if (empty($partner_check)) {
                    // Insert data to database
                    $partner_id = $this->partner_m->insert( array(
                        "partner_name" => $customer_address->shipment_address_name,
                        "company_name" => $customer_address->shipment_company,
                        "location_country" => $customer_address->shipment_country,
                        "invoicing_street" => $customer_address->invoicing_street,
                        "invoicing_zipcode" => $customer_address->invoicing_postcode,
                        "invoicing_city" => $customer_address->invoicing_city,
                        "invoicing_region" => $customer_address->invoicing_region,
                        "invoicing_country" => $customer_address->invoicing_country,
                        "price_model" => APConstants::DEfAULT_PRICING_MODEL_TEMPLATE,
                        "partner_type" => APConstants::PARTNER_LOCATION_TYPE,
                        "rev_share_in_percent" => $rev_share,
                        //"share_external_flag" => $share_external_flag
                    ));

                    $partner_code = APUtils::generatePartnerCode($partner_id);
                    $this->partner_m->update_by_many(array(
                        "partner_id" => $partner_id
                    ), array(
                        "partner_code" => $partner_code
                    ));
                    // Create default parner id
                    $this->cases_service_partner_m->insert(array(
                        "partner_id" => $partner_id,
                        "partner_name" => $customer_address->shipment_company,
                        "main_contact_point" => $customer_address->shipment_address_name,
                        "email" => $parent_customer->email,
                        "phone" => $customer_address->shipment_phone_number,
                        "created_date" => now()
                    ));
                    
                    
                } else {
                    $partner_id = $partner_check->partner_id;
                }
            }
            if ($this->form_validation->run()) {
                // Set default pricing tempalte
                $pricing_template_id = APConstants::DEfAULT_PRICING_MODEL_TEMPLATE;

                $paramNames = array("location_name", "street", "postcode", "city", "region", "country_id", "partner_id" , "available_shipping_services", "device_id", "shipping_factor_fl", 
                                    "public_flag", "rev_share", "business_postbox_text","office_space_active_flag", "phone_number"
                                    , "share_external_flag", "pricing_template_id", "location_phone_number"
                                    , 'only_express_shipping_flag', 'email', 'phone_number_flag', 'shared_office_space_flag', 'booking_email_address', 'sent_daily_reminder_flag');

                $paramValues = array($location_name, $street, $postcode, $city, $region, $country_id, $partner_id, $available_shipping_services, $device_id, $shipping_factor_fl, 
                                    $public_flag, $rev_share, $business_postbox_text, $office_space_active_flag, $phone_number
                                    , $share_external_flag, $pricing_template_id,$location_phone_number
                                    , $only_express_shipping_flag, $email, $phone_number_flag, $shared_office_space_flag, $booking_email_address,$sent_daily_reminder_flag);
                
                if(empty($location_id)){
                    $location_id = addresses_api::createLocation($paramNames, $paramValues);
                }else{
                    // fix for upload image case in add location.
                    $data_update = base_api::getArrayParams($paramNames, $paramValues);
                    // update location information
                    addresses_api::updateLocationByID($location_id, $data_update);
                }
                
                $this->load->model('users/user_m');
                $this->load->model('users/group_user_m');
                $password = APUtils::generateRandom(8);
                $group_id = APConstants::GROUP_LOCATION_ADMIN;
                // Check existing user admin
                $location_admin_check = $this->user_m->get_by_many(array(
                    'email' => $parent_customer->email
                ));
                if (empty($location_admin_check)) {
                    $user_id = $this->ion_auth->register($parent_customer->email, $password, $parent_customer->email, $group_id,
                        array(
                            "display_name" => $customer_address->shipment_company,
                            "first_name" => $customer_address->shipment_company,
                            "last_name" => '',
                            "active" => APConstants::ON_FLAG,
                            "partner_id" => $partner_id,
                            "location_available_id" => ''
                        ));
                } else {
                    $user_id = $location_admin_check->id;
                }
                // /$update_data ['partner_id'] = $partner_id;
                $this->user_m->update_by_many(array(
                    'id' => $user_id
                ), array(
                    "partner_id" => $partner_id,
                    "delete_flag" => APConstants::OFF_FLAG,
                    "password" => $parent_customer->password,
                    "salt" => null
                ));

                // Check if not exist this role
                $location_admin_role = $this->group_user_m->get_by_many(array(
                    "user_id" => $user_id,
                    "group_id" => $group_id
                ));
                if (empty($location_admin_role)) {
                    $this->group_user_m->insert(array(
                        "user_id" => $user_id,
                        "group_id" => $group_id
                    ));
                }
                
                $data = array(
                    "user_id"        => $user_id,
                    "location_id"    => $location_id                         
                 );
                $this->location_users_m->insert($data);
                
                $list_type_available = $this->input->post('list_type_available', '');
                if ($list_type_available && is_array($list_type_available)) {
                    $this->location_envelope_types_m->delete_by("location_id", $location_id); 
                    foreach($list_type_available as $ActualValue){
                        $location_envelope_type = array(
                           "location_id" => $location_id,
                            "type_id"    => $ActualValue
                        );
                        $this->location_envelope_types_m->insert($location_envelope_type);
                    }
                }    

                addresses_api::deleteLocationPricingById($location_id);
                addresses_api::updateLocationByID($location_id, array('pricing_template_id' => $pricing_template_id));
                addresses_api::createLocationPricing($location_id, array($pricing_template_id));
                
                // Update standard shipping service
                $primary_letter_shipping = $this->input->post('primary_letter_shipping');
                $primary_international_letter_shipping = $this->input->post('primary_international_letter_shipping');
                $standard_national_parcel_service = $this->input->post('standard_national_parcel_service');
                $standard_international_parcel_service = $this->input->post('standard_international_parcel_service');

                $data_to_update = array(
                    'primary_letter_shipping' => $primary_letter_shipping,
                    'primary_international_letter_shipping' => $primary_international_letter_shipping,
                    'standard_national_parcel_service' => $standard_national_parcel_service,
                    'standard_international_parcel_service' => $standard_international_parcel_service
                );
                $this->location_m->update($location_id, $data_to_update);
                
                // Insert data to location customers
                ci()->location_customers_m->insert(array(
                    'location_id' => $location_id,
                    'parent_customer_id' => $parent_customer_id
                ));

                // Insert own location invoice detail
                invoices_api::createEnterpriseInvoiceDetail($parent_customer_id, APConstants::INVOICE_ACTIVITY_TYPE_OWN_LOCATION);
                
                $message = lang('add_location_success');
                $this->success_output($message);
                return true;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return false;
            }
        }

        foreach ($this->validation_rules as $rule) {
            $location->{$rule['field']} = set_value($rule ['field']);
        }
        $location->share_external_flag == '1';
        $countries = settings_api::getAllCountries();
        $currencies = settings_api::getCurrenciesMany();

        $this->setLocationShippingServices($location);
        $this->setAvailbleType($location);

        $this->template->set('pricing_templates_list', array());
        $this->template->set('countries', $countries);
        $this->template->set('currencies', $currencies);
        $this->template->set('location', $location);
        $this->template->set('action_type', 'add');

        $this->template->build('location/form');
    }

    /**
     * Edit an existing location
     *
     * @param int $id The id of the location.
     */
    public function edit()
    {
        ci()->load->library('partner/partner_api');
        ci()->load->library('price/price_api');
        ci()->load->library('device/device_api');
        ci()->load->library('settings/settings_api');
        ci()->load->library('customers/customers_api');
        ci()->load->model('addresses/location_envelope_types_m');

        $this->template->set_layout(FALSE);

        $location_id = $this->input->get_post("id");
        $location = addresses_api::getLocationByID($location_id);
       
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->form_validation->set_rules($this->validation_rules);
            if ($this->form_validation->run() === true) {
                $location_name = $this->input->post('location_name');
                $street = $this->input->post('street');
                $postcode = $this->input->post('postcode');
                $city = $this->input->post('city');
                $region = $this->input->post('region');
                $country_id = $this->input->post('country_id');
                $public_flag = $this->input->post('public_flag');
                $phone_number = $this->input->post('phone_number');
                
                $available_shipping_services = $this->input->post('available_shipping_services', '');
                if ($available_shipping_services && is_array($available_shipping_services)) {
                    $available_shipping_services = implode(',', $available_shipping_services);
                }

                $list_type_available = $this->input->post('list_type_available', '');
                if ($list_type_available && is_array($list_type_available)) {
                    $this->location_envelope_types_m->delete_by("location_id", $location_id); 
                    foreach($list_type_available as $ActualValue){
                        $location_envelope_type = array(
                           "location_id" => $location_id,
                            "type_id"    => $ActualValue
                        );
                        $this->location_envelope_types_m->insert($location_envelope_type);
                    }
                }
                
                // $share_external_flag = $this->input->post('share_external_flag');
                $share_external_flag = 0;
                
                // Update standard shipping service
                $primary_letter_shipping = $this->input->post('primary_letter_shipping');
                $primary_international_letter_shipping = $this->input->post('primary_international_letter_shipping');
                $standard_national_parcel_service = $this->input->post('standard_national_parcel_service');
                $standard_international_parcel_service = $this->input->post('standard_international_parcel_service');
                
                $only_express_shipping_flag  = (int)$this->input->post('only_express_shipping_flag');
                $email                       = $this->input->post('email');
                $phone_number_flag           = $this->input->post('phone_number_flag');
                $business_postbox_text       = $this->input->post('business_postbox_text');
                $office_space_active_flag    = $this->input->post('office_space_active_flag');
                $shared_office_space_flag    = $this->input->post('shared_office_space_flag');
                $booking_email_address       = $this->input->post('booking_email_address');
                $sent_daily_reminder_flag    = $this->input->post('sent_daily_reminder_flag');
            
                $data = array(
                    "location_name" => $location_name,
                    "street" => $street,
                    "postcode" => $postcode,
                    "city" => $city,
                    "region" => $region,
                    "country_id" => $country_id,
                    "available_shipping_services" => $available_shipping_services,
                    "public_flag" => $public_flag,
                    "phone_number" => $phone_number,
                    "share_external_flag" => $share_external_flag,
                    'primary_letter_shipping' => $primary_letter_shipping,
                    'primary_international_letter_shipping' => $primary_international_letter_shipping,
                    'standard_national_parcel_service' => $standard_national_parcel_service,
                    'standard_international_parcel_service' => $standard_international_parcel_service,
                    'only_express_shipping_flag' => $only_express_shipping_flag,
                    'email' => $email,
                    'phone_number_flag' => $phone_number_flag,
                    'business_postbox_text' => $business_postbox_text,
                    'office_space_active_flag' => $office_space_active_flag,
                    'shared_office_space_flag' => $shared_office_space_flag,
                    'booking_email_address' => $booking_email_address,
                    "sent_daily_reminder_flag" => $sent_daily_reminder_flag
                );
                
                addresses_api::updateLocationByID($location_id, $data);
  
                $message = lang('edit_location_success');
                $this->success_output($message);
                exit;
                
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return false;

            }
        }
        $countries = settings_api::getAllCountries();
        $currencies = settings_api::getCurrenciesMany();
      
        $this->setLocationShippingServices($location);
        $this->setAvailbleType($location);
        
        // Get list office features
        $location_office = $this->location_office_m->get_by('location_id', $location_id);
        $office_id = 0;
        if (!empty($location_office)) {
            $office_id = $location_office->id;
        }
        // Get list office features
        $list_location_office_feature = $this->location_office_feature_m->get_many_by_many(array('office_id' => $office_id), '', '', array('order_id' => 'asc'));

        $this->template->set('location_office', $location_office);
        $this->template->set('list_location_office_feature', $list_location_office_feature);
        $this->template->set('countries', $countries);
        $this->template->set('currencies', $currencies);
        $this->template->set('location', $location);
        $this->template->set('action_type', 'edit');

        $this->template->build('location/form');
    }
    
    public function load_standard_shipping_services() {
        $this->load->model('shipping/shipping_services_m');
        $this->load->library('shipping/shipping_api');
        
        $shipping_service_ids = explode(',', $this->input->get_post('shipping_service_ids'));
        $type = $this->input->get_post('type');
        $all_shipping_services = $this->shipping_services_m->get_shipping_services_by('shipping_services.id', $shipping_service_ids, '');
        $shipping_services = array();
        if ($type == '1') {
            $shipping_services = shipping_api::filterListShippingServices($all_shipping_services, APConstants::ENVELOPE_TYPE_LETTER, array(0 , 1));
        } else if ($type == '2') {
            $shipping_services = shipping_api::filterListShippingServices($all_shipping_services, APConstants::ENVELOPE_TYPE_LETTER, array(0 , 2));
        } else if ($type == '3') { 
            $shipping_services = shipping_api::filterListShippingServices($all_shipping_services, APConstants::ENVELOPE_TYPE_PACKAGE, array(0 , 1));
        } else if ($type == '4') {
            $shipping_services = shipping_api::filterListShippingServices($all_shipping_services, APConstants::ENVELOPE_TYPE_PACKAGE, array(0 , 2));
        }
        $list_data = array();
        foreach ($shipping_services as $item) {
            $obj = new stdClass();
            $obj->key = $item->id;
            $obj->label = $item->name;
            $list_data[] = $obj;
        }
        echo json_encode($list_data);
        return;
    }

    public function upload_image_location()
    {
        if (!empty($_POST)) {
            $input_file_client_name = $this->input->get_post('input_file_client_name');
            $location_id = $this->input->get_post('location_id');
            $location = addresses_api::getLocationByID($location_id);
            if(empty($location)){
                $location_id = $this->location_m->insert(array(
                    'public_flag'  => APConstants::OFF_FLAG,
                    'created_date' => date("Y-m-d H:i:s")
                ));
            }

            $data = array();
            switch ($input_file_client_name) {
                case "imagepath":
                    $image_path = Files::upload('location', 'imagepath');
                    if ($image_path && is_string($image_path)) {
                        $data['image_path'] = $image_path;
                        addresses_api::updateLocationByID($location_id, $data);
                    }
                    break;
            }
        }
        $this->success_output($location_id);
        exit;
    }
    
    /**
     * Edit an existing user
     *
     * @param int $id
     *            The id of the user.
     */
    public function delete()
    {
        $this->template->set_layout(FALSE);
        $id = $this->input->get_post("id");

        // gets location used.
        $location = $this->postbox_m->get_by_many(array(
            "location_available_id" => $id
        ));
        if ($location) {
            // output error message
            $message = lang('delete_location_fail');
            $this->error_output($message);
        } else {
            // delete location
            $this->location_m->delete_by("id", $id);

            $this->location_pricing_m->delete_by("location_id", $id);

            // output message.
            $message = lang('delete_location_success');
            $this->success_output($message);
        }

        return;
    }

    /**
     * Check if the pricing template that is going to be removed is active (in use)
     */
    public function check_active_pricing_template()
    {
        if ($this->is_ajax_request()) {
            $this->load->library('addresses/addresses_api');
            $this->lang->load('settings/settings');

            $locationID = $this->input->get_post('location_id');
            $pricingTemplateIDs = $this->input->get_post('pricing_template_ids');
            $pricingTemplateIDs = explode(',', $pricingTemplateIDs);

            $location = addresses_api::getLocationByID($locationID);
            $pricingTemplateID = $location->pricing_template_id;

            if (in_array($pricingTemplateID, $pricingTemplateIDs)) {
                $message = lang('remove_active_pricing_template_error');
                $this->error_output($message);
                return false;
            } else {
                $this->success_output('');
                return true;
            }
        } else {
            $this->error_output('Invalid request');
            return false;
        }
    }
    
    public function display_confirmation_add_location() {
        $this->template->set_layout(FALSE);
        ci()->load->model(array(
            'price/pricing_m'
        ));
        
        // Get pricing of default template
        $pricing = ci()->pricing_m->get_by_many(array(
            'pricing_template_id' => 0,
            'item_name' => 'own_location_monthly',
            'account_type' => 5
        ));
        
        $start_date = now();
        if ($pricing->contract_terms == 'monthly') {
            $end_date = $start_date + 365 * 24 * 60 * 60 / 12;
        } else if ($pricing->contract_terms == 'quarterly') {
            $end_date = $start_date + 365 * 24 * 60 * 60 / 4;
        } else if ($pricing->contract_terms == 'yearly') {
            $end_date = $start_date + 365 * 24 * 60 * 60;
        } else {
            $end_date = $start_date + 365 * 24 * 60 * 60;
        }
        $end_contract_date = APUtils::convert_timestamp_to_date($end_date, APConstants::DATEFORMAT_DEFAULT);
        $this->template->set('price', $pricing->item_value);
        $this->template->set('end_contract_date', $end_contract_date);     
        $this->template->build('location/display_confirmation_add_location');
    }

    /**
     * delete all postboxes when delete location.
     */
    public function delete_all_postbox()
    {
        $this->template->set_layout(FALSE);
        $this->load->library(array(
            "mailbox/mailbox_api",
            "addresses/addresses_api"
        ));

        $locationId = $this->input->get_post("id");

        if ($locationId) {
            $postboxes = mailbox_api::getAllPostboxesByLocation($locationId);

            $this->location_m->db->trans_begin();
            foreach ($postboxes as $p) {
                $anotherLocationPostbox = mailbox_api::countPostboxOfCustomerAnotherLocation($locationId, $p->customer_id);
                if ($anotherLocationPostbox) {
                    // delete postbox by admin.
                    APUtils::deletePostbox($p->postbox_id, $p->customer_id, true);
                } else {
                    // delete customer
                    $openBalance = CustomerUtils::getAdjustOpenBalanceDue($p->customer_id);
                    $total = $openBalance['OpenBalanceDue'] + $openBalance['OpenBalanceThisMonth'];
                    $blacklistFlag = ($total > 0) ? true : false;
                    CustomerUtils::deleteCustomer($p->customer_id, true, $blacklistFlag);
                }
            }

            // delete location.
            addresses_api::deleteLocationById($locationId);

            if ($this->location_m->db->trans_status() === FALSE) {
                $this->location_m->db->trans_rollback();
                $this->error_output("There is some error when system delete this location.");
            } else {
                $this->location_m->db->trans_commit();
                $this->success_output("");
            }
        }
    }
    
    /**
     * show digital panel condition.
     */
    public function show_digital_panel(){
        $this->template->set_layout(false);
        
        $this->template->build("location/show_digital_panel");
    }
    
    public function add_digital_panel(){
        $this->template->set_layout(false);
        $location_id = $this->input->get_post("location_id");
        if(empty($location_id)){
            $this->error_output("Location is required.");
            return;
        }
        
        $location = $this->location_m->get($location_id);
        
        // send email notification
        $parent_customer = APContext::getParentCustomerLoggedIn();
        $data = array(
            "slug" => APConstants::email_notify_assign_digital_panel_location_enterprise,
            "to_email" => 'system@clevver.io',
            // Replace content
            "customer_code" => $parent_customer->customer_code,
            "location_name" => $location->location_name,
            "site_url" => APContext::getFullBalancerPath()
        );
        MailUtils::sendEmailByTemplate($data);

        // Insert own location invoice detail
        $parent_customer_id = $parent_customer->customer_id;
        invoices_api::createEnterpriseInvoiceDetail($parent_customer_id, APConstants::INVOICE_ACTIVITY_TYPE_TOUCH_PANEL_OWN_LOCATION);
        
        $this->success_output("");
        return;
    }
    
    public function location_office(){

        $this->template->set_layout(FALSE);
        $location_id = (int) $this->input->get_post('location_id');
        
        $location_office = '';
        if($location_id > 0){
            $location_office = $this->location_office_m->get_by('location_id', $location_id);
        }
        if ($this->input->server('REQUEST_METHOD') == 'POST' && $this->is_ajax_request()) {
            
            $business_concierge_flag = $this->input->post('business_concierge_flag');
            $video_conference_flag   = $this->input->post('video_conference_flag');
            $meeting_rooms_flag = $this->input->post('meeting_rooms_flag');
            $features = $this->input->post('feature');
            $data = array(
                'location_id' => $location_id,
                'business_concierge_flag' => $business_concierge_flag,
                'video_conference_flag'   => $video_conference_flag,
                'meeting_rooms_flag'      => $meeting_rooms_flag,
            );

            $office_id = 0;
            if (empty($location_office)) {
                $office_id = $this->location_office_m->insert($data);
            } else {
                $this->location_office_m->update_by_many(array('location_id' => $location_id),$data);
                $office_id = $location_office->id;
            }
            
            // Delete all current features
            $this->location_office_feature_m->delete_by_many(array('office_id' => $office_id));
            
            // Insert
            $i = 0;
            if (!empty($features) && count($features) > 0) {
                foreach ($features as $feature_name) {
                    $i++;
                    $this->location_office_feature_m->insert(array(
                        'office_id' => $office_id,
                        'feature_name' => $feature_name,
                        'order_id' => $i
                    ));
                }
            }
            
            $this->success_output('The location office feature has been updated successfully.');
            return;
        }
        $this->template->set('location_id', $location_id);
        $this->template->set('location_office', $location_office);
        $this->template->build('location/location_office');

    }

    private function setLocationShippingServices($location)
    {
        $this->load->model('shipping/shipping_services_m');

//        if (!isset($location->available_shipping_services) || empty($location->available_shipping_services)) {
//            $api_svc_code1 = array('INTERNATIONAL_ECONOMY', 'INTERNATIONAL_PRIORITY', 'INTERNATIONAL_FIRST');
//            $location_shipping_services = $this->shipping_services_m->get_shipping_services_by('api_svc_code1', $api_svc_code1, '');
//            $shipping_service_ids = array();
//            foreach ($location_shipping_services as $shipping_service) {
//                array_push($shipping_service_ids, $shipping_service->id);
//            }
//        } else {
//            $shipping_service_ids = explode(',', $location->available_shipping_services);
//        }
        
        if (!empty($location->available_shipping_services)) {
            $shipping_service_ids = explode(',', $location->available_shipping_services);
        }
        
        if (empty($shipping_service_ids)) {
            $shipping_service_ids[] = 0;
        }
        // Get location's shipping services
        $location_shipping_services = $this->shipping_services_m->get_shipping_services_by('shipping_services.id', $shipping_service_ids, '');
        $shipping_services = $this->shipping_services_m->get_shipping_services_exclude_by($shipping_service_ids);

        $this->template->set('shipping_services', $shipping_services);
        $this->template->set('location_shipping_services', $location_shipping_services);

    }


    private function setAvailbleType($location)
    {
        
        //$list_type_available = $this->location_envelope_types_m->getAvailbleTypeByLocation($location->id);
        
        $listType            = Settings::get_list(APConstants::ENVELOPE_TYPE_CODE);

        // comment on #1447
        $this->template->set('list_type_available', array());
        
        $this->template->set('listType', $listType);
        $this->template->set("digital_devices", array());

    }

}