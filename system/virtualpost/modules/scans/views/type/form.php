<?php

if ($action_type == 'add') {
    $submit_url = base_url().'scans/type/add';
} else {
    $submit_url = base_url().'scans/type/edit';
}
?>

<form id="addEditUserForm" method="post" action="<?php echo $submit_url?>">
	<table>
		<tr>
			<th><?php admin_language_e('scan_view_type_form_TypeName'); ?><span class="required">*</span></th>
			<td><input type="text" id="LabelValue" name="LabelValue" value="<?php echo $category->LabelValue;?>" class="input-width custom_autocomplete"/></td>
		</tr>
		<tr>
			<th><?php admin_language_e('scan_view_type_form_Customs'); ?></th>
			<td><input type="checkbox" id="Alias01" name="Alias01"
				value="1" class="custom_autocomplete" <?php if ($category->Alias01 == '1') {?> checked="checked" <?php }?> /></td>
		</tr>
		<tr>
			<th><?php admin_language_e('scan_view_type_form_Type'); ?></th>
			<td>
			    <select class="input-width" id="Alias02" name="Alias02">
			        <option value="Letter" <?php if ($category->Alias02 == 'Letter') {?> selected="selected" <?php }?>><?php admin_language_e('scan_view_type_form_Letter'); ?></option>
			        <option value="Package" <?php if ($category->Alias02 == 'Package') {?> selected="selected" <?php }?>><?php admin_language_e('scan_view_type_form_Package'); ?></option>
			     </select>    
			</td>
		</tr>
	</table>
	<input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type?>" />
	<input type="hidden" id="h_user_id" name="SettingKey" value="<?php echo $category->SettingKey;?>" />
</form>
