<?php defined('BASEPATH') or exit('No direct script access allowed');

class MailUtils
{
    /**
     * Send email with specified subject and content
     */
    public static function sendEmail($from_email = '', $to_email, $subject, $content, $attachments = array(), $smtp = true, $from_alias_name = '')
    {
        if(MailUtils::checkDummyEmail($to_email)){
            log_audit_message(APConstants::LOG_INFOR, "CAN NOT SEND TO EMAIL: ".$to_email.", subject: ".$subject, false, 'sendEmail');
            return true;
        }
        
        $to_customer = ci()->customer_m->get_by('email', $to_email);
        if (!empty($to_customer) && $to_customer->status == 1) {
           return false;
        }

        //From address
        $from_email = $from_email == '' ? Settings::get(APConstants::MAIL_CONTACT_CODE) : $from_email;
        $from_alias_name = $from_alias_name == '' ? Settings::get(APConstants::MAIL_ALIAS_NAME_CODE) : $from_alias_name;

        //If use SMTP to send, set up smtp configuration
        if ($smtp) {
            $config = Array(
                'protocol' => Settings::get(APConstants::MAIL_PROTOCOL_CODE),
                'smtp_host' => Settings::get(APConstants::MAIL_SMTP_HOST_CODE),
                'smtp_port' => Settings::get(APConstants::MAIL_SMTP_PORT_CODE),
                'smtp_user' => Settings::get(APConstants::MAIL_SMTP_USER_CODE),
                'smtp_pass' => Settings::get(APConstants::MAIL_SMTP_PASS_CODE),
                'mailtype' => 'html',
                'charset' => 'utf-8'//'iso-8859-1'
            );
            ci()->load->library('email', $config);
        } else {
            //Send by local library
            ci()->load->library('email');
        }

        ci()->load->model('email/email_sent_hist_m');

        // #783: Create a special footer for link in all emails
        $content2 = $content;
        if( strpos($content2, '{{direct_access_url}}') !== false ) {
            $content2 = str_replace('{{direct_access_url}}', MailUtils::createDirectAccessUrl($to_email), $content );
        }

        ci()->email->clear(TRUE);
        ci()->email->from($from_email, $from_alias_name);
        ci()->email->to($to_email);
        ci()->email->subject(str_replace("\r\n", "\n", $subject));
        ci()->email->message($content2);
        ci()->email->set_crlf("\n");
        ci()->email->set_newline("\n");

        foreach ($attachments as $attachment) {
            ci()->email->attach($attachment);
        }

        $send_history =  array(
                "email_type" => 0,
                "from_email" => $from_email,
                "to_email" => $to_email,
                "subject" => $subject,
                "content" => $content2,
                "sent_date" => now()
        );

        //Send email
        if (ci()->email->send()) {
            //Send email success
            ci()->email_sent_hist_m->insert(array_merge($send_history,array('sent_status' => 1)));
            return true;
        } else {
            //Send mail false
            ci()->email_sent_hist_m->insert(array_merge($send_history,array('sent_status' => 0)));
            return false;
        }
    }

    /**
     * Send email by template
     */
    public static function sendEmailByTemplate($data = array())
    {
        ci()->load->library('email');
        ci()->load->model('email/email_m');
        ci()->load->model('customers/customer_m');
        // Support get email template for enterprise user
        ci()->load->model('email/email_customer_m');

        $slug = $data['slug'];
        unset($data['slug']);

        // get email template by slug
        $to_email = isset($data['to_email']) ? $data['to_email'] : null;
        if (empty($to_email)) {
            return false;
        }
        if(MailUtils::checkDummyEmail($to_email)){
            log_audit_message(APConstants::LOG_INFOR, "CAN NOT SEND TO EMAIL: ".$to_email.", slug template: ".$slug, false, 'sendEmailByTemplate');
            return true;
        }

        // Get customer information
        $template = null;
        $to_customer = ci()->customer_m->get_by('email', $to_email);
        $is_user_enterprise = false;
        if (!empty($to_customer)) {
            
            // DO NOT SEND EMAIL TO USER DELETED
            if ($to_customer->status == 1) {
                return true;
            }
            
            $to_parent_customer_id = $to_customer->parent_customer_id;
            if (!empty($to_parent_customer_id) && $to_customer->account_type == APConstants::ENTERPRISE_CUSTOMER) {
                $is_user_enterprise = true;
                $template = ci()->email_customer_m->get_by_many(array('slug' => $slug, 'customer_id' => $to_parent_customer_id));
            }
        }
        // If this is not enterprise customer or the email template of enterprise customer did not exist
        if (empty($template)) {
            $template = ci()->email_m->get_by('slug', $slug);
        }

        if (!empty($template) && !empty($to_email)) {
            // DO NOT SEND EMAIL TO USER ENTEPRRISE
            if($is_user_enterprise && $template->relevant_enterprise_account == 0){
                return true;
            }

           

            // perhaps they've passed a pipe separated string, let's switch it
            // to commas for CodeIgniter
            if (!is_array($to_email)){
                $to_email = str_replace('|', ',', $to_email);
            }

            //From address
            $from_email = !empty($data['from_email']) ? $data['from_email'] : Settings::get(APConstants::MAIL_CONTACT_CODE) ;
            $from_alias_name = !empty($data['from_alias_name']) ? $data['from_alias_name'] : Settings::get(APConstants::MAIL_ALIAS_NAME_CODE) ;

            // If this is email send to customer
            if ($template->template_type == 'customers') {
                $from_noreply_email = Settings::getAlias01ByCode(APConstants::MAIL_CONTACT_CODE);
                if (!empty($from_noreply_email)) {
                    $from_email = $from_noreply_email;
                }
            }
            //Get template content
            // #783: Create a special footer for link in all emails
            if( strpos($template->content, '{{direct_access_url}}') !== false ) {
                $data['direct_access_url'] = MailUtils::createDirectAccessUrl($to_email);
            }
            $body = APUtils::parserString($template->content, $data, TRUE);
            $subject = MailUtils::parserString($template->subject, $data, TRUE);

            //Get smtp configuration
            $config = Array(
                'protocol' => Settings::get(APConstants::MAIL_PROTOCOL_CODE),
                'smtp_host' => Settings::get(APConstants::MAIL_SMTP_HOST_CODE),
                'smtp_port' => Settings::get(APConstants::MAIL_SMTP_PORT_CODE),
                'smtp_user' => Settings::get(APConstants::MAIL_SMTP_USER_CODE),
                'smtp_pass' => Settings::get(APConstants::MAIL_SMTP_PASS_CODE),
                'mailtype' => 'html',
                'charset' => 'utf-8'//'iso-8859-1'
            );

            ci()->load->library('email', $config);
            ci()->load->model('email/email_sent_hist_m');

            ci()->email->clear(TRUE);
            ci()->email->from($from_email, $from_alias_name);
            ci()->email->to($to_email);
            ci()->email->subject(str_replace("\r\n", "\n", $subject));
            ci()->email->message($body);
            ci()->email->set_crlf("\n");
            ci()->email->set_newline("\n");

            if (isset($data['attachments'])) {
                foreach ($data['attachments'] as $attachment) {
                    ci()->email->attach($attachment);
                }
            }

            $send_history =  array(
                "email_type" => 0,
                "from_email" => $from_email,
                "to_email" => $to_email,
                "subject" => $subject,
                "content" => $body,
                "sent_date" => now()
            );

            //Send email
            if (ci()->email->send()) {
                //Send email success
                ci()->email_sent_hist_m->insert(array_merge($send_history,array('sent_status' => 1)));
                return true;
            } else {
                //Send mail false
                ci()->email_sent_hist_m->insert(array_merge($send_history,array('sent_status' => 0)));
                return false;
            }

        }

        return false;
    }

    /**
     * #783: Create a special footer for link in all emails
     */
    public static function createDirectAccessUrl($to_email){

        ci()->load->model('customers/customer_m');
        //Get un-deleted customer infor by email
        $customer = ci()->customer_m->get_by_many(array(
            'email' => $to_email,
            '(status is null or status = 0)' => null
        ));

        $direct_access_url = '';

        if($customer){
            $direct_access_url = APContext::getFullBalancerPath().'customers/direct_access?access_token=';
            $new_key = $customer->direct_access_key;
            $time = now();
            $expire_days = APConstants::EXPIRE_DAYS_DIRECT_ACCESS_MAILBOX*86400;
            $expire_date = (int)$time + (int)$expire_days;
            //Create direct access url
            if($customer->direct_access_key){
                $direct_access_url .= $customer->direct_access_key;
            }else{
                $new_key = md5(uniqid());
                $direct_access_url .= $new_key;
            }
            //Update direct access url for customer
            ci()->customer_m->update_by_many(array(
                "customer_id" => $customer->customer_id
            ), array(
                "direct_access_expired" => $expire_date,
                "direct_access_key" => $new_key
            ));

        }

        return $direct_access_url;
    }

    /**
     * Add email to email_queues table, wait cron job (run every hours) to real send
     */
    public static function addEmailQueue($email_data){

        if (!empty($email_data['to_email']) && !empty($email_data['slug']) && !empty($email_data['send_date'])){

            $mail_queue_data = array(
                'from_email' => isset($email_data['from_email']) ? $email_data['from_email'] : '',
                'to_email' => $email_data['to_email'],
                'slug' =>  $email_data['slug'],
                'send_date' => $email_data['send_date'],
                'status' => 0,
                'attachments' => isset($email_data['attachments']) ? json_encode($email_data['attachments']): null,
                'data' => isset($email_data['data']) ? json_encode($email_data['data']): null
            );

           ci()->load->model('email/email_queue_m');
           if (ci()->email_queue_m->insert($mail_queue_data)){
               return true;
           }
        }

        return false;
    }

    /**
     * Replace all key by array of value.
     *
     * @param unknown_type $content :
     *            E.g: Please replace thhis value: {{user_name}}
     * @param unknown_type $data :
     *            The array of data: array("user_name" => "DungNT")
     */
    public static function parserString($content, $data)
    {
        return ci()->parser->parse_string(str_replace('&quot;', '"', $content), $data, TRUE);
    }

    /**
     * send email for customer when add post box
     *
     * @param int_type $postbox_id
     * @param int_type $account_type
     * @param int_type $customer_id
     * @param int_type $location
     */
    public static function sendEmailWhenAddPostBox($postbox_id, $account_type, $customer_id, $location_id)
    {
    	// Load model
    	ci()->load->model('customers/customer_m');
    	ci()->load->model('mailbox/postbox_m');
    	ci()->load->model('email/email_m');
    	ci()->load->model('settings/countries_m');
    	// load library
    	ci()->load->library('addresses/addresses_api');
    	// Load lang
    	ci()->lang->load('addresses/address');

    	// Get customer information
    	$customer = ci()->customer_m->get($customer_id);

    	// Check account_type: BUSINESS_TYPE, PRIVATE_TYPE, FREE_TYPE
    	if ($account_type == APConstants::FREE_TYPE ){
    		$account_type = lang('postbox_type_1');
    	}elseif ($account_type == APConstants::PRIVATE_TYPE){
    		$account_type = lang('postbox_type_2');
    	}elseif ($account_type == APConstants::BUSINESS_TYPE ) {
    		$account_type = lang('postbox_type_3');
    	}elseif ($account_type == APConstants::ENTERPRISE_CUSTOMER ) {
    		$account_type = lang('postbox_type_5');
    	}

    	// Get info postbox detail
    	$postbox_detail = ci ()->postbox_m->get_by_many ( array (
            "postbox_id" => $postbox_id
        ) );

    	// Send email to customer if this is business account
    	$postbox_name = $postbox_detail->postbox_name;
    	$name = $postbox_detail->name;
    	$company = $postbox_detail->company;

    	// Get info location
    	$location = ci ()->addresses_api->getLocationByID ($location_id);

    	// Get info country
    	$country = ci()->countries_m->get_by_many(array(
            'id' => $location->country_id
        ));
    	$to_email = $customer->email;

        if(MailUtils::checkDummyEmail($to_email)){
            log_audit_message(APConstants::LOG_INFOR, "CAN NOT SEND TO EMAIL: ".$to_email.", subject: ".APConstants::new_business_account_notification, false, 'sendEmailWhenAddPostBox');
            return true;
        }

    	$data = array(
            "slug" => APConstants::new_business_account_notification,
            "to_email" => $customer->email,
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
            "country" =>$country->country_name,
            "location_email" => $location->email,
            "location_phone" => $location->phone_number
    	);
    	// Send email
        MailUtils::sendEmailByTemplate($data);
    }


    public static function checkDummyEmail($email){
        $partern = "/^(c|C)(\d\d\d\d\d\d\d\d)$/";
        $tmp = explode("@", $email);
        if (preg_match($partern, $tmp[0])) {
            return true;
        } else {
            return false;
        }
    }
}