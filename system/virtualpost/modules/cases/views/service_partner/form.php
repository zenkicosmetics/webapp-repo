<?php
if ($action_type == 'add') {
	$submit_url = base_url() . 'cases/service_partner/add';
} else {
	$submit_url = base_url() . 'cases/service_partner/edit';
}
?>
<form id="addEditServicePartnerForm" method="post" action="<?php echo $submit_url ?>" autocomplete="on">
	<table>
		<tr>
			<th><?php admin_language_e('cases_view_service_partner_form_PartnerName'); ?> <span class="required">*</span></th>
			<td><input type="text" class="input-width " id="partner_name" name="partner_name"
			           value="<?php echo $ServicePartner->partner_name ?>" class="input-width" maxlength="250"/></td>
		</tr>
		<tr>
			<th><?php admin_language_e('cases_view_service_partner_form_ContactName'); ?> <span class="required">*</span></th>
			<td><input type="text" class="input-width " id="main_contact_point" name="main_contact_point"
			           value="<?php echo $ServicePartner->main_contact_point ?>" class="input-width" maxlength="250"/></td>
		</tr>
		<tr>
			<th><?php admin_language_e('cases_view_service_partner_form_Email'); ?> <span class="required">*</span></th>
			<td><input type="text" class="input-width " id="email" name="email"
			           value="<?php echo $ServicePartner->email ?>" class="input-width" maxlength="250"/></td>
		</tr>
		<tr>
			<th><?php admin_language_e('cases_view_service_partner_form_PhoneNumber'); ?> <span class="required">*</span></th>
			<td><input type="text" class="input-width " id="phone" name="phone"
			           value="<?php echo $ServicePartner->phone ?>" class="input-width" maxlength="250"/></td>
		</tr>
	</table>
	<input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type ?>"/> <input
		type="hidden" id="h_id" name="id"
		value="<?php echo $ServicePartner->id ?>"/>
</form>