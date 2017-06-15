<?php

if ($action_type == 'add') {
    $submit_url = base_url() . 'admin/customers/add_customer_blacklist';
}
?>
<form id="addEditCustomerBlackListForm" method="post" class="dialog-form"
	action="<?php echo $submit_url?>">
	<table>
	    <tr>
			<th>E-mail <span class="required">*</span></th>
			<td><input type="text" id="addEditCustomerBlackListForm_email" name="email"
				value="<?php echo $customer_blacklist->email?>"
				class="input-width" maxlength=50 /></td>
		</tr>
	</table>
	<input type="hidden" id="h_action_type" name="h_action_type"
		value="<?php echo $action_type?>" /> <input type="hidden" id="id"
		name="id" value="<?php echo $customer_blacklist->id?>" />
</form>
<script type="text/javascript">
jQuery(document).ready(function($){
	
});
</script>
