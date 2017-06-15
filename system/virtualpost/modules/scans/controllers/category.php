<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin controller for the users module
 */
class Category extends Admin_Controller {
    
    /**
     * Validation for basic profile
     * data.
     * The rest of the validation is
     * built by streams.
     * 
     * @var array
     */
    private $validation_rules = array (
            array (
                    'field' => 'LabelValue',
                    'label' => 'lang:category.LabelValue',
                    'rules' => 'required|max_length[100]' 
            )
    );
 
    
    /**
     * Constructor method
     */
    public function __construct() {
        parent::__construct();
        
        // Load the required classes
        $this->load->model('scans/setting_m');
        
        $this->load->library('form_validation');
        
        // Load language
        $this->lang->load('scans');
    }
    
    /**
     * List all users
     */
    public function index() {
        // Get input condition
        $array_condition = array ('SettingCode'=>APConstants::CATEGORY_TYPE_CODE);
        
        // If current request is ajax
        if ($this->is_ajax_request()) {
            
            // update limit into user_paging.
            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);
            
            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging['limit'] = $limit;
            
            // Call search method
            $query_result = $this->setting_m->get_category_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);
            //var_dump($query_result);
            // Process output data
            $total = $query_result ['total'];
            $datas = $query_result ['data'];
            
            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
            
            $i = 0;
            foreach ( $datas as $row ) {
                $response->rows [$i] ['id'] = $row->SettingKey;
                $response->rows [$i] ['cell'] = array (
                        $row->SettingKey,
                        $row->LabelValue,
                        $row->ActualValue,
                        $row->SettingKey 
                );
                $i ++;
            }
            
            echo json_encode($response);
        } else {
            // Display the current page
            $this->template->set('header_title', lang('header:list_user_title'))->build('category/index');
        }
    }
    
    /**
     * Method for handling different form actions
     */
    public function add() {
        $category = new stdClass();
        $this->template->set_layout(FALSE);
        
        if ($_POST) {
            $this->form_validation->set_rules($this->validation_rules);
            
            $category_name = $this->input->post('LabelValue');
            $category_value = $this->setting_m->count_by_many(array('SettingCode'=>APConstants::CATEGORY_TYPE_CODE)) + 1;
            
            if ($this->form_validation->run()) {
                $this->setting_m->insert(array('LabelValue'=>$category_name,'ActualValue'=>$category_value, 'SettingCode'=>APConstants::CATEGORY_TYPE_CODE));
                
                $this->success_output(lang('category.add_successfull'));
                return;
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }
        
        // Loop through each validation rule
        foreach ( $this->validation_rules as $rule ) {
            $category->{$rule ['field']} = set_value($rule ['field']);
        }
        
        $category->SettingKey = '';
        // Display the current page
        $this->template->set('category', $category)->set('action_type', 'add')->build('category/form');
    }
    
    
    /**
     * Edit an existing user
     * 
     * @param int $id
     *            The id of the user.
     */
    public function edit() {
        $this->template->set_layout(FALSE);
        $SettingKey = $this->input->get_post("SettingKey");
        // Get the user's data
        if (! ($member = $this->setting_m->get_by("SettingKey", $SettingKey))) {
            $this->session->set_flashdata('error', lang('category.not_found'));
            echo lang('category.not_found');
            return;
        }
        if ($_POST) {
            // Set the validation rules
            $this->form_validation->set_rules($this->validation_rules);
            
            if ($this->form_validation->run() === true) {
                // Get the POST data
                $update_data ['LabelValue'] = $this->input->post('LabelValue');

                $result = $this->setting_m->update($SettingKey, $update_data);
                if ($result) {
                    $this->success_output(lang('category.edit_successfull'));
                    return;
                } else {
                    $this->error_output(lang('category.edit_not_successfull'));
                    return;
                }
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }
        // Loop through each validation rule
        foreach ( $this->validation_rules as $rule ) {
            if ($this->input->post($rule ['field']) !== false) {
                $member->{$rule ['field']} = set_value($rule ['field']);
            }
        }
        
        // Display the current page
        $this->template->set('category',$member)->set('action_type', 'edit')->build('category/form');
    }

    /**
     * Delete group role(s)
     * 
     * @param int $id
     *            The id of the group.
     */
    public function delete() {
        $SettingKey = $this->input->get_post("SettingKey");
        
        $success = $this->setting_m->delete($SettingKey);
        if ($success) {
            $message = lang('category.delete_success');
            $this->success_output($message);
            return;
        } else {
            $message = lang('category.delete_error');
            $this->error_output($message);
            return;
        }
    }
}