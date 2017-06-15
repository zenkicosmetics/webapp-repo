<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @copyright Copyright (c) 2012-2013
 * @author Bui Duc Tien <tienbd@gmail.com>
 *         @website http://www.flightpedia.org
 * @package Addons\Shared_addons\Modules\Email\Controllers\Admin
 *          @created 2/19/2013
 */
class Admin extends Admin_Controller {
    /**
     * The current active section
     * 
     * @var string
     */
    protected $section = 'email';
    
    /**
     * Array that contains the validation rules
     * 
     * @var array
     */
    protected $validation_rules = array (
            'slug' => array (
                    'field' => 'slug',
                    'label' => 'lang:email.slug_label',
                    'rules' => 'trim|required|max_length[127]' 
            ),
            'subject' => array (
                    'field' => 'subject',
                    'label' => 'lang:email.subject_label',
                    'rules' => 'trim|required' 
            ),
            'language' => array (
                    'field' => 'language',
                    'label' => 'lang:email.language',
                    'rules' => 'trim|required' 
            ),
            'relevant_enterprise_account' => array (
                    'field' => 'relevant_enterprise_account',
                    'label' => 'lang:email.relevant_enterprise_account',
                    'rules' => 'trim|required' 
            ),
            'description' => array (
                    'field' => 'description',
                    'label' => 'lang:email.description_label',
                    'rules' => 'trim|required|max_length[500]' 
            ),
            'content' => array (
                    'field' => 'content',
                    'label' => 'lang:email.content_label',
                    'rules' => 'trim|required' 
            ) 
    );
    
    /**
     * The constructor
     */
    public function __construct() {
        parent::__construct();
        
        $this->load->model(array (
            'email_m',
            'email_customer_m',
            'settings/countries_m'
        ));
        $this->lang->load(array (
            'email' 
        ));
        
        $this->load->library(array (
            'form_validation' 
        ));
        
        // Date ranges for select boxes
        $this->template->set('orders', array_combine($orders = range(0, 15), $orders));
    }
    
    /**
     * Show all emails
     * 
     * @access public
     * @return void
     */
    public function index() {
        
        // If current request is ajax
        if ($this->is_ajax_request()) {
            // Get input condition
            $keyword = $this->input->get_post("enquiry");
            $relevant_enterprise_account = $this->input->get_post("relevant_enterprise_account");
            $language = $this->input->get_post("language");
            
            // declare array condition.
            $array_condition = array ();
            
            // Get paging input
            $input_paging = $this->post_paging_input();
            $array_condition['language'] = $language;
            $customer_id = '';
            if($relevant_enterprise_account){
                if (! empty($keyword)) {
                    $array_condition ["( slug LIKE '%" . $keyword . "%'  OR subject LIKE '%".$keyword."%' OR customer_id like '%".$keyword."%'  )"] = null;
                }
                
                // Call search method
                $query_result = $this->email_customer_m->get_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);
            } else {
                if (! empty($keyword)) {
                    $array_condition ["( emails.slug LIKE '%" . $keyword . "%'  OR emails.subject LIKE '%".$keyword."%' )"] = null;
                }
                
                // Call search method
                $query_result = $this->email_m->get_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);
            }

            // Process output data
            $total = $query_result ['total'];
            $datas = $query_result ['data'];
            
            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
            
            $i = 0;
            foreach ( $datas as $row ) {
                $customer_id = '';
                $used_by = 'Clevvermail';
                if($relevant_enterprise_account == 1){
                    $customer_id = $row->customer_id;
                    $used_by = 'C' .substr('0000000' .$row->customer_id, -8);
                }
                $tempate_code = APUtils::buildTemplateCode($row->code, $customer_id, $language);
                $response->rows [$i] ['id'] = $row->id;
                $response->rows [$i] ['cell'] = array (
                    $row->id,
                    $customer_id,
                    $tempate_code,
                    ($row->relevant_enterprise_account == 1)? "Yes" : "No",
                    $row->slug,
                    $used_by,
                    $row->subject,
                    $row->description,
                    $row->id 
                );
                $i ++;
            }
            
            echo json_encode($response);
        }
        else {
            // Languages
            $languages = $this->countries_m->getAllLanguagesForDropDownList();
            // Currencies
            $this->template->set("languages", $languages);
            $this->template->build('admin/email/index');
        }
    }
    
    /**
     * Add new email
     * 
     * @access public
     * @return void
     */
    public function add() {
        // The user needs to be able to add email.
        $relevant_enterprise_account = $this->input->get_post("relevant_enterprise_account");
        $language = $this->input->get_post("language");
        $this->form_validation->set_rules($this->validation_rules);
        if ($_POST) {
            if ($this->form_validation->run()) {
                $id = $this->email_m->insert(array (
                        'slug' => $this->input->post('slug'),
                        'subject' => $this->input->post('subject'),
                        'language' => $this->input->post('language'),
                        'relevant_enterprise_account' => $this->input->post('relevant_enterprise_account'),
                        'description' => $this->input->post('description'),
                        'content' => $this->input->post('content') 
                ));
                
                $code = sprintf('%1$04d', $id);
                $this->email_m->update_by_many(array(
                    'id' => $id
                ), array(
                    "code" => $code
                ));
                
                $message = lang('email.add_template_success');
                $this->success_output($message);
                return;
            }
            else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }
        $email = new stdClass();
        // Go through all the known fields and get the post values
        foreach ( $this->validation_rules as $key => $field ) {
            $email->$field ['field'] = set_value($field ['field']);
        }
        $email->relevant_enterprise_account = $relevant_enterprise_account;
        $email->language = $language;
        
        // Languages
        $languages = $this->countries_m->getAllLanguagesForDropDownList();
        // Currencies
        $this->template->set("languages", $languages);
        $email->id = '';
        $email->content = "<br />";
        $this->template->set('action_type', 'add');
        $this->template->title(sprintf(lang('email.add_title')))->set('active_section', 'email')->append_metadata($this->load->view('fragments/wysiwyg', array (), TRUE))->set('email', $email)->build('admin/email/form');
    }
    
    /**
     * Edit email with $id
     * 
     * @access public
     * @param int $id
     *            the ID of the email to edit
     * @return void
     */
    public function edit() {
        // We are lost without an id. Redirect to the pages index.
        $id = $this->input->get_post('id');
        $customer_id = $this->input->get_post('customer_id');
        
        // The user needs to be able to edit pages.
        // role_or_die('email', 'edit');
        
        // Retrieve the page data along with its chunk data as an array.
        if (empty($customer_id)) {
            $email = $this->email_m->get($id);
        } else {
            $email = $this->email_customer_m->get_by_many(array(
                'id' => $id,
                'customer_id' => $customer_id
            ));
        }
        
        
        // Got page?
        if (! $email or empty($email)) {
            // Maybe you would like to create one?
            $this->session->set_flashdata('error', lang('email.not_found_error'));
            redirect('admin/email');
        }
        
        if ($_POST) {
            // Validate the results
            $this->form_validation->set_rules($this->validation_rules);
            if ($this->form_validation->run()) {
                unset($email->EmailID);
                $email->slug = $this->input->post('slug');
                $email->description = $this->input->post('description');
                $email->content = $this->input->post('content');
                $email->subject = $this->input->post('subject');
                $email->language = $this->input->post('language');
                $email->relevant_enterprise_account = $this->input->post('relevant_enterprise_account');
                
                // Update the comment
                if (empty($customer_id)) {
                    $this->email_m->update($id, $email);
                } else {
                    $this->email_customer_m->update_by_many(array(
                        'id' => $id,
                        'customer_id' => $customer_id
                    ), array(
                        'content' => $this->input->post('content')
                    ));
                }
                
                $message = lang('email.edit_template_success');
                $this->success_output($message);
                return;
            }
            else {
                $errors = $this->form_validation->error_json();
                echo json_encode($errors);
                return;
            }
        }
        
        // Loop through each rule
        foreach ( $this->validation_rules as $rule ) {
            if ($this->input->post($rule ['field']) !== FALSE) {
                $email->{$rule ['field']} = $this->input->post($rule ['field']);
            }
        }
        
        // Languages
        $languages = $this->countries_m->getAllLanguagesForDropDownList();
        // Currencies
        $this->template->set("languages", $languages);
        $this->template->set("customer_id", $customer_id);
        $this->template->set('action_type', 'edit');
        $this->template->title(sprintf(lang('email.edit_title'), $email->id))->append_metadata($this->load->view('fragments/wysiwyg', array (), TRUE))->set('email', $email)->build('admin/email/form');
    }
    /**
     * Helper method to determine what to do with selected items from form post
     * 
     * @access public
     * @return void
     */
    public function action() {
        switch ($this->input->post('btnAction')) {
            case 'publish' :
                $this->publish();
                break;
            
            case 'delete' :
                $this->delete();
                break;
            
            default :
                redirect('admin/email');
                break;
        }
    }
    
    /**
     * Publish email
     * 
     * @access public
     * @param int $id
     *            the ID of the email to make public
     * @return void
     */
    public function publish($id = 0) {
        // We are lost without an id. Redirect to the pages index.
        $id or redirect('admin/email');
        
        role_or_die('email', 'publish');
        
        $email = $this->email_m->get($id);
        
        if (! $email or empty($email)) {
            // Maybe you would like to create one?
            $this->session->set_flashdata('error', lang('email.not_found_error'));
            redirect('admin/email');
        }
        $this->email_m->publish($id);
        
        // Wipe cache for this model, the content has changed
        $this->pyrocache->delete('email_m');
        // Some posts have been published
        $this->session->set_flashdata('success', sprintf($this->lang->line('email.publish_success'), $email->name));
        
        redirect('admin/email');
    }
    /**
     * For user have role delete email
     * 
     * @param int $id
     *            the ID of the email to delete
     * @return bool
     */
    public function delete($id = 0) {
        $customer_id = $this->input->get_post('customer_id');
        
        if(!empty($customer_id)){
            $this->error_output("You can not delete email template of enterprise customers");
            return;
        }
        if ($this->email_m->delete($id)) {
            $message = lang('email.delete_template_success');
            $this->success_output($message);
            return;
        }
        $message = lang('email.delete_template_fail');
        $this->success_output($message);
        return;
    }
  
}

