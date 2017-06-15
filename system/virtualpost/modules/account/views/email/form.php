<?php
    $submit_url = base_url() . 'account/email_template/edit';
?>
<form id="addEmailTemplateForm" method="post" class="dialog-form"
	action="<?php echo $submit_url?>">
	<table>
		<tr>
			<th><label for="subject"><?php echo lang('email.subject_label');?></label></th>
			<td><?php echo form_input('subject', $email->subject, 'class="input-width"');?></td>
		</tr>
                <tr>
			<th><label for="language"><?php echo lang('email.language');?></label></th>
                        <td>
                            <?php echo my_form_dropdown(array(
                                "data" => $languages,
                                "value_key" => 'language',
                                "label_key" => 'language',
                                "value" => $email->language,
                                "name" => 'language',
                                "id" => 'language',
                                "clazz" => 'input-width',
                                "style" => 'width: 150px;',
                                "has_empty" => false
                             )); ?>
                        </td>
		</tr>
		<tr>
			<th><label for="description"><?php echo lang('email.description_label');?> </label></th>
			<td>
                            <?php echo form_textarea(array('id' => 'description', 'name' => 'description', 'value' => $email->description, 'rows' => 6, 'class' => 'input-width', 'style'=>'width: 400px')); ?>
                        </td>
		</tr>
		<tr>
			<th><label for="content"><?php echo lang('email.content_label');?></label></th>
			<td>
                            <?php echo form_textarea(array('id' => 'content_temp', 'name' => 'content_temp', 'value' => $email->content, 'rows' => 6)); ?>
                        </td>
		</tr>
		<tr>
			<th>&nbsp;</th>
			<td>
				<button id="saveTemplate" type="button">Save</button>
				<button id="cancelTemplate" type="button">Cancel</button>
			</td>
		</tr>
	</table>
	<input type="hidden" id="h_action_type" name="h_action_type"
		value="<?php echo $action_type?>" /> <input type="hidden" id="id"
		name="id" value="<?php echo $email->id?>" />
	<input type="hidden" id="content"
		name="content" value="" />
</form>

<script type="text/javascript">
	CKEDITOR.replace( 'content_temp' );
	$('button').button();
    
	/**
     * Submit add template
     */
    $('#saveTemplate').click(function() {
        var submitUrl = $('#addEmailTemplateForm').attr('action');
        var editorText = CKEDITOR.instances.content_temp.getData();
        $('#content').val(editorText);
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'addEmailTemplateForm',
            success: function(data) {
                
                if (data.status) {
                	$.displayInfor(data.message, null, function() {
                        document.location = '<?php echo base_url()?>account/email_template/index';
                    });
                } else {
                    $.displayError(data.message);
                }
            }
        });
        return false;
    });

    /**
     * Back to email template list
     */
    $('#cancelTemplate').click(function(){
    	document.location = '<?php echo base_url()?>account/email_template/index';
    });
</script>
