<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin controller for the settings module
 */
class Servers extends Admin_Controller {
    
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
        
        $this->load->library('form_validation');
        $this->lang->load('terms_services');
    }
    
    /**
     * Index method, lists all generic settings
     * 
     * @return void
     */
    public function index() {
        $this->template->build ( 'page_construction' );
    }
    
    public function domain(){
        // TODO:
        $this->template->build ( 'page_construction' );
    }
    
    
    public function database(){
        //  TODO:
        $this->template->build ( 'page_construction' );
    }
    
    public function storage(){
        //  TODO:
        $this->template->build ( 'page_construction' );
    }
}

/* End of file admin.php */