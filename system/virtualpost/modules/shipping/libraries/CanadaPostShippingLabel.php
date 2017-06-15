<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MySoapClient extends SoapClient {

    function __construct($wsdl, $options = null) {
        parent::__construct($wsdl, $options);
    }

    function __doRequest($request, $location, $action, $version, $one_way = NULL) {
        $dom = new DOMDocument('1.0');
        $dom->loadXML($request);

        //get element name and values of group-id or transmit-shipment.
        $groupIdOrTransmitShipment = $dom->getElementsByTagName("groupIdOrTransmitShipment")->item(0);
        $element = $groupIdOrTransmitShipment->firstChild->firstChild->nodeValue;
        $value = $groupIdOrTransmitShipment->firstChild->firstChild->nextSibling->firstChild->nodeValue;

        //remove bad element
        $newDom = $groupIdOrTransmitShipment->parentNode->removeChild($groupIdOrTransmitShipment);

        //append correct element with namespace
        $body = $dom->getElementsByTagName("shipment")->item(0);
        $newElement = $dom->createElement($element, $value);
        $body->appendChild($newElement);

        //save $dom to string
        $request = $dom->saveXML();

        //echo $request;
        //doRequest
        return parent::__doRequest($request, $location, $action, $version);
    }

}

class CanadaPostShippingLabel {

    private $path_to_wsdl = '';
    private $client;
    private $request;
    private $shiping_from;
    private $shiping_to;
    private $package_info;
    private $service;
    private $message = '';
    private $tracking_number = '';
    private $location = '';
    private $cafile = "";
    private $hostname = "ct.soa-gw.canadapost.ca";
    private $WSSENS = "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd";

    public function __construct() {
        ini_set("soap.wsdl_cache_enabled", "0");

        // load lib
        ci()->load->library('settings/settings_api');
        ci()->load->library('shipping/ShippingConfigs');
    }

    public function init_label($_shipping_from, $_shipping_to, $package_info, $service) {
        // init data.
        $this->shiping_from = $_shipping_from;
        $this->shiping_to = $_shipping_to;
        $this->package_info = $package_info;
        $this->path_to_wsdl = APPPATH . 'libraries/shipping/canadapost/ship/shipment.wsdl';
        $this->service = $service;

        $this->location = 'https://' . $this->hostname . '/rs/soap/shipment/v8';
        $this->cafile = APPPATH . 'libraries/shipping/canadapost/cacert.pem';

        // init request.
        $this->init_client();
        $this->init_request();
    }

    private function init_client() {
        $opts = array('ssl' => array(
            'verify_peer' => false,
            'cafile' => $this->cafile,
            'CN_match' => $this->hostname
        ));

        $ctx = stream_context_create($opts);
        // $this->client = new MySoapClient($this->path_to_wsdl, array('location' => $this->location, 'features' => SOAP_SINGLE_ELEMENT_ARRAYS, 'stream_context' => $ctx));
        $this->client = new MySoapClient($this->path_to_wsdl, array());
        $usernameToken = new stdClass();
        $usernameToken->Username = new SoapVar($this->service->credential->auth_key, XSD_STRING, null, null, null, $this->WSSENS);
        $usernameToken->Password = new SoapVar($this->service->credential->password, XSD_STRING, null, null, null, $this->WSSENS);
        $content = new stdClass();
        $content->UsernameToken = new SoapVar($usernameToken, SOAP_ENC_OBJECT, null, null, null, $this->WSSENS);
        $header = new SOAPHeader($this->WSSENS, 'Security', $content);
        $this->client->__setSoapHeaders($header);
    }

    public function setEndPoint($end_point) {
        $this->client->__setLocation($end_point);
    }

    private function init_request() {
        // Gets label size
        $to_country_code = '';
        $label_type = Settings::get_label(APConstants::SHIPPING_TYPE_CANADAPOST_LABEL_SIZE, $this->package_info['label_size']);
        if($this->shiping_to->shipment_country){
            $country = settings_api::getCountryByID($this->shiping_to->shipment_country);
            $to_country_code = $country->country_code;
        }
        $this->request = array(
            'create-shipment-request' => array(
                'locale' => 'EN',
                'mailed-by' => $this->service->credential->account_no,
                'shipment' => array(
                    //The validation expects this structure. However, this element will be removed and replaced only with ns1:group-id or ns1:transmit-shipment. 
                    'groupIdOrTransmitShipment' => array(
                        'ns1:group-id' => "432643211"
                        // 'ns1:transmit-shipment' => 'true'
                    ),
                    'requested-shipping-point' => $this->shiping_from->postcode,
                    'cpc-pickup-indicator' => 'true',
                    'expected-mailing-date' => date("Y-m-d"),
                    'delivery-spec' => array(
                        'service-code' => $this->service->api->service_code,
                        'sender' => array(
                            'name' => '',
                            'company' => Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE),
                            'contact-phone' => $this->shiping_from->phone_number,
                            'address-details' => array(
                                'address-line-1' => $this->shiping_from->street,
                                'city' => $this->shiping_from->city,
                                'prov-state' => $this->getStateOrProvinceCode($this->shiping_from->city, $this->shiping_from->region),
                                'country-code' => $this->shiping_from->country_code,
                                'postal-zip-code' => $this->shiping_from->postcode
                            )
                        ),
                        'destination' => array(
                            'name' => $this->shiping_to->shipment_address_name,
                            'company' => $this->shiping_to->shipment_company,
                            'address-details' => array(
                                'address-line-1' => $this->shiping_to->shipment_street,
                                'city' => $this->shiping_to->shipment_city,
                                'prov-state' => $this->getStateOrProvinceCode($this->shiping_to->shipment_city, $this->shiping_to->shipment_region),
                                'country-code' => $to_country_code,
                                'postal-zip-code' => $this->shiping_to->shipment_postcode
                            )
                        ),
                        'options' => array(
                            'option' => array(
                                'option-code' => 'DC'
                            )
                        ),
                        'parcel-characteristics' => array(
                            'weight' => $this->package_info['weight'],
                            'dimensions' => array(
                                'length' => $this->package_info['length'],
                                'width' => $this->package_info['width'],
                                'height' => $this->package_info['height']
                            ),
                            'unpackaged' => false,
                            'mailing-tube' => false
                        ),
                        'print-preferences' => array(
                            // 'output-format' => '8.5x11'
                            'output-format' => $label_type
                        ),
                        'preferences' => array(
                            'show-packing-instructions' => true,
                            'show-postage-rate' => false,
                            'show-insured-value' => true
                        ),
                        'settlement-info' => array(
                            'contract-id' => $this->service->credential->username,
                            'intended-method-of-payment' => 'Account'
                        )
                    )
                )
            )
        );
    }

    public function getTrackingNumber() {
        return $this->tracking_number;
    }
    
    public function getMessage(){
        return $this->message;
    }

    /**
     * create estamp label.
     */
    public function create($filename) {
        try {
            // FedEx web service invocation
            $result = $this->client->__soapCall('CreateShipment', $this->request, null, null);

            $message = '{CreateShipmentRequest:'. json_encode($this->request);
            $message = $message. ', CreateShipmentResult: '. json_encode($result).'}';
            log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'CanadaPost-CreateShipment');
            
            if (isset($result->{'shipment-info'})) {
                $this->tracking_number = $result->{'shipment-info'}->{'tracking-pin'};
                $shipment_id = $result->{'shipment-info'}->{'shipment-id'};
                $artifact_id = "";

                if (is_array($result->{'shipment-info'}->{'artifacts'}->{'artifact'})) {
                    foreach ($result->{'shipment-info'}->{'artifacts'}->{'artifact'} as $artifact) {
                        $artifact_id = $artifact->{'artifact-id'};
                        break;
                    }
                } else {
                    $artifact = $result->{'shipment-info'}->{'artifacts'}->{'artifact'};
                    $artifact_id = $artifact->{'artifact-id'};
                }

                // get artifact api.
                $this->getArtifactRest($artifact_id, $filename);
                return  true;
            } else {
                if (is_array($result->{'messages'}->{'message'})) {
                    foreach ( $result->{'messages'}->{'message'} as $message ) {
                        $this->message =  $message->code . ":". $message->description."\n";
                    }
                } else {
                    $message = $result->{'messages'}->{'message'};
                    $this->message =  $message->code . ":". $message->description."\n";
                }
                throw new ThirdPartyException($this->message);
            }
        } catch (SoapFault $exception) {
            //$this->printFault($exception, $this->client);
            $this->message = $exception->getMessage();
            throw new ThirdPartyException($this->message);
        } catch (ThirdPartyException $e){
            $this->message = $e->getMessage();
            throw new ThirdPartyException($e->getMessage());
        }
        
        return  false;
    }

    public function getArtifactRest($artifact_id, $filename) {
        $username = $this->service->credential->auth_key;
        $password = $this->service->credential->password;

        $service_url = 'https://ct.soa-gw.canadapost.ca/ers/artifact/' . $username . '/' . $artifact_id . '/0';
        $curl = curl_init($service_url); // Create REST Request
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_CAINFO, realpath($this->cafile)); // Mozilla cacerts
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, $username . ':' . $password);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept:application/pdf', 'Accept-Language:en-CA'));
        $curl_response = curl_exec($curl); // Execute REST Request
        if (curl_errno($curl)) {
            throw new ThirdPartyException(curl_error($curl));
        }
        //echo 'HTTP Response Status: ' . curl_getinfo($curl, CURLINFO_HTTP_CODE) . "\n";
        $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        if (strpos($contentType, 'application/pdf') !== FALSE) {
            // Writing binary response to file
            file_put_contents($filename, $curl_response);
        } elseif (strpos($contentType, 'xml') > -1) {
            // Example of using SimpleXML to parse xml error response
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string('<root>' . preg_replace('/<\?xml.*\?>/', '', $curl_response) . '</root>');
            if (!$xml) {
                $this->message = 'Failed loading XML ' ;
                throw new ThirdPartyException("Failed loading XML");
            } else {
                if ($xml->{'messages'}) {
                    $messages = $xml->{'messages'}->children('http://www.canadapost.ca/ws/messages');
                    foreach ($messages as $message) {
                        echo 'Error Code: ' . $message->code . "\n";
                        $this->message .= $message->description . "\n";
                    }
                }

                throw new ThirdPartyException($this->message);
            }
        } else {
            $this->message = 'Unknown Content Type: ' . $contentType;
            throw new ThirdPartyException('Unknown Content Type: ' . $contentType);
        }
        curl_close($curl);
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


