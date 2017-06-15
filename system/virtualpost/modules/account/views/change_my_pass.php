<?php
    $submit_url = base_url().'account/change_my_pass';
?>
<form id="changeMyPasswordForm" method="post" class="dialog-form"
	action="<?php echo $submit_url?>">
	<table>
	    <tr>
			<th>Current Password <span class="required">*</span></th>
			<td><input type="password" id="changeMyPasswordForm_current_password" name="current_password"
				value=""
				class="input-width" maxlength=50 /></td>
		</tr>
		<tr>
			<th>Password <span class="required">*</span></th>
			<td><input type="password" id="changeMyPasswordForm_password" name="password"
				value=""
				class="input-width custom_autocomplete" maxlength=50 /></td>
		</tr>
		<tr>
			<th>Retype Password <span class="required">*</span></th>
			<td><input type="password" id="changeMyPasswordForm_repeat_password" name="repeat_password"
				value=""
				class="input-width custom_autocomplete" maxlength=50 /></td>
		</tr>
	</table>
</form>