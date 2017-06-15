<?php defined('BASEPATH') or exit('No direct script access allowed');

class cron_api
{
    public static function sendPushIOS(){
        ci()->load->library('PushUtils');
        ci()->load->model('api/push_message_notification_m');

        // Get 1000 push item not sent
        $list_ios_push_message_data = ci()->push_message_notification_m->get_paging(array(
            'sent_flag' => APConstants::OFF_FLAG,
            'platform' => APConstants::MOBILE_PLATFORM_IOS
        ), 0, 1000, NULL, NULL, NULL);
        $list_ios_push_message = $list_ios_push_message_data['data'];
        $total_ios_item = 0;
        $success_ios_item = 0;
        foreach ($list_ios_push_message as $item) {
            $total_ios_item++;
            $push_id = $item->push_id;
            $body = array(
                "customer_id" => $item->customer_id,
                "postbox_id" => $item->postbox_id,
                "envelope_id" => $item->envelope_id,
                "notification_type" => $item->notify_type,
                "alert" => $item->message
            );

            $send_result = PushUtils::sendIOSPush($push_id, $body);
            if ($send_result) {
                ci()->push_message_notification_m->update_by_many(array(
                    "id" => $item->id
                ), array(
                    "sent_flag" => APConstants::ON_FLAG,
                    "sent_date" => now()
                ));
                $success_ios_item++;
            }
        }

        return array(
            "success_ios_item" => $success_ios_item,
            "total_ios_item" => $total_ios_item
        );
    }

    public static function sendPushAndroid(){
        ci()->load->library('PushUtils');
        ci()->load->model('api/push_message_notification_m');

        // Get 1000 push item not sent
        $list_ios_push_message_data = ci()->push_message_notification_m->get_paging(array(
            'sent_flag' => APConstants::OFF_FLAG,
            'platform' => APConstants::MOBILE_PLATFORM_ANDROID
        ), 0, 1000, NULL, NULL, NULL);
        $list_ios_push_message = $list_ios_push_message_data['data'];
        $total_android_item = 0;
        $success_android_item = 0;
        foreach ($list_ios_push_message as $item) {
            $total_android_item++;
            $push_id = $item->push_id;
            $body = array(
                "customer_id" => $item->customer_id,
                "postbox_id" => $item->postbox_id,
                "envelope_id" => $item->envelope_id,
                "notification_type" => $item->notify_type,
                "body" => $item->message
            );

            $send_result = PushUtils::sendAndroidPush($push_id, $body);
            if ($send_result) {
                ci()->push_message_notification_m->update_by_many(array(
                    "id" => $item->id
                ), array(
                    "sent_flag" => APConstants::ON_FLAG,
                    "sent_date" => now()
                ));
                $success_android_item++;
            }
        }

        return array(
            "success_android_item" => $success_android_item,
            "total_android_item" => $total_android_item
        );
    }
    // update baseline customer: id, delete date
    public static function updateBaselineCustomerBy($array_where, $data){
    	 ci()->load->model('cron_job_data_m');
    	 ci()->cron_job_data_m->update_by_many($array_where, $data);
    	return 1;
    }
    // Insert baseline customer: id, delete date
    public static function insertBaselineCustomerBy($data){
    	 ci()->load->model('cron_job_data_m');
    	 ci()->cron_job_data_m->insert($data);
    	return 1;
    }

    /**
     * create credit note for discount customers of martketing partner.
     */
    public static function create_credit_note_discount_customer(){
        ci()->load->model(array(
            'partner/partner_customer_m',
            "invoices/invoice_summary_m",
            "customers/customer_m"
        ));

        $customers = ci()->partner_customer_m->get_many_by_many(array(
            "end_flag" => APConstants::OFF_FLAG
        ));
        $list_reset_customer = array();
        foreach($customers as $c){
            $create_ym = date("Ym", $c->created_date);
            $duration  = $c->duration_customer_discount;
            $target_date = strtotime("-{$duration} month");
            $target_ym = date("Ym", $target_date);
            if($target_ym > $create_ym){
                $list_reset_customer[] = $c->customer_id;
            }
        }

        if(!empty($list_reset_customer)){
            ci()->partner_customer_m->update_by_many(array(
                "end_flag" => APConstants::OFF_FLAG,
                "customer_id IN ('".  implode("','", $list_reset_customer)."')" => null
            ), array(
                "end_flag" => APConstants::ON_FLAG,
            ));
        }
        ob_flush();
        flush();

        // create credit note for list customer.
        $list_customers = ci()->partner_customer_m->get_many_by_many(array(
            "end_flag" => APConstants::OFF_FLAG
        ));

        $notificationMessage = "<h3>List customers:</h3>";
        $invoice_month = date("Ym", strtotime("last month"));
        if(!empty($list_customers)){
            foreach ($list_customers as $customer){
                $customer_id = $customer->customer_id;
                $invoice = ci()->invoice_summary_m->get_by_many(array(
                    "LEFT(invoice_month, 6)=".$invoice_month => null,
                    "customer_id" => $customer_id,
                    "(invoice_type is null OR invoice_type <> 2)" => null
                ));
                $discount_balance = $invoice->total_invoice * $customer->customer_discount / 100;

                // create credit note.
                $customerVat = CustomerUtils::getVatRateOfCustomer($customer_id);
                $vat = $customerVat->rate;
                $vatCase = $customerVat->vat_case_id;
                $invoiceSummaryId = ci()->invoice_summary_m->insert(array(
                    "vat" => $vat,
                    "vat_case" => $vatCase,
                    "customer_id" => $customer_id,
                    "invoice_month" => APUtils::getCurrentYearMonthDate(),
                    "payment_1st_flag" => APConstants::OFF_FLAG,
                    "payment_1st_amount" => $discount_balance,
                    "payment_2st_flag" => APConstants::OFF_FLAG,
                    "payment_2st_amount" => 0,
                    "total_invoice" => (-1) * ($discount_balance),
                    "invoice_type" => '2',
                    "send_invoice_flag" => APConstants::ON_FLAG,
                    "send_invoice_date" => now(),
                    "update_flag" => 0
                ));
                $invoiceCode = APUtils::generateInvoiceCodeById($invoiceSummaryId, true);
                ci()->invoice_summary_m->update_by_many(array(
                    "id" => $invoiceSummaryId
                        ), array(
                    "invoice_code" => $invoiceCode
                ));

                // Insert credit detail manual and credit note by location
                CustomerUtils::createCreditNoteByLocation($customer_id, $discount_balance * (1 + $vat) ,$invoiceSummaryId,  $invoiceCode, $customerVat);

                // export credit note.
                $invoice_file_path = ci()->export->export_invoice($invoiceCode, $customer_id);

                // send invoice
                $customer_info = ci()->customer_m->get($customer_id);
                $send_invoice_flag = "No";
                if($customer_info->auto_send_invoice_flag == APConstants::ON_FLAG){
                    $send_invoice_flag = "Yes";
                    APUtils::send_email_invoices_monthly_report($customer, $invoice_file_path, 2, $invoiceCode);
                }
                $notificationMessage .= "<div>Customer_id: ".$customer_id.", Credit note value: ".$discount_balance.", Send invoice: ".$send_invoice_flag."</div>";
            }
        }

        return $notificationMessage;
    }

    /**
     * notify to customer about term & condition.
     */
    public static function notify_term_and_condition(){
        ci()->load->model(array(
            'customers/customer_m',
            'settings/terms_service_m'
        ));

        $check_send_notify = ci()->terms_service_m->get_system_term_service(array(
            "type" => APConstants::ON_FLAG,
            "use_flag" => APConstants::ON_FLAG,
            "notify_flag" => APConstants::ON_FLAG,
            "need_customer_approval_flag" => APConstants::ON_FLAG
        ));

        //echo "<pre>";print_r($check_send_notify);exit;
        if (!empty($check_send_notify)) {
            $array_condition = array(
                "(status is NULL or status <> 1)" => NULL,
                "accept_terms_condition_flag" => APConstants::OFF_FLAG,
                "(parent_customer_id is null OR parent_customer_id = '')" => null,
            );
            $start = 0;
            $limit = 1000;

            $query_result = ci()->customer_m->get_paging($array_condition, $start, 1, 'customer_id');
            $total = $query_result['total'];
            $total_page = ceil($total / $limit);
            if ($total_page > 0) {

                $data_send = array();
                $j = 0;
                for ($i = 0; $i < $total_page; $i++) {
                    $start = $i * $limit;
                    $limit = 1000;
                    $query_result = ci()->customer_m->get_paging($array_condition, $start, $limit, 'customer_id');
                    $list_customers = $query_result['data'];
                    if (!empty($list_customers)) {
                        foreach ($list_customers as $customer) {
                            if (!empty($customer->email)) {
                                $data = array(
                                    "slug" => APConstants::send_notify_new_terms_condition,
                                    "to_email" => $customer->email,
                                    // Replace content
                                    "user_name" => $customer->user_name,
                                );
                                // Send email
                                MailUtils::sendEmailByTemplate($data);

                                $data_send[$j]['customer_id'] = $customer->customer_id;
                                $data_send[$j]['email'] = $customer->email;
                                $j++;
                            }
                        }
                    }
                }

                $check_send_notify = ci()->terms_service_m->update_by_many(array(
                    "type" => APConstants::ON_FLAG,
                    "use_flag" => APConstants::ON_FLAG,
                    "notify_flag" => APConstants::ON_FLAG,
                    "need_customer_approval_flag" => APConstants::ON_FLAG,
                    "customer_id" => "0"
                ), array(
                    "notify_flag" => APConstants::OFF_FLAG
                ));

                if (!empty($data_send)) {
                    $notificationMessage = ci()->load->view("cron/send_notify_new_terms_condition", array('data' => $data_send), true);
                }
            }
        }

        return $notificationMessage;
    }


    /**
     * Check accept term & condition of standard and enterprise cusotmer.
     * @return type
     */
    public static function deactive_customer_not_accept_new_terms_condition(){
        ci()->load->model(array(
            'customers/customer_m',
            'settings/terms_service_m'
        ));
        ci()->load->library('customers/customers_api');
        $curr_date = strtotime(APUtils::getCurrentYearMonthDate());
        $one_day = 86400;

        $check_new_terms = ci()->terms_service_m->get_system_term_service(array(
            "type" => APConstants::ON_FLAG,
            "use_flag" => APConstants::ON_FLAG,
            "need_customer_approval_flag" => APConstants::ON_FLAG,
            "effective_date >= " => $curr_date,
            "effective_date < " => $curr_date + $one_day
        ));

        $notificationMessage = "";
        if (!empty($check_new_terms)) {

            $array_condition = array(
                "(status is NULL or status <> 1)" => NULL,
                "accept_terms_condition_flag" => APConstants::OFF_FLAG,
                "(email <> '' or email is not null)" => NUll,
                "(parent_customer_id is null OR parent_customer_id = '')" => null,
            );

            $start = 0;
            $limit = 1000;

            $query_result = ci()->customer_m->get_paging($array_condition, $start, 1, 'customer_id');
            $total = $query_result['total'];
            $total_page = ceil($total / $limit);

            if ($total_page > 0) {
                $data_send = array();
                $j = 0;
                for ($i = 0; $i < $total_page; $i++) {
                    $start = $i * $limit;
                    $limit = 1000;
                    $query_result = ci()->customer_m->get_paging($array_condition, $start, $limit, 'customer_id');
                    $list_customers = $query_result['data'];

                    if (!empty($list_customers)) {
                        foreach ($list_customers as $customer) {
                            $data = array(
                                "slug" => APConstants::warning_accept_new_terms_condition,
                                "to_email" => $customer->email,
                                // Replace content
                                "user_name" => $customer->user_name,
                            );
                            // Send email
                            MailUtils::sendEmailByTemplate($data);

                            $data_send[$j]['customer_id'] = $customer->customer_id;
                            $data_send[$j]['email'] = $customer->email;
                            $j++;
                        }
                    }
                }

                ci()->customer_m->update_by_many(array(
                    "(status is NULL or status <> 1)" => NULL,
                    "accept_terms_condition_flag" => APConstants::OFF_FLAG,
                    "(email <> '' or email is not null)" => NUll
                        ), array(
                    "activated_flag" => APConstants::OFF_FLAG,
                    "deactivated_type" => APConstants::AUTO_INACTIVE_TYPE,
                    "deactivated_date" => now(),
                    "last_updated_date" => now()
                ));

                /*
                 * #1180 create postbox history page like check item page
                 *   Activity: deactivated
                 */
                $customer = ci()->customer_m->get_by_many(array(
                    "(status is NULL or status <> 1)" => NULL,
                    "accept_terms_condition_flag" => APConstants::OFF_FLAG,
                    "(email <> '' or email is not null)" => NUll,
                    "activated_flag" => APConstants::OFF_FLAG,
                    "deactivated_type" => APConstants::AUTO_INACTIVE_TYPE
                ));
                if (!empty($customer)) {
                    $customerID = $customer->customer_id;
                    $deactivated_date = $customer->deactivated_date;

                    // Get all postbox in a customer is deactivated
                    $postboxes = ci()->postbox_m->get_many_by('customer_id', $customerID);
                    // Check  postbox is deactivated in a customer
                    if (!empty($postboxes)) {
                        foreach ($postboxes as $curr_postbox) {
                            // Check postbox is not deleted
                            if (!empty($curr_postbox) &&
                                    ( $curr_postbox->deleted == '0' || ($curr_postbox->deleted == '0' && $curr_postbox->completed_delete_flag == '0'))) {
                                customers_api::addPostboxHistory($curr_postbox->postbox_id, APConstants::POSTBOX_DEACTIVATED, '');
                                // CustomerUtils::actionPostboxHistoryActivity($curr_postbox->postbox_id, APConstants::POSTBOX_DEACTIVATED, $deactivated_date, "", APConstants::INSERT_POSTBOX);
                            }
                        }
                    }
                }

                if (!empty($data_send)) {
                    $notificationMessage = ci()->load->view("cron/check_customer_accept_new_terms_condition", array('data' => $data_send), true);
                }
            }
        }

        return $notificationMessage;
    }

    /**
     * check accept term & condition of user enterprise.
     */
    public static function deactive_user_not_accept_new_terms_condition(){
        ci()->load->model(array(
            'customers/customer_m',
            'settings/terms_service_m'
        ));

        return null;
    }
    
    public static function createBonusCreditnoteOfPartner(){
        ci()->load->model(array(
            'partner/partner_marketing_profile_m',
            'partner/partner_customer_m'
        ));
        
        // list customers need bonus
        $customers = ci()->partner_marketing_profile_m->get_list_bonus_customers();
        $currentYM = APUtils::getCurrentYearMonth();
        foreach($customers as $customer){
            if($customer->bonus_month_total > $customer->bonus_month || $currentYM == $customer->bonus_current_month 
                    || $customer->status == APConstants::ON_FLAG){
                continue;
            }
            
            // add credit note bonus for customer
            CustomerUtils::createBonusCreditNoteForFirstTime($customer);
            
            // update current month bonus.
            ci()->partner_customer_m->update_by_many(array(
                "customer_id" => $customer->customer_id,
                "partner_id" => $customer->partner_id
            ), array(
                "bonus_current_month" => $currentYM,
                "bonus_month_total" => $customer->bonus_month_total + 1
            ));
        }
    }
    
    public static function calculate_enterprise_invoices(){
        ci()->load->model(array(
            'customers/customer_m'
        ));
        ci()->load->library("invoices/invoices_api");
        
        // list customers need bonus
        $customers = ci()->customer_m->get_many_by_many(array(
           "account_type" => APConstants::ENTERPRISE_CUSTOMER,
           "parent_customer_id IS NULL" => null     
        ));
        foreach($customers as $customer){
            $customer_id = $customer->customer_id;
            invoices_api::createEnterpriseInvoice($customer_id);
        }
    }
    
}