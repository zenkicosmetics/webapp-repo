<?php defined('BASEPATH') or exit('No direct script access allowed');

class Envelope
{
    /**
     * Tinh toan chi phi cho khach hang den thoi diem hien tai.
     */
    public function add_first_letter($customer_id, $postbox_id)
    {
        // Load model
        ci()->load->model('scans/envelope_m');
        ci()->load->model('email/email_m');
        ci()->load->model('customers/customer_m');
        ci()->load->model('mailbox/postbox_m');

        $first_letter_check = ci()->envelope_m->get_by_many(array(
            'to_customer_id' => $customer_id,
            'postbox_id' => $postbox_id,
            'incomming_letter_flag' => '1'
        ));
        if (!empty($first_letter_check)) {
            return;
        }

        // Get setting of customer id
        $customer = ci()->customer_m->get_by_many(array(
            "customer_id" => $customer_id
        ));

        // Get setting of customer id
        $postbox = ci()->postbox_m->get_by_many(array(
            "postbox_id" => $postbox_id
        ));

        $incomming_date_only = date('dmy');
        $envelope_code = $postbox->postbox_code . '_' . $incomming_date_only . '_000';

        $from_customer_name = 'ClevverMail Team';
        $envelope_type_id = 'C3';
        $category_type = null;
        // Insert information to envelope table
        $envelope_id = ci()->envelope_m->insert(array(
            'from_customer_name' => $from_customer_name,
            'to_customer_id' => $customer_id,
            'postbox_id' => $postbox_id,
            'envelope_code' => $envelope_code,
            'envelope_type_id' => $envelope_type_id,
            'weight' => '0',
            'weight_unit' => 'g',
            'last_updated_date' => now(),
            'incomming_date' => now(),
            'incomming_date_only' => $incomming_date_only,
            'completed_flag' => APConstants::OFF_FLAG,
            'category_type' => $category_type,
            'invoice_flag' => APConstants::OFF_FLAG,
            "envelope_scan_flag" => APConstants::ON_FLAG,
            "item_scan_flag" => APConstants::ON_FLAG,
            "email_notification_flag" => APConstants::ON_FLAG,
            "new_notification_flag" => APConstants::OFF_FLAG,
            "direct_shipping_flag" => APConstants::ON_FLAG,
            "incomming_letter_flag" => APConstants::ON_FLAG
        ));

        // Insert to envelope file
        $first_letter_envelope = Settings::get(APConstants::FIRST_ENVELOPE_KEY);
        $first_letter_item = Settings::get(APConstants::FIRST_LETTER_KEY);

        // Envelope image
        ci()->envelope_file_m->insert(array(
            "envelope_id" => $envelope_id,
            "customer_id" => $customer->customer_id,
            "file_size" => 0,
            //"file_name" => APContext::getAssetPath() . $first_letter_envelope,
            //"public_file_name" => APContext::getAssetPath() . $first_letter_envelope,
            "file_name" => $first_letter_envelope,
            "public_file_name" => $first_letter_envelope,
            "local_file_name" => $first_letter_envelope,
            "created_date" => now(),
            "type" => '1',
            "number_page" => '0',
            "amazon_path" => '',
            "amazon_relate_path" => ''
        ));

        // Envelope item
        ci()->envelope_file_m->insert(array(
            "envelope_id" => $envelope_id,
            "customer_id" => $customer->customer_id,
            "file_size" => 0,
            //"file_name" => APContext::getAssetPath() . $first_letter_item,
            //"public_file_name" => APContext::getAssetPath() . $first_letter_item,
            "file_name" => $first_letter_item,
            "public_file_name" => $first_letter_item,
            "local_file_name" => $first_letter_item,
            "created_date" => now(),
            "type" => '2',
            "number_page" => '0',
            "amazon_path" => '',
            "amazon_relate_path" => ''
        ));

        // Send email confirm for user
        $data = array(
            "slug" => APConstants::first_letter_notification,
            "to_email" => $customer->email,
            // Replace content
            "full_name" => $customer->email,
            "envelope_code" => $envelope_code
        );
        // Send email
        MailUtils::sendEmailByTemplate($data);
    }
}