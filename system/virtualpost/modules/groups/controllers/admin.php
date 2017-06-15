<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Roles controller for the groups module
 * 
 */
class Admin extends Admin_Controller {
    
    /**
     * Constructor method
     */
    public function __construct() {
        parent::__construct();
        
        // Load the required classes
        $this->load->library('form_validation');
        
        $this->load->model('permission_m');
        $this->load->model('group_m');        
        
        $this->lang->load('group');
        $this->lang->load(array (
                'groups/permissions' 
        ));
        
        // Validation rules
        $this->validation_rules = array (
                array (
                        'field' => 'Name',
                        'label' => lang('groups.name'),
                        'rules' => 'trim|required|max_length[100]' 
                ),
                array (
                        'field' => 'Description',
                        'label' => lang('groups.description'),
                        'rules' => 'trim|required|max_length[250]' 
                ) 
        );
    }
    
    /**
     * Create a new group role
     */
    public function index() {
        // If current request is ajax
        if ($this->is_ajax_request()) {
            $array_condition = array ();
            
            // Get paging input
            $input_paging = $this->get_paging_input();
            
            // Call search method
            $query_result = $this->group_m->get_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);
            
            // Process output data
            $total = $query_result ['total'];
            $datas = $query_result ['data'];
            
            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
            
            $i = 0;
            foreach ( $datas as $row ) {
                $response->rows [$i] ['id'] = $row->ID;
                $response->rows [$i] ['cell'] = array (
                        $row->ID,
                        $row->Description,
                        $row->Name,
                        $row->ID 
                );
                $i ++;
            }
            
            echo json_encode($response);
        } else {
            // Display the current page
            $this->template->set('header_title', lang('header:list_group_title'))->build('admin/index');
        }
    }
    
    /**
     * Create a new group role
     */
    public function add() {
        $group = new stdClass();
        $group->ID = '';
        $this->template->set_layout(FALSE);
        if ($_POST) {
            $this->form_validation->set_rules($this->validation_rules);
            
            if ($this->form_validation->run()) {
                $id = $this->group_m->insert($this->input->post());
                if ($id) {
                    $message = sprintf(lang('groups.add_success'), $this->input->post('Name'));
                    $this->success_output($message);
                    return;
                } else {
                    $message = sprintf(lang('groups.add_error'), $this->input->post('Name'));
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
        foreach ( $this->validation_rules as $rule ) {
            $group->{$rule ['field']} = set_value($rule ['field']);
        }
        $this->template->set('group', $group)->set('action_type', 'add')->build('admin/form');
    }
    
    /**
     * Edit a group role
     * 
     * @param int $id
     *            The id of the group.
     */
    public function edit() {
        $id = $this->input->get_post("id");
        $group = $this->group_m->get($id);
        $this->template->set_layout(FALSE);
        
        if ($_POST) {
            // Got validation?
            if ($group->Name == 'admin' or $group->Name == 'user') {
                // if they're changing description on admin or user save the old
                // name
                $_POST ['Name'] = $group->Name;
                $this->form_validation->set_rules('description', lang('groups.description'), 'trim|required|max_length[250]');
            } else {
                $this->form_validation->set_rules($this->validation_rules);
            }
            
            if ($this->form_validation->run()) {
                $success = $this->group_m->update($id, $this->input->post());
                if ($success) {
                    $message = sprintf(lang('groups.edit_success'), $this->input->post('Name'));
                    $this->success_output($message);
                    return;
                } else {
                    $message = sprintf(lang('groups.edit_error'), $this->input->post('Name'));
                    $this->error_output($message);
                    return;
                }
                
            } else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }
        
        $this->template->set('group', $group)->set('action_type', 'edit')->build('admin/form');
    }
    
    /**
     * Delete group role(s)
     * 
     * @param int $id
     *            The id of the group.
     */
    public function delete() {
        $id = $this->input->get_post("id");
        $success = $this->group_m->delete($id);
        if ($success) {
            $message = lang('success');
            $this->success_output($message);
            return;
        } else {
            $message = sprintf(lang('groups.delete_error'), $this->input->post('Name'));
            $this->error_output($message);
            return;
        }
    }
    
    /**
     * Shows the permissions for a specific user group.
     *
     * @param int $group_id The id of the group to show permissions for.
     */
    public function group($group_id)
    {
        $this->template->set_layout(FALSE);
    	$this->load->library('form_validation');
    
    	if ($_POST)
    	{
    		$modules = $this->input->post('modules');
    		$roles = $this->input->post('module_roles');
    
    		// Save the permissions.
    		if ( $this->permission_m->save($group_id, $modules, $roles)){
    
    			// Fire an event. Permissions have been saved.
    			Events::trigger('permissions_saved', array($group_id, $modules, $roles));    
    			//$this->session->set_flashdata('success', lang('permissions:message_group_saved_success'));
    			$this->success_output(lang('permissions:message_group_saved_success'));
    			return;
    		}
    		else
    		{
    			//$this->session->set_flashdata('error', lang('permissions:message_group_saved_error'));
    			$this->success_output(lang('permissions:message_group_saved_error'));
    			return;
    		}
    
    		//$this->input->post('btnAction') == 'save_exit' ? redirect('permissions/admin') : redirect('permissions/admin/group/' . $group_id);
    	}
    	// Get the group data
    	$group = $this->group_m->get($group_id);
    	// If the group data could not be retrieved
    	if ( ! $group ) {
    		// Set a message to notify the user.
    		//$this->session->set_flashdata('error', lang('permissions:message_no_group_id_provided'));
    		// Send him to the main index to select a proper group.
    		//redirect('permissions/admin');
    	    $this->success_output(lang('permissions:message_no_group_id_provided'));
    	    return;
    	}
    
    	// See if this is the admin group
    	$group_is_admin = (bool) ($this->config->item('admin_group', 'ion_auth') == $group->Name);
    	// Get the groups permission rules (no need if this is the admin group)
    	$edit_permissions = ($group_is_admin) ? array() : $this->permission_m->get_group($group_id);
    	// Get all the possible permission rules from the installed modules
    	$permission_modules = $this->module_m->get_all(array('IsBackEnd' => true));
    
    	foreach ($permission_modules as &$permission_module)
    	{
    		$permission_module->roles = $this->module_m->roles($permission_module->ModuleName);
    	}
    
    	$this->template
    	->set('edit_permissions', $edit_permissions)
    	->set('group_is_admin', $group_is_admin)
    	->set('permission_modules', $permission_modules)
    	->set('group', $group)
    	->build('admin/group');
    }
}
