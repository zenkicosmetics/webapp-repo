<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin controller for the users module
 */
class Admin extends Admin_Controller {
    
    /**
     * Validation for basic profile data. The rest of the validation is built by streams.
     * 
     * @var array
     */
    private $validation_rules = array ( 
            array (
                    'field' => 'username',
                    'label' => 'lang:user_username',
                    'rules' => 'required|validname|min_length[3]|max_length[20]' 
            ),
            array (
                    'field' => 'email',
                    'label' => 'lang:user_email_label',
                    'rules' => 'required|max_length[60]|valid_email' 
            ),
            array (
                    'field' => 'password',
                    'label' => 'lang:user_password_label',
                    'rules' => 'required|min_length[6]|max_length[100]' 
            ),
            array (
                    'field' => 'first_name',
                    'label' => 'lang:user_first_name_label',
                    'rules' => 'required|validname|max_length[20]' 
            ),
            array (
                    'field' => 'last_name',
                    'label' => 'lang:user_last_name_label',
                    'rules' => 'required|validname|max_length[20]' 
            ),
            array (
                    'field' => 'group_id',
                    'label' => 'lang:user_group_label',
                    'rules' => 'required' 
            ),
            array (
                    'field' => 'active',
                    'label' => 'lang:user_active_label',
                    'rules' => '' 
            ),
            array (
                    'field' => 'location_users_available',
                    'label' => 'lang:location_users_available_lavel',
                    'rules' => '' 
            )  
    );
    private $validation_rules03 = array (
            array (
                    'field' => 'password',
                    'label' => 'lang:password',
                    'rules' => 'required|trim|matches[repeat_password]|min_length[6]|max_length[255]' 
            ),
            array (
                    'field' => 'repeat_password',
                    'label' => 'lang:repeat_password',
                    'rules' => 'required|trim|min_length[6]|max_length[255]' 
            ) 
    );
    
    /**
     * Validation for basic profile user data. The rest of the validation is built by streams.
     * 
     * @var array
     */
    private $validation_rules_user = array (
            array (
                    'field' => 'username',
                    'label' => 'lang:user_username',
                    'rules' => 'required|validname|min_length[3]|max_length[20]' 
            ),
            array (
                    'field' => 'email',
                    'label' => 'lang:user_email_label',
                    'rules' => 'required|max_length[60]|valid_email' 
            ),
            array (
                    'field' => 'password',
                    'label' => 'lang:user_password_label',
                    'rules' => 'trim|matches[PasswordConf]|min_length[6]|max_length[100]' 
            ),
            array (
                    'field' => 'passwordconf',
                    'label' => 'lang:user_passwordconf_label',
                    'rules' => 'trim|min_length[6]|max_length[100]' 
            ),
            array (
                    'field' => 'first_name',
                    'label' => 'lang:user_first_name_label',
                    'rules' => 'required|validname|min_length[3]|max_length[20]' 
            ),
            array (
                    'field' => 'last_name',
                    'label' => 'lang:user_last_name_label',
                    'rules' => 'required|validname|min_length[3]|max_length[20]' 
            ) 
    );
    
    /**
     * Constructor method
     */
    public function __construct() {
        parent::__construct();
        
        // Load the required classes
        $this->load->model('user_m');
        $this->load->model('user_profiles_m');
        $this->load->model('group_user_m');
        $this->load->model('groups/group_m');
        $this->load->model('partner/partner_m');
        $this->load->helper('user');
        
        $this->load->library(array(
            'form_validation',
            'settings/settings_api',
            'users/users_api'
        ));
        
        $this->load->model('addresses/location_m');
        $this->lang->load('user');
        $this->load->model('addresses/location_users_m');
        $this->load->model('settings/currencies_m');
        $this->load->model('settings/countries_m');
    }
    
    /**
     * List all users
     */
    public function index() {
        if(APContext::isWorkerAdmin() || APContext::isAdminLocation()|| APContext::isAdminParner()){
            $list_location_id = APUtils::loadListAccessLocation();
            $list_access_id = array();
            foreach($list_location_id as $row){
                $list_access_id[] = $row->id;
            }
            // Get input condition
            $array_condition = array (
                "location_users.location_id IN ('".  implode("','", $list_access_id)."')" => null,

            );
            $array_condition["group_users.group_id NOT IN (0, 1)"] = null;

        }else{
            $array_condition = array();
        }
        
        //#1310 add name search in worker setting
        $enquiry = $this->input->get_post("enquiry");
        if(!empty($enquiry)) {
            $new_enquiry = APUtils::sanitizing($enquiry);
            $array_condition["(users.username LIKE '%" . $new_enquiry .
                    "%' OR users.email= '" . $new_enquiry . "' )"] = null;
        }

        #1058 add multi dimension capability for admin
        $date_format = APUtils::get_date_format_in_user_profiles();
        // If current request is ajax
        if ($this->is_ajax_request()) {
            
            // update limit into user_paging.
            $limit = isset($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
            APContext::updateAdminPagingSetting($limit);
            
            // Get paging input
            $input_paging = $this->get_paging_input();
            $input_paging ['limit'] = $limit;
            
            // Call search method
            $query_result = $this->user_m->get_user_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);
            
            // Process output data
            $total = $query_result ['total'];
            $datas = $query_result ['data'];
            
            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
            
            $i = 0;
            foreach ( $datas as $row ) {
                // Get the user's data
                $current_user_group_id = $this->group_user_m->get_selected_group_by ($row->id);
                $group_name = array();
                foreach($current_user_group_id as $current_group){
                    $group_name[] = $current_group->description;
                }
                
                // get location access
                $locations = $this->location_users_m->get_location_users_available($row->id);
                $location_name =array();
                foreach($locations as $l){
                    $location_name[] = $l->location_name;
                }

                $response->rows [$i] ['id'] = $row->id;
                $response->rows [$i] ['cell'] = array (
                        $row->id,
                        $row->username,
                        $row->email,
                        implode(",", $group_name),
                        $row->active,
                        implode(',', $location_name),
                        APUtils::viewDateFormat($row->created_on, $date_format),
                        APUtils::viewDateFormat($row->last_login, $date_format),
                        $row->delete_flag,
                        $row->id 
                );
                $i ++;
            }
            
            echo json_encode($response);
        }
        else {
            // Get all groups
            $groups = $this->group_m->get_all();
            
            // Display the current page
            $this->template->set('groups', $groups)->set('header_title', lang('header:list_user_title'))->build('admin/index');
        }
    }
    
    /**
     * Method for handling different form actions
     */
    public function add() {
        $user = new stdClass();
        $user->id = '';
        // #1058 add multi dimension capability for admin
        $profile_member = new stdClass();
        $profile_member->length_unit = '';
        $profile_member->weight_unit = '';
        $profile_member->decimal_separator = '';
        $profile_member->date_format = '';
        // Languages
        $this->load->model('settings/language_code_m');
        $languages = $this->language_code_m->get_many_by_many(array('status' => APConstants::ON_FLAG));
        // Currencies
        $currencies = $this->currencies_m->get_all();
        
        $this->template->set_layout(FALSE);

        if ($_POST) {
            // Extra validation for basic data
            $this->validation_rules [0] ['rules'] .= '|callback__username_check';
            $this->validation_rules [1] ['rules'] .= '|callback__email_check';

            $this->form_validation->set_rules($this->validation_rules);
            
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $username = $this->input->post('username');
            $group_id = $this->input->post('group_id');
            $display_name = $this->input->post('username');
            $first_name = $this->input->post('first_name');
            $last_name = $this->input->post('last_name');
            
            $sent_notification_customer_flag = (int) $this->input->post('sent_notification_customer_flag');
            $info_email = '';
            if(!empty($this->input->post('info_email'))){
                $info_email = $this->input->post('info_email');
            }
            
            $active = $this->input->post('active');
            
            $current_user_group_id = $this->group_user_m->get_selected_group_by (APContext::getAdminIdLoggedIn());
            $user_admin_group = array();
            foreach($current_user_group_id as $current_group){
                $user_admin_group[] = $current_group->group_id;
            }
            if ($group_id && in_array("0", $group_id) && in_array("0", $current_user_group_id) ) {
            	$message = 'Permission denied';
            	$this->error_output($message);
            	return;
            }
    
            if ($this->form_validation->run()) {
                if (empty($group_id)) {
                    $this->error_output("Group is required.");
                    exit();
                }

                // Hack to activate immediately
                if ($this->input->post('active')) {
                    $this->config->config ['ion_auth'] ['email_activation'] = false;
                }
                // $group = $this->group_m->get($group_id);
                $user_id = $this->ion_auth->register($username, $password, $email, null, array (
                        "display_name" => $display_name,
                        "first_name" => $first_name,
                        "last_name" => $last_name,
                        "active" => $active
                ));


                $location_users_available = $this->input->post('location_users_available');
                
                if ($location_users_available && is_array($location_users_available)) {
                        
                    $this->location_users_m->delete_by("user_id", $user_id); 
                    
                    foreach($location_users_available as $location_user_id){
                        $data = array(
                           "user_id"        => $user_id,
                           "location_id"    => $location_user_id                         
                        );

                        $this->location_users_m->insert($data);
                    }
                        
                }
                else {
                    $message = lang('location_not_exist');
                    $this->error_output($message);
                    return;
                }
                
                ///$update_data ['partner_id'] = $partner_id;
                $update_data = array();
                
                $update_data['delete_flag'] = APConstants::OFF_FLAG;
                $update_data['sent_notification_customer_flag'] = $sent_notification_customer_flag;
                $update_data['info_email'] = $info_email;
                
                $this->user_m->update_by_many(array (
                        'id' => $user_id 
                ), $update_data);
                
                $this->group_user_m->delete_by_many(array(
                    "user_id" => $user_id,
                ));
                foreach($group_id as $gid){
                    $this->group_user_m->insert(array(
                        "user_id" => $user_id,
                        "group_id" => $gid
                    ));
                }
                // #1058 add multi dimension capability for admin
                $this->user_profiles_m->insert(array(
                	"user_id" => $user_id,
                	'language' => ucfirst(strtolower($this->input->post('language'))),
                	'currency_id' => $this->input->post('currency_id'),
                	'length_unit' => $this->input->post('length_unit'),
                	'weight_unit' => $this->input->post('weight_unit'),
                	'decimal_separator' => $this->input->post('decimal_separator'),
                	'date_format' => $this->input->post('date_format'),
                	'created_date' => now()
                ));
                
                if ($user_id) {
                    $message = lang('success');
                    $this->success_output($message);
                    return;
                }
                else {
                    $message = $this->ion_auth->errors();
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
        foreach ( $this->validation_rules as $rule ) {
            $user->{$rule ['field']} = set_value($rule ['field']);
        }
        
        $locations = APUtils::loadListAccessLocation();
        
        // Get all information.
        if (APContext::isSupperAdminUser()) {
        	$groups = $this->group_m->get_all();
        	$partner = $this->partner_m->get_all();
        }
        else if (APContext::isAdminUser()) {
        	// $groups = $this->group_m->get_all();
        	$groups = $this->group_m->get_group_by_partner(array ("1", "2", "4", "5"));
        	$partner = $this->partner_m->get_all();
        }
        else if (APContext::isAdminLocation()) {
        	$partner = $this->partner_m->get_many_by("partner_id", APContext::getParnerIDLoggedIn());
        	$groups = $this->group_m->get_group_by_partner(array ("2", "4", "5"));
        }
        else {
        	$partner = $this->partner_m->get_many_by("partner_id", APContext::getParnerIDLoggedIn());
        	$groups = $this->group_m->get_group_by_partner(array (""));
        }
        
        // Gets selected group
        $selected_group = array();
        
        // Display the current page
        $this->template->set("languages", $languages);
        $this->template->set("currencies", $currencies);
        $this->template->set("profile_member", $profile_member);
        $this->template->set('locations', $locations);
        $this->template->set('selected_group', $selected_group);
        $this->template->set('user', $user);
        $this->template->set('groups', $groups);
        $this->template->set('action_type', 'add');
        $this->template->set("partner", $partner);
        $this->template->build('admin/form');
    }
    
    /**
     * Method for handling different form actions
     */
    public function action() {
        
        // Determine the type of action
        switch ($this->input->post('btnAction')) {
            case 'activate' :
                $this->activate();
                break;
            case 'delete' :
                $this->delete();
                break;
            default :
                redirect('admin/users');
                break;
        }
    }
    
    /**
     * Edit an existing user
     * 
     * @param int $id
     *            The id of the user.
     */
    public function edit() {
        
        $this->template->set_layout(FALSE);
        $id = $this->input->get_post("id");
        $location_users_available = $this->location_users_m->get_location_users_available($id);
        $location_users_available_id  = array();
        if(!empty($location_users_available)){
            foreach ($location_users_available as $location_user) {
                $location_users_available_id[] = $location_user->id;
            }
        }

        $member = $this->user_m->get_by('id', $id);
        
        // #1058 add multi dimension capability for admin
        //$languages = $this->countries_m->getAllLanguagesForDropDownList();
        $this->load->model('settings/language_code_m');
        $languages = $this->language_code_m->get_many_by_many(array('status' => APConstants::ON_FLAG));
        $currencies = $this->currencies_m->get_all();
        $profile_member = $this->user_profiles_m->get_by('user_id', $id);
        
        // Gets selected group
        $selected_group = $this->group_user_m->get_selected_group_by ($id);
        $selected_group_id = array();
        $enable = false;
        foreach($selected_group as $current_group){
            $selected_group_id[] = $current_group->group_id;
        }
        
        if( in_array(APConstants::GROUP_SUPER_ADMIN, $selected_group_id) 
         || in_array(APConstants::GROUP_LOCATION_ADMIN, $selected_group_id)
         || in_array(APConstants::GROUP_ADMIN, $selected_group_id)        
        ){
            $enable = true;
        }
        
        if ($_POST) {
            // Check to see if we are changing usernames
            if ($member->username != $this->input->post('username')) {
                $this->validation_rules [0] ['rules'] .= '|callback__username_check';
            }
            
            // Check to see if we are changing emails
            if ($member->email != $this->input->post('email')) {
                $this->validation_rules [1] ['rules'] .= '|callback__email_check';
            }
            $this->validation_rules [2] ['rules'] = '';
            
            $group_id = $this->input->post('group_id');

            // Get the user's data
            $current_user_group_id = $this->group_user_m->get_selected_group_by (APContext::getAdminIdLoggedIn());
            $user_admin_group = array();
            foreach($current_user_group_id as $current_group){
                $user_admin_group[] = $current_group->group_id;
            }
            if ($group_id && in_array("0", $group_id) && in_array("0", $current_user_group_id) ) {
                $message = 'Permission denied';
                $this->error_output($message);
                return;
            }
            
            // Set the validation rules
            $this->form_validation->set_rules($this->validation_rules);
            
            if ($this->form_validation->run() === true) {
                 if (empty($group_id)) {
                    $this->error_output("Group is required.");
                    exit();
                }
                
                // Get the POST data
                $update_data ['email'] = $this->input->post('email');
                $update_data ['active'] = $this->input->post('active');
                $update_data ['username'] = $this->input->post('username');
                $update_data ['display_name'] = $this->input->post('username');
                $update_data ['first_name'] = $this->input->post('first_name');
                $update_data ['last_name'] = $this->input->post('last_name');
                
                $update_data ['sent_notification_customer_flag'] = (int) $this->input->post('sent_notification_customer_flag');
                if(!empty($this->input->post('info_email'))){
                    $update_data ['info_email'] = $this->input->post('info_email');
                }
                // Get partner_id from location_available_id
                $location_users_available = $this->input->post('location_users_available');
                if (empty($location_users_available)) {
                    $this->error_output("Location is required.");
                    exit();
                }
                
                //echo "<pre>";print_r($location_users_available);exit;
                if ($location_users_available && is_array($location_users_available)) {
                    
                    $this->location_users_m->delete_by("user_id", $id); 
                    
                    foreach($location_users_available as $location_user_id){
                        //echo $location_user_id."\n";
                        $data = array(
                           "user_id"        => $id,
                           "location_id"    => $location_user_id                         
                        );

                        $this->location_users_m->insert($data);
                
                    }
                    
                }
                else {
                    $message = lang('location_not_exist');
                    $this->error_output($message);
                    return;
                }

                // Password provided, hash it for storage
                if ($this->input->post('password')) {
                    $update_data ['password'] = $this->input->post('password');
                }
                
                $this->group_user_m->delete_by_many(array(
                    "user_id" => $id,
                ));
                foreach($group_id as $gid){
                    $this->group_user_m->insert(array(
                        "user_id" => $id,
                        "group_id" => $gid
                    ));
                }
                
                // #1058 add multi dimension capability for admin
                $arr_user_id = $this->user_profiles_m->getAllUserId();
                if(array_search($id, array_column($arr_user_id, 'user_id')) !== false){
                	$this->user_profiles_m->update($id, array(
                			'language' => ucfirst(strtolower($this->input->post('language'))),
                			'currency_id' => $this->input->post('currency_id'),
                			'length_unit' => $this->input->post('length_unit'),
                			'weight_unit' => $this->input->post('weight_unit'),
                			'decimal_separator' => $this->input->post('decimal_separator'),
                			'date_format' => $this->input->post('date_format')
                	));
                	// #1058 add multi dimension capability for admin
                	APContext::reloadAdminLoggedIn();
                }else{
                	$this->user_profiles_m->insert(array(
                			"user_id" => $id,
                			'language' => ucfirst(strtolower($this->input->post('language'))),
                			'currency_id' => $this->input->post('currency_id'),
                			'length_unit' => $this->input->post('length_unit'),
                			'weight_unit' => $this->input->post('weight_unit'),
                			'decimal_separator' => $this->input->post('decimal_separator'),
                			'date_format' => $this->input->post('date_format'),
                			'created_date' => now()
                	));
                	// #1058 add multi dimension capability for admin
                	APContext::reloadAdminLoggedIn();
                }
                
                $result = $this->ion_auth->update_user($id, $update_data);
                if ($result) {
                    $message = $this->ion_auth->messages();
                    $this->success_output($message);
                    return;
                }
                else {
                    $message = $this->ion_auth->errors();
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
        $locations = APUtils::loadListAccessLocation();
        
        // Get all information.
        $groups = array();
        if (APContext::isSupperAdminUser()) {
            $list_group_id = $this->get_list_group_id($selected_group_id, array ("0", "1", "2", "4", "5"));

            if(count($list_group_id) > 0){
                $groups = $this->group_m->get_group_by_partner($list_group_id);
            }
        	$partner = $this->partner_m->get_all();

        	$locations = $this->location_m->get_all();

        }
        else if (APContext::isAdminUser()) {
            $list_group_id = $this->get_list_group_id($selected_group_id, array ("1", "2", "4", "5"));
            if(count($list_group_id) > 0){
                $groups = $this->group_m->get_group_by_partner($list_group_id);
            }
        	$partner = $this->partner_m->get_all();
        	
            $locations = $this->location_m->get_all();

        }
        else if (APContext::isAdminLocation()) {
        	$partner = $this->partner_m->get_many_by("partner_id", APContext::getParnerIDLoggedIn());
            $list_group_id = $this->get_list_group_id($selected_group_id, array ("2", "4", "5"));
            if(count($list_group_id) > 0){
                $groups = $this->group_m->get_group_by_partner($list_group_id);
            }
           
        }
        else {
        	$partner = $this->partner_m->get_many_by("partner_id", APContext::getParnerIDLoggedIn());
        	$groups = $this->group_m->get_group_by_partner(array (""));
            
        }

        $this->template->set('location_users_available', $location_users_available);

        // Display the current page
        $list_location  = array();
        foreach ($locations as $location) {
            if (in_array($location->id, $location_users_available_id)) continue;
            $list_location[] = $location;
        }
        
        // set template
        $this->template->set("languages", $languages);
        $this->template->set("currencies", $currencies);
        $this->template->set("profile_member", $profile_member);
        $this->template->set('locations', $list_location);
        $this->template->set('partner', $partner);
        $this->template->set('user', $member);
        $this->template->set('groups', $groups);
        $this->template->set('selected_group', $selected_group);
        $this->template->set('enable', $enable);
        $this->template->set('action_type', 'edit');
        $this->template->build('admin/form');
    }
    
    /**
     * Show a user preview
     * 
     * @param int $id
     *            The ID of the user.
     */
    public function preview($id = 0) {
        $user = $this->ion_auth->get_user($id);
        
        $this->template->set_layout('modal', 'admin')->set('user', $user)->build('admin/preview');
    }
    
    /**
     * Activate users Grabs the ids from the POST data (key: action_to).
     */
    public function activate() {
        // Activate multiple
        if (! ($ids = $this->input->post('action_to'))) {
            $this->session->set_flashdata('error', lang('user_activate_error'));
            redirect('admin/users');
        }
        
        $activated = 0;
        $to_activate = 0;
        foreach ( $ids as $id ) {
            if ($this->ion_auth->activate($id)) {
                $activated ++;
            }
            $to_activate ++;
        }
        $this->session->set_flashdata('success', sprintf(lang('user_activate_success'), $activated, $to_activate));
        
        redirect('admin/users');
    }
    
    /**
     * Delete group role(s)
     * 
     * @param int $id
     *            The id of the group.
     */
    public function delete() {
        $id = $this->input->get_post("id");
        
        // Check access.
        //if (! APContext::check_instance_admin_access() && ! APContext::isSupperAdminUser()) {
        //    redirect("admin");
        //}
        
        // Make sure the admin is not trying to delete themself
        if ($this->ion_auth->get_user()->id == $id) {
            $message = lang('user_delete_self_error');
            $this->error_output($message);
            return;
        }
        
        $this->user_m->update_by_many(array (
                'id' => $id 
        ), array (
                'delete_flag' => APConstants::ON_FLAG 
        ));
        
        // $success = $this->ion_auth->delete_user($id);
        $message = lang('success');
        $this->success_output($message);
        return;
    }
    
    /**
     * Username check
     * 
     * @author Ben Edmunds
     * @param string $username
     *            The username.
     * @return bool
     */
    public function _username_check($username) {
        if ($this->ion_auth->username_check($username)) {
            $this->form_validation->set_message('_username_check', lang('user_error_username'));
            return false;
        }
        return true;
    }
    
    /**
     * Email check
     * 
     * @author Ben Edmunds
     * @param string $email
     *            The email.
     * @return bool
     */
    public function _email_check($email) {
        if ($this->ion_auth->email_check($email)) {
            $this->form_validation->set_message('_email_check', lang('user_error_email'));
            return false;
        }
        return true;
    }
    
    /**
     * Check that a proper group has been selected
     * 
     * @author Stephen Cozart
     * @param int $group            
     *
     * @return bool
     */
    public function _group_check($group) {
        if (! $this->group_m->get($group)) {
            $this->form_validation->set_message('_group_check', lang('regex_match'));
            return false;
        }
        return true;
    }
    
    /**
     * Method for handling different form actions
     */
    public function change_pass() {
        $this->template->set_layout(FALSE);
        $id = $this->input->get_post("id");
        $user = $this->user_m->get_by("id", $id);
        if ($_POST) {
            $this->form_validation->set_rules($this->validation_rules03);
            if ($this->form_validation->run()) {
               
                $password = $this->input->post('password');
                $response = users_api::change_pass($id, $password);
                if ($response['status']) {
                    $this->success_output($response['message']);
                    return;
                }
                else {
                    $this->error_output($response['message']);
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
        $this->template->set('user', $user)->build('admin/change_pass');
    }
    
    /**
     * List all users
     */
    public function list_location_admin() {
        // Get input condition
        $array_condition = array ();
        
        // check access
        if (! APContext::check_instance_admin_access() || ! APContext::check_partner_admin_access()) {
            redirect("admin");
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
            $query_result = $this->user_m->get_user_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);
            
            // Process output data
            $total = $query_result ['total'];
            $datas = $query_result ['data'];
            
            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
            
            $i = 0;
            foreach ( $datas as $row ) {
                $response->rows [$i] ['id'] = $row->id;
                $response->rows [$i] ['cell'] = array (
                        $row->id,
                        $row->username,
                        $row->email,
                        $row->groupname,
                        $row->active,
                        $row->partner_name,
                        APUtils::convert_timestamp_to_date($row->created_on, 'd.m.Y'),
                        APUtils::convert_timestamp_to_date($row->last_login, 'd.m.Y'),
                        $row->id 
                );
                $i ++;
            }
            
            echo json_encode($response);
        }
        else {
            // Get all groups
            $groups = $this->group_m->get_all();
            
            // Display the current page
            $this->template->set('groups', $groups)->set('header_title', lang('header:list_user_title'))->build('admin/list_location_admin');
        }
    }
    private function check_access() {
        // Check authentication
        $admin = APContext::getAdminLoggedIn();
        if ($admin->group_id != APConstants::INSTANCE_ADMIN) {
            $this->error_output(lang('user_access_deny'));
            return;
        }
    }
    private function check_ajax_access() {
    }
    
    private function get_list_group_id($selected_group, $list){
        $result = array();
        
        foreach($list as $id){
            if(!in_array($id, $selected_group)){
                $result[] = $id;
            }
        }
        
        return $result;
    }
}