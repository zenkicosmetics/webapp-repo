<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sonetel {

    /**
     * Server end point
     */
    var $server_endpoint = '';

    /**
     * API Key
     */
    var $api_key = '';

    /**
     * API Token
     */
    var $api_token = '';

    /**
     * Basic key
     */
    var $basic_key = '';
    protected $_ci;

    /**
     * Constructor
     *
     */
    function __construct() {
        $this->_ci = & get_instance();
        $this->server_endpoint = Settings::get(APConstants::SONETEL_API_ENDPOINT);
        $this->api_key = Settings::get(APConstants::SONETEL_API_KEY);
        $this->api_token = Settings::get(APConstants::SONETEL_API_TOKEN);
        $this->basic_key = base64_encode($this->api_key . ':' . $this->api_token);
        $config = array(
            'server' => $this->server_endpoint,
            'http_user' => $this->api_key,
            'http_pass' => $this->api_token,
            'http_auth' => 'basic'
        );
    }

    /**
     * Call remote url
     *
     * @param unknown_type $url
     * @return mixed
     */
    private function get($uri, $param) {
        $url = $this->server_endpoint . $uri;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Basic ' . $this->basic_key,
            'Content-Type:application/json'
        ));

        $return_data = curl_exec($ch);
        //log_message(APConstants::LOG_ERROR, 'Call url: ' . $url . '| Response: ' . json_encode($return_data));
        log_audit_message(APConstants::LOG_INFOR, 'Call url: ' . $url . '| Response: ' . json_encode($return_data), false, 'sonetel-get');
        
        // var_dump($return_data);
        curl_close($ch);
        return $return_data;
    }

    /**
     * Call remote url
     *
     * @param unknown_type $url
     * @return mixed
     */
    private function delete($uri, $param) {
        $url = $this->server_endpoint . $uri;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Basic ' . $this->basic_key,
            'Content-Type:application/json'
        ));

        $return_data = curl_exec($ch);
        //log_message(APConstants::LOG_ERROR, 'Call url: ' . $url . '| Response: ' . json_encode($return_data));
        log_audit_message(APConstants::LOG_INFOR, 'Call url: ' . $url . '| Response: ' . json_encode($return_data), false, 'sonetel-delete');

        // var_dump($return_data);
        curl_close($ch);
        return $return_data;
    }

    /**
     * Call remote url
     *
     * @param unknown_type $url
     * @return mixed
     */
    private function post($uri, $param) {
        $url = $this->server_endpoint . $uri;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data_string = json_encode($param);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Basic ' . $this->basic_key,
            'Content-Type:application/json'
        ));

        $return_data = curl_exec($ch);
        //log_message(APConstants::LOG_ERROR, 'INPUT:' . json_encode($param));
        //log_message(APConstants::LOG_ERROR, 'Call url: ' . $url . '| Response: ' . json_encode($return_data));
        log_audit_message(APConstants::LOG_INFOR, 'INPUT PARAMS:' . json_encode($param), false, 'sonetel-post');
        log_audit_message(APConstants::LOG_INFOR, 'Call url: ' . $url . '| Response: ' . json_encode($return_data), false, 'sonetel-post');

        // var_dump($return_data);
        curl_close($ch);
        return $return_data;
    }
    
    /**
     * Call remote url
     *
     * @param unknown_type $url
     * @return mixed
     */
    private function post_upload_file($full_url, $local_file_path) {
        $url = $full_url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data_string = array(
            'testData' => '@'.$local_file_path
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Basic ' . $this->basic_key,
            'Content-Type:application/json'
        ));

        $return_data = curl_exec($ch);
        //log_message(APConstants::LOG_ERROR, 'Call url: ' . $url . '| Response: ' . json_encode($return_data));
        log_audit_message(APConstants::LOG_INFOR, 'Call url: ' . $url . '| Response: ' . json_encode($return_data), false, 'sonetel-post_upload_file');

        // var_dump($return_data);
        curl_close($ch);
        return $return_data;
    }
    
    /**
     * Call remote url
     *
     * @param unknown_type $url
     * @return mixed
     */
    private function put($uri, $param) {
        $url = $this->server_endpoint . $uri;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
        curl_setopt($ch, CURLOPT_URL, $url);

        $data_string = json_encode($param);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Basic ' . $this->basic_key,
            'Content-Type:application/json'
        ));

        $return_data = curl_exec($ch);
        //log_message(APConstants::LOG_ERROR, 'INPUT:' . json_encode($param));
        //log_message(APConstants::LOG_ERROR, 'Call url: ' . $url . '| Response: ' . json_encode($return_data));
        log_audit_message(APConstants::LOG_INFOR, 'INPUT:' . json_encode($param), false, 'sonetel-put');
        log_audit_message(APConstants::LOG_INFOR, 'Call url: ' . $url . '| Response: ' . json_encode($return_data), false, 'sonetel-put');

        // var_dump($return_data);
        curl_close($ch);
        return $return_data;
    }

    /**
     * Get list sub account
     */
    public function get_list_sub_account() {
        return $this->get('/account', '');
    }

    /**
     * Create new sub account.
     */
    public function create_sub_account($name, $email, $user_fname, $password) {
        // Check existing sub account based on email address
        $response_check_str = $this->get('/account?email='.$email, null);
        $response_check = json_decode($response_check_str);
        if ($response_check != null && isset($response_check->status) 
                && $response_check->status == 'success' && !empty($response_check->response)
                && count($response_check->response) > 0) {
            $sub_account = $response_check->response[0];
            return $sub_account->account_id;
        }
        $input = array(
            "name" => $name,
            "email" => $email,
            "user_fname" => $user_fname,
            "password" => $password
        );
        // Pull in an array of tweets
        $response_str = $this->post('/account', $input);
        $response = json_decode($response_str);
        if (!empty($response) && !empty($response->response) && !empty($response->response->account_id)) {
            return $response->response->account_id;
        }
        return '';
    }
    
    /**
     * Create new sub account.
     */
    public function get_sub_account($sub_account_id) {
        $input = array(
        );
        // Pull in an array of tweets
        $response_str = $this->get('/account/'.$sub_account_id, $input);
        $response = json_decode($response_str);
        if (!empty($response) && !empty($response->response) && !empty($response->response)) {
            return $response->response;
        }
        return '';
    }
    
    /**
     * Create new sub account.
     */
    public function get_sub_account_by_email($email) {
        // Check existing sub account based on email address
        $response_check_str = $this->post('/account?email='.$email, null);
        $response_check = json_decode($response_check_str);
        if ($response_check != null && isset($response_check->status) 
                && $response_check->status == 'success' && !empty($response_check->response)
                && count($response_check->response) > 0) {
            $sub_account = $response_check->response[0];
            return $sub_account->account_id;
        }
        return '';
    }
    
    /**
     * Create new sub account.
     */
    public function update_sub_account($sub_account_id, $change_balance) {
        $input = array(
            "change_balance" => number_format($change_balance, 2)
        );
        // Pull in an array of tweets
        $response_str = $this->put('/account/'.$sub_account_id, $input);
        $response = json_decode($response_str);
        if (!empty($response) && $response->status == 'success') {
            return true;
        }
        return false;
    }

    /**
     * Create new sub account.
     *
     */
    public function create_new_user($account_id, $email, $user_fname, $password) {
        // Check user existing by email address
        $response_check_str = $this->post('/account/' . $account_id . '/user?email='.$email, null);
        $response_check = json_decode($response_check_str);
        if ($response_check != null && isset($response_check->status) 
                && $response_check->status == 'success' && !empty($response_check->response)
                && count($response_check->response) > 0) {
            $user = $response_check->response[0];
            return $user->user_id;
        }
        
        $input = array(
            "email" => $email,
            "user_fname" => $user_fname,
            "password" => $password
        );
        // Pull in an array of tweets
        $response_str = $this->post('/account/' . $account_id . '/user', $input);
        $response = json_decode($response_str);
        if (!empty($response) && !empty($response->response) && !empty($response->response->user_id)) {
            return $response->response->user_id;
        }
        if (empty($response) || empty($response->response) || $response->status != 'success') {
            throw new Exception($response->response->detail);
        }
        return '';
    }
    
    /**
     * Create new sub account.
     *
     */
    public function get_user_id_by_email($account_id, $email) {
        // Check user existing by email address
        $response_check_str = $this->post('/account/' . $account_id . '/user?email='.$email, null);
        $response_check = json_decode($response_check_str);
        if ($response_check->status == 'success' && !empty($response_check->response)
                && count($response_check->response) > 0) {
            $user = $response_check->response[0];
            return $user->user_id;
        }
        return '';
    }
    
    /**
     * Get user detail from sonetel
     *
     */
    public function get_user($account_id, $user_id, $fields = 'call,phones,numbers,location') {
        $input = array();
        // Pull in an array of tweets
        $response_str = $this->get('/account/' . $account_id . '/user/' . $user_id.'?fields='.$fields, $input);
        $response = json_decode($response_str);
        
        // Check response
        if (empty($response) || empty($response->response) || $response->status != 'success') {
            throw new Exception('Can not get user detail information from backend.');
        }
        return $response->response;
    }
    
    /**
     * Get user detail from sonetel
     *
     */
    public function get_user_by_email($account_id, $email, $fields = 'call,phones,numbers,location') {
        $input = array();
        // Pull in an array of tweets
        $response_str = $this->get('/account/' . $account_id . '/user/?email='.$email.'fields='.$fields, $input);
        $response = json_decode($response_str);
        
        // Check response
        if (empty($response) || empty($response->response) || $response->status != 'success') {
            throw new Exception('Can not get user detail information from backend.');
        }
        return $response->response;
    }
    
    /**
     * Update user location
     * @param type $account_id
     * @param type $user_id
     * @param type $location
     */
    public function update_user($account_id, $user_id, $data = array()) {
        $input = array(
            "email" => $data['email'],
            "user_fname" => $data['user_fname']
        );
        if (isset($data['location']) && !empty($data['location'])) {
            $input['location'] = $data['location'];
        }
        if (isset($data['call']) && !empty($data['call'])) {
            $input['call'] = $data['call'];
        }
        if (isset($data['phones']) && !empty($data['phones'])) {
            $input['phones'] = $data['phones'];
        }
        if (isset($data['numbers']) && !empty($data['numbers'])) {
            $input['numbers'] = $data['numbers'];
        }
        // Pull in an array of tweets
        $response_str = $this->post('/account/' . $account_id . '/user/'.$user_id, $input);
        $response = json_decode($response_str);
        if (!empty($response) && !empty($response->response) && !empty($response->response->user_id)) {
            return $response->response->user_id;
        }
        if (empty($response) || empty($response->response) || $response->status != 'success') {
            throw new Exception($response->response->detail);
        }
        return '';
    }
    
    /**
     * Update user location
     * @param type $account_id
     * @param type $user_id
     * @param type $location
     */
    public function update_user_location($account_id, $user_id, $country_code, $area_code) {
        $input = array(
            'location' => array(
                'country' => $country_code,
                'area_code' => $area_code
            )
        );
        
        // Pull in an array of tweets
        $response_str = $this->post('/account/' . $account_id . '/user/'.$user_id, $input);
        $response = json_decode($response_str);
        if (!empty($response) && !empty($response->response) && !empty($response->response->user_id)) {
            return $response->response->user_id;
        }
        if (empty($response) || empty($response->response) || $response->status != 'success') {
            throw new Exception($response->response->detail);
        }
        return '';
    }
    /**
     * Update user location
     * @param type $account_id
     * @param type $user_id
     * @param type $location
     */
    public function update_user_outgoing($account_id, $user_id, $data) {
        // Pull in an array of tweets
        $response_str = $this->post('/account/' . $account_id . '/user/'.$user_id.'/call/outgoing', $data);
        $response = json_decode($response_str);
        if (!empty($response) && !empty($response->response) && !empty($response->response->user_id)) {
            return $response->response->user_id;
        }
        if (empty($response) || empty($response->response) || $response->status != 'success') {
            throw new Exception($response->response->detail);
        }
        return '';
    }
    
    /**
     * Update user location
     * @param type $account_id
     * @param type $user_id
     */
    public function update_user_call_incomming($account_id, $user_id, $data = array()) {
        // Pull in an array of tweets
        $response_str = $this->post('/account/' . $account_id . '/user/'.$user_id.'/call/incoming', $data);
        $response = json_decode($response_str);
        if (!empty($response) && !empty($response->response) && !empty($response->response->user_id)) {
            return $response->response->user_id;
        }
        if (empty($response) || empty($response->response) || $response->status != 'success') {
            throw new Exception($response->response->detail);
        }
        return '';
    }

    /**
     * Create new sub account.
     *
     */
    public function delete_user($account_id, $user_id) {
        $input = array();
        // Pull in an array of tweets
        $response_str = $this->delete('/account/' . $account_id . '/user/' . $user_id, $input);
        $response = json_decode($response_str);
        return $response;
    }
    
    /**
     * Get list available phone number by area code.
     * 
     * @param type $country
     * @param type $area_code
     */
    public function get_list_available_phonenumber($country, $area_code) {
        $input = array();
        // Pull in an array of tweets
        $response_str = $this->get('/numberstocksummary/' . $country . '/availablephonenumber?area_code=' . $area_code, $input);
        $response = json_decode($response_str);
        return $response;
    }
    
    /**
     * Get list phone number subscription
     * @param type $account_id
     */
    public function get_phonenumbersubscription($account_id) {
        $input = array();
        // Pull in an array of tweets
        $response_str = $this->get('/account/' . $account_id . '/phonenumbersubscription', $input);
        $response = json_decode($response_str);
        return $response->response;
    }
    
    /**
     * Get list phone number subscription
     * @param type $account_id
     */
    public function get_phonenumbersubscription_byphonenumber($account_id, $phone_number) {
        $input = array();
        // Pull in an array of tweets
        $response_str = $this->get('/account/' . $account_id . '/phonenumbersubscription/'.$phone_number, $input);
        $response = json_decode($response_str);
        return $response->response;
    }
    
    /**
     * Create new phone number.
     * 
     * @param type $account_id
     * @param type $phone_number
     */
    public function create_new_phone_number($account_id, $phone_number) {
        $input = array(
            "phnum" => $phone_number
        );
        // Pull in an array of tweets
        $response_str = $this->post('/account/' . $account_id . '/phonenumbersubscription', $input);
        $response = json_decode($response_str);
        return $response;
    }
    
    /**
     * Update new phone number connect to given phone user id
     * 
     * @param type $account_id
     * @param type $phone_number
     */
    public function update_phone_number($account_id, $phone_number, $input) {
        
        // Pull in an array of tweets
        $response_str = $this->post('/account/' . $account_id . '/phonenumbersubscription/'.$phone_number, $input);
        $response = json_decode($response_str);
        return $response;
    }
    
    /**
     * Delete new phone number connect to given phone user id
     * 
     * @param type $account_id
     * @param type $phone_number
     */
    public function delete_assign_phone_number($account_id, $phone_number, $phone_user_id, $number_id) {
        $input = array(
            "connect_to_type" => '',
            "connect_to" => ''
        );
        // Pull in an array of tweets
        $response_str = $this->post('/account/' . $account_id . '/phonenumbersubscription/'.$phone_number, $input);
        $response = json_decode($response_str);
        
        $input = array();
        $response_str = $this->delete('/account/' . $account_id . '/user/'.$phone_user_id.'/phones/'.$number_id, $input);
        $response = json_decode($response_str);
        
        return $response;
    }
    
    /**
     * Delete phone number.
     * 
     * @param type $account_id
     * @param type $phone_number
     */
    public function delete_phone_number($account_id, $phone_number) {
        $input = array(
        );
        // Pull in an array of tweets
        $response_str = $this->delete('/account/' . $account_id . '/phonenumbersubscription/'.$phone_number, $input);
        $response = json_decode($response_str);
        return $response;
    }
    
    /**
     * Add phones to given user
     * 
     * @param type $account_id
     * @param type $phone_number
     */
    public function add_phones($account_id, $phone_user_id, $phone_name, $phone_type, $phone_number) {
        $input = array(
            "phnum_name" => $phone_name,
            "phone_type" => $phone_type, 
            "phnum" => $phone_number, 
            "receive_calls" => "yes", 
        );
        // Pull in an array of tweets
        $response_str = $this->post('/account/' . $account_id . '/user/'.$phone_user_id.'/phones', $input);
        $response = json_decode($response_str);
        if ($response->status == 'success') {
            return $response->response->phone_id;
        }
        return '';
    }
    
    /**
     * Add phones to given user
     * 
     * @param type $account_id
     * @param type $phone_number
     */
    public function update_phones($account_id, $phone_user_id, $phone_id, $phone_name, $phone_type, $phone_number) {
        $input = array(
            "phnum_name" => $phone_name,
            "phone_type" => $phone_type, 
            "phnum" => $phone_number, 
            "receive_calls" => "yes", 
        );
        // Pull in an array of tweets
        $response_str = $this->put('/account/' . $account_id . '/user/'.$phone_user_id.'/phones/'.$phone_id, $input);
        $response = json_decode($response_str);
        return $response;
    }
    
    /**
     * Add phones to given user
     * 
     * @param type $account_id
     * @param type $phone_number
     */
    public function delete_phones($account_id, $phone_user_id, $phone_id) {
        $input = array(
        );
        // Pull in an array of tweets
        $response_str = $this->delete('/account/' . $account_id . '/user/'.$phone_user_id.'/phones/'.$phone_id, $input);
        $response = json_decode($response_str);
        return $response;
    }

    /**
     * Create new voice app.
     * 
     * @param type $name
     * @param type $app_type
     * @param type $other_fields
     */
    public function create_new_voiceapp($account_id, $name, $app_type, $other_fields = array()) {
        $input = array(
            "name" => $name,
            "app_type" => $app_type
        );
        
        $voicemail_local_path = isset($other_fields['voicemail_local_path']) ? $other_fields['voicemail_local_path'] : '';
        unset($other_fields['voicemail_local_path']);
        
        // Combine other fields
        if ($other_fields != null && count($other_fields) > 0) {
            $input = array_merge($input, $other_fields);
        }
        // Pull in an array of tweets
        $response_str = $this->post('/account/' . $account_id . '/voiceapp', $input);
        $response = json_decode($response_str);
        // Validate response
        if ($response->status == 'failed') {
            return '';
        }
        $app_id = $response->response->app_id;
        // Create system prompt
        if (!empty($voicemail_local_path) && $app_type == 'ivr') {
            // Create new prompt
            $this->create_new_prompt($account_id, $app_id, $name, $voicemail_local_path);
        }
        
        return $app_id;
    }
    
    /**
     * Update the existing voice app.
     * 
     * @param type $name
     * @param type $app_type
     * @param type $other_fields
     */
    public function update_voiceapp($account_id, $voice_app_id, $name, $other_fields = array()) {
        $input = array(
            "name" => $name
        );
        
        // Combine other fields
        if ($other_fields != null && count($other_fields) > 0) {
            $input = array_merge($input, $other_fields);
        }
        // Pull in an array of tweets
        $response_str = $this->put('/account/' . $account_id . '/voiceapp/'.$voice_app_id, $input);
        $response = json_decode($response_str);
        // Validate response
        if ($response->status == 'failed') {
            return '';
        }
        return $response->response->app_id;
    }
    
    /**
     * Create new system prompt
     * @param type $account_id
     * @param type $voice_app_id
     * @param type $name
     * @return type
     */
    public function create_new_prompt($account_id, $voice_app_id, $name, $voicemail_local_path) {
        $input = array();

        // Pull in an array of tweets
        $response_str = $this->post('/account/' . $account_id . '/voiceapp/'.$voice_app_id.'/prompt?name='.$name, $input);
        $response = json_decode($response_str);
        // Validate response
        if ($response->status == 'failed') {
            return '';
        }
        
        // Get Id
        $prompt_id = $response->response->prompt_id;
        $message_url = $response->response->message_url;
        if (!empty($voicemail_local_path) && !empty($message_url)) {
            // $message_url = https://api.sonetel.com/prompt/PT132439/message 
            // POST https://api.sonetel.com/prompt/PT132439/message 
            $this->post_upload_file('https://'.$message_url, $voicemail_local_path);
        }
        return $prompt_id;
    }

    /**
     * Get list voice app
     * 
     * @param type $name
     * @param type $app_type
     * @param type $other_fields
     */
    public function get_list_voiceapp($account_id) {
        // Pull in an array of tweets
        $response_str = $this->get('/account/' . $account_id . '/voiceapp', null);
        $response = json_decode($response_str);
        return $response;
    }
    
    /**
     * Get list voice app
     * 
     * @param type $name
     * @param type $app_type
     * @param type $other_fields
     */
    public function get_voiceapp_detail($account_id, $voice_app_id) {
        // Pull in an array of tweets
        $response_str = $this->get('/account/' . $account_id . '/voiceapp/'.$voice_app_id, null);
        $response = json_decode($response_str);
        return $response;
    }
    
    /**
     * Delete voice app.
     * 
     * @param type $account_id
     * @param type $voice_app_id
     */
    public function delete_voiceapp($account_id, $voice_app_id) {
        $input = array(
        );
        // Pull in an array of tweets
        $response_str = $this->delete('/account/' . $account_id . '/voiceapp/'.$voice_app_id, $input);
        $response = json_decode($response_str);
        return $response;
    }
    
    /**
     * get list of inbound records.
     * 
     * @param type $account_id
     */
    public function get_list_inbound_records($account_id){
        $response = $this->get('/account/'.$account_id.'/usagerecord?service=inbound_call&field=usage,charges', null);
        
        $repsone_json = json_decode($response);
        
        if((isset($repsone_json['response']) && $repsone_json['response'] == "No entries found") 
                || (isset($repsone_json['status']) && $repsone_json['status'] == false)  ){
            return array();
        }
        return $repsone_json;
    }
    
    /**
     * get list of outbound records.
     * 
     * @param type $account_id
     */
    public function get_list_outbound_records($account_id){
        $response = $this->get('/account/'.$account_id.'/usagerecord?service=outbound_call&field=usage,charges', null);
        
        $repsone_json = json_decode($response);
        return $repsone_json;
    }
    
    /**
     * Get phone number price by country
     */
    public function get_phone_number_price() {
        $response = $this->get('/country?fields=price&currency=EUR', null);
        
        $repsone_json = json_decode($response);
        return $repsone_json;
    }
    
    /**
     * Get phone number price by country
     */
    public function get_outboundcalls_price() {
        $response = $this->get('/price-list/outboundcalls-price?currency=EUR', null);
        
        $repsone_json = json_decode($response);
        return $repsone_json;
    }
    
    /**
     * get all avaiables phone numbers for all countries
     */
    public function get_availble_phonenumber($country_code){
        ini_set('memory_limit', '-1');
        $response = $this->get('/numberstocksummary/'.$country_code.'/availablephonenumber', null);
        
        $repsone_json = json_decode($response);
        return $repsone_json;
    }
    
    /**
     * gets phone number description
     * 
     * @param type $country_code
     * @param type $phone_number
     * @return type
     */
    public function get_phonenumber_description($country_code, $phone_number){
        $response = $this->get('/rest/numberstocksummary/'.$country_code.'/availablephonenumber/'.$phone_number, null);
        
        $repsone_json = json_decode($response);
        return $repsone_json;
    }
    
    /**
     * get all avaiables phone numbers for all countries
     */
    public function get_countries(){
        $response = $this->get('/country?phnum_support=yes', null);
        
        $repsone_json = json_decode($response);
        return $repsone_json;
    }
}
