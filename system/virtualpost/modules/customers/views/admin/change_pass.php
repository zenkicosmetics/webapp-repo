<?php
    $submit_url = base_url() . 'admin/customers/change_pass';
?>
<?php //echo "<pre>";print_r($customer);exit; ?>
<form id="resetPasswordCustomerForm" method="post" class="dialog-form"
	action="<?php echo $submit_url?>">
	<table>
		<tr>
			<th>E-mail <span class="required">*</span></th>
			<td><input type="text" id="addEditCustomerForm_email" name="email"
				value="<?php echo $customer->email?>" readonly="readonly"
				class="input-width readonly" maxlength=50 /></td>
		</tr>
		<tr>
			<th>Password <span class="required">*</span></th>
			<td><input type="password" id="addEditCustomerForm_password" name="password"
				value="<?php echo $customer->password?>"
				class="input-width custom_autocomplete" maxlength=50 /></td>
		</tr>
		<tr>
			<th>Retype Password <span class="required">*</span></th>
			<td><input type="password" id="addEditCustomerForm_repeat_password" name="repeat_password"
				value="<?php echo $customer->repeat_password?>"
				class="input-width custom_autocomplete" maxlength=50 /></td>
		</tr>
	</table>
	<input type="hidden" id="h_action_type" name="h_action_type"
		value="<?php echo $action_type?>" /> <input type="hidden" id="id"
		name="id" value="<?php echo $customer->customer_id?>" />
</form>