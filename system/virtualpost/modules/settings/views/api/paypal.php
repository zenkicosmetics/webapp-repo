<div class="header">
	<h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('setting_view_api_paypal_SettingsAPIsPaypal'); ?></h2>
</div>
<form id="usesrSearchForm" method="post"	action="<?php echo base_url()?>settings/api/paypal">
	<div class="input-form">
		<h2><?php admin_language_e('setting_view_api_paypal_PaypalSettingProductionSystem'); ?></h2>
		<table class="settings">
		    <tr>
				<th class="input-width-200"><?php admin_language_e('setting_view_api_paypal_SignatureCode'); ?></th>
				<td><input type="text" id="PAYMENT_PAYPAL_SIGNATURE_CODE" name="PAYMENT_PAYPAL_SIGNATURE_CODE"
					value="<?php echo Settings::get(APConstants::PAYMENT_PAYPAL_SIGNATURE_CODE)?>"
					class="input-width" /></td>
			</tr>
			<tr>
				<th class="input-width-200"><?php admin_language_e('setting_view_api_paypal_UserName'); ?></th>
				<td><input type="text" id="PAYMENT_PAYPAL_USERNAME_CODE" name="PAYMENT_PAYPAL_USERNAME_CODE"
					value="<?php echo Settings::get(APConstants::PAYMENT_PAYPAL_USERNAME_CODE)?>"
					class="input-width" /></td>
			</tr>
			<tr>
				<th class="input-width-200"><?php admin_language_e('setting_view_api_paypal_Password'); ?></th>
				<td><input type="text" id="PAYMENT_PAYPAL_PASSWORD_CODE"
					name="PAYMENT_PAYPAL_PASSWORD_CODE"
					value="<?php echo Settings::get(APConstants::PAYMENT_PAYPAL_PASSWORD_CODE)?>"
					class="input-width" /></td>
			</tr>
			
			<tr>
				<th class="input-width-200"><?php admin_language_e('setting_view_api_paypal_MerchantID'); ?></th>
				<td><input type="text" id="PAYMENT_PAYPAL_MERCHANT_ID"
					name="PAYMENT_PAYPAL_MERCHANT_ID"
					value="<?php echo Settings::get(APConstants::PAYMENT_PAYPAL_MERCHANT_ID)?>"
					class="input-width" /></td>
			</tr>
			<tr>
				<th class="input-width-200" style="vertical-align: top;">&nbsp;</th>
				<td>
					<button id="savePayoneButton" class="admin-button"><?php admin_language_e('setting_view_api_paypal_SaveBtn'); ?></button>
				</td>
			</tr>
		</table>
	</div>
</form>
<script type="text/javascript">
$(document).ready( function() {
	$('.admin-button').button();
});
</script>