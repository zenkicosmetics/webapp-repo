<div class="header">
	<h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('setting_view_api_google_SettingsAPIsGoogleAdWords'); ?></h2>
</div>
<form id="usesrSearchForm" method="post"
	action="<?php echo base_url()?>settings/api/google_adwords">
	<div class="input-form">
		<table class="settings">
		    <tr>
				<th class="input-width-200"><?php admin_language_e('setting_view_api_google_APIKey'); ?></th>
				<td><input type="text" id="GOOGLE_ADWORD_API_KEY" name="GOOGLE_ADWORD_API_KEY"
					value="<?php echo Settings::get(APConstants::GOOGLE_ADWORD_API_KEY)?>"
					class="input-width" /></td>
			</tr>
			<tr>
				<th class="input-width-200"><?php admin_language_e('setting_view_api_google_ClientID'); ?></th>
				<td><input type="text" id="GOOGLE_ADWORD_CLIENT_ID" name="GOOGLE_ADWORD_CLIENT_ID"
					value="<?php echo Settings::get(APConstants::GOOGLE_ADWORD_CLIENT_ID)?>"
					class="input-width" /></td>
			</tr>
			<tr>
				<th class="input-width-200"><?php admin_language_e('setting_view_api_google_ClientSecret'); ?></th>
				<td><input type="text" id="GOOGLE_ADWORD_CLIENT_SECRET"
					name="GOOGLE_ADWORD_CLIENT_SECRET"
					value="<?php echo Settings::get(APConstants::GOOGLE_ADWORD_CLIENT_SECRET)?>"
					class="input-width" /></td>
			</tr>
			<tr>
				<th class="input-width-200" style="vertical-align: top;">&nbsp;</th>
				<td>
					<button id="saveGoogleAdwordButton" class="admin-button"><?php admin_language_e('setting_view_api_google_SaveBtn'); ?></button>
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