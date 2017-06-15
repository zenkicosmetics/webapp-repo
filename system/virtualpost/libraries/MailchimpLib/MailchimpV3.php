<?php

require_once 'Mailchimp/Exceptions.php';

class MailchimpV3 {
    
    public $apikey;
    public $ch;
    public $root  = 'https://api.mailchimp.com/3.0';

    public function __construct($apikey=null) {

        if (!$apikey) {
            throw new Mailchimp_Error('You must provide a MailChimp API key');
        }

        $this->apikey = $apikey;
        $dc           = "us1";

        if (strstr($this->apikey, "-")){
            list($key, $dc) = explode("-", $this->apikey, 2);
            if (!$dc) {
                $dc = "us1";
            }
        }

        $this->root = str_replace('https://api', 'https://' . $dc . '.api', $this->root);
        $this->root = rtrim($this->root, '/') . '/';

        $this->ch = curl_init();

        if (isset($opts['CURLOPT_FOLLOWLOCATION']) && $opts['CURLOPT_FOLLOWLOCATION'] === true) {
            curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);    
        }

        curl_setopt($this->ch, CURLOPT_USERAGENT, 'PHP-MCAPI/3.0');
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_HEADER, false);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);

    }

    public function __destruct() {
        if(is_resource($this->ch)) {
            curl_close($this->ch);
        }
    }

    public function call($url, $params) {
        
        $params = json_encode($params);
        $ch     = $this->ch;

        curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $this->apikey);
        curl_setopt($ch, CURLOPT_URL, $this->root . $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_VERBOSE, false);

        $response_body = curl_exec($ch);

        if(curl_error($ch)) {
            throw new Mailchimp_HttpError("API call to $url failed: " . curl_error($ch));
        }
        $result = json_decode($response_body, true);
        
        return $result;
    }

    public function syncSubscribe($listId, $members) {
        $_params = array('members' => $members);
        return $this->call('lists/'.$listId, $_params);
    }
    
}


