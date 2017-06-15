<?php 
    $hidden_class = "";
    if(empty($location_id)){
        $hidden_class = "hide";
    }
    
    $rate = 0.01;

    // Calculate total rev share
    $rev_share_total = 0;
    if(!empty($invoice_by_location)){
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

        $rev_share_total += round($invoice_by_location->cash_payment_free_for_item_delivery_amount_share* $rate * $invoice_by_location->cash_payment_free_for_item_delivery_share_rev, 2);
        $rev_share_total += round($invoice_by_location->customs_cost_import_amount_share* $rate * $invoice_by_location->customs_cost_import_share_rev, 2);
        $rev_share_total += round($invoice_by_location->customs_handling_fee_import_amount_share* $rate * $invoice_by_location->customs_handling_fee_import_share_rev, 2);
        $rev_share_total += round($invoice_by_location->address_verification_amount_share* $rate * $invoice_by_location->address_verification_share_rev, 2);
        $rev_share_total += round($invoice_by_location->special_service_fee_in_15min_intervalls_amount_share* $rate * $invoice_by_location->special_service_fee_in_15min_intervalls_share_rev, 2);
        $rev_share_total += round($invoice_by_location->personal_pickup_charge_amount_share* $rate * $invoice_by_location->personal_pickup_charge_share_rev, 2);

        $rev_share_total += round($invoice_by_location->paypal_fee_share* $rate * $invoice_by_location->paypal_fee_share_rev, 2);
        $rev_share_total += round($invoice_by_location->other_local_invoice_share* $rate * $invoice_by_location->other_local_invoice_share_rev, 2);
        $rev_share_total += round($invoice_by_location->credit_note_given_share* $rate * $invoice_by_location->credit_note_given_share_rev, 2);
    }

    // cash upfront
    $total_cash_upfront = 0;

    // Location earning
    $location_earning = $rev_share_total - $total_cash_upfront;
    ?>
<style>
table.settings_border_template {
	border-collapse: separate;
	border-color: gray;
	border-spacing: 0px;
	border-top: 1px solid #dadada;
	border-left: 1px solid #dadada;
}

table.settings_border_template tr {
	border: 1px solid #DADADA;
}

table.settings_border_template tr th, table.settings_border_template tr td {
	border-bottom: 1px solid none;
	border-style: none solid solid none;
	border-width: 0 1px 1px 0;
	border-color: #DADADA;
    font-size: 27px;
	
}

.input-width-130-template {
    width: 130px;
    
}
.input-width-32-template {
    width: 32px;
    text-align:right;
}

.input-width-240-template {
    width: 240px;
    
}
.input-width-42-template {
    width: 42px;
    text-align:right;
}
br {
   line-height: 18%;
}
</style>
<?php
   
  if($check_detail_template == true){
       $width = '100%';
       $class_settings = 'settings_border_template';
       $class_input_01 = 'input-width-130-template';
       $class_input_02 = 'input-width-32-template ';
       $class_input_03 = 'input-width-240-template';
       $class_input_04 = 'input-width-42-template';
       $class_input_05 = 'input-width-42-template';
       $cellpadding = '2.5px;';
   }else{
       $width = '1200px';
       $class_settings = 'settings_border';
       $class_input_01 = 'input-width-200';
       $class_input_02 = 'input-width-100';
       $class_input_03 = 'input-width-200';
       $class_input_04 = 'input-width-100';
       $class_input_05 = 'input-width-100';
       $cellpadding = '0px;';
   }
?>

<?php if($check_detail_template == true){?>
<br>
<div style="font-weight: bold;text-align: left;margin: -5px 0px 1px 0px"><?php admin_language_e('report_views_admin_transaction_report_Header'); ?><?php echo $period ?></div>
<?php }else{?>
<div class="button_container">
    <div class="button-func"></div>
</div>
<?php }?>
<div id="gridwraper" style="margin: 0px;">
    <table class="<?php echo $class_settings?>" style="width: <?php echo $width?>" cellpadding="<?php echo $cellpadding?>">
        <tr>
            <th class="<?php echo $class_input_01;?>">&nbsp;</th>
            <td class="<?php echo $class_input_02;?>">&nbsp;</td>

            <th class="<?php echo $class_input_01;?>"><?php admin_language_e('report_views_admin_location_report_detail_TblHdNumPostbox'); ?></th>
            <td class="<?php echo $class_input_02;?>"><?php echo 1* ($invoice_by_location ? $invoice_by_location->number_of_postbox_share : 0) ?></td>

            <th class="<?php echo $class_input_01;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdItemsReceived'); ?></th>
            <td class="<?php echo $class_input_02;?>"><?php echo 1* ($invoice_by_location ? $invoice_by_location->number_of_item_received_share: 0) ?></td>

            <th class="<?php echo $class_input_01;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdEnvScanned'); ?></th>
            <td class="<?php echo $class_input_02;?>"><?php echo 1* ($invoice_by_location ? $invoice_by_location->number_of_envelope_scan_share: 0) ?></td>
        </tr>
        <tr>
            <th class="<?php echo $class_input_01;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdItemsScanned'); ?></th>
            <td class="<?php echo $class_input_02;?>"><?php echo 1* ($invoice_by_location ? $invoice_by_location->number_of_item_scan_share: 0) ?></td>

            <th class="<?php echo $class_input_01;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdItemsForwarded'); ?></th>
            <td class="<?php echo $class_input_02;?>"><?php echo 1* ($invoice_by_location ? $invoice_by_location->number_of_item_forwarded_share: 0) ?></td>

            <th class="<?php echo $class_input_01;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdItemsStorage'); ?></th>
            <td class="<?php echo $class_input_02;?>"><?php echo 1* ($invoice_by_location ? $invoice_by_location->number_of_storage_item_share: 0) ?></td>

            <th class="<?php echo $class_input_01;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdNewReg'); ?></th>
            <td class="<?php echo $class_input_02;?>"><?php echo 1* ($invoice_by_location ? $invoice_by_location->number_of_new_registration_share: 0) ?></td>
        </tr>
        
        <tr>
            <th class="<?php echo $class_input_01;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdNeverActivatedDel'); ?></th>
            <td class="<?php echo $class_input_02;?>"><?php echo 1* ($invoice_by_location ? $invoice_by_location->number_of_never_activated_deleted: 0) ?></td>

            <th class="<?php echo $class_input_01;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdManuaDel'); ?></th>
            <td class="<?php echo $class_input_02;?>"><?php echo 1* ($invoice_by_location ? $invoice_by_location->number_of_manual_deleted_share: 0) ?></td>

            <th class="<?php echo $class_input_01;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdAutoDel'); ?></th>
            <td class="<?php echo $class_input_02;?>"><?php echo 1* ($invoice_by_location ? $invoice_by_location->number_of_automatic_deleted_share: 0) ?></td>

            <th class="<?php echo $class_input_01;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdNumCus'); ?></th>
            <td class="<?php echo $class_input_02;?>"><?php echo 1* ($invoice_by_location ? $invoice_by_location->number_of_customers_share: 0) ?></td>
        </tr>
    </table>
</div>
<div id="gridwraper02" style="margin-top: 10px;" >
    <!-- summary -->
    <table class="<?php echo $class_settings?>" style="width: <?php echo $width?>" cellpadding="<?php echo $cellpadding?>">
        <tr>
            <th class="<?php echo $class_input_03;?>">&nbsp;</th>
            <th class="<?php echo $class_input_04;?>" style="text-align: center;">#</th>
            <th class="input-width-100 <?=$hidden_class?>" style="text-align: center;">Price</th>
            <th class="input-width-100" style="text-align: center;"><?php admin_language_e('report_views_admin_transaction_report_TblHdTotal'); ?>(<?php echo $currency_short ?>)</th>
            <th class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: center;">rev share location</th>
            <th class="input-width-100" style="text-align: center;"><?php admin_language_e('report_views_admin_transaction_report_TblHdTotalRevShare'); ?>(<?php echo $currency_short ?>)</th>
        </tr>
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdAsyougoPostbox'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo ($invoice_by_location ? $invoice_by_location->free_postbox_quantity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->free_postboxes_netprice: $pricing[APConstants::FREE_TYPE]['postbox_fee_as_you_go']->item_value), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->free_postboxes_amount_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->free_postboxes_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->free_postboxes_amount_share * $invoice_by_location->free_postboxes_share_rev: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdPrivatePostbox'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo ($invoice_by_location ? $invoice_by_location->private_postbox_quantity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->private_postboxes_netprice: $pricing[APConstants::PRIVATE_TYPE]['postbox_fee']->item_value), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->private_postboxes_amount_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->private_postboxes_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->private_postboxes_amount_share * $invoice_by_location->private_postboxes_share_rev: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdBussPostbox'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo ($invoice_by_location ? $invoice_by_location->business_postbox_quantity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->business_postboxes_netprice: $pricing[APConstants::BUSINESS_TYPE]['postbox_fee']->item_value), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->business_postboxes_amount_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->business_postboxes_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->business_postboxes_amount_share * $invoice_by_location->business_postboxes_share_rev: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>
   <?php if($check_detail_template == true){?>
     <br>
    <?php }?>
    <!-- Incomming -->
    <table class="<?php echo $class_settings?>" style="width: <?php echo $width?>;" cellpadding="<?php echo $cellpadding?>">
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdIncomingAsyougoPostbox'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo ($invoice_by_location ? $invoice_by_location->free_incoming_quantity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->incomming_items_free_netprice: $pricing[APConstants::FREE_TYPE]['additional_incomming_items']->item_value), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->incomming_items_free_account_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->incomming_items_free_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->incomming_items_free_account_share * $invoice_by_location->incomming_items_free_share_rev: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdIncomingPrivatePostbox'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo ($invoice_by_location ? $invoice_by_location->private_incoming_quantity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->incomming_items_private_netprice: $pricing[APConstants::PRIVATE_TYPE]['additional_incomming_items']->item_value), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->incomming_items_private_account_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->incomming_items_private_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->incomming_items_private_account_share * $invoice_by_location->incomming_items_private_share_rev: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdIncomingBussPostbox'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo ($invoice_by_location ? $invoice_by_location->business_incoming_item_quantity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->incomming_items_business_netprice: $pricing[APConstants::BUSINESS_TYPE]['additional_incomming_items']->item_value), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->incomming_items_business_account_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->incomming_items_business_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->incomming_items_business_account_share * $invoice_by_location->incomming_items_business_share_rev: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>
    <?php if($check_detail_template == true){?>
     <br>
    <?php }?>
    <!-- envelope scan -->
    <table class="<?php echo $class_settings?>" style="width: <?php echo $width?>" cellpadding="<?php echo $cellpadding?>">	
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdEnvScanAsyougoPostbox'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo ($invoice_by_location ? $invoice_by_location->free_envelope_scan_quantity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->envelope_scan_free_netprice: $pricing[APConstants::FREE_TYPE]['envelop_scanning']->item_value), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->envelope_scan_free_account_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->envelope_scan_free_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->envelope_scan_free_account_share * $invoice_by_location->envelope_scan_free_share_rev: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdEnvScanPrivatePostbox'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo ($invoice_by_location ? $invoice_by_location->private_envelope_scan_quantity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->envelope_scan_private_netprice: $pricing[APConstants::PRIVATE_TYPE]['envelop_scanning']->item_value), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->envelope_scan_private_account_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->envelope_scan_private_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->envelope_scan_private_account_share * $invoice_by_location->envelope_scan_private_share_rev: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdEnvScanBussPostbox'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo ($invoice_by_location ? $invoice_by_location->business_envelope_scan_quantity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->envelope_scan_business_netprice: $pricing[APConstants::BUSINESS_TYPE]['envelop_scanning']->item_value), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->envelope_scan_business_account_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->envelope_scan_business_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->envelope_scan_business_account_share *$invoice_by_location->envelope_scan_business_share_rev: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>
     <?php if($check_detail_template == true){?>
     <br>
    <?php }?>
    <!-- item scan  scan -->
    <table class="<?php echo $class_settings?>" style="width: <?php echo $width?>" cellpadding="<?php echo $cellpadding?>">	
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdItemScanAsyougoPostbox'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo ($invoice_by_location ? $invoice_by_location->free_item_scan_quantity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->item_scan_free_netprice: $pricing[APConstants::FREE_TYPE]['opening_scanning']->item_value), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->item_scan_free_account_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->item_scan_free_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->item_scan_free_account_share * $invoice_by_location->item_scan_free_share_rev: 0) , $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdItemScanPrivatePostbox'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo ($invoice_by_location ? $invoice_by_location->private_item_scan_quantity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->item_scan_private_netprice: $pricing[APConstants::PRIVATE_TYPE]['opening_scanning']->item_value), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->item_scan_private_account_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->item_scan_private_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->item_scan_private_account_share * $invoice_by_location->item_scan_private_share_rev: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdItemScanBussPostbox'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo ($invoice_by_location ? $invoice_by_location->business_item_scan_quantity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->item_scan_business_netprice: $pricing[APConstants::BUSINESS_TYPE]['opening_scanning']->item_value), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->item_scan_business_account_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->item_scan_business_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->item_scan_business_account_share * $invoice_by_location->item_scan_business_share_rev: 0) , $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>
     <?php if($check_detail_template == true){?>
     <br>
    <?php }?>
    <!-- additional pages scan -->
    <table class="<?php echo $class_settings?>" style="width: <?php echo $width?>" cellpadding="<?php echo $cellpadding?>">	
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdAddPagesAsyougoPostbox'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo 1 * ($invoice_by_location ? $invoice_by_location->free_additional_page_quantity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->additional_pages_scanning_free_netprice: $pricing[APConstants::FREE_TYPE]['additional_included_page_opening_scanning']->item_value), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency( ($invoice_by_location ? $invoice_by_location->additional_pages_scanning_free_amount_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->additional_pages_scanning_free_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->additional_pages_scanning_free_amount_share * $invoice_by_location->additional_pages_scanning_free_share_rev: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdAddPagesPrivatePostbox'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo 1 * ($invoice_by_location ? $invoice_by_location->private_additional_page_quantity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->additional_pages_scanning_private_netprice: $pricing[APConstants::PRIVATE_TYPE]['additional_included_page_opening_scanning']->item_value), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency( ($invoice_by_location ? $invoice_by_location->additional_pages_scanning_private_amount_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?><?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->additional_pages_scanning_private_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->additional_pages_scanning_private_amount_share * $invoice_by_location->additional_pages_scanning_private_share_rev: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdAddPagesBussPostbox'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo 1 * ($invoice_by_location ? $invoice_by_location->business_additional_page_quantity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->additional_pages_scanning_business_netprice: $pricing[APConstants::BUSINESS_TYPE]['additional_included_page_opening_scanning']->item_value), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency( ($invoice_by_location ? $invoice_by_location->additional_pages_scanning_business_amount_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->additional_pages_scanning_business_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->additional_pages_scanning_business_amount_share * $invoice_by_location->additional_pages_scanning_business_share_rev: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>
    <?php if($check_detail_template == true){?>
     <br>
    <?php }?>
    <!-- Forwading -->
    <table class="<?php echo $class_settings?>" style="width: <?php echo $width?>" cellpadding="<?php echo $cellpadding?>">	
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdForwardChargesTotal'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo 1 * ($invoice_by_location ? ($invoice_by_location->fowarding_charge_postal_quantity_share + $invoice_by_location->fowarding_charge_fee_quantity_share): 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(1 * ($invoice_by_location ? ($invoice_by_location->forwarding_charges_postal_share + $invoice_by_location->forwarding_charges_fee_share): 0), $currency_short,$currency_rate, $decimal_separator) ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;">0%</td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::number_format(0, 2) ?></td>
        </tr>
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdForwardChargesPostal'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo 1 * ($invoice_by_location ? ($invoice_by_location->fowarding_charge_postal_quantity_share + $invoice_by_location->fowarding_charge_fee_quantity_share): 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(1 * ($invoice_by_location ? $invoice_by_location->forwarding_charges_postal_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;">0%</td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::number_format(0, 2) ?></td>
        </tr>
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdForwardChargesFee'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo 1 * ($invoice_by_location ? ($invoice_by_location->fowarding_charge_postal_quantity_share + $invoice_by_location->fowarding_charge_fee_quantity_share): 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(1 * ($invoice_by_location ? $invoice_by_location->forwarding_charges_fee_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->forwarding_charges_fee_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->forwarding_charges_fee_share * $invoice_by_location->forwarding_charges_fee_share_rev: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>
    <?php if($check_detail_template == true){?>
     <br>
    <?php }?>
    <!-- storing letters scan -->
    <table class="<?php echo $class_settings?>" style="width: <?php echo $width?>" cellpadding="<?php echo $cellpadding?>">	
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdStoringLetterAsyougoPostbox'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo 1 * ($invoice_by_location ? $invoice_by_location->free_storage_letter_quanity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->storing_letters_free_netprice: $pricing[APConstants::FREE_TYPE]['storing_items_over_free_letter']->item_value), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency( ($invoice_by_location ? $invoice_by_location->storing_letters_free_account_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->storing_letters_free_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->storing_letters_free_account_share * $invoice_by_location->storing_letters_free_share_rev: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdStoringLetterPrivatePostbox'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo 1 * ($invoice_by_location ? $invoice_by_location->private_storage_letter_quanity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->storing_letters_private_netprice: $pricing[APConstants::PRIVATE_TYPE]['storing_items_over_free_letter']->item_value), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency( ($invoice_by_location ? $invoice_by_location->storing_letters_private_account_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->storing_letters_private_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->storing_letters_private_account_share * $invoice_by_location->storing_letters_private_share_rev: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdStoringLetterBussPostbox'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo 1 * ($invoice_by_location ? $invoice_by_location->business_storage_letter_quanity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->storing_letters_business_netprice: $pricing[APConstants::BUSINESS_TYPE]['storing_items_over_free_letter']->item_value), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->storing_letters_business_account_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->storing_letters_business_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->storing_letters_business_account_share * $invoice_by_location->storing_letters_business_share_rev: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>
    <?php if($check_detail_template == true){?>
     <br>
    <?php }?>
    <!-- storing packages scan -->
    <table class="<?php echo $class_settings?>" style="width: <?php echo $width?>" cellpadding="<?php echo $cellpadding?>">	
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdStoringPackagesAsyougoPostbox'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo 1 * ($invoice_by_location ? $invoice_by_location->free_storage_package_quanity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->storing_packages_free_netprice: $pricing[APConstants::FREE_TYPE]['storing_items_over_free_packages']->item_value), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->storing_packages_free_account_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->storing_packages_free_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->storing_packages_free_account_share * $invoice_by_location->storing_packages_free_share_rev: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdStoringPackagesPrivatePostbox'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo 1 * ($invoice_by_location ? $invoice_by_location->private_storage_package_quanity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->storing_packages_private_netprice: $pricing[APConstants::PRIVATE_TYPE]['storing_items_over_free_packages']->item_value), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->storing_packages_private_account_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->storing_packages_private_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->storing_packages_private_account_share * $invoice_by_location->storing_packages_private_share_rev: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdStoringPackagesBussPostbox'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo 1 * ($invoice_by_location ? $invoice_by_location->business_storage_package_quanity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->storing_packages_business_netprice: $pricing[APConstants::BUSINESS_TYPE]['storing_items_over_free_packages']->item_value), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->storing_packages_business_account_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->storing_packages_business_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->storing_packages_business_account_share * $invoice_by_location->storing_packages_business_share_rev: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>
     <?php if($check_detail_template == true){?>
     <br>
    <?php }?>
    <!-- custom declaration -->
    <table class="<?php echo $class_settings?>" style="width: <?php echo $width?>" cellpadding="<?php echo $cellpadding?>">	
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdCusDeclarations'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo ($invoice_by_location ? $invoice_by_location->custom_declaration_quantity_share: 0);?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo ''; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->custom_declaration_outgoing_price_01_amount_share + $invoice_by_location->custom_declaration_outgoing_price_02_amount_share : 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->custom_declaration_outgoing_price_01_share_rev : 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->custom_declaration_outgoing_price_01_share_rev
                                *($invoice_by_location->custom_declaration_outgoing_price_01_amount_share + $invoice_by_location->custom_declaration_outgoing_price_02_amount_share) : 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>
     <?php if($check_detail_template == true){?>
     <br>
    <?php }?>
    <!-- cash payment -->
    <table class="<?php echo $class_settings?>" style="width: <?php echo $width?>" cellpadding="<?php echo $cellpadding?>">	
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdCashPaymentFee'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo 1 * ($invoice_by_location ? $invoice_by_location->cash_payment_fee_quantity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo ''; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->cash_payment_free_for_item_delivery_amount_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->cash_payment_free_for_item_delivery_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->cash_payment_free_for_item_delivery_share_rev * $invoice_by_location->cash_payment_free_for_item_delivery_amount_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>
    <?php if($check_detail_template == true){?>
     <br>
    <?php }?>
    <!-- customs cost import -->
    <table class="<?php echo $class_settings?>" style="width: <?php echo $width?>" cellpadding="<?php echo $cellpadding?>">	
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdCustomsCostImport'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo 1 * ($invoice_by_location ? $invoice_by_location->import_custom_fee_quantity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo ''; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->customs_cost_import_amount_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?><?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->customs_cost_import_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->customs_cost_import_share_rev * $invoice_by_location->customs_cost_import_amount_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdImportCustomFee'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo 1 * ($invoice_by_location ? $invoice_by_location->import_custom_fee_quantity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo ''; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->customs_handling_fee_import_amount_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->customs_handling_fee_import_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->customs_handling_fee_import_share_rev * $invoice_by_location->customs_handling_fee_import_amount_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>
     <?php if($check_detail_template == true){?>
     <br>
    <?php }?>
    <!-- address verification -->
    <table class="<?php echo $class_settings?>" style="width: <?php echo $width?>" cellpadding="<?php echo $cellpadding?>">	
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdAddressVerification'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo 1 * ($invoice_by_location ? $invoice_by_location->address_verification_quantity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo ''; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->address_verification_amount_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->address_verification_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->address_verification_share_rev * $invoice_by_location->address_verification_amount_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdSpecialSrvFee'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo 1 * ($invoice_by_location ? $invoice_by_location->special_service_fee_quantity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo ''; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->special_service_fee_in_15min_intervalls_amount_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->special_service_fee_in_15min_intervalls_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->special_service_fee_in_15min_intervalls_share_rev * $invoice_by_location->special_service_fee_in_15min_intervalls_amount_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdPersonalPickupCharge'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo 1 * ($invoice_by_location ? $invoice_by_location->peronsal_pickup_charge_quantity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo ''; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->personal_pickup_charge_amount_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->personal_pickup_charge_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->personal_pickup_charge_share_rev * $invoice_by_location->personal_pickup_charge_amount_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdPaypalTranFee'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo ($invoice_by_location ? $invoice_by_location->paypal_transaction_fee_quantity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo ""; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->paypal_fee_share: 0), $currency_short,$currency_rate, $decimal_separator) ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->paypal_fee_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate * ($invoice_by_location ? $invoice_by_location->paypal_fee_share_rev * $invoice_by_location->paypal_fee_share: 0), $currency_short,$currency_rate, $decimal_separator) ?></td>
        </tr>
    </table>
     <?php if($check_detail_template == true){?>
     <br>
    <?php }?>
    <!-- other invoice local-->
    <table class="<?php echo $class_settings?>" style="width: <?php echo $width?>" cellpadding="<?php echo $cellpadding?>">	
        <tr>
            <th class="<?php echo $class_input_03;?>"><?php admin_language_e('report_views_admin_transaction_report_TblHdOtherLocalInv'); ?></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo ($invoice_by_location ? $invoice_by_location->other_local_invoice_quantity_share: 0); ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->other_local_invoice_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->other_local_invoice_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate *($invoice_by_location ? $invoice_by_location->other_local_invoice_share * $invoice_by_location->other_local_invoice_share_rev: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>
     <?php if($check_detail_template == true){?>
     <br>
    <?php }?>

    <!-- Credit note -->
    <table class="<?php echo $class_settings?>" style="width: <?php echo $width?>" cellpadding="<?php echo $cellpadding?>">	
        <tr>
            <th class="<?php echo $class_input_03;?>"><a href="#" id="creditNoteListButton"><?php admin_language_e('report_views_admin_transaction_report_TblHdCreditNote'); ?></a></th>
            <td class="<?php echo $class_input_04;?>" style="text-align: right;"><?php echo 1 * ($invoice_by_location ? $invoice_by_location->creditnote_quantity_share: 0) ?></td>
            <td class="input-width-100 <?=$hidden_class?>" style="text-align: right;"><?php echo ''; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->credit_note_given_share: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="<?php echo $class_input_05;?> <?=$hidden_class?>" style="text-align: right;"><?php echo APUtils::number_format(($invoice_by_location ? $invoice_by_location->credit_note_given_share_rev: 0), 0) . '%'; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rate *($invoice_by_location ? $invoice_by_location->credit_note_given_share * $invoice_by_location->credit_note_given_share_rev: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>
      <?php if($check_detail_template == true){?>
     <br>
    <?php }?>
    <!-- Total -->
    <table  class="<?php echo $class_settings?>" style="width: <?php echo $width?>" cellpadding="<?php echo $cellpadding?>">	
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_transaction_report_TblHdTotal'); ?></th>
            <td class="input-width-100" style="text-align: right;">&nbsp;</td>
            <td class="input-width-100  <?=$hidden_class?>" style="text-align: right;">&nbsp;</td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice->total_invoice: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100  <?=$hidden_class?>" style="text-align: right;">&nbsp;</td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($rev_share_total, $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <td colspan="<?php echo $hidden_class? 2: 4; ?>"></td>
            <td colspan=1 class="input-width-100" style="text-align: left;"><?php admin_language_e('report_views_admin_transaction_report_TblHdCashExpenditurePartner'); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice_by_location ? $invoice_by_location->cash_expenditure_of_partner: 0), $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <td colspan="<?php echo $hidden_class? 2: 4; ?>"></td>
            <th colspan=1 class="input-width-100" style="text-align: left;"><?php admin_language_e('report_views_admin_transaction_report_TblHdLocationEarning'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($location_earning, $currency_short,$currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>
</div>