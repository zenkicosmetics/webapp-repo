<?php

defined('BASEPATH') or exit('No direct script access allowed');

class partner_api {

    public static function getPartnerNameAndAddress($caseID, $baseTaskname, $postbox_id) {
        ci()->load->model('partner/partner_m');
        ci()->load->model('mailbox/postbox_m');

        $row = ci()->partner_m->getPartnerNameAndAddress($caseID, $baseTaskname);
        $location = ci()->postbox_m->get_postbox_location_by($postbox_id);

        if ($location) {
            $p_location_street = $location->street;
            $p_location_postcode = $location->postcode;
            $p_location_city = $location->city;
            $p_location_country = $location->country_name;
        } else {
            $p_location_street = Settings::get(APConstants::INSTANCE_OWNER_STREET_CODE);
            $p_location_postcode = Settings::get(APConstants::INSTANCE_OWNER_PLZ_CODE);
            $p_location_city = Settings::get(APConstants::INSTANCE_OWNER_CITY_CODE);
            $p_location_country = Settings::get(APConstants::INSTANCE_OWNER_DIRECTOR_CODE);
        }

        if (!empty($row) && $row->cmra > 0 && $row->clevvermail_flag != APConstants::ON_FLAG) {
            $p_company_name = $row->company_name;
        } else {
            $p_company_name = Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE);
        }

        $partner = array();
        $partner['p_location_street'] = $p_location_street;
        $partner['p_location_postcode'] = $p_location_postcode;
        $partner['p_location_city'] = $p_location_city;
        $partner['p_location_country'] = $p_location_country;
        $partner['company_name'] = $p_company_name;

        return $partner;
    }

    public static function getPartnerAll() {

        ci()->load->model('partner/partner_m');
        return ci()->partner_m->get_all();
    }

    public static function deletePartner($partnerID) {

        ci()->load->model('cases/cases_milestone_m');
        ci()->load->model('partner/partner_m');
        ci()->load->model('partner/partner_marketing_profile_m');
        ci()->lang->load('partner/partner');

        $result = array();
        $cases_milestone = ci()->cases_milestone_m->get_many_by_many(array(
            "( (partner_id = '" . $partnerID . "')  OR  (cmra = '" . $partnerID . "')  )" => null
        ));
        if (count($cases_milestone)) {
            $message = lang('delete_partner_exist_cases_milestone') . $cases_milestone[0]->milestone_name;
            $result['status'] = false;
            $result['message'] = $message;
            return $result;
        }

        ci()->db->trans_begin();
        ci()->partner_m->delete_by("partner_id", $partnerID);
        ci()->partner_marketing_profile_m->delete_by("partner_id", $partnerID);
        if (ci()->db->trans_status() === FALSE) {
            ci()->db->trans_rollback();
            $message = lang('delete_partner_error');
            $result['status'] = false;
            $result['message'] = $message;
        } else {
            ci()->db->trans_commit();
            $message = lang('delete_partner_success');
            $result['status'] = true;
            $result['message'] = $message;
        }

        return $result;
    }

    public static function getPartnerCodeByCustomer($customerID) {
        ci()->load->model('partner/partner_m');

        $partner = ci()->partner_m->get_partner_code_by_customer($customerID);

        return $partner;
    }

    /**
     * Gets partner martkerting profile by partner id
     * @param unknown $partner_id
     */
    public static function getPartnerMarketingProfileById($partner_id) {
        ci()->load->model('partner/partner_marketing_profile_m');
        $profile = ci()->partner_marketing_profile_m->get_by("partner_id", $partner_id);

        return $profile;
    }

    /**
     *
     * @param unknown $partner_id
     * @param unknown $script
     */
    public static function updatePartnerMarketingSessionCatch($partner_id, $script) {
        ci()->load->model('partner/partner_marketing_profile_m');

        ci()->partner_marketing_profile_m->update_by_many(array(
            "partner_id" => $partner_id
                ), array(
            "session_catch" => $script
        ));
    }

    /**
     *
     * @param unknown $partner_id
     * @param unknown $script
     */
    public static function updatePartnerMarketingLandingPage($partner_id, $script) {
        ci()->load->model('partner/partner_marketing_profile_m');

        ci()->partner_marketing_profile_m->update_by_many(array(
            "partner_id" => $partner_id
                ), array(
            "script_landing_page" => $script
        ));
    }

    /**
     *
     * @param unknown $partner_id
     * @param unknown $script
     */
    public static function updatePartnerMarketingRegistrationWidget($partner_id, $script) {
        ci()->load->model('partner/partner_marketing_profile_m');

        ci()->partner_marketing_profile_m->update_by_many(array(
            "partner_id" => $partner_id
                ), array(
            "script_widget" => $script
        ));
    }

    /**
     * update data reporting of partner.
     */
    public static function updatePartnerMarketingReport($selected_partner, $report_month) {
        ci()->load->model(array(
            'report/report_by_partner_m',
            "partner/partner_marketing_profile_m",
            'partner/partner_customer_m',
        ));

        ci()->load->library(array(
            "report/report_api",
        ));

        if (empty($selected_partner) || empty($report_month)) {
            return;
        }

        // reset the customer that end of MP. we will reset MP flag on first day of month.
        //if(APUtils::isFirstDayOfMonth()){
        $customers = ci()->partner_customer_m->get_many_by_many(array(
            "partner_id" => $selected_partner,
            "end_flag" => APConstants::OFF_FLAG
        ));
        $list_reset_customer = array();
        foreach ($customers as $c) {
            $create_ym = date("Ym", $c->created_date);
            $duration = $c->duration_customer_discount;
            $target_date = strtotime("-{$duration} month");
            $target_ym = date("Ym", $target_date);
            if ($target_ym > $create_ym) {
                $list_reset_customer[] = $c->customer_id;
            }
        }

        if (!empty($list_reset_customer)) {
            ci()->partner_customer_m->update_by_many(array(
                "partner_id" => $selected_partner,
                "end_flag" => APConstants::OFF_FLAG,
                "customer_id IN ('" . implode("','", $list_reset_customer) . "')" => null
                    ), array(
                "end_flag" => APConstants::ON_FLAG,
            ));
        }
        ob_flush();
        flush();
        //}
        // get partner profile
        $profile = ci()->partner_marketing_profile_m->get_by("partner_id", $selected_partner);

        /// get partner data.
        $partner_datas = report_api::getMarkettingReportDatas($report_month, $selected_partner);

        $data = array();
        $data['invoice_month'] = $report_month;
        $data['partner_id'] = $selected_partner;

        // check number of account, postbox, envelope.
        $number_customer = $partner_datas['number_customers'];
        $number_of_envelope = $partner_datas['numberEnvelopes'];

        $data['number_of_account'] = $number_customer['number_of_account'];
        $data['number_of_postbox'] = $partner_datas['numberPostboxs']['total'];
        $data['number_of_item_received'] = $number_of_envelope['received_num'];
        $data['number_of_envelope_scan'] = $number_of_envelope['envelope_scanned_num'];
        $data['number_of_item_scan'] = $number_of_envelope['item_scanned_num'];
        $data['number_of_item_forwarded'] = $number_of_envelope['forwarded_num'];
        $data['number_of_storage_item'] = $number_of_envelope['storage_num'];
        $data['number_of_new_registration'] = $number_customer['new_registration'];
        $data['number_of_never_activated_deleted'] = $number_customer['number_never_activated_deleted'];
        $data['number_of_manual_deleted'] = $number_customer['number_manually_deleted'];
        $data['number_of_automatic_deleted'] = $number_customer['number_automatic_deleted'];
        $data['number_of_customers'] = $number_customer['number_of_customer'];

        $data['free_postbox_quantity'] = $partner_datas['numberPostboxs']['number_free'];
        $data['private_postbox_quantity'] = $partner_datas['numberPostboxs']['number_private'];
        $data['business_postbox_quantity'] = $partner_datas['numberPostboxs']['number_business'];

        // check manual invoice
        $manual_invoice = $partner_datas['manualInvoices'];

        // other manual invoice.
        $data['cash_payment_free_for_item_delivery_amount'] = $manual_invoice['cash_payment_free_for_item_delivery']->total_amount;
        $data['cash_payment_free_for_item_delivery_amount_discount'] = $manual_invoice['cash_payment_free_for_item_delivery']->discount_total;
        $data['cash_payment_free_for_item_delivery_amount_share'] = $manual_invoice['cash_payment_free_for_item_delivery']->rev_share_total;
        $data['cash_payment_fee_quantity'] = $manual_invoice['cash_payment_free_for_item_delivery']->quantity;

        $data['customs_cost_import_amount'] = $manual_invoice['customs_cost_import']->total_amount;
        $data['customs_cost_import_amount_discount'] = $manual_invoice['customs_cost_import']->discount_total;
        $data['customs_cost_import_amount_share'] = $manual_invoice['customs_cost_import']->rev_share_total;
        $data['custom_cost_import_quantity'] = $manual_invoice['customs_cost_import']->quantity;

        $data['customs_handling_fee_import_amount'] = $manual_invoice['customs_handling_fee_import']->total_amount;
        $data['customs_handling_fee_import_amount_discount'] = $manual_invoice['customs_handling_fee_import']->discount_total;
        $data['customs_handling_fee_import_amount_share'] = $manual_invoice['customs_handling_fee_import']->rev_share_total;
        $data['import_custom_fee_quantity'] = $manual_invoice['customs_handling_fee_import']->quantity;

        $data['address_verification_amount'] = $manual_invoice['address_verification']->total_amount;
        $data['address_verification_amount_discount'] = $manual_invoice['address_verification']->discount_total;
        $data['address_verification_amount_share'] = $manual_invoice['address_verification']->rev_share_total;
        $data['address_verification_quantity'] = $manual_invoice['address_verification']->quantity;

        $data['special_service_fee_in_15min_intervalls_amount'] = $manual_invoice['special_service_fee_in_15min_intervalls']->total_amount;
        $data['special_service_fee_in_15min_intervalls_amount_discount'] = $manual_invoice['special_service_fee_in_15min_intervalls']->discount_total;
        $data['special_service_fee_in_15min_intervalls_amount_share'] = $manual_invoice['special_service_fee_in_15min_intervalls']->rev_share_total;
        $data['special_service_fee_quantity'] = $manual_invoice['special_service_fee_in_15min_intervalls']->quantity;

        $data['personal_pickup_charge_amount'] = $manual_invoice['personal_pickup_charge']->total_amount;
        $data['personal_pickup_charge_amount_discount'] = $manual_invoice['personal_pickup_charge']->discount_total;
        $data['personal_pickup_charge_amount_share'] = $manual_invoice['personal_pickup_charge']->rev_share_total;
        $data['peronsal_pickup_charge_quantity'] = $manual_invoice['personal_pickup_charge']->quantity;

        // credit note
        $credit_note = $partner_datas['credit_note'];
        $data['credit_note_given'] = $credit_note->total_amount;
        $data['credit_note_given_discount'] = $credit_note->discount_total;
        $data['credit_note_given_share'] = $credit_note->rev_share_total;
        $data['creditnote_quantity'] = $credit_note->quantity;

        // other local invoice.
        $other_invoice = $partner_datas['other_invoice'];
        $data['other_local_invoice'] = $other_invoice->total_amount;
        $data['other_local_invoice_discount'] = $other_invoice->discount_total;
        $data['other_local_invoice_share'] = $other_invoice->rev_share_total;
        $data['other_local_invoice_quantity'] = $other_invoice->quantity;

        // paypal fee
        $paypal = $partner_datas['paypalFee'];
        $data['paypal_fee'] = $paypal['total']->total_invoice;
        $data['paypal_fee_discount'] = $paypal['total']->discount_total;
        $data['paypal_fee_share'] = $paypal['total']->rev_share_total;
        $data['paypal_transaction_fee_quantity'] = $paypal['quantity'];

        // forwarding charge 
        $forwarding_charge = $partner_datas['forwardingCharges'];
        $data['forwarding_charges_postal'] = $forwarding_charge->forwarding_charges_postal;
        $data['forwarding_charges_fee'] = $forwarding_charge->forwarding_charges_fee;
        $data['forwarding_charges_postal_discount'] = $forwarding_charge->forwarding_charges_fee_discount_total;
        $data['forwarding_charges_fee_discount'] = $forwarding_charge->forwarding_charges_postal_rev_discount_total;
        $data['forwarding_charges_postal_share'] = $forwarding_charge->forwarding_charges_fee_share_total;
        $data['forwarding_charges_fee_share'] = $forwarding_charge->forwarding_charges_postal_rev_share_total;

        // get invoice summary
        $invoice_summary = $partner_datas['invoice_summary'];

        // total column
        $data['free_postboxes_amount'] = $invoice_summary->free_postboxes_amount;
        $data['private_postboxes_amount'] = $invoice_summary->private_postboxes_amount;
        $data['business_postboxes_amount'] = $invoice_summary->business_postboxes_amount;

        $data['incomming_items_free_account'] = $invoice_summary->incomming_items_free_account;
        $data['incomming_items_private_account'] = $invoice_summary->incomming_items_private_account;
        $data['incomming_items_business_account'] = $invoice_summary->incomming_items_business_account;

        $data['envelope_scan_free_account'] = $invoice_summary->envelope_scan_free_account;
        $data['envelope_scan_private_account'] = $invoice_summary->envelope_scan_private_account;
        $data['envelope_scan_business_account'] = $invoice_summary->envelope_scan_business_account;

        $data['item_scan_free_account'] = $invoice_summary->item_scan_free_account;
        $data['item_scan_private_account'] = $invoice_summary->item_scan_private_account;
        $data['item_scan_business_account'] = $invoice_summary->item_scan_business_account;

        $data['storing_letters_free_account'] = $invoice_summary->storing_letters_free_account;
        $data['storing_letters_private_account'] = $invoice_summary->storing_letters_private_account;
        $data['storing_letters_business_account'] = $invoice_summary->storing_letters_business_account;

        $data['storing_packages_free_account'] = $invoice_summary->storing_packages_free_account;
        $data['storing_packages_private_account'] = $invoice_summary->storing_packages_private_account;
        $data['storing_packages_business_account'] = $invoice_summary->storing_packages_business_account;

        $data['additional_pages_scanning_free_amount'] = $invoice_summary->additional_pages_scanning_free_amount;
        $data['additional_pages_scanning_private_amount'] = $invoice_summary->additional_pages_scanning_private_amount;
        $data['additional_pages_scanning_business_amount'] = $invoice_summary->additional_pages_scanning_business_amount;

        // discount column.
        $data['free_postboxes_amount_discount'] = $invoice_summary->free_postboxes_amount_discount;
        $data['private_postboxes_amount_discount'] = $invoice_summary->private_postboxes_amount_discount;
        $data['business_postboxes_amount_discount'] = $invoice_summary->business_postboxes_amount_discount;

        $data['incomming_items_free_account_discount'] = $invoice_summary->incomming_items_free_account_discount;
        $data['incomming_items_private_account_discount'] = $invoice_summary->incomming_items_private_account_discount;
        $data['incomming_items_business_account_discount'] = $invoice_summary->incomming_items_business_account_discount;

        $data['envelope_scan_free_account_discount'] = $invoice_summary->envelope_scan_free_account_discount;
        $data['envelope_scan_private_account_discount'] = $invoice_summary->envelope_scan_private_account_discount;
        $data['envelope_scan_business_account_discount'] = $invoice_summary->envelope_scan_business_account_discount;

        $data['item_scan_free_account_discount'] = $invoice_summary->item_scan_free_account_discount;
        $data['item_scan_private_account_discount'] = $invoice_summary->item_scan_private_account_discount;
        $data['item_scan_business_account_discount'] = $invoice_summary->item_scan_business_account_discount;

        $data['storing_letters_free_account_discount'] = $invoice_summary->storing_letters_free_account_discount;
        $data['storing_letters_private_account_discount'] = $invoice_summary->storing_letters_private_account_discount;
        $data['storing_letters_business_account_discount'] = $invoice_summary->storing_letters_business_account_discount;

        $data['storing_packages_free_account_discount'] = $invoice_summary->storing_packages_free_account_discount;
        $data['storing_packages_private_account_discount'] = $invoice_summary->storing_packages_private_account_discount;
        $data['storing_packages_business_account_discount'] = $invoice_summary->storing_packages_business_account_discount;

        $data['additional_pages_scanning_free_amount_discount'] = $invoice_summary->additional_pages_scanning_free_amount_discount;
        $data['additional_pages_scanning_private_amount_discount'] = $invoice_summary->additional_pages_scanning_private_amount_discount;
        $data['additional_pages_scanning_business_amount_discount'] = $invoice_summary->additional_pages_scanning_business_amount_discount;

        // share column.
        $data['free_postboxes_amount_share'] = $invoice_summary->free_postboxes_amount_share;
        $data['private_postboxes_amount_share'] = $invoice_summary->private_postboxes_amount_share;
        $data['business_postboxes_amount_share'] = $invoice_summary->business_postboxes_amount_share;

        $data['incomming_items_free_account_share'] = $invoice_summary->incomming_items_free_account_share;
        $data['incomming_items_private_account_share'] = $invoice_summary->incomming_items_private_account_share;
        $data['incomming_items_business_account_share'] = $invoice_summary->incomming_items_business_account_share;

        $data['envelope_scan_free_account_share'] = $invoice_summary->envelope_scan_free_account_share;
        $data['envelope_scan_private_account_share'] = $invoice_summary->envelope_scan_private_account_share;
        $data['envelope_scan_business_account_share'] = $invoice_summary->envelope_scan_business_account_share;

        $data['item_scan_free_account_share'] = $invoice_summary->item_scan_free_account_share;
        $data['item_scan_private_account_share'] = $invoice_summary->item_scan_private_account_share;
        $data['item_scan_business_account_share'] = $invoice_summary->item_scan_business_account_share;

        $data['storing_letters_free_account_share'] = $invoice_summary->storing_letters_free_account_share;
        $data['storing_letters_private_account_share'] = $invoice_summary->storing_letters_private_account_share;
        $data['storing_letters_business_account_share'] = $invoice_summary->storing_letters_business_account_share;

        $data['storing_packages_free_account_share'] = $invoice_summary->storing_packages_free_account_share;
        $data['storing_packages_private_account_share'] = $invoice_summary->storing_packages_private_account_share;
        $data['storing_packages_business_account_share'] = $invoice_summary->storing_packages_business_account_share;

        $data['additional_pages_scanning_free_amount_share'] = $invoice_summary->additional_pages_scanning_free_amount_share;
        $data['additional_pages_scanning_private_amount_share'] = $invoice_summary->additional_pages_scanning_private_amount_share;
        $data['additional_pages_scanning_business_amount_share'] = $invoice_summary->additional_pages_scanning_business_amount_share;

        // quantity column.
        // $data['number_of_postbox']                      =	$invoice_summary->free_postboxes_quantity + $invoice_summary->private_postboxes_quantity + $invoice_summary->business_postboxes_quantity;
        $data['free_postbox_quantity'] = $invoice_summary->free_postboxes_quantity;
        $data['private_postbox_quantity'] = $invoice_summary->private_postboxes_quantity;
        $data['business_postbox_quantity'] = $invoice_summary->business_postboxes_quantity;

        $data['free_incoming_quantity'] = $invoice_summary->incomming_items_free_quantity;
        $data['private_incoming_quantity'] = $invoice_summary->incomming_items_private_quantity;
        $data['business_incoming_item_quantity'] = $invoice_summary->incomming_items_business_quantity;

        $data['free_envelope_scan_quantity'] = $invoice_summary->envelope_scan_free_quantity;
        $data['private_envelope_scan_quantity'] = $invoice_summary->envelope_scan_private_quantity;
        $data['business_envelope_scan_quantity'] = $invoice_summary->envelope_scan_business_quantity;

        $data['free_item_scan_quantity'] = $invoice_summary->item_scan_free_quantity;
        $data['private_item_scan_quantity'] = $invoice_summary->item_scan_private_quantity;
        $data['business_item_scan_quantity'] = $invoice_summary->item_scan_business_quantity;

        $data['free_storage_letter_quanity'] = $invoice_summary->storing_letters_free_quantity;
        $data['private_storage_letter_quanity'] = $invoice_summary->storing_letters_private_quantity;
        $data['business_storage_letter_quanity'] = $invoice_summary->storing_letters_business_quantity;

        $data['free_storage_package_quanity'] = $invoice_summary->storing_packages_free_quantity;
        $data['private_storage_package_quanity'] = $invoice_summary->storing_packages_private_quantity;
        $data['business_storage_package_quanity'] = $invoice_summary->storing_packages_business_quantity;

        $data['custom_declaration_outgoing_price_01'] = $invoice_summary->custom_declaration_outgoing_price_01;
        $data['custom_declaration_outgoing_price_02'] = $invoice_summary->custom_declaration_outgoing_price_02;
        $data['custom_declaration_quantity'] = $invoice_summary->custom_declaration_outgoing_quantity_01 + $invoice_summary->custom_declaration_outgoing_quantity_02;

        $data['fowarding_charge_postal_quantity'] = $invoice_summary->direct_shipping_quantity;
        $data['fowarding_charge_fee_quantity'] = $invoice_summary->collect_shipping_quantity;

        // profile field.
        $data['rev_share'] = $profile->rev_share_ad;
        $data['duration_rev_share'] = $profile->duration_rev_share;
        $data['customer_discount'] = $profile->customer_discount;
        $data['commission_for_registration'] = ci()->partner_customer_m->sum_by_many(array("partner_id" => $selected_partner), 'comission_for_registration');
        $data['commission_for_activation'] = ci()->partner_customer_m->sum_by_many(array("partner_id" => $selected_partner), 'comission_for_activation');
        $data['duration_customer_discount'] = $profile->duration_customer_discount;

        // Gets invoice.
        $report_check = ci()->report_by_partner_m->get_by_many(array(
            "invoice_month" => $report_month,
            "partner_id" => $selected_partner
        ));
        if (empty($report_check)) {
            ci()->report_by_partner_m->insert($data);
        } else {
            ci()->report_by_partner_m->update_by_many(array(
                "invoice_month" => $report_month,
                "partner_id" => $selected_partner
                    ), $data);
        }

        // done.
    }

    /**
     * #1296 add receipt scan/upload to receipts
     * upload resource file of Receipt
     * 
     * function uploadResourceReceipt
     * 
     * @param type $type
     * @param type $input_file_client_name
     * 
     * @return type  array(
      "status" => $status,
      "message" => $message,
      'local_file_path' => $path
      );
     */
    public static function uploadResourceReceipt($type, $client_file_name) {
        // Load library
        ci()->load->library('files/files');

        // Check type 
        if ($type == 'partner_receipt') {
            // Defined server file path 
            $server_file_path = "uploads/temp/";

            // Get local file upload information 
            $upload_info = Files::upload_file($server_file_path, $client_file_name);

            // return local file upload infornamtion 
            return $upload_info;
        }
    }

}
