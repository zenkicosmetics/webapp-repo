<?php

defined('BASEPATH') or exit('No direct script access allowed');
$lang ['header:list_customer_title'] = 'Manage Customers';
$lang ['header:import_customer_title'] = 'Import Customers';
$lang ['header:list_postbox_history_title'] = 'Manage Postbox History';
$lang ['header:list_customer_history_title'] = 'Manage Customer History';

$lang ['email'] = 'Email';
$lang ['password'] = 'Password';
$lang ['repeat_password'] = 'Repeat Password';
$lang ['agree_flag'] = 'Agree to the Terms of Service';
$lang ['charge_fee_flag'] = 'Charge';

$lang ['customer.add_success'] = 'The customer "%s" has been added.';
$lang ['customer.add_error'] = 'The customer "%s" could not be added.';
$lang ['customer.edit_success'] = 'The customer "%s" has been saved.';
$lang ['customer.edit_error'] = 'The customer "%s" could not be saved.';
$lang ['customer.delete_success'] = 'This ClevverMail account has been deleted successfully.';
$lang ['customer.delete_error'] = 'There was an error deleting this customer';
$lang ['customer.confirm_password_error'] = 'The two passwords do not match';

$lang ['customer.exist_in_black_list'] = 'Your email has been rejected by the system. Please contact us at mail@clevvermail.com to register';

$lang ['email_exist'] = 'Your email address already exist.';
$lang ['login_unsuccessful'] = 'Your username or password are incorrect.';
$lang ['forgot_pass_unsuccessful'] = 'Your email is incorrect or does not exist.';
$lang ['forgot_pass_successful'] = 'Your password was reset successfully. Your new password has been sent to your email address.';
$lang ['customer.account_manual_activated_message'] = 'Your account has been deactivated.<br/>Please contact us at mail@clevvermail.com.<br/>Your Customer ID is: %s';


$lang ['customer_reset_pass_email_subject'] = 'Password Reset';
$lang ['customer_reset_pass_email_body'] = 'Dear %s, \r\nYour password at %s has been reset. Your new password is %s. \r\nIf you did not request this change, please email us at %s and we will resolve the situation.';

$lang ['customer.save_postboxname_success'] = 'The postbox has been updated successfully.';
$lang ['customer.address_required'] = 'Please input address name or address company name.';

$lang ['customer.activated'] = 'Activated';
$lang ['customer.not_activated'] = 'Not activated';
$lang ['customer.never_activated'] = 'Never activated';
$lang ['customer.auto_deactivated'] = 'auto-deactivated';
$lang ['customer.manu_deactivated'] = 'manu-deactivated';

$lang ['customer.charge'] = 'Charge';
$lang ['customer.no_charge'] = 'No Charge';
$lang ['customer.yes'] = 'Yes';
$lang ['customer.no'] = 'No';
$lang ['generate_invoice_success'] = 'New invoice code: %s generated successfully.';
$lang ['invoice_code_exists'] = 'Invoice code already exist.';
$lang ['postbox_name'] = 'postbox name';
$lang ['address_name'] = 'address name';
$lang ['address_company_name'] = 'Address Company Name';
$lang ['location_available_id'] = 'location available';

$lang ['resend_email_success'] = 'The email confirmation link has been sent to %s successfully.';
$lang ['customer.invoice_code_invalid'] = 'Invoice code is invalid. Please input 10 character.';

$lang ['customer.make_manual_payment_fail'] = 'The payment was processed fail.';
$lang ['customer.make_manual_payment_success'] = 'The payment was processed successfully.';
$lang ['customer.make_manual_invoice_success'] = 'The invoice has been created successfully.';

$lang ['customer.make_manual_credit_note_success'] = 'The credit note has been created successfully.';

$lang ['customer.record_external_payment_success'] = 'The record external payment of customer: %s has been added successfully.';
$lang ['customer.record_external_payment_fail'] = 'The record external payment of customer: %s has been added fail.';

$lang ['customer.record_refund_payment_success'] = 'The record refund payment of customer: %s has been added successfully.';
$lang ['customer.record_refund_payment_fail'] = 'The record refund payment of customer: %s has been added fail.';

$lang ['customer.has_no_credit_card'] = 'There is no credit card in the customer account.';
$lang ['customer.save_direct_charge_without_invoice_success'] = 'The direct charge of customer: %s has been added successfully.';
$lang ['customer.save_direct_charge_without_invoice_error'] = 'The charge to this credit card was unsuccessful. Please check your credit card details or contact your bank for more information.';
$lang ['customer.notsupport_direct_charge_without_invoice'] = 'The charge to this credit card was unsuccessful. Please check your credit card details or contact your bank for more information.';
$lang ['customer.save_direct_charge_without_invoice_validate'] = 'Total amount should be greater 0.';

$lang ['customer.save_direct_charge_without_invoice_success2'] = "The deposit charge of %s EUR to the account %s was initiated successfully. Please wait a few minutes to see it in your account";

$lang ['customer.status.active'] = 'Active';
$lang ['customer.status.deleted'] = 'Deleted';
$lang ['customer.delete_error.balance'] = 'You can not delete because balance of this customer is greater than 0';

$lang ['customer.customer_not_register'] = "You're not registered yet. Please <a id='reRegisterNewUser' href='#' class='tooltip'>register here</a>.";
$lang ['customer.customer_deleted'] = "You're not registered yet. Please <a id='reRegisterNewUser' href='#' class='tooltip'>register here</a>.";

$lang ['customer_blacklist.add_success'] = 'The customer "%s" has been added.';
$lang ['customer_blacklist.add_error'] = 'The customer "%s" could not be added.';
$lang ['customer_blacklist.not_exist'] = 'The customer "%s" did not exist in database.';
$lang ['customer_blacklist.delete_success'] = 'The customer blacklist was deleted successfully.';
$lang ['customer_blacklist.exist_blacklist'] = 'The email "%s" exist in database.';

$lang['customer.charge_paypal_payment_warning'] = 'Please note that your deposit payment is lower than your open balance. Do you really want to continue?';
$lang['customer.duplicate_payone_payment_warning'] = 'You have been made payment request in 5 minutes. Please wait to continue made other payment.';
$lang['customer.check_for_identical_name_in_postbox_name_field'] = 'A postbox with the name " %s " is already in use at this location. To be able to receive mail addressed to you, please change your name in the name field to something similar to " %s " - you will also need have addressed postal mail to this name with extension so that it can be sorted into your account.';
$lang['customer.submit_check_for_identical_name_in_postbox_name_field'] = 'A postbox with the name " %s " is already in use at this location. To be able to receive mail addressed to you, please change your name in the name field to something similar to " %s ".';
$lang ['pending_envelope_scan_activity'] = 'Envelope scanning';
$lang ['pending_item_scan_activity'] = 'Scanning';
$lang ['pending_direct_shipping_activity'] = 'Shipping&Handling';
$lang ['pending_collect_shipping_activity'] = 'Shipping&Handling';

$lang ['verification_none_status']              = "None";
$lang ['verification_NA_status']                = "N.A";
$lang ['verification_incomplete_status']        = "Incomplete";
$lang ['verification_completed_status']         = "Completed";

$lang ['customer.credit_card']         = "Credit Card";
$lang ['customer.invoice_payment']         = "Invoice payment";
$lang ['customer.paypal']         = "Paypal";

$lang ['postbox.free']            = "AS YOU GO";
$lang ['postbox.private']         = "PRIVATE";
$lang ['postbox.business']        = "BUSINESS";
$lang ['postbox.enterprise']      = "ENTERPRISE";

$lang ['postbox.created']                       = 'Created';
$lang ['postbox.downgrade_ordered']             = 'Downgrade ordered';
$lang ['postbox.upgrade_ordered']               = 'Upgrade ordered';
$lang ['postbox.downgraded']                    = 'Downgraded';
$lang ['postbox.upgraded']                      = 'Upgraded';
$lang ['postbox.delete_ordered_by_customer']    = 'Delete by customer';
$lang ['postbox.delete_ordered_by_system']      = 'Delete by system';
$lang ['postbox.deleted']                       = 'Deleted';
$lang ['postbox.deactivated']                   = 'Deactivated';
$lang ['postbox.reactivated']                   = 'Reactivated';


/* End of file customer_lang.php */
