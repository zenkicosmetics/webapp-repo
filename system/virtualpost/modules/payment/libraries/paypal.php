<?php

defined('BASEPATH') or exit('No direct script access allowed');

class paypal {
    # Sandbox
    private $host = 'https://api.paypal.com';
    private $clientId = 'AWYzLc1DIIzoUpCtMFnWJBrJgNE6of6KKyi9BuvIp6tkBTmgJa-KVWg63pwxH64-qh266COHLOrqLILr';
    private $clientSecret = 'ENdBYme2g7SkK2lbZj41DloHidZw5uyimrrlIycuglp3JzY086Y4_5K9Qrfc-C8dsY6SSuI3vcapTzM8';

    /**
     * Helper method for getting an APIContext for all calls
     * 
     * @param string $clientId
     *            Client ID
     * @param string $clientSecret
     *            Client Secret
     * @return PayPal\Rest\ApiContext
     */
    function getApiContext() {

        // Get setting from database
        $clientSecret = Settings::get(APConstants::PAYMENT_PAYPAL_CLIENT_SECRET);
        $clientId = Settings::get(APConstants::PAYMENT_PAYPAL_CLIENT_ID);

        // ### Api context
        // Use an ApiContext object to authenticate
        // API calls. The clientId and clientSecret for the
        // OAuthTokenCredential class can be retrieved from
        // developer.paypal.com


        $apiContext = new ApiContext(new OAuthTokenCredential($clientId, $clientSecret));

        // Comment this line out and uncomment the PP_CONFIG_PATH
        // 'define' block if you want to use static file
        // based configuration


        $apiContext->setConfig(array(
            'mode' => 'sandbox',
            'log.LogEnabled' => true,
            'log.FileName' => APPPATH . 'system/virtualpost/logs/PayPal.log',
            'log.LogLevel' => 'DEBUG',
            'validation.level' => 'log',
            'cache.enabled' => true
        ));

        // Partner Attribution Id
        // Use this header if you are a PayPal partner. Specify a unique BN Code to receive revenue attribution.
        // To learn more or to request a BN Code, contact your Partner Manager or visit the PayPal Partner Portal
        // $apiContext->addRequestHeader('PayPal-Partner-Attribution-Id', '123123123');
        return $apiContext;
    }

    /**
     * Get access token
     * @return type
     */
    function get_access_token() {
        $url = $this->host.'/v1/oauth2/token';
        $postdata = 'grant_type=client_credentials';
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERPWD, $this->clientId . ":" . $this->clientSecret);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        #curl_setopt($curl, CURLOPT_VERBOSE, TRUE);
        $response = curl_exec($curl);
        if (empty($response)) {
            // some kind of an error happened
            die(curl_error($curl));
            curl_close($curl); // close cURL handler
        } else {
            $info = curl_getinfo($curl);
            curl_close($curl); // close cURL handler
            if ($info['http_code'] != 200 && $info['http_code'] != 201) {
                die();
            }
        }
        // Convert the result from JSON format to a PHP array 
        $jsonResponse = json_decode($response);
        return $jsonResponse->access_token;
    }
    
    /**
     * Get payment information.
     * 
     * @param type $payment_id
     */
    function get_payment_info($payment_id) {
        $url = $this->host.'/v1/payments/payment/'.$payment_id;
        $result = $this->make_get_call($url);
        if (empty($result) || empty($result['transactions'])) {
            return '';
        }
        $transactions = $result['transactions'];
        if (count($transactions) == 0) {
            return '';
        }
        $related_resources = $transactions[0]['related_resources'];
        if (empty($related_resources) || count($related_resources) == 0) {
            return '';
        }
        $related_resource = $related_resources[0];
        return $related_resource['sale']['id'];
    }

    /**
     * 
     * @param type $url
     * @param type $postdata
     * @return type
     */
    function make_post_call($url, $postdata) {
        $token = $this->get_access_token();
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $token,
            'Accept: application/json',
            'Content-Type: application/json'
        ));

        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        #curl_setopt($curl, CURLOPT_VERBOSE, TRUE);
        $response = curl_exec($curl);
        if (empty($response)) {
            // some kind of an error happened
            die(curl_error($curl));
            curl_close($curl); // close cURL handler
        } else {
            $info = curl_getinfo($curl);
            curl_close($curl); // close cURL handler
            if ($info['http_code'] != 200 && $info['http_code'] != 201) {
                die();
            }
        }
        // Convert the result from JSON format to a PHP array 
        $jsonResponse = json_decode($response, TRUE);
        return $jsonResponse;
    }

    function make_get_call($url) {
        $token = $this->get_access_token();
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $token,
            'Accept: application/json',
            'Content-Type: application/json'
        ));

        #curl_setopt($curl, CURLOPT_VERBOSE, TRUE);
        $response = curl_exec($curl);
        if (empty($response)) {
            // some kind of an error happened
            die(curl_error($curl));
            curl_close($curl); // close cURL handler
        } else {
            $info = curl_getinfo($curl);
            curl_close($curl); // close cURL handler
            if ($info['http_code'] != 200 && $info['http_code'] != 201) {
                die();
            }
        }
        // Convert the result from JSON format to a PHP array 
        $jsonResponse = json_decode($response, TRUE);
        return $jsonResponse;
    }
}
