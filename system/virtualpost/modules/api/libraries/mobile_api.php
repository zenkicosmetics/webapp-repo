<?php defined('BASEPATH') or exit('No direct script access allowed');

class mobile_api  {
    public function __construct() {
        ci()->load->model(array(
            'price/pricing_m',
            'customers/customer_m',
            'price/pricing_template_m',
            'addresses/location_pricing_m',
            'users/mobile_session_m'
        ));
        
        ci()->load->library(array(
            'users/users_api'
        ));

    }

    /**
     * View pricing information. Get pricing structure for each account type.
     *
     * @uses $_POST['location_id']
     * @return array {'code' => 1020, 'message' => 'view_pricing_info', 'result' => array()}
     */
    public static function view_pricing_info($location) {
        // pricing template.
        $pricing_template_id = '';
        if (!empty($location)) {
            $pricing_template_id = $location->pricing_template_id;
        }

        // Gets all location.
        if (empty($pricing_template_id)) {
            // Get first pricing template of this location
            $pricing_template_id = APConstants::DEfAULT_PRICING_MODEL_TEMPLATE;
        }

        // Get don gia cua tat ca cac loai account type
        $pricings = ci()->pricing_m->get_pricing_by_template($pricing_template_id);

        $pricing_map = array();
        foreach ($pricings as $price) {
            // exception list: does not display at the moment.
            if($price->item_name == 'address_number' 
                    || $price->item_name == 'include_pages_scanning_number'
                    || $price->item_name == 'include_pages_scanning_number'
                    || $price->item_name == 'price_for_additional'
                    || $price->item_name == 'additional_price_included_page_opening_scanning'
                    || $price->item_name == 'include_pages_scanning_number'
                    || $price->item_name == 'additional_private_mailbox'
                    || $price->item_name == 'additional_business_mailbox'
                    || $price->item_name == 'include_pages_scanning_number'
                    || $price->item_name == 'official_address_verification'
                    || $price->item_name == 'own_domain'
            ){
                continue;
            }
            
            if($price->item_name == 'name_on_the_door') {
	            $price->item_name = "name_plate_at_the_entrance";
	            $price->order = lang('name_plate_at_the_entrance_order');
	            $price->text = lang('name_plate_at_the_entrance_text');
            }

            if (!array_key_exists($price->account_type, $pricing_map)) {
                $pricing_map [$price->account_type] = array();
            }
            
            
            $price->order = lang($price->item_name . '_order');
            $price->text = (string)lang($price->item_name . '_text');
            $format_value = self::format_price_value($price->item_name, $price->item_value, $price->item_unit);
            
            // unset value of postbox_fee_as_you_go
            if($price->item_name == 'postbox_fee_as_you_go' && $price->account_type != APConstants::FREE_TYPE){
                $price->item_value = "";
            }else{
                $price->item_value = $format_value;
            }
            $pricing_map [$price->account_type] [$price->item_name] = $price;
        }

        return $pricing_map;
    }

    /**
     * Format price value by name.
     *
     * @param unknown_type $name
     * @param unknown_type $value
     */
    public function format_price_value($name, $value, $unit) {
        if($name == 'name_on_the_door' || $name =='name_plate_at_the_entrance' 
                || $name == 'hand_sorting_of_advertising' || $name == 'cloud_service_connection'){
            return $value;
        } else if ($name == 'postbox_fee' || $name == 'additional_incomming_items' || $name == 'envelop_scanning' || $name == 'opening_scanning'
            || $name == 'send_out_directly' || $name == 'send_out_collected' || $name == 'storing_items_over_free_letter' || $name == 'storing_items_over_free_packages'
            || $name == 'additional_private_mailbox' || $name == 'additional_business_mailbox' || $name == 'custom_declaration_outgoing_01' || $name == 'custom_declaration_outgoing_02'
            || $name == 'cash_payment_on_delivery_mini_cost' || $name == 'official_address_verification' || $name == 'pickup_charge' || $name == 'additional_pages_scanning_price'
            || $name == 'special_requests_charge_by_time'
        ) {
            return APUtils::number_format($value);
        } else if ($name == 'storage' || $name == 'trashing_items') {
            if ($value != 0 && $value != -1) {
                return APUtils::number_format($value);;
            } else {
                return 'Unlimited';
            }
        } else if ($name == 'paypal_transaction_fee') {
            return APUtils::number_format($value);
        } else {
            return APUtils::number_format($value);
        }

    }
  
    public static function refeshUserSession($user_login){
        if (! empty($user_login) && ! empty($user_login->display_name)) {
            users_api::set_group_user($user_login);
        }

        $new_session_key = APUtils::generateRandom(64);
        $new_session_key = md5($new_session_key);

        // Delete all old activity
        ci()->mobile_session_m->delete_by_many(array(
            'last_activity <' => now() - APIAdmin_Controller::EXPIRED_TIME
        ));
        // Build session key
        ci()->mobile_session_m->insert(array(
            "session_key" => $new_session_key,
            "ip_address" => APUtils::getIPAddress(),
            "user_agent" => APUtils::getUserAgent(),
            "last_activity" => now(),
            "user_data" => json_encode($user_login)
        ));

        $response = array(
            'message' => lang('login.successfully'),
            'result'  => $user_login,
            'session_key' => $new_session_key
        );
        return $response;
    }

    /**
     * save receipt by id
     * @param type $receipt_id
     * @param type $data_input
     * @return type
     * @throws ThirdPartyException
     */
    public static function save_receipt($receipt_id, $data_input){
        ci()->load->model(array(
            'report/partner_receipt_m',
            "addresses/location_m",
            'partner/partner_m',
        ));
        ci()->load->library('S3');
        
        // get receipt check.
        $receipt_check = ci()->partner_receipt_m->get($receipt_id);
            
        // upload file on amazone.
        $data = $data_input;
        if( (!empty($receipt_check) && (!empty($data['local_file_path']) && $receipt_check->local_file_path != $data['local_file_path']) 
                || (!empty($data['local_file_path']) && empty($receipt_check) )) ){
            $old_file = !empty($receipt_check) ? $receipt_check->local_file_path : "";

            // Upload file to S3
            $default_bucket_name = ci()->config->item('default_bucket');
            $server_file_name = $data['local_file_path'];
            $upload_file_name_tmp = explode('/', $server_file_name);
            $upload_file_name = $upload_file_name_tmp[count($upload_file_name_tmp) - 1];
            $ext_file = explode('.', $upload_file_name);
            // Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE)
            $amazon_relate_path = 'partner_receipts/' . $data['partner_id'] . '/' . $data['partner_id'].'_'.date('YmdHis') . '.' . $ext_file[1];
            $local_file_path = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE).'partner_receipts/' . $data['partner_id'];
            if(!is_dir($local_file_path)){
                mkdir($local_file_path, 0777, TRUE);
                chmod($local_file_path, 0777);
            }
            
            // move temp file into local server file name
            copy($data['local_file_path'], Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE).$amazon_relate_path);
            
            try{
                $upload_result = S3::putObjectFile(Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE).$amazon_relate_path, $default_bucket_name, $amazon_relate_path, S3::ACL_PRIVATE);
                if(!$upload_result){
                    throw new ThirdPartyException("Can not upload file to S3. Please try it again.");
                    return;
                }
            }  catch (Exception $ex){
                throw new ThirdPartyException(strip_tags($ex->getMessage()));
                return;
            }

            // update file path.
            $data['local_file_path'] = Settings::get(APConstants::ABSOLUTE_PATH_DATA_FILE).$amazon_relate_path;
            $data['amazon_file_path'] = $amazon_relate_path;

            // delete older file
            if(!empty($old_file)){
                unlink($old_file);
                unlink($server_file_name);
                S3::deleteObject($default_bucket_name, $receipt_check->amazon_file_path);
            }
        }

        // update receipt
        if(empty($receipt_check)){
            $receipt_id = ci()->partner_receipt_m->insert($data);
        }else{
            ci()->partner_receipt_m->update_by_many(array(
                "id" => $receipt_id
            ), $data);
        }
        
        return $receipt_id;
    }
}