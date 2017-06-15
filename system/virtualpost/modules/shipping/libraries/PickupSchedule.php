<?php

defined('BASEPATH') or exit('No direct script access allowed');

define('Newline', "<br />");

class PickupSchedule {

    private $path_to_wsdl = '';
    private $client;
    private $request;
    
    private $shipping_from;
    private $package_info;
    
    private $account_number;
    private $fedex_meter_number;
    private $credential_key = '';
    private $credential_password = '';
    private $service;
    private $message = '';
    
    public function __construct() {
        ini_set("soap.wsdl_cache_enabled", "0");
        
        // load lib
        ci()->load->library('settings/settings_api');
        ci()->load->library('shipping/ShippingConfigs');
    }
    
    public function initVariables($shipping_from, $package_info, $service){
        // init data.
        $this->shipping_from = $shipping_from;
        $this->package_info = $package_info;
        $this->path_to_wsdl = APPPATH . 'libraries/shipping/PickupService_v3.wsdl';
        $this->service = $service;
        
        // init account setting
        /*if(ENVIRONMENT == 'production'){
            $this->account_number = ShippingConfigs::FEDEX_PRODUCTION_ACCOUNT_NUMBER;
            $this->fedex_meter_number = ShippingConfigs::FEDEX_PRODUCTION_METER_NUMBER;
            $this->credential_key = ShippingConfigs::FEDEX_PRODUCTION_KEY;
            $this->credential_password = ShippingConfigs::FEDEX_PRODUCTION_PASSWORD;
        }else{
            $this->account_number = ShippingConfigs::FEDEX_TEST_ACCOUNT_NUMBER;
            $this->fedex_meter_number = ShippingConfigs::FEDEX_TEST_METER_NUMBER;
            $this->credential_key = ShippingConfigs::FEDEX_TEST_KEY;
            $this->credential_password = ShippingConfigs::FEDEX_TEST_PASSWORD;
        }*/
        $this->account_number = $service->account_no;
        $this->fedex_meter_number = $service->meter_no;
        $this->credential_key = $service->auth_key;
        $this->credential_password = $service->password;

         // init request.
        $this->init_client();
    }

    private function init_client() {
        $this->client = new SoapClient($this->path_to_wsdl, array('trace' => 1));
    }

    /**
     * Init available date request 
     */
    public function initAvailDateRequest() {
        $this->request = array();
        $this->setCredential();

        $this->request['TransactionDetail'] = array('CustomerTransactionId' => now());
        $this->request['Version'] = array(
            'ServiceId' => 'disp', 
            'Major' => 3, 
            'Intermediate' => 0, 
            'Minor' => 0
        );
        
        // Set pickup request type
        $this->request['PickupRequestType'] = array("FUTURE_DAY", "SAME_DAY");
        
        // Set Carriers
        $this->request['Carriers'] = array("FDXE", "FDXG");
        $this->request['ShipmentAttributes'] = array(
            "Dimensions" => array(
                "Length" =>  $this->package_info['length'],
                "Width" =>  $this->package_info['width'],
                "Height" =>  $this->package_info['height'],
                "Units" =>  'CM',
                "UnitsSpecified" =>  true
            ),
            "Weight" => array(
                "Value" => $this->package_info['weight'] / 1000,
                "ValueSpecified" => true,
                "Units" => "KG",
                "UnitsSpecified" => true
            )
        );
        
        // $DispatchDate = date("Y-m-d", mktime(8, 0, 0, date("m")  , date("d")+1, date("Y")));
        $CustomerCloseTime =  mktime(18, 0, 0, date("m"), date("d")+1, date("Y"));
        $PackageReadyTime =  mktime(10, 0, 0, date("m"), date("d")+1, date("Y"));
        // Add the date
        //$this->request['DispatchDate'] = $DispatchDate;
        $this->request['DispatchDateSpecified'] = true;
        $this->request['PackageReadyTime'] = $PackageReadyTime;
        $this->request['PackageReadyTimeSpecified'] = true;
        $this->request['CustomerCloseTime'] = $CustomerCloseTime;
        $this->request['CustomerCloseTimeSpecified'] = true;
        
        // Setting address shipping to
        $this->setAddressShippingTo();
    }
    
    /**
     * Setting credentials
     */
    private function setCredential() {
        $this->request['WebAuthenticationDetail'] = array(
            'UserCredential' => array(
                'Key' => $this->credential_key,
                'Password' => $this->credential_password
            )
        );

        $this->request['ClientDetail'] = array(
            'AccountNumber' => $this->account_number,
            'MeterNumber' => $this->fedex_meter_number
        );
    }
    
    /**
     * Set address shipping to
     */
    private function setAddressShippingTo()
    {
        ci()->load->library('settings/settings_api');

        $stateOrProvinceCode = $this->getStateOrProvinceCode($this->shipping_from[ShippingConfigs::CITY], 
                $this->shipping_from[ShippingConfigs::REGION]);
        $country_code = settings_api::getCountryCodeByID($this->shipping_from[ShippingConfigs::COUNTRY_ID]);
        $this->request['PickupAddress'] = array(
            'StreetLines' => array($this->shipping_from[ShippingConfigs::STREET]),
            'City' => $this->shipping_from[ShippingConfigs::CITY],
            'StateOrProvinceCode' => $stateOrProvinceCode,
            'PostalCode' => $this->shipping_from[ShippingConfigs::POSTAL_CODE],
            'CountryCode' => $country_code,
        );
    }

    /**
     * create estamp label.
     */
    public function getAvailDate() {
        try {
            // FedEx web service invocation
            $response = $this->client->getPickupAvailability($this->request);
            if ($response->HighestSeverity == 'SUCCESS' || $response->HighestSeverity == 'NOTE'
                || $response->HighestSeverity == "WARNING") {
                $listResult = array();
                // Process list data
                foreach ($response->Options as $option){
                    $listResult[] = $option;
                }
                return $listResult;
            } else {
                $this->message = $this->getNotifications($response->Notifications);
                return  false;
            }
            $this->writeToLog($this->client);    // Write to log file
        } catch (SoapFault $exception) {
            //$this->printFault($exception, $this->client);
            $this->message = $exception->getMessage();
            return false;
        }
    }
    
    /**
     * Init available date request 
     */
    public function initCreatePickupRequest() {
        ci()->load->library('settings/settings_api');
        $this->request = array();
        // Set credential
        $this->setCredential();
        
        $this->request['TransactionDetail'] = array('CustomerTransactionId' => now());
        $this->request['Version'] = array(
            'ServiceId' => 'disp', 
            'Major' => 3, 
            'Intermediate' => 0, 
            'Minor' => 0
        );

        // Set pickup location
        $stateOrProvinceCode = $this->getStateOrProvinceCode($this->shipping_from[ShippingConfigs::CITY], 
                $this->shipping_from[ShippingConfigs::REGION]);
        $country_code = settings_api::getCountryCodeByID($this->shipping_from[ShippingConfigs::COUNTRY_ID]);
        $Address = array(
            'StreetLines' => array($this->shipping_from[ShippingConfigs::STREET]),
            'City' => $this->shipping_from[ShippingConfigs::CITY],
            'StateOrProvinceCode' => $stateOrProvinceCode,
            'PostalCode' => $this->shipping_from[ShippingConfigs::POSTAL_CODE],
            'CountryCode' => $country_code,
        );
        $Contact = array(
            "PersonName" => $this->shipping_from['ContactPersonName'],
            "CompanyName" => $this->shipping_from['ContactCompanyName'],
            "PhoneNumber" => $this->shipping_from['ContactPhoneNumber']
        );
        
        $PackageReadyTime = mktime(1, 0, 0, date("m")  , date("d")+1, date("Y"));
        echo date('Y-m-d H:i:s', $PackageReadyTime).'<br/>';
        $CompanyCloseTime = mktime(19, 0, 0, date("m")  , date("d")+1, date("Y"));
        echo date('Y-m-d H:i:s', $CompanyCloseTime);
        $this->request['OriginDetail'] = array(
            "PickupLocation"  => array(
                "Contact" => $Contact,
                "Address" => $Address
            ),
            "PackageLocation" => "FRONT",
            "PackageLocationSpecified" => false,
            "BuildingPart" => "SUITE",
            "BuildingPartSpecified" => false,
            "BuildingPartDescription" => "3B",
            "ReadyTimestamp" => $PackageReadyTime,
            "ReadyTimestampSpecified" => false,
            "CompanyCloseTime" => $CompanyCloseTime,
            "CompanyCloseTimeSpecified" => false
            
        );
        
        $this->request['PackageCount'] = 1;
        // Set Carriers
        $this->request['CarrierCode'] = $this->package_info['CarrierCode'];
        $this->request['CarrierCodeSpecified'] = true;
        
        $TotalWeight = array(
            "Value" => $this->package_info['weight'] / 1000,
            "ValueSpecified" => true,
            "Units" => "KG",
            "UnitsSpecified" => true
        );
        
        $this->request['TotalWeight'] = $TotalWeight;
        $this->request['Remarks'] = "Clvvermail shipping";
    }
    
    /**
     * create estamp label.
     */
    public function createPickupRequest() {
        try {
            // FedEx web service invocation
            $response = $this->client->createPickup($this->request);
            var_dump($response);
            if ($response->HighestSeverity == 'SUCCESS' || $response->HighestSeverity == 'NOTE'
                || $response->HighestSeverity == "WARNING") {
                
                // Process list data
                
                return true;
            } else {
                $this->message = $this->getNotifications($response->Notifications);
                return  false;
            }
            $this->writeToLog($this->client);    // Write to log file
        } catch (SoapFault $exception) {
            //$this->printFault($exception, $this->client);
            $this->message = $exception->getMessage();
            return false;
        }
    }
    
    /**
     * Get detail message.
     * 
     * @param type $notes
     * @return string
     */
    public function getNotifications($notes){
        $res_message = '';
        foreach($notes as $noteKey => $note){
            if(is_string($note)){    
                if ($noteKey == 'Message') {
                    $res_message = $res_message. $note . Newline;
                }
            } else {
                $inner_message = $this->getNotifications($note);
                $res_message = $res_message.$inner_message;
            }
        }
        return $res_message;
    }
    
    /**
     * get error message.
     * @return type
     */
    public function getMessage(){
        return $this->message;
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

}
