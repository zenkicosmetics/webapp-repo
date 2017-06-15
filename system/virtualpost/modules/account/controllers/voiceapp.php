<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Voiceapp extends AccountSetting_Controller {

    private $voiceapp_validation_rules = array(
        array(
            'field' => 'app_type',
            'label' => 'lang:app_type',
            'rules' => 'required|max_length[10]'
        ),
        array(
            'field' => 'name',
            'label' => 'lang:name',
            'rules' => 'required|max_length[250]'
        )
    );
    
    private $voiceapp_validation_rules_update_mailbox = array(
        array(
            'field' => 'name',
            'label' => 'lang:name',
            'rules' => 'required|max_length[250]'
        ),
        array(
            'field' => 'voice',
            'label' => 'lang:voice',
            'rules' => 'required'
        ),
        array(
            'field' => 'deliver_to',
            'label' => 'lang:deliver_to',
            'rules' => 'required'
        ),
        array(
            'field' => 'deliver_to_details_01',
            'label' => 'lang:deliver_to_details',
            'rules' => ''
        ),
        array(
            'field' => 'deliver_to_details_02',
            'label' => 'lang:deliver_to_details',
            'rules' => ''
        )
    );
    
    private $voiceapp_validation_rules_update_ivr = array(
        array(
            'field' => 'name',
            'label' => 'lang:name',
            'rules' => 'required|max_length[250]'
        ),
        array(
            'field' => 'voice',
            'label' => 'lang:voice',
            'rules' => 'required'
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
        $this->load->model('phones/phone_voiceapp_m');
        $this->load->model('settings/countries_m');

        $this->load->library('customers/customers_api');
        $this->load->library('addresses/addresses_api');

        $this->lang->load('account');
        $this->lang->load('user');

        $this->load->library('phones/sonetel');
        $this->load->library('phones/phones_api');
    }

    /**
     * Index Page for this controller. Maps to the following URL http://example.com/index.php/welcome - or -
     * http://example.com/index.php/welcome/index - or - Since this controller is set as the default controller in config/routes.php, it's displayed
     * at http://example.com/ So any other public methods not prefixed with an underscore will map to /index.php/welcome/<method_name>
     *
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index() {
        $this->template->build('voiceapp/index');
    }

    public function search() {
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
            $query_result = $this->phone_voiceapp_m->get_voiceapp_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

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
                    $row->app_type,
                    $row->name,
                    $row->use_flag,
                    $row->id,
                );
                $i++;
            }
            echo json_encode($response);
        }
    }

    public function add() {
        $this->load->library('addresses/addresses_api');
        $this->template->set_layout(FALSE);
        $voiceapp = new stdClass();
        $voiceapp->id = '';

        if ($this->input->post()) {
            $this->form_validation->set_rules($this->voiceapp_validation_rules);
            if ($this->form_validation->run()) {
                $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
                $app_type = $this->input->post('app_type');
                $name = $this->input->post('name');

                // Check exist sub account of this customer
                $phone_sub_account = $this->phone_customer_subaccount_m->get_by('customer_id', $parent_customer_id);
                if (empty($phone_sub_account)) {
                    $message = lang('users.message.add_voiceapp_fail_01');
                    $this->error_output($message);
                    return;
                }
                $account_id = $phone_sub_account->account_id;
                // Create new user
                try {
                    $response = $this->sonetel->create_new_voiceapp($account_id, $name, $app_type, array());
                    // Validate response
                    if ($response->status == 'failed') {
                        $message = $response->response->code .':'.$response->response->detail;
                        $this->error_output($message);
                        return;
                    }
                    
                    // Get app id from response
                    $app_id = $response->response->app_id;
                } catch (Exception $e) {
                    $message = lang('users.message.add_voiceapp_fail_02');
                    $this->error_output($message);
                    return;
                }
                
                $data = array(
                    "sub_account_id" => $account_id,
                    "parent_customer_id" => $parent_customer_id,
                    "name" => $name,
                    "app_id" => $app_id,
                    "app_type" => $app_type,
                    "use_flag" => APConstants::OFF_FLAG,
                    "created_date" => time()
                );
                
                // save voice app.
                $this->phone_voiceapp_m->insert($data);

                $message = lang('users.message.add_voiceapp_success');
                $this->success_output($message);
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
            }
            return;
        }

        // Loop through each validation rule
        foreach ($this->voiceapp_validation_rules as $rule) {
            $voiceapp->{$rule['field']} = set_value($rule['field']);
        }

        $this->template->set("voiceapp", $voiceapp);
        $this->template->set("action_type", "add");
        $this->template->build("voiceapp/form");
    }

    /**
     * Edit voice app setting.
     * 
     * @return type
     */
    public function edit_setting($id) {
        $this->load->library('addresses/addresses_api');
        $this->template->set_layout(FALSE);
        
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $phone_user_voiceapp = $this->phone_voiceapp_m->get_by_many(array(
            'parent_customer_id' => $parent_customer_id,
            'id' => $id
        ));
        if (empty($phone_user_voiceapp)) {
            $message = lang('users.message.get_voiceapp_fail_01');
            $this->error_output($message);
            return;
        }
        
        // Check exist sub account of this customer
        $phone_sub_account = $this->phone_customer_subaccount_m->get_by('customer_id', $parent_customer_id);
        if (empty($phone_sub_account)) {
            $message = lang('users.message.get_voiceapp_fail_02');
            $this->error_output($message);
            return;
        }
        $account_id = APContext::getSubAccountId($parent_customer_id);
        $voice_app_id = $phone_user_voiceapp->app_id;
        // Get voice app setting detail
        try {
            $result = $this->sonetel->get_voiceapp_detail($account_id, $voice_app_id);
            $voiceapp_detail = $result->response;
        } catch (Exception $e) {
            $message = lang('users.message.get_voiceapp_fail_03');
            $this->error_output($message);
            return;
        }
        
        // POST method
        if ($this->input->post()) {
            $name = $this->input->post('name');
            //$shortcode = $this->input->post('shortcode');
            $voice = $this->input->post('voice');
            //$sip_address = $this->input->post('sip_address');
            $update_other_field = array(
                'voice' => $voice
            );
            
            // Mailbox type
            if ($phone_user_voiceapp->app_type == 'mailbox') {
                $deliver_to =  $this->input->post('deliver_to');
                // Email type
                $deliver_to_details_01 =  $this->input->post('deliver_to_details_01');
                // User type
                $deliver_to_details_02 =  $this->input->post('deliver_to_details_02');
                
                $update_other_field['deliver_to'] = $deliver_to;
                if ($deliver_to == 'user') {
                    $update_other_field['deliver_to_details'] = $deliver_to_details_02;
                    $this->voiceapp_validation_rules_update_mailbox[4]['rules'] = 'required';
                } else if ($deliver_to == 'email') {
                    $update_other_field['deliver_to_details'] = $deliver_to_details_01;
                    $this->voiceapp_validation_rules_update_mailbox[3]['rules'] = 'required';
                }
                
                // Set rule
                $this->form_validation->set_rules($this->voiceapp_validation_rules_update_mailbox);
                if ($this->form_validation->run()) {
                    // Update database
                    $this->phone_voiceapp_m->update_by_many(array('id' => $id), array('name' => $name));
                    
                    // Update other field
                    $this->sonetel->update_voiceapp($account_id, $voice_app_id, $name, $update_other_field);
                } else {
                    $errors = $this->form_validation->error_json();
                    echo json_encode($errors);
                    return;
                }
            }
            // IVR type
            else if ($phone_user_voiceapp->app_type == 'ivr') {
                $action_play_welcome = $this->input->post('action_play_welcome');
                $action_get_extension = $this->input->post('action_get_extension');
                $action_play_menu = $this->input->post('action_play_menu');
                $update_other_field['play_menu'] = $action_play_menu == '1' ? 'yes': 'no';
                $update_other_field['play_welcome'] = $action_play_welcome == '1' ? 'yes': 'no';
                $update_other_field['get_extension'] = $action_get_extension == '1' ? 'yes': 'no';
                
                // Build menu
                $menu_digit_0 = $this->get_menu_digit(0);
                $menu_digit_1 = $this->get_menu_digit(1);
                $menu_digit_2 = $this->get_menu_digit(2);
                $menu_digit_3 = $this->get_menu_digit(3);
                $menu = array(
                    'digit_0' => $menu_digit_0,
                    'digit_1' => $menu_digit_1,
                    'digit_2' => $menu_digit_2,
                    'digit_3' => $menu_digit_3
                );
                $update_other_field['menu'] = $menu;
                
                $this->form_validation->set_rules($this->voiceapp_validation_rules_update_ivr);
                if ($this->form_validation->run()) {
                    // Update database
                    $this->phone_voiceapp_m->update_by_many(array('id' => $id), array('name' => $name));
                    
                    // Update other field
                    $this->sonetel->update_voiceapp($account_id, $voice_app_id, $name, $update_other_field);
                } else {
                    $errors = $this->form_validation->error_json();
                    echo json_encode($errors);
                    return;
                }
            } 
            
            $message = lang('users.message.update_voiceapp_success');
            $this->success_output($message);
            return;
        }
        
        $array_condition = array();
        $array_condition ["customers.parent_customer_id"] = $parent_customer_id;
        $list_user_phone = $this->phone_customer_user_m->get_list_phone_user($array_condition);
        
        $this->template->set("list_user_phone", $list_user_phone);
        $this->template->set("voiceapp", $phone_user_voiceapp);
        $this->template->set("voiceapp_detail", $voiceapp_detail);
        $this->template->set("action_type", "edit_setting");
        $this->template->build("voiceapp/form_setting");
    }
    
    /**
     * Get menu digit
     * @param type $index
     */
    private function get_menu_digit($index) {
        $menu_digit_index_action = $this->input->post('menu_digit_'.$index.'_action');
        $menu_digit_index_to = $this->input->post('menu_digit_'.$index.'_to');
        $menu_digit_index_to_other = $this->input->post('menu_digit_'.$index.'_to_other');
        $menu_digit = array();
        $menu_digit['action'] = '';
        $menu_digit['to'] = '';
        $menu_digit['id'] = '';
        if ($menu_digit_index_action == 'call_user') {
            $menu_digit['action'] = 'call';
            $menu_digit['to'] = 'user';
            $menu_digit['id'] = $menu_digit_index_to;
        } else if ($menu_digit_index_action == 'call_other') {
            $menu_digit['action'] = 'call';
            $menu_digit['to'] = 'other';
            $menu_digit['id'] = $menu_digit_index_to_other;
        } else if ($menu_digit_index_action == 'play_prompt') {
            $menu_digit['action'] = 'play';
            $menu_digit['to'] = 'prompt';
            $menu_digit['id'] = $menu_digit_index_to;
        } else if ($menu_digit_index_action == 'connect_app') {
            $menu_digit['action'] = 'connect';
            $menu_digit['to'] = 'app';
            $menu_digit['id'] = $menu_digit_index_to;
        } else if ($menu_digit_index_action == 'disconnect') {
            $menu_digit['action'] = 'disconnect';
            $menu_digit['to'] = '-NA-';
            $menu_digit['id'] = '-NA-';
        } 
        return $menu_digit;
    }
    
    /** Delete phone number
     * 
     */
    public function delete($id = '') {
        $this->template->set_layout(FALSE);
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $delete_flag = phones_api::delete_voiceapp($parent_customer_id, $id);

        if (!$delete_flag) {
            $message = lang('users.message.delete_voiceapp_fail_01');
            $this->error_output($message);
            return;
        }
        $this->success_output("");
    }
    
    
}
