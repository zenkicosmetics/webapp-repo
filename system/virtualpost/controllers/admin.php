<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * The admin class is basically the main controller for the backend.
 */
class Admin extends Admin_Controller
{

    /**
     * Constructor method
     */
    public function __construct ()
    {
        parent::__construct();
        
        $this->load->model('users/user_m');
        $this->load->model('users/group_user_m');
        $this->load->model('instances/supper_admin_m');
        $this->load->helper('users/user');
        $this->load->library('users/users_api');
    }

    /**
     * Show the control panel
     */
    public function index ()
    {
        $this->template->build('dashboard');
    }

    /**
     * Show the control panel
     */
    public function dashboard ()
    {
        $this->template->build('dashboard');
    }

    /**
     * Log in
     */
    public function login ()
    {
        // Set the validation rules
        $this->validation_rules = array(
                array(
                        'field' => 'email',
                        'label' => lang('email_label'),
                        'rules' => 'required|callback__check_login'
                ),
                array(
                        'field' => 'password',
                        'label' => lang('password_label'),
                        'rules' => 'required'
                )
        );
        
        // Call validation and set rules
        $this->load->library('form_validation');
        $this->form_validation->set_rules($this->validation_rules);

        // If the validation worked, or the user is already logged in
        if (($this->form_validation->run() or $this->ion_auth->logged_in())) {
            
            $user_login = APContext::getAdminLoggedIn();
            
            if (! empty($user_login)) {
                if (empty($user_login->display_name)) {
                    $user_login->display_name = $user_login->username;
                }
                users_api::set_group_user($user_login);

                if (APContext::isServiceParner()) {
                    redirect('cases/todo');
                }
                else {
                    redirect('scans/todo');
                }
            }
        }
        
        $this->template->set_layout(FALSE)->build('login');
    }

    /**
     * Logout
     */
    public function logout ()
    {
        $this->load->language('users/user');
        $this->ion_auth->logout();
        
        // delete session.
        ci()->session->unset_userdata(APConstants::SESSION_GROUP_USERS_ROLE);
        ci()->session->unset_userdata(APConstants::SESSION_USERADMIN_KEY);
        
        $this->session->set_flashdata('success', lang('user_logged_out'));
        redirect('admin/login');
    }

    /**
     * Login with email address and password
     */
    public function ajax_login ()
    {
        $this->template->set_layout(FALSE);
        $this->error_output('session time out', array(
                'code' => '999'
        ));
    }

    /**
     * Callback From: login()
     * 
     * @param string $email
     *            The Email address to validate
     * @return bool
     */
    public function _check_login ($email)
    {
        $password    = $this->input->post('password');
        $remember_me = (bool) $this->input->post('remember_me');
        $check_login = users_api::check_login ($email, $password, $remember_me);

        if($check_login['status']){

            return true;
        }
        else{
            $this->form_validation->set_message('_check_login', $check_login['message']);
            return false;
        }
    }
}