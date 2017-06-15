<?php
$open_balance = APUtils::getCurrentBalance(APContext::getCustomerCodeLoggedIn());
$open_balance_text = APUtils::number_format($open_balance);
$submit_url = base_url() . 'customers/paypal_payment';
?>
<div style="margin: auto 0px;">
	<div style="width: 100%">
        Your account has an open balance of <?php echo $open_balance_text?> EUR. <br />
		To access your account, please make a payment
	</div>
	<div style="width: 100%; text-align: center; margin-top: 10px;">
		<button id="paypalPaymentForm_paynowWithPaypal">pay now with paypal</button>
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
    $('#paypalPaymentForm_paynowWithPaypal').button();
    $('#paypalPaymentForm_paynow').button();


    $('#paypalPaymentForm_paynowWithPaypal').live('click', function() {
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
		
		$('#paypalPaymentWindow').dialog('close');
    	return false;
    	
    });
});
</script>