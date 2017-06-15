<div style="margin: auto 0px;">
	<div style="width: 100%">
        <?php language_e('cases_view_payment_bank_tranfer_PleaseMakeAPaymentWithBalancep', ['amount' => $amount, 'currency'	=>	'EUR']); ?>
	</div>
</div>
<table style="border: 0px none; width: 100%">
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