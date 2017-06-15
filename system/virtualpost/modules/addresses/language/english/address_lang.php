<?php defined('BASEPATH') OR exit('No direct script access allowed');

$lang['location_name'] = 'Location Name';
$lang['street'] = 'Street';
$lang['postcode'] = 'Post Code';
$lang['city'] = 'City';
$lang['region'] = 'Region';
$lang['country'] = 'Country';
$lang['image_path'] = 'Image Path';
$lang['pricing_template_id'] = "Pricing Template";
$lang['shipping_factor_fl'] = 'Shipping Factor FL';
$lang['language'] = "Language";
$lang['business_postbox_text'] = "Business postbox text";
$lang['booking_email_address'] = "Booking email address";
$lang['phone_number'] = "Phone number";

$lang['add_location_success'] = 'The location has been created successfully.';
$lang['edit_location_success'] = 'The location has been updated successfully.';
$lang['save_vat_success'] = 'The VAT number has been updated successfully.';
$lang['remove_vat_success'] = 'The VAT number has been removed successfully.';
$lang['delete_location_success'] = 'The location has been deleted successfully.';
$lang['delete_location_fail'] = 'Do you want to delete all postboxes from this location?';
$lang['change_address_setting_success'] = 'Shipping and Invoice address have been updated successfully.';
$lang['change_invoice_address_setting_success'] = 'Invoice address have been updated successfully.';
$lang['change_postbox_address_success'] = 'Postbox address has been updated successfully.';

$lang['change_postbox_info_message'] = 'Your postbox type %s will be changed to %s on %s.';
$lang ['change_my_postbox_type_success'] = 'Your postbox type %s was changed successfully.';
$lang ['postbox_type_1'] = 'AS YOU GO';
$lang ['postbox_type_2'] = 'PRIVATE';
$lang ['postbox_type_3'] = 'BUSINESS';

$lang ['shipment_address_name'] = 'shipment address name';
$lang ['shipment_company'] = 'shipment company';
$lang ['shipment_street'] = 'shipment street';
$lang ['shipment_postcode'] = 'shipment postcode';
$lang ['shipment_city'] = 'shipment city';
$lang ['shipment_region'] = 'shipment region';
$lang ['shipment_country'] = 'shipment country';
$lang ['shipment_phone_number'] = 'shipment phone number';

$lang ['manage_forward_address_success'] = 'Forward address has been updated successfully.';
$lang ['new_forward_address_success'] = 'Create new forward address successfully.';


$lang ['invoicing_address_name'] = 'invoicing address name';
$lang ['invoicing_company'] = 'invoicing company';
$lang ['invoicing_street'] = 'invoicing street';
$lang ['invoicing_postcode'] = 'invoicing postcode';
$lang ['invoicing_city'] = 'invoicing city';
$lang ['invoicing_region'] = 'invoicing region';
$lang ['invoicing_country'] = 'invoicing country';
$lang ['invoicing_phone_number'] = 'invoicing phone number';

$lang ['addresses.shipment_company_required'] = 'Shipment address name or shipment address company is required input.';
$lang ['addresses.invoicing_company_required'] = 'Invoicing address name or invoicing address company is required input.';

$lang['image_location_invalid'] = 'Please select PNG file or JPG file to upload.';

$lang['error_company_same_name'] = 'Only fill company field with your company name. if you are no company, please leave this field empty.';

$lang['apply_new_pricing_template_success'] = 'The new pricing template will be applied for next month';

$lang['shpping_address_is_using'] = 'Your address is using for shipping items';
$lang['delete_alternative_address_success'] = 'Delete alternative address have been successfully';
$lang['delete_alternative_address_error'] = 'Delete alternative address error.Please contact administrator to resolve';

$lang['change_pricing_template_confirm_1'] = 'Do you want to change pricing template to ';
$lang['change_pricing_template_confirm_2'] = ' in the next month?';
$lang['change_pricing_template_success'] = 'The new pricing template will be applied for next month.';
$lang['confirm_pricing_template'] = 'Confirm Pricing Template';
$lang['success_infomation'] = 'Success information';


/* Lang for system\virtualpost\modules\addresses\views\admin\prices.php */
$lang['postbox_fee.label'] = 'Postbox Price';
$lang['postbox_fee.dimension'] = 'EUR/month';

$lang['as_you_go_fee.label'] = 'AS YOU GO fee';
$lang['as_you_go_fee.dimension'] = 'EUR/month';

$lang['as_you_go_duration.label'] = 'AS YOU GO duration';
$lang['as_you_go_duration.dimension'] = 'Day(s)';

$lang['included_incoming_items.label'] = 'Included incoming items';
$lang['included_incoming_items.dimension'] = 'Pieces';

$lang['storage.label'] = 'Storage';
$lang['storage.dimension'] = 'GB';

$lang['hand_sorting_of_advertising.label'] = 'Hand sorting of advertising ';
$lang['hand_sorting_of_advertising.dimension'] = 'No/Yes';

$lang['envelope_scanning_front.label'] = 'Envelope scanning (front)';
$lang['envelope_scanning_front.dimension'] = 'Pieces';

$lang['included_opening_scanning.label'] = 'Item scan';
$lang['included_opening_scanning.dimension'] = 'Pieces';

$lang['storing_items_letters.label'] = 'Storing items free period (letters)';
$lang['storing_items_letters.dimension'] = 'Days';

$lang['storing_items_packages.label'] = 'Storing items free period (packages)';
$lang['storing_items_packages.dimension'] = 'Days';

$lang['storing_items_digitally.label'] = 'Storing items digitally';
$lang['storing_items_digitally.dimension'] = 'Years';

$lang['trashing_items.label'] = 'Trashing items';
$lang['trashing_items.dimension'] = 'Pieces';

$lang['cloud_service_connection.label'] = 'Cloud service connection';
$lang['cloud_service_connection.dimension'] = 'No/Yes';

$lang['additional_incomming_items.label'] = 'Additional incoming items';
$lang['additional_incomming_items.dimension'] = 'EUR';

$lang['envelop_scanning.label'] = 'Envelope scanning';
$lang['envelop_scanning.dimension'] = 'EUR';


$lang['opening_scanning.label'] = 'Opening and scanning';
$lang['opening_scanning.dimension'] = 'EUR';

$lang['send_out_directly.label'] = 'Direct forwarding fee (charge per incident)';
$lang['send_out_directly.dimension'] = 'EUR';

$lang['shipping_plus.label'] = 'Direct forwarding fee (charge based on postal charge)';
$lang['shipping_plus.dimension'] = 'Percentage';

$lang['send_out_collected.label'] = 'Collect forwarding(charge per incident)';
$lang['send_out_collected.dimension'] = 'EUR';

$lang['collect_shipping_plus.label'] = 'Collect forwarding (charge based on postal charge)';
$lang['collect_shipping_plus.dimension'] = 'Percentage';

$lang['storing_items_over_free_letter.label'] = 'Fee storage period (papermail)';
$lang['storing_items_over_free_letter.dimension'] = 'EUR/day';


$lang['storing_items_over_free_packages.label'] = 'Fee storage period (parcels)';
$lang['storing_items_over_free_packages.dimension'] = 'EUR/day';

$lang['paypal_transaction_fee.label'] = 'Paypal transaction fee';
$lang['paypal_transaction_fee.dimension'] = 'Percentage';

$lang['additional_included_page_opening_scanning.label'] = 'Included pages for opening and scanning';
$lang['additional_included_page_opening_scanning.dimension'] = 'Pieces';


$lang['custom_declaration_outgoing_01.label'] = 'Customs declaration outgoing (value >1000 EUR)';
$lang['custom_declaration_outgoing_01.dimension'] = 'EUR';

$lang['custom_declaration_outgoing_02.label'] = 'Customs declaration outgoing (value <1000 EUR)';
$lang['custom_declaration_outgoing_02.dimension'] = 'EUR';

$lang['custom_handling_import.label'] = 'Customs handling import';
$lang['custom_handling_import.dimension'] = 'percentage on occuring cost';

$lang['cash_payment_on_delivery_percentage.label'] = 'Cash payment for item on delivery or cash expenditure (percentage)';
$lang['cash_payment_on_delivery_percentage.dimension'] = 'percentage';

$lang['cash_payment_on_delivery_mini_cost.label'] = 'Cash payment for item on delivery or cash expenditure (minimum cost)';
$lang['cash_payment_on_delivery_mini_cost.dimension'] = 'EUR';

$lang['official_address_verification.label'] = 'Official address verification ';
$lang['official_address_verification.dimension'] = 'EUR';

$lang['pickup_charge.label'] = 'Pickup charge (only with confirmed appointment)';
$lang['pickup_charge.dimension'] = 'EUR';

$lang['additional_pages_scanning_price.label'] = 'scan of additional pages';
$lang['additional_pages_scanning_price.dimension'] = 'EUR';

$lang['special_requests_charge_by_time.label'] = 'Special requests';
$lang['special_requests_charge_by_time.dimension'] = 'EUR/hour';

$lang['name_on_the_door.label'] = 'Name plate at the entrance';
$lang['name_on_the_door.dimension'] = 'No/Yes';

$lang['lease_of_workplace_for_own_location_monthly.label'] = "Lease of workplace for own location – monthly";
$lang['lease_of_workplace_for_own_location_quarterly.label'] = "Lease of workplace for own location – quarterly";
$lang['lease_of_workplace_for_own_location_yearly.label'] = "Lease of workplace for own location – yearly";
$lang['lease_of_workplace_for_own_location_monthly.dimension'] = 'EUR/month';
$lang['lease_of_workplace_for_own_location_quarterly.dimension'] = 'EUR/month';
$lang['lease_of_workplace_for_own_location_yearly.dimension'] = 'EUR/month';

$lang['lease_of_workplace_for_clevverMail_location_monthly.label'] = "Lease of workplace for own location – monthly";
$lang['lease_of_workplace_for_clevverMail_location_quarterly.label'] = "Lease of workplace for own location – quarterly";
$lang['lease_of_workplace_for_clevverMail_location_yearly.label'] = "Lease of workplace for own location – yearly";
$lang['lease_of_workplace_for_clevverMail_location_monthly.dimension'] = 'EUR/month';
$lang['lease_of_workplace_for_clevverMail_location_quarterly.dimension'] = 'EUR/month';
$lang['lease_of_workplace_for_clevverMail_location_yearly.dimension'] = 'EUR/month';

$lang['lease_of_receptionist_own_location_monthly.label'] = "Lease of receptionist own location - monthly";
$lang['lease_of_receptionist_own_location_quarterly.label'] = "Lease of receptionist own location - quarterly";
$lang['lease_of_receptionist_own_location_yearly.label'] = "Lease of receptionist own location - yearly";
$lang['lease_of_receptionist_own_location_monthly.dimension'] = 'EUR/month';
$lang['lease_of_receptionist_own_location_quarterly.dimension'] = 'EUR/month';
$lang['lease_of_receptionist_own_location_yearly.dimension'] = 'EUR/month';

$lang['lease_of_receptionist_clevverMail_location_monthly.label'] = "Lease of receptionist ClevverMail location - monthly";
$lang['lease_of_receptionist_clevverMail_location_quarterly.label'] = "Lease of receptionist ClevverMail location - quarterly";
$lang['lease_of_receptionist_clevverMail_location_yearly.label'] = "Lease of receptionist ClevverMail location - yearly";
$lang['lease_of_receptionist_clevverMail_location_monthly.dimension'] = 'EUR/month';
$lang['lease_of_receptionist_clevverMail_location_quarterly.dimension'] = 'EUR/month';
$lang['lease_of_receptionist_clevverMail_location_yearly.dimension'] = 'EUR/month';

$lang['own_location_monthly.label'] = "Own location - monthly";
$lang['touch_panel_at_own_location_quarterly.label'] = "Touch Panel at own location - quarterly";
$lang['own_location_monthly.dimension'] = 'EUR/month';
$lang['touch_panel_at_own_location_quarterly.dimension'] = 'EUR/month';

$lang['own_mobile_app_monthly.label'] = "Own mobile app - monthly";
$lang['own_mobile_app_monthly.dimension'] = 'EUR/month';

$lang['api_access.label'] = "API Access";
$lang['api_access.dimension'] = 'EUR/month';

$lang['clevver_subdomain.label'] = "Clevver Subdomain";
$lang['clevver_subdomain.dimension'] = 'EUR/month';

$lang['own_domain.label'] = "Own Domain";
$lang['own_domain.dimension'] = 'EUR/month';
