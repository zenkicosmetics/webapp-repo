<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 *
 * @author TienNH
 */
class invoice_summary_total_by_location_m extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->profile_table = $this->db->dbprefix('invoice_summary_total_by_location');
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
                    
                    SUM(forwarding_charges_postal) AS forwarding_charges_postal,
                    SUM(forwarding_charges_fee) AS forwarding_charges_fee,
                    
                    SUM(cash_payment_for_item_delivery_amount) AS cash_payment_for_item_delivery_amount,
                    SUM(cash_payment_free_for_item_delivery_amount) AS cash_payment_free_for_item_delivery_amount,
                    
                    SUM(customs_cost_import_amount) AS customs_cost_import_amount,
                    SUM(customs_handling_fee_import_amount) AS customs_handling_fee_import_amount,
                    
                    SUM(address_verification_amount) AS address_verification_amount,
                    SUM(special_service_fee_in_15min_intervalls_amount) AS special_service_fee_in_15min_intervalls_amount,
                    SUM(personal_pickup_charge_amount) AS personal_pickup_charge_amount,
                    SUM(paypal_fee) AS paypal_fee,
                    SUM(other_local_invoice) AS other_local_invoice,
                    SUM(credit_note_given) AS credit_note_given,
                    
                    SUM(net_total_invoice) AS net_total_invoice,
                    SUM(gross_total_invoice) AS gross_total_invoice,
                    SUM(share_total_invoice) AS share_total_invoice,
                    
                    SUM(additional_pages_scanning_free_amount) AS additional_pages_scanning_free_amount,
                    SUM(additional_pages_scanning_private_amount) AS additional_pages_scanning_private_amount,
                    SUM(additional_pages_scanning_business_amount) AS additional_pages_scanning_business_amount,
                    
                    SUM(custom_declaration_outgoing_price_01) AS custom_declaration_outgoing_price_01,
                    SUM(custom_declaration_outgoing_price_02) AS custom_declaration_outgoing_price_02,


                    SUM(free_postboxes_amount_share) AS free_postboxes_amount_share,
                    SUM(private_postboxes_amount_share) AS private_postboxes_amount_share,
                    SUM(business_postboxes_amount_share) AS business_postboxes_amount_share,
                    
                    SUM(incomming_items_free_account_share) AS incomming_items_free_account_share,
                    SUM(incomming_items_private_account_share) AS incomming_items_private_account_share,
                    SUM(incomming_items_business_account_share) AS incomming_items_business_account_share,
                    
                    SUM(envelope_scan_free_account_share) AS envelope_scan_free_account_share,
                    SUM(envelope_scan_private_account_share) AS envelope_scan_private_account_share,
                    SUM(envelope_scan_business_account_share) AS envelope_scan_business_account_share,
                    
                    SUM(item_scan_free_account_share) AS item_scan_free_account_share,
                    SUM(item_scan_private_account_share) AS item_scan_private_account_share,
                    SUM(item_scan_business_account_share) AS item_scan_business_account_share,
                    
                    SUM(direct_shipping_free_account_share) AS direct_shipping_free_account_share,
                    SUM(direct_shipping_private_account_share) AS direct_shipping_private_account_share,
                    SUM(direct_shipping_business_account_share) AS direct_shipping_business_account_share,
                    
                    SUM(collect_shipping_free_account_share) AS collect_shipping_free_account_share,
                    SUM(collect_shipping_private_account_share) AS collect_shipping_private_account_share,
                    SUM(collect_shipping_business_account_share) AS collect_shipping_business_account_share,
                    
                    SUM(storing_letters_free_account_share) AS storing_letters_free_account_share,
                    SUM(storing_letters_private_account_share) AS storing_letters_private_account_share,
                    SUM(storing_letters_business_account_share) AS storing_letters_business_account_share,
                    
                    SUM(storing_packages_free_account_share) AS storing_packages_free_account_share,
                    SUM(storing_packages_private_account_share) AS storing_packages_private_account_share,
                    SUM(storing_packages_business_account_share) AS storing_packages_business_account_share,
                    
                    SUM(additional_pages_scanning_free_amount_share) AS additional_pages_scanning_free_amount_share,
                    SUM(additional_pages_scanning_private_amount_share) AS additional_pages_scanning_private_amount_share,
                    SUM(additional_pages_scanning_business_amount_share) AS additional_pages_scanning_business_amount_share,
                    
                    SUM(forwarding_charges_postal_share) AS forwarding_charges_postal_share,
                    SUM(forwarding_charges_fee_share) AS forwarding_charges_fee_share,
                    
                    SUM(cash_payment_for_item_delivery_amount_share) AS cash_payment_for_item_delivery_amount_share,
                    SUM(cash_payment_free_for_item_delivery_amount_share) AS cash_payment_free_for_item_delivery_amount_share,
                    
                    SUM(customs_cost_import_amount_share) AS customs_cost_import_amount_share,
                    SUM(customs_handling_fee_import_amount_share) AS customs_handling_fee_import_amount_share,
                    SUM(address_verification_amount_share) AS address_verification_amount_share,
                    SUM(special_service_fee_in_15min_intervalls_amount_share) AS special_service_fee_in_15min_intervalls_amount_share,
                    SUM(personal_pickup_charge_amount_share) AS personal_pickup_charge_amount_share,
                    
                    SUM(paypal_fee_share) AS paypal_fee_share,
                    
                    SUM(other_local_invoice_share) AS other_local_invoice_share,
                    
                    SUM(credit_note_given_share) AS credit_note_given_share,
                    
                    SUM(custom_declaration_outgoing_price_01_amount_share) AS custom_declaration_outgoing_price_01_amount_share,
                    SUM(custom_declaration_outgoing_price_02_amount_share) AS custom_declaration_outgoing_price_02_amount_share
                    
                FROM invoice_summary_total_by_location
                WHERE invoice_month='{$targetYM}'";
                
        $result = $this->db->query($stmt)->row();
        
        return $result;
    }
}