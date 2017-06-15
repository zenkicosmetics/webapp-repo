<?php defined('BASEPATH') or exit('No direct script access allowed');

class CanadaPost
{
    private $client;
    private $request;
    private $response;

    public function __construct(array $params)
    {
        ini_set("soap.wsdl_cache_enabled", "0");
        $hostName = 'ct.soa-gw.canadapost.ca';
        // SOAP URI
        $location = 'https://' . $hostName . '/rs/soap/rating/v3';
        // SSL Options
        $opts = array('ssl' =>
            array(
                'verify_peer'=> false,
                'cafile' => APPPATH.'libraries/shipping/canadapost/cacert.pem',
                'CN_match' => $hostName
            )
        );
        $ctx = stream_context_create($opts);
        $wsdl = APPPATH . 'libraries/shipping/canadapost/rate/rating.wsdl';
        // $this->client = new SoapClient($wsdl, array('location' => $location, 'features' => SOAP_SINGLE_ELEMENT_ARRAYS, 'stream_context' => $ctx));
        $this->client = new SoapClient($wsdl, array('trace' => 1, 'encoding'=>'UTF-8'));
        $this->initRequest();
        $this->response = null;
    }
    
    public function setEndPoint($end_point) {
        $this->client->__setLocation($end_point);
    }

    public function setCredentials($username, $password)
    {
        $WSSENS = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd';
        $usernameToken = new stdClass(); 
        $usernameToken->Username = new SoapVar($username, XSD_STRING, null, null, null, $WSSENS);
        $usernameToken->Password = new SoapVar($password, XSD_STRING, null, null, null, $WSSENS);
        $content = new stdClass(); 
        $content->UsernameToken = new SoapVar($usernameToken, SOAP_ENC_OBJECT, null, null, null, $WSSENS);
        $header = new SOAPHeader($WSSENS, 'Security', $content);
        $this->client->__setSoapHeaders($header); 
        
    }

    public function setShippingAccount($accountNo)
    {
        $this->request['get-rates-request']['mailing-scenario']['customer-number'] = $accountNo;
    }

    public function setServiceType($serviceType)
    {
        // valid values: INTERNATIONAL_PRIORITY, STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
        // $this->request['RequestedShipment']['ServiceType'] = $serviceType;
    }

    public function setShipper($senderName, $senderCompanyName, $phoneNumber, $addressLine1, $cityName, $stateOrProvinceCode, $postalCode, $countryCode)
    {
        $this->request['get-rates-request']['mailing-scenario']['origin-postal-code'] = $postalCode;
    }

    public function setRecipient($recipientName, $companyName, $phoneNumber, $addressLine1, $cityName, $stateOrProvinceCode, $postalCode, $countryCode, $is_domestic = false)
    {
        if ($is_domestic) {
            $destination = array(
                'domestic' => array(
                    'postal-code' => $postalCode
                )
            );
        } else {
            $destination = array(
                'international' => array(
                    'country-code' => $countryCode
                )
            );
        }
        $this->request['get-rates-request']['mailing-scenario']['destination'] = $destination;
    }

    public function setTotalInsuredValue($amount, $currency = 'EUR')
    {
        // Nothing
        if ($amount > 0) {
            $options = array(
                'option' => array(
                    'option-code' => 'COV',
                    'option-amount' => $amount
                )
            );
            $this->request['get-rates-request']['mailing-scenario']['options'] = $options;
        }
    }

    public function addPackageToShipment(array $package)
    {
        $parcel_characteristics = array(
            'weight'=> $package['Weight'],
            'dimensions'    => array(
                'length' => $package['Length'],
                'width'  => $package['Width'],
                'height' => $package['Height']
            )
        );
        $this->request['get-rates-request']['mailing-scenario']['parcel-characteristics'] = $parcel_characteristics;
    }

    public function getRate()
    {
        try {
            $list_prices = null;
            $this->response = $this->client->__soapCall('GetRates', $this->request);
            $errorMessage = '';
            
            $message = '{CanadaPostGetRateRequest:'. json_encode($this->request);
            $message = $message. ', CanadaPostGetRateResponse: '. json_encode($this->response).'}';
            log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'CanadaPost-GetRates');

            if ( isset($this->response->{'price-quotes'}) ) {
                $list_prices = $this->parseRateReply($this->response);
            } else {
                $errorMessages = array();
                $this->getErrorMessages($errorMessages, $this->response);
                $errorMessage = implode('<br />', $errorMessages);
            }

            // Write to log file
            $this->writeToLog();

            return array(
                'result' => $list_prices,
                'error' => $errorMessage
            );
        } catch (SoapFault $exception) {
            $message = '{CanadaPostGetRateRequest:'. json_encode($this->request);
            $message = $message. ', Exception: '. json_encode($exception).'}';
            log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'CanadaPost-GetRates');
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
        $this->request['get-rates-request'] = array(
            'locale'    => 'EN',
            'mailing-scenario' => array()
        );
    }

    private function parseRateReply($response)
    {
        $list_prices = array();
        

        foreach ( $response->{'price-quotes'}->{'price-quote'} as $priceQuote ) {  
            $result = new stdClass();
            $result->ServiceType = $priceQuote->{'service-code'};
            $result->ServiceName = $priceQuote->{'service-name'};
            $result->Price = $priceQuote->{'price-details'}->{'due'};
            $result->Currency = 'EUR';
            
            $list_prices[] = $result;
        }

        return $list_prices;
    }

    private function getErrorMessages(array &$messages, $response)
    {
        if (is_array($response->{'messages'}->{'message'})) {
            foreach ( $response->{'messages'}->{'message'} as $message ) {
                $messages[] = $message->code . ":". $message->description."\n";
            }
        } else {
            $message = $response->{'messages'}->{'message'};
            $messages[] = $message->code . ":". $message->description."\n";
        }
    }

    /**
     * SOAP request/response logging to a file
     */
    private function writeToLog()
    {
        $logFile = APPPATH . 'logs/canadapost.log';
        if (!$logfile = fopen($logFile, "a")) {
            error_func("Cannot open " . $logFile . " file.\n", 0);
            exit(1);
        }
        fwrite($logfile, sprintf("\r%s:- %s", date("D M j G:i:s T Y"), $this->client->__getLastRequest() . "\r\n" . $this->client->__getLastResponse() . "\r\n\r\n"));
    }
}