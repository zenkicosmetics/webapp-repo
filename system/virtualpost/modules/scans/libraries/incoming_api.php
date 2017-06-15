<?php defined('BASEPATH') or exit('No direct script access allowed');

class incoming_api extends Core_BaseClass
{
    public function __construct() {
        ci()->load->model(array(
            'scans/envelope_m',
            'settings/settings_m'
        ));
    }
    
    /**
     * Gets incomming list.
     */
    public function getIncomingList($customer_id, $from_customer_name, $type, $weight, $term, $list_filter_location_id, $input_paging, $limit){

        #1058 add multi dimension capability for admin
    	$date_format = APUtils::get_date_format_in_user_profiles();
    	$decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
    	$weight_unit = APUtils::get_weight_unit_in_user_profiles();
        
        // Gets last 2 years.
        $last2years = now() - 2 * 365 * 24 * 60 * 60;
        $array_condition = array(
            "from_customer_name LIKE " => '%' . $from_customer_name . '%',
            "incomming_date >= " => $last2years,
            "incomming_letter_flag" => APConstants::OFF_FLAG
        );

        if(!empty($list_filter_location_id)){
            
            $array_condition['location_id IN ' . "(" . implode(",", $list_filter_location_id) . ")"] = null;
        }

        $array_condition['(envelopes.deleted_flag is null or envelopes.deleted_flag = 0 )'] = null;

        // fixbug #459
        $array_condition["(p.postbox_name LIKE {$term} OR p.name LIKE {$term} OR customers.email LIKE {$term} OR customers.customer_code LIKE {$term})"] = null;

        if (!empty($type)) {
            $setting = ci()->settings_m->get_by_many(
                array(
                    'SettingCode' => APConstants::ENVELOPE_TYPE_CODE,
                    'LabelValue' => $type
                ));
            if ($setting) {
                $array_condition['envelope_type_id'] = $setting->ActualValue;
            }
        }
        if (!empty($weight)) {
            $array_condition['weight'] = $weight;
        }
        if (!empty($customer_id)) {
            $array_condition['to_customer_id'] = $customer_id;
        }
        //echo "<pre>";print_r($array_condition);exit;
        // Get paging input
        $input_paging['limit'] = $limit;
        // Call search method
        $query_result = ci()->envelope_m->get_envelope_paging_incomming($array_condition, $input_paging['start'], $input_paging['limit'],
            $input_paging['sort_column'], $input_paging['sort_type']);
        //echo "<pre>";print_r($query_result);exit;
        // Process output data
        $total = $query_result['total'];
        $datas = $query_result['data'];
        
        // Get output response
        $response = $this->get_paging_output($total, $input_paging['limit'], $input_paging['page']);

        $datas_mobile = array();
        $i = 0;
        foreach ($datas as $row) {
            $auto_scan = 'No';
            if ($row->auto_envelope_scan_flag == APConstants::ON_FLAG || $row->auto_item_scan_flag == APConstants::ON_FLAG) {
                $auto_scan = 'Yes';
            }

            #1058 add multi dimension capability for admin
            $weight = APUtils::view_convert_number_in_weight( $row->weight, $weight_unit, $decimal_separator);
            
            $type_id = Settings::get_label(APConstants::ENVELOPE_TYPE_CODE, $row->envelope_type_id);
            $category = Settings::get_label(APConstants::CATEGORY_TYPE_CODE, $row->category_type);
            $incomming_date = APUtils::viewDateFormat($row->incomming_date, $date_format . APConstants::TIMEFORMAT_OUTPUT01);

            $response->rows[$i]['id'] = $row->id;
            $response->rows[$i]['cell'] = array(
                $row->id,
                $row->envelope_code,
                $row->from_customer_name,
                $row->to_customer_id,
                $row->to_customer_name,
                $row->envelope_type_id,
                $type_id,
                $weight,
                $row->category_type,
                $category,
                $row->invoice_flag,
                $incomming_date,
                $auto_scan,
                $row->id,
                $row->activated_flag
            );

            //Data for mobile
            $row->auto_scan = $auto_scan;
            $row->type_id = $type_id;
            $row->weight     = $weight;
            $row->category   = $category;
            $row->incomming_date = $incomming_date;

            $datas_mobile[$i] = $row;

            $i++;
        }

        return  array(
                    "mobile_incomming_list" => $datas_mobile,
                    "web_incomming_list"    => $response
                );
    }

    /**
     * Add new incoming item by worker in back-end
     */
    public static function add_incomming($customer_id, $from_customer_name, $to, $postbox_id,$type, $labelValue, $width, $height, $weight, $length){

        ci()->load->model(array(
            'mailbox/postbox_m',
            'mailbox/postbox_setting_m',
            'customers/customer_m',
            'scans/envelope_m',
            'scans/envelope_pdf_content_m',
            'scans/setting_m',
            'addresses/location_m',
            'email/email_m',
            'addresses/customers_address_m',
            'scans/envelope_properties_m',
            'mailbox/envelope_customs_m',
            'scans/envelope_file_m'
        ));
        
        ci()->load->library(array(
            'scans/scans_api',
            'price/price_api',
            'mailbox/mailbox_api',
            'invoices/invoices',
            "shipping/shipping_api"
        ));

        ci()->lang->load('api/api');

        log_audit_message(APConstants::LOG_ERROR, " START add incomming of customer:" . $customer_id . ', TIME:' . time(), false, 'auditlog-incomming');
        
        // Get setting of customer id
        $postbox_setting = ci()->postbox_setting_m->get_by_many(
            array(
                'customer_id' => $customer_id,
                "postbox_id" => $postbox_id
            ));

        // Get setting of customer id
        $postbox = ci()->postbox_m->get_by_many(
            array(
                "postbox_id" => $postbox_id
            ));
        
        //Check postbox data
        if(empty($postbox)){
            $response['message'] = admin_language('scan_lib_incoming_api_PostboxDoesNotExists', array('postbox_id' => $postbox_id));
            $response['status'] = false;
            return $response;
        }
        
        //Check postbox delele
        if($postbox->deleted == APConstants::ON_FLAG ){
            $response['message'] = admin_language('scan_lib_incoming_api_PostboxDeleted');
            $response['status'] = false;
            return $response;
        }

        // Get setting of customer id
        $customer_setting = ci()->customer_m->get_by_many(
            array(
                'customer_id' => $customer_id
        ));
        
        if (empty($postbox_setting)) {
            $postbox_setting = new stdClass();
            $postbox_setting->always_scan_envelope = '0';
            $postbox_setting->always_scan_envelope_vol_avail = '0';
            $postbox_setting->always_scan_incomming = '0';
            $postbox_setting->always_scan_incomming_vol_avail = '0';
            $postbox_setting->email_notification = '0';
            $postbox_setting->always_forward_directly = '0';
            $postbox_setting->always_forward_collect = '0';
            $postbox_setting->invoicing_cycle = '0';
            $postbox_setting->collect_mail_cycle = '0';
            $postbox_setting->weekday_shipping = '2';
            $postbox_setting->always_mark_invoice = APConstants::OFF_FLAG;
        }

        $envelope_scan_flag = null;
        $item_scan_flag = null;
        $new_notification_flag = APConstants::ON_FLAG;
        $accounting_email_flag = APConstants::OFF_FLAG;
        
        // Check included item for scan.
        $included_envelope_scan_status = 0;
        $included_item_scan_status = 0;

        // Ticket #563 Case Management
        $verification_completed_flag = APConstants::OFF_FLAG;
        if (CaseUtils::isVerifiedAddress($customer_id) && CaseUtils::isVerifiedPostboxAddress($postbox_id, $customer_id)) {
            $verification_completed_flag = APConstants::ON_FLAG;
        }

        if ($customer_setting->activated_flag == APConstants::ON_FLAG
            && $verification_completed_flag == APConstants::ON_FLAG
            && ($postbox_setting->always_scan_envelope_vol_avail == '1' || $postbox_setting->always_scan_incomming_vol_avail == '1')
        ) {
            $postbox = ci()->postbox_m->get_by('postbox_id', $postbox_id);

            $pricings = price_api::getPricingMapByLocationId($postbox->location_available_id);
            
            // fixbug #1114
            $max_envelope_scan_volumn = $pricings[$postbox->type]['envelope_scanning_front']->item_value;
            $max_item_scan_volumn = $pricings[$postbox->type]['included_opening_scanning']->item_value;
            
            if ($postbox_setting->always_scan_envelope_vol_avail == '1') {
                // Gets current envelope scan number
                $current_envelope_scan_num = scans_api::getNumberEnvelopeScansOfCurrentMonth($customer_id, $postbox_id);
                if($current_envelope_scan_num < $max_envelope_scan_volumn){
                    // Set trang thai la request envelope scan
                    $envelope_scan_flag = APConstants::OFF_FLAG;
                    $new_notification_flag = APConstants::OFF_FLAG;
                    $included_envelope_scan_status = 1;
                }
            }
            
            if ($postbox_setting->always_scan_incomming_vol_avail == '1') {
                // Gets current envelope scan number
                $current_item_scan_num = scans_api::getNumberDocumentScansOfCurrentMonth($customer_id, $postbox_id);
                
                if($current_item_scan_num < $max_item_scan_volumn){
                    // Set trang thai la request item scan
                    $item_scan_flag = APConstants::OFF_FLAG;
                    $new_notification_flag = APConstants::OFF_FLAG;
                    $included_item_scan_status = 1;
                }
            }
        }

        if ($customer_setting->activated_flag == APConstants::ON_FLAG && $verification_completed_flag == APConstants::ON_FLAG
            && $postbox_setting->always_scan_envelope === '1'
        ) {
            // Set trang thai la request envelope scan
            $envelope_scan_flag = '0';
            $new_notification_flag = APConstants::OFF_FLAG;
        }
        if ($customer_setting->activated_flag == APConstants::ON_FLAG && $verification_completed_flag == APConstants::ON_FLAG
            && $postbox_setting->always_scan_incomming === '1'
        ) {
            // Set trang thai la request item scan
            $item_scan_flag = '0';
            $new_notification_flag = APConstants::OFF_FLAG;
        }

        $incomming_date_only = date('dmy');
        $envelope_code = $postbox->postbox_code . '_' . $incomming_date_only;

        // Count all envelope of current day
        $number_envelope = ci()->envelope_m->get_max_envelope_code(
            array(
                'to_customer_id' => $customer_id,
                'postbox_id' => $postbox_id,
                'incomming_date_only' => $incomming_date_only
            ));
        $number_envelope += 1;
        $envelope_code = $envelope_code . '_' . sprintf('%1$03d', $number_envelope);
        
           
        //Mark auto send PDF to accounting email
         if ($postbox_setting->always_mark_invoice == APConstants::ON_FLAG && !empty(EnvelopeUtils::get_accounting_interface_by_postbox($postbox_id)['email'])) {
            $accounting_email_flag = APConstants::ON_FLAG;
         }

        // Mark auto scan envelope/item
        $auto_envelope_scan_flag = APConstants::OFF_FLAG;
        if ($envelope_scan_flag == '0' && $verification_completed_flag == APConstants::ON_FLAG) {
            $auto_envelope_scan_flag = APConstants::ON_FLAG;
        }
        $auto_item_scan_flag = APConstants::OFF_FLAG;
        if ($item_scan_flag == '0' && $verification_completed_flag == APConstants::ON_FLAG) {
            $auto_item_scan_flag = APConstants::ON_FLAG;
        }
     
        
        #1058 add multi dimension capability for admin
        $weight_unit = APUtils::get_weight_unit_in_user_profiles();
        
        // Insert information to envelope table
        $id = ci()->envelope_m->insert(
            array(
                'from_customer_name' => $from_customer_name,
                'to_customer_id' => $customer_id,
                'postbox_id' => $postbox_id,
                'envelope_code' => $envelope_code,
                'envelope_type_id' => $type,
                'weight' => APUtils::convert_number_in_weight( $weight, $weight_unit ), #1058 add multi dimension capability for admin
                'weight_unit' => 'g',
                'last_updated_date' => now(),
                'incomming_date' => now(),
                'incomming_date_only' => $incomming_date_only,
                'completed_flag' => APConstants::OFF_FLAG,
                'category_type' => null,
                'invoice_flag' => $accounting_email_flag,
                "envelope_scan_flag" => $envelope_scan_flag,
                "item_scan_flag" => $item_scan_flag,
                "email_notification_flag" => APConstants::OFF_FLAG,
                "new_notification_flag" => $new_notification_flag,
                "location_id" => $postbox->location_available_id,
                "auto_envelope_scan_flag" => $auto_envelope_scan_flag,
                "auto_item_scan_flag" => $auto_item_scan_flag
            ));
        
        $completed_by = APContext::getAdminIdLoggedIn();
        
        // Insert completed activity (Registered incoming)
        scans_api::insertCompleteItem($id, APConstants::REGISTERED_INCOMMING_ACTIVITY_TYPE, APConstants::TRIGGER_BY_ADMIN, $completed_by);
        
        // trigger storage nubmer report.
        scans_api::updateStorageStatus($id, $customer_id, $postbox_id, APUtils::getCurrentYear(), APUtils::getCurrentMonth(), $postbox->location_available_id, APConstants::ON_FLAG);
        
        // Prepare data to send email
        $send_prepayment_email = false;
        $open_balance_due = 0;
        $open_balance_this_month = 0;
        $total_prepayment_cost = 0;
            
        // Automatically scan envelope incoming 
        if ($envelope_scan_flag == '0' && $verification_completed_flag == APConstants::ON_FLAG) {
            //Log request scan envelope by system
            scans_api::insertCompleteItem($id, APConstants::SCAN_ENVELOPE_ORDER_BY_SYSTEM_ACTIVITY_TYPE);
            // Check prepayment with envelope's scan type
            $check_prepayment_data = CustomerUtils::checkApplyScanPrepayment(APConstants::TRIGGER_ACTION_TYPE_SYSTEM, 
            APConstants::ENVELOPE_SCAN_TYPE, array($id), $customer_id, false);
            
            // Only request if pass pre-paymnet
            if ($check_prepayment_data['prepayment'] == true) {
                
                $send_prepayment_email = true;
                $open_balance_due = $check_prepayment_data['open_balance_due'];
                $open_balance_this_month = $check_prepayment_data['open_balance_this_month'];
                $total_prepayment_cost += $check_prepayment_data['estimated_cost'];
                
                // Update envelope
                ci()->envelope_m->update_by_many(array(
                    "id" => $id
                ), array(
                    "auto_envelope_scan_flag" => APConstants::OFF_FLAG,
                    "envelope_scan_flag" => NULL
                ));
                
                // Add envelope scan request to queue (orange)
                mailbox_api::requestEnvelopeScanToQueue($id, $customer_id);
                
                //Log activity
                scans_api::insertCompleteItem($id, APConstants::REQUEST_PREPAYMENT_FOR_SCAN_ENVELOPE_BY_SYSTEM_ACTIVITY_TYPE);
            
            } 
            
            // update envelope scan number.
            scans_api::updateEnvelopeScanNumber($customer_id, $postbox_id, $id, 0, $included_envelope_scan_status);
        }
        
        // Automatically scan item incoming 
        if ($item_scan_flag == '0' && $verification_completed_flag == APConstants::ON_FLAG) {
            //Log request scan envelope by system
            scans_api::insertCompleteItem($id, APConstants::SCAN_ITEM_ORDER_BY_SYSTEM_ACTIVITY_TYPE);
            // Check prepayment with item's scan type
            $check_prepayment_data = CustomerUtils::checkApplyScanPrepayment(APConstants::TRIGGER_ACTION_TYPE_SYSTEM, 
                     APConstants::ITEM_SCAN_TYPE, array($id), $customer_id, false);
            
            // Only request if pass pre-paymnet
            if ($check_prepayment_data['prepayment'] == true) {
                
                $send_prepayment_email = true;
                $open_balance_due = $check_prepayment_data['open_balance_due'];
                $open_balance_this_month = $check_prepayment_data['open_balance_this_month'];
                $total_prepayment_cost += $check_prepayment_data['estimated_cost'];
                
                // Update item
                ci()->envelope_m->update_by_many(array(
                    "id" => $id
                ), array(
                    "auto_item_scan_flag" => APConstants::OFF_FLAG,
                    "item_scan_flag" => NULL
                ));
                
                 // Add item scan request to queue
                mailbox_api::requestItemScanToQueue($id, $customer_id);
                
                //Insert activity
                scans_api::insertCompleteItem($id, APConstants::REQUEST_PREPAYMENT_FOR_SCAN_ITEM_BY_SYSTEM_ACTIVITY_TYPE);

            } 
            
            // update item scan number.
            scans_api::updateItemScanNumber($customer_id, $postbox_id, $id, 0, $included_item_scan_status);
        }
            
        // Insert information to envelope_properties table (if
        // envelope_type is 'Package')
        $type_actual_value = $type;
        $type_label_value  = $labelValue;
        #1058 add multi dimension capability for admin
        $length_unit = APUtils::get_length_unit_in_user_profiles();
        if (!empty($type_actual_value)) {
            $envelope_type = ci()->settings_m->get_by_many(
                array(
                    'SettingCode' => APConstants::ENVELOPE_TYPE_CODE,
                    'LabelValue'  => $type_label_value,
                    'ActualValue' => $type_actual_value
                ));
            // if envelope_type is 'Package'
            if ($envelope_type && $envelope_type->Alias02 == 'Package') {
                $envelope_properties_id = ci()->envelope_properties_m->insert(
                    array(
                        // #1058 add multi dimension capability for admin
                        'width' => 	APUtils::convert_number_in_length( $width,  $length_unit),
                        'height' => APUtils::convert_number_in_length( $height, $length_unit),
                        'length' => APUtils::convert_number_in_length( $length, $length_unit),
                        'envelope_id' => $id
                    ));
            }
        }

        $envelope_content = '';
        //#1058 add multi dimension capability for admin 
        $date_format = APUtils::get_date_format_in_user_profiles();
        $weight_unit = APUtils::get_weight_unit_in_user_profiles();
        
        $envelope_content = $envelope_content . ' ' . $from_customer_name;
        $envelope_content = $envelope_content . ' ' . APUtils::convert_number_in_weight($weight, $weight_unit);
        $envelope_content = $envelope_content . ' ' . APUtils::viewDateFormat($incomming_date_only, $date_format);
        $envelope_content = $envelope_content . ' ' . Settings::get_label(APConstants::ENVELOPE_TYPE_CODE, $type);
        ci()->envelope_pdf_content_m->insert(
            array(
                "envelope_id" => $id,
                "customer_id" => $customer_id,
                "postbox_id" => $postbox_id,
                "created_date" => now(),
                "envelope_content" => $envelope_content
            ));

        // Insert incomming number to [envelope_summary_table]
        ci()->invoices->cal_incomming_invoices($customer_id, $postbox_id, $id);
        
        $activated_flag = $customer_setting->activated_flag;
        if ($postbox_setting->email_notification === '1' && $activated_flag == '1') {
            $to_email = $customer_setting->email;
            $from_email = ci()->config->item('EMAIL_FROM');

            // Send email confirm for user
            $activated_flag = $customer_setting->activated_flag;
            if ($activated_flag == '1') {
                $email_template_code = APConstants::new_incomming_notification;
            } else {
                $email_template_code = APConstants::new_incomming_notification_for_notactivated;
            }

            // Get location
            $location = ci()->location_m->get_by_many(
                array(
                    'id' => $postbox->location_available_id
                ));
            $location_name = '';
            if ($location) {
                $location_name = $location->location_name;
            }

            // Get customer name
            $customer_name = '';
            $customer_address = ci()->customers_address_m->get_by_many(
                array(
                    'customer_id' => $customer_id
                ));
            if (!empty($customer_address)) {
                if (!empty($customer_address->invoicing_address_name)) {
                    $customer_name = $customer_address->invoicing_address_name;
                } else if (!empty($customer_address->invoicing_company)) {
                    $customer_name = $customer_address->invoicing_company;
                }
            } else {
                $customer_name = $postbox->postbox_name;
            }

            $type = Settings::get_label(APConstants::ENVELOPE_TYPE_CODE, $type);
            $data = array(
                "slug" => $email_template_code,
                "to_email" => $to_email,
                // Replace content
                "full_name" => $customer_name,
                "site_url"  => APContext::getFullBalancerPath(),
                "locations" => $location_name,
                "from"      => $from_customer_name,
                "type"      => $type,
                "weight"    => $weight . "g"
            );

            try {
                MailUtils::sendEmailByTemplate($data);
            } catch (Exception $e) {
                log_message($e);
            }

            // Update trang thai send email
            ci()->envelope_m->update_by("id", $id,
                array(
                    "email_notification_flag" => APConstants::ON_FLAG
                ));
                
            // Register push message (IOS#35)
            
            $message = lang('push.new_incomming');
            scans_api::registerPushMessage($customer_id, $postbox_id, $id, $message, APConstants::PUSH_MESSAGE_INCOMMING_TYPE);
        }

        // Get customer address
        $address = ci()->customers_address_m->get_by('customer_id', $customer_id);
        $eu_member_flag = '1';
        if ($address) {
            $eu_member_flag = $address->eu_member_flag;
        }

        // Auto request direct shipping
        if ($postbox_setting->always_forward_directly === '1' && $verification_completed_flag == APConstants::ON_FLAG) {
            
            // Add direct shipping request
            scans_api::insertCompleteItem($id, APConstants::DIRECT_FORWARDING_ORDER_BY_SYSTEM_ACTIVITY_TYPE);
            
            $customs_process_flag = EnvelopeUtils::check_customs_flag($customer_id, $postbox_id, $id);
            if ($customs_process_flag == APConstants::ON_FLAG) {
                //mailbox_api::regist_envelope_customs($customer_id, $id, $postbox_id, APConstants::DIRECT_FORWARDING, '');
                scans_api::insertCompleteItem($id, APConstants::WAITING_FOR_CUSTOMS_DECLARITON_ACTIVITY_TYPE);
                incoming_api::send_email_declare_customs($customer_setting);
            }
            
            $check_prepayment_data = CustomerUtils::checkApplyShippingPrepayment(
                APConstants::TRIGGER_ACTION_TYPE_SYSTEM, 
                APConstants::SHIPPING_SERVICE_NORMAL, 
                APConstants::SHIPPING_TYPE_DIRECT, array($id), $customer_id, false);
            
            // Only request if pass pre-paymnet
            if ($check_prepayment_data['prepayment'] == true && $customs_process_flag != APConstants::ON_FLAG) {
                
                $send_prepayment_email = true;
                $open_balance_due = $check_prepayment_data['open_balance_due'];
                $open_balance_this_month = $check_prepayment_data['open_balance_this_month'];
                $total_prepayment_cost += $check_prepayment_data['estimated_cost'];
                
                // Add direct shipping request to queue
                mailbox_api::requestDirectShippingToQueue($id, $customer_id);
                
                 //Insert activity
                scans_api::insertCompleteItem($id, APConstants::REQUEST_PREPAYMENT_FOR_DIRECT_FORWARDING_BY_SYSTEM_ACTIVITY_TYPE);
              
            } else {

                //If dont need prepayment, check declare custom, request direct shipping
                mailbox_api::requestDirectShipping($id, $customer_id);
                scans_api::insertCompleteItem($id, APConstants::REQUEST_TRACKING_NUMBER_ACTIVITY_TYPE);
                
            }
        //Auto mark collect shipping
        } else if ($postbox_setting->always_forward_collect === APConstants::ON_FLAG && $verification_completed_flag == APConstants::ON_FLAG) {
            
            // Add collect shipping request
            mailbox_api::requestCollectShipping($id, $customer_id);
            scans_api::insertCompleteItem($id, APConstants::MARK_COLLECT_FORWARDING_ORDER_BY_SYSTEM_ACTIVITY_TYPE);
            
        }
        
        // Send email
        if ($send_prepayment_email) {
            $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
            $open_balance_due = $open_balance_data['OpenBalanceDue'];
            $open_balance_this_month = $open_balance_data['OpenBalanceThisMonth'];
            CustomerUtils::sendPrepaymentEmail($customer_id, $customer_setting->email, $open_balance_due, 
                $open_balance_this_month, $total_prepayment_cost);

            CustomerUtils::sendAutoForwardNotWorking($customer_id,$customer_setting->email, $postbox->postbox_name, $open_balance_data);
        }

        log_audit_message(APConstants::LOG_INFOR, " END add incomming of customer:" . $customer_id . ', TIME:' . time(), false, 'auditlog-incomming');

        if ($id) {
            
            $message = sprintf(lang('incomming.add_success'), $to);
            
            $response['message']      = $message;
            $response['status']       = true;
            $result = new stdClass;
            $result->envelope_id = $id;
            $result->envelope_code = $envelope_code;
            $response['result']  = $result;
            //$this->success_output($message, array('id' => $id, 'envelope_code' => $envelope_code));
            return $response;
        } else {

            $message = sprintf(lang('incomming.add_error'), $to);
            $response['message'] = $message;
            $response['status']  = false;
            //$this->error_output($message);
            return $response;
        }
    }

    public static function send_email_declare_customs($customer_setting)
    {
        $to_email = $customer_setting->email;
        $data = array(
            "slug" => APConstants::declare_customs_notification,
            "to_email" => $to_email,
            // Replace content
            "full_name" => $customer_setting->user_name,
            "site_url" => APContext::getFullBalancerPath() . 'mailbox/index?declare_customs=1'
        );

        try {
            MailUtils::sendEmailByTemplate($data);
        } catch (Exception $e) {
            log_message($e);
        }
    }

    public static function auto_postbox($term, $location_id)
    {
        
        ci()->load->model(array(
            'mailbox/postbox_m'
        ));
            
        $postboxs = ci()->postbox_m->get_all_postbox_by_location($term, $location_id);

        // Rudimentary search
        $matches = array();
        $temp = array();
        $arrDeletedPostbox = array();
        foreach ($postboxs as $postbox) {
            if ($postbox->deleted == APConstants::ON_FLAG) {
                $arrDeletedPostbox[] = $postbox;
                continue;
            }

            $temp['customer_id'] = $postbox->customer_id;
            $temp['customer_status'] = $postbox->status;
            $temp['postbox_id'] = $postbox->postbox_id;
            $temp['name'] = $postbox->name;
            $temp['activated_flag'] = $postbox->activated_flag;
            $temp['customer_id'] = $postbox->customer_id;
            $temp['company'] = $postbox->company;
            $temp['postbox_verify'] = (CaseUtils::isVerifiedPostboxAddress($postbox->postbox_id, $postbox->customer_id))? 1 : 0;

            // Owen update to add customer code to auto complete (#552)
            $temp['label'] = "{$postbox->customer_code}, {$postbox->email}, {$postbox->postbox_name}";

            if (!empty($postbox->name)) {
                $temp['label'] = $temp['label'] . ", {$postbox->name}";
            }
            if (!empty($postbox->company)) {
                $temp['label'] = $temp['label'] . ", {$postbox->company}";
            }

            $matches[] = $temp;
        }
        
        // separate deleted postboxes.
        $temp['label'] = "----------------------------------------------------------------------------";
        $matches[] = $temp;
        // #452: Assign deleted postbox.
        foreach ($arrDeletedPostbox as $postbox) {
            $temp['customer_id'] = $postbox->customer_id;
            $temp['customer_status'] = $postbox->status;
            $temp['postbox_id'] = $postbox->postbox_id;
            $temp['name'] = $postbox->name;
            $temp['activated_flag'] = $postbox->activated_flag;
            $temp['customer_id'] = $postbox->customer_id;
            $temp['company'] = $postbox->company;
            $temp['label'] = "{$postbox->email}, {$postbox->postbox_name}";
            if (!empty($postbox->name)) {
                $temp['label'] = $temp['label'] . ", {$postbox->name}";
            }

            // fixbug: #452
            if ($postbox->deleted == APConstants::ON_FLAG) {
                $temp['label'] = $temp['label'] . " - DELETED";
            }
            $matches[] = $temp;
        }

        $matches = array_slice($matches, 0, 100);
        return $matches;
    }

    public static function get_type($actualValue, $labelValue)
    {
        ci()->load->model(array(
            'settings/settings_m'
        ));

        $setting = ci()->settings_m->get_by_many(
            array(
                'SettingCode' => APConstants::ENVELOPE_TYPE_CODE,
                'ActualValue' => $actualValue,
                'LabelValue' => $labelValue
            ));

        if ($setting) {
            
            $data_response['status'] = true;
            $data_response['data'] = $setting;
            
        } else {

            $data_response['status'] = false;
            $data_response['data'] = "";
        }
        return $data_response;
    }


}