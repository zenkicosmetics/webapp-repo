<?php defined('BASEPATH') or exit('No direct script access allowed');

class payment_api
{
    public static function insertExternalTranHist($customer_id, $tranId, $tranDate, $tranAmount)
    {
        ci()->load->model('payment/external_tran_hist_m');

        ci()->external_tran_hist_m->insert(array(
            "customer_id" => $customer_id,
            "tran_id" => $tranId,
            "tran_date" => $tranDate,
            "tran_amount" => (-1) * ($tranAmount),
            "payment_type" => "0", // payment
            "created_date" => now(),
            "status" => "OK"
        ));
    }

    public static function countPayment($customer_id)
    {
        ci()->load->model('payment/payment_m');

        $payment_count = ci()->payment_m->count_by_many(array(
            "customer_id" => $customer_id
        ));

        return $payment_count;
    }


    public static function getPayment($customerID, $primary_card)
    {
        ci()->load->model('payment/payment_m');

        $payments = ci()->payment_m->get_many_by_many(array(
            'customer_id' => $customerID,
            "primary_card" => $primary_card
        ));

        return $payments;
    }
    
    public static function getPaypalFeeByLocation($location_id, $target_month)
    {
        ci()->load->model("invoices/invoice_summary_by_location_m");
        if($location_id){
            $total = ci()->invoice_summary_by_location_m->sum_by_many(array(
                "location_id" => $location_id,
                "LEFT(invoice_month, 6)= " => $target_month,
                "(payment_transaction_id IS NOT NULL AND payment_transaction_id <> '')" => null
            ), "total_invoice");
            
            $quantity = ci()->invoice_summary_by_location_m->count_by_many(array(
                "location_id" => $location_id,
                "LEFT(invoice_month, 6)= " => $target_month,
                "(payment_transaction_id IS NOT NULL AND payment_transaction_id <> '')" => null
            ));
        }else{
            $total = ci()->invoice_summary_by_location_m->sum_by_many(array(
                "LEFT(invoice_month, 6)= " => $target_month,
                "(payment_transaction_id IS NOT NULL AND payment_transaction_id <> '')" => null
            ), "total_invoice");
            
            $quantity = ci()->invoice_summary_by_location_m->count_by_many(array(
                "LEFT(invoice_month, 6)= " => $target_month,
                "(payment_transaction_id IS NOT NULL AND payment_transaction_id <> '')" => null
            ));
        }
        
        return array(
             "total" => $total,
             "quantity" => $quantity
        );
    }
    
    /**
     * Check exsiting customer had setting credit card or not
     * @param type $customer_id
     */
    public static function isSettingCreditCard($customer_id) {
        ci()->load->model('payment/payment_m');
        $payment = ci()->payment_m->get_payment_account($customer_id, 0, 0);
        if(empty($payment) || count($payment) == 0){
            return false;
        }
        return true;
    }
    
    /**
     * Update payment user.
     * 
     * @param type $customer_id
     * @param type $userid
     * @param type $type
     */
    public static function updatePaymentUser($customer_id, $userid, $type) {
        ci()->load->model("payment/customer_payment_user_m");
        $payment_user_check = ci()->customer_payment_user_m->get_by_many(array(
            'customer_id' => $customer_id,
            'type' => $type
        ));
        if (empty($payment_user_check)) {
            ci()->customer_payment_user_m->insert(array(
                'customer_id' => $customer_id,
                'userid' => $userid,
                'type' => $type,
                'created_date' => now()
            ));
        } else {
            ci()->customer_payment_user_m->update_by_many(array(
                'customer_id' => $customer_id,
                'type' => $type
            ),array(
                'userid' => $userid,
                'updated_date' => now()
            ));
        }
    }
    
    /**
     * Get payment user by type.
     * 
     * @param type $customer_id
     * @param type $type
     */
    public static function getPayoneUser($customer_id) {
        return payment_api::getPaymentUser($customer_id, 'payone');
    }
    
    /**
     * Get payment user by type.
     * 
     * @param type $customer_id
     * @param type $type
     */
    public static function getPaymentUser($customer_id, $type) {
        ci()->load->model("payment/customer_payment_user_m");
        $payment_user_check = ci()->customer_payment_user_m->get_by_many(array(
            'customer_id' => $customer_id,
            'type' => $type
        ));
        if (!empty($payment_user_check)) {
            return $payment_user_check->userid;
        }
        return '';
    }
    
    
    /**
     * add new payment method
     */
    public static function addPaymentMethod($customer_id, $account_type, $card_type, $card_number,
            $card_name, $cvc, $expired_year, $expired_month , $pseudocardpan, $created_by_id = null) {
        ci()->load->model('payment/payment_m');
        ci()->lang->load('payment/payment');
        ci()->load->library('Payone_lib');
        ci()->load->config('payone');
        
        $payment_check = ci()->payment_m->get_by_many( array(
            "customer_id" => $customer_id
        ));
        
        // Payment already confirm
        $callback_tran_id = $customer_id . '_' . APUtils::generateRandom(32);

        //log_message(APConstants::LOG_DEBUG, ">>> Customer ID: " . $customer_id . " call ADD payment method. Payment information does not exist");
        // Insert data to database
        $primary_card = APConstants::OFF_FLAG;
        if (empty($payment_check)) {
            $primary_card = APConstants::ON_FLAG;
            $history_list['change_paymentmethod'] = [
                'customer_id' => $customer_id,
                'action_type' => APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_PAYMENT_METHOD,
                'current_data' => APConstants::CUSTOMER_HISTORY_PAYMENT_METHOD_CREDIT_CARD,
                'created_by_id' => $created_by_id,
            ];
        }

        // Insert this CC to database
        $payment = array(
            "account_type" => $account_type,
            "card_type" => $card_type,
            "card_number" => $card_number,
            "card_name" => $card_name,
            "cvc" => $cvc,
            "expired_year" => $expired_year,
            "expired_month" => $expired_month,
            "customer_id" => $customer_id,
            "card_confirm_flag" => APConstants::OFF_FLAG,
            "pseudocardpan" => $pseudocardpan,
            "callback_tran_id" => $callback_tran_id,
            "primary_card" => $primary_card
        );
        $new_payment_id = ci()->payment_m->insert($payment);
        // #1309: log customer history_list
        $history_list['add_card'] = [
            'customer_id' => $customer_id,
            'action_type' => APConstants::CUSTOMER_HISTORY_ACTIVITY_ADD_CREDITCARD,
            'current_data' => json_encode($payment),
            'created_by_id' => $created_by_id,
        ];

        // Make request to create payment
        $expired = $expired_year . $expired_month;

        $redirecturl = self::make_default_payment($pseudocardpan, $callback_tran_id, $customer_id);

        // This is none 3D secure credit card
        if (empty($redirecturl)) {
            //log_message(APConstants::LOG_DEBUG, ">>> Customer ID: " . $customer_id . " call ADD payment method. Redirect URL is empty");

            // Add payment library and make pending payment history
            ci()->load->library('payment/payone');

            $payment_result = true;
            // // Can not charge none 3D secure credit card directly from customer
            // $open_balance = APUtils::getCurrentBalance($customer_id);
            // if ($open_balance > 0.01) {
            //    $payment_result = ci()->payone->make_pending_payment($customer_id, $callback_tran_id);
            // } else {
            //    $payment_result = true;
            // }
            // If make payment successfully
            if ($payment_result) {
                // Update data to customer
                ci()->customer_m->update_by_many( array(
                    "customer_id" => $customer_id
                ), array(
                    "payment_detail_flag" => APConstants::ON_FLAG
                ));

                // Update credit card status
                ci()->payment_m->update_by_many( array(
                    "customer_id" => $customer_id,
                    "callback_tran_id" => $callback_tran_id
                ), array(
                    "account_type" => $account_type,
                    "card_type" => $card_type,
                    "card_number" => $card_number,
                    "card_name" => $card_name,
                    "cvc" => $cvc,
                    "expired_year" => $expired_year,
                    "expired_month" => $expired_month,
                    "pseudocardpan" => $pseudocardpan,
                    "card_confirm_flag" => APConstants::ON_FLAG
                ));

                // Set primary card
                // Always to set last CC is primary card
                ci()->payment_m->update_by_many( array(
                    "customer_id" => $customer_id
                ), array(
                    "primary_card" => APConstants::OFF_FLAG
                ));
                ci()->payment_m->update_by_many( array(
                    "customer_id" => $customer_id,
                    'payment_id' => $new_payment_id
                ), array(
                    "primary_card" => APConstants::ON_FLAG
                ));

                // #543: BUG: last payment method is not always set as standard
                // Update invoice type (make CC is standard payment method)
                ci()->customer_m->update_by_many( array(
                    'customer_id' => $customer_id
                ), array(
                    'invoice_type' => '1'
                ));

                // #1309: Log change primary card
                $change_primarycard_history = $history_list['add_card'];
                $change_primarycard_history['action_type'] = APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_PRIMARY_CREDITCARD;
                $history_list['change_primarycard'] = $change_primarycard_history;

                // update: convert registration process flag to customer_product_setting.
                CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'payment_detail_flag', APConstants::OFF_FLAG);
                customers_api::insertCustomerHistory($history_list);
                // If open balance less than 0.1 will activated customer now
                // we dont have to tell the Customer that a valid credit Card with non working payment can reactivate the account.
                // in most cases the Customer will Chose a Card that can handle the payment if it is valid
                // if ($open_balance <= 0.1) {
                // Only reactivate if deactivated_type = auto
                // customers_api::activateCustomerWhenUpdatePaymentMethod($customer_id);
            }
        } else {
            //log_message(APConstants::LOG_DEBUG, ">>> Customer ID: " . $customer_id . " call ADD payment method. Redirect URL is not empty:" . $redirecturl);
            if ($redirecturl != 'ERROR') {
                return array(
                    'status' => true,
                    'redirect' => true,
                    'message' => $redirecturl,
                    'result' => $redirecturl
                );
            } else {
                // Delete card on database
                ci()->payment_m->delete_by_many( array(
                    "account_type" => $account_type,
                    "card_type" => $card_type,
                    "card_number" => $card_number,
                    "card_confirm_flag" => APConstants::OFF_FLAG,
                    "customer_id" => $customer_id,
                    "callback_tran_id" => $callback_tran_id
                ));
                $message = lang('add_payment_fail');
                return array(
                    'status' => false,
                    'redirect' => false,
                    'message' => $message,
                    'result' => ''
                );
            }
            return array();
        }
    }
    
    /**
     * make default payment
     * @param type $pseudocardpan
     * @param type $callback_tran_id
     * @param string $customer_name
     * @return string
     */
    public static function make_default_payment($pseudocardpan, $callback_tran_id, $customer_id)
    {
        ci()->load->library('Payone_lib');
        ci()->load->config('payone');
        ci()->load->model("customers/customer_m");
        // Get config
        $merchant_id = ci()->config->item('payone.merchant-id');
        $portal_id = ci()->config->item('payone.portal-id');
        $portal_key = ci()->config->item('payone.portal-key');
        $sub_account_id = ci()->config->item('payone.sub-account-id');
        $mode = ci()->config->item('payone.mode');
        $encoding = ci()->config->item('payone.encoding');

        // Build service
        $bootstrap = new Payone_Bootstrap();
        $bootstrap->init();
        $builder = new Payone_Builder();
        $service = $builder->buildServiceVerification3dsCheck();

        // $request = new Payone_Api_Request_Preauthorization();
        $request = new Payone_Api_Request_Preauthorization();
        $request->setPortalid($portal_id);
        $request->setMid($merchant_id);
        $request->setKey($portal_key);
        $request->setAid($sub_account_id);
        $request->setMode($mode);
        $request->setEncoding($encoding);
        $request->setAmount('1');
        $request->setCurrency('EUR');
        $request->setClearingtype('cc');
        $request->setReference(now() . '_1ST');

        // Set person data
        $request_person_data = new Payone_Api_Request_Parameter_Authorization_PersonalData();
        $customer = ci()->customer_m->getCustomerInfoForPayOne($customer_id);
        $request_person_data->setCustomerid(!empty($customer->customer_code) ? $customer->customer_code : '');
        $last_name = APUtils::removeSpecialCharacterForPayone($customer->invoicing_address_name);
        $request_person_data->setLastname(!empty($last_name) ? $last_name : 'Christian Hemmrich');
        $company_name = APUtils::removeSpecialCharacterForPayone($customer->invoicing_company);
        $request_person_data->setCompany(!empty($company_name) ? $company_name : '');
        $request_person_data->setCountry(!empty($customer->country_code) ? $customer->country_code : 'DE');
        $request_person_data->setEmail(!empty($customer->email) ? $customer->email : '');
   
        $request->setPersonalData($request_person_data);
   
        $request->setPersonalData($request_person_data);

        $request_credit_date = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_CreditCard();
        $request_credit_date->setPseudocardpan($pseudocardpan);
        $request_credit_date->setSuccessurl(APContext::getFullBasePath() . 'payment/success_callback?callback_tran_id=' . $callback_tran_id);
        $request_credit_date->setErrorurl(APContext::getFullBasePath() . 'payment/error_callback?callback_tran_id=' . $callback_tran_id);
        $request->setPayment($request_credit_date);

        $service = $builder->buildServicePaymentPreauthorize();
        $response = $service->preauthorize($request);
        if ($response) {
            if ($response->getStatus() == 'REDIRECT') {
                // Update user id and transaction id
                $xid = $response->getTxid();
                $userid = $response->getUserid();

                // Store this information to database
                ci()->payment_m->update_by_many(array(
                    "callback_tran_id" => $callback_tran_id
                ), array(
                    "xid" => $xid,
                    "userid" => $userid
                ));

                return $response->getRedirecturl();
            } else if ($response->getStatus() == 'APPROVED') {
                return '';
            }
        }
        return 'ERROR';
    }
    
    public static function set_primary_card($customer_id, $payment_id){
        ci()->load->model('payment/payment_m');
        ci()->load->model('customers/customer_m');
        
        // Clear all other primary card
        ci()->payment_m->update_by_many(array(
            'customer_id' => $customer_id
        ), array(
            'primary_card' => APConstants::OFF_FLAG
        ));

        // Setting new primary card
        ci()->payment_m->update_by_many(
            array(
                'customer_id' => $customer_id,
                'payment_id' => $payment_id
            ), array(
            'primary_card' => APConstants::ON_FLAG
        ));

        if($payment_id){
            // Update invoice type
            ci()->customer_m->update_by_many(array(
                'customer_id' => $customer_id
            ), array(
                'invoice_type' => '1'
            ));
        }

        //#1309: Insert customer history_list
        $payment = ci()->payment_m->get_by_many( array(
            'payment_id' => $payment_id,
            'customer_id' => $customer_id
        ));
        $history_list['change_paymentmethod'] = [
            'customer_id' => $customer_id,
            'action_type' => APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_PAYMENT_METHOD,
            'current_data' => APConstants::CUSTOMER_HISTORY_PAYMENT_METHOD_CREDIT_CARD,
            'created_by_id' => APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER,
        ];
        $history_list['change_primarycard'] = [
            'customer_id' => $customer_id,
            'action_type' => APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_PRIMARY_CREDITCARD,
            'current_data' => json_encode($payment),
            'created_by_id' => APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER,
        ];
        customers_api::insertCustomerHistory($history_list);
    }
    
}