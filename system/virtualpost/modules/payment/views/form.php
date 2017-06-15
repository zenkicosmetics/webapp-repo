<?php
$submit_url = base_url().'payment/add';

?>

<form id="addEditPaymentMethodForm" method="post" action="<?php echo $submit_url?>">
	<table>
	    <tr style="display: none;">
			<th>Invoice Type <span class="required">*</span></th>
			<td>
    			<select id="addEditPaymentMethod_invoice_type" name="invoice_type" class="input-width" style="line-height: 24px; width: 262px;">
    			    <option value="1">Standard payment</option>
    			    <option value="2">Invoice payment</option>
    			</select>
			</td>
		</tr>
		<tr id="traddEditPaymentMethod_invoice_code" style="display: none;">
			<th>Invoice Code <span class="required">*</span></th>
			<td><input type="text" id="addEditPaymentMethod_invoice_code" name="invoice_code"
				value=""
				class="input-width custom_autocomplete" maxlength=255 /></td>
		</tr>
		<tr id="traddEditPaymentMethod_account_type">
			<th>Payment selection: <span class="required">*</span></th>
			<td>
    			<select id="addEditPaymentMethod_account_type" name="account_type" class="input-width" style="line-height: 24px;width: 262px;">
    			     <option value="<?php echo APConstants::PAYMENT_CREDIT_CARD_ACCOUNT?>">Credit Card Account</option>
    			     <option value="<?php echo APConstants::PAYMENT_DIRECT_DEBIT_ACCOUNT?>">Invoice with deposit</option>
    			</select>
			</td>
		</tr>
		<tr id="traddEditPaymentMethod_paypal_information" style="display: none;">
			<td colspan="2">
			     If you chose to pay us manually, you will have to make a deposit payment of at least 10 EUR into your account. You can do this by bank transfer or PayPal.
                <br/>
                Your account will be able to receive incoming items immediately. However your activity will be limited if your account runs into a negative balance.
			</td>
		</tr>
		<tr id="traddEditPaymentMethod_paypal_account" style="display: none;">
			<th>Paypal Account <span class="required">*</span></th>
			<td>
			    <input type="text" id="addEditPaymentMethod_paypal_account" name="paypal_account"
				value=""
				class="input-width custom_autocomplete" maxlength=255 />
				
			</td>
		</tr>
		<tr id="traddEditPaymentMethod_card_type">
			<th>Card Type <span class="required">*</span></th>
			<td>
    			<select id="addEditPaymentMethod_card_type" name="card_type" class="input-width" style="line-height: 24px; width: 262px;">
    			    <option value="V">VISA</option>
    			    <option value="M">MasterCard</option>
    			    <option value="J">JCB</option>
    			</select>
			</td>
		</tr>
		<tr id="traddEditPaymentMethod_card_number">
			<th>Card Number <span class="required">*</span></th>
			<td><input type="text" id="addEditPaymentMethod_card_number" name="card_number"
				value=""
				class="input-width custom_autocomplete" maxlength=255 /></td>
		</tr>
		<tr id="traddEditPaymentMethod_card_name">
			<th>Name of Cardholder <span class="required">*</span></th>
			<td><input type="text" id="addEditPaymentMethod_card_name" name="card_name"
				value=""
				class="input-width custom_autocomplete" maxlength=255 /></td>
		</tr>
		<tr id="traddEditPaymentMethod_expired">
			<th>Expiration date <span class="required">*</span></th>
			<td>
			    Year
			    <select id="addEditPaymentMethod_expired_year" name="expired_year" class="input-width" style="line-height: 24px; width: 75px">
    			    <?php $cur_date = now();?>
    			    <?php for ($i = $cur_date; $i < $cur_date + 20 * (365 * 24 * 60 * 60); $i = $i + (365 * 24 * 60 * 60)) {?>
    			    <option value="<?php echo date('y', $i);?>"><?php echo date('Y', $i);?></option>
    			    <?php }?>
    			</select>
    			Month
    			<select id="addEditPaymentMethod_expired_month" name="expired_month" class="input-width" style="line-height: 24px; width: 75px">
    			    <?php for ($i = 1; $i < 13; $i++) {?>
    			    <option value="<?php if ($i < 10) {echo '0'.$i;} else {echo $i;}?>"><?php echo $i?></option>
    			    <?php }?>
    			</select>
			</td>
		</tr>
		<tr id="traddEditPaymentMethod_cvc">
			<th>CVC/CVV <span class="required">*</span></th>
			<td><input type="text" id="addEditPaymentMethod_cvc" name="cvc"
				value="" style="width: 100px"
				class="input-width custom_autocomplete" maxlength=4 /></td>
		</tr>
		<tr id="traddEditPaymentMethod_trustwaveSealImage">
			<th>&nbsp;</th>
			<td><img id="trustwaveSealImage" src="https://sealserver.trustwave.com/seal_image.php?customerId=9b197218d48f4172a58c080d0e223214&size=105x54&style=invert" border="0" style="cursor:pointer;margin-top: 5px;" oncontextmenu="javascript:alert('Copying Prohibited by Law - Trusted Commerce is a Service Mark of TrustWave Holdings, Inc.'); return false;" alt="This site protected by Trustwave's Trusted Commerce program" title="This site protected by Trustwave's Trusted Commerce program" /></td>
		</tr>
	</table>
	<input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type?>" />
	<input type="hidden" id="addEditPaymentMethod_pseudocardpan" name="pseudocardpan" value="" />
	<input type="hidden" id="addEditPaymentMethod_truncatedcardpan" name="truncatedcardpan" value="" />
</form>
<script type="text/javascript" src="https://sealserver.trustwave.com/seal.js?style=invert&code=9b197218d48f4172a58c080d0e223214"></script>
<script type="text/javascript">
jQuery(document).ready(function($){
	$('#trustwaveSealImage').live('click', function(){
		javascript:window.open('https://sealserver.trustwave.com/cert.php?customerId=9b197218d48f4172a58c080d0e223214&size=105x54&style=invert', 'c_TW', 'location=no, toolbar=no, resizable=yes, scrollbars=yes, directories=no, status=no, width=615, height=720'); 
        return false;
	});

	// Account type change (20: Paypal | 30: Credit card)
	$('#addEditPaymentMethod_account_type').change(function() {
	    var accountType = $('#addEditPaymentMethod_account_type').val();
	    if (accountType == '10') {
		    $('#traddEditPaymentMethod_paypal_information').show();
	        $('#traddEditPaymentMethod_paypal_account').hide();
	        $('#traddEditPaymentMethod_card_type').hide();
	        $('#traddEditPaymentMethod_card_number').hide();
	        $('#traddEditPaymentMethod_card_name').hide();
	        $('#traddEditPaymentMethod_expired').hide();
	        $('#traddEditPaymentMethod_cvc').hide();
	        
		} else if (accountType == '20') {
			$('#traddEditPaymentMethod_paypal_information').hide();
	        $('#traddEditPaymentMethod_paypal_account').show();
	        $('#traddEditPaymentMethod_card_type').hide();
	        $('#traddEditPaymentMethod_card_number').hide();
	        $('#traddEditPaymentMethod_card_name').hide();
	        $('#traddEditPaymentMethod_expired').hide();
	        $('#traddEditPaymentMethod_cvc').hide();
	        
		} else if (accountType == '30') {
			$('#traddEditPaymentMethod_paypal_information').hide();
			$('#traddEditPaymentMethod_paypal_account').hide();
			$('#traddEditPaymentMethod_card_type').show();
	        $('#traddEditPaymentMethod_card_number').show();
	        $('#traddEditPaymentMethod_card_name').show();
	        $('#traddEditPaymentMethod_expired').show();
	        $('#traddEditPaymentMethod_cvc').show();
		}
	});
});
</script>
