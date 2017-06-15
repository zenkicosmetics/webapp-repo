<?php

if (! defined('BASEPATH'))
    exit('No direct script access allowed');
class admin extends Admin_Controller {
    
    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     * 
     * @todo Document properly please.
     */
    public function __construct() {
        parent::__construct();
        
        // load the theme_example view
        //$this->load->library('phonenumber/sonetel');
        
        // load model
        $this->load->model(array(
            'phones/phone_area_code_m',
            'settings/countries_m',
        ));
    }
    
    /**
     * Index Page for this controller.
     * Maps to the following URL
     * http://example.com/index.php/welcome
     * - or -
     * http://example.com/index.php/welcome/index
     * - or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * 
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index() {
        //$result =  $this->sonetel->get_list_sub_account();
        //$result_obj = json_decode($result);
        //var_dump($result_obj);
        
        //$result = $this->sonetel->create_sub_account('nguyen002', 'nguyen002@gmail.com', 'Nguyen', 'Abc@1234');
        // var_dump($result);
    	echo "OK";
    }
    
    /**
     * Default page for 404 error.
     */
    public function page_construction() {
        // load the theme_example view
        $this->template->build('page_construction');
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */