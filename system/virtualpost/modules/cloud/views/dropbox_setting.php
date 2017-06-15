<?php
    $submit_url = base_url().'cloud/dropbox_setting';
?>

<form id="editCloudSettingForm" method="post"
	action="<?php echo $submit_url?>">
	<table>
		<tr>
			<th><?php echo lang('cloud.folder_name')?></th>
			<td><input type="text" id="editCloudSettingForm_folder_name" name="folder_name"
				value="<?php echo $setting['folder_name'] ?>"
				class="input-width custom_autocomplete" maxlength=50 /></td>
		</tr>
		<tr>
			<th><?php echo lang('cloud.autu_save')?></th>
			<td style="text-align: left;">
			    <input type="checkbox" id="editCloudSettingForm_auto_save" name="auto_save_flag" value="1" class="customCheckbox" <?php if ($setting['auto_save_flag'] === '1') {?>checked="checked"<?php }?>/>
			</td>
		</tr>
		<tr>
			<td style="text-align: left;" colspan="2" >
			    <a href="<?php echo base_url()?>mailbox/request_dropbox" class="tooltip" style="color: #0E76BC;font-family: arial,helvetica,sans-serif;font-size: 12px;text-decoration: underline;" id="changeDropboxLoginPassword"><?php language_e('cloud_view_dropbox_setting_ChangeLoginpassword'); ?></a>
			</td>
		</tr>
	</table>
</form>
<script type="text/javascript">
$('input:checkbox.customCheckbox').checkbox({cls:'jquery-safari-checkbox'});
</script>