<?php
$submit_url = base_url() . 'account/users/change_call_setting_phone_users';
?>
<form id="changeCallToSettingForm" method="post" class="dialog-form"
      action="<?php echo $submit_url ?>">
    <table>
        <tr>
            <th>First Action</th>
            <th>Second Action</th>
        </tr>
        <tr>
            <td>
                <?php
                    $first_action = new stdClass();
                    $first_action->action = '';
                    $first_action->to = '';
                    $first_action->show = '';
                    $first_action->id = '';
                    if (!empty($sonetel_user)) {
                        $first_action = $sonetel_user->call->incoming->first_action; 
                    }
                ?>
                <table>
                    <tr>
                        <th>Action</th>
                        <td>
                            <select name="first_action" id="changeCallToSettingForm_first_action" class="input-width" style="width: 200px;">
                                <option value=""></option>
                                <option value="ring" <?php if (isset($first_action->action) && $first_action->action == 'ring') {?> selected="selected" <?php } ?> >Ring</option>
                                <option value="forward" <?php if (isset($first_action->action) && $first_action->action == 'forward') {?> selected="selected" <?php } ?> >Forward</option>
                                <option value="disconnect" <?php if (isset($first_action->action) && $first_action->action == 'disconnect') {?> selected="selected" <?php } ?>>Disconnect the call</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>To</th>
                        <td>
                            <select name="first_to" id="changeCallToSettingForm_first_to" class="input-width" style="width: 200px;">
                                
                            </select>
                            <input type="hidden" id="changeCallToSettingForm_first_to_hidden" value="<?php if (isset($first_action->to)) {echo $first_action->to;}?>" />
                            <input type="hidden" id="changeCallToSettingForm_first_id_hidden" value="<?php if (isset($first_action->id)) {echo $first_action->id;}?>" />
                        </td>
                    </tr>
                    <tr id="tr_changeCallToSettingForm_first_id">
                        <th>Id</th>
                        <td>
                            <?php 
                                $to_list_style = 'display: none;';
                                $text_list_style = 'display: none;';
                                if (($first_action->action == 'forward' && $first_action->to == 'sip') 
                                   || ($first_action->action == 'forward' && $first_action->to == 'voicemail')){
                                    $text_list_style = '';
                                }
                                if (($first_action->action == 'ring' && $first_action->to == 'phone')
                                    || ($first_action->action == 'forward' && $first_action->to == 'user')
                                    || ($first_action->action == 'forward' && $first_action->to == 'voiceapp')) {
                                    $to_list_style = '';
                                }
                            ?>
                            <input type="text" id="changeCallToSettingForm_first_id_other" name="first_id_other" 
                                   value="<?php if (isset($first_action->id)) { echo $first_action->id; } ?>" class="input-width" maxlength=250
                                   style="width: 190px; <?php echo $text_list_style;?> "/>
                            <select name="first_id_list" id="changeCallToSettingForm_first_id_list" class="input-width"
                                   style="width: 200px; <?php echo $to_list_style;?> ">
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Show</th>
                        <td>
                            <select name="first_show" id="changeCallToSettingForm_first_show" class="input-width" style="width: 200px;">
                                <option value=""></option>
                                <option value="caller" <?php if (isset($first_action->show) && $first_action->show == 'caller') {?> selected="selected" <?php } ?> >Show the phone number of the caller </option>
                                <option value="none" <?php if (isset($first_action->show) && $first_action->show == 'none') {?> selected="selected" <?php } ?> > No caller-Id is shown </option>
                                <option value="inum" <?php if (isset($first_action->show) && $first_action->show == 'inum') {?> selected="selected" <?php } ?>> User’s iNUM. </option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Ring-time</th>
                        <td>
                            <input type="text" style="width: 190px;" id="changeCallToSettingForm_first_ringtime" name="ringtime" value="<?php if (isset($first_action->ring_time)) { echo $first_action->ring_time;} ?>" class="input-width" maxlength=50 />
                        </td>
                    </tr>
                </table>
            </td>
            <td>
                <?php
                    $second_action = new stdClass();
                    $second_action->action = '';
                    $second_action->to = '';
                    $second_action->show = '';
                    $second_action->id = '';
                    if (!empty($sonetel_user)) {
                        $second_action = $sonetel_user->call->incoming->second_action; 
                    }
                ?>
                <table>
                    <tr>
                        <th>Action</th>
                        <td>
                            <select name="second_action" id="changeCallToSettingForm_second_action" class="input-width" style="width: 200px;">
                                <option value=""></option>
                                <option value="ring" <?php if ($second_action->action == 'ring') {?> selected="selected" <?php } ?> >Ring</option>
                                <option value="forward" <?php if ($second_action->action == 'forward') {?> selected="selected" <?php } ?> >Forward</option>
                                <option value="disconnect" <?php if ($second_action->action == 'disconnect') {?> selected="selected" <?php } ?>>Disconnect the call</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>To</th>
                        <td>
                            <select name="second_to" id="changeCallToSettingForm_second_to" class="input-width" style="width: 200px;">
                                
                            </select>
                            <input type="hidden" id="changeCallToSettingForm_second_to_hidden" value="<?php if (!empty($second_action->to)) {echo $second_action->to;}?>" />
                            <input type="hidden" id="changeCallToSettingForm_second_id_hidden" value="<?php if (!empty($second_action->id)) {echo $second_action->id;}?>" />
                        </td>
                    </tr>
                    <tr id="tr_changeCallToSettingForm_second_id">
                        <th>Id</th>
                        <td>
                            <?php 
                                $to_list_style = 'display: none;';
                                $text_list_style = 'display: none;';
                                if (($second_action->action == 'forward' && $second_action->to == 'sip')
                                    || ($second_action->action == 'forward' && $second_action->to == 'voicemail')) {
                                    $text_list_style = '';
                                }
                                if (($second_action->action == 'ring' && $second_action->to == 'phone')
                                    || ($second_action->action == 'forward' && $second_action->to == 'user')
                                    || ($second_action->action == 'forward' && $second_action->to == 'voiceapp')) {
                                    $to_list_style = '';
                                }
                            ?>
                            <input type="text" id="changeCallToSettingForm_second_id_other" name="first_id_other" 
                                   value="<?php if (isset($second_action->id)) {echo $second_action->id;} ?>" class="input-width" maxlength=250
                                   style="width: 190px; <?php echo $text_list_style;?> "/>
                            <select name="second_id_list" id="changeCallToSettingForm_second_id_list" class="input-width"
                                   style="width: 200px; <?php echo $to_list_style;?> ">
                            </select>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>    
    </table>
    <input type="hidden" id="changeCallToSettingForm_id" name="customer_id"
           value="<?php echo $customer_id; ?>" />
    <input type="hidden" id="changeCallToSettingForm_phone_user_id" name="phone_user_id"
           value="<?php echo $phone_user_id; ?>" />
</form>
<script>
jQuery(document).ready(function () {
    $('#changeCallToSettingForm_first_action').die('change');
    $('#changeCallToSettingForm_second_action').die('change');
    $('#changeCallToSettingForm_first_to').die('change');
    $('#changeCallToSettingForm_second_to').die('change');
    loadFirstCallSettingTo();
    loadSecondCallSettingTo();
    $('#changeCallToSettingForm_first_action').live('change', function() {
        loadFirstCallSettingTo();
    });
    $('#changeCallToSettingForm_second_action').live('change', function() {
       loadSecondCallSettingTo();
    });
    
    $('#changeCallToSettingForm_first_to').live('change', function() {
       loadFirstCallSettingId();
    });
    
    $('#changeCallToSettingForm_second_to').live('change', function() {
       loadSecondCallSettingId();
    });
    
    function loadFirstCallSettingTo() {
        var url = '<?php echo base_url() . "account/users/load_callsetting_to_target"?>';
        var action = $('#changeCallToSettingForm_first_action').val();
        $.bindSelect(url, 'action=' + action, 'changeCallToSettingForm_first_to', '', '', function() {
            $('#changeCallToSettingForm_first_to').val($('#changeCallToSettingForm_first_to_hidden').val());
            loadFirstCallSettingId();
        });
        
    }
    function loadSecondCallSettingTo() {
        var url = '<?php echo base_url() . "account/users/load_callsetting_to_target"?>';
        var action = $('#changeCallToSettingForm_second_action').val();
        $.bindSelect(url, 'action=' + action, 'changeCallToSettingForm_second_to', '', '', function() {
            $('#changeCallToSettingForm_second_to').val($('#changeCallToSettingForm_second_to_hidden').val());
            loadSecondCallSettingId();
        });
    }
    
    function loadFirstCallSettingId() {
        var url = '<?php echo base_url() . "account/users/load_callsetting_id_target"?>';
        var action = $('#changeCallToSettingForm_first_action').val();
        var to = $('#changeCallToSettingForm_first_to').val();
        var customer_id = $('#changeCallToSettingForm_id').val();
        if ((action == 'ring' && to == 'phone')
           || (action == 'forward' && to == 'user')
           || (action == 'forward' && to == 'voiceapp')) {
            $.bindSelect(url, 'action=' + action + '&to=' + to + '&customer_id=' + customer_id, 'changeCallToSettingForm_first_id_list', '', '', function() {
                $('#changeCallToSettingForm_first_id_list').val($('#changeCallToSettingForm_first_id_hidden').val());
            });
            
            $('#tr_changeCallToSettingForm_first_id').show();
            $('#changeCallToSettingForm_first_id_list').show();
            $('#changeCallToSettingForm_first_id_other').hide();
            $('#changeCallToSettingForm_first_id_other').val('');
        } else if ((action == 'forward' && to == 'sip')) {
            $('#tr_changeCallToSettingForm_first_id').show();
            $('#changeCallToSettingForm_first_id_list').hide();
            $('#changeCallToSettingForm_first_id_list').val('');
            $('#changeCallToSettingForm_first_id_other').show();
        } else {
            $('#tr_changeCallToSettingForm_first_id').hide();
            $('#changeCallToSettingForm_first_id_list').hide();
            $('#changeCallToSettingForm_first_id_other').hide();
            $('#changeCallToSettingForm_first_id_list').val('');
            $('#changeCallToSettingForm_first_id_other').val('');
        }
    }
    function loadSecondCallSettingId() {
        var url = '<?php echo base_url() . "account/users/load_callsetting_id_target"?>';
        var action = $('#changeCallToSettingForm_second_action').val();
        var to = $('#changeCallToSettingForm_second_to').val();
        var customer_id = $('#changeCallToSettingForm_id').val();
        if ((action == 'ring' && to == 'phone')
           || (action == 'forward' && to == 'user')
           || (action == 'forward' && to == 'voiceapp')) {
            $.bindSelect(url, 'action=' + action + '&to=' + to + '&customer_id=' + customer_id, 'changeCallToSettingForm_second_id_list', '', '', function() {
                $('#changeCallToSettingForm_second_id_list').val($('#changeCallToSettingForm_second_id_hidden').val());
            });
            
            $('#tr_changeCallToSettingForm_second_id').show();
            $('#changeCallToSettingForm_second_id_list').show();
            $('#changeCallToSettingForm_second_id_other').hide();
            $('changeCallToSettingForm_second_id_other').val('');
        } else if ((action == 'forward' && to == 'sip')
                || (action == 'forward' && to == 'phnum')) {
            $('#tr_changeCallToSettingForm_second_id').show();
            $('#changeCallToSettingForm_second_id_list').hide();
            $('#changeCallToSettingForm_second_id_list').val('');
            $('#changeCallToSettingForm_second_id_other').show();
        } else {
            $('#tr_changeCallToSettingForm_second_id').hide();
            $('#changeCallToSettingForm_second_id_list').hide();
            $('#changeCallToSettingForm_second_id_other').hide();
            $('#changeCallToSettingForm_second_id_list').val('');
            $('#changeCallToSettingForm_second_id_other').val('');
        }
    }
});
</script>