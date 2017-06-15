<?php

require_once dirname(__FILE__) .'/IXR_Library.php';

class CheckVAT {
    var $version = "1.0";
    
    /**
     * Connect to the MailChimp API for a given list.
     * 
     * @param string $apikey Your MailChimp apikey
     * @param string $secure Whether or not this should use a secure connection
     */
    function __construct() {
    }
    
    /**
     * Validate VAT number.
     * 
     * $UstId_1 = 'DE123456789'; the first UstId1 is our own (from company details instance owner).
     * $UstId_2 = 'AB123456789012'; value input from screen
     * $Company name = '. Company name including legal form';
     * $Location = 'place';
     * $ZIP = '1234567';
     * $Street = 'Street Address';
     * $Print = 'no';
     * 
     * @param unknown_type $seconds
     * @return boolean
     */
    public static function validate($UstId_1, $UstId_2, $CompanyName, $Location, $ZipCode, $StreetAddress){
        $apiUrl = Settings::get(APConstants::LINK_CHECK_VAT_02);
        
        // 'https://evatr.bff-online.de/'
        $client = new IXR_Client ($apiUrl);
        
        if (! $client->query('evatrRPC', $UstId_1, $UstId_2, $CompanyName, $Location, $ZipCode, $StreetAddress, 'nein')) {
            return false;
        }
        $OutString = $client->getResponse();
        $xml = simplexml_load_string($OutString);
        return $xml->param[1]->value[0]->array->data->value[1]->string == '200';
    }
    
    /**
     * Validate VAT eu number
     */
    public static function validateVATEU($vat_number) {
        // set API Endpoint and Access Key
        $endpoint = 'validate';
        $access_key = 'ce205992a9cef2b0e6435cc8e21dd4ec';
        
        // Initialize CURL:
        $ch = curl_init('http://apilayer.net/api/'.$endpoint.'?access_key='.$access_key.'&vat_number='.$vat_number.'');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Store the data:
        $json = curl_exec($ch);
        curl_close($ch);
        
        // Decode JSON response:
        $validationResult = json_decode($json, true);
        
        // Access and use your preferred validation result objects
        $validationResult['valid'];
        $validationResult['query'];
        $validationResult['company_name'];
        $validationResult['company_address'];
        
        echo json_encode($validationResult);
    }
}

?>