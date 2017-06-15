<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class CustomerProductSetting{
    public function __construct() {
        ci()->load->model("customers/customer_product_setting_m");
        ci()->load->library("customers/customers_api");
    }

    /**
     * Gets customer setting by key
     * @param type $customer_id
     * @param type $key
     */
    public static function get($customer_id, $product_id, $key){
        $result = ci()->customer_product_setting_m->get_by_many(array(
            "customer_id" => $customer_id,
            "product_id" => $product_id,
            "setting_key" => $key
        ));
        if(!empty($result)){
            return $result->setting_value;
        }
        return "";
    }

    /**
     * set customers setting by key.
     */
    public static function set($customer_id, $product_id, $key, $value){
        $check = ci()->customer_product_setting_m->get_by_many(array(
            "customer_id" => $customer_id,
            "product_id" => $product_id,
            "setting_key" => $key
        ));

        if(empty($check)){
            ci()->customer_product_setting_m->insert(array(
                "customer_id" => $customer_id,
                "product_id" => $product_id,
                "setting_key" => $key,
                "setting_value" => $value,
                'created_date' => now()
            ));
        }else{
            ci()->customer_product_setting_m->update_by_many(array(
                "customer_id" => $customer_id,
                "product_id" => $product_id,
                "setting_key" => $key
            ), array(
                "setting_value" => $value
            ));
        }

        return true;
    }

    /**
     * set customers setting by key.
     */
    public static function set_many($customer_id, $product_id, $arr_key, $arr_value){
        $index = 0;
        foreach($arr_key as $key){
            $value = $arr_value[$index];
            $check = ci()->customer_product_setting_m->get_by_many(array(
                "customer_id" => $customer_id,
                "product_id" => $product_id,
                "setting_key" => $key
            ));

            if(empty($check)){
                ci()->customer_product_setting_m->insert(array(
                    "customer_id" => $customer_id,
                    "product_id" => $product_id,
                    "setting_key" => $key,
                    "setting_value" => $value,
                    'created_date' => now()
                ));
            }else{
                ci()->customer_product_setting_m->update_by_many(array(
                    "customer_id" => $customer_id,
                    "product_id" => $product_id,
                    "setting_key" => $key
                ), array(
                    "setting_value" => $value
                ));
            }

            $index ++;
        }
        return true;
    }

    public static function get_many($customer_id, $product_id){
        $result = ci()->customer_product_setting_m->get_many_by_many(array(
            "customer_id" => $customer_id,
            "product_id" => $product_id,
        ));

        return $result;
    }

    public static function get_all($customer_id){
        $result = ci()->customer_product_setting_m->get_many_by_many(array(
            "customer_id" => $customer_id,
        ));

        return $result;
    }

    public static function get_activate_flags($customer_id){
        ci()->load->model('customers/customer_m');
        $result = array();
        $result['accept_terms_condition_flag'] = 0;
        $result['invoicing_address_completed'] = 0;
        $result['payment_detail_flag'] = 0;
        $result['city_address_flag'] = 0;
        $result['name_comp_address_flag'] = 0;
        $result['postbox_name_flag'] = 0;
        $result['email_confirm_flag'] = 0;
        $result['shipping_address_completed'] = 0;
        $result['SELECTION_CLEVVER_PRODUCT'] = "1";
        $result['activate_add_phone_number'] = 0;
        $result['activate_10_postbox_enterprise_customer'] = 0;

        if (empty($customer_id) || $customer_id == 0) {
            return $result;
        }

        // get customer
        $customer = ci()->customer_m->get($customer_id);
        $result['accept_terms_condition_flag'] = $customer->accept_terms_condition_flag;

        // get activate flags
        $customer_flags = self::get_all($customer_id);

        foreach($customer_flags as $activate_flag){
            $result[$activate_flag->setting_key] = $activate_flag->setting_value;
        }
        return $result;
    }

    /**
     * Do active customer
     * @param type $customer_id
     */
    public static function doActiveCustomer($customer_id, $created_by_id = null){
        ci()->load->model('customers/customer_m');
        // get customer
        $customer = ci()->customer_m->get($customer_id);

        // Gets customer product setting
        $customer_flags = self::get_all($customer_id);
        $invoicing_address_completed = false;
        $payment_detail_flag = false;
        $city_address_flag = false;
        $name_comp_address_flag = false;
        $postbox_name_flag = false;
        $email_confirm_flag = false;
        $shipping_address_completed = false;
        foreach($customer_flags as $activate_flag){
            switch($activate_flag->setting_key){
                case  APConstants::activate_invoicing_address_completed:
                    if($activate_flag->setting_value == APConstants::ON_FLAG){
                        $invoicing_address_completed = true;
                    }
                    break;
                case  APConstants::activate_payment_detail_flag:
                    if($activate_flag->setting_value == APConstants::ON_FLAG){
                        $payment_detail_flag = true;
                    }
                    break;
                case  APConstants::activate_city_address_flag:
                    if($activate_flag->setting_value == APConstants::ON_FLAG){
                        $city_address_flag = true;
                    }
                    break;
                case  APConstants::activate_name_comp_address_flag:
                    if($activate_flag->setting_value == APConstants::ON_FLAG){
                        $name_comp_address_flag = true;
                    }
                    break;
                case  APConstants::activate_postbox_name_flag:
                    if($activate_flag->setting_value == APConstants::ON_FLAG){
                        $postbox_name_flag = true;
                    }
                    break;
                case  APConstants::activate_email_confirm_flag:
                    if($activate_flag->setting_value == APConstants::ON_FLAG){
                        $email_confirm_flag = true;
                    }
                    break;
                case  APConstants::activate_shipping_address_completed:
                    if($activate_flag->setting_value == APConstants::ON_FLAG){
                        $shipping_address_completed = true;
                    }
                    break;
            }
        }
        $save_postboxhistory = $customer->deactivated_type == 'auto' ? true : false;
        if($customer->accept_terms_condition_flag == APConstants::ON_FLAG
                && $invoicing_address_completed
                && $payment_detail_flag
                && $city_address_flag
                && $name_comp_address_flag
                && $postbox_name_flag
                && $email_confirm_flag
                && $shipping_address_completed
                && $customer->deactivated_type != 'manual'){
            ci()->customer_m->update_by_many(array(
                "customer_id" => $customer_id
            ), array(
                "deactivated_type" => "",
                "deactivated_date" => null,
                "activated_flag" => APConstants::ON_FLAG,
                "last_updated_date" => now()
            ));

            //#1309: Insert customer history
            $history = [
                'customer_id' => $customer_id,
                'action_type' => APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_STATUS,
                'created_by_id' => $created_by_id,
                'current_data' => APConstants::CUSTOMER_HISTORY_STATUS_ACTIVATED
            ];
            customers_api::insertCustomerHistory([$history]);

            // 1180 Add re-active postbox history
            if ($save_postboxhistory) {
                // Get all postbox in a customer is deactivated
                $postboxes = ci()->postbox_m->get_many_by('customer_id', $customer_id);
                // Check  postbox is deactivated in a customer
                if (!empty($postboxes)) {
                    foreach ($postboxes as $curr_postbox) {
                        // Check postbox is not deleted
                        if (!empty($curr_postbox) &&
                                ( $curr_postbox->deleted == '0' || ($curr_postbox->deleted == '0' && $curr_postbox->completed_delete_flag == '0'))) {
                             customers_api::addPostboxHistory($curr_postbox->postbox_id, APConstants::POSTBOX_REACTIVATED, $curr_postbox->type);
                            // CustomerUtils::actionPostboxHistoryActivity($curr_postbox->postbox_id, APConstants::POSTBOX_REACTIVATED, now(), $curr_postbox->type, APConstants::INSERT_POSTBOX);
                        }
                    }
                }
            }
        }

        // reload customer login
        APContext::reloadCustomerLoggedIn();
    }

    /**
     * do active customer with phone product registered
     * @param type $customer_id
     */
    public static function doActiveCustomerWithPhoneProduct($customer_id){
        ci()->load->model('customers/customer_m');

        // get customer
        $customer = ci()->customer_m->get($customer_id);

        // Gets customer product setting
        $customer_flags = self::get_all($customer_id);
        $invoicing_address_completed = false;
        $payment_detail_flag = false;
        $activate_10_postbox_enterprise_customer = false;
        $email_confirm_flag = false;
        $shipping_address_completed = false;
        foreach($customer_flags as $activate_flag){
            ${$activate_flag->setting_key} = $activate_flag->setting_value;
        }

        if($customer->accept_terms_condition_flag == APConstants::ON_FLAG
                && $invoicing_address_completed
                && $payment_detail_flag
                && $activate_10_postbox_enterprise_customer
                && $email_confirm_flag
                && $shipping_address_completed){
            ci()->customer_m->update_by_many(array(
                "customer_id" => $customer_id
            ), array(
                "deactivated_type" => "",
                "deactivated_date" => null,
                "activated_flag" => APConstants::ON_FLAG,
                "last_updated_date" => now()
            ));
        }
    }

}
