<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Phones extends AccountSetting_Controller {

    private $phone_validation_rules =array(
        array(
            'field' => 'phone_name',
            'label' => 'lang:phone_name',
            'rules' => 'required|max_length[250]'
        ),
        array(
            'field' => 'phone_type',
            'label' => 'lang:phone_type',
            'rules' => 'required|max_length[10]'
        ),
        array(
            'field' => 'phone_number',
            'label' => 'lang:phone_number',
            'rules' => 'max_length[40]|callback__check_phone'
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
        $this->load->model('phones/phone_number_m');
        $this->load->library('addresses/addresses_api');
        $this->load->library('phones/phones_api');
        $this->lang->load('account');
        $this->lang->load('user');
    }

    /**
     * Index Page for this controller. Maps to the following URL http://example.com/index.php/welcome - or -
     * http://example.com/index.php/welcome/index - or - Since this controller is set as the default controller in config/routes.php, it's displayed
     * at http://example.com/ So any other public methods not prefixed with an underscore will map to /index.php/welcome/<method_name>
     *
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index() {
        $this->template->build('phones/index');
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
        if(!empty($customer_id) && !APContext::isAdminCustomerUser()){
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
            $query_result = $this->phone_m->get_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

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
                    $row->phone_name,
                    $row->phone_type,
                    $row->phone_number,
                    APUtils::convert_timestamp_to_date($row->created_date),
                    $row->id,
                );
                $i++;
            }
            echo json_encode($response);
        }
    }
    
    /**
     * add phone.
     */
    public function add(){
        $this->template->set_layout(FALSE);
        $phone = new stdClass();
        $phone->id = '';
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $customer_id = APContext::getCustomerCodeLoggedIn();
                
        if($_POST){
            $this->form_validation->set_rules($this->phone_validation_rules);
            if ($this->form_validation->run()) {
                $phone_name = $this->input->post('phone_name');
                $phone_type = $this->input->post('phone_type');
                $phone_number = $this->input->post('phone_number');
                $phone_number2 = $this->input->post('phone_number2');
                
                $data = array();
                $data['phone_name'] = $phone_name;
                $data['phone_type'] = $phone_type;
                $data['customer_id'] = null;
                $data['parent_customer_id'] = $parent_customer_id;
                if($phone_type == 'IP'){
                    $data['phone_number'] = $phone_number2;
                }else{
                    $data['phone_number'] = $phone_number;
                }
                $data['created_date'] = now();
                
                if (!APContext::isAdminCustomerUser()) {
                    $data['customer_id'] = $customer_id;
                }
                
                // insert new phone
                $phone_id = $this->phone_m->insert($data);
                
                // Assign this phones for them self
                if (!APContext::isAdminCustomerUser()) {
                    try {
                        $this->load->library('account/account_api');
                        account_api::assign_phones_byuser($parent_customer_id, $customer_id, $phone_id);
                    } catch (BusinessException $e) {
                        $message = $e->getMessage();
                        $this->error_output($message);
                        return;
                    }
                }
                
                $this->success_output(lang('users.message.add_phone_number_success'));
            }else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
            }
            return;
        }
        
        // Loop through each validation rule
        foreach ($this->phone_validation_rules as $rule) {
            $phone->{$rule['field']} = set_value($rule['field']);
        }
        
        // get list number
        $condition = array("parent_customer_id" => $parent_customer_id);
        if(!empty($customer_id) && $customer_id != $parent_customer_id){
            $condition['customer_id'] = $customer_id;
        }
        
        $list_number_alias = $this->phone_m->get_many_by_many($condition);
        $list_number_alias_id = array();
        foreach($list_number_alias as $number){
            $list_number_alias_id[] = $number->phone_number;
        }
        if(!empty($list_number_alias_id)){
            $condition["phone_number NOT IN ('".  implode("','", $list_number_alias_id)."')"] = null;
        }
        
        $list_numbers = $this->phone_number_m->get_many_by_many($condition);
        
        $this->template->set("list_numbers", $list_numbers);
        $this->template->set("phone", $phone);
        $this->template->build("phones/form");
    }
    
    /*
     * delete phone
     */
    public function delete($id = '') {
        $this->template->set_layout(FALSE);
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        phones_api::deletePhonesById($id, $parent_customer_id);
        $this->success_output("");
    }
    
    /**
     * check phone number.
     * @return boolean
     */
    public function _check_phone(){
        $number = $this->input->get_post('phone_number');
        $number2 = $this->input->get_post('phone_number2');
        $parent_customer_id = APContext::getParentCustomerCodeLoggedIn();
        $condition = array();
        $condition['parent_customer_id'] = $parent_customer_id;
        
        $phone_type = $this->input->post('phone_type');
        if($phone_type == 'IP'){
            $condition['phone_number'] = $number2;
            if(empty($number2)){
                $this->form_validation->set_message('_check_phone', 'The phone number is required. Please enter the phone number.');
                return false;
            }
        }else{
            $condition['phone_number'] = $number;
            if(empty($number)){
                $this->form_validation->set_message('_check_phone', 'The phone number is required. Please select phone number from the list.');
                return false;
            }
        }
        
        $phone = $this->phone_m->get_by_many($condition);
        if(!empty($phone)){
            $this->form_validation->set_message('_check_phone', lang('users.message.phone_number_exist'));
            return false;
        }
        
        return true;
    }
}
