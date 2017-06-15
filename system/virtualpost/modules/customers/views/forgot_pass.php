<?php
    $submit_url = base_url().'customers/forgot_pass';
?>
<form id="customerForgotPasswordForm" method="post" class="dialog-form"
	action="<?php echo $submit_url?>">
	<table>
		<tr>
			<th>E-mail <span class="required">*</span></th>
			<td><input type="text" id="customerForgotPasswordForm_email" name="email"
				value="<?php echo $customer->email?>"
				class="input-width custom_autocomplete" maxlength=50 /></td>
		</tr>
	</table>
</form>