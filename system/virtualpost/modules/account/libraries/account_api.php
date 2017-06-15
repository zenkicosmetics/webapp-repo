<?php

defined('BASEPATH') or exit('No direct script access allowed');

class account_api {

    const DURATION_MONTHS = 'months';
    const DURATION_DAYS = 'days';

    public function __construct()
    {
        ci()->load->model(array(
            'mailbox/postbox_m',
            'mailbox/postbox_setting_m'

        ));

    }

    public static function isActiveAccount($customer) {
        ci()->lang->load('customers/customer');
        ci()->load->library('customers/customers_api');

        $customerStatus = customers_api::getCustomerStatus($customer);

        return ($customerStatus == lang('customer.activated'));
    }

    public static function isDeletedAccount($customer) {
        ci()->lang->load('customers/customer');
        ci()->load->library('customers/customers_api');

        $customerStatus = customers_api::getCustomerStatus($customer);

        return ($customerStatus == lang('customer.status.deleted'));
    }

    public static function isInactiveAccount($customer) {
        return !self::isActiveAccount($customer) && !self::isDeletedAccount($customer);
    }

    /**
     * Get duration of account creation (the number of months)
     */
    public static function getAccountDuration($created_date) {
        $months = 0;
        if ($created_date) {
            $ts1 = strtotime(date('Y-m-d', $created_date));
            $ts2 = strtotime(date('Y-m-d'));
            $year1 = date('Y', $ts1);
            $year2 = date('Y', $ts2);
            $month1 = date('m', $ts1);
            $month2 = date('m', $ts2);
            $months = (($year2 - $year1) * 12) + ($month2 - $month1) + 1;
        }

        return $months;
    }

    /**
     * Get the number of deactivated days for the account
     */
    public static function getAccountInactiveDays($deactivated_date) {
        $inactiveDays = 0;
        if ($deactivated_date) {
            $ts1 = strtotime(date('Y-m-d', $deactivated_date));
            $ts2 = strtotime(date('Y-m-d'));
            $day1 = date('d', $ts1);
            $day2 = date('d', $ts2);
            $inactiveDays = ($day2 - $day1) + 1;
        }

        return $inactiveDays;
    }

    public static function addPostbox($customer, $account_type, $location_id, $name, $company, $postname) {

        ci()->load->library('mailbox/mailbox_api');
        ci()->load->library('customers/customers_api');
        $customer_id = $customer->customer_id;
        $primary_location = APUtils::getPrimaryLocationBy($customer_id);
        if(empty($primary_location)){
            $primary_location = $location_id;
        }

        $main_location_id = $location_id;
        if ($customer->account_type == APConstants::NORMAL_CUSTOMER &&  $account_type != APConstants::BUSINESS_TYPE) {
            $main_location_id = $primary_location;
        }

        $apply_date = null;
        $first_location_flag = APConstants::ON_FLAG;
        $first_location_postbox = mailbox_api::getPostboxWithFirstLocation($customer_id, $first_location_flag);

        if(!empty($first_location_postbox)){
            $first_location = $first_location_postbox->location_available_id;
        }else{
            $first_location = $location_id;
        }
        $first_flag = 0;
        if ($main_location_id == $first_location) {
            $first_flag = 1;
        }

        $updated_date = time();
        $created_date = time();
        $name_verification_flag = APConstants::ON_FLAG;
        $company_verification_flag = APConstants::ON_FLAG;
        $dataNames = array('customer_id', 'location_available_id', 'postbox_name', 'type', 'name', 'company', 'apply_date', "first_location_flag", 'updated_date', "created_date", "name_verification_flag", "company_verification_flag");
        $dataValues = array($customer_id, $main_location_id, $postname, $account_type, $name, $company, $apply_date, $first_flag, $updated_date, $created_date, $name_verification_flag, $company_verification_flag);
        $postbox_id = mailbox_api::createPostbox($dataNames, $dataValues);

        /*
         * #1180 create postbox history page like check item page
         * Insert new postbox into postbox_history_activity table (created)
         */
        customers_api::addPostboxHistory($postbox_id, APConstants::POSTBOX_CREATE, $account_type);
        // CustomerUtils::actionPostboxHistoryActivity($postbox_id, APConstants::POSTBOX_CREATE, $created_date, $account_type, APConstants::INSERT_POSTBOX);

        APUtils::updateAccountType($customer_id);

        MailUtils::sendEmailWhenAddPostBox($postbox_id, $account_type, $customer_id, $location_id);
        mailbox_api::createPostboxSetting($customer_id, $postbox_id);

        $tmp_postbox = mailbox_api::getPostBoxByID($postbox_id);
        CaseUtils::start_case_verification_by_postbox(true, $customer, $tmp_postbox);

        Events::trigger('cal_postbox_invoices_directly', array(
            'customer_id' => $customer_id
                ), 'string');

        return $postbox_id;
    }

    public static function main_postbox_setting(){
        ci()->load->library('shipping/shipping_api');

        $customer_id = APContext::getCustomerCodeLoggedIn();
        if(APContext::isPrimaryCustomerUser()){
            $list_customer_ids = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($customer_id);
            $list_customer_ids[] = $customer_id;
            // Get all postbox of this customer
            $postboxes = ci()->postbox_m->get_many_by_many(array(
                "customer_id IN (".  implode(",", $list_customer_ids).")" => null,
                "deleted <> " => '1',
                "(postbox_name IS NOT NULL AND postbox_name !='')" => null,
                "((name IS NOT NULL AND name != '') OR (company IS NOT NULL AND company != ''))" => null
            ));
        }else{
            // Get all postbox of this customer
            $postboxes = ci()->postbox_m->get_many_by_many(array(
                "customer_id" => $customer_id,
                "deleted <> " => '1',
                "(postbox_name IS NOT NULL AND postbox_name !='')" => null,
                "((name IS NOT NULL AND name != '') OR (company IS NOT NULL AND company != ''))" => null
            ));
        }

        // Get main postbox id
        $main_postbox_id = 0;
        foreach ($postboxes as $postbox) {
            if ($postbox->is_main_postbox === '1') {
                $main_postbox_id = $postbox->postbox_id;
                break;
            }
        }

        // Load postbox setting
        $main_postbox_setting = null;
        if ($main_postbox_id > 0) {
            $main_postbox_setting = ci()->postbox_setting_m->get_by_many(array(
                "postbox_id" => $main_postbox_id,
                "customer_id" => $customer_id
            ));
            if (!$main_postbox_setting) {
                $main_postbox_setting = new stdClass();
                $main_postbox_setting->always_scan_envelope = 0;
                $main_postbox_setting->always_scan_envelope_vol_avail = 0;
                $main_postbox_setting->always_scan_incomming = 0;
                $main_postbox_setting->always_scan_incomming_vol_avail = 0;
                $main_postbox_setting->email_notification = 0;
                $main_postbox_setting->invoicing_cycle = 0;
                $main_postbox_setting->collect_mail_cycle = 2;
                $main_postbox_setting->weekday_shipping = 0;
                $main_postbox_setting->email_scan_notification = 0;
                $main_postbox_setting->always_forward_directly = 0;
                $main_postbox_setting->always_forward_collect = 0;
                $main_postbox_setting->inform_email_when_item_trashed = 0;
                $main_postbox_setting->always_mark_invoice = 0;
                $main_postbox_setting->standard_service_national_letter = 0;
                $main_postbox_setting->standard_service_international_letter = 0;
                $main_postbox_setting->standard_service_national_package = 0;
                $main_postbox_setting->standard_service_international_package = 0;
            }

            $standard_shipping_services = shipping_api::get_standard_shipping_services_by_postbox($main_postbox_id);
            $main_postbox_setting->standard_service_national_letter = empty($main_postbox_setting->standard_service_national_letter) ? $standard_shipping_services['standard_service_national_letter'] : $main_postbox_setting->standard_service_national_letter;
            $main_postbox_setting->standard_service_international_letter = empty($main_postbox_setting->standard_service_international_letter) ? $standard_shipping_services['standard_service_international_letter'] : $main_postbox_setting->standard_service_international_letter;
            $main_postbox_setting->standard_service_national_package = empty($main_postbox_setting->standard_service_national_package) ? $standard_shipping_services['standard_service_national_package'] : $main_postbox_setting->standard_service_national_package;
            $main_postbox_setting->standard_service_international_package = empty($main_postbox_setting->standard_service_international_package) ? $standard_shipping_services['standard_service_international_package'] : $main_postbox_setting->standard_service_international_package;

            //Get list available services by postbox
            $shipping_services = shipping_api::get_shipping_services_by_postbox($main_postbox_id);

            $main_postbox_setting->standard_service_national_letter_dropdownlist = shipping_api::filterListShippingServices($shipping_services, APConstants::ENVELOPE_TYPE_LETTER, array(0 , 1));
            $main_postbox_setting->standard_service_international_letter_dropdownlist = shipping_api::filterListShippingServices($shipping_services, APConstants::ENVELOPE_TYPE_LETTER, array(0 , 2));
            $main_postbox_setting->standard_service_national_package_dropdownlist = shipping_api::filterListShippingServices($shipping_services, APConstants::ENVELOPE_TYPE_PACKAGE, array(0 , 1));
            $main_postbox_setting->standard_service_international_package_dropdownlist = shipping_api::filterListShippingServices($shipping_services, APConstants::ENVELOPE_TYPE_LETTER, array(0 , 2));

            $main_postbox_setting->accounting_email = EnvelopeUtils::get_accounting_interface_by_postbox($main_postbox_id)['email'];

        }

        return array(
            "postboxes"            => $postboxes,
            "main_postbox_setting" => $main_postbox_setting,
            "main_postbox_id"      => $main_postbox_id
        );

    }

    /**
     * change email from customer.
     *
     * @param type $customer_id
     * @param type $new_email
     */
    public static function change_account_email($customer_id, $new_email, $history_list = array()){
        ci()->load->model("customers/customer_m");
        ci()->load->model('email/email_m');

        // Gets random key.
        $activated_key = APUtils::generateRandom(30);

        // Insert new customer
        ci()->customer_m->update_by_many(array(
            "customer_id" => $customer_id
        ), array(
            "email" => $new_email,
            "activated_key" => $activated_key,
            "deactivated_type" => APConstants::AUTO_INACTIVE_TYPE,
            "deactivated_date" => time(),
            "email_confirm_flag" => APConstants::OFF_FLAG,
            "activated_flag" => APConstants::OFF_FLAG,
        ));
        CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'email_confirm_flag', APConstants::OFF_FLAG);
        customers_api::insertCustomerHistory($history_list);
        // send email confirmation.
        $url = APContext::getFullBalancerPath().'customers/confirm_new_email?key='.  $activated_key;
        $data = array(
            "slug" => APConstants::new_email_customer_confirmation,
            "to_email" => $new_email,
            // Replace content
            "active_url" => $url
        );
        // Send email
        MailUtils::sendEmailByTemplate($data);
        return true;
    }

    /**
     * Save invoicing address.
     * @param array $data_params
     */
    public static function save_invoicing_address($customer_id, array $data_params){
        ci()->load->model(array(
            'customers/customers_address_m',
            'customers/customer_m',
            "settings/countries_m"
        ));

        // Gets customer address infor.
        $customer_check = ci()->customers_address_m->get_by('customer_id', $customer_id);

        try{
            if ($customer_check) {
                // Get country entity
                $invoicing_country_entity = ci()->countries_m->get($data_params['invoicing_country']);

                // update address information.
                $data = array(
                    'invoicing_address_name' => $data_params['invoicing_address_name'],
                    'invoicing_company' => $data_params['invoicing_company'],
                    'invoicing_street' => $data_params['invoicing_street'],
                    'invoicing_postcode' => $data_params['invoicing_postcode'],
                    'invoicing_city' => $data_params['invoicing_city'],
                    'invoicing_region' => $data_params['invoicing_region'],
                    'invoicing_country' => $data_params['invoicing_country'],
                    'invoicing_phone_number' => $data_params['invoicing_phone_number'],
                    'eu_member_flag' => $invoicing_country_entity->eu_member_flag
                );

                // Ticket #361 (If the customer changes the invoicing company name
                // the VAT check has to be redone, therefore the sign erased.
                if ($data_params['invoicing_company'] != $customer_check->invoicing_company) {
                    // Update VAT sign (reset vat number in customers)
                    ci()->customer_m->update_by_many(array(
                        "customer_id" => $customer_id
                    ), array(
                        "vat_number" => ''
                    ));

                    // Reload customers information
                    APContext::reloadCustomerLoggedIn();
                }

                // Ticket #563 Case Management: check address verification case
                if (!empty($customer_check) && ($customer_check->invoicing_company != $data['invoicing_company'])) {
                    $list_case_number = APUtils::get_list_case_invoice_address($customer_id);
                    if (count($list_case_number) > 0) {
                        // Check if this customer already change, we need to reset invoice_address_verification_flag = 0
                        // that mean the system need to verification address again
                        if (!empty($customer_check) && ($customer_check->invoicing_company != $data['invoicing_company'])) {
                            $data['invoice_address_verification_flag'] = APConstants::OFF_FLAG;
                        }
                    }
                }

                // End fix ticket #563
                ci()->customers_address_m->update($customer_id, $data);

                // Ticket #563 Case Management: trigger address case verification.
                if (!empty($customer_check) && ($customer_check->invoicing_company != $data['invoicing_company']
                        || $customer_check->invoicing_address_name != $data['invoicing_address_name']
                        || $customer_check->invoicing_street != $data['invoicing_street']
                        || $customer_check->invoicing_postcode != $data['invoicing_postcode']
                        || $customer_check->invoicing_city != $data['invoicing_city']
                        || $customer_check->invoicing_region != $data['invoicing_region']
                        || $customer_check->invoicing_country != $data['invoicing_country']
                        || $customer_check->invoicing_phone_number != $data['invoicing_phone_number'])
                ) {
                    $customer = ci()->customer_m->get($customer_id);
                    CaseUtils::start_case_verification_by_postbox(true, $customer, '');
                }
                // End fix ticket #563
                // Update data to customer
                ci()->customer_m->update_by_many(array(
                    "customer_id" => $customer_id
                ), array(
                    "invoicing_address_completed" => APConstants::ON_FLAG
                ));

                // update: convert registration process flag to customer_product_setting.
                CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, APConstants::activate_invoicing_address_completed, APConstants::ON_FLAG);

                // activate customer.
                /*ci()->customer_m->update_by_many(array(
                    "customer_id" => $customer_id,
                    "shipping_address_completed" => APConstants::ON_FLAG,
                    "invoicing_address_completed" => APConstants::ON_FLAG,
                    "postbox_name_flag" => APConstants::ON_FLAG,
                    "name_comp_address_flag" => APConstants::ON_FLAG,
                    "city_address_flag" => APConstants::ON_FLAG,
                    "payment_detail_flag" => APConstants::ON_FLAG,
                    "email_confirm_flag" => APConstants::ON_FLAG
                ), array(
                    "activated_flag" => APConstants::ON_FLAG
                ));*/

                // update: convert registration process flag to customer_product_setting.
                $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;
                CustomerProductSetting::doActiveCustomer($customer_id, $created_by_id);
            } else {
                // Get country entity
                // $shipment_country_entity =
                $invoicing_country_entity = ci()->countries_m->get($data_params['invoicing_country']);
                $eu_member_flag = 0;
                if ($invoicing_country_entity) {
                    $eu_member_flag = $invoicing_country_entity->eu_member_flag;
                }
                // insert new address information.
                $data = array(
                    'customer_id' => $customer_id,
                    'invoicing_address_name' => $data_params['invoicing_address_name'],
                    'invoicing_company' => $data_params['invoicing_company'],
                    'invoicing_street' => $data_params['invoicing_street'],
                    'invoicing_postcode' => $data_params['invoicing_postcode'],
                    'invoicing_city' => $data_params['invoicing_city'],
                    'invoicing_region' => $data_params['invoicing_region'],
                    'invoicing_country' => $data_params['invoicing_country'],
                    'invoicing_phone_number' => $data_params['invoicing_phone_number'],
                    'invoice_address_verification_flag' => APConstants::ON_FLAG,
                    'eu_member_flag' => $eu_member_flag
                );
                ci()->customers_address_m->insert($data);

                // Update data to customer
                ci()->customer_m->update_by_many(array(
                    "customer_id" => $customer_id
                ), array(
                    "invoicing_address_completed" => APConstants::ON_FLAG
                ));
                // update: convert registration process flag to customer_product_setting.
                CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'invoicing_address_completed', APConstants::OFF_FLAG);

                // Update customer information
                /*ci()->customer_m->update_by_many(array(
                    "customer_id" => $customer_id,
                    "shipping_address_completed" => APConstants::ON_FLAG,
                    "invoicing_address_completed" => APConstants::ON_FLAG,
                    "postbox_name_flag" => APConstants::ON_FLAG,
                    "name_comp_address_flag" => APConstants::ON_FLAG,
                    "city_address_flag" => APConstants::ON_FLAG,
                    "payment_detail_flag" => APConstants::ON_FLAG,
                    "email_confirm_flag" => APConstants::ON_FLAG
                ), array(
                    "activated_flag" => APConstants::ON_FLAG
                ));*/

                // update: convert registration process flag to customer_product_setting.
                $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;
                CustomerProductSetting::doActiveCustomer($customer_id, $created_by_id);
            }
        } catch(Exception $e){
            throw new BusinessException($e->getMessage());
        }
        return true;
    }

    /**
     * Save main forwarding address.
     * @param array $data_params
     */
    public static function save_address($customer_id, array $data_params){
        ci()->load->model(array(
            'customers/customers_address_m',
            'customers/customer_m',
            "settings/countries_m"
        ));

        // Gets customer address infor.
        $customer_check = ci()->customers_address_m->get_by('customer_id', $customer_id);

        try{
            if ($customer_check) {
                // update address information.
                $data = array(
                    'shipment_address_name' => $data_params['shipment_address_name'],
                    'shipment_company' => $data_params['shipment_company'],
                    'shipment_street' => $data_params['shipment_street'],
                    'shipment_postcode' => $data_params['shipment_postcode'],
                    'shipment_city' => $data_params['shipment_city'],
                    'shipment_region' => $data_params['shipment_region'],
                    'shipment_country' => $data_params['shipment_country'],
                    'shipment_phone_number' => $data_params['shipment_phone_number']
                );

                // save address information.
                ci()->customers_address_m->update($customer_id, $data);

                // Update data to customer
                ci()->customer_m->update_by_many(array(
                    "customer_id" => $customer_id
                ), array(
                    "shipping_address_completed" => APConstants::ON_FLAG
                ));

                // activate customer.
                /*ci()->customer_m->update_by_many(array(
                    "customer_id" => $customer_id,
                    "shipping_address_completed" => APConstants::ON_FLAG,
                    "invoicing_address_completed" => APConstants::ON_FLAG,
                    "postbox_name_flag" => APConstants::ON_FLAG,
                    "name_comp_address_flag" => APConstants::ON_FLAG,
                    "city_address_flag" => APConstants::ON_FLAG,
                    "payment_detail_flag" => APConstants::ON_FLAG,
                    "email_confirm_flag" => APConstants::ON_FLAG
                ), array(
                    "activated_flag" => APConstants::ON_FLAG
                ));*/

                // update: convert registration process flag to customer_product_setting.
                $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;
                CustomerProductSetting::doActiveCustomer($customer_id, $created_by_id);
            } else {
                // insert new address information.
                $data = array(
                    'customer_id' => $customer_id,
                    'shipment_address_name' => $data_params['shipment_address_name'],
                    'shipment_company' => $data_params['shipment_company'],
                    'shipment_street' => $data_params['shipment_street'],
                    'shipment_postcode' => $data_params['shipment_postcode'],
                    'shipment_city' => $data_params['shipment_city'],
                    'shipment_region' => $data_params['shipment_region'],
                    'shipment_country' => $data_params['shipment_country'],
                    'shipment_phone_number' => $data_params['shipment_phone_number'],
                    'invoice_address_verification_flag' => APConstants::ON_FLAG,
                );
                ci()->customers_address_m->insert($data);

                // Update data to customer
                ci()->customer_m->update_by_many(array(
                    "customer_id" => $customer_id
                ), array(
                    "invoicing_address_completed" => APConstants::ON_FLAG
                ));
                // update: convert registration process flag to customer_product_setting.
                CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'invoicing_address_completed', APConstants::ON_FLAG);

                // Update customer information
                /*ci()->customer_m->update_by_many(array(
                    "customer_id" => $customer_id,
                    "shipping_address_completed" => APConstants::ON_FLAG,
                    "invoicing_address_completed" => APConstants::ON_FLAG,
                    "postbox_name_flag" => APConstants::ON_FLAG,
                    "name_comp_address_flag" => APConstants::ON_FLAG,
                    "city_address_flag" => APConstants::ON_FLAG,
                    "payment_detail_flag" => APConstants::ON_FLAG,
                    "email_confirm_flag" => APConstants::ON_FLAG
                ), array(
                    "activated_flag" => APConstants::ON_FLAG
                ));*/

                // update: convert registration process flag to customer_product_setting.
                $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;
                CustomerProductSetting::doActiveCustomer($customer_id, $created_by_id);

            }
        } catch(Exception $e){
            throw new BusinessException($e->getMessage());
        }
        return true;
    }

    /**
     * Add postbox to user
     *
     * @param type $parent_customer_id
     * @param type $customer_id
     */
    public static function unassign_postbox_to_user($parent_customer_id, $customer_id){
        ci()->load->model("customers/postbox_customer_user_m");
        ci()->load->model("mailbox/postbox_m");

        // Delete old one
        ci()->postbox_customer_user_m->delete_by_many(array(
            'parent_customer_id' => $parent_customer_id,
            'customer_id' => $customer_id
        ));

        // Change customer id from parent_customer_id to customer_id
        ci()->postbox_m->update_by_many(array(
            'customer_id' => $customer_id
        ), array(
            'customer_id' => $parent_customer_id
        ));
    }

    /**
     * Add postbox to user
     *
     * @param type $customer_id
     * @param type $new_email
     */
    public static function add_postbox_to_user($parent_customer_id, $customer_id, $postbox_id){
        ci()->load->model("customers/postbox_customer_user_m");
        ci()->load->model("mailbox/postbox_m");

        // Check exist data
        $postbox_user_check = ci()->postbox_customer_user_m->get_by_many(array(
            'customer_id' => $customer_id,
            'parent_customer_id' => $parent_customer_id,
            'postbox_id' => $postbox_id
        ));
        if (empty($postbox_user_check)) {
            // Delete old one
            ci()->postbox_customer_user_m->delete_by_many(array(
                'parent_customer_id' => $parent_customer_id,
                'postbox_id' => $postbox_id
            ));

            // Insert new data
            ci()->postbox_customer_user_m->insert(array(
                'customer_id' => $customer_id,
                'parent_customer_id' => $parent_customer_id,
                'postbox_id' => $postbox_id
            ));
            // if ($parent_customer_id != $customer_id) {
            //     customers_api::addPostboxHistory($postbox_id, APConstants::POSTBOX_DELETE_ORDER_BY_CUSTOMER, $postbox_type);
            // }
            // Change customer id from parent_customer_id to customer_id
            ci()->postbox_m->update_by_many(array(
                'postbox_id' => $postbox_id,
                'customer_id' => $parent_customer_id
            ), array(
                'customer_id' => $customer_id
            ));
            // Reassign postbox to child customer
            /* 1080
            * parent delete postbox reassgn to new customer
            * Child customer: create postbox
            */
            // customers_api::addPostboxHistory($postbox_id, APConstants::POSTBOX_CREATE, $postbox_type);
        }
    }
    /**
     * Delete postbox to user
     *
     * @param type $customer_id
     */
    public static function delete_postbox_to_user_byid($parent_customer_id, $postbox_user_id){
        ci()->load->model("customers/postbox_customer_user_m");
        ci()->load->model("mailbox/postbox_m");

        // Check exist data
        $postbox_user_check = ci()->postbox_customer_user_m->get_by_many(array(
            'parent_customer_id' => $parent_customer_id,
            'id' => $postbox_user_id
        ));

        if (!empty($postbox_user_check)) {
            ci()->postbox_customer_user_m->delete_by_many(array(
                'parent_customer_id' => $parent_customer_id,
                'id' => $postbox_user_id
            ));

            // Change customer id from customer_id to parent_customer_id
            ci()->postbox_m->update_by_many(array(
                'postbox_id' => $postbox_user_check->postbox_id
            ), array(
                'customer_id' => $parent_customer_id
            ));
        }
    }

    /**
     * Delete postbox to user
     *
     * @param type $customer_id
     */
    public static function get_postbox_by_postboxuser_id($parent_customer_id, $postbox_user_id){
        ci()->load->model("customers/postbox_customer_user_m");
        ci()->load->model("mailbox/postbox_m");

        // Check exist data
        $postbox_user_check = ci()->postbox_customer_user_m->get_by_many(array(
            'parent_customer_id' => $parent_customer_id,
            'id' => $postbox_user_id
        ));

        if (!empty($postbox_user_check)) {
            // Change customer id from customer_id to parent_customer_id
            return ci()->postbox_m->get_by_many(array(
                'postbox_id' => $postbox_user_check->postbox_id
            ));
        }
        return null;
    }

    /**
     * Delete postbox to user
     *
     * @param type $customer_id
     */
    public static function get_postbox_by_postbox_id($parent_customer_id, $postbox_id){
        ci()->load->model("customers/postbox_customer_user_m");
        ci()->load->model("mailbox/postbox_m");

        // Check exist data
        $postbox_user_check = ci()->postbox_customer_user_m->get_by_many(array(
            'parent_customer_id' => $parent_customer_id,
            'postbox_id' => $postbox_id
        ));

        if (!empty($postbox_user_check)) {
            // Change customer id from customer_id to parent_customer_id
            return ci()->postbox_m->get_by_many(array(
                'postbox_id' => $postbox_user_check->postbox_id
            ));
        }
        return null;
    }

    /**
     * Delete postbox to user
     *
     * @param type $customer_id
     */
    public static function delete_postbox_to_user($parent_customer_id, $customer_id, $postbox_id){
        ci()->load->model("customers/postbox_customer_user_m");

        // Check exist data
        $postbox_user_check = ci()->postbox_customer_user_m->get_by_many(array(
            'customer_id' => $customer_id,
            'parent_customer_id' => $parent_customer_id,
            'postbox_id' => $postbox_id
        ));
        if (!empty($postbox_user_check)) {
            ci()->postbox_customer_user_m->delete_by_many(array(
                'customer_id' => $customer_id,
                'parent_customer_id' => $parent_customer_id,
                'postbox_id' => $postbox_id
            ));
        }
    }

    /**
     * Get list product by user id.
     *
     * @param type $customer_id
     * @param type $user_id
     */
    public static function get_list_product($parent_customer_id, $customer_id, $product_type) {
        ci()->load->model("customers/customer_user_m");
        ci()->load->model("phones/phone_number_m");
        $list_product = array();
        if ($product_type == 'all' || $product_type == 'postbox') {
            $list_postbox = ci()->customer_user_m->get_list_postbox_byuser($parent_customer_id, $customer_id);
            if (!empty($list_postbox)) {
                foreach ($list_postbox as $item) {
                    $product_name = 'Postbox '.$item->location_name;
                    $list_product[] = $product_name;
                }
            }

        }

        // Get list phonenumber
        if ($product_type == 'all' || $product_type == 'phone') {
            $list_phonenumber = ci()->phone_number_m->get_list_phonenumber_by_user($parent_customer_id, $customer_id);
            if (!empty($list_phonenumber)) {
                 foreach ($list_phonenumber as $item) {
                    $product_name = 'PhoneNumber '.$item->phone_number;
                    $list_product[] = $product_name;
                }
            }
        }
        return implode(", ", $list_product);
    }

    /**
     * Upgrade account type. (1: Normal | 2: Enterprise)
     *
     * $separatePostboxType = 1 - Do you want to add all postboxes for the first user?
     * $separatePostboxType = 2 - Do you want to make an individual user out of every current postbox?
     * @param type $customer_type
     */
    public static function upgradeCustomerType($customer_id, $account_type, $separatePostboxType = '1', $setup_flag = 0) {
        ci()->load->model("customers/customer_m");
        ci()->load->model('mailbox/postbox_m');
        ci()->load->library('invoices/invoices_api');
        $customer = ci()->customer_m->get_by('customer_id', $customer_id);
        if (empty($customer)) {
            return false;
        }

        // Only upgrade (can not downgrade)
        $current_account_type = $customer->account_type;
        if ($current_account_type >= $account_type) {
            //return false;
        }

        // Update customer type
        ci()->customer_m->update_by_many(array('customer_id' => $customer_id), array(
            'account_type' => $account_type,
            'role_flag' => '1', // Admin
            'last_modified_date' => now()
        ));

        // If upgrade account type from standard to enterprise
        if ($account_type == APConstants::ENTERPRISE_CUSTOMER) {
            // Update all currency postbox to enterprise postbox (type = 5)
            ci()->postbox_m->update_by_many(array(
                'customer_id' => $customer_id,
                'deleted' => APConstants::OFF_FLAG,
            ), array(
                'type' => APConstants::ENTERPRISE_TYPE,
                'updated_date' => now(),
                "apply_date" => null,
                "plan_date_change_postbox_type" => null,
                "new_postbox_type" => null
            ));
           /*
           * 1080 save activity upgrade postbox
           */
           $postboxes = ci()->postbox_m->get_many_by('customer_id', $customer_id);
           if (!empty($postboxes)) {
               foreach ($postboxes as $curr_postbox) {
                   // Check postbox is not deleted
                   if (!empty($curr_postbox) &&
                           ( $curr_postbox->deleted == '0' || ($curr_postbox->deleted == '0' && $curr_postbox->completed_delete_flag == '0'))) {
                        if ($setup_flag) {
                            $array_where = array(
                                "customer_id" => $customer_id,
                                "postbox_id" => $curr_postbox->postbox_id
                            );
                            CustomerUtils::actionPostboxHistoryActivity($array_where,'', '', '', APConstants::UPDATE_POSTBOX);
                        } else {
                            customers_api::addPostboxHistory($curr_postbox->postbox_id, APConstants::POSTBOX_UPGRADE, APConstants::ENTERPRISE_TYPE);
                        }
                   }
               }
           }
            // Add default user
            account_api::addDefaultUserForEnterpriseCustomer($customer_id, $separatePostboxType);

            // Recalculate postbox fee
            invoices_api::calPostboxInvoicesOfEnterpriseCustomer($customer_id);

            // insert default term & condition of enterprise customer.
            settings_api::insertDefaultTermAndConditionOfEnterprise($customer_id);
        }

        // reload session.
        APContext::reloadCustomerLoggedIn();
    }

    /**
     * Add default 10 place holder user for enterprise customer
     * @param type $customer_id
     */
    public static function addDefaultUserForEnterpriseCustomer($parent_customer_id, $separatePostboxType = '1') {
        ci()->load->model('customers/customer_user_m');
        ci()->load->model('mailbox/postbox_m');
        ci()->load->model('mailbox/postbox_setting_m');
        ci()->load->model('addresses/customers_address_m');

        ci()->load->library('customers/customers_api');

        // Count total customer user
        $total_user = ci()->customer_user_m->count_by_many(array(
           'parent_customer_id' =>  $parent_customer_id
        ));
        if ($total_user >= 9) {
            return false;
        }
        $parent_customer = CustomerUtils::getCustomerByID($parent_customer_id);
        $email = "dump@clevvermail.com";
        $name = "dump";
        $password = $parent_customer_id."@1";//APUtils::generateRandom(10);
        $language =  "English";
        $currency_id = "1"; // EUR
        $decimal_separator = ",";
        $date_format = "m/d/Y";
        $role_flag = "0"; // 0: User | 1: Admin
        $activated_key = APUtils::generateRandom(30);
        $invoice_code = APUtils::generateRandom(30);

        $data = array(
            "customer_id" => "",
            "account_type" => APConstants::ENTERPRISE_CUSTOMER,
            "parent_customer_id" => $parent_customer_id,
            "user_name" => $name,
            "email" => $email,
            "password" => md5($password),
            "language" => $language,
            "currency_id" => $currency_id,
            "decimal_separator" => $decimal_separator,
            "date_format" => $date_format,
            "role_flag" => $role_flag,
            "charge_fee_flag" => APConstants::ON_FLAG,
            "shipping_address_completed" => APConstants::ON_FLAG,
            "invoicing_address_completed" => APConstants::ON_FLAG,
            "postbox_name_flag" => APConstants::OFF_FLAG,
            "name_comp_address_flag" => APConstants::OFF_FLAG,
            "city_address_flag" => APConstants::ON_FLAG,
            "invoice_type" => '2',
            "email_confirm_flag" => APConstants::ON_FLAG,
            "payment_detail_flag" => APConstants::ON_FLAG,
            "request_confirm_flag" => APConstants::ON_FLAG,
            "invoice_code" => $invoice_code,
            "activated_key" => $activated_key,
            "accept_terms_condition_flag" => $parent_customer->accept_terms_condition_flag,
            "created_date" => time(),
            "required_verification_flag" => $parent_customer->required_verification_flag,
            "required_prepayment_flag" => APConstants::OFF_FLAG,
            "charge_fee_flag" => $parent_customer->charge_fee_flag
        );

        // Get all postbox of current user.
        $list_postbox = ci()->postbox_m->get_list_postboxes($parent_customer_id);
        $current_postbox_count = count($list_postbox);
        $first_user_id = '';

        // calculate the number of users that created
        $number_created_users = 9;
        if($separatePostboxType == 1 ){
            $number_created_users = 9;
        }else{
            // neu so luong postbox > 10 thi tao tuong ung 1 user - 1 postbox
            if($current_postbox_count > 10){
                $number_created_users = $current_postbox_count - 1;
            }
        }

        // Add 10 users to place holder
        for ($i = $total_user; $i < $number_created_users; $i++) {
            $activated_key = APUtils::generateRandom(30);

            // save customer user.
            $data['customer_id'] = '';
            $data['activated_key'] = $activated_key;
            $data['user_name'] = $name;
            $data['email'] = $email;
            $invoice_code = APUtils::generateRandom(30);
            $data['invoice_code'] = $invoice_code;
            $customer_id = customers_api::updateCustomerUser($data);

            // update customer product setting..
            CustomerProductSetting::set_many($customer_id, APConstants::CLEVVERMAIL_PRODUCT, array(
                "shipping_address_completed",
                "invoicing_address_completed",
                "postbox_name_flag",
                "name_comp_address_flag",
                "city_address_flag",
                "email_confirm_flag",
                "payment_detail_flag",
                "accept_terms_condition_flag"
            ), array(
                $data["shipping_address_completed"],
                $data["invoicing_address_completed"],
                $data["postbox_name_flag"],
                $data["name_comp_address_flag"],
                $data["city_address_flag"],
                $data["email_confirm_flag"],
                $data["payment_detail_flag"],
                $data["accept_terms_condition_flag"]
            ));

            $new_name = $customer_code = sprintf('C%1$08d', $customer_id);
            $new_email = $new_name.'@clevvermail.com';

            $updated_data = $data;
            $updated_data['customer_id'] = $customer_id;
            $updated_data['user_name'] = $new_name;
            $updated_data['email'] = $new_email;
            customers_api::updateCustomerUser($updated_data);
            if ($i == 0) {
                $first_user_id = $customer_id;
            }

            $create_new_postbox_flag = true;
            $postbox_id = 0;
            // Add new postbox of Berlin location for each new customer
            // Insert default postbox
            if ($separatePostboxType == '1') {
                $create_new_postbox_flag = true;
            } else {
                // Distribute the current postbox for all account
                // Try to get the postbox id from the current list postbox
                // First postbox will assign for parent customer (enterprise customer user)
                if ($i + 1 < $current_postbox_count) {
                    //$postbox_id = $list_postbox[$i + 1]->postbox_id;
                    // reassign postbox to new user.
                    $postbox_id = account_api::reassignPostboxToUser($list_postbox[$i + 1]->postbox_id, $customer_id, $parent_customer_id);

                    if($postbox_id == $list_postbox[$i + 1]->postbox_id || empty($postbox_id)){
                        $create_new_postbox_flag = true;
                    }else{
                        $create_new_postbox_flag = false;
                    }
                } else {
                    $create_new_postbox_flag = true;
                }
            }

            // Create new one
            if ($create_new_postbox_flag) {
                // Create new postbox and assign to this user
                $location_available_id = '1'; // Berlin
                $postbox_id = ci()->postbox_m->insert(
                    array(
                        "customer_id" => $customer_id,
                        "postbox_name" => $new_name,
                        "name" => $new_name,
                        "company" => "",
                        "first_location_flag" => APConstants::ON_FLAG,
                        "location_available_id" => $location_available_id,
                        "type" => APConstants::ENTERPRISE_CUSTOMER,
                        "is_main_postbox" => APConstants::ON_FLAG,
                        "name_verification_flag" => APConstants::OFF_FLAG,
                        "company_verification_flag" => APConstants::OFF_FLAG,
                        "created_date" => now()
                    ));

                // update imcomming flag setting.
                ci()->postbox_setting_m->insert(array(
                    "customer_id" => $customer_id,
                    "postbox_id" => $postbox_id,
                    "email_notification" => 1,
                    "invoicing_cycle" => 1,
                    "weekday_shipping" => 2,
                    "collect_mail_cycle" => 2
                ));
            }
            /**
            * 1080 Save activity postbox
            * - CREATE
            */
            customers_api::addPostboxHistory($postbox_id, APConstants::POSTBOX_CREATE, APConstants::ENTERPRISE_TYPE);
            // Assign for this user
            account_api::add_postbox_to_user($parent_customer_id, $customer_id, $postbox_id);

            $parent_customer_address = CustomerUtils::getCustomerAddressByID($parent_customer_id);
            if (!empty($parent_customer_address)) {
                $customer_adress_check = CustomerUtils::getCustomerAddressByID($customer_id);
                // Update invoicing address and forwading
                $address_data = array(
                    'customer_id' => $customer_id,
                    'shipment_address_name' => 'your shipment address name',
                    'shipment_company' => 'your shipment company',
                    'shipment_street' => 'your shipment street',
                    'shipment_postcode' => 'your shipment postcode',
                    'shipment_city' => 'your shipment_city',
                    'shipment_region' => '',
                    'shipment_country' => $parent_customer_address->shipment_country,
                    'shipment_phone_number' => '',
                    'invoicing_address_name' => 'your invoicing_address name',
                    'invoicing_company' => 'your invoicing company',
                    'invoicing_street' => 'your invoicing street',
                    'invoicing_postcode' => 'your invoicing postcode',
                    'invoicing_city' => 'your invoicing city',
                    'invoicing_region' => 'your invoicing region',
                    'invoicing_country' => $parent_customer_address->invoicing_country,
                    'invoicing_phone_number' => '',
                    'invoice_address_verification_flag' => APConstants::ON_FLAG,
                    'eu_member_flag' => $parent_customer_address->eu_member_flag
                );
                if (empty($customer_adress_check)) {
                    ci()->customers_address_m->insert($address_data);
                } else {
                    ci()->customers_address_m->update_by_many( array('customer_id' => $customer_id), $address_data);
                }
            }
        }

        // Assign all postbox for first user
        if ($total_user == 0 && $separatePostboxType == '1') {
            // Assign all postbox
            if (!empty($list_postbox) && count($list_postbox) > 0) {
                foreach($list_postbox as $postbox) {
                    $postbox_id = $postbox->postbox_id;
                    account_api::add_postbox_to_user($parent_customer_id, $parent_customer_id, $postbox_id);
                }
            }
        } else {
            // Assign first postbox for first user
            if ($current_postbox_count > 0) {
                $postbox_id = $list_postbox[0]->postbox_id;
                account_api::add_postbox_to_user($parent_customer_id, $parent_customer_id, $postbox_id);
            }
        }

        // set main postbox for enterprise users.
        $list_user_id = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($parent_customer_id);
        if(!empty($list_user_id)){
            ci()->postbox_m->update_by_many(array(
                "customer_id IN (".implode(',', $list_user_id).")" => null
            ), array(
                "is_main_postbox" => APConstants::ON_FLAG,
                "first_location_flag" => APConstants::ON_FLAG,
            ));

            // set active postbox check flag for each user.
            foreach($list_user_id as $user_id){
                CustomerProductSetting::set($user_id, APConstants::CLEVVERMAIL_PRODUCT, APConstants::activate_postbox_name_flag, 1);
                CustomerProductSetting::set($user_id, APConstants::CLEVVERMAIL_PRODUCT, APConstants::activate_name_comp_address_flag, 1);
                CustomerProductSetting::set($user_id, APConstants::CLEVVERMAIL_PRODUCT, APConstants::activate_city_address_flag, 1);
            }
        }
    }

    /**
     * Create new sub account at sonetel
     * @param unknown_type $name
     * @param unknown_type $password
     */
    public static function create_sub_account($parent_customer_id) {
        ci()->load->library('phones/sonetel');
        ci()->load->model('phones/phone_customer_subaccount_m');
        $parent_customer = CustomerUtils::getCustomerByID($parent_customer_id);
        if (empty($parent_customer)) {
            return false;
        }
        $name = $parent_customer->customer_code;
        $email = $parent_customer->customer_code.'@clevvermail.com';
        $user_fname = $parent_customer->customer_code;
        $password_obj = APUtils::generatePassword(8);
        $password = $password_obj['raw_pass'];
        $account_id = ci()->sonetel->create_sub_account($name, $email, $user_fname, $password);
        if (empty($account_id)) {
            return false;
        }

        $change_balance = 9;
        // Add credit balance
        ci()->sonetel->update_sub_account($account_id, $change_balance);

        // Add invoice detail activity
        $activity = APConstants::SUBCRIBE_PHONE_ACCOUNT_AT;
        $activity_type= APConstants::SUBCRIBE_PHONE_ACCOUNT_ACTIVITY_TYPE;
        $activity_date = APUtils::getCurrentYearInvoice() . APUtils::getCurrentMonthInvoice() . APUtils::getCurrentDayInvoice();
        phones_api::insertActivityPhoneInvoiceDetail($parent_customer_id, $parent_customer_id, $activity, $activity_type,
                1, $change_balance + 1, $activity_date, true, '', '');

        ci()->phone_customer_subaccount_m->insert(array(
            "customer_id" => $parent_customer_id,
            "account_id" => $account_id,
            "name" => $name,
            "email" => $email,
            "user_fname" => $user_fname,
            "password" => md5($password),
            "created_date" => now()
        ));
        return $account_id;
    }

    /**
     * Gets list user of customer.
     * @param type $customer_id
     */
    public static function getListUserOfCustomer($customer_id){
        ci()->load->model("customers/customer_m");
        $list_user = ci()->customer_m->get_many_by_many(array(
            'parent_customer_id' => $customer_id,
            'activated_flag' => APConstants::ON_FLAG
        ));

        return $list_user;
    }

    /**
     * Gets list user id of customers
     *
     * @param type $customer_id
     */
    public static function getListUserIdOfCustomer($customer_id){
        $list_user_id = array($customer_id);
        $list_user = account_api::getListUserOfCustomer($customer_id);
        foreach($list_user as $user){
            $list_user_id[] = $user->customer_id;
        }

        return $list_user_id;
    }

    /**
     * Get list all postbox of enterprise customer.
     *
     * @param type $parent_customer_id
     */
    public static function getListAvailPostboxOfEnterpriseCustomer($parent_customer_id) {
        ci()->load->model("mailbox/postbox_m");
        return ci()->postbox_m->getListAvailPostboxOfEnterpriseCustomer($parent_customer_id);
    }

    /**
     * Change phone number owner
     * @param type $parent_customer_id
     * @param type $customer_id
     * @param type $phone_number
     */
    public static function assign_phonenumber_byuser($parent_customer_id, $customer_id, $phone_number) {
        // Add model and library
        ci()->load->library('phones/sonetel');
        ci()->load->model('customers/phone_customer_user_m');
        ci()->load->model('phones/phone_area_code_m');
        ci()->load->model('phones/phone_number_m');

        $update_data = array();
        $update_data ['customer_id'] = $customer_id;
        $update_data ['modified_date'] = date('Y-m-d H:i:s');
        ci()->phone_number_m->update_by_many(array(
            "phone_number" => $phone_number,
            "parent_customer_id" => $parent_customer_id
        ), $update_data);
    }

    /**
     * Change phone number owner
     * @param type $parent_customer_id
     * @param type $customer_id
     * @param type $phone_number
     */
    public static function unassign_phonenumber_byuser($parent_customer_id, $customer_id) {
        // Add model and library
        ci()->load->library('phones/sonetel');
        ci()->load->model('customers/phone_customer_user_m');
        ci()->load->model('phones/phone_area_code_m');
        ci()->load->model('phones/phone_number_m');

        $update_data = array();
        $update_data ['customer_id'] = $parent_customer_id;
        $update_data ['modified_date'] = date('Y-m-d H:i:s');
        ci()->phone_number_m->update_by_many(array(
            "customer_id" => $customer_id,
            "parent_customer_id" => $parent_customer_id
        ), $update_data);
    }

    /**
     * Interal assign given phone number to user. (Don't user)
     *
     * @deprecated since version 1.0
     * @param type $parent_customer_id
     * @param type $customer_id
     * @param type $phone_number
     */
    public static function internal_assign_phonenumber_byuser($parent_customer_id, $customer_id, $phone_number) {
        // Add model and library
        ci()->load->library('phones/sonetel');
        ci()->load->model('customers/phone_customer_user_m');
        ci()->load->model('phones/phone_area_code_m');
        ci()->load->model('phones/phone_number_m');

        $phone_user = ci()->phone_customer_user_m->get_by_many(array(
            "parent_customer_id" => $parent_customer_id,
            "customer_id" => $customer_id
        ));
        $account_id = APContext::getSubAccountId($parent_customer_id);
        $phone_user_id = $phone_user->phone_user_id;
        $update_data = array();
        $update_data ['customer_id'] = $customer_id;
        // $update_data ['phone_user_id'] = $phone_user_id;
        $update_data ['modified_date'] = date('Y-m-d H:i:s');
        ci()->phone_number_m->update_by_many(array(
            "phone_number" => $phone_number,
            "parent_customer_id" => $parent_customer_id
        ), $update_data);

        // Call sonetel to register this phone number to given
        $input = array(
            "connect_to_type" => 'user',
            "connect_to" => $phone_user_id
        );
        ci()->sonetel->update_phone_number($account_id, $phone_number, $input);
    }

    /**
     * Assign phones to user.
     *
     * @param type $parent_customer_id
     * @param type $customer_id
     * @param type $id
     */
    public static function assign_phones_byuser($parent_customer_id, $customer_id, $id) {
        ci()->load->library('phones/sonetel');
        ci()->load->model('customers/phone_customer_user_m');
        ci()->load->model('phones/phone_m');

        $phone_user = ci()->phone_customer_user_m->get_by_many(array(
            "parent_customer_id" => $parent_customer_id,
            "customer_id" => $customer_id
        ));
        if (empty($phone_user)) {
            throw new BusinessException('Please add a new phone number first.');
        }
        $account_id = APContext::getSubAccountId($parent_customer_id);
        $phone_user_id = $phone_user->phone_user_id;
        $update_data = array();
        $update_data ['customer_id'] = $customer_id;
        $phone = ci()->phone_m->get_by_many(array(
            "id" => $id,
            "parent_customer_id" => $parent_customer_id
        ));
        $phone_type = $phone->phone_type;
        $phone_name = $phone->phone_name;
        $phone_number = $phone->phone_number;

        if (empty($phone->phone_id)) {
            // Call sonetel to register this phone number to given user
            $phone_id = ci()->sonetel->add_phones($account_id, $phone_user_id, $phone_name, $phone_type, $phone_number);
            $update_data ['phone_id'] = $phone_id;
        }
        ci()->phone_m->update_by_many(array(
            "id" => $id,
            "parent_customer_id" => $parent_customer_id
        ), $update_data);
    }

    /**
     * Assign phones to user.
     *
     * @param type $parent_customer_id
     * @param type $customer_id
     * @param type $id
     */
    public static function unassign_phones_byuser($parent_customer_id, $customer_id) {
        ci()->load->model('phones/phone_m');

        $update_data = array();
        $update_data ['customer_id'] = $parent_customer_id;

        ci()->phone_m->update_by_many(array(
            "customer_id" => $customer_id
        ), $update_data);
    }

    /**
     * Assign a given phone to user.
     *
     * @deprecated since version 1.0
     * @param type $customer_id
     * @param type $user_id
     * @param type $id
     */
    public static function internal_assign_phones_byuser($parent_customer_id, $customer_id, $id) {
        ci()->load->library('phones/sonetel');
        ci()->load->model('customers/phone_customer_user_m');
        ci()->load->model('phones/phone_m');

        $phone_user = ci()->phone_customer_user_m->get_by_many(array(
            "parent_customer_id" => $parent_customer_id,
            "customer_id" => $customer_id
        ));
        $account_id = APContext::getSubAccountId($parent_customer_id);
        $phone_user_id = $phone_user->phone_user_id;
        $update_data = array();
        $update_data ['customer_id'] = $customer_id;
        $update_data ['modified_date'] = date('Y-m-d H:i:s');
        $phone = ci()->phone_m->get_by_many(array(
            "id" => $id,
            "parent_customer_id" => $parent_customer_id
        ));
        $phone_type = $phone->phone_type;
        $phone_name = $phone->phone_name;
        $phone_number = $phone->phone_number;

        // Call sonetel to register this phone number to given user
        $phone_id = ci()->sonetel->add_phones($account_id, $phone_user_id, $phone_name, $phone_type, $phone_number);

        $update_data ['phone_id'] = $phone_id;
        ci()->phone_m->update_by_many(array(
            "id" => $id,
            "parent_customer_id" => $parent_customer_id
        ), $update_data);
    }

    /**
     * Add phone user. (Don't use now)
     *
     * @param type $name
     * @param type $email
     */
    public static function add_phone_user($parent_customer_id, $customer_id, $name, $email, $password) {
        // Update phone number user
        ci()->load->model('customers/phone_customer_user_m');
        ci()->load->library('phones/sonetel');
        $phone_customer_user = ci()->phone_customer_user_m->get_by_many(array(
            'customer_id' => $customer_id,
            'parent_customer_id' => $parent_customer_id
        ));
        if (!empty($phone_customer_user)) {
            return;
        }

        $phone_subaccount_id = APContext::getSubAccountId($parent_customer_id);
        if (empty($phone_subaccount_id)) {
            $phone_subaccount_id = account_api::create_sub_account($parent_customer_id);
            if (empty($phone_subaccount_id)) {
                $message = sprintf(lang('users.message.add_fail'), $email);
                $this->error_output($message);
                return;
            }
        }

        // Create new user
        try {
            $phone_user_id = ci()->sonetel->create_new_user($phone_subaccount_id, $email, $name, $password);
            if (empty($phone_user_id)) {
                $message = sprintf(lang('users.message.add_fail'), $email);
                $this->error_output($message);
                return;
            }
        } catch (Exception $e) {
            $this->error_output($e->getMessage());
            return;
        }

        if (empty($phone_customer_user)) {
            ci()->phone_customer_user_m->insert(array(
                'customer_id' => $customer_id,
                'parent_customer_id' => $parent_customer_id,
                'phone_user_id' => $phone_user_id
            ));
        }
        return $phone_user_id;
    }

    /**
     * Get next collect shipping.
     *
     * @param unknown_type $collect_mail_cycle
     * @param unknown_type $weekday_shipping
     */
    public static function get_next_collect_shipping($postbox_setting)
    {
        $one_day = 86400;
        $collect_date = '';
        if(!is_object($postbox_setting) || empty($postbox_setting)){
            return '';
        }
        // Daily
        if ($postbox_setting->collect_mail_cycle == '1') {
            $collect_date = APUtils::convert_timestamp_to_date(now() + $one_day, 'd.m.Y');
        } // Weekly
        else if ($postbox_setting->collect_mail_cycle == '2') {
            for ($i = 1; $i <= 7; $i++) {
                $current_weekday = APUtils::convert_timestamp_to_date(now() + $one_day * $i, 'w') + 1;
                if ($postbox_setting->weekday_shipping == $current_weekday) {
                    $collect_date = APUtils::convert_timestamp_to_date(now() + $one_day * $i, 'd.m.Y');
                    break;
                }
            }
        } // Monthly
        else if ($postbox_setting->collect_mail_cycle == '3') {
            $collect_date = APUtils::nextDayOfMonthly($postbox_setting->weekday_shipping);
        } // Quartly
        else if ($postbox_setting->collect_mail_cycle == '4') {
            $collect_date = APUtils::nextDayOfQuart($postbox_setting->last_modified_date);
        }
        return $collect_date;
    }

    /**
     * Init term & condition of enterprise customer.
     *
     * @param type $parent_customer_id
     */
    public static function initEnterpriseTermAndCondition($parent_customer_id){
        ci()->load->library('settings/settings_api');
        $term = settings_api::getTermAndConditionEnterprise($parent_customer_id);
        if(empty($term)){
            // init if there is no term & condition.
            settings_api::insertDefaultTermAndConditionOfEnterprise($parent_customer_id);
        }
        return true;
    }
    /**
     * Init term & condition of enterprise customer.
     *
     * @param type $parent_customer_id
     */
    public static function initAPIAccess($parent_customer_id){
        ci()->load->model('api/app_external_m');
        // Check existing
        $app_external = ci()->app_external_m->get_by_many(array('customer_id' => $parent_customer_id));
        $app_code = '';
        $app_key = '';

        if (empty($app_external)) {
            $app_code = APUtils::generateRandom(20);
            $app_key = APUtils::generateRandom(50);
            ci()->app_external_m->insert(array(
                'app_code' => $app_code,
                'app_key' => $app_key,
                'app_name' => 'Mobile application',
                'validate_key_flag' => '1',
                'disable_flag' => '0',
                'created_date' => now(),
                'version' => '1.0',
                'customer_id' => $parent_customer_id
            ));
        } else {
            $app_code = $app_external->app_code;
            $app_key = $app_external->app_key;
        }

        // Get api access information
        $api_access_flag = AccountSetting::get($parent_customer_id, APConstants::CUSTOMER_API_ACCESS_SETTING);
        $end_date = AccountSetting::get_alias02($parent_customer_id, APConstants::CUSTOMER_API_ACCESS_SETTING);
        if (!empty($end_date)) {
            $end_date = APUtils::convert_timestamp_to_date($end_date);
        }

        return array(
            'app_code' => $app_code,
            'app_key' => $app_key,
            'api_access_flag' => $api_access_flag,
            'end_date' => $end_date
        );
    }

    /**
     * get contract term of product.
     * @param type $product_id
     * @param type $product_type
     * @return string
     */
    public static function getContractTermBy($product_id, $product_type){
        if($product_type == 'postbox'){
            return "12 Months";
        }
        if($product_type == 'phone'){
            return "12 Months";
        }
        return "12 Months";
    }

    /**
     * clone postbox and postbox setting.
     *
     * @param type $postbox_id
     * @param type $target_customer_id
     * @return type
     * @throws BusinessException
     */
    public static function reassignPostboxToUser($postbox_id, $target_customer_id, $parent_customer_id){
        ci()->load->model(array(
            "mailbox/postbox_m",
            "mailbox/postbox_setting_m",
            "scans/envelope_m",
            "customers/postbox_customer_user_m",
            "cases/cases_m"
        ));
        $is_deleted = false;
        $postbox = ci()->postbox_m->get($postbox_id);
        if(empty($postbox)){
            throw new BusinessException("This postbox does not exist.");
        }

        // count item of this postbox.
        $count_envelopes = ci()->envelope_m->count_by_many(array(
            "postbox_id" => $postbox_id,
            "RIGHT(envelope_code, 4) <> '_000'" => null,
            "deleted_flag" => APConstants::OFF_FLAG
        ));

        // declare variable.
        $new_postbox_id = $postbox_id;

        // open transaction.
        ci()->envelope_m->db->trans_begin();

        // check if there is no items in this postbox => assign directly to this customer.
        if(empty($count_envelopes)){
            $new_postbox_id = $postbox_id;
        }
        // delete old postbox and create new postbox for new user if postbox has at least 1 items.
        else {
            // clone new postbox.
            $tmp_postbox = APUTils::convertObjectToArray($postbox);
            $tmp_postbox['postbox_id'] = '';
            $tmp_postbox['customer_id'] = $target_customer_id;
            $tmp_postbox['created_date'] = now();
            $tmp_postbox['is_main_postbox'] = 0;
            $tmp_postbox['first_location_flag'] = 0;
            $new_postbox_id = ci()->postbox_m->insert($tmp_postbox);

            // clone postbox setting.
            $postbox_setting = ci()->postbox_setting_m->get($postbox_id);
            if(!empty($postbox_setting)){
                $postbox_setting->postbox_id = $new_postbox_id;
                $postbox_setting->customer_id = $target_customer_id;
                ci()->postbox_setting_m->insert(APUtils::convertObjectToArray($postbox_setting));
            }
            $is_deleted = true;
            // delete old postbox
            APUtils::deletePostbox($postbox_id, $postbox->customer_id, APConstants::POSTBOX_DELETE_ORDER_BY_CUSTOMER);
        }

        // do assign postbox.
        // Check exist data
        $postbox_user_check = ci()->postbox_customer_user_m->get_by_many(array(
            'customer_id' => $target_customer_id,
            'parent_customer_id' => $parent_customer_id,
            'postbox_id' => $new_postbox_id
        ));
        if (empty($postbox_user_check)) {
            // Insert new data
            // Check exist data
            ci()->postbox_customer_user_m->insert(array(
                'customer_id' => $target_customer_id,
                'parent_customer_id' => $parent_customer_id,
                'postbox_id' => $new_postbox_id
            ));
            /* 1080
            * Delete old postbox from parent customer
            */
            if (!$is_deleted) {
                customers_api::addPostboxHistory($new_postbox_id, APConstants::POSTBOX_DELETE_ORDER_BY_CUSTOMER, $postbox->type);
            }
            // Change customer id from parent_customer_id to customer_id
            ci()->postbox_m->update_by_many(array(
                'postbox_id' => $new_postbox_id
            ), array(
                'customer_id' => $target_customer_id
            ));
            /* 1080
            * Add new postbox actiity
            */
            customers_api::addPostboxHistory($new_postbox_id, APConstants::POSTBOX_CREATE, $postbox->type);
        }

        // update cases verification postbox.
        ci()->cases_m->update_by_many(array(
            "target_type" => APConstants::CASE_PRODUCT_TYPE_POSTBOX,
            "postbox_id" => $postbox_id,
            "customer_id" => $postbox->customer_id
        ), array(
            "postbox_id" => $new_postbox_id,
            "customer_id" => $target_customer_id,
            "deleted_flag" => APConstants::OFF_FLAG
        ));

        // commit transaction
        if(ci()->envelope_m->db->trans_status() == FALSE){
            ci()->envelope_m->db->trans_rollback();
        }else{
            ci()->envelope_m->db->trans_commit();
        }

        return $new_postbox_id;
    }
}
