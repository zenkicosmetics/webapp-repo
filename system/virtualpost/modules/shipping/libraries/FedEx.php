<?php defined('BASEPATH') or exit('No direct script access allowed');

// Print SOAP request and response
define('NEW_LINE', "<br />");

class FedEx
{
    private $client;
    private $request;
    private $response;

    public function __construct(array $params)
    {
        ini_set("soap.wsdl_cache_enabled", "0");

        $this->client = new SoapClient(APPPATH . 'libraries/shipping/RateService_v18.wsdl', array('trace' => 1, 'encoding'=>'UTF-8'));
        // if ($params['mode'] == ShippingConfigs::MODE_PRODUCTION) {
        if(ENVIRONMENT == 'production'){
            $this->client->__setLocation(ShippingConfigs::FEDEX_PRODUCTION_URL);
        } else {
            $this->client->__setLocation(ShippingConfigs::FEDEX_TEST_URL);
        }
        $this->initRequest();
        $this->response = null;
    }
    
    public function setEndPoint($end_point) {
        $this->client->__setLocation($end_point);
    }

    public function setCredentials($key, $password)
    {
        $this->request['WebAuthenticationDetail'] = array(
            'UserCredential' => array(
                'Key' => $key,
                'Password' => $password
            )
        );
    }

    public function setShippingAccount($accountNo, $meterNo)
    {
        $this->request['ClientDetail'] = array(
            'AccountNumber' => $accountNo,
            'MeterNumber' => $meterNo
        );
    }

    public function setServiceType($serviceType = 'INTERNATIONAL_PRIORITY')
    {
        // valid values: INTERNATIONAL_PRIORITY, STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
        $this->request['RequestedShipment']['ServiceType'] = $serviceType;
    }

    public function setShipper($senderName, $senderCompanyName, $phoneNumber, $addressLine1, $cityName, $stateOrProvinceCode, $postalCode, $countryCode)
    {
        $shipper = array(
            'Contact' => array(
                'PersonName' => $senderName,
                'CompanyName' => $senderCompanyName,
                'PhoneNumber' => $phoneNumber
            ),
            'Address' => array(
                'StreetLines' => array(htmlspecialchars(APUtils::convertToEnChar($addressLine1))),
                'City' => htmlspecialchars(APUtils::convertToEnChar($cityName)),
                'StateOrProvinceCode' => $stateOrProvinceCode,
                'PostalCode' => $postalCode,
                'CountryCode' => $countryCode
            )
        );
        $this->request['RequestedShipment']['Shipper'] = $shipper;
    }

    public function setRecipient($recipientName, $companyName, $phoneNumber, $addressLine1, $cityName, $stateOrProvinceCode, $postalCode, $countryCode, $redential = false)
    {
        $recipient = array(
            'Contact' => array(
                'PersonName' => htmlspecialchars(APUtils::convertToEnChar($recipientName)),
                'CompanyName' => htmlspecialchars(APUtils::convertToEnChar($companyName)),
                'PhoneNumber' => $phoneNumber
            ),
            'Address' => array(
                'StreetLines' => array(htmlspecialchars(APUtils::convertToEnChar($addressLine1))),
                'City' => htmlspecialchars(APUtils::convertToEnChar($cityName)),
                'StateOrProvinceCode' => $stateOrProvinceCode,
                'PostalCode' => $postalCode,
                'CountryCode' => $countryCode,
                'Residential' => false
            )
        );
        $this->request['RequestedShipment']['Recipient'] = $recipient;
    }

    public function setShippingChargesPayment($accountNo, $countryCode = 'DE')
    {
        $shippingChargesPayment = array(
            'PaymentType' => 'SENDER', // valid values RECIPIENT, SENDER and THIRD_PARTY
            'Payor' => array(
                'ResponsibleParty' => array(
                    'AccountNumber' => $accountNo, // bill account
                    'CountryCode' => $countryCode
                )
            )
        );
        $this->request['RequestedShipment']['ShippingChargesPayment'] = $shippingChargesPayment;
    }

    public function setTotalInsuredValue($amount, $currency = 'EUR')
    {
        if ($amount > 0) {
            $request['RequestedShipment']['TotalInsuredValue'] = array(
                'Amount' => $amount,
                'Currency' => $currency
            );
        }
    }

    public function addPackageToShipment(array $package, $groupPackageCount = 1)
    {
        $packageLineItem = array(
            'SequenceNumber' => 1,
            'GroupPackageCount' => $groupPackageCount, // Used only with PACKAGE_GROUPS, as a count of packages within a group of identical packages
            'Weight' => array(
                'Value' => $package['Weight'],
                'Units' => 'KG',
                'ValueSpecified' => true
            )
        );
        if ($package['Length'] > 0 && $package['Width'] > 0 && $package['Height'] > 0) {
            $packageLineItem['Dimensions'] = array(
                'Length' => $package['Length'],
                'Width' => $package['Width'],
                'Height' => $package['Height'],
                'Units' => 'CM',
                'UnitsSpecified' => true
            );
        }
        $this->request['RequestedShipment']['RequestedPackageLineItems'] = $packageLineItem;

        // The total number of packages in the entire shipment (even when the shipment spans multiple transactions.)
        $this->request['RequestedShipment']['PackageCount'] = 1;
    }

    public function getRate()
    {
        try {
            $result = null;
            $this->response = $this->client->getRates($this->request);
            $errorMessage = '';
            
            $message = '{FedexGetRateRequest:'. json_encode($this->request);
            $message = $message. ', FedexGetRateResponse: '. json_encode($this->response).'}';
            log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'Fedex-GetRates');

            if ($this->response->HighestSeverity != 'FAILURE' && $this->response->HighestSeverity != 'ERROR') {
                $rateReply = $this->response->RateReplyDetails;
                $result = $this->parseRateReply($rateReply);
                //$this->showRateReply($rateReply);
                //$this->printSuccess();
            } else {
                $errorMessages = array();
                $this->getErrorMessages($errorMessages, $this->response->Notifications);
                $errorMessage = implode(NEW_LINE, $errorMessages);
                //$this->printError();
            }

            // Write to log file
            $this->writeToLog();

            return array(
                'result' => $result,
                'error' => $errorMessage
            );
        } catch (SoapFault $exception) {
            $message = '{FedexGetRateRequest:'. json_encode($this->request);
            $message = $message. ', Exception: '. json_encode($exception).'}';
            log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'Fedex-GetRates');
            
            // Write to log file
            $this->writeToLog();
            
            //$this->printFault($exception);
            return array(
                'result' => null,
                'error' => $exception->faultstring
            );
        }
    }

    private function initRequest()
    {
        $this->request = array();
        $this->request['TransactionDetail'] = array('CustomerTransactionId' => now());
        $this->request['Version'] = array(
            'ServiceId' => 'crs',
            'Major' => '18',
            'Intermediate' => '0',
            'Minor' => '0'
        );
        $this->request['ReturnTransitAndCommit'] = true;
        $this->request['RequestedShipment']['DropoffType'] = 'REGULAR_PICKUP'; // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
        $this->request['RequestedShipment']['ShipTimestamp'] = date('c');
        $this->request['RequestedShipment']['PackagingType'] = 'YOUR_PACKAGING'; // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
        $this->request['RequestedShipment']['RateRequestTypes'] = 'LIST'; // valid values ACCOUNT, LIST
    }
    
    /**
     * Set packaging type.
     * 
     * @param type $packaging_type
     */
    public function setPackagingType($packaging_type = 'YOUR_PACKAGING') {
        $this->request['RequestedShipment']['PackagingType'] = $packaging_type;
    }

    private function parseRateReply($rateReply)
    {
        $result = new stdClass();
        $result->ServiceType = $rateReply->ServiceType;
        $result->Currency = 'EUR';

        // net price that was calculated using the API for CM customer account
        if(is_array($rateReply->RatedShipmentDetails)){
            $PAYOR_ACCOUNT_SHIPMENT = $rateReply->RatedShipmentDetails[0];
            $PAYOR_LIST_SHIPMENT = $rateReply->RatedShipmentDetails[1];
        }else{
            $PAYOR_ACCOUNT_SHIPMENT = $rateReply->RatedShipmentDetails;
            $PAYOR_LIST_SHIPMENT = '';
        }
        
        $amount = new stdClass();
        $amount->RateType = 'PAYOR_ACCOUNT_SHIPMENT';
        $amount->TotalNetCharge = $PAYOR_ACCOUNT_SHIPMENT->ShipmentRateDetail->TotalNetCharge->Amount;
        $result->DiscountRate = $amount;

        // official net price calculation without customer account
        $amount = new stdClass();
        $amount->RateType = 'PAYOR_LIST_SHIPMENT';
        $amount->TotalNetCharge = empty($PAYOR_LIST_SHIPMENT) ? 0 :$PAYOR_LIST_SHIPMENT->ShipmentRateDetail->TotalNetCharge->Amount;
        $result->OfficialRate = $amount;

        return $result;
    }

    private function getErrorMessages(array &$messages, $errors)
    {
        foreach ($errors as $errorKey => $error) {
            if (is_string($error)) {
                if ($errorKey == 'Message') {
                    $messages[] = $error . NEW_LINE;
                }
            } else {
                $this->getErrorMessages($messages, $error);
            }
        }
    }

    /**
     * SOAP request/response logging to a file
     */
    private function writeToLog()
    {
        $logFile = APPPATH . 'logs/fedextransactions.log';
        if (!$logfile = fopen($logFile, "a")) {
            error_func("Cannot open " . $logFile . " file.\n", 0);
            exit(1);
        }
        fwrite($logfile, sprintf("\r%s:- %s", date("D M j G:i:s T Y"), $this->client->__getLastRequest() . "\r\n" . $this->client->__getLastResponse() . "\r\n\r\n"));
    }
}