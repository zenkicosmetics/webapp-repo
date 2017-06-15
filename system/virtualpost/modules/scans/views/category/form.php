<?php

if ($action_type == 'add') {
    $submit_url = base_url().'scans/category/add';
} else {
    $submit_url = base_url().'scans/category/edit';
}
?>

<form id="addEditUserForm" method="post"
	action="<?php echo $submit_url?>">
	<table>
		<tr>
			<th>Category Name <span class="required">*</span></th>
			<td><input type="text" id="LabelValue" name="LabelValue"
				value="<?php echo $category->LabelValue;?>" class="input-width custom_autocomplete" maxlength=50 /></td>
		</tr>
	</table>
	<input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type?>" />
	<input type="hidden" id="h_user_id" name="SettingKey" value="<?php echo $category->SettingKey;?>" />
</form>
<script type="text/javascript">
$(document).ready( function() {
	
});
</script>