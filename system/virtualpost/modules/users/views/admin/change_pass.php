<?php
    $submit_url = base_url() . 'admin/users/change_pass';
?>
<form id="resetPasswordUserForm" method="post" class="dialog-form"
	action="<?php echo $submit_url?>">
	<table>
		<tr>
			<th>Email</th>
			<td><input type="text" id="resetPasswordUserForm_email" name="email"
				value="<?php echo $user->email?>" readonly="readonly"
				class="input-width readonly" maxlength=50 /></td>
		</tr>
		<tr>
			<th>Password <span class="required">*</span></th>
			<td><input type="password" id="resetPasswordUserForm_password" name="password"
				value=""
				class="input-width custom_autocomplete" maxlength=50 /></td>
		</tr>
		<tr>
			<th>Retype Password <span class="required">*</span></th>
			<td><input type="password" id="resetPasswordUserForm_repeat_password" name="repeat_password"
				value=""
				class="input-width custom_autocomplete" maxlength=50 /></td>
		</tr>
	</table>
	<input type="hidden" id="h_user_id" name="id"
		value="<?php echo $user->id?>" />
</form>