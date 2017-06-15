<?php defined('BASEPATH') or exit('No direct script access allowed');

class phone extends APIPhone_Controller
{
    public function __construct()
    {
        // set error repoting to false for proper formatting of json data
        parent::__construct();

        $this->load->library('Payone_lib');
        $this->load->config('payone');
        
        $this->load->model(array(
            'account/account_m',
            'users/mobile_session_m',
            'phones/pricing_phones_number_m',
            'phones/pricing_phones_outboundcalls_m',
        ));

        ci()->load->library(array(
            'api/mobile_api',
            "settings/settings_api",
            "phones/phones_api"
        ));
       
        $this->lang->load(array(
            'account/account',
            'api',
        ));
    }

    /**
     * Verify Web Services are working. void
     *
     * @return array {'code' => 1000, 'message' => 'Working'}
     */
    public function index()
    {
        $data = array(
            'code' => 1000,
            'message' => 'Working',
            'result' => 'index'
        );
        $this->api_success_output($data);
        exit();
    }
    
    /**
     * Gets phone pricing 
     */
    public function get_phone_pricing(){
        // get params
        $minutes = $this->input->get_post('minute');
        $number = $this->input->get_post('number');
        $country_code = $this->input->get_post('country_code');
        $area_code = $this->input->get_post('area_code');
        $country_forwarding = $this->input->get_post('forwarding_country_code');
        
        if(empty($number) || empty($country_code) || empty($area_code)){
            $this->api_error_output('Number, country code, area code must be required!');
            return;
        }
        
        // get phone pricing calculator.
        $result  = phones_api::get_phone_pricing($minutes, $number, $country_code, $area_code, $country_forwarding);
        
        $this->api_success_output('', $result);
        return;
    }

    /**
     * Gets phone country
     */
    public function get_phone_country(){
        $result = phones_api::get_phone_country();
        
        $this->api_success_output('', $result);
        return;
    }
    
    /**
     * Gets area by country.
     */
    public function get_phone_area(){
        // Gets country code
        $country_code = $this->input->get_post('country_code');
        $result = phones_api::get_phone_area_code($country_code);
        
        $this->api_success_output('', $result);
        return;
    }
    
    /**
     * get list phone number by country and area.
     */
    public function get_list_phone_number(){
        $country_code = $this->input->get_post('country_code');
        $area_code = $this->input->get_post('area_code');
        
        $result = phones_api::get_list_phone_number_by($country_code, $area_code);
        
        $this->api_success_output('', $result);
        return;
    }
}

?>
