<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Phone_Settings extends Phone_Controller
{
    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     */
    public function __construct()
    {
        parent::__construct();

        
    }
    
    public function index(){
        $this->template->build('index');
    }

}