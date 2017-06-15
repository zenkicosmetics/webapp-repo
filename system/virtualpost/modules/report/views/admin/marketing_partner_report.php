<style>
.input-error {
    border: 1px #800 solid !important;
    color: #800;
}
</style>
<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('report_views_admin_marketing_partner_report_Header'); ?></h2>
</div>
<div class="ym-grid mailbox">
    <form id="locationReportingSearchForm"
          action="<?php echo base_url() ?>admin/report/marketing_partner"
          method="post">
        <div class="ym-gl">
            <div class="ym-grid input-item">
                <div class="ym-g30 ym-gl" style="width: 100px">
                    <label style="text-align: left;"><?php admin_language_e('report_views_admin_marketing_partner_report_LblPartner'); ?></label>
                </div>
                <div class="ym-g70 ym-gl">
                    <?php
                    echo my_form_dropdown(array(
                        "data" => $marketing_partners,
                        "value_key" => 'partner_id',
                        "label_key" => 'partner_name',
                        "value" => $selected_partner,
                        "name" => 'partner',
                        "id" => 'partner',
                        "clazz" => 'input-txt',
                        "style" => 'width: 150px',
                        "has_empty" => false
                    ));
                    ?>

                    <?php
                    echo my_form_dropdown(array(
                        "data" => $list_year,
                        "value_key" => 'id',
                        "label_key" => 'label',
                        "value" => $select_year,
                        "name" => 'year',
                        "id" => 'year',
                        "clazz" => 'input-txt',
                        "style" => 'width: 70px',
                        "has_empty" => false
                    ));
                    ?>
                    <?php
                    echo my_form_dropdown(array(
                        "data" => $list_month,
                        "value_key" => 'id',
                        "label_key" => 'label',
                        "value" => $select_month,
                        "name" => 'month',
                        "id" => 'month',
                        "clazz" => 'input-txt',
                        "style" => 'width: 50px',
                        "has_empty" => false
                    ));
                    ?>
                    <button style="margin-left: 30px"
                            id="locationReportingButton" class="admin-button"><?php admin_language_e('report_views_admin_marketing_partner_report_BtnSearch'); ?></button>
                    <?php if(APContext::isSupperAdminUser()): ?>
<!--                        <button id="generateReport" type="button"  class="admin-button">Generate Report</button>-->
                    <?php endif;?>
                </div>
            </div>
        </div>
    </form>
</div>

<?php 
    $hidden_class = "";

    // Calculate total
    $total = 0;
    $total_discount = 0;
    $total_share = 0;
    
    $total += round($invoice->free_postboxes_amount,2 );
    $total += round($invoice->private_postboxes_amount,2 );
    $total += round($invoice->business_postboxes_amount,2 );

    $total += round($invoice->incomming_items_free_account,2 );
    $total += round($invoice->incomming_items_private_account,2 );
    $total += round($invoice->incomming_items_business_account,2 );

    $total += round($invoice->envelope_scan_free_account,2 );
    $total += round($invoice->envelope_scan_private_account,2 );
    $total += round($invoice->envelope_scan_business_account,2 );

    $total += round($invoice->item_scan_free_account,2 );
    $total += round($invoice->item_scan_private_account,2 );
    $total += round($invoice->item_scan_business_account,2 );

    $total += round($invoice->additional_pages_scanning_free_amount,2 );
    $total += round($invoice->additional_pages_scanning_private_amount,2 );
    $total += round($invoice->additional_pages_scanning_business_amount,2 );
    
    $total += round($invoice->forwarding_charges_postal + $invoice->forwarding_charges_fee,2 );

    $total += round($invoice->storing_letters_free_account,2 );
    $total += round($invoice->storing_letters_private_account,2 );
    $total += round($invoice->storing_letters_business_account,2 );

    $total += round($invoice->storing_packages_free_account,2 );
    $total += round($invoice->storing_packages_private_account,2 );
    $total += round($invoice->storing_packages_business_account,2 );

    $total += round($invoice->custom_declaration_outgoing_price_01, 2);
    $total += round($invoice->custom_declaration_outgoing_price_02, 2);

    //$total += round($cash_payment_for_item_delivery->total_amount, 2);
    $total += round($invoice->cash_payment_free_for_item_delivery_amount, 2);
    $total += round($invoice->customs_cost_import_amount, 2);
    $total += round($invoice->customs_handling_fee_import_amount, 2);
    $total += round($invoice->address_verification_amount, 2);
    $total += round($invoice->special_service_fee_in_15min_intervalls_amount, 2);
    $total += round($invoice->personal_pickup_charge_amount, 2);

    $total += round($invoice->paypal_fee, 2);

    // other invoice
    $total += round($invoice->other_local_invoice, 2);

    // Add credit note
    $total += round($invoice->credit_note_given, 2);
    
    // ===================== total discount=======================
    $total_discount += round($invoice->free_postboxes_amount_discount,2 );
    $total_discount += round($invoice->private_postboxes_amount_discount,2 );
    $total_discount += round($invoice->business_postboxes_amount_discount,2 );

    $total_discount += round($invoice->incomming_items_free_account_discount,2 );
    $total_discount += round($invoice->incomming_items_private_account_discount,2 );
    $total_discount += round($invoice->incomming_items_business_account_discount,2 );

    $total_discount += round($invoice->envelope_scan_free_account_discount,2 );
    $total_discount += round($invoice->envelope_scan_private_account_discount,2 );
    $total_discount += round($invoice->envelope_scan_business_account_discount,2 );

    $total_discount += round($invoice->item_scan_free_account_discount,2 );
    $total_discount += round($invoice->item_scan_private_account_discount,2 );
    $total_discount += round($invoice->item_scan_business_account_discount,2 );

    $total_discount += round($invoice->additional_pages_scanning_free_amount_discount,2 );
    $total_discount += round($invoice->additional_pages_scanning_private_amount_discount,2 );
    $total_discount += round($invoice->additional_pages_scanning_business_amount_discount,2 );
    
    $total_discount += round($invoice->forwarding_charges_postal_discount + $invoice->forwarding_charges_fee_discount);

    $total_discount += round($invoice->storing_letters_free_account_discount,2 );
    $total_discount += round($invoice->storing_letters_private_account_discount,2 );
    $total_discount += round($invoice->storing_letters_business_account_discount,2 );

    $total_discount += round($invoice->storing_packages_free_account_discount,2 );
    $total_discount += round($invoice->storing_packages_private_account_discount,2 );
    $total_discount += round($invoice->storing_packages_business_account_discount,2 );

    $total_discount += round($invoice->custom_declaration_outgoing_price_01_discount, 2);
    $total_discount += round($invoice->custom_declaration_outgoing_price_02_discount, 2);

    //$total += round($cash_payment_for_item_delivery->total_amount, 2);
    $total_discount += round($invoice->cash_payment_free_for_item_delivery_amount_discount, 2);
    $total_discount += round($invoice->customs_cost_import_amount_discount, 2);
    $total_discount += round($invoice->customs_handling_fee_import_amount_discount, 2);
    $total_discount += round($invoice->address_verification_amount_discount, 2);
    $total_discount += round($invoice->special_service_fee_in_15min_intervalls_amount_discount, 2);
    $total_discount += round($invoice->personal_pickup_charge_amount_discount, 2);

    $total_discount += round($invoice->paypal_fee_discount, 2);

    // other invoice
    $total_discount += round($invoice->other_local_invoice_discount, 2);

    // Add credit note
    $total_discount += round($invoice->credit_note_given_discount, 2);
    
    // ==================== total share===========================
    $total_share += round($invoice->free_postboxes_amount_share,2 );
    $total_share += round($invoice->private_postboxes_amount_share,2 );
    $total_share += round($invoice->business_postboxes_amount_share,2 );

    $total_share += round($invoice->incomming_items_free_account_share,2 );
    $total_share += round($invoice->incomming_items_private_account_share,2 );
    $total_share += round($invoice->incomming_items_business_account_share,2 );

    $total_share += round($invoice->envelope_scan_free_account_share,2 );
    $total_share += round($invoice->envelope_scan_private_account_share,2 );
    $total_share += round($invoice->envelope_scan_business_account_share,2 );

    $total_share += round($invoice->item_scan_free_account_share,2 );
    $total_share += round($invoice->item_scan_private_account_share,2 );
    $total_share += round($invoice->item_scan_business_account_share,2 );

    $total_share += round($invoice->additional_pages_scanning_free_amount_share,2 );
    $total_share += round($invoice->additional_pages_scanning_private_amount_share,2 );
    $total_share += round($invoice->additional_pages_scanning_business_amount_share,2 );
    
    $total_share += round($invoice->forwarding_charges_postal_share + $invoice->forwarding_charges_fee_share);

    $total_share += round($invoice->storing_letters_free_account_share,2 );
    $total_share += round($invoice->storing_letters_private_account_share,2 );
    $total_share += round($invoice->storing_letters_business_account_share,2 );

    $total_share += round($invoice->storing_packages_free_account_share,2 );
    $total_share += round($invoice->storing_packages_private_account_share,2 );
    $total_share += round($invoice->storing_packages_business_account_share,2 );

    $total_share += round($invoice->custom_declaration_outgoing_price_01_share, 2);
    $total_share += round($invoice->custom_declaration_outgoing_price_02_share, 2);

    //$total += round($cash_payment_for_item_delivery->total_amount, 2);
    $total_share += round($invoice->cash_payment_free_for_item_delivery_amount_share, 2);
    $total_share += round($invoice->customs_cost_import_amount_share, 2);
    $total_share += round($invoice->customs_handling_fee_import_amount_share, 2);
    $total_share += round($invoice->address_verification_amount_share, 2);
    $total_share += round($invoice->special_service_fee_in_15min_intervalls_amount_share, 2);
    $total_share += round($invoice->personal_pickup_charge_amount_share, 2);

    $total_share += round($invoice->paypal_fee_share, 2);

    // other invoice
    $total_share += round($invoice->other_local_invoice_share, 2);

    // Add credit note
    $total_share += round($invoice->credit_note_given_share, 2);
    
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

.input-width-240-template {
    width: 130px;
}
.input-width-60-template {
    width: 32px;
}

br {
   line-height: 18%;
}
</style>
<?php
       $width = '1200px';
       $class_settings = 'settings_border';
       $class_input_01 = 'input-width-200';
       $class_input_02 = 'input-width-100';
       $cellpadding = '0px;';
?>
<div class="button_container">
    <div class="button-func"></div>
</div>
<div id="gridwraper0" style="margin: 0px;" >
    <table style="width: 800px;" cellpadding="<?php echo $cellpadding?>" class="<?php echo $class_settings?>">
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdCommissionReg'); ?></th>
            <td class="input-width-200"><?php echo APUtils::view_convert_number_in_currency($invoice->commission_for_registration, $currency_short, $currency_rate, $decimal_separator) ; ?> (<?php echo $currency_short ?>)</td>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdCommissionAtc'); ?></th>
            <td class="input-width-200"><?php echo APUtils::view_convert_number_in_currency($invoice->commission_for_activation, $currency_short, $currency_rate, $decimal_separator) ;?> (<?php echo $currency_short ?>)</td>
        </tr>
    </table>
</div>
<div id="gridwraper" style="margin: 0px;">
    <table class="<?php echo $class_settings?>" style="width: <?php echo $width?>" cellpadding="<?php echo $cellpadding?>">
        <tr>
            <th class="<?php echo $class_input_01;?>"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdNumAcc'); ?></th>
            <td class="<?php echo $class_input_02;?>"><?php echo $invoice ? $invoice->number_of_account: 0 ?></td>

            <th class="<?php echo $class_input_01;?>"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdNumPostbox'); ?></th>
            <td class="<?php echo $class_input_02;?>"><?php echo $invoice ? $invoice->number_of_postbox: 0 ?></td>

            <th class="<?php echo $class_input_01;?>"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdItemsReceived'); ?></th>
            <td class="<?php echo $class_input_02;?>"><?php echo $invoice ? $invoice->number_of_item_received: 0 ?></td>

            <th class="<?php echo $class_input_01;?>"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdEnvScanned'); ?></th>
            <td class="<?php echo $class_input_02;?>"><?php echo $invoice ? $invoice->number_of_envelope_scan: 0 ?></td>
        </tr>
        <tr>
            <th class="<?php echo $class_input_01;?>"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdItemsScanned'); ?></th>
            <td class="<?php echo $class_input_02;?>"><?php echo $invoice ? $invoice->number_of_item_scan: 0 ?></td>

            <th class="<?php echo $class_input_01;?>"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdItemsForwarded'); ?></th>
            <td class="<?php echo $class_input_02;?>"><?php echo $invoice ? $invoice->number_of_item_forwarded: 0 ?></td>

            <th class="<?php echo $class_input_01;?>"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdItemsStorage'); ?></th>
            <td class="<?php echo $class_input_02;?>"><?php echo $invoice ? $invoice->number_of_storage_item: 0 ?></td>

            <th class="<?php echo $class_input_01;?>"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdNewReg'); ?></th>
            <td class="<?php echo $class_input_02;?>"><?php echo $invoice ? $invoice->number_of_new_registration: 0 ?></td>
        </tr>
        
        <tr>
            <th class="<?php echo $class_input_01;?>"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdNeverActDeleted'); ?></th>
            <td class="<?php echo $class_input_02;?>"><?php echo $invoice ? $invoice->number_of_never_activated_deleted: 0 ?></td>

            <th class="<?php echo $class_input_01;?>"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdManualDeleted'); ?></th>
            <td class="<?php echo $class_input_02;?>"><?php echo $invoice ? $invoice->number_of_manual_deleted: 0 ?></td>

            <th class="<?php echo $class_input_01;?>"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdAutDeleted'); ?></th>
            <td class="<?php echo $class_input_02;?>"><?php echo $invoice ? $invoice->number_of_automatic_deleted: 0 ?></td>

            <th class="<?php echo $class_input_01;?>">&nbsp;</th>
            <td class="<?php echo $class_input_02;?>">&nbsp;</td>
        </tr>
    </table>
</div>
<div id="gridwraper02" style="margin-top: 10px;" >
    <!-- summary -->
    <table class="<?php echo $class_settings ?>" style="width: <?php echo $width ?>" cellpadding="<?php echo $cellpadding ?>">
        <tr>
            <th class="input-width-200">&nbsp;</th>
            <th class="input-width-100" style="text-align: center;">#</th>
            <th class="input-width-100" style="text-align: center;"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdTotal'); ?>(<?php echo $currency_short ?>)</th>
            <th class="input-width-100" style="text-align: center;"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdDiscount'); ?>(<?php echo $currency_short ?>)</th>
            <th class="input-width-100" style="text-align: center;"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdRevShare'); ?>(<?php echo $currency_short ?>)</th>
        </tr>
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdAsyougoPostbox'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->free_postbox_quantity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->free_postboxes_amount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->free_postboxes_amount_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->free_postboxes_amount_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdPrivatePostbox'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->private_postbox_quantity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->private_postboxes_amount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->private_postboxes_amount_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->private_postboxes_amount_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdBussPostbox'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->business_postbox_quantity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->business_postboxes_amount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->business_postboxes_amount_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->business_postboxes_amount_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>

    <!-- Incomming -->
    <table class="<?php echo $class_settings ?>" style="width: <?php echo $width ?>;" cellpadding="<?php echo $cellpadding ?>">
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdIncomingAsyougoPostbox'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->free_incoming_quantity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->incomming_items_free_account, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->incomming_items_free_account_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->incomming_items_free_account_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdIncomingPrivatePostbox'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->private_incoming_quantity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->incomming_items_private_account, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->incomming_items_private_account * $invoice->customer_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->incomming_items_private_account * $invoice->rev_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdIncomingBussPostbox'); ?>s</th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->business_incoming_item_quantity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->incomming_items_business_account, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->incomming_items_business_account_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->incomming_items_business_account_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>

    <!-- envelope scan -->
    <table class="<?php echo $class_settings ?>" style="width: <?php echo $width ?>" cellpadding="<?php echo $cellpadding ?>">	
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdEnvScanAsyougoPostbox'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->free_envelope_scan_quantity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->envelope_scan_free_account, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->envelope_scan_free_account_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->envelope_scan_free_account_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdEnvScanPrivatePostbox'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->private_envelope_scan_quantity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->envelope_scan_private_account, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->envelope_scan_private_account_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->envelope_scan_private_account_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdEnvScanBussPostbox'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->business_envelope_scan_quantity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->envelope_scan_business_account, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->envelope_scan_business_account_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->envelope_scan_business_account_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>

    <!-- item scan  scan -->
    <table class="<?php echo $class_settings ?>" style="width: <?php echo $width ?>" cellpadding="<?php echo $cellpadding ?>">	
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdItemScanAsyougoPostbox'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->free_item_scan_quantity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->item_scan_free_account, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->item_scan_free_account_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->item_scan_free_account_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdItemScanPrivatePostbox'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->private_item_scan_quantity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->item_scan_private_account, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->item_scan_private_account_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->item_scan_private_account_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdItemScanBussPostbox'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->business_item_scan_quantity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->item_scan_business_account, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->item_scan_business_account_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->item_scan_business_account_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>

    <!-- additional pages scan -->
    <table class="<?php echo $class_settings ?>" style="width: <?php echo $width ?>" cellpadding="<?php echo $cellpadding ?>">	
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdAddPageAssyougoPostbox'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->free_additional_page_quantity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->additional_pages_scanning_free_amount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->additional_pages_scanning_free_amount_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->additional_pages_scanning_free_amount_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdAddPagePrivatePostbox'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->private_additional_page_quantity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->additional_pages_scanning_private_amount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->additional_pages_scanning_private_amount_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->additional_pages_scanning_private_amount_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdAddPageBussPostbox'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->business_additional_page_quantity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->additional_pages_scanning_business_amount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->additional_pages_scanning_business_amount_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->additional_pages_scanning_business_amount_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>

    <!-- Forwading -->
    <table class="<?php echo $class_settings ?>" style="width: <?php echo $width ?>" cellpadding="<?php echo $cellpadding ?>">	
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdForwardChargeTotal'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * ($invoice->fowarding_charge_postal_quantity + $invoice->fowarding_charge_fee_quantity) ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(1 * ($invoice->forwarding_charges_postal + $invoice->forwarding_charges_fee), $currency_short, $currency_rate, $decimal_separator) ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice->forwarding_charges_postal_discount + $invoice->forwarding_charges_fee_discount), $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice->forwarding_charges_postal_share + $invoice->forwarding_charges_fee_share), $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdForwardChargePostal'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * ($invoice->fowarding_charge_postal_quantity + $invoice->fowarding_charge_fee_quantity) ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(1 * $invoice->forwarding_charges_postal, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdForwardChargeFee'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * ($invoice->fowarding_charge_postal_quantity + $invoice->fowarding_charge_fee_quantity) ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(1 * $invoice->forwarding_charges_fee, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    </table>

    <!-- storing letters scan -->
    <table class="<?php echo $class_settings ?>" style="width: <?php echo $width ?>" cellpadding="<?php echo $cellpadding ?>">	
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdStoringLettersAsyougoPostbox'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->free_storage_letter_quanity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->storing_letters_free_account, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->storing_letters_free_account_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->storing_letters_free_account_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdStoringLettersPrivatePostbox'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->private_storage_letter_quanity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->storing_letters_private_account, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->storing_letters_private_account_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->storing_letters_private_account_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdStoringLettersBussPostbox'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->business_storage_letter_quanity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->storing_letters_business_account, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->storing_letters_business_account_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->storing_letters_business_account_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>

    <!-- storing packages scan -->
    <table class="<?php echo $class_settings ?>" style="width: <?php echo $width ?>" cellpadding="<?php echo $cellpadding ?>">	
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdStoringPackagesAsyougoPostbox'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->free_storage_package_quanity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->storing_packages_free_account, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->storing_packages_free_account_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->storing_packages_free_account_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdStoringPackagesPrivatePostbox'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->private_storage_package_quanity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->storing_packages_private_account, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->storing_packages_private_account_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->storing_packages_private_account_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdStoringPackagesBussPostbox'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->business_storage_package_quanity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->storing_packages_business_account, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->storing_packages_business_account_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->storing_packages_business_account_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>

    <!-- custom declaration -->
    <table class="<?php echo $class_settings ?>" style="width: <?php echo $width ?>" cellpadding="<?php echo $cellpadding ?>">	
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdCustDeclarations'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * ($invoice->custom_declaration_quantity) ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->custom_declaration_outgoing_price_01 + $invoice->custom_declaration_outgoing_price_02, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice->custom_declaration_outgoing_price_01_discount + $invoice->custom_declaration_outgoing_price_02_discount), $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency(($invoice->custom_declaration_outgoing_price_01_share + $invoice->custom_declaration_outgoing_price_02_share), $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>

    <!-- cash payment -->
    <table class="<?php echo $class_settings ?>" style="width: <?php echo $width ?>" cellpadding="<?php echo $cellpadding ?>">	
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdCashPaymentFee'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->cash_payment_fee_quantity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->cash_payment_free_for_item_delivery_amount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->cash_payment_free_for_item_delivery_amount_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->cash_payment_free_for_item_delivery_amount_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>

    <!-- customs cost import -->
    <table class="<?php echo $class_settings ?>" style="width: <?php echo $width ?>" cellpadding="<?php echo $cellpadding ?>">	
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdCustCostImport'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->custom_cost_import_quantity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->customs_cost_import_amount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->customs_cost_import_amount_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->customs_cost_import_amount_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdImportCustFee'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->import_custom_fee_quantity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->customs_handling_fee_import_amount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->customs_handling_fee_import_amount_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->customs_handling_fee_import_amount_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>

    <!-- address verification -->
    <table class="<?php echo $class_settings ?>" style="width: <?php echo $width ?>" cellpadding="<?php echo $cellpadding ?>">	
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdAddVerification'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->address_verification_quantity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->address_verification_amount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->address_verification_amount_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->address_verification_amount_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdSpecialServiceFee'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->special_service_fee_quantity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->special_service_fee_in_15min_intervalls_amount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->special_service_fee_in_15min_intervalls_amount_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->special_service_fee_in_15min_intervalls_amount_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdPersonalPickupCharge'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->peronsal_pickup_charge_quantity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->personal_pickup_charge_amount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->personal_pickup_charge_amount_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->personal_pickup_charge_amount_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdPaypalTranFee'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->paypal_transaction_fee_quantity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->paypal_fee, $currency_short, $currency_rate, $decimal_separator) ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->paypal_fee_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->paypal_fee_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>

    <!-- other invoice local-->
    <table class="<?php echo $class_settings ?>" style="width: <?php echo $width ?>" cellpadding="<?php echo $cellpadding ?>">	
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdOtherLocalInv'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo $invoice->other_local_invoice_quantity > 0 ? $other_quanity : 0; ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->other_local_invoice, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->other_local_invoice_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->other_local_invoice_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>


    <!-- Credit note -->
    <table class="<?php echo $class_settings ?>" style="width: <?php echo $width ?>" cellpadding="<?php echo $cellpadding ?>">	
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdCreditNote'); ?></th>
            <td class="input-width-100" style="text-align: right;"><?php echo 1 * $invoice->creditnote_quantity ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->credit_note_given, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->credit_note_given_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($invoice->credit_note_given_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>

    <!-- Total -->
    <table  class="<?php echo $class_settings ?>" style="width: <?php echo $width ?>" cellpadding="<?php echo $cellpadding ?>">	
        <tr>
            <th class="input-width-200"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdTotal'); ?></th>
            <td class="input-width-100" style="text-align: right;">&nbsp;</td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($total, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($total_discount, $currency_short, $currency_rate, $decimal_separator); ?></td>
            <td class="input-width-100" style="text-align: right;"><?php echo APUtils::view_convert_number_in_currency($total_share, $currency_short, $currency_rate, $decimal_separator); ?></td>
        </tr>
    </table>
</div>
<div class="clear-height"></div>
<!-- Content for dialog -->
<div class="hide">
	<div id="createReport" title="<?php admin_language_e('report_views_admin_marketing_partner_report_TitGenReport'); ?>" class="input-form dialog-form">
	</div>
</div>
<div class="hide" style="display: none;">
    <a id="view_report_file" class="iframe"><?php admin_language_e('report_views_admin_marketing_partner_report_TblHdPreviewFile'); ?></a>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('button').button();
        
         /**
         * #1072 add location report generation 
         */
        $('#generateReport').click(function() {
            // Clear control of all dialog form
            $('.dialog-form').html('');
            // Open new dialog
            $('#createReport').openDialog({
                autoOpen: false,
                height: 350,
                width: 695,
                modal: true,
                open: function() {
                    var v_location_id = $('#location_available_id').val();
                    var v_year = $('#year').val();
                    var v_month = $('#month').val();
                    
                    $(this).load("<?php echo base_url() ?>report/admin/create_location_report?location_id=" +v_location_id + "&year=" + v_year + "&month=" + v_month, function(){
                        
                    });
                },
                buttons: {
                    '<?php admin_language_e('report_views_admin_marketing_partner_report_BtnGenReportDlg'); ?>': function() {
                        createReport();
                    }
                   
                }
            });
            $('#createReport').dialog('option', 'position', 'center');
            $('#createReport').dialog('open');

        });
        /**
        * Create report
        */
        function createReport() {
            var submitUrl = $('#createReportForm').attr('action');
            var base_url = '<?php echo base_url()?>';
            $.ajaxSubmit({
                url: submitUrl,
                formId: 'createReportForm',
                success: function(data) {
                    if (data.status) {
                        $('#createReport').dialog('close');
                        $('#view_report_file').attr("href", base_url + 'report/admin/view_pdf_report?location_id=' + data.data[0] +  '&year=' + data.data[1] + '&month=' + data.data[2] +
                                '&costOfLocationAdvertising=' + data.data[3] + '&hardwareAmortization=' + data.data[4] + 
                                 '&locationExternalReceipts=' + data.data[5] + '&currentOpenBalance=' + data.data[6]) ;
                         $.displayInfor(data.message, null, function(){
                            // $.get(base_url + "cases/admin/view_report", {location:data.data[0], startDate:data.data[1], endDate:data.data[2]});
                             $('#view_report_file').click();
                         });
                    } else {
                        console.log("Response: "+JSON.stringify(data));
                        
                        $.each( data.data.message, function( key, value ){
                            $("#createReportForm").find("[name='" + key + "']").addClass("input-error").attr("title",value);
                        });
                        $("#createReportForm").find(".input-error").tipsy({gravity: 'sw'});
                        $.displayError(data.message);
                    }
                }
            });
        }

        $('#view_report_file').fancybox({
            width: 1000,
            height: 800
        });
        $(".datepicker").datepicker();
        $(".datepicker").datepicker("option", "dateFormat", 'dd.mm.yy');

        /**
         * Process when user click to search button
         */
        $('#locationReportingButton').live('click', function (e) {
            $('#locationReportingSearchForm').submit();
            e.preventDefault();
        });
        
        $("#generateInvoiceTotalButton").click(function(){
            location.href = "<?php echo base_url() ?>admin/report/generate_invoice_total_by_location?ym=<?php echo date("Ym", now()); ?>";
        });
        
        $("#generateInvoiceTotalButton2").click(function(){
            location.href = "<?php echo base_url() ?>admin/report/generate_invoice_total_by_location?ym=<?php echo date("Ym", strtotime("last month")); ?>";
        });
    });
</script>