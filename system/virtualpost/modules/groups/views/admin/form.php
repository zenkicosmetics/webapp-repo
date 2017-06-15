<?php

if ($action_type == 'add') {
    $submit_url = base_url().'groups/admin/add';
} else {
    $submit_url = base_url().'groups/admin/edit';
}
?>

<form id="addEditGroupForm" method="post"
	action="<?php echo $submit_url?>">
	<table>
		<tr>
			<th>Name <span class="required">*</span></th>
			<td><input type="text" id="Name" name="Name"
				value="<?php echo $group->Name?>"
				class="input-width custom_autocomplete" maxlength=50 /></td>
		</tr>
		<tr>
			<th>Description <span class="required">*</span></th>
			<td><input type="text" id="Description" name="Description"
				value="<?php echo $group->Description?>"
				class="input-width custom_autocomplete" maxlength=500 /></td>
		</tr>
	</table>
	<input type="hidden" id="h_action_type" name="h_action_type"
		value="<?php echo $action_type?>" />
	<input type="hidden" id="h_group_id" name="id"
		value="<?php echo $group->ID?>" />
</form>