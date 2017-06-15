<form id="paymentConfirmForm" method="post" class="dialog-form">
    <div class="ym-grid">
        <div style="font-size: 16px">
            <?php language_e('cases_view_payment_payment_YourCurrentOpenBalanceInYourAc', ['amount' => $amount, 'currency' => 'EUR']); ?>
        </div>

    </div>

    <div class="ym-clearfix"></div>
    <br /> <br />
    <div class="ym-grid">
        <div class="ym-g25 ym-gl">
          <a id="paymentPayPalButton" >
                <img src="<?php echo APContext::getImagePath()?>/paypal.gif" alt="Check out with PayPal" style="width: 120px" />
            </a>
        </div>
        <div class="ym-g50  ym-gl">
              <a id="paymentPayoneButton" style="margin-left: 15px">
                    <img alt="Check out with Payone by VISA card" src="<?php echo APContext::getImagePath()?>/visa.png" />
                    <img alt="Check out with Payone by Master card" src="<?php echo APContext::getImagePath()?>/mastercard.png" />
                </a>
            </a>
        </div>
        <div class="ym-g25  ym-gl">
            <button type="button" id="bankTranferButton" class="input-btn"
                style="width: 120px; height: 35px; margin-top: 3px;box-shadow:none"><?php language_e('cases_view_payment_payment_BankTranfer'); ?></button>
        </div>
    </div>
    <div class="hide" style="display:none">
        <div id="paymentWithPaypalWindow" title="Payment With PayPal" class="input-form dialog-form"></div>
        <div id="payonePaymentWindow" title="Payment with payone" class="input-form dialog-form"></div>
        <div id="bankTranferPaymentWindow" title="Bank Tranfer Payment" class="input-form dialog-form"></div>
    </div>
</form>
<script type="text/javascript">
$(document).ready( function() {
    $('button').button();

    /**
     * Paypal payment
     */
    $('#paymentPayPalButton').click(function() {
         // get total
        var total = $("#total").html();
        total = total.replace('EUR','');
        total = encodeURI(total);
        var url = "<?php echo base_url() ?>cases/services/paypal_payment?total=" + total;

        // Open new dialog
        $('#paymentWithPaypalWindow').openDialog({
            autoOpen: false,
            height: 300,
            width: 500,
            modal: true,
            closeOnEscape: false,
            open: function(event, ui) {
                $(this).load(url, function() {
                });
            }
        });

        $('#paymentWithPaypalWindow').dialog('option', 'position', 'center');
        $('#paymentWithPaypalWindow').dialog('open');
    });

    $("#paymentCreditButton").click(function(){
        // payone payment
    });

    /**
     * Back delete postbox account
     */
    $('#bankTranferButton').click(function(){
        // get total
        var total = $("#total").html();
        total = total.replace('EUR','');
        total = encodeURI(total);
        var url = "<?php echo base_url() ?>cases/services/bank_tranfer?total=" + total;

        //$('#divPaymentBoxWindow').dialog('destroy');

        // Open new dialog
        $('#bankTranferPaymentWindow').openDialog({
            autoOpen: false,
            height: 200,
            width: 400,
            modal: true,
            closeOnEscape: false,
            open: function() {
                $(this).load(url, function() {

                });
            },
            buttons: {

            }
        });
        $('#bankTranferPaymentWindow').dialog('option', 'position', 'center');
        $('#bankTranferPaymentWindow').dialog('open');
    });

    $("#paymentPayoneButton").click(function(){
        // get total
        var total = $("#total").html();
        total = total.replace('EUR','');
        total = encodeURI(total);
        var url = "<?php echo base_url() ?>cases/services/payone_payment?total=" + total;

        //$('#divPaymentBoxWindow').dialog('destroy');

        // Open new dialog
        $('#payonePaymentWindow').openDialog({
            autoOpen: false,
            height: 450,
            width: 550,
            modal: true,
            closeOnEscape: false,
            open: function() {
                $(this).load(url, function() {

                });
            },
            buttons: {
                'Payment': function(){
                    // do payment here.
                },
                'Cancel': function (){
                    $('#payonePaymentWindow').dialog('destroy');
                }
            }
        });
        $('#payonePaymentWindow').dialog('option', 'position', 'center');
        $('#payonePaymentWindow').dialog('open');
    });
});
</script>