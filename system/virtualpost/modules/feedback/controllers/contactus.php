<?php

if (! defined('BASEPATH'))
    exit('No direct script access allowed');
class Contactus extends Public_Controller {
    
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
                    'field' => 'YourName',
                    'label' => 'lang:your_name',
                    'rules' => 'required|max_length[255]' 
            ),
            array (
                    'field' => 'YourEmail',
                    'label' => 'lang:your_email',
                    'rules' => 'required|max_length[255]|valid_email' 
            ),
            array (
                    'field' => 'CompanyName',
                    'label' => 'lang:company_name',
                    'rules' => 'required|max_length[255]' 
            ),
            array (
                    'field' => 'PhoneNumber',
                    'label' => 'lang:phone_number',
                    'rules' => 'required|max_length[15]' 
            ),
            array (
                    'field' => 'Message',
                    'label' => 'lang:message',
                    'rules' => 'required' 
            ) 
    );
    
    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     * 
     * @todo Document properly please.
     */
    public function __construct() {
        parent::__construct();
        
        // Load model
        $this->load->model('feedback/contactus_m');
        $this->lang->load('feedback');
        $this->load->library('form_validation');
    }
    public function index() {
        if ($_POST) {
            $this->form_validation->set_rules($this->validation_rules);
            
            if ($this->form_validation->run()) {
                $feedback_id = $this->contactus_m->insert(array (
                        'YourName' => $this->input->post('YourName'),
                        'YourEmail' => $this->input->post('YourEmail'),
                        'CompanyName' => $this->input->post('CompanyName'),
                        'PhoneNumber' => $this->input->post('PhoneNumber'),
                        'Message' => $this->input->post('Message'),
                        'CreatedDate' => now() 
                ));
                
                if ($feedback_id) {
                    $this->session->set_flashdata('success', lang('success'));
                    redirect('feedback/contactus');
                } else {
                    $this->session->set_flashdata('error', lang('error'));
                    redirect('feedback/contactus');
                }
            }
        }
        
        // Loop through each validation rule
        $contactus = new stdClass();
        foreach ( $this->validation_rules as $rule ) {
            $contactus->{$rule ['field']} = set_value($rule ['field']);
        }
        
        // load the theme_example view
        $this->template->set('contactus', $contactus)->build('contactus');
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */