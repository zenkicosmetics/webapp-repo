<div class="button_container">
    <div style="font-size: 15px; margin-top: 5px;">You can now make a deposit into your ClevverMail account with your credit card:</div><br/>
    <div class="clear"></div>
    <form action="#" id="createDirectChargeWithoutInvoiceForm" class="dialog-form">
        <table style="border: 0px solid #dadada;margin: 5px 0px;width: 635px;">
            <tr>
                <td width="115px">
                    Select credit card: <br/>
                    <a href="<?php echo base_url()?>payment?add_payment=1" style="text-decoration: underline;" target="_blank">Enter new credit card</a>
                </td>
                <td colspan="2">
                    <select style="width: 250px;" name="cardID" id="recordExternalPaymentForm_cardID" class="input-width">
                        <option value=""></option>
                        <?php foreach($list_payments as $payment){?>
                            <option value="<?php echo $payment->payment_id?>" <?php if ($payment->primary_card == '1') {?> selected="selected" <?php }?>><?php echo $payment->card_name .'/'.$payment->card_number;?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: middle;">Amount in EUR:</td>
                <td style="vertical-align: middle;">
                    <input type="text" style="width: 120px;" name="tranAmount" 
                           id="recordExternalPaymentForm_amount" <?php if ($action_type === 'delete_postbox' && $total_postbox == 1) {?>readonly="readonly" <?php } ?>
                           value="<?php echo $pre_total_amount?>" class="input-width">
                </td>
                <td style="vertical-align: middle; padding:15px 15px 15px 25px; background-color: rgb(242,242,242); width:250px;" class="input-width">
                    <h5 style="margin-bottom: 10px; font-size: 14px;">Converted in other currency:</h5>
                    <select id="currency_id1" name="currency_id" style="width: 80px; margin-right: 10px;" class="input-width">
                        <?php foreach ($currencies as $currency): ?>
                            <?php if ($currency->currency_id == $selected_currency->currency_id): ?>
                                <option value="<?php echo $currency->currency_id; ?>" selected="selected"><?php echo $currency->currency_short; ?></option>
                            <?php else: ?>
                                <option value="<?php echo $currency->currency_id; ?>"><?php echo $currency->currency_short; ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" id="convertedAmount" name="convertedAmount" class="input-width" style="width: 120px;"/>
                    <p style="font-size: 12px; margin-top: 10px;">Currency exchange rate: <span id="exchange_rate"><?php echo APUtils::convert_currency($selected_currency->currency_rate, 1, 4, $decimal_separator); ?> <?php echo $selected_currency->currency_short; ?>/EUR</span></p>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="font-size: 12px;">
                    Note: The transfer will always be made in EUR.<br>
                    The currency conversion is for your information only and calculated based on daily updated exchange rates.
                </td>
            </tr>
        </table>
        
        <input type="hidden" id="createDirectChargeWithoutInvoiceForm_type" name="type" value="<?php echo $type?>">
        <input type="hidden" id="createDirectChargeWithoutInvoiceForm_list_envelope_id" name="list_envelope_id" value="<?php echo $list_envelope_id?>">
        <input type="hidden" id="createDirectChargeWithoutInvoiceForm_prepayment" name="prepayment" value="<?php echo $prepayment?>">
        <input type="hidden" id="createDirectChargeWithoutInvoiceForm_action_type" name="action_type" value="<?php echo $action_type?>">
        <input type="hidden" id="createDirectChargeWithoutInvoiceForm_pre_total_amount" name="createDirectChargeWithoutInvoiceForm_pre_total_amount" value="<?php echo $pre_total_amount?>">
    </form>
</div>
<div class="clear-height"></div>
<script type="text/javascript">
$(document).ready( function() {
    // When the customer changes currency selector
    $("#currency_id1").live('change',function(){
        var convertedCurrencyID = $(this).val();
        var externalPaymentamount = $.trim($("#recordExternalPaymentForm_amount").val());
        if (isValidBaseAmountInput(externalPaymentamount)) {
            convertCurrency(convertedCurrencyID, externalPaymentamount);
        }
    });

    // When the customer changes currency input value
    $("#recordExternalPaymentForm_amount").bind('input propertychange', function() {
        if ($.trim($(this).val()) == '') {
            $("#convertedAmount").val('');
        }
    });
    
    $("#recordExternalPaymentForm_amount").focusout(function(){
        convertCurrency($("#currency_id1").val(), $("#recordExternalPaymentForm_amount").val());
    });
    
    convertCurrency($("#currency_id1").val(), $("#recordExternalPaymentForm_amount").val());

//    var $externalPaymentInput = $("#recordExternalPaymentForm_amount");
//    var $selectCurrencyID = $("#currency_id");
//    $externalPaymentInput.data("value", $externalPaymentInput.val());
//    setInterval(function() {
//        var data = $("#recordExternalPaymentForm_amount").data("value"),
//            val = getExternalPaymentValue($("#recordExternalPaymentForm_amount")),
//            convertedCurrencyID = $("#currency_id").val();
//
//        if ((data !== val) && isValidBaseAmountInput(val)) {
//            $externalPaymentInput.data("value", val);
//            convertCurrency(convertedCurrencyID, val);
//        }
//    }, 1000);
});

function isValidBaseAmountInput(baseAmount) {
    var val = parseFloat(baseAmount);
    if (isNaN(val)) {
        return false;
    } else {
        return /^([0-9]+)([,\.])?([0-9]*)$/.test(baseAmount);
    }
}

function convertCurrency(_converted_currency_id, _base_amount) {
    var submitUrl = '<?php echo base_url(); ?>invoices/convert_currency';
    $.ajaxExec({
        url: submitUrl,
        data: {
            converted_currency_id: _converted_currency_id,
            base_amount: _base_amount
        },
        success: function(response){
            if (response.status) {
                $("#convertedAmount").val(response.data.converted_amount);
                $("#exchange_rate").html(response.data.exchange_rate);
            }
        }
    });
}

function getExternalPaymentValue($externalPaymentInput) {
    return $.trim($externalPaymentInput.val().replace(',', '.'));
}
</script>