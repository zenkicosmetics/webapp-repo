<?php

if (! defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 */
class Paypallib extends CI_Controller {
    var $is_sandbox = TRUE;
    var $_errors = array ();
    var $_credentials = array ();
    var $_endPoint = '';
    var $_version = '94.0';
    const PROCESS_URL = 'https://api-3t.paypal.com/nvp';
    const PROCESS_URL_TEST = 'https://api-3t.sandbox.paypal.com/nvp';
    
    /**
     * Constructor
     * 
     * @access public
     * @param
     *            array	config preferences
     * @return void
     */
    function __construct() {
        $this->ci = & get_instance();
        $this->is_sandbox = Settings::get(APConstants::PAYMENT_PAYPAL_TEST_MODE) == 'true';
        
        $this->_credentials = array (
                'USER' => Settings::get(APConstants::PAYMENT_PAYPAL_USERNAME_CODE),
                'PWD' => Settings::get(APConstants::PAYMENT_PAYPAL_PASSWORD_CODE),
                'SIGNATURE' => Settings::get(APConstants::PAYMENT_PAYPAL_SIGNATURE_CODE) 
        );
        $this->_endPoint = $this->is_sandbox ? self::PROCESS_URL_TEST : self::PROCESS_URL;
    }
    
    /**
     * Make API request
     * 
     * @param string $method
     *            string API method to request
     * @param array $params
     *            Additional request parameters
     * @return array / boolean Response array / boolean false on failure
     */
    function request($method, $params = array()) {
        $this->_errors = array ();
        if (empty($method)) { //Check if API method is not empty
            $this->_errors = array (
                    'API method is missing' 
            );
            return false;
        }
       
        
        //Our request parameters
        $requestParams = array (
                'METHOD' => $method,
                'VERSION' => $this->_version 
        ) + $this->_credentials;
        
        //Building our NVP string
        $request = http_build_query($requestParams + $params);
        
        //cURL settings
        $curlOptions = array (
                CURLOPT_URL => $this->_endPoint,
                CURLOPT_VERBOSE => 1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $request 
        );
        
        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);
        
        //Sending our request - $response will hold the API response
        $response = curl_exec($ch);
        
        //Checking for cURL errors
        if (curl_errno($ch)) {
            $this->_errors = curl_error($ch);
            curl_close($ch);
            log_message(APConstants::LOG_ERROR, 'Error when call Paypal:'.json_encode($this->_errors));
            $version = curl_version();
            log_message(APConstants::LOG_ERROR, 'CURL version:'.json_encode($version));
            return false;
            //Handle errors
        }
        else {
            curl_close($ch);
            $responseArray = array ();
            parse_str($response, $responseArray); // Break the NVP string to an array
            return $responseArray;
        }
    }
}