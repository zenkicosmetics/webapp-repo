<div class="ym-grid">
    <div id="cloud-body-wrapper" style="width: 1070px">
        <h2>Phone Settings</h2>
        <div class="ym-clearfix" style="height:1px;"></div>
    </div>
</div>
<div class="clearfix"></div>
<div id="account-body-wrapper" style="margin: 0px 0 0 40px">
    <div class="ym-grid">
        <table border="0" style="border:none; width: 66%;">
            <tr>
                <td>
                    <span style="position: relative; top:15px">
                        Your current account deposit: <?php 
                            if ($current_balance < 0) {
                                echo APUtils::convert_currency($current_balance, $currency->currency_rate, 2, $decimal_separator). ' '. $currency->currency_short;
                            } else {
                                echo APUtils::convert_currency(0, $currency->currency_rate, 2, $decimal_separator). ' '. $currency->currency_short;
                            }
                        ?>
                    </span>
                </td>
                <td  align="right" style="text-align: right">
                    <span style="position: relative; top:-15px">Make deposit </span>
                    <a id="paymentPayoneButton">
                        <img alt="Check out with Payone by VISA card" src="<?php echo APContext::getImagePath()?>/visa.png" />
                        <img alt="Check out with Payone by Master card" src="<?php echo APContext::getImagePath()?>/mastercard.png" />
                    </a>
                    <a id="paymentPayPalButton">
                        <img src="<?php echo APContext::getImagePath()?>/paypal.gif" alt="Check out with PayPal" style="width: 120px" />
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <?php
                    $checked = "";
                    $none_checked = "";
                    if(!empty($phone_setting) && $phone_setting->notify_flag == '1'){
                        $checked = 'checked="checked"';
                    }else{
                        $none_checked = 'checked="checked"';
                    }
                    ?>
                    Notify me if account is slow: &nbsp;&nbsp;&nbsp;&nbsp;
                    YES <input type="radio" name="notify_flag" value="1" <?php echo $checked ?> />
                    NO <input type="radio" name="notify_flag" value="0" <?php echo $none_checked ?> />
                </td>
                <td align="right" style="text-align: right"><a class="more_information main_link_color" href="#">More information</a></td>
            </tr>
            <!--<tr>
                <td>
                    Max daily usage: 
                    <select id="max_daily_usage" name="max_daily_usage" class="input-width" style="width: 80px">
                        <option value="10" <?php if(!empty($phone_setting) && $phone_setting->max_daily_usage == '10'){echo 'selected="selected"';} ?>>10</option>
                        <option value="20" <?php if(!empty($phone_setting) && $phone_setting->max_daily_usage == '20'){echo 'selected="selected"';} ?>>20</option>
                        <option value="50" <?php if(!empty($phone_setting) && $phone_setting->max_daily_usage == '50'){echo 'selected="selected"';} ?>>50</option>
                        <option value="100" <?php if(!empty($phone_setting) && $phone_setting->max_daily_usage == '100'){echo 'selected="selected"';} ?>>100</option>
                    </select>
                    (EUR)
                </td>
                <td align="right" style="text-align: right"><a class="more_information" href="#">More information</a></td>
            </tr>
            <tr>
                <td>
                    Premium 9,95 EUR / user / month: deactivated
                </td>
                <td align="right" style="text-align: right"><a class="more_information" href="#">More information</a></td>
            </tr>-->
            <tr>
                <td>
                    <button class="input-btn btn-yellow " id="savePhoneSetting" type="button">Save</button>
                </td>
                <td align="right" style="text-align: right">&nbsp;</td>
            </tr>
        </table>
        
    </div>
</div>

<!-- Content for dialog -->
<div class="hide" style="display: none">
    <div id="paymentWithPaypalWindow" title="Payment With PayPal" class="input-form dialog-form"></div>
    <div id="createDirectChargeWithoutInvoice" title="Make a deposit from credit card" class="input-form dialog-form"></div>
    <a id="display_payment_confirm" class="iframe" href="#">Goto payment view</a>
    
    <div id="moreInformation" title="More information" class="input-form dialog-form">Coming soon..</div>
</div>

<script>
jQuery(document).ready(function ($) {
     /**
     * Payone payment
     */
    $('#paymentPayoneButton').live('click', function() {
        createDirectCharge();
    });

    /**
     * Create direct charge
     */
    function createDirectCharge() {
        // Clear control of all dialog form
        $('#createDirectChargeWithoutInvoice').html('');

        // Open new dialog
        $('#createDirectChargeWithoutInvoice').openDialog({
            autoOpen: false,
            height: 400,
            width: 720,
            modal: true,
            open: function() {
                $(this).load("<?php echo base_url() ?>customers/create_direct_charge_without_invoice", function() {});
            },
            buttons: {
                'Submit': function () {
                    saveDirectChargeWithoutInvoice();
                }
            }
        });
        $('#createDirectChargeWithoutInvoice').dialog('option', 'position', 'center');
        $('#createDirectChargeWithoutInvoice').dialog('open');
    };

    /**
     * Save direct charge without invoice
     */
    function saveDirectChargeWithoutInvoice() {
        var submitUrl = "<?php echo base_url() ?>customers/save_direct_charge_without_invoice";
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'createDirectChargeWithoutInvoiceForm',
            success: function(data) {
                if (data.status) {
                    if (data.redirect) {
                    var submitUrl = data.message;
                        $('#display_payment_confirm').attr('href', submitUrl);
                        $('#display_payment_confirm').click();
                    } else {
                        $('#createDirectChargeWithoutInvoice').dialog('close');
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
    $('#paymentPayPalButton').live('click', function() {
        // Open new dialog
        $('#paymentWithPaypalWindow').openDialog({
                autoOpen: false,
                height: 332,
                width: 710,
                modal: true,
                closeOnEscape: false,
                open: function(event, ui) {
                    $(this).load("<?php echo base_url() ?>customers/paypal_payment_invoice", function() {
                    });
                }
        });

        $('#paymentWithPaypalWindow').dialog('option', 'position', 'center');
        $('#paymentWithPaypalWindow').dialog('open');
    });
    
    // TODO:
    $(".more_information").click(function(e){
        e.preventDefault();
        
        // Open new dialog
        $('#moreInformation').openDialog({
            autoOpen: false,
            height: 250,
            width: 410,
            modal: true,
            closeOnEscape: false
        });

        $('#moreInformation').dialog('option', 'position', 'center');
        $('#moreInformation').dialog('open');
        
        return false;
    });
    
    $("#savePhoneSetting").click(function(e){
        e.preventDefault();
        
        var notify_flag = $('input[name=notify_flag]:checked').val();
        var max_usage = $("#max_daily_usage").val();
        $.ajaxExec({
            url: '<?php echo base_url()?>account/phone_setting',
            data:{notify_flag: notify_flag, max_usage: max_usage},
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message);
                } else {
                    $.displayError(data.message);
                }
            }
        });
        
        return false;
    });
});

</script>