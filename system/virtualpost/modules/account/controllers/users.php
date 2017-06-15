<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Users extends AccountSetting_Controller {

    private $user_validation_rules = array(
        array(
            'field' => 'user_name',
            'label' => 'lang:users.name',
            'rules' => 'required|min_length[3]|max_length[100]'
        ),
        array(
            'field' => 'email',
            'label' => 'lang:email',
            'rules' => 'required|valid_email|max_length[255]|callback__check_email'
        ),
        array(
            'field' => 'password',
            'label' => 'lang:password',
            'rules' => 'required|trim|matches[repeat_password]|min_length[8]|max_length[255]'
        ),
        array(
            'field' => 'repeat_password',
            'label' => 'lang:repeat_password',
            'rules' => 'required|trim|min_length[8]|max_length[255]'
        ),
        array(
            'field' => 'product_type',
            'label' => 'Product',
            'rules' => 'required'
        ),
        array(
            'field' => 'customer_type',
            'label' => 'customer Type',
            'rules' => 'required'
        ),
        array(
            'field' => 'role_flag',
            'label' => 'lang:users.role_flag',
            'rules' => 'required'
        ),
        array(
            'field' => 'language',
            'label' => 'lang:users.language',
            'rules' => 'trim'
        ),
        array(
            'field' => 'currency_id',
            'label' => 'lang:users.currency_id',
            'rules' => 'trim'
        ),
        array(
            'field' => 'decimal_separator',
            'label' => 'lang:users.decimal_separator',
            'rules' => 'trim'
        ),
        array(
            'field' => 'date_format',
            'label' => 'lang:users.date_format',
            'rules' => 'trim'
        ),
        array(
            'field' => 'vat_rate',
            'label' => 'lang:users.vat_rate',
            'rules' => 'trim'
        ),
    );

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
        )
    );

    private $user_validation_rules02 = array(
        array(
            'field' => 'user_name',
            'label' => 'lang:users.name',
            'rules' => 'required|min_length[3]|max_length[100]'
        ),
        array(
            'field' => 'customer_type',
            'label' => 'customer Type',
            'rules' => 'required'
        ),
        array(
            'field' => 'role_flag',
            'label' => 'lang:users.role_flag',
            'rules' => 'required'
        ),
        array(
            'field' => 'language',
            'label' => 'lang:users.language',
            'rules' => 'trim'
        ),
        array(
            'field' => 'currency_id',
            'label' => 'lang:users.currency_id',
            'rules' => 'trim'
        ),
        array(
            'field' => 'decimal_separator',
            'label' => 'lang:users.decimal_separator',
            'rules' => 'trim'
        ),
        array(
            'field' => 'date_format',
            'label' => 'lang:users.date_format',
            'rules' => 'trim'
        ),
        array(
            'field' => 'vat_rate',
            'label' => 'lang:users.vat_rate',
            'rules' => 'trim'
        ),
    );
    private $validation_rules03 = array(
        array(
            'field' => 'password',
            'label' => 'lang:users.password',
            'rules' => 'required|trim|matches[repeat_password]|min_length[6]|max_length[255]'
        ),
        array(
            'field' => 'repeat_password',
            'label' => 'lang:users.repeat_password',
            'rules' => 'required|trim|min_length[6]|max_length[255]'
        )
    );
    private $validation_rules_assign_phone_number = array(
        array(
            'field' => 'phone_number',
            'label' => 'phone number',
            'rules' => 'required'
        )
    );

    private $validation_rules_assign_phones = array(
        array(
            'field' => 'phone_id',
            'label' => 'phones',
            'rules' => 'required'
        )
    );
    private $change_postbox_location_validation_rules = array(
        array(
            'field' => 'postbox_id',
            'label' => 'Postbox ID',
            'rules' => 'required'
        ),
        array(
            'field' => 'location_id',
            'label' => 'New Location',
            'rules' => 'required'
        )
    );


    /**
     * Validation for basic profile data. The rest of the validation is built by streams.
     *
     * @var array
     */
    private $assign_postbox_validation_rules = array(
        array(
            'field' => 'postbox_id',
            'label' => 'Postbox name',
            'rules' => 'required'
        ),
        array(
            'field' => 'customer_id',
            'label' => 'User name',
            'rules' => 'trim'
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
        $this->load->model('customers/customer_user_m');
        $this->load->model('customers/customer_m');
        $this->load->model('customers/phone_customer_user_m');
        $this->load->model('customers/postbox_customer_user_m');
        $this->load->model('settings/currencies_m');
        $this->load->model('settings/countries_m');
        $this->load->model('phones/phone_customer_subaccount_m');
        $this->load->model('phones/phone_area_code_m');
        $this->load->model('phones/phone_voiceapp_m');
        $this->load->model('phones/phone_number_m');
        $this->load->model('settings/countries_m');
        $this->load->model('phones/phone_m');
        $this->load->model('mailbox/postbox_m');
        $this->load->model('addresses/customers_address_m');

        $this->load->library('customers/customers_api');
        $this->load->library('addresses/addresses_api');
        $this->load->library('account/account_api');

        $this->lang->load('account');
        $this->lang->load('user');
        $this->lang->load('phones/phones');

        $this->load->library('phones/sonetel');
        $this->load->library('phones/phones_api');
        $this->load->library('account/account_api');
    }

    /**************************************************************************/
    // General user
    /**************************************************************************/

    /**
     * General user
     */
    public function general_users() {
        $this->template->build('users/general_users/index');
    }

    /**
     * Search general user
     */
    public function search_users() {
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $array_condition = array();
        $array_condition ["(customers.parent_customer_id = '".$parent_customer_id."' OR customers.customer_id = '".$parent_customer_id."')"] = null;
        $product_type = $this->input->get_post("product_type");
        $this->template->set("product_type", $product_type);

        // Get input condition
        $enquiry = APUtils::sanitizing($this->input->get_post("enquiry"));
        $hideDeletedUser = $this->input->get_post("hideDeletedUser");

        if (!empty ($enquiry)) {
            $array_condition ["(customers.user_name LIKE '%{$enquiry}%' OR customers.email LIKE '%{$enquiry}%')"] = null;
        }

        // Hide all deleted customer
        if ($hideDeletedUser == '1') {
            $array_condition ["(customers.deleted_flag = 0)"] = null;
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
            $query_result = $this->customer_user_m->get_user_paging($array_condition, $product_type, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

            // Process output data
            $total = $query_result['total'];
            $rows = $query_result['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);

            $i = 0;
            foreach ($rows as $row) {
                $products = account_api::get_list_product($parent_customer_id, $row->customer_id, $product_type);
                $status = '';
                if ($row->status == APConstants::ON_FLAG) {
                    $status = 'Deleted';
                } else if ($row->activated_flag == APConstants::ON_FLAG) {
                    $status = 'Activated';
                } else {
                    $status = 'Not Activated';
                }
                $response->rows [$i] ['id'] = $row->customer_id;
                $response->rows [$i] ['cell'] = array(
                    $row->customer_id,
                    $row->user_name,
                    $row->email,
                    $status,
                    APUtils::convert_timestamp_to_date($row->created_date),
                    $products,
                    lang('users.role_'.$row->role_flag),
                    $row->customer_id,
                );
                $i++;
            }
            echo json_encode($response);
        }
    }

    /**
     * Add general user
     * @return type
     */
    public function add_general_users() {
        $this->template->set_layout(FALSE);
        $user = new stdClass();
        $user->customer_id = '';
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $product_type = $this->input->get_post("product_type");
        $this->template->set("product_type", $product_type);

        $is_enterprise_customer = APContext::isEnterpriseCustomer();
        $vat = APUtils::getVatRateOfCustomer($parent_customer_id);

        if ($this->input->post()) {
            $this->form_validation->set_rules($this->user_validation_rules);

            if ($this->form_validation->run()) {
                $email = $this->input->post('email');
                $user_name = $this->input->post('user_name');
                $password = $this->input->post('password');
                $language = $this->input->post('language');
                $currency_id = $this->input->post('currency_id');
                $decimal_separator = $this->input->post('decimal_separator');
                $date_format = $this->input->post('date_format');
                $role_flag = $this->input->post('role_flag');
                $activated_key = APUtils::generateRandom(30);
                $product_type = $this->input->post('product_type');
                $vat_rate = $this->input->post('vat_rate');
                $customer_type = $this->input->post('customer_type');
                $account_type = $is_enterprise_customer ? APConstants::ENTERPRISE_CUSTOMER : APConstants::NORMAL_CUSTOMER;
                $data = array(
                    "customer_id" => "",
                    "parent_customer_id" => $parent_customer_id,
                    "user_name" => $user_name,
                    "email" => $email,
                    "password" => md5($password),
                    "language" => $language,
                    "currency_id" => $currency_id,
                    "decimal_separator" => $decimal_separator,
                    "date_format" => $date_format,
                    "role_flag" => $role_flag,
                    "account_type" => $account_type,
                    "activated_key" => $activated_key,
                    "vat_rate" => $vat_rate,
                    "customer_type" => $customer_type,
                    "created_date" => time(),
                );

                // save customer user.
                $customer_id = customers_api::updateCustomerUser($data);

                // Add phone information if product type = phone
                if ($product_type === 'phone') {
                    $phone_user_id = account_api::add_phone_user($parent_customer_id, $customer_id, $user_name, $email, $password);

                    // Assign phone number if this is add new case
                    $phonenumber_list_data = APContext::getSessionValue(APConstants::SESSION_PHONENUMBER_USER_DATA);
                    if (!empty($phonenumber_list_data) && count($phonenumber_list_data) > 0) {
                        foreach($phonenumber_list_data as $phone_number){
                            account_api::assign_phonenumber_byuser($parent_customer_id, $customer_id, $phone_number);
                        }
                        APContext::setSessionValue(APConstants::SESSION_PHONENUMBER_USER_DATA, null);
                    }

                    // Assign phones if this is add new case
                    $phones_list_data = APContext::getSessionValue(APConstants::SESSION_PHONES_USER_DATA);
                    if (!empty($phones_list_data) && count($phones_list_data) > 0) {
                        foreach($phones_list_data as $phone_id){
                            account_api::assign_phones_byuser($parent_customer_id, $customer_id, $phone_id);
                        }
                        APContext::setSessionValue(APConstants::SESSION_PHONES_USER_DATA, null);
                    }

                    /**
                    // Change incomming setting
                    $incomming_setting = APContext::getSessionValue(APConstants::SESSION_PHONE_INCOMMING_SETTING_USER_DATA);
                    if (!empty($incomming_setting)) {
                        $account_id = APContext::getSubAccountId($parent_customer_id);
                        // log_message(APConstants::LOG_ERROR, 'incomming_setting: ' . json_encode($incomming_setting));
                        if ($incomming_setting['first_action']['to'] == 'phone') {
                            // Get phone_id based on current $incomming_setting->first_action->id (this is id in database)
                            $db_phone_id = $incomming_setting['first_action']['id'];
                            $selected_phone = $this->phone_m->get($db_phone_id);
                            if (!empty($selected_phone)) {
                                $incomming_setting['first_action']['id'] = $selected_phone->phone_id;
                            }
                        }
                        $this->sonetel->update_user_call_incomming($account_id, $phone_user_id, $incomming_setting);
                        APContext::setSessionValue(APConstants::SESSION_PHONE_INCOMMING_SETTING_USER_DATA, null);
                    }

                    // Change outgoing setting
                    $outgoing_setting = APContext::getSessionValue(APConstants::SESSION_PHONE_OUTGOING_SETTING_USER_DATA);
                    if (!empty($outgoing_setting)) {
                        $account_id = APContext::getSubAccountId($parent_customer_id);
                        $this->sonetel->update_user_outgoing($account_id, $phone_user_id, $outgoing_setting);
                        APContext::setSessionValue(APConstants::SESSION_PHONE_OUTGOING_SETTING_USER_DATA, null);
                    }

                    // Add location area
                    $location_area = APContext::getSessionValue(APConstants::SESSION_PHONE_LOCATION_AREA_USER_DATA);
                    if (!empty($location_area)) {
                        $account_id = APContext::getSubAccountId($parent_customer_id);
                        $country_code = $location_area['country_code'];
                        $area_code = $location_area['area_code'];
                        // Call sonetel to update country and area
                        $this->sonetel->update_user_location($account_id, $phone_user_id, $country_code, $area_code);
                        APContext::setSessionValue(APConstants::SESSION_PHONE_LOCATION_AREA_USER_DATA, null);
                    }
                    */

                }else if($product_type == 'postbox'){
                    // add new postbox into account
                    $list_new_postbox = APContext::getSessionValue(APConstants::NEW_USER_POSTBOX_ENTERPRISE);
                    $new_custmer = APContext::getCustomerByID($customer_id);
                    foreach($list_new_postbox as $p){
                        $new_postbox_id = account_api::addPostbox($new_custmer, $p->account_type, $p->location, $p->custname, $p->company, $p->postname);
                        account_api::add_postbox_to_user($parent_customer_id, $customer_id, $new_postbox_id);
                    }

                    // do assign postbox if add action.
                    $postbox_list_data = APContext::getSessionValue(APConstants::SESSION_POSTBOX_USER_DATA);

                    // If user select postbox
                    if (!empty($postbox_list_data) && count($postbox_list_data) > 0) {
                        foreach($postbox_list_data as $postbox_id){
                            account_api::add_postbox_to_user($parent_customer_id, $customer_id, $postbox_id);
                        }
                        APContext::setSessionValue(APConstants::SESSION_POSTBOX_USER_DATA, null);
                    }
                }

                // Send email confirm for user
                $email_template = $this->email_m->get_by('slug', APConstants::new_customer_register);
                $activated_url = base_url() . "customers/active?key=" . $activated_key;
                $email_data = array(
                    "full_name" => $user_name,
                    "email" => $email,
                    "password" => $password,
                    "active_url" => $activated_url,
                    "site_url" => base_url()
                );
                $content = APUtils::parserString($email_template->content, $email_data);
                MailUtils::sendEmail('', $this->input->get_post('email'), $email_template->subject, $content);

                // update invoice address of user
                $this->update_invoice_address_user_enterprise($parent_customer_id, $customer_id, $customer_type);

                $message = sprintf(lang('users.message.add_success'), $user_name);
                $this->success_output($message);
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
            }
            return;
        }

        // Loop through each validation rule
        foreach ($this->user_validation_rules as $rule) {
            $user->{$rule['field']} = set_value($rule['field']);
        }
        APContext::setSessionValue(APConstants::SESSION_PHONENUMBER_USER_DATA, null);
        APContext::setSessionValue(APConstants::SESSION_POSTBOX_USER_DATA, null);
        APContext::setSessionValue(APConstants::SESSION_PHONES_USER_DATA, null);
        APContext::setSessionValue(APConstants::SESSION_PHONE_INCOMMING_SETTING_USER_DATA, null);
        APContext::setSessionValue(APConstants::SESSION_PHONE_OUTGOING_SETTING_USER_DATA, null);
        APContext::setSessionValue(APConstants::SESSION_PHONE_LOCATION_AREA_USER_DATA, null);
        APContext::setSessionValue(APConstants::NEW_USER_POSTBOX_ENTERPRISE, null);

        // Languages
        $languages = $this->countries_m->getAllLanguagesForDropDownList();
        // Currencies
        $currencies = $this->currencies_m->get_all();
        $this->template->set("languages", $languages);
        $this->template->set("currencies", $currencies);

        $this->template->set("is_enterprise_customer", $is_enterprise_customer);
        $this->template->set("vat", $vat);
        $this->template->set("user", $user);
        $this->template->set("action_type", "add");
        $this->template->build("users/general_users/form");
    }

    /**
     * Edit general user
     * @return type
     */
    public function edit_general_users() {
        $this->template->set_layout(FALSE);
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $product_type = $this->input->get_post("product_type");
        $this->template->set("product_type", $product_type);

        $customer_id = $this->input->get_post("customer_id");
        $is_enterprise_customer = APContext::isEnterpriseCustomer();
        $vat = APUtils::getVatRateOfCustomer($parent_customer_id);

        if($customer_id == $parent_customer_id){
            $user = $this->customer_user_m->get_by_many(array(
                'customer_id' => $customer_id
            ) );
        }else{
            $user = $this->customer_user_m->get_by_many(array(
                "parent_customer_id" => $parent_customer_id,
                'customer_id' => $customer_id)
            );
        }
        if ($this->input->post()) {
            $this->form_validation->set_rules($this->user_validation_rules02);

            if ($this->form_validation->run()) {
                $user_name = $this->input->post('user_name');
                $language = $this->input->post('language');
                $currency_id = $this->input->post('currency_id');
                $decimal_separator = $this->input->post('decimal_separator');
                $date_format = $this->input->post('date_format');
                $role_flag = $this->input->post('role_flag');
                $activate_flag = $this->input->post('status_flag');
                $activated_key = APUtils::generateRandom(30);
                $vat_rate = $this->input->post('vat_rate');
                $customer_type = $this->input->post('customer_type');

                $data = array(
                    "customer_id" => $customer_id,
                    "user_name" => $user_name,
                    "language" => $language,
                    "currency_id" => $currency_id,
                    "decimal_separator" => $decimal_separator,
                    "date_format" => $date_format,
                    "vat_rate" => $vat_rate,
                );

                if($customer_id != $parent_customer_id){
                    $data['parent_customer_id'] = $parent_customer_id;
                    $data['activated_flag'] = $activate_flag;
                    $data['activated_key'] = $activated_key;
                    $data['customer_type'] = $customer_type;
                    $data['role_flag'] = $role_flag;
                }

                // save customer user.
                customers_api::updateCustomerUser($data);

                // update invoice address of user
                $this->update_invoice_address_user_enterprise($parent_customer_id, $customer_id, $customer_type);

                $message = sprintf(lang('users.message.edit_success'), $user_name);
                $this->success_output($message);
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
            }
            return;
        }
        // Languages
        $languages = $this->countries_m->getAllLanguagesForDropDownList();
        // Currencies
        $currencies = $this->currencies_m->get_all();
        $this->template->set("languages", $languages);
        $this->template->set("currencies", $currencies);
        $this->template->set('parent_customer_id', $parent_customer_id);
        $this->template->set("is_enterprise_customer", $is_enterprise_customer);
        $this->template->set("vat", $vat);
        $this->template->set("user", $user);
        $this->template->set("action_type", "edit");
        $this->template->build("users/general_users/form");
    }

    /**
     * Delete general user by id
     * @param type $id
     */
    public function delete_general_users() {
        // get params.
        $id = $this->input->post('user_id');

        $product_id = $this->input->post('product_id');
        $product_type = $this->input->post('product_type');
        $actions = $this->input->post('actions');

        $user_ids = $this->input->post('user_ids');
        $renew = $this->input->post('renewal');

        $parent_customer = APContext::getParentCustomerLoggedIn();
        $list_user_id = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($parent_customer->customer_id);

        // validate 10 users.
        if(count($list_user_id) < 10){
            $this->error_output(lang('users.message.cannot_delete_user_under_10'));
            return;
        }

        $index = 0;
        $parent_customer_id = $parent_customer->customer_id;
        if(!empty($product_id)){
            foreach($product_id as $pid){
                $action = $actions[$index];
                $product = $product_type[$index];

                // postbox product.
                if($product == 'postbox'){
                    switch($action){
                        // assign to another customer.
                        case "0":
                            $to_user_id = $user_ids[$index];
                            $new_postbox_id = account_api::reassignPostboxToUser($pid, $to_user_id, $parent_customer_id);

                            if($new_postbox_id == $pid){
                                APUtils::deletePostbox($pid, $id, APConstants::POSTBOX_DELETE_ORDER_BY_CUSTOMER);
                            }
                            break;

                        // TODO: terminate product at end of contract
                        case "1":

                        // terminate now.
                        case "2":
                            APUtils::deletePostbox($pid, $id, APConstants::POSTBOX_DELETE_ORDER_BY_CUSTOMER);
                            break;
                    }
                }

                // TODO: phone product


                $index ++;
            }
        }

        // delete customer.
        CustomerUtils::deleteUser($parent_customer_id, $id, false, false, 0, APContext::getCustomerCodeLoggedIn());

        $this->success_output(lang('users.message.delete_user_success'));
        return;
    }

    /**
     * Change general user password
     */
    public function change_password_general_users() {
        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('id');
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $user = $this->customer_user_m->get_by_many(array(
            "customer_id" => $customer_id,
            "parent_customer_id" => $parent_customer_id
        ));

        if ($this->input->post()) {
            $this->form_validation->set_rules($this->validation_rules03);
            if ($this->form_validation->run()) {
                $update_data = array();
                $update_data ['password'] = md5($this->input->post('password'));
                $this->customer_user_m->update_by_many(array(
                    "customer_id" => $customer_id,
                    "parent_customer_id" => $parent_customer_id
                ), $update_data);

                $this->success_output(lang('users.message.change_password_success'));
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        $this->template->set('user', $user)->build('users/general_users/change_pass');
    }

    /**
     * Search product based on product_type (all|postbox|phonenumber)
     */
    public function search_product() {
        $customer_id = $this->input->get_post('customer_id');
        $product_type = $this->input->get_post('product_type');
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $list_product = array();

        // Get data
        if ($product_type == 'all') {
            $list_postbox = $this->get_postbox_product($parent_customer_id, $customer_id);
            $list_phonnumber = $this->get_phonenumber_product($parent_customer_id, $customer_id);
            $list_product = array_merge($list_postbox, $list_phonnumber);
        } else if ($product_type == 'postbox') {
            $list_product = $this->get_postbox_product($parent_customer_id, $customer_id);
        } else if ($product_type == 'phonenumber') {
            $list_product = $this->get_phonenumber_product($parent_customer_id, $customer_id);
        }

        // update limit into user_paging.
        $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APConstants::NUMBER_RECORD_PER_PAGE_CODE;

        // Get paging input
        $input_paging = $this->get_paging_input();
        $input_paging ['limit'] = $limit;
        // Process output data
        $total = count($list_product);
        $rows = $list_product;

        // Get output response
        $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);

        $i = 0;
        if ($product_type == 'all') {
            foreach ($rows as $row) {
                $response->rows [$i] ['id'] = $row->id;
                $response->rows [$i] ['cell'] = array(
                    $row->id,
                    $row->code,
                    $row->category,
                    $row->product,
                    $row->description,
                    //$row->type,
                    $row->created_date,
                    $row->status,
                    $row->id,
                );
                $i++;
            }
        } else if ($product_type == 'postbox') {
            foreach ($rows as $row) {
                $response->rows [$i] ['id'] = $row->id;
                $response->rows [$i] ['cell'] = array(
                    $row->id,
                    $row->code,
                    $row->product_name,
                    $row->location_name,
                    //$row->type,
                    $row->name,
                    $row->company,
                    $row->created_date,
                    $row->status,
                    $row->id,
                );
                $i++;
            }
        }
        echo json_encode($response);

    }

    /**
     * Get postbox product by customer id and user id
     *
     * @param type $parent_customer_id
     * @param type $customer_id
     */
    private function get_postbox_product($parent_customer_id, $customer_id) {
        $list_result = array();
        $parent_customer = CustomerUtils::getCustomerByID($parent_customer_id);

        if(empty($customer_id)){
            $postbox_list_data = APContext::getSessionValue(APConstants::NEW_USER_POSTBOX_ENTERPRISE);
            if (!empty($postbox_list_data)){
                foreach($postbox_list_data as  $item){
                    $location = addresses_api::getLocationByID($item->location);

                    $new_item = new stdClass();
                    $new_item->id = $item->id;
                    $new_item->code = $item->postname;
                    $new_item->category = 'Postbox';
                    $new_item->product = 'Postbox '.$location->location_name;
                    $new_item->description = $item->custname;
                    if (!empty($item->company)) {
                        $new_item->description = $item->company;
                    }
                    $new_item->product_name = $item->postname;
                    $new_item->name = $item->custname;
                    $new_item->company = $item->company;
                    $new_item->location_name = $location->location_name;
                    $new_item->type = lang('account_type_'.$item->account_type);
                    $new_item->created_date = APUtils::convert_timestamp_to_date(now());
                    $new_item->status = '';
                    if ($parent_customer->activated_flag != '1') {
                        $new_item->status = 'InActive';
                    } else {
                        $new_item->status = '<a href="'.base_url().'cases/services?case=verification" target="_blank">verify now </a>';
                    }
                    $list_result[] = $new_item;
                }
            }
            //$postbox_list_data = APContext::getSessionValue(APConstants::SESSION_POSTBOX_USER_DATA);
            //$list = $this->customer_user_m->get_list_postbox_by($parent_customer_id, $postbox_list_data);
        }else{
            $list = $this->customer_user_m->get_list_postbox_byuser($parent_customer_id, $customer_id);
            if (!empty($list)){
                foreach ($list as $item) {
                    $new_item = new stdClass();
                    $new_item->id = $item->id;
                    $new_item->code = $item->postbox_code;
                    $new_item->category = 'Postbox';
                    $new_item->product = 'Postbox '.$item->location_name;
                    $new_item->description = $item->name;
                    if (!empty($item->company)) {
                        $new_item->description = $item->company;
                    }
                    $new_item->product_name = $item->postbox_name;
                    $new_item->name = $item->name;
                    $new_item->company = $item->company;
                    $new_item->location_name = $item->location_name;
                    $new_item->type = lang('account_type_'.$item->type);
                    $new_item->created_date = APUtils::convert_timestamp_to_date($item->created_date);
                    $new_item->status = '';
                    if ($item->name_verification_flag == APConstants::OFF_FLAG
                        || $item->company_verification_flag == APConstants::OFF_FLAG ) {
                        $new_item->status = 'verify now';
                    }
                    //else if ($parent_customer->activated_flag == '1') {
                    //    $new_item->status = 'Active';
                    //} else {
                    //   $new_item->status = 'InActive';
                    //}
                    $list_result[] = $new_item;
                }
            }
        }

        return $list_result;
    }

    /**
     * Get postbox product by customer id and user id
     *
     * @param type $parent_customer_id
     * @param type $customer_id
     */
    private function get_phonenumber_product($parent_customer_id, $customer_id) {
        $list = $this->phone_number_m->get_list_phonenumber_by_user($parent_customer_id, $customer_id);
        // $parent_customer = CustomerUtils::getCustomerByID($parent_customer_id);
        $list_result = array();
        foreach ($list as $item) {
            $new_item = new stdClass();
            $new_item->id = $item->id;
            $new_item->code = $item->phone_code;
            $new_item->category = 'PhoneNumber';
            $new_item->product = 'PhoneNumber '.$item->phone_number;
            $new_item->description = '';
            $new_item->product_name = $item->location_name;
            $new_item->name = '';
            $new_item->company = '';
            $new_item->location_name = $item->location_name;
            $new_item->type = '';
            $new_item->created_date = APUtils::convert_timestamp_to_date($item->created_date);
            $new_item->status = '';

            //if ($item->name_verification_flag == APConstants::OFF_FLAG
            //    || $item->company_verification_flag == APConstants::OFF_FLAG ) {
            //    $new_item->status = '<a href="'.base_url().'cases/services?case=verification" target="_blank">verify now </a>';
            //} else

            if ($item->status == '1') {
                $new_item->status = 'Active';
            } else {
                $new_item->status = 'InActive';
            }
            $list_result[] = $new_item;
        }
        return $list_result;
    }

    /**
     * confirm delete user.
     */
    public function confirm_delete_user(){
        $this->template->set_layout(FALSE);
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $customer_id = $this->input->get_post("user_id");
        $user = $this->customer_m->get_by_many(array(
            "parent_customer_id" => $parent_customer_id,
            'customer_id' => $customer_id
        ));

        // donot parent customer in this screen.
        if($customer_id == $parent_customer_id){
            echo "<h2>You can not delete your account in this screen.</h2>";
            return;
        }

        // product result.
        $products = array();

        // get list postbox
        $postboxes = $this->postbox_m->get_postboxes_by($customer_id);
        foreach($postboxes as $postbox){
            $tmp = new stdClass();
            $tmp->product = "Postbox";
            $tmp->product_type = "postbox";
            $tmp->product_id = $postbox->postbox_id;
            $tmp->description = $postbox->postbox_name.' - '.$postbox->location_name;
            $tmp->created_date = APUtils::convert_timestamp_to_date($postbox->created_date);
            $tmp->contract_date = "";
            $tmp->contract_term = account_api::getContractTermBy($postbox->postbox_id, "postbox");
            $tmp->renewal = 1;
            $products[] = $tmp;
            unset($tmp);
        }

        // get list phone number
        $list_phones = $this->phone_number_m->get_list_phonenumber_by_user($parent_customer_id, $customer_id);
        foreach($list_phones as $phone){
            $tmp = new stdClass();
            $tmp->product = "Phone Number";
            $tmp->product_type = "phone";
            $tmp->product_id = $phone->phone_user_id;
            $tmp->description = $phone->location_name." - ".$phone->phone_number;
            $tmp->created_date = APUtils::convert_timestamp_to_date($phone->created_date);
            $tmp->contract_date = APUtils::convert_timestamp_to_date($phone->end_contract_datec);
            $tmp->contract_term = account_api::getContractTermBy($phone->id, "postbox");
            $tmp->renewal = $phone->auto_renewal;
            $products[] = $tmp;
            unset($tmp);
        }

        $this->template->set("products", $products);
        $this->template->set("user", $user);
        $this->template->set("action_type", "edit");
        $this->template->build("users/general_users/confirm_delete_user");
    }

    /**
     * confirm target user before assign product.
     */
    public function confirm_target_user(){
        $this->template->set_layout(false);
        $product_list_ids = $this->input->get_post("list_product_id");
        $product_ids = explode(",", $product_list_ids);
        $user_id = $this->input->get_post('user_id');

        $list_product = array();

        foreach($product_ids as $id){
            if(empty($id)){
                continue;
            }
            $postbox_id = explode("-", $id);

            // postbox product
            // get postbox information
            if($postbox_id[1] == 'postbox'){
                $tmp = new stdClass();
                $postbox = $this->postbox_m->get_postbox_location_by($postbox_id[0]);
                $tmp->product_id = $postbox->postbox_id;
                $tmp->description = 'Postbox-'.$postbox->postbox_name.' - '.$postbox->location_name;
                $tmp->type = $postbox_id[1];
                $tmp->action = $postbox_id[2];

                $list_product[] = $tmp;
                unset($tmp);
            }

            // TODO: phone product

        }

        // get all users and parent customers
        $list_customer = array();
        $parent_customer = APContext::getParentCustomerLoggedIn();
        $list_customer[] = $parent_customer;
        $list_user_id = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($parent_customer->customer_id);
        foreach($list_user_id as $uid){
            if($uid != $user_id){
                $list_customer[] = APContext::getCustomerByID($uid);
            }
        }

        $this->template->set('list_customer', $list_customer);
        $this->template->set('user_id', $user_id);
        $this->template->set("list_product", $list_product);
        $this->template->build("users/general_users/confirm_target_user");
    }


    /**************************************************************************/
    // Postbox user
    /**************************************************************************/
     /**
     * Postbox user
     */
    public function postbox_users() {
        $this->template->build('users/postbox_users/index');
    }

    /**
     * Assign postbox
     */
    public function assign_postbox() {
        $this->template->set_layout(FALSE);
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $customer_id = $this->input->get_post("customer_id");

        // Gets list availble postbox of
        $list_postbox = account_api::getListAvailPostboxOfEnterpriseCustomer($parent_customer_id);

        // Submit data
        if ($this->input->post()) {
            $this->form_validation->set_rules($this->assign_postbox_validation_rules);

            if ($this->form_validation->run()) {
                if(empty($customer_id)){
                    $postbox_id = $this->input->get_post("postbox_id");

                    // add postbox into session.
                    $postbox_list_data = APContext::getSessionValue(APConstants::SESSION_POSTBOX_USER_DATA);
                    if(empty($postbox_list_data)){
                        $postbox_list_data = array();
                    }
                    if(!in_array($postbox_id, $postbox_list_data)){
                        $postbox_list_data[] = $postbox_id;
                    }
                    APContext::setSessionValue(APConstants::SESSION_POSTBOX_USER_DATA, $postbox_list_data);
                }else{
                    $postbox_id = $this->input->get_post("postbox_id");
                    // Call account api to add postbox to sepecify user.
                    account_api::add_postbox_to_user($parent_customer_id, $customer_id, $postbox_id);
                }
                $message = lang('users.message.assign_postbox');
                $this->success_output($message);
                return;

            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Display the current page
        $this->template->set('customer_id', $customer_id);
        $this->template->set('list_postbox', $list_postbox)->build('users/postbox_users/assign_postbox');
    }

    /**
     * Unassign postbox
     */
    public function unassign_postbox() {
        $this->template->set_layout(FALSE);
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $postbox_user_id = $this->input->get_post("postbox_user_id");

        $postbox_list_data = APContext::getSessionValue(APConstants::SESSION_POSTBOX_USER_DATA);
        if(!empty($postbox_list_data)){
            if(($key = array_search($postbox_user_id, $postbox_list_data)) !== false) {
                unset($postbox_list_data[$key]);
            }
            APContext::setSessionValue(APConstants::SESSION_POSTBOX_USER_DATA, $postbox_list_data);
        }else{
            // Call account api to add postbox to sepecify user.
            account_api::delete_postbox_to_user_byid($parent_customer_id, $postbox_user_id);
        }

        // unassign postbox for new user.
        $list_new_postbox = APContext::getSessionValue(APConstants::NEW_USER_POSTBOX_ENTERPRISE);
        if(!empty($list_new_postbox)){
            foreach($list_new_postbox as $key=>$item){
                if($item->id == $postbox_user_id){
                    unset($list_new_postbox[$key]);
                    APContext::setSessionValue(APConstants::NEW_USER_POSTBOX_ENTERPRISE, $list_new_postbox);
                    break;
                }
            }
        }

        $message = lang('users.message.unassign_postbox');
        $this->success_output($message);
        return;
    }


    /**************************************************************************/
    // Phone user
    /**************************************************************************/
    /**
     * Phone users
     */
    public function phone_users() {
        $this->template->build('users/phone_users/index');
    }

    public function search_phone_users() {
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $array_condition = array();
        $array_condition ["parent_customer_id"] = $parent_customer_id;
        //$array_condition ["deleted_flag"] = APConstants::OFF_FLAG;
        // If current request is ajax
        if ($this->is_ajax_request()) {
            // update limit into user_paging.
            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APConstants::NUMBER_RECORD_PER_PAGE_CODE;

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging ['limit'] = $limit;

            // Call search method
            $query_result = $this->phone_customer_user_m->get_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

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
                    $row->name,
                    $row->email,
                    $row->activated_flag,
                    APUtils::convert_timestamp_to_date($row->created_date),
                    $row->deleted_flag,
                    $row->id,
                );
                $i++;
            }
            echo json_encode($response);
        }
    }

    public function edit_phone_users() {
        $this->template->set_layout(FALSE);
        $product_type = $this->input->get_post("product_type");
        $this->template->set("product_type", $product_type);
        $is_enterprise_customer = APContext::isEnterpriseCustomer();
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $customer_id = $this->input->get_post('customer_id');
        $user = $this->customer_user_m->get_by_many(array(
            "(parent_customer_id ='".$parent_customer_id."' OR customer_id='".$parent_customer_id."')" => null,
            'customer_id' => $customer_id)
        );
        $account_id = APContext::getSubAccountId($parent_customer_id);
        $phone_user_id = customers_api::getUserPhoneIdByCLUserId($parent_customer_id, $customer_id);
        $vat = APUtils::getVatRateOfCustomer($parent_customer_id);
        // Submit data
        if ($this->input->post()) {
            $this->form_validation->set_rules($this->user_validation_rules02);
            if ($this->form_validation->run()) {
                $user_name = $this->input->post('user_name');
                $language = $this->input->post('language');
                $currency_id = $this->input->post('currency_id');
                $decimal_separator = $this->input->post('decimal_separator');
                $date_format = $this->input->post('date_format');
                $role_flag = $this->input->post('role_flag');
                $activate_flag = $this->input->post('status_flag');
                $customer_type = $this->input->post('customer_type');
                $data = array(
                    "customer_id" => $customer_id,
                    "parent_customer_id" => $parent_customer_id,
                    "user_name" => $user_name,
                    "language" => $language,
                    "currency_id" => $currency_id,
                    "decimal_separator" => $decimal_separator,
                    "date_format" => $date_format,
                    "role_flag" => $role_flag,
                    "activated_flag" => $activate_flag,
                    "customer_type" => $customer_type,
                    "created_date" => time(),
                );

                // save customer user.
                customers_api::updateCustomerUser($data);

                // reload user list
                APContext::reloadListUser($parent_customer_id, $customer_id);

                $message = sprintf(lang('users.message.edit_success'), $user_name);
                $this->success_output($message);
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Load sonetel user
        $sonetel_user = null;
        if (!empty($phone_user_id)) {
            try{
                $sonetel_user = $this->sonetel->get_user($account_id, $phone_user_id);
            }catch(Exception $e){
                echo "We can not get phone number information at the moment. Please try again later.";
                return;
            }
        }
        // Get list country
        $list_country = $this->countries_m->get_all();
        $this->template->set("list_country", $list_country);
        $country_code =  !empty($sonetel_user->location) ? $sonetel_user->location->country : '';

        $country = $this->countries_m->get_by('country_code_3', $country_code);
        $country_id = !empty($country) ? $country->id : '';
        $list_area = $this->phone_area_code_m->get_many_by_many(array(
            'country_id' => $country_id
        ));
        $this->template->set("list_area", $list_area);

        // Get area information
        $area_code =  !empty($sonetel_user->location) ? $sonetel_user->location->area_code : '';
        $select_area = $this->phone_area_code_m->get_by_many(array(
            'country_id' => $country_id,
            'area_code' => $area_code
        ));

        // Languages
        $languages = $this->countries_m->getAllLanguagesForDropDownList();
        // Currencies
        $currencies = $this->currencies_m->get_all();
        $this->template->set("is_enterprise_customer", $is_enterprise_customer);
        $this->template->set("languages", $languages);
        $this->template->set("currencies", $currencies);
        $this->template->set("country", $country);
        $this->template->set("vat", $vat);
        $this->template->set("user", $user);
        $this->template->set("sonetel_user", $sonetel_user);
        $this->template->set("select_area", $select_area);
        $this->template->set("action_type", "edit");
        $this->template->build("users/phone_users/form");
    }

    /**
     * Change area location form
     */
    public function change_location_area() {
        $this->template->set_layout(FALSE);
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $customer_id = $this->input->get_post('customer_id');

        if (!empty($customer_id)) {
            $account_id = APContext::getSubAccountId($parent_customer_id);
            $phone_user_id = customers_api::getUserPhoneIdByCLUserId($parent_customer_id, $customer_id);
        } else {
            APContext::setSessionValue(APConstants::SESSION_PHONE_LOCATION_AREA_USER_DATA, null);
            $phone_user_id = '';
        }


        // Submit data
        if ($this->input->post()) {
            $country_code = $this->input->get_post('country_code');
            $area_code = $this->input->get_post('area_code');

            if (!empty($customer_id)) {
                // Call sonetel to update country and area
                $this->sonetel->update_user_location($account_id, $phone_user_id, $country_code, $area_code);
            } else {
                // Save to session
                $location_area = array('country_code' => $country_code, 'area_code' => $area_code);
                APContext::setSessionValue(APConstants::SESSION_PHONE_LOCATION_AREA_USER_DATA, $location_area);
            }
            $message = lang('users.message.edit_location_success');
            $this->success_output($message);
            return;
        }

        // Load sonetel user
        $sonetel_user = null;
        if (!empty($phone_user_id)) {
            $sonetel_user = $this->sonetel->get_user($account_id, $phone_user_id);
        }
        // Get list country
        $list_country = phones_api::get_all_countries();
        $this->template->set("list_country", $list_country);
        $country_code =  !empty($sonetel_user->location) ? $sonetel_user->location->country : '';

        $country = $this->countries_m->get_by('country_code_3', $country_code);
        $country_id = !empty($country) ? $country->id : '';
        $list_area = $this->phone_area_code_m->get_many_by_many(array(
            'country_id' => $country_id
        ));
        // Get area information
        $area_code =  !empty($sonetel_user->location) ? $sonetel_user->location->area_code : '';
        $select_area = $this->phone_area_code_m->get_by_many(array(
            'country_id' => $country_id,
            'area_code' => $area_code
        ));

        $this->template->set("country", $country);
        $this->template->set("list_area", $list_area);
        $this->template->set("customer_id", $customer_id);
        $this->template->set("sonetel_user", $sonetel_user);
        $this->template->set("select_area", $select_area);
        $this->template->set("action_type", "edit");
        $this->template->build("users/phone_users/change_location_area");
    }

    /**
     * Get list of phone number associated with user.
     */
    public function load_phonenumber_users() {
        $this->template->set_layout(FALSE);
        $product_type = $this->input->get_post("product_type");
        $this->template->set("product_type", $product_type);

        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $customer_id = $this->input->get_post('customer_id');
        $account_id = APContext::getSubAccountId($parent_customer_id);
        // $phone_user_id = customers_api::getUserPhoneIdByCLUserId($parent_customer_id, $customer_id);

        $sonetel_user_numbers = array();
        // Edit case
        if (!empty($customer_id)) {
            $array_condition = array();
            $array_condition['parent_customer_id'] = $parent_customer_id;
            $array_condition['customer_id'] = $customer_id;
            // $array_condition['phone_user_id'] = $phone_user_id;
            $sonetel_user_numbers = $this->phone_number_m->get_many_by_many($array_condition);
        }
        // Add new case
        else {
            $phone_number_list_data = APContext::getSessionValue(APConstants::SESSION_PHONENUMBER_USER_DATA);
            $array_condition = array();
            $array_condition['parent_customer_id'] = $parent_customer_id;
            if (!empty($phone_number_list_data) && count($phone_number_list_data) > 0) {
                $array_condition['phone_number IN ('. implode(',', $phone_number_list_data) .')'] = null;
            } else {
                $array_condition['phone_number IN (0)'] = null;
            }
            $sonetel_user_numbers = $this->phone_number_m->get_many_by_many($array_condition);
        }

        // update limit into user_paging.
        $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APConstants::NUMBER_RECORD_PER_PAGE_CODE;

        // Get paging input
        $input_paging = $this->get_paging_input();
        $input_paging ['limit'] = $limit;

        // Process output data
        $total = count($sonetel_user_numbers);
        $rows = $sonetel_user_numbers;

        // Get output response
        $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);

        $i = 0;
        foreach ($rows as $row) {
            $location = $this->get_location($parent_customer_id, $customer_id, $row->phone_number);
            $response->rows [$i] ['id'] = $row->id;
            $response->rows [$i] ['cell'] = array(
                trim($row->id),
                $row->phone_number,
                $location,
                '',
                $row->phone_number,
            );
            $i++;
        }
        echo json_encode($response);
    }

    /**
     * Get list of phone number associated with user.
     */
    public function load_handling_rules() {
        $this->template->set_layout(FALSE);

        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $customer_id = $this->input->get_post('customer_id');
        $account_id = APContext::getSubAccountId($parent_customer_id);
        $phone_user_id = customers_api::getUserPhoneIdByCLUserId($parent_customer_id, $customer_id);

        $sonetel_user_numbers = array();
        // Edit case
        if (!empty($customer_id)) {
            $array_condition = array();
            $array_condition['parent_customer_id'] = $parent_customer_id;
            $array_condition['customer_id'] = $customer_id;
            // $array_condition['phone_user_id'] = $phone_user_id;
            $sonetel_user_numbers = $this->phone_number_m->get_many_by_many($array_condition);
        }
        // Add new case
        else {
            $phone_number_list_data = APContext::getSessionValue(APConstants::SESSION_PHONENUMBER_USER_DATA);
            $array_condition = array();
            $array_condition['parent_customer_id'] = $parent_customer_id;
            if (!empty($phone_number_list_data) && count($phone_number_list_data) > 0) {
                $array_condition['phone_number IN ('. implode(',', $phone_number_list_data) .')'] = null;
            } else {
                $array_condition['phone_number IN (0)'] = null;
            }
            $sonetel_user_numbers = $this->phone_number_m->get_many_by_many($array_condition);
        }

        // update limit into user_paging.
        $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APConstants::NUMBER_RECORD_PER_PAGE_CODE;

        // Get paging input
        $input_paging = $this->get_paging_input();
        $input_paging ['limit'] = $limit;

        // Process output data
        $total = count($sonetel_user_numbers);
        $rows = $sonetel_user_numbers;

        // Get output response
        $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
        // Get sonetel detail handling rule information
        $sonetel_user = null;
        if (!empty($phone_user_id)) {
            $sonetel_user = $this->sonetel->get_user($account_id, $phone_user_id);
        }

        $i = 0;
        foreach ($rows as $row) {
            $first_action_label = '';
            $sedond_action_label = '';
            $show_target = '';
            $status = 'Active';
            if (!empty($sonetel_user)) {
                // Build first action
                $action = $sonetel_user->call->incoming->first_action;
                if ($action->action == 'ring') {
                    $first_action_label = 'Ring to my '.$action->to;
                    if (isset($action->id) && $action->id != '-NA-') {
                        $first_action_label = $first_action_label.' ('.$action->id.')';
                    }
                } else if ($action->action == 'forward') {
                    $first_action_label = 'Forward calls to my '.$action->to;
                    if (isset($action->id) && $action->id != '-NA-') {
                        $first_action_label = $first_action_label.' ('.$action->id.')';
                    }
                } else {
                    $first_action_label = 'Disconnect the call';
                }

                // Build second action
                $action = $sonetel_user->call->incoming->second_action;
                if ($action->action == 'ring') {
                    $sedond_action_label = 'Ring to my '.$action->to;
                    if (isset($action->id) && $action->id != '-NA-') {
                        $sedond_action_label = $sedond_action_label.' ('.$action->id.')';
                    }
                } else if ($action->action == 'forward') {
                    $sedond_action_label = 'Forward calls to my '.$action->to;
                    if (isset($action->id) && $action->id != '-NA-') {
                        $sedond_action_label = $sedond_action_label.' ('.$action->id.')';
                    }
                } else {
                    $sedond_action_label = 'Disconnect the call';
                }

                // Show target
                $show = $sonetel_user->call->outgoing->show;
                if ($show == 'auto') {
                    $show_target =  'Automatic';
                } else if ($show == 'none') {
                    $show_target =  'None';
                } else if ($show == 'inum') {
                    $show_target =  'iNUM';
                } else {
                    $show_target = $show;
                }
            }

            $response->rows [$i] ['id'] = $row->id;
            $response->rows [$i] ['cell'] = array(
                trim($row->id),
                $phone_user_id,
                $row->phone_number,
                $first_action_label,
                $sedond_action_label,
                $show_target,
                $status
            );
            $i++;
        }
        echo json_encode($response);
    }

    /**
     * Get list of phone number associated with user.
     */
    public function handling_rules() {
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $customer_id = APContext::getCustomerCodeLoggedIn();

        if ($this->is_ajax_request()) {
            $account_id = APContext::getSubAccountId($parent_customer_id);
            // $phone_user_id = customers_api::getUserPhoneIdByCLUserId($parent_customer_id, $customer_id);

            // Edit case
            $array_condition = array();
            $array_condition['phone_number.parent_customer_id'] = $parent_customer_id;
            if (!APContext::isAdminCustomerUser()) {
                $array_condition['phone_number.customer_id'] = $customer_id;
            }
            // $array_condition['phone_user_id'] = $phone_user_id;

            // update limit into user_paging.
            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APConstants::NUMBER_RECORD_PER_PAGE_CODE;

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging ['limit'] = $limit;

            $query_result = $this->phone_number_m->get_number_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

             // Process output data
            $total = $query_result['total'];
            $rows = $query_result['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);

            $i = 0;
            foreach ($rows as $row) {
                // Get sonetel detail handling rule information
                $phonenumbersubscription = $this->sonetel->get_phonenumbersubscription_byphonenumber($account_id, $row->phone_number);
                $connect_to_type = $phonenumbersubscription->connect_to_type;
                $connect_to = $phonenumbersubscription->connect_to;
                // $status = $phonenumbersubscription->status;
                $status = $row->status == APConstants::ON_FLAG ? 'Active' : 'Deactivate';
                if ($connect_to_type == 'app') {
                    $connect_to = $phonenumbersubscription->connect_to->app_type;
                    if (isset($phonenumbersubscription->connect_to->app_id)) {
                        $connect_to = $connect_to.':'.$phonenumbersubscription->connect_to->app_id;
                    }
                }
                if ($connect_to == 'null') {
                    $connect_to = '';
                }

                $response->rows [$i] ['id'] = $row->id;
                $response->rows [$i] ['cell'] = array(
                    trim($row->id),
                    $row->phone_number,
                    $row->country_name,
                    $row->area_name,
                    lang('connect_to_type_'.$connect_to_type),
                    $connect_to,
                    $status,
                    $row->phone_number
                );
                $i++;
            }
            echo json_encode($response);
            return;
        }
        $this->template->set('customer_id', $customer_id)->build('handling_rules/index');
    }

    /**
     * Get location of phone number
     * @param type $customer_id
     * @param type $user_id
     * @param type $phone_number
     */
    private function get_location($parent_customer_id, $customer_id,  $phone_number) {
        $location = $this->phone_number_m->get_location($parent_customer_id, $customer_id,  $phone_number);
        $result = '';
        if (!empty($location)) {
            $result = $location->location_name .' ('. $location->country_name.')';
        }
        return $result;
    }

    /**
     * Get list of phone number associated with user.
     */
    public function load_phones_users() {
        $this->template->set_layout(FALSE);
        $product_type = $this->input->get_post("product_type");
        $this->template->set("product_type", $product_type);

        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $customer_id = $this->input->get_post('customer_id');

        // update limit into user_paging.
        $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APConstants::NUMBER_RECORD_PER_PAGE_CODE;

        // Get paging input
        $input_paging = $this->get_paging_input();
        $input_paging ['limit'] = $limit;

        // Call search method
        $array_condition = array();
        $array_condition['parent_customer_id'] = $parent_customer_id;
        if (!empty($customer_id)) {
            $array_condition['customer_id'] = $customer_id;
        } else {
            $phone_id_list_data = APContext::getSessionValue(APConstants::SESSION_PHONES_USER_DATA);
            if (!empty($phone_id_list_data) && count($phone_id_list_data) > 0) {
                $array_condition['id IN ('. implode(',', $phone_id_list_data) .')'] = null;
            } else {
                $array_condition['id'] = 0;
            }
        }


        $query_result = $this->phone_m->get_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

        // Process output data
        $total = $query_result['total'];
        $rows = $query_result['data'];

        // Get output response
        $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);

        $i = 0;
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $phone_type = $row->phone_type;
                $phone_name = $row->phone_name;
                $phone_number = $row->phone_number;

                $response->rows [$i] ['id'] = $row->id;
                $response->rows [$i] ['cell'] = array(
                    $row->id,
                    $phone_name,
                    $phone_type,
                    $phone_number,
                    $row->id,
                );
                $i++;
            }
        }
        echo json_encode($response);
    }

    public function delete_phone_users($id = '') {
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $this->template->set_layout(FALSE);

        // Get account id
        $account_id = APContext::getSubAccountId($parent_customer_id);
        $phone_user_id = customers_api::getUserPhoneIdByCLUserId($parent_customer_id, $id);
        if (empty($account_id) || empty($phone_user_id)) {
            $this->success_output("");
            return;
        }
        $this->sonetel->delete_user($account_id, $phone_user_id);
        $this->phone_customer_user_m->delete_by_many(array(
            "parent_customer_id" => $parent_customer_id,
            "customer_id" => $parent_customer_id
        ));
        $this->success_output("");
    }

    public function _check_email($email) {
        $id = $this->input->get_post("id");
        // Get user information by email
        $customer_user = $this->customer_user_m->get_by_many(array(
            "email" => $email,
            "deleted_flag" => APConstants::OFF_FLAG
        ));

        if ($customer_user && $customer_user->customer_id != $id) {
            $this->form_validation->set_message('_check_email', lang('users.message.email_exist'));
            return false;
        }

        // Check exist in customer table
        $customer = $this->customer_m->get_by_many(array(
            "email" => $email,
            "deleted_flag" => APConstants::OFF_FLAG
        ));
        if (!empty($customer)) {
            $this->form_validation->set_message('_check_email', lang('users.message.email_exist'));
            return false;
        }

        return true;
    }

    /**
     * Load list of object to binding with the target of voice app.
     *
     * menu_digit_action (call_user | call_other | play_prompt | connect_app | disconnect)
     */
    public function load_voiceapp_target() {
        $menu_digit_action = $this->input->get_post('menu_digit_action');
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();

        $array_condition = array();
        $array_condition ["parent_customer_id"] = $parent_customer_id;
        // Load list of user
        if ($menu_digit_action == 'call_user') {
            $list_user_phone = $this->phone_customer_user_m->get_many_by_many($array_condition);
            $list_data = array();
            foreach ($list_user_phone as $item) {
                $obj = new stdClass();
                $obj->key = $item->phone_user_id;
                $obj->label = $item->name;
                $list_data[] = $obj;
            }
            echo json_encode($list_data);
            return;
        } else if ($menu_digit_action == 'connect_app') {
            $list_voice_app = $this->phone_voiceapp_m->get_many_by_many($array_condition);
            $list_data = array();
            foreach ($list_voice_app as $item) {
                $obj = new stdClass();
                $obj->key = $item->id;
                $obj->label = $item->name;
                $list_data[] = $obj;
            }
            echo json_encode($list_data);
            return;
        }
        $list_data = array();
        echo json_encode($list_data);
        return;
    }

    /**
     * Change call setting
     */
    public function change_call_setting_phone_users() {
        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('customer_id');
        $phone_user_id = $this->input->get_post('phone_user_id');
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();

        if (!empty($phone_user_id)) {
            $account_id = APContext::getSubAccountId($parent_customer_id);
            $sonetel_user = $this->sonetel->get_user($account_id, $phone_user_id);
        } else {
            APContext::setSessionValue(APConstants::SESSION_PHONE_INCOMMING_SETTING_USER_DATA, null);
            $sonetel_user = null;
        }

        // If this is post method
        if ($this->input->post()) {
            $first_action = $this->input->post('first_action');
            $first_to = $this->input->post('first_to');
            $first_id_other = $this->input->post('first_id_other');
            $first_id_list = $this->input->post('first_id_list');
            $first_show = $this->input->post('first_show');
            $ringtime = $this->input->post('ringtime');

            $second_action = $this->input->post('second_action');
            $second_to = $this->input->post('second_to');
            $second_id_other = $this->input->post('second_id_other');
            $second_id_list = $this->input->post('second_id_list');

            $incoming = array(
                "first_action" => array(
                    "action" => $first_action,
                    "to" => $first_to
                ),
                "second_action" => array(
                    "action" => $second_action,
                    "to" => $second_to
                )
            );
            if (!empty($first_show)) {
                $incoming['first_action']['show'] = $first_show;
            }
            if (!empty($ringtime)) {
                $incoming['first_action']['ring_time'] = $ringtime;
            }
            if (!empty($first_id_other)) {
                $incoming['first_action']['id'] = $first_id_other;
            }
            if (!empty($first_id_list)) {
                $incoming['first_action']['id'] = $first_id_list;
            }
            if (!empty($second_id_other)) {
                $incoming['second_action']['id'] = $second_id_other;
            }
            if (!empty($second_id_list)) {
                $incoming['second_action']['id'] = $second_id_list;
            }

            // For add new case the phone_id submit is id of clevvermail database
            if (empty($customer_id)) {
                if ($incoming['first_action']['to'] == 'phone') {
                    // Get phone_id based on current $incomming_setting->first_action->id (this is id in database)
                    $db_phone_id = $incoming['first_action']['id'];
                    $selected_phone = $this->phone_m->get($db_phone_id);
                    if (!empty($selected_phone)) {
                        if (empty($selected_phone->phone_id)) {
                            $phone_type = $selected_phone->phone_type;
                            $phone_name = $selected_phone->phone_name;
                            $phone_number = $selected_phone->phone_number;

                            if (empty($selected_phone->phone_id)) {
                                // Call sonetel to register this phone number to given user
                                $phone_id = $this->sonetel->add_phones($account_id, $phone_user_id, $phone_name, $phone_type, $phone_number);
                                $update_data = array();
                                $update_data ['phone_id'] = $phone_id;
                                $this->phone_m->update_by_many(array(
                                    "id" => $db_phone_id,
                                    "parent_customer_id" => $parent_customer_id
                                ), $update_data);
                            }
                            $incoming['first_action']['id'] = $phone_id;
                        } else {
                            $incoming['first_action']['id'] = $selected_phone->phone_id;
                        }
                    }
                }
            }

            // If already exist user in this case (edit)
            if (!empty($phone_user_id)) {
                // Call api
                $this->sonetel->update_user_call_incomming($account_id, $phone_user_id, $incoming);
            } else {
                // Save to session
                APContext::setSessionValue(APConstants::SESSION_PHONE_INCOMMING_SETTING_USER_DATA, $incoming);
            }

            $message = lang('users.message.change_incomming_success');
            $this->success_output($message);
            return;
        }

        // Load sonetel user
        $this->template->set('customer_id', $customer_id);
        $this->template->set('sonetel_user', $sonetel_user)->set('phone_user_id', $phone_user_id);
        $this->template->build('users/phone_users/change_call_setting');
    }

    /**
     * Change call setting
     */
    public function change_outgoing() {
        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('customer_id');
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $phone_user_id = $this->input->get_post('phone_user_id');
        // Edit case
        if (!empty($phone_user_id)) {
            $account_id = APContext::getSubAccountId($parent_customer_id);
            $sonetel_user = $this->sonetel->get_user($account_id, $phone_user_id);
        }
        // Add case
        else {
            APContext::setSessionValue(APConstants::SESSION_PHONE_OUTGOING_SETTING_USER_DATA, null);
            $sonetel_user = null;
        }

        // If this is post method
        if ($this->input->post()) {
            $show = $this->input->post('show');
            $phone_number = $this->input->post('phone_number');
            if ($show == 'other') {
                $show = $phone_number;
            }

            $outgoing = array(
                "show" => $show
            );

            // Call api
            if (!empty($phone_user_id)) {
                $this->sonetel->update_user_outgoing($account_id, $phone_user_id, $outgoing);
            } else {
                APContext::setSessionValue(APConstants::SESSION_PHONE_OUTGOING_SETTING_USER_DATA, $outgoing);
            }
            $message = lang('users.message.change_outgoing_success');
            $this->success_output($message);
            return;
        }

        // Get list phone number of this user
        $list_user_phonenumber = $this->phone_number_m->get_many_by_many(array(
            "parent_customer_id" => $parent_customer_id,
            "customer_id is null" => null,
            'phone_user_id is null' => null
        ));
        $this->template->set('list_user_phonenumber', $list_user_phonenumber);
        $this->template->set('customer_id', $customer_id);

        // Load sonetel user
        $this->template->set('sonetel_user', $sonetel_user)->set('phone_user_id', $phone_user_id);
        $this->template->build('users/phone_users/change_outgoing');
    }

    /**
     * Add Phone Number
     */
    public function assign_phone_number() {
        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('customer_id');
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();

        // If this is post method
        if ($this->input->post()) {
            $this->form_validation->set_rules($this->validation_rules_assign_phone_number);
            if ($this->form_validation->run()) {
                $phone_number = $this->input->post('phone_number');

                // If edit case
                if (!empty($customer_id)) {
                    $customer = CustomerUtils::getCustomerByID($customer_id);
                    $new_user_name = $customer->user_name;
                    if ($new_user_name == $customer->email) {
                        $new_user_name = APUtils::generateRandom(30);
                    }
                    $password = APUtils::generatePassword();
                    account_api::add_phone_user($parent_customer_id, $customer_id, $new_user_name, $customer->email, $password['raw_pass']);

                    $user = $this->customer_user_m->get_by_many(array(
                        "parent_customer_id" => $parent_customer_id,
                        "customer_id" => $customer_id
                    ));
                    account_api::assign_phonenumber_byuser($parent_customer_id, $customer_id, $phone_number);

                    $message = sprintf(lang('users.message.edit_success'), $user->user_name);
                    $this->success_output($message);
                    return;
                }
                // If add case
                else {
                    // add postbox into session.
                    $phone_number_list_data = APContext::getSessionValue(APConstants::SESSION_PHONENUMBER_USER_DATA);
                    if($phone_number_list_data == null){
                        $phone_number_list_data = array();
                    }
                    if(!in_array($phone_number, $phone_number_list_data)){
                        $phone_number_list_data[] = $phone_number;
                    }
                    APContext::setSessionValue(APConstants::SESSION_PHONENUMBER_USER_DATA, $phone_number_list_data);

                    $message = lang('users.message.add_phone_number_success');
                    $this->success_output($message);
                    return;
                }
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Get list phone number of this user
        $list_customer_avail_phonenumber = $this->phone_number_m->get_list_avail_phonenumber_by_customer($parent_customer_id);
        foreach ($list_customer_avail_phonenumber as $phonenumber) {
            $phonenumber->phone_number_label = $phonenumber->phone_number.'-'.$phonenumber->area_name.'-'.$phonenumber->country_name;
        }
        $this->template->set('list_customer_avail_phonenumber', $list_customer_avail_phonenumber);
        $this->template->set('customer_id', $customer_id);

        // Load sonetel user
        $this->template->build('users/phone_users/assign_phone_number');
    }

    /**
     * Delete assigned Phone Number
     */
    public function delete_assign_phone_number() {
        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('customer_id');
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $phone_number = $this->input->get_post('phone_number');
        $number_id = $this->input->get_post('number_id');

        // Edit case (already exist customer id)
        if (!empty($customer_id)) {

            $user = $this->customer_user_m->get_by_many(array(
                "parent_customer_id" => $parent_customer_id,
                "customer_id" => $customer_id
            ));
            $phone_user = $this->phone_customer_user_m->get_by_many(array(
                "parent_customer_id" => $parent_customer_id,
                "customer_id" => $customer_id
            ));
            $account_id = APContext::getSubAccountId($parent_customer_id);
            $phone_user_id = $phone_user->phone_user_id;

            // If this is post method
            $update_data = array();
            $update_data ['customer_id'] = null;
            $update_data ['phone_user_id'] = null;
            $update_data ['modified_date'] = now();
            $this->phone_number_m->update_by_many(array(
                "phone_number" => $phone_number,
                "parent_customer_id" => $parent_customer_id,
                "customer_id" => $customer_id
            ), $update_data);

            // Call sonetel to register this phone number to given
            $this->sonetel->delete_assign_phone_number($account_id, $phone_number, $phone_user_id, $number_id);
        }
        // Add new case
        else {
            // add postbox into session.
            $phone_number_list_data = APContext::getSessionValue(APConstants::SESSION_PHONENUMBER_USER_DATA);
            if($phone_number_list_data != null && count($phone_number_list_data) > 0){
                $new_phone_number_list_data = array();
                foreach ($phone_number_list_data as $item_phone_number) {
                    if ($item_phone_number != $phone_number) {
                        $new_phone_number_list_data[] = $item_phone_number;
                    }
                }
                APContext::setSessionValue(APConstants::SESSION_PHONENUMBER_USER_DATA, $new_phone_number_list_data);
            }
        }
        $message = lang('users.message.delete_assign_phone_number_success');
        $this->success_output($message);
    }

    /**
     * Delete assigned Phones
     */
    public function delete_assign_phones() {
        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('customer_id');
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $phone_id = $this->input->get_post('phone_id');

        // Edit case (already exist customer id)
        if (!empty($customer_id)) {
            /**
            $user = $this->customer_user_m->get_by_many(array(
                "parent_customer_id" => $parent_customer_id,
                "customer_id" => $customer_id
            ));
            $phone_user = $this->phone_customer_user_m->get_by_many(array(
                "parent_customer_id" => $parent_customer_id,
                "customer_id" => $customer_id
            ));
            $account_id = APContext::getSubAccountId($parent_customer_id);
            $phone_user_id = $phone_user->phone_user_id;
            */
            $update_data = array();
            $update_data ['customer_id'] = null;
            $update_data ['modified_date'] = date('Y-m-d H:i:s');
            $update_data ['phone_id'] = null;
            ci()->phone_m->update_by_many(array(
                "id" => $phone_id,
                "parent_customer_id" => $parent_customer_id
            ), $update_data);
        }
        // Add new case
        else {
            // Delete phone id from session
            $phone_id_list_data = APContext::getSessionValue(APConstants::SESSION_PHONES_USER_DATA);
            if($phone_id_list_data != null && count($phone_id_list_data) > 0){
                $new_phone_id_list_data = array();
                foreach ($phone_id_list_data as $item_phone_id) {
                    if ($item_phone_id != $phone_id) {
                        $new_phone_id_list_data[] = $item_phone_id;
                    }
                }
                APContext::setSessionValue(APConstants::SESSION_PHONES_USER_DATA, $new_phone_id_list_data);
            }
        }
        $message = lang('users.message.delete_assign_phones_success');
        $this->success_output($message);
    }

    /**
     * Add Phone Number
     */
    public function assign_phones() {
        $this->template->set_layout(FALSE);
        $customer_id = $this->input->get_post('customer_id');
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();

        // If this is post method
        if ($this->input->post()) {
            $this->form_validation->set_rules($this->validation_rules_assign_phones);
            if ($this->form_validation->run()) {
                $id = $this->input->post('phone_id');

                // If this is edit case
                if (!empty($customer_id)) {
                    $customer = CustomerUtils::getCustomerByID($customer_id);
                    $new_user_name = $customer->user_name;
                    if ($new_user_name == $customer->email) {
                        $new_user_name = APUtils::generateRandom(30);
                    }
                    $password = APUtils::generatePassword();
                    account_api::add_phone_user($parent_customer_id, $customer_id, $new_user_name, $customer->email, $password['raw_pass']);

                    $user = $this->customer_user_m->get_by_many(array(
                        "parent_customer_id" => $parent_customer_id,
                        "customer_id" => $customer_id
                    ));

                    // Call method to assgin
                    account_api::assign_phones_byuser($parent_customer_id, $customer_id, $id);

                    $message = sprintf(lang('users.message.edit_success'), $user->user_name);
                    $this->success_output($message);
                    return;
                } else {
                    // add postbox into session.
                    $phone_id_list_data = APContext::getSessionValue(APConstants::SESSION_PHONES_USER_DATA);
                    if(empty($phone_id_list_data)){
                        $phone_id_list_data = array();
                    }
                    if(!in_array($id, $phone_id_list_data)){
                        $phone_id_list_data[] = $id;
                    }
                    APContext::setSessionValue(APConstants::SESSION_PHONES_USER_DATA, $phone_id_list_data);

                    $message = lang('users.message.assign_phones_success');
                    $this->success_output($message);
                    return;
                }
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Get list phone number of this user
        $list_customer_avail_phones = $this->phone_m->get_many_by_many(array(
            "parent_customer_id" => $parent_customer_id,
            "phone_type" => 'regular',
            "customer_id is null" => null
        ));
        $this->template->set('list_customer_avail_phones', $list_customer_avail_phones);
        $this->template->set('customer_id', $customer_id);

        // Load sonetel user
        $this->template->build('users/phone_users/assign_phones');
    }

    /**
     * Load list of object to binding with the target of voice app.
     *
     * menu_digit_action (myphones | sipphone | play_prompt | connect_app | disconnect)
     */
    public function load_callsetting_to_target() {
        $list_data = array();
        $action = $this->input->get_post('action');
        if ($action == 'ring') {
            $obj = new stdClass();
            $obj->key = 'myphones';
            $obj->label = 'myphones';
            $list_data[] = $obj;

            $obj = new stdClass();
            $obj->key = 'sipphones';
            $obj->label = 'sipphones';
            $list_data[] = $obj;

            $obj = new stdClass();
            $obj->key = 'phone';
            $obj->label = 'phone';
            $list_data[] = $obj;
        } else if ($action == 'forward') {
            $obj = new stdClass();
            $obj->key = 'voicemail';
            $obj->label = 'voicemail';
            $list_data[] = $obj;

            $obj = new stdClass();
            $obj->key = 'user';
            $obj->label = 'user';
            $list_data[] = $obj;

            $obj = new stdClass();
            $obj->key = 'phnum';
            $obj->label = 'phnum';
            $list_data[] = $obj;

            $obj = new stdClass();
            $obj->key = 'voiceapp';
            $obj->label = 'voiceapp';
            $list_data[] = $obj;

            $obj = new stdClass();
            $obj->key = 'sip';
            $obj->label = 'sip';
            $list_data[] = $obj;
        } else if ($action == 'disconnect') {
            $obj = new stdClass();
            $obj->key = '';
            $obj->label = '-NA-';
            $list_data[] = $obj;
        }

        echo json_encode($list_data);
        return;
    }

    /**
     * Load list of object to binding with the target of voice app.
     *
     * menu_digit_action (myphones | sipphone | play_prompt | connect_app | disconnect)
     */
    public function load_callsetting_id_target() {
        $list_data = array();
        $action = $this->input->get_post('action');
        $to = $this->input->get_post('to');
        $customer_id = $this->input->get_post('customer_id');
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $array_condition = array();

        // Load list of user
        if ($action == 'forward' && $to == 'user') {
            $array_condition ["customers.parent_customer_id"] = $parent_customer_id;
            $list_user_phone = $this->phone_customer_user_m->get_list_phone_user($array_condition);
            $list_data = array();
            foreach ($list_user_phone as $item) {
                $obj = new stdClass();
                $obj->key = $item->phone_user_id;
                $obj->label = $item->user_name;
                $list_data[] = $obj;
            }
            echo json_encode($list_data);
            return;
        } else if ($action == 'forward' && $to == 'voiceapp') {
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
        } else if ($action == 'ring' && $to == 'phone') {
            $list_data = array();
            if (!empty($customer_id)) {
                // Get list phone number of this user
                $list_customer_avail_phones = $this->phone_m->get_many_by_many(array(
                    "parent_customer_id" => $parent_customer_id,
                    "customer_id" => $customer_id,
                    "phone_id is not null" => null
                ));
                foreach ($list_customer_avail_phones as $item) {
                    $obj = new stdClass();
                    $obj->key = $item->phone_id;
                    $obj->label = $item->phone_name;
                    $list_data[] = $obj;
                }
            } else {
                // if this is add new case
                $list_customer_avail_phones = $this->phone_m->get_many_by_many(array(
                    "parent_customer_id" => $parent_customer_id,
                    "phone_type" => 'regular',
                    "customer_id is null" => null,
                    "phone_id is null" => null
                ));
                foreach ($list_customer_avail_phones as $item) {
                    $obj = new stdClass();
                    $obj->key = $item->id;
                    $obj->label = $item->phone_name;
                    $list_data[] = $obj;
                }
            }

            echo json_encode($list_data);
            return;
        }
        echo json_encode($list_data);
        return;
    }

    /**
     * Load all area code by country code (SGP|USA)
     *
     */
    public function load_area_code_target() {
        $country_code = $this->input->get_post('country_code');

        $list_data = phones_api::get_phone_area_code($country_code);

        echo json_encode($list_data);
        return;
    }

    /**
     * update invoice address of user enterprise.
     *
     * @param type $customer_id
     * @param type $user_id
     * @param type $customer_type
     */
    private function update_invoice_address_user_enterprise($parent_customer_id, $customer_id, $customer_type){
        // get customer_type from setting.
        //$customer_type_list = Settings::get_list(APConstants::CUSTOMER_TYPE_CODE);

        // If employee, the same invoicing address applies as from the enterprise customer.
        // If individual, we need: street, postcode, city, region, country
        // If company, add company name
        if($customer_type == APConstants::CUSTOMER_TYPE_EMPLOYEE){
            // Gets customer invoice address.
            $customer_address = $this->customers_address_m->get_by('customer_id', $parent_customer_id);

            $user_address = $this->customers_address_m->get_by('customer_id', $customer_id);
            $data = array(
                //'shipment_address_name' => $customer_address['shipment_address_name'],
                //'shipment_company' => $customer_address['shipment_company'],
                //'shipment_street' => $customer_address['shipment_street'],
                //'shipment_postcode' => $customer_address['shipment_postcode'],
                //'shipment_city' => $customer_address['shipment_city'],
                //'shipment_region' => $customer_address['shipment_region'],
                //'shipment_country' => $customer_address['shipment_country'],
                'invoicing_address_name' => !empty($customer_address) ? $customer_address->invoicing_address_name : "",
                'invoicing_company' => !empty($customer_address) ? $customer_address->invoicing_company: "",
                'invoicing_street' => !empty($customer_address) ? $customer_address->invoicing_street: "",
                'invoicing_postcode' => !empty($customer_address) ? $customer_address->invoicing_postcode: "",
                'invoicing_city' => !empty($customer_address) ? $customer_address->invoicing_city: "",
                'invoicing_region' => !empty($customer_address) ? $customer_address->invoicing_region: "",
                'invoicing_country' => !empty($customer_address) ? $customer_address->invoicing_country: "",
                'is_bussiness' => !empty($customer_address) ? $customer_address->is_bussiness: "0",
                //'vat_number' => $customer_address->vat_number,
                'eu_member_flag' => !empty($customer_address) ? $customer_address->eu_member_flag: "0",
                //'shipment_phone_number' => $customer_address->shipment_phone_number,
                //'invoicing_phone_number' => $customer_address->invoicing_phone_number,
                'invoice_address_verification_flag' => !empty($customer_address) ? $customer_address->invoice_address_verification_flag: "0"
            );
            if(empty($user_address)){
                $data['customer_id'] = $customer_id;
                $this->customers_address_m->insert($data);
            }else{
                $this->customers_address_m->update_by_many(array(
                    "customer_id" => $customer_id
                ), $data);
            }
        } else if($customer_type == APConstants::CUSTOMER_TYPE_INDIVIDUAL){
            // do nothing.
        } else if ($customer_type == APConstants::CUSTOMER_TYPE_COMPANY){
            // do nothing.
        }
        return;
    }

    /**
     * Enterprise user change postbox location.
     *
     */
    public function change_postbox_location() {
        $this->template->set_layout(FALSE);
        ci()->load->library('addresses/addresses_api');
        ci()->load->library('account/account_api');
        ci()->load->model("mailbox/postbox_m");
        ci()->load->model("scans/envelope_m");
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $postbox_id = $this->input->get_post("postbox_id");
        $postbox_user_id = $this->input->get_post("postbox_user_id");

        // Gets list availble postbox of
        $locations = addresses_api::getLocationPublic();
        if (!empty($postbox_id)) {
            $postbox =  account_api::get_postbox_by_postbox_id($parent_customer_id, $postbox_id);
        } else {
            $postbox =  account_api::get_postbox_by_postboxuser_id($parent_customer_id, $postbox_user_id);
        }

        $location = addresses_api::getLocationByID($postbox->location_available_id);
        $postbox->current_location = $location->location_name;
        $customer = CustomerUtils::getCustomerByID($postbox->customer_id);

        // Submit data
        if ($this->input->post()) {
            $this->form_validation->set_rules($this->change_postbox_location_validation_rules);

            if ($this->form_validation->run()) {
                $location_id = $this->input->get_post("location_id");
                $name = $this->input->post('name');
                $company = $this->input->post('company');
                
                if(empty($name) && empty($company)){
                    $this->error_output("name or company field is required.");
                    return;
                }
                
                ci()->postbox_m->update_by_many(array(
                    'postbox_id' => $postbox_id
                ), array(
                    'location_available_id' => $location_id,
                    "name" => $name,
                    "company" => $company
                ));
                
                // start case verification
                CaseUtils::start_case_verification_by_postbox(false, $customer, $postbox);

                // Count all envelope id
                $number_item = ci()->envelope_m->count_by_many(array(
                    "to_customer_id" => $postbox->customer_id,
                    "postbox_id" => $postbox->postbox_id,
                    "RIGHT(envelope_code,4) <> '_000'" => null
                ));
                if ($number_item == 0) {
                    // Change postbox code
                    $new_location = addresses_api::getLocationByID($location_id);
                    $short_location_name = strtoupper(substr($new_location->location_name, 0, 3));
                    $box_count = ci()->postbox_m->count_by_customer_cityname($customer->customer_code, $short_location_name) + 1;

                    // Get customer code and update again
                    $postbox_code = $customer->customer_code . '_' . $short_location_name . sprintf('%1$02d', $box_count);
                    ci()->postbox_m->update_by_many(array(
                        "postbox_id" => $postbox->postbox_id,
                    ), array(
                        'postbox_code' => $postbox_code
                    ));
                }
                else if ($postbox->location_available_id != $location_id) {
                    // Trash all current item of old postbox
                    $list_envelope_ids = array();
                    $all_items_old_postbox = ci()->envelope_m->get_many_by_many(array(
                        "to_customer_id" => $postbox->customer_id,
                        "postbox_id" => $postbox->postbox_id,
                        "RIGHT(envelope_code,4) <> '_000'" => null
                    ));
                    foreach ($all_items_old_postbox as $item) {
                        $list_envelope_ids[] = $item->id;
                    }
                    EnvelopeUtils::trashEnvelopes($list_envelope_ids, $customer);
                }


                $message = lang('users.message.change_postbox_locations');
                $this->success_output($message);
                return;

            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }

        // Display the current page
        $this->template->set('postbox', $postbox);
        $this->template->set('postbox_user_id', $postbox_user_id);
        $this->template->set('customer', $customer);
        $this->template->set('locations', $locations)->build('users/change_postbox_location');
    }
}
