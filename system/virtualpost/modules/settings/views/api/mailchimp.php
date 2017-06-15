<div class="header">
	<h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('setting_view_api_mailchimp_SettingsAPIsMailChimp'); ?></h2>
</div>
<form id="usesrSearchForm" method="post"	action="<?php echo base_url()?>settings/api/mailchimp">
	<div class="input-form">
		<h2><?php admin_language_e('setting_view_api_mailchimp_MailChimpSetting'); ?></h2>
		<table class="settings">
		    <tr>
				<th class="input-width-200"><?php admin_language_e('setting_view_api_mailchimp_APIKey'); ?></th>
				<td><input type="text" id="MAILCHIMP_API_KEY" name="MAILCHIMP_API_KEY"
					value="<?php echo Settings::get(APConstants::MAILCHIMP_API_KEY)?>"
					class="input-width" /></td>
			</tr>
			<tr>
				<th class="input-width-200"><?php admin_language_e('setting_view_api_mailchimp_ListID'); ?></th>
				<td><input type="text" id="MAILCHIMP_LIST_ID" name="MAILCHIMP_LIST_ID"
					value="<?php echo Settings::get(APConstants::MAILCHIMP_LIST_ID)?>"
					class="input-width" /></td>
			</tr>
			<tr>
				<th class="input-width-200" style="vertical-align: top;">&nbsp;</th>
				<td>
					<button id="saveMailchimeButton" class="admin-button"><?php admin_language_e('setting_view_api_mailchimp_SaveBtn'); ?></button>
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