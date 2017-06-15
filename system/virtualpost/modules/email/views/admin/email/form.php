<?php
if ($action_type == 'add') {
    $submit_url = base_url() . 'admin/email/add';
} else {
    $submit_url = base_url() . 'admin/email/edit';
}

$readonly = '';
if(!empty($customer_id)){
    $readonly = 'readonly="readonly" disabled="disabled"';
}
?>
<form id="addEmailTemplateForm" method="post" class="dialog-form"
      action="<?php echo $submit_url ?>">
    <table>
        <tr>
            <th><label for="slug"><?php admin_language_e('email_view_admin_email_form_Slug'); ?></label></th>
            <td><?php echo form_input('slug', $email->slug, 'class="input-width" '. $readonly); ?></td>
        </tr>
        <tr>
            <th><label for="subject"><?php admin_language_e('email_view_admin_email_form_Subject'); ?></label></th>
            <td><?php echo form_input('subject', $email->subject, 'class="input-width" '. $readonly); ?></td>
        </tr>
        <tr>
            <th><label for="language"><?php admin_language_e('email_view_admin_email_form_Language'); ?></label></th>
            <td>
                <?php
                echo my_form_dropdown(array(
                    "data" => $languages,
                    "value_key" => 'language',
                    "label_key" => 'language',
                    "value" => $email->language,
                    "name" => 'language',
                    "id" => 'language',
                    "clazz" => 'input-width',
                    "style" => 'width: 150px;',
                    "has_empty" => false,
                    "html_option" => $readonly
                ));
                ?>
            </td>
        </tr>
        <?php if(empty($customer_id)){ ?>
        <tr>
            <th><label for="relevant_enterprise_account"><?php admin_language_e('email_view_admin_email_form_AvailableForEnterprise'); ?></label></th>
            <td>
                <select name="relevant_enterprise_account" id="relevant_enterprise_account" <?php echo $readonly; ?> class="input-width" style="width: 180px;">
                    <option value="0" <?php if ($email->relevant_enterprise_account == '0') { ?> selected="selected" <?php } ?> ><?php admin_language_e('email_view_admin_email_form_No'); ?></option>
                    <option value="1" <?php if ($email->relevant_enterprise_account == '1') { ?> selected="selected" <?php } ?> ><?php admin_language_e('email_view_admin_email_form_Yes'); ?></option>
                </select>
            </td>
        </tr>
        <?php }?>
        <tr>
            <th><label for="description"><?php admin_language_e('email_view_admin_email_form_Description'); ?></label></th>
            <td>
                <?php echo form_textarea(array('id' => 'description', 'name' => 'description', 'value' => $email->description, 'rows' => 6, 'class' => 'input-width', 'style' => 'width: 400px'), '' , $readonly); ?>
            </td>
        </tr>
        <tr>
            <th><label for="content"><?php admin_language_e('email_view_admin_email_form_Content'); ?></label></th>
            <td>
                <?php echo form_textarea(array('id' => 'content_temp', 'name' => 'content_temp', 'value' => $email->content, 'rows' => 6), '', $readonly); ?>
            </td>
        </tr>
        <tr>
            <th>&nbsp;</th>
            <td>
                <?php if(empty($customer_id)){?>
                <button id="saveTemplate" type="button"><?php admin_language_e('email_view_admin_email_form_SaveBtn'); ?></button>
                <?php }?>
                <button id="cancelTemplate" type="button"><?php admin_language_e('email_view_admin_email_form_CancelBtn'); ?></button>
            </td>
        </tr>
    </table>
    <input type="hidden" id="h_action_type" name="h_action_type"  value="<?php echo $action_type ?>" /> <input type="hidden" id="id"  name="id" value="<?php echo $email->id ?>" />
    <input type="hidden" id="content"       name="content" value="" />
</form>

<script type="text/javascript">
    CKEDITOR.replace('content_temp');
    $('button').button();

    /**
     * Submit add template
     */
    $('#saveTemplate').click(function () {
        var submitUrl = $('#addEmailTemplateForm').attr('action');
        var editorText = CKEDITOR.instances.content_temp.getData();
        $('#content').val(editorText);
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'addEmailTemplateForm',
            success: function (data) {

                if (data.status) {
                    $.displayInfor(data.message, null, function () {
                        document.location = '<?php echo base_url() ?>admin/email';
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
    $('#cancelTemplate').click(function () {
        document.location = '<?php echo base_url() ?>admin/email';
    });
</script>
