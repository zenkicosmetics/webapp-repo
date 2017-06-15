<?php Asset::css('jquery-ui-1.8.20.custom.css'); ?>
<?php Asset::css('Aristo.css'); ?>
<?php Asset::css('styles.css'); ?>

<?php Asset::js('jquery-1.7.2.min.js'); ?>
<?php Asset::js('jquery.blockUI.js'); ?>
<?php Asset::js('jquery-ui-1.8.20.custom.min.js'); ?>
<?php Asset::js('jquery.common.js'); ?>

<?php
$submit_url = base_url() . 'customers/paypal_payment';
?>
<style>
.paypal-button-tag-content {
  display: none;
}
</style>
<form id="creditCardPaymentForm" method="post" class="dialog-form"
  action="<?php echo base_url() ?>customers/save_paypal_payment">
    <div style="margin: auto 0px;">
      <div style="width: 100%; text-align: center;font-weight: bold;">
          <?php language_e('cases_view_payment_paypal_DoYouWantToMakeAPaypalPaymentFor'); ?>:
      </div>
      <div style="width: 100%; text-align: center; margin-top: 10px">
          <table style="width: 250px; margin: auto;">
              <tr>
                  <td style="vertical-align: middle;">
                      <?php echo $amount?> EUR.
                  </td>
                  <td>
                      <?php if ($amount > 0) {?>
                        <a href="#" id="paypalPaymentForm_paynowWithPaypal01" data-paypal-button="true"  >
                              <img src="<?php echo APContext::getImagePath()?>/paypal.gif" alt="Check out with PayPal" style="width: 120px" />
                        </a>
                    <?php } else {?>
                    <?php }?>
                  </td>
              </tr>
          </table>
      </div>
      <div style="width: 100%; text-align: center;font-weight: bold;margin-top: 20px">
          <?php language_e('cases_view_payment_paypal_OrDoYouWantToMakeADepositPayme'); ?>:
      </div>
      <div style="width: 100%; text-align: center; margin-top: 10px">
          <table style="width: 250px; margin: auto;">
          <tr>
                <td style="vertical-align: middle;"><input type="text" id="creditCardPaymentForm_paypal_amount" name="paypal_amount" value="<?php echo $amount?>" class="input-txt-none" style="width: 50px;" /> EUR.</td>
                <td>
                <a href="#" id="paypalPaymentForm_paynowWithPaypal02" >
                          <img src="<?php echo APContext::getImagePath()?>/paypal.gif" alt="Check out with PayPal" style="width: 120px" />
                    </a>
                </td>
            </tr>
            </table>
      </div>
    </div>
    <div style="width: 100%; text-align: center;font-weight: bold;margin-top: 20px">
        <?php
        ci()->load->library('price/price_api');
        $pricing_map = price_api::getDefaultPricingModel();
        $paypal_transaction_fee = $pricing_map[1]['paypal_transaction_fee']->item_value;
        ?>
      <?php language_e('cases_view_payment_paypal_WeChargeFeeTransaction',['fee' => $paypal_transaction_fee]); ?>
    </div>
</form>
<script>
  (function(d, s, id){
    var js, ref = d.getElementsByTagName(s)[0];
    if (!d.getElementById(id)){
      js = d.createElement(s); js.id = id; js.async = true;
      js.src = "//www.paypalobjects.com/js/external/paypal.v1.js";
      ref.parentNode.insertBefore(js, ref);
    }
  }(document, "script", "paypal-js"));
</script>
<script>
<?php $environment = 'sanbox';
    if (Settings::get(APConstants::PAYMENT_PAYPAL_TEST_MODE) != 'true') {
        $environment = 'production';
    }
?>
$(function(){
  var environment = '<?php echo $environment;?>';
  // Process when click button 1
  $('#paypalPaymentForm_paynowWithPaypal01').live('click', function(e){
    e.preventDefault();
    $('#paymentWithPaypalWindow').dialog('close');

    //Mini browser initing
    PAYPAL.apps.Checkout.initXO();

    /* do the AJAX call requesting EC token */
    var paypal_amount = '0';
    $.ajax({
       url: "<?php echo base_url() ?>customers/save_paypal_payment?paypal_amount=" + paypal_amount,
       type: "GET",

      //Load the minibrowser with the redirection url in the success handler
      success: function (responseData, textStatus, jqXHR) {
        var url = 'https://www.sandbox.paypal.com/checkoutnow?token='+responseData;
        if (environment == 'production') {
          url = 'https://www.paypal.com/checkoutnow?token='+responseData;
        }

        //Loading Mini browser with redirect url, true for async AJAX calls
        PAYPAL.apps.Checkout.startFlow(url, true);
      },


      error: function (responseData, textStatus, errorThrown) {
          //Gracefully Close the minibrowser in case of AJAX errors
          PAYPAL.apps.Checkout.closeFlow();
      }
    });
  });


// Process when click button 2
$('#paypalPaymentForm_paynowWithPaypal02').live('click', function(e){
    e.preventDefault();
    var paypal_amount = $('#creditCardPaymentForm_paypal_amount').val();
    if (!$.isValidNumber(paypal_amount) || paypal_amount < 0) {
          // Display error message
      $.displayError('Please input valid amount. The amount should be numberic value.');
      return false;
    } else {
        $('#paymentWithPaypalWindow').dialog('close');
        //Mini browser initing
        PAYPAL.apps.Checkout.initXO();

        /* do the AJAX call requesting EC token */
        $.ajax({
           url: "<?php echo base_url() ?>customers/save_paypal_payment?paypal_amount=" + paypal_amount,
           type: "GET",

          //Load the minibrowser with the redirection url in the success handler
          success: function (responseData, textStatus, jqXHR) {
            console.log(responseData);
            var url = 'https://www.sandbox.paypal.com/checkoutnow?token='+responseData;
            if (environment == 'production') {
              url = 'https://www.paypal.com/checkoutnow?token='+responseData;
            }

            //Loading Mini browser with redirect url, true for async AJAX calls
            PAYPAL.apps.Checkout.startFlow(url, true);
          },


          error: function (responseData, textStatus, errorThrown) {
              //Gracefully Close the minibrowser in case of AJAX errors
              PAYPAL.apps.Checkout.closeFlow();
          }
        });
      }
   });
});
</script>

<script type="text/javascript">
jQuery(document).ready(function($){

});
</script>