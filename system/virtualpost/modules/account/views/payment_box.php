<div class="ym-clearfix"></div>
<br />
<form id="paymentConfirmForm" method="post" class="dialog-form">
	<div class="ym-grid">
		<div style="font-size: 16px">
			Your current open balance in your account is <?php echo $curr_balance?> EUR.
			<br />Do you want to make the payment now?
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
		      <a id="paymentCreditButton" style="margin-left: 15px">
                    <img alt="Check out with Payone by VISA card" src="<?php echo APContext::getImagePath()?>/visa.png" />
                    <img alt="Check out with Payone by Master card" src="<?php echo APContext::getImagePath()?>/mastercard.png" />
                </a>
			</a>
		</div>
		<div class="ym-g25  ym-gl">
			<button type="button" id="delPostboxConfirmWindow_cancelButton"
				style="width: 120px; height: 35px; margin-top: 3px; background: rgb(128, 128, 128); border: 1px solid rgb(128, 128, 128); color: #FFFFFF;box-shadow:none">Cancel</button>
		</div>
	</div>
	<div class="hide">
		<div id="paymentWithPaypalWindow" title="Payment With PayPal" class="input-form dialog-form"></div>
		<div id="registedPaymentWindow" title="Register Payment" class="input-form dialog-form"></div>
                <div id="createDirectChargeWithoutInvoice" title="Make a deposit from credit card" class="input-form dialog-form"></div>
	</div>
</form>
<script type="text/javascript">
$(document).ready( function() {
    $('button').button();
    var click_flag = 0;

    /**
     * Paypal payment
     */
    $('#paymentPayPalButton').live('click', function() {
        // Open new dialog
        $('#paymentWithPaypalWindow').openDialog({
            autoOpen: false,
            height: 350,
            width: 770,
            modal: true,
            closeOnEscape: false,
            open: function(event, ui) {
                $(this).load("<?php echo base_url() ?>customers/paypal_payment_invoice?action_type=delete_postbox", function() {
                });
            }
        });

        $('#paymentWithPaypalWindow').dialog('option', 'position', 'center');
        $('#paymentWithPaypalWindow').dialog('open');
    });

    $("#paymentCreditButton").live('click',function(){
        if(click_flag == 1){
            location.reload();
            return ;
        }
        click_flag = 1;

        // direct charge.
        createDirectCharge();
    });

    /**
     * Back delete postbox account
     */
    $('#delPostboxConfirmWindow_cancelButton').unbind("click").bind('click', function(){
    	$('#openPaymentBoxWindow').dialog('close');
    	return false;
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
                     $(this).load("<?php echo base_url() ?>customers/create_direct_charge_without_invoice?action_type=delete_postbox", function() {});
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
        // Validate amount
        var action_type = $('#createDirectChargeWithoutInvoiceForm_action_type').val();
        var input_payment_amount = parseFloat($('#recordExternalPaymentForm_amount').val().replace(',', '.'));
        if (action_type === 'delete_postbox') {
            var min_payment_amount = parseFloat($('#createDirectChargeWithoutInvoiceForm_pre_total_amount').val().replace(',', '.'));
            if (input_payment_amount < min_payment_amount) {
                // Display error message
                $.displayError('Please input valid amount. The amount should be numeric value and greater than or equal ' + min_payment_amount + ' EUR');
                return false;
            }
        }
        
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
});
</script>