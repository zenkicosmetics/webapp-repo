<?php

defined('BASEPATH') or exit('No direct script access allowed');
define('Newline', "<br />");

class ShippingLabel {

    private $path_to_wsdl = '';
    private $client;
    private $request;
    
    private $shiping_from;
    private $shiping_to;
    private $package_info;
    private $label_type = '';
    
    private $account_number;
    private $fedex_meter_number;
    private $country_code = 'DE';
    private $credential_key = '';
    private $credential_password = '';
    private $service;
    private $message = '';
    private $tracking_number = '';
    
    public function __construct() {
        ini_set("soap.wsdl_cache_enabled", "0");
        
        // load lib
        ci()->load->library('settings/settings_api');
        ci()->load->library('shipping/ShippingConfigs');
    }
    
    public function init_label($_shipping_from, $_shipping_to, $package_info, $service){
        // init data.
        $this->shiping_from = $_shipping_from;
        $this->shiping_to = $_shipping_to;
        $this->package_info = $package_info;
        $this->label_type = $package_info['label_size'];
        $this->path_to_wsdl = APPPATH . 'libraries/shipping/ShipService_v12.wsdl';
        $this->service = $service;
        $this->account_number = $service->credential->account_no;
        $this->fedex_meter_number = $service->credential->meter_no;
        $this->credential_key = $service->credential->auth_key;
        $this->credential_password = $service->credential->password;

         // init request.
        $this->init_client();
        $this->init_request();
    }

    private function init_client() {
        $this->client = new SoapClient($this->path_to_wsdl, array('trace' => 1));
    }
    
    public function setEndPoint($end_point) {
        $this->client->__setLocation($end_point);
    }

    private function init_request() {
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

        $this->request['TransactionDetail'] = array('CustomerTransactionId' => '*** Express International Shipping Request v12 using PHP ***');

        $this->request['Version'] = array(
            'ServiceId' => 'ship',
            'Major' => '12',
            'Intermediate' => '1',
            'Minor' => '0'
        );

        $this->request['RequestedShipment'] = array(
            'ShipTimestamp' => date('c'),
            'DropoffType' => 'REGULAR_PICKUP',
            'ServiceType' => $this->service->api->service_code,
            'PackagingType' => 'YOUR_PACKAGING',
            'Shipper' => $this->addShipper(),
            'Recipient' => $this->addRecipient(),
            'ShippingChargesPayment' => $this->addShippingChargesPayment(),
            'CustomsClearanceDetail' => $this->addCustomClearanceDetail(),
            'LabelSpecification' => $this->addLabelSpecification(),
            'CustomerSpecifiedDetail' => array(
                'MaskedData' => 'SHIPPER_ACCOUNT_NUMBER'
            ),
            'RateRequestTypes' => array('ACCOUNT'), // valid values ACCOUNT and LIST
            'PackageCount' => 1,
            'RequestedPackageLineItems' => array(
                '0' => $this->addPackageLineItem1()
            ),
            'CustomerReferences' => array(
            )
        );
        if ($this->package_info[ShippingConfigs::PACKAGE_TYPE] == APConstants::ENVELOPE_TYPE_LETTER) {
            $this->request['RequestedShipment']['PackagingType'] = 'FEDEX_ENVELOPE';
        }
    }
    
    public function getTrackingNumber(){
        return $this->tracking_number;
    } 

    /**
     * create estamp label.
     */
    public function create($filename) {
        try {
            // FedEx web service invocation
            $response = $this->client->processShipment($this->request);
            
            $message = '{FedexCreateLabelRequest:'. json_encode($this->request);
            $message = $message. ', FedexCreateLabelResponse: '. json_encode($response).'}';
            log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'Fedex-CreateLabel');
            
            if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR') {
                // get tracking number.
                $this->tracking_number = $response->CompletedShipmentDetail->CompletedPackageDetails->TrackingIds->TrackingNumber;
                
                //$this->printSuccess($this->client, $response);
                // Create PNG or PDF label
                // Set LabelSpecification.ImageType to 'PDF' for generating a PDF label
                $fp = fopen($filename, 'a');
                fwrite($fp, ($response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image));
                fclose($fp);
                
                return true;
            } else {
                $this->message = $this->getNotifications($response->Notifications);
                throw new ThirdPartyException($this->message);
            }
        } catch (SoapFault $exception) {
            $message = '{FedexCreateLabelRequest:'. json_encode($this->request);
            $message = $message. ', FedexCreateLabelResponse: '. json_encode($exception).'}';
            log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'Fedex-CreateLabel');
            
            //$this->printFault($exception, $this->client);
            $this->message = $exception->getMessage();
            throw new ThirdPartyException($this->message);
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

    function addShipper() {
        $shipper = array(
            'Contact' => array(
                'PersonName' => '',
                'CompanyName' => Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE),
                'PhoneNumber' => $this->shiping_from->phone_number
            ),
            'Address' => array(
                'StreetLines' => array($this->shiping_from->street),
                'City' => $this->shiping_from->city,
                'StateOrProvinceCode' => $this->shiping_from->state_code,
                'PostalCode' => $this->shiping_from->postcode,
                'CountryCode' => $this->shiping_from->country_code
            )
        );
        return $shipper;
    }

    function addRecipient() {
        $forwarding_address = $this->shiping_to;
        $country_code = '';
        if($forwarding_address->shipment_country){
            $country = settings_api::getCountryByID($forwarding_address->shipment_country);
            $country_code = $country->country_code;
        }
        $recipient = array(
            'Contact' => array(
                'PersonName' => $forwarding_address->shipment_address_name,
                'CompanyName' => $forwarding_address->shipment_company,
                'PhoneNumber' => $forwarding_address->shipment_phone_number
            ),
            'Address' => array(
                'StreetLines' => array($forwarding_address->shipment_street),
                'City' => $forwarding_address->shipment_city,
                'StateOrProvinceCode' => $this->getStateOrProvinceCode($forwarding_address->shipment_city, $forwarding_address->shipment_region),
                'PostalCode' => $forwarding_address->shipment_postcode,
                'CountryCode' => $country_code,
                'Residential' => false
            )
        );
        return $recipient;
    }

    function addShippingChargesPayment() {
        $shippingChargesPayment = array(
            'PaymentType' => 'SENDER',
            'Payor' => array(
                'ResponsibleParty' => array(
                    'AccountNumber' => $this->account_number,
                    'Contact' => null,
                    'Address' => array('CountryCode' => $this->country_code)
                )
            )
        );
        return $shippingChargesPayment;
    }

    function addLabelSpecification() {
        // Gets label size
        $label_type = Settings::get_label(APConstants::SHIPPING_TYPE_FEDEX_LABEL_SIZE, $this->package_info['label_size']);
        $labelSpecification = array(
            'LabelFormatType' => 'COMMON2D', // valid values COMMON2D, LABEL_DATA_ONLY
            'ImageType' => 'PDF', // valid values DPL, EPL2, PDF, ZPLII and PNG
            'LabelStockType' => $label_type,
        );
        return $labelSpecification;
    }

    function addSpecialServices() {
        $specialServices = array(
            'SpecialServiceTypes' => array('COD'),
            'CodDetail' => array(
                'CodCollectionAmount' => array(
                    'Currency' => $this->package_info['currency'],
                    'Amount' => $this->package_info['handling_charges']
                ),
                'CollectionType' => 'ANY')// ANY, GUARANTEED_FUNDS
        );
        return $specialServices;
    }

    function addCustomClearanceDetail() {
        if (empty($this->package_info['total_insured_value']) || $this->package_info['total_insured_value'] == 0) {
            $this->package_info['total_insured_value'] = 1;
        }
        $customerClearanceDetail = array(
            'DutiesPayment' => array(
                'PaymentType' => 'SENDER', //valid values RECIPIENT,SENDER and THIRD_PARTY
                'Payor' => array(
                    'ResponsibleParty' => array(
                        'AccountNumber' => $this->account_number,
                        'Contact' => null,
                        'Address' => array('CountryCode' => $this->country_code)
                    )
                )
            ),
            'DocumentContent' => 'NON_DOCUMENTS',
            'CustomsValue' => array(
                'Currency' => $this->package_info['currency'],
                'Amount' => $this->package_info['total_insured_value']
            ),
            'Commodities' => array(
                '0' => array(
                    'NumberOfPieces' => 1,
                    'Description' => 'Customs of clevvermail package',
                    'CountryOfManufacture' => $this->country_code,
                    'Weight' => array(
                        'Units' => 'KG',
                        'Value' => $this->package_info['weight']
                    ),
                    'Quantity' => 1,
                    'QuantityUnits' => 'EA',
                    'UnitPrice' => array(
                        'Currency' => $this->package_info['currency'],
                        'Amount' => $this->package_info['total_charge']
                    ),
                    'CustomsValue' => array(
                        'Currency' => $this->package_info['currency'],
                        'Amount' => $this->package_info['total_insured_value']
                    )
                )
            ),
            'ExportDetail' => array(
                'B13AFilingOption' => 'NOT_REQUIRED'
            )
        );
        
        if ($this->package_info[ShippingConfigs::PACKAGE_TYPE] != APConstants::ENVELOPE_TYPE_LETTER) {
            $customerClearanceDetail['DocumentContent'] = 'NON_DOCUMENTS';
        } else {
            $customerClearanceDetail['DocumentContent'] = 'DOCUMENTS_ONLY';
        }
        
        return $customerClearanceDetail;
    }

    function addPackageLineItem1() {
        $packageLineItem = array(
            'SequenceNumber' => 1,
            'GroupPackageCount' => 1,
            'Weight' => array(
                'Value' => $this->package_info['weight'], // convert kg to lb
                'Units' => 'KG'),
            'Dimensions' => array(
                'Length' => $this->package_info['length'], // convert cm to inches
                'Width' => $this->package_info['width'],
                'Height' => $this->package_info['height'],
                'Units' => 'CM')
        );
        return $packageLineItem;
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
