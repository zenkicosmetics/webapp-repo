<?php

if (! defined('BASEPATH'))
    exit('No direct script access allowed');
class admin extends Admin_Controller {
    
    /**
     * Validation for basic profile data. The rest of the validation is built by streams.
     * 
     * @var array
     */
    private $validation_rules_instance = array (
            array (
                    'field' => 'name',
                    'label' => 'lang:name',
                    'rules' => 'required|valid_name|max_length[255]|callback__check_name' 
            ) 
    );
    private $validation_rules_domain = array (
            array (
                    'field' => 'domain_type',
                    'label' => 'lang:domain_type',
                    'rules' => 'required' 
            ),
            array (
                    'field' => 'full_url',
                    'label' => 'lang:full_url',
                    'rules' => 'required' 
            ),
            array (
                    'field' => 'domain_name',
                    'label' => 'lang:domain_name',
                    'rules' => 'required|valid_name|max_length[1000]|callback__check_domain_name' 
            ) 
    );
    private $validation_rules_amazon = array (
            array (
                    'field' => 's3_type',
                    'label' => 'lang:s3_type',
                    'rules' => 'required' 
            ),
            array (
                    'field' => 's3_name',
                    'label' => 'lang:s3_name',
                    'rules' => 'required|valid_name|max_length[100]|callback__check_s3_name' 
            ) 
    );
    private $validation_rules_database = array (
            array (
                    'field' => 'database_type',
                    'label' => 'lang:database_type',
                    'rules' => 'required' 
            ),
            array (
                    'field' => 'database_name',
                    'label' => 'lang:database_name',
                    'rules' => 'required|valid_name|max_length[100]|callback__check_database_name' 
            ),
            array (
                    'field' => 'host_address',
                    'label' => 'lang:host_address',
                    'rules' => 'required' 
            ),
            array (
                    'field' => 'username',
                    'label' => 'lang:username',
                    'rules' => 'required' 
            ),
            array (
                    'field' => 'password',
                    'label' => 'lang:password',
                    'rules' => '' 
            ) 
    );
    
    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     */
    public function __construct() {
        parent::__construct();
        
        // load the theme_example view
        $this->load->model('instances/instance_m');
        $this->load->model('instances/instance_amazon_m');
        $this->load->model('instances/instance_database_m');
        $this->load->model('instances/instance_domain_m');
        
        $this->lang->load(array (
                'instances/instance' 
        ));
        $this->load->library('form_validation');
    }
    
    /**
     * Index
     */
    public function index() {
        // Get input condition
        $name = $this->input->get_post("name");
        $domain = $this->input->get_post("domain_name");
        
        $array_condition = array ();
        if (! empty($domain)) {
            $array_condition ['instance_domain.domain_name LIKE '] = '%' . $domain . '%';
        }
        if (! empty($name)) {
            $array_condition ['instances.name LIKE '] = '%' . $name . '%';
        }
        
        // If current request is ajax
        if ($this->is_ajax_request()) {
            
            // update limit into user_paging.
            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);
            
            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging ['limit'] = $limit;
            
            // Call search method
            $query_result = $this->instance_m->get_customer_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);
            
            // Process output data
            $total = $query_result ['total'];
            $datas = $query_result ['data'];
            
            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
            
            $i = 0;
            foreach ( $datas as $row ) {
                $response->rows [$i] ['id'] = $row->instance_id;
                $response->rows [$i] ['cell'] = array (
                        $row->name,
                        lang('domain_type_' . $row->domain_type),
                        $row->domain_name,
                        lang('s3_type_' . $row->s3_type),
                        $row->s3_name,
                        lang('database_type_' . $row->database_type),
                        $row->database_name,
                        $row->instance_id 
                );
                $i ++;
            }
            echo json_encode($response);
        }
        else {
            // Display the current page
            $this->template->build('admin/index');
        }
    }
    
    /**
     * Method for handling different form actions
     */
    public function add() {
        $this->template->set_layout(FALSE);
        $instance = new stdClass();
        $instance_domain = new stdClass();
        $instance_database = new stdClass();
        $instance_amazon = new stdClass();
        $instance->instance_id = '';
        if ($_POST) {
            $this->form_validation->set_rules($this->validation_rules_instance);
            $this->form_validation->set_rules($this->validation_rules_domain);
            $this->form_validation->set_rules($this->validation_rules_amazon);
            $this->form_validation->set_rules($this->validation_rules_database);
            
            if ($this->form_validation->run()) {
                try {
                    $s3_type = $this->input->get_post("s3_type");
                    $s3_name = $this->input->get_post("s3_name");
                    
                    // Insert to main table
                    $instance_id = $this->instance_m->insert(array (
                            "name" => $this->input->get_post("name"),
                            "created_date" => now(),
                            "updated_date" => now(),
                            "activated_flag" => APConstants::ON_FLAG,
                            "activated_date" => now() 
                    ));
                    $instance_code = sprintf('%1$02d', $instance_id);
                    $this->instance_m->update($instance_id, array (
                            'instance_code' => $instance_code 
                    ));
                    
                    // Insert to domain table
                    $this->instance_domain_m->insert(array (
                            "instance_id" => $instance_id,
                            "domain_name" => $this->input->get_post("domain_name"),
                            "domain_type" => $this->input->get_post("domain_type"),
                            "full_url" => $this->input->get_post("full_url"),
                            "created_date" => now(),
                            "created_by" => APContext::getAdminIdLoggedIn(),
                            "last_updated_date" => null,
                            "last_updated_by" => null 
                    ));
                    
                    // Insert to amazone table
                    $this->instance_amazon_m->insert(array (
                            "instance_id" => $instance_id,
                            "s3_name" => $this->input->get_post("s3_name"),
                            "s3_type" => $this->input->get_post("s3_type") 
                    ));
                    
                    // Insert to database table
                    $this->instance_database_m->insert(array (
                            "instance_id" => $instance_id,
                            "database_name" => $this->input->get_post("database_name"),
                            "database_type" => $this->input->get_post("database_type"),
                            "host_address" => $this->input->get_post("host_address"),
                            "username" => $this->input->get_post("username"),
                            "password" => $this->input->get_post("password"),
                            "created_date" => now(),
                            "created_by" => APContext::getAdminIdLoggedIn(),
                            "last_updated_date" => null,
                            "last_updated_by" => null 
                    ));
                    
                    $message = sprintf(lang('instance.add_success'), $this->input->post('name'));
                    $this->success_output($message);
                    return;
                } catch ( Exception $e ) {
                    $message = sprintf(lang('instance.add_error'), $this->input->post('name'));
                    $this->error_output($message);
                    return;
                }
            }
            else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }
        
        // Loop through each validation rule
        foreach ( $this->validation_rules_instance as $rule ) {
            $instance->{$rule ['field']} = set_value($rule ['field']);
        }
        // Loop through each validation rule
        foreach ( $this->validation_rules_domain as $rule ) {
            $instance_domain->{$rule ['field']} = set_value($rule ['field']);
        }
        // Loop through each validation rule
        foreach ( $this->validation_rules_amazon as $rule ) {
            $instance_amazon->{$rule ['field']} = set_value($rule ['field']);
        }
        // Loop through each validation rule
        foreach ( $this->validation_rules_database as $rule ) {
            $instance_database->{$rule ['field']} = set_value($rule ['field']);
        }
        
        // Display the current page
        $this->template->set('instance_domain', $instance_domain);
        $this->template->set('instance_amazon', $instance_amazon);
        $this->template->set('instance_database', $instance_database);
        $this->template->set('instance', $instance)->set('action_type', 'add')->build('admin/form');
    }
    
    /**
     * Method for handling different form actions
     */
    public function edit() {
        $this->template->set_layout(FALSE);
        $id = $this->input->get_post("id");
        $instance = new stdClass();
        if (! empty($id)) {
            $instance = $this->instance_m->get($id);
            $instance_domain = $this->instance_domain_m->get($id);
            $instance_amazon = $this->instance_amazon_m->get($id);
            $instance_database = $this->instance_database_m->get($id);
        }
        
        if ($_POST) {
            $this->form_validation->set_rules($this->validation_rules_instance);
            $this->form_validation->set_rules($this->validation_rules_domain);
            $this->form_validation->set_rules($this->validation_rules_amazon);
            $this->form_validation->set_rules($this->validation_rules_database);
            
            if ($this->form_validation->run()) {
                try {
                    // Update instance
                    $this->instance_m->update_by_many(array (
                            "instance_id" => $id 
                    ), array (
                            "name" => $this->input->get_post("name"),
                            "updated_date" => now() 
                    ));
                    
                    // Update instance domain
                    $this->instance_domain_m->update_by_many(array (
                            "instance_id" => $id 
                    ), array (
                            "domain_name" => $this->input->get_post("domain_name"),
                            "domain_type" => $this->input->get_post("domain_type"),
                            "full_url" => $this->input->get_post("full_url"),
                            "last_updated_date" => now(),
                            "last_updated_by" => APContext::getAdminIdLoggedIn() 
                    ));
                    
                    // Update instance amazon
                    $this->instance_amazon_m->update_by_many(array (
                            "instance_id" => $id 
                    ), array (
                            "s3_name" => $this->input->get_post("s3_name"),
                            "s3_type" => $this->input->get_post("s3_type") 
                    ));
                    
                    // Update instance database
                    $this->instance_database_m->update_by_many(array (
                            "instance_id" => $id 
                    ), array (
                            "database_name" => $this->input->get_post("database_name"),
                            "database_type" => $this->input->get_post("database_type"),
                            "host_address" => $this->input->get_post("host_address"),
                            "username" => $this->input->get_post("username"),
                            "password" => $this->input->get_post("password"),
                            "last_updated_date" => now(),
                            "last_updated_by" => APContext::getAdminIdLoggedIn() 
                    ));
                    
                    $message = sprintf(lang('instance.edit_success'), $this->input->post('name'));
                    $this->success_output($message);
                    return;
                } catch ( Exception $e ) {
                    $message = sprintf(lang('instance.edit_error'), $this->input->post('name'));
                    $this->error_output($message);
                    return;
                }
            }
            else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }
        
        // Display the current page
        $this->template->set('instance_domain', $instance_domain);
        $this->template->set('instance_amazon', $instance_amazon);
        $this->template->set('instance_database', $instance_database);
        $this->template->set('instance', $instance)->set('action_type', 'edit')->build('admin/form');
    }
    
    /**
     * Delete customer
     */
    public function delete() {
        $customer_id = $this->input->get_post("id");
        $this->instance_m->delete_by_many(array (
                "instance_id" => $customer_id 
        ));
        $this->instance_domain_m->delete_by_many(array (
                "instance_id" => $customer_id 
        ));
        $this->instance_database_m->delete_by_many(array (
                "instance_id" => $customer_id 
        ));
        $this->instance_amazon_m->delete_by_many(array (
                "instance_id" => $customer_id 
        ));
        $message = sprintf(lang('customer.delete_success'));
        $this->success_output($message);
        return;
    }
    
    public function instance_owner(){
        //  TODO:
        $this->template->build ( 'page_construction' );
    }
    
    public function super_admin(){
        //  TODO:
        $this->template->build ( 'page_construction' );
    }
    
    /**
     * Callback From: check_name()
     * 
     * @param string $domain_name
     *            The domain address to validate
     * @return bool
     */
    public function _check_name($name) {
        $id = $this->input->get_post("id");
        // Get user information by email
        $customer = $this->instance_m->get_by_many(array (
                "name" => $name 
        ));
        
        if ($customer && $customer->instance_id != $id) {
            $this->form_validation->set_message('_check_name', lang('name_exist'));
            return false;
        }
        return true;
    }
    
    /**
     * Callback From: check_domain_name()
     * 
     * @param string $domain_name
     *            The domain address to validate
     * @return bool
     */
    public function _check_domain_name($domain_name) {
        $id = $this->input->get_post("id");
        // Get user information by email
        $customer = $this->instance_domain_m->get_by_many(array (
                "domain_name" => $domain_name 
        ));
        
        if ($customer && $customer->instance_id != $id) {
            $this->form_validation->set_message('_check_domain_name', lang('domain_name_exist'));
            return false;
        }
        return true;
    }
    
    /**
     * Callback From: check_s3_name()
     * 
     * @param string $s3_name
     *            The s3 address to validate
     * @return bool
     */
    public function _check_s3_name($s3_name) {
        $id = $this->input->get_post("id");
        // Get user information by email
        $customer = $this->instance_amazon_m->get_by_many(array (
                "s3_name" => $s3_name 
        ));
        
        if ($customer && $customer->instance_id != $id) {
            $this->form_validation->set_message('_check_s3_name', lang('s3_name_exist'));
            return false;
        }
        return true;
    }
    
    /**
     * Callback From: check_database_name()
     * 
     * @param string $database_name
     *            The database name to validate
     * @return bool
     */
    public function _check_database_name($database_name) {
        $id = $this->input->get_post("id");
        // Get user information by email
        $customer = $this->instance_database_m->get_by_many(array (
                "database_name" => $database_name 
        ));
        
        if ($customer && $customer->instance_id != $id) {
            $this->form_validation->set_message('_check_database_name', lang('database_name_exist'));
            return false;
        }
        return true;
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */