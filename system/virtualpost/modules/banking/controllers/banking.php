<?php 
if (!defined('BASEPATH'))  exit('No direct script access allowed');

class Banking extends AccountSetting_Controller {

    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     * 
     * @todo Document properly please.
     */
    public function __construct() {
        parent::__construct();

        // load the theme_example view
        $this->load->model('addresses/location_m');
        $this->load->model('office/location_office_m');
        $this->load->model('office/location_office_feature_m');
        $this->load->model('office/location_office_booking_request_m');
        $this->load->model('settings/countries_m');
        $this->load->model('email/email_m');
        $this->load->model('partner/partner_m');
        $this->load->model('users/user_m');
        $this->lang->load('office/message');
    }

    /**
     * index function.
     */
    public function index() {
        // load the theme_example view
        $this->template->build('index');
    }


}