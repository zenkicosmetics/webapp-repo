<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('setting_view_term_addprivacy_SettingsTermsPrivacy'); ?></h2>
</div>
<?php
    $submit_url = base_url().'settings/terms/add_privacy';
    if (!empty($action) && $action == 'edit') {
        $submit_url = base_url().'settings/terms/edit_privacy';
    }
?>
<form id="addPrivacyServiceForm" method="post" class="dialog-form"
    action="<?php echo $submit_url?>">
    <table>
        <tr>
            <th colspan="2"><?php admin_language_e('setting_view_term_addprivacy_AddEditPrivacy'); ?></th>
        </tr>
        <tr>
			<th><label for="content"><?php admin_language_e('setting_view_term_addprivacy_Content'); ?></label></th>
			<td>
					<?php echo form_textarea(array('id' => 'content_temp', 'name' => 'content_temp', 'value' => $content, 'rows' => 10)); ?>
				</td>
		</tr>
        <tr>
			<th>&nbsp;</th>
			<td>
			    <?php if (empty($terms) || $terms->use_flag == '1') {?>
				<button id="saveTemplate" type="button"><?php admin_language_e('setting_view_term_addprivacy_SaveBtn'); ?></button>
				<?php }?>
				<button id="cancelButton" type="button"><?php admin_language_e('setting_view_term_addprivacy_CancelBtn'); ?></button>
			</td>
		</tr>
    </table>
    <input type="hidden" id="addPrivacyServiceForm_id" name="id" value="<?php echo $id?>" />
    <input type="hidden" id="content" name="content" value="" />
</form>
<script type="text/javascript">
$(document).ready( function() {
	CKEDITOR.replace( 'content_temp' );
	$('button').button();
    
	/**
     * Submit add template
     */
    $('#saveTemplate').click(function() {
        var submitUrl = $('#addPrivacyServiceForm').attr('action');
        var editorText = CKEDITOR.instances.content_temp.getData();
        $('#content').val(editorText);
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'addPrivacyServiceForm',
            success: function(data) {
                if (data.status) {
                	$.displayInfor(data.message, null, function() {
                        document.location = '<?php echo base_url()?>settings/terms/privacy';
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
        return false;
    });

    /**
     * Cancel
     */
    $('#cancelButton').click(function(){
    	document.location = '<?php echo base_url()?>settings/terms/privacy';
    });
});
</script>