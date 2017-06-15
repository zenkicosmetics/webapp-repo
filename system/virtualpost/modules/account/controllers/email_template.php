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
class email_template extends AccountSetting_Controller {
    /**
     * Array that contains the validation rules
     * 
     * @var array
     */
    protected $validation_rules = array (
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
                'email/email_m',
                'email/email_customer_m',
                'settings/countries_m'
        ));
        $this->load->library(array (
                'email/email_api',
        ));
        $this->lang->load(array (
                'email/email' 
        ));
        
        $this->load->library(array (
                'form_validation' 
        ));
    }
    
    /**
     * Show all emails
     * 
     * @access public
     * @return void
     */
    public function index() {
        $customer_id = APContext::getParentCustomerCodeLoggedIn();
        
        // Sync data from master table emails to email_customer
        email_api::init_email_template($customer_id);
        
        // If current request is ajax
        if ($this->is_ajax_request()) {
            // Get input condition
            $keyword = $this->input->get_post("enquiry");
            // $relevant_enterprise_account = $this->input->get_post("relevant_enterprise_account");
            $language = $this->input->get_post("language");

            $array_condition = array ();
            if (! empty($keyword)) {
                $array_condition ["( email_customer.slug LIKE '%" . $keyword . "%'  OR email_customer.subject LIKE '%".$keyword."%' )"] = null;
            }
            // $array_condition['relevant_enterprise_account'] = $relevant_enterprise_account;
            $array_condition['email_customer.language'] = $language;
            $array_condition['email_customer.relevant_enterprise_account'] = APConstants::ON_FLAG;
            $array_condition['email_customer.customer_id'] = $customer_id;
        
            // Get paging input
            $input_paging = $this->post_paging_input();
            // Call search method
            $query_result = $this->email_customer_m->get_email_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);
            
            // Process output data
            $total = $query_result ['total'];
            $datas = $query_result ['data'];
            
            // Get output response
            $response = $this->get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
            
            $i = 0;
            foreach ( $datas as $row ) {
                $tempate_code = APUtils::buildTemplateCode($row->code, $row->customer_id, $language);
                $response->rows [$i] ['id'] = $row->id;
                $response->rows [$i] ['cell'] = array (
                    $row->id,
                    $tempate_code,
                    $row->slug,
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
            $this->template->build('email/index');
        }
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
        $customer_id = APContext::getParentCustomerCodeLoggedIn();
        
        // The user needs to be able to edit pages.
        // role_or_die('email', 'edit');
        
        // Retrieve the page data along with its chunk data as an array.
        $email = $this->email_customer_m->get($id);
        
        // Got page?
        if (! $email or empty($email)) {
            // Maybe you would like to create one?
            $this->session->set_flashdata('error', lang('email.not_found_error'));
            redirect('account/email_template/index');
        }
        
        if ($_POST) {
            // Validate the results
            $this->form_validation->set_rules($this->validation_rules);
            if ($this->form_validation->run()) {
                // Update the comment
                $this->email_customer_m->update_by_many(array(
                    'id' => $id,
                    'customer_id' => $customer_id
                ), array(
                    'subject' => $this->input->post('subject'),
                    'language' => $this->input->post('language'),
                    'description' => $this->input->post('description'),
                    'content' => $this->input->post('content') 
                ));
                
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
        $this->template->set('action_type', 'edit');
        $this->template->title(sprintf(lang('email.edit_title'), $email->id))->append_metadata($this->load->view('fragments/wysiwyg', array (), TRUE))->set('email', $email)->build('email/form');
    }
}

