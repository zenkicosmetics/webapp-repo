<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Number extends AccountSetting_Controller {

    private $number_validation_rules = array(
        array(
            'field' => 'country_code',
            'label' => 'lang:users.label.country_code',
            'rules' => 'required|max_length[3]'
        ),
        array(
            'field' => 'area_code',
            'label' => 'lang:users.label.area_code',
            'rules' => 'required|max_length[3]'
        ),
        array(
            'field' => 'location_id',
            'label' => 'lang:users.label.location_id',
            'rules' => 'trim'
        ),
        array(
            'field' => 'phone_number',
            'label' => 'lang:users.label.phone_number',
            'rules' => 'required|max_length[255]|callback__check_phonnumber'
        )
        ,
        array(
            'field' => 'auto_renewal',
            'label' => 'lang:users.label.auto_renewal',
            'rules' => ''
        )
    );

    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     *
     * @todo Document properly please.
     */
    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');

        // load the theme_example view
        $this->load->model('customers/phone_customer_user_m');
        $this->load->model('phones/phone_customer_subaccount_m');
        $this->load->model('phones/phone_area_code_m');
        $this->load->model('phones/phone_number_m');
        $this->load->model('phones/phone_target_m');
        $this->load->model('phones/phone_voiceapp_m');
        $this->load->model('settings/countries_m');
        $this->load->model('mailbox/postbox_m');

        $this->load->library('customers/customers_api');
        $this->load->library('addresses/addresses_api');

        $this->lang->load('account');
        $this->lang->load('user');
        $this->lang->load('phones/phones');

        $this->load->library('phones/sonetel');
        $this->load->library('phones/phones_api');
        $this->load->library('account/account_api');
    }

    /**
     * Index Page for this controller. Maps to the following URL http://example.com/index.php/welcome - or -
     * http://example.com/index.php/welcome/index - or - Since this controller is set as the default controller in config/routes.php, it's displayed
     * at http://example.com/ So any other public methods not prefixed with an underscore will map to /index.php/welcome/<method_name>
     *
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index() {
        $this->template->build('number/index');
    }

    public function search() {
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $array_condition = array();
        $array_condition ["phone_number.parent_customer_id"] = $parent_customer_id;
        if (!APContext::isAdminCustomerUser()) {
            $array_condition ["phone_number.customer_id"] = $customer_id;
        }
        //$array_condition ["deleted_flag"] = APConstants::OFF_FLAG;
        // If current request is ajax
        if ($this->is_ajax_request()) {
            // update limit into user_paging.
            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APConstants::NUMBER_RECORD_PER_PAGE_CODE;

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging ['limit'] = $limit;

            // Call search method
            $query_result = $this->phone_number_m->get_number_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

            // Process output data
            $total = $query_result['total'];
            $rows = $query_result['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);

            $i = 0;
            foreach ($rows as $row) {
                
                $endpoint = '';
                if (!empty($row->target_type)) {
                    $endpoint = lang('target_type_'.$row->target_type). ':'. $row->target_id;
                }
                
                $response->rows [$i] ['id'] = $row->id;
                $response->rows [$i] ['cell'] = array(
                    $row->id,
                    $row->phone_number,
                    $row->country_name,
                    $row->area_name,
                    $endpoint,
                    lang('users.label.auto_renewal_'.$row->auto_renewal),
                    APUtils::convert_timestamp_to_date($row->created_date),
                    APUtils::convert_timestamp_to_date($row->end_contract_date),
                    $row->id,
                );
                $i++;
            }
            echo json_encode($response);
        }
    }
    
    /**
     * Get list available phone
     * @return type
     */
    public function list_avail_phone() {
        //$array_condition ["deleted_flag"] = APConstants::OFF_FLAG;
        // If current request is ajax
        $this->load->model('phones/pricing_phones_number_m');
        $this->load->model('phones/pricing_phones_number_customer_m');
        if ($this->is_ajax_request()) {
            $country_code = $this->input->get_post('country_code');
            $area_code = $this->input->get_post('area_code');
            
            // update limit into user_paging.
            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging ['limit'] = $limit;

            // Call search method
            $list_result = $this->sonetel->get_list_available_phonenumber($country_code, $area_code);
            
            if (empty($list_result) || $list_result->status != 'success') {
                // Get output response
                $response = $this->get_paging_output(0, $input_paging ['limit'], $input_paging ['page']);
                echo json_encode($response);
                return;
            }
            $list_response = $list_result->response;
            if ($list_response == 'No entries found') {
                // Get output response
                $response = $this->get_paging_output(0, $input_paging ['limit'], $input_paging ['page']);
                echo json_encode($response);
                return;
            }
            $total = count($list_response);
            
            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
            
            // Get clevvermail upcharge value
            $one_time_fee_cl_upcharge = 0;
            $recurring_fee_cl_upchage = 0;
            $per_min_fee_cl_upcharge = 0;
            $price_phone_number = $this->pricing_phones_number_m->get_by('country_code_3', $country_code);
            if (!empty($price_phone_number)) {
                $one_time_fee_cl_upcharge = $price_phone_number->one_time_fee_upcharge;
                $recurring_fee_cl_upchage = $price_phone_number->recurring_fee_upcharge;
                $per_min_fee_cl_upcharge = $price_phone_number->per_min_fee_upcharge;
            }
            
            // Get enterprise upchage value
            $one_time_fee_enterprise_upcharge = 0;
            $recurring_fee_enterprise_upchage = 0;
            $per_min_fee_enterprise_upcharge = 0;
            $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
            if (APContext::isEnterpriseCustomer()) {
                $price_phone_number_enterprise = $this->pricing_phones_number_customer_m->get_by_many(array(
                    'country_code_3' => $country_code,
                    'customer_id' => $parent_customer_id
                ));
                if (!empty($price_phone_number_enterprise)) {
                    $one_time_fee_enterprise_upcharge = $price_phone_number_enterprise->one_time_fee_upcharge;
                    $recurring_fee_enterprise_upchage = $price_phone_number_enterprise->recurring_fee_upcharge;
                    $per_min_fee_enterprise_upcharge = $price_phone_number_enterprise->per_min_fee_upcharge;
                }
            }

            $start = $input_paging ['limit'] * ($input_paging ['page'] - 1);
            $end = $input_paging ['limit'] * ($input_paging ['page']);
            $i = 0;
            $j = 0;
            foreach ($list_response as $row) {
                // Add upcharge value
                $one_time_fee = $row->one_time_fee + $one_time_fee_cl_upcharge + $one_time_fee_enterprise_upcharge;
                $recurring_fee = ($row->recurring_fee * (1  + $recurring_fee_cl_upchage / 100)) * (1 + $recurring_fee_enterprise_upchage / 100);
                $per_min_fee = $row->per_min_fee + $per_min_fee_cl_upcharge + $per_min_fee_enterprise_upcharge;
                $recurrence_interval = '12 Months';
                if ($i >= $start && $i < $end) {
                    $response->rows [$j] ['id'] = $row->phnum;
                    $response->rows [$j] ['cell'] = array(
                        $row->phnum,
                        $row->phnum,
                        $row->type,
                        $row->city,
                        APUtils::number_format($one_time_fee) . ' EUR',
                        APUtils::number_format($recurring_fee). ' EUR',
                        APUtils::number_format($per_min_fee). ' EUR',
                        // lang('recurrence_interval_'.$row->recurrence_interval),
                        $recurrence_interval,
                        $row->range
                    );
                    $j++;
                }
                $i++;
            }
            echo json_encode($response);
        }
    }

    public function add() {
        $this->load->library('addresses/addresses_api');
        $this->load->model('phones/pricing_phones_number_m');
        $this->load->model('phones/pricing_phones_number_customer_m');
        
        $this->template->set_layout(FALSE);
        $number = new stdClass();
        $number->id = '';

        if ($this->input->post()) {
            $this->form_validation->set_rules($this->number_validation_rules);
            if ($this->form_validation->run()) {
                $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
                $customer_id = APContext::getCustomerCodeLoggedIn();
                $phone_number = $this->input->post('phone_number');
                $country_code = $this->input->post('country_code');
                $area_code = $this->input->post('area_code');
                $location_id = $this->input->post('location_id');
                $auto_renewal = $this->input->post('auto_renewal');
                $account_id = APContext::getSubAccountId($parent_customer_id);
                // Check exist sub account of this customer
                if (empty($account_id)) {
                    $account_id = account_api::create_sub_account($parent_customer_id);
                    
                    // Check empty
                    if (empty($account_id)) {
                        $message = lang('users.message.add_phone_number_fail_01');
                        $this->error_output($message);
                        return;
                    }
                }
                
                // Create new user
                try {
                    $response = $this->sonetel->create_new_phone_number($account_id, $phone_number);
                    // Validate response
                    if ($response->status == 'failed') {
                        $message = $response->response->code .':'.$response->response->detail;
                        $this->error_output($message);
                        return;
                    }
                } catch (Exception $e) {
                    $message = lang('users.message.add_phone_number_fail_02');
                    $this->error_output($message);
                    return;
                }
                $response_obj = $response->response;
                $base_one_time_fee = $response_obj->one_time_fee;
                $base_recurring_fee = $response_obj->recurring_fee;
                $recurrence_interval  = $response_obj->recurrence_interval;
                $number_interval_one_year = phones_api::getNumberIntervalOneYearContract($recurrence_interval);
                
                // Get clevvermail upcharge value
                $one_time_fee_cl_upcharge = 0;
                $recurring_fee_cl_upchage = 0;
                $price_phone_number = $this->pricing_phones_number_m->get_by('country_code_3', $country_code);
                if (!empty($price_phone_number)) {
                    $one_time_fee_cl_upcharge = $price_phone_number->one_time_fee_upcharge;
                    $recurring_fee_cl_upchage = $price_phone_number->recurring_fee_upcharge;
                }

                // Get enterprise upchage value
                $one_time_fee_enterprise_upcharge = 0;
                $recurring_fee_enterprise_upchage = 0;
                $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
                if (APContext::isEnterpriseCustomer()) {
                    $price_phone_number_enterprise = $this->pricing_phones_number_customer_m->get_by_many(array(
                        'country_code_3' => $country_code,
                        'customer_id' => $parent_customer_id
                    ));
                    if (!empty($price_phone_number_enterprise)) {
                        $one_time_fee_enterprise_upcharge = $price_phone_number_enterprise->one_time_fee_upcharge;
                        $recurring_fee_enterprise_upchage = $price_phone_number_enterprise->recurring_fee_upcharge;
                    }
                }
                
                $one_time_fee = $base_one_time_fee + $one_time_fee_cl_upcharge + $one_time_fee_enterprise_upcharge;
                $recurring_fee = ($base_recurring_fee * (1  + $recurring_fee_cl_upchage / 100)) * (1 + $recurring_fee_enterprise_upchage / 100);
                $total_recurring_fee = $recurring_fee * $number_interval_one_year;
                
                // Add invoice detail activity
                $activity = APConstants::SUBCRIBE_PHONE_NUMBER_AT;
                $activity_type= APConstants::SUBCRIBE_PHONE_NUMBER_ACTIVITY_TYPE;
                $activity_date = APUtils::getCurrentYearInvoice() . APUtils::getCurrentMonthInvoice() . APUtils::getCurrentDayInvoice();
                phones_api::insertActivityPhoneInvoiceDetail($parent_customer_id, $customer_id, $activity, $activity_type, 
                        1, $one_time_fee, $activity_date, true, $phone_number, '');
                
                $created_date = now();
                $end_contract_date = strtotime('+1 year', $created_date);
                
                // Build reference (using to check the charge fee in this period)
                $reference = date("Ymd", $end_contract_date);
                phones_api::insertActivityPhoneInvoiceDetail($parent_customer_id, $customer_id, APConstants::RECURRING_PHONE_NUMBER_AT,
                APConstants::RECURRING_PHONE_NUMBER_ACTIVITY_TYPE, 1, $total_recurring_fee, $activity_date, true, $phone_number, $reference);
                
                $phone_code = phones_api::getNextPhoneCode($parent_customer_id);
                $data = array(
                    "parent_customer_id" => $parent_customer_id,
                    "customer_id" => null,
                    "phone_number" => $phone_number,
                    "phone_code" => $phone_code,
                    "country_code" => $country_code,
                    "area_code" => $area_code,
                    "location_id" => $location_id,
                    "auto_renewal" => $auto_renewal,
                    "status" => APConstants::OFF_FLAG,
                    "created_date" => $created_date,
                    "end_contract_date" => $end_contract_date
                );
                if (!APContext::isAdminCustomerUser()) {
                    $data['customer_id'] = $customer_id;
                }
                
                // save customer user.
                $this->phone_number_m->insert($data);
                
                // Assign this phone number for them self
                if (!APContext::isAdminCustomerUser()) {
                    $this->load->library('account/account_api');
                    $customer = CustomerUtils::getCustomerByID($customer_id);
                    $new_user_name = $customer->user_name;
                    if ($new_user_name == $customer->email) {
                        $new_user_name = APUtils::generateRandom(30);
                    }
                    $password = APUtils::generatePassword();
                    account_api::add_phone_user($parent_customer_id, $customer_id, $new_user_name, $customer->email, $password['raw_pass']);
                    
                    account_api::assign_phonenumber_byuser($parent_customer_id, $customer_id, $phone_number);
                }

                $message = lang('users.message.add_phone_number_success');
                $this->success_output($message);
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
            }
            return;
        }

        // Loop through each validation rule
        foreach ($this->number_validation_rules as $rule) {
            $number->{$rule['field']} = set_value($rule['field']);
        }
        $number->auto_renewal = '1';
        // Get list country
        $list_country = phones_api::get_all_countries();
        $this->template->set("list_country", $list_country);

        // Default get area of USA (country_id = 430)
        $list_area = $this->phone_area_code_m->get_many_by_many(array(
            'country_id' => 430
        ));
        $this->template->set("list_area", $list_area);
        
        $locations = addresses_api::getLocationPublic();
        $this->template->set("locations", $locations);
        
        // Get all country code 3 of these location
        $map_location_country = array();
        foreach ($locations as $location) {
            $country_id = $location->country_id;
            $location_id = $location->id;
            $map_location_country[$location_id] = '';
            foreach ($list_country as $country) {
                if ($country_id == $country->id) {
                    $map_location_country[$location_id] = $country->country_code_3;
                    break;
                }
            }
        }
        $this->template->set("map_location_country", $map_location_country);
        $this->template->set("number", $number);
        $this->template->set("action_type", "add");
        $this->template->build("number/form");
    }
    
    public function edit() {
        $this->template->set_layout(FALSE);
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $id = $this->input->get_post('id');
        $number = $this->phone_number_m->get_by_many(array(
            'id' => $id,
            "parent_customer_id" => $parent_customer_id
        ));
        
        if ($this->input->post()) {
            $auto_renewal = $this->input->post('auto_renewal');
            
            $this->phone_number_m->update_by_many(array(
                'id' => $id,
                "parent_customer_id" => $parent_customer_id
            ), array(
                'auto_renewal' => $auto_renewal
            ));

            $message = lang('users.message.edit_phone_number_success');
            $this->success_output($message);
            return;
        }
        
        $this->template->set("number", $number);
        $this->template->set("action_type", "edit");
        $this->template->build("number/form_edit");
        
    }
    
    /** 
     * Delete phone number
     * @id : id of phone number
     */
    public function deactivate_handling_rule($id = '') {
        $this->template->set_layout(FALSE);
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $phone_number = $this->phone_number_m->get_by_many(array(
            'parent_customer_id' => $parent_customer_id,
            'id' => $id
        ));
        if (empty($phone_number)) {
            $message = lang('users.message.delete_phone_number_fail_01');
            $this->error_output($message);
            return;
        }
        // Check exist sub account of this customer
        $phone_sub_account = $this->phone_customer_subaccount_m->get_by('customer_id', $customer_id);
        if (empty($phone_sub_account)) {
            $message = lang('users.message.delete_phone_number_fail_02');
            $this->error_output($message);
            return;
        }
        
        $target_id = $phone_number->target_id;
        // Change status
        $this->phone_number_m->update_by_many(array(
            'parent_customer_id' => $parent_customer_id,
            'id' => $id
        ), array(
            "status" => APConstants::OFF_FLAG,
            "target_id" => ''
        ));
        
        // Update sonetel to disable the connecting to this number
        $input = array(
            "connect_to_type" => 'nowhere',
            "connect_to" => 'nowhere'
        );
        $account_id = APContext::getSubAccountId($parent_customer_id);
        $this->sonetel->update_phone_number($account_id, $phone_number->phone_number, $input);
        
        // If target is not empty
        if (!empty($target_id)) {   
            // Change use_flag in phones_target
            $this->phone_target_m->update_by_many(array(
                "id" => $target_id,
                "parent_customer_id" => $parent_customer_id
            ), array(
                "use_flag" => APConstants::OFF_FLAG
            ));
        }
            
        $this->success_output("");
    }

    /** 
     * Delete phone number
     * @id : id of phone number
     */
    public function delete($id = '') {
        $this->template->set_layout(FALSE);
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $phone_number = $this->phone_number_m->get_by_many(array(
            'parent_customer_id' => $parent_customer_id,
            'id' => $id
        ));
        if (empty($phone_number)) {
            $message = lang('users.message.delete_phone_number_fail_01');
            $this->error_output($message);
            return;
        }
        // Check exist sub account of this customer
        $phone_sub_account = $this->phone_customer_subaccount_m->get_by('customer_id', $customer_id);
        if (empty($phone_sub_account)) {
            $message = lang('users.message.delete_phone_number_fail_02');
            $this->error_output($message);
            return;
        }
        
        $end_contract_date = $phone_number->end_contract_date;
        if (empty($end_contract_date)) {
            $end_contract_date = strtotime('+1 year', $phone_number->created_date);
        }
        $this->phone_number_m->update_by_many(array(
            'parent_customer_id' => $parent_customer_id,
            'id' => $id
        ), array(
            'end_contract_date' => $end_contract_date,
            'plan_delete_date' => $end_contract_date
        ));
        $this->success_output("");
    }
    
    /** 
     * Delete phone number (Don't use now). Should use this method when do the physical delete
     */
    private function internal_delete($id = '') {
        $this->template->set_layout(FALSE);
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $phone_number = $this->phone_number_m->get_by_many(array(
            'parent_customer_id' => $parent_customer_id,
            'id' => $id
        ));
        if (empty($phone_number)) {
            $message = lang('users.message.delete_phone_number_fail_01');
            $this->error_output($message);
            return;
        }
        // Check exist sub account of this customer
        $phone_sub_account = $this->phone_customer_subaccount_m->get_by('customer_id', $customer_id);
        if (empty($phone_sub_account)) {
            $message = lang('users.message.delete_phone_number_fail_02');
            $this->error_output($message);
            return;
        }
        $account_id = $phone_sub_account->account_id;
        $phone_number = $phone_number->phone_number;
        // Delete
        try {
            $this->sonetel->delete_phone_number($account_id, $phone_number);
        } catch (Exception $e) {
            $message = lang('users.message.delete_phone_number_fail_03');
            $this->error_output($message);
            return;
        }
        $this->phone_number_m->delete_by_many(array(
            'parent_customer_id' => $parent_customer_id,
            'id' => $id
        ));
        $this->success_output("");
    }

    public function _check_phonnumber($phone_number) {
        // Get user information by email
        $phone_number = $this->phone_number_m->get_by_many(array(
            "phone_number" => $phone_number
        ));

        if ($phone_number) {
            $this->form_validation->set_message('_check_phonnumber', lang('users.message.phone_number_exist'));
            return false;
        }

        return true;
    }
    
    /**
     * Select your postbox location
     */
    public function select_your_postbox_location() {
        $this->template->set_layout(FALSE);
        $this->load->library('addresses/addresses_api');
        // $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $customer_id = APContext::getCustomerCodeLoggedIn();
        $selected_location_id = $this->input->get_post('selected_location_id');
        $this->template->set("selected_location_id", $selected_location_id);
        
        $locations = addresses_api::getMyLocation($customer_id);
        $this->template->set("locations", $locations);
        
        // Gets postbox count
        $postbox_count = $this->postbox_m->count_by_many(array(
            "deleted" => APConstants::OFF_FLAG,
            "(name is not null or company is not null)" => null,
            "customer_id" => $customer_id
        ));
        
        $this->template->set('postbox_count', $postbox_count);
        $this->template->build("number/form_select_location");
    }

    /**
     * Select your postbox location
     */
    public function limication_verification() {
        $this->template->set_layout(FALSE);
        $this->load->model('phones/pricing_phones_number_m');
        $country_code = $this->input->get_post('country_code');
        $this->template->set("country_code", $country_code);
        
        $pricing_phone_number = $this->pricing_phones_number_m->get_by_many(array(
            "country_code_3" => $country_code
        ));
        
        $this->template->set("pricing_phone_number", $pricing_phone_number);
        $this->template->build("number/limication_verification");
    }
    
    /**
     * Change phone number setting
     */
    public  function change_phone_number_setting() {
        $this->template->set_layout(FALSE);
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $customer_id = APContext::getCustomerCodeLoggedIn();
        
        $phone_number = $this->input->get_post('phone_number');
        $account_id = APContext::getSubAccountId($parent_customer_id);
        $phonenumbersubscription = $this->sonetel->get_phonenumbersubscription_byphonenumber($account_id, $phone_number);
        
        // Submit request
        if ($this->input->post()) {
            $connect_to_type = $this->input->post('connect_to_type');
            $connect_to = $this->input->post('connect_to');
            
            // Validate the connect to
            if (empty($connect_to)) {
                $message = lang('users.message.connect_to_is_required');
                $this->error_output($message);
                return;
            }
            
            $internal_connect_to_type = '';
            // Connect to user
            if ($connect_to_type == 'user') {
                $internal_connect_to_type = 'user';
                // Call sonetel to register this phone number to given
                $input = array(
                    "connect_to_type" => 'user',
                    "connect_to" => $connect_to
                );
                $this->sonetel->update_phone_number($account_id, $phone_number, $input);
                
                // Add this user to target table
                account_api::assign_phonenumber_byuser($parent_customer_id, $customer_id, $phone_number);
                
                $check_target = $this->phone_target_m->get_by_many(array(
                    "target_id" => $connect_to,
                    "target_type" => 'user',
                    "parent_customer_id" => $parent_customer_id
                ));
                
                // Add this user to target
                if (empty($check_target)) {
                    $data = array(
                        "target_name" => 'User:'.$connect_to,
                        "target_id" => $connect_to,
                        "target_type" => 'user',
                        "created_date" => now(),
                        "use_flag" => APConstants::ON_FLAG,
                        "parent_customer_id" => $parent_customer_id,
                    );
                    
                    $this->phone_target_m->insert($data);
                } else {
                    // Change use_flag in phones_target
                    $this->phone_target_m->update_by_many(array(
                        "target_id" => $connect_to,
                        "target_type" => 'user',
                        "parent_customer_id" => $parent_customer_id
                    ), array(
                        "use_flag" => APConstants::ON_FLAG,
                        "updated_date" => now()
                    ));
                }
            }
            else if ($connect_to_type == 'phnum') {
                $internal_connect_to_type = 'phone_number';
                // Call sonetel to register this phone number to given
                $input = array(
                    "connect_to_type" => 'phnum',
                    "connect_to" => $connect_to
                );
                $this->sonetel->update_phone_number($account_id, $phone_number, $input);
            }
            else if ($connect_to_type == 'sip') {
                $internal_connect_to_type = 'sip_phone';
                // Call sonetel to register this phone number to given
                $input = array(
                    "connect_to_type" => 'sip',
                    "connect_to" => $connect_to
                );
                $this->sonetel->update_phone_number($account_id, $phone_number, $input);
            }
            else if ($connect_to_type == 'app') {
                
                $app_type = '';
                $voice_app = $this->phone_voiceapp_m->get_by('app_id', $connect_to);
                if (!empty($voice_app)) {
                    // app_id is only relevant in case the app_type is “ivr” or “mailbox” 
                    // and takes the value of a specific voiceapp app_id 
                    $connect_to_obj = array(
                        'app_type' => $voice_app->app_type
                    );
                    if ($voice_app->app_type == 'ivr' || $voice_app->app_type == 'mailbox') {
                        $connect_to_obj['app_id'] = $voice_app->app_id;
                    }
                    // Call sonetel to register this phone number to given
                    $input = array(
                        "connect_to_type" => 'app',
                        "connect_to" => $connect_to_obj
                    );
                    $this->sonetel->update_phone_number($account_id, $phone_number, $input);
                    $internal_connect_to_type = $voice_app->app_type;
                }
            }
            
            $selected_target = $this->phone_target_m->get_by_many(array(
                "target_id" => $connect_to,
                "target_type" => $internal_connect_to_type,
                "parent_customer_id" => $parent_customer_id
            ));
            $target_id = !empty($selected_target) ? $selected_target->id : 0;
            $phone_number_status = $target_id == 0 ? APConstants::OFF_FLAG : APConstants::ON_FLAG;
            // Change related information in phone_number table
            $this->phone_number_m->update_by_many(array(
                "phone_number" => $phone_number,
                "parent_customer_id" => $parent_customer_id
            ), array(
                "status" => $phone_number_status,
                "target_id" => $target_id
            ));
            
            // Change use_flag in phones_target
            $this->phone_target_m->update_by_many(array(
                "target_id" => $connect_to,
                "target_type" => $internal_connect_to_type,
                "parent_customer_id" => $parent_customer_id
            ), array(
                "use_flag" => APConstants::ON_FLAG
            ));
        
            $message = lang('users.message.edit_phone_number_success');
            $this->success_output($message);
            return;
        }
        
        
        $list_connected_to_type = array();
        $obj = new stdClass();
        $obj->key = 'user';
        $obj->label = 'user';
        $list_connected_to_type[] = $obj;
        $obj = new stdClass();
        $obj->key = 'phnum';
        $obj->label = 'Phone Number';
        $list_connected_to_type[] = $obj;
        $obj = new stdClass();
        $obj->key = 'sip';
        $obj->label = 'sip';
        $list_connected_to_type[] = $obj;
        $obj = new stdClass();
        $obj->key = 'app';
        $obj->label = 'app';
        $list_connected_to_type[] = $obj;
        $this->template->set("list_connected_to_type", $list_connected_to_type);
        
        $list_connected_to = array();
        $this->template->set("list_connected_to", $list_connected_to);
        
        $this->template->set("phonenumber", $phonenumbersubscription);
        $this->template->build("number/form_setting");
    }
    
    /**
     * Load list of object to binding with the target of voice app.
     * 
     * menu_digit_action (myphones | sipphone | play_prompt | connect_app | disconnect)
     */
    public function load_phonenumber_setting_to_target() {
        $list_data = array();
        $connect_type_to = $this->input->get_post('connect_type_to');
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $customer_id = APContext::getCustomerCodeLoggedIn();
            
        if ($connect_type_to == 'user') {
            $array_condition = array();
            if (!APContext::isAdminCustomerUser()) {
                $array_condition['customer_id'] = $customer_id;
            } 
            // Load all user under this customer id
            $array_condition ["customers.parent_customer_id"] = $parent_customer_id;
            $list_user_phone = $this->phone_customer_user_m->get_list_phone_user($array_condition);
            $list_data = array();
            foreach ($list_user_phone as $item) {
                $obj = new stdClass();
                $obj->key = $item->phone_user_id;
                $obj->label = $item->email;
                $list_data[] = $obj;
            }
            echo json_encode($list_data);
            return;
            
        } else if ($connect_type_to == 'sip' || $connect_type_to == 'phnum') {
            $array_condition = array(
                'parent_customer_id' => $parent_customer_id,
            );
            if (!APContext::isAdminCustomerUser()) {
                $array_condition['customer_id'] = $customer_id;
            }  
            if ($connect_type_to == 'sip') {
                $array_condition['target_type'] = 'sip_phone';
            } else {
                $array_condition['target_type'] = 'phone_number';
            }
            $list_target = $this->phone_target_m->get_many_by_many($array_condition);
            foreach ($list_target as $item) {
                $obj = new stdClass();
                $obj->key = $item->target_id;
                $obj->label = $item->target_id. ' ('. $item->target_name .')';
                $list_data[] = $obj;
            }
            echo json_encode($list_data);
            return;
        } else if ($connect_type_to == 'app') {
            $array_condition = array();
            if (!APContext::isAdminCustomerUser()) {
                $array_condition['customer_id'] = $customer_id;
            } 
            $array_condition ["phone_voiceapp.parent_customer_id"] = $parent_customer_id;
            $list_voice_app = $this->phone_voiceapp_m->get_many_by_many($array_condition);
            $list_data = array();
            foreach ($list_voice_app as $item) {
                $obj = new stdClass();
                $obj->key = $item->app_id;
                $obj->label = $item->name;
                $list_data[] = $obj;
            }
            echo json_encode($list_data);
            return;
        }
            
        echo json_encode($list_data);
        return;
    }
}
