<?php defined('BASEPATH') or exit('No direct script access allowed');

class report_api
{
    /**
     * Gets receipt of partner report by location.
     */
    public static function getPartnerReceiptReportByLocation($partnerId, $receiptMonth)
    {
        ci()->load->model("report/partner_receipt_m");

        if(!empty($partnerId)){
            $otherCashExpenditure = ci()->partner_receipt_m->sum_by_many(array(
                "partner_id" => $partnerId,
                "substr(date_of_receipt, 3, 7) = '{$receiptMonth}'" => null
            ), 'net_amount');
        }else{
            $otherCashExpenditure = ci()->partner_receipt_m->sum_by_many(array(
                "substr(date_of_receipt, 3, 7) = '{$receiptMonth}'" => null
            ), 'net_amount');
        }

        return $otherCashExpenditure;
    }
    
    /**
     * Get datas for location report.
     * 
     * @param type $targetMonth
     * @param type $location_id
     */
    public static function getLocationReportDatas($report_month, $location_id, $partner_id, $receiptMonth){
         ci()->load->model(array(
            'report/report_m',
            
        ));
        ci()->load->library(array(
            'price/price_api',
            'mailbox/mailbox_api',
            "customers/customers_api",
            "scans/scans_api",
            "invoices/invoices_api",
            "payment/payment_api",
        ));
        
        // result.
        $result = array();
        
        // begin transaction.
        ci()->report_m->db->trans_begin();
        
        // get pricing model.
        $result['price_postboxes'] = price_api::getPricingModelByLocationID($location_id);
        $result['rev_price_postboxes'] = price_api::getRevShareMapByLocationID($location_id);
        
        // get customer registered
        $result['number_customers'] = mailbox_api::countCustomersRegistrationByMonth($report_month, $location_id);
        
        $result['numberPostboxs'] = mailbox_api::countPostboxesRegistrationByMonth($report_month, $location_id, false);
        $result['numberPostboxsShare'] = mailbox_api::countPostboxesRegistrationByMonth($report_month, $location_id, true);
        
        $result['numberEnvelopes'] = scans_api::countEnvelopesByMonth($report_month, $location_id, false);
        $result['numberEnvelopesShare'] = scans_api::countEnvelopesByMonth($report_month, $location_id, true);
        
        $result['invoice_summary'] = invoices_api::getInvoiceSummaryByLocationByMonth($location_id, $report_month, false);
        $result['invoice_summary_share'] = invoices_api::getInvoiceSummaryByLocationByMonth($location_id, $report_month, true);
        
        $result['postbox_fee_share'] = invoices_api::getPostboxFeeByLocation($location_id, $report_month, true);
        
        // Using for manual invoice report
        $result['manualInvoices'] = invoices_api::countSummaryManualInvoice($report_month, $location_id);
        $result['manualInvoicesShare'] = invoices_api::countSummaryManualInvoice($report_month, $location_id, true);
        
        //$result['otherLocalInvoice'] = invoices_api::sumOtherLocalInvoice($report_month, $location_id);
        //$result['otherLocalInvoiceShare'] = invoices_api::sumOtherLocalInvoice($report_month, $location_id, true);
        
        $result['credit_note'] = invoices_api::getCreditNoteByLocationByMonth($location_id, $report_month);
        $result['credit_note_share'] = invoices_api::getCreditNoteByLocationByMonth($location_id, $report_month, true);
        
        $result['forwardingCharges'] = scans_api::summaryForwardingEnvelopesByLocation($location_id, $report_month);
        $result['forwardingChargesShare'] = scans_api::summaryForwardingEnvelopesByLocation($location_id, $report_month, true);
        
        // paypal fee
        $result['paypalFee'] = payment_api::getPaypalFeeByLocation($location_id, $report_month);
        
        // get other cash
        $result['otherCashExpenditure'] = report_api::getPartnerReceiptReportByLocation($partner_id, $receiptMonth);
        
        // Gets rev share with location for other local invoice and credit note given
        $result['rev_share'] = price_api::getRevShareOfLocation($location_id);
        
        // commit transaction
        if(ci()->report_m->db->trans_status() == FALSE){
            ci()->report_m->db->trans_rollback();
        }else{
            ci()->report_m->db->trans_commit();
        }
        
        return $result;
    }
    
    /**
     * Get datas for location report.
     * 
     * @param type $targetMonth
     * @param type $location_id
     */
    public static function getMarkettingReportDatas($report_month, $partner_id){
        ci()->load->model(array(
            'report/report_m',
            "customers/customer_m",
            'mailbox/postbox_m',
            'scans/envelope_m',
            'scans/envelope_storage_month_m',
            'invoices/invoice_summary_by_location_m',
            'invoices/invoice_summary_m',
            'scans/envelope_shipping_m',
        ));
        
        // result.
        $result = array();
        
        // begin transaction.
        ci()->report_m->db->trans_begin();
        
        // ===================================================
        // get customer registered
        //====================================================
        if(true){
            // Count number of customers
            $numberOfAccount = ci()->customer_m->count_customer_partner_by(array(
                "from_unixtime(customers.created_date, '%Y%m') <= '" . $report_month . "'" => null,
                "customers.status"=> APConstants::OFF_FLAG,
                "partner_customers.partner_id" => $partner_id,
                "partner_customers.end_flag" => APConstants::OFF_FLAG
            ));

            // count new customer
            $number_of_customer = ci()->customer_m->count_customer_partner_by(array(
                "from_unixtime(customers.created_date, '%Y%m') <= '" . $report_month . "'" => null,
                "customers.status"=> APConstants::OFF_FLAG,
                // has at least one postbox.
				// "(customers.postbox_name_flag = 1 and customers.name_comp_address_flag = 1)" => null,
                "(cs1.setting_value = 1 and cs2.setting_value = 1)" => null,
                "partner_customers.partner_id" => $partner_id,
                "partner_customers.end_flag" => APConstants::OFF_FLAG
            ));

            // new registration.
            $new_registration =  ci()->customer_m->count_customer_partner_by(array(
                "from_unixtime(customers.created_date, '%Y%m') = '" . $report_month . "'" => null,
                "customers.status"=> APConstants::OFF_FLAG,
                "partner_customers.partner_id" => $partner_id,
                "partner_customers.end_flag" => APConstants::OFF_FLAG
            ));

            // never activated deleted.
            $number_never_activated_deleted = ci()->customer_m->count_customer_partner_by(array(
                "status"=> APConstants::ON_FLAG,
                "from_unixtime(customers.deleted_date, '%Y%m')='".$report_month."'" => null,
                "customers.activated_flag" => APConstants::OFF_FLAG,
                "(customers.deactivated_type = '' OR customers.deactivated_type is null)" => null,
                //"(customers.invoicing_address_completed = 0 OR cs1.postbox_name_flag = 0 OR cs2.name_comp_address_flag=0 OR customers.city_address_flag = 0 OR customers.email_confirm_flag = 0)" => null,
                "(cs1.setting_value = 0 OR cs2.setting_value = 0 OR cs3.setting_value = 0 OR cs4.setting_value = 0 OR cs4.setting_value = 0)" => null,
                "partner_customers.partner_id" => $partner_id,
                "partner_customers.end_flag" => APConstants::OFF_FLAG
            ));

            // manually deleted
            $number_manually_deleted = ci()->customer_m->count_customer_partner_by(array(
                "customers.status"=> APConstants::ON_FLAG,
                "from_unixtime(customers.deleted_date, '%Y%m')='".$report_month."'" => null,
                "customers.deleted_by <> 0" => null,
                "customers.deleted_by is not null" => null,
                "partner_customers.partner_id" => $partner_id,
                "partner_customers.end_flag" => APConstants::OFF_FLAG
            ));
            
            // automatic deleted number
            $number_automatic_deleted = ci()->customer_m->count_customer_partner_by(array(
                "customers.status"=> APConstants::ON_FLAG,
                "from_unixtime(customers.deleted_date, '%Y%m')='".$report_month."'" => null,
                "partner_customers.partner_id" => $partner_id,
                "partner_customers.end_flag" => APConstants::OFF_FLAG
            ));

            // number of automatic deleted by system.
            $number_automatic_deleted = $number_automatic_deleted - $number_manually_deleted - $number_never_activated_deleted;

            $result['number_customers'] = array(
                "number_of_account" => $numberOfAccount,
                "number_of_customer" => $number_of_customer,
                "new_registration" => $new_registration,
                "number_manually_deleted" => $number_manually_deleted,
                "number_automatic_deleted" => $number_automatic_deleted,
                "number_never_activated_deleted" => $number_never_activated_deleted
            );
        }
        
        // ===================================================
        // Count number of postbox.
        //====================================================
        if(true){
            $data = ci()->postbox_m->countPostboxesRegisteredByMonthOfPartner($report_month, $partner_id);
            $number_of_postbox = array(
                "number_free" => 0,
                "number_private" => 0,
                "number_business" => 0,
                "total" => 0
            );

            foreach ($data as $r) {
                if ($r->type == APConstants::FREE_TYPE) {
                    $number_of_postbox['number_free'] = $r->total;
                } else if ($r->type == APConstants::PRIVATE_TYPE) {
                    $number_of_postbox['number_private'] = $r->total;
                } else if ($r->type == APConstants::BUSINESS_TYPE) {
                    $number_of_postbox['number_business'] = $r->total;
                }
            }
            $number_of_postbox['total'] = $number_of_postbox['number_free'] + $number_of_postbox['number_private'] + $number_of_postbox['number_business'];
            $result['numberPostboxs'] = $number_of_postbox;
        }
        
        // ===================================================
        // Count number of envelopes.
        //====================================================
        if(true){
            // count of active customers
            $list_envelope_of_active_customers = ci()->envelope_m->countEnvelopesByMonthOfPartner($report_month, $partner_id);
            $list_envelopes = array(
                "received_num" => 0,
                "envelope_scanned_num" => 0,
                "item_scanned_num" => 0,
                "forwarded_num" => 0,
                "storage_num" => 0,
            );
            foreach ($list_envelope_of_active_customers as $r) {
                if ($r->kind == 'received_number') {
                    $list_envelopes['received_num'] = (int)$r->total;
                } else if ($r->kind == 'envelope_scanned_number') {
                    $list_envelopes['envelope_scanned_num'] = (int)$r->total;
                } else if ($r->kind == 'item_scanned_number') {
                    $list_envelopes['item_scanned_num'] = (int)$r->total;
                } else if ($r->kind == 'forwarded_number') {
                    $list_envelopes['forwarded_num'] = (int)$r->total;
                } else if ($r->kind == 'storage_number') {
                    $list_envelopes['storage_num'] = (int)$r->total;
                }
            }

            // count of deleted customers.
            $list_envelope_of_deleted_customers = ci()->envelope_m->countEnvelopesOfDeletedCustomerOfPartner($report_month, $partner_id);
            foreach ($list_envelope_of_deleted_customers as $r) {
                if ($r->kind == 'received_number') {
                    $list_envelopes['received_num'] += 0;
                } else if ($r->kind == 'envelope_scanned_number') {
                    $list_envelopes['envelope_scanned_num'] += (int)$r->total;
                } else if ($r->kind == 'item_scanned_number') {
                    $list_envelopes['item_scanned_num'] += (int)$r->total;
                } else if ($r->kind == 'forwarded_number') {
                    $list_envelopes['forwarded_num'] += (int)$r->total;
                }
            }

            // count storage number
            $list_envelopes['storage_num'] = ci()->envelope_storage_month_m->count_storage_item_of_partner($report_month, $partner_id);
            $result['numberEnvelopes'] = $list_envelopes;
        }
        
        // ===================================================
        // Count manual invoice.
        //====================================================
        if(true){
            $list_manual_invoices = ci()->invoice_summary_by_location_m->summary_by_manual_invoice_of_partner($report_month, $partner_id);

            $manualInvoices = array();

            // return manual invoice
            // 1. custom_declaration_greater_1000
            // 2. custom_declaration_less_1000
            // 3. cash_payment_for_item_delivery
            // 4. cash_payment_free_for_item_delivery
            // 5. customs_cost_import
            // 6. customs_handling_fee_import
            // 7. address_verification
            // 8. special_service_fee_in_15min_intervalls
            // 9. personal_pickup_charge
            foreach ($list_manual_invoices as $r) {
                $manualInvoices[$r->kind] = new stdClass();
                $manualInvoices[$r->kind]->quantity = (int)$r->quantity;
                $manualInvoices[$r->kind]->total_amount = (float)$r->total_amount;
                $manualInvoices[$r->kind]->discount_total = (float)$r->discount_total;
                $manualInvoices[$r->kind]->rev_share_total = (float)$r->rev_share_total;
            }

            $result['manualInvoices'] = $manualInvoices;
        }
        
        // ===================================================
        // Count credit note  of partner.
        //====================================================
        $result['credit_note'] = ci()->invoice_summary_by_location_m->summary_by_credit_note_of_partner($report_month, $partner_id);
        
        // ===================================================
        // Count other invoice  of partner.
        //====================================================
        $result['other_invoice'] = ci()->invoice_summary_by_location_m->summary_all_manual_invoice_of_partner($report_month, $partner_id);
        
        // ===================================================
        // Count forwarding Charges of partner.
        //====================================================
        $result['forwardingCharges'] = ci()->envelope_shipping_m->summary_by_partner($report_month, $partner_id);
        
        // ===================================================
        // Count getPaypalFeeByLocation.
        //====================================================
        $paypal_total = ci()->invoice_summary_by_location_m->summary_by_paypal_total_of_partner($report_month, $partner_id);
        $paypal_quantity = ci()->invoice_summary_by_location_m->summary_by_paypal_quantity_of_partner($report_month, $partner_id);
        $result['paypalFee'] = array(
             "total" => $paypal_total,
             "quantity" => $paypal_quantity
        );
        
        // get invoice summary
        $result['invoice_summary'] = ci()->invoice_summary_m->summary_by_partner($report_month, $partner_id);
        
        // commit transaction
        if(ci()->report_m->db->trans_status() == FALSE){
            ci()->report_m->db->trans_rollback();
        }else{
            ci()->report_m->db->trans_commit();
        }
        
        return $result;
    }
}