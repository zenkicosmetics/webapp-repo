<?php

defined('BASEPATH') or exit('No direct script access allowed');

class EnvelopeUtils {

/**
 * Common function to process trash (delete) envelopes by customer.
 * @param type $list_envelope_ids
 * @param type $customer
 * @return boolean
 */
    public static function trashEnvelopes($list_envelope_ids, $customer) {
        if (count($list_envelope_ids) == 0) {
            return true;
        }

        // load lib.
        ci()->load->library('scans/scans_api');
        ci()->load->model(array(
            'scans/envelope_m',
            'mailbox/postbox_setting_m',
            'mailbox/postbox_m',
            'email/email_m',
            'scans/envelope_shipping_request_m',
            'mailbox/envelope_customs_m'
        ));
        
        ci()->lang->load(array(
            'mailbox/delete_permission',
            'mailbox/activity_permission'
        ));

        $to_email = $customer->email;
        $data = array(
            "full_name" => $customer->user_name,
            "site_url" => APContext::getFullBalancerPath()
        );

        foreach ($list_envelope_ids as $envelope_id) {
            $envelope = ci()->envelope_m->get($envelope_id);

            if (empty($envelope)) {
                continue;
            }
            
            $customer_id = $envelope->to_customer_id;
            $envelope_code = $envelope->envelope_code;
            
            $data = array_merge($data, array('item_id' => $envelope_id, 'item_code' => $envelope_code));

            $postbox_setting = ci()->postbox_setting_m->get_by_many(array(
                "postbox_id" => $envelope->postbox_id,
                "customer_id" => $customer_id
            ));
            
            // trigger storage nubmer report.
            scans_api::updateStorageStatus($envelope->id, $customer_id, $envelope->postbox_id, APUtils::getCurrentYear(), APUtils::getCurrentMonth(), $envelope->location_id, APConstants::OFF_FLAG);

            // If this item is welcome letter
            if (APUtils::endsWith($envelope_code, '_000')) {
                // Call method to delete
                ci()->envelope_m->delete_by_many(array(
                    'id' => $envelope_id,
                    'to_customer_id' => $customer_id
                ));
                continue;
            }
            
            // check declare custom. If this item need declare custom, igrone it.
            $envelope_customs = ci()->envelope_customs_m->get_by_many( array(
                "customer_id" => $customer_id,
                "envelope_id" => $envelope_id,
                "process_flag" => APConstants::OFF_FLAG,
            ));
            
            if($envelope_customs){
                $check_custom_flag = EnvelopeUtils::check_customs_flag($customer_id, $envelope->postbox_id, $envelope_id);
                if($check_custom_flag){
                    continue;
                }
            }

            //Check send email when delete item
            $sendFlag = false;
            if ($postbox_setting && $postbox_setting->inform_email_when_item_trashed == 1) {
                $sendFlag = true;
            }

            // Insert trash activity to completed list
            // Update to remove complete activity from customer
            $delete_key_sign = APUtils::build_delete_sign($envelope->envelope_scan_flag, $envelope->item_scan_flag, $envelope->direct_shipping_flag, $envelope->collect_shipping_flag, $envelope->package_id);
            $delete_flag = lang("delete_" . $delete_key_sign);

            // Check item in trash folder
            $check_trash_folder = EnvelopeUtils::checkItemInTrashFolder($envelope);

            // cancel all prepare payment activities
            EnvelopeUtils::cancel_prepare_shippment_by($envelope_id); 
                            
            if ($check_trash_folder) {
                // do in trash folder
                // cancel scan activity
                if ($envelope->item_scan_flag == APConstants::OFF_FLAG || $envelope->envelope_scan_flag == APConstants::OFF_FLAG) {
                    
                    //Cancel item scan request
                    ci()->envelope_m->update_by_many(array(
                        "id" => $envelope_id,
                        "to_customer_id" => $customer_id,
                        "item_scan_flag" => APConstants::OFF_FLAG
                    ), array(
                        "item_scan_flag" => null,
                        'last_updated_date' => now(),
                    ));

                    //Cancel envelope scan request
                    ci()->envelope_m->update_by_many(array(
                        "id" => $envelope_id,
                        "to_customer_id" => $customer_id,
                        "envelope_scan_flag" => APConstants::OFF_FLAG
                    ), array(
                        "envelope_scan_flag" => null,
                        'last_updated_date' => now(),
                    ));
                    
                    //Log cancel scan activity by system
                    if ($envelope->item_scan_flag == APConstants::OFF_FLAG && $envelope->envelope_scan_flag == APConstants::OFF_FLAG) {
                        scans_api::insertCompleteItem($envelope_id, APConstants::CANCEL_ITEM_SCAN_BY_SYSTEM_ACTIVITY_TYPE);
                        scans_api::insertCompleteItem($envelope_id, APConstants::CANCEL_ENVELOPE_SCAN_BY_SYSTEM_ACTIVITY_TYPE);
                    } elseif ($envelope->item_scan_flag == APConstants::OFF_FLAG) {
                        scans_api::insertCompleteItem($envelope_id, APConstants::CANCEL_ITEM_SCAN_BY_SYSTEM_ACTIVITY_TYPE);
                    } elseif ($envelope->envelope_scan_flag == APConstants::OFF_FLAG) {
                        scans_api::insertCompleteItem($envelope_id, APConstants::CANCEL_ENVELOPE_SCAN_BY_SYSTEM_ACTIVITY_TYPE);
                    }

                }

                // update request trash flag.
                if($envelope->trash_flag == APConstants::ENVELOPE_COMPLETED_TRASH_BY_ADMIN){
                    // add trash activity
                    ci()->envelope_m->update_by_many(array(
                        'id' => $envelope_id
                    ), array(
                        "trash_flag" => APConstants::ON_FLAG,
                        "trash_date" => now(),
                        "completed_flag" => APConstants::ON_FLAG,
                        "completed_date" => now(),
                        'last_updated_date' => now(),
                        "new_notification_flag" => APConstants::OFF_FLAG
                    ));
                } else {
                    // add trash activity
                    ci()->envelope_m->update_by_many(array(
                        "id" => $envelope_id,
                        "to_customer_id" => $customer_id
                    ), array(
                        "trash_flag" => APConstants::TRASH_COMPLETED_ACTIVITY_TYPE,
                        "trash_date" => now(),
                        'last_updated_date' => now(),
                        "new_notification_flag" => APConstants::OFF_FLAG
                    ));
                }

                if ($sendFlag) {
                    EnvelopeUtils::sendEmailNotificationDeleteEnvelope($to_email, $data);
                }
            } else {
                /**
                 * check delete envelope case:
                 * case 1: delete direct and report to customer.
                 * case 2: add trash and report to customer.
                 * case 3: do nothing
                 * case 4: no item or scan available. Element can be deleted
                 * case 5: add trash and report to customer.
                 * case 0: impossible case: do nothing
                 */
                switch ($delete_flag) {
                    case "1":
                        // case 1: delete direct and report to customer. (BLUExGRAY or GRAYxBLUE or BLUExBLUE)x(BLUExGRAY or GRAYxBLUE). 
                        // Item is sent. Item complete 1 or all scan.
                        // S3 files.
                        
                        // Add trash activity
                        scans_api::insertCompleteItem($envelope_id, APConstants::TRASH_ORDER_BY_CUSTOMER_ACTIVITY_TYPE, APConstants::TRIGGER_BY_CUSTOMER, $customer_id);
                        
                        EnvelopeUtils::trash_envelope_complete_activity($envelope_id, $customer_id, true);
                        
                        // add trash activity
                        ci()->envelope_m->update_by_many(array(
                            'id' => $envelope_id
                        ), array(
                            "trash_flag" => APConstants::ON_FLAG,
                            "trash_date" => now(),
                            "completed_flag" => APConstants::ON_FLAG,
                            "completed_date" => now(),
                            'last_updated_date' => now()
                        ));

                        if ($sendFlag) {
                            EnvelopeUtils::sendEmailNotificationDeleteEnvelope($to_email, $data);
                        }
                        break;
                    case "2":
                        // case 2: add trash and report to customer. (x-x-GRAY-GRAY). x = GRAY, YELLOW, BLUE
                        // Item does not request sent yet.
                        // if item has scan request activity (x = YELLOW) : add "trash activity after scan"
                        if ($envelope->item_scan_flag == APConstants::OFF_FLAG || $envelope->envelope_scan_flag == APConstants::OFF_FLAG) {
                            scans_api::insertCompleteItem($envelope_id, APConstants::TRASH_AFTER_SCAN_ACTIVITY_TYPE);
                        }

                        if($envelope->trash_flag == APConstants::ENVELOPE_COMPLETED_TRASH_BY_ADMIN){
                            // add trash activity
                            ci()->envelope_m->update_by_many(array(
                                'id' => $envelope_id
                            ), array(
                                "trash_flag" => APConstants::ON_FLAG,
                                "trash_date" => now(),
                                "completed_flag" => APConstants::ON_FLAG,
                                "completed_date" => now(),
                                'last_updated_date' => now(),
                                "new_notification_flag" => APConstants::OFF_FLAG
                            ));
                        } else {
                            // add trash activity
                            ci()->envelope_m->update_by_many(array(
                                'id' => $envelope_id
                            ), array(
                                "trash_flag" => APConstants::OFF_FLAG,
                                "trash_date" => now(),
                                'last_updated_date' => now(),
                                "new_notification_flag" => APConstants::OFF_FLAG
                            ));
                            
                            // Add trash activity
                            scans_api::insertCompleteItem($envelope_id, APConstants::TRASH_ORDER_BY_CUSTOMER_ACTIVITY_TYPE, APConstants::TRIGGER_BY_CUSTOMER, $customer_id);                            

                        }
                        
                        ci()->envelope_m->update_by_many(array(
                            'id' => $envelope_id,
                            "direct_shipping_flag" => APConstants::OFF_FLAG
                        ), array(
                            "direct_shipping_flag" => null,
                            "direct_shipping_date" => null
                        ));

                        ci()->envelope_m->update_by_many(array(
                            'id' => $envelope_id,
                            "collect_shipping_flag" => APConstants::OFF_FLAG
                        ), array(
                            "collect_shipping_flag" => null,
                            "collect_shipping_date" => null
                        ));

                        if ($sendFlag) {
                            // report to customer.
                            $data = array(
                                "slug" => APConstants::email_is_notified_envelope_is_trashed,
                                "to_email" => $to_email,
                            );
                            // Send email
                            MailUtils::sendEmailByTemplate($data);

                            // Update trang thai send email
                            ci()->envelope_m->update_by("id", $envelope_id, array(
                                "email_notification_flag" => APConstants::ON_FLAG
                            ));
                        }
                        break;
                    case "3":
                        // case3: do nothing
                        break;
                    case "4":
                        // case 4: GRAY-GRAY-(BLUE-GRAY or GRAY-BLUE).  No scan request, item already has been sent
                        // Add trash activity
                        scans_api::insertCompleteItem($envelope_id, APConstants::TRASH_ORDER_BY_CUSTOMER_ACTIVITY_TYPE, APConstants::TRIGGER_BY_CUSTOMER, $customer_id);

                        // log history
                        LogUtils::log_delete_envelope_by_id($envelope_id, $customer_id);

                        // add trash activity
                        ci()->envelope_m->update_by_many(array(
                            'id' => $envelope_id
                        ), array(
                            "trash_flag" => APConstants::ON_FLAG,
                            "trash_date" => now(),
                            "completed_flag" => APConstants::ON_FLAG,
                            "completed_date" => now(),
                            'last_updated_date' => now()
                        ));

                        if ($sendFlag) {
                            EnvelopeUtils::sendEmailNotificationDeleteEnvelope($to_email, $data);
                        }
                        break;
                    case "5":
                        // case 5: GRAY-GRAY-GRAY-GRAY. add TRASH activity, delete elements, do not display on trash 30d
                        ci()->envelope_m->update_by_many(array(
                            'id' => $envelope_id
                        ), array(
                            "trash_flag" => APConstants::ENVELOPE_TRASH_BY_CUSTOMER_IN_TRASH_FOLDER,
                            "trash_date" => now(),
                            'last_updated_date' => now(),
                            "new_notification_flag" => APConstants::OFF_FLAG
                        ));
                        
                        ci()->envelope_m->update_by_many(array(
                            'id' => $envelope_id,
                            "direct_shipping_flag" => APConstants::OFF_FLAG
                        ), array(
                            "direct_shipping_flag" => null,
                            "direct_shipping_date" => null
                        ));

                        ci()->envelope_m->update_by_many(array(
                            'id' => $envelope_id,
                            "collect_shipping_flag" => APConstants::OFF_FLAG
                        ), array(
                            "collect_shipping_flag" => null,
                            "collect_shipping_date" => null
                        ));

                        // Add trash activity
                        scans_api::insertCompleteItem($envelope_id, APConstants::TRASH_ORDER_BY_CUSTOMER_ACTIVITY_TYPE, APConstants::TRIGGER_BY_CUSTOMER, $customer_id);

                        if ($sendFlag) {
                            EnvelopeUtils::sendEmailNotificationDeleteEnvelope($to_email, $data);
                        }
                        break;
                    default:
                        break;
                }
            }
        }
    }
    
    /**
     * Auto trigger trash all items if customer enable auto-trash function on setting postbox.
     * @param type $list_envelope_ids
     */
    public static function auto_trash($list_envelope_ids, $customer) {
        
        if (count($list_envelope_ids) == 0) {
            return true;
        }
        // load lib.
        ci()->load->library('scans/scans_api');
        ci()->load->model(array(
            'scans/envelope_m',
            'mailbox/postbox_setting_m',
            'mailbox/postbox_m',
            'email/email_m',
            'scans/envelope_shipping_request_m',
            'mailbox/envelope_customs_m'
        ));
        
        $to_email = $customer->email;
        $data = array(
            "full_name" => $customer->user_name,
            "site_url" => APContext::getFullBalancerPath()
        );
        
        foreach ($list_envelope_ids as $envelope_id) {
            
            $envelope = ci()->envelope_m->get($envelope_id);
            $customer_id = $envelope->to_customer_id;
            $envelope_code = $envelope->envelope_code;

            $postbox_setting = ci()->postbox_setting_m->get_by_many(array(
                "postbox_id" => $envelope->postbox_id,
                "customer_id" => $customer_id
            ));
            
            // trigger storage nubmer report.
            scans_api::updateStorageStatus($envelope->id, $customer_id, $envelope->postbox_id, APUtils::getCurrentYear(), APUtils::getCurrentMonth(), $envelope->location_id, APConstants::OFF_FLAG);
            
            $sendFlag = false;
            if ($postbox_setting && $postbox_setting->inform_email_when_item_trashed == 1) {
                $sendFlag = true;
            }
            
            ci()->envelope_m->update_by_many(array(
                'id' => $envelope_id
            ), array(
                "trash_flag" => APConstants::ENVELOPE_TRASH_BY_CUSTOMER_IN_TRASH_FOLDER,
                "trash_date" => now(),
                'last_updated_date' => now(),
                "new_notification_flag" => APConstants::OFF_FLAG
            ));
            
            if ($sendFlag) {
                EnvelopeUtils::sendEmailNotificationDeleteEnvelope($to_email, array_merge($data, array('item_id' => $envelope_id, 'item_code' => $envelope_code)));
            }
            log_audit_message('error', 'envelope_id: '.$envelope_id.' customer_id: '.$customer_id .' postbox_id: '.$envelope->postbox_id, false, 'auto_trash');
            //Add trash item by system
            scans_api::insertCompleteItem($envelope_id, APConstants::TRASH_ORDER_BY_SYSTEM_ACTIVITY_TYPE);
        }
        
    }

    public static function checkItemInTrashFolder($envelope) {
        $result = false;
        if ($envelope->trash_flag == APConstants::OFF_FLAG || $envelope->trash_flag == APConstants::ENVELOPE_COMPLETED_TRASH_BY_ADMIN) {
            $result = true;
        }

        return $result;
    }

    public static function getAddressOfEnvelope($customer_id, $package_id){

        ci()->load->model('scans/envelope_m');
        
        ci()->db->select('envelopes.id, envelopes.shipping_address_id, envelopes.shipping_address_date');
        
        ci()->db->from('envelopes');

        ci()->db->where("envelopes.to_customer_id", $customer_id);
        
        ci()->db->where("envelopes.package_id", $package_id);
        
        ci()->db->order_by('shipping_address_date', 'DESC');
        
        return ci()->db->get()->row();
    }


    public static function sendEmailNotificationDeleteEnvelope($to_email, $data) {
        
        if (!empty($to_email)){
            $email_data = array(
                    'to_email' => $to_email,
                    'slug' => APConstants::email_is_notified_envelope_is_direct_deleted,
                    'send_date' => now(),
                    'data' => json_encode($data)
            );

            //Add to email queue
            MailUtils::addEmailQueue($email_data);
        }
    }

    
    /**
     * Add trash activity.
     * @param type $envelope_id
     * @param type $deleted_by
     * @param type $delete_s3_file_flag
     */
    public static function trash_envelope_complete_activity($envelope_id, $deleted_by, $delete_s3_file_flag = false) {
        ci()->load->model('scans/envelope_m');
        ci()->load->model('scans/envelope_completed_m');
        ci()->load->library('scans/scans_api');

        // delete from S3 files
        if ($delete_s3_file_flag) {
            ci()->load->model('scans/envelope_file_m');
            $files = ci()->envelope_file_m->get_many_by_many(array(
                "envelope_id" => $envelope_id
            ));

            // Delete file content in amazone
            if ($files) {
                ci()->load->library('S3');
                $default_bucket_name = ci()->config->item('default_bucket');
                foreach ($files as $preview_file) {
                    $res = S3::deleteObject($default_bucket_name, $preview_file->amazon_relate_path);
                }
            }
        }

    }
    
   
    /**
     * Apply customs process for all items has been marked as collect shipment in this postbox
     * @param type $customer_id
     * @param type $postbox_id
     * @param type $package_id
     * @return type
     */
    public static function apply_collect_customs_process($customer_id, $postbox_id, $package_id) {
        ci()->load->model('scans/envelope_m');
        ci()->load->library("mailbox/mailbox_api");
        ci()->load->library('scans/scans_api');
        ci()->load->model('mailbox/envelope_customs_m');
        
        $declare_customs_flag = APConstants::OFF_FLAG;
        //Get all request collective envelope of this postbox
        //$list_package_envelopes = ci()->envelope_m->get_all_package_envelope($postbox_id);
        $listCollectiveItems = scans_api::getListCollectiveShippingItems($customer_id, $postbox_id);
        
        if (empty($listCollectiveItems)) {
            return $declare_customs_flag;
        }
        
        //Check if existe 1 item need declare custom in list collective items
        foreach ($listCollectiveItems as $item) {
            $envelope_id = $item->id;
            $check_flag = EnvelopeUtils::check_customs_flag($customer_id, $postbox_id, $envelope_id);
            if ($check_flag === APConstants::ON_FLAG) {
                $declare_customs_flag = APConstants::ON_FLAG;
                break;
            }
        }
        
        if($declare_customs_flag == APConstants::ON_FLAG){
            $list_envelopes = ci()->envelope_m->get_all_envelope_must_declare_customs($postbox_id);
            $check_customs_completed = EnvelopeUtils::check_envelope_customs($envelope_id, APConstants::ON_FLAG);
            if ($check_customs_completed) {
                return APConstants::OFF_FLAG;
            }
            foreach ($list_envelopes as $item) {
                $envelope_id = $item->id;
                // Register customs
                mailbox_api::regist_envelope_customs($customer_id, $envelope_id, $postbox_id, APConstants::COLLECT_FORWARDING, $package_id);
            }
        }
        
        return $declare_customs_flag;
    }

    /**
     * Check declare customs for envelope. Return 1 if this envelope need declare and does not declare yet.
     */
    public static function check_customs_flag($customer_id, $postbox_id, $envelope_id)
    {
        ci()->load->model('mailbox/postbox_m');
        ci()->load->model('addresses/location_m');
        ci()->load->model('addresses/customers_address_m');
        ci()->load->model('mailbox/customs_matrix_m');
        ci()->load->model('settings/countries_m');
        ci()->load->model('scans/envelope_m');
        ci()->load->model('addresses/customers_forward_address_m');
        ci()->load->model('settings/settings_m');
        
        ci()->load->library('scans/scans_api');
        
        if ( empty($customer_id) || empty($postbox_id) || empty($envelope_id) ) {
            return APConstants::OFF_FLAG;
        }

        // If this customer in EU
        // Get customer address information
        $customers_address = ci()->customers_address_m->get_by_many(array(
            'customer_id' => $customer_id
        ));
        if (empty($customers_address)) {
            return APConstants::OFF_FLAG;
        }
        
        // Get envelope
        $envelope = ci()->envelope_m->get_by_many(array('id' => $envelope_id));
        if (empty($envelope)) {
            return APConstants::OFF_FLAG;
        }
        
        // Check envelope type. If Alias01 = 1 => need declare custom
        $envelope_type_id = $envelope->envelope_type_id;
        $envelope_type = ci()->settings_m->get_by_many(array(
            'SettingCode' => APConstants::ENVELOPE_TYPE_CODE,
            'ActualValue' => $envelope_type_id
        ));
        if (!empty($envelope_type) && $envelope_type->Alias01 != '1') {
            return APConstants::OFF_FLAG;
        } 

        // Gets selection forwarding address.
        if($envelope->package_id > 0){
            $shipping_envelope = EnvelopeUtils::getAddressOfEnvelope($customer_id,$envelope->package_id);
            $to_forward_address = scans_api::getSelectedForwardingAddressOfEnvelopes($customer_id, $shipping_envelope->shipping_address_id);
        } else {
            $to_forward_address = scans_api::getSelectedForwardingAddressOfEnvelopes($customer_id, $envelope->shipping_address_id);
        }
        $to_country_name_of_envelope = '';
        if(!empty($to_forward_address)){
            $to_country = ci()->countries_m->get_by_many(array(
                "id" => $to_forward_address->shipment_country
            ));
            $to_country_name_of_envelope = $to_country ? $to_country->country_name : '';
        }

        // Get postbox information
        $postbox = ci()->postbox_m->get_by_many(
            array(
                "postbox_id" => $postbox_id,
                "customer_id" => $customer_id
            ));
        if (empty($postbox)) {
            return APConstants::OFF_FLAG;
        }

        $location_available_id = $postbox->location_available_id;
        if (empty($location_available_id)) {
            return APConstants::OFF_FLAG;
        }

        // Get detail location information
        $location = ci()->location_m->get_by_many(array(
            "id" => $location_available_id
        ));
        if (empty($location)) {
            return APConstants::OFF_FLAG;
        }
        
        $from_country = ci()->countries_m->get_by_many(array(
            "id" => $location->country_id
        ));
        $from_country_name = $from_country->country_name;

        $to_country_name = '';
        if (empty($to_country_name_of_envelope)) {
            $shipment_country_id = $customers_address->shipment_country;
            $to_country = ci()->countries_m->get_by_many(array(
                "id" => $shipment_country_id
            ));
            $to_country_name = $to_country->country_name;
        } else {
            $to_country_name = $to_country_name_of_envelope;
        }

        $customs_matrix = ci()->customs_matrix_m->get_by_many(
            array(
                "from_country" => $from_country_name,
                "to_country" => $to_country_name
            ));

        // If this item is declae in customs matrix table and custom_flag = 1
        // (no customs declaration)  Return custom flag is 1
        if (!empty($customs_matrix) && $customs_matrix->custom_flag == '1') {
            $check_customs_completed = EnvelopeUtils::check_envelope_customs($envelope_id, APConstants::ON_FLAG);
            if (!$check_customs_completed) {
                return APConstants::ON_FLAG;
            }
        }

       
        return APConstants::OFF_FLAG;
    }

    public static function get_envelope_shipping_tracking($envelopeID){

        ci()->load->model("scans/envelope_shipping_tracking_m");
        $envelope_shipping_tracking = ci()->envelope_shipping_tracking_m->get_by('envelope_id', $envelopeID);

        return $envelope_shipping_tracking;

    }

    public static function get_shipping_info($envelopeID, $shipping_services_id){
        
        ci()->load->model("scans/envelope_m");
        $item = false;
        if(!empty($shipping_services_id)){
            $item = ci()->envelope_m->getInforItemTracking($envelopeID, $shipping_services_id);
        }
        return $item;
    }

    public static function get_phone_number($customer_id,$envelope){

        ci()->load->model('mailbox/envelope_customs_m');
        ci()->load->library("scans/scans_api");

        $pending_envelope_customs = ci()->envelope_customs_m->get_by_many( array(
            "customer_id" => $customer_id,
            "envelope_id" => $envelope->id
        ));
        
        $phone_number = '';
        if (!empty($pending_envelope_customs)) {

            $phone_number = $pending_envelope_customs->phone_number;
        }
        if (empty($phone_number)) {
            // Gets selection forwarding address.
            if($envelope->package_id > 0){
                $shipping_envelope = EnvelopeUtils::getAddressOfEnvelope($customer_id,$envelope->package_id);
                $to_forward_address = scans_api::getSelectedForwardingAddressOfEnvelopes($customer_id, $shipping_envelope->shipping_address_id);
            } else {
                $to_forward_address = scans_api::getSelectedForwardingAddressOfEnvelopes($customer_id, $envelope->shipping_address_id);
            }
            if(!empty($to_forward_address)){
                $phone_number = $to_forward_address->shipment_phone_number;
            }
        }

        return $phone_number;
    }
    
    public static function cancel_prepare_shippment_by($envelope_id){
        ci()->load->model("scans/envelope_m");
        
        // begin transaction
        ci()->envelope_m->db->trans_begin();
        
        ci()->envelope_m->update_by_many(array(
            'id' => $envelope_id,
            'direct_shipping_flag' => 2
        ), array(
            'direct_shipping_flag' => null
        ));
        
        ci()->envelope_m->update_by_many(array(
            'id' => $envelope_id,
            'collect_shipping_flag' => 2
        ), array(
            'collect_shipping_flag' => null
        ));
        
        ci()->envelope_m->update_by_many(array(
            'id' => $envelope_id,
            'item_scan_flag' => 2
        ), array(
            'item_scan_flag' => null
        ));
        
        ci()->envelope_m->update_by_many(array(
            'id' => $envelope_id,
            'envelope_scan_flag' => 2
        ), array(
            'envelope_scan_flag' => null
        ));
        
        // commit transaction
        if(ci()->envelope_m->db->trans_status() == FALSE){
            ci()->envelope_m->db->trans_rollback();
        }else{
            ci()->envelope_m->db->trans_commit();
        }
    }
    
    public static function getEnvelopeCustoms($envelope_id) {
        ci()->load->model("scans/envelope_m");
        ci()->load->model("mailbox/envelope_customs_m");
        
        $envelope = ci()->envelope_m->get($envelope_id);
        if(empty($envelope)){
            return null;
        }
        
        $package_id = $envelope->package_id;
        
        // Get envelope customs
        if(!empty($package_id) ){
            $envelope_customs = ci()->envelope_customs_m->get_envelope_customs_by_package_id($package_id, $envelope->to_customer_id, $envelope->postbox_id);
        }else{
            $envelope_custom = ci()->envelope_customs_m->get_by_many(array('customer_id' => $envelope->to_customer_id, 'postbox_id' => $envelope->postbox_id, 'envelope_id' => $envelope_id));
            if (!empty($envelope_custom->package_id)) {
                $envelope_customs = ci()->envelope_customs_m->get_envelope_customs_by_package_id($envelope_custom->package_id, $envelope->to_customer_id, $envelope->postbox_id);
            } else {
                $envelope_customs = ci()->envelope_customs_m->get_envelope_customs_by_envelope_id($envelope_id);
            }
        }
        
        return $envelope_customs;
    }
    
    public static function check_envelope_customs($envelope_id, $process_flag = '0'){
        ci()->load->model('mailbox/envelope_customs_m');
        $envelope_customs = ci()->envelope_customs_m->get_by_many( array(
            "envelope_id" => $envelope_id,
            "process_flag" => $process_flag
        ));
        
        if(!empty($envelope_customs)){
            return true;
        }
                  
        return false;
    }
    
    public static function get_customs_package_id($envelope_id){
        ci()->load->model('mailbox/envelope_customs_m');
        $envelope_customs = ci()->envelope_customs_m->get_by_many( array(
            "envelope_id" => $envelope_id
        ));
        
        if(!empty($envelope_customs) && !empty($envelope_customs->package_id)){
            return $envelope_customs->package_id;
        }
        return '';
    }
    
    /**
     * Get accounting email of postbox
     * @param type $postbox_id
     * @return type
     */
    public static function get_accounting_interface_by_postbox($postbox_id){
        //Get accounting email of this postbox
        ci()->load->model('cloud/customer_cloud_m');
        
        if(APContext::isEnterpriseCustomer()){
            $customer_id = APContext::getCustomerCodeLoggedInMailboxByPostbox($postbox_id);
        }

        if(empty($customer_id)){
            // Gets customerid logged in.
            $customer_id = APContext::getCustomerCodeLoggedIn();
        }
        $customer_setting = ci()->customer_cloud_m->get_by_many(array(
            'cloud_id' => APConstants::CLOUD_ACCOUNTING_EMAIL_CODE,
            'customer_id' => $customer_id
        ));
        $accounting_email = null;
        $interface_id = 1;
        if (!empty($customer_setting) && !empty($customer_setting->settings)) {
            $settings = json_decode($customer_setting->settings, true);
            $interface_id = count($settings) + 1;
            foreach ($settings as $setting){
                if ($setting['postbox_id'] == $postbox_id) {
                    $accounting_email = $setting['email'];
                    if (!empty($setting['interface_id'])) {
                        $interface_id = $setting['interface_id'];
                    }
                    break;
                }
            }
        }
        if (is_numeric($interface_id)) {
            $interface_id = 'ITF ' . str_pad((string) $interface_id, 3, '0', STR_PAD_LEFT);
        }
        return array('email' => $accounting_email, 'interface_id' =>  $interface_id );
    }
    
    /**
     * Get scan item PDF file of envelopes
     */
    public static function get_item_scan_of_envelope($envelope_ids){
        //get scan item PDF file of all envelopes
        $preview_files = ci()->envelope_file_m->get_many_by_many(
            array(
                "envelope_id IN (" . implode(',', $envelope_ids) .  ")" => null,
                "customer_id" => APContext::getCustomerCodeLoggedIn(),
                "type" => 2 //item scan type
            ));
        
        ci()->load->library('common/common_api');
        $attachments = array();
       
        foreach ($preview_files as $preview_file){
            // Get local file name
            $local_file_name = $preview_file->local_file_name;
            //If not found file in local server, download it from amazon
            if (!file_exists($local_file_name)) {
                $preview_file = common_api::setDynamicPathEnvelopeFile($preview_file);
                //Download data from amazon to local to ensure this file is really exists
                APUtils::download_amazon_file($preview_file);
                if (is_object($preview_file)){
                    $local_file_name = $preview_file->local_file_name;
                }
            }
            //Add all attach file to send mail
            $attachments[$preview_file->envelope_id] = $local_file_name;
        }
        
        return $attachments;
    }
    
   
    /**
     * Cancel all activity of item and add that items to trash. This activity always trigger by system
     * @param type $customer_id
     * @param type $postbox_id
     */
    public static function cancelActivityAndTrashItemBy($customer_id, $postbox_id){
        // Load model
        ci()->load->model('scans/envelope_m');
        ci()->load->model('scans/envelope_storage_month_m');
        ci()->load->library(array(
            'scans/completed_api',
            'scans/scans_api',
        ));
        
        // DELETE directly welcome envelope.
        ci()->envelope_m->delete_by_many( array(
            'postbox_id' => $postbox_id,
            'to_customer_id' => $customer_id,
            "RIGHT(envelope_code,4) = '_000' " => null
        ));
        
        //Get list envelope that activity request (request status = 0 or 2)
        $list_todo_envelopes = ci()->envelope_m->get_many_by_many( array(
            "postbox_id" => $postbox_id,
            "to_customer_id" => $customer_id,
            "completed_flag <> 1" => null,
            "deleted_flag" => 0,
            "(envelope_scan_flag = 0 OR item_scan_flag = 0 OR direct_shipping_flag = 0 OR collect_shipping_flag = 0 OR envelope_scan_flag = 2 OR item_scan_flag = 2 OR direct_shipping_flag = 2 OR collect_shipping_flag = 2)" => null
        ));
        
        //Add log activity
        foreach($list_todo_envelopes as $el){
            if($el->item_scan_flag === '0' || $el->item_scan_flag == 2){
                scans_api::insertCompleteItem($el->id, APConstants::CANCEL_ITEM_SCAN_BY_SYSTEM_ACTIVITY_TYPE);
            }
            if($el->envelope_scan_flag === '0' || $el->envelope_scan_flag == 2){
                scans_api::insertCompleteItem($el->id, APConstants::CANCEL_ENVELOPE_SCAN_BY_SYSTEM_ACTIVITY_TYPE);
            }
            if($el->direct_shipping_flag === '0' || $el->direct_shipping_flag == 2){
                scans_api::insertCompleteItem($el->id, APConstants::CANCEL_DIRECT_FORWARDING_BY_SYSTEM_ACTIVITY_TYPE);
            }
            if($el->collect_shipping_flag === '0' || $el->collect_shipping_flag == 2){
                scans_api::insertCompleteItem($el->id, APConstants::CANCEL_COLLECT_SHIPPING_BY_SYSTEM_ACTIVITY_TYPE);
            }
        }
        
        // cancel tracking number request.
        ci()->envelope_m->update_by_many( array(
            "postbox_id" => $postbox_id,
            "to_customer_id" => $customer_id,
            "tracking_number_flag" => APConstants::OFF_FLAG,
            "(direct_shipping_flag = 1 OR collect_shipping_flag = 1)" => null
        ),
        array(
            "tracking_number_flag" => APConstants::ON_FLAG,
        ));

        // cancel all todo activities
        ci()->envelope_m->update_by_many( array(
            "postbox_id" => $postbox_id,
            "to_customer_id" => $customer_id,
            "completed_flag <> 1" => null,
            "deleted_flag" => 0,
            "(envelope_scan_flag = 0 OR envelope_scan_flag = 2 OR item_scan_flag = 0 OR item_scan_flag = 2 OR direct_shipping_flag = 0 OR direct_shipping_flag = 2 OR collect_shipping_flag = 0 OR collect_shipping_flag = 2)" => null
        ),
        array(
            "direct_shipping_flag" => null,
            "collect_shipping_flag" => null,
            "envelope_scan_flag" => null,
            "item_scan_flag" => null
        ));
        
        // trash all items in this postbox.
        $all_envelopes = ci()->envelope_m->get_many_by_many( array(
            "postbox_id" => $postbox_id,
            "to_customer_id" => $customer_id,
            "completed_flag <> 1" => null,
            "deleted_flag" => 0
        ));
        
        //Add trash activity for item
        foreach($all_envelopes as $el){
            // Add trash activity
            scans_api::insertCompleteItem($el->id, APConstants::TRASH_ORDER_BY_SYSTEM_ACTIVITY_TYPE);
        }
        
        //Update trash request flag
        ci()->envelope_m->update_by_many( array(
            "postbox_id" => $postbox_id,
            "to_customer_id" => $customer_id,
            "completed_flag <> 1" => null,
            "deleted_flag" => 0
        ),
        array(
            "trash_flag" => APConstants::OFF_FLAG,
            "trash_date" => now(),
            'last_updated_date' => now()
        ));
        
        // cancel instorage all items.
        ci()->envelope_storage_month_m->update_by_many(array(
            "postbox_id" => $postbox_id,
            "customer_id" => $customer_id,
            "month" => APUtils::getCurrentMonth(),
            "year" => APUtils::getCurrentYear()
        ), array(
            "storage_flag" => APConstants::OFF_FLAG
        ));
    }
}
