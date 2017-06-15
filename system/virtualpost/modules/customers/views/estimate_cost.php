<style>
    .input-width {
        border: 1px solid #cccccc;
        border-radius: 3px;
        box-shadow: 0 1px 0 #eeeeee inset, 0 1px 0 #ffffff;
        font-size: 13px;
        margin: 0;
        padding: 5px;
        width: 250px;
    }
    #prepaymentForm table th, td {
        line-height: 16px;
        padding: 5px 0.5em;
        vertical-align: top;
    }
    
</style>
<form id="prepaymentForm" method="post" class="" action="#">
    <h2 style="font-size: 14px; padding: 0 0 10px 10px; font-weight: bold;">An activity requires you to make a pre-payment/deposit into your account.
        You can either make a PayPal or Credit Card payment directly here or make a bank transfer:
    </h2>
    <table  style="width: 100%;border: none; padding: 5px;" >
        <tr>
            <td style="text-align: left;">&nbsp;</td>
            <td style="text-align: left;">&nbsp;</td>
            <td style="text-align: right;">Other currency:</td>
        </tr>
        <tr>
            <td style="text-align: left;">Your open balance due:</td>
            <td style="text-align: right;"><?php echo APUtils::number_format($open_balance_due); ?> EUR</td>
            <td style="text-align: right;"><?php echo $currency->currency_sign; ?>&nbsp;<?php echo APUtils::convert_currency($open_balance_due, $currency->currency_rate, 2, $decimal_separator); ?></td>
        </tr>
        <tr>
            <td style="text-align: left;">Your current open balance this month:</td>
            <td style="text-align: right;"><?php echo APUtils::number_format($open_balance_current_month); ?> EUR</td>
            <td style="text-align: right;"><?php echo $currency->currency_sign; ?>&nbsp;<?php echo APUtils::convert_currency($open_balance_current_month, $currency->currency_rate, 2, $decimal_separator); ?></td>
        </tr>
        <?php if ($type == 'add_more_postbox' || $type == 'change_postbox_type') { ?>
        <tr>
            <td style="text-align: left;border-bottom:1pt solid #CCCCCC">Current activity:</td>
            <td style="text-align: right;border-bottom:1pt solid #CCCCCC"><?php echo APUtils::number_format($estimate_cost); ?> EUR</td>
            <td style="text-align: right;border-bottom:1pt solid #CCCCCC"><?php echo $currency->currency_sign; ?>&nbsp;<?php echo APUtils::convert_currency($estimate_cost, $currency->currency_rate, 2, $decimal_separator); ?></td>
        </tr>
        <tr>
            <td style="text-align: left;border-bottom:1pt solid #CCCCCC">Do you want to add more than one new postbox?</td>
            <td style="text-align: right;border-bottom:1pt solid #CCCCCC"><a href="#" id="prepayment_add_more_postbox" style="text-decoration: underline;color: blue">Yes</a></td>
            <td style="text-align: right;border-bottom:1pt solid #CCCCCC">&nbsp;</td>
        </tr>
        <?php } else { ?>
        <tr>
            <td style="text-align: left;border-bottom:1pt solid #CCCCCC">Current activity <?php if ($estimated_type != 'calculated') {?>*<?php } ?>:</td>
            <td style="text-align: right;border-bottom:1pt solid #CCCCCC"><?php echo APUtils::number_format($estimate_cost); ?> EUR</td>
            <td style="text-align: right;border-bottom:1pt solid #CCCCCC"><?php echo $currency->currency_sign; ?>&nbsp;<?php echo APUtils::convert_currency($estimate_cost, $currency->currency_rate, 2, $decimal_separator); ?></td>
        </tr>
        <?php } ?>
        <tr>
            <td style="text-align: left;border-bottom:1pt solid #CCCCCC">Other required pre-payments (estimated)<?php if ($type != 'add_more_postbox' && $type != 'change_postbox_type' && $other_prepayment_amount!= 0) { ?> * <?php } ?>:</td>
            <td style="text-align: right;border-bottom:1pt solid #CCCCCC"><?php echo APUtils::number_format($other_prepayment_amount); ?> EUR</td>
            <td style="text-align: right;border-bottom:1pt solid #CCCCCC"><?php echo $currency->currency_sign; ?>&nbsp;<?php echo APUtils::convert_currency($other_prepayment_amount, $currency->currency_rate, 2, $decimal_separator); ?></td>
        </tr>
        <?php 
            $total_cost = $open_balance_due + $open_balance_current_month + $estimate_cost + $other_prepayment_amount;
        ?>
        <tr style="margin-top: 5px;">
            <td style="text-align: left;border-bottom:1pt solid #CCCCCC; font-weight: bold">Required Payment:</td>
            <td style="text-align: right;border-bottom:1pt solid #CCCCCC; font-weight: bold" id="prepaymentForm_total_cost_1"><?php echo APUtils::number_format($total_cost); ?> EUR</td>
            <td style="text-align: right;border-bottom:1pt solid #CCCCCC; font-weight: bold"><?php echo $currency->currency_sign; ?>&nbsp;
                <span id="prepaymentForm_total_cost_other_1"><?php echo APUtils::convert_currency($total_cost, $currency->currency_rate, 2, $decimal_separator); ?></span></td>
        </tr>
       <tr style="margin-top: 5px;border-bottom:1pt solid #CCCCCC">
            <td style="text-align: left;">Make Deposit Payment:</td>
            <td style="text-align: right;" id="prepaymentForm_total_cost_2"><?php echo APUtils::number_format($total_cost); ?> EUR</td>
            <td style="text-align: right;"><?php echo $currency->currency_sign; ?>&nbsp;
                <span id="prepaymentForm_total_cost_other_2"><?php echo APUtils::convert_currency($total_cost, $currency->currency_rate, 2, $decimal_separator); ?></span></td>
        </tr>
        <tr style="margin-top: 5px;">
            <td style="text-align: left;">Select payment method:</td>
            <td style="text-align: right;" colspan="2">
                <select id="prepaymentForm_payment_method" name="prepaymentForm_payment_method" class="input-width" style="width: 100%">
                    <option value="0">All credit cards</option>
                    <option value="1">Paypal</option>
                    <option value="2">Bank transfer</option>
                </select>
            </td>
        </tr>
        <?php if ($type != 'add_more_postbox' && $type != 'change_postbox_type' && ($estimated_type != 'calculated' || $other_prepayment_amount!= 0)) { ?>
        <tr>
            <td colspan="3">* The cost of this shipment could not be calculated automatically. This value is only an estimate. Your account will be charged with the actual cost that will occur for this shipment</td>
        </tr>
        <?php } ?>
        <tr>
            <td>&nbsp;</td>
            <td colspan="2" style="text-align: right;">
                <br />
                <button id="prepaymentForm_make_payment_button" class="input-width" style="width:60%; cursor: pointer;" type="button">Make payment</button>
            </td>
        </tr>
    </table>
    <input type="hidden" id="h_prepaymentForm_type" name="type" value="<?php echo $type?>" /> 
    <input type="hidden" id="h_prepaymentForm_currency_rate" name="type" value="<?php echo $currency->currency_rate?>" /> 
    <input type="hidden" id="h_prepaymentForm_total_cost" name="type" value="<?php echo number_format($total_cost, 2);?>" /> 
    <input type="hidden" id="h_prepaymentForm_list_envelope_id" name="list_envelope_id" value="<?php echo $list_envelope_id?>" /> 
    <input type="hidden" id="h_prepaymentForm_action_type" name="action_type" value="<?php echo $action_type?>" /> 
    <input type="hidden" id="h_prepaymentForm_location_id" name="location_id" value="<?php echo $location_id?>" /> 
    <input type="hidden" id="h_prepaymentForm_list_add_more_location_id" name="list_add_more_location_id" value="" />
    <input type="hidden" id="h_prepaymentForm_total_amount" name="prepaymentForm_total_amount" value="<?php echo $total_cost?>" />
</form>
<div class="hide" style="display: none;">
    <div id="paymentWithPaypalWindow_Prepayment" title="Payment With PayPal" class="input-form dialog-form"></div>
    <div id="createDirectChargeWithoutInvoice_Prepayment" title="Make a deposit from credit card" class="input-form dialog-form"></div>
    <a id="display_payment_confirm_Prepayment" class="iframe" href="#">Goto payment view</a>
    <div id="bankTranferDivContainer_Prepayment"  class="input-form dialog-form" title="Bank tranfer">
        <div style="text-align: center">
            <div style="margin-top: 10px">For a direct bank transfer please use the following Account Information:</div>
            <div  style="margin-top: 20px"><strong>Account holder: <?php echo Settings::get(APConstants::INSTANCE_OWNER_COMPANY_CODE)?></strong></div>
            <div><strong>IBAN: <?php echo Settings::get(APConstants::INSTANCE_OWNER_IBAN_CODE)?></strong></div>
            <div><strong>BIC: <?php echo Settings::get(APConstants::INSTANCE_OWNER_SWIFT_CODE)?></strong></div>
            <div><strong>Bank name: <?php echo Settings::get(APConstants::INSTANCE_OWNER_BANK_NAME_CODE)?></strong></div>
            <div><strong>Use your account e-mail as reference</strong></div>
            <div style="font-style: italic;font-size:12px;margin-top: 20px;">The money will be credited to your account as soon as it arrives on our bank account.</div>
        </div>
    </div>
    <div id="prepaymentForm_AddMoreBusinessPostbox" title="Add More Business Postbox" class="input-form dialog-form"></div>
</div>
<script type="text/javascript">
$(document).ready(function () {
    $('#display_payment_confirm_Prepayment').fancybox({
            width: 500,
            height: 300
    });
    $("#prepaymentForm_make_payment_button").click(function(){
        var payment_method = $('#prepaymentForm_payment_method').val();
        // Credit card
        if (payment_method === '0') {
            createDirectCharge();
        }
     	// Paypal
     	else if (payment_method === '1') {
            createPaypalCharge();
        }
     	// Bank
     	else if (payment_method === '2') {
            openBankTransfer();
        }
    });

    /**
    * Create direct charge
    */
   function createDirectCharge() {
        // Clear control of all dialog form
        $('#createDirectChargeWithoutInvoice_Prepayment').html('');

        // Open new dialog
        $('#createDirectChargeWithoutInvoice_Prepayment').openDialog({
                autoOpen: false,
                height: 385,
                width: 720,
                modal: true,
                open: function() {
                        var url = "<?php echo base_url() ?>customers/create_direct_charge_without_invoice?prepayment=1";

                        var type = $('#h_prepaymentForm_type').val();
                        var list_envelope_id = $('#h_prepaymentForm_list_envelope_id').val();
                        var action_type = $('#h_prepaymentForm_action_type').val();
                        var location_id = $('#h_prepaymentForm_location_id').val();

                        url += '&type=' + type;
                        url += '&list_envelope_id=' + list_envelope_id;
                        url += '&action_type=' + action_type;
                        url += '&location_id=' + location_id;
                        var amount = $('#h_prepaymentForm_total_amount').val();
                        url += "&amount=" + amount;
                        
                        $(this).load(url, function() {});
                },
                buttons: {
                        'Submit': function () {
                                saveDirectChargeWithoutInvoice();
                        }
                }
        });
        $('#createDirectChargeWithoutInvoice_Prepayment').dialog('option', 'position', 'center');
        $('#createDirectChargeWithoutInvoice_Prepayment').dialog('open');
   };

    /**
     * Create direct charge
     */
    function createPaypalCharge() {
        // Open new dialog
        $('#paymentWithPaypalWindow_Prepayment').openDialog({
            autoOpen: false,
            height: 332,
            width: 710,
            modal: true,
            closeOnEscape: false,
            open: function(event, ui) {
                    var url = "<?php echo base_url() ?>customers/paypal_payment_invoice?prepayment=1";

                    var type = $('#h_prepaymentForm_type').val();
                    var list_envelope_id = $('#h_prepaymentForm_list_envelope_id').val();
                    var action_type = $('#h_prepaymentForm_action_type').val();
                    url += '&type=' + type;
                    url += '&list_envelope_id=' + list_envelope_id;
                    url += '&action_type=' + action_type;
                    var amount = $('#h_prepaymentForm_total_amount').val();
                    url += "&amount=" + amount;
                    
                    var location_id = $('#h_prepaymentForm_location_id').val();
                    url += '&location_id=' + location_id;
                    
                    $(this).load(url, function() {
                    });
            }
        });

        $('#paymentWithPaypalWindow_Prepayment').dialog('option', 'position', 'center');
        $('#paymentWithPaypalWindow_Prepayment').dialog('open');
    }

    /**
     * Save direct charge without invoice
     */
    function saveDirectChargeWithoutInvoice() {
        var submitUrl = "<?php echo base_url() ?>customers/save_direct_charge_without_invoice?prepayment=1";
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'createDirectChargeWithoutInvoiceForm',
            success: function(data) {
                if (data.status) {
                    if (data.redirect) {
                        var submitUrl = data.message;
                        $('#display_payment_confirm_Prepayment').attr('href', submitUrl);
                        $('#display_payment_confirm_Prepayment').click();
                    } else {
                        $('#createDirectChargeWithoutInvoice_Prepayment').dialog('close');
                        $.displayInfor(data.message, null,  function() {
                        });
                    }
                } else {
                    $.displayError(data.message);
                }
            }
        });
    }

    /**
     * Paypal payment
     */
    function openBankTransfer() {
        // Open new dialog
        $('#bankTranferDivContainer_Prepayment').openDialog({
                autoOpen: true,
                height: 250,
                width: 400,
                modal: true,
                closeOnEscape: true
        });

        $('#bankTranferDivContainer_Prepayment').dialog('option', 'position', 'center');
        $('#bankTranferDivContainer_Prepayment').dialog('open');
        return false;
    };
    
    /**
     * Send request to check payone transaction status
     * @returns {undefined}
     */
    function checkPayoneTransactionStatus() {
        var location_id = $('#h_prepaymentForm_location_id').val();
        var submitUrl = "<?php echo base_url() ?>account/check_prepayment_status?location_id=" + location_id;
        $.ajaxExec({
            url: submitUrl,
            success: function(data) {
                if (data.prepayment == true) {
                    checkPayoneTransactionStatus();
                } else {
                    // Redirect to account module
                    location.href = "<?php echo base_url() ?>account/index?add_postbox=1&location_id=" + location_id;
                }
            }
        });
    }
    
    /**
     * When user click to YES or Amount link
     */
    $('#prepayment_add_more_postbox').live('click', function() {
        $('#prepaymentForm_AddMoreBusinessPostbox').openDialog({
            autoOpen: false,
            height: 560,
            width: 700,
            modal: true,
            closeOnEscape: false,
            open: function(event, ui) {
                var list_location_id = $('#h_prepaymentForm_list_add_more_location_id').val();
                var url = "<?php echo base_url() ?>account/add_multi_postbox?list_location_id=" + list_location_id;
                $(this).load(url, function() {
                });
            }
        });

        $('#prepaymentForm_AddMoreBusinessPostbox').dialog('option', 'position', 'center');
        $('#prepaymentForm_AddMoreBusinessPostbox').dialog('open');
        
    });
});
</script>