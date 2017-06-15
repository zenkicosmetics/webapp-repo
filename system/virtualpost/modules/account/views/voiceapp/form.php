<?php
if ($action_type == 'add') {
    $submit_url = base_url() . 'account/voiceapp/add';
} else {
    $submit_url = base_url() . 'account/voiceapp/edit';
}
?>
<form id="addEditVoiceAppForm" method="post" class="dialog-form" action="<?php echo $submit_url ?>">
    <table>
    	<tr>
            <th>Type <span class="required">*</span></th>
            <td>
                <?php 
                    // #472: added
                    echo code_master_form_dropdown(array(
                            "code" => APConstants::PHONE_APP_TYPE_CODE,
                            "value"=> $voiceapp->app_type,
                            "name" => 'app_type',
                            "id"    => 'addEditVoiceAppForm_app_type',
                            "clazz" => 'input-width',
                            "style" => 'width: 260px',
                            "has_empty" => true
                    ));
                ?>
                
            </td>
            
        </tr>
        <tr>
            <th>Name <span class="required">*</span></th>
            <td><input type="text" id="addEditVoiceAppForm_name" name="name" value="<?php echo $voiceapp->name ?>" class="input-width" maxlength=250 /></td>
        </tr>
        
    </table>

    <input type="hidden" id="h_action_type" name="h_action_type" value="<?php echo $action_type ?>" />
    <input type="hidden" id="id" name="id" value="<?php echo $voiceapp->id?>" />
</form>

<script>
jQuery(document).ready(function () {
    
});
</script>
