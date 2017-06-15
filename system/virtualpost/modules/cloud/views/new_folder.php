<form id="newDropboxFolderForm"
	action="<?php echo base_url()?>cloud/new_folder" method="post">
	<input type="text" id="newDropboxFolderForm_folder_name"
		name="folder_name" value="" class="input-width custom_autocomplete" style="margin: 30px" />
	<input type="hidden" id="newDropboxFolderForm_parent_folder_name"
		name="parent_folder_name" value="<?php echo $parent_folder_name?>" class="input-width custom_autocomplete"/>
</form>