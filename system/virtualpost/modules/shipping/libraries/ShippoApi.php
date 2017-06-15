<?php 
// Shippo singleton
require(APPPATH.'libraries/shipping/Shippo/Shippo.php');

// Utilities
require(APPPATH.'libraries/shipping/Shippo/Util.php');
require(APPPATH.'libraries/shipping/Shippo/Util/Set.php');

// Errors
require(APPPATH.'libraries/shipping/Shippo/Error.php');
require(APPPATH.'libraries/shipping/Shippo/ApiError.php');
require(APPPATH.'libraries/shipping/Shippo/ApiConnectionError.php');
require(APPPATH.'libraries/shipping/Shippo/AuthenticationError.php');
require(APPPATH.'libraries/shipping/Shippo/InvalidRequestError.php');
require(APPPATH.'libraries/shipping/Shippo/RateLimitError.php');

// Plumbing
require(APPPATH.'libraries/shipping/Shippo/Object.php');
require(APPPATH.'libraries/shipping/Shippo/ApiRequestor.php');
require(APPPATH.'libraries/shipping/Shippo/ApiResource.php');
require(APPPATH.'libraries/shipping/Shippo/CurlClient.php');
require(APPPATH.'libraries/shipping/Shippo/SingletonApiResource.php');
require(APPPATH.'libraries/shipping/Shippo/AttachedObject.php');
require(APPPATH.'libraries/shipping/Shippo/List.php');

// Shippo API Resources
require(APPPATH.'libraries/shipping/Shippo/Address.php');
require(APPPATH.'libraries/shipping/Shippo/Parcel.php');
require(APPPATH.'libraries/shipping/Shippo/Shipment.php');
require(APPPATH.'libraries/shipping/Shippo/Rate.php');
require(APPPATH.'libraries/shipping/Shippo/Transaction.php');
require(APPPATH.'libraries/shipping/Shippo/CustomsItem.php');
require(APPPATH.'libraries/shipping/Shippo/CustomsDeclaration.php');
require(APPPATH.'libraries/shipping/Shippo/Refund.php');
require(APPPATH.'libraries/shipping/Shippo/Manifest.php');
require(APPPATH.'libraries/shipping/Shippo/CarrierAccount.php');
require(APPPATH.'libraries/shipping/Shippo/Track.php');
require(APPPATH.'libraries/shipping/Shippo/Batch.php');

class ShippoApi
{
    private $apiKey;
    private $fromAddress;
    private $toAddress;
    private $parcels;
    private $carrier;
    private $service;
    private $insurance;
    
    public function getRate(){
        
        $shipment =  array( 'address_from'=> $this->fromAddress,
                            'address_to'=> $this->toAddress,
                            'parcels'=> $this->parcels,
                            'async'=> false,
                            'insurance_amount' => $this->insurance,
                            'insurance_currency' => 'EUR'
                          );

        $result = array();
        
        try {
            $messages = "{ shippo_rate_request : ".json_encode($shipment).",";
            $shippingObj =  Shippo_Shipment::create($shipment);
            $messages .= " shippo_obj_response : " . $shippingObj . ",";

            if (!empty($shippingObj) && $shippingObj['status'] == 'SUCCESS') {
                $rate_in_euro = Shippo_Shipment::get_shipping_rates( array(
                                                'id'=> $shippingObj['object_id'],
                                                'currency'=> 'EUR'
                                            ));   
                $result =  array('status' => 'success', 'data' =>  $rate_in_euro);
                $messages .= " shippo_get_rate_convert_to_EUR_response : " . $rate_in_euro . " ,";
            }
                
        } catch (Exception $ex){
                $result = array(
                    'status' => 'error',
                    'data' => $ex->getMessage()
                );
                $messages .= " shippo_get_rate_error : " . json_encode($result) . " ,";
        }
        
        log_audit_message(APConstants::LOG_INFOR, $messages . "}", FALSE, 'Shippo-getRate');

        return $result;
       
    }
    
    public function createLabel($label_size){
        $shipment = array(
            'address_from'=> $this->fromAddress,
            'address_to'=> $this->toAddress,
            'parcels'=> $this->parcels,
            'insurance_amount' => $this->insurance,
            'insurance_currency' => 'EUR'
        );
        
        $label =  array(
            'shipment' => $shipment,
            'carrier_account' => $this->carrier,
            'servicelevel_token' => $this->service,
            'label_file_type' => $label_size
        );
        
        log_audit_message(APConstants::LOG_INFOR, 'Create label request data:' . json_encode($label), FALSE, 'shippo_create_label_request');
        
        $result = array();
        try {
                $result = array(
                    'status' => 'success',
                    'data' => Shippo_Transaction::create($label)
                );
        } catch (Exception $ex){
                $result = array(
                    'status' => 'error',
                    'data' => $ex->getMessage()
                );
        }
        
        return $result;
    }
    
    public function setShipper($senderName, $senderCompanyName, $phoneNumber, $addressLine1, $cityName, $stateOrProvinceCode, $postalCode, $countryCode, $email)
    {
        $from_address = array(
            'name' => $senderName,
            'company' => $senderCompanyName,
            'street1' => $addressLine1,
            'city' => $cityName,
            'state' => $stateOrProvinceCode,
            'zip' => $postalCode,
            'country' => $countryCode,
            'phone' => $phoneNumber,
            'email' => $email
        );
        
        $this->fromAddress = $from_address;
    }
    
    public function setRecipient($recipientName, $companyName, $phoneNumber, $addressLine1, $cityName, $stateOrProvinceCode, $postalCode, $countryCode, $email, $residential = false)
    {
         $to_address = array(
            'name' => htmlspecialchars(APUtils::convertToEnChar($recipientName)),
            'company' => htmlspecialchars(APUtils::convertToEnChar($companyName)),
            'street1' => htmlspecialchars(APUtils::convertToEnChar($addressLine1)),
            'city' => htmlspecialchars(APUtils::convertToEnChar($cityName)),
            'state' => $stateOrProvinceCode,
            'zip' => $postalCode,
            'country' => $countryCode,
            'phone' => $phoneNumber,
            'email' => $email,
            'is_residential' => $residential
        );
        
        $this->toAddress = $to_address;
    }
    
    public function setPackageToShipment(array $package)
    {
        $parcel = array(
            'length'=> isset($package['Length']) ? $package['Length'] : $package['length'],
            'width'=> isset($package['Width']) ? $package['Width'] : $package['width'],
            'height'=> isset($package['Height']) ? $package['Height'] : $package['height'],
            'distance_unit'=> 'cm',
            'weight'=> isset($package['Weight']) ? $package['Weight'] : $package['weight'],
            'mass_unit'=> 'kg',
        );
        
        $this->parcels = array($parcel);
        
    }
    
    public function setCarrier($carrier, $service_code){
        $this->carrier = $carrier;
        $this->service = $service_code;
    }
    
    public function setInsurance($insurance_amout){
         $this->insurance = $insurance_amout;
    }
    
    public function setCredentials($apiKey)
    {
        $this->apiKey = $apiKey;
        //Set API key 
        Shippo::setApiKey($this->apiKey);
    }
    
}