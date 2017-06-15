<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin controller for the settings module
 */
class Api extends Admin_Controller
{
    /**
     * Validation array
     *
     * @var array
     */
    private $validation_rules = array();

    /**
     * Validation for basic profile data. The rest of the validation is built by streams.
     *
     * @var array
     */
    private $validation_rules_shipping_apis = array(
        array(
            'field' => 'name',
            'label' => 'Name',
            'rules' => 'required'
        ),
        array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => ''
        ),
        array(
            'field' => 'account_no',
            'label' => 'Account No',
            'rules' => ''
        ),
        array(
            'field' => 'meter_no',
            'label' => 'Meter No',
            'rules' => ''
        ),
        array(
            'field' => 'auth_key',
            'label' => 'Auth Key',
            'rules' => ''
        ),
        array(
            'field' => 'username',
            'label' => 'User name',
            'rules' => ''
        ),
        array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => ''
        ),
        array(
            'field' => 'site_id',
            'label' => 'Site ID',
            'rules' => ''
        ),
        array(
            'field' => 'carrier_id',
            'label' => 'Carriers',
            'rules' => ''
        ),
        array(
            'field' => 'estamp_partner_signature',
            'label' => 'E-Stamp PARTNER SIGNATURE',
            'rules' => ''
        ),
        array(
            'field' => 'estamp_namespace',
            'label' => 'E-Stamp Namespace',
            'rules' => ''
        ),
        array(
            'field' => 'price_includes_vat',
            'label' => 'Price includes VAT',
            'rules' => ''
        ),
        array(
            'field' => 'partner_id',
            'label' => 'partner to associate',
            'rules' => ''
        ),
        array(
            'field' => 'percental_partner_upcharge',
            'label' => 'percental upcharge',
            'rules' => ''
        )
    );
    
     private $validation_rules_shipping_credentials = array(
        array(
            'field' => 'name',
            'label' => 'Name',
            'rules' => 'required'
        ),
        array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => ''
        ),
        array(
            'field' => 'account_no',
            'label' => 'Account No',
            'rules' => ''
        ),
        array(
            'field' => 'meter_no',
            'label' => 'Meter No',
            'rules' => ''
        ),
        array(
            'field' => 'auth_key',
            'label' => 'Auth Key',
            'rules' => ''
        ),
        array(
            'field' => 'username',
            'label' => 'User name',
            'rules' => ''
        ),
        array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => ''
        ),
        array(
            'field' => 'estamp_partner_signature',
            'label' => 'E-Stamp PARTNER SIGNATURE',
            'rules' => ''
        ),
        array(
            'field' => 'estamp_namespace',
            'label' => 'E-Stamp Namespace',
            'rules' => ''
        ),
        array(
            'field' => 'partner_id',
            'label' => 'partner to associate',
            'rules' => ''
        ),
        array(
            'field' => 'percental_partner_upcharge',
            'label' => 'percental upcharge',
            'rules' => ''
        )
    );
    
    /**
     * partner validation rule.
     * @var type 
     */
    private $_partner_validation_rule = array(
        array(
            'field' => 'app_code',
            'label' => 'App code',
            'rules' => 'required|callback__check_app_code'
        ),
        array(
            'field' => 'app_name',
            'label' => 'App name',
            'rules' => 'required'
        ),
        array(
            'field' => 'app_key',
            'label' => 'App key',
            'rules' => 'required'
        ),
        array(
            'field' => 'version',
            'label' => 'version',
            'rules' => ''
        )
    );

    /**
     * Constructor method
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model(array(
            "api/app_external_m",
        ));
        $this->load->library('form_validation');
    }

    /**
     * Index method, lists all generic settings
     *
     * @return void
     */
    public function index()
    {
        $this->template->build('page_construction');
    }

    public function paypal()
    {
        // If user submit data
        if ($_POST) {
            // For LIVE system
            $PAYMENT_PAYPAL_USERNAME_CODE = $this->input->post('PAYMENT_PAYPAL_USERNAME_CODE');
            if (!empty($PAYMENT_PAYPAL_USERNAME_CODE)) {
                Settings::set(APConstants::PAYMENT_PAYPAL_USERNAME_CODE, $PAYMENT_PAYPAL_USERNAME_CODE);
            }

            $PAYMENT_PAYPAL_PASSWORD_CODE = $this->input->post('PAYMENT_PAYPAL_PASSWORD_CODE');
            if (!empty($PAYMENT_PAYPAL_PASSWORD_CODE)) {
                Settings::set(APConstants::PAYMENT_PAYPAL_PASSWORD_CODE, $PAYMENT_PAYPAL_PASSWORD_CODE);
            }

            $PAYMENT_PAYPAL_SIGNATURE_CODE = $this->input->post('PAYMENT_PAYPAL_SIGNATURE_CODE');
            if (!empty($PAYMENT_PAYPAL_SIGNATURE_CODE)) {
                Settings::set(APConstants::PAYMENT_PAYPAL_SIGNATURE_CODE, $PAYMENT_PAYPAL_SIGNATURE_CODE);
            }

            $PAYMENT_PAYPAL_MERCHANT_ID = $this->input->post('PAYMENT_PAYPAL_MERCHANT_ID');
            if (!empty($PAYMENT_PAYPAL_MERCHANT_ID)) {
                Settings::set(APConstants::PAYMENT_PAYPAL_MERCHANT_ID, $PAYMENT_PAYPAL_MERCHANT_ID);
            }
            $this->session->set_flashdata('success', lang('success'));
        }

        // Render the layout
        $this->template->set('header_title', lang('header:list_general_title'))->build('api/paypal');
    }

    public function google_adwords()
    {
        // If user submit data
        if ($_POST) {
            // For LIVE system
            $GOOGLE_ADWORD_API_KEY = $this->input->post('GOOGLE_ADWORD_API_KEY');
            if (!empty($GOOGLE_ADWORD_API_KEY)) {
                Settings::set(APConstants::GOOGLE_ADWORD_API_KEY, $GOOGLE_ADWORD_API_KEY);
            }

            $GOOGLE_ADWORD_CLIENT_ID = $this->input->post('GOOGLE_ADWORD_CLIENT_ID');
            if (!empty($GOOGLE_ADWORD_CLIENT_ID)) {
                Settings::set(APConstants::GOOGLE_ADWORD_CLIENT_ID, $GOOGLE_ADWORD_CLIENT_ID);
            }

            $GOOGLE_ADWORD_CLIENT_SECRET = $this->input->post('GOOGLE_ADWORD_CLIENT_SECRET');
            if (!empty($GOOGLE_ADWORD_CLIENT_SECRET)) {
                Settings::set(APConstants::GOOGLE_ADWORD_CLIENT_SECRET, $GOOGLE_ADWORD_CLIENT_SECRET);
            }

            $this->session->set_flashdata('success', lang('success'));
        }

        // Render the layout
        $this->template->set('header_title', lang('header:list_general_title'))->build('api/google_adwords');
    }
    // Phone number setting
    public function phone_number()
    {
        // If user submit data
        if ($_POST) {
            // For LIVE system
            $SONETEL_API_ENDPOINT = $this->input->post('SONETEL_API_ENDPOINT');
            if (!empty($SONETEL_API_ENDPOINT)) {
                Settings::set(APConstants::SONETEL_API_ENDPOINT, $SONETEL_API_ENDPOINT);
            }

            $SONETEL_API_KEY = $this->input->post('SONETEL_API_KEY');
            if (!empty($SONETEL_API_KEY)) {
                Settings::set(APConstants::SONETEL_API_KEY, $SONETEL_API_KEY);
            }

            $SONETEL_API_TOKEN = $this->input->post('SONETEL_API_TOKEN');
            if (!empty($SONETEL_API_TOKEN)) {
                Settings::set(APConstants::SONETEL_API_TOKEN, $SONETEL_API_TOKEN);
            }

            $this->session->set_flashdata('success', lang('success'));
        }

        // Render the layout
        $this->template->set('header_title', lang('header:list_general_title'))->build('api/phone_number');
    }
    
    // Server side OCR setting
    public function server_ocr()
    {
        // If user submit data
        if ($_POST) {
            // API Key
            $SERVER_OCR_API_KEY = $this->input->post('SERVER_OCR_API_KEY');
            if (!empty($SERVER_OCR_API_KEY)) {
                Settings::set(APConstants::SERVER_OCR_API_KEY, $SERVER_OCR_API_KEY);
            }
            
            // API Endpoint
            $SERVER_OCR_API_ENDPOINT = $this->input->post('SERVER_OCR_API_ENDPOINT');
            if (!empty($SERVER_OCR_API_ENDPOINT)) {
                Settings::set(APConstants::SERVER_OCR_API_ENDPOINT, $SERVER_OCR_API_ENDPOINT);
            }
            
            $this->session->set_flashdata('success', lang('success'));
        }

        // Render the layout
        $this->template->set('header_title', lang('header:list_general_title'))->build('api/server_ocr');
    }

    /**
     * Index method, lists all generic settings
     *
     * @return void
     */
    public function payone()
    {
        // If user submit data
        if ($_POST) {
            // For LIVE system
            $MERCHANT_ID_CODE = $this->input->post('MERCHANT_ID_CODE');
            if (!empty($MERCHANT_ID_CODE)) {
                Settings::set(APConstants::MERCHANT_ID_CODE, $MERCHANT_ID_CODE);
            }

            $PORTAL_ID_CODE = $this->input->post('PORTAL_ID_CODE');
            if (!empty($PORTAL_ID_CODE)) {
                Settings::set(APConstants::PORTAL_ID_CODE, $PORTAL_ID_CODE);
            }

            $PORTAL_KEY_CODE = $this->input->post('PORTAL_KEY_CODE');
            if (!empty($PORTAL_KEY_CODE)) {
                Settings::set(APConstants::PORTAL_KEY_CODE, $PORTAL_KEY_CODE);
            }

            $SUB_ACCOUNT_ID_CODE = $this->input->post('SUB_ACCOUNT_ID_CODE');
            if (!empty($SUB_ACCOUNT_ID_CODE)) {
                Settings::set(APConstants::SUB_ACCOUNT_ID_CODE, $SUB_ACCOUNT_ID_CODE);
            }

            // For test/dev system
            $TEST_MERCHANT_ID_CODE = $this->input->post('TEST_MERCHANT_ID_CODE');
            if (!empty($TEST_MERCHANT_ID_CODE)) {
                Settings::set(APConstants::TEST_MERCHANT_ID_CODE, $TEST_MERCHANT_ID_CODE);
            }

            $TEST_PORTAL_ID_CODE = $this->input->post('TEST_PORTAL_ID_CODE');
            if (!empty($TEST_PORTAL_ID_CODE)) {
                Settings::set(APConstants::TEST_PORTAL_ID_CODE, $TEST_PORTAL_ID_CODE);
            }

            $TEST_PORTAL_KEY_CODE = $this->input->post('TEST_PORTAL_KEY_CODE');
            if (!empty($TEST_PORTAL_KEY_CODE)) {
                Settings::set(APConstants::TEST_PORTAL_KEY_CODE, $TEST_PORTAL_KEY_CODE);
            }

            $TEST_SUB_ACCOUNT_ID_CODE = $this->input->post('TEST_SUB_ACCOUNT_ID_CODE');
            if (!empty($TEST_SUB_ACCOUNT_ID_CODE)) {
                Settings::set(APConstants::TEST_SUB_ACCOUNT_ID_CODE, $TEST_SUB_ACCOUNT_ID_CODE);
            }

            $this->session->set_flashdata('success', lang('success'));
        }

        // Render the layout
        $this->template->append_js('jquery.upload-1.0.0.min.js');
        $this->template->set('header_title', lang('header:list_general_title'))->build('api/payone');
    }

    /**
     * Index method, lists all generic settings
     *
     * @return void
     */
    public function mailchimp()
    {
        // If user submit data
        if ($_POST) {
            // For LIVE system
            $MAILCHIMP_API_KEY = $this->input->post('MAILCHIMP_API_KEY');
            if (!empty($MAILCHIMP_API_KEY)) {
                Settings::set(APConstants::MAILCHIMP_API_KEY, $MAILCHIMP_API_KEY);
            }

            $MAILCHIMP_LIST_ID = $this->input->post('MAILCHIMP_LIST_ID');
            if (!empty($MAILCHIMP_LIST_ID)) {
                Settings::set(APConstants::MAILCHIMP_LIST_ID, $MAILCHIMP_LIST_ID);
            }

            $this->session->set_flashdata('success', lang('success'));
        }

        // Render the layout
        $this->template->set('header_title', lang('header:list_general_title'))->build('api/mailchimp');
    }

    public function shipping_apis()
    {
        // If current request is ajax
        if ($this->is_ajax_request()) {
            $this->load->library('shipping/shipping_api');

            // Get input condition
            $name = $this->input->get_post("name");

            $arrayConditions = array();
            if (!empty($name)) {
                $arrayConditions["(name LIKE '%" . $name . "%')"] = null;
            }

            // update limit into user_paging.
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);

            // Get paging input
            $inputPaging = $this->get_paging_input();
            $inputPaging['limit'] = $limit;

            // Call search method
            $queryResult = shipping_api::getDataForPaging($arrayConditions, $inputPaging['start'], $inputPaging['limit'], $inputPaging['sort_column'], $inputPaging['sort_type']);

            // Process output data
            $total = $queryResult['total'];
            $rows = $queryResult['data'];

            // Get output response
            $response = $this->get_paging_output($total, $inputPaging['limit'], $inputPaging['page']);

            $i = 0;
            foreach ($rows as $row) {
                $response->rows[$i]['id'] = $row->id;
                $response->rows[$i]['cell'] = array(
                    $row->id,
                    $row->name,
                    $row->description,
                    $row->account_no,
                    $row->id
                );
                $i++;
            }

            echo json_encode($response);
        } else {
            $this->template->build('api/shipping_api_list');
        }
    }

    /**
     * Method for handling different form actions
     */
    public function add_shipping_api()
    {
        $this->load->library('partner/partner_api');
        $this->load->library('shipping/shipping_api');
        $this->lang->load('shipping/shipping');

        $this->template->set_layout(FALSE);

        $shipping_api = new stdClass();
        $shipping_api->id = '';

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->form_validation->set_rules($this->validation_rules_shipping_apis);
            if ($this->form_validation->run()) {
                $newShippingAPI = array(
                    'carrier_id' => $this->input->post('carrier_id'),
                    'name' => $this->input->post('name'),
                    'description' => $this->input->post('description'),
                    'account_no' => $this->input->post('account_no'),
                    'meter_no' => $this->input->post('meter_no'),
                    'auth_key' => $this->input->post('auth_key'),
                    'password' => $this->input->post('password'),
                    'site_id' => $this->input->post('site_id'),
                    'username' => $this->input->post('username'),
                    'estamp_partner_signature' => $this->input->post('estamp_partner_signature'),
                    'estamp_namespace' => $this->input->post('estamp_namespace'),
                    'price_includes_vat' => $this->input->post('price_includes_vat'),
                    'partner_id' => $this->input->post('partner_id'),
                    'percental_partner_upcharge' => $this->input->post('percental_partner_upcharge')
                );
                if (shipping_api::addShippingAPI($newShippingAPI)) {
                    $message = lang('shipping_api.add_success');
                    $this->success_output($message);
                    return true;
                } else {
                    $message = lang('shipping_api.add_error');
                    $this->error_output($message);
                    return false;
                }
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return false;
            }
        }

        // Loop through each validation rule
        foreach ($this->validation_rules_shipping_apis as $rule) {
            $shipping_api->{$rule ['field']} = set_value($rule ['field']);
        }
        $shipping_api->price_includes_vat = '0.00';

        $allCarriers = shipping_api::getAllShippingCarriers();
        $this->template->set('list_carriers', $allCarriers);
        
        $list_partner = partner_api::getPartnerAll();
        $this->template->set('list_partner', $list_partner);

        $this->template->set('shipping_api', $shipping_api);
        $this->template->set('action_type', 'add');

        // Display the current page
        $this->template->append_js('jquery.upload-1.0.0.min.js');
        $this->template->build('api/shipping_api_form');
    }

    /**
     * Method for handling different form actions
     */
    public function edit_shipping_api()
    {
        $this->load->library('partner/partner_api');
        $this->load->library('shipping/shipping_api');
        $this->lang->load('shipping/shipping');

        $this->template->set_layout(FALSE);

        $id = $this->input->get_post('id');
        $shippingAPI = shipping_api::getShippingAPIByID($id);

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->form_validation->set_rules($this->validation_rules_shipping_apis);
            if ($this->form_validation->run()) {
                $data = array(
                    'carrier_id' => $this->input->post('carrier_id'),
                    'name' => $this->input->post('name'),
                    'description' => $this->input->post('description'),
                    'account_no' => $this->input->post('account_no'),
                    'meter_no' => $this->input->post('meter_no'),
                    'auth_key' => $this->input->post('auth_key'),
                    'password' => $this->input->post('password'),
                    'site_id' => $this->input->post('site_id'),
                    'username' => $this->input->post('username'),
                    'estamp_partner_signature' => $this->input->post('estamp_partner_signature'),
                    'estamp_namespace' => $this->input->post('estamp_namespace'),
                    'price_includes_vat' => $this->input->post('price_includes_vat'),
                    'partner_id' => $this->input->post('partner_id'),
                    'percental_partner_upcharge' => $this->input->post('percental_partner_upcharge')
                );
                if (shipping_api::updateShippingAPI($id, $data)) {
                    $message = lang('shipping_api.edit_success');
                    $this->success_output($message);
                    return true;
                } else {
                    $message = lang('shipping_api.edit_error');
                    $this->error_output($message);
                    return false;
                }
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return false;
            }
        }

        $allCarriers = shipping_api::getAllShippingCarriers();
        $this->template->set('list_carriers', $allCarriers);
        
        $list_partner = partner_api::getPartnerAll();
        $this->template->set('list_partner', $list_partner);

        $this->template->set('shipping_api', $shippingAPI);
        $this->template->set('action_type', 'edit');

        // Display the current page
        $this->template->append_js('jquery.upload-1.0.0.min.js');
        $this->template->build('api/shipping_api_form');
    }

    /**
     * Delete shipping api
     */
    public function delete_shipping_api()
    {
        $this->load->library('shipping/shipping_api');
        $this->lang->load('shipping/shipping');

        $id = $this->input->get_post("id");
        shipping_api::deleteShippingAPI($id);

        $message = lang('shipping_api.delete_success');
        $this->success_output($message);
        return true;
    }

    public function etc()
    {
        //  TODO:
        $this->template->build('page_construction');
    }
    
    /**
     * setting of app external.
     */
    public function partners(){
        if(!APContext::isSupperAdminUser()){
            redirect('/admin');
        }
        
        if($this->is_ajax_request() && $_POST){
            // update limit into user_paging.
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;
            
            $array_condition = array(
                "disable_flag" => APConstants::OFF_FLAG
            );

            // Call search method
            $query_result = $this->app_external_m->get_paging($array_condition, $input_paging['start'], $input_paging['limit'], $input_paging['sort_column'], $input_paging['sort_type']);

            // Process output data
            $total = $query_result['total'];
            $datas = $query_result['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);

            $i = 0;
            foreach ($datas as $row) {
                $response->rows[$i]['cell'] = array(
                    $row->id,
                    $row->app_code,
                    $row->app_name,
                    $row->app_key,
                    $row->version,
                    $row->id,
                );
                $i++;
            }
            echo json_encode($response);
            return;
        }
        
        $this->template->build('api/partner_list');
    }
    
    /**
     * add/edit setting of app external.
     */
    public function edit_partners(){
        $this->template->set_layout(false);
        
        if(!APContext::isSupperAdminUser()){
            redirect('/admin');
        }
        
        // get id
        $app_id = $this->input->get_post('id');
        if($_POST){
            $this->form_validation->set_rules($this->_partner_validation_rule);
            if ($this->form_validation->run()) {
                $data = array(
                    'app_code' => $this->input->post('app_code'),
                    'app_key' => $this->input->post('app_key'),
                    'app_name' => $this->input->post('app_name'),
                    'version' => $this->input->post('version'),
                    'disable_flag' => 0
                );
                if(empty($app_id)){
                    $this->app_external_m->insert($data);
                }else{
                    $this->app_external_m->update_by_many(array(
                        "id" => $app_id
                    ), $data);
                }
                $message = lang('partner_app_external.add_successful');
                $this->success_output($message);
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return false;
            }
        }
        
        if(empty($app_id)){
            $partner = new stdClass();
            $partner->id = '';
            foreach ($this->_partner_validation_rule as $rule) {
                $partner->{$rule ['field']} = set_value($rule ['field']);
            }
        }else{
            $partner = $this->app_external_m->get($app_id);
        }
        
        $this->template->set("partner", $partner);
        $this->template->build('api/partner_form');
    }
    
    /**
     * delete partner
     */
    public function delete_partners(){
        $this->template->set_layout(false);
        
        $app_id = $this->input->post('id');
        if(!empty($app_id)){
            $this->app_external_m->update_by_many(array(
                "id" => $app_id
            ), array(
                'disable_flag' => APConstants::ON_FLAG
            ));
        }
        
        $this->success_output('');
        return;
    }
    
    /**
     * generate key for app.
     * @return type
     */
    public function generate_app_key(){
        $this->template->set_layout(false);
        
        $key = md5(uniqid());
        $this->success_output('', array($key));
        return;
    }
    
    /**
     * validate app code.
     */
    public function _check_app_code(){
        $app_id = $this->input->post('id');
        $app_code = $this->input->post('app_code');
        
        $partner = $this->app_external_m->get_by('app_code', $app_code);
        if ( !empty($partner) && $partner->id != $app_id) {
            $this->form_validation->set_message('_check_app_code', 'App code is existed');
            return false;
        }
        
        return  true;
    }
    
    public function shipping_credentials()
    {
        // If current request is ajax
        if ($this->is_ajax_request()) {
            $this->load->model('shipping/shipping_credentials_m');

            // Get input condition
            $search_text = $this->input->get_post("search_text");

            $arrayConditions = array();
            if (!empty($search_text)) {
                $arrayConditions["(name LIKE '%" . $search_text . "%')"] = null;
            }

            // update limit into user_paging.
            $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);

            // Get paging input
            $inputPaging = $this->get_paging_input();
            $inputPaging['limit'] = $limit;

            // Call search method
            $queryResult = $this->shipping_credentials_m->get_paging($arrayConditions, $inputPaging['start'], $inputPaging['limit'], $inputPaging['sort_column'], $inputPaging['sort_type']);

            // Process output data
            $total = $queryResult['total'];
            $rows = $queryResult['data'];

            // Get output response
            $response = $this->get_paging_output($total, $inputPaging['limit'], $inputPaging['page']);

            $i = 0;
            foreach ($rows as $row) {
                $response->rows[$i]['id'] = $row->id;
                $response->rows[$i]['cell'] = array(
                    $row->id,
                    $row->name,
                    $row->description
                );
                $i++;
            }

            echo json_encode($response);
        } else {
            $this->template->build('api/shipping_credential_list');
        }
    }
    
     public function add_shipping_credential()
    {
        $this->load->library('partner/partner_api');
        $this->lang->load('shipping/shipping');
        $this->load->model('shipping/shipping_credentials_m');

        $this->template->set_layout(FALSE);

        $shipping_credential = new stdClass();
        $shipping_credential->id = '';

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->form_validation->set_rules($this->validation_rules_shipping_credentials);
            if ($this->form_validation->run()) {
                $newShippingCredential = array(
                    'name' => $this->input->post('name'),
                    'description' => $this->input->post('description'),
                    'account_no' => $this->input->post('account_no'),
                    'meter_no' => $this->input->post('meter_no'),
                    'auth_key' => $this->input->post('auth_key'),
                    'password' => $this->input->post('password'),
                    'username' => $this->input->post('username'),
                    'estamp_partner_signature' => $this->input->post('estamp_partner_signature'),
                    'estamp_namespace' => $this->input->post('estamp_namespace'),
                    'partner_id' => $this->input->post('partner_id'),
                    'percental_partner_upcharge' => $this->input->post('percental_partner_upcharge')
                );

                if ($this->shipping_credentials_m->insert($newShippingCredential)) {
                    $message = lang('shipping_api.add_success');
                    $this->success_output($message);
                    return true;
                } else {
                    $message = lang('shipping_api.add_error');
                    $this->error_output($message);
                    return false;
                }
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return false;
            }
        }

        // Loop through each validation rule
        foreach ($this->validation_rules_shipping_credentials as $rule) {
            $shipping_credential->{$rule ['field']} = set_value($rule ['field']);
        }
        
        $list_partner = partner_api::getPartnerAll();
        $this->template->set('list_partner', $list_partner);

        $this->template->set('shipping_credential', $shipping_credential);
        $this->template->set('action_type', 'add');

        // Display the current page
        $this->template->append_js('jquery.upload-1.0.0.min.js');
        $this->template->build('api/shipping_credential_form');
    }
    
    public function edit_shipping_credential()
    {
        $this->load->library('partner/partner_api');
        $this->lang->load('shipping/shipping');
        $this->load->model('shipping/shipping_credentials_m');

        $this->template->set_layout(FALSE);

        $id = $this->input->get_post('id');
        $shipping_credential = $this->shipping_credentials_m->get($id);

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $this->form_validation->set_rules($this->validation_rules_shipping_credentials);
            if ($this->form_validation->run()) {
                $data = array(
                    'name' => $this->input->post('name'),
                    'description' => $this->input->post('description'),
                    'account_no' => $this->input->post('account_no'),
                    'meter_no' => $this->input->post('meter_no'),
                    'auth_key' => $this->input->post('auth_key'),
                    'password' => $this->input->post('password'),
                    'username' => $this->input->post('username'),
                    'estamp_partner_signature' => $this->input->post('estamp_partner_signature'),
                    'estamp_namespace' => $this->input->post('estamp_namespace'),
                    'partner_id' => $this->input->post('partner_id'),
                    'percental_partner_upcharge' => $this->input->post('percental_partner_upcharge')
                );

                if ($this->shipping_credentials_m->update($id, $data)) {
                    $message = lang('shipping_api.edit_success');
                    $this->success_output($message);
                    return true;
                } else {
                    $message = lang('shipping_api.edit_error');
                    $this->error_output($message);
                    return false;
                }
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return false;
            }
        }

        $list_partner = partner_api::getPartnerAll();
        $this->template->set('list_partner', $list_partner);

        $this->template->set('shipping_credential', $shipping_credential);
        $this->template->set('action_type', 'edit');

        // Display the current page
        $this->template->append_js('jquery.upload-1.0.0.min.js');
        $this->template->build('api/shipping_credential_form');
    }
    
     public function delete_shipping_credential()
    {
        $this->lang->load('shipping/shipping');
        $this->load->model('shipping/shipping_credentials_m');

        $id = $this->input->get_post("id");
        ci()->shipping_credentials_m->delete($id);

        $message = lang('shipping_api.delete_success');
        $this->success_output($message);
        return true;
    }
    
}