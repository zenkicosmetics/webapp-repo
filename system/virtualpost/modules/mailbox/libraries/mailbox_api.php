<?php defined('BASEPATH') or exit('No direct script access allowed');

class mailbox_api extends base_api
{
    public static function getPostBoxByID($postboxID)
    {
        ci()->load->model('mailbox/postbox_m');

        $postbox = ci()->postbox_m->get($postboxID);

        return $postbox;
    }

    public static function getMainPostboxByCustomerID($customerID)
    {
        ci()->load->model('mailbox/postbox_m');

        $mainPostbox = ci()->postbox_m->get_by_many(
            array(
                "customer_id" => $customerID,
                "is_main_postbox" => 1
            )
        );

        return $mainPostbox;
    }

    public static function getEnvelopeCustoms($customer_id)
    {
        ci()->load->model('mailbox/envelope_customs_m');

        $pending_envelope_customs = ci()->envelope_customs_m->get_by_many(
            array(
                "customer_id" => $customer_id,
                "process_flag" => APConstants::OFF_FLAG
            ));

        return $pending_envelope_customs;
    }
    
    public static function getEnvelopeCustomsByEnvelopeId($customer_id, $envelope_id)
    {
        ci()->load->model('mailbox/envelope_customs_m');

        $pending_envelope_customs = ci()->envelope_customs_m->get_by_many(
            array(
                "envelope_id" => $envelope_id,
                "customer_id" => $customer_id
            ));

        return $pending_envelope_customs;
    }

    public static function getListPendingEnvelopeCustoms($customer_id)
    {
        ci()->load->model('mailbox/envelope_customs_m');

//        $list_pending_envelope_customs = ci()->envelope_customs_m->get_many_by_many(
//            array(
//                "customer_id" => $customer_id,
//                "process_flag" => APConstants::OFF_FLAG
//            ));

         $list_pending_envelope_customs = ci()->envelope_customs_m->get_list_pending_envelopes_custom_by_customer($customer_id);

        
        return $list_pending_envelope_customs;
    }
    
    public static function getListPendingEnvelopeCustomsBy($customer_list_id)
    {
        ci()->load->model('mailbox/envelope_customs_m');

//        $list_pending_envelope_customs = ci()->envelope_customs_m->get_many_by_many(
//            array(
//                "customer_id IN ('".implode("','", $customer_list_id)."')" => null,
//                "process_flag" => APConstants::OFF_FLAG
//            ));

        $list_pending_envelope_customs = ci()->envelope_customs_m->get_list_pending_envelopes_custom_by_customer($customer_list_id);
        
        return $list_pending_envelope_customs;
    }

    public static function createPostbox($dataNames, $dataValues)
    {
        ci()->load->model('mailbox/postbox_m');

        $dataUpdate = self::getArrayParams($dataNames, $dataValues);

        $postbox_id = ci()->postbox_m->insert($dataUpdate);

        return $postbox_id;
    }

    public static function getFirstLocationBy($customerID)
    {
        ci()->load->model('mailbox/postbox_m');

        $current_location = ci()->postbox_m->getFirstLocationBy($customerID);

        return $current_location;
    }

    public static function getLocationListBy($customerID)
    {
        ci()->load->model('mailbox/postbox_m');

        $location_list = ci()->postbox_m->getLocationListBy($customerID);

        return $location_list;

    }

    public static function getPostboxByCustomer($customer_id)
    {
        ci()->load->model('mailbox/postbox_m');

        $listPostbox = ci()->postbox_m->get_many_by_many(
            array(
                "customer_id" => $customer_id
            ));

        return $listPostbox;
    }

    public static function getPostboxWithFirstLocation($customerID, $first_location_flag)
    {
        ci()->load->model('mailbox/postbox_m');

        $postbox = ci()->postbox_m->get_by_many(array("customer_id" => $customerID, 'first_location_flag' => $first_location_flag));

        return $postbox;
    }

    /**
     * @param $postboxID
     * @param $postboxType
     * @return bool
     */
    public static function updatePostbox($postboxID, $postboxType)
    {
        ci()->load->model('mailbox/postbox_m');

        ci()->postbox_m->update_by_many(array(
            "postbox_id" => $postboxID
        ), array(
            "type" => $postboxType,
            "plan_deleted_date" => null,
            "updated_date" => now(),
            "deleted" => 0,
            "apply_date" => APUtils::getCurrentYearMonthDate(),
            "name_verification_flag" => APConstants::ON_FLAG,
            "company_verification_flag" => APConstants::ON_FLAG
        ));

        return true;
    }

    public static function updatePostboxForPostCode($customerID, $locationID, $postboxID)
    {
        ci()->load->model('mailbox/postbox_m');

        $postbox_id = ci()->postbox_m->update($postboxID,
            array(
                "updated_date" => now(),
                "customer_id" => $customerID,
                "location_available_id" => $locationID
            )
        );

        return $postbox_id;
    }

    public static function updateManyPostbox($conditionNames, $conditionValues, $dataNames, $dataValues)
    {
        ci()->load->model('mailbox/postbox_m');

        $conditions = self::getArrayParams($conditionNames, $conditionValues);
        $dataUpdate = self::getArrayParams($dataNames, $dataValues);
        $result = ci()->postbox_m->update_by_many($conditions, $dataUpdate);

        return $result;
    }

    public static function updatePostboxByID($postboxID, $data)
    {
        ci()->load->model('mailbox/postbox_m');

        $resultUpdatePostboxByID = ci()->postbox_m->update_by_many(array(
            'postbox_id' => $postboxID
        ), $data);

        return $resultUpdatePostboxByID;
    }

    public static function getPostboxTypesByLocationID($customerID, $locationID)
    {
        ci()->load->model('mailbox/postbox_m');

        $arrayWhere = array(
            'customer_id' => $customerID,
            'location_available_id' => $locationID
        );
        $postboxTypes = ci()->postbox_m->get_many_by_many($arrayWhere, 'type', true);

        return $postboxTypes;
    }

    public static function getPostboxSetting($customerID, $postboxID)
    {
        ci()->load->model('mailbox/postbox_setting_m');

        $postbox_setting = ci()->postbox_setting_m->get_by_many(
            array(
                "postbox_id" => $postboxID,
                "customer_id" => $customerID
            )
        );

        return $postbox_setting;
    }

    public static function createPostboxSetting($customer_id, $postbox_id)
    {
        ci()->load->model('mailbox/postbox_setting_m');

        $data = array(
            "customer_id" => $customer_id,
            "postbox_id" => $postbox_id,
            "email_notification" => 1,
            "invoicing_cycle" => 1,
            "weekday_shipping" => 2,
            "collect_mail_cycle" => 2
        );

        $postboxSetting = ci()->postbox_setting_m->insert($data);

        return $postboxSetting;
    }

    public static function getPostboxesByLocationID($customerID, $locationID)
    {
        ci()->load->model('mailbox/postbox_m');

        $listPostboxes = ci()->postbox_m->get_many_by_many(
            array(
                "location_available_id" => $locationID,
                "customer_id" => $customerID
            ));

        return $listPostboxes;
    }

    public static function getPostboxesWithoutPostCode()
    {
        ci()->load->model('mailbox/postbox_m');

        $postboxes = ci()->postbox_m->get_many_by_many(
            array(
                "postbox_code IS NULL" => null,
                "location_available_id IS NOT NULL" => null
            )
        );

        return $postboxes;
    }

    /**
     * Count all customers registration by month.
     * @param unknown $yearMonth
     */
    public static function countCustomersRegistrationByMonth($yearMonth, $locationId)
    {
        ci()->load->model('mailbox/postbox_m');
        ci()->load->model('customers/customer_m');
        
        if(!empty($locationId)){
            // Count number of account
            $numberOfAccount = 0;

            // count new customer
            $stmt = "SELECT COUNT(*) AS total_record
                    FROM (
                    SELECT c.customer_id as customer_id
                    FROM customers c
                    INNER JOIN postbox p ON p.customer_id=c.customer_id
                    WHERE p.location_available_id IN(".$locationId.") 
                    AND c.charge_fee_flag = 1 
                    AND FROM_UNIXTIME(p.created_date, '%Y%m') <= '".$yearMonth."' 
                    AND ((p.deleted = 0) OR (p.completed_delete_flag = 1 AND FROM_UNIXTIME(p.deleted_date, '%Y%m') > '".$yearMonth."'))
                    AND (c.status = 0 OR (c.status=1 AND from_unixtime(c.deleted_date, '%Y%m') >'".$yearMonth."'))
                    group by c.customer_id
                    ) TMP";
            $number_customer = ci()->postbox_m->db->query($stmt)->row();
            $number_of_customer = $number_customer->total_record;
            
            // new registration.
            $stmt = "SELECT COUNT(*) AS total_record
                    FROM (
                    SELECT DISTINCT p.postbox_id
                    FROM customers c
                    INNER JOIN postbox p ON p.customer_id=c.customer_id
                    WHERE p.location_available_id IN(".$locationId.") 
                    AND c.charge_fee_flag = 1 
                    AND FROM_UNIXTIME(p.created_date, '%Y%m') = '".$yearMonth."' 
                    ) TMP";
            $registration = ci()->postbox_m->db->query($stmt)->row();
            $new_registration = $registration->total_record;
            
            // never activated deleted.
            $stmt = "SELECT COUNT(*) AS total_record
                    FROM (
                    SELECT c.customer_id as customer_id
                    FROM customers c
                    INNER JOIN postbox p ON p.customer_id=c.customer_id
                    LEFT JOIN customer_product_settings cs1 ON c.customer_id=cs1.customer_id AND cs1.product_id=1 AND cs1.setting_key='invoicing_address_completed'
                    LEFT JOIN customer_product_settings cs2 ON c.customer_id=cs2.customer_id AND cs2.product_id=1 AND cs2.setting_key='postbox_name_flag'
                    LEFT JOIN customer_product_settings cs3 ON c.customer_id=cs3.customer_id AND cs3.product_id=1 AND cs3.setting_key='name_comp_address_flag'
                    LEFT JOIN customer_product_settings cs4 ON c.customer_id=cs4.customer_id AND cs4.product_id=1 AND cs4.setting_key='city_address_flag'
                    LEFT JOIN customer_product_settings cs5 ON c.customer_id=cs5.customer_id AND cs5.product_id=1 AND cs5.setting_key='email_confirm_flag'
                    WHERE p.location_available_id IN(".$locationId.") 
                    AND c.charge_fee_flag = 1 
                    AND from_unixtime(c.deleted_date, '%Y%m')='".$yearMonth."' 
                    AND c.activated_flag = 0
                    AND c.status = 1
                    AND (c.deleted_by = 0 OR c.deleted_by is null)
                    AND (c.deactivated_type = '' OR c.deactivated_type is null)
                    AND (cs1.setting_value = 0 OR cs1.setting_value is null OR cs2.setting_value is null OR cs2.setting_value = 0 OR cs3.setting_value IS NULL OR cs3.setting_value = 0 OR cs4.setting_value IS NULL OR cs4.setting_value = 0 OR cs5.setting_value IS NULL OR cs5.setting_value = 0)
                    group by c.customer_id
                    ) TMP";
            $number_never_activated= ci()->postbox_m->db->query($stmt)->row();
            $number_never_activated_deleted = $number_never_activated->total_record;

            // manually deleted
            $stmt = "SELECT COUNT(*) AS total_record
                    FROM (
                    SELECT c.customer_id as customer_id
                    FROM customers c
                    INNER JOIN postbox p ON p.customer_id=c.customer_id
                    WHERE p.location_available_id IN(".$locationId.") 
                    AND c.charge_fee_flag = 1 
                    AND FROM_UNIXTIME(c.deleted_date, '%Y%m') = '".$yearMonth."' 
                    AND c.deleted_by <> 0
                    AND c.deleted_by is not null
                    AND c.status = 1
                    group by c.customer_id
                    ) TMP";
            $number_manually= ci()->postbox_m->db->query($stmt)->row();
            $number_manually_deleted = $number_manually->total_record;

            // automatic deleted number
            $stmt = "SELECT COUNT(*) AS total_record
                    FROM (
                    SELECT c.customer_id as customer_id
                    FROM customers c
                    INNER JOIN postbox p ON p.customer_id=c.customer_id
                    WHERE p.location_available_id IN(".$locationId.") 
                    AND c.charge_fee_flag = 1 
                    AND FROM_UNIXTIME(c.deleted_date, '%Y%m') = '".$yearMonth."' 
                    group by c.customer_id
                    ) TMP";
            $number_automatic= ci()->postbox_m->db->query($stmt)->row();
            $number_automatic_deleted = $number_automatic->total_record - $number_manually_deleted - $number_never_activated_deleted;
        }else{
            // Count number of customers
            $numberOfAccount = ci()->customer_m->count_by_many(array(
                "from_unixtime(customers.created_date, '%Y%m') <= '" . $yearMonth . "'" => null,
                "(status =0 OR (status = 1 AND from_unixtime(deleted_date, '%Y%m') > '" . $yearMonth . "') )"=> null
            ));

            // count new customer
            //$number_of_customer = ci()->customer_m->count_by_many(array(
            //    "from_unixtime(customers.created_date, '%Y%m') <= '" . $yearMonth . "'" => null,
            //    "((status = 0) OR (status = 1 AND from_unixtime(deleted_date, '%Y%m') > '" . $yearMonth . "') )"=> null,
                // has at least one postbox.
            //    "(postbox_name_flag = 1 and name_comp_address_flag = 1)" => null
            //));
            $number_of_customer = ci()->customer_m->count_customer_by(array(
                "from_unixtime(customers.created_date, '%Y%m') <= '" . $yearMonth . "'" => null,
                "((status = 0) OR (status = 1 AND from_unixtime(customers.deleted_date, '%Y%m') > '" . $yearMonth . "') )"=> null,
                // has at least one postbox: (postbox_name_flag = 1 and name_comp_address_flag = 1)
                "(cs1.setting_value = 1 and cs2.setting_value = 1)" => null
            ));
            
            // new registration.
            $new_registration =  ci()->customer_m->count_by_many(array(
                "from_unixtime(customers.created_date, '%Y%m') = '" . $yearMonth . "'" => null,
                "((status = 0) OR (status = 1 AND from_unixtime(customers.deleted_date, '%Y%m') >= '" . $yearMonth . "') )"=> null
            ));

            // never activated deleted.
            //$number_never_activated_deleted = ci()->customer_m->count_by_many(array(
            //    "status"=> APConstants::ON_FLAG,
            //    "from_unixtime(deleted_date, '%Y%m')='".$yearMonth."'" => null,
            //    "activated_flag" => APConstants::OFF_FLAG,
            //    "(deactivated_type = '' OR deactivated_type is null)" => null,
            //    "(customers.invoicing_address_completed = 0 OR customers.postbox_name_flag = 0 OR customers.name_comp_address_flag=0 OR customers.city_address_flag = 0 OR customers.email_confirm_flag = 0)" => null,
            //));
            $number_never_activated_deleted = ci()->customer_m->count_customer_by(array(
                "status"=> APConstants::ON_FLAG,
                "from_unixtime(customers.deleted_date, '%Y%m')='".$yearMonth."'" => null,
                "activated_flag" => APConstants::OFF_FLAG,
                "(deactivated_type = '' OR deactivated_type is null)" => null,
                "(deleted_by = 0 OR deleted_by is null)" => null,
                //"(customers.invoicing_address_completed = 0 OR customers.postbox_name_flag = 0 OR customers.name_comp_address_flag=0 OR customers.city_address_flag = 0 OR customers.email_confirm_flag = 0)" => null,
                "(cs1.setting_value = 0 OR cs1.setting_value is null OR cs2.setting_value is null OR cs2.setting_value = 0 OR cs3.setting_value IS NULL OR cs3.setting_value = 0 OR cs4.setting_value IS NULL OR cs4.setting_value = 0 OR cs5.setting_value IS NULL OR cs5.setting_value = 0)" => null
            ));

            // manually deleted
            $number_manually_deleted = ci()->customer_m->count_by_many(array(
                "status"=> APConstants::ON_FLAG,
                "from_unixtime(customers.deleted_date, '%Y%m')='".$yearMonth."'" => null,
                "deleted_by <> 0" => null,
                "deleted_by is not null" => null
            ));

            // automatic deleted number
            $number_automatic_deleted = ci()->customer_m->count_by_many(array(
                "status"=> APConstants::ON_FLAG,
                "from_unixtime(customers.deleted_date, '%Y%m')='".$yearMonth."'" => null,
            ));
            
            $number_automatic_deleted = $number_automatic_deleted - $number_manually_deleted - $number_never_activated_deleted;
        }

        $result = array(
            "number_of_account" => $numberOfAccount,
            "number_of_customer" => $number_of_customer,
            "new_registration" => $new_registration,
            "number_manually_deleted" => $number_manually_deleted,
            "number_automatic_deleted" => $number_automatic_deleted,
            "number_never_activated_deleted" => $number_never_activated_deleted
        );
        
        return $result;
    }

    /**
     * count all postboxes registered by month.
     * @param unknown $yearMonth
     * @param unknown $locationId
     * @return unknown
     */
    public static function countPostboxesRegistrationByMonth($yearMonth, $locationId, $charge_flag = true)
    {
        ci()->load->model('mailbox/postbox_m');

        // Count number of customers
        $result = ci()->postbox_m->countPostboxesRegisteredByMonth($yearMonth, $locationId, $charge_flag);
        $data = array(
            "number_free" => 0,
            "number_private" => 0,
            "number_business" => 0,
            "total" => 0
        );

        foreach ($result as $r) {
            if ($r->type == APConstants::FREE_TYPE) {
                $data['number_free'] = $r->total;
            } else if ($r->type == APConstants::PRIVATE_TYPE) {
                $data['number_private'] = $r->total;
            } else if ($r->type == APConstants::BUSINESS_TYPE) {
                $data['number_business'] = $r->total;
            }
        }
        $data['total'] = $data['number_free'] + $data['number_private'] + $data['number_business'];

        return $data;
    }

    /**
     * Gets all postboxes by location
     */
    public static function getAllPostboxesByLocation($locationId)
    {
        ci()->load->model("mailbox/postbox_m");
        
        $postboxes = ci()->postbox_m->get_many_by_many(array(
            "location_available_id" => $locationId,
            "deleted <> 1" => null,
            "completed_delete_flag <> 1" => null,
        ));

        return $postboxes;
    }

    /**
     * count all postboxes of customers of another location.
     */
    public static function countPostboxOfCustomerAnotherLocation($locationId, $customer_id)
    {
        ci()->load->model("mailbox/postbox_m");

        $postboxes = ci()->postbox_m->get_many_by_many(array(
            "location_available_id NOT IN (" . $locationId . ")" => null,
            "customer_id" => $customer_id,
            "deleted <> 1" => null,
            "completed_delete_flag <> 1" => null,
        ));

        return $postboxes;
    }

    public static function countAllPostboxesOfCustomerBy($arrCondition)
    {
        ci()->load->model("mailbox/postbox_m");

        $postboxes = ci()->postbox_m->count_all_postboxes_of_customer_by($arrCondition);

        return $postboxes;
    }

    /**
     * Get the verified status of postbox
     */
    public static function getPostboxVerifiedStatus($postbox_id)
    {
        ci()->load->library('customers/customers_api');

        $postbox = self::getPostBoxByID($postbox_id);
        $customer = customers_api::getCustomerByID($postbox->customer_id);
        if ($customer->required_verification_flag == APConstants::OFF_FLAG) {
            $verifiedStatus = APConstants::VERIFIED_STATUS_NOT_REQUIRED;
        } else {
            if ($postbox->name_verification_flag == APConstants::ON_FLAG && $postbox->company_verification_flag == APConstants::ON_FLAG) {
                $verifiedStatus = APConstants::VERIFIED_STATUS_COMPLETED;
            } else {
                $verifiedStatus = APConstants::VERIFIED_STATUS_INCOMPLETE;
            }
        }

        return $verifiedStatus;
    }
    
    /**
     * 
     * @param type $customer_id
     */
    public static function countAllPendingPrepaymentItem($customer_id) {
        ci()->load->model('scans/envelope_m');
        $total_pending = ci()->envelope_m->count_by_many(array(
            "to_customer_id" => $customer_id,
            "(envelope_scan_flag = '2' || item_scan_flag = '2' || direct_shipping_flag = '2' || collect_shipping_flag = '2')" => null
        ));
        return $total_pending;
    }
    
    /**
     * 
     * @param type $customer_id
     */
    public static function countAllPendingPrepaymentEnvelopScanItem($customer_id) {
        ci()->load->model('scans/envelope_m');
        $total_pending = ci()->envelope_m->count_by_many(array(
            "to_customer_id" => $customer_id,
            "(envelope_scan_flag = '2')" => null
        ));
        return $total_pending;
    }
    
    /**
     * 
     * @param type $customer_id
     */
    public static function countAllPendingPrepaymentItemScanItem($customer_id) {
        ci()->load->model('scans/envelope_m');
        $total_pending = ci()->envelope_m->count_by_many(array(
            "to_customer_id" => $customer_id,
            "(item_scan_flag = '2')" => null
        ));
        return $total_pending;
    }
    
    /**
     * 
     * @param type $customer_id
     */
    public static function getAllPendingPrepaymentItem($customer_id) {
        ci()->load->model('scans/envelope_m');
        $list_pending = ci()->envelope_m->get_many_by_many(array(
            "to_customer_id" => $customer_id,
            "(envelope_scan_flag = '2' || item_scan_flag = '2' || direct_shipping_flag = '2' || collect_shipping_flag = '2')" => null
        ));
        return $list_pending;
    }
    
    /**
     * Completed prepayment request
     * 1. When load customer main mailbox screen => Update correct status for item
     * 2. When admin create manual charge
     * 3. When system charge vie payone automatically
     * @param unknown_type $customer_id
     */
    public static function completeManualPrepaymentRequest($customer_id) {
        ci()->load->model('scans/envelope_m');

        //Get all pending envelope scan request
        $list_pending_envelope_scan = ci()->envelope_m->get_many_by_many(array(
            "to_customer_id" => $customer_id,
            "envelope_scan_flag" => '2'
        ));

        //Process for envelope scan request
        if (count($list_pending_envelope_scan) > 0) {

            //Loop item list
            foreach ($list_pending_envelope_scan as $item) {
                
                // Check prepayment with envelope's scan type
                $check_prepayment_data = CustomerUtils::checkApplyScanPrepayment(APConstants::TRIGGER_ACTION_TYPE_CUSTOMER, APConstants::ENVELOPE_SCAN_TYPE, array($item->id), $customer_id, false, true);
                
                // if prepayment is false
                if ($check_prepayment_data['prepayment'] === false) {
                   // Envelope scan request is successful
                   // Update envelope_scan_flag = 0 (yellow) 
                   mailbox_api::requestEnvelopeScan($item->id, $customer_id);
                    
                }// end if inner
                
            }// end foreach 
            
        }// end if outer

        //Get all pending item scan request
        $list_pending_item_scan = ci()->envelope_m->get_many_by_many(array(
            "to_customer_id" => $customer_id,
            "item_scan_flag" => '2'
        ));
        
        //Process for item scan request
        if (count($list_pending_item_scan) > 0) {
            
            //Loop item list
            foreach ($list_pending_item_scan as $item) {
                
                // Check prepayment with item's scan type
                $check_prepayment_data = CustomerUtils::checkApplyScanPrepayment(APConstants::TRIGGER_ACTION_TYPE_CUSTOMER, 
                APConstants::ITEM_SCAN_TYPE, array($item->id), $customer_id, false, true);
                
                if ($check_prepayment_data['prepayment'] === false) {
                    // item scan request is successfull
                    // Update item_scan_flag = 0 (yellow) 
                    mailbox_api::requestItemScan($item->id, $customer_id);
                    
                }// end if inner
                
            }// end foreach
            
        }// end if outner
        
        //Get all pending direct forwarding request
        $list_pending_item_direct = ci()->envelope_m->get_many_by_many(array(
            "to_customer_id" => $customer_id,
            "direct_shipping_flag" => '2'
        ));
        
        // Check direct 
        if (count($list_pending_item_direct) > 0) {
            foreach ($list_pending_item_direct as $item) {
                
                // Check prepayment with direct shipping
                $check_prepayment_data = CustomerUtils::checkApplyShippingPrepayment(APConstants::TRIGGER_ACTION_TYPE_CUSTOMER, 
                                                                                    APConstants::SHIPPING_SERVICE_NORMAL, 
                                                                                    APConstants::SHIPPING_TYPE_DIRECT, 
                                                                                    array($item->id), $customer_id, false, true);
                
                // If prepayment is false
                if ($check_prepayment_data['prepayment'] === false) {
                    
                    // Add request
                    $check_flag = EnvelopeUtils::check_customs_flag($customer_id, $item->postbox_id, $item->id);
                    
                    // Check flag
                    if($check_flag){
                        mailbox_api::regist_envelope_customs($customer_id, $item->id, $item->postbox_id, APConstants::DIRECT_FORWARDING);
                    }
                    
                    // Request direct shipping is successfull 
                    // Update direct_shipping_flag = 0 (yellow)
                    // And insert activity:REQUEST_TRACKING_NUMBER = '29'
                    // Save address forwarding
                    mailbox_api::requestDirectShipping($item->id, $customer_id);
                    scans_api::insertCompleteItem($item->id, APConstants::REQUEST_TRACKING_NUMBER_ACTIVITY_TYPE);
                    
                }// end if inner 
                
            }// end foreach 
            
        }// end if outner
        
        //Get all pending collect forwarding request
        $list_pending_item_collect = ci()->envelope_m->get_many_by_many(array(
            "to_customer_id" => $customer_id,
            "collect_shipping_flag" => '2'
        ));
        
        // Check collect
        if (count($list_pending_item_collect) > 0) {
            
            // Check prepayment with collect shipping 
            $check_prepayment_data = CustomerUtils::checkApplyShippingPrepayment(APConstants::TRIGGER_ACTION_TYPE_CUSTOMER, 
                                                                                APConstants::SHIPPING_SERVICE_NORMAL, 
                                                                                APConstants::SHIPPING_TYPE_COLLECT, 
                                                                                array($list_pending_item_collect[0]->id), $customer_id, false, true);
            
            // If prepayment is false
            if ($check_prepayment_data['prepayment'] === false) {
                // update request shipping.
                ci()->envelope_m->update_by_many(array(
                    "to_customer_id" => $customer_id,
                    "collect_shipping_flag" => '2'
                ), array(
                    "collect_shipping_flag" => '0'
                ));

                $location_available_id = $list_pending_item_collect[0]->location_id;
                $postbox_id = $list_pending_item_collect[0]->postbox_id;
                $package_id = scans_api::createCollectiveShippingPackage($customer_id, $location_available_id);
                $declare_customs_obj = scans_api::updatePackageIDForAllCollectiveShippingItems($customer_id, $location_available_id, $package_id, $postbox_id);

                // Add request
                if($declare_customs_obj['declare_customs_flag'] == false){
                    foreach ($list_pending_item_collect as $item) {
                        if ($declare_customs_obj['declare_customs_flag'] != APConstants::ON_FLAG) {
                            scans_api::insertCompleteItem($item->id, APConstants::REQUEST_TRACKING_NUMBER_ACTIVITY_TYPE);
                        }
                        mailbox_api::requestCollectShippingAfterPrepayment($item->id, $customer_id); 
                    }
                }
            }
        }
    }
    
    /**
     * Completed prepayment request
     * @param unknown_type $customer_id
     */
    public static function completeManualPrepaymentRequestWithoutCheck($customer_id) {
        ci()->load->model('scans/envelope_m');
    
        // Get all pending envelope scan
        $list_pending_envelope_scan = ci()->envelope_m->get_many_by_many(array(
            "to_customer_id" => $customer_id,
            "envelope_scan_flag" => '2'
        ));
        if (count($list_pending_envelope_scan) > 0) {
            foreach ($list_pending_envelope_scan as $item) {
                // Request envelope scan is successful
                // Update envelope_scan_flag = 0 (yellow) 
                mailbox_api::requestEnvelopeScan($item->id, $customer_id);
            }
        }
        
        // Get all pending envelope scan
        $list_pending_item_scan = ci()->envelope_m->get_many_by_many(array(
            "to_customer_id" => $customer_id,
            "item_scan_flag" => '2'
        ));
        if (count($list_pending_item_scan) > 0) {
            foreach ($list_pending_item_scan as $item) {
                // Request item scan is successful
                // Update item_scan_flag = 0 (yellow) 
                mailbox_api::requestItemScan($item->id, $customer_id);
            }
        }
        
        // Get all pending envelope scan
        $list_pending_item_direct = ci()->envelope_m->get_many_by_many(array(
            "to_customer_id" => $customer_id,
            "direct_shipping_flag" => '2'
        ));
        if (count($list_pending_item_direct) > 0) {
            foreach ($list_pending_item_direct as $item) {
                $check_flag = EnvelopeUtils::check_customs_flag($customer_id, $item->postbox_id, $item->id);
                if($check_flag){
                    mailbox_api::regist_envelope_customs($customer_id, $item->id, $item->postbox_id, APConstants::DIRECT_FORWARDING);
                }
                // Request direct shipping is successfull 
                // Update direct_shipping_flag = 0 (yellow)
                // And insert activity:REQUEST_TRACKING_NUMBER = '29'
                // Save address forwarding
                mailbox_api::requestDirectShipping($item->id, $customer_id);
            }
        }
        
        // Get all pending envelope scan
        $list_pending_item_collect = ci()->envelope_m->get_many_by_many(array(
            "to_customer_id" => $customer_id,
            "collect_shipping_flag" => '2'
        ));
        if (count($list_pending_item_collect) > 0) {    
            // Add request
            foreach ($list_pending_item_collect as $item) {
                $check_flag = EnvelopeUtils::check_customs_flag($customer_id, $item->postbox_id, $item->id);
                if($check_flag){
                    mailbox_api::regist_envelope_customs($customer_id, $item->id, $item->postbox_id, APConstants::COLLECT_FORWARDING);
                }
                mailbox_api::requestCollectShippingAfterPrepayment($item->id, $customer_id);
            }
        }
    }
    
    
    /** 
     * Request item scan. Status : NULL or 2 => 0
     * @param type $list_envelope_id_str
     * @param type $customer_id
     * @param type $api_mobile
     * @return type
     */
    public static function requestItemScan($list_envelope_id_str, $customer_id, $api_mobile = 0) {
        ci()->load->model('scans/envelope_m');
        ci()->load->library('scans/scans_api');
        
        $ids = APUtils::convertIdsInputToArray($list_envelope_id_str);
        
        if (count($ids) == 0) {
            return;
        }
        
        foreach ($ids as $id) {
            ci()->envelope_m->update_by_many(
                array(
                    'id' => $id,
                    'to_customer_id' => $customer_id,
                    "(item_scan_flag IS NULL OR item_scan_flag = '2')" => null
                ),
                array(
                    'item_scan_flag' => APConstants::OFF_FLAG,
                    'last_updated_date' => now(),
                    'item_scan_date' => now()
                ));
            // Update trang thai send email
            ci()->envelope_m->update_by_many(
                array(
                    "to_customer_id" => $customer_id,
                    "id" => $id
                ), array(
                "new_notification_flag" => APConstants::OFF_FLAG
            ));
        }
    }
    

    /**
     * Request item scan to queue. Status : NULL => 2
     * @param type $list_envelope_id_str
     * @param type $customer_id
     * @return type
     */
    public static function requestItemScanToQueue($list_envelope_id_str, $customer_id) {
        ci()->load->model('scans/envelope_m');
        ci()->load->library('scans/scans_api');
        
        $ids = APUtils::convertIdsInputToArray($list_envelope_id_str);
        
        if (count($ids) == 0) {
            return;
        }
        
        foreach ($ids as $id) {
            $envelope = ci()->envelope_m->get_by_many(
            array(
                "to_customer_id" => $customer_id,
                "id" => $id
            ));
            if (empty($envelope) || $envelope->item_scan_flag == '2') {
            	continue;
            }
            $postbox_id = $envelope->postbox_id;
            $envelope_scan_cost = EnvelopeItemScan::getCostForPreItemScan($customer_id, $postbox_id, $id);
            mailbox_api::updatePrepaymentCost($customer_id, $postbox_id, $id, $envelope_scan_cost, 'item');
            
            ci()->envelope_m->update_by_many(
                array(
                    'id' => $id,
                    'to_customer_id' => $customer_id,
                    "(item_scan_flag IS NULL)" => null
                ),
                array(
                    'item_scan_flag' => '2',
                    'last_updated_date' => now()
                ));
            // Update trang thai send email
            ci()->envelope_m->update_by_many(
                array(
                    "to_customer_id" => $customer_id,
                    "id" => $id
                ), array(
                "new_notification_flag" => APConstants::OFF_FLAG
            ));
        }
    }
    

    /**
     * Request item scan. Status : NULL or 2 => 0
     * @param type $list_envelope_id_str
     * @param type $customer_id
     * @param type $api_mobile
     * @return type
     */
    public static function requestEnvelopeScan($list_envelope_id_str, $customer_id, $api_mobile = 0) {
        ci()->load->model('scans/envelope_m');
        ci()->load->library('scans/scans_api');
        
        $ids = APUtils::convertIdsInputToArray($list_envelope_id_str);
        
        if (count($ids) == 0) {
            return;
        }
        
        foreach ($ids as $id) {
            ci()->envelope_m->update_by_many(
                array(
                    'id' => $id,
                    'to_customer_id' => $customer_id,
                    "(envelope_scan_flag IS NULL OR envelope_scan_flag = '2')" => null
                ),
                array(
                    'envelope_scan_flag' => APConstants::OFF_FLAG,
                    'last_updated_date' => now()
            ));

              
            // Update trang thai send email
            ci()->envelope_m->update_by_many(
                array(
                    "to_customer_id" => $customer_id,
                    "id" => $id
                ), array(
                "new_notification_flag" => APConstants::OFF_FLAG
            ));
        }
    }
    
	
    /**
     * Add envelope scan items to queue. Status NULL => 2
     * @param type $list_envelope_id_str
     * @param type $customer_id
     * @return type
     */
    public static function requestEnvelopeScanToQueue($list_envelope_id_str, $customer_id) {
        ci()->load->model('scans/envelope_m');
        
        
        ci()->load->library('scans/scans_api');
        
        $ids = APUtils::convertIdsInputToArray($list_envelope_id_str);
        
        if (count($ids) == 0) {
            return;
        }
        
        foreach ($ids as $id) {
            
            $envelope = ci()->envelope_m->get_by_many(
                array(
                    "to_customer_id" => $customer_id,
                    "id" => $id
                ));
            
            if (empty($envelope) || $envelope->envelope_scan_flag == '2') {
            	continue;
            }
            
            $postbox_id = $envelope->postbox_id;
            $envelope_scan_cost = EnvelopeItemScan::getCostForPreEnvelopeScan($customer_id, $postbox_id, $id);
            mailbox_api::updatePrepaymentCost($customer_id, $postbox_id, $id, $envelope_scan_cost, 'envelope');
            
            ci()->envelope_m->update_by_many(
                array(
                    'id' => $id,
                    'to_customer_id' => $customer_id,
                    "(envelope_scan_flag IS NULL)" => null
                ),
                array(
                    'envelope_scan_flag' => '2',
                    'last_updated_date' => now()
            ));

            // Update trang thai send email
            ci()->envelope_m->update_by_many(
                array(
                    "to_customer_id" => $customer_id,
                    "id" => $id
                ), array(
                "new_notification_flag" => APConstants::OFF_FLAG
            ));
        }
    }
    
    /**
     * Update prepayment cost.
     * 
     * @param type $customer_id
     * @param type $postbox_id
     * @param type $envelope_id
     * @param type $cost
     * @param type $type (envelope|item)
     */
    public static function updatePrepaymentCost($customer_id, $postbox_id, $envelope_id, $cost, $type) {
        ci()->load->model('scans/envelope_prepayment_cost_m');
        $key_array = array(
                "envelope_id" => $envelope_id,
                "postbox_id" => $postbox_id,
                "customer_id" => $customer_id
            );
        $check_exist = ci()->envelope_prepayment_cost_m->get_by_many($key_array);
        if (empty($check_exist)) {
            $data_array = $key_array;
            $data_array['created_date'] = now();
            if ($type == 'envelope') {
                $data_array['envelope_scan_cost'] = $cost;
            } else if ($type == 'item') {
                $data_array['item_scan_cost'] = $cost;
            }
            ci()->envelope_prepayment_cost_m->insert($data_array);
        } else {
            $data_array = array();
            $data_array['updated_date'] = now();
            if ($type == 'envelope') {
                $data_array['envelope_scan_cost'] = $cost;
            } else if ($type == 'item') {
                $data_array['item_scan_cost'] = $cost;
            }
            ci()->envelope_prepayment_cost_m->update_by_many($key_array, $data_array);
        }
    }
    
    
    /**
     * Request direct shipping for new item or item in queue. Status : Null or 2 => 0
     * @param type $list_envelope_id_str
     * @param type $customer_id
     * @param type $api_mobile
     * @return type
     */
    public static function requestDirectShipping($list_envelope_id_str, $customer_id, $api_mobile = 0) {
        ci()->load->model('scans/envelope_m');
        ci()->load->library('scans/scans_api');
        ci()->load->library('shipping/shipping_api');
        
        $ids = APUtils::convertIdsInputToArray($list_envelope_id_str);        

        if (count($ids) == 0) {
            return;
        }
        
        foreach ($ids as $id) {
            //Get envelope info
            $envelope = ci()->envelope_m->get_by_many(
                array(
                    'id' => $id,
                    'to_customer_id' => $customer_id
                ));
            
            if (empty($envelope) || (($envelope->collect_shipping_flag === APConstants::ON_FLAG || $envelope->trash_flag != '' ))) {
               continue;
            }
            
            $postbox_id = $envelope->postbox_id;
            
            // Check apply custom process
            $check_flag = EnvelopeUtils::check_customs_flag($customer_id, $postbox_id , $id);
            $check_customs_completed = EnvelopeUtils::check_envelope_customs($id, APConstants::ON_FLAG);
            if ($check_flag === APConstants::ON_FLAG && !$check_customs_completed) {
                self::regist_envelope_customs($customer_id, $id, $postbox_id, APConstants::DIRECT_FORWARDING);
                continue;
            }
            
            ci()->envelope_m->update_by_many(
                array(
                    'id' => $id,
                    'to_customer_id' => $customer_id,
                    "(direct_shipping_flag IS NULL OR direct_shipping_flag = '2')" => null
                ),
                array(
                    'direct_shipping_flag' => APConstants::OFF_FLAG,
                    'collect_shipping_flag' => null,
                    'package_id' => null,
                    'last_updated_date' => now(),
                    'direct_shipping_date' => now()
                )
            );

            // Update trang thai send email
            ci()->envelope_m->update_by_many(
                array(
                    "to_customer_id" => $customer_id,
                    "id" => $id
                ), array(
                "new_notification_flag" => APConstants::OFF_FLAG
            ));

            // save address shipping
            shipping_api::saveShippingAddress($envelope->id, $envelope->shipping_address_id);
        }
    }
    
	/**
     * Add direct shipping new item to queue. Status NULL => 2 
     * @param type $list_envelope_id_str
     * @param type $customer_id
     * @return type
     */
    public static function requestDirectShippingToQueue($list_envelope_id_str, $customer_id) {
        ci()->load->model('scans/envelope_m');
        ci()->load->library('scans/scans_api');
        
        $ids = APUtils::convertIdsInputToArray($list_envelope_id_str);
        
        if (count($ids) == 0) {
            return;
        }
        
        foreach ($ids as $id) {
            //Get envelope info
            $envelope = ci()->envelope_m->get_by_many(
                array(
                    'id' => $id,
                    'to_customer_id' => $customer_id
                ));
            
            if ($envelope && $envelope->collect_shipping_flag === APConstants::ON_FLAG) {
               continue;
            }
            //Add new item to queue
            ci()->envelope_m->update_by_many(
                array(
                    'id' => $id,
                    'to_customer_id' => $customer_id,
                    "(direct_shipping_flag IS NULL)" => null
                ),
                array(
                    'direct_shipping_flag' => '2',
                    'collect_shipping_flag' => null,
                    'package_id' => null,
                    'last_updated_date' => now()
                )
            );

            // Update trang thai send email
            ci()->envelope_m->update_by_many(
                array(
                    "to_customer_id" => $customer_id,
                    "id" => $id
                ), array(
                "new_notification_flag" => APConstants::OFF_FLAG
            ));
            
            // save address shipping
            shipping_api::saveShippingAddress($envelope->id, $envelope->shipping_address_id);
        }
    }
    
    /**
     * Request collect shipping for new item. Change status : null => 0
     * @param type $list_envelope_id_str
     * @param type $customer_id
     * @return type
     */
    public static function requestCollectShipping($list_envelope_id_str, $customer_id) {
        ci()->load->model('scans/envelope_m');
        ci()->load->library('scans/scans_api');
        ci()->load->library('shipping/shipping_api');
        ci()->load->language("mailbox/mailbox");
        
        $ids = APUtils::convertIdsInputToArray($list_envelope_id_str);
        
        if (count($ids) == 0) {
            return array('status' => true, 'message' => '');
        }
        
        //Validate item's weight
        foreach ($ids as $id) {
            $validWeight = shipping_api::checkValidCollectItem($id);
            if (!$validWeight) {
                $message = lang('collect_shipment_over68_warning');
                return array('status' => false, 'message' => $message);
            }
        }
        
        //Requests collect shipping for new item
        foreach ($ids as $id) {
            $envelope = ci()->envelope_m->get_by_many(array(
                'id' => $id,
                'to_customer_id' => $customer_id
            ));
            
            if ($envelope && ($envelope->direct_shipping_flag == APConstants::ON_FLAG || $envelope->trash_flag != '' ) ) {
                continue;
            }
            
            ci()->envelope_m->update_by_many(
                array(
                    'id' => $id,
                    'to_customer_id' => $customer_id,
                    "(collect_shipping_flag IS NULL)" => null
                ),
                array(
                    'collect_shipping_flag' => APConstants::OFF_FLAG,
                    'direct_shipping_flag' => null,
                    'last_updated_date' => now(),
                    'collect_shipping_date' => now()
            ));
            
            // Update trang thai send email
            ci()->envelope_m->update_by_many(
                array(
                    "to_customer_id" => $customer_id,
                    "id" => $id
                ), array(
                "new_notification_flag" => APConstants::OFF_FLAG
            ));

            // save address shipping
            shipping_api::saveShippingAddress($envelope->id, $envelope->shipping_address_id);
        }
        return array('status' => true, 'message' => '');
    }
    
    
   /**
    * Process requests collect shipping after prepayment for item in queues. Change status : 2 => 0
    * @param type $customer_id
    * @param type $api_mobile
    * @return type
    */
    public static function requestCollectShippingAfterPrepayment($list_envelope_id_str, $customer_id, $api_mobile = 0) {
        ci()->load->model('scans/envelope_m');
        ci()->load->library('scans/scans_api');
        ci()->load->language("mailbox/mailbox");
        ci()->load->library('shipping/shipping_api');
        
        $ids = explode(",", $list_envelope_id_str);
        if (count($ids) == 0) {
            return array('status' => true, 'message' => '');
        }
        
        //Check validate weight
        foreach ($ids as $id) {
            // Validate the weight
            $validWeight = shipping_api::checkValidCollectItem($id);
            if (!$validWeight) {
                $message = lang('collect_shipment_over68_warning');
                return array('status' => false, 'message' => $message);
            }
        }
        
        $postbox_id = 0;
        foreach ($ids as $id) {
            
            $envelope = ci()->envelope_m->get_by_many(
                array(
                    'id' => $id,
                    'to_customer_id' => $customer_id
                ));
            
            $postbox_id = $envelope->postbox_id;
            
            if ($envelope && $envelope->direct_shipping_flag === APConstants::ON_FLAG) {
                continue;
            }
            
            ci()->envelope_m->update_by_many(
                array(
                    'id' => $id,
                    'to_customer_id' => $customer_id,
                    "(collect_shipping_flag = '2')" => null
                ),
                array(
                    'collect_shipping_flag' => APConstants::OFF_FLAG,
                    'direct_shipping_flag' => null,
                    'last_updated_date' => now(),
                    'collect_shipping_date' => now()
            ));
            
            // Update trang thai send email
            ci()->envelope_m->update_by_many(
                array(
                    "to_customer_id" => $customer_id,
                    "id" => $id
                ), array(
                "new_notification_flag" => APConstants::OFF_FLAG
            ));
            
        }
        
        // Auto mark collect forwarding
        //Process collect shipping for all item marked request collect in this postbox
        
        //Get list item marked collect shipping (status = 0, package_id = null or 0 )
        $listCollectiveItems = scans_api::getListCollectiveShippingItems($customer_id, $postbox_id);
        
        if (!empty($listCollectiveItems) && count($listCollectiveItems) > 0) {
        
            $location_available_id = $listCollectiveItems[0]->location_available_id;
            $customer_id = $listCollectiveItems[0]->customer_id;
            
            $collectiveShippingItem = scans_api::getItemForCollectiveShippingRequestWithPackageID($customer_id, $location_available_id,$postbox_id);
            
            if ($collectiveShippingItem) {
                $package_id = $collectiveShippingItem->package_id;
            } else {
                $package_id = scans_api::createCollectiveShippingPackage($customer_id, $location_available_id);
            }
            
            //Update packageID for all collect item in postbox of this location
            scans_api::updatePackageIDForAllCollectiveShippingItems($customer_id, $location_available_id, $package_id,$postbox_id);
            foreach ($listCollectiveItems as $collectiveItem) {
                if($collectiveItem->package_id > 0){
                    continue;
                }
                scans_api::insertCompleteItem($collectiveItem->id, APConstants::REQUEST_TRACKING_NUMBER_ACTIVITY_TYPE);
            }
        }
        return array('status' => true, 'message' => '');
    }
    
	
    /**
     * Add new item or marked collect shipping item to queue. Status : NULL or 0 => 2
     * @param type $list_envelope_id_str
     * @param type $customer_id
     * @return type
     */
    public static function requestCollectShippingToQueue($list_envelope_id_str, $customer_id) {
        ci()->load->model('scans/envelope_m');
        ci()->load->library('scans/scans_api');
        
        $ids = APUtils::convertIdsInputToArray($list_envelope_id_str);
        
        if (count($ids) == 0) {
            return;
        }

        foreach ($ids as $id) {
            $envelope = ci()->envelope_m->get_by_many(
                array(
                    'id' => $id,
                    'to_customer_id' => $customer_id
                ));
            
            if ($envelope && $envelope->direct_shipping_flag === APConstants::ON_FLAG) {
                continue;
            }
            
            //Update status for new item or marked collect shipping item
            ci()->envelope_m->update_by_many(
                array(
                    'id' => $id,
                    'to_customer_id' => $customer_id,
                    "(collect_shipping_flag IS NULL OR collect_shipping_flag = '0')" => null,
                    "package_id IS NULL" => null
                ),
                array(
                    'collect_shipping_flag' => '2',
                    'direct_shipping_flag' => null,
                    'last_updated_date' => now()
            ));
            
            // Update trang thai send email
            ci()->envelope_m->update_by_many(
                array(
                    "to_customer_id" => $customer_id,
                    "id" => $id
                ), array(
                "new_notification_flag" => APConstants::OFF_FLAG
            ));
        }
    }
    
    /**
     * Load envelopes in mailbox
     */
    public static function loadEnvelopes($parent_customer_id, $customer_id, $postbox_id, $search_type, $fullTextSearchFlag, $fullTextSearchValue, $start, $limit){
        ci()->load->library('scans/scans_api');
        $list_postbox_id = array();

        if ($postbox_id == 0) {
            $isPrimaryCustomer = APContext::isPrimaryCustomerUser();
            // Get all postbox id of parent customer
            if ($isPrimaryCustomer || APContext::isStandardCustomer()) {
                $list_postbox_id = self::get_postbox_id_of_parent_customer($parent_customer_id);
            } else {
                $list_postbox_id = self::get_postbox_id_of_parent_customer($customer_id);
            }
        }
        if (empty($list_postbox_id) || count($list_postbox_id) == 0) {
            $list_postbox_id[] = 0;
        }
        $arr_postbox_id = implode(',', $list_postbox_id);
        // Fulltext search case
        if ($fullTextSearchFlag == '1') {
            // Reset postbox_id
            $postbox_id = '';
            $list_envelopes = scans_api::getEnvelopePDF($fullTextSearchValue, $fullTextSearchValue, $start, $limit);

            if ($list_envelopes && count($list_envelopes) > 0) {
                $list_envelope_id = array();
                foreach ($list_envelopes as $envelope) {
                    $list_envelope_id[] = $envelope->envelope_id;
                }
                $arr_envelope_id = implode(',', $list_envelope_id);

                $array_where = array(
                    "envelopes.id IN (" . $arr_envelope_id . ')' => null,
                    "envelopes.trash_flag IS NULL" => null
                );
                if ($postbox_id == 0) {
                    $array_where["envelopes.postbox_id IN (" . $arr_postbox_id . ')'] = null;
                } else {
                    $array_where['envelopes.to_customer_id'] = $customer_id;
                }

                $ouput = scans_api::getEnvelopePagingMailbox($array_where, 0, $limit);
                return $ouput;
            }
        } // Select case
        else {
            // Truong hop ma new
            if ($search_type == 7) {
                $array_where = array(
                    "trash_flag IS NULL" => null
                );
                if ($postbox_id == 0) {
                    $array_where["envelopes.postbox_id IN (" . $arr_postbox_id . ')'] = null;
                } else {
                    $array_where['envelopes.postbox_id'] = $postbox_id;
                    $array_where['envelopes.to_customer_id'] = $customer_id;
                }
                
                $ouput = scans_api::getEnvelopePagingMailbox($array_where, $start, $limit);
                return $ouput;
            } // Truong hop ma new
            else if ($search_type == 1) {
                $array_where = array(
                    "completed_flag" => APConstants::OFF_FLAG,
                    "(envelope_scan_flag IS NULL)" => null,
                    "(item_scan_flag IS NULL )" => null,
                    "(direct_shipping_flag IS NULL )" => null,
                    "(collect_shipping_flag IS NULL )" => null,
                    "trash_flag IS NULL" => null
                );
                if ($postbox_id == 0) {
                    $array_where["envelopes.postbox_id IN (" . $arr_postbox_id . ')'] = null;
                } else {
                    $array_where['envelopes.postbox_id'] = $postbox_id;
                    $array_where['envelopes.to_customer_id'] = $customer_id;
                }
                $ouput = scans_api::getEnvelopePagingMailbox($array_where, $start, $limit);
                return $ouput;
            } // Truong hop hien thi envelope scan
            else if ($search_type == 2) {
                $array_where = array(
                    "(envelope_scan_flag = 1 OR item_scan_flag = 1)" => null,
                    "trash_flag IS NULL" => null
                );
                if ($postbox_id == 0) {
                    $array_where["envelopes.postbox_id IN (" . $arr_postbox_id . ')'] = null;
                } else {
                    $array_where['envelopes.postbox_id'] = $postbox_id;
                    $array_where['envelopes.to_customer_id'] = $customer_id;
                }
                $ouput = scans_api::getEnvelopePagingMailbox($array_where, $start, $limit);
                return $ouput;
            } 
            else if ($search_type == 5) {
                $array_where = array(
                    "( trash_flag ='0' or trash_flag = '6' )" => null
                );
                if ($postbox_id == 0) {
                    $array_where["envelopes.postbox_id IN (" . $arr_postbox_id . ')'] = null;
                } else {
                    $array_where['envelopes.postbox_id'] = $postbox_id;
                    $array_where['envelopes.to_customer_id'] = $customer_id;
                }
                $ouput = scans_api::getEnvelopePagingMailbox($array_where, $start, $limit);
                return $ouput;
            } // Truong hop hien thi storage item
            else if ($search_type == 6) {
                $array_where = array(
                    "(direct_shipping_flag <> 1 OR direct_shipping_flag IS NULL)" => null,
                    "(collect_shipping_flag <> 1 OR collect_shipping_flag is null)" => null,
                    "(trash_flag IS NULL)" => null
                );
                if ($postbox_id == 0) {
                    $array_where["envelopes.postbox_id IN (" . $arr_postbox_id . ')'] = null;
                } else {
                    $array_where['envelopes.postbox_id'] = $postbox_id;
                    $array_where['envelopes.to_customer_id'] = $customer_id;
                }
                $ouput = scans_api::getEnvelopePagingMailbox($array_where, $start, $limit);
                return $ouput;
            }
        }
        
        // default value.
        return array(
            'total' => 0,
            'data' => array()
        );
    }
    
    public static function get_postbox_id_of_parent_customer($parent_customer_id) {
        ci()->load->model('mailbox/postbox_m');
        ci()->load->model('customers/postbox_customer_user_m');
        $postbox_users = ci()->postbox_customer_user_m->get_many_by_many(array(
            "parent_customer_id" => $parent_customer_id
        ));
        $postbox_list_id = array();
        foreach($postbox_users as $p){
            $postbox_list_id[] = $p->postbox_id;
        }

        $parent_postboxs = ci()->postbox_m->get_many_by_many(array(
            "customer_id" => $parent_customer_id
        ));
        foreach($parent_postboxs as $p){
            $postbox_list_id[] = $p->postbox_id;
        }
        if (empty($postbox_list_id) || count($postbox_list_id) == 0) {
            $postbox_list_id[] = 0;
        }

        $postboxs = ci()->postbox_m->get_many_by_many(array(
            "deleted <> " => '1',
            "completed_delete_flag <> " => APConstants::ON_FLAG,
            "(postbox_name IS NOT NULL AND postbox_name !='')" => null,
            "((name IS NOT NULL AND name != '') OR (company IS NOT NULL AND company != ''))" => null,
            "postbox_id IN ('".implode("','", $postbox_list_id)."')" => null
        ));
        
        $list_result = array();
        foreach($postboxs as $p){
            $list_result[] = $p->postbox_id;
        }
        return $list_result;
    }

    public static function update_new_notification_flag($envelope_id, $customer_id)
    {
        ci()->load->model('scans/envelope_m');
        ci()->envelope_m->update_by_many(array(
            "to_customer_id" => $customer_id,
            "id" => $envelope_id
        ), array(
            "new_notification_flag" => APConstants::OFF_FLAG
        ));
        return true;
    }
    
    public static function get_total_customs_cost($customer_id, $envelope_id)
    {
        ci()->load->model('scans/envelope_m');
        ci()->load->model('mailbox/envelope_customs_m');
        ci()->load->model('mailbox/envelope_customs_detail_m');
        
        $envelope_customs = EnvelopeUtils::getEnvelopeCustoms($envelope_id);

        if (empty($envelope_customs)) {
            return 0;
        }
        $custom_details = ci()->envelope_customs_detail_m->get_many_by_many(array(
            "customs_id" => $envelope_customs->id
        ));
        $total_cost = 0;
        foreach($custom_details as $detail){
            $total_cost += $detail->quantity * $detail->cost;
        }
        
        return $total_cost;
        
    }

    /**
     * Customer save custom declaration
     * @param type $customer_id
     * @param type $envelope_id
     * @param type $phone_number
     * @param type $declare_customs
     * @return boolean
     */
    public static function save_declare_customs($customer_id, $envelope_id, $phone_number, $declare_customs)
    {
        ci()->load->model('scans/envelope_m');
        ci()->load->model('mailbox/envelope_customs_m');
        ci()->load->model('mailbox/envelope_customs_detail_m');
        ci()->load->library('scans/scans_api');
        ci()->load->model('settings/countries_m');
        
        if (empty($declare_customs)) {
            return false;
        }

        //Get register custom declartion in table envelope_custom
        $envelope_customs = ci()->envelope_customs_m->get_by_many(
            array(
                "customer_id" => $customer_id,
                "envelope_id" => $envelope_id
            ));
        //Get envelope info
        $envelope = ci()->envelope_m->get($envelope_id);
        //If does not register custom declaration or empty envelope
        if (empty($envelope_customs) || empty($envelope)) {
            return false;
        }
        //Get package_id for collect shipping
        $package_id = $envelope_customs->package_id;

        //Save custom declaration info to table custom_detail
        foreach ($declare_customs as $custom) {
            $country = ci()->countries_m->get_by_many(array('country_name' => trim(isset($custom->country) ?$custom->country : null )));
            ci()->envelope_customs_detail_m->insert(
                array(
                    "customs_id" => $envelope_customs->id,
                    "material_name" => $custom->material_name,
                    "quantity" => $custom->quantity,
                    "cost" => $custom->cost,
                    'hs_code' => isset($custom->hs_code) ? $custom->hs_code : null,
                    'country_origin' => !empty($country) ? $country->id: null
                ));
        }

        // Update process flag = 1 (already declare custom information)
        if (empty($package_id)) {
            ci()->envelope_customs_m->update_by_many(
                array(
                    "customer_id" => $customer_id,
                    "envelope_id" => $envelope_id
                ),
                array(
                    "process_flag" => APConstants::ON_FLAG,
                    "phone_number" => $phone_number
                ));
        } else {
            ci()->envelope_customs_m->update_by_many(
                array(
                    "customer_id" => $customer_id,
                    "package_id" => $package_id
                ),
                array(
                    "process_flag" => APConstants::ON_FLAG,
                    "phone_number" => $phone_number
                ));
        }

        //After declare custom successfully, continue to process request shipping
        //Update direct or collect shipping request flag
        if ($envelope_customs->shipping_type == APConstants::DIRECT_FORWARDING) {

            // Check apply pre-payment process
            $check_prepayment_data = CustomerUtils::checkApplyShippingPrepayment(APConstants::TRIGGER_ACTION_TYPE_CUSTOMER, 
                                                                                APConstants::SHIPPING_SERVICE_NORMAL, 
                                                                                APConstants::SHIPPING_TYPE_DIRECT, 
                                                                                array($envelope_id), $customer_id);

            if ($check_prepayment_data['prepayment'] == true) {

                // Add direct shipping request to queue
                // Update direct_shipping_flag = 2 (Organe) 
                mailbox_api::requestDirectShippingToQueue($envelope_id, $customer_id);
                scans_api::insertCompleteItem($envelope_id, APConstants::REQUEST_PREPAYMENT_FOR_DIRECT_FORWARDING_BY_SYSTEM_ACTIVITY_TYPE);

            } else {
                // Request direct shipping is successfull 
                // Update direct_shipping_flag = 0 (yellow)
                // And insert activity:REQUEST_TRACKING_NUMBER = '29'
                // Save address forwarding
                mailbox_api::requestDirectShipping($envelope_id, $customer_id);
                scans_api::insertCompleteItem($envelope_id, APConstants::REQUEST_TRACKING_NUMBER_ACTIVITY_TYPE);

            }

        } elseif ($envelope_customs->shipping_type == APConstants::COLLECT_FORWARDING) {
            //Collect shipping request flag
            if (empty($package_id)) {

                // Check prepayment
                $check_prepayment_data = CustomerUtils::checkApplyShippingPrepayment(APConstants::TRIGGER_ACTION_TYPE_CUSTOMER, 
                                                                                    APConstants::SHIPPING_SERVICE_NORMAL, 
                                                                                    APConstants::SHIPPING_TYPE_COLLECT, 
                                                                                    array($envelope_id), $customer_id);

                // Check prepayment 
                if ($check_prepayment_data['prepayment'] == true) {

                    // Add collect shipping request to queue
                    // collect_shipping_flag = 2(Organe)
                    mailbox_api::requestCollectShippingToQueue($envelope_id, $customer_id);
                    scans_api::insertCompleteItem($envelope_id, APConstants::REQUEST_PREPAYMENT_FOR_COLLECT_FORWARDING_BY_SYSTEM_ACTIVITY_TYPE);
                    return true;
                } else {
                    // Request collect shipping is successful
                    // Update collect_shipping_flag = 0 (yellow)
                    // save address shipping
                    mailbox_api::requestCollectShipping($envelope_id, $customer_id);
                    scans_api::insertCompleteItem($envelope_id, APConstants::REQUEST_TRACKING_NUMBER_ACTIVITY_TYPE);
                }

            } else {
                //In case has package_id
                $listCollectiveItems = ci()->envelope_customs_m->get_many_by_many(array(
                    "package_id" => $package_id,
                    "customer_id" => $customer_id
                ));

                if (count($listCollectiveItems) > 0) {
                    $list_id = array();
                    foreach ($listCollectiveItems as $collectiveItem) {
                        $list_id[] = $collectiveItem->envelope_id;
                    }

                    // Check prepayment
                    $check_prepayment_data = CustomerUtils::checkApplyShippingPrepayment(APConstants::TRIGGER_ACTION_TYPE_CUSTOMER, 
                                                                                        APConstants::SHIPPING_SERVICE_NORMAL, 
                                                                                        APConstants::SHIPPING_TYPE_COLLECT, $list_id, $customer_id);

                    $list_envelope_id_str = implode(',', $list_id);

                    // If prepayment is true
                    if ($check_prepayment_data['prepayment'] == true) {
                        // Add collect shipping request to queue
                        // collect_shipping_flag = 2(Organe)
                        mailbox_api::requestCollectShippingToQueue($list_envelope_id_str, $customer_id);

                         //Insert activity: REQUEST_PREPAYMENT_FOR_COLLECT_FORWARDING_BY_SYSTEM_ACTIVITY_TYPE = '40';
                        scans_api::insertCompleteItem($list_envelope_id_str, APConstants::REQUEST_PREPAYMENT_FOR_COLLECT_FORWARDING_BY_SYSTEM_ACTIVITY_TYPE);

                        return true;
                    } else {
                        // Request collect shipping is successful
                        // Update collect_shipping_flag = 0 (yellow)
                        // save address shipping
                        mailbox_api::requestCollectShipping($list_envelope_id_str, $customer_id);
                        scans_api::insertCompleteItem($list_envelope_id_str, APConstants::REQUEST_TRACKING_NUMBER_ACTIVITY_TYPE);
                        // update package for collect items
                        ci()->envelope_m->update_by_many( array(
                                "to_customer_id" => $customer_id,
                                "id IN (".$list_envelope_id_str.")" => null
                            ), array(
                            "package_id" => $package_id
                        ));
                    }

                }
            }
        }
        //Process save custom declaration successfully
        return true;
        
    }

    /**
     * Create record in table envelope_custom for need custom declare item. This item will be declared late.
     * @param type $customer_id
     * @param type $list_envelope_id
     * @param type $postbox_id
     * @param type $shipping_type
     * @param type $package_id
     * @return boolean
     */
    public static function regist_envelope_customs($customer_id, $list_envelope_id, $postbox_id, $shipping_type, $package_id = ''){
        
        ci()->load->model('mailbox/envelope_customs_m');
        ci()->load->library(array(
            'scans/scans_api'
        ));
        
        $ids = APUtils::convertIdsInputToArray($list_envelope_id);
        
        if (empty($ids)) {
            return FALSE;
        }

        foreach ($ids as $envelope_id) {
            //Get old register custom    
            $envelope_customs_check = ci()->envelope_customs_m->get_by_many(
                array(
                    "customer_id" => $customer_id,
                    "envelope_id" => $envelope_id,
                    "postbox_id" => $postbox_id
                ));

            if (!$envelope_customs_check) {
                ci()->envelope_customs_m->insert(
                    array(
                        "customer_id" => $customer_id,
                        "envelope_id" => $envelope_id,
                        "package_id" => $package_id,
                        "postbox_id" => $postbox_id,
                        "process_flag" => APConstants::OFF_FLAG,
                        "shipping_type" => $shipping_type,
                        "created_date" => now()
                    ));

            } else {
                ci()->envelope_customs_m->update_by_many(
                    array(
                        "customer_id" => $customer_id,
                        "envelope_id" => $envelope_id,
                        "postbox_id" => $postbox_id
                    ), array(
                        "package_id" => $package_id,
                        "last_modified_date" => now()
                    ));
            }

        }
        //Register custom successfully
        return true;
    }
    
}