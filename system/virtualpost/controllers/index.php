<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * The admin class is basically the main controller for the backend.
 */
class Index extends Public_Controller {
    /**
     * Constructor method
     */
    public function __construct() {
        parent::__construct();
        
        $this->load->helper('users/user');
    }
    
    /**
     * Show the control panel
     */
    public function index() {
        redirect('home');
    }
    
    /**
     * Log in
     */
    public function login() {
        // Set the validation rules
        $this->validation_rules = array (
                array (
                        'field' => 'Email',
                        'label' => lang('email_label'),
                        'rules' => 'required|callback__check_login' 
                ),
                array (
                        'field' => 'Password',
                        'label' => lang('password_label'),
                        'rules' => 'required' 
                ) 
        );
        
        // Call validation and set rules
        $this->load->library('form_validation');
        $this->form_validation->set_rules($this->validation_rules);
        
        // If the validation worked, or the user is already logged in
        if ($this->form_validation->run() or $this->ion_auth->logged_in()) {
            redirect('admin/dashboard');
        }
        
        $this->template->set_layout(FALSE)->build('login');
    }
    
    /**
     * Logout
     */
    public function logout() {
        $this->load->language('users/user');
        $this->ion_auth->logout();
        $this->session->set_flashdata('success', lang('user_logged_out'));
        redirect('admin/login');
    }
    
    /**
     * Callback From: login()
     * 
     * @param string $email
     *            The Email address to validate
     * @return bool
     */
    public function _check_login($email) {
        if ($this->ion_auth->login($email, $this->input->post('Password'), ( bool ) $this->input->post('remember_me'))) {
            return true;
        }
        
        $this->form_validation->set_message('_check_login', $this->ion_auth->errors());
        return false;
    }
}