<?php

defined('BASEPATH') or exit('No direct script access allowed');

class payone {
    
    /**
     * Update email address from clevvermail to payone.
     * 
     * @param type $customer_id
     */
    public function update_user($customer_id) {
        ci()->load->library('Payone_lib');
        ci()->load->library('payment/payment_api');
        ci()->load->config('payone');
        ci()->load->model('customers/customer_m');
        
        $customer = ci()->customer_m->get_by('customer_id', $customer_id);
        if (empty($customer)) {
            log_message(APConstants::LOG_ERROR, 'Customer information of customer id: ' . $customer_id . ' does not exist');
            return false;
        }
        $email = $customer->email;
        
        // Get payone user id from
            
        $userid = payment_api::getPayoneUser($customer_id);
        if (empty($userid)) {
            log_message(APConstants::LOG_ERROR, 'Payone user of customer id: ' . $customer_id . ' does not exist');
            return false;
        }
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
        $service = $builder->buildServiceManagementUpdateUser();
        
        $request = new Payone_Api_Request_UpdateUser();
        $request->setPortalid($portal_id);
        $request->setMid($merchant_id);
        $request->setKey($portal_key);
        // $request->setAid($sub_account_id);
        $request->setMode($mode);
        $request->setEncoding($encoding);
        
        $request->setUserid($userid);
        $request->setEmail($email);
        
        $customerInfo = ci()->customer_m->getCustomerInfoForPayOne($customer_id);
        $request->setCustomerid(!empty($customerInfo->customer_code) ? $customerInfo->customer_code : '');
        $request->setLastname(!empty($customerInfo->invoicing_address_name) ? $customerInfo->invoicing_address_name : 'Christian Hemmrich');
        $request->setCompany(!empty($customerInfo->invoicing_company) ? $customerInfo->invoicing_company : '');
        $request->setCountry(!empty($customerInfo->country_code) ? $customerInfo->country_code : 'DE');
        $request->setEmail(!empty($customerInfo->email) ? $customerInfo->email : '');
        
        $response = $service->updateUser($request);
        if ($response->getStatus() == 'OK') {
            return true;
        }
        return false;
    }

    /**
     * Thanh toan dinh ky hang thang.
     */
    public function preauthorize($customer_id, $invoice_id, $amount, $tran_id = '') {
        // Get customer information
        ci()->load->library('Payone_lib');
        ci()->load->library('payment/payment_api');
        ci()->load->config('payone');
        ci()->load->model('customers/customer_m');
        ci()->load->model('payment/payment_m');
        ci()->load->model('payment/payment_tran_hist_m');

        $customer = ci()->customer_m->get_by('customer_id', $customer_id);
        if (empty($customer)) {
            log_message(APConstants::LOG_DEBUG, 'Customer information of customer id: ' . $customer_id . ' does not exist');
            return;
        }
        $customer_name = $customer->user_name;

        // Get card information
        $customer_payment = $this->get_credit_card($customer_id, $tran_id);
        if (empty($customer_payment)) {
            return;
        }

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
        $service = $builder->buildServicePaymentPreauthorize();

        $request = new Payone_Api_Request_Preauthorization();
        $request->setPortalid($portal_id);
        $request->setMid($merchant_id);
        $request->setKey($portal_key);
        $request->setAid($sub_account_id);
        $request->setMode($mode);
        $request->setEncoding($encoding);
        $request->setAmount($amount);
        $request->setCurrency('EUR');
        $request->setClearingtype('cc');
        $request->setReference($invoice_id);

        // Set person data
        $request_person_data = new Payone_Api_Request_Parameter_Authorization_PersonalData();
//        $request_person_data->setLastname($customer_name);
//        $request_person_data->setCountry('DE');
        $customerInfo = ci()->customer_m->getCustomerInfoForPayOne($customer_id);

        $request_person_data->setCustomerid(!empty($customerInfo->customer_code) ? $customerInfo->customer_code : '');
        $last_name = APUtils::removeSpecialCharacterForPayone($customerInfo->invoicing_address_name);
        $request_person_data->setLastname(!empty($last_name) ? $last_name : 'Christian Hemmrich');
        $company_name = APUtils::removeSpecialCharacterForPayone($customerInfo->invoicing_company);
        $request_person_data->setCompany(!empty($company_name) ? $company_name : '');
        $request_person_data->setCountry(!empty($customerInfo->country_code) ? $customerInfo->country_code : 'DE');
        $request_person_data->setEmail(!empty($customerInfo->email) ? $customerInfo->email : '');
        
        $userid = payment_api::getPayoneUser($customer_id);
        if (!empty($userid)) {
            $request_person_data->setUserid($userid);
        }
        $request->setPersonalData($request_person_data);

        // Set credit card
        $request_credit_date = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_CreditCard();
        $request_credit_date->setPseudocardpan($customer_payment->pseudocardpan);
        $request->setPayment($request_credit_date);

        // Insert data to payment_tran_hist
        $new_tran_id = ci()->payment_tran_hist_m->insert(
                array(
                    "customer_id" => $customer_id,
                    "tran_date" => now(),
                    "tran_type" => "preauthorize",
                    "pseudocardpan" => $customer_payment->pseudocardpan,
                    "amount" => $amount,
                    "ccy" => 'EUR'
        ));

        $response = $service->preauthorize($request);
        if ($response->getStatus() != 'ERROR') {
            // Update status
            ci()->payment_tran_hist_m->update_by_many(array(
                "id" => $new_tran_id
                    ), array(
                "status" => $response->getStatus()
            ));
            
            // Get user id and store in the database
            // $txid = $response->getTxid();
            $new_userid = $response->getUserid();
            payment_api::updatePaymentUser($customer_id, $new_userid, 'payone');
            
            return true;
        }

        // Update status
        ci()->payment_tran_hist_m->update_by_many(array(
            "id" => $new_tran_id
                ), array(
            "status" => $response->getStatus(),
            "message" => $response->getErrormessage()
        ));
        return false;
    }

    /**
     * Thanh toan dinh ky hang thang.
     */
    public function authorize($customer_id, $invoice_id, $amount, $tran_id = '') {
        try {
            // Get customer information
            ci()->load->library('Payone_lib');
            ci()->load->library('payment/payment_api');
            ci()->load->config('payone');
            ci()->load->model('customers/customer_m');
            ci()->load->model('payment/payment_m');
            ci()->load->model('payment/payment_tran_hist_m');
            ci()->load->config('payone');
            $customer = ci()->customer_m->get_by('customer_id', $customer_id);
            if (empty($customer)) {
                log_message(APConstants::LOG_ERROR, 'Customer information of customer id: ' . $customer_id . ' does not exist');
                return false;
            }

            if (abs($amount) < 0.1 || $amount < 0) {
                log_message(APConstants::LOG_ERROR, 'Can not make payment less than 0.1 EUR for customer id: ' . $customer_id);
                return true;
            }

            $customer_name = $customer->user_name;
            $email = $customer->email;

            // Check if customer already invoice code and not make payment for this customer
            if ($customer->invoice_type == '2') {
                log_message(APConstants::LOG_ERROR, 'Invoice code of customer id:' . $customer_id . ' already exist.');
                return true;
            }

            // Get card information
            $customer_payment = $this->get_credit_card($customer_id, $tran_id);
            if (empty($customer_payment)) {
                log_message(APConstants::LOG_ERROR, 'Payment information of customer id:' . $customer_id . ' does not exist.');
                // Set auto-deactivated this customer
                ci()->customer_m->update_by_many(array(
                    "customer_id" => $customer_id,
                    "activated_flag <> '0'" => null,
                        ), array(
                    "activated_flag" => APConstants::OFF_FLAG,
                    "deactivated_type" => APConstants::AUTO_INACTIVE_TYPE,
                    "deactivated_date" => now(),
                    "payment_detail_flag" => APConstants::OFF_FLAG,
                    "last_updated_date" => now()
                ));
                
                // update: convert registration process flag to customer_product_setting.
                CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'payment_detail_flag', APConstants::OFF_FLAG);

                // Send email trigger
                Events::trigger('deactivated_notifications', array(
                    'customer_id' => $customer_id
                        ), 'string');
                return false;
            }

            // Set reference
            $reference = sprintf('C%1$08d', $customer_id) . '_' . time();

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
            $service = $builder->buildServicePaymentAuthorize();

            $request = new Payone_Api_Request_Authorization();
            $request->setPortalid($portal_id);
            $request->setMid($merchant_id);
            $request->setKey($portal_key);
            $request->setAid($sub_account_id);
            $request->setMode($mode);
            $request->setEncoding($encoding);
            $request->setAmount($amount);
            $request->setCurrency('EUR');
            $request->setClearingtype('cc');
            $request->setReference($reference);

            // Set person data
            $request_person_data = new Payone_Api_Request_Parameter_Authorization_PersonalData();
            
            $customerInfo = ci()->customer_m->getCustomerInfoForPayOne($customer_id);

            $request_person_data->setCustomerid(!empty($customerInfo->customer_code) ? $customerInfo->customer_code : '');
            $last_name = APUtils::removeSpecialCharacterForPayone($customerInfo->invoicing_address_name);
            $request_person_data->setLastname(!empty($last_name) ? $last_name : 'Christian Hemmrich');
            $company_name = APUtils::removeSpecialCharacterForPayone($customerInfo->invoicing_company);
            $request_person_data->setCompany(!empty($company_name) ? $company_name : '');
            $request_person_data->setCountry(!empty($customerInfo->country_code) ? $customerInfo->country_code : 'DE');
            $request_person_data->setEmail(!empty($customerInfo->email) ? $customerInfo->email : '');
            
//            $request_person_data->setLastname($customer_name);
//            $request_person_data->setEmail($email);
//            $request_person_data->setCountry('DE');
            $userid = payment_api::getPayoneUser($customer_id);
            if (!empty($userid)) {
                $request_person_data->setUserid($userid);
            }
            $request->setPersonalData($request_person_data);

            // Set credit card
            $request_credit_date = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_CreditCard();
            $request_credit_date->setPseudocardpan($customer_payment->pseudocardpan);
            $request_credit_date->setSuccessurl(APContext::getFullBasePath() . 'payment/payone_authorize_success_callback?reference=' . $invoice_id);
            $request_credit_date->setErrorurl(APContext::getFullBasePath() . 'payment/payone_authorize_error_callback?reference=' . $invoice_id);
            $request_credit_date->setEcommercemode('internet');
            $request->setPayment($request_credit_date);

            // Insert data to payment_tran_hist
            $new_tran_id = ci()->payment_tran_hist_m->insert(
                    array(
                        "customer_id" => $customer_id,
                        "tran_date" => now(),
                        "tran_type" => "authorize",
                        "pseudocardpan" => $customer_payment->pseudocardpan,
                        "payment_id" => $customer_payment->payment_id,
                        "amount" => $amount,
                        "ccy" => 'EUR',
                        "invoice_id" => $invoice_id,
                        "reference" => $reference
            ));

            $response = $service->authorize($request);
            if ($response->getStatus() != 'ERROR') {
                // Update status
                ci()->payment_tran_hist_m->update_by_many(array(
                    "id" => $new_tran_id
                        ), array(
                    "status" => $response->getStatus()
                ));
                
                // Get user id and store in the database
                // $txid = $response->getTxid();
                $new_userid = $response->getUserid();
                payment_api::updatePaymentUser($customer_id, $new_userid, 'payone');
                
                return true;
            }

            // Update status
            ci()->payment_tran_hist_m->update_by_many(array(
                "id" => $new_tran_id
                    ), array(
                "status" => $response->getStatus(),
                "message" => $response->getErrormessage()
            ));

            // Update card_charge_flag status
            ci()->payment_m->update_by_many(array(
                "payment_id" => $customer_payment->payment_id,
                "customer_id" => $customer_id
                    ), array(
                "card_charge_flag" => APConstants::CARD_CHARGE_FAIL,
            ));

            // Gets open balance
            $open_balance_due = APUtils::getCurrentBalance($customer_id);

            if ($open_balance_due > 0.01) {
                // Send email
                CustomerUtils::sendEmailPaymentFail($customer_id);
                
                // Set auto-deactivated this customer
                ci()->customer_m->update_by_many(array(
                    "customer_id" => $customer_id,
                    "activated_flag <> '0'" => null,
                        ), array(
                    "activated_flag" => APConstants::OFF_FLAG,
                    "deactivated_type" => APConstants::AUTO_INACTIVE_TYPE,
                    "deactivated_date" => now(),
                    "payment_detail_flag" => APConstants::OFF_FLAG,
                    "last_updated_date" => now()
                ));
                
                // update: convert registration process flag to customer_product_setting.
                CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'payment_detail_flag', APConstants::OFF_FLAG);

                // Send email trigger
                Events::trigger('deactivated_notifications', array(
                    'customer_id' => $customer_id
                        ), 'string');
            }
            return false;
        } catch (Exception $e) {
            log_message(APConstants::LOG_ERROR, $e->getMessage());
            return false;
        }
    }

    /**
     * Thanh toan dinh ky hang thang.
     * Return 0: if success | 1: Fail 
     */
    public function authorize_bycreditcard($customer_id, $invoice_id, $amount, $tran_id = '', $credit_card_id) {
        try {
            // Get customer information
            ci()->load->library('Payone_lib');
            ci()->load->library('payment/payment_api');
            ci()->load->config('payone');
            ci()->load->model('customers/customer_m');
            ci()->load->model('payment/payment_m');
            ci()->load->model('payment/payment_tran_hist_m');
            ci()->load->config('payone');
            $customer = ci()->customer_m->get_by('customer_id', $customer_id);
            if (empty($customer)) {
                log_message(APConstants::LOG_ERROR, 'Customer information of customer id: ' . $customer_id . ' does not exist');
                return APConstants::ON_FLAG;
            }

            if (abs($amount) < 0.1 || $amount < 0) {
                log_message(APConstants::LOG_ERROR, 'Can not make payment less than 0.1 EUR for customer id: ' . $customer_id);
                return APConstants::OFF_FLAG;
            }

            $customer_name = $customer->user_name;
            $email = $customer->email;
            
            // Set reference
            $reference = sprintf('C%1$08d', $customer_id) . '_' . time();

            // Check if customer already invoice code and not make payment for this customer
            if ($customer->invoice_type == '2') {
                log_message(APConstants::LOG_ERROR, 'Invoice code of customer id:' . $customer_id . ' already exist.');
                return APConstants::OFF_FLAG;
            }

            // Get card information
            $customer_payment = $this->get_credit_card_by_card_id($customer_id, $tran_id, $credit_card_id);
            if (empty($customer_payment)) {
                log_message(APConstants::LOG_ERROR, 'Payment information of customer id:' . $customer_id . ' does not exist.');
                return APConstants::ON_FLAG;
            }

            // Ticket 885: Did not support AMEX card when customer make payment directly
            if ($customer_payment->card_type == 'A') {
                log_message(APConstants::LOG_ERROR, 'Can not make payment transaction directly from AMEX caed of customer id:' . $customer_id);
                return APConstants::ON_FLAG;
            }

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
            $service = $builder->buildServicePaymentAuthorize();

            $request = new Payone_Api_Request_Authorization();
            $request->setPortalid($portal_id);
            $request->setMid($merchant_id);
            $request->setKey($portal_key);
            $request->setAid($sub_account_id);
            $request->setMode($mode);
            $request->setEncoding($encoding);
            $request->setAmount($amount);
            $request->setCurrency('EUR');
            $request->setClearingtype('cc');
            $request->setReference($reference);

            // Set person data
            $request_person_data = new Payone_Api_Request_Parameter_Authorization_PersonalData();
//            $request_person_data->setLastname($customer_name);
//            $request_person_data->setEmail($email);
//            $request_person_data->setCountry('DE');
            
            $customerInfo = ci()->customer_m->getCustomerInfoForPayOne($customer_id);

            $request_person_data->setCustomerid(!empty($customerInfo->customer_code) ? $customerInfo->customer_code : '');
            $last_name = APUtils::removeSpecialCharacterForPayone($customerInfo->invoicing_address_name);
            $request_person_data->setLastname(!empty($last_name) ? $last_name : 'Christian Hemmrich');
            $company_name = APUtils::removeSpecialCharacterForPayone($customerInfo->invoicing_company);
            $request_person_data->setCompany(!empty($company_name) ? $company_name : '');
            $request_person_data->setCountry(!empty($customerInfo->country_code) ? $customerInfo->country_code : 'DE');
            $request_person_data->setEmail(!empty($customerInfo->email) ? $customerInfo->email : '');
            
            $userid = payment_api::getPayoneUser($customer_id);
            if (!empty($userid)) {
                $request_person_data->setUserid($userid);
            }
            $request->setPersonalData($request_person_data);

            // Set credit card
            $request_credit_date = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_CreditCard();
            $request_credit_date->setPseudocardpan($customer_payment->pseudocardpan);
            $request_credit_date->setSuccessurl(APContext::getFullBasePath() . 'payment/payone_authorize_success_callback?reference=' . $invoice_id);
            $request_credit_date->setErrorurl(APContext::getFullBasePath() . 'payment/payone_authorize_error_callback?reference=' . $invoice_id);
            $request_credit_date->setEcommercemode('3dsecure');
            $request->setPayment($request_credit_date);

            // Insert data to payment_tran_hist
            $new_tran_id = ci()->payment_tran_hist_m->insert(
                    array(
                        "customer_id" => $customer_id,
                        "tran_date" => now(),
                        "tran_type" => "authorize",
                        "pseudocardpan" => $customer_payment->pseudocardpan,
                        "payment_id" => $customer_payment->payment_id,
                        "amount" => $amount,
                        "ccy" => 'EUR',
                        "invoice_id" => $invoice_id,
                        "reference" => $reference
            ));

            $response = $service->authorize($request);
            if ($response->getStatus() != 'ERROR') {
                // Update status
                ci()->payment_tran_hist_m->update_by_many(array(
                    "id" => $new_tran_id
                        ), array(
                    "status" => $response->getStatus()
                ));
                
                // Get user id and store in the database
                // $txid = $response->getTxid();
                $new_userid = $response->getUserid();
                payment_api::updatePaymentUser($customer_id, $new_userid, 'payone');
                
                // Check if 3D secure
                if ($response->getStatus() == 'REDIRECT') {
                    return $response->getRedirecturl();
                }

                return APConstants::OFF_FLAG;
            }

            // Update status
            ci()->payment_tran_hist_m->update_by_many(array(
                "id" => $new_tran_id
                    ), array(
                "status" => $response->getStatus(),
                "message" => $response->getErrormessage()
            ));

            // Update card_charge_flag status
            ci()->payment_m->update_by_many(array(
                "payment_id" => $customer_payment->payment_id,
                "customer_id" => $customer_id
                    ), array(
                "card_charge_flag" => APConstants::CARD_CHARGE_FAIL,
            ));

            // Gets open balance
            $open_balance_due = APUtils::getCurrentBalance($customer_id);

            if ($open_balance_due > 0.01) {
                // Send email
                CustomerUtils::sendEmailPaymentFail($customer_id);
                
                // Set auto-deactivated this customer
                ci()->customer_m->update_by_many(array(
                    "customer_id" => $customer_id,
                    "activated_flag <> '0'" => null,
                        ), array(
                    "activated_flag" => APConstants::OFF_FLAG,
                    "deactivated_type" => APConstants::AUTO_INACTIVE_TYPE,
                    "deactivated_date" => now(),
                    "payment_detail_flag" => APConstants::OFF_FLAG,
                    "last_updated_date" => now()
                ));
                
                // update: convert registration process flag to customer_product_setting.
                CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'payment_detail_flag', APConstants::OFF_FLAG);

                // Send email trigger
                Events::trigger('deactivated_notifications', array(
                    'customer_id' => $customer_id
                        ), 'string');
            }
            return APConstants::ON_FLAG;
        } catch (Exception $e) {
            log_message($e->getMessage());
            return APConstants::ON_FLAG;
        }
    }

    /**
     * Update customer email.
     * 
     * @param unknown_type $customer_id            
     */
    public function update_customer_email($customer_id) {
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
    }

    /**
     * Make authorize request to test/dev portal of payone
     */
    public function test_authorize($customer_id, $invoice_id, $amount, $tran_id = '') {
        try {
            // Get customer information
            ci()->load->library('Payone_lib');
            ci()->load->config('payone');
            ci()->load->model('customers/customer_m');
            ci()->load->model('payment/payment_m');
            ci()->load->model('payment/payment_tran_hist_test_m');
            ci()->load->config('payone');
            $customer = ci()->customer_m->get_by('customer_id', $customer_id);

            if (empty($customer)) {
                log_message(APConstants::LOG_DEBUG, 'Customer information of customer id: ' . $customer_id . ' does not exist');
                return false;
            }

            if (abs($amount) < 0.1) {
                log_message(APConstants::LOG_DEBUG, 'Can not make payment less than 0.1 EUR for customer id: ' . $customer_id);
                return true;
            }

            $customer_name = $customer->user_name;

            // Check if customer already invoice code and not make payment for this customer
            if (!empty($customer->invoice_code)) {
                log_message(APConstants::LOG_DEBUG, 'Invoice code of customer id:' . $customer_id . ' already exist.');
                return true;
            }

            // Get card information
            $customer_payment = $this->get_credit_card($customer_id, $tran_id);
            if (empty($customer_payment)) {
                log_message(APConstants::LOG_DEBUG, 'Payment information of customer id:' . $customer_id . ' does not exist.');
                return false;
            }

            // Set reference
            $reference = sprintf('C%1$08d', $customer_id) . '_' . time();

            // Get config
            $merchant_id = Settings::get(APConstants::TEST_MERCHANT_ID_CODE);
            $portal_id = Settings::get(APConstants::TEST_PORTAL_ID_CODE);
            $portal_key = Settings::get(APConstants::TEST_PORTAL_KEY_CODE);
            $sub_account_id = Settings::get(APConstants::TEST_SUB_ACCOUNT_ID_CODE);
            $mode = "test";
            $encoding = "UTF-8";

            // Build service
            $bootstrap = new Payone_Bootstrap();
            $bootstrap->init();
            $builder = new Payone_Builder();
            $service = $builder->buildServicePaymentAuthorize();

            $request = new Payone_Api_Request_Authorization();
            $request->setPortalid($portal_id);
            $request->setMid($merchant_id);
            $request->setKey($portal_key);
            $request->setAid($sub_account_id);
            $request->setMode($mode);
            $request->setEncoding($encoding);
            $request->setAmount($amount);
            $request->setCurrency('EUR');
            $request->setClearingtype('cc');
            $request->setReference($reference);

            // Set person data
            $request_person_data = new Payone_Api_Request_Parameter_Authorization_PersonalData();
//            $request_person_data->setLastname($customer_name);
//            $request_person_data->setCountry('DE');
            
            $customerInfo = ci()->customer_m->getCustomerInfoForPayOne($customer_id);

            $request_person_data->setCustomerid(!empty($customerInfo->customer_code) ? $customerInfo->customer_code : '');
            $last_name = APUtils::removeSpecialCharacterForPayone($customerInfo->invoicing_address_name);
            $request_person_data->setLastname(!empty($last_name) ? $last_name : 'Christian Hemmrich');
            $company_name = APUtils::removeSpecialCharacterForPayone($customerInfo->invoicing_company);
            $request_person_data->setCompany(!empty($company_name) ? $company_name : '');
            $request_person_data->setCountry(!empty($customerInfo->country_code) ? $customerInfo->country_code : 'DE');
            $request_person_data->setEmail(!empty($customerInfo->email) ? $customerInfo->email : '');
            
            $request->setPersonalData($request_person_data);

            // Set credit card
            $request_credit_date = new Payone_Api_Request_Parameter_Authorization_PaymentMethod_CreditCard();
            $request_credit_date->setPseudocardpan($customer_payment->pseudocardpan);
            $request_credit_date->setEcommercemode('internet');
            $request->setPayment($request_credit_date);

            // Insert data to payment_tran_hist
            $tran_id = ci()->payment_tran_hist_test_m->insert(
                    array(
                        "customer_id" => $customer_id,
                        "tran_date" => now(),
                        "tran_type" => "authorize",
                        "pseudocardpan" => $customer_payment->pseudocardpan,
                        "amount" => $amount,
                        "ccy" => 'EUR',
                        "invoice_id" => $invoice_id,
                        "reference" => $reference
            ));

            $response = $service->authorize($request);
            if ($response->getStatus() != 'ERROR') {
                // Update status
                ci()->payment_tran_hist_test_m->update_by_many(array(
                    "id" => $tran_id
                        ), array(
                    "status" => $response->getStatus()
                ));
                return true;
            }

            // Update status
            ci()->payment_tran_hist_test_m->update_by_many(array(
                "id" => $tran_id
                    ), array(
                "status" => $response->getStatus(),
                "message" => $response->getErrormessage()
            ));
            return false;
        } catch (Exception $e) {
            log_message($e->getMessage());
            return false;
        }
    }

    /**
     * Default page for 404 error.
     */
    public function check_credit_card($card_number, $card_type, $cvc, $expired_year, $expired_month) {
        ci()->load->library('Payone_lib');
        ci()->load->config('payone');
        ci()->load->config('payone');

        // Get config
        $merchant_id = ci()->config->item('payone.merchant-id');
        $portal_id = ci()->config->item('payone.portal-id');
        $portal_key = ci()->config->item('payone.portal-key');
        $sub_account_id = ci()->config->item('payone.sub-account-id');
        $mode = ci()->config->item('payone.mode');
        $encoding = ci()->config->item('payone.encoding');

        // Get input parameter
        $expired = $expired_year . $expired_month;

        // Build service
        $bootstrap = new Payone_Bootstrap();
        $bootstrap->init();
        $builder = new Payone_Builder();
        $service = $builder->buildServiceVerificationCreditCardCheck();

        $request = new Payone_Api_Request_CreditCardCheck();
        $request->setPortalid($portal_id);
        $request->setMid($merchant_id);
        $request->setKey($portal_key);
        $request->setAid($sub_account_id);
        $request->setMode($mode);
        $request->setEncoding($encoding);
        $request->setStorecarddata("yes");
        $request->setLanguage("de");

        $request->setCardpan($card_number);
        $request->setCardcvc2($cvc);
        $request->setCardtype($card_type);
        $request->setCardexpiredate($expired);

        // Set Parameters here ...
        $response = $service->check($request);
        if (!empty($response) && $response->getStatus() === 'VALID') {
            return array(
                'status' => true,
                "message" => '',
                "pseudocardpan" => $response->getPseudocardpan(),
                "truncatedcardpan" => $response->getTruncatedcardpan()
            );
        }
        return array(
            'status' => false,
            "message" => $response->getCustomermessage()
        );
    }

    /**
     * Thanh toan chi phi cho tat ca cac thang dang bi no chi phi
     */
    public function make_pending_payment($customer_id, $tran_id = '') {
        // Get customer information
        ci()->load->library('payone');
        ci()->load->model('customers/customer_m');
        ci()->load->model('payment/payment_m');
        ci()->load->model('invoices/invoice_summary_m');
        ci()->load->model('payment/payment_tran_hist_m');

        // Get tat ca cac thang bi no chi phi
        // Get amount
        // #472: comment get vat.
        // $vat = APUtils::getVatFeeByCustomer($customer_id);
        // $vat_total = 1.19;
        // Customer information does not exist
        $customer = ci()->customer_m->get_by('customer_id', $customer_id);
        if (empty($customer)) {
            log_message(APConstants::LOG_DEBUG, 'Customer information of customer id: ' . $customer_id . ' does not exist');
            return false;
        }
        $open_balance = APUtils::getCurrentBalance($customer_id);
        $invoice_id = APUtils::genetateReferenceForOpenBalance($customer_id);

        // Make pending payment
        $payment_flag = true;
        if ($open_balance > 0) {
            $result = true;
            if ($open_balance > 0) {
                $result = ci()->payone->authorize($customer_id, $invoice_id, $open_balance, $tran_id);
            }
            if ($result) {
                // Update 2 st payment flag
                ci()->invoice_summary_m->update_by_many(array(
                    'customer_id' => $customer_id
                        ), array(
                    'invoice_flag' => '1',
                    'payment_1st_flag' => APConstants::ON_FLAG,
                    'payment_1st_amount' => 0,
                    'payment_2st_flag' => APConstants::ON_FLAG,
                    'payment_2st_amount' => 0,
                    "update_flag" => 0
                ));
                $payment_flag = true;
            } else {
                $payment_flag = false;
            }
        }
        return $payment_flag;
    }

    /**
     * Make all payment for open balance.
     * 
     * @param unknown_type $admin_user_id            
     */
    public function make_payment_for_open_balance($admin_user_id) {
        
    }

    /**
     * Get credit card by transaction id
     */
    private function get_credit_card($customer_id, $tran_id) {
        ci()->load->model('payment/payment_m');

        $array_condition = array();
        $array_condition['customer_id'] = $customer_id;
        if (!empty($tran_id)) {
            $array_condition['callback_tran_id'] = $tran_id;
        } else {
            $array_condition['primary_card'] = APConstants::ON_FLAG;
            $array_condition['card_confirm_flag'] = APConstants::ON_FLAG;
        }

        $customer_payments = ci()->payment_m->get_many_by_many($array_condition);
        if ((empty($customer_payments) || count($customer_payments) == 0) && empty($tran_id)) {
            log_message(APConstants::LOG_DEBUG, 'Customer payment information of customer id: ' . $customer_id . ' does not exist');
            // Try to get other card confirm flag
            $array_condition = array();
            $array_condition['customer_id'] = $customer_id;
            $array_condition['card_confirm_flag'] = APConstants::ON_FLAG;
            $customer_payments = ci()->payment_m->get_many_by_many($array_condition);
            if (empty($customer_payments) || count($customer_payments) == 0) {
                return null;
            }

            // Try to update primary card information
            ci()->payment_m->update_by_many(array(
                'customer_id' => $customer_id
                    ), array(
                'primary_card' => APConstants::OFF_FLAG
            ));

            $payment_id = $customer_payments[0]->payment_id;
            // Setting new primary card
            ci()->payment_m->update_by_many(
                    array(
                'customer_id' => $customer_id,
                'payment_id' => $payment_id
                    ), array(
                'primary_card' => APConstants::ON_FLAG
            ));
        }
        $customer_payment = $customer_payments[0];

        return $customer_payment;
    }

    /**
     * Get credit card by transaction id
     */
    private function get_credit_card_by_card_id($customer_id, $tran_id, $credit_card_id) {
        ci()->load->model('payment/payment_m');

        $array_condition = array();
        $array_condition['customer_id'] = $customer_id;
        $array_condition['payment_id'] = $credit_card_id;
        $array_condition['card_confirm_flag'] = APConstants::ON_FLAG;

        $customer_payments = ci()->payment_m->get_many_by_many($array_condition);
        if (empty($customer_payments) || count($customer_payments) == 0) {
            log_message(APConstants::LOG_DEBUG, 'Customer payment information of customer id: ' . $customer_id . ' does not exist');
            return null;
        }
        $customer_payment = $customer_payments[0];

        return $customer_payment;
    }

}
