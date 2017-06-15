<?php defined('BASEPATH') or exit('No direct script access allowed');

class CronConfigs
{
    // Cronjob status messages
    const EXEC_CRONJOB_INVALID_REQUEST = 'EXEC CRONJOB - INVALID REQUEST!';
    const EXEC_CRONJOB_EXECUTED_ALREADY = 'EXEC CRONJOB - EXECUTED ALREADY!';
    const EXEC_CRONJOB_PROCESS_AUTO_STOPPED = 'EXEC CRONJOB - NOT ENOUGH CONDITION TO CONTINUE!';
    const EXEC_CRONJOB_PROCESS_SUCCESS = 'EXEC CRONJOB - SUCCESS!';
    const EXEC_CRONJOB_PROCESS_ERROR = 'EXEC CRONJOB - ERROR!';

    // Cronjob keys
    const CRON_KEY_default = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_calculate_total_invoice = '5eb96ffccee348403fbf2cd4a0addca0';
    const CRON_KEY_check_account_registration = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_send_invoice_monthly_report = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_send_email_notify = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_calculate_invoices_directly = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_calculate_invoices = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_calculate_invoices_summary_directly = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_check_card_expire_date = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_delete_postbox = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_delete_envelope_old30 = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_storage_envelope_old30 = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_cal_storage_summary_directly = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_storage_envelope_old30_directly = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_cal_storage_summary_backdate_directly = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_apply_new_account = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_collect_shipping_envelope = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_notify_verification_address_postbox = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_send_first_letter = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_delete_plan_customer = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_update_post_code = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_generate_invoice_pdf = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_send_email_notify_open_balance_due = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_send_email_notify_deactivate_open_balance_due = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_get_all_customer_nopayment_method = '5eb96ffccee348403fbf2cd4a0addca0';
    const CRON_KEY_sync_customer_mailchimp = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_update_open_balance_due = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_update_currency_exchange_rate = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_update_pricing_template = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_cancel_verification_cases = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_google_adwards = 'xx';
    const CRON_KEY_auto_check_jobs_execution = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_send_push_message = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_notify_todo_number_to_location_admin = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_notify_email_new_and_delete_customers = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_send_general_accounts_reporting_job  = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_auto_trash  = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_send_email_job  = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_update_location_report  = 'f54d83d0f272327742f9ab305c89dd5f';

    const CRON_KEY_send_notify_new_terms_condition  = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_check_customer_accept_new_terms_condition  = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_create_credit_note_discount_customer  = 'f54d83d0f272327742f9ab305c89dd5f';

    const CRON_KEY_sync_phones_prices  = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_auto_payment_transfer_for_enterprise_customer = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_auto_calculate_recurring_fee_phone_number = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_createbonuscreditnoteofpartner = 'f54d83d0f272327742f9ab305c89dd5f';
    const CRON_KEY_calculate_enterprise_invoices = 'f54d83d0f272327742f9ab305c89dd5f';
    
    // Format message of job start time - job end time
    const MSG_JOB_STATUS = "%s - %s. Total: %d (minutes)";
    // Cron run every weeks
    const CRON_KEY_account_deletion_and_remain = 'f54d83d0f272327742f9ab305c89dd5f';
    
}