<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author DuNT
 */
class report_by_location_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('report_by_location');
        $this->primary_key = 'id';
    }
    
    public function get_invoice_total_by_location($targetYM, $share_rev_flag = false){
        $condition = '';
        if($share_rev_flag){
            $condition = " AND customers.charge_fee_flag = 1";
        }
        
        $sql = "SELECT
                   invoice_month
               ,	location_id
               ,	invoice_summary_by_location.invoice_type
               ,	SUM(free_postboxes_amount) as free_postboxes_amount
               ,	SUM(private_postboxes_amount) as private_postboxes_amount
               ,	SUM(business_postboxes_amount)  as business_postboxes_amount
               ,	SUM(incomming_items_free_account)  as incomming_items_free_account
               ,	SUM(incomming_items_private_account)  as incomming_items_private_account
               ,	SUM(incomming_items_business_account)  as incomming_items_business_account
               ,	SUM(envelope_scan_free_account)  as envelope_scan_free_account
               ,	SUM(envelope_scan_private_account)  as envelope_scan_private_account
               ,	SUM(envelope_scan_business_account)  as envelope_scan_business_account
               ,	SUM(item_scan_free_account)  as item_scan_free_account
               ,	SUM(item_scan_private_account)  as item_scan_private_account
               ,	SUM(item_scan_business_account)  as item_scan_business_account
               ,	SUM(direct_shipping_free_account) as direct_shipping_free_account
               ,	SUM(direct_shipping_private_account) as direct_shipping_private_account
               ,	SUM(direct_shipping_business_account) as direct_shipping_business_account
               ,	SUM(collect_shipping_free_account) as collect_shipping_free_account
               ,	SUM(collect_shipping_private_account) as  collect_shipping_private_account
               ,	SUM(collect_shipping_business_account) as collect_shipping_business_account
               ,	SUM(storing_letters_free_account) as storing_letters_free_account
               ,	SUM(storing_letters_private_account) as storing_letters_private_account
               ,	SUM(storing_letters_business_account) as storing_letters_business_account
               ,	SUM(storing_packages_free_account) as storing_packages_free_account
               ,	SUM(storing_packages_private_account) as  storing_packages_private_account
               ,	SUM(storing_packages_business_account) as storing_packages_business_account
               ,	SUM(additional_pages_scanning_free_amount) as additional_pages_scanning_free_amount
               ,	SUM(additional_pages_scanning_private_amount) as additional_pages_scanning_private_amount
               ,	SUM(additional_pages_scanning_business_amount) as additional_pages_scanning_business_amount
               ,	SUM(custom_declaration_outgoing_quantity_01) as custom_declaration_outgoing_quantity_01
               ,	SUM(custom_declaration_outgoing_quantity_02) as custom_declaration_outgoing_quantity_02
               ,	SUM(custom_declaration_outgoing_price_01) as custom_declaration_outgoing_price_01
               ,	SUM(custom_declaration_outgoing_price_02) as custom_declaration_outgoing_price_02
               ,	SUM( total_invoice ) as  net_total_invoice
               ,    SUM( total_invoice * (1 + IFNULL(vat, 0)) ) as gross_total_invoice
               ,    SUM(forwarding_charges_fee) as forwarding_charges_fee
               ,    SUM(forwarding_charges_postal) as forwarding_charges_postal
               FROM invoice_summary_by_location
               JOIN customers ON customers.customer_id=invoice_summary_by_location.customer_id
               WHERE (1 = 1)
                    ".$condition."
                    AND LEFT(invoice_month, 6) = '{$targetYM}'
                    AND  (invoice_summary_by_location.invoice_type is null OR invoice_summary_by_location.invoice_type = 1)
               GROUP BY
                    invoice_month
                    , location_id
                    , invoice_summary_by_location.invoice_type";
        
        $result = $this->db->query($sql)->result();
        
        return $result;
    }
    
    public function get_invoice_by_month($targetYM){
        $stmt = " SELECT
                    SUM(free_postboxes_amount) AS free_postboxes_amount,
                    SUM(private_postboxes_amount) AS private_postboxes_amount,
                    SUM(business_postboxes_amount) AS business_postboxes_amount,
                    
                    SUM(incomming_items_free_account) AS incomming_items_free_account,
                    SUM(incomming_items_private_account) AS incomming_items_private_account,
                    SUM(incomming_items_business_account) AS incomming_items_business_account,
                    
                    SUM(envelope_scan_free_account) AS envelope_scan_free_account,
                    SUM(envelope_scan_private_account) AS envelope_scan_private_account,
                    SUM(envelope_scan_business_account) AS envelope_scan_business_account,
                    
                    SUM(item_scan_free_account) AS item_scan_free_account,
                    SUM(item_scan_private_account) AS item_scan_private_account,
                    SUM(item_scan_business_account) AS item_scan_business_account,
                    
                    SUM(forwarding_charges_fee) AS forwarding_charges_fee,
                    SUM(forwarding_charges_postal) AS forwarding_charges_postal,
                    
                    SUM(direct_shipping_free_account) AS direct_shipping_free_account,
                    SUM(direct_shipping_private_account) AS direct_shipping_private_account,
                    SUM(direct_shipping_business_account) AS direct_shipping_business_account,
                    
                    SUM(collect_shipping_free_account) AS collect_shipping_free_account,
                    SUM(collect_shipping_private_account) AS collect_shipping_private_account,
                    SUM(collect_shipping_business_account) AS collect_shipping_business_account,
                    
                    SUM(storing_letters_free_account) AS storing_letters_free_account,
                    SUM(storing_letters_private_account) AS storing_letters_private_account,
                    SUM(storing_letters_business_account) AS storing_letters_business_account,
                    
                    SUM(storing_packages_free_account) AS storing_packages_free_account,
                    SUM(storing_packages_private_account) AS storing_packages_private_account,
                    SUM(storing_packages_business_account) AS storing_packages_business_account,
                    
                    SUM(additional_pages_scanning_free_amount) AS additional_pages_scanning_free_amount,
                    SUM(additional_pages_scanning_private_amount) AS additional_pages_scanning_private_amount,
                    SUM(additional_pages_scanning_business_amount) AS additional_pages_scanning_business_amount,
                    
                    SUM(forwarding_charges_postal) AS forwarding_charges_postal,
                    SUM(forwarding_charges_fee) AS forwarding_charges_fee,
                    
                    SUM(cash_payment_for_item_delivery_amount) AS cash_payment_for_item_delivery_amount,
                    SUM(cash_payment_free_for_item_delivery_amount) AS cash_payment_free_for_item_delivery_amount,
                    
                    SUM(customs_cost_import_amount) AS customs_cost_import_amount,
                    SUM(customs_handling_fee_import_amount) AS customs_handling_fee_import_amount,
                    
                    SUM(custom_declaration_outgoing_price_01) AS custom_declaration_outgoing_price_01,
                    SUM(custom_declaration_outgoing_price_02) AS custom_declaration_outgoing_price_02,
                    
                    SUM(address_verification_amount) AS address_verification_amount,
                    SUM(special_service_fee_in_15min_intervalls_amount) AS special_service_fee_in_15min_intervalls_amount,
                    SUM(personal_pickup_charge_amount) AS personal_pickup_charge_amount,
                    SUM(paypal_fee) AS paypal_fee,
                    SUM(other_local_invoice) AS other_local_invoice,
                    SUM(credit_note_given) AS credit_note_given,
                    
                    SUM(net_total_invoice) AS net_total_invoice,
                    SUM(gross_total_invoice) AS gross_total_invoice,
                    SUM(share_total_invoice) AS share_total_invoice,
                    
                    SUM(free_postboxes_amount_share * free_postboxes_share_rev / 100) AS free_postboxes_amount_share,
                    SUM(private_postboxes_amount_share * private_postboxes_share_rev / 100) AS private_postboxes_amount_share,
                    SUM(business_postboxes_amount_share * business_postboxes_share_rev / 100) AS business_postboxes_amount_share,
                    
                    SUM(incomming_items_free_account_share * incomming_items_free_share_rev / 100) AS incomming_items_free_account_share,
                    SUM(incomming_items_private_account_share * incomming_items_private_share_rev / 100) AS incomming_items_private_account_share,
                    SUM(incomming_items_business_account_share * incomming_items_business_share_rev/ 100) AS incomming_items_business_account_share,
                    
                    SUM(envelope_scan_free_account_share * envelope_scan_free_share_rev/ 100) AS envelope_scan_free_account_share,
                    SUM(envelope_scan_private_account_share * envelope_scan_private_share_rev/ 100) AS envelope_scan_private_account_share,
                    SUM(envelope_scan_business_account_share * envelope_scan_business_share_rev/ 100) AS envelope_scan_business_account_share,
                    
                    SUM(item_scan_free_account_share * item_scan_free_share_rev/ 100) AS item_scan_free_account_share,
                    SUM(item_scan_private_account_share * item_scan_private_share_rev/ 100) AS item_scan_private_account_share,
                    SUM(item_scan_business_account_share * item_scan_business_share_rev/ 100) AS item_scan_business_account_share,
                    
                    SUM(direct_shipping_free_account_share * direct_shipping_free_share_rev/ 100) AS direct_shipping_free_account_share,
                    SUM(direct_shipping_private_account_share * direct_shipping_private_share_rev/ 100) AS direct_shipping_private_account_share,
                    SUM(direct_shipping_business_account_share * direct_shipping_business_share_rev/ 100) AS direct_shipping_business_account_share,
                    
                    SUM(collect_shipping_free_account_share * collect_shipping_free_share_rev/ 100) AS collect_shipping_free_account_share,
                    SUM(collect_shipping_private_account_share * collect_shipping_private_share_rev/ 100) AS collect_shipping_private_account_share,
                    SUM(collect_shipping_business_account_share * collect_shipping_business_share_rev/ 100) AS collect_shipping_business_account_share,
                    
                    SUM(storing_letters_free_account_share * storing_letters_free_share_rev/ 100) AS storing_letters_free_account_share,
                    SUM(storing_letters_private_account_share * storing_letters_private_share_rev/ 100) AS storing_letters_private_account_share,
                    SUM(storing_letters_business_account_share * storing_letters_business_share_rev/ 100) AS storing_letters_business_account_share,
                    
                    SUM(storing_packages_free_account_share * storing_packages_free_share_rev/ 100) AS storing_packages_free_account_share,
                    SUM(storing_packages_private_account_share * storing_packages_private_share_rev/ 100) AS storing_packages_private_account_share,
                    SUM(storing_packages_business_account_share * storing_packages_business_share_rev/ 100) AS storing_packages_business_account_share,
                    
                    SUM(additional_pages_scanning_free_amount_share * additional_pages_scanning_free_share_rev/ 100) AS additional_pages_scanning_free_amount_share,
                    SUM(additional_pages_scanning_private_amount_share * additional_pages_scanning_private_share_rev/ 100) AS additional_pages_scanning_private_amount_share,
                    SUM(additional_pages_scanning_business_amount_share * additional_pages_scanning_business_share_rev/ 100) AS additional_pages_scanning_business_amount_share,
                    
                    SUM(forwarding_charges_postal_share * forwarding_charges_postal_share_rev/ 100) AS forwarding_charges_postal_share,
                    SUM(forwarding_charges_fee_share * forwarding_charges_fee_share_rev/ 100) AS forwarding_charges_fee_share,
                    
                    SUM(cash_payment_for_item_delivery_amount_share * cash_payment_for_item_delivery_share_rev/ 100) AS cash_payment_for_item_delivery_amount_share,
                    SUM(cash_payment_free_for_item_delivery_amount_share * cash_payment_free_for_item_delivery_share_rev/ 100) AS cash_payment_free_for_item_delivery_amount_share,
                    
                    SUM(customs_cost_import_amount_share * customs_cost_import_share_rev/ 100) AS customs_cost_import_amount_share,
                    SUM(customs_handling_fee_import_amount_share * customs_handling_fee_import_share_rev/ 100) AS customs_handling_fee_import_amount_share,
                    SUM(address_verification_amount_share * address_verification_share_rev/ 100) AS address_verification_amount_share,
                    SUM(special_service_fee_in_15min_intervalls_amount_share * special_service_fee_in_15min_intervalls_share_rev/ 100) AS special_service_fee_in_15min_intervalls_amount_share,
                    SUM(personal_pickup_charge_amount_share * personal_pickup_charge_share_rev/ 100) AS personal_pickup_charge_amount_share,
                    
                    SUM(paypal_fee_share * paypal_fee_share_rev/ 100) AS paypal_fee_share,
                    
                    SUM(other_local_invoice_share * other_local_invoice_share_rev/ 100) AS other_local_invoice_share,
                    SUM(credit_note_given_share * credit_note_given_share_rev/ 100) AS credit_note_given_share,
                    
                    SUM(custom_declaration_outgoing_price_01_amount_share * custom_declaration_outgoing_price_01_share_rev/ 100) AS custom_declaration_outgoing_price_01_amount_share,
                    SUM(custom_declaration_outgoing_price_02_amount_share * custom_declaration_outgoing_price_01_share_rev/ 100) AS custom_declaration_outgoing_price_02_amount_share,
                    
                    SUM(free_postbox_quantity) as free_postbox_quantity,
                    SUM(free_postbox_quantity_share) as free_postbox_quantity_share,
                    SUM(private_postbox_quantity) as private_postbox_quantity,
                    SUM(private_postbox_quantity_share) as private_postbox_quantity_share,
                    SUM(business_postbox_quantity) as business_postbox_quantity,
                    SUM(business_postbox_quantity_share) as business_postbox_quantity_share,
                    SUM(free_incoming_quantity) as free_incoming_quantity,
                    SUM(free_incoming_quantity_share) as free_incoming_quantity_share,
                    SUM(private_incoming_quantity) as private_incoming_quantity,
                    SUM(private_incoming_quantity_share) as private_incoming_quantity_share,
                    SUM(business_incoming_item_quantity) as business_incoming_item_quantity,
                    SUM(business_incoming_item_quantity_share) as business_incoming_item_quantity_share,
                    SUM(free_envelope_scan_quantity) as free_envelope_scan_quantity,
                    SUM(free_envelope_scan_quantity_share) as free_envelope_scan_quantity_share,
                    SUM(private_envelope_scan_quantity) as private_envelope_scan_quantity,
                    SUM(private_envelope_scan_quantity_share) as private_envelope_scan_quantity_share,
                    SUM(business_envelope_scan_quantity) as business_envelope_scan_quantity,
                    SUM(business_envelope_scan_quantity_share) as business_envelope_scan_quantity_share,
                    SUM(free_item_scan_quantity) as free_item_scan_quantity,
                    SUM(free_item_scan_quantity_share) as free_item_scan_quantity_share,
                    SUM(private_item_scan_quantity) as private_item_scan_quantity,
                    SUM(private_item_scan_quantity_share) as private_item_scan_quantity_share,
                    SUM(business_item_scan_quantity) as business_item_scan_quantity,
                    SUM(business_item_scan_quantity_share) as business_item_scan_quantity_share,
                    SUM(free_additional_page_quantity) as free_additional_page_quantity,
                    SUM(free_additional_page_quantity_share) as free_additional_page_quantity_share,
                    SUM(private_additional_page_quantity) as private_additional_page_quantity,
                    SUM(private_additional_page_quantity_share) as private_additional_page_quantity_share,
                    SUM(business_additional_page_quantity) as business_additional_page_quantity,
                    SUM(business_additional_page_quantity_share) as business_additional_page_quantity_share,
                    SUM(fowarding_charge_postal_quantity) as fowarding_charge_postal_quantity,
                    SUM(fowarding_charge_postal_quantity_share) as fowarding_charge_postal_quantity_share,
                    SUM(fowarding_charge_fee_quantity) as fowarding_charge_fee_quantity,
                    SUM(fowarding_charge_fee_quantity_share) as fowarding_charge_fee_quantity_share,
                    SUM(free_storage_letter_quanity) as free_storage_letter_quanity,
                    SUM(free_storage_letter_quanity_share) as free_storage_letter_quanity_share,
                    SUM(private_storage_letter_quanity) as private_storage_letter_quanity,
                    SUM(private_storage_letter_quanity_share) as private_storage_letter_quanity_share,
                    SUM(business_storage_letter_quanity) as business_storage_letter_quanity,
                    SUM(business_storage_letter_quanity_share) as business_storage_letter_quanity_share,
                    SUM(free_storage_package_quanity) as free_storage_package_quanity,
                    SUM(free_storage_package_quanity_share) as free_storage_package_quanity_share,
                    SUM(private_storage_package_quanity) as private_storage_package_quanity,
                    SUM(private_storage_package_quanity_share) as private_storage_package_quanity_share,
                    SUM(business_storage_package_quanity) as business_storage_package_quanity,
                    SUM(business_storage_package_quanity_share) as business_storage_package_quanity_share,
                    SUM(custom_declaration_quantity) as custom_declaration_quantity,
                    SUM(custom_declaration_quantity_share) as custom_declaration_quantity_share,
                    SUM(cash_payment_fee_quantity) as cash_payment_fee_quantity,
                    SUM(cash_payment_fee_quantity_share) as cash_payment_fee_quantity_share,
                    SUM(custom_cost_import_quantity) as custom_cost_import_quantity,
                    SUM(custom_cost_import_quantity_share) as custom_cost_import_quantity_share,
                    SUM(import_custom_fee_quantity) as import_custom_fee_quantity,
                    SUM(import_custom_fee_quantity_share) as import_custom_fee_quantity_share,
                    SUM(address_verification_quantity) as address_verification_quantity,
                    SUM(address_verification_quantity_share) as address_verification_quantity_share,
                    SUM(special_service_fee_quantity) as special_service_fee_quantity,
                    SUM(special_service_fee_quantity_share) as special_service_fee_quantity_share,
                    SUM(peronsal_pickup_charge_quantity) as peronsal_pickup_charge_quantity,
                    SUM(peronsal_pickup_charge_quantity_share) as peronsal_pickup_charge_quantity_share,
                    SUM(paypal_transaction_fee_quantity) as paypal_transaction_fee_quantity,
                    SUM(paypal_transaction_fee_quantity_share) as paypal_transaction_fee_quantity_share,
                    SUM(other_local_invoice_quantity) as other_local_invoice_quantity,
                    SUM(other_local_invoice_quantity_share) as other_local_invoice_quantity_share,
                    SUM(creditnote_quantity) as creditnote_quantity,
                    SUM(creditnote_quantity_share) as creditnote_quantity_share
                FROM report_by_location
                WHERE invoice_month='{$targetYM}'";
                
        $result = $this->db->query($stmt)->row();
        
        return $result;
    }
}