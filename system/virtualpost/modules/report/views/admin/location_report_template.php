<?php 
    $hidden_class = "";
    if(empty($location_id)){
        $hidden_class = "hide";
    }

    $rate = 0.01;

    // Calculate total rev share
    $rev_share_total = 0;
    if(empty($location_id)){
        $rev_share_total += round($invoice_by_location->free_postboxes_amount_share, 2);
        $rev_share_total += round($invoice_by_location->private_postboxes_amount_share, 2);
        $rev_share_total += round($invoice_by_location->business_postboxes_amount_share, 2);

        $rev_share_total += round($invoice_by_location->incomming_items_free_account_share, 2);
        $rev_share_total += round($invoice_by_location->incomming_items_private_account_share, 2);
        $rev_share_total += round($invoice_by_location->incomming_items_business_account_share, 2);

        $rev_share_total += round($invoice_by_location->envelope_scan_free_account_share, 2);
        $rev_share_total += round($invoice_by_location->envelope_scan_private_account_share, 2);
        $rev_share_total += round($invoice_by_location->envelope_scan_business_account_share, 2);

        $rev_share_total += round($invoice_by_location->item_scan_free_account_share, 2);
        $rev_share_total += round($invoice_by_location->item_scan_private_account_share, 2);
        $rev_share_total += round($invoice_by_location->item_scan_business_account_share, 2);

        $rev_share_total += round($invoice_by_location->additional_pages_scanning_free_amount_share, 2);
        $rev_share_total += round($invoice_by_location->additional_pages_scanning_private_amount_share, 2);
        $rev_share_total += round($invoice_by_location->additional_pages_scanning_business_amount_share, 2);

        $rev_share_total += round($invoice_by_location->forwarding_charges_fee_share, 2);

        $rev_share_total += round($invoice_by_location->storing_letters_free_account_share, 2);
        $rev_share_total += round($invoice_by_location->storing_letters_private_account_share, 2);
        $rev_share_total += round($invoice_by_location->storing_letters_business_account_share, 2);

        $rev_share_total += round($invoice_by_location->storing_packages_free_account_share, 2);
        $rev_share_total += round($invoice_by_location->storing_packages_private_account_share, 2);
        $rev_share_total += round($invoice_by_location->storing_packages_business_account_share, 2);

        $rev_share_total += round($invoice_by_location->custom_declaration_outgoing_price_01_amount_share, 2);
        $rev_share_total += round($invoice_by_location->custom_declaration_outgoing_price_02_amount_share, 2);

        $rev_share_total += round($invoice_by_location->cash_payment_free_for_item_delivery_amount_share, 2);

        $rev_share_total += round($invoice_by_location->customs_cost_import_amount_share, 2);
        $rev_share_total += round($invoice_by_location->customs_handling_fee_import_amount_share, 2);
        $rev_share_total += round($invoice_by_location->address_verification_amount_share, 2);
        $rev_share_total += round($invoice_by_location->special_service_fee_in_15min_intervalls_amount_share, 2);
        $rev_share_total += round($invoice_by_location->personal_pickup_charge_amount_share, 2);

        $rev_share_total += round($invoice_by_location->paypal_fee_share, 2);

        $rev_share_total += round($invoice_by_location->other_local_invoice_share, 2);

        $rev_share_total += round($invoice_by_location->credit_note_given_share, 2);
    }else{
        $rev_share_total += round($invoice_by_location->free_postboxes_amount_share * $rate * $invoice_by_location->free_postboxes_share_rev, 2);
        $rev_share_total += round($invoice_by_location->private_postboxes_amount_share* $rate * $invoice_by_location->private_postboxes_share_rev, 2);
        $rev_share_total += round($invoice_by_location->business_postboxes_amount_share* $rate * $invoice_by_location->business_postboxes_share_rev, 2);

        $rev_share_total += round($invoice_by_location->incomming_items_free_account_share* $rate * $invoice_by_location->incomming_items_free_share_rev, 2);
        $rev_share_total += round($invoice_by_location->incomming_items_private_account_share* $rate * $invoice_by_location->incomming_items_private_share_rev, 2);
        $rev_share_total += round($invoice_by_location->incomming_items_business_account_share* $rate * $invoice_by_location->incomming_items_business_share_rev, 2);

        $rev_share_total += round($invoice_by_location->envelope_scan_free_account_share* $rate * $invoice_by_location->envelope_scan_free_share_rev, 2);
        $rev_share_total += round($invoice_by_location->envelope_scan_private_account_share* $rate * $invoice_by_location->envelope_scan_private_share_rev, 2);
        $rev_share_total += round($invoice_by_location->envelope_scan_business_account_share* $rate * $invoice_by_location->envelope_scan_business_share_rev, 2);

        $rev_share_total += round($invoice_by_location->item_scan_free_account_share* $rate * $invoice_by_location->item_scan_free_share_rev, 2);
        $rev_share_total += round($invoice_by_location->item_scan_private_account_share* $rate * $invoice_by_location->item_scan_private_share_rev, 2);
        $rev_share_total += round($invoice_by_location->item_scan_business_account_share* $rate * $invoice_by_location->item_scan_business_share_rev, 2);

        $rev_share_total += round($invoice_by_location->additional_pages_scanning_free_amount_share* $rate * $invoice_by_location->additional_pages_scanning_free_share_rev, 2);
        $rev_share_total += round($invoice_by_location->additional_pages_scanning_private_amount_share* $rate * $invoice_by_location->additional_pages_scanning_private_share_rev, 2);
        $rev_share_total += round($invoice_by_location->additional_pages_scanning_business_amount_share* $rate * $invoice_by_location->additional_pages_scanning_business_share_rev, 2);

        $rev_share_total += round($invoice_by_location->forwarding_charges_fee_share* $rate * $invoice_by_location->forwarding_charges_fee_share_rev, 2);

        $rev_share_total += round($invoice_by_location->storing_letters_free_account_share* $rate * $invoice_by_location->storing_letters_free_share_rev, 2);
        $rev_share_total += round($invoice_by_location->storing_letters_private_account_share* $rate * $invoice_by_location->storing_letters_private_share_rev, 2);
        $rev_share_total += round($invoice_by_location->storing_letters_business_account_share* $rate * $invoice_by_location->storing_letters_business_share_rev, 2);

        $rev_share_total += round($invoice_by_location->storing_packages_free_account_share* $rate * $invoice_by_location->storing_packages_free_share_rev, 2);
        $rev_share_total += round($invoice_by_location->storing_packages_private_account_share* $rate * $invoice_by_location->storing_packages_private_share_rev, 2);
        $rev_share_total += round($invoice_by_location->storing_packages_business_account_share* $rate * $invoice_by_location->storing_packages_business_share_rev, 2);

        $rev_share_total += round($invoice_by_location->custom_declaration_outgoing_price_01_amount_share* $rate * $invoice_by_location->custom_declaration_outgoing_price_01_share_rev, 2);
        $rev_share_total += round($invoice_by_location->custom_declaration_outgoing_price_02_amount_share* $rate * $invoice_by_location->custom_declaration_outgoing_price_01_share_rev, 2);

        $rev_share_total += round($invoice_by_location->cash_payment_for_item_delivery_amount_share* $rate * $invoice_by_location->cash_payment_for_item_delivery_share_rev, 2);
        $rev_share_total += round($invoice_by_location->customs_cost_import_amount_share* $rate * $invoice_by_location->customs_cost_import_share_rev, 2);
        $rev_share_total += round($invoice_by_location->customs_handling_fee_import_amount_share* $rate * $invoice_by_location->customs_handling_fee_import_share_rev, 2);
        $rev_share_total += round($invoice_by_location->address_verification_amount_share* $rate * $invoice_by_location->address_verification_share_rev, 2);
        $rev_share_total += round($invoice_by_location->special_service_fee_in_15min_intervalls_amount_share* $rate * $invoice_by_location->special_service_fee_in_15min_intervalls_share_rev, 2);
        $rev_share_total += round($invoice_by_location->personal_pickup_charge_amount_share* $rate * $invoice_by_location->personal_pickup_charge_share_rev, 2);

        $rev_share_total += round($invoice_by_location->paypal_fee_share* $rate * $invoice_by_location->paypal_fee_share_rev, 2);
        $rev_share_total += round($invoice_by_location->other_local_invoice_share* $rate * $invoice_by_location->other_local_invoice_share_rev, 2);
        $rev_share_total += round($invoice_by_location->credit_note_given_share* $rate * $invoice_by_location->credit_note_given_share_rev, 2);
    }

    ?>
<style>
table{
    border: 1px solid black;
}
tr.separated td {
    /* set border style for separated rows */
    border-bottom: 1px solid black;
    width: 96.5%;
    height:2px;
} 
br {
   line-height: 18%;
}

</style>
<div style=" margin: 0.25px 0px 0px 0px; padding: 0px 0px 0px 0px;"> 
    <div style="font-weight: bold;text-align: left;font-size: 50px"><?php admin_language_e('report_views_admin_location_report_template_Location'); ?><?php echo $location_name ?></div>
    <div style="font-weight: bold;text-align: left;"><?php admin_language_e('report_views_admin_location_report_template_Period'); ?><?php echo $period ?></div>
    <div style="font-weight: bold;text-align: left;"><?php admin_language_e('report_views_admin_location_report_template_Overview'); ?></div>
</div>

<table width="103.5%">
    <tr bgcolor="#b1b1cd">
       <th align="left" width="30%"><font color="#ffffff"><?php admin_language_e('report_views_admin_location_report_template_TblHdDes'); ?></font></th>
       <th></th> 
       <th align="right"><font color="#ffffff">#</font></th> 
    </tr>    

    <tr>
        <td align="left" width="40%"><?php admin_language_e('report_views_admin_location_report_template_TblHdNumCus'); ?></td>
        <td align="left" width="22%">Total</td>
        <td align="right"><?php echo empty($location_id) ? 1* $invoice_by_total->number_of_customer : 1* $invoice_by_location->number_of_customers_share; ?></td>
    </tr>
    <tr>
        <td align="left" width="40%"><?php admin_language_e('report_views_admin_location_report_template_TblHdNumPostbox'); ?></td>
        <td align="left" width="22%"><?php admin_language_e('report_views_admin_location_report_template_TblHdTotal'); ?></td>
        <td align="right"><?php echo empty($location_id) ? 1* $invoice_by_total->number_of_postbox : 1* $invoice_by_location->number_of_postbox_share; ?></td>
    </tr>
    <tr>
       <td align="left" width="33.5%">
          <ul>
            <li><?php admin_language_e('report_views_admin_location_report_template_TypeFree'); ?></li>
            <li><?php admin_language_e('report_views_admin_location_report_template_TypePrivate'); ?></li>
            <li><?php admin_language_e('report_views_admin_location_report_template_TypeBuss'); ?></li>

          </ul>
       </td>
       <td align="left" width="28.5%">
            <ul type="none">
                <li><?php admin_language_e('report_views_admin_location_report_template_Total'); ?></li>
                <li><?php admin_language_e('report_views_admin_location_report_template_Total'); ?></li>
                <li><?php admin_language_e('report_views_admin_location_report_template_Total'); ?></li>
            </ul>  
       </td>
        <td >
            <ul type="none" align="right">
                <li><?php echo empty($location_id) ? 1* $invoice_by_location->free_postbox_quantity : 1* $invoice_by_location->free_postbox_quantity_share; ?></li>
                <li><?php echo empty($location_id) ? 1* $invoice_by_location->private_postbox_quantity : 1* $invoice_by_location->private_postbox_quantity_share; ?></li>
                <li><?php echo empty($location_id) ? 1* $invoice_by_location->business_postbox_quantity : 1* $invoice_by_location->business_postbox_quantity_share; ?></li>
            </ul>  
        </td>
   </tr>
    <tr>
        <td align="left" width="40%"><?php admin_language_e('report_views_admin_location_report_template_TblHdItemRec'); ?></td>
        <td align="left" width="22%"><?php admin_language_e('report_views_admin_location_report_template_TblHdPeriod'); ?></td>
        <td align="right"><?php echo empty($location_id) ? 1* $invoice_by_total->item_received : 1* $invoice_by_location->number_of_item_received_share; ?></td>
   </tr>

   <tr>
        <td align="left" width="40%"><?php admin_language_e('report_views_admin_location_report_template_TblHdItemEnvScanned'); ?></td>
        <td align="left" width="22%"><?php admin_language_e('report_views_admin_location_report_template_TblHdPeriod'); ?></td>
        <td align="right"><?php echo empty($location_id) ? 1* $invoice_by_total->envelope_scanned : 1* $invoice_by_location->number_of_envelope_scan_share; ?></td>
   </tr> 
    <tr>
        <td  align="left" width="40%"><?php admin_language_e('report_views_admin_location_report_template_TblHdItemsContentScanned'); ?></td>
        <td align="left"  width="22%" ><?php admin_language_e('report_views_admin_location_report_template_TblHdPeriod'); ?></td>
        <td align="right"><?php echo empty($location_id) ? 1* $invoice_by_total->item_scanned : 1* $invoice_by_location->number_of_item_scan_share; ?></td>
   </tr> 
    <tr>
        <td align="left" width="40%"><?php admin_language_e('report_views_admin_location_report_template_TblHdItemsForwarded'); ?></td>
        <td align="left" width="22%"><?php admin_language_e('report_views_admin_location_report_template_TblHdPeriod'); ?></td>
        <td align="right"><?php echo empty($location_id) ? 1* $invoice_by_total->item_forwarded : 1* $invoice_by_location->number_of_item_forwarded_share; ?></td>
   </tr>               
    <tr>
        <td align="left" width="40%"><?php admin_language_e('report_views_admin_location_report_template_TblHdItemsStorage'); ?></td>
        <td align="left" width="22%"><?php admin_language_e('report_views_admin_location_report_template_TblHdPeriod'); ?></td>
        <td align="right"><?php echo empty($location_id) ? 1* $invoice_by_total->item_on_storage : 1* $invoice_by_location->number_of_storage_item_share; ?></td>
   </tr>
    <tr>
        <td align="left" width="40%"><?php admin_language_e('report_views_admin_location_report_template_TblHdNewReg'); ?></td>
        <td align="left" width="22%"><?php admin_language_e('report_views_admin_location_report_template_TblHdPeriod'); ?></td>
        <td align="right"><?php echo empty($location_id) ? 1* $invoice_by_total->new_registration : 1* $invoice_by_location->number_of_new_registration_share; ?></td>
   </tr>
    <tr class="separated">
        <td></td>
   </tr>
   
   <!-- Total invoiceable so far, Total invoiced so far, Invoices written this month,  Total payments made till end of this month -->
    <tr>
        <td></td>
    </tr>
   <tr>
        <td align="left" width="40%"><?php admin_language_e('report_views_admin_location_report_template_TblHdTotalInvoiceable'); ?></td>
        <td align="left" width="22%"><?php admin_language_e('report_views_admin_location_report_template_TblHdPeriod'); ?></td>
        <td align="right"><?php echo ($total_invoiceable_so_far >= 0) ?  APUtils::view_convert_number_in_currency($total_invoiceable_so_far, $currency_short, $currency_rate, $decimal_separator) . ' ' . $currency_short : APUtils::view_convert_number_in_currency($total_invoiceable_so_far, $currency_short, $currency_rate, $decimal_separator) . ' ' . $currency_short ?> </td>
   </tr>  
    <tr>
        <td align="left" width="40%"><?php admin_language_e('report_views_admin_location_report_template_TblHdTotalInvoiced'); ?></td>
        <td align="left" width="22%"><?php admin_language_e('report_views_admin_location_report_template_TblHdPeriod'); ?></td>
        <td align="right"><?php echo ($total_invoiced_so_far >= 0) ?  APUtils::view_convert_number_in_currency($total_invoiced_so_far, $currency_short, $currency_rate, $decimal_separator) . ' ' . $currency_short : APUtils::view_convert_number_in_currency($total_invoiced_so_far, $currency_short, $currency_rate, $decimal_separator) . ' ' . $currency_short ?> </td>
   </tr>
    <tr>
        <td  align="left" width="40%"><?php admin_language_e('report_views_admin_location_report_template_TblInvoicesMonth'); ?></td>
        <td align="left"  width="22%"><?php admin_language_e('report_views_admin_location_report_template_TblHdPeriod'); ?></td>
        <td align="right"><?php echo ($invoices_written_this_month >= 0) ?  APUtils::view_convert_number_in_currency($invoices_written_this_month, $currency_short, $currency_rate, $decimal_separator) . ' ' . $currency_short : APUtils::view_convert_number_in_currency($invoices_written_this_month, $currency_short, $currency_rate, $decimal_separator) . ' ' . $currency_short ?> </td>
   </tr>   
   <tr>
        <td align="left" width="40%"><?php admin_language_e('report_views_admin_location_report_template_TblTotalPayment'); ?></td>
        <td align="left" width="22%"><?php admin_language_e('report_views_admin_location_report_template_TblHdPeriod'); ?></td>
        <td align="right"><?php echo ($total_payments_made_till_end_of_this_month >= 0) ?  APUtils::view_convert_number_in_currency($total_payments_made_till_end_of_this_month, $currency_short, $currency_rate, $decimal_separator) . ' ' . $currency_short : APUtils::view_convert_number_in_currency($total_payments_made_till_end_of_this_month, $currency_short, $currency_rate, $decimal_separator) . ' ' . $currency_short ?> </td>
   </tr>
   <tr class="separated">
        <td></td>
   </tr>
  
    <tr>
        <td></td>
    </tr>
   <tr>
        <td  align="left" width="40%"><?php admin_language_e('report_views_admin_location_report_template_TblNewRevShare'); ?></td>
        <td align="left"  width="22%"><?php admin_language_e('report_views_admin_location_report_template_TblHdPeriod'); ?></td>
        <td align="right"><?php echo ($rev_share_total >= 0) ? "+ " . APUtils::view_convert_number_in_currency($rev_share_total, $currency_short, $currency_rate, $decimal_separator) . ' ' . $currency_short : APUtils::view_convert_number_in_currency($rev_share_total, $currency_short, $currency_rate, $decimal_separator) . ' ' . $currency_short ?> </td>
   </tr>  
    <tr>
        <td  align="left" width="40%"><?php admin_language_e('report_views_admin_location_report_template_TblNewCostLocationAdv'); ?></td>
        <td align="left"  width="22%"><?php admin_language_e('report_views_admin_location_report_template_TblHdPeriod'); ?></td>
        <td align="right"><?php  echo "- " . APUtils::view_convert_number_in_currency($advertising_cost, $currency_short, $currency_rate, $decimal_separator). ' ' . $currency_short ?></td>
   </tr>
    <tr>
        <td  align="left" width="40%"><?php admin_language_e('report_views_admin_location_report_template_TblNewHardwareAmor'); ?></td>
        <td align="left"  width="22%"><?php admin_language_e('report_views_admin_location_report_template_TblHdPeriod'); ?></td>
        <td align="right"><?php echo  "- " . APUtils::view_convert_number_in_currency($hardware_cost, $currency_short, $currency_rate, $decimal_separator). ' ' . $currency_short?></td>
   </tr>   
   <tr>
        <td align="left" width="40%"><?php admin_language_e('report_views_admin_location_report_template_TblNewLocalRep'); ?></td>
        <td align="left" width="22%"><?php admin_language_e('report_views_admin_location_report_template_TblHdPeriod'); ?></td>
        <td align="right"><?php echo "+ " . APUtils::view_convert_number_in_currency($location_external_cost, $currency_short, $currency_rate, $decimal_separator) . ' ' . $currency_short ?></td>
   </tr>
   <tr class="separated">
        <td></td>
   </tr>
  
    <tr>
        <td></td>
    </tr>
    <tr >
        <td align="left" width="40%"><span style="font-weight: bold;"><?php admin_language_e('report_views_admin_location_report_template_TblOpenBalance'); ?></span></td>
        <td align="left" width="22%"><?php admin_language_e('report_views_admin_location_report_template_TblHdPeriod'); ?></td>
        <td align="right"><?php echo ($current_open_balance >= 0) ? "+ " . APUtils::view_convert_number_in_currency($current_open_balance, $currency_short, $currency_rate, $decimal_separator). ' ' . $currency_short :  '<span color="red">' . APUtils::view_convert_number_in_currency($current_open_balance, $currency_short, $currency_rate, $decimal_separator). '</span>' . ' ' . $currency_short?></td>
   </tr>   
   <tr>
        <td></td>
   </tr>
</table>
<font size="6.5"><?php admin_language_e('report_views_admin_location_report_template_Status'); ?><?php echo $last_update_date . ': 1 EUR = ' .$currency_rate. ' '. $currency_short ?></font>

<?php if(empty($location_id)){?>
<div class="button_container">
    <div class="button-func"></div>
</div>
    <div style="font-weight: bold;text-align: left;"><?php admin_language_e('report_views_admin_location_report_template_CusGrowth'); ?>(<?php echo $start . '-' . $period ?>)</div>
    <br><br>
    <table width="100%"><tr><td><img src="uploads/images/tmp/bar_chart_location_report_total_<?php echo $select_year . $select_month ?>.png" alt="location report total"  /></td></tr></table>

<?php }else{?>
    <div style="font-weight: bold;text-align: left;"><?php admin_language_e('report_views_admin_location_report_template_CusGrowth'); ?>(<?php echo $start . '-' . $period ?>)</div>
     <br><br>
    <table width="100%"><tr><td><img src="uploads/images/tmp/bar_chart_location_report_<?php echo $location_name . '_' . $select_year . $select_month ?>.png" alt="location report"  /></td></tr></table>

<?php } ?>
