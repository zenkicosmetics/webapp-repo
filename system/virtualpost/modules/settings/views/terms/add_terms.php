<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px"><?php admin_language_e('setting_view_term_addterm_SettingsTermsTerm'); ?></h2>
</div>
<?php
    $submit_url = base_url().'settings/terms/add_terms';
    if (!empty($action) && $action == 'edit') {
        $submit_url = base_url().'settings/terms/edit_terms';
    }
?>
<form id="addTermsServiceForm" method="post" class="dialog-form"
    action="<?php echo $submit_url?>">
    <table>
        <tr>
            <th colspan="2">
                <input type="checkbox" id="need_customer_approval_flag" name="need_customer_approval_flag" value="<?php echo isset($terms) && is_object($terms) ? $terms->need_customer_approval_flag : 0 ?>" />
                &nbsp; <?php admin_language_e('setting_view_term_addterm_NeedsCustomerApproval'); ?><p style="line-height: 10px;">&nbsp;</p>   
                <input type="checkbox" id="message_to_customer_flag" name="message_to_customer_flag" value="<?php echo isset($terms) && is_object($terms) ? $terms->message_to_customer_flag : 0 ?>" />
                &nbsp; <?php admin_language_e('setting_view_term_addterm_MessageToCustomer'); ?><p style="line-height: 10px;">&nbsp;</p>
                <textarea id="message_to_customer" name="message_to_customer" disabled="disabled" style="height: 100px;width:100%;resize: none;padding: 5px; border: 1px solid #dadada;line-height: 18px;">
                    <?php echo isset($terms) && is_object($terms) ? $terms->message_to_customer : "" ?></textarea>
                <p style="line-height: 10px;">&nbsp;</p>
                <?php admin_language_e('setting_view_term_addterm_EffectiveDate'); ?><input style="width: 120px;" type="text" id="effective_date" name="effective_date" 
                                       value="<?php echo isset($terms) && is_object($terms) && (!empty($terms->effective_date)) ? date("m/d/Y", $terms->effective_date) : "" ?>" class="input-txt" />
            </th>
        </tr>
       
        <tr>
            <th colspan="2"><?php admin_language_e('setting_view_term_addterm_AddEditTerms'); ?></th>
        </tr>
        <tr>
            <th><label for="content"><?php admin_language_e('setting_view_term_addterm_Content'); ?></label></th>
            <td>
                <?php echo form_textarea(array('id' => 'content_temp', 'name' => 'content_temp', 'value' => $content, 'rows' => 10)); ?>
            </td>
	</tr>
        <tr>
            <th>&nbsp;</th>
            <td>
                <?php if ( (!isset($terms)) || ($terms->use_flag == '1' && empty($terms->customer_id)) ) {?>
                <button id="saveTemplate" type="button"><?php admin_language_e('setting_view_term_addterm_SaveBtn'); ?>Save</button>
                <?php }?>
                <button id="cancelButton" type="button"><?php admin_language_e('setting_view_term_addterm_CancelBtn'); ?>Cancel</button>
            </td>
	</tr>
    </table>
    <input type="hidden" id="addTermsServiceForm_id" name="id" value="<?php echo $id?>" />
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
        
        if($("#need_customer_approval_flag").is(':checked')){ 
            
            $.confirm({
                    message: "<?php admin_language_e('setting_view_term_addterm_SaveTermWarningMessage'); ?>", 
                    yes: function() {
                        var submitUrl = $('#addTermsServiceForm').attr('action');
                        var editorText = CKEDITOR.instances.content_temp.getData();
                        $('#content').val(editorText);
                        $.ajaxSubmit({
                            url: submitUrl,
                            formId: 'addTermsServiceForm',
                            success: function(data) {
                                if (data.status) {
                                        $.displayInfor(data.message, null, function() {
                                        document.location = '<?php echo base_url()?>settings/terms/terms_service';
                                    });
                                } else {
                                    $.displayError(data.message);
                                }
                            }
                        });
                        return false;
                    }
            });
            
        } else{
            
            var submitUrl = $('#addTermsServiceForm').attr('action');
            var editorText = CKEDITOR.instances.content_temp.getData();
            $('#content').val(editorText);
            $.ajaxSubmit({
                url: submitUrl,
                formId: 'addTermsServiceForm',
                success: function(data) {
                    if (data.status) {
                            $.displayInfor(data.message, null, function() {
                            document.location = '<?php echo base_url()?>settings/terms/terms_service';
                        });
                    } else {
                        $.displayError(data.message);
                    }
                }
            });
            return false;
        }
    });

    /**
     * Cancel
     */
    $('#cancelButton').click(function(){
        <?php if(empty($terms->customer_id)){ ?>
            document.location = '<?php echo base_url()?>settings/terms/terms_service';
        <?php }else {?>
            document.location = '<?php echo base_url()?>settings/terms/enterprise_tc';
        <?php }?>
    });
    
    $( "#effective_date" ).datepicker({
         minDate: 0 
     });
     $("#message_to_customer_flag").click(function(){
         if($("#message_to_customer_flag").is(':checked')){ $("#message_to_customer").attr("disabled",false);}
         else{ $("#message_to_customer").attr("disabled",true); }
     });
     
     $("#need_customer_approval_flag, #message_to_customer_flag").each(function(){
         if($(this).val() == "1"){ $(this).attr("checked",true);}
         else{ $(this).attr("checked",false); }
     });
     
    if($("#message_to_customer_flag").is(':checked')){ $("#message_to_customer").attr("disabled",false);}
    else{$("#message_to_customer").attr("disabled",true);}
     
     $("#need_customer_approval_flag, #message_to_customer_flag").click(function(){
         if($(this).is(':checked')){  $(this).val(1); }
         else{ $(this).val(0);}
     });
     
});
</script>