<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Currently, this plugs into the admin_notification to use PyroStreams validation.
 *
 * @package PyroStreams
 * @category events
 * @author Parse19
 * @copyright Copyright (c) 2011 - 2012, Parse19
 * @license http://parse19.com/pyrostreams/docs/license
 * @link http://parse19.com/pyrostreams
 */
class Events_Customers
{
    protected $CI;

    // --------------------------------------------------------------------------
    public function __construct()
    {
        $this->CI = &get_instance();

        // Register the deactivated_notifications event
        Events::register('deactivated_notifications', array(
            $this,
            'deactivated_notifications'
        ));

        // Register the cal_postbox_invoices_directly event
        Events::register('cal_postbox_invoices_directly', array(
            $this,
            'cal_postbox_invoices_directly'
        ));

        // Register the cal_postbox_invoices_directly event
        Events::register('send_notify_auto_scan', array(
            $this,
            'send_notify_auto_scan'
        ));
    }

    // --------------------------------------------------------------------------

    /**
     * Send email notify when user deactivated
     *
     * @access public
     * @return void
     */
    public function deactivated_notifications($params)
    {
        ci()->load->model('customers/customer_m');
        ci()->load->model('email/email_m');

        // Get customer id
        $customer_id = $params['customer_id'];
        $customer = ci()->customer_m->get_by_many(array(
            'customer_id' => $customer_id
        ));

        // Check exist customer
        if (empty($customer)) {
            return;
        }

        // Get all customers
        $company_name = Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE);
        $bank_name = Settings::get(APConstants::INSTANCE_OWNER_BANK_NAME_CODE);
        $iban = Settings::get(APConstants::INSTANCE_OWNER_IBAN_CODE);
        $bic = Settings::get(APConstants::INSTANCE_OWNER_SWIFT_CODE);
        $open_balance_due = APUtils::number_format(APUtils::getCurrentBalance($customer->customer_id));

        // Send email notify de-activated user to customer
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
    }

    /**
     * Send email notify when user has auto envelope/item scan
     *
     * @access public
     * @return void
     */
    public function send_notify_auto_scan($params)
    {
        ci()->load->model('customers/customer_m');
        ci()->load->model('email/email_m');

        // Get customer id
        $customer_id = $params['customer_id'];
        $envelope_code = $params['envelope_code'];
        $auto_scan_type = $params['auto_scan_type'];
        $customer = ci()->customer_m->get_by_many(array(
            'customer_id' => $customer_id
        ));

        // Check exist customer
        if (empty($customer)) {
            return;
        }

        // Send email notify de-activated user to customer
        $email_template_code = '';
        if ($auto_scan_type == 'envelope') {
            $email_template_code = APConstants::send_notify_auto_envelope_scan;
        } else if ($auto_scan_type == 'item') {
            $email_template_code = APConstants::send_notify_auto_item_scan;
        }
            
        $data = array(
            "slug" => $email_template_code,
            "to_email" => $customer->email,
            // Replace content
            "full_name" => $customer->user_name,
            "envelope_code" => $envelope_code
        );
        // Send email
        MailUtils::sendEmailByTemplate($data);
    }

    /**
     * Send email notify when user deactivated
     *
     * @access public
     * @return void
     */
    public function cal_postbox_invoices_directly($params)
    {
        log_message(APConstants::LOG_DEBUG, 'Start to calculate postbox fee');
        ci()->load->model('customers/customer_m');
        ci()->load->library('invoices/Invoices');

        // Get customer id
        $customer_id = $params['customer_id'];
        $customer = ci()->customer_m->get_by_many(array(
            'customer_id' => $customer_id
        ));

        // Check exist customer
        if (empty($customer)) {
            return;
        }

        ci()->invoices->calculate_invoice($customer->customer_id);
        //$target_month = APUtils::getCurrentMonthInvoice();
        //$target_year = APUtils::getCurrentYearInvoice();
        //ci()->invoices->cal_invoice_summary($customer_id, $target_year, $target_month);
    }
}