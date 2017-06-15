<?php

if ($action_type == 'add') {
    $submit_url = base_url() . 'admin/instances/add';
} else {
    $submit_url = base_url() . 'admin/instances/edit';
}
?>
<form id="addEditInstanceForm" method="post" class="dialog-form"
	action="<?php echo $submit_url?>">
	<table>
	    <tr>
			<th>Name <span class="required">*</span></th>
			<td><input type="text" id="addEditInstanceForm_name" name="name"
				value="<?php echo $instance->name?>"
				class="input-width" maxlength=50 /></td>
			<th></th>
			<td></td>
		</tr>
		<tr>
			<th>Domain type <span class="required">*</span></th>
			<td>
			    <select class="input-width" id="addEditInstanceForm_domain_type" name="domain_type">
			        <option value="0" <?php if ($instance_domain->domain_type == '0') {?> selected="selected" <?php }?>>Sub Domain</option>
			        <option value="1" <?php if ($instance_domain->domain_type == '1') {?> selected="selected" <?php }?>>Customer Domain</option>
			     </select>
			</td>
			<th>Domain name <span class="required">*</span></th>
			<td><input type="text" id="addEditInstanceForm_domain_name" name="domain_name"
				value="<?php echo $instance_domain->domain_name?>"
				class="input-width custom_autocomplete" maxlength=50 /></td>
		</tr>
		<tr>
			<th>Domain URL <span class="required">*</span></th>
			<td colspan="3">
			    <input type="text" id="addEditInstanceForm_full_url" name="full_url"
				value="<?php echo $instance_domain->full_url?>"
				class="input-width custom_autocomplete" maxlength=50 />
			</td>
		</tr>
		<tr>
		    <th>S3 type <span class="required">*</span></th>
			<td>
			    <select class="input-width" id="addEditInstanceForm_s3_type" name="s3_type">
			        <option value="0" <?php if ($instance_amazon->s3_type == '0') {?> selected="selected" <?php }?>>ClevverMail S3</option>
			        <option value="1" <?php if ($instance_amazon->s3_type == '1') {?> selected="selected" <?php }?>>Customer S3</option>
			     </select>
			</td>
	        <th>S3 name <span class="required">*</span></th>
			<td><input type="text" id="addEditInstanceForm_s3_name" name="s3_name"
				value="<?php echo $instance_amazon->s3_name?>"
				class="input-width custom_autocomplete" maxlength=50 /></td>
		</tr>
		<tr>
		    <th>Database type <span class="required">*</span></th>
			<td>
			    <select class="input-width" id="addEditInstanceForm_database_type" name="database_type">
			        <option value="0" <?php if ($instance_database->database_type == '0') {?> selected="selected" <?php }?>>ClevverMail Database</option>
			        <option value="1" <?php if ($instance_database->database_type == '1') {?> selected="selected" <?php }?>>Customer Database</option>
			     </select>
			</td>
	        <th>Database name <span class="required">*</span></th>
			<td><input type="text" id="addEditInstanceForm_database_name" name="database_name"
				value="<?php echo $instance_database->database_name?>" autocomplete="off"
				class="input-width custom_autocomplete" maxlength=50 /></td>
		</tr>
		<tr>
		    <th>Database Host Address <span class="required">*</span></th>
			<td><input type="text" id="addEditInstanceForm_host_address" name="host_address"
				value="<?php echo $instance_database->host_address?>"
				class="input-width custom_autocomplete" maxlength=50 /></td>
	        <th>Database User Name <span class="required">*</span></th>
			<td><input type="text" id="addEditInstanceForm_username" name="username"
				value="<?php echo $instance_database->username?>" autocomplete="off"
				class="input-width custom_autocomplete" maxlength=50 /></td>
		</tr>
		<tr>
		    <th>Database Password <span class="required">*</span></th>
			<td><input type="password" id="addEditInstanceForm_password" name="password"
				value="<?php echo $instance_database->password?>" autocomplete="off"
				class="input-width custom_autocomplete" maxlength=50 /></td>
		</tr>
	</table>
	<input type="hidden" id="h_action_type" name="h_action_type"
		value="<?php echo $action_type?>" /> <input type="hidden" id="id"
		name="id" value="<?php echo $instance->instance_id?>" />
</form>
<script type="text/javascript">
jQuery(document).ready(function($){
});
</script>