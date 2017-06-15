<?php defined('BASEPATH') or exit('No direct script access allowed');

class email_api
{
    public static function getEmail($slug)
    {
        ci()->load->model('email/email_m');

        $email = ci()->email_m->get_by('slug', $slug);

        return $email;
    }
    
    /**
     * Apply default phone number price from table [pricing_phones_outboundcalls] to [pricing_phones_outboundcalls_customer]
     */
    public static function init_email_template($customer_id) {
        ci()->load->model(array(
            "email/email_m",
            "email/email_customer_m"
        ));
        
        // Get all records from [emails]
        $all_emails = ci()->email_m->get_many_by_many(array(
            "relevant_enterprise_account" => '1'
        ));
        // For each records
        if (empty($all_emails) || count($all_emails) == 0) {
            return;
        }
        $total_email = count($all_emails);
        
        $total_customer_email = ci()->email_customer_m->count_by_many(array(
            'customer_id' => $customer_id,
            'relevant_enterprise_account' => '1'
        ));
        if ($total_email == $total_customer_email) {
            return;
        }
        foreach($all_emails as $email) {
            // Insert new
            $array_email = APUtils::convertObjectToArray($email);
            unset($array_email['id']);
            $slug = $array_email['slug'];
            $email_check = ci()->email_customer_m->get_by_many(array(
                'slug' => $slug,
                'customer_id' => $customer_id
            ));
            if (empty($email_check)) {
                $array_email['customer_id'] = $customer_id;
                $array_email['language'] = 'English';
                ci()->email_customer_m->insert($array_email);
            }
        }
    }
}