<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 *
 * @copyright Copyright (c) 2012-2013
 * @author Bui Duc Tien <tienbd@gmail.com>
 *         @website http://www.flightpedia.org
 * @package Addons\Shared_addons\Modules\Feedback\Controllers
 *          @created 2/19/2013
 */
class Feedback extends Public_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model(array (
                'feedback_m' 
        ));
        $this->lang->load('feedback');
        $this->load->library('form_validation');
    }
    public function index() {
    }
    /**
     * View a country
     * 
     * @param string $slug
     *            The slug of the country.
     */
    public function view($slug = '') {
        if (! $slug or ! $feedback = $this->feedback_m->get_by('slug', $slug)) {
            redirect('home');
        }
        
        if ($feedback->status != 'live' && ! $this->ion_auth->is_admin()) {
            redirect('home');
        }
        
        $this->template->title($feedback->name)->set_metadata('description', $feedback->description);
        // ->set_breadcrumb(lang('feedback.list'), 'feedback')
        
        $this->template->set_breadcrumb($feedback->name)->set('feedback', $feedback)->build('view');
    }
    
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
                    'field' => 'Name',
                    'label' => 'lang:name',
                    'rules' => 'required|max_length[255]' 
            ),
            array (
                    'field' => 'Subject',
                    'label' => 'lang:subject',
                    'rules' => 'required|max_length[255]' 
            ),
            array (
                    'field' => 'CurrentPage',
                    'label' => 'lang:currentpage',
                    'rules' => 'required|max_length[255]' 
            ),
            array (
                    'field' => 'Message',
                    'label' => 'lang:message',
                    'rules' => 'required' 
            ) 
    );
    public function add() {
        $this->template->set_layout(FALSE);
        
        if ($_POST) {
            $this->form_validation->set_rules($this->validation_rules);
            
            if ($this->form_validation->run()) {
                $feedback_id = $this->feedback_m->insert(array (
                        'Name' => $this->input->post('Name'),
                        'Subject' => $this->input->post('Subject'),
                        'CurrentPage' => $this->input->post('CurrentPage'),
                        'Message' => $this->input->post('Message'),
                        // 'VehicleID' => $this->input->post('VehicleID'),
                        // 'Make' => $this->input->post('Make'),
                        // 'Model' => $this->input->post('Model'),
                        // 'Series' => $this->input->post('Series'),
                        'CreatedOn' => now(),
                        'AuthorID' => APContext::getCustomerCodeLoggedIn() 
                ));
                
                if ($feedback_id) {
                    $message = lang('success');
                    $this->success_output($message);
                    return;
                } else {
                    $message = lang('error');
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
        $feedback = new stdClass();
        foreach ( $this->validation_rules as $rule ) {
            $feedback->{$rule ['field']} = set_value($rule ['field']);
        }
        
        // load the theme_example view
        $this->template->set('feedback', $feedback)->set('vehicle', null)->build('form');
    }
}

/* End of file feedback.php */
