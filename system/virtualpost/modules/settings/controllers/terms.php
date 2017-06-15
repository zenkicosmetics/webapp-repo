<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin controller for the settings module
 */
class Terms extends Admin_Controller {
    
    /**
     * Validation array
     * 
     * @var array
     */
    private $validation_rules = array ();
    
    /**
     * Constructor method
     * 
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->load->model(array(
         'terms_service_m',
         'settings_m',
         'customers/customer_m'
        ));
        $this->load->library('form_validation');
        $this->lang->load('terms_services');
    }
    
    /**
     * Index method, lists all generic settings
     * 
     * @return void
     */
    public function index() {
        // Get input condition
        $array_condition = array ();
        $type = $this->input->get_post('type');
        $array_condition ['type'] = $type;
        
        // update term and condition for enteprrise customer
        $array_condition ['customer_id'] = 0;
        $customer_id = $this->input->post('customer_id');
        if(!empty($customer_id)){
            $array_condition ['customer_id'] = $customer_id;
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
            $query_result = $this->terms_service_m->get_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);
            
            // Process output data
            $total = $query_result ['total'];
            $datas = $query_result ['data'];
            
            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
            #1058 add multi dimension capability for admin
            $date_format = APUtils::get_date_format_in_user_profiles();
            
            $i = 0;
            foreach ( $datas as $row ) {
                $url = APContext::getFullBasePath() . 'customers/term_of_service';
                if ($type == '2') {
                    $url = APContext::getFullBasePath() . 'customers/privacy';
                }
                if ($row->use_flag != '1') {
                    $url = $row->file_name;
                }
                $response->rows [$i] ['id'] = $row->id;
                $response->rows [$i] ['cell'] = array (
                    $row->id,
                    $url,
                    APUtils::viewDateFormat($row->created_date, $date_format.APConstants::TIMEFORMAT_OUTPUT02),
                    $row->use_flag,
                    $row->id 
                );
                $i ++;
            }
            
            echo json_encode($response);
        }
        else {
            // Display the current page
            $this->template->build('settings/terms');
        }
    }
    public function terms_service() {
        // Display the current page
        $this->template->set("type", 'system');
        $this->template->build('settings/terms/terms');
    }
    public function privacy() {
        // Display the current page
        $this->template->build('settings/terms/privacy');
    }
    /**
     * Index method, lists all generic settings
     * 
     * @return void
     */
    public function add_terms() {
        // $this->template->set_layout(FALSE);
        $id = '';
        $content = '';
        if ($_POST) {
            $use_flag = $this->input->post('use_flag');
            $content = $this->input->post('content');
            
            // Get current main object
            $main = $this->terms_service_m->get_system_term_service(array (
                "type" => '1',
                "use_flag" => '1',
                "customer_id" => 0
            ));
            
            if ($main) {
                // Change file name
                $this->terms_service_m->update_by_many(array (
                        "id" => $main->id 
                ), array (
                        "file_name" => $main->file_name . '_Old_' . APUtils::convert_timestamp_to_date($main->created_date, 'dmYHi') 
                ));
            }
            
            // Update use flag of other record to '0'
            // Insert new record
            $this->terms_service_m->update_by_many(array (
                "type" => '1',
                "customer_id" => 0
            ), array (
                "use_flag" => '0' 
            ));
            
            // Insert new record
            $this->terms_service_m->insert(array (
                    "type" => '1',
                    "file_name" => "Terms&Conditions",
                    "use_flag" => '1',
                    "created_date" => now(),
                    "content" => $content . '<br/><br/> as of ' . date('d.m.Y') 
            ));
            
            $message = lang('terms_service_save_success');
            $this->success_output($message);
            return;
        }
        $this->template->append_metadata($this->load->view('fragments/wysiwyg', array (), TRUE));
        $this->template->set('content', $content)->set('id', $id);
        // Display the current page
        $this->template->build('terms/add_terms');
    }
    
    /**
     * Index method, lists all generic settings
     * 
     * @return void
     */
    public function edit_terms() {
        $id = $this->input->get_post('id');
        $terms = $this->terms_service_m->get($id);
        $content = '';
        if (! empty($terms)) {
            $content = $terms->content;
        }
        if ($_POST) {
            $use_flag = $this->input->post('use_flag');
            $content = $this->input->post('content');
            
            $need_customer_approval_flag = (int) $this->input->post('need_customer_approval_flag');
            $message_to_customer_flag = (int)  $this->input->post('message_to_customer_flag');
            $message_to_customer = $this->input->post('message_to_customer');
            $effective_date = $this->input->post('effective_date');
            if($need_customer_approval_flag && empty($effective_date)){
                $this->error_output("Please select an effective date.");
                return;
            }
            if(!empty($effective_date)){
                $effective_date = strtotime($effective_date." 23:59:59");
                if( (now() >  $effective_date) && ($need_customer_approval_flag) ){
                    $this->error_output("You need to select the effective date greater than or equal to the current date.");
                    return;
                }
            }else{
                $effective_date = NULL;
            }
            
            if(empty($message_to_customer)) $message_to_customer = "";
            
            if($need_customer_approval_flag == 1){
                // set customers need confirm term & condition.
               ci()->customer_m->update_by_many(array(
                    "status" => APConstants::OFF_FLAG,
                    "parent_customer_id" => null
                ), array(
                    "accept_terms_condition_flag" => APConstants::OFF_FLAG
                ));
            }
            
            // Insert new record
            $this->terms_service_m->update_by_many(array (
                "type" => '1',
                "customer_id" => 0
            ), array (
                "use_flag" => '0' 
            ));
            
            // Insert new record
            $this->terms_service_m->insert(array (
                    "type" => '1',
                    "file_name" => "Terms&Conditions",
                    "use_flag" => '1',
                    "created_date" => now(),
                    "content" => $content . '<br/><br/> as of ' . date('d.m.Y'),
                    "message_to_customer" => $message_to_customer,
                    "need_customer_approval_flag" => $need_customer_approval_flag,
                    "notify_flag" => $need_customer_approval_flag,
                    "message_to_customer_flag" => $message_to_customer_flag,
                    "effective_date" => $effective_date
            ));
            
            // Update use flag of other record to '0'
            // Insert new record
            $this->terms_service_m->update_by_many(array (
                    "id" => $id
            ), array (
                    "file_name" => "Terms&Conditions_Old_" . date('dmYHi'),
                    "use_flag" => '0' 
            ));
            $message = lang('terms_service_save_success');
            $this->success_output($message);
            return;
        }
        $this->template->append_metadata($this->load->view('fragments/wysiwyg', array (), TRUE));
        $this->template->set('action', 'edit');
        $this->template->set('terms', $terms);
        $this->template->set('content', $content)->set('id', $id);
        // Display the current page
        $this->template->build('terms/add_terms');
    }
    
    /**
     * Add privacy
     * 
     * @return void
     */
    public function add_privacy() {
        $id = '';
        $content = '';
        
        if ($_POST) {
            
            $use_flag = $this->input->post('use_flag');
            
            // Get current main object
            $main = $this->terms_service_m->get_system_term_service(array (
                    "type" => '2',
                    "use_flag" => '1' 
            ));
            
            if ($main) {
                // Change file name
                $this->terms_service_m->update_by_many(array (
                        "id" => $main->id 
                ), array (
                        "file_name" => $main->file_name . '_Old_' . APUtils::convert_timestamp_to_date($main->created_date, 'dmYHi') 
                ));
            }
            
            // Update use flag of other record to '0'
            // Insert new record
            $this->terms_service_m->update_by_many(array (
                "type" => '2',
                "customer_id" => 0
            ), array (
                    "use_flag" => '0' 
            ));
            
            $content = $this->input->post('content');
            // Insert new record
            $this->terms_service_m->insert(array (
                    "type" => '2',
                    "file_name" => "Privacy&DataProtection",
                    "use_flag" => '1',
                    "created_date" => now(),
                    "content" => $content . '<br/><br/> as of ' . date('d.m.Y') 
            ));
            
            $message = lang('privacy_save_success');
            $this->success_output($message);
            return;
        }
        $this->template->append_metadata($this->load->view('fragments/wysiwyg', array (), TRUE));
        $this->template->set('content', $content)->set('id', $id);
        // Display the current page
        $this->template->build('terms/add_privacy');
    }
    
    /**
     * Index method, lists all generic settings
     * 
     * @return void
     */
    public function edit_privacy() {
        $id = $this->input->get_post('id');
        $terms = $this->terms_service_m->get($id);
        $content = '';
        if (! empty($terms)) {
            $content = $terms->content;
        }
        if ($_POST) {
            $use_flag = $this->input->post('use_flag');
            $content = $this->input->post('content');
            // Insert new record
            $this->terms_service_m->update_by_many(array (
                "type" => '2',
                "customer_id" => 0
            ), array (
                "use_flag" => '0' 
            ));
            
            // Insert new record
            $this->terms_service_m->insert(array (
                    "type" => '2',
                    "file_name" => "Privacy&DataProtection",
                    "use_flag" => '1',
                    "created_date" => now(),
                    "content" => $content . '<br/><br/> as of ' . date('d.m.Y') 
            ));
            
            // Update use flag of other record to '0'
            // Insert new record
            $this->terms_service_m->update_by_many(array (
                    "id" => $id 
            ), array (
                    "file_name" => "Privacy&DataProtection_Old_" . date('dmYHi'),
                    "use_flag" => '0' 
            ));
            $message = lang('privacy_save_success');
            $this->success_output($message);
            return;
        }
        $this->template->append_metadata($this->load->view('fragments/wysiwyg', array (), TRUE));
        $this->template->set('action', 'edit');
        $this->template->set('terms', $terms);
        $this->template->set('content', $content)->set('id', $id);
        // Display the current page
        $this->template->build('terms/add_privacy');
    }
    
    /**
     * Delete group role(s)
     * 
     * @param int $id
     *            The id of the group.
     */
    public function delete() {
        $id = $this->input->get_post("id");
        $type = $this->input->get_post("type");
        $current = $this->terms_service_m->get_system_term_service(array (
                'id' => $id,
                'type' => $type 
        ));
        $success = $this->terms_service_m->delete_by_many(array (
                'id' => $id,
                'type' => $type 
        ));
        
        // Delete physical file
        if ($current && ! empty($current->full_path) && file_exists($current->full_path)) {
            unlink($current->full_path);
        }
        
        if ($success) {
            $message = lang('success');
            $this->success_output($message);
            return;
        }
        else {
            $message = lang('error');
            $this->error_output($message);
            return;
        }
    }
    
    /**
     * list enteprrise term and condition.
     */
    public function enterprise_tc(){
        $list_enterprise_customer = $this->customer_m->order_by('customer_id', 'desc')->get_many_by_many(array(
            "status" => APConstants::OFF_FLAG,
            "account_type" => APConstants::ENTERPRISE_CUSTOMER,
            "parent_customer_id" => null
        ));
        
        // Display the current page
        $this->template->set("type", 'enterprise');
        $this->template->set("list_enterprise_customer", $list_enterprise_customer);
        $this->template->build('settings/terms/terms');
    }
    
    
}

/* End of file admin.php */