<?php
    $submit_url = base_url() . 'account/target/add';
?>
<form id="addEditTargetForm" method="post" class="dialog-form" action="<?php echo $submit_url ?>">
    <table>
        <tr>
            <th>Target name <span class="required">*</span></th>
            <td><input class="input-width" type="text" name="target_name" id="target_name" value="" maxlength="250" /></td>
        </tr>
        <tr>
            <th>Target Type <span class="required">*</span></th>
            <td>
                <select id="target_type" name="target_type" class="input-width" style="width: 262px;">
                    <option value=""></option>
                    <option value="voicemail" <?php if ($target->target_type == 'voicemail') {?> selected="selected" <?php } ?>>Voice Mail</option>
                    <!--
                    <option value="user" <?php if ($target->target_type == 'user') {?> selected="selected" <?php } ?>>Other user</option>
                    -->
                    <option value="phone_number" <?php if ($target->target_type == 'phone_number') {?> selected="selected" <?php } ?>>Phone number</option>
                    <option value="sip_phone" <?php if ($target->target_type == 'sip_phone') {?> selected="selected" <?php } ?>>SIP Phone</option>
                    <!--
                    <option value="multi_sip_phone" <?php if ($target->target_type == 'multi_sip_phone') {?> selected="selected" <?php } ?>>Multi SIP Phone</option>
                    -->
                </select>
            </td>
        </tr>
        
        <tr id="tr_target_id">
            <th><span id="target_id_label">Target id</span> <span class="required">*</span></th>
            <td>
                <input type="text" id="target_id" name="target_id" value="" class="input-width" />
                <br/>
                <div style="margin-left: 10px;" class="target_id_guide hide" id="target_id_guide_phone_number">(Example format: +14243332434)</div>
                <div style="margin-left: 10px;" class="target_id_guide hide" id="target_id_guide_sip_phone">(Example format: 1.252322324.5932232@ms.sonetel.net)</div>
            </td>
        </tr>
        <tr id="tr_target_id_voicemail">
            <th>Upload new voice file</th>
            <td>
                <input name="upload_file_input" id="upload_file_input" value="" type="file" style="display: none" />
                <button id="uploadNewVoiceFileButton" style="margin-left: 10px;">Upload mp3,wav</button>
                <input type="hidden" id="voicemail_file_path" name="voicemail_file_path" /> 
            </td>
        </tr>
    </table>
    <input type="hidden" id="h_action_type" name="h_action_type" value="" />
    <input type="hidden" id="id" name="id" value="" />
</form>

<script>
$(document).ready(function () {
    $('#uploadNewVoiceFileButton').button().live('click',function(){
        // do upload function
        $("#upload_file_input").click();
        return false;
    });
    changeTargetType();
    
    $("#target_type").change(function(){
        changeTargetType();
    });
    
    /**
     * change phone type
     * @returns {undefined}
     */
    function changeTargetType(){
        var current_type = $('#target_type').val();
        $('.target_id_guide').hide();
        console.log(current_type);
        $('#target_id_guide_' + current_type).show();
        if (current_type == 'phone_number') {
            $('#target_id_label').html('Phone number');
            
            $('#tr_target_id_voicemail').hide();
            $('#tr_target_id').show();
        } else if (current_type == 'sip_phone') {
            $('#target_id_label').html('URL');
            
            $('#tr_target_id_voicemail').hide();
            $('#tr_target_id').show();
        } else if (current_type == 'voicemail') {
            $('#tr_target_id_voicemail').show();
            $('#tr_target_id').hide();
        } else {
            $('#tr_target_id_voicemail').hide();
            $('#tr_target_id').hide();
            $('#target_id_label').html('Target ID');
        }
    }
    
    $('#upload_file_input').change(function(e) {
        e.preventDefault();
        myfile= $('#upload_file_input').val();
        var ext = myfile.split('.').pop();
        if((ext.toUpperCase() != "MP3")
                && (ext.toUpperCase() != "WAV")){
            $.displayError('Please select PMP3, WAV file to upload.');
            return;
        }
        var time = $.now();
        
        // Upload data here
        $.ajaxFileUpload({
            id: 'upload_file_input',
            data: {
                voicemail_file_path: "voicemail_file_path"
            },
            url: '<?php echo base_url()?>account/target/upload_voicemail_file?t='+time,
            resetFileValue:true,
            success: function(response) {
                if(response.status){
                    $("#voicemail_file_path").val(response.message.local_file_path);
                }else{
                    $.displayError(response.message);
                }
            }
        });
        return false;
    });
});
</script>
