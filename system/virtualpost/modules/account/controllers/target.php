<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Target extends AccountSetting_Controller {

    private $target_validation_rules = array(
        array(
            'field' => 'target_name',
            'label' => 'lang:target_name',
            'rules' => 'required|max_length[250]'
        ),
        array(
            'field' => 'target_type',
            'label' => 'lang:target_type',
            'rules' => 'required|max_length[20]'
        ),
        array(
            'field' => 'target_id',
            'label' => 'lang:target_id',
            'rules' => 'max_length[100]'
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
        $this->load->model('phones/phone_m');
        $this->load->model('phones/phone_target_m');
        $this->load->model('phones/phone_voiceapp_m');
        $this->load->model('phones/phone_number_m');

        $this->load->library('addresses/addresses_api');
        $this->load->library('phones/phones_api');

        $this->lang->load('account/user');
        $this->lang->load('phones/phones');
    }

    /**
     * Default access
     */
    public function index() {
        $this->template->build('target/index');
    }

    /**
     * search phone number.
     */
    public function search() {
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $customer_id = APContext::getCustomerCodeLoggedIn();

        // declare condition.
        $array_condition = array();
        $array_condition ["parent_customer_id"] = $parent_customer_id;

        // only show phone of user if normal user.
        if (!empty($customer_id) && !APContext::isAdminCustomerUser()) {
            $array_condition ["customer_id"] = $customer_id;
        }

        // If current request is ajax
        if ($this->is_ajax_request()) {
            // update limit into user_paging.
            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APConstants::NUMBER_RECORD_PER_PAGE_CODE;

            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging ['limit'] = $limit;

            // Call search method
            $query_result = $this->phone_target_m->get_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

            // Process output data
            $total = $query_result['total'];
            $rows = $query_result['data'];

            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);

            $i = 0;
            foreach ($rows as $row) {
                $target_id = $row->target_id;
                if ($row->target_type == 'voicemail') {
                    // Get target id
                    $voice_app = $this->phone_voiceapp_m->get($target_id);
                    if (!empty($voice_app)) {
                        $target_id = 'Voice App:' . $voice_app->app_id;
                    }
                }
                $response->rows [$i] ['id'] = $row->id;
                $response->rows [$i] ['cell'] = array(
                    $row->id,
                    $row->target_name,
                    lang('target_type_' . $row->target_type),
                    $target_id,
                    lang('users.label.use_flag_' . $row->use_flag),
                    APUtils::convert_timestamp_to_date($row->created_date),
                    $row->id,
                );
                $i++;
            }
            echo json_encode($response);
        }
    }

    /**
     * add new target.
     */
    public function add() {
        $this->template->set_layout(FALSE);
        $target = new stdClass();
        $target->id = '';
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $customer_id = APContext::getCustomerCodeLoggedIn();

        if ($_POST) {
            $this->form_validation->set_rules($this->target_validation_rules);
            if ($this->form_validation->run()) {
                $target_name = $this->input->post('target_name');
                $target_type = $this->input->post('target_type');
                $target_id = $this->input->post('target_id');

                $data = array();
                $data['target_name'] = $target_name;
                $data['target_type'] = $target_type;
                $data['target_id'] = $target_id;
                $data['use_flag'] = APConstants::OFF_FLAG;
                $data['customer_id'] = null;
                $data['parent_customer_id'] = $parent_customer_id;
                $data['created_date'] = now();

                if (!APContext::isAdminCustomerUser()) {
                    $data['customer_id'] = $customer_id;
                }

                // Create new voiceapp
                if ($target_type == 'voicemail') {
                    $local_file_path = APContext::getSessionValue('VOICEMAIL_LOCAL_FILE_PATH');
                    $addional_setting = array(
                        'voicemail_local_path' => $local_file_path
                    );
                    
                    $clevvermail_app_id = phones_api::create_new_voiceapp($parent_customer_id, $target_name, 'mailbox', $addional_setting);
                    $data['target_id'] = $clevvermail_app_id;
                }
                APContext::setSessionValue('VOICEMAIL_LOCAL_FILE_PATH', '');

                // insert new phone
                $cl_target_id = $this->phone_target_m->insert($data);
                if ($target_type == 'phone_number') {
                    // Insert data to phone table
                    $new_phone = array();
                    $new_phone['phone_name'] = $target_name;
                    $new_phone['phone_type'] = 'regular';
                    $new_phone['target_id'] = $cl_target_id;
                    $new_phone['phone_number'] = $target_id;
                    $new_phone['customer_id'] = null;
                    $new_phone['parent_customer_id'] = $parent_customer_id;
                    $new_phone['created_date'] = now();

                    if (!APContext::isAdminCustomerUser()) {
                        $new_phone['customer_id'] = $customer_id;
                    }
                
                    $this->phone_m->insert($new_phone);
                }

                $this->success_output(lang('users.message.add_target_success'));
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
            }
            return;
        }

        // Loop through each validation rule
        foreach ($this->target_validation_rules as $rule) {
            $target->{$rule['field']} = set_value($rule['field']);
        }

        $this->template->set("target", $target);
        $this->template->build("target/form");
    }

    /*
     * delete phone
     */

    public function delete($id = '') {
        $this->template->set_layout(FALSE);
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();

        $phone_target = $this->phone_target_m->get_by_many(array(
            'id' => $id,
            'parent_customer_id' => $parent_customer_id
        ));
        if (empty($phone_target)) {
            $this->error_output("This target did not exist in the system.");
        }

        // Delete in data in Sonetel
        if ($phone_target->target_type == 'voicemail') {
            phones_api::delete_voiceapp($parent_customer_id, $phone_target->target_id);
        }
        
        if ($phone_target->target_type == 'phone_number') {
            phones_api::delete_phone($parent_customer_id, $phone_target->id);
        }
        
        // Delete data in database
        $this->phone_target_m->delete_by_many(array(
            'id' => $id,
            'parent_customer_id' => $parent_customer_id
        ));

        $this->success_output("");
    }

    /**
     * check phone number.
     * @return boolean
     */
    public function _check_phone() {
        $number = $this->input->get_post('phone_number');
        $number2 = $this->input->get_post('phone_number2');
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $condition = array();
        $condition['parent_customer_id'] = $parent_customer_id;

        $phone_type = $this->input->post('phone_type');
        if ($phone_type == 'IP') {
            $condition['phone_number'] = $number2;
            if (empty($number2)) {
                $this->form_validation->set_message('_check_phone', 'The phone number is required. Please enter the phone number.');
                return false;
            }
        } else {
            $condition['phone_number'] = $number;
            if (empty($number)) {
                $this->form_validation->set_message('_check_phone', 'The phone number is required. Please select phone number from the list.');
                return false;
            }
        }

        $phone = $this->phone_m->get_by_many($condition);
        if (!empty($phone)) {
            $this->form_validation->set_message('_check_phone', lang('users.message.phone_number_exist'));
            return false;
        }

        return true;
    }

    public function upload_voicemail_file() {
        $voicemail_file_path = '';
        $customer_code = APContext::getCustomerCodeLoggedIn();
        if (!empty($_POST)) {
            $this->load->library('files/files');
            $input_file_client_name = $this->input->get_post('voicemail_file_path');
            
            $data_file_path = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE);
            $_image_path = $data_file_path . "voicemail/" . $customer_code . '/';
            switch ($input_file_client_name) {
                case "voicemail_file_path":
                    $voicemail_file_path = Files::upload_file($_image_path, 'upload_file_input');
                    $local_file_path = $voicemail_file_path['local_file_path'];
                    APContext::setSessionValue('VOICEMAIL_LOCAL_FILE_PATH', $local_file_path);
                    break;
            }
        }
        $this->success_output($voicemail_file_path);
        exit;
    }

}
