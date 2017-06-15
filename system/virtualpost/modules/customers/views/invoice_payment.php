<?php 
    $open_balance = APUtils::getCurrentBalance(APContext::getCustomerCodeLoggedIn());
    $open_balance_text = APUtils::number_format($open_balance);
    $submit_url = base_url() . 'customers/invoice_payment';
?>
<div style="margin: auto 0px;">
    <div style="width: 100%">
        Your account has an open balance of <?php echo $open_balance_text?> EUR. <br/>
        Please make a new deposit into your account via bank transfer or paypal payment
    </div>
</div>
<form id="invoicePaymentForm" method="post" class="dialog-form"
	action="<?php echo $submit_url?>">
	<table style="border: 0px none; width: 100%">
	    <tr>
			<th colspan="2" style="border: 0px none;">Our bank details are as follows</th>
		</tr>
	    <tr>
	        <td>IBAN</td>
	        <td><input type="text" style="background: #fbfbfb" readonly="readonly" id="invoicePaymentForm_iban" name="iban" value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_IBAN_CODE)?>" class="input-txt-none"/></td>
	    </tr>
	    <tr>
	        <td>BIC</td>
	        <td><input type="text" style="background: #fbfbfb"  readonly="readonly" id="invoicePaymentForm_bic" name="bic" value="<?php echo Settings::get(APConstants::INSTANCE_OWNER_SWIFT_CODE)?>" class="input-txt-none"/></td>
	    </tr>
	    <tr>
	        <td colspan="2" style="text-align: center;"><button id="invoicePaymentForm_paynowWithPaypal">Pay now with paypal</button> </td>
	    </tr>
	</table>
</form>
<script type="text/javascript">
jQuery(document).ready(function($){
    $('#invoicePaymentForm_paynowWithPaypal').button();
    $('#invoicePaymentForm_paynowWithPaypal').live('click', function() {
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
		
		$('#invoicePaymentWindow').dialog('close');
    	return false;
	});
	
});
</script>