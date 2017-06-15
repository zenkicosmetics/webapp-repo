<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*********************************************************************************
 | Config data of AFI customer (sub_domain_name = localhost)
**********************************************************************************/
#$config['MAIL_PROTOCOL'] = 'sendmail'; // mail, sendmail, smtp
#$config['MAIL_SENDMAIL_PATH'] = 'ssl://smtp.googlemail.com';
#$config['MAIL_SMTP_HOST'] = 'ssl://smtp.googlemail.com';
#$config['MAIL_SMTP_USER'] = 'relation02@gmail.com';
#$config['MAIL_SMTP_PASS'] = 'relation@123';
#$config['MAIL_SMTP_PORT'] = '465';
#$config['MAIL_ALIAS_NAME'] = 'clevvermail.com';
#$config['EMAIL_FROM'] = 'register@clevvermail.com';

$config['MAIL_PROTOCOL'] = 'smtp'; // mail, sendmail, smtp
$config['MAIL_SENDMAIL_PATH'] = '';
$config['MAIL_SMTP_HOST'] = 'mail.clevvermail.com';
$config['MAIL_SMTP_USER'] = 'system@clevvermail.com';
$config['MAIL_SMTP_PASS'] = 'MailService.3';
$config['MAIL_SMTP_PORT'] = '587';
$config['MAIL_ALIAS_NAME'] = 'register@clevvermail.com';

/* End of file database.php */
/* Location: ./application/config/database.php */