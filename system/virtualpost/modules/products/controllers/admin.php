<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin controller for the users module
 */
class Admin extends Admin_Controller
{
    /**
     * Validation for FedEx shipping service data.
     * The rest of the validation is built by streams.
     *
     * @var array
     */
    private $fedex_shipping_service_validation_rules = array(
        array(
            'field' => 'name',
            'label' => 'Name',
            'rules' => 'required'
        ),
        array(
            'field' => 'short_desc',
            'label' => 'Description',
            'rules' => ''
        ),
        array(
            'field' => 'long_desc',
            'label' => 'Text',
            'rules' => ''
        ),
//        array(
//            'field' => 'api_svc_code1',
//            'label' => 'Webservice code 1',
//            'rules' => ''
//        ),
//        array(
//            'field' => 'api_svc_code2',
//            'label' => 'Webservice code 2',
//            'rules' => ''
//        ),
        array(
            'field' => 'carrier_id',
            'label' => 'Carriers',
            'rules' => 'required'
        ),
        array(
            'field' => 'api_acc_id',
            'label' => 'API',
            'rules' => 'trim'
        ),
        array(
            'field' => 'show_calculation_fails',
            'label' => 'show as option even if calculation fails',
            'rules' => 'required'
        ),
        array(
            'field' => 'logo',
            'label' => 'Logo',
            'rules' => ''
        ),
        array(
            'field' => 'factor_a',
            'label' => 'Factor A',
            'rules' => ''
        ),
        array(
            'field' => 'factor_b',
            'label' => 'Factor B',
            'rules' => ''
        ),
        array(
            'field' => 'weight_limit',
            'label' => 'Weight Limit',
            'rules' => ''
        ),
        array(
            'field' => 'dimension_limit',
            'label' => 'Dimension Limit',
            'rules' => ''
        ),
        array(
            'field' => 'shipping_service_template',
            'label' => 'Shipping service',
            'rules' => 'required'
        ),
        array(
            'field' => 'service_type',
            'label' => 'Shipping type',
            'rules' => 'required'
        ),
        array(
            'field' => 'packaging_type',
            'label' => 'Packaging type',
            'rules' => 'required'
        ),
        array(
            'field' => 'tracking_information_flag',
            'label' => 'No tracking information',
            'rules' => ''
        )
    );

    /**
     * Validation array
     *
     * @var array
     */
    private $validation_rules = array(
        array(
            'field' => 'code',
            'label' => 'Code',
            'rules' => 'required|trim|max_length[5]'
        ),
        array(
            'field' => 'name',
            'label' => 'Name',
            'rules' => 'required|trim|max_length[50]'
        ),
        array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => 'trim|max_length[255]'
        )
    );

    /**
     * Constructor method
     */
    public function __construct()
    {
        parent::__construct();

        // Load the required classes
        $this->load->model('addresses/location_m');
        $this->load->library('form_validation');
        $this->load->model('invoices/vatcase_m');
        $this->load->model('invoices/vatcase_standard_m');
        $this->load->model('settings/countries_m');
        
        // load language
        $this->lang->load('shipping/shipping');
        $this->lang->load('products/products');
    }

    /**
     * List all users
     */
    public function index()
    {
        // do nothing.
    }

    public function product_matrix()
    {
        // TODO:
        $this->template->build("page_construction");
    }

    /**
     * List all shipping services
     */
    public function shipping_services()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') { // POST request
            $this->load->library('shipping/shipping_api');
            $this->load->model('shipping/shipping_services_m');
            $this->load->model('shipping/shipping_apis_m');

            // Get input condition
            $enquiry = $this->input->get_post("enquiry");

            $array_condition = array();
            if (!empty($enquiry)) {
                $array_condition["(shipping_services.name LIKE '%" . $enquiry .
                "%' OR shipping_services.short_desc LIKE '%" . $enquiry .
                "%' OR shipping_services.long_desc LIKE '%" . $enquiry . "%')"] = null;
            }

            // update limit into user_paging.
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;

            // Call search method
            $query_result = $this->shipping_services_m->get_shipping_services_paging($array_condition, $input_paging['start'], $input_paging['limit'],
                $input_paging['sort_column'], $input_paging['sort_type']);

            // Process output data
            $total = $query_result['total'];
            $rows = $query_result['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);

            $i = 0;
            foreach ($rows as $row) {
                
                $shipping_api_name = '';
                $shipping_api = shipping_api::get_shipping_api_by_shipping_service($row->id);
                if (!empty($shipping_api['api_codes'])) {
                
                    //Get list api_ids    
                    $api_codes = array();
                    foreach ($shipping_api['api_codes'] as $api_code) {
                        $api_codes[] = $api_code->api_id;
                    }

                    //Get apis name
                    $shipping_api_names = $this->shipping_apis_m->get_shipping_apis_name($api_codes);
                    $shipping_api_name = implode(array_column($shipping_api_names, 'name'), ', ');
                    
                }
                
                $response->rows[$i]['id'] = $row->id;
                $response->rows[$i]['cell'] = array(
                    $row->id,
                    $row->name,
                    $row->short_desc,
                    $row->long_desc,
                    $row->logo,
                    $shipping_api_name,
                    $row->factor_a,
                    $row->factor_b,
                    $row->id
                );
                $i++;
            }
            echo json_encode($response);
        } else { // GET request
            $this->template->build("admin/shipping_services");
        }
    }

    /**
     * Method for handling different form actions
     */
    public function add_shipping_service()
    {
        $this->load->model('shipping/shipping_carriers_m');
        $this->load->model('shipping/shipping_apis_m');
        $this->load->model('shipping/shipping_credentials_m');
        $this->load->model('shipping/shipping_services_m');
        

        $this->template->set_layout(FALSE);

        $shipping_service = new stdClass();
        $shipping_service->id = '';
        if ($_POST) {
            $this->form_validation->set_rules($this->fedex_shipping_service_validation_rules);
            if ($this->form_validation->run()) {
                try {
                    $tracking_information_flag = $this->input->post('tracking_information_flag');
                    if ($tracking_information_flag == null || $tracking_information_flag == '') {
                        $tracking_information_flag = APConstants::ON_FLAG;
                    }
                    // Add to database
                    $data_to_store = array(
                        'name' => $this->input->post('name'),
                        'short_desc' => $this->input->post('short_desc'),
                        'long_desc' => $this->input->post('long_desc'),
//                        'api_svc_code1' => $this->input->post('api_svc_code1'),
//                        'api_svc_code2' => $this->input->post('api_svc_code2'),
                        'carrier_id' => $this->input->post('carrier_id'),
                        'api_acc_id' => $this->input->post('api_acc_id', 0),
                        'logo' => $this->input->post('logo'),
                        'factor_a' => $this->input->post('factor_a'),
                        'factor_b' => $this->input->post('factor_b'),
                        'weight_limit' => $this->input->post('weight_limit'),
                        'dimension_limit' => $this->input->post('dimension_limit'),
                        'shipping_service_template' => $this->input->post('shipping_service_template'),
                        'service_type' => $this->input->post('service_type'),
                        'packaging_type' => $this->input->post('packaging_type'),
                        'show_calculation_fails' => $this->input->post('show_calculation_fails'),
                        'tracking_information_flag' => $tracking_information_flag,
                        //'shipping_api' => $this->input->post('shippingApi'),
                        'shipping_api_code' => $this->input->post('shippingApiCode'),
                        'shipping_api_credential' => $this->input->post('shippingApiCredential')
                    );

                    //if the insert has returned true then we show the flash message
                    if ($this->shipping_services_m->insert($data_to_store)) ;

                    $message = lang('shipping_service.add_shipping_service_success');
                    $this->success_output($message);
                    return;
                } catch (Exception $e) {
                    $message = lang('shipping_service.add_shipping_service_error');
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
        foreach ($this->fedex_shipping_service_validation_rules as $rule) {
            $shipping_service->{$rule ['field']} = set_value($rule ['field']);
        }
        $shipping_service->factor_a = 0.5;
        $shipping_service->factor_b = 5000;
        $list_carriers = $this->shipping_carriers_m->get_all();
        $list_apis = $this->shipping_apis_m->get_all();
        $list_credentials = $this->shipping_credentials_m->get_all();
        // $list_location = APUtils::loadListAccessLocation();
        //$this->template->set('list_location', $list_location);
        $this->template->set('list_carriers', $list_carriers);
        $this->template->set('list_credentials', $list_credentials);
        $this->template->set('list_apis', $list_apis);
        $this->template->set('shipping_service', $shipping_service);
        $this->template->set('action_type', 'add');

        // Display the current page
        $this->template->append_js('jquery.upload-1.0.0.min.js');
        $this->template->build('admin/shipping_service_form');
    }

    /**
     * Method for handling different form actions
     */
    public function edit_shipping_service()
    {
        $this->load->model('shipping/shipping_carriers_m');
        $this->load->model('shipping/shipping_apis_m');
        $this->load->model('shipping/shipping_credentials_m');
        $this->load->model('shipping/shipping_services_m');

        $this->template->set_layout(FALSE);

        $id = $this->input->get_post('id');
        $shipping_service = $this->shipping_services_m->get_by_many(array('id' => $id));
        $list_apis = $this->shipping_apis_m->get_all();
        $list_credentials = $this->shipping_credentials_m->get_all();
        
        //Get shipping api codes
        $api_codes = array();
        $shipping_api_codes = json_decode($shipping_service->shipping_api_code, true);
        if (!empty($shipping_api_codes)){
            //Assign refered api name
            foreach ($shipping_api_codes as $shippinng_api_code){
                $api_code_item = new stdClass();
                $api_code_item->api_id = $shippinng_api_code['api_id'];
                $api_code_item->api_name = $this->get_shipping_api_name_from_array($shippinng_api_code['api_id'], $list_apis);
                $api_code_item->service_code = $shippinng_api_code['service_code'];
                //Add to array data
                $api_codes[] = $api_code_item;
            }
        }
        $shipping_service->shipping_api_codes = $api_codes;
        
        //Get shipping api credentials
        $api_credentials = array();
        $shipping_api_credentials = json_decode($shipping_service->shipping_api_credential, true);
        if (!empty($shipping_api_credentials)){
            //Assign refered api name
            foreach ($shipping_api_credentials as $shipping_api_credential){
                $api_credential_item = new stdClass();
                $api_credential_item->api_id = $shipping_api_credential['api_id'];
                $api_credential_item->api_name = $this->get_shipping_api_name_from_array($shipping_api_credential['api_id'], $list_apis);
                $api_credential_item->credential_id = $shipping_api_credential['credential_id'];
                $api_credential_item->credential_name = $this->get_shipping_api_name_from_array($shipping_api_credential['credential_id'], $list_credentials);
                //Add to array data
                $api_credentials[] = $api_credential_item;
            }
        }
        $shipping_service->shipping_api_credentials = $api_credentials;

        if ($_POST) {
            $this->form_validation->set_rules($this->fedex_shipping_service_validation_rules);
            if ($this->form_validation->run()) {
                try {
                    $tracking_information_flag = $this->input->post('tracking_information_flag');
                    if ($tracking_information_flag == null || $tracking_information_flag == '') {
                        $tracking_information_flag = APConstants::ON_FLAG;
                    }
                    // Add to database
                    $data_to_store = array(
                        'name' => $this->input->post('name'),
                        'short_desc' => $this->input->post('short_desc'),
                        'long_desc' => $this->input->post('long_desc'),
//                        'api_svc_code1' => $this->input->post('api_svc_code1'),
//                        'api_svc_code2' => $this->input->post('api_svc_code2'),
                        'carrier_id' => $this->input->post('carrier_id'),
                        'api_acc_id' => $this->input->post('api_acc_id', 0),
                        'logo' => $this->input->post('logo'),
                        'factor_a' => $this->input->post('factor_a'),
                        'factor_b' => $this->input->post('factor_b'),
                        'weight_limit' => $this->input->post('weight_limit'),
                        'dimension_limit' => $this->input->post('dimension_limit'),
                        'shipping_service_template' => $this->input->post('shipping_service_template'),
                        'service_type' => $this->input->post('service_type'),
                        'packaging_type' => $this->input->post('packaging_type'),
                        'show_calculation_fails' => $this->input->post('show_calculation_fails'),
                        'tracking_information_flag' => $tracking_information_flag,
                        'shipping_api_code' => $this->input->post('shippingApiCode'),
                        'shipping_api_credential' => $this->input->post('shippingApiCredential')
                    );

                    //if the insert has returned true then we show the flash message
                    $this->shipping_services_m->update_by_many(array('id' => $id), $data_to_store);

                    $message = lang('shipping_service.edit_shipping_service_success');
                    $this->success_output($message);
                    return;
                } catch (Exception $e) {
                    $message = lang('shipping_service.edit_shipping_service_error');
                    $this->error_output($message);
                    return;
                }
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }
        
        $list_carriers = $this->shipping_carriers_m->get_all();
        //$list_apis = $this->shipping_apis_m->get_all();
        // $list_location = APUtils::loadListAccessLocation();
        // $this->template->set('list_location', $list_location);
        $this->template->set('list_carriers', $list_carriers);
        $this->template->set('list_apis', $list_apis);
        $this->template->set('list_credentials', $list_credentials);
        $this->template->set('shipping_service', $shipping_service);
        $this->template->set('action_type', 'edit');

        // Display the current page
        $this->template->append_js('jquery.upload-1.0.0.min.js');
        $this->template->build('admin/shipping_service_form');
    }
    
    private function get_shipping_api_name_from_array($id, $array_data){
        if (!empty($array_data)){
            foreach ($array_data as $item){
                if ($id == $item->id){
                    return $item->name;
                }
            }
        }
        return '';
    }


    /**
     * Upload logo
     */
    public function upload_shipping_services_logo()
    {
        $this->load->library('files/files');
        $imagepath = Files::upload('logo', 'imagepath');
        $this->success_output($imagepath);
        return;
    }

    /**
     * Delete shipping api
     */
    public function delete_shipping_service()
    {
        $this->load->model('shipping/shipping_services_m');

        $id = $this->input->get_post("id");
        $this->shipping_services_m->delete_by_many(array('id' => $id));
        $message = lang('shipping_service.delete_shipping_service_success');

        $this->success_output($message);
    }

    /**
     * Standard shipping services
     */
    public function shipping_standards()
    {
        $this->load->model('addresses/location_m');
        $this->load->model('shipping/shipping_services_m');
        $this->load->library('shipping/shipping_api');

        // Process when submit form
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $primary_letter_shipping_services = $this->input->post('primary_letter_shipping_services');
            $primary_international_letter_shipping_services = $this->input->post('primary_international_letter_shipping_services');
            $standard_national_parcel_services = $this->input->post('standard_national_parcel_services');
            $standard_international_parcel_services = $this->input->post('standard_international_parcel_services');

            foreach ($primary_letter_shipping_services as $location_id => $primary_letter_shipping) {
                $primary_international_letter_shipping = $primary_international_letter_shipping_services[$location_id];
                $standard_national_parcel_service = $standard_national_parcel_services[$location_id];
                $standard_international_parcel_service = $standard_international_parcel_services[$location_id];
                $data_to_update = array(
                    'primary_letter_shipping' => $primary_letter_shipping,
                    'primary_international_letter_shipping' => $primary_international_letter_shipping,
                    'standard_national_parcel_service' => $standard_national_parcel_service,
                    'standard_international_parcel_service' => $standard_international_parcel_service
                );
                $this->location_m->update($location_id, $data_to_update);
            }
        }

        $list_location_shipping_services = array();
        $list_location = APUtils::loadListAccessLocation();
        foreach ($list_location as $location) {
            $shipping_service_ids = explode(',', $location->available_shipping_services);
            $shipping_services = $this->shipping_services_m->get_shipping_services_by('shipping_services.id', $shipping_service_ids, '');
            $location_shipping_services = array(
                'location' => $location,
                'shipping_services' => $shipping_services
            );
            array_push($list_location_shipping_services, $location_shipping_services);
            unset($location_shipping_services);
        }

        $this->template->set('list_location_shipping_services', $list_location_shipping_services);
        $this->template->build("admin/shipping_standards");
    }

    public function vat_eu()
    {
        // gets EU countries.
        $countries = $this->vatcase_standard_m->get_eu_countries();

        // set template
        $this->template->set("countries", $countries);
        $this->template->build("admin/vat_eu");
    }

    /**
     * save vat eu.
     */
    public function save_vat_eu()
    {
        $inputs = $this->input->post();

        foreach ($inputs as $base_key => $value) {
            $key = explode("-", $base_key);
            $this->vatcase_standard_m->update_by_many(array(
                "country_id" => $key[1]
            ), array(
                $key[0] => $value
            ));
        }

        redirect("admin/products/vat_eu");
    }

    /**
     * vat case.
     */
    public function vat_case()
    {
        // define standard vat
        $list_standard_vat = array();
        $tmp = new stdClass();
        $tmp->id = "0";
        $tmp->name = "No VAT";
        array_push($list_standard_vat, $tmp);
        unset($tmp);

        $tmp = new stdClass();
        $tmp->id = "1";
        $tmp->name = "EU VAT";
        array_push($list_standard_vat, $tmp);
        unset($tmp);

        $tmp = new stdClass();
        $tmp->id = "2";
        $tmp->name = "Germany VAT";
        array_push($list_standard_vat, $tmp);
        unset($tmp);

        // Gets list local service case.
        $list_local_service_vat = $this->get_local_service_list();

        // Gets list shipping case.
        $list_shipping_vat = $this->get_shipping_list();

        // Gets list digital goods.
        $list_digital_good_vat = $this->get_digital_good_list();

        // set reverse charge list.
        $reverse_charge_list = array();
        $tmp = new stdClass();
        $tmp->id = "1";
        $tmp->name = "Yes";
        array_push($reverse_charge_list, $tmp);
        unset($tmp);

        $tmp = new stdClass();
        $tmp->id = "0";
        $tmp->name = "No";
        array_push($reverse_charge_list, $tmp);
        unset($tmp);
        $this->template->set('reverse_charge_list', $reverse_charge_list);

        // render template.
        $this->template->set('list_standard_vat', $list_standard_vat);
        $this->template->set('list_local_service_vat', $list_local_service_vat);
        $this->template->set('list_shipping_vat', $list_shipping_vat);
        $this->template->set('list_digital_good_vat', $list_digital_good_vat);
        $this->template->build("admin/vat_case");
    }

    /**
     * Save vat case.
     */
    public function save_vat_case()
    {
        $inputs = $this->input->post();
        foreach ($inputs as $base_key => $value) {
            $key = explode("-", $base_key);
            if ($key[0] == 'type') {
                $rate = 0;
                $stardard_rate = 0;
                // germany vat
                if ($value == 2) {
                    $stardard_rate = $this->vatcase_standard_m->get_by("country_id", APConstants::GERMANY_COUNTRY_ID);
                } else if ($value == 1) {
                    $stardard_rate = $this->vatcase_standard_m->get_by("country_id", $key[1]);
                }
                $rate = $stardard_rate ? $stardard_rate->rate : 0;
                $product_type = APConstants::VAT_PRODUCT_LOCAL_SERVICE;
                if ($key[3] == 'shipping') {
                    $product_type = APConstants::VAT_PRODUCT_SHIPPING;
                } else if ($key[3] == 'dg') {
                    $product_type = APConstants::VAT_PRODUCT_DIGITAL_GOOD;
                }
                $customer_type = APConstants::CUSTOMER_TYPE_PRIVATE;
                if ($key[2] == 'enterprise') {
                    $customer_type = APConstants::CUSTOMER_TYPE_ENTERPRISE;
                }
                $this->vatcase_m->update_by_many(
                    array(
                        "baseon_country_id" => $key[4],
                        "product_type" => $product_type,
                        "customer_type" => $customer_type
                    ), array(
                    'rate' => $rate,
                    'type' => $value
                ));
            } else {
                $this->vatcase_m->update_by_many(array(
                    "vat_id" => $key[1]
                ), array(
                    $key[0] => $value
                ));
            }
        }

        redirect("admin/products/vat_case");
    }

    private function get_local_service_list()
    {
        $list_privates = $this->vatcase_m->get_vat_case_by(APConstants::VAT_PRODUCT_LOCAL_SERVICE, APConstants::CUSTOMER_TYPE_PRIVATE);
        $list_enterprises = $this->vatcase_m->get_vat_case_by(APConstants::VAT_PRODUCT_LOCAL_SERVICE, APConstants::CUSTOMER_TYPE_ENTERPRISE);

        $result = array();
        foreach ($list_privates as $private) {
            $temp = new stdClass();
            $temp->private_vat_id = $private->vat_id;
            $temp->product_type = $private->product_type;
            $temp->private_rate = $private->rate;
            $temp->private_type = $private->type;
            $temp->private_notes = $private->notes;
            $temp->private_reverse_charge = $private->reverse_charge;
            $temp->baseon_country_id = $private->baseon_country_id;
            $temp->country_name = $private->country_name ? $private->country_name : "All other country";
            $temp->private_vat_case_id = $private->vat_case_id;
            $temp->private_text = $private->text;
            $temp->enterprise_vat_id = 0;
            $temp->enterprise_rate = 0;
            $temp->enterprise_vat_case_id = 0;
            $temp->enterprise_text = '';
            $temp->enterprise_type = 0;
            $temp->enterprise_notes = '';
            $temp->enterprise_reverse_charge = '';

            foreach ($list_enterprises as $e) {
                if ($e->baseon_country_id == $private->baseon_country_id) {
                    $temp->enterprise_vat_id = $e->vat_id;
                    $temp->enterprise_rate = $e->rate;
                    $temp->enterprise_vat_case_id = $e->vat_case_id;
                    $temp->enterprise_text = $e->text;
                    $temp->enterprise_type = $e->type;
                    $temp->enterprise_notes = $e->notes;
                    $temp->enterprise_reverse_charge = $e->reverse_charge;
                }
            }

            array_push($result, $temp);
            unset($temp);
        }

        return $result;
    }

    private function get_shipping_list()
    {
        $list_privates = $this->vatcase_m->get_vat_case_by(APConstants::VAT_PRODUCT_SHIPPING, APConstants::CUSTOMER_TYPE_PRIVATE);
        $list_enterprises = $this->vatcase_m->get_vat_case_by(APConstants::VAT_PRODUCT_SHIPPING, APConstants::CUSTOMER_TYPE_ENTERPRISE);

        $result = array();
        foreach ($list_privates as $private) {
            $temp = new stdClass();
            $temp->private_vat_id = $private->vat_id;
            $temp->product_type = $private->product_type;
            $temp->private_rate = $private->rate;
            $temp->private_type = $private->type;
            $temp->private_notes = $private->notes;
            $temp->private_reverse_charge = $private->reverse_charge;
            $temp->baseon_country_id = $private->baseon_country_id;
            $temp->country_name = $private->country_name ? $private->country_name : "All other country";
            $temp->private_vat_case_id = $private->vat_case_id;
            $temp->private_text = $private->text;
            $temp->enterprise_vat_id = 0;
            $temp->enterprise_rate = 0;
            $temp->enterprise_vat_case_id = 0;
            $temp->enterprise_text = '';
            $temp->enterprise_type = 0;
            $temp->enterprise_notes = '';
            $temp->enterprise_reverse_charge = '';

            foreach ($list_enterprises as $e) {
                if ($e->baseon_country_id == $private->baseon_country_id) {
                    $temp->enterprise_vat_id = $e->vat_id;
                    $temp->enterprise_rate = $e->rate;
                    $temp->enterprise_vat_case_id = $e->vat_case_id;
                    $temp->enterprise_text = $e->text;
                    $temp->enterprise_type = $e->type;
                    $temp->enterprise_notes = $e->notes;
                    $temp->enterprise_reverse_charge = $e->reverse_charge;
                }
            }

            array_push($result, $temp);
            unset($temp);
        }

        return $result;
    }

    private function get_digital_good_list()
    {
        $list_privates = $this->vatcase_m->get_vat_case_by(APConstants::VAT_PRODUCT_DIGITAL_GOOD, APConstants::CUSTOMER_TYPE_PRIVATE);
        $list_enterprises = $this->vatcase_m->get_vat_case_by(APConstants::VAT_PRODUCT_DIGITAL_GOOD, APConstants::CUSTOMER_TYPE_ENTERPRISE);

        $result = array();
        foreach ($list_privates as $private) {
            $temp = new stdClass();
            $temp->private_vat_id = $private->vat_id;
            $temp->product_type = $private->product_type;
            $temp->private_rate = $private->rate;
            $temp->private_type = $private->type;
            $temp->private_notes = $private->notes;
            $temp->private_reverse_charge = $private->reverse_charge;
            $temp->baseon_country_id = $private->baseon_country_id;
            $temp->country_name = $private->country_name ? $private->country_name : "All other country";
            $temp->private_vat_case_id = $private->vat_case_id;
            $temp->private_text = $private->text;
            $temp->enterprise_vat_id = 0;
            $temp->enterprise_rate = 0;
            $temp->enterprise_vat_case_id = 0;
            $temp->enterprise_text = '';
            $temp->enterprise_type = 0;
            $temp->enterprise_notes = '';
            $temp->enterprise_reverse_charge = '';

            foreach ($list_enterprises as $e) {
                if ($e->baseon_country_id == $private->baseon_country_id) {
                    $temp->enterprise_vat_id = $e->vat_id;
                    $temp->enterprise_rate = $e->rate;
                    $temp->enterprise_vat_case_id = $e->vat_case_id;
                    $temp->enterprise_text = $e->text;
                    $temp->enterprise_type = $e->type;
                    $temp->enterprise_notes = $e->notes;
                    $temp->enterprise_reverse_charge = $e->reverse_charge;
                }
            }

            array_push($result, $temp);
            unset($temp);
        }

        return $result;
    }
    
    /**
     * List all shipping services
     */
    public function shipping_carriers()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') { // POST request
            $this->load->model('shipping/shipping_carriers_m');

            // Get input condition
            $array_condition = array();

            // update limit into user_paging.
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;

            // Call search method
            $query_result = $this->shipping_carriers_m->get_paging($array_condition, $input_paging['start'], $input_paging['limit'],
                $input_paging['sort_column'], $input_paging['sort_type']);

            // Process output data
            $total = $query_result['total'];
            $rows = $query_result['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);

            $i = 0;
            foreach ($rows as $row) {
                $response->rows[$i]['id'] = $row->id;
                $response->rows[$i]['cell'] = array(
                    $row->id,
                    $row->code,
                    $row->name,
                    $row->description,
                    $row->id
                );
                $i++;
            }
            echo json_encode($response);
        } else { // GET request
            $this->template->build("admin/shipping_carriers");
        }
    }
    
    /**
     * Method for handling different form actions
     */
    public function add_shipping_carrier()
    {
        $this->load->model('shipping/shipping_carriers_m');
        $this->template->set_layout(FALSE);

        $shipping_carrier = new stdClass();
        $shipping_carrier->id = '';
        if ($_POST) {
            $this->form_validation->set_rules($this->validation_rules);
            if ($this->form_validation->run()) {
                try {
                    // Add to database
                    $data_to_store = array(
                        'name' => $this->input->post('name'),
                        'code' => $this->input->post('code'),
                        'description' => $this->input->post('description'),
                        'tracking_number_url' => trim($this->input->post('tracking_number_url'))
                    );

                    //if the insert has returned true then we show the flash message
                    if ($this->shipping_carriers_m->insert($data_to_store)) ;

                    $message = lang('shipping_service.add_shipping_carrier_success');
                    $this->success_output($message);
                    return;
                } catch (Exception $e) {
                    $message = lang('shipping_service.add_shipping_carrier_error');
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
        foreach ($this->validation_rules as $rule) {
            $shipping_carrier->{$rule ['field']} = set_value($rule ['field']);
        }
        $shipping_carrier->tracking_number_url = "";
        $this->template->set('shipping_carrier', $shipping_carrier);
        $this->template->set('action_type', 'add');

        // Display the current page
        $this->template->build('admin/shipping_carrier_form');
    }

    /**
     * Method for handling different form actions
     */
    public function edit_shipping_carrier()
    {
        $this->load->model('shipping/shipping_carriers_m');
        $this->template->set_layout(FALSE);

        $id = $this->input->get_post('id');
        $shipping_carrier = $this->shipping_carriers_m->get_by_many(array('id' => $id));

        if ($_POST) {
            $this->form_validation->set_rules($this->validation_rules);
            if ($this->form_validation->run()) {
                try {
                    // Add to database
                    $data_to_store = array(
                        'name' => $this->input->post('name'),
                        'code' => $this->input->post('code'),
                        'description' => $this->input->post('description'),
                        'tracking_number_url' => trim($this->input->post('tracking_number_url'))
                    );

                    //if the insert has returned true then we show the flash message
                    $this->shipping_carriers_m->update_by_many(array('id' => $id), $data_to_store);

                    $message = lang('shipping_service.edit_shipping_carrier_success');
                    $this->success_output($message);
                    return;
                } catch (Exception $e) {
                    $message = lang('shipping_service.edit_shipping_carrier_error');
                    $this->error_output($message);
                    return;
                }
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }
        $this->template->set('shipping_carrier', $shipping_carrier);
        $this->template->set('action_type', 'edit');

        // Display the current page
        $this->template->build('admin/shipping_carrier_form');
    }
    
    /**
     * Delete shipping api
     */
    public function delete_shipping_carrier()
    {
        $this->load->model('shipping/shipping_carriers_m');

        $id = $this->input->get_post("id");
        $this->shipping_carriers_m->delete_by_many(array('id' => $id));
        $message = lang('shipping_service.delete_shipping_carrier_success');

        $this->success_output($message);
    }
    
    
    public function load_shipping_api_form() {
        $this->template->set_layout(FALSE);
        
        $this->load->model('shipping/shipping_apis_m');
        $api_id = $this->input->get_post('api_id');
        $service_code = $this->input->get_post('service_code');
        
        $list_apis = $this->shipping_apis_m->get_all();
        
        $this->template->set('api_id', $api_id);
        $this->template->set('service_code', $service_code);
        $this->template->set('list_apis', $list_apis);
        
        $this->template->build('admin/shipping_service_form_api');
    }
    
    public function load_shipping_credential_form() {
        $this->template->set_layout(FALSE);
        
        $this->load->model('shipping/shipping_apis_m');
        $this->load->model('shipping/shipping_credentials_m');
        
        $api_id = $this->input->get_post('api_id');
        $credential_id = $this->input->get_post('credential_id');
        
        $list_apis = $this->shipping_apis_m->get_all();
        $list_credentials = $this->shipping_credentials_m->get_all();
        
        $this->template->set('list_credentials', $list_credentials);
        $this->template->set('list_apis', $list_apis);
        $this->template->set('api_id', $api_id);
        $this->template->set('credential_id', $credential_id);
        
        $this->template->build('admin/shipping_service_form_credential');
    }
    
    public function get_shipping_api_by_shipping_service() {
        $this->load->model('shipping/shipping_services_m');
        $service_id = $this->input->get_post("id");
        $service = $this->shipping_services_m->get($service_id);
        $this->success_output($service->external_shipping_api);
    }
    
}