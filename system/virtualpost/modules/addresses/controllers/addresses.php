<?php defined('BASEPATH') or exit('No direct script access allowed');

class addresses extends AccountSetting_Controller
{
    /**
     * Validation for basic profile data.
     * The rest of the validation is built by streams.
     *
     * @var array
     */
    private $validation_rules = array(
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
            'rules' => ''
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

    /*
     * Rules of forward address.
     */
    private $validation_rules_01 = array(
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
        array(
            'field' => 'shipment_address_name_alt[]',
            'label' => 'lang:invoicing_address_name',
            'rules' => 'validname'
        ),
        array(
            'field' => 'shipment_company_alt[]',
            'label' => 'lang:shipment_company',
            'rules' => 'validname|callback__check_shipment_company'
        ),
        array(
            'field' => 'shipment_street_alt[]',
            'label' => 'lang:shipment_street',
            'rules' => 'required'
        ),
        array(
            'field' => 'shipment_postcode_alt[]',
            'label' => 'lang:shipment_postcode',
            'rules' => 'required|postcode'
        ),
        array(
            'field' => 'shipment_city_alt[]',
            'label' => 'lang:shipment_city',
            'rules' => 'required|validname'
        ),
        array(
            'field' => 'shipment_region_alt[]',
            'label' => 'lang:shipment_region',
            'rules' => ''
        ),
        array(
            'field' => 'shipment_country_alt[]',
            'label' => 'lang:shipment_country',
            'rules' => 'required'
        ),
        array(
            'field' => 'shipment_phone_number_alt[]',
            'label' => 'lang:shipment_phone_number',
            'rules' => 'phong_number'
        )
    );

    /*
     * Rules of new forward address.
     */
    private $validation_rules_02 = array(
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

        // Model
        $this->load->model('addresses/customers_address_m');
        $this->load->model('addresses/customers_forward_address_m');
        $this->load->model('addresses/location_m');
        $this->load->model('addresses/customer_location_m');
        $this->load->model('mailbox/postbox_m');
        $this->load->model('customers/customer_m');
        $this->load->model('settings/countries_m');
        $this->lang->load('address');
        $this->lang->load('account/account');
        $this->load->library('form_validation');
        $this->load->library('addresses/addresses_api');
		$this->load->library('customers/customers_api');
    }

    /**
     * Index Page for this controller.
     * Maps to the following URL http://example.com/index.php/welcome - or -
     * http://example.com/index.php/welcome/index - or - Since this controller
     * is set as the default controller in config/routes.php, it's displayed
     * at http://example.com/ So any other public methods not prefixed with an
     * underscore will map to /index.php/welcome/<method_name>
     *
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index()
    {
        $base_url = base_url() . 'addresses';

        // load address
        $this->load_address();

        // load postbox
        $this->load_postbox();

        // load location
        $this->load_location();

        // load location public
        // $this->load_location_public();

        // Get all countries
        $countries = $this->countries_m->get_many_by_many(array(), '', false, array(
            'country_name' => 'ASC'
        ));

        $customer = $this->customer_m->get_current_customer_info();
        $this->template->set('customer', $customer);

        // Display the current page
        $this->template->set('countries', $countries);
        APContext::reloadCustomerLoggedIn();
        // load the theme_example view
        $this->template->build('index');
    }

    /**
     * save shipment and invoice address.
     */
    public function save_vat()
    {
        $this->template->set_layout(FALSE);
        // Gets customerid logged in.
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $vatnum = $this->input->get_post('vatnum');
        $CompanyName = htmlspecialchars($this->input->get_post('CompanyName'));
        $Location = htmlspecialchars($this->input->get_post('Street'));
        $ZipCode = htmlspecialchars($this->input->get_post('PostCode'));
        $StreetAddress = htmlspecialchars($this->input->get_post('City'));

        // Load VAT Validation library
        $countryCode = substr($vatnum, 0, 2);
        $vatNumber = substr($vatnum, 2, strlen($vatnum));

        $check_vat_result = false;
        if ($countryCode == 'DE') {
            $this->load->library('Nusoap_lib');
            $nusoap_client = new nusoap_client(Settings::get(APConstants::LINK_CHECK_VAT), 'wsdl');
            if ($nusoap_client->fault) {
                throw new Exception('The Soap library has to be installed and enabled');
            }
            $proxy = $nusoap_client->getProxy();
            $rs = $proxy->checkVat(array(
                'countryCode' => $countryCode,
                'vatNumber' => $vatNumber
            ));
            $return_data = array();
            if ($rs['valid']) {
                $return_data = array(
                    'name' => $rs['name'],
                    'address' => $rs['address']
                );
                $check_vat_result = true;
            }
        } else {
            $this->load->library('CheckVAT');
            $UstId_1 = Settings::get(APConstants::INSTANCE_OWNER_VAT_NUM_CODE);
            $UstId_2 = $vatnum;

            $result = CheckVAT::validate($UstId_1, $UstId_2, $CompanyName, $Location, $ZipCode, $StreetAddress);
            if ($result) {
                $check_vat_result = true;
            }
        }
        if (!$check_vat_result) {
            $this->error_output("VAT Number isn't valid.", array(
                'name' => '',
                'address' => ''
            ));
            return;
        }
        // Update data to customer
        $this->customer_m->update_by_many(array(
            "customer_id" => $customer_id
        ), array(
            "vat_number" => $vatnum
        ));

        APContext::reloadCustomerLoggedIn();

        //#1309: Insert customer history
        $history = [
            'customer_id' => $customer_id,
            'action_type' => APConstants::CUSTOMER_HISTORY_ACTIVITY_UPDATE_VAT_NUMBER,
            'created_by_id' => APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER,
            'current_data' => $vatnum,
        ];
        customers_api::insertCustomerHistory([$history]);

        $message = lang('save_vat_success');
        $this->success_output($message);
    }

    /**
     * save shipment and invoice address.
     */
    public function remove_vat()
    {
        $this->template->set_layout(FALSE);

        // Gets customerid logged in.
        $customer_id = APContext::getCustomerCodeLoggedIn();

        // Update data to customer
        $this->customer_m->update_by_many(array(
            "customer_id" => $customer_id
        ), array(
            "vat_number" => ''
        ));

        //#1309: Insert customer history
        $history = [
            'customer_id' => $customer_id,
            'action_type' => APConstants::CUSTOMER_HISTORY_ACTIVITY_REMOVE_VAT_NUMBER,
            'created_by_id' => APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER,
            'current_data' => null,
        ];
        customers_api::insertCustomerHistory([$history]);

        APContext::reloadCustomerLoggedIn();
        $message = lang('remove_vat_success');
        $this->success_output($message);
    }

    /**
     * save shipment and invoice address.
     */
    public function save_address()
    {
        $this->load->library('invoices/invoices_api');

        $this->template->set_layout(FALSE);
        // Gets customerid logged in.
        $customer_id = APContext::getCustomerCodeLoggedIn();

        // Gets customer address infor.
        $check = $this->customers_address_m->get_by('customer_id', $customer_id);

        if ($_POST) {
            $this->form_validation->set_rules($this->validation_rules);
            if ($this->form_validation->run()) {
                //extract($_POST);
                $shipment_address_name    = $this->input->post('shipment_address_name');
                $shipment_company         = $this->input->post('shipment_company');
                $shipment_street          = $this->input->post('shipment_street');
                $shipment_postcode        = $this->input->post('shipment_postcode');
                $shipment_city            = $this->input->post('shipment_city');
                $shipment_region          = $this->input->post('shipment_region');
                $shipment_country         = $this->input->post('shipment_country');
                $shipment_phone_number    = $this->input->post('shipment_phone_number');
                $invoicing_address_name   = $this->input->post('invoicing_address_name');
                $invoicing_company        = $this->input->post('invoicing_company');
                $invoicing_street         = $this->input->post('invoicing_street');
                $invoicing_postcode       = $this->input->post('invoicing_postcode');
                $invoicing_city           = $this->input->post('invoicing_city');
                $invoicing_region         = $this->input->post('invoicing_region');
                $invoicing_country        = $this->input->post('invoicing_country');
                $invoicing_phone_number   = $this->input->post('invoicing_phone_number');

                if ($check) {
                    // Get country entity
                    $invoicing_country_entity = $this->countries_m->get($invoicing_country);
                    $customers_address_check = $this->customers_address_m->get_by_many(array(
                        "customer_id" => $customer_id
                    ));

                    // update address information.
                    $data = array(
                        'shipment_address_name' => $shipment_address_name,
                        'shipment_company' => $shipment_company,
                        'shipment_street' => $shipment_street,
                        'shipment_postcode' => $shipment_postcode,
                        'shipment_city' => $shipment_city,
                        'shipment_region' => $shipment_region,
                        'shipment_country' => $shipment_country,
                        'shipment_phone_number' => $shipment_phone_number,
                        'invoicing_address_name' => $invoicing_address_name,
                        'invoicing_company' => $invoicing_company,
                        'invoicing_street' => $invoicing_street,
                        'invoicing_postcode' => $invoicing_postcode,
                        'invoicing_city' => $invoicing_city,
                        'invoicing_region' => $invoicing_region,
                        'invoicing_country' => $invoicing_country,
                        'invoicing_phone_number' => $invoicing_phone_number,
                        'eu_member_flag' => $invoicing_country_entity->eu_member_flag
                    );

                    // Ticket #361 (If the customer changes the invoicing company name
                    // the VAT check has to be redone, therefore the sign erased.
                    if ($invoicing_company != $check->invoicing_company) {
                        // Update VAT sign (reset vat number in customers)
                        $this->customer_m->update_by_many(array(
                            "customer_id" => $customer_id
                        ), array(
                            "vat_number" => ''
                        ));

                        // Reload customers information
                        APContext::reloadCustomerLoggedIn();
                    }

                    // Ticket #563 Case Management
                    if (!empty($customers_address_check) && ($customers_address_check->invoicing_company != $data['invoicing_company'])) {
                        $list_case_number = APUtils::get_list_case_invoice_address($customer_id);
                        if (count($list_case_number) > 0) {
                            // Check if this customer already change, we need to reset invoice_address_verification_flag = 0
                            // that mean the system need to verification address again
                            if (!empty($customers_address_check) && ($customers_address_check->invoicing_company != $data['invoicing_company'])) {
                                $data['invoice_address_verification_flag'] = APConstants::OFF_FLAG;
                            }
                        }
                    }

                    // End fix ticket #563
                    $this->customers_address_m->update($customer_id, $data);

                    // Ticket #563 Case Management
                    if (!empty($customers_address_check)
                        && ($customers_address_check->invoicing_company != $data['invoicing_company']
                            || $customers_address_check->invoicing_address_name != $data['invoicing_address_name']
                            || $customers_address_check->invoicing_street != $data['invoicing_street']
                            || $customers_address_check->invoicing_postcode != $data['invoicing_postcode']
                            || $customers_address_check->invoicing_city != $data['invoicing_city']
                            || $customers_address_check->invoicing_region != $data['invoicing_region']
                            || $customers_address_check->invoicing_country != $data['invoicing_country']
                            || $customers_address_check->invoicing_phone_number != $data['invoicing_phone_number'])
                    ) {
                        // reset case
                        CaseUtils::start_case_verification_by_postbox(true, APContext::getCustomerLoggedIn(), null);
                    }
                    // End fix ticket #563

                    // Update data to customer
                    $this->customer_m->update_by_many(array(
                        "customer_id" => $customer_id
                    ), array(
                        "shipping_address_completed" => APConstants::ON_FLAG,
                        "invoicing_address_completed" => APConstants::ON_FLAG
                    ));

                    // update: convert registration process flag to customer_product_setting.
                    CustomerProductSetting::set_many($customer_id, APConstants::CLEVVERMAIL_PRODUCT, array(
                        "shipping_address_completed",
                        "invoicing_address_completed"
                    ), array(
                        APConstants::ON_FLAG,
                        APConstants::ON_FLAG
                    ));

                    $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
                    $open_balance = $open_balance_data['OpenBalanceDue'];
                    if ($open_balance <= 0.1) {
                        // we dont have to tell the Customer that a valid credit Card with non working payment can reactivate the account.
                        // in most cases the Customer will Chose a Card that can handle the payment if it is valid
                        // Only reactivate if deactivated_type = auto
                        $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;
                        customers_api::reactivateCustomer($customer_id, $created_by_id);
                    }
                } else {
                    // Get country entity
                    // $shipment_country_entity =
                    // $this->countries_m->get($shipment_country);
                    $invoicing_country_entity = $this->countries_m->get($invoicing_country);
                    $eu_member_flag = 0;
                    if ($invoicing_country_entity) {
                        $eu_member_flag = $invoicing_country_entity->eu_member_flag;
                    }
                    // insert new address information.
                    $data = array(
                        'customer_id' => $customer_id,
                        'shipment_address_name' => $shipment_address_name,
                        'shipment_company' => $shipment_company,
                        'shipment_street' => $shipment_street,
                        'shipment_postcode' => $shipment_postcode,
                        'shipment_city' => $shipment_city,
                        'shipment_region' => $shipment_region,
                        'shipment_country' => $shipment_country,
                        'shipment_phone_number' => $shipment_phone_number,
                        'invoicing_address_name' => $invoicing_address_name,
                        'invoicing_company' => $invoicing_company,
                        'invoicing_street' => $invoicing_street,
                        'invoicing_postcode' => $invoicing_postcode,
                        'invoicing_city' => $invoicing_city,
                        'invoicing_region' => $invoicing_region,
                        'invoicing_country' => $invoicing_country,
                        'invoicing_phone_number' => $invoicing_phone_number,
                        'invoice_address_verification_flag' => APConstants::ON_FLAG,
                        'eu_member_flag' => $eu_member_flag
                    );
                    $this->customers_address_m->insert($data);

                    // Update data to customer
                    $this->customer_m->update_by_many(array(
                        "customer_id" => $customer_id
                    ), array(
                        "shipping_address_completed" => APConstants::ON_FLAG,
                        "invoicing_address_completed" => APConstants::ON_FLAG
                    ));

                    // update: convert registration process flag to customer_product_setting.
                    CustomerProductSetting::set_many($customer_id, APConstants::CLEVVERMAIL_PRODUCT, array(
                        "shipping_address_completed",
                        "invoicing_address_completed"
                    ), array(
                        APConstants::ON_FLAG,
                        APConstants::ON_FLAG
                    ));

                    $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
                    $open_balance = $open_balance_data['OpenBalanceDue'];
                    if ($open_balance <= 0.1) {
                        // we dont have to tell the Customer that a valid credit Card with non working payment can reactivate the account.
                        // in most cases the Customer will Chose a Card that can handle the payment if it is valid
                        // Only reactivate if deactivated_type = auto
                        $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;
                        customers_api::reactivateCustomer($customer_id, $created_by_id);
                    }
                }

                // update invoice VAT of customer
                invoices_api::update_invoice_vat($customer_id, APUtils::getCurrentYear(), APUtils::getCurrentMonth(), null);

                // trigger start case.
                CaseUtils::start_verification_case($customer_id);

                APContext::reloadCustomerLoggedIn();
                $message = lang('change_address_setting_success');
                $this->success_output($message);
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        $this->template->build('index');
        // redirect("addresses");
    }

    /**
    * Des: Check if data empty is not processing.
    */
    public function checkEmptyData($data){

        $shipment_id_alt = array("");
        $shipment_address_name_alt = array("");
        $shipment_company_alt = array("");
        $shipment_street_alt = array("");
        $shipment_postcode_alt = array("");
        $shipment_city_alt = array("");
        $shipment_region_alt = array("");
        $shipment_phone_number_alt = array("");

        extract($data);
        if (isset($shipment_id_alt) && count($shipment_id_alt)){

            if(is_array($shipment_id_alt) && (count($shipment_id_alt) > 0) ){
                for ($i = 0; $i < count($shipment_id_alt); $i++) {
                    if( ($shipment_id_alt[$i] == 0) && ($shipment_address_name_alt[$i] == '') && ($shipment_company_alt[$i] == '') && ($shipment_street_alt[$i] == '') && ($shipment_postcode_alt[$i] == '') && ($shipment_city_alt[$i] == '') && ($shipment_region_alt[$i] == '') && ($shipment_phone_number_alt[$i] == '')){

                        unset($data['shipment_id_alt'][$i]);
                        unset($data['shipment_address_name_alt'][$i]);
                        unset($data['shipment_company_alt'][$i]);
                        unset($data['shipment_street_alt'][$i]);
                        unset($data['shipment_postcode_alt'][$i]);
                        unset($data['shipment_city_alt'][$i]);
                        unset($data['shipment_region_alt'][$i]);
                        unset($data['shipment_country_alt'][$i]);
                        unset($data['shipment_phone_number_alt'][$i]);
                    }
                }
            }
            else {

                if( ($shipment_id_alt == 0) && ($shipment_address_name_alt == '') && ($shipment_company_alt == '') && ($shipment_street_alt == '') && ($shipment_postcode_alt == '') && ($shipment_city_alt == '') && ($shipment_region_alt == '') && ($shipment_phone_number_alt == '')){

                    unset($data['shipment_id_alt']);
                    unset($data['shipment_address_name_alt']);
                    unset($data['shipment_company_alt']);
                    unset($data['shipment_street_alt']);
                    unset($data['shipment_postcode_alt']);
                    unset($data['shipment_city_alt']);
                    unset($data['shipment_region_alt']);
                    unset($data['shipment_country_alt']);
                    unset($data['shipment_phone_number_alt']);
                }

            }

        }
        return $data;
    }

    /**
     * save forward address.
     */
    public function save_forward_address()
    {
        $this->template->set_layout(FALSE);

        if ($_POST) {

            $data_submit = json_decode($_POST['data_submit']);
            $data_submit = APUtils::convertObjectToArray($data_submit);
            $envelope_id = $data_submit['envelope_id'];
            foreach ($data_submit as $k => $v) {

                if(strpos($k,"alt") != false){

                    $new_key = substr($k, 0,(strlen($k)-2));
                    $data_submit[$new_key] = $v;
                    unset($data_submit[$k]);
                }
            }
            unset($_POST);
            $_POST = $data_submit;
            $_POST = $this->checkEmptyData($_POST);
            $rules = $this->validation_rules_01;

            $customer_id= $_POST['customer_id'];
            if(empty($customer_id)){
                $customer_id = APContext::getCustomerCodeLoggedIn();
            }

            $check = $this->customers_address_m->get_by('customer_id', $customer_id);

            if(!isset($_POST['shipment_id_alt'])){
                unset($rules[8]);unset($rules[9]);
                unset($rules[10]);unset($rules[11]);
                unset($rules[12]);unset($rules[13]);
                unset($rules[14]);unset($rules[15]);
            }
            $this->form_validation->set_rules($rules);
            if ($this->form_validation->run()) {

                $shipment_address_name    = $this->input->post('shipment_address_name');
                $shipment_company         = $this->input->post('shipment_company');
                $shipment_street          = $this->input->post('shipment_street');
                $shipment_postcode        = $this->input->post('shipment_postcode');
                $shipment_city            = $this->input->post('shipment_city');
                $shipment_region          = $this->input->post('shipment_region');
                $shipment_country         = $this->input->post('shipment_country');
                $shipment_phone_number    = $this->input->post('shipment_phone_number');

                if(isset($_POST['shipment_id_alt'])){
                    $shipment_address_name_alt      = $this->input->post('shipment_address_name_alt');
                    $shipment_company_alt           = $this->input->post('shipment_company_alt');
                    $shipment_street_alt            = $this->input->post('shipment_street_alt');
                    $shipment_postcode_alt          = $this->input->post('shipment_postcode_alt');
                    $shipment_city_alt              = $this->input->post('shipment_city_alt');
                    $shipment_region_alt            = $this->input->post('shipment_region_alt');
                    $shipment_country_alt           = $this->input->post('shipment_country_alt');
                    $shipment_phone_number_alt      = $this->input->post('shipment_phone_number_alt');
                    $shipment_id_alt                = $this->input->post('shipment_id_alt');
                }

                $data = array(
                    'customer_id' => $customer_id,
                    'shipment_address_name' => $shipment_address_name,
                    'shipment_company' => $shipment_company,
                    'shipment_street' => $shipment_street,
                    'shipment_postcode' => $shipment_postcode,
                    'shipment_city' => $shipment_city,
                    'shipment_region' => $shipment_region,
                    'shipment_country' => $shipment_country,
                    'shipment_phone_number' => $shipment_phone_number,
                    'last_modified_date' => date("Y-m-d H:i:s")
                );

                if ($check) {
                    $this->customers_address_m->update($customer_id, $data);
                } else {
                    $data['invoicing_country'] = $shipment_country;
                    $data['created_date'] = date("Y-m-d H:i:s");
                    $this->customers_address_m->insert($data);
                }

                //Alternative forward address
                if ( isset($_POST['shipment_id_alt']) && count($shipment_id_alt)):
                    // open transaction.
                    $this->customer_m->db->trans_begin();

                    if(is_array($shipment_id_alt) && count($shipment_id_alt) > 0){
                        for ($i  = 0; $i < count($shipment_id_alt); $i++) {

                            $data = array(
                                'customer_id' => $customer_id,
                                'shipment_address_name' => $shipment_address_name_alt[$i],
                                'shipment_company' => $shipment_company_alt[$i],
                                'shipment_street' => $shipment_street_alt[$i],
                                'shipment_postcode' => $shipment_postcode_alt[$i],
                                'shipment_city' => $shipment_city_alt[$i],
                                'shipment_region' => $shipment_region_alt[$i],
                                'shipment_country' => $shipment_country_alt[$i],
                                'shipment_phone_number' => $shipment_phone_number_alt[$i],
                                'update_date' => date("Y-m-d H:i:s")
                            );

                            addresses_api::save_forward_address($shipment_id_alt[$i], $data);
                        }
                    }
                    else{

                        $data = array(
                            'customer_id' => $customer_id,
                            'shipment_address_name' => $shipment_address_name_alt,
                            'shipment_company' => $shipment_company_alt,
                            'shipment_street' => $shipment_street_alt,
                            'shipment_postcode' => $shipment_postcode_alt,
                            'shipment_city' => $shipment_city_alt,
                            'shipment_region' => $shipment_region_alt,
                            'shipment_country' => $shipment_country_alt,
                            'shipment_phone_number' => $shipment_phone_number_alt,
                            'update_date' => date("Y-m-d H:i:s")
                        );
                        addresses_api::save_forward_address($shipment_id_alt, $data);
                    }

                    // commit transaction
                    if(ci()->customer_m->db->trans_status() == FALSE){
                        ci()->customer_m->db->trans_rollback();
                    }else{
                        ci()->customer_m->db->trans_commit();
                    }
                endif;

                $this->customer_m->update_by_many(array(
                    "customer_id" => $customer_id
                ), array(
                    "shipping_address_completed" => APConstants::ON_FLAG
                ));

                $this->success_output(language('address_controller_address_SaveForwardingAddressSuccessfully'), array('envelope_id' => $envelope_id));
                exit;

            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        $this->template->build('index');
    }

    public function deleteAlternativeAddress(){

        if ($this->is_ajax_request()) {

            ci()->load->library('scans/scans_api');
            ci()->load->library('addresses/addresses_api');

            $customer_id = APContext::getCustomerCodeLoggedIn();
            $shipping_address_id = (int) $this->input->get_post('shipping_address_id');
            $allowDeleteAddress = scans_api::checkShppingAdress($customer_id, $shipping_address_id);

            if(!$allowDeleteAddress){
                $this->error_output(lang('shpping_address_is_using'));
                return;
            }

            $resultDelete = addresses_api::deleteAlternativeAddress($shipping_address_id);
            if($resultDelete){
                $this->success_output(lang('delete_alternative_address_success'));
                return;
            }
            else{
                $this->error_output(lang('delete_alternative_address_error'));
                return;
            }
        }
    }

    /*
     * Save new forward address
     */
    public function save_new_forward_address()
    {

        ci()->load->library('scans/scans_api');
        $customer_id= $this->input->get_post('customer_id');
        if(empty($customer_id)){
            $customer_id = APContext::getCustomerCodeLoggedIn();
        }

        if ($_POST) {
            $this->form_validation->set_rules($this->validation_rules_02);

            if ($this->form_validation->run()) {

                //extract($_POST);
                $envelope_id           = $this->input->post('envelope_id');
                $active_flag           = $this->input->post('active_flag');

                $shipment_address_name      = $this->input->post('shipment_address_name');
                $shipment_company           = $this->input->post('shipment_company');
                $shipment_street            = $this->input->post('shipment_street');
                $shipment_postcode          = $this->input->post('shipment_postcode');
                $shipment_city              = $this->input->post('shipment_city');
                $shipment_region            = $this->input->post('shipment_region');
                $shipment_country           = $this->input->post('shipment_country');
                $shipment_phone_number      = $this->input->post('shipment_phone_number');

                $data = array(
                    'customer_id' => $customer_id,
                    'shipment_address_name' => $shipment_address_name,
                    'shipment_company' => $shipment_company,
                    'shipment_street' => $shipment_street,
                    'shipment_postcode' => $shipment_postcode,
                    'shipment_city' => $shipment_city,
                    'shipment_region' => $shipment_region,
                    'shipment_country' => $shipment_country,
                    'shipment_phone_number' => $shipment_phone_number,
                    'update_date' => date("Y-m-d H:i:s"),
                    'created_date'       => now()
                );
//                if(!empty($active_flag)){
//                    $data['active_flag'] = 1;
//                } else {
//                    $data['active_flag'] = 0;
//                }

                $shipping_address_id = $this->customers_forward_address_m->insert($data);
                scans_api::saveShippingAddress($shipping_address_id, $envelope_id, $customer_id);

                $this->success_output(language('address_controller_address_SaveForwardingAddressSuccessfully'), array('envelope_id' => $envelope_id, 'shipping_address_id' => $shipping_address_id));
                exit;

            } else {

                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;

            }
        }

        return;

    }

    /**
     * save shipment and invoice address.
     */
    public function save_postbox_address()
    {
        $this->template->set_layout(FALSE);
        // Gets customerid logged in.
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $customer    = APContext::getCustomerLoggedIn();

        ci()->load->library('mailbox/mailbox_api');
        ci()->load->library('customers/customers_api');
        ci()->load->library('addresses/addresses_api');

        $check = addresses_api::getCustomerAddress($customer_id);
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $data = array();
            foreach ($_POST as $key => $value) {
                $this->check_data_input($data, 'postbox_id', $key, $value);
                //$this->check_data_input($data, 'postbox_name', $key, $value);
                $this->check_data_input($data, 'type', $key, $value);
                $this->check_data_input($data, 'name', $key, $value);
                $this->check_data_input($data, 'company', $key, $value);
            }

            // For each postbox item
            foreach ($data as $value) {
                // Validate data
                //$check_result = APUtils::required($value['postbox_name']);
                //if (!$check_result['status']) {
                //    return $this->error_output(sprintf($check_result['message'], 'Postbox name'));
                //}
                if(strtolower($value['name']) == strtolower($value['company'])){
                    $this->error_output(lang('error_company_same_name'),array("error_status"=>0));
                    return;
                }
            }
            // added #668: can not change bussiness postbox type if additional location.
            $primary_location = APUtils::getPrimaryLocationBy($customer_id);
            foreach ($data as $value) {

                $postboxID = $value['postbox_id'];
                $new_postbox_type = $value['type'];
                $old_postbox = mailbox_api::getPostBoxByID($postboxID);
                $old_postbox_type = $old_postbox->type;
                if ($old_postbox_type != $new_postbox_type && $old_postbox->location_available_id != $primary_location) {
                    $this->error_output(lang('can_not_change_if_is_additional_location'));
                    return;
                }
            }

            $display_message = '';
            $i = 0;

            // For each postbox item
            foreach ($data as $value) {
                $postboxID = $value['postbox_id'];
                $new_postbox_type = $value['type'];
                $i++;
                $message = '';
                $old_postbox = mailbox_api::getPostBoxByID($postboxID);
                $old_postbox_type = $old_postbox->type;

                if ($old_postbox_type != $new_postbox_type) {
                    // #1012 Add pre-payment process
                    if ($new_postbox_type == APConstants::BUSINESS_TYPE) {
                        $location_id = $old_postbox->location_available_id;
                        $check_prepayment_data = CustomerUtils::checkApplyAddPostboxPrepayment($new_postbox_type, $location_id, $customer_id);
                        if ($check_prepayment_data['prepayment'] == true) {
                            $check_prepayment_data['status'] = FALSE;
                            $check_prepayment_data['new_postbox_type'] = $new_postbox_type;
                            $check_prepayment_data['postbox_id'] = $postboxID;
                            echo json_encode($check_prepayment_data);
                            return;
                        }
                    }

                    if ($old_postbox_type == APConstants::FREE_TYPE) {
                        mailbox_api::updatePostbox($postboxID, $new_postbox_type);

                        /*
                         * #1180 create postbox history page like check item page
                         *  Upgrade from AS YOU GO to private or bussiness
                         */
                        // CustomerUtils::actionPostboxHistoryActivity($postboxID, APConstants::POSTBOX_UPGRADE, now(), $new_postbox_type, APConstants::INSERT_POSTBOX);
                        customers_api::addPostboxHistory($postboxID, $APConstants::POSTBOX_UPGRADE, $new_postbox_type, $old_postbox_type);

                        //$conditionNamesCustomer  = array("customer_id");
                        //$conditionValuesCustomer = array($old_postbox->customer_id);
                        //$dataNamesCustomer       = array("account_type","new_account_type","plan_date_change_account_type");
                        //$dataValuesCustomer      = array($new_postbox_type,$new_account_type = null,$plan_date_change_account_type = null);
                        //customers_api::updateCustomer($conditionNamesCustomer, $conditionValuesCustomer, $dataNamesCustomer, $dataValuesCustomer);

                        $message = lang('change_my_postbox_type_success');
                        $message = sprintf($message, $i);

                        // revert envelopes of current month
                        APUtils::revert_all_envelopes($customer_id, $postboxID, APUtils::getCurrentYear(), APUtils::getCurrentMonth());
                    } else {
                        $change_date = APUtils::getFirstDayOfNextMonth();
                        $conditionNamesPostbox  = array("postbox_id");
                        $conditionValuesPostbox = array($postboxID);
                        $dataNamesPostbox       = array("new_postbox_type","plan_date_change_postbox_type");
                        $dataValuesPostbox      = array($new_postbox_type,$change_date);
                        mailbox_api::updateManyPostbox($conditionNamesPostbox, $conditionValuesPostbox, $dataNamesPostbox, $dataValuesPostbox);

                         /*
                        * #1180 create postbox history page like check item page
                        *  Activity: Upgrade ordered, downgrade ordered, upgraded, downgraded
                        */
                        if ($old_postbox_type === APConstants::PRIVATE_TYPE && $new_postbox_type === APConstants::BUSINESS_TYPE) {
                            // Upgrade ordered from  private to bussiness
                            // CustomerUtils::actionPostboxHistoryActivity($postbox_id, APConstants::POSTBOX_UPGRADE_ORDER, now(), $new_account_type, APConstants::INSERT_POSTBOX);
                            customers_api::addPostboxHistory($postboxID, APConstants::POSTBOX_UPGRADE_ORDER, $new_postbox_type, $old_postbox_type);
                        } else {
                            customers_api::addPostboxHistory($postboxID, APConstants::POSTBOX_DOWNGRADE_ORDER, $new_postbox_type, $old_postbox_type);
                        }

                        // if ($old_postbox_type === APConstants::PRIVATE_TYPE && $new_postbox_type === APConstants::BUSINESS_TYPE) {
                        //     // Upgrade ordered from  private to bussiness
                        //      CustomerUtils::actionPostboxHistoryActivity($postboxID, APConstants::POSTBOX_UPGRADE_ORDER, now(), $new_postbox_type, APConstants::INSERT_POSTBOX);

                        // }else if ($old_postbox_type === APConstants::BUSINESS_TYPE && $new_postbox_type != APConstants::BUSINESS_TYPE) {
                        //     //  downgrade ordered from  AS YOU GO, private to bussiness
                        //     CustomerUtils::actionPostboxHistoryActivity($postboxID, APConstants::POSTBOX_DOWNGRADE_ORDER, now(), $new_postbox_type, APConstants::INSERT_POSTBOX);

                        // }else if($old_postbox_type === APConstants::PRIVATE_TYPE && $new_postbox_type === APConstants::FREE_TYPE) {
                        //     //  downgrade ordered from  private to AS YOU GO
                        //     CustomerUtils::actionPostboxHistoryActivity($postboxID, APConstants::POSTBOX_DOWNGRADE_ORDER, now(), $new_postbox_type, APConstants::INSERT_POSTBOX);
                        // }

                        // Change account type if current selected postbox is
                        // main postbox
                        //if ($old_postbox->is_main_postbox === '1') {
                        //    $conditionNamesCustomer  = array("customer_id");
                        //    $conditionValuesCustomer = array($old_postbox->customer_id);
                        //    $dataNamesCustomer       = array("new_account_type","plan_date_change_account_type");
                        //    $dataValuesCustomer      = array($new_postbox_type,$change_date);
                        //    customers_api::updateCustomer($conditionNamesCustomer, $conditionValuesCustomer, $dataNamesCustomer, $dataValuesCustomer);
                        //}

                        $message = lang('change_postbox_info_message');
                        $new_account_name = lang('postbox_type_' . $new_postbox_type);
                        $message = sprintf($message, $i, $new_account_name, APUtils::displayDate($change_date));
                    }
                }

                // Update account type of current customer
                if ($old_postbox->is_main_postbox === '1') {
                    $conditionKey = array("customer_id");
                    $conditionVal = array($old_postbox->customer_id);
                    $dataKey = array("postbox_name_flag","name_comp_address_flag","city_address_flag");
                    $dataVal = array(APConstants::ON_FLAG, APConstants::ON_FLAG, APConstants::ON_FLAG);
                    // update: convert registration process flag to customer_product_setting.
                    //customers_api::updateCustomer($conditionKey,$conditionVal,$dataKey,$dataVal);
                    CustomerProductSetting::set_many($customer_id, APConstants::CLEVVERMAIL_PRODUCT, $dataKey, $dataVal);
                }


                unset($value['postbox_id']);
                unset($value['type']);

                mailbox_api::updatePostboxByID($postboxID, $value);

                if ($value['name'] != $old_postbox->name || $value['company'] != $old_postbox->company) {
                    CaseUtils::start_case_verification_by_postbox(false, $customer, $old_postbox);
                }

                if (!empty($message)) {
                    $display_message = $display_message . $message . '</br>';
                }
            } // End for postbox

            $isEnterpriseCustomer = APContext::isEnterpriseCustomer();
            if (!$isEnterpriseCustomer) {
                // Update box new postbox type
                APUtils::updateOnlyAccountType($customer_id);

                // #615 Calculate postbox fee after insert new postbox
                Events::trigger('cal_postbox_invoices_directly', array(
                    'customer_id' => $customer_id
                ), 'string');
            }
            // trigger restart case.
            CaseUtils::start_verification_case($customer_id);

            $message = lang('change_postbox_address_success');
            $display_message = $display_message . $message . '</br>';
            $this->success_output($display_message);
            return;
        }

        $this->template->build('index');
    }

    /**
     * Show mailing address.
     */
    public function show_mailing_address()
    {
        $this->template->set_layout(FALSE);
        ci()->load->library('account/account_api');
        // Gets customerid logged in.
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $postbox_id = $this->input->get_post('postbox_id');

        // Gets customer address infor.
        if (APContext::isPrimaryCustomerUser()) {
            $postbox =  account_api::get_postbox_by_postbox_id($customer_id, $postbox_id);
        } else {
            $postbox = $this->postbox_m->get_by_many(array(
                'customer_id' => $customer_id,
                "postbox_id" => $postbox_id
            ));
        }

        $location = $this->location_m->getLocationInfo($postbox->location_available_id);

        $this->template->set('location', $location);
        $this->template->set('postbox', $postbox)->build('show_mailing_address');
    }

    private function check_data_input(&$data, $needle, $key, $value)
    {
        $value = trim($value);
        $pos = strpos($key, $needle);
        if (is_int($pos)) {
            $num = substr($key, $pos + strlen($needle));
            $subarr[$needle] = $value;

            if (isset($data) && array_key_exists($num, $data)) {
                $temp[$num] = $subarr;
                $old[$num] = $data[$num];
                $data[$num] = array_merge($old[$num], $temp[$num]);
            } else {
                $data[$num] = $subarr;
            }
        }
    }

    /**
     * Load all address information.
     */
    private function load_address()
    {
        // Gets customerid logged in.
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $address = $this->customers_address_m->get_by('customer_id', $customer_id);
        $this->template->set("address", $address);
    }

    /**
     * Get all postbox of account
     */
    private function load_postbox()
    {
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $list_customer_id = array();
        $isPrimaryCustomer = APContext::isPrimaryCustomerUser();
        $parent_customer = APContext::getParentCustomerLoggedIn();
        if ($isPrimaryCustomer) {
            $list_customer_id = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($customer_id);
            $list_customer_id[] = $customer_id;
        } else {
            $list_customer_id[] = $customer_id;
        }

        // Get all postbox of this customer
        $postbox = $this->postbox_m->get_many_by_many(array(
            'customer_id IN (' . implode(',', $list_customer_id) . ')' => null,
            "deleted <> " => '1',
            "(postbox_name IS NOT NULL AND postbox_name !='')" => null
        ));

        $map_postbox_username = array();
        if ($isPrimaryCustomer) {
            $list_map_postbox = $this->postbox_m->get_all_postboxes_by_enterprise_customer($customer_id);
            foreach ($list_map_postbox as $map_postbox) {
                if (!array_key_exists($map_postbox->postbox_id, $map_postbox_username)) {
                    $map_postbox_username[$map_postbox->postbox_id] = $map_postbox->user_name;
                }
            }
            
            /// Gets postbox of parent customer
            $postboxes = $this->postbox_m->get_many_by_many(array(
                "customer_id" => $customer_id
            ));
            foreach($postboxes as $p){
                $map_postbox_username[$p->postbox_id] = $parent_customer->user_name;
            }
        }

        $this->template->set("postbox", $postbox);
        $this->template->set("map_postbox_username", $map_postbox_username);
    }

    /**
     * Get location
     */
    private function load_location()
    {
        ci()->load->library('addresses/addresses_api');
        // $locate = $this->location_m->get_location_paging(array('location.public_flag' => 1), 0, 100000, '');
        $locate = addresses_api::getLocationPublic();
        $this->template->set('locate', $locate);
    }


    /**
     * Default page for 404 error.
     */
    public function thanking_page()
    {
        $this->template->set_layout(FALSE);
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $customer_address = $this->customers_address_m->get_by('customer_id', $customer_id);

        // Get main postbox address
        $customer_postbox = $this->postbox_m->get_by_many(array(
            'customer_id' => $customer_id,
            'is_main_postbox' => APConstants::ON_FLAG
        ));

        // Get post box city name
        if (!empty($customer_postbox)) {
            $location = $this->location_m->get_by('id', $customer_postbox->location_available_id);
            $country = new stdClass();
            $country->country_name = '';
            if (!empty($location)) {
                $country = $this->countries_m->get_by('id', $location->country_id);
            }
            $this->template->set('country', $country);
            $this->template->set('location', $location);
        }

        // load the theme_example view
        $this->template->set('customer_postbox', $customer_postbox);
        $this->template->set('customer_address', $customer_address)->build('thanking_page');
    }

    /**
     * Default page for 404 error.
     */
    public function thanking_page_deposit_invoice()
    {
        $this->template->set_layout(FALSE);
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $customer_address = $this->customers_address_m->get_by('customer_id', $customer_id);

        // Get main postbox address
        $customer_postbox = $this->postbox_m->get_by_many(array(
            'customer_id' => $customer_id,
            'is_main_postbox' => APConstants::ON_FLAG
        ));

        // Get post box city name
        if (!empty($customer_postbox)) {
            $location = $this->location_m->get_by('id', $customer_postbox->location_available_id);
            $country = new stdClass();
            $country->country_name = '';
            if (!empty($location)) {
                $country = $this->countries_m->get_by('id', $location->country_id);
            }
            $this->template->set('country', $country);
            $this->template->set('location', $location);
        }

        // load the theme_example view
        $this->template->set('customer_postbox', $customer_postbox);
        $this->template->set('customer_address', $customer_address)->build('thanking_page_deposit_invoice');
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
}