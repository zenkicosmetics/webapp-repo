<?php defined('BASEPATH') or exit('No direct script access allowed');

class customers_api extends base_api
{


    public function __construct() {

        ci()->load->model(array(
            'customers/customer_m',
            'email/email_m',
            'scans/envelope_summary_month_m',
            'mailbox/postbox_m',
            'customers/customers_address_m',
            'settings/countries_m',
            'cloud/customer_cloud_m',
            'invoices/invoice_summary_m'
        ));

        ci()->load->library(array(
            'settings/settings_api',
            'scans/scans_api',
            'mailbox/mailbox_api',
            'email/email_api',
            'payment/payment_api',
            'partner/partner_api',

        ));

        ci()->lang->load(array(
            'customers/customer'
        ));

    }

    public static function updateCustomerInvoiceCode($customerID, $invoiceCode)
    {

        $result = ci()->customer_m->update_by_many(array(
            "customer_id" => $customerID,
            "(request_confirm_flag IS NULL OR request_confirm_flag = '0')" => NULL
        ), array(
            "invoice_code" => $invoiceCode,
            "request_invoice_flag" => APConstants::OFF_FLAG,
            "request_invoice_date" => now()
        ));

        return $result;
    }

    /**
     * @description: Set customer to Active
     */
    public static function updateActiveCustomer($customerID)
    {
        ci()->load->model('customers/customer_m');

        ci()->customer_m->update_by_many(array(
            'customer_id' => $customerID
        ), array(
            "activated_flag" => '1',
            "status" => '0',
            "deactivated_type" => ''
        ));

        // Insert into table history's postbox when customer is reactivated
        if (!empty($customerID)) {
             // Get all postbox in a customer is deactivated
            $postboxes = ci()->postbox_m->get_many_by('customer_id', $customerID);
            // Check  postbox is deactivated in a customer
            if (!empty($postboxes)) {
                foreach ($postboxes as $curr_postbox) {
                    // Check postbox is not deleted
                    if (!empty($curr_postbox) &&
                            ( $curr_postbox->deleted == '0' || ($curr_postbox->deleted == '0' && $curr_postbox->completed_delete_flag == '0'))) {
                        self::addPostboxHistory($curr_postbox->postbox_id, APConstants::POSTBOX_REACTIVATED, $curr_postbox->type);
                    }
                }
            }
        }
    }

    public static function getCurrentCustomer()
    {
        ci()->load->model('customers/customer_m');

        $customer = ci()->customer_m->get_current_customer_info();

        return $customer;
    }

    public static function updateCustomer($conditionNames, $conditionValues, $dataNames, $dataValues, $history_list = array())
    {
        ci()->load->model('customers/customer_m');

        $conditions = self::getArrayParams($conditionNames, $conditionValues);
        $dataUpdate = self::getArrayParams($dataNames, $dataValues);

        $result = ci()->customer_m->update_by_many($conditions, $dataUpdate);
        // #1309: Insert log to customer history
        if(!empty($history_list)){
            customers_api::insertCustomerHistory($history_list);
        }
        return $result;
    }

    public static function getCustomerByID($customerID)
    {
        ci()->load->model('customers/customer_m');

        $customer = ci()->customer_m->get($customerID);

        return $customer;
    }

    public static function getCustomersCaseVerifyAddress()
    {
        ci()->load->model('customers/customer_m');

        $customers = ci()->customer_m->get_all_customer_must_verify_case_address();

        return $customers;
    }

    public static function getNewCustomersRegisteredWithin24h()
    {
        ci()->load->model('customers/customer_m');

        $preCreatedDate = time();
        $customers = ci()->customer_m->get_new_customer_in24($preCreatedDate);

        return $customers;
    }

    public static function getAllActiveAccounts()
    {
        ci()->load->model('customers/customer_m');

        $accounts = ci()->customer_m->get_many_by(array(
            "(status <> '1' or status is null)" => null,
            "(activated_flag = 1 OR (activated_flag = 0 AND (deactivated_type = 'manual' OR deactivated_type = 'auto') ) )" => null
        ));

        return $accounts;
    }

    public static function getAccountsUnconfirmedEmailStatus()
    {
        ci()->load->model('customers/customer_m');

        $accounts = ci()->customer_m->get_customers_by(
            array(
                "(cs5.setting_value is null OR cs5.setting_value = 0)" => null,
                "customers.status" => APConstants::OFF_FLAG
            )
        );

        return $accounts;
    }

    public static function getAccountsDeletedUnconfirmedEmailStatusInFrame($timestamp, $by_system = false)
    {
        ci()->load->model('customers/customer_m');
        $array_where =  array(
                "(cs5.setting_value is null OR cs5.setting_value = 0)" => null,
                "customers.status" => 1,
                'deleted_date > ' => (time() - $timestamp)
        );
        if ($by_system) {
            $array_where['deleted_by'] = 0; // Deleted by Cron
        }
        $accounts = ci()->customer_m->get_customers_by(
            $array_where
        );

        return $accounts;
    }

    public static function getAccountsInactiveStatus()
    {
        ci()->load->model('customers/customer_m');

        $accounts = ci()->customer_m->get_many_by_many(
            array(
                "activated_flag" => APConstants::OFF_FLAG,
                "deactivated_type IS NULL" => null,
                "(status IS NULL OR status = '0')" => null,
                "(created_notify_date IS NOT NULL)" => null
            )
        );

        return $accounts;
    }

    public static function getAccountsAutoDeactivated()
    {
        ci()->load->model('customers/customer_m');
        ci()->load->library('customers/customers_api');
        $accounts = ci()->customer_m->get_many_by_many(
            array(
                "activated_flag" => APConstants::OFF_FLAG,
                "deactivated_type" => 'auto',
                "(status IS NULL OR status = '0')" => null,
                "(deactivated_date IS NOT NULL)" => null
            )
        );

        // Insert history postbox when the customer is deactivated by the system ( auto)
        // if (!empty($accounts)) {
        //     foreach ($accounts as $customer) {
        //         $customerID = $customer->customer_id;
        //         $deactivated_date = $customer->deactivated_date;

        //         // Get all postbox in a customer is deactivated
        //         $postboxes = ci()->postbox_m->get_many_by('customer_id', $customerID);
        //         // Check  postbox is deactivated in a customer
        //         if (!empty($postboxes)) {
        //             foreach ($postboxes as $curr_postbox) {
        //                 // Check postbox is not deleted
        //                 if (!empty($curr_postbox) &&
        //                         ( $curr_postbox->deleted == '0' || ($curr_postbox->deleted == '0' && $curr_postbox->completed_delete_flag == '0'))) {
        //                     customers_api::addPostboxHistory($curr_postbox->postbox_id, APConstants::POSTBOX_DEACTIVATED, "");
        //                     // CustomerUtils::actionPostboxHistoryActivity($curr_postbox->postbox_id, APConstants::POSTBOX_DEACTIVATED, $deactivated_date, "", APConstants::INSERT_POSTBOX);
        //                 }
        //             }
        //         }
        //     }

        // }

        return $accounts;
    }

    public static function getAccountsDeletedAutoDeactivatedInFrame($timestamp)
    {
        ci()->load->model('customers/customer_m');

        $accounts = ci()->customer_m->get_many_by_many(
            array(
                "activated_flag" => APConstants::OFF_FLAG,
                "deactivated_type" => 'auto',
                "(status = '1')" => null,
                "(deactivated_date IS NOT NULL)" => null,
                'deleted_date > ' => (time() - $timestamp)
            )
        );

        return $accounts;
    }

    public static function getAllAccountDeactived($timestamp = null)
    {
        ci()->load->model('customers/customer_m');
        $where = array(
                "activated_flag" => APConstants::OFF_FLAG,
                "(deactivated_date IS NOT NULL)" => null
            );
        if ($timestamp != null) {
            // Todo: other filter with time
        }
        $accounts = ci()->customer_m->get_many_by_many(
            $where
        );

        return $accounts;
    }
    public static function updateAccountActivationKey($customerID)
    {
        ci()->load->model('customers/customer_m');

        $activationKey = APUtils::generateRandom(30);
        ci()->customer_m->update($customerID, array("activated_key" => $activationKey));

        return true;
    }

    public static function autoDeactivateAccount($customerID, $created_by_id = null)
    {
        ci()->load->model('customers/customer_m');

        $data = array(
            "activated_flag" => APConstants::OFF_FLAG,
            "deactivated_type" => APConstants::AUTO_INACTIVE_TYPE,
            "deactivated_date" => now(),
            "last_updated_date" => now()
        );
        ci()->customer_m->update($customerID, $data);

        //#1309: Insert customer history
        $history = [
            'customer_id' => $customerID,
            'action_type' => APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_STATUS,
            'created_by_id' => $created_by_id,
            'current_data' => APConstants::CUSTOMER_HISTORY_STATUS_AUTO_DEACTIVATED,
        ];
        customers_api::insertCustomerHistory([$history]);

        /*
        * 1180 Add deactive status postbox
        */
        if (!empty($customerID)) {
             // Get all postbox in a customer is deactivated
            $postboxes = ci()->postbox_m->get_many_by('customer_id', $customerID);
            // Check  postbox is deactivated in a customer
            if (!empty($postboxes)) {
                foreach ($postboxes as $curr_postbox) {
                    // Check postbox is not deleted
                    if (!empty($curr_postbox) &&
                            ( $curr_postbox->deleted == '0' || ($curr_postbox->deleted == '0' && $curr_postbox->completed_delete_flag == '0'))) {
                        self::addPostboxHistory($curr_postbox->postbox_id, APConstants::POSTBOX_DEACTIVATED, '');
                    }
                }
            }
        }
        return true;
    }

    public static function getVATCustomer($customer, $customerAddress)
    {
        ci()->load->library('invoices/invoices_api');

        $invoicingCountry = $customerAddress->invoicing_country;
        if (self::isEnterpriseCustomer($customer, $customerAddress)) {
            return invoices_api::getVAT($invoicingCountry, APConstants::CUSTOMER_TYPE_ENTERPRISE);
        } else {
            return invoices_api::getVAT($invoicingCountry, APConstants::CUSTOMER_TYPE_PRIVATE);
        }
    }

    public static function isPrivateCustomer($customer, $customerAddress)
    {
        return !self::isEnterpriseCustomer($customer, $customerAddress);
    }

    public static function isEnterpriseCustomer($customer, $customerAddress)
    {
        // if customer comes from EU country OR Germany
        if ($customerAddress->eu_member_flag == '1' || $customerAddress->invoicing_country == APConstants::GERMANY_COUNTRY_ID) {
            if (self::isValidEUVatNumber($customer, $customerAddress)) {
                return true; // enterprise
            } else {
                return false; // private
            }
        } else { // customer comes from a country outside EU
            if (empty($customerAddress->invoicing_company)) {
                return false; // private
            } else {
                return true; // enterprise
            }
        }
    }

    public static function isValidEUVatNumber($customer, $customerAddress)
    {
        if (!empty($customer->vat_number) && !empty($customerAddress->invoicing_company)) {
            return true;
        }

        return false;
    }

    public static function getStandardCurrency($customerID = 0)
    {
        ci()->load->model(array(
            'customers/customer_m',
            'settings/currencies_m'
        ));

        $standardCurrency = ci()->customer_m->get_standard_setting_currency($customerID);
        if (empty($standardCurrency)) {
            $standardCurrency = ci()->currencies_m->get_by(array('currency_short' => 'EUR'));
        }
        return $standardCurrency;
    }

    public static function getStandardDecimalSeparator($customerID = 0)
    {
        ci()->load->model('customers/customer_m');

        $standardDecimalSeparator = ci()->customer_m->get_standard_setting_decimal_separator($customerID);

        return $standardDecimalSeparator;
    }

    public static function addBlacklistCustomer($customerId, $email)
    {
        ci()->load->model('customers/customer_blacklist_m');

        $id = ci()->customer_blacklist_m->insert(array(
            "customer_id" => $customerId,
            "email" => $email,
            "created_date" => now()
        ));

        return $id;
    }

    public static function getCustomersByPlanDeleteDate($planDeleteDate)
    {
        ci()->load->model('customers/customer_m');

        $customers = ci()->customer_m->get_many_by(array("plan_delete_date" => $planDeleteDate));

        return $customers;
    }

    /**
     * Gets customer by paging.
     */
    public static function getCustomerPaging($arrayCondition, $start, $limit, $sortCol, $sortType, $locationIdList)
    {
        ci()->load->model('customers/customer_m');

        $result = ci()->customer_m->get_customer_paging($arrayCondition, $start, $limit, $sortCol, $sortType, $locationIdList);

        return $result;
    }

    public static function countAllCustomersActivatedWithChargeFee($listLocationAccess)
    {
        ci()->load->model("customers/customer_m");

        $customers = ci()->customer_m->count_by_customer_paging(array(
            'activated_flag' => APConstants::ON_FLAG,
            "charge_fee_flag" => APConstants::ON_FLAG,
        ), $listLocationAccess);

        return $customers;
    }

    public static function countAllCustomersWithChargeFee($listLocationAccess)
    {
        ci()->load->model("customers/customer_m");

        $customers = ci()->customer_m->count_by_customer_paging(array(
            "charge_fee_flag" => APConstants::ON_FLAG,
        ), $listLocationAccess);

        return $customers;
    }

    /**
     * Get customer status.
     * Return: active | auto-deactivated | manu-deactivated | never activated |
     * deleted
     *
     * @param unknown_type $customer
     */
    public static function getCustomerStatus($customer, $customer_status='')
    {
        ci()->lang->load('customers/customer');

        $status = $customer->status;
        if(empty($customer_status)){
            $customer_status = CustomerProductSetting::get_activate_flags($customer->customer_id);
        }
        $email_confirm_flag = $customer_status['email_confirm_flag'];
        $deactivated_type = $customer->deactivated_type;
        $activated_flag = $customer->activated_flag;

        if ($status === '1') {
            return lang('customer.status.deleted');
        } else if ($activated_flag === '1') {
            return lang('customer.activated');
        } else {
            if ($email_confirm_flag != '1' || $status === '-1') {
                return lang('customer.never_activated');
            } else if ($deactivated_type == 'auto') {
                return lang('customer.auto_deactivated');
            } else if ($deactivated_type == 'manual') {
                return lang('customer.manu_deactivated');
            }
            if ($activated_flag != '1') {
                return lang('customer.never_activated');
            }
        }
    }

    /**
     * Activate customer by id
     *
     * @param unknown_type $customer_id
     */
    public static function reactivateCustomerWhenPaymentSuccess($customer_id)
    {
        ci()->load->model('customers/customer_m');

        /*ci()->customer_m->update_by_many( array(
            "customer_id" => $customer_id,
            "shipping_address_completed" => APConstants::ON_FLAG,
            "invoicing_address_completed" => APConstants::ON_FLAG,
            "postbox_name_flag" => APConstants::ON_FLAG,
            "name_comp_address_flag" => APConstants::ON_FLAG,
            "city_address_flag" => APConstants::ON_FLAG,
            "payment_detail_flag" => APConstants::ON_FLAG,
            "email_confirm_flag" => APConstants::ON_FLAG,
            "accept_terms_condition_flag" => APConstants::ON_FLAG
        ), array(
            "deactivated_type" => "",
            "deactivated_date" => null,
            "activated_flag" => APConstants::ON_FLAG,
            "last_updated_date" => now()
        ));*/

        // update: convert registration process flag to customer_product_setting.
        $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;
        CustomerProductSetting::doActiveCustomer($customer_id, $created_by_id);
    }

    /**
     * only activate customer if customer has no method payment.
     * @param type $customer_id
     */
    public static function activateCustomerWhenUpdatePaymentMethod($customer_id){
        ci()->load->model('customers/customer_m');

        /*ci()->customer_m->update_by_many( array(
            "customer_id" => $customer_id,
            "shipping_address_completed" => APConstants::ON_FLAG,
            "invoicing_address_completed" => APConstants::ON_FLAG,
            "postbox_name_flag" => APConstants::ON_FLAG,
            "name_comp_address_flag" => APConstants::ON_FLAG,
            "city_address_flag" => APConstants::ON_FLAG,
            "payment_detail_flag" => APConstants::ON_FLAG,
            "email_confirm_flag" => APConstants::ON_FLAG,
            "(deactivated_type IS NULL OR deactivated_type = '')" => null,
			"accept_terms_condition_flag" => APConstants::ON_FLAG
        ), array(
            "activated_flag" => APConstants::ON_FLAG,
            "last_updated_date" => now()
        ));*/

        /* update: convert registration process flag to customer_product_setting.
        * Add Re-active to postbox history */
        $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;
        CustomerProductSetting::doActiveCustomer($customer_id, $created_by_id);

        APContext::reloadCustomerLoggedIn();
    }

    /**
     * List all customer
     */
    public static function postboxlist($enquiry,$hideDeletedPostbox, $input_paging, $location_id, $api_mobile = 0)
    {
        $new_enquiry = APUtils::sanitizing($enquiry);
        $array_condition = array();
        if (!empty ($enquiry)) {
            $array_condition ["(customers.email LIKE '%" . $new_enquiry . "%'" . " OR customers.user_name LIKE '%" . $new_enquiry . "%'" . " OR customers.customer_id LIKE '%" . $new_enquiry . "%'" . " OR (p.name LIKE '%" . $new_enquiry . "%'" . " OR p.company LIKE '%" . $new_enquiry . "%'))"] = null;
        }

        // Hide all deleted Postbox
        if ( $hideDeletedPostbox == '1') {
            $array_condition ["(p.completed_delete_flag = '0' OR p.completed_delete_flag IS NULL)"] = null;
        }

        if($api_mobile){
            $list_access_location = APUtils::mobileLoadListAccessLocation();
        }
        else{
            $list_access_location = APUtils::loadListAccessLocation();
        }
        #1058 add multi dimension capability for admin
        $date_format = APUtils::get_date_format_in_user_profiles();

//#1250 HOTFIX: the Hamburg panel shows this customer but this cusotmer has already been deleted
        // update limit into user_paging.
//        $limit = isset ($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
//        APContext::updateAdminPagingSetting($limit);
//
//        // Get paging input
//        //$input_paging = $this->get_paging_input();
//        $input_paging ['limit'] = $limit;

        $list_access_location_id = array();
        //$location_id = $this->input->get_post("location_id");

        // #481 location selection.
        APContext::updateLocationUserSetting($location_id);

        foreach ($list_access_location as $location) {
            $list_access_location_id [] = $location->id;
        }

        if (!empty ($location_id) && in_array($location_id, $list_access_location_id)) {
            // if (! empty($location_id)) {
            $list_access_location_id = array(
                $location_id
            );
        } else {
            if (APContext::isAdminUser()) {
                $list_access_location_id = array();
            } else {
                $list_access_location_id [] = 0;
            }
        }
        //echo "<pre>";print_r($list_access_location_id);exit;
        // Call search method
        $query_result = ci()->customer_m->get_postbox_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type'], $list_access_location_id);

        // Process output data
        $total = $query_result ['total'];
        $datas = $query_result ['data'];

        // Get output response
        $response = self::get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);
        $datas_mobile = array();
        $i = 0;
        foreach ($datas as $row) {

            $account_type = '';
            if (!empty ($row->type)) {
                $account_type = lang('account_type_' . $row->type);
            }

            // Get customer status
            $customer_product_setting = CustomerProductSetting::get_activate_flags($row->customer_id);
            $customer_status = customers_api::getCustomerStatus($row, $customer_product_setting);
            $postbox_created_date = $row->postbox_created_date ? APUtils::viewDateFormat($row->postbox_created_date, $date_format) : "";

            $deleted = "";
            if($row->required_verification_flag == 1){
                if($row->deleted == 1){
                    $deleted = "Deleted";
                }else if($row->name_verification_flag == 0 || $row->company_verification_flag == 0){
                    $deleted = "Incompleted verification";
                } else if($row->name_verification_flag == 1 && $row->company_verification_flag == 1){
                    $deleted = "Completed verification";
                }
            }

            $response->rows [$i] ['id'] = $row->customer_id;
            $response->rows [$i] ['cell'] = array(
                $row->customer_id,
                $row->customer_code,
                $row->postbox_code,
                $row->name,
                $row->postbox_company,
                $account_type,
                $postbox_created_date,
                $customer_status,
                $deleted,
                $row->email,
                $row->number_received_items,
                $row->invoicing_address_name,
                $row->invoicing_company
            );

            //Data for mobile
            $row->account_type         = $account_type;
            $row->postbox_created_date = $postbox_created_date;
            $row->customer_status      = $customer_status;
            $row->deleted              = $deleted;

            $datas_mobile[$i] = $row;

            $i++;
        }

        return  array(
            "mobile_postbox_list" => $datas_mobile,
            "web_postbox_list"    => $response
        );

    }


    /**
     * List all customer
     */
    public static function get_list_customer($enquiry, $hideDeletedCustomer, $input_paging, $location_id, $api_mobile = 0, $account_type = NULL)
    {

        $array_condition = array();
        if (!empty ($enquiry)) {
            $array_condition ["(customers.customer_id LIKE '%{$enquiry}%' OR customers.email LIKE '%" . $enquiry . "%'" . " OR (p.name LIKE '%" . $enquiry . "%'" . " OR p.company LIKE '%" . $enquiry . "%')" . " OR (ca.invoicing_address_name LIKE '%" . $enquiry . "%' OR ca.invoicing_company LIKE '%" . $enquiry . "%')" . " OR (ca.shipment_address_name LIKE '%" . $enquiry . "%' OR ca.shipment_company LIKE '%" . $enquiry . "%'))"] = null;
        }

        // Hide all deleted customer
        if ($hideDeletedCustomer == '1') {
            $array_condition ["(customers.status <> '1' OR customers.status IS NULL)"] = null;
            $array_condition ["(p.deleted = 0)"] = null;
        }

        if($api_mobile){
            $list_access_location = APUtils::mobileLoadListAccessLocation();
        }
        else{
            $list_access_location = APUtils::loadListAccessLocation();
        }

        if ($account_type) {
            $array_condition["(customers.account_type =". $account_type .")"] = NULL;
        }

        $date_format = APUtils::get_date_format_in_user_profiles();

        // update limit into user_paging.
        $limit = isset ($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
        APContext::updateAdminPagingSetting($limit);

        $input_paging ['limit'] = $limit;

        $list_access_location_id = array();

        APContext::updateLocationUserSetting($location_id);

        foreach ($list_access_location as $location) {
            $list_access_location_id [] = $location->id;
        }

        if (!empty ($location_id) && in_array($location_id, $list_access_location_id)) {
            $list_access_location_id = array(
                $location_id
            );
        } else {
            if (APContext::isAdminUser()) {
                $list_access_location_id = array();
            } else {
                $list_access_location_id [] = 0;
            }
        }

        // Call search method
        $query_result = customers_api::getCustomerPaging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type'], $list_access_location_id);

        // Process output data
        $total = $query_result['total'];
        $rows = $query_result['data'];

        // Gets list customer id
        $listOfCustomerId = array();
        $listOfParentCustomerId = array();
        foreach ($rows as $row) {
            $listOfCustomerId[] = $row->customer_id;

            if(!empty($row->parent_customer_id)){
                $listOfParentCustomerId[] = $row->parent_customer_id;
            }
        }

        $listEnterpriseCustomer = array();
        if(!empty($listOfParentCustomerId)){
            $listEnterpriseCustomer = ci()->customer_m->get_many_by_many(array(
                "customer_id IN (".  implode(',', $listOfParentCustomerId).")" => null
            ));
        }

        // count number items of customer.
        if (count($listOfCustomerId) > 0) {
            $numberItems = scans_api::getNumberItemsByCustomerList($listOfCustomerId);
        }
        // Get output response
        $response = self::get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);

        // Gets postbox name and company
        $postboxes = ci()->postbox_m->get_list_name_company($listOfCustomerId);
        $deleted_by = "";
        $datas_mobile = array();
        $i = 0;
        foreach ($rows as $row) {
            $deleted_by = "";
            if($row->status == "1" && $row->customer_id == $row->deleted_by){
                $deleted_by = "Customer";
            } else if($row->status == "1" &&  !empty($row->display_name)){
                $deleted_by = $row->display_name;
            } else if($row->status == "1"){
                $deleted_by = "Systems";
            }

            $city = $row->shipment_city;
            $countryID = $row->shipment_country;
            $user_name = '';
            $user_name = isset($postboxes[$row->customer_id]) ? $postboxes[$row->customer_id]->name : "";
            $company = isset($postboxes[$row->customer_id]) ? $postboxes[$row->customer_id]->company : "";

            $account_type = '';
            if (!empty ($row->account_type)) {
                $account_type = lang('account_type_' . $row->account_type);
            }
            $customer_product_setting = CustomerProductSetting::get_activate_flags($row->customer_id);
            $customer_status = customers_api::getCustomerStatus($row, $customer_product_setting);
            if($row->status == APConstants::ON_FLAG){
                $required_verification_flag = lang('verification_NA_status');
            }else{
                $required_verification_flag = lang('verification_none_status');
                if ($row->required_verification_flag) {
                    $checkVerifyStatus = CaseUtils::check_case_verification_completed($row->customer_id);

                    if($row->activated_flag == APConstants::OFF_FLAG && $row->active_postbox_name_flag != APConstants::ON_FLAG){
                        $required_verification_flag = lang('verification_NA_status');
                    } else if (!$checkVerifyStatus) {
                        $required_verification_flag = lang('verification_incomplete_status');
                    } else{
                        $required_verification_flag = lang('verification_completed_status');
                    }
                }
            }
            // Calculate number deactivated days
            $row_activated_flag = $row->activated_flag;

            if ($row_activated_flag == '1') {
                $number_deactivated_days = '';
            } else {
                $number_deactivated_days = CustomerUtils::getInactiveDayOfCustomerBy($row);
            }
            if ($number_deactivated_days == 0) {
                $number_deactivated_days = '';
            }

            $charge_status  = customers_api::get_charge_status($row->charge_fee_flag);
            $email_status   = customers_api::get_email_status($customer_product_setting['email_confirm_flag']);
            $payment_status = customers_api::get_payment_status($customer_product_setting['payment_detail_flag']);
            $created_date   = APUtils::viewDateFormat($row->created_date, $date_format);
            $action_value   = customers_api::get_action_value($row->status, $row->customer_id);

            // Get enterprise customer code.
            $enterprise_customer_code = '';
            if(!empty($row->parent_customer_id)){
                foreach($listEnterpriseCustomer as $el){
                    if($el->customer_id == $row->parent_customer_id){
                        $enterprise_customer_code = $el->customer_code;
                        break;
                    }
                }
            }

            $response->rows [$i] ['id'] = $row->customer_id;
            $response->rows [$i] ['cell'] = array(
                $row->customer_id,
                $row->parent_customer_id,
                $row->customer_code,
                $user_name,
                $company,
                $row->email,
                $row->invoicing_address_name,
                $row->invoicing_company,
                $row->shipment_address_name,
                $row->shipment_company,
                $account_type,
                $customer_status,
                !empty($row->parent_customer_id)? "N/A" :$charge_status,
                $email_status,
                $payment_status,
                $city,
                $row->country_name,
                $numberItems[$row->customer_id],
                $created_date,
                $required_verification_flag,
                $number_deactivated_days,
                $deleted_by,
                $enterprise_customer_code,
                $action_value
            );

            //Data for mobile
            $row->user_name                  = $user_name;
            $row->company                    = $company;
            $row->account_type               = $account_type;
            $row->customer_status            = $customer_status;
            $row->charge_status              = $charge_status;
            $row->email_status               = $email_status;
            $row->payment_status             = $payment_status;
            $row->city                       = $city;
            $row->numberItems                = $numberItems[$row->customer_id];
            $row->created_date               = $created_date;
            $row->required_verification_flag = $required_verification_flag;
            $row->number_deactivated_days    = $number_deactivated_days;
            $row->action_value               = $action_value;

            $datas_mobile[$i]                = $row;
            $i++;
        }

        return  array(
            "mobile_customer_list" => $datas_mobile,
            "web_customer_list"    => $response
        );

    }

    /**
     * Get activated status.
     *
     * @param unknown_type $activated_flag
     */
    public static function get_charge_status($charge_flag)
    {
        if ($charge_flag === '1') {
            return lang('customer.charge');
        } else {
            return lang('customer.no_charge');
        }
    }

    /**
     * Get activated status.
     *
     * @param unknown_type $activated_flag
     */
    public static function get_email_status($email_confirm_flag)
    {
        if ($email_confirm_flag === '1') {
            return lang('customer.yes');
        } else {
            return lang('customer.no');
        }
    }

    /**
     * Get activated status.
     *
     * @param unknown_type $activated_flag
     */
    public static  function get_payment_status($payment_detail_flag)
    {
        if ($payment_detail_flag === '1') {
            return lang('customer.yes');
        } else {
            return lang('customer.no');
        }
    }

    /**
     * Get action value.
     * return -1 if customer is deleted (status = 1), or return customer_id if active
     *
     * @param unknown_type $customer_id
     */
    public static function get_action_value($status, $customer_id)
    {
        if ($status == 1) {
            return -1;
        } else {
            return $customer_id;
        }
    }


    /**
     * Method for handling different form actions
     */
    public static function save_customer($customer, $updateData, $created_by_id = null)
    {
        ci()->load->model('customers/customer_m');
        ci()->load->model('customers/customer_history_m');
        ci()->load->model('addresses/customers_address_m');
        ci()->load->model('mailbox/postbox_m');

        $customerID = $customer->customer_id;

        $response = array(
            'message' => '',
            'status'  => ''
        );
        try {

            //$updateData['password'] = '';
            //$updateData['status_flag'] = '';
            $conditionNamesCustomer2 = array("customer_id");
            $conditionValuesCustomer2 = array($customerID);
            $dataNamesCustomer2 = array('user_name', 'email', 'charge_fee_flag', 'required_verification_flag', 'shipping_factor_fc', 'required_prepayment_flag','auto_trash_flag');
            $dataValuesCustomer2 = array($updateData['email'], $updateData['email'], $updateData['charge_fee_flag'], $updateData['required_verification_flag'], $updateData['shipping_factor_fc'], $updateData['required_prepayment_flag'],$updateData['auto_trash_flag']);

            // #1309: customer history_list
            $customer_history_log = json_decode($updateData['customer_history_log'], true);
            $history_list = array();
            if($updateData['status_flag'] != $customer_history_log['status_flag']){
                $history_list['status'] = array(
                    'customer_id' => $customer->customer_id,
                    'created_by_id' => $created_by_id,
                    'action_type' => APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_STATUS
                );
            }

            // check status_flag
            $status_flag = $updateData['status_flag'];

            // Sync email to payone
            if ($customer->email != $updateData['email']) {
                $history_list['email'] = array(
                    'customer_id' => $customer->customer_id,
                    'action_type' => APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_EMAIL,
                    'old_data' => $customer->email,
                    'current_data' => $updateData['email'],
                    'created_by_id' => $created_by_id,
                );

                CustomerUtils::syncEmailToPayone($customerID);
            }

            // Deleted
            if ($status_flag == '0') {
                array_push($dataNamesCustomer2, 'status');
                array_push($dataValuesCustomer2, '1');
                 /*
                 * #1180 create postbox history page like check item page
                 *   Activity: APConstants::POSTBOX_DELETE_ORDER_BY_SYSTEM
                 */
                CustomerUtils::deleteCustomer($customerID, true, false, 1, APContext::getAdminIdLoggedIn(), $created_by_id);

                $message = sprintf(lang('customer.edit_success'), $updateData['email']);
                $response = array(
                    'message' => $message,
                    'status'  => true
                );
                return $response;
            } else if ($status_flag == '1') {
                array_push($dataNamesCustomer2, 'activated_flag', 'status', 'deactivated_type','accept_terms_condition_flag');
                array_push($dataValuesCustomer2, '1', '0', '','1');
                if(!empty($history_list['status']))
                    $history_list['status']['current_data'] = APConstants::CUSTOMER_HISTORY_STATUS_ACTIVATED;
                                // Get all postbox in a customer is deactivated
               $postboxes = ci()->postbox_m->get_many_by('customer_id', $customerID);
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
            } else if ($status_flag == '2') {
                // Auto de-activated customer
                $deactivated_type = APConstants::AUTO_INACTIVE_TYPE;
                $deactivated_date = now();
                array_push($dataNamesCustomer2, 'activated_flag', 'status', 'deactivated_type', 'deactivated_date');
                array_push($dataValuesCustomer2, '0', '0', $deactivated_type, $deactivated_date);

                // Get current open balance
                $open_balance = APUtils::strToNumber(APUtils::getCurrentBalance($customerID));
                if ($open_balance > 0.1) {
                    Events::trigger('deactivated_notifications', array(
                        'customer_id' => $customerID
                    ), 'string');
                } else {
                    $data = array(
                        "slug" => APConstants::deactived_customer_notification,
                        "to_email" => $customer->email,
                        // Replace content
                        "full_name" => $customer->email,
                        "email" => $customer->email,
                        "site_url" => APContext::getFullBalancerPath()
                    );
                    // Send email
                    MailUtils::sendEmailByTemplate($data);
                }
                 /*
                 * #1180 create postbox history page like check item page
                 *   Activity: deactivated
                 */
                // $curr_postbox = ci()->postbox_m->get_by('customer_id', $customerID);
                // CustomerUtils::actionPostboxHistoryActivity($curr_postbox->postbox_id, APConstants::POSTBOX_DEACTIVATED, $deactivated_date, $curr_postbox->type, APConstants::INSERT_POSTBOX);
                // Get all postbox in a customer is deactivated
                $postboxes = ci()->postbox_m->get_many_by('customer_id', $customerID);
                // Check  postbox is deactivated in a customer
                if (!empty($postboxes)) {
                    foreach ($postboxes as $curr_postbox) {
                        // Check postbox is not deleted
                        if (!empty($curr_postbox) &&
                                ( $curr_postbox->deleted == '0' || ($curr_postbox->deleted == '0' && $curr_postbox->completed_delete_flag == '0'))) {
                            self::addPostboxHistory($curr_postbox->postbox_id, APConstants::POSTBOX_DEACTIVATED, "");
                            // CustomerUtils::actionPostboxHistoryActivity($curr_postbox->postbox_id, APConstants::POSTBOX_DEACTIVATED, $deactivated_date, "", APConstants::INSERT_POSTBOX);
                        }
                    }
                }
                if(!empty($history_list['status']))
                    $history_list['status']['current_data'] = APConstants::CUSTOMER_HISTORY_STATUS_AUTO_DEACTIVATED;
            } else if ($status_flag == '3') {
                // Manual de-activated customer
                $deactivated_type = APConstants::MANUAL_INACTIVE_TYPE;
                $deactivated_date = now();
                array_push($dataNamesCustomer2, 'activated_flag', 'status', 'deactivated_type', 'deactivated_date');
                array_push($dataValuesCustomer2, '0', '0', $deactivated_type, $deactivated_date);
                // Get current open balance
                $open_balance = APUtils::strToNumber(APUtils::getCurrentBalance($customerID));
                if ($open_balance > 0.1) {

                    // when customer deactivated Send email trigger
                    Events::trigger('deactivated_notifications', array(
                        'customer_id' => $customerID
                    ), 'string');
                } else {
                    $data = array(
                        "slug" => APConstants::deactived_customer_notification,
                        "to_email" => $customer->email,
                        // Replace content
                        "full_name" => $customer->email,
                        "email" => $customer->email,
                        "site_url" => APContext::getFullBalancerPath()
                    );
                    // Send email
                    MailUtils::sendEmailByTemplate($data);
                }

               /*
                 * #1180 create postbox history page like check item page
                 *   Activity: deactivated
                 */

                // $curr_postbox = ci()->postbox_m->get_by('customer_id', $customerID);
                // self::addPostboxHistory($curr_postbox->postbox_id, APConstants::POSTBOX_DEACTIVATED);
                // CustomerUtils::actionPostboxHistoryActivity($curr_postbox->postbox_id, APConstants::POSTBOX_DEACTIVATED, $deactivated_date, $curr_postbox->type, APConstants::INSERT_POSTBOX);
                $postboxes = ci()->postbox_m->get_many_by('customer_id', $customerID);
                // Check  postbox is deactivated in a customer
                if (!empty($postboxes)) {
                    foreach ($postboxes as $curr_postbox) {
                        // Check postbox is not deleted
                        if (!empty($curr_postbox) &&
                                ( $curr_postbox->deleted == '0' || ($curr_postbox->deleted == '0' && $curr_postbox->completed_delete_flag == '0'))) {
                            self::addPostboxHistory($curr_postbox->postbox_id, APConstants::POSTBOX_DEACTIVATED, "");
                            // CustomerUtils::actionPostboxHistoryActivity($curr_postbox->postbox_id, APConstants::POSTBOX_DEACTIVATED, $deactivated_date, "", APConstants::INSERT_POSTBOX);
                        }
                    }
                }
                if(!empty($history_list['status']))
                    $history_list['status']['current_data'] = APConstants::CUSTOMER_HISTORY_STATUS_MANUAL_DEACTIVATED;
            } else if ($status_flag == '4') {
                // Never activate
                array_push($dataNamesCustomer2, 'activated_flag', 'status', 'deactivated_type');
                array_push($dataValuesCustomer2, '0', '0', '');
                if(!empty($history_list['status']))
                    $history_list['status']['current_data'] = APConstants::CUSTOMER_HISTORY_STATUS_NEVER_ACTIVATED;
            }
            // Check invoice type
            $invoice_type = $updateData['invoice_type'];
            if ($customer->invoice_type != $invoice_type) {
                // Change from 2 ==> 1
                if ($invoice_type == '1') {
                    // Only deactivate account if this customer does not have payment information
                    $activated_flag = APConstants::OFF_FLAG;
                    $payment_detail_flag = $customer->payment_detail_flag;

                    // Set primary card
                    $primary_card = APConstants::ON_FLAG;
                    $payments = payment_api::getPayment($customerID, $primary_card);

                    if (count($payments) > 0 && $customer->activated_flag == APConstants::ON_FLAG) {
                        $activated_flag = APConstants::ON_FLAG;
                    }
                    if (count($payments) == 0) {
                        $payment_detail_flag = APConstants::OFF_FLAG;
                    }
                    // Update invoice type and invalid invoice code
                    array_push($dataNamesCustomer2, 'invoice_type', 'invoice_code', 'payment_detail_flag', 'activated_flag');
                    array_push($dataValuesCustomer2, '1', '', $payment_detail_flag, $activated_flag);

                } else if ($invoice_type == '2') {
                    // Change from 1 ==> 2
                    $invoice_code = $updateData['invoice_code'];
                    if (strlen($invoice_code) != 10) {

                        $response = array(
                            'message' => 'Please create invoice code',
                            'status'  => false
                        );
                        return $response;
                    }
                    // Update invoice type and invalid invoice code
                    if (empty ($invoice_code)) {
                        $invoice_code = substr(md5($customerID), 0, 6) . APUtils::generateRandom(4);
                    }
                    $payment_detail_flag = APConstants::ON_FLAG;
                    $request_invoice_flag = APConstants::ON_FLAG;
                    $request_confirm_flag = APConstants::ON_FLAG;
                    $request_invoice_date = now();
                    $request_confirm_date = now();
                    array_push($dataNamesCustomer2, 'invoice_type', 'invoice_code', 'payment_detail_flag', 'request_invoice_flag', 'request_confirm_flag', 'request_invoice_date', 'request_confirm_date');
                    array_push($dataValuesCustomer2, '2', $invoice_code, $payment_detail_flag, $request_invoice_flag, $request_confirm_flag, $request_invoice_date, $request_confirm_date);
                }
            }

            // #581: change first location
            $new_first_location = isset($updateData['location_id'])?$updateData['location_id']:0;
            $current_location = mailbox_api::getFirstLocationBy($customerID);
            if ($current_location && $new_first_location) {
                $conditionNames = array("customer_id");
                $conditionValues = array($customerID);
                $dataNames = array("first_location_flag");
                $dataValues = array(0);
                mailbox_api::updateManyPostbox($conditionNames, $conditionValues, $dataNames, $dataValues);

                $conditionNames1 = array("customer_id", "location_available_id");
                $conditionValues1 = array($customerID, $new_first_location);
                $dataNames1 = array("first_location_flag");
                $dataValues1 = array(1);
                mailbox_api::updateManyPostbox($conditionNames1, $conditionValues1, $dataNames1, $dataValues1);
            }
            customers_api::updateCustomer($conditionNamesCustomer2, $conditionValuesCustomer2, $dataNamesCustomer2, $dataValuesCustomer2, $history_list);

            // check verification.
            // get list user id.
            $list_customer_id = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($customerID);
            if ($updateData['required_verification_flag'] == '0') {
                // do not check verification case.
                ci()->customer_m->update_by_many(array(
                    "customer_id" => $customerID
                ), array(
                    "required_verification_flag" => APConstants::OFF_FLAG
                ));

                ci()->postbox_m->update_by_many(array(
                    "customer_id" => $customerID
                ), array(
                    "name_verification_flag" => APConstants::ON_FLAG,
                    "company_verification_flag" => APConstants::ON_FLAG,
                ));

                ci()->customers_address_m->update_by_many(array(
                    "customer_id" => $customerID
                ), array(
                    "invoice_address_verification_flag" => APConstants::ON_FLAG
                ));

                // apply no-verification for all users of customers
                if(!empty($list_customer_id)){
                    ci()->customer_m->update_by_many(array(
                        "customer_id IN (".implode(',', $list_customer_id).")" => null
                    ), array(
                        "required_verification_flag" => APConstants::OFF_FLAG
                    ));

                    ci()->postbox_m->update_by_many(array(
                        "customer_id IN (".implode(',', $list_customer_id).")" => null
                    ), array(
                        "name_verification_flag" => APConstants::ON_FLAG,
                        "company_verification_flag" => APConstants::ON_FLAG,
                    ));

                    ci()->customers_address_m->update_by_many(array(
                        "customer_id IN (".implode(',', $list_customer_id).")" => null
                    ), array(
                        "invoice_address_verification_flag" => APConstants::ON_FLAG
                    ));
                }
            } else if ($updateData ['required_verification_flag'] == '1') {
                ci()->customer_m->update_by_many(array(
                    "customer_id" => $customerID
                ), array(
                    "required_verification_flag" => APConstants::ON_FLAG
                ));

                CaseUtils::start_verification_case($customerID);

                // apply no-verification for all users of customers
                if(!empty($list_customer_id)){
                    ci()->customer_m->update_by_many(array(
                        "customer_id IN (".implode(',', $list_customer_id).")" => null
                    ), array(
                        "required_verification_flag" => APConstants::ON_FLAG
                    ));

                    foreach($list_customer_id as $id){
                        CaseUtils::start_verification_case($id);
                    }
                }
            }

            // Check prepayment
            if ($updateData['required_prepayment_flag'] == '0') {
                mailbox_api::completeManualPrepaymentRequestWithoutCheck($customerID);
            }

            $message = sprintf(lang('customer.edit_success'), $updateData['email']);
            $response = array(
                'message' => $message,
                'status'  => true
            );
            return $response;

        } catch (Exception $e) {

            log_message('ERROR', $e->getMessage(), FALSE);
            $message = sprintf(lang('customer.edit_error'), $updateData['email']);

            $response = array(
                'message' => $message,
                'status'  => false
            );
            return $response;

        }

    }


    /**
     * Method for handling different form actions
     */
    public static function change_pass_customer($customer, $insert_data, $created_by_id = null)
    {
        $response = array(
            'message' => '',
            'status' => ''
        );
        try {

            $new_pass = $insert_data ['password'];
            $insert_data ['password'] = md5($insert_data ['password']);
            ci()->customer_m->update_password($customer->customer_id, $insert_data);

            // Build email content
            $email = $customer->email;
            // Send email
            $send_email_data = array(
                "slug" => APConstants::customer_reset_password,
                "to_email" => $email,
                // Replace content
                "full_name" => $email,
                "email" => $email,
                "password" => $new_pass,
                "site_url" => APContext::getFullBalancerPath()
            );

            //#1309: Insert customer history
            $history['customer_id'] = $customer->customer_id;
            $history['action_type'] = APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_PASSWORD;
            $history['created_by_id'] = $created_by_id;
            customers_api::insertCustomerHistory([$history]);

            // Call API to send email
            MailUtils::sendEmailByTemplate($send_email_data);

            $message = sprintf(lang('customer.edit_success'), $insert_data['email']);

            $response = array(
                'message' => $message,
                'status'  => true
            );
            return $response;

        } catch (Exception $e) {

            $message = sprintf(lang('customer.edit_error'), $insert_data['email']);
            $response = array(
                'message' => $message,
                'status'  => false
            );
            return $response;
        }
    }

    /**
     * Tinh toan so luong item (envelope scan, document scan, shipping).
     *
     * @param unknown_type $customer_id
     */
    public static function cal_scan_items($customer)
    {
        $customer_id = $customer->customer_id;
        $startDate = $customer->created_date;
        $endDate = now();
        $diff_month = APUtils::getMongthDiff($startDate, $endDate);
        if ($diff_month == 0) {
            $diff_month = 1;
        }

        $scan_summary = ci()->envelope_summary_month_m->summary_envelope($customer_id);
        $envelope_scan_number = $scan_summary->envelope_scan_number;
        $document_scan_number = $scan_summary->document_scan_number;
        $shipping_number = $scan_summary->direct_shipping_number;
        $shipping_number += $scan_summary->collect_shipping_number;

        return array(
            "envelope_scan_number" => empty ($envelope_scan_number) ? 0 : APUtils::number_format($envelope_scan_number / $diff_month, 0),
            "document_scan_number" => empty ($document_scan_number) ? 0 : APUtils::number_format($document_scan_number / $diff_month, 0),
            "shipping_number" => empty ($shipping_number) ? 0 : APUtils::number_format($shipping_number / $diff_month, 0)
        );
    }

    /**
     * Get activated status.
     *
     * @param unknown_type $activated_flag
     */
    public static function get_dropbox_status($cloud_id)
    {
        if (!empty ($cloud_id)) {
            return lang('customer.yes');
        } else {
            return lang('customer.no');
        }
    }

    /**
     * Generate invoice code
     */
    public static function generate_invoice_code($customer_id)
    {
        $invoice_code = substr(md5($customer_id), 0, 6) . APUtils::generateRandom(4);

        // Update new invoice_code to database
        $response = array();
        $result = customers_api::updateCustomerInvoiceCode($customer_id, $invoice_code);

        if ($result) {

            $message = sprintf(lang('generate_invoice_success'), $invoice_code);
            $response['status'] = true;
            $response['message'] = $message;
            $response['invoice_code'] = $invoice_code;
            return $response;

        } else {

            $message = lang('invoice_code_exists');
            $response['status'] = false;
            $response['message'] = $message;
            return $response;
        }
    }

    /**
     * View detail information for admin.
     */
    public static function view_detail_customer($customer_id)
    {

        $data_response = array();

        #1058 add multi dimension capability for admin
        $decimal_separator = APUtils::get_decimal_separator_in_user_profiles();
        $currency_sign = APUtils::get_currency_sign_in_user_profiles();
        $currency_short = APUtils::get_currency_short_in_user_profiles();
        $currency_rate = APUtils::get_currency_rate_in_user_profiles();
        $date_format = APUtils::get_date_format_in_user_profiles();

        $customer = new stdClass ();
        $customer = ci()->customer_m->get($customer_id);
        $customer_shipping_address = ci()->customers_address_m->get($customer_id);
        if(empty($customer_shipping_address)){
            $customer_shipping_address = null;
        }
        $main_postbox = ci()->postbox_m->get_by_many(array(
            'customer_id' => $customer_id,
            'is_main_postbox' => '1'
        ));
        $total_vat = 1.19;


        // 20140515 DuNT Start fixbug #203
        if ($customer_shipping_address) {
            if (is_numeric($customer_shipping_address->shipment_country)) {
                $ship_country = ci()->countries_m->get($customer_shipping_address->shipment_country);
                if ($ship_country) {
                    $customer_shipping_address->shipment_country = $ship_country->country_name;
                }
            }
            if (is_numeric($customer_shipping_address->invoicing_country)) {
                $invoice_country = ci()->countries_m->get($customer_shipping_address->invoicing_country);
                if ($invoice_country) {
                    $customer_shipping_address->invoicing_country = $invoice_country->country_name;
                }
            }
        }


        $customer_cloud = ci()->customer_cloud_m->get_by_many(array(
            "customer_id" => $customer_id
        ));
        $scan_item = customers_api::cal_scan_items($customer);
        if ($customer_cloud) {
            $dropbox_status = customers_api::get_dropbox_status($customer_cloud->cloud_id);
        } else {
            $dropbox_status = customers_api::get_dropbox_status(null);
        }

        // Gets customer infor.
        $postbox_counts = ci()->postbox_m->get_postbox_count_by_customer($customer_id);
        $free_postbox_count = 0;
        $private_postbox_count = 0;
        $business_postbox_count = 0;
        foreach ($postbox_counts as $postbox_count) {
            if ($postbox_count->type == '1') {
                $free_postbox_count = $postbox_count->box_count;
            } else if ($postbox_count->type == '2') {
                $private_postbox_count = $postbox_count->box_count;
            } else if ($postbox_count->type == '3') {
                $business_postbox_count = $postbox_count->box_count;
            }
        }

        $target_month = APUtils::getTargetMonthInvoice();
        $target_year = APUtils::getTargetYearInvoice();
        $next_invoices = ci()->invoice_summary_m->get_by_many(array(
            'invoice_month' => $target_year . $target_month,
            'customer_id' => $customer_id
        ));

        $next_invoices_display = new stdClass ();
        $next_invoices_display->postboxes_amount = 0;
        $next_invoices_display->envelope_scanning_amount = 0;
        $next_invoices_display->scanning_amount = 0;
        $next_invoices_display->additional_items_amount = 0;
        $next_invoices_display->shipping_handing_amount = 0;
        $next_invoices_display->storing_amount = 0;
        $next_invoices_display->additional_pages_scanning_amount = 0;
        if ($next_invoices) {
            // Postbox amount
            $next_invoices_display->postboxes_amount += empty ($next_invoices->free_postboxes_amount) ? 0 : $next_invoices->free_postboxes_amount;
            $next_invoices_display->postboxes_amount += empty ($next_invoices->private_postboxes_amount) ? 0 : $next_invoices->private_postboxes_amount;
            $next_invoices_display->postboxes_amount += empty ($next_invoices->business_postboxes_amount) ? 0 : $next_invoices->business_postboxes_amount;

            $next_invoices_display->postboxes_amount = $next_invoices_display->postboxes_amount;

            // Envelope scanning amount
            $next_invoices_display->envelope_scanning_amount += empty ($next_invoices->envelope_scan_free_account) ? 0 : $next_invoices->envelope_scan_free_account;
            $next_invoices_display->envelope_scanning_amount += empty ($next_invoices->envelope_scan_private_account) ? 0 : $next_invoices->envelope_scan_private_account;
            $next_invoices_display->envelope_scanning_amount += empty ($next_invoices->envelope_scan_business_account) ? 0 : $next_invoices->envelope_scan_business_account;

            $next_invoices_display->envelope_scanning_amount = $next_invoices_display->envelope_scanning_amount;

            // Item scanning amount
            $next_invoices_display->scanning_amount += empty ($next_invoices->item_scan_free_account) ? 0 : $next_invoices->item_scan_free_account;
            $next_invoices_display->scanning_amount += empty ($next_invoices->item_scan_private_account) ? 0 : $next_invoices->item_scan_private_account;
            $next_invoices_display->scanning_amount += empty ($next_invoices->item_scan_business_account) ? 0 : $next_invoices->item_scan_business_account;

            $next_invoices_display->scanning_amount = $next_invoices_display->scanning_amount ;

            // Additional item amount
            $next_invoices_display->additional_items_amount += empty ($next_invoices->incomming_items_free_account) ? 0 : ($next_invoices->incomming_items_free_account);
            $next_invoices_display->additional_items_amount += empty ($next_invoices->incomming_items_private_account) ? 0 : ($next_invoices->incomming_items_private_account );
            $next_invoices_display->additional_items_amount += empty ($next_invoices->incomming_items_business_account) ? 0 : ($next_invoices->incomming_items_business_account );

            $next_invoices_display->additional_items_amount = $next_invoices_display->additional_items_amount;

            // Additional item scanning amount
            $next_invoices_display->additional_pages_scanning_amount += empty ($next_invoices->additional_pages_scanning_free_amount) ? 0 : ($next_invoices->additional_pages_scanning_free_amount);
            $next_invoices_display->additional_pages_scanning_amount += empty ($next_invoices->additional_pages_scanning_private_amount) ? 0 : ($next_invoices->additional_pages_scanning_private_amount);
            $next_invoices_display->additional_pages_scanning_amount += empty ($next_invoices->additional_pages_scanning_business_amount) ? 0 : ($next_invoices->additional_pages_scanning_business_amount);
            $next_invoices_display->additional_pages_scanning_amount = $next_invoices_display->additional_pages_scanning_amount;

            // Shipping handding amount
            $next_invoices_display->shipping_handing_amount += empty ($next_invoices->direct_shipping_free_account) ? 0 : $next_invoices->direct_shipping_free_account;
            $next_invoices_display->shipping_handing_amount += empty ($next_invoices->direct_shipping_private_account) ? 0 : $next_invoices->direct_shipping_private_account;
            $next_invoices_display->shipping_handing_amount += empty ($next_invoices->direct_shipping_business_account) ? 0 : $next_invoices->direct_shipping_business_account;
            $next_invoices_display->shipping_handing_amount += empty ($next_invoices->collect_shipping_free_account) ? 0 : $next_invoices->collect_shipping_free_account;
            $next_invoices_display->shipping_handing_amount += empty ($next_invoices->collect_shipping_private_account) ? 0 : $next_invoices->collect_shipping_private_account;
            $next_invoices_display->shipping_handing_amount += empty ($next_invoices->collect_shipping_business_account) ? 0 : $next_invoices->collect_shipping_business_account;

            $next_invoices_display->shipping_handing_amount = $next_invoices_display->shipping_handing_amount;

            // Storing amount
            $next_invoices_display->storing_amount += empty ($next_invoices->storing_letters_free_account) ? 0 : $next_invoices->storing_letters_free_account;
            $next_invoices_display->storing_amount += empty ($next_invoices->storing_letters_private_account) ? 0 : $next_invoices->storing_letters_private_account;
            $next_invoices_display->storing_amount += empty ($next_invoices->storing_letters_business_account) ? 0 : $next_invoices->storing_letters_business_account;
            $next_invoices_display->storing_amount += empty ($next_invoices->storing_packages_free_account) ? 0 : $next_invoices->storing_packages_free_account;
            $next_invoices_display->storing_amount += empty ($next_invoices->storing_packages_private_account) ? 0 : $next_invoices->storing_packages_private_account;
            $next_invoices_display->storing_amount += empty ($next_invoices->storing_packages_business_account) ? 0 : $next_invoices->storing_packages_business_account;
            // #472

            $next_invoices_display->storing_amount = $next_invoices_display->storing_amount;
        }

        $next_invoices_date = APUtils::viewDateFormat(strtotime(APUtils::getLastDayOfCurrentMonth()),$date_format);

        $data_response['result']['next_invoices_date']  = $next_invoices_date;

        $account_type = lang('account_type_'.$customer->account_type);
        $data_response['result']['account_type']  = $account_type;

        if ($customer->status === '1') {
            $customer_status = lang('customer.status.deleted');
        } else if ($customer->activated_flag === '1') {
            $customer_status =  lang('customer.activated');
        }
        else {
            $customer_status =  lang('customer.not_activated');
        }
        $data_response['result']['customer_status']  = $customer_status;

        if ($customer->charge_fee_flag === '1') {
            $customer_charge_fee =  lang('customer.charge');
        }
        else {
            $customer_charge_fee = lang('customer.no_charge');
        }
        $data_response['result']['customer_charge_fee']  = $customer_charge_fee;

        $next_invoices = $next_invoices_display;
        if($next_invoices){
            #1058 add multi dimension capability for admin
            $postboxes =  $currency_sign .' '. APUtils::view_convert_number_in_currency($next_invoices->postboxes_amount, $currency_short, $currency_rate, $decimal_separator );
            $envelope_scanning = $currency_sign .' '.  APUtils::view_convert_number_in_currency($next_invoices->envelope_scanning_amount, $currency_short, $currency_rate, $decimal_separator );
            $scanning = $currency_sign .' ' . APUtils::view_convert_number_in_currency($next_invoices->scanning_amount, $currency_short, $currency_rate, $decimal_separator );
            $additional_items = $currency_sign .' '. APUtils::view_convert_number_in_currency($next_invoices->additional_items_amount, $currency_short, $currency_rate, $decimal_separator );
            $additional_scanning_items = $currency_sign .' ' . APUtils::view_convert_number_in_currency($next_invoices->additional_pages_scanning_amount, $currency_short, $currency_rate, $decimal_separator );
            $shipping_handling = $currency_sign .' '. APUtils::view_convert_number_in_currency($next_invoices->shipping_handing_amount, $currency_short, $currency_rate, $decimal_separator );
            $storing_items = $currency_sign .' ' .  APUtils::view_convert_number_in_currency($next_invoices->storing_amount, $currency_short, $currency_rate, $decimal_separator );

            $total = $next_invoices->postboxes_amount;
            $total += $next_invoices->envelope_scanning_amount;
            $total += $next_invoices->scanning_amount;
            $total += $next_invoices->additional_items_amount;
            $total += $next_invoices->shipping_handing_amount;
            $total += $next_invoices->storing_amount;
            $total += $next_invoices->additional_pages_scanning_amount;
            #1058 add multi dimension capability for admin
            $current_total = $currency_sign.' '. APUtils::view_convert_number_in_currency($total, $currency_short, $currency_rate, $decimal_separator );

            $data_response['result']['postboxes']  = $postboxes;
            $data_response['result']['envelope_scanning']  = $envelope_scanning;
            $data_response['result']['scanning']  = $scanning;
            $data_response['result']['additional_items']  = $additional_items;
            $data_response['result']['additional_scanning_items']  = $additional_scanning_items;
            $data_response['result']['shipping_handling']  = $shipping_handling;
            $data_response['result']['storing_items']  = $storing_items;

            $data_response['result']['current_total']  = $current_total;

        }

        if ($customer->invoice_type == '1') {
           $standard_payment_method = "Credit Card";
        } else {
            $standard_payment_method =  "Invoice";
        }
        $data_response['result']['standard_payment_method']  = $standard_payment_method;

        $vat  = APUtils::getVatRateOfCustomer($customer->customer_id);
        $vat_rate = APUtils::number_format(($vat->rate)*100, 2,$decimal_separator).'%';

        $data_response['result']['vat']  = $vat;
        $data_response['result']['vat_rate']  = $vat_rate;

        if ($customer->activated_flag === '1') {
            $customer_activated =  lang('customer.activated');
        }
        else {
            $customer_activated = lang('customer.not_activated');
        }
        $data_response['result']['customer_activated']  = $customer_activated;

        $data_response['result']['next_invoices']  = $next_invoices_display;
        $data_response['result']['customer']  = $customer;
        $data_response['result']['main_postbox']  = $main_postbox;
        $data_response['result']['scan_item']  = $scan_item;
        $data_response['result']['dropbox_status']  = $dropbox_status;
        $data_response['result']['customer_cloud']  = empty($customer_cloud) ? null : $customer_cloud;
        $data_response['result']['customer_shipping_address']  = $customer_shipping_address;
        $data_response['result']['free_postbox_count']  = $free_postbox_count;
        $data_response['result']['private_postbox_count']  = $private_postbox_count;
        $data_response['result']['business_postbox_count']  = $business_postbox_count;
        $data_response['result']['decimal_separator']  = $decimal_separator;
        $data_response['result']['currency_sign']  = $currency_sign;
        $data_response['result']['date_format']  = $date_format;

        $data_response['message'] = "Successfully";

        return $data_response;
    }

     /**
      * #1180 create postbox history page like check item page
     * List all postbox history
     */
    public static function get_list_postbox_history($customer, $input_paging, $api_mobile = 0)
    {
        // Lang
        ci()->lang->load('customers/customer');

        // load model mailbox/postbox_history_activity_m
        ci()->load->model('mailbox/postbox_history_activity_m');

        $array_condition = array();
        if (!empty ($customer)) {
            $array_condition ["(customers.customer_id LIKE '%{$customer}%' OR customers.customer_code LIKE '%{$customer}%' OR customers.email LIKE '%{$customer}%' "
            . " OR postbox_history_activity.postbox_code LIKE '%{$customer}%'  OR postbox_history_activity.postbox_name LIKE '%{$customer}%' OR postbox_history_activity.name LIKE '%{$customer}%' OR postbox_history_activity.company LIKE '%{$customer}%')"] = null;
        }

        // update limit into user_paging.
        $limit = isset ($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
        APContext::updateAdminPagingSetting($limit);
        $input_paging ['limit'] = $limit;

        // Call search method
        $query_result = ci()->postbox_history_activity_m->get_postbox_history_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

        // Process output data
        $total = $query_result['total'];
        $rows = $query_result['data'];

        // Get output response
        $response = self::get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);

        // date format
        $date_format = APUtils::get_date_format_in_user_profiles();

        $i = 0;
        foreach ($rows as $row) {
            // date action
            $date = customers_api::get_date($row->action_date, $date_format . '- H:i:s');

            // action type
            if ($row->action_type === APConstants::POSTBOX_CREATE) {
                $action_type = lang('postbox.created');
            } else if ($row->action_type === APConstants::POSTBOX_DOWNGRADE_ORDER) {
                $action_type = lang('postbox.downgrade_ordered');
            } else if ($row->action_type === APConstants::POSTBOX_UPGRADE_ORDER) {
                $action_type = lang('postbox.upgrade_ordered');
            } else if ($row->action_type === APConstants::POSTBOX_DOWNGRADE) {
                $action_type = lang('postbox.downgraded');
            } else if ($row->action_type === APConstants::POSTBOX_UPGRADE) {
                $action_type = lang('postbox.upgraded');
            } else if ($row->action_type === APConstants::POSTBOX_DELETE_ORDER_BY_CUSTOMER) {
                $action_type = lang('postbox.delete_ordered_by_customer');
            } else if ($row->action_type === APConstants::POSTBOX_DELETE_ORDER_BY_SYSTEM) {
                $action_type = lang('postbox.delete_ordered_by_system');
            } else if ($row->action_type === APConstants::POSTBOX_DELETE) {
                $action_type = lang('postbox.deleted');
            } else if ($row->action_type === APConstants::POSTBOX_DEACTIVATED) {
                $action_type = lang('postbox.deactivated');
            } else if ($row->action_type === APConstants::POSTBOX_REACTIVATED) {
                $action_type = lang('postbox.reactivated');
            }

            // after type
            if($row->type === APConstants::ENTERPRISE_TYPE){
                $after_type = lang('postbox.enterprise');
            }else if ($row->type === APConstants::BUSINESS_TYPE) {
                $after_type = lang('postbox.business');
            } else if ($row->type === APConstants::PRIVATE_TYPE) {
                $after_type = lang('postbox.private');
            } else if ($row->type === APConstants::FREE_TYPE) {
                $after_type = lang('postbox.free');
            }
            $response->rows [$i] ['id'] = $row->id;
            $response->rows [$i] ['cell'] = array(
                $row->customer_id,
                $row->postbox_id,
                $row->customer_code,
                $row->postbox_code,
                $row->email,
                $row->name,
                $row->company,
                $action_type,
                $date,
                $after_type
            );

            $i++;
        }
        return  array(
            "web_postbox_history_list"    => $response
        );

    }

    /**
     * Get date.
     *
     * @param int $timestamp
     * @param unknown_type $date_format
     */
    public static function get_date($timestamp,$date_format)
    {
        $tmp_string_date = APUtils::viewDateFormat($timestamp, $date_format);

        return $tmp_string_date;
    }

    /**
     * Save customer shipping report.
     *
     * @param type $customer_id
     * @param type $envelope
     * @param type $shipping_service
     * @param type $tracking_number
     * @param type $postal_charge
     * @param type $completed_by
     * @return type
     */
    public static function save_customer_shipping_report($customer_id, $envelope, $shipping_service, $tracking_number, $postal_charge, $completed_by, $shipping_api_id, $shipping_credential_id){
        ci()->load->model("customers/customer_shipping_report_m");
        ci()->load->model("scans/envelope_m");
        ci()->load->model('shipping/shipping_carriers_m');
        ci()->load->model('mailbox/envelope_customs_m');
        ci()->load->model('shipping/shipping_credentials_m');

        if(empty($envelope)){
            return;
        }

        $location_id = $envelope->location_id;
        $custom_id = 0;

        if(empty($envelope->package_id)){
            // direct shippment
            $weight = $envelope->weight;

            // shipping type
            $type = 1;

            // soource package id.
            $source_package_id = $envelope->id;

            // declare custom
            $custom = ci()->envelope_customs_m->get_by_many(array(
                "customer_id" => $customer_id,
                "postbox_id" => $envelope->postbox_id,
                "shipping_type" => "1",
                "envelope_id" => $envelope->id
            ));
        }else{
            // collect shipment type.
            $weight = ci()->envelope_m->sum_by_many(array(
                "package_id" => $envelope->package_id
            ), "weight");

            // shipping type
            $type = 2;

            // source package id
            $source_package_id = $envelope->package_id;

            // declare custom.
            $custom = ci()->envelope_customs_m->get_by_many(array(
                "customer_id" => $customer_id,
                "postbox_id" => $envelope->postbox_id,
                "shipping_type" => "2",
                "package_id" => $envelope->package_id
            ));
        }

        if(!empty($custom)){
            $custom_id = $custom->id;
        }

        $shipping_credential = ci()->shipping_credentials_m->get($shipping_credential_id);

        $data = array(
            "customer_id" => $customer_id,
            "location_id" => $location_id,
            "carrier_id" => !empty($shipping_service->carrier_id) ? $shipping_service->carrier_id : null,
            "shipping_service_id" => !empty($shipping_service->id) ? $shipping_service->id : null,
            "tracking_number" => $tracking_number,
            "type" => $type,
            "weight" => $weight,
            "customs_id" => $custom_id,
            "postal_charge" => $postal_charge,
            "upcharge" => !empty($shipping_credential->percental_partner_upcharge) ? $shipping_credential->percental_partner_upcharge : null,
            "source_package_id" => $source_package_id,
            "shipping_date" => now(),
            "completed_by" => $completed_by,
            "shipping_api_id" => $shipping_api_id,
            "shipping_credential_id" => $shipping_credential_id
        );

        $report_check = ci()->customer_shipping_report_m->get_by_many(array(
            "customer_id" => $customer_id,
            "source_package_id" => $source_package_id,
        ));

        if(!empty($report_check)){
            ci()->customer_shipping_report_m->update_by_many(array(
                "customer_id" => $customer_id,
                "source_package_id" => $source_package_id,
            ), $data);
        }else{
            ci()->customer_shipping_report_m->insert($data);
        }
    }

    /**
     * only save tracking number for shipping report.
     *
     * @param type $customer_id
     * @param type $envelope
     * @param type $tracking_number
     */
    public static function save_tracking_number_customer_shipping_report($customer_id, $envelope, $tracking_number){
        if(empty($tracking_number)){
            return;
        }

        ci()->load->model("customers/customer_shipping_report_m");
        if(empty($envelope->package_id)){
            // check for direct shipment
            // soource package id.
            $source_package_id = $envelope->id;
            $type = 1;
        }else{
            // check for collect shipment.
            // source package id
            $source_package_id = $envelope->package_id;
            $type = 2;
        }

        ci()->customer_shipping_report_m->update_by_many(array(
            "customer_id" => $customer_id,
            "source_package_id" => $source_package_id,
            "type" => $type
        ), array(
            "tracking_number" => $tracking_number
        ));
    }

    public static function updateCustomerUser($data){
        ci()->load->model('customers/customer_user_m');
        $customer_id = $data['customer_id'];
        unset($data['customer_id']);

        if($customer_id){
            // update user
            ci()->customer_user_m->update_by_many(array(
                'customer_id' => $customer_id
            ), $data);
        }else{
            // insert new user.
            $data["created_date"] = now();
            $customer_id = ci()->customer_user_m->insert($data);
        }
        return $customer_id;
    }

    /**
     * Get user phone id.
     *
     * @param type $customer_id
     *      Clevvermail customer id
     * @param type $clevvermail_user_id
     *      Clevvermail user id
     */
    public static function getUserPhoneIdByCLUserId($parent_customer_id, $clevvermail_user_id) {
        ci()->load->model('customers/phone_customer_user_m');
        $customer_user = ci()->phone_customer_user_m->get_by_many(array('parent_customer_id' => $parent_customer_id, 'customer_id' => $clevvermail_user_id));
        if (empty($customer_user)) {
            return '';
        }
        return $customer_user->phone_user_id;
    }

    public static function countPostboxByLocation($location_id)
    {
        ci()->load->model("mailbox/postbox_m");

        $total = ci()->postbox_m->count_by_many(array(
            "postbox.deleted <> '1'" => null,
            'location_available_id' => $location_id
        ));

        return $total;
    }


    /**
     * Activate customer by id
     *
     * @param unknown_type $customer_id
     */
    public static function reactivateCustomer($customer_id, $created_by_id = null) {

        ci()->load->model('customers/customer_m');
        ci()->load->model('mailbox/postbox_m');

        $customer = ci()->customer_m->get_by_many(array('customer_id' => $customer_id,
            '(status is NULL or status <> 1)' => NULL,
            'activated_flag' => APConstants::OFF_FLAG,
            '(deactivated_type = "manual" or deactivated_type = "auto")' => NULL
        ));

        /*ci()->customer_m->update_by_many(array(
            "customer_id" => $customer_id,
            "shipping_address_completed" => APConstants::ON_FLAG,
            "invoicing_address_completed" => APConstants::ON_FLAG,
            "postbox_name_flag" => APConstants::ON_FLAG,
            "name_comp_address_flag" => APConstants::ON_FLAG,
            "city_address_flag" => APConstants::ON_FLAG,
            "payment_detail_flag" => APConstants::ON_FLAG,
            "email_confirm_flag" => APConstants::ON_FLAG,
            "accept_terms_condition_flag" => APConstants::ON_FLAG
        ), array(
            "deactivated_type" => "",
            "deactivated_date" => null,
            "activated_flag" => APConstants::ON_FLAG,
            "last_updated_date" => now()
        ));*/

        // update: convert registration process flag to customer_product_setting.
        /*
         * #1180 create postbox history page like check item page
         *   Activity: reactivated
         */
        $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;
        CustomerProductSetting::doActiveCustomer($customer_id, $created_by_id);
    }

    /**
     * Set up automatic credit card transfers for enterprise deposits
     */
    public static function auto_payment_transfer_for_enterprise_customer() {
        ci()->load->library('payment/payone');
        // Get all customers
        $customers = CustomerUtils::getlistAutomaticChargeCustomer();

        // For each customer
        if (empty($customers) || count($customers) == 0) {
            return;
        }
        foreach ($customers as $customer) {
            $customer_id = $customer->customer_id;
            $limit_amount = $customer->alias02;
            $charge_amount = $customer->alias03;
            // Check auto deposit condition
            $auto_deposit_flag = CustomerUtils::checkAutomaticChargeCustomer($customer_id, $limit_amount);
            if (!$auto_deposit_flag) {
                continue;
            }

            // Charge customer
            $invoice_id = APUtils::genetateReferenceForOpenBalance($customer_id);
            ci()->payone->authorize($customer_id, $invoice_id, $charge_amount);
        }
    }

    /**
     * save_selection_product_register
     */
    public static function save_selection_product_register($customer_id, $product_type){
        // $product =
        // 1: clevvermail postbox product
        // 2:clevverphone number product
        CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, APConstants::SELECTION_CLEVVER_PRODUCT, $product_type);
    }

    /**
     * Register new customer.
     */
    public static function registerNewCustomer($email, $password, $google_click_id='', $partner_login_id='', $partner_login_type=''){
        ci()->load->model(array(
            'customers/customer_m',
            'mailbox/postbox_m',
            'mailbox/postbox_setting_m',
            'email/email_m'
        ));
        $activated_key = APUtils::generateRandom(30);

        // TODO: validate partner login id ( google id or facebook id).


        // open new transaction.
        ci()->postbox_m->db->trans_begin();

        //#1309: Insert customer history
        $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;

        // Insert new customer
        $customer_id = ci()->customer_m->insert( array(
            "email" => $email,
            "user_name" => $email,
            "password" => md5($password),
            "google_click_id" => $google_click_id,
            "account_type" => APConstants::NORMAL_CUSTOMER,
            "charge_fee_flag" => APConstants::ON_FLAG,
            "activated_key" => $activated_key,
            "required_verification_flag" => APConstants::ON_FLAG,
            "auto_send_invoice_flag" => APConstants::ON_FLAG,
            "accept_terms_condition_flag" => APConstants::ON_FLAG,
            "created_date" => now(),
            "last_updated_date" => now()
        ), null, $created_by_id);

        // Insert default postbox
        $postbox_id = ci()->postbox_m->insert( array(
            "customer_id" => $customer_id,
            "postbox_name" => "",
            "name" => "",
            "company" => "",
            "type" => APConstants::FREE_TYPE,
            "is_main_postbox" => 1,
            "location_available_id" => 1,// default berlin location
            "name_verification_flag" => APConstants::OFF_FLAG,
            "company_verification_flag" => APConstants::OFF_FLAG,
            "first_location_flag" => 1,
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

        // Start update ticket #563
        //CaseUtils::start_verification_case($customer_id);

        /*
         * #1180 create postbox history page like check item page
         * Insert default postbox into postbox_history_activity table (created)
         */
        // CustomerUtils::actionPostboxHistoryActivity($postbox_id, APConstants::POSTBOX_CREATE, now(), APConstants::FREE_TYPE, APConstants::INSERT_POSTBOX);
        customers_api::addPostboxHistory($postbox_id, APConstants::POSTBOX_CREATE, APConstants::FREE_TYPE);

        // save account setting: partner login id.
        if(!empty($partner_login_id)){
            if($partner_login_type == 'googleplus'){
                AccountSetting::set($customer_id, 'facebook_login_id', $partner_login_id);
            } else if($partner_login_type == 'facebook'){
                AccountSetting::set($customer_id, 'google_login_id', $partner_login_id);
            }
        }

        // commit transaction
        if(ci()->postbox_m->db->trans_status() == FALSE){
            ci()->postbox_m->db->trans_rollback();
        }else{
            ci()->postbox_m->db->trans_commit();

            // Do send email confirmation if success transaction.
            $email_template = ci()->email_m->get_by('slug', APConstants::new_customer_register);
            $activated_url = APContext::getFullBalancerPath() . "customers/active?key=" . $activated_key;
            $data = array(
                "full_name" => $email,
                "email" => $email,
                "password" => $password,
                "active_url" => $activated_url,
                "site_url" => APContext::getFullBalancerPath()
            );
            $content = APUtils::parserString($email_template->content, $data);
            MailUtils::sendEmail('', $email, $email_template->subject, $content);
        }

        return array(
            "customer_id" => $customer_id,
            "postbox_id" => $postbox_id
        );
    }

    /**
     * Register partner customer
     */
    public static function registerPartnerCustomer($customer_id, $p_code='', $p_website='', $p_bonus_code=''){
        ci()->load->model(array(
            'partner/partner_marketing_profile_m',
            'partner/partner_m',
            'widget/partner_customer_m',
        ));
        log_audit_message(APConstants::LOG_DEBUG, "partner code: ".$p_code.", partner site: ".$p_website.", p_bonus_code: ".$p_bonus_code, false, 'partner-customer-register');
        if(empty($p_code) && empty($p_website) && empty($p_bonus_code)){
            return null;
        }

        $partner_id = 0;
        if(!empty($p_bonus_code)){
            $tmp_partner = ci()->partner_m->get_by_many(array(
                "partner_code" => $p_bonus_code
            ));

            if(!empty($tmp_partner)){
                $partner_id = $tmp_partner->partner_id;
                $partner_profile = ci()->partner_marketing_profile_m->get_by_many(array("partner_id" => $partner_id));
            }
        } else if ($p_code) {
            // gets partner
            $partner_profile = ci()->partner_marketing_profile_m->get_by_many(array("token" => $p_code));

            if ($partner_profile) {
                $partner_id = $partner_profile->partner_id;
            }
        } else if ($p_website) {
            // check partner website.
            $partner_profile = ci()->partner_marketing_profile_m->get_by_many(array("partner_domain" => $p_website));

            if ($partner_profile) {
                $partner_id = $partner_profile->partner_id;
            }
        }
        if (!empty($partner_id)) {
            $check_partner = ci()->partner_m->get_by_many(array(
                "partner_id" => $partner_id
            ));

            if ($check_partner) {
                ci()->partner_customer_m->insert(array(
                    "customer_id" => $customer_id,
                    "partner_id" => $partner_id,
                    "customer_discount" => $partner_profile->customer_discount,
                    "duration_customer_discount" => $partner_profile->duration_customer_discount,
                    "duration_rev_share" => $partner_profile->duration_rev_share,
                    "rev_share_ad" => $partner_profile->rev_share_ad,
                    "comission_for_registration" => $partner_profile->registration,
                    "comission_for_activation" => $partner_profile->activation,
                    "end_flag" => APConstants::OFF_FLAG,
                    "created_date" => now()
                ));
            }
        }

        return $partner_id;
    }

    /**
     * #1180 create postbox history page like check item page
     * List all postbox history
     */
    public static function get_list_customer_history($customer, $input_paging, $api_mobile = 0)
    {
        // load model customers/customer_history_m
        ci()->load->model('customers/customer_history_m');
        ci()->load->model('users/user_m');

        $array_condition = array();
        if (!empty ($customer)) {
            $array_condition ["(cus.customer_id LIKE '%{$customer}%' OR cus.email LIKE '%{$customer}%')"] = null;
        }

        // update limit into user_paging.
        $limit = isset ($_REQUEST ['rows']) ? $_REQUEST ['rows'] : APContext::getAdminPagingSetting();
        APContext::updateAdminPagingSetting($limit);
        $input_paging ['limit'] = $limit;

        // Call search method
        $query_result = ci()->customer_history_m->get_customer_history_paging($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

        // Process output data
        $total = $query_result['total'];
        $rows = $query_result['data'];

        // Get output response
        $response = self::get_paging_output($total, $input_paging ['limit'], $input_paging ['page']);

        // date format
        $date_format = APUtils::get_date_format_in_user_profiles();
        $i = 0;
        foreach ($rows as $row) {
            // date action
            $date = customers_api::get_date($row->created_date, $date_format . '- H:i:s');
            // find created name

            if(empty($row->created_by_id)){
                $created_by = self::getCustomerByID($row->customer_id)->user_name;
            }else if($row->created_by_id < 0){
                $created_by = 'System';
            }else{
                $created_by = ci()->user_m->get_by('id', $row->created_by_id)->username;
            }

            $response->rows [$i] ['id'] = $row->id;
            $response->rows [$i] ['cell'] = array(
                $row->customer_id,
                $row->customer_code,
                $row->email,
                language($row->action_type),
                $row->old_data,
                $row->current_data,
                $created_by,
                $date,
            );

            $i++;
        }
        return  $response;
    }

    /**
     * Insert record to customer_history table
     * @param $customer_id
     * @param $action_type
     */
    public static function insertCustomerHistory($history_list = array()){
        // load model customers/customer_history_m
        ci()->load->model('customers/customer_history_m');
        $result = null;

        foreach($history_list as $cus_history){
            if(empty($cus_history['customer_id']))
                $cus_history['customer_id'] = 0;
            if(empty($cus_history['action_type']))
                $cus_history['action_type'] = APConstants::CUSTOMER_HISTORY_ACTIVITY_UNDEFINED;
            if(empty($cus_history['current_data']))
                $cus_history['current_data'] = null;
            if(empty($cus_history['old_data']))
                $cus_history['old_data'] = null;
            if(empty($cus_history['created_by_id']))
                $cus_history['created_by_id'] = APConstants::CUSTOMER_HISTORY_CREATED_BY_CUSTOMER;
                $cus_history['created_date'] = now();

            switch($cus_history['action_type']){
                case APConstants::CUSTOMER_HISTORY_ACTIVITY_CREATE :
                case APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_PASSWORD :
                case APConstants::CUSTOMER_HISTORY_ACTIVITY_DELETE :
                case APConstants::CUSTOMER_HISTORY_ACTIVITY_ADD_CREDITCARD :
                case APConstants::CUSTOMER_HISTORY_ACTIVITY_REMOVE_CREDITCARD :
                    $result = ci()->customer_history_m->insert($cus_history);
                    break;
                case APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_EMAIL :
                case APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_STATUS :
                    $where = "customer_id = {$cus_history['customer_id']} AND action_type = '{$cus_history['action_type']}'";
                    $last_record = ci()->customer_history_m->getLastestRecord($where);
                    if(!empty($last_record) && empty($cus_history['old_data'])){
                        $cus_history['old_data'] = $last_record->current_data;
                    }
                    $result = ci()->customer_history_m->insert($cus_history);
                    break;
                case APConstants::CUSTOMER_HISTORY_ACTIVITY_UPDATE_VAT_NUMBER :
                case APConstants::CUSTOMER_HISTORY_ACTIVITY_REMOVE_VAT_NUMBER :
                    $where = "customer_id = {$cus_history['customer_id']} AND action_type IN ( '"
                        . APConstants::CUSTOMER_HISTORY_ACTIVITY_REMOVE_VAT_NUMBER . "','"
                        . APConstants::CUSTOMER_HISTORY_ACTIVITY_UPDATE_VAT_NUMBER ."')";
                    $last_record = ci()->customer_history_m->getLastestRecord($where);
                    if(!empty($last_record) && empty($cus_history['old_data'])){
                        $cus_history['old_data'] = $last_record->current_data;
                    }
                    $result = ci()->customer_history_m->insert($cus_history);
                    break;
                case APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_PRIMARY_CREDITCARD :
                    $where = "customer_id = {$cus_history['customer_id']} AND action_type IN ( '"
                        . APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_PAYMENT_METHOD . "','"
                        . APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_PRIMARY_CREDITCARD ."')";
                    $last_record = ci()->customer_history_m->getLastestRecord($where);
                    if(!empty($last_record) && empty($cus_history['old_data'])){
                        if(($last_record->current_data != APConstants::CUSTOMER_HISTORY_PAYMENT_METHOD_INVOICE)
                        &&($last_record->current_data != APConstants::CUSTOMER_HISTORY_PAYMENT_METHOD_CREDIT_CARD)){
                            $cus_history['old_data'] = $last_record->current_data;
                        }
                    }
                    $result = ci()->customer_history_m->insert($cus_history);
                    break;
                case APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_PAYMENT_METHOD :
                    $where = "customer_id = {$cus_history['customer_id']} AND action_type = '{$cus_history['action_type']}'";
                    $last_record = ci()->customer_history_m->getLastestRecord($where);
                    if(!empty($last_record) && empty($cus_history['old_data'])){
                        if ($last_record->current_data == $cus_history['current_data'])
                            continue;
                        $cus_history['old_data'] = $last_record->current_data;
                    }
                    $result = ci()->customer_history_m->insert($cus_history);
                    break;
            }
        }
        return $result;
    }

    /*************************************
    * Add Psot box history
    * @$action_type :
    * 1. POSTBOX_CREATE
    * 2. POSTBOX_DOWNGRADE_ORDER
    * 3. POSTBOX_UPGRADE_ORDER
    * 4. POSTBOX_DOWNGRADE
    * 5. POSTBOX_UPGRADE
    * 6. POSTBOX_DELETE(customer/system)
    * 7. PPOSTBOX_DEACTIVATED
    * 8. POSTBOX_REACTIVATED
    * @$postbox_id = Id of postbox
    * @$old_account_type : Old account
    * @$pre_action: update || insert
    **************************************/
    public static function addPostboxHistory($postbox_id, $action_type, $new_ps_account_type, $old_ps_account_type = NULL, $pre_action = APConstants::INSERT_POSTBOX)
    {
        $created_date = time();
        switch($action_type) {
            case APConstants::POSTBOX_CREATE: { // Insert boxbox: register user, add postbox in setting, address, localtion screen
                if ($pre_action == APConstants::INSERT_POSTBOX) {
                    CustomerUtils::actionPostboxHistoryActivity($postbox_id, APConstants::POSTBOX_CREATE, $created_date, $new_ps_account_type, $pre_action);
                }
                break;
            }
            case APConstants::POSTBOX_DOWNGRADE_ORDER: {
                /* From Businese to lowlever account Private/As you go
                * From Private to As  you go
                *******************************************************/
                if ($old_ps_account_type === APConstants::BUSINESS_TYPE && in_array($new_ps_account_type, [APConstants::PRIVATE_TYPE, APConstants::FREE_TYPE])) {
                    //  downgrade ordered from  AS YOU GO, private to bussiness
                    CustomerUtils::actionPostboxHistoryActivity($postbox_id, APConstants::POSTBOX_DOWNGRADE_ORDER, now(), $new_ps_account_type, $pre_action);

                } else if ($old_ps_account_type === APConstants::PRIVATE_TYPE && $new_ps_account_type === APConstants::FREE_TYPE) {
                    //  downgrade ordered from  private to AS YOU GO
                    CustomerUtils::actionPostboxHistoryActivity($postbox_id, APConstants::POSTBOX_DOWNGRADE_ORDER, now(), $new_ps_account_type, $pre_action);
                }
                break;
            }
            case APConstants::POSTBOX_UPGRADE_ORDER: {
                /*
                * Private account will register upgrade order in next month
                ***/
                if ($old_ps_account_type === APConstants::PRIVATE_TYPE && $new_ps_account_type === APConstants::BUSINESS_TYPE) {
                    // Upgrade ordered from  private to bussiness
                     CustomerUtils::actionPostboxHistoryActivity($postbox_id, APConstants::POSTBOX_UPGRADE_ORDER, now(), $new_ps_account_type, $pre_action);
                }
                break;
            }
            case APConstants::POSTBOX_DOWNGRADE: {
                if (in_array($old_ps_account_type, [APConstants::BUSINESS_TYPE, APConstants::PRIVATE_TYPE]) && $new_ps_account_type != APConstants::BUSINESS_TYPE || old_ps_account_type == APConstants::ENTERPRISE_TYPE) {
                    if (($old_ps_account_type == APConstants::BUSINESS_TYPE && in_array($new_ps_account_type, [APConstants::PRIVATE_TYPE, APConstants::FREE_TYPE])) || ($old_ps_account_type == APConstants::PRIVATE_TYPE && $new_ps_account_type == APConstants::FREE_TYPE) || $old_ps_account_type == APConstants::ENTERPRISE_TYPE) {
                        // Downgrade from bussiness to lower level
                        CustomerUtils::actionPostboxHistoryActivity($postbox_id, APConstants::POSTBOX_DOWNGRADE, now(), $new_ps_account_type, $pre_action);
                    }
                }
                break;
            }
            case APConstants::POSTBOX_UPGRADE: {
                if ($old_ps_account_type == APConstants::FREE_TYPE && in_array($new_ps_account_type, [APConstants::PRIVATE_TYPE, APConstants::BUSINESS_TYPE]) || $new_ps_account_type == APConstants::ENTERPRISE_TYPE) {
                    /*
                     * #1180 create postbox history page like check item page
                     *  Upgrade order from AS YOU GO to private or bussiness
                     */
                    CustomerUtils::actionPostboxHistoryActivity($postbox_id, APConstants::POSTBOX_UPGRADE, now(),$new_ps_account_type, $pre_action);
                }
                if ($old_ps_account_type == APConstants::PRIVATE_TYPE && $new_ps_account_type == APConstants::BUSINESS_TYPE) {
                    // Upgrade from Private to Businese
                    CustomerUtils::actionPostboxHistoryActivity($postbox_id, APConstants::POSTBOX_UPGRADE, now(),$new_ps_account_type, $pre_action);
                }
                break;
            }
            case $action_type == APConstants::POSTBOX_DELETE || $action_type == APConstants::POSTBOX_DELETE_ORDER_BY_CUSTOMER  || $action_type == APConstants::POSTBOX_DELETE_ORDER_BY_SYSTEM : {
                CustomerUtils::actionPostboxHistoryActivity($postbox_id, $action_type, now(),$new_ps_account_type, $pre_action);
                break;
            }

            case $action_type == APConstants::POSTBOX_DEACTIVATED || $action_type == APConstants::POSTBOX_REACTIVATED : {
                if ($action_type == APConstants::POSTBOX_REACTIVATED) {
                    $postbox = CustomerUtils::getLastStatusPostbox($postbox_id);
                    if (! empty($postbox) && $postbox->action_type == APConstants::POSTBOX_DEACTIVATED) {
                        CustomerUtils::actionPostboxHistoryActivity($postbox_id, $action_type, now(),$new_ps_account_type, $pre_action);
                    }
                } else {
                    if (! empty($postbox) && $postbox->action_type != APConstants::POSTBOX_DEACTIVATED) {
                        // To do multiple postbox deactive when status is deactive
                    }
                    CustomerUtils::actionPostboxHistoryActivity($postbox_id, $action_type, now(),$new_ps_account_type, $pre_action);
                }
                break;
            }
        }
    }
}