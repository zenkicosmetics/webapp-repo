<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * MY_Email - Allows for email config settings to be stored in the db.
 */
class MY_Email extends CI_Email
{
    /**
     * Constructor method
     *
     * @access public
     * @return void
     */
    public function __construct($config = array())
    {
        parent::__construct($config);

        // set mail protocol
        $config ['protocol'] = ci()->config->item('MAIL_PROTOCOL');

        // set a few config items (duh)
        $config ['mailtype'] = "html";
        $config ['charset'] = "utf-8";
        $config ['crlf'] = "\n";
        $config ['newline'] = "\n";

        // sendmail options
        if (ci()->config->item('MAIL_PROTOCOL') == 'sendmail') {
            if (ci()->config->item('MAIL_SENDMAIL_PATH') == '') {
                // set a default
                $config ['mailpath'] = '/usr/sbin/sendmail';
            } else {
                $config ['mailpath'] = ci()->config->item('MAIL_SENDMAIL_PATH');
            }
        }

        // smtp options
        if (ci()->config->item('MAIL_PROTOCOL') == 'smtp') {
            $config ['smtp_crypto'] = 'tls';
            $config ['smtp_host'] = ci()->config->item('MAIL_SMTP_HOST');
            $config ['smtp_user'] = ci()->config->item('MAIL_SMTP_USER');
            $config ['smtp_pass'] = ci()->config->item('MAIL_SMTP_PASS');
            $config ['smtp_port'] = ci()->config->item('MAIL_SMTP_PORT');
        }
        $this->initialize($config);
    }
}