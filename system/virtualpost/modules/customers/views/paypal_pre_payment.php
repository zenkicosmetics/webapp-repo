<?php Asset::css('jquery-ui-1.8.20.custom.css'); ?>
<?php Asset::css('Aristo.css'); ?>
<?php Asset::css('styles.css'); ?>

<?php Asset::js('jquery-1.7.2.min.js'); ?>
<?php Asset::js('jquery.blockUI.js'); ?>
<?php Asset::js('jquery-ui-1.8.20.custom.min.js'); ?>
<?php Asset::js('jquery.common.js'); ?>

<?php
ci()->load->library('price/price_api');
$pricing_map = price_api::getDefaultPricingModel();
$open_balance = APUtils::getCurrentBalance(APContext::getCustomerCodeLoggedIn());
$submit_url = base_url() . 'customers/paypal_payment';
$paypal_transaction_fee = $pricing_map[1]['paypal_transaction_fee'];
?>
<style>
    .paypal-button-tag-content {
        display: none;
    }
</style>
<form id="creditCardPaymentForm" method="post" class="dialog-form" action="<?php echo base_url() ?>customers/save_paypal_payment">
    <div style="margin: auto 0;">
        <table style=" width: 680px; margin:10px;">
            <tr><td colspan="3">Do you want to make a PayPal payment:</td></tr>
            <tr style="height: 25px;">
                <td style="vertical-align: middle;">
                    <input type="text" id="creditCardPaymentForm_paypal_amount" name="paypal_amount" value="<?php echo $pre_total_amount ?>" class="input-txt-none" style="width: 100px;" />&nbsp;&nbsp;EUR
                </td>
                <td style="vertical-align: middle;">
                    <a href="#" id="paypalPaymentForm_paynowWithPaypal02"   >
                        <img src="<?php echo APContext::getImagePath() ?>/paypal.gif" alt="Check out with PayPal" style="width: 120px" />
                    </a>
                </td>
                <td id="currency-conversion-box" class="input-width" style="vertical-align: middle; min-width: 320px; padding-left: 12px; display: block; height: 30px; margin-top:5px;">
                    <span style="float:left; margin-top:5px;">Show in other currency:&nbsp;&nbsp;</span>
                    <select name="currency_id_02" id="currency_id_02" style="float: right;width: 100px; margin-right: 10px; margin-top:1px;" class="input-width">
                        <?php foreach ($currencies as $currency): ?>
                            <?php if ($currency->currency_id == $selected_currency->currency_id): ?>
                                <option value="<?php echo $currency->currency_id; ?>" selected="selected"><?php echo $currency->currency_short; ?></option>
                            <?php else: ?>
                                <option value="<?php echo $currency->currency_id; ?>"><?php echo $currency->currency_short; ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <span id="converted_amount_02" style="float: right;margin-top: 5px; margin-right: 8px;">0,00</span>
                </td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: center; vertical-align: middle; font-size: 12px;">
                    We charge a  <?php echo $paypal_transaction_fee ?>% transaction fee for PayPal payment.
                    <br>The payment will be made in EUR.
                </td>
            </tr>
        </table>
        <input type="hidden" id="creditCardPaymentForm_type" name="type" value="<?php echo $type ?>">
        <input type="hidden" id="creditCardPaymentForm_location_id" name="type" value="<?php echo $location_id ?>">
        <input type="hidden" id="creditCardPaymentForm_list_envelope_id" name="list_envelope_id" value="<?php echo $list_envelope_id ?>">
        <input type="hidden" id="creditCardPaymentForm_prepayment" name="prepayment" value="<?php echo $prepayment ?>">
        <input type="hidden" id="creditCardPaymentForm_action_type" name="action_type" value="<?php echo $action_type ?>">
    </div>
</form>
<script async src="//www.paypalobjects.com/api/checkout.js"></script>
<script>
jQuery(document).ready(function ($) {
    var merchantId = '<?php echo Settings::get(APConstants::PAYMENT_PAYPAL_MERCHANT_ID); ?>';
    <?php
    $environment = 'sanbox';
    if (Settings::get(APConstants::PAYMENT_PAYPAL_TEST_MODE) != 'true') {
    $environment = 'production';
    }
    ?>
    var paypalButtons = ['paypalPaymentForm_paynowWithPaypal02'];

    $('#paypalPaymentForm_paynowWithPaypal02').live('click', function () {
        return false;
    });
    window.paypalCheckoutReady = function () {
        var environment = '<?php echo $environment; ?>';

        // Setup for second button
        paypal.checkout.setup(merchantId, {
            environment: environment,
            buttons: [{
                    button: 'paypalPaymentForm_paynowWithPaypal02',
                    click: function (event) {
                        event.preventDefault();
                        payCustomAmount();
                    }
                }]
        });

        // Pay with customize amount
        function payCustomAmount() {
            var open_balance = '<?php echo $open_balance; ?>'.replace(',', '.');
            var paypal_amount = $('#creditCardPaymentForm_paypal_amount').val().replace(',', '.');
            var warning = '<?php echo lang('customer.charge_paypal_payment_warning'); ?>';

            if (!$.isValidNumber(paypal_amount) || paypal_amount < 0) {
                // Display error message
                $.displayError('Please input valid amount. The amount should be numeric value.');
                return false;
            }
            var submit_url = "<?php echo base_url() ?>customers/save_paypal_payment?paypal_amount=" + $('#creditCardPaymentForm_paypal_amount').val();
            var prepayment = $('#creditCardPaymentForm_prepayment').val();
            if (prepayment === '1') {
                var type = $('#creditCardPaymentForm_type').val();
                var list_envelope_id = $('#creditCardPaymentForm_list_envelope_id').val();
                var action_type = $('#creditCardPaymentForm_action_type').val();
                submit_url += '&type=' + type;
                submit_url += '&list_envelope_id=' + list_envelope_id;
                submit_url += '&action_type=' + action_type;
                submit_url += '&prepayment=' + prepayment;
                submit_url += '&location_id=' + $('#creditCardPaymentForm_location_id').val();
            }

            if (parseFloat(paypal_amount) < parseFloat(open_balance)) {
                $.confirm({message: warning, yes: function () {
                        $('#paymentWithPaypalWindow').dialog('close');
                        //Mini browser initing
                        paypal.checkout.initXO();

                        /* do the AJAX call requesting EC token */
                        $.ajax({
                            url: submit_url,
                            type: "GET",
                            //Load the minibrowser with the redirection url in the success handler
                            success: function (responseData, textStatus, jqXHR) {
                                console.log(responseData);
                                var url = 'https://www.sandbox.paypal.com/checkoutnow?token=' + responseData;
                                if (environment == 'production') {
                                    url = 'https://www.paypal.com/checkoutnow?token=' + responseData;
                                }

                                //Loading Mini browser with redirect url, true for async AJAX calls
                                paypal.checkout.startFlow(url, true);
                            },
                            error: function (responseData, textStatus, errorThrown) {
                                //Gracefully Close the minibrowser in case of AJAX errors
                                paypal.checkout.closeFlow();
                            }
                        });
                    }});
                return false;
            } else {
                $('#paymentWithPaypalWindow').dialog('close');
                //Mini browser initing
                paypal.checkout.initXO();

                /* do the AJAX call requesting EC token */
                $.ajax({
                    url: submit_url,
                    type: "GET",
                    //Load the minibrowser with the redirection url in the success handler
                    success: function (responseData, textStatus, jqXHR) {
                        console.log(responseData);
                        var url = 'https://www.sandbox.paypal.com/checkoutnow?token=' + responseData;
                        if (environment == 'production') {
                            url = 'https://www.paypal.com/checkoutnow?token=' + responseData;
                        }

                        //Loading Mini browser with redirect url, true for async AJAX calls
                        paypal.checkout.startFlow(url, true);
                    },
                    error: function (responseData, textStatus, errorThrown) {
                        //Gracefully Close the minibrowser in case of AJAX errors
                        paypal.checkout.closeFlow();
                    }
                });
            }
        }
    };
});
</script>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var open_balance_due = '<?php echo $open_balance; ?>'.replace(',', '.');
        var $paypalPaymentInput = $("#creditCardPaymentForm_paypal_amount");
        var $selectCurrencyID01 = $("#currency_id_01");
        var $selectCurrencyID02 = $("#currency_id_02");
        var $conversionBox = $("#currency-conversion-box");

        // Hide currency conversion for deposit payment
        // $conversionBox.hide();
        // $paypalPaymentInput.val('');

        // When the customer changes currency unit of Open balance due
        $selectCurrencyID01.change(function () {
            var converted_currency_id = $(this).val();
            if (isValidBaseAmountInput(open_balance_due)) {
                convertCurrency(converted_currency_id, open_balance_due, '01');
            }
        });

        // When the customer enters some value into the deposit payment text-box
        $paypalPaymentInput.data("value", $paypalPaymentInput.val());
        setInterval(function () {
            var data = $paypalPaymentInput.data("value"),
                    val = getPayPalPaymentValue($paypalPaymentInput),
                    converted_currency_id = $selectCurrencyID02.val();

            if ((data !== val) && isValidBaseAmountInput(val)) {
                $paypalPaymentInput.data("value", val);
                convertCurrency(converted_currency_id, val, '02');
            }
        }, 1000);

        $paypalPaymentInput.bind('input propertychange', function () {
            var amount_in_euro = getPayPalPaymentValue($(this));
            if (isValidBaseAmountInput(amount_in_euro)) {
                $conversionBox.show();
            } else {
                $conversionBox.hide();
            }
        });

        // When the customer changes currency unit of deposit payment
        $selectCurrencyID02.change(function () {
            var converted_currency_id = $(this).val();
            var amount_in_euro = getPayPalPaymentValue($paypalPaymentInput);
            if (isValidBaseAmountInput(amount_in_euro)) {
                convertCurrency(converted_currency_id, amount_in_euro, '02');
            }
        });

    });

    function isValidBaseAmountInput(baseAmount) {
        var val = parseFloat(baseAmount);
        if (isNaN(val) || (val == 0)) {
            return false;
        } else {
            return /^([0-9]+)([,\.])?([0-9]*)$/.test(baseAmount);
        }
    }

    function convertCurrency(_converted_currency_id, _base_amount, type) {
        var submitUrl = '<?php echo base_url(); ?>invoices/convert_currency';
        $.ajaxExec({
            url: submitUrl,
            data: {
                converted_currency_id: _converted_currency_id,
                base_amount: _base_amount
            },
            success: function (response) {
                if (response.status) {
                    $("#converted_amount_" + type).text(response.data.converted_amount);
                }
            }
        });
    }

    function getPayPalPaymentValue($paypalPaymentInput) {
        return $.trim($paypalPaymentInput.val().replace(',', '.'));
    }
</script>