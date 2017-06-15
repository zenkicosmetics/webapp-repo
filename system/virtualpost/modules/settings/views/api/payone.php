<div class="header">
	<h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('setting_view_api_payone_SettingsAPIsPayone'); ?></h2>
</div>
<form id="usesrSearchForm" method="post"
	action="<?php echo base_url()?>settings/api/payone">
	<div class="input-form">
		<h2><?php admin_language_e('setting_view_api_payone_PayoneSettingLive'); ?></h2>
		<table class="settings">
			<tr>
				<th class="input-width-200"><?php admin_language_e('setting_view_api_payone_MerchantID'); ?></th>
				<td><input type="text" id="MERCHANT_ID_CODE" name="MERCHANT_ID_CODE"
					value="<?php echo Settings::get(APConstants::MERCHANT_ID_CODE)?>"
					class="input-width" /></td>
			</tr>
			<tr>
				<th class="input-width-200"><?php admin_language_e('setting_view_api_payone_SubAccountID'); ?></th>
				<td><input type="text" id="SUB_ACCOUNT_ID_CODE"
					name="SUB_ACCOUNT_ID_CODE"
					value="<?php echo Settings::get(APConstants::SUB_ACCOUNT_ID_CODE)?>"
					class="input-width" /></td>
			</tr>
			<tr>
				<th class="input-width-200"><?php admin_language_e('setting_view_api_payone_PortalID'); ?></th>
				<td><input type="text" id="PORTAL_ID_CODE" name="PORTAL_ID_CODE"
					value="<?php echo Settings::get(APConstants::PORTAL_ID_CODE)?>"
					class="input-width" /></td>
			</tr>
			<tr>
				<th class="input-width-200"><?php admin_language_e('setting_view_api_payone_PortalKEY'); ?></th>
				<td><input type="text" id="PORTAL_KEY_CODE" name="PORTAL_KEY_CODE"
					value="<?php echo Settings::get(APConstants::PORTAL_KEY_CODE)?>"
					class="input-width" /></td>
			</tr>
		</table>

		<h2><?php admin_language_e('setting_view_api_payone_PayoneSettingDev'); ?></h2>
		<table class="settings">
			<!-- Using for test -->
			<tr>
				<th class="input-width-200"><?php admin_language_e('setting_view_api_payone_MerchantID'); ?></th>
				<td><input type="text" id="TEST_MERCHANT_ID_CODE"
					name="TEST_MERCHANT_ID_CODE"
					value="<?php echo Settings::get(APConstants::TEST_MERCHANT_ID_CODE)?>"
					class="input-width" /></td>
			</tr>
			<tr>
				<th class="input-width-200"><?php admin_language_e('setting_view_api_payone_SubAccountID'); ?></th>
				<td><input type="text" id="TEST_SUB_ACCOUNT_ID_CODE"
					name="TEST_SUB_ACCOUNT_ID_CODE"
					value="<?php echo Settings::get(APConstants::TEST_SUB_ACCOUNT_ID_CODE)?>"
					class="input-width" /></td>
			</tr>
			<tr>
				<th class="input-width-200"><?php admin_language_e('setting_view_api_payone_PortalID'); ?></th>
				<td><input type="text" id="TEST_PORTAL_ID_CODE"
					name="TEST_PORTAL_ID_CODE"
					value="<?php echo Settings::get(APConstants::TEST_PORTAL_ID_CODE)?>"
					class="input-width" /></td>
			</tr>
			<tr>
				<th class="input-width-200"><?php admin_language_e('setting_view_api_payone_PortalKEY'); ?></th>
				<td><input type="text" id="TEST_PORTAL_KEY_CODE"
					name="TEST_PORTAL_KEY_CODE"
					value="<?php echo Settings::get(APConstants::TEST_PORTAL_KEY_CODE)?>"
					class="input-width" /></td>
			</tr>
			<tr>
				<th class="input-width-200" style="vertical-align: top;">&nbsp;</th>
				<td>
					<button id="savePayoneButton" class="admin-button"><?php admin_language_e('setting_view_api_payone_SaveBtn'); ?></button>
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