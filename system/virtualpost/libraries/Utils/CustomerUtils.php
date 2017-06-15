<?php

defined('BASEPATH') or exit('No direct script access allowed');

class CustomerUtils {

    /**
     * delete customer function.
     * @param type $customerId
     * @param type $isDirectDelete
     * @param type $addBlacklistFlag
     * @param type $charge_flag
     * @return typed
     */
    public static function deleteUser($parent_customer_id, $customerId, $isDirectDelete = false, $addBlacklistFlag = false, $charge_flag = 1, $deleted_by = 0) {
        // load libs
        ci()->load->model('customers/customer_m');
        ci()->load->library('account/account_api');

        // set delete flag.
        ci()->customer_m->update_by_many(array(
            "customer_id" => $customerId
        ), array(
            'status' => 1,
            'plan_delete_date' => null,
            'deleted_date' => now(),
            "deleted_by" => $deleted_by
        ));

        // UnAssign Postbox and Assign to main account
        account_api::unassign_postbox_to_user($parent_customer_id, $customerId);

        // UnAssign Phone Number
        account_api::unassign_phonenumber_byuser($parent_customer_id, $customerId);

        // UnAssign Phones
        account_api::unassign_phones_byuser($parent_customer_id, $customerId);
    }

    /**
     * delete customer function.
     * @param type $customerId
     * @param type $isDirectDelete
     * @param type $addBlacklistFlag
     * @param type $charge_flag
     * @return typed
     */
    public static function deleteCustomer($customerId, $isDirectDelete = false, $addBlacklistFlag = false, $charge_flag = 1, $deleted_by = 0, $created_by_id = null) {
        // load libs
        ci()->load->library('invoices/invoices_api');
        ci()->load->model('invoices/invoice_summary_m');
        ci()->load->model('invoices/invoice_summary_by_location_m');
        ci()->load->library('invoices/export');
        ci()->load->library('customers/customers_api');

        $total = CustomerUtils::getAdjustOpenBalanceDue($customerId);
        $balance = $total['OpenBalanceDue'] + $total['OpenBalanceThisMonth'];
        $openCurrentThisMonth = $total['OpenBalanceThisMonth'];
        log_audit_message(APConstants::LOG_INFOR, 'Customer ID: ' . $customerId . '| OpenBalanceDue:'
                . $total['OpenBalanceDue'] . '| Open Balance This Month:' . $total['OpenBalanceThisMonth'] . ", black_list:"
                . $addBlacklistFlag . ", charge_flag: " . $charge_flag . ', direct delete:' . $isDirectDelete, FALSE, 'delete-customer');

        // if customer delete his account and he has balance > 0, ==> do not delete his account.
        if (!$isDirectDelete && $balance > 0) {
            log_audit_message(APConstants::LOG_INFOR, 'Can not direct delete Customer ID: ' . $customerId);
            return sprintf(lang('customer.delete_error.balance'));
        }

        // Gets customer information.
        $customer = customers_api::getCustomerByID($customerId);
        $customerVat = CustomerUtils::getVatRateOfCustomer($customerId);
        $vat = $customerVat->rate;
        $vatCase = $customerVat->vat_case_id;

        $chargeSuccessFlag = false;

        // check user enteprise
        $is_user_enterprise = false;
        $is_enterprise_account = false;
        if (!empty($customer->parent_customer_id) && $customer->account_type == APConstants::ENTERPRISE_CUSTOMER) {
            $is_user_enterprise = true;
        }

        if (empty($customer->parent_customer_id) && $customer->account_type == APConstants::ENTERPRISE_CUSTOMER) {
            $is_enterprise_account = true;
        }

        // try to payment by payone before when admin delete customer.
        if (!$is_user_enterprise) {
            if ($isDirectDelete && $balance > 0) {
                log_audit_message(APConstants::LOG_INFOR, 'Admin delete Customer ID: ' . $customerId . '| Balance:' . $balance);

                // make payment with payone if possible.
                if ($chargeSuccessFlag == '1') {
                    $chargeSuccessFlag = CustomerUtils::makePaymentWithPayone($customer, $balance);
                }

                // create credit note when admin delete customer.
                if (!$chargeSuccessFlag) {
                    log_audit_message(APConstants::LOG_INFOR, '>>> Charge Payone Fail 01 (Update). Customer ID: ' . $customerId . '| Open Balance This Month:' . ($openCurrentThisMonth / (1 + $vat)));
                    // create credit note.
                    $invoiceSummaryId = ci()->invoice_summary_m->insert(array(
                        "vat" => $vat,
                        "vat_case" => $vatCase,
                        "customer_id" => $customerId,
                        "invoice_month" => APUtils::getCurrentYearMonthDate(),
                        "payment_1st_flag" => APConstants::OFF_FLAG,
                        "payment_1st_amount" => $balance / (1 + $vat),
                        "payment_2st_flag" => APConstants::OFF_FLAG,
                        "payment_2st_amount" => 0,
                        "total_invoice" => (-1) * ($balance) / (1 + $vat),
                        "invoice_type" => '2'
                    ));
                    $invoiceCode = APUtils::generateInvoiceCodeById($invoiceSummaryId, true);
                    ci()->invoice_summary_m->update_by_many(array(
                        "id" => $invoiceSummaryId
                            ), array(
                        "invoice_code" => $invoiceCode
                    ));

                    log_audit_message(APConstants::LOG_INFOR, '>>> Charge Payone Fail 02 (Insert Credit Note). Customer ID: ' . $customerId . '| Open Balance This Month:' . ((-1) * ($balance) / (1 + $vat)) . '| Invoice Code:' . $invoiceCode);

                    // Insert credit detail manual and credit note by location
                    CustomerUtils::createCreditNoteByLocation($customerId, $balance, $invoiceSummaryId, $invoiceCode, $customerVat);

                    // export credit note.
                    ci()->export->export_invoice($invoiceCode, $customerId);
                }
            } else {
                log_audit_message(APConstants::LOG_INFOR, 'CronJob or SeftDelete Customer ID: ' . $customerId . '| Balance:' . $balance);

                // only create invoice when customer delete or Cronjob delete account.
                if ($balance < 0) {
                    // create manual invoice.
                    $invoiceSummaryId = ci()->invoice_summary_m->insert(array(
                        "customer_id" => $customerId,
                        "invoice_month" => APUtils::getCurrentYearMonthDate(),
                        "vat" => $vat,
                        "vat_case" => $vatCase,
                        "payment_1st_flag" => APConstants::ON_FLAG,
                        "payment_1st_amount" => $balance / (1 + $vat),
                        "payment_2st_flag" => APConstants::ON_FLAG,
                        "payment_2st_amount" => 0,
                        "total_invoice" => (-1) * ($balance) / (1 + $vat),
                        "invoice_type" => '2'
                    ));

                    $invoiceCode = APUtils::generateInvoiceCodeById($invoiceSummaryId);
                    log_audit_message(APConstants::LOG_ERROR, 'CronJob or SeftDelete Customer ID: ' . $customerId . '| total_invoice:' . ((-1) * ($balance) / (1 + $vat)) . '| Invoice Code:' . $invoiceCode);
                    ci()->invoice_summary_m->update_by_many(array(
                        "id" => $invoiceSummaryId
                            ), array(
                        "invoice_code" => $invoiceCode
                    ));

                    // Insert invoice detail manual and invoice by location
                    CustomerUtils::createInvoiceByLocation($customerId, $balance, $invoiceSummaryId, $customerVat);

                    // export invoice.
                    ci()->export->export_invoice($invoiceCode, $customerId);
                } else {
                    // update current date invoice.
                    ci()->invoice_summary_m->update_by_many(array(
                        "LEFT(invoice_month, 6) = '" . APUtils::getCurrentYear() . APUtils::getCurrentMonth() . "'" => null,
                        "customer_id" => $customerId,
                        "invoice_type" => "1"
                            ), array(
                        "invoice_month" => APUtils::getCurrentYearMonthDate(),
                    ));

                    // update current date invoice_summary_by_location
                    ci()->invoice_summary_by_location_m->update_by_many(array(
                        "LEFT(invoice_month, 6) = '" . APUtils::getCurrentYear() . APUtils::getCurrentMonth() . "'" => null,
                        "customer_id" => $customerId,
                        "invoice_type" => "1"
                            ), array(
                        "invoice_month" => APUtils::getCurrentYearMonthDate(),
                    ));
                }
            }

            // send last invoice to customer when customer delete his account.
            if (!$isDirectDelete) {
                CustomerUtils::sendLastInvoiceToCustomer($customer);
            }
        }
        // If this is enterprise customer
        if($is_enterprise_account) {
            // Get all enterprise user
            $list_enterprise_users = CustomerUtils::getListCustomerIdOfEnterpriseCustomer($customerId);
            foreach ($list_enterprise_users as $user_id) {
                $user = CustomerUtils::getCustomerByID($user_id);

                if (!empty($user)) {
                    $parent_customer_id = $user->parent_customer_id;
                    CustomerUtils::deleteAllPostboxAndResourceOfCustomer($user, $isDirectDelete, $deleted_by);
                    self::deleteUser($customer->customer_id, $customerId, $isDirectDelete, $addBlacklistFlag, $charge_flag, $deleted_by);
                }
            }
        }
        // Add this customer to black list
        if ($addBlacklistFlag) {
            CustomerUtils::addCustomerToBlacklist($customer);
        }

        // delete all postbox and resouce of customer.
        $result = CustomerUtils::deleteAllPostboxAndResourceOfCustomer($customer, $isDirectDelete, $deleted_by);

        //#1309: Insert customer history
        $history['customer_id'] = $customerId;
        $history['action_type'] = APConstants::CUSTOMER_HISTORY_ACTIVITY_DELETE;
        $history['current_data'] = APConstants::CUSTOMER_HISTORY_STATUS_DELETED;
        $history['created_by_id'] = $created_by_id;
        customers_api::insertCustomerHistory([$history]);

        return array(
            "message" => $result,
            "charge_success_flag" => $chargeSuccessFlag
        );
    }

    /**
     * Create credit note by customer id
     */
    public static function createCreditNoteByCustomer($customerId, $balance, $description='') {
        ci()->load->model('invoices/invoice_summary_m');

        $customerVat = CustomerUtils::getVatRateOfCustomer($customerId);
        $vat = $customerVat->rate;
        $vatCase = $customerVat->vat_case_id;

        // create credit note.
        $invoiceCode = APUtils::generateInvoiceCodeById('', true);
        $invoiceSummaryId = ci()->invoice_summary_m->insert(array(
            "vat" => $vat,
            "vat_case" => $vatCase,
            "customer_id" => $customerId,
            "invoice_month" => APUtils::getCurrentYearMonthDate(),
            "payment_1st_flag" => APConstants::OFF_FLAG,
            "payment_1st_amount" => $balance / (1 + $vat),
            "payment_2st_flag" => APConstants::OFF_FLAG,
            "payment_2st_amount" => 0,
            "total_invoice" => (-1) * ($balance) / (1 + $vat),
            "invoice_type" => '2',
            "invoice_code" => $invoiceCode
        ));

        // Insert credit detail manual and credit note by location
        CustomerUtils::createCreditNoteByLocation($customerId, $balance, $invoiceSummaryId, $invoiceCode, $customerVat, $description);
    }

    /**
     * make one payment with payone
     *
     * @param unknown $customer
     * @param unknown $balance
     * @return boolean
     */
    public static function makePaymentWithPayone($customer, $balance) {
        ci()->load->library('payment/payone');

        if ($customer->invoice_type == '1') {
            $chargeInvoiceId = CustomerUtils::genetateReferenceForOpenBalance($customer->customer_id);
            $result = ci()->payone->authorize($customer->customer_id, $chargeInvoiceId, $balance);
            if ($result) {
                return true;
            }
        }

        return false;
    }

    public static function createCreditNoteByLocation($customerId, $balance, $invoice_summary_id, $invoice_code, $customerVat, $description='') {
        ci()->load->library('invoices/invoices_api');
        ci()->load->library('price/price_api');
        ci()->load->model("invoices/invoice_summary_by_location_m");
        ci()->load->model('invoices/invoice_detail_manual_m');

        $vat = $customerVat->rate;
        $vatCase = $customerVat->vat_case_id;
        $netBalance = $balance / (1 + $vat);

        // Gets invoice by location.
        $invoices = invoices_api::getTotalLocationInvoiceByCustomer($customerId);

        if(empty($description)){
            $description = 'auto-credit note';
        }
        if (!$invoices) {
            $location_id = APUtils::getPrimaryLocationBy($customerId);
            // get rev share of location
            $rev_share = price_api::getRevShareOfLocation($location_id);

            // Insert detail manual invoice
            ci()->invoice_detail_manual_m->insert(array(
                "customer_id" => $customerId,
                "created_date" => now(),
                "description" => $description,
                "quantity" => 1,
                "net_price" => (-1) * $netBalance,
                "vat" => $vat,
                "vat_case" => $vatCase,
                "rev_share" => $rev_share,
                "gross_price" => (-1) * $balance,
                "payment_flag" => APConstants::ON_FLAG,
                "payment_date" => now(),
                "invoice_date" => APUtils::getCurrentYearMonthDate(),
                "invoice_summary_id" => $invoice_summary_id,
                "location_id" => $location_id
            ));

            // create credit note by location.
            ci()->invoice_summary_by_location_m->insert(array(
                "customer_id" => $customerId,
                "invoice_code" => $invoice_code,
                "invoice_month" => APUtils::getCurrentYearMonthDate(),
                "vat" => $vat,
                "vat_case" => $vatCase,
                "rev_share" => $rev_share,
                "total_invoice" => (-1) * $netBalance,
                "invoice_type" => '2',
                "location_id" => $location_id
            ));

            return;
        }

        $total_invoice = 0;
        foreach ($invoices as $location => $invoice) {
            $total_invoice += $invoice->total_invoice;
        }

        foreach ($invoices as $location => $invoice) {
            $locationBalance = 0;
            if ($total_invoice > 0) {
                $locationBalance = $invoice->total_invoice * $netBalance / $total_invoice;
            }

            // get rev share of location
            $rev_share = price_api::getRevShareOfLocation($location);

            // Insert detail manual invoice
            ci()->invoice_detail_manual_m->insert(array(
                "customer_id" => $customerId,
                "created_date" => now(),
                "description" => $description,
                "quantity" => 1,
                "net_price" => (-1) * $locationBalance,
                "vat" => $vat,
                "vat_case" => $vatCase,
                "rev_share" => $rev_share,
                "gross_price" => (-1) * $locationBalance * (1 + $vat),
                "payment_flag" => APConstants::ON_FLAG,
                "payment_date" => now(),
                "invoice_date" => APUtils::getCurrentYearMonthDate(),
                "invoice_summary_id" => $invoice_summary_id,
                "location_id" => $location
            ));

            // create credit note by location.
            ci()->invoice_summary_by_location_m->insert(array(
                "customer_id" => $customerId,
                "invoice_summary_id" => $invoice_summary_id,
                "invoice_month" => APUtils::getCurrentYearMonthDate(),
                "vat" => $vat,
                "vat_case" => $vatCase,
                "rev_share" => $rev_share,
                "total_invoice" => (-1) * $locationBalance,
                "invoice_type" => '2',
                "location_id" => $location
            ));
        }
    }

    public static function createInvoiceByLocation($customerId, $balance, $invoiceSummaryId, $customerVat) {
        ci()->load->library('invoices/invoices_api');
        ci()->load->library('price/price_api');
        ci()->load->model("invoices/invoice_summary_by_location_m");
        ci()->load->model('invoices/invoice_detail_manual_m');

        $vat = $customerVat->rate;
        $vatCase = $customerVat->vat_case_id;
        $netBalance = $balance / (1 + $vat);

        // Gets invoice by location.
        $invoices = invoices_api::getTotalLocationInvoiceByCustomer($customerId);

        $description = 'auto-invoice';

        if (!$invoices) {
            $location_id = APUtils::getPrimaryLocationBy($customerId);
            // get rev share of location
            $rev_share = price_api::getRevShareOfLocation($location_id);

            // Insert detail manual invoice
            ci()->invoice_detail_manual_m->insert(array(
                "customer_id" => $customerId,
                "created_date" => now(),
                "description" => $description,
                "quantity" => 1,
                "net_price" => (-1) * $netBalance,
                "vat" => $vat,
                "vat_case" => $vatCase,
                "rev_share" => $rev_share,
                "gross_price" => (-1) * $balance,
                "payment_flag" => APConstants::ON_FLAG,
                "payment_date" => now(),
                "invoice_date" => APUtils::getCurrentYearMonthDate(),
                "invoice_summary_id" => $invoiceSummaryId,
                "location_id" => $location_id
            ));

            // create credit note by location.
            ci()->invoice_summary_by_location_m->insert(array(
                "customer_id" => $customerId,
                "invoice_summary_id" => $invoiceSummaryId,
                "invoice_month" => APUtils::getCurrentYearMonthDate(),
                "vat" => $vat,
                "vat_case" => $vatCase,
                "rev_share" => $rev_share,
                "total_invoice" => (-1) * $netBalance,
                "invoice_type" => '2',
                "location_id" => $location_id
            ));

            return;
        }

        $total_invoice = 0;
        foreach ($invoices as $location => $invoice) {
            $total_invoice += $invoice->total_invoice;
        }

        if ($total_invoice == 0) {
            return;
        }
        foreach ($invoices as $location => $invoice) {
            $locationBalance = $invoice->total_invoice * $netBalance / $total_invoice;

            // get rev share of location
            $rev_share = price_api::getRevShareOfLocation($location);

            // Insert detail manual invoice
            ci()->invoice_detail_manual_m->insert(array(
                "customer_id" => $customerId,
                "created_date" => now(),
                "description" => $description,
                "quantity" => 1,
                "net_price" => (-1) * $locationBalance,
                "vat" => $vat,
                "vat_case" => $vatCase,
                "rev_share" => $rev_share,
                "gross_price" => (-1) * $locationBalance * (1 + $vat),
                "payment_flag" => APConstants::ON_FLAG,
                "payment_date" => now(),
                "invoice_date" => APUtils::getCurrentYearMonthDate(),
                "invoice_summary_id" => $invoiceSummaryId,
                "location_id" => $location
            ));

            // create credit note by location.
            ci()->invoice_summary_by_location_m->insert(array(
                "customer_id" => $customerId,
                "invoice_summary_id" => $invoiceSummaryId,
                "invoice_month" => APUtils::getCurrentYearMonthDate(),
                "vat" => $vat,
                "vat_case" => $vatCase,
                "rev_share" => $rev_share,
                "total_invoice" => (-1) * $locationBalance,
                "invoice_type" => '2',
                "location_id" => $location
            ));
        }
    }

    /**
     * send last invoice to customer.
     *
     * @param unknown $invoiceCode
     * @param unknown $customerId
     */
    public static function sendLastInvoiceToCustomer($customer) {
        ci()->load->model('invoices/invoice_summary_m');
        ci()->load->library('invoices/export');

        $currentInvoice = ci()->invoice_summary_m->get_by_many(array(
            "customer_id" => $customer->customer_id,
            "invoice_month" => APUtils::getCurrentYearMonth(),
            "total_invoice > 0" => null,
            "invoice_type" => "1"
        ));

        if (!$currentInvoice) {
            $currentInvoice = ci()->invoice_summary_m->get_by_many(array(
                "customer_id" => $customer->customer_id,
                "(LEFT(invoice_month, 6) = '" . APUtils::getCurrentYearMonth() . "')" => null,
                "total_invoice > 0" => null
            ));
        }

        if ($currentInvoice) {
            $invoice_code = $currentInvoice->invoice_code;
            if ($currentInvoice->invoice_file_path) {
                $invoice_file_path = $currentInvoice->invoice_file_path;
            } else {
                $invoice_file_path = ci()->export->export_invoice($invoice_code, $customer->customer_id);
            }

            // only send invoice to customer.
            APUtils::send_email_invoices_monthly_report($customer, $invoice_file_path, 1, $invoice_code);
        }
    }

    /**
     * add customer to black list.
     * @param unknown $customer
     */
    public static function addCustomerToBlacklist($customer) {
        // Get email
        if ($customer) {
            ci()->load->model('customers/customer_blacklist_m');

            ci()->customer_blacklist_m->insert(array(
                "customer_id" => $customer->customer_id,
                "email" => $customer->email,
                "created_date" => now()
            ));
        }
    }

    /**
     * delete all information of customer.
     *
     * @return NULL
     */
    public static function deleteAllPostboxAndResourceOfCustomer($customer, $isDirectDelete, $deleted_by = 0) {
        // Load libs
        ci()->load->model('customers/customer_m');
        ci()->load->model('mailbox/postbox_m');
        ci()->load->model('payment/payment_m');

        // Gets customer id.
        $customerId = $customer->customer_id;

        // set delete flag.
        $success = ci()->customer_m->update_by_many(array(
            "customer_id" => $customerId
                ), array(
            'status' => 1,
            'plan_delete_date' => null,
            'deleted_date' => now(),
            "deleted_by" => $deleted_by
        ));
        if ($success) {
            // delete all postbox.
            $postboxes = ci()->postbox_m->get_many_by_many(array(
                "customer_id" => $customerId,
                "completed_delete_flag <> 1 " => null
            ));
            foreach ($postboxes as $postbox) {
                /*
                 * #1180 create postbox history page like check item page
                 *  Activity: delete ordered by system
                 */
                APUtils::deletePostbox($postbox->postbox_id, $customerId, APConstants::POSTBOX_DELETE_ORDER_BY_SYSTEM, $isDirectDelete);
            }

            // Delete all payment information
            ci()->payment_m->delete_by_many(array(
                "customer_id" => $customerId
            ));

            // cancel all cases
            CaseUtils::cancel_verification_case($customerId, 1);
        } else {
            return sprintf(lang('customer.delete_error'));
        }

        return "";
    }

    /**
     * Get vat rate of customer.
     *
     * @param unknown $customerId
     * @return number
     */
    public static function getVatRateOfCustomer($customer_id) {
        return APUtils::getVatRateOfCustomer($customer_id);
    }

    /**
     * Get open balance due after adjust.
     * ( gia tri tra ve da bao gom VAT- gross total)
     *
     * @param unknown_type $customer_id
     *            The customer ID
     */
    public static function getAdjustOpenBalanceDue($customer_id) {
        return APUtils::getAdjustOpenBalanceDue($customer_id);
    }

    /**
     * get primary location by customer id
     */
    public static function getPrimaryLocationBy($customer_id) {
        return APUtils::getPrimaryLocationBy($customer_id);
    }

    /**
     * Get customer logged information.
     */
    public static function getCustomerByID($customerId) {
        ci()->load->model('customers/customer_m');

        $customer = ci()->customer_m->get_by_many(array('customer_id' => $customerId));

        return $customer;
    }

    /**
     * Get customer logged information.
     */
    public static function getCustomerAddressByID($customerId) {
        ci()->load->model('customers/customers_address_m');

        $customer_address = ci()->customers_address_m->get_by_many(array('customer_id' => $customerId));

        return $customer_address;
    }

    /**
     * Get customer logged information.
     */
    public static function getPostboxByID($postboxId) {
        ci()->load->model('mailbox/postbox_m');

        $postbox = ci()->postbox_m->get_by_many(array('postbox_id' => $postboxId));

        return $postbox;
    }

    /**
     * Calculate total invoice
     *
     * @param unknown_type $invoice_summary
     */
    public static function genetateReferenceForOpenBalance($customer_id) {
        return APUtils::genetateReferenceForOpenBalance($customer_id);
    }


    /**
     * Estimate shipping cost of envelope.
     *
     * 5 EUR for national letters
     * 10 EUR for intn. Letters
     * 20 EUR for national packages
     * 50 EUR for intl packages
     *
     * @param $shipping_service: The shipping service (normal|fedex) (dont' use now)
     * @param unknown_type $shipping_type: The shipping type (direct|collect) (dont' use now)
     * @param unknown_type $list_envelope_id: The list of envelope id
     * @param unknown_type $customer_id
     */
    public static function estimateShippingCost($shipping_service, $shipping_type, $list_envelope_id, $customer_id, $include_all = false) {
        $estimated_type = 'estimated';
        $estimateResult = array(
            'cost' => 0,
            'estimated_type' => $estimated_type
        );
        // Load model
        ci()->load->model('scans/envelope_m');
        ci()->load->model('scans/envelope_prepayment_cost_m');
        ci()->load->model('addresses/location_m');
        ci()->load->model('mailbox/postbox_m');

        ci()->load->library('price/price_api');
        ci()->load->library('scans/scans_api');
        ci()->load->library('mailbox/mailbox_api');
        ci()->load->library('settings/settings_api');
        ci()->load->library('shipping/shipping_api');
        ci()->load->library('shipping/ShippingConfigs');

        // Get customer information
        $customer = CustomerUtils::getCustomerByID($customer_id);
        if (empty($customer)) {
            return $estimateResult;
        }

        // Get customer address information
        $customer_address = CustomerUtils::getCustomerAddressByID($customer_id);
        if (empty($customer_address)) {
            return $estimateResult;
        }

        // Get all setting type
        $all_envelope_type = Settings::get_list(APConstants::ENVELOPE_TYPE_CODE);
        $all_envelope_type_letter = array();
        $all_envelope_type_package = array();
        // Get all letter type & package type
        foreach ($all_envelope_type as $envelope_type) {
            if ($envelope_type->Alias02 == 'Letter') {
                $all_envelope_type_letter [] = $envelope_type->ActualValue;
            }
            if ($envelope_type->Alias02 == 'Package') {
                $all_envelope_type_package [] = $envelope_type->ActualValue;
            }
        }

        $total_cost = 0;
        $letter_national_price = Settings::get(APConstants::ESTIMATE_SHIPPING_COST_LETTER_NATIONAL);
        $letter_international_price = Settings::get(APConstants::ESTIMATE_SHIPPING_COST_LETTER_INNATIONAL);
        $package_national_price = Settings::get(APConstants::ESTIMATE_SHIPPING_COST_PACKAGE_NATIONAL);
        $package_international_price = Settings::get(APConstants::ESTIMATE_SHIPPING_COST_PACKAGE_INNATIONAL);
        // Using for collect shipping
        $shipping_customs_cost_fee = 0;
        // Calculate total item for collect shipping
        if ($shipping_type === APConstants::SHIPPING_TYPE_COLLECT) {
            $first_envelope_id = $list_envelope_id[0];
            // Get envelope
            $arr_condition = array(
                "id" => $first_envelope_id,
                "to_customer_id" => $customer_id
            );
            $envelope = ci()->envelope_m->get_by_many($arr_condition);
            $envelope_id = $envelope->id;

            // Get confirmed shipping cost
            $confrimed_total_cost = $envelope->shipping_rate;
            $shipping_rate_id = $envelope->shipping_rate_id;

            // Calculate customs handling fee
            $insurance_customs_cost = mailbox_api::get_total_customs_cost($customer_id, $envelope_id);

            if ($insurance_customs_cost > 0) {
                $postbox_id = $envelope->postbox_id;
                // Get postbox information
                $postbox = ci()->postbox_m->get($postbox_id);
                if (empty($postbox)) {
                    return $estimateResult;
                }

                $location_id = $postbox->location_available_id;
                // Update #1438
                // $pricing_map = price_api::getPricingModelByPostboxID($postbox_id);
                $pricing_map = price_api::getPricingModelByCusotomerAndLocationID($customer_id, $location_id);

                $postbox_type = $postbox->type;
                if ($insurance_customs_cost > 1000) {
                    $shipping_customs_cost_fee = $pricing_map [$postbox_type] ['custom_declaration_outgoing_01'];
                } else {
                    $shipping_customs_cost_fee = $pricing_map [$postbox_type] ['custom_declaration_outgoing_02'];
                }
            }
            if ($confrimed_total_cost > 0) {
                $estimateResult['cost'] = $confrimed_total_cost + $shipping_customs_cost_fee;
                $estimateResult['estimated_type'] = 'calculated';
                return $estimateResult;
            }

            $location_id = $envelope != null ? $envelope->location_id : 0;
            $list_all_collect_items = ci()->envelope_m->get_many_by_many(array(
                "to_customer_id" => $customer_id,
                "collect_shipping_flag" => '2',
                "location_id" => $location_id
            ));

            // Estimate cost at Fedex (Fedex including customs handling)
            $temp_total_cost = 0;

            // Check existing cache value
            $customers_address = shipping_api::getShippingAddressByEnvelope($customer_id, $envelope_id);
            $total_customs_cost = mailbox_api::get_total_customs_cost($customer_id, $envelope_id);
            $shippingInfo = array(
                ShippingConfigs::CUSTOMER_ID => $customer_id,
                ShippingConfigs::CUSTOMS_VALUE => $total_customs_cost,
                ShippingConfigs::STREET => $customers_address->shipment_street,
                ShippingConfigs::POSTAL_CODE => $customers_address->shipment_postcode,
                ShippingConfigs::CITY => $customers_address->shipment_city,
                ShippingConfigs::REGION => $customers_address->shipment_region,
                ShippingConfigs::COUNTRY_ID => $customers_address->shipment_country,
                ShippingConfigs::LOCATION_ID => $location_id
            );
            $shipping_info_decode_key = json_encode($shippingInfo);
            $envelope_prepayment_cost = ci()->envelope_prepayment_cost_m->get_by_many(array(
                'customer_id' => $customer_id,
                'envelope_id' => $envelope_id,
                'shipping_info_decode_key' => $shipping_info_decode_key
            ));

            if (empty($shipping_rate_id) || $shipping_rate_id == '0') {
                $selected_shipping_service_obj = shipping_api::getShippingServiceIdByEnvelope($envelope_id);
                $selected_shipping_service_id = $selected_shipping_service_obj['shipping_service_id'];

                // Check empty cost
                if (empty($envelope_prepayment_cost) || empty($envelope_prepayment_cost->collect_shipping_cost)) {
                    try {
                        $cost_object = shipping_api::calculateCostOfAllServices($customer_id, $list_all_collect_items, $envelope_id, ShippingConfigs::COLLECT_SHIPPING);
                        if ($cost_object != null) {
                            $data = $cost_object['data'];
                            foreach ($data as $shipping_service_item_id => $item_cost) {
                                if ($shipping_service_item_id == $selected_shipping_service_id && !empty($item_cost['raw_total_charge']) && $item_cost['raw_total_charge'] > 0) {
                                    if ($temp_total_cost == 0) {
                                        $temp_total_cost = $item_cost['raw_total_charge'];
                                    } else {
                                        $temp_total_cost = min($item_cost['raw_total_charge'], $temp_total_cost);
                                    }
                                }
                            }
                        }
                    } catch (Exception $ex) {
                        log_audit_message(APConstants::LOG_INFOR, "Total Estimate FedexShippingCost: " . $shipping_type . ' of envelope id: ' . json_encode($list_envelope_id) . ' has error ' . $ex->getMessage(), FALSE, 'estimateShippingCost');
                    }
                } else {
                    $temp_total_cost = $envelope_prepayment_cost->collect_shipping_cost;
                }
            }
            if ($temp_total_cost > 0) {
                // Update to database
                CustomerUtils::updatePrepaymentShippingCost($envelope, $temp_total_cost, ShippingConfigs::COLLECT_SHIPPING, $shipping_info_decode_key);
                $estimateResult['cost'] = $temp_total_cost;
                $estimateResult['estimated_type'] = 'calculated';
                return $estimateResult;
            }
            foreach ($list_all_collect_items as $item) {
                $estimated_type = 'estimated';
                $envelope_id = $item->id;
                $temp_total_cost = 0;
                // Get envelope
                $arr_condition = array(
                    "id" => $envelope_id,
                    "to_customer_id" => $customer_id
                );
                $envelope = ci()->envelope_m->get_by_many($arr_condition);
                if (empty($envelope)) {
                    continue;
                }

                $envelope_type_id = $envelope->envelope_type_id;
                // Get postbox location
                $postbox_location = ci()->location_m->get_by_many(array("id" => $location_id));
                if (empty($postbox_location)) {
                    continue;
                }

                // Get target shipping address
                // $target_shipping_address = scans_api::getSelectedForwardingAddressOfEnvelopes($customer_id, $envelope->shipping_address_id);
                $target_shipping_address = shipping_api::getShippingAddressByEnvelope($customer_id, $envelope_id);
                // Get country to target shipping address
                $source_shipping_country = settings_api::getCountryByID($postbox_location->country_id);
                $letter_national_price_by_country = $letter_national_price;
                $letter_international_price_by_country = $letter_international_price;
                $package_national_price_by_country = $package_national_price;
                $package_international_price_by_country = $package_international_price;
                if (!empty($source_shipping_country)) {
                    $letter_national_price_by_country = $source_shipping_country->letter_national_price;
                    $letter_international_price_by_country = $source_shipping_country->letter_international_price;
                    $package_national_price_by_country = $source_shipping_country->package_national_price;
                    $package_international_price_by_country = $source_shipping_country->package_international_price;
                }

                // Envelope is Letter
                if (in_array($envelope_type_id, $all_envelope_type_letter)) {
                    // forwarding country=same country as location : national letter
                    if ($postbox_location->country_id === $target_shipping_address->shipment_country) {
                        // Return 5 EUR
                        $temp_total_cost = $letter_national_price_by_country;
                    } else {
                        // Return 10 EUR
                        $temp_total_cost = $letter_international_price_by_country;
                    }
                }
                // Envelope is Package
                else if (in_array($envelope_type_id, $all_envelope_type_package)) {
                    // forwarding country=same country as location : national letter
                    if ($postbox_location->country_id === $target_shipping_address->shipment_country) {
                        // Return 20 EUR
                        $temp_total_cost = $package_national_price_by_country;
                    } else {
                        // Return 50 EUR
                        $temp_total_cost = $package_international_price_by_country;
                    }
                }
                if ($temp_total_cost > $total_cost) {
                    $total_cost = $temp_total_cost;
                }
            }
        }
        // Direct Shipping
        else {
            $estimated_type = '';
            foreach ($list_envelope_id as $envelope_id) {
                // Get envelope
                $arr_condition = array(
                    "id" => $envelope_id,
                    "to_customer_id" => $customer_id,
                    "(trash_flag IS NULL)" => null
                );

                // When call to calculate total do not check this condition
                if (!$include_all) {
                    $arr_condition['(direct_shipping_flag IS NULL)'] = null;
                }
                $envelope = ci()->envelope_m->get_by_many($arr_condition);
                if (empty($envelope)) {
                    $total_cost += 0;
                    continue;
                }

                // Get confirmed shipping cost
                $confrimed_total_cost = $envelope->shipping_rate;
                $shipping_rate_id = $envelope->shipping_rate_id;

                // Calculate customs handling fee
                $insurance_customs_cost = mailbox_api::get_total_customs_cost($customer_id, $envelope_id);
                $shipping_customs_cost_fee = 0;
                if ($insurance_customs_cost > 0) {
                    $postbox_id = $envelope->postbox_id;
                    // Get postbox information
                    $postbox = ci()->postbox_m->get($postbox_id);
                    if (empty($postbox)) {
                        $total_cost += 0;
                        continue;
                    }
                    $location_id = $postbox->location_available_id;
                    // $pricing_map = price_api::getPricingModelByPostboxID($postbox_id);
                    $pricing_map = price_api::getPricingModelByCusotomerAndLocationID($customer_id, $location_id);

                    $postbox_type = $postbox->type;
                    if ($insurance_customs_cost > 1000) {
                        $shipping_customs_cost_fee = $pricing_map [$postbox_type] ['custom_declaration_outgoing_01'];
                    } else {
                        $shipping_customs_cost_fee = $pricing_map [$postbox_type] ['custom_declaration_outgoing_02'];
                    }
                }
                if ($confrimed_total_cost > 0) {
                    $total_cost += $confrimed_total_cost + $shipping_customs_cost_fee;
                    if (empty($estimated_type)) {
                        $estimated_type = 'calculated';
                    }
                    continue;
                }

                // Estimate cost at Fedex
                $temp_total_cost = 0;
                $location_id = $envelope != null ? $envelope->location_id : 0;
                $customers_address = shipping_api::getShippingAddressByEnvelope($customer_id, $envelope_id);
                $total_customs_cost = mailbox_api::get_total_customs_cost($customer_id, $envelope_id);
                $shippingInfo = array(
                    ShippingConfigs::CUSTOMER_ID => $customer_id,
                    ShippingConfigs::CUSTOMS_VALUE => $total_customs_cost,
                    ShippingConfigs::STREET => $customers_address->shipment_street,
                    ShippingConfigs::POSTAL_CODE => $customers_address->shipment_postcode,
                    ShippingConfigs::CITY => $customers_address->shipment_city,
                    ShippingConfigs::REGION => $customers_address->shipment_region,
                    ShippingConfigs::COUNTRY_ID => $customers_address->shipment_country,
                    ShippingConfigs::LOCATION_ID => $location_id
                );
                $shipping_info_decode_key = json_encode($shippingInfo);
                $envelope_prepayment_cost = ci()->envelope_prepayment_cost_m->get_by_many(array(
                    'customer_id' => $customer_id,
                    'envelope_id' => $envelope_id,
                    'shipping_info_decode_key' => $shipping_info_decode_key
                ));

                // Only re-calculate if customer did not confirm the shipping service
                if (empty($shipping_rate_id) || $shipping_rate_id == '0') {
                    $selected_shipping_service_obj = shipping_api::getShippingServiceIdByEnvelope($envelope_id);
                    $selected_shipping_service_id = $selected_shipping_service_obj['shipping_service_id'];

                    if (empty($envelope_prepayment_cost) || empty($envelope_prepayment_cost->collect_shipping_cost)) {
                        try {
                            $cost_object = shipping_api::calculateCostOfAllServices($customer_id, array($envelope), $envelope_id, ShippingConfigs::DIRECT_SHIPPING);
                            if ($cost_object != null) {
                                $data = $cost_object['data'];
                                foreach ($data as $shipping_service_item_id => $item_cost) {
                                    if ($shipping_service_item_id == $selected_shipping_service_id && !empty($item_cost['raw_total_charge']) && $item_cost['raw_total_charge'] > 0) {
                                        if ($temp_total_cost == 0) {
                                            $temp_total_cost = $item_cost['raw_total_charge'];
                                        } else {
                                            $temp_total_cost = min($item_cost['raw_total_charge'], $temp_total_cost);
                                        }
                                    }
                                }
                            }
                        } catch (Exception $ex) {
                            log_audit_message(APConstants::LOG_INFOR, "Total Estimate FedexShippingCost: " . $shipping_type . ' of envelope id: ' . json_encode($list_envelope_id) . ' has error ' . $ex->getMessage(), FALSE, 'estimateShippingCost');
                        }
                    } else {
                        $temp_total_cost = $envelope_prepayment_cost->direct_shipping_cost;
                    }
                    if ($temp_total_cost > 0) {
                        $total_cost += $temp_total_cost;
                        if (empty($estimated_type)) {
                            $estimated_type = 'calculated';
                        }
                        // Update to database
                        CustomerUtils::updatePrepaymentShippingCost($envelope, $temp_total_cost, ShippingConfigs::DIRECT_SHIPPING, $shipping_info_decode_key);
                        continue;
                    }
                }

                $envelope_type_id = $envelope->envelope_type_id;
                $postbox_id = $envelope->postbox_id;

                // Get postbox information
                $postbox = CustomerUtils::getPostboxByID($postbox_id);
                if (empty($postbox)) {
                    $total_cost += 0;
                    continue;
                }

                $location_id = $postbox->location_available_id;
                // Get postbox location
                $postbox_location = ci()->location_m->get_by_many(array("id" => $location_id));
                if (empty($postbox_location)) {
                    $total_cost += 0;
                    continue;
                }

                // Get target shipping address
                // $target_shipping_address = scans_api::getSelectedForwardingAddressOfEnvelopes($customer_id, $envelope->shipping_address_id);
                $target_shipping_address = shipping_api::getShippingAddressByEnvelope($customer_id, $envelope_id);
                // Get country to target shipping address
                $source_shipping_country = settings_api::getCountryByID($postbox_location->country_id);
                $letter_national_price_by_country = $letter_national_price;
                $letter_international_price_by_country = $letter_international_price;
                $package_national_price_by_country = $package_national_price;
                $package_international_price_by_country = $package_international_price;
                if (!empty($source_shipping_country)) {
                    $letter_national_price_by_country = $source_shipping_country->letter_national_price;
                    $letter_international_price_by_country = $source_shipping_country->letter_international_price;
                    $package_national_price_by_country = $source_shipping_country->package_national_price;
                    $package_international_price_by_country = $source_shipping_country->package_international_price;
                }

                // Envelope is Letter
                if (in_array($envelope_type_id, $all_envelope_type_letter)) {
                    $estimated_type = 'estimated';
                    // forwarding country=same country as location : national letter
                    if ($postbox_location->country_id === $target_shipping_address->shipment_country) {
                        // Return 5 EUR
                        $total_cost += $letter_national_price_by_country;
                    } else {
                        // Return 10 EUR
                        $total_cost += $letter_international_price_by_country;
                    }
                }
                // Envelope is Package
                else if (in_array($envelope_type_id, $all_envelope_type_package)) {
                    $estimated_type = 'estimated';
                    // forwarding country=same country as location : national letter
                    if ($postbox_location->country_id === $target_shipping_address->shipment_country) {
                        // Return 20 EUR
                        $total_cost += $package_national_price_by_country;
                    } else {
                        // Return 50 EUR
                        $total_cost += $package_international_price_by_country;
                    }
                }

                // Add customs handling fee
                $total_cost += $shipping_customs_cost_fee;
            } // End for envelopes
        }

        $estimateResult['cost'] = $total_cost;
        $estimateResult['estimated_type'] = $estimated_type;
        log_audit_message(APConstants::LOG_INFOR, "Total Estimate ShippingCost: " . $shipping_type . ' of envelope id: ' . json_encode($list_envelope_id) . ' ==> ' . json_encode($estimateResult), FALSE, 'estimateShippingCost');
        return $estimateResult;
    }

    /**
     * Update prepayment cost to database
     * @param type $envelope
     * @param type $shipping_cost
     * @param type $shipping_type
     */
    public static function updatePrepaymentShippingCost($envelope, $shipping_cost, $shipping_type, $shipping_info_decode_key) {
        $envelope_prepayment_cost = ci()->envelope_prepayment_cost_m->get_by_many(array(
            'customer_id' => $envelope->to_customer_id,
            'envelope_id' => $envelope->id,
            'postbox_id' => $envelope->postbox_id
        ));
        if (empty($envelope_prepayment_cost)) {
            ci()->envelope_prepayment_cost_m->insert(array(
                'customer_id' => $envelope->to_customer_id,
                'envelope_id' => $envelope->id,
                'postbox_id' => $envelope->postbox_id,
                'created_date' => now()
            ));
        }

        // Update information
        $update_data = array();
        $update_data['shipping_info_decode_key'] = $shipping_info_decode_key;
        if (ShippingConfigs::COLLECT_SHIPPING == $shipping_type) {
            $update_data['collect_shipping_cost'] = $shipping_cost;
        } else if (ShippingConfigs::DIRECT_SHIPPING == $shipping_type) {
            $update_data['direct_shipping_cost'] = $shipping_cost;
        }
        ci()->envelope_prepayment_cost_m->update_by_many(array(
            'customer_id' => $envelope->to_customer_id,
            'envelope_id' => $envelope->id,
            'postbox_id' => $envelope->postbox_id
                ), $update_data);
    }

    /**
     * Check this one activity need to apply pre-payment process or not
     * The return data will have format
     * array(
     * 		"prepayment": true,
     * 		"trigger_type": customer,
     * 		"open_balance_due": 100,
     * 		"open_balance_this_month": 20,
     * 		"estimated_cost": 20
     * )
     *
     * @param $trigger_type: The trigger type (customer|system)
     * @param $shipping_service: The shipping service (normal|fedex) (dont' use now)
     * @param unknown_type $shipping_type: The shipping type (direct|collect) (dont' use now)
     * @param unknown_type $list_envelope_id: The list of envelope id
     * @param unknown_type $customer_id
     */
    public static function checkApplyShippingPrepayment($trigger_type, $shipping_service, $shipping_type, $list_envelope_id, $customer_id, $send_email = false, $second_check_flag = false) {
        ci()->load->model('payment/payone_tran_hist_m');
        ci()->load->model('payment/external_tran_hist_m');
        $customer = APContext::getCustomerByID($customer_id);

        // only check prepayment for enterprise customer and standard customer. donot check for user enterprise.
        if (!empty($customer->parent_customer_id) && $customer->account_type == APConstants::ENTERPRISE_CUSTOMER) {
            return array(
                "prepayment" => false,
                "trigger_type" => $trigger_type,
            );
        }

        if ($customer->required_prepayment_flag === APConstants::OFF_FLAG) {
            return array(
                "prepayment" => false,
                "trigger_type" => $trigger_type,
            );
        }

        $acitivity_cost_obj = CustomerUtils::estimateShippingCost($shipping_service, $shipping_type, $list_envelope_id, $customer_id);
        $total_estimated_cost = $acitivity_cost_obj['cost'];
        $other_prepayment_cost = CustomerUtils::estimateTotalPrepaymentRequest($customer_id);
        $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
        $open_balance_due = $open_balance_data['OpenBalanceDue'];
        $open_balance_this_month = $open_balance_data['OpenBalanceThisMonth'];

        // 1. Does account have deposit that covers cost+OB?
        $total_cost_check = $total_estimated_cost + $open_balance_due + $open_balance_this_month + $other_prepayment_cost;
        if ($second_check_flag) {
            $total_cost_check = $open_balance_due + $open_balance_this_month + $other_prepayment_cost;
        }
        if ($total_cost_check <= 0.01) {
            log_audit_message(APConstants::LOG_INFOR, "Total Cost: " . json_encode($list_envelope_id) . ' ==> ' . ($total_estimated_cost + $open_balance_due + $open_balance_this_month));
            return array(
                "prepayment" => false,
                "trigger_type" => $trigger_type,
                "open_balance_due" => $open_balance_due,
                "open_balance_this_month" => $open_balance_this_month,
                "estimated_cost" => $total_estimated_cost,
                "other_prepayment_cost" => $other_prepayment_cost
            );
        }
        // Get all payone status
        $array_condition = array(
            'customer_id' => $customer_id,
            "(txaction = 'paid')" => null
        );
        $total_payone_payment = ci()->payone_tran_hist_m->count_by_many($array_condition);

        $array_condition = array(
            'customer_id' => $customer_id
        );
        $total_external_payment = ci()->external_tran_hist_m->count_by_many($array_condition);
        $total_success_payment = $total_payone_payment + $total_external_payment;

        $list_approve_envelope_id = array();
        $list_reject_envelope_id = array();
        // 2. Does account have Open Balance Due>0?
        // 2.1 YES case
        if ($open_balance_due > 0.01) {
            $list_reject_envelope_id = $list_envelope_id;
            log_audit_message(APConstants::LOG_INFOR, "Open Balance Due: " . json_encode($list_envelope_id) . ' ==> ' . ($open_balance_due));

            return array(
                "prepayment" => true,
                "trigger_type" => $trigger_type,
                "open_balance_due" => $open_balance_due,
                "open_balance_this_month" => $open_balance_this_month,
                "estimated_cost" => $total_estimated_cost,
                "other_prepayment_cost" => $other_prepayment_cost,
                "list_approve_envelope_id" => $list_approve_envelope_id,
                "list_reject_envelope_id" => $list_reject_envelope_id
            );
        }

        // 2.2 NO Case
        // Get all setting type
        $all_envelope_type = Settings::get_list(APConstants::ENVELOPE_TYPE_CODE);
        $all_envelope_type_letter = array();
        $all_envelope_type_package = array();
        // Get all letter type & package type
        foreach ($all_envelope_type as $envelope_type) {
            if ($envelope_type->Alias02 == 'Letter') {
                $all_envelope_type_letter [] = $envelope_type->ActualValue;
            }
            if ($envelope_type->Alias02 == 'Package') {
                $all_envelope_type_package [] = $envelope_type->ActualValue;
            }
        }

        // For each envelope id
        foreach ($list_envelope_id as $envelope_id) {
            // Get envelope
            $envelope = ci()->envelope_m->get_by_many(array(
                "id" => $envelope_id,
                "to_customer_id" => $customer_id
            ));
            if (empty($envelope)) {
                continue;
            }

            // Check pending custom flag. Don't trigger again if this envelope is pending to declare customs
            $pending_customs_flag = EnvelopeUtils::check_envelope_customs($envelope_id);
            if ($pending_customs_flag) {
                continue;
            }

            $envelope_type_id = $envelope->envelope_type_id;

            // 3. Is shipment a letter?
            // Envelope is letter
            if (in_array($envelope_type_id, $all_envelope_type_letter)) {
                // 4. Does account have at least 3 successful payments into the account?
                if ($total_success_payment < 3) {
                    $list_reject_envelope_id[] = $envelope_id;
                    continue;
                }
            } else {
                $list_reject_envelope_id[] = $envelope_id;
                continue;
            }

            // Add to approve list
            $list_approve_envelope_id[] = $envelope_id;
        }

        // 5. Was the activity triggered by system?
        if (count($list_reject_envelope_id) > 0) {
            if ($trigger_type == APConstants::TRIGGER_ACTION_TYPE_SYSTEM) {

                if ($send_email) {
                    $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
                    $open_balance_due = $open_balance_data['OpenBalanceDue'];
                    $open_balance_this_month = $open_balance_data['OpenBalanceThisMonth'];
                    CustomerUtils::sendPrepaymentEmail($customer_id, $customer->email, $open_balance_due, $open_balance_this_month, $total_estimated_cost);
                }

                return array(
                    "prepayment" => true,
                    "trigger_type" => $trigger_type,
                    "open_balance_due" => $open_balance_due,
                    "open_balance_this_month" => $open_balance_this_month,
                    "estimated_cost" => $total_estimated_cost,
                    "other_prepayment_cost" => $other_prepayment_cost,
                    "list_approve_envelope_id" => $list_approve_envelope_id,
                    "list_reject_envelope_id" => $list_reject_envelope_id
                );
            } else {

                return array(
                    "prepayment" => true,
                    "trigger_type" => $trigger_type,
                    "open_balance_due" => $open_balance_due,
                    "open_balance_this_month" => $open_balance_this_month,
                    "estimated_cost" => $total_estimated_cost,
                    "other_prepayment_cost" => $other_prepayment_cost,
                    "list_approve_envelope_id" => $list_approve_envelope_id,
                    "list_reject_envelope_id" => $list_reject_envelope_id
                );
            }
        }

        // Return success list
        return array(
            "prepayment" => false,
            "trigger_type" => $trigger_type,
            "open_balance_due" => $open_balance_due,
            "open_balance_this_month" => $open_balance_this_month,
            "estimated_cost" => $total_estimated_cost,
            "other_prepayment_cost" => $other_prepayment_cost,
            "list_approve_envelope_id" => $list_approve_envelope_id,
            "list_reject_envelope_id" => $list_reject_envelope_id
        );
    }

    /**
     * Estimate cost for scanning item
     *
     * @param unknown_type $list_envelope_id
     * @param unknown_type $action_type
     * @param unknown_type $customer_id
     */
    public static function estimateScanningCost($list_envelope_id, $action_type, $customer_id, $include_all = false) {
        ci()->load->model('scans/envelope_m');
        ci()->load->library('invoices/EnvelopeItemScan');
        $total_cost = 0;
        foreach ($list_envelope_id as $envelope_id) {
            // Get envelope
            $arr_condition = array(
                "id" => $envelope_id,
                "to_customer_id" => $customer_id,
                "(trash_flag IS NULL)" => null
            );
            // Get envelope
            $envelope = ci()->envelope_m->get_by_many($arr_condition);
            if (empty($envelope)) {
                $total_cost += 0;
                continue;
            }

            // Only add this condition when calculate cost for each item
            if (!$include_all && (($action_type === 'envelope' && empty($envelope->envelope_scan_flag)) || ($action_type == 'item' && empty($envelope->item_scan_flag)))) {
                $total_cost += 0;
                continue;
            }

            $postbox_id = $envelope->postbox_id;

            // Envelope is Letter
            if ($action_type == 'envelope') {
                $total_cost += EnvelopeItemScan::getCostForPreEnvelopeScan($customer_id, $postbox_id, $envelope_id);
            }
            // Envelope is Package
            else if ($action_type == 'item') {
                $total_cost += EnvelopeItemScan::getCostForPreItemScan($customer_id, $postbox_id, $envelope_id);
            }
        }
        log_audit_message(APConstants::LOG_INFOR, "Total Estimate ScanningCost: " . $action_type . ' of envelope id: ' . json_encode($list_envelope_id) . ' ==> ' . $total_cost);
        return $total_cost;
    }

    /**
     * Check this one activity need to apply pre-payment process or not
     * The return data will have format
     * array(
     * 		"prepayment": true,
     * 		"trigger_type": customer,
     * 		"open_balance_due": 100,
     * 		"open_balance_this_month": 20,
     * 		"estimated_cost": 20
     * )
     *
     * @param unknown_type $scan_type: The shipping type (envelope|item) (dont' use now)
     * @param unknown_type $list_envelope_id: The list of envelope id
     * @param unknown_type $customer_id
     */
    public static function checkApplyScanPrepayment($trigger_type, $scan_type, $list_envelope_id, $customer_id, $send_email = false, $second_check_flag = false) {
        ci()->load->model('payment/payone_tran_hist_m');
        ci()->load->model('payment/external_tran_hist_m');
        $customer = APContext::getCustomerByID($customer_id);

        // only check prepayment for enterprise customer and standard customer. donot check for user enterprise.
        if (!empty($customer->parent_customer_id) && $customer->account_type == APConstants::ENTERPRISE_CUSTOMER) {
            return array(
                "prepayment" => false,
                "trigger_type" => $trigger_type,
            );
        }

        if ($customer->required_prepayment_flag === APConstants::OFF_FLAG) {
            return array(
                "prepayment" => false,
                "trigger_type" => $trigger_type
            );
        }

        $total_estimated_cost = CustomerUtils::estimateScanningCost($list_envelope_id, $scan_type, $customer_id, true);

        // If request 2nd, the other payment cost will include estiamted cost of this activity
        $other_prepayment_cost = CustomerUtils::estimateTotalPrepaymentRequest($customer_id);
        $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
        $open_balance_due = $open_balance_data['OpenBalanceDue'];
        $open_balance_this_month = $open_balance_data['OpenBalanceThisMonth'];

        // 1. Does account have deposit that covers cost+OB?
        $total_cost_check = $total_estimated_cost + $open_balance_due + $open_balance_this_month + $other_prepayment_cost;
        if ($second_check_flag) {
            $total_cost_check = $open_balance_due + $open_balance_this_month + $other_prepayment_cost;
        }
        if ($total_cost_check < 0.01) {
            log_audit_message(APConstants::LOG_INFOR, "Total Cost: " . json_encode($list_envelope_id) . ' ==> ' . ($total_estimated_cost + $open_balance_due + $open_balance_this_month));
            return array(
                "prepayment" => false,
                "trigger_type" => $trigger_type,
                "open_balance_due" => $open_balance_due,
                "open_balance_this_month" => $open_balance_this_month,
                "estimated_cost" => $total_estimated_cost,
                "other_prepayment_cost" => $other_prepayment_cost
            );
        }

        // Get all payone status
        $array_condition = array(
            'customer_id' => $customer_id,
            "(txaction = 'paid')" => null
        );
        $total_payone_payment = ci()->payone_tran_hist_m->count_by_many($array_condition);

        $array_condition = array(
            'customer_id' => $customer_id
        );
        $total_external_payment = ci()->external_tran_hist_m->count_by_many($array_condition);
        $total_success_payment = $total_payone_payment + $total_external_payment;

        log_audit_message(APConstants::LOG_INFOR, "total_success_payment of customer id: " . $customer_id . ' ==> ' . ($total_success_payment));

        $list_approve_envelope_id = array();
        $list_reject_envelope_id = array();
        // 2. Does account have Open Balance Due>0?
        // 2.1 YES case
        if ($open_balance_due > 0.01) {
            $list_reject_envelope_id = $list_envelope_id;
            log_audit_message(APConstants::LOG_INFOR, "Open Balance Due: " . json_encode($list_envelope_id) . ' ==> ' . ($open_balance_due));

            return array(
                "prepayment" => true,
                "trigger_type" => $trigger_type,
                "open_balance_due" => $open_balance_due,
                "open_balance_this_month" => $open_balance_this_month,
                "estimated_cost" => $total_estimated_cost,
                "other_prepayment_cost" => $other_prepayment_cost,
                "list_approve_envelope_id" => $list_approve_envelope_id,
                "list_reject_envelope_id" => $list_reject_envelope_id
            );
        }

        // 2.2 NO Case
        // 3. Does account have at least 3 successful payments into the account?
        // YES CASE
        if ($total_success_payment >= 1) {
            $list_approve_envelope_id = $list_envelope_id;
            return array(
                "prepayment" => false,
                "trigger_type" => $trigger_type,
                "open_balance_due" => $open_balance_due,
                "open_balance_this_month" => $open_balance_this_month,
                "estimated_cost" => $total_estimated_cost,
                "other_prepayment_cost" => $other_prepayment_cost
            );
        } else {
            $list_reject_envelope_id = $list_envelope_id;
        }

        // 5. Was the activity triggered by system?
        if (count($list_reject_envelope_id) > 0) {
            if ($trigger_type == APConstants::TRIGGER_ACTION_TYPE_SYSTEM) {

                if ($send_email) {
                    $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
                    $open_balance_due = $open_balance_data['OpenBalanceDue'];
                    $open_balance_this_month = $open_balance_data['OpenBalanceThisMonth'];
                    CustomerUtils::sendPrepaymentEmail($customer_id, $customer->email, $open_balance_due, $open_balance_this_month, $total_estimated_cost);
                }
                return array(
                    "prepayment" => true,
                    "trigger_type" => $trigger_type,
                    "open_balance_due" => $open_balance_due,
                    "open_balance_this_month" => $open_balance_this_month,
                    "estimated_cost" => $total_estimated_cost,
                    "other_prepayment_cost" => $other_prepayment_cost,
                    "list_approve_envelope_id" => $list_approve_envelope_id,
                    "list_reject_envelope_id" => $list_reject_envelope_id
                );
            } else {

                return array(
                    "prepayment" => true,
                    "trigger_type" => $trigger_type,
                    "open_balance_due" => $open_balance_due,
                    "open_balance_this_month" => $open_balance_this_month,
                    "estimated_cost" => $total_estimated_cost,
                    "other_prepayment_cost" => $other_prepayment_cost,
                    "list_approve_envelope_id" => $list_approve_envelope_id,
                    "list_reject_envelope_id" => $list_reject_envelope_id
                );
            }
        }

        // Return success list
        return array(
            "prepayment" => false,
            "trigger_type" => $trigger_type,
            "open_balance_due" => $open_balance_due,
            "open_balance_this_month" => $open_balance_this_month,
            "estimated_cost" => $total_estimated_cost,
            "other_prepayment_cost" => $other_prepayment_cost,
            "list_approve_envelope_id" => $list_approve_envelope_id,
            "list_reject_envelope_id" => $list_reject_envelope_id
        );
    }

    public static function countPaymentSuccess($customer_id) {
        ci()->load->model('payment/payone_tran_hist_m');
        ci()->load->model('payment/external_tran_hist_m');
        // Get all payone status
        $array_condition = array(
            'customer_id' => $customer_id,
            "(txaction = 'paid')" => null
        );
        $total_payone_payment = ci()->payone_tran_hist_m->count_by_many($array_condition);

        $array_condition = array(
            'customer_id' => $customer_id
        );
        $total_external_payment = ci()->external_tran_hist_m->count_by_many($array_condition);
        $total_success_payment = $total_payone_payment + $total_external_payment;
        return $total_success_payment;
    }

    /**
     * Estimate cost for add new postbox
     *
     * @param unknown_type $list_envelope_id
     * @param unknown_type $action_type
     * @param unknown_type $customer_id
     */
    public static function estimateNewPostboxCost($postbox_type, $location_id, $customer_id) {
        ci()->load->model('price/pricing_m');
        ci()->load->model('addresses/location_m');
        $total_cost = 0;

        // Get postbox location
        $postbox_location = ci()->location_m->get_by_many(array("id" => $location_id));
        if (empty($postbox_location)) {
            $total_cost += 0;
            return;
        }

        // change invoices by pricing template.
        $pricings = ci()->pricing_m->get_many_by_many(array(
            "pricing_template_id" => $postbox_location->pricing_template_id
        ));
        $pricing_map = array();
        foreach ($pricings as $price) {
            if (!array_key_exists($price->account_type, $pricing_map)) {
                $pricing_map [$price->account_type] = array();
            }
            $pricing_map [$price->account_type] [$price->item_name] = $price->item_value;
        }
        $price = $pricing_map [$postbox_type];
        $postbox_netprice = $price['postbox_fee'];

        // when customer create new bussiness postbox
        $postbox_created_date = date("Ymd");
        $start_day_of_month = APUtils::getFirstDayOfCurrentMonth();
        $end_day_of_month = APUtils::getLastDayOfCurrentMonth();
        $number_of_day_month = APUtils::getDateDiff($start_day_of_month, $end_day_of_month);
        $number_day_must_be_calculated_fee = APUtils::getDateDiff($postbox_created_date, $end_day_of_month);
        $total_cost += $postbox_netprice * $number_day_must_be_calculated_fee / $number_of_day_month;

        log_audit_message(APConstants::LOG_INFOR, "Total Estimate Cost to Add NEW POSTBOX TYPE: " . $postbox_type . ' ==> ' . $total_cost);
        return $total_cost;
    }

    /**
     * Check this one activity need to apply pre-payment process or not
     * The return data will have format
     * array(
     * 		"prepayment": true,
     * 		"open_balance_due": 100,
     * 		"open_balance_this_month": 20,
     * 		"estimated_cost": 20
     * )
     *
     * @param unknown_type $postbox_type: The shipping type (enterprise/business/private/as you go)
     * @param unknown_type $location_id: The location id
     * @param unknown_type $customer_id: The customer id
     */
    public static function checkApplyAddPostboxPrepayment($postbox_type, $location_id, $customer_id) {
        ci()->load->model('mailbox/postbox_m');
        ci()->load->model('scans/envelope_m');
        $customer = APContext::getCustomerByID($customer_id);

        // only check prepayment for enterprise customer and standard customer. donot check for user enterprise.
        if (!empty($customer->parent_customer_id) && $customer->account_type == APConstants::ENTERPRISE_CUSTOMER) {
            return array(
                "prepayment" => false
            );
        }

        if ($customer->required_prepayment_flag === APConstants::OFF_FLAG) {
            return array(
                "prepayment" => false
            );
        }

        // Get number of postbox type
        $number_current_postbox = ci()->postbox_m->count_by_customer_postbox_type($customer_id, $postbox_type);
        if ($number_current_postbox == 0) {
            return array(
                "prepayment" => false,
                "open_balance_due" => '',
                "open_balance_this_month" => '',
                "estimated_cost" => ''
            );
        }

        $total_estimated_cost = CustomerUtils::estimateNewPostboxCost($postbox_type, $location_id, $customer_id);
        $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
        $open_balance_due = $open_balance_data['OpenBalanceDue'];
        $open_balance_this_month = $open_balance_data['OpenBalanceThisMonth'];

        // 1. Does account have deposit that covers cost+OB?
        if ($total_estimated_cost + $open_balance_due + $open_balance_this_month < 0.01) {
            log_audit_message(APConstants::LOG_INFOR, 'Total Cost: ==> ' . ($total_estimated_cost + $open_balance_due + $open_balance_this_month));
            return array(
                "prepayment" => false,
                "open_balance_due" => $open_balance_due,
                "open_balance_this_month" => $open_balance_this_month,
                "estimated_cost" => $total_estimated_cost
            );
        }

        // Return success list
        return array(
            "prepayment" => true,
            "open_balance_due" => $open_balance_due,
            "open_balance_this_month" => $open_balance_this_month,
            "estimated_cost" => $total_estimated_cost
        );
    }

    /**
     * Send prepayment data
     */
    public static function sendPrepaymentEmail($customer_id, $email, $open_balance_due, $open_balance_this_month, $total_estimated_cost) {
        $total = $open_balance_due + $open_balance_this_month + $total_estimated_cost;
        $other_prepayment_cost = CustomerUtils::estimateTotalPrepaymentRequest($customer_id) - $total_estimated_cost;
        $total += $other_prepayment_cost;

        // Get customer address
        $customer_address = CustomerUtils::getCustomerAddressByID($customer_id);
        $customer_name = '';
        if (!empty($customer_address)) {
            if (!empty($customer_address->invoicing_address_name)) {
                $customer_name = $customer_address->invoicing_address_name;
            } else if (!empty($customer_address->invoicing_company)) {
                $customer_name = $customer_address->invoicing_company;
            }
        } else {
            $customer_name = $email;
        }

        // Send email
        $email_data = array(
            "slug" => APConstants::prepayment_notification_email,
            "full_name" => $customer_name,
            "to_email" => $email,
            "open_balance_due" => APUtils::number_format($open_balance_due),
            "open_balance_this_month" => APUtils::number_format($open_balance_this_month),
            "estimated_cost" => APUtils::number_format($total_estimated_cost),
            "other_prepayment_cost" => APUtils::number_format($other_prepayment_cost),
            "total" => APUtils::number_format($total),
        );
        MailUtils::sendEmailByTemplate($email_data);
    }

    /**
     * Completed prepayment request
     * @param unknown_type $customer_id
     */
    public static function estimateTotalPrepaymentRequest($customer_id) {
        ci()->load->model('scans/envelope_m');

        $total_prepayment_amount = 0;
        // Get all pending envelope scan
        $list_pending_envelope_scan = ci()->envelope_m->get_many_by_many(array(
            "to_customer_id" => $customer_id,
            "envelope_scan_flag" => '2',
            "(trash_flag IS NULL)" => null
        ));

        if (count($list_pending_envelope_scan) > 0) {
            foreach ($list_pending_envelope_scan as $item) {
                $total_prepayment_amount += CustomerUtils::estimateScanningCost(array($item->id), 'envelope', $customer_id, true);
            }
        }

        // Get all pending envelope scan
        $list_pending_item_scan = ci()->envelope_m->get_many_by_many(array(
            "to_customer_id" => $customer_id,
            "item_scan_flag" => '2'
        ));
        if (count($list_pending_item_scan) > 0) {
            foreach ($list_pending_item_scan as $item) {
                $total_prepayment_amount += CustomerUtils::estimateScanningCost(array($item->id), 'item', $customer_id, true);
            }
        }

        // Get all pending envelope scan
        $list_pending_item_direct = ci()->envelope_m->get_many_by_many(array(
            "to_customer_id" => $customer_id,
            "direct_shipping_flag" => '2'
        ));
        if (count($list_pending_item_direct) > 0) {
            foreach ($list_pending_item_direct as $item) {
                $acitivity_cost_obj = CustomerUtils::estimateShippingCost(APConstants::SHIPPING_SERVICE_NORMAL, APConstants::SHIPPING_TYPE_DIRECT, array($item->id), $customer_id, true);
                $total_prepayment_amount += $acitivity_cost_obj['cost'];
            }
        }

        // Actually this only have 1 package pending for each customer
        // So this cost will be count for one item
        // Get all pending envelope scan
        $list_pending_item_collect = ci()->envelope_m->get_many_by_many(array(
            "to_customer_id" => $customer_id,
            "collect_shipping_flag" => '2'
        ));
        if (count($list_pending_item_collect) > 0) {
            $first_collect_envelope = $list_pending_item_collect[0];
            $acitivity_cost_obj = CustomerUtils::estimateShippingCost(APConstants::SHIPPING_SERVICE_NORMAL, APConstants::SHIPPING_TYPE_COLLECT, array($first_collect_envelope->id), $customer_id, true);
            $total_prepayment_amount += $acitivity_cost_obj['cost'];
        }
        return $total_prepayment_amount;
    }

    /**
     * Count number of postbox by customer id
     * @param type $customer_id
     */
    public static function countNumberPostbox($customer_id) {
        ci()->load->model('mailbox/postbox_m');
        // Get all postbox of this customer
        $number_postbox = ci()->postbox_m->count_by_many(array(
            "customer_id" => $customer_id,
            "deleted <> " => '1',
            "completed_delete_flag <> " => APConstants::ON_FLAG,
            "(postbox_name IS NOT NULL AND postbox_name !='')" => null,
            "((name IS NOT NULL AND name != '') OR (company IS NOT NULL AND company != ''))" => null
        ));
        return $number_postbox;
    }

    public static function getInactiveDayOfCustomerBy($customer) {
        if($customer->status == APConstants::ON_FLAG){
            return "";
        }
        $number_deactivated_days = "";
        if ($customer->deactivated_date) {
            $startDate = $customer->deactivated_date;
            $endDate = strtotime(date('Y-m-d'));
            $number_deactivated_days = floor(($endDate - $startDate) / (60 * 60 * 24));
        } else {
            $startDate = $customer->created_date;
            $endDate = strtotime(date('Y-m-d'));
            $number_deactivated_days = floor(($endDate - $startDate) / (60 * 60 * 24));
        }

        $number_deactivated_days = $number_deactivated_days < 0 ? "0" : $number_deactivated_days;

        return $number_deactivated_days;
    }

    /**
     * Sync email address from clevvermail system to payone.
     *
     * @param type $customer_id
     */
    public static function syncEmailToPayone($customer_id) {
        ci()->load->library('payment/payone');
        ci()->payone->update_user($customer_id);
    }

    /**
     * Send this email if payment of creadit card fail.
     *
     * @param type $customer_id
     */
    public static function sendEmailPaymentFail($customer_id) {
        // Get open balance
        $result = APUtils::getAdjustOpenBalanceDue($customer_id);
        $open_balance_due = $result['OpenBalanceDue'];
        if ($open_balance_due > 0.01) {
            // Get customer address
            $customer_address = CustomerUtils::getCustomerAddressByID($customer_id);
            $customer = CustomerUtils::getCustomerByID($customer_id);
            $email = $customer->email;
            $customer_name = '';
            if (!empty($customer_address)) {
                if (!empty($customer_address->invoicing_address_name)) {
                    $customer_name = $customer_address->invoicing_address_name;
                } else if (!empty($customer_address->invoicing_company)) {
                    $customer_name = $customer_address->invoicing_company;
                }
            } else {
                $customer_name = $email;
            }
            $due_date = APUtils::getLastDayOfPreviousMonth();
            // Send email
            $email_data = array(
                "slug" => APConstants::notify_payment_fails_email,
                "full_name" => $customer_name,
                "to_email" => $email,
                "due_date" => $due_date,
                "amount" => APUtils::number_format($open_balance_due)
            );
            MailUtils::sendEmailByTemplate($email_data);
        }
    }

    /**
     * #1180 create postbox history page like check item page
     * function actionPostboxHistoryActivity():
     * Activity: 1.upgrade order, downgrade order, upgrade, downgrade
     *           2. delete ordered by customer, delete ordered by system, delete
     *           3. deactivated, reactivated
     * @param type $condition
     * @param type $action_type
     * @param type $action_date
     * @param type $after_type
     * @param type $type
     */
    public static function actionPostboxHistoryActivity($condition, $action_type, $action_date, $after_type, $type) {
        // Load model
        ci()->load->model('mailbox/postbox_m');
        ci()->load->model('mailbox/postbox_history_activity_m');

        if ($type === APConstants::INSERT_POSTBOX) {
            $curr_postbox = ci()->postbox_m->get($condition);

            $data = array(
                "customer_id" => $curr_postbox->customer_id,
                "postbox_id" => $curr_postbox->postbox_id,
                "postbox_code" => $curr_postbox->postbox_code,
                "postbox_name" => $curr_postbox->postbox_name,
                "name" => $curr_postbox->name,
                "company" => $curr_postbox->company,
                "action_type" => $action_type,
                "action_date" => $action_date,
                "type" => ($after_type != "") ? $after_type : $curr_postbox->type
            );
            // Insert new postbox into postbox_history_activity table
            ci()->postbox_history_activity_m->insert($data);
        } else if ($type === APConstants::UPDATE_POSTBOX) {
            $curr_postbox = ci()->postbox_m->get_by_many($condition);

            $data = array(
                "postbox_code" => $curr_postbox->postbox_code,
                "postbox_name" => $curr_postbox->postbox_name,
                "location_available_id" => $curr_postbox->location_available_id,
                "name" => $curr_postbox->name,
                "company" => $curr_postbox->company,
                'type' => $curr_postbox->type
            );
            // Update  postbox into postbox_history_activity table
            ci()->postbox_history_activity_m->update_by_many($condition, $data);
        }
    }

    public static function getLastStatusPostbox($postbox_id)
    {
        ci()->load->model('mailbox/postbox_m');
        ci()->load->model('mailbox/postbox_history_activity_m');
        $postbox = ci()->postbox_history_activity_m->get_by_many_order(["postbox_id = $postbox_id" => null], ['action_type' => 'DESC']);
        return $postbox;
    }

    /**
     * Get list customer id of enterprise customer
     * @param type $enterprise_customer_id
     */
    public static function getListCustomerIdOfEnterpriseCustomer($enterprise_customer_id, $pageNum=1, $pageSize=1000000) {
        ci()->load->model(array(
            'customers/customer_m'
        ));
        $start = ($pageNum-1) * $pageSize;
        $list_customer_user = ci()->customer_m->order_by("customer_id")->limit($pageSize, $start)->get_many_by_many(array(
            'parent_customer_id' => $enterprise_customer_id,
            'status' => APConstants::OFF_FLAG
        ));
        $list_customer_id = array();
        foreach ($list_customer_user as $customer) {
            $list_customer_id[] = $customer->customer_id;
        }
        return $list_customer_id;
    }

    /**
     * Check valid payment method of customer.
     * @param type $customer_id
     * @return boolean
     */
    public static function checkAutomaticChargeCustomer($customer_id, $limit_amount) {
        ci()->load->library('payment/payment_api');
        if (!payment_api::isSettingCreditCard($customer_id)) {
            return false;
        }

        $current_balance = APUtils::getCurrentBalance($customer_id);
        $deposit_amount = $current_balance < 0 ? abs($current_balance) : 0;
        if ($limit_amount > 0 && $deposit_amount > $limit_amount) {
            return false;
        }

        return true;
    }

    /**
     * Gets list customers that sets automatic charge
     */
    public static function getlistAutomaticChargeCustomer() {
        ci()->load->model("customers/customer_m");

        return ci()->customer_m->getListAutomaticChargeCustomer();
    }

    /**
     * Send notification that auto forwarding not work
     */
    public static function sendAutoForwardNotWorking($customer_id, $email, $postbox_name, $open_balance_due) {
        // Get customer address
        $customer_address = CustomerUtils::getCustomerAddressByID($customer_id);
        $customer_name = '';
        if (!empty($customer_address)) {
            if (!empty($customer_address->invoicing_address_name)) {
                $customer_name = $customer_address->invoicing_address_name;
            } else if (!empty($customer_address->invoicing_company)) {
                $customer_name = $customer_address->invoicing_company;
            }
        } else {
            $customer_name = $email;
        }

        // Send email
        $email_data = array(
            "slug" => APConstants::auto_forward_not_work_open_balance_prohibits,
            "full_name" => $customer_name,
            "to_email" => $email,
            "postbox_name" => $postbox_name,
            "open_balance_due" => $open_balance_due,
        );
        MailUtils::sendEmailByTemplate($email_data);
    }
    
    /**
     * create bonus credit note for bonus customer of partner martketing
     * 
     * @param type $customer
     */
    public static function createBonusCreditNoteForFirstTime($customer_partner_profile){
        $customer_id = $customer_partner_profile->customer_id;

        ci()->load->library('price/price_api');
        
        // get parnter martketing profile by customer.
        if(empty($customer_partner_profile) || $customer_partner_profile->bonus_flag == APConstants::OFF_FLAG){
            return;
        }

        $customerVat = CustomerUtils::getVatRateOfCustomer($customer_id);
        $vat = $customerVat->rate;
        
        // Gets pricing posbox of customer.
        $pricing = price_api::getPricingMapByLocationId($customer_partner_profile->bonus_location);

        $bonus_fee = 1 * $pricing[APConstants::BUSINESS_TYPE]['postbox_fee']->item_value * (1 + $vat);
        if($bonus_fee > 0){
            self::createCreditNoteByCustomer($customer_id, $bonus_fee, 'bonus credit for partner code', false);
        }
    }
}
