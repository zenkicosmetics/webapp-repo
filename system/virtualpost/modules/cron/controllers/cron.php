<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cron extends MY_Controller {

    // The list of Recipients to get notifications of running cronjob
    private $notifiedEmailList;
    private $curYear;
    private $curMonth;
    private $curDate;
    private $curHour;
    private $curMinute;
    private $curSecond;

    /**
     * Loads the gazillion of stuff, in Flash Gordon speed.
     */
    public function __construct() {
        parent::__construct();

        date_default_timezone_set('CET');

        // load the theme_example view
        $this->load->library(array(
            'cron_api',
            'CronUtils',
            'CronConfigs',
            'GoogleAdwards',
            'payment/payone',
            'scans/Envelope',
            'invoices/Invoices',
            'addresses/addresses_api',
            'users/users_api',
            'scans/scans_api',
            'customers/customers_api'
        ));

        $this->load->model(array(
            'scans/envelope_m',
            'scans/envelope_file_m',
            'scans/envelope_package_m',
            'email/email_m',
            'mailbox/postbox_m',
            'mailbox/postbox_setting_m',
            'customers/customer_m',
            'addresses/customers_address_m',
            'invoices/invoice_summary_m',
            'payment/payment_m',
            'addresses/location_users_m',
            'users/user_m',
            'cron_job_m',
            'cron_job_data_m',
            'mailbox/envelope_customs_m',
            'settings/terms_service_m'
        ));

        $this->notifiedEmailList = Settings::get(APConstants::CRON_MAILING_LIST);
        $this->initCurrentTime();
    }

    /**
     * Cronjob: Calculate total invoices (Tinh toan total invoice 10 phut 1 lan)
     */
    public function calculate_total_invoice() {
        ci()->load->library('invoices/invoices_api');

        $jobName = "calculate_total_invoice";
        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        // Do calculate_total_invoice
        $countInvoicesUpdated = invoices_api::calculateTotalInvoice(date('Ym'));
        $countInvoicesUpdated2 = invoices_api::calculateTotalInvoiceByLocation(date('Ym'));

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // E-mail notification
        if ($countInvoicesUpdated) {
            $notificationMsg = "There are totally {$countInvoicesUpdated} invoices have the value [invoice_summary.total_invoice] updated in this month.";
        } else {
            $notificationMsg = 'There is no invoice of this month processed by cron job';
        }
        $this->send_notification($jobName, true, $notificationMsg);

        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * - e-mail is not confirmed after 1 day - e-mail is not confirmed after 3
     * days - e-mail is not confirmed after 7 days can you catch, if an e-mail
     * cannot be delivered? (if all 3 email come back undelivered, we can
     * automatically delete the account [for this please finish the "delete
     * account" routine first])
     */
    public function check_account_registration() {
        $jobName = "check_account_registration";
        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        // Check accounts with email not confirmed yet
        $affectedList1 = $this->check_accounts_never_activated();

        // check account is not activated.
        $affectedList2 = $this->check_accounts_not_activated();

        // Check account is auto deactivated
        $affectedList3 = $this->check_accounts_auto_deactivated();

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);
        // Email notification
        if ($affectedList1 || $affectedList2 || $affectedList3) {
            $notificationMessage = CronUtils::buildHtmlTableOfDeletedAccounts($affectedList1, $affectedList2, $affectedList3);
        } else {
            $notificationMessage = 'There is no customer account processed by cron job';
        }
        $this->send_notification($jobName, true, $notificationMessage);

        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
    * Every week : back to 7 days
    * Get account information base from account status: deleted, not active, inactive, auto deactive
    * Remain account status
    */
    public function account_deletion_and_remain()
    {
        $jobName = "account_deletion_and_remain";
        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);
        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        $this->load->model('customers/customer_m');
        $curr_date = now();
        $one_day = 86400;
        $byCustomers = []; // delete by customer
        $neverActives = []; // account never active has been deleted
        $autoDeactived = []; // account delete in 70days deactive account
        $byAdmins = []; // delete by admin

        $remain_auto_deactives = [];
        $remain_manual_deactives = [];
        $customers = $this->customer_m->get_customers_by(array(
            "status" => 1,
            'deleted_date > ' => (time() - 7 * 86400)
        ));
        foreach ($customers as $key => $cust) {
            if ($cust->customer_id == $cust->deleted_by && $cust->deleted_by != NULL) {
                $byCustomers[] = $cust;
            } else {
                if ($cust->deleted_by != 0) {
                    $byAdmins[] = $cust;
                }
            }
        }
        // account deleted when not confirmed email
        $neverActives = customers_api::getAccountsDeletedUnconfirmedEmailStatusInFrame(7 * 86400, true);
        // Delete due to 70 days in-active
        $autoDeactived = customers_api::getAccountsDeletedAutoDeactivatedInFrame(7 * 86400);

        $never_avtive_accounts = customers_api::getAccountsUnconfirmedEmailStatus(); // remain never actives

        // Get list customer deactive account: by auto, by manual
        $deactive_accounts = customers_api::getAllAccountDeactived();
        foreach ($deactive_accounts as $de_key => $de_value) {
            $date_diff = $curr_date - $de_value->deactivated_date;
            if ($de_value->deactivated_type == 'auto') {
                if ($date_diff < 70 * $one_day) {
                    $remain_auto_deactives[] = $de_value; // remain auto deatives accounts due to 70days
                }
            } else if ($de_value->deactivated_type == 'manual') {
                $remain_manual_deactives[] = $de_value; // remain manual deactives accounts
            }
        }
        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);
        // Email notification
        if ($byCustomers || $neverActives || $autoDeactived || $byAdmins || $never_avtive_accounts
                || $remain_auto_deactives || $remain_manual_deactives) {
            $notificationMessage = CronUtils::buildHtmlTableOfDeletedAndRemainAccounts($byCustomers, $neverActives, $autoDeactived, $byAdmins, $never_avtive_accounts, $remain_auto_deactives, $remain_manual_deactives);
        } else {
            $notificationMessage = 'There is no customer account processed by cron job';
        }
        $this->send_notification($jobName, true, $notificationMessage);

        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * e-mail for the invoice (invoice attached) Export invoice report and Send
     * an email to customer by monthly.
     * This method runs at first day of this
     * month. The data invoice will be get from previous month.
     */
    public function send_invoice_monthly_report() {
        $this->load->library('invoices/export');

        $jobName = 'send_invoice_monthly_report';

        $this->checkCronjobKey($jobName);

        // If it is the last day of the current month, the system will do sending invoice at 19:00 (Only check if run from cron job)
        $direct = $this->input->get_post('direct');
        if ($direct != '1') {
            //$this->checkCronjobStatus($jobName);
        }

        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        echo "Start to send invoice monthly report" . '<br/>';
        $total_customer_count = 0;

        // Gets all customer.
        $customers = $this->customer_m->get_many_by_many(
                array(
                    "email_confirm_flag" => APConstants::ON_FLAG,
                    "activated_flag" => APConstants::ON_FLAG,
                    "charge_fee_flag" => APConstants::ON_FLAG,
                    "(status IS NULL OR status <> '1')" => NULL,
                    "auto_send_invoice_flag" => APConstants::ON_FLAG
        ));

        // Display total customer
        echo 'Total Customer:' . count($customers) . '<br/>';
        log_audit_message('error', 'Total Customer:' . count($customers) . '<br/>', false, 'send-invoice');

        // Gets target month to export invoice report.
        $previous_month = date("Ym", strtotime("first day of previous month"));

        // $previous_month = date("Ym");
        foreach ($customers as $customer) {
            $customer_id = $customer->customer_id;
            $invoice_summary = $this->invoice_summary_m->get_by_many(array(
                'customer_id' => $customer_id,
                'invoice_file_path IS NOT NULL' => NULL,
                "(send_invoice_flag IS NULL OR send_invoice_flag <> '1')" => NULL,
                'total_invoice > 0' => NULL,
                'invoice_month' => $previous_month
            ));

            // Truong hop ko ton tai hoac da gui roi
            if (empty($invoice_summary) || $invoice_summary->send_invoice_flag == '1') {
                echo 'Customer ID:' . $customer_id . '<br/>';
                continue;
            }

            echo 'Customer ID:' . $customer_id . '|Invoice Summary ID:' . $invoice_summary->id . '<br/>';
            log_audit_message('error', 'Customer ID:' . $customer_id . '|Invoice Summary ID:' . $invoice_summary->id . '<br/>', false, 'send-invoice');

            // Export pdf report.
            $file_export = $invoice_summary->invoice_file_path;

            log_audit_message('error', 'file export:' . $file_export . '<br/>', false, 'send-invoice');

            // send mail with invoice report attackment.
            if ($file_export) {
                // Update 2 st payment flag
                $this->invoice_summary_m->update_by_many(
                        array(
                    'customer_id' => $customer_id,
                    'id' => $invoice_summary->id
                        ), array(
                    'send_invoice_flag' => '1',
                    'send_invoice_date' => now()
                ));
                $this->send_email_invoices_monthly_report($customer, $file_export, $invoice_summary->invoice_code);

                log_audit_message('error', 'send success:' . $file_export . '<br/>', false, 'send-invoice');
            }
            $total_customer_count++;
        }

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // Notify
        $this->send_notification($jobName, true);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Send email notify for all customer have new incomming request.
     * Don't user now. Will send email directly when add new incoming
     */
    public function send_email_notify() {
        $jobName = 'send_email_notify';

        $this->checkCronjobKey($jobName);
        //$this->checkCronjobStatus($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        // 1. Daily notification.
        $list_notify_daily = $this->notify_incomming_item_daily();

        $current_date = DateTimeUtils::getCurrentYearMonthDate();
        $first_day_of_week = DateTimeUtils::convert_timestamp_to_date(DateTimeUtils::getFirstDayOfWeek(date('Y-m-d')), 'Ymd');
        $first_day_of_month = DateTimeUtils::getFirstDayOfCurrentMonth();

        // 2. Weekly notification.
        $list_notify_weekly = array();
        if ($current_date == $first_day_of_week) {
            $list_notify_weekly = $this->notify_incomming_item_weekly();
        }

        // 3. Monthly notification.
        $list_notify_monthly = array();
        if ($current_date == $first_day_of_month) {
            $list_notify_monthly = $this->notify_incomming_item_monthly();
        }

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        $notificationMessage = '';
        if (!empty($list_notify_daily) || !empty($list_notify_weekly) || !empty($list_notify_monthly)) {
            $notificationMessage = CronUtils::buildHtmlTableOfNotifyIncommingItem($list_notify_daily, $list_notify_weekly, $list_notify_monthly);
        } else {
            $notificationMessage = 'No data executed';
        }

        // Notify
        $this->send_notification($jobName, true, $notificationMessage);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Tinh toan invoices cho phan store item.
     */
    public function calculate_invoices() {
        $this->load->library('customers/customers_api');
        $this->load->library('price/price_api');

        $jobName = 'calculate_invoices';
        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        // count all customers not deleted
        $total_customer = $this->customer_m->count_by_many(array(
            "status" => APConstants::OFF_FLAG
        ));
        // Gets all pricings template
        $pricings = price_api::getAllPricingsGroupByTemplate();
        echo $total_customer ."<br/>";
        $limit = 1000;
        $start = 0;
        $page = ($total_customer / $limit) + 1;
        $list = array();
        for($i=0; $i <= $page; $i++){
            $start = $i * $limit;
            $accounts = $this->customer_m->db->select("customer_id")->limit($limit, $start)->where("status", APConstants::OFF_FLAG)->get('customers')->result();
            if (!empty($accounts)) {
                foreach ($accounts as $account) {
                    $customerID = $account->customer_id;
                    array_push($list, $customerID);
                    $this->invoices->calculate_invoice($customerID, $pricings, false);
                }
            }
        }

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // E-mail notification
        if ($list) {
            $notificationMsg = CronUtils::buildHtmlTableOfCustomerInvoices($list);
        } else {
            $notificationMsg = 'There is no customer invoice calculated';
        }
        $this->send_notification($jobName, true, $notificationMsg);

        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Tinh toan phi postbox va invoice summary.
     */
    public function calculate_invoices_directly() {
        $jobName = 'calculate_invoices_directly';

        $customer_id = $this->input->get_post('customer_id');

        //$this->checkCronjobKey($jobName);

        $this->invoices->calculate_invoice($customer_id);

        // Notify
        //$this->send_notification(__METHOD__, true);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Warning trigger when credit card expires.
     * trigger 60 days and 30 days before a credit card expires, so that the customer gets a warning e-mail account must
     * then be deactivated when credit card has expired.
     * Reactivation only through new payment details. Change email trigger to 4 weeks, 2 weeks and 1 week
     */
    public function check_card_expire_date() {
        $jobName = 'check_card_expire_date';
        $this->checkCronjobKey($jobName);

        // Only run this job on Tuesday (0-Sunday ~ 6-Saturday)
        if (date('w') != '2') {
            echo CronConfigs::EXEC_CRONJOB_PROCESS_AUTO_STOPPED;
            exit();
        }

        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // Gets all cards
        $all_cards = $this->payment_m->get_all_cards_of_active_user();
        $from_email = '';

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');
        $arr_data_sent = array();
        $i = 0;
        foreach ($all_cards as $card) {

            if (!$card->expired_year || !$card->expired_month) {
                continue;
            }

            $to_email = $card->email;
            $data = array(
                "full_name" => $card->user_name,
                "site_url" => APContext::getFullBalancerPath()
            );

            // Calculate number of days before this card expired
            $expired_year = $card->expired_year;
            if (strlen($card->expired_year) == 2) {
                $expired_year = '20' . $card->expired_year;
            }
            $end_date = date("Ymt", strtotime($expired_year . $card->expired_month . '01'));
            $start_date = date('Ymd', now());
            $diff = strtotime($end_date) - strtotime($start_date);
            $number_of_date = floor($diff / (60 * 60 * 24));

            // Year of expiration date of credit card == current year
            // trigger 60 days. Change to 4 weeks
            if ($number_of_date == 28) {

                $email_template_trigger_60_days = $this->email_m->get_by('slug', APConstants::email_is_confirmed_card_expired_date_remain_sixty_days);
                $content = MailUtils::parserString($email_template_trigger_60_days->content, $data);
                MailUtils::sendEmail($from_email, $to_email, $email_template_trigger_60_days->subject, $content);

                $arr_data_sent[$i] = array(
                    "full_name" => $card->user_name,
                    "customer_id" => $card->customer_id,
                    "to_email" => $card->email,
                    "email_slug" => APConstants::email_is_confirmed_card_expired_date_remain_sixty_days
                );
                $i++;
            } // Trigger 30 days ==> change to 2 weeks
            else if ($number_of_date == 14) {

                $email_template_trigger_30_days = $this->email_m->get_by('slug', APConstants::email_is_confirmed_card_expired_date_remain_thirty_days);
                $content = MailUtils::parserString($email_template_trigger_30_days->content, $data);
                MailUtils::sendEmail($from_email, $to_email, $email_template_trigger_30_days->subject, $content);

                $arr_data_sent[$i] = array(
                    "full_name" => $card->user_name,
                    "customer_id" => $card->customer_id,
                    "to_email" => $card->email,
                    "email_slug" => APConstants::email_is_confirmed_card_expired_date_remain_thirty_days
                );
                $i++;
            } // trigger for 7 days
            else if ($number_of_date == 7) {

                $email_template_trigger_7_days = $this->email_m->get_by('slug', APConstants::email_is_confirmed_card_expired_date_remain_seven_days);
                $content = MailUtils::parserString($email_template_trigger_7_days->content, $data);
                MailUtils::sendEmail($from_email, $to_email, $email_template_trigger_7_days->subject, $content);

                $arr_data_sent[$i] = array(
                    "full_name" => $card->user_name,
                    "customer_id" => $card->customer_id,
                    "to_email" => $card->email,
                    "email_slug" => APConstants::email_is_confirmed_card_expired_date_remain_seven_days
                );
                $i++;
            } // trigger for expired yesterday.
            else if ($number_of_date == -1) {
                // credit card expires yesterday
                PaymentUtils::changeCreditCardHasExpiredDate($card->customer_id, $card->payment_id, $to_email, $from_email, $data);
                $arr_data_sent[$i] = array(
                    "full_name" => $card->user_name,
                    "customer_id" => $card->customer_id,
                    "to_email" => $card->email,
                    "email_slug" => APConstants::email_change_new_payment_method_standard
                );
                $i++;
            }
        }
        if (!empty($arr_data_sent)) {
            $notificationMessage = ci()->load->view("notify_check_card_expire_date", array('arr_data_sent' => $arr_data_sent), true);
        } else {
            $notificationMessage = "No customer have been sent email";
        }
        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);
        $this->send_notification($jobName, true, $notificationMessage);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * The job to delete postboxes at the last day of current month
     */
    public function delete_postbox() {
        $jobName = 'delete_postbox';
        $this->checkCronjobKey($jobName);

        // Only run this cron job at the last day of current month
        if (!APUtils::isFirstDayOfMonth()) {
            echo CronConfigs::EXEC_CRONJOB_PROCESS_AUTO_STOPPED;
            exit();
        }

        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        // Delete all postbox have plan_deleted_date is previous month
        $plan_delete_date = APUtils::getFirstDayOfCurrentMonth();

        // Gets all deleted postbox
        $postboxes = $this->postbox_m->get_many_by_many(
                array(
                    "deleted" => 1,
                    "plan_deleted_date" => $plan_delete_date
                )
        );

        $notificationMessage = "No postbox be deleted by cron job delete_postbox";
        if (!empty($postboxes)) {

            foreach ($postboxes as $p) {
                 /*
                 * #1180 create postbox history page like check item page
                 *   Activity: deleted
                 */
                APUtils::deletePostbox($p->postbox_id, $p->customer_id, APConstants::POSTBOX_DELETE);
                APUtils::updateAccountType($p->customer_id);

                $this->postbox_m->update_by_many(array(
                    "postbox_id" => $p->postbox_id,
                    "customer_id" => $p->customer_id
                        ), array(
                    "plan_deleted_date" => null
                ));
            }
            $notificationMessage = ci()->load->view("delete_postbox", array('postboxes' => $postboxes), true);
        }

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // Notify
        $this->send_notification($jobName, true, $notificationMessage);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Job delete envelope da bi danh dau la deleted
     */
    public function delete_envelope_old30() {
        $this->load->library('scans/scans_api');

        $jobName = 'delete_envelope_old30';
        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        $envelopesMarkedForDeletion = scans_api::getAllEnvelopesNeedToTrashed();
        if ($envelopesMarkedForDeletion) {
            foreach ($envelopesMarkedForDeletion as $envelope) {
                $envelopeID = $envelope->id;
                $customerID = $envelope->to_customer_id;
                LogUtils::log_delete_envelope_by_id($envelopeID, $customerID); // log for envelope history
                scans_api::deleteEnvelope($customerID, $envelopeID);
            }
        }

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // E-mail notification
        if ($envelopesMarkedForDeletion) {
            $notificationMsg = CronUtils::buildHtmlTableOfDeleteEnvelopeOld30($envelopesMarkedForDeletion);
        } else {
            $notificationMsg = 'There is no envelope marked for deletion on yesterday to delete now!';
        }
        $this->send_notification($jobName, true, $notificationMsg);

        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * The folder "items in storage" should only include physical items, that have not been send or trashed after 30 days.
     * if an item has been shipped, it cannot be in this folder.
     */
    public function storage_envelope_old30() {
        $this->load->library('invoices/Invoices');

        $jobName = 'storage_envelope_old30';
        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // time start
        $startTime = date("Y-m-d H:i:s");

        // Calculate storage invoices
        $totalEnvelopesCalculation = $this->invoices->cal_store_invoices();

        // Summary storage from envelopes to invoice_summary
        $totalCustomersCalculation = $this->invoices->cal_storage_summary();

        // time end
        $endTime = date("Y-m-d H:i:s");
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // E-mail notification
        $notificationMsg = "There are {$totalEnvelopesCalculation} were calculated for storage invoices.<br>";
        $notificationMsg .= "There are {$totalCustomersCalculation} were calculated for summary storage fee from envelopes to invoice summary";
        $this->send_notification($jobName, true, $notificationMsg);

        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Only run for urgent case
     */
    public function cal_storage_summary_directly() {
        $jobName = 'cal_storage_summary_directly';

        $customer_id = $this->input->get_post('customer_id');

        $this->checkCronjobKey($jobName);

        // Summary storage from envelopes to invoice_summary
        $this->invoices->cal_storage_summary($customer_id);

        // Notify
        $this->send_notification($jobName, true);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Only run for ugent case
     */
    public function storage_envelope_old30_directly() {
        $this->load->library('invoices/Invoices');

        $jobName = 'storage_envelope_old30_directly';
        $this->checkCronjobKey($jobName);

        // Calculate storage invoices
        $totalEnvelopesCalculation = $this->invoices->cal_store_invoices();

        // E-mail notification
        $notificationMsg = "There are {$totalEnvelopesCalculation} envelopes was calculated storage fee";
        $this->send_notification($jobName, true, $notificationMsg);

        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Only run for ugent case
     */
    public function cal_storage_summary_backdate_directly() {
        $jobName = 'cal_storage_summary_backdate_directly';

        $customer_id = $this->input->get_post('customer_id');

        $this->checkCronjobKey($jobName);

        // Calculate storage invoices
        $this->invoices->cal_storage_summary_backdate($customer_id, '2016', '04', '03');

        // Notify
        $this->send_notification($jobName, true);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Job apply private and business postbox
     */
    public function apply_new_account() {
        $jobName = 'apply_new_account';
        $this->load->library('addresses/addresses_api');
        $this->load->library('customers/customers_api');
        $this->load->model('customers/customer_m');
        $this->load->model('mailbox/postbox_m');
        $this->load->model('addresses/location_m');

        // check cron key
        $this->checkCronjobKey($jobName);

        // Get all postbox need delete today (yyyyMMdd)
        $today = DateTimeUtils::getCurrentYearMonthDate();
        if ($today != DateTimeUtils::getFirstDayOfCurrentMonth()) {
            echo CronConfigs::EXEC_CRONJOB_PROCESS_AUTO_STOPPED;
            return false;
        }
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        $startTime = date('Y-m-d H:i:s');
        $notificationMessage = "There is no customer have been change new account type";

        // TODO:
        //$this->checkCronjobStatus($jobName);
        // Only process if this is first day of month
        // Delete all postbox have plan_deleted_date is previsous month
        $postboxes = $this->postbox_m->get_many_by_many(array(
            "plan_date_change_postbox_type" => $today
        ));
        // For each customer need apply new account type
        $data_tracking = array();
        $i = 0;
        foreach ($postboxes as $postbox) {
            $data_tracking[$i]['customer_id'] = $postbox->customer_id;
            $data_tracking[$i]['old_account_type'] = $postbox->type;
            $data_tracking[$i]['new_account_type'] = $postbox->new_postbox_type;

            // Get all postbox type need changed
            $this->postbox_m->update_by_many(array(
                "postbox_id" => $postbox->postbox_id
                    ), array(
                "type" => $postbox->new_postbox_type,
                "plan_date_change_postbox_type" => null,
                "updated_date" => now(),
                "new_postbox_type" => null,
                "apply_date" => null
            ));

            // update account type
            APUtils::updateAccountType($postbox->customer_id);

            // gets customer
            $customer = $this->customer_m->get($postbox->customer_id);

            // Get postbox info
            $postbox_name = $postbox->postbox_name;
            $name = $postbox->name;
            $company = $postbox->company;

            // Send email when has new account
            if ($postbox->new_postbox_type == APConstants::BUSINESS_TYPE) {
                // add action postbox history: Upgrade  from  private to bussiness
                // CustomerUtils::actionPostboxHistoryActivity($postbox->postbox_id, APConstants::POSTBOX_UPGRADE, strtotime($today), $postbox->new_postbox_type, APConstants::INSERT_POSTBOX);
                customers_api::addPostboxHistory($postbox->postbox_id, APConstants::POSTBOX_UPGRADE, $postbox->new_postbox_type, $postbox->type);
                // Account type
                $account_type = "Business";

                // Get info location
                $location = $this->location_m->getLocationInfo($postbox->location_available_id);

                $to_email = Settings::get(APConstants::MAIL_CONTACT_CODE);
                $data = array(
                    "slug" => APConstants::new_business_account_notification,
                    "to_email" => $to_email,
                    // Replace content
                    "user_name" => $customer->user_name,
                    "email" => $customer->email,
                    "account_type" => $account_type,
                    "postbox_name" => $postbox_name,
                    "name" => $name,
                    "company" => $company,
                    "street" => $location->street,
                    "postcode" => $location->postcode,
                    "city" => $location->city,
                    "region" => $location->region,
                    "country" => $location->country_name,
                    "location_email" => $location->email,
                    "location_phone" => $location->phone_number
                );

                // Send email
                MailUtils::sendEmailByTemplate($data);

                // Send email to customer
                $data = array(
                    "slug" => APConstants::new_business_account_notification_for_customer,
                    "to_email" => $customer->email,
                    // Replace content
                );
                // Send email
                MailUtils::sendEmailByTemplate($data);
            } else {
                // 1080 Add action down grade postbox history
                customers_api::addPostboxHistory($postbox->postbox_id, APConstants::POSTBOX_DOWNGRADE, $postbox->new_postbox_type, $postbox->type);
            }

            // Send email downgraded
            if ($postbox->new_postbox_type != APConstants::BUSINESS_TYPE && $postbox->type == APConstants::BUSINESS_TYPE) {
                // Downgrade to  AS YOU GO, private From bussiness, private
                // CustomerUtils::actionPostboxHistoryActivity($postbox->postbox_id, APConstants::POSTBOX_DOWNGRADE, strtotime($today), $postbox->new_postbox_type, APConstants::INSERT_POSTBOX);

                $to_email = Settings::get(APConstants::MAIL_CONTACT_CODE);
                $data = array(
                    "slug" => APConstants::downgraded_business_account,
                    "to_email" => $to_email,
                    // Replace content
                    "user_name" => $customer->user_name,
                    "email" => $customer->email,
                    "postbox_name" => $postbox_name,
                    "name" => $name,
                    "company" => $company
                );
                // Send email
                MailUtils::sendEmailByTemplate($data);
            }
            $i++;
        }

        if (!empty($data_tracking)) {
            $notificationMessage = ci()->load->view("apply_new_account", array('data' => $data_tracking), true);
        }
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);
        $this->send_notification($jobName, true, $notificationMessage);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Job collect shipping item (will run everyday or daily, monthly or quarterly)
     * This job will run only one time a day at 00:20
     */
     public function collect_shipping_envelope() {
        ci()->load->library('scans/scans_api');
        ci()->load->library('mailbox/mailbox_api');
        ci()->load->library('scans/incoming_api');

        $jobName = 'collect_shipping_envelope';
        $this->checkCronjobKey($jobName);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // Get all postboxes have envelope item marked for collective shipping request
        $collectiveShippingPostboxes = scans_api::getAllPostboxesRequestForCollectiveShipping();

        if ($collectiveShippingPostboxes) {

            foreach ($collectiveShippingPostboxes as $collectiveShippingPostbox) {

                $location_id = $collectiveShippingPostbox->location_available_id;
                $customer_id = $collectiveShippingPostbox->customer_id;
                $postboxes = mailbox_api::getPostboxesByLocationID($customer_id, $location_id);
                $customer_setting = APContext::getCustomerByID($customer_id);

                //Process collect shipment for postboxes by location
                foreach ($postboxes as $postbox) {

                    //Get postbox setting
                    $postbox_setting = mailbox_api::getPostboxSetting($postbox->customer_id, $postbox->postbox_id);
                    if (empty($postbox_setting)) {
                        continue;
                    }

                    $is_collect = false;

                    // Check if postbox have collect daily
                    if (($postbox_setting->collect_mail_cycle == '1') || ($postbox_setting->collect_mail_cycle == '2')
                            || ($postbox_setting->collect_mail_cycle == '3') || ($postbox_setting->collect_mail_cycle == '4')) {

                        $weekday_shipping = $postbox_setting->weekday_shipping;
                        $current_weekday = date('w') + 1;

                        if (($postbox_setting->collect_mail_cycle === '1')) {
                            $is_collect = true;
                        }

                        //Weekly
                        if (($postbox_setting->collect_mail_cycle === '2') && ($weekday_shipping == $current_weekday)) {
                            $is_collect = true;
                        }

                        // If this is monthly
                        if ($postbox_setting->collect_mail_cycle == '3') {
                            // Check if this is first week and this is weekday shipping
                            $current_day = date('d');
                            $lastDayofMonth = date("t");
                            if (( $current_day >= ($lastDayofMonth - 7) ) && ( $current_day <= $lastDayofMonth) && ($weekday_shipping == $current_weekday)) {
                                $is_collect = true;
                            }
                        }

                        // If this is collect shipping
                        if ($postbox_setting->collect_mail_cycle == '4' && APUtils::isNextDayOfQuart($postbox_setting->last_modified_date)) {
                            $is_collect = true;
                        }

                        $message = "This is setting for: customer: " . $postbox->customer_id;
                        $message .= " \n postbox_id: " . $postbox->postbox_id;
                        $message .= " \n collect_mail_cycle: " . $postbox_setting->collect_mail_cycle;
                        $message .= "\n weekday_shipping: " . $postbox_setting->weekday_shipping;
                        $message .= "\n current_weekday: " . (date('w') + 1);
                        $message .= "\n current_day: " . date('d') . "\n lastDayofMonth: " . date("t") . "\n";
                        $message .= "\n -------------------------------------------------- \n ";
                        log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'collect_shipping_tracking_setting_');

                        //Process collect shipment
                        if ($is_collect) {
                            // Gets all items mark collect shippment of postbox.
                            $allCollectiveShippingItems = $this->envelope_m->get_all_envelope_must_declare_customs($postbox->postbox_id);

                            // #1012 Check pre-payment
                            if (count($allCollectiveShippingItems) == 0) {
                                continue;
                            }

                            $list_collect_envelope_id = array();
                            foreach ($allCollectiveShippingItems as $envelope) {
                                $list_collect_envelope_id[] = $envelope->id;
                            }

                            scans_api::insertCompleteItem($list_collect_envelope_id, APConstants::TRIGGER_ITEM_COLLECT_FORWARDING_BY_SYSTEM_ACTIVITY_TYPE);

                            //Process trigger collect shipping
                            if (count($allCollectiveShippingItems) == 1) {
                                // trong truong hop chi co 1 item, se chuyen thanh direct shipping.
                                // // Add direct shipping request by system
                                scans_api::insertCompleteItem($list_collect_envelope_id, APConstants::DIRECT_FORWARDING_ORDER_BY_SYSTEM_ACTIVITY_TYPE);
                                //Check custom declaration
                                $check_flag = EnvelopeUtils::check_customs_flag($customer_id, $postbox->postbox_id, $allCollectiveShippingItems[0]-> id);
                                if($check_flag == APConstants::ON_FLAG){
                                    mailbox_api::regist_envelope_customs($customer_id, $allCollectiveShippingItems[0]->id, $postbox->postbox_id, APConstants::DIRECT_FORWARDING);
                                    //Add waiting declare custom
                                    scans_api::insertCompleteItem($list_collect_envelope_id, APConstants::WAITING_FOR_CUSTOMS_DECLARITON_ACTIVITY_TYPE);
                                    // send email notification for declare custom.
                                    incoming_api::send_email_declare_customs($customer_setting);
                                    continue;
                                }

                                // check prepayment process.
                                $check_prepayment_data = CustomerUtils::checkApplyShippingPrepayment(APConstants::TRIGGER_ACTION_TYPE_SYSTEM,
                                                                                                APConstants::SHIPPING_SERVICE_NORMAL,
                                                                                                APConstants::SHIPPING_TYPE_DIRECT,
                                                                                                $list_collect_envelope_id, $customer_id, false);

                                // If this package need to required pre-payment
                                if ($check_prepayment_data['prepayment'] === true) {
                                    //Send email data
                                    $open_balance_due = $check_prepayment_data['open_balance_due'];
                                    $open_balance_this_month = $check_prepayment_data['open_balance_this_month'];
                                    $total_prepayment_cost = $check_prepayment_data['estimated_cost'];

                                     // Add collect shipping request to queue
                                    // collect_shipping_flag = 2(Organe)
                                    mailbox_api::requestDirectShippingToQueue($list_collect_envelope_id, $customer_id);

                                    //Add request prepayment for direct shipment by system
                                    scans_api::insertCompleteItem($list_collect_envelope_id, APConstants::REQUEST_PREPAYMENT_FOR_DIRECT_FORWARDING_BY_SYSTEM_ACTIVITY_TYPE);

                                    $customer = APContext::getCustomerByID($customer_id);
                                    CustomerUtils::sendPrepaymentEmail($customer_id, $customer->email, $open_balance_due, $open_balance_this_month, $total_prepayment_cost);
                                    continue;
                                }

                                //Request shipment succesfully
                                mailbox_api::requestDirectShipping($list_collect_envelope_id, $customer_id);
                                //Add activity request tracking number
                                scans_api::insertCompleteItem($list_collect_envelope_id, APConstants::REQUEST_TRACKING_NUMBER_ACTIVITY_TYPE);

                            } else {
                                // trong truong hop collect shipping many items
                                $package_id = EnvelopeUtils::get_customs_package_id($allCollectiveShippingItems[0]->id);
                                if (empty($package_id)) {
                                    $package_id = scans_api::createCollectiveShippingPackage($customer_id, $postbox->location_available_id);
                                }

                                $declare_customs_flag = EnvelopeUtils::apply_collect_customs_process($customer_id, $postbox->postbox_id, $package_id);
                                if ($declare_customs_flag == APConstants::ON_FLAG) {
                                    //Add waiting declare custom activity
                                    scans_api::insertCompleteItem($list_collect_envelope_id, APConstants::WAITING_FOR_CUSTOMS_DECLARITON_ACTIVITY_TYPE);
                                    // send email notification for declare custom.
                                    incoming_api::send_email_declare_customs($customer_setting);
                                    continue;
                                }

                                // check prepayment process.
                                $check_prepayment_data = CustomerUtils::checkApplyShippingPrepayment(APConstants::TRIGGER_ACTION_TYPE_SYSTEM,
                                                                                                    APConstants::SHIPPING_SERVICE_NORMAL,
                                                                                                    APConstants::SHIPPING_TYPE_COLLECT,
                                                                                                    $list_collect_envelope_id, $customer_id, false);

                                // If this package need to required pre-payment
                                if ($check_prepayment_data['prepayment'] === true) {

                                    $open_balance_due = $check_prepayment_data['open_balance_due'];
                                    $open_balance_this_month = $check_prepayment_data['open_balance_this_month'];
                                    $total_prepayment_cost = $check_prepayment_data['estimated_cost'];

                                     // Add collect shipping request to queue
                                    // collect_shipping_flag = 2(Organe)
                                    mailbox_api::requestCollectShippingToQueue($list_collect_envelope_id, $customer_id);

                                    //Insert activity request prepayment for collect shipment
                                    scans_api::insertCompleteItem($list_collect_envelope_id, APConstants::REQUEST_PREPAYMENT_FOR_COLLECT_FORWARDING_BY_SYSTEM_ACTIVITY_TYPE);

                                    $customer = APContext::getCustomerByID($customer_id);
                                    CustomerUtils::sendPrepaymentEmail($customer_id, $customer->email, $open_balance_due, $open_balance_this_month, $total_prepayment_cost);
                                    CustomerUtils::sendAutoForwardNotWorking($customer_id, $customer->email, $postbox->postbox_name, $open_balance_due);
                                    continue;
                                }

                                $message = "Collect shipping for Customer:" . $customer_id . " - Packages: " . $package_id . " - Location_id: " . $location_id . "\n";
                                $message .= "Postbox_id: " . $postbox->postbox_id;
                                $message .= "\n ---------------------------------------------------------------\n";
                                log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'collect_shipping_');


                                // Update package_id of envelope
                                $declare_customs_obj = scans_api::updatePackageIDForAllCollectiveShippingItems($customer_id, $location_id, $package_id, $postbox->postbox_id);
                                if ($declare_customs_obj['declare_customs_flag'] != APConstants::ON_FLAG) {
                                    //Request tracking number
                                    scans_api::insertCompleteItem($list_collect_envelope_id, APConstants::REQUEST_TRACKING_NUMBER_ACTIVITY_TYPE);
                                }


                            }

                        } // end if ($is_collect)
                    }
                } // End foreach ($postboxes as $postbox)
            } // End foreach $collectiveShippingPostboxes
        } //End if exist list collective postboxes

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // Email notification
        $this->send_notification($jobName, true);

        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * If the customer has an invoice address or postbox address on must verify
     * and the verification has not yet been completed,
     * AND there are items in the account, then we need a daily automated email
     * going out to the customer warning him,
     * that storage cost for the items in the account will increase and he needs
     * to verify the account to get access to the items,
     * to scan, forward or trash them.
     * (our welcome letter excluded)
     */
    public function notify_verification_address_postbox() {
        $jobName = 'notify_verification_address_postbox';

        $this->checkCronjobKey($jobName);

        $customers = $this->customer_m->get_list_customers_must_verify_case();
        if ($customers) {
            foreach ($customers as $row) {
                $customer_id = $row->customer_id;
                $customer_email = $row->email;

                $items = $this->envelope_m->get_by_many(
                        array(
                            "to_customer_id" => $customer_id,
                            "RIGHT(envelope_code, 4) <> '_000'" => null
                ));

                if ($items) {
                    // Send mail notification.
                    $data = array();
                    $this->sendEmailRemind(APConstants::account_has_envelope_must_verify_case, $customer_email, $data);
                }
            }
        }

        // Notify
        $this->send_notification($jobName, true);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Get all new customer (only extractly 24 hour after register)
     */
    public function send_first_letter() {
        $this->load->library('customers/customers_api');
        $this->load->library('scans/Envelope');

        $jobName = 'send_first_letter';
        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        $newCustomers = customers_api::getNewCustomersRegisteredWithin24h();
        foreach ($newCustomers as $customer) {
            $this->envelope->add_first_letter($customer->customer_id, $customer->postbox_id);
        }

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // E-mail notification
        if ($newCustomers) {
            $notificationMsg = CronUtils::buildHtmlTableOfNewCustomersRegisteredIn24h($newCustomers);
        } else {
            $notificationMsg = 'There is no new customer registered in 24 hours';
        }
        $this->send_notification($jobName, true, $notificationMsg);

        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Delete all customer have [delete_plan_date = current_date - 1 )
     */
    public function delete_plan_customer() {
        $this->load->library('customers/customers_api');

        $jobName = 'delete_plan_customer';
        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        // Get yesterday
        $currentDate = new \DateTime();
        $yesterday = $currentDate->sub(new DateInterval('P1D'))->format('Ymd');

        // Get the list of customers who are going to delete their account
        $customers = customers_api::getCustomersByPlanDeleteDate($yesterday);
        $list = array();
        if ($customers) {
            foreach ($customers as $customer) {
                $customerID = $customer->customer_id;

                // Gets current balance
                $balance = CustomerUtils::getAdjustOpenBalanceDue($customerID);
                $totalBalance = $balance["OpenBalanceDue"] + $balance['OpenBalanceThisMonth'];

                $item = array(
                    'CustomerCode' => $customer->customer_code,
                    'OpenBalanceDue' => $balance["OpenBalanceDue"],
                    'OpenBalanceThisMonth' => $balance['OpenBalanceThisMonth']
                );
                array_push($list, $item);
                $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_SYSTEM;

                // Delete logic: if customer has open balance -> insert into blacklist.
                if ($totalBalance > 0) {
                     /*
                     * #1180 create postbox history page like check item page
                     *   Activity: APConstants::POSTBOX_DELETE_ORDER_BY_SYSTEM
                     */
                    CustomerUtils::deleteCustomer($customerID, true, true, null, null, $created_by_id);
                } else {
                     /*
                     * #1180 create postbox history page like check item page
                     *   Activity: APConstants::POSTBOX_DELETE_ORDER_BY_SYSTEM
                     */
                    CustomerUtils::deleteCustomer($customerID, true, false, null, null, $created_by_id);
                }
            }
        }

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // E-mail notification
        if ($list) {
            $notificationMsg = CronUtils::buildHtmlTableOfPlanDeleteCustomers($list);
        } else {
            $notificationMsg = 'There is no customer deleted by cron job';
        }
        $this->send_notification($jobName, true, $notificationMsg);

        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Update all postbox code.
     */
    public function update_post_code() {
        $this->load->library('mailbox/mailbox_api');

        $jobName = 'update_post_code';
        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        $postboxes = mailbox_api::getPostboxesWithoutPostCode();
        if ($postboxes) {
            foreach ($postboxes as $postbox) {
                mailbox_api::updatePostboxForPostCode($postbox->customer_id, $postbox->location_available_id, $postbox->postbox_id);
            }
        }

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // E-mail notification
        if ($postboxes) {
            $notificationMsg = CronUtils::buildHtmlTableOfUpdatePostboxCode($postboxes);
        } else {
            $notificationMsg = 'There is no postbox needs to update postbox code.';
        }
        $this->send_notification($jobName, true, $notificationMsg);

        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * e-mail for the invoice (invoice attached) Export invoice report and Send
     * an email to customer by monthly.
     * This method runs at first day of this
     * month. The data invoice will be get from previous month.
     */
    public function generate_invoice_pdf() {
        $this->load->library('invoices/export');

        $jobName = 'generate_invoice_pdf';
        $customer_id = $this->input->get_post('customer_id');
        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        // Call library to export all pdf
        $this->export->export_all_pdf($customer_id);

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // Notify
        $this->send_notification($jobName, true);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Send email notify open balance due at the first day of month 01:00:00
     */
    public function send_email_notify_open_balance_due() {
        $jobName = 'send_email_notify_open_balance_due';

        $this->checkCronjobKey($jobName);
        //$this->checkCronjobStatus($jobName, '01', '00', '00');
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        $total_customer_count = 0;

        // Get all customers
        $company_name = Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE);
        $bank_name = Settings::get(APConstants::INSTANCE_OWNER_BANK_NAME_CODE);
        $iban = Settings::get(APConstants::INSTANCE_OWNER_IBAN_CODE);
        $bic = Settings::get(APConstants::INSTANCE_OWNER_SWIFT_CODE);

        $customers = $this->customer_m->get_all_customer_for_notify_open_balance();
        if ($customers) {
            foreach ($customers as $customer) {
                // dont send email to user enterprise.
                if($customer->account_type == APConstants::ENTERPRISE_TYPE && !empty($customer->parent_customer_id)){
                    continue;
                }

                $open_balance_due = APUtils::number_format(APUtils::getCurrentBalance($customer->customer_id));
                if ($open_balance_due < 0.01) {
                    continue;
                }

                // Prepare email to send
                try {
                    // Build email content
                    $data = array(
                        "slug" => APConstants::email_notify_open_balance_due,
                        "to_email" => $customer->email,
                        // Replace content
                        "full_name" => $customer->user_name,
                        "open_balance_due" => $open_balance_due,
                        "company_name" => $company_name,
                        "bank_name" => $bank_name,
                        "iban" => $iban,
                        "bic" => $bic
                    );

                    // Send email
                    MailUtils::sendEmailByTemplate($data);

                    $total_customer_count++;
                } catch (Exception $e) {
                    // Notify
                    $this->send_notification($jobName, false);
                    echo CronConfigs::EXEC_CRONJOB_PROCESS_ERROR;
                }
            }
        }

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // Notify
        $this->send_notification($jobName, true);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Send email notify open balance due at the 10 th day of month 01:00:00
     */
    public function send_email_notify_deactivate_open_balance_due() {
        $jobName = 'send_email_notify_deactivate_open_balance_due';

        //$currentHour = date('H');
        $total_customer_count = 0;

        $this->checkCronjobKey($jobName);
        //$this->checkCronjobStatus($jobName, $currentHour, '00', '00');
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        // Get all customers
        $company_name = Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE);
        $bank_name = Settings::get(APConstants::INSTANCE_OWNER_BANK_NAME_CODE);
        $iban = Settings::get(APConstants::INSTANCE_OWNER_IBAN_CODE);
        $bic = Settings::get(APConstants::INSTANCE_OWNER_SWIFT_CODE);

        $customers = $this->customer_m->get_all_customer_for_notify_open_balance();
        if ($customers) {
            echo "Total customers: " . count($customers) . '<br/>';
            foreach ($customers as $customer) {
                // dont send email to user enterprise.
                if($customer->account_type == APConstants::ENTERPRISE_TYPE && !empty($customer->parent_customer_id)){
                    continue;
                }

                $customer_id = $customer->customer_id;
                $open_balance_due = APUtils::getCurrentBalance($customer->customer_id);

                $message = " Start for Customer:" . $customer_id . " - open_balance_due: " . $open_balance_due . "\n";
                $message .= "status:" . $customer->status . " - charge_fee_flag:" . $customer->charge_fee_flag . " - invoice_type: " . $customer->invoice_type . "\n";
                $message .= "Activated_flag: " . $customer->activated_flag . " - Date: " . date("d-m-Y H:i:s");
                $message .= "\n ----------------------------------------------------------- \n";
                log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'send_email_notify_deactivate_');

                if ($open_balance_due < 0.01 || $customer->activated_flag == APConstants::OFF_FLAG) {
                    continue;
                }

                // Prepare email to send
                try {

                    $message = "Try for Customer:" . $customer_id . " - open_balance_due: " . $open_balance_due . "\n";
                    $message .= "status:" . $customer->status . " - charge_fee_flag:" . $customer->charge_fee_flag . " - invoice_type: " . $customer->invoice_type . "\n";
                    $message .= "Activated_flag: " . $customer->activated_flag . " - Date: " . date("d-m-Y H:i:s");
                    $message .= "\n ----------------------------------------------------------- \n";
                    log_audit_message(APConstants::LOG_INFOR, $message, FALSE, 'send_email_notify_deactivate_');


                    // Deactivate this customer
                    log_audit_message(APConstants::LOG_INFOR, 'Deactivated customer by cron job:' . $customer_id, FALSE, 'send_email_notify_deactivate_');
                    $this->customer_m->update_by_many(array(
                        "customer_id" => $customer_id
                            ), array(
                        "activated_flag" => APConstants::OFF_FLAG,
                        "deactivated_type" => APConstants::AUTO_INACTIVE_TYPE,
                        "deactivated_date" => now(),
                        "payment_detail_flag" => APConstants::OFF_FLAG,
                        "last_updated_date" => now()
                            )
                    );

                    //#1309: Insert customer history
                    $history = [
                        'customer_id' => $customer_id,
                        'action_type' => APConstants::CUSTOMER_HISTORY_ACTIVITY_CHANGE_STATUS,
                        'created_by_id' => APConstants::CUSTOMER_HISTORY_CREATED_BY_SYSTEM,
                        'current_data' => APConstants::CUSTOMER_HISTORY_STATUS_DELETED,
                    ];

                    // update: convert registration process flag to customer_product_setting.
                    CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'payment_detail_flag', APConstants::OFF_FLAG);
                    customers_api::insertCustomerHistory([$history]);
                    // Build email content
                    $data = array(
                        "slug" => APConstants::send_email_notify_deactivate_open_balance_due,
                        "to_email" => $customer->email,
                        // Replace content
                        "full_name" => $customer->user_name,
                        "open_balance_due" => $open_balance_due,
                        "company_name" => $company_name,
                        "bank_name" => $bank_name,
                        "iban" => $iban,
                        "bic" => $bic
                    );

                    // Send email
                    MailUtils::sendEmailByTemplate($data);

                    $total_customer_count++;
                } catch (Exception $e) {

                    $message = "Catch for Customer:" . $customer_id;
                    $message .= "\n ----------------------------------------------------------- \n";
                    log_audit_message(APConstants::LOG_ERROR, $message, FALSE, 'send_email_notify_deactivate_');

                    // Notify
                    $this->send_notification($jobName, false);
                    echo CronConfigs::EXEC_CRONJOB_PROCESS_ERROR;
                }
            }
        }

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // Notify
        $this->send_notification($jobName, true);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Test function.
     */
    public function get_all_customer_nopayment_method() {
        $this->load->model('addresses/customers_address_m');
        $this->load->model('settings/countries_m');
        $this->load->model('payment/payment_m');

        $jobName = 'get_all_customer_nopayment_method';

        $this->checkCronjobKey($jobName);

        $customers = $this->customer_m->get_customer_paging(array(), 0, 10000, '');
        if ($customers) {
            echo '<table border = "1">';
            foreach ($customers['data'] as $customer) {
                $customer_id = $customer->customer_id;
                $all_payments = $this->payment_m->get_many_by_many(
                        array(
                            "customer_id" => $customer_id
                ));
                $active_payments = $this->payment_m->get_many_by_many(
                        array(
                            "customer_id" => $customer_id,
                            "primary_card" => APConstants::ON_FLAG
                ));
                if (count($all_payments) > 0 && count($active_payments) == 0) {
                    echo "<tr>";
                    echo "<td>" . $customer->customer_id . "</td>";
                    echo "<td>" . $customer->email . "</td>";
                    echo "</tr>";
                }
            }
            echo "</table>";
        }

        // Notify
        $this->send_notification($jobName, true);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Sync clevvermail customer to mailchimp
     */
    public function sync_customer_mailchimp() {
        $jobName = 'sync_customer_mailchimp';
        $baseline_customer_id = 'baseline_customer_id';
        $baseline_deleted_date = 'baseline_deleted_date';
        $this->checkCronjobKey($jobName);

        ini_set('memory_limit', '-1');

        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        $list_id = Settings::get(APConstants::MAILCHIMP_LIST_ID);
        echo 'Start sync_customer_mailchimp. ListID:' . $list_id . '<br/>';

        // Load library
        $this->load->library('MailchimpLib/MailchimpV3', Settings::get(APConstants::MAILCHIMP_API_KEY), 'mail_chimp');

        //Get cron data for subcribe customer
        $cron_job_data_customer_subcribe = $this->cron_job_data_m->get_by_many(array(
            "job_name" => $jobName,
            "param_name" => $baseline_customer_id
        ));
        //Get customer to subcribe
        if (!empty($cron_job_data_customer_subcribe)) {
            $customer_id = $cron_job_data_customer_subcribe->param_value;
            $array_where = array(
                'customers.customer_id >' => $customer_id
            );
            $customers = $this->customer_m->get_all_customer_great_than_baseline($array_where);
        } else {
            $customers = $this->customer_m->get_all_customer();
        }

        echo 'Total subcribe customers' . count($customers) . '<br/>';
        flush();
        ob_flush();

        //Process subcribe to mailchimp for each customer
        $customer_id_finally = 0;

        if (!empty($customers)) {
            //Add customer information to prepair sync data
            $members = array();
            foreach ($customers as $customer) {
                $customer_id_finally = $customer->customer_id;

                $firstname = $customer->invoicing_address_name;
                $email = $customer->email;
                $merge_fields = array(
                    'FNAME' => $firstname
                );

                $members[] = array("email_address" => $email, "status" => "subscribed", "merge_fields" => $merge_fields);
            }
            //Subcribe to mailchimp. Each API call can sync up to 500 members
            foreach (array_chunk($members, 500) as $member) {
                try {
                    $result = $this->mail_chimp->syncSubscribe($list_id, $member);
                    echo 'Sync Customer ID:' . $customer->customer_id . '<br/>';
                    echo 'Result:' . json_encode($result) . '<br/>';
                    flush();
                    ob_flush();
                } catch (Exception $e) {
                    // Notify
                    // $this->send_notification($jobName, false);
                    echo CronConfigs::EXEC_CRONJOB_PROCESS_ERROR . '<br/>';
                    flush();
                    ob_flush();
                }
            }
        }

        //Update subcribe cron data
        if ($customer_id_finally) {
            if ($cron_job_data_customer_subcribe) {
                $array_where = array(
                    "job_name" => $jobName,
                    "param_name" => $baseline_customer_id
                );
                $data = array("param_value" => $customer_id_finally);
                //Update to db
                cron_api::updateBaselineCustomerBy($array_where, $data);
            } else {
                $data = array(
                    "job_name" => $jobName,
                    "param_name" => $baseline_customer_id,
                    "param_value" => $customer_id_finally
                );
                //Insert to db
                cron_api::insertBaselineCustomerBy($data);
            }
        }

        //Get cron data for unsubcribe customer
        $cron_job_data_unsubcribe = $this->cron_job_data_m->get_by_many(array(
            "job_name" => $jobName,
            "param_name" => $baseline_deleted_date
        ));

        //Get customer to unsubcribe
        if (!empty($cron_job_data_unsubcribe)) {
            $deleted_date = $cron_job_data_unsubcribe->param_value;
            $array_where = array('deleted_date >' => $deleted_date);
            $deleted_customers = $this->customer_m->get_all_customer_delete_by($array_where);
        } else {
            $deleted_customers = $this->customer_m->get_many_by_many(array(
                'status' => APConstants::ON_FLAG
            ));
        }

        echo 'Total unsubcribe customers' . count($deleted_customers) . '<br/>';
        flush();
        ob_flush();

        $deleted_date_finally = 0;
        if (!empty($deleted_customers)) {
            //Add customer information to prepair sync data
            $members = array();
            foreach ($deleted_customers as $customer) {
                $deleted_date_finally = $customer->deleted_date;

                $email = $customer->email;

                $members[] = array("email_address" => $email, "status" => "unsubscribed");
            }
            //Subcribe to mailchimp. Each API call can sync up to 500 members
            foreach (array_chunk($members, 500) as $member) {
                try {
                    $result = $this->mail_chimp->syncSubscribe($list_id, $member);
                    echo 'Sync Customer ID:' . $customer->customer_id . '<br/>';
                    echo 'Result:' . json_encode($result) . '<br/>';
                    flush();
                    ob_flush();
                } catch (Exception $e) {
                    // Notify
                    // $this->send_notification($jobName, false);
                    echo CronConfigs::EXEC_CRONJOB_PROCESS_ERROR;
                }
            }
        }

        //Update unsubcribe cron data
        if ($deleted_date_finally) {
            if ($cron_job_data_unsubcribe) {
                $array_where_deleted = array(
                    "job_name" => $jobName,
                    "param_name" => $baseline_deleted_date
                );
                $data_deleted = array("param_value" => $deleted_date_finally);
                //Update to db
                cron_api::updateBaselineCustomerBy($array_where_deleted, $data_deleted);
            } else {
                $data_deleted = array(
                    "job_name" => $jobName,
                    "param_name" => $baseline_deleted_date,
                    "param_value" => $deleted_date_finally
                );
                //Insert to db
                cron_api::insertBaselineCustomerBy($data_deleted);
            }
        }

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // Notify
        $this->send_notification($jobName, true);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Calculate open balance due every 10 minutes.
     */
    public function update_open_balance_due() {
        $jobName = 'update_open_balance_due';

        $this->checkCronjobKey($jobName);
        //$this->checkCronjobStatus($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');
        $targetYM = APUtils::getCurrentYearMonth();

        // Get all customer
        $customers = $this->customer_m->get_many_by_many(array(
            "(status IS NULL or status <> '1') OR (status = 1 AND from_unixtime(deleted_date, '%Y%m') = '" . $targetYM . "')" => null
        ));

        $result_update = array();
        if ($customers) {
            echo 'Total customer: ' . count($customers);
            $i = 0;
            foreach ($customers as $customer) {
                $customer_id = $customer->customer_id;
                log_audit_message(APConstants::LOG_INFOR, 'Update open balance due:' . $customer_id, FALSE, 'update_open_balance_due_');
                $result = APUtils::updateOpenBalanceToDB($customer_id);

                $result_update[$i]['customer_id'] = $customer_id;
                $result_update[$i]['OpenBalanceDue'] = $result['OpenBalanceDue'];
                $result_update[$i]['OpenBalanceThisMonth'] = $result['OpenBalanceThisMonth'];

                $i++;
            }
        }

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        $notificationMessage = '';
        if (count($result_update)) {
            $notificationMessage = CronUtils::buildHtmlUpdateOpenBalanceDue($result_update);
        } else {
            $notificationMessage = 'No data update';
        }

        // Notify
        $this->send_notification($jobName, true, $notificationMessage);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * For test only
     */
    public function google_adwards() {
        $jobName = 'google_adwards';

        $this->checkCronjobKey($jobName);

        // make list customer for upload conversion offline
        $current_date = new DateTime();

        $customers = $this->customer_m->get_many_by_many(
                array(
                    'created_date <= ' . $current_date->getTimestamp() => null,
                    'created_date >= ' . $current_date->sub(new DateInterval('P75D'))->getTimestamp() => null,
                    'activated_flag' => APConstants::ON_FLAG
                )
        );

        if ($customers) {
            $conversions = array();
            foreach ($customers as $customer) {
                if (empty($customer->google_click_id)) {
                    continue;
                }
                $conversions[] = array(
                    'gclid' => $customer->google_click_id,
                    'value' => APUtils::calculate_customer_lifetime($customer->customer_id),
                    'date' => $customer->created_date
                );
            }

            try {
                // google_click_id created_date
                GoogleAdwards::sendOfflineConversionFeed($conversions);
            } catch (Exception $e) {
                log_message('error', $e->getMessage(), FALSE);

                // Notify
                $this->send_notification($jobName, false, $e->getMessage());
                echo CronConfigs::EXEC_CRONJOB_PROCESS_ERROR;
            }
        }

        // Notify
        $this->send_notification($jobName, true);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Update currency exchange rate with the base of EUR
     */
    public function update_currency_exchange_rate() {
        $jobName = 'update_currency_exchange_rate';
        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        // load model
        $this->load->model('settings/currencies_m');
        // load library
        $this->load->library('settings/settings');

        // Prefix currency
        $prefix = "EUR";

        // The request URL prefix
        $request = Settings::get(APConstants::CODE_API_YAHOO_CHANGED_CURRENCY_RATE);

        // The request parameters( store and format)
        $store = 'store://datatables.org/alltableswithkeys';
        $format = 'json';

        //currencies
        $currencies = $this->currencies_m->get_all();

        // The request parameters (query)
        if ($currencies) {
            foreach ($currencies as $currency) {

                $currency_short = $currency->currency_short;

                $curr[] = $prefix . $currency_short;
            }
        }
        $query = 'select * from yahoo.finance.xchange where pair in ("' . implode('","', $curr) . '")';

        // urlencode and concatenate the POST arguments
        $postargs = 'q=' . urlencode($query) . '&env=' . urlencode($store) . '&format=' . $format;
        // return response
        $response = APUtils::callRemoteUrl($request, $postargs);
        log_audit_message(APConstants::LOG_INFOR, json_encode($response), FALSE, 'update_currency_exchange_rate');

        // Json decode
        $result = json_decode($response, true);
        $i = 0;
        $result_update = array();
        if ($currencies && isset($result['query']['results']['rate'])) {

            $rates = $result['query']['results']['rate'];
            foreach ($currencies as $currency) {

                $currency_id = $currency->currency_id;
                $currency_rate = $currency->currency_rate;
                $currency_short = $currency->currency_short;

                $exchange_rate = 0;
                foreach ($rates as $r) {
                    if ('EUR' . $currency_short == $r['id']) {
                        $exchange_rate = $r['Rate'];
                        //date update
                        $date = new DateTime($r['Date']);
                        $updated_date = $date->getTimestamp();
                        break;
                    }
                }
                if ($exchange_rate) {
                    $result_update[] = array(
                        "currency_rate" => $exchange_rate,
                        "last_updated_date" => $updated_date,
                        "currency_id" => $currency_short
                    );

                    $this->currencies_m->update($currency_id, array(
                        'currency_rate' => $exchange_rate,
                        'last_updated_date' => time()
                    ));
                }
            }
        }

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        $notificationMessage = '';
        if (count($result_update)) {
            $notificationMessage = CronUtils::buildHtmlUpdateCurrencyExchangeRate($result_update);
        } else {
            $notificationMessage = 'No data update';
        }

        // Notify
        $this->send_notification($jobName, true, $notificationMessage);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    public function update_pricing_template() {
        $this->load->library('addresses/addresses_api');
        $this->load->library('price/price_api');

        // Just run the cron job at the beginning of the month (on the 1st day of current month)
        if (intval(date('d')) != 1)
            return false;

        $jobName = 'update_pricing_template';
        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        // For reporting of cron job
        $reportData = array();

        $locations = addresses_api::getAllLocations();
        foreach ($locations as $location) {
            $nextPricingTemplateID = $location->next_pricing_template_id;
            if ($nextPricingTemplateID) {

                // Get data for report line
                $locationID = $location->id;
                $locationName = $location->location_name;
                $currentPricingTemplateID = $location->pricing_template_id;
                $currentPricingTemplateName = price_api::getPricingTemplateNameByID($currentPricingTemplateID);
                $nextPricingTemplateName = price_api::getPricingTemplateNameByID($nextPricingTemplateID);
                $reportLine = array($locationID, $locationName, $currentPricingTemplateID, $currentPricingTemplateName, $nextPricingTemplateID, $nextPricingTemplateName);
                array_push($reportData, $reportLine);

                // Update pricing_template
                $updateData = array(
                    'pricing_template_id' => $nextPricingTemplateID,
                    'next_pricing_template_id' => null
                );

                $nextEnterprisePricingTemplateID = $location->next_enterprise_pricing_template_id;
                if (!empty($nextEnterprisePricingTemplateID)) {
                    $updateData['enterprise_pricing_template_id'] = $nextEnterprisePricingTemplateID;
                    $updateData['next_enterprise_pricing_template_id'] = null;
                }
                addresses_api::updateLocationByID($locationID, $updateData);
            }
        }

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // E-mail notification
        if ($reportData) {
            $notificationMsg = CronUtils::buildHtmlTableOfUpdatePricingTemplate($reportData);
        } else {
            $notificationMsg = 'There is no location have pricing template changed for this month';
        }
        $this->send_notification($jobName, true, $notificationMsg);

        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * update verification case on admin.
     *
     * @return void|boolean
     */
    public function cancel_verification_cases() {
        $this->load->library('customers/customers_api');

        $jobName = 'cancel_verification_cases';
        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        // cancel all verifications cases.
        $customers = customers_api::getCustomersCaseVerifyAddress();
        $list = array();
        if ($customers) {
            foreach ($customers as $customer) {
                $customerID = $customer->customer_id;
                array_push($list, $customerID);
                CaseUtils::start_verification_case($customerID);
            }
        }

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // E-mail notification
        if ($list) {
            $notificationMsg = CronUtils::buildHtmlTableOfCustomersCaseVerifyAddress($list);
        } else {
            $notificationMsg = 'There is no customer case verification address';
        }
        $this->send_notification($jobName, true, $notificationMsg);

        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Get all deleted customer with open balance not equal 0
     */
    public function get_deleted_customer_withopenbalance() {
        $this->load->model('customers/customer_m');

        $customers = $this->customer_m->get_many_by_many(array(
            // "status" => 1
            "(status IS NULL OR status <> '1')" => NULL,
            "charge_fee_flag" => APConstants::ON_FLAG
        ));
        foreach ($customers as $customer) {
            $customerId = $customer->customer_id;
            $total = CustomerUtils::getAdjustOpenBalanceDue($customerId);
            $total_balance = $total['OpenBalanceDue'] + $total['OpenBalanceThisMonth'];
            $last_payment_info = PaymentUtils::get_last_payment_info($customerId);
            $last_payment_date = $last_payment_info['last_payment_date'] > 0 ? APUtils::convert_timestamp_to_date($last_payment_info['last_payment_date']) : '';
            ;
            $last_payment_type = $last_payment_info['last_payment_type'];
            $activated_flag = $customer->activated_flag;
            $deactivated_type = $customer->deactivated_type;
            if ($total_balance != 0 && $total['OpenBalanceDue'] > 0.01) {
                // if ($total_balance != 0 && abs($total) > 0.01) {
                echo $customer->customer_code . '|' . $activated_flag . '|' . $deactivated_type . '|' . number_format($total['OpenBalanceDue'], 2) . '|' . number_format($total['OpenBalanceThisMonth'], 2) . '|' . $last_payment_date . '|' . $last_payment_type . '<br/>';

                // Delete customer again to create credit note or invoice
                // CustomerUtils::deleteCustomer($customerId, true, true);
                flush();
                ob_flush();
            }
        }
        echo "Successful.";
    }

    /**
     * Send push message.
     *
     */
    public function send_push_message() {
        $this->load->library('cron_api');

        $jobName = 'send_push_message';
        $this->checkCronjobKey($jobName);

        // send ios.
        $result = cron_api::sendPushIOS();
        echo "The number of success item/total: " . $result['success_ios_item'] . '/' . $result['total_ios_item'];

        // send anddroid.
        $result = cron_api::sendPushAndroid();
        echo "The number of success item/total: " . $result['success_android_item'] . '/' . $result['total_android_item'];
    }

    public function auto_check_jobs_execution() {
        ci()->load->library('cron/JobListDescription');

        $jobName = 'auto_check_jobs_execution';
        //$this->checkCronjobKey($jobName);
        $jobListDescription = new JobListDescription();
        $expectedJobs = $jobListDescription->getExpectedExecJobs();
        $borderColor = 'border: solid 1px #988F9E;';
        $html = <<<HTML
<table style="{$borderColor} border-collapse: collapse;" cellpadding="10px" cellspacing="0">
    <thead>
        <tr style="{$borderColor}">
            <th style="{$borderColor} font-weight: bold; text-align: center;">#</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Today's cron jobs</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Expected start time</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Actual execution time</th>
            <th style="{$borderColor} font-weight: bold; text-align: left;">Status</th>
        </tr>
    </thead>
    <tbody>
HTML;
        foreach ($expectedJobs as $index => $expectedJob) {
            $i = $index + 1;
            $jobName = $expectedJob->getJobName();
            $expectedStartTime = $expectedJob->getJobStartTime();
            $jobTimes = CronUtils::getJobTimesArray();
            $actualJob = $this->cron_job_m->getExecutedJob($jobName, $jobTimes);
            $actualExecutionTime = $actualJob ? $actualJob->job_status : '';
            $jobStatus = CronUtils::checkJobStatus($actualExecutionTime);
            $style = ($jobStatus == 'SUCCESS') ? '' : 'style="color: red;"';
            $tr = <<<HTML
        <tr style="{$borderColor}">
            <td style="{$borderColor}">{$i}</td>
            <td style="{$borderColor}">{$jobName}</td>
            <td style="{$borderColor}">{$expectedStartTime}</td>
            <td style="{$borderColor}">{$actualExecutionTime}</td>
            <td style="{$borderColor}"><span {$style}>{$jobStatus}</span></td>
        </tr>
HTML;
            $html .= $tr;
        }
        $ending = <<<HTML
    </tbody>
</table>
HTML;
        $html .= $ending;
        $environment = (ENVIRONMENT == 'production') ? 'LIV' : 'DEV';
        $domainName = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
        $currentTime = date('Y-m-d H:i:s');
        $subject = sprintf("[%s][%s][%s]: Auto check Jobs execution!", $environment, $domainName, $currentTime);

        MailUtils::sendEmail('', $this->notifiedEmailList, $subject, $html);

        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    private function clean_first_letter() {
        $customers = ci()->customer_m->get_all();
        foreach ($customers as $customer) {
            $envelopes = ci()->envelope_m->get_many_by_many(
                    array(
                        "incomming_letter_flag" => '1',
                        "to_customer_id" => $customer->customer_id
            ));
            if (count($envelopes) > 1) {
                for ($i = 0; $i < count($envelopes) - 1; $i++) {
                    ci()->envelope_m->delete($envelopes[$i]->id);
                }
            }
        }
    }

    /**
     * Using for update one time only (03092014)
     */
    private function update_country() {
        $key = $this->input->get_post('key');
        if ($key != '5eb96ffccee348403fbf2cd4a0addca0') {
            return;
        }
        $this->load->model('settings/countries_m');
        $this->load->model('addresses/customers_address_m');

        // Get all address
        $addresses = $this->customers_address_m->get_all();
        $ship_count = 0;
        foreach ($addresses as $address) {
            $ship_country = $this->countries_m->get_by_many(
                    array(
                        "id" => $address->shipment_country
            ));
            if ($ship_country) {
                // Update shipment country by id
                $this->customers_address_m->update_by_many(
                        array(
                    "customer_id" => $address->customer_id
                        ), array(
                    "eu_member_flag" => $ship_country->eu_member_flag
                ));
                $ship_count++;
            }
        }

        echo "Shipment address update: " . $ship_count . '/' . count($addresses);
    }

    /**
     * Update VAT case
     */
    private function update_vat_case() {
        // Get all customer
        $customers = $this->customer_m->get_all();
        foreach ($customers as $customer) {
            $customer_id = $customer->customer_id;
            $vat_obj = CustomerUtils::getVatRateOfCustomer($customer_id);

            // Update vat
            $this->invoice_summary_m->update_by_many(
                    array(
                "customer_id" => $customer_id,
                "vat IS NULL" => null
                    ), array(
                'vat' => $vat_obj->rate,
                'vat_case' => $vat_obj->vat_case_id
            ));
            echo "Update VAT Rate." . $customer_id . '<br/>';
        }
    }

    /**
     * Thanh toan dinh ky
     */
    private function payment() {
        if (APUtils::isLastDayOfMonth()) {
            // Check the end of date
            if (APUtils::isEndOfDay()) {
                // Gop chung vao payment_2st()
                // $this->payment_1st();
                $this->payment_2st();
            }
        }
    }

    /**
     * Update card number
     */
    private function update_card_number() {
        // Load library
        $this->load->library('payment/payone');
        $key = $this->input->get_post('key');
        if ($key != '5eb96ffccee348403fbf2cd4a0addca0') {
            return;
        }
        $this->load->model('payment/payment_m');

        // Get all payment number have
        $payments = $this->payment_m->get_many_by_many(array(
            "pseudocardpan IS NULL" => null
        ));
        // For each payment
        foreach ($payments as $payment) {
            // Call check credit card
            $result = $this->payone->check_credit_card($payment->card_number, $payment->card_type, $payment->cvc, $payment->expired_year, $payment->expired_month);
            if ($result['status']) {
                // Update pseudocardpan
                $this->payment_m->update_by_many(
                        array(
                    "payment_id" => $payment->payment_id
                        ), array(
                    "card_number" => $result['truncatedcardpan'],
                    "pseudocardpan" => $result['pseudocardpan']
                ));
            }
        }
    }

    /**
     * Get all customer have pending fee of previous month and make payment
     * again.
     */
    private function auto_make_pending_payment() {
        $key = $this->input->get_post('key');
        if ($key != '5eb96ffccee348403fbf2cd4a0addca0') {
            return;
        }
        $this->load->model('invoices/invoice_summary_m');
        $this->load->model('customers/customer_m');
        $this->load->model('payment/payment_m');
        $this->load->library('payment/payone');
        $customers = $this->invoice_summary_m->get_all_customer_pending_fee();
        if ($customers && count($customers) > 0) {
            foreach ($customers as $customer) {
                $customer_id = $customer->customer_id;
                $payment_result = $this->payone->make_pending_payment($customer_id);
                // If make payment successfully
                if ($payment_result) {
                    // Update data to customer
                    $this->customer_m->update_by_many(
                            array(
                        "customer_id" => $customer_id
                            ), array(
                        "payment_detail_flag" => APConstants::ON_FLAG
                    ));
                    // update: convert registration process flag to customer_product_setting.
                    CustomerProductSetting::set($customer_id, APConstants::CLEVVERMAIL_PRODUCT, 'payment_detail_flag', APConstants::ON_FLAG);

                    $this->payment_m->update_by_many(
                            array(
                        "customer_id" => $customer_id
                            ), array(
                        "card_confirm_flag" => APConstants::ON_FLAG
                    ));

                    $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
                    $open_balance = $open_balance_data['OpenBalanceDue'];
                    if ($open_balance <= 0.1) {
                        // we dont have to tell the Customer that a valid credit Card with non working payment can reactivate the account.
                        // in most cases the Customer will Chose a Card that can handle the payment if it is valid
                        // Only reactivate if deactivated_type = auto
                        customers_api::reactivateCustomerWhenPaymentSuccess($customer_id);
                    }
                }
            }
        }
    }

    /**
     * Check accounts with email not confirmed
     */
    private function check_accounts_never_activated() {
        $affectedList = array();
        $curr_date = now();
        $one_day = 86400;

        $accounts = customers_api::getAccountsUnconfirmedEmailStatus();
        foreach ($accounts as $account) {
            // do not send email notification to user + auto-deactivate user by cronjob.
            // dont send email to user enterprise.
            if($account->account_type == APConstants::ENTERPRISE_TYPE && !empty($account->parent_customer_id)){
                continue;
            }

            if (empty($account->activated_key)) {
                customers_api::updateAccountActivationKey($account->customer_id);
            }
            $activatedUrl = APContext::getFullBalancerPath() . "customers/active?key=" . $account->activated_key;
            $data = array(
                "full_name" => $account->user_name,
                "site_url" => APContext::getFullBalancerPath(),
                "active_url" => $activatedUrl
            );
            $date_diff = $curr_date - $account->created_date;

            // send mail notification.
            if ($date_diff >= $one_day && $date_diff < 2 * $one_day) {
                $this->sendEmailRemind(APConstants::email_is_not_confirmed_after_one_day, $account->email, $data);
            } else if ($date_diff >= 3 * $one_day && $date_diff < 4 * $one_day) {
                $this->sendEmailRemind(APConstants::email_is_not_confirmed_after_three_days, $account->email, $data);
            } else if ($date_diff >= 7 * $one_day && $date_diff < 8 * $one_day) {
                $this->sendEmailRemind(APConstants::email_is_not_confirmed_after_seven_days, $account->email, $data);
            } else if ($date_diff >= 8 * $one_day && $date_diff < 9 * $one_day) {
                // delete account
                array_push($affectedList, $account->customer_code);
                $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_SYSTEM;
                customers_api::autoDeactivateAccount($account->customer_id, $created_by_id);
            }
        }

        return $affectedList;
    }

    /**
     * check account is not activated.
     */
    private function check_accounts_not_activated() {
        $affectedList = array();
        $curr_date = now();
        $one_day = 86400;

        $accounts = customers_api::getAccountsInactiveStatus();
        foreach ($accounts as $account) {
            // do not send email notification to user + auto-deactivate user by cronjob.
            // dont send email to user enterprise.
            if($account->account_type == APConstants::ENTERPRISE_TYPE && !empty($account->parent_customer_id)){
                continue;
            }

            $customerId = $account->customer_id;
            // Gets customer setting
            $active_flag = CustomerProductSetting::get_activate_flags($customerId);

            $setup_process_missing = "";
            $i = 0;
            if($active_flag['shipping_address_completed'] != "1"){ $i++;
                $setup_process_missing .= "<span style='color: red;'>".$i.". Shipping address incomplete</span><br/><br/>";
            }
            if($active_flag['invoicing_address_completed'] != "1"){ $i++;
                $setup_process_missing .= "<span style='color: red;'>".$i.". Invoicing address incomplete</span><br/><br/>";
            }
            if($active_flag['postbox_name_flag'] != "1"){ $i++;
                $setup_process_missing .= "<span style='color: red;'>".$i.". Postbox name incomplete</span><br/><br/>";
            }
            if($active_flag['name_comp_address_flag'] != "1"){ $i++;
                $setup_process_missing .= "<span style='color: red;'>".$i.". Name/company in address incomplete</span><br/><br/>";
            }
            if($active_flag['city_address_flag'] != "1"){ $i++;
                $setup_process_missing .= "<span style='color: red;'>".$i.". City for address incomplete</span><br/><br/>";
            }
            if($active_flag['payment_detail_flag'] != "1"){ $i++;
                $setup_process_missing .= "<span style='color: red;'>".$i.". Payment details incomplete</span><br/><br/>";
            }
            if($active_flag['email_confirm_flag'] != "1"){ $i++;
                $setup_process_missing .= "<span style='color: red;'>".$i.". E-Mail confirmation incomplete</span><br/><br/>";
            }
            if($active_flag['accept_terms_condition_flag'] != "1"){ $i++;
                $setup_process_missing .= "<span style='color: red;'>".$i.". Terms & Conditions incomplete</span><br/><br/>";
            }

            $data = array(
                "full_name" => $account->user_name,
                "site_url" => APContext::getFullBalancerPath(),
                "setup_process_missing" => $setup_process_missing
            );
            $date_diff = $curr_date - $account->created_notify_date;

            // send mail notification.
            if ($date_diff >= 8 * $one_day && $date_diff < 9 * $one_day) {
                $this->sendEmailRemind(APConstants::account_is_not_activated_after_eight_days, $account->email, $data);
            } else if ($date_diff >= 30 * $one_day && $date_diff < 31 * $one_day) {
                $this->sendEmailRemind(APConstants::account_is_not_activated_after_thirty_days, $account->email, $data);
            } else if ($date_diff >= 60 * $one_day && $date_diff < 61 * $one_day) {
                $this->sendEmailRemind(APConstants::account_is_not_activated_after_sixty_days, $account->email, $data);
            } else if ($date_diff >= 70 * $one_day) {
                // Gets current balance
                $balance = CustomerUtils::getAdjustOpenBalanceDue($customerId);
                $totalBalance = $balance["OpenBalanceDue"] + $balance['OpenBalanceThisMonth'];
                $textBalance = ' OpenBalance: ' . $balance["OpenBalanceDue"] . ' ; ' . $balance['OpenBalanceThisMonth'];
                array_push($affectedList, $account->customer_code . $textBalance);
                // $history store data to insert customer_history table
                $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_SYSTEM;
                // Delete logic: if customer has open balance -> insert into blacklist.
                if ($totalBalance > 0) {
                    /*
                     * #1180 create postbox history page like check item page
                     *   Activity: APConstants::POSTBOX_DELETE_ORDER_BY_SYSTEM
                     */
                    CustomerUtils::deleteCustomer($customerId, true, true, null, null, $created_by_id);
                } else {
                    /*
                     * #1180 create postbox history page like check item page
                     *   Activity: APConstants::POSTBOX_DELETE_ORDER_BY_SYSTEM
                     */
                    CustomerUtils::deleteCustomer($customerId, true, false, null, null, $created_by_id);
                }
                // Send mail
                $this->sendEmailRemind(APConstants::account_has_been_deleted, $account->email, $data);
            }
        }

        return $affectedList;
    }

    /**
     * check account is not activated.
     */
    private function check_accounts_auto_deactivated() {
        $affectedList = array();
        $curr_date = now();
        $one_day = 86400;

        $accounts = customers_api::getAccountsAutoDeactivated();
        foreach ($accounts as $account) {
            // do not send email notification to user + auto-deactivate user by cronjob.
            // dont send email to user enterprise.
            if($account->account_type == APConstants::ENTERPRISE_TYPE && !empty($account->parent_customer_id)){
                continue;
            }

            // Gets customer setting
            $active_flag = CustomerProductSetting::get_activate_flags($account->customer_id);

            $customerId = $account->customer_id;
            $setup_process_missing = "";
            $i = 0;
            if($active_flag['shipping_address_completed'] != "1"){ $i++;
                $setup_process_missing .= "<span style='color: red;'>".$i.". Shipping address incomplete</span><br/><br/>";
            }
            if($active_flag['invoicing_address_completed'] != "1"){ $i++;
                $setup_process_missing .= "<span style='color: red;'>".$i.". Invoicing address incomplete</span><br/><br/>";
            }
            if($active_flag['postbox_name_flag'] != "1"){ $i++;
                $setup_process_missing .= "<span style='color: red;'>".$i.". Postbox name incomplete</span><br/><br/>";
            }
            if($active_flag['name_comp_address_flag'] != "1"){ $i++;
                $setup_process_missing .= "<span style='color: red;'>".$i.". Name/company in address incomplete</span><br/><br/>";
            }
            if($active_flag['city_address_flag'] != "1"){ $i++;
                $setup_process_missing .= "<span style='color: red;'>".$i.". City for address incomplete</span><br/><br/>";
            }
            if($active_flag['payment_detail_flag'] != "1"){ $i++;
                $setup_process_missing .= "<span style='color: red;'>".$i.". Payment details incomplete</span><br/><br/>";
            }
            if($active_flag['email_confirm_flag'] != "1"){ $i++;
                $setup_process_missing .= "<span style='color: red;'>".$i.". E-Mail confirmation incomplete</span><br/><br/>";
            }
            if($active_flag['accept_terms_condition_flag'] != "1"){ $i++;
                $setup_process_missing .= "<span style='color: red;'>".$i.". Terms & Conditions incomplete</span><br/><br/>";
            }

            $data = array(
                "full_name" => $account->user_name,
                "site_url" => APContext::getFullBalancerPath(),
                "setup_process_missing" => $setup_process_missing
            );
            $date_diff = $curr_date - $account->deactivated_date;

            // send mail notification.
            if ($date_diff >= 8 * $one_day && $date_diff < 9 * $one_day) {
                $this->sendEmailRemind(APConstants::account_is_auto_deactivated_after_eight_days, $account->email, $data);
            } else if ($date_diff >= 30 * $one_day && $date_diff < 31 * $one_day) {
                $this->sendEmailRemind(APConstants::account_is_auto_deactivated_after_thirty_days, $account->email, $data);
            } else if ($date_diff >= 60 * $one_day && $date_diff < 61 * $one_day) {
                $this->sendEmailRemind(APConstants::account_is_auto_deactivated_after_sixty_days, $account->email, $data);
            } else if ($date_diff >= 70 * $one_day) {

                //ignore the customers auto by not yet accept new term and condition
                if($active_flag['shipping_address_completed'] == 1
                        && $active_flag['invoicing_address_completed'] == 1
                        && $active_flag['postbox_name_flag'] == 1
                        && $active_flag['name_comp_address_flag'] == 1
                        && $active_flag['city_address_flag'] == 1
                        && $active_flag['payment_detail_flag'] == 1
                        && $active_flag['accept_terms_condition_flag'] == 0
                        && $active_flag['email_confirm_flag'] == 1){
                    continue;
                }

                // Gets current balance
                $balance = CustomerUtils::getAdjustOpenBalanceDue($customerId);
                $totalBalance = $balance["OpenBalanceDue"] + $balance['OpenBalanceThisMonth'];
                $textBalance = ' OpenBalance: ' . $balance["OpenBalanceDue"] . ' ; ' . $balance['OpenBalanceThisMonth'];
                array_push($affectedList, $account->customer_code . $textBalance);
                $created_by_id = APConstants::CUSTOMER_HISTORY_CREATED_BY_SYSTEM;

                // Delete logic: if customer has open balance -> insert into blacklist.
                if ($totalBalance > 0) {
                     /*
                     * #1180 create postbox history page like check item page
                     *   Activity: APConstants::POSTBOX_DELETE_ORDER_BY_SYSTEM
                     */
                    CustomerUtils::deleteCustomer($customerId, true, true, null, null, null, $created_by_id);
                } else {
                     /*
                     * #1180 create postbox history page like check item page
                     *   Activity: APConstants::POSTBOX_DELETE_ORDER_BY_SYSTEM
                     */
                    CustomerUtils::deleteCustomer($customerId, true, false, null, null, null, $created_by_id);
                }
                // Send mail
                $this->sendEmailRemind(APConstants::account_has_been_deleted, $account->email, $data);
            }
        }

        return $affectedList;
    }

    /**
     * send email
     *
     * @param unknown $template_email_slug
     * @param unknown $to_email
     * @param unknown $data
     */
    private function sendEmailRemind($template_email_slug, $to_email, $data) {
        $data['slug'] = $template_email_slug;
        $data['to_email'] = $to_email;
        // Send email
        MailUtils::sendEmailByTemplate($data);
        return false;
    }

    /**
     * send email invoices monthly report of customer.
     *
     * @param unknown $customer
     * @param unknown $file_export
     */
    private function send_email_invoices_monthly_report($customer, $file_export, $invoice_code) {
        $data = array(
            "slug" => APConstants::email_invoices_report_by_monthly,
            "to_email" => $customer->email,
            // Replace content
            "full_name" => $customer->user_name,
            'invoice_id' => $invoice_code,
            'attachments' => array(
                'file' => $file_export
            )
        );

        // Send email
        MailUtils::sendEmailByTemplate($data);


        return true;
    }

    /**
     * Using for addhoc only
     */
    private function update_vat_rate() {
        $key = $this->input->get_post('key');
        if ($key === '5eb96ffccee348403fbf2cd4a0addca0') {
            $list_invoice_summary = $this->invoice_summary_m->get_many_by_many(
                    array(
                        "invoice_type" => '1',
                        "substr( invoice_month, 1, 6 ) = '" . $this->curYear . $this->curMonth . "'" => null
            ));
            foreach ($list_invoice_summary as $invoice_summary) {
                $this->invoice_summary_m->update_by_many(
                        array(
                    'customer_id' => $invoice_summary->customer_id,
                    'id' => $invoice_summary->id,
                    "invoice_type" => '1',
                    "substr( invoice_month, 1, 6 ) = '" . $this->curYear . $this->curMonth . "'" => null
                        ), array(
                    'vat' => CustomerUtils::getVatRateOfCustomer($invoice_summary->customer_id)->vat
                ));
                echo 'Update vat of customer.' . $invoice_summary->customer_id;
            }
        }
    }

    /**
     * Run addhoc only
     * DO NOT USE THIS METHOD
     */
    private function update_invoice_payment_method() {
        // Get all customer
        $customers = $this->customer_m->get_customer_paging(
                array(
            "shipping_address_completed" => APConstants::ON_FLAG,
            "invoicing_address_completed" => APConstants::ON_FLAG,
            "postbox_name_flag" => APConstants::ON_FLAG,
            "name_comp_address_flag" => APConstants::ON_FLAG,
            "city_address_flag" => APConstants::ON_FLAG,
            "payment_detail_flag" => APConstants::OFF_FLAG,
            "email_confirm_flag" => APConstants::ON_FLAG,
            "activated_flag" => APConstants::OFF_FLAG
                ), 0, 100000, '');

        foreach ($customers['data'] as $customer) {
            $customer_id = $customer->customer_id;
            // Check invoice code
            $invoice_code = substr(md5($customer_id), 0, 6) . APUtils::generateRandom(4);

            // Update payment flag
            $this->customer_m->update_by_many(
                    array(
                "customer_id" => $customer_id,
                "shipping_address_completed" => APConstants::ON_FLAG,
                "invoicing_address_completed" => APConstants::ON_FLAG,
                "postbox_name_flag" => APConstants::ON_FLAG,
                "name_comp_address_flag" => APConstants::ON_FLAG,
                "city_address_flag" => APConstants::ON_FLAG,
                "payment_detail_flag" => APConstants::OFF_FLAG,
                "email_confirm_flag" => APConstants::ON_FLAG,
                "accept_terms_condition_flag" => APConstants::ON_FLAG
                    ), array(
                "invoice_type" => '2',
                "payment_detail_flag" => APConstants::ON_FLAG,
                "request_confirm_flag" => APConstants::ON_FLAG,
                "request_confirm_date" => now(),
                "last_updated_date" => now(),
                "invoice_code" => $invoice_code
            ));

            $open_balance_data = CustomerUtils::getAdjustOpenBalanceDue($customer_id);
            $open_balance = $open_balance_data['OpenBalanceDue'];
            if ($open_balance <= 0.1) {
                // we dont have to tell the Customer that a valid credit Card with non working payment can reactivate the account.
                // in most cases the Customer will Chose a Card that can handle the payment if it is valid
                // Only reactivate if deactivated_type = auto
                customers_api::reactivateCustomerWhenPaymentSuccess($customer_id);
            }

            echo 'Activated customer id:' . $customer_id . '<br/>';
        }
    }

    /**
     * Get all customer empty first location
     */
    private function get_all_customer_empty_first_location() {
        // Get all customer
        $customers = $this->customer_m->get_customer_paging(array(), 0, 100000, '');

        foreach ($customers['data'] as $customer) {
            $customer_id = $customer->customer_id;
            $primary_location = APUtils::getPrimaryLocationBy($customer_id);
            if ($primary_location == 0) {
                echo 'Customer ID: ' . $customer_id . '<br/>';
            }
        }
        echo "OK";
    }

    /**
     * For test only
     */
    private function checkVAT() {
        $this->load->library('CheckVAT');
        $UstId_1 = Settings::get(APConstants::INSTANCE_OWNER_VAT_NUM_CODE);
        $UstId_2 = 'SE556983504301';
        $CompanyName = 'Kaitoland Group AB';
        $Location = 'DALVÄGEN 8';
        $ZipCode = '16956';
        $StreetAddress = 'Solna';

        $result = CheckVAT::validate($UstId_1, $UstId_2, $CompanyName, $Location, $ZipCode, $StreetAddress);
        if ($result) {
            echo "XXX";
        }
        // $vat_number = 'LU26375245';
        // CheckVAT::validateVATEU($vat_number);
    }

    private function set_default_postbox_setting() {
        $this->load->model('mailbox/postbox_setting_m');
        $this->load->model('mailbox/postbox_m');

        $key = $this->input->get_post('key');
        if ($key === 'f54d83d0f272327742f9ab305c89dd5f') {
            $postboxes = $this->postbox_m->get_many_by_many(array(
                "deleted <> 1" => null
            ));

            foreach ($postboxes as $p) {
                $check = $this->postbox_setting_m->get_by_many(
                        array(
                            "postbox_id" => $p->postbox_id,
                            "customer_id" => $p->customer_id
                ));
                if (!$check) {
                    $data = array(
                        "customer_id" => $p->customer_id,
                        "postbox_id" => $p->postbox_id,
                        "email_notification" => 1,
                        "invoicing_cycle" => 1,
                        "weekday_shipping" => 2,
                        "collect_mail_cycle" => 2
                    );
                    $this->postbox_setting_m->insert($data);
                }
            }
            echo "OK";
        }
    }

    /**
     * E-Mail notification for incoming item: daily
     */
    private function notify_incomming_item_daily() {
        // Gets all customers that has setting incomming notification ( daily)
        $customers = $this->customer_m->get_customer_has_daily_incomming_notification();

        if (count($customers)) {
            $i = 0;
            foreach ($customers as $customer) {
                $customer_id = $customer->customer_id;
                if (!empty($customer->invoicing_address_name)) {
                    $customer_name = $customer->invoicing_address_name;
                } else if (!empty($customer->invoicing_company)) {
                    $customer_name = $customer->invoicing_company;
                } else {
                    $customer_name = $customer->postbox_name;
                }
                // count all envelopes of this customer
                $total_envelopes = $this->envelope_m->count_by_many(
                        array(
                            'to_customer_id' => $customer_id,
                            'email_notification_flag' => APConstants::OFF_FLAG
                ));

                $customers[$i]->total_envelopes = $total_envelopes;

                $data = array(
                    "slug" => APConstants::new_incomming_notification_daily,
                    "to_email" => $customer->email,
                    // Replace content
                    "full_name" => $customer_name,
                    "site_url" => APContext::getFullBalancerPath(),
                    "total" => $total_envelopes
                );


                try {
                    if (MailUtils::sendEmailByTemplate($data)) {
                        // remarked notified
                        $this->envelope_m->update_by_many(
                                array(
                            'to_customer_id' => $customer_id,
                            'email_notification_flag' => APConstants::OFF_FLAG
                                ), array(
                            'email_notification_flag' => APConstants::ON_FLAG
                        ));
                    }
                } catch (Exception $e) {
                    log_message($e);
                }

                $i++;
            }
        }
        return $customers;
    }

    /**
     * E-Mail notification for incoming item: weekly
     */
    private function notify_incomming_item_weekly() {
        // Gets all customers that has setting incomming notification ( weekly)
        $customers = $this->customer_m->get_customer_has_weekly_incomming_notification();
        if (count($customers)) {
            $i = 0;
            foreach ($customers as $customer) {
                $customer_id = $customer->customer_id;
                if (!empty($customer->invoicing_address_name)) {
                    $customer_name = $customer->invoicing_address_name;
                } else if (!empty($customer->invoicing_company)) {
                    $customer_name = $customer->invoicing_company;
                } else {
                    $customer_name = $customer->postbox_name;
                }
                // count all envelopes of this customer
                $total_envelopes = $this->envelope_m->count_by_many(
                        array(
                            'to_customer_id' => $customer_id,
                            'email_notification_flag' => APConstants::OFF_FLAG
                ));
                $customers[$i]->total_envelopes = $total_envelopes;

                $data = array(
                    "slug" => APConstants::new_incomming_notification_weekly,
                    "to_email" => $customer->email,
                    // Replace content
                    "full_name" => $customer_name,
                    "site_url" => APContext::getFullBalancerPath(),
                    "total" => $total_envelopes
                );

                try {
                    if (MailUtils::sendEmailByTemplate($data)) {
                        // remarked notified
                        $this->envelope_m->update_by_many(
                                array(
                            'to_customer_id' => $customer_id,
                            'email_notification_flag' => APConstants::OFF_FLAG
                                ), array(
                            'email_notification_flag' => APConstants::ON_FLAG
                        ));
                    }
                } catch (Exception $e) {
                    log_message($e);
                }

                $i++;
            }
        }
        return $customers;
    }

    /**
     * E-Mail notification for incoming item: monthly
     */
    private function notify_incomming_item_monthly() {
        // Gets all customers that has setting incomming notification ( monthly)
        $customers = $this->customer_m->get_customer_has_monthly_incomming_notification();

        if (count($customers)) {
            $i = 0;
            foreach ($customers as $customer) {
                $customer_id = $customer->customer_id;
                if (!empty($customer->invoicing_address_name)) {
                    $customer_name = $customer->invoicing_address_name;
                } else if (!empty($customer->invoicing_company)) {
                    $customer_name = $customer->invoicing_company;
                } else {
                    $customer_name = $customer->postbox_name;
                }
                // count all envelopes of this customer
                $total_envelopes = $this->envelope_m->count_by_many(
                        array(
                            'to_customer_id' => $customer_id,
                            'email_notification_flag' => APConstants::OFF_FLAG
                ));
                $data = array(
                    "slug" => APConstants::new_incomming_notification_monthly,
                    "to_email" => $customer->email,
                    // Replace content
                    "full_name" => $customer_name,
                    "site_url" => APContext::getFullBalancerPath(),
                    "total" => $total_envelopes
                );
                $customers[$i]->total_envelopes = $total_envelopes;
                try {
                    if (MailUtils::sendEmailByTemplate($data)) {
                        // remarked notified
                        $this->envelope_m->update_by_many(
                                array(
                            'to_customer_id' => $customer_id,
                            'email_notification_flag' => APConstants::OFF_FLAG
                                ), array(
                            'email_notification_flag' => APConstants::ON_FLAG
                        ));
                    }
                } catch (Exception $e) {
                    log_message($e);
                }

                $i++;
            }
        }
        return $customers;
    }

    /**
     * Send notification email to admin after run a cron job
     */
    private function send_notification($jobName, $execState = true, $notificationMsg = '') {

        if (empty($notificationMsg)) {
            $notificationMsg = '';
        }

        $environment = (ENVIRONMENT == 'production') ? 'LIV' : 'DEV';
        $domainName = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
        $currentTime = date('Y-m-d H:i:s');
        $state = ($execState) ? 'SUCCESS' : 'FAILED';
        $mailTitle = sprintf("[%s][%s][%s]: Exec cronjob '%s' - %s", $environment, $domainName, $currentTime, $jobName, $state);
        if ($execState) {
            if (!empty($notificationMsg)) {
                $mailContent = $notificationMsg;
            } else {
                $mailContent = sprintf("[%s][%s][%s]: The cronjob '%s' has been executed successfully!", $environment, $domainName, $currentTime, $jobName);
            }
        } else {
            if (!empty($notificationMsg)) {
                $mailContent = $notificationMsg;
            } else {
                $mailContent = sprintf("[%s][%s][%s]: The cronjob '%s' has been failed!", $environment, $domainName, $currentTime, $jobName);
            }
        }
        MailUtils::sendEmail('', $this->notifiedEmailList, $mailTitle, $mailContent);
    }

    private function checkCronjobKey($jobName) {
        $key = $this->input->get_post('key');

        // Get the value of constant
        $constant = 'CRON_KEY_' . strtolower($jobName);
        $ref = new ReflectionClass('CronConfigs');
        $validKey = $ref->getConstant($constant);

        if ($key != $validKey) {
            $this->send_notification($jobName, false, CronConfigs::EXEC_CRONJOB_INVALID_REQUEST);
            echo CronConfigs::EXEC_CRONJOB_INVALID_REQUEST;
            exit();
        }
    }

    // Check if today already run this job
    private function checkCronjobStatus($jobName, $hour = 0, $minute = 0, $second = 0) {
        if (CronUtils::isJobExecuted($jobName, $this->curYear, $this->curMonth, $this->curDate, $hour, $minute, $second)) {
            $this->send_notification($jobName, false, CronConfigs::EXEC_CRONJOB_EXECUTED_ALREADY);
            echo CronConfigs::EXEC_CRONJOB_EXECUTED_ALREADY;
            exit();
        }
    }

    private function initCurrentTime() {
        $this->curYear = intval(date("Y"));
        $this->curMonth = intval(date("m"));
        $this->curDate = intval(date("d"));
        $this->curHour = intval(date("H"));
        $this->curMinute = intval(date("i"));
        $this->curSecond = intval(date("s"));
    }

    /*
     * Des: notify email to location admin about number of activity on TODO pages
     * Ticket: #1043
     */

    public function notify_todo_number_to_location_admin() {

        $sent_daily_reminder_flag = APConstants::ON_FLAG;
        $list_locations = addresses_api::getLocationPublic($sent_daily_reminder_flag);
        $jobName = "notify_todo_number_to_location_admin";
        $this->checkCronjobKey($jobName);

        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);
        $startTime = date('Y-m-d H:i:s');
        $notificationMessage = '';
        $parse_message = array();
        $i = 0;
        if (!empty($list_locations)) {
            $arr_data = array();
            foreach ($list_locations as $location) {

                $list_filter_location_id [] = $location->id;
                $input_paging = $this->get_paging_input();
                $input_paging['sort_column'] = "id";
                $input_paging['sort_type'] = "DESC";

                $array_condition = array();
                $array_condition ['envelopes.location_id IN ' . "(" . $location->id . ")"] = null;
                $array_condition ["(customers.status <> '1' OR customers.status IS NULL)"] = null;

                $result = scans_api::getEnvelopePagingInTodoList($array_condition, $input_paging ['start'], $input_paging ['limit'], $input_paging ['sort_column'], $input_paging ['sort_type']);

                $arr_data[$location->id] = $result['total'];
            } //foreach ($list_locations as $location)

            $list_users = $this->user_m->get_all_location_admin_users_by($list_filter_location_id);
            if (!empty($list_users)) {

                $parse_message = array();
                $i = 0;
                foreach ($list_users as $user) {

                    $user_location = $this->user_m->get_user_location($user->user_id, $list_filter_location_id);

                    $html_content = ci()->load->view("notify_location_admin_content", array('user_location' => $user_location, 'arr_data' => $arr_data), true);

                    if (!empty($user->email)) {

                        $data = array(
                            "slug" => APConstants::notify_email_to_location_admin,
                            "to_email" => $user->email,
                            // Replace content
                            "full_name" => $user->display_name,
                            "str_content" => $html_content
                        );

                        // Send email
                        MailUtils::sendEmailByTemplate($data);

                        $message = new stdClass();
                        $message->email = $user->email;
                        $message->display_name = $user->display_name;
                        $message->str_content = $html_content;
                        $message->user_location = $user_location;
                        $message->arr_data = $arr_data;

                        $parse_message[$i] = $message;
                        $i++;
                    }
                } // End foreach ($list_users as $user)

                if (!empty($parse_message)) {
                    $notificationMessage = CronUtils::buildHtmlTableOfNotifyLocationAdminOpenActivity($parse_message);
                }
            } else {
                $notificationMessage = "There is no users are 'location admin' have location setting 'daily reminder' on systems";
            }
        } else {

            $notificationMessage = "There is no location be setting or no email template exist on systems";
        }

        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        $this->send_notification($jobName, true, $notificationMessage);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /*
     * Des: notify email to location admin, instance owner, Super admin about new and deleted customer
     * at location that user management
     * @author: Hung
     */

    public function notify_email_new_and_delete_customers() {

        $jobName = "notify_email_new_and_delete_customers";
        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        $startTime = date('Y-m-d H:i:s');

        $notificationMessage = "<p> This is list of email had sent when run cron job notify_email_new_and_delete_customers</p>";

        $list_user = $this->user_m->get_user_sent_notification_customer();
        if (count($list_user)) {

            foreach ($list_user as $user) {

                $location_users = $this->location_users_m->get_location_users_available($user->user_id);
                $html_content = ci()->load->view("notify_email_new_and_delete_customers_content", array(
                    'location_users' => $location_users
                        ), true);

                if (!empty($user->info_email)) {

                    $to_email = $user->info_email;
                } else {

                    $to_email = $user->email;
                }
                $data = array(
                    "slug" => APConstants::notify_email_new_and_delete_customers,
                    "to_email" => $to_email,
                    // Replace content
                    "full_name" => $user->display_name,
                    "html_content" => $html_content
                );
                // Send email
                MailUtils::sendEmailByTemplate($data);
                $notificationMessage .= $to_email . "<br/>";
            }
        } else {
            $notificationMessage = "There is no users be setting to get notification email about new and delete customers or not exist email template in systems";
        }

        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);
        //echo $content. "<hr/>"; echo $notificationMessage; exit;
        $this->send_notification($jobName, true, $notificationMessage);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /*
     * Des: Run if customer enable auto-trash function on setting postbox
     * @author: Hung
     */

    public function auto_trash() {

        $jobName = "auto_trash";
        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);
        $startTime = date('Y-m-d H:i:s');
        $notificationMessage = "";

        $curr_date = now();
        $one_day = 86400;

        $list_customer = $this->customer_m->get_customer_setting_auto_trash();
        $data_process = array();
        $i = 0;
        if (count($list_customer)) {
            foreach ($list_customer as $customer) {
                $list_envelope_ids = array();

                $list_envelopes = $this->envelope_m->get_many_by_many(array(
                    "postbox_id" => $customer->postbox_id,
                    "to_customer_id" => $customer->customer_id,
                    "item_scan_flag" => null,
                    "envelope_scan_flag" => null,
                    "direct_shipping_flag" => null,
                    "collect_shipping_flag" => null,
                    "trash_flag" => null,
                    "incomming_date <= " => ($curr_date - ($one_day * ($customer->trash_after_day) )),
                    "incomming_date > " => ($curr_date - ($one_day * ($customer->trash_after_day + 1) ))
                ));

                if (count($list_envelopes)) {
                    foreach ($list_envelopes as $envelope) {

                        $envelope_customs = ci()->envelope_customs_m->get_by("envelope_id", $envelope->id);
                        if (!empty($envelope_customs)) {
                            continue;
                        }
                        $list_envelope_ids[] = $envelope->id;
                        $data_process[$i]['envelope_id'] = $envelope->id;
                        $data_process[$i]['envelope_code'] = $envelope->envelope_code;
                        $data_process[$i]['postbox_id'] = $envelope->postbox_id;
                        $data_process[$i]['customer_id'] = $customer->customer_id;
                        $i++;
                    }
                    EnvelopeUtils::auto_trash($list_envelope_ids, $customer);
                }
            }
            if (count($data_process)) {
                $notificationMessage = ci()->load->view("tracking_auto_trash", array('data_process' => $data_process), true);
            }
        } else {
            $notificationMessage = "There is no envelopes be auto trash";
        }
        //echo $notificationMessage;exit;

        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);
        //echo $content. "<hr/>"; echo $notificationMessage; exit;
        $this->send_notification($jobName, true, $notificationMessage);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * #1161 create report cron job
     * 1. accounts deactivated because of failed setup process ( not activated account)
     * 2. accounts deactivated due to failed payment
     * 3. accounts manually deactivated
     * 4. accounts (older 3 month) deleted automatically
     * 5. accounts (younger 3 month) deleted automatically
     * 6. accounts deleted manually
     */
    public function send_general_accounts_reporting_job() {
        $jobName = 'send_general_accounts_reporting_job';

        $this->checkCronjobKey($jobName);

        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        echo "Start to create report" . '<br/>';

        // 1. accounts deactivated because of failed setup process ( not activated account)
        $condition_arr_accounts_deactivated = array(
            "customers.activated_flag" => APConstants::OFF_FLAG,
            "customers.status" => APConstants::OFF_FLAG,
            "customers.deactivated_type" => APConstants::AUTO_INACTIVE_TYPE,
            "customers.payment_detail_flag" => APConstants::ON_FLAG,
            "(customers.shipping_address_completed = '0' OR customers.invoicing_address_completed = '0' "
            . "OR customers.postbox_name_flag = '0' OR customers.name_comp_address_flag = '0' "
            . "OR customers.city_address_flag = '0' OR customers.email_confirm_flag = '0')" => NULL
        );

        $total_accounts_deactivated = $this->customer_m->count_accounts_deactivated_by_failed_setup_process($condition_arr_accounts_deactivated);
        $data_accounts_deactivated = $this->customer_m->get_many_by_many($condition_arr_accounts_deactivated);

        // Display total accounts
        echo 'Total accounts deactivated because of failed setup process ( not activated account): ' . $total_accounts_deactivated . '<br/>';
        echo 'Cutomer ID: ';
        foreach ($data_accounts_deactivated as $value) {
            echo $value->customer_id . ', ';
        }
        echo '<br/>';
        log_audit_message('error', 'Total accounts deactivated because of failed setup process ( not activated account): ' . $total_accounts_deactivated . '<br/>', false, 'accounts-deactivated-failed-setup-process');

        // 2. accounts deactivated due to failed payment
        $condition_arr_accounts_deactivated_failed_payment = array(
            "customers.activated_flag" => APConstants::OFF_FLAG,
            "customers.status" => APConstants::OFF_FLAG,
            "customers.deactivated_type" => APConstants::AUTO_INACTIVE_TYPE,
            "customers.payment_detail_flag" => APConstants::OFF_FLAG
        );

        $total_accounts_deactivated_failed_payment = $this->customer_m->count_accounts_deactivated_by_failed_payment($condition_arr_accounts_deactivated_failed_payment);
        $data_accounts_deactivated_failed_payment = $this->customer_m->get_many_by_many($condition_arr_accounts_deactivated_failed_payment);

        // Display total accounts
        echo 'Total accounts deactivated due to failed payment: ' . $total_accounts_deactivated_failed_payment . '<br/> ';
        echo 'Cutomer ID: ';
        foreach ($data_accounts_deactivated_failed_payment as $value) {
            echo $value->customer_id . ', ';
        };
        echo '<br/>';
        log_audit_message('error', 'Total  accounts deactivated due to failed payment: ' . $total_accounts_deactivated_failed_payment . '<br/>', false, 'accounts-deactivated-failed-payment');

        // 3. accounts manually deactivated
        $condition_arr_accounts_manually_deactivated = array(
            "customers.activated_flag" => APConstants::OFF_FLAG,
            "(customers.status=0 OR customers.status IS NULL)" => NULL,
            "customers.deactivated_type" => APConstants::MANUAL_INACTIVE_TYPE,
            "customers.email_confirm_flag" => APConstants::ON_FLAG
        );

        $total_accounts_manually_deactivated = $this->customer_m->count_accounts_manually_deactivated($condition_arr_accounts_manually_deactivated);
        $data_accounts_manually_deactivate = $this->customer_m->get_many_by_many($condition_arr_accounts_manually_deactivated);
        // Display total accounts
        echo 'Total accounts manually deactivated: ' . $total_accounts_manually_deactivated . '<br/>';
        echo 'Cutomer ID: ';
        foreach ($data_accounts_manually_deactivate as $value) {
            echo $value->customer_id . ', ';
        };
        echo '<br/>';
        log_audit_message('error', 'Total accounts manually deactivated: ' . $total_accounts_manually_deactivated . '<br/>', false, 'accounts-manually-deactivated');

        // 4. accounts (older 3 month) deleted automatically
        $condition_arr_accounts_older_3_month_deleted = array(
            "customers.status" => APConstants::ON_FLAG,
            "((customers.deleted_by = 0) OR (customers.deleted_by IS NULL) )" => NULL,
            "(( FROM_UNIXTIME(customers.deleted_date) < DATE_SUB(NOW(),INTERVAL 90 DAY) ) OR (customers.deleted_date IS NULL))" => NULL
        );

        $total_accounts_older_3_month_deleted = $this->customer_m->count_accounts_deleted_automatically_older_three_month($condition_arr_accounts_older_3_month_deleted);

        // Display total accounts
        echo 'Total accounts (older 3 month) deleted automatically: ' . $total_accounts_older_3_month_deleted . '<br/>';
        log_audit_message('error', 'Total accounts (older 3 month) deleted automatically: ' . $total_accounts_older_3_month_deleted . '<br/>', false, 'accounts-accounts-older-3-month-deleted');

        // 5.accounts (younger 3 month) deleted automatically
        $condition_arr_accounts_younger_3_month_deleted = array(
            "customers.status" => APConstants::ON_FLAG,
            "((customers.deleted_by = 0) OR (customers.deleted_by IS NULL) )" => NULL,
            "FROM_UNIXTIME(customers.deleted_date) BETWEEN DATE_SUB(NOW(),INTERVAL 90 DAY) AND NOW()" => NULL
        );

        $total_accounts_younger_3_month_deleted = $this->customer_m->count_accounts_deleted_automatically_younger_three_month($condition_arr_accounts_younger_3_month_deleted);

        // Display total accounts
        echo 'Total accounts (younger 3 month) deleted automatically: ' . $total_accounts_younger_3_month_deleted . '<br/>';
        log_audit_message('error', 'Total accounts (younger 3 month) deleted automatically: ' . $total_accounts_younger_3_month_deleted . '<br/>', false, 'accounts-accounts-older-3-month-deleted');

        // 6.accounts deleted manually
        $condition_arr_deleted_manually = array(
            "customers.status" => APConstants::ON_FLAG,
            "((customers.deleted_by <> 0) OR (customers.deleted_by = customers.customer_id))" => NULL
        );

        $total_accounts_deleted_manually = $this->customer_m->count_accounts_deleted_manually($condition_arr_deleted_manually);
        $data_accounts_deleted_manually = $this->customer_m->get_many_by_many($condition_arr_deleted_manually);
        // Display total accounts
        echo 'Total accounts deleted manually: ' . $total_accounts_deleted_manually . '<br/>';
        echo 'Cutomer ID: ';
        foreach ($data_accounts_deleted_manually as $value) {
            echo $value->customer_id . ', ';
        };
        log_audit_message('error', 'Total accounts deleted manually: ' . $total_accounts_deleted_manually . '<br/>', false, 'accounts-deleted-manually');

        // Send report to email
        $notificationMessage = "<p> This is email had sent when run cron job notify_email_new_and_delete_customers</p>";
        $list_email = explode(",", $this->notifiedEmailList);

        if (!empty($list_email)) {

            $html_content = ci()->load->view("notify_email_report_deactive_and_delete_accounts_content", array(
                'total_accounts_deactivated' => $total_accounts_deactivated,
                'total_accounts_deactivated_failed_payment' => $total_accounts_deactivated_failed_payment,
                'total_accounts_manually_deactivated' => $total_accounts_manually_deactivated,
                'total_accounts_older_3_month_deleted' => $total_accounts_older_3_month_deleted,
                'total_accounts_younger_3_month_deleted' => $total_accounts_younger_3_month_deleted,
                'total_accounts_deleted_manually' => $total_accounts_deleted_manually,
                'data_accounts_deactivated' => $data_accounts_deactivated,
                'data_accounts_deactivated_failed_payment' => $data_accounts_deactivated_failed_payment,
                'data_accounts_manually_deactivate' => $data_accounts_manually_deactivate,
                'data_accounts_deleted_manually' => $data_accounts_deleted_manually
                    ), true);

            foreach ($list_email as $email) {
                $data = array(
                    "slug" => APConstants::notify_email_report_deactive_and_delete_accounts,
                    "to_email" => $email,
                    // Replace content
                    "email" => $email,
                    "html_content" => $html_content
                );

                // Send email
                MailUtils::sendEmailByTemplate($data);
                $notificationMessage .= $email . "<br/>";
            }
        } else {
            $notificationMessage = "There is no accounts be setting to get notification email about new and delete customers or not exist email template in systems";
        }

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // Notify
        $this->send_notification($jobName, true, $notificationMessage);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Check mail queues and send email on reserved datetime
     * Combine all email sent to the same address, same content, same day to 1 email
     */
    public function send_email_job() {

        $this->load->model('email/email_queue_m');

        $jobName = "send_email_job";
        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        //Start job time
        $startTime = date('Y-m-d H:i:s');
        //Count sent mail
        $total_email = 0;
        $total_email_combine = 0;
        $sent_email_success = 0;
        //Set email send_date range
        $start_send_time = strtotime('yesterday');
        $end_send_time = strtotime('+ 24 hours', strtotime('yesterday'));
        $where = array(
            'send_date >=' => $start_send_time,
            'send_date <=' => $end_send_time,
            'status' => 0 //Email waiting to send
        );
        //Get CONSTRAINT (email,slug) in mail queues need to send
        $constraints = ci()->email_queue_m->get_many_by_many($where, 'from_email, to_email, slug', true);
        $total_email_combine = count($constraints);
        if (!empty($constraints)) {
            foreach ($constraints as $constraint) {

                //Set sendmail info
                $from_email = $constraint->from_email;
                $to_email = $constraint->to_email;
                $slug = $constraint->slug;
                //Get all email of this constraint
                $where = array(
                    'send_date >=' => $start_send_time,
                    'send_date <=' => $end_send_time,
                    'status' => 0, //Email waiting to send,
                    'to_email' => $to_email,
                    'slug' => $slug
                );
                if (!empty($from_email)) {
                    $where['from_email'] = $from_email;
                }
                $emails = ci()->email_queue_m->get_many_by_many($where);
                $total_email += count($emails);
                //Process to combine content for a constraint(email, slug)
                $data = array(
                    'from_email' => $from_email,
                    'to_email' => $to_email,
                    'slug' => $slug,
                );
                $content = array();
                //Process combine email for each email template (slug)
                switch ($slug) {
                    //Process for accounting email
                    case APConstants::accounting_invoice_email:

                        $attachments = array();
                        $full_name = CustomerUtils::getCustomerByID(APUtils::get_json_by_key($emails[0]->data, 'customer_id'))->user_name;
                        $item_codes = array();
                        $item_ids = array();
                        $email_queues = array();

                        foreach ($emails as $email) {

                            if (!empty($email->attachments)) {
                                $attachments = array_merge($attachments, json_decode($email->attachments, true));
                            }

                            $item_id = APUtils::get_json_by_key($email->data, 'item_id');

                            if (!empty($item_id)) {
                                $email_queues[] = $email->id;
                                $item_ids[] = $item_id;
                                $item_codes[] = $this->envelope_m->get_by_many(array('id' => $item_id))->envelope_code;
                            }
                        }

                        $content = array(
                            'attachments' => $attachments,
                            'full_name' => $full_name,
                            'items' => implode(', ', $item_codes)
                        );

                        //Send email
                        if (MailUtils::sendEmailByTemplate(array_merge($data, $content))) {
                            $sent_email_success++;
                            $this->envelope_m->update_many($item_ids, array('invoice_date' => now(), 'invoice_flag' => APConstants::ON_FLAG));
                            $this->email_queue_m->update_many($email_queues, array('status' => 1));
                        }

                        break;
                    //Process for delete item email
                    case APConstants::email_is_notified_envelope_is_direct_deleted:

                        $full_name = APUtils::get_json_by_key(json_decode($emails[0]->data), 'full_name');
                        $item_codes = array();
                        $item_ids = array();
                        $email_queues = array();

                        foreach ($emails as $email) {

                            $item_id = APUtils::get_json_by_key(json_decode($email->data), 'item_id');

                            if (!empty($item_id)) {
                                $email_queues[] = $email->id;
                                $item_ids[] = $item_id;
                                $item_codes[] = APUtils::get_json_by_key(json_decode($email->data), 'item_code');
                            }
                        }

                        $content = array(
                            'full_name' => $full_name,
                            'items' => implode('<br>', $item_codes)
                        );

                        //Send email
                        if (MailUtils::sendEmailByTemplate(array_merge($data, $content))) {
                            $sent_email_success++;
                            $this->envelope_m->update_many($item_ids, array('email_notification_flag' => APConstants::ON_FLAG));
                            $this->email_queue_m->update_many($email_queues, array('status' => 1));
                        }


                        break;

                    default:
                }
            }
        }

        //End job time
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // E-mail notification
        if ($total_email) {
            $notificationMsg = "There are totally {$total_email} need to send, combine to {$total_email_combine} and already sent {$sent_email_success} successfully.";
        } else {
            $notificationMsg = 'There is no email need to send in this time.';
        }
        $this->send_notification($jobName, true, $notificationMsg);

        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * update location report data.
     */
    public function update_location_report() {
        $this->load->library("invoices/invoices_api");

        $jobName = "update_location_report";
        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);
        $startTime = date('Y-m-d H:i:s');
        $notificationMessage = "";

        if (APUtils::isFirstDayOfMonth()) {
            // update location report of last month.
            $report_month = date("Ym", strtotime('last month'));
            invoices_api::updateInvoiceSummaryTotalByLocation($report_month, '', true);
        }

        // update location report in this month.
        $report_month = $this->input->get('ym', '');
        if (empty($report_month)) {
            $report_month = APUtils::getCurrentYearMonth();
        }
        invoices_api::updateInvoiceSummaryTotalByLocation($report_month, '', true);

        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        $this->send_notification($jobName, true, $notificationMessage);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /*
     * Des: If admin worker update terms and condition and check checkbox "Needs customer approval", this cron job will send emaill
     * to the customer not yet delete, and not yet accept new terms and condition
     */

    public function send_notify_new_terms_condition() {
        $jobName = "send_notify_new_terms_condition";
        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);
        $startTime = date('Y-m-d H:i:s');

        // notify term & condition to customers.
        $notificationMessage = cron_api::notify_term_and_condition();

        $this->send_notification($jobName, true, $notificationMessage);
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /*
     * Des: This cron job check if after effective date but customer not yet accept new term and condition, Customer will be auto-deactivated
     *
     */
    public function check_customer_accept_new_terms_condition() {
        $jobName = "check_customer_accept_new_terms_condition";
        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);
        $startTime = date('Y-m-d H:i:s');

        // deactivate customer.
        $notificationMessage = cron_api::deactive_customer_not_accept_new_terms_condition();

        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);
        $this->send_notification($jobName, true, $notificationMessage);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * sync call history from sontel of all account.
     * @return boolean
     */
    public function sync_callhistory_sontel(){
        // Load model
        $this->load->model('phones/phone_customer_subaccount_m');
        $this->load->library('phones/phones_api');

        $jobName = 'sync_callhistory_sontel';

        $this->checkCronjobKey($jobName);

        // Get all postbox need delete today (yyyyMMdd)
        $today = DateTimeUtils::getCurrentYearMonthDate();
        if ($today != DateTimeUtils::getFirstDayOfCurrentMonth()) {
            echo CronConfigs::EXEC_CRONJOB_PROCESS_AUTO_STOPPED;
            return false;
        }

        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        // Only process if this is first day of month
        $customers = $this->phone_customer_subaccount_m->get_all();
        $message = "<h3>List data synchronized:</h3><br/>";
        foreach($customers as $customer){
            $account_id = $customer->account_id;
            $customer_id= $customer->customer_id;
            try{
                $total_record = phones_api::getCallHistoryFromSontel($customer_id, $account_id);
                $message .= "<div>Customer_id: ".$customer_id.", total record sync: ".$total_record."</div>";
            }catch(ThirdPartyException $e){
                $message .= "<div> can not sync Customer_id: ".$customer_id."</div>";
                log_audit_message('error', 'can not sync phone call history of Customer_id:' . $customer_id.", message: ".$e->getMessage() , false, 'sync_callhistory_sontel');
            } catch (DAOException $e1){
                $message .= "<div> can not sync Customer_id: ".$customer_id."</div>";
                log_audit_message('error', 'can not sync phone call history of Customer_id:' . $customer_id.", message: ".$e1->getMessage() , false, 'sync_callhistory_sontel');
            }
        }

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // Notify
        $this->send_notification($jobName, true, $message);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * notify warning message with slow account.
     */
    public function notify_slow_account(){
        $jobName = 'notify_slow_account';

        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        $this->load->model('phones/phone_setting_m');
        $customers = $this->phone_setting_m->get_many_by_many(array(
            "notify_flag" => APConstants::ON_FLAG,
        ));
        $message = "<h3>List of customers :</h3><br/>";
        foreach($customers as $customer){
            $customer_id = $customer->parent_customer_id;
            $open_balance = APUtils::getAdjustOpenBalanceDue($customer_id);
            if($open_balance['OpenBalanceDue'] < $customer->max_daily_usage){
                $message .= "<div>Customer_id: ".$customer_id."</div>";
                $customer_infor = $this->customer_m->get($customer_id);
                $data = array(
                    "slug" => APConstants::email_notify_warning_slow_account,
                    "to_email" => $customer_infor->email,
                    // Replace content
                    "full_name" => $customer_infor->user_name,
                    "site_url" => base_url(),
                );

                // Send email
                MailUtils::sendEmailByTemplate($data);
            }
        }

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // Notify
        $this->send_notification($jobName, true, $message);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Create credit note for discount customer of marketing partner.
     * THIS JOB ONLY RUN ON FIRST DAY OF MONTH.
     */
    public function create_credit_note_discount_customer(){
        $jobName = "create_credit_note_discount_customer";
        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);
        $startTime = date('Y-m-d H:i:s');

        // Only run this cron job at the first day of month
        if (!APUtils::isFirstDayOfMonth()) {
            echo CronConfigs::EXEC_CRONJOB_PROCESS_AUTO_STOPPED;
            //exit();
        }

        $notificationMessage = cron_api::create_credit_note_discount_customer();

        $this->send_notification($jobName, true, $notificationMessage);
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

	/**
     * apply new vat of enterprise customer.
     */
    public function apply_new_upcharge_enterprise_customer(){
        $jobName = 'apply_new_upcharge_enterprise_customer';

        if(!APUtils::isFirstDayOfMonth()){
            echo "This job only apply on first day of month";
            return;
        }

        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        $this->load->model("customers/customer_setting_m");
        $this->load->model('customers/customer_m');

        $date_change = APUtils::getFirstDayOfCurrentMonth();
        $customers = $this->customer_setting_m->get_many_by_many(array(
            "apply_date" => $date_change
        ));
        $message = "<h3>List of changes :</h3><br/>";
        foreach($customers as $customer){
            $this->customer_setting_m->update_by_many(array(
                "id" => $customer->id,
            ), array(
                "setting_value" => $customer->new_value,
                "new_value" => null,
                "apply_date" => null
            ));

            log_audit_message(APConstants::LOG_INFOR, "Parent customer_id: ".$customer->parent_customer_id.", key: ".$customer->setting_key.", old value: ".$customer->setting_value.", new value: ".$customer->new_value, FALSE,'apply_new_upcharge_enterprise_customer');
            $message .= "<div>Parent Customer_id: ".$customer->parent_customer_id."</div>";
        }

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // Notify
        $this->send_notification($jobName, true, $message);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Sync phone prices from sonetel to clevvermail database (DAILY)
     */
    public function sync_phones_prices(){
        $jobName = 'sync_phones_prices';
        $this->load->library("phones/phones_api");

        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        // calculate invoice summary
        $message = '';
        phones_api::sync_phone_number_price();
        phones_api::sync_outboundcalls_price();

        phones_api::apply_phone_number_price();
        phones_api::apply_outboundcalls_price();

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // Notify
        $this->send_notification($jobName, true, $message);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Sync phone prices from sonetel to clevvermail database (DAILY)
     */
    public function auto_payment_transfer_for_enterprise_customer(){
        $jobName = 'auto_payment_transfer_for_enterprise_customer';
        $this->load->library("customers/customers_api");

        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        // calculate invoice summary
        $message = '';
        customers_api::auto_payment_transfer_for_enterprise_customer();

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // Notify
        $this->send_notification($jobName, true, $message);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Auto calcualte recurring fee for phone number
     */
    public function auto_calculate_recurring_fee_phone_number() {
        $jobName = 'auto_calculate_recurring_fee_phone_number';
        $this->load->library("phones/phones_api");

        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        // calculate invoice summary
        $message = '';
        // Extend expired contract
        phones_api::autoExtendPhoneNumberContract();

        // Calculate recurring fee
        phones_api::autoCalculateRecurringFeePhoneNumber();

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // Notify
        $this->send_notification($jobName, true, $message);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * sync phone area code.
     */
    public function sync_phone_area_code(){
        $jobName = 'sync_phone_area_code';
        $this->load->library("phones/phones_api");
        $this->load->model('settings/countries_m');
        $this->load->model('phones/phone_area_code_latest_m');

        //$this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        // calculate invoice summary
        $message = '';

        phones_api::sync_phone_area_code();

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // Notify
        $this->send_notification($jobName, true, $message);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * Create bonus credit note by monthly for customers of special partner
     */
    public function createBonusCreditnoteOfPartner(){
        $jobName = 'createBonusCreditnoteOfPartner';
        $this->load->model(array(
            'partner/partner_marketing_profile_m',
            'invoices/invoice_summary_by_location_m',
        ));

        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        // calculate invoice summary
        $message = '';

        // do bonus
        cron_api::createBonusCreditnoteOfPartner();

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // Notify
        $this->send_notification($jobName, true, $message);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }

    /**
     * calculate enterprise charge for enterprise account
     */
    public function calculate_enterprise_invoices(){
        $jobName = 'calculate_enterprise_invoices';

        $this->checkCronjobKey($jobName);
        $jobID = CronUtils::insertJob($jobName, $this->curYear, $this->curMonth, $this->curDate, $this->curHour, $this->curMinute, $this->curSecond);

        // tracking time run.
        $startTime = date('Y-m-d H:i:s');

        // calculate invoice summary
        $message = '';

        // do bonus
        cron_api::calculate_enterprise_invoices();

        // tracking time execution.
        $endTime = date('Y-m-d H:i:s');
        CronUtils::logJobTimeExecution($jobID, $startTime, $endTime);

        // Notify
        $this->send_notification($jobName, true, $message);
        echo CronConfigs::EXEC_CRONJOB_PROCESS_SUCCESS;
    }
}
