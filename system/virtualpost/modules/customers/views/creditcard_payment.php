<?php
$open_balance = APUtils::getCurrentBalance(APContext::getCustomerCodeLoggedIn());
$open_balance_text = APUtils::number_format($open_balance);
$submit_url = base_url() . 'customers/creditcard_payment';
?>
<div style="margin: auto 0px;">
	<div style="width: 100%">Your account has been deactivated due to
		failed payment or an open balance</div>
	<div style="width: 100%; text-align: center; margin-top: 10px;">
		<button id="creditCardPaymentForm_retryCreditCard">Retry To Charge</button>
		<button id="creditCardPaymentForm_selectNewPaymentMethod">Select New
			Payment Method</button>
		<button id="creditCardPaymentForm_makePaypalPayment">Make Paypal
			Payment</button>
	</div>
</div>

<table style="border: 0px none; width: 100%">
	<tr>
		<th colspan="2" style="border: 0px none;">Or make a deposit into your
			account with a bank transfer Our bank details are as follows:</th>
	</tr>
	<tr>
		<td>IBAN</td>
		<td><input type="text" style="background: #fbfbfb" readonly="readonly"
			id="invoicePaymentForm_iban" name="iban"
			value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_IBAN_CODE)?>"
			class="input-txt-none" /></td>
	</tr>
	<tr>
		<td>BIC</td>
		<td><input type="text" style="background: #fbfbfb" readonly="readonly"
			id="invoicePaymentForm_bic" name="bic"
			value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_SWIFT_CODE)?>"
			class="input-txt-none" /></td>
	</tr>
</table>
<form id="creditCardPaymentForm" method="post" class="dialog-form"
	action="<?php echo base_url() ?>customers/save_paypal_payment"></form>
<script type="text/javascript">
jQuery(document).ready(function($){
    $('#creditCardPaymentForm_retryCreditCard').button();
    $('#creditCardPaymentForm_selectNewPaymentMethod').button();
    $('#creditCardPaymentForm_makePaypalPayment').button();

    $('#creditCardPaymentForm_retryCreditCard').live('click', function() {
    	var submitUrl = '<?php echo base_url()?>customers/direct_charge';
        $.ajaxExec({
             url: submitUrl,
             success: function(data) {
                 if (data.status) {
                     document.location = '<?php echo base_url()?>mailbox';
                 } else {
                 	$.displayError(data.message);
                 }
             }
         });
    	return false;
    });

    $('#creditCardPaymentForm_selectNewPaymentMethod').live('click', function() {
    	$('#creditCardPaymentWindow').dialog('close');
    	openPaymentWindow();
    	return false;
    });

    $('#creditCardPaymentForm_makePaypalPayment').live('click', function() {
    	// Open new dialog
		$('#paymentWithPaypalWindow').openDialog({
			autoOpen: false,
			height: 300,
			width: 500,
			modal: true,
			closeOnEscape: false,
			open: function(event, ui) {
				$(this).load("<?php echo base_url() ?>customers/paypal_payment_invoice", function() {
				});
			}
		});
		
		$('#paymentWithPaypalWindow').dialog('option', 'position', 'center');
		$('#paymentWithPaypalWindow').dialog('open');
		
		$('#creditCardPaymentWindow').dialog('close');
    	return false;
    });
});
</script>