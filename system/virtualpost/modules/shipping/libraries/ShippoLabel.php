<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ShippoLabel {

    private $shippo;
    private $from_address;
    private $to_address;
    private $package_info;
    private $tracking_number = '';
    private $service;

    public function __construct() {
        ci()->load->library('settings/settings_api');
        ci()->load->library('shipping/ShippingConfigs');
        ci()->load->library('shipping/ShippoApi');
    }
    
    public function init_label($from_address, $to_address, $package_info, $service){
        // init data.
        $this->from_address = $from_address;
        $this->to_address = $to_address;
        $this->package_info = $package_info;
        $this->service = $service;
        
        $this->shippo = ci()->shippoapi;
        $this->shippo->setCredentials($this->service->credential->auth_key);

         // init request.
        $this->addShipper();
        $this->addRecipient();
    }
    
    /**
     * create label.
     */
    public function create($filename) {
        
         $this->shippo->setPackageToShipment($this->package_info);
         $this->shippo->setCarrier($this->package_info['carrier'], $this->service->api->service_code);

         $result = $this->shippo->createLabel($this->package_info['label_size']);
         
         if ($result['status'] == 'success'){
                // get tracking number.
                $this->tracking_number = $result['data']['tracking_number'];
                 return ($result['data']['label_url']);
         }
    }

    function addShipper() {
        $senderName = ShippingConfigs::DEFAULT_SENDER_NAME;
        $senderCompanyName = ShippingConfigs::DEFAULT_SENDER_COMPANY_NAME;
        $phoneNumber = $this->from_address->phone_number;
        $email = ShippingConfigs::DEFAULT_SENDER_EMAIL;
        $stateOrProvinceCode = $this->from_address->state_code;

        $this->shippo->setShipper($senderName, $senderCompanyName, $phoneNumber, $this->from_address->street, $this->from_address->city, $stateOrProvinceCode, $this->from_address->postcode, $this->from_address->country_code, $email);
    }

    function addRecipient() {
        $recipientName = $this->to_address->shipment_address_name; 
        $companyName = $this->to_address->shipment_company; 
        $phoneNumber = $this->to_address->shipment_phone_number; 
        $stateOrProvinceCode = $this->getStateOrProvinceCode($this->to_address->shipment_city, $this->to_address->shipment_region);
        $country = settings_api::getCountryByID($this->to_address->shipment_country);
        $this->shippo->setRecipient($recipientName, $companyName, $phoneNumber, $this->to_address->shipment_street, $this->to_address->shipment_city, $stateOrProvinceCode, $this->to_address->shipment_postcode, $country->country_code, $this->to_address->shipment_email);
    }

    private function getStateOrProvinceCode($city, $region)
    {
        $region = trim($region);
        $city = trim($city);
        if ($region) {
            $stateOrProvinceCode = (strlen($region) > 2) ? substr($region, 0, 2) : $region;
        } elseif ($city) {
            $stateOrProvinceCode = (strlen($city) > 2) ? substr($city, 0, 2) : $city;
        } else {
            $stateOrProvinceCode = '';
        }

        return strtoupper($stateOrProvinceCode);
    }
    
    public function getTrackingNumber(){
        return $this->tracking_number;
    } 

}
